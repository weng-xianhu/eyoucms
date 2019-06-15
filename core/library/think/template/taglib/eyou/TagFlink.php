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
    public function getFlink($type = 'text', $limit = '')
    {
        if ($type == 'text') {
            $typeid = 1;
        } elseif ($type == 'image') {
            $typeid = 2;
        }

        $map = array();
        if (!empty($typeid)) {
            $map['typeid'] = array('eq', $typeid);
        }
        $result = M("links")->where($map)
            ->where('lang', $this->home_lang)
            ->order('sort_order asc')
            ->limit($limit)
            ->cache(true,EYOUCMS_CACHE_TIME,"links")
            ->select();
        foreach ($result as $key => $val) {
            $val['logo'] = get_default_pic($val['logo']);
            $val['target'] = ($val['target'] == 1) ? 'target="_blank"' : 'target="_self"';
            $result[$key] = $val;
        }

        return $result;
    }
}