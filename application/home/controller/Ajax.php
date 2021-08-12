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
use think\Config;
use think\AjaxPage;
use think\Request;

class Ajax extends Base
{
    public function _initialize() {
        parent::_initialize();
    }

    /**
     * 获取评论列表
     * @return mixed
     */
    public function product_comment()
    {
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        $post = input('post.');
        $post['aid'] = !empty($post['aid']) ? $post['aid'] : input('param.aid/d');

        if (isMobile() && 1 < $post['p']) {
            $Result = [];
        } else {
            $Result = cache('EyouHomeAjaxComment_' . $post['aid']);
            if (empty($Result)) {
                /*商品评论数计算*/
                $where = [
                    'is_show' => 1,
                    'product_id' => $post['aid']
                ];
                $count = Db::name('shop_order_comment')
                    ->field('count(*) as count, total_score')
                    ->group('total_score')
                    ->where($where)
                    ->select();

                $Result['total']  = 0;
                $Result['good']   = 0;
                $Result['middle'] = 0;
                $Result['bad']    = 0;

                foreach ($count as $k => $v) {
                    $Result['total'] += $v['count'];
                    switch ($v['total_score']) {
                        case 1:
                            $Result['good'] = $v['count'];
                            break;
                        case 2:
                            $Result['middle'] = $v['count'];
                            break;
                        case 3:
                            $Result['bad'] = $v['count'];
                            break;
                        default:
                            break;
                    }
                }

                $Result['good_percent']   = $Result['good'] > 0 ? round($Result['good'] / $Result['total'] * 100) : 0;
                $Result['middle_percent'] = $Result['middle'] > 0 ? round($Result['middle'] / $Result['total'] * 100) : 0;
                $Result['bad_percent']    = $Result['bad'] > 0 ? 100 - $Result['good_percent'] - $Result['middle_percent'] : 0;
                // $Result['good_percent']   = $Result['good_percent'] . "%";
                // $Result['middle_percent'] = $Result['middle_percent'] . "%";
                // $Result['bad_percent']    = $Result['bad_percent'] . "%";
                // 存在评论则执行
                if (!empty($Result)) cache('EyouHomeAjaxComment_' . $post['aid'], $Result, null, 'shop_order_comment');
            }

            /*选中状态*/
            $Result['Class_1'] = 0 == $post['score'] ? 'check' : '';
            $Result['Class_2'] = 1 == $post['score'] ? 'check' : '';
            $Result['Class_3'] = 2 == $post['score'] ? 'check' : '';
            $Result['Class_4'] = 3 == $post['score'] ? 'check' : '';
        }
        
        // 调用评价列表
        $this->GetCommentList($post);
        $this->assign('Result', $Result);
        return $this->fetch('system/product_comment');
    }

    // 手机端加载更多时调用
    public function comment_list()
    {
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        // 调用评价列表
        $this->GetCommentList(input('post.'));
        return $this->fetch('system/comment_list');
    }

    // 调用评价列表
    private function GetCommentList($post = [])
    {
        /*商品评论数据处理*/
        $field = 'a.*, u.nickname, u.head_pic, l.level_name';
        $where = [
            'is_show' => 1,
            'a.product_id' => $post['aid']
        ];
        if (!empty($post['score'])) $where['a.total_score'] = $post['score'];
        
        $count = Db::name('shop_order_comment')->alias('a')->where($where)->count();
        $Page = new AjaxPage($count, 5);
        $Comment = Db::name('shop_order_comment')
            ->alias('a')
            ->field($field)
            ->where($where)
            ->join('__USERS__ u', 'a.users_id = u.users_id', 'LEFT')
            ->join('__USERS_LEVEL__ l', 'u.level = l.level_id', 'LEFT')
            ->order('a.comment_id desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();
        $Comment = !empty($Comment) ? $Comment : [];

        foreach ($Comment as &$value) {
            // 会员头像处理
            $value['head_pic'] = handle_subdir_pic(get_head_pic($value['head_pic']));

            // 评价转换星级评分
            $value['total_score'] = GetScoreArray($value['total_score']);

            // 评价上传的图片
            $value['upload_img'] = !empty($value['upload_img']) ? explode(',', unserialize($value['upload_img'])) : '';
            
            // 评价的内容
            $value['content'] = !empty($value['content']) ? htmlspecialchars_decode(unserialize($value['content'])) : '';
        }

        // 加载渲染模板
        $this->assign('Page', $Page->show());
        $this->assign('Comment', $Comment);
    }
}