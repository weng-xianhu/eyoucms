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

namespace app\api\model\v1;

use think\Db;
use think\Cache;

/**
 * 微信小程序个人中心模型 
 */
class UserBase extends Base
{
    public $users_id;
    public $session;

    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();

        $token = input('param.token/s');
        if (!empty($token)) {
            $tokenDecode = mchStrCode($token, 'DECODE', '#!@diyminipro#!$');
            if (preg_match_all('/^([0-9a-zA-Z]{8})eyoucms(\d{1,})eyoucms(.+)eyoucms([a-z]{8})eyoucms(.+)eyoucms_token_salt$/i', $tokenDecode, $matches)) {
                $this->users_id = !empty($matches[2][0]) ? intval($matches[2][0]) : 0;
                $openid = !empty($matches[3][0]) ? $matches[3][0] : '';
                $session_key = !empty($matches[5][0]) ? $matches[5][0] : '';
                // 记录缓存, 7天
                $this->session = [
                    'openid'    => $openid,
                    'session_key'   => $session_key,
                    'users_id'  => $this->users_id,
                ];
                Cache::set($token, $this->session, 86400 * 7);
            }
        }

        if (!empty($this->users_id)) {
            // 订单超过 get_shop_order_validity 设定的时间，则修改订单为已取消状态，无需返回数据
            // $shopModel = new \app\user\model\Shop;
            // $shopModel->UpdateShopOrderData($this->users_id);
            $this->UpdateShopOrderData($this->users_id);
        }
    }

    // 处理购买订单，超过指定时间修改为已订单过期，针对未付款订单
    private function UpdateShopOrderData($users_id){
        $time  = getTime() - config('global.get_shop_order_validity');
        $where = array(
            'users_id'     => $users_id,
            'order_status' => 0,
            'add_time'     => array('<',$time),
        );
        $data = [
            'order_status'    => 4,  // 状态修改为订单过期
            'pay_name'        => '', // 订单过期则清空支付方式标记
            'wechat_pay_type' => '', // 订单过期则清空微信支付类型标记
            'update_time'     => getTime(),
        ];

        // 查询订单id数组用于添加订单操作记录
        $OrderIds = Db::name('shop_order')->field('order_id')->where($where)->select();

        if (!empty($OrderIds)) {
            // 订单过期，更新规格数量
            $productSpecValueModel = new \app\user\model\ProductSpecValue;
            $productSpecValueModel->SaveProducSpecValueStock($OrderIds, $users_id);

            //批量修改订单状态
            Db::name('shop_order')->where($where)->update($data);

            // 添加订单操作记录
            AddOrderAction($OrderIds, $users_id, 0, 4, 0, 0, '订单过期！', '会员未在订单有效期内支付，订单过期！');
        }
    }
}