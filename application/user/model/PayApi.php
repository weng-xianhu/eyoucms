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
namespace app\user\model;

use think\Model;
use think\Config;
use think\Db;

/**
 * 支付API数据层
 */
class PayApi extends Model
{
    private $home_lang = 'cn';
    private $key = ''; // key密钥

    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        $this->home_lang = get_home_lang();
    }

    /*
     *   微信端H5支付，手机微信直接调起微信支付
     *   @params string $openid : 用户的openid
     *   @params string $out_trade_no : 商户订单号
     *   @params number $total_fee : 订单金额，单位分
     *   return string  $ReturnData : 微信支付所需参数数组
     */
    public function getWechatPay($openid, $out_trade_no, $total_fee, $PayInfo = [], $is_applets = 0, $transaction_type = 2)
    {
        // 获取微信配置信息
        if (empty($PayInfo)) {
            $where = [
                'pay_id' => 1,
                'pay_mark' => 'wechat'
            ];
            $PayInfo = Db::name('pay_api_config')->where($where)->getField('pay_info');
            if (empty($PayInfo)) return false;
            $PayInfo = unserialize($PayInfo);
        }

        // 支付密钥
        $this->key = $PayInfo['key'];

        // 小程序配置
        if (1 === $is_applets) {
            $MiniproValue = Db::name('weapp_minipro0002')->where('type', 'minipro')->getField('value');
            if (empty($MiniproValue)) return false;
            $MiniproValue = !empty($MiniproValue) ? json_decode($MiniproValue, true) : [];
            $PayInfo['appid'] = $MiniproValue['appId'];

            // 支付备注
            $body = "小程序支付";
        } else {
            // 支付备注
            $body = "微信支付";
        }

        // 支付备注
        if (1 == config('global.opencodetype')) {
            $web_name = tpCache('web.web_name');
            $web_name = !empty($web_name) ? "[{$web_name}]" : "";
            $body = $web_name . $body;
        }

        //支付数据
        $data['body']             = $body . "订单号:{$out_trade_no}";
        $data['attach']           = "wechat|,|is_notify|,|" . $transaction_type . '|,|' . session('users_id');
        $data['out_trade_no']     = $out_trade_no;
        $data['total_fee']        = $total_fee * 100;
        $data['nonce_str']        = getTime();
        $data['spbill_create_ip'] = $this->get_client_ip();
        $data['appid']            = $PayInfo['appid'];
        $data['mch_id']           = $PayInfo['mchid'];
        $data['trade_type']       = "JSAPI";
        $data['notify_url']       = request()->domain() . ROOT_DIR . '/index.php'; // 异步地址
        $data['openid']           = $openid;
        
        // MD5加密签名
        $sign = $this->getParam($data);

        // 数据XML化
        $DataXML = "<xml>
           <appid>".$data['appid']."</appid>
           <attach>".$data['attach']."</attach>
           <body>".$data['body']."</body>
           <mch_id>".$data['mch_id']."</mch_id>
           <nonce_str>".$data['nonce_str']."</nonce_str>
           <notify_url>".$data['notify_url']."</notify_url>
           <openid>".$data['openid']."</openid>
           <out_trade_no>".$data['out_trade_no']."</out_trade_no>
           <spbill_create_ip>".$data['spbill_create_ip']."</spbill_create_ip>
           <total_fee>".$data['total_fee']."</total_fee>
           <trade_type>".$data['trade_type']."</trade_type>
           <sign>".$sign."</sign>
        </xml>";

        // 调用接口并解析XML数据，返回调用支付所需参数
        $PostUrl = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $Result = $this->https_post($PostUrl, $DataXML);
        $Result = $this->xmlToArray($Result);
        if (isset($Result['return_code']) && $Result['return_code'] == 'SUCCESS' && $Result['return_msg'] == 'OK') {
            $timeStamp  = getTime();
            $ReturnData = [
                'appId'     => $PayInfo['appid'],
                'timeStamp' => "$timeStamp",
                'nonceStr'  => $this->GetRandomString(12),
                'package'   => 'prepay_id='.$Result['prepay_id'],
                'signType'  => 'MD5',
            ];
            $ReturnSign = $this->getParam($ReturnData);
            $ReturnData['paySign'] = $ReturnSign;
            return $ReturnData;
        } else {
            return $Result;
        }
    }

    /*
     *   微信H5支付，手机浏览器调起微信支付
     *   @params string $openid : 用户的openid
     *   @params string $out_trade_no : 商户订单号
     *   @params number $total_fee : 订单金额，单位分
     *   return string $mweb_url : 二维码URL链接
     */
    public function getMobilePay($out_trade_no = null, $total_fee = null, $PayInfo = [], $transaction_type = 2)
    {
        // 获取微信配置信息
        if (empty($PayInfo)) {
            $where = [
                'pay_id' => 1,
                'pay_mark' => 'wechat'
            ];
            $PayInfo = Db::name('pay_api_config')->where($where)->getField('pay_info');
            if (empty($PayInfo)) return false;
            $PayInfo = unserialize($PayInfo);
        }

        // 支付密钥
        $this->key = $PayInfo['key'];

        // 支付备注
        $body = "微信支付";
        if (1 == config('global.opencodetype')) {
            $web_name = tpCache('web.web_name');
            $web_name = !empty($web_name) ? "[{$web_name}]" : "";
            $body = $web_name . $body;
        }

        //支付数据
        $data['out_trade_no']     = $out_trade_no;
        $data['total_fee']        = $total_fee * 100;
        $data['spbill_create_ip'] = $this->get_client_ip();
        $data['attach']           = "wechat|,|is_notify|,|" . $transaction_type . '|,|' . session('users_id');
        $data['body']             = $body . "订单号:{$out_trade_no}";
        $data['appid']            = $PayInfo['appid'];
        $data['mch_id']           = $PayInfo['mchid'];
        $data['nonce_str']        = getTime();
        $data['trade_type']       = "MWEB";
        $data['notify_url']       = request()->domain() . ROOT_DIR . '/index.php'; // 异步地址
        $data['scene_info']       = '{"h5_info":{"type":"Wap","wap_url":' . $data['notify_url'] . ',"wap_name":"微信支付"}}';
        
        // MD5加密签名
        $sign = $this->getParam($data);

        // 数据XML化
        $DataXML = "<xml>
           <appid>".$data['appid']."</appid>
           <attach>".$data['attach']."</attach>
           <body>".$data['body']."</body>
           <mch_id>".$data['mch_id']."</mch_id>
           <nonce_str>".$data['nonce_str']."</nonce_str>
           <notify_url>".$data['notify_url']."</notify_url>
           <out_trade_no>".$data['out_trade_no']."</out_trade_no>
           <scene_info>".$data['scene_info']."</scene_info>
           <spbill_create_ip>".$data['spbill_create_ip']."</spbill_create_ip>
           <total_fee>".$data['total_fee']."</total_fee>
           <trade_type>".$data['trade_type']."</trade_type>
           <sign>".$sign."</sign>
        </xml>";

        // 调用接口并解析XML数据，返回跳转支付链接
        $PostUrl = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $Result = $this->https_post($PostUrl, $DataXML);
        $Result = $this->xmlToArray($Result);
        if(isset($Result['return_code']) && $Result['return_code'] == 'SUCCESS' && $Result['return_msg'] == 'OK') {
            if (!empty($Result['err_code'])) return $Result['err_code_des'];
            return $Result['mweb_url'];
        } else {
            return $Result;
        }
    }

    /*
     *   微信二维码支付
     *   @params string $openid : 用户的openid
     *   @params string $out_trade_no : 商户订单号
     *   @params number $total_fee : 订单金额，单位分
     *   return string $code_url : 二维码URL链接
     */
    public function payForQrcode($out_trade_no = null, $total_fee = null, $transaction_type = 2)
    {
        if (!empty($out_trade_no) || !empty($total_fee)) {
            // 支付配置
            $where = [
                'pay_id' => 1,
                'pay_mark' => 'wechat'
            ];
            $PayInfo = Db::name('pay_api_config')->where($where)->getField('pay_info');
            if (empty($PayInfo)) return false;
            $wechat = unserialize($PayInfo);

            // 支付密钥
            $this->key = $wechat['key'];

            // 支付备注
            $body = "微信支付";
            if (1 == config('global.opencodetype')) {
                $web_name = tpCache('web.web_name');
                $web_name = !empty($web_name) ? "[{$web_name}]" : "";
                $body = $web_name . $body;
            }

            //支付数据
            $data['out_trade_no']     = $out_trade_no;
            $data['total_fee']        = $total_fee * 100;
            $data['spbill_create_ip'] = $this->get_client_ip();
            $data['attach']           = "wechat|,|is_notify|,|" . $transaction_type . '|,|' . session('users_id');
            $data['body']             = $body . "订单号:{$out_trade_no}";
            $data['appid']            = $wechat['appid'];
            $data['mch_id']           = $wechat['mchid'];
            $data['nonce_str']        = getTime();
            $data['trade_type']       = "NATIVE";
            $data['notify_url']       = request()->domain() . ROOT_DIR . '/index.php'; // 异步地址

            // MD5加密签名
            $sign = $this->getParam($data);

            // 数据XML化
            $DataXML = "<xml>
               <appid>".$data['appid']."</appid>
               <attach>".$data['attach']."</attach>
               <body>".$data['body']."</body>
               <mch_id>".$data['mch_id']."</mch_id>
               <nonce_str>".$data['nonce_str']."</nonce_str>
               <notify_url>".$data['notify_url']."</notify_url>
               <out_trade_no>".$data['out_trade_no']."</out_trade_no>
               <spbill_create_ip>".$data['spbill_create_ip']."</spbill_create_ip>
               <total_fee>".$data['total_fee']."</total_fee>
               <trade_type>".$data['trade_type']."</trade_type>
               <sign>".$sign."</sign>
            </xml>";

            // 调用接口并解析XML数据，返回二维码链接
            $PostUrl = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
            $Result = $this->https_post($PostUrl, $DataXML);
            $Result = $this->xmlToArray($Result);
            if (isset($Result['return_code']) && $Result['return_code'] == 'SUCCESS' && $Result['return_msg'] == 'OK') {
                return $Result['code_url'];
            } else {
                return $Result;
            }
        }
    }

    // 获取客户端IP
    private function get_client_ip()
    {
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches [0] : '';
    }

    //对参数排序，生成MD5加密签名
    private function getParam($paramArray, $isencode=false)
    {
        $paramStr = '';
        ksort($paramArray);
        $i = 0;

        foreach ($paramArray as $key => $value) {
            if ($key == 'Signature') {
                continue;
            }
            if ($i == 0) {
                $paramStr .= '';
            } else {
                $paramStr .= '&';
            }
            $paramStr .= $key . '=' . ($isencode ? urlencode($value) : $value);
            ++$i;
        }

        $stringSignTemp=$paramStr."&key=".$this->key;
        $sign=strtoupper(md5($stringSignTemp));
        return $sign;

    }

    //POST提交数据
    private function https_post($url,$data)
    {
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
        // curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return 'Errno: '.curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }

    /*
    * XML转array
    * @params xml $xml : xml 数据
    * return array $data : 转义后的array数组
    */
    private function xmlToArray($xml)
    {
        libxml_disable_entity_loader(true);
        $xmlstring = (array)simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring),true);
        return $val;
    }

    /*
     *   支付宝新版支付，生成支付链接方法。
     *   @params string $data 订单表数据，必须传入
     */
    public function getNewAliPayPayUrl($data = [])
    {
        if (empty($data)) return false;

        // 获取支付宝配置信息
        $where = [
            'pay_id' => 2,
            'pay_mark' => 'alipay'
        ];
        $PayApiConfig = Db::name('pay_api_config')->field('pay_info, pay_terminal')->where($where)->find();
        if (empty($PayApiConfig['pay_info'])) return false;
        $PayInfo = unserialize($PayApiConfig['pay_info']);
        $PayTerminal = !empty($PayApiConfig['pay_terminal']) ? unserialize($PayApiConfig['pay_terminal']) : [];
        
        // 后台支付宝支付配置信息
        $config['app_id'] = $PayInfo['app_id'];
        $config['merchant_private_key'] = $PayInfo['merchant_private_key'];
        $config['alipay_public_key'] = $PayInfo['alipay_public_key'];

        // 支付订单类型
        $config['transaction_type'] = $type = $data['transaction_type'];

        // 异步地址
        $config['notify_url'] = request()->domain() . ROOT_DIR . '/index.php?transaction_type=' . $type . '&is_notify=1';

        // 同步地址
        $config['return_url'] = url('user/Pay/alipay_return', ['transaction_type' => $type, 'is_notify' => 2], true, true);

        // 支付接口固定参数
        $config['charset'] = 'UTF-8';
        $config['sign_type'] = 'RSA2';
        $config['gatewayUrl'] = 'https://openapi.alipay.com/gateway.do';
        
        // 商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = trim($data['unified_number']);

        // 付款金额，必填
        $total_amount = trim($data['unified_amount']);

        // 订单名称，必填
        $subject = '支付';

        // 商品描述，可空
        $body = '支付宝支付';

        // 处理订单名称级商品描述
        if (1 == config('global.opencodetype')) {
            $web_name = tpCache('web.web_name');
            $web_name = !empty($web_name) ? "[{$web_name}]" : "";
            $subject = $web_name . $subject;
            $body = $web_name . $body;
        }

        // 引入SDK文件
        vendor('alipay.pagepay.service.AlipayTradeService');
        vendor('alipay.pagepay.buildermodel.AlipayTradePagePayContentBuilder');

        // 实例化并且构造参数
        $PayContentBuilder = new \AlipayTradePagePayContentBuilder($PayTerminal, isMobile());
        $PayContentBuilder->setBody($body . "订单号:{$out_trade_no}");
        $PayContentBuilder->setSubject($subject . "订单号:{$out_trade_no}");
        $PayContentBuilder->setOutTradeNo($out_trade_no);
        $PayContentBuilder->setTotalAmount($total_amount);

        // 调用SDK进行支付宝支付
        $TradeService = new \AlipayTradeService($config);

        // 支付宝支付终端分发调用
        if (true === isMobile() && !empty($PayTerminal['mobile'])) {
            // 支付宝手机端支付调用
            $response = $TradeService->wapPay($PayContentBuilder, $config['return_url'], $config['notify_url']);
        } else if (!empty($PayTerminal['computer']) || !empty($PayTerminal[0])) {
            // 支付宝电脑端支付调用
            $response = $TradeService->pagePay($PayContentBuilder, $config['return_url'], $config['notify_url']);
        } else {
            // 支付终端全部关闭
            return '后台支付宝支付配置中支付终端全部关闭，请联系管理员！';
        }
    }

    /*
     *   支付宝旧版支付，生成支付链接方法。
     *   @params string $data 订单表数据，必须传入
     *   @params string $alipay 支付宝配置信息，通过 getUsersConfigData 方法调用数据
     *   return string $alipay_url 支付宝支付链接
     */
    public function getOldAliPayPayUrl($data = [], $alipay = [])
    {
        // 重要参数，支付宝配置信息
        if (empty($alipay)) {
            $where = [
                'pay_id' => 2,
                'pay_mark' => 'alipay'
            ];
            $PayInfo = Db::name('pay_api_config')->where($where)->getField('pay_info');
            if (empty($PayInfo)) return false;
            $alipay = unserialize($PayInfo);
        }

        // 参数设置
        $order['out_trade_no'] = $data['unified_number']; //订单号
        $order['price']        = $data['unified_amount']; //订单金额
        $charset               = 'utf-8';  //编码格式
        $real_method           = '2';      //调用方式
        $agent                 = 'C4335994340215837114'; //代理机构
        $seller_email          = $alipay['account'];//支付宝用户账号
        $security_check_code   = $alipay['code'];   //交易安全校验码
        $partner               = $alipay['id'];     //合作者身份ID

        switch ($real_method){
            case '0':
                $service = 'trade_create_by_buyer';
                break;
            case '1':
                $service = 'create_partner_trade_by_buyer';
                break;
            case '2':
                $service = 'create_direct_pay_by_user';
                break;
        }

        // 支付备注
        $body = "支付";
        if (1 == config('global.opencodetype')) {
            $web_name = tpCache('web.web_name');
            $web_name = !empty($web_name) ? "[{$web_name}]" : "";
            $body = $web_name.$body;
        }

        $type       = $data['transaction_type']; //自定义，用于验证
        // 异步地址
        $notify_url = request()->domain().ROOT_DIR.'/index.php?transaction_type='.$type.'&is_notify=1';
        // 同步地址
        $return_url = url('user/Pay/alipay_return', ['transaction_type'=>$type,'is_notify'=>2], true, true);
        // 参数拼装
        $parameter = array(
          'agent'             => $agent,
          'service'           => $service,
          //合作者ID
          'partner'           => $partner,
          '_input_charset'    => $charset,
          'notify_url'        => $notify_url,
          'return_url'        => $return_url,
          /* 业务参数 */
          'subject'           => $body."订单号:{$order['out_trade_no']}",
          'out_trade_no'      => $order['out_trade_no'],
          'price'             => $order['price'],
          'quantity'          => 1,
          'payment_type'      => 1,
          /* 物流参数 */
          'logistics_type'    => 'EXPRESS',
          'logistics_fee'     => 0,
          'logistics_payment' => 'BUYER_PAY_AFTER_RECEIVE',
          /* 买卖双方信息 */
          'seller_email'      => $seller_email,
        );

        ksort($parameter);
        reset($parameter);
        $param = '';
        $sign  = '';

        foreach ($parameter AS $key => $val) {
            $param .= "$key=" . urlencode($val) . "&";
            $sign  .= "$key=$val&";
        }

        $param      = substr($param, 0, -1);
        $sign       = substr($sign, 0, -1) . $security_check_code;
        // $alipay_url = 'https://www.alipay.com/cooperate/gateway.do?' . $param . '&sign=' . MD5($sign) . '&sign_type=MD5';
        $alipay_url = 'https://mapi.alipay.com/gateway.do?' . $param . '&sign=' . MD5($sign) . '&sign_type=MD5';
        return $alipay_url;
    }

    // 获取随机字符串
    // 长度 length
    // 结果 str
    public function GetRandomString($length)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str   = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
}
