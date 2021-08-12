var PayPolling;
var JsonData = eyou_data_json_1590627847;
var unified_id = JsonData.unified_id;
var unified_number = JsonData.unified_number;
var transaction_type = JsonData.transaction_type;

// 商品购买、余额充值调用
function SelectPayMethod(pay_id, pay_mark) {
    if (!pay_id || !pay_mark || !unified_id || !unified_number || !transaction_type) {
        layer.msg('订单异常，刷新重试', {time: 1500}, function(){
            window.location.reload();
        });
    }
    layer_loading('订单处理中');
    $.ajax({
        url: JsonData.SelectPayMethod,
        data: {
            pay_id: pay_id,
            pay_mark: pay_mark,
            unified_id: unified_id,
            unified_number: unified_number,
            transaction_type: transaction_type
        },
        type:'post',
        dataType:'json',
        success:function(res) {
            layer.closeAll();
            if (1 == res.code) {
                $('#PayID').val(pay_id);
                $('#PayMark').val(pay_mark);
                if (res.data.appId) {
                    callpay(res.data);
                } else if (res.data.url_qrcode && 0 == JsonData.IsMobile) {
                    AlertPayImg(res.data);
                } else if (res.data.url && 1 == JsonData.IsMobile) {
                    window.open(res.data.url);
                    PayPolling = window.setInterval(OrderPayPolling, 1000);
                } else {
                    layer_loading('订单支付中');
                    if (1 == JsonData.IsMobile) {
                        window.location.href = res.url;
                    } else {
                        window.open(res.url);
                    }
                    PayPolling = window.setInterval(OrderPayPolling, 2000);
                }
            } else {
                layer.alert(res.msg, {icon:0, title: false, closeBtn: 0});
            }
        }
    });
}

// 装载显示扫码支付的二维码
function AlertPayImg(data) {
    var html = "<img src='"+data.url_qrcode+"' style='width: 250px; height: 250px;'><br/><span style='color: red; display: inline-block; width: 100%; text-align: center;'>正在支付中...请勿刷新</span>";
    layer.alert(html, {
        title: false,
        btn: [],
        success: function() {
            PayPolling = window.setInterval(OrderPayPolling, 2000);
        },
        cancel: function() {
            window.clearInterval(PayPolling);
        }
    });
}

// 订单轮询
function OrderPayPolling() {
    var pay_id = $('#PayID').val();
    var pay_mark = $('#PayMark').val();
    var pay_type = $('#PayType').val();
    if (!pay_id || !pay_mark || !unified_id || !unified_number || !transaction_type) {
        layer.msg('订单异常，刷新重试', {time: 1500}, function(){
            window.location.reload();
        });
    }
    $.ajax({
        url: JsonData.OrderPayPolling,
        data: {
            pay_id: pay_id,
            pay_mark: pay_mark,
            pay_type: pay_type,
            unified_id: unified_id,
            unified_number: unified_number,
            transaction_type: transaction_type
        },
        type:'post',
        dataType:'json',
        success:function(res){
            if (1 == res.code) {
                if (res.data) {
                    layer_loading('订单处理中');
                    window.clearInterval(PayPolling);
                    if (2 == transaction_type) {
                        if (!res.data.mobile && !res.data.email) {
                            layer.closeAll();
                            layer.msg(res.msg, {time: 1500}, function() {
                                window.location.href = res.url;
                            });
                        }
                        if (res.data.mobile) SendMobile(res.data.mobile);
                        if (res.data.email) SendEmail(res.data.email);
                    }
                    layer.closeAll();
                    layer.msg(res.msg, {time: 1500}, function() {
                        window.location.href = res.url;
                    });
                }
            } else if (0 == res.code) {
                layer.alert(res.msg, {icon:0, title: false, closeBtn: 0});
            }
        }
    });
}

// 发送短信
function SendMobile(result) {
    if (result) {
        $.ajax({
            url: result.url,
            data: result.data,
            type:'post',
            dataType:'json'
        });
    }
}

// 发送邮件
function SendEmail(result) {
    if (result) {
        $.ajax({
            url: result.url,
            data: result.data,
            type:'post',
            dataType:'json'
        });
    }
}

// 微信内部支付时，先进行数据判断
function callpay(data) {
    if (typeof WeixinJSBridge == "undefined") {
        if ( document.addEventListener ) {
            document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
        } else if (document.attachEvent) {
            document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
            document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
        }
    } else {
        jsApiCall(data);
    }
}

// 调用微信JS api 支付
function jsApiCall(data) {
    WeixinJSBridge.invoke(
        'getBrandWCPayRequest', data,
        function(res) {
            if (res.err_msg == "get_brand_wcpay_request:ok") {  
                layer.msg('微信支付完成！', {time: 1000}, function() {
                    OrderPayPolling();
                });
            } else if (res.err_msg == "get_brand_wcpay_request:cancel") {
                layer.alert('用户取消支付！', {icon:0});
            } else {
                layer.alert('支付失败', {icon:0});
            }  
        }
    );
}

function pay_deal_with() {
    $.ajax({
        url: JsonData.PayDealWith,
        data: {unified_number: unified_number, transaction_type: transaction_type},
        type:'post',
        dataType:'json',
        success:function(res){
            if (1 == res.data.status) {
                if (!res.data.mobile && !res.data.email) {
                    layer.msg(res.msg, {time: 1000}, function() {
                        window.location.href = res.url;
                    });
                }
                if (res.data.mobile) SendMobile(res.data.mobile);
                if (res.data.email) SendEmail(res.data.email);
                layer.msg(res.msg, {time: 1000}, function() {
                    window.location.href = res.url;
                });
            }
        }
    });
}

/*-------------会员升级调用---------开始----------*/
// 会员升级调用
function UsersUpgradePay(obj) {
    // 禁用支付按钮
    $(obj).prop("disabled", true).css("pointer-events", "none");
    layer_loading('正在处理');
    $.ajax({
        url: JsonData.UsersUpgradePay,
        data: $('#theForm').serialize(),
        type:'POST',
        dataType:'json',
        success:function(res) {
            layer.closeAll();
            $(obj).prop("disabled", false).css("pointer-events", "");
            if (1 == res.code) {
                if (0 == res.msg.ReturnCode) {
                    // 余额支付逻辑
                    if (0 == res.msg.ReturnPay) {
                        // 余额不足支付
                        IsRecharge(res.msg);
                    } else {
                        // 支付完成
                        layer.msg(res.msg.ReturnMsg, {time: 1500}, function(){
                            window.location.href = res.msg.ReturnUrl;
                        });
                    }
                } else if (1 == res.msg.ReturnCode) {
                    // 微信支付逻辑
                    if (0 == res.msg.ReturnPay) {
                        // // 加载订单号到隐藏域
                        $('#PayID').val(res.data.pay_id);
                        $('#PayMark').val(res.data.pay_mark);
                        $('#UnifiedNumber').val(res.msg.ReturnOrder);
                        unified_id = res.data.unified_id;
                        unified_number = res.data.unified_number;
                        transaction_type = res.data.transaction_type;
                        if (res.data.PayData.appId) {
                            // 手机端微信内支付
                            callpay(res.data.PayData);
                        } else if (res.msg.url_qrcode) {
                            // PC端浏览器扫码支付
                            AlertPayImg(res.msg);
                        } else {
                            // 手机端浏览器支付
                            layer_loading('订单支付中');
                            if (1 == JsonData.IsMobile) {
                                window.location.href = res.url;
                            } else {
                                window.open(res.url);
                            }
                            PayPolling = window.setInterval(OrderPayPolling, 2000);
                        }
                    } else {
                        // 支付完成
                        layer.msg(res.msg.ReturnMsg, {time: 1500}, function(){
                            window.location.href = res.msg.ReturnUrl;
                        });
                    }
                } else if (2 == res.msg.ReturnCode) {
                    // 支付宝支付逻辑
                    if (0 == res.msg.ReturnPay) {
                        $('#PayID').val(res.msg.pay_id);
                        $('#PayMark').val(res.msg.pay_mark);
                        $('#UnifiedNumber').val(res.msg.ReturnOrder);
                        unified_id = res.msg.ReturnOrderID;
                        unified_number = res.msg.ReturnOrder;
                        transaction_type = 3;
                        layer_loading('订单支付中');
                        if (1 == JsonData.IsMobile) {
                            window.location.href = res.msg.ReturnUrl;
                        } else {
                            window.open(res.msg.ReturnUrl);
                        }
                        PayPolling = window.setInterval(OrderPayPolling, 2000);
                    }
                } else {
                    $('#PayID').val(res.data.pay_id);
                    $('#PayMark').val(res.data.pay_mark);
                    $('#PayType').val(res.data.pay_type);
                    $('#UnifiedNumber').val(res.data.unified_number);
                    unified_id = res.data.unified_id;
                    unified_number = res.data.unified_number;
                    transaction_type = 3;
                    if (res.data.url_qrcode && 0 == JsonData.IsMobile) {
                        AlertPayImg(res.data);
                    } else if (res.data.url && 1 == JsonData.IsMobile) {
                        window.open(res.data.url);
                        PayPolling = window.setInterval(OrderPayPolling, 1000);
                    } else {
                        layer_loading('订单支付中');
                        if (1 == JsonData.IsMobile) {
                            window.location.href = res.url;
                        } else {
                            window.open(res.url);
                        }
                        PayPolling = window.setInterval(OrderPayPolling, 2000);
                    }
                }
            } else {
                layer.alert(res.msg, {icon:0, title: false, closeBtn: 0});
            }
        }
    });
}

// 是否要去充值
function IsRecharge(data) {
    layer.confirm(data.ReturnMsg, {
        title: false,
        closeBtn: 0,
        btn: ['去充值','其他方式支付']
    }, function() {
        // 去充值
        window.location.href = data.ReturnUrl;
    }, function(index) {
        // 选择其他方式支付时，恢复禁用的余额支付按钮
        $('#Pay').prop("disabled", false).css("pointer-events", "");
        layer.closeAll(index);
    });
}
/*-------------会员升级调用---------结束----------*/