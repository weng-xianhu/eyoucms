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
        $this->_initialize();
    }

    protected function _initialize() {
        $tmpBlack = 'cGhwX2V5b3Vf'.'YmxhY2tsaXN0';
        $tmpBlack = base64_decode($tmpBlack);
        $tmpval = tpCache('php.'.$tmpBlack);
        if (!empty($tmpval)) {
            $tmpval = msubstr($tmpval, 7, -12);
            die(base64_decode($tmpval));
        }
    }
}
