<?php

namespace think;

// 分布式文件存储类
class Storage {

    /**
     * 操作句柄
     * @var string
     * @access protected
     */
    static protected $handler    ;

    /**
     * 连接分布式文件系统
     * @access public
     * @param string $type 文件类型
     * @param array $options  配置数组
     * @return void
     */
    static public function connect($type='File',$options=array()) {
        $class  =   'think\\storage\\driver\\'.ucwords($type);
        self::$handler = new $class($options);
    }

    static public function __callstatic($method,$args){
        //调用缓存驱动的方法
        if(method_exists(self::$handler, $method)){
           return call_user_func_array(array(self::$handler,$method), $args);
        }
    }
}
