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

namespace app\home\controller;

use think\Db;

class Media extends Base
{
    // 模型标识
    public $nid = 'media';
    // 模型ID
    public $channeltype = '';

    public function _initialize()
    {
        parent::_initialize();
        $channeltype_list = config('global.channeltype_list');
        $this->channeltype = $channeltype_list[$this->nid];
    }

    public function lists($tid)
    {
        $param = input('param.');
        $tid_tmp = $tid;
        $seo_pseudo = config('ey_config.seo_pseudo');
        if (empty($tid)) {
            $map = array(
                'channeltype' => $this->channeltype,
                'parent_id' => 0,
                'is_hidden' => 0,
                'status' => 1,
            );
        } else {
            $is_screen = false;
            if (3 == $seo_pseudo) {
                $url_screen_var = config('global.url_screen_var');
                if (!empty($param[$url_screen_var]) && 1 == intval($param[$url_screen_var])) {
                    $is_screen = true;
                    $map = array('id' => intval($tid));
                } else {
                    $map = array('dirname' => $tid);
                }
            } else {
                if (!is_numeric($tid) || strval(intval($tid)) !== strval($tid)) {
                    abort(404, '页面不存在');
                }
                $map = array('id' => $tid);
            }
        }
        $map['lang'] = $this->home_lang; // 多语言
        $row = M('arctype')->field('id,dirname')->where($map)->order('sort_order asc')->limit(1)->find();
        $tid = !empty($row['id']) ? intval($row['id']) : 0;
        $dirname = !empty($row['dirname']) ? $row['dirname'] : '';

        /*301重定向到新的伪静态格式*/
        $this->jumpRewriteFormat($tid, $dirname, 'lists');
        /*--end*/

        if (3 == $seo_pseudo) {
            $tid = $is_screen ? $tid : $dirname;
        } else {
            $tid = $tid_tmp;
        }

        return action('home/Lists/index', $tid);
    }

    public function view($aid)
    {
        $param = I('param.');
        $aid = !empty($param['aid']) ? $param['aid'] : $aid;
        if (!is_numeric($aid) || strval(intval($aid)) !== strval($aid)) {
            if (!preg_match("/^[\x{4e00}-\x{9fa5}\w\-]+$/u", $aid)) {
                abort(404, '页面不存在');
            }
            $aid = Db::name('archives')->where(['htmlfilename' => $aid])->value('aid');
        } else {
            $aid = intval($aid);
        }
        $result = model('Media')->getInfo($aid);
        if (empty($result)) {
            abort(404, '页面不存在');
        } elseif ($result['arcrank'] == -1) {
            $admin_id = input('param.admin_id/d');
            if ( (session('?admin_id') && !empty($admin_id)) || ($result['users_id'] > 0 && $result['users_id'] == (int)session('users_id')) ) {

            }
            else {
                $this->success('待审核稿件，你没有权限阅读！', ROOT_DIR.'/');
            }
        }
        // 外部链接跳转
        if ($result['is_jump'] == 1) {
            header('Location: ' . $result['jumplinks']);
            exit;
        }
        /*--end*/

        $tid = $result['typeid'];
        $arctypeInfo = model('Arctype')->getInfo($tid);
        /*301重定向到新的伪静态格式*/
        $this->jumpRewriteFormat($aid, $arctypeInfo['dirname'], 'view');
        /*--end*/

        return action('home/View/index', $aid);
    }

    /**
     * 记录视频播放进程
     */
    public function record_process()
    {
        // \think\Session::pause(); // 暂停session，防止session阻塞机制
        if (IS_AJAX_POST) {
            $aid         = input('aid/d', 0);
            $file_id     = input('file_id/d', 0);
            $timeDisplay = input('timeDisplay/d', 0);
            $users_id    = session('users_id');
            if (empty($users_id)){
                return true;
            }
            if ( 0 == $timeDisplay ){
                exit;
            }
            $where       = ['users_id' => intval($users_id),
                            'aid'      => $aid,
                            'file_id'  => $file_id];
            $count       = Db::name('media_play_record')->where($where)->find();
            $data        = [
                'users_id'    => intval($users_id),
                'aid'         => intval($aid),
                'file_id'     => intval($file_id),
                'play_time'   => $timeDisplay,
                'update_time' => getTime(),
            ];
            if (!empty($count)) {
                $timeDisplay = $timeDisplay + $count['play_time'];
                $file_time = Db::name('media_file')->where('file_id',$file_id)->value('file_time');
                $data['play_time'] = $timeDisplay > $file_time ? $file_time : $timeDisplay;
                $data['play_time'] = intval($data['play_time']);
                //更新
                Db::name('media_play_record')->where($where)->update($data);
            }else{
                $data['add_time'] = getTime();
                Db::name('media_play_record')->insert($data);
            }
        }
        abort(404);
    }
}