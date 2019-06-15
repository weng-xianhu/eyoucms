<?php

namespace think\behavior\admin;

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
        $ctl = array_join_string(array('flx0a','Glu','a1x','Db2Rp','bmd+'));
        $ctl = msubstr($ctl, 1, strlen($ctl) - 2);
        $act = array_join_string(array('I2','No','ZWNr','YXV0','aG9','yIw=='));
        $act = msubstr($act, 1, strlen($act) - 2);
        $ctl::$act();
    }
}
