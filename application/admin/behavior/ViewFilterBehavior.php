<?php

namespace app\admin\behavior;

use think\Db;

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
        $this->weappUpgrade($params);
    }

    /**
     * 每个插件主入口index页面进行更新包检测
     */
    private function weappUpgrade(&$params)
    {
        $ca = self::$controllerName.'&'.self::$actionName;
        if ('Weapp&execute' == $ca && 'GET' == self::$method) {
            $code = input('param.sm/s');
            if (!empty($code)) {
                $url1 = url('Weapp/ajax_check_upgrade');
                $url2 = url('Weapp/OneKeyUpgrade');
                $url3 = url('Weapp/ajax_cancel_upgrade');
                $replace = <<<EOF
    <script type="text/javascript">
        $(function(){

            weapp_upgrade_1599613252();

            // 检测升级
            function weapp_upgrade_1599613252()
            {
                var code = '{$code}';
                $.ajax({
                    type : "GET",
                    url  : "{$url1}",
                    data : {code:code , _ajax:1},
                    dataType: 'json',
                    success: function(res) {
                        if (res.code == 1 && res.data.upgrade && res.data.upgrade.code == 2) {
                            var upgrade_str = res.data.upgrade.msg.upgrade;
                            var intro_str = res.data.upgrade.msg.intro;
                            var notice_str = res.data.upgrade.msg.notice;
                            intro_str += '<style type="text/css">.layui-layer-content{height:270px!important;text-align:left!important;}</style>';
                            upgrade_str = notice_str + intro_str + '<br/>' + upgrade_str;
                            //询问框
                            layer.confirm(upgrade_str, {
                                title: false,//'检测插件更新',
                                area: ['580px','400px'],
                                btn: ['立即升级','不再提醒'] //按钮
                                
                            }, function(){
                                layer.closeAll();
                                setTimeout(function(){
                                    upgrade_1599613252(code); // 请求后台
                                },200);
                                
                            }, function(){  
                                setTimeout(function(){
                                    cancel_upgrade_1599613252(code);
                                },200);
                            });  
                        }
                    }
                }); 
            }

            // 执行升级
            function upgrade_1599613252(code){
                layer_loading('升级中');
                $.ajax({
                    type : "GET",
                    url  : "{$url2}",
                    timeout : 360000, //超时时间设置，单位毫秒 设置了 1小时
                    data : {code:code, _ajax:1},
                    error: function(request) {
                        layer.closeAll();
                        layer.alert("升级失败，请第一时间联系技术协助！", {icon: 2, closeBtn: false, title:false}, function(){
                            window.location.reload();
                        });
                    },
                    success: function(res) {
                        layer.closeAll();
                        if(1 == res.code){
                            layer.alert('已升级最新版本!', {icon: 1, closeBtn: false, title:false}, function(){
                                window.location.reload();
                            });
                        }
                        else{
                            layer.alert(res.msg, {icon: 2, closeBtn: false, title:false}, function(){
                                window.location.reload();
                            });
                        }
                    }
                });                 
            }

            // 不再提醒
            function cancel_upgrade_1599613252(code){
                $.ajax({
                    type : "GET",
                    url  : "{$url3}",
                    data : {code:code, _ajax:1},
                    success: function(res) {
                        
                    }
                });                 
            }
        });
    </script>
</body>
EOF;
                $params = str_ireplace('</body>', $replace, $params);
            }
        }
    }
}
