
function newWin_1563185380(url, id) {
    var a = document.createElement("a");
    a.setAttribute("href", url);
    a.setAttribute("target", "_blank");
    a.setAttribute("id", id);
    // 防止反复添加
    if(!document.getElementById(id)) {
        document.body.appendChild(a);
    }
    a.click();
}

function ey_1563185380(file_id) {
    var downurl = document.getElementById("ey_file_list_"+file_id).value + "&_ajax=1";
    //创建异步对象
    var ajaxObj = new XMLHttpRequest();
    ajaxObj.open("get", downurl, true);
    ajaxObj.setRequestHeader("X-Requested-With","XMLHttpRequest");
    ajaxObj.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    //发送请求
    ajaxObj.send();
    ajaxObj.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (ajaxObj.readyState == 4 && ajaxObj.status == 200) {
          var json = ajaxObj.responseText;  
          var res = JSON.parse(json);
          if (0 == res.code) {
            // 没有登录
            if (undefined != res.data.is_login && 0 == res.data.is_login) {
                if (document.getElementById('ey_login_id_v665117')) {
                    $('#ey_login_id_v665117').trigger('click');
                } else {
                    window.location.href = res.data.url;
                    // newWin_1563185380(res.data.url, 'a_newWin_1563185380');
                }
            } else {
                if (res.data.need_buy == 1){
                    DownloadBuyNow(res.data.url,res.data.aid);
                    return false;
                } 
                if (!window.layer) {
                    alert(res.msg);
                    if (undefined != res.data.url && res.data.url) {
                        window.location.href = res.data.url;
                        // newWin_1563185380(res.data.url, 'a_newWin_1563185380');
                    }
                } else {
                    if (undefined != res.data.url && '' != res.data.url) {
                        layer.confirm(res.msg, {
                            title: false
                            , icon: 5
                            , closeBtn: false
                        }, function (index) {
                            layer.close(index);
                            window.location.href = res.data.url;
                            // newWin_1563185380(res.data.url, 'a_newWin_1563185380');
                        });
                    } else {
                        layer.alert(res.msg, {icon: 5, title: false, closeBtn: false});
                    }
                }
            }
            return false;
          }else{
            window.location.href = res.url;
            // newWin_1563185380(res.url, 'a_newWin_1563185380');
          }
        } 
    };
};
  
  // 立即购买
function DownloadBuyNow(url,aid){
    // 步骤一:创建异步对象
    var ajax = new XMLHttpRequest();
    //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
    ajax.open("post", url, true);
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
                    area: ['500px', '202px'],
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