$( function() {
    // 小程序查询调用
    wx.miniProgram.getEnv( function(res) {
        if(res.miniprogram) {
            // 小程序
            var i = 0;
            i = setInterval('AppletsPay()', 1000);
        }
    });
});

// 小程序查询
function AppletsPay() {
    var unified_id       = $('#unified_id').val();
    var unified_number   = $('#unified_number').val();
    var transaction_type = $('#transaction_type').val();
    if (unified_id && unified_number && transaction_type) {
        $.ajax({
            url: eyou_basefile + "?m=user&c=Pay&a=ajax_applets_pay&_ajax=1",
            data: {unified_id:unified_id, unified_number:unified_number, transaction_type:transaction_type},
            type:'post',
            dataType:'json',
            success:function(res){
                if (1 == res.code) {
                    if (!res.data.mobile && !res.data.email) window.location.href = res.url;
                    if (res.data.mobile) SendMobile(res.data.mobile);
                    if (res.data.email) SendEmail(res.data.email);
                    window.location.href = res.url;
                }
            }
        });
    }
}

// 判断支付类型是否一致并且更新支付方式
function wechatJsApiPay(unified_id, unified_number, transaction_type) {
    layer_loading('正在处理');
    $.ajax({
        url : eyou_basefile + "?m=user&c=Pay&a=update_pay_method&_ajax=1",
        data: {unified_id:unified_id,unified_number:unified_number,pay_method:'WeChatInternal',transaction_type:transaction_type,order_source:2},
        type:'post',
        dataType:'json',
        success:function(res){
            layer.closeAll();
            if (0 == res.code) {
                showErrorAlert(res.msg, 0);
            }else{
                if (1 == res.data.is_gourl) {
                    window.location.href = res.url;
                }else{
                    $('#unified_id').val(unified_id);
                    $('#unified_number').val(unified_number);
                    $('#transaction_type').val(transaction_type);
                    WeChatInternal(unified_id, unified_number, transaction_type);
                }
            }
        }
    });
}

// 微信内部中进行支付
function WeChatInternal(unified_id, unified_number, transaction_type) {
    wx.miniProgram.getEnv( function(res) {
        if (res.miniprogram) {
            // 小程序
            wx.miniProgram.navigateTo({
                url: '/pages/pay/pay?unified_id='+ unified_id +'&unified_number=' + unified_number + '&type=' + transaction_type
            });
        } else {
            // 微信端
            $.ajax({
                url : eyou_basefile + "?m=user&c=Pay&a=wechat_pay&_ajax=1",
                data: {unified_id:unified_id,unified_number:unified_number,transaction_type:transaction_type},
                type:'post',
                dataType:'json',
                success:function(res){
                    layer.closeAll();
                    if (1 == res.code) {
                        callpay(res.msg);
                    }else{
                        showErrorAlert(res.msg, 0);
                    }
                }
            });
        }
    });
}

function jsApiCall(data) {
    WeixinJSBridge.invoke(
        'getBrandWCPayRequest',data,
        function(res){
            if(res.err_msg == "get_brand_wcpay_request:ok"){  
                layer.msg('微信支付完成！', {time: 1000}, function(){
                    pay_deal_with();
                });
            }else if(res.err_msg == "get_brand_wcpay_request:cancel"){
                showErrorAlert('用户取消支付！', 0);
            }else{
                showErrorAlert('支付失败，原因可能是订单号已支付！', 0);
            }  
        }
    );
}


// 微信内部支付时，先进行数据判断
function callpay(data) {
    if (typeof WeixinJSBridge == "undefined"){
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

// 微信订单查询
function pay_deal_with() {
    var unified_number   = $('#unified_number').val();
    var transaction_type = $('#transaction_type').val();
    $.ajax({
        url : eyou_basefile + "?m=user&c=Pay&a=pay_deal_with&_ajax=1",
        data: {unified_number:unified_number,transaction_type:transaction_type},
        type:'post',
        dataType:'json',
        success:function(res){
            if (1 == res.data.status) {
                if (!res.data.mobile && !res.data.email) window.location.href = res.url;
                if (res.data.mobile) SendMobile(res.data.mobile);
                if (res.data.email) SendEmail(res.data.email);
                window.location.href = res.url;
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

// 订单号复制
function orderCopy() {
    var clipboard1 = new Clipboard(".order_code");
    clipboard1.on("success", function(e) {
        layer.msg("复制成功");
    });
    clipboard1.on("error", function(e) {
        layer.msg("复制失败！请手动复制", {icon: 5});
    }); 
}

// 判断是核销码否存在img子元素
if ($("#qrcode img").length === 0) {
  // 隐藏代码
  $("#qrcode").hide();
}