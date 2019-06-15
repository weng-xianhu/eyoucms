// 添加收货地址
function ShopAddAddress(){
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
        title: '编辑收货地址',
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
            data: {addr_id:addr_id},
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
            error: function () {
                layer.closeAll();
                layer.alert('网络失败，请刷新页面后重试', {icon: 2, title:false});
            }
        });
    }, function (index) {
        // 否
        layer.closeAll(index);
    });
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
            data: {addr_id:addr_id},
            type:'post',
            dataType:'json',
            success:function(res){
                layer.closeAll();
                if ('1' == res.code) {
                    var spans = $('#'+JsonData.UlHtmlId+' span');
                    var id = addr_id+'_color';
                    spans.each(function(){
                        if (id == this.id) {
                            $('#'+this.id).html('默认地址');
                            $('#'+this.id).css('color','red');
                            $('#'+this.id).attr('data-is_default', 1);
                        }else{
                            $('#'+this.id).css('color','#76838f');
                            $('#'+this.id).html('设为默认');
                            $('#'+this.id).attr('data-is_default', 0);
                        }
                    });
                }else{
                    layer.msg(res.msg, {time: 2000});
                }
            },
            error: function () {
                layer.closeAll();
                layer.alert('网络失败，请刷新页面后重试', {icon: 2, title:false});
            }
        });
    }, function (index) {
        // 否
        layer.closeAll(index);
    });
}