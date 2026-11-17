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

use PHPMailer\PHPMailer\SMTP;

/**
 * 兼容禁用了 stream_socket_client 的服务器
 *
 * PHPMailer 在 stream_socket_client 不可用时会降级使用 fsockopen，
 * 而 fsockopen 无法传入 stream context，导致 STARTTLS 时证书校验拿不到 CA 证书。
 * 这里在 STARTTLS 握手前把 SSL 选项（cafile / verify_peer 等）挂到已建立的连接流上。
 */
class PHPMailerSmtp extends SMTP
{
    /**
     * SSL 上下文选项
     * @var array
     */
    private $sslOptions = array();

    /**
     * @param array $sslOptions 形如 ['cafile' => '...', 'verify_peer' => true]
     */
    public function __construct($sslOptions = array())
    {
        $this->sslOptions = is_array($sslOptions) ? $sslOptions : array();
    }

    /**
     * 开始 TLS 加密会话（覆盖父类，握手前挂载 SSL 选项）
     * @return bool
     */
    public function startTLS()
    {
        if (is_resource($this->smtp_conn) && !empty($this->sslOptions)) {
            foreach ($this->sslOptions as $key => $value) {
                @stream_context_set_option($this->smtp_conn, 'ssl', $key, $value);
            }
        }

        return parent::startTLS();
    }
}
