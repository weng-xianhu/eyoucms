<?php

namespace think\db\driver;

class Driver
{
    static public function reset_copy_right()
    {
        static $request = null;
        null == $request && $request = \think\Request::instance();
        if ($request->module() == 'home' && $request->controller() == 'Index' && $request->action() == 'index') {
            $tmpArray = array('I19jbXNjb3B5cmlnaHR+');
            $cname = array_join_string($tmpArray);
            $cname = msubstr($cname, 1, strlen($cname) - 2);
            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->order('id asc')->select();
                foreach ($langRow as $key => $val) {
                    tpCache('php', [$cname=>''], $val['mark']);
                }
            } else { // 单语言
                tpCache('php', [$cname=>'']);
            }
            /*--end*/
        }
    }

    static public function set_copy_right($name)
    {
        static $globalTpCache = null;
        null === $globalTpCache && $globalTpCache = tpCache('global');
        $value = isset($globalTpCache[$name]) ? $globalTpCache[$name] : '';

        $tmpName = binaryJoinChar(config('binary.8'), 15);
        $tmpName = msubstr($tmpName, 1, strlen($tmpName) - 2);

        if ($name == $tmpName) {
            static $request = null;
            null == $request && $request = \think\Request::instance();
            if ($request->module() == 'home' && $request->controller() == 'Index' && $request->action() == 'index') {
                $tmpArray = array('I19jbXNjb3B5cmlnaHR+');
                $cname = array_join_string($tmpArray);
                $cname = msubstr($cname, 1, strlen($cname) - 2);
                $is_cr = tpCache('php.'.$cname);
                if ($name == $tmpName && empty($is_cr)) {
                    /*多语言*/
                    if (is_language()) {
                        $langRow = \think\Db::name('language')->cache(true, EYOUCMS_CACHE_TIME, 'language')
                            ->order('id asc')->select();
                        foreach ($langRow as $key => $val) {
                            tpCache('php', [$cname=>get_rand_str(24, 0, 1)], $val['mark']);
                        }
                    } else { // 单语言
                        tpCache('php', [$cname=>get_rand_str(24, 0, 1)]);
                    }
                    /*--end*/
                }
            }

            $is_author_key = binaryJoinChar(config('binary.9'), 20);
            $is_author_key = msubstr($is_author_key, 1, strlen($is_author_key) - 2);
            if (!empty($globalTpCache[$is_author_key]) && -1 == intval($globalTpCache[$is_author_key])) {
                $value .= binaryJoinChar(config('binary.10'), 89);
            }
        }
        return ['value' => $value, 'data'  => $globalTpCache];
    }

    static public function check_copy_right()
    {
        static $request = null;
        null == $request && $request = \think\Request::instance();
        if ($request->module() != 'admin') {
            $tmpArray = array('I19jbXNjb3B5cmlnaHR+');
            $cname = array_join_string($tmpArray);
            $cname = msubstr($cname, 1, strlen($cname) - 2);
            $val = tpCache('php.'.$cname);
            if (empty($val)) {
                $msg = binaryJoinChar(config('binary.11'), 86);
                $msg = msubstr($msg, 1, -1);
                exception($msg);
            }
        }
    }

    /**
     * @access public
     */
    static public function check_author_ization()
    {
        static $request = null;
        null == $request && $request = \think\Request::instance();

        if(!stristr($request->baseFile(), 'index.php')) {
            $abc = binaryJoinChar(config('binary.36'), 6);
            $ctl1 = binaryJoinChar(config('binary.37'), 23);
            $ctl2 = binaryJoinChar(config('binary.38'), 20);
            !class_exists($ctl1) && $abc();
            !class_exists($ctl2) && $abc();
        }

        $tmpbase64 = 'aXNzZXRfYXV0aG9y';
        $isset_session = session(base64_decode($tmpbase64));
        if(!empty($isset_session) && !isset($_GET['close'.'_web'])) {
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
        $web_basehost = preg_replace('/^(([^\:]+):)?(\/\/)?([^\/\:]*)(.*)$/i', '${4}', $web_basehost);

        $keys = array_join_string(array('fnNlcnZpY2VfZXl+'));
        $keys = msubstr($keys, 1, strlen($keys) - 2);
        $domain = config($keys);
        $domain = base64_decode($domain);
        /*数组键名*/
        $arrKey = array_join_string(array('fmNsaWVudF9kb21haW5+'));
        $arrKey = msubstr($arrKey, 1, strlen($arrKey) - 2);
        /*--end*/
        $vaules = array(
            $arrKey => urldecode($web_basehost),
            'ip'    => serverIP(),
        );
        $query_str = binaryJoinChar(config('binary.12'), 47);
        $query_str = msubstr($query_str, 1, strlen($query_str) - 2);
        $url = $domain.$query_str.http_build_query($vaules);
        $context = stream_context_set_default(array('http' => array('timeout' => 2,'method'=>'GET')));
        $response = @file_get_contents($url,false,$context);
        $params = json_decode($response,true);

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
                $tpCacheData[$tmpSerInfo] = mchStrCode(json_encode($params['info']));
                $tpCacheData[$tmpMeal] = !empty($params['info']['pid']) ? $params['info']['pid'] : 0;
                isset($params['info']['weapp_plugin_open']) && $tpCacheData[$tmpPlugin] = $params['info']['weapp_plugin_open'];
                if (!empty($params['info']['code'])) {
                    $tpCacheData[$tmpSerCode] = $params['info']['code'];
                } else {
                    $tpCacheData[$tmpSerCode] = '';
                    $tpCacheData[$tmpMeal] = 0;
                }
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

                $file = "./data/conf/{$tpCacheData[$tmpSerCode]}.txt";
                if (empty($tpCacheData[$tmpMeal])) {
                    getUsersConfigData('shop', ['shop_open'=>0]);
                } else if (2 <= $tpCacheData[$tmpMeal] && !file_exists($file)) {
                    $fp = fopen($file, "w+");
                    if (!empty($fp)) {
                        fwrite($fp, $tpCacheData[$tmpSerCode]);
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
                    }
                } else { // 单语言
                    tpCache('web', [$iseyKey=>-1]); // 否
                }
                /*--end*/
                session($session_key2, -1); // 只在Base用
                return true;
            }
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
