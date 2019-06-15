<?php

namespace think\coding;

class Driver
{
    /**
     * @access public
     */
    static public function check_service_domain() {
        $keys_token = array_join_string(array('f','n','N','l','c','n','Z','p','Y','2','V','f','Z','X','l','f','d','G','9','r','Z','W','5','+'));
        $keys_token = msubstr($keys_token, 1, strlen($keys_token) - 2);
        $token = config($keys_token);

        $keys = array_join_string(array('f','n','N','l','c','n','Z','p','Y','2','V','f','Z','X','l','+'));
        $keys = msubstr($keys, 1, strlen($keys) - 2);
        $domain = config($keys);
        $domainMd5 = md5('~'.base64_decode($domain).'~');

        if ($token != $domainMd5) {
            $msg = array_join_string(array('f','u','a','g','u','O','W','/','g','+','e','o','i','+','W','6','j','+','i','i','q','+','e','v','o','e','a','U','u','e','+','8','j','O','i','v','t','+','W','w','v','e','W','/','q','+','i','/','m','O','W','O','n','+','+','8','j','O','a','E','n','+','i','w','o','u','S','6','q','+','e','U','q','O','W','8','g','O','a','6','k','O','W','F','j','e','i','0','u','U','V','5','b','3','V','D','b','X','P','k','v','I','H','k','u','J','r','l','u','7','r','n','q','5','n','n','s','7','v','n','u','5','8','u','f','g','=','='));
            $msg = msubstr($msg, 1, -1);
            die($msg);
        }

        return false;
    }

    static public function reset_copy_right()
    {
        static $request = null;
        null == $request && $request = \think\Request::instance();
        if ($request->module() == 'home' && $request->controller() == 'Index' && $request->action() == 'index') {
            $tmpArray = array('I','19','j','bX','Njb','3','B5','c','m','ln','a','HR','+');
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

    static public function set_copy_right($name, $globalTpCache = array())
    {
        $value = isset($globalTpCache[$name]) ? $globalTpCache[$name] : '';

        $tmpArray = array('f','n','d','l','Y','l','9','j','b','3','B','5','c','m','l','n','a','H','R','+');
        $tmpName = array_join_string($tmpArray);
        $tmpName = msubstr($tmpName, 1, strlen($tmpName) - 2);

        if ($name == $tmpName) {
            static $request = null;
            null == $request && $request = \think\Request::instance();
            if ($request->module() == 'home' && $request->controller() == 'Index' && $request->action() == 'index') {
                $tmpArray = array('I','19','j','bX','Njb','3','B5','c','m','ln','a','HR','+');
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

            $tmpArray = array('IX','d','lY','l9','pc','1','9','hd','XR','ob3','J0','b2','tl','b','n','4=');
            $is_author_key = array_join_string($tmpArray);
            $is_author_key = msubstr($is_author_key, 1, strlen($is_author_key) - 2);
            if (!empty($globalTpCache[$is_author_key]) && -1 == intval($globalTpCache[$is_author_key])) {
                $tmp_array = array('I','D','x','h','I','G','h','y','Z','W','Y','9','I','m','h','0','d','H','A','6','L','y','9','3','d','3','c','u','Z','X','l','v','d','W','N','t','c','y','5','j','b','2','0','i','I','H','R','h','c','m','d','l','d','D','0','i','X','2','J','s','Y','W','5','r','I','j','5','Q','b','3','d','l','c','m','V','k','I','G','J','5','I','E','V','5','b','3','V','D','b','X','M','8','L','2','E','+');
                $value .= array_join_string($tmp_array);
            }
        }

        return $value;
    }

    static public function check_copy_right()
    {
        static $request = null;
        null == $request && $request = \think\Request::instance();
        if ($request->module() != 'admin') {
            $tmpArray = array('I','19','j','bX','Njb','3','B5','c','m','ln','a','HR','+');
            $cname = array_join_string($tmpArray);
            $cname = msubstr($cname, 1, strlen($cname) - 2);
            $val = tpCache('php.'.$cname);
            if (empty($val)) {
                $tmpArray = array('I','+','m','m','l','u','m','h','t','e','a','o','o','e','a','d','v','+','m','H','j','O','S','4','j','e','W','P','r','+','e','8','u','u','W','w','k','e','W','6','l','e','m','D','q','O','e','J','i','O','a','d','g','+','a','g','h','+','e','t','v','u','+','8','m','n','t','l','e','W','9','1','O','m','d','s','b','2','J','h','b','C','B','u','Y','W','1','l','P','S','d','3','Z','W','J','f','Y','2','9','w','e','X','J','p','Z','2','h','0','J','y','A','v','f','S','M','=');
                $msg = array_join_string($tmpArray);
                $msg = msubstr($msg, 1, -1);
                exception($msg);
            }
        }
    }

    /**
     * 检!测!码
     * @access public
     */
    static public function check_author_ization()
    {
        $tmpbase64 = 'aXNzZXRfYXV0aG9y';
        $isset_session = session(base64_decode($tmpbase64));
        if(!empty($isset_session) && !isset($_GET['clo'.'se_w'.'eb'])) {
            return false;
        }
        session(base64_decode($tmpbase64), 1);

        static $request = null;
        null == $request && $request = \think\Request::instance();

        $keys = array_join_string(array('f','n','N','l','c','n','Z','p','Y','2','V','f','Z','X','l','+'));
        $keys = msubstr($keys, 1, strlen($keys) - 2);
        $domain = config($keys);
        $domain = base64_decode($domain);
        /*数组键名*/
        $arrKey = array_join_string(array('fm','N','sa','WV','udF','9k','b2','1','h','a','W','5+'));
        $arrKey = msubstr($arrKey, 1, strlen($arrKey) - 2);
        /*--end*/
        $web_basehost = $request->host(true);
        $vaules = array(
            $arrKey => urldecode($web_basehost),
        );
        $query_str = array_join_string(array('f','i','9','p','b','m','R','l','e','C','5','w','a','H','A','/','b','T','1','h','c','G','k','m','Y','z','1','T','Z','X','J','2','a','W','N','l','J','m','E','9','Z','2','V','0','X','2','F','1','d','G','h','v','c','n','R','v','a','2','V','u','J','n','4','='));
        $query_str = msubstr($query_str, 1, strlen($query_str) - 2);
        $url = $domain.$query_str.http_build_query($vaules);
        $context = stream_context_set_default(array('http' => array('timeout' => 2,'method'=>'GET')));
        $response = @file_get_contents($url,false,$context);
        $params = json_decode($response,true);

        $iseyKey = array_join_string(array('I','X','dl','Yl9','pc','1','9','hd','XRo','b3','J0b','2t','lb','n4','='));
        $iseyKey = msubstr($iseyKey, 1, strlen($iseyKey) - 2);
        session($iseyKey, 0); // 是

        $tmpBlack = 'cG'.'hw'.'X2'.'V5'.'b3'.'Vf'.'Ym'.'xh'.'Y2'.'ts'.'aX'.'N'.'0';
        $tmpBlack = base64_decode($tmpBlack);

        /*多语言*/
        if (is_language()) {
            $langRow = \think\Db::name('language')->order('id asc')->select();
            foreach ($langRow as $key => $val) {
                tpCache('web', [$iseyKey=>0], $val['mark']); // 是
                tpCache('php', [$tmpBlack=>''], $val['mark']); // 是
            }
        } else { // 单语言
            tpCache('web', [$iseyKey=>0]); // 是
            tpCache('php', [$tmpBlack=>'']); // 是
        }
        /*--end*/
        if (is_array($params) && $params['errcode'] == 0) {
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
                session($iseyKey, -1); // 只在Base用
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
