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

namespace app\admin\logic;

use think\Config;
use think\Model;
use think\Db;

/**
 * 逻辑定义
 * Class CatsLogic
 * @package admin\Logic
 */
class AjaxLogic extends Model
{
    private $request = null;
    private $admin_lang = 'cn';
    private $main_lang = 'cn';

    /**
     * 析构函数
     */
    function  __construct() {
        $this->request = request();
        $this->admin_lang = get_admin_lang();
        $this->main_lang = get_main_lang();
    }

    /**
     * 进入登录页面需要异步处理的业务
     */
    public function login_handle()
    {
        $this->saveBaseFile(); // 存储后台入口文件路径，比如：/login.php
        clear_session_file(); // 清理过期的data/session文件
    }

    /**
     * 清理未存在的左侧菜单
     * @return [type] [description]
     */
    public function admin_menu_clear()
    {
        $del_ids = [];
        $codeArr = Db::name('weapp')->column('code');
        $list = Db::name('admin_menu')->where(['controller_name'=>'Weapp','action_name'=>'execute'])->select();
        foreach ($list as $key => $val) {
            $code = preg_replace('/^(.*)\|sm\|([^\|]+)\|sc\|(.*)$/i', '${2}', $val['param']);
            if (!in_array($code, $codeArr)) {
                $del_ids[] = $val['id'];
            }
        }
        if (!empty($del_ids)) {
            Db::name('admin_menu')->where(['id'=>['IN', $del_ids]])->delete();
        }
    }

    /**
     * 进入欢迎页面需要异步处理的业务
     */
    public function welcome_handle()
    {
        getVersion('version_themeusers', 'v1.0.1', true);
        getVersion('version_themeshop', 'v1.0.1', true);
        $this->addChannelFile(); // 自动补充自定义模型的文件
        $this->saveBaseFile(); // 存储后台入口文件路径，比如：/login.php
        $this->renameInstall(); // 重命名安装目录，提高网站安全性
        $this->renameSqldatapath(); // 重命名数据库备份目录，提高网站安全性
        $this->del_adminlog(); // 只保留最近一个月的操作日志
        model('Member')->batch_update_userslevel(); // 批量更新会员过期等级
        // tpversion(); // 统计装载量，请勿删除，谢谢支持！
    }
    
    /**
     * 自动补充自定义模型的文件
     */
    public function addChannelFile()
    {
        try {
            $list = Db::name('channeltype')->where([
                'ifsystem'  => 0,
                ])->select();
            if (!empty($list)) {
                $cmodSrc = "data/model/application/common/model/CustomModel.php";
                $cmodContent = @file_get_contents($cmodSrc);
                $hctlSrc = "data/model/application/home/controller/CustomModel.php";
                $hctlContent = @file_get_contents($hctlSrc);
                $hmodSrc = "data/model/application/home/model/CustomModel.php";
                $hmodContent = @file_get_contents($hmodSrc);
                foreach ($list as $key => $val) {
                    // 验证ctl_name只包含合法的类名字符，防止路径遍历攻击
                    if (!preg_match('/^[a-zA-Z_][\w\-]*$/', $val['ctl_name'])) {
                        continue;
                    }
                    $file = "application/common/model/{$val['ctl_name']}.php";
                    if (!file_exists($file)) {
                        $cmodContent = str_replace('CustomModel', $val['ctl_name'], $cmodContent);
                        $cmodContent = str_replace('custommodel', strtolower($val['nid']), $cmodContent);
                        $cmodContent = str_replace('CUSTOMMODEL', strtoupper($val['nid']), $cmodContent);
                        @file_put_contents($file, $cmodContent);
                    }
                    $file = "application/home/controller/{$val['ctl_name']}.php";
                    if (!file_exists($file)) {
                        $hctlContent = str_replace('CustomModel', $val['ctl_name'], $hctlContent);
                        $hctlContent = str_replace('custommodel', strtolower($val['nid']), $hctlContent);
                        $hctlContent = str_replace('CUSTOMMODEL', strtoupper($val['nid']), $hctlContent);
                        @file_put_contents($file, $hctlContent);
                    }
                    $file = "application/home/model/{$val['ctl_name']}.php";
                    if (!file_exists($file)) {
                        $hmodContent = str_replace('CustomModel', $val['ctl_name'], $hmodContent);
                        $hmodContent = str_replace('custommodel', strtolower($val['nid']), $hmodContent);
                        $hmodContent = str_replace('CUSTOMMODEL', strtoupper($val['nid']), $hmodContent);
                        @file_put_contents($file, $hmodContent);
                    }
                }
            }
        } catch (\Exception $e) {}
    }
    
    /**
     * 只保留最近一个月的操作日志
     */
    public function del_adminlog()
    {
        try {
            $is_system = true;
            if (file_exists(ROOT_PATH.'weapp/Equal/logic/EqualLogic.php')) {
                $equalLogic = new \weapp\Equal\logic\EqualLogic;
                if (method_exists($equalLogic, 'del_adminlog')) {
                    $is_system = false;
                    $equalLogic->del_adminlog();
                }
            }
            else if (file_exists(ROOT_PATH.'weapp/Systemdoctor/logic/SystemdoctorLogic.php')) {
                $systemdoctorLogic = new \weapp\Systemdoctor\logic\SystemdoctorLogic;
                if (method_exists($systemdoctorLogic, 'del_adminlog')) {
                    $is_system = false;
                    $systemdoctorLogic->del_adminlog();
                }
            }
            if ($is_system) {
                // 只保留一个月的最新日志
                $mtime = strtotime("-1 month");
                Db::name('admin_log')->where([
                        'log_time'  => ['lt', $mtime],
                    ])->delete(true);
                
                // 只保留admin_id < 0 的最新10000条日志
                $log_ids = Db::name('admin_log')->where([
                        'admin_id'  => ['lt', 0],
                    ])->order('log_id desc')->limit(10000,1)->column('log_id');
                if (!empty($log_ids[0])) {
                    Db::name('admin_log')->where([
                        'admin_id'  => ['lt', 0],
                        'log_id' => ['elt', $log_ids[0]],
                    ])->delete(true);
                }
            }
        } catch (\Exception $e) {}
    }

    /*
     * 修改备份数据库目录
     */
    private function renameSqldatapath() {
        $default_sqldatapath = config('DATA_BACKUP_PATH');
        if (is_dir('.'.$default_sqldatapath)) { // 还是符合初始默认的规则的链接方式
            $dirname = get_rand_str(20, 0, 1);
            $new_path = '/data/sqldata_'.$dirname;
            if (@rename(ROOT_PATH.ltrim($default_sqldatapath, '/'), ROOT_PATH.ltrim($new_path, '/'))) {
                /*多语言*/
                if (is_language()) {
                    $langRow = \think\Db::name('language')->order('id asc')->select();
                    foreach ($langRow as $key => $val) {
                        tpCache('web', ['web_sqldatapath'=>$new_path], $val['mark']);
                    }
                } else { // 单语言
                    tpCache('web', ['web_sqldatapath'=>$new_path]);
                }
                /*--end*/
            }
        }
    }

    /**
     * 重命名安装目录，提高网站安全性
     * 在 Admin@login 和 Index@index 操作下
     */
    private function renameInstall()
    {
        if (stristr($this->request->host(), 'eycms.hk')) {
            return true;
        }
        $install_path = ROOT_PATH.'install';
        if (is_dir($install_path) && file_exists($install_path)) {
            $install_time = get_rand_str(20, 0, 1);
            $new_path = ROOT_PATH.'install_'.$install_time;
            @rename($install_path, $new_path);
        }
        else {
            $dirlist = glob('install_*');
            $install_dirname = current($dirlist);
            if (!empty($install_dirname)) {
                /*---修补v1.1.6版本删除的安装文件 install.lock start----*/
                if (!empty($_SESSION['isset_install_lock'])) {
                    return true;
                }
                $_SESSION['isset_install_lock'] = 1;
                /*---修补v1.1.6版本删除的安装文件 install.lock end----*/

                $install_path = ROOT_PATH.$install_dirname;
                if (preg_match('/^install_[0-9]{10}$/i', $install_dirname)) {
                    $install_time = get_rand_str(20, 0, 1);
                    $install_dirname = 'install_'.$install_time;
                    $new_path = ROOT_PATH.$install_dirname;
                    if (@rename($install_path, $new_path)) {
                        $install_path = $new_path;
                        /*多语言*/
                        if (is_language()) {
                            $langRow = \think\Db::name('language')->order('id asc')->select();
                            foreach ($langRow as $key => $val) {
                                tpSetting('install', ['install_dirname'=>$install_time], $val['mark']);
                            }
                        } else { // 单语言
                            tpSetting('install', ['install_dirname'=>$install_time]);
                        }
                        /*--end*/
                    }
                }

                $filename = $install_path.DS.'install.lock';
                if (!file_exists($filename)) {
                    @file_put_contents($filename, '');
                }
            }
        }
    }

    /**
     * 存储后台入口文件路径，比如：/login.php
     * 在 Admin@login 和 Index@index 操作下
     */
    private function saveBaseFile()
    {
        $data = [];
        $data['web_adminbasefile'] = $this->request->baseFile();
        $data['web_cmspath'] = ROOT_DIR; // EyouCMS安装目录
        /*多语言*/
        if (is_language()) {
            $langRow = \think\Db::name('language')->field('mark')->order('id asc')->select();
            foreach ($langRow as $key => $val) {
                tpCache('web', $data, $val['mark']);
            }
        } else { // 单语言
            tpCache('web', $data);
        }
        /*--end*/
    }

    /**
     * 升级前台会员中心的模板文件
     */
    public function update_template($type = '')
    {
        if (!empty($type)) {
            if ('users' == $type) {
                if (file_exists(ROOT_PATH.'template/'.TPL_THEME.'pc/users') || file_exists(ROOT_PATH.'template/'.TPL_THEME.'mobile/users')) {
                    $upgrade = getDirFile(DATA_PATH.'backup'.DS.'tpl');
                    if (!empty($upgrade) && is_array($upgrade)) {
                        delFile(DATA_PATH.'backup'.DS.'template_www');
                        // 升级之前，备份涉及的源文件
                        foreach ($upgrade as $key => $val) {
                            $val_tmp = str_replace("template/", "template/".TPL_THEME, $val);
                            $source_file = ROOT_PATH.$val_tmp;
                            if (file_exists($source_file)) {
                                $destination_file = DATA_PATH.'backup'.DS.'template_www'.DS.$val_tmp;
                                tp_mkdir(dirname($destination_file));
                                @copy($source_file, $destination_file);
                            }
                        }

                        // 递归复制文件夹
                        $this->recurse_copy(DATA_PATH.'backup'.DS.'tpl', rtrim(ROOT_PATH, DS));
                    }
                    /*--end*/
                }
            }
        }
    }

    /**
     * 自定义函数递归的复制带有多级子目录的目录
     * 递归复制文件夹
     *
     * @param string $src 原目录
     * @param string $dst 复制到的目录
     * @return string
     */                        
    //参数说明：            
    //自定义函数递归的复制带有多级子目录的目录
    private function recurse_copy($src, $dst)
    {
        $planPath_pc = "template/".TPL_THEME."pc/";
        $planPath_m = "template/".TPL_THEME."mobile/";
        $dir = opendir($src);

        /*pc和mobile目录存在的情况下，才拷贝会员模板到相应的pc或mobile里*/
        $dst_tmp = str_replace('\\', '/', $dst);
        $dst_tmp = rtrim($dst_tmp, '/').'/';
        if (stristr($dst_tmp, $planPath_pc) && file_exists($planPath_pc)) {
            tp_mkdir($dst);
        } else if (stristr($dst_tmp, $planPath_m) && file_exists($planPath_m)) {
            tp_mkdir($dst);
        }
        /*--end*/

        while (false !== $file = readdir($dir)) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $needle = '/template/'.TPL_THEME;
                    $needle = rtrim($needle, '/');
                    $dstfile = $dst . '/' . $file;
                    if (!stristr($dstfile, $needle)) {
                        $dstfile = str_replace('/template', $needle, $dstfile);
                    }
                    $this->recurse_copy($src . '/' . $file, $dstfile);
                }
                else {
                    if (file_exists($src . DIRECTORY_SEPARATOR . $file)) {
                        /*pc和mobile目录存在的情况下，才拷贝会员模板到相应的pc或mobile里*/
                        $rs = true;
                        $src_tmp = str_replace('\\', '/', $src . DIRECTORY_SEPARATOR . $file);
                        if (stristr($src_tmp, $planPath_pc) && !file_exists($planPath_pc)) {
                            continue;
                        } else if (stristr($src_tmp, $planPath_m) && !file_exists($planPath_m)) {
                            continue;
                        }
                        /*--end*/
                        $rs = @copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                        if($rs) {
                            @unlink($src . DIRECTORY_SEPARATOR . $file);
                        }
                    }
                }
            }
        }
        closedir($dir);
    }
    
    // 记录当前是多语言还是单语言到文件里
    public function system_langnum_file()
    {
        model('Language')->setLangNum();
    }
    
    // 记录当前是否多站点到文件里
    public function system_citysite_file()
    {
        $key = base64_decode('cGhwLnBocF9zZXJ2aWNlbWVhbA==');
        $value = tpCache($key);
        if (2 > $value) {
            /*多语言*/
            if (is_language()) {
                $langRow = Db::name('language')->order('id asc')->select();
                foreach ($langRow as $key => $val) {
                    tpCache('web', ['web_citysite_open'=>0], $val['mark']);
                }
            } else { // 单语言
                tpCache('web', ['web_citysite_open'=>0]);
            }
            /*--end*/
            model('Citysite')->setCitysiteOpen();
        }
    }

    public function admin_logic_1609900642()
    {
        // 更新自定义的样式表文件
        $version = getVersion();
        $syn_admin_logic_1697156935 = tpSetting('syn.admin_logic_1697156935', [], 'cn');
        if ($version != $syn_admin_logic_1697156935) {
            $r = $this->admin_update_theme_css();
            if ($r !== false) {
                tpSetting('syn', ['admin_logic_1697156935'=>$version], 'cn');
            }
        }

        $vars1 = 'cGhwLnBo'.'cF9zZXJ2aW'.'NlaW5mbw==';
        $vars1 = base64_decode($vars1);
        $data = tpCache($vars1);
        $data = mchStrCode($data, 'DECODE');
        $data = json_decode($data, true);
        if (empty($data['pid']) || 2 > $data['pid']) return true;
        $file = "./data/conf/{$data['code']}.txt";
        $vars2 = 'cGhwX3Nl'.'cnZpY2V'.'tZWFs';
        $vars2 = base64_decode($vars2);
        if (!file_exists($file)) {
            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')->select();
                foreach ($langRow as $key => $val) {
                    tpCache('php', [$vars2=>1], $val['mark']);
                }
            } else { // 单语言
                tpCache('php', [$vars2=>1]);
            }
            /*--end*/
        } else {
            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')->select();
                foreach ($langRow as $key => $val) {
                    tpCache('php', [$vars2=>$data['pid']], $val['mark']);
                }
            } else { // 单语言
                tpCache('php', [$vars2=>$data['pid']]);
            }
            /*--end*/
        }
    }

    /**
     * 更新后台自定义的样式表文件
     * @return [type] [description]
     */
    public function admin_update_theme_css()
    {
        $r = false;
        $file = APP_PATH.'admin/template/public/theme_css.htm';
        if (file_exists($file)) {
            $view = \think\View::instance(\think\Config::get('template'), \think\Config::get('view_replace_str'));
            $view->assign('global', tpCache('global'));
            $css = $view->fetch($file);
            $css = str_replace(['<style type="text/css">','</style>'], '', $css);
            if (function_exists('chmod')) {
                @chmod($file, 0755);
            }
            $r = @file_put_contents(ROOT_PATH.'public/static/admin/css/theme_style.css', $css);
        }

        return $r;
    }

    /**
     * 更新会员中心自定义的样式表文件
     * @return [type] [description]
     */
    public function users_update_theme_css()
    {
        
    }

    public function admin_logic_1623036205()
    {
        $arr = [
            ROOT_PATH."data/conf/eyoufilelist.txt",
            ROOT_PATH."weapp/P"."oj"."ie/",
            ROOT_PATH."data/conf/quickentry_data.txt",
            ROOT_PATH."weapp/po"."ji"."e/",
            ROOT_PATH."vendor/phpmailer/get_oauth_token.php",
        ];

        $vars1 = 'cGhwLnBo'.'cF9zZXJ2aW'.'NlaW5mbw==';
        $vars1 = base64_decode($vars1);
        $data = tpCache($vars1);
        $data = mchStrCode($data, 'DECODE');
        $data = json_decode($data, true);
        if (!empty($data['pid']) && 5 > $data['pid']) {
            // $arr[] = ROOT_PATH."ai-content-index.html";
            // $arr[] = ROOT_PATH."llms.txt";
        }
        
        foreach ($arr as $key => $val) {
            if (is_dir($val)) {
                try {
                    delFile($val, true);
                } catch (\Exception $e) {}
            } else if (file_exists($val)) {
                @unlink($val);
            }
        }

        // 通用清理：递归删除各 vendor 目录下会暴露依赖信息的元文件
        // （installed.json / composer.json / composer.lock 运行时不加载，删除不影响自动加载；
        //   不删 installed.php，Composer2 的 InstalledVersions.php 运行时会依赖它）
        // 覆盖主 vendor + weapp/*/vendor + data/weapp/*/vendor，后续新装插件触发本方法时会自动清理
        // 仅线上执行：本地开发环境保留这些文件
        try {
            $__host = request()->host();
            $__is_local = is_local_ip($__host)
                || preg_match('/^(10|127)\./', $__host)
                || preg_match('/^172\.(1[6-9]|2\d|3[01])\./', $__host)
                || preg_match('/\.(test|local|localhost|dev)$/i', $__host);
            if (!$__is_local) {
                $vendorDirs = [ROOT_PATH."vendor"];
                foreach ([ROOT_PATH."weapp/*/vendor", ROOT_PATH."data/weapp/*/vendor"] as $pattern) {
                    $matched = glob($pattern, GLOB_ONLYDIR);
                    if (is_array($matched)) {
                        $vendorDirs = array_merge($vendorDirs, $matched);
                    }
                }
                $sensitiveNames = ['installed.json', 'composer.json', 'composer.lock'];
                foreach ($vendorDirs as $vendorDir) {
                    if (!is_dir($vendorDir)) continue;
                    try {
                        $iterator = new \RecursiveIteratorIterator(
                            new \RecursiveDirectoryIterator($vendorDir, \FilesystemIterator::SKIP_DOTS),
                            \RecursiveIteratorIterator::LEAVES_ONLY
                        );
                        foreach ($iterator as $fileInfo) {
                            if ($fileInfo->isFile() && in_array($fileInfo->getFilename(), $sensitiveNames, true)) {
                                @unlink($fileInfo->getPathname());
                            }
                        }
                    } catch (\Exception $e) {}
                }
            }
        } catch (\Exception $e) {}
        
        // 更新外贸助手的JS多语言变量
        model('ForeignPack')->updateLangFile();
        // 同步模板的付费选择支付文件到前台模板指定位置
        $this->copy_tplpayfile();
        $system_proving_tips = tpSetting('system.system_proving_tips', '', 'cn');
        if (empty($system_proving_tips)) {
            $system_proving_tips = '5qOA5rWL5Yiw5pyJ5L2'.
                '/55So57O757uf56C06Kej5o+S5L'.
                'u255qE5LiN5a6J5YWo6KGM5Li67'.
                '7yM6K+36Ieq6KGM5L+u5aSN44C'.
                'C5aaC5pyJ6K+v5oql6K+35Y'.
                '+K5pe25ZCR5piT5LyY5a6Y5pa'.
                '55byA5Y+R6ICF5Y+N6aaI';
            tpSetting('system', ['system_proving_tips'=>$system_proving_tips], 'cn');
        }
        // 自动更新插件里的jquery文件为最新版本，修复jquery漏洞
        $this->copy_jquery();
        // 升级后，清理缓存文件
        // $this->upgrade_clear_cache();
        // 升级v1.7.7版本要处理的数据
        // $this->eyou_v177_handle_data();
        // 升级v1.7.9版本要处理的数据
        // $this->eyou_v179_handle_data();
        // 升级v1.8.1版本要处理的数据
        // $this->eyou_v181_handle_data();
        // 升级v1.8.3版本要处理的数据
        // $this->eyou_v183_handle_data();
    }

    // 升级v1.8.3版本要处理的数据
    private function eyou_v183_handle_data()
    {
        $Prefix = config('database.prefix');

        // AI配置中心的默认数据
        $admin_logic_1788321959 = tpSetting('syn.admin_logic_1788321959', [], 'cn');
        if (empty($admin_logic_1788321959)) {
            try {
                $data = [
                    'aiservice_rate_limit' => 30,
                    'aiservice_budget_chat' => 0,
                    'aiservice_budget_image' => 0,
                    'aiservice_log_days' => 90,
                ];
                $langRow = \think\Db::name('language')->order('id asc')->select();
                foreach ($langRow as $key => $val) {
                    tpCache('aiservice', $data, $val['mark']);
                }
                tpSetting('syn', ['admin_logic_1788321959'=>1], 'cn');
            } catch (\Exception $e) {}
        }
    }

    // 升级v1.8.1版本要处理的数据
    private function eyou_v181_handle_data()
    {
        $Prefix = config('database.prefix');

        // admin字段
        $tableInfo = Db::query("SHOW COLUMNS FROM {$Prefix}admin");
        $tableInfo = get_arr_column($tableInfo, 'Field');
        if (!empty($tableInfo) && !in_array('security_verify_time', $tableInfo)) {
            $sql = "ALTER TABLE `{$Prefix}admin` ADD COLUMN `security_verify_time`  int(11) NULL DEFAULT 0 COMMENT '密保验证通过时间' AFTER `union_id`;";
            @Db::execute($sql);
            schemaTable("admin");
        }
    }

    // 升级v1.7.9版本要处理的数据
    private function eyou_v179_handle_data()
    {
        $Prefix = config('database.prefix');

        // 插件字段
        $tableInfo = Db::query("SHOW COLUMNS FROM {$Prefix}weapp");
        $tableInfo = get_arr_column($tableInfo, 'Field');
        if (!empty($tableInfo) && !in_array('is_valid', $tableInfo)) {
            $sql = "ALTER TABLE `{$Prefix}weapp` ADD COLUMN `is_valid`  tinyint(1) NULL DEFAULT 1 AFTER `is_upgrade`;";
            @Db::execute($sql);
            schemaTable("weapp");
        }

        // 密保设置里去掉了IP白名单，将用户数据同一个改为关闭IP白名单
        $admin_logic_1770603298 = tpSetting('syn.admin_logic_1770603298', [], 'cn');
        if (empty($admin_logic_1770603298)) {
            try {
                $security_ask_ip_open_old = tpSetting('security.security_ask_ip_open');
                $langRow = \think\Db::name('language')->order('id asc')->select();
                foreach ($langRow as $key => $val) {
                    tpSetting('security', ['security_ask_ip_open'=>0], $val['mark']);
                    tpSetting('security', ['security_ask_ip_open_old'=>$security_ask_ip_open_old], $val['mark']);
                }
                tpSetting('syn', ['admin_logic_1770603298'=>1], 'cn');
            } catch (\Exception $e) {}
        }

        $admin_logic_1769398665 = tpSetting('syn.admin_logic_1769398665', [], 'cn');
        if (empty($admin_logic_1769398665)) {
            try {
                \think\Db::name('weapp')->where(['is_valid'=>0])->update(['is_valid'=>1]);
                $list = [];
                $vars = 'c3lzdGV'.'tLnN5c3R'.'lbV91c2'.'Vjb2R'.'lbGlz'.'dA==';
                $value = tpCache(base64_decode($vars));
                if (!empty($value)) {
                    $list = json_decode(mchStrCode($value, 'DECODE', 'xhg'), true);
                }
                if (is_array($list) && !empty($list)) {
                    $codes = [];
                    foreach ($list as $_k => $_v) {
                        if (strlen($_v) < 100) {
                            $codes[] = $_v;
                        }
                    }
                    if (!empty($codes)) {
                        \think\Db::name('weapp')->where(['code'=>['IN', $codes]])->update(['is_valid'=>0]);
                    }
                }
                \think\Cache::clear('weapp');
                tpSetting('syn', ['admin_logic_1769398665'=>1], 'cn');
            } catch (\Exception $e) {}
        }

    }

    // 升级v1.7.7版本要处理的数据
    private function eyou_v177_handle_data()
    {
        $Prefix = config('database.prefix');

        // 清理瀑布流的临时表
        $admin_logic_1766367066 = tpSetting('syn.admin_logic_1766367066', [], 'cn');
        if (empty($admin_logic_1766367066)) {
            try {
                Db::name('arcmulti')->where(['id'=>['gt', 0]])->delete(true);
                @Db::execute("ALTER TABLE `{$Prefix}arcmulti` AUTO_INCREMENT 1");
                @Db::query("REPAIR TABLE `{$Prefix}arcmulti`");
                tpSetting('syn', ['admin_logic_1766367066'=>1], 'cn');
            } catch (\Exception $e) {}
        }

        // 处理 download_file 表数据的md5值
        $admin_logic_1767517187 = tpSetting('syn.admin_logic_1767517187', [], 'cn');
        if (empty($admin_logic_1767517187)) {
            try {
                $updateData = [];
                $result = Db::name('download_file')->where("(uhash = '' OR uhash IS NULL) AND (md5file = '' OR md5file IS NULL)")->select();
                foreach ($result as $key => $val) {
                    $uhash = $md5file = '';
                    // 本地文件：自动计算 uhash 和 md5file
                    if (!is_http_url($val['file_url'])) {
                        $file_url = preg_replace('#^(/[/\w\-]+)?(/uploads/soft/)#i', '$2', $val['file_url']);
                        $file_url = '.' . $file_url;
                        if (file_exists($file_url)) {
                            $fragment = 65536;
                            $rh = fopen($file_url, 'rb');
                            $size = filesize($file_url);
                            $part1 = fread( $rh, $fragment );
                            fseek($rh, $size-$fragment);
                            $part2 = fread( $rh, $fragment);
                            fclose($rh);
                            $uhash = md5( $part1.$part2 );
                            $md5file = md5_file($file_url);
                        }
                    } else {
                        $uhash = md5($val['file_url']);
                        $md5file = md5($val['file_url']);
                    }

                    $updateData[] = [
                        'file_id' => $val['file_id'],
                        'uhash' => $uhash,
                        'md5file' => $md5file,
                    ];
                }
                model('DownloadFile')->saveAll($updateData);
                tpSetting('syn', ['admin_logic_1767517187'=>1], 'cn');
            } catch (\Exception $e) {}
        }

        // 安全中心权限问题
        $admin_logic_1758510736 = tpSetting('syn.admin_logic_1758510736', [], 'cn');
        if (empty($admin_logic_1758510736)) {
            try {
                $r = Db::name('quickentry')->where(['menu_id'=>2004017, 'type'=>15])->update(['auth_role'=>'', 'update_time'=>getTime()]);
                if ($r !== false) {
                    tpSetting('syn', ['admin_logic_1758510736'=>1], 'cn');
                }
            } catch (\Exception $e) {}
        }

        // 百度编辑器的风格切换，默认是新风格
        $admin_logic_1764810608 = tpSetting('syn.admin_logic_1764810608', [], 'cn');
        if (empty($admin_logic_1764810608)) {
            try {
                $langRow = \think\Db::name('language')->order('id asc')->select();
                foreach ($langRow as $key => $val) {
                    tpCache('editor', ['editor_bd_style'=>''], $val['mark']);
                    tpSetting('editor', ['editor_bd_style'=>''], $val['mark']);
                }
                tpSetting('syn', ['admin_logic_1764810608'=>1], 'cn');
                schemaAllTable();
            } catch (\Exception $e) {}
        }

        // Robots在功能地图的入口
        $admin_logic_1758178197 = tpSetting('syn.admin_logic_1758178197', [], 'cn');
        if (empty($admin_logic_1758178197)) {
            try {
                $result = Db::name('quickentry')->where(['controller'=>'Robots', 'action'=>'edit'])->find();
                if (empty($result)) {
                    $sql = "INSERT INTO `{$Prefix}quickentry` (`title`, `laytext`, `type`, `controller`, `action`, `vars`, `groups`, `checked`, `litpic`, `menu_id`, `menu_group`, `is_lang`, `auth_role`, `intro`, `statistics_type`, `status`, `sort_order`, `add_time`, `update_time`) VALUES ('Robots', 'Robots', '15', 'Robots', 'edit', '', '0', '0', 'iconfont e-robots', '2003005', '4', '0', '', '', '0', '1', '49', '1658910390', '1746752260')";
                    @Db::execute($sql);
                }
                $result = Db::name('quickentry')->where(['controller'=>'Faq', 'action'=>'index'])->find();
                if (empty($result)) {
                    $sql = "INSERT INTO `{$Prefix}quickentry` (`title`, `laytext`, `type`, `controller`, `action`, `vars`, `groups`, `checked`, `litpic`, `menu_id`, `menu_group`, `is_lang`, `auth_role`, `intro`, `statistics_type`, `status`, `sort_order`, `add_time`, `update_time`) VALUES ('FAQ', 'FAQ', '15', 'Faq', 'index', '', '0', '0', 'iconfont e-fqa', '2004028', '2', '0', '', '', '0', '1', '41', '1658910390', '1746752260')";
                    @Db::execute($sql);
                }
                tpSetting('syn', ['admin_logic_1758178197'=>1], 'cn');
            } catch (\Exception $e) {}
        }

        // 删掉索引
        $admin_logic_1764918904 = tpSetting('syn.admin_logic_1764918904', [], 'cn');
        if (empty($admin_logic_1764918904)) {
            try {
                @Db::execute("ALTER TABLE `{$Prefix}field_type` DROP INDEX `name`");
                tpSetting('syn', ['admin_logic_1764918904'=>1], 'cn');
            } catch (\Exception $e) {}
        }
        
        $isTable = Db::query('SHOW TABLES LIKE \''.$Prefix.'faq_asklist\'');
        if (empty($isTable)) {
            $r = true;
            $tableSql = <<<EOF
CREATE TABLE IF NOT EXISTS `{$Prefix}faq_asklist` (
  `asklist_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '问题ID',
  `group_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分组ID',
  `asklist_title` varchar(255) NOT NULL DEFAULT '' COMMENT '问题标题',
  `asklist_content` text NOT NULL COMMENT '问题回答',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `lang` varchar(50) NOT NULL DEFAULT 'cn' COMMENT '多语言',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`asklist_id`),
  KEY `group_id` (`group_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='常见问题问答列表';
EOF;
            try{
                $r = @Db::execute($tableSql);
            }catch(\Exception $e){
                if (stristr($e->getMessage(), 'Storage engine MyISAM is disabled')) {
                    $tableSql = str_replace('ENGINE=MyISAM', 'ENGINE=InnoDB', $tableSql);
                    $r = @Db::execute($tableSql);
                }
            }
            if ($r !== false) {
                schemaTable('faq_asklist');
            }
        }
        
        $isTable = Db::query('SHOW TABLES LIKE \''.$Prefix.'faq_group\'');
        if (empty($isTable)) {
            $r = true;
            $tableSql = <<<EOF
CREATE TABLE IF NOT EXISTS `{$Prefix}faq_group` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分组ID',
  `group_title` varchar(60) NOT NULL DEFAULT '' COMMENT '分组标题',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '启用状态(0:否; 1:是;)',
  `lang` varchar(50) NOT NULL DEFAULT 'cn' COMMENT '多语言',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='常见问题分组列表';
EOF;
            try{
                $r = @Db::execute($tableSql);
            }catch(\Exception $e){
                if (stristr($e->getMessage(), 'Storage engine MyISAM is disabled')) {
                    $tableSql = str_replace('ENGINE=MyISAM', 'ENGINE=InnoDB', $tableSql);
                    $r = @Db::execute($tableSql);
                }
            }
            if ($r !== false) {
                schemaTable('faq_group');
            }
        }

        // 促销语字段
        $tableInfo = Db::query("SHOW COLUMNS FROM {$Prefix}archives");
        $tableInfo = get_arr_column($tableInfo, 'Field');
        if (!empty($tableInfo) && !in_array('introduction', $tableInfo)) {
            $sql = "ALTER TABLE `{$Prefix}archives` ADD COLUMN `introduction` varchar(500) DEFAULT '' COMMENT '促销语' AFTER `subtitle`;";
            @Db::execute($sql);
            schemaTable("archives");
        }

        // 调整字段的长度
        $admin_logic_1761201779 = tpSetting('syn.admin_logic_1761201779', [], 'cn');
        if (empty($admin_logic_1761201779)) {
            try {
                Db::execute("ALTER TABLE `{$Prefix}arctype` MODIFY COLUMN `typename`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '栏目名称' AFTER `topid`");
                Db::execute("ALTER TABLE `{$Prefix}arctype` MODIFY COLUMN `dirname`  varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '目录英文名' AFTER `typename`");
                Db::execute("ALTER TABLE `{$Prefix}arctype` MODIFY COLUMN `dirpath`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '目录存放HTML路径' AFTER `dirname`");
                Db::execute("ALTER TABLE `{$Prefix}arctype` MODIFY COLUMN `diy_dirpath`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '列表静态文件存放规则' AFTER `dirpath`");
                Db::execute("ALTER TABLE `{$Prefix}arctype` MODIFY COLUMN `englist_name`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '栏目英文名' AFTER `ruleview`");
                Db::execute("ALTER TABLE `{$Prefix}arctype` MODIFY COLUMN `typelink`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '栏目链接' AFTER `grade`");
                Db::execute("ALTER TABLE `{$Prefix}arctype` MODIFY COLUMN `litpic`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '栏目图片' AFTER `typelink`");
                Db::execute("ALTER TABLE `{$Prefix}arctype` MODIFY COLUMN `seo_title`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'SEO标题' AFTER `tempview`");
                Db::execute("ALTER TABLE `{$Prefix}arctype` MODIFY COLUMN `seo_keywords`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'seo关键字' AFTER `seo_title`");
                schemaTable("arctype");

                Db::execute("ALTER TABLE `{$Prefix}archives` MODIFY COLUMN `seo_title`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'SEO标题' AFTER `ismake`");
                Db::execute("ALTER TABLE `{$Prefix}archives` MODIFY COLUMN `seo_keywords`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'SEO关键词' AFTER `seo_title`");
                schemaTable("archives");

                tpSetting('syn', ['admin_logic_1761201779'=>1], 'cn');
            } catch (\Exception $e) {}
        }

        // 验证码配置多语言同步
        $admin_logic_1762851725 = tpSetting('syn.admin_logic_1762851725', [], 'cn');
        if (empty($admin_logic_1762851725)) {
            try {
                $system_vertify = Db::name('setting')->where('name', 'system_vertify')->value('value');
                if (!empty($system_vertify)) {
                    tpSetting('system', ['system_vertify' => $system_vertify], 'cn');
                    Db::name('setting')->where(['name' => 'system_vertify', 'lang' => ['neq', 'cn']])->delete();
                }
                tpSetting('syn', ['admin_logic_1762851725'=>1], 'cn');
            } catch (\Exception $e) {}
        }
    }

    /**
     * 升级后，清理缓存文件
     * @return [type] [description]
     */
    private function upgrade_clear_cache()
    {
        $version = getVersion();
        $syn_admin_logic_1726881989 = tpSetting('syn.syn_admin_logic_1726881989', [], 'cn');
        if ($syn_admin_logic_1726881989 != $version) {
            try {
                delFile(rtrim(RUNTIME_PATH, '/'));
                tpSetting('syn', ['syn_admin_logic_1726881989' => $version], 'cn');
            } catch (\Exception $e) {}
        }
    }

    /**
     * 自动更新插件里的jquery文件为最新版本，修复jquery漏洞
     * @return [type] [description]
     */
    private function copy_jquery()
    {
        $list = glob('weapp/*/template/skin/js/jquery.js');
        if (!empty($list)) {
            $list[] = 'public/static/common/diyminipro/js/jquery.min.js';
            $minilist = glob('weapp/*/template/*/js/jquery.min.js');
            if (!empty($minilist)) {
                $list = array_merge($list, $minilist);
            }
            foreach ($list as $key => $val) {
                if (file_exists('./'.$val)) {
                    @copy(realpath('public/static/admin/js/jquery.js'), realpath($val));
                }
            }
        }
    }
    
    /*
    * 初始化原来的菜单栏目
    */
    public function initialize_admin_menu(){
        $total = Db::name("admin_menu")->count();
        if (empty($total)){
            $menuArr = getAllMenu();
            $insert_data = [];
            foreach ($menuArr as $key => $val){
                foreach ($val['child'] as $nk=>$nrr) {
                    $sort_order = 100;
                    $is_switch = 1;
                    if ($nrr['id'] == 2004){
                        $sort_order = 10000;
                        $is_switch = 0;
                    }
                    $insert_data[] = [
                        'menu_id' => $nrr['id'],
                        'title' => $nrr['name'],
                        'controller_name' => $nrr['controller'],
                        'action_name' => $nrr['action'],
                        'param' => !empty($nrr['param']) ? $nrr['param'] : '',
                        'is_menu' => $nrr['is_menu'],
                        'is_switch' => $is_switch,
                        'icon' =>  $nrr['icon'],
                        'sort_order' => $sort_order,
                        'add_time' => getTime(),
                        'update_time' => getTime()
                    ];
                }
            }
            Db::name("admin_menu")->insertAll($insert_data);
        }
    }

    /**
     * 同步模板的付费选择支付文件到前台模板指定位置
     * @return [type] [description]
     */
    public function copy_tplpayfile($channel = 0)
    {
        $shop_open_comment = getUsersConfigData('shop.shop_open_comment');
        $channelRow = Db::name('channeltype')->where(['status'=>1])->getAllWithIndex('id');
        foreach ($channelRow as $key => $val) {
            $data = json_decode($val['data'], true);
            $val['data'] = empty($data) ? [] : $data;
            $channelRow[$key] = $val;
        }
        $source_path = ROOT_PATH.'public/html/template/';
        $dest_path = ROOT_PATH.'template/'.THEME_STYLE_PATH.'/system/';
        if (stristr($dest_path, '/pc/system/')) {
            tp_mkdir($dest_path);
            if (!empty($channelRow[1]['data']['is_article_pay'])) {
                if (in_array($channel, [0,1]) && !file_exists($dest_path.'article_pay.htm') && file_exists($source_path.'pc/system/article_pay.htm')) {
                    @copy($source_path.'pc/system/article_pay.htm', $dest_path.'article_pay.htm');
                }
            }
            if (!empty($channelRow[4]['data']['is_download_pay'])) {
                if (in_array($channel, [0,4]) && !file_exists($dest_path.'download_pay.htm') && file_exists($source_path.'pc/system/download_pay.htm')) {
                    @copy($source_path.'pc/system/download_pay.htm', $dest_path.'download_pay.htm');
                }
            }
            if (!empty($shop_open_comment)) {
                if (in_array($channel, [0,2]) && !file_exists($dest_path.'product_comment.htm') && file_exists($source_path.'pc/system/product_comment.htm')) {
                    @copy($source_path.'pc/system/product_comment.htm', $dest_path.'product_comment.htm');
                }
            }
        }

        $dest_path = ROOT_PATH.'template/'.THEME_STYLE_PATH;
        $dest_path = preg_replace('/\/pc$/i', '/mobile', $dest_path);
        if (file_exists($dest_path)) {
            $dest_path .= '/system/';
            tp_mkdir($dest_path);
            if (!empty($channelRow[1]['data']['is_article_pay'])) {
                if (in_array($channel, [0,1]) && !file_exists($dest_path.'article_pay.htm') && file_exists($source_path.'mobile/system/article_pay.htm')) {
                    @copy($source_path.'mobile/system/article_pay.htm', $dest_path.'article_pay.htm');
                }
            }
            if (!empty($channelRow[4]['data']['is_download_pay'])) {
                if (in_array($channel, [0,4]) && !file_exists($dest_path.'download_pay.htm') && file_exists($source_path.'mobile/system/download_pay.htm')) {
                    @copy($source_path.'mobile/system/download_pay.htm', $dest_path.'download_pay.htm');
                }
            }
            if (!empty($shop_open_comment)) {
                if (in_array($channel, [0,2]) && !file_exists($dest_path.'product_comment.htm') && file_exists($source_path.'mobile/system/product_comment.htm')) {
                    @copy($source_path.'mobile/system/product_comment.htm', $dest_path.'product_comment.htm');
                }
                if (in_array($channel, [0,2]) && !file_exists($dest_path.'comment_list.htm') && file_exists($source_path.'mobile/system/comment_list.htm')) {
                    @copy($source_path.'mobile/system/comment_list.htm', $dest_path.'comment_list.htm');
                }
            }
        }
    }
}
