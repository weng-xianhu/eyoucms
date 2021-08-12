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
switch_exception();

if (!function_exists('is_adminlogin')) 
{
    /**
     * 检验登陆
     * @param
     * @return bool
     */
    function is_adminlogin(){
        $admin_id = session('admin_id');
        if(isset($admin_id) && $admin_id > 0){
            return $admin_id;
        }else{
            return false;
        }
    }
}

if (!function_exists('apiAdminLog'))
{
    /**
     * 管理员操作记录
     * @param $log_url 操作URL
     * @param $log_info 记录信息
     */
    function apiAdminLog($log_info = ''){
        $admin_id = session('admin_id');
        $admin_id = !empty($admin_id) ? $admin_id : -2;
        $add['log_time'] = getTime();
        $add['admin_id'] = $admin_id;
        $add['log_info'] = $log_info;
        $add['log_ip'] = clientIP();
        $add['log_url'] = request()->baseUrl() ;
        M('admin_log')->add($add);
    }
}