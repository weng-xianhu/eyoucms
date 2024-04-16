var ey_jquery_v968479 = false;
if (!window.jQuery) {
    ey_jquery_v968479 = true;
} else {
    var ey_jq_ver_v968479 = jQuery.fn.jquery;
    if (ey_jq_ver_v968479 < '1.8.0') {
        ey_jquery_v968479 = true;
    }
}

if (ey_jquery_v968479) {
    document.write(unescape("%3Cscript src='"+root_dir_v968479+"/public/static/common/js/jquery.min.js?v=v1.6.4' type='text/javascript'%3E%3C/script%3E"));
    document.write(unescape("%3Cscript type='text/javascript'%3E try{jQuery.noConflict();}catch(e){} %3C/script%3E"));
}

if (!window.layer || !layer.v) {
    document.write(unescape("%3Cscript src='"+root_dir_v968479+"/public/plugins/layer-v3.1.0/layer.js' type='text/javascript'%3E%3C/script%3E"));
}

var PayPolling;
function ey_article_v968479(aid) {
    // 步骤一:创建异步对象
    var ajax = new XMLHttpRequest();
    //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
    ajax.open("post", buy_url_v968479, true);
    // 给头部添加ajax信息
    ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
    // 如果需要像 HTML 表单那样 POST 数据，请使用 setRequestHeader() 来添加 HTTP 头。然后在 send() 方法中规定您希望发送的数据：
    ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    //步骤三:发送请求+数据
    ajax.send('_ajax=1&aid=' + aid+'&return_url='+encodeURIComponent(window.location.href));
    //步骤四:注册事件 onreadystatechange 状态改变就会调用
    ajax.onreadystatechange = function () {
        //步骤五 请求成功，处理逻辑
        if (ajax.readyState==4 && ajax.status==200) {
            var json = ajax.responseText;  
            var res  = JSON.parse(json);
            if (1 == res.code) {
                window.location.href = res.url;
            } else {
                if (res.data.url){
                    //登录
                    if (-1 == res.data.url.indexOf('?')) {
                        window.location.href = res.data.url+'?referurl='+encodeURIComponent(window.location.href);
                    }else{
                        window.location.href = res.data.url+'&referurl='+encodeURIComponent(window.location.href);
                    }
                }else{
                    if (!window.layer) {
                        alert(res.msg);
                    } else {
                        layer.alert(res.msg, {icon: 5, title: false, closeBtn: false});
                    }
                }
            }
      　}
    };
}

function ey_ajax_get_content_v968479(aid,url) {
    // 步骤一:创建异步对象
    var ajax = new XMLHttpRequest();
    //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
    ajax.open("post", url, true);
    // 给头部添加ajax信息
    ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
    // 如果需要像 HTML 表单那样 POST 数据，请使用 setRequestHeader() 来添加 HTTP 头。然后在 send() 方法中规定您希望发送的数据：
    ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    //步骤三:发送请求+数据
    ajax.send('_ajax=1&aid=' + aid);
    //步骤四:注册事件 onreadystatechange 状态改变就会调用
    ajax.onreadystatechange = function () {
        //步骤五 请求成功，处理逻辑
        if (ajax.readyState==4 && ajax.status==200) {
            var json = ajax.responseText;
            var res  = JSON.parse(json);
            if (1 == res.code) {
                if (document.getElementById('article_content_'+aid+'_v061972')) {
                    document.getElementById('article_content_'+aid+'_v061972').innerHTML = res.data.content;
                }

                if (document.getElementById('article_display_'+aid+'_v061972')) {
                    if (res.data.display && 1 == res.data.display) {
                        document.getElementById('article_display_'+aid+'_v061972').style.display = "block";
                    }else if ('undefined' != res.data.display && 0 == res.data.display) {
                        document.getElementById('article_display_'+aid+'_v061972').style.display = "none";
                    }
                }

                if (document.getElementById('article_vipDisplay_'+aid+'_v061972')) {
                    if (res.data.vipDisplay && 1 == res.data.vipDisplay) {
                        document.getElementById('article_vipDisplay_'+aid+'_v061972').style.display = "block";
                    }else if ('undefined' != res.data.vipDisplay && 0 == res.data.vipDisplay) {
                        document.getElementById('article_vipDisplay_'+aid+'_v061972').style.display = "none";
                    }
                }

                if (res.data.users_price && document.getElementById('users_price_1640658971')){
                    document.getElementById('users_price_1640658971').innerHTML = res.data.users_price;
                }
                if (res.data.buy_onclick && document.getElementById('vipBuy230816')) {
                    document.getElementById('vipBuy230816').setAttribute("onclick",res.data.buy_onclick);
                    document.getElementById('vipBuy230816').innerText = "立即购买";
                }
            } else {
                if (!window.layer) {
                    alert(res.msg);
                } else {
                    layer.alert(res.msg, {icon: 5, title: false, closeBtn: false});
                }
            }
        }
    };
}

// 立即购买
function ArticleBuyNow(aid){
    // 步骤一:创建异步对象
    var ajax = new XMLHttpRequest();
    //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
    ajax.open("post", buy_url_v968479, true);
    // 给头部添加ajax信息
    ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
    // 如果需要像 HTML 表单那样 POST 数据，请使用 setRequestHeader() 来添加 HTTP 头。然后在 send() 方法中规定您希望发送的数据：
    ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    //步骤三:发送请求+数据
    ajax.send('_ajax=1&aid=' + aid+'&return_url='+encodeURIComponent(window.location.href));
    //步骤四:注册事件 onreadystatechange 状态改变就会调用
    ajax.onreadystatechange = function () {
        //步骤五 请求成功，处理逻辑
        if (ajax.readyState==4 && ajax.status==200) {
            var json = ajax.responseText;
            var res  = JSON.parse(json);
            if (1 == res.code) {
                layer.open({
                    type: 2,
                    title: '选择支付方式',
                    shadeClose: false,
                    maxmin: false, //开启最大化最小化按钮
                    skin: 'WeChatScanCode_20191120',
                    area: ['800px', '200px'],
                    content: res.url
                });
            } else {
                if (res.data.url){
                    //登录
                    if (document.getElementById('ey_login_id_v665117')) {
                        $('#ey_login_id_v665117').trigger('click');
                    } else {
                        if (-1 == res.data.url.indexOf('?')) {
                            window.location.href = res.data.url+'?referurl='+encodeURIComponent(window.location.href);
                        }else{
                            window.location.href = res.data.url+'&referurl='+encodeURIComponent(window.location.href);
                        }
                    }
                }else{
                    if (!window.layer) {
                        alert(res.msg);
                    } else {
                        layer.alert(res.msg, {icon: 5, title: false, closeBtn: false});
                    }
                }
            }
        }
    };
}

// 是否要去充值
function PayIsRecharge(msg ,url,unified_id,unified_number,transaction_type) {
    layer.confirm(msg, {
        title: false,
        closeBtn: 0,
        btn: ['去充值','其他方式支付'],
        cancel: function(index, layero){
            jQuery('#PayBalancePayment').prop("disabled", false).css("pointer-events", "");
        }
    }, function() {
        // 去充值
        // window.open(url);
        newWinarticlepay(url);
        layer.confirm('充值成功？ 是否立即支付？', {
            title: false,
            closeBtn: 0,
            btn: ['立即支付','其他方式支付'],
            cancel: function(index, layero){
                jQuery('#PayBalancePayment').prop("disabled", false).css("pointer-events", "");
            }
        }, function() {
            // 立即支付
            PayBalancePayment(unified_id,unified_number,transaction_type);

        }, function(index) {
            // 选择其他方式支付
            layer.closeAll(index);
            ArticleBuyNow(aid_v968479);
        });
    }, function(index) {
        // 选择其他方式支付时;
        layer.closeAll(index);
        ArticleBuyNow(aid_v968479);
    });
}

// 订单轮询
function OrderPayPolling(data, showMsgCode) {
    data = JSON.parse(data);
    if (!data.pay_id || !data.pay_mark || !data.unified_id || !data.unified_number || !data.transaction_type) {
        layer.msg('订单异常，刷新重试', {time: 1500}, function() {
            window.location.reload();
        });
    }
    jQuery.ajax({
        url: data.OrderPayPolling,
        data: {
            pay_id: data.pay_id,
            pay_mark: data.pay_mark,
            pay_type: data.pay_type,
            unified_id: data.unified_id,
            unified_number: data.unified_number,
            transaction_type: data.transaction_type
        },
        type: 'post',
        dataType: 'json',
        success: function(res){
            if (1 == res.code) {
                if (res.data) {
                    window.clearInterval(PayPolling);
                    if (9 == data.transaction_type) {
                        if (!res.data.mobile && !res.data.email) {
                            layer.msg(res.msg, {time: 1500}, function() {
                                window.location.reload();
                            });
                        }
                        if (res.data.mobile) SendMobile(res.data.mobile);
                        if (res.data.email) SendEmail(res.data.email);
                    }
                    layer.msg(res.msg, {time: 1500}, function() {
                        window.location.href = res.url;
                    });
                } else {
                    if (showMsgCode && res.msg) {
                        $('#' + showMsgCode).show().html(res.msg);
                        setTimeout(function() {
                            $('#' + showMsgCode).hide().html('');
                        }, 3000);
                    }
                }
            } else if (0 == res.code) {
                layer.alert(res.msg, {icon:0, title: false, closeBtn: 0});
            }
        }
    });
}

// 购买升级
function BuyVipClick()
{
    var url = buy_vip_url_v968479;
    if (url.indexOf('?') > -1) {
        url += '&';
    } else {
        url += '?';
    }
    url += 'referurl='+encodeURIComponent(window.location.href);
    window.location.href = url;
}

//通过a标签点击事件弹出支付宝支付页面
function newWinarticlepay(url) {
    var a = document.createElement("a");
    a.setAttribute("href", url);
    a.setAttribute("target", "_blank");
    a.setAttribute('style', 'display:none');
    document.body.appendChild(a);
    a.click();
    a.parentNode.removeChild(a);
}
