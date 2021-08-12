    // 修改密码
    function ChangePwd()
    {
        var url = eyou_basefile + "?m=user&c=Users&a=change_pwd";
        //iframe窗
        layer.open({
            type: 2,
            title: '修改密码',
            shadeClose: false,
            maxmin: false, //开启最大化最小化按钮
            area: ['350px', '300px'],
            content: url
        });
    }

    // 修改会员属性信息
    function UpdateUsersData()
    {
        layer_loading('正在处理');
        $.ajax({
            url: eyou_basefile + "?m=user&c=Users&a=centre_update",
            data: $('#theForm').serialize(),
            type:'post',
            dataType:'json',
            success:function(res){
                layer.closeAll();
                if (1 == res.code) {
                    layer.msg(res.msg, {time: 1000},function(){
                        location.reload();
                    });
                } else {
                    showErrorMsg(res.msg);
                }
            },
            error : function(e) {
                layer.closeAll();
                showErrorAlert(e.responseText);
            }
        });
    };