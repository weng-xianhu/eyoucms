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
        // $this->repairAdmin(); // 修复管理员ID为0的问题
        $this->saveBaseFile(); // 存储后台入口文件路径，比如：/login.php
        clear_session_file(); // 清理过期的data/session文件
    }

    /**
     * 修复管理员
     * @return [type] [description]
     */
    private function repairAdmin()
    {
        $row = [];
        $result = Db::name('admin')->field('admin_id,user_name')->order('add_time asc')->select();
        $total = count($result);
        foreach ($result as $key => $val) {
            $pre_admin_id = $next_admin_id = 0;
            if (empty($val['admin_id'])) {
                if (1 == $total) {
                    Db::name('admin')->where(['user_name'=>$val['user_name']])->update(['admin_id'=>1, 'update_time'=>getTime()]);
                } else {
                    $pre_admin_id = empty($key) ? 0 : $result[$key - 1]['admin_id'];
                    if ($key < ($total - 1)) {
                        $next_admin_id = $result[$key + 1]['admin_id'];
                    } else {
                        $next_admin_id = $pre_admin_id + 2;
                    }

                    if (($next_admin_id - $pre_admin_id) >= 2) {
                        $admin_id = $pre_admin_id + 1;
                        Db::name('admin')->where(['user_name'=>$val['user_name']])->update(['admin_id'=>$admin_id, 'update_time'=>getTime()]);
                    }
                }
            }
        }
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
            ROOT_PATH."data/conf/quickentry_data.txt",
            ROOT_PATH."data/weapp/Sample/weapp/Sample/template/skin/css/main.css",
            ROOT_PATH."data/weapp/Sample/weapp/Sample/template/skin/css/page.css",
            ROOT_PATH."data/weapp/Sample/weapp/Sample/template/skin/css/perfect-scrollbar.min.css",
            ROOT_PATH."data/weapp/Sample/weapp/Sample/template/skin/images/combine_img.png",
            ROOT_PATH."data/weapp/Sample/weapp/Sample/template/skin/images/macro_arrow.gif",
            ROOT_PATH."data/weapp/Sample/weapp/Sample/template/skin/images/ui_tip.png",
            ROOT_PATH."data/weapp/Sample/weapp/Sample/template/skin/js/jquery-ui",
            ROOT_PATH."data/weapp/Sample/weapp/Sample/template/skin/js/admin.js",
            ROOT_PATH."data/weapp/Sample/weapp/Sample/template/skin/js/global.js",
            ROOT_PATH."data/weapp/Sample/weapp/Sample/template/skin/js/jquery.js",
            ROOT_PATH."data/weapp/Sample/weapp/Sample/template/skin/js/perfect-scrollbar.min.js",
            ROOT_PATH."application/admin/template/archives_flag/edit.htm",
            ROOT_PATH."public/static/admin/images/ai/feature-1.png",
            ROOT_PATH."public/static/admin/images/ai/feature-2.png",
            ROOT_PATH."public/static/admin/images/ai/feature-3.png",
            ROOT_PATH."public/static/admin/images/ai/feature-4.png",
            ROOT_PATH."public/static/admin/images/ai/feature-5.png",
            ROOT_PATH."public/static/admin/images/ai/feature-6.png",
            ROOT_PATH."vendor/phpmailer/get_oauth_token.php",
            ROOT_PATH."vendor/aliyun-php-sdk-core/Auth/AbstractCredential.php",
            ROOT_PATH."vendor/aliyun-php-sdk-core/Auth/BearerTokenCredential.php",
            ROOT_PATH."vendor/aliyun-php-sdk-core/Auth/BearTokenSigner.php",
            ROOT_PATH."vendor/aliyun-php-sdk-core/Auth/EcsRamRoleCredential.php",
            ROOT_PATH."vendor/aliyun-php-sdk-core/Auth/EcsRamRoleService.php",
            ROOT_PATH."vendor/aliyun-php-sdk-core/Auth/RamRoleArnCredential.php",
            ROOT_PATH."vendor/aliyun-php-sdk-core/Auth/RamRoleArnService.php",
            ROOT_PATH."vendor/aliyun-php-sdk-core/Regions/LocationService.php",
            ROOT_PATH."core/codecov.yml",
            ROOT_PATH."core/composer.json",
            ROOT_PATH."core/phpunit.xml",
            ROOT_PATH."core/.travis.yml",
            ROOT_PATH."core/library/think/console/bin/hiddeninput.exe",
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
        // 升级v1.6.7版本要处理的数据
        // $this->eyou_v167_handle_data();
        // 升级后，清理缓存文件
        // $this->upgrade_clear_cache();
        // 升级v1.7.0版本要处理的数据
        // $this->eyou_v170_handle_data();
        // 升级v1.7.1版本要处理的数据
        // $this->eyou_v171_handle_data();
        // 升级v1.7.2版本要处理的数据
        // $this->eyou_v172_handle_data();
        // 升级v1.7.3版本要处理的数据
        // $this->eyou_v173_handle_data();
        // 升级v1.7.4版本要处理的数据
        // $this->eyou_v174_handle_data();
        // 升级v1.7.5版本要处理的数据
        $this->eyou_v175_handle_data();
        // 升级v1.7.6版本要处理的数据
        $this->eyou_v176_handle_data();
        // 升级v1.7.7版本要处理的数据
        $this->eyou_v177_handle_data();
        // 升级v1.7.9版本要处理的数据
        $this->eyou_v179_handle_data();
        // 升级v1.8.1版本要处理的数据
        $this->eyou_v181_handle_data();
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

    // 升级v1.7.6版本要处理的数据
    private function eyou_v176_handle_data()
    {
        $Prefix = config('database.prefix');

        // 售后订单表字段新增处理
        if (config('database.type') == 'dm') { // 达梦优化
            $tableInfo = Db::query("SELECT COLUMN_NAME,DATA_TYPE,DATA_DEFAULT,NULLABLE FROM ALL_TAB_COLS WHERE TABLE_NAME = '{$Prefix}shop_order_service'");
            $tableInfo = get_arr_column($tableInfo, 'COLUMN_NAME');
            if (!empty($tableInfo) && !in_array('refund_reason', $tableInfo)) {
                $sql = "ALTER TABLE `{$Prefix}shop_order_service` ADD COLUMN `refund_reason` VARCHAR(255) NOT NULL DEFAULT '';";
                if (@Db::execute($sql)) {
                    $sql = "comment ON COLUMN `{$Prefix}shop_order_service`.`refund_reason` IS '提交维权时的(退货退款 or 换货)原因';";
                    @Db::execute($sql);
                }
            }
        } elseif (config('database.type') == 'kingbase') { // 人大金仓优化
            $tableInfo = Db::query("SELECT column_name FROM information_schema.columns WHERE table_name = '{$Prefix}shop_order_service' AND table_schema = 'public'");
            $tableInfo = get_arr_column($tableInfo, 'column_name');
            if (!empty($tableInfo) && !in_array('refund_reason', $tableInfo)) {
                $sql = "ALTER TABLE \"{$Prefix}shop_order_service\" ADD COLUMN \"refund_reason\" VARCHAR(255) NOT NULL DEFAULT ''";
                @Db::execute($sql);
                $sql = "COMMENT ON COLUMN \"{$Prefix}shop_order_service\".\"refund_reason\" IS '提交维权时的(退货退款 or 换货)原因'";
                @Db::execute($sql);
            }
        } else {
            $tableInfo = Db::query("SHOW COLUMNS FROM {$Prefix}shop_order_service");
            $tableInfo = get_arr_column($tableInfo, 'Field');
            if (!empty($tableInfo) && !in_array('refund_reason', $tableInfo)) {
                $sql = "ALTER TABLE `{$Prefix}shop_order_service` ADD COLUMN `refund_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '提交维权时的(退货退款 or 换货)原因' AFTER `refund_code`;";
                @Db::execute($sql);
            }
        }
        schemaTable('shop_order_service');

        // 文档主表字段新增处理
        if (config('database.type') == 'dm') { // 达梦优化
            $serviceTableInfo = Db::query("SELECT COLUMN_NAME,DATA_TYPE,DATA_DEFAULT,NULLABLE FROM ALL_TAB_COLS WHERE TABLE_NAME = '{$Prefix}archives'");
            $serviceTableInfo = get_arr_column($serviceTableInfo, 'COLUMN_NAME');
            if (!empty($serviceTableInfo) && !in_array('editor_ai_create', $serviceTableInfo)) {
                $sql = "ALTER TABLE `{$Prefix}archives` ADD COLUMN `editor_ai_create` TINYINT NOT NULL DEFAULT 0;";
                if (@Db::execute($sql)) {
                    $sql = "comment ON COLUMN `{$Prefix}archives`.`editor_ai_create` IS 'AI创作声明';";
                    @Db::execute($sql);
                }
            }
        } elseif (config('database.type') == 'kingbase') { // 人大金仓优化
            $serviceTableInfo = Db::query("SELECT column_name FROM information_schema.columns WHERE table_name = '{$Prefix}archives' AND table_schema = 'public'");
            $serviceTableInfo = get_arr_column($serviceTableInfo, 'column_name');
            if (!empty($serviceTableInfo) && !in_array('editor_ai_create', $serviceTableInfo)) {
                $sql = "ALTER TABLE \"{$Prefix}archives\" ADD COLUMN \"editor_ai_create\" SMALLINT NOT NULL DEFAULT 0";
                @Db::execute($sql);
                $sql = "COMMENT ON COLUMN \"{$Prefix}archives\".\"editor_ai_create\" IS 'AI创作声明'";
                @Db::execute($sql);
            }
        } else {
            $serviceTableInfo = Db::query("SHOW COLUMNS FROM {$Prefix}archives");
            $serviceTableInfo = get_arr_column($serviceTableInfo, 'Field');
            if (!empty($serviceTableInfo) && !in_array('editor_ai_create', $serviceTableInfo)) {
                $sql = "ALTER TABLE `{$Prefix}archives` ADD COLUMN `editor_ai_create`  tinyint(1) DEFAULT 0 COMMENT 'AI创作声明' AFTER `editor_img_clear_link`;";
                @Db::execute($sql);
            }
        }
        schemaTable('archives');

        // [搜索管理]-[搜索模式]-[智能模糊]默认选中标题和SEO关键词
        $admin_logic_1755827611 = tpSetting('syn.admin_logic_1755827611', [], 'cn');
        if (empty($admin_logic_1755827611)) {
            try {
                $data = [
                    'intellect_arr' => '["title","seo_keywords"]',
                ];
                $langRow = \think\Db::name('language')->order('id asc')->select();
                foreach ($langRow as $key => $val) {
                    tpCache('search', $data, $val['mark']);
                }
                tpSetting('syn', ['admin_logic_1755827611'=>1], 'cn');
            } catch (\Exception $e) {}
        }

        // 邮件模板表增加邮箱验证
        $admin_logic_1756106817 = tpSetting('syn.admin_logic_1756106817', [], 'cn');
        if (empty($admin_logic_1756106817)) {
            try {
                // 删除原有模板
                Db::name('smtp_tpl')->where(['send_scene' => 40])->delete(true);
                // 添加新模板
                $insert = [
                    'tpl_name' => '邮箱验证',
                    'tpl_title' => '您的邮箱验证码，请查收！',
                    'tpl_content' => '${content}',
                    'send_scene' => 40,
                    'is_open' => 1,
                    'lang' => 'cn',
                    'add_time' => getTime(),
                    'update_time' => getTime(),
                ];
                $langRow = Db::name('language')->order('id asc')->select();
                $insertAll = [];
                foreach ($langRow as $key => $val) {
                    if (!empty($val)) {
                        $insert['lang'] = trim($val['mark']);
                        $insertAll[] = $insert;
                    }
                }
                if (!empty($insertAll)) {
                    Db::name('smtp_tpl')->insertAll($insertAll);
                    tpSetting('syn', ['admin_logic_1756106817'=>1], 'cn');
                }
            } catch (\Exception $e) {}
        }
    }

    // 升级v1.7.5版本要处理的数据
    private function eyou_v175_handle_data()
    {
        $Prefix = config('database.prefix');

        // 如果没有用到小程序这块，API接口强制开启验证，并关闭接口
        $admin_logic_1750415654 = tpSetting('syn.admin_logic_1750415654', [], 'cn');
        if (empty($admin_logic_1750415654)) {
            try {
                $is_use_api = false;
                $main_lang = get_main_lang();
                if (false === $is_use_api) {
                    $weixin_data = tpSetting("OpenMinicode.conf_weixin", [], $main_lang);
                    $weixin_data = json_decode($weixin_data, true);
                    if (!empty($weixin_data['appid']) && !empty($weixin_data['appsecret'])) {
                        $is_use_api = true;
                    }
                }
                if (false === $is_use_api) {
                    $baidu_data = tpSetting("OpenMinicode.conf_baidu", [], $main_lang);
                    $baidu_data = json_decode($baidu_data, true);
                    if (!empty($baidu_data['appid']) && !empty($baidu_data['appkey']) && !empty($baidu_data['appsecret'])) {
                        $is_use_api = true;
                    }
                }
                if (false === $is_use_api) {
                    $toutiao_data = tpSetting("OpenMinicode.conf_toutiao", [], $main_lang);
                    $toutiao_data = !empty($toutiao_data) ? json_decode($toutiao_data, true) : [];
                    if (!empty($toutiao_data['appid']) && !empty($toutiao_data['secret'])) {
                        $is_use_api = true;
                    }
                }
                if (false === $is_use_api) { // 如果没有用到小程序这块，API接口强制开启验证，并关闭接口
                    $data = tpSetting("OpenMinicode.conf", [], $main_lang);
                    if (empty($data)) {
                        $data = [];
                        $data['apiopen'] = 0;
                        $data['apiverify'] = 0;
                        $data['apikey'] = get_rand_str(32, 0, 1);
                        tpSetting('OpenMinicode', ['conf' => json_encode($data)], $main_lang);
                    } else {
                        $data = json_decode($data, true);
                    }
                    $old_apikey = empty($data['apikey']) ? '' : $data['apikey'];

                    $post = [
                        'apiopen' => 1,
                        'apiverify' => 1,
                        'apikey' => get_rand_str(32, 0, 1),
                        'old_apikey' => $old_apikey,
                    ];
                    if ($post['apikey'] != $post['old_apikey']) {
                        $post['apikey_uptime'] = getTime();
                    }
                    $data = array_merge($data, $post);
                    tpSetting('OpenMinicode', ['conf' => json_encode($data)], $main_lang);
                    tpSetting('syn', ['admin_logic_1750415654'=>1], 'cn');
                }
            } catch (\Exception $e) {
                
            }
        }

        // 公众号消息推送 --- 问答回复提醒
        $admin_logic_1750736088 = tpSetting('syn.admin_logic_1750736088', [], 'cn');
        if (empty($admin_logic_1750736088)) {
            try {
                $row = Db::name('wechat_template')->where('send_scene', 20)->count();
                if (empty($row)) {
                    $insert = [
                        'tpl_title' => '问答',
                        'template_title' => '收到工单通知',
                        'template_code' => 0,
                        'template_id' => '',
                        'tpl_data' => json_encode([
                            'keywordsList' => [
                                0 => ['name' => '工单名称', 'example' => 'NPS低分', 'rule' => 'thing4'],
                                1 => ['name' => '触发来源', 'example' => '顾客', 'rule' => 'thing8'],
                                2 => ['name' => '更新时间', 'example' => '2022-01-01 00:00:00', 'rule' => 'time20'],
                            ],
                        ]),
                        'send_scene' => 20,
                        'is_open' => 0,
                        'info' => '用户回复问答后立即发送',
                        'lang' => 'cn',
                        'add_time' => 1712367392,
                        'update_time' => 1712367392,
                    ];
                    Db::name('wechat_template')->insert($insert);
                }
                tpSetting('syn', ['admin_logic_1750736088'=>1], 'cn');
            } catch (\Exception $e) {}
        }

    }

    // 升级v1.7.4版本要处理的数据
    private function eyou_v174_handle_data()
    {
        $Prefix = config('database.prefix');

        // ai欢迎页主题
        $admin_logic_1749546524 = tpSetting('syn.admin_logic_1749546524', [], 'cn');
        if (empty($admin_logic_1749546524)) {
            try {
                $row = Db::name('admin_theme')->where('theme_id', 80)->find();
                if (empty($row)) {
                    @Db::execute("INSERT INTO `{$Prefix}admin_theme` (`theme_id`, `theme_type`, `theme_title`, `theme_pic`, `theme_color_model`, `theme_main_color`, `theme_assist_color`, `login_logo`, `login_bgimg_model`, `login_bgimg`, `login_tplname`, `admin_logo`, `welcome_tplname`, `is_system`, `sort_order`, `add_time`, `update_time`) VALUES ('80', '2', 'AI欢迎页', '/public/static/admin/images/theme/theme_pic_4.png', '', '', '', '', '', '', '', '', 'welcome_ai.htm', '1', '100', '1681200988', '1681200988');");
                    \think\Cache::clear('admin_theme');
                }
                tpSetting('syn', ['admin_logic_1749546524'=>1], 'cn');
            } catch (\Exception $e) {
                
            }
        }
    }

    // 升级v1.7.3版本要处理的数据
    private function eyou_v173_handle_data()
    {
        $Prefix = config('database.prefix');
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

        // 搜索配置里新增 eval 等高危函数，禁止搜索
        $admin_logic_1744164167 = tpSetting('syn.admin_logic_1744164167', [], 'cn');
        if (empty($admin_logic_1744164167)) {
            $search_tabu_words = tpCache('search.search_tabu_words');
            if (empty($search_tabu_words)) {
                $search_tabu_words = implode(PHP_EOL, ['<','>','"',';',',','@','&','eval','call_user_func_array','file_put_contents','phpinfo','shell_exec']);
            } else {
                $arr = explode(PHP_EOL, strtolower($search_tabu_words));
                if (!in_array('eval', $arr)) $arr[] = 'eval';
                if (!in_array('call_user_func_array', $arr)) $arr[] = 'call_user_func_array';
                if (!in_array('file_put_contents', $arr)) $arr[] = 'file_put_contents';
                if (!in_array('phpinfo', $arr)) $arr[] = 'phpinfo';
                if (!in_array('shell_exec', $arr)) $arr[] = 'shell_exec';
                $search_tabu_words = implode(PHP_EOL, $arr);
            }
            /*多语言*/
            $langRow = \think\Db::name('language')->order('id asc')->select();
            foreach ($langRow as $key => $val) {
                tpCache('search', ['search_tabu_words'=>$search_tabu_words], $val['mark']);
            }
            /*--end*/
            tpSetting('syn', ['admin_logic_1744164167'=>1], 'cn');
        }
    }

    // 升级v1.7.2版本要处理的数据
    private function eyou_v172_handle_data()
    {
        // 表前缀
        $Prefix = config('database.prefix');
        
        // 补充下架时间的字段记录
        $count = Db::name('channelfield')->where(['name'=>'removal_time', 'channel_id'=>['egt', 0]])->count();
        if (empty($count)) {
            $ids = Db::name('channeltype')->where(['nid'=>['NOTIN', ['guestbook','ask']]])->order('id asc')->column('id');
            $ids[] = 0;
            $insert_data = [];
            foreach ($ids as $key => $val) {
                $insert_data[] = [
                    'name' => 'removal_time',
                    'channel_id' => $val,
                    'title' => '下架时间',
                    'dtype' => 'datetime',
                    'define' => 'int(11)',
                    'maxlength' => 11,
                    'dfvalue' => 0,
                    'dfvalue_unit' => '',
                    'remark' => '',
                    'is_screening' => 0,
                    'is_release' => 0,
                    'ifeditable' => 1,
                    'ifrequire' => 0,
                    'ifsystem' => 1,
                    'ifmain' => 1,
                    'ifcontrol' => 1,
                    'sort_order' => 100,
                    'status' => 1,
                    'add_time' => 1574233796,
                    'update_time' => 1574233796,
                    'set_type' => 0,
                ];
            }
            Db::name('channelfield')->insertAll($insert_data);
        }

        // 处理外贸助手功能数据表
        $this->syn_handle_foreign_pack();
    }

    /**
     * 处理外贸助手功能数据表
     * @return [type] [description]
     */
    private function syn_handle_foreign_pack()
    {
        $Prefix = config('database.prefix');
        $admin_logic_1741662665 = tpSetting('syn.admin_logic_1741662665', [], 'cn');
        if (empty($admin_logic_1741662665)) {
            try {
                $row = Db::name('foreign_pack')->column('name');
                if (empty($row) || !in_array('users51', $row)) {
                    @Db::execute("INSERT INTO `{$Prefix}foreign_pack` (`type`, `name`, `value`, `lang`, `sort_order`, `add_time`, `update_time`) VALUES ('4', 'users51', '商品库存仅%s件！', 'cn', '100', '1543890216', '1543890216');");
                    @Db::execute("INSERT INTO `{$Prefix}foreign_pack` (`type`, `name`, `value`, `lang`, `sort_order`, `add_time`, `update_time`) VALUES ('4', 'users51', 'The inventory of the product is only %s pieces', 'en', '100', '1543890216', '1706580800');");
                }
                if (empty($row) || !in_array('users52', $row)) {
                    @Db::execute("INSERT INTO `{$Prefix}foreign_pack` (`type`, `name`, `value`, `lang`, `sort_order`, `add_time`, `update_time`) VALUES ('4', 'users52', '商品数量最少为%s', 'cn', '100', '1543890216', '1543890216');");
                    @Db::execute("INSERT INTO `{$Prefix}foreign_pack` (`type`, `name`, `value`, `lang`, `sort_order`, `add_time`, `update_time`) VALUES ('4', 'users52', 'The minimum quantity of goods is %s', 'en', '100', '1543890216', '1706580800');");
                }
                model('ForeignPack')->updateLangFile();
                \think\Cache::clear('foreign_pack');
                tpSetting('syn', ['admin_logic_1741662665'=>1], 'cn');
            } catch (\Exception $e) {
                
            }
        }
    }

    // 升级v1.7.1版本要处理的数据
    private function eyou_v171_handle_data()
    {
        // 表前缀
        $Prefix = config('database.prefix');
        
        $isTable = Db::query('SHOW TABLES LIKE \''.$Prefix.'language_archives_copy_log\'');
        if (empty($isTable)) {
            $r = true;
            $tableSql = <<<EOF
CREATE TABLE IF NOT EXISTS `{$Prefix}language_archives_copy_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `channel` int(10) DEFAULT '0',
  `typeid` int(10) DEFAULT '0' COMMENT '分类ID',
  `new_typeid` int(10) DEFAULT '0',
  `oldid` int(10) DEFAULT '0',
  `newid` int(10) DEFAULT '0',
  `lang` varchar(20) DEFAULT '' COMMENT '生成语言',
  `add_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_oldid` (`oldid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
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
                schemaTable('language_archives_copy_log');
            }
        }

        $syn_admin_logic_1732517850 = tpSetting('syn.syn_admin_logic_1732517850', [], 'cn');
        if (empty($syn_admin_logic_1732517850)) {
            try{
                $param = [];
                // 编辑器防注入
                $param['web_xss_filter'] = tpCache('web.web_xss_filter');
                $web_xss_words = ['union','delete','outfile','char','concat','truncate','insert','revoke','grant','replace','rename','declare','exec','delimiter','phar','eval','onerror','script'];
                $param['web_xss_words'] = implode(PHP_EOL, $web_xss_words);
                // 网站防止被刷
                $param['web_anti_brushing'] = tpCache('web.web_anti_brushing');
                $param['web_anti_words'] = implode(PHP_EOL, ['wd']);
                /*-------------------后台安全配置 end-------------------*/
                $langRow = \think\Db::name('language')->order('id asc')->select();
                foreach ($langRow as $key => $val) {
                    tpCache('web', $param, $val['mark']);
                }
                // 存储文件
                $content = json_encode($param);
                $tfile = webXssKeyFile();
                $fp = @fopen($tfile,'w');
                if(!$fp) {
                    @file_put_contents($tfile, $content);
                }
                else {
                    fwrite($fp, $content);
                    fclose($fp);
                }
                tpSetting('syn', ['syn_admin_logic_1732517850'=>1], 'cn');
            }catch(\Exception $e){}
        }
        
        // 处理上个版本升级后，导航数据因为标签底层改动，缓存问题导致不显示
        $syn_admin_logic_1732586784 = tpSetting('syn.syn_admin_logic_1732586784', [], 'cn');
        if (empty($syn_admin_logic_1732586784)) {
            try {
                $upgradeTime = Db::name('config')->where(['name'=>'system_version', 'value'=>'v1.7.0'])->order('update_time asc')->value('update_time');
                if ($upgradeTime < 1732587428) {
                    delFile(rtrim(RUNTIME_PATH, '/'));
                }
            } catch (\Exception $e) {
                
            }
            tpSetting('syn', ['syn_admin_logic_1732586784'=>1], 'cn');
        }

        $syn_admin_logic_1735088029 = tpSetting('syn.syn_admin_logic_1735088029', [], 'cn');
        if (empty($syn_admin_logic_1735088029)) {
            try{
                $r = true;
                Db::name('users_menu')->where(['version'=>'v5'])->delete();
                $saveData = Db::name('users_menu')->field('id', true)->where(['version'=>'v2'])->select();
                if (!empty($saveData)) {
                    $addData = [];
                    foreach ($saveData as $key => $val) {
                        $val['version'] = 'v5';
                        $addData[] = $val;
                    }
                    $r = Db::name('users_menu')->insertAll($addData);
                }
                if ($r !== false) {
                    tpSetting('syn', ['syn_admin_logic_1735088029'=>1], 'cn');
                }
            }catch(\Exception $e){}
        }

        // 处理国家表数据
        $this->syn_handle_country();
    }

    /**
     * 处理国家表数据
     * @return [type] [description]
     */
    private function syn_handle_country()
    {
        $admin_logic_1735200508 = tpSetting('syn.admin_logic_1735200508', [], 'cn');
        if (empty($admin_logic_1735200508)) {
            $r = true;
            $Prefix = config('database.prefix');
            $isTable = Db::query('SHOW TABLES LIKE \''.$Prefix.'country\'');
            if (empty($isTable)) {
                $tableSql = <<<EOF
CREATE TABLE `{$Prefix}country` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '编码',
  `continent` varchar(50) NOT NULL DEFAULT '' COMMENT '所属大洲',
  `sort_order` int(11) unsigned DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态(0:禁用; 1:启用;)',
  `add_time` int(11) unsigned DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='国家列表';
EOF;
                try{
                    $r = @Db::execute($tableSql);
                }catch(\Exception $e){
                    if (stristr($e->getMessage(), 'Storage engine MyISAM is disabled')) {
                        $tableSql = str_replace('ENGINE=MyISAM', 'ENGINE=InnoDB', $tableSql);
                        $r = @Db::execute($tableSql);
                    }
                }
            }
            if ($r !== false) {
                schemaTable('country');
                Db::name('country')->where(['id'=>['gt', 0]])->delete(true);
                $saveData = [
                    [
                        "id" => 1,
                        "name" => "中国",
                        "code" => "CN",
                        "continent" => "AS",
                        "sort_order" => 1,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1732173305,
                    ],
                    [
                        "id" => 2,
                        "name" => "Afghanistan",
                        "code" => "AF",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 3,
                        "name" => "Albania",
                        "code" => "AL",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 4,
                        "name" => "Algeria",
                        "code" => "DZ",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 5,
                        "name" => "American Samoa",
                        "code" => "AS",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 6,
                        "name" => "Andorra",
                        "code" => "AD",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 7,
                        "name" => "Angola",
                        "code" => "AO",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 8,
                        "name" => "Anguilla",
                        "code" => "AI",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 9,
                        "name" => "Antarctica",
                        "code" => "AQ",
                        "continent" => "AN",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 10,
                        "name" => "Antigua and Barbuda",
                        "code" => "AG",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 11,
                        "name" => "Argentina",
                        "code" => "AR",
                        "continent" => "SA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 12,
                        "name" => "Armenia",
                        "code" => "AM",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 13,
                        "name" => "Aruba",
                        "code" => "AW",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 14,
                        "name" => "Australia",
                        "code" => "AU",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 15,
                        "name" => "Austria",
                        "code" => "AT",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 16,
                        "name" => "Azerbaijan",
                        "code" => "AZ",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 17,
                        "name" => "Bahamas",
                        "code" => "BS",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 18,
                        "name" => "Bahrain",
                        "code" => "BH",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 19,
                        "name" => "Bangladesh",
                        "code" => "BD",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 20,
                        "name" => "Barbados",
                        "code" => "BB",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 21,
                        "name" => "Belarus",
                        "code" => "BY",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 22,
                        "name" => "Belgium",
                        "code" => "BE",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 23,
                        "name" => "Belize",
                        "code" => "BZ",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 24,
                        "name" => "Benin",
                        "code" => "BJ",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 25,
                        "name" => "Bermuda",
                        "code" => "BM",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 26,
                        "name" => "Bhutan",
                        "code" => "BT",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 27,
                        "name" => "Bolivia",
                        "code" => "BO",
                        "continent" => "SA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 28,
                        "name" => "Bosnia and Herzegovina",
                        "code" => "BA",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 29,
                        "name" => "Botswana",
                        "code" => "BW",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 30,
                        "name" => "Bouvet Island",
                        "code" => "BV",
                        "continent" => "AN",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 31,
                        "name" => "Brazil",
                        "code" => "BR",
                        "continent" => "SA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 32,
                        "name" => "British Indian Ocean Territory",
                        "code" => "IO",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 33,
                        "name" => "Brunei Darussalam",
                        "code" => "BN",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 34,
                        "name" => "Bulgaria",
                        "code" => "BG",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 35,
                        "name" => "Burkina Faso",
                        "code" => "BF",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 36,
                        "name" => "Burundi",
                        "code" => "BI",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 37,
                        "name" => "Cambodia",
                        "code" => "KH",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 38,
                        "name" => "Cameroon",
                        "code" => "CM",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 39,
                        "name" => "Canada",
                        "code" => "CA",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 40,
                        "name" => "Cape Verde",
                        "code" => "CV",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 41,
                        "name" => "Cayman Islands",
                        "code" => "KY",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 42,
                        "name" => "Central African Republic",
                        "code" => "CF",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 43,
                        "name" => "Chad",
                        "code" => "TD",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 44,
                        "name" => "Chile",
                        "code" => "CL",
                        "continent" => "SA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 45,
                        "name" => "Christmas Island",
                        "code" => "CX",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 46,
                        "name" => "Cocos (Keeling) Islands",
                        "code" => "CC",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 47,
                        "name" => "Colombia",
                        "code" => "CO",
                        "continent" => "SA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 48,
                        "name" => "Comoros",
                        "code" => "KM",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 49,
                        "name" => "Congo",
                        "code" => "CG",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 50,
                        "name" => "Cook Islands",
                        "code" => "CK",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 51,
                        "name" => "Costa Rica",
                        "code" => "CR",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 52,
                        "name" => "Cote D'Ivoire",
                        "code" => "CI",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 53,
                        "name" => "Croatia",
                        "code" => "HR",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 54,
                        "name" => "Cuba",
                        "code" => "CU",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 55,
                        "name" => "Cyprus",
                        "code" => "CY",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 56,
                        "name" => "Czech Republic",
                        "code" => "CZ",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 57,
                        "name" => "Denmark",
                        "code" => "DK",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 58,
                        "name" => "Djibouti",
                        "code" => "DJ",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 59,
                        "name" => "Dominica",
                        "code" => "DM",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 60,
                        "name" => "Dominican Republic",
                        "code" => "DO",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 61,
                        "name" => "East Timor",
                        "code" => "TL",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 62,
                        "name" => "Ecuador",
                        "code" => "EC",
                        "continent" => "SA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 63,
                        "name" => "Egypt",
                        "code" => "EG",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 64,
                        "name" => "El Salvador",
                        "code" => "SV",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 65,
                        "name" => "Equatorial Guinea",
                        "code" => "GQ",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 66,
                        "name" => "Eritrea",
                        "code" => "ER",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 67,
                        "name" => "Estonia",
                        "code" => "EE",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 68,
                        "name" => "Ethiopia",
                        "code" => "ET",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 69,
                        "name" => "Falkland Islands (Malvinas)",
                        "code" => "FK",
                        "continent" => "SA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 70,
                        "name" => "Faroe Islands",
                        "code" => "FO",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 71,
                        "name" => "Fiji",
                        "code" => "FJ",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 72,
                        "name" => "Finland",
                        "code" => "FI",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 73,
                        "name" => "France, Metropolitan",
                        "code" => "FR",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 74,
                        "name" => "French Guiana",
                        "code" => "GF",
                        "continent" => "SA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 75,
                        "name" => "French Polynesia",
                        "code" => "PF",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 76,
                        "name" => "French Southern Territories",
                        "code" => "TF",
                        "continent" => "AN",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 77,
                        "name" => "Gabon",
                        "code" => "GA",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 78,
                        "name" => "Gambia",
                        "code" => "GM",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 79,
                        "name" => "Georgia",
                        "code" => "GE",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 80,
                        "name" => "Germany",
                        "code" => "DE",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 81,
                        "name" => "Ghana",
                        "code" => "GH",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 82,
                        "name" => "Gibraltar",
                        "code" => "GI",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 83,
                        "name" => "Greece",
                        "code" => "GR",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 84,
                        "name" => "Greenland",
                        "code" => "GL",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 85,
                        "name" => "Grenada",
                        "code" => "GD",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 86,
                        "name" => "Guadeloupe",
                        "code" => "GP",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 87,
                        "name" => "Guam",
                        "code" => "GU",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 88,
                        "name" => "Guatemala",
                        "code" => "GT",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 89,
                        "name" => "Guinea",
                        "code" => "GN",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 90,
                        "name" => "Guinea-Bissau",
                        "code" => "GW",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 91,
                        "name" => "Guyana",
                        "code" => "GY",
                        "continent" => "SA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 92,
                        "name" => "Haiti",
                        "code" => "HT",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 93,
                        "name" => "Heard and Mc Donald Islands",
                        "code" => "HM",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 94,
                        "name" => "Honduras",
                        "code" => "HN",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 95,
                        "name" => "Hungary",
                        "code" => "HU",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 96,
                        "name" => "Iceland",
                        "code" => "IS",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 97,
                        "name" => "India",
                        "code" => "IN",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 98,
                        "name" => "Indonesia",
                        "code" => "ID",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 99,
                        "name" => "Iran (Islamic Republic of)",
                        "code" => "IR",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 100,
                        "name" => "Iraq",
                        "code" => "IQ",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 101,
                        "name" => "Ireland",
                        "code" => "IE",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 102,
                        "name" => "Israel",
                        "code" => "IL",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 103,
                        "name" => "Italy",
                        "code" => "IT",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 104,
                        "name" => "Jamaica",
                        "code" => "JM",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 105,
                        "name" => "Japan",
                        "code" => "JP",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 106,
                        "name" => "Jordan",
                        "code" => "JO",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 107,
                        "name" => "Kazakhstan",
                        "code" => "KZ",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 108,
                        "name" => "Kenya",
                        "code" => "KE",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 109,
                        "name" => "Kiribati",
                        "code" => "KI",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 110,
                        "name" => "North Korea",
                        "code" => "KP",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 111,
                        "name" => "South Korea",
                        "code" => "KR",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 112,
                        "name" => "Kuwait",
                        "code" => "KW",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 113,
                        "name" => "Kyrgyzstan",
                        "code" => "KG",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 114,
                        "name" => "Lao People's Democratic Republic",
                        "code" => "LA",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 115,
                        "name" => "Latvia",
                        "code" => "LV",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 116,
                        "name" => "Lebanon",
                        "code" => "LB",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 117,
                        "name" => "Lesotho",
                        "code" => "LS",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 118,
                        "name" => "Liberia",
                        "code" => "LR",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 119,
                        "name" => "Libyan Arab Jamahiriya",
                        "code" => "LY",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 120,
                        "name" => "Liechtenstein",
                        "code" => "LI",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 121,
                        "name" => "Lithuania",
                        "code" => "LT",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 122,
                        "name" => "Luxembourg",
                        "code" => "LU",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 123,
                        "name" => "FYROM",
                        "code" => "MK",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 124,
                        "name" => "Madagascar",
                        "code" => "MG",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 125,
                        "name" => "Malawi",
                        "code" => "MW",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 126,
                        "name" => "Malaysia",
                        "code" => "MY",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 127,
                        "name" => "Maldives",
                        "code" => "MV",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 128,
                        "name" => "Mali",
                        "code" => "ML",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 129,
                        "name" => "Malta",
                        "code" => "MT",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 130,
                        "name" => "Marshall Islands",
                        "code" => "MH",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 131,
                        "name" => "Martinique",
                        "code" => "MQ",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 132,
                        "name" => "Mauritania",
                        "code" => "MR",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 133,
                        "name" => "Mauritius",
                        "code" => "MU",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 134,
                        "name" => "Mayotte",
                        "code" => "YT",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 135,
                        "name" => "Mexico",
                        "code" => "MX",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 136,
                        "name" => "Micronesia, Federated States of",
                        "code" => "FM",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 137,
                        "name" => "Moldova, Republic of",
                        "code" => "MD",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 138,
                        "name" => "Monaco",
                        "code" => "MC",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 139,
                        "name" => "Mongolia",
                        "code" => "MN",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 140,
                        "name" => "Montserrat",
                        "code" => "MS",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 141,
                        "name" => "Morocco",
                        "code" => "MA",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 142,
                        "name" => "Mozambique",
                        "code" => "MZ",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 143,
                        "name" => "Myanmar",
                        "code" => "MM",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 144,
                        "name" => "Namibia",
                        "code" => "NA",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 145,
                        "name" => "Nauru",
                        "code" => "NR",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 146,
                        "name" => "Nepal",
                        "code" => "NP",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 147,
                        "name" => "Netherlands",
                        "code" => "NL",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 148,
                        "name" => "Netherlands Antilles",
                        "code" => "AN",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 149,
                        "name" => "New Caledonia",
                        "code" => "NC",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 150,
                        "name" => "New Zealand",
                        "code" => "NZ",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 151,
                        "name" => "Nicaragua",
                        "code" => "NI",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 152,
                        "name" => "Niger",
                        "code" => "NE",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 153,
                        "name" => "Nigeria",
                        "code" => "NG",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 154,
                        "name" => "Niue",
                        "code" => "NU",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 155,
                        "name" => "Norfolk Island",
                        "code" => "NF",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 156,
                        "name" => "Northern Mariana Islands",
                        "code" => "MP",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 157,
                        "name" => "Norway",
                        "code" => "NO",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 158,
                        "name" => "Oman",
                        "code" => "OM",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 159,
                        "name" => "Pakistan",
                        "code" => "PK",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 160,
                        "name" => "Palau",
                        "code" => "PW",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 161,
                        "name" => "Panama",
                        "code" => "PA",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 162,
                        "name" => "Papua New Guinea",
                        "code" => "PG",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 163,
                        "name" => "Paraguay",
                        "code" => "PY",
                        "continent" => "SA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 164,
                        "name" => "Peru",
                        "code" => "PE",
                        "continent" => "SA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 165,
                        "name" => "Philippines",
                        "code" => "PH",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 166,
                        "name" => "Pitcairn",
                        "code" => "PN",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 167,
                        "name" => "Poland",
                        "code" => "PL",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 168,
                        "name" => "Portugal",
                        "code" => "PT",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 169,
                        "name" => "Puerto Rico",
                        "code" => "PR",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 170,
                        "name" => "Qatar",
                        "code" => "QA",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 171,
                        "name" => "Reunion",
                        "code" => "RE",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 172,
                        "name" => "Romania",
                        "code" => "RO",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 173,
                        "name" => "Russian Federation",
                        "code" => "RU",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 174,
                        "name" => "Rwanda",
                        "code" => "RW",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 175,
                        "name" => "Saint Kitts and Nevis",
                        "code" => "KN",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 176,
                        "name" => "Saint Lucia",
                        "code" => "LC",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 177,
                        "name" => "Saint Vincent and the Grenadines",
                        "code" => "VC",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 178,
                        "name" => "Samoa",
                        "code" => "WS",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 179,
                        "name" => "San Marino",
                        "code" => "SM",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 180,
                        "name" => "Sao Tome and Principe",
                        "code" => "ST",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 181,
                        "name" => "Saudi Arabia",
                        "code" => "SA",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 182,
                        "name" => "Senegal",
                        "code" => "SN",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 183,
                        "name" => "Seychelles",
                        "code" => "SC",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 184,
                        "name" => "Sierra Leone",
                        "code" => "SL",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 185,
                        "name" => "Singapore",
                        "code" => "SG",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 186,
                        "name" => "Slovak Republic",
                        "code" => "SK",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 187,
                        "name" => "Slovenia",
                        "code" => "SI",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 188,
                        "name" => "Solomon Islands",
                        "code" => "SB",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 189,
                        "name" => "Somalia",
                        "code" => "SO",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 190,
                        "name" => "South Africa",
                        "code" => "ZA",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 191,
                        "name" => "South Georgia &amp; South Sandwich Islands",
                        "code" => "GS",
                        "continent" => "SA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 192,
                        "name" => "Spain",
                        "code" => "ES",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 193,
                        "name" => "Sri Lanka",
                        "code" => "LK",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 194,
                        "name" => "St. Helena",
                        "code" => "SH",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 195,
                        "name" => "St. Pierre and Miquelon",
                        "code" => "PM",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 196,
                        "name" => "Sudan",
                        "code" => "SD",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 197,
                        "name" => "Suriname",
                        "code" => "SR",
                        "continent" => "SA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 198,
                        "name" => "Svalbard and Jan Mayen Islands",
                        "code" => "SJ",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 199,
                        "name" => "Swaziland",
                        "code" => "SZ",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 200,
                        "name" => "Sweden",
                        "code" => "SE",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 201,
                        "name" => "Switzerland",
                        "code" => "CH",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 202,
                        "name" => "Syrian Arab Republic",
                        "code" => "SY",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 203,
                        "name" => "Tajikistan",
                        "code" => "TJ",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 204,
                        "name" => "Tanzania, United Republic of",
                        "code" => "TZ",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 205,
                        "name" => "Thailand",
                        "code" => "TH",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 206,
                        "name" => "Togo",
                        "code" => "TG",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 207,
                        "name" => "Tokelau",
                        "code" => "TK",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 208,
                        "name" => "Tonga",
                        "code" => "TO",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 209,
                        "name" => "Trinidad and Tobago",
                        "code" => "TT",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 210,
                        "name" => "Tunisia",
                        "code" => "TN",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 211,
                        "name" => "Turkey",
                        "code" => "TR",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 212,
                        "name" => "Turkmenistan",
                        "code" => "TM",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 213,
                        "name" => "Turks and Caicos Islands",
                        "code" => "TC",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 214,
                        "name" => "Tuvalu",
                        "code" => "TV",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 215,
                        "name" => "Uganda",
                        "code" => "UG",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 216,
                        "name" => "Ukraine",
                        "code" => "UA",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 217,
                        "name" => "United Arab Emirates",
                        "code" => "AE",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 218,
                        "name" => "United Kingdom",
                        "code" => "GB",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 219,
                        "name" => "United States",
                        "code" => "US",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 220,
                        "name" => "United States Minor Outlying Islands",
                        "code" => "UM",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 221,
                        "name" => "Uruguay",
                        "code" => "UY",
                        "continent" => "SA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 222,
                        "name" => "Uzbekistan",
                        "code" => "UZ",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 223,
                        "name" => "Vanuatu",
                        "code" => "VU",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 224,
                        "name" => "Vatican City State (Holy See)",
                        "code" => "VA",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 225,
                        "name" => "Venezuela",
                        "code" => "VE",
                        "continent" => "SA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 226,
                        "name" => "Viet Nam",
                        "code" => "VN",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 227,
                        "name" => "Virgin Islands (British)",
                        "code" => "VG",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 228,
                        "name" => "Virgin Islands (U.S.)",
                        "code" => "VI",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 229,
                        "name" => "Wallis and Futuna Islands",
                        "code" => "WF",
                        "continent" => "OA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 230,
                        "name" => "Western Sahara",
                        "code" => "EH",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 231,
                        "name" => "Yemen",
                        "code" => "YE",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1732173279,
                    ],
                    [
                        "id" => 232,
                        "name" => "Democratic Republic of Congo",
                        "code" => "CD",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 233,
                        "name" => "Zambia",
                        "code" => "ZM",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 234,
                        "name" => "Zimbabwe",
                        "code" => "ZW",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 235,
                        "name" => "Montenegro",
                        "code" => "ME",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 236,
                        "name" => "Serbia",
                        "code" => "RS",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 237,
                        "name" => "Aaland Islands",
                        "code" => "AX",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 238,
                        "name" => "Bonaire, Sint Eustatius and Saba",
                        "code" => "BQ",
                        "continent" => "SA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 239,
                        "name" => "Curacao",
                        "code" => "CW",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 240,
                        "name" => "Palestinian Territory, Occupied",
                        "code" => "PS",
                        "continent" => "AS",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 241,
                        "name" => "South Sudan",
                        "code" => "SS",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 242,
                        "name" => "St. Barthelemy",
                        "code" => "BL",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 243,
                        "name" => "St. Martin (French part)",
                        "code" => "MF",
                        "continent" => "NA",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 244,
                        "name" => "Canary Islands",
                        "code" => "IC",
                        "continent" => "AF",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 245,
                        "name" => "Ascension Island (British)",
                        "code" => "AC",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 246,
                        "name" => "Kosovo, Republic of",
                        "code" => "XK",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 247,
                        "name" => "Isle of Man",
                        "code" => "IM",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 248,
                        "name" => "Tristan da Cunha",
                        "code" => "TA",
                        "continent" => "NULL",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 249,
                        "name" => "Guernsey",
                        "code" => "GG",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                    [
                        "id" => 250,
                        "name" => "Jersey",
                        "code" => "JE",
                        "continent" => "EU",
                        "sort_order" => 100,
                        "status" => 1,
                        "add_time" => 1704038400,
                        "update_time" => 1704038400,
                    ],
                ];
                $r = Db::name('country')->insertAll($saveData);
                if ($r !== false) {
                    tpSetting('syn', ['admin_logic_1735200508'=>1], 'cn');
                }
            }
        }
    }

    // 升级v1.7.0版本要处理的数据
    private function eyou_v170_handle_data()
    {
        $this->syn_handle_twofactor_tpl();
    }

    /**
     * 同步双因子登录的模板
     * @return [type] [description]
     */
    private function syn_handle_twofactor_tpl()
    {
        $syn_admin_logic_1727423349 = tpSetting('syn.syn_admin_logic_1727423349', [], 'cn');
        if (empty($syn_admin_logic_1727423349)) {
            try{
                $r = true;
                Db::name('sms_template')->where(['send_scene'=>30])->delete();
                $saveData = Db::name('sms_template')->field('tpl_id', true)->where(['send_scene'=>2])->select();
                if (!empty($saveData)) {
                    $addData = [];
                    foreach ($saveData as $key => $val) {
                        $val['tpl_title'] = '后台登录';
                        $val['send_scene'] = 30;
                        // $val['sms_sign'] = '';
                        // $val['sms_tpl_code'] = '';
                        // if (2 == $val['sms_type']) {
                        //     $val['tpl_content'] = '验证码为 {1} ，请在30分钟内输入验证。';
                        // } else {
                        //     $val['tpl_content'] = '验证码为 ${content} ，请在30分钟内输入验证。';
                        // }
                        $val['is_open'] = 1;
                        $addData[] = $val;
                    }
                    $r = Db::name('sms_template')->insertAll($addData);
                }
                if ($r !== false) {
                    tpSetting('syn', ['syn_admin_logic_1727423349'=>1], 'cn');
                }
            }catch(\Exception $e){}
        }

        $syn_admin_logic_1727423350 = tpSetting('syn.syn_admin_logic_1727423350', [], 'cn');
        if (empty($syn_admin_logic_1727423350)) {
            try{
                $r = true;
                Db::name('smtp_tpl')->where(['send_scene'=>30])->delete();
                $saveData = Db::name('smtp_tpl')->field('tpl_id', true)->where(['send_scene'=>2])->select();
                if (!empty($saveData)) {
                    $addData = [];
                    foreach ($saveData as $key => $val) {
                        $val['tpl_name'] = '后台登录';
                        $val['tpl_title'] = '后台登录验证码，请查收！';
                        $val['send_scene'] = 30;
                        $val['is_open'] = 1;
                        $addData[] = $val;
                    }
                    $r = Db::name('smtp_tpl')->insertAll($addData);
                }
                if ($r !== false) {
                    tpSetting('syn', ['syn_admin_logic_1727423350'=>1], 'cn');
                }
            }catch(\Exception $e){}
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

    // 升级v1.6.7版本要处理的数据
    private function eyou_v167_handle_data()
    {
        $Prefix = config('database.prefix');

        // 售后数据表加入原路退回功能需要的字段
        if (config('database.type') == 'dm') { // 达梦优化
            $serviceTableInfo = Db::query("SELECT COLUMN_NAME,DATA_TYPE,DATA_DEFAULT,NULLABLE FROM ALL_TAB_COLS WHERE TABLE_NAME = '{$Prefix}shop_order_service'");
            $serviceTableInfo = get_arr_column($serviceTableInfo, 'COLUMN_NAME');
            if (!empty($serviceTableInfo) && !in_array('refund_way', $serviceTableInfo)) {
                $sql = "ALTER TABLE `{$Prefix}shop_order_service` ADD COLUMN `refund_way` TINYINT NOT NULL DEFAULT 0;";
                if (@Db::execute($sql)) {
                    $sql = "comment ON COLUMN `{$Prefix}shop_order_service`.`refund_way` IS '退款方式(1:退款到余额; 2:线下退款; 3:原路退回(微信))';";
                    @Db::execute($sql);
                }
            }
        } elseif (config('database.type') == 'kingbase') { // 人大金仓优化
            $serviceTableInfo = Db::query("SELECT column_name FROM information_schema.columns WHERE table_name = '{$Prefix}shop_order_service' AND table_schema = 'public'");
            $serviceTableInfo = get_arr_column($serviceTableInfo, 'column_name');
            if (!empty($serviceTableInfo) && !in_array('refund_way', $serviceTableInfo)) {
                $sql = "ALTER TABLE \"{$Prefix}shop_order_service\" ADD COLUMN \"refund_way\" SMALLINT NOT NULL DEFAULT 0";
                @Db::execute($sql);
                $sql = "COMMENT ON COLUMN \"{$Prefix}shop_order_service\".\"refund_way\" IS '退款方式(1:退款到余额; 2:线下退款; 3:原路退回(微信))'";
                @Db::execute($sql);
            }
        } else {
            $serviceTableInfo = Db::query("SHOW COLUMNS FROM {$Prefix}shop_order_service");
            $serviceTableInfo = get_arr_column($serviceTableInfo, 'Field');
            if (!empty($serviceTableInfo) && !in_array('refund_way', $serviceTableInfo)) {
                $sql = "ALTER TABLE `{$Prefix}shop_order_service` ADD COLUMN `refund_way`  tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '退款方式(1:退款到余额; 2:线下退款; 3:原路退回(微信))' AFTER `refund_note`;";
                @Db::execute($sql);
            }
        }
        schemaTable('shop_order_service');

        // 修复外贸助手的文案错误
        $syn_admin_logic_1719815413 = tpSetting('syn.syn_admin_logic_1719815413', [], 'cn');
        if (empty($syn_admin_logic_1719815413)) {
            try {
                Db::name('foreign_pack')->where(['name'=>'users7','value'=>'%已存在！'])->update(['value'=>'%s已存在！']);
                Db::name('foreign_pack')->where(['name'=>'users7','value'=>'% already exists!'])->update(['value'=>'%s already exists!']);
                tpSetting('syn', ['syn_admin_logic_1719815413' => 1], 'cn');
            } catch (\Exception $e) {
            }
        }

        // 修复消息推送的多余数据
        $syn_admin_logic_1721179406 = tpSetting('syn.syn_admin_logic_1721179406', [], 'cn');
        if (empty($syn_admin_logic_1721179406)) {
            try {
                $tpl_ids = [];
                $markList = Db::name('language')->field('mark')->getAllWithIndex('mark');
                $result = Db::name('wechat_template')->order('send_scene asc, lang asc, tpl_id asc')->select();
                $result = group_same_key($result, 'send_scene');
                foreach ($result as $key => $val) {
                    $mark_arr = $markList;
                    foreach ($val as $_k => $_v) {
                        if (isset($mark_arr[$_v['lang']])) {
                            $tpl_ids[] = $_v['tpl_id'];
                            unset($mark_arr[$_v['lang']]);
                        }
                        if (empty($mark_arr)) {
                            break;
                        }
                    }
                }
                if (!empty($tpl_ids)) {
                    Db::name('wechat_template')->where(['tpl_id'=>['NOTIN', $tpl_ids]])->delete();
                }
                Db::name('wechat_template')->where(['send_scene'=>1])->update(['tpl_title'=>'新表单']);
                Db::name('wechat_template')->where(['send_scene'=>9])->update(['tpl_title'=>'新订单']);
                
                tpSetting('syn', ['syn_admin_logic_1721179406' => 1], 'cn');
            } catch (\Exception $e) {
            }
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
