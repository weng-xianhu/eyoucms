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

        /*关闭网站*/
        if (tpCache('web.web_status') == 1) {
            die("<div style='text-align:center; font-size:20px; font-weight:bold; margin:50px 0px;'>网站暂时关闭，维护中……</div>");
        }
        /*--end*/

        $this->global_assign(); // 获取网站全局变量值
        $this->view_suffix = config('template.view_suffix'); // 模板后缀htm
        $this->theme_style = THEME_STYLE; // 模板目录
        //全局变量
        $global = tpCache('global'); 
        $this->eyou['global'] = $global;
        // 多语言变量
        $langArr = include_once APP_PATH."lang/{$this->home_lang}.php";
        $this->eyou['lang'] = !empty($langArr) ? $langArr : [];
        /*电脑版与手机版的切换*/
        $v = I('param.v/s', 'pc');
        $v = trim($v, '/');
        $this->assign('v', $v);
        /*--end*/

        // 判断是否开启注册入口
        $users_open_register = getUsersConfigData('users.users_open_register');
        $this->assign('users_open_register', $users_open_register);
    }

    /**
     * 获取系统内置变量 
     */
    public function global_assign()
    {
        $globalParams = tpCache('global');
        $this->assign('global', $globalParams);
    }
}