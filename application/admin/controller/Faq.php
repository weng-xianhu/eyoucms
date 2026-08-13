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

class Faq extends Base
{

    public function _initialize() {
        parent::_initialize();
        $this->times = getTime();
        $this->faqGroupDb = Db::name('faq_group');
        $this->faqAsklistDb = Db::name('faq_asklist');

        $functionLogic = new \app\common\logic\FunctionLogic;
        $functionLogic->validate_authorfile(1);
    }

    public function index()
    {
        // 查询条件
        $where = [
            'lang' => $this->admin_lang
        ];
        $keywords = input('keywords/s');
        if (!empty($keywords)) $where['group_title'] = ['LIKE', "%{$keywords}%"];

        // 查询列表
        $count = $this->faqGroupDb->where($where)->count();
        $pageObj = new Page($count, config('paginate.list_rows'));
        $list = $this->faqGroupDb->where($where)->order('group_id desc')->limit($pageObj->firstRow.','.$pageObj->listRows)->getAllWithIndex('group_id');

        // 加载模板
        $this->assign('list', $list);
        $this->assign('pager', $pageObj);
        $this->assign('page', $pageObj->show());

        return $this->fetch();
    }

    public function add()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 是否存在分组名称
            $where = [
                'group_title' => trim($post['group_title']),
                'lang'  => $this->admin_lang,
            ];
            if ($this->faqGroupDb->where($where)->count() > 0) $this->error('分组名称已存在，请检查');

            // 添加分组
            $insert = [
                'group_title' => trim($post['group_title']),
                'lang'        => trim($this->admin_lang),
                'add_time'    => $this->times,
                'update_time' => $this->times,
            ];
            $resultID = !empty($insert) ? $this->faqGroupDb->insertGetId($insert) : 0;
            if (!empty($resultID)) {
                Cache::clear('faq_group');
                // 添加分组问答
                $i = 1;
                $insertAll = [];
                foreach ($post['asklist_title'] as $key => $value) {
                    $asklistTitle = trim($value);
                    $asklistContent = trim($post['asklist_content'][$key]);
                    if (!empty($asklistTitle) || !empty($asklistContent)) {
                        $insertAll[] = [
                            'group_id'        => intval($resultID),
                            'asklist_title'   => !empty($asklistTitle) ? trim($asklistTitle) : '',
                            'asklist_content' => !empty($asklistContent) ? trim($asklistContent) : '',
                            'sort_order'      => $i,
                            'lang'            => trim($this->admin_lang),
                            'add_time'        => $this->times,
                            'update_time'     => $this->times,
                        ];
                        $i++;
                    }
                }
                $resultID = !empty($insertAll) ? $this->faqAsklistDb->insertAll($insertAll) : 0;
                if (!empty($resultID)) {
                    Cache::clear('faq_asklist');
                    $this->success("添加成功");
                }
            }
            $this->error("添加失败，请重试~");
        }

        return $this->fetch();
    }

    public function edit()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 是否存在分组名称
            $where = [
                'group_id'    => ['NEQ', $post['group_id']],
                'group_title' => trim($post['group_title']),
                'lang'        => $this->admin_lang,
            ];
            if ($this->faqGroupDb->where($where)->count() > 0) $this->error('分组名称已存在，请检查');

            // 修改分组信息
            $update = [
                'group_title' => trim($post['group_title']),
                'update_time' => $this->times,
            ];
            $resultID = $this->faqGroupDb->where(['group_id' => intval($post['group_id'])])->update($update);
            if (!empty($resultID)) {
                // 执行删除分组问题
                if (!empty($post['del_asklist_id'])) $this->faqAsklistDb->where(['asklist_id' => ['IN', explode(',', $post['del_asklist_id'])]])->delete(true);

                // 保存分组问答
                $i = 1;
                $update = [];
                foreach ($post['asklist_title'] as $key => $value) {
                    $asklistTitle = trim($value);
                    $asklistContent = trim($post['asklist_content'][$key]);
                    $update[] = [
                        'asklist_id'      => intval($key),
                        'asklist_title'   => !empty($asklistTitle) ? trim($asklistTitle) : '',
                        'asklist_content' => !empty($asklistContent) ? trim($asklistContent) : '',
                        'sort_order'      => $i,
                        'update_time'     => $this->times,
                    ];
                    $i++;
                }

                // 新增分组问题
                foreach ($post['asklist_title_new'] as $key => $value) {
                    $asklistTitle = trim($value);
                    $asklistContent = trim($post['asklist_content_new'][$key]);
                    if (!empty($asklistTitle) || !empty($asklistContent)) {
                        $insertAll[] = [
                            'group_id'        => intval($post['group_id']),
                            'asklist_title'   => !empty($asklistTitle) ? trim($asklistTitle) : '',
                            'asklist_content' => !empty($asklistContent) ? trim($asklistContent) : '',
                            'sort_order'      => $i,
                            'lang'            => trim($this->admin_lang),
                            'add_time'        => $this->times,
                            'update_time'     => $this->times,
                        ];
                        $i++;
                    }
                }

                // 执行保存分组问答
                if (!empty($update)) model('FaqAsklist')->saveAll($update);
                // 执行新增分组问题
                if (!empty($insertAll)) $this->faqAsklistDb->insertAll($insertAll);
                // 返回结束
                Cache::clear('faq_asklist');
                $this->success("保存成功");
            }
            $this->error("保存失败，刷新重试~");
        }

        $assignData = [];
        $group_id = input('group_id/d', 0);
        $where = [
            'group_id' => intval($group_id)
        ];
        // 查询问题分组
        $faqGroup = $this->faqGroupDb->where($where)->find();
        $assignData['faqGroup'] = $faqGroup;

        // 查询问题列表
        $faqAskList = $this->faqAsklistDb->where($where)->order('sort_order asc, asklist_id asc')->select();
        $assignData['faqAskList'] = $faqAskList;

        $this->assign($assignData);
        return $this->fetch();
    }

    public function del()
    {
        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if (IS_AJAX_POST && !empty($id_arr)) {
            $where = [
                'group_id' => ['IN', $id_arr]
            ];
            $r = $this->faqGroupDb->where($where)->delete(true);
            if ($r !== false) {
                Cache::clear('faq_group');
                $this->faqAsklistDb->where($where)->delete(true);
                Cache::clear('faq_asklist');
                $this->success('删除成功');
            }
        }
        $this->error('删除失败');
    }
}