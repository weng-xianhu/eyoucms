// 上传售后图片
function uploadServiceImg() {
    var upoladNum = parseInt($('#uploadServiceImg').attr('data-numlimit'));
    if (0 >= upoladNum) {
        showErrorAlert('最多允许上传6张图片', 4);
        return false;
    }
    // 调起上传图片框
    GetUploadify(upoladNum, '', 'allimg', 'uploadImgCallBack', '', '图片说明', 0);
}

// 上传多图回调函数
function uploadImgCallBack(paths) {
    var obj = $('#uploadServiceImg');
    var last_div = $("#uploadServiceImgTpl").html();
    var pathsArr = $.isArray(paths) ? paths : [paths];
    for (var i = 0; i < pathsArr.length; i++) {
        // 若可上传数量为0则执行返回
        var num = obj.attr('data-numlimit');
        if (0 === parseInt(num)) return false;
        num = Number(num) - 1;
        if (0 === parseInt(num)) obj.find('.img-add').hide();
        obj.attr('data-numlimit', parseInt(num));
        obj.find('.fieldext_upload:eq(0)').before(last_div);
        obj.find('.fieldext_upload:eq(0)').find('input').val(pathsArr[i]);
        obj.find('.fieldext_upload:eq(0)').find('img').attr('src', pathsArr[i]);
        obj.find('.fieldext_upload:eq(0)').find('a:eq(0)').attr('href', paths[i]).attr('target', "_blank");
        obj.find('.fieldext_upload:eq(0)').find('a:eq(1)').attr('onclick', "uploadImgDel(this, '" + pathsArr[i] + "')");
    }
}

// 上传之后删除组图input
function uploadImgDel(obj, path) {
    $(obj).parent().remove();
    var imgObj = $('#uploadServiceImg');
    var num = imgObj.attr('data-numlimit');
    num = Number(num) + 1;
    imgObj.attr('data-numlimit', parseInt(num)).find('.img-add').show();
    $.ajax({
        url : delUploadify_url,
        data: {action: "del", filename: path, '_ajax': 1},
        type: 'POST'
    });  
}

// 提交申请
function submitApply(obj) {
    if ($("#service_type").val() == '') {
        showErrorMsg('请选择服务类型');
        return false;
    } else if ($.trim($("#content").val()) == '') {
        $("#content").focus();
        showErrorMsg('请填写问题描述');
        return false;
    } else if ($("#address").val() == '') {
        $("#address").focus();
        showErrorMsg('请填写您的收货地址');
        return false;
    } else if ($("#consignee").val() == '') {
        $("#consignee").focus();
        showErrorMsg('请填写您的姓名');
        return false;
    } else if ($("#mobile").val() == '') {
        $("#mobile").focus();
        showErrorMsg('请填写您的手机号码');
        return false;
    }

    layer_loading('正在处理');
    $.ajax({
        url: $(obj).data('url'),
        data: $('#post_form').serialize(),
        type: 'post',
        dataType: 'json',
        success: function (res) {
            layer.closeAll();
            if (1 == res.code) {
                showSuccessMsg(res.msg, function () {
                    window.location.href = res.url;
                });
            } else {
                showErrorMsg(res.msg);
            }
        },
        error: function(e) {
            layer.closeAll();
            showErrorAlert(e.responseText);
        }
    });
}

// 图集相册的拖动排序相关
$(".sort-list").sortable({
    start: function(event, ui) {},
    stop: function(event, ui) {}
});
$(".sort-list").disableSelection();