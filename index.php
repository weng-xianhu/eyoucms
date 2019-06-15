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
header("Content-type:text/html;charset=utf-8");
// [ 应用入口文件 ]
if (extension_loaded('zlib')){
    try{
        ob_end_clean();
    } catch(Exception $e) {

    }
    ob_start('ob_gzhandler');
}
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.4.0','<'))  die('本系统要求PHP版本 >= 5.4.0，当前PHP版本为：'.PHP_VERSION . '，请到虚拟主机控制面板里切换PHP版本，或联系空间商协助切换。<a href="http://www.eyoucms.com/help/" target="_blank">点击查看易优安装教程</a>');
// error_reporting(E_ALL ^ E_NOTICE);//显示除去 E_NOTICE 之外的所有错误信息
error_reporting(E_ERROR | E_WARNING | E_PARSE);//报告运行时错误

// 检测是否已安装EyouCMS系统
if(file_exists("./install/") && !file_exists("./install/install.lock")){
    header('Location:./install/index.php');
    exit(); 
}

// 缓存时间
define('EYOUCMS_CACHE_TIME', 86400);
// 数据绝对路径
define('DATA_PATH', __DIR__ . '/data/');
// 运行缓存
define('RUNTIME_PATH', DATA_PATH . 'runtime/');
// 安装程序定义
define('DEFAULT_INSTALL_DATE',1525756440);
// 序列号
define('DEFAULT_SERIALNUMBER','20180508131400oCWIoa');
// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');
// 加载框架引导文件
require __DIR__ . '/core/start.php';
