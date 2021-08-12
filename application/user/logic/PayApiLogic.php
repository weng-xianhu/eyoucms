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

namespace app\user\logic;

use think\Model;
use think\Db;
use think\Request;
use think\Config;

/**
 * 支付API逻辑处理
 * @package user\Logic
 */
load_trait('controller/Jump');
class PayApiLogic extends Model
{
    use \traits\controller\Jump;

    private $home_lang = 'cn';

    /**
     * 初始化操作
     */
    public function initialize() {
        parent::initialize();
        // 多语言
        $this->home_lang         = get_home_lang();
        // 会员信息表
        $this->users_db          = Db::name('users');
        // 订单主表
        $this->shop_order_db     = Db::name('shop_order');
        // 会员金额明细表
        $this->users_money_db    = Db::name('users_money');
        // 支付API配置
        $this->pay_api_config_db = Db::name('pay_api_config');
        // 会员信息
        $this->users = GetUsersLatestData();
        $this->users_id = $this->users['users_id'];
    }

    // 支付API配置信息查询
    public function GetPayApiConfig($post = [])
    {
        if (empty($post['pay_mark'])) $this->error('支付API异常，请刷新重试');

        //先查虎皮椒支付有没有配置
        $hupijiao_pay_config = $this->pay_api_config_db->where(['pay_mark'=>'Hupijiaopay'])->find();
        if (!empty($hupijiao_pay_config)) {
            $hupijiao_pay_config['pay_info'] = unserialize($hupijiao_pay_config['pay_info']);
            $hupijiaoInfo = Db::name('weapp')->where(['code'=>'Hupijiaopay','status'=>1])->find();
            if (empty($hupijiaoInfo) || !isset($hupijiao_pay_config['pay_info']['is_open_pay']) || 1 == $hupijiao_pay_config['pay_info']['is_open_pay']) {
                $Config = $this->GetOtherPayApiConfig($post);
            } else {
                //兼容订单轮询查支付配置
                if ($post['pay_mark'] != 'Hupijiaopay') {
                    if (empty($hupijiao_pay_config['pay_info'][$post['pay_mark'] . '_appid'])) {
                        $Config = $this->GetOtherPayApiConfig($post);
                    } else {
                        $new_pay_info = [];
                        $new_pay_info['is_open_pay'] = 0;
                        $new_pay_info['appid'] = $hupijiao_pay_config['pay_info'][$post['pay_mark'] . '_appid'];
                        $new_pay_info['appsecret'] = $hupijiao_pay_config['pay_info'][$post['pay_mark'] . '_appsecret'];
                        $new_pay_info['pay_type'] = $post['pay_mark'];
                        $hupijiao_pay_config['pay_info'] = $new_pay_info;
                        $Config = $hupijiao_pay_config;
                    }
                } else {
                    $new_pay_info = [];
                    $new_pay_info['is_open_pay'] = 0;
                    $new_pay_info['appid'] = $hupijiao_pay_config['pay_info'][$post['pay_type'] . '_appid'];
                    $new_pay_info['appsecret'] = $hupijiao_pay_config['pay_info'][$post['pay_type'] . '_appsecret'];
                    $new_pay_info['pay_type'] = $post['pay_type'];
                    $hupijiao_pay_config['pay_info'] = $new_pay_info;
                    $Config = $hupijiao_pay_config;
                }
            }
        } else {
            $Config = $this->GetOtherPayApiConfig($post);
        }

        return $Config;
    }

    // 支付API配置信息查询 -- 先查询虎皮椒支付
    public function GetOtherPayApiConfig($post = [])
    {
        if (empty($post['pay_id']) || empty($post['pay_mark'])) $this->error('支付API异常，请刷新重试');
        $where = [
            'pay_id'   => $post['pay_id'],
            'pay_mark' => $post['pay_mark']
        ];
        $Config = $this->pay_api_config_db->where($where)->find();
        if (empty($Config) || empty($Config['pay_info'])) $this->error('【支付接口】-【'.$Config['pay_name'].'】配置信息不全');
        $Config['pay_info'] = unserialize($Config['pay_info']);

        if (1 == $post['pay_id']) {
            if (!isset($Config['pay_info']['is_open_wechat']) || 1 == $Config['pay_info']['is_open_wechat']) {
                $this->error($Config['pay_name'] . '未开启');
            }
        } else if (2 == $post['pay_id']) {
            if (!isset($Config['pay_info']['is_open_alipay']) || 1 == $Config['pay_info']['is_open_alipay']) {
                $this->error($Config['pay_name'] . '未开启');
            }
        } else {
            if (!isset($Config['pay_info']['is_open_pay']) || 1 == $Config['pay_info']['is_open_pay']) {
                $this->error($Config['pay_name'] . '未开启');
            }
        }

        return $Config;
    }

    // 订单查询
    public function GetFindOrderData($post = [], $is_up_order = false)
    {
        if (empty($post['unified_id']) || empty($post['unified_number']) || empty($post['transaction_type'])) $this->error('订单异常，请刷新重试');

        if (1 == $post['transaction_type']) {
            // 获取充值订单
            $where = [
                'users_id'     => $this->users_id,
                'lang'         => $this->home_lang,
                'moneyid'      => $post['unified_id'],
                'order_number' => $post['unified_number']
            ];
            $OrderData = $this->users_money_db->where($where)->find();
            if (empty($OrderData)) $this->error('订单不存在或已变更');

            // 判断订单状态，1未付款，2已付款，3已完成，4订单取消
            $url = urldecode(url('user/Pay/pay_consumer_details'));
            if (in_array($OrderData['status'], [2, 3])) {
                $this->success('订单已支付，即将跳转', $url, true);
            } else if ($OrderData['status'] == 4) {
                $this->success('订单已取消，即将跳转', $url, true);
            }
            $OrderData['unified_amount'] = $OrderData['money'];
            $OrderData['unified_number'] = $post['unified_number'];

            // 更新订单支付方式
            if (!empty($is_up_order)) {
                $update = [
                    'pay_method' => $post['pay_mark'],
                    'wechat_pay_type' => '',
                    'update_time' => getTime()
                ];
                if ('wechat' == $post['pay_mark']) {
                    if (!isMobile()) {
                        // PC端
                        $wechat_pay_type = 'WeChatScanCode';
                    } else if (isMobile() && !isWeixin()) {
                        // 手机端浏览器
                        $wechat_pay_type = 'WeChatH5';
                    } else if (isMobile() && isWeixin()) {
                        // 手机端微信
                        $wechat_pay_type = 'WeChatInternal';
                    }
                    if (!empty($OrderData['wechat_pay_type'])) {
                        $ReturnData = $this->determine_pay_type($OrderData['wechat_pay_type'], $wechat_pay_type);
                        if (!empty($ReturnData)) $this->error($ReturnData);
                    }
                    $update['wechat_pay_type'] = $wechat_pay_type;
                }
                $this->users_money_db->where($where)->update($update);
            }

        } else if (2 == $post['transaction_type']) {
            // 获取商品订单
            $where = [
                'users_id'   => $this->users_id,
                'lang'       => $this->home_lang,
                'order_id'   => $post['unified_id'],
                'order_code' => $post['unified_number']
            ];
            $OrderData = $this->shop_order_db->where($where)->find();
            if (empty($OrderData)) $this->error('订单不存在或已变更');
            
            // 判断订单状态，1已付款(待发货)，2已发货(待收货)，3已完成(确认收货)，-1订单取消(已关闭)，4订单过期
            $url = urldecode(url('user/Shop/shop_order_details', ['order_id' => $OrderData['order_id']]));
            if (in_array($OrderData['order_status'], [1, 2, 3])) {
                $this->success('订单已支付，即将跳转', $url, true);
            } else if ($OrderData['order_status'] == 4) {
                $this->success('订单已过期，即将跳转', $url, true);
            } else if ($OrderData['order_status'] == -1) {
                $this->success('订单已关闭，即将跳转', $url, true);
            }
            $OrderData['unified_amount'] = $OrderData['order_amount'];
            $OrderData['unified_number'] = $post['unified_number'];

            // 更新订单支付方式
            if (!empty($is_up_order)) {
                $update = [
                    'pay_name' => $post['pay_mark'],
                    'update_time' => getTime()
                ];
                if ('wechat' == $post['pay_mark']) {
                    if (!isMobile()) {
                        // PC端
                        $wechat_pay_type = 'WeChatScanCode';
                    } else if (isMobile() && !isWeixin()) {
                        // 手机端浏览器
                        $wechat_pay_type = 'WeChatH5';
                    } else if (isMobile() && isWeixin()) {
                        // 手机端微信
                        $wechat_pay_type = 'WeChatInternal';
                    }
                    if (!empty($OrderData['wechat_pay_type'])) {
                        $ReturnData = $this->determine_pay_type($OrderData['wechat_pay_type'], $wechat_pay_type);
                        if (!empty($ReturnData)) $this->error($ReturnData);
                    }
                    $update['wechat_pay_type'] = $wechat_pay_type;
                }
                $this->shop_order_db->where($where)->update($update);
            }
        } else if (3 == $post['transaction_type']) {
            // 获取会员升级订单
            $where = [
                'users_id'     => $this->users_id,
                'lang'         => $this->home_lang,
                'moneyid'      => $post['unified_id'],
                'order_number' => $post['unified_number']
            ];
            $OrderData = $this->users_money_db->where($where)->find();
            if (empty($OrderData)) $this->error('订单不存在或已变更');

            // 判断订单状态，1未付款，2已付款，3已完成，4订单取消
            $url = urldecode(url('user/Level/level_centre'));
            if (in_array($OrderData['status'], [2, 3])) {
                $this->success('订单已支付，即将跳转', $url, true);
            } else if ($OrderData['status'] == 4) {
                $this->success('订单已取消，即将跳转', $url, true);
            }
            $OrderData['unified_amount'] = $OrderData['money'];
            $OrderData['unified_number'] = $post['unified_number'];
        } else if (8 == $post['transaction_type']) {
            // 获取商品订单
            $where = [
                'users_id'   => $this->users_id,
                'lang'       => $this->home_lang,
                'order_id'   => $post['unified_id'],
                'order_code' => $post['unified_number']
            ];
            $OrderData = Db::name('media_order')->where($where)->find();
            if (empty($OrderData)) $this->error('订单不存在或已变更', url('user/Media/index'));
            
            $url = url('user/Media/index');
            if (in_array($OrderData['order_status'], [1])) $this->error('订单已支付，即将跳转！', $url, true);
            $OrderData['unified_amount'] = $OrderData['order_amount'];
            $OrderData['unified_number'] = $post['unified_number'];

            // 更新订单支付方式
            if (!empty($is_up_order)) {
                $update = [
                    'pay_name' => $post['pay_mark'],
                    'update_time' => getTime()
                ];
                if ('wechat' == $post['pay_mark']) {
                    if (!isMobile()) {
                        // PC端
                        $wechat_pay_type = 'WeChatScanCode';
                    } else if (isMobile() && !isWeixin()) {
                        // 手机端浏览器
                        $wechat_pay_type = 'WeChatH5';
                    } else if (isMobile() && isWeixin()) {
                        // 手机端微信
                        $wechat_pay_type = 'WeChatInternal';
                    }
                    if (!empty($OrderData['wechat_pay_type'])) {
                        $ReturnData = $this->determine_pay_type($OrderData['wechat_pay_type'], $wechat_pay_type);
                        if (!empty($ReturnData)) $this->error($ReturnData);
                    }
                    $update['wechat_pay_type'] = $wechat_pay_type;
                }
                Db::name('media_order')->where($where)->update($update);
            }
        } else if (9 == $post['transaction_type']) {
            //文章购买
            $where = [
                'users_id'   => $this->users_id,
                'lang'       => $this->home_lang,
                'order_id'   => $post['unified_id'],
                'order_code' => $post['unified_number']
            ];
            $OrderData = Db::name('article_order')->where($where)->find();
            if (empty($OrderData)) $this->error('订单不存在或已变更', url('user/Article/index'));

            $url = url('user/Article/index');
            if (in_array($OrderData['order_status'], [1])) $this->error('订单已支付，即将跳转！', $url, true);
            $OrderData['unified_amount'] = $OrderData['order_amount'];
            $OrderData['unified_number'] = $post['unified_number'];

            // 更新订单支付方式
            if (!empty($is_up_order)) {
                $update = [
                    'pay_name' => $post['pay_mark'],
                    'update_time' => getTime()
                ];
                if ('wechat' == $post['pay_mark']) {
                    if (!isMobile()) {
                        // PC端
                        $wechat_pay_type = 'WeChatScanCode';
                    } else if (isMobile() && !isWeixin()) {
                        // 手机端浏览器
                        $wechat_pay_type = 'WeChatH5';
                    } else if (isMobile() && isWeixin()) {
                        // 手机端微信
                        $wechat_pay_type = 'WeChatInternal';
                    }
                    if (!empty($OrderData['wechat_pay_type'])) {
                        $ReturnData = $this->determine_pay_type($OrderData['wechat_pay_type'], $wechat_pay_type);
                        if (!empty($ReturnData)) $this->error($ReturnData);
                    }
                    $update['wechat_pay_type'] = $wechat_pay_type;
                }
                Db::name('article_order')->where($where)->update($update);
            }
        }

        return $OrderData;
    }

    // 使用微信支付，判断终端调起支付功能
    public function UseWeChatPay($Post = [], $Order = [], $PayInfo = [])
    {
        if (isset($PayInfo['is_open_wechat']) && 0 == $PayInfo['is_open_wechat']) {
            $total_fee    = $Order['unified_amount'];
            $out_trade_no = $Order['unified_number'];

            if (!isMobile()) {
                $Param = [
                    'unified_number' => $Post['unified_number'],
                    'transaction_type' => $Post['transaction_type']
                ];
                $ResultData['url_qrcode'] = url('user/PayApi/pay_wechat_png', $Param);
                $this->success('订单支付中', null, $ResultData);

            } else if (isMobile() && !isWeixin()) {
                // 移动端非微信H5页面支付
                $weixin_url   = model('PayApi')->getMobilePay($out_trade_no, $total_fee, $PayInfo, $Post['transaction_type']);
                if (!empty($weixin_url['return_code']) && 'FAIL' == $weixin_url['return_code']) {
                    $this->error('商户微信支付H5支付尚未配置完成，请在配置开通成功后支付');
                }
                $this->success('订单支付中', $weixin_url);
            
            } else if (isMobile() && isWeixin()) {
                if (!empty($Post['openid'])) {
                    // 小程序支付
                    $Paydata = model('PayApi')->getWechatPay($Post['openid'], $out_trade_no, $total_fee, $PayInfo, 1, $Post['transaction_type']);
                    if (!empty($Paydata)) echo json_encode($Paydata);

                } else if (!empty($this->users_id)) {
                    // 是否使用余额支付
                    $where = [
                        'users_id' => $this->users_id,
                        'lang'     => $this->home_lang
                    ];
                    $open_id = Db::name('users')->where($where)->getField('open_id');
                    if (empty($open_id)) $this->error('手机端微信使用本站账号登录仅可余额支付！');
                    
                    // 手机端微信支付
                    $Paydata = model('PayApi')->getWechatPay($open_id, $out_trade_no, $total_fee, $PayInfo, 0, $Post['transaction_type']);
                    if (!empty($Paydata)) $this->success('订单支付中', null, $Paydata);

                } else {
                    $this->error('使用本站账号登录仅可余额支付！');
                }
            }
        } else {
            $this->error('微信支付已关闭');
        }
    }

    // 使用支付宝支付，读取数据调起支付功能
    public function UseAliPayPay($Post = [], $Order = [], $PayInfo = [], $isReturn = false)
    {
        $alipay_url = null;
        if (!empty($Order) && !empty($PayInfo)) {
            $Order['transaction_type'] = $Post['transaction_type'];
            if (version_compare(PHP_VERSION,'5.5.0','<')) {
                // 低于5.5版本，仅可使用旧版支付宝支付
                $PayApi_model = new \app\user\model\PayApi;
                $alipay_url = $PayApi_model->getOldAliPayPayUrl($Order, $PayInfo);
            } else {
                // 高于或等于5.5版本，可使用新版支付宝支付
                if (empty($PayInfo['version'])) {
                    // 新版
                    $AliPayResult = [
                        'unified_number'   => $Order['unified_number'],
                        'unified_amount'   => $Order['unified_amount'],
                        'transaction_type' => $Order['transaction_type']
                    ];
                    $alipay_url = url('user/Pay/newAlipayPayUrl', $AliPayResult);
                } else if ($PayInfo['version'] == 1){
                    // 旧版
                    $PayApi_model = new \app\user\model\PayApi;
                    $alipay_url = $PayApi_model->getOldAliPayPayUrl($Order, $PayInfo);
                }
            }
        }

        if (true === $isReturn) {
            return ['code'=>1, 'msg'=>'订单支付中', 'alipay_url'=>$alipay_url];
        } else {
            $this->success('订单支付中', $alipay_url);
        }
    }

    // 微信支付订单处理
    public function WeChatPayProcessing($Post = [], $Order = [], $PayInfo = [], $Config = [])
    {
        vendor('wechatpay.lib.WxPayApi');
        vendor('wechatpay.lib.WxPayConfig');

        // 实例化加载订单号
        $WxPayOrderQuery  = new \WxPayOrderQuery;
        $WxPayOrderQuery->SetOut_trade_no($Order['unified_number']);

        // 处理微信配置数据
        $ApiConfig['app_id'] = $PayInfo['appid'];
        $ApiConfig['mch_id'] = $PayInfo['mchid'];
        $ApiConfig['key']    = $PayInfo['key'];

        // 实例化微信配置
        $WxPayConfig = new \WxPayConfig($ApiConfig);
        $WxPayApi = new \WxPayApi;

        if (empty($WxPayConfig->app_id)) $this->error('微信支付配置信息不全');

        // 返回结果
        $WeChatOrder = $WxPayApi->orderQuery($WxPayConfig, $WxPayOrderQuery);

        if (isset($WeChatOrder['return_code']) && $WeChatOrder['return_code'] == 'SUCCESS' && $WeChatOrder['result_code'] == 'SUCCESS') {
            if ($WeChatOrder['trade_state'] == 'SUCCESS' && !empty($WeChatOrder['transaction_id'])) {
                $this->OrderProcessing($Post, $Order, $WeChatOrder, $Config);
            } else if ($WeChatOrder['trade_state'] == 'NOTPAY') {
                $this->success('正在支付中...');
            }
        }
    }

    // 支付宝支付订单处理
    public function AliPayPayProcessing($Post = [], $Order = [], $PayInfo = [], $Config = [])
    {
        if (!empty($PayInfo) && 0 == $PayInfo['version']) {
            vendor('alipay.pagepay.service.AlipayTradeService');
            vendor('alipay.pagepay.buildermodel.AlipayTradeQueryContentBuilder');

            // 实例化加载订单号
            $RequestBuilder = new \AlipayTradeQueryContentBuilder;
            $OutTradeNo     = trim($Order['unified_number']);
            $RequestBuilder->setOutTradeNo($OutTradeNo);

            // 拼装配置
            $ApiConfig['app_id']     = $PayInfo['app_id'];
            $ApiConfig['merchant_private_key'] = $PayInfo['merchant_private_key'];
            $ApiConfig['charset']    = 'UTF-8';
            $ApiConfig['sign_type']  = 'RSA2';
            $ApiConfig['gatewayUrl'] = 'https://openapi.alipay.com/gateway.do';
            $ApiConfig['alipay_public_key'] = $PayInfo['alipay_public_key'];

            // 实例化支付宝配置
            $AlipayTradeService = new \AlipayTradeService($ApiConfig);
            $AliPayOrder = $AlipayTradeService->Query($RequestBuilder);
            
            // 解析数据
            $AliPayOrder = json_decode(json_encode($AliPayOrder), true);

            if ('40004' == $AliPayOrder['code'] && 'Business Failed' === $AliPayOrder['msg']) {
                $this->success('正在建立订单信息');
            } else if ('10000' == $AliPayOrder['code'] && 'WAIT_BUYER_PAY' === $AliPayOrder['trade_status']) {
                $this->success('订单已建立，尚未支付');
            } else if ('10000' == $AliPayOrder['code'] && 'TRADE_SUCCESS' === $AliPayOrder['trade_status']) {
                // 已经支付，处理订单
                $this->OrderProcessing($Post, $Order, $AliPayOrder, $Config);
            }
        } else {
            $this->success('订单支付中');
        }
    }

    // 订单统一处理
    public function OrderProcessing($Post = [], $Order = [], $PayDetails = [], $Config = [])
    {
        if (1 == $Post['transaction_type']) {
            // 付款成功后，订单并未修改状态时，修改订单状态并返回
            $Result['email'] = false;
            $Result['mobile'] = false;
            $url = url('user/Pay/pay_consumer_details');

            if ($Order['status'] == 1) {
                // 订单更新条件
                $where = [
                    'moneyid' => $Order['moneyid'],
                    'users_id' => $this->users_id
                ];

                // 订单更新数据，更新为已付款
                $data = [
                    'status'        => 2,
                    'pay_details'   => serialize($PayDetails),
                    'update_time'   => getTime()
                ];

                // 订单更新
                $ResultID = $this->users_money_db->where($where)->update($data);

                // 订单更新后续操作
                if (!empty($ResultID)) {
                    // 同步修改会员的金额
                    $UsersWhere = [
                        'users_id' => $this->users_id,
                        'lang'     => $this->home_lang
                    ];
                    $UpUsersData = [
                        'users_money' => Db::raw('users_money+'.($Order['money']))
                    ];
                    $ReturnID = Db::name('users')->where($UsersWhere)->update($UpUsersData);

                    // 用户充值金额后续操作
                    if (!empty($ReturnID)) {
                        // 业务处理完成，订单已完成
                        $data2 = [
                            'status'      => 3,
                            'update_time' => getTime()
                        ];
                        $this->users_money_db->where($where)->update($data2);
                        // 返回
                        $this->success('充值成功，即将跳转', $url, $Result);
                    } else {
                        $this->success('付款成功，但未充值成功，请联系管理员。', $url, $Result);
                    }
                } else {
                    $this->success('付款成功，数据错误，未能充值成功，请联系管理员。', $url, $Result);
                }
            } else if (in_array($Order['status'], [2, 3])) {
                // 返回
                $this->success('充值成功，即将跳转', $url, $Result);
            } else if ($Order['status'] == 4) {
                // 返回
                $this->success('订单已取消，即将跳转', $url, $Result);
            }
        } else if (2 == $Post['transaction_type']) {
            // 付款成功后，订单并未修改状态时，修改订单状态并返回
            if (empty($Order['order_status'])) {
                $returnData = pay_success_logic($this->users_id, $Order['order_code'], $PayDetails, 'wechat');
                if (is_array($returnData)) {
                    if (1 == $returnData['code']) {
                        $this->success($returnData['msg'], $returnData['url'], $returnData['data']);
                    } else {
                        $this->error($returnData['msg']);
                    }
                }
            }
        } else if (3 == $Post['transaction_type']) {
            // 付款成功后，订单并未修改状态时，修改订单状态并返回
            $Result['email']  = false;
            $Result['mobile'] = false;
            $url = url('user/Level/level_centre');

            if ($Order['status'] == 1) {
                // 订单更新条件
                $where = [
                    'moneyid' => $Order['moneyid'],
                    'users_id' => $this->users_id
                ];

                // 订单更新数据，更新为已付款
                $UpMoneyData = $this->GetUpMoneyData(session('UsersTypeData'), $Order['pay_method']);

                // 订单更新
                $ResultID = $this->users_money_db->where($where)->update($UpMoneyData);

                // 订单更新后续操作
                if (!empty($ResultID)) {
                    $Where = [
                        'users_id' => $Order['users_id'],
                    ];

                    // 获取更新会员数据数组
                    $UpUsersData = $this->GetUpUsersData(session('UsersTypeData'));
                    $ReturnID = $this->users_db->where($Where)->update($UpUsersData);
                    
                    // 用户充值金额后续操作
                    if (!empty($ReturnID)) {
                        // 返回
                        $this->success('升级成功', $url, $Result);
                    } else {
                        $this->success('付款成功，数据错误，升级失败，请联系管理员', $url, $Result);
                    }
                } else {
                    $this->success('付款成功，数据错误，升级失败，请联系管理员', $url, $Result);
                }
            }
        } else if (8 == $Post['transaction_type']) {
            // 付款成功后，订单并未修改状态时，修改订单状态并返回
            if (empty($Order['order_status'])) {
                // 订单更新条件
                $OrderWhere = [
                    'order_id'  => $Order['order_id'],
                    'users_id'  => $this->users_id,
                    'lang'      => $this->home_lang
                ];

                // 订单更新数据，更新为已付款
                $OrderData = [
                    'order_status' => 1,
                    'pay_details'  => serialize($PayDetails),
                    'pay_time'     => getTime(),
                    'update_time'  => getTime()
                ];

                // 订单更新
                $ResultID = Db::name('media_order')->where($OrderWhere)->update($OrderData);

                // 订单更新后续操作
                if (!empty($ResultID)) {
                    // 订单操作完成，返回跳转
                    $ViewUrl = cookie($this->users_id . '_' . $Order['product_id'] . '_EyouMediaViewUrl');
                    $this->success('支付成功，处理订单完成', $ViewUrl, true);
                }
            }
        }else if (9 == $Post['transaction_type']) {
            // 付款成功后，订单并未修改状态时，修改订单状态并返回
            if (empty($Order['order_status'])) {
                // 订单更新条件
                $OrderWhere = [
                    'order_id'  => $Order['order_id'],
                    'users_id'  => $this->users_id,
                    'lang'      => $this->home_lang
                ];

                // 订单更新数据，更新为已付款
                $OrderData = [
                    'order_status' => 1,
                    'pay_details'  => serialize($PayDetails),
                    'pay_time'     => getTime(),
                    'update_time'  => getTime()
                ];

                // 订单更新
                $ResultID = Db::name('article_order')->where($OrderWhere)->update($OrderData);

                // 订单更新后续操作
                if (!empty($ResultID)) {
                    // 订单操作完成，返回跳转
                    $ViewUrl = cookie($this->users_id . '_' . $Order['product_id'] . '_EyouArticleViewUrl');
                    $this->success('支付成功，处理订单完成', $ViewUrl, true);
                }
            }
        }
    }

    // 确定支付类型
    // $OrderPayMethodType 数据中的数据
    // $PayMethodType 当前提交的类型，用于判断
    private function determine_pay_type($OrderPayMethodType = null, $PayMethodType = null)
    {
        // 若为空，则表现未标记过支付类型
        if (empty($OrderPayMethodType)) return false;

        // 是否数据库中的支付类型和传入的一致
        if ($OrderPayMethodType != $PayMethodType) {
            // 判断返回提示信息
            switch ($OrderPayMethodType) {
                case 'WeChatScanCode':
                    // PC端微信扫码支付
                    return '已在PC端浏览器中微信扫码生成订单，请到PC端浏览器完成支付！';
                    break;
                case 'WeChatInternal':
                    // 手机微信端H5支付
                    return '已在手机端微信中生成订单，请到手机端微信完成支付！';                
                    break;
                case 'WeChatH5':
                    // 手机端浏览器H5支付
                    return '已在手机端浏览器中生成订单，请到手机端浏览器完成支付！';                
                    break;
                default:
                    return '错误提示：102，选择支付方式错误，请刷新后重试~~';
                    break;
            }
        } else {
            return false;
        }
    }

    /*--------------------以下为会员升级代码---------------------*/

    // 判断是否可以升级
    public function IsAllowUpgrade($post = [])
    {
        // 查询会员升级选择的数据
        $UsersTypeData = Db::name('users_type_manage')->where('type_id', $post['type_id'])->find();

        // 查询提交过来级别等级值
        $LevelValue = Db::name('users_level')->where('level_id', $UsersTypeData['level_id'])->getField('level_value');

        // 查询当前会员等级值
        $UsersValue = Db::name('users_level')->where('level_id', $this->users['level'])->getField('level_value');

        // 提交的等级是否比现有等级高
        if ($UsersValue > $LevelValue) $this->error('选择升级的等级不可以比目前持有的等级低');

        // 将查询数据存入 session，微信和支付宝回调时需要查询数据
        if (!empty($UsersTypeData)) session('UsersTypeData', $UsersTypeData);
    }

    // 余额支付
    public function BalancePayment($order_number = null, $UsersTypeData = [])
    {
        // 没有传入则从 session 中读取
        $UsersTypeData = !empty($UsersTypeData) ? $UsersTypeData : session('UsersTypeData');

        if (!empty($UsersTypeData)) {
            $UsersMoney = $this->users_db->where('users_id', $this->users_id)->getField('users_money');
            if ($UsersMoney < $UsersTypeData['price']) {
                // 若会员余额不足支付则返回
                $ReturnData = $this->GetReturnData();
                $this->success($ReturnData);
            } else {
                if (!empty($order_number)) {
                    // 获取更新金额明细表数据数组
                    $UpMoneyData = $this->GetUpMoneyData($UsersTypeData);
                    // 更新数据
                    $ReturnID = $this->users_money_db->where('order_number', $order_number)->update($UpMoneyData);
                } else {
                    // 获取生成的订单信息
                    $AddMoneyData = $this->GetAddMoneyData($UsersTypeData);
                    // 存入会员金额明细表
                    $ReturnID = $this->users_money_db->add($AddMoneyData);
                }

                if (!empty($ReturnID)) {
                    $Where = [
                        'users_id' => $this->users_id,
                        'lang'     => $this->home_lang
                    ];
                    // 获取更新会员数据数组
                    $UpUsersData = $this->GetUpUsersData($UsersTypeData, true);
                    $ReturnID = $this->users_db->where($Where)->update($UpUsersData);
                    if (!empty($ReturnID)) {
                        // 支付完成返回
                        $ReturnData = $this->GetReturnData(0, 1, '余额支付完成！', url('user/Level/level_centre'));
                        $this->success($ReturnData);
                    }
                }
            }
        } else {
            $this->error('升级失败，刷新重试');
        }
    }

    // 微信支付
    public function WeChatPayment($Post = [], $PayInfo = [])
    {
        $MoneyData = $this->GetMoneyData('*', $Post['order_number']);
        $UsersTypeData = session('UsersTypeData');

        if (empty($MoneyData)) {
            // 获取生成的订单信息
            $AddMoneyData = $this->GetAddMoneyData($UsersTypeData, 'wechat', 1);

            // 存入会员金额明细表
            $ReturnID = $this->users_money_db->add($AddMoneyData);
            if (!empty($ReturnID)) {
                // 返回订单数据
                $AddMoneyData['moneyid'] = $ReturnID;
                $this->ReturnMoneyPayData($Post, $AddMoneyData, $PayInfo);
            }
        } else {
            $MoneyDataCause = unserialize($MoneyData['cause']);
            if (isMobile() && !isWeixin()) {
                // 手机浏览器端支付
                $PayType = 'WeChatH5';
            } else if (isMobile() && isWeixin()) {
                // 手机微信端支付
                $PayType = 'WeChatInternal';
            } else {
                // PC端扫码支付
                $PayType = 'WeChatScanCode';
            }

            if ($MoneyDataCause['level_id'] == $UsersTypeData['level_id'] && $MoneyData['money'] == $UsersTypeData['price'] && $MoneyData['wechat_pay_type'] == $PayType) {
                // 提交的订单与上一次是同一类型产品，直接返回数据
                $this->ReturnMoneyPayData($Post, $MoneyData, $PayInfo);
            } else {
                // 生成新订单覆盖原来的订单返回
                $UpMoneyData = $this->GetUpMoneyData($UsersTypeData, 'wechat');
                $UpMoneyData['status'] = 1;
                $UpMoneyData['order_number'] = date('Ymd').getTime().rand(10,100);
                $this->users_money_db->where('moneyid', $MoneyData['moneyid'])->update($UpMoneyData);

                // 返回订单数据
                $UpMoneyData['moneyid'] = $MoneyData['moneyid'];
                $this->ReturnMoneyPayData($Post, $UpMoneyData, $PayInfo);
            }
        }
    }

    // 支付宝支付
    public function AliPayPayment($Post = [], $PayInfo = [])
    {
        $UsersTypeData = session('UsersTypeData');
        if (!empty($UsersTypeData)) {
            $MoneyData = $this->GetMoneyData('*', $Post['order_number']);
            if (empty($MoneyData)) {
                // 获取生成的订单信息
                $AddMoneyData = $this->GetAddMoneyData($UsersTypeData, 'alipay', 1);
                // 存入会员金额明细表
                $ReturnID = $this->users_money_db->add($AddMoneyData);
                // 支付宝处理返回信息
                $AddMoneyData['moneyid'] = $ReturnID;
                if (!empty($ReturnID)) $this->AliPayProcessing($AddMoneyData, $PayInfo, $Post);
            } else {
                // 获取生成的订单信息
                $UpMoneyData = $this->GetUpMoneyData($UsersTypeData, 'alipay');
                $UpMoneyData['status'] = 1;
                // 更新会员金额明细表
                $ReturnID = $this->users_money_db->where('moneyid', $MoneyData['moneyid'])->update($UpMoneyData);
                if (!empty($ReturnID)) {
                    // 支付宝处理返回信息
                    $MoneyData = $this->GetMoneyData('*', $Post['order_number']);
                    $this->AliPayProcessing($MoneyData, $PayInfo, $Post);
                }
            }
        } else {
            $this->error('升级失败，刷新重试');
        }
    }

    // 获取第三方订单
    public function GetPayOrderData($Post = [], $PayInfo = [], $pay_mark = null)
    {
        $UsersTypeData = session('UsersTypeData');
        $MoneyData = $this->GetMoneyData('*', $Post['order_number']);
        if (empty($MoneyData)) {
            // 获取生成的订单信息
            $AddMoneyData = $this->GetAddMoneyData($UsersTypeData, $pay_mark, 1);
            // 存入会员金额明细表
            $ReturnID = $this->users_money_db->add($AddMoneyData);
            // 支付宝处理返回信息
            $AddMoneyData['moneyid'] = $ReturnID;
            return $AddMoneyData;
        } else {
            // 获取生成的订单信息
            $UpMoneyData = $this->GetUpMoneyData($UsersTypeData, $pay_mark);
            $UpMoneyData['status'] = 1;
            // 更新会员金额明细表
            $ReturnID = $this->users_money_db->where('moneyid', $MoneyData['moneyid'])->update($UpMoneyData);
            if (!empty($ReturnID)) {
                // 支付宝处理返回信息
                $MoneyData = $this->GetMoneyData('*', $Post['order_number']);
                return $MoneyData;
            }
        }
    }

    // 支付宝订单处理逻辑
    private function AliPayProcessing($MoneyData = [], $PayInfo = [], $Post = [])
    {
        // 返回订单数据
        $AliPayUrl = '';
        // 支付宝支付所需参数信息拼装
        $Data = [
            'unified_number' => $MoneyData['order_number'],
            'unified_amount' => $MoneyData['money'],
            'transaction_type' => 3,
        ];
        if (version_compare(PHP_VERSION,'5.5.0','<')) {
            // 低于5.5版本，仅可使用旧版支付宝支付
            $AliPayUrl = model('PayApi')->getOldAliPayPayUrl($Data, $PayInfo);
        } else {
            // 高于或等于5.5版本，可使用新版支付宝支付
            if (empty($PayInfo['version'])) {
                // 新版
                $AliPayUrl = url('user/Pay/newAlipayPayUrl', $Data);
            } else if ($PayInfo['version'] == 1) {
                // 旧版
                $AliPayUrl = model('PayApi')->getOldAliPayPayUrl($Data, $PayInfo);
            }
        }
        if (!empty($AliPayUrl)) {
            $ReturnData = $this->GetReturnData(2, 0, '订单生成！', $AliPayUrl, $Data['unified_number']);
            $ReturnData['ReturnOrderID'] = $MoneyData['moneyid'];
            $ReturnData['pay_id']   = $Post['pay_id'];
            $ReturnData['pay_mark'] = $Post['pay_mark'];
        } else {
            $this->error('升级失败，刷新重试');
        }
        $this->success($ReturnData);
    }

    // 处理微信订单支付信息并加载回页面
    private function ReturnMoneyPayData($Post = [], $MoneyData = [], $PayInfo = [])
    {
        if (empty($MoneyData)) $this->error('订单生成错误，请刷新后重试');
        // 订单信息
        $ReturnOrderData = [
            'pay_id'             => $Post['pay_id'],
            'pay_mark'           => $Post['pay_mark'],
            'unified_id'         => $MoneyData['moneyid'],
            'unified_number'     => $MoneyData['order_number'],
            'transaction_type'   => 3, // 订单支付购买
            'order_total_amount' => $MoneyData['money'],
            'PayData'            => [
                'appId' => ''
            ]
        ];
        if (isMobile() && !isWeixin()) {
            // 手机浏览器端支付
            $out_trade_no = $MoneyData['order_number'];
            if (empty($out_trade_no)) $this->error('支付异常，请刷新后重试~');
            
            $total_fee = $MoneyData['money'];
            if (empty($total_fee)) $this->error('支付异常，请刷新后重试~');
            
            $url = model('PayApi')->getMobilePay($out_trade_no, $total_fee, $PayInfo, 3);
            if (isset($url['return_code']) && 'FAIL' == $url['return_code']) {
                $this->error('商户公众号尚未成功开通H5支付，请开通成功后重试~');
            }
            $ReturnDataNew['url_qrcode'] = null;
        } else if (isMobile() && isWeixin()) {
            // 手机微信端支付
            if (empty($this->users['open_id'])) {
                // 如果会员没有openid则使用扫码支付方式
                $Param = [
                    'unified_number' => $MoneyData['order_number'],
                    'transaction_type' => 3
                ];
                $url = url('user/PayApi/pay_wechat_png', $Param);
            } else {
                $url = null;
                $Paydata = model('PayApi')->getWechatPay($this->users['open_id'], $MoneyData['order_number'], $MoneyData['money'], $PayInfo, 0, 3);
                $ReturnOrderData['PayData'] = $Paydata;
            }
            $ReturnDataNew['url_qrcode'] = $url;
        } else {
            $Param = [
                'unified_number' => $MoneyData['order_number'],
                'transaction_type' => 3
            ];
            $url = url('user/PayApi/pay_wechat_png', $Param);
            $ReturnDataNew['url_qrcode'] = $url;
        }

        $ReturnData = $this->GetReturnData(1, 0, '订单生成！', $url, $MoneyData['order_number']);
        $ReturnData = array_merge($ReturnData, $ReturnDataNew);
        $this->success($ReturnData, $url, $ReturnOrderData);
    }

    // 查询
    // field  字段信息，若不传入则默认值为*
    // 值为*：find方式查询，查询所有字段，返回一维数组
    // 值为多个：find方式查询，查询指定字段，返回一维数组
    // 值为单个：getField方式查询，返回单个字段值
    // return 返回查询结果
    public function GetMoneyData($field = '*', $order_number = null)
    {
        $data = [];
        // 查询条件
        $where = [
            'users_id'   => $this->users_id,
            'cause_type' => 0, // 消费类型
            'status'     => 1, // 未付款状态
            'lang'       => $this->home_lang,
        ];

        if (!empty($order_number)) $where['order_number'] = $order_number;

        if ('*' == $field) {
            // 查询所有字段
            $data = $this->users_money_db->where($where)->find();
        } else {
            $info = explode(',', $field);
            if (1 < count($info)) {
                // 查询指定的多个字段
                $data = $this->users_money_db->where($where)->field($field)->find();
            } else {
                // 查询指定的单个字段
                $data = $this->users_money_db->where($where)->getField($field);
            }
        }
        return $data;
    }

    // 拼装更新会员数据数组
    private function GetUpUsersData($data = array(), $balance = false)
    {
        $time = getTime();
        // 会员期限定义数组
        $limit_arr = Config::get('global.admin_member_limit_arr');
        // 到期天数
        $maturity_days = $limit_arr[$data['limit_id']]['maturity_days'];
        // 更新会员属性表的数组
        $result = [
            'level' => $data['level_id'],
            'update_time' => $time,
            'level_maturity_days' => Db::raw('level_maturity_days+'.($maturity_days)),
        ];

        // 如果是余额支付则追加数组
        if (!empty($balance)) $result['users_money'] = Db::raw('users_money-'.($data['price']));
        
        // 判断是否需要追加天数，maturity_code在Base层已计算，1表示终身会员天数
        if (1 != $this->users['maturity_code']) {
            // 判断是否到期，到期则执行，3表示会员在期限内，不需要进行下一步操作
            if (3 != $this->users['maturity_code']) {
                // 追加天数数组
                $result['open_level_time']     = $time;
                $result['level_maturity_days'] = $maturity_days;
            }
        }

        return $result;
    }

    // 拼装返回数组
    private function GetReturnData($ReturnCode = 0, $ReturnPay = 0, $ReturnMsg = null, $ReturnUrl = null, $ReturnOrder = null)
    {
        // 返回跳转的链接
        $ReturnUrl = !empty($ReturnUrl) ? $ReturnUrl : url('user/Pay/pay_account_recharge');

        // 返回提示的信息
        $ReturnMsg = 0 == $ReturnCode && empty($ReturnMsg) ? '余额不足，若要使用余额支付，请先充值！' : $ReturnMsg;

        // 拼装数据
        $ReturnData = [
            // 返回判断支付类型，0为余额支付，1为微信，2为支付宝，2以上为第三方支付
            'ReturnCode' => $ReturnCode,
            // 返回判断是否已支付，0为未支付，1为完成支付
            'ReturnPay'  => $ReturnPay,
            // 返回提示的信息
            'ReturnMsg'  => $ReturnMsg,
            // 返回跳转的链接
            'ReturnUrl'  => $ReturnUrl,
            // 支付订单号
            'ReturnOrder'=> $ReturnOrder,
        ];

        // 微信支付才需要的返回字段
        if (2 == $ReturnCode) {
            if (isMobile() && !isWeixin()) {
                // 手机浏览器端支付
                $ReturnData['WeChatType'] = 'WeChatH5';
            } else if (isMobile() && isWeixin()) {
                // 手机微信端支付
                $ReturnData['WeChatType'] = 'WeChatInternal';
            } else {
                // PC端扫码支付
                $ReturnData['WeChatType'] = 'WeChatScanCode';
            }
        }

        return $ReturnData;
    }

    // 拼装订单数组
    private function GetAddMoneyData($UsersTypeData = array(), $pay_method = 'balance', $status = 2, $details = '')
    {
        $wechat_pay_type = '';
        if ('balance' == $pay_method) {
            $pay_method_new = '余额';
        } else if ('alipay' == $pay_method) {
            $pay_method_new = '支付宝';
        } else if ('wechat' == $pay_method) {
            if (isMobile() && !isWeixin()) {
                // 手机浏览器端支付
                $wechat_pay_type = 'WeChatH5';
            } else if (isMobile() && isWeixin()) {
                // 手机微信端支付
                $wechat_pay_type = 'WeChatInternal';
            } else {
                // PC端扫码支付
                $wechat_pay_type = 'WeChatScanCode';
            }
            $pay_method_new = '微信';
        } else {
            $pay_method_new = $pay_method;
        }

        $details = '会员当前级别为【' . $this->users['level_name'] . '】，使用' . $pay_method_new . '支付【 ' . $UsersTypeData['type_name'] . '】，支付金额为' . $UsersTypeData['price'];

        $time = getTime();
        // 拼装数组存入会员购买等级表
        $AddMoneyData = [
            'users_id'     => $this->users_id,
            // 订单生成规则
            'order_number' => date('Ymd') . $time . rand(10,100),
            // 金额
            'money'        => $UsersTypeData['price'],
            // 购买的产品等级ID(level_id)
            'cause'        => serialize($UsersTypeData),
            // 购买消费标记
            'cause_type'   => 0,
            // 支付状态，默认2为支付完成
            'status'       => $status,
            // 支付方式，默认余额支付
            'pay_method'   => $pay_method,
            'wechat_pay_type' => $wechat_pay_type,
            // 支付详情
            'pay_details'  => serialize($details),
            'lang'         => $this->home_lang,
            'add_time'     => $time,
            'update_time'  => $time
        ];

        return $AddMoneyData;
    }

    // 拼装更新金额明细表数据数组
    private function GetUpMoneyData($data = array(), $pay_method = 'balance')
    {
        $wechat_pay_type = '';
        if ('balance' == $pay_method) {
            $pay_method_new = '余额';
        } else if ('alipay' == $pay_method) {
            $pay_method_new = '支付宝';
        } else if ('wechat' == $pay_method) {
            if (isMobile() && !isWeixin()) {
                // 手机浏览器端支付
                $wechat_pay_type = 'WeChatH5';
            } else if (isMobile() && isWeixin()) {
                // 手机微信端支付
                $wechat_pay_type = 'WeChatInternal';
            } else {
                // PC端扫码支付
                $wechat_pay_type = 'WeChatScanCode';
            } 
            $pay_method_new = '微信';
        } else {
            $pay_method_new = $pay_method;
            $pay_method = 'Hupijiaopay' != $pay_method ? 'wechat' : $pay_method;
        }

        $details = '会员当前级别为【' . $this->users['level_name'] . '】，使用' . $pay_method_new . '支付【 ' . $data['type_name'] . '】，支付金额为' . $data['price'];

        $result = [
            'cause'           => serialize($data),
            'money'           => $data['price'],
            'status'          => 2,
            'pay_method'      => $pay_method,
            'wechat_pay_type' => $wechat_pay_type,
            'pay_details'     => serialize($details),
            'update_time'     => getTime()
        ];

        return $result;
    }
}