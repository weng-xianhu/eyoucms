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

class ArchivesFlag extends Base
{
    public function index()
    {
        $list = array();
        $keywords = input('keywords/s');
        $keywords = addslashes(trim($keywords));

        $condition = array();
        if (!empty($keywords)) {
            $condition['flag_name'] = array('LIKE', "%{$keywords}%");
        }

        $archivesflagM =  Db::name('archives_flag');
        $count = $archivesflagM->where($condition)->count('id');// 查询满足要求的总记录数
        $Page = $pager = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $archivesflagM->where($condition)->order('sort_order asc, id asc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pager',$pager);// 赋值分页对象
        return $this->fetch();
    }

    /**
     * 删除
     * @return [type] [description]
     */
    public function del()
    {
        if (IS_POST) {
            $id_arr = input('del_id/a');
            $id_arr = eyIntval($id_arr);
            if(!empty($id_arr)){     
                $result = Db::name('archivesFlag')->field('flag_name,id,flag_fieldname')->where("id",'IN',$id_arr)->find();
                $r = Db::name('archivesFlag')->where([
                        'id' => ['IN', $id_arr], 'ifsystem' => 0
                    ])->delete();
                if($r !== false){
                    model('ArchivesFlag')->afterSave($result, 'del');
                    adminLog('删除文档属性：'.$result['flag_name']);
                    $this->success('删除成功');
                }
            }
        }
        $this->error('删除失败');
    }

    /**
     * 新增
     */
    public function add(){
        if (IS_POST) {
            $post = input('post.');
            $flag_name = str_replace(['"','\'',';','@','?','？','$','&',' ','*','#'], '', trim($post['flag_name']));
            if(empty($post['flag_name'])){
                $this->error('请输入属性名称');
            } else if(empty($flag_name)){
                $this->error('属性名称禁止含有特殊符号');
            } else if(Db::name('archivesFlag')->where(['flag_name'=>$flag_name])->find()){
                $this->error('属性名称已存在');
            }
            $post['flag_name'] = $flag_name;

            $post['flag_attr'] = strtolower(trim($post['flag_attr']));
            if(empty($post['flag_attr'])){
                $this->error('请输入属性值');
            } else if($this->validateFieldA($post['flag_attr'])==false){
                $this->error('属性值至少2个及以上小写字母');
            } else if(Db::name('archivesFlag')->where(['flag_attr'=>$post['flag_attr']])->find()){
                $this->error('属性值已存在');
            }  

            if (preg_match('/^is_(.+)$/i', $post['flag_attr'])) {
                $post['flag_fieldname'] = $post['flag_attr'];
            } else {
                $post['flag_fieldname'] = 'is_'.$post['flag_attr'];
            }

            /*检测字段是否存在于主表与附加表中*/
            $fieldLogic = new \app\admin\logic\FieldLogic;
            $channeltype = Db::name('channeltype')->field('id,table')->where(['id'=>['not in',[8,51]]])->select();
            foreach ($channeltype as $key => $val) {
                $table = PREFIX . $val['table'] . '_content';
                if (true == $fieldLogic->checkChannelFieldList($table, $post['flag_fieldname'], $val['id'])) {
                    $this->error("属性值 " . $post['flag_attr'] . " 与系统字段冲突！");
                }
            }
            /*--end*/

            $post['status'] = 1;
            $post['add_time'] = getTime();
            $post['update_time'] = getTime();
            $lastid = Db::name('archivesFlag')->insertGetId($post);
            if ($lastid) {
                model('ArchivesFlag')->afterSave($post, 'add');
                adminLog('新增文档属性：'.$post['flag_name']); // 写入操作日志
                $this->success("操作成功");
            }
            $this->error('操作失败');
        }                        
        return $this->fetch();
    }

    /**
     * 验证字段
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    private function validateFieldA($value) {
        // 规则：必须是全小写字母，且至少 2 个字符
        if (preg_match('/^[a-z]{2,}$/', $value)) {
            return true; // 验证通过
        } else {
            return false; // 验证失败
        }
    }
}