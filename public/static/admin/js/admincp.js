$(function() {
//使用title内容作为tooltip提示文字
    $(document).tooltip({
        track: true
    });
    
    // 侧边导航展示形式切换
    $('#foldSidebar > i, #foldSidebar2').click(function(){
        var that = $('#foldSidebar').find('i');
        if ($('.admincp-container').hasClass('unfold')) {
            $(that).addClass('fa-in').removeClass('fa-out');
            $('.sub-menu').removeAttr('style');
            $('.admincp-container').addClass('fold').removeClass('unfold');
        } else {
            $(that).addClass('fa-out').removeClass('fa-in');
            $('.nav-tabs').each(function(i){
                $(that).find('dl').each(function(i){
                    $(that).find('dd').css('top', (-70)*i + 'px');
                    if ($(that).hasClass('active')) {
                        $(that).find('dd').show();
                    }
                });
            });
            $('.admincp-container').addClass('unfold').removeClass('fold');
        }
    });

    // 侧边导航三级级菜单点击
    $('.sub-menu').on('click','a',function(){
        if ($(this).attr('data-param') != undefined) {
            openItem($(this).attr('data-param'), $(this).attr('data-menu_id'), $(this).attr('data-click'), $(this).attr('data-click_url'));
        }
    });
    
    if ($.cookie('workspaceParam') == null) {
        // 默认选择第一个菜单
        // $('.nc-module-menu').find('li:first > a').click();
        openItem('Index|welcome', '', '');
    } else {
        // openItem($.cookie('workspaceParam'));
        openItem('Index|welcome', '', '');
    }
});

// 点击菜单，iframe页面跳转
function openItem(param, menu_id, click, click_url) {
    $('.sub-menu').find('li').removeClass('active');
    data_str = param.split('|');
    $this = $('div[id^="admincpNavTabs_"]').find('a[data-param="' + param + '"]');
    if ($('.admincp-container').hasClass('unfold')) {
        $this.parents('dd:first').show();
    }
    $('li[data-param="' + data_str[0] + '"]').addClass('active');
    $this.parent().addClass('active').parents('dl:first').addClass('active').parents('div:first').show();
    if (click != '' && click != undefined && click != 'undefined') {
        var src = click_url;
    } else {
        var src = eyou_basefile + '?m='+module_name+'&c=' + data_str[0] + '&a=' + data_str[1];
    }
    if (data_str.length%2 == 0) {
        for (var i = 2; i < data_str.length; i++) {
            if (i%2 == 0) {
                src = src + '&';
            } else {
                src = src + '=';
            }
            src = src + data_str[i];
        }
    }
    var lang = $.cookie('admin_lang');
    if (!lang) lang = __lang__;
    if (false != $.inArray('lang', data_str) && $.trim(lang) != '') {
        src = src + '&lang=' + lang;
    }
    //商城样式特殊处理
    var conceal_key = data_str.indexOf('conceal');
    if(conceal_key >= 0 && data_str.hasOwnProperty(conceal_key+1)){  //存在下标
        sessionStorage.setItem('conceal_1649209614', data_str[conceal_key+1]);   //sessionStorage保存点击状态
    }else{
        sessionStorage.setItem('conceal_1649209614', 0);
    }
    //栏目入口内页没有mt20（class）
    var mt20_key = data_str.indexOf('mt20');
    if(mt20_key >= 0 && data_str.hasOwnProperty(mt20_key+1)){  //存在下标
        sessionStorage.setItem('mt20_1649209614', data_str[mt20_key+1]);   //sessionStorage保存点击状态
    }else{
        sessionStorage.setItem('mt20_1649209614', 0);
    }
    $('#workspace').attr('src', src);
    $.cookie('workspaceParam', data_str[1] + '|' + data_str[0], { expires: 1 ,path:"/"});

    // 循环清空选中的标记的Class
    var SubMenuA = $('.sub-menu a');
    SubMenuA.each(function(){
        // 其他参数处理
        $(this).removeClass('on');
        // 特殊参数处理
        $(this).parent().siblings().removeClass('on');
    });
    // 拼装ID获取到点击的ID
    // var ColorId = data_str.join('_');
    var ColorId = data_str[0]+"_"+data_str[1]+"_"+ menu_id;
    if (0 == $('.'+ColorId).attr('data-child')) {
        // 其他参数选项
        $('.'+ColorId).addClass('on');
    }else{
        // 特殊参数处理
        $('.'+ColorId).parent().siblings().addClass('on');
    }
}

/* 显示Ajax表单 */
function ajax_form(id, title, url, width, model)
{
    if (!width)	width = 480;
    if (!model) model = 1;
    var d = DialogManager.create(id);
    d.setTitle(title);
    d.setContents('ajax', url);
    d.setWidth(width);
    d.show('center',model);
    return d;
}