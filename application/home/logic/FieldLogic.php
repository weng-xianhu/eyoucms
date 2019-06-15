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

namespace app\home\logic;

use think\Model;
use think\Db;
/**
 * 字段逻辑定义
 * Class CatsLogic
 * @package home\Logic
 */
class FieldLogic extends Model
{
    /**
     * 查询解析模型数据用以页面展示
     * @param array $data 表数据
     * @param intval $channel_id 模型ID
     * @param array $batch 是否批量列表
     * @author 小虎哥 by 2018-7-25
     */
    public function getChannelFieldList($data, $channel_id = '', $batch = false)
    {
        if (!empty($data) && !empty($channel_id)) {
            /*获取模型对应的附加表字段信息*/
            $map = array(
                'channel_id'    => $channel_id,
            );
            $fieldInfo = model('Channelfield')->getListByWhere($map, '*', 'name');
            /*--end*/
            $data = $this->handleAddonFieldList($data, $fieldInfo, $batch);
        } else {
            $data = array();
        }
        
        return $data;
    }

    /**
     * 查询解析单个数据表的数据用以页面展示
     * @param array $data 表数据
     * @param intval $channel_id 模型ID
     * @param array $batch 是否批量列表
     * @author 小虎哥 by 2018-7-25
     */
    public function getTableFieldList($data, $channel_id = '', $batch = false)
    {
        if (!empty($data) && !empty($channel_id)) {
            /*获取自定义表字段信息*/
            $map = array(
                'channel_id'    => $channel_id,
            );
            $fieldInfo = model('Channelfield')->getListByWhere($map, '*', 'name');
            /*--end*/
            $data = $this->handleAddonFieldList($data, $fieldInfo, $batch);
        } else {
            $data = array();
        }

        return $data;
    }

    /**
     * 处理自定义字段的值
     * @param array $data 表数据
     * @param array $fieldInfo 自定义字段集合
     * @param array $batch 是否批量列表
     * @author 小虎哥 by 2018-7-25
     */
    public function handleAddonFieldList($data, $fieldInfo, $batch = false)
    {
        if (false !== $batch) {
            return $this->handleBatchAddonFieldList($data, $fieldInfo);
        }

        if (!empty($data) && !empty($fieldInfo)) {
            foreach ($data as $key => $val) {
                $dtype = !empty($fieldInfo[$key]) ? $fieldInfo[$key]['dtype'] : '';
                $dfvalue_unit = !empty($fieldInfo[$key]) ? $fieldInfo[$key]['dfvalue_unit'] : '';
                switch ($dtype) {
                    case 'int':
                    case 'float':
                    case 'decimal':
                    case 'text':
                    {
                        $data[$key.'_unit'] = $dfvalue_unit;
                        break;
                    }

                    case 'checkbox':
                    case 'imgs':
                    case 'files':
                    {
                        if (!is_array($val)) {
                            $val = !empty($val) ? explode(',', $val) : array();
                        }
                        /*支持子目录*/
                        foreach ($val as $k1 => $v1) {
                            $val[$k1] = handle_subdir_pic($v1);
                        }
                        /*--end*/
                        break;
                    }

                    case 'htmltext':
                    {
                        $val = htmlspecialchars_decode($val);

                        /*追加指定内嵌样式到编辑器内容的img标签，兼容图片自动适应页面*/
                        $titleNew = !empty($data['title']) ? $data['title'] : '';
                        $val = img_style_wh($val, $titleNew);
                        /*--end*/

                        /*支持子目录*/
                        $val = handle_subdir_pic($val, 'html');
                        /*--end*/
                        break;
                    }

                    case 'decimal':
                    {
                        $val = number_format($val,'2','.',',');
                        break;
                    }
                    
                    default:
                    {
                        /*支持子目录*/
                        if (is_string($val)) {
                            $val = handle_subdir_pic($val, 'html');
                            $val = handle_subdir_pic($val);
                        }
                        /*--end*/
                        break;
                    }
                }
                $data[$key] = $val;
            }
        }
        return $data;
    }

    /**
     * 列表批量处理自定义字段的值
     * @param array $data 表数据
     * @param array $fieldInfo 自定义字段集合
     * @author 小虎哥 by 2018-7-25
     */
    public function handleBatchAddonFieldList($data, $fieldInfo)
    {
        if (!empty($data) && !empty($fieldInfo)) {
            foreach ($data as $key => $subdata) {
                $data[$key] = $this->handleAddonFieldList($subdata, $fieldInfo);
            }
        }
        return $data;
    }
}
