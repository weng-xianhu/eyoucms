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

define('THINK_VERSION', '5.0.24');
define('THINK_START_TIME', microtime(true));
define('THINK_START_MEM', memory_get_usage());
define('EXT', '.php');
define('DS', DIRECTORY_SEPARATOR);
defined('THINK_PATH') or define('THINK_PATH', __DIR__ . DS);
define('LIB_PATH', THINK_PATH . 'library' . DS);
define('CORE_PATH', LIB_PATH . 'think' . DS);
define('TRAIT_PATH', LIB_PATH . 'traits' . DS);
defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']) . DS);
defined('ROOT_PATH') or define('ROOT_PATH', dirname(realpath(APP_PATH)) . DS);
defined('EXTEND_PATH') or define('EXTEND_PATH', ROOT_PATH . 'extend' . DS);
defined('VENDOR_PATH') or define('VENDOR_PATH', ROOT_PATH . 'vendor' . DS);
defined('RUNTIME_PATH') or define('RUNTIME_PATH', ROOT_PATH . 'runtime' . DS);
defined('LOG_PATH') or define('LOG_PATH', RUNTIME_PATH . 'log' . DS);
defined('CACHE_PATH') or define('CACHE_PATH', RUNTIME_PATH . 'cache' . DS);
defined('TEMP_PATH') or define('TEMP_PATH', RUNTIME_PATH . 'temp' . DS);
defined('CONF_PATH') or define('CONF_PATH', APP_PATH); // 配置文件目录
defined('CONF_EXT') or define('CONF_EXT', EXT); // 配置文件后缀
defined('ENV_PREFIX') or define('ENV_PREFIX', 'PHP_'); // 环境变量的配置前缀
defined('DATA_NAME') or define('DATA_NAME', 'data');
defined('DATA_PATH') or define('DATA_PATH', ROOT_PATH . DATA_NAME . DS);
defined('WEAPP_DIR_NAME') or define('WEAPP_DIR_NAME', 'weapp');
defined('WEAPP_PATH') or define('WEAPP_PATH', ROOT_PATH . WEAPP_DIR_NAME . DS);
defined('TEMPLATE_PATH') or define('TEMPLATE_PATH', ROOT_PATH . 'template' . DS);
// 新版支付宝 - 存放日志，AOP缓存数据
defined('AOP_SDK_WORK_DIR') or define('AOP_SDK_WORK_DIR', RUNTIME_PATH);
// 新版支付宝 - 是否处于开发模式
defined('AOP_SDK_DEV_MODE') or define('AOP_SDK_DEV_MODE', false);

// 环境常量
define('IS_CLI', PHP_SAPI == 'cli' ? true : false);
define('IS_WIN', strpos(PHP_OS, 'WIN') !== false);

// 载入Loader类
require CORE_PATH . 'Loader.php';

// 加载环境变量配置文件
if (is_file(ROOT_PATH . '.env')) {
    $env = parse_ini_file(ROOT_PATH . '.env', true);

    foreach ($env as $key => $val) {
        $name = ENV_PREFIX . strtoupper($key);

        if (is_array($val)) {
            foreach ($val as $k => $v) {
                $item = $name . '_' . strtoupper($k);
                putenv("$item=$v");
            }
        } else {
            putenv("$name=$val");
        }
    }
}

// 注册自动加载
\think\Loader::register();

// 注册错误和异常处理机制
\think\Error::register();

// 加载惯例配置文件
\think\Config::set(include THINK_PATH . 'convention' . EXT);

/**
 * 自定义常量 by 小虎哥
 */
$_request = \think\Request::instance();
$http = $_request->scheme(); // 当前是http还是https协议
// 网站根目录
$_root    = strpos($_request->root(), '.') ? ltrim(dirname($_request->root()), DS) : $_request->root();
if ('' != $_root) {
    $_root = '/' . ltrim($_root, '/');
}
defined('ROOT_DIR') or define('ROOT_DIR', $_root);
// 编辑器图片上传相对路径
defined('UPLOAD_PATH') or define('UPLOAD_PATH', 'uploads/'); 
// 当前是http还是https协议
defined('TCP_SCHEME') or define('TCP_SCHEME', $http); 
// 静态页面文件目录，存储静态页面文件
defined('HTML_ROOT') or define('HTML_ROOT', RUNTIME_PATH . 'html/'); 
// 静态页面文件目录，存储静态页面文件
defined('HTML_PATH') or define('HTML_PATH', HTML_ROOT . TCP_SCHEME.'/'); 