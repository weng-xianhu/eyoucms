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
 * Date: 2021-01-26
 */

namespace app\admin\controller;

use think\Page;
use think\Db;
use think\Config;

class ShopComment extends Base
{

    public function _initialize()
    {
        parent::_initialize();

        $functionLogic = new \app\common\logic\FunctionLogic;
        $functionLogic->check_authorfile(2);
        
        // 产品属性表
        $this->shop_order_comment_db = Db::name('shop_order_comment');
    }

    // 评价列表
    public function comment_index()
    {
        $functionLogic = new \app\common\logic\FunctionLogic;
        $assign_data = $functionLogic->comment_index();
        
        $this->assign($assign_data);
        return $this->fetch();
    }

    // 评价详情
    public function comment_details()
    {
        $comment_id = input('param.comment_id');
        if (!empty($comment_id)) {
            // 退换货信息
            $field = 'a.comment_id, a.order_id, a.users_id, a.order_code, a.product_id, a.upload_img, a.content, a.total_score, a.is_show, a.add_time, b.product_name, b.product_price, b.litpic as product_img, b.num as product_num, b.data, c.username, c.nickname';
            $Comment[0] = $this->shop_order_comment_db->alias('a')->where('comment_id', $comment_id)
                ->field($field)
                ->join('__SHOP_ORDER_DETAILS__ b', 'a.details_id = b.details_id', 'LEFT')
                ->join('__USERS__ c', 'a.users_id = c.users_id')
                ->find();
            $array_new = get_archives_data($Comment, 'product_id');
            $Comment = $Comment[0];
            
            // 评价上传的图片
            $Comment['upload_img'] = !empty($Comment['upload_img']) ? explode(',', unserialize($Comment['upload_img'])) : '';

            // 商品规格
            $Comment['product_spec'] = str_replace("&lt;br/&gt;", " || ", unserialize($Comment['data'])['spec_value']);

            // 商品图片
            $Comment['product_img']  = handle_subdir_pic(get_default_pic($Comment['product_img']));

            // 商品链接
            $Comment['arcurl'] = get_arcurl($array_new[$Comment['product_id']]);
            
            // 商品评价评分
            $Comment['order_total_score'] = Config::get('global.order_total_score')[$Comment['total_score']];

            // 评价转换星级评分
            $Comment['total_score'] = GetScoreArray($Comment['total_score']);

            // 评价的内容
            $Comment['content'] = !empty($Comment['content']) ? htmlspecialchars_decode(unserialize($Comment['content'])) : '';

            // 会员信息
            $Users = Db::name('users')->field('users_id, username, nickname, mobile')->find($Service['users_id']);
            $Users['nickname'] = empty($Users['nickname']) ? $Users['username'] : $Users['nickname'];

            // 加载数据
            $this->assign('Users', $Users);
            $this->assign('Comment', $Comment);
            return $this->fetch('comment_details');
        } else {
            $this->error('非法访问！');
        }
    }

    // 评价删除
    public function comment_del()
    {
        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if(IS_POST && !empty($id_arr)){
            $ResultID = $this->shop_order_comment_db->where(['comment_id' => ['IN', $id_arr]])->delete();
            if (!empty($ResultID)) {
                foreach ($id_arr as $key => $val) {
                    cache('EyouHomeAjaxComment_' . $val, null, null, 'shop_order_comment');
                }
                adminLog('删除评价-id：'.implode(',', $id_arr));
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
    }
}