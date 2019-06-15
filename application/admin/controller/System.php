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
        $root_dir = ROOT_DIR; // 支持子目录

        if (IS_POST) {
            $param = input('post.');
            $param['web_keywords'] = str_replace('，', ',', $param['web_keywords']);
            $param['web_description'] = filter_line_return($param['web_description']);
            
            // 网站根网址
            $web_basehost = rtrim($param['web_basehost'], '/');
            if (!is_http_url($web_basehost) && !empty($web_basehost)) {
                $web_basehost = 'http://'.$web_basehost;
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
                $source = realpath(preg_replace('#^'.$root_dir.'/#i', '', $param['web_ico']));
                $destination = realpath('favicon.ico');
                if (file_exists($source) && @copy($source, $destination)) {
                    $param['web_ico'] = $root_dir.'/favicon.ico';
                }
            }

            tpCache($inc_type, $param);
            write_global_params(); // 写入全局内置参数
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

        $config['web_ico'] = preg_replace('#^(/[/\w]+)?(/)#i', $root_dir.'$2', $config['web_ico']); // 支持子目录
        
        /*系统模式*/
        $web_cmsmode = isset($config['web_cmsmode']) ? $config['web_cmsmode'] : 2;
        $this->assign('web_cmsmode', $web_cmsmode);
        /*--end*/

        /*自定义变量*/
        $eyou_row = M('config_attribute')->field('a.attr_id, a.attr_name, a.attr_var_name, a.attr_input_type, b.value, b.id, b.name')
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

        $this->assign('config',$config);//当前配置项
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

            /*EyouCMS安装目录*/
            empty($param['web_cmspath']) && $param['web_cmspath'] = ROOT_DIR; // 支持子目录
            $web_cmspath = trim($param['web_cmspath'], '/');
            $web_cmspath = !empty($web_cmspath) ? '/'.$web_cmspath : '';
            $param['web_cmspath'] = $web_cmspath;
            /*--end*/
            /*插件入口*/
            $web_weapp_switch = $param['web_weapp_switch'];
            $web_weapp_switch_old = tpCache('web.web_weapp_switch');
            /*--end*/
            /*会员入口*/
            $web_users_switch = $param['web_users_switch'];
            $web_users_switch_old = tpCache('web.web_users_switch');
            /*--end*/
            /*自定义后台路径名*/
            $adminbasefile = trim($param['adminbasefile']).'.php'; // 新的文件名
            $param['web_adminbasefile'] = ROOT_DIR.'/'.$adminbasefile; // 支持子目录
            $adminbasefile_old = trim($param['adminbasefile_old']).'.php'; // 旧的文件名
            unset($param['adminbasefile']);
            unset($param['adminbasefile_old']);
            if ('index.php' == $adminbasefile) {
                $this->error("新后台地址禁止使用index", null, '', 1);
            }
            /*--end*/
            $param['web_sqldatapath'] = '/'.trim($param['web_sqldatapath'], '/'); // 数据库备份目录
            $param['web_htmlcache_expires_in'] = intval($param['web_htmlcache_expires_in']); // 页面缓存有效期
            /*多语言入口*/
            $web_language_switch = $param['web_language_switch'];
            $web_language_switch_old = tpCache('web.web_language_switch');
            /*--end*/

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
                write_global_params(); // 写入全局内置参数
            }
            /*--end*/

            $refresh = false;
            $gourl = request()->domain().ROOT_DIR.'/'.$adminbasefile; // 支持子目录
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

            /*更改之后，需要刷新后台的参数*/
            if ($web_weapp_switch_old != $web_weapp_switch || $web_language_switch_old != $web_language_switch || $web_users_switch_old != $web_users_switch) {
                $refresh = true;
            }
            /*--end*/
            
            /*刷新整个后台*/
            if ($refresh) {
                $this->success('操作成功', $gourl, '', 1, [], '_parent');
            }
            /*--end*/

            $this->success('操作成功', url('System/web2'));
        }

        $config = tpCache($inc_type);
        //自定义后台路径名
        $baseFile = explode('/', $this->request->baseFile());
        $web_adminbasefile = end($baseFile);
        $adminbasefile = preg_replace('/^(.*)\.([^\.]+)$/i', '$1', $web_adminbasefile);
        $this->assign('adminbasefile', $adminbasefile);
        // 数据库备份目录
        $sqlbackuppath = config('DATA_BACKUP_PATH');
        $this->assign('sqlbackuppath', $sqlbackuppath);

        $this->assign('config',$config);//当前配置项
        return $this->fetch();
    }

    /**
     * 附件设置
     */
    public function basic()
    {
        $inc_type =  'basic';

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
            $param['file_size'] = intval($param['file_size']);

            if (0 < $max_filesize && $max_filesize < $param['file_size']) {
                $this->error("附件上传大小超过空间的最大限制".$maxFileupload);
            }

            // 禁止php扩展名的附件类型
            $param['image_type'] = str_ireplace('|php|', '|', '|'.$param['image_type'].'|');
            $param['image_type'] = trim($param['image_type'], '|');
            $param['file_type'] = str_ireplace('|php|', '|', '|'.$param['file_type'].'|');
            $param['file_type'] = trim($param['file_type'], '|');
            $param['media_type'] = str_ireplace('|php|', '|', '|'.$param['media_type'].'|');
            $param['media_type'] = trim($param['media_type'], '|');

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
                }
            } else {
                tpCache($inc_type,$param);
            }
            /*--end*/
            $this->success('操作成功', url('System/basic'));
        }

        $config = tpCache($inc_type);
        $this->assign('config',$config);//当前配置项
        $this->assign('max_filesize',$max_filesize);// 文件上传最大字节数
        $this->assign('max_sizeunit',$max_sizeunit);// 文件上传最大字节的单位
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

    /**
     * 邮件配置
     */
    public function smtp()
    {
        $inc_type =  'smtp';

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
                tpCache($inc_type,$param);
            }
            /*--end*/
            $this->success('操作成功', url('System/smtp'));
        }

        $config = tpCache($inc_type);
        $this->assign('config',$config);//当前配置项
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
        $this->assign('pageStr', $pageStr); // 赋值分页输出
        $this->assign('pageObj', $pageObj); // 赋值分页对象
        
        return $this->fetch();
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
                        'lang'      => $this->home_lang,
                    ])->update($saveData);
                if ($r) {
                    $tpl_name = Db::name('smtp_tpl')->where([
                            'tpl_id'    => $post['tpl_id'],
                            'lang'      => $this->home_lang,
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
                'lang'      => $this->home_lang,
            ])->find();
        if (empty($row)) {
            $this->error('数据不存在，请联系管理员！');
            exit;
        }

        $this->assign('row',$row);
        return $this->fetch();
    }

    /**
     * 清空缓存 - 兼容升级没刷新后台，点击报错404，过1.2.5版本之后清除掉代码
     */
    public function clearCache()
    {
        return $this->clear_cache();
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

            if (!empty($post['clearHtml'])) { // 清除页面缓存
                $this->clearHtmlCache($post['clearHtml']);
            }

            if (!empty($post['clearCache'])) { // 清除数据缓存
                $this->clearSystemCache($post['clearCache']);
            }

            // 清除其他临时文件
            $this->clearOtherCache();

            /*兼容每个用户的自定义字段，重新生成数据表字段缓存文件*/
            $systemTables = ['arctype'];
            $data = Db::name('channeltype')
                ->where('nid','NEQ','guestbook')
                ->column('table');
            $tables = array_merge($systemTables, $data);
            foreach ($tables as $key => $table) {
                if ('arctype' != $table) {
                    $table = $table.'_content';
                }
                try {
                    schemaTable($table);
                } catch (\Exception $e) {}
            }
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

            // cache('admin_ModuleInitBehavior_isset_checkInlet', 1); // 配合ModuleInitBehavior.php行为的checkInlet方法，进行自动隐藏index.php

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
                if ($val == 'index') {
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
        $content = '演示一串随机数字：' . mt_rand(100000,999999);
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
        $param = input('post.');
        $res = sendSms(4,$param['sms_test_mobile'],array('content'=>mt_rand(1000,9999)));
        exit(json_encode($res));
    }

    /**
     * 新增自定义变量
     */
    public function customvar_add()
    {
        $this->language_access(); // 多语言功能操作权限

        if (IS_POST) {
            $configAttributeM = model('ConfigAttribute');

            $post_data = input('post.');
            $attr_input_type = isset($post_data['attr_input_type']) ? $post_data['attr_input_type'] : '';

            if ($attr_input_type == 3) {
                // 本地/远程图片上传的处理
                $is_remote = !empty($post_data['is_remote']) ? $post_data['is_remote'] : 0;
                $litpic = '';
                if ($is_remote == 1) {
                    $litpic = $post_data['value_remote'];
                } else {
                    $litpic = $post_data['value_local'];
                }
                $attr_values = $litpic;
            } else {
                $attr_values = input('attr_values');
                // $attr_values = str_replace('_', '', $attr_values); // 替换特殊字符
                // $attr_values = str_replace('@', '', $attr_values); // 替换特殊字符
                $attr_values = trim($attr_values);
                $attr_values = isset($attr_values) ? $attr_values : '';
            }

            $savedata = array(
                'inc_type'    => $post_data['inc_type'],
                'attr_name' => $post_data['attr_name'],
                'attr_input_type'   => $attr_input_type,
                'attr_values'   => $attr_values,
                'update_time'   => getTime(),
            );

            // 数据验证            
            $validate = \think\Loader::validate('ConfigAttribute');
            if(!$validate->batch()->check($savedata))
            {
                $error = $validate->getError();
                $error_msg = array_values($error);
                $this->error($error_msg[0]);
            } else {
                $langRow = Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();

                $attr_var_name = '';
                foreach ($langRow as $key => $val) {
                    $savedata['add_time'] = getTime();
                    $savedata['lang'] = $val['mark'];
                    $insert_id = Db::name('config_attribute')->insertGetId($savedata);
                    // 更新变量名
                    if (!empty($insert_id)) {
                        if (0 == $key) {
                            $attr_var_name = $post_data['inc_type'].'_attr_'.$insert_id;
                        }
                        Db::name('config_attribute')->where([
                                'attr_id'   => $insert_id,
                                'lang'  => $val['mark'],
                            ])->update(array('attr_var_name'=>$attr_var_name));
                    }
                }
                adminLog('新增自定义变量：'.$savedata['attr_name']);

                // 保存到config表，更新缓存
                $inc_type = $post_data['inc_type'];
                $configData = array(
                    $attr_var_name  => $attr_values,
                );

                // 多语言
                if (is_language()) {
                    foreach ($langRow as $key => $val) {
                        tpCache($inc_type, $configData, $val['mark']);
                    }
                } else { // 单语言
                    tpCache($inc_type, $configData);
                }

                $this->success('操作成功');
            }  
        }

        $inc_type = input('param.inc_type/s', '');
        $this->assign('inc_type', $inc_type);

        return $this->fetch();
    }

    /**
     * 编辑自定义变量
     */
    public function customvar_edit()
    {
        if (IS_POST) {
            $configAttributeM = model('ConfigAttribute');

            $post_data = input('post.');
            $attr_input_type = isset($post_data['attr_input_type']) ? $post_data['attr_input_type'] : '';

            if ($attr_input_type == 3) {
                // 本地/远程图片上传的处理
                $is_remote = !empty($post_data['is_remote']) ? $post_data['is_remote'] : 0;
                $litpic = '';
                if ($is_remote == 1) {
                    $litpic = $post_data['value_remote'];
                } else {
                    $litpic = $post_data['value_local'];
                }
                $attr_values = $litpic;
            } else {
                $attr_values = input('attr_values');
                // $attr_values = str_replace('_', '', $attr_values); // 替换特殊字符
                // $attr_values = str_replace('@', '', $attr_values); // 替换特殊字符
                $attr_values = trim($attr_values);
                $attr_values = isset($attr_values) ? $attr_values : '';
            }

            $savedata = array(
                'inc_type'    => $post_data['inc_type'],
                'attr_name' => $post_data['attr_name'],
                'attr_input_type'   => $attr_input_type,
                'attr_values'   => $attr_values,
                'update_time'   => getTime(),
            );

            // 数据验证            
            $validate = \think\Loader::validate('ConfigAttribute');
            if(!$validate->batch()->check($savedata))
            {
                $error = $validate->getError();
                $error_msg = array_values($error);
                $this->error($error_msg[0]);
            } else {
                $langRow = Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();

                $configAttributeM->data($savedata,true); // 收集数据
                $configAttributeM->isUpdate(true, [
                        'attr_id'   => $post_data['attr_id'],
                        'lang'  => $this->admin_lang,
                    ])->save(); // 写入数据到数据库  
                // 更新变量名
                $attr_var_name = $post_data['name'];
                adminLog('编辑自定义变量：'.$savedata['attr_name']);

                // 保存到config表，更新缓存
                $inc_type = $post_data['inc_type'];
                $configData = array(
                    $attr_var_name  => $attr_values,
                );

                tpCache($inc_type, $configData);

                $this->success('操作成功');
            }  
        }

        $field = array();
        $id = input('param.id/d', 0);
        $field = M('config')->field('a.id, a.value, a.name, b.attr_id, b.attr_name, b.attr_input_type')
            ->alias('a')
            ->join('__CONFIG_ATTRIBUTE__ b', 'a.name=b.attr_var_name AND a.lang=b.lang', 'LEFT')
            ->where([
                'a.id'    => $id,
                'a.lang'  => $this->admin_lang,
            ])->find();
        if ($field['attr_input_type'] == 3) {
            if (is_http_url($field['value'])) {
                $field['is_remote'] = 1;
                $field['value_remote'] = $field['value'];
            } else {
                $field['is_remote'] = 0;
                $field['value_local'] = $field['value'];
            }
        }
        $this->assign('field', $field);

        $inc_type = input('param.inc_type/s', '');
        $this->assign('inc_type', $inc_type);

        return $this->fetch();
    }

    /**
     * 删除自定义变量
     */
    public function customvar_del()
    {
        $this->language_access(); // 多语言功能操作权限

        $id = input('del_id/d');
        if(!empty($id)){
            $attr_var_name = M('config')->where([
                    'id'    => $id,
                    'lang'  => $this->admin_lang,
                ])->getField('name');

            $r = M('config')->where('name', $attr_var_name)->update(array('is_del'=>1, 'update_time'=>getTime()));
            if($r){
                M('config_attribute')->where('attr_var_name', $attr_var_name)->update(array('update_time'=>getTime()));
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
        if (IS_AJAX_POST) {
            $name = input('post.name/s');
            $msg = '';
            switch ($name) {
                case 'web_users_switch': // 会员功能入口标签
                    {
                        $msg = '
<div yne-bulb-block="paragraph">
    <strong>前台会员登录注册标签调用</strong><br data-filtered="filtered">
    比如需要在PC通用头部加入会员入口，复制下方代码在\template\pc\header.htm模板文件里找到合适位置粘贴
</div>
<br data-filtered="filtered">
<div yne-bulb-block="paragraph" style="color:red;">
    <div>
        {eyou:user type=\'open\'}
        &nbsp;</div>
    <div>
        &nbsp; &nbsp; &nbsp; &nbsp; {eyou:user type=\'login\'}</div>
    <div>
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;a href="{$field.url}" id="{$field.id}" &gt;登录&lt;/a&gt;</div>
    <div>
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {$field.hidden}</div>
    <div>
        &nbsp; &nbsp; &nbsp; &nbsp; {/eyou:user}</div>
    <div>
        &nbsp;&nbsp;</div>
    <div>
        &nbsp; &nbsp; &nbsp; &nbsp; {eyou:user type=\'reg\'}</div>
    <div>
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;a href="{$field.url}" id="{$field.id}" &gt;注册&lt;/a&gt;</div>
    <div>
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {$field.hidden}</div>
    <div>
        &nbsp; &nbsp; &nbsp; &nbsp; {/eyou:user}</div>
    <div>
        &nbsp;</div>
    <div>
        &nbsp; &nbsp; &nbsp; &nbsp; {eyou:user type=\'logout\'}</div>
    <div>
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &lt;a href="{$field.url}" id="{$field.id}" &gt;退出&lt;/a&gt;</div>
    <div>
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {$field.hidden}</div>
    <div>
        &nbsp; &nbsp; &nbsp; &nbsp; {/eyou:user}
        &nbsp;</div>
    <div>
        {/eyou:user}</div>
</div>
';
                    }
                    break;

                case 'web_language_switch': // 多语言入口标签
                    {
                        $msg = '
<div yne-bulb-block="paragraph">
    <strong>前台多语言切换入口标签调用</strong><br data-filtered="filtered">
    比如需要在PC通用头部加入多语言切换，复制下方代码在\template\pc\header.htm模板文件里找到合适位置粘贴
</div>
<br data-filtered="filtered">
<div yne-bulb-block="paragraph" style="color:red">
    {eyou:language type=\'default\'}<br/>
    &nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="{$field.url}"&gt;&lt;img src="{$field.logo}" alt="{$field.title}"&gt;{$field.title}&lt;/a&gt;<br/>
    {/eyou:language}</div>
';
                    }
                    break;

                case 'thumb_open':
                    {
                        $msg = '
<div yne-bulb-block="paragraph">
    <span style="color:red">（温馨提示：高级调用不会受缩略图功能的开关影响！）</span></div>
<div yne-bulb-block="paragraph">
    【标签方法的格式】</div>
<div yne-bulb-block="paragraph">
    &nbsp;&nbsp;&nbsp;&nbsp;thumb_img=###,宽度,高度,生成方式</div>
<br data-filtered="filtered">
<div yne-bulb-block="paragraph">
    【指定宽高度的调用】</div>
<div yne-bulb-block="paragraph">
    &nbsp;&nbsp;&nbsp;&nbsp;列表页/内容页：{$eyou.field.litpic<span style="color:red">|thumb_img=###,500,500</span>}</div>
<div yne-bulb-block="paragraph">
    &nbsp;&nbsp;&nbsp;&nbsp;标签arclist/list里：{$field.litpic<span style="color:red">|thumb_img=###,500,500</span>}</div>
<br data-filtered="filtered">
<div yne-bulb-block="paragraph">
    【指定生成方式的调用】</div>
<div yne-bulb-block="paragraph">
    &nbsp;&nbsp;&nbsp;&nbsp;生成方式：1 = 拉伸；2 = 留白；3 = 截减；<br data-filtered="filtered">
    &nbsp;&nbsp;&nbsp;&nbsp;以标签arclist为例：</div>
<div yne-bulb-block="paragraph">
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;缩略图拉伸：{$field.litpic<span style="color:red">|thumb_img=###,500,500,1</span>}</div>
<div yne-bulb-block="paragraph">
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;缩略图留白：{$field.litpic<span style="color:red">|thumb_img=###,500,500,2</span>}</div>
<div yne-bulb-block="paragraph">
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;缩略图截减：{$field.litpic<span style="color:red">|thumb_img=###,500,500,3</span>}</div>
<div yne-bulb-block="paragraph">
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;默&nbsp;认&nbsp;生&nbsp;成：{$field.litpic<span style="color:red">|thumb_img=###,500,500</span>}&nbsp;&nbsp;&nbsp;&nbsp;(以默认全局配置的生成方式)</div>
';
                    }
                    break;
                
                case 'shop_open':
                    {
                        $msg = '
<div yne-bulb-block="paragraph">
    <strong>前台产品内容页的购买入口标签调用</strong><br data-filtered="filtered">
    比如需要在产品模型的内容页加入购买功能，复制下方代码在\template\pc\view_product.htm模板文件里找到合适位置粘贴
</div>
<br data-filtered="filtered">
<div yne-bulb-block="paragraph" style="color:red">
  &lt;!--购物车组件start--&gt; 
  <br data-filtered="filtered">
  {eyou:sppurchase id=\'field\'}
  <br data-filtered="filtered">&nbsp;&nbsp;&nbsp;&nbsp;
      &lt;div class="ey-price"&gt;&lt;span&gt;￥{$field.users_price}&lt;/span&gt; &lt;/div&gt;
      <br data-filtered="filtered">&nbsp;&nbsp;&nbsp;&nbsp;
      &lt;div class="ey-number"&gt;
      <br data-filtered="filtered">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &lt;label&gt;数量&lt;/label&gt;
        <br data-filtered="filtered">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &lt;div class="btn-input"&gt;
        <br data-filtered="filtered">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &lt;button class="layui-btn" {$field.ReduceQuantity}&gt;-&lt;/button&gt;
          <br data-filtered="filtered">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &lt;input type="text" class="layui-input" {$field.UpdateQuantity}&gt;
          <br data-filtered="filtered">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &lt;button class="layui-btn" {$field.IncreaseQuantity}&gt;+&lt;/button&gt;
          <br data-filtered="filtered">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        &lt;/div&gt;
        <br data-filtered="filtered">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      &lt;/div&gt;
      <br data-filtered="filtered">&nbsp;&nbsp;&nbsp;&nbsp;
      &lt;div class="ey-buyaction"&gt;
      <br data-filtered="filtered">&nbsp;&nbsp;&nbsp;&nbsp;
      &lt;a class="ey-joinin" href="JavaScript:void(0);" {$field.ShopAddCart}&gt;加入购物车&lt;/a&gt;
      <br data-filtered="filtered">&nbsp;&nbsp;&nbsp;&nbsp;
      &lt;a class="ey-joinbuy" href="JavaScript:void(0);" {$field.BuyNow}&gt;立即购买&lt;/a&gt;
      <br data-filtered="filtered">&nbsp;&nbsp;&nbsp;&nbsp;
      &lt;/div&gt;
      <br data-filtered="filtered">&nbsp;&nbsp;&nbsp;&nbsp;
      {$field.hidden}
      <br data-filtered="filtered">
  {/eyou:sppurchase}
  <br data-filtered="filtered">
  &lt;!--购物车组件end--&gt; 
</div>
';
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
}