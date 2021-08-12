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

if (!function_exists('is_adminlogin')) 
{
    /**
     * 检验登陆
     * @param
     * @return bool
     */
    function is_adminlogin(){
        $admin_id = session('admin_id');
        if(isset($admin_id) && $admin_id > 0){
            return $admin_id;
        }else{
            return false;
        }
    }
}

if (!function_exists('adminLog')) 
{
    /**
     * 管理员操作记录
     * @param $log_url 操作URL
     * @param $log_info 记录信息
     */
    function adminLog($log_info = ''){
        $admin_id = session('admin_id');
        $admin_id = !empty($admin_id) ? $admin_id : -1;
        $add['log_time'] = getTime();
        $add['admin_id'] = $admin_id;
        $add['log_info'] = $log_info;
        $add['log_ip'] = clientIP();
        $add['log_url'] = request()->baseUrl() ;
        M('admin_log')->add($add);
        
        // 只保留最近一个月的操作日志
        try {
            $ajaxLogic = new \app\admin\logic\AjaxLogic;
            $ajaxLogic->del_adminlog();
        } catch (\Exception $e) {
            
        }
    }
}

if (!function_exists('getAdminInfo')) 
{
    /**
     * 获取管理员登录信息
     */
    function getAdminInfo($admin_id = 0)
    {
        $admin_info = [];
        $admin_id = empty($admin_id) ? session('admin_id') : $admin_id;
        if (0 < intval($admin_id)) {
            $admin_info = \think\Db::name('admin')
                ->field('a.*, b.name AS role_name')
                ->alias('a')
                ->join('__AUTH_ROLE__ b', 'b.id = a.role_id', 'LEFT')
                ->where("a.admin_id", $admin_id)
                ->find();
            if (!empty($admin_info)) {
                // 头像
                empty($admin_info['head_pic']) && $admin_info['head_pic'] = get_head_pic($admin_info['head_pic'], true);
                
                // 权限组
                $admin_info['role_id'] = !empty($admin_info['role_id']) ? $admin_info['role_id'] : -1;
                if (-1 == $admin_info['role_id']) {
                    if (!empty($admin_info['parent_id'])) {
                        $role_name = '超级管理员';
                    } else {
                        $role_name = '创始人';
                    }
                } else {
                    $role_name = $admin_info['role_name'];
                }
                $admin_info['role_name'] = $role_name;
            }
        }
        
        return $admin_info;
    }
}

if (!function_exists('get_conf')) 
{
    /**
     * 获取conf配置文件
     */
    function get_conf($name = 'global')
    {
        $arr = include APP_PATH.MODULE_NAME.'/conf/'.$name.'.php';
        return $arr;
    }
}

if (!function_exists('get_auth_rule')) 
{
    /**
     * 获取权限列表文件
     */
    function get_auth_rule($where = [])
    {
        $auth_rule = include APP_PATH.MODULE_NAME.'/conf/auth_rule.php';

        // 排序号排序
        // $sort_order_arr = array();
        // foreach($auth_rule as $key => $val){
        //     $sort_order_arr[]['sort_order'] = $val['sort_order'];
        // }
        // array_multisort($sort_order_arr,SORT_ASC,$auth_rule);

        if (!empty($where)) {
            foreach ($auth_rule as $k1 => $rules) {
                foreach ($where as $k2 => $v2) {
                    if ($rules[$k2] != $v2) {
                        unset($auth_rule[$k1]);
                    }
                }
            }
        }
        return $auth_rule;
    }
}

if (!function_exists('is_check_access')) 
{
    /**
     * 检测是否有该权限
     */
    function is_check_access($str = 'Index@index') {  
        $bool_flag = 1;
        $role_id = session('admin_info.role_id');
        if (0 < intval($role_id)) {
            $ctl_act = strtolower($str);
            $arr = explode('@', $ctl_act);
            $ctl = !empty($arr[0]) ? $arr[0] : '';
            $act = !empty($arr[1]) ? $arr[1] : '';
            $ctl_all = $ctl.'@*';

            $auth_role_info = session('admin_info.auth_role_info');
            $permission = $auth_role_info['permission'];
            $permission_rules = !empty($permission['rules']) ? $permission['rules'] : [];

            $auth_rule = get_auth_rule();
            $all_auths = []; // 系统全部权限对应的菜单ID
            $admin_auths = []; // 用户当前拥有权限对应的菜单ID
            $diff_auths = []; // 用户没有被授权的权限对应的菜单ID
            foreach($auth_rule as $key => $val){
                $all_auths = array_merge($all_auths, explode(',', strtolower($val['auths'])));
                if (in_array($val['id'], $permission_rules)) {
                    $admin_auths = array_merge($admin_auths, explode(',', strtolower($val['auths'])));
                }
            }
            $all_auths = array_unique($all_auths);
            $admin_auths = array_unique($admin_auths);
            $diff_auths = array_diff($all_auths, $admin_auths);

            if (in_array($ctl_act, $diff_auths) || in_array($ctl_all, $diff_auths)) {
                $bool_flag = false;
            }
        }

        return $bool_flag;
    }
}

if (!function_exists('getMenuList')) 
{
    /**
     * 根据角色权限过滤菜单
     */
    function getMenuList() {
        $menuArr = getAllMenu();
        // return $menuArr;

        $role_id = session('admin_info.role_id');
        if (0 < intval($role_id)) {
            $auth_role_info = session('admin_info.auth_role_info');
            $permission = $auth_role_info['permission'];
            $permission_rules = !empty($permission['rules']) ? $permission['rules'] : [];

            $auth_rule = get_auth_rule();
            $all_auths = []; // 系统全部权限对应的菜单ID
            $admin_auths = []; // 用户当前拥有权限对应的菜单ID
            $diff_auths = []; // 用户没有被授权的权限对应的菜单ID
            foreach($auth_rule as $key => $val){
                $all_auths = array_merge($all_auths, explode(',', $val['menu_id']), explode(',', $val['menu_id2']));
                if (in_array($val['id'], $permission_rules)) {
                    $admin_auths = array_merge($admin_auths, explode(',', $val['menu_id']), explode(',', $val['menu_id2']));
                }
            }
            $all_auths = array_unique($all_auths);
            $admin_auths = array_unique($admin_auths);
            $diff_auths = array_diff($all_auths, $admin_auths);

            /*过滤三级数组菜单*/
            foreach($menuArr as $k=>$val){
                foreach ($val['child'] as $j=>$v){
                    foreach ($v['child'] as $s=>$son){
                        if (in_array($son['id'], $diff_auths)) {
                            unset($menuArr[$k]['child'][$j]['child'][$s]);//过滤菜单
                        }
                    }
                }
            }
            /*--end*/

            /*过滤二级数组菜单*/
            foreach ($menuArr as $mk=>$mr){
                foreach ($mr['child'] as $nk=>$nrr){
                    if (in_array($nrr['id'], $diff_auths)) {
                        unset($menuArr[$mk]['child'][$nk]);//过滤菜单
                    }
                }
            }
            /*--end*/
        }

        return $menuArr;
    }
}

if (!function_exists('getAllMenu')) 
{
    /**
     * 获取左侧菜单
     */
    function getAllMenu() {
        $menuArr = false;//extra_cache('admin_all_menu');
        if (!$menuArr) {
            $menuArr = get_conf('menu');
            extra_cache('admin_all_menu', $menuArr);
        }
        return $menuArr;
    }
}

if ( ! function_exists('getChanneltypeList'))
{
    /**
     * 获取全部的模型
     */
    function getChanneltypeList()
    {
        $result = extra_cache('admin_channeltype_list_logic');
        if ($result == false)
        {
            $result = model('Channeltype')->getAll('*', array(), 'id');
            extra_cache('admin_channeltype_list_logic', $result);
        }

        return $result;
    }
}

if (!function_exists('tpversion')) 
{
    function tpversion($timeout = 5)
    {
        if(!empty($_SESSION['isset_push']))
            return false;
        $_SESSION['isset_push'] = 1;
        error_reporting(0);//关闭所有错误报告
        $install_time = DEFAULT_INSTALL_DATE;
        $serial_number = DEFAULT_SERIALNUMBER;

        $constsant_path = APP_PATH.'admin/conf/constant.php';
        if (file_exists($constsant_path)) {
            require_once($constsant_path);
            defined('INSTALL_DATE') && $install_time = INSTALL_DATE;
            defined('SERIALNUMBER') && $serial_number = SERIALNUMBER;
        }
        $curent_version = getCmsVersion();
        $mysqlinfo = \think\Db::query("SELECT VERSION() as version");
        $mysql_version  = $mysqlinfo[0]['version'];
        $global_config = tpCache('global');
        $users_config = getUsersConfigData('all');
        $vaules = array(            
            'domain'=>$_SERVER['HTTP_HOST'], 
            'key_num'=>$curent_version, 
            'install_time'=>$install_time, 
            'serial_number'=>$serial_number,
            'ip'    => GetHostByName($_SERVER['SERVER_NAME']),
            'agentcode' => !empty($global_config['php_agentcode']) ? $global_config['php_agentcode'] : 0,
            'global_config' => base64_encode(json_encode($global_config)),
            'users_config' => base64_encode(json_encode($users_config)),
            'phpv'  => urlencode(phpversion()),
            'mysql_version' => urlencode($mysql_version),
            'web_server'    => urlencode($_SERVER['SERVER_SOFTWARE']),
            'web_title' => tpCache('web.web_title'),
        );
        // api_Service_user_push
        $service_ey = config('service_ey');
        $tmp_str = 'L2luZGV4LnBocD9tPWFwaSZjPVNlcnZpY2UmYT11c2VyX3B1c2gm';
        $url = base64_decode($service_ey).base64_decode($tmp_str);
        @httpRequest($url, 'POST', $vaules, [], $timeout);
    }
}

if (!function_exists('push_zzbaidu')) 
{
    /**
     * 将新链接推送给百度蜘蛛
     */
    function push_zzbaidu($type = 'urls', $aid = '', $typeid = '')
    {
        // 获取token的值：http://ziyuan.baidu.com/linksubmit/index?site=http://www.eyoucms.com/
        $aid = intval($aid);
        $typeid = intval($typeid);
        $sitemap_zzbaidutoken = tpCache('sitemap.sitemap_zzbaidutoken');
        if (empty($sitemap_zzbaidutoken) || (empty($aid) && empty($typeid)) || !function_exists('curl_init')) {
            return '';
        }

        $urlsArr = array();
        $channeltype_list = model('Channeltype')->getAll('id, ctl_name', array(), 'id');

        if ($aid > 0) {
            $res = M('archives')->field('b.*, a.*, a.aid, b.id as typeid')
                ->alias('a')
                ->join('__ARCTYPE__ b', 'b.id = a.typeid', 'LEFT')
                ->find($aid);
            $arcurl = get_arcurl($res, false);
            array_push($urlsArr, $arcurl);
        }
        if (0 < $typeid) {
            $res = M('arctype')->field('a.*')
                ->alias('a')
                ->find($typeid);
            $typeurl = get_typeurl($res, false);
            array_push($urlsArr, $typeurl);
        }

        $type = ('edit' == $type) ? 'update' : 'urls';
        $api = 'http://data.zz.baidu.com/'.$type.'?site='.request()->host(true).'&token='.$sitemap_zzbaidutoken;
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => implode("\n", $urlsArr),
            CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        
        return $result;    
    }
}

if (!function_exists('sitemap_auto')) 
{
    /**
     * 自动生成引擎sitemap
     */
    function sitemap_auto()
    {
        $sitemap_config = tpCache('sitemap');
        if (isset($sitemap_config['sitemap_auto']) && $sitemap_config['sitemap_auto'] > 0) {
            sitemap_all();
        }
    }
}

if (!function_exists('sitemap_all')) 
{
    /**
     * 生成全部引擎sitemap
     */
    function sitemap_all()
    {
        sitemap_xml();
    }
}

if (!function_exists('sitemap_xml')) 
{
    /**
     * 生成xml形式的sitemap
     */
    function sitemap_xml()
    {
        $globalConfig = tpCache('global');
        if (!isset($globalConfig['sitemap_xml']) || empty($globalConfig['sitemap_xml'])) {
            return '';
        }

        $modelu_name = 'home';
        $lang = get_current_lang();
        $default_lang = get_default_lang();
        $langRow = \think\Db::name('language')->field('is_home_default')->where(['mark'=>$lang])->find();
        if (!empty($langRow['is_home_default'])) {
            $filename = ROOT_PATH . "sitemap.xml";
        } else {
            $filename = ROOT_PATH . "sitemap_{$lang}.xml";
        }

        /* 分类列表(用于生成列表链接的sitemap) */
        $map = array(
            'status'    => 1,
            'is_del'    => 0,
            'lang'      => $lang,
        );
        if (is_array($globalConfig)) {
            // 过滤隐藏栏目
            if (isset($globalConfig['sitemap_not1']) && $globalConfig['sitemap_not1'] > 0) {
                $map['is_hidden'] = 0;
            }
            // 过滤外部模块
            if (isset($globalConfig['sitemap_not2']) && $globalConfig['sitemap_not2'] > 0) {
                $map['is_part'] = 0;
            }
        }
        $result_arctype = M('arctype')->field("*, id AS loc, add_time AS lastmod, 'hourly' AS changefreq, '0.8' AS priority")
            ->where($map)
            ->order('sort_order asc, id asc')
            ->getAllWithIndex('id');

        /* 文章列表(用于生成文章详情链接的sitemap) */
        if (!isset($globalConfig['sitemap_archives_num']) || $globalConfig['sitemap_archives_num'] === '') {
            $sitemap_archives_num = 100;
        } else {
            $sitemap_archives_num = intval($globalConfig['sitemap_archives_num']);
        }
        $result_archives = [];
        if (0 < $sitemap_archives_num) {
            $map = array(
                'channel'   => ['IN', config('global.allow_release_channel')],
                'arcrank'   => array('gt', -1),
                'status'    => 1,
                'is_del'    => 0,
                'lang'      => $lang,
            );
            if (is_array($globalConfig)) {
                // 过滤外部模块
                if (isset($globalConfig['sitemap_not2']) && $globalConfig['sitemap_not2'] > 0) {
                    $map['is_jump'] = 0;
                }
            }
            /*定时文档显示插件*/
            if (is_dir('./weapp/TimingTask/')) {
                $TimingTaskRow = model('Weapp')->getWeappList('TimingTask');
                if (!empty($TimingTaskRow['status']) && 1 == $TimingTaskRow['status']) {
                    $map['add_time'] = ['elt', getTime()]; // 只显当天或之前的文档
                }
            }
            /*end*/
            $field = "aid, channel, is_jump, jumplinks, add_time, update_time, typeid, aid AS loc, add_time AS lastmod, 'daily' AS changefreq, '0.5' AS priority";
            $result_archives = M('archives')->field($field)
                ->where($map)
                ->order('aid desc')
                ->limit($sitemap_archives_num)
                ->select();
        }

        /* Tag列表(用于生成Tag标签链接的sitemap) */
        if (!isset($globalConfig['sitemap_tags_num']) || $globalConfig['sitemap_tags_num'] === '') {
            $sitemap_tags_num = 100;
        } else {
            $sitemap_tags_num = intval($globalConfig['sitemap_tags_num']);
        }
        $result_tags = [];
        if (0 < $sitemap_tags_num) {
            $map = array(
                'lang'      => $lang,
            );
            $field = "id, add_time, id AS loc, add_time AS lastmod, 'daily' AS changefreq, '0.5' AS priority";
            $result_tags = M('tagindex')->field($field)
                ->where($map)
                ->order('add_time desc')
                ->limit($sitemap_tags_num)
                ->select();
        }

        // header('Content-Type: text/xml');//这行很重要，php默认输出text/html格式的文件，所以这里明确告诉浏览器输出的格式为xml,不然浏览器显示不出xml的格式
        $xml_wrapper = <<<XML
<?xml version='1.0' encoding='utf-8'?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
</urlset>
XML;

        if (function_exists('simplexml_load_string')) {
            $xml = @simplexml_load_string($xml_wrapper);
        } else if (class_exists('SimpleXMLElement')) {
            $xml = new SimpleXMLElement($xml_wrapper);
        }
        if (!$xml) {
            return true;
        }

        // 更新频率
        $sitemap_changefreq_index = !empty($globalConfig['sitemap_changefreq_index']) ? $globalConfig['sitemap_changefreq_index'] : 'always';
        $sitemap_changefreq_list = !empty($globalConfig['sitemap_changefreq_list']) ? $globalConfig['sitemap_changefreq_list'] : 'hourly';
        $sitemap_changefreq_view = !empty($globalConfig['sitemap_changefreq_view']) ? $globalConfig['sitemap_changefreq_view'] : 'daily';

        // 优先级别
        $sitemap_priority_index = !empty($globalConfig['sitemap_priority_index']) ? $globalConfig['sitemap_priority_index'] : '1.0';
        $sitemap_priority_list = !empty($globalConfig['sitemap_priority_list']) ? $globalConfig['sitemap_priority_list'] : '0.8';
        $sitemap_priority_view = !empty($globalConfig['sitemap_priority_view']) ? $globalConfig['sitemap_priority_view'] : '0.5';

        $langRow = \think\Db::name('language')
            ->where(['status'=>1])
            ->order('id asc')
            ->cache(true, EYOUCMS_CACHE_TIME, 'language')
            ->select();

        // 去掉入口文件
        $inletStr = '/index.php';
        $seo_inlet = config('ey_config.seo_inlet');
        1 == intval($seo_inlet) && $inletStr = '';

        // 首页
        if ($lang == $default_lang) {
            foreach ($langRow as $key => $val) {
                $mark = $val['mark'];
                if (empty($globalConfig['web_language_switch']) && $lang != $mark) { // 关闭多语言
                    continue;
                }
                /*单独域名*/
                $url = $val['url'];
                if (empty($url)) {
                    if (1 == $val['is_home_default']) {
                        $url = request()->domain().ROOT_DIR.'/';
                    } else {
                        if ($mark != $default_lang) {
                            $url = request()->domain().ROOT_DIR."/sitemap_{$mark}.xml";
                        } else {
                            $seoConfig = tpCache('seo', [], $mark);
                            $seo_pseudo = !empty($seoConfig['seo_pseudo']) ? $seoConfig['seo_pseudo'] : config('ey_config.seo_pseudo');
                            if (1 == $seo_pseudo) {
                                $url = request()->domain().ROOT_DIR.$inletStr;
                                if (!empty($inletStr)) {
                                    $url .= '?';
                                } else {
                                    $url .= '/?';
                                }
                                $url .= http_build_query(['lang'=>$mark]);
                            } else {
                                $url = request()->domain().ROOT_DIR.$inletStr.'/'.$mark;
                            }
                        }
                    }
                } else {
                    if (0 == $val['is_home_default']) {
                        $url = $url.ROOT_DIR."/sitemap_{$mark}.xml";
                    }
                }
                /*--end*/

                $item = $xml->addChild('url'); //使用addChild添加节点
                foreach (['loc','lastmod','changefreq','priority'] as $key1) {
                    if ('loc' == $key1) {
                        $row = $url;
                    } else if ('lastmod' == $key1) {
                        $row = date('Y-m-d');
                    } else if ('changefreq' == $key1) {
                        $row = $sitemap_changefreq_index;
                    } else if ('priority' == $key1) {
                        $row = $sitemap_priority_index;
                    }
                    try {
                        $node = $item->addChild($key1, $row);
                    } catch (\Exception $e) {}
                    if (isset($attribute_array[$key1]) && is_array($attribute_array[$key1])) {
                        foreach ($attribute_array[$key1] as $akey => $aval) {//设置属性值，我这里为空
                            $node->addAttribute($akey, $aval);
                        }
                    }
                }
            }
        }
        else {
            foreach ($langRow as $key => $val) {
                $mark = $val['mark'];
                if (empty($globalConfig['web_language_switch']) || $lang != $mark) { // 关闭多语言
                    continue;
                }
                /*单独域名*/
                $url = $val['url'];
                if (empty($url)) {
                    $seoConfig = tpCache('seo', [], $mark);
                    $seo_pseudo = !empty($seoConfig['seo_pseudo']) ? $seoConfig['seo_pseudo'] : config('ey_config.seo_pseudo');
                    if (1 == $seo_pseudo) {
                        $url = request()->domain().ROOT_DIR.$inletStr;
                        if (!empty($inletStr)) {
                            $url .= '?';
                        } else {
                            $url .= '/?';
                        }
                        $url .= http_build_query(['lang'=>$mark]);
                    } else {
                        $url = request()->domain().ROOT_DIR.$inletStr.'/'.$mark;
                    }
                }
                /*--end*/

                $item = $xml->addChild('url'); //使用addChild添加节点
                foreach (['loc','lastmod','changefreq','priority'] as $key1) {
                    if ('loc' == $key1) {
                        $row = $url;
                    } else if ('lastmod' == $key1) {
                        $row = date('Y-m-d');
                    } else if ('changefreq' == $key1) {
                        $row = $sitemap_changefreq_index;
                    } else if ('priority' == $key1) {
                        $row = $sitemap_priority_index;
                    }
                    try {
                        $node = $item->addChild($key1, $row);
                    } catch (\Exception $e) {}
                    if (isset($attribute_array[$key1]) && is_array($attribute_array[$key1])) {
                        foreach ($attribute_array[$key1] as $akey => $aval) {//设置属性值，我这里为空
                            $node->addAttribute($akey, $aval);
                        }
                    }
                }
            }
        }
        /*--end*/
         
        /*所有栏目*/
        foreach ($result_arctype as $sub) {
            if (is_array($sub)) {
                $item = $xml->addChild('url'); //使用addChild添加节点
                foreach ($sub as $key => $row) {
                    if (in_array($key, array('loc','lastmod','changefreq','priority'))) {
                        if ($key == 'loc') {
                            if ($sub['is_part'] == 1) {
                                $row = $sub['typelink'];
                            } else {
                                $row = get_typeurl($sub, false);
                            }
                            $row = str_replace('&amp;', '&', $row);
                            $row = str_replace('&', '&amp;', $row);
                        } else if ($key == 'lastmod') {
                            $row = date('Y-m-d');
                        } else if ($key == 'changefreq') {
                            $row = $sitemap_changefreq_list;
                        } else if ($key == 'priority') {
                            $row = $sitemap_priority_list;
                        }
                        try {
                            $node = $item->addChild($key, $row);
                        } catch (\Exception $e) {}
                        if (isset($attribute_array[$key]) && is_array($attribute_array[$key])) {
                            foreach ($attribute_array[$key] as $akey => $aval) {//设置属性值，我这里为空
                                $node->addAttribute($akey, $aval);
                            }
                        }
                    }
                }
            }
        }
        /*--end*/

        /*所有文档*/
        foreach ($result_archives as $val) {
            if (is_array($val) && isset($result_arctype[$val['typeid']])) {
                $item = $xml->addChild('url'); //使用addChild添加节点
                $val = array_merge($result_arctype[$val['typeid']], $val);
                foreach ($val as $key => $row) {
                    if (in_array($key, array('loc','lastmod','changefreq','priority'))) {
                        if ($key == 'loc') {
                            if ($val['is_jump'] == 1) {
                                $row = $val['jumplinks'];
                            } else {
                                $row = get_arcurl($val, false);
                            }
                            $row = str_replace('&amp;', '&', $row);
                            $row = str_replace('&', '&amp;', $row);
                        } else if ($key == 'lastmod') {
                            $lastmod_time = empty($val['update_time']) ? $val['add_time'] : $val['update_time'];
                            $row = date('Y-m-d', $lastmod_time);
                        } else if ($key == 'changefreq') {
                            $row = $sitemap_changefreq_view;
                        } else if ($key == 'priority') {
                            $row = $sitemap_priority_view;
                        }
                        try {
                            $node = $item->addChild($key, $row);
                        } catch (\Exception $e) {}
                        if (isset($attribute_array[$key]) && is_array($attribute_array[$key])) {
                            foreach ($attribute_array[$key] as $akey => $aval) {//设置属性值，我这里为空
                                $node->addAttribute($akey, $aval);
                            }
                        }
                    }
                }
            }
        }
        /*--end*/

        /*所有Tag*/
        foreach ($result_tags as $val) {
            if (is_array($val)) {
                $item = $xml->addChild('url'); //使用addChild添加节点
                foreach ($val as $key => $row) {
                    if (in_array($key, array('loc','lastmod','changefreq','priority'))) {
                        if ($key == 'loc') {
                            $row = get_tagurl($val['id']);
                            $row = str_replace('&amp;', '&', $row);
                            $row = str_replace('&', '&amp;', $row);
                        } else if ($key == 'lastmod') {
                            $row = date('Y-m-d', $val['add_time']);
                        } else if ($key == 'changefreq') {
                            $row = $sitemap_changefreq_view;
                        } else if ($key == 'priority') {
                            $row = $sitemap_priority_view;
                        }
                        try {
                            $node = $item->addChild($key, $row);
                        } catch (\Exception $e) {}
                        if (isset($attribute_array[$key]) && is_array($attribute_array[$key])) {
                            foreach ($attribute_array[$key] as $akey => $aval) {//设置属性值，我这里为空
                                $node->addAttribute($akey, $aval);
                            }
                        }
                    }
                }
            }
        }
        /*--end*/

        $content = $xml->asXML(); //用asXML方法输出xml，默认只构造不输出。
        @file_put_contents($filename, $content);
    }
}

if (!function_exists('get_typeurl')) 
{
    /**
     * 获取栏目链接
     *
     * @param array $arctype_info 栏目信息
     * @param boolean $admin 后台访问链接，还是前台链接
     */
    function get_typeurl($arctype_info = array(), $admin = true)
    {
        /*问答模型*/
        if ($arctype_info['current_channel'] == 51) { 
            $typeurl = get_askurl("home/Ask/index");
            // 自动隐藏index.php入口文件
            $typeurl = auto_hide_index($typeurl);

            return $typeurl;
        }
        /*end*/

        static $domain = null;
        null === $domain && $domain = request()->domain();

        /*兼容采集没有归属栏目的文档*/
        if (empty($arctype_info['current_channel'])) {
            $channelRow = \think\Db::name('channeltype')->field('id as channel')
                ->where('id',1)
                ->find();
            $arctype_info = array_merge($arctype_info, $channelRow);
        }
        /*--end*/
        
        static $result = null;
        null === $result && $result = model('Channeltype')->getAll('id, ctl_name', array(), 'id');
        $ctl_name = '';
        if ($result) {
            $ctl_name = $result[$arctype_info['current_channel']]['ctl_name'];
        }

        static $seo_pseudo = null;
        static $seo_dynamic_format = null;
        if (null === $seo_pseudo || null === $seo_dynamic_format) {
            $seoConfig = tpCache('seo');
            $seo_pseudo = !empty($seoConfig['seo_pseudo']) ? $seoConfig['seo_pseudo'] : config('ey_config.seo_pseudo');
            $seo_dynamic_format = !empty($seoConfig['seo_dynamic_format']) ? $seoConfig['seo_dynamic_format'] : config('ey_config.seo_dynamic_format');
        }

        if (2 == $seo_pseudo && $admin) {
            static $lang = null;
            null === $lang && $lang = input('param.lang/s', 'cn');
            $typeurl = ROOT_DIR."/index.php?m=home&c=Lists&a=index&tid={$arctype_info['id']}&lang={$lang}&t=".getTime();
        } else {
            $typeurl = typeurl("home/{$ctl_name}/lists", $arctype_info, true, $domain, $seo_pseudo, $seo_dynamic_format);
            // 自动隐藏index.php入口文件
            $typeurl = auto_hide_index($typeurl);
        }

        return $typeurl;
    }
}

if (!function_exists('get_arcurl')) 
{
    /**
     * 获取文档链接
     *
     * @param array $arctype_info 栏目信息
     * @param boolean $admin 后台访问链接，还是前台链接
     */
    function get_arcurl($arcview_info = array(), $admin = true)
    {
        static $domain = null;
        null === $domain && $domain = request()->domain();

        /*兼容采集没有归属栏目的文档*/
        if (empty($arcview_info['channel'])) {
            $channelRow = \think\Db::name('channeltype')->field('id as channel')
                ->where('id',1)
                ->find();
            $arcview_info = array_merge($arcview_info, $channelRow);
        }
        /*--end*/

        static $result = null;
        null === $result && $result = model('Channeltype')->getAll('id, ctl_name', array(), 'id');
        $ctl_name = '';
        if ($result) {
            $ctl_name = $result[$arcview_info['channel']]['ctl_name'];
        }

        static $seo_pseudo = null;
        static $seo_dynamic_format = null;
        if (null === $seo_pseudo || null === $seo_dynamic_format) {
            $seoConfig = tpCache('seo');
            $seo_pseudo = !empty($seoConfig['seo_pseudo']) ? $seoConfig['seo_pseudo'] : config('ey_config.seo_pseudo');
            $seo_dynamic_format = !empty($seoConfig['seo_dynamic_format']) ? $seoConfig['seo_dynamic_format'] : config('ey_config.seo_dynamic_format');
        }
        
        if ($admin) {
            if (2 == $seo_pseudo) {
                static $lang = null;
                null === $lang && $lang = input('param.lang/s', 'cn');
                $arcurl = ROOT_DIR."/index.php?m=home&c=View&a=index&aid={$arcview_info['aid']}&lang={$lang}&admin_id=".session('admin_id');
            } else {
                $arcurl = arcurl("home/{$ctl_name}/view", $arcview_info, true, $domain, $seo_pseudo, $seo_dynamic_format);
                // 自动隐藏index.php入口文件
                $arcurl = auto_hide_index($arcurl);
                if (stristr($arcurl, '?')) {
                    $arcurl .= '&admin_id='.session('admin_id');
                } else {
                    $arcurl .= '?admin_id='.session('admin_id');
                }
            }
        } else {
            $arcurl = arcurl("home/{$ctl_name}/view", $arcview_info, true, $domain, $seo_pseudo, $seo_dynamic_format);
            // 自动隐藏index.php入口文件
            $arcurl = auto_hide_index($arcurl);
        }

        return $arcurl;
    }
}

if (!function_exists('get_askurl')) 
{
    /**
     * 获取问答链接
     *
     * @param array $arctype_info 栏目信息
     * @param boolean $admin 后台访问链接，还是前台链接
     */
    function get_askurl($url = '', $ask_info = array(), $admin = true)
    {
        static $domain = null;
        null === $domain && $domain = request()->domain();

        static $seo_pseudo = null;
        static $seo_dynamic_format = null;
        if (null === $seo_pseudo || null === $seo_dynamic_format) {
            $seoConfig = tpCache('seo');
            $seo_pseudo = !empty($seoConfig['seo_pseudo']) ? $seoConfig['seo_pseudo'] : config('ey_config.seo_pseudo');
            $seo_dynamic_format = !empty($seoConfig['seo_dynamic_format']) ? $seoConfig['seo_dynamic_format'] : config('ey_config.seo_dynamic_format');
        }

        $askurl = askurl($url, $ask_info, true, $domain, $seo_pseudo, $seo_dynamic_format);
        // 自动隐藏index.php入口文件
        $askurl = auto_hide_index($askurl);

        return $askurl;
    }
}

if (!function_exists('get_tagurl')) 
{
    /**
     * 获取标签链接
     *
     * @param array $tagid 标签ID
     */
    function get_tagurl($tagid = '')
    {
        static $seo_pseudo = null;
        static $seo_dynamic_format = null;
        if (null === $seo_pseudo || null === $seo_dynamic_format) {
            $seoConfig = tpCache('seo');
            $seo_pseudo = !empty($seoConfig['seo_pseudo']) ? $seoConfig['seo_pseudo'] : config('ey_config.seo_pseudo');
            $seo_dynamic_format = !empty($seoConfig['seo_dynamic_format']) ? $seoConfig['seo_dynamic_format'] : config('ey_config.seo_dynamic_format');
        }
    
        $tagurl = tagurl("home/Tags/lists", ['tagid'=>$tagid], true, true, $seo_pseudo, $seo_dynamic_format);
        // 自动隐藏index.php入口文件
        $tagurl = auto_hide_index($tagurl);

        return $tagurl;
    }
}

if (!function_exists('get_homeurl')) 
{
    /**
     * 获取前台链接
     *
     * @param array $tagid 标签ID
     */
    function get_homeurl($mca = '', $vars = [])
    {
        static $seo_pseudo = null;
        static $seo_dynamic_format = null;
        if (null === $seo_pseudo || null === $seo_dynamic_format) {
            $seoConfig = tpCache('seo');
            $seo_pseudo = !empty($seoConfig['seo_pseudo']) ? $seoConfig['seo_pseudo'] : config('ey_config.seo_pseudo');
            $seo_dynamic_format = !empty($seoConfig['seo_dynamic_format']) ? $seoConfig['seo_dynamic_format'] : config('ey_config.seo_dynamic_format');
        }
    
        $url = url($mca, $vars, true, true, $seo_pseudo, $seo_dynamic_format);
        // 自动隐藏index.php入口文件
        $url = auto_hide_index($url);

        return $url;
    }
}

if (!function_exists('get_total_arc')) 
{
    /**
     * 获取指定栏目的文档数
     */
    function get_total_arc($typeid)
    {
        static $arctypeList = null;
        static $archivesNums = null;
        static $guestbookNums = null;
        static $allow_release_channel = [];
        if (null === $arctypeList) {
            $allow_release_channel = config('global.allow_release_channel');
            $arctypeList = \think\Db::name('arctype')->field('id, parent_id, current_channel')->where([
                    'lang'  => get_admin_lang(),
                    'is_del'    => 0,
                    'weapp_code' => ['EQ', ''],
                ])->getAllWithIndex('id');

            /*-----------------文档列表模型统计数 start--------------*/
            $map = [
                'channel'   => ['IN', $allow_release_channel],
                'lang'  => get_admin_lang(),
                'is_del'    => 0, // 回收站功能
            ];
            $mapNew = "(users_id = 0 OR (users_id > 0 AND arcrank >= 0))";

            /*权限控制 by 小虎哥*/
            $admin_info = session('admin_info');
            if (0 < intval($admin_info['role_id'])) {
                $auth_role_info = $admin_info['auth_role_info'];
                if(! empty($auth_role_info)){
                    if(isset($auth_role_info['only_oneself']) && 1 == $auth_role_info['only_oneself']){
                        $map['admin_id'] = $admin_info['admin_id'];
                    }
                }
            }
            /*--end*/
            $SqlQuery = \think\Db::name('archives')->field('typeid, count(typeid) as num')->where($map)->where($mapNew)->group('typeid')->select(false);
            $SqlResult = \think\Db::name('sql_cache_table')->where(['sql_md5'=>md5($SqlQuery)])->getField('sql_result');
            if (!empty($SqlResult)) {
                $archivesNums = json_decode($SqlResult, true);
            } else {
                $archivesNums = \think\Db::name('archives')->field('typeid, count(typeid) as num')->where($map)->where($mapNew)->group('typeid')->getAllWithIndex('typeid');
                /*添加查询执行语句到mysql缓存表*/
                $SqlCacheTable = [
                    'sql_name' => '|arctype|all|count|',
                    'sql_result' => json_encode($archivesNums),
                    'sql_md5' => md5($SqlQuery),
                    'sql_query' => $SqlQuery,
                    'add_time' => getTime(),
                    'update_time' => getTime(),
                ];
                \think\Db::name('sql_cache_table')->insertGetId($SqlCacheTable);
                /*END*/
            }
            /*-----------------文档列表模型统计数 end--------------*/

            /*-----------------留言模型 start--------------*/
            $guestbookNums = \think\Db::name('guestbook')->field('typeid, count(typeid) as num')->where([
                    'lang'  => get_admin_lang(),
                ])->group('typeid')->getAllWithIndex('typeid');
            /*-----------------留言模型 end--------------*/
        }

        $totalnum = 0;
        if (!empty($arctypeList[$typeid])) {
            $current_channel = $arctypeList[$typeid]['current_channel'];
            if (in_array($current_channel, $allow_release_channel)) { // 能发布文档的模型
                $result = model('Arctype')->getHasChildren($typeid);
                $typeidArr = get_arr_column($result, 'id');
                foreach($typeidArr as $tid)
                {
                    $totalnum += (!empty($archivesNums[$tid]['num']) ? $archivesNums[$tid]['num'] : 0);
                }
            } elseif ($current_channel == 8) { // 留言模型
                $totalnum = !empty($guestbookNums[$typeid]['num']) ? $guestbookNums[$typeid]['num'] : 0;
            }
        }
            
        return $totalnum;
    }
}

if (!function_exists('replace_path')) 
{
    /**
     * 将路径斜杆、反斜杠替换为冒号符，适用于IIS服务器在URL上的双重转义限制
     * @param string $filepath 相对路径
     * @param string $replacement 目标字符
     * @param boolean $is_back false为替换，true为还原
     */
    function replace_path($filepath = '', $replacement = ':', $is_back = false)
    {
        if (false == $is_back) {
            $filepath = str_replace(DIRECTORY_SEPARATOR, $replacement, $filepath);
            $filepath = preg_replace('#\/#', $replacement, $filepath);
        } else {
            $filepath = preg_replace('#'.$replacement.'#', '/', $filepath);
            $filepath = str_replace('//', ':/', $filepath);
        }
        return $filepath;
    }
}

if (!function_exists('get_seo_pseudo_list')) 
{
    /**
     * URL模式下拉列表
     */
    function get_seo_pseudo_list($key = '')
    {
        $data = array(
            1   => '动态URL',
            3   => '伪静态化',
            2   => '静态页面',
        );

        return isset($data[$key]) ? $data[$key] : $data;
    }
}

if (!function_exists('get_chown_pathinfo')) 
{
    /**
     * 对指定的操作系统获取目录的所有组与所有者
     * @param string $path 目录路径
     * @return array
     */
    function get_chown_pathinfo($path = '') 
    {
        $pathinfo = true;

        if (function_exists('stat')) {
            /*指定操作系统，在列表内才进行后续获取*/
            $isValidate = false;
            $os = PHP_OS;
            $osList = array('linux','unix');
            foreach ($osList as $key => $val) {
                if (stristr($os, $val)) {
                    $isValidate = true;
                    continue;
                }
            }
            /*--end*/

            if (true === $isValidate) {
                $path = !empty($path) ? $path : ROOT_PATH;
                $stat = stat($path);
                if (function_exists('posix_getpwuid')) {
                    $pathinfo = posix_getpwuid($stat['uid']); 
                } else {
                    $pathinfo = array(
                        'name'  => (0 == $stat['uid']) ? 'root' : '',
                        'uid'  => $stat['uid'],
                        'gid'  => $stat['gid'],
                    );
                }
            }
        }

        return $pathinfo;
    }
}

if (!function_exists('menu_select')) 
{
    /*组装成层级下拉列表框*/
    function menu_select($selected = 0)
    {
        $select_html = '';
        $menuArr = getAllMenu();
        if (!empty($menuArr)) {
            foreach ($menuArr AS $key => $val)
            {
                $select_html .= '<option value="' . $val['id'] . '" data-grade="' . $val['grade'] . '"';
                $select_html .= ($selected == $val['id']) ? ' selected="ture"' : '';
                if (!empty($val['child'])) {
                    $select_html .= ' disabled="true" style="background-color:#f5f5f5;"';
                }
                $select_html .= '>';
                if ($val['grade'] > 0)
                {
                    $select_html .= str_repeat('&nbsp;', $val['grade'] * 4);
                }
                $name = !empty($val['name']) ? $val['name'] : '默认';
                $select_html .= htmlspecialchars(addslashes($name)) . '</option>';

                if (empty($val['child'])) {
                    continue;
                }
                foreach ($menuArr[$key]['child'] as $key2 => $val2) {
                    $select_html .= '<option value="' . $val2['id'] . '" data-grade="' . $val2['grade'] . '"';
                    $select_html .= ($selected == $val2['id']) ? ' selected="ture"' : '';
                    if (!empty($val2['child'])) {
                        $select_html .= ' disabled="true" style="background-color:#f5f5f5;"';
                    }
                    $select_html .= '>';
                    if ($val2['grade'] > 0)
                    {
                        $select_html .= str_repeat('&nbsp;', $val2['grade'] * 4);
                    }
                    $select_html .= htmlspecialchars(addslashes($val2['name'])) . '</option>';

                    if (empty($val2['child'])) {
                        continue;
                    }
                    foreach ($menuArr[$key]['child'][$key2]['child'] as $key3 => $val3) {
                        $select_html .= '<option value="' . $val3['id'] . '" data-grade="' . $val3['grade'] . '"';
                        $select_html .= ($selected == $val3['id']) ? ' selected="ture"' : '';
                        if (!empty($val3['child'])) {
                            $select_html .= ' disabled="true" style="background-color:#f5f5f5;"';
                        }
                        $select_html .= '>';
                        if ($val3['grade'] > 0)
                        {
                            $select_html .= str_repeat('&nbsp;', $val3['grade'] * 4);
                        }
                        $select_html .= htmlspecialchars(addslashes($val3['name'])) . '</option>';
                    }
                }
            }
        }

        return $select_html;
    }
}

if (!function_exists('schemaTable')) 
{
    /**
     * 重新生成单个数据表缓存字段文件
     */
    function schemaTable($name)
    {
        $table = $name;
        $prefix = \think\Config::get('database.prefix');
        if (!preg_match('/^'.$prefix.'/i', $name)) {
            $table = $prefix.$name;
        }
        /*调用命令行的指令*/
        \think\Console::call('optimize:schema', ['--table', $table]);
        /*--end*/
    }
}

if (!function_exists('schemaAllTable')) 
{
    /**
     * 重新生成全部数据表缓存字段文件
     */
    function schemaAllTable()
    {
        $dbtables = \think\Db::query('SHOW TABLE STATUS');
        $tableList = [];
        foreach ($dbtables as $k => $v) {
            if (preg_match('/^'.PREFIX.'/i', $v['Name'])) {
                /*调用命令行的指令*/
                \think\Console::call('optimize:schema', ['--table', $v['Name']]);
                /*--end*/
            }
        }
    }
}

if (!function_exists('testWriteAble')) 
{
    /**
     * 测试目录路径是否有写入权限
     * @param string $d 目录路劲
     * @return boolean
     */
    function testWriteAble($filepath)
    {
        $tfile = '_eyout.txt';
        $fp = @fopen($filepath.$tfile,'w');
        if(!$fp) {
            return false;
        }
        else {
            fclose($fp);
            $rs = @unlink($filepath.$tfile);
            return true;
        }
    }
}

if (!function_exists('getArchivesSortUrl')) 
{
    /**
     * 在文档列表拼接排序URL
     * @param string $orderby 排序字段
     * @param string $orderwayDefault 默认为空时升序
     * @return string
     */
    function getArchivesSortUrl($orderby = '', $orderwayDefault = '')
    {
        $parseArr = parse_url(request()->url());
        $query_str = '';
        if (!empty($parseArr['query'])) {
            parse_str($parseArr['query'], $output);
            $output['orderby'] = $orderby;

            $orderway = input('param.orderway/s', $orderwayDefault);
            $orderway = !empty($orderway) ? $orderway : 'desc';
            if ('desc' == $orderway) {
                $orderway = 'asc';
            } else {
                $orderway = 'desc';
                // 再次点击恢复到默认排序
                // if ('arcrank' == $orderby) {
                //     $output['orderby'] = '';
                // }
            }
            $output['orderway'] = $orderway;

            $query_str = http_build_query($output);
        }

        $url = $parseArr['path'];
        !empty($query_str) && $url .= '?'.$query_str;

        return $url;
    }
}

if (!function_exists('showArchivesFlagStr')) 
{
    /**
     * 在文档列表显示文档属性标识
     * @param array $archivesInfo 文档信息
     * @return string
     */
    function showArchivesFlagStr($archivesInfo = [])
    {
        static $flagResult = null;
        if (null === $flagResult) {
            $flagResult = \think\Db::name('archives_flag')->field('flag_name,flag_fieldname')->getAllWithIndex('flag_fieldname');
        }

        $arr = [];
        $flaglist = ['is_head','is_recom','is_special','is_b','is_jump','is_roll','is_slide','is_diyattr'];
        foreach ($flaglist as $key => $fieldname) {
            if (!empty($archivesInfo[$fieldname]) && !empty($flagResult[$fieldname]['flag_name'])) {
                $small_name = msubstr($flagResult[$fieldname]['flag_name'], 0, 1);
                $arr[$fieldname] = [
                    'small_name'   => $small_name,
                ];
            }
        }

        return $arr;
    }
}

if (!function_exists('checkPasswordLevel')) 
{
    /**
     * 检查密码复杂度
     * @param string $strPassword 密码
     * @return string
     */
    function checkPasswordLevel($strPassword = '')
    {
        $result = 0;
        $pwdlen = strlen($strPassword);
        if ( $pwdlen == 0) {
            $result += 0;
        }
        else if ( $pwdlen<8 && $pwdlen >0 ) {
            $result += 5;
        }
        else if ($pwdlen>10) {
            $result += 25;
        }
        else {
            $result += 10;
        }
        
        //check letter
        $bHave = false;
        $bAll = false;
        $capital = preg_match('/[A-Z]{1}/', $strPassword);//找大写字母
        $small = preg_match('/[a-z]{1}/', $strPassword);//找小写字母
        if ( empty($capital) && empty($small) )
        {
            $result += 0; //没有字母
            $bHave = false;
        }
        else if ( !empty($capital) && !empty($small) )
        {
            $result += 20;
            $bAll = true;
        }
        else
        {   
            $result += 10;
            $bAll = true;
        }
        
        //检查数字
        $bDigi = false;
        $digitalLen = 0;
        for ( $i=0; $i<$pwdlen; $i++)
        {
        
            if ( $strPassword[$i] <= '9' && $strPassword[$i] >= '0' )
            {
                $bDigi = true;
                $digitalLen += 1;
            }
            
        }
        if ( $digitalLen==0 )//没有数字
        {
            $result += 0;
            $bDigi = false;
        }
        else if ($digitalLen>2)//2个数字以上
        {
            $result += 20 ;
            $bDigi = true;
        }
        else
        {
            $result += 10;
            $bDigi = true;
        }
        
        //检查非单词字符
        $bOther = false;
        $otherLen = 0;
        for ($i=0; $i<$pwdlen; $i++)
        {
            if ( ($strPassword[$i]>='0' && $strPassword[$i]<='9') ||  
                ($strPassword[$i]>='A' && $strPassword[$i]<='Z') ||
                ($strPassword[$i]>='a' && $strPassword[$i]<='z')) {
                continue;
            }
            $otherLen += 1;
            $bOther = true;
        }
        if ( $otherLen == 0 )//没有非单词字符
        {
            $result += 0;
            $bOther = false;
        }
        else if ( $otherLen >1)//1个以上非单词字符
        {
            $result +=25 ;
            $bOther = true;
        }
        else
        {
            $result +=10;
            $bOther = true;
        }
        
        //检查额外奖励
        if ( $bAll && $bDigi && $bOther) {
            $result += 5;
        }
        else if ($bHave && $bDigi && $bOther) {
            $result += 3;
        }
        else if ($bHave && $bDigi ) {
            $result += 2;
        }

        $level = 0;
        //根据分数来算密码强度的等级
        if ( $result >=80 )
            $level = 7;
        else if ( $result>=70)
            $level = 6;
        else if ( $result>=60)
            $level = 5;
        else if ( $result>=50)
            $level = 4;
        else if ( $result>=40)
            $level = 3;
        else if ( $result>20)
            $level = 2;
        else if ( $result>0)
            $level = 1;
        else
            $level = 0;

        return $level;
    }
}

if (!function_exists('getPasswordLevelTitle')) 
{
    /**
     * 获取密码复杂度名称
     * @param string $level 复杂程度
     * @return string
     */
    function getPasswordLevelTitle($level = 0)
    {
        $title = '弱';
        //根据分数来算密码强度的等级
        if ( $level == 7 ) {
            $title = '极佳';
        }
        else if ( $level == 6) {
            $title = '非常强';
        }
        else if ( $level == 5) {
            $title = '强';
        }
        else if ( $level == 4) {
            $title = '较强';
        }
        else if ( $level == 3) {
            $title = '一般';
        }
        else if ( $level == 2) {
            $title = '较弱';
        }
        else if ( $level == 1) {
            $title = '非常弱';
        }
        else {
            $title = '弱';
        }

        return $title;
    }
}

if (!function_exists('downloadExcel')) {
    /**
     * 下载excel
     * @param $strTable    表格内容
     * @param $filename 文件名
     */
    function downloadExcel($strTable, $filename)
    {
        ob_end_clean();
        header("Content-type: application/vnd.ms-excel");
        header("Content-Type: application/force-download");
        header("Content-Disposition: attachment; filename=" . $filename . "_" . date('Y-m-d') . ".xls");
        header('Expires:0');
        header('Pragma:public');
        echo '<html><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . $strTable . '</html>';
    }
}

if (!function_exists('check_single_uiset')) {
    /**
     * 检测指定单页模板是否支持可视化
     */
    function check_single_uiset($templist = '')
    {
        $uisetRow = [];
        $templist = !empty($templist) ? $templist : 'lists_single.htm';

        $file = "./template/".TPL_THEME."pc/{$templist}";
        if (file_exists($file)) {
            $content = @file_get_contents($file);
            if (!empty($content) && preg_match('/eyou\:ui(\s+)open\=(\'|\")(on|off)(\'|\")/i', $content)) {
                $uisetRow['pc'] = true;
            }
        }

        $file = "./template/".TPL_THEME."mobile/{$templist}";
        if (file_exists($file)) {
            $content = @file_get_contents($file);
            if (!empty($content) && preg_match('/eyou\:ui(\s+)open\=(\'|\")(on|off)(\'|\")/i', $content)) {
                $uisetRow['mobile'] = true;
            }
        }

        return $uisetRow;
    }
}