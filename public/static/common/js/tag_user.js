function tag_user(result)
{
    var obj = document.getElementById(result.id);
    var txtObj = document.getElementById(result.txtid);
    var before_display = document.getElementById(result.id) ? document.getElementById(result.id).style.display : '';
    var before_html = '';
    var before_txt_html = '';
    if (txtObj) {
        before_txt_html = txtObj.innerHTML;
        if ('login' == result.type) {
            txtObj.innerHTML = 'Loading…';
        }
    } else if (obj) {
        before_html = obj.innerHTML;
        if ('login' == result.type) {
            obj.innerHTML = 'Loading…';
        }
    }
    if (obj) {
        obj.style.display="none";
    } else {
        obj = txtObj;
    }
    //步骤一:创建异步对象
    var ajax = new XMLHttpRequest();
    //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
    var url = result.root_dir+"/index.php?m=api&c=Ajax&a=check_user&type="+result.type+"&img="+result.img+"&afterhtml="+result.afterhtml;
    if (result.currentstyle != '') {
        url += "&currentstyle="+result.currentstyle;
    }
    ajax.open("post", url, true);
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
            if (1 == res.code) {
                if (1 == res.data.ey_is_login) {
                    if (obj) {
                        if ('login' == result.type) {
                            if (result.txt.length > 0) {
                                res.data.html = result.txt;
                            }
                            if (txtObj) {
                                txtObj.innerHTML = res.data.html;
                            } else {
                                if (result.afterhtml) {
                                    obj.insertAdjacentHTML('afterend', res.data.html); 
                                    obj.remove();
                                } else {
                                    obj.innerHTML = res.data.html;
                                }
                            }
                            try {
                                obj.setAttribute("href", result.url);
                                if (!before_display) {
                                    obj.style.display=before_display;
                                }
                            }catch(err){}
                        } else if ('logout' == result.type) {
                            if (txtObj) {
                                txtObj.innerHTML = before_txt_html;
                            } else {
                                obj.innerHTML = before_html;
                            }
                            try {
                                if (!before_display) {
                                    obj.style.display=before_display;
                                }
                            }catch(err){}
                        } else if ('reg' == result.type) {
                            obj.style.display="none";
                        } else if ('cart' == result.type) {
                            try {
                                var cartidObj = document.getElementById(result.cartid);
                                if (cartidObj) {
                                    cartidObj.innerHTML = res.data.ey_cart_num_20191212;
                                }
                                if (!before_display) {
                                    obj.style.display=before_display;
                                }
                            }catch(err){}
                        }
                    }
                } else {
                    // 恢复未登录前的html文案
                    if (obj) {
                        if (txtObj) {
                            txtObj.innerHTML = before_txt_html;
                        } else {
                            obj.innerHTML = before_html;
                        }
                        if ('logout' == result.type) {
                            obj.style.display="none";
                        } else if ('cart' == result.type) {
                            try {
                                var cartidObj = document.getElementById(result.cartid);
                                if (cartidObj) {
                                    cartidObj.innerHTML = res.data.ey_cart_num_20191212;
                                }
                                if (!before_display) {
                                    obj.style.display=before_display;
                                }
                            }catch(err){}
                        } else {
                            try {
                                if (!before_display) {
                                    obj.style.display=before_display;
                                }
                            }catch(err){}
                        }
                    }
                }
            } else {
                if (obj) {
                    obj.innerHTML = 'Error';
                    try {
                        if (!before_display) {
                            obj.style.display=before_display;
                        }
                    }catch(err){}
                }
            }
      　}
    } 
}

function tag_user_info(result)
{
    var obj = document.getElementById(result.t_uniqid);
    var before_display = '';
    if (obj) {
        before_display = obj.style.display;
        obj.style.display="none";
    }

    //步骤一:创建异步对象
    var ajax = new XMLHttpRequest();
    //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
    ajax.open("post", result.root_dir+"/index.php?m=api&c=Ajax&a=get_tag_user_info&t_uniqid="+result.t_uniqid, true);
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
            if (1 == res.code) {
                if (1 == res.data.ey_is_login) {
                    var dtypes = res.data.dtypes;
                    var users = res.data.users;
                    for (var key in users) {
                        var subobj = document.getElementById(key);
                        if (subobj) {
                            if ('img' == dtypes[key]) {
                                subobj.setAttribute("src", users[key]);
                            } else if ('href' == dtypes[key]) {
                                subobj.setAttribute("href", users[key]);
                            } else {
                                subobj.innerHTML = users[key];
                            }
                        }
                    }
                    if (obj) {
                        try {
                            if (!before_display) {
                                obj.style.display=before_display;
                            }
                        }catch(err){}
                    }
                } else {
                    if (obj) {
                        obj.style.display="none";
                    }
                }
            }
      　}
    }
}

// 读取 cookie
function getCookie(c_name)
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