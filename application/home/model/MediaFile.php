<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海口快推科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */

namespace app\home\model;

use think\Db;
use think\Model;

/**
 * 视频文件
 */
class MediaFile extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }
    
    /**
     * 获取指定下载文章的所有文件
     * @author 小虎哥 by 2018-4-3
     */
    public function getMediaFile($aid, $field = '*')
    {
        $request = request();
        $result = Db::name('media_file')->field($field)
            ->where('aid', $aid)
            ->order('file_id asc')
            ->select();
        foreach ($result as $key => $val) {
            if (!empty($val['file_url'])) {
                $result[$key]['file_url'] = handle_subdir_pic($val['file_url'], 'media');
                if (!is_http_url($result[$key]['file_url'])){
                    $result[$key]['file_url'] = $request->domain() .$result[$key]['file_url'];
                }
            }
            if (!empty($val['file_time'])) {
                // 总秒数
                $time = intval($val['file_time']);
                // 可读时间
                $humanTime = '';
                if ($time > 3600) {// 判断小时
                    $hours = intval(($time / 3600));
                    $time  = $time % 3600;
                    if ($hours && $hours < 10) {
                        $humanTime .= '0' . $hours . ':';
                    } elseif ($hours && $hours > 10) {
                        $humanTime .= $hours . ':';
                    }
                } else {
                    $humanTime .= '00:';
                }
                if ($time > 60) {// 判断分钟
                    $minutes = intval(($time / 60));
                    $time    = $time % 60;
                    if ($minutes && $minutes < 10) {
                        $humanTime .= '0' . $minutes . ':';
                    } elseif ($minutes && $minutes > 10) {
                        $humanTime .= $minutes . ':';
                    }
                } else {
                    $humanTime .= '00:';
                }
                if ($time > 0) {// 判断秒钟
                    $seconds = $time;
                    if ($seconds && $seconds < 10) {
                        $humanTime .= '0' . $seconds . '';
                    } elseif ($seconds && $seconds >= 10) {
                        $humanTime .= $seconds . '';
                    }
                } else {
                    $humanTime .= '00';
                }
                $result[$key]['file_time'] = $humanTime;
            }
        }
        return $result;
    }
}