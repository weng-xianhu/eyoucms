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

namespace app\user\controller;

class Uploadify extends Base {

    private $image_type = '';
    private $sub_name = '';
    private $fileExt = 'jpg,png,gif,jpeg,bmp,ico';
    private $savePath = 'allimg/';
    private $upload_path = '';
    
    /**
     * 析构函数
     */
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Shanghai");
        $this->savePath = input('savepath/s','allimg').'/';
        error_reporting(E_ERROR | E_WARNING);
        header("Content-Type: text/html; charset=utf-8");
        
        $this->sub_name = date('Ymd/');
        $this->image_type = tpCache('basic.image_type');
        $this->image_type = !empty($this->image_type) ? str_replace('|', ',', $this->image_type) : 'jpg,gif,png,bmp,jpeg,ico';
        $this->upload_path = UPLOAD_PATH.'user/'.$this->users_id.'/';
    }

    public function upload()
    {
        $func = input('func');
        $path = input('path','allimg');
        $num = input('num/d', '1');
        $default_size = intval(tpCache('basic.file_size') * 1024 * 1024); // 单位为b
        $size = input('size/d'); // 单位为kb
        $size = empty($size) ? $default_size : $size*1024;
        $info = array(
            'num'=> $num,
            'title' => '',          
            'upload' =>url('Uploadify/imageUp',array('savepath'=>$path,'pictitle'=>'banner','dir'=>'images')),
            'fileList'=>url('Uploadify/fileList',array('path'=>$path)),
            'size' => $size,
            'type' => $this->image_type,
            'input' => input('input'),
            'func' => empty($func) ? 'undefined' : $func,
        );
        $this->assign('info',$info);
        return $this->fetch('./application/user/template/uploadify/upload.htm');
    }
    
    /*
     * 删除上传的图片
     */
    public function delupload()
    {
        if (IS_POST) {
            $action = input('post.action/s','del');                
            $filename= input('post.filename/s');
            $filename= empty($filename) ? input('url') : $filename;
            $filename= str_replace('../','',$filename);
            $filename= trim($filename,'.');
            $filename= trim($filename,'/');
            if(eyPreventShell($filename) && $action=='del' && !empty($filename) && file_exists($filename)){
                $fileArr = explode('/', $filename);
                if ($fileArr[2] != $this->users_id) {
                    return false;
                }
                $filetype = preg_replace('/^(.*)\.(\w+)$/i', '$2', $filename);
                $phpfile = strtolower(strstr($filename,'.php'));  //排除PHP文件
                $size = getimagesize($filename);
                $fileInfo = explode('/',$size['mime']);
                if($fileInfo[0] != 'image' || $phpfile || !in_array($filetype, explode(',', config('global.image_ext')))){
                    exit;
                }
                if(@unlink($filename)){
                    echo 1;
                }else{
                    echo 0;
                }  
                exit;
            }
        }
    }
    
    // public function fileList(){
    //     /* 判断类型 */
    //     $type = input('type','Images');
    //     switch ($type){
    //         /* 列出图片 */
    //         case 'Images' : $allowFiles = str_replace(',', '|', $this->image_type);break;
        
    //         case 'Flash' : $allowFiles = 'flash|swf';break;
        
    //         /* 列出文件 */
    //         default : 
    //         {
    //             $file_type = tpCache('basic.file_type');
    //             $media_type = tpCache('basic.media_type');
    //             $allowFiles = $file_type.'|'.$media_type;
    //         }
    //     }

    //     $listSize = 102400000;
        
    //     $key = empty($_GET['key']) ? '' : $_GET['key'];
        
    //     /* 获取参数 */
    //     $size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
    //     $start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
    //     $end = $start + $size;
        
    //     $path = input('path','allimg');
    //     if (1 == preg_match('#\.#', $path)) {
    //         echo json_encode(array(
    //             "state" => "路径不符合规范",
    //             "list" => array(),
    //             "start" => $start,
    //             "total" => 0
    //         ));
    //         exit;
    //     }
    //     $path = $this->upload_path.$path;

    //     /* 获取文件列表 */
    //     $files = $this->getfiles($path, $allowFiles, $key);
    //     if (empty($files)) {
    //         echo json_encode(array(
    //             "state" => "没有相关文件",
    //             "list" => array(),
    //             "start" => $start,
    //             "total" => count($files)
    //         ));
    //         exit;
    //     }
        
    //     /* 获取指定范围的列表 */
    //     $len = count($files);
    //     for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
    //         $list[] = $files[$i];
    //     }
        
    //     /* 返回数据 */
    //     $result = json_encode(array(
    //         "state" => "SUCCESS",
    //         "list" => $list,
    //         "start" => $start,
    //         "total" => count($files)
    //     ));
        
    //     echo $result;
    // }

    /**
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param array $files
     * @return array
     */
    // private function getfiles($path, $allowFiles, $key, &$files = array()){
    //     if (!is_dir($path)) return null;
    //     if(substr($path, strlen($path) - 1) != '/') $path .= '/';
    //     $handle = opendir($path);
    //     while (false !== ($file = readdir($handle))) {
    //         if ($file != '.' && $file != '..') {
    //             $path2 = $path . $file;
    //             if (is_dir($path2)) {
    //                 $this->getfiles($path2, $allowFiles, $key, $files);
    //             } else {
    //                 if (preg_match("/\.(".$allowFiles.")$/i", $file) && preg_match("/.*". $key .".*/i", $file)) {
    //                     $files[] = array(
    //                         'url'=> ROOT_DIR.'/'.$path2, // 支持子目录
    //                         'name'=> $file,
    //                         'mtime'=> filemtime($path2)
    //                     );
    //                 }
    //             }
    //         }
    //     }
    //     return $files;
    // }
    
    // public function index(){
        
    //     $CONFIG2 = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("./public/plugins/Ueditor/php/config.json")), true);
    //     $action = $_GET['action'];
        
    //     switch ($action) {
    //         case 'config':
    //             $result =  json_encode($CONFIG2);
    //             break;
    //         /* 上传图片 */
    //         case 'uploadimage':
    //             $fieldName = $CONFIG2['imageFieldName'];
    //             $result = $this->imageUp();
    //             break;
    //         /* 上传涂鸦 */
    //         case 'uploadscrawl':
    //             $config = array(
    //                 "pathFormat" => $CONFIG2['scrawlPathFormat'],
    //                 "maxSize" => $CONFIG2['scrawlMaxSize'],
    //                 "allowFiles" => $CONFIG2['scrawlAllowFiles'],
    //                 "oriName" => "scrawl.png"
    //             );
    //             $fieldName = $CONFIG2['scrawlFieldName'];
    //             $base64 = "base64";
    //             $result = $this->upBase64($config,$fieldName);
    //             break;
    //         /* 上传视频 */
    //         case 'uploadvideo':
    //             $fieldName = $CONFIG2['videoFieldName'];
    //             $result = $this->upFile($fieldName);
    //             break;
    //         /* 上传文件 */
    //         case 'uploadfile':
    //             $fieldName = $CONFIG2['fileFieldName'];
    //             $result = $this->upFile($fieldName);
    //             break;
    //         /* 列出图片 */
    //         case 'listimage':
    //             $allowFiles = $CONFIG2['imageManagerAllowFiles'];
    //             $listSize = $CONFIG2['imageManagerListSize'];
    //             $path = $CONFIG2['imageManagerListPath'];
    //             $get = $_GET;
    //             $result =$this->fileList2($allowFiles,$listSize,$get);
    //             break;
    //         /* 列出文件 */
    //         case 'listfile':
    //             $allowFiles = $CONFIG2['fileManagerAllowFiles'];
    //             $listSize = $CONFIG2['fileManagerListSize'];
    //             $path = $CONFIG2['fileManagerListPath'];
    //             $get = $_GET;
    //             $result = $this->fileList2($allowFiles,$listSize,$get);
    //             break;
    //         /* 抓取远程文件 */
    //         case 'catchimage':
    //             $config = array(
    //                 "pathFormat" => $CONFIG2['catcherPathFormat'],
    //                 "maxSize" => $CONFIG2['catcherMaxSize'],
    //                 "allowFiles" => $CONFIG2['catcherAllowFiles'],
    //                 "oriName" => "remote.png"
    //             );
    //             $fieldName = $CONFIG2['catcherFieldName'];
    //             /* 抓取远程图片 */
    //             $list = array();
    //             isset($_POST[$fieldName]) ? $source = $_POST[$fieldName] : $source = $_GET[$fieldName];
                
    //             foreach($source as $imgUrl){
    //                 $info = json_decode($this->saveRemote($config,$imgUrl),true);
    //                 array_push($list, array(
    //                     "state" => $info["state"],
    //                     "url" => $info["url"],
    //                     "size" => $info["size"],
    //                     "title" => htmlspecialchars($info["title"]),
    //                     "original" => str_replace("&amp;", "&", htmlspecialchars($info["original"])),
    //                     // "source" => htmlspecialchars($imgUrl)
    //                     "source" => str_replace("&amp;", "&", htmlspecialchars($imgUrl))
    //                 ));
    //             }

    //             $result = json_encode(array(
    //                 'state' => !empty($list) ? 'SUCCESS':'ERROR',
    //                 'list' => $list
    //             ));
    //             break;
    //         default:
    //             $result = json_encode(array(
    //                 'state' => '请求地址出错'
    //             ));
    //             break;
    //     }

    //     /* 输出结果 */
    //     if(isset($_GET["callback"])){
    //         if(preg_match("/^[\w_]+$/", $_GET["callback"])){
    //             echo htmlspecialchars($_GET["callback"]).'('.$result.')';
    //         }else{
    //             echo json_encode(array(
    //                 'state' => 'callback参数不合法'
    //             ));
    //         }
    //     }else{
    //         echo $result;
    //     }
    // }
    
    // //上传文件
    // private function upFile($fieldName){
    //     $image_upload_limit_size = intval(tpCache('basic.file_size') * 1024 * 1024);
    //     $file = request()->file($fieldName);
    //     if(empty($file)){
    //         return json_encode(['state' =>'ERROR，请上传文件']);
    //     }
    //     $error = $file->getError();
    //     if(!empty($error)){
    //         return json_encode(['state' =>$error]);
    //     }
    //     $result = $this->validate(
    //         ['file' => $file], 
    //         ['file'=>'fileSize:'.$image_upload_limit_size],
    //         ['file.fileSize' => '上传文件过大']
    //     );
    //     if (true !== $result || empty($file)) {
    //         $state = "ERROR" . $result;
    //         return json_encode(['state' =>$state]);
    //     }

    //     // 移动到框架应用根目录/uploads/ 目录下
    //     $savePath = $this->upload_path.$this->savePath.$this->sub_name;

    //     $ossConfig = tpCache('oss');
    //     if ($ossConfig['oss_switch']) {
    //         //商品图片可选择存放在oss
    //         $object = $savePath.md5(getTime().uniqid(mt_rand(), TRUE)).'.'.pathinfo($file->getInfo('name'), PATHINFO_EXTENSION);
    //         $ossClient = new \app\common\logic\OssLogic;
    //         $return_url = $ossClient->uploadFile($file->getRealPath(), $object);
    //         if (!$return_url) {
    //             $data = array('state' => 'ERROR'.$ossClient->getError());
    //         } else {
    //             $data = array(
    //                 'state'     => 'SUCCESS',
    //                 'url'       => $return_url,
    //                 'title'     => $file->getInfo('name'),
    //                 'original'  => $file->getInfo('name'),
    //                 'type'      => $file->getInfo('type'),
    //                 'size'      => $file->getInfo('size'),
    //             );
    //         }
    //         @unlink($file->getRealPath());
    //         return json_encode($data);
    //     } else {
    //         // 使用自定义的文件保存规则
    //         $info = $file->rule(function ($file) {
    //             return  md5(mt_rand());
    //         })->move($savePath);
    //     }
        
    //     if($info){
    //         $data = array(
    //             'state' => 'SUCCESS',
    //             'url' => '/'.$savePath.$info->getSaveName(),
    //             'title' => $info->getSaveName(),
    //             'original' => $info->getSaveName(),
    //             'type' => '.' . $info->getExtension(),
    //             'size' => $info->getSize(),
    //         );

    //         //图片加水印
    //         if($data['state'] == 'SUCCESS'){
    //             $file_type = $file->getInfo('type');
    //             $file_ext = pathinfo($file->getInfo('name'), PATHINFO_EXTENSION);
    //             $fileextArr = explode(',', $this->fileExt);
    //             if (stristr($file_type, 'image') && 'ico' != $file_ext) {
    //                 $imgresource = ".".$data['url'];
    //                 $image = \think\Image::open($imgresource);
    //                 $water = tpCache('water');
    //                 $return_data['mark_type'] = $water['mark_type'];
    //                 if($water['is_mark']==1 && $image->width()>$water['mark_width'] && $image->height()>$water['mark_height']){
    //                     if($water['mark_type'] == 'text'){
    //                         //$image->text($water['mark_txt'],ROOT_PATH.'public/static/common/font/hgzb.ttf',20,'#000000',9)->save($imgresource);
    //                         $ttf = ROOT_PATH.'public/static/common/font/hgzb.ttf';
    //                         if (file_exists($ttf)) {
    //                             $size = $water['mark_txt_size'] ? $water['mark_txt_size'] : 30;
    //                             $color = $water['mark_txt_color'] ?: '#000000';
    //                             if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
    //                                 $color = '#000000';
    //                             }
    //                             $transparency = intval((100 - $water['mark_degree']) * (127/100));
    //                             $color .= dechex($transparency);
    //                             $image->open($imgresource)->text($water['mark_txt'], $ttf, $size, $color, $water['mark_sel'])->save($imgresource);
    //                             $return_data['mark_txt'] = $water['mark_txt'];
    //                         }
    //                     }else{
    //                         /*支持子目录*/
    //                         $water['mark_img'] = preg_replace('#^(/[/\w]+)?(/public/upload/|/uploads/)#i', '$2', $water['mark_img']); // 支持子目录
    //                         /*--end*/
    //                         //$image->water(".".$water['mark_img'],9,$water['mark_degree'])->save($imgresource);
    //                         $waterPath = "." . $water['mark_img'];
    //                         if (eyPreventShell($waterPath) && file_exists($waterPath)) {
    //                             $quality = $water['mark_quality'] ? $water['mark_quality'] : 80;
    //                             $waterTempPath = dirname($waterPath).'/temp_'.basename($waterPath);
    //                             $image->open($waterPath)->save($waterTempPath, null, $quality);
    //                             $image->open($imgresource)->water($waterTempPath, $water['mark_sel'], $water['mark_degree'])->save($imgresource);
    //                             @unlink($waterTempPath);
    //                         }
    //                     }
    //                 }
    //             }
    //         }

    //         $data['url'] = ROOT_DIR.$data['url']; // 支持子目录
    //     }else{
    //         $data = array('state' => 'ERROR'.$info->getError());
    //     }
    //     return json_encode($data);
    // }

    // //列出图片
    // private function fileList2($allowFiles,$listSize,$get){
    //     $dirname = './'.$this->upload_path;
    //     $allowFiles = substr(str_replace(".","|",join("",$allowFiles)),1);
    //     /* 获取参数 */
    //     $size = isset($get['size']) ? htmlspecialchars($get['size']) : $listSize;
    //     $start = isset($get['start']) ? htmlspecialchars($get['start']) : 0;
    //     $end = $start + $size;
    //     /* 获取文件列表 */
    //     $path = $dirname;
    //     $files = $this->getFiles($path,$allowFiles);
    //     if(empty($files)){
    //         return json_encode(array(
    //             "state" => "没有相关文件",
    //             "list" => array(),
    //             "start" => $start,
    //             "total" => count($files)
    //         ));
    //     }
    //     /* 获取指定范围的列表 */
    //     $len = count($files);
    //     for($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
    //         $list[] = $files[$i];
    //     }

    //     /* 返回数据 */
    //     $result = json_encode(array(
    //         "state" => "SUCCESS",
    //         "list" => $list,
    //         "start" => $start,
    //         "total" => count($files)
    //     ));

    //     return $result;
    // }
    
    // //抓取远程图片
    // private function saveRemote($config,$fieldName){
    //     $imgUrl = htmlspecialchars($fieldName);
    //     $imgUrl = str_replace("&amp;","&",$imgUrl);

    //     //http开头验证
    //     if(strpos($imgUrl,"http") !== 0){
    //         $data=array(
    //             'state' => '链接不是http链接',
    //         );
    //         return json_encode($data);
    //     }
    //     //获取请求头并检测死链
    //     $heads = get_headers($imgUrl);
    //     if(!(stristr($heads[0],"200") && stristr($heads[0],"OK"))){
    //         $data=array(
    //             'state' => '链接不可用',
    //         );
    //         return json_encode($data);
    //     }
    //     //格式验证(扩展名验证和Content-Type验证)
    //     if(preg_match("/^http(s?):\/\/mmbiz.qpic.cn\/(.*)/", $imgUrl) != 1){
    //         $fileType = strtolower(strrchr($imgUrl,'.'));
    //         if(!in_array($fileType,$config['allowFiles']) || (isset($heads['Content-Type']) && stristr($heads['Content-Type'],"image"))){
    //             $data=array(
    //                 'state' => '链接contentType不正确',
    //             );
    //             return json_encode($data);
    //         }
    //     }

    //     //打开输出缓冲区并获取远程图片
    //     ob_start();
    //     $context = stream_context_create(
    //         array('http' => array(
    //             'follow_location' => false // don't follow redirects
    //         ))
    //     );
    //     readfile($imgUrl,false,$context);
    //     $img = ob_get_contents();
    //     ob_end_clean();
    //     preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/",$imgUrl,$m);

    //     $dirname = './'.$this->upload_path.'ueditor/'.$this->sub_name;
    //     $file['oriName'] = $m ? $m[1] : "";
    //     $file['filesize'] = strlen($img);
    //     $file['ext'] = strtolower(strrchr($config['oriName'],'.'));
    //     $file['name'] = uniqid().$file['ext'];
    //     $file['fullName'] = $dirname.$file['name'];
    //     $fullName = $file['fullName'];

    //     //检查文件大小是否超出限制
    //     if($file['filesize'] >= ($config["maxSize"])){
    //         $data=array(
    //             'state' => '文件大小超出网站限制',
    //         );
    //         return json_encode($data);
    //     }

    //     //创建目录失败
    //     if(!file_exists($dirname) && !mkdir($dirname,0777,true)){
    //         $data=array(
    //             'state' => '目录创建失败',
    //         );
    //         return json_encode($data);
    //     }else if(!is_writeable($dirname)){
    //         $data=array(
    //             'state' => '目录没有写权限',
    //         );
    //         return json_encode($data);
    //     }

    //     //移动文件
    //     if(!(file_put_contents($fullName, $img) && file_exists($fullName))){ //移动失败
    //         $data=array(
    //             'state' => '写入文件内容错误',
    //         );
    //         return json_encode($data);
    //     }else{ //移动成功
    //         $data=array(
    //             'state' => 'SUCCESS',
    //             'url' => ROOT_DIR.substr($file['fullName'],1), // 支持子目录
    //             'title' => $file['name'],
    //             'original' => $file['oriName'],
    //             'type' => $file['ext'],
    //             'size' => $file['filesize'],
    //         );

    //         $ossConfig = tpCache('oss');
    //         if ($ossConfig['oss_switch']) {
    //             //图片可选择存放在oss
    //             $savePath = $this->upload_path.$this->savePath.$this->sub_name;
    //             $object = $savePath.md5(getTime().uniqid(mt_rand(), TRUE)).'.'.pathinfo($data['url'], PATHINFO_EXTENSION);
    //             $getRealPath = ltrim($data['url'], '/');
    //             $ossClient = new \app\common\logic\OssLogic;
    //             $return_url = $ossClient->uploadFile($getRealPath, $object);
    //             if (!$return_url) {
    //                 $state = "ERROR" . $ossClient->getError();
    //                 $return_url = '';
    //             } else {
    //                 $state = "SUCCESS";
    //             }
    //             @unlink($getRealPath);
    //             $data['url'] = $return_url;
    //         }
    //     }
    //     return json_encode($data);
    // }

    // /*
    //  * 处理base64编码的图片上传
    //  * 例如：涂鸦图片上传
    // */
    // private function upBase64($config,$fieldName){
    //     $base64Data = $_POST[$fieldName];
    //     $img = base64_decode($base64Data);

    //     $dirname = './'.$this->upload_path.'ueditor/'.$this->sub_name;
    //     $file['filesize'] = strlen($img);
    //     $file['oriName'] = $config['oriName'];
    //     $file['ext'] = strtolower(strrchr($config['oriName'],'.'));
    //     $file['name'] = uniqid().$file['ext'];
    //     $file['fullName'] = $dirname.$file['name'];
    //     $fullName = $file['fullName'];

    //     //检查文件大小是否超出限制
    //     if($file['filesize'] >= ($config["maxSize"])){
    //         $data=array(
    //             'state' => '文件大小超出网站限制',
    //         );
    //         return json_encode($data);
    //     }

    //     //创建目录失败
    //     if(!file_exists($dirname) && !mkdir($dirname,0777,true)){
    //         $data=array(
    //             'state' => '目录创建失败',
    //         );
    //         return json_encode($data);
    //     }else if(!is_writeable($dirname)){
    //         $data=array(
    //             'state' => '目录没有写权限',
    //         );
    //         return json_encode($data);
    //     }

    //     //移动文件
    //     if(!(file_put_contents($fullName, $img) && file_exists($fullName))){ //移动失败
    //         $data=array(
    //             'state' => '写入文件内容错误',
    //         );
    //     }else{ //移动成功          
    //         $data=array(
    //             'state' => 'SUCCESS',
    //             'url' => substr($file['fullName'],1),
    //             'title' => $file['name'],
    //             'original' => $file['oriName'],
    //             'type' => $file['ext'],
    //             'size' => $file['filesize'],
    //         );
    //     }
        
    //     return json_encode($data);
    // }

    /**
     * @function imageUp
     */
    public function imageUp()
    {
        if (!IS_POST) {
            $return_data['state'] = '非法上传';
            respose($return_data,'json');
        }

        $image_upload_limit_size = intval(tpCache('basic.file_size') * 1024 * 1024);
        // 上传图片框中的描述表单名称，
        $pictitle = input('pictitle');
        $dir = input('dir');
        $title = htmlspecialchars($pictitle , ENT_QUOTES);        
        $path = htmlspecialchars($dir, ENT_QUOTES);
        //$input_file ['upfile'] = $info['Filedata'];  一个是上传插件里面来的, 另外一个是 文章编辑器里面来的
        // 获取表单上传文件
        $file = request()->file('file');
        if(empty($file))
            $file = request()->file('upfile');    

        // ico图片文件不进行验证
        if (pathinfo($file->getInfo('name'), PATHINFO_EXTENSION) != 'ico') {
            $result = $this->validate(
                ['file' => $file], 
                ['file'=>'image|fileSize:'.$image_upload_limit_size.'|fileExt:'.$this->fileExt],
                ['file.image' => '上传文件必须为图片','file.fileSize' => '上传文件过大','file.fileExt'=>'上传文件后缀名必须为'.$this->fileExt]                
               );
        } else {
            $result = true;
        }

        /*验证图片一句话木马*/
        $imgstr = @file_get_contents($file->getInfo('tmp_name'));
        if (false !== $imgstr && preg_match('#<\?php#i', $imgstr)) {
            $result = '上传图片不合格';
        }
        /*--end*/

        if (true !== $result || empty($file)) {
            $state = "ERROR：" . $result;
        } else {
            $savePath = $this->upload_path.$this->savePath.$this->sub_name;
            $ossConfig = tpCache('oss');
            if ($ossConfig['oss_switch']) {
                //商品图片可选择存放在oss
                $object = $savePath.md5(getTime().uniqid(mt_rand(), TRUE)).'.'.pathinfo($file->getInfo('name'), PATHINFO_EXTENSION);
                $ossClient = new \app\common\logic\OssLogic;
                $return_url = $ossClient->uploadFile($file->getRealPath(), $object);
                if (!$return_url) {
                    $state = "ERROR" . $ossClient->getError();
                    $return_url = '';
                } else {
                    $state = "SUCCESS";
                }
                @unlink($file->getRealPath());
            } else {
                // 移动到框架应用根目录/public/uploads/ 目录下
                $info = $file->rule(function ($file) {
                    return  md5(mt_rand()); // 使用自定义的文件保存规则
                })->move($savePath);
                if ($info) {
                    $state = "SUCCESS";
                } else {
                    $state = "ERROR" . $file->getError();
                }
                $return_url = '/'.$savePath.$info->getSaveName();
            }
            $return_data['url'] = ROOT_DIR.$return_url; // 支持子目录
        }
        
        if($state == 'SUCCESS' && pathinfo($file->getInfo('name'), PATHINFO_EXTENSION) != 'ico'){
            if(true || $this->savePath=='news/'){ // 添加水印
                $imgresource = ".".$return_url;
                $image = \think\Image::open($imgresource);
                $water = tpCache('water');
                $return_data['mark_type'] = $water['mark_type'];
                if($water['is_mark']==1 && $image->width()>$water['mark_width'] && $image->height()>$water['mark_height']){
                    if($water['mark_type'] == 'text'){
                        //$image->text($water['mark_txt'],ROOT_PATH.'public/static/common/font/hgzb.ttf',20,'#000000',9)->save($imgresource);
                        $ttf = ROOT_PATH.'public/static/common/font/hgzb.ttf';
                        if (file_exists($ttf)) {
                            $size = $water['mark_txt_size'] ? $water['mark_txt_size'] : 30;
                            $color = $water['mark_txt_color'] ?: '#000000';
                            if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
                                $color = '#000000';
                            }
                            $transparency = intval((100 - $water['mark_degree']) * (127/100));
                            $color .= dechex($transparency);
                            $image->open($imgresource)->text($water['mark_txt'], $ttf, $size, $color, $water['mark_sel'])->save($imgresource);
                            $return_data['mark_txt'] = $water['mark_txt'];
                        }
                    }else{
                        /*支持子目录*/
                        $water['mark_img'] = preg_replace('#^(/[/\w]+)?(/public/upload/|/uploads/)#i', '$2', $water['mark_img']); // 支持子目录
                        /*--end*/
                        //$image->water(".".$water['mark_img'],9,$water['mark_degree'])->save($imgresource);
                        $waterPath = "." . $water['mark_img'];
                        if (eyPreventShell($waterPath) && file_exists($waterPath)) {
                            $quality = $water['mark_quality'] ? $water['mark_quality'] : 80;
                            $waterTempPath = dirname($waterPath).'/temp_'.basename($waterPath);
                            $image->open($waterPath)->save($waterTempPath, null, $quality);
                            $image->open($imgresource)->water($waterTempPath, $water['mark_sel'], $water['mark_degree'])->save($imgresource);
                            @unlink($waterTempPath);
                        }
                    }
                }
            }
        }
        $return_data['title'] = $title;
        $return_data['original'] = ''; // 这里好像没啥用 暂时注释起来
        $return_data['state'] = $state;
        $return_data['path'] = $path;
        respose($return_data,'json');
    }
}