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
use think\Request;

/**
 * 内容页上下篇
 */
class TagPrenext extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取内容页上下篇
     * @author wengxianhu by 2018-4-20
     */
    public function getPrenext($aid = '', $typeid = '', $get = 'pre', $titlelen = 100)
    {
        !empty($aid) && $this->aid = $aid;
        if (empty($this->aid)) return false;
        
        !empty($typeid) && $this->tid = $typeid;
        if (empty($this->tid)) {
            $this->tid = Db::name('archives')->where('aid', $this->aid)->value('typeid');
        }

        $result = [];
        if ($get == 'pre' || $get == 'all') { // 上一篇
            $preDetail = Db::name('archives')->field('a.aid, a.typeid, a.title')
                ->alias('a')
                ->where([
                    'a.typeid'  => $this->tid,
                    'a.aid'     => ['LT', $this->aid],
                    'a.arcrank' => ['EGT', 0],
                    'a.status'  => 1,
                    'a.is_del'  => 0,
                    'a.lang'    => $this->main_lang,
                ])
                ->order('a.aid desc')
                ->find();
            if (!empty($preDetail)) {
                $preDetail['title'] = text_msubstr($preDetail['title'], 0, $titlelen, false);
                // $preDetail['arcurl'] = '/pages/article/view?aid='.$preDetail['aid'];
                // $preDetail['typeurl'] = '/pages/article/list?typeid='.$preDetail['typeid'];
                $result['preDetail'] = $preDetail;
            }
        }
        if ($get == 'next' || $get == 'all') { // 下一篇
            $nextDetail = Db::name('archives')->field('a.aid, a.typeid, a.title')
                ->alias('a')
                ->where([
                    'a.typeid'  => $this->tid,
                    'a.aid'     => ['GT', $this->aid],
                    'a.arcrank' => ['EGT', 0],
                    'a.status'  => 1,
                    'a.is_del'  => 0,
                    'a.lang'    => $this->main_lang,
                ])
                ->order('a.aid asc')
                ->find();
            if (!empty($nextDetail)) {
                $nextDetail['title'] = text_msubstr($nextDetail['title'], 0, $titlelen, false);
                // $nextDetail['arcurl'] = '/pages/article/view?aid='.$nextDetail['aid'];
                // $nextDetail['typeurl'] = '/pages/article/list?typeid='.$nextDetail['typeid'];
                $result['nextDetail'] = $nextDetail;
            }
        }

        return !empty($result) ? $result : false;
    }
}