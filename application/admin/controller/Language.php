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
 * Date: 2018-06-28
 */

namespace app\admin\controller;

use think\Page;
use think\Db;
use app\common\logic\ArctypeLogic;

/**
 * 插件的控制器
 */
class Language extends Base
{
    /**
     * 语言库模型
     */
    public $langModel;

    /**
     * 国家语言模型
     */
    public $langMarkModel;

    /**
     * 语言包模型
     */
    public $langPackModel;

    /**
     * 语言模板变量模型
     */
    public $langAttributeModel;

    /**
     * 语言模板变量关联绑定的数据模型
     */
    public $langAttrModel;

    /**
     * 构造方法
     */
    public function __construct(){
        parent::__construct();
        $this->langModel = model('Language');
        $this->langMarkModel = model('LanguageMark');
        $this->langAttributeModel = model('LanguageAttribute');
        $this->langAttrModel = model('LanguageAttr');
        $this->langPackModel = model('LanguagePack');
    }

    /**
     * 多语言 - 列表
     */
    public function index()
    {
        function_exists('set_time_limit') && set_time_limit(0); //防止备份数据过程超时
        
        /*修复多语言之前的坑，删除索引，兼容多语言的重名变量*/
        Db::execute('ALTER TABLE `ey_config` DROP INDEX `name`');
        /*--end*/

        /*同步数据到模板变量*/
        $this->syn_langattr();
        /*--end*/

        $list = array();
        $keywords = input('keywords/s');

        $map = array();
        if (!empty($keywords)) {
            $map['cn_title'] = array('LIKE', "%{$keywords}%");
        }

        $language_db = Db::name('language');
        $count = $language_db->where($map)->count('id');// 查询满足要求的总记录数
        $pageObj = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $language_db->where($map)
            ->order('id asc')
            ->limit($pageObj->firstRow.','.$pageObj->listRows)
            ->select();
        if (!empty($list)) {
            $marks = get_arr_column($list, 'mark');
            $languagemarkList = Db::name('language_mark')->field('mark,cn_title')
                ->where([
                    'mark'  => ['IN', $marks]
                ])->getAllWithIndex('mark');
            $this->assign('languagemarkList', $languagemarkList);
        }
        $pageStr = $pageObj->show(); // 分页显示输出
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageStr', $pageStr); // 赋值分页输出
        $this->assign('pageObj', $pageObj); // 赋值分页对象

        return $this->fetch();
    }

    /**
     * 多语言 - 新增
     */
    public function add()
    {
        //防止php超时
        function_exists('set_time_limit') && set_time_limit(0);

        $this->language_access(); // 多语言功能操作权限

        if (IS_POST) {
            $post = input('post.');
            $mark = trim($post['mark']);
            $is_home_default = intval($post['is_home_default']);

            $count = $this->langModel->where('mark',$mark)->count();
            if (!empty($count)) {
                $this->error('该语言已存在，请检查');
            }

            if (!empty($post['url']) && !is_http_url($post['url'])) {
                $post['url'] = 'http://'.$post['url'];
            }

            /*组装存储数据*/
            $nowData = array(
                'is_home_default'    => $is_home_default,
                'add_time'    => getTime(),
                'update_time'    => getTime(),
            );
            $saveData = array_merge($post, $nowData);
            /*--end*/
            $this->langModel->save($saveData);
            $insertId = $this->langModel->id;
            if (false !== $insertId) {
                $syn_status = $this->langModel->afterAdd($insertId, $post);
                if (false !== $syn_status) {
                    adminLog('新增多语言：'.$post['title']); // 写入操作日志
                    $this->success("新增成功，正在同步官方语言包数据……", url('Language/add_lang_syn_pack', ['mark'=>$mark, 'c_lang'=>$post['copy_lang']]), '', 2);
                } else {
                    $id_arr = [$insertId];
                    $lang_list = [$mark];
                    $this->langModel->where("id",'IN',$id_arr)->delete();
                    $this->langModel->afterDel($id_arr, $lang_list);
                    $this->error("同步数据失败，请重新操作");
                }
            }else{
                $this->error("操作失败");
            }
            exit;
        }

        $assign_data = [];
        $assign_data['languagemark'] = $this->langMarkModel
            ->field('title,mark,cn_title')
            ->order('sort_order asc, pinyin asc')
            ->select(); // 多国语言列表
        $assign_data['main_lang'] = get_main_lang(); // 主体语言（第一个存在的语言）

        $this->assign($assign_data);

        return $this->fetch();
    }
    
    /**
     * 多语言 - 编辑
     */
    public function edit()
    {
        if (IS_POST) {
            $post = input('post.');
            $post['id'] = eyIntval($post['id']);
            if(!empty($post['id'])){
                $is_home_default = intval($post['is_home_default']);
                $mark = trim($post['mark']);
                
                $count = $this->langModel->where([
                    'mark'=>$mark,
                    'id'=>['NEQ', $post['id']]
                ])->count();
                if (!empty($count)) {
                    $this->error('该语言已存在，请检查');
                }

                /*组装存储数据*/
                $nowData = array(
                    'is_home_default'    => $is_home_default,
                    'update_time'    => getTime(),
                );
                $saveData = array_merge($post, $nowData);
                /*--end*/
                $r = $this->langModel->save($saveData, ['id'=>$post['id']]);
                if ($r) {
                    /*默认语言的设置*/
                    if (1 == $is_home_default) { // 设置默认语言，只允许有一个是默认，其他取消
                        $this->langModel->where('id','NEQ',$post['id'])->update([
                            'is_home_default' => 0,
                            'update_time' => getTime(),
                        ]);
                        /*多语言 设置默认前台语言*/
                        if (is_language()) {
                            $langRow = \think\Db::name('language')->order('id asc')
                                ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                                ->select();
                            foreach ($langRow as $key => $val) {
                                tpCache('system', ['system_home_default_lang'=>$mark], $val['mark']);
                            }
                        } else { // 单语言
                            tpCache('system', ['system_home_default_lang'=>$mark]);
                        }
                        /*--end*/
                    } else { // 默认语言取消之后，自动将第一个语言设置为默认
                        $count = Db::name('language')->where(['is_home_default'=>1])->count();
                        if (empty($count)) {
                            $langInfo = Db::name('language')->field('id,mark')->order('id asc')->limit(1)->find();
                            $this->langModel->where('id','eq',$langInfo['id'])->update([
                                'is_home_default' => 1,
                                'update_time' => getTime(),
                            ]);
                            /*多语言 设置默认前台语言*/
                            if (is_language()) {
                                $langRow = \think\Db::name('language')->order('id asc')
                                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                                    ->select();
                                foreach ($langRow as $key => $val) {
                                    tpCache('system', ['system_home_default_lang'=>$langInfo['mark']], $val['mark']);
                                }
                            } else { // 单语言
                                tpCache('system', ['system_home_default_lang'=>$langInfo['mark']]);
                            }
                            /*--end*/
                        }
                    }
                    /*--end*/
                    adminLog('编辑多语言：'.$post['title']); // 写入操作日志
                    $this->success("操作成功!", url('Language/index'));
                }
            }
            $this->error("操作失败!");
        }

        $id = input('id/d', 0);
        $row = $this->langModel->find($id);
        $row['cn_title'] = Db::name('language_mark')->where([
                'mark'  => $row['mark']
            ])->getField('cn_title');
        if (empty($row)) {
            $this->error('数据不存在，请联系管理员！');
            exit;
        }

        $this->assign('row',$row);

        return $this->fetch();
    }
    
    /**
     * 多语言 - 删除文档
     */
    public function del()
    {
        //防止php超时
        function_exists('set_time_limit') && set_time_limit(0);
        
        $this->language_access(); // 多语言功能操作权限

        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if(!empty($id_arr)){

            /*不允许删除默认语言*/
            $count = $this->langModel->where([
                'id'    => ['IN', $id_arr],
                'is_home_default' => 1,
            ])->count();
            if (!empty($count)) {
                $this->error('禁止删除前台默认语言');
            }
            /*--end*/

            $result = $this->langModel->where("id",'IN',$id_arr)->select();
            $title_list = get_arr_column($result, 'title');
            $lang_list = get_arr_column($result, 'mark');

            $r = $this->langModel->where("id",'IN',$id_arr)->delete();
            if($r){
                $this->langModel->afterDel($id_arr, $lang_list);
                adminLog('删除多语言：'.implode(',', $title_list));
                $this->success("删除成功!");
            }else{
                $this->error("删除失败!");
            }
        }else{
            $this->error("参数有误!");
        }
    }

    /**
     * 模板栏目变量 - 列表
     */
    public function customvar_arctype()
    {
        /*同步数据到模板变量*/
        $this->syn_langattr();
        /*--end*/

        $list = array();
        $keywords = input('keywords/s');

        $map = array('attr_group'=>'arctype','is_del'=>0);
        if (!empty($keywords)) {
            $map['attr_name'] = array('LIKE', "%{$keywords}%");
        }

        $langAttribute_db = Db::name('language_attribute');
        $count = $langAttribute_db->where($map)->count('attr_id');// 查询满足要求的总记录数
        $pageObj = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $langAttribute_db->where($map)->order('attr_id asc')->limit($pageObj->firstRow.','.$pageObj->listRows)->select();

        $bindAttrName = []; // 当前页被绑定的所有栏目ID
        foreach ($list as $key => $val) {
            if ('arctype' == $val['attr_group']) {
                array_push($bindAttrName, $val['attr_name']);
            }
        }

        $pageStr = $pageObj->show(); // 分页显示输出
        $this->assign('list', $list); // 赋值数据集
        $this->assign('pageStr', $pageStr); // 赋值分页输出
        $this->assign('pageObj', $pageObj); // 赋值分页对象

        $arctypeList = [];
        $bindAttrList = [];
        if (!empty($bindAttrName)) {
            $bindAttrName = array_unique($bindAttrName);

            /*获取每个栏目ID关联的栏目列表*/
            $bindAttrList =Db::name('language_attr')->field('attr_name,attr_value,lang')
                ->where('attr_name','IN',$bindAttrName)
                ->select();
            $arctypeids = get_arr_column($bindAttrList, 'attr_value');
            $bindAttrList = group_same_key($bindAttrList, 'attr_name');
            /*--end*/

            /*获取指定栏目的信息*/
            $arctypeList = Db::name('arctype')->field('id,typename')
                ->where('id','IN',$arctypeids)
                ->getAllWithIndex('id');
            /*--end*/
        }
        $this->assign('bindAttrList', $bindAttrList);
        $this->assign('arctypeList', $arctypeList);

        $this->assign('main_lang', $this->main_lang);
        $this->assign('admin_lang', $this->admin_lang);

        return $this->fetch();
    }

    /**
     * 同步数据到多语言模板变量表
     */
    private function syn_langattr()
    {
        $this->syn_langattr_arctype();
        $this->syn_langattr_gbookattribute();
        $this->syn_langattr_proattribute();
        $this->syn_langattr_ad();
        $this->syn_langattr_ad_position();
    }

    /**
     * 同步栏目到多语言模板变量表
     */
    private function syn_langattr_arctype()
    {
        $attr_group = 'arctype';
        $arctype_arr = array(); // 栏目数组
        $addData = array(); // 新增数据存储
        $updateData = array(); // 更新数据存储
        $addAttrData = array(); // 新增模板栏目变量数据存储
        $langAttributeRow = $this->langAttributeModel->where('attr_group',$attr_group)->getAllWithIndex('attr_name');
        $result = Db::name('arctype')->where('lang',$this->main_lang)->order('id asc')->select();
        //  栏目表ey_arctype对比多语言模板变量表ey_language_attribute，检测两边数据是否一致
        foreach($result as $k=>$v){
            // 对比结果：栏目表有，多语言模板变量表没有
            $attr_name = 'tid'.$v['id'];
            $arctype_arr[] = $attr_name;
            $data = array(
                'attr_title'    => $v['typename'],
                'attr_name'     => $attr_name,
                'attr_group'    => $attr_group,
                'is_del'        => isset($v['is_del']) ? $v['is_del'] : 0,
            );
            if(empty($langAttributeRow[$attr_name])){ // 新增栏目之后进行同步到多语言模板变量
                $data['add_time'] = getTime();
                $data['update_time'] = getTime();
                $addData[] = $data;
                $addAttrData[] = [
                    'attr_name'    => $attr_name,
                    'attr_value' => $v['id'],
                    'lang'  => $this->main_lang,
                    'attr_group'    => $attr_group,
                    'add_time'  => getTime(),
                    'update_time'   => getTime(),
                ]; 
            } else { // 更新
                if ($langAttributeRow[$attr_name]['attr_title'] != $v['typename']) {
                    $updateData[] = [
                        'attr_id'   => $langAttributeRow[$attr_name]['attr_id'],
                        'attr_title'   => $v['typename'],
                        'is_del'        => $v['is_del'],
                        'update_time'   => getTime(),
                    ];
                }
            }
        }
        if (!empty($addData)) {
            $this->langAttributeModel->saveAll($addData);
        }
        if (!empty($addAttrData)) {
            $this->langAttrModel->saveAll($addAttrData);
        }
        if (!empty($updateData)) {
            $this->langAttributeModel->saveAll($updateData);
        }
        //多语言模板变量表有，栏目表没有，清除多余的模板变量
        /*foreach($langAttributeRow as $k => $v){
            $attr_name = $v['attr_name'];
            if (!in_array($attr_name, $arctype_arr)) {
                $this->langAttributeModel->where('attr_name',$attr_name)->delete();
                $this->langAttrModel->where('attr_name',$v['attr_name'])->delete();
            }
        }*/
    }

    /**
     * 同步留言属性到多语言模板变量表
     */
    private function syn_langattr_gbookattribute()
    {
        $attr_group = 'guestbook_attribute';
        $gbookAttr_arr = array(); // 留言属性数组
        $addData = array(); // 新增数据存储
        $updateData = array(); // 更新数据存储
        $addAttrData = array(); // 新增模板留言属性变量数据存储
        $langAttributeRow = $this->langAttributeModel->where('attr_group',$attr_group)->getAllWithIndex('attr_name');
        $result = Db::name('guestbook_attribute')->where('lang',$this->main_lang)->order('attr_id asc')->select();
        //  栏目表ey_arctype对比多语言模板变量表ey_language_attribute，检测两边数据是否一致
        foreach($result as $k=>$v){
            // 对比结果：栏目表有，多语言模板变量表没有
            $attr_name = 'attr_'.$v['attr_id'];
            $gbookAttr_arr[] = $attr_name;
            $data = array(
                'attr_title'    => $v['attr_name'],
                'attr_name'     => $attr_name,
                'attr_group'    => $attr_group,
                'is_del'        => isset($v['is_del']) ? $v['is_del'] : 0,
            );
            if(empty($langAttributeRow[$attr_name])){ // 新增留言属性之后进行同步到多语言模板变量
                $data['add_time'] = getTime();
                $data['update_time'] = getTime();
                $addData[] = $data;
                $addAttrData[] = [
                    'attr_name'    => $attr_name,
                    'attr_value' => $v['attr_id'],
                    'lang'  => $this->main_lang,
                    'attr_group'    => $attr_group,
                    'add_time'  => getTime(),
                    'update_time'   => getTime(),
                ]; 
            } else { // 更新
                if ($langAttributeRow[$attr_name]['attr_title'] != $v['attr_name']) {
                    $updateData[] = [
                        'attr_id'   => $langAttributeRow[$attr_name]['attr_id'],
                        'attr_title'   => $v['attr_name'],
                        'is_del'        => $v['is_del'],
                        'update_time'   => getTime(),
                    ];
                }
            }
        }
        if (!empty($addData)) {
            $this->langAttributeModel->saveAll($addData);
        }
        if (!empty($addAttrData)) {
            $this->langAttrModel->saveAll($addAttrData);
        }
        if (!empty($updateData)) {
            $this->langAttributeModel->saveAll($updateData);
        }
    }

    /**
     * 同步产品属性到多语言模板变量表
     */
    private function syn_langattr_proattribute()
    {
        $attr_group = 'product_attribute';
        $proAttr_arr = array(); // 产品属性数组
        $addData = array(); // 新增数据存储
        $updateData = array(); // 更新数据存储
        $addAttrData = array(); // 新增模板留言属性变量数据存储
        $langAttributeRow = $this->langAttributeModel->where('attr_group',$attr_group)->getAllWithIndex('attr_name');
        $result = Db::name('product_attribute')->where('lang',$this->main_lang)->order('attr_id asc')->select();
        //  栏目表ey_arctype对比多语言模板变量表ey_language_attribute，检测两边数据是否一致
        foreach($result as $k=>$v){
            // 对比结果：栏目表有，多语言模板变量表没有
            $attr_name = 'attr_'.$v['attr_id'];
            $proAttr_arr[] = $attr_name;
            $data = array(
                'attr_title'    => $v['attr_name'],
                'attr_name'     => $attr_name,
                'attr_group'    => $attr_group,
                'is_del'        => isset($v['is_del']) ? $v['is_del'] : 0,
            );
            if(empty($langAttributeRow[$attr_name])){ // 新增产品属性之后进行同步到多语言模板变量
                $data['add_time'] = getTime();
                $data['update_time'] = getTime();
                $addData[] = $data;
                $addAttrData[] = [
                    'attr_name'    => $attr_name,
                    'attr_value' => $v['attr_id'],
                    'lang'  => $this->main_lang,
                    'attr_group'    => $attr_group,
                    'add_time'  => getTime(),
                    'update_time'   => getTime(),
                ]; 
            } else { // 更新
                if ($langAttributeRow[$attr_name]['attr_title'] != $v['attr_name']) {
                    $updateData[] = [
                        'attr_id'   => $langAttributeRow[$attr_name]['attr_id'],
                        'attr_title'   => $v['attr_name'],
                        'is_del'        => $v['is_del'],
                        'update_time'   => getTime(),
                    ];
                }
            }
        }
        if (!empty($addData)) {
            $this->langAttributeModel->saveAll($addData);
        }
        if (!empty($addAttrData)) {
            $this->langAttrModel->saveAll($addAttrData);
        }
        if (!empty($updateData)) {
            $this->langAttributeModel->saveAll($updateData);
        }
    }

    /**
     * 同步广告到多语言模板变量表
     */
    private function syn_langattr_ad()
    {
        $attr_group = 'ad';
        $ad_arr = array(); // 广告数组
        $addData = array(); // 新增数据存储
        $updateData = array(); // 更新数据存储
        $addAttrData = array(); // 新增模板广告变量数据存储
        $langAttributeRow = $this->langAttributeModel->where('attr_group',$attr_group)->getAllWithIndex('attr_name');
        $result = Db::name('ad')->where('lang',$this->main_lang)->order('id asc')->select();
        //  广告表ey_ad对比多语言模板变量表ey_language_attribute，检测两边数据是否一致
        foreach($result as $k=>$v){
            // 对比结果：广告表有，多语言模板变量表没有
            $attr_name = 'ad'.$v['id'];
            $ad_arr[] = $attr_name;
            $data = array(
                'attr_title'    => $v['title'],
                'attr_name'     => $attr_name,
                'attr_group'    => $attr_group,
                'is_del'        => isset($v['is_del']) ? $v['is_del'] : 0,
            );
            if(empty($langAttributeRow[$attr_name])){ // 新增广告之后进行同步到多语言模板变量
                $data['add_time'] = getTime();
                $data['update_time'] = getTime();
                $addData[] = $data;
                $addAttrData[] = [
                    'attr_name'    => $attr_name,
                    'attr_value' => $v['id'],
                    'lang'  => $this->main_lang,
                    'attr_group'    => $attr_group,
                    'add_time'  => getTime(),
                    'update_time'   => getTime(),
                ]; 
            } else { // 更新
                if ($langAttributeRow[$attr_name]['attr_title'] != $v['title']) {
                    $updateData[] = [
                        'attr_id'   => $langAttributeRow[$attr_name]['attr_id'],
                        'attr_title'   => $v['title'],
                        'is_del'        => $v['is_del'],
                        'update_time'   => getTime(),
                    ];
                }
            }
        }
        if (!empty($addData)) {
            $this->langAttributeModel->saveAll($addData);
        }
        if (!empty($addAttrData)) {
            $this->langAttrModel->saveAll($addAttrData);
        }
        if (!empty($updateData)) {
            $this->langAttributeModel->saveAll($updateData);
        }
    }

    /**
     * 同步广告到多语言模板变量表
     */
    private function syn_langattr_ad_position()
    {
        $attr_group = 'ad_position';
        $adposition_arr = array(); // 广告位置数组
        $addData = array(); // 新增数据存储
        $updateData = array(); // 更新数据存储
        $addAttrData = array(); // 新增模板广告变量数据存储
        $langAttributeRow = $this->langAttributeModel->where('attr_group',$attr_group)->getAllWithIndex('attr_name');
        $result = Db::name('ad_position')->where('lang',$this->main_lang)->order('id asc')->select();
        //  广告位置表ey_ad对比多语言模板变量表ey_language_attribute，检测两边数据是否一致
        foreach($result as $k=>$v){
            // 对比结果：广告位置表有，多语言模板变量表没有
            $attr_name = 'adp'.$v['id'];
            $adposition_arr[] = $attr_name;
            $data = array(
                'attr_title'    => $v['title'],
                'attr_name'     => $attr_name,
                'attr_group'    => $attr_group,
                'is_del'        => isset($v['is_del']) ? $v['is_del'] : 0,
            );
            if(empty($langAttributeRow[$attr_name])){ // 新增广告位置之后进行同步到多语言模板变量
                $data['add_time'] = getTime();
                $data['update_time'] = getTime();
                $addData[] = $data;
                $addAttrData[] = [
                    'attr_name'    => $attr_name,
                    'attr_value' => $v['id'],
                    'lang'  => $this->main_lang,
                    'attr_group'    => $attr_group,
                    'add_time'  => getTime(),
                    'update_time'   => getTime(),
                ]; 
            } else { // 更新
                if ($langAttributeRow[$attr_name]['attr_title'] != $v['title']) {
                    $updateData[] = [
                        'attr_id'   => $langAttributeRow[$attr_name]['attr_id'],
                        'attr_title'   => $v['title'],
                        'is_del'        => $v['is_del'],
                        'update_time'   => getTime(),
                    ];
                }
            }
        }
        if (!empty($addData)) {
            $this->langAttributeModel->saveAll($addData);
        }
        if (!empty($addAttrData)) {
            $this->langAttrModel->saveAll($addAttrData);
        }
        if (!empty($updateData)) {
            $this->langAttributeModel->saveAll($updateData);
        }
    }

    /**
     * 关联绑定栏目
     */
    public function customvar_bind()
    {
        if (IS_POST) {
            $attr_id = input('post.attr_id/d', '');
            $typeid = input('post.typeid/d', '');

            $row = $this->langAttributeModel->where('attr_group','arctype')->find($attr_id);
            if (!empty($row)) {
                $attr_name = $row['attr_name'];
                $row2 = $this->langAttrModel->where([
                    'attr_name'    => $attr_name,
                    'attr_group'    => 'arctype',
                    'lang'  => $this->admin_lang,
                ])->find();
                if (!empty($row2)) {
                    $r = $this->langAttrModel->where('id', $row2['id'])
                        ->update([
                            'attr_value' => $typeid,
                            'update_time' => getTime(),
                        ]);
                } else {
                    $r = $this->langAttrModel->add([
                            'attr_name'    => $attr_name,
                            'attr_value' => $typeid,
                            'lang'  => $this->admin_lang,
                            'add_time'  => getTime(),
                            'update_time' => getTime(),
                        ]);
                }
                if (false != $r) {
                    $this->success('操作成功');
                }
            }
            $this->error('操作失败');
        }

        $attr_id = input('param.attr_id/d', '');
        $this->assign('attr_id',$attr_id);

        /*所有栏目列表*/
        $arctype_max_level = intval(config('global.arctype_max_level'));
        $arctypeLogic = new ArctypeLogic();
        $select_html = $arctypeLogic->arctype_list(0, 0, true, $arctype_max_level);
        $this->assign('select_html',$select_html);
        /*--end*/

        return $this->fetch();
    }

    /**
     * 模板语言变量
     */
    public function pack_index()
    {
        $list = array();
        $param = input('param.');
        $keywords = input('keywords/s');
        $condition = array();
        // 应用搜索条件
        foreach (['keywords'] as $key) {
            if (isset($param[$key]) && $param[$key] !== '') {
                if ($key == 'keywords') {
                    $condition['a.name|a.value'] = array('LIKE', "%{$param[$key]}%");
                } else {
                    $tmp_key = 'a.'.$key;
                    $condition[$tmp_key] = array('eq', $param[$key]);
                }
            }
        }

        // 多语言
        $condition['a.lang'] = $this->admin_lang;
        $condition['a.is_syn'] = 0;

        $pack_db =  Db::name('language_pack');
        $count = $pack_db->alias('a')->where($condition)->count();// 查询满足要求的总记录数
        $pageObj = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $pack_db->alias('a')
            ->where($condition)
            ->order('id desc')
            ->limit($pageObj->firstRow.','.$pageObj->listRows)
            ->select();

        $this->assign('pageStr',$pageObj->show());// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pageObj',$pageObj);// 赋值分页对象

        return $this->fetch();
    }

    /**
     * 模板语言变量 - 新增
     */
    public function pack_add()
    {
        if (IS_POST) {
            $name = strtolower(input('post.name/s'));
            $values = input('post.value/a', '', 'strip_sql');

            // 检测变量名
            if (preg_match('/^(sys)(\d+)$/i', $name)) {
                $this->error('禁止使用sys+数字的变量名，请更换');
            }

            $count = Db::name('language_pack')->where([
                    'name'  => $name
                ])->count();
            if (!empty($count)) {
                $this->error('该变量名已存在，请检查');
            }

            $saveData = [];
            $languageRow = Db::name('language')->field('mark')
                ->order('sort_order asc,id asc')
                ->select();
            foreach ($languageRow as $key => $val) {
                $saveData[] = [
                    'name' => $name,
                    'value' => !empty($values[$val['mark']]) ? $values[$val['mark']] : '',
                    'lang'  => $val['mark'],
                    'sort_order'    => 100,
                    'add_time'  => getTime(),
                    'update_time'  => getTime(),
                ];
            }

            $languagePack_M = model('LanguagePack');
            $r = $languagePack_M->saveAll($saveData);

            if (false !== $r) {
                $this->createLangFile(); // 生成语言包文件
                adminLog('新增模板语言变量：'.$name); // 写入操作日志
                $this->success("操作成功", url('Language/pack_index'));
            }else{
                $this->error("操作失败");
            }
        }

        $languageRow = Db::name('language')->field('mark,title')
            ->order('sort_order asc,id asc')
            ->select();
        $this->assign('languageRow', $languageRow);

        return $this->fetch();
    }

    /**
     * 模板语言变量 - 批量新增
     */
    public function pack_batch_add()
    {
        //防止php超时
        function_exists('set_time_limit') && set_time_limit(0);
        
        if (IS_POST) {
            $admin_lang = $this->admin_lang;
            $main_lang = $this->main_lang;
            $content = input('post.content/s', '', 'strip_sql');
            $languagePack_M = model('LanguagePack');

            $tmp_content = trim(str_replace(PHP_EOL,"",$content)); //去掉回车换行符号
            if (empty($tmp_content)) {
                $this->error('数据不能为空！');
            }

            // 语言列表
            $languageRow = Db::name('language')->field('mark')
                ->order('id asc')
                ->select();

            $r = false;
            $time = getTime();
            $data = explode(PHP_EOL, $content);
            foreach ($data as $key => $val) {
                if (empty($val) || "" === trim($val)) {
                    continue;
                }
                $saveData = [];
                $values = explode('=', str_replace('＝', '=', $val));
                $saveData = [
                    'value' => !empty($values[0]) ? $values[0] : '',
                    'lang'  => $main_lang,
                    'sort_order'    => 100,
                    'add_time'  => $time,
                    'update_time'  => getTime(),
                ];
                $r = $languagePack_M->insertGetId($saveData);
                $insertId = $r;
                if (false != $insertId) {
                    $name = 'lang'.$insertId;
                    $r = $languagePack_M->update([
                            // 'name'  => Db::raw("CONCAT('lang',id)"),
                            'name'  => $name,
                        ], ['id'  => $insertId]);
                    if (false != $r) {
                        $saveDataAll = [];
                        foreach ($languageRow as $k2 => $v2) {
                            if (0 < intval($k2)) {
                                $saveDataAll[] = [
                                    'name'  => $name,
                                    'value' => !empty($values[$k2]) ? $values[$k2] : '',
                                    'lang'  => $v2['mark'],
                                    'sort_order'    => 100,
                                    'add_time'  => $time,
                                    'update_time'  => getTime(),
                                ];
                            }
                        }
                        !empty($saveDataAll) && $languagePack_M->saveAll($saveDataAll);
                    }
                }
            }

            if (false !== $r) {
                $this->createLangFile(); // 生成语言包文件
                adminLog('新增模板语言变量：'.str_replace(PHP_EOL, '|', $content)); // 写入操作日志
                $this->success("操作成功", url('Language/pack_index'));
            }else{
                model('LanguagePack')->where(['add_time'=>$time])->delete();
                $this->error("操作失败");
            }
        }

        $languageRow = Db::name('language')->field('mark,title')
            ->order('id asc')
            ->select();
        $languageStr = implode('=', get_arr_column($languageRow, 'title')).PHP_EOL;
        $this->assign('languageStr', $languageStr);

        return $this->fetch();
    }

    /**
     * 模板语言变量 - 批量新增
     */
    // public function pack_batch_add()
    // {
    //     if (IS_POST) {
    //         $admin_lang = $this->admin_lang;
    //         $content = input('post.content/s', '', 'strip_sql');

    //         $tmp_content = trim(str_replace(PHP_EOL,"",$content)); //去掉回车换行符号
    //         if (empty($tmp_content)) {
    //             $this->error('数据不能为空！');
    //         }

    //         $time = getTime();
    //         $saveData = [];
    //         $data = explode(PHP_EOL, $content);
    //         foreach ($data as $key => $val) {
    //             if (empty($val) || "" === trim($val)) {
    //                 continue;
    //             }
    //             $saveData[] = [
    //                 'value' => $val,
    //                 'lang'  => $admin_lang,
    //                 'sort_order'    => 100,
    //                 'add_time'  => $time,
    //                 'update_time'  => getTime(),
    //             ];
    //         }

    //         $r = false;
    //         $languagePack_M = model('LanguagePack');
    //         if (!empty($saveData)) {
    //             $r = $languagePack_M->saveAll($saveData);
    //             if (false != $r) {
    //                 $r = $languagePack_M->update([
    //                         'name'  => Db::raw("CONCAT('language',id)"),
    //                     ], ['add_time'  => $time]);
    //             }
    //         }

    //         if (false !== $r) {
    //             /*同步到其他多语言*/
    //             $languageRow = Db::name('language')->field('mark')
    //                 ->where('mark','NEQ',$admin_lang)
    //                 ->order('sort_order asc,id asc')
    //                 ->select();
    //             if (!empty($languageRow)) {
    //                 $synData = Db::name('language_pack')->field('id,lang', true)
    //                     ->where([
    //                         'add_time'  => $time,
    //                         'lang'  => $admin_lang,
    //                     ])->select();
    //                 $synSaveData = [];
    //                 foreach ($languageRow as $k1 => $v1) {
    //                     foreach ($synData as $k2 => $v2) {
    //                         $v2['lang']   = $v1['mark'];
    //                         $synSaveData[] = $v2;
    //                     }
    //                 }
    //                 $languagePack_M->saveAll($synSaveData);
    //             }
    //             /*--end*/
    //             $this->createLangFile(); // 生成语言包文件
    //             adminLog('新增模板语言变量：'.str_replace(PHP_EOL, '|', $content)); // 写入操作日志
    //             $this->success("操作成功", url('Language/pack_index'));
    //         }else{
    //             model('LanguagePack')->where(['add_time'=>$time])->delete();
    //             $this->error("操作失败");
    //         }
    //     }

    //     return $this->fetch();
    // }

    /**
     * 模板语言变量 - 编辑
     */
    public function pack_edit()
    {
        $admin_lang = $this->admin_lang;

        $official = input('official');

        if (IS_POST) {
            $id = input('post.id/d');
            $name = strtolower(input('post.name/s'));
            $values = input('post.value/a', '', 'strip_sql');
            $languagepack_db = Db::name('language_pack');

            // 旧的变量名
            $old_name = $languagepack_db->where([
                    'id'  => $id,
                    'lang'    => $admin_lang,
                ])->getField('name');

            // 检测变量名
            if ($old_name != $name && preg_match('/^(sys)(\d+)$/i', $name)) {
                $this->error('禁止使用sys+数字的变量名，请更换');
            }

            $count = $languagepack_db->where([
                    'name'  => $name,
                    'id'    => ['NEQ', $id],
                    'lang'  => $admin_lang,
                ])->count();
            if (!empty($count)) {
                $this->error('该变量名已存在，请检查');
            }

            // 所有语言对应此变量的id
            $idRow = $languagepack_db->field('id,lang')
                ->where(['name'=>$old_name])
                ->getAllWithIndex('lang');

            // 更新变量值
            $updateData = [];
            foreach ($values as $key => $val) {
                $updateData[] = [
                    'id' => $idRow[$key]['id'],
                    'name' => $name,
                    'lang' => $key,
                    'value' => $val,
                    'update_time'   => getTime(),
                ];
            }
            $r = model('LanguagePack')->saveAll($updateData);
            if (false !== $r) {
                $this->createLangFile(); // 生成语言包文件
                adminLog('编辑模板语言变量：'.$name); // 写入操作日志
                if (1 == $official) {
                    $gourl = url('Language/official_pack_index');
                } else {
                    $gourl = url('Language/pack_index');
                }
                $this->success("操作成功", $gourl);
            }else{
                $this->error("操作失败");
            }
        }

        $id = input('id/d');
        $row = Db::name('language_pack')->where([
                'id'    => $id,
                'lang'  => $admin_lang,
            ])->find();
        if (empty($row)) {
            $this->error('数据不存在，请联系管理员！');
            exit;
        }
        $this->assign('row',$row);

        // 语言列表
        $languageRow = Db::name('language')->field('mark,title')
            ->order('sort_order asc,id asc')
            ->select();
        $this->assign('languageRow', $languageRow);

        // 变量值列表
        $values = Db::name('language_pack')->field('lang,value')->where([
                'name'    => $row['name'],
            ])->getAllWithIndex('lang');
        $this->assign('values', $values);

        $this->assign('official', $official);

        return $this->fetch();
    }
    
    /**
     * 模板语言变量 - 删除
     */
    public function pack_del()
    {
        if (IS_POST) {
            $id_arr = input('del_id/a');
            $id_arr = eyIntval($id_arr);
            if(!empty($id_arr)){
                $languagepack_db = Db::name('language_pack');

                $count = $languagepack_db->where([
                        'id'    => ['IN', $id_arr],
                        'lang'  => $this->admin_lang,
                        'is_syn'=> 1,
                    ])->count();
                if (!empty($count)) {
                    $this->error('官方同步语言包，禁止删除');
                }

                $names = $languagepack_db->where([
                        'id'    => ['IN', $id_arr],
                        'lang'  => $this->admin_lang,
                    ])->column('name');

                $r = $languagepack_db->where([
                        'name'    => ['IN', $names],
                    ])->delete();
                if($r){
                    $this->createLangFile(); // 生成语言包文件
                    adminLog('删除模板语言变量：'.implode(',', $names));
                    $this->success('删除成功');
                }else{
                    $this->error('删除失败');
                }
            } else {
                $this->error('参数有误');
            }
        }
        $this->error('非法访问');
    }

    /**
     * 官方语言包变量
     */
    public function official_pack_index()
    {
        $list = array();
        $param = input('param.');
        $keywords = input('keywords/s');
        $condition = array();
        // 应用搜索条件
        foreach (['keywords'] as $key) {
            if (isset($param[$key]) && $param[$key] !== '') {
                if ($key == 'keywords') {
                    $condition['a.name|a.value'] = array('LIKE', "%{$param[$key]}%");
                } else {
                    $tmp_key = 'a.'.$key;
                    $condition[$tmp_key] = array('eq', $param[$key]);
                }
            }
        }

        // 多语言
        $condition['a.lang'] = $this->admin_lang;
        $condition['a.is_syn'] = 1;

        $pack_db =  Db::name('language_pack');
        $count = $pack_db->alias('a')->where($condition)->count();// 查询满足要求的总记录数
        $pageObj = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $pack_db->alias('a')
            ->where($condition)
            ->order('id desc')
            ->limit($pageObj->firstRow.','.$pageObj->listRows)
            ->select();

        $this->assign('pageStr',$pageObj->show());// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pageObj',$pageObj);// 赋值分页对象

        return $this->fetch();
    }

    /**
     * 生成语言包文件
     */
    private function createLangFile()
    {
        $result = [];
        $packRow = Db::name('language_pack')->field('name,value,lang')->order('lang asc, id asc')->select();
        foreach ($packRow as $key => $val) {
            $result[$val['lang']][$val['name']] = $val['value'];
        }
        foreach ($result as $key => $val) {
            file_put_contents( APP_PATH."lang/{$key}.php", "<?php\r\n\r\n"."return ".var_export($val,true).";" );
        }
    }

    /**
     * 自动同步官方语言包
     */
    public function official_pack_syn($lang = '')
    {
        $this->pack_syn(true);
        $this->success('同步成功', url('Language/official_pack_index'));
    }

    /**
     * 新增语言，系统自动同步官方语言包
     */
    public function add_lang_syn_pack($mark = '', $c_lang = '')
    {
        if (!empty($mark) && !empty($c_lang)) {
            $service_ey = config('service_ey');
            $query_str = 'L2luZGV4LnBocD9tPWFwaSZjPUxhbmd1YWdlJmE9c3luX3BhY2tfc2luZ2xlJg==';
            $values = array(            
                'lang'=>$mark, 
            );
            $url = base64_decode($service_ey).base64_decode($query_str).http_build_query($values);
            $context = stream_context_set_default(array('http' => array('timeout' => 3,'method'=>'GET')));
            $response = @file_get_contents($url,false,$context);
            $params = json_decode($response,true);
            if (is_array($params) && !empty($params)) {
                $saveData = [];
                foreach ($params as $key => $val) {
                    $saveData[] = [
                        'name'  => $val['name'],
                        'value'  => $val['value'],
                        'is_syn'    => 1,
                        'lang'  => $mark,
                        'sort_order'    => 100,
                        'add_time'  => getTime(),
                        'update_time'  => getTime(),
                    ];
                }
                if (!empty($saveData)) {
                    $r = $this->langPackModel->saveAll($saveData);
                    if ($r) {
                        /*同步官方语言包最后一次同步的ID*/
                        $language_db = Db::name('language');
                        $syn_pack_id = $language_db->where([
                                'mark'  => $c_lang,
                            ])->getField('syn_pack_id');
                        $language_db->where([
                                'mark'  => $mark,
                            ])->update([
                                'syn_pack_id'   => $syn_pack_id,
                                'update_time'   => getTime(),
                            ]);
                        /*--end*/
                        $this->createLangFile(); // 生成语言包文件
                        $this->success('同步成功', url('Language/index'));
                    }
                }
            }
        }

        $this->error("同步失败，稍后系统默认同步");
    }

    /**
     * 同步全部语言的官方语言包
     */
    public function pack_syn($return = false)
    {
        //防止超时
        function_exists('set_time_limit') && set_time_limit(0);

        $syn_pack_id = Db::name('language')->max('syn_pack_id');
        $service_ey = config('service_ey');
        $query_str = 'L2luZGV4LnBocD9tPWFwaSZjPUxhbmd1YWdlJmE9c3luX3BhY2tfbGlzdCY=';
        $values = array(            
            'pack_id'=>$syn_pack_id, 
        );
        $url = base64_decode($service_ey).base64_decode($query_str).http_build_query($values);
        $context = stream_context_set_default(array('http' => array('timeout' => 5,'method'=>'GET')));
        $response = @file_get_contents($url,false,$context);
        $params = json_decode($response,true);
        $list = !empty($params['list']) ? $params['list'] : [];
        if (false != $response && empty($list)) {
            $this->success("已是最新语言包");
        }

        if (!empty($list) && is_array($list)) {
            $new_syn_pack_id = $params['max_pack_id'];
            $saveData = [];
            $languageRow = Db::name('language')->field('mark')
                ->order('sort_order asc,id asc')
                ->select();
            foreach ($languageRow as $k1 => $v1) {
                foreach ($list['cn'] as $k2 => $v2) {
                    $name = !empty($list[$v1['mark']]) ? $list[$v1['mark']][$k2]['name'] : $v2['name'];
                    $value = !empty($list[$v1['mark']]) ? $list[$v1['mark']][$k2]['value'] : $v2['value'];
                    $lang = !empty($list[$v1['mark']]) ? $list[$v1['mark']][$k2]['lang'] : $v1['mark'];
                    $saveData[] = [
                        'name'  => $name,
                        'value'  => $value,
                        'is_syn'    => 1,
                        'lang'  => $lang,
                        'sort_order'    => 100,
                        'add_time'  => getTime(),
                        'update_time'  => getTime(),
                    ];
                }
            }

            if (!empty($saveData)) {
                $r = $this->langPackModel->saveAll($saveData);
                if (false !== $r) {
                    $this->langModel->where(['id'=>['gt',0]])->update([
                            'syn_pack_id' => $new_syn_pack_id,
                            'update_time'   => getTime(),
                        ]);
                    $this->createLangFile(); // 生成语言包文件
                    if (false === $return) {
                        $this->success('操作成功');
                    } else {
                        return true;
                    }
                }
            }
        }
        if (false === $return) {
            $this->error("操作失败");
        } else {
            return false;
        }
    }

    /**
     * 检测单个语言是否同步最新官方语言包
     */
    public function check_pack_syn()
    {
        //防止超时
        function_exists('set_time_limit') && set_time_limit(0);

        $syn_pack_id = Db::name('language')->max('syn_pack_id');
        $service_ey = config('service_ey');
        $query_str = 'L2luZGV4LnBocD9tPWFwaSZjPUxhbmd1YWdlJmE9Y2hlY2tfYWxsX25ld19wYWNrJg==';
        $values = array(            
            'pack_id'=>$syn_pack_id, 
        );
        $url = base64_decode($service_ey).base64_decode($query_str).http_build_query($values);
        $context = stream_context_set_default(array('http' => array('timeout' => 3,'method'=>'GET')));
        $response = @file_get_contents($url,false,$context);
        $params = json_decode($response,true);
        if (false != $params && 0 < intval($params)) {
            $this->error("有新版本语言包");
        } else {
            $this->success("已是最新语言包");
        }
    }
}