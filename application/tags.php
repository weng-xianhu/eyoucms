<?php

// 应用行为扩展定义文件

$weappRow = \think\Db::name('weapp')->field('code')->where([
    'status'    => 1,
])->cache(true, null, "weapp")->select();

$app_init = [
    'app\\common\\behavior\\AppInitBehavior',
];
$app_begin = [];
$module_init = [];
$action_begin = [];
$view_filter = [];
$log_write = [];
$app_end = [];
foreach ($weappRow as $key => $val) {
    /*引入全部插件的app_init行为*/
    $file = WEAPP_DIR_NAME.DS.$val['code'].DS.'behavior'.DS.'AppInitBehavior.php';
    if (file_exists($file)) {
        $fileStr = 'weapp\\'.$val['code'].'\\behavior\\AppInitBehavior';
        array_push($app_init, $fileStr);
    }

    /*引入全部插件的app_begin行为*/
    $file = WEAPP_DIR_NAME.DS.$val['code'].DS.'behavior'.DS.'AppBeginBehavior.php';
    if (file_exists($file)) {
        $fileStr = 'weapp\\'.$val['code'].'\\behavior\\AppBeginBehavior';
        array_push($app_begin, $fileStr);
    }

    /*引入全部插件的module_init行为*/
    $file = WEAPP_DIR_NAME.DS.$val['code'].DS.'behavior'.DS.'ModuleInitBehavior.php';
    if (file_exists($file)) {
        $fileStr = 'weapp\\'.$val['code'].'\\behavior\\ModuleInitBehavior';
        array_push($module_init, $fileStr);
    }
    
    /*引入全部插件的action_begin行为*/
    $file = WEAPP_DIR_NAME.DS.$val['code'].DS.'behavior'.DS.'ActionBeginBehavior.php';
    if (file_exists($file)) {
        $fileStr = 'weapp\\'.$val['code'].'\\behavior\\ActionBeginBehavior';
        array_push($action_begin, $fileStr);
    }

    /*引入全部插件的view_filter行为*/
    $file = WEAPP_DIR_NAME.DS.$val['code'].DS.'behavior'.DS.'ViewFilterBehavior.php';
    if (file_exists($file)) {
        $fileStr = 'weapp\\'.$val['code'].'\\behavior\\ViewFilterBehavior';
        array_push($view_filter, $fileStr);
    }

    /*引入全部插件的log_write行为*/
    $file = WEAPP_DIR_NAME.DS.$val['code'].DS.'behavior'.DS.'LogWriteBehavior.php';
    if (file_exists($file)) {
        $fileStr = 'weapp\\'.$val['code'].'\\behavior\\LogWriteBehavior';
        array_push($log_write, $fileStr);
    }
    
    /*引入全部插件的app_end行为*/
    $file = WEAPP_DIR_NAME.DS.$val['code'].DS.'behavior'.DS.'AppEndBehavior.php';
    if (file_exists($file)) {
        $fileStr = 'weapp\\'.$val['code'].'\\behavior\\AppEndBehavior';
        array_push($app_end, $fileStr);
    }
}

return array(
    // 应用初始化
    'app_init'     => $app_init,
    // 应用开始
    'app_begin'    => $app_begin,
    // 模块初始化
    'module_init'  => $module_init,
    // 操作开始执行
    'action_begin' => $action_begin,
    // 视图内容过滤
    'view_filter'  => $view_filter,
    // 日志写入
    'log_write'    => $log_write,
    // 应用结束
    'app_end'      => $app_end,
);
