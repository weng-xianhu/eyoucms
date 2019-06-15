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
 * 权限属性说明
 * array
 *      id  主键ID
 *      menu_id   一级模块ID
 *      menu_id2    二级模块ID
 *      name  权限名称
 *      is_modules 是否显示在分组下
 *      auths  权限列表(格式：控制器@*,控制器@操作名 --多个权限以逗号隔开)
 */
return [
    [
        'id' => 1,
        'menu_id' => 1001,
        'menu_id2' => 0,
        'name'  => '栏目管理',
        'is_modules'    => 1,
        'auths' => 'Arctype@index,Arctype@add,Arctype@edit,Arctype@del,Arctype@pseudo_del',
    ],
    [
        'id' => 2,
        'menu_id' => 1002,
        'menu_id2' => 0,
        'name'  => '内容管理',
        'is_modules'    => 1,
        'auths' => 'Archives@*,Arctype@single_edit',
    ],
    [
        'id' => 3,
        'menu_id' => 1003,
        'menu_id2' => 0,
        'name'  => '允许操作',
        'is_modules'    => 1,
        'auths' => 'Other@*,AdPosition@*',
    ],
    [
        'id' => 4,
        'menu_id' => 2001,
        'menu_id2' => 0,
        'name'  => '允许操作',
        'is_modules'    => 1,
        'auths' => 'System@web,System@web2,System@basic,System@water,System@index',
    ],
    // [
    //     'id' => 5,
    //     'menu_id' => 2001,
    //     'menu_id2' => 0,
    //     'name'  => '核心设置',
    //     'is_modules'    => 1,
    //     'auths' => 'System@web2,System@index',
    // ],
    // [
    //     'id' => 6,
    //     'menu_id' => 2001,
    //     'menu_id2' => 0,
    //     'name'  => '附件设置',
    //     'is_modules'    => 1,
    //     'auths' => 'System@basic,System@index',
    // ],
    // [
    //     'id' => 7,
    //     'menu_id' => 2001,
    //     'menu_id2' => 0,
    //     'name'  => '图片水印',
    //     'is_modules'    => 1,
    //     'auths' => 'System@water,System@index',
    // ],
    [
        'id' => 8,
        'menu_id' => 2003,
        'menu_id2' => 2003001,
        'name'  => 'SEO优化',
        'is_modules'    => 1,
        'auths' => 'Seo@*',
    ],
    [
        'id' => 9,
        'menu_id' => 2003,
        'menu_id2' => 2003002,
        'name'  => '友情链接',
        'is_modules'    => 1,
        'auths' => 'Links@*',
    ],
    [
        'id' => 10,
        'menu_id' => 2004,
        'menu_id2' => 2004001,
        'name'  => '管理员',
        'is_modules'    => 1,
        'auths' => 'Admin@admin_edit,Admin@admin_pwd',
    ],
    [
        'id' => 19,
        'menu_id' => 2004,
        'menu_id2' => 2004006,
        'name'  => '回收站',
        'is_modules'    => 1,
        'auths' => 'RecycleBin@*',
    ],
    [
        'id' => 12,
        'menu_id' => 2004,
        'menu_id2' => 2004003,
        'name'  => '模板管理',
        'is_modules'    => 1,
        'auths' => 'Filemanager@*',
    ],
    [
        'id' => 11,
        'menu_id' => 2004,
        'menu_id2' => 2004002,
        'name'  => '备份还原',
        'is_modules'    => 1,
        'auths' => 'Tools@*',
    ],
    // [
    //     'id' => 13,
    //     'menu_id' => 2004,
    //     'menu_id2' => 2004004,
    //     'name'  => '字段管理',
    //     'is_modules'    => 0,
    //     'auths' => 'Field@*',
    // ],
    [
        'id' => 15,
        'menu_id' => 2005,
        'menu_id2' => 0,
        'name'  => '插件应用',
        'is_modules'    => 1,
        'auths' => 'Weapp@index,Weapp@create,Weapp@pack,Weapp@upload,Weapp@disable,Weapp@install,Weapp@enable,Weapp@execute',
    ],
    [
        'id' => 16,
        'menu_id' => 2002,
        'menu_id2' => 0,
        'name'  => '允许操作',
        'is_modules'    => 1,
        'auths' => 'Uiset@*',
    ],
    [
        'id' => 17,
        'menu_id' => 2005,
        'menu_id2' => 0,
        'name'  => '插件卸载',
        'is_modules'    => 0,
        'auths' => 'Weapp@uninstall',
    ],
    [
        'id' => 18,
        'menu_id' => 2004,
        'menu_id2' => 2004001,
        'name'  => '权限组',
        'is_modules'    => 0,
        'auths' => 'Admin@admin_add,Admin@admin_del,AuthRole@*',
    ],
    [
        'id' => 20,
        'menu_id' => 2004,
        'menu_id2' => 2004007,
        'name'  => '频道模型',
        'is_modules'    => 1,
        'auths' => 'Channeltype@*,Field@*',
    ],
    [
        'id' => 14,
        'menu_id' => 2004,
        'menu_id2' => 2004005,
        'name'  => '清除缓存',
        'is_modules'    => 1,
        'auths' => 'System@clear_cache',
    ],
    [
        'id' => 21,
        'menu_id' => 2006,
        'menu_id2' => 0,
        'name'  => '允许操作',
        'is_modules'    => 1,
        'auths' => 'Member@*',
    ],
    [
        'id' => 22,
        'menu_id' => 2007,
        'menu_id2' => 0,
        'name'  => '允许操作',
        'is_modules'    => 1,
        'auths' => 'Index@switch_map',
    ],
];