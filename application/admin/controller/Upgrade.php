<?php
/**
 * eyoucms
 * ============================================================================
 * 版权所有 2016-2028 海口快推科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */

namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\response\Json;
use app\admin\model;
class Upgrade extends Controller {

    /**
     * 析构函数
     */
    function __construct() {
        parent::__construct();
        function_exists('set_time_limit') && set_time_limit(0);
        @ini_set('memory_limit','-1');
        // @ini_set('memory_limit', '1024M'); // 设置内存大小
        // @ini_set("max_execution_time", "0"); // 请求超时时间 0 为不限时
        // @ini_set('default_socket_timeout', 3600); // 设置 file_get_contents 请求超时时间 官方的说明，似乎没有不限时间的选项，也就是不能填0，你如果填0，那么socket就会立即返回失败，
        $this->assign('version', getCmsVersion());
    }

    public function welcome()
    {
        $sys_info['os']             = PHP_OS;
        $sys_info['zlib']           = function_exists('gzclose') ? 'YES' : 'NO';//zlib
        $sys_info['safe_mode']      = (boolean) ini_get('safe_mode') ? 'YES' : 'NO';//safe_mode = Off       
        $sys_info['timezone']       = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
        $sys_info['curl']           = function_exists('curl_init') ? 'YES' : 'NO';  
        $sys_info['web_server']     = $_SERVER['SERVER_SOFTWARE'];
        $sys_info['phpv']           = phpversion();
        $sys_info['ip']             = GetHostByName($_SERVER['SERVER_NAME']);
        $sys_info['fileupload']     = @ini_get('file_uploads') ? ini_get('upload_max_filesize') :'unknown';
        $sys_info['max_ex_time']    = @ini_get("max_execution_time").'s'; //脚本最大执行时间
        $sys_info['set_time_limit'] = function_exists("set_time_limit") ? true : false;
        $sys_info['domain']         = request()->host();
        $sys_info['memory_limit']   = ini_get('memory_limit');                                  
        $sys_info['version']        = file_get_contents(DATA_PATH.'conf/version.txt');
        $mysqlinfo = Db::query("SELECT VERSION() as version");
        $sys_info['mysql_version']  = $mysqlinfo[0]['version'];
        if(function_exists("gd_info")){
            $gd = gd_info();
            $sys_info['gdinfo']     = $gd['GD Version'];
        }else {
            $sys_info['gdinfo']     = "未知";
        }

        $globalTpCache = tpCache('global');

        $upgradeLogic = new \app\admin\logic\UpgradeLogic();
        $sys_info['curent_version'] = $upgradeLogic->curent_version; //当前程序版本
        $sys_info['web_name'] = !empty($globalTpCache['web_name']) ? $globalTpCache['web_name'] : '';
        $this->assign('sys_info', $sys_info);

        $upgradeMsg = $upgradeLogic->checkVersion(); //升级包消息     
        $this->assign('upgradeMsg',$upgradeMsg);

        if (isset($globalTpCache['web_show_popup_upgrade']) && 2 == $globalTpCache['web_show_popup_upgrade'] && $this->php_servicemeal <= 0) {
            $globalTpCache['web_show_popup_upgrade'] = -1;
        }
        $this->assign('web_show_popup_upgrade', $globalTpCache['web_show_popup_upgrade']);

        $this->assign('global', $globalTpCache);

        return $this->fetch();
    }

    /**
    * 一键升级
    */
    public function OneKeyUpgrade(){
        header('Content-Type:application/json; charset=utf-8');
        function_exists('set_time_limit') && set_time_limit(0);

        /*权限控制 by 小虎哥*/
        $admin_info = session('admin_info');
        if (0 < intval($admin_info['role_id'])) {
            $auth_role_info = $admin_info['auth_role_info'];
            if (!empty($auth_role_info) && intval($auth_role_info['online_update']) <= 0) {
                $this->error('您没有操作权限，请联系超级管理员分配权限');
            }
        }
        /*--end*/

        $curent_version = getCmsVersion();
        $upgradeLogic = new \app\admin\logic\UpgradeLogic();
        $data = $upgradeLogic->OneKeyUpgrade(); //升级包消息
        if (1 <= intval($data['code'])) {
            adminLog("系统在线升级：{$curent_version} -> ".getCmsVersion());
            $this->success($data['msg'], null, ['code'=>$data['code']]);
        } else {
            $code = 0;
            $msg = '升级异常，请排查问题！';
            if (is_array($data)) {
                isset($data['code']) && $code = $data['code'];
                isset($data['msg']) && $msg = $data['msg'];
            }
            $this->error($msg, null, ['code'=>$code]);
        }
    }

    /**
    * 设置弹窗更新-不再提醒
    */
    public function setPopupUpgrade()
    {
        $show_popup_upgrade = input('param.show_popup_upgrade/s', '1');
        $inc_type = 'web';
        tpCache($inc_type, array($inc_type.'_show_popup_upgrade'=>$show_popup_upgrade));
        respose(1);
    }

    /**
    * 检测目录权限、当前版本的数据库是否与官方一致
    */
    public function check_authority()
    {
        /*------------------检测目录读写权限----------------------*/
        $filelist = tpCache('system.system_upgrade_filelist');
        $filelist = base64_decode($filelist);
        $filelist = htmlspecialchars_decode($filelist);
        $filelist = explode('<br>', $filelist);
        $dirs = array();
        $i = -1;
        foreach($filelist as $filename)
        {
            $tfilename = $filename;
            $curdir = $this->GetDirName($tfilename);
            if (empty($curdir) || !file_exists($curdir)) {
                continue;
            }
            if( !isset($dirs[$curdir]) ) 
            {
                $dirs[$curdir] = $this->TestIsFileDir($curdir);
            }
            if($dirs[$curdir]['isdir'] == FALSE) 
            {
                continue;
            }
            else {
                @tp_mkdir($curdir, 0777);
                $dirs[$curdir] = $this->TestIsFileDir($curdir);
            }
            $i++;
        }

        $is_pass = true;
        $msg = '检测通过';
        if($i > -1)
        {
            $n = 0;
            $dirinfos = '';
            foreach($dirs as $curdir)
            {
                $dirinfos .= $curdir['name']."&nbsp;&nbsp;状态：";
                if ($curdir['writeable']) {
                    $dirinfos .= "[√正常]";
                } else {
                    $is_pass = false;
                    $n++;
                    $dirinfos .= "<font color='red'>[×不可写]</font>";
                }
                $dirinfos .= "<br />";
            }
            $title = "本次升级需要在下面文件夹写入更新文件，已检测站点有 <font color='red'>{$n}</font> 处没有写入权限：<br />";
            $title .= "<font color='red'>问题分析（如有问题，请咨询技术支持）：<br />";
            $title .= "1、检查站点目录的用户组与所有者，禁止是 root ;<br />";
            $title .= "2、检查站点目录的读写权限，一般权限值是 0755 ;<br />";
            $title .= "</font>涉及更新目录列表如下：<br />";
            $msg = $title . $dirinfos;
        }
        /*------------------end----------------------*/

        if (true == $is_pass) {
            /*------------------检测目录读写权限----------------------*/
            $values = [
                'version' => getCmsVersion(),
            ];
            $upgradeLogic = new \app\admin\logic\UpgradeLogic;
            $upgradeLogic->GetKeyData($values);
            $url = $upgradeLogic->getServiceUrl().'/index.php?m=api&c=Service&a=get_database_txt';
            $response = @httpRequest($url, 'POST', $values, [], 5);
            if (false === $response) {
                $url = $url.'&'.http_build_query($values);
                $context = stream_context_set_default(array('http' => array('timeout' => 5,'method'=>'GET')));
                $response = @file_get_contents($url, false, $context);
            }
            $params = json_decode($response,true);
            if (false == $params) {
                $this->error('连接升级服务器超时，请刷新重试！', null, ['code'=>2]);
            }

            if (is_array($params)) {
                if (1 == intval($params['code'])) {
                    /*------------------组合本地数据库信息----------------------*/
                    $dbtables = Db::query('SHOW TABLE STATUS');
                    $local_database = array();
                    foreach ($dbtables as $k => $v) {
                        $table = $v['Name'];
                        if (preg_match('/^'.PREFIX.'/i', $table)) {
                            $local_database[$table] = [
                                'name'  => $table,
                                'field' => [],
                            ];
                        }
                    }
                    /*------------------end----------------------*/

                    /*------------------组合官方远程数据库信息----------------------*/
                    $info = $params['info'];
                    $info = preg_replace("#[\r\n]{1,}#", "\n", $info);
                    $infos = explode("\n", $info);
                    $infolists = [];
                    foreach ($infos as $key => $val) {
                        if (!empty($val)) {
                            $arr = explode('|', $val);
                            $infolists[$arr[0]] = $val;
                        }
                    }
                    /*------------------end----------------------*/

                    /*------------------校验数据库是否合格----------------------*/
                    foreach ([1] as $testk => $testv) {
                        // 对比数据表数量
                        if (count($local_database) < count($infolists)) {
                            $is_pass = false;
                            break;
                        }

                        // 对比数据表字段数量
                        foreach ($infolists as $k1 => $v1) {
                            $arr1 = explode('|', $v1);
                            
                            if (1 >= count($arr1)) {
                                continue; // 忽略不对比的数据表
                            }

                            $fieldArr = explode(',', $arr1[1]);
                            $table = preg_replace('/^ey_/i', PREFIX, $arr1[0]);
                            $local_fields = Db::getFields($table); // 本地数据表字段列表
                            $local_database[$table]['field'] = $local_fields;
                            if (count($local_fields) < count($fieldArr)) {
                                $is_pass = false;
                                break;
                            }
                        }
                        if (false == $is_pass) break;
                    }
                    /*------------------end----------------------*/
                } else if (2 == intval($params['code'])) {
                    $this->error('官方缺少版本号'.getCmsVersion().'的数据库比较文件！', null, ['code'=>2]);
                }
            }

            if (true == $is_pass) {
                $this->success($msg);
            } else {
                $this->error('当前数据库结构与官方不一致，请查看官方解决教程！', null, ['code'=>2]);
            }
            /*------------------end----------------------*/
        } else {
            $this->error($msg, null, ['code'=>1]);
        }
    }

    /**
     * 获取文件的目录路径
     * @param string $filename 文件路径+文件名
     * @return string
     */
    private function GetDirName($filename)
    {
        $dirname = preg_replace("#[\\\\\/]{1,}#", '/', $filename);
        $dirname = preg_replace("#([^\/]*)$#", '', $dirname);
        $dirname = preg_replace('/^data\/backup\/tpl\//i', '', $dirname);
        return $dirname;
    }

    /**
     * 测试目录路径是否有读写权限
     * @param string $dirname 文件目录路径
     * @return array
     */
    private function TestIsFileDir($dirname)
    {
        $dirs = array('name'=>'', 'isdir'=>FALSE, 'writeable'=>FALSE);
        $dirs['name'] =  $dirname;
        tp_mkdir($dirname);
        if(is_dir($dirname))
        {
            $dirs['isdir'] = TRUE;
            $dirs['writeable'] = $this->TestWriteAble($dirname);
        }
        return $dirs;
    }

    /**
     * 测试目录路径是否有写入权限
     * @param string $d 目录路劲
     * @return boolean
     */
    private function TestWriteAble($d)
    {
        $tfile = '_eyout.txt';
        $fp = @fopen($d.$tfile,'w');
        if(!$fp) {
            if (@is_writable($d)) {
                return true;
            }
            return false;
        }
        else {
            fclose($fp);
            $rs = @unlink($d.$tfile);
            return true;
        }
    }
}