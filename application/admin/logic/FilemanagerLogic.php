<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海口快推科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */

namespace app\admin\logic;

use think\Model;
use think\Db;
/**
 * 文件管理逻辑定义
 * Class CatsLogic
 * @package admin\Logic
 */
class FilemanagerLogic extends Model
{
    public $globalTpCache = array();
    public $baseDir = ''; // 服务器站点根目录绝对路径
    public $maxDir = '';
    public $replaceImgOpArr = array(); // 替换权限
    public $editOpArr = array(); // 编辑权限
    public $renameOpArr = array(); // 改名权限
    public $delOpArr = array(); // 删除权限
    public $moveOpArr = array(); // 移动权限
    public $editExt = array(); // 允许新增/编辑扩展名文件
    public $disableFuns = array(); // 允许新增/编辑扩展名文件

    /**
     * 析构函数
     */
    function  __construct() {
        $this->globalTpCache = tpCache('global');
        $this->baseDir = rtrim(ROOT_PATH, DS); // 服务器站点根目录绝对路径
        $this->maxDir = $this->globalTpCache['web_templets_dir']; // 默认文件管理的最大级别目录
        // 替换权限
        $this->replaceImgOpArr = array('gif','jpg','svg');
        // 编辑权限
        $this->editOpArr = array('txt','htm','js','css');
        // 改名权限
        $this->renameOpArr = array('dir','gif','jpg','svg','flash','zip','exe','mp3','wmv','rm','txt','htm','js','css','other');
        // 删除权限
        $this->delOpArr = array('dir','gif','jpg','svg','flash','zip','exe','mp3','wmv','rm','txt','htm','php','js','css','other');
        // 移动权限
        $this->moveOpArr = array('gif','jpg','svg','flash','zip','exe','mp3','wmv','rm','txt','htm','js','css','other');
        // 允许新增/编辑扩展名文件
        $this->editExt = array('htm','js','css','txt');
        // 过滤php危险函数
        $this->disableFuns = ['phpinfo'];
    }

    /**
     * 编辑文件
     *
     * @access    public
     * @param     string  $filename  文件名
     * @param     string  $activepath  当前路径
     * @param     string  $content  文件内容
     * @return    string
     */
    public function editFile($filename, $activepath = '', $content = '')
    {
        $security = tpSetting('security');
        if (empty($security['security_ask_open']) || empty($security['security_answer'])) {
            return '需要开启密保问题设置';
        } else {
            $admin_id = session('?admin_id') ? (int)session('admin_id') : 0;
            $admin_info = Db::name('admin')->field('admin_id,last_ip')->where(['admin_id'=>$admin_id])->find();
            // 当前管理员二次安全验证过的IP地址
            $security_answerverify_ip = !empty($security['security_answerverify_ip']) ? $security['security_answerverify_ip'] : '-1';
            // 同IP不验证
            if (empty($admin_info) || $admin_info['last_ip'] != $security_answerverify_ip) {
                return '出于安全考虑，请勿非法越过密保答案验证';
            }  
        }

        if (!filename_preg_match()) {
            return '文件名称含有非法入侵字符！';
        }

        $fileinfo = pathinfo($filename);
        $ext = strtolower($fileinfo['extension']);
        $filename = trim($fileinfo['filename'], '.').'.'.$fileinfo['extension'];

        /*不允许越过指定最大级目录的文件编辑*/
        $tmp_max_dir = preg_replace("#\/#i", "\/", $this->maxDir);
        if (!preg_match("#^".$tmp_max_dir."#i", $activepath)) {
            return '没有操作权限！';
        }
        /*--end*/

        /*允许编辑的文件类型*/
        if (!in_array($ext, $this->editExt)) {
            return '只允许操作文件类型如下：'.implode('|', $this->editExt);
        }
        /*--end*/

        $file = $this->baseDir."$activepath/$filename";
        if (!is_writable(dirname($file))) {
            return "请把模板文件目录设置为可写入权限！";
        }
        if ('htm' == $ext) {
            $content = htmlspecialchars_decode($content, ENT_QUOTES);
            if (preg_match('#<([^?]*)\?php#i', $content) || preg_match('#<\?(\s*)=#i', $content) || (preg_match('#<\?#i', $content) && preg_match('#\?>#i', $content)) || preg_match('#\{eyou\:php([^\}]*)\}#i', $content) || preg_match('#\{php([^\}]*)\}#i', $content) || preg_match('#(\s+)language(\s*)=(\s*)("|\')?php("|\')?#i', $content)) {
                return "模板里不允许有php语法，为了安全考虑，请通过FTP工具进行编辑上传。";
            }
            foreach ($this->disableFuns as $key => $val) {
                $val_new = msubstr($val, 0, 1).'-'.msubstr($val, 1);
                $content = preg_replace("/(@)?".$val."(\s*)\(/i", "{$val_new}(", $content);
            }
        }
        $fp = fopen($file, "w");
        fputs($fp, $content);
        fclose($fp);
        return true;
    }

    /**
     * 上传文件
     *
     * @param     string  $dirname  新目录
     * @param     string  $activepath  当前路径
     * @param     boolean  $replace  是否替换
     * @param     string  $type  文件类型：图片image , 附件file , 视频media
     */
    public function upload($fileElementId, $activepath = '', $replace = false, $type = 'image')
    {
        $retData = [];
        $file = request()->file($fileElementId);
        if (is_object($file) && !is_array($file)) {
            $retData = $this->uploadfile($file, $activepath, $replace, $type);
        } 
        else if (!is_object($file) && is_array($file)) {
            $fileArr = $file;
            $i = 0;
            $j = 0;
            foreach ($fileArr as $key => $fileObj) {
                if (empty($fileObj)) {
                    continue;
                }
                $res = $this->uploadfile($fileObj, $activepath, $replace, $type);
                if(!empty($res['code']) && $res['code'] == 1) {
                    $i++;
                } else {
                    $j++;
                }
            }

            if ($j == 0) {
                $retData['code'] = 0;
                $retData['msg'] = "上传失败 $i 个文件到: $activepath";
            } else {
                $retData['code'] = 1;
                $retData['msg'] = "上传成功！";
            }
        }

        return $retData;
    }

    /**
     * 自定义上传
     *
     * @param     object  $file  文件对象
     * @param     string  $activepath  当前路径
     * @param     boolean  $replace  是否替换
     * @param     string  $type  文件类型：图片image , 附件file , 视频media
     */
    public function uploadfile($file, $activepath = '', $replace = false, $type = 'image')
    {
        $validate = array();

        /*文件类型限制*/
        switch ($type) {
            case 'image':
                $validate_ext = tpCache('basic.image_type');
                break;

            case 'file':
                $validate_ext = tpCache('basic.file_type');
                break;

            case 'media':
                $validate_ext = tpCache('basic.media_type');
                break;
            
            default:
                $validate_ext = tpCache('basic.image_type');
                break;
        }
        $validate['ext'] = explode('|', $validate_ext);
        /*--end*/

        /*文件大小限制*/
        $validate_size = tpCache('basic.file_size');
        if (!empty($validate_size)) {
            $validate['size'] = $validate_size * 1024 * 1024; // 单位为b
        }
        /*--end*/

        /*上传文件验证*/
        if (!empty($validate)) {
            $is_validate = $file->check($validate);
            if ($is_validate === false) {
                return ['code'=>0, 'msg'=>$file->getError()];
            }   
        }
        /*--end*/

        $savePath = !empty($activepath) ? trim($activepath, '/') : UPLOAD_PATH.'temp';
        if (!file_exists($savePath)) {
            tp_mkdir($savePath);
        }

        if (false == $replace) {
            $fileinfo = $file->getInfo();
            $filename = pathinfo($fileinfo['name'], PATHINFO_BASENAME); //获取上传文件名
        } else {
            $filename = $replace;
        }
        $fileExt = pathinfo($filename, PATHINFO_EXTENSION); //获取上传文件扩展名
        if (!in_array($fileExt, $validate['ext'])) {
            return ['code'=>0, 'msg'=>'上传文件后缀不允许'];
        }

        // 使用自定义的文件保存规则
        $info = $file->move($savePath, $filename, true);
        if($info){
            return ['code'=>1, 'msg'=>'上传成功'];
        }else{
            return ['code'=>0, 'msg'=>$file->getError()];
        }
    }

    /**
     * 当前目录下的文件列表
     */
    public function getDirFile($directory, $activepath = '',  &$arr_file = array()) {

        if (!file_exists($directory)) {
            return false;
        }

        $fileArr = $dirArr = $parentArr = array();

        $mydir = dir($directory);
        while(false !== $file = $mydir->read())
        {
            $filesize = $filetime = $intro = '';
            $filemine = 'file';

            if($file != "." && $file != ".." && !is_dir("$directory/$file"))
            {
                @$filesize = filesize("$directory/$file");
                @$filesize = format_bytes($filesize);
                @$filetime = filemtime("$directory/$file");
            }

            if ($file == '.') 
            {
                continue;
            } 
            else if($file == "..") 
            {
                if($activepath == "" || $activepath == $this->maxDir) {
                    continue;
                }
                $parentArr = array(
                    array(
                        'filepath'  => preg_replace("#[\/][^\/]*$#i", "", $activepath),
                        'filename'  => '上级目录',
                        'filesize'  => '',
                        'filetime'  => '',
                        'filemine'  => 'dir',
                        'filetype'  => 'dir2',
                        'icon'      => 'file_topdir.gif',
                        'intro'  => '（当前目录：'.$activepath.'）',
                    ),
                );
                continue;
            } 
            else if(is_dir("$directory/$file"))
            {
                if(preg_match("#^_(.*)$#i", $file)) continue; #屏蔽FrontPage扩展目录和linux隐蔽目录
                if(preg_match("#^\.(.*)$#i", $file)) continue;
                $file_info = array(
                    'filepath'  => $activepath.'/'.$file,
                    'filename'  => $file,
                    'filesize'  => '',
                    'filetime'  => '',
                    'filemine'  => 'dir',
                    'filetype'  => 'dir',
                    'icon'      => 'dir.gif',
                    'intro'     => '',
                );
                array_push($dirArr, $file_info);
                continue;
            }
            else if(preg_match("#\.(gif|png)#i",$file))
            {
                $filemine = 'image';
                $filetype = 'gif';
                $icon = 'gif.gif';
            }
            else if(preg_match("#\.(jpg|jpeg|bmp|webp)#i",$file))
            {
                $filemine = 'image';
                $filetype = 'jpg';
                $icon = 'jpg.gif';
            }
            else if(preg_match("#\.(svg)#i",$file))
            {
                $filemine = 'image';
                $filetype = 'svg';
                $icon = 'jpg.gif';
            }
            else if(preg_match("#\.(swf|fla|fly)#i",$file))
            {
                $filetype = 'flash';
                $icon = 'flash.gif';
            }
            else if(preg_match("#\.(zip|rar|tar.gz)#i",$file))
            {
                $filetype = 'zip';
                $icon = 'zip.gif';
            }
            else if(preg_match("#\.(exe)#i",$file))
            {
                $filetype = 'exe';
                $icon = 'exe.gif';
            }
            else if(preg_match("#\.(mp3|wma)#i",$file))
            {
                $filetype = 'mp3';
                $icon = 'mp3.gif';
            }
            else if(preg_match("#\.(wmv|api)#i",$file))
            {
                $filetype = 'wmv';
                $icon = 'wmv.gif';
            }
            else if(preg_match("#\.(rm|rmvb)#i",$file))
            {
                $filetype = 'rm';
                $icon = 'rm.gif';
            }
            else if(preg_match("#\.(txt|inc|pl|cgi|asp|xml|xsl|aspx|cfm)#",$file))
            {
                $filetype = 'txt';
                $icon = 'txt.gif';
            }
            else if(preg_match("#\.(htm|html)#i",$file))
            {
                $filetype = 'htm';
                $icon = 'htm.gif';
            }
            else if(preg_match("#\.(php)#i",$file))
            {
                $filetype = 'php';
                $icon = 'php.gif';
            }
            else if(preg_match("#\.(js)#i",$file))
            {
                $filetype = 'js';
                $icon = 'js.gif';
            }
            else if(preg_match("#\.(css)#i",$file))
            {
                $filetype = 'css';
                $icon = 'css.gif';
            }
            else
            {
                $filetype = 'other';
                $icon = 'other.gif';
            }

            $file_info = array(
                'filepath'  => $activepath.'/'.$file,
                'filename'  => $file,
                'filesize'  => $filesize,
                'filetime'  => $filetime,
                'filemine'  => $filemine,
                'filetype'  => $filetype,
                'icon'      => $icon,
                'intro'     => $intro,
            );
            array_push($fileArr, $file_info);
        }
        $mydir->close();

        $arr_file = array_merge($parentArr, $dirArr, $fileArr);

        return $arr_file;
    }

    /**
     * 将冒号符反替换为反斜杠，适用于IIS服务器在URL上的双重转义限制
     * @param string $filepath 相对路径
     * @param string $replacement 目标字符
     * @param boolean $is_back false为替换，true为还原
     */
    public function replace_path($activepath, $replacement = ':', $is_back = false)
    {
        $activepath = replace_path($activepath, $replacement, $is_back);
        $activepath = preg_replace('/<(\w+)([^\>]*)>([^<]*)<\/(\w+)>/i', '', $activepath);
        $activepath = preg_replace('/(<|>|;|\(|\)|\!)/i', '', $activepath);
        return $activepath;
    }
}