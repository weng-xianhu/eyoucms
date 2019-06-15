<?php

// 应用行为扩展定义文件

/*引入全部插件的app_init行为*/
$app_init = [
    'app\\common\\behavior\\AppInitBehavior',
];
$files = glob(WEAPP_DIR_NAME.DS.'*'.DS.'behavior'.DS.'AppInitBehavior.php');
if (!empty($files)) {
    foreach ($files as $key => $file) {
        if (is_file($file) && file_exists($file)) {
            $fileStr = str_replace('/', '\\', $file);
            $fileStr = str_replace('.php', '', $fileStr);
            array_push($app_init, $fileStr);
        }
    }
}
/*--end*/

/*引入全部插件的app_begin行为*/
$app_begin = [];
$files = glob(WEAPP_DIR_NAME.DS.'*'.DS.'behavior'.DS.'AppBeginBehavior.php');
if (!empty($files)) {
    foreach ($files as $key => $file) {
        if (is_file($file) && file_exists($file)) {
            $fileStr = str_replace('/', '\\', $file);
            $fileStr = str_replace('.php', '', $fileStr);
            array_push($app_begin, $fileStr);
        }
    }
}
/*--end*/

/*引入全部插件的app_begin行为*/
$module_init = [];
$files = glob(WEAPP_DIR_NAME.DS.'*'.DS.'behavior'.DS.'ModuleInitBehavior.php');
if (!empty($files)) {
    foreach ($files as $key => $file) {
        if (is_file($file) && file_exists($file)) {
            $fileStr = str_replace('/', '\\', $file);
            $fileStr = str_replace('.php', '', $fileStr);
            array_push($module_init, $fileStr);
        }
    }
}
/*--end*/

/*引入全部插件的action_begin行为*/
$action_begin = [];
$files = glob(WEAPP_DIR_NAME.DS.'*'.DS.'behavior'.DS.'ActionBeginBehavior.php');
if (!empty($files)) {
    foreach ($files as $key => $file) {
        if (is_file($file) && file_exists($file)) {
            $fileStr = str_replace('/', '\\', $file);
            $fileStr = str_replace('.php', '', $fileStr);
            array_push($action_begin, $fileStr);
        }
    }
}
/*--end*/

/*引入全部插件的view_filter行为*/
$view_filter = [];
$files = glob(WEAPP_DIR_NAME.DS.'*'.DS.'behavior'.DS.'ViewFilterBehavior.php');
if (!empty($files)) {
    foreach ($files as $key => $file) {
        if (is_file($file) && file_exists($file)) {
            $fileStr = str_replace('/', '\\', $file);
            $fileStr = str_replace('.php', '', $fileStr);
            array_push($view_filter, $fileStr);
        }
    }
}
/*--end*/

/*引入全部插件的log_write行为*/
$log_write = [];
$files = glob(WEAPP_DIR_NAME.DS.'*'.DS.'behavior'.DS.'LogWriteBehavior.php');
if (!empty($files)) {
    foreach ($files as $key => $file) {
        if (is_file($file) && file_exists($file)) {
            $fileStr = str_replace('/', '\\', $file);
            $fileStr = str_replace('.php', '', $fileStr);
            array_push($log_write, $fileStr);
        }
    }
}
/*--end*/

/*引入全部插件的app_end行为*/
$app_end = [];
$files = glob(WEAPP_DIR_NAME.DS.'*'.DS.'behavior'.DS.'AppEndBehavior.php');
if (!empty($files)) {
    foreach ($files as $key => $file) {
        if (is_file($file) && file_exists($file)) {
            $fileStr = str_replace('/', '\\', $file);
            $fileStr = str_replace('.php', '', $fileStr);
            array_push($app_end, $fileStr);
        }
    }
}
/*--end*/

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
