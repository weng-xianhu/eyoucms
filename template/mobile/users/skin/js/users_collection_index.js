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

// 批量删除文档
function batchDelCollection(obj) {
    var ids = [];
    $('input[name*=ids]').each(function(i, o) {
        if ($(o).is(':checked')) ids.push($(o).val());
    });
    if (0 === parseInt(ids.length)) {
        showLayerMsg('请选择需要删除的收藏');
        return false;
    }
    showConfirmBox('确认删除选中收藏？', null, function() {
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
                        $('#collection_' + ids[i]).remove();
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