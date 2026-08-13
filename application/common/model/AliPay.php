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



namespace app\common\model;

use think\Db;
use think\Config;

/**
 * 支付宝支付模型
 */
 load_trait('controller/Jump');
class AliPay

{  
    use \traits\controller\Jump;
    private $alipayConfig = [];
    private $times = 0;
    private $headers = ['Content-Type: application/x-www-form-urlencoded'];

    // 构造函数
    public function __construct($alipayConfig = [])
    {
        $this->times = getTime();  // 获取当前时间戳
        // 支付宝配置
        $this->alipayConfig = !empty($alipayConfig) ? $alipayConfig : model('ShopPublicHandle')->getSpecifyAppletsConfig();
    }

    /**
     * 获取支付宝支付请求所需的配置信息
     * @param string $orderID 订单ID
     * @param string $orderCode 订单编号
     * @param float $orderAmount 订单金额
     * @param string $table 订单表
     * @param int $orderType 订单类型
     * @return array 支付宝请求参数
     */
       public function getAlipayAppletsPay($orderID, $orderCode, $orderAmount,$orderType, $openid)
    {
        // 调用 alipay.trade.create 创建订单
        $paramsArr = [
            'app_id'        => trim(strval($this->alipayConfig['appid'])),
            'method'        => 'alipay.trade.create',  // 使用统一收单交易创建接口
            'charset'       => 'UTF-8',
            'sign_type'     => 'RSA2',
            'timestamp'     => date('Y-m-d H:i:s'),
            'version'       => '1',
            'notify_url'    => request()->domain() . ROOT_DIR . '/index.php', // 支付宝异步回调地址
            'biz_content'   => json_encode([
                'out_trade_no'  => $orderCode,  // 订单号
                'total_amount'  => $orderAmount, // 支付金额
                'subject'       => '订单支付', // 订单名称
                'product_code'  => 'JSAPI_PAY', // 支付宝支付产品码
                'op_app_id' => $this->alipayConfig['appid'],
                'buyer_open_id' => $openid
                // 可以根据需要加入更多的参数，比如买家ID等
            ])
        ];
       
        $rsaSign = $this->getPayRsaSign($paramsArr, $this->alipayConfig['appPrivateKey']);
    
        // 将签名结果添加到请求参数
        $paramsArr['sign'] = trim($rsaSign);
    
        // 调用支付宝接口，创建支付订单
        $url = 'https://openapi.alipay.com/gateway.do';
        $response = $this->makeHttpRequest($url, $paramsArr);
       
        // 检查返回结果
        if (isset($response['alipay_trade_create_response']['code']) && $response['alipay_trade_create_response']['code'] == '10000') {
            // 返回支付相关信息
            return [
                'success'    => true,
                'trade_no'   => $response['alipay_trade_create_response']['trade_no'], // 获取返回的trade_no
                'order_id'   => $orderCode,
                'order_token'=> $response['alipay_trade_create_response']['order_token'],
            ];
        } else {
            return [
                'success'    => false,
                'err_tips'   => $response['alipay_trade_create_response']['sub_msg'], // 返回错误信息
            ];
        }
    }
     // 支付宝小程序商品购买支付后续处理
    public function aliPayAppletsPayDealWith($post = [], $notify = false, $table = 'shop_order')
    {
        // 异步回调时执行
        $returnData = !empty($post['returnData']) ? $post['returnData'] : [];
        if (true === $notify && !empty($returnData['payType']) && 'alipay' == $returnData['payType']) {
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
            // 查询支付宝支付订单是否真实完成支付
            $jsonData = $this->queryAliPayOrder($post['openid'], $order['unified_number'], $order['order_amount']);
            
            // 检查支付宝接口返回的错误码和状态
            if (isset($jsonData['alipay_trade_query_response']['code']) && '10000' === $jsonData['alipay_trade_query_response']['code']
                && isset($jsonData['alipay_trade_query_response']['msg']) && 'Success' === trim($jsonData['alipay_trade_query_response']['msg'])) {
                
                // 获取支付宝返回的数据
                $jsonData = $jsonData['alipay_trade_query_response'];
            
                // 支付成功判断
                if (isset($jsonData['trade_status']) && 'TRADE_SUCCESS' === $jsonData['trade_status'] && !empty($jsonData['trade_no']) && !empty($jsonData['buyer_logon_id'])) {
                    // 处理系统订单
                    $post['unified_id'] = $order['unified_id'];
                    $post['unified_number'] = $order['unified_number'];
                    $payApiLogic = new \app\user\logic\PayApiLogic($post['users_id'], true);
                    $payApiLogic->OrderProcessing($post, $order, $jsonData);
                }
                // 正在支付中判断
                else if (isset($jsonData['trade_status']) && 'WAIT_BUYER_PAY' === $jsonData['trade_status']) {
                    if (true !== $notify) {
                        $this->success('正在支付中');
                    }
                }
                // 订单异常，处理其他情况
                else {
                    if (true !== $notify) {
                        $this->error($jsonData['msg']);
                    }
                }
            } else {
                if (true !== $notify) {
                    $this->error($jsonData['alipay_trade_query_response']['msg']);
                }
            }
        }
    }
    
    // 查询支付宝支付订单是否真实完成支付
    public function queryAliPayOrder($openid,$orderCode = '',$orderAmount='')
    {
        // 接口参数
        $paramsArr = [
            'app_id' => trim(strval($this->alipayConfig['appid'])),
            'out_trade_no' => trim(strval($orderCode)),
            'charset' => 'UTF-8',
            'sign_type' => 'RSA2',
            'method' => 'alipay.trade.query',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'biz_content' => json_encode([
                'out_trade_no' => $orderCode,
                'buyer_open_id' => $openid,
               'total_amount' => $orderAmount,
            ]),
        ];
        $rsaSign = $this->getPayRsaSign($paramsArr, $this->alipayConfig['appPrivateKey']);
    
        // 将签名结果添加到请求参数
        $paramsArr['sign'] = trim($rsaSign);
        // 订单查询Api
        $requestApi = 'https://openapi.alipay.com/gateway.do?' . http_build_query($paramsArr);
        
        $result = json_decode(httpRequest($requestApi, 'GET', null, $this->headers, 300000), true);
        // 返回结果
        return $result;
    }
    
    // 获取系统订单
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


    /**
     * 发送 HTTP 请求
     *
     * @param string $url 请求的 URL
     * @param array $data 请求的参数
     * @param array $headers 请求的头部
     * @return array 返回的结果
     */
    public function makeHttpRequest($url, $data, $headers = [])
    {
        // 初始化 cURL
        $ch = curl_init();
    
        // 设置 cURL 参数
        curl_setopt($ch, CURLOPT_URL, $url);                // 设置请求 URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);      // 将响应内容返回，而不是直接输出
        curl_setopt($ch, CURLOPT_POST, true);                // 设置请求方法为 POST
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // 设置 POST 请求的参数
    
        // 设置请求头部
        $defaultHeaders = [
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8', // 设置内容类型
        ];
        $headers = array_merge($defaultHeaders, $headers); // 合并默认头部和自定义头部
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
        // 禁用 SSL 证书验证（如果需要）
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
        // 执行 cURL 请求
        $response = curl_exec($ch);
    
        // 检查是否发生错误
        if (curl_errno($ch)) {
            // 如果发生错误，记录错误并返回空数组
            error_log('cURL Error: ' . curl_error($ch));
            return [];
        }
    
        // 关闭 cURL 连接
        curl_close($ch);
    
        // 将返回的 JSON 响应解码为数组
        return json_decode($response, true);
    }


    /**
     * 获取支付宝支付订单查询结果
     * @param string $orderCode 订单号
     * @return array 支付订单查询结果
     */
    public function queryOrderPayResult($orderCode = '')
    {
        $paramsArr = [
            'app_id'        => $this->alipayConfig['appid'],
            'method'        => 'alipay.trade.query',
            'charset'       => 'UTF-8',
            'sign_type'     => 'RSA2',
            'timestamp'     => date('Y-m-d H:i:s'),
            'version'       => '1.0',
            'biz_content'   => json_encode(['out_trade_no' => $orderCode]),
            'notify_url'    => request()->domain() . ROOT_DIR . '/index.php'
        ];

        $rsaSign = $this->getPayRsaSign($paramsArr,$this->alipayConfig['appPrivateKey']);

        $paramsArr['sign'] = trim($rsaSign);

        // 支付宝查询API
        $requestApi = 'https://openapi.alipay.com/gateway.do?' . http_build_query($paramsArr);
        $result = json_decode(httpRequest($requestApi, 'GET', null, $this->headers, 300000), true);
        return $result;
    }

    /**
     * 获取支付宝支付签名
     * @param array $assocArr 请求参数数组
     * @return string 支付宝签名
     */
   private function getPayRsaSign($data, $privateKey)
{
    // 按照键名升序排序
    ksort($data);

    $stringToSign = '';
    foreach ($data as $key => $value) {
        // 排除空值和 sign 参数
        if ($value !== '' && !is_null($value) && $key !== 'sign') {
            $stringToSign .= "$key=$value&";
        }
    }

    // 去掉末尾多余的 &
    $stringToSign = rtrim($stringToSign, '&');
    
    // 确保私钥格式正确，自动拼接开始和结束部分
    $formattedPrivateKey = "-----BEGIN RSA PRIVATE KEY-----\n" . trim($privateKey) . "\n-----END RSA PRIVATE KEY-----";
    $privateKeyResource = openssl_pkey_get_private($formattedPrivateKey);
    
    if (!$privateKeyResource) {
        die('私钥加载失败');
    }

    // 使用 RSA2 签名，采用 SHA256 算法
    if (!openssl_sign($stringToSign, $sign, $privateKeyResource, OPENSSL_ALGO_SHA256)) {
        die('签名生成失败');
    }

    // 释放私钥资源
    openssl_free_key($privateKeyResource);

    // 返回 Base64 编码后的签名
    return base64_encode($sign);
}

    /**
     * 处理支付私钥
     * @param int $keyType 私钥类型 (0:公钥, 1:私钥)
     * @return string 支付密钥
     */
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
        $rsaKeyPem .= wordwrap($this->alipayConfig['appPrivateKey'], $pemWidth, "\n", true) . "\n";
        $rsaKeyPem .= $keySuffix;

        // openssl扩展不存在
        if (!function_exists('openssl_pkey_get_public') || !function_exists('openssl_pkey_get_private')) $this->error('openssl扩展不存在');
        // 私钥加密错误，请检查支付宝支付配置
        if (1 === intval($keyType) && false == openssl_pkey_get_private($rsaKeyPem)) $this->error('RSA加密私钥错误，请检查支付宝支付配置');

        return $rsaKeyPem;
    }
}
