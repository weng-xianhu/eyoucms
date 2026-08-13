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
use think\Backup;

class Tools extends Base {

    public function _initialize() {
        parent::_initialize();
        $this->language_access(); // 多语言功能操作权限
    }
    
    /**
     * 数据表列表
     */
    public function index()
    {
        $dbtables = Db::query('SHOW TABLE STATUS');
        $total = 0;
        $list = array();
        foreach ($dbtables as $k => $v) {
            if (preg_match('/^'.PREFIX.'/i', $v['Name'])) {
                $v['size'] = format_bytes($v['Data_length'] + $v['Index_length']);
                $list[$k] = $v;
                $total += $v['Data_length'] + $v['Index_length'];
            }
        }
        $path = tpCache('global.web_sqldatapath');
        $path = !empty($path) ? $path : config('DATA_BACKUP_PATH');
        if (file_exists(realpath(trim($path, '/')) . DS . 'backup.lock')) {
            @unlink(realpath(trim($path, '/')) . DS . 'backup.lock');
        }
        // if (session('?backup_config.path')) {
            //备份完成，清空缓存
            session('backup_tables', null);
            session('backup_file', null);
            session('backup_config', null);
        // }
        $this->assign('list', $list);
        $this->assign('total', format_bytes($total));
        $this->assign('tableNum', count($list));
        return $this->fetch();
    }

    /**
     * 数据备份
     */
    public function export($tables = null, $id = null, $start = null,$optstep = 0)
    {
        //防止备份数据过程超时
        function_exists('set_time_limit') && set_time_limit(0);
        @ini_set('memory_limit','-1');

        /*升级完自动备份所有数据表*/
        if ('all' == $tables) {
            $dbtables = Db::query('SHOW TABLE STATUS');
            $list = array();
            foreach ($dbtables as $k => $v) {
                if (preg_match('/^'.PREFIX.'/i', $v['Name'])) {
                    $list[] = $v['Name'];
                }
            }
            $tables = $list;
            unlink(session('backup_config.path') . 'backup.lock');
        }
        /*--end*/

        if(IS_POST && !empty($tables) && is_array($tables) && empty($optstep)){ //初始化

            /*多语言*/
            $tpCacheData = ['php_atqueryrequest_time'=>0, 'php_atqueryrequest_time2'=>0];
            $langRow = \think\Db::name('language')->order('id asc')->select();
            foreach ($langRow as $key => $val) {
                tpCache('php', $tpCacheData, $val['mark']); // n
            }
            /*--end*/

            $path = tpCache('global.web_sqldatapath');
            $path = !empty($path) ? $path : config('DATA_BACKUP_PATH');
            $path = trim($path, '/');
            if(!empty($path) && !is_dir($path)){
                mkdir($path, 0755, true);
            }

            //读取备份配置
            $config = array(
                'path'     => realpath($path) . DS,
                'part'     => config('DATA_BACKUP_PART_SIZE'),
                'compress' => config('DATA_BACKUP_COMPRESS'),
                'level'    => config('DATA_BACKUP_COMPRESS_LEVEL'),
            );

            //检查备份目录是否可写
            if(!is_writeable($config['path'])){
                return json(array('msg'=>'备份目录不存在或不可写，请检查后重试！', 'code'=>0, 'url'=>''));
            }
            //检查是否有正在执行的任务
            $lock = "{$config['path']}backup.lock";
            if(is_file($lock)){
                return json(array('msg'=>'检测到有一个备份任务正在执行，请稍后再试！', 'code'=>0, 'url'=>''));
            } else {
                //创建锁文件
                file_put_contents($lock, $_SERVER['REQUEST_TIME']);
                session('backup_config', $config);
            }

            //生成备份文件信息
            $file = array(
                'name' => date('Ymd-His'),
                'part' => 1,
                'version' => getCmsVersion(),
            );
            session('backup_file', $file);
            //缓存要备份的表
            session('backup_tables', $tables);
            //创建备份文件
            $Database = new Backup($file, $config);
            if(false !== $Database->create()){
                // 同步备份到安装目录里
                $install_path = $this->install_exist();
                if (!empty($install_path)) {
                    try {
                        $config_ins = $config;
                        $config_ins['path'] = $install_path . DS;
                        $config_ins['part'] = 1000000000; // 接近1G
                        $config_ins['compress'] = 0;
                        $file_ins = [
                            'name' => 'install',
                            'part' => 1,
                            'version' => getCmsVersion(),
                        ];
                        if (@unlink($config_ins['path'].'eyoucms.sql')) {
                            $Database_ins = new Backup($file_ins, $config_ins);
                            $Database_ins->create();
                        }
                    } catch (\Exception $e) {}
                }

                $speed = (floor((1/count($tables))*10000)/10000*100);
                $speed = sprintf("%.2f", $speed);
                $tab = array('id' => 0, 'start' => 0, 'speed'=>$speed, 'table'=>$tables[0], 'optstep'=>1);
                return json(array('tables' => $tables, 'tab' => $tab, 'msg'=>'初始化成功！', 'code'=>1, 'url'=>''));
            } else {
                return json(array('msg'=>'初始化失败，备份文件创建失败！', 'code'=>0, 'url'=>''));
            }
        } elseif (IS_POST && is_numeric($id) && is_numeric($start) && 1 == intval($optstep)) { //备份数据
            $init_start = $start;
            $tables = session('backup_tables');
            //备份指定表
            $Database = new Backup(session('backup_file'), session('backup_config'));
            $start  = $Database->backup($tables[$id], $init_start);
            if(false === $start){ //出错
                return json(array('msg'=>'备份出错！', 'code'=>0, 'url'=>''));
            } else {
                // 同步备份到安装目录里
                $install_path = $this->install_exist();
                if (!empty($install_path)) {
                    //备份指定表
                    try {
                        $config_ins = session('backup_config');
                        $config_ins['path'] = $install_path . DS;
                        $config_ins['part'] = 1000000000; // 接近1G
                        $config_ins['compress'] = 0;
                        $file_ins = [
                            'name' => 'install',
                            'part' => 1,
                            'version' => getCmsVersion(),
                        ];
                        $Database_ins = new Backup($file_ins, $config_ins);
                        $Database_ins->backup($tables[$id], $init_start);
                    } catch (\Exception $e) {}
                }
                if (0 === $start) { //下一表
                    if(isset($tables[++$id])){
                        $speed = (floor((($id+1)/count($tables))*10000)/10000*100);
                        $speed = sprintf("%.2f", $speed);
                        $tab = array('id' => $id, 'start' => 0, 'speed' => $speed, 'table'=>$tables[$id], 'optstep'=>1);
                        return json(array('tab' => $tab, 'msg'=>'备份完成！', 'code'=>1, 'url'=>''));
                    }
                    else { //备份完成，清空缓存

                        // 自动备份用于自动还原
                        $sql_backup_path = DATA_PATH.'sql';
                        if (file_exists($sql_backup_path)) {
                            $srcfile = session('backup_config.path').session('backup_file.name').'-'.session('backup_file.part').'-'.session('backup_file.version').'.sql';
                            $dstfile = $sql_backup_path.'/'.getVersion().'.sql';
                            @copy($srcfile, $dstfile);
                        }
                        
                        @unlink(session('backup_config.path') . 'backup.lock');
                        session('backup_tables', null);
                        session('backup_file', null);
                        session('backup_config', null);
                        adminLog('备份数据库');
                        return json(array('msg'=>'备份完成！', 'code'=>1, 'url'=>''));
                    }
                } else {
                    $rate = floor(100 * ($start[0] / $start[1]));
                    $speed = floor((($id+1)/count($tables))*10000)/10000*100 + ($rate/100);
                    $speed = sprintf("%.2f", $speed);
                    $tab  = array('id' => $id, 'start' => $start[0], 'speed' => $speed, 'table'=>$tables[$id], 'optstep'=>1);
                    return json(array('tab' => $tab, 'msg'=>"正在备份...({$rate}%)", 'code'=>1, 'url'=>''));
                }
            }
        } else {//出错
            return json(array('msg'=>'参数有误', 'tab'=>['speed'=>-1], 'code'=>0, 'url'=>''));
        }
    }
    
    /**
     * 安装目录是否存在
     * @return [type] [description]
     */
    private function install_exist()
    {
        $install_path = ROOT_PATH.'install';
        if (!is_dir($install_path) || !file_exists($install_path)) {
            $dirlist = glob('install_*');
            $install_dirname = current($dirlist);
            if (!empty($install_dirname)) {
                $install_path = ROOT_PATH.$install_dirname;
            }
        }
        if (is_dir($install_path) && file_exists($install_path)) {
            return $install_path;
        } else {
            return false;
        }
    }

    /**
     * 优化
     */
    public function optimize()
    {
        $batchFlag = input('get.batchFlag', 0, 'intval');
        //批量删除
        if ($batchFlag) {
            $table = input('key', array());
        }else {
            $table[] = input('tablename' , '');
        }

        if (empty($table)) {
            $this->error('请选择数据表');
        }

        // 安全修复: 表名白名单验证，防止 SQL 注入
        $allowedTables = $this->getAllowedTables();
        $safeTables = [];
        foreach ($table as $t) {
            $t = preg_replace('/[^\w\-]/', '', $t); // 只允许字母数字下划线、连接符
            if (in_array($t, $allowedTables)) {
                $safeTables[] = '`' . $t . '`';
            }
        }
        if (empty($safeTables)) {
            $this->error('无效的数据表');
        }

        $strTable = implode(',', $safeTables);
        if (!DB::query("OPTIMIZE TABLE {$strTable} ")) {
            $strTable = '';
        }
        adminLog('优化数据库：'.$strTable);
        $this->success("操作成功" . $strTable, url('Tools/index'));

    }
    
    /**
     * 修复
     */
    public function repair()
    {
        $batchFlag = input('get.batchFlag', 0, 'intval');
        //批量删除
        if ($batchFlag) {
            $table = input('key', array());
        }else {
            $table[] = input('tablename' , '');
        }

        if (empty($table)) {
            $this->error('请选择数据表');
        }

        // 安全修复: 表名白名单验证，防止 SQL 注入
        $allowedTables = $this->getAllowedTables();
        $safeTables = [];
        foreach ($table as $t) {
            $t = preg_replace('/[^\w\-]/', '', $t);
            if (in_array($t, $allowedTables)) {
                $safeTables[] = '`' . $t . '`';
            }
        }
        if (empty($safeTables)) {
            $this->error('无效的数据表');
        }

        $strTable = implode(',', $safeTables);
        if (!in_array(config('database.type'), array('dm', 'kingbase'))) { // 达梦、人大金仓不支持REPAIR TABLE
            if (!DB::query("REPAIR TABLE {$strTable} ")) {
                $strTable = '';
            }
        }
        adminLog('修复数据库：'.$strTable);
        $this->success("操作成功" . $strTable, url('Tools/index'));

    }

    /**
     * 安全修复: 获取允许操作的数据表白名单
     * @return array
     */
    private function getAllowedTables()
    {
        static $tables = null;
        if ($tables === null) {
            $dbName = config('database.database');
            if (config('database.type') == 'dm') { // 达梦优化
                $result = Db::query("SELECT table_name FROM all_tables");
                $tables = array();
                foreach ($result as $row) {
                    $tables[] = current($row);
                }
            } elseif (config('database.type') == 'kingbase') { // 人大金仓优化
                $result = Db::query("SELECT tablename FROM pg_tables WHERE schemaname = 'public'");
                $tables = array();
                foreach ($result as $row) {
                    $tables[] = current($row);
                }
            } else {
                $result = Db::query("SHOW TABLES");
                $tables = array();
                foreach ($result as $row) {
                    $tables[] = $row['Tables_in_' . $dbName];
                }
            }
        }
        return $tables;
    }

    /**
     * 数据还原
     */
    public function restore()
    {
        $path = tpCache('global.web_sqldatapath');
        $path = !empty($path) ? $path : config('DATA_BACKUP_PATH');
        $path = trim($path, '/');
        if(!empty($path) && !is_dir($path)){
            mkdir($path, 0755, true);
        }
        $path = realpath($path);
        $flag = \FilesystemIterator::KEY_AS_FILENAME;
        $glob = new \FilesystemIterator($path,  $flag);
        $list = array();
        $filenum = $total = 0;
        foreach ($glob as $name => $file) {
            if(preg_match('/^\d{8,8}-\d{6,6}-\d+-v\d+\.\d+\.\d+(.*)\.sql(?:\.gz)?$/', $name)){
                $name = sscanf($name, '%4s%2s%2s-%2s%2s%2s-%d-%s');
                $date = "{$name[0]}-{$name[1]}-{$name[2]}";
                $time = "{$name[3]}:{$name[4]}:{$name[5]}";
                $part = $name[6];
                $version = preg_replace('#\.sql(.*)#i', '', $name[7]);
                $info = pathinfo($file);
                if(isset($list["{$date} {$time}"])){
                    $info = $list["{$date} {$time}"];
                    $info['part'] = max($info['part'], $part);
                    $info['size'] = $info['size'] + $file->getSize();
                } else {
                    $info['part'] = $part;
                    $info['size'] = $file->getSize();
                }
                $info['compress'] = ($info['extension'] === 'sql') ? '-' : $info['extension'];
                $info['time']  = strtotime("{$date} {$time}");
                $info['version']  = $version;
                $filenum++;
                $total += $info['size'];
                $list["{$date} {$time}"] = $info;
            }
        }
        array_multisort($list, SORT_DESC);
        $this->assign('list', $list);
        $this->assign('filenum',$filenum);
        $this->assign('total',$total);
        return $this->fetch();
    }

    /**
     * 执行还原数据库操作
     * @param int $time
     * @param null $part
     * @param null $start
     */
    public function import($time = 0, $part = null, $start = null)
    {
        function_exists('set_time_limit') && set_time_limit(0);

        if(is_numeric($time) && is_null($part) && is_null($start)){ //初始化
            //获取备份文件信息
            $name  = date('Ymd-His', $time) . '-*.sql*';
            $path = tpCache('global.web_sqldatapath');
            $path = !empty($path) ? $path : config('DATA_BACKUP_PATH');
            $path = trim($path, '/');
            $path  = realpath($path) . DS . $name;
            $files = glob($path);
            $list  = array();
            foreach($files as $name){
                $basename = basename($name);
                $match    = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d');
                $gz       = preg_match('/^\d{8,8}-\d{6,6}-\d+\.sql.gz$/', $basename);
                $list[$match[6]] = array($match[6], $name, $gz);
            }
            ksort($list);

            //检测文件正确性
            $last = end($list);
            if(count($list) === $last[0]){
                session('backup_list', $list); //缓存备份列表
                $part = 1;
                $start = 0;
                $data = array('part' => $part, 'start' => $start);
                // $this->success('初始化完成！', null, array('part' => $part, 'start' => $start));
                respose(array('code'=>1, 'msg'=>"初始化完成！准备还原#{$part}...", 'rate'=>'', 'data'=>$data));
            } else {
                // $this->error('备份文件可能已经损坏，请检查！');
                respose(array('code'=>0, 'msg'=>"备份文件可能已经损坏，请检查！"));
            }
        } elseif(is_numeric($part) && is_numeric($start)) {
            $list  = session('backup_list');
            $path = tpCache('global.web_sqldatapath');
            $path = !empty($path) ? $path : config('DATA_BACKUP_PATH');
            $path = trim($path, '/');
            $db = new Backup($list[$part], array(
                    'path'     => realpath($path) . DS,
                    'compress' => $list[$part][2]));
            $start = $db->import($start);
            if(false === $start){
                // $this->error('还原数据出错！');
                respose(array('code'=>0, 'msg'=>"还原数据出错！", 'rate'=>'0%'));
            } elseif(0 === $start) { //下一卷
                if(isset($list[++$part])){
                    $data = array('part' => $part, 'start' => 0);
                    // $this->success("正在还g原...#{$part}", null, $data);
                    $rate = (floor((($start+1)/count($list))*10000)/10000*100).'%';
                    respose(array('code'=>1, 'msg'=>"正在还原#{$part}...", 'rate'=>$rate, 'data'=>$data));
                } else {
                    adminLog('还原数据库');
                    session('backup_list', null);
                    delFile(RUNTIME_PATH);
                    \think\Cache::clear();
                    respose(array('code'=>1, 'msg'=>"还原完成...", 'rate'=>'100%'));
                    // $this->success('还原完成！');
                }
            } else {
                $data = array('part' => $part, 'start' => $start[0]);
                if($start[1]){
                    $rate = floor(100 * ($start[0] / $start[1])).'%';
                    respose(array('code'=>1, 'msg'=>"正在还原#{$part}...", 'rate'=>$rate, 'data'=>$data));
                    // $this->success("正在还d原...#{$part} ({$rate}%)", null, $data);
                } else {
                    $data['gz'] = 1;
                    respose(array('code'=>1, 'msg'=>"正在还原#{$part}...", 'data'=>$data, 'start'=>$start));
                    // $this->success("正在还s原...#{$part}", null, $data);
                }
            }
        } else {
            // $this->error('参数错误！');
            respose(array('code'=>0, 'msg'=>"参数有误", 'rate'=>'0%'));
        }
    }

    /**
     * (新)执行还原数据库操作，流式读取 + 断点续传，单卷可支持超大文件
     * @param int  $time   备份时间戳
     * @param null $part   当前卷号，为空表示初始化
     * @param null $offset 当前卷内字节偏移
     */
    public function new_import($time = 0, $part = null, $offset = null)
    {
        function_exists('set_time_limit') && set_time_limit(0);
        @ini_set('memory_limit','-1');
        function_exists('ignore_user_abort') && ignore_user_abort(true);

        if (!is_numeric($time) || intval($time) <= 0) {
            respose(array('code'=>0, 'msg'=>'参数有误'));
        }

        // 初始化：校验分卷完整性与版本，缓存卷列表
        if (is_null($part) || is_null($offset)) {
            $list = $this->getBackupVolumes($time);
            if (empty($list)) {
                respose(array('code'=>0, 'msg'=>'备份文件不存在，请检查！'));
            }
            $last = end($list);
            if (count($list) !== intval($last[0])) {
                respose(array('code'=>0, 'msg'=>'备份文件可能已经损坏（分卷不完整），请检查！'));
            }
            /*校验sql文件是否属于当前CMS版本*/
            preg_match('/(\d{8,8})-(\d{6,6})-(\d+)-(v\d+\.\d+\.\d+(.*))\.sql/i', $last[1], $matches);
            $version = getCmsVersion();
            if (empty($matches[4]) || $matches[4] != $version) {
                respose(array('code'=>0, 'msg'=>'sql不兼容当前版本：'.$version));
            }
            /*--end*/
            session('backup_list', $list);
            session('backup_total_size', array_sum(array_column($list, 3)));
            session('backup_sqldatapath', tpCache('web.web_sqldatapath'));
            respose(array('code'=>1, 'msg'=>'初始化完成，准备还原第1卷...', 'rate'=>'0%', 'data'=>array('part'=>1, 'offset'=>0)));
        }

        $part   = intval($part);
        $offset = intval($offset);
        $list   = session('backup_list');
        if (empty($list) || !isset($list[$part])) {
            respose(array('code'=>0, 'msg'=>'还原进度已失效，请重新点击恢复！'));
        }
        $item = $list[$part];
        if (empty($item[1]) || !file_exists($item[1])) {
            respose(array('code'=>0, 'msg'=>"第{$part}卷文件不存在，请检查！"));
        }

        $gz = !empty($item[2]);
        $fp = $gz ? @gzopen($item[1], 'r') : @fopen($item[1], 'rb');
        if (false === $fp || null === $fp) {
            respose(array('code'=>0, 'msg'=>"第{$part}卷文件无法读取，请检查目录权限！"));
        }
        if ($offset > 0) {
            $gz ? gzseek($fp, $offset) : fseek($fp, $offset);
        }

        Db::execute("SET SESSION sql_mode = ''"); // 兼容备份数据中枚举字段的空值等宽松数据
        Db::execute("SET FOREIGN_KEY_CHECKS = 0");

        $prefix   = config('database.prefix');
        $deadline = time() + 20; // 每次请求只执行约20秒，避免PHP与网关超时
        $sql      = '';
        $eof      = false;
        $inTrans  = false;
        $batch    = 0;
        Db::execute("START TRANSACTION"); // 批量提交，避免逐条提交导致的极慢写入
        $inTrans = true;
        while (true) {
            $line = $gz ? gzgets($fp) : fgets($fp);
            if (false === $line || '' === $line) {
                $eof = true;
                break;
            }
            $line = trim($line);
            if ('' === $sql && ('' === $line || '#' === substr($line, 0, 1) || '--' === substr($line, 0, 2) || '/*' === substr($line, 0, 2))) {
                $offset = $gz ? gztell($fp) : ftell($fp);
                continue;
            }
            $sql .= $line;
            if (';' !== substr($line, -1)) {
                continue;
            }
            $query = $this->prepareImportSql($sql, $prefix);
            $sql   = '';
            if ('' !== $query) {
                $isDdl = preg_match('/^\s*(DROP|CREATE|ALTER|TRUNCATE|RENAME)\s/i', $query);
                try {
                    if ($isDdl && $inTrans) { // DDL会隐式提交，先显式收尾保持状态一致
                        Db::execute("COMMIT");
                        $inTrans = false;
                        $batch = 0;
                    }
                    Db::execute($query);
                    if (!$inTrans) {
                        Db::execute("START TRANSACTION");
                        $inTrans = true;
                        $batch = 0;
                    } elseif (++$batch >= 1000) {
                        Db::execute("COMMIT");
                        Db::execute("START TRANSACTION");
                        $batch = 0;
                    }
                } catch (\Exception $e) {
                    if ($inTrans) {
                        Db::execute("ROLLBACK");
                    }
                    $gz ? gzclose($fp) : fclose($fp);
                    respose(array('code'=>0, 'msg'=>"第{$part}卷还原出错：".$e->getMessage()));
                }
            }
            $offset = $gz ? gztell($fp) : ftell($fp);
            if (time() >= $deadline) {
                break;
            }
        }
        if ($inTrans) { // 本次请求的数据全部提交后才回报进度，中断可从上次进度安全续跑
            Db::execute("COMMIT");
        }
        $gz ? gzclose($fp) : fclose($fp);

        /*计算整体进度*/
        $finished = 0;
        foreach ($list as $k => $v) {
            if ($k < $part) {
                $finished += $v[3];
            }
        }
        $totalSize = intval(session('backup_total_size'));
        $rate = $totalSize > 0 ? min(100, floor(100 * ($finished + $offset) / $totalSize)) : 0;
        /*--end*/

        if (!$eof) { // 当前卷未读完，继续
            respose(array('code'=>1, 'msg'=>"正在还原第{$part}卷...", 'rate'=>$rate.'%', 'data'=>array('part'=>$part, 'offset'=>$offset)));
        }
        if (isset($list[$part + 1])) { // 进入下一卷
            $next = $part + 1;
            respose(array('code'=>1, 'msg'=>"正在还原第{$next}卷...", 'rate'=>$rate.'%', 'data'=>array('part'=>$next, 'offset'=>0)));
        }

        // 全部分卷还原完毕
        //修改数据库备份目录为原来的目录
        $tpCacheData = ['web_sqldatapath'=>session('backup_sqldatapath'), 'php_atqueryrequest_time'=>0, 'php_atqueryrequest_time2'=>0];
        if (is_language()) {
            $langRow = Db::name('language')->order('id asc')->select();
            foreach ($langRow as $key => $val) {
                tpCache('web', $tpCacheData, $val['mark']);
            }
        } else { // 单语言
            tpCache('web', $tpCacheData);
        }
        session('backup_list', null);
        session('backup_total_size', null);
        session('backup_sqldatapath', null);
        delFile(RUNTIME_PATH); // 清除缓存
        \think\Cache::clear();
        adminLog('还原数据库');
        verify_authortoken();
        respose(array('code'=>1, 'msg'=>'还原完成！', 'rate'=>'100%', 'data'=>array('done'=>1)));
    }

    /**
     * 获取某次备份的分卷文件列表，键为卷号，值为 [卷号, 完整路径, 是否压缩, 文件大小]
     * @param int $time 备份时间戳
     * @return array
     */
    private function getBackupVolumes($time)
    {
        $name = date('Ymd-His', $time) . '-*.sql*';
        $path = tpCache('global.web_sqldatapath');
        $path = !empty($path) ? $path : config('DATA_BACKUP_PATH');
        $path = trim($path, '/');
        $path = realpath($path) . DS . $name;
        $files = glob($path);
        $list  = array();
        if (is_array($files)) {
            foreach ($files as $filename) {
                $basename = basename($filename);
                $match = sscanf($basename, '%4s%2s%2s-%2s%2s%2s-%d-%s');
                if (empty($match[6])) {
                    continue;
                }
                $gz = preg_match('/\.sql\.gz$/i', $basename);
                $list[intval($match[6])] = array(intval($match[6]), $filename, $gz, filesize($filename));
            }
        }
        ksort($list);
        return $list;
    }

    /**
     * 还原前对单条SQL做表前缀与存储引擎的兼容处理
     * @param string $sql
     * @param string $prefix
     * @return string
     */
    private function prepareImportSql($sql, $prefix)
    {
        if ('ey_' != $prefix) {
            $sql = str_replace('`ey_', '`'.$prefix, $sql);
        }
        $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);
        return rtrim(trim($sql), ';');
    }

    /**
     * 下载
     * @param int $time
     */
    public function downFile($time = 0)
    {
        $name  = date('Ymd-His', $time) . '-*.sql*';
        $path = tpCache('global.web_sqldatapath');
        $path = !empty($path) ? $path : config('DATA_BACKUP_PATH');
        $path = trim($path, '/');
        $path  = realpath($path) . DS . $name;
        $files = glob($path);
        if(is_array($files)){
            foreach ($files as $filePath){
                if (!file_exists($filePath)) {
                    $this->error("该文件不存在，可能是被删除");
                }else{
                    $filename = basename($filePath);
                    header("Content-type: application/octet-stream");
                    header('Content-Disposition: attachment; filename="' . $filename . '"');
                    header("Content-Length: " . filesize($filePath));
                    readfile($filePath);
                }
            }
        }
    }

    /**
     * 删除备份文件
     * @param  Integer $time 备份时间
     */
    public function del()
    {
        $time_arr = input('del_id/a');
        $time_arr = eyIntval($time_arr);
        if(is_array($time_arr) && !empty($time_arr)){
            foreach ($time_arr as $key => $val) {
                $name  = date('Ymd-His', $val) . '-*.sql*';
                $path = tpCache('global.web_sqldatapath');
                $path = !empty($path) ? $path : config('DATA_BACKUP_PATH');
                $path = trim($path, '/');
                $path  = realpath($path) . DS . $name;
                array_map("unlink", glob($path));
                if(count(glob($path))){
                    $this->error('备份文件删除失败，请检查目录权限！');
                }
            }
            adminLog('删除数据库备份文件');
            $this->success('删除成功！');
        } else {
            $this->error('参数有误');
        }
    }
}
