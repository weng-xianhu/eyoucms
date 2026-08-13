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
        $this->security_verify($params);
        // $this->weappUpgrade($params);
    }

    private function security_verify(&$params)
    {
        $ctl_act = self::$controllerName.'@'.self::$actionName;
        $ctl_act_arr = ['Arctype@ajax_newtpl','Archives@ajax_newtpl','Index@ajax_theme_tplfile_add','Index@ajax_theme_tplfile_edit'];
        if ('GET' == self::$method && (in_array(self::$controllerName, ['Filemanager', 'Weapp']) || in_array($ctl_act, $ctl_act_arr))) {
            $security = tpSetting('security');
            // 已设置密保问题，继续验证流程
            if (!empty($security['security_ask_open']) && security_verify_func($ctl_act)) {

            } else {
                return true;
            }

            $admin_info = session('admin_info');
            $admin_id = !empty($admin_info['admin_id']) ? (int)$admin_info['admin_id'] : 0;

            // 优先检查 Session（快速路径）
            $session_key = 'security_verified_' . $admin_id;
            $is_verified = session($session_key);

            // 如果已验证，允许访问（不检查超时时间）
            if (!empty($is_verified)) {
                return true;
            }

            // Session 丢失时查数据库（兼容 CDN 环境）
            if ($admin_id > 0) {
                $admin_row = Db::name('admin')->field('security_verify_time,last_login')->where(['admin_id'=>$admin_id])->find();
                if (!empty($admin_row['security_verify_time']) && $admin_row['security_verify_time'] >= $admin_row['last_login']) {
                    session($session_key, $admin_row['security_verify_time']);
                    return true;
                }
            }

            $security_ask = $security['security_ask'];
            $url = url('Security/ajax_answer_verify', ['_ajax'=>1]);
            $replace = <<<EOF
    <script type="text/javascript">
        $(function(){
            autoload_security();
            function autoload_security()
            {
                layer.prompt({
                    id: 'layerid_1645598368',
                    title: '密保问题<style type="text/css">#layerid_1645598368 {font-size: 13px; font-family: "Microsoft Yahei", "Lucida Grande", Verdana, Lucida, Helvetica, Arial, sans-serif;}</style>',
                    btn: ['确定'],
                    shade: [0.7, '#fafafa'],
                    closeBtn: 3,
                    success: function(layero, index) {
                        var before_str = "<div style='margin: -8px 0px 10px 0px;color: red;font-weight: bold;'>{$security_ask}</div>";
                        $("#layerid_1645598368").prepend(before_str);
                        $("#layerid_1645598368").find('input').attr('placeholder', '请录入密保答案！');
                        $("#layerid_1645598368").find('input').bind('keydown', function(event) {
                            if (event.keyCode == 13) {
                                security_answer_verify($(this).val());
                            }
                        });
                    },
                    btn2: function(index, layero){
                        window.location.reload();
                        return false;
                    },
                    cancel: function(index, layero){ 
                        history.back();
                        return false; 
                    }
                }, function(value, index) {
                    security_answer_verify(value);
                });
            }

            function security_answer_verify(answer)
            {
                $.ajax({
                    type : 'post',
                    url : "{$url}",
                    data : {answer:answer},
                    dataType : 'json',
                    success : function(res){
                        if (res.code == 1) {
                            layer.closeAll();
                            layer.msg(res.msg, {time: 1000}, function(){
                                window.location.reload();
                            });
                        }else{
                            $('#layerid_1645598368').find('input[type=text]').focus();
                            layer.msg(res.msg, {time: 1000});
                        }
                    },
                    error: function(e) {
                        showErrorAlert(e.responseText);
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

    /**
     * 每个插件主入口index页面进行更新包检测
     */
    private function weappUpgrade(&$params)
    {
        $code = input('param.sm/s');
        $sc = input('param.sc/s');
        $sa = input('param.sa/s');
        $sca = $sc.'&'.$sa;
        $ca = self::$controllerName.'&'.self::$actionName;
        if ('Weapp&execute' == $ca && "{$code}&index" == $sca && 'GET' == self::$method && !isMobile()) {
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
                                shade: [0.7, '#fafafa'],
                                area: ['580px','400px'],
                                move: false,
                                title: '新版本更新',
                                btnAlign:'r',
                                closeBtn: 3,
                                btn: ['升级','不再提醒'], //按钮
                                success: function () {
                                    $(".layui-layer-content").css('text-align', 'left');
                                }
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
                        layer.alert("升级失败！", {icon: 2, closeBtn: false, title:false}, function(){
                            window.location.reload();
                        });
                    },
                    success: function(res) {
                        layer.closeAll();
                        if(1 == res.code){
                            layer.msg('已升级最新版本!', {time:1000}, function(){
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
