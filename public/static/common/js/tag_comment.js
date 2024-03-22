var JsonData = eyou_data_json_v563866;
var IsMobile = JsonData.IsMobile;
var ProductID = JsonData.ProductID;
var GetAllDataUrl = JsonData.GetAllDataUrl;
var GetCommentUrl = JsonData.GetCommentUrl;

$(function() {
    AjaxComment(0, 1); // 自动加载商品评价
})

if (0 == IsMobile) { // PC端
    // 商品评价分页
    $(document).on('click', '#ajax_comment_return .pagination a', function () {
        var score = $(".evalute-titleul .check").eq(0).data("level");
        AjaxComment(score, $(this).data('p'));
    });

    // 调用商品评价
    function AjaxComment(score, page) {
        if (ProductID) {
            $.ajax({
                url: GetAllDataUrl,
                data: {score: score, p: page, aid: ProductID},
                type:'post',
                success:function(res) {
                    $('#ajax_comment_return').empty().html(res);
                }
            });
        }
    }
} else if (1 == IsMobile) { // 移动端
    // 商品评价分页
    function AjaxLoadMore(obj) {
        var p = $(obj).attr('data-p');
        p++;
        AjaxComment(0, p++,  obj);
    }

    // 调用商品评价
    function AjaxComment(score, page, obj) {
        // 如果存在对象，表示分页调用，否则为自动加载
        var url = obj ? GetCommentUrl : GetAllDataUrl;
        if (ProductID) {
            $.ajax({
                url: url,
                data: {score: score, p: page, aid: ProductID},
                type:'post',
                success:function(res){
                    if (false == res) {
                        $('.GetMoreData').hide();
                        $('.NoMoreData').show();
                    } else {
                        if (obj) {
                            $('.comList').append(res);
                            $(obj).attr('data-p', page);
                        } else {
                            $('#ajax_comment_return').empty().html(res);
                        }
                    }
                }
            });
        }
    }
}