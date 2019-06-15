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
use app\common\logic\ArctypeLogic;

class Uploadify extends Base
{
    public $image_type = '';

    /**
     * 析构函数
     */
    function __construct() 
    {
        parent::__construct();
        $this->image_type = tpCache('basic.image_type');
        $this->image_type = !empty($this->image_type) ? str_replace('|', ',', $this->image_type) : 'jpg,gif,png,bmp,jpeg,ico';
    }

    /**
     * 通用的上传图片
     */
    public function upload()
    {
        $func = input('func');
        $path = input('path','allimg');
        $num  = input('num/d', '1');
        $default_size = intval(tpCache('basic.file_size') * 1024 * 1024); // 单位为b
        $size = input('size/d'); // 单位为kb
        $size = empty($size) ? $default_size : $size*1024;
        $info = array(
            'num'      => $num,
            'title'    => '',          
            'upload'   => url('Ueditor/imageUp',array('savepath'=>$path,'pictitle'=>'banner','dir'=>'images')),
            'fileList' => url('Uploadify/fileList',array('path'=>$path)),
            'size'     => $size,
            'type'     => $this->image_type,
            'input'    => input('input'),
            'func'     => empty($func) ? 'undefined' : $func,
            'path'     => $path,
        );
        $this->assign('info',$info);
        return $this->fetch();
    }

    /**
     * 图库在线管理 - 左侧树形目录结构
     */
    public function picture_folder()
    {   
        $func = input('func');
        $path = input('path','allimg');
        $num  = input('num/d', '1');
        $default_size = intval(tpCache('basic.file_size') * 1024 * 1024); // 单位为b
        $size = input('size/d'); // 单位为kb
        $size = empty($size) ? $default_size : $size*1024;
        $info = array(
            'num'      => $num,
            'title'    => '',          
            'upload'   => url('Ueditor/imageUp',array('savepath'=>$path,'pictitle'=>'banner','dir'=>'images')),
            'fileList' => url('Uploadify/fileList',array('path'=>$path)),
            'size'     => $size,
            'type'     => $this->image_type,
            'input'    => input('input'),
            'func'     => empty($func) ? 'undefined' : $func,
            'path'     => $path,
        );
        $this->assign('info',$info);

        // 侧边栏目录栏目
        $dirArr  = $this->getDir('uploads');
        $dirArr2 = [];
        foreach ($dirArr as $key => $val) {
            $dirArr2[$val['id']] = $val['dirpath'];
        }
        foreach ($dirArr as $key => $val) {
            $dirfileArr = glob("{$val['dirpath']}/*");
            if (empty($dirfileArr)) {
                empty($dirfileArr) && @rmdir($val['dirpath']);
                $dirArr[$key] = [];
                continue;
            }
            $dirname = preg_replace('/([^\/]+)$/i', '', $val['dirpath']);
            $arr_key = array_search(trim($dirname, '/'), $dirArr2);
            if (!empty($arr_key)) {
                $dirArr[$key]['pId'] = $arr_key;
            } else {
                $dirArr[$key]['pId'] = 0;
            }
            $dirArr[$key]['name'] = preg_replace('/^(.*)\/([^\/]+)$/i', '${2}', $val['dirpath']);
        }

        $zNodes = json_encode($dirArr,true);
        $this->assign('zNodes', $zNodes);
        return $this->fetch();
    }

    /**
     * 图库在线管理 - 图片列表显示
     */
    public function get_images_path($images_path = 'uploads')
    {
       if ('uploads' != $images_path && !preg_match('#^(uploads)/(.*)$#i', $images_path)) {
            $this->error('非法访问！');
        }

        $func = input('func/s');
        $num  = input('num/d', '1');
        $info = array(
            'num'  => $num,
            'func' => empty($func) ? 'undefined' : $func,
        );
        $this->assign('info',$info);

        // 常用图片
        $common_pic = [];
        $arr1 = explode('/', $images_path);
        if (1 >= count($arr1)) { // 只有一级目录才显示常用图片
            $where = [
                'lang' => $this->admin_lang,
            ];
            $common_pic = M('common_pic')->where($where)->order('id desc')->limit(6)->field('pic_path')->select();
        }
        $this->assign('common_pic', $common_pic);

        // 图片列表
        $images_data = glob($images_path.'/*');
        $list = [];
        if (!empty($images_data)) {
            // 图片类型数组
            $image_ext = explode(',', config('global.image_ext'));
            // 处理图片
            foreach ($images_data as $key => $file) {
                $fileArr = explode('.', $file);    
                $ext = end($fileArr);
                $ext = strtolower($ext);
                if (in_array($ext, $image_ext)) {
                    $list[$key]['path'] = ROOT_DIR.'/'.$file;
                    $list[$key]['time'] = @filemtime($file);
                }
            }
        }
        
        // 图片选择的时间从大到小排序
        $list_time = get_arr_column($list,'time');
        array_multisort($list_time,SORT_DESC,$list);
        // 返回数据
        $this->assign('list', $list);

        $this->assign('path_directory', $images_path);

        return $this->fetch();
    }

    /**
     * 记录常用图片
     */
    public function update_pic()
    {
        if(IS_AJAX_POST){
            $param = input('param.');
            if (!empty($param['images_array'])) {
                $commonPic_db = Db::name('common_pic');

                $where = '';
                $data  = []; 
                foreach ($param['images_array'] as $key => $value) {
                    // 删除条件
                    if ($key > '0') { $where .= ','; }
                    $where .= $value;

                    // 添加数组
                    $data[$key] = [
                        'pic_path'    => $value,
                        'lang'        => $this->admin_lang,
                        'add_time'    => getTime(),
                        'update_time' => getTime(),
                    ];
                }

                // 批量删除选中的图片
                $commonPic_db->where('pic_path','IN',$where)->delete();

                // 批量添加图片
                !empty($data) && $commonPic_db->insertAll($data);

                // 查询最后一条数据
                $row = $commonPic_db->order('id desc')->limit('20,1')->field('id')->select();
                if (!empty($row)) {
                    $id = $row[0]['id'];
                    // 删除ID往后的数据
                    $where_ = array(
                        'id'   => array('<',$id),
                        'lang' => $this->admin_lang,
                    );
                    $commonPic_db->where($where_)->delete();
                }
            }
        }
    }

    /**
     * 在弹出窗里的上传图片
     */
    public function upload_frame()
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
            'upload' =>url('Ueditor/imageUp',array('savepath'=>$path,'pictitle'=>'banner','dir'=>'images')),
            'fileList'=>url('Uploadify/fileList',array('path'=>$path)),
            'size' => $size,
            'type' => $this->image_type,
            'input' => input('input'),
            'func' => empty($func) ? 'undefined' : $func,
            'path'     => $path,
        );
        $this->assign('info',$info);
        return $this->fetch();
    }

    /**
     * 后台（产品）专用
     */
    public function upload_product()
    {
        $aid = input('aid/d');
        $func = input('func');
        $path = input('path','allimg');
        $num = input('num/d', '1');
        $default_size = intval(tpCache('basic.file_size') * 1024 * 1024); // 单位为b
        $size = input('size/d'); // 单位为kb
        $size = empty($size) ? $default_size : $size*1024;
        $field = array(
            'aid' => $aid,
            'num' => $num,
            'title' => '',          
            'upload' => url('Ueditor/imageUp',array('savepath'=>$path,'pictitle'=>'banner','dir'=>'images')),
            'fileList'=> url('Uploadify/fileList',array('path'=>$path)),
            'size' => $size,
            'type' => $this->image_type,
            'input' => input('input'),
            'func' => empty($func) ? 'undefined' : $func,
            'path'     => $path,
        );
        $this->assign('field',$field);
        return $this->fetch();
    }

    /**
     * 完整的上传模板展示
     */
    public function upload_full()
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
            'upload' =>url('Ueditor/imageUp',array('savepath'=>$path,'pictitle'=>'banner','dir'=>'images')),
            'fileList'=>url('Uploadify/fileList',array('path'=>$path)),
            'size' => $size,
            'type' => $this->image_type,
            'input' => input('input'),
            'func' => empty($func) ? 'undefined' : $func,
            'path'     => $path,
        );
        $this->assign('info',$info);
        return $this->fetch();
    }
    
    /*
     * 删除上传的图片
     */
    public function delupload()
    {
        // if (IS_POST) {
        //     $action = input('action','del');                
        //     $filename= input('filename/s');
        //     $filename= empty($filename) ? input('url') : $filename;
        //     $filename= str_replace('../','',$filename);
        //     $filename= trim($filename,'.');
        //     $filename= trim($filename,'/');
        //     if(eyPreventShell($filename) && $action=='del' && !empty($filename) && file_exists($filename)){
        //         $filetype = preg_replace('/^(.*)\.(\w+)$/i', '$2', $filename);
        //         $phpfile = strtolower(strstr($filename,'.php'));  //排除PHP文件
        //         $size = getimagesize($filename);
        //         $fileInfo = explode('/',$size['mime']);
        //         if($fileInfo[0] != 'image' || $phpfile || !in_array($filetype, explode(',', config('global.image_ext')))){
        //             exit;
        //         }
        //         if(@unlink($filename)){
        //             echo 1;
        //         }else{
        //             echo 0;
        //         }  
        //         exit;
        //     }
        // }
    }
    
    public function fileList(){
        /* 判断类型 */
        $type = input('type','Images');
        switch ($type){
            /* 列出图片 */
            case 'Images' : $allowFiles = str_replace(',', '|', $this->image_type);break;
        
            case 'Flash' : $allowFiles = 'flash|swf';break;
        
            /* 列出文件 */
            default : 
            {
                $file_type = tpCache('basic.file_type');
                $media_type = tpCache('basic.media_type');
                $allowFiles = $file_type.'|'.$media_type;
            }
        }

        $listSize = 102400000;
        
        $key = empty($_GET['key']) ? '' : $_GET['key'];
        
        /* 获取参数 */
        $size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
        $start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
        $end = $start + $size;
        
        $path = input('path','allimg');
        if (1 == preg_match('#\.#', $path)) {
            echo json_encode(array(
                    "state" => "路径不符合规范",
                    "list" => array(),
                    "start" => $start,
                    "total" => 0
            ));
            exit;
        }
        if ('adminlogo' == $path) {
            $path = 'public/static/admin/logo';
        } else {
            $path = UPLOAD_PATH.$path;
        }

        /* 获取文件列表 */
        $files = $this->getfiles($path, $allowFiles, $key);
        if (empty($files)) {
            echo json_encode(array(
                    "state" => "没有相关文件",
                    "list" => array(),
                    "start" => $start,
                    "total" => count($files)
            ));
            exit;
        }
        
        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
            $list[] = $files[$i];
        }
        
        /* 返回数据 */
        $result = json_encode(array(
                "state" => "SUCCESS",
                "list" => $list,
                "start" => $start,
                "total" => count($files)
        ));
        
        echo $result;
    }

    /**
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param array $files
     * @return array
     */
    private function getfiles($path, $allowFiles, $key, &$files = array()){
        if (!is_dir($path)) return null;
        if(substr($path, strlen($path) - 1) != '/') $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    $this->getfiles($path2, $allowFiles, $key, $files);
                } else {
                    if (preg_match("/\.(".$allowFiles.")$/i", $file) && preg_match("/.*". $key .".*/i", $file)) {
                        $files[] = array(
                            'url'=> ROOT_DIR.'/'.$path2, // 支持子目录
                            'name'=> $file,
                            'mtime'=> filemtime($path2)
                        );
                    }
                }
            }
        }
        return $files;
    }

    /**
     * 提取上传图片目录下的所有图片
     *
     * @param string $directory 目录路径
     * @param string $dir_name 显示的目录前缀路径
     * @param array $arr_file 是否删除空目录
     * @param num $num 数量
     */
    private function getDir($directory, &$arr_file = array(), &$num = 0) {
        $mydir = glob($directory.'/*', GLOB_ONLYDIR);
        $param = input('param.');
        if (0 <= $num) {
            $dirpathArr = explode('/', $directory);
            $level = count($dirpathArr);
            $open = (1 >= $level) ? true : false;
            $fileList = glob($directory.'/*');
            $total = count($fileList); // 目录是否存在任意文件，否则删除该目录
            if (!empty($total)) {
                $isExistPic = $this->isExistPic($directory);
                if (!empty($isExistPic)) {
                    $arr_file[] = [
                        'id'        => $num,
                        'url'       => url('Uploadify/get_images_path',['num'=>$param['num'],'func'=>$param['func'],'lang'=>$param['lang'],'images_path'=>$directory]),
                        'target'    => 'content_body',
                        'isParent'  => true,
                        'open'      => $open,
                        'dirpath'   => $directory,
                        'level'     => $level,
                        'total'     => $total,
                    ];
                }
            } else {
                @rmdir("$directory");
            }
        }
        if (!empty($mydir)) {
            foreach ($mydir as $key => $dir) {
                if (stristr("$dir/", 'uploads/soft_tmp/')) {
                    continue;
                }
                $num++;
                $dirname = str_replace('\\', '/', $dir);
                $dirArr  = explode('/', $dirname);
                $dir     = end($dirArr);
                $mydir2  = glob("$directory/$dir/*", GLOB_ONLYDIR);
                if(!empty($mydir2) AND ($dir != ".") AND ($dir != ".."))
                {
                    $this->getDir("$directory/$dir", $arr_file, $num);
                }
                else if(($dir != ".") AND ($dir != ".."))
                {
                    $dirpathArr = explode('/', "$directory/$dir");
                    $level = count($dirpathArr);
                    $fileList = glob("$directory/$dir/*"); // 目录是否存在任意文件，否则删除该目录
                    $total = count($fileList);
                    if (!empty($total)) {
                         // 目录是否存在图片文件，否则删除该目录
                        $isExistPic = $this->isExistPic("$directory/$dir");
                        if (!empty($isExistPic)) {
                            $arr_file[] = [
                                'id'        => $num,
                                'url'       => url('Uploadify/get_images_path',['num'=>$param['num'],'func'=>$param['func'],'lang'=>$param['lang'],'images_path'=>"$directory/$dir"]),
                                'target'    => 'content_body',
                                'isParent'  => false,
                                'open'      => false,
                                'dirpath'   => "$directory/$dir",
                                'level'     => $level,
                                'icon'      => 'public/plugins/ztree/css/zTreeStyle/img/dir_close.png',
                                'iconOpen'  => 'public/plugins/ztree/css/zTreeStyle/img/dir_open.png',
                                'iconClose' => 'public/plugins/ztree/css/zTreeStyle/img/dir_close.png',
                                'total'     => $total,
                            ];
                        }
                    } else {
                        @rmdir("$directory/$dir");
                    }
                }
            }
        }
        return $arr_file;
    }

    /**
     * 检测指定目录是否存在图片
     *
     * @param string $directory 目录路径
     * @param string $dir_name 显示的目录前缀路径
     * @param array $arr_file 是否删除空目录
     * @return boolean
     */
    private function isExistPic($directory, $dir_name='', &$arr_file = [])
    {
        if (!file_exists($directory) ) {
            return false;
        }

        if (!empty($arr_file)) {
            return true;
        }

        // 图片类型数组
        $image_ext = explode(',', config('global.image_ext'));
        $mydir = dir($directory);
        while($file = $mydir->read())
        {
            if((is_dir("$directory/$file")) AND ($file != ".") AND ($file != ".."))
            {
                if ($dir_name) {
                    return $this->isExistPic("$directory/$file", "$dir_name/$file", $arr_file);
                } else {
                    return $this->isExistPic("$directory/$file", "$file", $arr_file);
                }
                
            }
            else if(($file != ".") AND ($file != ".."))
            {
                $fileArr = explode('.', $file);    
                $ext = end($fileArr);
                $ext = strtolower($ext);
                if (in_array($ext, $image_ext)) {
                    if ($dir_name) {
                        $arr_file[] = "$dir_name/$file";
                    } else {
                        $arr_file[] = "$file";
                    }
                    return true;
                }

            }
        }
        $mydir->close();

        return $arr_file;
    }
}