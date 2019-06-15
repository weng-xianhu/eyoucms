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
use app\common\logic\ArctypeLogic;

class Guestbook extends Base
{
    // 模型标识
    public $nid = 'guestbook';
    // 模型ID
    public $channeltype = '';
    // 表单类型
    public $attrInputTypeArr = array();
    
    public function _initialize() {
        parent::_initialize();
        $channeltype_list = config('global.channeltype_list');
        $this->channeltype = $channeltype_list[$this->nid];
        $this->attrInputTypeArr = config('global.attr_input_type_arr');
    }

    /**
     * 留言列表
     */
    public function index()
    {
        $assign_data = array();
        $condition = array();
        // 获取到所有GET参数
        $get = input('get.');
        $typeid = input('typeid/d');
        $begin = strtotime(input('add_time_begin'));
        $end = strtotime(input('add_time_end'));

        // 应用搜索条件
        foreach (['keywords','typeid'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                if ($key == 'keywords') {
                    $attr_row = M('guestbook_attr')->field('aid')->where(array('attr_value'=>array('LIKE', "%{$get[$key]}%")))->group('aid')->getAllWithIndex('aid');
                    $aids = array_keys($attr_row);
                    $condition['a.aid'] = array('IN', $aids);
                } else if ($key == 'typeid') {
                    $condition['a.typeid'] = array('eq', $get[$key]);
                } else {
                    $condition['a.'.$key] = array('eq', $get[$key]);
                }
            }
        }

        // 时间检索
        if ($begin > 0 && $end > 0) {
            $condition['a.add_time'] = array('between',"$begin,$end");
        } else if ($begin > 0) {
            $condition['a.add_time'] = array('egt', $begin);
        } else if ($end > 0) {
            $condition['a.add_time'] = array('elt', $end);
        }
        
        // 多语言
        $condition['a.lang'] = array('eq', $this->admin_lang);

        /**
         * 数据查询，搜索出主键ID的值
         */
        $count = DB::name('guestbook')->alias('a')->where($condition)->count('aid');// 查询满足要求的总记录数
        $Page = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = DB::name('guestbook')
            ->field("b.*, a.*")
            ->alias('a')
            ->join('__ARCTYPE__ b', 'a.typeid = b.id', 'LEFT')
            ->where($condition)
            ->order('a.update_time desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->getAllWithIndex('aid');

        /**
         * 完善数据集信息
         * 在数据量大的情况下，经过优化的搜索逻辑，先搜索出主键ID，再通过ID将其他信息补充完整；
         */
        if ($list) {
            $aids = array_keys($list);
            $row = DB::name('guestbook_attribute')
                ->field('a.attr_name, b.attr_value, b.aid, b.attr_id')
                ->alias('a')
                ->join('__GUESTBOOK_ATTR__ b', 'b.attr_id = a.attr_id', 'LEFT')
                ->where('b.aid', 'in', $aids)
                ->order('b.aid desc, a.sort_order asc, a.attr_id asc')
                ->getAllWithIndex();
            $attr_list = array();
            foreach ($row as $key => $val) {
                $attr_list[$val['aid']][] = $val;
            }
            foreach ($list as $key => $val) {
                $list[$key]['attr_list'] = isset($attr_list[$val['aid']]) ? $attr_list[$val['aid']] : array();
            }
        }
        $show = $Page->show(); // 分页显示输出
        $assign_data['page'] = $show; // 赋值分页输出
        $assign_data['list'] = $list; // 赋值数据集
        $assign_data['pager'] = $Page; // 赋值分页对象

        // 栏目ID
        $assign_data['typeid'] = $typeid; // 栏目ID
        /*当前栏目信息*/
        $arctype_info = array();
        if ($typeid > 0) {
            $arctype_info = M('arctype')->field('typename')->find($typeid);
        }
        $assign_data['arctype_info'] = $arctype_info;
        /*--end*/

        /*选项卡*/
        $tab = input('param.tab/d', 3);
        $assign_data['tab'] = $tab;
        /*--end*/

        $this->assign($assign_data);
        return $this->fetch();
    }

    /**
     * 删除
     */
    public function del()
    {
        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if(!empty($id_arr)){
            $r = M('guestbook')->where([
                    'aid'   => ['IN', $id_arr],
                    'lang'  => $this->admin_lang,
                ])->delete();
            if($r){
                // ---------后置操作
                model('Guestbook')->afterDel($id_arr);
                // ---------end
                adminLog('删除留言-id：'.implode(',', $id_arr));
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('参数有误');
        }
    }

    /**
     * 留言表单表单列表
     */
    public function attribute_index()
    {
        $assign_data = array();
        $condition = array();
        // 获取到所有GET参数
        $get = input('get.');
        $typeid = input('typeid/d');

        // 应用搜索条件
        foreach (['keywords','typeid'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                if ($key == 'keywords') {
                    $condition['a.attr_name'] = array('LIKE', "%{$get[$key]}%");
                } else if ($key == 'typeid') {
                    $condition['a.typeid'] = array('eq', $get[$key]);
                } else {
                    $condition['a.'.$key] = array('eq', $get[$key]);
                }
            }
        }

        $condition['is_del'] = 0;
        // 多语言
        $condition['lang'] = $this->admin_lang;

        /**
         * 数据查询，搜索出主键ID的值
         */
        $count = DB::name('guestbook_attribute')->alias('a')->where($condition)->count();// 查询满足要求的总记录数
        $Page = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = DB::name('guestbook_attribute')
            ->field("a.attr_id")
            ->alias('a')
            ->where($condition)
            ->order('a.typeid desc, a.sort_order asc, a.attr_id asc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->getAllWithIndex('attr_id');

        /**
         * 完善数据集信息
         * 在数据量大的情况下，经过优化的搜索逻辑，先搜索出主键ID，再通过ID将其他信息补充完整；
         */
        if ($list) {
            $attr_ida = array_keys($list);
            $fields = "b.*, a.*";
            $row = DB::name('guestbook_attribute')
                ->field($fields)
                ->alias('a')
                ->join('__ARCTYPE__ b', 'a.typeid = b.id', 'LEFT')
                ->where('a.attr_id', 'in', $attr_ida)
                ->getAllWithIndex('attr_id');
            
            /*获取多语言关联绑定的值*/
            $row = model('LanguageAttr')->getBindValue($row, 'guestbook_attribute', $this->main_lang); // 多语言
            /*--end*/

            foreach ($row as $key => $val) {
                $val['fieldname'] = 'attr_'.$val['attr_id'];
                $row[$key] = $val;
            }
            foreach ($list as $key => $val) {
                $list[$key] = $row[$val['attr_id']];
            }
        }
        $show = $Page->show(); // 分页显示输出
        $assign_data['page'] = $show; // 赋值分页输出
        $assign_data['list'] = $list; // 赋值数据集
        $assign_data['pager'] = $Page; // 赋值分页对象

        // 栏目ID
        $assign_data['typeid'] = $typeid; // 栏目ID
        /*当前栏目信息*/
        $arctype_info = array();
        if ($typeid > 0) {
            $arctype_info = M('arctype')->field('typename')->find($typeid);
        }
        $assign_data['arctype_info'] = $arctype_info;
        /*--end*/

        /*选项卡*/
        $tab = input('param.tab/d', 3);
        $assign_data['tab'] = $tab;
        /*--end*/

        $assign_data['attrInputTypeArr'] = $this->attrInputTypeArr; // 表单类型
        $this->assign($assign_data);
        return $this->fetch();
    }

    /**
     * 新增留言表单
     */
    public function attribute_add()
    {
        //防止php超时
        function_exists('set_time_limit') && set_time_limit(0);
        
        $this->language_access(); // 多语言功能操作权限

        if(IS_AJAX && IS_POST)//ajax提交验证
        {
            $model = model('GuestbookAttribute');

            $attr_values = str_replace('_', '', input('attr_values')); // 替换特殊字符
            $attr_values = str_replace('@', '', $attr_values); // 替换特殊字符            
            $attr_values = trim($attr_values);
            
            $post_data = input('post.');
            $post_data['attr_values'] = $attr_values;

            $savedata = array(
                'attr_name' => $post_data['attr_name'],
                'typeid'    => $post_data['typeid'],
                'attr_input_type'   => isset($post_data['attr_input_type']) ? $post_data['attr_input_type'] : '',
                'attr_values'   => isset($post_data['attr_values']) ? $post_data['attr_values'] : '',
                'sort_order'    => 100,
                'lang'  => $this->admin_lang,
                'add_time'  => getTime(),
                'update_time'   => getTime(),
            );

            // 数据验证            
            $validate = \think\Loader::validate('GuestbookAttribute');
            if(!$validate->batch()->check($savedata))
            {
                $error = $validate->getError();
                $error_msg = array_values($error);
                $return_arr = array(
                    'status' => -1,
                    'msg' => $error_msg[0],
                    'data' => $error,
                );
                respose($return_arr);
            } else {
                $model->data($savedata,true); // 收集数据
                $model->save(); // 写入数据到数据库
                $insertId = $model->getLastInsID();

                /*同步留言属性ID到多语言的模板变量里*/
                $this->syn_add_language_attribute($insertId);
                /*--end*/

                $return_arr = array(
                     'status' => 1,
                     'msg'   => '操作成功',                        
                     'data'  => array('url'=>url('Guestbook/attribute_index', array('typeid'=>$post_data['typeid']))),
                );
                adminLog('新增留言表单：'.$savedata['attr_name']);
                respose($return_arr);
            }  
        }  

        $typeid = input('param.typeid/d', 0);
        if ($typeid > 0) {
            $select_html = M('arctype')->where('id', $typeid)->getField('typename');
            $select_html = !empty($select_html) ? $select_html : '该栏目不存在';
        } else {
            $arctypeLogic = new ArctypeLogic();
            $map = array(
                'channeltype'    => $this->channeltype,
            );
            $arctype_max_level = intval(config('global.arctype_max_level'));
            $select_html = $arctypeLogic->arctype_list(0, $typeid, true, $arctype_max_level, $map);
        }
        $assign_data['select_html'] = $select_html; // 
        $assign_data['typeid'] = $typeid; // 栏目ID
        
        $this->assign($assign_data);
        return $this->fetch();
    }

    /**
     * 编辑留言表单
     */
    public function attribute_edit()
    {
        if(IS_AJAX && IS_POST)//ajax提交验证
        {
            $model = model('GuestbookAttribute');

            $attr_values = str_replace('_', '', input('attr_values')); // 替换特殊字符
            $attr_values = str_replace('@', '', $attr_values); // 替换特殊字符            
            $attr_values = trim($attr_values);
            
            $post_data = input('post.');
            $post_data['attr_values'] = $attr_values;

            $savedata = array(
                'attr_id'   => $post_data['attr_id'],
                'attr_name' => $post_data['attr_name'],
                'typeid'    => $post_data['typeid'],
                'attr_input_type'   => isset($post_data['attr_input_type']) ? $post_data['attr_input_type'] : '',
                'attr_values'   => isset($post_data['attr_values']) ? $post_data['attr_values'] : '',
                'sort_order'    => 100,
                'update_time'   => getTime(),
            );
            // 数据验证            
            $validate = \think\Loader::validate('GuestbookAttribute');
            if(!$validate->batch()->check($savedata))
            {
                $error = $validate->getError();
                $error_msg = array_values($error);
                $return_arr = array(
                    'status' => -1,
                    'msg' => $error_msg[0],
                    'data' => $error,
                );
                respose($return_arr);
            } else {
                $model->data($savedata,true); // 收集数据
                $model->isUpdate(true, [
                        'attr_id'   => $post_data['attr_id'],
                        'lang'  => $this->admin_lang,
                    ])->save(); // 写入数据到数据库     
                $return_arr = array(
                     'status' => 1,
                     'msg'   => '操作成功',                        
                     'data'  => array('url'=>url('Guestbook/attribute_index', array('typeid'=>$post_data['typeid']))),
                );
                adminLog('编辑留言表单：'.$savedata['attr_name']);
                respose($return_arr);
            }  
        }  

        $assign_data = array();

        $id = input('id/d');
        /*获取多语言关联绑定的值*/
        $new_id = model('LanguageAttr')->getBindValue($id, 'guestbook_attribute'); // 多语言
        !empty($new_id) && $id = $new_id;
        /*--end*/
        $info = M('GuestbookAttribute')->where([
                'attr_id'    => $id,
                'lang'  => $this->admin_lang,
            ])->find();
        if (empty($info)) {
            $this->error('数据不存在，请联系管理员！');
            exit;
        }
        $assign_data['field'] = $info;

        // 所在栏目
        $select_html = M('arctype')->where('id', $info['typeid'])->getField('typename');
        $select_html = !empty($select_html) ? $select_html : '该栏目不存在';
        $assign_data['select_html'] = $select_html;

        $this->assign($assign_data);
        return $this->fetch();
    }
    
    /**
     * 删除留言表单
     */
    public function attribute_del()
    {
        $this->language_access(); // 多语言功能操作权限

        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if(!empty($id_arr)){
            /*多语言*/
            if (is_language()) {
                $attr_name_arr = [];
                foreach ($id_arr as $key => $val) {
                    $attr_name_arr[] = 'attr_'.$val;
                }
                $new_id_arr = Db::name('language_attr')->where([
                        'attr_name' => ['IN', $attr_name_arr],
                        'attr_group'    => 'guestbook_attribute',
                    ])->column('attr_value');
                !empty($new_id_arr) && $id_arr = $new_id_arr;
            }
            /*--end*/
            $r = M('GuestbookAttribute')->where([
                    'attr_id'   => ['IN', $id_arr],
                ])->update([
                    'is_del'    => 1,
                    'update_time'   => getTime(),
                ]);
            if($r){
                adminLog('删除留言表单-id：'.implode(',', $id_arr));
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }else{
            $this->error('参数有误');
        }
    }

    /**
     * 同步新增留言属性ID到多语言的模板变量里
     */
    private function syn_add_language_attribute($attr_id)
    {
        /*单语言情况下不执行多语言代码*/
        if (!is_language()) {
            return true;
        }
        /*--end*/

        $attr_group = 'guestbook_attribute';
        $languageRow = Db::name('language')->field('mark')->order('id asc')->select();
        if (!empty($languageRow) && $this->admin_lang == $this->main_lang) { // 当前语言是主体语言，即语言列表最早新增的语言
            $result = Db::name('guestbook_attribute')->find($attr_id);
            $attr_name = 'attr_'.$attr_id;
            $r = Db::name('language_attribute')->save([
                'attr_title'    => $result['attr_name'],
                'attr_name'     => $attr_name,
                'attr_group'    => $attr_group,
                'add_time'      => getTime(),
                'update_time'   => getTime(),
            ]);
            if (false !== $r) {
                $data = [];
                foreach ($languageRow as $key => $val) {
                    /*同步新留言属性到其他语言留言属性列表*/
                    if ($val['mark'] != $this->admin_lang) {
                        $addsaveData = $result;
                        $addsaveData['lang'] = $val['mark'];
                        $newTypeid = Db::name('language_attr')->where([
                                'attr_name' => 'tid'.$result['typeid'],
                                'attr_group'    => 'arctype',
                                'lang'  => $val['mark'],
                            ])->getField('attr_value');
                        $addsaveData['typeid'] = $newTypeid;
                        unset($addsaveData['attr_id']);
                        $attr_id = Db::name('guestbook_attribute')->insertGetId($addsaveData);
                    }
                    /*--end*/
                    
                    /*所有语言绑定在主语言的ID容器里*/
                    $data[] = [
                        'attr_name' => $attr_name,
                        'attr_value'    => $attr_id,
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