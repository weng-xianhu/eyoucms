function tag_userinfo_1608459452(result)
{
    var users_id = getCookie_1610585974('users_id');
    if (!users_id) {
        return true;
    }

    var before_display = '';
    var htmlObj = document.getElementById(result.htmlid);
    if (!htmlObj) {
        return true;
    } else {
        before_display = htmlObj.style.display;
    }

    if (users_id > 0 && htmlObj) {
        htmlObj.style.display = 'none';
    }

    /*图形验证码*/
    var ey_login_vertify_display = '';
    if (document.getElementById('ey_login_vertify')) {
        ey_login_vertify_display = document.getElementById('ey_login_vertify').style.display;
        document.getElementById('ey_login_vertify').style.display = 'none';
    }
    /*end*/

    /*第三方快捷登录*/
    var third_party_login_display = third_party_wxlogin_display = third_party_wblogin_display = third_party_qqlogin_display = '';
    if (document.getElementById('ey_third_party_login')) {
        third_party_login_display = document.getElementById('ey_third_party_login').style.display;
        document.getElementById('ey_third_party_login').style.display = 'none';
        if (document.getElementById('ey_third_party_wxlogin')) {
            third_party_wxlogin_display = document.getElementById('ey_third_party_wxlogin').style.display;
            document.getElementById('ey_third_party_wxlogin').style.display = 'none';
        }
        if (document.getElementById('ey_third_party_wblogin')) {
            third_party_wblogin_display = document.getElementById('ey_third_party_wblogin').style.display;
            document.getElementById('ey_third_party_wblogin').style.display = 'none';
        }
        if (document.getElementById('ey_third_party_qqlogin')) {
            third_party_qqlogin_display = document.getElementById('ey_third_party_qqlogin').style.display;
            document.getElementById('ey_third_party_qqlogin').style.display = 'none';
        }
    }
    /*end*/

    if (window.jQuery) {
        $.ajax({
            type : 'POST',
            url : result.root_dir+"/index.php?m=api&c=Diyajax&a=check_userinfo",
            data : {_ajax:1},
            dataType : 'json',
            success : function(res){
                loginafter_1610585974(res, htmlObj, before_display, ey_login_vertify_display, third_party_login_display, third_party_wxlogin_display, third_party_wblogin_display, third_party_qqlogin_display);
                
            }
        });
    } else {
        //步骤一:创建异步对象
        var ajax = new XMLHttpRequest();
        //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
        ajax.open("post", result.root_dir+"/index.php?m=api&c=Diyajax&a=check_userinfo", true);
        // 给头部添加ajax信息
        ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
        // 如果需要像 HTML 表单那样 POST 数据，请使用 setRequestHeader() 来添加 HTTP 头。然后在 send() 方法中规定您希望发送的数据：
        ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        //步骤三:发送请求+数据
        ajax.send('_ajax=1');
        //步骤四:注册事件 onreadystatechange 状态改变就会调用
        ajax.onreadystatechange = function () {
            //步骤五 如果能够进到这个判断 说明 数据 完美的回来了,并且请求的页面是存在的
            if (ajax.readyState==4 && ajax.status==200) {
                var json = ajax.responseText;  
                var res = JSON.parse(json);
                loginafter_1610585974(res, htmlObj, before_display, ey_login_vertify_display, third_party_login_display, third_party_wxlogin_display, third_party_wblogin_display, third_party_qqlogin_display);
          　}
        }
    }
}

function loginafter_1610585974(res, htmlObj, before_display, ey_login_vertify_display, third_party_login_display, third_party_wxlogin_display, third_party_wblogin_display, third_party_qqlogin_display)
{
    if (htmlObj) {
        htmlObj.style.display = before_display;
    }
    if (1 == res.code) {
        if (1 == res.data.ey_is_login) {
            if (htmlObj) {
                htmlObj.innerHTML = res.data.html;
                try {
                    executeScript_1610585974(res.data.html);
                } catch (e) {}
            }
        } else {
            /*图形验证码*/
            if (1 == res.data.ey_login_vertify && document.getElementById('ey_login_vertify')) {
                document.getElementById('ey_login_vertify').style.display = ey_login_vertify_display;
            }
            /*end*/
            
            /*第三方快捷登录*/
            if (1 == res.data.ey_third_party_login && document.getElementById('ey_third_party_login')) {
                document.getElementById('ey_third_party_login').style.display = third_party_login_display;
                if (1 == res.data.ey_third_party_wxlogin && document.getElementById('ey_third_party_wxlogin')) {
                    document.getElementById('ey_third_party_wxlogin').style.display = third_party_wxlogin_display;
                }
                if (1 == res.data.ey_third_party_wblogin && document.getElementById('ey_third_party_wblogin')) {
                    document.getElementById('ey_third_party_wblogin').style.display = third_party_wblogin_display;
                }
                if (1 == res.data.ey_third_party_qqlogin && document.getElementById('ey_third_party_qqlogin')) {
                    document.getElementById('ey_third_party_qqlogin').style.display = third_party_qqlogin_display;
                }
            }
            /*end*/
        }
    }
}

/**
 * 执行AJAX返回HTML片段中的JavaScript脚本
 * 将html里的js代码抽取出来，然后通过eval函数执行它
 * @param  {[type]} html [description]
 * @return {[type]}      [description]
 */
function executeScript_1610585974(html)
{
    var reg = /<script[^>]*>([^\x00]+)$/i;
    //对整段HTML片段按<\/script>拆分
    var htmlBlock = html.split("<\/script>");
    for (var i in htmlBlock) 
    {
        var blocks;//匹配正则表达式的内容数组，blocks[1]就是真正的一段脚本内容，因为前面reg定义我们用了括号进行了捕获分组
        if (blocks = htmlBlock[i].match(reg)) 
        {
            //清除可能存在的注释标记，对于注释结尾-->可以忽略处理，eval一样能正常工作
            var code = blocks[1].replace(/<!--/, '');
            try {
                eval(code) //执行脚本
            } catch (e) {}
        }
    }
}

// 读取 cookie
function getCookie_1610585974(c_name)
{
    if (document.cookie.length>0)
    {
      c_start = document.cookie.indexOf(c_name + "=")
      if (c_start!=-1)
      { 
        c_start=c_start + c_name.length+1 
        c_end=document.cookie.indexOf(";",c_start)
        if (c_end==-1) c_end=document.cookie.length
            return unescape(document.cookie.substring(c_start,c_end))
      } 
    }
    return "";
}
