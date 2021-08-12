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
 * Date: 2020-05-22
 */

namespace app\user\controller;

use think\Page;
use think\Db;
use think\Config;
use app\user\logic\PayApiLogic;

class PayApi extends Base {

    /**
     * 构造方法
     */
    public function __construct(){
        parent::__construct();
        // 支付API配置
        $this->pay_api_config_db = Db::name('pay_api_config');

        // 支付API逻辑层
        $this->PayApiLogic = new PayApiLogic();
    }

    // 支付接口列表
    public function select_pay_method()
    {
        $post = input('post.');

        /* 订单查询 */
        $Order = $this->PayApiLogic->GetFindOrderData($post, true);
        /* END */

        /* 支付API配置信息查询 */
        $Config  = $this->PayApiLogic->GetPayApiConfig($post);
        $PayInfo = $Config['pay_info'];
        /* END */

        if (!empty($Config) && 1 == $Config['pay_id'] && 'wechat' == $Config['pay_mark']) {
            /*系统内置的微信支付*/
            $this->PayApiLogic->UseWeChatPay($post, $Order, $PayInfo);
            /* END */

        } else if (!empty($Config) && 2 == $Config['pay_id'] && 'alipay' == $Config['pay_mark']) {
            /*系统内置的支付宝支付*/
            $this->PayApiLogic->UseAliPayPay($post, $Order, $PayInfo);
            /* END */

        } else if (!empty($Config) && !empty($Config['pay_mark']) && 0 == $Config['system_built']) {
            /*第三方插件*/
            $ControllerName  = "\weapp\\" . $Config['pay_mark']."\controller\\" . $Config['pay_mark'];
            $UnifyController = new $ControllerName;

            // 虎皮椒支付成功后返回页面，主要用于手机浏览器端、微信端使用虎皮椒支付后页面跳转
            if (1 == $post['transaction_type']) {
                $UnifiedUrl = url('user/Pay/pay_consumer_details');
            } else if (2 == $post['transaction_type']) {
                $UnifiedUrl = url('user/Shop/shop_order_details', ['order_id' => $post['unified_id']]);
            } else if (8 == $post['transaction_type']) {
                $UnifiedUrl = cookie($this->users_id . '_' . $Order['product_id'] . '_EyouMediaViewUrl');
            } else if (9 == $post['transaction_type']) {
                $UnifiedUrl = cookie($this->users_id . '_' . $Order['product_id'] . '_EyouArticleViewUrl');
            }
            $ReturnUrl = $_SERVER['HTTP_ORIGIN'] . $UnifiedUrl;

            $ResultData = $UnifyController->UnifyGetPayAction($PayInfo, $Order, $ReturnUrl);
            if (!empty($ResultData)) {
                $this->success('订单支付中', $ResultData['url'], $ResultData['data']);
            } else {
                $this->error('订单异常，刷新重试');
            }
            /* END */
        }
    }

    // 订单支付轮询
    public function order_pay_polling()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');

            /* 订单查询 */
            $Order = $this->PayApiLogic->GetFindOrderData($post);
            /* END */

            /* 支付API配置信息查询 */
            $Config  = $this->PayApiLogic->GetPayApiConfig($post);
            $PayInfo = $Config['pay_info'];
            /* END */

            /* 根据所选的支付方式执行相应操作 */
            if (!empty($Config) && 1 == $Config['pay_id'] && 'wechat' == $Config['pay_mark']) {
                // 系统内置微信支付---微信支付订单处理
                $this->PayApiLogic->WeChatPayProcessing($post, $Order, $PayInfo, $Config);

            } else if (!empty($Config) && 2 == $Config['pay_id'] && 'alipay' == $Config['pay_mark']) {
                // 系统内置支付宝支付---支付宝支付订单处理
                $this->PayApiLogic->AliPayPayProcessing($post, $Order, $PayInfo, $Config);
                
            } else if (!empty($Config) && !empty($Config['pay_mark']) && 0 == $Config['system_built']) {
                // 第三方支付
                $ControllerName  = "\weapp\\" . $Config['pay_mark']."\controller\\" . $Config['pay_mark'];
                $UnifyController = new $ControllerName;
                $ResultData = $UnifyController->OtherPayProcessing($PayInfo, $post['unified_number']);
                if (is_array($ResultData)) {
                    // 订单数据更新处理
                    $this->PayApiLogic->OrderProcessing($post, $Order, $ResultData, $Config);
                } else {
                    $this->success($ResultData);
                }
            }
            /* END */
        }
    }

    // 购物余额支付(购物+购买视频+购买文章时使用)
    public function balance_payment()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            $post['unified_id'] = intval($post['unified_id']);
            if (empty($post['unified_id']) || !is_numeric($post['unified_id'])) {
                $this->error('订单异常，刷新重试');
            }
            if (8 == $post['transaction_type']) {
                // 视频购买
                $Data = Db::name('media_order')->find($post['unified_id']);
                if (empty($Data)) $this->error('订单异常，刷新重试');

                $ViewUrl = cookie($this->users_id . '_' . $Data['product_id'] . '_EyouMediaViewUrl');
                if (in_array($Data['order_status'], [1])) $this->success('订单已支付！即将跳转', $ViewUrl);

                if ($this->users['users_money'] >= $Data['order_amount']) {
                    // 订单更新条件
                    $OrderWhere = [
                        'order_id'  => $Data['order_id'],
                        'users_id'  => $this->users_id,
                        'lang'      => $this->home_lang
                    ];

                    // 订单更新数据，更新为已付款
                    $post['payment_type'] = '余额支付';
                    $post['payment_amount'] = $Data['order_amount'];
                    $OrderData = [
                        'order_status' => 1,
                        'pay_details'  => serialize($post),
                        'pay_time'     => getTime(),
                        'update_time'  => getTime()
                    ];
                    $ResultID = Db::name('media_order')->where($OrderWhere)->update($OrderData);

                    // 订单更新后续操作
                    if (!empty($ResultID)) {
                        $Where = [
                            'users_id' => $this->users_id,
                            'lang'     => $this->home_lang
                        ];
                        $UsersData = [
                            'users_money' => $this->users['users_money'] - $Data['order_amount'],
                            'update_time' => getTime()
                        ];
                        $users_id = Db::name('users')->where($Where)->update($UsersData);

                        // 订单操作完成，返回跳转
                        $this->success('支付成功，处理订单完成', $ViewUrl);
                    }
                } else {
                    $url = urldecode(url('user/Pay/pay_account_recharge'));
                    $this->error('余额不足，请先充值！', $url);
                }
            }else if (9 == $post['transaction_type']) {
                // 文章购买
                $Data = Db::name('article_order')->find($post['unified_id']);
                if (empty($Data)) $this->error('订单异常，刷新重试');

                $ViewUrl = cookie($this->users_id . '_' . $Data['product_id'] . '_EyouArticleViewUrl');
                if (in_array($Data['order_status'], [1])) $this->success('订单已支付！即将跳转', $ViewUrl);

                if ($this->users['users_money'] >= $Data['order_amount']) {
                    // 订单更新条件
                    $OrderWhere = [
                        'order_id'  => $Data['order_id'],
                        'users_id'  => $this->users_id,
                        'lang'      => $this->home_lang
                    ];

                    // 订单更新数据，更新为已付款
                    $post['payment_type'] = '余额支付';
                    $post['payment_amount'] = $Data['order_amount'];
                    $OrderData = [
                        'order_status' => 1,
                        'pay_details'  => serialize($post),
                        'pay_time'     => getTime(),
                        'update_time'  => getTime()
                    ];
                    $ResultID = Db::name('article_order')->where($OrderWhere)->update($OrderData);

                    // 订单更新后续操作
                    if (!empty($ResultID)) {
                        $Where = [
                            'users_id' => $this->users_id,
                            'lang'     => $this->home_lang
                        ];
                        $UsersData = [
                            'users_money' => $this->users['users_money'] - $Data['order_amount'],
                            'update_time' => getTime()
                        ];
                        $users_id = Db::name('users')->where($Where)->update($UsersData);

                        // 订单操作完成，返回跳转
                        $this->success('支付成功，处理订单完成', $ViewUrl);
                    }
                } else {
                    $url = urldecode(url('user/Pay/pay_account_recharge'));
                    $this->error('余额不足，请先充值！', $url);
                }
            } else {
                $Where = [
                    'users_id' => $this->users_id,
                    'lang'     => $this->home_lang
                ];
                // 商城商品购买
                $OrderWhere = [
                    'order_id'      => $post['unified_id'],
                    'order_code'    => $post['unified_number'],
                ];
                $OrderWhere = array_merge($Where, $OrderWhere);
                $orderData = Db::name('shop_order')->field('order_id, order_code, order_amount, order_status, users_id')
                    ->where($OrderWhere)->find();
                if (empty($orderData)) $this->error('该订单不存在！');
                
                //1已付款(待发货)，2已发货(待收货)，3已完成(确认收货)，-1订单取消(已关闭)，4订单过期
                $url = urldecode(url('user/Shop/shop_order_details', ['order_id' => $orderData['order_id']]));
                if (in_array($orderData['order_status'], [1, 2, 3])) {
                    $this->success('订单已支付！即将跳转', $url);
                } else if ($orderData['order_status'] == 4) {
                    $this->success('订单已过期！即将跳转', $url);
                } else if ($orderData['order_status'] == -1) {
                    $this->success('订单已关闭！即将跳转', $url);
                }

                // 订单数据更新处理
                if ($this->users['users_money'] < $orderData['order_amount']) {
                    $url = urldecode(url('user/Pay/pay_account_recharge'));
                    $this->error('余额不足，若要使用余额支付，请去充值！',$url);
                } else {
                    $ret = Db::name('users')->where($Where)->update([
                        'users_money' => Db::raw('users_money-'.$orderData['order_amount']),
                        'update_time' => getTime(),
                    ]);
                    if (false !== $ret) {
                        $pay_details = [
                            'unified_id'        => $orderData['order_id'],
                            'unified_number'    => $orderData['order_code'],
                            'transaction_type'  => $post['transaction_type'],
                            'payment_amount'    => $orderData['order_amount'],
                            'payment_type'      => "余额支付",
                        ];
                        $returnData = pay_success_logic($this->users_id, $orderData['order_code'], $pay_details, 'balance');
                        if (is_array($returnData)) {
                            if (1 == $returnData['code']) {
                                $this->success($returnData['msg'], $returnData['url'], $returnData['data']);
                            } else {
                                $this->error($returnData['msg']);
                            }
                        }
                        // $this->error('支付成功，订单处理失败！');
                    }
                    $this->error('订单支付异常，请刷新后再进行支付！');
                }
            }
        }
    }

    // 微信支付，获取订单信息并调用微信接口，生成二维码用于扫码支付
    public function pay_wechat_png()
    {
        if (!empty($this->users_id)) {
            $unified_number   = input('param.unified_number/s');
            $transaction_type = input('param.transaction_type/d');
            if (in_array($transaction_type, [1,3])) {
                // 充值订单 / 会员升级订单
                $where  = array(
                    'users_id'     => $this->users_id,
                    'order_number' => $unified_number
                );
                $data  = Db::name('users_money')->where($where)->find();
                $out_trade_no = $data['order_number'];
                $total_fee    = $data['money'];

            } else if (2 == $transaction_type) {
                // 产品购买订单
                $where  = array(
                    'users_id'   => $this->users_id,
                    'order_code' => $unified_number
                );
                $data  = Db::name('shop_order')->where($where)->find();
                $out_trade_no = $data['order_code'];
                $total_fee    = $data['order_amount'];

            } else if (8 == $transaction_type) {
                // 视频购买订单
                $where  = array(
                    'users_id'   => $this->users_id,
                    'order_code' => $unified_number
                );
                $data  = Db::name('media_order')->where($where)->find();
                $out_trade_no = $data['order_code'];
                $total_fee    = $data['order_amount'];
            } else if (9 == $transaction_type) {
                // 文章购买订单
                $where  = array(
                    'users_id'   => $this->users_id,
                    'order_code' => $unified_number
                );
                $data  = Db::name('article_order')->where($where)->find();
                $out_trade_no = $data['order_code'];
                $total_fee    = $data['order_amount'];
            }

            // 调取微信支付链接
            $payUrl = model('PayApi')->payForQrcode($out_trade_no, $total_fee, $transaction_type);

            // 生成二维码加载在页面上
            vendor('wechatpay.phpqrcode.phpqrcode');
            $qrcode = new \QRcode;
            $pngurl = $payUrl;
            $qrcode->png($pngurl);
            exit();
        } else {
            $this->redirect('user/Users/login');
        }
    }

    // 会员升级支付处理
    public function users_upgrade_pay()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 判断是否存在支付方式
            if (!isset($post['pay_id'])) $this->error('网站支付配置未完善，升级服务暂停使用');

            // 处理API标识
            $post['pay_mark'] = $post['pay_mark'][$post['pay_id']];

            // 是否选择产品
            if (empty($post['type_id'])) $this->error('请选择购买产品！');

            /* 判断是否可以升级 */
            $this->PayApiLogic->IsAllowUpgrade($post);
            /* END */

            if (isset($post['pay_id']) && 0 == $post['pay_id']) {
                // 余额支付
                $this->PayApiLogic->BalancePayment($post['order_number']);
            } else {
                /* 支付API配置信息查询 */
                $Config  = $this->PayApiLogic->GetPayApiConfig($post);
                $PayInfo = $Config['pay_info'];
                /* END */

                if (!empty($Config) && 1 == $Config['pay_id'] && 'wechat' == $Config['pay_mark']) {
                    /*系统内置的微信支付*/
                    $this->PayApiLogic->WeChatPayment($post, $PayInfo);
                    /* END */

                } else if (!empty($Config) && 2 == $Config['pay_id'] && 'alipay' == $Config['pay_mark']) {
                    /*系统内置的支付宝支付*/
                    $this->PayApiLogic->AliPayPayment($post, $PayInfo);
                    /* END */

                } else if (!empty($Config) && !empty($Config['pay_mark']) && 0 == $Config['system_built']) {
                    /* 订单查询 */
                    $Order = $this->PayApiLogic->GetPayOrderData($post, $PayInfo, $Config['pay_mark']);
                    $Order['unified_amount'] = $Order['money'];
                    $Order['unified_number'] = $Order['order_number'];
                    /* END */

                    /*第三方插件*/
                    $ControllerName  = "\weapp\\" . $Config['pay_mark']."\controller\\" . $Config['pay_mark'];
                    $UnifyController = new $ControllerName;
                    // 虎皮椒支付成功后返回页面，主要用于手机浏览器端、微信端使用虎皮椒支付后页面跳转
                    $ReturnUrl = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . url('user/Level/level_centre');
                    $ResultData = $UnifyController->UnifyGetPayAction($PayInfo, $Order, $ReturnUrl);
                    if (!empty($ResultData)) {
                        $ResultData['data']['pay_id'] = $Config['pay_id'];
                        $ResultData['data']['pay_mark'] = $Config['pay_mark'];
                        $ResultData['data']['pay_type'] = $PayInfo['pay_type'];
                        $ResultData['data']['unified_id'] = $Order['moneyid'];
                        $ResultData['data']['unified_number'] = $Order['unified_number'];
                        $this->success('订单支付中', $ResultData['url'], $ResultData['data']);
                    } else {
                        $this->error('订单异常，刷新重试');
                    }
                    /* END */
                }
            }
        }
    }
}