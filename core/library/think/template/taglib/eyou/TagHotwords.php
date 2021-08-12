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
 * 获取网站搜索的热门关键字
 */
class TagHotwords extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取网站搜索的热门关键字
     * @author wengxianhu by 2018-4-20
     */
    public function getHotwords($num = 0, $subday = 0, $maxlength = 0)
    {
        $nowtime = getTime();
        if(empty($subday)) $subday = 365;
        if(empty($num)) $num = 6;
        if(empty($maxlength)) $maxlength = 20;
        $maxlength = $maxlength + 1;
        $mintime = $nowtime - ($subday * 24 * 3600);

        if($sort == 'rand') $orderby = 'rand() ';
        else if($sort == 'sort_order') $orderby=' sort_order ASC, id desc ';
        else if($sort == 'hot') $orderby=' searchNum DESC, id desc ';
        else if($sort == 'new') $orderby=' id DESC ';
        else $orderby = 'sort_order asc, searchNum desc, id desc ';

        $result = Db::name('search_word')->field('word,searchNum')
            ->where("update_time > {$mintime} AND length(word) < {$maxlength}")
            ->orderRaw($orderby)
            ->limit($num)
            ->select();
        $searchurl = url('home/Search/lists');
        foreach ($result as $key => $val) {
            $url = $searchurl;
            if (stristr($searchurl, '?')) {
                $url .= "&keywords=".urlencode($val['word']);
            } else {
                $url .= "?keywords=".urlencode($val['word']);
            }
            $val['url'] = $url;

            $result[$key] = $val;
        }

        return $result;
    }
}