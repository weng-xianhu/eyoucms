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
 * 单个广告信息
 */
class TagAd extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取单个广告信息
     * @author wengxianhu by 2018-4-20
     */
    public function getAd($aid = '')
    {
        if (empty($aid)) {
            return false;
        }

        $result = Db::name("ad")->field('id,title,links,litpic,intro,media_type')
            ->where(['id'=>$aid])
            ->cache(true,EYOUCMS_CACHE_TIME,"ad")
            ->find();
        if (!empty($result)) {
            if (1 == $result['media_type']) {
                $result['litpic'] = $this->get_default_pic($result['litpic']); // 默认无图封面
            } else if (2 == $result['media_type']) {
                $result['litpic'] = handle_subdir_pic($result['litpic'], 'media');
            }
            $result['intro'] = htmlspecialchars_decode($result['intro']); // 解码内容
            $result['intro'] = handle_subdir_pic($result['intro'], 'html'); // 支持子目录
            unset($result['media_type']);
        }

        return [
            'data'=> !empty($result) ? $result : false,
        ];
    }
}