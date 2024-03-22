<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海口快推科技有限公司，并保留所有权利。
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
    function tag_notice_v670918()
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
            var ajax = new XMLHttpRequest();
            ajax.open("get", "{$this->root_dir}/index.php?m=api&c=Ajax&a=notice", true);
            ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
            // ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            ajax.send();
            ajax.onreadystatechange = function () {
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
    tag_notice_v670918();
</script>
EOF;
        } else {
            $notice_js = '';
        }
        $result['hidden'] = $notice_js;

        return $result;
    }
}