<?php

namespace think\process\bhvcore;

/**
 * 系统行为扩展：校验
 */
class BhvadminAEnd {
    protected static $actionName;
    protected static $controllerName;
    protected static $moduleName;

    /**
     * 构造方法
     * @param Request $request Request对象
     * @access public
     */
    public function __construct()
    {

    }

    // 行为扩展的执行入口必须是run
    public function run(&$params){
        self::$actionName = request()->action();
        self::$controllerName = request()->controller();
        self::$moduleName = request()->module();
        if ('GET' == request()->method()) {
            // 初始化
            $this->_initialize();
        }
    }

    protected function _initialize() {
        $this->corecall();
    }

    /**
     * 验证葛优瘫
     * @access protected
     */
    protected function corecall() {
        $tmp = 'I3NlcnZpY2VfZXlfdG9rZW4j';
        $token = base64_decode($tmp);
        $token = trim($token, '#');
        $tokenStr = \think\Config::get($token);

        $tmp = 'I3NlcnZpY2VfZXkj';
        $keys = base64_decode($tmp);
        $keys = trim($keys, '#');
        $md5Str = md5('~'.base64_decode(\think\Config::get($keys)).'~');

        if ($tokenStr != $md5Str) {
            $tmp = 'I+aguOW/g+eoi+W6j+iiq+evoeaUue+8jOivt+WwveW/q+i/mOWOn++8jOaEn+iwouS6q+eUqOW8gOa6kEV5b3VDbXPkvIHkuJrlu7rnq5nns7vnu58uIw==';
            $msg = base64_decode($tmp);
            $msg = trim($msg, '#');
            die($msg);
        }

        return false;
    }
}
