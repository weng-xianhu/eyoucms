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
namespace app\admin\model;

use think\Db;
use think\Model;

/**
 * 回收站
 */
class RecycleBin extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 清空回收站
     * @author wengxianhu by 2017-7-26
     */
    public function clear($type = 'all')
    {
        try {
            // 栏目
            if (in_array($type, ['all','arctype'])) {
                $typeids = Db::name('arctype')->where(['is_del'=>1])->column('id');
                if (!empty($typeids)) {
                    Db::name('arctype')->where(['id'=>['IN', $typeids]])->delete();
                    Db::name('archives')->where(['typeid'=>['IN', $typeids],'channel'=>6])->delete();
                    Db::name('single_content')->where(['typeid'=>['IN', $typeids]])->delete();
                }
            }
            // 文档
            if (in_array($type, ['all','archives'])) {
                $condition = array();
                $condition['channel'] = array('neq', 6); // 排除单页模型
                $condition['is_del'] = 1;
                $row = Db::name('archives')->field('aid,channel')->where($condition)->select();
                $list = array();
                foreach ($row as $key => $val) {
                    $list[$val['channel']][] = $val['aid'];
                }
                $channeltypeRow = Db::name('channeltype')->field('id,table')->where(['id'=>['gt',0]])->getAllWithIndex('id');
                foreach ($list as $key => $val) {
                    $aids = $list[$key];
                    $table_name = $channeltypeRow[$key]['table'];
                    Db::name('archives')->where(['aid'=>['IN', $aids]])->delete();
                    Db::name("{$table_name}_content")->where(['aid'=>['IN', $aids]])->delete();
                }
            }
            // 自定义变量
            if (in_array($type, ['all','customvar'])) {
                $names = Db::name('config')->where(['is_del'=>1])->column('name');
                if (!empty($names)) {
                    Db::name('config')->where(['name'=>['IN', $names], 'is_del'=>1])->delete();
                    Db::name('config_attribute')->where(['attr_var_name'=>['IN', $names]])->delete();
                }
            }
            // 留言属性
            if (in_array($type, ['all','gbookattr'])) {
                $attr_ids = Db::name('guestbook_attribute')->where(['is_del'=>1])->column('attr_id');
                if (!empty($attr_ids)) {
                    Db::name('guestbook_attribute')->where(['attr_id'=>['IN', $attr_ids], 'is_del'=>1])->delete();
                    Db::name('guestbook_attr')->where(['attr_id'=>['IN', $attr_ids]])->delete();
                }
            }
        } catch (\Exception $e) {
            
        }
    }
}