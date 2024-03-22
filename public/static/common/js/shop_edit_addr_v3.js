///////////////////////mobile代码//////////////////////////
var pcd_select_iframe = '';
function pcd_select()
{
    if ($('#city').val() == '') {
        changeMobileRigion('province');
    }else if ($('#district').val() == '') {
        changeMobileRigion('city');
    }else if ($('#district').val() != '') {
        changeMobileRigion('district');
    }

    $('#pcd_html').show();
}

function pcd_select_close()
{
    $('#pcd_html').hide();
}

function changeMobileRigion(type) {
    $(".address-selectd span").each(function(){
        $(this).removeClass('active');
    });
    $(".address-selectd ."+type).addClass('active');
    if ('province' == type) {
        $('.address-list-province').css('display','block');
        $('.address-list-city').css('display','none');
        $('.address-list-district').css('display','none');
    } else if ('city' == type){
        $('.address-list-province').css('display','none');
        $('.address-list-city').css('display','block');
        $('.address-list-district').css('display','none');
    } else if ('district' == type){
        $('.address-list-province').css('display','none');
        $('.address-list-city').css('display','none');
        $('.address-list-district').css('display','block');
    }
}

// 获取移动端联动地址
function GetMobileRegionData(t,type){
    var parent_id = $(t).data('id');
    if(!parent_id > 0){
        return false ;
    }
    var url = $('#GetRegionDataS').val();

    if ('district' == type){
        $('.dizhi-choose').css('display','none');
    }
    $.ajax({
        url: url,
        data: {parent_id:parent_id,_ajax:1},
        type:'post',
        dataType:'json',
        success:function(res){
            if ('province' == type) {
                $('#province').val(parent_id);
                $('#city').val('');
                $('#district').val('');

                $('.address-list-province').css('display','none');
                $('.address-list-city').css('display','block');
                $('.address-list-district').css('display','none');
                var html = '';
                $.each(res.data, function(k,e) {
                    html += "<span data-id='"+e.id+"' onclick=\"GetMobileRegionData(this,'city')\">"+e.name+"</span>";
                });
                if (0 < $(".address-selectd .province").length) {
                    $(".address-selectd .province").text($(t).text());
                }else{
                    $(".address-selectd").prepend("<span class='province' onclick=\"changeMobileRigion('province');\">"+$(t).text()+"</span>");
                }
                $('.dizhi-choose').css('display','');
                $('.dizhi-choose').addClass('active');
                $(".address-selectd .province").removeClass('active');

                $(".address-selectd .city").remove();
                $(".address-selectd .district").remove();

                $('.address-list-city').empty().html(html);

                var address_title = $(t).text();
                $('#address-title').val(address_title);

            } else if ('city' == type) {
                $('#city').val(parent_id);
                $('#district').val('');

                $('.address-list-province').css('display','none');
                $('.address-list-city').css('display','none');
                $('.address-list-district').css('display','block');
                var html = '';
                $.each(res.data, function(k,e) {
                    html += "<span data-id='"+e.id+"' onclick=\"GetMobileRegionData(this,'district')\">"+e.name+"</span>";
                });
                if (0 < $(".address-selectd .city").length) {
                    $(".address-selectd .city").text($(t).text());
                }else{
                    $(".address-selectd .province").after("<span class='city' onclick=\"changeMobileRigion('city');\">"+$(t).text()+"</span>");
                }
                $('.dizhi-choose').css('display','');
                $('.dizhi-choose').addClass('active');
                $(".address-selectd .city").removeClass('active');

                $(".address-selectd .district").remove();

                $('.address-list-district').empty().html(html);


                var address_title = $(".address-selectd .province").html() +' '+ $(t).text();
                $('#address-title').val(address_title);

            }else if ('district' == type) {
                $('#district').val(parent_id);
                $('.address-list-province').css('display','none');
                $('.address-list-city').css('display','none');
                $('.address-list-district').css('display','none');
                var html = '';
                $.each(res.data, function(k,e) {
                    html += "<span data-id='"+e.id+"' onclick=\"GetMobileRegionData(this,'district')\">"+e.name+"</span>";
                });
                if (0 < $(".address-selectd .district").length) {
                    $(".address-selectd .district").text($(t).text());
                }else{
                    $(".address-selectd .city").after("<span class='district' onclick=\"changeMobileRigion('district');\">"+$(t).text()+"</span>");
                }

                var address_title = $(".address-selectd .province").html() +' '+ $(".address-selectd .city").html() +' '+ $(t).text();
                $('#address-title').val(address_title);
                pcd_select_close();
            }
        },
        error : function(e) {
            layer.alert(e.responseText, {icon: 5});
        }
    });
}

// 更新收货地址
function EditAddressMobile(){
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
            console.log(res)
            if(res.code == 1){
                var gourl = getQueryString('gourl');
                location.replace(gourl);
            }else{
                layer.msg(res.msg, {icon: 5});
            }
        },
        error : function(e) {
            layer.alert(e.responseText, {icon: 5});
        }
    });
};

///////////////////////PC代码//////////////////////////
function show_address_choose() {
    $('.address-select-box').css('display','block');

    if ($('#city').val() == 0) {
        changeRigion('province');
    }else if ($('#district').val() == 0) {
        changeRigion('city');

    }else if ($('#district').val() != 0) {
        changeRigion('district');
        $(".address-selectd .gray").text('');
    }
}

function changeRigion(type) {
    if ('province' == type) {
        $('.address-list-province').css('display','block');
        $('.address-list-city').css('display','none');
        $('.address-list-district').css('display','none');
    } else if ('city' == type){
        $('.address-list-province').css('display','none');
        $('.address-list-city').css('display','block');
        $('.address-list-district').css('display','none');
    } else if ('district' == type){
        $('.address-list-province').css('display','none');
        $('.address-list-city').css('display','none');
        $('.address-list-district').css('display','block');
    }
}

// 获取联动地址
function GetRegionData(t,type){
    var parent_id = $(t).data('id');

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
            console.log(type)
            console.log(res)
            if ('province' == type) {
                $('#province').val(parent_id);
                $('#city').val('');
                $('#district').val('');

                $('.address-list-province').css('display','none');
                $('.address-list-city').css('display','block');
                $('.address-list-district').css('display','none');
                var html = '';
                $.each(res.data, function(k,e) {
                    html += "<span data-id='"+e.id+"' onclick=\"GetRegionData(this,'city')\">"+e.name+"</span>";
                });
                console.log($(".address-selectd .province").length)
                if (0 < $(".address-selectd .province").length) {
                    $(".address-selectd .province").text($(t).text());
                }else{
                    $(".address-selectd").prepend("<span class='province' onclick=\"changeRigion('province');\">"+$(t).text()+"</span>");
                }

                $(".address-selectd .city").remove();
                $(".address-selectd .district").remove();

                $('.address-list-city').empty().html(html);
                $(".address-selectd .gray").text('选择城市/地区');

                var address_title = $(t).text();
                $('#address-title').val(address_title);

            } else if ('city' == type) {
                $('#city').val(parent_id);
                $('#district').val('');

                $('.address-list-province').css('display','none');
                $('.address-list-city').css('display','none');
                $('.address-list-district').css('display','block');
                var html = '';
                $.each(res.data, function(k,e) {
                    html += "<span data-id='"+e.id+"' onclick=\"GetRegionData(this,'district')\">"+e.name+"</span>";
                });
                if (0 < $(".address-selectd .city").length) {
                    $(".address-selectd .city").text($(t).text());
                }else{
                    $(".address-selectd .province").after("<span class='city' onclick=\"changeRigion('city');\">"+$(t).text()+"</span>");
                }
                $(".address-selectd .district").remove();

                $('.address-list-district').empty().html(html);
                $(".address-selectd .gray").text('选择区县');

                var address_title = $(".address-selectd .province").text() +' '+ $(t).text();
                $('#address-title').val(address_title);

            }else if ('district' == type) {
                $('#district').val(parent_id);
                $('.address-list-province').css('display','none');
                $('.address-list-city').css('display','none');
                $('.address-list-district').css('display','none');
                var html = '';
                $.each(res.data, function(k,e) {
                    html += "<span data-id='"+e.id+"' onclick=\"GetRegionData(this,'district')\">"+e.name+"</span>";
                });
                if (0 < $(".address-selectd .district").length) {
                    $(".address-selectd .district").text($(t).text());
                }else{
                    $(".address-selectd .city").after("<span class='district' onclick=\"changeRigion('district');\">"+$(t).text()+"</span>");
                }
                $('.address-select-box').css('display','none');
                var address_title = $(".address-selectd .city").text() +' '+ $(".address-selectd .city").text() +' '+ $(t).text();
                $('#address-title').val(address_title);
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
                layer.msg(res.msg, {icon: 5});
            }
        },
        error : function(e) {
            layer.alert(e.responseText, {icon: 5});
        }
    });
};

// 删除收货地址
function DelAddress(addr_id, obj){
    // layer.confirm('是否删除收货地址？', {
    //     title:false,
    //     closeBtn: false,
    //     btn: ['是', '否'] //按钮
    // }, function () {
    //     // 是
    //     layer_loading('正在处理');
        $.ajax({
            url: $('#DelAddress').val(),
            data: {addr_id:addr_id,_ajax:1},
            type:'post',
            dataType:'json',
            success:function(res){
                // layer.closeAll();
                if (1 == res.code) {
                    // layer.msg(res.msg, {time: 1000}, function(){
                        window.location.href = res.data.url;
                    // });
                }else{
                    layer.msg(res.msg, {time: 2000});
                }
            },
            error: function (e) {
                layer.alert(e.responseText, {icon: 5, title:false});
            }
        });
    // }, function (index) {
    //     layer.closeAll(index);
    // });
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
function layer_colse() {
    var parentObj = parent.layer.getFrameIndex(window.name);
    parent.layer.close(parentObj);
}