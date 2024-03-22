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

use think\Page;
use think\Db;

class Other extends Base
{
    /*
     * 初始化操作
     */
    public function _initialize() 
    {
        parent::_initialize();
        // 判断是否有广告位置
        if (strtolower(ACTION_NAME) != 'index') {
            $count = Db::name('ad_position')->count('id');
            if (empty($count)) {
                $this->success('缺少广告位置，正在前往中……', url('AdPosition/add'), '', 3);
                exit;
            }
        }
    }

    public function index()
    {
        $list = array();
        $get = input('get.');
        $pid = input('param.pid/d', 0);
        $keywords = input('keywords/s');
        $condition = array();
        // 应用搜索条件
        foreach (['keywords', 'pid'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                if ($key == 'keywords') {
                    $condition['a.title'] = array('LIKE', "%{$get[$key]}%");
                } else {
                    $tmp_key = 'a.'.$key;
                    $condition[$tmp_key] = array('eq', $get[$key]);
                }
            }
        }

        // 多语言
        $condition['a.lang'] = array('eq', $this->admin_lang);

        $adM =  Db::name('ad');
        $count = $adM->alias('a')->where($condition)->count();// 查询满足要求的总记录数
        $Page = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $adM->alias('a')->where($condition)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        /*支持子目录*/
        foreach ($list as $key => $val) {
            $val['litpic'] = handle_subdir_pic($val['litpic']);
            $list[$key] = $val;
        }
        /*--end*/

        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pager',$Page);// 赋值分页对象

        $ad_position = model('AdPosition')->getAll('*','id');
        $this->assign('ad_position',$ad_position);

        $this->assign('pid',$pid);// 赋值分页对象
        return $this->fetch();
    }
    
    /**
     * 新增
     */
    public function add()
    {
        $this->language_access(); // 多语言功能操作权限

        if (IS_POST) {
            $post = input('post.');
            $is_remote = !empty($post['is_remote']) ? $post['is_remote'] : 0;
            $litpic = '';
            if ($is_remote == 1) {
                $litpic = $post['litpic_remote'];
            } else {
                $litpic = $post['litpic_local'];
            }
            $newData = array(
                'litpic'            => $litpic,
                'admin_id'  => session('admin_id'),
                'lang'  => $this->admin_lang,
                'sort_order'    => 100,
                'add_time'           => getTime(),
                'update_time'   => getTime(),
            );
            $data = array_merge($post, $newData);
            $insertId = Db::name('ad')->insertGetId($data);

            if ($insertId) {

                /*同步广告位置ID到多语言的模板变量里*/
                $this->syn_add_language_attribute($insertId);
                /*--end*/

                \think\Cache::clear('ad');
                adminLog('新增广告：'.$post['title']);
                $this->success("操作成功", url('Other/index'));
            } else {
                $this->error("操作失败");
            }
            exit;
        }

        $pid = input('param.pid/d', 0);
        $this->assign('pid', $pid);

        $ad_position = model('AdPosition')->getAll('*', 'id');
        $this->assign('ad_position', $ad_position);

        $ad_media_type = config('global.ad_media_type');
        $this->assign('ad_media_type', $ad_media_type);

        return $this->fetch();
    }

    
    /**
     * 编辑
     */
    public function edit()
    {
        if (IS_POST) {
            $post = input('post.');
            if(!empty($post['id'])){
                $post['id'] = intval($post['id']);
                $is_remote = !empty($post['is_remote']) ? $post['is_remote'] : 0;
                $litpic = '';
                if ($is_remote == 1) {
                    $litpic = $post['litpic_remote'];
                } else {
                    $litpic = $post['litpic_local'];
                }
                $newData = array(
                    'litpic'            => $litpic,
                    'update_time'       => getTime(),
                );
                $data = array_merge($post, $newData);
                $r = Db::name('ad')->where([
                        'id'    => $post['id'],
                    ])
                    ->cache(true,null,'ad')
                    ->update($data);
            }
            if ($r) {
                adminLog('编辑广告');
                $this->success("操作成功", url('Other/index'));
            } else {
                $this->error("操作失败");
            }
        }

        $assign_data = array();

        $id = input('id/d');
        $field = Db::name('ad')->where([
                'id'    => $id,
            ])->find();
        if (empty($field)) {
            $this->error('广告不存在，请联系管理员！');
            exit;
        }
        if (is_http_url($field['litpic'])) {
            $field['is_remote'] = 1;
            $field['litpic_remote'] = handle_subdir_pic($field['litpic']);
        } else {
            $field['is_remote'] = 0;
            $field['litpic_local'] = handle_subdir_pic($field['litpic']);
        }
        
        /*支持子目录*/
        $field['intro'] = handle_subdir_pic($field['intro'], 'html');
        /*--end*/

        $assign_data['field'] = $field;
        $assign_data['ad_position'] = model('AdPosition')->getAll('*', 'id');

        $assign_data['ad_media_type'] = config('global.ad_media_type');

        $this->assign($assign_data);
        return $this->fetch();
    }
    
    /**
     * 删除
     */
    public function del()
    {
        $this->language_access(); // 多语言功能操作权限

        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if(!empty($id_arr)){

            /*多语言*/
            $attr_name_arr = [];
            foreach ($id_arr as $key => $val) {
                $attr_name_arr[] = 'ad'.$val;
            }
            if (is_language()) {
                $new_id_arr = Db::name('language_attr')->where([
                        'attr_name' => ['IN', $attr_name_arr],
                        'attr_group'    => 'ad',
                    ])->column('attr_value');
                !empty($new_id_arr) && $id_arr = $new_id_arr;
            }
            /*--end*/
            $r = Db::name('ad')->where([
                    'id'    => ['IN', $id_arr],
                ])
                ->cache(true,null,'ad')
                ->delete();
            if ($r) {

                /*多语言*/
                if (!empty($attr_name_arr)) {
                    Db::name('language_attr')->where([
                            'attr_name' => ['IN', $attr_name_arr],
                            'attr_group'    => 'ad',
                        ])->delete();
                    Db::name('language_attribute')->where([
                            'attr_name' => ['IN', $attr_name_arr],
                            'attr_group'    => 'ad',
                        ])->delete();
                }
                /*--end*/

                adminLog('删除广告-id：'.implode(',', $id_arr));
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }else{
            $this->error('参数有误');
        }
    }

    /**
     * ui美化新增
     */
    public function ui_add()
    {
        $this->language_access(); // 多语言功能操作权限

        if (IS_POST) {
            $post = input('post.');
            $is_remote = !empty($post['is_remote']) ? $post['is_remote'] : 0;
            $litpic = '';
            if ($is_remote == 1) {
                $litpic = $post['litpic_remote'];
            } else {
                $litpic = $post['litpic_local'];
            }
            $newData = array(
                'media_type'    => 1,
                'litpic'            => $litpic,
                'lang'  => get_current_lang(),
                'add_time'       => getTime(),
                'update_time'       => getTime(),
            );
            $data = array_merge($post, $newData);
            $insertId = Db::name('ad')->insertGetId($data);
            if ($insertId) {

                /*同步广告位置ID到多语言的模板变量里*/
                $this->syn_add_language_attribute($insertId);
                /*--end*/

                \think\Cache::clear('ad');
                adminLog('新增广告：'.$post['title']);
                $this->success('操作成功');
            } else {
                $this->error('操作失败');
            }
        }

        $edit_id = input('param.edit_id/d', 0);
        $pid = input('param.pid/d', 0);
        /*多语言*/
        $new_pid = model('LanguageAttr')->getBindValue($pid, 'ad_position');
        !empty($new_pid) && $pid = $new_pid;
        /*--end*/
        $assign_data = array();
        $assign_data['ad_position'] = model('AdPosition')->getInfo($pid);
        $assign_data['edit_id'] = $edit_id;

        $this->assign($assign_data);
        return $this->fetch();
    }

    /**
     * ui美化编辑
     */
    public function ui_edit()
    {
        if (IS_POST) {
            $post = input('post.');
            if(!empty($post['id'])){
                $post['id'] = intval($post['id']);
                $is_remote = !empty($post['is_remote']) ? $post['is_remote'] : 0;
                $litpic = '';
                if ($is_remote == 1) {
                    $litpic = $post['litpic_remote'];
                } else {
                    $litpic = $post['litpic_local'];
                }
                $newData = array(
                    'litpic'            => $litpic,
                    'update_time'       => getTime(),
                );
                $data = array_merge($post, $newData);
                $r = Db::name('ad')->where([
                        'id'    => $post['id'],
                    ])
                    ->cache(true,null,'ad')
                    ->update($data);
                if ($r) {
                    adminLog('编辑广告：'.$post['title']);
                    $this->success('操作成功');
                }
            }
            $this->error('操作失败');
        }

        $assign_data = array();

        $id = input('id/d');
        $field = Db::name('ad')->where([
                'id'    => $id,
            ])->find();
        if (empty($field)) {
            $this->error('广告不存在，请联系管理员！');
            exit;
        }
        if (is_http_url($field['litpic'])) {
            $field['is_remote'] = 1;
            $field['litpic_remote'] = $field['litpic'];
        } else {
            $field['is_remote'] = 0;
            $field['litpic_local'] = $field['litpic'];
        }
        $assign_data['field'] = $field;
        $assign_data['ad_position'] = model('AdPosition')->getInfo($field['pid']);

        $this->assign($assign_data);
        return $this->fetch();
    }
    
    /**
     * 删除
     */
    public function ui_del()
    {
        $this->language_access(); // 多语言功能操作权限
        
        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if(!empty($id_arr)){

            /*多语言*/
            $attr_name_arr = [];
            foreach ($id_arr as $key => $val) {
                $attr_name_arr[] = 'ad'.$val;
            }
            if (is_language()) {
                $new_id_arr = Db::name('language_attr')->where([
                        'attr_name' => ['IN', $attr_name_arr],
                        'attr_group'    => 'ad',
                    ])->column('attr_value');
                !empty($new_id_arr) && $id_arr = $new_id_arr;
            }
            /*--end*/

            $r = Db::name('ad')->where([
                    'id'    => ['IN', $id_arr],
                ])
                ->cache(true,null,'ad')
                ->delete();
            if ($r) {

                /*多语言*/
                if (!empty($attr_name_arr)) {
                    Db::name('language_attr')->where([
                            'attr_name' => ['IN', $attr_name_arr],
                            'attr_group'    => 'ad',
                        ])->delete();
                    Db::name('language_attribute')->where([
                            'attr_name' => ['IN', $attr_name_arr],
                            'attr_group'    => 'ad',
                        ])->delete();
                }
                /*--end*/

                adminLog('删除广告-id：'.implode(',', $id_arr));
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }else{
            $this->error('参数有误');
        }
    }

    /**
     * 同步新增广告ID到多语言的模板变量里
     */
    private function syn_add_language_attribute($ad_id)
    {
        /*单语言情况下不执行多语言代码*/
        if (!is_language()) {
            return true;
        }
        /*--end*/

        $attr_group = 'ad';
        $admin_lang = $this->admin_lang;
        $main_lang = get_main_lang();
        $languageRow = Db::name('language')->field('mark')->order('id asc')->select();
        if (!empty($languageRow) && $admin_lang == $main_lang) { // 当前语言是主体语言，即语言列表最早新增的语言
            $ad_db = Db::name('ad');
            $result = $ad_db->find($ad_id);
            $attr_name = 'ad'.$ad_id;
            $r = Db::name('language_attribute')->save([
                'attr_title'    => $result['title'],
                'attr_name'     => $attr_name,
                'attr_group'    => $attr_group,
                'add_time'      => getTime(),
                'update_time'   => getTime(),
            ]);
            if (false !== $r) {
                $data = [];
                foreach ($languageRow as $key => $val) {
                    /*同步新广告到其他语言广告列表*/
                    if ($val['mark'] != $admin_lang) {
                        $addsaveData = $result;
                        $addsaveData['lang'] = $val['mark'];
                        $newPid = Db::name('language_attr')->where([
                                'attr_name' => 'adp'.$result['pid'],
                                'attr_group'    => 'ad_position',
                                'lang'  => $val['mark'],
                            ])->getField('attr_value');
                        $addsaveData['pid'] = $newPid;
                        unset($addsaveData['id']);
                        $ad_id = $ad_db->insertGetId($addsaveData);
                    }
                    /*--end*/
                    
                    /*所有语言绑定在主语言的ID容器里*/
                    $data[] = [
                        'attr_name' => $attr_name,
                        'attr_value'    => $ad_id,
                        'lang'  => $val['mark'],
                        'attr_group'    => $attr_group,
                        'add_time'      => getTime(),
                        'update_time'   => getTime(),
                    ];
                    /*--end*/
                }
                if (!empty($data)) {
                    model('LanguageAttr')->saveAll($data);
                }
            }
        }
    }
}