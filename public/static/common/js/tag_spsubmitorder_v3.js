// 自动加载默认的运费
$(function(){
    if (0 == b1decefec6b39feb3be1064e27be2a9.is_wap) {
        $('input[name=addr_id]').val(0); // 不存在默认收货地址的逻辑
    }

    var addr_id = $('#addr_id').val();
    if (addr_id) SelectEd('addr_id', addr_id);

    // 收货地址更多
    if ($('#UlHtml div.address-item').length > 4) {
        $('#UlHtml .more-btn').show();
    } else {
        $('#UlHtml .more-btn').hide();
    }
    $('.more-btn').click(function(){
        var showhide = $(this).attr('data-showhide');
        if ('hide' == showhide) {
            $('#UlHtml div.address-item').each(function(index, item){
                if (index > 3) {
                    $(item).show();
                }
            });
            $(this).attr('data-showhide', 'show');
            $(this).find('span').html('收起更多地址<i class="iconfont-normal"></i>');
        } else {
            $('#UlHtml div.address-item').each(function(index, item){
                if (index > 3) {
                    $(item).hide();
                }
            });
            $(this).attr('data-showhide', 'hide');
            $(this).find('span').html('显示更多地址<i class="iconfont-normal"></i>');
        }
    });

    var sign_selectCurAddr_click = false;
    // 选择该收货地址
    $('#selectCurAddr_1625108121').click(function(event){
        sign_selectCurAddr_click = true;
        $('#selectCurAddrHtml_1625108329').hide();
        $("body,html").animate({ scrollTop: 0 }, 300); //返回顶部，用JQ的animate动画
    });

    /* 窗体滚动事件 */
    $(window).scroll(function () {
        if ($('input[name=addr_id]').val() == 0) {
            var t_a = $("#goods-list_html").offset();
            //判断滚动条的垂直位置是否大于0，说白了就是：判断滚动条是否在顶部
            if ($(window).scrollTop() <= (t_a.top - 150)) {
                if ($(window).scrollTop() <= 0) {
                    sign_selectCurAddr_click = false;
                }
                $('#selectCurAddrHtml_1625108329').hide();
            } else {
                if (sign_selectCurAddr_click == false) {
                    $('#selectCurAddrHtml_1625108329').show();
                }
            }
        }
    });
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
    var is_wap = JsonData.is_wap;
    if (url.indexOf('?') > -1) {
        url += '&';
    } else {
        url += '?';
    }
    url += 'type=order';
    if (1 == is_wap) {
        url += '&gourl='+encodeURIComponent(window.location.href);
        window.location.href = url;
    } else {
        layer.open({
            type: 2,
            title: '添加收货地址',
            shadeClose: false,
            maxmin: false, //开启最大化最小化按钮
            area: [width, height],
            content: url
        }); 
    }
}

// 更新收货地址
function ShopEditAddress(addr_id){
    var JsonData = b1decefec6b39feb3be1064e27be2a9;
    var url = JsonData.shop_edit_address;
    var width  = JsonData.addr_width;
    var height = JsonData.addr_height;
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
    event.stopPropagation();
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
function SelectEd(idname, addr_id, addrData) {
    if (addr_id) {
        $('#'+idname).val(addr_id);
        if ($('input[name=addr_id]').val() > 0) {
            $('#selectCurAddrHtml_1625108329').hide();
        }
        if (addrData && $('#addr_consignee')) {
            $('#addr_consignee').html(addrData.consignee);
            $('#addr_mobile').html(addrData.mobile);
            $('#addr_Info').html(addrData.Info);
            $('#addr_address').html(addrData.address);
        } else {
            var lis = $('#UlHtml div');
            var id  = addr_id+'_ul_li';
            $('#'+id).addClass("selected");
            lis.each(function(){
                if (id != this.id) $('#'+this.id).removeClass("selected");
            });
        }

        // 查询运费
        var JsonData = b1decefec6b39feb3be1064e27be2a9;
        var url = JsonData.shop_inquiry_shipping;
        $.ajax({
            url : url,
            data: {addr_id: addr_id, _ajax: 1},
            type:'post',
            dataType:'json',
            success:function(res){
                // 运费
                var template_money = '包邮';
                if (res.data > 0) {
                    template_money = '￥'+res.data;
                }
                $('#template_money').html(template_money);
                $('#shipping_money').html(res.data);

                // 计算总价+运费
                var TotalAmount_old = $('#TotalAmount_old').val();
                var AmountNew = (Number(TotalAmount_old) + Number(res.data)).toFixed(2);
                $('#TotalAmount, #PayTotalAmountID').html(parseFloat(AmountNew));

                // 计算支付后剩余余额
                var UsersMoney = (Number(JsonData.UsersMoney) - Number(AmountNew)).toFixed(2);
                $('#UsersSurplusMoneyID').html(parseFloat(UsersMoney));
            }
        });
    }
}

// 提交订单
function ShopPaymentPage() {
    var JsonData = b1decefec6b39feb3be1064e27be2a9;
    var is_wap = JsonData.is_wap;
    var addr_id = $('input[name=addr_id]').val();
    if (addr_id == 0) {
        layer.alert('请选择地址！', {icon:0, title: false});
        return false;
    }

    if (1 == is_wap) {
        layer_loading_mini();
    } else {
        layer_loading('正在处理');
    }

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
                if (res.data.code && 'order_status_0' == res.data.code) { // 兼容第三套会员中心
                    SelectPayMethod_2(res.data.pay_id, res.data.pay_mark, res.data.unified_id, res.data.unified_number, res.data.transaction_type);
                } else {
                    if (res.data.email) SendEmail_1608628263(res.data.email);
                    if (res.data.mobile) SendMobile_1608628263(res.data.mobile);
                    window.location.href = res.url;
                }
            } else {
                layer.closeAll();
                if (1 == res.data.add_addr) {
                    if (0 == is_wap) {
                        // PC
                        layer.alert('请选择收货地址', {icon:0, title: false});
                    } else {
                        // 移动端
                        layer.msg('请选择收货地址', {time: 1000}, function(){
                            window.location.href = res.data.url;
                        });
                    }
                    // ShopAddAddress();
                } else if (res.data.url) { // 兼容第二套会员中心
                    layer.msg(res.msg, {icon: 5,time: 1500}, function(){
                        window.location.href = res.data.url;
                    });
                } else {
                    layer.alert(res.msg, {icon:0, title: false, closeBtn: 0});
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

function goAddressList(obj) {
    var url = $(obj).data('url');
    if (url.indexOf('?') > -1) {
        url += '&';
    } else {
        url += '?';
    }
    url += 'gourl='+encodeURIComponent(window.location.href);
    window.location.href = url;
}

function returnUrl(url) {
    window.location.href = url;
}