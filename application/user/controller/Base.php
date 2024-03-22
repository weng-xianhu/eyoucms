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
namespace app\user\controller;
use think\Controller;
use app\common\controller\Common;
use think\Cookie;
use think\Db;

class Base extends Common {

    public $usersTplVersion = '';
    public $usersTpl2xVersion = '';

    /**
     * 初始化操作
     */
    public function _initialize() {
        parent::_initialize();
        $default_lang = config('ey_config.system_home_default_lang');
        if (!empty($default_lang) && $default_lang != 'cn'){
            $this->error("主体语言不是中文，不支持会员中心");
        }
        $assignName2 = $this->arrJoinStr(['cGhwX3Nlcn','ZpY2VtZWFs']);
        $assignValue2 = tpCache('php.'.$assignName2);
        $this->assign($assignName2, $assignValue2);

        $this->usersTplVersion = getUsersTplVersion();
        $this->assign('usersTplVersion', $this->usersTplVersion);

        $this->usersTpl2xVersion = getUsersTpl2xVersion();
        $this->assign('usersTpl2xVersion', $this->usersTpl2xVersion);

        $users_id = session('users_id');
        if (!empty($users_id)) {
            $users = GetUsersLatestData($users_id);
            if (empty($users)) {
                session('users_id', null);
                session('users', null);
                cookie('users_id', null);
                $this->redirect('user/Users/login');
            }

            $users_lock_model = config('global.users_lock_model');
            $users_lock_model_msg = !empty($users_lock_model[$users['is_lock']]) ? $users_lock_model[$users['is_lock']]['msg'] : '';
            $users_lock_model_name = !empty($users_lock_model[$users['is_lock']]) ? $users_lock_model[$users['is_lock']]['name'] : '正常用户';
            if (0 > $users['is_lock'] && IS_POST) {
                $this->error($users_lock_model_msg);
            }
            $users['lock_model_name'] = $users_lock_model_name;

            $this->users = $users;
            $this->users_id = $users['users_id'];
            $this->assign('users', $users);
            $this->assign('users_id', $this->users_id);
            $this->assign('nickname', !empty($this->users['nickname']) ? $this->users['nickname'] : $this->users['username']);
        } else {
            //过滤不需要登陆的行为
            $ctl_all = CONTROLLER_NAME.'@*';
            $ctl_act = CONTROLLER_NAME.'@'.ACTION_NAME;
            $filter_login_action = config('filter_login_action');
            if ('v3' == $this->usersTplVersion && $this->is_mobile) {
                $filter_login_action[] = 'Users@index';
            }
            $dealerParam = input('param.dealerParam/s', '');
            if (!empty($dealerParam)) {
                cookie('dealerParam', null);
                session('dealerParam', null);
                cookie('dealerParam', $dealerParam);
                session('dealerParam', $dealerParam);
                $filter_login_action[] = 'Users@index';
            }
            if (!in_array($ctl_act, $filter_login_action) && !in_array($ctl_all, $filter_login_action)) {
                $resource = input('param.resource/s');
                if ('Uploadify@*' == $ctl_all) {
                    // 注册时上传图片不验证登录行为
                    $is_uploads = Db::name('users_parameter')->where(['dtype'=>['IN',['file','img','imgs']],'is_hidden'=>0, 'is_reg'=>1])->count();
                    $resource = empty($is_uploads) ? '' : $resource;
                }
                if ('Uploadify@*' == $ctl_all && 'reg' == $resource) {

                } else {
                    if (IS_AJAX) {
                        $this->error('请先登录！', null, ['url'=>url('user/Users/login')]);
                    } else {
                        if (isWeixin()) {
                            //微信端
                            $this->redirect(url('user/Users/users_select_login', ['is_ajax'=>0]));
                        } else {
                            // 其他端
                            $this->redirect('user/Users/login');
                        }
                    }
                }
            }
        }

        // 订单超过 get_shop_order_validity 设定的时间，则修改订单为已取消状态，无需返回数据
        // if ('v3' == $this->usersTplVersion) model('Shop')->UpdateShopOrderData($this->users_id);

        // 会员功能是否开启
        $logut_redirect_url = '';
        $logut_redirect_url_test = "";
        $web_users_switch = tpCache('web.web_users_switch');
        if (empty($web_users_switch) || isset($this->usersConfig['users_open_register']) && $this->usersConfig['users_open_register'] == 1) { 
            // 前台会员中心已关闭
            $logut_redirect_url = ROOT_DIR.'/';
            $logut_redirect_url_test  = "会员中心已关闭";
        } else if (session('?users_id') && empty($this->users)) { 
            // 登录的会员被后台删除，立马退出会员中心
            $logut_redirect_url = url('user/Users/centre');
             $logut_redirect_url_test  = "会员不存在";
        }

        // 是否开启会员注册功能
        if (isset($this->usersConfig['users_open_reg']) && $this->usersConfig['users_open_reg'] == 1 && 'reg' == request()->action()) {
            $logut_redirect_url = ROOT_DIR.'/';
             $logut_redirect_url_test  = "会员注册功能已关闭";
        }

        if (!empty($logut_redirect_url)) {
            // 清理session并回到首页
            session('users_id', null);
            session('users', null);
            cookie('users_id', null);
            $this->error($logut_redirect_url_test, $logut_redirect_url);
            //$this->redirect($logut_redirect_url);
        }
        // --end

        // 是否为手机端，1手机端，2其他端
        $this->assign('is_mobile', isMobile() ? 1 : 2);
        
        // 是否为端微信，1微信端，2其他端
        $this->assign('is_wechat', isWeixin() ? 1 : 2);

        // 是否为微信端小程序，1在微信小程序中，0不在微信小程序中
        $this->assign('is_wechat_applets', isWeixinApplets() ? 1 : 0);

        // 站内消息
        if ($this->usersTplVersion != 'v1' && !empty($this->users_id)) {
            // 从notice表里同步新的数据到notice_read表
            $this->update_notice_data();
            // 查询会员站内通知数据
            $where = [
                'is_del' => 0,
                'lang' => $this->home_lang,
                'users_id' => $this->users_id,
            ];
            $usersNoticeNum = Db::name('users_notice_read')->field('id, is_read')->where($where)->select();
            $result = [
                // 未读消息数
                'unread_num' => 0,
                // 总消息数
                'allNotice_num' => 0,
            ];
            foreach ($usersNoticeNum as $key => $value) {
                $result['allNotice_num']++;
                if (0 === intval($value['is_read'])) $result['unread_num']++;
            }
            $this->assign($result);
        }

        //积分名称
        $score_name = getUsersConfigData('score.score_name');
        $score_name = $score_name?:'积分';
        $this->assign('score_name',$score_name);

        // 子目录
        $this->assign('RootDir', ROOT_DIR); 
        $this->assign('eyou', $this->eyou);

        // 订单预处理 (自动关闭未付款订单  发货后自动确认收货  收货后超过维权时间则关闭维权入口  消费赠送)
        if (!empty($this->users_id)) {
            // 调用传参
            // users_id     会员ID，传入则处理指定会员的订单数据，为空则处理所有会员订单数据
            // usersConfig  配置信息，为空则在后续处理中自动查询
            model('OrderPreHandle')->eyou_shopOrderPreHandle($this->users_id, $this->usersConfig);
        }
    }

    // 从notice表里同步新的数据到notice_read表
    public function update_notice_data()
    {
        //查接收消息通知的会员id
        $users_id = $this->users_id;
        $last_read_notice_id = Db::name('users_notice_read')
            ->where(['users_id'=>$users_id])
            ->max("notice_id");

        $no_add_data = Db::name('users_notice')
            ->field("id as notice_id,lang,add_time,update_time")
            ->where("users_id = '' OR users_id = '{$users_id}' OR FIND_IN_SET('{$users_id}', users_id)")
            ->where(['id'=>['GT',$last_read_notice_id]])
            ->order("id ASC")
            ->select();

        if (!empty($no_add_data)) {
            foreach ($no_add_data as $k=>$v) {
                $no_add_data[$k]['users_id'] = $users_id;
            }
            Db::name('users_notice_read')->insertAll($no_add_data);
        }
    }
}