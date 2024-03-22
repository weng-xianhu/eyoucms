// 管理相关
var relatedContent = $('.related-content');
var managementButton = $('#management-button');
function handleClick() {
    if (relatedContent.is(':hidden')) {
        relatedContent.show();
        managementButton.text('完成');
    } else {
        relatedContent.hide();
        managementButton.text('管理');
    }
}
managementButton.on('click', handleClick);

$(function() {
    $('input[name*=ids]').click(function() {
        if ($('input[name*=ids]').length == $('input[name*=ids]:checked').length) {
            $('#checkboxAll').prop('checked', true);
        } else {
            $('#checkboxAll').prop('checked', false);
        }
    });
    $('#checkboxAll').click(function() {
        $('input[type=checkbox]').prop('checked', this.checked);
    });
});

function readNotice(obj) {
    var id = $(obj).attr('data-id');
    var read = $(obj).attr('data-read');
    if (0 === parseInt(read)) {
        $.ajax({
            url : eyou_basefile + "?m=api&c=Ajax&a=notice_read",
            data: {id: id, _ajax: 1},
            type: 'get',
            dataType: 'json',
            success: function(res) {
                if (res.code == 1) {
                    $(obj).attr('data-read', 1);
                    $('.unread_' + id).removeClass('red').addClass('cor9').html('[已读]');
                    if (parseInt(res.data.unread_num) < 1) {
                        $("#users_unread_num").remove();
                    } else {
                        $("#users_unread_num").html(res.data.unread_num);
                    }
                }
            }
        });
    }
    layer.open({
        title: $(obj).attr('data-title'),
        type: 1,
        skin: 'z_pl', //加上边框
        area: ['80%', '80%'], //宽高
        content: $(obj).attr('data-content')
    });
}

// 批量删除文档
function batchDelNotice(obj) {
    var ids = [];
    $('input[name*=ids]').each(function(i, o) {
        if ($(o).is(':checked')) ids.push($(o).val());
    });
    if (0 === parseInt(ids.length)) {
        showLayerMsg('请选择需要删除的消息');
        return false;
    }
    showConfirmBox('确认删除选中消息？', null, function() {
        showLayerLoad();
        $.ajax({
            url : $(obj).attr('data-url'),
            data: {del_id: ids},
            type: "POST",
            dataType: 'json',
            success: function (res) {
                layer.closeAll();
                if (1 === parseInt(res.code)) {
                    for (var i = 0; i < ids.length; i++) {
                        $('#notice_' + ids[i]).remove();
                    }
                    showLayerMsg(res.msg, 2, function() {
                        window.location.reload();
                    });
                } else {
                    showLayerAlert(res.msg);
                }
            },
            error: function(e) {
                layer.closeAll();
                showLayerAlert(e.responseText);
            }
        });
    });
}