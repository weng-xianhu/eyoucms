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
     * 跳转到文档详情页
     * @return [type] [description]
     */
    public function toView()
    {
        $aid = input('param.aid/d');
        if (!empty($aid)) {
            $archives = Db::name('archives')->alias('a')
                ->field('b.*, a.*, c.ctl_name')
                ->join('arctype b', 'a.typeid = b.id', 'LEFT')
                ->join('channeltype c', 'c.id = a.channel', 'LEFT')
                ->where(['a.aid'=>$aid])
                ->find();
            $url = arcurl('home/'.$archives['ctl_name'].'/view', $archives, true, true);
            header('Location: '.$url);
            exit;
        }
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
                $time = getTime();
                $where = [
                    'is_show' => 1,
                    'add_time' => ['<=', $time],
                    'product_id' => $post['aid']
                ];
                $count = Db::name('shop_order_comment')->field('total_score, is_new_comment')->where($where)->select();

                $Result['total']  = count($count);
                $Result['good']   = 0;
                $Result['middle'] = 0;
                $Result['bad']    = 0;
                foreach ($count as $k => $v) {
                    // 评价转换星级评分(旧版评价数据执行)
                    $v['total_score'] = empty($v['is_new_comment']) ? GetScoreArray($v['total_score']) : $v['total_score'];
                    if (in_array($v['total_score'], [1, 2])) {
                        $Result['bad']++;
                    } else if (in_array($v['total_score'], [3, 4])) {
                        $Result['middle']++;
                    } else if (in_array($v['total_score'], [5])) {
                        $Result['good']++;
                    }
                }
                $Result['good_percent'] = $Result['good'] > 0 ? round($Result['good'] / $Result['total'] * 100) : 0;
                if (0 === intval($Result['good_percent']) && $Result['total'] === 0) $Result['good_percent'] = 100;
                $Result['middle_percent'] = $Result['middle'] > 0 ? round($Result['middle'] / $Result['total'] * 100) : 0;
                $Result['bad_percent'] = $Result['bad'] > 0 ? 100 - $Result['good_percent'] - $Result['middle_percent'] : 0;
                // 存在评论则执行
                // if (!empty($Result)) cache('EyouHomeAjaxComment_' . $post['aid'], $Result, null, 'shop_order_comment');
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
        // 商品评论数据处理
        $field = 'a.*, u.username, u.nickname, u.head_pic, u.sex, l.level_name, b.data';
        $time = getTime();
        $where = [
            'a.is_show' => 1,
            'a.add_time' => ['<=', $time],
            'a.product_id' => $post['aid']
        ];
        if (!empty($post['score'])) {
//            $where['a.total_score'] = $post['score'];
             if (3 === intval($post['score'])) {
                 $where['a.total_score'] = ['IN', [1, 2]];
             } else if (2 === intval($post['score'])) {
                 $where['a.total_score'] = ['IN', [3, 4]];
             } else if (1 === intval($post['score'])) {
                 $where['a.total_score'] = ['IN', [5]];
             }
        }
        $count = Db::name('shop_order_comment')->alias('a')->where($where)->count();
        $Page = new AjaxPage($count, 5);
        $Comment = Db::name('shop_order_comment')
            ->alias('a')
            ->field($field)
            ->where($where)
            ->join('__SHOP_ORDER_DETAILS__ b', 'a.details_id = b.details_id', 'LEFT')
            ->join('__USERS__ u', 'a.users_id = u.users_id', 'LEFT')
            ->join('__USERS_LEVEL__ l', 'u.level = l.level_id', 'LEFT')
            ->order('a.add_time desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();
        $Comment = !empty($Comment) ? $Comment : [];
        foreach ($Comment as &$value) {
            // 规格处理
            $value['data'] = unserialize($value['data']);
            $value['spec_value'] = !empty($value['data']['spec_value']) ? htmlspecialchars_decode($value['data']['spec_value']) : '';
            $value['spec_value'] = rtrim(str_replace("<br/>", "，", $value['spec_value']), '，');
            // 会员头像处理
            $value['nickname'] = empty($value['nickname']) ? $value['username'] : $value['nickname'];
            $value['head_pic'] = handle_subdir_pic(get_default_pic($value['head_pic'], false, $value['sex']));
            // $value['head_pic'] = get_head_pic($value['head_pic'], false, $value['sex']);
            // 是否匿名评价
            $value['nickname'] = empty($value['is_anonymous']) ? $value['nickname'] : '匿名用户';
            // 评价转换星级评分(旧版评价数据执行)
            $value['total_score'] = empty($value['is_new_comment']) ? GetScoreArray($value['total_score']) : $value['total_score'];
            // 评价上传的图片
            $value['upload_img'] = !empty($value['upload_img']) ? explode(',', unserialize($value['upload_img'])) : '';
            // 评价的内容
            $value['content'] = !empty($value['content']) ? htmlspecialchars_decode(unserialize($value['content'])) : '';
            // 回复的内容
            $adminReply = !empty($value['admin_reply']) ? unserialize($value['admin_reply']) : [];
            $adminReply['adminReply'] = !empty($adminReply['adminReply']) ? htmlspecialchars_decode($adminReply['adminReply']) : '';
            $value['admin_reply'] = $adminReply;
        }
        // dump($Comment);exit;

        // 新版评价处理，查询全部评价评分
        $totalScoreAll = Db::name('shop_order_comment')->alias('a')->field('total_score, is_new_comment')->where($where)->select();
        $totalScore = $praiseNum = 0;
        foreach ($totalScoreAll as $score) {
            // 计算总评分
            $score['total_score'] = empty($score['is_new_comment']) ? GetScoreArray($score['total_score']) : $score['total_score'];
            $totalScore = intval($totalScore) + intval($score['total_score']);
            // 好评数统计(4星以上算好评)
            if (intval($score['total_score']) >= 4) $praiseNum++;
        }
        // 评价总数
        $totalScoreRows = count($totalScoreAll);
        // 好评率(4星以上算好评)
        $praiseRate = intval($totalScoreRows) == 0 ? 0 : intval((intval($praiseNum) / intval($totalScoreRows)) * 100);
        if (0 === intval($praiseRate)) $praiseRate = 100;
        // 总评分 / 评价人数 = 评分平均值
        // $averageRating = floatval(sprintf("%.2f", strval(intval($totalScore) / intval($totalScoreRows))));
        $averageRating = intval($totalScoreRows) == 0 ? 0 : sprintf("%.1f", strval(intval($totalScore) / intval($totalScoreRows)));
        if (0 === intval($averageRating)) $averageRating = '5.0';
        // 平均值占比
        $averageRatingRate = floatval(sprintf("%.2f", (strval($averageRating) / strval(5)) * strval(100)));
        $this->assign('praiseRate', $praiseRate);
        $this->assign('averageRating', $averageRating);
        $this->assign('totalScoreRows', $totalScoreRows);
        $this->assign('averageRatingRate', $averageRatingRate);

        // 加载渲染模板
        $this->assign('PageObj', $Page);
        $this->assign('Page', $Page->show());
        $this->assign('Comment', $Comment);
    }

    // 联动地址获取
    public function ajax_get_region_data()
    {
        $parent_id = input('param.parent_id/d');
        // 获取指定区域ID下的城市并判断是否需要处理特殊市返回值
        $RegionData = $this->SpecialCityDealWith($parent_id);
        // 处理数据
        $region_html = $region_names = $region_ids = '';
        if ($RegionData) {
            // 拼装下拉选项
            foreach ($RegionData as $key => $value) {
                $region_html .= "<option value='{$value['id']}'>{$value['name']}</option>";
                if ($key > '0') {
                    $region_names .= '，';
                    $region_ids   .= ',';
                }
                $region_names .= $value['name'];
                $region_ids   .= $value['id'];
            }
        }
        $return = [
            'region_html'  => $region_html,
            'region_names' => $region_names,
            'region_ids'   => $region_ids,
//            'parent_array' => config('global.field_region_all_type'),
        ];
        echo json_encode($return);
    }

    // 获取指定区域ID下的城市并判断是否需要处理特殊市返回值
    // 特殊市：北京市，上海市，天津市，重庆市
    private function SpecialCityDealWith($parent_id = 0)
    {
        empty($parent_id) && $parent_id = 0;

        /*parent_id在特殊范围内则执行*/
        // 处理北京市，上海市，天津市，重庆市逻辑
        $RegionData   = Db::name('region')->where("parent_id", $parent_id)->select();
        $parent_array = config('global.field_region_type');
        if (in_array($parent_id, $parent_array)) {
            $region_ids = get_arr_column($RegionData, 'id');
            $RegionData = Db::name('region')->where('parent_id', 'IN', $region_ids)->select();
        }
        /*结束*/
        return $RegionData;
    }
}