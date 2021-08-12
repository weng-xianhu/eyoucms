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
use app\user\model\Pay as PayModel;

/**
 * 插件控制器
 */
class Weapp extends Base
{
    public $weappM;
    public $weappLogic;
    public $plugins = array();
    public $admin_info = array();
    public $service_ey = '';

    /*
     * 前置操作
     */
    protected $beforeActionList = array(
        'init'
    );


    /*
     * 初始化操作
     */
    public function _initialize()
    {
        parent::_initialize();

        $web_weapp_switch = tpCache('web.web_weapp_switch');
        if (1 != $web_weapp_switch) {
            $this->error('插件功能没有开启！');
        }

        $this->weappM     = model('Weapp');
        $this->weappLogic = new WeappLogic();
        //  更新插件
        $this->weappLogic->insertWeapp();

        // 管理员信息
        $this->admin_info = session('admin_info');

        $this->service_ey = base64_decode(config('service_ey'));
    }

    public function init()
    {
        /*权限控制 by 小虎哥*/
        if (0 < intval(session('admin_info.role_id'))) {
            $auth_role_info = session('admin_info.auth_role_info');
            if (!empty($auth_role_info)) {
                if (!empty($auth_role_info['permission']['plugins'])) {
                    foreach ($auth_role_info['permission']['plugins'] as $plugins) {
                        if (isset($plugins['code'])) {
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
        /*云部分*/
        $assign_data = array();
        $condition   = array();
        // 获取到所有GET参数
        $get = input('get.');

        // 应用搜索条件
        foreach (['keywords'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                if ($key == 'keywords') {
                    $condition['a.name|code'] = array('LIKE', "%{$get[$key]}%");
                } else {
                    $condition['a.' . $key] = array('eq', $get[$key]);
                }
            }
        }

        /*权限控制 by 小虎哥*/
        if (!empty($this->plugins)) {
            $condition['a.code'] = array('in', $this->plugins);
        }
        /*--end*/
        $condition['a.is_buy'] =['=',0];
        $weappArr = array(); // 插件标识数组

        /**
         * 数据查询，搜索出主键ID的值
         */
        $count = DB::name('weapp')->alias('a')->where($condition)->count();// 查询满足要求的总记录数
        $Page  = new Page($count, 15);// 实例化分页类 传入总记录数和每页显示的记录数
        $list  = DB::name('weapp')
            ->field('a.*')
            ->alias('a')
            ->where($condition)
            ->order('a.sort_order asc, a.id desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->getAllWithIndex('id');
        foreach ($list as $key => $val) {
            if ($val['is_buy'] == 0) {
                $config                = include WEAPP_PATH . $val['code'] . DS . 'config.php';
                $config['description'] = filter_line_return($config['description'], '<br/>');
                $val['version']        = getWeappVersion($val['code']);
            }else if ($val['is_buy'] == 1){
                $config = json_decode($val['config'],true);
            }
            $config['litpic']      = !empty($config['litpic']) ? get_default_pic($config['litpic']) : get_default_pic();
            $val['config']         = $config;

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
                'code'    => $val['code'],
                'version' => $val['version'],
            );
            /*--end*/
        }
        $show                 = $Page->show(); // 分页显示输出
        $assign_data['page']  = $show; // 赋值分页输出
        $assign_data['list']  = $list; // 赋值数据集
        $assign_data['pager'] = $Page; // 赋值分页对象

        $RenewList = []; // 续费插件列表

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
            $vaules        = array(
                'domain' => request()->host(true),
                'ip'    => serverIP(),
                'code'   => $codeStr,
                'v'      => $versionStr,
                'pid'   => $this->php_servicemeal,
                // 'dev'   => 1,
            );
            tpCache('system', ['system_usecodelist'=>'']);
            $tmp_str       = 'L2luZGV4LnBocD9tPWFwaSZjPVdlYXBwJmE9Y2hlY2tCYXRjaFZlcnNpb24m';
            $service_url   = base64_decode(config('service_ey')) . base64_decode($tmp_str);
            $url           = $service_url . http_build_query($vaules);
            $context       = stream_context_set_default(array('http' => array('timeout' => 3, 'method' => 'GET')));
            $response      = @file_get_contents($url, false, $context);
            $batch_upgrade = json_decode($response, true);

            if (is_array($batch_upgrade)) {
                if (!empty($batch_upgrade['RenewList20210311'])) {
                    $RenewList = $batch_upgrade['RenewList20210311'];
                    unset($batch_upgrade['RenewList20210311']);
                }
                if (!empty($batch_upgrade)) {
                    $weapp_upgrade = $this->weappLogic->checkBatchVersion($batch_upgrade); //升级包消息 
                }
            }
        }
        $assign_data['weapp_upgrade'] = $weapp_upgrade;
        /*--end*/

        $assign_data['weapp_plugin_open'] = tpCache('php.php_weapp_plugin_open');
        $assign_data['RenewList'] = $RenewList;

        $this->assign($assign_data);
        return $this->fetch();
    }

    /*
     * 已购买插件列表
     */
    public function mybuy()
    {
        /*云部分*/
        Db::name('weapp')->where('is_buy',1)->delete();
        $post_data = [
            'pid'   => $this->php_servicemeal,
            'domain'=>$this->request->host(true),
        ];
        $url       = 'http://plugins.eyoucms.com/user/ajax_memberplugin.php?action=myplugin';
        $response  = httpRequest2($url, 'POST', $post_data);
        $params    = json_decode($response, true);
        if (empty($params['code'])) {
            $msg = !empty($params['msg']) ? $params['msg'] : '连接远程插件接口失败！';
            $this->error($msg);
        }

        if (!empty($params['code']) && 1 == $params['code']) {
            //云购买插件写入数据库
            $local_weapp = Db::name('weapp')->getAllWithIndex('code');
            foreach ($params['plugin'] as $key => $val) {
                if (empty($local_weapp[$val['weapp_code']])) {
                    if (preg_match('/^\d+\.\d+\.\d+([0-9\.]*)$/', $val['version'])) {
                        $val['version'] = 'v'.$val['version'];
                    }
                    $config = [
                        'code' => $val['weapp_code'], // 插件标识
                        'name' => $val['pname'], // 插件名称
                        'version' => $val['version'],
                        'min_version' => $val['min_version'], // CMS最低版本支持
                        'author' => '匿名', // 开发者
                        'litpic'    => $val['litpic'],
                        'description' => $val['description'],
                        'scene' => '0',  // 使用场景 0 PC+手机 1 手机 2 PC
                        'permission' => array(),
                    ];
                    $saveData[] = [
                        'code'      => $val['weapp_code'],
                        'name'      => $val['pname'],
                        'config'    => json_encode($config),
                        'is_buy'    => 1,
                        'add_time'  => getTime(),
                        'update_time'  => getTime(),
                    ];
                }
            }
            model('Weapp')->saveAll($saveData);
        }
        /*云部分*/

        $assign_data = array();
        $condition   = array();
        // 获取到所有GET参数
        $get = input('get.');

        // 应用搜索条件
        foreach (['keywords'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                if ($key == 'keywords') {
                    $condition['a.name|code'] = array('LIKE', "%{$get[$key]}%");
                } else {
                    $condition['a.' . $key] = array('eq', $get[$key]);
                }
            }
        }

        $codeList = [];
        if (!empty($params['plugin']) && is_array($params['plugin'])) {
            $codeList = get_arr_column($params['plugin'], 'weapp_code');
        }
        /*权限控制 by 小虎哥*/
        if (!empty($this->plugins)) {
            $codeList = array_merge($codeList, $this->plugins);
        }
        /*--end*/
        $condition['a.code'] = array('in', $codeList);
        $condition['a.is_buy'] = array('<', 2);

        $weappArr = array(); // 插件标识数组

        /**
         * 数据查询，搜索出主键ID的值
         */
        $count = DB::name('weapp')->alias('a')->where($condition)->count();// 查询满足要求的总记录数
        $Page  = new Page($count, 15);// 实例化分页类 传入总记录数和每页显示的记录数
        $list  = DB::name('weapp')
            ->field('a.*')
            ->alias('a')
            ->where($condition)
            ->order('a.sort_order asc, a.id desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->getAllWithIndex('id');
        foreach ($list as $key => $val) {
            if ($val['is_buy'] == 0) {
                $config                = include WEAPP_PATH . $val['code'] . DS . 'config.php';
                $config['description'] = !empty($config['description']) ? filter_line_return($config['description'], '<br/>') : '';
                $val['version']        = getWeappVersion($val['code']);
            }else if ($val['is_buy'] == 1){
                $config = json_decode($val['config'],true);
                $config['description'] = !empty($config['description']) ? filter_line_return($config['description'], '<br/>') : '';
                $val['version']        = !empty($config['version']) ? $config['version'] : 'v1.0.0';
            }
            $config['litpic']      = !empty($config['litpic']) ? get_default_pic($config['litpic']) : get_default_pic();
            $val['config']         = $config;

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
                'code'    => $val['code'],
                'version' => $val['version'],
            );
            /*--end*/
        }
        $show                 = $Page->show(); // 分页显示输出
        $assign_data['page']  = $show; // 赋值分页输出
        $assign_data['list']  = $list; // 赋值数据集
        $assign_data['pager'] = $Page; // 赋值分页对象

        $assign_data['weapp_plugin_open'] = tpCache('php.php_weapp_plugin_open');

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
        $actionName     = !empty($sa) ? $sa : "index";
        $class_path     = "\\" . WEAPP_DIR_NAME . "\\" . $sm . "\\controller\\" . $controllerName;
        $controller     = new $class_path();
        $result         = $controller->$actionName();

        \think\Cache::clear('weapp');
        
        return $result;
    }

    /**
     * 安装插件
     */
    public function install($id)
    {
        $row           = Db::name('Weapp')->field('name,code,thorough,config')->find($id);
        $row['config'] = json_decode($row['config'], true);
        $class         = get_weapp_class($row['code']);
        if (!class_exists($class)) {
            $this->error('插件不存在！');
        }
        $weapp = new $class;
        if (!$weapp->checkConfig()) {//检测信息的正确性
            $this->error('插件config配置参数不全！');
        }
        $cms_version = getCmsVersion();
        $min_version = $row['config']['min_version'];
        if ($cms_version < $min_version) {
            $this->error('当前CMS版本太低，该插件要求CMS版本 >= ' . $min_version . '，请升级系统！');
        }
        /*插件安装的前置操作（可无）*/
        $this->beforeInstall($weapp);
        /*--end*/

        if (true) {
            /*插件sql文件*/
            $sqlfile = WEAPP_DIR_NAME . DS . $row['code'] . DS . 'data' . DS . 'install.sql';
            if (empty($row['thorough']) && file_exists($sqlfile)) {
                $execute_sql = file_get_contents($sqlfile);
                $sqlFormat   = $this->sql_split($execute_sql, PREFIX, $row['code']);
                /**
                 * 执行SQL语句
                 */
                $counts = count($sqlFormat);

                for ($i = 0; $i < $counts; $i++) {
                    $sql = trim($sqlFormat[$i]);

                    if (strstr($sql, 'CREATE TABLE')) {
                        Db::execute($sql);
                    } else {
                        if (trim($sql) == '')
                            continue;
                        Db::execute($sql);
                    }
                }
            }
            /*--end*/
            $r = Db::name('weapp')->where('id', $id)->update(array('thorough' => 1, 'status' => 1, 'add_time' => getTime()));
            if ($r) {
                cache('hooks', null);
                cache("hookexec_" . $row['code'], null);
                \think\Cache::clear('hooks');
                \think\Cache::clear('weapp');
                /*插件安装的后置操作（可无）*/
                $this->afterInstall($weapp);
                /*--end*/
                adminLog('安装插件：' . $row['name']);
                $this->success('安装成功', url('Weapp/index'));
                exit;
            }
        }

        $this->error('安装失败');
    }

    /**
     * 卸载插件
     */
    public function uninstall()
    {
        $id       = input('param.id/d', 0);
        $thorough = input('param.thorough/d', 0);
        $row      = Db::name('Weapp')->field('name,code')->find($id);
        $class    = get_weapp_class($row['code']);
        if (!class_exists($class)) {
            $this->error('插件不存在！');
        }
        $weapp = new $class;

        // 插件卸载的前置操作（可无）
        $this->beforeUninstall($weapp);
        /*--end*/

        if (true) {
            $is_uninstall = false;
            if (1 == $thorough) {
                $r = Db::name('weapp')->where('id', $id)->update(array('thorough' => $thorough, 'status' => 0, 'add_time' => getTime()));
            } else if (0 == $thorough) {
                $r = Db::name('weapp')->where('id', $id)->update(array('thorough' => $thorough, 'status' => 0, 'update_time' => getTime()));
                $r && $is_uninstall = true;
            }
            if (false !== $r) {
                /*插件sql文件，不执行删除插件数据表*/
                $sqlfile = WEAPP_DIR_NAME . DS . $row['code'] . DS . 'data' . DS . 'uninstall.sql';
                if (empty($thorough) && file_exists($sqlfile)) {
                    $execute_sql = file_get_contents($sqlfile);
                    $sqlFormat   = $this->sql_split($execute_sql, PREFIX, $row['code']);
                    /**
                     * 执行SQL语句
                     */
                    $counts = count($sqlFormat);

                    for ($i = 0; $i < $counts; $i++) {
                        $sql = trim($sqlFormat[$i]);

                        if (strstr($sql, 'CREATE TABLE')) {
                            Db::execute($sql);
                        } else {
                            if (trim($sql) == '')
                                continue;
                            Db::execute($sql);
                        }
                    }
                }
                /*--end*/

                cache('hooks', null);
                cache("hookexec_" . $row['code'], null);
                \think\Cache::clear('hooks');
                \think\Cache::clear('weapp');
                /*插件卸载的后置操作（可无）*/
                $this->afterUninstall($weapp);
                /*--end*/

                // 删除插件相关文件
                if ($is_uninstall) {
                    $rdel = Db::name('weapp')->where('id', $id)->delete();
                    $this->unlinkcode($row['code']);
                }

                adminLog('卸载插件：' . $row['name']);
                $this->success('卸载成功', url('Weapp/index'));
                exit;
            }
        }

        $this->error('卸载失败');
    }

    /**
     * 启用插件
     */
    public function enable($id = 0)
    {
        if (0 < $id) {
            $row   = Db::name('weapp')->field('code')->find($id);
            $class = get_weapp_class($row['code']);
            if (!class_exists($class)) {
                $this->error('插件不存在！');
            }
            $weapp = new $class;
            /*插件启用的前置操作（可无）*/
            $this->beforeEnable($weapp);
            /*--end*/
            $r = Db::name('weapp')->where('id', $id)->update(array('status' => 1, 'update_time' => getTime()));
            if ($r) {
                /*插件启用的后置操作（可无）*/
                $this->afterEnable($weapp);
                /*--end*/
                cache("hookexec_" . $row['code'], null);
                cache('hooks', null);
                \think\Cache::clear('hooks');
                \think\Cache::clear('weapp');
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
    public function disable($id = 0)
    {
        if (0 < $id) {
            $row   = Db::name('weapp')->field('code')->find($id);
            $class = get_weapp_class($row['code']);
            if (!class_exists($class)) {
                $this->error('插件不存在！');
            }
            $weapp = new $class;
            /*插件禁用的前置操作（可无）*/
            $this->beforeDisable($weapp);
            /*--end*/
            $r = Db::name('weapp')->where('id', $id)->update(array('status' => -1, 'update_time' => getTime()));
            if ($r) {
                /*插件禁用的后置操作（可无）*/
                $this->afterDisable($weapp);
                /*--end*/
                cache("hookexec_" . $row['code'], null);
                cache('hooks', null);
                \think\Cache::clear('hooks');
                \think\Cache::clear('weapp');
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
            if (!empty($id_arr)) {
                $result    = Db::name('weapp')->field('id,name,code')
                    ->where([
                        'id' => ['IN', $id_arr],
                    ])->select();
                $name_list = get_arr_column($result, 'name');

                $r = Db::name('weapp')->where([
                    'id' => ['IN', $id_arr],
                ])
                    ->delete();
                if ($r) {
                    /*清理插件相关文件*/
                    foreach ($result as $key => $val) {
                        $unbool = $this->unlinkcode($val['code']);
                        if (true == $unbool) {
                            continue;
                        }
                    }
                    /*--end*/

                    adminLog('删除插件：' . implode(',', $name_list));
                    $this->success('删除成功');
                } else {
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
        try {
            $code_strtolower = strtolower($code);
            $filelist_path = WEAPP_DIR_NAME . DS . $code . DS . 'filelist.txt';
            if (file_exists($filelist_path)) {
                $file = fopen($filelist_path, "r"); // 以只读的方式打开文件
                if (empty($file)) {
                    return true;
                }
                delFile(WEAPP_DIR_NAME . DS . $code, true);
                while (!feof($file)) {
                    $itemStr = fgets($file); //fgets()函数从文件指针中读取一行
                    $itemStr = trim($itemStr);
                    if (!empty($itemStr) && file_exists($itemStr)) {
                        if (is_file($itemStr)) {
                            if (preg_match('/^(application\/plugins|data\/schema)\//i', $itemStr) || stristr($itemStr, $code_strtolower)) {
                                @unlink('./' . $itemStr);
                            }
                        } else if (is_dir($itemStr)) {
                            if (preg_match('/^template\/plugins\/' . $code . '$/i', $itemStr) || stristr($itemStr, $code_strtolower)) {
                                delFile('./' . $itemStr, true);
                            }
                        }
                    }
                }
                fclose($file);
                delFile(WEAPP_DIR_NAME . DS . $code, true);
            }
            return true;

        } catch (\Exception $e) {
            return true;
        }
    }

    /**
     * 分解SQL文件的语句
     */
    public function sql_split($sql, $tablepre, $code)
    {
        $installSqlAccess = ['Diyminipro']; // 允许系统表增删的权限

        $sql = str_replace("`#@__", '`' . $tablepre, $sql);

        $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);

        $sql          = str_replace("\r", "\n", $sql);
        $ret          = array();
        $num          = 0;
        $queriesarray = explode(";\n", trim($sql));
        unset($sql);
        foreach ($queriesarray as $query) {
            $ret[$num] = '';
            $queries   = explode("\n", trim($query));
            $queries   = array_filter($queries);
            foreach ($queries as $query) {
                $str1 = substr($query, 0, 1);
                if ($str1 != '#' && $str1 != '-')
                    $ret[$num] .= $query;
            }
            if ((!stristr($ret[$num], 'SET FOREIGN_KEY_CHECKS') && !stristr($ret[$num], 'SET NAMES'))) {
                if (false === stripos($ret[$num], $tablepre . 'weapp_') && !in_array($code, $installSqlAccess)) {
                    $this->error('请删除不相干的SQL语句，或者数据表前缀是否符合插件规范（#@__weapp_）');
                }
            }
            $num++;
        }
        return $ret;
    }

    /**
     * 插件升级的前置操作（可无）
     */
    public function beforeUpgrade($weappClass)
    {
        if (method_exists($weappClass, 'beforeUpgrade')) {
            $weappClass->beforeUpgrade();
        }
    }

    /**
     * 插件升级的后置操作（可无）
     */
    public function afterUpgrade($weappClass)
    {
        if (method_exists($weappClass, 'afterUpgrade')) {
            $weappClass->afterUpgrade();
        }
        /*存储插件列表所有信息*/
        model('Weapp')->clearWeappCache();
        /*end*/
    }

    /**
     * 插件安装的前置操作（可无）
     */
    public function beforeInstall($weappClass)
    {
        if (method_exists($weappClass, 'beforeInstall')) {
            $weappClass->beforeInstall();
        }
    }

    /**
     * 插件安装的后置操作（可无）
     */
    public function afterInstall($weappClass)
    {
        if (method_exists($weappClass, 'afterInstall')) {
            $weappClass->afterInstall();
        }
        /*存储插件列表所有信息*/
        model('Weapp')->clearWeappCache();
        /*end*/
    }

    /**
     * 插件卸载的前置操作（可无）
     */
    public function beforeUninstall($weappClass)
    {
        if (method_exists($weappClass, 'beforeUninstall')) {
            $weappClass->beforeUninstall();
        }
    }

    /**
     * 插件卸载的后置操作（可无）
     */
    public function afterUninstall($weappClass)
    {
        if (method_exists($weappClass, 'afterUninstall')) {
            $weappClass->afterUninstall();
        }
        /*存储插件列表所有信息*/
        model('Weapp')->clearWeappCache();
        /*end*/
    }

    /**
     * 插件启用的前置操作（可无）
     */
    public function beforeEnable($weappClass)
    {
        if (method_exists($weappClass, 'beforeEnable')) {
            $weappClass->beforeEnable();
        }
    }

    /**
     * 插件启用的后置操作（可无）
     */
    public function afterEnable($weappClass)
    {
        if (method_exists($weappClass, 'afterEnable')) {
            $weappClass->afterEnable();
        }
        /*存储插件列表所有信息*/
        model('Weapp')->clearWeappCache();
        /*end*/
    }

    /**
     * 插件禁用的前置操作（可无）
     */
    public function beforeDisable($weappClass)
    {
        if (method_exists($weappClass, 'beforeDisable')) {
            $weappClass->beforeDisable();
        }
    }

    /**
     * 插件禁用的后置操作（可无）
     */
    public function afterDisable($weappClass)
    {
        if (method_exists($weappClass, 'afterDisable')) {
            $weappClass->afterDisable();
        }
        /*存储插件列表所有信息*/
        model('Weapp')->clearWeappCache();
        /*end*/
    }

    /**
     * 一键更新插件
     */
    public function OneKeyUpgrade()
    {
        header('Content-Type:application/json; charset=utf-8');
        $code = input('param.code/s', '');
        /*插件升级的前置操作（可无）*/
        $class = get_weapp_class($code);
        $weapp = new $class;
        $this->beforeUpgrade($weapp);
        /*--end*/
        $upgradeMsg = $this->weappLogic->OneKeyUpgrade($code); //一键更新插件
        if (!empty($upgradeMsg['code'])) {
            /*插件升级的后置操作（可无）*/
            $this->afterUpgrade($weapp);
            /*--end*/
        }
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
        $sample  = 'Sample';
        $srcPath = DATA_NAME . DS . WEAPP_DIR_NAME . DS . $sample;

        if (IS_POST) {
            $post = input('post.');
            foreach ($post as $key => $val) {
                $post[$key] = addslashes($val);
            }

            $code = $post['code'] = trim($post['code']);
            if (!preg_match('/^[A-Z]([a-zA-Z0-9]*)$/', $code)) {
                $this->error('插件标识格式不正确！');
            }
            if ('Sample' == $code) {
                $this->error('插件标识已被占用！');
            }
            if (!preg_match('/^v\d+\.\d+\.\d+([0-9\.]*)$/', $post['version'])) {
                $this->error('插件版本号格式不正确！');
            }
            if (empty($post['version'])) {
                $post['version'] = 'v1.0.0';
            }
            if (!preg_match('/^v\d+\.\d+\.\d+([0-9\.]*)$/', $post['min_version'])) {
                $this->error('CMS版本号格式不正确！');
            }
            if (empty($post['min_version'])) {
                $post['min_version'] = getCmsVersion();
            }

            /*复制样本结构到插件目录下*/
            $srcFiles = getDirFile($srcPath);
            $filetxt  = '';
            foreach ($srcFiles as $key => $srcfile) {
                $dstfile = str_replace($sample, $code, $srcfile);
                $dstfile = str_replace(strtolower($sample), strtolower($code), $dstfile);
                if (!preg_match('/^' . WEAPP_DIR_NAME . '\/' . $code . '/i', $dstfile)) {
                    $filetxt .= $dstfile . "\n\r";
                }
                if (tp_mkdir(dirname($dstfile))) {
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
            $filetxt .= WEAPP_DIR_NAME . '/' . $code;
            @file_put_contents(WEAPP_DIR_NAME . DS . $code . DS . 'filelist.txt', $filetxt); //初始化插件文件列表
            /*--end*/

            /*读取配置文件，并替换插件信息*/
            $configPath = WEAPP_DIR_NAME . DS . $code . DS . 'config.php';
            if (!eyPreventShell($configPath) || !file_exists($configPath)) {
                $this->error('创建插件结构不完整，请重新创建！');
            }
            $strConfig = file_get_contents(WEAPP_DIR_NAME . DS . $code . DS . 'config.php');
            $strConfig = str_replace('#CODE#', $code, $strConfig);
            $strConfig = str_replace('#NAME#', $post['name'], $strConfig);
            $strConfig = str_replace('#VERSION#', $post['version'], $strConfig);
            $strConfig = str_replace('#MIN_VERSION#', $post['min_version'], $strConfig);
            $strConfig = str_replace('#AUTHOR#', $post['author'], $strConfig);
            $strConfig = str_replace('#DESCRIPTION#', $post['description'], $strConfig);
            $strConfig = str_replace('#SCENE#', $post['scene'], $strConfig);
            @chmod(WEAPP_DIR_NAME . DS . $code . DS . 'config.php'); //配置文件的地址
            $puts = @file_put_contents(WEAPP_DIR_NAME . DS . $code . DS . 'config.php', $strConfig); //配置文件的地址
            if (!$puts) {
                $this->error('替换插件信息失败，请设置目录权限为 755！');
            }
            /*--end*/

            /*推送插件标识到服务器，确保唯一性*/
            $service_ey = base64_decode(config('service_ey'));
            $url        = "{$service_ey}/index.php?m=api&c=Weapp&a=push_add_authorization";
            $config = $post;
            $configData = include WEAPP_DIR_NAME . DS . $code . DS . 'config.php';
            !empty($configData) && $config = array_merge($config, $configData);
            $post_data = [
                'code'  => $code,
                'config'    => json_encode($config),
                'name'  => $config['name'],
                'description'  => $config['description'],
            ];
            $post_data = mchStrCode(json_encode($post_data), 'ENCODE', 'hln');
            $postData = [
                'post_data' => $post_data,
                'version'   => getCmsVersion(),
            ];
            $response   = httpRequest2($url, "POST", $postData);
            $params = json_decode($response, true);
            if (empty($params['code'])) {
                $msg = !empty($params['msg']) ? $params['msg'] : '同步插件信息到服务器失败！';
            }
            /*--end*/

            $this->success('初始化插件成功，请在该插件基础上进行二次开发！', url('Weapp/index'), [], 3);
        }

        /*删除多余目录以及文件，兼容v1.1.7之后的版本*/
        if (file_exists($srcPath . DS . 'application' . DS . 'weapp')) {
            delFile($srcPath . DS . 'application' . DS . 'weapp', true);
        }
        if (file_exists($srcPath . DS . 'template' . DS . 'weapp')) {
            delFile($srcPath . DS . 'template' . DS . 'weapp', true);
        }
        if (file_exists($srcPath . DS . 'weapp' . DS . $sample . DS . 'behavior' . DS . 'weapp')) {
            delFile($srcPath . DS . 'weapp' . DS . $sample . DS . 'behavior' . DS . 'weapp', true);
        }
        if (file_exists($srcPath . DS . 'weapp' . DS . $sample . DS . 'template' . DS . 'skin' . DS . 'font')) {
            delFile($srcPath . DS . 'weapp' . DS . $sample . DS . 'template' . DS . 'skin' . DS . 'font', true);
        }
        if (file_exists($srcPath . DS . 'weapp' . DS . $sample . DS . 'common.php')) {
            @unlink($srcPath . DS . 'weapp' . DS . $sample . DS . 'common.php');
        }
        /*--end*/

        $assign_data                = array();
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

            $post            = input('post.');
            $code            = $post['code'];
            $additional_file = $post['additional_file'];

            if (!preg_match('/^[A-Z]([a-zA-Z0-9]*)$/', $code)) {
                $this->error('插件标识格式不正确！');
            } else if (!file_exists(WEAPP_DIR_NAME . DS . $code)) {
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
            $zip      = new \ZipArchive();//新建一个ZipArchive的对象
            $filepath = DATA_PATH . WEAPP_DIR_NAME;
            tp_mkdir($filepath);
            $zipName = $filepath . DS . $code . '.zip';//定义打包后的包名
            if ($zip->open($zipName, \ZIPARCHIVE::OVERWRITE | \ZIPARCHIVE::CREATE) !== TRUE)
                $this->error('插件压缩包打开失败！');

            /*打包插件标准结构涉及的文件与目录，并且打包zip*/
            $is_template = false;
            $filetxt     = '';
            foreach ($packfiles as $key => $srcfile) {
                if (!stristr($srcfile, "weapp/{$code}/") && !stristr($srcfile, "template/plugins/" . strtolower($code) . "/")) {
                    $filetxt .= $srcfile . "\n";
                } else if (stristr($srcfile, "template/plugins/" . strtolower($code) . "/")) {
                    $is_template = true;
                }
                // $dstfile = DATA_NAME.DS.WEAPP_DIR_NAME.DS.$code.DS.$srcfile;
                // if(true == tp_mkdir(dirname($dstfile))) {
                if (file_exists($srcfile)) {
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
            if ($is_template) {
                $filetxt .= "template/plugins/" . strtolower($code) . "\n";
            }
            $filetxt      .= "weapp/{$code}" . "\n";
            $src_filelist = WEAPP_DIR_NAME . DS . $code . DS . 'filelist.txt';
            @file_put_contents($src_filelist, $filetxt); //初始化插件文件列表  
            // copy($src_filelist, $dst_filelist);
            /*--end*/
            $zip->addFile($src_filelist);
            $zip->close();

            /*压缩插件目录*/
            if (!file_exists($zipName)) {
                $this->error('打包zip文件包失败！');
            }

            $msg = "打包成功，【{$code}.zip】插件包在 data/weapp/ 目录下。";
            $this->success($msg, url('Weapp/pack'), [], 20);

        }

        return $this->fetch();
    }

    /**
     * 压缩文件
     */
    private function zip($files = array(), $zipName)
    {
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
        foreach ($files as $val) {
            //$attachfile = $attachmentDir . $val['filepath']; //获取原始文件路径
            if (file_exists($val)) {
                //addFile函数首个参数如果带有路径，则压缩的文件里包含的是带有路径的文件压缩
                //若不希望带有路径，则需要该函数的第二个参数
                $zip->addFile($val, basename($val));//第二个参数是放在压缩包中的文件名称，如果文件可能会有重复，就需要注意一下
            }
        }
        $zip->close();//关闭

        if (!file_exists($zipName)) {
            return "无法找到文件"; //即使创建，仍有可能失败
        }

        //如果不要下载，下面这段删掉即可，如需返回压缩包下载链接，只需 return $zipName;
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename=' . basename($zipName)); //文件名
        header("Content-Type: application/zip"); //zip格式的
        header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
        header('Content-Length: ' . filesize($zipName)); //告诉浏览器，文件大小
        @readfile($zipName);
    }

    /**
     * 验证插件标识是否同名
     */
    public function ajax_check_code($code)
    {
        $version = getCmsVersion();
        $service_ey = base64_decode(config('service_ey'));
        $url        = "{$service_ey}/index.php?m=api&c=Weapp&a=checkIsCode&code={$code}&version={$version}";
        $response   = httpRequest2($url, "GET");
        if (1 == intval($response)) {
            $this->success('插件标识可使用！', url('Weapp/create'));
        } else if (-1 == intval($response)) {
            $this->error('插件标识已被占用！');
        }
        $this->error('远程验证插件标识失败！');
    }

    /**
     * 获取云插件列表
     */
    public function plugin()
    {
        $is_pay    = input('param.is_pay/d', 0);
        $keywords  = input('param.keywords/s', 0);
        $url       = 'http://plugins.eyoucms.com/user/ajax_memberplugin.php?action=plugin';
        $post_data = [
            'page'      => input('param.p/d', 1),
            'is_pay'    => $is_pay,
            'keywords'  => $keywords,
            'query_str' => input('param.'),
            'pid'   => $this->php_servicemeal,
        ];
        $response  = httpRequest2($url, 'POST', $post_data);
        $params    = json_decode($response, true);
        if (empty($params['code'])) {
            $msg = !empty($params['msg']) ? $params['msg'] : '连接远程插件接口失败！';
            $this->error($msg);
        }
        
        $local = Db::name('weapp')->where(['status'=>1])->getAllWithIndex('code');
        foreach ($params['list'] as $key =>$val){
            if ($val['meal']){
                $val['meal'] = unserialize($val['meal']);
            }
            $val['install'] = 0;
            foreach ($local as $k =>$v){
                if ($val['weapp_code'] == $k){
                    $val['install']=1;
                    break;
                }
            }
            $params['list'][$key] = $val;
        }

        $Page                 = new Page($params['total'], 15);// 实例化分页类 传入总记录数和每页显示的记录数
        $show                 = $Page->show(); // 分页显示输出
        $assign_data['page']  = $show; // 赋值分页输出
        $assign_data['list']  = $params['list']; // 赋值数据集
        $assign_data['pager'] = $Page; // 赋值分页对象

        $assign_data['service_ey'] = $this->service_ey;
        $assign_data['ip'] = serverIP();

        //序列号
        $serial_number = DEFAULT_SERIALNUMBER;
        $constsant_path = APP_PATH.MODULE_NAME.'/conf/constant.php';
        if (file_exists($constsant_path)) {
            require_once($constsant_path);
            defined('SERIALNUMBER') && $serial_number = SERIALNUMBER;
        }
        $assign_data['serial_number'] = $serial_number;

        $assign_data['weapp_plugin_open'] = tpCache('php.php_weapp_plugin_open');

        $this->assign($assign_data);
        return $this->fetch();
    }

    /**     
     * @param type $fileUrl 下载文件地址
     * @return string 错误或成功提示
     */
    private function downloadFile($fileUrl, $saveDir = '', $fileName = '')
    {
        empty($saveDir) && $saveDir = UPLOAD_PATH . 'tmp' . DS; //保存路径
        if (empty($fileName)) {
            $folderName = session('admin_id') . '-' . dd2char(date("ymdHis") . mt_rand(100, 999));
            $fileName   = $folderName . ".zip";
        }

        $saveDir .= $fileName; // 保存目录
        tp_mkdir(dirname($saveDir));
        $file = httpRequest($fileUrl);
        if(empty($file)){
            return ['code' => 0, 'msg' => '该插件包不存在']; // 文件存在直接退出
        }
        if (preg_match('#__HALT_COMPILER()#i', $file)) {
            return ['code' => 0, 'msg' => '下载包损坏，请联系官方客服！'];
        }
        curl_close ($ch);
        $fp = fopen($saveDir,'w');
        fwrite($fp, $file);
        fclose($fp);
        if(!eyPreventShell($saveDir) || !file_exists($saveDir) || !filesize($saveDir))
        {
            return ['code' => 0, 'msg' => '下载保存插件包失败，请检查所有目录的权限以及用户组不能为root'];
        }
        return ['code' => 1, 'msg' => '下载成功', 'filepath'=>$saveDir];
    }

    /**
     * 远程插件安装
     * @param string $id
     * @param string $url
     * @param string $is_authortoken
     * @param string $money
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function remoteInstall($code = '',$min_version='')
    {
        // 防止php超时
        function_exists('set_time_limit') && set_time_limit(0);

        if (IS_POST) {
            //版本判断
            $cms_version = getCmsVersion();
            $min_version = trim($min_version, 'v');
            if ($cms_version < 'v'.$min_version) {
                $this->error('当前CMS版本太低，该插件要求CMS版本 >= v' . $min_version . '，请升级系统！');
            }
            /*是否付费start*/
            $post_data = [
                'code' => base64_encode($code),
                'cms_version' => $cms_version,
                'pid'   => $this->php_servicemeal,
            ];
            $url       = 'http://plugins.eyoucms.com/user/ajax_memberplugin.php?action=verify';
            $response  = httpRequest2($url, 'POST', $post_data);
            $params    = json_decode($response, true);

            /*是否付费end*/
            if (empty($params['code'])) {
                $msg = !empty($params['msg']) ? $params['msg'] : '安装失败';
                $this->error($msg);
            }
            if (!empty($params['url'])) {
                $params['url'] = trim($params['url']);
                $this->downloadInstall($params['url']);
            }
        }
    }

    public function downloadInstall($url)
    {
        $parse_data = parse_url($url);
        if (empty($parse_data['host']) || GetUrlToDomain($parse_data['host']) != 'eyoucms.com') {
            $this->error('该云插件下载链接出错！', url('Weapp/plugin'));
        } else {
            $paths = explode('.', $parse_data['path']);
            $exts = end($paths);
            if (empty($exts) || 'zip' != $exts) {
                $this->error('该云插件下载链接出错！', url('Weapp/plugin'));
            }
        }
        
        /*远程下载文件start*/
        $savePath   = UPLOAD_PATH . 'tmp' . DS;//保存路径
        $folderName = session('admin_id') . '-' . dd2char(date("ymdHis") . mt_rand(100, 999));
        $fileName   = $folderName . ".zip";
        //保存至框架应用根目录/public/upload/tmp/ 目录下  返回文件详细路径+名称
        $result = $this->downloadFile($url, $savePath, $fileName);
        if (!isset($result['code']) || $result['code'] != 1) {
            $this->error($result['msg']);
        }
        $filepath = $result['filepath'];
        /*远程下载文件end*/

        if (file_exists($filepath)) {
            /*解压文件*/
            $zip = new \ZipArchive();//新建一个ZipArchive的对象
            if ($zip->open($filepath) != true) {
                $this->error("插件压缩包读取失败!", url('Weapp/plugin'));
            }
            $zip->extractTo($savePath . $folderName . DS);//假设解压缩到在当前路径下插件名称文件夹内
            $zip->close();//关闭处理的zip文件
            /*--end*/
            /*获取插件目录名称*/
            $dirList   = glob($savePath . $folderName . DS . WEAPP_DIR_NAME . DS . '*');
            $weappPath = !empty($dirList) ? $dirList[0] : '';
            if (empty($weappPath)) {
                @unlink(realpath($savePath . $fileName));
                delFile($savePath . $folderName, true);
                $this->error('插件压缩包缺少目录文件', url('Weapp/plugin'));
            }

            $weappPath    = str_replace("\\", DS, $weappPath);
            $weappPathArr = explode(DS, $weappPath);
            $weappName    = $weappPathArr[count($weappPathArr) - 1];
            /*--end*/

            /*修复非法插件上传，导致任意文件上传的漏洞*/
            $configfile = $savePath . $folderName . DS . WEAPP_DIR_NAME . DS . $weappName . '/config.php';
            if (!file_exists($configfile)) {
                $msg = '插件不符合标准！';
                $filelist_tmp = getDirFile($savePath . $folderName . DS . WEAPP_DIR_NAME . DS . $weappName);
                if (empty($filelist_tmp)) {
                    $msg = '压缩包解压失败，请联系空间商';
                }
                @unlink(realpath($savePath . $fileName));
                delFile($savePath . $folderName, true);
                $this->error($msg, url('Weapp/plugin'));
            } else {
                $configdata = include($configfile);
                if (empty($configdata) || !is_array($configdata)) {
                    @unlink(realpath($savePath . $fileName));
                    delFile($savePath . $folderName, true);
                    $this->error('插件不符合标准！', url('Weapp/plugin'));
                } else {
                    $sampleConfig = include(DATA_NAME . DS . 'weapp' . DS . 'Sample' . DS . 'weapp' . DS . 'Sample' . DS . 'config.php');
                    if (is_array($sampleConfig)) {
                        foreach ($configdata as $key => $val) {
                            if ('permission' != $key && !isset($sampleConfig[$key])) {
                                @unlink(realpath($savePath . $fileName));
                                delFile($savePath . $folderName, true);
                                $this->error('插件不符合标准！', url('Weapp/index'));
                            }
                        }
                    }
                }
            }
            /*--end*/

            // 递归复制文件夹
            $copy_bool = recurse_copy($savePath . $folderName, rtrim(ROOT_PATH, DS));
            if (true !== $copy_bool) {
                $this->error($copy_bool);
            }

            /*删除上传的插件包*/
            @unlink(realpath($savePath . $fileName));
            @delFile($savePath . $folderName, true);
            /*--end*/

            /*安装插件*/
            $configfile = WEAPP_DIR_NAME . DS . $weappName . '/config.php';
            if (file_exists($configfile)) {
                $configdata = include($configfile);
                $code       = isset($configdata['code']) ? $configdata['code'] : 'error_' . date('Ymd');
                Db::name('weapp')->where(['code' => $code])->delete();

                $addData  = [
                    'code'     => $code,
                    'name'     => isset($configdata['name']) ? $configdata['name'] : '配置信息不完善',
                    'config'   => empty($configdata) ? '' : json_encode($configdata),
                    'data'     => '',
                    'add_time' => getTime(),
                ];
                $weapp_id = Db::name('weapp')->insertGetId($addData);
                if (!empty($weapp_id)) {
                    \think\Cache::clear('weapp');
                    $this->install($weapp_id);
                }
            }
            /*--end*/
        }
    }

    public function pay_success()
    {
        $url      = $this->service_ey.'/index.php?m=api&c=Pay&a=notify';
        $response = httpRequest($url, 'POST', $_GET);
        $params   = json_decode($response, true);
        return $this->fetch();
    }

    /**
     * 我的插件列表删除云插件
     */
    public function del_remote()
    {
        if (IS_POST) {
            $id = input('del_id/d');
            if (!empty($id)) {
                $result    = Db::name('weapp')->field('id,name,code,is_buy')->where('id',$id)->find();
                if ($result['is_buy'] == 1){
                    $r = Db::name('weapp')->where('id',$id)->update(['is_buy'=>2]);
                    if ($r) {
                        \think\Cache::clear('weapp');
                        $res = ['code'=>1,'msg'=>'删除成功'];
                        respose($res);
                    } else {
                        $res = ['code'=>0,'msg'=>'删除失败'];
                        respose($res);
                    }
                }
            } else {
                $res = ['code'=>0,'msg'=>'参数有误'];
                respose($res);
            }
        }
        $res = ['code'=>0,'msg'=>'非法访问'];
        respose($res);
    }

    /**
     * 检测插件更新包
     * @return [type] [description]
     */
    public function ajax_check_upgrade() {
        $code = input('param.code/s');
        $upgrade = array();
        if (!empty($code)) {
            $weappInfo = Db::name('weapp')->where('code', $code)->find();
            $weappConfig = json_decode($weappInfo['config'], true);
            if (1 == $weappInfo['is_upgrade'] && !empty($weappConfig['version'])) {
                // URL参数
                $vaules        = array(
                    'domain' => request()->host(true),
                    'ip'    => serverIP(),
                    'code'   => $code,
                    'v'      => $weappConfig['version'],
                    // 'dev'   => 1,
                );
                $tmp_str       = 'L2luZGV4LnBocD9tPWFwaSZjPVdlYXBwJmE9Y2hlY2tCYXRjaFZlcnNpb24m';
                $service_url   = base64_decode(config('service_ey')) . base64_decode($tmp_str);
                $url           = $service_url . http_build_query($vaules);
                $context       = stream_context_set_default(array('http' => array('timeout' => 3, 'method' => 'GET')));
                $response      = @file_get_contents($url, false, $context);
                $batch_upgrade = json_decode($response, true);

                if (is_array($batch_upgrade) && !empty($batch_upgrade)) {
                    $upgrade = $this->weappLogic->checkBatchVersion($batch_upgrade); //升级包消息 
                    $upgrade = !empty($upgrade[$code]) ? $upgrade[$code] : [];
                }
            }
        }
        $this->success('请求成功', null, ['upgrade'=>$upgrade]);
    }

    /**
     * 取消插件更新提醒
     * @return [type] [description]
     */
    public function ajax_cancel_upgrade() {
        $code = input('param.code/s');
        if (!empty($code)) {
            $data = [
                'is_upgrade'    => 0,
                'update_time'   => getTime(),
            ];
            Db::name('weapp')->where('code', $code)->update($data);
        }
        $this->success('请求成功');
    }
}