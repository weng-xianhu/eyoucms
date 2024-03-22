var htmlcode;
var postHtml;
// window.setInterval(eyGetMemberlist230614, 5000);

function eyGetMemberlist230614() {
    var txtObj = document.getElementById(eyResultJson230614.txtid);
    if (txtObj) {
        if (postHtml) {
            htmlcode = postHtml;
        } else {
            htmlcode = txtObj.innerHTML;
            postHtml = htmlcode;
        }
        txtObj.innerHTML = 'Loading…';

        //步骤一:创建异步对象
        var ajax = new XMLHttpRequest();
        //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
        ajax.open("POST", eyResultJson230614.root_dir+"/index.php?m=api&c=Ajax&a=get_tag_memberlist", true);
        // 给头部添加ajax信息
        ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
        // 如果需要像 HTML 表单那样 POST 数据，请使用 setRequestHeader() 来添加 HTTP 头。然后在 send() 方法中规定您希望发送的数据：
        ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        //步骤三:发送请求+数据
        ajax.send("_ajax=1&htmlcode="+htmlcode+"&attarray="+eyResultJson230614.attarray);
        //步骤四:注册事件 onreadystatechange 状态改变就会调用
        ajax.onreadystatechange = function () {
            //步骤五 如果能够进到这个判断 说明 数据 完美的回来了,并且请求的页面是存在的
            if (ajax.readyState == 4 && ajax.status == 200) {
                var json = ajax.responseText;  
                var res = JSON.parse(json);
                if (1 == res.code) {
                    txtObj.innerHTML = res.data.html;
                } else {
                    txtObj.innerHTML = res.msg;
                }
          　}
        } 
    } else {
        document.write('<font color="red">请指定用户循环列表上一层 id="{$field.txtid}"</font>');
    }
}