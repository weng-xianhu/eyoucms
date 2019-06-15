
//工具栏上的所有的功能按钮和下拉框，可以在new编辑器的实例时选择自己需要的重新定义
var ueditor_toolbars = [[
    'fullscreen', 'source', '|', 'undo', 'redo', '|',
    'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', '|',
    'rowspacingtop', 'rowspacingbottom', 'lineheight', '|',
    'customstyle', 'paragraph', 'fontfamily', 'fontsize', '|',
    'directionalityltr', 'directionalityrtl', 'indent', '|',
    'justifyleft', 'justifycenter', 'justifyright', 'justifyjustify', '|', 'touppercase', 'tolowercase', '|',
    'link', 'unlink', '|', 'imagenone', 'imageleft', 'imageright', 'imagecenter', '|',
    'simpleupload', 'insertimage', 'emotion', 'scrawl', 'insertvideo', 'music', 'attachment', 'map', 'insertframe', 'insertcode', 'pagebreak', 'background', '|',
    'horizontal', 'spechars', '|',
    'inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts', '|',
    'preview', 'searchreplace', 'drafts'
]];

var layer_tips; // 全局提示框的对象

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
});

/**
 * 批量删除提交
 */
function batch_del(obj, name) {
    var a = [];
    $('input[name^='+name+']').each(function(i,o){
        if($(o).is(':checked')){
            a.push($(o).val());
        }
    })
    if(a.length == 0){
        layer.alert('请至少选择一项', {icon: 2});
        return;
    }
    // 删除按钮
    layer.confirm('此操作不可逆，确认批量删除？', {
        btn: ['确定', '取消'] //按钮
    }, function () {
        layer_loading('正在处理');
        $.ajax({
            type: "POST",
            url: $(obj).attr('data-url'),
            data: {del_id:a},
            dataType: 'json',
            success: function (data) {
                layer.closeAll();
                if(parseInt(data.code) == 1){
                    layer.msg(data.msg, {icon: 1});
                    window.location.reload();
                }else{
                    layer.alert(data.msg, {icon: 2});
                }
            },
            error:function(){
                layer.closeAll();
                layer.alert('网络失败，请刷新页面后重试', {icon: 2});
            }
        });
    }, function (index) {
        layer.closeAll(index);
    });
}

/**
 * 单个删除
 */
function delfun(obj) {
    layer.confirm('此操作不可逆，确认删除？', {
          btn: ['确定','取消'] //按钮
        }, function(){
            // 确定
            layer_loading('正在处理');
            $.ajax({
                type : 'post',
                url : $(obj).attr('data-url'),
                data : {del_id:$(obj).attr('data-id')},
                dataType : 'json',
                success : function(data){
                    layer.closeAll();
                    if(parseInt(data.code) == 1){
                        layer.msg(data.msg, {icon: 1});
                        window.location.reload();
                    }else{
                        layer.alert(data.msg, {icon: 2});
                    }
                }
            })
        }, function(index){
            layer.close(index);
            return false;// 取消
        }
    );
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
    if ($(obj).is(':checked')) {
        $('#'+id+'_remote').show();
        $('.div_'+id+'_local').hide();
    } else {
        $('.div_'+id+'_local').show();
        $('#'+id+'_remote').hide();
    }
}

/**
 * 批量移动操作
 */
function batch_move(obj, name) {
    var a = [];
    $('input[name^='+name+']').each(function(i,o){
        if($(o).is(':checked')){
            a.push($(o).val());
        }
    })
    if(a.length == 0){
        layer.alert('请至少选择一项', {icon: 2});
        return;
    }
    // 删除按钮
    layer.confirm('确认批量移动？', {
        btn: ['确定', '取消'] //按钮
    }, function () {
        layer_loading('正在处理');
        $.ajax({
            type: "POST",
            url: $(obj).attr('data-url'),
            data: {move_id:a},
            dataType: 'json',
            success: function (data) {
                layer.closeAll();
                if(data.status == 1){
                    layer.msg(data.msg, {icon: 1});
                    window.location.reload();
                }else{
                    layer.alert(data.msg, {icon: 2});
                }
            },
            error:function(){
                layer.closeAll();
                layer.alert('网络失败，请刷新页面后重试', {icon: 2});
            }
        });
    }, function (index) {
        layer.closeAll(index);
    });
}


// 修改指定表的指定字段值 包括有按钮点击切换是否 或者 排序 或者输入框文字
function changeTableVal(table,id_name,id_value,field,obj)
{   
        var src = "";
         if($(obj).hasClass('no')) // 图片点击是否操作
         {          
            //src = '/public/images/yes.png';
            $(obj).removeClass('no').addClass('yes');
            $(obj).html("<i class='fa fa-check-circle'></i>是");
            var value = 1;
            try {  
                if ($(obj).attr('data-value')) {
                    value = $(obj).attr('data-value');
                }
            } catch(e) {  
                // 出现异常以后执行的代码  
                // e:exception，用来捕获异常的信息  
            } 
                
         }else if($(obj).hasClass('yes')){ // 图片点击是否操作                     
            $(obj).removeClass('yes').addClass('no');
            $(obj).html("<i class='fa fa-ban'></i>否");
            var value = 0;
            try {  
                if ($(obj).attr('data-value')) {
                    value = $(obj).attr('data-value');
                }
            } catch(e) {  
                // 出现异常以后执行的代码  
                // e:exception，用来捕获异常的信息  
            } 
         }else{ // 其他输入框操作
            var value = $(obj).val();            
         }      
                                                   
        $.ajax({
            type:'POST',
            url: eyou_basefile + "?m="+module_name+"&c=Index&a=changeTableVal&table="+table+"&id_name="+id_name+"&id_value="+id_value+"&field="+field+'&value='+value,         
            success: function(data){
                 if(!$(obj).hasClass('no') && !$(obj).hasClass('yes')){
                    layer.msg('更新成功', {icon: 1});  
                 } else {
                    if (0 == data.code) {
                        layer.msg(data.msg, {icon: 2});  
                    }
                 }
            }
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
    var reg = /(^1{10}$)/;
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
        if (!url) {
            url = GetUploadify_url;
        }
        
        if (url.indexOf('?') > -1) {
            url += '&';
        } else {
            url += '?';
        }

        var upurl = url+'num='+num+'&input='+elementid+'&path='+path+'&func='+callback;
        layer_GetUploadify = layer.open({
            type: 2,
            title: '上传图片',
            shadeClose: false,
            shade: 0.3,
            maxmin: true, //开启最大化最小化按钮
            area: ['50%', '60%'],
            content: upurl
         });
    } else {
        layer.alert('允许上传0张图片', {icon:2});
        return false;
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

function setCookie(name, value, time)
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

function showErrorMsg(msg){
    // layer.open({content:msg,time:2000});
    layer.msg(msg, {icon: 2,time: 2000});
}

/**
 * 封装的加载层
 */
function layer_loading(msg){
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

function tipsText(){  
    $('.ui-text').each(function(){  
        var _this = $(this);  
        var elm = _this.find('.ui-input');  
        var txtElm = _this.find('.ui-textTips');  
        var maxNum = _this.find('.ui-input').attr('data-num') || 500;  
        // console.log($.support.leadingWhitespace);
        changeNum(elm,txtElm,maxNum);
        if(!$.support.leadingWhitespace){  
            _this.find('textarea').on('propertychange',function(){  
                changeNum(elm,txtElm,maxNum);  
            });  
            _this.find('input').on('propertychange',function(){  
                changeNum(elm,txtElm,maxNum);  
            });  
        } else {
            _this.on('input',function(){  
                changeNum(elm,txtElm,maxNum);  
            });  
        }  
    });  
}  
  
//获取文字输出字数，可以遍历使用  
//txtElm动态改变的dom，maxNum获取data-num值默认为120个字，ps数字为最大字数*2  
function changeNum(elm,txtElm,maxNum) {  
    //汉字的个数  
    //var str = (elm.val().replace(/\w/g, "")).length;  
    //非汉字的个数  
    //var abcnum = elm.val().length - str;  
    total = elm.val().length;  
    if(total <= maxNum ){  
        texts = maxNum - total;  
        txtElm.html('还可以输入<em>'+texts+'</em>个字');  
    }else{  
        texts = total - maxNum ;  
        txtElm.html('已超出<em class="error">'+texts+'</em>个字');  
    }  
    return ;  
} 