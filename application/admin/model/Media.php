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
 * 视频
 */
class Media extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 后置操作方法
     * 自定义的一个函数 用于数据保存后做的相应处理操作, 使用时手动调用
     * @param int $aid 产品id
     * @param array $post post数据
     * @param string $opt 操作
     */
    public function afterSave($aid, $post, $opt)
    {
        $video_files = [];
        if (!empty($post['video'])) {
            $post['addonFieldExt']['total_video'] = count($post['video']);
            $post['addonFieldExt']['total_duration'] = 0;
            foreach ($post['video'] as $k => $v) {
                $v['file_url'] = trim($v['file_url']);
                if (empty($v['file_url'])){
                    $post['addonFieldExt']['total_video'] -= 1;
                    continue;
                }
                $post['addonFieldExt']['total_duration'] += $v['file_time'];
                $file_size = !empty($v['file_size']) ? $v['file_size'] : 0;
                $is_remote = 0;
                $file_ext  = explode('.', $v['file_url']);
                $uhash = md5($v['file_url'].$file_size);
                if (is_http_url($v['file_url'])) {
                    $is_remote = 1;
                    $md5file = '';
                } else {
                    if (preg_match('#^(/[\w]+)?(/uploads/media/)#i', $v['file_url'])) {
                        $file_path_tmp = preg_replace('#^(/[\w]+)?(/uploads/media/)#i', '$2', $v['file_url']);
                    } else {
                        $file_path_tmp = preg_replace('#^('.$this->root_dir.')?(/)#i', '$2', $v['file_url']);
                    }
                    $md5file = md5_file('.'.$file_path_tmp);
                }
                $video_files[] = [
                    'aid'         => $aid,
                    'file_id'     => $v['file_id'],
                    'title'       => $post['title'],
                    'file_url'    => !empty($v['file_url']) ? $v['file_url'] : '',
                    'file_time'   => !empty($v['file_time']) ? $v['file_time'] : 0,
                    'file_title'   => !empty($v['file_title']) ? $v['file_title'] : '',
                    'file_ext'    => end($file_ext),
                    'file_size'   => $file_size,
                    'file_mime'   => !empty($v['file_mime']) ? $v['file_mime'] : '',
                    'uhash'   => $uhash,
                    'md5file'   => $md5file,
                    'is_remote'   => $is_remote,
                    'gratis'    => !empty($v['gratis']) ? $v['gratis'] : 0,
                    'add_time'    => getTime(),
                    'update_time' => getTime(),
                ];
            }
        }

        $post['aid'] = $aid;
        $addonFieldExt = !empty($post['addonFieldExt']) ? $post['addonFieldExt'] : array();
        model('Field')->dealChannelPostData($post['channel'], $post, $addonFieldExt);

        // ---------多视频
        model('MediaFile')->savefile($aid, $video_files, $opt);

        // --处理TAG标签
        model('Taglist')->savetags($aid, $post['typeid'], $post['tags'], $post['arcrank'], $opt);

        // 处理mysql缓存表数据
        if (isset($post['arcrank']) && -1 == $post['arcrank'] && -1 == $post['old_arcrank'] && !empty($post['users_id'])) {
            // 待审核
            model('SqlCacheTable')->UpdateDraftSqlCacheTable($post, $opt);
        } else if (isset($post['arcrank'])) {
            // 已审核
            $post['old_typeid'] = intval($post['attr']['typeid']);
            model('SqlCacheTable')->UpdateSqlCacheTable($post, $opt, 'media');
        }
    }

    /**
     * 获取单条记录
     * @author wengxianhu by 2017-7-26
     */
    public function getInfo($aid, $field = null, $isshowbody = true)
    {
        $result = array();
        $field = !empty($field) ? $field : '*';
        $result = Db::name('archives')->field($field)
            ->where([
                'aid'   => $aid,
                'lang'  => get_admin_lang(),
            ])
            ->find();
        if ($isshowbody) {
            $tableName = Db::name('channeltype')->where('id','eq',$result['channel'])->getField('table');
            $result['addonFieldExt'] = Db::name($tableName.'_content')->where('aid',$aid)->find();
        }

        // 文章TAG标签
        if (!empty($result)) {
            $typeid = isset($result['typeid']) ? $result['typeid'] : 0;
            $tags = model('Taglist')->getListByAid($aid, $typeid);
            $result['tags'] = $tags['tag_arr'];
            $result['tag_id'] = $tags['tid_arr'];
        }

        return $result;
    }

    /**
     * 删除的后置操作方法
     * 自定义的一个函数 用于数据删除后做的相应处理操作, 使用时手动调用
     * @param int $aid
     */
    public function afterDel($aidArr = array())
    {
        if (is_string($aidArr)) {
            $aidArr = explode(',', $aidArr);
        }
        // 同时删除内容
       Db::name('media_content')->where(
                array(
                    'aid'=>array('IN', $aidArr)
                )
            )
            ->delete();
        // 同时删除软件
        $result = Db::name('media_file')->field('file_url')
            ->where(
                array(
                    'aid'=>array('IN', $aidArr)
                )
            )
            ->select();
        if (!empty($result)) {
            foreach ($result as $key => $val) {
                $file_url = preg_replace('#^(/[/\w]+)?(/public/upload/|/uploads/)#i', '$2', $val['file_url']);
                if (!is_http_url($file_url) && file_exists('.'.$file_url) && preg_match('#^(/uploads/|/public/upload/)(.*)/([^/]+)\.([a-z]+)$#i', $file_url)) {
                    @unlink(realpath('.'.$file_url));
                }
            }
            $r = Db::name('media_file')->where(
                array(
                    'aid'=>array('IN', $aidArr)
                )
            )->delete();
            if ($r !== false) {
               Db::name('media_log')->where(array('aid'=>array('IN', $aidArr)))->delete();
            }
        }
        // 同时删除TAG标签
        model('Taglist')->delByAids($aidArr);
    }
}