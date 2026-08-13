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
use think\Cache;

/**
 * 标签索引列表
 */
class TagTagIndex extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取标签索引列表
     * @param string $limit 条数限制
     * @param string $orderby 排序方式
     * @return array
     */
    public function getTagIndex($limit = 10, $orderby = 'id asc')
    {
        $result = [];
        
        try {
        
        // 处理limit参数
        if (!stristr($limit, ',')) {
            $limit = "0, {$limit}";
        }
        $limitArr = explode(',', $limit);
        $offset = intval(trim($limitArr[0]));
        $length = intval(trim($limitArr[1]));

        // 查询标签索引数据
        $tagIndexList = Db::name('tagindex')
            ->field('id, tag, count, add_time, update_time')
            ->order($orderby)
            ->limit($offset, $length)
            ->select();

        if (!empty($tagIndexList)) {
            foreach ($tagIndexList as $key => $val) {
                // 处理标签名称
                $val['tag'] = htmlspecialchars_decode($val['tag']);
                
                // 时间格式化
                $val['add_time_format'] = $this->time_format($val['add_time']);
                $val['update_time_format'] = $this->time_format($val['update_time']);
                
                $result[] = $val;
            }
        }

            $total = Db::name('tagindex')->count();
            
        } catch (\Exception $e) {
            // 如果表不存在或字段不存在，返回空数据
            $result = [];
            $total = 0;
        }

        return [
            'data' => $result,
            'total' => $total,
        ];
    }
}
