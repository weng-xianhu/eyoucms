<?php
/**
 * AiService 统一异常
 * code 常量可判定，插件按 code 降级
 */

namespace app\common\util;

class AiServiceException extends \Exception
{
    const NOT_CONFIGURED  = 1001; // 能力未配置可用模型/服务商
    const RATE_LIMITED    = 1002; // IP 限流
    const BUDGET_EXCEEDED = 1003; // 每日预算熔断
    const PROVIDER_ERROR  = 1004; // 服务商接口报错
    const TIMEOUT         = 1005; // 请求超时
    const BAD_PARAM       = 1006; // 调用参数错误

    /** 服务商返回的原始信息（PROVIDER_ERROR 时） */
    protected $providerRaw = '';

    public function __construct($message, $code, $providerRaw = '')
    {
        parent::__construct($message, $code);
        $this->providerRaw = is_string($providerRaw) ? $providerRaw : json_encode($providerRaw, JSON_UNESCAPED_UNICODE);
    }

    public function getProviderRaw()
    {
        return $this->providerRaw;
    }
}
