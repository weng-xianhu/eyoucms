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

/**
 * 只作用于网站前台页面，指定需要缓存的页面
 * 参数规则：
 *     mca ：plugins_控制器_操作名
 *     filename ：生成在/data/runtime目录下的指定路径，建议参考以下
 *     p ：当前url控制器的操作方法传入的全部参数变量名
 *     cache : 页面缓存有效时间，单位是秒
 */
return array(
    // array('mca'=>'plugins_Sample_index', 'filename'=>'channel/sample/index', 'cache'=>1),  
    // array('mca'=>'plugins_Sample_lists', 'filename'=>'articlelist/sample/list', 'p'=>array('tid','page'), 'cache'=>1), 
    // array('mca'=>'plugins_Sample_view', 'filename'=>'detail/sample/view', 'p'=>array('aid'), 'cache'=>1), 
);
