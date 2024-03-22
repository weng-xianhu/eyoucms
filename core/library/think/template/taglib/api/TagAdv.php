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
 * 广告
 */
class TagAdv extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取广告
     * @author wengxianhu by 2018-4-20
     */
    public function getAdv($pid = '', $orderby = '', $limit = '')
    {
        if (empty($pid)) {
            return false;
        }

        // 排序
        switch ($orderby) {
            case 'hot':
            case 'click':
                $orderby = 'a.click desc';
                break;

            case 'now':
            case 'new': // 兼容写法
                $orderby = 'a.add_time desc';
                break;
                
            case 'id':
                $orderby = 'a.id desc';
                break;

            case 'sort_order':
                $orderby = 'a.sort_order asc';
                break;

            case 'rand':
                $orderby = 'rand()';
                break;
            
            default:
                if (empty($orderby)) {
                    $orderby = 'a.sort_order asc, a.id desc';
                }
                break;
        }
        $result = Db("ad")->alias('a')
            ->field("a.id,a.title,a.links,a.litpic,a.intro,a.media_type")
            ->join('__AD_POSITION__ b', 'b.id = a.pid', 'LEFT')
            ->where([
                'a.pid' => $pid,
                'b.status'  => 1,
            ])
            ->orderRaw($orderby)
            ->limit($limit)
            ->cache(true,EYOUCMS_CACHE_TIME,"ad")
            ->select();
        
        foreach ($result as $key => $val) {
            if (1 == $val['media_type']) {
                $val['litpic'] = $this->get_default_pic($val['litpic']); // 默认无图封面
            } else if (2 == $val['media_type']) {
                $val['litpic'] = handle_subdir_pic($val['litpic'], 'media');
            }
            $val['intro'] = htmlspecialchars_decode($val['intro']);
            $val['intro'] = handle_subdir_pic($val['intro'], 'html'); // 支持子目录
            unset($val['media_type']);
            $result[$key] = $val;
        }

        return [
            'data'=> !empty($result) ? $result : false,
        ];
    }
}