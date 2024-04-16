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
 * 搜索
 */
class TagSearch extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取热搜关键词
     */
    public function apiHotSearch($pagesize = 10,$keyword = '')
    {
        $where['is_hot'] = 1;
        if (!empty($keyword)){
            $where['word'] = ['LIKE',"%{$keyword}%"];
        }
        $list = Db::name('search_word')
            ->where($where)
            ->order('is_hot desc, searchNum desc')
            ->limit($pagesize)
            ->select();

        return $list;
    }
}