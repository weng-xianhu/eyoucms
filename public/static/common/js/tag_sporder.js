function CancelOrder(order_id) {
    layer.confirm('确定要取消订单？', {
        title:false,
        btn: ['确定', '关闭'] //按钮
    }, function () {
        // 确定
        var JsonData = eeb8a85ee533f74014310e0c0d12778;
        var url = JsonData.shop_order_cancel;
        $.ajax({
            url: url,
            data: {order_id:order_id},
            type:'post',
            dataType:'json',
            success:function(res){
                layer.closeAll();
                if ('1' == res.code) {
                    layer.msg(res.msg, {time: 2000}, function(){
                        window.location.reload();
                    });
                }else{
                    layer.msg(res.msg, {time: 2000});
                }
            }
        });
    }, function (index) {
        // 关闭
        layer.closeAll(index);
    });
}

function LogisticsInquiry(url) {
    //iframe窗
    layer.open({
        type: 2,
        title: '物流查询',
        shadeClose: false,
        maxmin: false, //开启最大化最小化按钮
        area: ['90%', '90%'],
        content: url
    });
}