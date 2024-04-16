function upgradeType(obj) {
    var index = $(obj).val();
    $('.upgrade_type_all').hide();
    if (0 < parseInt(index)) $('#upgrade_type_' + index).show().find('input').focus();
}

function discountType(obj) {
    var index = $(obj).val();
    $('#discount_type').hide();
    if (0 < parseInt(index)) $('#discount_type').show().find('input').focus();
}

function handleDiscountValue(obj, bool) {
    var value = $(obj).val();
    if (0 >= parseInt(value) && !bool) $(obj).val(0.1);
    if ((10 <= parseInt(value) || '00' == value || '000' == value) && bool) $(obj).val(9.9);
}

function levelStatus(obj) {
    var status = $(obj).attr('data-status');
    var level_id = $(obj).attr('data-level_id');
    var level_name = $(obj).attr('data-level_name');
    var statusText = parseInt(status) === 1 ? '禁用' : '启用';
    showConfirm('确认' + statusText + '' + level_name + '？', {}, function() {
        layer_loading('正在处理');
        $.ajax({
            type: 'post',
            url : $(obj).attr('data-url'),
            data: {status: status, level_id: level_id, _ajax: 1},
            dataType: 'json',
            success: function(res) {
                layer.closeAll();
                if (1 === parseInt(res.code)) {
                    showSuccessMsg(res.msg, 1500);
                    $('#levelListPage').empty().html(res.data.html);
                } else {
                    showErrorAlert(res.msg);
                }
            },
            error: function (e) {
                layer.closeAll();
                showErrorAlert(e.responseText);
            }
        })
    });
}

function levelDel(obj) {
    var level_id = $(obj).attr('data-level_id');
    var level_name = $(obj).attr('data-level_name');
    var manage_num = $(obj).attr('data-manage_num');
    var msg = parseInt(manage_num) > 0 ? level_name+'已有关联'+manage_num+'个升级套餐，删除此会员级别的同时将会删除对应升级套餐，确认删除？' : '确认删除'+level_name+'？';
    showConfirm(msg, {}, function() {
        layer_loading('正在处理');
        $.ajax({
            type: 'post',
            url : $(obj).attr('data-url'),
            data: {level_id: level_id, _ajax: 1},
            dataType: 'json',
            success: function(res) {
                layer.closeAll();
                if (1 === parseInt(res.code)) {
                    showSuccessMsg(res.msg, 1500);
                    $('#levelListPage').empty().html(res.data.html);
                } else {
                    showErrorAlert(res.msg);
                }
            },
            error: function (e) {
                layer.closeAll();
                showErrorAlert(e.responseText);
            }
        })
    });
}

function saveUsersLevel(obj) {
    if ($('#level_value').val() == '') {
        showErrorMsg('请填写级别权重');
        $('#level_value').focus();
        return false;
    }
    if ($('#level_name').val() == '') {
        showErrorMsg('请填写级别名称');
        $('#level_name').focus();
        return false;
    }
    var upgrade_type = $('input[name=upgrade_type]:checked').val();
    if (1 === parseInt(upgrade_type) && $('#upgrade_order_money').val() == '') {
        showErrorMsg('请填写订单金额');
        $('#upgrade_order_money').focus();
        return false;
    }
    var discount_type = $('input[name=discount_type]:checked').val();
    if (1 === parseInt(discount_type) && $('#discount').val() == '') {
        showErrorMsg('请填写等级折扣权益');
        $('#discount').focus();
        return false;
    }

    layer_loading('正在处理');
    $.ajax({
        type: 'post',
        url : $(obj).attr('data-url'),
        data: $('#postForm').serialize(),
        dataType: 'json',
        success: function(res) {
            layer.closeAll();
            if (1 === parseInt(res.code)) {
                parent.layer.closeAll();
                parent.showSuccessMsg(res.msg, 1500);
                parent.$('#levelListPage').empty().html(res.data.html);
            } else {
                showErrorAlert(res.msg);
            }
        },
        error: function (e) {
            layer.closeAll();
            showErrorAlert(e.responseText);
        }
    });
}