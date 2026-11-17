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
namespace app\common\logic;

use think\Model;
use think\Db;

/**
 * Description of SmsLogic
 *
 * 短信类
 */
class SmsLogic extends Model
{
    private $config;
    
    public function __construct($sms_config = []) 
    {
        $this->config = !empty($sms_config) ? $sms_config : tpCache('sms');
    }

    /**
     * 发送短信逻辑
     * @param unknown $source
     */
    public function sendSms($source = null, $sender = null, $params = [], $unique_id = 0)
    {
        $sms_config = $this->config;
        if (empty($sms_config['sms_nation_code'])) $sms_config['sms_nation_code'] = 86;
        $sms_type = isset($sms_config['sms_type']) ? $sms_config['sms_type'] : 1;
        $smsTemp = Db::name('sms_template')->where(["send_scene"=>$source,"sms_type"=>$sms_type,'lang'=>get_admin_lang()])->find();
        if (empty($smsTemp) || empty($smsTemp['sms_sign']) || empty($smsTemp['sms_tpl_code']) || empty($smsTemp['tpl_content'])){
            return $result = ['status' => -1, 'msg' => '尚未正确配置短信模板，请联系管理员！'];
        }
        if (0 == $smsTemp['is_open']) return $result = ['status' => -1, 'msg' => '模板类型已关闭，请先开启'];
        $content = !empty($params['content']) ? $params['content'] : false;
        $code = !empty($params['code']) ? $params['code'] : $content;
        $express_time = !empty($params['express_time']) ? date('Y-m-d H:i:s', $params['express_time']) : false;
        $product_name = !empty($params['product_name']) ? msubstr(trim($params['product_name']), 0, 15).'...' : false;
        if (empty($params['nation_code'])){
            $nationCode = $sms_config['sms_nation_code'];
        }else{
            $nationCode = $params['nation_code'];
        }

        if(empty($unique_id)){
            $session_id = session_id();
        }else{
            $session_id = $unique_id;
        }

        // ToSms短信通知插件内置代码 start
        $toSmsBool = false;
        if (file_exists('./weapp/ToSms/model/ToSmsModel.php')) {
            $toSmsModel = new \weapp\ToSms\model\ToSmsModel;
            $toSmsBool = $toSmsModel->common_SmsLogic_sendSms($sms_type);
        }
        // ToSms短信通知插件内置代码 end

        if ($sms_type == 1 || $toSmsBool === true) {
            if (strpos($smsTemp['tpl_content'], 'code') !== false) {
                $smsParams = array(
                    0 => "{\"code\":\"$code\"}",
                    1 => "{\"code\":\"$code\"}",
                    2 => "{\"code\":\"$code\"}",
                    3 => "{\"code\":\"$code\"}",
                    4 => "{\"code\":\"$code\"}",
                    5 => "{\"code\":\"$code\"}",
                    6 => "{\"code\":\"$code\"}",
                    7 => "{\"code\":\"$code\"}",
                    30 => "{\"code\":\"$code\"}",
                );
            } else if (strpos($smsTemp['tpl_content'], 'content') !== false) {
                $smsParams = array(
                    0 => "{\"content\":\"$content\"}",
                    1 => "{\"content\":\"$content\"}",
                    2 => "{\"content\":\"$content\"}",
                    3 => "{\"content\":\"$content\"}",
                    4 => "{\"content\":\"$content\"}",
                    5 => "{\"content\":\"$content\"}",
                    6 => "{\"content\":\"$content\"}",
                    7 => "{\"content\":\"$content\"}",
                    30 => "{\"content\":\"$content\"}",
                );
            } else if (strpos($smsTemp['tpl_content'], 'name') !== false) {
                $smsParams = array(
                    0 => "{\"name\":\"$name\"}",
                    1 => "{\"name\":\"$name\"}",
                    2 => "{\"name\":\"$name\"}",
                    3 => "{\"name\":\"$name\"}",
                    4 => "{\"name\":\"$name\"}",
                    5 => "{\"name\":\"$name\"}",
                    6 => "{\"name\":\"$name\"}",
                    7 => "{\"name\":\"$name\"}",
                    30 => "{\"name\":\"$name\"}",
                );
            } else if (in_array($source, [20,30])) {
                $smsParams[$source] = "{\"content\":\"$content\"}";
            } else {
                $smsParams[6] = "{\"goods_title\":\"$product_name\",\"express_time\":\"$express_time\"}";
            }
            $smsParam = $smsParams[$source];
            // 提取发送短信内容
            $msg = $smsTemp['tpl_content'];
            $params_arr = json_decode($smsParam);
            foreach ($params_arr as $k => $v) {
                $msg = str_replace('${' . $k . '}', $v, $msg);
            }
        } else {
            if (!in_array($source,[11,20])){
                $params_arr = $smsParam = array_values($params);
                //提取发送短信内容
                $msg = $smsTemp['tpl_content'];
                foreach ($params_arr as $k => $v) {
                    $index = $k+1;
                    $msg = str_replace('{' . $index . '}', $v, $msg);
                }
            }else{
                $smsParam = [];
            }
        }
        //发送记录存储数据库
        $smsData = [];
        $time = getTime();
        $sender_arr = explode(',', $sender);
        foreach ($sender_arr as $key => $val) {
            $val = trim($val);
            if (empty($val)) {
                unset($sender_arr[$key]);
                continue;
            }
            //如果是中国的手机号 那就验证手机号格式
            if (86 == $nationCode && !check_mobile($val)){
                unset($sender_arr[$key]);
                continue;
            }

            $smsData[] = [
                'source'    => $source,
                'sms_type'  => $sms_type,
                'mobile'    => 86 == $nationCode ? $val : $nationCode . $val,
                'code'      => $code,
                'add_time'  => $time,
                'status'    => 0,
                'msg'       => $msg,
                'is_use'    => 0,
                'error_msg' => '',
                'ip'        => clientIP(),
                'update_time' => $time,
            ];

            //阿里云发送短信直接拼区号,腾讯云不拼,单独传区号
            if (86 != $nationCode && 1 == $sms_type){
                $sender_arr[$key] = $nationCode.$val;
            }
        }
        $sender = implode(',', $sender_arr);

        if (!empty($sender)) {
            // 如果是正常的手机号码才发送
            try {
                $resp = $this->realSendSms($sender, $smsTemp['sms_sign'], $smsParam, $smsTemp['sms_tpl_code'],$nationCode, $msg);
            } catch (\Exception $e) {
                $resp = ['status' => -1, 'msg' => $this->translateSmsError($e->getMessage())];
            }
            if (!empty($resp['status']) && $resp['status'] == 1) {
                try{
                    if (!empty($smsData)) {
                        $rdata = model('SmsLog')->saveAll($smsData);
                        if ($rdata !== false) {
                            if (30 == $source) {
                                foreach ($rdata as $k1 => $v1) {
                                    $id_new = $v1->getData('id');
                                    $mobile_new = $v1->getData('mobile');
                                    $where = [
                                        'source' => $source,
                                        'mobile' => $mobile_new,
                                        'id' => ['NEQ', $id_new],
                                    ];
                                    Db::name('sms_log')->where($where)->update(['is_use'=>1]);
                                }
                            }
                        }
                    }
                }catch (\Exception $e){}
                // 修改发送状态为成功
                Db::name('sms_log')->where(['mobile'=>['IN', $sender_arr], 'update_time'=>$time])->save(['status'=>1, 'update_time'=>getTime()]);
                // 只保留最近1小时没使用的短信日志
                try {
                    $mtime = strtotime("-1 day");
                    Db::name('sms_log')->where([
                        'update_time'  => ['lt', $mtime],
                        'status' => 1,
                        ])->delete(true);
                } catch (\Exception $e) {}
            } else {
                try{
                    if (!empty($smsData)) {
                        $rdata = model('SmsLog')->saveAll($smsData);
                        if ($rdata !== false) {
                            if (30 == $source) {
                                foreach ($rdata as $k1 => $v1) {
                                    $id_new = $v1->getData('id');
                                    $mobile_new = $v1->getData('mobile');
                                    $where = [
                                        'source' => $source,
                                        'mobile' => $mobile_new,
                                        'id' => ['NEQ', $id_new],
                                    ];
                                    Db::name('sms_log')->where($where)->update(['is_use'=>1]);
                                }
                            }
                        }
                    }
                }catch (\Exception $e){}
                // 发送失败, 将发送失败信息保存数据库
                Db::name('sms_log')->where(['mobile'=>['IN', $sender_arr], 'update_time'=>$time])->update(['error_msg'=>$resp['msg'], 'update_time'=>getTime()]);
                // 删除发送失败的记录
                try {
                    $mtime = strtotime("-2 minute");
                    Db::name('sms_log')->where([
                        'update_time'  => ['lt', $mtime],
                        'status' => 0,
                        ])->delete(true);
                } catch (\Exception $e) {}
            }
            return $resp;
        } else {
           return $result = ['status' => -1, 'msg' => '接收手机号不正确['.$sender.']'];
        }
        
    }

    /**
     * 将短信接口返回的英文/网络错误信息转换为易懂的中文提示（各平台通用）
     * @param string $msg 原始错误信息
     * @param string $platform 平台名称，用于提示语
     * @return string 中文提示
     */
    private function translateSmsError($msg, $platform = '短信平台')
    {
        $msg = trim(strval($msg));
        if ('' === $msg) {
            return '短信发送失败，接口未返回错误信息，请检查短信平台配置是否正确';
        }
        // 已转换过的信息不再重复处理
        if (false !== strpos($msg, '原始信息') || false !== strpos($msg, '错误码：')) {
            return $msg;
        }
        $raw = preg_replace('/\s+/', ' ', trim(strip_tags($msg)));

        // 按优先级排列的匹配规则：[正则, 中文提示]
        $rules = [
            ['/call to undefined function curl|curl_init|curl extension|未知函数调用.*curl/i',
                'PHP 未开启 curl 扩展，无法请求' . $platform . '接口，请开启 curl 扩展或联系空间商'],
            ['/could not resolve|getaddrinfo|name or service not known|no such host|resolving timed out/i',
                '无法解析' . $platform . '接口域名，请检查服务器 DNS 设置及网络是否正常'],
            ['/ssl|tls|certificate|handshake/i',
                '请求' . $platform . '接口时 SSL 连接失败，请确认 PHP 已开启 openssl 扩展，且服务器 CA 证书及系统时间正确'],
            ['/server unreachable|timed out|timeout|connection refused|failed to connect|couldn\'t connect|network is unreachable|connection reset|recv failure|empty reply/i',
                '连接' . $platform . '接口失败或超时，请检查服务器外网访问及防火墙是否放行 443 端口，稍后重试'],
            ['/proxy|407/i',
                '服务器代理设置异常，导致无法访问' . $platform . '接口，请检查服务器网络配置'],
            ['/http code|http status|http error|(?<!\d)50[0-4](?!\d)|bad gateway|service unavailable|gateway time-?out/i',
                $platform . '接口暂时不可用，请稍后重试'],
            ['/json|malformed|unexpected/i',
                $platform . '接口返回数据异常，请稍后重试，如持续出现请检查服务器网络是否被劫持'],
        ];
        $tip = '';
        foreach ($rules as $rule) {
            if (preg_match($rule[0], $raw)) {
                $tip = $rule[1];
                break;
            }
        }
        if ('' === $tip) {
            // 原始信息已是中文，直接返回
            if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $raw)) {
                return $msg;
            }
            $tip = '短信发送失败，请核对' . $platform . '的密钥、签名、模板配置是否正确';
        }
        if (function_exists('mb_strlen') && mb_strlen($raw, 'UTF-8') > 200) {
            $raw = mb_substr($raw, 0, 200, 'UTF-8') . '...';
        }
        return $tip . '（原始信息：' . htmlspecialchars($raw, ENT_QUOTES, 'UTF-8') . '）';
    }

    private function realSendSms($mobile, $smsSign, $smsParam, $templateCode, $nationCode,$msg = '')
    {
        if (config('sms_debug') == true) {
            return array('status' => 1, 'msg' => '专用于越过短信发送');
        }
        $param = input('param.');
        if (!isset($param['sms_type'])) {
            $type = (int)$this->config['sms_type'] ?: 1;
        }else{
            $type = $param['sms_type'];
        }

        switch($type) {
            case 1:
                $result = $this->sendSmsByAliyun($mobile, $smsSign, $smsParam, $templateCode);
                break;
            case 2:
                $result = $this->sendSmsByTencentCloud($mobile, $smsSign, $smsParam, $templateCode,$nationCode);
                break;
            default:
                $result = ['status' => -1, 'msg' => '不支持的短信平台'];
                // ToSms短信通知插件内置代码 start
                if (file_exists('./weapp/ToSms/model/ToSmsModel.php')) {
                    $toSmsModel = new \weapp\ToSms\model\ToSmsModel;
                    $res = $toSmsModel->common_SmsLogic_realSendSms($type, $mobile, $smsSign, $smsParam, $templateCode, $this->config, $msg);
                    if (!empty($res)) {
                        $result = $res;
                    }
                }
                // ToSms短信通知插件内置代码 end
        }
        
        return $result;
    }

    /**
     * 发送短信（阿里云短信）
     * @param $mobile  手机号码
     * @param $code    验证码
     * @return bool    短信发送成功返回true失败返回false
     */
    private function sendSmsByAliyun($mobile, $smsSign, $smsParam, $templateCode)
    {
        include_once './vendor/aliyun-php-sdk-core/Config.php';
        include_once './vendor/Dysmsapi/Request/V20170525/SendSmsRequest.php';
        
        $accessKeyId = $this->config['sms_appkey'];
        $accessKeySecret = $this->config['sms_secretkey'];
        if (empty($accessKeyId) || empty($accessKeySecret)){
            return array('status' => -1, 'msg' => '请设置短信平台appkey和secretKey');
        }
        //短信API产品名
        $product = "Dysmsapi";
        //短信API产品域名
        $domain = "dysmsapi.aliyuncs.com";
        //暂时不支持多Region
        $region = "cn-hangzhou";

        //初始化访问的acsCleint
        $profile = \DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
        \DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
        $acsClient= new \DefaultAcsClient($profile);

        $request = new \Dysmsapi\Request\V20170525\SendSmsRequest;
        //必填-短信接收号码
        $request->setPhoneNumbers($mobile);
        //必填-短信签名
        $request->setSignName($smsSign);
        //必填-短信模板Code
        $request->setTemplateCode($templateCode);
        //选填-假如模板中存在变量需要替换则为必填(JSON格式)
        $request->setTemplateParam($smsParam);
        //选填-发送短信流水号
        //$request->setOutId("1234");
        
        //发起访问请求
        try {
            $resp = $acsClient->getAcsResponse($request);
        } catch (\Exception $e) {
            $errCode = method_exists($e, 'getErrorCode') ? $e->getErrorCode() : '';
            return array('status' => -1, 'msg' => $this->aliyunSmsErrorMsg($errCode, $e->getMessage()));
        }
        
        //短信发送成功返回True，失败返回false
        if (!empty($resp) && isset($resp->Code) && $resp->Code == 'OK') {
            return array('status' => 1, 'msg' => $resp->Code);
        } else {
            $respCode = isset($resp->Code) ? $resp->Code : '';
            $respMsg  = isset($resp->Message) ? $resp->Message : '';
            return array('status' => -1, 'msg' => $this->aliyunSmsErrorMsg($respCode, $respMsg));
        }
    }

    /**
     * 将阿里云短信错误码转换为易懂的中文提示
     * @param string $code 阿里云返回的错误码
     * @param string $message 阿里云返回的原始错误信息
     * @return string 中文提示
     */
    private function aliyunSmsErrorMsg($code, $message = '')
    {
        $map = array(
            'InvalidAccessKeyId.NotFound'      => 'AccessKey ID 不存在，请检查【平台appkey】是否填写正确，或该密钥是否已被删除/禁用',
            'InvalidAccessKeyId'               => 'AccessKey ID 无效，请检查【平台appkey】是否填写正确',
            'SignatureDoesNotMatch'            => 'AccessKey Secret 不正确，请检查【平台secretKey】是否填写正确',
            'isv.SMS_SIGNATURE_ILLEGAL'        => '短信签名不合法或未审核通过，请检查短信模板中的签名设置',
            'isv.SMS_TEMPLATE_ILLEGAL'         => '短信模板不存在或未审核通过，请检查模板Code是否正确',
            'isv.ACCOUNT_NOT_EXISTS'           => '阿里云短信账户不存在，请确认已开通短信服务',
            'isv.ACCOUNT_ABNORMAL'             => '阿里云短信账户异常，请登录阿里云控制台查看',
            'isv.AMOUNT_NOT_ENOUGH'            => '阿里云账户余额不足，请充值后重试',
            'isv.OUT_OF_SERVICE'               => '短信服务已停机（余额不足），请充值后重试',
            'isv.BUSINESS_LIMIT_CONTROL'       => '发送频率超限，请稍后再试',
            'isv.MOBILE_NUMBER_ILLEGAL'        => '手机号码格式错误，请检查接收号码',
            'isv.DAY_LIMIT_CONTROL'            => '当日发送量已达上限，请明天再试',
            'isv.TEMPLATE_MISSING_PARAMETERS'  => '短信模板变量缺失，请检查模板内容与变量设置',
            'isv.INVALID_PARAMETERS'           => '参数异常，请检查签名、模板和变量设置',
            'isp.RAM_PERMISSION_DENY'          => '该 AccessKey 没有短信发送权限，请在阿里云 RAM 中授权 AliyunDysmsFullAccess',
            'Forbidden.AccessKeyDisabled'      => 'AccessKey 已被禁用，请到阿里云控制台启用或更换密钥',
            'MissingAccessKeyId'               => '未填写 AccessKey ID，请填写【平台appkey】',
            'InvalidTimeStamp.Expired'         => '服务器系统时间不正确（与北京时间相差超过15分钟），请校准服务器时间后重试',
            'Throttling'                       => '调用频率过高被阿里云限流，请稍后再试',
            'Throttling.User'                  => '调用频率过高被阿里云限流，请稍后再试',
            'isv.PRODUCT_UN_SUBSCRIPT'         => '阿里云账号未开通短信服务，请先到阿里云控制台开通',
            'isv.PRODUCT_UNSUBSCRIBE'          => '阿里云账号未开通短信服务，请先到阿里云控制台开通',
            'isv.MONTH_LIMIT_CONTROL'          => '当月发送量已达上限，请到阿里云控制台提升额度',
            'isv.SMS_CONTENT_ILLEGAL'          => '短信内容包含禁止发送的内容，请修改模板内容或变量',
            'isv.SMS_SIGN_ILLEGAL'             => '短信签名不合法或未审核通过，请检查短信模板中的签名设置',
            'isv.INVALID_JSON_PARAM'           => '模板变量格式错误（非标准 JSON），请检查模板变量内容是否包含特殊字符',
            'isv.PARAM_LENGTH_LIMIT'           => '模板变量内容超长（单个变量限 20 字），请精简变量内容',
            'isv.PARAM_NOT_SUPPORT_URL'        => '模板变量中不允许包含网址，请修改变量内容',
            'isv.BLACK_KEY_CONTROL_LIMIT'      => '模板变量中包含阿里云禁止的关键词，请修改变量内容',
            'isv.DENY_IP_RANGE'                => '当前服务器 IP 不在阿里云允许的访问范围内，请到控制台检查 IP 白名单设置',
            'isv.EXTEND_CODE_ERROR'            => '扩展码使用错误，请检查短信接口参数',
            'isv.DOMESTIC_NUMBER_NOT_SUPPORTED'=> '当前接口不支持国内号码，请检查国际区号设置',
            'isv.SMS_TEST_SIGN_TEMPLATE_LIMIT' => '测试签名/模板仅能搭配使用，请使用已审核通过的正式签名和模板',
            'isv.SMS_TEST_NUMBER_LIMIT'        => '测试签名/模板只能向阿里云控制台绑定的测试手机号发送',
            'isv.SMS_TEST_TEMPLATE_PARAMS_ILLEGAL' => '测试模板的变量值不合法，请使用数字验证码测试',
            'isv.SYSTEM_ERROR'                 => '阿里云短信系统异常，请稍后重试',
            'isp.SYSTEM_ERROR'                 => '阿里云短信系统异常，请稍后重试',
            'SDK.ServerUnreachable'            => '服务器无法连接阿里云短信接口（dysmsapi.aliyuncs.com），请检查服务器外网访问、DNS 及防火墙是否放行 443 端口',
            'SDK.InvalidRegionId'              => '阿里云短信接口地域配置错误，请联系技术人员检查 SDK',
            'SDK.InvalidProfile'               => '阿里云 SDK 初始化失败，请检查【平台appkey】和【平台secretKey】是否填写',
        );
        $code = trim(strval($code));
        $message = trim(strval($message));
        if ('' !== $code && isset($map[$code])) {
            return $map[$code] . '（错误码：' . $code . '）';
        }
        // 未收录的错误码：按错误码关键词分类提示
        $codeRules = [
            ['/AccessKey|Signature|InvalidSecurityToken|Forbidden/i', '密钥验证失败，请检查【平台appkey】和【平台secretKey】是否填写正确'],
            ['/SIGN/i',                      '短信签名异常，请检查签名是否已审核通过且与模板中填写一致'],
            ['/TEMPLATE/i',                  '短信模板异常，请检查模板Code是否正确且已审核通过'],
            ['/MOBILE|NUMBER|PHONE/i',       '手机号码异常，请检查接收号码及国际区号'],
            ['/LIMIT|Throttling/i',          '发送频率或发送量超限，请稍后再试'],
            ['/AMOUNT|OUT_OF_SERVICE|ACCOUNT/i', '阿里云短信账户异常或余额不足，请登录阿里云控制台查看'],
            ['/PARAM/i',                     '参数异常，请检查短信模板变量设置'],
            ['/^SDK\./i',                    '调用阿里云短信 SDK 失败，请检查服务器网络与 PHP curl/openssl 扩展'],
        ];
        if ('' !== $code) {
            foreach ($codeRules as $rule) {
                if (preg_match($rule[0], $code)) {
                    $tip = $rule[1] . '（错误码：' . $code;
                    if ('' !== $message) {
                        $tip .= '，原始信息：' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
                    }
                    return $tip . '）';
                }
            }
        }
        if ('' === $message) {
            return '' !== $code ? '短信发送失败（错误码：' . $code . '）' : '短信发送失败，阿里云未返回错误信息，请稍后重试';
        }
        if (strpos($message, 'Specified access key is not found') !== false) {
            return 'AccessKey ID 不存在，请检查【平台appkey】是否填写正确，或该密钥是否已被删除/禁用（错误码：InvalidAccessKeyId.NotFound）';
        }
        // 网络/环境类错误以及其他英文信息，走通用转换
        $tip = $this->translateSmsError($message, '阿里云短信');
        if ('' !== $code && false === strpos($tip, $code)) {
            $tip .= '（错误码：' . $code . '）';
        }
        return $tip;
    }

    /**
     * 发送短信（腾讯云短信）
     * @param $mobile  手机号码
     * @param $code    验证码
     * @return bool    短信发送成功返回true失败返回false
     */
    private function sendSmsByTencentCloud($mobile, $smsSign, $smsParam, $templateCode,$nationCode = "86")
    {
        // 短信应用SDK AppID   1400开头
        $appid     = $this->config['sms_appid_tx'];

        // 短信应用SDK AppKey
        $appkey = $this->config['sms_appkey_tx'];
        if (empty($appid) || empty($appkey)) {
            return array('status' => -1, 'msg' => '请设置短信平台appkid和appkey');
        }

        // 需要发送短信的手机号码
        $phoneNumbers = $mobile;

        // 短信模板ID，需要在短信应用中申请
        $templateId = $templateCode;  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请

        // 签名
        $smsSign = $smsSign; // NOTE: 这里的签名只是示例，请使用真实的已申请的签名，签名参数使用的是`签名内容`，而不是`签名ID`
//        var_dump($smsParam);exit;

        // 单发短信
        Vendor('tencentsms.SmsSingleSender');
        $ssender = new \SmsSingleSender($appid, $appkey);
        $params = $smsParam;//数组具体的元素个数和模板中变量个数必须一致，例如事例中 templateId:5678对应一个变量，参数数组中元素个数也必须是一个
        $result = $ssender->sendWithParam($nationCode, $phoneNumbers, $templateId,
            $params, $smsSign, "", "");  // 签名参数未提供或者为空时，会使用默认签名发送短信
        $resp = json_decode($result,TRUE);

        //短信发送成功返回True，失败返回false
        if (is_array($resp) && isset($resp['errmsg']) && $resp['errmsg'] == 'OK') {
            return array('status' => 1, 'msg' => $resp['errmsg']);
        } else {
            $errmsg = is_array($resp) && isset($resp['errmsg']) ? $resp['errmsg'] : strval($result);
            $errcode = is_array($resp) && isset($resp['result']) ? $resp['result'] : '';
            return array('status' => -1, 'msg' => $this->tencentSmsErrorMsg($errmsg, $errcode));
        }
    }

    /**
     * 将腾讯云短信错误信息转换为易懂的中文提示
     * @param string $errmsg 腾讯云返回的原始错误信息
     * @param string|int $errcode 腾讯云返回的 result 错误码
     * @return string 中文提示
     */
    private function tencentSmsErrorMsg($errmsg, $errcode = '')
    {
        $errmsg = trim(strval($errmsg));
        $errcode = trim(strval($errcode));
        // 优先按腾讯云 result 错误码匹配
        $codeMap = array(
            '-2'   => '服务器无法连接腾讯云短信接口，请检查服务器外网访问、DNS、防火墙是否放行 443 端口，以及 PHP curl/openssl 扩展',
            '-1'   => '腾讯云短信接口返回异常状态，请稍后重试',
            '1001' => 'App Key 验证失败，请检查【平台appkey】是否填写正确',
            '1002' => '短信内容包含敏感词，请修改模板内容或变量',
            '1004' => '手机号码格式错误，请检查接收号码',
            '1008' => '请求腾讯云接口超时，请稍后重试',
            '1011' => '短信模板不存在或未审核通过，请检查模板ID',
            '1014' => '短信内容与模板不匹配（变量个数/格式不一致），请检查模板内容与变量',
            '1016' => '手机号码格式错误，请检查接收号码',
            '1024' => '同一手机号 30 秒内发送次数超限，请稍后再试',
            '1025' => '同一手机号当日发送次数超限，请明天再试或更换号码测试',
            '1026' => '同一手机号短时间内发送相同内容次数超限，请稍后再试',
            '1029' => '签名异常，请检查短信签名是否已审核通过且填写一致',
            '1033' => '腾讯云短信套餐包余量不足或已过期，请充值后重试',
        );
        if ('' !== $errcode && isset($codeMap[$errcode])) {
            return $codeMap[$errcode] . '（错误码：' . $errcode . ('' !== $errmsg ? '，原始信息：' . htmlspecialchars($errmsg, ENT_QUOTES, 'UTF-8') : '') . '）';
        }
        // 再按英文错误信息关键词匹配（注意顺序：具体的在前，泛的在后）
        $map = array(
            'sdkappid'                 => 'SDK AppID 不正确或已停用，请检查【平台appid】是否填写正确',
            'appid'                    => 'SDK AppID 不正确，请检查【平台appid】是否填写正确',
            'sign'                     => '短信签名不合法或未审核通过，请检查签名设置',
            'sig'                      => 'App Key 验证失败，请检查【平台appkey】是否填写正确',
            'sensitive'                => '短信内容包含敏感词，请修改模板内容或变量',
            'template'                 => '短信模板不存在、未审核通过或变量不匹配，请检查模板ID与模板内容',
            'nationcode'               => '国际区号不正确，请检查短信设置中的国际区号（国内为 86）',
            'tel format error'         => '手机号码格式错误，请检查接收号码',
            'phone'                    => '手机号码异常，请检查接收号码及国际区号',
            'mobile'                   => '手机号码异常，请检查接收号码及国际区号',
            'fee not enough'           => '腾讯云短信套餐包余量不足，请充值后重试',
            'package'                  => '腾讯云短信套餐包余量不足或已过期，请充值后重试',
            'balance'                  => '腾讯云账户余额不足，请充值后重试',
            'frequency'                => '发送频率超限，请稍后再试',
            'limit'                    => '发送次数或频率超限，请稍后再试',
            'iplimit'                  => '当前服务器 IP 被腾讯云限制，请到腾讯云控制台检查 IP 白名单设置',
            'ip limit'                 => '当前服务器 IP 被腾讯云限制，请到腾讯云控制台检查 IP 白名单设置',
            'not exist'                => '相关配置不存在，请检查【平台appid】、模板ID和签名是否正确',
            'not approved'             => '签名或模板尚未审核通过，请到腾讯云控制台查看审核状态',
            'timeout'                  => '请求腾讯云接口超时，请稍后重试',
            'param'                    => '接口参数错误，请检查模板变量与模板内容是否匹配',
        );
        foreach ($map as $key => $val) {
            if ('' !== $errmsg && stripos($errmsg, $key) !== false) {
                return $val . '（' . ('' !== $errcode ? '错误码：' . $errcode . '，' : '') . '原始信息：' . htmlspecialchars($errmsg, ENT_QUOTES, 'UTF-8') . '）';
            }
        }
        // 网络/环境类错误以及其他英文信息，走通用转换
        $tip = $this->translateSmsError($errmsg, '腾讯云短信');
        if ('' !== $errcode && false === strpos($tip, '错误码：')) {
            $tip .= '（错误码：' . $errcode . '）';
        }
        return $tip;
    }
} 
