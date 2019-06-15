// 系统升级 js 文件


$(document).ready(function(){
    $("#a_upgrade").click(function(){
        btn_upgrade(this, 0);  
    });
});

function btn_upgrade(obj, type)
{
    var v = '';
    var filelist = $("#upgrade_filelist").html();
    var intro = $("#upgrade_intro").html();
    var notice = $("#upgrade_notice").html();
    intro += '<style type="text/css">.layui-layer-content{height:270px!important}</style>';
    // filelist = filelist.replace(/\n/g,"<br/>");
    v = notice + intro + '<br/>' + filelist;
    var version = $(obj).data('version');
    var max_version = $(obj).data('max_version');
    var title = '检测会员模板最新版本：'+version;

    var btn = ['升级','忽略'];

    //询问框
    layer.confirm(v, {
            title: title
            ,area: ['580px','400px']
            ,btn: btn //按钮

        }, function(){
            layer.closeAll();
            setTimeout(function(){
                checkdir(obj,filelist); // 请求后台
            },200);
        }, function(){  
            layer.msg('不升级无法同步最新功能！', {
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
function checkdir(obj,filelist) {
    layer_loading('检测目录');
    $.ajax({
        type : "POST",
        url  : $(obj).data('check_authority'),
        timeout : 360000, //超时时间设置，单位毫秒 设置了 1小时
        data : {filelist:filelist},
        error: function(request) {
            layer.closeAll();
            layer.alert("检测不通过，可能被服务器防火墙拦截，请添加白名单，或者联系技术协助！", {icon: 2, title:false}, function(){
                top.location.reload();
            });
        },
        success: function(res) {
            layer.closeAll();
            if (1 == res.code) {
                upgrade($(obj));
            } else {
                //提示框
                if (2 == res.data.code) {
                    var alert = layer.alert(res.msg, {icon: 2, title:false});
                } else {
                    var confirm = layer.confirm(res.msg, {
                            title: '检测目录结果'
                            ,area: ['580px','400px']
                            ,btn: ['关闭'] //按钮

                        }, function(){
                            layer.close(confirm);
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
    layer_loading('升级中');
    var version = $(obj).data('version');
    var max_version = $(obj).data('max_version');
    $.ajax({
        type : "GET",
        url  :  $(obj).data('upgrade_url'),
        timeout : 360000, //超时时间设置，单位毫秒 设置了 1小时
        data : {},
        error: function(request) {
            layer.closeAll();
            layer.alert("模板升级失败，请第一时间联系技术协助！", {icon: 2, title:false}, function(){
                top.location.reload();
            });
        },
        success: function(res) {
            if(1 == res.code){
                layer.closeAll();
                setTimeout(function(){
                    var title = '已升级最新版本！';
                    var btn = ['关闭'];
                    var full = layer.alert(title, {
                            title: false,
                            icon: 1,
                            closeBtn: 0,
                            btn: btn //按钮
                        }, function(){
                            window.location.reload();
                        }
                    );
                },200);
            }
            else{
                layer.closeAll();
                layer.alert(res.msg, {icon: 2, title:false}, function(){
                    window.location.reload();
                });
            }
        }
    });                 
}

function layer_loading(msg){
    var loading = layer.msg(
    msg+'...&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;请勿刷新页面', 
    {
        icon: 1,
        time: 3600000, //1小时后后自动关闭
        shade: [0.2] //0.1透明度的白色背景
    });
    //loading层
    var index = layer.load(3, {
        shade: [0.1,'#fff'] //0.1透明度的白色背景
    });

    return loading;
}

/*
$('#').click(funcion(){

});


 
*/