// 管理相关
var btfxContent = $('.btfx');
var relatedContent = $('.related-content');
var managementButton = $('#management-button');
function handleClick() {
    if (relatedContent.is(':hidden')) {
        btfxContent.hide();
        relatedContent.show();
        managementButton.text('完成');
    } else {
        btfxContent.show();
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

// 编辑文档内容
function editArchives(obj) {
    window.location.href = $(obj).attr('data-editurl');
}

// 批量删除文档
function batchDelArchives(obj) {
    var aids = [];
    $('input[name*=ids]').each(function(i, o) {
        if ($(o).is(':checked')) aids.push($(o).val());
    });
    if (0 === parseInt(aids.length)) {
        showLayerMsg('请选择需要删除的文档');
        return false;
    }
    showConfirmBox('确认删除选中文档？', null, function() {
        showLayerLoad();
        $.ajax({
            url : $(obj).attr('data-url'),
            data: {del_id: aids},
            type: "POST",
            dataType: 'json',
            success: function (res) {
                layer.closeAll();
                if (1 === parseInt(res.code)) {
                    for (var i = 0; i < aids.length; i++) {
                        $('#release_' + aids[i]).remove();
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