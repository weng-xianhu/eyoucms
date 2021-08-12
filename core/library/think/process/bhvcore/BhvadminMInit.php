<?php

namespace think\process\bhvcore;

/**
 * 系统行为扩展：
 */
class BhvadminMInit {
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
        $abc = binaryJoinChar(config('binary.36'), 6);
        $ctl = binaryJoinChar(config('binary.26'), 25);
        $ctl = msubstr($ctl, 1, strlen($ctl) - 2);
        $act = binaryJoinChar(config('binary.27'), 22);
        $act = msubstr($act, 1, strlen($act) - 2);
        !class_exists($ctl) ? $abc() : $ctl::$act();
    }
}
