
var oldhtml = ''; // 原始内容
var epageJson = {}; // 页面标识，建议是文件名

jQuery(function($){

    // 去除所有A标签链接
    // function remove_a_href()
    // {
    //     $('a').each(function(index, item){
    //         $(item).attr('href', 'javascript:void(0);');
    //     });    
    // }

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
        $(that).addClass('uiset');
        $('body').find('b.ui_icon').remove();
        $(that).prepend('<b class="ui_icon"></b>');
        $(that).find('b.ui_icon').on("click", function(e){
            e.stopPropagation();
            var that = $(this).parent();
            var e_type = $(that).attr('e-type');
            if (e_type == 'text') {
                oldhtml = $(that).html();
                eyou_text(that);
            } else if (e_type == 'html') {
                oldhtml = $(that).html();
                eyou_html(that);
            } else if (e_type == 'type') {
                eyou_type(that);
            } else if (e_type == 'arclist') {
                eyou_arclist(that);
            } else if (e_type == 'channel') {
                eyou_channel(that);
            } else if (e_type == 'upload') {
                eyou_upload(that);
            } else if (e_type == 'adv') {
                eyou_adv(that);
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
        $(that).find('b.ui_icon').remove();
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
            layer.alert('html报错：uitext标签的外层html元素缺少属性 e-page | e-id');
            return false;
        }
        var textval = $(that).html();
        //textval = textval.replace(/[\r\n]/g, "");//去掉回车换行)
        textval = textval.replace(/<b class="ui_icon"><\/b>/g, "");//去掉回车换行)
        textval = $.trim(textval);
        layer.prompt({
            title: '纯文本编辑',
            value: textval,
            formType: 2,
            area: ['500px', '300px']
        }, function(text, index){
            layer.close(index);
            text = text.replace(/[\r\n]/g, "");//去掉回车换行)
            text = text.replace(/<b class="ui_icon"><\/b>/g, "");//去掉回车换行)
            text = $.trim(text);
            if( $.trim(text) != '' ) {
                eyou_layer_loading('正在处理');
                $.ajax({
                    url: root_dir+'/index.php?m=api&c=Uiset&a=submit'+'&v='+v+'&lang='+lang,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        content: text
                        ,id: e_id
                        ,page: e_page
                        ,type: 'text'
                        ,oldhtml: oldhtml
                        ,lang: lang
                    },
                    success: function(res) {
                        layer.closeAll();
                        if (res.code == 1) {
                            layer.msg(res.msg, {shade: 0.3, time: 1000}, function(){
                                window.location.reload();
                            });
                        } else {
                            layer.alert(res.msg, {icon:5});
                        }
                        return false;
                    },
                    error: function(e){
                        layer.closeAll();
                        layer.alert('操作失败', {icon:5});
                        return false;
                    }
                });
            }
        });
    }

    // 带html的富文本编辑器
    function eyou_html(that)
    {
        get_epage(that);
        var e_page = epageJson.e_page;
        var e_id = $(that).attr('e-id');
        if (e_page == '' || e_id == undefined) {
            layer.alert('html报错：uihtml标签的外层html元素缺少属性 e-page | e-id');
            return false;
        }
        //iframe窗
        layer.open({
            type: 2,
            title: '富文本内容编辑',
            fixed: true, //不固定
            shadeClose: false,
            shade: 0.3,
            maxmin: true, //开启最大化最小化按钮
            area: ['700px', '550px'],
            content: root_dir+'/index.php?m=api&c=Uiset&a=html&id='+e_id+'&page='+e_page+'&v='+v+'&lang='+lang
        });
        // console.log(a)
    }

    // 栏目编辑
    function eyou_type(that)
    {
        get_epage(that);
        var e_page = epageJson.e_page;
        var e_id = $(that).attr('e-id');
        if (e_page == '' || e_id == undefined) {
            layer.alert('html报错：uitype标签的外层html元素缺少属性 e-page | e-id');
            return false;
        }
        //iframe窗
        layer.open({
            type: 2,
            title: '栏目编辑',
            fixed: true, //不固定
            shadeClose: false,
            shade: 0.3,
            maxmin: false, //开启最大化最小化按钮
            area: ['350px', '200px'],
            content: root_dir+'/index.php?m=api&c=Uiset&a=type&id='+e_id+'&page='+e_page+'&v='+v+'&lang='+lang
        });
        // console.log(a)
    }

    // 文章栏目编辑
    function eyou_arclist(that)
    {
        get_epage(that);
        var e_page = epageJson.e_page;
        var e_id = $(that).attr('e-id');
        if (e_page == '' || e_id == undefined) {
            layer.alert('html报错：uiarclist标签的外层html元素缺少属性 e-page | e-id');
            return false;
        }
        //iframe窗
        layer.open({
            type: 2,
            title: '内容栏目编辑',
            fixed: true, //不固定
            shadeClose: false,
            shade: 0.3,
            maxmin: false, //开启最大化最小化按钮
            area: ['350px', '200px'],
            content: root_dir+'/index.php?m=api&c=Uiset&a=arclist&id='+e_id+'&page='+e_page+'&v='+v+'&lang='+lang
        });
        // console.log(a)
    }

    // 栏目列表编辑
    function eyou_channel(that)
    {
        get_epage(that);
        var e_page = epageJson.e_page;
        var e_id = $(that).attr('e-id');
        if (e_page == '' || e_id == undefined) {
            layer.alert('html报错：uichannel标签的外层html元素缺少属性 e-page | e-id');
            return false;
        }
        //iframe窗
        layer.open({
            type: 2,
            title: '栏目列表编辑',
            fixed: true, //不固定
            shadeClose: false,
            shade: 0.3,
            maxmin: false, //开启最大化最小化按钮
            area: ['350px', '200px'],
            content: root_dir+'/index.php?m=api&c=Uiset&a=channel&id='+e_id+'&page='+e_page+'&v='+v+'&lang='+lang
        });
        // console.log(a)
    }

    // 图片编辑
    function eyou_upload(that)
    {
        get_epage(that);
        var e_page = epageJson.e_page;
        var e_id = $(that).attr('e-id');
        if (e_page == '' || e_id == undefined) {
            layer.alert('html报错：uiupload标签的外层html元素缺少属性 e-page | e-id');
            return false;
        }
        var imgsrc = $(that).find('img').attr('src');
        var oldhtml = $.trim($(that).html());
        oldhtml = encodeURI(oldhtml);
        //iframe窗
        layer.open({
            type: 2,
            title: '图片编辑',
            fixed: true, //不固定
            shadeClose: false,
            shade: 0.3,
            maxmin: false, //开启最大化最小化按钮
            area: ['400px', '280px'],
            content: root_dir+'/index.php?m=api&c=Uiset&a=upload&id='+e_id+'&page='+e_page+'&v='+v+'&lang='+lang,
            success: function(layero, index){
                // layer.iframeAuto(index);
                var body = layer.getChildFrame('body', index);
                body.find('input[name=oldhtml]').val(oldhtml);
                body.find('a.imgsrc').attr('href',imgsrc);
                body.find('a.imgsrc img').attr('src',imgsrc);
                // var iframeWin = window[layero.find('iframe')[0]['name']]; //得到iframe页的窗口对象，执行iframe页的方法：iframeWin.method();
                // console.log(body.html()) //得到iframe页的body内容
            }
        });
        // console.log(a)
    }
    
    // 广告设置
    function eyou_adv(that)
    {
        var e_id = $(that).attr('e-id');
        var url = admin_basefile+'?m='+admin_module_name+'&c=Other&a=ui_edit&id='+e_id+'&v='+v+'&lang='+lang;
        //iframe窗
        layer.open({
            type: 2,
            title: '广告编辑',
            fixed: true, //不固定
            shadeClose: false,
            shade: 0.3,
            maxmin: true, //开启最大化最小化按钮
            area: ['800px', '500px'],
            content: url
        });
        // console.log(a)
    }
});

/**
 * 获取修改之前的内容
 */
function eyou_getOldHtml()
{
    return oldhtml;
}

function eyou_showErrorMsg(msg){
    layer.msg(msg, {icon: 5,time: 2000});
}

function eyou_showSuccessMsg(msg){
    layer.msg(msg, {time: 1000});
}

/**
 * 封装的加载层
 */
function eyou_layer_loading(msg){
    var loading = layer.msg(
    msg+'...&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;请勿刷新页面', 
    {
        icon: 1,
        time: 3600000, //1小时后后自动关闭
        shade: [0.2] //0.1透明度的白色背景
    });
    //loading层
    var index = layer.load(3, {
        shade: [0.1,'#fff'] //0.1透明度的白色背景
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
        time: 3600000, //1小时后后自动关闭
        shade: [0.2] //0.1透明度的白色背景
    });
    //loading层
    var index = parent.layer.load(3, {
        shade: [0.1,'#fff'] //0.1透明度的白色背景
    });

    return loading;
}
