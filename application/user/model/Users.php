<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */
namespace app\user\model;

use think\Db;
use think\Model;
use think\Config;

/**
 * 会员
 */
class Users extends Model
{
    private $home_lang = 'cn';
    private $appid = '';
    private $mchid = '';
    private $key = '';

    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        $this->home_lang = get_home_lang();
    }

    // 判断会员属性中必填项是否为空
    // 传入参数：
    // $post_users ：会员属性信息数组
    // return error：错误提示
    public function isEmpty($post_users = [], $type = '', $return = 'string')
    {
        $error = '';
        // 会员属性
        $where = array(
            'lang'        => $this->home_lang,
            'is_hidden'   => 0, // 是否隐藏属性，0为否
            'is_required' => 1, // 是否必填属性，1为是
        );
        if ('reg' == $type) {
            $where['is_reg'] = 1; // 是否为注册表单
        }
        $para_data = M('users_parameter')->where($where)->field('title,name')->select();
        // 处理提交的属性中必填项是否为空
        foreach ($para_data as $key => $value) {
            if (isset($post_users[$value['name']])) {
                if (is_array($post_users[$value['name']])) {
                    $post_users[$value['name']] = implode(',', $post_users[$value['name']]);
                }
                $attr_value = trim($post_users[$value['name']]);
                if (empty($attr_value)) {
                    $msg = sprintf(foreign_lang('users2', $this->home_lang), $value['title']);
                    if ('array' === $return) {
                        return [
                            'field' => $value['name'],
                            'msg'   => $msg,
                        ];
                    } else {
                        return $msg;
                    }
                }
            }
        }

        return false;
    }

    // 判断邮箱和手机是否存在，并且判断验证码是否验证通过
    // 传入参数：
    // $post_users:会员属性信息数组
    // $users_id:会员ID，注册时不需要传入，修改时需要传入。
    // return error
    public function isRequired($post_users = [],$users_id='', $type = '', $return = 'string')
    {
        if (empty($post_users)) {
            return false;
        }

        // 处理邮箱和手机是否存在
        $where_1 = [
            'is_system'=> 1,
            'lang'     => $this->home_lang,
        ];
        $where_1[] = Db::raw(" ( name LIKE 'email_%' OR name LIKE 'mobile_%' ) ");
        if ('reg' == $type) {
            $where_1['is_reg'] = 1; // 是否为注册表单
        }
        $users_parameter = M('users_parameter')->where($where_1)->field('para_id,title,name')->getAllWithIndex('name');

        $email = '';
        $email_code = '';
        $mobile = '';
        $mobile_code = '';
        /*获取邮箱和手机号码*/
        foreach ($post_users as $key => $val) {
            if (preg_match('/^email_/i', $key)) {
                if (!preg_match('/(_code|_vertify)$/i', $key)) {
                    $email = $val;
                    if (!empty($val) && !check_email($val)) {
                        $msg = sprintf(foreign_lang('users3', $this->home_lang), $users_parameter[$key]['title']);
                        if ('array' == $return) {
                            return [
                                'code_status'   =>  0,
                                'field' => $key,
                                'msg'   => $msg,
                            ];
                        } else {
                            return $msg;
                        }
                    }
                } else {
                    $email_code = $val;
                }
            } else if (preg_match('/^mobile_/i', $key)) {
                if (!preg_match('/(_code|_vertify)$/i', $key)) {
                    $mobile = $val;
                    if (!empty($val) && !check_mobile($val)) {
                        $msg = sprintf(foreign_lang('users3', $this->home_lang), $users_parameter[$key]['title']);
                        if ('array' == $return) {
                            return [
                                'code_status'   =>  0,
                                'field' => $key,
                                'msg'   => $msg,
                            ];
                        } else {
                            return $msg;
                        }
                    }
                } else {
                    $mobile_code = $val;
                }
            }
        }
        /*--end*/

        $users_verification = getUsersConfigData('users.users_verification');
        if (2 == $users_verification) {
            $time = getTime();
            /*处理邮箱验证码逻辑*/
            if (!empty($email)) {
                $where = [
                    'email' => $email,
                    'code'  => $email_code,
                    'lang'  => $this->home_lang,
                ];
                !empty($users_id) && $where['users_id'] = $users_id;
                $record = M('smtp_record')->where($where)->field('record_id,status,add_time')->find();
                if (!empty($record)) {
                    $record['add_time'] += Config::get('global.email_default_time_out');
                    if (1 == $record['status'] || $record['add_time'] <= $time) {
                        $msg = foreign_lang('users4', $this->home_lang);
                        if ('array' == $return) {
                            return [
                                'code_status'   =>  0,
                                'field' => 'email_1_code',
                                'msg'   => $msg,
                            ];
                        } else {
                            return $msg;
                        }
                    }else{
                        // 返回后处理邮箱验证码失效操作
                        $data = [
                            'code_status' => 1,// 正确
                            'email'       => $email,
                        ];
                        return $data;
                    }
                }else{
                    $msg = foreign_lang('users5', $this->home_lang);
                    if (!empty($users_id)) {
                        // 当会员修改邮箱地址，验证码为空或错误返回
                        $row = $this->getUsersListData('email',$users_id);
                        if ($email != $row['email']) {
                            if ('array' == $return) {
                                return [
                                    'code_status'   =>  0,
                                    'field' => 'email_1_code',
                                    'msg'   => $msg,
                                ];
                            } else {
                                return $msg;
                            }
                        }
                    }else{
                        // 当会员注册时，验证码为空或错误返回
                        if ('array' == $return) {
                            return [
                                'code_status'   =>  0,
                                'field' => 'email_1_code',
                                'msg'   => $msg,
                            ];
                        } else {
                            return $msg;
                        }
                    }
                }
            }
            /*--end*/
        } else if (3 == $users_verification) {
            $time = getTime();
            /*处理短信验证码逻辑*/
            if (!empty($mobile)) {
                $msg = foreign_lang('users6', $this->home_lang);
                $where = [
                    'mobile' => $mobile,
                    'code' => $mobile_code
                ];
                $smslog = Db::name('sms_log')->where($where)->field('is_use, add_time')->order('id desc')->find();
                if (!empty($smslog)) {
                    $smslog['add_time'] += Config::get('global.mobile_default_time_out');
                    if (1 == $smslog['is_use'] || $smslog['add_time'] <= $time) {
                        if ('array' == $return) {
                            $data = [
                                'code_status'   =>  0,
                                'field' => 'mobile_1_code',
                                'msg'   => $msg,
                            ];
                        } else {
                            $data = $msg;
                        }
                    } else {
                        // 返回后处理短信验证码失效操作
                        $data = [
                            'code_status' => 1,// 正确
                            'mobile' => $mobile
                        ];
                    }
                } else {
                    if (!empty($users_id)) {
                        // 当会员修改手机地址，验证码为空或错误返回
                        $row = $this->getUsersListData('mobile', $users_id);
                        if ($mobile != $row['mobile']) {
                            if ('array' == $return) {
                                $data = [
                                    'code_status'   =>  0,
                                    'field' => 'mobile_1_code',
                                    'msg'   => $msg,
                                ];
                            } else {
                                $data = $msg;
                            }
                        }
                    } else {
                        // 当会员注册时，验证码为空或错误返回
                        if ('array' == $return) {
                            $data = [
                                'code_status'   =>  0,
                                'field' => 'mobile_1_code',
                                'msg'   => $msg,
                            ];
                        } else {
                            $data = $msg;
                        }
                    }
                }
                return $data;
            }
            /*--end*/
        }

        foreach ($users_parameter as $key => $value) {
            if (isset($post_users[$value['name']])) {
                $where_2 = [
                    'para_id'  => ['EQ', $value['para_id']],
                    'info'     => trim($post_users[$value['name']]),
                    'users_id' => ['NEQ', $users_id],
                    'lang'     => $this->home_lang,
                ];

                // 若users_id为空，则清除条件中的users_id条件
                if (empty($users_id)) { unset($where_2['users_id']); }

                $users_list = M('users_list')->where($where_2)->field('info')->find();
                if (!empty($users_list['info'])) {
                    $msg = sprintf(foreign_lang('users7', $this->home_lang), $value['title']);
                    if ('array' == $return) {
                        return [
                            'code_status'   =>  0,
                            'field' => $key,
                            'msg'   => $msg,
                        ];
                    } else {
                        return $msg;
                    }
                }
            }
        }

        return false;
    }

    // 查询会员属性信息表的邮箱和手机字段
    // 必须传入参数：
    // users_id 会员ID
    // field    查询字段，email仅邮箱，mobile仅手机号，*为两项都查询。
    // return   Data
    public function getUsersListData($field,$users_id)
    {   
        $Data = array();
        if ('email' == $field || '*' == $field) {
            // 查询邮箱
            $parawhere = [
                'name'      => ['LIKE', "email_%"],
                'is_system' => 1,
                'lang'     => $this->home_lang,
            ];
            $paraData = M('users_parameter')->where($parawhere)->field('para_id')->find();
            $listwhere = [
                'para_id'   => $paraData['para_id'],
                'users_id'  => $users_id,
                'lang'     => $this->home_lang,
            ];
            $listData = M('users_list')->where($listwhere)->field('users_id,info')->find();
            $Data['email'] = !empty($listData['info']) ? $listData['info'] : '';
        }

        if ('mobile' == $field || '*' == $field) {
            // 查询手机号
            $parawhere_1 = [
                'name'      => ['LIKE', "mobile_%"],
                'is_system' => 1,
                'lang'     => $this->home_lang,
            ];
            $paraData_1 = M('users_parameter')->where($parawhere_1)->field('para_id')->find();
            $listwhere_1 = [
                'para_id'   => $paraData_1['para_id'],
                'users_id'  => $users_id,
                'lang'     => $this->home_lang,
            ];
            $listData_1 = M('users_list')->where($listwhere_1)->field('users_id,info')->find();
            $Data['mobile'] = !empty($listData_1['info']) ? $listData_1['info'] : '';
        }

        return $Data;
    }

    /**
     * 查询解析数据表的数据用以构造from表单
     * @param   return $list
     * @param   用于添加，不携带数据
     * @author  陈风任 by 2019-2-20
     */
    public function getDataPara($source = '')
    {
        // 字段及内容数据处理
        $where = array(
            'lang'       => $this->home_lang,
            'is_hidden'  => 0,
        );
        'reg' == $source && $where['is_reg'] = 1;

        $row = M('users_parameter')->field('*')
            ->where($where)
            ->order('sort_order asc,para_id asc')
            ->select();

        // 根据所需数据格式，拆分成一维数组
        $addonRow = array();

        // 根据不同字段类型封装数据
        $list = $this->showViewFormData($row, 'users_', $addonRow);
        return $list;
    }

    /**
     * 查询解析数据表的数据用以构造from表单
     * @param   return $list
     * @param   用于修改，携带数据
     * @author  陈风任 by 2019-2-20
     */
    public function getDataParaList($users_id = '', $is_system = '')
    {
        // 字段及内容数据处理
        $where = [
            'a.lang'       => $this->home_lang,
            'a.is_hidden'  => 0,

        ];
        if (!empty($is_system)) {
            $where['a.is_system'] = 1;
        }
        //删除多余干扰数据
        $have = Db::name("users_list")->field("max(list_id) as list_id")->where(['lang'=>$this->home_lang,"users_id"=>$users_id])->group("para_id")->getField("list_id",true);
        if ($have){
            Db::name("users_list")->where(['lang'=>$this->home_lang,"users_id"=>$users_id,"list_id"=>['not in',$have]])->delete();
        }
        $row = Db::name('users_parameter')->alias('a')->where($where)->order('a.sort_order asc,a.para_id asc')->cache(true, EYOUCMS_CACHE_TIME, 'users_parameter')->select();
        $listRow = Db::name('users_list')->field('info,para_id,users_id')->where(['users_id'=>$users_id])->getAllWithIndex('para_id');
        foreach ($row as $key => $val) {
            $val['users_id'] = empty($listRow[$val['para_id']]) ? '' : $listRow[$val['para_id']]['users_id'];
            $val['info'] = empty($listRow[$val['para_id']]) ? '' : $listRow[$val['para_id']]['info'];
            $row[$key] = $val;
        }
        // 根据所需数据格式，拆分成一维数组
        $addonRow = [];
        foreach ($row as $key => $value) {
            $addonRow[$value['name']] = $value['info'];
        }
        // 根据不同字段类型封装数据
        $list = $this->showViewFormData($row, 'users_', $addonRow);
        return $list;
    }

    /**
     * 处理页面显示字段的表单数据
     * @param array $list 字段列表
     * @param array $formFieldStr 表单元素名称的统一数组前缀
     * @param array $addonRow 字段的数据
     * @author 陈风任 by 2019-2-20
     */
    public function showViewFormData($list, $formFieldStr, $addonRow = array())
    {
        if (!empty($list)) {
            foreach ($list as $key => $val) {
                $val['fieldArr'] = $formFieldStr;
                switch ($val['dtype']) {
                    case 'int':
                    {
                        if (isset($addonRow[$val['name']])) {
                            $val['dfvalue'] = $addonRow[$val['name']];
                        } else {
                            if(preg_match("#[^0-9]#", $val['dfvalue']))
                            {
                                $val['dfvalue'] = "";
                            }
                        }
                        break;
                    }

                    case 'float':
                    case 'decimal':
                    {
                        if (isset($addonRow[$val['name']])) {
                            $val['dfvalue'] = $addonRow[$val['name']];
                        } else {
                            if(preg_match("#[^0-9\.]#", $val['dfvalue']))
                            {
                                $val['dfvalue'] = "";
                            }
                        }
                        break;
                    }

                    case 'select':
                    {
                        $dfvalue = $val['dfvalue'];
                        $dfvalueArr = explode(',', $dfvalue);
                        $val['dfvalue'] = $dfvalueArr;
                        if (isset($addonRow[$val['name']])) {
                            $val['trueValue'] = explode(',', $addonRow[$val['name']]);
                        } else {
                            $dfTrueValue = !empty($dfvalueArr[0]) ? $dfvalueArr[0] : '';
                            $val['trueValue'] = array();
                        }
                        break;
                    }

                    case 'radio':
                    {
                        $dfvalue = $val['dfvalue'];
                        $dfvalueArr = explode(',', $dfvalue);
                        $val['dfvalue'] = $dfvalueArr;
                        if (isset($addonRow[$val['name']])) {
                            $val['trueValue'] = explode(',', $addonRow[$val['name']]);
                        } else {
                            $dfTrueValue = !empty($dfvalueArr[0]) ? $dfvalueArr[0] : '';
                            $val['trueValue'] = array($dfTrueValue);
                        }
                        break;
                    }

                    case 'checkbox':
                    {
                        $dfvalue = $val['dfvalue'];
                        $dfvalueArr = explode(',', $dfvalue);
                        $val['dfvalue'] = $dfvalueArr;
                        if (isset($addonRow[$val['name']])) {
                            $val['trueValue'] = explode(',', $addonRow[$val['name']]);
                        } else {
                            $val['trueValue'] = array();
                        }
                        break;
                    }

                    case 'img':
                    {
                        if (isset($addonRow[$val['name']])) {
                            $val[$val['name']] = handle_subdir_pic($addonRow[$val['name']]);
                            $val['info'] = handle_subdir_pic($addonRow[$val['name']]);
                        }
                        break;
                    }

                    case 'imgs':
                    {
                        $val[$val['name'].'_eyou_imgupload_list'] = array();
                        if (isset($addonRow[$val['name']]) && !empty($addonRow[$val['name']])) {
                            $eyou_imgupload_list = explode(',', $addonRow[$val['name']]);
                            /*支持子目录*/
                            foreach ($eyou_imgupload_list as $k1 => $v1) {
                                $eyou_imgupload_list[$k1] = handle_subdir_pic($v1);
                            }
                            /*--end*/
                            $val[$val['name'].'_eyou_imgupload_list'] = $eyou_imgupload_list;
                        }
                        break;
                    }

                    case 'file':
                    {
                        if (isset($addonRow[$val['name']])) {
                            $val[$val['name']] = handle_subdir_pic($addonRow[$val['name']]);
                        }
                        $ext = tpCache('basic.file_type');
                        $val['ext'] = !empty($ext) ? $ext : "zip|gz|rar|iso|doc|xls|ppt|wps";
                        $val['filesize'] = upload_max_filesize();
                        break;
                    }

                    case 'datetime':
                    {
                        if (!empty($addonRow[$val['name']])) {
                            if (is_numeric($addonRow[$val['name']])) {
                                $val['dfvalue'] = date('Y-m-d H:i:s', $addonRow[$val['name']]);
                            } else {
                                $val['dfvalue'] = $addonRow[$val['name']];
                            }
                        } else {
                            $val['dfvalue'] = date('Y-m-d H:i:s');
                        }
                        break;
                    }

                    case 'htmltext':
                    {
                        $val['dfvalue'] = isset($addonRow[$val['name']]) ? $addonRow[$val['name']] : $val['dfvalue'];
                        /*支持子目录*/
                        $val['dfvalue'] = handle_subdir_pic($val['dfvalue'], 'html');
                        /*--end*/
                        break;
                    }
                    
                    default:
                    {
                        $val['dfvalue'] = isset($addonRow[$val['name']]) ? $addonRow[$val['name']] : $val['dfvalue'];
                        /*支持子目录*/
                        if (is_string($val['dfvalue'])) {
                            $val['dfvalue'] = handle_subdir_pic($val['dfvalue'], 'html');
                            $val['dfvalue'] = handle_subdir_pic($val['dfvalue']);
                        }
                        /*--end*/
                        break;
                    }
                }
                $list[$key] = $val;
            }
        }
        return $list;
    }

}