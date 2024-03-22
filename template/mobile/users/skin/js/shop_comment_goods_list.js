var detailsID = 0;

// 上传评价图片
function uploadCommentImg(id) {
    detailsID = parseInt(id);
    var upoladNum = parseInt($('#uploadCommentImg_' + detailsID).attr('data-numlimit'));
    if (0 >= upoladNum) {
        showLayerAlert('最多允许上传6张图片', 2, 4);
        return false;
    }
    // 调起上传图片框
    GetUploadify(upoladNum, '', 'allimg', 'uploadImgCallBack', '', '图片说明', 1);
}

// 上传多图回调函数
function uploadImgCallBack(paths) {
    var obj = $('#uploadCommentImg_' + detailsID);
    var last_div = $("#uploadCommentImgTpl").html();
    var pathsArr = $.isArray(paths) ? paths : [paths];
    for (var i = 0; i < pathsArr.length; i++) {
        // 若可上传数量为0则执行返回
        var num = obj.attr('data-numlimit');
        if (0 === parseInt(num)) return false;
        num = Number(num) - 1;
        if (0 === parseInt(num)) obj.find('.img-add').hide();
        obj.attr('data-numlimit', parseInt(num));
        obj.find('.img-item:eq(0)').before(last_div);
        obj.find('.img-item:eq(0)').find('img').attr('src', pathsArr[i]);
        obj.find('.img-item:eq(0)').find('input').attr('name', 'upload_img[' + detailsID + '][]').val(pathsArr[i]);
        obj.find('.img-item:eq(0)').find('span').attr('onclick', "uploadImgDel(this, '" + pathsArr[i] + "')");
    }
}

// 上传之后删除组图input
function uploadImgDel(obj, path) {
    $(obj).parent().remove();
    $.ajax({
        url : delUploadify_url,
        data: {action: "del", filename: path, '_ajax':1},
        type: 'POST',
        success: function() {
            var imgObj = $('#uploadCommentImg_' + detailsID);
            var num = imgObj.attr('data-numlimit');
            num = Number(num) + 1;
            imgObj.attr('data-numlimit', parseInt(num)).find('.img-add').show();
        }
    });  
}

// 评分设置
function totalScore(score, details_id) {
    // 删除所有评分选中效果
    $('.total-score-' + details_id).removeClass('active');
    // 追加当选评分
    $('.total-score-' + details_id).each(function(idx, ele) {
        $('.total-score-' + details_id).eq(idx).addClass('active');
        if (parseInt(idx) === parseInt(score)) {
            $('#total_score_' + details_id).val(score+1);
            return false;
        }
    });
}

// 提交申请
function submitComment(obj) {
    showLayerLoad();
    $.ajax({
        url: $(obj).data('url'),
        data: $('#post_form').serialize(),
        type: 'post',
        dataType: 'json',
        success: function (res) {
            layer.closeAll();
            if (1 == res.code) {
                showLayerMsg(res.msg, 2, function () {
                    window.location.href = res.url;
                });
            } else {
                showLayerMsg(res.msg);
            }
        },
        error: function(e) {
            layer.closeAll();
            showLayerAlert(e.responseText);
        }
    });
}