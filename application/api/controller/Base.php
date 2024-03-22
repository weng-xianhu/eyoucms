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

namespace app\api\controller;
use app\common\controller\Common;
use think\Db;
use think\response\Json;
class Base extends Common {

    public $uipath = '';
    public $theme_style = '';
    public $theme_style_path = '';

    /**
     * 析构函数
     */
    function __construct() 
    {
        parent::__construct();
        $this->theme_style = THEME_STYLE;
        $this->theme_style_path = THEME_STYLE_PATH;
        $this->uipath = RUNTIME_PATH.'ui/'.$this->theme_style_path.'/';
    }
    
    /*
     * 初始化操作
     */
    public function _initialize() 
    {
        parent::_initialize();
        
        $this->set_global_variable();
    }

    /**
     * 设置全局模板变量 
     */
    public function set_global_variable()
    {
        // 设置全局模板变量
        if (!defined('MODULE_NAME')) {
            $request = \think\Request::instance();
            define('MODULE_NAME', $request->module());
        }
        $global_variable = array();
        $view_replace_str = config('view_replace_str');
        foreach ($view_replace_str as $key => $val) {
            $view_replace_str[$key] = preg_replace('/(.*?)(\/'.MODULE_NAME.'\/)(\w+)(.*?)/i', '${1}${2}'.$this->theme_style_path.'${4}', $val);
        }
        config('view_replace_str', $view_replace_str);
        $global_variable = array_merge($global_variable, config('view_replace_str'));
        $this->assign($global_variable);
    }
}