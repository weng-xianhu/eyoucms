$(function(){
    if ('1' == $('#AllSelected').val()) {
        $('#AllChecked').prop('checked','true');
    }
});

// 数量加减
function CartUnifiedAlgorithm(is_sold_out = null ,aid = null, symbol = null, selected = null, spec_value_id = null, cart_id = null){
    if ('IsSoldOut' == is_sold_out) {
        layer.msg('商品已售罄！', {time: 1500});
        return false;
    }
    if ('IsDel' == is_sold_out) {
        layer.msg('无效商品！', {time: 1500});
        return false;
    }
    
    var NumV = $('#'+cart_id+'_num'); //数量
    var CartNum = NumV.val();
    if ('+' == symbol) {
        CartNum = Number(NumV.val()) + 1;
    } else if ('-' == symbol) {
        CartNum = Number(NumV.val()) - 1;
    }
    if (Number(is_sold_out) < Number(CartNum)) {
        layer.msg('商品库存仅！'+is_sold_out+'件', {time: 1500});
        var pre_value = NumV.attr('data-pre_value');
        NumV.val(pre_value);
        return false;
    }
    NumV.attr('data-pre_value', CartNum);

    var PriceV       = $('#'+cart_id+'_price');     //单价
    var SubTotalV    = $('#'+cart_id+'_subtotal');  //小计
    var TotalNumberV = $('#TotalNumber');           //总数
    var TotalAmountV = $('#TotalAmount');           //总价

    // 数量处理逻辑
    if ('change' == symbol) {
        // 手动输入数量
        if ('1' > NumV.val() || '' == NumV.val()) {
            NumV.val(1);
            layer.msg('商品数量最少为1', {time: 1500});
        }
        // 计算单品小计
        var SubTotalNums = Number(PriceV.html()) * Number(NumV.val());
        SubTotalV.html(parseFloat(SubTotalNums.toFixed(2)));
        
    } else if ('+' == symbol) {
        // 计算单品数量
        NumV.val(Number(NumV.val()) + 1);
        // 计算单品小计
        var SubTotalNums = Number(PriceV.html()) * Number(NumV.val());
        SubTotalV.html(parseFloat(SubTotalNums.toFixed(2)));

    } else if ('-' == symbol && NumV.val() > '1') {
        // 计算单品数量
        NumV.val(Number(NumV.val()) - 1);
        // 计算单品小计
        var SubTotalNums = Number(PriceV.html()) * Number(NumV.val());
        SubTotalV.html(parseFloat(SubTotalNums.toFixed(2)));

    } else {
        // 数量减少，为1时不可减。
        layer.msg('商品数量最少为1', {time: 1500});
        return false;
    }

    var JsonData = b82ac06cf24687eba9bc5a7ba92be4c8;
    var url = JsonData.cart_unified_algorithm_url;
    $.ajax({
        url: url,
        data: {aid:aid,symbol:symbol,num:Number(NumV.val()),spec_value_id:spec_value_id,_ajax:1},
        type:'post',
        dataType:'json',
        success:function(res){
            // 返回错误信息
            if ('0' == res.code) {
                layer.msg(res.msg, {time: 1500}, function(){
                    // 购物车不存在商品，非法操作则刷新页面
                    if ('0' == res.data.error) {
                        window.location.reload();
                    }
                });
            } else {
                TotalNumberV.html(res.data.NumberVal);
                TotalAmountV.html(parseFloat(res.data.AmountVal));
            }
        }
    });
}

// 购物车选中产品
function Checked(cart_id,selected){
    var JsonData = b82ac06cf24687eba9bc5a7ba92be4c8;
    var url = JsonData.cart_checked_url;

    // html无刷新更新选中状态
    var TotalNumberV = $('#TotalNumber'); // 获取总数对象
    var TotalAmountV = $('#TotalAmount'); // 获取总价对象
    var NumberVal    = 0; // 初始化参数
    var AmountVal    = 0; // 初始化参数
    if ('*' == cart_id) {
        // 全选中或全撤销选中
        selected       = $('#AllSelected').val(); // 获取是否全选
        var div_inputs = $('input[name=ey_buynum]');  // 获取所有input
        if ('0' == selected) {
            div_inputs.each(function(){
                // 赋值单选框
                $('#'+this.id).prop('checked', true);
                $('#'+this.id).val('1');

                // 赋值隐藏域
                var NewCartId = $('#'+this.id).attr('cart-id');
                $('#'+NewCartId+'_Selected').val(1);

                // 计算总数总额
                // var product_id = $('#'+this.id).attr('product-id');
                NumberVal +=  + Number($('#'+NewCartId+'_num').val());
                AmountVal +=  + Number($('#'+NewCartId+'_subtotal').html());
            });

            // 赋值主选框
            $('#AllSelected').val('1');

        }else{
            div_inputs.each(function(){
                // 赋值单选框
                $('#'+this.id).prop("checked", false);
                $('#'+this.id).val('0');

                // 赋值隐藏域
                var NewCartId = $('#'+this.id).attr('cart-id');
                $('#'+NewCartId+'_Selected').val(0);
            });

            // 赋值主选框
            $('#AllSelected').val('0');

        }

        // 赋值总额总数
        TotalNumberV.html(NumberVal);
        TotalAmountV.html(parseFloat(AmountVal.toFixed(2)));

    }else{
        selected       = $('#'+cart_id+'_Selected').val();  // 获取是否全选
        var div_inputs = $('input[name=ey_buynum]');  // 获取所有input
        var CheckedNum = 0; // 初始化参数
        div_inputs.each(function(){
            if ( $('#'+this.id).is(':checked') ) {
                // 计算选中数量
                CheckedNum++;

                // 计算总数总额
                NumberVal +=  + Number($('#'+$('#'+this.id).attr('cart-id')+'_num').val());
                AmountVal +=  + Number($('#'+$('#'+this.id).attr('cart-id')+'_subtotal').html());
            }
        });

        if ('0' == selected) {
            $('#'+cart_id+'_Selected').val('1');
            if (div_inputs.length == CheckedNum) {
                // 全部选中
                $('#AllChecked').prop('checked', true);
                $('#AllSelected').val('1');
            }else{
                // 非全部选中
                $('#AllChecked').prop("checked", false);
                $('#AllSelected').val('0');
            }
        }else{
            // 撤销选中
            $('#'+cart_id+'_Selected').val('0');
            $('#AllChecked').prop("checked", false);
            $('#AllSelected').val('0');
        }

        // 赋值数据
        TotalNumberV.html(NumberVal);
        TotalAmountV.html(parseFloat(AmountVal.toFixed(2)));
    }
    
    // 修改购物车选中数据
    $.ajax({
        url: url,
        data: {cart_id:cart_id,selected:selected,_ajax:1},
        type:'post',
        dataType:'json',
        success:function(res){
            if ('0' == res.code) {
                layer.msg(res.msg, {time: 2000});
            }
        }
    });
}

// 删除购物车产品
function CartDel(cart_id,title){
    var JsonData = b82ac06cf24687eba9bc5a7ba92be4c8;
    var url = JsonData.cart_del_url;
    layer.confirm('确定删除吗？', {
        title: false,
        btn: ['确认', '取消'] //按钮
    }, function () {
        $.ajax({
            url: url,
            data: {cart_id:cart_id,_ajax:1},
            type:'post',
            dataType:'json',
            success:function(res){
                if (1 == res.code) {
                    layer.msg(res.msg, {time: 1000});
                    $('#' + cart_id + '_product').remove();
                    $('#' + cart_id + '_product_spec').remove();
                    $('#TotalNumber').html(res.data.NumberVal);
                    $('#TotalAmount').html(parseFloat(res.data.AmountVal));
                    if (0 == res.data.CartCount) {
                        window.location.reload();
                    }
                } else {
                    layer.msg(res.msg, {time: 2000});
                }
            }
        });
    });
}

// 移入收藏
function MoveToCollection(cart_id,title){
    var JsonData = b82ac06cf24687eba9bc5a7ba92be4c8;
    var url = JsonData.move_to_collection_url;
    layer.confirm('确定要移入收藏？', {
        btn: ['确认', '取消'] //按钮
    }, function () {
        $.ajax({
            url: url,
            data: {cart_id:cart_id,_ajax:1},
            type:'post',
            dataType:'json',
            success:function(res){
                if (1 == res.code) {
                    layer.msg(res.msg, {time: 1000});
                    $('#' + cart_id + '_product').remove();
                    $('#' + cart_id + '_product_spec').remove();
                    $('#TotalNumber').html(res.data.NumberVal);
                    $('#TotalAmount').html(parseFloat(res.data.AmountVal));
                } else {
                    layer.msg(res.msg, {time: 2000});
                }
            }
        });
    });
}

// 检查购物车商品是否库存都充足，不足时提示
function SubmitOrder(GetUrl) {
    var JsonData = b82ac06cf24687eba9bc5a7ba92be4c8;
    var PostUrl  = JsonData.cart_stock_detection;
    $.ajax({
        url: PostUrl,
        data: {_ajax:1},
        type:'post',
        dataType:'json',
        success:function(res){
            if (1 == res.code) {
                if (1 == res.data) {
                    layer.confirm('部分商品库存数量不足，是否确认提交？', {
                        btn: ['确认', '取消'],
                        title:false,
                        closeBtn: 0
                    }, function () {
                        window.location.href = GetUrl;
                    });
                } else {
                    window.location.href = GetUrl;
                }
            } else {
                layer.msg(res.msg, {time: 2000});
            }
        }
    });
}