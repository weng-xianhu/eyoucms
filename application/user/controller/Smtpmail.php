<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海口快推科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */
namespace app\user\controller;

use think\Config;
use app\user\logic\SmtpmailLogic;

// 用于邮箱验证
class Smtpmail extends Base
{
    public $smtpmailLogic;

    /**
     * 构造方法
     */
    public function __construct(){
        parent::__construct();
        $this->smtpmailLogic = new SmtpmailLogic;
    }

    /**
     * 发送邮件
     */
    public function send_email($email = '', $title = '', $type = 'reg', $scene = 2, $data = [])
    {
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        // 超时后，断掉邮件发送
        function_exists('set_time_limit') && set_time_limit(5);
        $data = !empty($data) && !is_array($data) ? json_decode(htmlspecialchars_decode(htmlspecialchars_decode($data)), true) : $data;
        $data = $this->smtpmailLogic->send_email($email, $title, $type, $scene, $data);
        if (1 == $data['code']) {
            $this->success($data['msg']);
        } else {
            $this->error($data['msg']);
        }
    }
}