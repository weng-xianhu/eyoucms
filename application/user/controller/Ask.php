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
use think\Page;

class Ask extends Base
{

    public function _initialize()
    {
        parent::_initialize();
        $this->ask_db             = Db::name('ask'); // 问题表
        $this->ask_answer_db      = Db::name('ask_answer'); // 答案表
        $this->ask_type_db        = Db::name('ask_type'); // 问题栏目分类表

        $functionLogic = new \app\common\logic\FunctionLogic;
        $functionLogic->validate_authorfile(2);
    }

    /**
     * 会员中心--我的问答--我的问题
     */
    public function ask_index()
    {
        // 提问问题查询列表
        /*查询字段*/
        $field = 'a.ask_id, a.ask_title, a.click, a.replies, a.add_time, a.is_review, b.type_name';
        /* END */

        /*查询条件*/
        $where = [
            'a.status'   => ['IN', [0, 1]],
            'a.users_id' => $this->users_id,
        ];
        /* END */

        /* 分页 */
        $count             = $this->ask_db->alias('a')->where($where)->count('ask_id');
        $pageObj           = new Page($count, config('paginate.list_rows'));
        /* END */

        /*问题表数据(问题表+会员表+问题分类表)*/
        $result = $this->ask_db->field($field)
            ->alias('a')
            ->join('ask_type b', 'a.type_id = b.type_id', 'LEFT')
            ->where($where)
            ->order('a.add_time desc')
            ->limit($pageObj->firstRow . ',' . $pageObj->listRows)
            ->select();
        /* END */
        /*数据处理*/
        foreach ($result as $key => $value) {
            // 问题内容Url
            $result[$key]['AskUrl'] = url('home/Ask/details', ['ask_id' => $value['ask_id']]);
        }
        /* END */

        $show = $pageObj->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$result);// 赋值数据集
        $this->assign('pager',$pageObj);// 赋值分页对象

        return $this->fetch('ask_index');
    }

    /**
     *   会员中心--我的问答--我的回复
     */
    public function answer_index()
    {
        // 回答问题查询列表
        /*查询字段*/
        $field = 'a.*, b.ask_title';
        /* END */

        /*查询条件*/
        $where = [
            'a.users_id' =>$this->users_id,
        ];
        /* END */

        /* 分页 */
        $count             = $this->ask_answer_db->alias('a')->where($where)->count('answer_id');
        $pageObj           = new Page($count, config('paginate.list_rows'));
        /* END */

        /*问题回答人查询*/
        $result = $this->ask_answer_db->field($field)
            ->alias('a')
            ->join('ask b', 'a.ask_id = b.ask_id', 'LEFT')
            ->where($where)
            ->order('a.add_time desc')
            ->limit($pageObj->firstRow . ',' . $pageObj->listRows)
            ->select();
        /* END */
        /*数据处理*/
        foreach ($result as $key => $value) {
            // 问题内容Url
            $result[$key]['AskUrl'] = url('home/Ask/details', ['ask_id' => $value['ask_id']]);

            if (isset($value['answer_id']) && !empty($value['answer_id'])) {
                $preg                               = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
                $value['content']                   = htmlspecialchars_decode($value['content']);
                $value['content']                   = preg_replace($preg, '[图片]', $value['content']);
                $value['content']                   = strip_tags($value['content']);
                $result[$key]['content'] = mb_strimwidth($value['content'], 0, 120, "...");
            }
        }
        /* END */

        $show = $pageObj->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$result);// 赋值数据集
        $this->assign('pager',$pageObj);// 赋值分页对象
        return $this->fetch('answer_index');
    }
}

