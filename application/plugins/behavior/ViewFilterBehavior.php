<?php

namespace app\plugins\behavior;

/**
 * 系统行为扩展：
 */
class ViewFilterBehavior {
    protected static $actionName;
    protected static $controllerName;
    protected static $moduleName;
    protected static $method;

    /**
     * 构造方法
     * @param Request $request Request对象
     * @access public
     */
    public function __construct()
    {

    }

    // 行为扩展的执行入口必须是run
    public function run(&$params){
        self::$actionName = request()->action();
        self::$controllerName = request()->controller();
        self::$moduleName = request()->module();
        self::$method = request()->method();
        $this->_initialize($params);
    }

    private function _initialize(&$params) {
        // $this->eyGlobalJs($params); // 全局JS
        $this->AppFootprintJsCode($params); //当问答调用前端公共头部时，登录需要引入以下js
    }
    
    /**
     * 全局JS
     * @access public
     */
    private function eyGlobalJs(&$params)
    {
        if (in_array(self::$controllerName, ['Ask'])) {
            $root_dir = ROOT_DIR;
            $version   = getCmsVersion();
            $srcurl = get_absolute_url("{$root_dir}/public/static/common/js/ey_global.js?v={$version}");
            $JsHtml = <<<EOF
<script type="text/javascript" src="{$srcurl}"></script>
EOF;
            // 追加替换JS
            $params = str_ireplace('</head>', htmlspecialchars_decode($JsHtml)."\n</head>", $params);
        }
    }
    
    /**
     * 当问答调用前端公共头部时，登录需要引入以下js
     * @access public
     */
    private function AppFootprintJsCode(&$params)
    {
        if (in_array(self::$controllerName, ['Ask'])) {
            $root_dir = ROOT_DIR;
            $version   = getCmsVersion();
            $lang = get_home_lang();
            $lang_str = config('lang_switch_on') ? " var __lang__='{$lang}';" : '';
            $srcurl = get_absolute_url("{$root_dir}/public/static/common/js/ey_footer.js?v={$version}.0");
            $ey_footer_js = <<<EOF
<script type="text/javascript">var root_dir="{$root_dir}";var ey_aid=0;{$lang_str}</script>
<script language="javascript" type="text/javascript" src="{$srcurl}"></script>
EOF;
            $params = str_ireplace('</body>', $ey_footer_js."\n</body>", $params);
        }
    }
}
