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
namespace app\user\logic;

use think\Model;
use think\Db;
use think\Page;
use think\Config;

/**
 * @package common\Logic
 */
load_trait('controller/Jump');
class FunctionLogic extends Model
{
    use \traits\controller\Jump;

    /**
     * 商品评价 - 读取会员自身所有服务单信息
     * @param [type] $users_id   [description]
     * @param [type] $order_code [description]
     */
    public function GetAllCommentInfo($users_id = null, $order_code = null)
    {
        $functionLogic = new \app\common\logic\FunctionLogic;
        $functionLogic->check_authorfile(2);

        $where = [
            'a.order_id' => ['>', 0],
            'a.users_id' => $users_id
        ];
        if (!empty($order_code)) $where['a.order_code'] = ['LIKE', "%{$order_code}%"];

        $count   = Db::name('shop_order_comment')->alias('a')->where($where)->count('comment_id');
        $pageObj = new Page($count, config('paginate.list_rows'));

        // 订单主表数据查询
        $field = 'a.comment_id, a.order_id, a.order_code, a.product_id, a.total_score, a.is_new_comment, a.is_show, a.add_time, b.product_name, b.product_price, b.litpic as product_img, b.num as product_num';
        $Comment = Db::name('shop_order_comment')->alias('a')->where($where)
            ->field($field)
            ->join('__SHOP_ORDER_DETAILS__ b', 'a.details_id = b.details_id', 'LEFT')
            ->limit($pageObj->firstRow.','.$pageObj->listRows)
            ->order('comment_id desc')
            ->select();

        $New = get_archives_data($Comment, 'product_id');
        foreach ($Comment as &$value) {
            // 商品图片
            $value['product_img']  = handle_subdir_pic(get_default_pic($value['product_img']));
            // 商品链接
            $value['ArchivesUrl']  = urldecode(arcurl('home/Product/view', $New[$value['product_id']]));
            // 订单详情
            $value['OrDetailsUrl'] = url('user/Shop/shop_order_details', ['order_id' => $value['order_id']]);
            // 评价详情
            $value['ViewCommentUrl'] = $value['ArchivesUrl'];
            // 评价转换星级评分
            $value['total_score'] = empty($value['is_new_comment']) ? GetScoreArray($value['total_score']) : $value['total_score'];
            // 商品评价评分
            $value['order_total_score'] = Config::get('global.order_total_score')[$value['total_score']];
        }

        $Return['Comment'] = $Comment;
        $Return['pageStr'] = $pageObj->show();

        return $Return;
    }
}