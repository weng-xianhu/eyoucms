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

namespace think\template\taglib\eyou;

use app\home\logic\FieldLogic;
use think\Db;

/**
 * 获取字段值
 */
class TagField extends Base
{
    public $aid = '';
    // public $fieldLogic;
    
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        // $this->fieldLogic = new FieldLogic();
        /*应用于文档列表*/
        $this->aid = input('param.aid/d', 0);
        /*--end*/
    }

    /**
     * 获取字段值
     * @author wengxianhu by 2018-4-20
     */
    public function getField($addfields = '', $aid = '')
    {
        $aid = !empty($aid) ? $aid : $this->aid;

        if (empty($aid)) {
            echo '标签field报错：缺少属性 aid 值，或文档ID不存在。';
            return false;
        }
        if (empty($addfields)) {
            echo '标签field报错：缺少属性 addfields 值。';
            return false;
        }
        $addfields = str_replace('，', ',', $addfields);
        $addfields = trim($addfields, ',');
        $addfields = explode(',', $addfields);

        $parseStr = '';

        $archivesRow = Db::name('archives')->field('typeid,channel')->where(['aid'=>$aid])->find();
        if (empty($archivesRow)) {
            return $parseStr;
        }
        $channel = $archivesRow['channel'];

        // 获取栏目绑定的自定义字段ID列表
        $field_ids = Db::name('channelfield_bind')->where([
                'typeid'    => ['IN', [0, $archivesRow['typeid']]],
            ])->column('field_id');
        if (empty($field_ids)) {
            $fieldname = current($addfields);
        } else {
            // 获取栏目对应的频道下指定的自定义字段
            $row = Db::name('channelfield')->where([
                    'id'            => ['IN', $field_ids],
                    'name'          => ['IN', $addfields],
                    'channel_id'    => $channel,
                ])->field('name')->getAllWithIndex('name');
            foreach ($addfields as $key => $val) {
                if (!empty($row[$val])) {
                    $fieldname = $val;
                    break;
                }
            }
        }

        /*附加表*/
        if (!empty($fieldname)) {
            // 自定义字段的类型
            $dtype = Db::name('channelfield')->where([
                    'name'          => ['EQ', $fieldname],
                    'channel_id'    => $channel,
                ])->getField('dtype');
            $channelInfo = model('Channeltype')->getInfo($channel);
            $tableContent = $channelInfo['table'].'_content';
            $parseStr = Db::name($tableContent)->where('aid',$aid)->getField($fieldname);
            if ('htmltext' == $dtype) {
                $parseStr = htmlspecialchars_decode($parseStr);
            }
        }
        /*--end*/

        return $parseStr;
    }
}