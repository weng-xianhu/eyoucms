// 提交更新用户购物车数据并跳转到订单下单页
function submitBuyGoods(obj) {
    $.ajax({
        url : eyou_basefile + "?m=user&c=Shop&a=submitBuyGoods",
        data: {ids: $(obj).attr('data-ids'), _ajax: 1},
        type: 'post',
        dataType: 'json',
        success: function(res) {
            if (1 == res.code) {
                parent.layer.closeAll();
                parent.window.location.href = res.url;
            } else {
                layer.msg(res.msg, {time: 2000});
            }
        },
        error: function(e) {
            showErrorAlert(e.responseText);
        }
    });
}