function un_qq_code()
{
    layer.confirm('您确定要解绑关联QQ吗？', {
        title: false,
        btn: ['确定', '取消'] //按钮
    }, function(){

        layer_loading('正在处理');
        $.ajax({
            url : un_qq_code_url,
            type: 'POST',
            dataType: 'JSON',
            data: {fmdo:'jiebang'},
            success: function(res){
                layer.closeAll();
                if (1 == res.code) {
                    layer.msg(res.msg, {icon: 1, time: 1000}, function(){
                        window.location.reload();
                    });
                } else {
                    layer.msg(res.msg, {icon: 2, time: 3000});
                }
            },
            error: function(e){
                layer.closeAll();
                layer.alert(e.responseText, {icon: 5, title:false,time: 3000});
            }
        });
    });
}
function bind_qq_code()
{
    var b = 720, c = 450;
    window.open(bind_qq_code_url, '账户关联', "width=" + b + ",height=" + c + ",top=" + ((window.screen.availHeight - 30 - c) / 2) + ",left=" + ((window.screen.availWidth - 10 - b) / 2) + ",menubar=0,scrollbars=1,resizable=1,status=1,titlebar=0,toolbar=0,location=1");
}