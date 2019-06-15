$(function(){
    if ('1' == $('#AllSelected').val()) {
        $('#AllChecked').prop('checked','true');
    }
});

// 数量加减
function CartUnifiedAlgorithm(aid,symbol,selected){
    var JsonData = b82ac06cf24687eba9bc5a7ba92be4c8;
    var url = JsonData.cart_unified_algorithm_url;

    var NumV         = $('#'+aid+'_num');       //数量
    var PriceV       = $('#'+aid+'_price');     //单价
    var SubTotalV    = $('#'+aid+'_subtotal');  //小计
    var TotalNumberV = $('#TotalNumber');       //总数
    var TotalAmountV = $('#TotalAmount');       //总价

    // 数量处理逻辑
    if ('change' == symbol) {
        // 手动输入数量
        if ('1' > NumV.val() || '' == NumV.val()) {
            NumV.val(1);
            layer.msg('商品数量最少为1', {time: 1500});
        }
        // 计算单品小计
        var SubTotalNums = Number(PriceV.html()) * Number(NumV.val());
        SubTotalV.html(SubTotalNums.toFixed(2));
        
    }else if ('+' == symbol) {
        // 计算单品数量
        NumV.val(Number(NumV.val()) + 1);
        // 计算单品小计
        var SubTotalNums = Number(PriceV.html()) * Number(NumV.val());
        SubTotalV.html(SubTotalNums.toFixed(2));

    }else if ('-' == symbol && NumV.val() > '1') {
        // 计算单品数量
        NumV.val(Number(NumV.val()) - 1);
        // 计算单品小计
        var SubTotalNums = Number(PriceV.html()) * Number(NumV.val());
        SubTotalV.html(SubTotalNums.toFixed(2));

    }else{
        // 数量减少，为1时不可减。
        layer.msg('商品数量最少为1', {time: 1500});
        return false;
    }

    $.ajax({
        url: url,
        data: {aid:aid,symbol:symbol,num:Number(NumV.val())},
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
            }else{
                TotalNumberV.html(res.data.NumberVal);
                TotalAmountV.html(res.data.AmountVal);
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
                $('#'+this.id).prop('checked','true');
                $('#'+this.id).val('1');

                // 赋值隐藏域
                var NewCartId = $('#'+this.id).attr('cart-id');
                $('#'+NewCartId+'_Selected').val(1);

                // 计算总数总额
                var product_id = $('#'+this.id).attr('product-id');
                NumberVal +=  + Number($('#'+product_id+'_num').val());
                AmountVal +=  + Number($('#'+product_id+'_subtotal').html());
            });

            // 赋值主选框
            $('#AllSelected').val('1');

        }else{
            div_inputs.each(function(){
                // 赋值单选框
                $('#'+this.id).removeProp("checked");
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
        TotalAmountV.html(AmountVal.toFixed(2));

    }else{
        selected       = $('#'+cart_id+'_Selected').val();  // 获取是否全选
        var div_inputs = $('input[name=ey_buynum]');  // 获取所有input
        var CheckedNum = 0; // 初始化参数
        div_inputs.each(function(){
            if ( $('#'+this.id).is(':checked') ) {
                // 计算选中数量
                CheckedNum++;

                // 计算总数总额
                var product_id = $('#'+this.id).attr('product-id');
                NumberVal +=  + Number($('#'+product_id+'_num').val());
                AmountVal +=  + Number($('#'+product_id+'_subtotal').html());
            }
        });

        if ('0' == selected) {
            $('#'+cart_id+'_Selected').val('1');
            if (div_inputs.length == CheckedNum) {
                // 全部选中
                $('#AllChecked').prop('checked','true');
                $('#AllSelected').val('1');
            }else{
                // 非全部选中
                $('#AllChecked').removeProp("checked");
                $('#AllSelected').val('0');
            }
        }else{
            // 撤销选中
            $('#'+cart_id+'_Selected').val('0');
            $('#AllChecked').removeProp("checked");
            $('#AllSelected').val('0');
        }

        // 赋值数据
        TotalNumberV.html(NumberVal);
        TotalAmountV.html(AmountVal.toFixed(2));
    }
    
    // 修改购物车选中数据
    $.ajax({
        url: url,
        data: {cart_id:cart_id,selected:selected},
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
    layer.confirm('确定要删除 <span style="color:red;">'+title+'</span> ？', {
        btn: ['确认', '取消'] //按钮
    }, function () {
        $.ajax({
            url: url,
            data: {cart_id:cart_id},
            type:'post',
            dataType:'json',
            success:function(res){
                if ('1' == res.code) {
                    layer.msg(res.msg, {time: 1500});
                    $('#'+cart_id+'_product').remove();
                    layer.closeAll();
                }else{
                    layer.msg(res.msg, {time: 2000});
                }
            }
        });
    });
}