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

$user_config = array(
    //分页配置
    'paginate'      => array(
        'type'      => 'eyou',
        'var_page'  => 'page',
        'list_rows' => 15,
    ),
    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------
    //默认错误跳转对应的模板文件
    'dispatch_error_tmpl' => 'public/static/common/dispatch_jump.htm',
    //默认成功跳转对应的模板文件
    'dispatch_success_tmpl' => 'public/static/common/dispatch_jump.htm', 

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件 
    //'exception_tmpl'         => ROOT_PATH.'public/static/errpage/404.html',
    // errorpage 错误页面
    //'error_tmpl'         => ROOT_PATH.'public/static/errpage/404.html',
    
    /**假设这个访问地址是 www.xxxxx.dev/index/goods/goodsInfo/id/1.html 
     *就保存名字为 index_goods_goodsinfo_1.html     
     *配置成这样, 指定 模块 控制器 方法名 参数名
     */
    'HTML_CACHE_ARR'=> array(),

    // 过滤不需要登录的操作
    'filter_login_action' => array(
        'Users@login', // 登录
        'Users@logout', // 退出
        'Users@reg', // 注册
        'Users@vertify', // 验证码
        'Users@retrieve_password', // 忘记密码
        'Users@reset_password', // 忘记密码
        'Users@get_wechat_info', // 微信登陆
        'Users@users_select_login', // 选择登陆方式
        'Users@ajax_wechat_login', // 授权微信登陆
        'Users@pc_wechat_login',   // PC端微信扫码登陆
        'Pay@alipay_return',   // 支付宝异步通知
        'Smtpmail@*', // 邮箱发送
        'LoginApi@*', // 第三方登录
    ),
);

$html_config = include_once 'html.php';
return array_merge($user_config, $html_config);
?>