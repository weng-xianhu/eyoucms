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
     * 获取单条产品的所有参数
     * @author 小虎哥 by 2018-4-3
     */
    public function getProAttr($aid, $field = 'b.*, a.*')
    {
        $result = db('ProductAttribute')->field($field)
            ->alias('a')
            ->join('__PRODUCT_ATTR__ b', 'b.attr_id = a.attr_id', 'LEFT')
            ->where([
                'b.aid' => $aid,
                'a.is_del' => 0,
            ])
            ->order('a.sort_order asc, a.attr_id asc')
            ->select();

        return $result;
    }
}