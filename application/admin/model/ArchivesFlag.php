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

/**
 * 文档属性
 */
class ArchivesFlag extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 获取启用的文档属性列表
     * @return [type] [description]
     */
    public function getList($where = [])
    {
    	if (empty($where)) {
    		$where['status'] = ['gt', 0];
    	}
        $result = Db::name('archives_flag')->where($where)
        	->order("sort_order asc, id asc")
        	->cache(true, EYOUCMS_CACHE_TIME, 'archives_flag')
        	->select();

        return $result;
    }
    /**
     * 后置操作AFTER is_head
     */
    public function afterSave($post, $opt){
        $prefix = \think\Config::get('database.prefix');
        $table = $prefix.'archives';
        switch ($opt) {
            case 'del':
                try {
                    $flag_fieldname = $post['flag_fieldname'];
                    Db::name('channelfield')->where(['name'=>$flag_fieldname])->delete();
                    Db::execute("ALTER TABLE `{$table}` DROP COLUMN {$flag_fieldname}");
                    schemaTable('archives');
                    \think\Cache::clear('archives_flag');
                } catch (\Exception $e) {
                    
                }
                break;
            case 'add':
                $datas = [];
                $channeltype = Db::name('channeltype')->field('id,title')->where(['id'=>['not in',[8,51]]])->select();
                foreach ($channeltype as $key => $v) {
                    $info = [
                        'name'=>$post['flag_fieldname'],
                        'channel_id'=>$v['id'],
                        'title'=>$post['flag_name'].'（0=否，1=是）',
                        'dtype'=>'switch',
                        'define'=>'tinyint(1)',
                        'maxlength'=>1,
                        'dfvalue'=>0,
                        'ifeditable'=>1,
                        'ifsystem'=>1,
                        'ifmain'=>1,
                        'ifcontrol'=>1,
                        'add_time'=>getTime(),
                        'update_time'=>getTime()
                    ];
                    $datas[] = $info;
                    if (0 == $key) {
                        $info['channel_id'] = 0;
                        $datas[] = $info;
                    }
                }
                $r = Db::name('channelfield')->insertAll($datas);
                if ($r !== false) {
                    Db::execute("ALTER TABLE `{$table}` ADD COLUMN `{$post['flag_fieldname']}`  tinyint(1) NULL DEFAULT 0 COMMENT '{$post['flag_name']}（0=否，1=是）' AFTER `is_diyattr`");
                    schemaTable('archives');
                    \think\Cache::clear();
                    delFile(RUNTIME_PATH);
                }
                break;
            default:
                
                break;
        }
    }
}