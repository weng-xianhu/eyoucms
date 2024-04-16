function get_formreply_list(obj) {
    var typeid = obj.dataset.typeid;
    var page = obj.dataset.page;
        page++;
    obj.dataset.page = page;
    var pagesize = obj.dataset.pagesize;
    var totalpage = obj.dataset.totalpage;
    var ordermode = obj.dataset.ordermode;
    if (page > totalpage) {
        obj.style.display = 'none';
        return false;
    }

    var ajaxdata = 'typeid='+typeid+'&page='+page+'&pagesize='+pagesize+'&ordermode='+ordermode+'&totalpage='+totalpage+'&_ajax=1';

    //步骤一:创建异步对象
    var ajax = new XMLHttpRequest();
    //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
    ajax.open("post", root_dir_v379494+"/index.php?m=api&c=Ajax&a=get_formreply_list", true);
    // 给头部添加ajax信息
    ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(ajaxdata);
    //步骤四:注册事件 onreadystatechange 状态改变就会调用
    ajax.onreadystatechange = function () {
        //步骤五 如果能够进到这个判断 说明 数据 完美的回来了,并且请求的页面是存在的
        if (ajax.readyState==4 && ajax.status==200) {
            var json = ajax.responseText;
            var res = JSON.parse(json);
            if (res.code == 1) {
                if (res.data.msg) {
                    obj.insertAdjacentHTML('beforebegin', res.data.msg);
                }
                if (1 == res.data.lastpage) {
                    obj.style.display = 'none';
                    return false;
                }
            } else {
                alert(res.data.msg);
                return false;
            }
        }
    }
}