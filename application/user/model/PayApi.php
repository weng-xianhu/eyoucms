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
        $this->times = getTime();
        $this->home_lang = get_home_lang();
        $this->url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    }

    /*
     *   微信端H5支付，手机微信直接调起微信支付
     *   @params string $openid : 用户的openid
     *   @params string $out_trade_no : 商户订单号
     *   @params number $total_fee : 订单金额，单位分
     *   return string  $result : 微信支付所需参数数组
     */
    public function getWechatPay($openid = '', $out_trade_no = '', $total_fee = 0, $PayInfo = [], $is_applets = 0, $transaction_type = 2)
    {
        if (isMobile() && isWeixin()) {
            $thirdparty = Db::name('users')->where(['users_id'=>session('users_id')])->getField('thirdparty');
            if (0 === intval($thirdparty) && empty($openid)) $openid = model('ShopPublicHandle')->weChatauthorizeCookie(session('users_id'));
        }

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

        // 支付备注
        $body = "微信支付";

        // 小程序配置
        if (1 === intval($is_applets)) {
            $miniproValue = Db::name('weapp_minipro0002')->where('type', 'minipro')->getField('value');
            if (empty($miniproValue)) return false;
            $miniproValue = !empty($miniproValue) ? json_decode($miniproValue, true) : [];
            $PayInfo['appid'] = $miniproValue['appId'];
            $body = "小程序支付";
        }

        // 支付备注
        if (1 == config('global.opencodetype')) {
            $web_name = tpCache('web.web_name');
            $web_name = !empty($web_name) ? "[{$web_name}]" : "";
            $body = $web_name . $body;
        }

        // 调用支付接口参数
        $params = [
            'appid'            => $PayInfo['appid'],
            'attach'           => "wechat|,|is_notify|,|" . $transaction_type . '|,|' . session('users_id'),
            'body'             => $body . "订单号: {$out_trade_no}",
            'mch_id'           => $PayInfo['mchid'],
            'nonce_str'        => md5($this->times . $openid),
            'notify_url'       => request()->domain() . ROOT_DIR . '/index.php', // 异步地址
            'openid'           => $openid,
            'out_trade_no'     => $out_trade_no,
            'spbill_create_ip' => getClientIP(),
            'total_fee'        => strval($total_fee * 100),
            'trade_type'       => 'JSAPI'
        ];
        // 微信公众号支付密钥
        $this->key = $PayInfo['key'];
        // 微信小程序参数签名
        $params['sign'] = $this->getParamsSign($params);

        // 调用接口返回数据
        $result = $this->getParamsArr($this->executePostRequest($this->getParamsXml($params)));
        // 请求接口成功
        if (!empty($result['return_code']) && $result['return_code'] == 'SUCCESS' && $result['return_msg'] == 'OK') {
            // 返回支付所需参数
            return [
                'appId'     => $PayInfo['appid'],
                'nonceStr'  => $params['nonce_str'],
                'timeStamp' => strval($this->times),
                'package'   => 'prepay_id='.$result['prepay_id'],
                'signType'  => 'MD5',
                // 微信小程序支付签名
                'paySign' => $this->getPaySign($PayInfo['appid'], $params['nonce_str'], $result['prepay_id']),
            ];
        }
        // 请求接口失败
        else if (!empty($result['return_code']) && $result['return_code'] == 'FAIL') {
            if (stristr($result['return_msg'], '签名错误')) {
                $result['return_msg'] = '微信支付KEY密钥不正确';
            } else if (stristr($result['return_msg'], 'mch_id')) {
                $result['return_msg'] = '微信支付商户号配置不正确';
            } else if (stristr($result['return_msg'], 'appid')) {
                $result['return_msg'] = '微信支付AppID配置不正确';
            }
            return $result;
        } else {
            $result['postCode'] = 'error';
            if (!empty($result['return_code']) && $result['return_code'] == 'FAIL' && empty($openid)) $result['return_msg'] = '未配置公众号信息，无法进行微信支付';
            return $result;
        }
    }

    /*
     *   微信H5支付，手机浏览器调起微信支付
     *   @params string $out_trade_no : 商户订单号
     *   @params number $total_fee : 订单金额，单位分
     *   return string $mweb_url : 二维码URL链接
     */
    public function getMobilePay($out_trade_no = '', $total_fee = 0, $PayInfo = [], $transaction_type = 2)
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

        // 支付备注
        $body = "微信支付";
        if (1 == config('global.opencodetype')) {
            $web_name = tpCache('web.web_name');
            $web_name = !empty($web_name) ? "[{$web_name}]" : "";
            $body = $web_name . $body;
        }

        // 调用支付接口参数
        $params = [
            'appid'            => $PayInfo['appid'],
            'attach'           => "wechat|,|is_notify|,|" . $transaction_type . '|,|' . session('users_id'),
            'body'             => $body . "订单号: {$out_trade_no}",
            'mch_id'           => $PayInfo['mchid'],
            'nonce_str'        => md5($this->times),
            'notify_url'       => request()->domain() . ROOT_DIR . '/index.php', // 异步地址
            'out_trade_no'     => $out_trade_no,
            'spbill_create_ip' => getClientIP(),
            'total_fee'        => strval($total_fee * 100),
            'trade_type'       => 'MWEB'
        ];
        $params['scene_info'] = '{"h5_info":{"type":"Wap","wap_url":' . $params['notify_url'] . ',"wap_name":"微信支付"}}';
        // 微信支付密钥
        $this->key = $PayInfo['key'];
        // 微信支付参数签名
        $params['sign'] = $this->getParamsSign($params);
        // 调用接口返回数据
        $result = $this->getParamsArr($this->executePostRequest($this->getParamsXml($params)));
        // 请求接口成功
        if (isset($result['return_code']) && $result['return_code'] == 'SUCCESS' && $result['return_msg'] == 'OK') {
            if (!empty($result['mweb_url'])) return $result['mweb_url'];
            if (!empty($result['err_code'])) return $result['err_code_des'];
        }
        // 请求接口失败
        else if (!empty($result['return_code']) && $result['return_code'] == 'FAIL') {
            if (stristr($result['return_msg'], '签名错误')) {
                $result['return_msg'] = '微信支付KEY密钥不正确';
            } else if (stristr($result['return_msg'], 'mch_id')) {
                $result['return_msg'] = '微信支付商户号配置不正确';
            } else if (stristr($result['return_msg'], 'appid')) {
                $result['return_msg'] = '微信支付AppID配置不正确';
            }
            return $result;
        }
        return $result;
    }

    /*
     *   微信二维码支付
     *   @params string $out_trade_no : 商户订单号
     *   @params number $total_fee : 订单金额，单位分
     *   return string $code_url : 二维码URL链接
     */
    public function payForQrcode($out_trade_no = '', $total_fee = 0, $transaction_type = 2)
    {
        if (!empty($out_trade_no) || !empty($total_fee)) {
            // 支付配置
            $where = [
                'pay_id' => 1,
                'pay_mark' => 'wechat'
            ];
            $PayInfo = Db::name('pay_api_config')->where($where)->getField('pay_info');
            if (empty($PayInfo)) return false;
            $PayInfo = unserialize($PayInfo);

            // 支付备注
            $body = "微信支付";
            if (1 == config('global.opencodetype')) {
                $web_name = tpCache('web.web_name');
                $web_name = !empty($web_name) ? "[{$web_name}]" : "";
                $body = $web_name . $body;
            }

            // 调用支付接口参数
            $params = [
                'appid'            => $PayInfo['appid'],
                'attach'           => "wechat|,|is_notify|,|" . $transaction_type . '|,|' . session('users_id'),
                'body'             => $body . "订单号: {$out_trade_no}",
                'mch_id'           => $PayInfo['mchid'],
                'nonce_str'        => md5($this->times),
                'notify_url'       => request()->domain() . ROOT_DIR . '/index.php', // 异步地址
                'out_trade_no'     => $out_trade_no,
                'spbill_create_ip' => getClientIP(),
                'total_fee'        => strval($total_fee * 100),
                'trade_type'       => 'NATIVE'
            ];
            // 微信公众号支付密钥
            $this->key = $PayInfo['key'];
            // 微信小程序参数签名
            $params['sign'] = $this->getParamsSign($params);

            // 调用接口返回数据
            $result = $this->getParamsArr($this->executePostRequest($this->getParamsXml($params)));
            // 请求接口成功
            if (isset($result['return_code']) && $result['return_code'] == 'SUCCESS' && $result['return_msg'] == 'OK') {
                return $result['code_url'];
            }
            // 请求接口失败
            else if (!empty($result['return_code']) && $result['return_code'] == 'FAIL') {
                if (stristr($result['return_msg'], '签名错误')) {
                    $result['return_msg'] = '微信支付KEY密钥不正确';
                } else if (stristr($result['return_msg'], 'mch_id')) {
                    $result['return_msg'] = '微信支付商户号配置不正确';
                } else if (stristr($result['return_msg'], 'appid')) {
                    $result['return_msg'] = '微信支付AppID配置不正确';
                }
                return $result;
            }
            return $result;
        }
    }

    /*
     *   微信小程序支付
     *   @params string $openid : 用户的openid
     *   @params string $out_trade_no : 商户订单号
     *   @params number $total_fee : 订单金额，单位分
     *   return string  $result : 微信支付所需参数数组
     */
    public function getWechatAppletsPay($openid = '', $out_trade_no = '', $total_fee = 0, $transaction_type = 2, $miniproInfo = [])
    {
        // 微信小程序配置
        if (empty($miniproInfo)) $miniproInfo = model('ShopPublicHandle')->getSpecifyAppletsConfig();

        // 支付备注
        $body = "小程序支付";
        if (1 == config('global.opencodetype')) {
            $web_name = tpCache('web.web_name');
            $web_name = !empty($web_name) ? "[{$web_name}]" : "";
            $body = $web_name . $body;
        }

        // 调用支付接口参数
        $applets_id = input('param.applets_id/d', 0);
        $sendTerminal = input('param.sendTerminal/s', '') ? '|,|' . input('param.sendTerminal/s', '') : '';
        $params = [
            'appid'            => $miniproInfo['appid'],
            'attach'           => "wechat|,|is_notify|,|" . $transaction_type . '|,|' . session('users_id') . '|,|' . $applets_id . "|,|applets" . $sendTerminal,
            'body'             => $body . "订单号: {$out_trade_no}",
            'mch_id'           => $miniproInfo['mchid'],
            'nonce_str'        => md5($this->times . $openid),
            'notify_url'       => request()->domain() . ROOT_DIR . '/index.php', // 异步地址
            'openid'           => $openid,
            'out_trade_no'     => $out_trade_no,
            'spbill_create_ip' => getClientIP(),
            'total_fee'        => strval($total_fee * 100),
            'trade_type'       => 'JSAPI'
        ];
        // 微信小程序支付密钥
        $this->key = $miniproInfo['apikey'];
        // 微信小程序参数签名
        $params['sign'] = $this->getParamsSign($params);
        // 调用接口返回数据
        $result = $this->getParamsArr($this->executePostRequest($this->getParamsXml($params)));
        // 请求接口成功
        if ($result['return_code'] == 'SUCCESS' && $result['return_msg'] == 'OK') {
            if ($result['result_code'] == 'SUCCESS') {
                // 返回支付所需参数
                return [
                    'prepay_id' => $result['prepay_id'],
                    'nonceStr'  => $params['nonce_str'],
                    'timeStamp' => strval($this->times),
                    'return_code' => $result['return_code'],
                    // 微信小程序支付签名
                    'paySign' => $this->getPaySign($miniproInfo['appid'], $params['nonce_str'], $result['prepay_id']),
                ];
            }
        }
        // 请求接口失败
        else if (!empty($result['return_code']) && $result['return_code'] == 'FAIL') {
            if (stristr($result['return_msg'], '签名错误')) {
                $result['return_msg'] = '小程序支付APIv2密钥不正确';
            } else if (stristr($result['return_msg'], 'mch_id')) {
                $result['return_msg'] = '小程序支付商户号配置不正确';
            } else if (stristr($result['return_msg'], 'appid')) {
                $result['return_msg'] = '小程序支付AppID配置不正确';
            }
            return $result;
        }
        return $result;
    }

    /*
     *   查询微信小程序支付结果
     *   @params string $openid : 用户的openid
     *   @params string $out_trade_no : 商户订单号
     *   @params number $total_fee : 订单金额，单位分
     *   return string $code_url : 二维码URL链接
     */
    public function getWeChatPayResult($openid = '', $out_trade_no = '', $miniproInfo = [])
    {
        // 微信小程序配置
        if (empty($miniproInfo)) $miniproInfo = model('ShopPublicHandle')->getSpecifyAppletsConfig();

        // 调用支付接口参数
        $params = [
            'appid'        => $miniproInfo['appid'],
            'mch_id'       => $miniproInfo['mchid'],
            'nonce_str'    => !empty($openid) ? md5($this->times . $openid) : md5($this->times),
            'out_trade_no' => $out_trade_no
        ];
        // 微信小程序支付密钥
        if (!empty($miniproInfo['apikey'])) {
            $this->key = $miniproInfo['apikey'];
        } else if (!empty($miniproInfo['key'])) {
            $this->key = $miniproInfo['key'];
        }
        // 微信小程序参数签名
        $params['sign'] = $this->getParamsSign($params);

        // 微信小程序查询支付结果URL
        $this->url = 'https://api.mch.weixin.qq.com/pay/orderquery';
        // 调用接口返回数据
        $result = $this->getParamsArr($this->executePostRequest($this->getParamsXml($params)));
        if (!empty($result['return_msg'])) {
            if (stristr($result['return_msg'], '签名错误')) {
                $result['return_msg'] = '小程序支付密钥不正确';
            } else if (stristr($result['return_msg'], 'mch_id')) {
                $result['return_msg'] = '小程序商户号配置不正确';
            } else if (stristr($result['return_msg'], 'appid')) {
                $result['return_msg'] = '小程序AppID配置不正确';
            }
        }
        return $result;
    }

    // 对参数排序，生成MD5加密签名
    private function getParamsSign($params = [])
    {
        // 按字典序排序参数后拼装支付密钥key参数再进行MD5加密后转为纯大写字母返回
        ksort($params);
        return strtoupper(md5($this->getParamsUrl($params) . '&key=' . $this->key));
    }

    // 获取提交参数xml(arr转成xml)
    private function getParamsXml($params = [])
    {
        if (!is_array($params) || count($params) <= 0) return false;
        $xml = "<xml>";
        foreach ($params as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    // POST提交数据
    private function executePostRequest($paramsXml = '')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $paramsXml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        // 返回错误提示
        if (curl_errno($ch)) return 'Errno: ' . curl_error($ch);
        // 返回成功数据
        curl_close($ch);
        return $result;
    }

    // 获取返回参数数组(xml转成arr)
    private function getParamsArr($xml = [])
    {
        // 禁止引用外部xml实体
        @libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    // 微信小程序支付签名
    private function getPaySign($appid = '', $nonceStr = '', $prepay_id = '')
    {
        $result = [
            'appId'     => $appid,
            'nonceStr'  => $nonceStr,
            'package'   => 'prepay_id=' . $prepay_id,
            'signType'  => 'MD5',
            'timeStamp' => strval($this->times),
        ];
        return $this->getParamsSign($result);
        // 按字典序排序参数后拼装支付密钥key参数再进行MD5加密后转为纯大写字母返回
        // ksort($result);
        // return strtoupper(md5($this->getParamsUrl($result) . '&key=' . $this->key));
    }

    // 获取数组参数拼装的URL参数
    private function getParamsUrl($values)
    {
        $url = '';
        foreach ($values as $key => $value) {
            if ($key != 'sign' && $value != '' && !is_array($value)) $url .= $key . '=' . $value . '&';
        }
        return trim($url, '&');
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

        // 跳转链接
        $referurl = input('param.referurl/s', null, 'urldecode');
        $referurl = base64_encode($referurl);
        //自定义，用于验证
        $type       = $data['transaction_type'];
        // 异步地址
        $notify_url = request()->domain().ROOT_DIR.'/index.php?transaction_type='.$type.'&is_notify=1';
        // 同步地址
        $return_url = url('user/Pay/alipay_return', ['transaction_type'=>$type,'is_notify'=>2,'referurl'=>$referurl], true, true);
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