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

        // 订单预处理 (自动关闭未付款订单  发货后自动确认收货  收货后超过维权时间则关闭维权入口  消费赠送)
        if (!empty($this->users_id)) {
            // 调用传参
            // users_id     会员ID，传入则处理指定会员的订单数据，为空则处理所有会员订单数据
            // usersConfig  配置信息，为空则在后续处理中自动查询
            model('OrderPreHandle')->eyou_shopOrderPreHandle($this->users_id);
        }
    }
}