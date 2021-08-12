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

use think\Page;
use think\Db;
use think\Config;
use think\Cookie;
use think\Request;

class Article extends Base
{
    public function _initialize()
    {
        parent::_initialize();
        $this->users_db = Db::name('users');
        $this->article_order_db = Db::name('article_order');
    }

    //购买
    public function buy() {
        if (IS_AJAX_POST) {

            // 提交的订单信息判断
            $post = input('post.');
            if (empty($post['aid'])) $this->error('操作异常，请刷新重试');

            // 查询是否已购买
            $where = [
                'order_status' => 1,
                'product_id' => $post['aid'],
                'users_id' => $this->users_id
            ];
            $count = $this->article_order_db->where($where)->count();
            if (!empty($count)) $this->error('已购买过');

            // 查询视频文档内容
            $Where = [
                'is_del' => 0,
                'status' => 1,
                'aid' => $post['aid'],
                'arcrank' => ['>', -1]
            ];
            $list = Db::name('archives')->where($Where)->find();
            if (empty($list)) $this->error('操作异常，请刷新重试');

            // 查看是否已生成过订单
            $where['order_status'] = 0;
            $order =$this->article_order_db->where($where)->order('order_id desc')->find();
            if (!empty($order)) {
                $OrderID = $order['order_id'];
                // 更新订单信息
                $time = getTime();
                $OrderData = [
                    'order_code'      => $order['order_code'],
                    'users_id'        => $this->users_id,
                    'order_status'    => 0,
                    'order_amount'    => $list['users_price'],
                    'product_id'      => $list['aid'],
                    'product_name'    => $list['title'],
                    'product_litpic'  => get_default_pic($list['litpic']),
                    'lang'            => $this->home_lang,
                    'add_time'        => $time,
                    'update_time'     => $time
                ];
                $this->article_order_db->where('order_id', $OrderID)->update($OrderData);
            } else {
                // 生成订单并保存到数据库
                $time = getTime();
                $OrderData = [
                    'order_code'      => date('Y') . $time . rand(10,100),
                    'users_id'        => $this->users_id,
                    'order_status'    => 0,
                    'order_amount'    => $list['users_price'],
                    'pay_time'        => '',
                    'pay_name'        => '',
                    'wechat_pay_type' => '',
                    'pay_details'     => '',
                    'product_id'      => $list['aid'],
                    'product_name'    => $list['title'],
                    'product_litpic'  => get_default_pic($list['litpic']),
                    'lang'            => $this->home_lang,
                    'add_time'        => $time,
                    'update_time'     => $time
                ];
                $OrderID = $this->article_order_db->insertGetId($OrderData);
            }

            // 保存成功
            if (!empty($OrderID)) {
                // 支付结束后返回的URL
                $ReturnUrl = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $post['return_url'];
                cookie($this->users_id . '_' . $post['aid'] . '_EyouArticleViewUrl', $ReturnUrl);

                // 对ID和订单号加密，拼装url路径
                $Paydata = [
                    'type' => 9,
                    'order_id' => $OrderID,
                    'order_code' => $OrderData['order_code'],
                ];

                // 先 json_encode 后 md5 加密信息
                $Paystr = md5(json_encode($Paydata));

                // 清除之前的 cookie
                Cookie::delete($Paystr);

                // 存入 cookie
                cookie($Paystr, $Paydata);

                // 跳转链接
                if (isMobile()){
                    $PaymentUrl = urldecode(url('user/Pay/pay_recharge_detail',['paystr'=>$Paystr]));//第一种支付
                }else{
                    $PaymentUrl = urldecode(url('user/Article/pay_recharge_detail',['paystr'=>$Paystr]));//第二种支付,弹框支付
                }
                $this->success('订单已生成！', $PaymentUrl);
            }
        } else {
            abort(404);
        }
    }
    // 充值详情
    public function pay_recharge_detail()
    {
        // 接收数据读取解析
        $Paystr = input('param.paystr/s');
        $PayData = cookie($Paystr);

        if (!empty($PayData['order_id']) && !empty($PayData['order_code'])) {
            // 订单信息
            $order_id   = !empty($PayData['order_id']) ? intval($PayData['order_id']) : 0;
            $order_code = !empty($PayData['order_code']) ? $PayData['order_code'] : '';
        } else {
            $this->error('订单不存在或已变更', url('user/Shop/shop_centre'));
        }

        // 处理数据
        if (is_array($PayData) && (!empty($order_id) || !empty($order_code))) {
            $data = [];
            if (!empty($order_id)) {
                /*余额开关*/
                $pay_balance_open = getUsersConfigData('pay.pay_balance_open');
                if (!is_numeric($pay_balance_open) && empty($pay_balance_open)) {
                    $pay_balance_open = 1;
                }
                /*end*/

                if(!empty($PayData['type']) && 9 == $PayData['type']){
                    //文章支付订单
                    $where = [
                        'order_id'   => $order_id,
                        'order_code' => $order_code,
                        'users_id'   => $this->users_id,
                        'lang'       => $this->home_lang
                    ];
                    $data = Db::name('article_order')->where($where)->find();
                    if (empty($data)) $this->error('订单不存在或已变更', url('user/Article/index'));

                    $url = url('user/Article/index');
                    if (in_array($data['order_status'], [1])) $this->error('订单已支付，即将跳转！', $url);

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