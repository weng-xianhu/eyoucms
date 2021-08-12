<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 陈风任 <491085389@qq.com>
 * Date: 2019-7-3
 */
namespace app\user\model;

use think\Db;
use think\Model;
use think\Config;

/**
 * 会员投稿
 */
class UsersRelease extends Model
{
    private $home_lang = 'cn';

    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        $this->home_lang = get_home_lang();
    }

     /**
     * 后置操作方法
     * 自定义的一个函数 用于数据保存后做的相应处理操作, 使用时手动调用
     * @param int $aid 产品id
     * @param array $post post数据
     * @param string $opt 操作
     */
    public function afterSave($aid, $post, $opt, $table)
    {
        $post['aid'] = $aid;
        $addonFieldExt = !empty($post['addonFieldExt']) ? $post['addonFieldExt'] : array();
        $this->dealChannelPostData($post['channel'], $post, $addonFieldExt);

        // 图集模型
        if ($post['channel'] == 3) {
            $this->saveimg($aid, $post);
        } else if ($post['channel'] == 4) {
            model('DownloadFile')->savefile($aid, $post);
        }

        // 处理TAG标签
        model('Taglist')->savetags($aid, $post['typeid'], $post['tags'], $post['arcrank'], $opt);

        if (isset($post['arcrank']) && 0 <= $post['arcrank']) {
            // 投稿已审核
            model('SqlCacheTable')->UpdateSqlCacheTable($post, $opt, $table, true);
        } else if (isset($post['arcrank']) && -1 == $post['arcrank']) {
            // 投稿待审核
            model('SqlCacheTable')->UpdateDraftSqlCacheTable($post, $opt);
        }
    }

    /**
     * 删除单条图集的所有图片
     * @author 小虎哥 by 2018-4-3
     */
    public function delImgUpload($aid = array())
    {
        if (!is_array($aid)) {
            $aid = array($aid);
        }
        $result = Db::name('ImagesUpload')->where(array('aid'=>array('IN', $aid)))->delete();

        return $result;
    }

    /**
     * 保存图集图片
     * @author 小虎哥 by 2018-4-3
     */
    public function saveimg($aid, $post = array())
    {
        $imgupload = isset($post['imgupload']) ? $post['imgupload'] : array();
        $imgintro = isset($post['imgintro']) ? $post['imgintro'] : array();
        if (!empty($imgupload)) {
            
            array_pop($imgupload); // 弹出最后一个

            // 删除产品图片
            $this->delImgUpload($aid);

            // 添加图片
            $data = array();
            $sort_order = 0;
            foreach($imgupload as $key => $val)
            {
                if($val == null || empty($val))  continue;

                $filesize = 0;
                $img_info = array();
                if (is_http_url($val)) {
                    $imgurl = handle_subdir_pic($val);
                } else {
                    $imgurl = ROOT_PATH.ltrim($val, '/');
                    $filesize = @filesize('.'.$val);
                }
                $img_info = @getimagesize($imgurl);
                $width = isset($img_info[0]) ? $img_info[0] : 0;
                $height = isset($img_info[1]) ? $img_info[1] : 0;
                $type = isset($img_info[2]) ? $img_info[2] : 0;
                $attr = isset($img_info[3]) ? $img_info[3] : '';
                $mime = isset($img_info['mime']) ? $img_info['mime'] : '';
                $title = !empty($post['title']) ? $post['title'] : '';
                $intro = !empty($imgintro[$key]) ? $imgintro[$key] : '';
                ++$sort_order;
                $data[] = array(
                    'aid' => $aid,
                    'title' => $title,
                    'image_url'   => $val,
                    'intro'   => $intro,
                    'width' => $width,
                    'height' => $height,
                    'filesize'  => $filesize,
                    'mime'  => $mime,
                    'sort_order'    => $sort_order,
                    'add_time' => getTime(),
                );
            }
            if (!empty($data)) {
                Db::name('ImagesUpload')->insertAll($data);

                // 没有封面图时，取第一张图作为封面图
                $litpic = isset($post['litpic']) ? $post['litpic'] : '';
                if (empty($litpic)) {
                    $litpic = $data[0]['image_url'];
                    Db::name('archives')->where(array('aid'=>$aid))->update(array('litpic'=>$litpic, 'update_time'=>getTime()));
                }
            }
            delFile(UPLOAD_PATH."images/thumb/$aid"); // 删除缩略图
        }
    }

    /**
     * 获取单条记录
     */
    public function getInfo($aid, $field = null, $isshowbody = true)
    {
        $result = array();
        $field = !empty($field) ? $field : '*';
        $result = Db::name('archives')->field($field)
            ->where([
                'aid'   => $aid,
                'lang'  => get_home_lang(),
            ])
            ->find();
        if ($isshowbody) {
            $tableName = M('channeltype')->where('id','eq',$result['channel'])->getField('table');
            $result['addonFieldExt'] = Db::name($tableName.'_content')->where('aid',$aid)->find();
        }

        // 文章TAG标签
        if (!empty($result)) {
            $typeid = isset($result['typeid']) ? $result['typeid'] : 0;
            $tags = model('Taglist')->getListByAid($aid, $typeid);
            if (!empty($tags['tag_arr'])){
                $result['tags'] = $tags['tag_arr'];
            }else{
                $result['tags'] = '';
            }

        }

        return $result;
    }

    /**
     * 获取单条图集的所有图片
     * @author 小虎哥 by 2018-4-3
     */
    public function getImgUpload($aid, $field = '*')
    {
        $result = Db::name('ImagesUpload')->field($field)
            ->where('aid', $aid)
            ->order('sort_order asc')
            ->select();

        return $result;
    }

    /**
     * 查询解析模型数据用以构造from表单
     */
    public function dealChannelPostData($channel_id, $data = array(), $dataExt = array())
    {
        if (!empty($dataExt) && !empty($channel_id)) {
            $nowDataExt = array();
            $fieldTypeList = model('Channelfield')->getListByWhere(array('channel_id'=>$channel_id), 'name,dtype', 'name');
            foreach ($dataExt as $key => $val) {
                $key = preg_replace('/^(.*)(_eyou_is_remote|_eyou_remote|_eyou_local)$/', '$1', $key);
                $dtype = !empty($fieldTypeList[$key]) ? $fieldTypeList[$key]['dtype'] : '';
                switch ($dtype) {

                    case 'checkbox':
                    {
                        $val = implode(',', $val);
                        break;
                    }

                    case 'switch':
                    case 'int':
                    {
                        $val = intval($val);
                        break;
                    }

                    case 'img':
                    {
                        $val = $dataExt[$key];
                        break;
                    }

                    case 'imgs':
                    case 'files':
                    {
                        foreach ($val as $k2 => $v2) {
                            if (empty($v2)) {
                                unset($val[$k2]);
                                continue;
                            }
                            $val[$k2] = trim($v2);
                        }
                        $val = implode(',', $val);
                        break;
                    }

                    case 'datetime':
                    {
                        $val = !empty($val) ? strtotime($val) : getTime();
                        break;
                    }

                    case 'decimal':
                    {
                        $moneyArr = explode('.', $val);
                        $money1 = !empty($moneyArr[0]) ? intval($moneyArr[0]) : '0';
                        $money2 = !empty($moneyArr[1]) ? intval(msubstr($moneyArr[1], 0, 2)) : '00';
                        $val = $money1.'.'.$money2;
                        break;
                    }

                    case 'htmltext':
                    {
                        if (!empty($val)) {
                            $val = preg_replace("/^&amp;nbsp;/i", "", $val);
                        }
                        $val = preg_replace("/&lt;script[\s\S]*?script&gt;/i", "", $val);
                        $val = trim($val);
                    }

                    default:
                    {
                        $val = trim($val);
                        break;
                    }
                }
                $nowDataExt[$key] = $val;
            }

            $nowData = array(
                'aid'   => $data['aid'],
                'add_time'   => getTime(),
                'update_time'   => getTime(),
            );
            $nowDataExt = array_merge($nowDataExt, $nowData);
            $tableExt = M('channeltype')->where('id', $channel_id)->getField('table');
            $tableExt .= '_content';
            $count = M($tableExt)->where('aid', $data['aid'])->count();
            if (empty($count)) {
                M($tableExt)->insert($nowDataExt);
            } else {
                M($tableExt)->where('aid', $data['aid'])->save($nowDataExt);
            }
        }
    }

    /**
     * 查询解析数据表的数据用以构造from表单
     * @param   return $list
     * @param   用于添加，不携带数据
     */
    public function GetUsersReleaseData($channel_id = null, $typeid = null, $aid = null, $method = 'add')
    {
//        $typeid = 0;distinct
        // 不显示在发布表单的字段
        $hideField = array('id','aid','add_time','update_time');

        // 查询指定的自定义字段
        $id = Db::name('channelfield_bind')->where('typeid', 'in',[0,$typeid])->column('field_id');

        // 模型ID
        $channel_id = intval($channel_id);
        $map = array(
            'id'            => ['IN', $id],
            'channel_id'    => array('eq', $channel_id),
            'name'          => array('notin', $hideField),
            'ifmain'        => 0,
            'ifeditable'    => 1,
            'is_release'    => 1,
        );
        $row = model('Channelfield')->getListByWhere($map, '*');
//        $map2 = array(
//            'name' => 'content',
//            'channel_id' => $channel_id
//        );
//        $row2 = model('Channelfield')->getListByWhere($map2, '*');
//
//        $row = array_merge($row, $row2);

        /*编辑时显示的数据*/
        $addonRow = array();
        if ('edit' == $method) {
            if (6 == $channel_id) {
                $aid = M('archives')->where(array('typeid'=>$typeid, 'channel'=>$channel_id))->getField('aid');
            }
            $tableExt = M('channeltype')->where('id', $channel_id)->getField('table');
            $tableExt .= '_content';
            $addonRow = M($tableExt)->field('*')->where('aid', $aid)->find();
        }
        /*--end*/

        $list = $this->showViewFormData($row, 'addonFieldExt', $addonRow);
        return $list;
    }

    /**
     * 查询解析数据表的数据用以构造from表单
     * @param   return $list
     * @param   用于修改，携带数据
     * @author  陈风任 by 2019-2-20
     */
    public function getDataParaList($users_id = '')
    {
        // 字段及内容数据处理
        $row = M('users_parameter')->field('a.*,b.info,b.users_id')
            ->alias('a')
            ->join('__USERS_LIST__ b', "a.para_id = b.para_id AND b.users_id = {$users_id}", 'LEFT')
            ->where([
                'a.lang'       => $this->home_lang,
                'a.is_hidden'  => 0,
            ])
            ->order('a.sort_order asc,a.para_id asc')
            ->select();
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
                    case 'region':
                    {
                        $dfvalue    = unserialize($val['dfvalue']);
                        $RegionData = [];
                        $region_ids = explode(',', $dfvalue['region_ids']);
                        foreach ($region_ids as $id_key => $id_value) {
                            $RegionData[$id_key]['id'] = $id_value;
                        }
                        $region_names = explode('，', $dfvalue['region_names']);
                        foreach ($region_names as $name_key => $name_value) {
                            $RegionData[$name_key]['name'] = $name_value;
                        }

                        $val['dfvalue'] = $RegionData;
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
                            $val['dfvalue'] = handle_subdir_pic($addonRow[$val['name']]);
                        }
                        break;
                    }
                    case 'file':
                    {
                        if (isset($addonRow[$val['name']])) {
                            $val['dfvalue'] = handle_subdir_pic($addonRow[$val['name']]);
                        }
                        $ext = tpCache('basic.file_type');
                        $val['ext'] = !empty($ext) ? $ext : "zip|gz|rar|iso|doc|xls|ppt|wps";
                        $val['filesize'] = upload_max_filesize();
                        break;
                    }
                    case 'media':
                    {
                        if (isset($addonRow[$val['name']])) {
                            $val['dfvalue'] = handle_subdir_pic($addonRow[$val['name']],'media');
                        }
                        $val['upload_flag'] = 'local';
                        $WeappConfig = Db::name('weapp')->field('code, status')->where('code', 'IN', ['Qiniuyun', 'AliyunOss', 'Cos'])->where('status',1)->select();
                        foreach ($WeappConfig as $value) {
                            if ('Qiniuyun' == $value['code']) {
                                $val['upload_flag'] = 'qny';
                            } else if ('AliyunOss' == $value['code']) {
                                $val['upload_flag'] = 'oss';
                            } else if ('Cos' == $value['code']) {
                                $val['upload_flag'] = 'cos';
                            }
                        }

                        $ext = tpCache('basic.media_type');
                        $val['ext'] = !empty($ext) ? $ext : "swf|mpg|mp3|rm|rmvb|wmv|wma|wav|mid|mov|mp4";
                        $val['filesize'] = upload_max_filesize();
                        break;
                    }
                    case 'imgs':
                    {
                        $val[$val['name'].'_eyou_imgupload_list'] = array();
                        if (isset($addonRow[$val['name']]) && !empty($addonRow[$val['name']])) {
                            $eyou_imgupload_list = explode(',', $addonRow[$val['name']]);
                            //支持子目录
                            foreach ($eyou_imgupload_list as $k1 => $v1) {
                                $eyou_imgupload_list[$k1] = handle_subdir_pic($v1);
                            }
                            $val[$val['name'].'_eyou_imgupload_list'] = $eyou_imgupload_list;
                        }
                        break;
                    }
                    case 'datetime':
                    {
                        $val['dfvalue'] = !empty($addonRow[$val['name']]) ? date('Y-m-d H:i:s', $addonRow[$val['name']]) : date('Y-m-d H:i:s');
                        break;
                    }
                    case 'htmltext':
                    {
                        $val['dfvalue'] = isset($addonRow[$val['name']]) ? $addonRow[$val['name']] : $val['dfvalue'];
                        $val['dfvalue'] = handle_subdir_pic($val['dfvalue'], 'html');//支持子目录
                        break;
                    }
                    default:
                    {
                        $val['dfvalue'] = isset($addonRow[$val['name']]) ? $addonRow[$val['name']] : $val['dfvalue'];
                        if (is_string($val['dfvalue'])) {
                            //支持子目录
                            $val['dfvalue'] = handle_subdir_pic($val['dfvalue'], 'html');
                            $val['dfvalue'] = handle_subdir_pic($val['dfvalue']);
                        }
                        break;
                    }
                }
                $list[$key] = $val;
            }
        }
        return $list;
    }
}