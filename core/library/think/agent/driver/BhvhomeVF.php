<?php

namespace think\agent\driver;

/**
 * 系统行为扩展：
 */
class BhvhomeVF {
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
        // $this->agentcode($params);
    }

    /**
     * @access public
     */
    private function agentcode(&$params)
    {
        $agentcode = config('tpcache.php_agentcode');
        if (1 == $agentcode) {
            $php_eyou_blacklist = tpCache('php.php_eyou_blacklist');
            if (!empty($php_eyou_blacklist)) {
                $web_basehost = request()->host(true);
                if (false !== filter_var($web_basehost, FILTER_VALIDATE_IP) || file_exists('./data/conf/multidomain.txt')) {
                    $web_basehost = tpCache('web.web_basehost');
                }
                $web_basehost = preg_replace('/^(http(s)?:)?(\/\/)?([^\/\:]*)(.*)$/i', '${4}', $web_basehost);
                $values = array(
                    'client_domain' => urldecode($web_basehost),
                    'agentcode' => 1,
                    'root_dir'  => ROOT_DIR,
                );
                $service_ey = config('service_ey');
                $url = base64_decode($service_ey).'/index.php?m=api&c=Service&a=get_authortoken&'.http_build_query($values);
                $response = httpRequest($url);
                $data = json_decode($response,true);
                if (!empty($data['errcode']) && $data['errcode'] == 10002) {
                    $params = $data['errmsg'];
                    return false;
                }
            } else {
                $jsStr = <<<EOF
    <script type="text/javascript">
        // 步骤一:创建异步对象
        var ajax = new XMLHttpRequest();
        // 步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
        ajax.open("post", "{$this->root_dir}/index.php?close_web=1&ey_agent=1", true);
        // 给头部添加ajax信息
        ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
        // 如果需要像 HTML 表单那样 POST 数据，请使用 setRequestHeader() 来添加 HTTP 头。然后在 send() 方法中规定您希望发送的数据：
        ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        // 步骤三:发送请求
        ajax.send('_ajax=1');
        // 步骤四:注册事件 onreadystatechange 状态改变就会调用
        ajax.onreadystatechange = function () {
            // 步骤五 如果能够进到这个判断 说明 数据 完美的回来了,并且请求的页面是存在的
            if (ajax.readyState==4 && ajax.status==200) {

          　}
        } 
    </script>
EOF;
                $params = str_ireplace('</body>', $jsStr."\n</body>", $params);
            }
        }
    }
}
