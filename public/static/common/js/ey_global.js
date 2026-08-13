
// 首页、列表页等加入购物车
function ShopAddCart1625194556(aid, spec_value_id, num, rootDir) {
    rootDir = rootDir ? rootDir : '';
    $.ajax({
        url : rootDir + '/index.php?m=user&c=Shop&a=shop_add_cart&_ajax=1',
        data: {aid: aid, num: num, spec_value_id: spec_value_id},
        type:'post',
        dataType:'json',
        success:function(res){
            if (1 == res.code) {
                window.location.href = res.url;
            } else {
                if (-1 == res.data.code) {
                    layer.msg(res.msg, {time: time});
                } else {
                    // 去登陆
                    window.location.href = res.url;
                }
            }
        }
    });
}

function openNewWin(url, id) {
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

/**
 * 锚点 - 内容页显示目录大纲
 * @param  {[type]} toc_id     [目录大纲的最外层元素id]
 * @param  {[type]} content_id [内容的元素id]
 * @return {[type]}            [description]
 */
function ey_outline_toc(content_id, toc_id, scrollTop)
{
    setTimeout(function(){
        // 是否要显示目录大纲
        var is_show_toc = false;
        // 获取要显示目录的元素
        const tocContainer = document.getElementById(toc_id);
        if (tocContainer) {
            // 获取要提取h2\h3\h4\h5\h6的内容元素
            const articleObj = document.getElementById(content_id);
            // 获取所有标题元素
            if (articleObj) {
                const headers = articleObj.querySelectorAll('h2, h3');
                // 内容里是否存在h2\h3\h4\h5\h6标签
                if (headers.length > 0) {
                    // 获取锚点
                    var anchor = window.location.hash;
                    // 创建目录列表
                    const tocList = document.createElement('ul');
                    // 遍历标题元素，创建目录项
                    headers.forEach((header) => {
                        const level = header.tagName.substr(1);
                        const tocItem = document.createElement('li');
                        const link = document.createElement('a');
                        var name = '';
                        if (header.id) {
                            name = header.id;
                        } else if (header.querySelector('a') && header.querySelector('a').name) {
                            name = header.querySelector('a').name;
                        }
                        if (name) {
                            var data_top = -1;
                            try {
                                data_top = $("#"+content_id+" a[name='" + name + "']").offset().top;
                            }catch(err){}
                            link.setAttribute('data-top', data_top);
                            if (anchor.length > 0 && anchor == `#${name}`) {
                                link.setAttribute('class', 'ey_toc_selected');
                            }
                            link.href = `#${name}`;
                            link.textContent = name;
                            tocItem.appendChild(link);
                            tocItem.setAttribute('class', `ey_toc_h${level}`);
                            tocItem.style.paddingLeft = ((level - 2) * 1) + 'em';
                            tocList.appendChild(tocItem);
                            // 显示目录大纲
                            is_show_toc = true;
                        }
                    });
                    if (is_show_toc) {
                        // 将目录列表添加到容器中
                        tocContainer.appendChild(tocList);
                    }
                }
            }
            if (is_show_toc) {
                tocContainer.style.display = "block";

                // 自动绑定点击滑动事件
                if (window.jQuery) {
                    if (!scrollTop) scrollTop = 'unbind';
                    if ('unbind' != scrollTop) {
                        $('#'+toc_id+' ul li').on('click', function(){
                            var aObj = $(this).find('a');
                            var name = aObj.attr('data-name');
                            if (!name) {
                                name = aObj.attr('href');
                                name = name.replace('#', '');
                                aObj.attr('data-name', name);
                            }
                            // aObj.attr('href', 'javascript:void(0);');
                            aObj.attr('data-name', name);
                            $('#'+toc_id+' ul li').find('a').removeClass('ey_toc_selected');
                            aObj.addClass('ey_toc_selected');
                            var contentObj = $("#"+content_id+" a[name='" + name + "']");
                            if (0 < contentObj.length) {
                                var data_top = aObj.attr('data-top');
                                if (data_top <= -1) {
                                    data_top = contentObj.offset().top;
                                }
                                $("html,body").animate({
                                    scrollTop: data_top - scrollTop
                                })
                            }
                        });

                        // 刷新页面自动定位到锚点位置
                        setTimeout(function(){
                            $('#'+toc_id+' ul li').find('a.ey_toc_selected').click();
                        }, 300);
                    }
                }
            }
        }
    }, 10);
}

/**
 * 设置cookie
 * @param {[type]} name  [description]
 * @param {[type]} value [description]
 * @param {[type]} time  [description]
 */
function ey_setCookies(name, value, time)
{
    var cookieString = name + "=" + escape(value) + ";";
    if (time != 0) {
        var Times = new Date();
        Times.setTime(Times.getTime() + time);
        cookieString += "expires="+Times.toGMTString()+";"
    }
    document.cookie = cookieString+"path=/";
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

function ey_getCookie(c_name)
{
    return getCookie(c_name);
}

function getQueryString(name) {
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null) return unescape(r[2]);
    return null;
}

/*------------------外贸助手的JS多语言变量 start------------------*/
/**
 * 外贸助手的JS文件的多语言包
 */
function ey_foreign(string, ...args) {
    return string.replace(/%([a-zA-Z0-9]{1,1})/g, function() {
        return args.shift();
    });
}
var ey_foreign_page1 = "首页";
var ey_foreign_page2 = "上一页";
var ey_foreign_page3 = "下一页";
var ey_foreign_page4 = "末页";
var ey_foreign_page5 = "共<strong>%s</strong>页 <strong>%s</strong>条";
var ey_foreign_page6 = "第%s页";
var ey_foreign_gbook1 = "操作成功";
var ey_foreign_gbook2 = "同一个IP在%s秒之内不能重复提交！";
var ey_foreign_gbook3 = "%s不能为空！";
var ey_foreign_gbook4 = "%s格式不正确！";
var ey_foreign_gbook5 = "图片验证码不能为空！";
var ey_foreign_gbook6 = "图片验证码不正确！";
var ey_foreign_gbook7 = "请输入手机号码！";
var ey_foreign_gbook8 = "手机号码和手机验证码不一致，请重新输入！";
var ey_foreign_gbook9 = "手机验证码已被使用或超时，请重新发送！";
var ey_foreign_gbook10 = "请输入手机验证码！";
var ey_foreign_gbook11 = "表单缺少标签属性{$field.hidden}";
var ey_foreign_gbook12 = "页面自动 %s跳转%s 等待时间：";
var ey_foreign_gbook13 = "%s至少选择一项！";
var ey_foreign_gbook14 = "请选择%s";
var ey_foreign_gbook15 = "请输入正确的手机号码！";
var ey_foreign_gbook16 = "图片验证码";
var ey_foreign_gbook17 = "手机验证码";
var ey_foreign_gbook18 = "获取验证码";
var ey_foreign_gbook19 = "看不清？点击更换验证码";
var ey_foreign_gbook20 = "看不清？%s点击更换%s";
var ey_foreign_gbook21 = "请输入邮箱地址！";
var ey_foreign_gbook22 = "请输入邮箱验证码！";
var ey_foreign_gbook23 = "请输入正确的邮箱地址！";
var ey_foreign_gbook24 = "邮箱验证码不正确，请重新输入！";
var ey_foreign_system1 = "图";
var ey_foreign_system2 = "确定";
var ey_foreign_system3 = "取消";
var ey_foreign_system4 = "提示";
var ey_foreign_system5 = "是";
var ey_foreign_system6 = "否";
var ey_foreign_system7 = "请至少选择一项！";
var ey_foreign_system8 = "正在处理";
var ey_foreign_system9 = "请勿刷新页面";
var ey_foreign_system10 = "上传成功";
var ey_foreign_system11 = "操作失败";
var ey_foreign_system12 = "操作成功";
var ey_foreign_system13 = "含有敏感词（%s），禁止搜索！";
var ey_foreign_system14 = "过度频繁搜索，离解禁还有%s分钟！";
var ey_foreign_system15 = "关键词不能为空！";
var ey_foreign_users1 = "您的购物车还没有商品！";
var ey_foreign_users2 = "%s不能为空！";
var ey_foreign_users3 = "%s格式不正确！";
var ey_foreign_users4 = "邮箱验证码已被使用或超时，请重新发送！";
var ey_foreign_users5 = "邮箱验证码不正确，请重新输入！";
var ey_foreign_users6 = "短信验证码不正确，请重新输入！";
var ey_foreign_users7 = "%s已存在！";
var ey_foreign_users8 = "签到成功";
var ey_foreign_users9 = "今日已签过到";
var ey_foreign_users10 = "是否删除该收藏？";
var ey_foreign_users11 = "确认批量删除收藏？";
var ey_foreign_users12 = "每日签到";
var ey_foreign_users13 = "充值金额不能为空！";
var ey_foreign_users14 = "请输入正确的充值金额！";
var ey_foreign_users15 = "请选择支付方式！";
var ey_foreign_users16 = "用户名不能为空！";
var ey_foreign_users17 = "用户名不正确！";
var ey_foreign_users18 = "密码不能为空！";
var ey_foreign_users19 = "图片验证码不能为空！";
var ey_foreign_users20 = "图片验证码错误";
var ey_foreign_users21 = "前台禁止管理员登录！";
var ey_foreign_users22 = "该会员尚未激活，请联系管理员！";
var ey_foreign_users23 = "管理员审核中，请稍等！";
var ey_foreign_users24 = "登录成功";
var ey_foreign_users25 = "密码不正确！";
var ey_foreign_users26 = "该用户名不存在，请注册！";
var ey_foreign_users27 = "看不清？点击更换验证码";
var ey_foreign_users28 = "手机号码不能为空！";
var ey_foreign_users29 = "手机号码格式不正确！";
var ey_foreign_users30 = "手机验证码不能为空！";
var ey_foreign_users31 = "手机验证码已失效！";
var ey_foreign_users32 = "手机号码已经注册！";
var ey_foreign_users33 = "用户名为系统禁止注册！";
var ey_foreign_users34 = "请输入2-30位的汉字、英文、数字、下划线等组合";
var ey_foreign_users35 = "登录密码不能为空！";
var ey_foreign_users36 = "重复密码不能为空！";
var ey_foreign_users37 = "用户名已存在";
var ey_foreign_users38 = "两次密码输入不一致！";
var ey_foreign_users39 = "注册成功，正在跳转中……";
var ey_foreign_users40 = "注册成功，等管理员激活才能登录！";
var ey_foreign_users41 = "注册成功，请登录！";
var ey_foreign_users42 = "昵称不可为纯空格";
var ey_foreign_users43 = "原密码不能为空！";
var ey_foreign_users44 = "新密码不能为空！";
var ey_foreign_users45 = "手机号码不存在，不能找回密码！";
var ey_foreign_users46 = "手机号码未绑定，不能找回密码！";
var ey_foreign_users47 = "手机验证码已被使用或超时，请重新发送！";
var ey_foreign_users48 = "晚上好～";
var ey_foreign_users49 = "早上好～";
var ey_foreign_users50 = "下午好～";
var ey_foreign_users51 = "商品库存仅%s件！";
var ey_foreign_users52 = "商品数量最少为%s";
/*------------------外贸助手的JS多语言变量 end------------------*/