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

namespace app\common\model;

use think\Db;
use think\Model;

/**
 * 模型自定义字段
 */
class Channelfield extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 获取单条记录
     * @author 小虎哥 by 2018-4-16
     */
    public function getInfo($id, $field = '*')
    {
        $result = Db::name('Channelfield')->field($field)->find($id);

        return $result;
    }

    /**
     * 获取单条记录
     * @author 小虎哥 by 2018-4-16
     */
    public function getInfoByWhere($where, $field = '*')
    {
        $result = Db::name('Channelfield')->field($field)->where($where)->find();

        return $result;
    }

    /**
     * 默认模型字段
     * @author 小虎哥 by 2018-4-16
     */
    public function getListByWhere($map = array(), $field = '*', $index_key = '')
    {
        $result = Db::name('Channelfield')->field($field)
            ->where($map)
            ->order('sort_order asc, channel_id desc, id desc')
            ->select();

        if (!empty($index_key)) {
            $result = convert_arr_key($result, $index_key);
        }
        
        return $result;
    }
}