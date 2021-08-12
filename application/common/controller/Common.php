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

namespace app\common\controller;
use think\Controller;
use think\Session;
use think\Db;
class Common extends Controller {

    public $session_id;
    public $theme_style = '';
    public $theme_style_path = '';
    public $view_suffix = 'html';
    public $eyou = array();

    public $users_id = 0;
    public $users = array();

    /**
     * 析构函数
     */
    function __construct() 
    {
        /*是否隐藏或显示应用入口index.php*/
        if (tpCache('seo.seo_inlet') == 0) {
            \think\Url::root('/index.php');
        } else {
            // \think\Url::root('/');
        }
        /*--end*/
        parent::__construct();
    }    
    
    /*
     * 初始化操作
     */
    public function _initialize() 
    {
        session('admin_info'); // 传后台信息到前台，此处可视化用到
        if (!session_id()) {
            Session::start();
        }
        header("Cache-control: private");  // history.back返回后输入框值丢失问题 
        $this->session_id = session_id(); // 当前的 session_id
        !defined('SESSION_ID') && define('SESSION_ID', $this->session_id); //将当前的session_id保存为常量，供其它方法调用
        
        if (!session('?users_id')) {
            session('users_id', null);
            session('users', null);
            cookie('users_id', null);
        }

        $global = tpCache('global'); 

        /*关闭网站*/
        if (in_array(MODULE_NAME, ['home']) && !empty($global['web_status']) && $global['web_status'] == 1) {
            die("<div style='text-align:center; font-size:20px; font-weight:bold; margin:50px 0px;'>网站暂时关闭，维护中……</div>");
        }
        /*--end*/

        /*强制微信模式，仅允许微信端访问*/
        $shop_force_use_wechat = getUsersConfigData('shop.shop_force_use_wechat');
        if (!empty($shop_force_use_wechat) && 1 == $shop_force_use_wechat  && !isWeixin()) {
            $html = "<div style='text-align:center; font-size:20px; font-weight:bold; margin:50px 0px;'>网站仅微信端可访问</div>";
            $WeChatLoginConfig = getUsersConfigData('wechat.wechat_login_config') ? unserialize(getUsersConfigData('wechat.wechat_login_config')) : [];
            if (!empty($WeChatLoginConfig['wechat_name'])) $html .= "<div style='text-align:center; font-size:20px; font-weight:bold; margin:50px 0px;'>关注微信公众号：".$WeChatLoginConfig['wechat_name']."</div>";
            if (!empty($WeChatLoginConfig['wechat_pic'])) $html .= "<div style='text-align:center; font-size:20px; font-weight:bold; margin:50px 0px;'><img style='width: 400px; height: 400px;' src='".$WeChatLoginConfig['wechat_pic']."'></div>";
            die($html);
        }
        /*END*/

        $this->global_assign($global); // 获取网站全局变量值
        $this->view_suffix = config('template.view_suffix'); // 模板后缀htm
        $this->theme_style = THEME_STYLE; // 模板标识
        $this->theme_style_path = THEME_STYLE_PATH; // 模板目录
        //全局变量
        $this->eyou['global'] = $global;
        // 多语言变量
        try {
            $langArr = include_once APP_PATH."lang/{$this->home_lang}.php";
        } catch (\Exception $e) {
            $this->home_lang = $this->main_lang;
            $langCookieVar = \think\Config::get('global.home_lang');
            \think\Cookie::set($langCookieVar, $this->home_lang);
            $langArr = include_once APP_PATH."lang/{$this->home_lang}.php";
        }
        $this->eyou['lang'] = !empty($langArr) ? $langArr : [];
        /*电脑版与手机版的切换*/
        $v = I('param.v/s', 'pc');
        $v = trim($v, '/');
        $this->assign('v', $v);
        /*--end*/

        /*多语言开关限制 - 不开某个语言情况下，报404 */
        if ($this->main_lang != $this->home_lang) {
            if (1 != $global['web_language_switch']) {
                abort(404,'页面不存在');
            } else {
                $langInfo = Db::name('language')->field('id')
                    ->where([
                        'mark'      => $this->home_lang,
                        'status'    => 1,
                    ])->find();
                if (empty($langInfo)) {
                    abort(404,'页面不存在');
                }
            }
        }
        /*--end*/

        // 判断是否开启注册入口
        $users_open_register = getUsersConfigData('users.users_open_register');
        $this->assign('users_open_register', $users_open_register);
    }

    /**
     * 获取系统内置变量 
     */
    public function global_assign($globalParams = [])
    {
        if (empty($globalParams)) {
            $globalParams = tpCache('global');
        }
        $this->assign('global', $globalParams);
    }
}