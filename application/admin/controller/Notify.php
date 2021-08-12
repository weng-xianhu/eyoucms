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
 * Date: 2021-02-22
 */

namespace app\admin\controller;

use think\Db;
use think\Page;

class Notify extends Base {

    /**
     * 构造方法
     */
    public function __construct() {
        parent::__construct();
        // 邮件通知配置
        $this->smtp_tpl_db      = Db::name('smtp_tpl');
        // 短信通知配置
        $this->sms_template_db  = Db::name('sms_template');
        // 站内信配置
        $this->users_notice_tpl_db = Db::name('users_notice_tpl');
        // 站内信通知记录表
        $this->users_notice_tpl_content_db = Db::name('users_notice_tpl_content');
    }

    /**
     * 站内信模板列表
     */
    public function notify_tpl()
    {
        $list = array();
        $keywords = input('keywords/s');

        $map = array();
        if (!empty($keywords)) {
            $map['tpl_name'] = array('LIKE', "%{$keywords}%");
        }

        // 多语言
        $map['lang'] = array('eq', $this->admin_lang);

        $count = $this->users_notice_tpl_db->where($map)->count('tpl_id');// 查询满足要求的总记录数
        $pageObj = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $this->users_notice_tpl_db->where($map)
            ->order('tpl_id asc')
            ->limit($pageObj->firstRow.','.$pageObj->listRows)
            ->select();
        $pageStr = $pageObj->show(); // 分页显示输出
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $pageStr); // 赋值分页输出
        $this->assign('pager', $pageObj); // 赋值分页对象

        $shop_open = getUsersConfigData('shop.shop_open');
        $this->assign('shop_open', $shop_open);

        return $this->fetch();
    }

    // 统计未读的站内信数量
    public function count_unread_notify()
    {
        $notice_where = [
            'is_read' => 0,
            'admin_id' => ['>', 0],
        ];
        $notice_count = $this->users_notice_tpl_content_db->where($notice_where)->count('content_id');
        if (IS_AJAX_POST) {
            $this->success('查询成功', null, ['notice_count'=>$notice_count]);
        } else {
            $this->assign('notice_count', $notice_count);
        }
    }

    // 通知首页
    /*public function index()
    {
        // 公共查询条件
        $where = [
            'lang' => $this->admin_lang
        ];

        // 查询邮件配置
        $Smtp = $this->smtp_tpl_db->field('tpl_id, is_open, tpl_name')->order('send_scene asc')->where($where)->select();

        // 查询站内信配置
        $Notice = $this->users_notice_tpl_db->field('tpl_id, is_open, tpl_name')->order('send_scene asc')->where($where)->select();

        // 查询短信配置
        $sms_type = tpCache('sms.sms_type') ? tpCache('sms.sms_type') : 1;
        $where['sms_type'] = $sms_type;
        $Sms = $this->sms_template_db->field('tpl_id, is_open, tpl_title')->order('send_scene asc')->where($where)->select();

        // 拼装数据
        $NotifyList = [
            [
                // 功能名称
                'notify_title' => '留言表单',
                // title提示
                'title_msg' => '留言表单',
                // 短信开关
                'sms_open' => [],
                // 邮件开关
                'smtp_open' => $Smtp[0],
                // 站内开关
                'notice_open' => $Notice[0]
            ],
            [
                // 功能名称
                'notify_title' => '会员注册',
                // title提示
                'title_msg' => '会员注册',
                // 短信开关
                'sms_open' => $Sms[0],
                // 邮件开关
                'smtp_open' => $Smtp[1],
                // 站内开关
                'notice_open' => []
            ],
            [
                // 功能名称
                'notify_title' => '账户绑定',
                // title提示
                'title_msg' => '邮箱/手机绑定',
                // 短信开关
                'sms_open' => $Sms[1],
                // 邮件开关
                'smtp_open' => $Smtp[2],
                // 站内开关
                'notice_open' => []
            ],
            [
                // 功能名称
                'notify_title' => '找回密码',
                // title提示
                'title_msg' => '找回密码',
                // 短信开关
                'sms_open' => $Sms[2],
                // 邮件开关
                'smtp_open' => $Smtp[3],
                // 站内开关
                'notice_open' => []
            ],
            [
                // 功能名称
                'notify_title' => '订单付款',
                // title提示
                'title_msg' => '订单付款',
                // 短信开关
                'sms_open' => $Sms[3],
                // 邮件开关
                'smtp_open' => $Smtp[4],
                // 站内开关
                'notice_open' => $Notice[1]
            ],
            [
                // 功能名称
                'notify_title' => '订单发货',
                // title提示
                'title_msg' => '订单发货',
                // 短信开关
                'sms_open' => $Sms[4],
                // 邮件开关
                'smtp_open' => $Smtp[5],
                // 站内开关
                'notice_open' => $Notice[2]
            ]
        ];

        $this->assign('NotifyList', $NotifyList);
        return $this->fetch();
    }*/

    // 短信参数配置页面
    // public function SmsConfig()
    // {
    //     if (IS_AJAX_POST) {
    //         $inc_type = 'sms';
    //         $param = input('post.');

    //         if (!isset($param['sms_type'])) $param['sms_type'] = 1;
    //         if ($param['sms_type'] == 1) {
    //             unset($param['sms_appkey_tx']);
    //             unset($param['sms_appid_tx']);
    //         } else {
    //             unset($param['sms_appkey']);
    //             unset($param['sms_secretkey']);
    //         }

    //         /*多语言*/
    //         if (is_language()) {
    //             $langRow = \think\Db::name('language')->order('id asc')
    //                 ->cache(true, EYOUCMS_CACHE_TIME, 'language')
    //                 ->select();
    //             foreach ($langRow as $key => $val) {
    //                 tpCache($inc_type, $param, $val['mark']);
    //             }
    //         } else {
    //             tpCache($inc_type, $param);
    //         }
    //         /*--end*/

    //         $this->success('配置完成');
    //     }

    //     // 手机短信配置
    //     $sms = tpCache('sms');
    //     if (!isset($sms['sms_type'])) {
    //         $sms['sms_type'] = 1;
    //         tpCache('sms', ['sms_type' => 1]);
    //     }
    //     $this->assign('sms', $sms);
    //     return $this->fetch('sms_config');
    // }

    // 邮件参数配置页面
    // public function SmtpConfig()
    // {
    //     if (IS_AJAX_POST) {
    //         $inc_type = 'smtp';
    //         $param = input('post.');

    //         /*多语言*/
    //         if (is_language()) {
    //             $langRow = \think\Db::name('language')->order('id asc')
    //                 ->cache(true, EYOUCMS_CACHE_TIME, 'language')
    //                 ->select();
    //             foreach ($langRow as $key => $val) {
    //                 tpCache($inc_type, $param, $val['mark']);
    //             }
    //         } else {
    //             tpCache($inc_type, $param);
    //         }
    //         /*--end*/

    //         $this->success('配置完成');
    //     }

    //     // 邮箱配置
    //     $smtp = tpCache('smtp');
    //     $this->assign('smtp', $smtp);
    //     return $this->fetch('smtp_config');
    // }

    // 配置页面
    // public function TemplateConfig()
    // {
    //     if (IS_AJAX_POST) {
    //         $post = input('post.');
    //         if ('sms' == $post['config_type']) {
    //             if (empty($post['sms_sign'])) $this->error('请填写签名名称');
    //             if (empty($post['sms_tpl_code'])) $this->error(1 == $post['sms_type'] ? '请填写模板CODE' : '请填写模板ID');
    //             if (empty($post['tpl_content'])) $this->error('请填写模板内容');
    //             $UpData = [
    //                 'tpl_id'       => $post['tpl_id'],
    //                 'sms_sign'     => $post['sms_sign'],
    //                 'sms_tpl_code' => $post['sms_tpl_code'],
    //                 'tpl_content'  => $post['tpl_content'],
    //                 'is_open'      => $post['is_open'],
    //                 'update_time'  => getTime()
    //             ];
    //             $UpdateID = $this->sms_template_db->update($UpData);

    //         } else if ('smtp' == $post['config_type']) {
    //             if (empty($post['tpl_title'])) $this->error('请填写邮件标题');
    //             $UpData = [
    //                 'tpl_id'       => $post['tpl_id'],
    //                 'tpl_title'    => $post['tpl_title'],
    //                 'is_open'      => $post['is_open'],
    //                 'update_time'  => getTime()
    //             ];
    //             $UpdateID = $this->smtp_tpl_db->update($UpData);

    //         } else if ('notice' == $post['config_type']) {
    //             if (empty($post['tpl_title'])) $this->error('请填写站内信标题');
    //             $UpData = [
    //                 'tpl_id'       => $post['tpl_id'],
    //                 'tpl_title'    => $post['tpl_title'],
    //                 'is_open'      => $post['is_open'],
    //                 'update_time'  => getTime()
    //             ];
    //             $UpdateID = $this->users_notice_tpl_db->update($UpData);
    //         }
            
    //         if (!empty($UpdateID)) {
    //             $this->success('保存成功');
    //         } else {
    //             $this->error('保存失败');
    //         }
    //     }

    //     $TemplateID = input('param.tpl_id/d');
    //     $ConfigType = input('param.config_type/s');
    //     if ('sms' == $ConfigType) {
    //         // 短信处理逻辑
    //         $this->FindSmsConfig($TemplateID, $ConfigType);
    //         return $this->fetch('sms_tpl');

    //     } else if ('smtp' == $ConfigType) {
    //         // 邮箱处理逻辑
    //         $this->FindSmptConfig($TemplateID, $ConfigType);
    //         return $this->fetch('smtp_tpl');

    //     } else if ('notice' == $ConfigType) {
    //         // 站内信处理逻辑
    //         $this->FindNoticeConfig($TemplateID, $ConfigType);
    //         return $this->fetch('notice_tpl');
    //     }
    // }

    // 短信参数配置验证及加载页面
    // private function FindSmsConfig($TemplateID = null, $ConfigType = 'sms')
    // {
    //     /*短信参数配置验证*/
    //     $Sms = tpCache('sms');
    //     $SmsConfigured = 404;
    //     if (!empty($Sms)) {
    //         if (1 == $Sms['sms_type'] && !empty($Sms['sms_appkey']) && !empty($Sms['sms_secretkey']) && !empty($Sms['sms_test_mobile'])) {
    //             // 阿里云短信配置完成
    //             $SmsConfigured = 200;
    //         } else if (2 == $Sms['sms_type'] && !empty($Sms['sms_appkey_tx']) && !empty($Sms['sms_appid_tx']) && !empty($Sms['sms_test_mobile'])) {
    //             // 腾讯云短信配置完成
    //             $SmsConfigured = 200;
    //         }
    //     }
    //     $this->assign('SmsConfigured', $SmsConfigured);
    //     /* END */

    //     /*短信自定义模板配置*/
    //     $where = [
    //         'tpl_id' => $TemplateID,
    //         'lang' => $this->admin_lang
    //     ];
    //     // 查询短信模板配置
    //     $Find = $this->sms_template_db->where($where)->find();
    //     $this->assign('Find', $Find);
    //     /* END */

    //     // 配置类型
    //     $this->assign('ConfigType', $ConfigType);
    // }

    // 邮件参数配置验证及加载页面
    // private function FindSmptConfig($TemplateID = null, $ConfigType = 'smtp')
    // {
    //     /*邮件参数配置验证*/
    //     $Smtp = tpCache('smtp');
    //     $SmtpConfigured = 404;
    //     if (!empty($Smtp['smtp_server']) && !empty($Smtp['smtp_port']) && !empty($Smtp['smtp_user']) && !empty($Smtp['smtp_pwd']) && !empty($Smtp['smtp_from_eamil'])) {
    //         $SmtpConfigured = 200;
    //     }
    //     $this->assign('SmtpConfigured', $SmtpConfigured);
    //     /* END */

    //     /*邮箱自定义模板配置*/
    //     $where = [
    //         'tpl_id' => $TemplateID,
    //         'lang' => $this->admin_lang
    //     ];
    //     // 查询邮箱模板配置
    //     $Find = $this->smtp_tpl_db->where($where)->find();
    //     $this->assign('Find', $Find);
    //     /* END */

    //     // 配置类型
    //     $this->assign('ConfigType', $ConfigType);
    // }

    // 站内信参数配置验证及加载页面
    // private function FindNoticeConfig($TemplateID = null, $ConfigType = 'notice')
    // {
    //     /*站内信自定义模板配置*/
    //     $where = [
    //         'tpl_id' => $TemplateID,
    //         'lang' => $this->admin_lang
    //     ];
    //     // 查询站内信模板配置
    //     $Find = $this->users_notice_tpl_db->where($where)->find();
    //     $this->assign('find', $Find);
    //     $this->assign('ConfigType', $ConfigType);
    //     /* END */
    // }
}