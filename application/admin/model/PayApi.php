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
namespace app\admin\model;

use think\Model;
use think\Config;
use think\Db;

/**
 * 支付接口模型
 */
class PayApi extends Model
{
    private $key = ''; // key密钥

    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /*
     * 验证微信支付配置信息是否正确
     */
    public function VerifyWeChatConfig($wechat = [])
    {
        if (empty($wechat)) return false;
        $this->key = $wechat['key'];

        // 支付备注
        $body = "验证支付";
        if (1 == config('global.opencodetype')) {
            $web_name = tpCache('web.web_name');
            $web_name = !empty($web_name) ? "[{$web_name}]" : "";
            $body = $web_name.$body;
        }

        // 支付数据
        $out_trade_no             = getTime();
        $data['out_trade_no']     = $out_trade_no;
        $data['total_fee']        = '1';
        $data['spbill_create_ip'] = $this->get_client_ip();
        $data['attach']           = '微信扫码支付';
        $data['body']             = $body."订单号:{$out_trade_no}";
        $data['appid']            = $wechat['appid'];
        $data['mch_id']           = $wechat['mchid'];
        $data['nonce_str']        = getTime();
        $data['trade_type']       = "NATIVE";
        $data['notify_url']       = url('user/Pay/pay_deal_with');

        // 签名加密
        $sign = $this->getParam($data);

        // 转化XML格式
        $dataXML = "<xml>
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

        // 调用接口
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $result = $this->https_post($url, $dataXML);

        // 转换回数组格式
        $result = $this->xmlToArray($result);

        // 返回结果
        if($result['return_code'] == 'SUCCESS' && $result['return_msg'] == 'OK') {
            return $result['code_url'];
        } else {
            if ('签名错误' == $result['return_msg']) {
                $result['return_msg'] = '微信KEY值错误！';
            } else if (stristr($result['return_msg'], 'mch_id')) {
                $result['return_msg'] = '微信商户号错误！';
            } else if (stristr($result['return_msg'], 'appid')) {
                $result['return_msg'] = '微信AppId错误！';
            }
            return $result;
        }
    }

    // 获取客户端IP
    private function get_client_ip() {
        if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
    }

    // 对参数排序，生成MD5加密签名
    private function getParam($paramArray, $isencode=false)
    {
        $paramStr = '';
        ksort($paramArray);
        $i = 0;

        foreach ($paramArray as $key => $value)
        {
            if ($key == 'Signature'){
                continue;
            }
            if ($i == 0){
                $paramStr .= '';
            }else{
                $paramStr .= '&';
            }
            $paramStr .= $key . '=' . ($isencode ? urlencode($value) : $value);
            ++$i;
        }

        $stringSignTemp=$paramStr."&key=".$this->key;
        $sign=strtoupper(md5($stringSignTemp));
        return $sign;

    }

    // POST提交数据
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

    // XML转array
    private function xmlToArray($xml)
    {
        libxml_disable_entity_loader(true);
        $xmlstring = (array)simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring),true);
        return $val;
    }

    // 验证支付宝支付配置是否正确
    public function VerifyAliPayConfig($alipay = [])
    {
        if (!empty($alipay)) {
            // 时间戳当订单号
            $order_number = getTime();

            // 引入文件
            vendor('alipay.pagepay.service.AlipayTradeService');
            vendor('alipay.pagepay.buildermodel.AlipayTradeQueryContentBuilder');

            // 实例化加载订单号
            $RequestBuilder = new \AlipayTradeQueryContentBuilder;
            $out_trade_no   = trim($order_number);
            $RequestBuilder->setOutTradeNo($out_trade_no);

            // 处理支付宝配置数据
            $config['app_id']     = $alipay['app_id'];
            $config['merchant_private_key'] = $alipay['merchant_private_key'];
            $config['charset']    = 'UTF-8';
            $config['sign_type']  = 'RSA2';
            $config['gatewayUrl'] = 'https://openapi.alipay.com/gateway.do';
            $config['alipay_public_key'] = $alipay['alipay_public_key'];

            // 实例化支付宝配置
            $aop = new \AlipayTradeService($config);

            // 返回结果
            $result = $aop->IsQuery($RequestBuilder, 'admin_pay');
            $result = json_decode(json_encode($result), true);

            // 判断结果
            if ('40004' == $result['code'] && 'Business Failed' == $result['msg']) {
                // 用于支付宝支付配置验证
                return 'ok';
            } else if ('40001' == $result['code'] && 'Missing Required Arguments' == $result['msg']) {
                return '商户私钥错误！';
            } else if (!is_array($result)) {
                return $result;
            }
        }
    }
}