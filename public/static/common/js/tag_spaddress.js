// 添加收货地址
function ShopAddAddress(obj){
    var JsonData = aeb461fdb660da59b0bf4777fab9eea;
    var wechat_addr_url = JsonData.shop_get_wechat_addr_url;
    if (wechat_addr_url) {
        window.location.href = wechat_addr_url;
        return false;
    }
    var url = JsonData.shop_add_address;
    var width  = JsonData.addr_width;
    var height = JsonData.addr_height;
    var url = url;
    if (url.indexOf('?') > -1) {
        url += '&';
    } else {
        url += '?';
    }
    url += 'type=list';
    //iframe窗
    layer.open({
        type: 2,
        title: '添加收货地址',
        shadeClose: false,
        maxmin: false, //开启最大化最小化按钮
        area: [width, height],
        content: url
    });
}

// 更新收货地址
function ShopEditAddress(addr_id){
    event.stopPropagation();
    var JsonData = aeb461fdb660da59b0bf4777fab9eea;
    var url = JsonData.shop_edit_address;
    var width  = JsonData.addr_width;
    var height = JsonData.addr_height;
    var url = url;
    if (url.indexOf('?') > -1) {
        url += '&';
    } else {
        url += '?';
    }
    url += 'addr_id='+addr_id;
    //iframe窗
    layer.open({
        type: 2,
        title: '修改收货地址',
        shadeClose: false,
        maxmin: false, //开启最大化最小化按钮
        area: [width, height],
        content: url
    });
}

// 删除收货地址
function ShopDelAddress(addr_id){
    layer.confirm('是否删除收货地址？', {
        title:false,
        btn: ['是', '否'] //按钮
    }, function () {
        // 是
        var JsonData = aeb461fdb660da59b0bf4777fab9eea;
        var url = JsonData.shop_del_address;
        layer_loading('正在处理');
        $.ajax({
            url: url,
            data: {addr_id:addr_id,_ajax:1},
            type:'post',
            dataType:'json',
            success:function(res){
                layer.closeAll();
                if ('1' == res.code) {
                    layer.msg(res.msg, {time: 1500});
                    $("#"+addr_id+'_ul_li').remove();
                }else{
                    layer.msg(res.msg, {time: 2000});
                }
            },
            error: function (e) {
                layer.closeAll();
                layer.alert(e.responseText, {icon: 5, title:false});
            }
        });
    }, function (index) {
        // 否
        layer.closeAll(index);
    });
}

/**
 * 选中收货地址，返回到下单提交页面 - 第二套会员中心
 * @param  {[type]} addr_id [description]
 * @param  {[type]} obj     [description]
 * @return {[type]}         [description]
 */
function selectAddress_v201146(addr_id, obj)
{
    event.stopPropagation();
    setCookies_v201146('PlaceOrderAddrid', addr_id);
    var gourl = $('input[name=gourl]').val();
    if (gourl && gourl.length > 0) {
        window.location.href = gourl;
    }
}

/**
 * 设置cookie
 * @param {[type]} name  [description]
 * @param {[type]} value [description]
 * @param {[type]} time  [description]
 */
function setCookies_v201146(name, value, time)
{
    var cookieString = name + "=" + escape(value) + ";";
    if (time != 0) {
        var Times = new Date();
        Times.setTime(Times.getTime() + time);
        cookieString += "expires="+Times.toGMTString()+";"
    }
    document.cookie = cookieString+"path=/";
}

// 设置默认
function SetDefault(obj, addr_id){
    var is_default = $(obj).attr('data-is_default');
    if (1 == is_default) {
        return false;
    }

    layer.confirm('是否设置为默认？', {
        title:false,
        btn: ['是', '否'] //按钮
    }, function () {
        // 是
        var JsonData = aeb461fdb660da59b0bf4777fab9eea;
        var url = JsonData.shop_set_default;
        layer_loading('正在处理');
        $.ajax({
            url: url,
            data: {addr_id:addr_id,_ajax:1},
            type:'post',
            dataType:'json',
            success:function(res){
                layer.closeAll();
                if ('1' == res.code) {
                    var spans = $('#'+JsonData.UlHtmlId).find('span[data-setbtn=1]');
                    var id = addr_id+'_color';
                    spans.each(function(){
                        if (id == this.id) {
                            $('#'+this.id).html('默认地址');
                            $('#'+this.id).css('color','red');
                            $('#'+this.id).attr('data-is_default', 1);
                            $('#'+addr_id+'_ul_li').children('div.address-item').addClass('cur');
                        }else{
                            $('#'+this.id).css('color','#76838f');
                            $('#'+this.id).html('设为默认');
                            $('#'+this.id).attr('data-is_default', 0);
                            $('#'+$(this).attr('data-attr_id')+'_ul_li').children('div.address-item').removeClass('cur');
                        }
                    });
                }else{
                    layer.msg(res.msg, {time: 2000});
                }
            },
            error: function (e) {
                layer.closeAll();
                layer.alert(e.responseText, {icon: 5, title:false});
            }
        });
    }, function (index) {
        // 否
        layer.closeAll(index);
    });
}