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
use think\Db;
use think\Cache;
use think\Request;
use think\Page;

class System extends Base
{
    // 选项卡是否显示
    public $tabase = '';
    
    public function _initialize() {
        parent::_initialize();
        $this->tabase = input('param.tabase/d');
    }

    public function index()
    {
        $this->redirect(url('System/web'));
    }

    /**
     * 网站设置
     */
    public function web()
    {
        $inc_type =  'web';

        if (IS_POST) {
            $param = input('post.');
            $param['web_keywords'] = str_replace('，', ',', $param['web_keywords']);
            $param['web_description'] = filter_line_return($param['web_description']);
            
            if (1 == $param['web_status']) {
                /*多语言 - 不知为何v1.4.0新增该逻辑【在静态模式下，关闭网站会自动切换为动态URL模式】*/
                // $seo_pseudo = tpCache('seo.seo_pseudo');
                // if (2 == $seo_pseudo) {
                //     if (is_language()) {
                //         $langRow = \think\Db::name('language')->order('id asc')
                //             ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                //             ->select();
                //         foreach ($langRow as $key => $val) {
                //             tpCache('seo',['seo_pseudo'=>1],$val['mark']);
                //         }
                //     } else {
                //         tpCache('seo',['seo_pseudo'=>1]);
                //     }
                // }
                /*--end*/
                @unlink('./index.html');
            }

            // 网站根网址
            $web_basehost = rtrim($param['web_basehost'], '/');
            if (!is_http_url($web_basehost) && !empty($web_basehost)) {
                $web_basehost = $this->request->scheme().'://'.$web_basehost;
            }
            $param['web_basehost'] = $web_basehost;

            // 网站logo
            $web_logo_is_remote = !empty($param['web_logo_is_remote']) ? $param['web_logo_is_remote'] : 0;
            $web_logo = '';
            if ($web_logo_is_remote == 1) {
                $web_logo = $param['web_logo_remote'];
            } else {
                $web_logo = $param['web_logo_local'];
            }
            $param['web_logo'] = $web_logo;
            unset($param['web_logo_is_remote']);
            unset($param['web_logo_remote']);
            unset($param['web_logo_local']);

            // 浏览器地址图标
            if (!empty($param['web_ico']) && !is_http_url($param['web_ico'])) {
                $source = realpath(preg_replace('#^'.$this->root_dir.'/#i', '', $param['web_ico']));
                $destination = realpath('favicon.ico');
                if (empty($destination) || $source == $destination) {
                    unset($param['web_ico']);
                } else {
                    /*修复copy一句话图片木马漏洞*/
                    $image_ext = config('global.image_ext');
                    $image_ext_arr = explode(',', $image_ext);
                    $source_ext = pathinfo($source, PATHINFO_EXTENSION);
                    if (!empty($source_ext) && !in_array($source_ext, $image_ext_arr)) {
                        $this->error('地址栏图标必须是ico扩展名的图片');
                    }
                    /*end*/
                    if (file_exists($source) && @copy($source, $destination)) {
                        $param['web_ico'] = $this->root_dir.'/favicon.ico';
                    }
                }
            }

            tpCache($inc_type, $param);
            write_global_params($this->admin_lang); // 写入全局内置参数
            $this->success('操作成功', url('System/web'));
            exit;
        }

        $config = tpCache($inc_type);
        // 网站logo
        if (is_http_url($config['web_logo'])) {
            $config['web_logo_is_remote'] = 1;
            $config['web_logo_remote'] = handle_subdir_pic($config['web_logo']);
        } else {
            $config['web_logo_is_remote'] = 0;
            $config['web_logo_local'] = handle_subdir_pic($config['web_logo']);
        }

        $config['web_ico'] = preg_replace('#^(/[/\w]+)?(/)#i', $this->root_dir.'$2', $config['web_ico']); // 支持子目录
        
        /*系统模式*/
        $web_cmsmode = isset($config['web_cmsmode']) ? $config['web_cmsmode'] : 2;
        $this->assign('web_cmsmode', $web_cmsmode);
        /*--end*/

        /*自定义变量*/
        $eyou_row = Db::name('config_attribute')->field('a.attr_id, a.attr_name, a.attr_var_name, a.attr_input_type, b.value, b.id, b.name')
            ->alias('a')
            ->join('__CONFIG__ b', 'b.name = a.attr_var_name AND b.lang = a.lang', 'LEFT')
            ->where([
                'b.lang'    => $this->admin_lang,
                'a.inc_type'    => $inc_type,
                'b.is_del'  => 0,
            ])
            ->order('a.attr_id asc')
            ->select();
        foreach ($eyou_row as $key => $val) {
            $val['value'] = handle_subdir_pic($val['value'], 'html'); // 支持子目录
            $val['value'] = handle_subdir_pic($val['value']); // 支持子目录
            $eyou_row[$key] = $val;
        }
        $this->assign('eyou_row',$eyou_row);
        /*--end*/

        /*网站关键词*/
        // if ($config['web_keywords']) {
        //     if (unserialize($config['web_keywords'])) {
        //         $config['web_keywords'] = implode(",", unserialize($config['web_keywords']));
        //         tpCache('web',['web_keywords'=>$config['web_keywords']]);
        //     }
        // }

        $this->assign('config',$config);//当前配置项
        $this->assign('seo_pseudo', tpCache('seo.seo_pseudo')); // URL模式
        return $this->fetch();
    }

    /**
     * 核心设置
     */
    public function web2()
    {
        $this->language_access(); // 多语言功能操作权限

        $inc_type = 'web';

        if (IS_POST) {
            $param = input('post.');

            if (1 == $param['web_mobile_domain_open']) {
                $web_mobile_domain = trim($param['web_mobile_domain']);
                if (!empty($web_mobile_domain) && ($web_mobile_domain == 'www' || $web_mobile_domain == $this->request->subDomain())) {
                    $this->error("手机站域名配置不能与主站域名一致！");
                }
            } else {
                unset($param['web_mobile_domain']);
            }
            /*EyouCMS安装目录*/
            empty($param['web_cmspath']) && $param['web_cmspath'] = $this->root_dir; // 支持子目录
            $web_cmspath = trim($param['web_cmspath'], '/');
            $web_cmspath = !empty($web_cmspath) ? '/'.$web_cmspath : '';
            $param['web_cmspath'] = $web_cmspath;
            /*自定义后台路径名*/
            $adminbasefile = trim($param['adminbasefile']).'.php'; // 新的文件名
            $param['web_adminbasefile'] = $this->root_dir.'/'.$adminbasefile; // 支持子目录
            $adminbasefile_old = trim($param['adminbasefile_old']).'.php'; // 旧的文件名
            unset($param['adminbasefile']);
            unset($param['adminbasefile_old']);
            if ('index.php' == $adminbasefile) {
                $this->error("后台路径禁止使用index", null, '', 1);
            }
            /*--end*/
            $param['web_sqldatapath'] = '/'.trim($param['web_sqldatapath'], '/'); // 数据库备份目录
            $param['web_htmlcache_expires_in'] = intval($param['web_htmlcache_expires_in']); // 页面缓存有效期
            /*后台登录超时*/
            $web_login_expiretime = $param['web_login_expiretime'];
            $web_login_expiretime = preg_replace('/^(\d{0,})(.*)$/i', '${1}', $web_login_expiretime);
            empty($web_login_expiretime) && $web_login_expiretime = config('login_expire');
            if ($web_login_expiretime > 2592000) {
                $web_login_expiretime = 2592000; // 最多一个月
            }
            $param['web_login_expiretime'] = $web_login_expiretime;
            /*--end*/
            
            /*前台模板风格*/
            $web_tpl_theme = $param['web_tpl_theme'];
            $web_tpl_theme_old = tpCache('web.web_tpl_theme');
            /*--end*/

            tpSetting('recycle', ['recycle_switch' => $param['recycle_switch']]);//回收站开关

            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache($inc_type,$param,$val['mark']);
                    write_global_params($val['mark']); // 写入全局内置参数
                }
            } else {
                tpCache($inc_type,$param);
                write_global_params($this->admin_lang); // 写入全局内置参数
            }
            /*--end*/

            /*更改session会员设置 - session有效期（后台登录超时）*/
            $session_conf = [];
            $session_file = APP_PATH.'admin/conf/session_conf.php';
            if (file_exists($session_file)) {
                require_once($session_file);
                $session_conf_tmp = EY_SESSION_CONF;
                if (!empty($session_conf_tmp)) {
                    $session_conf_tmp = json_decode($session_conf_tmp, true);
                    if (!empty($session_conf_tmp) && is_array($session_conf_tmp)) {
                        $session_conf = $session_conf_tmp;
                    }
                }
            }
            $session_conf['expire'] = $param['web_login_expiretime'];
            $str_session_conf = '<?php'.PHP_EOL.'$session_1600593464 = json_encode('.var_export($session_conf,true).');'.PHP_EOL.'define(\'EY_SESSION_CONF\', $session_1600593464);';
            @file_put_contents(APP_PATH . 'admin/conf/session_conf.php', $str_session_conf);
            /*--end*/

            $refresh = false;
            $gourl = request()->domain().$this->root_dir.'/'.$adminbasefile; // 支持子目录
            /*更改自定义后台路径名*/
            if ($adminbasefile_old != $adminbasefile && eyPreventShell($adminbasefile_old)) {
                if (file_exists($adminbasefile_old)) {
                    if(rename($adminbasefile_old, $adminbasefile)) {
                        $refresh = true;
                    }
                } else {
                    $this->error("根目录{$adminbasefile_old}文件不存在！", null, '', 2);
                }
            }
            /*--end*/

            if ($web_tpl_theme != $web_tpl_theme_old) {
                delFile(rtrim(RUNTIME_PATH, '/'), true);
            }
            
            /*刷新整个后台*/
            if ($refresh) {
                $this->success('操作成功', $gourl, '', 1, [], '_parent');
            }
            /*--end*/

            $this->success('操作成功', url('System/web2'));
        }

        $config = tpCache($inc_type);
        // 当前主域名
        $this->assign('subDomain', $this->request->subDomain());
        //自定义后台路径名
        $baseFile = explode('/', $this->request->baseFile());
        $web_adminbasefile = end($baseFile);
        $adminbasefile = preg_replace('/^(.*)\.([^\.]+)$/i', '$1', $web_adminbasefile);
        $this->assign('adminbasefile', $adminbasefile);
        // 数据库备份目录
        $sqlbackuppath = config('DATA_BACKUP_PATH');
        $this->assign('sqlbackuppath', $sqlbackuppath);
        // 当前域名是否IP或者localhost本地
        $is_localhost = 0;
        if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/i', $this->request->host(true)) || 'localhost' == $this->request->host(true)) {
            $is_localhost = 1;
        }
        $this->assign('is_localhost',$is_localhost);

        /*模板风格列表*/
        $tpl_theme_list = glob('./template/*', GLOB_ONLYDIR);
        foreach ($tpl_theme_list as $key => &$val) {
            $val = str_replace('\\', '/', $val);
            $val = preg_replace('/^(.*)\/([^\/]*)$/i', '${2}', $val);
        }
        $this->assign('tpl_theme_list', $tpl_theme_list);

        $show_uiset = '';
        $web_tpl_theme = !empty($config['web_tpl_theme']) ? $config['web_tpl_theme'].DS : '';
        if (file_exists(ROOT_PATH.'template'.DS.$web_tpl_theme.'pc'.DS.'uiset.txt') && file_exists(ROOT_PATH.'template'.DS.$web_tpl_theme.'mobile'.DS.'uiset.txt')) {
            $show_uiset = 'pc+mobile';
        } else if (file_exists(ROOT_PATH.'template'.DS.$web_tpl_theme.'pc'.DS.'uiset.txt')) {
            $show_uiset = 'pc';
        } else if (file_exists(ROOT_PATH.'template'.DS.$web_tpl_theme.'mobile'.DS.'uiset.txt')) {
            $show_uiset = 'mobile';
        }
        $this->assign('show_uiset', $show_uiset);
        /*end*/

        /*代理贴牌功能限制-s*/
        $upgrade = true;
        if (function_exists('checkAuthRule')) {
            //系统升级
            $upgrade = checkAuthRule('upgrade');
        }
        $this->assign('upgrade', $upgrade);
        /*代理贴牌功能限制-e*/
        
        $this->assign('config',$config);//当前配置项
        $recycle_switch = tpSetting('recycle.recycle_switch');
        $this->assign('recycle_switch', $recycle_switch);//回收站

        return $this->fetch();
    }

    /**
     * 附件设置
     */
    public function basic()
    {
        $inc_type =  'basic';
        $other_inc_type = 'other';

        // 文件上传最大限制
        $maxFileupload = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 0;
        if (0 !== $maxFileupload) {
            $max_filesize = unformat_bytes($maxFileupload);
            $max_filesize = $max_filesize / 1024 / 1024; // 单位是MB的大小
        } else {
            $max_filesize = 500;
        }
        $max_sizeunit = 'MB';
        $maxFileupload = $max_filesize.$max_sizeunit;

        if (IS_POST) {
            $param = input('post.');

            // 文档默认浏览量
            if (isset($param['other_arcclick']) && 0 <= $param['other_arcclick']) {
                $arcclick_arr = explode("|", $param['other_arcclick']);
                if (count($arcclick_arr) > 1) {
                    foreach ($arcclick_arr as $k => $v) {
                        $arcclick_arr[$k] = intval($v);
                        if ($k > 1) {
                            unset($arcclick_arr[$k]);
                        }
                    }
                    asort($arcclick_arr);
                    $other_param['other_arcclick'] = implode('|', $arcclick_arr);
                } else {
                    $other_param['other_arcclick'] = intval($arcclick_arr[0]);
                }
            }else{
                $other_param['other_arcclick'] = '500|1000';
            }
            unset($param['other_arcclick']);

            // 软件默认下载量
            if (isset($param['other_arcdownload']) && 0 <= $param['other_arcdownload']) {
                $arcdownload_arr = explode("|", $param['other_arcdownload']);
                if (count($arcdownload_arr) > 1) {
                    foreach ($arcdownload_arr as $k => $v) {
                        $arcdownload_arr[$k] = intval($v);
                        if ($k > 1) {
                            unset($arcdownload_arr[$k]);
                        }
                    }
                    asort($arcdownload_arr);
                    $other_param['other_arcdownload'] = implode('|', $arcdownload_arr);
                } else {
                    $other_param['other_arcdownload'] = intval($arcdownload_arr[0]);
                }
            }else{
                $other_param['other_arcdownload'] = '500|1000';
            }
            unset($param['other_arcdownload']);

            $old_basic_img_style_wh = $param['old_basic_img_style_wh'];
            unset($param['old_basic_img_style_wh']);

            $param['file_size'] = intval($param['file_size']);
            if (0 < $max_filesize && $max_filesize < $param['file_size']) {
                $this->error("附件上传大小超过空间的最大限制".$maxFileupload);
            }
            // 允许上传图片类型
            $image_ext = config('global.image_ext');
            $image_ext_arr = explode(',', $image_ext);
            $image_type = explode('|', $param['image_type']);
            foreach ($image_type as $key => $val) {
                $val = trim($val);
                if (!in_array($val, $image_ext_arr) || empty($val)) {
                    unset($image_type[$key]);
                }
            }
            $param['image_type'] = implode('|', $image_type);

            // 允许上传软件类型，类型太多无法进行白名单处理
            $file_type = explode('|', $param['file_type']);
            foreach ($file_type as $key => $val) {
                $val = trim($val);
                if (preg_match('/^(php|asp|jsp|perl|cgi|asa|pht|phtml)/i', $val) || empty($val)) {
                    unset($file_type[$key]);
                }
            }
            $param['file_type'] = implode('|', $file_type);

            // 允许多媒体类型
            $media_ext = config('global.media_ext');
            $media_ext_arr = explode(',', $media_ext);
            $media_type = explode('|', $param['media_type']);
            foreach ($media_type as $key => $val) {
                $val = trim($val);
                if (!in_array($val, $media_ext_arr) || empty($val)) {
                    unset($media_type[$key]);
                }
            }
            $param['media_type'] = implode('|', $media_type);
            /*end*/

            /*多语言*/
            if (is_language()) {
                $newParam['basic_indexname'] = $param['basic_indexname'];
                tpCache($inc_type,$newParam);

                $synLangParam = $param; // 同步更新多语言的数据
                unset($synLangParam['basic_indexname']);
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache($inc_type, $synLangParam, $val['mark']);
                    if (!empty($other_param)) {
                        tpCache($other_inc_type, $other_param, $val['mark']);
                    }
                }
            } else {
                tpCache($inc_type,$param);
                if (!empty($other_param)) {
                    tpCache($other_inc_type, $other_param);
                }
            }
            /*--end*/

            if ($old_basic_img_style_wh != $param['basic_img_style_wh']) {
                // 清空详情页缓存
                foreach (['http','https'] as $key => $val) {
                    delFile(RUNTIME_PATH.'html/'.$val.'/view');
                }
            }

            // 编辑器选项配置
            if (!empty($param['editor_select'])) {
                tpSetting('editor', ['editor_select' => $param['editor_select']]);
            }

            $this->success('操作成功', url('System/basic'));
        }

        $config = tpCache($inc_type);
        $other_config = tpCache($other_inc_type);

        $this->assign('config', $config);
        $this->assign('other_config', $other_config);
        $this->assign('max_filesize', $max_filesize);
        $this->assign('max_sizeunit', $max_sizeunit);

        $editor = tpSetting('editor');
        $this->assign('editor', $editor);
        return $this->fetch();
    }

    /**
     * 图片水印
     */
    public function water()
    {
        $this->language_access(); // 多语言功能操作权限

        $inc_type =  'water';

        if (IS_POST) {
            $param = input('post.');
            $tabase = input('post.tabase/d');
            unset($param['tabase']);

            $mark_img_is_remote = !empty($param['mark_img_is_remote']) ? $param['mark_img_is_remote'] : 0;
            $mark_img = '';
            if ($mark_img_is_remote == 1) {
                $mark_img = $param['mark_img_remote'];
            } else {
                $mark_img = $param['mark_img_local'];
            }
            $waterPath = preg_replace('#^(/[/\w]+)?(/public/upload/|/uploads/)#i', '$2', $mark_img); 
            $waterImgInfo = @getimagesize('.'.$waterPath);
            $waterImgW = !empty($waterImgInfo[0]) ? $waterImgInfo[0] : 0;
            $waterImgH = !empty($waterImgInfo[1]) ? $waterImgInfo[1] : 0;
            if ($waterImgW > 2000 || $waterImgH > 2000) {
                $this->error('水印图片像素不能过大，否则无法对小图片进行水印！');
            }
            $param['mark_img'] = $mark_img;
            unset($param['mark_img_is_remote']);
            unset($param['mark_img_remote']);
            unset($param['mark_img_local']);

            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache($inc_type, $param, $val['mark']);
                }
            } else {
                tpCache($inc_type,$param);
            }
            /*--end*/
            $this->success('操作成功', url('System/'.$inc_type, ['tabase'=>$tabase]));
        }

        $config = tpCache($inc_type);
        if (is_http_url($config['mark_img'])) {
            $config['mark_img_is_remote'] = 1;
            $config['mark_img_remote'] = handle_subdir_pic($config['mark_img']);
        } else {
            $config['mark_img_is_remote'] = 0;
            $config['mark_img_local'] = handle_subdir_pic($config['mark_img']);
        }

        $this->assign('config',$config);//当前配置项
        return $this->fetch();
    }

    /**
     * 缩略图配置
     */
    public function thumb()
    {
        $this->language_access(); // 多语言功能操作权限

        $inc_type =  'thumb';

        if (IS_POST) {
            $param = input('post.');
            $tabase = input('post.tabase/d');
            unset($param['tabase']);
            isset($param['thumb_width']) && $param['thumb_width'] = preg_replace('/[^0-9]/', '', $param['thumb_width']);
            isset($param['thumb_height']) && $param['thumb_height'] = preg_replace('/[^0-9]/', '', $param['thumb_height']);

            $thumbConfig = tpCache('thumb'); // 旧数据

            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache($inc_type, $param, $val['mark']);
                }
            } else {
                tpCache($inc_type,$param);
            }
            /*--end*/

            /*校验配置是否改动，若改动将会清空缩略图目录*/
            unset($param['__token__']);
            if (md5(serialize($param)) != md5(serialize($thumbConfig))) {
                delFile(RUNTIME_PATH.'html'); // 清空缓存页面
                delFile(UPLOAD_PATH.'thumb'); // 清空缩略图
            }
            /*--end*/

            $this->success('操作成功', url('System/'.$inc_type, ['tabase'=>$tabase]));
        }

        $config = tpCache($inc_type);

        // 设置缩略图默认配置
        if (!isset($config['thumb_open'])) {
            /*多语言*/
            $thumbextra = config('global.thumb');
            $param = [
                'thumb_open'    => $thumbextra['open'],
                'thumb_mode'    => $thumbextra['mode'],
                'thumb_color'   => $thumbextra['color'],
                'thumb_width'   => $thumbextra['width'],
                'thumb_height'  => $thumbextra['height'],
            ];
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')->select();
                foreach ($langRow as $key => $val) {
                    tpCache($inc_type, $param, $val['mark']);
                }
            } else {
                tpCache($inc_type,$param);
            }
            $config = tpCache($inc_type);
            /*--end*/
        }

        $this->assign('config',$config);//当前配置项
        return $this->fetch();
    }

    // 所有API接口的配置
    public function api_conf()
    {
        /*会员中心总配置信息*/
        $userConfig = getUsersConfigData('all');
        $this->assign('userConfig', $userConfig);
        /* END */

        /*是否开启支付功能*/
        $this->assign('pay_open', $userConfig['pay_open']);
        /* END */

        /*微信支付配置*/
        $wechat = !empty($userConfig['pay_wechat_config']) ? unserialize($userConfig['pay_wechat_config']) : [];
        $this->assign('wechat', $wechat);
        /* END */

        /*支付宝支付配置*/
        $alipay = !empty($userConfig['pay_alipay_config']) ? unserialize($userConfig['pay_alipay_config']) : [];
        $this->assign('alipay', $alipay);
        if (version_compare(PHP_VERSION,'5.5.0','<')) {
            $php_version = 1; // PHP5.4.0或更低版本，可使用旧版支付方式
        } else {
            $php_version = 0;// PHP5.5.0或更高版本，可使用新版支付方式，兼容旧版支付方式
        }
        $this->assign('php_version',$php_version);
        /* END */

        /*微站点配置*/
        $login = !empty($userConfig['wechat_login_config']) ? unserialize($userConfig['wechat_login_config']) : [];
        $this->assign('login', $login);
        /* END */

        /*邮箱配置*/
        // $smtp = tpCache('smtp');
        // $this->assign('smtp', $smtp);
        /* END */

        /*手机短信配置*/
        // $sms = tpCache('sms');
        // if (!isset($sms['sms_type'])) {
        //     $sms['sms_type'] = 1;
        //     tpCache('sms',array('sms_type'=>1));
        // }
        // $this->assign('sms', $sms);
        /* END */

        /*阿里云OSS配置*/
        $oss = tpCache('oss');
        $this->assign('oss', $oss);
        /* END */

        /*余额支付开关*/
        $pay_balance_open = 1;
        if (!isset($userConfig['pay_balance_open'])) {
            getUsersConfigData('pay', ['pay_balance_open' => 1]);
        } else {
            $pay_balance_open = intval($userConfig['pay_balance_open']);
        }
        $this->assign('pay_balance_open', $pay_balance_open);
        /* END */

        /*支付接口*/
        $pay_api_list = Db::name('pay_api_config')->where('status', 1)->order('pay_id asc')->select();
        foreach ($pay_api_list as $key => $val) {
            if (1 == $val['system_built']) {
                $val['litpic'] = $this->root_dir . "/public/static/admin/images/{$val['pay_mark']}.png";
            } else {
                $val['litpic'] = $this->root_dir . "/weapp/{$val['pay_mark']}/logo.png";
            }
            $pay_api_list[$key] = $val;
        }
        $this->assign('pay_api_list', $pay_api_list);
        /* END */

        /*通知管理*/
        // action('admin/Notify/index');
        /* END */

        return $this->fetch();
    }

    /**
     * 邮件配置
     */
    public function smtp()
    {
        $inc_type =  'smtp';
        if (IS_POST) {
            $goback = input('param.goback/s');
            $param = input('post.');
            // $param['smtp_shop_order_pay'] = !empty($param['smtp_shop_order_pay']) ? 1 : 0;
            // $param['smtp_shop_order_send'] = !empty($param['smtp_shop_order_send']) ? 1 : 0;

            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache($inc_type, $param, $val['mark']);
                }
            } else {
                tpCache($inc_type, $param);
            }
            /*--end*/
            
            $this->success('操作成功', url('System/smtp', ['goback'=>$goback]));
        }

        $smtp = tpCache('smtp');
        $this->assign('smtp', $smtp);

        $goback = input('param.goback/s');
        $this->assign('goback', $goback);

        return $this->fetch();
    }

    /**
     * 邮件模板列表
     */
    public function smtp_tpl()
    {
        $list = array();
        $keywords = input('keywords/s');

        $map = array();
        if (!empty($keywords)) {
            $map['tpl_name'] = array('LIKE', "%{$keywords}%");
        }

        // 多语言
        $map['lang'] = array('eq', $this->admin_lang);

        $count = Db::name('smtp_tpl')->where($map)->count('tpl_id');// 查询满足要求的总记录数
        $pageObj = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = Db::name('smtp_tpl')->where($map)
            ->order('tpl_id asc')
            ->limit($pageObj->firstRow.','.$pageObj->listRows)
            ->select();
        $pageStr = $pageObj->show(); // 分页显示输出
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $pageStr); // 赋值分页输出
        $this->assign('pager', $pageObj); // 赋值分页对象
        
        // 是否填写邮件配置
        $is_conf = 1;
        $smtp_config = tpCache('smtp');
        if (empty($smtp_config['smtp_user']) || empty($smtp_config['smtp_pwd'])) {
            $is_conf = 0;
        }
        $this->assign('is_conf', $is_conf);

        $shop_open = getUsersConfigData('shop.shop_open');
        $this->assign('shop_open', $shop_open);

        return $this->fetch();
    }

    /**
     * 短信配置
     */
    public function sms()
    {
        $inc_type =  'sms';
        if (IS_POST) {
            $param = input('post.');

            if (!isset($param['sms_type'])) $param['sms_type'] = 1;
            if ($param['sms_type'] == 1) {
                unset($param['sms_appkey_tx']);
                unset($param['sms_appid_tx']);
            }else {
                unset($param['sms_appkey']);
                unset($param['sms_secretkey']);
            }

            // $param['sms_shop_order_pay'] = !empty($param['sms_shop_order_pay']) ? 1 : 0;
            // $param['sms_shop_order_send'] = !empty($param['sms_shop_order_send']) ? 1 : 0;

            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache($inc_type, $param, $val['mark']);
                }
            } else {
                tpCache($inc_type, $param);
            }
            /*--end*/

            $this->success('操作成功', url('System/sms'));
        }

        $sms = tpCache('sms');
        if (!isset($sms['sms_type'])) {
            $sms['sms_type'] = 1;
            tpCache('sms', array('sms_type'=>1));
        }
        $this->assign('sms', $sms);
        return $this->fetch();
    }

    /**
     * 短信模板列表
     */
    public function sms_tpl()
    {
        $param = input('param.');
        $list = array();
        $keywords = input('keywords/s');
        $sms_type = input('sms_type/d');
        if (empty($sms_type)) {
            $sms_type = tpCache('sms.sms_type');
            $sms_type = !empty($sms_type) ? $sms_type : 1;
        }

        $map = [
            'lang' => $this->admin_lang,
            'sms_type' => $sms_type,
        ];
        if (!empty($keywords)) $map['tpl_title'] = array('LIKE', "%{$keywords}%");

        $count = Db::name('sms_template')->where($map)->count('tpl_id');// 查询满足要求的总记录数
        $pageObj = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = Db::name('sms_template')->where($map)
            ->order('tpl_id asc')
            ->limit($pageObj->firstRow.','.$pageObj->listRows)
            ->select();
        $pageStr = $pageObj->show(); // 分页显示输出
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $pageStr); // 赋值分页输出
        $this->assign('pager', $pageObj); // 赋值分页对象
        $this->assign('sms_type', $sms_type); // 短信服务商

        // 是否填写手机短信配置
        $is_conf = 1;
        $sms_arr = [];
        $sms_config = tpCache('sms');
        foreach ($sms_config as $key => $val) {
            $sms_arr[$key] = $val;
        }
        foreach (['sms_appkey','sms_secretkey','sms_appkey_tx','sms_appid_tx'] as $key => $val) {
            if (2 == $sms_type) {
                if (preg_match('/^sms_(.*)_tx$/i', $val) && empty($sms_arr[$val])) {
                    $is_conf = 0;
                }
            } else {
                if (preg_match('/^sms_/i', $val) && empty($sms_arr[$val])) {
                    $is_conf = 0;
                }
            }
        }
        $this->assign('is_conf', $is_conf);

        $shop_open = getUsersConfigData('shop.shop_open');
        $this->assign('shop_open', $shop_open);

        return $this->fetch();
    }

    /**
     * 微站点配置
     */
    public function microsite()
    {
        if (IS_POST) {
            $post = input('post.');
            if (!empty($post)) {
                // 过滤左右多余空格
                foreach ($post as $key => $val) {
                    if (is_array($val)) {
                        foreach ($val as $_k => $_v) {
                            if (is_string($_v)) {
                                $post[$key][$_k] = trim($_v);
                            }
                        }
                    } else if (is_string($_v)) {
                        $post[$key] = trim($val);
                    }
                }

                $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$post['login']['appid']."&secret=".$post['login']['appsecret'];
                $response = httpRequest($url);
                $params = json_decode($response, true);
                if (!isset($params['access_token'])) {
                    $wechat_code = config('error_code.wechat');
                    $msg = !empty($wechat_code[$params['errcode']]) ? $wechat_code[$params['errcode']] : $params['errmsg'];
                    $this->error($msg);
                }

                if (1 == $post['shop']['shop_micro']) {
                    if (empty($post['login']['appid']) || empty($post['login']['appsecret'])) {
                        $post['shop']['shop_micro'] = 0;
                    }
                }
                if (1 == $post['shop']['shop_force_use_wechat']) {
                    if (empty($post['login']['appid']) || empty($post['login']['appsecret'])) {
                        $post['shop']['shop_force_use_wechat'] = 0;
                    } else {
                        $post['shop']['shop_micro'] = 1;
                    }
                }

                // 微信登录配置处理
                $post['wechat']['wechat_login_config'] = serialize($post['login']);
                unset($post['login']);

                foreach ($post as $key => $val) {
                    is_array($val) && getUsersConfigData($key, $val);
                }
                $this->success('设置成功！', url('Shop/conf'));
            }
        }
    }
    
    
    /**
     * 邮件模板列表 - 编辑
     */
    public function smtp_tpl_edit()
    {
        if (IS_POST) {
            $post = input('post.');
            $post['tpl_id'] = eyIntval($post['tpl_id']);
            if(!empty($post['tpl_id'])){
                $post['tpl_title'] = trim($post['tpl_title']);

                /*组装存储数据*/
                $nowData = array(
                    'update_time'   => getTime(),
                );
                $saveData = array_merge($post, $nowData);
                /*--end*/
                
                $r = Db::name('smtp_tpl')->where([
                        'tpl_id'    => $post['tpl_id'],
                        'lang'      => $this->admin_lang,
                    ])->update($saveData);
                if ($r) {
                    $tpl_name = Db::name('smtp_tpl')->where([
                            'tpl_id'    => $post['tpl_id'],
                            'lang'      => $this->admin_lang,
                        ])->getField('tpl_name');
                    adminLog('编辑邮件模板：'.$tpl_name); // 写入操作日志
                    $this->success("操作成功", url('System/smtp_tpl'));
                }
            }
            $this->error("操作失败");
        }

        $id = input('id/d', 0);
        $row = Db::name('smtp_tpl')->where([
                'tpl_id'    => $id,
                'lang'      => $this->admin_lang,
            ])->find();
        if (empty($row)) {
            $this->error('数据不存在，请联系管理员！');
            exit;
        }

        $this->assign('row',$row);
        return $this->fetch();
    }

    /**
     * 阿里云OSS配置
     */
    public function oss()
    {
        $inc_type =  'oss';
        if (IS_POST) {
            $param = input('post.');
            
            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache($inc_type, $param, $val['mark']);
                }
            } else {
                tpCache($inc_type, $param);
            }
            /*--end*/
            $this->success('操作成功');
        }
    }

    /**
     * 清空缓存
     */
    public function clear_cache()
    {
        if (IS_POST) {
            if (!function_exists('unlink')) {
                $this->error('php.ini未开启unlink函数，请联系空间商处理！');
            }

            $post = input('post.');

            if (!empty($post['clearall'])) { // 清除全部缓存
                $this->clearSystemCache();
            } else {
                if (!empty($post['clearHtml'])) { // 清除页面缓存
                    $this->clearHtmlCache($post['clearHtml']);
                }

                if (!empty($post['clearCache'])) { // 清除数据缓存
                    $this->clearSystemCache($post['clearCache']);
                }
            }

            // 清除其他临时文件
            $this->clearOtherCache();

            /*重新生成全部数据表字段缓存文件*/
            try {
                schemaAllTable();
            } catch (\Exception $e) {}
            /*--end*/

            /*清除旧升级备份包，保留最后一个备份文件*/
            $backupArr = glob(DATA_PATH.'backup/v*_www');
            for ($i=0; $i < count($backupArr) - 1; $i++) { 
                delFile($backupArr[$i], true);
            }

            $backupArr = glob(DATA_PATH.'backup/*');
            foreach ($backupArr as $key => $filepath) {
                if (file_exists($filepath) && !stristr($filepath, '.htaccess') && !stristr($filepath, '_www')) {
                    if (is_dir($filepath)) {
                        delFile($filepath, true);
                    } else if (is_file($filepath)) {
                        @unlink($filepath);
                    }
                }
            }
            /*--end*/

            $request = Request::instance();
            $gourl = $request->baseFile();
            $lang = $request->param('lang/s');
            if (!empty($lang) && $lang != get_main_lang()) {
                $gourl .= "?lang={$lang}";
            }
            $this->success('操作成功', $gourl, '', 1, [], '_parent');
        }
        
        return $this->fetch();
    }

    /**
     * 清空数据缓存
     */
    public function fastClearCache($arr = array())
    {
        $this->clearSystemCache();
        $script = "<script>parent.layer.msg('操作成功', {time:3000,icon: 1});window.location='".url('Index/welcome')."';</script>";
        echo $script;
    }

    /**
     * 清空数据缓存
     */
    public function clearSystemCache($arr = array())
    {
        if (empty($arr)) {
            delFile(rtrim(RUNTIME_PATH, '/'), true);
        } else {
            foreach ($arr as $key => $val) {
                delFile(RUNTIME_PATH.$val, true);
            }
        }

        /*多语言*/
        if (is_language()) {
            $langRow = Db::name('language')->order('id asc')
                ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                ->select();
            foreach ($langRow as $key => $val) {
                tpCache('global', '', $val['mark']);
            }
        } else { // 单语言
            tpCache('global');
        }
        /*--end*/

        return true;
    }

    /**
     * 清空页面缓存
     */
    public function clearHtmlCache($arr = array())
    {
        if (empty($arr)) {
            delFile(rtrim(HTML_ROOT, '/'), true);
        } else {
            $seo_pseudo = tpCache('seo.seo_pseudo');
            foreach ($arr as $key => $val) {
                $fileList = glob(HTML_ROOT.'http*/'.$val.'*');
                if (!empty($fileList)) {
                    foreach ($fileList as $k2 => $v2) {
                        if (file_exists($v2) && is_dir($v2)) {
                            delFile($v2, true);
                        } else if (file_exists($v2) && is_file($v2)) {
                            @unlink($v2);
                        }
                    }
                }
                if ($val == 'index' && 2 != $seo_pseudo) {
                    foreach (['index.html','indexs.html'] as $sk1 => $sv1) {
                        $filename = ROOT_PATH.$sv1;
                        if (file_exists($filename)) {
                            @unlink($filename);
                        }
                    }
                }
            }
        }
    }

    /**
     * 清除其他临时文件
     */
    private function clearOtherCache()
    {
        $arr = [
            'template',
        ];
        foreach ($arr as $key => $val) {
            delFile(RUNTIME_PATH.$val, true);
        }

        return true;
    }
      
    /**
     * 发送测试邮件
     */
    public function send_email()
    {
        $param = $smtp_config = input('post.');
        $title = '演示标题';
        $content = '演示一串随机数字：' . mt_rand(1000,9999);
        $res = send_email($param['smtp_from_eamil'], $title, $content, 0, $smtp_config);
        if (intval($res['code']) == 1) {
            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache('smtp', $smtp_config, $val['mark']);
                }
            } else {
                tpCache('smtp',$smtp_config);
            }
            /*--end*/
            $this->success($res['msg']);
        } else {
            $this->error($res['msg']);
        }
    }
      
    /**
     * 发送测试短信
     */
    public function send_mobile()
    {
        $param = $sms_config = input('post.');
        if (!isset($param['sms_type']) || empty($param['sms_type'])) $param['sms_type'] = 1;
        if ($param['sms_type'] == 1) {
            unset($sms_config['sms_appkey_tx']);
            unset($sms_config['sms_appid_tx']);
            unset($sms_config['sms_test_mobile']);
        }else{
            unset($sms_config['sms_appkey']);
            unset($sms_config['sms_secretkey']);
            unset($sms_config['sms_test_mobile']);
        }

        $send_scene = Db::name('sms_template')->where(['sms_tpl_code'=>['NEQ', ''],'sms_type'=>$param['sms_type'],'is_open'=>1])->order('tpl_id asc')->value("send_scene");
        $send_scene = intval($send_scene);
        $res = sendSms($send_scene, $param['sms_test_mobile'], array('content'=>mt_rand(1000,9999)),0,$sms_config);
        if (intval($res['status']) == 1) {
            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache('sms', $sms_config, $val['mark']);
                }
            } else {
                unset($sms_config['sms_type']);
                tpCache('sms', $sms_config);
            }
            /*--end*/
            $this->success($res['msg']);
        } else {
            $this->error($res['msg']);
        }
    }

    //自定义变量列表
    public function customvar_index()
    {
        $list = array();
        $keywords = input('keywords/s');

        $condition = array();
        if (!empty($keywords)) {
            $condition['a.attr_name'] = array('LIKE', "%{$keywords}%");
        }
        $condition['a.lang'] = array('eq', $this->admin_lang);

        $attr_var_names = Db::name('config')->field('name')
            ->where([
                'name'  => ['LIKE', "web_attr_%"],
                'lang'  => $this->admin_lang,
                'is_del'    => 0,
            ])->getAllWithIndex('name');
        $condition['a.attr_var_name'] = array('IN', array_keys($attr_var_names));

        $count = Db::name('config_attribute')->alias('a')->where($condition)->count();
        $pageObj = new Page($count, 100);
        $list = Db::name('config_attribute')->alias('a')
            ->field('a.*, b.id')
            ->join('__CONFIG__ b', 'b.name = a.attr_var_name AND b.lang = a.lang', 'LEFT')
            ->where($condition)
            ->where([
                'b.is_del'  => 0,
            ])
            ->order('a.attr_id asc')
            ->limit($pageObj->firstRow.','.$pageObj->listRows)
            ->select();

        $pageStr = $pageObj->show();
        $this->assign('pageStr',$pageStr);
        $this->assign('list',$list);
        $this->assign('pageObj',$pageObj);
        $recycle_switch = tpSetting('recycle.recycle_switch');//回收站开关
        $this->assign('recycle_switch', $recycle_switch);
        return $this->fetch();
    }

    /**
     * 保存自定义变量
     */
    public function customvar_save()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');

            if (empty($post['attr_name'])) {
                $this->error('至少新增一个自定义变量！');
            }

            // 数据拼装
            $addData = $editData = [];
            foreach ($post['attr_name'] as $key => $val) {
                $attr_name  = trim($val);
                if (!empty($attr_name)) {
                    $attr_input_type = intval($post['attr_input_type'][$key]);
                    if (empty($post['attr_id'][$key])) {
                        if ($this->admin_lang == $this->main_lang) {
                            $addData[] = [
                                'inc_type' => 'web',
                                'attr_name' => $attr_name,
                                'attr_input_type' => $attr_input_type,
                                'lang' => $this->admin_lang,
                                'add_time' => getTime(),
                                'update_time' => getTime(),
                            ];
                        }
                    } else {
                        $attr_id = intval($post['attr_id'][$key]);
                        $editData[] = [
                            'attr_id' => $attr_id,
                            'inc_type' => 'web',
                            'attr_name' => $attr_name,
                            'attr_input_type' => $attr_input_type,
                            'lang' => $this->admin_lang,
                            'update_time' => getTime(),
                        ];
                    }
                }
            }
            if (!empty($addData)) {
                $rdata = model('ConfigAttribute')->saveAll($addData);
                if ($rdata) {
                    foreach ($rdata as $k1 => $v1) {
                        $attr_id = $v1->getData('attr_id');
                        $addData[$k1]['attr_id'] = $attr_id;
                        $addData[$k1]['attr_var_name'] = 'web_attr_'.$attr_id;
                        $addData[$k1]['update_time'] = getTime();
                        unset($addData[$k1]['add_time']);
                    }
                    $editData = array_merge($editData, $addData);
                }

                /*多语言*/
                $langRow = [];
                if (is_language()) {
                    $langAddData = [];
                    $langRow = Db::name('language')->order('id asc')
                        ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                        ->select();
                    foreach ($langRow as $key => $val) {
                        if ($this->admin_lang == $val['mark']) {
                            continue;
                        }

                        foreach ($rdata as $k1 => $v1) {
                            $attr_data = $v1->getData();
                            $attr_data['lang'] = $val['mark'];
                            $attr_data['attr_var_name'] = 'web_attr_'.$attr_data['attr_id'];
                            unset($attr_data['attr_id']);
                            $langAddData[] = $attr_data;
                        }
                    }
                    !empty($langAddData) && model('ConfigAttribute')->saveAll($langAddData);
                }
                /*end*/
            }
            if (!empty($editData)) {
                $r = model('ConfigAttribute')->saveAll($editData);
                if ($r) {
                    // 新增到config表，更新缓存
                    if (!empty($addData)) {
                        $configData = [];
                        foreach ($addData as $key => $val) {
                            $configData[$val['attr_var_name']] = '';
                        }
                        // 多语言
                        if (is_language()) {
                            foreach ($langRow as $key => $val) {
                                tpCache('web', $configData, $val['mark']);
                            }
                        } else { // 单语言
                            tpCache('web', $configData);
                        }
                    }
                    // end

                    adminLog('保存自定义变量：'.implode(',', $post['attr_name']));
                    $this->success('操作成功', url('System/web'));
                } else {
                    $this->error('操作失败');
                }
            } 
        }
        $this->error('非法访问！');
    }

    /**
     * 删除自定义变量
     */
    public function customvar_del()
    {
        $this->language_access(); // 多语言功能操作权限

        $id = input('del_id/d');
        $deltype = input('deltype/s');
        if(!empty($id)){
            $attr_var_name = Db::name('config')->where([
                    'id'    => $id,
                    'lang'  => $this->admin_lang,
                ])->getField('name');
            if ('del' == $deltype){//彻底删除
                $r = Db::name('config')->where('name', $attr_var_name)->delete();
            }else{
                $r = Db::name('config')->where('name', $attr_var_name)->update(array('is_del'=>1, 'update_time'=>getTime()));
            }
            if($r){
                if ('del' == $deltype){
                    Db::name('config_attribute')->where('attr_var_name', $attr_var_name)->delete();
                }else{
                    Db::name('config_attribute')->where('attr_var_name', $attr_var_name)->update(array('update_time'=>getTime()));
                }
                adminLog('删除自定义变量：'.$attr_var_name);
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('参数有误');
        }
    }

    /**
     * 标签调用的弹窗说明
     */
    public function ajax_tag_call()
    {
        $space = "&nbsp;&nbsp;&nbsp;&nbsp;";
        if (IS_AJAX_POST) {
            $name = input('post.name/s');
            $msg = '';
            switch ($name) {
                case 'web_users_switch': // 会员功能入口标签
                    {
$msg_code = <<<EOF
{eyou:user type='open'}  <br>
{$space}{eyou:user type='cart'} <br>
{$space}{$space}&lt;a href="{\$field.url}" id="{\$field.id}" &gt;购物车&lt;/a&gt; <br>
{$space}{$space}{\$field.hidden} <br>
{$space}{/eyou:user} <br>
     <br>
{$space}{eyou:user type='login'} <br>
{$space}{$space}&lt;a href="{\$field.url}" id="{\$field.id}" &gt;登录&lt;/a&gt; <br>
{$space}{$space}{\$field.hidden} <br>
{$space}{/eyou:user} <br>
     <br>
{$space}{eyou:user type='reg'} <br>
{$space}{$space}&lt;a href="{\$field.url}" id="{\$field.id}" &gt;注册&lt;/a&gt; <br>
{$space}{$space}{\$field.hidden} <br>
{$space}{/eyou:user} <br>
     <br>
{$space}{eyou:user type='logout'} <br>
{$space}{$space}&lt;a href="{\$field.url}" id="{\$field.id}" &gt;退出&lt;/a&gt; <br>
{$space}{$space}{\$field.hidden} <br>
{$space}{/eyou:user}   <br>
{/eyou:user}
EOF;

$tpl_theme = TPL_THEME;
$msg = <<<EOF
<strong>前台会员登录注册标签调用</strong><br>
比如需要在PC通用头部加入会员入口，复制下方代码在/template/{$tpl_theme}pc/header.htm模板文件里找到合适位置粘贴
<br/><br/>
<div style="color:red">
{$msg_code}
</div>
EOF;
                    }
                    break;

                case 'web_language_switch': // 多语言入口标签
                    {
$tpl_theme = TPL_THEME;
$msg = <<<EOF
<strong>前台多语言切换入口标签调用</strong><br>
比如需要在PC通用头部加入多语言切换，复制下方代码在/template/{$tpl_theme}pc/header.htm模板文件里找到合适位置粘贴
<br/><br/>
<div style="color:red">
{eyou:language type='default'}<br/>
{$space}&lt;a href="{\$field.url}"&gt;&lt;img src="{\$field.logo}" alt="{\$field.title}"&gt;{\$field.title}&lt;/a&gt;<br/>
{/eyou:language}
</div>
EOF;
                    }
                    break;

                case 'thumb_open':
                    {
$msg = <<<EOF
<span style="color:red">（温馨提示：高级调用不会受缩略图功能的开关影响！）</span><br/>
【标签方法的格式】<br/>
{$space}thumb_img=###,宽度,高度,生成方式<br/>
<br/>
【指定宽高度的调用】<br/>
{$space}列表页/内容页：{\$eyou.field.litpic<span style="color:red">|thumb_img=###,500,500</span>}<br/>
{$space}标签arclist/list里：{\$field.litpic<span style="color:red">|thumb_img=###,500,500</span>}<br/>
<br/>
【指定生成方式的调用】<br/>
{$space}生成方式：1 = 拉伸；2 = 留白；3 = 截减；<br/>
{$space}以标签arclist为例：<br/>
{$space}{$space}缩略图拉伸：{\$field.litpic<span style="color:red">|thumb_img=###,500,500,1</span>}<br/>
{$space}{$space}缩略图留白：{\$field.litpic<span style="color:red">|thumb_img=###,500,500,2</span>}<br/>
{$space}{$space}缩略图截减：{\$field.litpic<span style="color:red">|thumb_img=###,500,500,3</span>}<br/>
{$space}{$space}默&nbsp;认&nbsp;生&nbsp;成：{\$field.litpic<span style="color:red">|thumb_img=###,500,500</span>}{$space}(以默认全局配置的生成方式)<br/>
EOF;
                    }
                    break;
                
                case 'shop_open':
                    {
$msg_code = <<<EOF
&lt;!--购物车组件start--&gt; <br/>
{eyou:sppurchase id='field' currentstyle='btn-danger'} <br/>
{$space}&lt;!-- 价格 标签开始 --&gt;  <br/>
{$space}&lt;div class="ey-price"&gt;&lt;span&gt;￥{\$field.users_price}&lt;/span&gt; &lt;/div&gt;  <br/>
{$space}&lt;!-- 价格 标签结束 --&gt;  <br/>
     <br/>
{$space}&lt;!-- 规格 标签开始 --&gt; <br/>
{$space}&lt;div class="ey-spec"&gt; <br/>
{$space}{eyou:volist name="\$field.ReturnData" id='field2'} <br/>
{$space}{$space}&lt;div class="row m-t-15"&gt; <br/>
{$space}{$space}{$space}&lt;label class="form-control-label col-sm-7"&gt;{\$field2.spec_name}&lt;/label&gt; <br/>
{$space}{$space}{$space}&lt;div class="col-sm-10"&gt; <br/>
{$space}{$space}{$space}{eyou:volist name="\$field2.spec_value" id='field3'} <br/>
{$space}{$space}{$space}{$space}&lt;a href="JavaScript:void(0);" {\$field3.SpecData} class="btn btn-default btn-selected {\$field3.SpecClass}"&gt;{\$field3.spec_value}&lt;/a&gt; <br/>
{$space}{$space}{$space}{/eyou:volist} <br/>
{$space}{$space}{$space}&lt;/div&gt; <br/>
{$space}{$space}&lt;/div&gt; <br/>
{$space}{/eyou:volist} <br/>
{$space}&lt;/div&gt; <br/>
{$space}&lt;!-- 规格 标签结束 --&gt; <br/>
     <br/>
{$space}&lt;!-- 数量操作 标签开始 --&gt;  <br/>
{$space}&lt;div class="ey-number"&gt;  <br/>
{$space}{$space}&lt;label&gt;数量&lt;/label&gt;  <br/>
{$space}{$space}&lt;div class="btn-input"&gt;  <br/>
{$space}{$space}{$space}&lt;button class="layui-btn" {\$field.ReduceQuantity}&gt;-&lt;/button&gt;  <br/>
{$space}{$space}{$space}&lt;input type="text" class="layui-input" {\$field.UpdateQuantity}&gt;  <br/>
{$space}{$space}{$space}&lt;button class="layui-btn" {\$field.IncreaseQuantity}&gt;+&lt;/button&gt;  <br/>
{$space}{$space}&lt;/div&gt;  <br/>
{$space}&lt;/div&gt;  <br/>
{$space}&lt;!-- 数量操作 标签结束 --&gt;  <br/>
     <br/>
{$space}&lt;!-- 库存量 标签开始 --&gt;  <br/>
{$space}&lt;span {\$field.stock_show}&gt;库存量：{\$field.stock_count} 件&lt;/span&gt;  <br/>
{$space}&lt;!-- 库存量 标签结束 --&gt;  <br/>
     <br/>
{$space}&lt;!-- 购买按钮 标签开始 --&gt;  <br/>
{$space}&lt;div class="ey-buyaction"&gt;  <br/>
{$space}{$space}&lt;a class="ey-joinin" href="JavaScript:void(0);" {\$field.ShopAddCart}&gt;加入购物车&lt;/a&gt;  <br/>
{$space}{$space}&lt;a class="ey-joinbuy" href="JavaScript:void(0);" {\$field.BuyNow}&gt;立即购买&lt;/a&gt; <br/>
{$space}&lt;/div&gt;  <br/>
{$space}&lt;!-- 购买按钮 标签结束 --&gt;  <br/>
     <br/>
{$space}{\$field.hidden}  <br/>
{/eyou:sppurchase}  <br/>
&lt;!--购物车组件end--&gt;
EOF;

$tpl_theme = TPL_THEME;
$msg = <<<EOF
<div style="color:red"> 
请手工调用最新版的购买行为入口标签，代码验证通过便可启用
<br/>
复制下方代码在/template/{$tpl_theme}pc/view_product.htm模板文件里找到合适位置粘贴
</div>
<br/>
<div id='ShopOpenCode'>
{$msg_code}
</div>
EOF;
                    }
                    break;

                default:
                    # code...
                    break;
            }
            $this->success('请求成功', null, ['msg'=>$msg]);
        }
        $this->error('非法访问！');
    }

    /**
     * 手机面板
     * @return [type] [description]
     */
    public function web_m()
    {
        return $this->fetch();
    }
}