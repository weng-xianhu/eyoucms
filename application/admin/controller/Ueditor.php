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

use common\util\File;
use think\Db;

/**
 * Class UeditorController
 * @package admin\Controller
 */
class Ueditor extends Base
{
    private $image_type = '';
    private $sub_name = array('date', 'Ymd');
    private $imageExt = '';
    private $savePath = 'allimg/';
    private $nowFileName = '';
    private $type_id = 0; // 图片分组
    private $compressConf = [];
    public $admin_id = 0;

    public function __construct()
    {
        parent::__construct();
        
        //header('Access-Control-Allow-Origin: http://www.baidu.com'); //设置http://www.baidu.com允许跨域访问
        //header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); //设置允许的跨域header
        
        date_default_timezone_set("Asia/Shanghai");
        
        // 安全修复：验证savepath参数，防止路径穿越攻击
        $savePath = input('savepath', 'allimg');
        // 移除路径穿越字符和危险字符
        $savePath = str_replace(array('..', '/', '\\', "\0", ':'), '', $savePath);
        // 只允许字母、数字、下划线和中划线
        $savePath = preg_replace('/[^a-zA-Z0-9_\-]/', '', $savePath);
        // 如果过滤后为空，使用默认值
        if (empty($savePath)) {
            $savePath = 'allimg';
        }
        $this->savePath = $savePath . '/';

        $this->nowFileName = input('nowfilename', '');
        if (empty($this->nowFileName)) {
            $this->nowFileName = secure_random_string(16, true);
        }
        
        error_reporting(E_ERROR | E_WARNING);
        
        header("Content-Type: text/html; charset=utf-8");

        $this->imageExt = config('global.image_ext');
        $this->image_type = tpCache('basic.image_type');
        $this->image_type = !empty($this->image_type) ? str_replace('|', ',', $this->image_type) : $this->imageExt;
        $this->type_id = input('param.type_id/d');
        $this->admin_id = (int)session('admin_id');
        $compress = input('param.compress/s');
        if (!empty($compress)) {
            $this->compressConf = explode('-', $compress);
            $this->compressConf[0] = !empty($this->compressConf[0]) ? intval($this->compressConf[0]) : 0;
            $this->compressConf[1] = !empty($this->compressConf[1]) ? intval($this->compressConf[1]) : 0;
            $this->compressConf[2] = !empty($this->compressConf[2]) ? intval($this->compressConf[2]) : 75;
        }
    }
    
    public function index() {
        $CONFIG2 = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("./public/plugins/Ueditor/php/config.json")), true);
        
        /*---------------百度编辑器配置文件与后台设置关联 start----------------*/
        $CONFIG2['imageMaxSize'] = $CONFIG2['scrawlMaxSize'] = $CONFIG2['catcherMaxSize'] = $CONFIG2['videoMaxSize'] = $CONFIG2['fileMaxSize'] = $CONFIG2['fileMaxSize'] = intval(tpCache('basic.file_size') * 1024 * 1024);

        $media_type = tpCache('basic.media_type');
        $media_type = explode('|', $media_type);
        foreach ($media_type as $key => $val) {
            if (!in_array('.'.$val, $CONFIG2['videoAllowFiles'])) {
                array_push($CONFIG2['videoAllowFiles'], '.'.$val);
            }
            if (!in_array('.'.$val, $CONFIG2['fileAllowFiles'])) {
                array_push($CONFIG2['fileAllowFiles'], '.'.$val);
            }
        }

        $image_type = tpCache('basic.image_type');
        $image_type = explode('|', $image_type);
        foreach ($image_type as $key => $val) {
            if (!in_array('.'.$val, $CONFIG2['fileAllowFiles'])) {
                array_push($CONFIG2['fileAllowFiles'], '.'.$val);
            }
        }

        $file_type = tpCache('basic.file_type');
        $file_type = explode('|', $file_type);
        foreach ($file_type as $key => $val) {
            if (!in_array('.'.$val, $CONFIG2['fileAllowFiles'])) {
                array_push($CONFIG2['fileAllowFiles'], '.'.$val);
            }
        }
        /*---------------百度编辑器配置文件与系统设置关联 end----------------*/

        $action = $_GET['action'];
        switch ($action) {
            case 'config':
                $result =  json_encode($CONFIG2);
                break;
            /* 上传图片 */
            case 'uploadimage':
                $fieldName = $CONFIG2['imageFieldName'];
                $result = $this->upFile($fieldName);
                break;
            /* 上传涂鸦 */
            case 'uploadscrawl':
                $config = array(
                    "pathFormat" => $CONFIG2['scrawlPathFormat'],
                    "maxSize" => $CONFIG2['scrawlMaxSize'],
                    "allowFiles" => $CONFIG2['scrawlAllowFiles'],
                    "oriName" => "scrawl.png"
                );
                $fieldName = $CONFIG2['scrawlFieldName'];
                $base64 = "base64";
                $result = $this->upBase64($config,$fieldName);
                break;
            /* 上传视频 */
            case 'uploadvideo':
                $fieldName = $CONFIG2['videoFieldName'];
                $result = $this->upFile($fieldName);
                /*同步到第三方对象存储空间*/
                $result = json_decode($result, true);
                $bucket_data = SynImageObjectBucket($result['url']);
                $result = array_merge($result, $bucket_data);
                $result = json_encode($result);
                /*end*/
                break;
            /* 上传文件 */
            case 'uploadfile':
                $fieldName = $CONFIG2['fileFieldName'];
                $result = $this->upFile($fieldName);
                break;
            /* 列出图片 */
            case 'listimage':
                $allowFiles = $CONFIG2['imageManagerAllowFiles'];
                $listSize = $CONFIG2['imageManagerListSize'];
                $path = $CONFIG2['imageManagerListPath'];
                $get =$_GET;
                $result =$this->fileList($allowFiles,$listSize,$get);
                break;
            /* 列出文件 */
            case 'listfile':
                $allowFiles = $CONFIG2['fileManagerAllowFiles'];
                $listSize = $CONFIG2['fileManagerListSize'];
                $path = $CONFIG2['fileManagerListPath'];
                $get = $_GET;
                $result = $this->fileList($allowFiles,$listSize,$get);
                break;
            /* 抓取远程文件 */
            case 'catchimage':
                $config = array(
                    "pathFormat" => $CONFIG2['catcherPathFormat'],
                    "maxSize" => $CONFIG2['catcherMaxSize'],
                    "allowFiles" => $CONFIG2['catcherAllowFiles'],
                    "oriName" => "remote.png"
                );
                $fieldName = $CONFIG2['catcherFieldName'];
                /* 抓取远程图片 */
                $list = array();
                isset($_POST[$fieldName]) ? $source = $_POST[$fieldName] : $source = $_GET[$fieldName];
                
                /*编辑器七牛云/OSS等同步*/
                $weappList = Db::name('weapp')->where([
                    'status'    => 1,
                ])->cache(true, EYOUCMS_CACHE_TIME, 'weapp')
                ->getAllWithIndex('code');
                /* END */
                
                foreach($source as $imgUrl){
                    $info = json_decode($this->saveRemote($config,$imgUrl),true);

                    /*同步到第三方对象存储空间*/
                    $bucket_data = SynImageObjectBucket($info['url'], $weappList);
                    $info = array_merge($info, $bucket_data);
                    /*end*/

                    array_push($list, array(
                        "state" => $info["state"],
                        "url" => $info["url"],
                        "size" => $info["size"],
                        "title" => htmlspecialchars($info["title"]),
                        "original" => str_replace("&amp;", "&", htmlspecialchars($info["original"])),
                        // "source" => htmlspecialchars($imgUrl)
                        "source" => str_replace("&amp;", "&", htmlspecialchars($imgUrl))
                    ));
                }

                $result = json_encode(array(
                    'state' => !empty($list) ? 'SUCCESS':'ERROR',
                    'list' => $list
                ));
                break;
            default:
                $result = json_encode(array(
                    'state' => '请求地址出错'
                ));
                break;
        }

        /* 输出结果 */
        if(isset($_GET["callback"])){
            if(preg_match("/^[\w_]+$/", $_GET["callback"])){
                echo htmlspecialchars($_GET["callback"]).'('.$result.')';
            }else{
                echo json_encode(array(
                    'state' => 'callback参数不合法'
                ));
            }
        }else{
            echo $result;
        }
    }
    
    //上传文件
    public function upFile($fieldName = 'file') {
        if (!IS_POST) {
            return json_encode(['state' =>'非法上传']);
        }

        $file = request()->file($fieldName);
        if (empty($file)) $file = request()->file('upfile');
        if (empty($file)) $file = request()->file('upload');

        if(empty($file)){
            if (!@ini_get('file_uploads')) {
                return json_encode(['state' =>'请检查空间是否开启文件上传功能！']);
            } else {
                return json_encode(['state' =>'ERROR，请上传文件']);
            }
        }
        $error = $file->getError();
        if(!empty($error)){
            return json_encode(['state' =>$error]);
        }
        $original = $file->getInfo('name');

        // 获取配置的文件大小限制
        $max_file_size = intval(tpCache('basic.file_size') * 1024 * 1024);

        // 获取允许的文件类型
        $fileExt = '';
        $image_type = tpCache('basic.image_type');
        !empty($image_type) && $fileExt .= '|'.$image_type;
        $file_type = tpCache('basic.file_type');
        !empty($file_type) && $fileExt .= '|'.$file_type;
        $media_type = tpCache('basic.media_type');
        !empty($media_type) && $fileExt .= '|'.$media_type;
        // 保存原始的附件类型配置，因为后面 $file_type 会被重新赋值
        $file_type_config = $file_type;
        $fileExt = !empty($fileExt) ? str_replace('||', '|', $fileExt) : config('global.image_ext');
        $fileExt = str_replace('|', ',', trim($fileExt, '|'));
        $result = $this->validate(
            ['file' => $file], 
            ['file'=>'fileSize:'.$max_file_size.'|fileExt:'.$fileExt],
            ['file.fileSize' => '上传文件过大','file.fileExt'=>'上传文件后缀名必须为'.$fileExt]
        );

        // 获取文件后缀
        $file_ext = pathinfo($original, PATHINFO_EXTENSION);
        $file_ext = strtolower($file_ext);
    
        // 二次校验文件类型
        if (in_array($file_ext, explode('|', $image_type))) {
            // 使用getimagesize二次验证
            $imageInfo = @getimagesize($file->getInfo('tmp_name'));
            if (false === $imageInfo) {
                return json_encode(['state' => '文件不是有效的图像']);
            }
            
            // 使用imagecreatefromstring重新生成图片，去除恶意代码
            $img = @imagecreatefromstring(file_get_contents($file->getInfo('tmp_name')));
            if (false === $img) {
                return json_encode(['state' => '图像文件已损坏']);
            }
            
            $allowImageTypes = [];
            if (defined('IMAGETYPE_GIF')) $allowImageTypes[IMAGETYPE_GIF] = 'gif';
            if (defined('IMAGETYPE_JPEG')) $allowImageTypes[IMAGETYPE_JPEG] = 'jpeg';
            if (defined('IMAGETYPE_PNG')) $allowImageTypes[IMAGETYPE_PNG] = 'png';
            if (defined('IMAGETYPE_PSD')) $allowImageTypes[IMAGETYPE_PSD] = 'psd';
            if (defined('IMAGETYPE_BMP')) $allowImageTypes[IMAGETYPE_BMP] = 'bmp';
            if (defined('IMAGETYPE_JPC')) $allowImageTypes[IMAGETYPE_JPC] = 'jpc';
            if (defined('IMAGETYPE_JP2')) $allowImageTypes[IMAGETYPE_JP2] = 'jp2';
            if (defined('IMAGETYPE_JPX')) $allowImageTypes[IMAGETYPE_JPX] = 'jpx';
            if (defined('IMAGETYPE_JB2')) $allowImageTypes[IMAGETYPE_JB2] = 'jb2';
            if (defined('IMAGETYPE_IFF')) $allowImageTypes[IMAGETYPE_IFF] = 'iff';
            if (defined('IMAGETYPE_WBMP')) $allowImageTypes[IMAGETYPE_WBMP] = 'wbmp';
            if (defined('IMAGETYPE_XBM')) $allowImageTypes[IMAGETYPE_XBM] = 'xbm';
            if (defined('IMAGETYPE_ICO')) $allowImageTypes[IMAGETYPE_ICO] = 'ico';
            if (defined('IMAGETYPE_WEBP')) $allowImageTypes[IMAGETYPE_WEBP] = 'webp';
            if (defined('IMAGETYPE_AVIF')) $allowImageTypes[IMAGETYPE_AVIF] = 'avif';
    
            $realImageType = $imageInfo[2];
            if (empty($allowImageTypes[$realImageType])) {
                imagedestroy($img);
                return json_encode(['state' => '不支持的图片格式']);
            }
    
            $type = $allowImageTypes[$realImageType];
    
            // 根据不同格式保存图片
            if ($type == 'jpeg') {
                // JPEG格式设置隔行扫描，提高加载体验
                imageinterlace($img, true);
                imagejpeg($img, $file->getInfo('tmp_name'), 100);
            } elseif ($type == 'png') {
                // PNG格式保存完整的alpha通道信息（透明度）
                imagesavealpha($img, true);
                // PNG质量范围0-9
                imagepng($img, $file->getInfo('tmp_name'), 9);
            } else {
                // 其他格式使用动态函数调用
                // 安全验证：只允许调用已知的图片处理函数
                $allowedImageFunctions = array('imagegif', 'imagebmp', 'imagewbmp', 'imagexbm', 'imagewebp', 'imageavif');
                $fun = 'image' . $type;
                if (function_exists($fun) && in_array($fun, $allowedImageFunctions)) {
                    $fun($img, $file->getInfo('tmp_name'));
                } else {
                    imagedestroy($img);
                    return json_encode(['state' => '服务器不支持' . strtoupper($type) . '格式']);
                }
            }
            
            imagedestroy($img);
        }

        if (in_array($file_ext, explode('|', $image_type))) {
            if (false === check_illegal($file->getInfo('tmp_name'), false, $file_ext)) {
                $result = base64_decode(config('global.mm_tips'));
            }
        }
        else if (in_array($file_ext, explode('|', $file_type))) {
            $this->savePath = preg_replace('/^allimg\//i', 'soft/', $this->savePath);
        }
        else if (in_array($file_ext, explode('|', $media_type))) {
            $this->savePath = preg_replace('/^allimg\//i', 'media/', $this->savePath);
        }
        
        if (true !== $result || empty($file)) {
            $state = "ERROR" . $result;
            return json_encode(['state' =>$state]);
        }

        // 移动到框架应用根目录/public/uploads/ 目录下
        $this->savePath = $this->savePath.getUploadSubDir();

        // 检查上传目录权限
        $uploadDir = UPLOAD_PATH . $this->savePath;
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }
        if (!is_dir($uploadDir) || !is_writable(UPLOAD_PATH)) {
            return json_encode(array('state' => 'uploads目录没有写入权限'));
        }

        // 使用自定义的文件保存规则
        $file_uploadname_open = (int)tpCache('global.file_uploadname_open');
        if ($file_uploadname_open == 1) { // by 小秋
            $SaveName = substr(str_shuffle('abcdefgAhijklRmnopqGrstuvwxyz'), 0, 1).mt_rand(1,9999).'-'.$original;
            $info = $file->move(UPLOAD_PATH.$this->savePath,$SaveName);
        } else {
            $info = $file->rule(function ($file) {
                return $this->admin_id.'-'.dd2char(date("ymdHis").mt_rand(100,999)); // 使用自定义的文件保存规则
            })->move(UPLOAD_PATH.$this->savePath);
        }

        if (!empty($info)) {
            $file_ext = pathinfo($file->getInfo('name'), PATHINFO_EXTENSION);
            $file_ext = strtolower($file_ext); // 确保扩展名为小写
            $return_url = '/'.UPLOAD_PATH.$this->savePath.$info->getSaveName();
            $data = array(
                'state' => 'SUCCESS',
                'url' => ROOT_DIR.$return_url,
                'title' => '',//$info->getSaveName(),
                'original' => ($file_uploadname_open == 1) ? $info->getSaveName() : $file->getInfo('name'),
                'time' => date("Y-m-d H:i:s"),
                'type' => '.' . $info->getExtension(),
                'size' => $info->getSize(),
                'mime'     => $file->getInfo('type'),
            );

            //图片加水印
            $file_type = $file->getInfo('type');
            $fileextArr = explode(',', $this->image_type);
            if (stristr($file_type, 'image') && 'ico' != $file_ext) {
                print_water($data['url']);
                /*同步到第三方对象存储空间*/
                $bucket_data = SynImageObjectBucket($data['url']);
                $data = array_merge($data, $bucket_data);
                /*end*/
            } else {
                // 非图片（视频/附件）也支持云存储同步
                $unneed_syn = input('unneed_syn/d');
                if (empty($unneed_syn)) {
                    $relative_url = '/' . ltrim($return_url, '/');
                    $bucket_data = SynImageObjectBucket($relative_url, [], $file);
                    if (!empty($bucket_data) && is_array($bucket_data)) {
                        $data = array_merge($data, $bucket_data);
                        if (!empty($bucket_data['url'])) {
                            $data['url'] = $bucket_data['url'];
                        }
                    }
                }
            }
            
            /*-------------------------保存上传图片记录 start-----------------------*/
            $img_info = [];
            if (in_array($file_ext, ['jpg','jpeg','png','bmp','gif','ico','webp'])) {
                $img_info = @getimagesize('.'.$return_url);
                $width = isset($img_info[0]) ? $img_info[0] : 0;
                $height = isset($img_info[1]) ? $img_info[1] : 0;
                $mime = '';
                if (!is_http_url($data['url'])) {
                    $mime = isset($img_info['mime']) ? $img_info['mime'] : $info->getMime();
                }

                $data['time'] = getTime();
                $data['width'] = $width;
                $data['height'] = $height;

                $addData = [
                    'aid'         =>0,
                    'type_id'     =>$this->type_id,
                    'group_code'  => 'image',
                    'image_url'   =>$data['url'],
                    'title'       => $data['original'],
                    'intro'       => '',
                    'width'       => $width,
                    'height'      => $height,
                    'filesize'    => $data['size'],
                    'mime'        => $mime,
                    'users_id'    => (int)session('admin_info.syn_users_id'),
                    'sort_order'  => 100,
                    'add_time'    => getTime(),
                    'update_time' => getTime(),
                ];
                $img_id = Db::name('uploads')->insertGetId($addData);
                $data['img_id'] = $img_id;
                
            } else if (in_array($file_ext, explode('|', $media_type))) {
                $addData = [
                    'aid'         =>0,
                    'type_id'     =>$this->type_id,
                    'group_code'  => 'video',
                    'image_url'   =>$data['url'],
                    'title'       => $data['original'],
                    'intro'       => '',
                    'width'       => 0,
                    'height'      => 0,
                    'filesize'    => $data['size'],
                    'mime'        => $data['mime'],
                    'users_id'    => (int)session('admin_info.syn_users_id'),
                    'sort_order'  => 100,
                    'add_time'    => getTime(),
                    'update_time' => getTime(),
                ];
                Db::name('uploads')->insertGetId($addData);
            } else if (!empty($file_type_config) && in_array($file_ext, explode('|', $file_type_config))) {
                // 附件文件保存到数据库
                $addData = [
                    'aid'         =>0,
                    'type_id'     =>$this->type_id,
                    'group_code'  => 'file',
                    'image_url'   =>$data['url'],
                    'title'       => $data['original'],
                    'intro'       => '',
                    'width'       => 0,
                    'height'      => 0,
                    'filesize'    => $data['size'],
                    'mime'        => $data['mime'],
                    'users_id'    => (int)session('admin_info.syn_users_id'),
                    'sort_order'  => 100,
                    'add_time'    => getTime(),
                    'update_time' => getTime(),
                ];
                try {
                    $file_id = Db::name('uploads')->insertGetId($addData);
                    if ($file_id) {
                        $data['img_id'] = $file_id; // 为了兼容前端代码，也返回 img_id
                    }
                } catch (\Exception $e) {
                    // 记录错误但不影响文件上传
                    // \think\Log::write('附件保存到数据库失败: ' . $e->getMessage(), 'error');
                }
            }
            /*-------------------------保存上传图片记录 end-----------------------*/
        } else {
            $data = array('state' => 'ERROR'.$info->getError());
        }
        
        if (1 == $this->editor['editor_select']) {
            return json_encode($data);
        } else if (2 == $this->editor['editor_select']) {
            $CKEditorFuncNum = input('param.CKEditorFuncNum');
            if ($CKEditorFuncNum === '' || $CKEditorFuncNum === null) {
                // CKEditorFuncNum 为空时，返回 JSON 格式
                return json_encode($data);
            }
            $CKEditorFuncNum = intval($CKEditorFuncNum);
            $message = '';
            $str = '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('.$CKEditorFuncNum.', \''.$data['url'].'\', \''.$message.'\');</script>';
            exit($str);
        }
    }

    public function upFileAjax($fieldName = 'file') {
        $result = $this->upFile($fieldName);
        exit($result);
    }

    // 列出图片
    private function fileList($allowFiles, $listSize, $get) {
        $dirname = './'.UPLOAD_PATH;
        $allowFiles = substr(str_replace(".","|",join("",$allowFiles)),1);
        /* 获取参数 */
        $size = isset($get['size']) ? htmlspecialchars($get['size']) : $listSize;
        $start = isset($get['start']) ? htmlspecialchars($get['start']) : 0;
        $end = $start + $size;
        /* 获取文件列表 */
        $path = $dirname;
        $files = $this->getFiles($path,$allowFiles);
        if(empty($files)){
            return json_encode(array(
                "state" => "no match file",
                "list" => array(),
                "start" => $start,
                "total" => count($files)
            ));
        }
        /* 获取指定范围的列表 */
        $len = count($files);
        for($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
            $list[] = $files[$i];
        }

        /* 返回数据 */
        $result = json_encode(array(
            "state" => "SUCCESS",
            "list" => $list,
            "start" => $start,
            "total" => count($files)
        ));

        return $result;
    }

    /*
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param array $files
     * @return array
    */
    private function getFiles($path,$allowFiles,&$files = array()){
        if(!is_dir($path)) return null;
        if(substr($path,strlen($path)-1) != '/') $path .= '/';
        $handle = opendir($path);
            
        while(false !== ($file = readdir($handle))){
            if($file != '.' && $file != '..'){
                $path2 = $path.$file;
                if(is_dir($path2)){
                    $this->getFiles($path2,$allowFiles,$files);
                }else{
                    if(preg_match("/\.(".$allowFiles.")$/i",$file)){
                        $files[] = array(
                            'url' => substr($path2,1),
                            'mtime' => filemtime($path2)
                        );
                    }
                }
            }
        }       
        return $files;
    }

    //抓取远程图片
    private function saveRemote($config,$fieldName){
        $imgUrl = htmlspecialchars($fieldName);
        $imgUrl = str_replace("&amp;","&",$imgUrl);
        $imgUrl = preg_replace('/#/', '', $imgUrl);

        //http开头验证
        if(strpos($imgUrl,"http") !== 0){
            $data=array(
                'state' => '链接不是http链接',
            );
            return json_encode($data);
        }

        // 检查是否为内网 IP
        $validateResult = validateRemoteUrl($imgUrl);
        if ($validateResult['code'] == 0) {
            $data = array(
                'state' => $validateResult['msg'],
            );
            return json_encode($data);
        }

        //获取请求头并检测死链
        $heads = @get_headers($imgUrl, 1, get_remote_stream_context($imgUrl));
        if (empty($heads)) {
            $data=array(
                'state' => '链接不可用',
            );
            return json_encode($data);
        } else if(!(stristr($heads[0],"200") && !stristr($heads[0],"304"))){
            $data=array(
                'state' => '链接不可用',
            );
            return json_encode($data);
        }
        //格式验证(扩展名验证和Content-Type验证)
        if(preg_match("/^http(s?):\/\/(mmbiz.qpic.cn|thirdwx.qlogo.cn)\/(.*)/", $imgUrl) != 1){
            $fileType = strtolower(strrchr($imgUrl,'.'));
            if(!in_array($fileType,$config['allowFiles']) || (isset($heads['Content-Type']) && !stristr($heads['Content-Type'],"image"))){
                $data=array(
                    'state' => '链接contentType不正确',
                );
                return json_encode($data);
            }
        } else {
            $data=array(
                'state' => '微信公众号图片请点击远程本地化处理！',
            );
            return json_encode($data);
        }

        //打开输出缓冲区并获取远程图片
        ob_start();
        $context = get_remote_stream_context($imgUrl);
        readfile($imgUrl,false,$context);
        $img = ob_get_contents();
        ob_end_clean();
        preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/",$imgUrl,$m);

        $dirname = './'.UPLOAD_PATH.'ueditor/'.getUploadSubDir();
        $file['oriName'] = $m ? $m[1] : "";
        $file['filesize'] = strlen($img);
        $file['ext'] = strtolower(strrchr($config['oriName'],'.'));
        $file['name'] = $this->admin_id.'-'.dd2char(date("ymdHis").mt_rand(100,999)).$file['ext'];
        $file['fullName'] = $dirname.$file['name'];
        $fullName = $file['fullName'];

        //检查文件大小是否超出限制
        if($file['filesize'] >= ($config["maxSize"])){
            $data=array(
                'state' => '文件大小超出网站限制',
            );
            return json_encode($data);
        }

        //创建目录失败
        if(!file_exists($dirname) && !mkdir($dirname,0755,true)){
            $data=array(
                'state' => '目录创建失败',
            );
            return json_encode($data);
        }else if(!is_writeable($dirname)){
            $data=array(
                'state' => '目录没有写权限',
            );
            return json_encode($data);
        }

        //移动文件
        if(!(file_put_contents($fullName, $img) && file_exists($fullName))){ //移动失败
            $data=array(
                'state' => '写入文件内容错误',
            );
            return json_encode($data);
        }else{ //移动成功
            $return_url = substr($file['fullName'],1);
            $data = array(
                'state' => 'SUCCESS',
                'url' => ROOT_DIR.$return_url, // 支持子目录
                'title' => $file['name'],
                'original' => $file['oriName'],
                'type' => $file['ext'],
                'size' => $file['filesize'],
            );
            
            /*-------------------------保存上传图片记录 start-----------------------*/
            $img_info = @getimagesize('.'.$return_url);
            $width = isset($img_info[0]) ? $img_info[0] : 0;
            $height = isset($img_info[1]) ? $img_info[1] : 0;
            $mime = isset($img_info['mime']) ? $img_info['mime'] : "";
            $data['time'] = getTime();
            $data['width'] = $width;
            $data['height'] = $height;

            $addData = [
                'aid'         =>0,
                'type_id'     =>$this->type_id,
                'group_code'  => 'image',
                'image_url'   =>$data['url'],
                'title'       => $data['original'],
                'intro'       => '',
                'width'       => $width,
                'height'      => $height,
                'filesize'    => $data['size'],
                'mime'        => $mime,
                'users_id'    => (int)session('admin_info.syn_users_id'),
                'sort_order'  => 100,
                'add_time'    => getTime(),
                'update_time' => getTime(),
            ];
            $img_id = Db::name('uploads')->insertGetId($addData);
            $data['img_id'] = $img_id;
            /*-------------------------保存上传图片记录 end-----------------------*/

            print_water($data['url']); // 添加水印
        }
        return json_encode($data);
    }

    /*
     * 处理base64编码的图片上传
     * 例如：涂鸦图片上传
    */
    private function upBase64($config,$fieldName){
        if (!IS_POST) {
            return json_encode(['state' =>'非法上传']);
        }

        $base64Data = $_POST[$fieldName];
        $img = base64_decode($base64Data);

        // 验证Base64解码后的内容是否为真实图片，防止任意文件写入
        $allowedMimes = array('image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp');
        $detectedMime = false;
        if (class_exists('finfo')) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $detectedMime = $finfo->buffer($img);
        } elseif (function_exists('getimagesizefromstring')) {
            $imageInfo = @getimagesizefromstring($img);
            if ($imageInfo !== false && isset($imageInfo['mime'])) {
                $detectedMime = $imageInfo['mime'];
            }
        }
        if ($detectedMime === false || !in_array($detectedMime, $allowedMimes)) {
            return json_encode(array('state' => '非法文件类型，仅允许上传图片'));
        }

        $dirname = './'.UPLOAD_PATH.'ueditor/'.getUploadSubDir();
        $file['filesize'] = strlen($img);
        $file['oriName'] = $config['oriName'];
        $file['ext'] = strtolower(strrchr($config['oriName'],'.'));
        $file['name'] = uniqid().$file['ext'];
        $file['fullName'] = $dirname.$file['name'];
        $fullName = $file['fullName'];

        //检查文件大小是否超出限制
        if($file['filesize'] >= ($config["maxSize"])){
            $data=array(
                'state' => '文件大小超出网站限制',
            );
            return json_encode($data);
        }

        //创建目录失败
        if(!file_exists($dirname) && !mkdir($dirname,0755,true)){
            $data=array(
                'state' => '目录创建失败',
            );
            return json_encode($data);
        }else if(!is_writeable($dirname)){
            $data=array(
                'state' => '目录没有写权限',
            );
            return json_encode($data);
        }

        //移动文件
        if(!(file_put_contents($fullName, $img) && file_exists($fullName))){ //移动失败
            $data=array(
                'state' => '写入文件内容错误',
            );
        }else{ //移动成功          
            $data=array(
                'state' => 'SUCCESS',
                'url' => substr($file['fullName'],1),
                'title' => $file['name'],
                'original' => $file['oriName'],
                'type' => $file['ext'],
                'size' => $file['filesize'],
            );
        }
        
        return json_encode($data);
    }

    /**
     * @function imageUp
     */
    public function imageUp()
    {
        if (!IS_POST) {
            $return_data['state'] = '非法上传';
            respose($return_data,'json');
        }
        
        $max_file_size = intval(tpCache('basic.file_size') * 1024 * 1024);
        // 上传图片框中的描述表单名称，
        $pictitle = input('pictitle');
        $dir = input('dir');
        $title = htmlspecialchars($pictitle , ENT_QUOTES);        
        $path = htmlspecialchars($dir, ENT_QUOTES);
        //$input_file ['upfile'] = $info['Filedata'];  一个是上传插件里面来的, 另外一个是 文章编辑器里面来的
        // 获取表单上传文件
        $file = request()->file('file');
        empty($file) && $file = request()->file('upfile');
        if (empty($file) || !@ini_get('file_uploads')) {
            $return_data['state'] = '请检查空间是否开启文件上传功能！';
            respose($return_data,'json');
        }
        $original = $file->getInfo('name');
        // 图片后缀名
        $file_ext = pathinfo($original, PATHINFO_EXTENSION);
        // ico图片文件不进行验证
        if ($file_ext != 'ico') {
            if('webp' == $file_ext && version_compare(PHP_VERSION,'7.1.0','<')) {
                $result = "php7.1.0或以上版本才支持上传{$file_ext}";
            } else {
                $result = $this->validate(
                    ['file' => $file], 
                    ['file' => 'image|fileSize:' . $max_file_size . '|fileExt:' . $this->image_type],
                    [
                        'file.image'    => '上传文件必须为图片',
                        'file.fileSize' => '上传图片过大',
                        'file.fileExt'  => '上传图片后缀名必须为' . $this->image_type
                    ]
                );
            }
        } else {
            $result = true;
        }

        if (false === check_illegal($file->getInfo('tmp_name'), false, $file_ext)) {
            $result = base64_decode(config('global.mm_tips'));
        }

        $return_url = '';
        if (true !== $result || empty($file)) {
            $state = "ERROR：" . $result;
            respose(['state' =>$state],'json');
        } else {
            if ('adminlogo/' == $this->savePath) {
                $savePath = 'public/static/admin/logo/';
            } else if ('loginlogo/' == $this->savePath) {
                $savePath = 'public/static/admin/login/';
            } else if ('loginbgimg/' == $this->savePath) {
                $savePath = 'public/static/admin/loginbg/';
            } else {
                $savePath = UPLOAD_PATH . $this->savePath . getUploadSubDir();
            }

            // 检查上传目录权限
            if (!is_dir($savePath)) {
                @mkdir($savePath, 0755, true);
            }
            if (!is_dir($savePath) || !is_writable(dirname($savePath))) {
                respose(array('state' => 'uploads目录没有写入权限'), 'json');
            }

            // 移动到框架应用根目录/public/uploads/ 目录下
            $file_uploadname_open = (int)tpCache('global.file_uploadname_open');
            if ($file_uploadname_open == 1) { // by 小秋
                $SaveName = substr(str_shuffle('abcdefgAhijklRmnopqGrstuvwxyz'), 0, 1).mt_rand(1,9999).'-'.$original;
                $info = $file->move($savePath,$SaveName);
            } else {
                $info = $file->rule(function ($file) {
                    return $this->admin_id.'-'.dd2char(date("ymdHis").mt_rand(100,999)); // 使用自定义的文件保存规则
                })->move($savePath);
            }

            if ($info) {
                $state = "SUCCESS";
            } else {
                $state = "ERROR" . $file->getError();
            }
            $return_url = '/' . $savePath . $info->getSaveName();

            // 是否要压缩图片
            if (!empty($this->compressConf[0])) {
                $return_url = func_thumb_img($return_url, $this->compressConf[0], $this->compressConf[1], $this->compressConf[2]);
            }

            $return_data['url'] = ROOT_DIR . $return_url; // 支持子目录

            // 重新制作一张图片，抹去任何可能有危害的数据
            // $image       = \think\Image::open('.'.$return_url);
            // $image->save('.'.$return_url, null, 100);
            
            /*-------------------------保存上传图片记录 start-----------------------*/
            $img_info = @getimagesize('.'.$return_url);
            $width = isset($img_info[0]) ? $img_info[0] : 0;
            $height = isset($img_info[1]) ? $img_info[1] : 0;
            $mime = isset($img_info['mime']) ? $img_info['mime'] : $info->getMime();
            $filesize = $info->getSize();

            $return_data['time'] = getTime();
            $return_data['width'] = $width;
            $return_data['height'] = $height;
            /*-------------------------保存上传图片记录 end-----------------------*/
        }

        // 添加水印
        if ($state == 'SUCCESS') {
            if (pathinfo($original, PATHINFO_EXTENSION) != 'ico') {
                $is_water = input('param.is_water/d');
                if(!in_array($this->savePath, ['adminlogo/','loginlogo/','loginbgimg/']) && $is_water == 1) {
                    print_water($return_url);
                }
            }
        }

        // 返回数据
        $return_data['title']    = $title;
        $return_data['original'] = $original;
        $return_data['state']    = $state;
        $return_data['path']     = $path;
        unset($info);
        $unneed_syn = input('unneed_syn/d');
        //水印传值为1  不需要上传到第三方
        if (empty($unneed_syn)){
            /*同步到第三方对象存储空间*/
            $bucket_data = SynImageObjectBucket($return_url, [], $file);
            $return_data = array_merge($return_data, $bucket_data);
            /*end*/
        }

        /*-------------------------保存上传图片记录 start-----------------------*/
        $addData = [
            'aid'         =>0,
            'type_id'     =>$this->type_id,
            'group_code'  => 'image',
            'image_url'   =>$return_data['url'],
            'title'       => $original,
            'intro'       => '',
            'width'       => $width,
            'height'      => $height,
            'filesize'    => $filesize,
            'mime'        => $mime,
            'users_id'    => (int)session('admin_info.syn_users_id'),
            'sort_order'  => 100,
            'add_time'    => getTime(),
            'update_time' => getTime(),
        ];
        $img_id = Db::name('uploads')->insertGetId($addData);
        $return_data['img_id'] = $img_id;
        /*-------------------------保存上传图片记录 end-----------------------*/

        respose($return_data,'json');
    }
    
    /**
     * app文件上传
     */
    public function appFileUp()
    {
        if (!IS_POST) {
            $return_data['state'] = '非法上传';
            respose($return_data);
        }

        $max_file_size = intval(tpCache('basic.file_size') * 1024 * 1024);
        $path = UPLOAD_PATH.'soft/'.getUploadSubDir();

        // 检查上传目录权限
        if (!is_dir($path)) {
            @mkdir($path, 0755, true);
        }
        if (!is_dir($path) || !is_writable(UPLOAD_PATH)) {
            respose(array('state' => 'uploads目录没有写入权限'));
        }

        //$input_file  ['upfile'] = $info['Filedata'];  一个是上传插件里面来的, 另外一个是 文章编辑器里面来的
        // 获取表单上传文件
        $file = request()->file('Filedata');
        if (empty($file)) {
            $file = request()->file('upfile');    
        }
        
        $result = $this->validate(
            ['file2' => $file], 
            ['file2'=>'fileSize:'.$max_file_size.'|fileExt:apk,ipa,pxl,deb'],
            ['file2.fileSize' => '上传文件过大', 'file2.fileExt' => '上传文件后缀名必须为：apk,ipa,pxl,deb']                    
           );
        if (true !== $result || empty($file)) {            
            $state = "ERROR" . $result;
        } else {
            $original = $file->getInfo('name');
            // 使用自定义的文件保存规则
            $file_uploadname_open = (int)tpCache('global.file_uploadname_open');
            if ($file_uploadname_open == 1) { // by 小秋
                $SaveName = substr(str_shuffle('abcdefgAhijklRmnopqGrstuvwxyz'), 0, 1).mt_rand(1,9999).'-'.$original;
                $info = $file->move($path,$SaveName);
            } else {
                $info = $file->rule(function ($file) {
                    return $this->admin_id.'-'.dd2char(date("ymdHis").mt_rand(100,999)); // 使用自定义的文件保存规则
                })->move($path);
            }

            if ($info) {
                $state = "SUCCESS";                         
            } else {
                $state = "ERROR" . $file->getError();
            }
            $return_data['url'] = $path.$info->getSaveName();            
        }
        
        $return_data['title'] = 'app文件';
        $return_data['original'] = ''; // 这里好像没啥用 暂时注释起来
        $return_data['state'] = $state;
        $return_data['path'] = $path;        

        respose($return_data);
    }

    private function uhash( $file ) {
        $fragment = 65536;

        $rh = fopen($file, 'rb');
        $size = filesize($file);

        $part1 = fread( $rh, $fragment );
        fseek($rh, $size-$fragment);
        $part2 = fread( $rh, $fragment);
        fclose($rh);

        return md5( $part1.$part2 );
    }

    //上传文件
    public function DownloadUploadFile(){
        if (!IS_POST) {
            echo json_encode(['msg' => '非法上传']);exit;
        }

        header('Content-Type: text/html; charset=utf-8');
        // 获取定义的上传最大参数
        $max_file_size = intval(tpCache('basic.file_size') * 1024 * 1024);
        // 获取上传的文件信息
        $files = request()->file();
        // 若获取不到则定义为空
        $file  = !empty($files['file']) ? $files['file'] : '';

        /*判断上传文件是否存在错误*/
        if(empty($file)){
            echo json_encode(['msg' => '文件过大或文件已损坏！']);exit;
        }
        $error = $file->getError();
        if(!empty($error)){
            echo json_encode(['msg' => $error]);exit;
        }

        $file_type = tpCache('basic.file_type');
        $file_type = !empty($file_type) ? str_replace('|', ',', $file_type) : 'zip,gz,rar,iso,doc,xls,ppt,wps,txt,docx';

        $result = $this->validate(
            ['file' => $file],
            ['file'=>'fileSize:'.$max_file_size.'|fileExt:'.$file_type],
            ['file.fileSize' => '上传文件过大','file.fileExt'=>'上传文件后缀名必须为'.$file_type]
        );
        if (true !== $result || empty($file)) {
            echo json_encode(['msg' => $result]);exit;
        }
        /*--end*/

        // 移动到框架应用根目录/public/uploads/ 目录下
        $this->savePath = $this->savePath.getUploadSubDir();
        // 定义文件名
        $fileName    = $file->getInfo('name');
        // 提取文件名后缀
        $file_ext    = pathinfo($fileName, PATHINFO_EXTENSION);
        // 提取出文件名，不包括扩展名
        $newfileName = preg_replace('/\.([^\.]+)$/', '', $fileName);
        // 过滤文件名.\/的特殊字符，防止利用上传漏洞
        $newfileName = preg_replace('#(\\\|\/|\.)#i', '', $newfileName);
        // 过滤后的新文件名
        $fileName = $newfileName.'.'.$file_ext;
        // 中文转码
        $this->fileName = iconv("utf-8","gb2312//IGNORE",$fileName);

        // 检查上传目录权限
        $uploadDir = UPLOAD_PATH . $this->savePath;
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }
        if (!is_dir($uploadDir) || !is_writable(UPLOAD_PATH)) {
            echo json_encode(array('msg' => 'uploads目录没有写入权限'));
            exit;
        }

        // 使用自定义的文件保存规则
        $file_uploadname_open = (int)tpCache('global.file_uploadname_open');
        if ($file_uploadname_open == 1) { // by 小秋
            $SaveName = substr(str_shuffle('abcdefgAhijklRmnopqGrstuvwxyz'), 0, 1).mt_rand(1,9999).'-'.$fileName;
            $info = $file->move(UPLOAD_PATH.$this->savePath,$SaveName);
        } else {
            $info = $file->rule(function ($file) {
                return $this->admin_id.'-'.dd2char(date("ymdHis").mt_rand(100,999)); // 使用自定义的文件保存规则
            })->move(UPLOAD_PATH.$this->savePath);
        }
        $aiEditor = input('aiEditor/d', 0);
        if (1 === intval($aiEditor)) {
            return [
                'errorCode' => 0,
                'data' => [
                    'href' => ROOT_DIR . '/' . UPLOAD_PATH . $this->savePath . $info->getSaveName(),
                    'fileName' => ($file_uploadname_open == 1) ? $info->getSaveName() : $fileName,
                ],
            ];
        } else {
            if ($info) {
                // 拼装数据存入session
                $file_path = UPLOAD_PATH.$this->savePath.$info->getSaveName();
                $file_url = '/' . UPLOAD_PATH.$this->savePath.$info->getSaveName();
                
                /* 同步到第三方对象存储空间（如七牛、OSS、COS、AWS S3） */
                $unneed_syn = input('unneed_syn/d');
                if (empty($unneed_syn)) {
                    // 传入相对路径并携带文件对象，保持与图片上传一致
                    $relative_url = '/' . ltrim($file_path, '/');
                    $bucket_data = SynImageObjectBucket($relative_url, [], $file);
                    if (!empty($bucket_data) && is_array($bucket_data)) {
                        if (!empty($bucket_data['url'])) {
                            $file_url = $bucket_data['url'];
                        }
                    }
                }
                /* end */
                
                // 兼容前端 getting-started.js 期望的数据格式
                $return_data = array(
                    'error' => array(
                        'code'     => 0,
                        'path'     => $file_url,
                        'mime'     => $file->getInfo('type'),
                        'uhash'    => $this->uhash($file_path),
                        'md5file'  => md5_file($file_path),
                    ),
                    // 同时保留原有格式，以便其他调用方式兼容
                    'code'      => 1,
                    'msg'       => '上传成功',
                    'file_url'  => $file_url,
                    'file_mime' => $file->getInfo('type'),
                    'file_name' => ($file_uploadname_open == 1) ? $info->getSaveName() : $fileName,
                    'file_ext'  => '.' . $file_ext,
                    'file_size' => $info->getSize(),
                    'uhash'     => $this->uhash($file_path),
                    'md5file'   => md5_file($file_path),
                );
            }else{
                $return_data = array(
                    'error' => array(
                        'code' => 1,
                        'msg'  => $info->getError()
                    ),
                    'msg' => $info->getError()
                );
            }
            echo json_encode($return_data);
        }
    }

    //上传文件
    public function DownloadUploadFileAjax()
    {
        if (!IS_POST) {
            $res = ['code' => 0, 'msg' => '非法上传'];
            respose($res);
        }

        // 获取上传的文件信息
        $file = request()->file('file');
        /*判断上传文件是否存在错误*/
        if (empty($file)) {
            $res = ['code' => 0, 'msg' => '文件过大或文件已损坏！'];
            respose($res);
        }
        $error = $file->getError();
        if (!empty($error)) {
            $res = ['code' => 0, 'msg' => $error];
            respose($res);
        }

        $file_type = tpCache('basic.file_type');
        $file_type = !empty($file_type) ? str_replace('|', ',', $file_type) : 'zip,gz,rar,iso,doc,xls,ppt,wps,txt,docx';
        $max_file_size = intval(tpCache('basic.file_size') * 1024 * 1024);

        $result  = $this->validate(
            ['file' => $file],
            ['file'=>'fileSize:'.$max_file_size.'|fileExt:'.$file_type],
            ['file.fileSize' => '上传文件过大','file.fileExt'=>'上传文件后缀名必须为'.$file_type]
        );

        if (true !== $result || empty($file)) {
            $res = ['code' => 0, 'msg' => $result];
            respose($res);
        }
        /*--end*/

        // 移动到框架应用根目录/public/uploads/ 目录下
        $this->savePath = "soft/" . getUploadSubDir();
        // 定义文件名
        $fileName = $file->getInfo('name');
        // 提取文件名后缀
        $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);

        // 检查上传目录权限
        $uploadDir = UPLOAD_PATH . $this->savePath;
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }
        if (!is_dir($uploadDir) || !is_writable(UPLOAD_PATH)) {
            respose(array('code' => 0, 'msg' => 'uploads目录没有写入权限'));
        }

        // 使用自定义的文件保存规则
        $file_uploadname_open = (int)tpCache('global.file_uploadname_open');
        if ($file_uploadname_open == 1) { // by 小秋
            $SaveName = substr(str_shuffle('abcdefgAhijklRmnopqGrstuvwxyz'), 0, 1).mt_rand(1,9999).'-'.$fileName;
            $info = $file->move(UPLOAD_PATH.$this->savePath,$SaveName);
        } else {
            $info = $file->rule(function ($file) {
                return $this->admin_id.'-'.dd2char(date("ymdHis").mt_rand(100,999)); // 使用自定义的文件保存规则
            })->move(UPLOAD_PATH.$this->savePath);
        }
        if ($info) {
            // 拼装数据存入session
            $file_path = UPLOAD_PATH . $this->savePath . $info->getSaveName();
            $file_url = ROOT_DIR.'/' . UPLOAD_PATH . $this->savePath . $info->getSaveName();
            
            /* 同步到第三方对象存储空间（如七牛、OSS、COS、AWS S3） */
            $unneed_syn = input('unneed_syn/d');
            if (empty($unneed_syn)) {
                // 传入相对路径并携带文件对象，保持与图片上传一致
                $relative_url = '/' . ltrim($file_path, '/');
                $bucket_data = SynImageObjectBucket($relative_url, [], $file);
                if (!empty($bucket_data) && is_array($bucket_data)) {
                    if (!empty($bucket_data['url'])) {
                        $file_url = $bucket_data['url'];
                    }
                }
            }
            /* end */
            
            // 兼容前端 getting-started.js 期望的数据格式
            $return = array(
                'error' => array(
                    'code'     => 0,
                    'path'     => $file_url,
                    'mime'     => $file->getInfo('type'),
                    'uhash'    => $this->uhash($file_path),
                    'md5file'  => md5_file($file_path),
                ),
                // 同时保留原有格式，以便其他调用方式兼容
                'code'      => 1,
                'msg'       => '上传成功',
                'file_url'  => $file_url,
                'file_mime' => $file->getInfo('type'),
                'file_name' => ($file_uploadname_open == 1) ? $info->getSaveName() : $fileName,
                'file_ext'  => '.' . $file_ext,
                'file_size' => $info->getSize(),
                'uhash'     => $this->uhash($file_path),
                'md5file'   => md5_file($file_path),
            );
        } else {
            $return = array(
                'error' => array(
                    'code' => 1,
                    'msg'  => $file->getError()
                ),
                'code' => 0,
                'msg'  => $file->getError()
            );
        }
        respose($return);
    }

    // 上传视频
    public function upVideo()
    {
        if (!IS_POST) {
            respose(['state' => '非法上传']);
        }

        $file     = request()->file('file');
        if (empty($file)) {
            if (!@ini_get('file_uploads')) {
                respose(['state' => '请检查空间是否开启文件上传功能！']);
            } else {
                respose(['state' => 'ERROR，空间限制上传大小！']);
            }
        }
        $error = $file->getError();
        if (!empty($error)) {
            respose(['state' => $error]);
        }

        $media_type                 = tpCache('basic.media_type');
        $media_type = !empty($media_type) ? str_replace('|', ',', $media_type) : config('global.media_ext');
        if (empty($media_type)) {
            respose(['state' => 'ERROR，请设置上传多媒体文件类型！']);
        } else {
            $media_type = str_replace('|', ',', $media_type);
        }
        $max_file_size = intval(tpCache('basic.file_size') * 1024 * 1024);
        $result  = $this->validate(
            ['file' => $file],
            ['file' => 'fileSize:' . $max_file_size . '|fileExt:' . $media_type],
            ['file.fileSize' => '上传视频过大', 'file.fileExt' => '上传视频后缀名必须为' . $media_type]
        );
        if (true !== $result || empty($file)) {
            $state = "ERROR：" . $result;
            respose(['state' => $state]);
        }
        // 定义文件名
        $fileName    = $file->getInfo('name');
        // 提取出文件名，不包括扩展名
        $newfileName = preg_replace('/\.([^\.]+)$/', '', $fileName);
        // 过滤文件名.\/的特殊字符，防止利用上传漏洞
        $newfileName = preg_replace('#(\\\|\/|\.)#i', '', $newfileName);

        //获取视频时长start
        vendor('getid3.getid3');
        // 实例化
        $getID3       = new \getID3();  //实例化类
        $tmp_name = $file->getInfo('tmp_name');
        $ThisFileInfo = $getID3->analyze($tmp_name); //分析文件，$path为音频文件的地址
        $fileduration = empty($ThisFileInfo['playtime_seconds']) ? 0 : intval($ThisFileInfo['playtime_seconds']); //这个获得的便是音频文件的时长
        //获取视频时长end

        // 移动到框架应用根目录/public/uploads/ 目录下
        $this->savePath = preg_replace('/^allimg\//i', 'media/', $this->savePath);
        $this->savePath = $this->savePath.getUploadSubDir();

        // 检查上传目录权限
        $uploadDir = UPLOAD_PATH . $this->savePath;
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }
        if (!is_dir($uploadDir) || !is_writable(UPLOAD_PATH)) {
            respose(array('state' => 'uploads目录没有写入权限'));
        }

        // 使用自定义的文件保存规则
        $file_uploadname_open = (int)tpCache('global.file_uploadname_open');
        if ($file_uploadname_open == 1) { // by 小秋
            $SaveName = substr(str_shuffle('abcdefgAhijklRmnopqGrstuvwxyz'), 0, 1).mt_rand(1,9999).'-'.$newfileName;
            $info = $file->move(UPLOAD_PATH.$this->savePath,$SaveName);
        } else {
            $info = $file->rule(function ($file) {
                return $this->admin_id.'-'.dd2char(date("ymdHis").mt_rand(100,999)); // 使用自定义的文件保存规则
            })->move(UPLOAD_PATH.$this->savePath);
        }
        $aiEditor = input('aiEditor/d', 0);
        if (1 === intval($aiEditor)) {
            return [
                'errorCode' => 0,
                'data' => [
                    'src' => ROOT_DIR . '/' . UPLOAD_PATH . $this->savePath . $info->getSaveName(),
                    'poster' => '',
                ],
            ];
        } else {
            if ($info) {
                $file_path = UPLOAD_PATH.$this->savePath.$info->getSaveName();
                $data = array(
                    'state'    => 'SUCCESS',
                    'url'      => ROOT_DIR . '/' . $file_path,
                    'time'     => $fileduration,
                    'title'    => $newfileName,
                    'original' => $info->getSaveName(),
                    'type'     => '.' . $info->getExtension(),
                    'size'     => $info->getSize(),
                    'mime'     => $file->getInfo('type'),
                    'width'    => 0,
                    'height'   => 0,
                );

                /* 同步到第三方对象存储空间（如七牛） */
                $unneed_syn = input('unneed_syn/d');
                if (empty($unneed_syn)) {
                    // 传入相对路径并携带文件对象，保持与图片上传一致
                    $relative_url = '/' . ltrim($file_path, '/');
                    $bucket_data = SynImageObjectBucket($relative_url, [], $file);
                    if (!empty($bucket_data) && is_array($bucket_data)) {
                        $data = array_merge($data, $bucket_data);
                        if (!empty($bucket_data['url'])) {
                            $data['url'] = $bucket_data['url'];
                        }
                    }
                }
                /* end */

                $addData = [
                    'aid'         =>0,
                    'type_id'     =>$this->type_id,
                    'group_code'  => 'video',
                    'image_url'   =>$data['url'],
                    'title'       => $data['original'],
                    'intro'       => '',
                    'width'       => $data['width'],
                    'height'      => $data['height'],
                    'filesize'    => $data['size'],
                    'mime'        => $data['mime'],
                    'users_id'    => (int)session('admin_info.syn_users_id'),
                    'sort_order'  => 100,
                    'add_time'    => getTime(),
                    'update_time' => getTime(),
                ];
                Db::name('uploads')->insertGetId($addData);
            } else {
                $data = json_encode(array('state' => 'ERROR' . $info->getError()));
            }
            respose($data);
        }
    }

    /**
     * AI智能助手
     * @return [type] [description]
     */
    public function ai_helper_check()
    {
        $driver = 'DeepSeek';
        $api_key = $api_model = $api_url = '';
        $api_option = [];
        $promptText = input('param.promptText/s');
        $promptText = htmlspecialchars_decode($promptText);

        $row = Db::name('weapp')->where(['code'=>'Ai'])->find();
        if (empty($row)) {
            $this->error("请先安装插件[Ai智能创作平台]，且填写{$driver}的AI配置");
        } else if ($row['status'] != 1) {
            $this->error("请先启用插件[Ai智能创作平台]，且填写{$driver}的AI配置");
        }

        $aiLogic = new \weapp\Ai\logic\AiLogic;
        $aiData = $aiLogic->getAiData();
        if ('阿里云百炼' == $driver) {
            $api_model = empty($aiData['aliyun_text_model']) ? '' : $aiData['aliyun_text_model'];
            $api_key = empty($aiData['aliyun_api_key']) ? '' : $aiData['aliyun_api_key'];
            $api_url = 'https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions';
        } else if ('文心一言' == $driver) {
            $api_model = empty($aiData['baidu_model']) ? '' : $aiData['baidu_model'];
            $api_key = empty($aiData['baidu_api_key']) ? '' : $aiData['baidu_api_key'];
            $secret_key = empty($aiData['baidu_secret_key']) ? '' : $aiData['baidu_secret_key'];
            $access_token = $this->getContentAccessToken($api_key, $secret_key);
            if (!empty($access_token)) {
                $api_url = $this->baiduAiPortUrlArr($api_model, $access_token);
            }
            $api_option = [
                'body' => [
                    'model' => $api_model,
                    'messages' => [
                        ['role' => 'user', 'content' => $promptText]
                    ],
                    'stream' => true,
                    'temperature' => !empty($aiData['baidu_temperature']) ? floatval($aiData['baidu_temperature']) : 0,
                ],
            ];
        } else if ('豆包' == $driver) {
            $api_model = empty($aiData['doubao_api_model']) ? '' : $aiData['doubao_api_model'];
            $api_key = empty($aiData['doubao_api_key']) ? '' : $aiData['doubao_api_key'];
            $api_url = 'https://ark.cn-beijing.volces.com/api/v3/chat/completions';
        } else if ('DeepSeek' == $driver) {
            $api_model = 'deepseek-chat';
            $api_key = empty($aiData['deepseek_api_key']) ? '' : $aiData['deepseek_api_key'];
            $api_url = 'https://api.deepseek.com/chat/completions';
        } else if ('ModStart' == $driver) {
            $api_model = 'default';
            $api_key = 'default';
            $api_option = [
                'body' => [
                    'prompt' => $promptText,
                ],
            ];
        }

        if (empty($api_key) || empty($api_model) || empty($api_url)) {
            $this->error("请在插件[Ai智能创作平台]填写{$driver}的AI配置");
        }

        $data = [
            'ai_config' => [
                'driver' => $driver,
                'model' => $api_model,
                'key' => $api_key,
                'url' => $api_url,
                'option' => $api_option,
            ],
        ];
        $this->success('ok', null, $data);
    }

    // 获取百度AI验证token
    private function getContentAccessToken($api_key, $secret_key)
    {
        // $contentAccessToken = \think\Cache::get('contentAccessToken');
        if (true || empty($contentAccessToken)) {
            // 请求access_token
            $postData = [
                'grant_type' => 'client_credentials',
                'client_id' => $api_key,
                'client_secret' => $secret_key
            ];
            $headers = ['Content-Type: application/json'];
            $jsonData = httpRequest('https://aip.baidubce.com/oauth/2.0/token', 'POST', http_build_query($postData), $headers, 300000);
            $jsonData = json_decode($jsonData, true);
            // 请求失败
            if (!empty($jsonData['error']) && 'invalid_request' == $jsonData['error']) $this->error('无效的请求<br/>' . $jsonData['error_description']);
            if (!empty($jsonData['error']) && 'invalid_client' == $jsonData['error']) $this->error('接口返回信息<br/>' . $jsonData['error_description']);
            // 空access_token则提示
            if (empty($jsonData['access_token'])) $this->error('请在插件[Ai智能创作平台]填写文心一言的AI配置');
            // 重置access_token
            $contentAccessToken = $jsonData['access_token'];
            // 存入缓存
            // \think\Cache::set('contentAccessToken', $contentAccessToken, 86400);
        }

        return $contentAccessToken;
    }

    /**
     * 文心一言的模型对应接口URL
     * @param  string $baiduModel         [description]
     * @param  string $contentAccessToken [description]
     * @return [type]                     [description]
     */
    private function baiduAiPortUrlArr($baiduModel = '', $contentAccessToken = '')
    {
        $portUrlArr = [
            // 文本接口--免费
            'ERNIE-Lite-8K' => 'https://aip.baidubce.com/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/ernie-lite-8k?access_token=' . $contentAccessToken,
            'ERNIE-Speed-128K' => 'https://aip.baidubce.com/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/ernie-speed-128k?access_token=' . $contentAccessToken,
            // 文本接口
            'ERNIE-Bot' => 'https://aip.baidubce.com/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/completions?access_token=' . $contentAccessToken,
            'ERNIE-Bot-turbo' => 'https://aip.baidubce.com/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/eb-instant?access_token=' . $contentAccessToken,
            'ERNIE-Bot 4.0' => 'https://aip.baidubce.com/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/completions_pro?access_token=' . $contentAccessToken,
            'Llama-2-13b-chat' => 'https://aip.baidubce.com/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/llama_2_13b?access_token=' . $contentAccessToken,
            'Llama-2-70b-chat' => 'https://aip.baidubce.com/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/llama_2_70b?access_token=' . $contentAccessToken,
            'ChatGLM2-6B-32K' => 'https://aip.baidubce.com/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/chatglm2_6b_32k?access_token=' . $contentAccessToken,
        ];

        return !empty($baiduModel) && !empty($portUrlArr[$baiduModel]) ? $portUrlArr[$baiduModel] : '';
    }
}