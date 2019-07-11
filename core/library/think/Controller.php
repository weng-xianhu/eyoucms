<?php

namespace think;

use think\exception\ValidateException;
use traits\controller\Jump;

Loader::import('controller/Jump', TRAIT_PATH, EXT);

class Controller
{
    use Jump;

    /**
     * @var \think\View 视图类实例
     */
    protected $view;

    /**
     * @var \think\Request Request 实例
     */
    protected $request;

    /**
     * @var bool 验证失败是否抛出异常
     */
    protected $failException = false;

    /**
     * @var bool 是否批量验证
     */
    protected $batchValidate = false;

    /**
     * @var array 前置操作方法列表
     */
    protected $beforeActionList = [];

    /**
     * 主体语言（语言列表中最早一条）
     */
    public $main_lang = 'cn';

    /**
     * 后台当前语言
     */
    public $admin_lang = 'cn';

    /**
     * 前台当前语言
     */
    public $home_lang = 'cn';

    /**
     * 子目录路径
     */
    public $root_dir = ROOT_DIR;

    /**
     * CMS版本号
     */
    public $version = null;

    /**
     * 构造方法
     * @access public
     * @param Request $request Request 对象
     */
    public function __construct(Request $request = null)
    {
        if (is_null($request)) {
            $request = Request::instance();
        }
        $this->request = $request;

        if (!defined('IS_AJAX')) {
            $this->request->isAjax() ? define('IS_AJAX',true) : define('IS_AJAX',false);  // 
        }
        if (!defined('IS_GET')) {
            ($this->request->method() == 'GET') ? define('IS_GET',true) : define('IS_GET',false);  // 
        }
        if (!defined('IS_POST')) {
            ($this->request->method() == 'POST') ? define('IS_POST',true) : define('IS_POST',false);  // 
        }
        if (!defined('IS_AJAX_POST')) {
            ($this->request->isAjax() && $this->request->method() == 'POST') ? define('IS_AJAX_POST',true) : define('IS_AJAX_POST',false);  // 
        }
        
        !defined('MODULE_NAME') && define('MODULE_NAME',$this->request->module());  // 当前模块名称是
        !defined('CONTROLLER_NAME') && define('CONTROLLER_NAME',$this->request->controller()); // 当前控制器名称
        !defined('ACTION_NAME') && define('ACTION_NAME',$this->request->action()); // 当前操作名称是
        !defined('PREFIX') && define('PREFIX',Config::get('database.prefix')); // 数据库表前缀
        !defined('SYSTEM_ADMIN_LANG') && define('SYSTEM_ADMIN_LANG', Config::get('global.admin_lang')); // 后台语言变量
        !defined('SYSTEM_HOME_LANG') && define('SYSTEM_HOME_LANG', Config::get('global.home_lang')); // 前台语言变量

        // 自动判断手机端和PC，以及PC/手机自适应模板 by 小虎哥 2018-05-10
        $v = I('param.v/s', 'pc');
        $v = trim($v, '/');
        $is_mobile = 0;
        if ($v == 'mobile') {
            $is_mobile = 1;
        }
        if((isMobile() || $is_mobile == 1) && file_exists(ROOT_PATH.'template/mobile')) {
            !defined('THEME_STYLE') && define('THEME_STYLE', 'mobile'); // 手机端模板
        } else {
            !defined('THEME_STYLE') && define('THEME_STYLE', 'pc'); // pc端模板
        }
        if (in_array($this->request->module(), ['home','user'])) {
            Config::set('template.view_path', './template/'.THEME_STYLE.'/');
        } else if (in_array($this->request->module(), array('admin'))) {
            if ('weapp' == strtolower($this->request->controller()) && 'execute' == strtolower($this->request->action())) {
                Config::set('template.view_path', '.'.ROOT_DIR.'/'.WEAPP_DIR_NAME.'/'.$this->request->param('sm').'/template/');
            }
        }
        // -------end

        $this->view    = View::instance(Config::get('template'), Config::get('view_replace_str'));

        /*多语言*/
        $this->home_lang = get_home_lang();
        $this->admin_lang = get_admin_lang();
        $this->main_lang = get_main_lang();
        null === $this->version && $this->version = getCmsVersion();
        $this->assign('home_lang', $this->home_lang);
        $this->assign('admin_lang', $this->admin_lang);
        $this->assign('main_lang', $this->main_lang);
        $this->assign('version', $this->version);
        /*--end*/
        
        $param = $this->request->param();
        if (isset($param['clear']) || Config::get('app_debug') === true) {

        } else {
            read_html_cache(); // 尝试从缓存中读取
        }

        // 控制器初始化
        $this->_initialize();

        // 前置操作方法
        if ($this->beforeActionList) {
            foreach ($this->beforeActionList as $method => $options) {
                is_numeric($method) ?
                $this->beforeAction($options) :
                $this->beforeAction($method, $options);
            }
        }
        // 逻辑化
        $this->coding();
    }

    /**
     * 初始化操作
     * @access protected
     */
    protected function _initialize()
    {
        $request = request();
        $searchformhidden = '';
        /*纯动态URL模式下，必须要传参的分组、控制器、操作名*/
        if (1 == config('ey_config.seo_pseudo') && 1 == config('ey_config.seo_dynamic_format')) {
            $searchformhidden .= '<input type="hidden" name="m" value="'.MODULE_NAME.'">';
            $searchformhidden .= '<input type="hidden" name="c" value="'.CONTROLLER_NAME.'">';
            $searchformhidden .= '<input type="hidden" name="a" value="'.ACTION_NAME.'">';
            if ('Weapp' == $request->get('c') && 'execute' == $request->get('a')) { // 插件的搜索
                $searchformhidden .= '<input type="hidden" name="sm" value="'.$request->get('sm').'">';
                $searchformhidden .= '<input type="hidden" name="sc" value="'.$request->get('sc').'">';
                $searchformhidden .= '<input type="hidden" name="sa" value="'.$request->get('sa').'">';
            }
            /*多语言*/
            $lang = $request->param('lang/s');
            empty($lang) && $lang = get_main_lang();
            $searchformhidden .= '<input type="hidden" name="lang" value="'.$lang.'">';
            /*--end*/
        }
        /*--end*/
        $searchform['hidden'] = $searchformhidden;
        $this->assign('searchform', $searchform);
    }

    public function _empty($name)
    {
        abort(404);
    }

    /**
     * 加工处理
     * @access protected
     */
    protected function coding()
    {
        \think\Coding::checksd();
        \think\Coding::resetcr();
    }

    /**
     * 前置操作
     * @access protected
     * @param  string $method  前置操作方法名
     * @param  array  $options 调用参数 ['only'=>[...]] 或者 ['except'=>[...]]
     * @return void
     */
    protected function beforeAction($method, $options = [])
    {
        if (in_array($this->request->module(), array('admin')) && 'Weapp' == $this->request->controller() && 'execute' == $this->request->action()) {
            /*插件的前置操作*/
            $sm = $this->request->param('sm');
            $sc = $this->request->param('sc');
            $sa = $this->request->param('sa');
            if (isset($options['only'])) {
                if (is_string($options['only'])) {
                    $options['only'] = explode(',', $options['only']);
                }

                if (!in_array($sa, $options['only'])) {
                    return;
                }
            } elseif (isset($options['except'])) {
                if (is_string($options['except'])) {
                    $options['except'] = explode(',', $options['except']);
                }

                if (in_array($sa, $options['except'])) {
                    return;
                }
            }

            call_user_func([$this, $method], $sm, $sc, $sa);
            /*--end*/
        } else {
            if (isset($options['only'])) {
                if (is_string($options['only'])) {
                    $options['only'] = explode(',', $options['only']);
                }

                if (!in_array($this->request->action(), $options['only'])) {
                    return;
                }
            } elseif (isset($options['except'])) {
                if (is_string($options['except'])) {
                    $options['except'] = explode(',', $options['except']);
                }

                if (in_array($this->request->action(), $options['except'])) {
                    return;
                }
            }

            call_user_func([$this, $method]);
        }
    }

    /**
     * 检测是否存在模板文件 by 小虎哥
     * @access public
     * @param string $template 模板文件或者模板规则
     * @return bool
     */
    protected function exists($template = '')
    {
        $bool = $this->view->exists($template);
        return $bool;
    }

    /**
     * 加载模板输出
     * @access protected
     * @param  string $template 模板文件名
     * @param  array  $vars     模板输出变量
     * @param  array  $replace  模板替换
     * @param  array  $config   模板参数
     * @return mixed
     */
    protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        $html = $this->view->fetch($template, $vars, $replace, $config);
        // \think\Coding::checkcr();
        /*尝试写入静态缓存*/
        $param = $this->request->param();
        if (isset($param['clear']) || false === config('app_debug')) {
            write_html_cache($html);
        }
        /*--end*/

        return $html;
    }

    /**
     * 渲染内容输出
     * @access protected
     * @param  string $content 模板内容
     * @param  array  $vars    模板输出变量
     * @param  array  $replace 替换内容
     * @param  array  $config  模板参数
     * @return mixed
     */
    protected function display($content = '', $vars = [], $replace = [], $config = [])
    {
        return $this->view->display($content, $vars, $replace, $config);
    }

    /**
     * 模板变量赋值
     * @access protected
     * @param  mixed $name  要显示的模板变量
     * @param  mixed $value 变量的值
     * @return $this
     */
    protected function assign($name, $value = '')
    {
        $this->view->assign($name, $value);

        return $this;
    }

    /**
     * 初始化模板引擎
     * @access protected
     * @param array|string $engine 引擎参数
     * @return $this
     */
    protected function engine($engine)
    {
        $this->view->engine($engine);

        return $this;
    }

    /**
     * 设置验证失败后是否抛出异常
     * @access protected
     * @param bool $fail 是否抛出异常
     * @return $this
     */
    protected function validateFailException($fail = true)
    {
        $this->failException = $fail;

        return $this;
    }

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @param  mixed        $callback 回调方法（闭包）
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate($data, $validate, $message = [], $batch = false, $callback = null)
    {
        if (is_array($validate)) {
            $v = Loader::validate();
            $v->rule($validate);
        } else {
            // 支持场景
            if (strpos($validate, '.')) {
                list($validate, $scene) = explode('.', $validate);
            }

            $v = Loader::validate($validate);

            !empty($scene) && $v->scene($scene);
        }

        // 批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        // 设置错误信息
        if (is_array($message)) {
            $v->message($message);
        }

        // 使用回调验证
        if ($callback && is_callable($callback)) {
            call_user_func_array($callback, [$v, &$data]);
        }

        if (!$v->check($data)) {
            if ($this->failException) {
                throw new ValidateException($v->getError());
            }

            return $v->getError();
        }

        return true;
    }
}
