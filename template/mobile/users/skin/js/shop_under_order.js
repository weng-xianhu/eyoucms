$(function() {
    // 默认支付方式
    $($('.pay-type-item')[0]).trigger('click');
});

// 支付方式选择
function selectPayType(obj) {
    var type = $(obj).data('type');
    // 重置支付方式
    $('#payment_type').val(type);
    // 在线支付 or 线下支付
    $('#payment_method').val(0);
    if ('hdfk_payOnDelivery' == type) $('#payment_method').val(1);
    // 重置选中标记
    $(obj).siblings().removeClass('active').end().addClass('active');
}

// 返回指定URL页面
function returnUrl(url) {
    if (url) {
        window.location.href = url;
    } else {
        layer.alert('没有指定URL页面', {icon:0, title: false, closeBtn: 0});
    }
}

$( function() {
    // 小程序查询调用
    wx.miniProgram.getEnv( function(res) {
        if(res.miniprogram) {
            // 小程序
            var i = 0;
            i = setInterval('AppletsPay()', 1000);
        }
    });

    // 支付类型选择
    $('.pay-type-item').click(function(){
        $(this).siblings().removeClass('active').end().addClass('active');
        var payment_method = $('#payment_method').val();
        if (0 == payment_method) {
            $('#payment_type').val($(this).data('type'));
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
function wechatJsApiPay() {
    if ('yezf_balance' == $('#payment_type').val()) {
        // 微信端余额支付
        ShopPaymentPage();
    } else {
        // 微信端内部支付
        layer_loading('正在处理');
        $.ajax({
            url: eyou_basefile + "?m=user&c=Shop&a=shop_payment_page&_ajax=1",
            data: $('#theForm').serialize(),
            type:'post',
            dataType:'json',
            success:function(res){
                layer.closeAll();
                if (1 == res.code) {
                    if (1 == res.data.is_gourl) {
                        window.location.href = res.url;
                    } else {
                        $('#unified_id').val(res.data.unified_id);
                        $('#unified_number').val(res.data.unified_number);
                        $('#transaction_type').val(res.data.transaction_type);
                        WeChatInternal(res.data);
                    }
                } else {
                    layer.alert(res.msg, {icon:0, title: false, closeBtn: 0});
                }
            }
        });
    }
}

// 微信内部中进行支付
function WeChatInternal(wechatdata) {
    wx.miniProgram.getEnv( function(res) {
        if(res.miniprogram) {
            // 小程序
            wx.miniProgram.navigateTo({
                url: '/pages/pay/pay?unified_id='+ wechatdata['unified_id'] +'&unified_number=' + wechatdata['unified_number'] + '&type=' + wechatdata['transaction_type']
            });
        } else {
            // 微信端
            $.ajax({
                url: eyou_basefile + "?m=user&c=Pay&a=wechat_pay&_ajax=1",
                data: wechatdata,
                type:'post',
                dataType:'json',
                success:function(res){
                    if (1 == res.code) {
                        callpay(res.msg);
                    } else {
                        showErrorAlert(res.msg);
                    }
                }
            });
        }
    });
}

//调用微信JS api 支付
function jsApiCall(data) {
    WeixinJSBridge.invoke(
        'getBrandWCPayRequest',data,
        function(res){
            if (res.err_msg == "get_brand_wcpay_request:ok") {  
                layer.msg('微信支付完成！', {time: 1000}, function() {
                    pay_deal_with();
                });
            } else if (res.err_msg == "get_brand_wcpay_request:cancel") {
                layer.alert('用户取消支付，跳转至订单列表页进行支付！', {icon:5, title: false, closeBtn: false}, function() {
                    var OrderUrl = eyou_basefile + "?m=user&c=Shop&a=shop_centre";
                    window.location.href = OrderUrl;
                });
            } else {
                showErrorAlert('支付失败');
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
        url: eyou_basefile + "?m=user&c=Pay&a=pay_deal_with&_ajax=1",
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

// 支付方式选择
function payTag(showContent, selfObj, type) {
    // 操作标签
    var tag = document.getElementById("payTags").getElementsByTagName("li");
    var taglength = tag.length;
    for (i = 0; i < taglength; i++) {
        tag[i].className = "";
    }
    selfObj.parentNode.className = "payTag";

    // 操作内容
    var LiDiv = document.getElementsByClassName('li_div');
    for (i = 0; i < LiDiv.length; i++) {
        LiDiv[i].style.display = "none";
    }
    document.getElementById(showContent).style.display = "block";
    
    if ('hdfk' == type) { // 货到付款
        $('#payment_method').val(1);
        $('#payment_type').val('hdfk_payOnDelivery');
    } else {
        $('#payment_method').val(0);
        if ('yezf' == type) { // 余额支付
            $('#payment_type').val('yezf_balance');
        } else {
            $('#payment_type').val(type);
        }
    }
}

// 支付方式选择
function payTag2(obj) {
    var type = $(obj).data('type');
    if ('hdfk_payOnDelivery' == type) { // 货到付款
        $('#payment_method').val(1);
        $('#payment_type').val('hdfk_payOnDelivery');
    } else {
        $('#payment_method').val(0);
        if ('yezf_balance' == type) { // 余额支付
            $('#payment_type').val('yezf_balance');
        } else {
            $('#payment_type').val(type);
        }
    }
}
