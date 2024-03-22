$(function() {
    if ($('#div_zhifufangshi').find('.radio-label').length > 0) $('#div_zhifufangshi').show();
});

$(document).keydown(function(event){
    if(event.keyCode ==13){
        pay_money();
        return false;
    }
});