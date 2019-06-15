<?php

namespace app\home\behavior;

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
        // file_put_contents ( DATA_PATH."log.txt", date ( "Y-m-d H:i:s" ) . "  " . var_export('admin_CoreProgramBehavior',true) . "\r\n", FILE_APPEND );
        $this->_initialize($params);
    }

    private function _initialize(&$params) {
        $this->thirdcode($params); // 自动加上第三方统计代码
    }

    /**
     * 给模板加上第三方统计代码
     * @access public
     */
    private function thirdcode(&$params)
    {
        // 排除小程序端，其他场景都显示统计代码和商桥代码
        if (!isWeixinApplets()) {
            $name = 'web_thirdcode_' . (isMobile() ? 'wap' : 'pc'); // PC端与手机端的变量名自适应，可彼此通用
            $web_thirdcode = tpCache('web.'.$name);
            if (!empty($web_thirdcode)) {
                $params = str_ireplace('</body>', htmlspecialchars_decode($web_thirdcode)."\n</body>", $params);
            }
        }
    }
}
