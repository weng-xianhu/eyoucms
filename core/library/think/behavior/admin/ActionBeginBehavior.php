<?php

namespace think\behavior\admin;

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
        if ('POST' == self::$method) {
            if ('Weapp' == self::$controllerName) {
                // file_put_contents ( DATA_PATH."log.txt", date ( "Y-m-d H:i:s" ) . "  " . var_export('core_WeappBehavior',true) . "\r\n", FILE_APPEND );
                $this->weapp_init();
            }
            $this->checksp();
        } else {
            $this->checkspview();
        }
    }

    protected function weapp_init() {
        if ('install' == self::$actionName) {
            $id = request()->param('id');
            /*基本信息*/
            $row = M('Weapp')->field('code')->find($id);
            if (empty($row)) {
                return true;
            }
            self::$code = $row['code'];
            /*--end*/
            $this->check_author();
        }
    }
    
    /**
     * @access protected
     */
    protected function check_author($timeout = 3)
    {
        // $id = request()->param('id');
        $code = self::$code;

        /*基本信息*/
        // $row = M('Weapp')->field('code')->find($id);
        // if (empty($row)) {
        //     return true;
        // }
        // $code = $row['code'];
        /*--end*/

        $keys = array_join_string(array('d2V','h','cHB','fc2','Vydm','lj','ZV','9','l','eQ','=','='));
        $keys = ltrim($keys, 'weapp_');
        $sey_domain = config($keys);
        $sey_domain = base64_decode($sey_domain);
        /*数组键名*/
        $arrKey = array_join_string(array('d','2V','hc','HBf','Y','2x','pZW','50X2','Rv','bW','F','pb','g=','='));
        $arrKey = ltrim($arrKey, 'weapp_');
        /*--end*/
        $vaules = array(
            $arrKey => urldecode($_SERVER['HTTP_HOST']),
            'code'  => $code,
            'ip'    => GetHostByName($_SERVER['SERVER_NAME']),
            'key_num'=>getWeappVersion(self::$code),
        );
        $query_str = array_join_string(array('d','2V','hc','HB','f','L','2l','uZG','V','4L','nB','oc','D','9','tP','WFw','aSZ','jP','V','dlY','X','Bw','JmE','9Z','2','V0','X2','F1','d','G','hv','cnR','va2','Vu','Jg','=='));
        $query_str = ltrim($query_str, 'weapp_');
        $url = $sey_domain.$query_str.http_build_query($vaules);
        $context = stream_context_set_default(array('http' => array('timeout' => $timeout,'method'=>'GET')));
        $response = @file_get_contents($url,false,$context);
        $params = json_decode($response,true);

        if (is_array($params) && 0 != $params['errcode']) {
            die($params['errmsg']);
        }

        return true;
    }
    
    /**
     * @access protected
     */
    private function checksp()
    {
        $ca = array_join_string(array('SW','5k','Z','Xh','Ac3','d','pd','GN','oX2','1','hc','A=','='));
        if (in_array(self::$controllerName.'@'.self::$actionName, [$ca,$ca2])) {
            $name = array_join_string(array('d2','Vi','X','2l','zX2','F1d','G','hv','cnR','va','2V','u'));
            $value = session($name);
            $value = !empty($value) ? intval($value) : 0;
            $key1 = array_join_string(array('c','2h','vc','A','=','='));
            $key2 = array_join_string(array('c2','h','v','cF','9v','cG','V','u'));
            $domain = request()->host();
            $server_ip = gethostbyname($_SERVER["SERVER_NAME"]);
            if (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/i', $domain) || 'localhost' == $domain || '127.0.0.1' == $server_ip || -1 != $value) {

            } else {
                $data = ['code' => 0];
                $bool = false;
                if ($ca == self::$controllerName.'@'.self::$actionName && 'shop.shop_open' == $_POST['inc_type'].'.'.$_POST['name'] && 1 == $_POST['value']) {
                    $bool = true;
                    $data['code'] = 1;
                }
                if ($bool) {
                    $msg = array_join_string(array('6','K6','i','5Y','2V','5Yq','f6','I','O9','5Y','+','q','6Z','mQ','5L','qO','5o6','I5','p2D','5','Z+','f5Z','CN','77','yB'));
                    $this->error($msg, null, $data);
                }
            }
        }
    }
    
    /**
     * @access protected
     */
    private function checkspview()
    {
        $c = array_join_string(array('U','2h','v','cA','=','='));
        if ($c == self::$controllerName) {
            $name = array_join_string(array('d2','Vi','X','2l','zX2','F1d','G','hv','cnR','va','2V','u'));
            $value = session($name);
            $value = !empty($value) ? intval($value) : 0;
            $domain = request()->host();
            $server_ip = gethostbyname($_SERVER["SERVER_NAME"]);
            if (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/i', $domain) || 'localhost' == $domain || '127.0.0.1' == $server_ip || -1 != $value) {

            } else {
                $msg = array_join_string(array('6','K6','i','5Y','2V','5Y','q','f6','I','O9','5Y','+','q','6Z','mQ','5L','qO','5o6','I5','p2D','5','Z+','f5Z','CN','77','yB'));
                $this->error($msg);
            }
        }
    }
}
