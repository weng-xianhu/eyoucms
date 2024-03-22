<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海口快推科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */

namespace think\template\taglib\eyou;

/**
 * 外观调试的最初引入文件
 */
class TagUi extends Base
{
    public $uiset = 'off';

    protected function _initialize()
    {
        parent::_initialize();
        $this->uiset = I("param.uiset/s", "off");
        $this->uiset = trim($this->uiset, '/');
    }

    /**
     * 纯文本编辑
     * @author wengxianhu by 2018-4-20
     */
    public function getUi()
    {
        $v = I('param.v/s', 'pc');
        $v = trim($v, '/');
        $parseStr = '';
        if ("on" == $this->uiset) {

            /*权限控制 by 小虎哥*/
            $admin_info = session('admin_info');
            if (!empty($admin_info) && 0 < intval($admin_info['role_id'])) {
                if(empty($admin_info['auth_role_info'])){
                    return '';
                }
                $auth_role_info = $admin_info['auth_role_info'];
                $permission = $auth_role_info['permission'];
                $auth_rule = include APP_PATH.'admin/conf/auth_rule.php';
                $all_auths = []; // 系统全部权限对应的菜单ID
                $admin_auths = []; // 用户当前拥有权限对应的菜单ID
                $diff_auths = []; // 用户没有被授权的权限对应的菜单ID
                foreach($auth_rule as $key => $val){
                    $all_auths = array_merge($all_auths, explode(',', $val['menu_id']), explode(',', $val['menu_id2']));
                    if (in_array($val['id'], $permission['rules'])) {
                        $admin_auths = array_merge($admin_auths, explode(',', $val['menu_id']), explode(',', $val['menu_id2']));
                    }
                }
                $all_auths = array_unique($all_auths);
                $admin_auths = array_unique($admin_auths);
                $diff_auths = array_diff($all_auths, $admin_auths);

                if(in_array(2002, $diff_auths)){
                    return '';
                }
            }
            /*--end*/
            $version = getCmsVersion();
            $webConfig = tpCache('web');
            $web_adminbasefile = !empty($webConfig['web_adminbasefile']) ? $webConfig['web_adminbasefile'] : $this->root_dir.'/login.php'; // 后台入口文件路径
            $iframe = input('param.iframe/d');
            $parseStr .= "<script language='javascript' type='text/javascript' src='".get_absolute_url("{$this->root_dir}/public/plugins/layer-v3.1.0/layer.js?v={$version}")."'></script>";
            $parseStr .= "<script type='text/javascript'>var admin_basefile = '{$web_adminbasefile}'; var v = '{$v}'; var __lang__ = '".self::$home_lang."'; var __root_dir__ = '{$this->root_dir}'; var __urltypeid__ = '".$this->tid."'; var __urlaid__ = '".$this->aid."'; var __iframe__ = '{$iframe}';</script>";

            if ($v == 'mobile' && 1 == $iframe) {
                $parseStr .= "<link rel='stylesheet' type='text/css' href='".get_absolute_url("{$this->root_dir}/public/static/common/css/eyou_m.css?v={$version}")."' />";
                $parseStr .= "<script language='javascript' type='text/javascript' src='".get_absolute_url("{$this->root_dir}/public/static/common/js/eyou_m.js?v={$version}")."'></script>";
            } else {
                $parseStr .= "<link href='".get_absolute_url("{$this->root_dir}/public/static/common/css/eyou.css?v={$version}")."' rel='stylesheet' media='screen' type='text/css' />";
                $parseStr .= "<script language='javascript' type='text/javascript' src='".get_absolute_url("{$this->root_dir}/public/static/common/js/eyou.js?v={$version}")."'></script>";
            }
        }

        return $parseStr;
    }
}