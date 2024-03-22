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

namespace app\admin\model;

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
        $file_url_list = Db::name('media_file')->where(['aid'=>['IN', $aid]])->column('file_url');
        $result = Db::name('media_file')->where(['aid'=>['IN', $aid]])->delete();
        if ($result !== false) {
            Db::name('media_log')->where(['aid'=>['IN', $aid]])->delete();
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
     * 获取指定下载文章的所有文件
     * @author 小虎哥 by 2018-4-3
     */
    public function getMediaFile($aid, $field = '*')
    {
        if (!is_dir('./weapp/Videogroup/')) {
            $result = Db::name('media_file')->field($field)
                ->where('aid', $aid)
                ->order('sort_order asc,file_id asc')
                ->select();
        } else { // 安装了视频章节分组插件
            $videogroupLogic = new \weapp\Videogroup\logic\VideogroupLogic;
            $weappData = $videogroupLogic->getWeappData();
            if (!empty($weappData['is_open'])) { // 开启
                $result = Db::name('media_file')->alias('a')
                    ->field('a.*, b.group_id as video_group_id,c.sort_order as c_sort,c.group_name')
                    ->join('weapp_videogroup_file b', 'a.file_id = b.file_id', 'LEFT')
                    ->join('weapp_videogroup c', 'c.aid = a.aid and c.group_id = b.group_id', 'LEFT')
                    ->where('a.aid', $aid)
                    ->order('c_sort asc,a.sort_order asc,a.file_id asc')
                    ->select();
                if (!empty($result)){
                    foreach ($result as $k => $v){
                        if (empty($v['video_group_id'])){
                            $result[] = $v;
                            unset($result[$k]);
                        }else{
                            break;
                        }
                    }
                    $result = array_merge($result);
                }
            } else { // 关闭
                $result = Db::name('media_file')->field($field)
                    ->where('aid', $aid)
                    ->order('sort_order asc,file_id asc')
                    ->select();
            }
        }

        return $result;
    }

    /**
     * 保存视频文章的视频
     * @author 小虎哥 by 2018-4-3
     */
    public function savefile($aid, $video_files = [], $opt = 'add')
    {
        if (!empty($video_files)) {
            if ('add' == $opt) {
                $redata = self::saveAll($video_files);
                // 视频章节分组插件
                if (is_dir('./weapp/Videogroup/')) {
                    $videogroupfiles = [];
                    foreach ($redata as $k1 => $v1) {
                        $video_group_id = $v1->getData('video_group_id');
                        $videogroupfiles[] = [
                            'file_id' => $v1->getData('file_id'),
                            'aid' => $v1->getData('aid'),
                            'group_id' => intval($video_group_id),
                            'add_time' => getTime(),
                            'update_time' => getTime(),
                        ];
                    }
                    if (!empty($videogroupfiles)) {
                        Db::name('weapp_videogroup_file')->insertAll($videogroupfiles);
                    }
                }
            } else if ('edit' == $opt) {
                $videogroupfiles = [];
                $file_ids = [];
                $insert = [];
                foreach ($video_files as $k =>$v){
                    if (!empty($v['file_id'])){
                        $file_ids[] = $v['file_id'];
                    }else{
                        unset($v['file_id']);
                        $insert[] = $v;
                        unset($video_files[$k]);
                    }
                }

                $file_url_list = Db::name('media_file')->where('aid',$aid)->column('file_url');
                Db::name('media_file')->where('aid',$aid)->where('file_id','not in',$file_ids)->delete();
                // 更新
                $update = self::saveAll($video_files);
                foreach ($update as $k1 => $v1) {
                    $video_group_id = $v1->getData('video_group_id');
                    $videogroupfiles[] = [
                        'file_id' => $v1->getData('file_id'),
                        'aid' => $v1->getData('aid'),
                        'group_id' => intval($video_group_id),
                        'add_time' => getTime(),
                        'update_time' => getTime(),
                    ];
                }
                //插入
                $insert = self::saveAll($insert);
                if (!empty($update) || !empty($insert)) {
                    foreach ($insert as $k1 => $v1) {
                        $video_group_id = $v1->getData('video_group_id');
                        $videogroupfiles[] = [
                            'file_id' => $v1->getData('file_id'),
                            'aid' => $v1->getData('aid'),
                            'group_id' => intval($video_group_id),
                            'add_time' => getTime(),
                            'update_time' => getTime(),
                        ];
                    }
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
                    } catch (\Exception $e) {}
                }
                // 视频章节分组插件
                if (is_dir('./weapp/Videogroup/')) {
                    Db::name('weapp_videogroup_file')->where('aid',$aid)->delete();
                    if (!empty($videogroupfiles)) {
                        Db::name('weapp_videogroup_file')->insertAll($videogroupfiles);
                    }
                }
            }
        }else{
            if ('edit' == $opt) {
                Db::name('media_file')->where('aid',$aid)->delete();
                // 视频章节分组插件
                if (is_dir('./weapp/Videogroup/')) {
                    Db::name('weapp_videogroup_file')->where('aid',$aid)->delete();
                }
            }
        }
    }
}