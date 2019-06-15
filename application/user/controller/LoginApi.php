<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 陈风任 <491085389@qq.com>
 * Date: 2019-2-25
 */

namespace app\user\controller;

use think\Db;
// use think\Session;
use think\Config;

class LoginApi extends Base
{
    public $oauth;

    public function _initialize() {
        parent::_initialize();
        session('?users_id');
        $this->oauth = input('param.oauth/s');
        if (!$this->oauth) {
            $this->error('非法操作', url('user/Users/login'));
        }
    }

    public function login(){
        $this->error('该功能尚未开放', url('user/Users/login'));
    }

    public function callback()
    {

    }
}