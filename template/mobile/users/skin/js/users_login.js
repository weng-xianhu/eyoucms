$(document).keydown(function(event) {
    if (13 === parseInt(event.keyCode)) {
        var display_1 = $('#con_sign_1').css('display');
        var display_2 = $('#con_sign_2').css('display');
        if ('block' == display_1 && 'none' == display_2) {
            $('input[name=submit]').trigger('click');
        } else if('none' == display_1 && 'block' == display_2) {
            checkMobileUserLogin1649732103();
        }
    }
});

// 点击事件处理函数
function showPhoneSignIn1() {
    // 获取元素
    var conSign1 = document.getElementById('con_sign_1');
    var conSign2 = document.getElementById('con_sign_2');
    // 隐藏账号登录相关元素
    conSign1.style.display = 'none';
    // 显示手机号登录相关元素
    conSign2.style.display = 'block';
}

function showPhoneSignIn2() {
    // 获取元素
    var conSign1 = document.getElementById('con_sign_1');
    var conSign2 = document.getElementById('con_sign_2');
    // 显示账号登录相关元素
    conSign1.style.display = 'block';
    // 隐藏手机号登录相关元素
    conSign2.style.display = 'none';
}

// 明文密码
$(".pass-showhide").attr('data-showOrHide', 'hide');
$(".pass-showhide").on('click', function(){
    var showOrHide = $(this).attr('data-showOrHide');
    if ('hide' == showOrHide) {
        $(this).attr('data-showOrHide', 'show');
        var name = $(this).data('name');
        $("input[name="+name+"]").get(0).type="text";
        $(this).removeClass('pass-hide').addClass('pass-show');
    } else {
        $(this).attr('data-showOrHide', 'hide');
        var name = $(this).data('name');
        $("input[name="+name+"]").get(0).type="password";
        $(this).removeClass('pass-show').addClass('pass-hide');
    }
});

