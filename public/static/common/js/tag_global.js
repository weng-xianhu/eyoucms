/*
 * 上传图片 后台专用
 * @access  public
 * @null int 一次上传图片张图
 * @elementid string 上传成功后返回路径插入指定ID元素内
 * @path  string 指定上传保存文件夹,默认存在public/upload/temp/目录
 * @callback string  回调函数(单张图片返回保存路径字符串，多张则为路径数组 )
 */
var layer_GetUploadify;
// PC端上传头像
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
            title: '上传头像',
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

// 手机端上传头像
function GetUploadify_mobile(num,url)
{
    var scriptUrl = '/public/plugins/layer_mobile/layer.js';
    // 支持子目录
    if (typeof __root_dir__ != "undefined") {
        scriptUrl = __root_dir__ + scriptUrl;
    }
    if (typeof __version__ != "undefined") {
        scriptUrl = scriptUrl + '?v=' + __version__;
    }
    // end
    $.getScript(scriptUrl, function(){
        if (num > 0) {
            if (!url) {
                url = GetUploadify_url;
            }
            
            if (url.indexOf('?') > -1) {
                url += '&';
            } else {
                url += '?';
            }

            var content = $('#update_mobile_file').html();
            content = content.replace(/up_f/g, 'upfile');
            content = content.replace(/form1/g,'form2'); 
            layer_GetUploadify = layer.open({
                type:1,
                title:'头像',
                anim:'up',
                style:'position:fixed; bottom:0; left:0; width: 100%; padding:10px 0; border:none;max-width: 100%;',
                content:content,
             });
        } else {
            layer.open({
                content: '允许上传0张图片',
                skin: 'footer',
            });
            return false;
        }
    });
}

// 加载层
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