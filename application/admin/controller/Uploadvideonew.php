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
use think\Page;

class Uploadvideonew extends Base
{
    public $video_type = '';
    private $videoExt = '';
    private $php_sessid = '';
    private $video_accept = '';

    /**
     * 析构函数
     */
    function __construct() 
    {
        parent::__construct();
        // 视频文件类型配置
        $this->videoExt = config('global.media_ext');
        $this->video_type = tpCache('basic.media_type');
        $this->video_type = !empty($this->video_type) ? str_replace('|', ',', $this->video_type) : $this->videoExt;
        $this->video_accept = video_accept_arr($this->video_type);
        $this->php_sessid = !empty($_COOKIE['PHPSESSID']) ? $_COOKIE['PHPSESSID'] : '';
    }

    //获取上传目录列表
    public function get_type()
    {
        if (IS_AJAX){
            $type_list = Db::name('uploads_type')->field('id,upload_type')->where(['group_code'=>'video'])->select();
            if (empty($type_list)){
                $this->error('请先创建目录');
            }else{
                $this->success('success','',$type_list);
            }
        }
    }

    public function update_type_id()
    {
        $id_arr = input('img_id/a');
        $id_arr = eyIntval($id_arr);
        if (IS_AJAX && !empty($id_arr)) {
            $type_id = input('param.type_id/d', 0);
            $r = Db::name('uploads')->where('img_id','in',$id_arr)->update(['type_id'=>$type_id,'update_time'=>getTime()]);
            if ($r !== false) {
                $this->success('移动成功');
            }
        }
        $this->error('移动失败');
    }

    /**
     * 通用的上传视频
     */
    public function upload()
    {
        $assign_data = [];
        $type_id_selected = (int)tpSetting('system.system_uploads_video_type_id_selected', [], 'cn');
        $type_id = input('param.type_id/d', $type_id_selected);
        $assign_data['type_id'] = $type_id;
        // 基础配置 - 附件配置
        $basicConfig = tpCache('basic');
        $assign_data['basicConfig'] = $basicConfig;

        $func = input('param.func/s', 'undefined');
        $path = input('path','media');
        $num  = input('num/d', 1);
        $open_source  = input('open_source/s', 'bendi');
        $default_size = intval($basicConfig['file_size'] * 1024 * 1024); // 单位为b
        $size = input('size/d'); // 单位为kb
        $size = empty($size) ? $default_size : $size*1024;
        $info = array(
            'num'      => $num,
            'size'     => $size,
            'input'    => input('input'),
            'func'     => $func,
            'path'     => $path,
            'open_source' => preg_replace('/([^\w\-]+)/i', '', $open_source),
        );
        $assign_data['info'] = $info;
        $assign_data['default_upload_list_url'] = url('Uploadvideonew/get_upload_list', ['type_id'=>0, 'info'=>mchStrCode(json_encode($info), 'ENCODE')]);
        $assign_data['current_upload_list_url'] = url('Uploadvideonew/get_upload_list', ['type_id'=>$type_id, 'info'=>mchStrCode(json_encode($info), 'ENCODE')]);

        // 侧边栏我的分组
        $uploads_total_list = Db::name('uploads')->field('type_id, count(img_id) as total')->where(['group_code'=>'video','is_del'=>0])->group('type_id')->getAllWithIndex('type_id');
        $uploads_type_list = Db::name('uploads_type')->where(['group_code'=>'video'])->order('id asc')->select();
        foreach ($uploads_type_list as $key => $val) {
            $val['total'] = !empty($uploads_total_list[$val['id']]['total']) ? $uploads_total_list[$val['id']]['total'] : 0;
            $val['url'] = url('Uploadvideonew/get_upload_list', ['type_id'=>$val['id'], 'info'=>mchStrCode(json_encode($info), 'ENCODE')]);
            $uploads_type_list[$key] = $val;
        }
        $assign_data['uploads_type_list'] = $uploads_type_list;
        $assign_data['uploads_total_list'] = $uploads_total_list;

        $this->assign($assign_data);
        return $this->fetch('uploadvideonew/upload');
    }

    /**
     * 完整的上传模板展示
     */
    public function upload_full()
    {
        $func = input('func');
        $path = input('path','media');
        $path = preg_replace('/([^\w\-\/\\\]*)/i', '', $path);
        $num = input('num/d', '1');
        $default_size = intval(tpCache('basic.file_size') * 1024 * 1024); // 单位为b
        $size = input('size/d'); // 单位为kb
        $size = empty($size) ? $default_size : $size*1024;
        $info = array(
            'num'=> $num,
            'upload' =>url('Ueditor/upVideo',array('savepath'=>$path,'pictitle'=>'video','dir'=>'media')),
            'size' => $size,
            'type' => $this->video_type,
            'input' => preg_replace('/([^\w\-]*)/i', '', input('input')),
            'func' => empty($func) ? 'undefined' : preg_replace('/([^\w\-]*)/i', '', $func),
            'path'     => $path,
        );
        $assign_data['info'] = $info;

        // 视频列表
        $videolist = [];
        $assign_data['videolist'] = $videolist;

        $this->assign($assign_data);
        return $this->fetch();
    }

    /**
     * 获取左侧树形的目录结构
     * @return [type] [description]
     */
    public function ajax_get_treedir()
    {
        // 侧边栏视频目录
        $dirArr  = $this->getDirVideo('uploads');
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
            /*视频库显示数量*/
            $countFile = 0;
            $video_type_tmp = str_replace(',', '|', $this->video_type);
            foreach ($dirfileArr as $_k => $_v) {
                if (preg_match('/\.('.$video_type_tmp.')$/i', $_v)) {
                    $countFile++;
                }
            }
            /*end*/
            $dirname = preg_replace('/([^\/]+)$/i', '', $val['dirpath']);
            $arr_key = array_search(trim($dirname, '/'), $dirArr2);
            if (!empty($arr_key)) {
                $dirArr[$key]['pId'] = $arr_key;
            } else {
                $dirArr[$key]['pId'] = 0;
            }
            $dirArr[$key]['name'] = preg_replace('/^(.*)\/([^\/]+)$/i', '${2}', $val['dirpath']);
            !empty($countFile) && $dirArr[$key]['name'] .= "({$countFile})"; // 视频库显示数量
        }
        $zNodes = json_encode($dirArr,true);
        $this->success('请求成功', null, ['zNodes'=>$zNodes]);
    }

    /**
     * 右侧上传视频记录的列表显示
     */
    public function get_upload_list()
    {
        $assign_data = [];

        $type_id = input('param.type_id/d', 0);
        tpSetting('system', ['system_uploads_video_type_id_selected'=>$type_id], 'cn');
        $assign_data['type_id'] = $type_id;
        
        // 基础配置 - 视频配置
        $basicConfig = tpCache('basic');
        $assign_data['basicConfig'] = $basicConfig;

        $pageNum = input('param.p/d', 1);
        $assign_data['pageNum'] = $pageNum;

        $info = input('param.info/s');
        $info = json_decode(mchStrCode($info, 'DECODE'), true);
        $info['num'] = intval($info['num']);
        $info['size'] = preg_replace('/([^\d]*)/i', '', $info['size']);
        $info['input'] = preg_replace('/([^\w\-]*)/i', '', $info['input']);
        $info['func'] = preg_replace('/([^\w\-]*)/i', '', $info['func']);
        $info['path'] = preg_replace('/([^\w\-\/\\\]*)/i', '', $info['path']);
        $info['open_source'] = preg_replace('/([^\w\-]+)/i', '', $info['open_source']);
        $info['upload'] = url('Ueditor/upVideo',array('savepath'=>$info['path'],'type_id'=>$type_id,'pictitle'=>'video','dir'=>'media'));
        $info['video_accept'] = $this->video_accept;
        $assign_data['info'] = $info;

        $Where = [
            'group_code' => 'video',
        ];
        // 时间检索条件查询
        $post_eytime = input('eytime/s');
        $EyTime = !empty($post_eytime) ? $post_eytime : '';
        $assign_data['eytime'] = $EyTime;
        if (!empty($EyTime)) {
            $EyTime = explode(' - ', $EyTime);
            // 开始日期
            $Begin = 0;
            if (!empty($EyTime[0])) {
                if (stristr($EyTime[0], ':')) { // 包含了时分秒
                    $Begin = strtotime($EyTime[0]);
                } else {
                    $Begin = strtotime($EyTime[0]." 00:00:00");
                }
            }
            // 结束日期
            $End = 0;
            if (!empty($EyTime[1])) {
                if (stristr($EyTime[1], ':')) { // 包含了时分秒
                    $End = strtotime($EyTime[1]);
                } else {
                    $End = strtotime($EyTime[1]." 23:59:59");
                }
            }
            if ($Begin > 0 && $End > 0) {
                $Where['add_time'] = array('between', "$Begin, $End");
            } else if ($Begin > 0) {
                $Where['add_time'] = array('egt', $Begin);
            } else if ($End > 0) {
                $Where['add_time'] = array('elt', $End);
            }
        }
        
        // 视频名称搜索条件查询
        $keywords = input('keywords/s');
        $keywords = !empty($keywords) ? trim($keywords) : '';
        $assign_data['keywords'] = $keywords;
        if (!empty($keywords)) {
            $Where['title'] = array('like', '%'.$keywords.'%');
        }
        
        $Where['type_id'] = $type_id;
        $Where['is_del'] = 0;
        $countvideo = Db::name('uploads')->where($Where)->count('img_id');
        $pageObj = new Page($countvideo, 10);

        // 列表显示
        $videolist = Db::name('uploads')
            ->where($Where)
            ->order('img_id desc')
            ->limit($pageObj->firstRow.','.$pageObj->listRows)
            ->select();
        foreach ($videolist as $key => $val) {
            $val['image_url'] = handle_subdir_pic($val['image_url'], 'media');
            empty($val['title']) && $val['title'] = preg_replace('/^(.*)\/([^\/]+)$/i', '${2}', $val['image_url']);
            $videolist[$key] = $val;
        }
        $assign_data['videolist'] = $videolist;
        $assign_data['pageStr'] = $pageObj->show();

        /*----------------------------第三方存储插件 start------------------------*/
        $storageTitle = '本地服务器';
        $weappList = Db::name('weapp')->where(['status' => 1])
            ->cache(true, EYOUCMS_CACHE_TIME, 'weapp')
            ->getAllWithIndex('code');
        if (!empty($weappList['Qiniuyun']['data'])) {
            $qnyData = json_decode($weappList['Qiniuyun']['data'], true);
            if (!empty($qnyData['bucket'])) {
                $storageTitle = '七牛云';
            }
        } else if (!empty($weappList['AliyunOss']['data'])) {
            $ossData = json_decode($weappList['AliyunOss']['data'], true);
            if (!empty($ossData['bucket'])) {
                $storageTitle = '阿里云OSS';
            }
        } else if (!empty($weappList['Cos']['data'])) {
            $cosData = json_decode($weappList['Cos']['data'], true);
            if (!empty($cosData['secretName'])) {
                $storageTitle = '腾讯云COS';
            }
        } else if (!empty($weappList['AwsOss']['data'])) {
            $awsData = json_decode($weappList['AwsOss']['data'], true);
            if (!empty($awsData['secretName'])) {
                $storageTitle = '亚马逊S3';
            }
        }
        $assign_data['storageTitle'] = $storageTitle;
        /*----------------------------第三方存储插件 end------------------------*/

        // 分组视频总数
        $assign_data['countvideo'] = Db::name('uploads')->where(['group_code'=>'video','type_id' => $type_id, 'is_del' => 0])->count('img_id');

        $this->assign($assign_data);

        return $this->fetch('uploadvideonew/get_upload_list');
    }

    /**
     * 右侧视频目录的视频列表显示
     */
    public function get_dir_videolist($videos_path = 'uploads')
    {
        $assign_data = [];

        if ('uploads' != $videos_path && !preg_match('#^(uploads)/(.*)$#i', $videos_path)) {
            $this->error('禁止访问');
        }

        $num  = input('num/d', 1);
        $info = array(
            'num'  => $num,
        );
        $assign_data['info'] = $info;

        // 视频列表
        $list = [];
        // 递归获取所有视频文件
        $this->getVideoFilesRecursive($videos_path, $list);
        
        if (!empty($list)) {
            // 视频选择的时间从大到小排序
            $list_time = get_arr_column($list, 'add_time');
            array_multisort($list_time, SORT_DESC, $list);
        }

        // 每页显示的视频
        $count = count($list); // 总条数
        $pagesize = 10; // 每页多少条
        $page = input('param.p/d', 1);
        $start = ($page - 1) * $pagesize; // 偏移量，当前页-1乘以每页显示条数
        $list = array_slice($list, $start, $pagesize);
        $assign_data['list'] = $list;

        // 分页码
        $pageObj = new Page($count, $pagesize);
        $assign_data['pageStr'] = $pageObj->show();

        $this->assign($assign_data);

        return $this->fetch('uploadvideonew/get_dir_videolist');
    }
    
    /*
     * 删除上传的视频
     */
    public function delupload()
    {
        echo 1;
        exit;
    }

    /**
     * 检测指定目录是否存在视频
     *
     * @param string $directory 目录路径
     * @param string $dir_name 显示的目录前缀路径
     * @param array $arr_file 是否删除空目录
     * @return boolean
     */
    private function isExistVideo($directory, $dir_name='', &$arr_file = [])
    {
        if (!file_exists($directory) ) {
            return false;
        }

        if (!empty($arr_file)) {
            return true;
        }

        // 视频类型数组 - 使用 $this->video_type 以包含用户自定义的视频类型
        $video_ext = explode(',', $this->video_type);
        $mydir = dir($directory);
        $hasVideo = false;
        while($file = $mydir->read())
        {
            if((is_dir("$directory/$file")) AND ($file != ".") AND ($file != ".."))
            {
                if ($dir_name) {
                    $result = $this->isExistVideo("$directory/$file", "$dir_name/$file", $arr_file);
                } else {
                    $result = $this->isExistVideo("$directory/$file", "$file", $arr_file);
                }
                if ($result) {
                    $hasVideo = true;
                }
                
            }
            else if(($file != ".") AND ($file != ".."))
            {
                $fileArr = explode('.', $file);    
                $ext = end($fileArr);
                $ext = strtolower($ext);
                if (in_array($ext, $video_ext)) {
                    if ($dir_name) {
                        $arr_file[] = "$dir_name/$file";
                    } else {
                        $arr_file[] = "$file";
                    }
                    $hasVideo = true;
                }

            }
        }
        $mydir->close();

        return $hasVideo ? true : $arr_file;
    }

    /**
     * 提取上传视频目录下的所有视频
     *
     * @param string $directory 目录路径
     * @param string $dir_name 显示的目录前缀路径
     * @param array $arr_file 是否删除空目录
     * @param num $num 数量
     */
    private function getDirVideo($directory, &$arr_file = array(), &$num = 0, &$processed_dirs = []) {
        // 初始化已处理目录数组（只在第一次调用时）
        if (empty($processed_dirs)) {
            $processed_dirs = [];
        }
        
        // 检查目录是否已经处理过，避免重复添加
        $dirpath_normalized = str_replace('\\', '/', $directory);
        if (in_array($dirpath_normalized, $processed_dirs)) {
            return $arr_file;
        }
        
        $mydir = glob($directory.'/*', GLOB_ONLYDIR);
        $param = input('param.');
        if (0 <= $num) {
            $dirpathArr = explode('/', $directory);
            $level = count($dirpathArr);
            $open = (1 >= $level) ? true : false;
            $fileList = glob("$directory/*");
            $total = count($fileList); // 目录是否存在任意文件，否则删除该目录
            if (!empty($total)) {
                $isExistVideo = $this->isExistVideo($directory);
                if (!empty($isExistVideo)) {
                    $arr_file[] = [
                        'id'        => $num,
                        'url'       => url('Uploadvideonew/get_dir_videolist',['num'=>$param['num'],'lang'=>$this->admin_lang,'videos_path'=>$directory]),
                        'target'    => 'content_body',
                        'isParent'  => true,
                        'open'      => $open,
                        'dirpath'   => $directory,
                        'level'     => $level,
                        'total'     => $total,
                    ];
                    // 标记该目录已处理
                    $processed_dirs[] = $dirpath_normalized;
                }
            } else {
                @rmdir("$directory");
            }
        }
        if (!empty($mydir)) {
            foreach ($mydir as $key => $dir) {
                // 视频文件主要存储在 media 目录下，所以不过滤 media 目录
                // 只过滤临时目录和软件目录
                if (preg_match('/uploads\/(soft_tmp|tmp|allimg|soft)\//i', "$dir/")) {
                    continue;
                }
                $num++;
                $dirname = str_replace('\\', '/', $dir);
                $dirArr  = explode('/', $dirname);
                $dir     = end($dirArr);
                $mydir2  = glob("$directory/$dir/*", GLOB_ONLYDIR);
                $subdir_path = "$directory/$dir";
                $subdir_path_normalized = str_replace('\\', '/', $subdir_path);
                
                // 检查子目录是否已经处理过
                if (in_array($subdir_path_normalized, $processed_dirs)) {
                    continue;
                }
                
                if(!empty($mydir2) AND ($dir != ".") AND ($dir != ".."))
                {
                    $this->getDirVideo($subdir_path, $arr_file, $num, $processed_dirs);
                }
                else if(($dir != ".") AND ($dir != ".."))
                {
                    $dirpathArr = explode('/', $subdir_path);
                    $level = count($dirpathArr);
                    $fileList = glob("$subdir_path/*"); // 目录是否存在任意文件，否则删除该目录
                    $total = count($fileList);
                    if (!empty($total)) {
                         // 目录是否存在视频文件，否则删除该目录
                        $isExistVideo = $this->isExistVideo($subdir_path);
                        if (!empty($isExistVideo)) {
                            $arr_file[] = [
                                'id'        => $num,
                                'url'       => url('Uploadvideonew/get_dir_videolist',['num'=>$param['num'],'lang'=>$this->admin_lang,'videos_path'=>$subdir_path]),
                                'target'    => 'content_body',
                                'isParent'  => false,
                                'open'      => false,
                                'dirpath'   => $subdir_path,
                                'level'     => $level,
                                'icon'      => 'public/plugins/ztree/css/zTreeStyle/img/dir_close.png',
                                'iconOpen'  => 'public/plugins/ztree/css/zTreeStyle/img/dir_open.png',
                                'iconClose' => 'public/plugins/ztree/css/zTreeStyle/img/dir_close.png',
                                'total'     => $total,
                            ];
                            // 标记该目录已处理
                            $processed_dirs[] = $subdir_path_normalized;
                        }
                    } else {
                        @rmdir($subdir_path);
                    }
                }
            }
        }
        return $arr_file;
    }

    /**
     *  远程视频本地化
     * @access    public
     * @return    string
     */
    public function ajax_remote_to_videolocal()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            $type_id = !empty($post['type_id']) ? $post['type_id'] : 0;
            $videoremoteurl = !empty($post['videoremoteurl']) ? trim($post['videoremoteurl']) : '';
            if (empty($videoremoteurl)) {
                $this->error("请输入视频地址！");
            } else if (!is_http_url($videoremoteurl)) {
                $this->error("请输入有效的视频地址！");
            }
            
            // 下载远程视频
            $reData = saveRemoteVideo($videoremoteurl, 'media/');
            $reData = json_decode($reData, true);
            if ('SUCCESS' != $reData['state']) {
                $msg = !empty($reData['state']) ? $reData['state'] : '源网站视频可能已开启防盗链功能！';
                $this->error($msg);
            } else {
                if (is_http_url($reData['url'])) {
                    $video_url = $reData['url'];
                } else {
                    $video_url = handle_subdir_pic($reData['url'], 'media');
                }
            }

            // 添加视频进数据库
            $addData = [
                'aid'         => 0,
                'type_id'     => $type_id,
                'group_code'  => 'video',
                'image_url'   => $video_url,
                'title'       => !empty($reData['title']) ? $reData['title'] : '',
                'intro'       => '',
                'width'       => !empty($reData['width']) ? $reData['width'] : 0,
                'height'      => !empty($reData['height']) ? $reData['height'] : 0,
                'filesize'    => !empty($reData['size']) ? $reData['size'] : 0,
                'mime'        => !empty($reData['mime']) ? $reData['mime'] : 'video/mp4',
                'users_id'    => (int)session('admin_info.syn_users_id'),
                'sort_order'  => 100,
                'add_time'    => getTime(),
                'update_time' => getTime(),
            ];
            Db::name('uploads')->add($addData);
            $this->success('提取成功', null, $addData);
        }
        $this->error('提取失败');
    }

    /*------------------------------------新版上传视频带分组 start-----------------------------------*/
    //添加分组
    public function Addtype()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            $upload_type = trim($post['upload_type']);
            if (empty($upload_type)) $this->error("请输入分组名称");

            $data = [
                'group_code'  => 'video',
                'upload_type' => $upload_type,
                'add_time'    => getTime(),
                'update_time' => getTime(),
            ];
            $rs = Db::name('UploadsType')->add($data);
            if (!empty($rs)) {
                $this->success("分组添加成功");
            }
        }
        $this->error("分组添加失败");
    }

    // 编辑分组
    public function EditType()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');

            $type_id = $post['type_id'];
            if (empty($type_id)) $this->error("请选择分组");

            $upload_type = trim($post['upload_type']);
            if (empty($upload_type)) $this->error("请输入分组名称");

            $data = [
                'upload_type' => $upload_type,
                'update_time' => getTime(),
            ];
            $rs = Db::name('UploadsType')->where('id', $type_id)->update($data);
            if (!empty($rs)) {
                $this->success("分组编辑成功");
            }
        }
        $this->error("分组编辑失败");
    }

    // 删除分组
    public function DelType()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');

            $type_id = $post['type_id'];
            if (empty($type_id)) $this->error("请选择分组");

            $rs = Db::name('UploadsType')->where(['group_code'=>'video','id' => $type_id])->delete();
            if (!empty($rs)) {
                Db::name('uploads')->where(['group_code'=>'video','type_id' => $type_id])->update(['type_id' => 0, 'update_time'=>getTime()]);
                $this->success("删除分组成功");
            }
        }
        $this->error("删除分组失败");
    }
    
    // 批量删除
    public function del_uploadsvideo() 
    {
        $del_type = 'img_id';
        $video_ids = input('img_id/a');
        foreach ($video_ids as $key => $val) {
            if (!is_numeric($val)) {
                $del_type = 'image_url';
                $val = mchStrCode($val, 'DECODE');
                if (empty($val) || !preg_match("/^([\w\-\/\.]+)\.(".str_replace(',', '|', $this->video_type).")$/i", $val)) {
                    unset($video_ids[$key]);
                }
            } else {
                $val = intval($val);
            }
            $video_ids[$key] = $val;
        }
        if (IS_POST && !empty($video_ids)) {
            if ('image_url' == $del_type) {
                foreach ($video_ids as $key => $val) {
                    $rs = @unlink(ROOT_PATH.$val);
                }
            } else {
                $rs = Db::name('uploads')->where("img_id", 'IN', $video_ids)->delete();
                // $rs = Db::name('uploads')->where("img_id", 'IN', $video_ids)->update(['is_del' => 1, 'update_time'=>getTime()]);
            }
            if ($rs !== false) {
                $this->success("删除成功");
            }
        }
        $this->error("删除失败");
    }

    // 彻底删除视频（包括服务器上的文件）
    public function del_uploadsvideo_completely() 
    {
        $video_ids = input('img_id/a');
        if (IS_POST && !empty($video_ids)) {
            // 验证并处理video_id
            foreach ($video_ids as $key => $val) {
                if (!is_numeric($val)) {
                    unset($video_ids[$key]);
                } else {
                    $video_ids[$key] = intval($val);
                }
            }
            
            if (empty($video_ids)) {
                $this->error("参数错误");
            }

            // 查询视频信息
            $video_list = Db::name('uploads')->field('img_id,image_url')->where("img_id", 'IN', $video_ids)->select();
            
            if (empty($video_list)) {
                $this->error("未找到要删除的视频");
            }

            $delete_count = 0;
            $file_delete_count = 0;
            
            // 删除视频文件
            foreach ($video_list as $video) {
                $video_url = $video['image_url'];
                
                // 只删除本地视频，远程视频跳过
                if (!is_http_url($video_url)) {
                    // 处理视频路径，去除ROOT_DIR前缀
                    $file_path = preg_replace('#^'.preg_quote(ROOT_DIR, '#').'#i', '', $video_url);
                    $file_path = ltrim($file_path, '/');
                    
                    // 构建完整文件路径
                    $full_path = ROOT_PATH . $file_path;
                    
                    // 安全检查：确保路径在uploads目录下
                    if (strpos($file_path, 'uploads/') === 0 && file_exists($full_path)) {
                        if (@unlink($full_path)) {
                            $file_delete_count++;
                        }
                    }
                }
            }
            
            // 删除数据库记录
            $rs = Db::name('uploads')->where("img_id", 'IN', $video_ids)->delete();
            
            if ($rs !== false) {
                $msg = "删除成功";
                if ($file_delete_count > 0) {
                    $msg .= "，已删除 {$file_delete_count} 个视频文件";
                }
                $this->success($msg);
            } else {
                $this->error("删除失败");
            }
        }
        $this->error("删除失败");
    }
    /*------------------------------------新版上传视频带分组 end-----------------------------------*/

    /**
     * 递归获取目录下的所有视频文件
     *
     * @param string $directory 目录路径
     * @param array &$list 视频文件列表（引用传递）
     */
    private function getVideoFilesRecursive($directory, &$list = [])
    {
        if (!is_dir($directory)) {
            return;
        }

        // 视频类型数组 - 使用 $this->video_type 以包含用户自定义的视频类型
        $video_ext = explode(',', $this->video_type);
        
        // 获取当前目录下的所有文件和目录
        $files = glob($directory.'/*');
        if (empty($files)) {
            return;
        }

        foreach ($files as $file) {
            // 如果是目录，递归处理
            if (is_dir($file)) {
                $this->getVideoFilesRecursive($file, $list);
                continue;
            }

            // 检查文件是否存在
            if (!file_exists($file)) {
                continue;
            }

            // 检查文件扩展名
            $fileArr = explode('.', $file);
            $ext = end($fileArr);
            $ext = strtolower(trim($ext));
            
            if (in_array($ext, $video_ext)) {
                $info = [];
                $info['file_path'] = ROOT_DIR.'/'.$file;
                $info['add_time'] = @filemtime($file);
                empty($info['add_time']) && $info['add_time'] = getTime();
                $info['title'] = preg_replace('/^(.*)\/([^\/]+)$/i', '${2}', $file);
                $info['filesize'] = @filesize($file);
                $info['width'] = 0;
                $info['height'] = 0;
                $list[] = $info;
            }
        }
    }

    /**
     * 根据视频URL获取ey_uploads表中的title
     */
    public function get_video_title()
    {
        if (IS_AJAX) {
            $video_url = input('video_url/s', '');
            if (empty($video_url)) {
                $this->error('视频URL不能为空');
            }
            
            // 标准化URL，去除可能的子目录前缀和协议
            $normalized_url = $video_url;
            // 如果是完整URL，提取路径部分
            if (preg_match('/^https?:\/\//i', $normalized_url)) {
                $parsed = parse_url($normalized_url);
                $normalized_url = !empty($parsed['path']) ? $parsed['path'] : $normalized_url;
            }
            // 去除开头的斜杠
            $normalized_url = ltrim($normalized_url, '/');
            // 去除ROOT_DIR前缀（如果存在）
            $normalized_url = preg_replace('#^'.preg_quote(ROOT_DIR, '#').'/?#i', '', $normalized_url);
            $normalized_url = ltrim($normalized_url, '/');
            $basename = basename($normalized_url);
            
            // 从ey_uploads表查询title，尝试多种匹配方式
            $video_info = Db::name('uploads')
                ->field('title')
                ->where('group_code', 'video')
                ->where('is_del', 0)
                ->where(function($query) use ($video_url, $normalized_url, $basename) {
                    $query->where('image_url', $video_url)
                          ->whereOr('image_url', 'like', '%' . $normalized_url . '%')
                          ->whereOr('image_url', 'like', '%' . $basename . '%');
                })
                ->order('img_id desc')
                ->find();
            
            if (!empty($video_info) && !empty($video_info['title'])) {
                $this->success('获取成功', null, ['title' => $video_info['title']]);
            } else {
                $this->error('未找到对应的视频记录');
            }
        }
        $this->error('请求方式错误');
    }
}

