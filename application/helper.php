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

//------------------------
// EyouCms 助手函数
//-------------------------

use think\Url;
use think\Config;

if (!function_exists('memcache')) 
{
    /**
     * 缓存管理
     * @param mixed     $name 缓存标识，具体查看./app/extra/admin_memcache.php
     * @param mixed     $value 缓存值
     * @return mixed
     */
    function memcache($name = null, $value = null, $options = false)
    {
        //暂时改用memcached
        return memcached($name, $value, $options);
        exit;


        //暂这么连接  后期更改
        static $memcache;
        // $module = strtolower(MODULE_NAME);
        $data = Config::get('memcache_key');

        // 关闭memcached时，自动改用cache方式
        if (Config::get('memcache.switch') == 0) {
            if (empty($name) || empty($data[$name])) {
                return false;
            }
            $expire = $data[$name]['expire'];
            return cache($name, $value, $expire);
        }

        if ($options === false) {
            $options = Config::get('memcache');
        }

        $memcache = new \think\cache\driver\Memcache($options);
        if (is_null($name) && is_null($value)) {
            return $memcache;
        }

        if (empty($name) || empty($data[$name])) {
            return false;
        }

        $key = md5(strtolower($name));
        $expire = $data[$name]['expire'];
        $tag = $data[$name]['tag'];

        if (is_null($value)) {
            // 获取缓存
            return true === $memcache->has($key) ? $memcache->get($key) : false;
        } elseif ('' === $value) {
            // 删除缓存
            return $memcache->rm($key);
        } else {
            // 缓存数据
            $expire = is_numeric($expire) ? $expire : null; //默认快捷缓存设置过期时间

            if (is_null($tag) || empty($tag)) {
                return $memcache->set($key, $value, $expire);
            } else {
                // $memcache->tag = $tag;
                return $memcache->set($key, $value, $expire);
            }
        }
    }
}

if (!function_exists('memcached')) 
{
    /**
     * 缓存管理
     * @param mixed     $name 缓存标识，具体查看./app/extra/admin_memcache.php
     * @param mixed     $value 缓存值
     * @return mixed
     */
    function memcached($name = null, $value = null, $options = false)
    {
        //暂这么连接  后期更改
        static $memcached;
        // $module = strtolower(MODULE_NAME);
        $data = Config::get('memcache_key');

        // 关闭memcached时，自动改用cache方式
        if (Config::get('memcache.switch') == 0) {
            if (empty($name) || empty($data[$name])) {
                return false;
            }
            $expire = $data[$name]['expire'];
            return cache($name, $value, $expire);
        }

        if ($options === false) {
            $options = Config::get('memcache');
        }

        $memcached = new \think\cache\driver\Memcached($options);
        if (is_null($name) && is_null($value)) {
            return $memcached;
        }

        if (empty($name) || empty($data[$name])) {
            return false;
        }

        $key = md5(strtolower($name));
        $expire = $data[$name]['expire'];
        $tag = $data[$name]['tag'];

        if (is_null($value)) {
            // 获取缓存
            return true === $memcached->has($key) ? $memcached->get($key) : false;
        } elseif ('' === $value) {
            // 删除缓存
            return $memcached->rm($key);
        } else {
            // 缓存数据
            $expire = is_numeric($expire) ? $expire : null; //默认快捷缓存设置过期时间

            if (is_null($tag) || empty($tag)) {
                return $memcached->set($key, $value, $expire);
            } else {
                // $memcached->tag = $tag;
                return $memcached->set($key, $value, $expire);
            }
        }
    }
}

if (!function_exists('extra_cache')) {
/**
 * 获取和设置配置参数 支持批量定义
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @param mixed $default 默认值
 * @return mixed
 */
    function extra_cache($name, $value = '', $expire = 0) {
        $request = think\Request::instance();
        $module = strtolower($request->module());
        $keys_list = config('extra_cache_key');

        $key = md5(strtolower($name));
        if (!isset($keys_list[$name])) {
            return false;
        }
        $options = $keys_list[$name]['options'];
        $cache_conf = config('cache');
        if ($expire > 0) {
            $cache_conf['expire'] = $expire;
        } else {
            if (!empty($options['expire'])) {
                $cache_conf['expire'] = $options['expire'];
            }
        }
        if (!empty($options['prefix'])) {
            $cache_conf['prefix'] = $options['prefix'];
        }

        $tag = $keys_list[$name]['tag'];
        if (empty($tag)) {
            $tag = $module;
        }

        return cache($key, $value, $cache_conf, $tag);
   }   
}

if (!function_exists('html_cache')) {
/**
 * 获取和设置配置参数 支持批量定义
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @param mixed $default 默认值
 * @return mixed
 */
    function html_cache($name, $value = '', $options = array()) {

        $new_conf = $options;

        if (!isset($options['path'])) {
            if (!stristr(request()->baseFile(), 'index.php')) {
                $lang = get_admin_lang();
            } else {
                $lang = get_home_lang();
            }
            if (isMobile()) {
                $path = HTML_PATH."other/{$lang}_mobile_cache/";
            } else {
                $path = HTML_PATH."other/{$lang}_pc_cache/";
            }
            $new_conf['path'] = $path;
        }

        if (is_numeric($options)) {
            $new_conf['expire'] = $options;
        }

        $cache_conf = config('cache');
        $cache_conf = array_merge($cache_conf, $new_conf);

        $tag = $cache_conf['prefix'];

        if (!is_array($name)) {
            $name = strtolower($name);
        } else {
            $name = array_merge($cache_conf, $name);
            return cache($name);
        }

        return cache($name, $value, $cache_conf, $tag);
   }   
}

if (!function_exists('typeurl')) {
    /**
     * 栏目Url生成
     * @param string        $url 路由地址
     * @param string|array  $param 变量
     * @param bool|string   $suffix 生成的URL后缀
     * @param bool|string   $domain 域名
     * @param string          $seo_pseudo URL模式
     * @param string          $seo_pseudo_format URL格式
     * @return string
     */
    function typeurl($url = '', $param = '', $suffix = true, $domain = false, $seo_pseudo = null, $seo_pseudo_format = null)
    {
        $eyouUrl = '';
        static $uiset = null;
        null === $uiset && $uiset = input('param.uiset/s', 'off');
        $uiset = trim($uiset, '/');
        $seo_pseudo = !empty($seo_pseudo) ? $seo_pseudo : config('ey_config.seo_pseudo');
        if (empty($seo_pseudo_format)) {
            if (1 == $seo_pseudo) {
                $seo_pseudo_format = config('ey_config.seo_dynamic_format');
            }
        }

        if ('on' != $uiset && 1 == $seo_pseudo && 2 == $seo_pseudo_format) {
            if (is_array($param)) {
                $vars = array(
                    'tid'   => $param['id'],
                );
                $vars = http_build_query($vars);
            } else {
                $vars = $param;
            }
            $eyouUrl = url($url, array(), $suffix, $domain, $seo_pseudo, $seo_pseudo_format);
            $urlParam = parse_url($eyouUrl);
            $query_str = isset($urlParam['query']) ? $urlParam['query'] : '';
            if (empty($query_str)) {
                $eyouUrl .= '?';
            } else {
                $eyouUrl .= '&';
            }
            $eyouUrl .= $vars;
        } elseif ('on' != $uiset && 2 == $seo_pseudo) { // 生成静态页面代码
            
            if (isMobile()) { // 手机端访问非静态页面
                if (is_array($param)) {
                    $vars = array(
                        'tid'   => $param['id'],
                    );
                    $vars = http_build_query($vars);
                } else {
                    $vars = $param;
                }
                static $home_lang = null;
                null == $home_lang && $home_lang = get_home_lang(); // 前台语言 by 小虎哥
                static $main_lang = null;
                null == $main_lang && $main_lang = get_main_lang(); // 前台主体语言 by 小虎哥
                if ($home_lang != $main_lang) {
                    $vars .= "&lang=".get_home_lang();
                }
                $eyouUrl = url('home/Lists/index', $vars, true, false, 1);
            }
            else
            { // PC端访问是静态页面
                static $seo_html_listname = null;
                null === $seo_html_listname && $seo_html_listname = tpCache('seo.seo_html_listname');
                static $seo_html_arcdir = null;
                null === $seo_html_arcdir && $seo_html_arcdir = tpCache('seo.seo_html_arcdir');
                if($seo_html_listname == 1){//存放顶级目录
                    $dirpath = explode('/',$param['dirpath']);
                    if($param['parent_id'] == 0){
                        $url = $seo_html_arcdir.'/'.$dirpath[1].'/';
                    }else{
                        $url = $seo_html_arcdir.'/'.$dirpath[1]."/lists_".$param['id'].'.html';
                    }
                } else if ($seo_html_listname == 3) { // 存放子级目录
                    $dirpath = explode('/',$param['dirpath']);
                    $url = $seo_html_arcdir.'/'.end($dirpath).'/';
                }else{
                    $url = $seo_html_arcdir.$param['dirpath'].'/';
                }
                
                $eyouUrl = ROOT_DIR.$url;
                if (false !== $domain) {
                    static $re_domain = null;
                    null === $re_domain && $re_domain = request()->domain();
                    if (true === $domain) {
                        $eyouUrl = $re_domain.$eyouUrl;
                    } else {
                        $eyouUrl = rtrim($domain, '/').$eyouUrl;
                    }
                }
            }

        } elseif ('on' != $uiset && 3 == $seo_pseudo) {
            if (is_array($param)) {
                $vars = array(
                    'tid'   => $param['dirname'],
                );
            } else {
                $vars = $param;
            }
            /*伪静态格式*/
            $seo_rewrite_format = config('ey_config.seo_rewrite_format');
            if (1 == intval($seo_rewrite_format)) {
                $eyouUrl = url('home/Lists/index', $vars, $suffix, $domain, $seo_pseudo, $seo_pseudo_format);
                if (!strstr($eyouUrl, '.htm')){
                    $eyouUrl .= '/';
                }
            } else if (3 == intval($seo_rewrite_format)) {
                $eyouUrl = url('home/Lists/index', $vars, $suffix, $domain, $seo_pseudo, $seo_pseudo_format);
                if (!strstr($eyouUrl, '.htm')){
                    $eyouUrl .= '/';
                }
            } else {
                $eyouUrl = url($url, $vars, $suffix, $domain, $seo_pseudo, $seo_pseudo_format); // 兼容v1.1.6之前被搜索引擎收录的URL
            }
            /*--end*/
        } else {
            if (is_array($param)) {
                $vars = array(
                    'tid'   => $param['id'],
                );
            } else {
                $vars = $param;
            }
            $eyouUrl = url('home/Lists/index', $vars, $suffix, $domain, $seo_pseudo, $seo_pseudo_format);
        }

        return $eyouUrl;
    }
}

if (!function_exists('arcurl')) {
    /**
     * 文档Url生成
     * @param string        $url 路由地址
     * @param string|array  $param 变量
     * @param bool|string   $suffix 生成的URL后缀
     * @param bool|string   $domain 域名
     * @param string          $seo_pseudo URL模式
     * @param string          $seo_pseudo_format URL格式
     * @return string
     */
    function arcurl($url = '', $param = '', $suffix = true, $domain = false, $seo_pseudo = '', $seo_pseudo_format = null)
    {
        $eyouUrl = '';
        static $uiset = null;
        null === $uiset && $uiset = input('param.uiset/s', 'off');
        $uiset = trim($uiset, '/');
        $seo_pseudo = !empty($seo_pseudo) ? $seo_pseudo : config('ey_config.seo_pseudo');
        if (empty($seo_pseudo_format)) {
            if (1 == $seo_pseudo) {
                $seo_pseudo_format = config('ey_config.seo_dynamic_format');
            }
        }
        
        if ('on' != $uiset && 1 == $seo_pseudo && 2 == $seo_pseudo_format) {
            if (is_array($param)) {
                $vars = array(
                    'aid'   => $param['aid'],
                );
                $vars = http_build_query($vars);
            } else {
                $vars = $param;
            }
            $eyouUrl = url($url, array(), $suffix, $domain, $seo_pseudo, $seo_pseudo_format);
            $urlParam = parse_url($eyouUrl);
            $query_str = isset($urlParam['query']) ? $urlParam['query'] : '';
            if (empty($query_str)) {
                $eyouUrl .= '?';
            } else {
                $eyouUrl .= '&';
            }
            $eyouUrl .= $vars;
        } elseif ($seo_pseudo == 2 && $uiset != 'on') { // 生成静态页面代码
            
            if (isMobile()) { // 手机端访问非静态页面
                if (is_array($param)) {
                    $vars = array(
                        'aid'   => $param['aid'],
                    );
                    $vars = http_build_query($vars);
                } else {
                    $vars = $param;
                }
                static $home_lang = null;
                null == $home_lang && $home_lang = get_home_lang(); // 前台语言 by 小虎哥
                static $main_lang = null;
                null == $main_lang && $main_lang = get_main_lang(); // 前台主体语言 by 小虎哥
                if ($home_lang != $main_lang) {
                    $vars .= "&lang=".get_home_lang();
                }
                $eyouUrl = url('home/View/index', $vars, true, false, 1);
            }
            else
            { // PC端访问是静态页面
                if (!empty($param['htmlfilename'])){
                    $aid = $param['htmlfilename'];
                }else{
                    $aid = $param['aid'];
                }
                $url = $param['dirpath']."/{$aid}.html";
                static $seo_html_pagename = null;
                null === $seo_html_pagename && $seo_html_pagename = tpCache('seo.seo_html_pagename');
                static $seo_html_arcdir = null;
                null === $seo_html_arcdir && $seo_html_arcdir = tpCache('seo.seo_html_arcdir');
                if($seo_html_pagename == 1){//存放顶级目录
                    $dirpath = explode('/',$param['dirpath']);
                    $url = $seo_html_arcdir.'/'.$dirpath[1].'/'.$aid.'.html';
                } else if ($seo_html_pagename == 3) {
                    $dirpath = explode('/',$param['dirpath']);
                    $url = $seo_html_arcdir.'/'.end($dirpath).'/'.$aid.'.html';
                }else{
                    $url = $seo_html_arcdir.$param['dirpath'].'/'.$aid.'.html';
                }
                
                $eyouUrl = ROOT_DIR.$url;
                if (false !== $domain) {
                    static $re_domain = null;
                    null === $re_domain && $re_domain = request()->domain();
                    if (true === $domain) {
                        $eyouUrl = $re_domain.$eyouUrl;
                    } else {
                        $eyouUrl = rtrim($domain, '/').$eyouUrl;
                    }
                }
            }

        } elseif ($seo_pseudo == 3 && $uiset != 'on') {
            /*伪静态格式*/
            $seo_rewrite_format = config('ey_config.seo_rewrite_format');
            if (1 == intval($seo_rewrite_format)) {
                $url = 'home/View/index';
                /*URL里第一层级固定是顶级栏目的目录名称*/
                static $tdirnameArr = null;
                null === $tdirnameArr && $tdirnameArr = every_top_dirname_list();
                if (!empty($param['dirname']) && isset($tdirnameArr[md5($param['dirname'])]['tdirname'])) {
                    $param['dirname'] = $tdirnameArr[md5($param['dirname'])]['tdirname'];
                }
                /*--end*/
            } else if (3 == intval($seo_rewrite_format)) {
                $url = 'home/View/index';
            }
            /*--end*/
            if (is_array($param)) {
                $vars = array(
                    'aid'   => $param['aid'],
                    'dirname'   => $param['dirname'],
                );
            } else {
                $vars = $param;
            }
            $eyouUrl = url($url, $vars, $suffix, $domain, $seo_pseudo, $seo_pseudo_format);
        } else {
            if (is_array($param)) {
                $vars = array(
                    'aid'   => $param['aid'],
                );
                $vars = http_build_query($vars);
            } else {
                $vars = $param;
            }
            $eyouUrl = url('home/View/index', $vars, $suffix, $domain, $seo_pseudo, $seo_pseudo_format);
        }

        return $eyouUrl;
    }
}

if (!function_exists('tagurl')) {
    /**
     * Tag标签Url生成
     * @param string        $url 路由地址
     * @param string|array  $param 变量
     * @param bool|string   $suffix 生成的URL后缀
     * @param bool|string   $domain 域名
     * @param string          $seo_pseudo URL模式
     * @param string          $seo_pseudo_format URL格式
     * @return string
     */
    function tagurl($url = '', $param = '', $suffix = true, $domain = false, $seo_pseudo = '', $seo_pseudo_format = null)
    {
        $eyouUrl = '';
        $seo_pseudo = !empty($seo_pseudo) ? $seo_pseudo : config('ey_config.seo_pseudo');
        if (empty($seo_pseudo_format)) {
            if (1 == $seo_pseudo) {
                $seo_pseudo_format = config('ey_config.seo_dynamic_format');
            }
        }
        
        static $tags_html = null;
        null === $tags_html && $tags_html = config('tpcache.plus_tags_html');
        if (!empty($tags_html)) {
            $eyouUrl = ROOT_DIR."/tags/{$param['tagid']}.html";
            if (false !== $domain) {
                static $re_domain = null;
                null === $re_domain && $re_domain = request()->domain();
                if (true === $domain) {
                    $eyouUrl = $re_domain.$eyouUrl;
                } else {
                    $eyouUrl = rtrim($domain, '/').$eyouUrl;
                }
            }
        } else {
            if (is_array($param)) {
                $vars = array(
                    'tagid'   => $param['tagid'],
                );
                $vars = http_build_query($vars);
            } else {
                $vars = $param;
            }
            $eyouUrl = url('home/Tags/lists', $vars, $suffix, $domain, $seo_pseudo, $seo_pseudo_format);
        }

        return $eyouUrl;
    }
}

if (!function_exists('askurl')) {
    /**
     * 问答模型Url生成
     * @param string $url 路由地址
     * @param string|array $param 变量
     * @param bool|string $suffix 生成的URL后缀
     * @param bool|string $domain 域名
     * @param string $seo_pseudo URL模式
     * @param string $seo_pseudo_format URL格式
     * @return string
     */
    function askurl($url = '', $param = '', $suffix = true, $domain = false, $seo_pseudo = '', $seo_pseudo_format = null, $seo_inlet = null)
    {
        $eyouUrl    = '';
        $seo_pseudo = !empty($seo_pseudo) ? $seo_pseudo : config('ey_config.seo_pseudo');
        if (empty($seo_pseudo_format)) {
            if (1 == $seo_pseudo) {
                $seo_pseudo_format = config('ey_config.seo_dynamic_format');
            }
        }

        if ($seo_pseudo == 3 || $seo_pseudo == 2) {
            // static $askdirnameArr = null;
            // null === $askdirnameArr && $askdirnameArr = \think\Db::name('arctype')->where(['current_channel' => 51, 'is_del' => 0])->getField('dirname');
            if (is_array($param)) {
                $vars         = $param;
                // $vars['code'] = $askdirnameArr;
            } else {
                $vars = $param;
                if (!empty($vars)) {
                    // $vars .= '&code='.$askdirnameArr;
                } else {
                    // $vars = 'code='.$askdirnameArr;
                }
            }
            $eyouUrl = url($url, $vars, $suffix, $domain,3, $seo_pseudo_format, $seo_inlet);
            if (!strstr($eyouUrl, '.htm')){
                $eyouUrl .= '/';
            }
        } else {
            $eyouUrl = url($url, $param, $suffix, $domain, $seo_pseudo, $seo_pseudo_format, $seo_inlet);
        }

        return $eyouUrl;
    }
}

if (!function_exists('eyIntval')) {
    /**
     * 强制把数值转为整型
     * @param mixed        $data 任意数值
     * @return mixed
     */
    function eyIntval($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $data[$key] = intval($val);
            }
        } else if (is_string($data) && stristr($data, ',')) {
            $arr = explode(',', $data);
            foreach ($arr as $key => $val) {
                $arr[$key] = intval($val);
            }
            $data = implode(',', $arr);
        } else {
            $data = intval($data);
        }

        return $data;
    }
}

if (!function_exists('eyPreventShell')) {
    /**
     * 验证是否shell注入
     * @param mixed        $data 任意数值
     * @return mixed
     */
    function eyPreventShell($data = '')
    {
        $redata = true;
        if (!is_array($data) && (preg_match('/^phar:\/\//i', $data) || stristr($data, 'phar://'))) {
            $redata = false;
        }

        return $redata;
    }
}