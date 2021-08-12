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

namespace app\api\controller\v1;

use think\Db;
use think\Request;

class Users extends Base
{
    public $users;
    public $users_id;

    /**
     * 初始化操作
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->users    = $this->getUser();   // 用户信息
        $this->users_id = !empty($this->users['users_id']) ? intval($this->users['users_id']) : null;
        if (empty($this->users_id)) $this->error('请先登录');
    }

    /**
     * 我的订单列表
     * @param $dataType
     * @return array
     * @throws \think\exception\DbException
     */
    public function order_lists($dataType)
    {
        $list = model('v1.Shop')->getOrderList($this->users_id, $dataType);
        return $this->renderSuccess(compact('list'));
    }

    /**
     * 取消订单
     * @param $order_id
     * @return array
     * @throws \Exception
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function order_cancel($order_id)
    {
        if (IS_AJAX_POST && !empty($order_id)) {
            model('v1.Shop')->orderCancel($order_id, $this->users_id);
        }
        $this->error('订单取消失败！');
    }

    /**
     * 订单详情信息
     * @param $order_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function order_detail($order_id)
    {
        if (IS_AJAX) {
            // 订单详情
            $detail = model('v1.Shop')->getOrderDetail($order_id, $this->users_id);
            return $this->renderSuccess([
                'order'   => $detail,  // 订单详情
                'setting' => [],
            ]);
        }
        $this->error('订单读取失败！');
    }

    /**
     * 订单支付
     * @param int $order_id 订单id
     * @param int $payType 支付方式
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function order_pay($order_id, $payType = 20)
    {
        // 订单支付事件
        $order =  model('v1.Shop')->getOrderDetail($order_id, $this->users_id);
        // 判断订单状态
        if (!isset($order['order_status']) || $order['order_status'] != 0) {
            $this->error('很抱歉，当前订单不合法，无法支付');
        }
        // 构建微信支付请求
        $payment = model('v.Shop')->onOrderPayment($this->users, $order, $payType);
        if (isset($payment['code']) && empty($payment['code'])) {
            $this->error($payment['msg'] ?: '订单支付失败');
        }
        // 支付状态提醒
        $this->renderSuccess([
            'order_id' => $order['order_id'],   // 订单id
            'pay_type' => $payType,             // 支付方式
            'payment'  => $payment               // 微信支付参数
        ], ['success' => '支付成功', 'error' => '订单未支付']);
    }

    /**
     * 获取物流信息
     * @param $order_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function order_express($order_id, $timestamp = '')
    {
        // 订单详情
        $detail = model('v1.Shop')->getOrderDetail($order_id, $this->users_id);
        if (empty($detail['express_order'])) {
            return $this->error('没有物流信息');
        }
        // 获取物流信息
        /* @var \app\store\model\Express $model */
        $express = model('v1.Shop')->orderExpress($detail['express_name'], $detail['express_code'], $detail['express_order'], $timestamp);
        if (!empty($express)) {
            return $this->renderSuccess(compact('express'));
        }
        $this->error('没有找到物流信息！');
    }

    /**
     * 确认收货
     * @param $order_id
     * @return array
     * @throws \Exception
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function order_receipt($order_id)
    {
        if (IS_AJAX_POST && !empty($order_id)) {
            model('v1.Shop')->orderReceipt($order_id, $this->users_id);
        }
        $this->error('确认收货失败！');
    }


    /* -------------陈风任------------- */
    /**
     * 添加商品到购物车
     */
    public function shop_add_cart()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 数量判断
            if (empty($post['product_num']) || 0 > $post['product_num']) $this->error('请输入数量');

            // 默认会员ID
            $post['users_id'] = $this->users_id;

            // 商城模型
            $ShopModel = model('v1.Shop');

            // 商品是否已售罄
            $ShopModel->IsSoldOut($post);

            // 添加购物车
            $ShopModel->ShopAddCart($post);
        }
    }

    /**
     * 添加商品到购物车
     */
    public function shop_page_add_cart()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 数量判断
            if (empty($post['product_num']) || 0 > $post['product_num']) $this->error('请输入数量');
            // 默认会员ID
            $post['users_id'] = $this->users_id;

            // 商城模型
            $ShopModel = model('v1.Shop');

            // 添加购物车
            $ShopModel->ShopPageAddCart($post);
        }
    }

    /**
     * 立即购买
     */
    public function shop_buy_now()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 数量判断
            if (empty($post['product_num']) || 0 > $post['product_num']) $this->error('请输入数量');

            // 默认会员ID
            $post['users_id'] = $this->users_id;

            // 商城模型
            $ShopModel = model('v1.Shop');

            // 商品是否已售罄
            $ShopModel->IsSoldOut($post);

            // 立即购买
            $ShopModel->ShopBuyNow($post);
        }
    }

    /**
     * 产品购买
     */
    public function shop_product_buy()
    {
        if (IS_AJAX_POST) {
            // 获取解析数据
            $querystr = input('param.querystr/s');
            if (empty($querystr)) $this->error('无效链接！');

            // 商城模型
            $ShopModel = model('v1.Shop');

            // 获取商品信息进行展示
            $ShopModel->GetProductData($querystr, $this->users_id, $this->users['level_discount']);
        }
    }

    /**
     * 订单结算
     */
    public function shop_order_pay()
    {
        if (IS_AJAX_POST) {
            $post           = input('post.');
            $post['action'] = !empty($post['action']) ? $post['action'] : 'CreatePay';

            // 默认会员ID
            $post['users_id'] = $this->users_id;
            $post['openid']   = Db::name('weapp_diyminipro_mall_users')->where('users_id', $this->users_id)->getField('openid');

            // 商城模型
            $ShopModel = model('v1.Shop');

            // 操作分发
            if ('DirectPay' == $post['action']) {
                // 订单直接支付
                $ShopModel->OrderDirectPay($post);
            } else if ('CreatePay' == $post['action']) {
                // 获取解析数据
                if (empty($post['querystr'])) $this->error('无效链接！');

                // 获取商品信息生成订单并支付
                $ShopModel->ShopOrderPay($post, $this->users['level_discount']);
            }
        }
    }

    /**
     * 订单支付后续处理
     */
    public function shop_order_pay_deal_with()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');

            // 默认会员ID
            $post['users_id'] = $this->users_id;
            $post['openid']   = Db::name('weapp_diyminipro_mall_users')->where('users_id', $this->users_id)->getField('openid');

            // 商城模型
            $ShopModel = model('v1.Shop');

            // 订单支付后续处理
            $ShopModel->WechatAppletsPayDealWith($post);
        }
    }

    /**
     * 购物车操作(改变数量、选中状态、删除)
     */
    public function shop_cart_action()
    {
        if (IS_AJAX) {
            $param = input('param.');
            if (empty($param['action'])) $param['action'] = null;
            $param['users_id'] = $this->users_id;

            // 商城模型
            $ShopModel = model('v1.Shop');

            /* 购物车操作 */
            if ('add' == $param['action']) {
                // 数量 + 1
                $ShopModel->ShopCartNumAdd($param);
            } else if ('less' == $param['action']) {
                // 数量 - 1
                $ShopModel->ShopCartNumLess($param);
            } else if ('selected' == $param['action']) {
                // 是否选中
                $ShopModel->ShopCartSelected($param);
            } else if ('all_selected' == $param['action']) {
                // 是否全部选中
                $ShopModel->ShopCartAllSelected($param);
            } else if ('del' == $param['action']) {
                // 删除购物车商品
                $ShopModel->ShopCartDelete($param);
            } else {
                $this->error('请正确操作');
            }
            /* END */

        }
    }

    /**
     * 收货地址列表
     */
    public function shop_address_list()
    {
        if (IS_AJAX) {
            // 商城模型
            $ShopModel = model('v1.Shop');

            // 收货地址列表
            $ReturnData = $ShopModel->GetAllAddressList($this->users);
        }
    }

    /**
     * 收货地址操作分发
     */
    public function shop_address_action()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 商城模型
            $ShopModel = model('v1.Shop');

            // 操作分发
            if ('find_add' == $post['action']) {
                // 添加单条收货地址
                $ShopModel->FindAddAddr($post, $this->users_id);
            } else if ('find_edit' == $post['action']) {
                // 设置默认收货地址
                $ShopModel->FindEditAddr($post, $this->users_id);
            } else if ('default' == $post['action']) {
                // 设置默认收货地址
                $ShopModel->SetDefaultAddr($post, $this->users_id);
            } else if ('find_detail' == $post['action']) {
                // 获取单条收货地址
                $ShopModel->GetFindAddrDetail($post, $this->users);
            } else if ('find_del' == $post['action']) {
                // 删除单条收货地址
                $ShopModel->FindDelAddr($post, $this->users_id);
            } else {
                $this->error('请正确操作');
            }
        }
    }
    /* -------------END------------- */

    /**
     * 获取评价订单商品列表
     * @param $order_id
     * @return array
     * @throws \Exception
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function order_comment($order_id)
    {
        if (IS_AJAX) {
            $data = model('v1.Shop')->getOrderComment($order_id, $this->users_id);
            return $this->renderSuccess([
                'goods'   => $data,  // 订单详情
            ]);
        }
        $this->error('读取失败！');
    }

    /**
     * 保存评价
     * @return array
     */
    public function save_comment()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            return model('v1.Shop')->getSaveComment($post,$this->users_id);
        }
        $this->error('评价失败！');
    }

    /**
     * 用户领取优惠券
     */
    public function get_coupon($coupon_id)
    {
        if (IS_AJAX) {
            $where = [
                'coupon_id' => $coupon_id,
            ];
            //查询优惠券信息,结束发放时间/库存
            $coupon = Db::name('shop_coupon')->where($where)->find();
            if (!empty($coupon)) {
                if (1 > $coupon['coupon_stock']) {
                    $this->error('优惠券库存不足！');
                }
                if (getTime() > $coupon['end_date']) {
                    $this->error('优惠券发放已结束！');
                }
                $where['users_id']   = $this->users_id;
                $where['use_status'] = 0;
                $where['start_time'] = ['<=',getTime()];
                $where['end_time'] = ['>=',getTime()];

                $count = Db::name('shop_coupon_use')->where($where)->find();

                if (!empty($count)) {
                    $this->error('请勿重复领取！');
                } else {
                    $insert['coupon_id']   = $coupon_id;
                    $insert['coupon_code'] = $coupon['coupon_code'];
                    $insert['users_id']    = $this->users_id;
                    $insert['use_status']  = 0;
                    $insert['get_time']    = getTime();
                    $insert['add_time']    = getTime();
                    $insert['update_time'] = getTime();
                    //根据使用期限不同插入开始/结束使用时间
                    if (1 == $coupon['use_type']) {//固定期限
                        $insert['start_time'] = $coupon['use_start_time'];
                        $insert['end_time']   = $coupon['use_end_time'];
                    } else if (2 == $coupon['use_type']) {//当日开始N天有效
                        $insert['start_time'] = strtotime(date("Y-m-d", time()));
                        $insert['end_time']   = $insert['start_time'] + $coupon['valid_days'] * 86400;
                    } else if (3 == $coupon['use_type']) {//次日开始N天有效
                        $insert['start_time'] = strtotime(date("Y-m-d", time())) + 86400;
                        $insert['end_time']   = $insert['start_time'] + $coupon['valid_days'] * 86400;
                    }
                    if (!empty($insert)) {
                        $use_insert = Db::name('shop_coupon_use')->insert($insert);
                        if (!empty($use_insert)) {
                            //减库存
                            Db::name('shop_coupon')->where('coupon_id', $coupon_id)->setDec('coupon_stock');
                            $this->success('领取成功！');
                        }
                    }
                }
            }
        }
        $this->error('优惠券领取失败！');
    }

    /**
     * 获取我的优惠券
     */
    public function get_my_coupon($dataType)
    {
        $list = model('v1.Shop')->GetMyCouponList($this->users_id, $dataType);

        return $this->renderSuccess(compact('list'));
    }
    /**
     * 领券中心
     */
    public function get_coupon_center()
    {
        $list = model('v1.Shop')->GetCouponCenter($this->users_id);

        return $this->renderSuccess(compact('list'));
    }

    /**
     * 收藏/取消
     */
    public function get_collect()
    {
        if (IS_AJAX_POST) {
            $aid = input('param.aid/d');
            if(empty($aid)){
                $this->error('缺少文档ID！');
            }
            $count = Db::name('users_collection')->where([
                'aid'   => $aid,
                'users_id'  => $this->users_id,
            ])->count();
            if (empty($count)) {
                $addSave = Db::name('archives')->field('aid,channel,typeid,lang,title,litpic')->where('aid',$aid)->find();
                if(empty($addSave)){
                    $this->error('文档不存在！');
                }
                $addSave['add_time']  = getTime();
                $addSave['users_id']  = $this->users_id;
                $r = Db::name('users_collection')->insert($addSave);
                if (!empty($r)){
                    $this->success('已收藏', null, ['is_collect'=>1]);
                }
            }else{
                $r = Db::name('users_collection')->where(['aid'=>$aid,'users_id'=>$this->users_id])->delete();
                if (!empty($r)){
                    $this->success('已取消', null, ['is_collect'=>0]);
                }
            }
        }
        $this->error('请求错误！');
    }

    //获取收藏列表
    public function get_collect_list()
    {
        if (IS_AJAX) {
            $param = input('param.');
            $list = model('v1.User')->GetMyCollectList($param);

            return $this->renderSuccess(compact('list'));
        }
        $this->error('请求错误！');
    }
    //修改头像/昵称
    public function save_user_info()
    {
        if (IS_AJAX_POST) {
            $head_pic = input('param.head_pic/s');
            $nickname = input('param.nickname/s');
            if(!empty($head_pic) || !empty($nickname)){
                $update = ['update_time'=>getTime()];
                if (!empty($head_pic)){
                    $update['head_pic'] = $head_pic;
                }
                if (!empty($nickname)){
                    $update['nickname'] = $nickname;
                }
                $r = Db::name('users')->where(['users_id'=>$this->users_id])->update($update);
                if (!empty($r)){
                    $this->success('保存成功');
                }
            }
            $this->error('保存失败');
        }
    }
}