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
 * Date: 2019-1-25
 */

namespace app\user\controller;

use think\Db;
use think\Config;
use think\Cookie;
use think\Request;

class Article extends Base
{
    public function _initialize()
    {
        parent::_initialize();
        $this->article_order_db = Db::name('article_order');
    }

    //购买
    public function buy()
    {
        if (IS_AJAX_POST) {
            // 提交的订单信息判断
            $post = input('post.');
            if (empty($post['aid'])) $this->error('操作异常，请刷新重试');

            // 查询是否已购买
            $where = [
                'order_status' => 1,
                'product_id' => intval($post['aid']),
                'users_id' => $this->users_id
            ];
            $count = $this->article_order_db->where($where)->count();
            if (!empty($count)) $this->error('已购买过');

            // 查看是否已生成过订单
            $where['order_status'] = 0;
            $articleOrder = $this->article_order_db->where($where)->order('order_id desc')->find();

            // 查询文章文档内容
            $where = [
                'is_del' => 0,
                'status' => 1,
                'aid' => $post['aid'],
                'arcrank' => ['>', -1]
            ];
            $archives = Db::name('archives')->field('aid, title, litpic, users_price')->where($where)->find();
            if (empty($archives)) $this->error('操作异常，请刷新重试');
            $archives['users_price'] = get_discount_price($this->users, $archives['users_price']);

            // 订单生成规则
            $time = getTime();
            $orderCode = date('Y') . $time . rand(10, 100);
            if (!empty($articleOrder)) {
                // 更新订单信息
                $orderID = !empty($articleOrder['order_id']) ? intval($articleOrder['order_id']) : 0;
                $orderData = [
                    'order_code'      => $orderCode,
                    'users_id'        => $this->users_id,
                    'order_status'    => 0,
                    'order_amount'    => $archives['users_price'],
                    'product_id'      => $archives['aid'],
                    'product_name'    => $archives['title'],
                    'product_litpic'  => get_default_pic($archives['litpic']),
                    'lang'            => $this->home_lang,
                    'update_time'     => $time
                ];
                $this->article_order_db->where('order_id', $orderID)->update($orderData);
            } else {
                // 生成订单并保存到数据库
                $orderData = [
                    'order_code'      => $orderCode,
                    'users_id'        => $this->users_id,
                    'order_status'    => 0,
                    'order_amount'    => $archives['users_price'],
                    'pay_time'        => '',
                    'pay_name'        => '',
                    'wechat_pay_type' => '',
                    'pay_details'     => '',
                    'product_id'      => $archives['aid'],
                    'product_name'    => $archives['title'],
                    'product_litpic'  => get_default_pic($archives['litpic']),
                    'lang'            => $this->home_lang,
                    'add_time'        => $time,
                    'update_time'     => $time
                ];
                $orderID = $this->article_order_db->insertGetId($orderData);
            }

            // 保存成功
            if (!empty($orderID)) {
                // 支付结束后返回的URL
                $ReturnUrl = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $post['return_url'];
                cookie($this->users_id . '_' . $post['aid'] . '_EyouArticleViewUrl', $ReturnUrl);
                // 对ID和订单号加密，拼装url路径
                $paydata = [
                    'type' => 9,
                    'order_id' => $orderID,
                    'order_code' => $orderData['order_code'],
                ];
                // 先 json_encode 后 md5 加密信息
                $paystr = md5(json_encode($paydata));
                // 清除之前的 cookie
                Cookie::delete($paystr);
                // 存入 cookie
                cookie($paystr, $paydata);
                // 跳转链接
                // if (isMobile()) {
                //     $PaymentUrl = urldecode(url('user/Pay/pay_recharge_detail', ['paystr'=>$paystr]));//第一种支付
                // } else {
                //     $PaymentUrl = urldecode(url('user/Article/pay_recharge_detail', ['paystr'=>$paystr]));//第二种支付,弹框支付
                // }
                $this->success('订单已生成！', urldecode(url('user/Article/pay_recharge_detail', ['paystr' => $paystr])));
            }
        } else {
            abort(404);
        }
    }

    // 充值详情
    public function pay_recharge_detail()
    {
        $url = url('user/Article/index');
        // 接收数据读取解析
        $paystr = input('param.paystr/s', '');
        $paydata = !empty($paystr) ? cookie($paystr) : [];
        if (!empty($paydata['order_id']) && !empty($paydata['order_code'])) {
            // 订单信息
            $order_id   = !empty($paydata['order_id']) ? intval($paydata['order_id']) : 0;
            $order_code = !empty($paydata['order_code']) ? $paydata['order_code'] : '';
        } else {
            $this->error('订单不存在或已变更', $url);
        }

        // 处理数据
        if (is_array($paydata) && (!empty($order_id) || !empty($order_code))) {
            $data = [];
            if (!empty($order_id)) {
                // 余额开关
                $pay_balance_open = getUsersConfigData('pay.pay_balance_open');
                if (!is_numeric($pay_balance_open) && empty($pay_balance_open)) $pay_balance_open = 1;

                // 查询文章支付订单
                if (!empty($paydata['type']) && 9 == $paydata['type']) {
                    $where = [
                        'order_id'   => $order_id,
                        'order_code' => $order_code,
                        'users_id'   => $this->users_id,
                        'lang'       => $this->home_lang
                    ];
                    $data = Db::name('article_order')->where($where)->find();
                    if (empty($data)) $this->error('订单不存在或已变更', $url);
                    if (in_array($data['order_status'], [1])) $this->error('订单已支付，即将跳转', $url);

                    // 组装数据返回
                    $data['transaction_type'] = 9; // 交易类型，9为购买文章
                    $data['unified_id']       = $data['order_id'];
                    $data['unified_number']   = $data['order_code'];
                    $data['cause']            = $data['product_name'];
                    $data['pay_balance_open'] = $pay_balance_open;
                    $this->assign('data', $data);
                }
            }
            return $this->fetch('system/article_pay');
        }
        $this->error('参数错误！');
    }
}