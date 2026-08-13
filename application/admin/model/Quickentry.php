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

namespace app\admin\model;

use think\Db;
use think\Model;

/**
 * 快捷入口模型
 */
class Quickentry extends Model
{
    public $main_lang;
    public $admin_lang;

    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        $this->main_lang = get_main_lang();
        $this->admin_lang = get_admin_lang();
    }

    public function getMenuGroupList($query_data = [])
    {
        $globalConfig = tpCache('global');
        $usersConfig = getUsersConfigData('all');
        if (file_exists('./data/conf/memgift_open.txt')) {
            $usersConfig['memgift_open'] = 1;
        }
        $where = [
        	'type' => 15,
        	'status' => 1,
        ];
        if (!empty($query_data['where']) && is_array($query_data['where'])) $where = array_merge($where, $query_data['where']);
        if (empty($query_data['order'])) $query_data['order'] = 'menu_group asc, add_time asc, id asc';
        $list = Db::name('quickentry')->where($where)->order($query_data['order'])->select();
        foreach ($list as $key => $val) {
            // 权限验证
            $opt_access = 1;
            if (empty($val['auth_role'])) {
                $val['auth_role'] = "{$val['controller']}@{$val['action']}";
            }
            $auth_role_arr = explode('|', $val['auth_role']);
            foreach ($auth_role_arr as $_k => $_v) {
                if (!is_check_access($_v)) {
                    $opt_access = 0;
                    break;
                }
            }
            // 其他逻辑的权限验证
            if (1 == $opt_access && empty($val['is_lang'])) {
                if ($this->main_lang != $this->admin_lang) {
                    $opt_access = 0;
                }
            }
            if (1 == $opt_access) {
                if (2004006 == $val['menu_id']) {
                    if (!empty($globalConfig['web_recycle_switch']) && 1 == $globalConfig['web_recycle_switch']) {
                        $opt_access = 0;
                    }
                } else if (2002 == $val['menu_id']) {
                    if (!file_exists(ROOT_PATH.'template/'.TPL_THEME.'pc/uiset.txt') && !file_exists(ROOT_PATH.'template/'.TPL_THEME.'mobile/uiset.txt')) {
                        $opt_access = 0;
                    }
                } else if (2004023 == $val['menu_id']) {
                    // 列出功能地图里已使用的模块
                    $shopLogic = new \app\admin\logic\ShopLogic;
                    $useFunc = $shopLogic->useFuncLogic();
                    if (!in_array('memgift', $useFunc) || $globalConfig['php_servicemeal'] <= 1 || empty($usersConfig['memgift_open'])) {
                        $opt_access = 0;
                    }
                } else if (2004026 == $val['menu_id']) {
                    $foreign_authorize = tpSetting('foreign.foreign_authorize', [], 'cn');
                    if ($globalConfig['php_servicemeal'] < 1 && $foreign_authorize != 1) {
                        $opt_access = 0;
                    }
                } else if (2004027 == $val['menu_id']) {
                    if ($globalConfig['php_servicemeal'] < 5) {
                        $opt_access = 0;
                    }
                }
            }
            $val['opt_access'] = $opt_access;

            // 在页面上显示或隐藏
            $show_status = 1;
            if (2004019 == $val['menu_id']) {
                if (empty($globalConfig['web_citysite_open'])) {
                    $show_status = 0;
                }
            }
            $val['show_status'] = $show_status;

            // url
            $val['href'] = url("{$val['controller']}/{$val['action']}", $val['vars']);

            $list[$key] = $val;
        }

        return $list;
    }
}