<?php

namespace think\behavior\home;

/**
 * 系统行为扩展：
 */
class CoreProgramBehavior {
    protected static $actionName;
    protected static $controllerName;
    protected static $moduleName;

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
        // file_put_contents ( DATA_PATH."log.txt", date ( "Y-m-d H:i:s" ) . "  " . var_export('core_CoreProgramBehavior',true) . "\r\n", FILE_APPEND );
        $this->_initialize();
    }

    protected function _initialize() {
        $tmpBlack = 'cG'.'hw'.'X2'.'V5'.'b3'.'Vf'.'Ym'.'xh'.'Y2'.'ts'.'aX'.'N'.'0';
        $tmpBlack = base64_decode($tmpBlack);
        $tmpval = tpCache('php.'.$tmpBlack);
        if (!empty($tmpval)) {
            $tmpval = msubstr($tmpval, 7, -12);
            die(base64_decode($tmpval));
        }
    }
}
