// 当分页不足以显示隐藏div
$(function() {
    if (parseInt($('div.dataTables_paginate li').length) > 0) {
        $('div.dataTables_paginate').show();
    }
});

// 正在加载中的圈圈提示
function showLayerLoad() {
    var result = layer.load(1, {anim: 2, shade: [0.1, '#fff']});
    return result;
}

// 内容提示(无确认按钮)
function showLayerMsg(msg, anim, callback) {
    if (!anim && anim != 0) anim = 2;
    layer.msg(msg, {anim: anim, time: 1500}, function(index) {
        if (typeof callback !== 'undefined') callback();
        layer.close(index);
    });
}

// 内容提示(有确认按钮)
function showLayerAlert(msg, anim, icon, callback) {
    if (!anim && anim != 0) anim = 2;
    if (!icon && icon != 0) icon = 5;
    layer.alert(msg, {anim: anim, icon: icon, title: false, closeBtn: false}, function(index) {
        if (typeof callback !== 'undefined') callback();
        layer.close(index);
    });
}

// 统一提示确认框
function showConfirmBox(msg, btn, callback_1, callback_2) {
    if (typeof msg === 'undefined' || !msg) msg = '确认执行此操作？';
    if (typeof btn === 'undefined' || !btn) btn = [ey_foreign_system2, ey_foreign_system3];
    layer.confirm(msg, {
        anim: 2,
        btn: btn,
        closeBtn: 0,
        title: false,
        shadeClose: true,
        skin: 'xin-demo-btn'
    }, function (index) {
        // 确认操作
        if (typeof callback_1 !== 'undefined') callback_1();
        layer.close(index);
    }, function (index) {
        // 取消操作
        if (typeof callback_2 !== 'undefined') callback_2();
        layer.close(index);
    });
}

// 处理验证邮箱格式
function handleEmailFormat(email) {
    var reg = /^[a-z0-9]([a-z0-9\\.]*[-_]{0,4}?[a-z0-9-_\\.]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+([\.][\w_-]+){1,5}$/i;
    return reg.test(email);
}

// 处理验证码刷新，默认获取会员登录的验证码，可传入type类型(admin_login、users_reg、users_login、users_retrieve_password、guestbook)
function handleVerifyRefresh(id, type) {
    id = !id ? 'imgVerifys' : id;
    type = !type ? 'users_login' : type;
    var src = eyou_basefile + "?m=api&c=Ajax&a=vertify&type=" + type;
    src = src + '&r=' + Math.floor(Math.random() * 100);
    $('#' + id).attr('src', src);
}

// 渲染编辑器
function showLoadEditor(elemtid) {
    var content = '';
    try{
        content = UE.getEditor(elemtid).getContent();
        UE.getEditor(elemtid).destroy();
    }catch(e){}

    var options = {
        serverUrl : __root_dir__+'/index.php?m=user&c=Uploadify&a=index&savepath=ueditor&lang='+__lang__,
        zIndex: 999,
        initialFrameWidth: "100%",
        initialFrameHeight: 450,
        focus: false,
        maximumWords: 99999,
        removeFormatAttributes: 'class,style,lang,width,height,align,hspace,valign',
        pasteplain: false,
        autoHeightEnabled: false,
        toolbars: [['fullscreen', 'forecolor', 'backcolor', 'removeformat', '|', 'simpleupload', 'unlink', '|', 'paragraph', 'fontfamily', 'fontsize']],
        xssFilterRules: true,
        inputXssFilter: true,
        outputXssFilter: true
    };

    eval("ue_"+elemtid+" = UE.getEditor(elemtid, options);ue_"+elemtid+".ready(function() {ue_"+elemtid+".setContent(content);});");
}

// 发送(短信、邮箱)提醒
function eyUnifiedSendRemind(result) {
    if (result) {
        $.ajax({
            url: result.url,
            data: result.data,
            type: 'post',
            dataType: 'json'
        });
    }
}