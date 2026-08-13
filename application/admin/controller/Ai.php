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

class Ai extends Base {
    public function _initialize() {
        parent::_initialize();
        $this->language_access(); // 多语言功能操作权限
    }
    public function index()
    {
        if(IS_POST){
            $data = input('post.');
            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache('ai', $data, $val['mark']);
                }
            } else {
                tpCache('ai', $data);
            }
            /*--end*/
            $this->success('提交成功');
        }
        $aiConfig = tpCache('ai');
        if(empty($aiConfig)){
            $aiConfig=['ai_ask_open'=>0,'ai_ask_power'=>'你是一名论坛技术版主，请回答：[标题]，回答不超过300字。',
                    'ai_ask_obj'=>0,'ai_pl_open'=>0,'ai_pl_power'=>'你是一名游客，读完这篇文章：[标题]后，评论不超过300字。','ai_pl_obj'=>0,'ai_bd_adr_open'=>0];
        }
        $is_weapp_ai = 0;
        if (is_dir('./weapp/Ai/')){
            $is_weapp_ai = 1;
        }
        $this->assign('row',$aiConfig);
        $this->assign('is_weapp_ai',$is_weapp_ai);
        return $this->fetch();
    }
    public function check(){
        if(IS_POST){
            $post = input('post.');
            if(empty($post['port']) || empty($post['value'])){
                $this->error('参数非法');
            }
            if (!is_dir('./weapp/Ai/')) {
                $this->error('请先安装Ai智能创作平台');
            }
        }
    }
}