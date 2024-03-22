<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海口快推科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */

namespace app\common\model;

use think\Db;
use think\Config;

/**
 * 百度支付模型
 */
load_trait('controller/Jump');
class BaiduPay
{
    use \traits\controller\Jump;

    // 构造函数
    public function __construct($baiduPayConfig = [], $verify = false)
    {
        // 统一接收参数处理
        $this->times = getTime();
        // 是否验证请求
        $this->verify = !empty($verify) ? true : false;
        // 接口请求头
        $this->headers  = ['Content-Type: application/x-www-form-urlencoded'];
        // 百度支付配置信息
        $this->baiduPayConfig = !empty($baiduPayConfig) ? $baiduPayConfig : model('ShopPublicHandle')->getSpecifyAppletsConfig();
        // 获取百度access_token
        if (empty($this->verify) && !empty($this->baiduPayConfig)) $this->getBaiduAccessToken();
    }

    // 获取百度 access_token 
    public function getBaiduAccessToken()
    {
        // 尚未配置信息
        if (empty($this->baiduPayConfig['appkey']) || empty($this->baiduPayConfig['appsecret'])) $this->error('请先完善百度小程序配置');
        // 非验证配置请求时，如果 access_token 存在且不超过3天则无需再次获取
        if (empty($this->verify) && !empty($this->baiduPayConfig['accessToken']) && !empty($this->baiduPayConfig['accessTokenTimes'])) {
            $this->baiduPayConfig['accessTokenTimes'] = $this->baiduPayConfig['accessTokenTimes'] + (86400 * 3);
            if (intval($this->baiduPayConfig['accessTokenTimes']) > intval($this->times)) return $this->baiduPayConfig;
        }
        // 接口参数
        $paramsArr = [
            'grant_type' => 'client_credentials',
            'client_id' => trim(strval($this->baiduPayConfig['appkey'])),
            'client_secret' => trim(strval($this->baiduPayConfig['appsecret'])),
            'scope' => 'smartapp_snsapi_base',
        ];
        // 获取 access_token (GET方式)
        $requestApi = 'https://openapi.baidu.com/oauth/2.0/token?' . http_build_query($paramsArr);
        $result = json_decode(httpRequest($requestApi, 'GET', null, $this->headers, 300000), true);
        if (!empty($result['error']) && !empty($result['error_description'])) {
            if (stristr($result['error_description'], 'client id')) {
                $result['error_description'] = '百度登录App Key不正确，请检查';
            } else if (stristr($result['error_description'], 'client secret') || stristr($result['error_description'], 'authentication')) {
                $result['error_description'] = '百度登录App Secret不正确，请检查';
            }
            $this->error($result['error_description']);
        }
        if (empty($result['access_token'])) $this->error('获取access_token失败，请确认百度小程序是否申请成功');
        // 存入 access_token 值
        $this->baiduPayConfig['accessToken'] = trim(strval($result['access_token']));
        $this->baiduPayConfig['accessTokenTimes'] = intval($this->times);
        // 返回结果
        if (!empty($this->verify)) return $this->baiduPayConfig;
    }

    // 获取调用百度支付API的配置信息
    public function getBaiDuAppletsPay($orderID = '', $orderCode = '', $orderAmount = 0, $table = 'shop_order', $orderType = 2)
    {
        // 接口参数
        $paramsArr = [
            'dealId'      => trim(strval($this->baiduPayConfig['payDealId'])),
            'appKey'      => trim(strval($this->baiduPayConfig['payAppkey'])),
            'totalAmount' => floatval($orderAmount * 100),
            'tpOrderId'   => trim(strval($orderCode)),
        ];
        $rsaSign = $this->getPayRsaSign($paramsArr);
        // 异步回调地址
        $paramsArr['notifyUrl'] = request()->domain() . ROOT_DIR . '/index.php';
        // 订单名称
        $paramsArr['dealTitle'] = '百度小程序支付';
        // 验签类型(0:appKey、dealId、tpOrderId; 1:appKey、dealId、tpOrderId、totalAmount)
        $paramsArr['signFieldsRange'] = 1;
        // MD5加密签名
        $paramsArr['rsaSign'] = trim($rsaSign);
        // 自定义参数
        $appletsID = input('param.applets_id/d', 0);
        $sendTerminal = input('param.sendTerminal/s', '');
        $bizInfo = [
            'tpData' => [
                'returnData' => [
                    'payType' => 'baiduPay',
                    'table' => trim(strval($table)),
                    'usersID' => session('users_id'),
                    'orderID' => intval($orderID),
                    'orderType' => intval($orderType),
                    'orderCode' => trim(strval($orderCode)),
                    'appletsID' => intval($appletsID),
                    'sendTerminal' => trim(strval($sendTerminal)),
                ]
            ]
        ];
        $paramsArr['bizInfo'] = json_encode($bizInfo);
        // 返回数据
        return $paramsArr;
    }

    // 百度支付小程序商品购买支付后续处理
    public function baiDuAppletsPayDealWith($post = [], $notify = false, $table = 'shop_order')
    {
        // 异步回调时执行
        $returnData = !empty($post['returnData']) ? $post['returnData'] : [];
        if (true === $notify && !empty($returnData['payType']) && 'baiduPay' == $returnData['payType']) {
            $table = !empty($returnData['table']) ? trim($returnData['table']) : $table;
            $post['users_id'] = !empty($returnData['usersID']) ? intval($returnData['usersID']) : 0;
            $post['order_id'] = !empty($returnData['orderID']) ? intval($returnData['orderID']) : 0;
            if ('users_recharge_pack_order' === trim($table)) {
                $post['order_pay_code'] = !empty($returnData['orderCode']) ? trim($returnData['orderCode']) : '';
            } else {
                $post['order_code'] = !empty($returnData['orderCode']) ? trim($returnData['orderCode']) : '';
            }
            $post['transaction_type'] = !empty($returnData['orderType']) ? intval($returnData['orderType']) : 0;
        }

        if (!empty($post['users_id'])) {
            // 获取系统订单
            $order = $this->getSystemOrder($post, $notify, $table);
            // 查询百度支付支付订单是否真实完成支付
            $jsonData = $this->queryOrderPayResult($order['unified_number']);
            @file_put_contents(ROOT_PATH . "/a_jsonData_1.php", date("Y-m-d H:i:s") . "  " . json_encode($jsonData) . "\r\n", FILE_APPEND);
            if (isset($jsonData['errno']) && 0 === intval($jsonData['errno']) && isset($jsonData['msg']) && 'success' === trim($jsonData['msg'])) {
                $jsonData = !empty($jsonData['data']) ? $jsonData['data'] : [];
                // 支付成功
                if (2 === intval($jsonData['status']) && !empty($jsonData['tradeNo']) && !empty($jsonData['payType'])) {
                    // 处理系统订单
                    $post['unified_id'] = $order['unified_id'];
                    $post['unified_number'] = $order['unified_number'];
                    $payApiLogic = new \app\user\logic\PayApiLogic($post['users_id'], true);
                    $payApiLogic->OrderProcessing($post, $order, $jsonData);
                }
                // 正在支付中
                else if (1 === intval($jsonData['order_status'])) {
                    if (true !== $notify) $this->success('正在支付中');
                }
                // 订单异常
                else {
                    if (true !== $notify) $this->error($jsonData['msg']);
                }
            } else {
                if (true !== $notify) $this->error($jsonData['msg']);
            }
        }
    }

    // 查询系统订单
    private function getSystemOrder($post = [], $notify = false, $table = 'shop_order')
    {
        // 商城商品订单
        if ('shop_order' == $table) {
            // 查询系统订单信息
            $where = [
                'users_id'   => intval($post['users_id']),
                'order_id'   => intval($post['order_id']),
                'order_code' => strval($post['order_code']),
            ];
            $order = Db::name('shop_order')->where($where)->find();
            if (empty($order)) {
                // 同步
                if (true !== $notify) $this->error('无效订单');
            } else if (0 < $order['order_status']) {
                // 异步
                if (true === $notify) {
                    echo 'SUCCESS'; exit;
                }
                // 同步
                else {
                    $usersData = Db::name('users')->where('users_id', $post['users_id'])->find();
                    // 邮箱发送
                    $resultData['email'] = GetEamilSendData(tpCache('smtp'), $usersData, $order, 1, 'wechat');
                    // 短信发送
                    $resultData['mobile'] = GetMobileSendData(tpCache('sms'), $usersData, $order, 1, 'wechat');
                    // 跳转链接
                    $url = 1 == input('param.fenbao/d') ? '' : '/pages/order/index';
                    $resultData['url'] = $url;
                    $this->success('支付完成', $url, $resultData);
                }
            }
            $order['unified_id'] = intval($order['order_id']);
            $order['unified_number'] = strval($order['order_code']);
        }
        // 会员充值套餐订单
        else if ('users_recharge_pack_order' == $table) {
            $where = [
                'users_id' => intval($post['users_id']),
                'order_id' => intval($post['order_id']),
            ];
            if (!empty($post['order_code']) && !empty($post['order_pay_code'])) {
                $where['order_code'] = strval($post['order_code']);
                $where['order_pay_code'] = strval($post['order_pay_code']);
            } else if (!empty($post['order_code'])) {
                $where['order_pay_code'] = strval($post['order_code']);
            }
            $order = Db::name('users_recharge_pack_order')->where($where)->find();
            if (empty($order)) {
                // 同步
                if (true !== $notify) $this->error('无效订单');
            } else if (1 < $order['order_status']) {
                // 异步
                if (true === $notify) {
                    echo 'SUCCESS'; exit;
                }
                // 同步
                else {
                    $this->success('支付完成');
                }
            }
            $order['unified_id'] = intval($order['order_id']);
            $order['unified_number'] = strval($order['order_pay_code']);
        }
        // 会员(充值余额 or 升级)订单
        else if ('users_money' == $table) {
            $post['moneyid'] = !empty($post['order_id']) ? $post['order_id'] : $post['moneyid'];
            $post['order_number'] = !empty($post['order_code']) ? $post['order_code'] : $post['order_number'];
            $where = [
                'moneyid' => intval($post['moneyid']),
                'users_id' => intval($post['users_id']),
                'order_number' => strval($post['order_number']),
            ];
            $order = Db::name('users_money')->where($where)->find();
            if (empty($order)) {
                // 同步
                if (true !== $notify) $this->error('无效订单');
            } else if (1 < $order['status']) {
                // 异步
                if (true === $notify) {
                    echo 'SUCCESS'; exit;
                }
                // 同步
                else {
                    $this->success('支付完成');
                }
            }
            $order['unified_id'] = intval($order['moneyid']);
            $order['unified_number'] = strval($order['order_number']);
        }

        return $order;
    }

    // 查询百度支付支付订单是否真实完成支付
    public function queryOrderPayResult($orderCode = '')
    {
        // 接口参数
        $paramsArr = [
            'access_token' => $this->baiduPayConfig['accessToken'],
            'tpOrderId' => !empty($this->verify) ? intval($this->times) : trim(strval($orderCode)),
            'pmAppKey' => trim(strval($this->baiduPayConfig['payAppkey'])),
        ];
        // 订单查询Api(GET方式)
        $requestApi = 'https://openapi.baidu.com/rest/2.0/smartapp/pay/paymentservice/findByTpOrderId?' . http_build_query($paramsArr);
        $result = json_decode(httpRequest($requestApi, 'GET', null, $this->headers, 300000), true);
        // 返回结果
        if (!empty($this->verify)) {
            if (isset($result['errno']) && !in_array($result['errno'], [0, 1])) $this->error('百度提示: ' . $result['msg'] . '，请检查百度支付APP KEY是否正确');
            return $this->baiduPayConfig;
        } else {
            return $result;
        }
    }

    // 获取支付Sign
    private function getPayRsaSign($assocArr = [])
    {
        // openssl扩展不存在
        if (!function_exists('openssl_pkey_get_private') || !function_exists('openssl_sign')) $this->error('openssl扩展不存在');

        // 处理私钥
        $rsaPriKeyPem = $this->handlePaySecret(1);
        $priKey = openssl_pkey_get_private($rsaPriKeyPem);

        // 参数按字典顺序排序
        ksort($assocArr); 

        // 加密参数
        $parts = array();
        foreach ($assocArr as $k => $v) {
            $parts[] = $k . '=' . $v;
        }
        $str = implode('&', $parts);

        // 生成加密串
        $sign = '';
        openssl_sign($str, $sign, $priKey);
        openssl_free_key($priKey);
        return base64_encode($sign);
    }

    // 处理支付私钥
    private function handlePaySecret($keyType = 0)
    {
        $pemWidth = 64;
        $rsaKeyPem = '';

        $begin = '-----BEGIN ';
        $end = '-----END ';
        $key = ' KEY-----';
        $type = $keyType ? 'PRIVATE' : 'PUBLIC';

        $keyPrefix = $begin . $type . $key;
        $keySuffix = $end . $type . $key;

        $rsaKeyPem .= $keyPrefix . "\n";
        $rsaKeyPem .= wordwrap($this->baiduPayConfig['paySecret'], $pemWidth, "\n", true) . "\n";
        $rsaKeyPem .= $keySuffix;

        // openssl扩展不存在
        if (!function_exists('openssl_pkey_get_public') || !function_exists('openssl_pkey_get_private')) $this->error('openssl扩展不存在');
        // 公钥加密错误，请检查百度支付配置
        if (0 === intval($keyType) && false == openssl_pkey_get_public($rsaKeyPem)) $this->error('公钥加密错误，请检查百度支付配置');
        // 私钥加密错误，请检查百度支付配置
        if (1 === intval($keyType) && false == openssl_pkey_get_private($rsaKeyPem)) $this->error('RSA加密私钥错误，请检查百度支付配置');

        return $rsaKeyPem;
    }

}