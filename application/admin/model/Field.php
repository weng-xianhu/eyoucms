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
namespace app\admin\model;

use think\Model;

/**
 * 字段
 */
class Field extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 获取全部字段类型
     * @author 小虎哥 by 2018-7-25
     */
    public function getFieldTypeAll($field = '*', $index_key = '')
    {
        $cacheKey = "admin-Field-getFieldTypeAll-{$field}-{$index_key}";
        $result = cache($cacheKey);
        if (!empty($result)) {
            return $result;
        }

        $result = db('FieldType')->field($field)->order('sort_order asc')->select();

        if (!empty($index_key)) {
            $result = convert_arr_key($result, $index_key);
        }
        cache($cacheKey, $result, null, 'field_type');

        return $result;
    }

    /**
     * 查询解析模型数据用以构造from表单
     * @param intval $channel_id 模型ID
     * @param intval $ifmain 是否主表、附加表
     * @param intval $aid 表主键ID
     * @param array $archivesInfo 主表数据
     * @author 小虎哥 by 2018-7-25
     */
    public function getChannelFieldList($channel_id, $ifmain = false, $aid = '', $archivesInfo = [])
    {
        $hideField = array('id','aid','add_time','update_time'); // 不显示在发布表单的字段
        $channel_id = intval($channel_id);
        $map = array(
            'channel_id'    => array('eq', $channel_id),
            'name'          => array('notin', $hideField),
            'ifmain'        => 0,
        );
        if (false !== $ifmain) {
            $map['ifmain'] = $ifmain;
        }
        $row = model('Channelfield')->getListByWhere($map, '*');

        /*编辑时显示的数据*/
        $addonRow = array();
        if (0 < intval($aid)) {
            if (6 == $channel_id) {
                $aid = M('archives')->where(array('typeid'=>$aid, 'channel'=>$channel_id))->getField('aid');
            }
            $tableExt = M('channeltype')->where('id', $channel_id)->getField('table');
            $tableExt .= '_content';
            $addonRow = M($tableExt)->field('*')->where('aid', $aid)->find();
        }
        /*--end*/

        $list = $this->showViewFormData($row, 'addonFieldExt', $addonRow, $archivesInfo);
        return $list;
    }

    /**
     * 查询解析数据表的数据用以构造from表单
     * @param intval $channel_id 模型ID
     * @param intval $id 表主键ID
     * @author 小虎哥 by 2018-7-25
     */
    public function getTabelFieldList($channel_id, $id = '')
    {
        $hideField = array('id','aid','add_time','update_time'); // 不显示在发布表单的字段
        $channel_id = intval($channel_id);
        $map = array(
            'channel_id'    => array('eq', $channel_id),
            'name'          => array('notin', $hideField),
            'ifsystem'      => 0,
        );
        $row = model('Channelfield')->getListByWhere($map, '*');

        /*编辑时显示的数据*/
        $addonRow = array();
        if (0 < intval($id)) {
            if (config('global.arctype_channel_id') == $channel_id) {
                $addonRow = M('arctype')->field('*')->where('id', $id)->find();
            }
        }
        /*--end*/

        $list = $this->showViewFormData($row, 'addonField', $addonRow);
        return $list;
    }

    /**
     * 处理页面显示自定义字段的表单数据
     * @param array $list 自定义字段列表
     * @param array $formFieldStr 表单元素名称的统一数组前缀
     * @param array $addonRow 自定义字段的数据
     * @param array $archivesInfo 主表数据
     * @author 小虎哥 by 2018-7-25
     */
    public function showViewFormData($list, $formFieldStr, $addonRow = array(), $archivesInfo = [])
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
                        $val[$val['name'].'_eyou_is_remote'] = 0;
                        $val[$val['name'].'_eyou_remote'] = '';
                        $val[$val['name'].'_eyou_local'] = '';
                        if (isset($addonRow[$val['name']])) {
                            if (is_http_url($addonRow[$val['name']])) {
                                $val[$val['name'].'_eyou_is_remote'] = 1;
                                $val[$val['name'].'_eyou_remote'] = handle_subdir_pic($addonRow[$val['name']]);
                            } else {
                                $val[$val['name'].'_eyou_is_remote'] = 0;
                                $val[$val['name'].'_eyou_local'] = handle_subdir_pic($addonRow[$val['name']]);
                            }
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

                    case 'datetime':
                    {
                        $val['dfvalue'] = !empty($addonRow[$val['name']]) ? date('Y-m-d H:i:s', $addonRow[$val['name']]) : date('Y-m-d H:i:s');
                        break;
                    }

                    case 'htmltext':
                    {
                        $val['dfvalue'] = isset($addonRow[$val['name']]) ? $addonRow[$val['name']] : $val['dfvalue'];

                        /*追加指定内嵌样式到编辑器内容的img标签，兼容图片自动适应页面*/
                        $title = '';
                        if (!empty($archivesInfo['title'])) {
                            $title = $archivesInfo['title'];
                        } else {
                            $title = !empty($archivesInfo['typename']) ? $archivesInfo['typename'] : '';
                        }
                        $content = htmlspecialchars_decode($val['dfvalue']);
                        $val['dfvalue'] = htmlspecialchars(img_style_wh($content, $title));
                        /*--end*/

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

    /**
     * 查询解析模型数据用以构造from表单
     * @author 小虎哥 by 2018-7-25
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
                        $is_remote = !empty($dataExt[$key.'_eyou_is_remote']) ? $dataExt[$key.'_eyou_is_remote'] : 0;
                        if (1 == $is_remote) {
                            $val = $dataExt[$key.'_eyou_remote'];
                        } else {
                            $val = $dataExt[$key.'_eyou_local'];
                        }
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

                    // case 'htmltext':
                    // {
                    //     /*追加指定内嵌样式到编辑器内容的img标签，兼容图片自动适应页面*/
                    //     $title = '';
                    //     if (!empty($data['title'])) {
                    //         $title = $data['title'];
                    //     } else {
                    //         $title = !empty($data['typename']) ? $data['typename'] : '';
                    //     }
                    //     $content = htmlspecialchars_decode($val);
                    //     $val = htmlspecialchars(img_style_wh($content, $title));
                    //     /*--end*/
                    //     break;
                    // }
                    
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
}