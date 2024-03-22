<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海口快推科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */

// session随机存放目录名称
$serial_number = DEFAULT_SERIALNUMBER;
$constsant_path = APP_PATH.'admin/conf/constant.php';
if (file_exists($constsant_path)) {
    require_once($constsant_path);
    $serial_number = substr(SERIALNUMBER, -8);
}
// end

// 多语言开启\禁用
$lang_switch_on = false;
$langnum_file = DATA_PATH.'conf'.DS.'lang_enable_num.txt';
if (file_exists($langnum_file)) {
    $langnum = @file_get_contents($langnum_file);
    if (!empty($langnum) && 1 < $langnum) {
        $lang_switch_on = true;
    }
}

// 不支持http请求的api，就通过文件改为https
$service_ey = 'aHR0cDovL3NlcnZpY2UuZXlvdWNtcy5jb20=';
$service_ey_token = '0763150235251e259b1a47f2838ecc26';
$service_ey2 = 'aHR0cDovL3NlcnZpY2UuNWZhLmNu';
if (file_exists("./data/conf/https_service.txt")) {
    $service_ey = 'aHR0cHM6Ly9zZXJ2aWNlLmV5b3VjbXMuY29t';
    $service_ey_token = '010fbcb69eb7f820b7297b4dc706e302';
    $service_ey2 = 'aHR0cHM6Ly9zZXJ2aWNlLjVmYS5jbg==';
}

// 多城市开启\禁用
$city_switch_on = false;
$citysite_file = DATA_PATH.'conf'.DS.'citysite.txt';
if (false === $lang_switch_on && file_exists($citysite_file)) {
    $citysite_value = @file_get_contents($citysite_file);
    if (!empty($citysite_value) && 0 < intval($citysite_value)) {
        $city_switch_on = true;
    }
}

// redis缓存
$redis_enable = $cache_prefix = '';
$redis_config_file = WEAPP_PATH.'Redis'.DS.'conf'.DS.'config.php';
if (file_exists($redis_config_file)) {
    require_once($redis_config_file);
    if (defined('WEAPP_REDIS_OPEN') && WEAPP_REDIS_OPEN == 1) {
        $redis_enable = 'redis';
        if (defined('WEAPP_REDIS_CACHE_PREFIX')) {
            $cache_prefix = WEAPP_REDIS_CACHE_PREFIX;
        } else {
            $cache_prefix = $serial_number.'_';
        }
    }
}

// session会话设置
$session_conf = [
    'id'             => '',
    // SESSION_ID的提交变量,解决flash上传跨域
    'var_session_id' => '',
    // SESSION 前缀
    'prefix'         => !empty($redis_enable) ? $redis_enable : 'think',
    // 驱动方式 支持redis memcache memcached
    'type'           => '',
    // 是否自动开启 SESSION
    'auto_start'     => true,
    // 主机
    // 'host'           => '127.0.0.1',
    // 端口
    // 'port'           => 11211,
    'path'  => 'data/session_'.$serial_number,
];
$session_file = APP_PATH.'admin/conf/session_conf.php';
if (file_exists($session_file)) {
    require_once($session_file);
    $session_conf_tmp = EY_SESSION_CONF;
    if (!empty($session_conf_tmp)) {
        $session_conf_tmp = json_decode($session_conf_tmp, true);
        if (!empty($session_conf_tmp) && is_array($session_conf_tmp)) {
            isset($session_conf_tmp['expire']) && $session_conf_tmp['expire'] = intval($session_conf_tmp['expire']);
            $session_conf = array_merge($session_conf, $session_conf_tmp);
        } else {
            $session_conf = array_merge($session_conf, ['expire'=>7200]);
        }
    }
}

return array(
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // 默认Host地址
    'app_host'               => '',
    // 应用命名空间
    'app_namespace'          => 'app',
    // 应用调试模式
    'app_debug'              => false,
    // 应用Trace
    'app_trace'              => false,
    // 应用模式状态
    'app_status'             => '',
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => array(),
    // 扩展函数文件
    'extra_file_list'        => array(APP_PATH . 'helper' . EXT, THINK_PATH . 'helper' . EXT, APP_PATH . 'function' . EXT),
    // 默认输出类型
    'default_return_type'    => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'json',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'PRC',
    // 是否开启多语言
    'lang_switch_on'         => $lang_switch_on,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => 'strip_sql,htmlspecialchars', // htmlspecialchars
    // 默认语言
    'default_lang'           => 'cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,
    // 是否https链接
    'is_https'               => false,
    // 是否开启多城市
    'city_switch_on'         => $city_switch_on,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module'         => 'home',
    // 禁止访问模块
    'deny_module_list'       => array('common'),
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => array('ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'),
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // URL伪静态后缀
    'url_html_suffix'        => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否开启路由
    'url_route_on'           => true,
    // 路由使用完整匹配
    'route_complete_match'   => false,
    // 路由配置文件（支持配置多个）
    'route_config_file'      => array('route'),
    // 是否强制使用路由
    'url_route_must'         => false,
    // 域名部署
    'url_domain_deploy'      => false,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => false,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 表单请求类型伪装变量
    'var_method'             => '_method',
    // 表单ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表单pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache'          => false,
    // 请求缓存有效期
    'request_cache_expire'   => null,
    // 全局请求缓存排除规则
    'request_cache_except'   => array(),

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template'               => array(
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 模板路径
        'view_path'    => '',
        // 模板后缀
        'view_suffix'  => 'htm',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end'   => '}',
    ),

    // 视图输出字符串内容替换
    'view_replace_str'       => array(),
    // 默认跳转页面对应的模板文件
    'dispatch_error_tmpl' => 'public/static/common/dispatch_jump.htm',
    // 默认成功跳转对应的模板文件
    'dispatch_success_tmpl' => 'public/static/common/dispatch_jump.htm', 

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件 
    'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',
    // errorpage 错误页面
    'error_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_error.tpl', 
    

    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => true,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '',

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log'                    => array(
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => DATA_PATH.'logs/',
        //单个日志文件的大小限制，超过后会自动记录到第二个文件
        'file_size' =>2097152,
        //日志的时间格式，默认是` c `
        'time_format' =>'c',
        // error和sql日志单独记录
        'apart_level' => ['error','sql','notice'],
        // 日志记录级别
        'level' => array('log','info','notice','error','sql'),
        // 日志开关  1 开启 0 关闭，最好同时开启 app_debug
        'switch' => 0,
    ),

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace'                  => array(
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ),

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

    'cache'                  => array(
        // 驱动方式
        'type'   => !empty($redis_enable) ? $redis_enable : 'File',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存前缀
        'prefix' => $cache_prefix,
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ),

    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session'                => $session_conf,

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'                 => array(
        // cookie 名称前缀
        'prefix'    => '',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '', // 设置为true时，通过阻止 JS 读取 Cookie 来 防止XSS 攻击
        // 是否使用 setcookie
        'setcookie' => true,
    ),
    
    // +----------------------------------------------------------------------
    // | Memcache设置(支持集群)
    // +----------------------------------------------------------------------
    'memcache'             => array(
        'switch'    => 0, // 0 关闭，1 开启
        'host' => '127.0.0.1,127.0.0.2', // 多个集群IP用,隔开
        'port' => '11211,11212', // 多个集群端口号用,隔开
        'expire' => 0,
    ),

    //分页配置
    'paginate'      => array(
        'type'      => 'eyou',
        'var_page'  => 'page',
        'list_rows' => 15,
    ),
    // 密码加密串
    'AUTH_CODE' => "!*&^eyoucms<>|?", //安装完毕之后不要改变，否则所有密码都会出错
    
    // 核心字符串
    'service_ey' => $service_ey,
    'service_ey_token' => $service_ey_token,
    'service_ey2' => $service_ey2,
    
    // +----------------------------------------------------------------------
    // | 验证码
    // +----------------------------------------------------------------------
    'captcha' => array(
        'default'    => [
            // 验证码字符集合
            'codeSet'  => '2345678abcdefhijkmnpqrstuvwxyz', 
            // 验证码字体大小(px)
            'fontSize' => 35, 
            // 是否画混淆曲线
            'useCurve' => false, 
            // 是否添加杂点
            'useNoise' => false, 
            // 验证码图片高度
            'imageH'   => 0,
            // 验证码图片宽度
            'imageW'   => 0, 
            // 验证码位数
            'length'   => 4, 
            // 验证成功后是否重置        
            'reset'    => false,
            // 验证码字体，不设置随机获取
            'fontttf' => '4.ttf',
        ],
        // 后台登录验证码配置
        'admin_login'   => [
            'is_on' => 1, // 开关
            'config' => [],
        ],
        // 留言提交验证码配置
        'guestbook'   => [
            'is_on' => 0, // 开关
            'config' => [],
        ],
        // 会员登录验证码配置
        'users_login'   => [
            'is_on' => 0, // 开关
            'config' => [],
        ],
        // 会员注册验证码配置
        'users_reg'   => [
            'is_on' => 0, // 开关
            'config' => [],
        ],
        // 会员找回密码验证码配置
        'users_retrieve_password'   => [
            'is_on' => 0, // 开关
            'config' => [],
        ],
    ),

    // +----------------------------------------------------------------------
    // | 404页面跳转
    // +----------------------------------------------------------------------
    'http_exception_template' => array(
        // 还可以定义其它的HTTP status
        400 => ROOT_PATH.'public/errpage/400.html',
        // 还可以定义其它的HTTP status
        401 => ROOT_PATH.'public/errpage/401.html',
        // 还可以定义其它的HTTP
        403 => ROOT_PATH.'public/errpage/403.html',
        // 还可以定义其它的HTTP
        404 => ROOT_PATH.'public/errpage/404.html',
        // 还可以定义其它的HTTP
        405 => ROOT_PATH.'public/errpage/405.html',
        // 还可以定义其它的HTTP
        500 => ROOT_PATH.'public/errpage/500.html',
        // 还可以定义其它的HTTP status
        503 => ROOT_PATH.'public/errpage/503.html',
    ),

    /**假设这个访问地址是 www.xxxxx.dev/home/goods/goodsInfo/id/1.html 
     *就保存名字为 home_goods_goodsinfo_1.html     
     *配置成这样, 指定 模块 控制器 方法名 参数名
     */
    // true 开启页面缓存
    'HTML_CACHE_STATUS' => true,
    // 缓存的页面，规则：模块 控制器 方法名 参数名
    'HTML_CACHE_ARR'    => [
        // 首页
        'home_Index_index'      => ['filename'=>'index', 'cache'=>7200],
        // [普通伪静态]文章
        'home_Article_index'    => ['filename'=>'channel', 'cache'=>7200],
        'home_Article_lists'    => ['filename'=>'lists', 'p'=>array('page','tid'), 'cache'=>7200],
        'home_Article_view'     => ['filename'=>'view', 'p'=>array('dirname','page','aid'), 'cache'=>7200],
        // [普通伪静态]产品
        'home_Product_index'    => ['filename'=>'channel', 'cache'=>7200],
        'home_Product_lists'    => ['filename'=>'lists', 'p'=>array('page','tid'), 'cache'=>7200],
        'home_Product_view'     => ['filename'=>'view', 'p'=>array('dirname','page','aid'), 'cache'=>7200],
        // [普通伪静态]图集
        'home_Images_index'     => ['filename'=>'channel', 'cache'=>7200],
        'home_Images_lists'     => ['filename'=>'lists', 'p'=>array('page','tid'), 'cache'=>7200],
        'home_Images_view'      => ['filename'=>'view', 'p'=>array('dirname','page','aid'), 'cache'=>7200],
        // [普通伪静态]下载
        'home_Download_index'   => ['filename'=>'channel', 'cache'=>7200],
        'home_Download_lists'   => ['filename'=>'lists', 'p'=>array('page','tid'), 'cache'=>7200],
        'home_Download_view'    => ['filename'=>'view', 'p'=>array('dirname','page','aid'), 'cache'=>7200],
        // [普通伪静态]视频
        'home_Media_index'    => ['filename'=>'channel', 'cache'=>7200],
        'home_Media_lists'    => ['filename'=>'lists', 'p'=>array('page','tid'), 'cache'=>7200],
        'home_Media_view'     => ['filename'=>'view', 'p'=>array('dirname','page','aid'), 'cache'=>7200],
        // [普通伪静态]专题
        'home_Special_index'     => ['filename'=>'channel', 'cache'=>7200],
        'home_Special_lists'     => ['filename'=>'lists', 'p'=>array('page','tid'), 'cache'=>7200],
        'home_Special_view'      => ['filename'=>'view', 'p'=>array('dirname','page','aid'), 'cache'=>7200],
        // [普通伪静态]单页
        'home_Single_index'     => ['filename'=>'channel', 'cache'=>7200],
        'home_Single_lists'     => ['filename'=>'lists', 'p'=>array('page','tid'), 'cache'=>7200],
        // [超短伪静态]列表页
        'home_Lists_index'      => ['filename'=>'lists', 'p'=>array('page','tid'), 'cache'=>7200],
        // [超短伪静态]内容页
        'home_View_index'       => ['filename'=>'view', 'p'=>array('dirname','page','aid'), 'cache'=>7200],
        // [标签页伪静态]列表页
        'home_Tags_index'       => ['filename'=>'tags', 'cache'=>7200],
        'home_Tags_lists'       => ['filename'=>'tags', 'p'=>array('page', 'tagid'), 'cache'=>7200],
    ],

    // +----------------------------------------------------------------------
    // | 短信设置
    // +----------------------------------------------------------------------
    // 开启调试模式，跳过手机接收短信这一块
    'sms_debug' => false,

    // +----------------------------------------------------------------------
    // | 邮件设置
    // +----------------------------------------------------------------------
    //邮件使用场景
    'send_email_scene' => [
        1   => ['scene'=>1], // 留言表单
        2   => ['scene'=>2], // 会员注册
        3   => ['scene'=>3], // 绑定邮箱
        4   => ['scene'=>4], // 找回密码
        5   => ['scene'=>5], // 订单付款
        6   => ['scene'=>6], // 订单发货
        20  => ['scene'=>20], // 会员投稿提醒
    ],
);
