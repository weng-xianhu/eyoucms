<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */

namespace think\template\taglib\eyou;

use think\Db;

/**
 * 站内消息通知
 */
class TagNotice extends Base
{
    public $users    = [];

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        // 会员信息
        $this->users_id = session('users_id');
        $this->users_id = !empty($this->users_id) ? intval($this->users_id) : 0;
    }

    /**
     * 站内消息通知
     * @author wengxianhu by 2018-4-20
     */
    public function getNotice()
    {
        $t_uniqid = md5(getTime().uniqid(mt_rand(), TRUE));
        // A标签ID
        $id = md5("ey_{$this->users_id}_{$t_uniqid}");
        $result['id'] = $id;
        $result['url'] = url('user/UsersNotice/index');

        $times = getTime();
        static $notice_js = null;
        if (null === $notice_js) {
            $notice_js = <<<EOF
<script type="text/javascript">
    function tag_notice_1609670918()
    {
        var before_display = '';
        if (document.getElementById("{$id}")) {
            before_display = document.getElementById("{$id}").style.display;
            document.getElementById("{$id}").style.display = 'none';
        }
        
        var users_id = 0;
        if (document.cookie.length>0)
        {
            var c_name = 'users_id';
            c_start = document.cookie.indexOf(c_name + "=");
            if (c_start!=-1)
            { 
                c_start=c_start + c_name.length+1;
                c_end=document.cookie.indexOf(";",c_start);
                if (c_end==-1) c_end=document.cookie.length;
                users_id = unescape(document.cookie.substring(c_start,c_end));
            } 
        }

        if (users_id > 0) {
            //步骤一:创建异步对象
            var ajax = new XMLHttpRequest();
            //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
            ajax.open("post", "{$this->root_dir}/index.php?m=api&c=Ajax&a=notice", true);
            // 给头部添加ajax信息
            ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
            // 如果需要像 HTML 表单那样 POST 数据，请使用 setRequestHeader() 来添加 HTTP 头。然后在 send() 方法中规定您希望发送的数据：
            ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            //步骤三:发送请求
            ajax.send('_ajax=1');
            //步骤四:注册事件 onreadystatechange 状态改变就会调用
            ajax.onreadystatechange = function () {
                //步骤五 如果能够进到这个判断 说明 数据 完美的回来了,并且请求的页面是存在的
                if (ajax.readyState==4 && ajax.status==200) {
                    if (document.getElementById("{$id}")) {
                        document.getElementById("{$id}").innerHTML = ajax.responseText;
                        if (ajax.responseText > 0) {
                            document.getElementById("{$id}").style.display = before_display;
                        } else {
                            document.getElementById("{$id}").style.display = 'none';
                        }
                    }
              　}
            } 
        }
    }
    tag_notice_1609670918();
</script>
EOF;
        } else {
            $notice_js = '';
        }
        $result['hidden'] = $notice_js;

        return $result;
    }
}