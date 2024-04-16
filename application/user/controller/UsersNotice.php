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
namespace app\user\controller;

use think\Db;
use think\Config;
use think\Verify;
use think\Page;
use think\Request;

/**
 * 消息通知
 */
class UsersNotice extends Base
{
    public function _initialize() {
        parent::_initialize();

        $functionLogic = new \app\common\logic\FunctionLogic;
        $functionLogic->validate_authorfile(1);
    }

    public function index()
    {
        $condition = array();
        $keywords = input('keywords/s');

        if (!empty($keywords)) {
            $condition['b.title'] = array('LIKE', "%{$keywords}%");
        }
        $is_read = input('param.is_read');
        if ($is_read) {
            $condition['a.is_read'] = $is_read == 1 ?1:0;
        }

        //查接收消息通知的会员id
        $users_id = $this->users_id;
        $condition['a.users_id'] = $users_id;
        $condition['a.is_del'] = 0;

        // 多语言
        $condition['a.lang'] = $this->home_lang;

        $count = Db::name('users_notice_read')->alias('a')->where($condition)->count('id');

        $Page = $pager = new Page($count, config('paginate.list_rows'));
        $result['data'] = Db::name('users_notice_read')
            ->alias('a')
            ->field("a.id,a.users_id,a.notice_id,a.is_read,a.is_del,b.title,b.remark,b.add_time,b.update_time")
            ->join("users_notice b","a.notice_id = b.id")
            ->where($condition)
            ->order('a.is_read ASC,b.add_time DESC')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();

        $show = $Page->show();// 分页显示输出
        $result['delurl']  = url('user/UsersNotice/del');
        $result['readurl'] = url('user/UsersNotice/batch_read');

        $eyou = array(
            'field' => $result,
        );

        $this->assign('page', $show);
        $this->assign('eyou', $eyou);
        $this->assign('pager', $pager);
        return $this->fetch('users_notice_index');
    }

    /**
     * 删除通知
     */
    public function del()
    {
        if (IS_POST) {
            $id_arr = input('del_id/a');
            $id_arr = eyIntval($id_arr);
            if (!empty($id_arr)) {
                $unread_notice = Db::name('users_notice_read')->field("id")->where(['id'=>['IN',$id_arr],'is_read'=>0])->find();
                if (!empty($unread_notice)) $this->error('不能删除未读消息');

                //将状态值改为已删除
                Db::name('users_notice_read')->where(['id'=>['IN',$id_arr]])->update(['is_del'=>1]);
                $this->success('删除成功');
            }
        }
        $this->error('删除失败');
    }

    /**
     * 批量标记为已读
     */
    public function batch_read()
    {
        if (IS_POST) {
            $id_arr = input('del_id/a');
            $id_arr = eyIntval($id_arr);
            $users_id = $this->users_id;
            if (!empty($id_arr)) {
                $notice_read_ids = Db::name('users_notice_read')->where(['id'=>['IN', $id_arr],'is_read'=>1])->column('id');
                $id_arr_unread = array_diff($id_arr, $notice_read_ids);

                if ($id_arr_unread) {
                    Db::name('users_notice_read')->where(['id'=>['IN', $id_arr_unread]])->update(['is_read'=>1]);
                    $unread_notice_num = Db::name("users")->where(['users_id' => $users_id])->value('unread_notice_num');
                    $update_num = $unread_notice_num-count($id_arr_unread);
                    if ($update_num>0) {
                        Db::name("users")->where(['users_id' => $users_id])->update(['unread_notice_num'=>$update_num]);
                    }else{
                        Db::name("users")->where(['users_id' => $users_id])->update(['unread_notice_num'=>0]);
                    }

                    $this->success(foreign_lang('system12', $this->home_lang));
                }
                $this->error('未选择未读消息');
            }
            $this->error('未选择消息');
        }
    }


    /**
     * 删除所有通知
     */
    public function del_all()
    {
        if (IS_POST) {
            $users_id = $this->users_id;
            $unread_notice = Db::name('users_notice_read')->field("id")->where(['users_id'=>$users_id,'is_read'=>0])->find();
            if ($unread_notice) $this->error('不能删除未读消息');
            Db::name('users_notice_read')->where(['users_id'=>$users_id])->update(['is_del'=>1]);
            Db::name("users")->where(['users_id' => $users_id])->update(['unread_notice_num'=> 0]);
            $this->success('删除成功');

        }
        $this->error('未知错误');
    }


    /**
     * 所有标记为已读
     */
    public function batch_read_all()
    {
        if (IS_POST) {
            $users_id = $this->users_id;
            Db::name('users_notice_read')->where(['users_id'=>$users_id])->update(['is_read'=>1]);
            Db::name("users")->where(['users_id' => $users_id])->update(['unread_notice_num'=> 0]);
            $this->success(foreign_lang('system12', $this->home_lang));
        }
        $this->error(foreign_lang('system11', $this->home_lang));
    }
}