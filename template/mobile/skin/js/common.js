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