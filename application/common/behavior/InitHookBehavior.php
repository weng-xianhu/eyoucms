<?php

namespace app\common\behavior;
use think\Db;
use think\Hook;

class InitHookBehavior {
    // 行为扩展的执行入口必须是run
    public function run(&$params){
        $data = cache('hooks');
        $hooks = Db::name('hooks')->field('name,module')->where(array('status'=>1))->cache(true, EYOUCMS_CACHE_TIME, 'hooks')->select();
        if(empty($data) && !empty($hooks)){
            $Prefix = config('database.prefix');
            $dbType = config('database.type');
            $tableName = $Prefix . 'weapp';
            if ($dbType == 'dm') { // 达梦优化
                $exist = \think\Db::query("SELECT table_name FROM all_tables WHERE table_name = ?", [$tableName]);
            } elseif ($dbType == 'kingbase') { // 人大金仓优化
                $exist = \think\Db::query("SELECT tablename FROM pg_tables WHERE schemaname = 'public' AND tablename = ?", [$tableName]);
            } else {
                $database = config('database.database');
                $exist = \think\Db::query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?", [$database, $tableName]);
            }
            if (!empty($exist)) {
                $weappRow = Db::name('weapp')->field('code,status,is_valid')->where(array('status'=>1))->getAllWithIndex('code');
                if (!empty($hooks)) {
                    foreach ($hooks as $key => $val) {
                        $module = $val['module'];
                        if (!empty($module) && isset($weappRow[$module]) && 1 == $weappRow[$module]['is_valid']) {
                            Hook::add($val['name'], get_weapp_class($module));
                        }
                    }
                    cache('hooks', Hook::get());
                }
            }
        }else{
            if (!empty($data)) {
                Hook::import($data, false);
            }
        }
    }
}
