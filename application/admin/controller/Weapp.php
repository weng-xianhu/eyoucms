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

use think\Page;
use think\Db;
use app\admin\logic\WeappLogic;

/**
 * 插件控制器
 */
class Weapp extends Base
{
    public $weappM;
    public $weappLogic;
    public $plugins = array();

    /*
     * 前置操作
     */
    protected $beforeActionList = array(
        'init'
    );


    /*
     * 初始化操作
     */
    public function _initialize() {
        parent::_initialize();
        $this->weappM = model('Weapp');
        $this->weappLogic = new WeappLogic();
        //  更新插件
        $this->weappLogic->insertWeapp();
    }

    public function init(){
        /*权限控制 by 小虎哥*/
        if (0 < intval(session('admin_info.role_id'))) {
            $auth_role_info = session('admin_info.auth_role_info');
            if(! empty($auth_role_info)){
                if(! empty($auth_role_info['permission']['plugins'])){
                    foreach ($auth_role_info['permission']['plugins'] as $plugins){
                        if(isset($plugins['code'])){
                            $this->plugins[] = $plugins['code'];
                        }
                    }
                }
            }
        }
        /*--end*/
    }

    /*
     * 插件列表
     */
    public function index()
    {
        $root_dir = ROOT_DIR;
        if (!empty($root_dir)) {
            $this->error('子目录暂时不支持插件，待完善中……');
        }
        
        $assign_data = array();
        $condition = array();
        // 获取到所有GET参数
        $get = input('get.');

        // 应用搜索条件
        foreach (['keywords'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                if ($key == 'keywords') {
                    $condition['a.name|code'] = array('LIKE', "%{$get[$key]}%");
                } else {
                    $condition['a.'.$key] = array('eq', $get[$key]);
                }
            }
        }

        /*权限控制 by 小虎哥*/
        if(! empty($this->plugins)){
            $condition['a.code'] = array('in', $this->plugins);
        }
        /*--end*/

        $weappArr = array(); // 插件标识数组

        /**
         * 数据查询，搜索出主键ID的值
         */
        $count = DB::name('weapp')->alias('a')->where($condition)->count();// 查询满足要求的总记录数
        $Page = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = DB::name('weapp')
            ->field('a.*')
            ->alias('a')
            ->where($condition)
            ->order('a.sort_order asc, a.id desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->getAllWithIndex('id');
        foreach ($list as $key => $val) {
            $config = include WEAPP_PATH.$val['code'].DS.'config.php';
            $config['description'] = filter_line_return($config['description'], '<br/>');
            $val['config'] = $config;
            $val['version'] = getWeappVersion($val['code']);

            switch ($val['status']) {
                case '-1':
                    $status_text = '禁用';
                    break;

                case '1':
                    $status_text = '启用';
                    break;

                default:
                    $status_text = '未安装';
                    break;
            }
            $val['status_text'] = $status_text;

            $list[$key] = $val;

            /*插件标识数组*/
            $weappArr[$val['code']] = array(
                'code'  => $val['code'],
                'version'  => $val['version'],
            );
            /*--end*/
        }
        $show = $Page->show(); // 分页显示输出
        $assign_data['page'] = $show; // 赋值分页输出
        $assign_data['list'] = $list; // 赋值数据集
        $assign_data['pager'] = $Page; // 赋值分页对象

        /*检测更新*/
        $weapp_upgrade = array();
        if (!empty($weappArr)) {
            // 标识
            $codeArr = get_arr_column($weappArr, 'code');
            $codeStr = implode(',', $codeArr);
            // 版本号
            $versionArr = get_arr_column($weappArr, 'version');
            $versionStr = implode(',', $versionArr);
            // URL参数
            $vaules = array(
                'domain'    => request()->host(true),
                'code'      => $codeStr,
                'v'         => $versionStr,
            );
            $tmp_str = 'L2luZGV4LnBocD9tPWFwaSZjPVdlYXBwJmE9Y2hlY2tCYXRjaFZlcnNpb24m';
            $service_url = base64_decode(config('service_ey')).base64_decode($tmp_str);
            $url = $service_url.http_build_query($vaules);
            $context = stream_context_set_default(array('http' => array('timeout' => 3,'method'=>'GET')));
            $response = @file_get_contents($url,false,$context);
            $batch_upgrade = json_decode($response,true);

            if (is_array($batch_upgrade) && !empty($batch_upgrade)) {
                $weapp_upgrade = $this->weappLogic->checkBatchVersion($batch_upgrade); //升级包消息 
            }
        }
        $assign_data['weapp_upgrade'] = $weapp_upgrade;
        /*--end*/

        $this->assign($assign_data);
        return $this->fetch();
    }

    /**
     *  执行插件控制器
     *  控制模块  参数m
     *  控制器名  参数c来确定
     *  控制器里-操作名  参数a
     *  http://网站域名/login.php/weapp/execute?m=login&c=Qq&a=callback
     */
    public function execute($sm = '', $sc = '', $sa = '')
    {
        if (!IS_AJAX) {
            $msg = $this->weappLogic->checkInstall();
            if ($msg !== true) {
                $this->error($msg, url('Weapp/index'));
            }
        }
        $sm = request()->param('sm');
        $sc = request()->param('sc');
        $sa = request()->param('sa');

        /*插件转为内置*/
        if ('Smtpmail' == $sm) {
            $this->success('该插件已迁移，前往中…', url('System/smtp'));
        }
        /*--end*/

        $controllerName = !empty($sc) ? $sc : $sm;
        $actionName = !empty($sa) ? $sa : "index";
        $class_path = "\\".WEAPP_DIR_NAME."\\".$sm."\\controller\\".$controllerName;
        $controller = new $class_path();
        $result = $controller->$actionName();
        return $result;
    }

    /**
     * 安装插件
     */
    public function install($id){
        $row      =   M('Weapp')->field('name,code,thorough,config')->find($id);
        $row['config'] = json_decode($row['config'], true);
        $class    =   get_weapp_class($row['code']);
        if (!class_exists($class)) {
            $this->error('插件不存在！');
        }
        $weapp  =   new $class;
        if(!$weapp->checkConfig()) {//检测信息的正确性
            $this->error('插件config配置参数不全！');
        }
        $cms_version = getCmsVersion();
        $min_version = $row['config']['min_version'];
        if ($cms_version < $min_version) {
            $this->error('当前CMS版本太低，该插件要求CMS版本 >= '.$min_version.'，请升级系统！');
        }
        /*插件安装的前置操作（可无）*/
        $this->beforeInstall($weapp);
        /*--end*/

        if (true) {
            /*插件sql文件*/
            $sqlfile = WEAPP_DIR_NAME.DS.$row['code'].DS.'data'.DS.'install.sql';
            if (empty($row['thorough']) && file_exists($sqlfile)) {
                $execute_sql = file_get_contents($sqlfile);
                $sqlFormat = $this->sql_split($execute_sql, PREFIX);
                /**
                 * 执行SQL语句
                 */
                $counts = count($sqlFormat);

                for ($i = 0; $i < $counts; $i++) {
                    $sql = trim($sqlFormat[$i]);

                    if (strstr($sql, 'CREATE TABLE')) {
                        Db::execute($sql);
                    } else {
                        if(trim($sql) == '')
                           continue;
                        Db::execute($sql);
                    }
                }
            }
            /*--end*/
            $r = M('weapp')->where('id',$id)->update(array('thorough'=>1,'status'=>1,'add_time'=>getTime()));
            if ($r) {
                cache('hooks', null);
                cache("hookexec_".$row['code'], null);
                \think\Cache::clear('hooks');
                /*插件安装的后置操作（可无）*/
                $this->afterInstall($weapp);
                /*--end*/
                adminLog('安装插件：'.$row['name']);
                $this->success('安装成功', url('Weapp/index'));
                exit;
            }
        }

        $this->error('安装失败');
    }

    /**
     * 卸载插件
     */
    public function uninstall(){
        $id       =   input('param.id/d', 0);
        $thorough = input('param.thorough/d', 0);
        $row      =   M('Weapp')->field('name,code')->find($id);
        $class    =   get_weapp_class($row['code']);
        if (!class_exists($class)) {
            $this->error('插件不存在！');
        }
        $weapp  =   new $class;

        // 插件卸载的前置操作（可无）
        $this->beforeUninstall($weapp);
        /*--end*/

        if (true) {
            $is_uninstall = false;
            if (1 == $thorough) {
                $r = M('weapp')->where('id',$id)->update(array('thorough'=>$thorough,'status'=>0,'add_time'=>getTime()));
            } else if (0 == $thorough) {
                $r = M('weapp')->where('id',$id)->update(array('thorough'=>$thorough,'status'=>0,'update_time'=>getTime()));
                $r && $is_uninstall = true;
            }
            if (false !== $r) {
               /*插件sql文件，不执行删除插件数据表*/
                $sqlfile = WEAPP_DIR_NAME.DS.$row['code'].DS.'data'.DS.'uninstall.sql';
                if (empty($thorough) && file_exists($sqlfile)) {
                    $execute_sql = file_get_contents($sqlfile);
                    $sqlFormat = $this->sql_split($execute_sql, PREFIX);
                    /**
                     * 执行SQL语句
                     */
                    $counts = count($sqlFormat);

                    for ($i = 0; $i < $counts; $i++) {
                        $sql = trim($sqlFormat[$i]);

                        if (strstr($sql, 'CREATE TABLE')) {
                            Db::execute($sql);
                        } else {
                            if(trim($sql) == '')
                               continue;
                            Db::execute($sql);
                        }
                    }
                }
                /*--end*/

                cache('hooks', null);
                cache("hookexec_".$row['code'], null);
                \think\Cache::clear('hooks');
                /*插件卸载的后置操作（可无）*/
                $this->afterUninstall($weapp);
                /*--end*/

                // 删除插件相关文件
                if ($is_uninstall) {
                    $rdel = M('weapp')->where('id',$id)->delete();
                    $this->unlinkcode($row['code']);
                }

                adminLog('卸载插件：'.$row['name']);
                $this->success('卸载成功', url('Weapp/index'));
                exit;
            }
        }

        $this->error('卸载失败');
    }

    /**
     * 启用插件
     */
    public function enable()
    {
        $id       =   input('param.id/d', 0);
        if (0 < $id) {
            $row = M('weapp')->field('code')->find($id);
            $class    =   get_weapp_class($row['code']);
            if (!class_exists($class)) {
                $this->error('插件不存在！');
            }
            $weapp  =   new $class;
            /*插件启用的前置操作（可无）*/
            $this->beforeEnable($weapp);
            /*--end*/
            $r = M('weapp')->where('id',$id)->update(array('status'=>1,'update_time'=>getTime()));
            if ($r) {
                /*插件启用的后置操作（可无）*/
                $this->afterEnable($weapp);
                /*--end*/
                cache("hookexec_".$row['code'], null);
                cache('hooks', null);
                \think\Cache::clear('hooks');
                $this->success('操作成功！', url('Weapp/index'));
                exit;
            }
        }
        $this->error('操作失败！');
        exit;
    }

    /**
     * 禁用插件
     */
    public function disable()
    {
        $id       =   input('param.id/d', 0);
        if (0 < $id) {
            $row = M('weapp')->field('code')->find($id);
            $class    =   get_weapp_class($row['code']);
            if (!class_exists($class)) {
                $this->error('插件不存在！');
            }
            $weapp  =   new $class;
            /*插件禁用的前置操作（可无）*/
            $this->beforeDisable($weapp);
            /*--end*/
            $r = M('weapp')->where('id',$id)->update(array('status'=>-1,'update_time'=>getTime()));
            if ($r) {
                /*插件禁用的后置操作（可无）*/
                $this->afterDisable($weapp);
                /*--end*/
                cache("hookexec_".$row['code'], null);
                cache('hooks', null);
                \think\Cache::clear('hooks');
                $this->success('操作成功！', url('Weapp/index'));
                exit;
            }
        }
        $this->error('操作失败！');
        exit;
    }
    
    /**
     * 删除插件以及文件
     */
    public function del()
    {
        if (IS_POST) {
            $id_arr = input('del_id/a');
            $id_arr = eyIntval($id_arr);
            if(!empty($id_arr)){
                $result = M('weapp')->field('id,name,code')
                    ->where([
                        'id'    => ['IN', $id_arr],
                    ])->select();
                $name_list = get_arr_column($result, 'name');

                $r = M('weapp')->where([
                        'id'    => ['IN', $id_arr],
                    ])
                    ->delete();
                if($r){
                    /*清理插件相关文件*/
                    foreach ($result as $key => $val) {
                        $unbool = $this->unlinkcode($val['code']);
                        if (true == $unbool) {
                            continue;
                        }
                    }
                    /*--end*/

                    adminLog('删除插件：'.implode(',', $name_list));
                    $this->success('删除成功');
                }else{
                    $this->error('删除失败');
                }
            } else {
                $this->error('参数有误');
            }
        }
        $this->error('非法访问');
    }

    /**
     * 清理插件相关文件
     */
    private function unlinkcode($code)
    {
        $filelist_path = WEAPP_DIR_NAME.DS.$code.DS.'filelist.txt';
        if (file_exists($filelist_path)) {
            $filelistStr = file_get_contents($filelist_path);
            $filelist = explode("\n\r", $filelistStr);
            if (empty($filelist)) {
                return true;
            }
            delFile(WEAPP_DIR_NAME.DS.$code, true);
            foreach ($filelist as $k2 => $v2) {
                if (!empty($v2) && !preg_match('/^'.WEAPP_DIR_NAME.'\/'.$code.'/i', $v2)) {
                    if (file_exists($v2) && is_file($v2)) {
                        @unlink($v2);
                    }
                }
            }
            delFile(WEAPP_DIR_NAME.DS.$code, true);
        }

        return true;
    }

    /**
     * 分解SQL文件的语句
     */
    public function sql_split($sql, $tablepre) {

        $sql = str_replace("`#@__", '`'.$tablepre, $sql);

        $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);
        
        $sql = str_replace("\r", "\n", $sql);
        $ret = array();
        $num = 0;
        $queriesarray = explode(";\n", trim($sql));
        unset($sql);
        foreach ($queriesarray as $query) {
            $ret[$num] = '';
            $queries = explode("\n", trim($query));
            $queries = array_filter($queries);
            foreach ($queries as $query) {
                $str1 = substr($query, 0, 1);
                if ($str1 != '#' && $str1 != '-')
                    $ret[$num] .= $query;
            }
            if ((!stristr($ret[$num], 'SET FOREIGN_KEY_CHECKS') && !stristr($ret[$num], 'SET NAMES')) && false === stripos($ret[$num], $tablepre.'weapp_')) {
                $this->error('请删除不相干的SQL语句，或者数据表前缀是否符合插件规范（#@__weapp_）');
            }
            $num++;
        }
        return $ret;
    }

    /**
     * 插件安装的前置操作（可无）
     */
    public function beforeInstall($weappClass){
        if (method_exists($weappClass, 'beforeInstall')) {
            $weappClass->beforeInstall();
        }
    }

    /**
     * 插件安装的后置操作（可无）
     */
    public function afterInstall($weappClass){
        if (method_exists($weappClass, 'afterInstall')) {
            $weappClass->afterInstall();
        }
    }

    /**
     * 插件卸载的前置操作（可无）
     */
    public function beforeUninstall($weappClass){
        if (method_exists($weappClass, 'beforeUninstall')) {
            $weappClass->beforeUninstall();
        }
    }

    /**
     * 插件卸载的后置操作（可无）
     */
    public function afterUninstall($weappClass){
        if (method_exists($weappClass, 'afterUninstall')) {
            $weappClass->afterUninstall();
        }
    }

    /**
     * 插件启用的前置操作（可无）
     */
    public function beforeEnable($weappClass){
        if (method_exists($weappClass, 'beforeEnable')) {
            $weappClass->beforeEnable();
        }
    }

    /**
     * 插件启用的后置操作（可无）
     */
    public function afterEnable($weappClass){
        if (method_exists($weappClass, 'afterEnable')) {
            $weappClass->afterEnable();
        }
    }

    /**
     * 插件禁用的前置操作（可无）
     */
    public function beforeDisable($weappClass){
        if (method_exists($weappClass, 'beforeDisable')) {
            $weappClass->beforeDisable();
        }
    }

    /**
     * 插件禁用的后置操作（可无）
     */
    public function afterDisable($weappClass){
        if (method_exists($weappClass, 'afterDisable')) {
            $weappClass->afterDisable();
        }
    }

    /**
     * 上传插件并解压
     */
    public function upload() 
    {
        //防止php超时
        function_exists('set_time_limit') && set_time_limit(0);
        
        if (IS_POST) {
            $fileExt = 'zip';
            $savePath = UPLOAD_PATH.'tmp'.DS;
            $image_upload_limit_size = intval(tpCache('basic.file_size') * 1024 * 1024);
            $file = request()->file('weappfile');
            if(empty($file)){
                $this->error('请先上传zip文件');
            }
            $error = $file->getError();
            if(!empty($error)){
                $this->error($error);
            }
            $result = $this->validate(
                ['file' => $file], 
                ['file'=>'fileSize:'.$image_upload_limit_size.'|fileExt:'.$fileExt],
                ['file.fileSize' => '上传文件过大','file.fileExt'=>'上传文件后缀名必须为'.$fileExt]
            );
            if (true !== $result || empty($file)) {
                $this->error($result);
            }
            // 移动到框架应用根目录/public/upload/tmp/ 目录下
            $fileName = md5(getTime().uniqid(mt_rand(), TRUE)).'.'.$fileExt; // 上传之后的文件全名
            $folderName = str_replace(".zip", "", $fileName);  // 文件名，不带扩展名
            /*使用自定义的文件保存规则*/
            $info = $file->rule(function ($file) {
                return  $folderName;
            })->move($savePath, $folderName);
            /*--end*/
            if ($info) {
                $filepath = $savePath.$fileName;
                if (file_exists($filepath)) {
                    /*解压之前，删除存在的文件夹*/
                    delFile($savePath.$folderName);
                    /*--end*/

                    /*解压文件*/
                    $zip = new \ZipArchive();//新建一个ZipArchive的对象
                    if ($zip->open($savePath.$fileName) != true) {
                        $this->error("插件压缩包读取失败!");
                    }
                    $zip->extractTo($savePath.$folderName.DS);//假设解压缩到在当前路径下插件名称文件夹内
                    $zip->close();//关闭处理的zip文件
                    /*--end*/
                    
                    /*获取插件目录名称*/
                    $dirList = glob($savePath.$folderName.DS.WEAPP_DIR_NAME.DS.'*');
                    $weappPath = !empty($dirList) ? $dirList[0] : '';
                    if (empty($weappPath)) {
                        $this->error('插件压缩包缺少目录文件');
                    }
                    
                    $weappPath = str_replace("\\", DS, $weappPath);
                    $weappPathArr = explode(DS, $weappPath);
                    $weappName = $weappPathArr[count($weappPathArr) - 1];
                    // if (is_dir(ROOT_PATH.WEAPP_DIR_NAME.DS.$weappName)) {
                    //     $this->error("已存在同名插件{$weappName}，请手工移除".WEAPP_DIR_NAME.DS.$weappName."目录");
                    // }
                    /*--end*/

                    // 递归复制文件夹            
                    $copy_bool = recurse_copy($savePath.$folderName, rtrim(ROOT_PATH, DS));
                    if (true !== $copy_bool) {
                        $this->error($copy_bool);
                    }

                    /*删除上传的插件包*/
                    @unlink(realpath($savePath.$fileName));
                    @delFile($savePath.$folderName, true);
                    /*--end*/

                    /*安装插件*/
                    $configfile = WEAPP_DIR_NAME.DS.$weappName.'/config.php';
                    if (file_exists($configfile)) {
                        $configdata = include($configfile);
                        $code = isset($configdata['code']) ? $configdata['code'] : 'error_'.date('Ymd');
                        Db::name('weapp')->where(['code'=>$code])->delete();

                        $addData = [
                            'code'          => $code,
                            'name'          => isset($configdata['name']) ? $configdata['name'] : '配置信息不完善',
                            'config'        => empty($configdata) ? '' : json_encode($configdata),
                            'add_time'      => getTime(),
                        ];
                        $weapp_id = Db::name('weapp')->insertGetId($addData);
                        if (!empty($weapp_id)) {
                            $this->install($weapp_id);
                        }
                    }
                    /*--end*/
                }
            }else{
                //上传错误提示错误信息
                $this->error($info->getError());
            }
        }
    }

    /**
     * 一键更新插件
     */
    public function OneKeyUpgrade()
    {
        header('Content-Type:application/json; charset=utf-8');
        $code = input('param.code/s', '');
        $upgradeMsg = $this->weappLogic->OneKeyUpgrade($code); //一键更新插件
        respose($upgradeMsg);
    }

    /**
     * 检查插件是否有更新包
     * @return type 提示语
     */
    public function checkVersion()
    { 
        // error_reporting(0);//关闭所有错误报告
        $upgradeMsg = $this->weappLogic->checkVersion(); //升级包消息   
        respose($upgradeMsg);
    }

    /**
     * 创建初始插件结构
     */
    public function create()
    {
        $sample = 'Sample';
        $srcPath = DATA_NAME.DS.WEAPP_DIR_NAME.DS.$sample;

        if (IS_POST) {
            $post = input('post.');
            $code = trim($post['code']);
            if (!preg_match('/^[A-Z]([a-zA-Z0-9]*)$/', $code)) {
                $this->error('插件标识格式不正确！');
            }
            if ('Sample' == $code) {
                $this->error('插件标识已被占用！');
            }
            if (!preg_match('/^v([0-9]+)\.([0-9]+)\.([0-9]+)$/', $post['version'])) {
                $this->error('插件版本号格式不正确！');
            }
            if (empty($post['min_version'])) {
                $post['min_version'] = getCmsVersion();
            }
            if (empty($post['version'])) {
                $post['version'] = 'v1.0.0';
            }

            /*复制样本结构到插件目录下*/
            $srcFiles = getDirFile($srcPath);
            $filetxt = '';
            foreach ($srcFiles as $key => $srcfile) {
                $dstfile = str_replace($sample, $code, $srcfile);
                $dstfile = str_replace(strtolower($sample), strtolower($code), $dstfile);
                if (!preg_match('/^'.WEAPP_DIR_NAME.'\/'.$code.'/i', $dstfile)) {
                    $filetxt .= $dstfile."\n\r";
                }
                if(tp_mkdir(dirname($dstfile))) {
                    $fileContent = file_get_contents($srcPath . DS . $srcfile);
                    if (preg_match('/\.sql$/i', $dstfile)) {
                        $fileContent = str_replace(strtolower($sample), uncamelize($code), $fileContent);
                    } else {
                        $fileContent = str_replace($sample, $code, $fileContent);
                        $fileContent = str_replace(strtolower($sample), strtolower($code), $fileContent);
                    }
                    $puts = @file_put_contents($dstfile, $fileContent); //初始化插件文件列表   
                    if (!$puts) {
                        $this->error('写入文件内容 ' . $dstfile . ' 失败');
                        exit;
                    }
                }
            }
            $filetxt .= WEAPP_DIR_NAME.'/'.$code;
            @file_put_contents(WEAPP_DIR_NAME.DS.$code.DS.'filelist.txt', $filetxt); //初始化插件文件列表  
            /*--end*/

            /*读取配置文件，并替换插件信息*/
            $configPath = WEAPP_DIR_NAME.DS.$code.DS.'config.php';
            if (!eyPreventShell($configPath) || !file_exists($configPath)) {
                $this->error('创建插件结构不完整，请重新创建！');
            }
            $strConfig = file_get_contents(WEAPP_DIR_NAME.DS.$code.DS.'config.php');
            $strConfig = str_replace('#CODE#', $code, $strConfig);
            $strConfig = str_replace('#NAME#', $post['name'], $strConfig);
            $strConfig = str_replace('#VERSION#', $post['version'], $strConfig);
            $strConfig = str_replace('#MIN_VERSION#', $post['min_version'], $strConfig);
            $strConfig = str_replace('#AUTHOR#', $post['author'], $strConfig);
            $strConfig = str_replace('#DESCRIPTION#', $post['description'], $strConfig);
            $strConfig = str_replace('#SCENE#', $post['scene'], $strConfig);
            @chmod(WEAPP_DIR_NAME.DS.$code.DS.'config.php'); //配置文件的地址
            $puts = @file_put_contents(WEAPP_DIR_NAME.DS.$code.DS.'config.php', $strConfig); //配置文件的地址    
            if (!$puts) {
                $this->error('替换插件信息失败，请设置目录权限为 755！');
            }
            /*--end*/

            $this->success('初始化插件成功，请在该插件基础上进行二次开发！', url('Weapp/index'), [], 3);
        }

        /*删除多余目录以及文件，兼容v1.1.7之后的版本*/
        if (file_exists($srcPath.DS.'application'.DS.'weapp')) {
            delFile($srcPath.DS.'application'.DS.'weapp', true);
        }
        if (file_exists($srcPath.DS.'template'.DS.'weapp')) {
            delFile($srcPath.DS.'template'.DS.'weapp', true);
        }
        if (file_exists($srcPath.DS.'weapp'.DS.$sample.DS.'behavior'.DS.'weapp')) {
            delFile($srcPath.DS.'weapp'.DS.$sample.DS.'behavior'.DS.'weapp', true);
        }
        if (file_exists($srcPath.DS.'weapp'.DS.$sample.DS.'template'.DS.'skin'.DS.'font')) {
            delFile($srcPath.DS.'weapp'.DS.$sample.DS.'template'.DS.'skin'.DS.'font', true);
        }
        if (file_exists($srcPath.DS.'weapp'.DS.$sample.DS.'common.php')) {
            @unlink($srcPath.DS.'weapp'.DS.$sample.DS.'common.php');
        }
        /*--end*/

        $assign_data = array();
        $assign_data['min_version'] = getCmsVersion();

        $this->assign($assign_data);
        return $this->fetch();
    }

    /**
     * 打包插件
     */
    public function pack()
    {
        if (IS_POST) {
            $packfiles = array(); // 打包的全部文件列表

            $post = input('post.');
            $code = $post['code'];
            $additional_file = $post['additional_file'];

            if (!preg_match('/^[A-Z]([a-zA-Z0-9]*)$/', $code)) {
                $this->error('插件标识格式不正确！');
            } else if (!file_exists(WEAPP_DIR_NAME.DS.$code)) {
                $this->error('该插件不存在！');
            }
            if (empty($additional_file)) {
                $this->error('打包文件不能为空！');
            }

            /*额外打包文件*/
            if (!empty($additional_file)) {
                $file_arr = explode(PHP_EOL, $additional_file);
                foreach ($file_arr as $key => $val) {
                    if (empty($val)) {
                        continue;
                    }
                    if (eyPreventShell($val) && is_file($val) && file_exists($val)) {
                        $packfiles[$val] = $val;
                    } else if (eyPreventShell($val) && is_dir($val) && file_exists($val)) {
                        $dirfiles = getDirFile($val, $val);
                        foreach ($dirfiles as $k2 => $v2) {
                            $packfiles[$v2] = $v2;
                        }
                    }
                }
            }
            /*--end*/

            /*压缩插件目录*/
            $zip = new \ZipArchive();//新建一个ZipArchive的对象
            $filepath = DATA_PATH.WEAPP_DIR_NAME;
            tp_mkdir($filepath);
            $zipName = $filepath.DS.$code.'.zip';//定义打包后的包名
            if ($zip->open($zipName, \ZIPARCHIVE::OVERWRITE | \ZIPARCHIVE::CREATE) !== TRUE)
                $this->error('插件压缩包打开失败！');

            /*打包插件标准结构涉及的文件与目录，并且打包zip*/
            $filetxt = '';
            foreach ($packfiles as $key => $srcfile) {
                $filetxt .= $srcfile."\n\r";
                // $dstfile = DATA_NAME.DS.WEAPP_DIR_NAME.DS.$code.DS.$srcfile;
                // if(true == tp_mkdir(dirname($dstfile))) {
                    if(file_exists($srcfile)) {
                        // $copyrt = copy($srcfile, $dstfile); //复制文件
                        // if (!$copyrt) {
                        //     $this->error('copy ' . $dstfile . ' 失败');
                        //     exit;
                        // }
                        //addFile函数首个参数如果带有路径，则压缩的文件里包含的是带有路径的文件压缩
                        //若不希望带有路径，则需要该函数的第二个参数
                        $zip->addFile($srcfile);//第二个参数是放在压缩包中的文件名称，如果文件可能会有重复，就需要注意一下
                    }
                // }
            }
            // $dst_filelist = DATA_NAME.DS.WEAPP_DIR_NAME.DS.$code.DS.WEAPP_DIR_NAME.DS.$code.DS.'filelist.txt';
            $src_filelist = WEAPP_DIR_NAME.DS.$code.DS.'filelist.txt';
            @file_put_contents($src_filelist, $filetxt); //初始化插件文件列表  
            // copy($src_filelist, $dst_filelist);
            /*--end*/
            $zip->addFile($src_filelist);
            $zip->close(); 

            /*压缩插件目录*/
            if (!file_exists($zipName)) {
                $this->error('打包zip文件包失败！');
            }
            
            $this->success('打包成功', url('Weapp/pack'));

        }

        return $this->fetch();
    }

    /**
     * 压缩文件
     */
    private function zip($files = array(), $zipName){
        $zip = new \ZipArchive; //使用本类，linux需开启zlib，windows需取消php_zip.dll前的注释
        /*
         * 通过ZipArchive的对象处理zip文件
         * $zip->open这个方法如果对zip文件对象操作成功，$zip->open这个方法会返回TRUE
         * $zip->open这个方法第一个参数表示处理的zip文件名。
         * 这里重点说下第二个参数，它表示处理模式
         * ZipArchive::OVERWRITE 总是以一个新的压缩包开始，此模式下如果已经存在则会被覆盖。
         * ZIPARCHIVE::CREATE 如果不存在则创建一个zip压缩包，若存在系统就会往原来的zip文件里添加内容。
         *
         * 这里不得不说一个大坑。
         * 我的应用场景是需要每次都是创建一个新的压缩包，如果之前存在，则直接覆盖，不要追加
         * so，根据官方文档和参考其他代码，$zip->open的第二个参数我应该用 ZipArchive::OVERWRITE
         * 问题来了，当这个压缩包不存在的时候，会报错：ZipArchive::addFile(): Invalid or uninitialized Zip object
         * 也就是说，通过我的测试发现，ZipArchive::OVERWRITE 不会新建，只有当前存在这个压缩包的时候，它才有效
         * 所以我的解决方案是 $zip->open($zipName, \ZIPARCHIVE::OVERWRITE | \ZIPARCHIVE::CREATE)
         *
         * 以上总结基于我当前的运行环境来说
         * */
        if ($zip->open($zipName, \ZIPARCHIVE::OVERWRITE | \ZIPARCHIVE::CREATE) !== TRUE) {
            return '无法打开文件，或者文件创建失败';
        }
        foreach($files as $val){
            //$attachfile = $attachmentDir . $val['filepath']; //获取原始文件路径
            if(file_exists($val)){
                //addFile函数首个参数如果带有路径，则压缩的文件里包含的是带有路径的文件压缩
                //若不希望带有路径，则需要该函数的第二个参数
                $zip->addFile($val, basename($val));//第二个参数是放在压缩包中的文件名称，如果文件可能会有重复，就需要注意一下
            }
        }
        $zip->close();//关闭
 
        if(!file_exists($zipName)){
            return "无法找到文件"; //即使创建，仍有可能失败
        }
 
        //如果不要下载，下面这段删掉即可，如需返回压缩包下载链接，只需 return $zipName;
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename='.basename($zipName)); //文件名
        header("Content-Type: application/zip"); //zip格式的
        header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
        header('Content-Length: '. filesize($zipName)); //告诉浏览器，文件大小
        @readfile($zipName);
    }

    /**
     * 验证插件标识是否同名
     */
    public function ajax_check_code($code)
    {
        $service_ey = base64_decode(config('service_ey'));
        $url = "{$service_ey}/index.php?m=api&c=Weapp&a=checkIsCode&code={$code}";
        $response = httpRequest($url, "GET");
        if (1 == intval($response)) {
            $this->success('插件标识可使用！', url('Weapp/create'));
        } else if (-1 == intval($response)) {
            $this->error('插件标识已被占用！');
        }
        $this->error('远程验证插件标识失败！');
    }
}