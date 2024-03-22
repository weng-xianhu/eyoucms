// 一进来视频文档内页，默认就自动播放
if (vars1612143009.player == 'default') {
    changeVideoUrl341922(vars1612143009.file_id_0, vars1612143009.aid, vars1612143009.uhash_0);
}

function changeVideoUrl341922(id, aid, uhash, type) {
    if (document.getElementById("VideoDiv13579")) {
        if (video_sp_1618221427 && video_sp_1618221427 == 'sp1') {
            console.log('函数：changeVideoUrl341922_sp1');
            changeVideoUrl341922_sp1(id, aid, uhash, type); // 第一套demo视频模板
        } else {
            console.log('函数：changeVideoUrl341922_sp2');
            changeVideoUrl341922_sp2(id, aid, uhash, type); // 知了那套
        }
    } else {
        console.log('函数：changeVideoUrl341922_sp3');
        changeVideoUrl341922_sp3(id, aid, uhash, type); // 易而优
    }
}

function changeVideoUrl341922_sp1(id, aid, uhash, type) {
    submitPlayRecord(); // 记录播放时长
    //步骤一:创建异步对象
    var ajax = new XMLHttpRequest();
    //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
    ajax.open("post", vars1612143009.root_dir + "/index.php?m=home&c=View&a=pay_video_url", true);
    // 给头部添加ajax信息
    ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
    // 如果需要像 HTML 表单那样 POST 数据，请使用 setRequestHeader() 来添加 HTTP 头。然后在 send() 方法中规定您希望发送的数据：
    ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    //步骤三:发送请求+数据
    ajax.send('_ajax=1&id='+id+'&aid='+aid+'&uhash='+uhash);
    //步骤四:注册事件 onreadystatechange 状态改变就会调用
    ajax.onreadystatechange = function () {
        //步骤五 如果能够进到这个判断 说明 数据 完美的回来了,并且请求的页面是存在的
        if (ajax.readyState==4 && ajax.status==200) {
            var json = ajax.responseText;
            var res = JSON.parse(json);
            if (document.getElementById('fid1616057948')) {
                document.getElementById('fid1616057948').value = id;
            }
            if (res.code == 1) {
                if (res.data.txy_video_html) {
                    // 腾讯云点播视频
                    $('#local_video_id').hide();
                    $('#txy_video_id').show().empty().html(res.data.txy_video_html);
                } else {
                    $('#local_video_id').show();
                    $('#txy_video_id').hide();
                    let obj = document.getElementById('video_play_20200520_'+aid);
                    if (obj) {
                        if (document.getElementById("VideoDiv13579")) {
                            document.getElementById("VideoDiv13579").style.display = 'none';
                        }
                        
                        if (obj.getElementsByTagName('source')[0]) {
                            obj.getElementsByTagName('source')[0].src = res.url;
                            obj.load();
                        } else {
                            obj.src = res.url;
                        }

                        if ('video' == obj.tagName.toLowerCase()) {
                            obj.controls = 'controls';
                            var autoplay = vars1612143009.autoplay;
                            if ('on' == autoplay) {
                                document.getElementById('video_play_20200520_'+aid).play();
                            } else if ('off' == autoplay) {
                                document.getElementById('video_play_20200520_'+aid).autoplay = false;
                            } else {
                                document.getElementById('video_play_20200520_'+aid).play();
                            }
                        }
                    } else {
                        if (!window.layer) {
                            alert('请查看模板里videoplay视频播放标签是否完整！');
                        } else {
                            layer.alert('请查看模板里videoplay视频播放标签是否完整！', {icon: 5, title: false, closeBtn: false});
                        }
                    }
                }
            } else {
                if (document.getElementById("VideoDiv13579")) {
                    document.getElementById("VideoDiv13579").style.display = 'block';
                    document.getElementById('video_play_20200520_'+aid).pause();
                    document.getElementById('video_play_20200520_'+aid).setAttribute("src", '');
                }

                if (document.getElementById("MsgTitle13579")) {
                    if (!res.data.users_id) {
                        document.getElementById('MsgTitle13579').innerHTML = '';
                    } else {
                        document.getElementById('MsgTitle13579').innerHTML = res.msg;
                    }
                }

                if (document.getElementById("MsgOnclick13579") && res.data) {
                    document.getElementById('MsgOnclick13579').innerHTML = res.data.button;
                    document.getElementById("MsgOnclick13579").setAttribute("onclick", res.data.onclick);
                }
            }
      　}
    }
}

function changeVideoUrl341922_sp2(id, aid, uhash, type) {
    submitPlayRecord(); // 记录播放时长
    //步骤一:创建异步对象
    var ajax = new XMLHttpRequest();
    //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
    ajax.open("post", vars1612143009.root_dir + "/index.php?m=home&c=View&a=pay_video_url", true);
    // 给头部添加ajax信息
    ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
    // 如果需要像 HTML 表单那样 POST 数据，请使用 setRequestHeader() 来添加 HTTP 头。然后在 send() 方法中规定您希望发送的数据：
    ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    //步骤三:发送请求+数据
    ajax.send('_ajax=1&id='+id+'&aid='+aid+'&uhash='+uhash);
    //步骤四:注册事件 onreadystatechange 状态改变就会调用
    ajax.onreadystatechange = function () {
        //步骤五 如果能够进到这个判断 说明 数据 完美的回来了,并且请求的页面是存在的
        if (ajax.readyState==4 && ajax.status==200) {
            var json = ajax.responseText;
            var res = JSON.parse(json);
            if (document.getElementById('fid1616057948')) {
                document.getElementById('fid1616057948').value = id;
            }
            // 焦点
            $(".VideoCur341922").each(function(){
                $(this).parent().removeClass('cur');
            });
            $('.VideoCur341922_'+id).parent().addClass('cur');
            $('.text-white').text($('.VideoCur341922_'+id).attr('data-file_title'));
            if (res.code == 1) {
                if (res.data.txy_video_html) {
                    // 腾讯云点播视频
                    $('#local_video_id').hide();
                    $('#txy_video_id').show().empty().html(res.data.txy_video_html);
                } else {
                    $('#local_video_id').show();
                    $('#txy_video_id').hide();
                    let obj = document.getElementById('video_play_20200520_'+aid);
                    if (obj) {
                        if (document.getElementById("VideoDiv13579")) {
                            document.getElementById("VideoDiv13579").style.display = 'none';
                        }

                        if (obj.getElementsByTagName('source')[0]) {
                            obj.getElementsByTagName('source')[0].src = res.url;
                            obj.load();
                        } else {
                            obj.src = res.url;
                        }

                        if ('video' == obj.tagName.toLowerCase()) {
                            obj.controls = 'controls';
                            var autoplay = vars1612143009.autoplay;
                            if ('on' == autoplay) {
                                document.getElementById('video_play_20200520_'+aid).play();
                            } else if ('off' == autoplay) {
                                document.getElementById('video_play_20200520_'+aid).autoplay = false;
                            } else {
                                document.getElementById('video_play_20200520_'+aid).play();
                            }
                        }
                    } else {
                        if (!window.layer) {
                            alert('请查看模板里videoplay视频播放标签是否完整！');
                        } else {
                            layer.alert('请查看模板里videoplay视频播放标签是否完整！', {icon: 5, title: false, closeBtn: false});
                        }
                    }
                }
            } else {
                if (document.getElementById("VideoDiv13579")) {
                    document.getElementById("VideoDiv13579").style.display = 'block';
                    document.getElementById('video_play_20200520_'+aid).pause();
                    document.getElementById('video_play_20200520_'+aid).setAttribute("src", '');
                }

                if (document.getElementById("MsgTitle13579")) {
                    if (!res.data.users_id) {
                        document.getElementById("MsgTitle13579").style.display = 'none';
                        document.getElementById('MsgTitle13579').innerHTML = '';
                    } else {
                        document.getElementById('MsgTitle13579').innerHTML = res.msg;
                    }
                }

                if (document.getElementById("MsgOnclick13579") && res.data) {
                    document.getElementById('MsgOnclick13579').innerHTML = res.data.button;
                    document.getElementById("MsgOnclick13579").setAttribute("onclick", res.data.onclick);
                }
            }
      　}
    }
}

function changeVideoUrl341922_sp3(id, aid, uhash, type) {
    if (type == 'play' || type == 'list') {
        checkAuth_v341922(id, aid, type);
        return false;
    }
    submitPlayRecord(); // 记录播放时长
    //步骤一:创建异步对象
    var ajax = new XMLHttpRequest();
    //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
    ajax.open("post", vars1612143009.root_dir + "/index.php?m=home&c=View&a=pay_video_url", true);
    // 给头部添加ajax信息
    ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
    // 如果需要像 HTML 表单那样 POST 数据，请使用 setRequestHeader() 来添加 HTTP 头。然后在 send() 方法中规定您希望发送的数据：
    ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    //步骤三:发送请求+数据
    ajax.send('_ajax=1&id='+id+'&aid='+aid+'&uhash='+uhash);
    //步骤四:注册事件 onreadystatechange 状态改变就会调用
    ajax.onreadystatechange = function () {
        //步骤五 如果能够进到这个判断 说明 数据 完美的回来了,并且请求的页面是存在的
        if (ajax.readyState==4 && ajax.status==200) {
            var json = ajax.responseText;
            var res = JSON.parse(json);
            if (document.getElementById('fid1616057948')) {
                document.getElementById('fid1616057948').value = id;
            }
            if (res.code == 1) {
                if (res.data.txy_video_html) {
                    // 腾讯云点播视频
                    $('#local_video_id').hide();
                    $('#txy_video_id').show().empty().html(res.data.txy_video_html);
                } else {
                    $('#local_video_id').show();
                    $('#txy_video_id').hide();
                    let obj = document.getElementById('video_play_20200520_'+aid);
                    if (obj) {
                        if (document.getElementById("VideoDiv13579")) {
                            document.getElementById("VideoDiv13579").setAttribute("style", "display: none");
                        }
                        
                        if (obj.getElementsByTagName('source')[0]) {
                            obj.getElementsByTagName('source')[0].src = res.url;
                            obj.load();
                        } else {
                            obj.src = res.url;
                        }

                        if ('video' == obj.tagName.toLowerCase()) {
                            obj.controls = 'controls';
                            var autoplay = vars1612143009.autoplay;
                            if ('on' == autoplay) {
                                document.getElementById('video_play_20200520_'+aid).play();
                            } else if ('off' == autoplay) {
                                document.getElementById('video_play_20200520_'+aid).autoplay = false;
                            } else {
                                document.getElementById('video_play_20200520_'+aid).play();
                            }
                        }
                    } else {
                        if (!window.layer) {
                            alert('请查看模板里videoplay视频播放标签是否完整！');
                        } else {
                            layer.alert('请查看模板里videoplay视频播放标签是否完整！', {icon: 5, title: false, closeBtn: false});
                        }
                    }
                }
            } else {
                if (document.getElementById("VideoDiv13579")) {
                    document.getElementById("VideoDiv13579").setAttribute("style", "display: block");
                    document.getElementById('video_play_20200520_'+aid).pause();
                    document.getElementById('video_play_20200520_'+aid).setAttribute("src", '');
                }
                
                if (document.getElementById("MsgTitle13579")) {
                    if (!res.data.users_id) {
                        document.getElementById("MsgTitle13579").setAttribute("style", "display: none");
                    }
                    document.getElementById('MsgTitle13579').innerHTML = res.msg;
                }

                if (document.getElementById("MsgOnclick13579") && res.data) {
                    document.getElementById('MsgOnclick13579').innerHTML = res.data.button;
                    document.getElementById("MsgOnclick13579").setAttribute("onclick", res.data.onclick);
                }
            }
      　}
    }
}

function checkAuth_v341922(fid, aid, type) {
    var url = vars1612143009.root_dir + "/index.php?m=home&c=View&a=play&aid="+aid+"&fid="+fid;
    //步骤一:创建异步对象
    var ajax = new XMLHttpRequest();
    //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
    ajax.open("post", vars1612143009.root_dir + "/index.php?m=home&c=View&a=check_auth", true);
    // 给头部添加ajax信息
    ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
    // 如果需要像 HTML 表单那样 POST 数据，请使用 setRequestHeader() 来添加 HTTP 头。然后在 send() 方法中规定您希望发送的数据：
    ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    //步骤三:发送请求+数据
    ajax.send('_ajax=1&fid='+fid+'&aid='+aid);
    //步骤四:注册事件 onreadystatechange 状态改变就会调用
    ajax.onreadystatechange = function () {
        //步骤五 如果能够进到这个判断 说明 数据 完美的回来了,并且请求的页面是存在的
        if (ajax.readyState==4 && ajax.status==200) {
            var json = ajax.responseText;  
            var res = JSON.parse(json);
            if (res.is_mobile == 1) {
                if (type == 'list') {
                    if (res.status == 1) {
                        if (window.jQuery) {
                            $("#BuyOnclick13579").click();
                        } else {
                            // IE
                            if (document.all) {
                                document.getElementById("BuyOnclick13579").click();
                            }
                            // 其它浏览器
                            else {
                                var e = document.createEvent("MouseEvents");
                                e.initEvent("click", true, true);
                                document.getElementById("BuyOnclick13579").dispatchEvent(e);
                            }
                        }
                    } else {
                        window.location.href = url;
                    }
                } else {
                    if (document.getElementById("video_play_20200520_" + aid).parentNode) {
                        if (res.status < 2) {
                            var html = '';
                            if (res.url) {
                                html = '<div class="buy_div"><p style="color: #ffffff;text-align:center;">' + res.msg + '</p><p class="buy_ap"><a href="' + res.url + '" class="buy_a">立即购买</a></p></div>';
                            } else {
                                html = '<div class="buy_div"><p class="buy_price_p">￥' + res.price + '</p><p class="buy_ap"><a  href="javascript:void(0);" onClick="MediaOrderBuy_v341922(' + aid + ');" class="buy_a">立即购买</a></p></div>';
                            }
                            document.getElementById("video_play_20200520_" + aid).parentNode.innerHTML = html;
                        } else {
                            window.location.href = url;
                        }
                    } else {
                        if (res.status == 1) {
                            if (window.jQuery) {
                                $("#BuyOnclick13579").click();
                            } else {
                                // IE
                                if (document.all) {
                                    document.getElementById("BuyOnclick13579").click();
                                }
                                // 其它浏览器
                                else {
                                    var e = document.createEvent("MouseEvents");
                                    e.initEvent("click", true, true);
                                    document.getElementById("BuyOnclick13579").dispatchEvent(e);
                                }
                            }
                        } else {
                            window.location.href = url;
                        }
                    }
                }
            } else {
                if (res.status == 0) {
                    if (!window.layer) {
                        alert(res.msg);
                    } else {
                        layer.alert(res.msg, {icon: 5, title: false, closeBtn: false});
                    }
                } else if (res.status==1) {
                    window.location.href=res.url;
                }else{
                    window.location.href=url;
                }
            }
            
      　}
    }
}

function MediaOrderBuy_v341922(aid) {
    // 步骤一:创建异步对象
    var ajax = new XMLHttpRequest();
    //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
    ajax.open("post", vars1612143009.root_dir + "/index.php?m=user&c=Media&a=media_order_buy", true);
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
            if (1 == res.code && res.url) {
                window.location.href = res.url;
            } else if (0 == res.code && res.url) {
                window.location.href = res.url;
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
