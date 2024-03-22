
var oldhtml = ''; // 原始内容
var epageJson = {}; // 页面标识，建议是文件名
var backgroundColor = '';

jQuery(function($){
 
    // 去除所有A标签链接
    // function remove_a_href()
    // {
    //     $('a').each(function(index, item){
    //         $(item).attr('href', 'javascript:void(0);');
    //     });    
    // }

    // 去除所有A标签链接
    removeAHref();
    setTimeout(function(){
        removeAHref();
    },3000);

    /**
     * 页面右上角显示还原数据的操作
     */
    $('body').prepend('<div e-id="clearall" class="uiset_back-btn" title="初始化设置" onclick="eyou_clear();"></div>');

    /**
     * Make the elements editable
     */
    $('.eyou-edit').mouseenter(function(e){ // 鼠标移入选中状态，只针对该绑定元素
        e.stopPropagation();
        var that = this;
        eyou_mouseenter(that);
    })
    .mouseleave(function(e){ // 鼠标移出消除选中状态，只针对该绑定元素
        e.stopPropagation();
        var that = this;
        eyou_mouseleave(that);
    });

    // 鼠标移入选中状态，只针对该绑定元素
    function eyou_mouseenter(that)
    {
        var e_type = $(that).attr('e-type');
        $(that).addClass('uiset');
        $('body').find('b.ui_icon').remove();
        if (-1 < $.inArray(e_type, ['map','upload'])) {
            $('body').find('div.ui_zhezhaoceng').remove();
            $(that).prepend('<b class="ui_icon"></b><div class="ui_zhezhaoceng"></div>');
        } else {
            $(that).prepend('<b class="ui_icon"></b>');
            backgroundColor = $(that).find('b.ui_icon').parent().css('background-color');
            $(that).find('b.ui_icon').parent().css('background-color', 'rgba(0, 0, 0, 0.2)');
        }
        $(that).find('b.ui_icon').on("click", function(e){
            e.stopPropagation();
            var that = $(this).parent();
            if (e_type == 'text') {
                oldhtml = $(that).html();
                eyou_text(that);
            } else if (e_type == 'html') {
                oldhtml = $(that).html();
                oldhtml = oldhtml.replace('<b class="ui_icon"></b>', '');
                eyou_html(that);
            } else if (e_type == 'type') {
                eyou_type(that);
            } else if (e_type == 'arclist' || e_type == 'artlist') {
                eyou_arclist(that);
            } else if (e_type == 'channel' || e_type == 'models') {
                eyou_channel(that);
            } else if (e_type == 'upload') {
                eyou_upload(that);
            } else if (e_type == 'adv') {
                eyou_adv(that);
            } else if (e_type == 'map') {
                eyou_map(that);
            } else if (e_type == 'code') {
                oldhtml = $(that).html();
                oldhtml = oldhtml.replace('<b class="ui_icon"></b>', '');
                eyou_code(that);
            } else if (e_type == 'background') {
                eyou_background(that);
            }
            // eyou_mouseleave(that);
        });
        if (that.nodeName == 'A') {
            $(that).attr('href', 'javascript:void(0);');
        }
    }

    // 鼠标移出消除选中状态，只针对该绑定元素
    function eyou_mouseleave(that)
    {
        $(that).removeClass('uiset');
        var e_type = $(that).attr('e-type');
        if (-1 < $.inArray(e_type, ['map','upload'])) {
            $(that).find('div.ui_zhezhaoceng').remove();
            $(that).find('b.ui_icon').remove();
        } else {
            $(that).find('b.ui_icon').parent().css('background-color', backgroundColor);
            $(that).find('b.ui_icon').remove();
        }
        $(that).bind('mouseenter');
    }

    // 递归获取最近含有e-page的元素对象
    function get_epage(obj)
    {
        if ($(obj).attr('e-page') == undefined) {
            var parentObj = $(obj).parent();
            if (parentObj.find('body').length > 0) {
                epageJson = {
                    e_page: ''
                };
                return false;
            } else {
                get_epage(parentObj);
            }
        } else {
            epageJson = {
                e_page: $(obj).attr('e-page')
            };
            return false;
        }
    }

    // 纯文本编辑
    function eyou_text(that)
    {
        get_epage(that);
        var e_page = epageJson.e_page;
        var e_id = $(that).attr('e-id');
        if (e_page == '' || e_id == undefined) {
            eyou_showErrorAlert('html报错：uitext标签的外层html元素缺少属性 e-page | e-id');
            return false;
        }

        parent.eyou_text(that, e_id, e_page, oldhtml);
    }

    // 带html的富文本编辑器
    function eyou_html(that)
    {
        get_epage(that);
        var e_page = epageJson.e_page;
        var e_id = $(that).attr('e-id');
        if (e_page == '' || e_id == undefined) {
            eyou_showErrorAlert('html报错：uihtml标签的外层html元素缺少属性 e-page | e-id');
            return false;
        }

        parent.eyou_html(that, e_id, e_page, oldhtml);
    }

    // 栏目编辑
    function eyou_type(that)
    {
        get_epage(that);
        var e_page = epageJson.e_page;
        var e_id = $(that).attr('e-id');
        if (e_page == '' || e_id == undefined) {
            eyou_showErrorAlert('html报错：uitype标签的外层html元素缺少属性 e-page | e-id');
            return false;
        }
        
        parent.eyou_type(that, e_id, e_page);
    }

    // 文章栏目编辑
    function eyou_arclist(that)
    {
        get_epage(that);
        var e_page = epageJson.e_page;
        var e_id = $(that).attr('e-id');
        if (e_page == '' || e_id == undefined) {
            eyou_showErrorAlert('html报错：uiarclist标签的外层html元素缺少属性 e-page | e-id');
            return false;
        }
        
        parent.eyou_arclist(that, e_id, e_page);
    }

    // 栏目列表编辑
    function eyou_channel(that)
    {
        get_epage(that);
        var e_page = epageJson.e_page;
        var e_id = $(that).attr('e-id');
        if (e_page == '' || e_id == undefined) {
            eyou_showErrorAlert('html报错：uichannel标签的外层html元素缺少属性 e-page | e-id');
            return false;
        }
        
        parent.eyou_channel(that, e_id, e_page);
    }

    // 图片编辑
    function eyou_upload(that)
    {
        get_epage(that);
        var e_page = epageJson.e_page;
        var e_id = $(that).attr('e-id');
        if (e_page == '' || e_id == undefined) {
            eyou_showErrorAlert('html报错：uiupload标签的外层html元素缺少属性 e-page | e-id');
            return false;
        }
        var imgsrc = $(that).find('img').attr('src');
        var oldhtml = $.trim($(that).html());
        oldhtml = encodeURI(oldhtml);
        
        parent.eyou_upload(that, e_id, e_page, oldhtml, imgsrc);
    }

    // 背景图片编辑
    function eyou_background(that)
    {
        get_epage(that);
        var e_page = epageJson.e_page;
        var e_id = $(that).attr('e-id');
        if (e_page == '' || e_id == undefined) {
            eyou_showErrorAlert('html报错：uibackground标签的外层html元素缺少属性 e-page | e-id');
            return false;
        }
        var imgsrc = $(that).css("backgroundImage").replace('url(', '').replace(')', '');
        re = new RegExp("'","g");
        imgsrc = imgsrc.replace(re, "");
        re2 = new RegExp("\"","g");
        imgsrc = imgsrc.replace(re2, "");
        imgsrc = $.trim(imgsrc);
        
        parent.eyou_background(that, e_id, e_page, imgsrc);
    }
    
    // 广告设置
    function eyou_adv(that)
    {
        var e_id = $(that).attr('e-id');
        
        parent.eyou_adv(that, e_id);
    }

    // 百度地图
    function eyou_map(that)
    {
        get_epage(that);
        var e_page = epageJson.e_page;
        var e_id = $(that).attr('e-id');
        if (e_page == '' || e_id == undefined) {
            eyou_showErrorAlert('html报错：uimap标签的外层html元素缺少属性 e-page | e-id');
            return false;
        }
        
        parent.eyou_map(that, e_id, e_page);
    }

    // 源代码编辑
    function eyou_code(that)
    {
        get_epage(that);
        var e_page = epageJson.e_page;
        var e_id = $(that).attr('e-id');
        if (e_page == '' || e_id == undefined) {
            eyou_showErrorAlert('html报错：uicode标签的外层html元素缺少属性 e-page | e-id');
            return false;
        }
        
        parent.eyou_code(that, e_id, e_page, oldhtml);
    }
});

// 去除所有A标签链接
function removeAHref()
{
    $('a').mouseenter(function(e){ // 鼠标移入选中状态，只针对该绑定元素
        $(this).attr('data-ey_href', $(this).attr('href')).removeAttr('href');
        $(this).attr('data-ey_target', $(this).attr('target')).removeAttr('target');
    })
    .mouseleave(function(e){ // 鼠标移出消除选中状态，只针对该绑定元素
        $(this).attr('href', $(this).attr('data-ey_href')).removeAttr('data-ey_href');
        $(this).attr('target', $(this).attr('data-ey_target')).removeAttr('data-ey_target');
    });
}

/**
 * 获取修改之前的内容
 */
function eyou_getOldHtml()
{
    return oldhtml;
}

// 清除全部数据
function eyou_clear()
{
    parent.eyou_clear();
}

function eyou_showErrorMsg(msg){
    layer.msg(msg, {icon: 5,time: 2000});
}

function eyou_showSuccessMsg(msg){
    layer.msg(msg, {time: 1000});
}

function eyou_showErrorAlert(msg, icon){
    if (!icon && icon != 0) {
        icon = 5;
    }
    layer.alert(msg, {icon: icon, title: false, closeBtn: false});
}

/**
 * 封装的加载层
 */
function eyou_layer_loading(msg){
    var loading = layer.msg(
    msg+'...&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;请勿刷新页面', 
    {
        icon: 1,
        time: 3600000,
        shade: [0.2]
    });
    var index = layer.load(3, {
        shade: [0.1,'#fff']
    });

    return loading;
}

/**
 * 封装的加载层，用于iframe
 */
function eyou_iframe_layer_loading(msg){
    var loading = parent.layer.msg(
    msg+'...&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;请勿刷新页面', 
    {
        icon: 1,
        time: 3600000,
        shade: [0.2]
    });
    var index = parent.layer.load(3, {
        shade: [0.1,'#fff']
    });

    return loading;
}
