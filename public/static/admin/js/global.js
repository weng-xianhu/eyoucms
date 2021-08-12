
//工具栏上的所有的功能按钮和下拉框，可以在new编辑器的实例时选择自己需要的重新定义
window.UEDITOR_HOME_URL = __root_dir__+"/public/plugins/Ueditor/";
var ueditor_toolbars = [[
    'fullscreen', 'source', '|', 'undo', 'redo', '|',
    'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', '|',
    'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
    'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
    'directionalityltr', 'directionalityrtl', 'indent', '|',
    'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
    'link', 'unlink', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
    'simpleupload', 'insertimage', 'emotion', 'insertvideo', 'attachment', 'map', 'insertframe', 'insertcode', '|',
    'horizontal', 'spechars', '|',
    'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
    'preview', 'searchreplace', 'drafts'
]];

var layer_tips; // 全局提示框的对象
var ey_unknown_error = '未知错误，无法继续！';

$(function(){
    auto_notic_tips();
    /**
     * 自动小提示
     */
    function auto_notic_tips()
    {
        var html = '<a class="ui_tips" href="javascript:void(0);" onmouseover="layer_tips = layer.tips($(this).parent().find(\'p.notic\').html(), this, {time:100000});" onmouseout="layer.close(layer_tips);">提示</a>';
        $.each($('dd.opt > p.notic'), function(index, item){
            if ($(item).html() != '') {
                $(item).before(html);
            }
        });
    }

    /*TAG标签选中与取消处理*/
    $('.TagIndex1591690114').click(function() {
        if ($(this).html() && $(this).data('id')) {
            /*读取原有数据*/
            var id  = $(this).data('id');
            var tag = $(this).html();
            var tagOldSelectID  = $('#TagOldSelectID').val();
            var tagOldSelectTag = $('#TagOldSelectTag').val();
            /*END*/
            if (tagOldSelectID) {
                /*处理原有数据*/
                var tagOldSelectNew = tagOldSelectID.split(',');
                var index = $.inArray(String(id), tagOldSelectNew);
                if (index == -1) {
                    /*追加新数据*/
                    tagOldSelectID  += ',' + id;
                    tagOldSelectTag += ',' + tag;
                    /*END*/
                } else {
                    /*删除原有数据*/
                    tagOldSelectNew.splice(index, 1);
                    tagOldSelectID  = tagOldSelectNew.join(',');
                    tagOldSelectTag = tagOldSelectTag.split(',');
                    tagOldSelectTag.splice(index, 1);
                    tagOldSelectTag = tagOldSelectTag.join(',');
                    /*END*/
                }
                /*END*/
            } else {
                /*追加新数据*/
                tagOldSelectID  = id;
                tagOldSelectTag = tag;
                /*END*/
            }
            $('#TagOldSelectID, #NewTagOldSelectID').val(tagOldSelectID);
            $('#TagOldSelectTag, #NewTagOldSelectTag, #tags').val(tagOldSelectTag);
        }
    });
    /*END*/
});
 
/*TAG标签代码*/
/*打开ATG快捷选择列表*/
function TagListSelect1591784354(obj) {
    var url = eyou_basefile + "?m="+module_name+"&c=Tags&a=tag_list&lang=" + __lang__;
    //iframe窗
    layer.open({
        type: 2,
        title: 'TAG标签选择',
        fixed: true, //不固定
        shadeClose: false,
        shade: 0.3,
        maxmin: false, //开启最大化最小化按钮
        area: ['500px', '400px'],
        content: url,
        cancel: function () {
            $('#TagOldSelectID').val($('#NewTagOldSelectID').val());
            $('#TagOldSelectTag').val($('#NewTagOldSelectTag').val());
        }
    });
}

/*通过TAGID删除对应数据*/
function UseTagIDDel1591784354(obj) {
    // 获取已选中的tag标签ID
    var tagOldSelectID = $('#TagOldSelectID').val();
    // 获取已选中的tag标签Tag
    var tagOldSelectTag = $('#TagOldSelectTag').val();
    // 获取当前点击的tag标签ID
    var id = $(obj).attr('data-id');
    if (tagOldSelectID) {
        // 将字符串转成数组，判断tag标签ID是否已存在
        var tagOldSelectID = tagOldSelectID.split(',');
        // 将字符串转成数组，判断tag标签ID是否已存在
        var tagOldSelectTag = tagOldSelectTag.split(',');
        // 是否存在，存在则返回下标
        var index = $.inArray(String(id), tagOldSelectID);
        // 若存在则执行
        if (index != -1) {
            // 删除指定tag的ID
            tagOldSelectID.splice(index, 1);
            // 将数组转成字符串
            tagOldSelectID = tagOldSelectID.join(',');
            // 赋值给已选中的tag标签ID隐藏域
            $('#TagOldSelectID, #NewTagOldSelectID').val(tagOldSelectID);

            // 删除指定tag的名称
            tagOldSelectTag.splice(index, 1);
            // 将数组转成字符串
            tagOldSelectTag = tagOldSelectTag.join(',');
            // 赋值给已选中的tag标签名称隐藏域
            $('#tags').show().val(tagOldSelectTag).hide();
            $('#TagOldSelectTag, #NewTagOldSelectTag').val(tagOldSelectTag);
        }
    }
    // 删除自身
    $(obj).parent().remove();
}
/*END*/

/*通过TAG名称删除对应数据*/
function UseTagNameDel1591784354(obj) {
    //区分是文章的TAG标签还是网站web_keywords
    var web_keywords_1607062084 = $('#web_keywords_1607062084').val();
    if ('web_keywords' == web_keywords_1607062084) {
        // 获取当前点击的关键词
        var words = $(obj).val();
        // 获取已填写的关键词字符串
        var web_keywords = $('#web_keywords').val();
        // 将字符串转成数组，判断当前点击的关键词是否已存在
        var arr = web_keywords.split(',');
        // 是否存在，存在则返回下标
        var index = $.inArray(String(words), arr);
        if (index != -1) {
            // 删除指定下标的元素
            arr.splice(index, 1);
            // 将数组转成字符串
            web_keywords = arr.join(',');
            // 赋值给已选中的关键词输入框
            $('#web_keywords').val(web_keywords);
        }
    } else {
        // 获取已选中的tag标签ID
        var tagOldSelectID = $('#TagOldSelectID').val();
        // 获取已选中的tag标签Tag
        var tagOldSelectTag = $('#TagOldSelectTag').val();
        // 获取当前点击的tag标签Tag
        var Tag = $(obj).val();
        if (tagOldSelectID && tagOldSelectTag) {
            // 将字符串转成数组，判断tag标签ID是否已存在
            var tagOldSelectID = tagOldSelectID.split(',');
            // 将字符串转成数组，判断tag标签ID是否已存在
            var tagOldSelectTag = tagOldSelectTag.split(',');
            // 是否存在，存在则返回下标
            var index = $.inArray(String(Tag), tagOldSelectTag);
            // 若存在则执行
            if (index != -1) {
                // 删除指定tag的ID
                tagOldSelectID.splice(index, 1);
                // 将数组转成字符串
                tagOldSelectID = tagOldSelectID.join(',');
                // 赋值给已选中的tag标签ID隐藏域
                $('#TagOldSelectID, #NewTagOldSelectID').val(tagOldSelectID);
                // 删除指定tag的名称
                tagOldSelectTag.splice(index, 1);
                // 将数组转成字符串
                tagOldSelectTag = tagOldSelectTag.join(',');
                // 赋值给已选中的tag标签名称隐藏域
                $('#tags').show().val(tagOldSelectTag).hide();
                $('#TagOldSelectTag, #NewTagOldSelectTag').val(tagOldSelectTag);
            }
        }
    }
    // 删除自身
    $(obj).parent().remove();
}
/*END*/

/**
 * 批量复制
 */
function func_batch_copy(obj, name)
{
    var a = [];
    var k = 0;
    aids = '';
    $('input[name^='+name+']').each(function(i,o){
        if($(o).is(':checked')){
            a.push($(o).val());
            if (k > 0) {
                aids += ',';
            }
            aids += $(o).val();
            k++;
        }
    })
    if(a.length == 0){
        showErrorAlert('请至少选择一项！');
        return;
    }

    var url = $(obj).attr('data-url');
    //iframe窗
    layer.open({
        type: 2,
        title: '批量复制',
        fixed: true, //不固定
        shadeClose: false,
        shade: 0.3,
        maxmin: false, //开启最大化最小化按钮
        area: ['450px', '300px'],
        content: url
    });
}

/**
 * 批量删除提交
 */
function batch_del(obj, name) {

    var url = $(obj).attr('data-url');

    var a = [];
    $('input[name^='+name+']').each(function(i,o){
        if($(o).is(':checked')){
            a.push($(o).val());
        }
    })
    if(a.length == 0){
        showErrorAlert('请至少选择一项！');
        return;
    }

    var deltype = $(obj).attr('data-deltype');
    if ('pseudo' == deltype) {
        batch_del_pseudo(obj, a);
    } else {
        title = '此操作不可恢复，确定批量删除？';
        btn = ['确定', '取消']; //按钮
        // 删除按钮
        layer.confirm(title, {
            title: false,
            btn: btn //按钮
        }, function () {
            layer_loading('正在处理');
            $.ajax({
                type: "POST",
                url: url,
                data: {del_id:a, thorough:1,_ajax:1},
                dataType: 'json',
                success: function (data) {
                    layer.closeAll();
                    if(data.code == 1){
                        layer.msg(data.msg, {icon: 1});
                        //window.location.reload();
                
                        /* 生成静态页面代码 */
                        var slice_start = url.indexOf('m=admin&c=');
                        slice_start = parseInt(slice_start) + 10;
                        var slice_end = url.indexOf('&a=');
                        var ctl_name = url.slice(slice_start,slice_end);
                        $.ajax({
                            url:__root_dir__+"/index.php?m=home&c=Buildhtml&a=upHtml&lang="+__lang__,
                            type:'POST',
                            dataType:'json',
                            data: {del_ids:a,ctl_name:ctl_name,_ajax:1},
                            success:function(data){
                                window.location.reload();
                            },
                            error: function(){
                                window.location.reload();
                            }
                        });
                        /* end */
                                
                        // layer.alert(data.msg, {
                        //     icon: 1,
                        //     closeBtn: 0
                        // }, function(){
                        //     window.location.reload();
                        // });
                    }else{
                        showErrorAlert(data.msg);
                    }
                },
                error:function(e){
                    layer.closeAll();
                    showErrorAlert(e.responseText);
                }
            });
        }, function (index) {
            layer.closeAll(index);
        });
    }
}

/**
 * 批量删除-针对临时存放在回收站的数据
 */
function batch_del_pseudo(obj, a) {

    var url = $(obj).attr('data-url');

    // 删除按钮
    layer.confirm('将批量删除文档，请选择操作？', {
        title: false,
        btn: ['彻底删除', '放入回收站'] //按钮
    }, function () {
        layer_loading('正在处理');
        $.ajax({
            type: "POST",
            url: url,
            data: {del_id:a, thorough:1, _ajax:1},
            dataType: 'json',
            success: function (data) {
                layer.closeAll();
                if(data.code == 1){
                    layer.msg(data.msg, {icon: 1});
            
                    /* 生成静态页面代码 */
                    var slice_start = url.indexOf('m=admin&c=');
                    slice_start = parseInt(slice_start) + 10;
                    var slice_end = url.indexOf('&a=');
                    var ctl_name = url.slice(slice_start,slice_end);
                    $.ajax({
                        url:__root_dir__+"/index.php?m=home&c=Buildhtml&a=upHtml&lang="+__lang__,
                        type:'POST',
                        dataType:'json',
                        data: {del_ids:a,ctl_name:ctl_name,_ajax:1},
                        success:function(data){
                            window.location.reload();
                        },
                        error: function(){
                            window.location.reload();
                        }
                    });
                    /* end */
                }else{
                    showErrorAlert(data.msg);
                }
            },
            error:function(e){
                layer.closeAll();
                showErrorAlert(e.responseText);
            }
        });
    }, function (index) {
        layer_loading('正在处理');
        $.ajax({
            type: "POST",
            url: url,
            data: {del_id:a, _ajax:1},
            dataType: 'json',
            success: function (data) {
                layer.closeAll();
                if(data.code == 1){
                    layer.msg(data.msg, {icon: 1});
            
                    /* 生成静态页面代码 */
                    var slice_start = url.indexOf('m=admin&c=');
                    slice_start = parseInt(slice_start) + 10;
                    var slice_end = url.indexOf('&a=');
                    var ctl_name = url.slice(slice_start,slice_end);
                    $.ajax({
                        url:__root_dir__+"/index.php?m=home&c=Buildhtml&a=upHtml&lang="+__lang__,
                        type:'POST',
                        dataType:'json',
                        data: {del_ids:a,ctl_name:ctl_name,_ajax:1},
                        success:function(data){
                            window.location.reload();
                        },
                        error: function(){
                            window.location.reload();
                        }
                    });
                    /* end */
                }else{
                    showErrorAlert(data.msg);
                }
            },
            error:function(e){
                layer.closeAll();
                showErrorAlert(e.responseText);
            }
        });
    });
}

/**
 * 单个删除
 */
function delfun(obj) {

    var url = $(obj).attr('data-url');
    var deltype = $(obj).attr('data-deltype');
    if ('pseudo' == deltype) {
        delfun_pseudo(obj);
    } else {
        title = '此操作不可恢复，确定删除？';
        btn = ['确定', '取消']; //按钮
        layer.confirm(title, {
                title: false,
                btn: btn //按钮
            }, function(){
                // 确定
                layer_loading('正在处理');
                $.ajax({
                    type : 'POST',
                    url : url,
                    data : {del_id:$(obj).attr('data-id'),thorough:1, _ajax:1},
                    dataType : 'json',
                    success : function(data){
                        layer.closeAll();
                        if(data.code == 1){
                            layer.msg(data.msg, {icon: 1});
                            //window.location.reload();

                            /* 生成静态页面代码 */
                            var slice_start = url.indexOf('m=admin&c=');
                            slice_start = parseInt(slice_start) + 10;
                            var slice_end = url.indexOf('&a=');
                            var ctl_name = url.slice(slice_start,slice_end);
                            $.ajax({
                                url:__root_dir__+"/index.php?m=home&c=Buildhtml&a=upHtml&lang="+__lang__,
                                type:'POST',
                                dataType:'json',
                                data: {del_ids:$(obj).attr('data-id'),ctl_name:ctl_name,_ajax:1},
                                success:function(data){
                                     window.location.reload();
                                },
                                error: function(){
                                    window.location.reload();
                                }
                            });
                            /* end */
                        }else{
                            showErrorAlert(data.msg);
                        }
                    },
                    error:function(e){
                        layer.closeAll();
                        showErrorAlert(e.responseText);
                    }
                })
            }, function(index){
                layer.close(index);
                return false;// 取消
            }
        );
    }
}

/**
 * 单个删除-针对临时存放在回收站的数据
 */
function delfun_pseudo(obj) {

    var url = $(obj).attr('data-url');

    layer.confirm('将删除该文档，请选择操作？', {
            title: false,
            btn: ['彻底删除', '放入回收站'] //按钮
        }, function(){
            // 直接删除
            layer_loading('正在处理');
            $.ajax({
                type : 'POST',
                url : url,
                data : {del_id:$(obj).attr('data-id'), thorough:1, _ajax:1},
                dataType : 'json',
                success : function(data){
                    layer.closeAll();
                    if(data.code == 1){
                        layer.msg(data.msg, {icon: 1});

                        /* 生成静态页面代码 */
                        var slice_start = url.indexOf('m=admin&c=');
                        slice_start = parseInt(slice_start) + 10;
                        var slice_end = url.indexOf('&a=');
                        var ctl_name = url.slice(slice_start,slice_end);
                        $.ajax({
                            url:__root_dir__+"/index.php?m=home&c=Buildhtml&a=upHtml&lang="+__lang__,
                            type:'POST',
                            dataType:'json',
                            data: {del_ids:$(obj).attr('data-id'),ctl_name:ctl_name,_ajax:1},
                            success:function(data){
                                 window.location.reload();
                            },
                            error: function(){
                                window.location.reload();
                            }
                        });
                        /* end */
                    }else{
                        showErrorAlert(data.msg);
                    }
                },
                error:function(e){
                    layer.closeAll();
                    showErrorAlert(e.responseText);
                }
            })

        }, function(index){
            // 确定
            layer_loading('正在处理');
            $.ajax({
                type : 'POST',
                url : url,
                data : {del_id:$(obj).attr('data-id'), _ajax:1},
                dataType : 'json',
                success : function(data){
                    layer.closeAll();
                    if(data.code == 1){
                        layer.msg(data.msg, {icon: 1});
                        //window.location.reload();

                        /* 生成静态页面代码 */
                        var slice_start = url.indexOf('m=admin&c=');
                        slice_start = parseInt(slice_start) + 10;
                        var slice_end = url.indexOf('&a=');
                        var ctl_name = url.slice(slice_start,slice_end);
                        $.ajax({
                            url:__root_dir__+"/index.php?m=home&c=Buildhtml&a=upHtml&lang="+__lang__,
                            type:'POST',
                            dataType:'json',
                            data: {del_ids:$(obj).attr('data-id'),ctl_name:ctl_name,_ajax:1},
                            success:function(data){
                                 window.location.reload();
                            },
                            error: function(){
                                window.location.reload();
                            }
                        });
                        /* end */
                    }else{
                        showErrorAlert(data.msg);
                    }
                },
                error:function(e){
                    layer.closeAll();
                    showErrorAlert(e.responseText);
                }
            })
        }
    );
}

/**
 * 批量属性操作
 */
function batch_attr(obj, name, title)
{
    var a = [];
    var k = 0;
    var aids = '';
    $('input[name^='+name+']').each(function(i,o){
        if($(o).is(':checked')){
            a.push($(o).val());
            if (k > 0) {
                aids += ',';
            }
            aids += $(o).val();
            k++;
        }
    })
    if(a.length == 0){
        showErrorAlert('请至少选择一项！');
        return;
    }

    var url = $(obj).attr('data-url');
    //iframe窗
    layer.open({
        type: 2,
        title: title,
        fixed: true, //不固定
        shadeClose: false,
        shade: 0.3,
        maxmin: false, //开启最大化最小化按钮
        area: ['390px', '250px'],
        content: url,
        success: function(layero, index){
            var body = layer.getChildFrame('body', index);
            body.find('input[name=aids]').val(aids);
        }
    });
}

/**
 * 全选
 */
function selectAll(name,obj){
    $('input[name*='+name+']').prop('checked', $(obj).checked);
} 


/**
 * 远程/本地上传图片切换
 */
function clickRemote(obj, id)
{
    try {
        if ($(obj).is(':checked')) {
            $('#'+id+'_remote').show();
            $('.div_'+id+'_local').hide();
            if ($("input[name="+id+"_remote]").val().length > 0) {
                $("input[name=is_litpic]").attr('checked', true); // 自动勾选属性[图片]
            } else {
                $("input[name=is_litpic]").attr('checked', false); // 自动取消属性[图片]
            }
        } else {
            $('.div_'+id+'_local').show();
            $('#'+id+'_remote').hide();
            if ($("input[name="+id+"_local]").val().length > 0) {
                $("input[name=is_litpic]").attr('checked', true); // 自动勾选属性[图片]
            } else {
                $("input[name=is_litpic]").attr('checked', false); // 自动取消属性[图片]
            }
        }
    }catch(e){}
}

/**
 * 监听远程图片文本框的按键输入事件
 */
function keyupRemote(obj, id)
{
    try {
        var value = $(obj).val();
        if (value != '') {
            $("input[name=is_litpic]").attr('checked', true); // 自动勾选属性[图片]
        } else {
            $("input[name=is_litpic]").attr('checked', false); // 自动取消属性[图片]
        }
    }catch(e){}
}

/**
 * 批量移动操作
 */
function batch_move(obj, name) {

    var url = $(obj).attr('data-url');

    var a = [];
    $('input[name^='+name+']').each(function(i,o){
        if($(o).is(':checked')){
            a.push($(o).val());
        }
    })
    if(a.length == 0){
        showErrorAlert('请至少选择一项！');
        return;
    }
    // 删除按钮
    layer.confirm('确认批量移动？', {
        title: false,
        btn: ['确定', '取消'] //按钮
    }, function () {
        layer_loading('正在处理');
        $.ajax({
            type: "POST",
            url: url,
            data: {move_id:a, _ajax:1},
            dataType: 'json',
            success: function (data) {
                layer.closeAll();
                if(data.status == 1){
                    layer.msg(data.msg, {icon: 1});
                    window.location.reload();
                }else{
                    showErrorAlert(data.msg);
                }
            },
            error:function(e){
                layer.closeAll();
                showErrorAlert(e.responseText);
            }
        });
    }, function (index) {
        layer.closeAll(index);
    });
}

/**
 * 输入为空检查
 * @param name '#id' '.id'  (name模式直接写名称)
 * @param type 类型  0 默认是id或者class方式 1 name='X'模式
 */
function is_empty(name,type){
    if(type == 1){
        if($('input[name="'+name+'"]').val() == ''){
            return true;
        }
    }else{
        if($(name).val() == ''){
            return true;
        }
    }
    return false;
}

/**
 * 邮箱格式判断
 * @param str
 */
function checkEmail(str){
    var reg = /^[a-z0-9]([a-z0-9\\.]*[-_]{0,4}?[a-z0-9-_\\.]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+([\.][\w_-]+){1,5}$/i;
    if(reg.test(str)){
        return true;
    }else{
        return false;
    }
}
/**
 * 手机号码格式判断
 * @param tel
 * @returns {boolean}
 */
function checkMobile(tel) {
    var reg = /(^1[0-9]{10}$)/;
    if (reg.test(tel)) {
        return true;
    }else{
        return false;
    };
}

/*
 * 上传图片 后台专用
 * @access  public
 * @null int 一次上传图片张图
 * @elementid string 上传成功后返回路径插入指定ID元素内
 * @path  string 指定上传保存文件夹,默认存在public/upload/temp/目录
 * @callback string  回调函数(单张图片返回保存路径字符串，多张则为路径数组 )
 */
var layer_GetUploadify;
function GetUploadify(num,elementid,path,callback,url)
{
    if (layer_GetUploadify){
        layer.close(layer_GetUploadify);
    }
    if (num > 0) {

        if (!url) url = GetUploadify_url;

        var is_water = 1;
        if ('water' == url) {
            is_water = 0;
            url = GetUploadify_url;
        }
        
        if (url.indexOf('?') > -1) {
            url += '&';
        } else {
            url += '?';
        }

        var width = '85%';
        var height = '85%';
        if ('adminlogo' == path || 'loginlogo' == path || 'loginbgimg' == path) { // 上传后台logo
            width = '50%';
            height = '66%';
        }

        var upurl = url+'num='+num+'&input='+elementid+'&path='+path+'&func='+callback+'&is_water='+is_water;
        layer_GetUploadify = layer.open({
            type: 2,
            title: '上传图片',
            shadeClose: false,
            shade: 0.3,
            maxmin: true, //开启最大化最小化按钮
            area: [width, height],
            content: upurl
         });
    } else {
        showErrorAlert('允许上传0张图片！');
        return false;
    }
}

/*
 * 上传图片 在弹出窗里的上传图片
 * @access  public
 * @null int 一次上传图片张图
 * @elementid string 上传成功后返回路径插入指定ID元素内
 * @path  string 指定上传保存文件夹,默认存在public/upload/temp/目录
 * @callback string  回调函数(单张图片返回保存路径字符串，多张则为路径数组 )
 */
var layer_GetUploadifyFrame;
function GetUploadifyFrame(num,elementid,path,callback,url)
{
    if (layer_GetUploadifyFrame){
        layer.close(layer_GetUploadifyFrame);
    } 
    if (num > 0) {
        if (url.indexOf('?') > -1) {
            url += '&';
        } else {
            url += '?';
        }

        var upurl = url + 'num='+num+'&input='+elementid+'&path='+path+'&func='+callback;
        layer_GetUploadifyFrame = layer.open({
            type: 2,
            title: '上传图片',
            shadeClose: false,
            shade: 0.3,
            maxmin: true, //开启最大化最小化按钮
            area: ['85%', '85%'],
            content: upurl
         });
    } else {
        showErrorAlert('允许上传0张图片！');
        return false;
    }
}

/*
 * 上传图片 后台（图片新闻）专用
 * @access  public
 * @null int 一次上传图片张图
 * @elementid string 上传成功后返回路径插入指定ID元素内
 * @path  string 指定上传保存文件夹,默认存在public/upload/temp/目录
 * @callback string  回调函数(单张图片返回保存路径字符串，多张则为路径数组 )
 */
function GetUploadifyProduct(id,num,elementid,path,callback)
{       
    var upurl = eyou_basefile + '?m='+module_name+'&c=Uploadify&a=upload_product&aid='+id+'&num='+num+'&input='+elementid+'&path='+path+'&func='+callback;
    layer.open({
        type: 2,
        title: '上传图片',
        shadeClose: true,
        shade: false,
        maxmin: true, //开启最大化最小化按钮
        area: ['50%', '60%'],
        content: upurl
     });
}
    
// 获取活动剩余天数 小时 分钟
//倒计时js代码精确到时分秒，使用方法：注意 var EndTime= new Date('2013/05/1 10:00:00'); //截止时间 这一句，特别是 '2013/05/1 10:00:00' 这个js日期格式一定要注意，否则在IE6、7下工作计算不正确哦。
//js代码如下：
function GetRTime(end_time){
      // var EndTime= new Date('2016/05/1 10:00:00'); //截止时间 前端路上 http://www.51xuediannao.com/qd63/
       var EndTime= new Date(end_time); //截止时间 前端路上 http://www.51xuediannao.com/qd63/
       var NowTime = new Date();
       var t =EndTime.getTime() - NowTime.getTime();
       /*var d=Math.floor(t/1000/60/60/24);
       t-=d*(1000*60*60*24);
       var h=Math.floor(t/1000/60/60);
       t-=h*60*60*1000;
       var m=Math.floor(t/1000/60);
       t-=m*60*1000;
       var s=Math.floor(t/1000);*/

       var d=Math.floor(t/1000/60/60/24);
       var h=Math.floor(t/1000/60/60%24);
       var m=Math.floor(t/1000/60%60);
       var s=Math.floor(t/1000%60);
       if(s >= 0)   
       return d + '天' + h + '小时' + m + '分' +s+'秒';
   }
   
/**
 * 获取多级联动
 */
function get_select_options(t,next){
    var parent_id = $(t).val();
    var url = $(t).attr('data-url');
    if(!parent_id > 0 || url == ''){
        return;
    }
    url = url + '?pid='+ parent_id;
    $.ajax({
        type : "GET",
        url  : url,
        data : {_ajax:1},
        error: function(e) {
            alert(e.responseText);
            return;
        },
        success: function(v) {
            $('#'+next).html(v);
        }
    });
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

function setCookies(name, value, time)
{
    var cookieString = name + "=" + escape(value) + ";";
    if (time != 0) {
        var Times = new Date();
        Times.setTime(Times.getTime() + time);
        cookieString += "expires="+Times.toGMTString()+";"
    }
    document.cookie = cookieString+"path=/";
}
function delCookie(name){
    var exp=new Date();
    exp.setTime(exp.getTime()-1);
    var cval=getCookie(name);
    if(cval!=null){
        document.cookie=name+"="+cval+";expires="+exp.toGMTString() +"path=/";
    }
}

function layConfirm(msg , callback){
    layer.confirm(msg, {
            title: false,
            btn: ['确定','取消'] //按钮
        }, function(){
            callback();
            layer.closeAll();
        }, function(index){
            layer.close(index);
            return false;// 取消
        }
    );
}

function isMobile(){
    return "yes";
}

// 判断是否手机浏览器
function isMobileBrowser()
{
    var sUserAgent = navigator.userAgent.toLowerCase();    
    var bIsIpad = sUserAgent.match(/ipad/i) == "ipad";    
    var bIsIphoneOs = sUserAgent.match(/iphone os/i) == "iphone os";    
    var bIsMidp = sUserAgent.match(/midp/i) == "midp";    
    var bIsUc7 = sUserAgent.match(/rv:1.2.3.4/i) == "rv:1.2.3.4";    
    var bIsUc = sUserAgent.match(/ucweb/i) == "ucweb";    
    var bIsAndroid = sUserAgent.match(/android/i) == "android";    
    var bIsCE = sUserAgent.match(/windows ce/i) == "windows ce";    
    var bIsWM = sUserAgent.match(/windows mobile/i) == "windows mobile";    
    if (bIsIpad || bIsIphoneOs || bIsMidp || bIsUc7 || bIsUc || bIsAndroid || bIsCE || bIsWM ){    
        return true;
    }else 
        return false;
}

function getCookieByName(name) {
    var start = document.cookie.indexOf(name + "=");
    var len = start + name.length + 1;
    if ((!start) && (name != document.cookie.substring(0, name.length))) {
        return null;
    }
    if (start == -1)
        return null;
    var end = document.cookie.indexOf(';', len);
    if (end == -1)
        end = document.cookie.length;
    return unescape(document.cookie.substring(len, end));
}
function showErrorMsg(msg){
    // layer.open({content:msg,time:2000});
    layer.msg(msg, {icon: 5,time: 2000});
}
function showErrorAlert(msg, icon){
    if (!icon && icon != 0) {
        icon = 5;
    }
    layer.alert(msg, {icon: icon, title: false, closeBtn: false});
}
//关闭页面
function CloseWebPage(){
    if (navigator.userAgent.indexOf("MSIE") > 0) {
        if (navigator.userAgent.indexOf("MSIE 6.0") > 0) {
            window.opener = null;
            window.close();
        } else {
            window.open('', '_top');
            window.top.close();
        }
    }
    else if (navigator.userAgent.indexOf("Firefox") > -1 || navigator.userAgent.indexOf("Chrome") > -1) {
        window.location.href = 'about:blank';
    } else {
        window.opener = null;
        window.open('', '_self', '');
        window.close();
    }
}
function getHsonLength(json){
    var jsonLength=0;
    for (var i in json) {
        jsonLength++;
    }
    return jsonLength;
}

// post提交之前，切换编辑器从【源代码】到【设计】视图
function ueditorHandle()
{
    try {
        var funcStr = "";
        $('textarea[class*="ckeditor"]').each(function(index, item){
            var func = $(item).data('func');
            if (undefined != func && func) {
                funcStr += func+"();";
            }
        });
        eval(funcStr);
    }catch(e){}
}

/**
 * 封装的加载层
 */
function layer_loading(msg){
    try {
        ueditorHandle(); // post提交之前，切换编辑器从【源代码】到【设计】视图
    }catch(e){}
    
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
 * 父窗口 - 封装的加载层
 */
function parent_layer_loading(msg){
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

function tipsText(){  
    $('.ui-text').each(function(){  
        var _this = $(this);  
        var elm = _this.find('.ui-input');  
        var txtElm = _this.find('.ui-textTips');  
        var maxNum = _this.find('.ui-input').attr('data-num') || 500;  
        // console.log($.support.leadingWhitespace);
        changeNum(elm,txtElm,maxNum,_this);
        if(!$.support.leadingWhitespace){  
            _this.find('textarea').on('propertychange',function(){  
                changeNum(elm,txtElm,maxNum,_this);  
            });  
            _this.find('input').on('propertychange',function(){  
                changeNum(elm,txtElm,maxNum,_this);  
            });  
        } else {
            _this.on('input',function(){  
                changeNum(elm,txtElm,maxNum,_this);  
            });  
        }  
    });  
}  
  
//获取文字输出字数，可以遍历使用  
//txtElm动态改变的dom，maxNum获取data-num值默认为120个字，ps数字为最大字数*2  
function changeNum(elm,txtElm,maxNum,_this) {  
    //汉字的个数  
    //var str = (elm.val().replace(/\w/g, "")).length;  
    //非汉字的个数  
    //var abcnum = elm.val().length - str;  
    var bigtxtElm = _this.find('.ui-big-text');
    total = elm.val().length;  
    if(total <= maxNum ){  
        texts = maxNum - total;  
        txtElm.html('还可以输入<em>'+texts+'</em>个字符');  
        if (bigtxtElm) {
            bigtxtElm.hide();
        }
    }else{  
        texts = total - maxNum ;  
        txtElm.html('已超出<em class="error">'+texts+'</em>个字符');
        if (bigtxtElm) {
            bigtxtElm.show();
        }
    }  
    return ;  
} 

// 查看大图
function Images(links, max_width, max_height){
    var img = "<img src='"+links+"'/>";
    $(img).load(function() {
        width  = this.width;
        height = this.height;

        if (this.width > max_width) {
            width = max_width + 'px';
            height = 'auto';
        }

        if (this.height > max_height) {
            width = 'auto';
            height = max_height + 'px';
        }

        // if (width > height) {
        //     if (width > max_width) {
        //         width = max_width;
        //     }
        //     width += 'px';
        // } else {
        //     width = 'auto';
        // }
        // if (width < height) {
        //     if (height > max_height) {
        //         height = max_height;
        //     }
        //     height += 'px';
        // } else {
        //     height = 'auto';
        // }

        var links_img = "<style type='text/css'>.layui-layer-content{overflow-y: hidden!important;}</style><img style='width:"+width+";height:"+height+";' src="+links+">";
        layer.open({
            type: 1,
            title: false,
            area: [width, height],
            skin: 'layui-layer-nobg', //没有背景色
            content: links_img
        });
    });
}

function gourl(url)
{
    window.location.href = url;
}

// 百度自动推送
function push_zzbaidu(url, type)
{
    $.ajax({
        url:__root_dir__+"/index.php?m=api&c=Ajax&a=push_zzbaidu&lang="+__lang__,
        type:'POST',
        dataType:'json',
        data:{"url":url,"type":type,"_ajax":1},
        success:function(res){
            console.log(res.msg);
        },
        error: function(e){
            console.log(e);
        }
    });
}

// 更新sitemap.xml地图
function update_sitemap(controller, action)
{
    $.ajax({
        url:__root_dir__+"/index.php?m=admin&c=Ajax&a=update_sitemap&lang="+__lang__,
        type:'POST',
        dataType:'json',
        data:{"controller":controller,"action":action,"_ajax":1},
        success:function(res){
            console.log(res.msg);
        },
        error: function(e){
            console.log(e);
        }
    });
}

//在iframe内打开易优官网的页面
function click_to_eyou_1575506523(url,title,width,height) {
    //iframe窗
    if (!width) width = '80%';
    if (!height) height = '80%';
    layer.open({
        type: 2,
        title: title,
        fixed: true, //不固定
        shadeClose: false,
        shade: 0.3,
        maxmin: false, //开启最大化最小化按钮
        area: [width, height],
        content: url
    });
}

//在iframe内打开页面操作
function openFullframe(obj,title,width,height) {
    //iframe窗
    var url = '';
    if (typeof(obj) == 'string' && obj.indexOf("?m=admin&c=") != -1) {
        url = obj;
    } else {
        url = $(obj).data('href');
    }
    if (!width) width = '80%';
    if (!height) height = '80%';
    var iframes = layer.open({
        type: 2,
        title: title,
        fixed: true, //不固定
        shadeClose: false,
        shade: 0.3,
        // maxmin: true, //开启最大化最小化按钮
        area: [width, height],
        content: url,
        end: function() {
            if (1 == $(obj).data('closereload')) window.location.reload();
        }
    });
    if (width == '100%' && height == '100%') {
        layer.full(iframes);
    }
}

/**
 * 选择每页数量进行检索
 * @param  {[type]} obj [description]
 * @return {[type]}     [description]
 */
function ey_selectPagesize(obj)
{
    layer_loading('正在处理');
    var pagesize = $(obj).val();
    var thisURL = ey_updateUrlParam('pagesize', pagesize);
    thisURL = thisURL.replace(/&p=\d+/, '&p=1');
    window.location.href = thisURL;
}

/**
 * 添加 或者 修改 url中参数的值
 * @param {[type]} name [description]
 * @param {[type]} val  [description]
 */
function ey_updateUrlParam(name, val) {
    var thisURL = document.location.href;

    // 如果 url中包含这个参数 则修改
    if (thisURL.indexOf(name+'=') > 0) {
        var v = ey_getUrlParam(name);
        if (v != null) {
            // 是否包含参数
            thisURL = thisURL.replace(name + '=' + v, name + '=' + val);

        }
        else {
            thisURL = thisURL.replace(name + '=', name + '=' + val);
        }
        
    } // 不包含这个参数 则添加
    else {
        if (thisURL.indexOf("?") > 0) {
            thisURL = thisURL + "&" + name + "=" + val;
        }
        else {
            thisURL = thisURL + "?" + name + "=" + val;
        }
    }
    return thisURL;
};

function ajax_system_1610425892()
{
    setTimeout(function(){
        $.ajax({
            type : 'get',
            url : eyou_basefile + "?m="+module_name+"&c=Encodes&a=ajax_system_1610425892&lang=" + __lang__,
            data : {_ajax:1},
            dataType : 'json',
            success : function(res){}
        });
    },5000);
}

/**
 * 获取url参数值的方法
 * @param  {[type]} name [description]
 * @return {[type]}      [description]
 */
function ey_getUrlParam(name)
{
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if (r!=null) return unescape(r[2]); return null;
}

function tags_list_1610411887(obj)
{
    layer.closeAll();
    $('#often_tags').hide();
    var url = eyou_basefile + "?m="+module_name+"&c=Tags&a=index&source=archives&lang=" + __lang__;
    //iframe窗
    var iframes = layer.open({
        type: 2,
        title: 'TAG标签管理',
        fixed: true, //不固定
        shadeClose: false,
        content: url
    });

    layer.full(iframes);
}

function get_common_tagindex(obj)
{
    var val = $(obj).val();
    $('#often_tags').hide();
    $('#often_tags_input').hide();
    $('#tag_loading').show();
    $.ajax({
        type : 'post',
        url : eyou_basefile + "?m="+module_name+"&c=Tags&a=get_common_list&is_click=1&lang=" + __lang__,
        data : {tags:val, _ajax:1},
        dataType : 'json',
        success : function(res){
            $('#tag_loading').hide();
            if(res.code == 1){
                if (res.data.html) {
                    $('#often_tags').html(res.data.html).show();
                }
            }else{
                showErrorMsg(res.msg);
            }
        },
        error: function(e){
            layer.closeAll();
            $('#tag_loading').hide();
            layer.alert(e.responseText, {icon: 5, title:false});
        }
    });
}

function get_common_tagindex_input(obj)
{
    var val = $(obj).val();
    $('#tags_click_count').val(0);
    $('#often_tags_input').hide();
    $.ajax({
        type : 'post',
        url : eyou_basefile + "?m="+module_name+"&c=Tags&a=get_common_list&lang=" + __lang__,
        data : {tags:val,type:1, _ajax:1},
        dataType : 'json',
        success : function(res){
            if(res.code == 1){
                if (res.data.html) {
                    $('#often_tags_input').html(res.data.html).show();
                }
            }else{
                showErrorMsg(res.msg);
            }
        },
        error: function(e){
            layer.closeAll();
            layer.alert(e.responseText, {icon: 5, title:false});
        }
    });
}

function selectArchivesTag(obj)
{
    event.stopPropagation();
    var newTag = $.trim($(obj).html());
    var tags = $.trim($('#tags').val());
    if (tags != '') {
        tags = tags.replace(/，/ig, ',');
        tagsList = tags.split(',');
    } else {
        tagsList = new Array();
    }
    if (-1 < $.inArray(newTag, tagsList)) {
        tagsList.splice($.inArray(newTag, tagsList), 1);
        $(obj).removeClass('cur');
    } else {
        tagsList.push(newTag);
        $(obj).addClass('cur');
    }
    tags = tagsList.join(',');
    $('#tags').val(tags);
}

function selectArchivesTagInput(obj)
{
    event.stopPropagation();
    var newTag = $.trim($(obj).html());
    var tags = $.trim($('#tags').val());
    var count = $('#tags_click_count').val();
    if (tags != '') {
        tags = tags.replace(/，/ig, ',');
        tagsList = tags.split(',');
    } else {
        tagsList = new Array();
    }
    if (-1 < $.inArray(newTag, tagsList)) {
        tagsList.splice($.inArray(newTag, tagsList), 1);
        $(obj).removeClass('cur');
    } else {
        if(0 == count){
            tagsList.splice(tagsList.length-1,1);
            $(obj).removeClass('cur');
        }

        tagsList.push(newTag);
        $(obj).addClass('cur');
        $('#tags_click_count').val(count+1)
    }
    tags = tagsList.join(',');
    $('#tags').val(tags);
}

/**
 * 检测文档的自定义文件名
 * @return {[type]} [description]
 */
function ajax_check_htmlfilename()
{
    var flag = false;
    var aid = $('input[name=aid]').val();
    var htmlfilename = $.trim($('input[name=htmlfilename]').val());
    if (htmlfilename == '') {
        return true;
    }

    $.ajax({
        url : eyou_basefile + "?m="+module_name+"&c=Archives&a=ajax_check_htmlfilename&lang=" + __lang__,
        type: 'POST',
        async: false,
        dataType: 'JSON',
        data: {htmlfilename: htmlfilename, aid: aid, _ajax:1},
        success: function(res){
            if(res.code == 1){
                flag = true;
            }
        },
        error: function(e){
            showErrorAlert(e.responseText);
        }
    });

    return flag;
}
function check_title_repeat(obj,aid) {
    var title = $(obj).val();
    if (title){
        $.ajax({
            type: "POST",
            url : eyou_basefile + "?m="+module_name+"&c=Archives&a=check_title_repeat&lang=" + __lang__,
            data: {title:title,aid:aid, _ajax:1},
            dataType: 'json',
            success: function (data) {
                if(data.code == 0){
                    layer.tips(data.msg, '#title',{
                        tips: [2, '#F5F5F5'],
                        area: ['300px', 'auto'],
                        time: 0
                    });
                }else {
                    layer.closeAll();
                }
            },
            error:function(){
            }
        });
    }else{
        layer.closeAll();
    }
}

function set_author()
{
    layer.prompt({
            title:'<font color="red">设置作者默认名称</font>'
        },
        function(val, index){
            $.ajax({
                url: eyou_basefile + "?m=admin&c=Admin&a=ajax_setfield&_ajax=1",
                type: 'POST',
                dataType: 'JSON',
                data: {field:'pen_name',value:val},
                success: function(res){
                    if (res.code == 1) {
                        $('#author').val(val);
                        layer.msg(res.msg, {icon: 1, time:1000});
                    } else {
                        showErrorMsg(res.msg);
                        return false;
                    }
                },
                error: function(e){
                    showErrorMsg(e.responseText);
                    return false;
                }
            });
            layer.close(index);
        }
    );
}