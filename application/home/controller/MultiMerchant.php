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
 * Date: 2022-03-23
 */

namespace app\home\controller;

use think\Db;

class MultiMerchant extends Base
{
    public function _initialize() {
        parent::_initialize();

        $this->weapp_multi_merchant_db = Db::name('weapp_multi_merchant');
    }

    // 商家列表
    public function merchant_lists()
    {
        $eyou = [];
        $this->eyou = array_merge($this->eyou, $eyou);
        $this->assign('eyou', $this->eyou);
        return $this->fetch('merchant/merchant_lists');
    }

    // 店铺首页
    public function merchant_index()
    {
        $merchant_id = input('merchant_id/d', 0);
        empty($merchant_id) && $this->error('请选择商家访问..');
        $this->assign('merchant_id', $merchant_id);

        $eyou = [];
        $this->eyou = array_merge($this->eyou, $eyou);
        $this->assign('eyou', $this->eyou);
        return $this->fetch('merchant/merchant_index');
    }
}