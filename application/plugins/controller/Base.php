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

namespace app\plugins\controller;
use app\common\controller\Common;
use think\Db;

class Base extends Common {

    /**
     * 析构函数
     */
    function __construct() 
    {
        parent::__construct();
    }
    
    /*
     * 初始化操作
     */
    public function _initialize() 
    {
        parent::_initialize();
        $users_id = session('users_id');
        $users_id = intval($users_id);
        if (!empty($users_id)) {
            $users = GetUsersLatestData($users_id);
            if (empty($users)) {
                session('users_id', null);
                session('users', null);
                cookie('users_id', null);
            } else {
                if (0 > $users['is_lock'] && IS_POST) {
                    $users_lock_model = config('global.users_lock_model');
                    $users_lock_model_msg = !empty($users_lock_model[$users['is_lock']]) ? $users_lock_model[$users['is_lock']]['msg'] : '';
                    $this->error($users_lock_model_msg);
                }
            }
            $this->users = $users;
            $this->users_id = $users['users_id'];
        }
    }
}