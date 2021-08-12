<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 陈风任 <491085389@qq.com>
 * Date: 2019-7-3
 */

namespace app\user\controller;

use think\Db;
use think\Page;

/**
 * 我的下载
 */
class Download extends Base
{
    public function _initialize() {
        parent::_initialize();

        $status = Db::name('channeltype')->where([
                'nid'   => 'download',
                'is_del'    => 0,
            ])->getField('status');
        if (empty($status)) {
            $this->error('下载模型已关闭，该功能被禁用！');
        }
    }

    public function index()
    {
        $list = array();

        $condition = array();

        $condition['users_id'] = $this->users_id;

        $count = Db::name('download_log')->where($condition)->count('log_id');// 查询满足要求的总记录数
        $Page = $pager = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = Db::name('download_log')->where($condition)->group('aid')->order('log_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $aids = [];
        foreach ($list as $key => $val) {
            array_push($aids, $val['aid']);
        }

        $channeltype_row = \think\Cache::get('extra_global_channeltype');

        $archivesList = DB::name('archives')
            ->field("b.*, a.*, a.aid as aid")
            ->alias('a')
            ->join('__ARCTYPE__ b', 'a.typeid = b.id', 'LEFT')
            ->where('a.aid', 'in', $aids)
            ->getAllWithIndex('aid');
        foreach ($archivesList as $key => $val) {
            $controller_name = $channeltype_row[$val['channel']]['ctl_name'];
            $val['arcurl'] = arcurl('home/'.$controller_name.'/view', $val);
            $val['litpic'] = handle_subdir_pic($val['litpic']); // 支持子目录
            $archivesList[$key] = $val;
        }
        $this->assign('archivesList', $archivesList);

        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pager',$pager);// 赋值分页对象
        return $this->fetch('users/download_index');
    }

    public function search_servername()
    {
        if (IS_AJAX_POST) {
            $post = input('param.');
            $keyword = $post['keyword'];

            $servernames = tpCache('download.download_select_servername');
            $servernames = unserialize($servernames);

            $search_data = $servernames;
            if (!empty($keyword)) {
                $search_data = [];
                if ($servernames) {
                    foreach ($servernames as $k => $v) {
                        if (preg_match("/$keyword/s", $v)) $search_data[] = $v;
                    }
                }
            }

            $this->success("获取成功",null,$search_data);
        }
    }
    public function get_template()
    {
        if (IS_AJAX_POST) {
            //$list = Db::name('download_attr_field')->where('field_use',1)->select();
            $list = Db::name('download_attr_field')->select();
            $this->success("查询成功！", null, $list);
        }
    }
}