$(function() {
	$(".sel-vip .pc-vip-list").click(function(){
        var active = $(this).is('.active');
        if (active == false) {
            $(this).children('input[name="type_id"]').prop('checked', true);
            $(this).addClass("active").siblings().removeClass("active");
        }
    });
});

