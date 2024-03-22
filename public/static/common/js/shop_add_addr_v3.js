///////////////////////mobile代码//////////////////////////
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

// 添加收货地址
function AddAddressMobile(){
    var types = $('#types').val();
    var url   = $('#ShopAddAddress').val();
    if (url.indexOf('?') > -1) {
        url += '&';
    } else {
        url += '?';
    }
    url += '_ajax=1';

    var _parent = parent;
    $.ajax({
        url: url,
        data: $('#theForm').serialize(),
        type:'post',
        dataType:'json',
        success:function(res){
            layer.closeAll();
            if(res.code == 1){
                if (res.data.url) {
                    window.location.href = res.data.url;
                } else {
                    // if ('order' == types && 1 == res.data.is_mobile) {
                        var gourl = getQueryString('gourl');
                        location.replace(gourl);
                    // } else {
                    //     AddHtml(res.data,types);
                    // }
                }
            }else{
                layer.msg(res.msg, {icon: 5});
            }
        },
        error : function(e) {
            layer.closeAll();
            layer.alert(e.responseText, {icon: 5});
        }
    });
};

///////////////////////PC代码//////////////////////////

function show_address_choose() {
    $('.address-select-box').css('display','block');

    if ($('#city').val() == '') {
        changeRigion('province');
    }else if ($('#district').val() == '') {
        changeRigion('city');
    }else if ($('#district').val() != '') {
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
                var address_title = $(".address-selectd .province").text() +' '+ $(".address-selectd .city").text() +' '+ $(t).text();
                $('#address-title').val(address_title);
            }
        },
        error : function(e) {
            layer.closeAll();
            layer.alert(e.responseText, {icon: 5});
        }
    });
}

// 添加收货地址
function AddAddress(){
    var parentObj = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
    var types = $('#types').val();
    var url   = $('#ShopAddAddress').val();
    if (url.indexOf('?') > -1) {
        url += '&';
    } else {
        url += '?';
    }
    url += '_ajax=1';

    var _parent = parent;
    layer_loading('正在处理');
    $.ajax({
        url: url,
        data: $('#theForm').serialize(),
        type:'post',
        dataType:'json',
        success:function(res){
            layer.closeAll();
            if(res.code == 1){
                if (res.data.url) {
                    parent.layer.close(parentObj);
                    _parent.returnUrl(res.data.url);
                    // parent.layer.msg(res.msg, {time: 1000}, function(){
                    //     _parent.returnUrl(res.data.url);
                    // });
                } else {
                    parent.layer.close(parentObj);
                    if ('order' == types && 1 == res.data.is_mobile) {
                        setOrderAddr(res.data);
                    } else {
                        AddHtml(res.data,types);
                    }
                    parent.layer.msg(res.msg, {time: 1000});    
                }
            }else{
                layer.msg(res.msg, {icon: 5});
            }
        },
        error : function(e) {
            layer.closeAll();
            layer.alert(e.responseText, {icon: 5});
        }
    });
};

// 地址管理页追加地址html
function AddHtml(data,types)
{
    var divhtml = $('#divhtml').html();
    var strings = '';
    // 替换ID值
    strings = divhtml.replace('#ul_li_id#',    data.addr_id + "_ul_li");
    strings = strings.replace('#consigneeid#', data.addr_id + "_consignee");
    strings = strings.replace('#mobileid#',    data.addr_id + "_mobile");
    strings = strings.replace('#infoid#',      data.addr_id + "_info");
    strings = strings.replace('#addressid#',   data.addr_id + "_address");
    // 替换地址内容信息
    strings = strings.replace('#consignee#', data.consignee);
    strings = strings.replace('#mobile#',    data.mobile);
    strings = strings.replace('#info#',      data.country+" "+data.province+" "+data.city+" "+data.district);
    strings = strings.replace('#address#',   data.address);
    // 替换JS方法
    strings = strings.replace('#selected#',     "SelectEd('addr_id','" + data.addr_id + "');");
    strings = strings.replace('#setdefault#',   "SetDefault(this, '" + data.addr_id + "');\" data-is_default=\"0\" id=\"" + data.addr_id + "_color\" data-setbtn=\"1\" data-attr_id=\"" + data.addr_id + "\"");
    strings = strings.replace('#shopeditaddr#', "ShopEditAddress('" + data.addr_id + "');");
    strings = strings.replace('#shopdeladdr#',  "ShopDelAddress('" + data.addr_id + "');");
    strings = strings.replace('#addr_del_id#',  "addr_del_" + data.addr_id );
    // 隐藏域，下单页第一次添加收货地址则出现一次，存在则替换数据
    strings = strings.replace('#name#',  "addr_id");
    strings = strings.replace('#id#',    "addr_id");
    strings = strings.replace('#value#', data.addr_id);

    if ('order' == types) {
        parent.$('#UlHtml').find('.address-item:last').before(strings);
        parent.$('#addr_del_' + data.addr_id).remove();
        // 下单页新增地址后，调用选择方法，选择新增的地址
        parent.SelectEd('addr_id',data.addr_id);
    }else{
        parent.$('#UlHtml').append(strings);
    }
}

/**
 * 下单页新增收货地址后，默认是选中的收货地址
 * @param {[type]} data [description]
 */
function setOrderAddr(data)
{
    parent.SelectEd('addr_id',data.addr_id,data);
}

function layer_colse() {
    var parentObj = parent.layer.getFrameIndex(window.name);
    parent.layer.close(parentObj);
}