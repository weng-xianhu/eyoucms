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
            // 安全修复：令牌密钥改为“固定串 + 每站唯一密钥(get_auth_code)”，与生成端保持一致
            $tokenDecode = mchStrCode($token, 'DECODE', '#!@diyminipro#!$' . get_auth_code());
            $matches = [];
            $is_expired = false;
            if (!empty($tokenDecode)) {
                /* 新格式：末尾带10位过期时间，超期不再建立登录态（客户端收到 code=-1 后重新登录） */
                if (preg_match('/^([0-9a-zA-Z]{8})eyoucms(\d{1,})eyoucms(.+)eyoucms([a-z]{8})eyoucms(.+)eyoucms(\d{10})eyoucms_token_salt$/i', $tokenDecode, $matches)) {
                    $is_expired = intval($matches[6]) < getTime();
                } else if (preg_match('/^([0-9a-zA-Z]{8})eyoucms(\d{1,})eyoucms(.+)eyoucms([a-z]{8})eyoucms(.+)eyoucms_token_salt$/i', $tokenDecode, $matches)) {
                    /* 旧格式：无过期时间，继续兼容放行，避免升级后已登录用户被强制下线 */
                    $is_expired = false;
                }
            }
            if (!empty($matches) && !$is_expired) {
                $this->users_id = !empty($matches[2]) ? intval($matches[2]) : 0;
                $openid = !empty($matches[3]) ? $matches[3] : '';
                $session_key = !empty($matches[5]) ? $matches[5] : '';
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