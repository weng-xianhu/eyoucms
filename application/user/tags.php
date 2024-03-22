<?php

// 应用行为扩展定义文件
return array(
    // 模块初始化
    'module_init'  => array(
        'app\\user\\behavior\\ModuleInitBehavior',
    ),
    // 操作开始执行
    'action_begin' => array(
        'app\\user\\behavior\\ActionBeginBehavior',
    ),
    // 视图内容过滤
    'view_filter'  => array(
        'app\\user\\behavior\\ViewFilterBehavior',
    ),
    // 日志写入
    'log_write'    => array(),
    // 应用结束
    'app_end'      => array(

    ),
);
