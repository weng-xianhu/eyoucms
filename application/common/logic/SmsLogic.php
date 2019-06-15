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

/**
 * Description of SmsLogic
 *
 * 短信类
 */
class SmsLogic 
{
    private $config;
    
    public function __construct() 
    {
        $this->config = tpCache('sms') ?: [];
    }

    /**
     * 发送短信逻辑
     * @param unknown $scene
     */
    public function sendSms($scene, $sender, $params, $unique_id=0)
    {
        $smsTemp = M('sms_template')->where("send_scene", $scene)->find();    //用户注册.
        $code = !empty($params['code']) ? $params['code'] : false;
        $content = !empty($params['content']) ? $params['content'] : false;
        if(empty($unique_id)){
            $session_id = session_id();
        }else{
            $session_id = $unique_id;
        }
        $product = $this->config['sms_product'];

        $smsParams = array(
            1 => "{\"code\":\"$code\",\"product\":\"$product\"}", //1. 用户注册
            2 => "{\"code\":\"$code\"}", //2. 用户找回密码
            3 => "{\"code\":\"$code\"}", //3. 
            4 => "{\"content\":\"$content\"}", //4. 
        );

        $smsParam = $smsParams[$scene];

        //提取发送短信内容
        $scenes = config('SEND_SCENE');
        $msg = $scenes[$scene][1];
        $params_arr = json_decode($smsParam);
        foreach ($params_arr as $k => $v) {
            $msg = str_replace('${' . $k . '}', $v, $msg);
        }

        //发送记录存储数据库
        $log_id = M('sms_log')->insertGetId(array('mobile' => $sender, 'code' => $code, 'add_time' => time(), 'session_id' => $session_id, 'status' => 0, 'scene' => $scene, 'msg' => $msg));
        if ($sender != '' && check_mobile($sender)) {//如果是正常的手机号码才发送
            try {
                $resp = $this->realSendSms($sender, $smsTemp['sms_sign'], $smsParam, $smsTemp['sms_tpl_code']);
            } catch (\Exception $e) {
                $resp = ['status' => -1, 'msg' => $e->getMessage()];
            }
            if ($resp['status'] == 1) {
                M('sms_log')->where(array('id' => $log_id))->save(array('status' => 1)); //修改发送状态为成功
            }else{
                M('sms_log')->where(array('id' => $log_id))->update(array('error_msg'=>$resp['msg'])); //发送失败, 将发送失败信息保存数据库
            }
            return $resp;
        }else{
           return $result = ['status' => -1, 'msg' => '接收手机号不正确['.$sender.']'];
        }
        
    }

    private function realSendSms($mobile, $smsSign, $smsParam, $templateCode)
    {
        if (config('sms_debug') == true) {
            return array('status' => 1, 'msg' => '专用于越过短信发送');
        }
        
        $type = (int)$this->config['sms_platform'] ?: 1;
        switch($type) {
            case 1:
                $result = $this->sendSmsByAliyun($mobile, $smsSign, $smsParam, $templateCode);
                break;
            case 2:
                $result = $this->sendSmsByAlidayu($mobile, $smsSign, $smsParam, $templateCode);
                break;
            default:
                $result = ['status' => -1, 'msg' => '不支持的短信平台'];
        }
        
        return $result;
    }
    
    /**
     * 发送短信（阿里大于）
     * @param $mobile  手机号码
     * @param $code    验证码
     * @return bool    短信发送成功返回true失败返回false
     */
    private function sendSmsByAlidayu($mobile, $smsSign, $smsParam, $templateCode)
    {
        //时区设置：亚洲/上海
        date_default_timezone_set('Asia/Shanghai');
        //这个是你下面实例化的类
        vendor('Alidayu.TopClient');
        //这个是topClient 里面需要实例化一个类所以我们也要加载 不然会报错
        vendor('Alidayu.ResultSet');
        //这个是成功后返回的信息文件
        vendor('Alidayu.RequestCheckUtil');
        //这个是错误信息返回的一个php文件
        vendor('Alidayu.TopLogger');
        //这个也是你下面示例的类
        vendor('Alidayu.AlibabaAliqinFcSmsNumSendRequest');

        $c = new \TopClient;
        //App Key的值 这个在开发者控制台的应用管理点击你添加过的应用就有了
        $c->appkey = $this->config['sms_appkey'];
        //App Secret的值也是在哪里一起的 你点击查看就有了
        $c->secretKey = $this->config['sms_secretKey'];
        //这个是用户名记录那个用户操作
        $req = new \AlibabaAliqinFcSmsNumSendRequest;
        //代理人编号 可选
        $req->setExtend("123456");
        //短信类型 此处默认 不用修改
        $req->setSmsType("normal");
        //短信签名 必须
        $req->setSmsFreeSignName($smsSign);
        //短信模板 必须
        $req->setSmsParam($smsParam);
        //短信接收号码 支持单个或多个手机号码，传入号码为11位手机号码，不能加0或+86。群发短信需传入多个号码，以英文逗号分隔，
        $req->setRecNum("$mobile");
        //短信模板ID，传入的模板必须是在短信平台“管理中心-短信模板管理”中的可用模板。
        $req->setSmsTemplateCode($templateCode); // templateCode

        $c->format = 'json';

        //发送短信
        $resp = $c->execute($req);
        //短信发送成功返回True，失败返回false
        if ($resp && $resp->result) {
            return array('status' => 1, 'msg' => $resp->sub_msg);
        } else {
            return array('status' => -1, 'msg' => $resp->msg . ' ,sub_msg :' . $resp->sub_msg . ' subcode:' . $resp->sub_code);
        }
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
        $accessKeySecret = $this->config['sms_secretKey'];
        
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
        $resp = $acsClient->getAcsResponse($request);
        
        //短信发送成功返回True，失败返回false
        if ($resp && $resp->Code == 'OK') {
            return array('status' => 1, 'msg' => $resp->Code);
        } else {
            return array('status' => -1, 'msg' => $resp->Message . '. Code: ' . $resp->Code);
        }
    }
}
