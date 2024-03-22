var PayPolling;
var JsonData = eyou_data_json_v627847;
var unified_id = JsonData.unified_id;
var unified_number = JsonData.unified_number;
var transaction_type = JsonData.transaction_type;

// 自动加载默认paypal支付的form
$(function() {
    $('body').append(JsonData.submitForm);
});

// 商品购买、余额充值调用
function SelectPayMethod(pay_id, pay_mark) {
    var is_wap = JsonData.is_wap;
    if (is_wap == 1) {
        SelectPayMethod_Mb(pay_id, pay_mark);
        return false;
    }

    $('.payment-list li').removeClass('selected');
    $('#paymethod_'+pay_mark).addClass('selected');
    if (!pay_id || !pay_mark || !unified_id || !unified_number || !transaction_type) {
        layer.msg('订单异常001，刷新重试', {time: 1500}, function(){
            window.location.reload();
        });
    }
    var a_alipay_url = "";
    layer_loading('订单处理中');
    $.ajax({
        url: JsonData.SelectPayMethod,
        async: false,
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
                } else if (res.data.is_applets && 1 == res.data.is_applets) {
                    WeChatInternal(res.data);
                } else if (res.data.url_qrcode && 0 == JsonData.IsMobile) {
                    AlertPayImg(res.data);
                } else if (res.data.url && 1 == JsonData.IsMobile) {
                    a_alipay_url = res.data.url;
                    // window.open(res.data.url);
                    PayPolling = window.setInterval(OrderPayPolling, 1000);
                } else if (1 == res.data.is_paypal) {
                    if (res.data.item_name && res.data.amount && res.data.invoice) {
                        $('#eyou_paypalForm, #eyou_itemName').val(res.data.item_name);
                        $('#eyou_paypalForm, #eyou_amount').val(res.data.amount);
                        $('#eyou_paypalForm, #eyou_invoice').val(res.data.invoice);
                        if (res.data.eyou_cancel_return) $('#eyou_paypalForm, #eyou_cancel_return').val(res.data.eyou_cancel_return);
                        $('#eyou_paypalForm, #eyou_submitForm').click();
                    }
                    PayPolling = window.setInterval(OrderPayPolling, 1000);
                } else {
                    if (1 == JsonData.IsMobile) {
                        layer_loading('订单支付中');
                        window.location.href = res.url;
                    } else {
                        a_alipay_url = res.url;
                        // window.open(res.url);
                        layer.confirm('支付页面已在新窗口打开，请支付！', {
                            btn: ['支付成功','支付失败'],
                            title:'正在支付...',
                        }, function(index){
                            layer.close(index);
                        });
                    }
                    PayPolling = window.setInterval(OrderPayPolling, 2000);
                }
            } else {
                layer.alert(res.msg, {icon:0, title: false, closeBtn: 0});
            }
        }
    });
    if (a_alipay_url != ""){
        newWinSppay3(a_alipay_url);
    }
    return false;
}

function SelectPayMethod_Mb(pay_id, pay_mark) {
    $('.payment-list li').removeClass('selected');
    $('#paymethod_'+pay_mark).addClass('selected');
    if (!pay_id || !pay_mark || !unified_id || !unified_number || !transaction_type) {
        layer.msg('订单异常，刷新重试', {time: 1500}, function(){
            window.location.reload();
        });
    }
    layer_loading_mini();
    var a_alipay_url = "";
    $.ajax({
        async: false,
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
            if (1 == res.code) {
                $('#PayID').val(pay_id);
                $('#PayMark').val(pay_mark);
                if (res.data.appId) {
                    layer.closeAll();
                    callpay(res.data);
                } else if (res.data.url_qrcode && 0 == JsonData.IsMobile) {
                    layer.closeAll();
                    AlertPayImg(res.data);
                } else if (res.data.url && 1 == JsonData.IsMobile) {
                    layer.closeAll();
                    a_alipay_url = res.data.url;
                    // window.open(res.data.url);
                    PayPolling = window.setInterval(OrderPayPolling, 1000);
                } else {
                    if (1 == JsonData.IsMobile) {
                        window.location.href = res.url;
                        layer.closeAll();
                    } else {
                        layer.closeAll();
                        a_alipay_url = res.url;
                        // window.open(res.url);
                        layer.confirm('支付页面已在新窗口打开，请支付！', {
                            btn: ['支付成功','支付失败'],
                            title:'正在支付...',
                        }, function(index){
                            layer.close(index);
                        });
                    }
                    PayPolling = window.setInterval(OrderPayPolling, 2000);
                }
            } else {
                layer.closeAll();
                layer.alert(res.msg, {icon:0, title: false, closeBtn: 0});
            }
        }
    });
    if (a_alipay_url != ""){
        newWinSppay3(a_alipay_url);
    }
    return false;
}

// 商品购买、余额充值调用
function SelectPayMethod_2(pay_id, pay_mark, unifiedId, unifiedNumber, transactionType) {

    var is_wap = JsonData.is_wap;
    if (is_wap == 1) {
        SelectPayMethod_2_Mb(pay_id, pay_mark, unifiedId, unifiedNumber, transactionType);
        return false;
    }

    if (!pay_id || !pay_mark || !unifiedId || !unifiedNumber || !transactionType) {
        layer.msg('订单异常，刷新重试', {time: 1500}, function(){
            window.location.reload();
        });
    }
    unified_id = JsonData.unified_id = unifiedId;
    unified_number = JsonData.unified_number = unifiedNumber;
    transaction_type = JsonData.transaction_type = transactionType;
    var referurl = $('#referurl').val();
    if (undefined == referurl) {
        referurl = '';
    }
    var a_alipay_url = "";
    $.ajax({
        async: false,
        url: JsonData.SelectPayMethod,
        data: {
            pay_id: pay_id,
            pay_mark: pay_mark,
            unified_id: unified_id,
            unified_number: unified_number,
            transaction_type: transaction_type,
            referurl: referurl,
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
                    a_alipay_url = res.data.url;
                    // window.open(res.data.url);
                    PayPolling = window.setInterval(OrderPayPolling, 1000);
                } else if (1 == res.data.is_paypal) {
                    if (res.data.item_name && res.data.amount && res.data.invoice) {
                        $('#eyou_paypalForm, #eyou_itemName').val(res.data.item_name);
                        $('#eyou_paypalForm, #eyou_amount').val(res.data.amount);
                        $('#eyou_paypalForm, #eyou_invoice').val(res.data.invoice);
                        if (res.data.eyou_cancel_return) $('#eyou_paypalForm, #eyou_cancel_return').val(res.data.eyou_cancel_return);
                        $('#eyou_paypalForm, #eyou_submitForm').click();
                    }
                    PayPolling = window.setInterval(OrderPayPolling, 1000);
                } else {
                    // layer.open({
                    //     type: 1
                    //     , title: '正在支付...'
                    //     , btn: ['支付成功', '支付失败']
                    //     , yes: function (index, layero) {
                    //         layer.closeAll();
                    //         remoteInstall(code, min_version);
                    //     }
                    //     ,btn2: function(index, layero){
                    //         layer.close();
                    //         location.reload()//重新加载页面
                    //     }
                    //     , cancel: function () {
                    //         //右上角关闭回调
                    //         location.reload()//重新加载页面
                    //     }
                    //     ,shadeClose: true //点击遮罩关闭
                    //     ,content: "支付页面已在新窗口打开，请支付！"
                    // });
                    if (1 == JsonData.IsMobile) {
                        layer_loading('订单支付中');
                        window.location.href = res.url;
                    } else {
                        a_alipay_url = res.url;
                        // window.open(res.url);
                        layer.confirm('支付页面已在新窗口打开，请支付！', {
                            btn: ['支付成功','支付失败'],
                            title:'正在支付...',
                        }, function(index){
                            layer.close(index);
                            OrderPayPolling();
                        }, function(index){
                            layer.close(index);
                            var submit_order_type = $('#submit_order_type').val();
                            if (undefined != submit_order_type && '0' === submit_order_type) {
                                if (b1decefec6b39feb3be1064e27be2a9.shop_centre_url) {
                                    window.location.href = b1decefec6b39feb3be1064e27be2a9.shop_centre_url;
                                } else {
                                    window.location.reload();
                                }
                            }
                        });
                    }
                    PayPolling = window.setInterval(OrderPayPolling, 2000);
                }
            } else {
                layer.alert(res.msg, {icon:0, title: false, closeBtn: 0});
            }
            
            $.ajax({
                url: JsonData.get_token_url,
                data: {_ajax:1},
                type:'get',
                dataType:'html',
                success:function(res) {
                    $('#__token__dfbfa92d4c447bf2c942c7d99a223b49').val(res);
                }
            });
        }
    });
    if (a_alipay_url != ""){
        newWinSppay3(a_alipay_url);
    }
    return false;
}

// 商品购买、余额充值调用
function SelectPayMethod_2_Mb(pay_id, pay_mark, unifiedId, unifiedNumber, transactionType) {
    if (!pay_id || !pay_mark || !unifiedId || !unifiedNumber || !transactionType) {
        layer.msg('订单异常，刷新重试', {time: 1500}, function(){
            window.location.reload();
        });
    }
    unified_id = JsonData.unified_id = unifiedId;
    unified_number = JsonData.unified_number = unifiedNumber;
    transaction_type = JsonData.transaction_type = transactionType;
    var referurl = $('#referurl').val();
    if (undefined == referurl) {
        referurl = '';
    }
    var a_alipay_url = "";
    $.ajax({
        url: JsonData.SelectPayMethod,
        async: false,
        data: {
            pay_id: pay_id,
            pay_mark: pay_mark,
            unified_id: unified_id,
            unified_number: unified_number,
            transaction_type: transaction_type,
            referurl: referurl,
        },
        type:'post',
        dataType:'json',
        success:function(res) {
            if (1 == res.code) {
                $('#PayID').val(pay_id);
                $('#PayMark').val(pay_mark);
                if (res.data.appId) {
                    layer.closeAll();
                    callpay(res.data);
                } else if (res.data.url_qrcode && 0 == JsonData.IsMobile) {
                    layer.closeAll();
                    AlertPayImg(res.data);
                } else if (res.data.url && 1 == JsonData.IsMobile) {
                    layer.closeAll();
                    a_alipay_url= res.data.url;
                    // window.open(res.data.url);
                    PayPolling = window.setInterval(OrderPayPolling, 1000);
                } else {
                    // layer.open({
                    //     type: 1
                    //     , title: '正在支付...'
                    //     , btn: ['支付成功', '支付失败']
                    //     , yes: function (index, layero) {
                    //         layer.closeAll();
                    //         remoteInstall(code, min_version);
                    //     }
                    //     ,btn2: function(index, layero){
                    //         layer.close();
                    //         location.reload()//重新加载页面
                    //     }
                    //     , cancel: function () {
                    //         //右上角关闭回调
                    //         location.reload()//重新加载页面
                    //     }
                    //     ,shadeClose: true //点击遮罩关闭
                    //     ,content: "支付页面已在新窗口打开，请支付！"
                    // });
                    if (1 == JsonData.IsMobile) {
                        window.location.href = res.url;
                        layer.closeAll();
                    } else {
                        layer.closeAll();
                        a_alipay_url= res.url;
                        // window.open(res.url);
                        layer.confirm('支付页面已在新窗口打开，请支付！', {
                            btn: ['支付成功','支付失败'],
                            title:'正在支付...',
                        }, function(index){
                            layer.close(index);
                            OrderPayPolling();
                        }, function(index){
                            layer.close(index);
                            var submit_order_type = $('#submit_order_type').val();
                            if (undefined != submit_order_type && '0' === submit_order_type) {
                                if (b1decefec6b39feb3be1064e27be2a9.shop_centre_url) {
                                    window.location.href = b1decefec6b39feb3be1064e27be2a9.shop_centre_url;
                                } else {
                                    window.location.reload();
                                }
                            }
                        });
                    }
                    PayPolling = window.setInterval(OrderPayPolling, 2000);
                }
            } else {
                layer.closeAll();
                layer.alert(res.msg, {icon:0, title: false, closeBtn: 0});
            }
            
            $.ajax({
                url: JsonData.get_token_url,
                data: {_ajax:1},
                type:'get',
                dataType:'html',
                success:function(res) {
                    $('#__token__dfbfa92d4c447bf2c942c7d99a223b49').val(res);
                }
            });
        }
    });
    if (a_alipay_url != ""){
        newWinSppay3(a_alipay_url);
    }
    return false;
}

// 装载显示扫码支付的二维码
function AlertPayImg(data) {
    var html = "<img src='"+data.url_qrcode+"' style='width: 250px; height: 250px;'><br/><span style='color: red; display: inline-block; width: 100%; text-align: center;'>正在支付中...请勿刷新</span>";
    layer.alert(html, {
        title: false,
        btn: [],
        success: function() {
            PayPolling = window.setInterval(function(){ OrderPayPolling(); }, 2000);
        },
        cancel: function() {
            window.clearInterval(PayPolling);
            var submit_order_type = $('#submit_order_type').val();
            if (undefined != submit_order_type && '0' === submit_order_type) {
                if (b1decefec6b39feb3be1064e27be2a9.shop_centre_url) {
                    window.location.href = b1decefec6b39feb3be1064e27be2a9.shop_centre_url;
                } else {
                    window.location.reload();
                }
            }
        }
    });
}

// 订单轮询
function OrderPayPolling() {
    var pay_id = $('#PayID').val();
    var pay_mark = $('#PayMark').val();
    var pay_type = $('#PayType').val();
    if (!pay_id || !pay_mark || !unified_id || !unified_number || !transaction_type) {
        layer.msg('订单异常002，刷新重试', {time: 1500}, function(){
            window.location.reload();
        });
    }

    var is_wap = JsonData.is_wap;
    var submit_order_type = !$('#submit_order_type').val() ? -1 : $('#submit_order_type').val();
    $.ajax({
        url: JsonData.OrderPayPolling,
        data: {
            pay_id: pay_id,
            pay_mark: pay_mark,
            pay_type: pay_type,
            unified_id: unified_id,
            unified_number: unified_number,
            transaction_type: transaction_type,
            submit_order_type: submit_order_type
        },
        type:'post',
        dataType:'json',
        success:function(res){
            if (1 == res.code) {
                if (res.data) {
                    if (is_wap == 1) { // 移动端
                        layer_loading_mini();
                    } else { // PC端
                        layer_loading('订单处理中');
                    }
                    window.clearInterval(PayPolling);
                    if (2 == transaction_type) {
                        if (!res.data.mobile && !res.data.email) {
                            if (is_wap == 1) { // 移动端
                                window.location.href = res.url;
                                layer.closeAll();
                            } else { // PC端
                                layer.closeAll();
                                layer.msg(res.msg, {time: 1500}, function() {
                                    window.location.href = res.url;
                                });
                            }
                        }
                        if (res.data.mobile) SendMobile(res.data.mobile);
                        if (res.data.email) SendEmail(res.data.email);
                    }

                    if (is_wap == 1) { // 移动端
                        window.location.href = res.url;
                        layer.closeAll();
                    } else { // PC端
                        layer.closeAll();
                        layer.msg(res.msg, {time: 1500}, function() {
                            window.location.href = res.url;
                        });
                    }
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
            async: false,
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
            async: false,
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
                if (1 != JsonData.IsMobile){
                    layer.alert('用户取消支付！', {icon:0, title: false, closeBtn: 0});
                }
            } else {
                layer.alert('支付失败！', {icon:0, title: false, closeBtn: 0});
            }
        }
    );
}

function pay_deal_with() {
    $.ajax({
        async: false,
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
    var a_alipay_url = "";
    layer_loading('正在处理');
    $.ajax({
        async: false,
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
                        } else if (res.data.is_applets && 1 == res.data.is_applets) {
                            // 微信小程序内支付
                            $('#unified_id').val(unified_id);
                            $('#unified_number').val(unified_number);
                            $('#transaction_type').val(transaction_type);
                            WeChatInternal(res.data);
                        } else if (res.msg.url_qrcode) {
                            // PC端浏览器扫码支付
                            AlertPayImg(res.msg);
                        } else {
                            // 手机端浏览器支付
                            layer_loading('订单支付中');
                            if (1 == JsonData.IsMobile) {
                                window.location.href = res.url;
                            } else {
                                a_alipay_url = res.url;
                                // window.open(res.url);
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
                            a_alipay_url = res.msg.ReturnUrl;
                            // window.open(res.msg.ReturnUrl);
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
                        a_alipay_url = res.data.url;
                        // window.open(res.data.url);
                        PayPolling = window.setInterval(OrderPayPolling, 1000);
                    } else {
                        layer_loading('订单支付中');
                        if (1 == JsonData.IsMobile) {
                            window.location.href = res.url;
                        } else {
                            a_alipay_url = res.url;
                            // window.open(res.url);
                        }
                        PayPolling = window.setInterval(OrderPayPolling, 2000);
                    }
                }
            } else {
                layer.alert(res.msg, {icon:0, title: false, closeBtn: 0});
            }
        }
    });
    if (a_alipay_url != ""){
        newWinSppay3(a_alipay_url);
    }
    return false;
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

// 弹框支付 开始
function SelectPayMethodLayer(pay_id, pay_mark) {
    var parentObj = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
    var _parent = parent;

    if (!pay_id || !pay_mark || !unified_id || !unified_number || !transaction_type) {
        _parent.layer.close(parentObj);
        _parent.layer.msg('订单异常003，刷新重试', {time: 1500}, function(){
            window.location.reload();
        });
    }
    layer_loading('订单处理中');
    $.ajax({
        async: false,
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
            _parent.layer.close(parentObj);
            var pollingData = {
                pay_id: pay_id,
                pay_mark: pay_mark,
                unified_id: unified_id,
                unified_number: unified_number,
                transaction_type: transaction_type,
                OrderPayPolling: JsonData.OrderPayPolling,
            };
            if (1 == res.code) {
                $('#PayID').val(pay_id);
                $('#PayMark').val(pay_mark);
                if (res.data.appId) {
                    callpay(res.data);
                } else if (res.data.url_qrcode) {
                    AlertPayImgLayer(res.data,pollingData);
                } else if (1 == res.data.is_paypal) {
                    if (res.data.item_name && res.data.amount && res.data.invoice) {
                        $('#eyou_paypalForm, #eyou_itemName').val(res.data.item_name);
                        $('#eyou_paypalForm, #eyou_amount').val(res.data.amount);
                        $('#eyou_paypalForm, #eyou_invoice').val(res.data.invoice);
                        if (res.data.eyou_cancel_return) $('#eyou_paypalForm, #eyou_cancel_return').val(res.data.eyou_cancel_return);
                        $('#eyou_paypalForm, #eyou_submitForm').click();
                    }
                    PayPolling = window.setInterval(OrderPayPolling, 1000);
                } else {
                    if (1 == JsonData.IsMobile) {
                        _parent.window.location.href = res.url;
                    } else {
                        _parent.layer.confirm('支付页面已在新窗口打开，请支付！', {
                            btn: ['支付成功','支付失败'],
                            title:false,
                            closeBtn:0
                        }, function(){
                            _parent.window.location.reload();
                        }, function(){
                            _parent.window.location.reload();
                        });
                        _parent.window.open(res.url);
                    }
                    pollingData = JSON.stringify(pollingData);
                    _parent.PayPolling = _parent.setInterval("parent.OrderPayPolling('"+pollingData+"');", 3000);
                }
            } else {
                _parent.layer.alert(res.msg, {icon:0, title: false, closeBtn: 0});
            }
        }
    });
}

// 装载显示扫码支付的二维码
function AlertPayImgLayer(data,pollingData) {
    var _parent = parent;
    var html = "<img src='"+data.url_qrcode+"' style='width: 250px; height: 250px;'><br/><span style='color: red; display: inline-block; width: 100%; text-align: center;'>正在支付中...请勿刷新</span>";
    _parent.layer.alert(html, {
        title: false,
        btn: [],
        success: function() {
            pollingData = JSON.stringify(pollingData);
            _parent.PayPolling = _parent.setInterval("parent.OrderPayPolling('"+pollingData+"');", 3000);
        },
        cancel: function() {
            _parent.clearInterval(_parent.PayPolling);
        }
    });
}
// 弹框支付 结束

//通过a标签点击事件弹出支付宝支付页面
function newWinSppay3(url) {
    var a = document.createElement("a");
    a.setAttribute("href", url);
    a.setAttribute("target", "_blank");
    a.setAttribute('style', 'display:none');
    document.body.appendChild(a);
    a.click();
    a.parentNode.removeChild(a);
}
