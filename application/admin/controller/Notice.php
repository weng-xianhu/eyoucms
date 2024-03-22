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

namespace app\admin\controller;

use think\Db;
use think\Page;
use think\Cache;

class Notice extends Base
{
    private $notice_applets_tpl = [];
    private $notice_wechat_tpl = [];

    public function _initialize() {
        parent::_initialize();
        $this->notice_applets_tpl = [
            7 => ['id' => 855, 'keywords' => [1, 2, 4, 5, 7]], // 订单发货通知
        ];
        $this->notice_wechat_tpl = [
            9 => ['id' => 'OPENTM417958215'], // 订单支付成功通知
        ];
        $this->assign('admin_id', session('admin_id'));
    }


    // 基础通知 - 买家通知
    public function buyer_notice()
    {
        // 短信消息模板
        $sms_type = tpCache('sms.sms_type');
        $sms_tplist = Db::name('sms_template')->where(['sms_type'=>$sms_type, 'lang'=>$this->admin_lang])->order('send_scene asc')->select();
        $this->assign('sms_tplist', $sms_tplist);

        // 邮箱消息模板
        $smtp_tplist = Db::name('smtp_tpl')->where(['lang'=>$this->admin_lang])->order('send_scene asc')->select();
        $this->assign('smtp_tplist', $smtp_tplist);

        // 微信小程序消息模板
        $applets_tplist = Db::name('applets_template')->where(['lang'=>$this->admin_lang])->order('send_scene asc')->select();
        $this->assign('applets_tplist', $applets_tplist);

        // 微信小程序消息模板
        $wechat_tplist = Db::name('wechat_template')->where(['lang'=>$this->admin_lang])->order('send_scene asc')->select();
        $this->assign('wechat_tplist', $wechat_tplist);

        // 站内信消息模板
        $notice_tplist = Db::name('users_notice_tpl')->where(['lang'=>$this->admin_lang])->order('send_scene asc')->select();
        $this->assign('notice_tplist', $notice_tplist);

        return $this->fetch();
    }

    public function notice_details_bar()
    {
        // 查询发送类型
        $send_type = input('param.send_type/d', 1);
        $this->assign('send_type', $send_type);

        // 查询指定的短信模板是否存在
        $sms_type = tpCache('sms.sms_type');
        $sms_where = [
            'sms_type' => $sms_type,
            'lang' => $this->admin_lang,
            'send_scene' => $this->getSmsTplSendSecneID()
        ];
        $notice_sms_tpl = Db::name('sms_template')->where($sms_where)->count();
        $this->assign('notice_sms_tpl', $notice_sms_tpl);

        // 查询指定的邮箱模板是否存在
        $smtp_where = [
            'lang' => $this->admin_lang,
            'send_scene' => $this->getSmtpTplSendSecneID()
        ];
        $notice_smtp_tpl = Db::name('smtp_tpl')->where($smtp_where)->count();
        $this->assign('notice_smtp_tpl', $notice_smtp_tpl);

        // 查询指定的微信小程序模板是否存在
        $applets_where = [
            'lang' => $this->admin_lang,
            'send_scene' => $send_type
        ];
        $notice_applets_tpl = Db::name('applets_template')->where($applets_where)->count();
        $this->assign('notice_applets_tpl', $notice_applets_tpl);

        // 查询指定的微信公众号模板是否存在
        $applets_where = [
            'lang' => $this->admin_lang,
            'send_scene' => $send_type
        ];
        $notice_wechat_tpl = Db::name('wechat_template')->where($applets_where)->count();
        $this->assign('notice_wechat_tpl', $notice_wechat_tpl);

        // 查询指定的站内信模板是否存在
        $notice_where = [
            'lang' => $this->admin_lang,
            'send_scene' => $this->getNoticeTplSendSecneID()
        ];
        $notice_notice_tpl = Db::name('users_notice_tpl')->where($notice_where)->count();
        $this->assign('notice_notice_tpl', $notice_notice_tpl);

    }

    // 短信通知详情
    public function notice_details_sms()
    {
        if (IS_POST) {
            // 是否填写短信配置
            if (1 == $this->globalConfig['sms_type'] && (empty($this->globalConfig['sms_appkey']) || empty($this->globalConfig['sms_secretkey']))) {
                $this->error("请先完善<font color='red'>[基本信息]-[接口API]-[云短信]</font>配置");
            }
            // 是否填写短信配置
            if (2 == $this->globalConfig['sms_type'] && (empty($this->globalConfig['sms_appid_tx']) || empty($this->globalConfig['sms_appkey_tx']))) {
                $this->error("请先完善<font color='red'>[基本信息]-[接口API]-[云短信]</font>配置");
            }
            $post = input('post.');
            $data = [
                'tpl_content' => filter_line_return($post['tpl_content']),
                'update_time' => getTime(),
            ];
            $data = array_merge($post, $data);
            $r = Db::name('sms_template')->where(['tpl_id'=>intval($post['tpl_id'])])->update($data);
            if ($r !== false) {
                $this->success('操作成功');
            }
            $this->error('操作失败');
        }

        $sms_type = tpCache('sms.sms_type');
        $send_scene = $this->getSmsTplSendSecneID();
        $where = [
            'sms_type' => $sms_type,
            'send_scene' => $send_scene,
            'lang' => $this->admin_lang
        ];
        $info = Db::name('sms_template')->where($where)->find();
        if (empty($info)) $this->error('数据不存在，请联系管理员！');

        $info['tpl_content_demo'] = '【<签名名称>】' . $info['tpl_content'];
        $info['tpl_content_demo'] = str_replace(['${code}','{1}'], '<订单号>', $info['tpl_content_demo']);
        $info['tpl_content_demo'] = str_replace(['${content}','{1}'], '<订单号>', $info['tpl_content_demo']);
        $this->assign('info', $info);

        $this->notice_details_bar();
        return $this->fetch();
    }

    // 邮件通知详情
    public function notice_details_smtp()
    {
        if (IS_POST) {
            $post = input('post.');
            // 是否填写短信配置
            if (empty($post['tpl_title'])) $this->error("请先完善<font color='red'>[基本信息]-[接口API]-[云短信]</font>配置");
            $data = [
                'tpl_title' => filter_line_return($post['tpl_title']),
                'update_time' => getTime(),
            ];
            $data = array_merge($post, $data);
            $r = Db::name('smtp_tpl')->where(['tpl_id'=>intval($post['tpl_id'])])->update($data);
            if ($r !== false) {
                $this->success('操作成功');
            }
            $this->error('操作失败');
        }

        $send_scene = $this->getSmtpTplSendSecneID();
        $where = [
            'send_scene' => $send_scene,
            'lang' => $this->admin_lang
        ];
        $info = Db::name('smtp_tpl')->where($where)->find();
        if (empty($info)) $this->error('数据不存在，请联系管理员！');
        $this->assign('info', $info);

        $this->notice_details_bar();
        return $this->fetch();
    }

    // 微信小程序消息详情
    public function notice_details_applets()
    {
        // 查询指定的微信小程序模板是否存在
        $send_scene = input('param.send_scene/d') ? input('param.send_scene/d') : input('param.send_type/d');
        $where = [
            'lang' => $this->admin_lang,
            'send_scene' => $send_scene
        ];
        $info = Db::name('applets_template')->where($where)->find();
        if (empty($info)) $this->error('数据不存在，请联系管理员！');
        $info['tpl_data'] = !empty($info['tpl_data']) ? json_decode($info['tpl_data'], true) : [];

        if (IS_POST) {
            // 是否填写微信小程序配置
            $wechat_data = tpSetting("OpenMinicode.conf_weixin");
            $wechat_data = !empty($wechat_data) ? json_decode($wechat_data, true) : [];
            if (empty($wechat_data['appid'])) {
                $this->error("请先完善<font color='red'>[基本信息]-[接口API]-[小程序API]-[微信小程序]</font>配置");
            }

            if (empty($info['template_code']) || $this->notice_applets_tpl[$send_scene]['id'] != $info['template_code']) {
                $tokenData = get_weixin_access_token(true);
                if (!empty($tokenData['code'])) {
                    // 添加消息模板
                    $url = "https://api.weixin.qq.com/wxaapi/newtmpl/addtemplate?access_token=".$tokenData['access_token'];
                    $post_data = array(
                        "tid" => $this->notice_applets_tpl[$send_scene]['id'],
                        "kidList" => $this->notice_applets_tpl[$send_scene]['keywords'],
                        "sceneDesc" => !empty($info['tpl_title']) ? $info['tpl_title'] : '用户操作行为通知',
                    );
                    $response = httpRequest($url, 'POST', $post_data);
                    $params = json_decode($response, true);
                    if (!empty($params['priTmplId'])) {
                        $update = [
                            'template_code' => $this->notice_applets_tpl[$send_scene]['id'],
                            'template_id' => $params['priTmplId'],
                        ];
                        $result = Db::name('applets_template')->where(['send_scene'=>$send_scene])->update($update);
                        if (empty($result)) $this->error('保存失败');

                        // 删除旧的消息模板
                        $url = "https://api.weixin.qq.com/wxaapi/newtmpl/deltemplate?access_token=".$tokenData['access_token'];
                        $post_data = array(
                            "priTmplId" => $info['template_id'],
                        );
                        httpRequest($url, 'POST', $post_data);
                    } else {
                        $msg = !empty($params['errmsg']) ? $params['errmsg'] : '保存失败';
                        $this->error($msg);
                    }
                } else {
                    $this->error($tokenData['msg']);
                }
            }

            $post = input('post.');
            $update = [
                'is_open' => intval($post['is_open']),
                'update_time' => getTime(),
            ];
            $result = Db::name('applets_template')->where(['tpl_id'=>intval($post['tpl_id'])])->update($update);
            if (!empty($result)) $this->success('操作成功');
            $this->error('操作失败');
        }

        $this->assign('info', $info);
        $this->notice_details_bar();
        return $this->fetch();
    }

    // 微信公众号消息详情
    public function notice_details_wechat()
    {
        // 查询指定的微信小程序模板是否存在
        $send_scene = input('param.send_scene/d') ? input('param.send_scene/d') : input('param.send_type/d');
        $where = [
            'lang' => $this->admin_lang,
            'send_scene' => $send_scene
        ];
        $info = Db::name('wechat_template')->where($where)->find();
        if (empty($info)) $this->error('数据不存在，请联系管理员！');
        $info['tpl_data'] = !empty($info['tpl_data']) ? json_decode($info['tpl_data'], true) : [];

        if (IS_POST) {
            // 是否填写微信公众号配置
            $wechat_data = tpSetting("OpenMinicode.conf_wechat");
            $wechat_data = !empty($wechat_data) ? json_decode($wechat_data, true) : [];
            if (empty($wechat_data['appid'])) {
                $this->error("请先完善<font color='red'>[基本信息]-[接口API]-[微信公众号]</font>配置");
            }

            if (empty($info['template_code']) || $this->notice_wechat_tpl[$send_scene]['id'] != $info['template_code']) {
                $tokenData = get_wechat_access_token();
                if (!empty($tokenData['code'])) {
                    // 添加消息模板
                    $url = "https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=".$tokenData['access_token'];
                    $post_data = array(
                        'template_id_short' => $this->notice_wechat_tpl[$send_scene]['id'],
                    );
                    $response = httpRequest($url, 'POST', json_encode($post_data, JSON_UNESCAPED_UNICODE));
                    $params = json_decode($response, true);
                    if (!empty($params['template_id'])) {
                        $update = [
                            'template_code' => $this->notice_wechat_tpl[$send_scene]['id'],
                            'template_id' => $params['template_id'],
                        ];
                        $result = Db::name('wechat_template')->where(['send_scene'=>$send_scene])->update($update);
                        if (empty($result)) $this->error('保存失败');

                        // 删除旧的消息模板
                        $url = "https://api.weixin.qq.com/cgi-bin/template/del_private_template?access_token=".$tokenData['access_token'];
                        $post_data = array(
                            "template_id" => $info['template_id'],
                        );
                        httpRequest($url, 'POST', $post_data);
                    } else {
                        $msg = !empty($params['errmsg']) ? $params['errmsg'] : '保存失败';
                        $this->error($msg);
                    }
                } else {
                    $this->error($tokenData['msg']);
                }
            }

            $post = input('post.');
            $update = [
                'is_open' => intval($post['is_open']),
                'update_time' => getTime(),
            ];
            $result = Db::name('wechat_template')->where(['tpl_id'=>intval($post['tpl_id'])])->update($update);
            if (!empty($result)) $this->success('操作成功');
            $this->error('操作失败');
        }

        $this->assign('info', $info);
        $this->notice_details_bar();
        return $this->fetch();
    }

    public function notice_details_notice()
    {
        if (IS_POST) {
            $post = input('post.');
            // 是否填写短信配置
            if (empty($post['tpl_title'])) $this->error("站内信标题不能为空！");
            $data = [
                'tpl_title' => filter_line_return($post['tpl_title']),
                'update_time' => getTime(),
            ];
            $data = array_merge($post, $data);
            $r = Db::name('users_notice_tpl')->where(['tpl_id'=>intval($post['tpl_id'])])->update($data);
            if ($r !== false) {
                $this->success('操作成功');
            }
            $this->error('操作失败');
        }

        $send_scene = $this->getNoticeTplSendSecneID();
        $where = [
            'send_scene' => $send_scene,
            'lang' => $this->admin_lang
        ];
        $info = Db::name('users_notice_tpl')->where($where)->find();
        if (empty($info)) $this->error('数据不存在，请联系管理员！');
        $this->assign('info', $info);

        $this->notice_details_bar();
        return $this->fetch();
    }

    // 获取短信模板发送场景ID
    private function getSmsTplSendSecneID()
    {
        // 查询发送类型
        $send_scene = -1;
        $send_type = input('param.send_type/d', 1);
        switch ($send_type) {
            // 账号注册
            case '1':
                $send_scene = 0;
                break;
            // 账号登录
            case '2':
                $send_scene = 2;
                break;
            // 手机绑定
            case '4':
                $send_scene = 1;
                break;
            // 找回密码
            case '5':
                $send_scene = 4;
                break;
            // 留言验证
            case '6':
                $send_scene = 7;
                break;
            // 订单发货
            case '7':
                $send_scene = 6;
                break;
            // 留言表单
            case '8':
                $send_scene = 11;
                break;
            // 订单付款
            case '9':
                $send_scene = 5;
                break;
        }

        return $send_scene;
    }

    // 获取邮件模板发送场景ID
    private function getSmtpTplSendSecneID()
    {
        // 查询发送类型
        $send_scene = -1;
        $send_type = input('param.send_type/d', 1);
        switch ($send_type) {
            // 账号注册
            case '1':
                $send_scene = 2;
                break;
            // 邮箱绑定
            case '3':
                $send_scene = 3;
                break;
            // 找回密码
            case '5':
                $send_scene = 4;
                break;
            // 订单发货
            case '7':
                $send_scene = 6;
                break;
            // 留言表单
            case '8':
                $send_scene = 1;
                break;
            // 订单付款
            case '9':
                $send_scene = 5;
                break;
        }

        return $send_scene;
    }

    // 获取站内信模板发送场景ID
    private function getNoticeTplSendSecneID()
    {
        // 查询发送类型
        $send_scene = -1;
        $send_type = input('param.send_type/d', 1);
        switch ($send_type) {
            // 订单发货
            case '7':
                $send_scene = 6;
                break;
            // 留言表单
            case '8':
                $send_scene = 1;
                break;
            // 订单付款
            case '9':
                $send_scene = 5;
                break;
        }

        return $send_scene;
    }

}