
var body_display = '';

// 轮询body元素是否被渲染出来
function ey_body_render(display_value)
{
    var obj = document.body;
    var timer = setTimeout(function(){
        if (null == obj || obj == '') {
            ey_body_render(display_value);
        } else {
            body_display = obj.style.display;
            obj.style.display = display_value;
            clearTimeout(timer); // 清理定时任务
        }
    }, 10);
}

// 文档阅读AJAX
function ey_1564127378() {
    var JsonData = ey_1564127251;
    var get_url = JsonData.get_url;
    var ClosePage = JsonData.ClosePage;

    // var users_id = ey_getCookie('users_id');
    // if (users_id == '' || users_id == 0) {
    //     ey_body_render('none');
    // }

    // 步骤一:创建异步对象
    var ajax = new XMLHttpRequest();
    //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
    ajax.open("post", get_url, true);
    // 给头部添加ajax信息
    ajax.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    // 如果需要像 HTML 表单那样 POST 数据，请使用 setRequestHeader() 来添加 HTTP 头。然后在 send() 方法中规定您希望发送的数据：
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    //步骤三:发送请求+数据
    ajax.send('_ajax=1&gourl='+encodeURIComponent(window.location.href));
    //步骤四:注册事件 onreadystatechange 状态改变就会调用
    ajax.onreadystatechange = function () {
        //步骤五 请求成功，处理逻辑
        if (ajax.readyState == 4 && ajax.status == 200) {
            var json = ajax.responseText;
            var res = JSON.parse(json);
            if (0 == res.code) {
                if (res.data && res.data.is_login == 0) {
                    // 不可以查看
                    ey_body_render(body_display);
                    document.body.innerHTML = "<div style='text-align:center; font-size:20px; font-weight:bold; margin:50px 0px;'>跳转到登录界面中……</div>";
                    window.location.href = res.data.gourl;
                } else {
                    // 不可以查看
                    document.body.innerHTML = "";
                    setTimeout(function () {
                        res.msg = res.msg.replace("__html__",'');
                        res.msg = res.msg.replace("<br/>",'，');
                        confirm(res.msg);
                        if (ClosePage) {
                            window.close();
                        } else {
                            var return_url = document.referrer;
                            window.location.href = return_url;
                        }
                    }, 600);
                }
            } else if (1 == res.code) {
                ey_body_render(body_display);
                if ('undefined' != res.data.is_admin && 1 == res.data.is_admin) {
                    setTimeout(function () {
                        res.msg = res.msg.replace("__html__",'');
                        res.msg = res.msg.replace("<br/>",'，');
                        alert(res.data.msg);
                    }, 1000);
                }
            }
        }
    };
}

ey_1564127378();

// 视频购买
function MediaOrderBuy_v878548() {
    var JsonData = ey_1564127251;
    var BuyUrl = JsonData.buy_url;
    var aid = JsonData.aid;

    // 步骤一:创建异步对象
    var ajax = new XMLHttpRequest();
    //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
    ajax.open("post", BuyUrl, true);
    // 给头部添加ajax信息
    ajax.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    // 如果需要像 HTML 表单那样 POST 数据，请使用 setRequestHeader() 来添加 HTTP 头。然后在 send() 方法中规定您希望发送的数据：
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    //步骤三:发送请求+数据
    ajax.send('aid=' + aid);
    //步骤四:注册事件 onreadystatechange 状态改变就会调用
    ajax.onreadystatechange = function () {
        //步骤五 请求成功，处理逻辑
        if (ajax.readyState == 4 && ajax.status == 200) {
            var json = ajax.responseText;
            var res = JSON.parse(json);
            if (1 == res.code && res.url) {
                window.location.href = res.url;
            } else {
                // 没有登录
                if (document.getElementById('ey_login_id_v665117')) { // 最新demo的弹窗登录
                    $('#ey_login_id_v665117').trigger('click');
                } else { // 一般模板
                    var url = '';
                    if (res.data && res.data.url) {
                        url = res.data.url;
                    } else {
                        url = res.url;
                    }

                    if (url.indexOf('?') > -1) {
                        url += '&';
                    } else {
                        url += '?';
                    }
                    url += 'referurl=' + encodeURIComponent(window.location.href);
                    window.location.href = url;
                }

            }
        }
    };
}

function MediaOrderBuy_1592878548() {
    MediaOrderBuy_v878548();
}

// 跳转至会员升级页面
function LevelCentre_v878548() {
    var JsonData = ey_1564127251;
    window.location.href = JsonData.LevelCentreUrl;
}

// 点击隐藏遮幕层并播放视频
function PlayVideo(id) {
    document.getElementsByClassName("jw-video-expense")[0].setAttribute("style", "display: none");
    document.getElementById(id).play();
}

// 视频播放逻辑AJAX
function ey_1618221427(type) {
    if (video_sp_1618221427 && video_sp_1618221427 == 'sp3') { // 易而优
        console.log('函数：video_moban_3');
        video_moban_3(video_sp_1618221427);
    } else {
        if (video_sp_1618221427 && video_sp_1618221427 == 'sp1') { // 第一套demo视频模板
            console.log('函数：video_moban_1');
            video_moban_1('sp1');
        } else {
            console.log('函数：video_moban_2');
            video_moban_2('sp2'); // 知了那套
        }
    }
}

function video_moban_1(type) {
    var JsonData = ey_1564127251;
    var VideoLogicUrl = JsonData.VideoLogicUrl;
    var aid = JsonData.aid;
    // 步骤一:创建异步对象
    var ajax = new XMLHttpRequest();
    //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
    ajax.open("post", VideoLogicUrl, true);
    // 给头部添加ajax信息
    ajax.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    // 如果需要像 HTML 表单那样 POST 数据，请使用 setRequestHeader() 来添加 HTTP 头。然后在 send() 方法中规定您希望发送的数据：
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    //步骤三:发送请求+数据
    ajax.send('aid=' + aid+'&type='+type);
    //步骤四:注册事件 onreadystatechange 状态改变就会调用
    ajax.onreadystatechange = function () {
        //步骤五 请求成功，处理逻辑
        if (ajax.readyState == 4 && ajax.status == 200) {
            var json = ajax.responseText;
            var res = JSON.parse(json);
            if (1 == res.code && document.getElementById("BuyOnclick13579")) {
                if (res.data.status_value == 0){ // 所有人免费
                    document.getElementById("BuyOnclick13579").innerHTML = '免费';
                }else if (res.data.status_value == 1){ // 所有人付费
                    document.getElementById("BuyOnclick13579").setAttribute("onclick", "MediaOrderBuy_v878548();");
                    document.getElementById("BuyOnclick13579").innerHTML = '立即购买';
                }else if (res.data.status_value == 3){ // 会员付费
                    document.getElementById("BuyOnclick13579").setAttribute("href", "javascript:void(0);");
                    document.getElementById("BuyOnclick13579").setAttribute("onclick", res.data.button_url);
                    document.getElementById("BuyOnclick13579").innerHTML = res.data.button;
                }else{ // 会员免费
                    document.getElementById("BuyOnclick13579").innerHTML = 'VIP';
                }
                document.getElementById("BuyOnclick13579").style.display = '';

                if (res.data.button) {
                    if (document.getElementsByClassName('VideoButton13579')[0]) {
                        var videoButton = document.getElementsByClassName('VideoButton13579');
                        for (var i = 0; i < videoButton.length; i++) {
                            videoButton[i].innerHTML = res.data.button;
                        }
                    }
                    if('观看' == res.data.button){
                        document.getElementById("BuyOnclick13579").setAttribute("href", "javascript:void(0);");
                        document.getElementById("BuyOnclick13579").setAttribute("onclick", "window.location.href='"+res.data.button_url+"'");
                        document.getElementById("BuyOnclick13579").innerHTML = '立即播放';
                    }
                }
            }
        }
    };
}

function video_moban_2(type) {
    var JsonData = ey_1564127251;
    var VideoLogicUrl = JsonData.VideoLogicUrl;
    var aid = JsonData.aid;

    var videoPeriodObj = '';
    if (document.getElementById("video-period-20190425")) {
        videoPeriodObj = document.getElementById("video-period-20190425");
    } else if (document.getElementsByClassName('video-period')[0]) {
        videoPeriodObj = document.getElementsByClassName('video-period')[0];
    }

    if (videoPeriodObj) {
        var display_old_value = videoPeriodObj.style.display;
        videoPeriodObj.style.display = 'none';

        // 步骤一:创建异步对象
        var ajax = new XMLHttpRequest();
        //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
        ajax.open("post", VideoLogicUrl, true);
        // 给头部添加ajax信息
        ajax.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        // 如果需要像 HTML 表单那样 POST 数据，请使用 setRequestHeader() 来添加 HTTP 头。然后在 send() 方法中规定您希望发送的数据：
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        //步骤三:发送请求+数据
        ajax.send('aid=' + aid+'&type='+type);
        //步骤四:注册事件 onreadystatechange 状态改变就会调用
        ajax.onreadystatechange = function () {
            //步骤五 请求成功，处理逻辑
            if (ajax.readyState == 4 && ajax.status == 200) {
                var json = ajax.responseText;
                var res = JSON.parse(json);
                videoPeriodObj.style.display = display_old_value;
                if (1 == res.code) {
                    if (res.data.status_value == 0){ // 所有人免费
                        videoPeriodObj.innerHTML = '<div class="video-free-now button button-big bg-yellow text-center radius-rounded text-middle">免费</div>';
                    }else if (res.data.status_value == 1){ // 所有人付费
                        if (document.getElementById("BuyOnclick13579")) {
                            document.getElementById("BuyOnclick13579").style.display = '';
                            document.getElementById("BuyOnclick13579").setAttribute("onclick", "MediaOrderBuy_v878548();");
                            document.getElementById("BuyOnclick13579").innerHTML = '立即购买';
                        }
                    }else if (res.data.status_value == 3){ // 会员付费
                        var html = videoPeriodObj.innerHTML;
                        videoPeriodObj.innerHTML = html+'<a class="video-free-now button button-big bg-yellow text-center radius-rounded text-middle" href="javascript:void(0);" onclick="'+res.data.button_url+'">'+res.data.button+'</a>';
                    }else{ // 会员免费
                        if (!res.data.button) {
                            res.data.button = 'VIP';
                        }
                        videoPeriodObj.innerHTML = '<div class="video-free-now button button-big bg-yellow text-center radius-rounded text-middle" style="cursor: pointer;" onclick="'+res.data.button_url+'">'+res.data.button+'</div>';
                    }

                    if (res.data.button) {
                        if (document.getElementsByClassName('VideoButton13579')[0]) {
                            var videoButton = document.getElementsByClassName('VideoButton13579');
                            for (var i = 0; i < videoButton.length; i++) {
                                videoButton[i].innerHTML = res.data.button;
                            }
                        }
                        if('观看' == res.data.button){
                            videoPeriodObj.innerHTML = '<a class="video-free-now button button-big bg-yellow text-center radius-rounded text-middle" href="'+res.data.button_url+'">立即播放</a>';
                        }
                    }

                    if (res.data.users_price && document.getElementById('users_price_1640658971')){
                        document.getElementById('users_price_1640658971').innerHTML = res.data.users_price;
                    }
                }
            }
        };
    }
}

// 易而优视频模板
function video_moban_3(type)
{
    var JsonData = ey_1564127251;
    var VideoLogicUrl = JsonData.VideoLogicUrl;
    var aid = JsonData.aid;
    // 步骤一:创建异步对象
    var ajax = new XMLHttpRequest();
    //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
    ajax.open("post", VideoLogicUrl, true);
    // 给头部添加ajax信息
    ajax.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    // 如果需要像 HTML 表单那样 POST 数据，请使用 setRequestHeader() 来添加 HTTP 头。然后在 send() 方法中规定您希望发送的数据：
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    //步骤三:发送请求+数据
    ajax.send('aid=' + aid+'&type='+type);
    //步骤四:注册事件 onreadystatechange 状态改变就会调用
    ajax.onreadystatechange = function () {
        //步骤五 请求成功，处理逻辑
        if (ajax.readyState == 4 && ajax.status == 200) {
            var json = ajax.responseText;
            var res = JSON.parse(json);
            if (1 == res.code) {
                if (res.data.status_name) {
                    if (document.getElementById("Mianfei13579")) { // 易而优
                        document.getElementById("Mianfei13579").innerHTML = res.data.status_name;
                    }
                }

                if (res.data.play_auth == 1) {
                    //有播放权限
                    if (document.getElementById("Xuexi20210201")) {  // 易而优
                        if (-1 < $.inArray(res.data.status_value, [2,3])) {
                            document.getElementById("Xuexi20210201").style.display = 'none';
                            if (document.getElementById("VipFreeLearn20210201")) {  // 易而优
                                var href = document.getElementById("Xuexi20210201").getAttribute("href");
                                document.getElementById("VipFreeLearn20210201").setAttribute("href", href);
                                document.getElementById("VipFreeLearn20210201").style.display = '';
                                document.getElementById("VipFreeLearn20210201").innerHTML = ('立即观看');//2024-03-22新更改逻辑
                            }
                        } else {
                            document.getElementById("Xuexi20210201").style.display = '';
                        }
                    }
                } else {
                    //没有播放权限
                    if (-1 < $.inArray(res.data.status_value, [1,3])) {
                        if (res.data.is_pay > 0 || (res.data.vip_status == 0 && 3 == res.data.status_value)) {
                            if (document.getElementById("VipFreeLearn20210201") && 3 == res.data.status_value) {  // 易而优
                                document.getElementById("VipFreeLearn20210201").style.display = '';
                                if (3 == res.data.status_value ) {
                                    document.getElementById("VipFreeLearn20210201").innerHTML = (res.data.status_name+'可购买');
                                }else{
                                    document.getElementById("VipFreeLearn20210201").innerHTML = ('升级'+res.data.status_name+'免费学');
                                }
                                document.getElementById("VipFreeLearn20210201").setAttribute("title", res.data.status_name+'可免费观看');
                            }
                        } else {
                            if (document.getElementById("BuyOnclick20210201")) {
                                document.getElementById("BuyOnclick20210201").style.display = 'block';
                            }
                        }
                    } else if (res.data.status_value == 2) {
                        if (document.getElementById("VipFreeLearn20210201")) {  // 易而优
                            document.getElementById("VipFreeLearn20210201").style.display = '';
                        }
                        if (res.data.no_vip_pay) {
                            document.getElementById("BuyOnclick20210201").style.display = '';
                        }
                    }
                }

                if (res.data.button) {
                    if (document.getElementsByClassName('VideoButton13579')[0]) {
                        var videoButton = document.getElementsByClassName('VideoButton13579');
                        for (var i = 0; i < videoButton.length; i++) {
                            videoButton[i].innerHTML = res.data.button;
                        }
                    }
                }
            } else {
                if (document.getElementById("BuyOnclick20210201")) {
                    document.getElementById("BuyOnclick20210201").style.display = 'block';
                }
            }
        }
    };
}