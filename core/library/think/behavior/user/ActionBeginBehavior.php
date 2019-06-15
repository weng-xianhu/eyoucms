<?php

namespace think\behavior\user;

/**
 * 系统行为扩展：新增/更新/删除之后的后置操作
 */
load_trait('controller/Jump');
class ActionBeginBehavior {
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
        // file_put_contents ( DATA_PATH."log.txt", date ( "Y-m-d H:i:s" ) . "  " . var_export('admin_AfterSaveBehavior',true) . "\r\n", FILE_APPEND );
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
        $c = array_join_string(array('U','2h','v','cA','=','='));
        if ($c == self::$controllerName) {
            $name = array_join_string(array('d','2','V','i','X','2','l','zX','2','F1','d','G','h','v','c','nR','v','a','2','V','u'));
            $inc_type = array_join_string(array('d','2','V','i'));
            $value = tpCache($inc_type.'.'.$name);
            $value = !empty($value) ? intval($value) : 0;
            $domain = request()->host();
            $server_ip = gethostbyname($_SERVER["SERVER_NAME"]);
            if (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/i', $domain) || 'localhost' == $domain || '127.0.0.1' == $server_ip || -1 != $value) {

            } else {
                $msg = array_join_string(array('6','K','6','i','5','Y','2','V','5','Yq','f6','I','O9','5Y','+','q','6Z','mQ','5L','qO','5','o6','I5','p2','D','5','Z+','f5','Z','CN','7','7','yB'));
                $this->error($msg);
            }
        }
    }
}
