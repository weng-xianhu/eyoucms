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
use think\Page;
use app\admin\logic\UpgradeLogic;
use app\admin\logic\EyouCmsLogic;

class Index extends Base
{
    public $eyouCmsLogic;

    public function _initialize()
    {
        parent::_initialize();
        $this->eyouCmsLogic = new EyouCmsLogic;
        //初始化admin_menu表（将原来左边栏目设置为跟原来一样）
        $ajaxLogic = new \app\admin\logic\AjaxLogic;
        $ajaxLogic->initialize_admin_menu();
    }
    
    public function index()
    {
        // wxh_database_column();
        // wxh_source_filelist();
        // wxh_handle_database();

        $language_db = Db::name('language');
        /*多语言列表*/
        $web_language_switch = tpCache('global.web_language_switch');
        $languages = [];
        $languages = $language_db->field('a.mark, a.title')
            ->alias('a')
            ->where('a.status',1)
            ->order('sort_order asc,id asc')
            ->getAllWithIndex('mark');
        $this->assign('languages', $languages);
        $this->assign('web_language_switch', $web_language_switch);
        /*--end*/

        $web_adminlogo = tpCache('web.web_adminlogo', [], $this->main_lang);
        $this->assign('web_adminlogo', handle_subdir_pic($web_adminlogo));

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
        $diyminipro_list = [];
        if ($this->admin_lang == $this->main_lang) {
            $diyminipro_list = Db::name('weapp')->field('id,code,name,config')->where(['code'=>['IN',['Diyminipro','DiyminiproMall','BdDiyminipro','TtDiyminipro','ZfbDiyminipro']],'status'=>1])->order('code desc')->select();
            foreach ($diyminipro_list as $key => $val) {
                $val['config'] = (array)json_decode($val['config']);
                $val['litpic'] = empty($val['config']['litpic']) ? '' : handle_subdir_pic($val['config']['litpic']);
                if ('Diyminipro' == $val['code']) {
                    $val['name'] = '微信企业小程序';
                } else if ('DiyminiproMall' == $val['code']) {
                    $val['name'] = '微信商城小程序';
                } else if ('BdDiyminipro' == $val['code']) {
                    $val['name'] = '百度企业小程序';
                } else if ('TtDiyminipro' == $val['code']) {
                    $val['name'] = '抖音企业小程序';
                } else if ('ZfbDiyminipro' == $val['code']) {
                    $val['name'] = '支付宝企业小程序';
                } 
                $diyminipro_list[$key] = $val;
            }
        }
        $this->assign('diyminipro_list', $diyminipro_list);
        /*end*/

        //获取前台入口链接
        $this->assign('home_url', $this->eyouCmsLogic->shouye($this->globalConfig));
        /*--end*/
        $this->assign('admin_info', getAdminInfo(session('admin_id')));
        //左侧菜单列表（old）
//        $this->assign('menu',getMenuList());
        //获取所有权限
        $all_menu_tree = getAllMenu();
        $all_menu_list = tree_to_list($all_menu_tree,'child','id');
        $this->assign('all_menu_list',$all_menu_list);
        function_exists('highlights_trash') && highlights_trash();
        //获取选中的权限
        $ajaxLogic = new \app\admin\logic\AjaxLogic;
        $ajaxLogic->admin_menu_clear();
        // $ajaxLogic->eyou_v165_del_func();
        $menu_list = Db::name("admin_menu")->where(['status'=>1,'is_menu'=>1])->order("sort_order asc,update_time asc,id asc")->select();
        foreach ($menu_list as $key => $val) {
            if (stristr($val['param'], '|sm|Diyminipro|')) {
                $val['title'] = '微信企业小程序';
            } else if (stristr($val['param'], '|sm|DiyminiproMall|')) {
                $val['title'] = '微信商城小程序';
            } else if (stristr($val['param'], '|sm|BdDiyminipro|')) {
                $val['title'] = '百度企业小程序';
            } else if (stristr($val['param'], '|sm|TtDiyminipro|')) {
                $val['title'] = '抖音企业小程序';
            } else if (stristr($val['param'], '|sm|ZfbDiyminipro|')) {
                $val['title'] = '支付宝企业小程序';
            }
            $menu_list[$key] = $val;

            // 其他语言不显示留言管理
            /*if ($this->admin_lang != $this->main_lang) {
                foreach ([2004018] as $_k => $_v) {
                    if ($_v == $val['menu_id']) {
                        unset($menu_list[$key]);
                    }
                }
            }*/
        }
        $menu_list = getAdminMenuList($menu_list);
        $this->assign('menu_list',$menu_list);
        //获取因为没有开启相关模块没有权限的节点
        $not_role_menu_id_arr = get_not_role_menu_id();
        $this->assign('not_role_menu_id_arr',$not_role_menu_id_arr);

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
        $assign_data = [];
        // 更新数据缓存表信息
        $this->update_sql_cache_table();
        
        /*小程序组件更新*/
        $assign_data['is_update_component_access'] = 1;
        if (!is_dir('./weapp/Diyminipro/') || $this->admin_lang != $this->main_lang) {
            $assign_data['is_update_component_access'] = 0;
        }
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

        /*检查密码复杂度*/
        $admin_login_pwdlevel = -1;
        $system_explanation_welcome_2 = !empty($this->globalConfig['system_explanation_welcome_2']) ? $this->globalConfig['system_explanation_welcome_2'] : 0;
        if (empty($system_explanation_welcome_2)) {
            $admin_login_pwdlevel = session('admin_login_pwdlevel');
            if (!session('?admin_login_pwdlevel') || 3 < intval($admin_login_pwdlevel)) {
                $system_explanation_welcome_2 = 1;
            }
        }
        $assign_data['admin_login_pwdlevel'] = $admin_login_pwdlevel;
        $assign_data['system_explanation_welcome_2'] = $system_explanation_welcome_2;
        /*end*/
        
        // 定期检查多久没有扫描
        $ddos_scan_last_time = (int)tpSetting('ddos.ddos_scan_last_time', [], 'cn');
        $admin_info = session('admin_info');
        if (!empty($admin_info['is_founder']) && $admin_info['login_cnt'] <= 3) { // 第一次安装登录后，默认已扫描
            $ddos_scan_last_time = getTime();
            tpSetting('ddos', ['ddos_scan_last_time'=>$ddos_scan_last_time], 'cn');
            Db::name('ddos_log')->where(['id'=>['gt', 0]])->delete(true);
            Db::name('ddos_whitelist')->where(['id'=>['gt', 0]])->delete(true);
            $ddosLogic = new \app\admin\logic\DdosLogic;
            $ddosLogic->ddos_setting('web.source_dirlist_total', 0);
            // 删除地图
            // $admin_logic_1758271813 = tpSetting('syn.admin_logic_1758271813', [], 'cn');
            // if (empty($admin_logic_1758271813)) {
            //     if (is_file('./sitemap.xml')) @unlink('./sitemap.xml');
            //     if (is_file('./sitemap.txt')) @unlink('./sitemap.txt');
            //     if (is_file('./sitemap.html')) @unlink('./sitemap.html');
            //     if (is_file('./ai-content-index.html')) @unlink('./ai-content-index.html');
            //     if (is_file('./llms.txt')) @unlink('./llms.txt');
            //     tpSetting('syn', ['admin_logic_1758271813'=>1], 'cn');
            // }
        }
        $ddos_scan_day = 15;
        $system_explanation_welcome_4 = 1;
        if (intval($ddos_scan_last_time) + ($ddos_scan_day * 86400) < getTime()) {
            $system_explanation_welcome_4 = 0;
        }
        $assign_data['system_explanation_welcome_4'] = $system_explanation_welcome_4;
        $assign_data['ddos_scan_last_time'] = $ddos_scan_last_time;

        // 定期自动扫描发现风险
        if (empty($system_explanation_welcome_4)) {
            $system_explanation_welcome_5 = 1;
        } else {
            $system_explanation_welcome_5 = !empty($this->globalConfig['system_explanation_welcome_5']) ? $this->globalConfig['system_explanation_welcome_5'] : 0;
            if (empty($system_explanation_welcome_5)) {
                $ddos_logs = Db::name('ddos_log')->where(['admin_id' => (int)session('admin_id'), 'file_grade'=>['gt',0]])->count();
                $system_explanation_welcome_5 = empty($ddos_logs) ? 1 : 0;
            }
        }
        $assign_data['system_explanation_welcome_5'] = $system_explanation_welcome_5;

        /*代理贴牌功能限制-s*/
        $assign_data['upgrade'] = true;
        if (function_exists('checkAuthRule')) {
            //系统更新
            $assign_data['upgrade'] = checkAuthRule('upgrade');
        }
        /*代理贴牌功能限制-e*/

        // 是否开启安全补丁
        $assign_data['security_patch'] = (int)tpSetting('upgrade.upgrade_security_patch');
        // 升级弹窗
        if (2 == $this->globalConfig['web_show_popup_upgrade'] && $this->php_servicemeal <= 0) {
            $this->globalConfig['web_show_popup_upgrade'] = -1;
        }
        $assign_data['web_show_popup_upgrade'] = $this->globalConfig['web_show_popup_upgrade'];
        // 升级系统时，同时处理sql语句
        $this->synExecuteSql();

        $ajaxLogic = new \app\admin\logic\AjaxLogic;
        $ajaxLogic->system_langnum_file(); // 记录当前是多语言还是单语言到文件里
        $ajaxLogic->system_citysite_file(); // 记录当前是否多站点到文件里

        $ajaxLogic->admin_logic_1609900642(); // 内置方法
        // 纠正SQL缓存表结果字段类型(v1.6.1节点去掉--陈风任)
        $ajaxLogic->admin_logic_1623036205();

        $viewfile = 'welcome';
        $web_theme_welcome_tplname = empty($this->globalConfig['web_theme_welcome_tplname']) ? '' : $this->globalConfig['web_theme_welcome_tplname'];
        if (!empty($web_theme_welcome_tplname) && file_exists("application/admin/template/theme/{$web_theme_welcome_tplname}")) {
            $welcome_tplname = str_ireplace('.htm', '', $web_theme_welcome_tplname);
            $viewfile = "theme/{$welcome_tplname}";
        }

        if (preg_match('/^(.*)\/welcome_shop$/i', $viewfile)) {
            // 商城版欢迎页主题
            $this->eyouCmsLogic->welcome_shop($assign_data, $this->globalConfig, $this->usersConfig);
        } else if (preg_match('/^(.*)\/welcome_taskflow$/i', $viewfile)) {
            // 任务流版欢迎页主题
            $this->eyouCmsLogic->welcome_taskflow($assign_data, $this->globalConfig, $this->usersConfig);
        } else if (preg_match('/^(.*)\/welcome_ai$/i', $viewfile)) {
            // AI版欢迎页主题
            $this->eyouCmsLogic->welcome_ai($assign_data, $this->globalConfig, $this->usersConfig);
        } else {
            // 默认欢迎页主题
            $this->eyouCmsLogic->welcome_default($assign_data, $this->globalConfig, $this->usersConfig);
        }

        $this->assign($assign_data);
        $html = $this->fetch($viewfile);

        // 后台密码修改提醒 - 大黄
        if (file_exists('./weapp/PasswordRemind/model/PasswordRemindModel.php')) {
            $PasswordRemindModel = new \weapp\PasswordRemind\model\PasswordRemindModel;
            $html = $PasswordRemindModel->getInfo($html);
        }

        // 风险检测弹窗提示
        // $detecting_domain_str = getVersion('detecting_domain_pop', '');
        // $detecting_domain_arr = explode(',', $detecting_domain_str);
        if (is_dir('./weapp/Systemdoctor/')/* && in_array(request()->rootDomain(), $detecting_domain_arr)*/) {
            $pop_detecting_domain = tpSetting('pop.pop_detecting_domain', [], 'cn');
            if (empty($pop_detecting_domain)) {
                $set_pop_detecting_domain_url = url('Ajax/set_pop_detecting_domain');
                $security_url = url('Security/index', ['pop_detecting_domain'=>1]);
                $append_html =<<<EOF
<script type="text/javascript">
    pop_detecting_domain();
    function pop_detecting_domain()
    {
        var msg = '贵站近期未进行安全检测，建议前往安全中心扫描';
        layer.confirm(msg, {
                title: '提示'
                ,shade: layer_shade
                ,area: ['400px','180px']
                ,btn: ['立即前往'] //按钮
                ,success: function () {
                    $(".layui-layer-content").css('text-align', 'left');
                    $(".layui-layer-content").css('height', 'auto');
                }
                ,cancel: function(index){
                    $.getJSON("{$set_pop_detecting_domain_url}", {value:1,_ajax:1}, function(){});
                    layer.close(index);
                    return false;
                }
            }, function(){
                layer_loading('正在前往');
                window.location.href = "{$security_url}";
                return false;
            }, function(){
                return false;
            }
        );
    }
</script>
EOF;
            }
            if (stristr($html, '</body>')) {
                $html = str_replace('</body>', $append_html.'</body>', $html);
            } else if (stristr($html, '</head>')) {
                $html = str_replace('</head>', $append_html.'</head>', $html);
            }
        }

        return $html;
    }

    /**
     * 实时概况快捷导航管理
     */
    public function ajax_surveyquickmenu()
    {
        if (IS_AJAX_POST) {
            $checkedids = input('post.checkedids/a', []);
            if (count($checkedids) != 4){
                $this->error('保存数量必须为4个');
            }
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
                if ($r !== false) {
                    $this->success('操作成功', url('Index/welcome'));
                }
            }
            $this->error('操作失败');
        }
        $menuList = Db::name('quickentry')->where([
            'type'      => 21,
            'groups'    => 1,
            'status'    => 1,
        ])->order('sort_order asc, id asc')->select();

        $this->assign('menuList',$menuList);

        return $this->fetch();
    }

    /**
     * 实时概况快捷导航管理 - 任务流版
     */
    public function ajax_surveyquickmenu_taskflow()
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
                if ($r !== false) {
                    $this->success('操作成功', url('Index/welcome'));
                }
            }
            $this->error('操作失败');
        }
        $menuList = Db::name('quickentry')->where([
            'type'      => 31,
            'groups'    => 1,
            'status'    => 1,
        ])->order('sort_order asc, id asc')->select();

        $this->assign('menuList',$menuList);

        return $this->fetch();
    }

    /**
     * 升级系统时，同时处理sql语句
     * @return [type] [description]
     */
    private function synExecuteSql()
    {
        // 新增订单提醒的邮箱模板
        if (!tpCache('global.system_smtp_tpl_5')){
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
                if ($r !== false) {
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

        // 最大选中数量
        $max_selnum = input('param.max_selnum/d', 9);
        $this->assign('max_selnum',$max_selnum);

        // 页面类型
        $welcome_type = input('param.welcome_type/s');
        $this->assign('welcome_type',$welcome_type);

        return $this->fetch();
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
                if ($r !== false) {
                    $this->success('操作成功', url('Index/welcome'));
                }
            }
            $this->error('操作失败');
        }

        // 最大选中数量
        $max_selnum = input('param.max_selnum/d', 1000);
        $this->assign('max_selnum',$max_selnum);

        // 页面类型
        $welcome_type = input('param.welcome_type/s');
        $this->assign('welcome_type',$welcome_type);

        if ($welcome_type == 'shop') {
            $type = [11];
        } else {
            /*同步v1.3.9以及早期版本的自定义模型*/
            $this->syn_custom_quickmenu(1);
            /*end*/
            $type = [1];
        }

        $menuList = Db::name('quickentry')->where([
                'type'      => ['IN', $type],
                'groups'    => 0,
                'status'    => 1,
            ])->order('sort_order asc, id asc')->select();
        foreach ($menuList as $key => $val) {
            if ($this->php_servicemeal <= 2 && $val['controller'] == 'Shop' && $val['action'] == 'index') {
                unset($menuList[$key]);
                continue;
            }
            if (!empty($this->globalConfig['web_recycle_switch']) && $val['controller'] == 'RecycleBin' && $val['action'] == 'archives_index'){
                unset($menuList[$key]);
                continue;
            }
            if (is_language() && $this->main_lang != $this->admin_lang) {
                $controllerArr = ['Weapp','Filemanager','Sitemap','Admin','Member','Seo','Channeltype','Tools'];
                if (empty($globalConfig['language_split'])) {
                    $controllerArr[] = 'RecycleBin';
                }
                $ctlActArr = ['System@water','System@thumb','System@api_conf'];
                if (in_array($val['controller'], $controllerArr) || in_array($val['controller'].'@'.$val['action'], $ctlActArr)) {
                    unset($menuList[$key]);
                    continue;
                }
            }
        }
        $this->assign('menuList',$menuList);

        return $this->fetch();
    }

    /**
     * 常用功能
     */
    public function ajax_quickmenu_ai()
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
                if ($r !== false) {
                    $this->success('操作成功', url('Index/welcome'));
                }
            }
            $this->error('操作失败');
        }

        // 最大选中数量
        $max_selnum = input('param.max_selnum/d', 1000);
        $this->assign('max_selnum',$max_selnum);

        $menuList = model('Quickentry')->getMenuGroupList(['order'=>'sort_order asc, id asc']);
        foreach ($menuList as $key => $val) {
            if (empty($val['opt_access']) || empty($val['show_status'])) {
                unset($menuList[$key]);
            }
        }
        $this->assign('menuList',$menuList);

        return $this->fetch();
    }

    /**
     *
     * 插件快捷导航管理
     */
    public function ajax_weapp_quickmenu()
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
                    'quick_sort'    => intval($key) + 1,
                    'update_time'   => getTime(),
                ];
            }
            if (!empty($saveData)) {
                $r = model('Weapp')->saveAll($saveData);
                if ($r !== false) {
                    $this->success('操作成功', url('Index/welcome'));
                }
            }
            $this->error('操作失败');
        }

        $where = ['status'=>1];
        $menuList = Db::name('weapp')->where($where)->order('quick_sort asc, id asc')->select();
        $this->assign('menuList',$menuList);

        // 最大选中数量
        $max_selnum = input('param.max_selnum/d', 9);
        $this->assign('max_selnum',$max_selnum);

        // 页面类型
        $welcome_type = input('param.welcome_type/s');
        $this->assign('welcome_type',$welcome_type);

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
     * 录入商业授权
     */
    public function authortoken()
    {
        $is_force = input('param.is_force/d', 0);
        $redata = verify_authortoken($is_force);
        if (!empty($redata['code'])) {
            $source = realpath('public/static/admin/images/logo_ey.png');
            $destination = realpath('public/static/admin/images/logo.png');
            @copy($source, $destination);

            adminLog('验证授权');
            $this->success('授权校验成功', request()->baseFile(), '', 1, [], '_parent');
        }
        $msg = empty($redata['msg']) ? '域名（'.request()->host(true).'）未授权' : $redata['msg'];
        $this->error($msg, request()->baseFile(), '', 5, [], '_parent');
    }

    /**
     * 更换后台logo
     */
    public function edit_adminlogo()
    {
        $filename = input('param.filename/s', '');
        if (!empty($filename)) {
            $source = realpath(preg_replace('#^'.ROOT_DIR.'/#i', '', $filename)); // 支持子目录
            $web_is_authortoken = tpCache('global.web_is_authortoken');
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

            /* 安全校验：本接口仅用于后台列表的状态/排序等字段切换，
               严格限制可操作的数据表与字段，防止越权修改任意表的任意字段 start */
            // 表名、字段名、主键名只允许合法标识符
            $identityArr = ['table' => $table, 'field' => $field, 'id_name' => $id_name];
            foreach ($identityArr as $identityKey => $identityVal) {
                if (!preg_match('/^[A-Za-z0-9_]+$/', $identityVal)) {
                    $this->error('非法操作：参数 '.$identityKey.' 格式不合法！');
                }
            }
            $checkTable = strtolower($table);
            $checkField = strtolower($field);

            // 敏感数据表：一律禁止通过本通用接口更新
            $denyTables = [
                'admin', 'auth_group', 'auth_group_access', 'auth_rule', 'auth_role', 'auth_role_user',
                'config', 'users_money', 'users_score', 'users_oauth', 'users_session', 'sql_cache_table',
            ];
            if (in_array($checkTable, $denyTables)) {
                $this->error('非法操作：禁止通过该接口更新此数据表！');
            }

            // 敏感字段：任何数据表都禁止通过本通用接口更新
            $denyFields = [
                'password', 'salt', 'level', 'money', 'score', 'is_admin', 'role_id', 'group_id',
                'purview', 'username', 'admin_name', 'users_name', 'mobile', 'email',
                'token', 'openid', 'unionid', 'session_id',
            ];
            if (in_array($checkField, $denyFields)) {
                $this->error('非法操作：禁止通过该接口更新此字段！');
            }

            // 各数据表允许修改的字段白名单
            $allowFields = [
                'ad'                     => ['sort_order'],
                'ad_position'            => ['status'],
                'applets_template'       => ['is_open'],
                'archives'               => ['arcrank', 'is_recom', 'sort_order'],
                'archives_flag'          => ['flag_name', 'sort_order', 'status'],
                'arctype'                => ['is_hidden', 'sort_order'],
                'ask'                    => ['is_recom', 'is_review'],
                'ask_answer'             => ['is_review'],
                'ask_type'               => ['sort_order'],
                'auth_modular'           => ['sort_order', 'status'],
                'channelfield'           => ['sort_order'],
                'citysite'               => ['name', 'sort_order'],
                'discount_goods'         => ['sort_order', 'status'],
                'diyminipro_page'        => ['is_home', 'sort_order', 'status'],
                'faq_group'              => ['status'],
                'foreign_pack'           => ['value'],
                'form'                   => ['status'],
                'guestbook_attribute'    => ['is_showlist', 'sort_order'],
                'language'               => ['sort_order', 'status'],
                'links'                  => ['sort_order', 'status'],
                'links_group'            => ['sort_order', 'status'],
                'memgift'                => ['sort_order', 'status'],
                'nav_list'               => ['sort_order', 'status'],
                'nav_position'           => ['sort_order'],
                'product_attribute'      => ['sort_order'],
                'search_word'            => ['is_hot'],
                'sharp_active'           => ['status'],
                'sharp_active_time'      => ['status'],
                'sharp_goods'            => ['sort_order', 'status'],
                'shop_coupon'            => ['sort_order', 'status'],
                'shop_express'           => ['is_choose', 'sort_order'],
                'shop_order_comment'     => ['is_show'],
                'shop_product_attrlist'  => ['sort_order'],
                'shop_shipping_template' => ['template_money'],
                'sms_template'           => ['is_open', 'sms_sign', 'sms_tpl_code', 'tpl_content'],
                'smtp_tpl'               => ['is_open', 'tpl_title'],
                'tagindex'               => ['count', 'is_common'],
                'users'                  => ['is_activation'],
                'users_bottom_menu'      => ['display', 'icon', 'mca', 'sort_order', 'title'],
                'users_level'            => ['ask_is_release', 'ask_is_review', 'down_count', 'posts_count'],
                'users_list'             => ['is_hidden', 'is_required', 'sort_order'],
                'users_menu'             => ['is_userpage', 'status'],
                'users_notice_tpl'       => ['is_open', 'tpl_title'],
                'users_parameter'        => ['is_hidden', 'is_required', 'sort_order'],
                'users_recharge_pack'    => ['sort_order', 'status'],
                'users_type_manage'      => ['activity', 'price', 'sort_order', 'type_name'],
                'weapp'                  => ['status'],
                'wechat_template'        => ['is_open'],
            ];

            if (isset($allowFields[$checkTable])) {
                if (!in_array($checkField, $allowFields[$checkTable])) {
                    $this->error('非法操作：该数据表不允许修改此字段！');
                }
            } else if (0 !== strpos($checkTable, 'weapp_')) {
                // 非插件自建表（weapp_ 前缀）一律不允许通过本通用接口更新；
                // 插件表的字段由插件自行定义，此处仅由上面的敏感字段黑名单兜底
                $this->error('非法操作：禁止通过该接口更新此数据表！');
            }
            /* end */

            $_POST[$id_name] = $id_value;
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
                    if (stristr($val, '&lt;') && stristr($val, '&gt;')) {
                        $val = htmlspecialchars_decode($val);
                    }
                    if (preg_match('/<script([^\>]*)>/i', $val)) {
                        $this->error('数据含有非法入侵字符！');
                    }
                } else {
                    if (!preg_match('/^([A-Za-z0-9_-]*)$/i', $val)) {
                        $this->error('数据含有非法入侵字符！');
                    }
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
                        if (in_array($field, ['flag_name'])) {
                            if(empty($value)){
                                $this->error('属性名称不能为空', null, [],2);
                            }
                            $value = htmlspecialchars($value);
                        }
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

                // 多语言表
                case 'language':
                    {
                        $return = model('Language')->isValidateStatus($field,$value);
                        if (is_array($return)) {
                            $time = !empty($return['time']) ? $return['time'] : 3;
                            $this->error($return['msg'], null, [], $time);
                        }
                    }
                    break;
                // 积分商品列表
                case 'memgift':
                    {
                        if ('sort_order' == $field) {
                            $data['refresh'] = 1;
                            $data['time'] = 500;
                        }
                    }
                    break;

                // 邮箱自定义模板表
                case 'smtp_tpl':
                    {
                        if (in_array($field, ['tpl_title'])) {
                            $value = htmlspecialchars($value);
                        }
                    }
                    break;

                // 站内信自定义模板表
                case 'users_notice_tpl':
                    {
                        if (in_array($field, ['tpl_title'])) {
                            $value = htmlspecialchars($value);
                        }
                    }
                    break;

                // 手机端会员中心底部菜单
                case 'users_bottom_menu':
                    {
                        if (in_array($field, ['title'])) {
                            $value = htmlspecialchars($value);
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
            $where = [
                $id_name => $id_value
            ];
            // 根据条件保存修改的数据
            $r = Db::name($table)->where($where)->cache(true,null,$table)->save($savedata);
            if ($r !== false) {
                if (!empty($ScreeningTable) && !empty($ScreeningField) && 'archives' == $ScreeningTable && 'arcrank' == $ScreeningField) {
                    $Result = model('SqlCacheTable')->ScreeningArchives($ScreeningAid, $ScreeningValue);
                    if (!empty($Result)) {
                        $data['refresh'] = 1;
                        $data['time'] = 500;
                    }
                }elseif ('users' == $table && 'is_activation' == $field){
                    $data['refresh'] = 1;
                    $data['time'] = 500;
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
                            model('Arctype')->hand_type_count(['aid'=>[$id_value]]);//统计栏目文档数量
                            Db::name('taglist')->where('aid', $id_value)->update([
                                'arcrank'=>$value,
                                'update_time'   => getTime(),
                            ]);
                            \think\Cache::clear('taglist');
                            adminLog('文档'.($value >=0 ? '通过审核' : '取消审核').'：'.$id_value);
                            if (isset($value) && -1 === intval($value)) {
                                // 系统快捷下架时，积分商品的被动处理
                                model('ShopPublicHandle')->pointsGoodsPassiveHandle([$id_value]);
                            }
                            // 清空sql_cache_table数据缓存表 并 添加查询执行语句到mysql缓存表
                            Db::execute('TRUNCATE TABLE '.config('database.prefix').'sql_cache_table');
                            model('SqlCacheTable')->InsertSqlCacheTable(true);
                        }
                        break;
                    }
                
                    // 问答插件
                    case 'weapp_ask_users_level':
                    {
                        if ('ask_is_release' == $field) {
                            Db::name('users_level')->where('level_id', $id_value)->update([
                                'ask_is_release'=>$value,
                                'update_time'   => getTime(),
                            ]);
                        } else if ('ask_is_review' == $field) {
                            Db::name('users_level')->where('level_id', $id_value)->update([
                                'ask_is_review'=>$value,
                                'update_time'   => getTime(),
                            ]);
                        }
                        \think\Cache::clear('users_level');
                        break;
                    }
                
                    // 会员字段
                    case 'users_list':
                    case 'users_parameter':
                    {
                        \think\Cache::clear('users_parameter');
                        \think\Cache::clear('users_list');
                        break;
                    }
                
                    // 广告
                    case 'ad':
                    case 'ad_position':
                    {
                        \think\Cache::clear('ad_position');
                        \think\Cache::clear('ad');
                        break;
                    }
                
                    // 多语言
                    case 'language':
                    {
                        if ('status' == $field) {
                            /*统计多语言数量*/
                            model('Language')->setLangNum();
                            break;
                        }
                    }

                    default:
                        // 清除logic逻辑定义的缓存
                        extra_cache('admin_'.$table.'_list_logic', null);
                        break;
                }
                \think\Cache::clear($table);
                delFile(HTML_ROOT.'index');
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
        $Prefix = config('database.prefix');
        if (config('database.type') == 'dm') { // 达梦优化
            $serviceTableInfo = Db::query("SELECT COLUMN_NAME,DATA_TYPE,DATA_DEFAULT,NULLABLE FROM ALL_TAB_COLS WHERE TABLE_NAME = '{$Prefix}quickentry'");
            $serviceTableInfo = get_arr_column($serviceTableInfo, 'COLUMN_NAME');  
            if (!empty($serviceTableInfo) && !in_array('menu_id', $serviceTableInfo)) {
                $sql = "ALTER TABLE `{$Prefix}quickentry` ADD COLUMN `menu_id` INT DEFAULT 0;";                
                if (@Db::execute($sql)) {
                    $sql = "comment ON COLUMN `{$Prefix}quickentry`.`menu_id` IS '左侧菜单ID';";
                    @Db::execute($sql);
                }
            }
            if (!empty($serviceTableInfo) && !in_array('menu_group', $serviceTableInfo)) {
                $sql = "ALTER TABLE `{$Prefix}quickentry` ADD COLUMN `menu_group` SMALLINT DEFAULT 0;";                
                if (@Db::execute($sql)) {
                    $sql = "comment ON COLUMN `{$Prefix}quickentry`.`menu_group` IS '菜单分组，0=欢迎页不归属；1=文档相关；2=高级扩展；3=商城模块；4=SEO模块';";
                    @Db::execute($sql);
                }
            }
            if (!empty($serviceTableInfo) && !in_array('is_lang', $serviceTableInfo)) {
                $sql = "ALTER TABLE `{$Prefix}quickentry` ADD COLUMN `is_lang` TINYINT NULL DEFAULT 0;";                
                if (@Db::execute($sql)) {
                    $sql = "comment ON COLUMN `{$Prefix}quickentry`.`is_lang` IS '是否支持多语言，1=支持，0=不支持';";
                    @Db::execute($sql);
                }
            }
            if (!empty($serviceTableInfo) && !in_array('auth_role', $serviceTableInfo)) {
                $sql = "ALTER TABLE `{$Prefix}quickentry` ADD COLUMN `auth_role` VARCHAR(500) NULL DEFAULT '';";                
                if (@Db::execute($sql)) {
                    $sql = "comment ON COLUMN `{$Prefix}quickentry`.`auth_role` IS '权限';";
                    @Db::execute($sql);
                }
            }
            if (!empty($serviceTableInfo) && !in_array('intro', $serviceTableInfo)) {
                $sql = "ALTER TABLE `{$Prefix}quickentry` ADD COLUMN `intro` VARCHAR(500) NULL DEFAULT '';";                
                if (@Db::execute($sql)) {
                    $sql = "comment ON COLUMN `{$Prefix}quickentry`.`intro` IS '描述';";
                    @Db::execute($sql);
                }
            }
        } elseif (config('database.type') == 'kingbase') { // 人大金仓优化
            $serviceTableInfo = Db::query("SELECT column_name FROM information_schema.columns WHERE table_name = '{$Prefix}quickentry' AND table_schema = 'public'");
            $serviceTableInfo = get_arr_column($serviceTableInfo, 'column_name');
            if (!empty($serviceTableInfo) && !in_array('menu_id', $serviceTableInfo)) {
                $sql = "ALTER TABLE \"{$Prefix}quickentry\" ADD COLUMN \"menu_id\" INT DEFAULT 0";
                @Db::execute($sql);
                $sql = "COMMENT ON COLUMN \"{$Prefix}quickentry\".\"menu_id\" IS '左侧菜单ID'";
                @Db::execute($sql);
            }
            if (!empty($serviceTableInfo) && !in_array('menu_group', $serviceTableInfo)) {
                $sql = "ALTER TABLE \"{$Prefix}quickentry\" ADD COLUMN \"menu_group\" SMALLINT DEFAULT 0";
                @Db::execute($sql);
                $sql = "COMMENT ON COLUMN \"{$Prefix}quickentry\".\"menu_group\" IS '菜单分组，0=欢迎页不归属；1=文档相关；2=高级扩展；3=商城模块；4=SEO模块'";
                @Db::execute($sql);
            }
            if (!empty($serviceTableInfo) && !in_array('is_lang', $serviceTableInfo)) {
                $sql = "ALTER TABLE \"{$Prefix}quickentry\" ADD COLUMN \"is_lang\" SMALLINT DEFAULT 0";
                @Db::execute($sql);
                $sql = "COMMENT ON COLUMN \"{$Prefix}quickentry\".\"is_lang\" IS '是否支持多语言，1=支持，0=不支持'";
                @Db::execute($sql);
            }
            if (!empty($serviceTableInfo) && !in_array('auth_role', $serviceTableInfo)) {
                $sql = "ALTER TABLE \"{$Prefix}quickentry\" ADD COLUMN \"auth_role\" VARCHAR(500) DEFAULT ''";
                @Db::execute($sql);
                $sql = "COMMENT ON COLUMN \"{$Prefix}quickentry\".\"auth_role\" IS '权限'";
                @Db::execute($sql);
            }
            if (!empty($serviceTableInfo) && !in_array('intro', $serviceTableInfo)) {
                $sql = "ALTER TABLE \"{$Prefix}quickentry\" ADD COLUMN \"intro\" VARCHAR(500) DEFAULT ''";
                @Db::execute($sql);
                $sql = "COMMENT ON COLUMN \"{$Prefix}quickentry\".\"intro\" IS '描述'";
                @Db::execute($sql);
            }
        } else {
            $tableInfo = Db::query("SHOW COLUMNS FROM {$Prefix}quickentry");
            $tableInfo = get_arr_column($tableInfo, 'Field');
            // 字段新增处理
            if (!empty($tableInfo) && !in_array('menu_id', $tableInfo)) {
                $sql = "ALTER TABLE `{$Prefix}quickentry` ADD COLUMN `menu_id`  int(10) NULL DEFAULT 0 COMMENT '左侧菜单ID' AFTER `litpic`;";
                @Db::execute($sql);
            }
            if (!empty($tableInfo) && !in_array('menu_group', $tableInfo)) {
                $sql = "ALTER TABLE `{$Prefix}quickentry` ADD COLUMN `menu_group`  smallint(5) NULL DEFAULT 0 COMMENT '菜单分组，0=欢迎页不归属；1=文档相关；2=高级扩展；3=商城模块；4=SEO模块' AFTER `menu_id`;";
                @Db::execute($sql);
            }
            if (!empty($tableInfo) && !in_array('is_lang', $tableInfo)) {
                $sql = "ALTER TABLE `{$Prefix}quickentry` ADD COLUMN `is_lang`  tinyint(1) NULL DEFAULT 0 COMMENT '是否支持多语言，1=支持，0=不支持' AFTER `menu_group`;";
                @Db::execute($sql);
            }
            if (!empty($tableInfo) && !in_array('auth_role', $tableInfo)) {
                $sql = "ALTER TABLE `{$Prefix}quickentry` ADD COLUMN `auth_role`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '权限' AFTER `is_lang`;";
                @Db::execute($sql);
            }
            if (!empty($tableInfo) && !in_array('intro', $tableInfo)) {
                $sql = "ALTER TABLE `{$Prefix}quickentry` ADD COLUMN `intro`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '描述' AFTER `auth_role`;";
                @Db::execute($sql);
            }
        }
        schemaTable("quickentry");

        // 提取数据
        $list = '[{"title":"\u63d0\u8d27\u8bbe\u7f6e","laytext":"\u63d0\u8d27\u8bbe\u7f6e","type":15,"controller":"OrderVerify","action":"drive_list","vars":"","groups":0,"checked":0,"litpic":"\/public\/static\/admin\/images\/theme\/survey_ziti.png","menu_id":0,"menu_group":0,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":13,"add_time":1569232484,"update_time":1746605307},{"title":"\u652f\u4ed8\u8bbe\u7f6e","laytext":"\u652f\u4ed8\u8bbe\u7f6e","type":15,"controller":"System","action":"api_conf","vars":"","groups":0,"checked":0,"litpic":"\/public\/static\/admin\/images\/theme\/survey_zhifu.png","menu_id":0,"menu_group":0,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":7,"add_time":1569232484,"update_time":1746605307},{"title":"\u8fd0\u8d39\u6a21\u677f","laytext":"\u8fd0\u8d39\u6a21\u677f","type":15,"controller":"Shop","action":"shipping_template","vars":"","groups":0,"checked":0,"litpic":"\/public\/static\/admin\/images\/theme\/survey_yunfei.png","menu_id":0,"menu_group":0,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":9,"add_time":1569232484,"update_time":1746605307},{"title":"\u4f18\u60e0\u5238","laytext":"\u4f18\u60e0\u5238","type":15,"controller":"Coupon","action":"index","vars":"","groups":0,"checked":0,"litpic":"\/public\/static\/admin\/images\/theme\/survey_youhuiquan.png","menu_id":0,"menu_group":0,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":10,"add_time":1569232484,"update_time":1746605307},{"title":"\u552e\u540e\u7ef4\u6743","laytext":"\u552e\u540e\u7ef4\u6743","type":15,"controller":"ShopService","action":"after_service","vars":"","groups":0,"checked":1,"litpic":"\/public\/static\/admin\/images\/theme\/survey_weiquan.png","menu_id":0,"menu_group":0,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":4,"add_time":1569232484,"update_time":1746605307},{"title":"\u8bc4\u4ef7\u7ba1\u7406","laytext":"\u8bc4\u4ef7\u7ba1\u7406","type":15,"controller":"ShopComment","action":"comment_index","vars":"","groups":0,"checked":1,"litpic":"\/public\/static\/admin\/images\/theme\/survey_pingjia.png","menu_id":0,"menu_group":0,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":5,"add_time":1569232484,"update_time":1746605307},{"title":"\u4e3b\u9898\u98ce\u683c","laytext":"\u4e3b\u9898\u98ce\u683c","type":15,"controller":"Index","action":"theme_index","vars":"","groups":0,"checked":1,"litpic":"\/public\/static\/admin\/images\/theme\/survey_pifu.png","menu_id":0,"menu_group":0,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":11,"add_time":1569232484,"update_time":1746605307},{"title":"\u4f1a\u5458\u7ba1\u7406","laytext":"\u4f1a\u5458\u7ba1\u7406","type":15,"controller":"Member","action":"users_index","vars":"","groups":0,"checked":1,"litpic":"\/public\/static\/admin\/images\/theme\/survey_huiyuan.png","menu_id":0,"menu_group":0,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":6,"add_time":1569232484,"update_time":1746605307},{"title":"\u524d\u5f80\u53d1\u8d27","laytext":"\u524d\u5f80\u53d1\u8d27","type":15,"controller":"Shop","action":"index","vars":"order_status=1","groups":0,"checked":1,"litpic":"\/public\/static\/admin\/images\/theme\/survey_fahuo.png","menu_id":0,"menu_group":0,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":3,"add_time":1569232484,"update_time":1746605307},{"title":"\u53d1\u5e03\u5546\u54c1","laytext":"\u53d1\u5e03\u5546\u54c1","type":15,"controller":"ShopProduct","action":"add","vars":"firstrun=1","groups":0,"checked":1,"litpic":"\/public\/static\/admin\/images\/theme\/survey_fabu.png","menu_id":0,"menu_group":0,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":1,"add_time":1569232484,"update_time":1746605307},{"title":"\u8ba2\u5355\u67e5\u8be2","laytext":"\u8ba2\u5355\u67e5\u8be2","type":15,"controller":"Order","action":"index","vars":"","groups":0,"checked":1,"litpic":"\/public\/static\/admin\/images\/theme\/survey_dingdan.png","menu_id":0,"menu_group":0,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":2,"add_time":1569232484,"update_time":1746605307},{"title":"\u5145\u503c\u8ba2\u5355","laytext":"\u5145\u503c\u8ba2\u5355","type":15,"controller":"Member","action":"money_index","vars":"status=2","groups":0,"checked":0,"litpic":"\/public\/static\/admin\/images\/theme\/survey_chongzhi.png","menu_id":0,"menu_group":0,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":8,"add_time":1569232484,"update_time":1746605307},{"title":"\u680f\u76ee\u7ba1\u7406","laytext":"\u680f\u76ee\u7ba1\u7406","type":15,"controller":"Arctype","action":"index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-lanmuguanli","menu_id":1001,"menu_group":1,"is_lang":1,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":12,"add_time":1658910010,"update_time":1746605307},{"title":"\u5185\u5bb9\u7ba1\u7406","laytext":"\u5185\u5bb9\u7ba1\u7406","type":15,"controller":"Archives","action":"index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-neirongwendang","menu_id":1002,"menu_group":1,"is_lang":1,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":14,"add_time":1658910020,"update_time":1746605307},{"title":"\u5f85\u5ba1\u6587\u6863","laytext":"\u5f85\u5ba1\u6587\u6863","type":15,"controller":"Archives","action":"index_draft","vars":"","groups":0,"checked":0,"litpic":"iconfont e-tougao","menu_id":1004,"menu_group":1,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":15,"add_time":1658910030,"update_time":1746605307},{"title":"\u9000\u56de\u6587\u6863","laytext":"\u9000\u56de\u6587\u6863","type":15,"controller":"Archives","action":"index_reback","vars":"","groups":0,"checked":0,"litpic":"iconfont e-tougao","menu_id":1006,"menu_group":1,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":0,"sort_order":35,"add_time":1658910035,"update_time":1658917791},{"title":"\u5e7f\u544a\u7ba1\u7406","laytext":"\u5e7f\u544a\u7ba1\u7406","type":15,"controller":"AdPosition","action":"index","vars":"","groups":0,"checked":1,"litpic":"iconfont e-guanggao","menu_id":1003,"menu_group":1,"is_lang":1,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":16,"add_time":1658910040,"update_time":1746605307},{"title":"TAG\u7ba1\u7406","laytext":"TAG\u7ba1\u7406","type":15,"controller":"Tags","action":"index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-TAGguanli","menu_id":2004011,"menu_group":1,"is_lang":1,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":17,"add_time":1658910050,"update_time":1746605307},{"title":"\u641c\u7d22\u7ba1\u7406","laytext":"\u641c\u7d22\u7ba1\u7406","type":15,"controller":"Search","action":"conf","vars":"","groups":0,"checked":0,"litpic":"iconfont e-soguanjianci","menu_id":2004022,"menu_group":1,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":18,"add_time":1658910060,"update_time":1746605307},{"title":"\u680f\u76ee\u5b57\u6bb5","laytext":"\u680f\u76ee\u5b57\u6bb5","type":15,"controller":"Field","action":"arctype_index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-lanmuziduan","menu_id":2004004,"menu_group":1,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":19,"add_time":1658910070,"update_time":1746605307},{"title":"\u9891\u9053\u6a21\u578b","laytext":"\u9891\u9053\u6a21\u578b","type":15,"controller":"Channeltype","action":"index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-pindaomoxing","menu_id":2004007,"menu_group":1,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":20,"add_time":1658910080,"update_time":1746605307},{"title":"\u6587\u6863\u5c5e\u6027","laytext":"\u6587\u6863\u5c5e\u6027","type":15,"controller":"ArchivesFlag","action":"index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-wendangshuxing","menu_id":2004008,"menu_group":1,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":21,"add_time":1658910090,"update_time":1746605307},{"title":"\u56fe\u7247\u6c34\u5370","laytext":"\u56fe\u7247\u6c34\u5370","type":15,"controller":"System","action":"water","vars":"","groups":0,"checked":0,"litpic":"iconfont e-shuiyinpeizhi","menu_id":2004009,"menu_group":1,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":22,"add_time":1658910100,"update_time":1746605307},{"title":"\u7f29\u7565\u56fe\u8bbe\u7f6e","laytext":"\u7f29\u7565\u56fe\u8bbe\u7f6e","type":15,"controller":"System","action":"thumb","vars":"","groups":0,"checked":0,"litpic":"iconfont e-suolvetupeizhi","menu_id":2004010,"menu_group":1,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":23,"add_time":1658910110,"update_time":1746605307},{"title":"\u5bfc\u822a\u7ba1\u7406","laytext":"\u5bfc\u822a\u7ba1\u7406","type":15,"controller":"Navigation","action":"index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-daohangguanli","menu_id":2004013,"menu_group":1,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":24,"add_time":1658910120,"update_time":1746605307},{"title":"\u5b89\u5168\u4e2d\u5fc3","laytext":"\u5b89\u5168\u4e2d\u5fc3","type":15,"controller":"Security","action":"index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-anquan col-f3398cc","menu_id":2004017,"menu_group":2,"is_lang":0,"auth_role":"System@index","intro":"","statistics_type":0,"status":1,"sort_order":25,"add_time":1658910130,"update_time":1746605307},{"title":"\u57fa\u672c\u4fe1\u606f","laytext":"\u57fa\u672c\u4fe1\u606f","type":15,"controller":"System","action":"web","vars":"","groups":0,"checked":0,"litpic":"iconfont e-shezhi","menu_id":2001,"menu_group":2,"is_lang":1,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":26,"add_time":1658910140,"update_time":1746605307},{"title":"\u7ba1\u7406\u5458","laytext":"\u7ba1\u7406\u5458","type":15,"controller":"Admin","action":"index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-guanliyuan","menu_id":2004001,"menu_group":2,"is_lang":0,"auth_role":"Admin@admin_pwd","intro":"","statistics_type":0,"status":1,"sort_order":27,"add_time":1658910150,"update_time":1746605307},{"title":"\u56de\u6536\u7ad9","laytext":"\u56de\u6536\u7ad9","type":15,"controller":"RecycleBin","action":"archives_index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-huishouzhan","menu_id":2004006,"menu_group":2,"is_lang":1,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":28,"add_time":1658910160,"update_time":1746605307},{"title":"\u5907\u4efd\u8fd8\u539f","laytext":"\u5907\u4efd\u8fd8\u539f","type":15,"controller":"Tools","action":"index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-beifenhuanyuan","menu_id":2004002,"menu_group":2,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":29,"add_time":1658910170,"update_time":1746605307},{"title":"\u9a8c\u8bc1\u7801\u7ba1\u7406","laytext":"\u9a8c\u8bc1\u7801\u7ba1\u7406","type":15,"controller":"Vertify","action":"index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-yanzhengmaguanli","menu_id":2004015,"menu_group":2,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":30,"add_time":1658910180,"update_time":1746605307},{"title":"\u6a21\u677f\u7ba1\u7406","laytext":"\u6a21\u677f\u7ba1\u7406","type":15,"controller":"Filemanager","action":"index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-mobanguanli","menu_id":2004003,"menu_group":2,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":31,"add_time":1658910190,"update_time":1746605307},{"title":"\u4f1a\u5458\u4e2d\u5fc3","laytext":"\u4f1a\u5458\u4e2d\u5fc3","type":15,"controller":"Member","action":"users_index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-gerenzhongxin","menu_id":2006,"menu_group":2,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":32,"add_time":1658910200,"update_time":1746605307},{"title":"\u57ce\u5e02\u5206\u7ad9","laytext":"\u57ce\u5e02\u5206\u7ad9","type":15,"controller":"Citysite","action":"index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-chengshifenzhan","menu_id":2004019,"menu_group":2,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":33,"add_time":1658910210,"update_time":1746605160},{"title":"\u63d2\u4ef6\u5e94\u7528","laytext":"\u63d2\u4ef6\u5e94\u7528","type":15,"controller":"Weapp","action":"index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-chajian","menu_id":2005,"menu_group":2,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":33,"add_time":1658910220,"update_time":1746605307},{"title":"\u8ba2\u5355\u7ba1\u7406","laytext":"\u8ba2\u5355\u7ba1\u7406","type":15,"controller":"Order","action":"index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-dingdanguanli","menu_id":2004021,"menu_group":2,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":34,"add_time":1658910230,"update_time":1746605307},{"title":"\u53ef\u89c6\u7f16\u8f91","laytext":"\u53ef\u89c6\u7f16\u8f91","type":15,"controller":"Uiset","action":"ui_index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-keshihuabianji","menu_id":2002,"menu_group":2,"is_lang":1,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":35,"add_time":1658910240,"update_time":1746605307},{"title":"\u79ef\u5206\u5151\u6362","laytext":"\u79ef\u5206\u5151\u6362","type":15,"controller":"Memgift","action":"gift_exchange_list","vars":"","groups":0,"checked":0,"litpic":"iconfont e-lipinduihuan","menu_id":2004023,"menu_group":2,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":36,"add_time":1658910250,"update_time":1746605307},{"title":"\u7559\u8a00\u7ba1\u7406","laytext":"\u7559\u8a00\u7ba1\u7406","type":15,"controller":"Form","action":"index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-biaodanguanli","menu_id":2004018,"menu_group":2,"is_lang":1,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":37,"add_time":1658910260,"update_time":1746605307},{"title":"\u4e3b\u9898\u98ce\u683c","laytext":"\u4e3b\u9898\u98ce\u683c","type":15,"controller":"Index","action":"theme_index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-zhutifengge","menu_id":2004025,"menu_group":2,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":38,"add_time":1658910270,"update_time":1746605307},{"title":"\u5916\u8d38\u52a9\u624b","laytext":"\u5916\u8d38\u52a9\u624b","type":15,"controller":"Foreign","action":"index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-zhutifengge","menu_id":2004026,"menu_group":2,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":39,"add_time":1658910280,"update_time":1746605307},{"title":"\u667a\u80fd\u4e92\u8054","laytext":"\u667a\u80fd\u4e92\u8054","type":15,"controller":"Ai","action":"index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-aizhushou","menu_id":2004027,"menu_group":2,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":40,"add_time":1658910290,"update_time":1746605307},{"title":"\u5546\u57ce\u4e2d\u5fc3","laytext":"\u5546\u57ce\u4e2d\u5fc3","type":15,"controller":"Shop","action":"home","vars":"conceal=1","groups":0,"checked":0,"litpic":"iconfont e-shangcheng","menu_id":2008,"menu_group":3,"is_lang":0,"auth_role":"Shop@index","intro":"","statistics_type":0,"status":1,"sort_order":41,"add_time":1658910300,"update_time":1746605307},{"title":"\u6570\u636e\u7edf\u8ba1","laytext":"\u6570\u636e\u7edf\u8ba1","type":15,"controller":"Statistics","action":"index","vars":"conceal=1","groups":0,"checked":0,"litpic":"iconfont e-shujutongji","menu_id":2008001,"menu_group":3,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":42,"add_time":1658910310,"update_time":1746605307},{"title":"\u5546\u54c1\u7ba1\u7406","laytext":"\u5546\u54c1\u7ba1\u7406","type":15,"controller":"ShopProduct","action":"index","vars":"conceal=1","groups":0,"checked":0,"litpic":"iconfont e-shangpinguanli","menu_id":2008002,"menu_group":3,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":43,"add_time":1658910320,"update_time":1746605307},{"title":"\u5546\u54c1\u53c2\u6570","laytext":"\u5546\u54c1\u53c2\u6570","type":15,"controller":"ShopProduct","action":"attrlist_index","vars":"conceal=1","groups":0,"checked":0,"litpic":"iconfont e-shangpincanshu","menu_id":2008003,"menu_group":3,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":44,"add_time":1658910330,"update_time":1746605307},{"title":"\u5546\u54c1\u89c4\u683c","laytext":"\u5546\u54c1\u89c4\u683c","type":15,"controller":"Shop","action":"spec_index","vars":"conceal=1","groups":0,"checked":0,"litpic":"iconfont e-shangpinguige","menu_id":2008008,"menu_group":3,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":45,"add_time":1658910340,"update_time":1746605307},{"title":"\u5546\u57ce\u914d\u7f6e","laytext":"\u5546\u57ce\u914d\u7f6e","type":15,"controller":"Shop","action":"conf","vars":"conceal=1","groups":0,"checked":0,"litpic":"iconfont e-shangchengpeizhi","menu_id":2008004,"menu_group":3,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":46,"add_time":1658910350,"update_time":1746605307},{"title":"\u8425\u9500\u529f\u80fd","laytext":"\u8425\u9500\u529f\u80fd","type":15,"controller":"Shop","action":"market_index","vars":"conceal=1","groups":0,"checked":0,"litpic":"iconfont e-yingxiaogongneng","menu_id":2008005,"menu_group":3,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":47,"add_time":1658910360,"update_time":1746605307},{"title":"SEO\u8bbe\u7f6e","laytext":"SEO\u8bbe\u7f6e","type":15,"controller":"Seo","action":"seo","vars":"","groups":0,"checked":0,"litpic":"iconfont e-seo","menu_id":2003,"menu_group":4,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":48,"add_time":1658910370,"update_time":1746605307},{"title":"html\u751f\u6210","laytext":"html\u751f\u6210","type":15,"controller":"Seo","action":"build","vars":"","groups":0,"checked":0,"litpic":"iconfont e-jingtaishengcheng","menu_id":2004016,"menu_group":4,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":49,"add_time":1658910380,"update_time":1746605307},{"title":"Sitemap","laytext":"Sitemap","type":15,"controller":"Sitemap","action":"index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-Sitemap","menu_id":2003002,"menu_group":4,"is_lang":0,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":50,"add_time":1658910390,"update_time":1746605307},{"title":"\u53cb\u60c5\u94fe\u63a5","laytext":"\u53cb\u60c5\u94fe\u63a5","type":15,"controller":"Links","action":"index","vars":"","groups":0,"checked":0,"litpic":"iconfont e-youqinglianjie1","menu_id":2003003,"menu_group":4,"is_lang":1,"auth_role":"","intro":"","statistics_type":0,"status":1,"sort_order":51,"add_time":1658910400,"update_time":1746605307},{"title":"\u6d4f\u89c8\u91cf","laytext":"\u603b\u6d4f\u89c8\u91cf","type":25,"controller":"","action":"","vars":"","groups":1,"checked":1,"litpic":"","menu_id":0,"menu_group":0,"is_lang":0,"auth_role":"","intro":"","statistics_type":1,"status":1,"sort_order":2,"add_time":1569232484,"update_time":1745972077},{"title":"\u6587\u7ae0\u6570","laytext":"\u6587\u7ae0\u603b\u6570","type":25,"controller":"Article","action":"index","vars":"","groups":1,"checked":1,"litpic":"","menu_id":0,"menu_group":0,"is_lang":0,"auth_role":"","intro":"","statistics_type":7,"status":1,"sort_order":3,"add_time":1569232484,"update_time":1745972077},{"title":"\u5546\u54c1\u6570","laytext":"\u5546\u54c1\u603b\u6570","type":25,"controller":"ShopProduct","action":"index","vars":"","groups":1,"checked":1,"litpic":"","menu_id":0,"menu_group":0,"is_lang":0,"auth_role":"","intro":"","statistics_type":6,"status":1,"sort_order":3,"add_time":1569232484,"update_time":1745972077}]';
        $list = !empty($list) ? json_decode($list, true) : [];
        if (!empty($list)) {
            $quickentry = Db::name('quickentry')->field('type, controller, action, menu_group')->where(['type' => ['IN', [15, 25]]])->select();
            foreach ($quickentry as $key => $value) {
                if (!empty($value)) $quickentry[$key] = implode('_', $value);
            }
            foreach ($list as $k => $v) {
                $a = implode('_',['type' => $v['type'], 'controller' => $v['controller'], 'action' => $v['action'], 'menu_group' => $v['menu_group']]);
                if (in_array($a, $quickentry)) unset($list[$k]);
            }
            if (!empty($list)) Db::name('quickentry')->insertAll($list);
        }

        /*权限控制 by 小虎哥*/
        $auth_role_info = session('admin_info.auth_role_info');
        if(0 < intval(session('admin_info.role_id')) && ! empty($auth_role_info) && intval($auth_role_info['switch_map']) <= 0){
            $this->error('您没有操作权限，请联系超级管理员分配权限');
        }
        /*--end*/
        
        $msg = '操作成功';
        $seo_pseudo = $this->globalConfig['seo_pseudo'];
        $web_users_tpl_theme = $this->globalConfig['web_users_tpl_theme'];
        empty($web_users_tpl_theme) && $web_users_tpl_theme = 'users';

        if (IS_POST) {
            $inc_type = input('post.inc_type/s');
            $name = input('post.name/s');
            $value = input('post.value/s');
            $is_force = input('post.is_force/d'); // 是否强制开启，跳过检测提示，目前用于（多语言、多站点）
            $langRow = \think\Db::name('language')->order('id asc')->select();

            $data = [];
            switch ($inc_type) {
                case 'pay':
                case 'shop':
                {
                    foreach ($langRow as $key => $val) {
                        getUsersConfigData($inc_type, [$name => $value], $val['mark']);
                    }

                    // 开启商城
                    if (1 == $value) {
                        /*多语言 - 同时开启会员中心*/
                        foreach ($langRow as $key => $val) {
                            tpCache('web', ['web_users_switch' => 1], $val['mark']);
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
                        $shop_tpl_list = glob("./template/".TPL_THEME."pc/{$web_users_tpl_theme}/shop_*");
                        if (!empty($value) && empty($shop_tpl_list)) {
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
                                ])->update([
                                    'status'    => (1 == $value) ? 1 : 0,
                                    'update_time'   => getTime(),
                                ]);
                        }
                    } else if ('pay_open' == $name) {
                        // 同步会员中心的左侧菜单
                        Db::name('users_menu')->where([
                                'mca'   => 'user/Pay/pay_consumer_details',
                            ])->update([
                                'status'    => (1 == $value) ? 1 : 0,
                                'update_time'   => getTime(),
                            ]);
                        //同步会员中心手机端底部菜单开关
                        Db::name('users_bottom_menu')->where([
                                'mca'   => ['IN',['user/Pay/pay_account_recharge']]
                            ])->update([
                                'status'    => (1 == $value) ? 1 : 0,
                                'update_time'   => getTime(),
                            ]);
                    }

                    //同步会员中心手机端底部菜单开关  ---start
                    Db::name('users_bottom_menu')->where([
                            'mca'   => ['IN',['user/Shop/shop_centre','user/Shop/shop_cart_list']]
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
                    if ('users_open_release' == $name) {
                        if (empty($this->php_servicemeal) && !empty($value)) {
                            $str = '6K+l5Yqf6IO95Y+q6ZmQ5LqO5o6I5p2D5Z+f5ZCN77yB';
                            $this->error(base64_decode($str));
                        }
                    }
                    
                    //同步会员中心手机端底部菜单开关  ---start
                    Db::name('users_bottom_menu')->where([
                            'mca'   => ['IN',['user/UsersRelease/article_add','user/UsersRelease/release_centre']]
                        ])->update([
                            'status'    => (1 == $value) ? 1 : 0,
                            'update_time'   => getTime(),
                        ]);
                    //同步会员中心手机端底部菜单开关  ---end
                    // 会员投稿
                    $r = Db::name('users_menu')->where([
                            'mca'  => 'user/UsersRelease/release_centre',
                        ])->update([
                            'status'      => (1 == $value) ? 1 : 0,
                            'update_time' => getTime(),
                        ]);
                    if ($r !== false) {
                        foreach ($langRow as $key => $val) {
                            getUsersConfigData($inc_type, [$name => $value], $val['mark']);
                        }

                        if (1 == $value) {
                            // 多语言 - 同时开启会员中心
                            foreach ($langRow as $key => $val) {
                                tpCache('web', ['web_users_switch' => 1], $val['mark']);
                            }
                            // end
                        }
                    }
                    break;
                }

                case 'level':
                {
                    // 会员升级
                    //同步会员中心手机端底部菜单开关  ---start
                    Db::name('users_bottom_menu')->where([
                            'mca'   => ['IN',['user/Level/level_centre','user/Pay/pay_account_recharge']]
                        ])->update([
                            'status'    => (1 == $value) ? 1 : 0,
                            'update_time'   => getTime(),
                        ]);
                    //同步会员中心手机端底部菜单开关  ---end

                    // 会员升级
                    $r = Db::name('users_menu')->where([
                            'mca'  => 'user/Level/level_centre',
                        ])->update([
                            'status'      => (1 == $value) ? 1 : 0,
                            'update_time' => getTime(),
                        ]);
                    if ($r) {
                        foreach ($langRow as $key => $val) {
                            getUsersConfigData($inc_type, [$name => $value], $val['mark']);
                        }

                        if (1 == $value) {
                            // 多语言 - 同时开启会员中心
                            $langRow = \think\Db::name('language')->order('id asc')->select();
                            foreach ($langRow as $key => $val) {
                                tpCache('web', ['web_users_switch' => 1], $val['mark']);
                            }
                            // end
                        }
                    }
                    break;
                }

                case 'web':
                {
                    if (empty($is_force)) {
                        if ($name == 'web_language_switch' && $value == 1) { // 多语言开关
                            if (!empty($this->globalConfig['web_citysite_open'])) {
                                $this->error('强制开启多语言，会自动关闭城市分站。');
                            }
                        } else if ($name == 'web_citysite_open' && $value == 1) { // 多站点开关
                            if (!empty($this->globalConfig['web_language_switch'])) {
                                $this->error('强制开启城市分站，会自动关闭多语言。');
                            }
                        }
                    }

                    /*多语言*/
                    foreach ($langRow as $key => $val) {
                        tpCache($inc_type, [$name => $value], $val['mark']);
                    }
                    /*--end*/

                    if (in_array($name, ['web_users_switch'])) {
                        // $data['reload'] = 1;
                        /*检测是否存在会员中心模板*/
                        if (!empty($value) && !file_exists('template/'.TPL_THEME.'pc/'.$web_users_tpl_theme)) {
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
                        // 强制关闭多站点
                        if (!empty($is_force)) {
                            $data['reload'] = 1;
                            foreach ($langRow as $key => $val) {
                                tpCache('web', ['web_citysite_open' => 0], $val['mark']);
                            }
                            model('Citysite')->setCitysiteOpen();
                        }
                        // 清除页面缓存
                        delFile(HTML_ROOT);
                    } else if ($name == 'web_citysite_open') { // 多城市站点开关
                        model('Citysite')->setCitysiteOpen();
                        // 强制关闭多语言
                        if (!empty($is_force)) {
                            $data['reload'] = 1;
                            $msg = "已开启城市分站<br/>1、仅支持动态URL、伪静态这两种模式；<br/>2、可在下方的【高级扩展】进入城市分站；";
                        }
                        foreach ($langRow as $key => $val) {
                            tpCache('web', ['web_language_switch' => 0], $val['mark']);
                            if (!empty($value) && 2 == $seo_pseudo) {
                                tpCache('seo', ['seo_pseudo'=>1, 'seo_dynamic_format'=>1], $val['mark']);
                                if (file_exists('./index.html')) {
                                    @unlink('./index.html');
                                }
                            }
                        }
                        // 统计多语言数量
                        model('Language')->setLangNum();
                        // 重新生成sitemap.xml
                        sitemap_all();
                        // 清除页面缓存
                        delFile(HTML_ROOT);
                    }
                    break;
                }
            }

            $this->success($msg, null, $data);
        }

        $menuGroupList = model('Quickentry')->getMenuGroupList(['order'=>'menu_group asc, add_time asc, id asc']);
        $menuGroupList = group_same_key($menuGroupList, 'menu_group');
        $this->assign('menuGroupList', $menuGroupList);

        /*代理贴牌功能限制-s*/
        $weapp_switch = true;
        if (function_exists('checkAuthRule')) {
            //插件应用
            $weapp_switch = checkAuthRule(2005);
        }
        $this->assign('weapp_switch', $weapp_switch);
        /*代理贴牌功能限制-e*/

        $is_online = 0;
        if (is_realdomain()) {
            $is_online = 1;
        }
        $this->assign('is_online',$is_online);

        /*检测是否存在会员中心模板*/
        if (!file_exists('template/'.TPL_THEME.'pc/'.$web_users_tpl_theme)) {
            $is_themeusers_exist = 1;
        } else {
            $is_themeusers_exist = 0;
        }
        $this->assign('is_themeusers_exist',$is_themeusers_exist);
        /*--end*/

        /*检测是否存在商城中心模板*/
        $shop_tpl_list = glob("./template/".TPL_THEME."pc/{$web_users_tpl_theme}/shop_*");
        if (empty($shop_tpl_list)) {
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

        //获取所有权限列表（id为键值的list格式）
        $all_menu_tree = getAllMenu();
        $all_menu_list = tree_to_list($all_menu_tree,'child','id');
        $this->assign('all_menu_list',$all_menu_list);

        //选中的且需要展示在“当前导航”菜单栏目
        $admin_menu_list = Db::name("admin_menu")->field("menu_id,controller_name,action_name,title,icon,is_menu,is_switch")->where(['is_menu'=>1,'status'=>1])->order("sort_order asc,update_time asc")->select();
        $admin_menu_id_arr = [];  //在“当前导航”栏目显示菜单集合
        foreach ($admin_menu_list as $key=>$val){
            $admin_menu_id_arr[] = $val['menu_id'];
        }
        //用户手动关闭的权限集合
        $this->assign('admin_menu_id_arr', $admin_menu_id_arr);
        $menu_list = getAdminMenuList($admin_menu_list);
        $this->assign('menu_list',$menu_list);
        //获取因为没有开启相关模块没有权限的节点（用于初始化）
        $not_role_menu_id_arr = get_not_role_menu_id();
        $this->assign('not_role_menu_id_arr',$not_role_menu_id_arr);

        //模块开关与入口关联(用于动态js)
        $global = include APP_PATH.MODULE_NAME.'/conf/global.php';
        $this->assign('module_rele_menu',$global['module_rele_menu']);
        $this->assign('module_default_menu',$global['module_default_menu']);
        $this->assign('module_reverse_menu',$global['module_reverse_menu']);

        //创始人才有权限控制
        $admin_info = session('admin_info');
        $is_founder = !empty($admin_info['is_founder']) ? $admin_info['is_founder'] : 0;
        $this->assign('is_founder', $is_founder);

        $security_ask_open = (int)tpSetting('security.security_ask_open');
        $this->assign('security_ask_open', $security_ask_open);

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
                Db::execute('TRUNCATE TABLE '.config('database.prefix').'sql_cache_table');
                model('SqlCacheTable')->InsertSqlCacheTable(true);
                /* END */
            }
        }
    }

    /**
     * 主题风格
     * @return [type] [description]
     */
    public function theme_index()
    {
        // 主题风格
        // $list = Db::name('admin_theme')->where(['theme_type'=>1])->order('is_system desc, sort_order asc, theme_id asc')->select();
        // $this->assign('list', $list);

        // 登录页自定义模板
        $login_tplist = glob('application/admin/template/theme/login_*.htm');
        foreach ($login_tplist as $key => $val) {
            $val = preg_replace('/^(.*)login_([\w\-]+)\.htm$/i', 'login_${2}.htm', $val);
            $login_tplist[$key] = $val;
        }
        $this->assign('login_tplist', $login_tplist);

        // 欢迎页主题风格
        $where = [];
        $where['theme_type'] = 2;
        if (5 > $this->php_servicemeal) {
            $where['welcome_tplname'] = ['neq', 'welcome_ai.htm'];
        }
        $welcome_list = Db::name('admin_theme')->where($where)->order('is_system desc, sort_order asc, theme_id asc')->select();
        foreach ($welcome_list as $key => $val) {
            $val['disabled'] = $val['disabled_tips'] = '';
            if ($val['welcome_tplname'] == 'welcome_shop.htm') {
                if (empty($this->usersConfig['shop_open'])) {
                    $val['disabled'] = ' disabled="disabled" readonly="true" ';
                    $val['disabled_tips'] = ' title="需开启商城中心才能使用" ';
                }
            } else if ($val['welcome_tplname'] == 'welcome_taskflow.htm') {
                $weappRow = model('weapp')->getWeappList('TaskFlow');
                if (!is_dir('./weapp/TaskFlow/') || empty($weappRow['status'])) {
                    $val['disabled'] = ' disabled="disabled" readonly="true" ';
                    $val['disabled_tips'] = ' title="需安装【工作任务流】插件才能使用" ';
                }
            }
            $welcome_list[$key] = $val;
        }
        $this->assign('welcome_list', $welcome_list);

        return $this->fetch();
    }

    /**
     * 主题设置 - 保存
     * @return [type] [description]
     */
    public function theme_conf()
    {
        if (IS_POST) {
            $post = input('post.');
            $webData = [];
            $image_ext = config('global.image_ext');
            $image_ext_arr = explode(',', $image_ext);
            foreach ($post as $key => $val) {
                $org_key = $key;
                $val = trim($val);
                if (in_array($key, ['admin_logo','login_logo','login_bgimg','web_ico'])) { // 后台LOGO/登录LOGO
                    $source = preg_replace('#^'.$this->root_dir.'#i', '', $val); // 支持子目录
                    $source_ext = pathinfo('.'.$source, PATHINFO_EXTENSION);
                    if (!empty($source_ext) && !in_array($source_ext, $image_ext_arr)) {
                        $this->error('上传图片扩展名错误！');
                    }
                }
                if ('theme_id' == $key) {
                    $key = 'web_theme_styleid';
                } else if ('login_logo' == $key) {
                    $key = 'web_loginlogo';
                } else if ('login_bgimg_model' == $key) {
                    $key = 'web_loginbgimg_model';
                } else if ('login_bgimg' == $key) {
                    $key = 'web_loginbgimg';
                } else if ('theme_color_model' == $key) {
                    $key = 'web_theme_color_model';
                } else if ('theme_main_color' == $key) {
                    $key = 'web_theme_color';
                } else if ('theme_assist_color' == $key) {
                    $key = 'web_assist_color';
                } else if ('admin_logo' == $key) {
                    $key = 'web_adminlogo';
                } else if ('login_tplname' == $key) { // 登录页设置
                    $key = 'web_theme_login_tplname';
                } else if ('welcome_tplname' == $key) { // 欢迎页设置
                    $key = 'web_theme_welcome_tplname';
                }
                $webData[$key] = $val;
                if ($org_key != $key) {
                    unset($webData[$org_key]);
                }
            }
            $webData['web_theme_style_uptime'] = getTime();
            /*多语言*/
            $langRow = \think\Db::name('language')->order('id asc')->select();
            foreach ($langRow as $key => $val) {
                tpCache('web', $webData, $val['mark']);
            }
            /*--end*/
            $ajaxLogic = new \app\admin\logic\AjaxLogic;
            $ajaxLogic->admin_update_theme_css();

            $is_change = 0;
            // $theme_info = Db::name('admin_theme')->field('theme_title,theme_pic,add_time,update_time', true)->where(['theme_id'=>$post['theme_id']])->find();
            // foreach ($post as $key => $val) {
            //     if (in_array($key, ['login_logo','login_bgimg','admin_logo'])) {
            //         $val = handle_subdir_pic($val);
            //         $theme_info[$key] = handle_subdir_pic($theme_info[$key]);
            //     }
            //     if (isset($theme_info[$key]) && $theme_info[$key] != $val) {
            //         $is_change = 1;
            //         break;
            //     }
            // }
            // if (empty($post['is_select_theme'])) {
            //     $is_change = 0;
            // }

            $this->success('操作成功，需刷新后台看效果！', null, ['is_change'=>$is_change]);
        }
    }

    /**
     * 欢迎页设置 - 保存
     * @return [type] [description]
     */
    public function theme_welcome_conf()
    {
        if (IS_POST) {
            $post = input('post.');
            $webData = ['web_theme_welcome_tplname'=>$post['welcome_tplname']];
            /*多语言*/
            $langRow = \think\Db::name('language')->order('id asc')->select();
            foreach ($langRow as $key => $val) {
                tpCache('web', $webData, $val['mark']);
            }
            /*--end*/
            $this->success('操作成功');
        }
    }

    /**
     * 新增主题风格
     * @return [type] [description]
     */
    public function theme_add_login()
    {
        $this->error('出于安全考虑已禁用，请使用ftp或易优助手插件修改模板');
        if (IS_POST) {
            $post = input('post.');
            $post['theme_title'] = trim($post['theme_title']);
            if (empty($post['theme_title'])) {
                $this->error('主题名称不能为空！');
            }
            if (isset($post['theme_id'])) {
                unset($post['theme_id']);
            }
            $newData = [
                'theme_type'=>1,
                'is_system' => 0,
                'sort_order' => 100,
                'add_time' => getTime(),
                'update_time' => getTime(),
            ];
            $newData = array_merge($post, $newData);
            $theme_id = Db::name('admin_theme')->insertGetId($newData);
            if ($theme_id !== false) {
                /*多语言*/
                $langRow = \think\Db::name('language')->order('id asc')->select();
                foreach ($langRow as $key => $val) {
                    tpCache('web', ['web_theme_styleid'=>$theme_id], $val['mark']);
                }
                /*--end*/
                $this->success('操作成功，需刷新后台看效果！');
            }
            $this->error('操作失败');
        }
    }

    /**
     * 获取主题风格信息
     * @return [type] [description]
     */
    public function ajax_get_theme_info()
    {
        $theme_id = input('param.theme_id/d');
        $info = Db::name('admin_theme')->where(['theme_id'=>$theme_id])->find();
        $this->success('读取成功', null, ['info'=>$info]);
    }

    /**
     * 生成随机欢迎页文件名，确保唯一性
     */
    private function theme_rand_filename($filename = '', $prefix = 'style', $filename_list = [])
    {
        if (empty($filename)) {
            $filename = $prefix . mt_rand(100,999);
        }
        if (in_array($filename, $filename_list)) {
            $filename = $prefix . mt_rand(100,999);
            return $this->theme_rand_filename($filename, $prefix, $filename_list);
        }

        return $filename;
    }

    //ajax获取任务流数据
    public function get_task_list()
    {
        $this->eyouCmsLogic->get_task_list();
    }

    /**
     * 创建指定模板文件
     * @return [type] [description]
     */
    public function ajax_theme_tplfile_add()
    {
        $this->error('出于安全考虑已禁用，请使用ftp或易优助手插件修改模板');
    }

    /**
     * 编辑指定模板文件
     * @return [type] [description]
     */
    public function ajax_theme_tplfile_edit()
    {
        $this->error('出于安全考虑已禁用，请使用ftp或易优助手插件修改模板');
    }

    /**
     * 删除指定模板文件
     */
    public function ajax_theme_tplfile_del()
    {
        $theme_id = input('param.theme_id/d');
        if (IS_POST && !empty($theme_id)) {
            $type = input('param.type/s', '');
            $select_input_id = '';
            if ('welcome' == $type) {
                $select_input_id = 'welcome_tplname';
            } else if ('login' == $type) {
                $select_input_id = 'login_tplname';
            }
            $tpldirpath = '/application/admin/template/theme';
            $info = Db::name('admin_theme')->where(['theme_id'=>$theme_id])->find();
            $r = Db::name('admin_theme')->where(['theme_id'=>$theme_id])->delete();
            if ($r !== false) {
                @unlink('.'.$tpldirpath.'/'.$info['welcome_tplname']);
                adminLog('删除欢迎页模板：'.$info['theme_title']);
                $this->success('删除成功', null, ['select_input_id'=>$select_input_id]);
            }
        }
        $this->error('删除失败');
    }
}
