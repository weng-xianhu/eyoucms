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

namespace app\admin\controller;
use think\Db;
use think\Session;
use think\Config;
use app\admin\logic\AjaxLogic;

/**
 * 所有ajax请求或者不经过权限验证的方法全放在这里
 */
class Ajax extends Base {
    
    private $ajaxLogic;

    public function _initialize() {
        parent::_initialize();
        $this->ajaxLogic = new AjaxLogic;
    }

    /**
     * 进入欢迎页面需要异步处理的业务
     */
    public function welcome_handle()
    {
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        $this->ajaxLogic->welcome_handle();
    }

    /**
     * 隐藏后台欢迎页的系统提示
     */
    public function explanation_welcome()
    {
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        $type = input('param.type/d', 0);
        $tpCacheKey = 'system_explanation_welcome';
        if (1 < $type) {
            $tpCacheKey .= '_'.$type;
        }
        
        /*多语言*/
        if (is_language()) {
            $langRow = \think\Db::name('language')->field('mark')->order('id asc')->select();
            foreach ($langRow as $key => $val) {
                tpCache('system', [$tpCacheKey=>1], $val['mark']);
            }
        } else { // 单语言
            tpCache('system', [$tpCacheKey=>1]);
        }
        /*--end*/
    }

    /**
     * 版本检测更新弹窗
     */
    public function check_upgrade_version()
    {
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        $upgradeLogic = new \app\admin\logic\UpgradeLogic;
        $security_patch = tpSetting('upgrade.upgrade_security_patch');
        if (!empty($security_patch) && 1 == $security_patch) {
            $upgradeMsg = $upgradeLogic->checkSecurityVersion(); // 安全补丁包消息
        } else {
            $upgradeMsg = $upgradeLogic->checkVersion(); // 升级包消息
        }
        $this->success('检测成功', null, $upgradeMsg);  
    }

    /**
     * 更新stiemap.xml地图
     */
    public function update_sitemap($controller, $action)
    {
        if (IS_AJAX_POST) {
            \think\Session::pause(); // 暂停session，防止session阻塞机制
            $channeltype_row = \think\Cache::get("extra_global_channeltype");
            if (empty($channeltype_row)) {
                $ctlArr = \think\Db::name('channeltype')
                    ->where('id','NOTIN', [6,8])
                    ->column('ctl_name');
            } else {
                $ctlArr = array();
                foreach($channeltype_row as $key => $val){
                    if (!in_array($val['id'], [6,8])) {
                        $ctlArr[] = $val['ctl_name'];
                    }
                }
            }

            $systemCtl= ['Arctype'];
            $ctlArr = array_merge($systemCtl, $ctlArr);
            $actArr = ['add','edit'];
            if (in_array($controller, $ctlArr) && in_array($action, $actArr)) {
                Session::pause(); // 暂停session，防止session阻塞机制
                sitemap_auto();
                $this->success('更新sitemap成功！');
            }
        }

        $this->error('更新sitemap失败！');
    }

    // 开启\关闭余额支付
    public function BalancePayOpen()
    {
        if (IS_AJAX_POST) {
            $open_value = input('post.open_value/d');
            getUsersConfigData('pay', ['pay_balance_open' => $open_value]);
            $this->success('操作成功');
        }
    }
}