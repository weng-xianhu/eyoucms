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
            $exist = \think\Db::query('SHOW TABLES LIKE \''.config('database.prefix').'weapp\'');
            if (!empty($exist)) {
                $weappRow = Db::name('weapp')->field('code,status')->where(array('status'=>1))->getAllWithIndex('code');
                if (!empty($hooks)) {
                    foreach ($hooks as $key => $val) {
                        $module = $val['module'];
                        if (isset($weappRow[$module]) && !empty($module)) {
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
