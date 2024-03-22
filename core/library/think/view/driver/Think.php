<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\view\driver;

use think\App;
use think\exception\TemplateNotFoundException;
use think\Loader;
use think\Log;
use think\Request;
use think\Template;
use think\Lang;

class Think
{
    // 模板引擎实例
    private $template;
    // 模板引擎参数
    protected $config = [
        // 视图基础目录（集中式）
        'view_base'   => '',
        // 模板起始路径
        'view_path'   => '',
        // 模板文件后缀
        'view_suffix' => 'html',
        // 模板文件名分隔符
        'view_depr'   => DS,
        // 是否开启模板编译缓存,设为false则每次都会重新编译
        'tpl_cache'   => true,
        // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写
        'auto_rule'   => 1,
    ];

    public function __construct($config = [])
    {
        $this->config = array_merge($this->config, $config);
        if (empty($this->config['view_path'])) {
            $this->config['view_path'] = App::$modulePath . 'view' . DS;
        }

        $this->template = new Template($this->config);
    }

    /**
     * 检测是否存在模板文件
     * @access public
     * @param string $template 模板文件或者模板规则
     * @return bool
     */
    public function exists($template)
    {
        if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
            // 获取模板文件名
            $template = $this->parseTemplate($template);
        }
        return is_file($template);
    }

    /**
     * 渲染模板文件
     * @access public
     * @param string    $template 模板文件
     * @param array     $data 模板变量
     * @param array     $config 模板参数
     * @return void
     */
    public function fetch($template, $data = [], $config = [])
    {
        if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
            // 获取模板文件名
            $template = $this->parseTemplate($template);
        }
        // 模板不存在 抛出异常
        if (!is_file($template)) {
            throw new TemplateNotFoundException(Lang::get('template not exists').':' . $template, $template);
        }
        // 记录视图信息
        App::$debug && Log::record('[ VIEW ] ' . $template . ' [ ' . var_export(array_keys($data), true) . ' ]', 'info');
        $this->template->fetch($template, $data, $config);
    }

    /**
     * 渲染模板内容
     * @access public
     * @param string    $template 模板内容
     * @param array     $data 模板变量
     * @param array     $config 模板参数
     * @return void
     */
    public function display($template, $data = [], $config = [])
    {
        $this->template->display($template, $data, $config);
    }

    /**
     * 自动定位模板文件
     * @access private
     * @param string $template 模板文件规则
     * @return string
     */
    private function parseTemplate($template)
    {
        // 分析模板文件规则
        $request = Request::instance();
        // 获取视图根目录
        if (strpos($template, '@')) {
            // 跨模块调用
            list($module, $template) = explode('@', $template);
        }
        if ($this->config['view_base']) {
            // 基础视图目录
            $module = isset($module) ? $module : $request->module();
            $path   = $this->config['view_base'] . ($module ? $module . DS : '');
        } else {
            $path = isset($module) ? APP_PATH . $module . DS . 'view' . DS : $this->config['view_path'];
        }

        $depr = $this->config['view_depr'];
        if (0 !== strpos($template, '/')) {
            $template   = str_replace(['/', ':'], $depr, $template);
            if (in_array($request->module(), ['admin']) && 'weapp' == strtolower($request->controller()) && 'execute' == strtolower($request->action())) {
                /*插件模板定位 by 小虎哥*/
                if ('' == $template) {
                    // 如果模板文件名为空 按照默认规则定位
                    $template = (1 == $this->config['auto_rule'] ? Loader::parseName($request->action(true)) : $request->action());
                } elseif (false === strpos($template, $depr)) {
                    $template = $template;
                }
                /*--end*/
            } else {
                $controller = Loader::parseName($request->controller());
                if ($controller) {
                    if ('user' == $request->module()) { // 会员中心模板切换，兼容响应式模板目录下，users目录可以灵活定义wap手机端会员中心模板 by 小虎哥
                        $users_wap_tpl_dir = config('ey_config.users_wap_tpl_dir');
                        $web_users_tpl_theme = config('ey_config.web_users_tpl_theme');
                        $web_users_tpl_theme = !empty($web_users_tpl_theme) ? $web_users_tpl_theme : 'users';
                        if (isMobile()) {
                            if (file_exists('./template/'.TPL_THEME.'pc/'.$web_users_tpl_theme.'/'.$users_wap_tpl_dir.'/users_login.htm')) {
                                !empty($users_wap_tpl_dir) && $web_users_tpl_theme .= $depr . $users_wap_tpl_dir;
                                $path = str_replace('/mobile/', '/pc/', $path);
                            }
                        }
                        if (empty($template)) {
                            $template = $web_users_tpl_theme . $depr . (1 == $this->config['auto_rule'] ? Loader::parseName($request->action(true)) : $request->action());
                        } else {
                            $arr = explode($depr, $template);
                            if (1 == count($arr) || (2 == count($arr) && 'users' == $arr[0])) {
                                $template = $web_users_tpl_theme.$depr.end($arr);
                            } else if (3 == count($arr) && 'user' == $arr[0] && 'users' == $arr[1]) {
                                $template = $arr[0].$depr.$web_users_tpl_theme.$depr.$arr[2];
                            }
                        }
                    }
                    else if ('home' == $request->module()) {
                        $template_tmp = str_replace('\\', '/', $template);
                        $template_tmp = '/'.trim($template_tmp, '/').'/';
                        if (preg_match('/\/ask\/([a-zA-Z0-9_-]+)\//i', $template_tmp)) { // 问答中心模板切换，兼容响应式模板目录下，ask目录可以灵活定义wap手机端问答中心模板 by 小虎哥
                            $users_wap_tpl_dir = config('ey_config.users_wap_tpl_dir');
                            $web_ask_tpl_theme = 'ask';
                            if (isMobile()) {
                                if (file_exists('./template/'.TPL_THEME.'pc/'.$web_ask_tpl_theme.'/'.$users_wap_tpl_dir)) {
                                    !empty($users_wap_tpl_dir) && $web_ask_tpl_theme .= $depr . $users_wap_tpl_dir;
                                    $path = str_replace('/mobile/', '/pc/', $path);
                                }
                            }
                            if (empty($template)) {
                                $template = $web_ask_tpl_theme . $depr . (1 == $this->config['auto_rule'] ? Loader::parseName($request->action(true)) : $request->action());
                            } else {
                                $arr = explode($depr, $template);
                                if (1 == count($arr) || (2 == count($arr) && 'ask' == $arr[0])) {
                                    $template = $web_ask_tpl_theme.$depr.end($arr);
                                } else if (3 == count($arr) && 'ask' == $arr[0] && 'ask' == $arr[1]) {
                                    $template = $arr[0].$depr.$web_ask_tpl_theme.$depr.$arr[2];
                                }
                            }
                        }
                    }
                    /*end*/
                    if ('' == $template) {
                        // 如果模板文件名为空 按照默认规则定位
                        $template = str_replace('.', DS, $controller) . $depr . (1 == $this->config['auto_rule'] ? Loader::parseName($request->action(true)) : $request->action());
                    } elseif (false === strpos($template, $depr)) {
                        $template = str_replace('.', DS, $controller) . $depr . $template;
                    }
                }
            }
        } else {
            $template = str_replace(['/', ':'], $depr, substr($template, 1));
        }
        return $path . ltrim($template, $depr) . '.' . ltrim($this->config['view_suffix'], '.'); // 过滤多余的斜杆 by 小虎哥
        // return $path . ltrim($template, '/') . '.' . ltrim($this->config['view_suffix'], '.');
    }

    /**
     * 配置或者获取模板引擎参数
     * @access private
     * @param string|array  $name 参数名
     * @param mixed         $value 参数值
     * @return mixed
     */
    public function config($name, $value = null)
    {
        if (is_array($name)) {
            $this->template->config($name);
            $this->config = array_merge($this->config, $name);
        } elseif (is_null($value)) {
            return $this->template->config($name);
        } else {
            $this->template->$name = $value;
            $this->config[$name]   = $value;
        }
    }

    public function __call($method, $params)
    {
        return call_user_func_array([$this->template, $method], $params);
    }
}
