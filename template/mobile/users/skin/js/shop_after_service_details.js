$(function () {
    if ($('#add_time').length) $('#add_time').layDate(); 
});
function submitReturnGoods(obj) {
    var name = $("input[name='delivery[name]']");
    if (!name.val()) {
        showLayerMsg('请填写快递公司');
        name.focus();
        return false;
    }
    var code = $("input[name='delivery[code]']");
    if (!code.val()) {
        showLayerMsg('请填写快递单号');
        code.focus();
        return false;
    }
    var time = $("input[name='delivery[time]']");
    if (!time.val()) {
        $('#add_time').click();
        return false;
    }
    $.ajax({
        url : $(obj).data('url'),
        data: $('#postForm').serialize(),
        type: 'post',
        dataType: 'json',
        success: function(res) {
            layer.closeAll();
            if (1 == res.code) {
                showLayerMsg(res.msg, 2, function() {
                    window.location.reload();
                });
            } else {
                showLayerAlert(res.msg);
            }
        },
        error: function() {
            layer.closeAll();
            showLayerAlert(e.responseText);
        }
    });
}

// 取消服务单
function cancelService(obj) {
    showConfirmBox('确定要取消服务单？', null, function() {
        $.ajax({
            url : $(obj).data('url'),
            data: {service_id: $(obj).data('id')},
            type: 'post',
            dataType: 'json',
            success: function(res) {
                layer.closeAll();
                if (1 == res.code) {
                    showLayerMsg(res.msg, 2, function() {
                        window.location.reload();
                    });
                } else {
                    showLayerAlert(res.msg, {time: 2000});
                }
            },
            error: function() {
                layer.closeAll();
                showLayerAlert(e.responseText);
            }
        });
    });
}