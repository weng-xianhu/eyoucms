
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

