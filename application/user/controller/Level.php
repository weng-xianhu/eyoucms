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
 * Date: 2019-6-21
 */
 
namespace app\user\controller;

use think\Db;
use think\Config;
use think\Page;

class Level extends Base
{
    // 初始化
    public function _initialize() {
        parent::_initialize();
        // 会员金额明细表
        $this->users_money_db = Db::name('users_money');
        // 会员等级管理表
        $this->users_type_manage_db = Db::name('users_type_manage');

        // 商城微信配置信息
        $this->pay_wechat_config = '';
        $where = [
            'pay_id' => 1,
            'pay_mark' => 'wechat'
        ];
        $PayInfo = Db::name('pay_api_config')->where($where)->getField('pay_info');
        if (!empty($PayInfo)) $this->pay_wechat_config = unserialize($PayInfo);

        // 商城支付宝配置信息
        $this->pay_alipay_config = '';
        $where = [
            'pay_id' => 2,
            'pay_mark' => 'alipay'
        ];
        $PayInfo = Db::name('pay_api_config')->where($where)->getField('pay_info');
        if (!empty($PayInfo)) $this->pay_alipay_config = unserialize($PayInfo);

        // 判断PHP版本信息
        if (version_compare(PHP_VERSION,'5.5.0','<')) {
            $this->php_version = 1; // PHP5.5.0以下版本，可使用旧版支付方式
        }else{
            $this->php_version = 0; // PHP5.5.0以上版本，可使用新版支付方式，兼容旧版支付方式
        }

        // 支付功能是否开启
        $redirect_url = '';
        $pay_open = getUsersConfigData('pay.pay_open');
        $web_users_switch = tpCache('web.web_users_switch');
        if (empty($pay_open)) { 
            // 支付功能关闭，立马跳到会员中心
            $redirect_url = url('user/Users/index');
            $msg = '支付功能尚未开启！';
        } else if (empty($web_users_switch)) { 
            // 前台会员中心已关闭，跳到首页
            $redirect_url = ROOT_DIR.'/';
            $msg = '会员中心尚未开启！';
        }
        if (!empty($redirect_url)) {
            Db::name('users_menu')->where([
                    'mca'   => 'user/Shop/shop_centre',
                    'lang'  => $this->home_lang,
                ])->update([
                    'status'    => 0,
                    'update_time' => getTime(),
                ]);
            $this->error($msg, $redirect_url);
        }
        // --end
    }

    // 等级管理列表
    public function level_centre()
    {
        // 查询升级产品分类表
        $users_type = $this->users_type_manage_db->order('sort_order asc')->select();
        $this->assign('users_type', $users_type);

        // 会员期限
        $member_limit_arr = Config::get('global.admin_member_limit_arr');
        foreach($member_limit_arr as $key => $value) {
            // 下标从 1 开始，重组数组，$key初始为 1 
            $member_limit_arr[$key] = $value['limit_name'];
        }
        $this->assign('member_limit_arr', $member_limit_arr);

        // 查询订单号
        $where_1 = [
            'users_id'   => $this->users_id,
            'cause_type' => 0, // 消费类型
            'status'     => 1, // 未付款状态
            'lang'       => $this->home_lang,
        ];
        $OrderNumber = $this->users_money_db->where($where_1)->getField('order_number');
        $this->assign('OrderNumber', $OrderNumber);

        // 是否开启微信支付方式
        $is_open_wechat = 1;
        if (!empty($this->pay_wechat_config)) {
            $is_open_wechat = !empty($this->pay_wechat_config['is_open_wechat']) ? $this->pay_wechat_config['is_open_wechat'] : 0;
        } else {
            $where = [
                'pay_id' => 1,
                'pay_mark' => 'wechat'
            ];
            $PayInfo = Db::name('pay_api_config')->where($where)->getField('pay_info');
            if (!empty($PayInfo)) {
                $wechat = unserialize($PayInfo);
                $is_open_wechat = !empty($wechat['is_open_wechat']) ? $wechat['is_open_wechat'] : 0;
            }
        }
        $this->assign('is_open_wechat', $is_open_wechat);

        // 是否开启支付宝支付方式
        $is_open_alipay = 1;
        if (!empty($this->pay_alipay_config)) {
            $is_open_alipay = !empty($this->pay_alipay_config['is_open_alipay']) ? $this->pay_alipay_config['is_open_alipay'] : 0;
        } else {
            $where = [
                'pay_id' => 2,
                'pay_mark' => 'alipay'
            ];
            $PayInfo = Db::name('pay_api_config')->where($where)->getField('pay_info');
            if (!empty($PayInfo)) {
                $alipay = unserialize($PayInfo);
                $is_open_alipay = !empty($alipay['is_open_wechat']) ? $alipay['is_open_wechat'] : 0;
            }
        }
        $this->assign('is_open_alipay', $is_open_alipay);

        $result = [];
        // 菜单名称
        $result['title'] = Db::name('users_menu')->where([
                'mca'  => 'user/Level/level_centre',
                'lang' => $this->home_lang,
            ])->getField('title');

        /*余额开关*/
        $pay_balance_open = getUsersConfigData('pay.pay_balance_open');
        if (!is_numeric($pay_balance_open) && empty($pay_balance_open)) {
            $pay_balance_open = 1;
        }
        $result['pay_balance_open'] = $pay_balance_open;
        /*end*/

        $eyou = array(
            'field' => $result,
        );
        $this->assign('eyou', $eyou);

        // 跳转链接
        $referurl = input('param.referurl/s', null, 'htmlspecialchars_decode,urldecode');
        if (empty($referurl)) {
            if (isset($_SERVER['HTTP_REFERER']) && stristr($_SERVER['HTTP_REFERER'], $this->request->host())) {
                $referurl = $_SERVER['HTTP_REFERER'];
            } else {
                $referurl = url("user/Users/centre");
            }
        }
        cookie('referurl', $referurl);
        $this->assign('referurl', $referurl);

        return $this->fetch('users/level_centre');
    }

}