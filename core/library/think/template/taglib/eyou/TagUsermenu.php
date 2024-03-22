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

namespace think\template\taglib\eyou;

use think\Db;

/**
 * 会员菜单
 */
class TagUsermenu extends Base
{
    public $usersTplVersion    = '';

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->usersTplVersion = getUsersTplVersion();
    }

    /**
     * 获取会员菜单
     * @author wengxianhu by 2018-4-20
     */
    public function getUsermenu($currentclass = '', $limit = '')
    {
        if ($this->usersTplVersion == 'v2') {
            return $this->get_usermenu_v2($currentclass, $limit);
        } else if ($this->usersTplVersion == 'v4') {
            return $this->get_usermenu_v4($currentclass, $limit);
        } else {
            return $this->get_usermenu_v1($currentclass, $limit);
        }
    }

    /**
     * 获取会员菜单
     * @author wengxianhu by 2018-4-20
     */
    private function get_usermenu_v1($currentclass = '', $limit = '')
    {
        $map = array();
        $map['status'] = 1;
        $map['version'] = ['IN', ['weapp','v1']];

        $menuRow = Db::name("users_menu")->where($map)
            ->order('sort_order asc,id ASC')
            ->limit($limit)
            ->select();
        $result = [];
        foreach ($menuRow as $key => $val) {
            $val['url'] = url($val['mca']);
            if ('Users' == CONTROLLER_NAME){
                if (preg_match('/^'.MODULE_NAME.'\/'.CONTROLLER_NAME.'\/'.ACTION_NAME.'/i', $val['mca'])) {
                    $val['currentclass'] = $val['currentstyle'] = $currentclass;
                } else {
                    $val['currentclass'] = $val['currentstyle'] = '';
                }
            }else{
                /*标记被选中效果*/
                if (preg_match('/^'.MODULE_NAME.'\/'.CONTROLLER_NAME.'\//i', $val['mca'])) {
                    $val['currentclass'] = $val['currentstyle'] = $currentclass;
                } else {
                    $val['currentclass'] = $val['currentstyle'] = '';
                }
                /*--end*/
            }

            $result[] = $val;
        }

        return $result;
    }

    /**
     * 获取会员菜单
     * @author wengxianhu by 2018-4-20
     */
    private function get_usermenu_v2($currentclass = '', $limit = '')
    {
        $map = array();
        $map['status'] = 1;
        $map['version'] = ['IN', ['v2']];

        $menuRow = Db::name("users_menu")->where($map)
            ->order('sort_order asc,id ASC')
            ->limit($limit)
            ->select();
        $result = [];
        foreach ($menuRow as $key => $val) {
            $val['url'] = url($val['mca']);
            if ($val['active_url']) $val['active_url'] = explode('|', $val['active_url']);

            if (in_array(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,$val['active_url'])) {
                $val['currentclass'] = $val['currentstyle'] = $currentclass;
            }else{
                $val['currentclass'] = $val['currentstyle'] = '';
            }

            $result[] = $val;
        }

        return $result;
    }

    /**
     * 获取会员菜单
     * @author wengxianhu by 2018-4-20
     */
    private function get_usermenu_v4($currentclass = '', $limit = '')
    {
        $map = array();
        $map['status'] = 1;
        $map['version'] = ['IN', ['v4']];

        $menuRow = Db::name("users_menu")->where($map)
            ->order('sort_order asc,id ASC')
            ->limit($limit)
            ->select();
        $result = [];
        foreach ($menuRow as $key => $val) {
            $val['url'] = url($val['mca']);
            if ($val['active_url']) $val['active_url'] = explode('|', $val['active_url']);

            if (in_array(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,$val['active_url'])) {
                $val['currentclass'] = $val['currentstyle'] = $currentclass;
            }else{
                $val['currentclass'] = $val['currentstyle'] = '';
            }

            $result[] = $val;
        }

        return $result;
    }
}