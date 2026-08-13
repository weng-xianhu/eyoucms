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

namespace app\user\logic;

use think\Model;
use think\Db;
use think\Request;
use think\Config;

/**
 * 邮箱逻辑定义
 * Class CatsLogic
 * @package user\Logic
 */
class SmtpmailLogic extends Model
{
    private $home_lang = 'cn';

    /**
     * 初始化操作
     */
    public function initialize() {
        parent::initialize();
        $this->home_lang = get_home_lang();
    }

    /**
     * 生成美化的HTML邮件模板
     * @param string $title 邮件标题
     * @param string $content 邮件内容
     * @param string $type 邮件类型
     * @param array $extra 额外信息
     * @return string
     */
    private function buildEmailTemplate($title, $content, $type = 'default', $extra = [])
    {
        $web_name = tpCache('web.web_name') ?: '网站';
        $year = date('Y');
        $source = isMobile() ? '移动端' : '电脑端';

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background-color:#f5f5f5;font-family:'Microsoft YaHei',Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f5f5;padding:30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);">
                    <!-- 头部 -->
                    <tr>
                        <td style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:30px;border-radius:8px 8px 0 0;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:24px;font-weight:normal;">{$title}</h1>
                        </td>
                    </tr>
                    <!-- 内容 -->
                    <tr>
                        <td style="padding:40px 30px;">
                            <div style="color:#333333;font-size:14px;line-height:1.8;">
                                {$content}
                            </div>
                        </td>
                    </tr>
                    <!-- 底部 -->
                    <tr>
                        <td style="background-color:#f8f9fa;padding:20px 30px;border-radius:0 0 8px 8px;border-top:1px solid #eee;">
                            <p style="margin:0;color:#999999;font-size:12px;text-align:center;">
                                来源：{$source} | {$web_name}<br>
                                &copy; {$year} {$web_name} 版权所有
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    /**
     * 发送邮件
     */
    public function send_email($email = '', $title = '', $type = 'reg', $scene = 2, $data = [])
    {
        // 是否传入邮箱地址
        $email = trim($email);
        if (empty($email)) {
            return ['code'=>0, 'msg'=>"邮箱地址参数不能为空！"];
        } else {
            $email_arr = explode(',', $email);
            if (1 == count($email_arr)) {
                if (!check_email($email)) {
                    return ['code'=>0, 'msg'=>"邮箱格式不正确！"];
                }
            } else {
                foreach ($email_arr as $key => $val) {
                    $val = trim($val);
                    if (!check_email($val) || empty($val)) {
                        unset($email_arr[$key]);
                    }
                }
                if (empty($email_arr)) {
                    return ['code'=>0, 'msg'=>"邮箱格式不正确！"];
                }
                $email = implode(',', $email_arr);
            }
        }

        // 查询扩展是否开启
        $openssl_funcs = get_extension_funcs('openssl');
        if (!$openssl_funcs) {
            return ['code'=>0, 'msg'=>"请联系空间商，开启php的 <font color='red'>openssl</font> 扩展！"];
        }

        // 是否填写邮件配置
        $smtp_config = tpCache('smtp');
        if (empty($smtp_config['smtp_user']) || empty($smtp_config['smtp_pwd'])) {
            return ['code'=>0, 'msg'=>"该功能待开放，网站管理员尚未完善邮件配置！"];
        }

        // 邮件使用场景
        $scene = intval($scene);
        $send_email_scene = config('send_email_scene');
        $send_scene = $send_email_scene[$scene]['scene'];

        // 获取邮件模板
        $emailtemp = M('smtp_tpl')->where(['send_scene' => $send_scene, 'lang' => $this->home_lang])->find();

        // 是否启用邮件模板
        if (empty($emailtemp) || empty($emailtemp['is_open'])) {
            return ['code'=>0, 'msg'=>"该功能待开放，网站管理员尚未启用(<font color='red'>{$emailtemp['tpl_name']}</font>)邮件模板"];
        }

        // 会员ID
        $users_id = session('users_id');

        // 找回密码
        if ('retrieve_password' == $type) {
            // 判断邮箱是否存在
            $where = array(
                'info'     => array('eq',$email),
                'lang'     => array('eq',$this->home_lang),
            );
            $users_list = M('users_list')->where($where)->field('users_id,info')->find();
            // 判断会员是否已绑定邮箱
            $userswhere = array(
                'email'    => array('eq',$email),
                'lang'     => array('eq',$this->home_lang),
            );
            $usersdata = M('users')->where($userswhere)->field('is_email,is_activation')->find();
            if (!empty($usersdata)) {
                if (empty($usersdata['is_activation'])) {
                    return ['code'=>0, 'msg'=>'该会员尚未激活，不能找回密码！'];
                } else if (empty($usersdata['is_email'])) {
                    return ['code'=>0, 'msg'=>'邮箱地址未绑定，不能找回密码！'];
                }
            }
            if (!empty($users_list)) {
                $time = getTime();
                // 数据添加
                $datas['source']   = 4; // 来源，与场景ID对应：4=找回密码
                $datas['email']    = $email;
                $datas['users_id']    = $users_list['users_id'];
                $datas['code']     = rand(1000,9999);
                $datas['lang']     = $this->home_lang;
                $datas['add_time'] = $time;
                M('smtp_record')->add($datas);
            } else {
                return ['code'=>0, 'msg'=>'邮箱地址不存在！'];
            }
        }
        // 邮箱绑定
        else if ('bind_email' == $type) {
            // 判断邮箱是否已存在
            $listwhere = array(
                'info'     => array('eq',$email),
                'users_id' => array('neq',$users_id),
                'lang'     => array('eq',$this->home_lang),
            );
            $users_list = M('users_list')->where($listwhere)->field('info')->find();
            // 判断会员是否已绑定相同邮箱
            $userswhere = array(
                'users_id' => array('eq',$users_id),
                'email'    => array('eq',$email),
                'is_email' => 1,
                'lang'     => array('eq',$this->home_lang),
            );
            $usersdata = M('users')->where($userswhere)->field('is_email')->find();
            if (!empty($usersdata['is_email'])) {
                return ['code'=>0, 'msg'=>'邮箱已绑定，无需重新绑定！'];
            }
            // 邮箱数据处理
            if (empty($users_list)) {
                $time = getTime();
                // 数据添加
                $datas['source']   = 3; // 来源，与场景ID对应：3=绑定邮箱
                $datas['email']    = $email;
                $datas['users_id'] = $users_id;
                $datas['code']     = rand(1000,9999);
                $datas['lang']     = $this->home_lang;
                $datas['add_time'] = $time;
                M('smtp_record')->add($datas);
            } else {
                return ['code'=>0, 'msg'=>"邮箱已经存在，不可以绑定！"];
            }
        }
        // 会员注册
        else if ('reg' == $type) {
            // 判断邮箱是否已存在
            $where = array(
                'info' => array('eq',$email),
                'lang' => array('eq',$this->home_lang),
            );
            $users_list = M('users_list')->where($where)->field('info')->find();
            if (empty($users_list)) {
                $time = getTime();
                // 数据添加
                $datas['source']   = 2; // 来源，与场景ID对应：2=注册
                $datas['email']    = $email;
                $datas['code']     = rand(1000,9999);
                $datas['lang']     = $this->home_lang;
                $datas['add_time'] = $time;
                M('smtp_record')->add($datas);
            } else {
                return ['code'=>0, 'msg'=>'邮箱已存在！'];
            }
        }
        // 订单(支付、发货)提醒
        else if ('order_msg' == $type) {
            $content = '订单有新的消息，请登录查看。';
            if (!empty($data)) {
                $PayMethod = '';
                if (!empty($data['pay_method'])) {
                    switch ($data['pay_method']) {
                        case 'balance':
                            $PayMethod = '余额支付';
                            break;
                        case 'delivery_pay':
                            $PayMethod = '货到付款';
                            break;
                        case 'wechat':
                            $PayMethod = '微信';
                            break;
                        case 'alipay':
                            $PayMethod = '支付宝';
                            break;
                        default:
                            $PayMethod = '第三方支付';
                            break;
                    }
                }
                switch ($data['type']) {
                    case '1':
                        // 订单付款提醒 - 发给管理员
                        $order_data = Db::name('shop_order')->where('order_code',$data['order_code'])->field('order_id,order_total_amount')->find();
                        $order_detail = Db::name('shop_order_details')->where('order_id',$order_data['order_id'])->select();
                        $products_html = '';
                        foreach ($order_detail as $k => $v) {
                            $products_html .= '<tr><td style="padding:10px;border-bottom:1px solid #eee;">'.$v['product_name'].'</td>';
                            $products_html .= '<td style="padding:10px;border-bottom:1px solid #eee;text-align:center;">￥'.floatval($v['product_price']).'</td>';
                            $products_html .= '<td style="padding:10px;border-bottom:1px solid #eee;text-align:center;">'.$v['num'].'</td>';
                            $products_html .= '<td style="padding:10px;border-bottom:1px solid #eee;text-align:right;">￥'.floatval($v['product_price']*$v['num']).'</td></tr>';
                        }
                        $content = '<p style="margin-bottom:20px;">您好，管理员：</p>';
                        $content .= '<p>会员 <strong style="color:#667eea;">'.$data['nickname'].'</strong> 使用 <strong>'.$PayMethod.'</strong> 完成了订单支付，请及时处理发货。</p>';
                        $content .= '<div style="background:#f8f9fa;border-radius:6px;padding:15px;margin:20px 0;">';
                        $content .= '<p style="margin:0 0 10px;"><strong>订单编号：</strong>'.$data['order_code'].'</p>';
                        $content .= '<table width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;">';
                        $content .= '<tr style="background:#eee;"><th style="padding:10px;text-align:left;">商品名称</th><th style="padding:10px;text-align:center;">单价</th><th style="padding:10px;text-align:center;">数量</th><th style="padding:10px;text-align:right;">小计</th></tr>';
                        $content .= $products_html;
                        $content .= '</table>';
                        $content .= '<p style="margin:15px 0 0;text-align:right;font-size:16px;"><strong>订单总额：<span style="color:#e74c3c;">￥'.floatval($order_data['order_total_amount']).'</span></strong></p>';
                        $content .= '</div>';
                        break;
                    case '2':
                        // 订单发货提醒 - 发给用户
                        $url = request()->domain() . url('user/Shop/shop_order_details', ['order_id'=>$data['order_id']]);
                        $content = '<p style="margin-bottom:20px;">您好，'.$data['nickname'].'：</p>';
                        $content .= '<p>您的订单 <strong style="color:#667eea;">'.$data['order_code'].'</strong> 已发货，请注意查收。</p>';
                        $content .= '<div style="text-align:center;margin:30px 0;">';
                        $content .= '<a href="'.$url.'" style="display:inline-block;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:12px 30px;border-radius:25px;text-decoration:none;font-weight:bold;">查看订单详情</a>';
                        $content .= '</div>';
                        break;
                }
            }
        }
        // 会员投稿提醒
        else if ('usersRelease' == $type) {
            $content = '<p style="margin-bottom:20px;">您好，管理员：</p>';
            $content .= '<p>您有新的投稿文档，请及时审核处理。</p>';
            if (!empty($data)) {
                $content .= '<div style="background:#f8f9fa;border-radius:6px;padding:20px;margin:20px 0;">';
                $content .= '<p style="margin:0 0 10px;"><strong>文档标题：</strong>'.$data['title'].'</p>';
                $content .= '<p style="margin:0 0 10px;"><strong>投稿时间：</strong>'.$data['add_time'].'</p>';
                $content .= '<p style="margin:0 0 10px;"><strong>审核状态：</strong>'.$data['arcrank'].'</p>';
                $content .= '<p style="margin:0;"><strong>文档内容：</strong></p>';
                $content .= '<div style="background:#fff;padding:15px;border-radius:4px;margin-top:10px;border:1px solid #eee;">'.$data['content'].'</div>';
                $content .= '</div>';
            }
        }

        // 生成邮件内容
        $email_title = $emailtemp['tpl_name'];

        // 验证码类邮件（注册、绑定邮箱、找回密码）
        if (empty($content) && !empty($datas['code'])) {
            $type_text = '';
            switch ($type) {
                case 'reg':
                    $type_text = '会员注册';
                    break;
                case 'bind_email':
                    $type_text = '绑定邮箱';
                    break;
                case 'retrieve_password':
                    $type_text = '找回密码';
                    break;
                default:
                    $type_text = '验证';
            }
            $content = '<p style="margin-bottom:20px;">您好：</p>';
            $content .= '<p>您正在进行 <strong>'.$type_text.'</strong> 操作，验证码如下：</p>';
            $content .= '<div style="text-align:center;margin:30px 0;">';
            $content .= '<div style="display:inline-block;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;font-size:32px;font-weight:bold;letter-spacing:8px;padding:15px 40px;border-radius:8px;">'.$datas['code'].'</div>';
            $content .= '</div>';
            $content .= '<p style="color:#999;font-size:12px;">验证码有效期为10分钟，请勿将验证码告知他人。</p>';
            $content .= '<p style="color:#999;font-size:12px;">如非本人操作，请忽略此邮件。</p>';
        }

        // 使用美化模板
        $html = $this->buildEmailTemplate($email_title, $content, $type);

        // 实例化类库，调用发送邮件
        $res = send_email($email, $emailtemp['tpl_title'], $html, $send_scene);
        if (intval($res['code']) == 1) {
            return ['code'=>1, 'msg'=>$res['msg']];
        } else {
            return ['code'=>0, 'msg'=>$res['msg']];
        }
    }
}
