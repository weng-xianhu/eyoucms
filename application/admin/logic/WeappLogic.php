<?php
/**
 * eyoucms
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
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
 
class WeappLogic extends Model
{
    public $root_path;
    public $weapp_path;
    public $data_path;
    // public $config_path;
    // public $curent_version;    
    public $service_url;
    // public $upgrade_url;
    public $service_ey;
    // public $code;
    
    /**
     * 析构函数
     */
    function  __construct() {
        // $this->code = input('param.code/s', '');
        // $this->curent_version = getWeappVersion($this->code);
        $this->service_ey = config('service_ey');
        $this->root_path = ROOT_PATH; // 
        $this->weapp_path = WEAPP_DIR_NAME.DS; // 
        $this->data_path = DATA_PATH; // 
        // $this->config_path = $this->weapp_path.$this->code.DS.'config.php'; // 版本配置文件路径
        // api_Weapp_checkVersion
        $tmp_str = 'L2luZGV4LnBocD9tPWFwaSZjPVdlYXBwJmE9Y2hlY2tWZXJzaW9u';
        $this->service_url = base64_decode($this->service_ey).base64_decode($tmp_str).'&domain='.request()->host(true);
        // $this->upgrade_url = $this->service_url.'&code='.$this->code.'&v='.$this->curent_version;
    }

    /**
     * 更新插件到数据库
     * @param $weapp_list array 本地插件数组
     */
    public function insertWeapp(){
        $row = M('weapp')->field('id,code,config')->getAllWithIndex('code'); // 数据库
        $new_arr = array(); // 本地
        $addData = array(); // 数据存储变量
        $updateData = array(); // 数据存储变量
        $weapp_list = $this->scanWeapp();
        //  本地对比数据库
        foreach($weapp_list as $k=>$v){
            $code = isset($v['code']) ? $v['code'] : 'error_'.date('Ymd');
            /*初步过滤不规范插件*/
            if ($k != $code) {
                continue;
            }
            /*--end*/
            $new_arr[] = $code;
            // 对比数据库 本地有 数据库没有
            $data = array(
                'code'          => $code,
                'name'          => isset($v['name']) ? $v['name'] : '配置信息不完善',
                'config'        => empty($v) ? '' : json_encode($v),
                'position'      => isset($v['position']) ? $v['position'] : 'default',
                'sort_order'    => 100,
            );
            if(empty($row[$code])){ // 新增插件
                $data['add_time'] = getTime();
                $addData[] = $data;
            } else { // 更新插件
                if ($row[$code]['config'] != json_encode($v)) {
                    $data['id'] = $row[$code]['id'];
                    $data['update_time'] = getTime();
                    $updateData[] = $data;
                }
            }
        }
        if (!empty($addData)) {
            model('weapp')->saveAll($addData);
        }
        if (!empty($updateData)) {
            model('weapp')->saveAll($updateData);
            // \think\Cache::clear('hook');
        }
        //数据库有 本地没有
        foreach($row as $k => $v){
            if (!in_array($v['code'], $new_arr)) {
                M('weapp')->where($v)->delete();
            }
        }
    }

    /**
     * 插件目录扫描
     * @return array 返回目录数组
     */
    private function scanWeapp(){
        $dir = WEAPP_DIR_NAME;
        $weapp_list = $this->dirscan($dir);
        foreach($weapp_list as $k=>$v){
            if (!is_dir(WEAPP_DIR_NAME.DS.$v) || !file_exists(WEAPP_DIR_NAME.DS.$v.'/config.php')) {
                unset($weapp_list[$k]);
            }
            else
            {
                $weapp_list[$v] = include(WEAPP_DIR_NAME.DS.$v.'/config.php');
                unset($weapp_list[$k]);                    
            }
        }
        return $weapp_list;
    }

    /**
     * 获取插件目录列表
     * @param $dir
     * @return array
     */
    private function dirscan($dir){
        $dirArray = array();
        if (false != ($handle = opendir($dir))) {
            $i = 0;
            while ( false !== ($file = readdir ($handle)) ) {
                //去掉"“.”、“..”以及带“.xxx”后缀的文件
                if ($file != "." && $file != ".." && !strpos($file,".")) {
                    $dirArray[$i] = $file;
                    $i++;
                }
            }
            //关闭句柄
            closedir($handle);
        }
        return $dirArray;
    }

    /**
     * 插件基类构造方法
     * sm：module        插件模块
     * sc：controller    插件控制器
     * sa：action        插件操作
     */
    public function checkInstall()
    {
        $msg = true;
        if(!array_key_exists("sm", request()->param())){
            $msg = '无效插件URL！';
        } else {
            $module = request()->param('sm');
            $module = $module ?: request()->param('sc');
            $row = M('Weapp')->field('code, name, status')
                ->where(array('code'=>$module))
                ->find();
            if (empty($row)) {
                $msg = "插件【{$row['name']}】不存在";
            } else {
                if ($row['status'] == -1) {
                    $msg = "请先启用插件【{$row['name']}】";
                } else if (intval($row['status']) == 0) {
                    $msg = "请先安装插件【{$row['name']}】";
                }
            }
        }

        return $msg;
    }

    /**
     * 检查是否有更新包
     * @return type 提示语
     */
    public function checkVersion($code, $serviceVersionList = false) {
        error_reporting(0);//关闭所有错误报告
        $lastupgrade = array();
        if (false === $serviceVersionList) {
            $curent_version = getWeappVersion($code);
            $url = $this->service_url.'&code='.$code.'&v='.$curent_version;
            $context = stream_context_set_default(array('http' => array('timeout' => 3,'method'=>'GET')));
            $serviceVersionList = @file_get_contents($url,false,$context);    
            $serviceVersionList = json_decode($serviceVersionList,true);
        }
        if(!empty($serviceVersionList))
        {
            $upgradeArr = array();
            $introStr = '';
            $upgradeStr = '';
            foreach ($serviceVersionList as $key => $val) {
                $upgrade = !empty($val['upgrade']) ? $val['upgrade'] : array();
                $upgradeArr = array_merge($upgradeArr, $upgrade);
                $introStr .= '<br/>'.filter_line_return($val['intro'], '<br/>');
            }
            $upgradeArr = array_unique($upgradeArr);
            $upgradeStr = implode('<br/>', $upgradeArr); // 升级提示需要覆盖哪些文件

            $introArr = explode('<br/>', $introStr);
            $introStr = '更新日志：';
            foreach ($introArr as $key => $val) {
                if (empty($val)) {
                    continue;
                }
                $introStr .= "<br/>{$key}、".$val;
            }

            $lastupgrade = $serviceVersionList[count($serviceVersionList) - 1];
            if (!empty($lastupgrade['upgrade_title'])) {
                $introStr .= '<br/>'.$lastupgrade['upgrade_title'];
            }
            $lastupgrade['intro'] = htmlspecialchars_decode($introStr);
            $lastupgrade['upgrade'] = htmlspecialchars_decode($upgradeStr); // 升级提示需要覆盖哪些文件
            /*升级公告*/
            if (!empty($lastupgrade['notice'])) {
                $lastupgrade['notice'] = htmlspecialchars_decode($lastupgrade['notice']) . '<br>';
            }
            /*--end*/

            return ['code' => 2, 'msg' => $lastupgrade];
        }
        return ['code' => 1, 'msg' => '已是最新版'];
    }

    /**
     * 批量检查是否有更新包
     * @return type 提示语
     */
    public function checkBatchVersion($upgradeArr) 
    {
        $result = array();
        if (is_array($upgradeArr) && !empty($upgradeArr)) {
            foreach ($upgradeArr as $key => $upgrade) {
                $result[$key] = $this->checkVersion($key, $upgrade);
            }
        }
        return $result;
    }

    /**
     * 一键更新
     */
    public function OneKeyUpgrade($code){
        error_reporting(0);//关闭所有错误报告
        if (empty($code)) {
            return ['code' => 0, 'msg' => "URL传参错误，缺少插件标识参数值！"];
        }

        $allow_url_fopen = ini_get('allow_url_fopen');
        if (!$allow_url_fopen) {
            return ['code' => 0, 'msg' => "请联系空间商，设置 php.ini 中参数 allow_url_fopen = 1"];
        }     
               
        if (!extension_loaded('zip')) {
            return ['code' => 0, 'msg' => "请联系空间商，开启 php.ini 中的php-zip扩展"];
        }

        $curent_version = getWeappVersion($code);
        $upgrade_url = $this->service_url.'&code='.$code.'&v='.$curent_version;
        $serviceVersionList = file_get_contents($upgrade_url);
        $serviceVersionList = json_decode($serviceVersionList,true);
        if (empty($serviceVersionList)) {
            return ['code' => 0, 'msg' => "没找到升级包"];
        }
        
        clearstatcache(); // 清除文件夹权限缓存
        $config_path = $this->weapp_path.$code.DS.'config.php'; // 版本配置文件路径
        if(!is_writeable($config_path)) {
            return ['code' => 0, 'msg' => '文件'.$config_path.' 不可写，不能升级!!!'];
        }
        /*最新更新版本信息*/
        $lastServiceVersion = $serviceVersionList[count($serviceVersionList) - 1];
        /*--end*/
        /*批量下载更新包*/
        $upgradeArr = array(); // 更新的文件列表
        $sqlfileArr = array(); // 更新SQL文件列表
        $folderName = $code.'-'.$lastServiceVersion['key_num'];
        foreach ($serviceVersionList as $key => $val) {
            // 下载更新包
            $result = $this->downloadFile($val['down_url'], $val['file_md5']);
            if (!isset($result['code']) || $result['code'] != 1) {
                return $result;
            }

            /*第一个循环执行的业务*/
            if ($key == 0) {
                /*解压到最后一个更新包的文件夹*/
                $lastDownFileName = explode('/', $lastServiceVersion['down_url']);    
                $lastDownFileName = end($lastDownFileName);
                $folderName = $code.'-'.str_replace(".zip", "", $lastDownFileName);  // 文件夹
                /*--end*/

                /*解压之前，删除已重复的文件夹*/
                delFile($this->data_path.'backup'.DS.$folderName);
                /*--end*/
            }
            /*--end*/

            $downFileName = explode('/', $val['down_url']);    
            $downFileName = end($downFileName);

            /*解压文件*/
            $zip = new \ZipArchive();//新建一个ZipArchive的对象
            if($zip->open($this->data_path.'backup'.DS.$downFileName) != true) {
                return ['code' => 0, 'msg' => "升级包读取失败!"];
            }
            $zip->extractTo($this->data_path.'backup'.DS.$folderName.DS);//假设解压缩到在当前路径下backup文件夹内
            $zip->close();//关闭处理的zip文件
            /*--end*/

            if(!file_exists($this->data_path.'backup'.DS.$folderName.DS.'www'.DS.'weapp'.DS.$code.DS.'config.php')) {
                return ['code' => 0, 'msg' => $code."插件目录缺少config.php文件,请联系客服"];
            }

            /*更新的文件列表*/
            $upgrade = !empty($val['upgrade']) ? $val['upgrade'] : array();
            $upgradeArr = array_merge($upgradeArr, $upgrade);
            /*--end*/

            /*更新的SQL文件列表*/
            $sql_file = !empty($val['sql_file']) ? $val['sql_file'] : array();
            $sqlfileArr = array_merge($sqlfileArr, $val['sql_file']);
            /*--end*/
        }
        /*--end*/

        /*将多个更新包重新组建一个新的完全更新包*/
        $upgradeArr = array_unique($upgradeArr); // 移除文件列表里重复的文件
        $sqlfileArr = array_unique($sqlfileArr); // 移除文件列表里重复的文件
        $serviceVersion = $lastServiceVersion;
        $serviceVersion['upgrade'] = $upgradeArr;
        $serviceVersion['sql_file'] = $sqlfileArr;
        /*--end*/

        /*升级之前，备份涉及的源文件*/
        $upgrade = $serviceVersion['upgrade'];
        if (!empty($upgrade) && is_array($upgrade)) {
            foreach ($upgrade as $key => $val) {
                $source_file = $this->root_path.$val;
                if (file_exists($source_file)) {
                    $destination_file = $this->data_path.'backup'.DS.$code.'-'.$curent_version.'_www'.DS.$val;
                    tp_mkdir(dirname($destination_file));
                    $copy_bool = @copy($source_file, $destination_file);
                    if (false == $copy_bool) {
                        return ['code' => 0, 'msg' => "更新前备份文件失败，请检查所有目录是否有读写权限"];
                    }
                }
            }
        }
        /*--end*/

        /*升级的 sql文件*/
        if(!empty($serviceVersion['sql_file']))
        {
            foreach($serviceVersion['sql_file'] as $key => $val)
            {
                //读取数据文件
                $sqlpath = $this->data_path.'backup'.DS.$folderName.DS.'sql'.DS.trim($val);
                $execute_sql = file_get_contents($sqlpath);
                $sqlFormat = $this->sql_split($execute_sql, PREFIX);
                /**
                 * 执行SQL语句
                 */
                try {
                    $counts = count($sqlFormat);

                    for ($i = 0; $i < $counts; $i++) {
                        $sql = trim($sqlFormat[$i]);

                        if (stristr($sql, 'CREATE TABLE')) {
                            Db::execute($sql);
                        } else {
                            if(trim($sql) == '')
                               continue;
                            Db::execute($sql);
                        }
                    }
                } catch (\Exception $e) {
                    return ['code' => 0, 'msg' => "数据库执行中途失败，请第一时间请求技术支持，否则将影响该插件后续的版本升级！"];
                }
            }
        }
        /*--end*/

        // 递归复制文件夹
        $copy_data = $this->recurse_copy($this->data_path.'backup'.DS.$folderName.DS.'www', rtrim($this->root_path, DS), $folderName);

        // 清空缓存
        delFile(rtrim(RUNTIME_PATH, '/'));
        tpCache('global');

        /*删除下载的升级包*/
        $ziplist = glob($this->data_path.'backup'.DS.'*.zip');
        @array_map('unlink', $ziplist);
        /*--end*/

        // 推送回服务器  记录升级成功
        $this->UpgradeLog($code, $serviceVersion['key_num']);
        
        return ['code' => $copy_data['code'], 'msg' => "升级成功{$copy_data['msg']}"];
    }

    /**
     * 自定义函数递归的复制带有多级子目录的目录
     * 递归复制文件夹
     *
     * @param string $src 原目录
     * @param string $dst 复制到的目录
     * @param string $folderName 存放升级包目录名称
     * @return string
     */                        
    //参数说明：            
    //自定义函数递归的复制带有多级子目录的目录
    private function recurse_copy($src, $dst, $folderName)
    {
        static $badcp = 0; // 累计覆盖失败的文件总数
        static $n = 0; // 累计执行覆盖的文件总数
        static $total = 0; // 累计更新的文件总数
        $dir = opendir($src);
        tp_mkdir($dst);
        while (false !== $file = readdir($dir)) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurse_copy($src . '/' . $file, $dst . '/' . $file, $folderName);
                }
                else {
                    if (file_exists($src . DIRECTORY_SEPARATOR . $file)) {
                        $rs = @copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                        if($rs) {
                            $n++;
                            @unlink($src . DIRECTORY_SEPARATOR . $file);
                        } else {
                            $n++;
                            $badcp++;
                        }
                    } else {
                        $n++;
                    }
                    $total++;
                }
            }
        }
        closedir($dir);

        $code = 1;
        $msg = '！';
        if($badcp > 0)
        {
            $code = 2;
            $msg = "，其中失败 <font color='red'>{$badcp}</font> 个文件，<br />请从升级包目录[<font color='red'>data/backup/{$folderName}/www</font>]中的取出全部文件覆盖到根目录，完成手工升级。";
        }

        $this->copy_speed($n, $total);

        return ['code'=>$code, 'msg'=>$msg];
    }

    /**
     * 复制文件进度
     */
    private function copy_speed($n, $total)
    {
        $data = false;

        if ($n < $total) {
            $this->copy_speed($n, $total);
        } else {
            $data = true;
        }
        
        return $data;
    }

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
            $num++;
        }
        return $ret;
    }
 
    /**     
     * @param type $fileUrl 下载文件地址
     * @param type $md5File 文件MD5 加密值 用于对比下载是否完整
     * @return string 错误或成功提示
     */
    private function downloadFile($fileUrl,$md5File)
    {
        $downFileName = explode('/', $fileUrl);    
        $downFileName = end($downFileName);
        $saveDir = $this->data_path.'backup'.DS.$downFileName; // 保存目录
        tp_mkdir(dirname($saveDir));
        if(!file_get_contents($fileUrl, 0, null, 0, 1)){
            return ['code' => 0, 'msg' => '官方插件升级包不存在']; // 文件存在直接退出
        }
        $ch = curl_init($fileUrl);            
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $file = curl_exec ($ch);
        curl_close ($ch);                                                            
        $fp = fopen($saveDir,'w');
        fwrite($fp, $file);
        fclose($fp);
        if(!eyPreventShell($saveDir) || !file_exists($saveDir) || $md5File != md5_file($saveDir))
        {
            return ['code' => 0, 'msg' => '下载保存升级包失败，请检查所有目录的权限以及用户组不能为root'];
        }
        return ['code' => 1, 'msg' => '下载成功'];
    }            
    
    // 升级记录 log 日志
    private  function UpgradeLog($code, $to_key_num){
        $serial_number = DEFAULT_SERIALNUMBER;

        $constsant_path = APP_PATH.MODULE_NAME.'/conf/constant.php';
        if (file_exists($constsant_path)) {
            require_once($constsant_path);
            defined('SERIALNUMBER') && $serial_number = SERIALNUMBER;
        }
        $mysqlinfo = \think\Db::query("SELECT VERSION() as version");
        $mysql_version  = $mysqlinfo[0]['version'];
        $vaules = array(
            'domain'=>$_SERVER['HTTP_HOST'], //用户域名  
            'code'  => $code, // 插件标识
            'key_num'=>getWeappVersion($code), // 用户版本号
            'to_key_num'=>$to_key_num, // 用户要升级的版本号                
            'add_time'=>time(), // 升级时间
            'serial_number'=>$serial_number,
            'ip'    => GetHostByName($_SERVER['SERVER_NAME']),
            'phpv'  => phpversion(),
            'mysql_version' => $mysql_version,
            'web_server'    => $_SERVER['SERVER_SOFTWARE'],
        );
        // api_Weapp_upgradeLog
        $tmp_str = 'L2luZGV4LnBocD9tPWFwaSZjPVdlYXBwJmE9dXBncmFkZUxvZyY=';
        $url = base64_decode($this->service_ey).base64_decode($tmp_str).http_build_query($vaules);
        file_get_contents($url);
    }
} 
?>