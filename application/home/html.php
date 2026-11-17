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


$html_cache_arr = array();
// 全局变量数组
$global = config('tpcache');
empty($global) && $global = tpCache('global');
// 系统模式
$web_cmsmode = isset($global['web_cmsmode']) ? $global['web_cmsmode'] : 2;
/*页面缓存有效期*/
$app_debug = true;
$uiset = trim(input('param.uiset/s', 'off'), '/');
if (1 == $web_cmsmode) { // 运营模式
    // 安全修复：只有已登录管理员的可视化装修模式才允许开启调试，
    // 防止匿名请求带 uiset=on 打开调试模式导致源码、Session 等信息泄露
    if ('on' == $uiset && is_adminlogin()) {
        $app_debug = true;
    } else {
        $app_debug = false;
        $html_cache_arr = config('HTML_CACHE_ARR');
    }
} else {
    // 开发模式：调试信息只对已登录管理员可见，避免匿名访客看到源码、环境变量、Session 等报错详情
    $app_debug = is_adminlogin() ? true : false;
}
/*--end*/

return array(
    // 应用调试模式
    'app_debug' => $app_debug,
    // 模板设置
    'template' => array(
        // 模板路径
        'view_path' => './template/',
        // 模板后缀
        'view_suffix' => 'htm',
        // 模板引擎禁用函数
        'tpl_deny_func_list' => config('global.tpl_deny_func_list'),
        // 默认模板引擎是否禁用PHP原生代码
        'tpl_deny_php'       => !empty($global['web_tpl_deny_php']) ? false : true,
    ),
    // 视图输出字符串内容替换
    'view_replace_str' => array(
        '__EVAL__'  => '', // 过滤模板里的eval函数，防止被注入
    ),

    /**假设这个访问地址是 www.xxxxx.dev/home/goods/goodsInfo/id/1.html 
     *就保存名字为 index_goods_goodsinfo_1.html     
     *配置成这样, 指定 模块 控制器 方法名 参数名
     */
    'HTML_CACHE_ARR'=> $html_cache_arr,
);
?>