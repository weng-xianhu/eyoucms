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
namespace app\admin\model;

use think\Db;
use think\Model;

class Form extends Model
{
    public $admin_lang = 'cn';

    // 初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        $this->admin_lang = get_admin_lang();
    }

    // 查询表单提交的数量
    public function GetFormListCount($form_ids = [])
    {
        // 查询条件
        $where = [
            'typeid' => ['IN', $form_ids],
            'form_type' => 1,
            'lang' => $this->admin_lang,
        ];

        // 执行查询
        $form_list_count = Db::name('guestbook')
            ->field('typeid as form_id, count(aid) AS count')
            ->where($where)
            ->group('form_id')
            ->getAllWithIndex('form_id');

        // 返回结果
        return $form_list_count;
    }

    /**
     * 删除的后置操作方法
     * 自定义的一个函数 用于数据删除后做的相应处理操作, 使用时手动调用
     * @param int $aid
     */
    public function afterDel($aidArr = array())
    {
        if (is_string($aidArr)) {
            $aidArr = explode(',', $aidArr);
        }

        // 同时删除属性内容
        Db::name('guestbook_attr')->where([
                'aid'   => ['IN', $aidArr]
            ])->delete();
    }
}