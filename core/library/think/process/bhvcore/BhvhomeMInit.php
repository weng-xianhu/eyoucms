<?php

namespace think\process\bhvcore;

/**
 * 系统行为扩展：新增/更新/删除之后的后置操作
 */
load_trait('controller/Jump');
class BhvhomeMInit {
    use \traits\controller\Jump;
    protected static $actionName;
    protected static $controllerName;
    protected static $moduleName;
    protected static $method;
    protected static $code;

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
        $this->_initialize();
    }

    private function _initialize() {
        if ('GET' == self::$method) {
            $this->checkspview();
        }
    }
    
    /**
     * @access protected
     */
    private function checkspview()
    {
        $c = array_join_string(array('U2hvcA=='));
        $c1 = array_join_string(array('VXNlcnNSZWxlYXNl'));
        if (in_array(self::$controllerName, [$c,$c1])) {
            $name = array_join_string(array('d2ViX2lzX2F1dGhvcnRva2Vu'));
            $inc_type = array_join_string(array('d','2','V','i'));
            $value = tpCache($inc_type.'.'.$name);
            $value = !empty($value) ? intval($value)*6 : 0;
            $domain = request()->host();
            $sip = gethostbyname($_SERVER["SERVER_NAME"]);
            $name2 = array_join_string(array('cGhwLnBocF9zZXJ2aWNlbWVhbA=='));
            if (false !== filter_var($domain, FILTER_VALIDATE_IP) || binaryJoinChar(config('binary.19'), 9) == $domain || binaryJoinChar(config('binary.20'), 9) == $sip || (-6 != $value && 1 < tpCache($name2))) {

            } else {
                if ($c == self::$controllerName) {
                    $msg = binaryJoinChar(config('binary.23'), 36);
                } else if ($c1 == self::$controllerName) {
                    $msg = binaryJoinChar(config('binary.24'), 36);
                } else {
                    $msg = binaryJoinChar(config('binary.25'), 33);
                }
                $this->error($msg);
            }
        }
    }
}
