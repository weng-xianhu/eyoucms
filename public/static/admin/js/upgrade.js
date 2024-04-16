// 系统升级 js 文件


$(document).ready(function(){
    $("#a_upgrade").click(function(){
        if ($(this).attr('data-php_servicemeal') > 0) {
            btn_upgrade(this, 0);
        } else {
            upgrade_buy_tips(this);
        }
    });
});

function btn_upgrade(obj, type)
{
    var v = '';
    var filelist = $("#upgrade_filelist").html();
    if (undefined == filelist || !filelist) {
        parent.layer.closeAll();
        var alert1 = layer.alert("请清除后台缓存以及Ctrl+F5强制刷新页面，再尝试升级！", {icon: 7, title:false}, function(){
            layer.close(alert1);
            var url = eyou_basefile + "?m="+module_name+"&c=System&a=clear_cache";
            var iframe = $(obj).data('iframe');
            if ('parent' == iframe) {
                workspace.window.location.href = url;
            } else {
                window.location.href = url;
            }
        });
        return false;
    }
    
    var version = $(obj).data('version');
    var max_version = $(obj).data('max_version');
    var curent_version = $(obj).data('curent_version');
    var intro = $("#upgrade_intro").html();
    var notice = $("#upgrade_notice").html();
    intro += '<style type="text/css">.layui-layer-content{height:270px!important;text-align:left!important;}</style>';
    // 截图前50个文件记录
    var filelist_arr = filelist.split('<br>');
    if (filelist_arr.length > 50) {
        filelist_arr = filelist_arr.slice(0,50);
        filelist_arr.push("……");
        filelist_arr.push("<a href='https://www.eyoucms.com/plus/upgrade.php?version="+curent_version+"-"+version+"' target='_blank'>此次更新涉及的全部文件，点击这里查看！</a>");
        filelist = filelist_arr.join('<br>');
    }
    v = notice + intro + '<br/>' + filelist;
    var title = '检测系统最新版本：'+version;
    var btn = [];
    if (0 == type) {
        btn = ['升级','忽略'];
    } else if (1 == type) {
        btn = ['升级','忽略','不再提醒'];
    }
    
    if (1 == VarSecurityPatch) {
        btn = ['升级','忽略'];
        title = '检测系统安全补丁最新版本：'+version;
    }

    /*显示顶部导航更新提示*/
    $("#upgrade_filelist", window.parent.document).html($("#upgrade_filelist").html());    
    $("#upgrade_intro", window.parent.document).html($("#upgrade_intro").html());
    $("#upgrade_notice", window.parent.document).html($("#upgrade_notice").html());
    $('#a_upgrade', window.parent.document).attr('data-version',version)
        .attr('data-max_version',max_version)
        .show();
    /*--end*/

    //询问框
    parent.layer.confirm(v, {
            title: title
            ,shade: layer_shade
            ,area: ['580px','400px']
            ,btn: btn //按钮
            ,success: function () {
                $(".layui-layer-content").css('text-align', 'left');
            }
            ,btn3: function(index){
                var url = $(obj).data('tips_url');
                $.getJSON(url, {show_popup_upgrade:-1,_ajax:1}, function(){});
                parent.layer.msg('【核心设置】里可以开启该提醒', {
                    btnAlign: 'c',
                    time: 20000, //20s后自动关闭
                    btn: ['知道了']
                });
                return false;
            }

        }, function(){
            parent.layer.closeAll();
            setTimeout(function(){
                checkdir(obj); // 请求后台
            },200);
        }, function(){  
            parent.layer.msg('不升级可能有安全隐患', {
                btnAlign: 'c',
                time: 20000, //20s后自动关闭
                btn: ['明白了']
            });
            return false;

        }
    );   
}

/** 
 * 检测升级文件的目录权限
 */
function checkdir(obj) {
    layer_loading2('检测系统');
    $.ajax({
        type : "POST",
        url  : $(obj).data('check_authority'),
        timeout : 360000, //超时时间设置，单位毫秒 设置了 1小时
        data : {filelist:0,_ajax:1},
        error: function(e) {
            var msg = e.responseText;
            if (msg.indexOf('错误代码') == -1) {
                msg = "检测不通过，可能被服务器防火墙拦截，请添加白名单！";
            }
            parent.layer.closeAll();
            parent.layer.alert(msg, {icon: 2, title:false}, function(){
                top.location.reload();
            });
        },
        success: function(res) {
            parent.layer.closeAll();
            if (1 == res.code) {
                upgrade($(obj));
            } else {
                //提示框
                if (2 == res.data.code) { 
                    var alert = parent.layer.alert(res.msg, {icon: 2, title:false, btn: ['立即查看']}, function(){
                        window.parent.open('https://www.eyoucms.com/plus/view.php?aid=9105');
                    });
                } else {
                    var confirm = parent.layer.confirm(res.msg, {
                            title: '检测系统结果'
                            ,area: ['580px','400px']
                            ,btn: ['关闭'] //按钮

                        }, function(){
                            parent.layer.close(confirm);
                            return false;
                        }
                    );  
                }
            }
        }
    }); 
}

/** 
 * 升级系统
 */
function upgrade(obj){
    layer_loading2('升级<font id="upgrade_speed">中</font>');
    var version = $(obj).data('version');
    var max_version = $(obj).data('max_version');
    var timer = '';
    var speed = 0.01;
    $.ajax({
        type : "GET",
        url  :  $(obj).data('upgrade_url'),
        timeout : 360000, //超时时间设置，单位毫秒 设置了 1小时
        data : {_ajax:1},
        beforeSend:function(){
            timer = setInterval(function(){
                random = Math.floor(Math.random()*89+10);
                random = random.toString();
                random = '1.' + random;
                speed = speed + parseFloat(random);
                speed = Math.floor(speed * 100) / 100;
                if (speed >= 98) {
                    speed = 98;
                }
                $('#upgrade_speed', window.parent.document).html(speed+'%');
            }, 500);
        },
        error: function(request) {
            parent.layer.closeAll();
            parent.layer.alert("空间超时请稍后再试，或手工升级！", {icon: 2, title:false}, function(){
                top.location.reload();
            });
        },
        success: function(res) {
            $('#upgrade_speed', window.parent.document).html('100%');
            clearInterval(timer);
            if(1 == res.code){
                // setTimeout(function(){
                    setTimeout(function(){
                        var finish = false; // 是否升到最新版
                        if (2 == res.data.code) {
                            var title = res.msg;
                            var btn = ['关闭'];
                        }else if (version < max_version) { // 当前升级之后的版本还不是官方最新版本，将继续连续更新
                            var title = '已升级版本：'+version+'，官方最新版本：'+max_version+'。';
                            var btn = ['开始检测'];
                        } else { // 升级版本是官方最新版本，将引导到备份新数据
                            finish = true;
                            var title = '已升级最新版本！';
                            var btn = ['关闭'];
                            $('#a_upgrade', window.parent.document).hide(); // 隐藏顶部的更新提示
                        }

                        if (true == finish) {
                            parent.layer.closeAll();
                            var full = parent.layer.alert(title, {
                                    title: false,
                                    icon: 1,
                                    closeBtn: 0,
                                    btn: btn //按钮
                                }, function(){
                                    parent.layer.close(full);
                                    top.location.href = eyou_basefile;
                                }
                            );
                            // export_data();
                        } else {
                            var full = parent.layer.alert(title, {
                                    title: false,
                                    icon: 1,
                                    closeBtn: 0,
                                    btn: btn //按钮
                                }, function(){
                                    if (version < max_version) { // 当前升级之后的版本还不是官方最新版本，将继续连续更新
                                        top.location.reload();
                                    } else { // 升级版本是官方最新版本，将引导到备份新数据
                                        parent.layer.close(full);
                                        var url = eyou_basefile + "?m="+module_name+"&c=Tools&a=index";
                                        var iframe = $(obj).data('iframe');
                                        if ('parent' == iframe) {
                                            top.location.href = eyou_basefile;
                                            // workspace.window.location.href = url;
                                        } else {
                                            top.location.href = eyou_basefile;
                                            // window.location.href = url;
                                        }
                                    }
                                }
                            );
                        }
                    },500);
                // },40000); // 睡眠1分钟，让复制文件执行完
            }
            else if (-2 == res.data.code) {
                parent.layer.closeAll();
                parent.layer.alert(res.msg, {icon: 2, title:false, btn: ['立即查看']}, function(){
                    window.parent.open('https://www.eyoucms.com/plus/view.php?aid=9105');
                });
            }
            else if (-3 == res.data.code) {
                parent.layer.closeAll();
                var msg = '<style type="text/css">.layui-layer-content{text-align:left!important;}</style>' + res.msg;
                parent.layer.alert(msg, {
                    shade: layer_shade,
                    area: ['480px', '230px'],
                    move: false,
                    title: '温馨提示',
                    btnAlign:'r',
                    closeBtn: 3,
                    btn: ['购买授权', '确定'],
                    success: function(){
                        $(".layui-layer-btn", window.parent.document).find('a.layui-layer-btn1').removeClass('layui-layer-btn1').addClass('layui-layer-btn2');
                        $(".layui-layer-btn", window.parent.document).find('a.layui-layer-btn0').removeClass('layui-layer-btn0').addClass('layui-layer-btn1');
                        $(".layui-layer-btn", window.parent.document).find('a.layui-layer-btn2').removeClass('layui-layer-btn2').addClass('layui-layer-btn0');
                    }
                }, function(){
                    window.open('https://www.eyoucms.com/buy');
                });
            }
            else {
                parent.layer.closeAll();
                parent.layer.alert(res.msg, {icon: 2, title:false}, function(){
                    top.location.reload();
                });
            }
        }
    });                 
}

function upgrade_buy_tips(obj)
{
    var msg = '<style type="text/css">.layui-layer-content{text-align:left!important;}</style>由于升级服务器并发压力过大，暂停非授权站点在线升级，有需要可以购买商业授权，如有不便请多包涵！';
    parent.layer.alert(msg, {
        shade: layer_shade,
        area: ['480px', '230px'],
        move: false,
        title: '温馨提示',
        btnAlign:'r',
        closeBtn: 3,
        btn: ['购买授权', '确定'],
        success: function(){
            $(".layui-layer-btn", window.parent.document).find('a.layui-layer-btn1').removeClass('layui-layer-btn1').addClass('layui-layer-btn2');
            $(".layui-layer-btn", window.parent.document).find('a.layui-layer-btn0').removeClass('layui-layer-btn0').addClass('layui-layer-btn1');
            $(".layui-layer-btn", window.parent.document).find('a.layui-layer-btn2').removeClass('layui-layer-btn2').addClass('layui-layer-btn0');
        }
    }, function(){
        window.open('https://www.eyoucms.com/buy');
    });
}

function layer_loading2(msg){
    var loading = parent.layer.msg(
    msg+'...&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;请勿刷新页面', 
    {
        icon: 1,
        time: 3600000, //1小时后后自动关闭
        shade: [0.2] //0.1透明度的白色背景
    });
    //loading层
    var index = parent.layer.load(3, {
        shade: [0.1,'#fff'] //0.1透明度的白色背景
    });

    return loading;
}

function export_data(){
    parent.layer.msg('已完成升级，正在备份数据，请勿刷新页面！', 
    {
        icon: 1,
        time: 3600000, //1小时后后自动关闭
        shade: [0.2] //0.1透明度的白色背景
    });
    setTimeout(function(){
        var url = eyou_basefile + "?m="+module_name+"&c=Tools&a=export&_ajax=1";
        $.ajax({
            url: url,
            data: {tables:'all'},
            type:'post',
            dataType:'json',
            success:function(res){
                parent.layer.closeAll();
                if(res.code){
                    tables = res.tables;
                    var loading = parent.layer.msg('正在备份表(<font id="upgrade_backup_table">'+res.tab.table+'</font>)……<font id="upgrade_backup_speed">0.01</font>%', 
                    {
                        icon: 1,
                        time: 3600000, //1小时后后自动关闭
                        shade: [0.2] //0.1透明度的白色背景
                    });
                    backup_data(res.tab);
                } else {
                    var _parent = parent;
                    _parent.layer.alert('已升级最新版本，自动备份数据库失败，请立即前往备份！', {icon: 1, title:false}, function(){
                        _parent.layer.closeAll();
                        var url = eyou_basefile + "?m="+module_name+"&c=Tools&a=index";
                        _parent.workspace.window.location.href = url;
                    });
                }
            },
            error : function() {
                var _parent = parent;
                _parent.layer.alert('已升级最新版本，自动备份数据库失败，请立即前往备份！', {icon: 1, title:false}, function(){
                    _parent.layer.closeAll();
                    var url = eyou_basefile + "?m="+module_name+"&c=Tools&a=index";
                    _parent.workspace.window.location.href = url;
                });
            }
        });
    }, 1500);
}

function backup_data(tab){
    var url = eyou_basefile + "?m="+module_name+"&c=Tools&a=export&_ajax=1";
    $.ajax({
        url: url,
        data: tab,
        type:'post',
        dataType:'json',
        success:function(res){
            if(res.code){
                if (tab.table) {
                    $('#upgrade_backup_table', window.parent.document).html(tab.table);
                    $('#upgrade_backup_speed', window.parent.document).html(tab.speed);
                }
                if(!$.isPlainObject(res.tab)){
                    var loading = parent.layer.msg('备份完成……100%，请勿刷新页面！', 
                    {
                        icon: 1,
                        time: 2000, //1小时后后自动关闭
                        shade: [0.2] //0.1透明度的白色背景
                    });
                    setTimeout(function(){
                        parent.layer.closeAll();
                        var full = parent.layer.alert('已升级最新版本！', {
                                title: false,
                                icon: 1,
                                closeBtn: 0,
                                btn: ['关闭'] //按钮
                            }, function(){
                                parent.layer.close(full);
                                top.location.href = eyou_basefile;
                            }
                        );
                    }, 1000);
                    return;
                }
                setTimeout(function () {
                    backup_data(res.tab);
                }, 350);
            } else {
                var full = parent.layer.alert('已升级最新版本！', {
                        title: false,
                        icon: 1,
                        closeBtn: 0,
                        btn: ['关闭'] //按钮
                    }, function(){
                        parent.layer.close(full);
                        top.location.href = eyou_basefile;
                    }
                );
            }
        },
        error : function() {
            var _parent = parent;
            _parent.layer.alert('已升级最新版本，自动备份数据库失败，请立即前往备份！', {icon: 1, title:false}, function(){
                _parent.layer.closeAll();
                var url = eyou_basefile + "?m="+module_name+"&c=Tools&a=index";
                _parent.workspace.window.location.href = url;
            });
        }
    });
}