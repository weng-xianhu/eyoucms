jQuery(document).ready(function(){
	/*头部搜索*/
	if( $(window).width() < 748)
	{
	$('.searchBar a').click(function(){
	   if($(this).attr('id')!='click'){
			$(this).attr('id','click');
			$('.searchBar-m').show();
		}else{
			$(this).removeAttr('id');
			$('.searchBar-m').hide();
		}
	});
	}
	else
	{
		$('.searchBar a').click(function(){
		$('.searchBar-m').slideDown();
	})
	}
		$('.searchBar-m a').click(function(){
		$('.searchBar-m').hide();
	})

	 /*弹出友链*/
	$(".friendlink,.popup-link").hover(function(){
		$(this).parent().find("ul").show();
		},function(){
		$(this).parent().find("ul").hide();
	})
});

//登录注册tab
function setTab(name, cursel, n) {
	for (i = 1; i <= n; i++) {
		var menu = document.getElementById(name + i);
		var con = document.getElementById("con_" + name + "_" + i);
		menu.className = i == cursel ? "cur": "";
		con.style.display = i == cursel ? "block": "none";
	}
}

function showErrorMsg(msg){
    layer.msg(msg, {icon: 5,time: 2000});
}

function showErrorAlert(msg, icon){
    if (!icon && icon != 0) {
        icon = 5;
    }
    layer.alert(msg, {icon: icon, title: false, closeBtn: false});
}

// 加载层
function layer_loading(msg){
    var loading = layer.msg(
    msg+'...&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;请勿刷新页面', 
    {
        icon: 1,
        time: 3600000, //1小时后后自动关闭
        shade: [0.2] //0.1透明度的白色背景
    });
    //loading层
    var index = layer.load(3, {
        shade: [0.1,'#fff'] //0.1透明度的白色背景
    });

    return loading;
}

$(document).keydown(function(event){
    if(event.keyCode ==13){
        $('form#popup_login_submit input[name=submit]').trigger("click");
    }
});

function ey_fleshVerify(){
    var src = __eyou_basefile__ + "?m=api&c=Ajax&a=vertify&type=users_login&r=" + Math.floor(Math.random()*100);
    $('form#popup_login_submit #imgVerifys').attr('src', src);
}

function popup_login_submit()
{
    var username = $('form#popup_login_submit input[name=username]');
    var password = $('form#popup_login_submit input[name=password]');

    if($.trim(username.val()) == ''){
        layer.msg('用户名不能为空！', {time: 1500, icon: 5});
        username.focus();
        return false;
    }

    if($.trim(password.val()) == ''){
        layer.msg('密码不能为空！', {time: 1500, icon: 5});
        password.focus();
        return false;
    }

    $('form#popup_login_submit input[name=referurl]').val(window.location.href);

    layer_loading('正在处理');
    $.ajax({
        // async:false,
        url : __eyou_basefile__ + "?m=user&c=Users&a=login",
        data: $('#popup_login_submit').serialize(),
        type:'post',
        dataType:'json',
        success:function(res){
            if (1 == res.code) {
                if (5 == res.data.status) {
                    layer.alert(res.msg, {icon: 5, title: false, closeBtn: false},function(){
                        window.location.href = res.url;
                    });
                }else{
                    window.location.href = res.url;
                }
            } else {
                layer.closeAll();
                if ('vertify' == res.data.status) {
                    ey_fleshVerify();
                }
                
                if (2 == res.data.status) {
                    showErrorAlert(res.msg, 4);
                } else {
                    layer.msg(res.msg, {icon: 5,time: 1500});
                }
            }
        },
        error:function(e) {
            layer.closeAll();
            showErrorAlert(e.responseText);
        }
    });
}
