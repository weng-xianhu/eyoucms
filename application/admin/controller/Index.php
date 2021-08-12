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

namespace app\admin\controller;
use app\admin\controller\Base;
use think\Controller;
use think\Db;

class Index extends Base
{
    public function index()
    {
        $language_db = Db::name('language');

        /*多语言列表*/
        $web_language_switch = tpCache('web.web_language_switch');
        $languages = [];
        if (1 == intval($web_language_switch)) {
            $languages = $language_db->field('a.mark, a.title')
                ->alias('a')
                ->where('a.status',1)
                ->getAllWithIndex('mark');
        }
        $this->assign('languages', $languages);
        $this->assign('web_language_switch', $web_language_switch);
        /*--end*/

        /*代理贴牌功能限制-s*/
        $function_switch = $upgrade = true;
        if (function_exists('checkAuthRule')) {
            // 功能地图
            $function_switch = checkAuthRule(2004008);
            // 系统更新
            $upgrade = checkAuthRule('upgrade');
        }
        $this->assign('function_switch', $function_switch);
        $this->assign('upgrade', $upgrade);
        /*代理贴牌功能限制-e*/

        /*小程序开关*/
        $web_diyminipro_switch = tpCache('web.web_diyminipro_switch');
        if (!is_dir('./weapp/Diyminipro/') || $this->admin_lang != $this->main_lang) {
            $web_diyminipro_switch = -1;
        }
        $this->assign('web_diyminipro_switch', $web_diyminipro_switch);
        /*end*/

        /*网站首页链接*/
        // 去掉入口文件
        $inletStr = '/index.php';
        $seo_inlet = config('ey_config.seo_inlet');
        1 == intval($seo_inlet) && $inletStr = '';
        // --end
        $home_default_lang = config('ey_config.system_home_default_lang');
        $admin_lang = $this->admin_lang;
        $home_url = request()->domain().ROOT_DIR.'/';  // 支持子目录
        if ($home_default_lang != $admin_lang) {
            $home_url = $language_db->where(['mark'=>$admin_lang])->getField('url');
            if (empty($home_url)) {
                $seoConfig = tpCache('seo');
                $seo_pseudo = !empty($seoConfig['seo_pseudo']) ? $seoConfig['seo_pseudo'] : config('ey_config.seo_pseudo');
                if (1 == $seo_pseudo) {
                    $home_url = request()->domain().ROOT_DIR.$inletStr; // 支持子目录
                    if (!empty($inletStr)) {
                        $home_url .= '?';
                    } else {
                        $home_url .= '/?';
                    }
                    $home_url .= http_build_query(['lang'=>$admin_lang]);
                } else {
                    $home_url = request()->domain().ROOT_DIR.$inletStr.'/'.$admin_lang; // 支持子目录
                }
            }
        }
        $this->assign('home_url', $home_url);
        /*--end*/

        $this->assign('admin_info', getAdminInfo(session('admin_id')));
        $this->assign('menu',getMenuList());

        /*检测是否存在会员中心模板*/
        if ('v1.0.1' > getVersion('version_themeusers') && !empty($this->globalConfig['web_users_switch'])) {
            $is_syn_theme_users = 1;
        } else {
            $is_syn_theme_users = 0;
        }
        $this->assign('is_syn_theme_users',$is_syn_theme_users);
        /*--end*/

        // 是否开启安全补丁
        $security_patch = tpSetting('upgrade.upgrade_security_patch');
        if (empty($security_patch)) $security_patch = 0;
        $this->assign('security_patch', $security_patch);

        // 统计未读的站内信数量
        action('admin/Notify/count_unread_notify');

        return $this->fetch();
    }
   
    public function welcome()
    {
        // 更新数据缓存表信息
        $this->update_sql_cache_table();
        
        /*小程序组件更新*/
        $is_update_component_access = 1;
        if (!is_dir('./weapp/Diyminipro/') || $this->admin_lang != $this->main_lang) {
            $is_update_component_access = 0;
        }
        $this->assign('is_update_component_access', $is_update_component_access);
        /*end*/

        // 纠正上传附件的大小，始终以空间大小为准
        $file_size = $this->globalConfig['file_size'];
        $maxFileupload = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 0;
        $maxFileupload = intval($maxFileupload);
        if (empty($file_size) || $file_size > $maxFileupload) {
            /*多语言*/
            if (is_language()) {
                $langRow = Db::name('language')->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->order('id asc')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache('basic', ['file_size'=>$maxFileupload], $val['mark']);
                }
            } else { // 单语言
                tpCache('basic', ['file_size'=>$maxFileupload]);
            }
            /*--end*/
        }

        /*未备份数据库提示*/
        $system_explanation_welcome = !empty($this->globalConfig['system_explanation_welcome']) ? $this->globalConfig['system_explanation_welcome'] : 0;
        $sqlfiles = glob(DATA_PATH.'sqldata/*');
        foreach ($sqlfiles as $file) {
            if(stristr($file, getCmsVersion())){
                $system_explanation_welcome = 1;
            }
        }
        $this->assign('system_explanation_welcome', $system_explanation_welcome);
        /*--end*/

        /*检查密码复杂度*/
        $admin_login_pwdlevel = -1;
        $system_explanation_welcome_2 = !empty($this->globalConfig['system_explanation_welcome_2']) ? $this->globalConfig['system_explanation_welcome_2'] : 0;
        if (empty($system_explanation_welcome_2)) {
            $admin_login_pwdlevel = session('admin_login_pwdlevel');
            if (!session('?admin_login_pwdlevel') || 3 < intval($admin_login_pwdlevel)) {
                $system_explanation_welcome_2 = 1;
            }
        }
        $this->assign('admin_login_pwdlevel', $admin_login_pwdlevel);
        $this->assign('system_explanation_welcome_2', $system_explanation_welcome_2);
        /*end*/

        // 同步导航与内容统计的状态
        $this->syn_open_quickmenu();

        // 快捷导航
        $quickMenu = Db::name('quickentry')->where([
                'type'      => 1,
                'checked'   => 1,
                'status'    => 1,
            ])->order('sort_order asc, id asc')->select();
        foreach ($quickMenu as $key => $val) {
            if ($this->php_servicemeal <= 1 && $val['controller'] == 'Shop' && $val['action'] == 'index') {
                unset($quickMenu[$key]);
                continue;
            }
            $quickMenu[$key]['vars'] = !empty($val['vars']) ? $val['vars']."&lang=".$this->admin_lang : "lang=".$this->admin_lang;
        }
        $this->assign('quickMenu',$quickMenu);

        // 内容统计
        $contentTotal = $this->contentTotalList();
        $this->assign('contentTotal',$contentTotal);

        // 是否开启安全补丁
        $security_patch = tpSetting('upgrade.upgrade_security_patch');
        if (empty($security_patch)) $security_patch = 0;
        $this->assign('security_patch', $security_patch);

        /*代理贴牌功能限制-s*/
        $upgrade = true;
        if (function_exists('checkAuthRule')) {
            //系统更新
            $upgrade = checkAuthRule('upgrade');
        }
        $this->assign('upgrade', $upgrade);
        /*代理贴牌功能限制-e*/

        // 服务器信息
        $this->assign('sys_info',$this->get_sys_info());
        // 升级弹窗
        $this->assign('web_show_popup_upgrade', $this->globalConfig['web_show_popup_upgrade']);

        // 升级系统时，同时处理sql语句
        $this->synExecuteSql();

        $ajaxLogic = new \app\admin\logic\AjaxLogic;
        $ajaxLogic->update_template('users'); // 升级前台会员中心的模板文件
        $ajaxLogic->system_langnum_file(); // 记录当前是多语言还是单语言到文件里
        $ajaxLogic->admin_logic_1609900642(); // 内置方法
        $ajaxLogic->admin_logic_1608884981();// 补充后台登录logo与背景图(v1.6.1节点去掉)
        $ajaxLogic->admin_logic_1610086647(); // 内置手机端会员中心底部菜单数据(v1.6.1节点去掉)
        $ajaxLogic->admin_logic_model_addfields(); // 同步内置模型内置的附加表字段(v1.6.1节点去掉)
        $ajaxLogic->admin_logic_arctype_topid(); // 纠正栏目的topid字段值(v1.6.1节点去掉)
        $ajaxLogic->admin_logic_balance_pay(); // 内置余额支付开关(v1.6.1节点去掉)
        $ajaxLogic->admin_logic_1610086648(); // 文档图片自适应修改为默认关闭(v1.6.1节点去掉)
        $ajaxLogic->admin_logic_1614829120(); // 补充站内信模板的数据(v1.6.1节点去掉)
        $ajaxLogic->admin_logic_1616123192(); // 补充邮箱/短信模板的数据(v1.6.1节点去掉)
        // 补充问题点赞表的like_source字段(v1.6.1节点去掉--陈风任)
        $ajaxLogic->admin_logic_1617069276();
        // 纠正商品主表的评价数(appraise 字段)、收藏数(collection 字段)(v1.6.1节点去掉--陈风任)
        $ajaxLogic->admin_logic_archives_1618279798();

        // 纠正SQL缓存表结果字段类型(v1.6.1节点去掉--陈风任)
        $ajaxLogic->admin_logic_1623036205();
        
        return $this->fetch();
    }

    /**
     * 升级系统时，同时处理sql语句
     * @return [type] [description]
     */
    private function synExecuteSql()
    {
        // 新增订单提醒的邮箱模板
        if (!tpCache('system.system_smtp_tpl_5')){
            /*多语言*/
            if (is_language()) {
                $langRow = Db::name('language')->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->order('id asc')
                    ->select();
                foreach ($langRow as $key => $val) {
                    $r = Db::name('smtp_tpl')->insert([
                        'tpl_name'      => '订单提醒',
                        'tpl_title'     => '您有新的订单消息，请查收！',
                        'tpl_content'   => '${content}',
                        'send_scene'    => 5,
                        'is_open'       => 1,
                        'lang'          => $val['mark'],
                        'add_time'      => getTime(),
                    ]);
                    false !== $r && tpCache('system', ['system_smtp_tpl_5' => 1], $val['mark']);
                }
            } else { // 单语言
                $r = Db::name('smtp_tpl')->insert([
                    'tpl_name'      => '订单提醒',
                    'tpl_title'     => '您有新的订单消息，请查收！',
                    'tpl_content'   => '${content}',
                    'send_scene'    => 5,
                    'is_open'       => 1,
                    'lang'          => $this->admin_lang,
                    'add_time'      => getTime(),
                ]);
                false !== $r && tpCache('system', ['system_smtp_tpl_5' => 1]);
            }
            /*--end*/
        }
    }

    /**
     * 内容统计管理
     */
    public function ajax_content_total()
    {
        if (IS_AJAX_POST) {
            $checkedids = input('post.checkedids/a', []);
            $ids = input('post.ids/a', []);
            $saveData = [];
            foreach ($ids as $key => $val) {
                if (in_array($val, $checkedids)) {
                    $checked = 1;
                } else {
                    $checked = 0;
                }
                $saveData[$key] = [
                    'id'            => $val,
                    'checked'       => $checked,
                    'sort_order'    => intval($key) + 1,
                    'update_time'   => getTime(),
                ];
            }
            if (!empty($saveData)) {
                $r = model('Quickentry')->saveAll($saveData);
                if ($r) {
                    $this->success('操作成功', url('Index/welcome'));
                }
            }
            $this->error('操作失败');
        }

        /*同步v1.3.9以及早期版本的自定义模型*/
        $this->syn_custom_quickmenu(2);
        /*end*/

        $totalList = Db::name('quickentry')->where([
                'type'      => ['IN', [2]],
                'status'    => 1,
            ])->order('sort_order asc, id asc')->select();
        $this->assign('totalList',$totalList);

        return $this->fetch();
    }

    /**
     * 内容统计 - 数量处理
     */
    private function contentTotalList()
    {
        $archivesTotalRow = null;
        $quickentryList = Db::name('quickentry')->where([
                'type'      => 2,
                'checked'   => 1,
                'status'    => 1,
            ])->order('sort_order asc, id asc')->select();
        foreach ($quickentryList as $key => $val) {
            $code = $val['controller'].'@'.$val['action'].'@'.$val['vars'];
            $quickentryList[$key]['vars'] = !empty($val['vars']) ? $val['vars']."&lang=".$this->admin_lang : "lang=".$this->admin_lang;
            if ($code == 'Guestbook@index@channel=8') // 留言列表
            {
                $map = [
                    'lang'    => $this->admin_lang,
                ];
                $quickentryList[$key]['total'] = Db::name('guestbook')->where($map)->count();
            }
            else if (1 == $val['groups']) // 模型内容统计
            {
                if (null === $archivesTotalRow) {
                    $map = [
                        'lang'    => $this->admin_lang,
                        'status'    => 1,
                        'is_del'    => 0,
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
                    $SqlQuery = Db::name('archives')->field('channel, count(aid) as total')->where($map)->where($mapNew)->group('channel')->select(false);
                    $SqlResult = Db::name('sql_cache_table')->where(['sql_md5'=>md5($SqlQuery)])->getField('sql_result');
                    if (!empty($SqlResult)) {
                        $archivesTotalRow = json_decode($SqlResult, true);
                    } else {
                        $archivesTotalRow = Db::name('archives')->field('channel, count(aid) as total')->where($map)->where($mapNew)->group('channel')->getAllWithIndex('channel');
                        /*添加查询执行语句到mysql缓存表*/
                        $SqlCacheTable = [
                            'sql_name' => '|model|all|count|',
                            'sql_result' => json_encode($archivesTotalRow),
                            'sql_md5' => md5($SqlQuery),
                            'sql_query' => $SqlQuery,
                            'add_time' => getTime(),
                            'update_time' => getTime(),
                        ];
                        Db::name('sql_cache_table')->insertGetId($SqlCacheTable);
                        /*END*/
                    }
                }
                parse_str($val['vars'], $vars);
                $total = !empty($archivesTotalRow[$vars['channel']]['total']) ? intval($archivesTotalRow[$vars['channel']]['total']) : 0;
                $quickentryList[$key]['total'] = $total;
            }
            else if ($code == 'AdPosition@index@') // 广告
            {
                $map = [
                    'lang'    => $this->admin_lang,
                    'is_del'    => 0,
                ];
                $quickentryList[$key]['total'] = Db::name('ad_position')->where($map)->count();
            }
            else if ($code == 'Links@index@') // 友情链接
            {
                $map = [
                    'lang'    => $this->admin_lang,
                ];
                $quickentryList[$key]['total'] = Db::name('links')->where($map)->count();
            }
            else if ($code == 'Tags@index@') // Tags标签
            {
                $map = [
                    'lang'    => $this->admin_lang,
                ];
                $quickentryList[$key]['total'] = Db::name('tagindex')->where($map)->count();
            }
            else if ($code == 'Member@users_index@') // 会员
            {
                $map = [
                    'lang'    => $this->admin_lang,
                    'is_del'    => 0,
                ];
                $quickentryList[$key]['total'] = Db::name('users')->where($map)->count();
            }
            else if ($code == 'Shop@index@') // 订单
            {
                $map = [
                    'lang'    => $this->admin_lang,
                ];
                $quickentryList[$key]['total'] = Db::name('shop_order')->where($map)->count();
            }
        }

        return $quickentryList;
    }

    /**
     * 快捷导航管理
     */
    public function ajax_quickmenu()
    {
        if (IS_AJAX_POST) {
            $checkedids = input('post.checkedids/a', []);
            $ids = input('post.ids/a', []);
            $saveData = [];
            foreach ($ids as $key => $val) {
                if (in_array($val, $checkedids)) {
                    $checked = 1;
                } else {
                    $checked = 0;
                }
                $saveData[$key] = [
                    'id'            => $val,
                    'checked'       => $checked,
                    'sort_order'    => intval($key) + 1,
                    'update_time'   => getTime(),
                ];
            }
            if (!empty($saveData)) {
                $r = model('Quickentry')->saveAll($saveData);
                if ($r) {
                    $this->success('操作成功', url('Index/welcome'));
                }
            }
            $this->error('操作失败');
        }

        /*同步v1.3.9以及早期版本的自定义模型*/
        $this->syn_custom_quickmenu(1);
        /*end*/

        $menuList = Db::name('quickentry')->where([
                'type'      => ['IN', [1]],
                'groups'    => 0,
                'status'    => 1,
            ])->order('sort_order asc, id asc')->select();
        foreach ($menuList as $key => $val) {
            if ($this->php_servicemeal <= 2 && $val['controller'] == 'Shop' && $val['action'] == 'index') {
                unset($menuList[$key]);
                continue;
            }
        }
        $this->assign('menuList',$menuList);

        return $this->fetch();
    }

    /**
     * 同步自定义模型的快捷导航
     */
    private function syn_custom_quickmenu($type = 1)
    {
        $row = Db::name('quickentry')->where([
                'controller'    => 'Custom',
                'type'  => $type,
            ])->count();
        if (empty($row)) {
            $customRow = Db::name('channeltype')->field('id,ntitle')
                ->where(['ifsystem'=>0])->select();
            $saveData = [];
            foreach ($customRow as $key => $val) {
                $saveData[] = [
                    'title' => $val['ntitle'],
                    'laytext'   => $val['ntitle'].'列表',
                    'type' => $type,
                    'controller' => 'Custom',
                    'action' => 'index',
                    'vars' => 'channel='.$val['id'],
                    'groups'    => 1,
                    'sort_order' => 100,
                    'add_time' => getTime(),
                    'update_time' => getTime(),
                ];
            }
            model('Quickentry')->saveAll($saveData);
        }
    }

    /**
     * 同步受开关控制的导航和内容统计
     */
    private function syn_open_quickmenu()
    {
        $tpcacheConfig = tpCache('global');
        $usersConfig = getUsersConfigData('all');

        /*商城中心 - 受本身开关和会员中心开关控制*/
        if (!empty($tpcacheConfig['web_users_switch']) && !empty($usersConfig['shop_open'])) {
            $shop_open = 1;
        } else {
            $shop_open = 0;
        }
        /*end*/

        $saveData = [
            [
                'id'    => 31,
                'status'    => !empty($tpcacheConfig['web_users_switch']) ? 1 : 0,
                'update_time'   => getTime(),
            ],
            [
                'id'    => 32,
                'status'    => (1 == $tpcacheConfig['web_weapp_switch']) ? 1 : 0,
                'update_time'   => getTime(),
            ],
            [
                'id'    => 33,
                'status'    => !empty($tpcacheConfig['web_users_switch']) ? 1 : 0,
                'update_time'   => getTime(),
            ],
            [
                'id'    => 34,
                'status'    => $shop_open,
                'update_time'   => getTime(),
            ],
            [
                'id'    => 35,
                'status'    => $shop_open,
                'update_time'   => getTime(),
            ],
        ];
        model('Quickentry')->saveAll($saveData);

        /*处理模型导航和统计*/
        $channeltypeRow = Db::name('channeltype')->cache(true,EYOUCMS_CACHE_TIME,"channeltype")->select();
        foreach ($channeltypeRow as $key => $val) {
            $updateData = [
                'groups'    => 1,
                'vars'  => 'channel='.$val['id'],
                'status'    => $val['status'],
                'update_time'   => getTime(),
            ];
            Db::name('quickentry')->where([
                    'vars' => 'channel='.$val['id']
                ])->update($updateData);
        }
        /*end*/
    }

    /**
     * 服务器信息
     */
    private function get_sys_info()
    {
        $sys_info['os']             = PHP_OS;
        $sys_info['zlib']           = function_exists('gzclose') ? 'YES' : '<font color="red">NO（请开启 php.ini 中的php-zlib扩展）</font>';//zlib
        $sys_info['safe_mode']      = (boolean) ini_get('safe_mode') ? 'YES' : 'NO';//safe_mode = Off       
        $sys_info['timezone']       = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
        $sys_info['curl']           = function_exists('curl_init') ? 'YES' : '<font color="red">NO（请开启 php.ini 中的php-curl扩展）</font>';  
        $web_server                 = $_SERVER['SERVER_SOFTWARE'];
        if (stristr($web_server, 'apache')) {
            $web_server = 'apache';
        } else if (stristr($web_server, 'nginx')) {
            $web_server = 'nginx';
        } else if (stristr($web_server, 'iis')) {
            $web_server = 'iis';
        }
        $sys_info['web_server']     = $web_server;
        $sys_info['phpv']           = phpversion();
        $sys_info['ip']             = serverIP();
        $sys_info['postsize']       = @ini_get('file_uploads') ? ini_get('post_max_size') :'未知';
        $sys_info['fileupload']     = @ini_get('file_uploads') ? ini_get('upload_max_filesize') :'未开启';
        $sys_info['max_ex_time']    = @ini_get("max_execution_time").'s'; //脚本最大执行时间
        $sys_info['set_time_limit'] = function_exists("set_time_limit") ? true : false;
        $sys_info['domain']         = $_SERVER['HTTP_HOST'];
        $sys_info['memory_limit']   = ini_get('memory_limit');
        $sys_info['version']        = file_get_contents(DATA_PATH.'conf/version.txt');
        $mysqlinfo = Db::query("SELECT VERSION() as version");
        $sys_info['mysql_version']  = $mysqlinfo[0]['version'];
        if(function_exists("gd_info")){
            $gd = gd_info();
            $sys_info['gdinfo']     = $gd['GD Version'];
        }else {
            $sys_info['gdinfo']     = "未知";
        }
        if (extension_loaded('zip')) {
            $sys_info['zip']     = "YES";
        } else {
            $sys_info['zip']     = '<font color="red">NO（请开启 php.ini 中的php-zip扩展）</font>';
        }
        $upgradeLogic = new \app\admin\logic\UpgradeLogic();
        $sys_info['curent_version'] = $upgradeLogic->curent_version; //当前程序版本
        $sys_info['web_name'] = tpCache('global.web_name');

        return $sys_info;
    }

    /**
     * 录入商业授权
     */
    public function authortoken()
    {
        $domain = config('service_ey');
        $domain = base64_decode($domain);
        $vaules = array(
            'client_domain' => urldecode($this->request->host(true)),
        );
        $url = $domain.'/index.php?m=api&c=Service&a=check_authortoken&'.http_build_query($vaules);
        $context = stream_context_set_default(array('http' => array('timeout' => 3,'method'=>'GET')));
        $response = @file_get_contents($url,false,$context);
        if (false === $response) {
            $url = str_replace('http://service', 'https://service', $url);
            $response = @httpRequest($url);
        }
        $params = json_decode($response,true);
        if (false === $response || (is_array($params) && 1 == $params['code'])) {
            $web_authortoken = $params['msg'];
            /*多语言*/
            if (is_language()) {
                $langRow = Db::name('language')->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->order('id asc')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache('web', ['web_authortoken'=>$web_authortoken], $val['mark']);
                }
            } else { // 单语言
                tpCache('web', array('web_authortoken'=>$web_authortoken));
            }
            /*--end*/

            $source = realpath('public/static/admin/images/logo_ey.png');
            $destination = realpath('public/static/admin/images/logo.png');
            @copy($source, $destination);

            delFile(RUNTIME_PATH.'html'); // 清空缓存页面
            session('isset_author', null);
            adminLog('验证商业授权');
            $this->success('域名授权成功', request()->baseFile(), '', 1, [], '_parent');
        }
        $this->error('域名（'.$this->request->domain().'）未授权', request()->baseFile(), '', 3, [], '_parent');
    }

    /**
     * 更换后台logo
     */
    public function edit_adminlogo()
    {
        $filename = input('param.filename/s', '');
        if (!empty($filename)) {
            $source = realpath(preg_replace('#^'.ROOT_DIR.'/#i', '', $filename)); // 支持子目录
            $web_is_authortoken = tpCache('web.web_is_authortoken');
            if (empty($web_is_authortoken)) {
                $destination = realpath('public/static/admin/images/logo.png');
            } else {
                $destination = realpath('public/static/admin/images/logo_ey.png');
            }
            if (@copy($source, $destination)) {
                $this->success('操作成功');
            }
        }
        $this->error('操作失败');
    }

    /**
     * 待处理事项
     */
    public function pending_matters()
    {
        $html = '<div style="text-align: center; margin: 20px 0px; color:red;">惹妹子生气了，没啥好处理！</div>';
        echo $html;
    }
    
    /**
     * ajax 修改指定表数据字段  一般修改状态 比如 是否推荐 是否开启 等 图标切换的
     * table,id_name,id_value,field,value
     */
    public function changeTableVal()
    {
        if (IS_AJAX_POST) {
            $url = null;
            $data = [
                'refresh'   => 0,
            ];

            $param    = input('param.');
            $table    = input('param.table/s'); // 表名
            $id_name  = input('param.id_name/s'); // 表主键id名
            $id_value = input('param.id_value/d'); // 表主键id值
            $field    = input('param.field/s'); // 修改哪个字段
            $value    = input('param.value/s', '', null); // 修改字段值
            $value    = eyPreventShell($value) ? $value : strip_sql($value);
            if ('archives' == $table && 'arcrank' == $field) {
                $ScreeningTable = $table;
                $ScreeningField = $field;
                $ScreeningValue = $value;
                $ScreeningAid   = $id_value;
            }

            /*插件专用*/
            if ('weapp' == $table) {
                if (1 == intval($value)) { // 启用
                    action('Weapp/enable', ['id' => $id_value]);
                } else if (-1 == intval($value)) { // 禁用
                    action('Weapp/disable', ['id' => $id_value]);
                }
            }
            /*end*/

            /*处理数据的安全性*/
            if (empty($id_value)) {
                $this->error('查询条件id不合法！');
            }
            foreach ($param as $key => $val) {
                if ('value' == $key) {
                    continue;
                }
                if (!preg_match('/^([A-Za-z0-9_-]*)$/i', $val)) {
                    $this->error('数据含有非法入侵字符！');
                }
            }
            /*end*/

            switch ($table) {
                // 会员等级表
                case 'users_level':
                    {
                        $return = model('UsersLevel')->isRequired($id_name,$id_value,$field,$value);
                        if (is_array($return)) {
                            $this->error($return['msg']);
                        }
                    }
                    break;
                
                // 会员属性表
                case 'users_parameter':
                    {
                        $return = model('UsersParameter')->isRequired($id_name,$id_value,$field,$value);
                        if (is_array($return)) {
                            $time = !empty($return['time']) ? $return['time'] : 3;
                            $this->error($return['msg'], null, [], $time);
                        }
                    }
                    break;
                
                // 会员中心菜单表
                case 'users_menu':
                    {
                        if ('is_userpage' == $field) {
                            Db::name('users_menu')->where('id','gt',0)->update([
                                    'is_userpage'   => 0,
                                    'update_time'   => getTime(),
                                ]);
                        }
                        $data['refresh'] = 1;
                    }
                    break;
                
                // 会员投稿功能
                case 'archives':
                    {
                        if ('arcrank' == $field) {
                            if (0 == $value) {
                                $value = -1;
                            }else{
                                $value = 0;
                            }
                        }
                    }
                    break;

                // 会员产品类型表
                case 'users_type_manage':
                    {
                        if (in_array($field, ['type_name','price'])) {
                            if (empty($value)) {
                                $this->error('不可为空');
                            }
                        }
                    }
                    break;

                // 留言属性表
                case 'guestbook_attribute':
                    {
                        $return = model('GuestbookAttribute')->isValidate($id_name,$id_value,$field,$value);
                        if (is_array($return)) {
                            $time = !empty($return['time']) ? $return['time'] : 3;
                            $this->error($return['msg'], null, [], $time);
                        }
                    }
                    break;

                // 小程序页面表
                case 'diyminipro_page':
                    {
                        $re = Db::name('diyminipro_page')->where([
                            'is_home'    => 1,
                            $id_name    => ['EQ', $id_value],
                        ])->count();
                        if (!empty($re)) {
                            $this->error('禁止取消默认项', null, [], 3);
                        }
                    }
                    break;

                // 文档属性表
                case 'archives_flag':
                    {
                        if ('sort_order' == $field) {
                            $data['refresh'] = 1;
                            $data['time'] = 500;
                        }
                    }
                    break;
                // 会员中心移动端底部菜单表
                case 'users_bottom_menu':
                    {
                        if ('sort_order' == $field) {
                            $data['refresh'] = 1;
                            $data['time'] = 500;
                        }
                    }
                    break;

                // 友情链接分组表
                case 'links_group':
                    {
                        if ('sort_order' == $field) {
                            $data['refresh'] = 1;
                            $data['time'] = 500;
                        }
                    }
                    break;

                // 栏目表
                case 'arctype':
                    {
                        if ('is_hidden' == $field) {
                            $value = (1 == $value) ? 0 : 1;
                        }
                    }
                    break;

                default:
                    # code...
                    break;
            }

            $savedata = [
                $field => $value,
                'update_time'   => getTime(),
            ];
            switch ($table) {
                case 'diyminipro_page':
                {
                    if ('is_home' == $field) {
                        if ($value == 1) {
                            $savedata['page_type'] = 1;
                        } else {
                            $savedata['page_type'] = -1;
                        }
                    }
                    break;
                }
            }
            // 根据条件保存修改的数据
            $r = Db::name($table)->where([$id_name => $id_value])->cache(true,null,$table)->save($savedata);
            if ($r !== false) {
                if ('archives' == $ScreeningTable && 'arcrank' == $ScreeningField) {
                    $Result = model('SqlCacheTable')->ScreeningArchives($ScreeningAid, $ScreeningValue);
                    if (!empty($Result)) {
                        $data['refresh'] = 1;
                        $data['time'] = 500;
                    }
                }
                // 以下代码可以考虑去掉，与行为里的清除缓存重复 AppEndBehavior.php / clearHtmlCache
                switch ($table) {
                    case 'auth_modular':
                        extra_cache('admin_auth_modular_list_logic', null);
                        extra_cache('admin_all_menu', null);
                        break;

                    case 'diyminipro_page':
                    {
                        if ('is_home' == $field) {
                            $data['refresh'] = 1;
                            Db::name('diyminipro_page')->where([
                                $id_name    => ['NEQ', $id_value],
                                'lang'      => $this->admin_lang,
                            ])->update([
                                'is_home'    => 0, 
                                'page_type'    => -1, 
                                'update_time'   => getTime()
                            ]);
                        }
                        break;
                    }
                
                    // 会员投稿功能
                    case 'archives':
                    {
                        if ('arcrank' == $field) {
                            Db::name('taglist')->where('aid', $id_value)->update([
                                'arcrank'=>$value,
                                'update_time'   => getTime(),
                            ]);
                        }
                        break;
                    }
                    
                    default:
                        // 清除logic逻辑定义的缓存
                        extra_cache('admin_'.$table.'_list_logic', null);
                        // 清除一下缓存
                        // delFile(RUNTIME_PATH.'html'); // 先清除缓存, 否则不好预览
                        \think\Cache::clear($table);
                        break;
                }
                $this->success('更新成功', $url, $data);
            }
            $this->error('更新失败', null, []);
        }
    }

    /**
     * 功能地图
     */
    public function switch_map()
    {
        if (IS_POST) {
            $inc_type = input('post.inc_type/s');
            $name = input('post.name/s');
            $value = input('post.value/s');

            $data = [];
            switch ($inc_type) {
                case 'pay':
                case 'shop':
                {
                    getUsersConfigData($inc_type, [$name => $value]);

                    // 开启商城
                    if (1 == $value) {
                        /*多语言 - 同时开启会员中心*/
                        if (is_language()) {
                            $langRow = \think\Db::name('language')->order('id asc')
                                ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                                ->select();
                            foreach ($langRow as $key => $val) {
                                tpCache('web', ['web_users_switch' => 1], $val['mark']);
                            }
                        } else { // 单语言
                            tpCache('web', ['web_users_switch' => 1]);
                        }
                        /*--end*/

                        // 同时显示发布文档时的价格文本框
                        Db::name('channelfield')->where([
                                'name'   => 'users_price',
                                'channel_id'  => 2,
                            ])->update([
                                'ifeditable'    => 1,
                                'update_time'   => getTime(),
                            ]);
                    }

                    if (in_array($name, ['shop_open'])) {
                        // $data['reload'] = 1;
                        /*检测是否存在订单中心模板*/
                        if ('v1.0.1' > getVersion('version_themeshop') && !empty($value)) {
                            $is_syn = 1;
                        } else {
                            $is_syn = 0;
                        }
                        $data['is_syn'] = $is_syn;
                        /*--end*/
                        // 同步会员中心的左侧菜单
                        if ('shop_open' == $name) {
                            Db::name('users_menu')->where([
                                    'mca'   => 'user/Shop/shop_centre',
                                    'lang'  => $this->admin_lang,
                                ])->update([
                                    'status'    => (1 == $value) ? 1 : 0,
                                    'update_time'   => getTime(),
                                ]);
                        }
                    } else if ('pay_open' == $name) {
                        // 同步会员中心的左侧菜单
                        Db::name('users_menu')->where([
                                'mca'   => 'user/Pay/pay_consumer_details',
                                'lang'  => $this->admin_lang,
                            ])->update([
                                'status'    => (1 == $value) ? 1 : 0,
                                'update_time'   => getTime(),
                            ]);
                    }

                    //同步会员中心手机端底部菜单开关  ---start
                    Db::name('users_bottom_menu')->where([
                        'mca'   => ['IN',['user/Shop/shop_centre','user/Shop/shop_cart_list',]]
                    ])->update([
                        'status'    => (1 == $value) ? 1 : 0,
                        'update_time'   => getTime(),
                    ]);
                    //同步会员中心手机端底部菜单开关  ---end


                    break;
                }

                case 'users':
                {
                    // 会员投稿
                    //同步会员中心手机端底部菜单开关  ---start
                    Db::name('users_bottom_menu')->where([
                        'mca'   => ['IN',['user/UsersRelease/article_add','user/UsersRelease/release_centre',]]
                    ])->update([
                        'status'    => (1 == $value) ? 1 : 0,
                        'update_time'   => getTime(),
                    ]);
                    //同步会员中心手机端底部菜单开关  ---end

                    // 会员投稿
                    $r = Db::name('users_menu')->where([
                        'mca'  => 'user/UsersRelease/release_centre',
                        'lang' => $this->admin_lang,
                    ])->update([
                        'status'      => (1 == $value) ? 1 : 0,
                        'update_time' => getTime(),
                    ]);
                    if ($r) {
                        getUsersConfigData($inc_type, [$name => $value]);

                        if (1 == $value) {
                            /*多语言 - 同时开启会员中心*/
                            if (is_language()) {
                                $langRow = \think\Db::name('language')->order('id asc')
                                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                                    ->select();
                                foreach ($langRow as $key => $val) {
                                    tpCache('web', ['web_users_switch' => 1], $val['mark']);
                                }
                            } else { // 单语言
                                tpCache('web', ['web_users_switch' => 1]);
                            }
                            /*--end*/
                        }
                    }
                    break;
                }

                case 'level':
                {
                    // 会员升级
                    //同步会员中心手机端底部菜单开关  ---start
                    Db::name('users_bottom_menu')->where([
                        'mca'   => ['IN',['user/Level/level_centre','user/Pay/pay_account_recharge',]]
                    ])->update([
                        'status'    => (1 == $value) ? 1 : 0,
                        'update_time'   => getTime(),
                    ]);
                    //同步会员中心手机端底部菜单开关  ---end

                    // 会员升级
                    $r = Db::name('users_menu')->where([
                        'mca'  => 'user/Level/level_centre',
                        'lang' => $this->admin_lang,
                    ])->update([
                        'status'      => (1 == $value) ? 1 : 0,
                        'update_time' => getTime(),
                    ]);
                    if ($r) {
                        getUsersConfigData($inc_type, [$name => $value]);

                        if (1 == $value) {
                            /*多语言 - 同时开启会员中心*/
                            if (is_language()) {
                                $langRow = \think\Db::name('language')->order('id asc')
                                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                                    ->select();
                                foreach ($langRow as $key => $val) {
                                    tpCache('web', ['web_users_switch' => 1], $val['mark']);
                                }
                            } else { // 单语言
                                tpCache('web', ['web_users_switch' => 1]);
                            }
                            /*--end*/
                        }
                    }
                    break;
                }

                case 'web':
                {
                    /*多语言*/
                    if (is_language()) {
                        $langRow = \think\Db::name('language')->order('id asc')
                            ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                            ->select();
                        foreach ($langRow as $key => $val) {
                            tpCache($inc_type, [$name => $value], $val['mark']);
                        }
                    } else { // 单语言
                        tpCache($inc_type, [$name => $value]);
                    }
                    /*--end*/

                    if (in_array($name, ['web_users_switch'])) {
                        // $data['reload'] = 1;
                        /*检测是否存在会员中心模板*/
                        if ('v1.0.1' > getVersion('version_themeusers') && !empty($value)) {
                            $is_syn = 1;
                        } else {
                            $is_syn = 0;
                        }
                        $data['is_syn'] = $is_syn;
                        /*--end*/
                        // 同时关闭会员相关的开关
                        if (empty($value)) {
                            getUsersConfigData('users', ['users_open_release' => 0]); // 会员投稿
                            getUsersConfigData('level', ['level_member_upgrade' => 0]); // 会员升级
                            getUsersConfigData('shop', ['shop_open' => 0]); // 商城中心
                            getUsersConfigData('pay', ['pay_open' => 0]); // 支付功能
                        }
                    } else if ($name == 'web_language_switch') { // 多语言开关
                        // 统计多语言数量
                        model('Language')->setLangNum();
                        // 重新生成sitemap.xml
                        sitemap_all();
                    }
                    break;
                }
            }

            $this->success('操作成功', null, $data);
        }

        /*代理贴牌功能限制-s*/
        $weapp_switch = true;
        if (function_exists('checkAuthRule')) {
            //插件应用
            $weapp_switch = checkAuthRule(2005);
        }
        $this->assign('weapp_switch', $weapp_switch);
        /*代理贴牌功能限制-e*/
        
        $this->assign('globalConfig', $this->globalConfig);

        $UsersConfigData = getUsersConfigData('all');
        $this->assign('userConfig',$UsersConfigData);

        $is_online = 0;
        if (is_realdomain()) {
            $is_online = 1;
        }
        $this->assign('is_online',$is_online);

        /*检测是否存在会员中心模板*/
        if ('v1.0.1' > getVersion('version_themeusers')) {
            $is_themeusers_exist = 1;
        } else {
            $is_themeusers_exist = 0;
        }
        $this->assign('is_themeusers_exist',$is_themeusers_exist);
        /*--end*/

        /*检测是否存在商城中心模板*/
        if ('v1.0.1' > getVersion('version_themeshop')) {
            $is_themeshop_exist = 1;
        } else {
            $is_themeshop_exist = 0;
        }
        $this->assign('is_themeshop_exist',$is_themeshop_exist);
        /*--end*/

        /*支付接口*/
        $pay = Db::name('pay_api_config')->where('status', 1)->order('pay_id asc')->select();
        foreach ($pay as $key => $val) {
            if (1 == $val['system_built']) {
                $val['litpic'] = $this->root_dir . "/public/static/admin/images/{$val['pay_mark']}.png";
            } else {
                $val['litpic'] = $this->root_dir . "/weapp/{$val['pay_mark']}/logo.png";
            }
            $pay[$key] = $val;
        }
        $this->assign('pay_list', $pay);
        /*--end*/

        $recycle_switch = tpSetting('recycle.recycle_switch');
        $this->assign('recycle_switch', $recycle_switch);//回收站
        return $this->fetch();
    }

    // 更新数据缓存表信息
    public function update_sql_cache_table()
    {
        $CacheMaxID = Db::name('sql_cache_table')->where('sql_name', 'ArchivesMaxID')->getField('sql_result');
        if (empty($CacheMaxID)) {
            // 添加查询执行语句到mysql缓存表
            model('SqlCacheTable')->InsertSqlCacheTable(true);
        } else {
            $ArchivesMaxID = Db::name('archives')->max('aid');
            if ($ArchivesMaxID != $CacheMaxID) {
                /*清空sql_cache_table数据缓存表 并 添加查询执行语句到mysql缓存表*/
                Db::name('sql_cache_table')->query('TRUNCATE TABLE '.config('database.prefix').'sql_cache_table');
                model('SqlCacheTable')->InsertSqlCacheTable(true);
                /* END */
            }
        }
    }
}
