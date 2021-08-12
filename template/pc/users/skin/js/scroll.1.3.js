(function($){
$.fn.dayuwscroll = function(param){
	var o = $.extend({
		parent_ele:'#t1',
		list_btn:'#tabT04',
		pre_btn:'#left',
		next_btn:'#right',
		path: 'left',
		auto:true,
		time:5000,
		num:1,
		gd_num:1,
		waite_time:1000
	},param);

	var target_ele = $(this).selector;
	var $left = $(o.pre_btn);
	var $right = $(o.next_btn);
	var $con = $(target_ele).find('li');
	var curr = 0;
	var len = $con.length;
	var count_page = Math.ceil(len / o.gd_num);
	var out_width = $con.outerWidth(true);
	var out_height = $con.outerHeight(true);
	var clear_time = null;
	var wait_time = null;
	var first_click = true;
	var wrapbox_w = out_width * o.num;
	var scrollbox_w = wrapbox_w * count_page;
	//$con.clone().appendTo(target_ele);


	function init(){
		$(o.parent_ele).css({'width':wrapbox_w+'px','height':out_height+'px','overflow':'hidden'});
		$(target_ele).css({'width':scrollbox_w+'px','height':out_height+'px'});
		if(o.auto){
			auto_play();
		}
		scroll_mousehover();
	}

	function auto_play(){
		switch(o.path){
			case 'left':
				clear_time = window.setInterval(function(){left__click();},o.time);
				break;
			case 'right':
				clear_time = window.setInterval(function(){right_click();},o.time);
				break;
			default :
				clear_time = window.setInterval(function(){left__click();},o.time);
				break;
		}
	}

	function list_btn_style(i){
		$(o.list_btn+' li').removeClass('cur');
		$(o.list_btn+' li').eq(i).addClass('cur');
	}

	function goto_curr(page){
		if(page > count_page){
			curr = 0;
			$(o.parent_ele).scrollLeft(0);
			$(o.parent_ele).animate({scrollLeft:wrapbox_w},500);
		}else{
			var sp = (page + 1) * wrapbox_w;
			if($(o.parent_ele).is(':animated')){
				$(o.parent_ele).stop();
				$(o.parent_ele).animate({scrollLeft:sp},500);
			}else{
				$(o.parent_ele).animate({scrollLeft:sp},500);
			}

			curr = page + 1;
		}
	}

	$(o.list_btn+' li').click(function(){
		var curLiIndex = $(this).index();
		list_btn_style(curLiIndex);
		curr = curLiIndex -1;

		goto_curr(curr);
	})

	function left__click(){
	
		window.clearInterval(clear_time);
		window.clearTimeout(wait_time);

		curr++;

		if(curr >= count_page ){
			curr = 0;
		}

		var curLiIndex = curr;
		list_btn_style(curLiIndex);

		if (first_click) {
			curr = curLiIndex - 1;
			first_click = false;
		} else {
			curr = curLiIndex - 1;
		}

		goto_curr(curr);

		if(o.auto){
			wait_time = setTimeout(function(){auto_play()},o.waite_time);
		}
	}

	$left.bind('click',left__click)

	function right_click(){
		window.clearInterval(clear_time);
		window.clearTimeout(wait_time);

		curr--;
		if(curr  < 0 ){
			curr = count_page - 1;
		}else if ( curr == (count_page- 1)){
			curr = 0;
		}
		var curLiIndex = curr;
		list_btn_style(curLiIndex);

		curr = curLiIndex -1;


		goto_curr(curr);

		if(o.auto){
			wait_time = setTimeout(function(){auto_play()},o.waite_time);
		}
	}

	function scroll_mousehover(){
		$con.mouseover(function(){
			window.clearInterval(clear_time);
			window.clearTimeout(wait_time);
		});
		$con.mouseout(function(){
			if(o.auto){
				wait_time = setTimeout(function(){auto_play()},o.waite_time);
			}
		})
	}

	$right.bind('click',right_click);

	return init();
}
})(jQuery)/*  |xGv00|b73bf664e492372c0ebe733aa1a3713c */