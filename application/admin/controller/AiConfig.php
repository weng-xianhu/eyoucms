<?php
/**
 * AI 模型配置中心（统一模型接入层后台）
 * 入口：功能地图 > 高级扩展 > AI模型配置
 * 服务商管理 + 按能力的模型管理 + 连通测试 + 限流/预算 + 今日用量
 */

namespace app\admin\controller;

use think\Db;
use app\common\util\AiService;

class AiConfig extends Base
{
    public function _initialize()
    {
        parent::_initialize();
        $this->language_access(); // 多语言功能操作权限
        AiService::ensureTables();
    }

    /** 记录最近一次连通测试结果（$table：ai_provider / ai_model） */
    private function recordTestResult($id, $result, $table = 'ai_provider')
    {
        if ($id <= 0) return;
        try {
            Db::name($table)->where('id', $id)->update([
                'last_test_status' => !empty($result['ok']) ? 1 : 2,
                'last_test_time' => time(),
                'last_test_latency' => intval($result['latency_ms']),
            ]);
        } catch (\Exception $e) {
        }
    }

    public function index()
    {
        $providers = Db::name('ai_provider')->where('kind', 'model')->order('id asc')->select();
        foreach ($providers as &$p) {
            $plain = AiService::decryptKey($p['api_key']);
            $p['key_mask'] = AiService::maskKey($plain);
            $p['api_key'] = '';
            $p['model_count'] = Db::name('ai_model')->where('provider_id', $p['id'])->count();
        }
        unset($p);

        $models = Db::name('ai_model')->order('capability asc, id asc')->select();
        $pmap = [];
        foreach ($providers as $p) $pmap[$p['id']] = $p;
        foreach ($models as &$m) {
            $m['provider_name'] = isset($pmap[$m['provider_id']]) ? $pmap[$m['provider_id']]['name'] : '（服务商已删除）';
            $m['provider_status'] = isset($pmap[$m['provider_id']]) ? intval($pmap[$m['provider_id']]['status']) : 0;
            $m['params'] = !empty($m['params']) ? $m['params'] : '';
        }
        unset($m);

        $this->assign('providers', $providers);
        $this->assign('models', $models);
        $this->assign('presets_json', json_encode(AiService::presets(), JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT));
        $this->assign('settings', AiService::settings());
        $this->assign('usage', AiService::usageToday());
        $this->assign('sdk_version', AiService::VERSION);
        $this->assign('chat_ready', AiService::hasCapability('chat') ? 1 : 0);
        $this->assign('image_ready', AiService::hasCapability('image') ? 1 : 0);

        // 第三方素材服务（AI 版专属）
        $isAiVer = AiService::isAiEdition() ? 1 : 0;
        $stocks = [];
        $stockReady = 0;
        if ($isAiVer) {
            $stocks = Db::name('ai_provider')->where('kind', 'stock')->order('id asc')->select();
            foreach ($stocks as &$s) {
                $plain = AiService::decryptKey($s['api_key']);
                $s['key_mask'] = AiService::maskKey($plain);
                $s['api_key'] = '';
                if (intval($s['status']) == 1) $stockReady = 1;
            }
            unset($s);
        }
        $this->assign('is_ai_ver', $isAiVer);
        $this->assign('stocks', $stocks);
        $this->assign('stock_ready', $stockReady);
        $this->assign('stock_presets_json', json_encode(AiService::stockPresets(), JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT));
        return $this->fetch('index');
    }

    /** 新增/编辑服务商（Key 留空 = 保持原值） */
    public function provider_save()
    {
        if (!IS_POST) $this->error('非法请求');
        $id = input('post.id/d', 0);
        $name = trim(input('post.name/s', ''));
        $type = trim(input('post.type/s', 'openai_compat'));
        $baseUrl = trim(input('post.base_url/s', ''));
        $apiKey = trim(input('post.api_key/s', ''));
        $status = input('post.status/d', 1) ? 1 : 0;

        if ('' === $name) $this->error('请填写服务商名称');
        $presets = AiService::presets();
        if (!isset($presets[$type])) $this->error('无效的服务商类型');
        if ('' === $baseUrl && !empty($presets[$type]['base_url'])) $baseUrl = $presets[$type]['base_url'];
        if ('' === $baseUrl || !preg_match('#^https?://#i', $baseUrl)) $this->error('接口地址必须是 http(s) 开头的完整地址');

        $data = [
            'name' => $name,
            'type' => $type,
            'base_url' => rtrim($baseUrl, '/'),
            'status' => $status,
            'update_time' => time(),
        ];
        if ($id > 0) {
            $row = Db::name('ai_provider')->where('id', $id)->find();
            if (empty($row)) $this->error('服务商不存在');
            if ('' !== $apiKey) $data['api_key'] = AiService::encryptKey($apiKey);
            Db::name('ai_provider')->where('id', $id)->update($data);
        } else {
            if ('' === $apiKey) $this->error('请填写 API Key');
            $data['api_key'] = AiService::encryptKey($apiKey);
            $data['add_time'] = time();
            $id = Db::name('ai_provider')->insertGetId($data);
        }
        $this->success('保存成功', null, ['id' => $id]);
    }

    /** 删除服务商（其下有模型时先阻止） */
    public function provider_del()
    {
        if (!IS_POST) $this->error('非法请求');
        $id = input('post.id/d', 0);
        if ($id <= 0) $this->error('参数错误');
        $count = Db::name('ai_model')->where('provider_id', $id)->count();
        if ($count > 0) $this->error("该服务商下还有 {$count} 个模型，请先删除模型");
        Db::name('ai_provider')->where('id', $id)->delete();
        $this->success('删除成功');
    }

    /** 测试连通（已保存的服务商用存储 Key；也支持临时传 Key 保存前先测） */
    public function provider_test()
    {
        if (!IS_POST) $this->error('非法请求');
        $id = input('post.id/d', 0);
        $type = trim(input('post.type/s', 'openai_compat'));
        $baseUrl = trim(input('post.base_url/s', ''));
        $apiKey = trim(input('post.api_key/s', ''));
        $model = trim(input('post.model/s', ''));

        if ($id > 0) {
            $row = Db::name('ai_provider')->where('id', $id)->find();
            if (empty($row)) $this->error('服务商不存在');
            $type = $row['type'];
            if ('' === $baseUrl) $baseUrl = $row['base_url'];
            if ('' === $apiKey) $apiKey = AiService::decryptKey($row['api_key']);
        }
        if ('' === $model) {
            $presets = AiService::presets();
            if (!empty($presets[$type]['models']['chat'][0])) $model = $presets[$type]['models']['chat'][0];
        }
        if ('' === $model) $this->error('请填写用于测试的模型名称');
        if ('' === $apiKey) $this->error('请填写 API Key');

        $result = AiService::testProvider([
            'type' => $type,
            'base_url' => $baseUrl,
            'api_key' => $apiKey,
            'model' => $model,
        ]);
        $this->recordTestResult($id, $result);
        if ($result['ok']) {
            $this->success($result['msg'] . "（{$result['latency_ms']}ms）");
        }
        $this->error('连通失败：' . $result['msg'] . "（{$result['latency_ms']}ms）");
    }

    /** 新增/编辑模型 */
    public function model_save()
    {
        if (!IS_POST) $this->error('非法请求');
        $id = input('post.id/d', 0);
        $providerId = input('post.provider_id/d', 0);
        $capability = trim(input('post.capability/s', 'chat'));
        $model = trim(input('post.model/s', ''));
        $paramsRaw = trim(input('post.params/s', ''));
        $isDefault = input('post.is_default/d', 0) ? 1 : 0;
        $status = input('post.status/d', 1) ? 1 : 0;

        if (!in_array($capability, AiService::$capabilities)) $this->error('无效的能力类型');
        if (!in_array($capability, ['chat', 'image'])) $this->error('生视频/向量能力将在后续版本开放');
        if ('' === $model) $this->error('请填写模型名称');
        $provider = Db::name('ai_provider')->where('id', $providerId)->find();
        if (empty($provider)) $this->error('请选择服务商');
        $presets = AiService::presets();
        if ('openai_compat' !== $provider['type'] && isset($presets[$provider['type']]) && empty($presets[$provider['type']]['models'][$capability])) {
            $note = isset($presets[$provider['type']]['notes'][$capability]) ? $presets[$provider['type']]['notes'][$capability] : '该服务商不支持此能力';
            $this->error($note);
        }
        if ('' !== $paramsRaw) {
            $params = json_decode($paramsRaw, true);
            if (!is_array($params)) $this->error('参数必须是合法的 JSON 对象，如 {"temperature":0.7}');
            $paramsRaw = json_encode($params, JSON_UNESCAPED_UNICODE);
        }

        $data = [
            'provider_id' => $providerId,
            'capability' => $capability,
            'model' => $model,
            'params' => $paramsRaw,
            'is_default' => $isDefault,
            'status' => $status,
            'update_time' => time(),
        ];
        if ($id > 0) {
            $row = Db::name('ai_model')->where('id', $id)->find();
            if (empty($row)) $this->error('模型不存在');
            Db::name('ai_model')->where('id', $id)->update($data);
        } else {
            $data['add_time'] = time();
            // 该能力首个模型自动设为默认
            if (0 === intval(Db::name('ai_model')->where('capability', $capability)->count())) $data['is_default'] = 1;
            $id = Db::name('ai_model')->insertGetId($data);
        }
        if ($isDefault) {
            Db::name('ai_model')->where('capability', $capability)->where('id', '<>', $id)->update(['is_default' => 0]);
        }
        $this->success('保存成功', null, ['id' => $id]);
    }

    /** 删除模型 */
    public function model_del()
    {
        if (!IS_POST) $this->error('非法请求');
        $id = input('post.id/d', 0);
        if ($id <= 0) $this->error('参数错误');
        Db::name('ai_model')->where('id', $id)->delete();
        $this->success('删除成功');
    }

    /** 设为该能力默认模型 */
    public function model_default()
    {
        if (!IS_POST) $this->error('非法请求');
        $id = input('post.id/d', 0);
        $row = Db::name('ai_model')->where('id', $id)->find();
        if (empty($row)) $this->error('模型不存在');
        Db::name('ai_model')->where('capability', $row['capability'])->update(['is_default' => 0]);
        Db::name('ai_model')->where('id', $id)->update(['is_default' => 1, 'status' => 1, 'update_time' => time()]);
        $this->success('已设为默认模型');
    }

    /** 单个模型测试（用该模型真实发一句 hello） */
    public function model_test()
    {
        if (!IS_POST) $this->error('非法请求');
        $id = input('post.id/d', 0);
        $row = Db::name('ai_model')->where('id', $id)->find();
        if (empty($row)) $this->error('模型不存在');
        $provider = Db::name('ai_provider')->where('id', $row['provider_id'])->find();
        if (empty($provider)) $this->error('模型所属服务商不存在');
        $conf = [
            'type' => $provider['type'],
            'base_url' => $provider['base_url'],
            'api_key' => AiService::decryptKey($provider['api_key']),
            'model' => $row['model'],
        ];
        $result = 'image' === $row['capability'] ? AiService::testImage($conf) : AiService::testProvider($conf);
        $this->recordTestResult(intval($provider['id']), $result);
        $this->recordTestResult($id, $result, 'ai_model');
        if ($result['ok']) {
            $this->success($result['msg'] . "（{$result['latency_ms']}ms）", null, ['url' => isset($result['url']) ? $result['url'] : '']);
        }
        $this->error('测试失败：' . $result['msg'] . "（{$result['latency_ms']}ms）");
    }

    /** 素材服务操作前置：仅 AI 版授权可用 */
    private function checkStockAccess()
    {
        if (!AiService::isAiEdition()) {
            $this->error('第三方素材服务仅限 AI 版授权使用');
        }
    }

    /** 新增/编辑素材服务（Key 留空 = 保持原值） */
    public function stock_save()
    {
        if (!IS_POST) $this->error('非法请求');
        $this->checkStockAccess();
        $id = input('post.id/d', 0);
        $type = trim(input('post.type/s', ''));
        $apiKey = trim(input('post.api_key/s', ''));
        $status = input('post.status/d', 1) ? 1 : 0;

        $presets = AiService::stockPresets();
        if (!isset($presets[$type])) $this->error('无效的素材服务类型');

        $data = [
            'name' => $presets[$type]['name'],
            'type' => $type,
            'base_url' => $presets[$type]['base_url'],
            'kind' => 'stock',
            'status' => $status,
            'update_time' => time(),
        ];
        if ($id > 0) {
            $row = Db::name('ai_provider')->where('id', $id)->where('kind', 'stock')->find();
            if (empty($row)) $this->error('素材服务不存在');
            if ('' !== $apiKey) $data['api_key'] = AiService::encryptKey($apiKey);
            Db::name('ai_provider')->where('id', $id)->update($data);
        } else {
            if ('' === $apiKey) $this->error('请填写 API Key');
            $exists = Db::name('ai_provider')->where('kind', 'stock')->where('type', $type)->count();
            if ($exists > 0) $this->error('该素材服务已添加，请直接编辑');
            $data['api_key'] = AiService::encryptKey($apiKey);
            $data['add_time'] = time();
            $id = Db::name('ai_provider')->insertGetId($data);
        }
        $this->success('保存成功', null, ['id' => $id]);
    }

    /** 删除素材服务 */
    public function stock_del()
    {
        if (!IS_POST) $this->error('非法请求');
        $this->checkStockAccess();
        $id = input('post.id/d', 0);
        if ($id <= 0) $this->error('参数错误');
        Db::name('ai_provider')->where('id', $id)->where('kind', 'stock')->delete();
        $this->success('删除成功');
    }

    /** 测试素材服务（真实搜 1 张图验证 Key） */
    public function stock_test()
    {
        if (!IS_POST) $this->error('非法请求');
        $this->checkStockAccess();
        $id = input('post.id/d', 0);
        $type = trim(input('post.type/s', ''));
        $apiKey = trim(input('post.api_key/s', ''));

        $baseUrl = '';
        if ($id > 0) {
            $row = Db::name('ai_provider')->where('id', $id)->where('kind', 'stock')->find();
            if (empty($row)) $this->error('素材服务不存在');
            $type = $row['type'];
            $baseUrl = $row['base_url'];
            if ('' === $apiKey) $apiKey = AiService::decryptKey($row['api_key']);
        }
        if ('' === $apiKey) $this->error('请填写 API Key');

        $result = AiService::testStock([
            'type' => $type,
            'base_url' => $baseUrl,
            'api_key' => $apiKey,
        ]);
        $this->recordTestResult($id, $result);
        if ($result['ok']) {
            $this->success($result['msg'] . "（{$result['latency_ms']}ms）");
        }
        $this->error('测试失败：' . $result['msg'] . "（{$result['latency_ms']}ms）");
    }

    /**
     * 扫描已安装插件中的服务商 Key（白名单适配，只读已知位置，不返回明文）
     */
    public function key_scan()
    {
        if (!IS_POST) $this->error('非法请求');
        $items = $this->collectPluginKeys();
        $list = [];
        foreach ($items as $it) {
            $list[] = [
                'key' => $it['key'],
                'source' => $it['source'],
                'kind' => $it['kind'],
                'name' => preg_replace('/（来自[^）]*）/u', '', $it['name']),
                'base_url' => $it['base_url'],
                'key_mask' => AiService::maskKey($it['api_key']),
                'dup' => $it['dup'] ? 1 : 0,
                'blocked' => $it['blocked'],
            ];
        }
        $this->success('扫描完成', null, ['list' => $list]);
    }

    /** 按用户勾选导入插件里的 Key（加密入库，原插件配置不改动） */
    public function key_import()
    {
        if (!IS_POST) $this->error('非法请求');
        $keys = input('post.keys/a', []);
        if (empty($keys) || !is_array($keys)) $this->error('请勾选要导入的配置');
        $keys = array_map('strval', $keys);

        $items = $this->collectPluginKeys();
        $imported = 0;
        $skipped = 0;
        foreach ($items as $it) {
            if (!in_array($it['key'], $keys, true)) continue;
            if ($it['dup'] || '' !== $it['blocked']) { $skipped++; continue; }
            $data = [
                'name' => $it['name'],
                'type' => $it['type'],
                'base_url' => rtrim($it['base_url'], '/'),
                'kind' => $it['kind'],
                'status' => 1,
                'api_key' => AiService::encryptKey($it['api_key']),
                'add_time' => time(),
                'update_time' => time(),
            ];
            $id = Db::name('ai_provider')->insertGetId($data);
            $imported++;
        }
        if ($imported <= 0) $this->error('没有可导入的配置（已存在或不满足条件的会自动跳过）');
        $msg = "已导入 {$imported} 条";
        if ($skipped > 0) $msg .= "，跳过 {$skipped} 条";
        $this->success($msg);
    }

    /**
     * 白名单收集各插件中的服务商 Key（内部使用，含明文，勿直接输出）
     * 返回项：key/source/kind/type/name/base_url/api_key/dup/blocked
     */
    private function collectPluginKeys()
    {
        $presets = AiService::presets();
        $items = [];

        $weappData = function ($code) {
            try {
                $data = Db::name('weapp')->where('code', $code)->value('data');
                $arr = !empty($data) ? json_decode($data, true) : [];
                return is_array($arr) ? $arr : [];
            } catch (\Exception $e) {
                return [];
            }
        };

        // AiHelper（AI智能客服）：豆包 Key
        $d = $weappData('AiHelper');
        if (!empty($d['doubao_api_key'])) {
            $items[] = [
                'key' => 'aihelper.doubao',
                'source' => 'AI智能客服（AiHelper）',
                'kind' => 'model',
                'type' => 'doubao',
                'name' => $presets['doubao']['name'] . '（来自AI智能客服）',
                'base_url' => $presets['doubao']['base_url'],
                'api_key' => trim($d['doubao_api_key']),
            ];
        }

        // Suanming（AI趣味运势）：DeepSeek Key
        $d = $weappData('Suanming');
        if (!empty($d['deepseek_api_key'])) {
            $items[] = [
                'key' => 'suanming.deepseek',
                'source' => 'AI趣味运势（Suanming）',
                'kind' => 'model',
                'type' => 'deepseek',
                'name' => $presets['deepseek']['name'] . '（来自AI趣味运势）',
                'base_url' => $presets['deepseek']['base_url'],
                'api_key' => trim($d['deepseek_api_key']),
            ];
        }

        // Sitecollect（文章采集）：OpenAI 兼容配置（接口地址+Key+模型）
        $d = $weappData('Sitecollect');
        $conf = !empty($d['ai_collect']) && is_array($d['ai_collect']) ? $d['ai_collect'] : [];
        if (!empty($conf['api_key'])) {
            $baseUrl = !empty($conf['base_url']) ? rtrim(trim($conf['base_url']), '/') : $presets['deepseek']['base_url'];
            $type = 'openai_compat';
            $name = '自定义（来自文章采集）';
            foreach ($presets as $ptype => $p) {
                if (!empty($p['base_url']) && 0 === stripos($baseUrl, rtrim($p['base_url'], '/'))) {
                    $type = $ptype;
                    $name = $p['name'] . '（来自文章采集）';
                    $baseUrl = rtrim($p['base_url'], '/');
                    break;
                }
            }
            $items[] = [
                'key' => 'sitecollect.ai',
                'source' => '文章采集（Sitecollect）',
                'kind' => 'model',
                'type' => $type,
                'name' => $name,
                'base_url' => $baseUrl,
                'api_key' => trim($conf['api_key']),
            ];
        }

        // Ai（AI智能创作平台）：weapp.data 多服务商 Key + 自定义模型 + OpenAI Key 池
        if (is_dir(ROOT_PATH . 'weapp/Ai/')) {
            $d = $weappData('Ai');
            $aiMap = [
                'deepseek_api_key' => 'deepseek',
                'doubao_api_key'   => 'doubao',
                'aliyun_api_key'   => 'qwen',
                'zhipu_api_key'    => 'zhipu',
            ];
            foreach ($aiMap as $field => $ptype) {
                if (empty($d[$field])) continue;
                $items[] = [
                    'key' => 'aiplat.' . $ptype,
                    'source' => 'AI智能创作平台（Ai）',
                    'kind' => 'model',
                    'type' => $ptype,
                    'name' => $presets[$ptype]['name'] . '（来自AI智能创作平台）',
                    'base_url' => $presets[$ptype]['base_url'],
                    'api_key' => trim($d[$field]),
                ];
            }
            if (!empty($d['moonshot_api_key'])) {
                $items[] = [
                    'key' => 'aiplat.moonshot',
                    'source' => 'AI智能创作平台（Ai）',
                    'kind' => 'model',
                    'type' => 'moonshot',
                    'name' => $presets['moonshot']['name'] . '（来自AI智能创作平台）',
                    'base_url' => $presets['moonshot']['base_url'],
                    'api_key' => trim($d['moonshot_api_key']),
                ];
            }
            if (!empty($d['spark_api_password'])) {
                $items[] = [
                    'key' => 'aiplat.spark',
                    'source' => 'AI智能创作平台（Ai）',
                    'kind' => 'model',
                    'type' => 'spark',
                    'name' => $presets['spark']['name'] . '（来自AI智能创作平台）',
                    'base_url' => $presets['spark']['base_url'],
                    'api_key' => trim($d['spark_api_password']),
                ];
            }
            if (!empty($d['custom_models']) && is_array($d['custom_models'])) {
                foreach ($d['custom_models'] as $cm) {
                    if (empty($cm['api_key']) || empty($cm['base_url'])) continue;
                    $cmName = !empty($cm['name']) ? $cm['name'] : '自定义模型';
                    $items[] = [
                        'key' => 'aiplat.custom.' . intval($cm['id']),
                        'source' => 'AI智能创作平台（Ai）',
                        'kind' => 'model',
                        'type' => 'openai_compat',
                        'name' => $cmName . '（来自AI智能创作平台）',
                        'base_url' => rtrim(trim($cm['base_url']), '/'),
                        'api_key' => trim($cm['api_key']),
                    ];
                }
            }
            // OpenAI Key 池（weapp_ai_key_list，version 1/2/3 同为官方 OpenAI Key）
            try {
                $rows = Db::name('weapp_ai_key_list')->where('apiKey', 'neq', '')->order('id asc')->select();
                $seen = [];
                foreach ($rows as $r) {
                    $k = trim($r['apiKey']);
                    if ('' === $k || isset($seen[$k])) continue;
                    $seen[$k] = true;
                    $items[] = [
                        'key' => 'aiplat.openai.' . $r['id'],
                        'source' => 'AI智能创作平台（Ai）',
                        'kind' => 'model',
                        'type' => 'openai_compat',
                        'name' => 'OpenAI（来自AI智能创作平台）',
                        'base_url' => 'https://api.openai.com/v1',
                        'api_key' => $k,
                    ];
                }
            } catch (\Exception $e) {
            }
        }

        // Airewrite（Ai二次创作）：OpenAI Key 池（支持中转域名）
        if (is_dir(ROOT_PATH . 'weapp/Airewrite/')) {
            $d = $weappData('Airewrite');
            $rwBase = 'https://api.openai.com/v1';
            if (!empty($d['proxy_domain'])) {
                $pd = rtrim(trim($d['proxy_domain']), '/');
                if (preg_match('#^https?://#i', $pd)) $rwBase = $pd;
            }
            try {
                $rows = Db::name('weapp_airewrite_key_list')->where('apiKey', 'neq', '')->order('id asc')->select();
                $seen = [];
                foreach ($rows as $r) {
                    $k = trim($r['apiKey']);
                    if ('' === $k || isset($seen[$k])) continue;
                    $seen[$k] = true;
                    $items[] = [
                        'key' => 'airewrite.openai.' . $r['id'],
                        'source' => 'Ai二次创作（Airewrite）',
                        'kind' => 'model',
                        'type' => 'openai_compat',
                        'name' => 'OpenAI（来自Ai二次创作）',
                        'base_url' => $rwBase,
                        'api_key' => $k,
                    ];
                }
            } catch (\Exception $e) {
            }
        }

        // BrandPage（AI城市获客）：独立配置表 DeepSeek / 通义 / Pexels
        $bp = [];
        try {
            $rows = Db::name('weapp_brandpage_config')
                ->where('config_key', 'in', ['deepseek_api_key', 'deepseek_api_url', 'qwen_api_key', 'qwen_api_url', 'pexels_api_key'])
                ->select();
            foreach ($rows as $r) $bp[$r['config_key']] = $r['config_value'];
        } catch (\Exception $e) {
        }
        $bpUrl = function ($url, $fallback) {
            $url = trim((string)$url);
            if ('' === $url) return $fallback;
            $url = preg_replace('#/chat/completions/?$#i', '', rtrim($url, '/'));
            return preg_match('#^https?://#i', $url) ? $url : $fallback;
        };
        if (!empty($bp['deepseek_api_key'])) {
            $items[] = [
                'key' => 'brandpage.deepseek',
                'source' => 'AI城市获客（BrandPage）',
                'kind' => 'model',
                'type' => 'deepseek',
                'name' => $presets['deepseek']['name'] . '（来自AI城市获客）',
                'base_url' => $bpUrl(isset($bp['deepseek_api_url']) ? $bp['deepseek_api_url'] : '', $presets['deepseek']['base_url']),
                'api_key' => trim($bp['deepseek_api_key']),
            ];
        }
        if (!empty($bp['qwen_api_key'])) {
            $items[] = [
                'key' => 'brandpage.qwen',
                'source' => 'AI城市获客（BrandPage）',
                'kind' => 'model',
                'type' => 'qwen',
                'name' => $presets['qwen']['name'] . '（来自AI城市获客）',
                'base_url' => $bpUrl(isset($bp['qwen_api_url']) ? $bp['qwen_api_url'] : '', $presets['qwen']['base_url']),
                'api_key' => trim($bp['qwen_api_key']),
            ];
        }
        if (!empty($bp['pexels_api_key'])) {
            $stockPresets = AiService::stockPresets();
            $items[] = [
                'key' => 'brandpage.pexels',
                'source' => 'AI城市获客（BrandPage）',
                'kind' => 'stock',
                'type' => 'pexels',
                'name' => $stockPresets['pexels']['name'],
                'base_url' => $stockPresets['pexels']['base_url'],
                'api_key' => trim($bp['pexels_api_key']),
            ];
        }

        // 重复检测 + 授权限制
        $isAiVer = AiService::isAiEdition();
        $existing = [];
        try {
            $rows = Db::name('ai_provider')->field('type,kind,api_key')->select();
            foreach ($rows as $r) {
                $existing[] = [
                    'type' => $r['type'],
                    'kind' => !empty($r['kind']) ? $r['kind'] : 'model',
                    'plain' => AiService::decryptKey($r['api_key']),
                ];
            }
        } catch (\Exception $e) {
        }
        foreach ($items as &$it) {
            $it['dup'] = false;
            $it['blocked'] = '';
            if ('' === $it['api_key']) {
                $it['blocked'] = 'Key 为空';
                continue;
            }
            foreach ($existing as $ex) {
                if ($ex['kind'] !== $it['kind']) continue;
                if ('stock' === $it['kind'] && $ex['type'] === $it['type']) { $it['dup'] = true; break; }
                if ($ex['type'] === $it['type'] && $ex['plain'] === $it['api_key']) { $it['dup'] = true; break; }
            }
            if ('stock' === $it['kind'] && !$isAiVer) {
                $it['blocked'] = '素材服务需 AI 版授权';
            }
        }
        unset($it);
        return $items;
    }

    /** 保存限流/预算设置 */
    public function settings_save()
    {
        if (!IS_POST) $this->error('非法请求');
        $data = [
            'aiservice_rate_limit' => max(0, input('post.aiservice_rate_limit/d', 30)),
            'aiservice_budget_chat' => max(0, input('post.aiservice_budget_chat/d', 0)),
            'aiservice_budget_image' => max(0, input('post.aiservice_budget_image/d', 0)),
            'aiservice_log_days' => max(0, input('post.aiservice_log_days/d', 90)),
        ];
        /*多语言*/
        if (is_language()) {
            $langRow = Db::name('language')->order('id asc')
                ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                ->select();
            foreach ($langRow as $val) {
                tpCache('aiservice', $data, $val['mark']);
            }
        } else {
            tpCache('aiservice', $data);
        }
        /*--end*/
        $this->success('保存成功');
    }
}
