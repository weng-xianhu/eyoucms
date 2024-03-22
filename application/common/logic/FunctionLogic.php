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

namespace app\common\logic;

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
     * 验证功能版sq文件 - 应用于不加密的控制器文件
     * @return [type] [description]
     */
    public function validate_authorfile($pid = '') {
        $this->check_authorfile($pid);
    }

    /**
     * 验证功能版sq文件
     * @return [type] [description]
     */
    public function check_authorfile($pid = '') {
        $buypid = $this->getAuthortokenInfo('pid');
        if ($pid > $buypid) {
            $authormsg = "authormsg{$pid}";
            $authormsg = $this->getAuthortokenInfo($authormsg);
            if (MODULE_NAME == 'admin') {
                $this->error($authormsg);
            } else {
                die("<div style='text-align:center; font-size:20px; font-weight:bold; margin:150px 0px;'>{$authormsg}</div>");
            }
        } else {
            $code = $this->getAuthortokenInfo('code');
            $file = "./data/conf/{$code}.txt";
            if (2 <= $buypid && !file_exists($file)) {
                $php_servicemeal = 1;
                $is_old = $this->getAuthortokenInfo('is_old');
                if (1 == $is_old) {
                    $php_servicemeal = 1.5;
                }
                tpCache('php', ['php_servicemeal' => $php_servicemeal]);
                $authorfilemsg = $this->getAuthortokenInfo('authorfilemsg');
                if (MODULE_NAME == 'admin') {
                    $this->error($authorfilemsg);
                } else {
                    die("<div style='text-align:center; font-size:20px; font-weight:bold; margin:150px 0px;'>{$authorfilemsg}</div>");
                }
            }
        }
    }

    /**
     * 获取网站sq信息
     * @return [type] [description]
     */
    public function getAuthortokenInfo($field = '') {
        $authortokenInfo = tpCache('php.php_serviceinfo');
        $authortokenInfo = mchStrCode($authortokenInfo, 'DECODE');
        $authortokenInfo = json_decode($authortokenInfo, true);
        if (empty($field)) {
            return !empty($authortokenInfo) ? $authortokenInfo : [];
        } else if (isset($authortokenInfo[$field])) {
            if ('pid' == $field) {
                tpCache('php', ['php_servicemeal' => $authortokenInfo[$field]]);
            } else if ('authormsg' == $field) {
                $authortokenInfo[$field] = '如有疑问，联系客服！';
            }
            return $authortokenInfo[$field];
        } else {
            return ('pid' == $field) ? 0 : '';
        }
    }

    /**
     * 保存积分设置
     * @return [type] [description]
     */
    public function scoreConf($post = [])
    {
        getUsersConfigData('score', $post);
    }

    // 评价列表
    public function comment_index()
    {
        $functionLogic = new \app\common\logic\FunctionLogic;
        $functionLogic->check_authorfile(2);

        $assign_data = array();
        $condition   = array();

        // 订单号查询
        $order_code = input('order_code/s');
        if (!empty($order_code)) $condition['a.order_code'] = array('LIKE', "%{$order_code}%");

        // 分页查询
        $count = Db::name('shop_order_comment')->alias('a')->where($condition)->count('comment_id');
        $Page  = new Page($count, config('paginate.list_rows'));
        $show = $Page->show();
        $assign_data['page']  = $show;
        $assign_data['pager'] = $Page;

        // 评价查询
        $field = 'a.comment_id, a.order_id, a.users_id, a.order_code, a.product_id, a.content, a.admin_reply, a.total_score, a.is_new_comment, a.is_show, a.add_time, b.product_name, b.product_price, b.litpic as product_img, b.num as product_num, c.username, c.nickname, c.head_pic, d.title, d.users_price, d.litpic';
        $Comment = Db::name('shop_order_comment')->alias('a')->where($condition)
            ->field($field)
            ->join('__SHOP_ORDER_DETAILS__ b', 'a.details_id = b.details_id', 'LEFT')
            ->join('__USERS__ c', 'a.users_id = c.users_id')
            ->join('__ARCHIVES__ d', 'a.product_id = d.aid')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->order('comment_id desc')
            ->select();

        // 评价数据处理
        $New = get_archives_data($Comment, 'product_id');
        foreach ($Comment as &$value) {
            $value['head_pic'] = handle_subdir_pic(get_default_pic($value['head_pic']));
            // 如果不存在商品标题则执行
            if (empty($value['product_name'])) $value['product_name'] = $value['title'];
            // 如果不存在商品价格则执行
            if (empty($value['product_price'])) $value['product_price'] = $value['users_price'];
            // 如果不存在商品图则执行
            if (empty($value['product_img'])) $value['product_img'] = $value['litpic'];
            // 如果不存在商品数量则执行
            if (empty($value['product_num'])) $value['product_num'] = 1;
            // 是否属于后台系统评价
            $value['systemComment'] = 0;
            if (empty($value['order_id']) && empty($value['order_code']) && empty($value['details_id'])) {
                $value['systemComment'] = 1;
            }
            // 商品图片
            $value['product_img']  = handle_subdir_pic(get_default_pic($value['product_img']));
            // 商品链接
            $value['arcurl'] = get_arcurl($New[$value['product_id']]);
            // 评价转换星级评分，注释暂停使用，显示实际星评分
            $value['total_score'] = empty($value['is_new_comment']) ? GetScoreArray($value['total_score']) : $value['total_score'];
            // 商品评价评分
            $value['order_total_score'] = Config::get('global.order_total_score')[$value['total_score']];
            // 评价的内容
            $value['content'] = !empty($value['content']) ? htmlspecialchars_decode(unserialize($value['content'])) : '';
            $value['content'] = @msubstr($value['content'], 0, 60, true);
            // 回复的内容
            $adminReply = !empty($value['admin_reply']) ? unserialize($value['admin_reply']) : [];
            $adminReply['adminReply'] = !empty($adminReply['adminReply']) ? htmlspecialchars_decode($adminReply['adminReply']) : '';
            $value['admin_reply'] = $adminReply;
        }

        $assign_data['Comment']  = $Comment;

        return $assign_data;
    }
}