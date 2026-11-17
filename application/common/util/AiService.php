<?php
/**
 * EyouCMS 统一 AI 模型接入层（AI 配置中心 SDK）
 *
 * 定位：核心只做「服务商/模型配置 + 调用」，不含任何具体 AI 功能。
 * 能力维度：chat（生文，P1）/ image / video / embed（P2+ 预留）。
 * 兼容 PHP 5.6+，纯 curl，无扩展依赖（openssl 用于 Key 加密，PHP5.6 默认自带）。
 *
 * 用法：
 *   $answer = AiService::chat($messages, ['scene' => 'aihelper']);
 *   AiService::chatStream($messages, function ($delta) { echo $delta; }, ['scene' => 'aihelper']);
 *   if (AiService::hasCapability('chat')) { ... }
 * 异常：AiServiceException，按 getCode() 区分 NOT_CONFIGURED / RATE_LIMITED /
 *       BUDGET_EXCEEDED / PROVIDER_ERROR / TIMEOUT / BAD_PARAM。
 */

namespace app\common\util;

use think\Db;

class AiService
{
    const VERSION = '1.2.0';

    /** 支持的能力枚举（预留 tts/asr） */
    public static $capabilities = ['chat', 'image', 'video', 'embed'];

    /** 服务商预设模板：base_url + 常用模型（后台下拉带出，避免手抄文档） */
    public static function presets()
    {
        return [
            'deepseek' => [
                'name' => 'DeepSeek',
                'base_url' => 'https://api.deepseek.com',
                'chat_path' => '/chat/completions',
                'image_path' => '/images/generations',
                'docs_url' => 'https://api-docs.deepseek.com/zh-cn/',
                'models' => ['chat' => ['deepseek-v4-flash', 'deepseek-v4-pro'], 'image' => []],
                'notes' => ['image' => 'DeepSeek 暂未提供生图模型，请改用豆包或智谱服务商'],
            ],
            'doubao' => [
                'name' => '豆包（火山方舟）',
                'base_url' => 'https://ark.cn-beijing.volces.com/api/v3',
                'chat_path' => '/chat/completions',
                'image_path' => '/images/generations',
                'docs_url' => 'https://www.volcengine.com/docs/82379/1330310',
                'models' => ['chat' => ['doubao-seed-1-6-250615', 'doubao-1-5-pro-32k-250115'], 'image' => ['doubao-seedream-3-0-t2i-250415']],
            ],
            'qwen' => [
                'name' => '通义千问（阿里云百炼）',
                'base_url' => 'https://dashscope.aliyuncs.com/compatible-mode/v1',
                'chat_path' => '/chat/completions',
                'image_path' => '/images/generations',
                'docs_url' => 'https://help.aliyun.com/zh/model-studio/models',
                'models' => ['chat' => ['qwen3.7-plus', 'qwen3.8-flash', 'qwen3.8-max'], 'image' => []],
                'notes' => ['image' => '通义的生图（qwen-image/万相）仅提供阿里云原生接口，不兼容 OpenAI 格式，请改用豆包或智谱服务商'],
            ],
            'zhipu' => [
                'name' => '智谱 GLM',
                'base_url' => 'https://open.bigmodel.cn/api/paas/v4',
                'chat_path' => '/chat/completions',
                'image_path' => '/images/generations',
                'docs_url' => 'https://docs.bigmodel.cn/cn/guide/start/model-overview',
                'models' => ['chat' => ['glm-5.3', 'glm-5.3-flash', 'glm-4.7-flash'], 'image' => ['cogview-3-flash', 'cogview-4-250304']],
            ],
            'moonshot' => [
                'name' => 'Kimi（月之暗面）',
                'base_url' => 'https://api.moonshot.cn/v1',
                'chat_path' => '/chat/completions',
                'image_path' => '/images/generations',
                'docs_url' => 'https://platform.moonshot.cn/docs/api/chat',
                'models' => ['chat' => ['kimi-k2.5', 'kimi-k2-0905-preview', 'moonshot-v1-8k'], 'image' => []],
                'notes' => ['image' => 'Kimi 暂未提供生图模型，请改用豆包或智谱服务商'],
            ],
            'spark' => [
                'name' => '讯飞星火',
                'base_url' => 'https://spark-api-open.xf-yun.com/v1',
                'chat_path' => '/chat/completions',
                'image_path' => '/images/generations',
                'docs_url' => 'https://www.xfyun.cn/doc/spark/HTTP%E8%B0%83%E7%94%A8%E6%96%87%E6%A1%A3.html',
                'models' => ['chat' => ['generalv3.5', '4.0Ultra', 'lite'], 'image' => []],
                'notes' => ['image' => '星火生图仅提供原生接口，不兼容 OpenAI 格式，请改用豆包或智谱服务商'],
            ],
            'hunyuan' => [
                'name' => '腾讯混元',
                'base_url' => 'https://tokenhub.tencentmaas.com/v1',
                'chat_path' => '/chat/completions',
                'image_path' => '/images/generations',
                'docs_url' => 'https://cloud.tencent.com/document/product/1823',
                'models' => ['chat' => ['hy3', 'hy4-preview', 'hunyuan-role-latest'], 'image' => []],
                'notes' => ['image' => '混元生图仅提供原生接口，不兼容 OpenAI 格式，请改用豆包或智谱服务商'],
            ],
            'wenxin' => [
                'name' => '文心（百度千帆v2）',
                'base_url' => 'https://qianfan.baidubce.com/v2',
                'chat_path' => '/chat/completions',
                'image_path' => '/images/generations',
                'docs_url' => 'https://cloud.baidu.com/doc/qianfan-api/s/wm7ltcvgc',
                'models' => ['chat' => ['ernie-5.1', 'deepseek-v4.1-flash', 'qwen3.5-35b-a3b'], 'image' => []],
                'notes' => ['image' => '千帆生图仅提供原生接口，不兼容 OpenAI 格式，请改用豆包或智谱服务商'],
            ],
            'openai_compat' => [
                'name' => '自定义（OpenAI 兼容接口）',
                'base_url' => '',
                'chat_path' => '/chat/completions',
                'image_path' => '/images/generations',
                'models' => ['chat' => [], 'image' => []],
            ],
        ];
    }

    /** 第三方素材服务预设（AI 版专属） */
    public static function stockPresets()
    {
        return [
            'pexels' => [
                'name' => 'Pexels 图库',
                'base_url' => 'https://api.pexels.com/v1',
                'docs_url' => 'https://www.pexels.com/api/',
                'desc' => '免费可商用图库，注册后在 API 页面获取 Key',
            ],
        ];
    }

    /** 当前授权是否 AI 版（绑官方套餐 php_servicemeal >= 5） */
    public static function isAiEdition()
    {
        if (!function_exists('tpCache')) return false;
        return floatval(tpCache('php.php_servicemeal')) >= 5;
    }

    /*==================== 对外：能力探测 ====================*/

    /** 该能力是否已配置可用（有启用的默认模型且服务商启用） */
    public static function hasCapability($capability)
    {
        if (!in_array($capability, self::$capabilities)) return false;
        if (in_array($capability, ['video', 'embed'])) return false; // P3+ 开放
        try {
            $model = self::resolveModel($capability, []);
            return !empty($model);
        } catch (\Exception $e) {
            return false;
        }
    }

    /*==================== 对外：生文 ====================*/

    /**
     * 对话补全（整段返回）
     * @param array|string $messages OpenAI 格式 messages，或纯字符串（视为单条 user 消息）
     * @param array $opts scene(调用方标识) / model_id(指定模型) / temperature / max_tokens / timeout
     * @return string 模型回答文本
     * @throws AiServiceException
     */
    public static function chat($messages, $opts = [])
    {
        return self::chatRun($messages, null, $opts);
    }

    /**
     * 对话补全（流式，SSE 增量经 $onDelta 回调；缓冲环境自动整段退化不影响正确性）
     * @param callable $onDelta function($deltaText) 每个增量片段回调
     * @return string 完整回答文本
     * @throws AiServiceException
     */
    public static function chatStream($messages, $onDelta, $opts = [])
    {
        if (!is_callable($onDelta)) {
            throw new AiServiceException('chatStream 需要可调用的 onDelta 回调', AiServiceException::BAD_PARAM);
        }
        return self::chatRun($messages, $onDelta, $opts);
    }

    /** 输出 SSE 响应头并尽力关闭输出缓冲（chatStream 的调用方在 echo 前调用一次） */
    public static function streamBegin()
    {
        if (!headers_sent()) {
            header('Content-Type: text/event-stream; charset=utf-8');
            header('Cache-Control: no-cache');
            header('X-Accel-Buffering: no');
        }
        @ini_set('output_buffering', 'off');
        @ini_set('zlib.output_compression', 'off');
        while (ob_get_level() > 0) { @ob_end_flush(); }
    }

    /*==================== 对外：生图与 P3+ 能力占位 ====================*/

    /**
     * 文生图：同步生成并下载落地到站内 uploads 目录（远程 URL 有效期极短，禁止直接外发）
     * @param string $prompt 提示词
     * @param array $opts scene / model_id / size(如 1024x1024) / n(张数,默认1) / timeout
     * @return array 站内图片 URL 列表（如 ['/uploads/aiimg/20260831/xx.png']）
     * @throws AiServiceException
     */
    public static function image($prompt, $opts = [])
    {
        return self::imageRun($prompt, $opts);
    }

    public static function videoSubmit($prompt, $opts = [])
    {
        throw new AiServiceException('生视频能力将在后续版本开放', AiServiceException::NOT_CONFIGURED);
    }

    public static function videoQuery($taskId)
    {
        throw new AiServiceException('生视频能力将在后续版本开放', AiServiceException::NOT_CONFIGURED);
    }

    public static function embed($input, $opts = [])
    {
        throw new AiServiceException('向量能力将在后续版本开放', AiServiceException::NOT_CONFIGURED);
    }

    /*==================== 对外：素材图库搜索（AI 版专属） ====================*/

    /**
     * 第三方图库搜图（Pexels 等；仅 AI 版授权可用）
     * @param string $keyword 搜索关键词
     * @param array $opts scene / n(张数 1-30, 默认 10) / page / orientation(landscape|portrait|square)
     * @return array 图片列表 [['url','thumb','width','height','author','link'], ...]
     * @throws AiServiceException
     */
    public static function stockImage($keyword, $opts = [])
    {
        self::ensureTables();
        if (!self::isAiEdition()) {
            throw new AiServiceException('第三方素材服务仅限 AI 版授权使用', AiServiceException::NOT_CONFIGURED);
        }
        $keyword = trim((string)$keyword);
        if ('' === $keyword) {
            throw new AiServiceException('搜图关键词不能为空', AiServiceException::BAD_PARAM);
        }
        $scene = isset($opts['scene']) ? preg_replace('/[^\w\-]/', '', (string)$opts['scene']) : 'default';
        $settings = self::settings();

        if ($settings['aiservice_rate_limit'] > 0 && empty($opts['_skip_limit'])) {
            $ip = self::clientIp();
            $n = self::counterBump('rl:' . $ip . ':' . floor(time() / 60), 120);
            if ($n > $settings['aiservice_rate_limit']) {
                throw new AiServiceException('AI 调用过于频繁，请稍后重试', AiServiceException::RATE_LIMITED);
            }
        }

        $provider = Db::name('ai_provider')->where(['kind' => 'stock', 'status' => 1])->order('id asc')->find();
        if (empty($provider)) {
            throw new AiServiceException('未配置可用的素材服务，请到后台「AI 模型配置」添加', AiServiceException::NOT_CONFIGURED);
        }
        $conf = [
            'type' => $provider['type'],
            'base_url' => $provider['base_url'],
            'api_key' => self::decryptKey($provider['api_key']),
        ];
        $list = self::requestStock($conf, $keyword, $opts);
        self::recordUsage(0, 'stock', $scene, count($list));
        return $list;
    }

    /**
     * 测试素材服务（真实搜 1 张；供后台「测试」按钮）
     * @param array $conf ['type'=>, 'base_url'=>, 'api_key'=>]
     * @return array ['ok'=>bool, 'msg'=>string, 'latency_ms'=>int]
     */
    public static function testStock($conf)
    {
        $start = microtime(true);
        try {
            $list = self::requestStock($conf, 'nature', ['n' => 1, 'timeout' => 30]);
            $ms = intval((microtime(true) - $start) * 1000);
            if (empty($list)) {
                return ['ok' => false, 'msg' => '接口可达但未返回图片', 'latency_ms' => $ms];
            }
            return ['ok' => true, 'msg' => '连通正常，搜到图片', 'latency_ms' => $ms];
        } catch (\Exception $e) {
            $ms = intval((microtime(true) - $start) * 1000);
            return ['ok' => false, 'msg' => $e->getMessage(), 'latency_ms' => $ms];
        }
    }

    /*==================== 对外：连通测试（后台用） ====================*/

    /**
     * 测试服务商/模型连通性（不记账、不限流；供后台「测试连通」按钮）
     * @param array $conf ['base_url'=>, 'api_key'=>, 'model'=>, 'type'=>]
     * @return array ['ok'=>bool, 'msg'=>string, 'latency_ms'=>int]
     */
    public static function testProvider($conf)
    {
        $start = microtime(true);
        try {
            $content = self::requestChat($conf, [['role' => 'user', 'content' => '你好，请回复“连接成功”四个字。']], null, ['max_tokens' => 20, 'timeout' => 30]);
            $ms = intval((microtime(true) - $start) * 1000);
            return ['ok' => true, 'msg' => '连通正常，回复：' . mb_substr(trim($content['content']), 0, 50), 'latency_ms' => $ms];
        } catch (\Exception $e) {
            $ms = intval((microtime(true) - $start) * 1000);
            return ['ok' => false, 'msg' => $e->getMessage(), 'latency_ms' => $ms];
        }
    }

    /**
     * 测试生图模型（真实生成 1 张并落地；供后台生图模型「测试」按钮）
     * @param array $conf ['base_url'=>, 'api_key'=>, 'model'=>, 'type'=>]
     * @return array ['ok'=>bool, 'msg'=>string, 'latency_ms'=>int, 'url'=>string]
     */
    public static function testImage($conf)
    {
        $start = microtime(true);
        try {
            $remotes = self::requestImage($conf, '一只可爱的橘猫，扁平插画风格', ['size' => '1024x1024', 'timeout' => 120]);
            if (empty($remotes)) {
                throw new AiServiceException('生图接口未返回图片', AiServiceException::PROVIDER_ERROR);
            }
            $url = self::saveImage($remotes[0]);
            $ms = intval((microtime(true) - $start) * 1000);
            return ['ok' => true, 'msg' => '生图成功', 'latency_ms' => $ms, 'url' => $url];
        } catch (\Exception $e) {
            $ms = intval((microtime(true) - $start) * 1000);
            return ['ok' => false, 'msg' => $e->getMessage(), 'latency_ms' => $ms, 'url' => ''];
        }
    }

    /*==================== 对外：Key 加解密与掩码 ====================*/

    /** 加密 API Key（AES-256-CBC，密钥在 data/conf/ 私有目录，不入库） */
    public static function encryptKey($plain)
    {
        if ('' === (string)$plain) return '';
        $key = self::secretKey();
        $iv = self::randomBytes(16);
        $cipher = openssl_encrypt($plain, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return 'aiv1:' . base64_encode($iv . $cipher);
    }

    /** 解密 API Key */
    public static function decryptKey($stored)
    {
        $stored = (string)$stored;
        if ('' === $stored) return '';
        if (0 !== strpos($stored, 'aiv1:')) return $stored; // 兼容明文迁移期
        $bin = base64_decode(substr($stored, 5));
        if (false === $bin || strlen($bin) <= 16) return '';
        $iv = substr($bin, 0, 16);
        $cipher = substr($bin, 16);
        $plain = openssl_decrypt($cipher, 'AES-256-CBC', self::secretKey(), OPENSSL_RAW_DATA, $iv);
        return false === $plain ? '' : $plain;
    }

    /** Key 掩码回显（只露前 4 后 4） */
    public static function maskKey($plain)
    {
        $plain = (string)$plain;
        $len = strlen($plain);
        if ($len <= 8) return '' === $plain ? '' : '******';
        return substr($plain, 0, 4) . str_repeat('*', min(20, $len - 8)) . substr($plain, -4);
    }

    /*==================== 对外：配置与用量（后台用） ====================*/

    /** 读取 SDK 设置（限流/预算），带默认值 */
    public static function settings()
    {
        $row = [];
        if (function_exists('tpCache')) {
            $row = tpCache('aiservice');
            if (!is_array($row)) $row = [];
        }
        return [
            'aiservice_rate_limit' => isset($row['aiservice_rate_limit']) ? max(0, intval($row['aiservice_rate_limit'])) : 30,
            'aiservice_budget_chat' => isset($row['aiservice_budget_chat']) ? max(0, intval($row['aiservice_budget_chat'])) : 0,
            'aiservice_budget_image' => isset($row['aiservice_budget_image']) ? max(0, intval($row['aiservice_budget_image'])) : 0,
            'aiservice_log_days' => isset($row['aiservice_log_days']) ? max(0, intval($row['aiservice_log_days'])) : 90,
        ];
    }

    /** 今日用量（按能力汇总 + 按场景明细） */
    public static function usageToday()
    {
        self::ensureTables();
        $today = strtotime(date('Y-m-d'));
        $out = ['chat_tokens' => 0, 'image_count' => 0, 'calls' => 0, 'scenes' => []];
        try {
            $rows = Db::name('ai_usage_daily')->field('scene, capability, calls, usage_num as total')
                ->where('day', $today)->order('calls desc')->select();
            foreach ($rows as $r) {
                $out['calls'] += intval($r['calls']);
                if ('chat' === $r['capability']) $out['chat_tokens'] += intval($r['total']);
                if ('image' === $r['capability']) $out['image_count'] += intval($r['total']);
                $out['scenes'][] = ['scene' => $r['scene'], 'capability' => $r['capability'], 'calls' => intval($r['calls']), 'usage' => intval($r['total'])];
            }
        } catch (\Exception $e) {
        }
        return $out;
    }

    /** 建表（不存在则创建；控制器与 SDK 首次调用时执行） */
    public static function ensureTables()
    {
        static $done = false;
        if ($done) return;
        $prefix = config('database.prefix');
        Db::execute("CREATE TABLE IF NOT EXISTS `{$prefix}ai_provider` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL DEFAULT '',
            `type` varchar(30) NOT NULL DEFAULT 'openai_compat',
            `base_url` varchar(255) NOT NULL DEFAULT '',
            `api_key` text,
            `kind` varchar(20) NOT NULL DEFAULT 'model',
            `status` tinyint(1) NOT NULL DEFAULT 1,
            `add_time` int(11) NOT NULL DEFAULT 0,
            `update_time` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        Db::execute("CREATE TABLE IF NOT EXISTS `{$prefix}ai_model` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `provider_id` int(10) unsigned NOT NULL DEFAULT 0,
            `capability` varchar(20) NOT NULL DEFAULT 'chat',
            `model` varchar(150) NOT NULL DEFAULT '',
            `params` text,
            `is_default` tinyint(1) NOT NULL DEFAULT 0,
            `status` tinyint(1) NOT NULL DEFAULT 1,
            `add_time` int(11) NOT NULL DEFAULT 0,
            `update_time` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            KEY `capability` (`capability`, `is_default`, `status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        Db::execute("CREATE TABLE IF NOT EXISTS `{$prefix}ai_usage_log` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `model_id` int(10) unsigned NOT NULL DEFAULT 0,
            `capability` varchar(20) NOT NULL DEFAULT 'chat',
            `scene` varchar(50) NOT NULL DEFAULT '',
            `usage_num` int(10) unsigned NOT NULL DEFAULT 0,
            `cost_est` decimal(10,4) NOT NULL DEFAULT 0,
            `ip` varchar(46) NOT NULL DEFAULT '',
            `create_time` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            KEY `create_time` (`create_time`),
            KEY `scene` (`scene`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        Db::execute("CREATE TABLE IF NOT EXISTS `{$prefix}ai_usage_daily` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `day` int(11) NOT NULL DEFAULT 0,
            `capability` varchar(20) NOT NULL DEFAULT 'chat',
            `scene` varchar(50) NOT NULL DEFAULT '',
            `model_id` int(10) unsigned NOT NULL DEFAULT 0,
            `calls` int(10) unsigned NOT NULL DEFAULT 0,
            `usage_num` int(10) unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            UNIQUE KEY `dim` (`day`, `capability`, `scene`, `model_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        Db::execute("CREATE TABLE IF NOT EXISTS `{$prefix}ai_counter` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL DEFAULT '',
            `value` int(10) unsigned NOT NULL DEFAULT 0,
            `expire_time` int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            UNIQUE KEY `name` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        $done = true;
    }

    /*==================== 内部实现 ====================*/

    /** chat 主流程：限流→预算→选模型→请求（失败自动跨服务商 fallback）→记账 */
    private static function chatRun($messages, $onDelta, $opts)
    {
        self::ensureTables();
        $messages = self::normalizeMessages($messages);
        $scene = isset($opts['scene']) ? preg_replace('/[^\w\-]/', '', (string)$opts['scene']) : 'default';
        $settings = self::settings();

        // IP 限流（每 IP 每分钟；0=不限）
        if ($settings['aiservice_rate_limit'] > 0 && empty($opts['_skip_limit'])) {
            $ip = self::clientIp();
            $n = self::counterBump('rl:' . $ip . ':' . floor(time() / 60), 120);
            if ($n > $settings['aiservice_rate_limit']) {
                throw new AiServiceException('AI 调用过于频繁，请稍后重试', AiServiceException::RATE_LIMITED);
            }
        }

        // 每日 tokens 预算熔断（0=不限）
        if ($settings['aiservice_budget_chat'] > 0) {
            $today = strtotime(date('Y-m-d'));
            $used = 0;
            try {
                $used = intval(Db::name('ai_usage_log')->where('capability', 'chat')->where('create_time', '>=', $today)->sum('usage_num'));
            } catch (\Exception $e) {
            }
            if ($used >= $settings['aiservice_budget_chat']) {
                throw new AiServiceException('生文能力今日预算已用完', AiServiceException::BUDGET_EXCEEDED);
            }
        }

        $model = self::resolveModel('chat', $opts);
        if (empty($model)) {
            throw new AiServiceException('生文能力未配置可用模型，请到后台「AI 模型配置」添加', AiServiceException::NOT_CONFIGURED);
        }

        // 候选队列：首选模型 + 其他服务商的同能力启用模型（指定 model_id 时不做 fallback）
        $candidates = [$model];
        if (empty($opts['model_id'])) {
            $candidates = array_merge($candidates, self::fallbackModels('chat', $model));
        }

        $emitted = false;
        $wrappedDelta = null;
        if (null !== $onDelta) {
            $wrappedDelta = function ($delta) use ($onDelta, &$emitted) {
                $emitted = true;
                call_user_func($onDelta, $delta);
            };
        }

        $result = null;
        $lastEx = null;
        $total = count($candidates);
        foreach ($candidates as $i => $m) {
            $provider = self::providerOf($m['provider_id']);
            if (empty($provider)) {
                $lastEx = new AiServiceException('模型所属服务商不存在或已停用', AiServiceException::NOT_CONFIGURED);
                continue;
            }
            $conf = [
                'type' => $provider['type'],
                'base_url' => $provider['base_url'],
                'api_key' => self::decryptKey($provider['api_key']),
                'model' => $m['model'],
            ];
            $params = !empty($m['params']) ? json_decode($m['params'], true) : [];
            $callOpts = is_array($params) ? array_merge($params, $opts) : $opts;
            try {
                $result = self::requestChat($conf, $messages, $wrappedDelta, $callOpts);
                $model = $m;
                break;
            } catch (AiServiceException $e) {
                $lastEx = $e;
                $retriable = in_array($e->getCode(), [AiServiceException::PROVIDER_ERROR, AiServiceException::TIMEOUT], true);
                // 流式已向调用方输出过增量时不能换模型重试，否则内容重复
                if (!$retriable || $emitted || $i + 1 >= $total) {
                    throw $e;
                }
            }
        }
        if (null === $result) {
            if (null !== $lastEx) throw $lastEx;
            throw new AiServiceException('生文能力未配置可用模型，请到后台「AI 模型配置」添加', AiServiceException::NOT_CONFIGURED);
        }

        self::recordUsage(intval($model['id']), 'chat', $scene, intval($result['tokens']));
        return $result['content'];
    }

    /** image 主流程：限流→预算→选模型→请求→落地→记账 */
    private static function imageRun($prompt, $opts)
    {
        self::ensureTables();
        $prompt = trim((string)$prompt);
        if ('' === $prompt) {
            throw new AiServiceException('生图 prompt 不能为空', AiServiceException::BAD_PARAM);
        }
        $scene = isset($opts['scene']) ? preg_replace('/[^\w\-]/', '', (string)$opts['scene']) : 'default';
        $settings = self::settings();

        if ($settings['aiservice_rate_limit'] > 0 && empty($opts['_skip_limit'])) {
            $ip = self::clientIp();
            $n = self::counterBump('rl:' . $ip . ':' . floor(time() / 60), 120);
            if ($n > $settings['aiservice_rate_limit']) {
                throw new AiServiceException('AI 调用过于频繁，请稍后重试', AiServiceException::RATE_LIMITED);
            }
        }

        // 每日张数预算熔断（0=不限）
        if ($settings['aiservice_budget_image'] > 0) {
            $today = strtotime(date('Y-m-d'));
            $used = 0;
            try {
                $used = intval(Db::name('ai_usage_log')->where('capability', 'image')->where('create_time', '>=', $today)->sum('usage_num'));
            } catch (\Exception $e) {
            }
            if ($used >= $settings['aiservice_budget_image']) {
                throw new AiServiceException('生图能力今日预算已用完', AiServiceException::BUDGET_EXCEEDED);
            }
        }

        $model = self::resolveModel('image', $opts);
        if (empty($model)) {
            throw new AiServiceException('生图能力未配置可用模型，请到后台「AI 模型配置」添加', AiServiceException::NOT_CONFIGURED);
        }
        $provider = self::providerOf($model['provider_id']);
        if (empty($provider)) {
            throw new AiServiceException('模型所属服务商不存在或已停用', AiServiceException::NOT_CONFIGURED);
        }
        $conf = [
            'type' => $provider['type'],
            'base_url' => $provider['base_url'],
            'api_key' => self::decryptKey($provider['api_key']),
            'model' => $model['model'],
        ];
        $params = !empty($model['params']) ? json_decode($model['params'], true) : [];
        $callOpts = is_array($params) ? array_merge($params, $opts) : $opts;

        $remotes = self::requestImage($conf, $prompt, $callOpts);
        $urls = [];
        foreach ($remotes as $item) {
            $urls[] = self::saveImage($item);
        }
        if (empty($urls)) {
            throw new AiServiceException('生图接口未返回图片', AiServiceException::PROVIDER_ERROR);
        }
        self::recordUsage(intval($model['id']), 'image', $scene, count($urls));
        return $urls;
    }

    /** 发起 OpenAI 兼容 chat/completions 请求（$onDelta 非空则流式） */
    private static function requestChat($conf, $messages, $onDelta, $opts)
    {
        $baseUrl = rtrim(trim((string)$conf['base_url']), '/');
        if ('' === $baseUrl || !preg_match('#^https?://#i', $baseUrl)) {
            throw new AiServiceException('服务商 base_url 未配置或格式错误', AiServiceException::NOT_CONFIGURED);
        }
        $apiKey = (string)$conf['api_key'];
        if ('' === $apiKey) {
            throw new AiServiceException('服务商 API Key 未配置', AiServiceException::NOT_CONFIGURED);
        }
        $presets = self::presets();
        $type = isset($conf['type']) && isset($presets[$conf['type']]) ? $conf['type'] : 'openai_compat';
        $path = $presets[$type]['chat_path'];
        // base_url 已含完整路径时不再拼接
        $url = preg_match('#/chat/completions$#', $baseUrl) ? $baseUrl : $baseUrl . $path;

        $body = [
            'model' => (string)$conf['model'],
            'messages' => $messages,
        ];
        if (isset($opts['temperature']) && '' !== $opts['temperature']) $body['temperature'] = floatval($opts['temperature']);
        if (!empty($opts['max_tokens'])) $body['max_tokens'] = intval($opts['max_tokens']);
        $stream = null !== $onDelta;
        if ($stream) $body['stream'] = true;

        $timeout = !empty($opts['timeout']) ? intval($opts['timeout']) : ($stream ? 300 : 120);
        $ca = ini_get('curl.cainfo') ? ini_get('curl.cainfo') : ini_get('openssl.cafile');
        $verify = !empty($ca) && is_file($ca);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($body, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_SSL_VERIFYPEER => $verify ? 1 : 0,
            CURLOPT_SSL_VERIFYHOST => $verify ? 2 : 0,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        ]);

        $full = '';
        $usage = 0;
        $rawBuf = '';
        if ($stream) {
            $sseBuf = '';
            curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $data) use (&$sseBuf, &$full, &$usage, &$rawBuf, $onDelta) {
                $rawBuf .= $data;
                if (strlen($rawBuf) > 65536) $rawBuf = substr($rawBuf, -65536);
                $sseBuf .= $data;
                // 按行解析 SSE，保留最后不完整行
                while (false !== ($pos = strpos($sseBuf, "\n"))) {
                    $line = trim(substr($sseBuf, 0, $pos));
                    $sseBuf = substr($sseBuf, $pos + 1);
                    if ('' === $line || 0 !== strpos($line, 'data:')) continue;
                    $payload = trim(substr($line, 5));
                    if ('[DONE]' === $payload) continue;
                    $json = json_decode($payload, true);
                    if (!is_array($json)) continue;
                    if (isset($json['usage']['total_tokens'])) $usage = intval($json['usage']['total_tokens']);
                    if (isset($json['choices'][0]['delta']['content'])) {
                        $delta = $json['choices'][0]['delta']['content'];
                        if ('' !== $delta) {
                            $full .= $delta;
                            call_user_func($onDelta, $delta);
                        }
                    }
                }
                return strlen($data);
            });
        } else {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        }

        $resp = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $status = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));

        if ($errno) {
            $code = in_array($errno, [CURLE_OPERATION_TIMEOUTED]) ? AiServiceException::TIMEOUT : AiServiceException::PROVIDER_ERROR;
            throw new AiServiceException(self::friendlyCurlError($errno, $error, 'AI 接口'), $code);
        }

        if ($stream) {
            if ($status >= 400 || ('' === $full && '' !== trim($rawBuf) && null !== json_decode(trim($rawBuf), true))) {
                self::throwProviderError($status, $rawBuf);
            }
            if (0 === $usage) $usage = self::estimateTokens($messages, $full);
            return ['content' => $full, 'tokens' => $usage];
        }

        $json = json_decode((string)$resp, true);
        if ($status >= 400 || !is_array($json)) {
            self::throwProviderError($status, (string)$resp);
        }
        if (!isset($json['choices'][0]['message']['content'])) {
            self::throwProviderError($status, (string)$resp);
        }
        $content = $json['choices'][0]['message']['content'];
        $usage = isset($json['usage']['total_tokens']) ? intval($json['usage']['total_tokens']) : self::estimateTokens($messages, $content);
        return ['content' => $content, 'tokens' => $usage];
    }

    /** 发起 OpenAI 兼容 images/generations 请求，返回 [['url'=>..] 或 ['b64'=>..]] 列表 */
    private static function requestImage($conf, $prompt, $opts)
    {
        $baseUrl = rtrim(trim((string)$conf['base_url']), '/');
        if ('' === $baseUrl || !preg_match('#^https?://#i', $baseUrl)) {
            throw new AiServiceException('服务商 base_url 未配置或格式错误', AiServiceException::NOT_CONFIGURED);
        }
        $apiKey = (string)$conf['api_key'];
        if ('' === $apiKey) {
            throw new AiServiceException('服务商 API Key 未配置', AiServiceException::NOT_CONFIGURED);
        }
        $presets = self::presets();
        $type = isset($conf['type']) && isset($presets[$conf['type']]) ? $conf['type'] : 'openai_compat';
        $path = isset($presets[$type]['image_path']) ? $presets[$type]['image_path'] : '/images/generations';
        $url = preg_match('#/images/generations$#', $baseUrl) ? $baseUrl : $baseUrl . $path;

        $body = [
            'model' => (string)$conf['model'],
            'prompt' => $prompt,
        ];
        if (!empty($opts['size'])) $body['size'] = preg_replace('/[^\dxX]/', '', (string)$opts['size']);
        $n = !empty($opts['n']) ? max(1, min(4, intval($opts['n']))) : 1;
        if ($n > 1) $body['n'] = $n;
        // 透传模型自有参数（排除已处理的通用项）
        foreach ($opts as $k => $v) {
            if (in_array($k, ['scene', 'model_id', 'size', 'n', 'timeout', '_skip_limit', 'temperature', 'max_tokens'], true)) continue;
            if (is_scalar($v) && !isset($body[$k])) $body[$k] = $v;
        }

        $timeout = !empty($opts['timeout']) ? intval($opts['timeout']) : 180;
        $ca = ini_get('curl.cainfo') ? ini_get('curl.cainfo') : ini_get('openssl.cafile');
        $verify = !empty($ca) && is_file($ca);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($body, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_SSL_VERIFYPEER => $verify ? 1 : 0,
            CURLOPT_SSL_VERIFYHOST => $verify ? 2 : 0,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        ]);
        $resp = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $status = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));

        if ($errno) {
            $code = in_array($errno, [CURLE_OPERATION_TIMEOUTED]) ? AiServiceException::TIMEOUT : AiServiceException::PROVIDER_ERROR;
            throw new AiServiceException(self::friendlyCurlError($errno, $error, '生图接口'), $code);
        }
        $json = json_decode((string)$resp, true);
        if ($status >= 400 || !is_array($json) || empty($json['data']) || !is_array($json['data'])) {
            self::throwProviderError($status, (string)$resp);
        }
        $out = [];
        foreach ($json['data'] as $d) {
            if (!empty($d['url'])) $out[] = ['url' => (string)$d['url']];
            elseif (!empty($d['b64_json'])) $out[] = ['b64' => (string)$d['b64_json']];
        }
        return $out;
    }

    /** 发起图库搜索请求（目前支持 Pexels），返回标准化图片列表 */
    private static function requestStock($conf, $keyword, $opts)
    {
        $type = isset($conf['type']) ? (string)$conf['type'] : '';
        $presets = self::stockPresets();
        if (!isset($presets[$type])) {
            throw new AiServiceException('不支持的素材服务类型：' . $type, AiServiceException::NOT_CONFIGURED);
        }
        $baseUrl = rtrim(trim((string)$conf['base_url']), '/');
        if ('' === $baseUrl) $baseUrl = $presets[$type]['base_url'];
        $apiKey = (string)$conf['api_key'];
        if ('' === $apiKey) {
            throw new AiServiceException('素材服务 API Key 未配置', AiServiceException::NOT_CONFIGURED);
        }

        $n = !empty($opts['n']) ? max(1, min(30, intval($opts['n']))) : 10;
        $query = ['query' => $keyword, 'per_page' => $n];
        if (!empty($opts['page'])) $query['page'] = max(1, intval($opts['page']));
        if (!empty($opts['orientation']) && in_array($opts['orientation'], ['landscape', 'portrait', 'square'], true)) {
            $query['orientation'] = $opts['orientation'];
        }
        $url = $baseUrl . '/search?' . http_build_query($query);

        $timeout = !empty($opts['timeout']) ? intval($opts['timeout']) : 60;
        $ca = ini_get('curl.cainfo') ? ini_get('curl.cainfo') : ini_get('openssl.cafile');
        $verify = !empty($ca) && is_file($ca);
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: ' . $apiKey],
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_SSL_VERIFYPEER => $verify ? 1 : 0,
            CURLOPT_SSL_VERIFYHOST => $verify ? 2 : 0,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
        ]);
        $resp = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $status = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));

        if ($errno) {
            $code = in_array($errno, [CURLE_OPERATION_TIMEOUTED]) ? AiServiceException::TIMEOUT : AiServiceException::PROVIDER_ERROR;
            throw new AiServiceException(self::friendlyCurlError($errno, $error, '素材接口'), $code);
        }
        $json = json_decode((string)$resp, true);
        if (401 === $status || 403 === $status) {
            throw new AiServiceException('素材服务 API Key 无效或已过期（HTTP ' . $status . '）', AiServiceException::PROVIDER_ERROR);
        }
        if ($status >= 400 || !is_array($json) || !isset($json['photos']) || !is_array($json['photos'])) {
            self::throwProviderError($status, (string)$resp);
        }
        $out = [];
        foreach ($json['photos'] as $p) {
            if (empty($p['src']['large'])) continue;
            $out[] = [
                'url' => (string)$p['src']['large'],
                'thumb' => !empty($p['src']['medium']) ? (string)$p['src']['medium'] : (string)$p['src']['large'],
                'width' => isset($p['width']) ? intval($p['width']) : 0,
                'height' => isset($p['height']) ? intval($p['height']) : 0,
                'author' => isset($p['photographer']) ? (string)$p['photographer'] : '',
                'link' => isset($p['url']) ? (string)$p['url'] : '',
            ];
        }
        return $out;
    }

    /** 下载/解码图片并落地 uploads/aiimg/Ymd/，返回站内相对 URL */
    private static function saveImage($item)
    {
        $bin = '';
        if (!empty($item['b64'])) {
            $bin = base64_decode($item['b64']);
        } elseif (!empty($item['url'])) {
            $u = (string)$item['url'];
            if (!preg_match('#^https?://#i', $u)) {
                throw new AiServiceException('生图返回的图片地址非法', AiServiceException::PROVIDER_ERROR);
            }
            $ca = ini_get('curl.cainfo') ? ini_get('curl.cainfo') : ini_get('openssl.cafile');
            $verify = !empty($ca) && is_file($ca);
            $ch = curl_init($u);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 3,
                CURLOPT_CONNECTTIMEOUT => 15,
                CURLOPT_TIMEOUT => 120,
                CURLOPT_SSL_VERIFYPEER => $verify ? 1 : 0,
                CURLOPT_SSL_VERIFYHOST => $verify ? 2 : 0,
                CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
                CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            ]);
            $bin = curl_exec($ch);
            $errno = curl_errno($ch);
            if ($errno || false === $bin || '' === $bin) {
                throw new AiServiceException('生成图下载失败（远程 URL 有效期极短，请重试）', AiServiceException::PROVIDER_ERROR);
            }
        }
        if ('' === $bin || false === $bin) {
            throw new AiServiceException('生图接口返回的图片数据为空', AiServiceException::PROVIDER_ERROR);
        }
        if (strlen($bin) > 20 * 1024 * 1024) {
            throw new AiServiceException('生成图超过 20MB 限制', AiServiceException::PROVIDER_ERROR);
        }
        $info = function_exists('getimagesizefromstring') ? @getimagesizefromstring($bin) : false;
        if (false === $info || empty($info[2])) {
            throw new AiServiceException('生成图不是合法的图片文件', AiServiceException::PROVIDER_ERROR);
        }
        $extMap = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_GIF => 'gif'];
        if (defined('IMAGETYPE_WEBP')) $extMap[IMAGETYPE_WEBP] = 'webp';
        $ext = isset($extMap[$info[2]]) ? $extMap[$info[2]] : 'png';

        $root = defined('ROOT_PATH') ? ROOT_PATH : './';
        $rel = 'uploads/aiimg/' . date('Ymd');
        $dir = $root . $rel;
        if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
            throw new AiServiceException('生图目录创建失败：' . $rel, AiServiceException::PROVIDER_ERROR);
        }
        $name = date('His') . '_' . bin2hex(self::randomBytes(8)) . '.' . $ext;
        if (false === @file_put_contents($dir . '/' . $name, $bin)) {
            throw new AiServiceException('生成图保存失败，请检查 uploads 目录写权限', AiServiceException::PROVIDER_ERROR);
        }
        $rootDir = defined('ROOT_DIR') ? rtrim(ROOT_DIR, '/') : '';
        return $rootDir . '/' . $rel . '/' . $name;
    }

    /** 统一记账：明细 + 日汇总 upsert + 低频清理过期明细 */
    private static function recordUsage($modelId, $capability, $scene, $usageNum)
    {
        try {
            Db::name('ai_usage_log')->insert([
                'model_id' => $modelId,
                'capability' => $capability,
                'scene' => $scene,
                'usage_num' => $usageNum,
                'cost_est' => 0,
                'ip' => self::clientIp(),
                'create_time' => time(),
            ]);
        } catch (\Exception $e) {
        }
        try {
            $prefix = config('database.prefix');
            $day = intval(strtotime(date('Y-m-d')));
            Db::execute(
                "INSERT INTO `{$prefix}ai_usage_daily` (`day`,`capability`,`scene`,`model_id`,`calls`,`usage_num`) VALUES (?,?,?,?,1,?) "
                . "ON DUPLICATE KEY UPDATE `calls`=`calls`+1, `usage_num`=`usage_num`+VALUES(`usage_num`)",
                [$day, $capability, $scene, $modelId, $usageNum]
            );
        } catch (\Exception $e) {
        }
        // 低频触发明细清理（保留期 0=永久）
        if (1 === mt_rand(1, 50)) {
            try {
                $days = self::settings();
                $days = $days['aiservice_log_days'];
                if ($days > 0) {
                    Db::name('ai_usage_log')->where('create_time', '<', time() - $days * 86400)->limit(500)->delete();
                }
            } catch (\Exception $e) {
            }
        }
    }

    private static function throwProviderError($status, $raw)
    {
        $raw = trim((string)$raw);
        $detail = '';
        $json = json_decode($raw, true);
        if (is_array($json)) {
            if (isset($json['error']['message'])) $detail = (string)$json['error']['message'];
            elseif (isset($json['message'])) $detail = (string)$json['message'];
        }
        $friendly = self::friendlyProviderError($status, $detail);
        $msg = 'AI 服务商返回错误（HTTP ' . $status . '）';
        if ('' !== $friendly) $msg = $friendly;
        if ('' !== $detail) $msg .= "\n原始信息：" . mb_substr($detail, 0, 300);
        throw new AiServiceException($msg, AiServiceException::PROVIDER_ERROR, mb_substr($raw, 0, 2000));
    }

    /** 把 curl 网络层报错翻译成中文指引 */
    private static function friendlyCurlError($errno, $error, $label)
    {
        if (CURLE_OPERATION_TIMEOUTED === $errno) {
            return $label . '请求超时。可能是服务商响应慢或服务器出网受限，请稍后重试；若持续超时，检查服务器能否访问该接口地址';
        }
        if (in_array($errno, [CURLE_COULDNT_RESOLVE_HOST, CURLE_COULDNT_CONNECT], true)) {
            return '无法连接服务商接口。请检查：1）接口地址是否拼写正确；2）服务器能否访问外网（部分国外接口需要网络代理）';
        }
        if (in_array($errno, [CURLE_SSL_CONNECT_ERROR, CURLE_SSL_CERTPROBLEM, CURLE_SSL_CACERT], true)) {
            return 'SSL 连接失败。请确认接口地址为 https 且服务器 CA 证书配置正常（php.ini 的 curl.cainfo）';
        }
        return $label . '请求失败：' . $error;
    }

    /** 把常见服务商报错翻译成带操作指引的中文；不认识的返回空串 */
    private static function friendlyProviderError($status, $detail)
    {
        $d = strtolower((string)$detail);
        $has = function ($needles) use ($d) {
            foreach ((array)$needles as $n) {
                if (false !== strpos($d, strtolower($n))) return true;
            }
            return false;
        };
        // 模型不存在 / 未开通（火山方舟等需在控制台先开通模型）
        if (404 === intval($status) || $has(['does not exist', 'model not found', 'modelnotopen', 'not activated', 'no access', 'do not have access', '未开通', '不存在'])) {
            return '模型不存在或未开通。请检查：1）模型名称是否拼写正确；2）是否已在服务商控制台开通该模型（例如火山方舟需在「开通管理」页面找到对应模型点击开通，开通后几分钟内生效）；3）接口地址是否正确；4）Key 所属账号与开通模型的账号是否一致';
        }
        // Key 无效
        if (in_array(intval($status), [401, 403], true) || $has(['invalid api key', 'invalid_api_key', 'authentication', 'unauthorized', 'invalid token', 'api key'])) {
            return 'API Key 无效或没有权限。请到服务商控制台重新生成 Key 并完整粘贴（注意不要带空格），保存后再测试';
        }
        // 欠费 / 额度用尽
        if (402 === intval($status) || $has(['insufficient balance', 'arrears', 'quota', 'billing', 'exceeded your current quota', '余额不足', '欠费'])) {
            return '账户余额不足或额度已用完。请到服务商控制台充值或开通付费后再测试';
        }
        // 限流
        if (429 === intval($status) || $has(['rate limit', 'too many requests', 'tpm', 'rpm'])) {
            return '请求过于频繁（触发服务商限流）。请稍等一分钟再测试；若持续出现，需在服务商控制台提升并发额度';
        }
        return '';
    }

    /** 选模型：opts.model_id 指定 > 该能力默认 > 该能力任一启用 */
    private static function resolveModel($capability, $opts)
    {
        self::ensureTables();
        if (!empty($opts['model_id'])) {
            $m = Db::name('ai_model')->where('id', intval($opts['model_id']))->where('status', 1)->find();
            if (empty($m) || $m['capability'] !== $capability) {
                throw new AiServiceException('指定的 model_id 不存在、已停用或能力不匹配', AiServiceException::BAD_PARAM);
            }
            return $m;
        }
        $m = Db::name('ai_model')->where(['capability' => $capability, 'is_default' => 1, 'status' => 1])->find();
        if (!empty($m)) return $m;
        return Db::name('ai_model')->where(['capability' => $capability, 'status' => 1])->order('id asc')->find();
    }

    /** fallback 候选：其他服务商的同能力启用模型（默认优先，其次 id 升序），每服务商取一个 */
    private static function fallbackModels($capability, $primary)
    {
        try {
            $rows = Db::name('ai_model')->where(['capability' => $capability, 'status' => 1])
                ->where('id', '<>', intval($primary['id']))
                ->order('is_default desc, id asc')->select();
        } catch (\Exception $e) {
            return [];
        }
        $out = [];
        $seen = [intval($primary['provider_id']) => true];
        foreach ($rows as $r) {
            $pid = intval($r['provider_id']);
            if (!empty($seen[$pid])) continue;
            $seen[$pid] = true;
            $out[] = $r;
        }
        return $out;
    }

    private static function providerOf($providerId)
    {
        return Db::name('ai_provider')->where('id', intval($providerId))->where('status', 1)->find();
    }

    private static function normalizeMessages($messages)
    {
        if (is_string($messages)) {
            $messages = [['role' => 'user', 'content' => $messages]];
        }
        if (!is_array($messages) || empty($messages)) {
            throw new AiServiceException('messages 不能为空', AiServiceException::BAD_PARAM);
        }
        foreach ($messages as $m) {
            if (!is_array($m) || empty($m['role']) || !isset($m['content'])) {
                throw new AiServiceException('messages 格式错误，需为 [{role, content}] 数组', AiServiceException::BAD_PARAM);
            }
        }
        return array_values($messages);
    }

    /** 无 usage 返回时的 tokens 估算（中文约 1 字/token，英文约 4 字符/token，粗略取字符数*0.75） */
    private static function estimateTokens($messages, $answer)
    {
        $chars = mb_strlen((string)$answer, 'UTF-8');
        foreach ($messages as $m) {
            $chars += mb_strlen(is_string($m['content']) ? $m['content'] : json_encode($m['content']), 'UTF-8');
        }
        return max(1, intval($chars * 0.75));
    }

    /*==================== 计数器（限流用，随过期复位） ====================*/

    private static function counterBump($name, $ttl)
    {
        $now = time();
        try {
            $row = Db::name('ai_counter')->where('name', $name)->find();
            if (empty($row)) {
                try {
                    Db::name('ai_counter')->insert(['name' => $name, 'value' => 1, 'expire_time' => $now + $ttl]);
                    return 1;
                } catch (\Exception $e) {
                    $row = Db::name('ai_counter')->where('name', $name)->find();
                    if (empty($row)) return 1;
                }
            }
            if ($row['expire_time'] < $now) {
                Db::name('ai_counter')->where('id', $row['id'])->update(['value' => 1, 'expire_time' => $now + $ttl]);
                return 1;
            }
            Db::name('ai_counter')->where('id', $row['id'])->setInc('value');
            return intval($row['value']) + 1;
        } catch (\Exception $e) {
            return 1;
        }
    }

    /*==================== 密钥文件 ====================*/

    /** 加密密钥：data/conf/aiservice.key.php（首次随机生成；PHP 头防直接下载） */
    private static function secretKey()
    {
        static $key = null;
        if (null !== $key) return $key;
        $dir = (defined('ROOT_PATH') ? ROOT_PATH : './') . 'data/conf';
        $file = $dir . '/aiservice.key.php';
        if (is_file($file)) {
            $lines = file($file);
            $hex = trim(isset($lines[1]) ? $lines[1] : '');
            if (64 === strlen($hex) && ctype_xdigit($hex)) {
                $key = pack('H*', $hex);
                return $key;
            }
        }
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
        $hex = bin2hex(self::randomBytes(32));
        @file_put_contents($file, "<?php exit('Access Denied'); ?>\n" . $hex . "\n");
        $key = pack('H*', $hex);
        return $key;
    }

    private static function randomBytes($len)
    {
        if (function_exists('random_bytes')) {
            try {
                return random_bytes($len);
            } catch (\Exception $e) {
            }
        }
        $strong = false;
        $bytes = openssl_random_pseudo_bytes($len, $strong);
        if (false !== $bytes) return $bytes;
        $out = '';
        for ($i = 0; $i < $len; $i++) $out .= chr(mt_rand(0, 255));
        return $out;
    }

    private static function clientIp()
    {
        if (function_exists('request')) {
            try {
                return request()->ip();
            } catch (\Exception $e) {
            }
        }
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }
}
