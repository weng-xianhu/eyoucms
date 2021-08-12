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

class LinksGroup extends Base
{
    public function index()
    {
        $list = array();
        $keywords = input('keywords/s');

        $condition = array();
        if (!empty($keywords)) {
            $condition['group_name'] = array('LIKE', "%{$keywords}%");
        }


        $linksgroupsM =  Db::name('links_group');
        $count = $linksgroupsM->where($condition)->count('id');// 查询满足要求的总记录数
        $Page = $pager = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $linksgroupsM->where($condition)->order('sort_order asc, id asc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pager',$pager);// 赋值分页对象
        return $this->fetch();
    }

    /**
     * 保存友情链接分组
     */
    public function linksgroup_save()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');

            if (empty($post['group_name'])) {
                $this->error('至少新增一个链接分组！');
            } else {
                $is_empty = true;
                foreach ($post['group_name'] as $key => $val) {
                    $val = trim($val);
                    if (!empty($val)) {
                        $is_empty = false;
                        break;
                    }
                }
                if (true === $is_empty) {
                    $this->error('分组名称不能为空！');
                }
            }

            // 数据处理
            $now_time = getTime();
            $admin_lang = $this->admin_lang;
            foreach ($post['group_name'] as $key => $val) {
                $group_name  = trim($val);
                if (!empty($group_name)) {
                    if (empty($post['id'][$key])) {
                        $addData = [
                            'group_name' => $group_name,
                            'sort_order' => $post['sort_order'][$key] ? :100,
                            'lang' => $admin_lang,
                            'add_time' => $now_time,
                            'update_time' => $now_time,
                        ];
                        Db::name("links_group")->insert($addData);
                    } else {
                        $id = intval($post['id'][$key]);
                        $editData = [
                            'group_name' => $group_name,
                            'sort_order' => $post['sort_order'][$key] ? :100,
                            'lang' => $this->admin_lang,
                            'update_time' => $now_time,
                        ];
                        Db::name("links_group")->where(['id'=>$id])->update($editData);
                    }
                }
            }
            adminLog('保存链接分组：'.implode(',', $post['group_name']));
            $this->success('保存成功');
        }
        $this->error('非法访问！');
    }

    
    /**
     * 删除友情链接分组
     */
    public function del()
    {
        if (IS_POST) {
            $id_arr = input('del_id/a');
            $id_arr = eyIntval($id_arr);
            if(!empty($id_arr)){
                $result = Db::name('links_group')->field('group_name')
                    ->where([
                        'id'    => ['IN', $id_arr],
                    ])->select();
                $group_name_list = get_arr_column($result, 'group_name');

                $r = Db::name('links_group')->where([
                        'id'    => ['IN', $id_arr],

                    ])
                    ->cache(true, null, "links_group")
                    ->delete();
                Db::name('links')->where([
                    'groupid'    => ['IN', $id_arr],

                ])
                    ->cache(true, null, "links_group")
                    ->delete();
                if($r){
                    adminLog('删除友情链接分组：'.implode(',', $group_name_list));
                    $this->success('删除成功');
                }else{
                    $this->error('删除失败');
                }
            } else {
                $this->error('参数有误');
            }
        }
        $this->error('非法访问');
    }
}