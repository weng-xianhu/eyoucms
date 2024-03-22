var jsonData = eeb8a85ee533f74014310e0c0d12778;
var showHeight = '200px;';
var showWidth = 1 === parseInt(jsonData.is_wap) ? '380px;' : '480px;';

// 倒计时
if (jsonData.paymentExpire > 0) {
    executeCountdownTimes(jsonData.paymentExpire);
}
function executeCountdownTimes(ey_totalSeconds) {
    // 取模（余数）
    var modulo = parseInt(ey_totalSeconds) % (60 * 60 * 24);
    // 小时数
    var hours = Math.floor(modulo / (60 * 60));
    modulo = modulo % (60 * 60);
    // 分钟
    var minutes = Math.floor(modulo / 60);
    // 秒数
    var seconds = parseInt(ey_totalSeconds % 60, 10);
    // 输出到页面
    $('#' + jsonData.eyCountdownTimes).html(hours + "小时" + minutes + "分" + seconds + "秒");
    // 剩余秒数
    ey_totalSeconds--;
    // 倒计时结束则刷新页面
    if (parseInt(ey_totalSeconds) <= -1) {
        unifiedConfirmBox('订单已过期', showWidth, showHeight, function() {
            window.location.href = jsonData.shop_centre;
        }, ['确认']);
    } else {
        // 延迟一秒执行自己
        setTimeout(function () {
            executeCountdownTimes(ey_totalSeconds);
        }, 1000);
    }
}

// 取消订单
function CancelOrder(order_id) {
    if (1 === parseInt(jsonData.is_wap)) {
        showConfirmBox('确定要取消订单？', null, function() {
            $.ajax({
                url : jsonData.shop_order_cancel,
                data: {order_id: order_id},
                type: 'post',
                dataType: 'json',
                success: function(res) {
                    layer.closeAll();
                    if (1 === parseInt(res.code)) {
                        showSuccessMsg(res.msg, function() {
                            window.location.reload();
                        });
                    } else {
                        showLayerMsg(res.msg);
                    }
                }
            });
        });
    } else {
        unifiedConfirmBox('确定要取消订单？', showWidth, showHeight, function() {
            $.ajax({
                url : jsonData.shop_order_cancel,
                data: {order_id: order_id},
                type: 'post',
                dataType: 'json',
                success: function(res) {
                    layer.closeAll();
                    if (1 === parseInt(res.code)) {
                        showSuccessMsg(res.msg, function() {
                            window.location.reload();
                        });
                    } else {
                        showErrorMsg(res.msg);
                    }
                }
            });
        });
    }
}

// 提醒发货
function OrderRemind(order_id, order_code) {
    if (1 === parseInt(jsonData.is_wap)) {
        showConfirmBox('需要提醒管理员发货？', null, function() {
            $.ajax({
                url : jsonData.shop_order_remind,
                data: {order_id: order_id, order_code: order_code},
                type: 'post',
                dataType: 'json',
                success: function(res) {
                    layer.closeAll();
                    showLayerMsg(res.msg);
                }
            });
        });
    } else {
        unifiedConfirmBox('需要提醒管理员发货？', showWidth, showHeight, function() {
            $.ajax({
                url : jsonData.shop_order_remind,
                data: {order_id: order_id, order_code: order_code},
                type: 'post',
                dataType: 'json',
                success: function(res) {
                    layer.closeAll();
                    showSuccessMsg(res.msg);
                }
            });
        });
    }
}   

// 确认收货
function Confirm(order_id, order_code) {
    if (1 === parseInt(jsonData.is_wap)) {
        showConfirmBox('您确认已收到货物？', null, function() {
            $.ajax({
                url : jsonData.shop_member_confirm,
                data: {order_id: order_id, order_code: order_code},
                type: 'post',
                dataType: 'json',
                success: function(res) {
                    layer.closeAll();
                    if (1 === parseInt(res.code)) {
                        window.location.reload();
                    } else {
                        showLayerMsg(res.msg);
                    }
                }
            });
        });
    } else {
        unifiedConfirmBox('您确认已收到货物？', showWidth, showHeight, function() {
            $.ajax({
                url : jsonData.shop_member_confirm,
                data: {order_id: order_id, order_code: order_code},
                type: 'post',
                dataType: 'json',
                success: function(res) {
                    layer.closeAll();
                    if (1 === parseInt(res.code)) {
                        window.location.reload();
                    } else {
                        showErrorMsg(res.msg);
                    }
                }
            });
        });
    }
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

//生成二维码
function outputQRCod(text) {
    new QRCode("qrcode", {
        text: text,
        width: 100,
        height: 100,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
}