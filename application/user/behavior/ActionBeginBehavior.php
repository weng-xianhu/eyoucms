<?php

namespace app\user\behavior;

/**
 * 系统行为扩展：新增/更新/删除之后的后置操作
 */
class ActionBeginBehavior {
    protected static $actionName;
    protected static $controllerName;
    protected static $moduleName;
    protected static $method;

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
        self::$method = request()->method();
        $this->_initialize();
    }

    private function _initialize() {
        if ('GET' == self::$method) {
            $this->reg_origin();
        } else if ('POST' == self::$method) {
            $this->logout_origin();
        }
    }

    /**
     * 标记来自不同项目的注册会员渠道
     * @access private
     */
    private function reg_origin()
    {
        /*特定场景专用*/
        $opencodetype = config('global.opencodetype');
        if (1 == $opencodetype) {
            if (self::$controllerName == 'Users' && self::$actionName == 'login') {
                $origin_type = 0;
                $origin_mid = 0;
                $origin_query = input('param.origin_query/s');
                if (!empty($origin_query)) {
                    $origin_query = mchStrCode($origin_query, 'DECODE', '#$eyoucms%^');
                    $origin_arr = explode('|', $origin_query);
                    $origin_type = !empty($origin_arr[0]) ? intval($origin_arr[0]) : 0;
                    $origin_mid = !empty($origin_arr[1]) ? intval($origin_arr[1]) : 0;
                }

                if (!empty($origin_type)) {
                    cookie('origin_type', $origin_type);
                    cookie('origin_mid', $origin_mid);
                } else {
                    cookie('origin_type', null);
                    cookie('origin_mid', null);
                }
            }
        }
    }

    /**
     * 退出清除的注册会员渠道标记
     * @access private
     */
    private function logout_origin()
    {
        /*特定场景专用*/
        $opencodetype = config('global.opencodetype');
        if (1 == $opencodetype) {
            if (self::$controllerName == 'Users' && self::$actionName == 'logout') {
                cookie('origin_type', null);
                cookie('origin_mid', null);
            }
        }
    }
}
