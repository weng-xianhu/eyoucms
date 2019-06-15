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
 * Date: 2019-1-25
 */

namespace app\user\controller;
use think\Controller;
use app\common\controller\Common;
use think\Db;

class Base extends Common {

    public $usersConfig = [];

    /**
     * 初始化操作
     */
    public function _initialize() {
        parent::_initialize();

        if(session('?users_id'))
        {
            $users_id = session('users_id');
            $users = M('users')->field('a.*,b.level_name')
                ->alias('a')
                ->join('__USERS_LEVEL__ b', 'a.level = b.level_id', 'LEFT')
                ->where([
                    'a.users_id'        => $users_id,
                    'a.lang'            => $this->home_lang,
                    'a.is_activation'   => 1,
                ])->find();
            session('users',$users);  //覆盖session 中的 users
            $this->users = $users;
            $this->users_id = $users['users_id'];
            
            $nickname = $this->users['nickname'];
            if (empty($nickname)) {
                $nickname = $this->users['username'];
            }
            $this->assign('nickname',$nickname);
            
            $this->assign('users',$users); //存储用户信息
            $this->assign('users_id',$this->users_id);
        } else {
            //过滤不需要登陆的行为
            $ctl_act = CONTROLLER_NAME.'@'.ACTION_NAME;
            $ctl_all = CONTROLLER_NAME.'@*';
            $filter_login_action = config('filter_login_action');
            if (!in_array($ctl_act, $filter_login_action) && !in_array($ctl_all, $filter_login_action)) {
                if (IS_AJAX) {
                    $this->error('请先登录！');
                } else {
                    if (isWeixin()) {
                        //微信端
                        $this->redirect('user/Users/users_select_login');
                        exit;
                    }else{
                        // 其他端
                        $this->redirect('user/Users/login');
                        exit;
                    }
                }
            }
        }

        // 订单超过 get_shop_order_validity 设定的时间，则修改订单为已取消状态，无需返回数据
        model('Shop')->UpdateShopOrderData($this->users_id);

        // 会员功能是否开启
        $logut_redirect_url = '';
        $this->usersConfig = getUsersConfigData('all');
        $web_users_switch = tpCache('web.web_users_switch');
        if (empty($web_users_switch) || isset($this->usersConfig['users_open_register']) && $this->usersConfig['users_open_register'] == 1) { 
            // 前台会员中心已关闭
            $logut_redirect_url = ROOT_DIR.'/';
        } else if (session('?users_id') && empty($this->users)) { 
            // 登录的会员被后台删除，立马退出会员中心
            $logut_redirect_url = url('user/Users/centre');
        }
        if (!empty($logut_redirect_url)) {
            // 清理session并回到首页
            session('users_id', null);
            session('users', null);
            $this->redirect($logut_redirect_url);
            exit;
        }
        // --end
        
        $this->assign('usersConfig', $this->usersConfig);
        
        $this->usersConfig['theme_color'] = $theme_color = !empty($this->usersConfig['theme_color']) ? $this->usersConfig['theme_color'] : '#ff6565'; // 默认主题颜色
        $this->assign('theme_color', $theme_color);

        // 是否为手机端
        $is_mobile = 2;     // 其他端
        if (isMobile()) {
            $is_mobile = 1; // 手机端
        }
        $this->assign('is_mobile',$is_mobile);
        
        // 是否为端微信
        $is_wechat = 2;     // 其他端
        if (isWeixin()) {
            $is_wechat = 1; // 微信端
        }
        $this->assign('is_wechat',$is_wechat);

        // 是否为微信端小程序
        $is_wechat_applets = 0;     // 不在微信小程序中
        if (isWeixinApplets()) {
            $is_wechat_applets = 1; // 在微信小程序中
        }
        $this->assign('is_wechat_applets',$is_wechat_applets);
    }
}