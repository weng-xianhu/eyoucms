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
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        $notice_where = [
            'is_read' => 0,
            'admin_id' => ['>', 0],
        ];
        $notice_count = $this->users_notice_tpl_content_db->where($notice_where)->count('content_id');
        $notice_count = intval($notice_count);
        if (IS_AJAX) {
            $this->success('查询成功', null, ['notice_count'=>$notice_count]);
        } else {
            $this->assign('notice_count', $notice_count);
        }
    }
}