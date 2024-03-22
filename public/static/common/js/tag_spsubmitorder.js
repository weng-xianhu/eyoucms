// 自动加载默认的运费
$(function(){
    var addr_id = $('#addr_id').val();
    if (addr_id) {
        SelectEd('addr_id',addr_id);
    }
});

// 颜色控制
function ColorS(css){
    if ('zxzf' == css) {
        $('#zxzf').addClass("btn-primary");
        $('#hdfk').removeClass("btn-primary");
        $('#payment_method').val(0);
    }else{
        $('#hdfk').addClass("btn-primary");
        $('#zxzf').removeClass("btn-primary");
        $('#payment_method').val(1);
    }
}

// 在微信端时，跳转至选择添加收货地址方式页面
function GetWeChatAddr(){
    var JsonData = b1decefec6b39feb3be1064e27be2a9;
    window.location.href = JsonData.shop_add_address;
}

// 添加收货地址
function ShopAddAddress(){
    var JsonData = b1decefec6b39feb3be1064e27be2a9;
    var url = JsonData.shop_add_address;
    var width  = JsonData.addr_width;
    var height = JsonData.addr_height;
    var url = url;
    if (url.indexOf('?') > -1) {
        url += '&';
    } else {
        url += '?';
    }
    url += 'type=order';
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
    var JsonData = b1decefec6b39feb3be1064e27be2a9;
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
        var JsonData = b1decefec6b39feb3be1064e27be2a9;
        var url = JsonData.shop_del_address;

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
            }
        });
    }, function (index) {
        // 否
        layer.closeAll(index);
    });
}

// 选中收货地址
function SelectEd(idname,addr_id)
{   
    if (addr_id) {
        $('#'+idname).val(addr_id);
        var lis = $('#UlHtml li');
        var id  = addr_id+'_ul_li';
        $('#'+id).addClass("selected");
        lis.each(function(){
            if (id != this.id) {
                $('#'+this.id).removeClass("selected");
            }
        });


        // 查询运费
        var JsonData = b1decefec6b39feb3be1064e27be2a9;
        var url = JsonData.shop_inquiry_shipping;
        
        $.ajax({
            url : url,
            data: {addr_id:addr_id,_ajax:1},
            type:'post',
            dataType:'json',
            success:function(res){
                // 运费
                $('#template_money').html('￥'+res.data);
                
                // 计算总价+运费
                var TotalAmount_old = $('#TotalAmount_old').val();
                var AmountNew = Number(TotalAmount_old) + Number(res.data);
                $('#TotalAmount').html(parseFloat(AmountNew.toFixed(2)));
            }
        });
    }
}

// 提交订单
function ShopPaymentPage(){
    layer_loading('正在处理');
    var JsonData = b1decefec6b39feb3be1064e27be2a9;
    var url = JsonData.shop_payment_page;
    if (url.indexOf('?') > -1) {
        url += '&';
    } else {
        url += '?';
    }
    url += '_ajax=1';
    
    $.ajax({
        async: false,
        url : url,
        data: $('#theForm').serialize(),
        type:'post',
        dataType:'json',
        success:function(res) {
            if (1 == res.code) {
                var IsSendEmail = 1;
                var IsSendMobile = 1;
                if (res.data.email) IsSendEmail = SendEmail_1608628263(res.data.email);
                if (res.data.mobile) IsSendMobile = SendMobile_1608628263(res.data.mobile);
                if (IsSendEmail || IsSendMobile) window.location.href = res.url;
            } else {
                layer.closeAll();
                if (1 == res.data.add_addr) {
                    ShopAddAddress();
                } else {
                    layer.msg(res.msg, {icon: 5,time: 1000});
                }
            }
        }
    });
}

// 邮箱发送
function SendEmail_1608628263(result) {
    var ResultID = 1;
    if (result) {
        $.ajax({
            url: result.url,
            data: result.data,
            type:'post',
            dataType:'json'
        });
    }
    return ResultID;
}
 
// 手机发送
function SendMobile_1608628263(result) {
    var ResultID = 1;
    if (result) {
        $.ajax({
            url: result.url,
            data: result.data,
            type:'post',
            dataType:'json'
        });
    }
    return ResultID;
}