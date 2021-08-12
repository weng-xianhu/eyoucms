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
        
        $assignName2 = $this->arrJoinStr(['cGhwX3Nlcn','ZpY2VtZWFs']);
        $assignValue2 = tpCache('php.'.$assignName2);
        $this->assign($assignName2, $assignValue2);

        if (session('?users_id')) {
            $users_id = session('users_id');
            $users = GetUsersLatestData($users_id);
            $this->users = $users;
            $this->users_id = $users['users_id'];
            $this->assign('users', $users);
            $this->assign('users_id', $this->users_id);
            $this->assign('nickname', !empty($this->users['nickname']) ? $this->users['nickname'] : $this->users['username']);
        } else {
            //过滤不需要登陆的行为
            $ctl_act = CONTROLLER_NAME.'@'.ACTION_NAME;
            $ctl_all = CONTROLLER_NAME.'@*';
            $filter_login_action = config('filter_login_action');
            if (!in_array($ctl_act, $filter_login_action) && !in_array($ctl_all, $filter_login_action)) {
                $resource = input('param.resource/s');
                if ('Uploadify@*' == $ctl_all && 'reg' == $resource) {
                    // 注册时上传图片不验证登录行为
                } else {
                    if (IS_AJAX) {
                        $this->error('请先登录！', null, ['url'=>url('user/Users/login')]);
                    } else {
                        if (isWeixin()) {
                            //微信端
                            $this->redirect('user/Users/users_select_login');
                        } else {
                            // 其他端
                            $this->redirect('user/Users/login');
                        }
                    }
                }
            }
        }

        // 订单超过 get_shop_order_validity 设定的时间，则修改订单为已取消状态，无需返回数据
        // model('Shop')->UpdateShopOrderData($this->users_id);

        $usersTplVersion = getUsersTplVersion();

        // 会员功能是否开启
        $logut_redirect_url = '';
        $this->usersConfig = getUsersConfigData('all');
        $this->assign('usersConfig', $this->usersConfig);
        $web_users_switch = tpCache('web.web_users_switch');
        if (empty($web_users_switch) || isset($this->usersConfig['users_open_register']) && $this->usersConfig['users_open_register'] == 1) { 
            // 前台会员中心已关闭
            $logut_redirect_url = ROOT_DIR.'/';
        } else if (session('?users_id') && empty($this->users)) { 
            // 登录的会员被后台删除，立马退出会员中心
            $logut_redirect_url = url('user/Users/centre');
        }

        // 是否开启会员注册功能
        if (isset($this->usersConfig['users_open_reg']) && $this->usersConfig['users_open_reg'] == 1 && 'reg' == request()->action()) {
            $logut_redirect_url = ROOT_DIR.'/';
        }
        if (!empty($logut_redirect_url)) {
            // 清理session并回到首页
            session('users_id', null);
            session('users', null);
            $this->redirect($logut_redirect_url);
        }
        // --end
        
        // 默认主题颜色
        if (!empty($this->usersConfig['theme_color'])) {
            $theme_color = $this->usersConfig['theme_color'];
        } else {
            if ($usersTplVersion == 'v1') {
                $theme_color = '#ff6565';
            } else {
                $theme_color = '#fd8a27';
            }
        }
        $this->usersConfig['theme_color'] = $theme_color;
        $this->assign('theme_color', $theme_color);

        // 是否为手机端，1手机端，2其他端
        $this->assign('is_mobile', isMobile() ? 1 : 2);
        
        // 是否为端微信，1微信端，2其他端
        $this->assign('is_wechat', isWeixin() ? 1 : 2);

        // 是否为微信端小程序，1在微信小程序中，0不在微信小程序中
        $this->assign('is_wechat_applets', isWeixinApplets() ? 1 : 0);

        // 站内消息
        if ($usersTplVersion != 'v1') {
            // 未读消息数
            $unread_num = Db::name('users')->where(['users_id' => $this->users_id])->value("unread_notice_num");
            $this->assign('unread_num',$unread_num);
            // 总消息数
            $allNotice_num = Db::name('users_notice')->where([
                    'users_id' => [
                        ['EQ', ''],
                        ['EQ', $this->users_id],
                        'OR'
                    ]
                ])->whereOR("FIND_IN_SET({$this->users_id}, users_id)")->count();
            $this->assign('allNotice_num', $allNotice_num);
        }

        //积分名称
        $score_name = getUsersConfigData('score.score_name');
        $score_name = $score_name?:'积分';
        $this->assign('score_name',$score_name);

        // 子目录
        $this->assign('RootDir', ROOT_DIR); 
    }
}