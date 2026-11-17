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

use think\Db;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Description of SmsLogic
 *
 * 邮件类
 */
class EmailLogic 
{
    private $config;
    private $home_lang;
    
    public function __construct($smtp_config = []) 
    {
        $this->config = !empty($smtp_config) ? $smtp_config : tpCache('smtp');
        $this->home_lang = get_home_lang();
    }

    /**
     * 置换邮件模版内容
     * @param intval $scene 应用场景
     */
    private function replaceContent($scene = '', $params = '')
    {
        if (0 == intval($scene)) {
            $msg = $params;
        } else {
            $params_arr = [];
            $emailTemp = Db::name('smtp_tpl')->where([
                    'send_scene'=> $scene,
                    'lang'      => $this->home_lang,
                ])->find();
            if (!empty($emailTemp)) {
                $msg = $emailTemp['tpl_content'];
                preg_match_all('/\${([^\}]+)}/i', $msg, $matchs);
                if (!empty($matchs[1])) {
                    foreach ($matchs[1] as $key => $val) {
                        if (is_array($params)) {
                            $params_arr[$val] = $params[$val];
                        } else {
                            $params_arr[$val] = $params;
                        }
                    }
                }

                //置换邮件模版内容
                foreach ($params_arr as $k => $v) {
                    $msg = str_replace('${' . $k . '}', $v, $msg);
                }
            } else {
                return '';
            }
        }

        return $msg;
    }

    /**
     * 邮件发送
     * @param $to    接收人
     * @param string $subject   邮件标题
     * @param string|array $data   邮件内容(html模板渲染后的内容)
     * @param string $scene   使用场景
     * @throws Exception
     */
    public function send_email($to='', $subject='', $data='', $scene=1, $library = 'phpmailer'){
        // 只保留最近1小时的邮件日志
        try {
            $mtime = strtotime("-1 hour");
            Db::name('smtp_record')->where([
                'add_time'  => ['lt', $mtime],
                ])->delete();
        } catch (\Exception $e) {}

        if (0 < intval($scene)) {
            $smtp_tpl_row = Db::name('smtp_tpl')->where([
                    'send_scene'=> $scene,
                    'lang'      => $this->home_lang,
                ])->find();
            if (empty($smtp_tpl_row)) {
                return ['code'=>0,'msg'=>'找不到相关邮件模板！'];
            } else if (empty($smtp_tpl_row['is_open'])) {
                return ['code'=>0,'msg'=>'该功能待开放，请先启用邮件模板('.$smtp_tpl_row['tpl_name'].')'];
            } else {
                empty($subject) && $subject = $smtp_tpl_row['tpl_title'];
                $subject = htmlspecialchars_decode($subject);
            }
        }

        switch ($library) {
            case 'phpmailer':
                return $this->send_phpmailer($to, $subject, $data, $scene);
                break;

            case 'swiftmailer':
                return $this->send_swiftmailer($to, $subject, $data, $scene);
                break;
            
            default:
                return $this->send_phpmailer($to, $subject, $data, $scene);
                break;
        }
    }

    /**
     * 邮件发送 - swiftmailer第三方库
     * @param $to    接收人
     * @param string $subject   邮件标题
     * @param string|array $data   邮件内容(html模板渲染后的内容)
     * @param string $scene   使用场景
     * @throws Exception
     */
    private function send_swiftmailer($to='', $subject='', $data='', $scene=1){
        vendor('swiftmailer.lib.swift_required');
        // require_once 'vendor/swiftmailer/lib/swift_required.php';

        // 服务器禁用了 socket 相关函数时直接返回明确提示，避免致命错误
        $env_msg = $this->checkSmtpSocketEnv();
        if ('' !== $env_msg) {
            return array('code'=>0 , 'msg'=>$env_msg);
        }

        try {
            //判断openssl是否开启
            $openssl_funcs = get_extension_funcs('openssl');
            if(!$openssl_funcs){
                return array('code'=>0 , 'msg'=>'请先开启php的openssl扩展');
            }

            //判断openssl是否开启
            // $sockets_funcs = get_extension_funcs('sockets');
            // if(!$sockets_funcs){
            //     return array('code'=>0 , 'msg'=>'请先开启php的sockets扩展');
            // }
        
            empty($to) && $to = $this->config['smtp_from_eamil'];
            $to = explode(',', $to);

            //smtp服务器
            $host = $this->config['smtp_server'];
            //端口 - likely to be 25, 465 or 587
            $port = intval($this->config['smtp_port']);
            //用户名
            $user = $this->config['smtp_user'];
            //密码
            $pwd = $this->config['smtp_pwd']; 
            //发送者
            $from = $this->config['smtp_user'];
            //发送者名称
            $from_name = $user;//tpCache('web.web_name');
            // 使用安全协议
            $encryption_type = null;
            switch ($port) {
                case 465:
                    $encryption_type = 'ssl';
                    break;
                
                case 587:
                    $encryption_type = 'tls';
                    break;

                default:
                    # code...
                    break;
            }
            //HTML内容转换
            $body = $this->replaceContent($scene, $data);

            // Create the Transport
            $transport = (new \Swift_SmtpTransport($host, $port, $encryption_type))
                ->setUsername($user)
                ->setPassword($pwd);

            // Create the Mailer using your created Transport
            $mailer = new \Swift_Mailer($transport);

            // Create a message
            $message = (new \Swift_Message($subject))
                ->setFrom([$from=>$from_name])
                // ->setTo([$to, '第二个接收者邮箱' => '别名'])
                ->setTo($to)
                ->setContentType("text/html")
                ->setBody($body);

            // Send the message
            $result = $mailer->send($message);
            if (!$result) {
                return array('code'=>0 , 'msg'=>'发送失败');
            } else {
                return array('code'=>1 , 'msg'=>'发送成功');
            }
        } catch (\Exception $e) {
            return $this->buildMailFailResult($e);
        } catch (\Throwable $e) {
            // PHP 7+ 中被禁用函数抛出的 Error，统一转为中文提示
            return $this->buildMailFailResult($e);
        }
    }

    /**
     * 检查服务器是否具备建立 SMTP 连接的能力
     * 部分空间商会在 php.ini 的 disable_functions 中禁用 fsockopen / stream_socket_client
     * @return string 不可用时返回提示信息，可用时返回空字符串
     */
    private function checkSmtpSocketEnv()
    {
        if (function_exists('stream_socket_client') || function_exists('fsockopen')) {
            return '';
        }

        return '服务器（PHP ' . PHP_VERSION . '）已禁用 fsockopen 与 stream_socket_client 函数，无法建立 SMTP 连接：请在 php.ini 的 disable_functions 中移除这两个函数，重启 PHP 后重试';
    }

    /**
     * 统一构造邮件发送失败返回值
     * 兼容 PHP 7+ 的 Error（如调用被禁用函数产生的致命错误），避免致命错误直接输出到页面
     * @param \Exception|\Throwable $e
     * @return array
     */
    private function buildMailFailResult($e)
    {
        $emsg = method_exists($e, 'errorMessage') ? $e->errorMessage() : $e->getMessage();
        $port = isset($this->config['smtp_port']) ? intval($this->config['smtp_port']) : 0;

        return array('code' => 0, 'msg' => '发送失败：' . $this->translateMailError($emsg, $port));
    }

    /**
     * 将邮件发送的英文错误信息转换为易懂的中文提示
     * @param string $msg 原始错误信息（PHPMailer/SwiftMailer 或 SMTP 服务器返回）
     * @param int $port SMTP端口
     * @param bool $withRaw 是否在提示后附带原始错误信息，便于排查
     * @return string 中文提示
     */
    private function translateMailError($msg, $port = 0, $withRaw = true)
    {
        $msg = trim(strval($msg));
        if ('' === $msg) {
            return '邮件发送失败，服务器未返回错误信息，请检查SMTP配置是否正确';
        }
        // 已转换过的信息不再重复处理
        if (false !== strpos($msg, '原始错误信息')) {
            return $msg;
        }
        $port = intval($port);
        $raw  = trim(strip_tags($msg));
        // 去掉 PHPMailer 附带的帮助链接，避免干扰匹配
        $raw  = trim(preg_replace('/\s*https?:\/\/\S+/i', '', $raw));
        $raw  = preg_replace('/\s+/', ' ', $raw);

        if (465 == $port) {
            $connectTip = '无法连接邮件服务器：<br/>1.请检查SMTP服务器地址和端口是否正确<br/>2.服务器防火墙/安全组是否放行出站 465 端口<br/>3.空间是否开启函数 fsockopen / stream_socket_client<br/>4.部分机房屏蔽 465 端口，可改用 587 端口（TLS）后重试';
        } else {
            $connectTip = '无法连接邮件服务器：<br/>1.请检查SMTP服务器地址和端口是否正确，推荐使用 465 端口（SSL），其次 25 端口<br/>2.服务器防火墙是否放行该端口<br/>3.空间是否开启函数 fsockopen / stream_socket_client';
        }

        // 按优先级排列的匹配规则：[正则, 中文提示]
        $rules = [
            // 域名解析失败
            ['/getaddrinfo|php_network_getaddresses|name or service not known|could not resolve|no such host|host not found|invalid host|无效的主机/i',
                'SMTP服务器地址无法解析，请检查“SMTP服务器”填写是否正确（如：smtp.qq.com、smtp.163.com），不要带 http:// 前缀'],
            // 端口要求加密连接
            ['/need EHLO and AUTH first|must issue a STARTTLS|STARTTLS command first|secure connection is requi|encryption required|TLS required/i',
                '该端口要求加密连接，请将SMTP端口改为 465（SSL）或 587（TLS）后重试'],
            // SSL/TLS 握手失败
            ['/\bssl\b|\btls\b|certificate|crypto|handshake|stream_socket_enable_crypto/i',
                'SSL/TLS 加密连接失败：<br/>1.请确认端口与加密方式匹配（465=SSL、587=TLS、25=不加密）<br/>2.请确认PHP已开启 openssl 扩展<br/>3.请确认服务器时间准确（时间偏差过大会导致证书校验失败）<br/>4.若仍失败请联系空间商更新CA根证书'],
            // 连接失败
            ['/smtp connect\(\) failed|could not connect to smtp host|connection refused|connection timed out|timed out|failed to connect|network is unreachable|no route to host|unable to connect|connection reset|SMTP服务器连接失败|无法连接到 SMTP 主机/i',
                $connectTip],
            // 需要先登录
            ['/authentication is required|authentication required|auth first|(?<!\d)530(?!\d)/i',
                '邮件服务器要求先进行身份验证，请填写正确的发件邮箱账号和密码（授权码）'],
            // 账号或密码错误
            ['/could not authenticate|authentication fail|auth(entication)? (login|plain|error)|password not accepted|login fail|invalid login|bad credentials|credentials invalid|username and password not accepted|incorrect authentication|登录失败|(?<!\d)535(?!\d)/i',
                '邮箱账号或密码验证失败：<br/>1.请检查发件邮箱账号是否填写正确<br/>2.QQ/163/126/企业邮箱等需填写“SMTP授权码”，不是邮箱登录密码<br/>3.请确认邮箱已开启 SMTP 服务'],
            // 发件人与登录账号不一致
            ['/mail from must equal authorized user|sender address rejected|sender not allowed|sender verify failed|from address failed|sender mismatch|发送地址错误|(?<!\d)553(?!\d)/i',
                '发件人地址被拒绝：发件人邮箱必须与SMTP登录账号一致，请检查“发件人邮箱”与“SMTP账号”是否相同'],
            // 收件人无效
            ['/recipients failed|recipient address rejected|recipient rejected|user unknown|no such user|mailbox unavailable|mailbox not found|does not exist|unrouteable address|relay access denied|relaying denied|not permitted to relay|收件人地址错误|(?<!\d)55[01](?!\d)/i',
                '收件人地址无效或被邮件服务器拒收，请检查“管理员邮箱”是否填写正确'],
            // 地址格式错误
            ['/invalid address|provide at least one recipient|invalid (email|mailbox)|address syntax|电子邮箱地址是无效的|必须提供至少一个收件人/i',
                '邮箱地址格式错误，请检查发件人/收件人邮箱是否填写正确（多个收件人请用英文逗号分隔）'],
            // 正文为空
            ['/message body empty|邮件正文为空/i',
                '邮件内容为空，请检查邮件模板内容是否已填写'],
            // 邮件过大
            ['/message size|too large|size exceeds|exceeds size/i',
                '邮件内容过大，超出服务器允许的大小限制，请精简邮件内容'],
            // 被判定为垃圾邮件 / 内容被拒
            ['/data not accepted|DT:SPM|spam|junk|content rejected|rejected by content|blacklist|blocked|policy rejection|数据不被接受|(?<!\d)554(?!\d)/i',
                '邮件被服务器判定为垃圾邮件或内容被拒收：<br/>1.请稍后再试，或修改邮件标题/内容<br/>2.发件邮箱刚开通SMTP时可能受限，建议先给自己发送几封邮件<br/>3.163/126 等邮箱请勿短时间内频繁发送'],
            // 发送频率 / 配额
            ['/quota|too many|rate limit|exceeded|frequency|too frequently|try again later|temporarily|(?<!\d)4(21|50|51|52)(?!\d)/i',
                '发送过于频繁或已超出邮箱当日发送配额，请稍后再试或更换发件邮箱'],
            // 服务器环境问题
            ['/could not instantiate mail function|extension missing|fsockopen|stream_socket_client|disabled for security reasons|has been disabled|call to undefined function|缺少扩展名|未知函数调用/i',
                '服务器环境不支持邮件发送：请确认PHP已开启 openssl 扩展，且 fsockopen / stream_socket_client 函数未被禁用，如无法处理请联系空间商'],
        ];

        $tip = '';
        foreach ($rules as $rule) {
            if (preg_match($rule[0], $raw)) {
                $tip = $rule[1];
                break;
            }
        }
        // 连接类错误：补一次 TCP 连通性探测，给出更具体的排查方向
        if ($connectTip === $tip) {
            $probe = $this->probeSmtpConnect();
            if ('' !== $probe) {
                $tip .= '<br/>' . $probe;
            }
        }
        if ('' === $tip) {
            // 原始信息已是中文提示，直接返回
            if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $raw)) {
                return $msg;
            }
            $tip = '邮件服务器返回了未知错误，请核对SMTP服务器、端口、账号、密码（授权码）是否正确';
        }

        if ($withRaw && '' !== $raw) {
            if (function_exists('mb_strlen') && mb_strlen($raw, 'UTF-8') > 200) {
                $raw = mb_substr($raw, 0, 200, 'UTF-8') . '...';
            }
            $tip .= '<br/><span style="color:#999;font-size:12px;">原始错误信息：' . htmlspecialchars($raw, ENT_QUOTES, 'UTF-8') . '</span>';
        }

        return $tip;
    }

    /**
     * 探测 SMTP 服务器的 TCP 连通性（仅用于连接失败时补充排查信息）
     * @param int $timeout 超时秒数
     * @return string 探测结论，配置缺失时返回空字符串
     */
    private function probeSmtpConnect($timeout = 5)
    {
        $host = isset($this->config['smtp_server']) ? trim(strval($this->config['smtp_server'])) : '';
        $port = isset($this->config['smtp_port']) ? intval($this->config['smtp_port']) : 0;
        if ('' === $host || $port <= 0) {
            return '';
        }

        $errno = 0;
        $errstr = '';
        if (function_exists('stream_socket_client')) {
            $fp = @stream_socket_client('tcp://' . $host . ':' . $port, $errno, $errstr, $timeout);
        } else if (function_exists('fsockopen')) {
            $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
        } else {
            return '';
        }

        if (!is_resource($fp)) {
            return '连接探测：无法连通 ' . $host . ':' . $port . '（' . ('' !== $errstr ? $errstr : '无返回信息') . '，错误码 ' . intval($errno) . '），通常是防火墙/安全组拦截出站端口或DNS解析失败';
        }
        fclose($fp);

        $msg = '连接探测：TCP 可达 ' . $host . ':' . $port . '，出站网络未被拦截';
        $msg .= is_file($this->getCaFile()) ? '；内置CA证书包已找到' : '；未找到 extend/cacert.pem，证书校验将依赖系统CA';

        // 465 为隐式SSL；stream_socket_client 被禁用时 fsockopen 无法加载内置CA，握手必然失败
        if (465 == $port) {
            if (!function_exists('stream_socket_client')) {
                $msg .= '；服务器已禁用 stream_socket_client，465 端口在降级模式下无法加载内置CA证书包，请将SMTP端口改为 587（TLS）后重试';
            } else {
                $tls_errno = 0;
                $tls_errstr = '';
                $tls_warn = '';
                set_error_handler(function ($no, $str) use (&$tls_warn) {
                    $tls_warn .= $str;
                    return true;
                });
                $tls = stream_socket_client(
                    'ssl://' . $host . ':' . $port,
                    $tls_errno,
                    $tls_errstr,
                    $timeout,
                    STREAM_CLIENT_CONNECT,
                    stream_context_create($this->getSmtpSslOptions())
                );
                restore_error_handler();

                if (is_resource($tls)) {
                    fclose($tls);
                    $msg .= '；TLS握手成功，请检查SMTP账号密码（授权码）是否正确';
                } else {
                    $reason = '' !== $tls_errstr ? $tls_errstr : trim($tls_warn);
                    $msg .= '；TLS握手失败：' . ('' !== $reason ? $reason : '未知原因（错误码 ' . intval($tls_errno) . '）');
                }
            }
        }

        return $msg;
    }

    /**
     * 内置 CA 证书包路径
     * @return string
     */
    private function getCaFile()
    {
        return dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'extend' . DIRECTORY_SEPARATOR . 'cacert.pem';
    }

    /**
     * 构造 SMTP 的 SSL 上下文选项
     * 部分虚拟主机未配置 CA 根证书，会导致 stream_socket_enable_crypto 证书校验失败，
     * 此时使用项目内置的 CA 证书包（extend/cacert.pem）完成校验
     * @return array
     */
    private function getSmtpSslOptions()
    {
        $options = array(
            'ssl' => array(
                'verify_peer'      => true,
                'verify_peer_name' => true,
            ),
        );

        $cafile = $this->getCaFile();
        if (is_file($cafile)) {
            $options['ssl']['cafile'] = $cafile;
        }

        return $options;
    }

    /**
     * 邮件发送 - 第三方库phpmailer
     * @param $to    接收人
     * @param string $subject   邮件标题
     * @param string|array $data   邮件内容(html模板渲染后的内容)
     * @param string $scene   使用场景
     * @throws Exception
     */
    private function send_phpmailer($to='', $subject='', $data='', $scene=1){
        // 服务器禁用了 socket 相关函数时直接返回明确提示，避免致命错误
        $env_msg = $this->checkSmtpSocketEnv();
        if ('' !== $env_msg) {
            return array('code'=>0 , 'msg'=>$env_msg);
        }

        //判断openssl是否开启
        $openssl_funcs = get_extension_funcs('openssl');
        if(!$openssl_funcs){
            return array('code'=>0 , 'msg'=>'请先开启php的openssl扩展');
        }

        //判断openssl是否开启
        // $sockets_funcs = get_extension_funcs('sockets');
        // if(!$sockets_funcs){
        //     return array('code'=>0 , 'msg'=>'请先开启php的sockets扩展');
        // }

        if (version_compare(PHP_VERSION, '5.5.0', '<')) {
            return $this->phpmailer_5_4_0($to, $subject, $data, $scene);
        } else {
            return $this->phpmailer_5_5_0($to, $subject, $data, $scene);
        }
    }

    /**
     * php版本5.5或以上
     */
    private function phpmailer_5_5_0($to='', $subject='', $data='', $scene=1){
        try {
            $mail = new PHPMailer(true);
            //Enable SMTP debugging
            // 0 = off (for production use)
            // 1 = client messages
            // 2 = client and server messages
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                         //Enable verbose debug output
            $mail->CharSet  = PHPMailer::CHARSET_UTF8;                  //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
            $mail->setLanguage('zh_cn');                                //错误提示使用中文
            $mail->isSMTP();                                            //Send using SMTP
            $sslOptions = $this->getSmtpSslOptions();                   //SSL证书校验（缺CA根证书时用内置证书包）
            $mail->SMTPOptions = $sslOptions;
            // 服务器禁用 stream_socket_client 时 PHPMailer 会降级 fsockopen，
            // 用自定义 SMTP 实例把 SSL 选项挂到连接流上，保证 587（STARTTLS）能完成证书校验
            if (!function_exists('stream_socket_client')) {
                $mail->setSMTPInstance(new PHPMailerSmtp(isset($sslOptions['ssl']) ? $sslOptions['ssl'] : array()));
            }
            $mail->Host       = $this->config['smtp_server'];           //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = $this->config['smtp_user'];             //SMTP username
            $mail->Password   = $this->config['smtp_pwd'];              //SMTP password
            $mail->Port       = intval($this->config['smtp_port']);     //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            // 使用安全协议
            switch ($mail->Port) {
                case 465:
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    break;
                
                case 587:
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    break;

                default:
                    break;
            }

            //Set who the message is to be sent from
            $mail->setFrom($this->config['smtp_user']);
            //接收者邮件
            empty($to) && $to = $this->config['smtp_from_eamil'];
            $to = explode(',', $to);
            //接收邮件方
            if(is_array($to)){
                foreach ($to as $v){
                    $mail->addAddress($v);
                }
            }else{
                $mail->addAddress($to);
            }
            //回复地址
            // $mail->addReplyTo('info@example.com', 'Information');
            // $mail->addCC('cc@example.com');
            // $mail->addBCC('bcc@example.com');
        
            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
        
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            //标题
            $mail->Subject = $subject;
            //HTML内容转换
            $content = $this->replaceContent($scene, $data);
            $mail->Body    = $content;
            //Replace the plain text body with one created manually
            // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
            //send the message, check for errors
            $result = $mail->send();
            if (!$result) {
                $msg = $this->translateMailError($mail->ErrorInfo, $mail->Port);
                return array('code'=>0 , 'msg'=>'发送失败：'.$msg);
            } else {
                return array('code'=>1 , 'msg'=>'发送成功');
            }
        } catch (\Exception $e) {
            return $this->buildMailFailResult($e);
        } catch (\Throwable $e) {
            // PHP 7+ 中被禁用函数抛出的 Error，统一转为中文提示
            return $this->buildMailFailResult($e);
        }
    }

    /**
     * php5.4版本或以下
     */
    private function phpmailer_5_4_0($to='', $subject='', $data='', $scene=1){
        vendor('phpmailer.PHPMailerAutoload');
        try {
            $mail = new \PHPMailer;
            //调试输出格式
            //$mail->Debugoutput = 'html';
            //Enable SMTP debugging
            // 0 = off (for production use)
            // 1 = client messages
            // 2 = client and server messages
            $mail->SMTPDebug = 0;
            $mail->CharSet  = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
            $mail->setLanguage('zh_cn'); //错误提示使用中文
            $mail->isSMTP();
            $mail->Host = $this->config['smtp_server'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['smtp_user'];
            $mail->Password = $this->config['smtp_pwd'];
            $mail->Port = intval($this->config['smtp_port']);
            // 使用安全协议
            switch ($mail->Port) {
                case 465:
                    $mail->SMTPSecure = 'ssl';
                    break;
                
                case 587:
                    $mail->SMTPSecure = 'tls';
                    break;

                default:
                    break;
            }

            //Set who the message is to be sent from
            $mail->setFrom($this->config['smtp_user']);
            //接收者邮件
            empty($to) && $to = $this->config['smtp_from_eamil'];
            $to = explode(',', $to);
            //接收邮件方
            if(is_array($to)){
                foreach ($to as $v){
                    $mail->addAddress($v);
                }
            }else{
                $mail->addAddress($to);
            }
            //回复地址
            // $mail->addReplyTo('info@example.com', 'Information');
            //Attachments
            //$mail->addAttachment('images/phpmailer_mini.png');
        
            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            //标题
            $mail->Subject = $subject;
            //HTML内容转换
            $content = $this->replaceContent($scene, $data);
            $mail->msgHTML($content);
            //Replace the plain text body with one created manually
            // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
            //send the message, check for errors
            $result = $mail->send();
            if (!$result) {
                $msg = $this->translateMailError($mail->ErrorInfo, $mail->Port);
                return array('code'=>0 , 'msg'=>'发送失败：'.$msg);
            } else {
                return array('code'=>1 , 'msg'=>'发送成功');
            }
        } catch (\Exception $e) {
            return $this->buildMailFailResult($e);
        } catch (\Throwable $e) {
            // PHP 7+ 中被禁用函数抛出的 Error，统一转为中文提示
            return $this->buildMailFailResult($e);
        }
    }
}
