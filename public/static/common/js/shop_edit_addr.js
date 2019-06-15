// 获取联动地址
function GetRegionData(t,type){
    var parent_id = $(t).val();
    if(!parent_id > 0){
        return false ;
    }
    
    var url = $('#GetRegionDataS').val();
    $.ajax({
        url: url,
        data: {parent_id:parent_id},
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
        error : function() {
            layer.closeAll();
            layer.alert('网络失败，请刷新页面后重试', {icon: 5});
        }
    });
}

// 更新收货地址
function EditAddress(){
    var parentObj = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
    
    var url   = $('#ShopEditAddress').val();
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
                layer.msg(res.msg, {icon: 2});
            }
        },
        error : function() {
            layer.closeAll();
            layer.alert('网络失败，请刷新页面后重试', {icon: 5});
        }
    });
};

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