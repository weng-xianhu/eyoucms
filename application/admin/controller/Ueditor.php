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

    public function __construct()
    {
        parent::__construct();
        
        //header('Access-Control-Allow-Origin: http://www.baidu.com'); //设置http://www.baidu.com允许跨域访问
        //header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With'); //设置允许的跨域header
        
        date_default_timezone_set("Asia/Shanghai");
        
        $this->savePath = input('savepath','allimg').'/';

        $this->nowFileName = input('nowfilename', '');
        if (empty($this->nowFileName)) {
            $this->nowFileName = md5(time().uniqid(mt_rand(), TRUE));
        }
        
        error_reporting(E_ERROR | E_WARNING);
        
        header("Content-Type: text/html; charset=utf-8");

        $this->imageExt = config('global.image_ext');
        $this->image_type = tpCache('basic.image_type');
        $this->image_type = !empty($this->image_type) ? str_replace('|', ',', $this->image_type) : $this->imageExt;
    }
    
    public function index() {
        $CONFIG2 = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("./public/plugins/Ueditor/php/config.json")), true);
        $action = $_GET['action'];
        switch ($action) {
            case 'config':
                $result =  json_encode($CONFIG2);
                break;
            /* 上传图片 */
            case 'uploadimage':
                $fieldName = $CONFIG2['imageFieldName'];
                $result = $this->upFile($fieldName);

                /*同步到第三方对象存储空间*/
                $result = json_decode($result, true);
                $bucket_data = SynImageObjectBucket($result['url']);
                $result = array_merge($result, $bucket_data);
                $result = json_encode($result);
                /*end*/

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
    private function upFile($fieldName) {
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

        $max_file_size = intval(tpCache('basic.file_size') * 1024 * 1024);
        $fileExt = '';
        $image_type = tpCache('basic.image_type');
        !empty($image_type) && $fileExt .= '|'.$image_type;
        $file_type = tpCache('basic.file_type');
        !empty($file_type) && $fileExt .= '|'.$file_type;
        $media_type = tpCache('basic.media_type');
        !empty($media_type) && $fileExt .= '|'.$media_type;
        $fileExt = !empty($fileExt) ? str_replace('||', '|', $fileExt) : config('global.image_ext');
        $fileExt = str_replace('|', ',', trim($fileExt, '|'));
        $result = $this->validate(
            ['file' => $file], 
            ['file'=>'fileSize:'.$max_file_size.'|fileExt:'.$fileExt],
            ['file.fileSize' => '上传文件过大','file.fileExt'=>'上传文件后缀名必须为'.$fileExt]
        );
        if (true !== $result || empty($file)) {
            $state = "ERROR" . $result;
            return json_encode(['state' =>$state]);
        }

        // 移动到框架应用根目录/public/uploads/ 目录下
        $this->savePath = $this->savePath.date('Ymd/');
        // 使用自定义的文件保存规则
        $info = $file->rule(function ($file) {
            return session('admin_id').'-'.dd2char(date("ymdHis").mt_rand(100,999));
        })->move(UPLOAD_PATH.$this->savePath);

        if (!empty($info)) {
            $data = array(
                'state' => 'SUCCESS',
                'url' => '/'.UPLOAD_PATH.$this->savePath.$info->getSaveName(),
                'title' => '',//$info->getSaveName(),
                'original' => $info->getSaveName(),
                'type' => '.' . $info->getExtension(),
                'size' => $info->getSize(),
            );

            //图片加水印
            if ($data['state'] == 'SUCCESS') {
                $file_type = $file->getInfo('type');
                $file_ext = pathinfo($file->getInfo('name'), PATHINFO_EXTENSION);
                $fileextArr = explode(',', $this->image_type);
                if (stristr($file_type, 'image') && 'ico' != $file_ext) {
                    print_water($data['url']);
                }
            }

            $data['url'] = ROOT_DIR.$data['url']; // 支持子目录
        } else {
            $data = array('state' => 'ERROR'.$info->getError());
        }
        
        if (1 == $this->editor['editor_select']) {
            return json_encode($data);
        } else if (2 == $this->editor['editor_select']) {
            $CKEditorFuncNum = input('param.CKEditorFuncNum/d');
            $message = '';
            $str = '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('.$CKEditorFuncNum.', \''.$data['url'].'\', \''.$message.'\');</script>';
            exit($str);
        }
    }

    //列出图片
    private function fileList($allowFiles,$listSize,$get){
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

        //http开头验证
        if(strpos($imgUrl,"http") !== 0){
            $data=array(
                'state' => '链接不是http链接',
            );
            return json_encode($data);
        }
        //获取请求头并检测死链
        $heads = @get_headers($imgUrl, 1);
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
        if(preg_match("/^http(s?):\/\/mmbiz.qpic.cn\/(.*)/", $imgUrl) != 1){
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
        $context = stream_context_create(
            array('http' => array(
                'follow_location' => false // don't follow redirects
            ))
        );
        readfile($imgUrl,false,$context);
        $img = ob_get_contents();
        ob_end_clean();
        preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/",$imgUrl,$m);

        $dirname = './'.UPLOAD_PATH.'ueditor/'.date('Ymd/');
        $file['oriName'] = $m ? $m[1] : "";
        $file['filesize'] = strlen($img);
        $file['ext'] = strtolower(strrchr($config['oriName'],'.'));
        $file['name'] = session('admin_id').'-'.dd2char(date("ymdHis").mt_rand(100,999)).$file['ext'];
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
        if(!file_exists($dirname) && !mkdir($dirname,0777,true)){
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
            $data = array(
                'state' => 'SUCCESS',
                'url' => ROOT_DIR.substr($file['fullName'],1), // 支持子目录
                'title' => $file['name'],
                'original' => $file['oriName'],
                'type' => $file['ext'],
                'size' => $file['filesize'],
            );

            print_water($data['url']); // 添加水印
        }
        return json_encode($data);
    }

    /*
     * 处理base64编码的图片上传
     * 例如：涂鸦图片上传
    */
    private function upBase64($config,$fieldName){
        $base64Data = $_POST[$fieldName];
        $img = base64_decode($base64Data);

        $dirname = './'.UPLOAD_PATH.'ueditor/'.date('Ymd/');
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
        if(!file_exists($dirname) && !mkdir($dirname,0777,true)){
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
        // ico图片文件不进行验证
        if (pathinfo($file->getInfo('name'), PATHINFO_EXTENSION) != 'ico') {
            $result = $this->validate(
                ['file' => $file], 
                ['file' => 'image|fileSize:' . $max_file_size . '|fileExt:' . $this->image_type],
                [
                    'file.image'    => '上传文件必须为图片',
                    'file.fileSize' => '上传图片过大',
                    'file.fileExt'  => '上传图片后缀名必须为' . $this->image_type
                ]
            );
        } else {
            $result = true;
        }

        /*验证图片一句话木马*/
        if (false === check_illegal($file->getInfo('tmp_name'))) {
            $result = '疑似木马图片！';
        }
        /*--end*/

        if (true !== $result || empty($file)) {
            $state = "ERROR：" . $result;
        } else {
            if ('adminlogo/' == $this->savePath) {
                $savePath = 'public/static/admin/logo/';
            } else if ('loginlogo/' == $this->savePath) {
                $savePath = 'public/static/admin/login/';
            } else if ('loginbgimg/' == $this->savePath) {
                $savePath = 'public/static/admin/loginbg/';
            } else {
                $savePath = UPLOAD_PATH . $this->savePath . date('Ymd/');
            }
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->rule(function ($file) {
                // return  md5(mt_rand()); // 使用自定义的文件保存规则
                return session('admin_id').'-'.dd2char(date("ymdHis").mt_rand(100,999)); // 使用自定义的文件保存规则
            })->move($savePath);
            if ($info) {
                $state = "SUCCESS";
            } else {
                $state = "ERROR" . $file->getError();
            }
            $return_url = '/' . $savePath . $info->getSaveName();
            $return_data['url'] = ROOT_DIR . $return_url; // 支持子目录

            // 重新制作一张图片，抹去任何可能有危害的数据
            // $image       = \think\Image::open('.'.$return_url);
            // $image->save('.'.$return_url, null, 100);
        }

        // 添加水印
        if($state == 'SUCCESS' && pathinfo($file->getInfo('name'), PATHINFO_EXTENSION) != 'ico'){
            $is_water = input('param.is_water/d');
            if(!in_array($this->savePath, ['adminlogo/','loginlogo/','loginbgimg/']) && $is_water == 1) print_water($return_url);
        }

        // 返回数据
        $return_data['title']    = $title;
        $return_data['original'] = $title;
        $return_data['state']    = $state;
        $return_data['path']     = $path;

        /*同步到第三方对象存储空间*/
        $bucket_data = SynImageObjectBucket($return_url, [], $file);
        $return_data = array_merge($return_data, $bucket_data);
        /*end*/

        respose($return_data,'json');
    }
    
    /**
     * app文件上传
     */
    public function appFileUp()
    {      
        $max_file_size = intval(tpCache('basic.file_size') * 1024 * 1024);
        $path = UPLOAD_PATH.'soft/'.date('Ymd/');
        if (!file_exists($path)) {
            mkdir($path);
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
            $info = $file->rule(function ($file) {    
                return date('YmdHis_').input('Filename'); // 使用自定义的文件保存规则
            })->move($path);
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

    public function uhash( $file ) {
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
        $this->savePath = $this->savePath.date('Ymd/');
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

        // 使用自定义的文件保存规则
        $info = $file->rule(function ($file) {
            return  $this->fileName;
        })->move(UPLOAD_PATH.$this->savePath);
        if($info){
            // 拼装数据存入session
            $file_path = UPLOAD_PATH.$this->savePath.$info->getSaveName();
            $return = array(
                'code'      => 1,
                'msg'       => '上传成功',
                'file_url'  => '/' . UPLOAD_PATH.$this->savePath.$fileName,
                'file_mime' => $file->getInfo('type'),
                'file_name' => $fileName,
                'file_ext'  => '.' . $file_ext,
                'file_size' => $info->getSize(),
                'uhash'     => $this->uhash($file_path),
                'md5file'   => md5_file($file_path),
            );
        }else{
            $return = array('msg' => $info->getError());
        }
        echo json_encode($return);
    }

    //上传文件
    public function DownloadUploadFileAjax()
    {
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
        $this->savePath = "soft/" . date('Ymd/');
        // 定义文件名
        $fileName = $file->getInfo('name');
        // 提取文件名后缀
        $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);

        // 使用自定义的文件保存规则
        $info = $file->rule(function ($file) {
            return session('admin_id') . '-' . dd2char(date("ymdHis") . mt_rand(100, 999));
        })->move(UPLOAD_PATH . $this->savePath);
        if ($info) {
            // 拼装数据存入session
            $file_path = UPLOAD_PATH . $this->savePath . $info->getSaveName();
            $return    = array(
                'code'      => 1,
                'msg'       => '上传成功',
                'file_url'  => ROOT_DIR.'/' . UPLOAD_PATH . $this->savePath . $info->getSaveName(),
                'file_mime' => $file->getInfo('type'),
                'file_name' => $fileName,
                'file_ext'  => '.' . $file_ext,
                'file_size' => $info->getSize(),
                'uhash'     => $this->uhash($file_path),
                'md5file'   => md5_file($file_path),
            );
        } else {
            $res = ['code' => 0, 'msg' => $info->getError()];
        }
        respose($return);
    }

    // 上传视频
    public function upVideo()
    {
        $file     = request()->file('file');
        if (empty($file)) {
            if (!@ini_get('file_uploads')) {
                return json_encode(['state' => '请检查空间是否开启文件上传功能！']);
            } else {
                return json_encode(['state' => 'ERROR，空间限制上传大小！']);
            }
        }
        $error = $file->getError();
        if (!empty($error)) {
            return json_encode(['state' => $error]);
        }

        $media_type                 = tpCache('basic.media_type');
        $media_type = !empty($media_type) ? str_replace('|', ',', $media_type) : config('global.media_ext');
        if (empty($media_type)) {
            return json_encode(['state' => 'ERROR，请设置上传多媒体文件类型！']);
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
            $state = "ERROR" . $result;
            return json_encode(['state' => $state]);
        }

        //获取视频时长start
        vendor('getid3.getid3');
        // 实例化
        $getID3       = new \getID3();  //实例化类
        $tmp_name = $file->getInfo('tmp_name');
        $ThisFileInfo = $getID3->analyze($tmp_name); //分析文件，$path为音频文件的地址
        $fileduration = intval($ThisFileInfo['playtime_seconds']); //这个获得的便是音频文件的时长
        //获取视频时长end

        // 移动到框架应用根目录/public/uploads/ 目录下
        $this->savePath = $this->savePath.date('Ymd/');
        // 使用自定义的文件保存规则
        $info = $file->rule(function ($file) {
            return session('admin_id') . '-' . dd2char(date("ymdHis") . mt_rand(100, 999));
        })->move(UPLOAD_PATH . $this->savePath);

        if ($info) {
            // 定义文件名
            $fileName    = $file->getInfo('name');
            // 提取出文件名，不包括扩展名
            $newfileName = preg_replace('/\.([^\.]+)$/', '', $fileName);
            // 过滤文件名.\/的特殊字符，防止利用上传漏洞
            $newfileName = preg_replace('#(\\\|\/|\.)#i', '', $newfileName);

            $file_path = UPLOAD_PATH.$this->savePath.$info->getSaveName();

            $file_size = $info->getSize();

            $data = array(
                'state'    => 'SUCCESS',
                'url'      => '/' . $file_path,
                'time'     => $fileduration,
                'title'    => $newfileName,
                'original' => $info->getSaveName(),
                'type'     => '.' . $info->getExtension(),
                'size'     => $file_size,
                'mime'     => $file->getInfo('type'),
            );

            $data['url'] = ROOT_DIR . $data['url']; // 支持子目录
        } else {
            $data = array('state' => 'ERROR' . $info->getError());
        }
        return $data;
    }
}