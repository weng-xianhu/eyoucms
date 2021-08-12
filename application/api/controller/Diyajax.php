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

namespace app\api\controller;

use think\Db;

class Diyajax extends Base
{
    /*
     * 初始化操作
     */
    public function _initialize() {
        parent::_initialize();
    }

    /**
     * 检验会员登录
     */
    public function check_userinfo()
    {
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        if (IS_AJAX) {
            $users = session('users');
            if (!empty($users)) {
                $users_id = intval($users['users_id']);
                // 头像处理
                $head_pic = get_head_pic(htmlspecialchars_decode($users['head_pic']));
                $users['head_pic'] = func_preg_replace(['http://thirdqq.qlogo.cn'], ['https://thirdqq.qlogo.cn'], $head_pic);
                // 注册时间转换时间日期格式
                $users['reg_time'] = MyDate('Y-m-d H:i:s', $users['reg_time']);
                // 购物车数量
                $users['cart_num'] = Db::name('shop_cart')->where(['users_id'=>$users_id])->sum('product_num');

                $assignData = [
                    'users' => $users,
                ];
                $this->assign($assignData);

                $filename = './template/'.THEME_STYLE_PATH.'/'.'system/users_info.htm';
                if (file_exists($filename)) {
                    $html = $this->fetch($filename); // 渲染模板标签语法
                } else {
                    $html = '缺少模板文件：'.ltrim($filename, '.');
                }

                $data = [
                    'ey_is_login'   => 1,
                    'html'  => $html,
                ];
            }
            else {
                $data = [
                    'ey_is_login'   => 0,
                    'ey_third_party_login'  => $this->is_third_party_login(),
                    'ey_login_vertify'  => $this->is_login_vertify(),
                ];
            }

            $this->success('请求成功', null, $data);
        }
        abort(404);
    }

    /**
     * 是否启用第三方登录
     * @return boolean [description]
     */
    private function is_third_party_login()
    {
        $is_third_party_login = 0;
        if (is_dir('./weapp/QqLogin/') || is_dir('./weapp/WxLogin/') || is_dir('./weapp/Wblogin/')) {
            $result = Db::name('weapp')->field('id')->where([
                    'code'  => ['IN', ['QqLogin','WxLogin','Wblogin']],
                    'status'    => 1,
                ])->select();
            if (!empty($result)) {
                $is_third_party_login = 1;
            }
        }

        return $is_third_party_login;
    }

    /**
     * 是否开启登录图形验证码
     * @return boolean [description]
     */
    private function is_login_vertify()
    {
        // 默认开启验证码
        $is_vertify          = 1;
        $users_login_captcha = config('captcha.users_login');
        if (!function_exists('imagettftext') || empty($users_login_captcha['is_on'])) {
            $is_vertify = 0; // 函数不存在，不符合开启的条件
        }

        return $is_vertify;
    }
}