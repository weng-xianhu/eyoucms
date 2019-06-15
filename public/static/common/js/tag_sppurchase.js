// 加入购物车
function shop_add_cart() {
    var JsonData    = fe912b5dac71082e12c1827a3107f9b;
    var QuantityObj = document.getElementById(JsonData.quantity);
    var aid = JsonData.aid;
    var num = QuantityObj.value;
    var url = JsonData.shop_add_cart_url;
    var ajaxdata = 'aid='+aid+'&num='+num;

    //创建异步对象
    var ajaxObj = new XMLHttpRequest();
    ajaxObj.open("post", url, true);
    ajaxObj.setRequestHeader("X-Requested-With","XMLHttpRequest");
    ajaxObj.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    //发送请求
    ajaxObj.send(ajaxdata);

    ajaxObj.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (ajaxObj.readyState == 4 && ajaxObj.status == 200) {
            var json = ajaxObj.responseText;  
            var res = JSON.parse(json);
            if ('1' == res.code) {
                // 是否要去购物车 
                shop_cart_list(JsonData.shop_cart_list_url);
            }else{
                // 去登陆
                is_login(JsonData.login_url);
            }
        } 
    };
}

// 立即购买
function BuyNow(aid){
    var JsonData    = fe912b5dac71082e12c1827a3107f9b;
    var QuantityObj = document.getElementById(JsonData.quantity);
    var aid = JsonData.aid;
    var num = QuantityObj.value;
    var url = JsonData.shop_buy_now_url;
    var ajaxdata = 'aid='+aid+'&num='+num;

    //创建异步对象
    var ajaxObj = new XMLHttpRequest();
    ajaxObj.open("post", url, true);
    ajaxObj.setRequestHeader("X-Requested-With","XMLHttpRequest");
    ajaxObj.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    //发送请求
    ajaxObj.send(ajaxdata);

    ajaxObj.onreadystatechange = function () {
        // 这步为判断服务器是否正确响应
        if (ajaxObj.readyState == 4 && ajaxObj.status == 200) {
            var json = ajaxObj.responseText;  
            var res  = JSON.parse(json);
            if ('1' == res.code) {
                // 去购买
                window.location.href = res.url;
            }else{
                // 去登录
                is_login(JsonData.login_url);
            }
        } 
    };
}

// 数量加减处理
function CartUnifiedAlgorithm(symbol){
    // 数量
    var QuantityObj = document.getElementById(fe912b5dac71082e12c1827a3107f9b.quantity);

    if ('change' == symbol) {
        // 直接修改数量
        if ('1' > QuantityObj.value || '' == QuantityObj.value) {
            QuantityObj.value = '1';
            alert('商品数量最少为1');
        }
    }else if ('+' == symbol) {
        // 加数量
        var quantity = Number(QuantityObj.value) + 1;
        QuantityObj.value = quantity;
    }else if ('-' == symbol && QuantityObj.value > '1') {
        // 减数量
        var quantity = Number(QuantityObj.value) - 1;
        QuantityObj.value = quantity;
    }else{
        // 如果数量小于1则自动填充1
        QuantityObj.value = '1';
        alert('商品数量最少为1');
    }
}

// 去购车去
function shop_cart_list(url){
    var mymessage = confirm("加入购物车成功，前往购物车！");
    if(mymessage == true){
        window.location.href = url;
    }
}

// 去登陆
function is_login(url){
    var mymessage = confirm("您还没未登录，请登录后购买！");
    if(mymessage == true){
        window.location.href = url;
    }
}
