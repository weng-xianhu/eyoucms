$(function() {
	// 默认支付方式
    var default_pay_id = $('#div_zhifufangshi').find('input[name=pay_id]:checked').length;
    if (default_pay_id == 0) {
        $('#balance_pay_id').attr("checked","checked");
    }
    
	$(".sel-vip .pc-vip-list").click(function(){
        var active = $(this).is('.active');
        if (active == false) {
            $(this).children('input[name="type_id"]').prop('checked', true);
            $(this).addClass("active").siblings().removeClass("active");
        }
    });
});

