
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
        layer.alert('请至少选择一项', {icon: 2, title:false});
        return;
    }

    var deltype = $(obj).attr('data-deltype');
    if ('pseudo' == deltype) {
        title = '删除到回收站，确认批量删除？';
    } else {
        title = '此操作不可逆，确认批量删除？';
    }

    // 删除按钮
    layer.confirm(title, {
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
                if(data.code == 1){
                    layer.msg(data.msg, {icon: 1});
                    window.location.reload();
                }else{
                    layer.alert(data.msg, {icon: 2, title:false});
                }
            },
            error:function(){
                layer.closeAll();
                layer.alert('网络失败，请刷新页面后重试', {icon: 2, title:false});
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
    var deltype = $(obj).attr('data-deltype');
    if ('pseudo' == deltype) {
        title = '删除到回收站，确认删除？';
    } else {
        title = '此操作不可逆，确认删除？';
    }

    layer.confirm(title, {
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
                    if(data.code == 1){
                        layer.msg(data.msg, {icon: 1});
                        window.location.reload();
                        // $(obj).parent().parent().parent().remove();
                    }else{
                        layer.alert(data.msg, {icon: 2, title:false});
                    }
                },
                error:function(){
                    layer.closeAll();
                    layer.alert('网络失败，请刷新页面后重试', {icon: 2, title:false});
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
        layer.alert('请至少选择一项', {icon: 2, title:false});
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
                    layer.alert(data.msg, {icon: 2, title:false});
                }
            },
            error:function(){
                layer.closeAll();
                layer.alert('网络失败，请刷新页面后重试', {icon: 2, title:false});
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
        if (!url) {
            url = GetUploadify_url;
        }
        
        if (url.indexOf('?') > -1) {
            url += '&';
        } else {
            url += '?';
        }

        var width = '85%';
        var height = '85%';
        if ('adminlogo' == path) { // 上传后台logo
            width = '50%';
            height = '66%';
        }

        var upurl = url+'num='+num+'&input='+elementid+'&path='+path+'&func='+callback;
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
        layer.alert('允许上传0张图片', {icon:2, title:false});
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
        layer.alert('允许上传0张图片', {icon:2, title:false});
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
        error: function(request) {
            alert("网络失败，请刷新页面后重试");
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
    layer.msg(msg, {icon: 2,time: 2000});
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
    ueditorHandle(); // post提交之前，切换编辑器从【源代码】到【设计】视图

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