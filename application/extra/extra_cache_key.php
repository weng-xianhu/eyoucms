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
// 整站cache缓存key键值存放处，统一管理
// 注意：键名要唯一，不然会出现缓存错乱。
// 参考格式如下：
// 格式：模块_控制器_操作名[_序号]
// 1、中括号的序号可选，在同一个操作名内用于区分开。
// 2、键名不区分大写小写，要注意大小写，系统自己转为小写处理在md5()加密处理。

$cache_prefix = \think\Config::get('cache.prefix');

return array(
    /* -------------------------全局使用------------------------- */
    'global_get_region_list'     => array(
        'tag'=>'region', 'options'=>array('expire'=>0, 'prefix'=>$cache_prefix)
    ),
    'global_get_province_list'     => array(
        'tag'=>'region', 'options'=>array('expire'=>0, 'prefix'=>$cache_prefix)
    ),
    'global_get_city_list'     => array(
        'tag'=>'region', 'options'=>array('expire'=>0, 'prefix'=>$cache_prefix)
    ),
    'global_get_area_list'     => array(
        'tag'=>'region', 'options'=>array('expire'=>0, 'prefix'=>$cache_prefix)
    ),

    /* -------------------------数据表数据缓存------------------------- */
    'table_arctype'     => array(
        'tag'=>'arctype', 'options'=>array('expire'=>0, 'prefix'=>$cache_prefix)
    ),

    /* -------------------------前台使用------------------------- */
    // 'home_base_global_assign'     => array(
    //     'tag'=>'home_base', 'options'=>array('expire'=>43200, 'prefix'=>$cache_prefix)
    // ),

    /* -------------------------后台使用------------------------- */
    'admin_all_menu'     => array(
        'tag'=>'admin_common', 'options'=>array('expire'=>43200, 'prefix'=>$cache_prefix)
    ),
    'admin_auth_role_list_logic'     => array(
        'tag'=>'admin_logic', 'options'=>array('expire'=>-1, 'prefix'=>$cache_prefix)
    ),
    'admin_auth_modular_list_logic'     => array(
        'tag'=>'admin_logic', 'options'=>array('expire'=>-1, 'prefix'=>$cache_prefix)
    ),
    'admin_channeltype_list_logic'     => array(
        'tag'=>'admin_logic', 'options'=>array('expire'=>86400, 'prefix'=>$cache_prefix)
    ),
);