$(function () {
    if ($('#add_time').length) $('#add_time').layDate();

    // 展开、收起服务详情
    $('.has-result').on('click', function() {
        var isheight = $('.delivery-list-wrapper').hasClass('height-auto');
        if (isheight) {
            $('.delivery-list-wrapper').removeClass('height-auto');
            $(this).children('span').text('展开服务详情∨');
        } else {
            $('.delivery-list-wrapper').addClass('height-auto');
            $(this).children('span').text('收起服务详情∧');
        }
    });

    // 显示物流填写
    $("#wl-btn").on("click", function() {
        $(".mi-popup").show();
        $(".mi-modal").show();
    });
});

// 隐藏物流填写
function closeMsg() {
    $(".mi-popup").hide();
    $(".mi-modal").hide();  
}

// 提交物流信息
function submitReturnGoods(obj) {
    var name = $("#name");
    if (!name.val()) {
        showErrorMsg('请填写快递公司');
        name.focus();
        return false;
    }
    var code = $("#code");
    if (!code.val()) {
        showErrorMsg('请填写快递单号');
        code.focus();
        return false;
    }
    var time = $("#add_time");
    if (!time.val()) {
        $('#add_time').click();
        return false;
    }
    layer_loading('正在处理');
    $.ajax({
        url : $(obj).data('url'),
        data: $('#postForm').serialize(),
        type: 'post',
        dataType: 'json',
        success: function(res) {
            layer.closeAll();
            if (1 == res.code) {
                showSuccessMsg(res.msg, function() {
                    window.location.reload();
                });
            } else {
                showErrorAlert(res.msg);
            }
        },
        error: function() {
            layer.closeAll();
            showErrorAlert(e.responseText);
        }
    });
}

// 取消服务单
function cancelService(obj) {
    var data = {
        service_id: $(obj).data('id')
    };
    ajaxSubmitPost(obj, '', '确定要取消服务单？', data);
}