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
use app\admin\logic\FilemanagerLogic;

class Filemanager extends Base
{
    public $filemanagerLogic;
    public $baseDir = '';
    public $maxDir = '';
    public $globalTpCache = array();

    public function _initialize() {
        parent::_initialize();
        $this->filemanagerLogic = new FilemanagerLogic(); 
        $this->globalTpCache = $this->filemanagerLogic->globalTpCache;
        $this->baseDir = $this->filemanagerLogic->baseDir; // 服务器站点根目录绝对路径
        $this->maxDir = $this->filemanagerLogic->maxDir; // 默认文件管理的最大级别目录
    }

    public function index()
    {
        // 安全修复：防止目录遍历攻击
        
        // 1. 获取并初步清理用户输入
        $activepath = input('param.activepath', '', null);
        $activepath = $this->filemanagerLogic->replace_path($activepath, ':', true);

        /*当前目录路径*/
        $activepath = !empty($activepath) ? $activepath : $this->maxDir;
        
        // 2. 移除所有危险字符（../ .\ 等）
        $activepath = str_replace(['..', '\\', "\0"], '', $activepath);
        
        // 3. 规范化多个斜杆为单个斜杆
        $activepath = preg_replace("#\/{2,}#", "/", $activepath);
        if ($activepath == "/") {
            $activepath = "";
        }
        
        // 4. 字符白名单验证（只允许字母、数字、下划线、连字符、斜杆）
        if (!empty($activepath) && !preg_match('#^[a-zA-Z0-9_\-/]+$#', $activepath)) {
            $this->error('路径包含非法字符');
        }
        
        // 5. 检查路径前缀（必须以 maxDir 开头）
        $tmp_max_dir = preg_replace("#\/#i", "\/", $this->maxDir);
        if (!preg_match("#^".$tmp_max_dir."#i", $activepath)) {
            $activepath = $this->maxDir;
        }
        
        // 6. 构建完整路径
        $inpath = "";
        if (empty($activepath)) {
            $inpath = $this->baseDir . $this->maxDir;
        } else {
            $inpath = $this->baseDir . $activepath;
        }
        
        // 7. 使用 realpath 规范化路径（这是最关键的防护）
        $base_path = realpath($this->baseDir . $this->maxDir);
        $real_path = realpath($inpath);
        
        // 8. 如果路径不存在或不在允许的目录范围内，拒绝访问
        if (false === $real_path || false === $base_path) {
            $this->error('目录不存在');
        }
        
        // 9. 检查规范化后的路径是否在允许的范围内
        if (0 !== strpos($real_path, $base_path)) {
            $this->error('非法路径访问，禁止目录穿越');
        }
        
        // 10. 验证通过，使用规范化后的路径
        $inpath = $real_path;

        $list = $this->filemanagerLogic->getDirFile($inpath, $activepath);
        $assign_data['list'] = $list;

        /*文件操作*/
        $assign_data['replaceImgOpArr'] = $this->filemanagerLogic->replaceImgOpArr;
        $assign_data['editOpArr'] = $this->filemanagerLogic->editOpArr;
        $assign_data['renameOpArr'] = $this->filemanagerLogic->renameOpArr;
        $assign_data['delOpArr'] = $this->filemanagerLogic->delOpArr;
        $assign_data['moveOpArr'] = $this->filemanagerLogic->moveOpArr;
        /*--end*/

        $assign_data['activepath'] = $activepath;
        // 文件树最小可导航路径（maxDir的父目录），强制正斜杠
        $assign_data['minPath'] = str_replace('\\', '/', dirname($this->maxDir));

        $this->assign($assign_data);
        return $this->fetch();
    }


    /**
     * 替换图片
     */
    public function replace_img()
    {
        if (IS_POST) {
            $post = input('post.', '', null);
            $activepath = !empty($post['activepath']) ? trim($post['activepath']) : '';
            
            // 安全修复：验证路径
            $activepath = $this->validatePath($activepath);
            if (empty($activepath)) {
                $this->error('参数有误');
                exit;
            }

            $file = request()->file('upfile');
            if (empty($file)) {
                $this->error('请选择上传图片！');
                exit;
            } else {
                $image_type = tpCache('global.image_type');
                $fileExt = !empty($image_type) ? str_replace('|', ',', $image_type) : config('global.image_ext');
                $image_upload_limit_size = intval(tpCache('global.file_size') * 1024 * 1024);
                $result = $this->validate(
                    ['file' => $file], 
                    ['file'=>'image|fileSize:'.$image_upload_limit_size.'|fileExt:'.$fileExt],
                    ['file.image' => '上传文件必须为图片','file.fileSize' => '上传文件过大','file.fileExt'=>'上传文件后缀名必须为'.$fileExt]
                );
                if (true !== $result || empty($file)) {
                    $this->error($result);
                    exit;
                }
            }

            $res = $this->filemanagerLogic->upload('upfile', $activepath, $post['filename'], 'image');
            if ($res['code'] == 1) {
                $this->success('操作成功！', url('Filemanager/index', array('activepath'=>$this->filemanagerLogic->replace_path($activepath, ':', false))));
            } else {
                $this->error($res['msg'], url('Filemanager/index', array('activepath'=>$this->filemanagerLogic->replace_path($activepath, ':', false))));
            }
        }

        $filename = input('param.filename/s', '', null);

        $activepath = input('param.activepath/s', '', null);
        $activepath = $this->filemanagerLogic->replace_path($activepath, ':', true);
        
        // 安全修复：验证路径
        $activepath = $this->validatePath($activepath);
        
        if ($activepath == "") $activepathname = "根目录";
        else $activepathname = $activepath;

        $info = array(
            'activepath'    => $activepath,
            'activepathname'    => $activepathname,
            'filename'  => $filename,
        );
        $this->assign('info', $info);
        return $this->fetch();
    }

    /**
     * 编辑
     */
    public function edit()
    {
        if (IS_POST) {
            $post = input('post.', '', null);
            $content = input('post.content', '', null);
            $filename = !empty($post['filename']) ? trim($post['filename']) : '';
            $content = !empty($content) ? $content : '';
            $activepath = !empty($post['activepath']) ? trim($post['activepath']) : '';
            
            // 安全修复：验证路径和文件名
            $activepath = $this->validatePath($activepath);
            $filename = $this->validateFilename($filename);
            
            // 安全修复：禁止编辑系统关键文件
            if (!$this->checkForbiddenFile($filename, $activepath)) {
                $this->error('禁止编辑系统关键文件');
                exit;
            }

            if (empty($filename) || empty($activepath)) {
                $this->error('参数有误');
                exit;
            }

            $r = $this->filemanagerLogic->editFile($filename, $activepath, $content);
            if ($r === true) {
                $this->success('操作成功！', url('Filemanager/index', array('activepath'=>$this->filemanagerLogic->replace_path($activepath, ':', false))));
                exit;
            } else {
                $this->error($r, null, [], 8);
                exit;
            }
        }

        $activepath = input('param.activepath/s', '', null);
        $activepath = $this->filemanagerLogic->replace_path($activepath, ':', true);

        $filename = input('param.filename/s', '', null);

        // 安全修复：验证路径和文件名
        $activepath = $this->validatePath($activepath);
        $filename = $this->validateFilename($filename);
        
        // 安全修复：禁止编辑系统关键文件
        if (!$this->checkForbiddenFile($filename)) {
            $this->error('禁止编辑系统关键文件');
            exit;
        }
        
        $path_parts  = pathinfo($filename);
        $path_parts['extension'] = strtolower($path_parts['extension']);

        /*不允许越过指定最大级目录的文件编辑*/
        $tmp_max_dir = preg_replace("#\/#i", "\/", $this->filemanagerLogic->maxDir);
        if (!preg_match("#^".$tmp_max_dir."#i", $activepath)) {
            $this->error('没有操作权限！');
            exit;
        }
        /*--end*/
        
        /*允许编辑的文件类型*/
        if (!in_array($path_parts['extension'], $this->filemanagerLogic->editExt)) {
            $this->error('只允许操作文件类型如下：'.implode('|', $this->filemanagerLogic->editExt));
            exit;
        }
        /*--end*/
        
        // 安全修复：额外检查 - 严格限制可编辑文件类型，不包括 php
        $safe_edit_ext = ['txt', 'htm', 'html', 'css', 'js', 'json'];
        if (!in_array($path_parts['extension'], $safe_edit_ext)) {
            $this->error('出于安全考虑，只允许编辑以下类型文件：'.implode(', ', $safe_edit_ext));
            exit;
        }
        /*--end*/

        /*读取文件内容*/
        $file = $this->baseDir."$activepath/$filename";
        $content = "";
        if(is_file($file))
        {
            $filesize = filesize($file);
            if (0 < $filesize) {
                $fp = fopen($file, "r");
                $content = fread($fp, $filesize);
                fclose($fp);
                if ('htm' == $path_parts['extension']) {
                    $content = htmlspecialchars($content, ENT_QUOTES);
                    foreach ($this->filemanagerLogic->disableFuns as $key => $val) {
                        $val_new = msubstr($val, 0, 1).'-'.msubstr($val, 1);
                        $content = preg_replace("/(@)?".$val."(\s*)\(/i", "{$val_new}(", $content);
                    }
                }
            }
        }
        /*--end*/

        if($path_parts['extension'] == 'js'){
            $extension = 'text/javascript';
        } else if($path_parts['extension'] == 'css'){
            $extension = 'text/css';
        } else {
            $extension = 'text/html';
        }

        $info = array(
            'filename'  => $filename,
            'activepath'=> $activepath,
            'extension' => $extension,
            'content'   => $content,
        );
        $this->assign('info', $info);
        return $this->fetch();
    }

    /**
     * 新建文件
     */
    public function newfile()
    {
        if (IS_POST) {
            $post = input('post.', '', null);
            $content = input('post.content', '', null);
            $filename = !empty($post['filename']) ? trim($post['filename']) : '';
            $content = !empty($content) ? $content : '';
            $activepath = !empty($post['activepath']) ? trim($post['activepath']) : '';
            
            // 安全修复：验证路径和文件名
            $activepath = $this->validatePath($activepath);
            $filename = $this->validateFilename($filename);
            
            // 安全修复：禁止编辑系统关键文件
            if (!$this->checkForbiddenFile($filename, $activepath)) {
                $this->error('禁止编辑系统关键文件');
                exit;
            }

            if (empty($filename) || empty($activepath)) {
                $this->error('参数有误');
                exit;
            }

            $r = $this->filemanagerLogic->editFile($filename, $activepath, $content);
            if ($r === true) {
                $this->success('操作成功！', url('Filemanager/index', array('activepath'=>$this->filemanagerLogic->replace_path($activepath, ':', false))));
                exit;
            } else {
                $this->error($r, null, [], 8);
                exit;
            }
        }

        $activepath = input('param.activepath/s', '', null);
        $activepath = $this->filemanagerLogic->replace_path($activepath, ':', true);
        
        // 安全修复：验证路径
        $activepath = $this->validatePath($activepath);
        
        $filename = 'newfile.htm';
        $content = "";
        $info = array(
            'filename'  => $filename,
            'activepath'=> $activepath,
            'content'   => $content,
            'extension' => 'text/html',
        );
        $this->assign('info', $info);
        return $this->fetch();
    }
    
    /**
     * 安全函数：验证路径，防止目录穿越
     * @param string $path 待验证的路径
     * @return string 清理后的安全路径
     */
    private function validatePath($path)
    {
        if (empty($path)) {
            return '';
        }
        
        // 1. 移除所有危险字符
        $path = str_replace(['..', '\\', "\0", '<', '>', '|', '&', ';'], '', $path);

        // 2. 字符白名单验证（只允许字母、数字、下划线、连字符、斜杆、点、冒号）
        // 注意：EyouCMS 使用冒号作为路径分隔符（如 template:pc）
        if (!preg_match('#^[a-zA-Z0-9_\-/.:]+$#', $path)) {
            return '';
        }
        
        // 3. 移除连续的斜杆
        $path = preg_replace('#/{2,}#', '/', $path);
        
        return $path;
    }
    
    /**
     * 安全函数：验证文件名，防止路径穿越
     * @param string $filename 待验证的文件名
     * @return string 清理后的安全文件名
     */
    private function validateFilename($filename)
    {
        if (empty($filename)) {
            return '';
        }
        
        // 1. 移除所有危险字符
        $filename = str_replace(['..', '/', '\\', "\0", '<', '>', '|', '&', ';'], '', $filename);
        
        // 2. 字符白名单验证（只允许字母、数字、下划线、连字符、点）
        if (!preg_match('#^[a-zA-Z0-9_\-\.]+$#', $filename)) {
            return '';
        }
        
        return $filename;
    }
    
    /**
     * 安全函数：检查是否为禁止编辑的系统关键文件
     * @param string $filename 待检查的文件名
     * @param string $filepath 文件所在路径（用于判断目录白名单）
     * @return bool true=允许编辑, false=禁止编辑
     */
    private function checkForbiddenFile($filename, $filepath = '')
    {
        if (empty($filename)) {
            return false;
        }
        
        // 1. 禁止编辑的文件名列表（不区分大小写）
        $forbidden_files = [
            // 核心配置文件（实际存在）
            'config.php',           // 各模块配置
            'database.php',         // 数据库配置
            'route.php',            // 路由配置
            'tags.php',             // 标签配置
            'html.php',             // 静态生成配置
            '.htaccess',            // Apache配置
            
            // 系统入口文件（实际存在）
            'index.php',            // 前台入口
            'login.php',            // 后台登录入口
            'console.php',          // 控制台入口
            
            // 系统核心文件（实际存在）
            'common.php',           // 公共函数
            'function.php',         // 扩展函数
            'helper.php',           // 助手函数
            'start.php',            // 框架启动
            'base.php',             // 基础配置
            'convention.php',       // 惯例配置
            
            // 命令和自动化
            'command.php',          // 命令配置
            'auto.php',             // 安装器自动化
            
            // 第三方库配置
            'autoload.php',         // Composer自动加载
            'composer.json',        // Composer配置
        ];
        
        // 2. 转换为小写进行比较
        $filename_lower = strtolower($filename);
        
        // 3. 检查是否在禁止列表中
        foreach ($forbidden_files as $forbidden) {
            if ($filename_lower === strtolower($forbidden)) {
                return false; // 禁止编辑
            }
        }
        
        // 4. PHP 文件的特殊处理：只允许 weapp、plugins 目录下的 PHP 文件
        $file_ext = pathinfo($filename_lower, PATHINFO_EXTENSION);
        if ($file_ext === 'php') {
            // 规范化路径（移除开头的斜杠和冒号）
            $normalized_path = ltrim($filepath, '/:');

            // 检查是否在白名单目录中
            $allowed_dirs = ['weapp', 'plugins'];
            $is_in_whitelist = false;

            foreach ($allowed_dirs as $dir) {
                if (strpos($normalized_path, $dir) === 0 || strpos($normalized_path, '/' . $dir) === 0) {
                    $is_in_whitelist = true;
                    break;
                }
            }

            // 如果不在白名单目录中，禁止编辑
            if (!$is_in_whitelist) {
                return false;
            }
        }
        
        // 5. 禁止编辑隐藏文件（以 . 开头）
        if (strpos(basename($filename), '.') === 0) {
            return false;
        }
        
        // 6. 禁止编辑数据库文件
        $db_extensions = ['sql', 'db', 'sqlite', 'mdb'];
        if (in_array($file_ext, $db_extensions)) {
            return false;
        }
        
        return true; // 允许编辑
    }

    /**
     * 专业级模板编辑器
     */
    /*public function editor()
    {
        $activepath = input('param.activepath/s', '', null);
        $activepath = $this->filemanagerLogic->replace_path($activepath, ':', true);
        $filename = input('param.filename/s', '', null);

        // 安全验证
        $activepath = $this->validatePath($activepath);
        $filename = $this->validateFilename($filename);

        if (!$this->checkForbiddenFile($filename, $activepath)) {
            $this->error('禁止编辑系统关键文件');
            exit;
        }

        $path_parts = pathinfo($filename);
        $ext = strtolower($path_parts['extension'] ?? '');

        // 检查目录权限
        $tmp_max_dir = preg_replace("#\/#i", "\/", $this->filemanagerLogic->maxDir);
        if (!preg_match("#^".$tmp_max_dir."#i", $activepath)) {
            $this->error('没有操作权限！');
            exit;
        }

        // 检查文件类型
        $safe_edit_ext = ['txt', 'htm', 'html', 'css', 'js', 'json'];
        if (!in_array($ext, $safe_edit_ext)) {
            $this->error('只允许编辑: '.implode(', ', $safe_edit_ext));
            exit;
        }

        // 读取文件内容
        $file = $this->baseDir."$activepath/$filename";
        $content = "";
        if (is_file($file)) {
            $content = file_get_contents($file);
            if ($ext == 'htm' || $ext == 'html') {
                $content = htmlspecialchars($content, ENT_QUOTES);
            }
        }

        // 确定编辑器模式
        $modeMap = [
            'js' => 'text/javascript',
            'css' => 'text/css',
            'json' => 'application/json',
            'htm' => 'text/html',
            'html' => 'text/html',
            'txt' => 'text/plain'
        ];
        $extension = $modeMap[$ext] ?? 'text/html';

        $info = [
            'filename' => $filename,
            'activepath' => $activepath,
            'extension' => $extension,
            'content' => $content,
        ];
        $this->assign('info', $info);
        return $this->fetch();
    }*/

    /**
     * AJAX获取文件树
     */
    public function ajax_file_tree()
    {
        $activepath = input('param.activepath/s', '', null);
        $activepath = $this->filemanagerLogic->replace_path($activepath, ':', true);
        $activepath = $this->validatePath($activepath);

        if (empty($activepath)) {
            $activepath = $this->filemanagerLogic->maxDir;
        }

        // 限制路径范围：不能超出 maxDir 的父目录
        $minDir = dirname($this->maxDir);
        if ($minDir === '/' || $minDir === '\\' || $minDir === '.') {
            // maxDir 只有一级目录，限制为 maxDir 本身
            $minDir = $this->maxDir;
        }
        $tmp_min_dir = preg_replace("#\/#i", "\/", $minDir);
        if (!preg_match("#^".$tmp_min_dir."#i", $activepath)) {
            $activepath = $this->filemanagerLogic->maxDir;
        }

        $inpath = $this->baseDir . $activepath;
        if (!is_dir($inpath)) {
            return json(['code' => 0, 'msg' => '目录不存在']);
        }

        $files = scandir($inpath);
        $result = [];
        $editExt = ['htm', 'html', 'css', 'js', 'txt', 'json'];

        foreach ($files as $file) {
            if ($file == '.' || $file == '..') continue;

            $fullpath = $inpath . '/' . $file;
            $item = ['name' => $file];

            if (is_dir($fullpath)) {
                $item['type'] = 'dir';
                $item['path'] = $activepath . '/' . $file;
            } else {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (!in_array($ext, $editExt)) continue;

                $item['type'] = 'file';
                $item['path'] = $activepath;
            }
            $result[] = $item;
        }

        // 目录排前面
        usort($result, function($a, $b) {
            if ($a['type'] == $b['type']) {
                return strcmp($a['name'], $b['name']);
            }
            return $a['type'] == 'dir' ? -1 : 1;
        });

        return json(['code' => 1, 'data' => $result]);
    }

    /**
     * AJAX获取文件内容
     */
    public function ajax_get_content()
    {
        $filename = input('param.filename/s', '', null);
        $activepath = input('param.activepath/s', '', null);

        // 将冒号转换为斜杠（EyouCMS路径格式）
        $activepath = $this->filemanagerLogic->replace_path($activepath, ':', true);

        $activepath = $this->validatePath($activepath);
        $filename = $this->validateFilename($filename);

        if (empty($filename) || empty($activepath)) {
            return json(['code' => 0, 'msg' => '参数有误']);
        }

        $filepath = $this->baseDir . $activepath . '/' . $filename;
        if (!file_exists($filepath)) {
            return json(['code' => 0, 'msg' => '文件不存在']);
        }

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $modeMap = [
            'htm' => 'text/html',
            'html' => 'text/html',
            'css' => 'text/css',
            'js' => 'text/javascript',
            'json' => 'application/json',
            'php' => 'text/x-php'
        ];

        $content = file_get_contents($filepath);
        return json([
            'code' => 1,
            'data' => [
                'content' => $content,
                'extension' => $ext,
                'mode' => isset($modeMap[$ext]) ? $modeMap[$ext] : 'text/plain'
            ]
        ]);
    }

    /**
     * AJAX保存文件
     */
    public function ajax_save()
    {
        if (!IS_POST) {
            return json(['code' => 0, 'msg' => '非法请求']);
        }

        $filename = input('post.filename/s', '', null);
        $activepath = input('post.activepath/s', '', null);
        $content = input('post.content', '', null);

        // 将冒号转换为斜杠（EyouCMS路径格式）
        $activepath = $this->filemanagerLogic->replace_path($activepath, ':', true);

        $activepath = $this->validatePath($activepath);
        $filename = $this->validateFilename($filename);

        if (!$this->checkForbiddenFile($filename, $activepath)) {
            return json(['code' => 0, 'msg' => '禁止编辑系统文件']);
        }

        if (empty($filename) || empty($activepath)) {
            return json(['code' => 0, 'msg' => '参数有误']);
        }

        $r = $this->filemanagerLogic->editFile($filename, $activepath, $content);
        if ($r === true) {
            return json(['code' => 1, 'msg' => '保存成功']);
        } else {
            return json(['code' => 0, 'msg' => $r]);
        }
    }
}
