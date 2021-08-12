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

namespace app\admin\controller;

use think\Db;
use think\Page;
use think\Cache;

class UsersNotice extends Base
{
    /**
     * 构造方法
     */
    public function __construct(){
        parent::__construct();

        $this->language_access(); // 多语言功能操作权限
        
        // 会员中心配置信息
        $this->UsersConfigData = getUsersConfigData('all');
        $this->assign('userConfig',$this->UsersConfigData);
    }

    /**
     * 站内通知 - 列表
     */
    public function index()
    {
        $list = array();
        $keywords = input('keywords/s');

        $map = array();
        if (!empty($keywords)) {
            $map['title'] = array('LIKE', "%{$keywords}%");
        }

        $count = Db::name('users_notice')->where($map)->count('id');// 查询满足要求的总记录数
        $pageObj = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = Db::name('users_notice')->where($map)->order('id desc')->limit($pageObj->firstRow.','.$pageObj->listRows)->select();
        if ($list) {
            foreach ($list as $k=>$v) {
                $usernames_str = '';
                if ($v['users_id']) {
                    $usernames_arr = explode(',', $v['usernames']);
                    if (count($usernames_arr) > 3) {
                        for ($i = 0; $i < 3; $i++) {
                            $usernames_str .= $usernames_arr[$i] . ',';
                        }
                        $usernames_str .= ' ...';
                        $list[$k]['usernames'] = $usernames_str;
                    }
                }else{
                    $list[$k]['usernames'] = '全站会员';
                }
            }
        }

        $pageStr = $pageObj->show(); // 分页显示输出
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $pageStr); // 赋值分页输出
        $this->assign('pager', $pageObj); // 赋值分页对象

        return $this->fetch();
    }

    /**
     * 站内通知 - 编辑
     */
    public function edit()
    {
        if (IS_POST) {
            $post = input('post.');
            if (isset($post['usernames'])) unset($post['usernames']);
            if (isset($post['users_id'])) unset($post['users_id']);

            $post['id'] = eyIntval($post['id']);
            if(!empty($post['id'])){
                $post['update_time'] = getTime();
                $r = Db::name('users_notice')->where(['id'=>$post['id']])->update($post);
                if ($r) {
                    adminLog('编辑站内通知：通知id为'.$post['id']); // 写入操作日志
                    $this->success("操作成功!", url('UsersNotice/index'));
                }
            }
            $this->error("操作失败!");
        }

        $id = input('id/d', 0);
        $row = Db::name('users_notice')->find($id);
        if (empty($row)) {
            $this->error('数据不存在，请联系管理员！');
            exit;
        }

        // 转化换行格式，适应输出
        $row['remark'] = str_replace("<br/>", "\n", $row['remark']);

        $listname = Db::name('users')->order('users_id desc')->field('users_id,username')->select();
        $this->assign('listname', $listname);

        $this->assign('row',$row);
        return $this->fetch();
    }

    /**
     * 站内通知 - 删除
     */
    public function del()
    {
        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if(!empty($id_arr) && IS_POST){
            $result = Db::name('users_notice')->where("id",'IN',$id_arr)->select();
            $r = Db::name('users_notice')->where("id",'IN',$id_arr)->delete();
            if($r !== false){
                $usersTplVersion = getUsersTplVersion();
                if ($usersTplVersion != 'v1') {
                    //未读消息数-1
                    foreach ($result as $item) {
                        if ($item['users_id']) {
                            $users_id_arr_new = explode(",", $item['users_id']);
                            Db::name('users')->where(['users_id' => ['IN', $users_id_arr_new], 'unread_notice_num'=>['gt', 0]])->setDec('unread_notice_num');
                        }else{
                            //通知的是全站会员
                            Db::name('users')->where(['unread_notice_num'=>['gt', 0]])->setDec('unread_notice_num');
                        }
                    }
                }
                Db::name('users_notice_read')->where("notice_id",'IN',$id_arr)->delete();
                adminLog('删除站内通知：'.implode(',', $id_arr));
                $this->success("删除成功!");
            }
        }
        $this->error("删除失败!");
    }

    /**
     * 站内通知 - 管理员接收通知列表
     */
    public function admin_notice_index()
    {
        $list = array();
        $keywords = input('keywords/s');

        $map = array();
        if (!empty($keywords)) {
            $map['a.content_title'] = array('LIKE', "%{$keywords}%");
        }

        $count = Db::name('users_notice_tpl_content')->alias('a')->where($map)->count('content_id');
        $pageObj = new Page($count, config('paginate.list_rows'));
        $list = Db::name('users_notice_tpl_content')
            ->field('a.*, b.tpl_name')
            ->alias('a')
            ->join('__USERS_NOTICE_TPL__ b', 'a.source = b.send_scene', 'LEFT')
            ->where($map)
            ->order('content_id desc')
            ->limit($pageObj->firstRow.','.$pageObj->listRows)
            ->select();

        $pageStr = $pageObj->show(); // 分页显示输出
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $pageStr); // 赋值分页输出
        $this->assign('pager', $pageObj); // 赋值分页对象

        return $this->fetch();
    }

    /**
     * 站内通知 - 编辑管理员接收通知
     */
    public function admin_notice_edit()
    {
        $content_id = input('content_id/d', 0);
        $Find = Db::name('users_notice_tpl_content')->field('a.*, b.tpl_name')->alias('a')->join('__USERS_NOTICE_TPL__ b', 'a.source = b.send_scene', 'LEFT')->find($content_id);
        if (empty($Find)) $this->error('数据不存在，请联系管理员！');

        // 更新通知为已查看
        if (empty($Find['is_read'])) {
            $update = [
                'content_id'  => $Find['content_id'],
                'is_read'     => 1,
                'update_time' => getTime()
            ];
            Db::name('users_notice_tpl_content')->update($update);
        }

        $this->assign('find', $Find);
        return $this->fetch();
    }

    /**
     * 站内通知 - 删除管理员接收通知
     */
    public function admin_notice_del()
    {
        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if(!empty($id_arr) && IS_POST) {
            // 查询要删除的通知信息
            $result = Db::name('users_notice_tpl_content')->field('content_id')->where("content_id", 'IN', $id_arr)->select();
            // 获取ID列表
            $id_list = get_arr_column($result, 'content_id');
            // 执行删除
            $DeleteID = Db::name('users_notice_tpl_content')->where("content_id", 'IN', $id_arr)->delete();
            // 添加操作日志，返回结束
            if (!empty($DeleteID)) {
                adminLog('删除接收的站内通知：' . implode(',', $id_list));
                $this->success("删除成功");
            } else {
                $this->error("删除失败");
            }
        } else {
            $this->error("参数有误");
        }
    }

    /**
     * 全部标记已读
     */
    public function sign_admin_allread()
    {
        if (IS_AJAX_POST) {
            $update = [
                'is_read'     => 1,
                'update_time' => getTime()
            ];
            $r = Db::name('users_notice_tpl_content')->where(['admin_id'=>['gt', 0]])->update($update);
            if ($r !== false) {
                $this->success('操作成功');
            }
        }
        $this->error('操作失败');
    }
}