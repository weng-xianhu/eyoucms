<?php

namespace think\agent\driver;

class Driver
{
    /**
     * @access public
     */
    static public function check_author_ization()
    {
        static $request = null;
        null == $request && $request = \think\Request::instance();

        if(!stristr($request->baseFile(), 'index.php')) {
            !class_exists('\\think\\db\\driver\\Driver') && tp_die();
            !class_exists('\\think\\coding\\Driver') && tp_die();
        }

        $tmpbase64 = 'aXNzZXRfYXV0aG9y';
        $isset_session = session(base64_decode($tmpbase64));
        if(!empty($isset_session) && !isset($_REQUEST['close'.'_web'])) {
            return false;
        }
        
        session(base64_decode($tmpbase64), 1);

        // 云插件开关
        $tmpPlugin = 'cGhwX3dlYXBwX3BsdWdpbl9vcGVu';
        $tmpPlugin = base64_decode($tmpPlugin);
        $tmpMeal = 'cGhwX3NlcnZpY2VtZWFs';
        $tmpMeal = base64_decode($tmpMeal);
        $tmpSerInfo = 'cGhwX3NlcnZpY2VpbmZv';
        $tmpSerInfo = base64_decode($tmpSerInfo);
        $tmpSerCode = 'cGhwX3NlcnZpY2Vjb2Rl';
        $tmpSerCode = base64_decode($tmpSerCode);

        $web_basehost = $request->host(true);
        if (false !== filter_var($web_basehost, FILTER_VALIDATE_IP) || file_exists('./data/conf/multidomain.txt')) {
            $web_basehost = tpCache('web.web_basehost');
        }
        $web_basehost = preg_replace('/^(http(s)?:)?(\/\/)?([^\/\:]*)(.*)$/i', '${4}', $web_basehost);

        /*数组键名*/
        $arrKey = array_join_string(array('fmNsaWVudF9kb21haW5+'));
        $arrKey = msubstr($arrKey, 1, strlen($arrKey) - 2);
        /*--end*/
        $values = array(
            $arrKey => urldecode($web_basehost),
            'agentcode' => 1,
            'ip'    => serverIP(),
            'mid'    => tpCache('php.php_eyou_auth_info'),
            'root_dir'  => ROOT_DIR,
        );
        $upgradeLogic = new \app\admin\logic\UpgradeLogic;
        $upgradeLogic->GetKeyData($values);
        $url = $upgradeLogic->getServiceUrl()."/index.php?m=api&c=Service&a=get_authortoken&".http_build_query($values);
        $context = stream_context_set_default(array('http' => array('timeout' => 2,'method'=>'GET')));
        $response = @file_get_contents($url, false, $context);
        // $url = $upgradeLogic->getServiceUrl()."/index.php?m=api&c=Service&a=get_authortoken";
        // $response = @httpRequest2($url, 'POST', $values, [], 5);
        $params = json_decode($response, true);
        

        $iseyKey = binaryJoinChar(config('binary.9'), 20);
        $iseyKey = msubstr($iseyKey, 1, strlen($iseyKey) - 2);
        $session_key2 = binaryJoinChar(config('binary.13'), 24);
        session($session_key2, 0); // 是

        $tmpBlack = 'cGhwX2V5b3Vf'.'YmxhY2tsaXN0';
        $tmpBlack = base64_decode($tmpBlack);

        /*多语言*/
        if (is_language()) {
            $langRow = \think\Db::name('language')->order('id asc')->select();
            foreach ($langRow as $key => $val) {
                tpCache('web', [$iseyKey=>0], $val['mark']); // 是
                tpCache('php', [$tmpBlack=>'',$tmpPlugin=>0], $val['mark']); // 是
            }
        } else { // 单语言
            tpCache('web', [$iseyKey=>0]); // 是
            tpCache('php', [$tmpBlack=>'',$tmpPlugin=>0]); // 是
        }
        /*--end*/

        if (is_array($params) && $params['errcode'] == 0) {

            if (!empty($params['info'])) {
                $tpCacheData = [];
                isset($params['info']) && $tpCacheData[$tmpSerInfo] = mchStrCode(json_encode($params['info']));
                $tpCacheData[$tmpMeal] = !empty($params['info']['pid']) ? $params['info']['pid'] : 0;
                isset($params['info']['weapp_plugin_open']) && $tpCacheData[$tmpPlugin] = $params['info']['weapp_plugin_open'];
                $tpCacheData[$tmpSerCode] = !empty($params['info']['code']) ? $params['info']['code'] : '';
                isset($params['info']['auth_info']) && $tpCacheData['php_auth_function'] = $params['info']['auth_info'];
                if (!empty($tpCacheData)) {
                    /*多语言*/
                    if (is_language()) {
                        $langRow = \think\Db::name('language')->order('id asc')->select();
                        foreach ($langRow as $key => $val) {
                            tpCache('php', $tpCacheData, $val['mark']); // 否
                        }
                    } else { // 单语言
                        tpCache('php', $tpCacheData); // 否
                    }
                    /*--end*/

                    // 云插件库开关
                    $file = "./data/conf/weapp_plugin_open.txt";
                    $fp = fopen($file, "w+");
                    if (!empty($fp)) {
                        fwrite($fp, $tpCacheData[$tmpPlugin]);
                    }
                    fclose($fp);
                }
            }

            if (empty($params['info']['code'])) {
                /*多语言*/
                if (is_language()) {
                    $langRow = \think\Db::name('language')->order('id asc')->select();
                    foreach ($langRow as $key => $val) {
                        tpCache('web', [$iseyKey=>-1], $val['mark']); // 否
                        tpCache('php', [$tmpMeal=>0], $val['mark']); // 否
                    }
                } else { // 单语言
                    tpCache('web', [$iseyKey=>-1]); // 否
                    tpCache('php', [$tmpMeal=>0]); // 否
                }
                /*--end*/
                session($session_key2, -1); // 只在Base用
                return true;
            }
        } else {
            try {
                $version = getVersion();
                if (preg_match('/^v(\d+)\.(\d+)\.(\d+)_(.*)$/i', $version)) {
                    $paginate_type = str_replace(['jsonpR','turn'], ['','y_'], config('default_jsonp_handler'));
                    $filename = strtoupper(md5($paginate_type.$version));
                    $file = "./data/conf/{$filename}.txt";
                    $tmpMealValue = file_exists($file) ? 2 : 0;
                    tpCache('php', [$tmpMeal=>$tmpMealValue]);
                }
            } catch (\Exception $e) {}
        }
        if (is_array($params) && $params['errcode'] == 10002) {
            $ctl_act_list = array(
                // 'index_index',
                // 'index_welcome',
                // 'upgrade_welcome',
                // 'system_index',
            );
            $ctl_act_str = strtolower($request->controller()).'_'.strtolower($request->action());
            if(in_array($ctl_act_str, $ctl_act_list))  
            {

            } else {
                session(base64_decode($tmpbase64), null);

                /*多语言*/
                $tmpval = 'EL+#$JK'.base64_encode($params['errmsg']).'WENXHSK#0m3s';
                if (is_language()) {
                    $langRow = \think\Db::name('language')->order('id asc')->select();
                    foreach ($langRow as $key => $val) {
                        tpCache('php', [$tmpBlack=>$tmpval], $val['mark']); // 是
                    }
                } else { // 单语言
                    tpCache('php', [$tmpBlack=>$tmpval]); // 是
                }
                /*--end*/

                die($params['errmsg']);
            }
        }

        return true;
    }
}
