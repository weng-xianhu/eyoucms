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

// 模板错误提示
use think\Db;

switch_exception();
if (!function_exists('users_log_off')) {

    //会员注销前台标签
    function users_log_off()
    {
        $users_open_log_off = getUsersConfigData('users.users_open_log_off','', 'cn'); // 开启注销

        //检测插件
        if (empty($users_open_log_off)){
            return false;
        }

        $field['display'] = '';
        $field['text'] = '申请注销';
        $field['func'] = " onclick='ajax_users_log_off_1017(this);' ";
        $users_id = session('users_id');
        if (empty($users_open_log_off)) {
            $field['display'] = 'none';
        }
        $where['users_id'] = $users_id;
        $where['status'] = ['in', [0, 2]];
        $info = Db::name('users_log_off')->where($where)->order('id desc')->find();
        if (!empty($info) && 2 == $info['status']) {
            $field['text'] = '拒绝注销<span style="color: red;">(拒绝原因:' . $info['refuse_reason'] . ')</span>';
        } elseif (!empty($info) && 0 == $info['status']) {
            $field['text'] = '审核中';
            $field['func'] = '';
        }
        $url = url('user/Users/log_off');
        $field['hidden'] = <<<EOF
<script>
function ajax_users_log_off_1017(obj) {
    var title = '此操作不可恢复，确定注销账号？';
    var btn = ['确定', '取消']; //按钮
    // 删除按钮
    layer.confirm(title, {
        title: false,
        btn: btn //按钮
    }, function () {
        $.ajax({
            type: 'POST',
            url: '{$url}',
            data: {_ajax:1},
            dataType: 'json',
            success: function (data) {
                layer.closeAll();
                if(data.code == 1){
                    layer.msg(data.msg, {icon:1, time: 1000}, function(){
                        window.location.reload();
                    });
                }else{
                    layer.alert(data.msg, {icon: 2, title:false});
                }
            },
            error:function(){
                layer.closeAll();
            }
        });
    }, function (index) {
        layer.closeAll(index);
    });
}
</script>
EOF;

        if (empty($field['display'])) {
            return [$field];
        } else {
            return [];
        }
    }
}