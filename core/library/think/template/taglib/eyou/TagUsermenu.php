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
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取会员菜单
     * @author wengxianhu by 2018-4-20
     */
    public function getUsermenu($currentstyle = '', $limit = '')
    {
        $map = array();
        $map['status'] = 1;
        $map['lang'] = $this->home_lang;

        $menuRow = Db::name("users_menu")->where($map)
            ->order('sort_order asc')
            ->limit($limit)
            ->select();
        $result = [];
        foreach ($menuRow as $key => $val) {
            $val['url'] = url($val['mca']);

            /*标记被选中效果*/
            if (preg_match('/^'.MODULE_NAME.'\/'.CONTROLLER_NAME.'\//i', $val['mca'])) {
                $val['currentstyle'] = $currentstyle;
            } else {
                $val['currentstyle'] = '';
            }
            /*--end*/

            $result[] = $val;
        }

        return $result;
    }
}