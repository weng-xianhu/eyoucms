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

namespace app\home\model;

use think\Db;
use think\Model;

/**
 * 产品参数
 */
class ProductAttr extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 获取指定产品的所有参数
     * @author 小虎哥 by 2018-4-3
     */
    public function getProAttr($aids = [], $field = 'b.*, a.*')
    {
        $where = [];
        !empty($aids) && $where['b.aid'] = ['IN', $aids];
        $where['a.is_del'] = 0;
        $result = Db::name('ProductAttribute')->field($field)
            ->alias('a')
            ->join('__PRODUCT_ATTR__ b', 'b.attr_id = a.attr_id', 'LEFT')
            ->where($where)
            ->order('a.sort_order asc, a.attr_id asc')
            ->select();
        !empty($result) && $result = group_same_key($result, 'aid');

        return $result;
    }

    /**
     * 获取指定产品的所有新版本参数
     * @author 小虎哥 by 2018-4-3
     */
    public function getProAttrNew($aids = [], $field = 'b.*, a.*')
    {
        $where = [];
        !empty($aids) && $where['b.aid'] = ['IN', $aids];
        $where['a.is_del'] = 0;
        $result = Db::name('ShopProductAttribute')->field($field)
            ->alias('a')
            ->join('__SHOP_PRODUCT_ATTR__ b', 'b.attr_id = a.attr_id', 'LEFT')
            ->where($where)
            ->order('a.sort_order asc, a.attr_id asc')
            ->select();
        !empty($result) && $result = group_same_key($result, 'aid');

        return $result;
    }
}