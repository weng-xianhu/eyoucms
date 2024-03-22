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

namespace app\home\model;

use think\Model;
use think\Db;

/**
 * 简介
 */
class Single extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 获取单条记录
     * @author wengxianhu by 2017-7-26
     */
    public function getInfoByTypeid($typeid)
    {
        $cacheKey = md5("home_model_Single_getInfoByTypeid_{$typeid}");
        $result = cache($cacheKey);
        if (empty($result)) {
            $field = 'c.*, b.*, a.*, b.aid, a.id as typeid';
            $result = M('arctype')->field($field)
                ->alias('a')
                ->join('__ARCHIVES__ b', 'b.typeid = a.id', 'LEFT')
                ->join('__SINGLE_CONTENT__ c', 'c.aid = b.aid', 'LEFT')
                ->where('b.channel', 6)
                ->find($typeid);

            cache($cacheKey, $result, null, "arctype");
        }

        return $result;
    }
}