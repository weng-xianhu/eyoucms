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

namespace think\template\taglib\api;

use think\Db;

/**
 * 友情链接
 */
class TagFlink extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取友情链接
     * @author wengxianhu by 2018-4-20
     */
    public function getFlink($type = 'text', $limit = '', $groupid = 1, $titlelen = 100)
    {
        if ($type == 'text') {
            $typeid = 1;
        } elseif ($type == 'image') {
            $typeid = 2;
        }

        $map = array();
        if (!empty($groupid) && $groupid != 'all') {
            $map['groupid'] = array('eq', $groupid);
        }
        if (!empty($typeid)) {
            $map['typeid'] = array('eq', $typeid);
        }
        $map['status'] = 1;
        $result = Db::name("links")->where($map)
            ->order('sort_order asc')
            ->limit($limit)
            ->cache(true,EYOUCMS_CACHE_TIME,"links")
            ->select();
        foreach ($result as $key => $val) {
            $val['title'] = text_msubstr($val['title'], 0, $titlelen, false);
            $val['logo'] = $this->get_default_pic($val['logo']);
            $result[$key] = $val;
        }

        return [
            'data'=> !empty($result) ? $result : false,
        ];
    }
}