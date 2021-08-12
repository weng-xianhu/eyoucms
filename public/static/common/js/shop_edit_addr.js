// 获取联动地址
function GetRegionData(t,type){
    var parent_id = $(t).val();
    if(!parent_id > 0){
        return false ;
    }
    
    var url = $('#GetRegionDataS').val();
    $.ajax({
        url: url,
        data: {parent_id:parent_id,_ajax:1},
        type:'post',
        dataType:'json',
        success:function(res){
            if ('province' == type) {
                res = '<option value="0">请选择城市</option>'+ res;
                $('#city').empty().html(res);
                $('#district').empty().html('<option value="0">请选择县/区/镇</option>');
            } else if ('city' == type) {
                res = '<option value="0">请选择县/区/镇</option>'+ res;
                $('#district').empty().html(res);
            }
        },
        error : function(e) {
            layer.closeAll();
            layer.alert(e.responseText, {icon: 5});
        }
    });
}

// 更新收货地址
function EditAddress(){
    var parentObj = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
    
    var url   = $('#ShopEditAddress').val();
    if (url.indexOf('?') > -1) {
        url += '&';
    } else {
        url += '?';
    }
    url += '_ajax=1';
    
    $.ajax({
        url: url,
        data: $('#theForm').serialize(),
        type:'post',
        dataType:'json',
        success:function(res){
            if(res.code == 1){
                parent.layer.close(parentObj);
                EditHtml(res.data);
                parent.layer.msg(res.msg, {time: 1000});
            }else{
                layer.closeAll();
                layer.msg(res.msg, {icon: 5});
            }
        },
        error : function(e) {
            layer.closeAll();
            layer.alert(e.responseText, {icon: 5});
        }
    });
};

// 删除收货地址
function DelAddress(addr_id, obj){
    var parentObj = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引

    layer.confirm('是否删除收货地址？', {
        title:false,
        closeBtn: false,
        btn: ['是', '否'] //按钮
    }, function () {
        // 是
        layer_loading('正在处理');
        $.ajax({
            url: $('#DelAddress').val(),
            data: {addr_id:addr_id,_ajax:1},
            type:'post',
            dataType:'json',
            success:function(res){
                layer.closeAll();
                if (1 == res.code) {
                    var _parent = parent;
                    _parent.layer.close(parentObj);
                    _parent.$('#UlHtml').find("#"+addr_id+'_ul_li').remove();
                    _parent.layer.msg(res.msg, {time: 1000});
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

// 更新收货地址html
function EditHtml(data)
{   
    // 获取修改后的值
    var consignee = data.consignee;
    var mobile    = data.mobile;
    var info      = data.country+' '+data.province+' '+data.city+' '+data.district;
    var address   = data.address;
    // 赋值到相应的ID下
    parent.$('#'+data.addr_id+'_consignee').html(consignee);
    parent.$('#'+data.addr_id+'_mobile').html(mobile);
    parent.$('#'+data.addr_id+'_info').html(info);
    parent.$('#'+data.addr_id+'_address').html(address);
}