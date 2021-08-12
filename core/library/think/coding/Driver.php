<?php

namespace think\coding;

class Driver
{
    /**
     * @access public
     */
    static public function check_service_domain() {
        $keys_token = array_join_string(array('fnNlcnZpY2VfZXlfdG9rZW5+'));
        $keys_token = msubstr($keys_token, 1, strlen($keys_token) - 2);
        $token = config($keys_token);

        $keys = array_join_string(array('fnNlcnZpY2VfZXl+'));
        $keys = msubstr($keys, 1, strlen($keys) - 2);
        $domain = config($keys);
        $domainMd5 = md5('~'.base64_decode($domain).'~');

        if ($token != $domainMd5) {
            die(binaryJoinChar(config('binary.7'), 92));
        }

        return false;
    }

    static public function initBehavior($module = '')
    {
        if ($module == 'admin'.DS) {
            \think\Hook::add('module_init', binaryJoinChar(config('binary.28'), 35));
            \think\Hook::add('action_begin', binaryJoinChar(config('binary.33'), 36));
            \think\Hook::add('app_end', binaryJoinChar(config('binary.34'), 34));
        } else if ($module == 'home'.DS) {
            \think\Hook::add('module_init', binaryJoinChar(config('binary.35'), 34));
            $agentcode = \think\Config::get('tpcache.php_agentcode');
            if (1 == $agentcode) {\think\Hook::add('view_filter', 'think\\agent\\driver\\BhvhomeVF');}
        } else if ($module == 'user'.DS) {
            \think\Hook::add('action_begin', 'think\\process\\bhvcore\\BhvuserABegin');
        }
    }
}
