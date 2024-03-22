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

namespace app\user\model;

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
     * 删除单条视频文章的所有视频
     * @author 小虎哥 by 2018-4-3
     */
    public function delVideoFile($aid = array())
    {
        if (!is_array($aid)) {
            $aid = array($aid);
        }
        $file_url_list = Db::name('media_file')->where(['aid' => ['IN', $aid]])->column('file_url');
        $result = Db::name('media_file')->where(['aid' => ['IN', $aid]])->delete();
        if ($result !== false) {
            Db::name('media_log')->where(['aid' => ['IN', $aid]])->delete();
            foreach ($file_url_list as $key => $val) {
                $file_url_tmp = preg_replace('#^(/[/\w\-]+)?(/uploads/media/)#i', '.$2', $val);
                if (!is_http_url($val) && file_exists($file_url_tmp)) {
                    @unlink($file_url_tmp);
                }
            }
        }
        \think\Cache::clear('media_file');

        return $result;
    }

    /**
     * 保存视频文章的视频
     * @author 小虎哥 by 2018-4-3
     */
    public function savefile($aid, $post = [], $opt = 'add')
    {
        $video_files = [];
        if (!empty($post['video'])) {
            $post['video'] = htmlspecialchars_decode($post['video']);
            $post['video'] = json_decode($post['video'], true);
            $addonFieldExt['total_video'] = count($post['video']);
            $addonFieldExt['total_duration'] = 0;
            foreach ($post['video'] as $k => $v) {
                $v['file_url'] = trim($v['file_url']);
                if (empty($v['file_url'])) {
                    $addonFieldExt['total_video'] -= 1;
                    continue;
                }
                $addonFieldExt['total_duration'] += $v['file_time'];
                $file_size = !empty($v['file_size']) ? $v['file_size'] : 0;
                $is_remote = 0;
                $file_ext = explode('.', $v['file_url']);
                $file_ext = preg_replace('/^(.*)\?(.*)$/i', '${1}', end($file_ext));
                $uhash = md5($v['file_url'] . $file_size);
                if (is_http_url($v['file_url'])) {
                    $is_remote = 1;
                    $md5file = '';
                } else {
                    if (preg_match('#^(/[\w]+)?(/uploads/media/)#i', $v['file_url'])) {
                        $file_path_tmp = preg_replace('#^(/[\w]+)?(/uploads/media/)#i', '$2', $v['file_url']);
                    } else {
                        $file_path_tmp = preg_replace('#^(' . $this->root_dir . ')?(/)#i', '$2', $v['file_url']);
                    }
                    $md5file = md5_file('.' . $file_path_tmp);
                }
                $arr = [
                    'aid' => $aid,
                    'title' => $post['title'],
                    'file_url' => !empty($v['file_url']) ? $v['file_url'] : '',
                    'file_time' => !empty($v['file_time']) ? $v['file_time'] : 0,
                    'file_title' => !empty($v['file_title']) ? $v['file_title'] : '',
                    'file_ext' => $file_ext,
                    'file_size' => $file_size,
                    'file_mime' => !empty($v['file_mime']) ? $v['file_mime'] : '',
                    'uhash' => $uhash,
                    'md5file' => $md5file,
                    'is_remote' => $is_remote,
                    'gratis' => !empty($v['gratis']) ? $v['gratis'] : 0,
                    'add_time' => getTime(),
                    'update_time' => getTime(),
                ];
                if (!empty($v['file_id'])){
                    $arr['file_id'] = $v['file_id'];
                }
                $video_files[] = $arr;
            }
        }

        if (!empty($video_files)) {
            if ('add' == $opt) {
                Db::name('media_file')->insertAll($video_files);
            } else if ('edit' == $opt) {
                $file_ids = [];
                $insert = [];
                foreach ($video_files as $k => $v) {
                    if (!empty($v['file_id'])) {
                        $file_ids[] = $v['file_id'];
                    } else {
                        unset($v['file_id']);
                        $insert[] = $v;
                        unset($video_files[$k]);
                    }
                }

                $file_url_list = Db::name('media_file')->where('aid', $aid)->column('file_url');
                Db::name('media_file')->where('aid', $aid)->where('file_id', 'not in', $file_ids)->delete();
                //更新
                $update = self::saveAll($video_files);
                //插入
                $insert = Db::name('media_file')->insertAll($insert);
                if (!empty($update) || !empty($insert)) {
                    \think\Cache::clear('media_file');
                    foreach ($video_files as $k => $v) {
                        $index_key = array_search($v['file_url'], $file_url_list);
                        if (false !== $index_key && 0 <= $index_key) {
                            unset($file_url_list[$index_key]);
                        }
                    }
                    try {
                        foreach ($file_url_list as $key => $val) {
                            $file_url_tmp = preg_replace('#^(/[/\w\-]+)?(/uploads/media/)#i', '.$2', $val);
                            if (!is_http_url($val) && file_exists($file_url_tmp)) {
                                @unlink($file_url_tmp);
                            }
                        }
                    } catch (\Exception $e) {
                    }
                }
            }
            $addonFieldExt['update_time'] = getTime();
            Db::name('media_content')->where('aid', $aid)->update($addonFieldExt);
        } else {
            if ('edit' == $opt) {
                Db::name('media_file')->where('aid', $aid)->delete();
                Db::name('media_content')->where('aid', $aid)->update(['total_duration' => 0, 'total_video' => 0, 'update_time' => getTime()]);
            }
        }
    }
}