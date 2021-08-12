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
use think\Db;

/**
 * 产品参数
 */
class ProductAttribute extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    public function getListByTypeid($typeid = 0)
    {
    	$result = Db::name('product_attribute')->where([
    			'typeid'	=> $typeid,
    			'is_del'	=> 0,
    			'lang'		=> get_admin_lang(),
    		])->count();

    	return $result;
    }
}