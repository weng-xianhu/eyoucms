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

$icon_arr = array(
    'article' => 'fa fa-file-text',
    'product'  => 'fa fa-cubes',
    'images'  => 'fa fa-file-picture-o',
    'download'  => 'fa fa-cloud-download',
    'single'  => 'fa fa-bookmark-o',
    'about'  => 'fa fa-minus',
    'job'  => 'fa fa-minus',
    'guestbook'  => 'fa fa-file-text-o',
    'feedback'  => 'fa fa-file-text-o',
);
$main_lang= get_main_lang();
$admin_lang = get_admin_lang();
$domain = request()->domain();
$default_words = array();
$default_addcontent = array();

// 获取有栏目的模型列表
$channel_list = model('Channeltype')->getArctypeChannel('yes');
foreach ($channel_list as $key => $val) {
    $default_words[] = array(
        'name'  => $val['ntitle'],
        'action'  => 'index',
        'controller'  => $val['ctl_name'],
        'url'  => $val['typelink'],
        'icon'  => $icon_arr[$val['nid']],
    );
    if (!in_array($val['nid'], array('single','guestbook','feedback'))) {
        $default_addcontent[] = array(
            'name'  => $val['ntitle'],
            'action'  => 'add',
            'controller'  => $val['ctl_name'],
            'url'  => $val['typelink'],
            'icon'  => $icon_arr[$val['nid']],
        );
    }
}

/*PC端可视编辑URl*/
$uiset_pc_arr = [];
if (file_exists(ROOT_PATH.'template/pc/uiset.txt')) {
    $uiset_pc_arr = array(
        'url' => url('Uiset/pc', array(), true, $domain),
        'is_menu' => 1,
    );
}
/*--end*/

/*手机端可视编辑URl*/
$uiset_mobile_arr = [];
if (file_exists(ROOT_PATH.'template/mobile/uiset.txt')) {
    $uiset_mobile_arr = array(
        'url' => url('Uiset/mobile', array(), true, $domain),
        'is_menu' => 1,
    );
}
/*--end*/

/*清理数据URl*/
$uiset_data_url = '';
if (!empty($uiset_pc_arr) || !empty($uiset_mobile_arr)) {
    $uiset_data_url = url('Uiset/ui_index', array(), true, $domain);
}
/*--end*/

/*可视编辑URL*/
$uiset_index_arr = array();
if (!empty($uiset_pc_arr) || !empty($uiset_mobile_arr)) {
    $uiset_index_arr = array(
        'url' => url('Uiset/index', array(), true, $domain),
        'is_menu' => 1,
    );
}
/*--end*/

/*SEO优化URl*/
$seo_index_arr = array();
if ($main_lang == $admin_lang) {
    $seo_index_arr = array(
        'is_menu' => 1,
    );
}
/*--end*/

/*备份还原URl*/
$tools_index_arr = array();
if ($main_lang == $admin_lang) {
    $tools_index_arr = array(
        'is_menu' => 1,
    );
}
/*--end*/

/*频道模型URl*/
$channeltype_index_arr = array();
if ($main_lang == $admin_lang) {
    $channeltype_index_arr = array(
        'is_menu' => 1,
    );
}
/*--end*/

/*回收站URl*/
$recyclebin_index_arr = array();
if ($main_lang == $admin_lang) {
    $recyclebin_index_arr = array(
        'is_menu' => 1,
    );
}
/*--end*/

/*插件应用URl*/
$weapp_index_arr = array();
// $weappDirList = glob(ROOT_PATH.'weapp/*');
if (1 == tpCache('web.web_weapp_switch') && file_exists(ROOT_PATH.'weapp')) {
    $weapp_index_arr = array(
        'is_menu' => 1,
    );
}
/*--end*/

/*会员中心URl*/
$users_index_arr = array();
if (1 == tpCache('web.web_users_switch') && $main_lang == $admin_lang) {
    $users_index_arr = array(
        'is_menu' => 1,
        'is_modules' => 1,
    );
}
/*--end*/

/**
 * 权限模块属性说明
 * array
 *      id  主键ID
 *      parent_id   父ID
 *      name    模块名称
 *      controller  控制器
 *      action  操作名
 *      url     跳转链接(控制器与操作名为空时，才使用url)
 *      target  打开窗口方式
 *      icon    菜单图标
 *      grade   层级
 *      is_menu 是否显示菜单
 *      is_modules  是否显示权限模块分组
 *      child   子模块
 */
return  array(
    '1000'=>array(
        'id'=>1000,
        'parent_id'=>0,
        'name'=>'',
        'controller'=>'',
        'action'=>'',
        'url'=>'',
        'target'=>'workspace',
        'grade'=>0,
        'is_menu'=>1,
        'is_modules'=>1,
        'child'=>array(
            '1001' => array(
                'id'=>1001,
                'parent_id'=>1000,
                'name' => '栏目管理',
                'controller'=>'Arctype',
                'action'=>'index',
                'url'=>'', 
                'target'=>'workspace',
                'icon'=>'fa fa-sitemap',
                'grade'=>1,
                'is_menu'=>1,
                'is_modules'=>1,
                'child' => array(),
            ),
            '1002' => array(
                'id'=>1002,
                'parent_id'=>1000,
                'name' => '内容管理',
                'controller'=>'Archives',
                'action'=>'index',
                'url'=>'', 
                'target'=>'workspace',
                'icon'=>'fa fa-list',
                'grade'=>1,
                'is_menu'=>1,
                'is_modules'=>1,
                'child' => array(),
            ),
            '1003' => array(
                'id'=>1003,
                'parent_id'=>1000,
                'name' => '广告管理',
                'controller'=>'AdPosition',
                'action'=>'index',
                'url'=>'', 
                'target'=>'workspace',
                'icon'=>'fa fa-image',
                'grade'=>1,
                'is_menu'=>1,
                'is_modules'=>1,
                'child' => array(),
            ),
        ),
    ),
        
    '2000'=>array(
        'id'=>2000,
        'parent_id'=>0,
        'name'=>'设置',
        'controller'=>'',
        'action'=>'',
        'url'=>'', 
        'target'=>'workspace',
        'grade'=>0,
        'is_menu'=>1,
        'is_modules'=>1,
        'child'=>array(
            '2001' => array(
                'id'=>2001,
                'parent_id'=>2000,
                'name' => '基本信息',
                'controller'=>'System',
                'action'=>'web',
                'url'=>'', 
                'target'=>'workspace',
                'icon'=>'fa fa-cog',
                'grade'=>1,
                'is_menu'=>1,
                'is_modules'=>1,
                'child' => array(),
            ),
            '2002' => array(
                'id'=>2002,
                'parent_id'=>2000,
                'name' => '可视编辑',
                'controller'=>'Weapp',
                'action'=>'index',
                'url'=>isset($uiset_index_arr['url']) ? $uiset_index_arr['url'] : '',
                'target'=>'workspace',
                'icon'=>'fa fa-tachometer',
                'grade'=>1,
                'is_menu'=>isset($uiset_index_arr['is_menu']) ? $uiset_index_arr['is_menu'] : 0,
                'is_modules'=>1,
                'child'=>array(
                    '2002001' => array(
                        'id'=>2002001,
                        'parent_id'=>2002,
                        'name' => '电脑版',
                        'controller'=>'',
                        'action'=>'',
                        'url'=>isset($uiset_pc_arr['url']) ? $uiset_pc_arr['url'] : '',
                        'target'=>'_blank',
                        'icon'=>'fa fa-desktop',
                        'grade'=>2,
                        'is_menu'=>isset($uiset_pc_arr['is_menu']) ? $uiset_pc_arr['is_menu'] : 0,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                    '2002002' => array(
                        'id'=>2002002,
                        'parent_id'=>2002,
                        'name' => '手机版',
                        'controller'=>'',
                        'action'=>'',
                        'url'=>isset($uiset_mobile_arr['url']) ? $uiset_mobile_arr['url'] : '',
                        'target'=>'_blank',
                        'icon'=>'fa fa-mobile',
                        'grade'=>2,
                        'is_menu'=>isset($uiset_mobile_arr['is_menu']) ? $uiset_mobile_arr['is_menu'] : 0,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                    '2002003' => array(
                        'id'=>2002003,
                        'parent_id'=>2002,
                        'name' => '数据清理',
                        'controller'=>'Uiset',
                        'action'=>'ui_index',
                        'url'=>'', 
                        'target'=>'workspace',
                        'icon'=>'fa fa-undo',
                        'grade'=>2,
                        'is_menu'=>1,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                ),
            ),
            '2003' => array(
                'id'=>2003,
                'parent_id'=>2000,
                'name' => '营销设置',
                'controller'=>'Other',
                'action'=>'index',
                'url'=>'', 
                'target'=>'workspace',
                'icon'=>'fa fa-paper-plane',
                'grade'=>1,
                'is_menu'=>1,
                'is_modules'=>1,
                'child'=>array(
                    '2003001' => array(
                        'id'=>2003001,
                        'parent_id'=>2003,
                        'name' => 'SEO优化', 
                        'controller'=>'Seo',
                        'action'=>'index',
                        'url'=>'',
                        'target'=>'workspace',
                        'icon'=>'fa fa-newspaper-o',
                        'grade'=>2,
                        'is_menu'=>isset($seo_index_arr['is_menu']) ? $seo_index_arr['is_menu'] : 0,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                    '2003002' => array(
                        'id'=>2003002,
                        'parent_id'=>2003,
                        'name' => '友情链接', 
                        'controller'=>'Links',
                        'action'=>'index', 
                        'url'=>'', 
                        'target'=>'workspace',
                        'icon'=>'fa fa-link',
                        'grade'=>2,
                        'is_menu'=>1,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                ),
            ),
            '2004' => array(
                'id'=>2004,
                'parent_id'=>2000,
                'name' => '高级选项',
                'controller'=>'Senior',
                'action'=>'index',
                'url'=>'', 
                'target'=>'workspace',
                'icon'=>'fa fa-code',
                'grade'=>1,
                'is_menu'=>1,
                'is_modules'=>1,
                'child' => array(
                    '2004001' => array(
                        'id'=>2004001,
                        'parent_id'=>2004,
                        'name' => '管理员', 
                        'controller'=>'Admin',
                        'action'=>'index', 
                        'url'=>'', 
                        'target'=>'workspace',
                        'icon'=>'fa fa-user',
                        'grade'=>2,
                        'is_menu'=>1,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                    '2004006' => array(
                        'id'=>2004006,
                        'parent_id'=>2004,
                        'name' => '回收站',
                        'controller'=>'RecycleBin',
                        'action'=>'arctype_index',
                        'url'=>'',
                        'target'=>'workspace',
                        'icon'=>'fa fa-recycle',
                        'grade'=>2,
                        'is_menu'=>isset($recyclebin_index_arr['is_menu']) ? $recyclebin_index_arr['is_menu'] : 0,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                    '2004003' => array(
                        'id'=>2004003,
                        'parent_id'=>2004,
                        'name' => '模板管理', 
                        'controller'=>'Filemanager',
                        'action'=>'index', 
                        'url'=>'', 
                        'target'=>'workspace',
                        'icon'=>'fa fa-folder-open',
                        'grade'=>2,
                        'is_menu'=>1,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                    '2004002' => array(
                        'id'=>2004002,
                        'parent_id'=>2004,
                        'name' => '备份还原', 
                        'controller'=>'Tools',
                        'action'=>'index',
                        'url'=>'',
                        'target'=>'workspace',
                        'icon'=>'fa fa-database',
                        'grade'=>2,
                        'is_menu'=>isset($tools_index_arr['is_menu']) ? $tools_index_arr['is_menu'] : 0,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                    // '2004004' => array(
                    //     'id'=>2004004,
                    //     'parent_id'=>2004,
                    //     'name' => '字段管理', 
                    //     'controller'=>isset($field_cindex_arr['controller']) ? $field_cindex_arr['controller'] : '',
                    //     'action'=>isset($field_cindex_arr['action']) ? $field_cindex_arr['action'] : '',
                    //     'url'=>isset($field_cindex_arr['url']) ? $field_cindex_arr['url'] : '',
                    //     'target'=>'workspace',
                    //     'icon'=>'fa fa-cogs',
                    //     'grade'=>2,
                    //     'is_menu'=>0,
                    //     'is_modules'=>0,
                    //     'child' => array(),
                    // ),
                    '2004007' => array(
                        'id'=>2004007,
                        'parent_id'=>2004,
                        'name' => '频道模型',
                        'controller'=>'Channeltype',
                        'action'=>'index',
                        'url'=>'',
                        'target'=>'workspace',
                        'icon'=>'fa fa-cube',
                        'grade'=>2,
                        'is_menu'=>isset($channeltype_index_arr['is_menu']) ? $channeltype_index_arr['is_menu'] : 0,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                    '2004005' => array(
                        'id'=>2004005,
                        'parent_id'=>2004,
                        'name' => '清除缓存',
                        'controller'=>'System',
                        'action'=>'clear_cache', 
                        'url'=>'', 
                        'target'=>'workspace',
                        'icon'=>'fa fa-undo',
                        'grade'=>2,
                        'is_menu'=>1,
                        'is_modules'=>0,
                        'child' => array(),
                    ),
                ),
            ),
            '2005' => array(
                'id'=>2005,
                'parent_id'=>2000,
                'name' => '插件应用',
                'controller'=>'Weapp',
                'action'=>'index',
                'url'=>'',
                'target'=>'workspace',
                'icon'=>'fa fa-futbol-o',
                'grade'=>1,
                'is_menu'=>isset($weapp_index_arr['is_menu']) ? $weapp_index_arr['is_menu'] : 0,
                'is_modules'=>0,
                'child'=>array(),
            ),
            '2006' => array(
                'id'=>2006,
                'parent_id'=>2000,
                'name' => '会员中心',
                'controller'=>'Member',
                'action'=>'users_index',
                'url'=>'',
                'target'=>'workspace',
                'icon'=>'fa fa-user',
                'grade'=>1,
                'is_menu'=>isset($users_index_arr['is_menu']) ? $users_index_arr['is_menu'] : 0,
                'is_modules'=>isset($users_index_arr['is_modules']) ? $users_index_arr['is_modules'] : 0,
                'child' => array(),
            ),
            '2007' => array(
                'id'=>2007,
                'parent_id'=>2000,
                'name' => '功能开关',
                'controller'=>'Index',
                'action'=>'switch_map',
                'url'=>'',
                'target'=>'workspace',
                'icon'=>'fa fa-toggle-on',
                'grade'=>1,
                'is_menu'=>0,
                'is_modules'=>1,
                'child' => array(),
            ),
        ),
    ),
);