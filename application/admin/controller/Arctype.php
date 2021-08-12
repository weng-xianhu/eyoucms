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
use app\admin\logic\FieldLogic;

class Arctype extends Base
{
    public $fieldLogic;
    public $arctypeLogic;
    // 栏目对应模型ID
    public $arctype_channel_id = '';
    // 允许发布文档的模型ID
    public $allowReleaseChannel = array();
    // 禁用的目录名称
    public $disableDirname = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->fieldLogic          = new FieldLogic();
        $this->arctypeLogic        = new ArctypeLogic();
        $this->allowReleaseChannel = config('global.allow_release_channel');
        $this->arctype_channel_id  = config('global.arctype_channel_id');
        $this->disableDirname      = config('global.disable_dirname');
        $this->arctype_db  = Db::name('arctype');
        $this->archives = Db::name('archives');

        /*兼容每个用户的自定义字段，重新生成数据表字段缓存文件*/
        $arctypeFieldInfo = include DATA_PATH . 'schema/' . PREFIX . 'arctype.php';
        foreach (['weapp_code'] as $key => $val) {
            if (!isset($arctypeFieldInfo[$val])) {
                try {
                    schemaTable('arctype');
                } catch (\Exception $e) {}
                break;
            }
        }
        /*--end*/

        // 纠正栏目的topid字段值(v1.6.1节点去掉)
        $actionName = ACTION_NAME;
        if (in_array($actionName, ['add','edit'])) {
            $ajaxLogic = new \app\admin\logic\AjaxLogic;
            $ajaxLogic->admin_logic_arctype_topid();
        }
    }

    public function index()
    {
        $arctype_list = array();
        // 目录列表
        $where['is_del'] = '0'; // 回收站功能
        $arctype_list = $this->arctypeLogic->arctype_list(0, 0, false, 0, $where, false);
        /*foreach ($arctype_list as $key => $val) {
            if ($val['current_channel'] == 51 && 2 > $this->php_servicemeal && empty($val['has_children'])) {
                unset($arctype_list[$key]);
                continue;
            } else if ($val['current_channel'] == 5 && 1.5 > $this->php_servicemeal) {
                unset($arctype_list[$key]);
                continue;
            }
        }*/
        $this->assign('arctype_list', $arctype_list);

        /*多语言模式下，栏目ID显示主体语言的ID和属性title名称*/
        $main_arctype_list = [];
        if ($this->admin_lang != $this->main_lang) {
            $attr_values = get_arr_column($arctype_list, 'id');
            $languageAttrRow = Db::name('language_attr')->field('attr_name,attr_value')->where([
                    'attr_value'    => ['IN', $attr_values],
                    'attr_group'    => 'arctype',
                    'lang'          => $this->admin_lang,
                ])->getAllWithIndex('attr_value');
            $typeids = [];
            foreach ($languageAttrRow as $key => $val) {
                $tid_tmp = str_replace('tid', '', $val['attr_name']);
                array_push($typeids, intval($tid_tmp));
            }
            $main_ArctypeRow = Db::name('arctype')->field("id,typename,CONCAT('tid', id) AS attr_name")
                ->where([
                    'id'    => ['IN', $typeids],
                    'lang'  => $this->main_lang,
                ])->getAllWithIndex('attr_name');
            foreach ($arctype_list as $key => $val) {
                $key_tmp = !empty($languageAttrRow[$val['id']]['attr_name']) ? $languageAttrRow[$val['id']]['attr_name'] : '';
                $main_arctype_list[$val['id']] = [
                    'id'        => !empty($main_ArctypeRow[$key_tmp]['id']) ? $main_ArctypeRow[$key_tmp]['id'] : 0,
                    'typename'  => !empty($main_ArctypeRow[$key_tmp]['typename']) ? $main_ArctypeRow[$key_tmp]['typename'] : '',
                ];
            }
        }
        $this->assign('main_arctype_list', $main_arctype_list);
        /*end*/

        // 模型列表
        $channeltype_list = getChanneltypeList();
        $this->assign('channeltype_list', $channeltype_list);

        // 栏目最多级别
        $arctype_max_level = intval(config('global.arctype_max_level'));
        $this->assign('arctype_max_level', $arctype_max_level);
        
        /* 生成静态页面代码 */
        $typeid = input('param.typeid/d',0);
        $is_del = input('param.is_del/d',0);
        $this->assign('typeid',$typeid);
        $this->assign('is_del',$is_del);
        /* end */
        $recycle_switch = tpSetting('recycle.recycle_switch');
        $this->assign('recycle_switch', $recycle_switch);
        return $this->fetch();
    }
    
    /**
     * 新增
     */
    public function add()
    {
        //防止php超时
        function_exists('set_time_limit') && set_time_limit(0);
        
        $this->language_access(); // 多语言功能操作权限

        if (IS_POST) {
            $post = input('post.');
            if ($post) {
                // 问答模型只能存在一个
                if (51 == $post['current_channel']){
                    $ask_info = Db::name('arctype')->where(['current_channel' => $post['current_channel'], 'is_del' => 0])->order('id desc')->find();
                    if (!empty($ask_info)){
                        $this->error('问答模型只能创建一个栏目!');
                    }
                }
                /*目录名称*/
                $post['dirname'] = func_preg_replace([' ','　'], '', $post['dirname']);
                $dirname = $this->arctypeLogic->get_dirname($post['typename'], $post['dirname']);
                // 检测
                if (!empty($post['dirname']) && !$this->arctypeLogic->dirname_unique($post['dirname'])) {
                    $arctype_is_del = Db::name('arctype')->where(['dirname'=>$post['dirname'], 'lang'=>$this->admin_lang])->value('is_del');
                    if (empty($arctype_is_del)) {
                        $this->error('目录名称与系统内置冲突，请更改！');
                    } else {
                        $this->error('目录名称与回收站里的栏目冲突，请更改！');
                    }
                }
                /*--end*/
                $dirpath = rtrim($post['dirpath'],'/');
                /* ------临时代码，当能支持静态页面生成，再去掉 */
                $dirpath = $dirpath . '/' . $dirname;
                /* -----------end----------- */
                $typelink = !empty($post['is_part']) ? $post['typelink'] : '';
                /*封面图的本地/远程图片处理*/
                $is_remote = !empty($post['is_remote']) ? $post['is_remote'] : 0;
                $litpic = '';
                if ($is_remote == 1) {
                    $litpic = $post['litpic_remote'];
                } else {
                    $litpic = $post['litpic_local'];
                }
                /*--end*/
                // 获取顶级模型ID
                if (empty($post['parent_id'])) {
                    $channeltype = $post['current_channel'];
                    $topid = 0;
                } else {
                    $parentInfo = Db::name('arctype')->field('id,channeltype,topid')->where('id', $post['parent_id'])->find();
                    $channeltype = $parentInfo['channeltype'];
                    $topid = !empty($parentInfo['topid']) ? $parentInfo['topid'] : $parentInfo['id'];
                }
                /*SEO描述*/
                $seo_description = $post['seo_description'];
                /*--end*/
                /*处理自定义字段值*/
                $addonField = array();
                if (!empty($post['addonField'])) {
                    $addonField = $this->fieldLogic->handleAddonField($this->arctype_channel_id, $post['addonField']);
                }
                /*--end*/
                $newData = array(
                    'topid' => $topid,
                    'dirname' => $dirname,
                    'dirpath'   => $dirpath,
                    'typelink' => $typelink,
                    'litpic'    => $litpic,
                    'channeltype'   => $channeltype,
                    'current_channel' => $post['current_channel'],
                    'seo_keywords' => str_replace('，', ',', $post['seo_keywords']),
                    'seo_description' => $seo_description,
                    'admin_id'  => session('admin_info.admin_id'),
                    'lang'  => $this->admin_lang,
                    'sort_order'    => 100,
                    'add_time'  => getTime(),
                    'update_time'  => getTime(),
                );
                $data = array_merge($post, $newData, $addonField);
                $insertId = model('Arctype')->addData($data);
                if(false !== $insertId){
                    $_POST['id'] = $insertId;
                    
                    // 删除多余的问答栏目
                    if (51 == $post['current_channel']){
                        Db::name('arctype')->where(['current_channel' => $post['current_channel'], 'id' => ['NEQ', $insertId]])->delete();
                    }
                    
                    /*同步栏目ID到多语言的模板栏目变量里*/
                    $this->arctypeLogic->syn_add_language_attribute($insertId);
                    /*--end*/

                    adminLog('新增栏目：'.$data['typename']);

                    // 生成静态页面代码
                    $this->success("操作成功!", url('Arctype/index', ['typeid'=>$insertId]));
                    exit;
                }
            }
            $this->error("操作失败!");
            exit;
        }

        $assign_data = array();

        /* 模型 */
        $map = array(
            'status'    => 1,
        );
        $channeltype_list = model('Channeltype')->getAll('id,title,nid', $map, 'id');
        $this->assign('channeltype_list', $channeltype_list);

        // 新增栏目在指定的上一级栏目下
        $parent_id = input('param.parent_id/d');
        $grade = 0;
        $current_channel = '';
        $predirpath = ''; // 生成静态页面代码
        $ptypename = '';
        if (0 < $parent_id) {
            $info = Db::name('arctype')->where(array('id'=>$parent_id))->find();
            if ($info) {
                // 级别
                $grade = $info['grade'] + 1;
                // 菜单对应下的栏目
                // $selected = $info['id'];
                // 模型
                $current_channel = $info['current_channel'];
                // 上级目录
                $predirpath = $info['dirpath'];
                // 上级栏目名称
                $ptypename = $info['typename'];
            }
        }
        $this->assign('predirpath', $predirpath);
        $this->assign('parent_id', $parent_id);
        $this->assign('ptypename', $ptypename);
        $this->assign('grade',$grade);
        $this->assign('current_channel',$current_channel);
        
        /*发布文档的模型ID，用于是否显示文档模板列表*/
        $js_allow_channel_arr = '[';
        foreach ($this->allowReleaseChannel as $key => $val) {
            if (51 == $val) continue; // 问答模型
            if ($key > 0) {
                $js_allow_channel_arr .= ',';
            }
            $js_allow_channel_arr .= $val;
        }
        $js_allow_channel_arr = $js_allow_channel_arr.']';
        $this->assign('js_allow_channel_arr', $js_allow_channel_arr);
        /*--end*/

        /*模板列表*/
        $templateList = $this->ajax_getTemplateList('add');
        $this->assign('templateList', $templateList);
        /*--end*/

        /*自定义字段*/
        $assign_data['addonFieldExtList'] = model('Field')->getTabelFieldList(config('global.arctype_channel_id'));
        $assign_data['aid'] = 0;
        $assign_data['channeltype'] = 6;
        $assign_data['nid'] = 'arctype';
        /*--end*/

        $this->assign($assign_data);
        return $this->fetch();
    }
    
    /**
     * 编辑
     */
    public function edit()
    {
        if (IS_POST) {
            $post = input('post.');
            if (!empty($post['id'])) {
                //问答模型只能存在一个
                if (51 == $post['current_channel']){
                    $ask_info = Db::name('arctype')->where(['current_channel'=>$post['current_channel'], 'id'=>['NEQ', $post['id']], 'is_del'=>0])->find();
                    if (!empty($ask_info)){
                        $this->error('问答模型只能存在一个栏目，请删除!');
                    }
                }

                /*自己的上级不能是自己*/
                if (intval($post['id']) == intval($post['parent_id'])) {
                    $this->error('自己不能成为自己的上级栏目');
                }
                /*--end*/

                /*目录名称*/
                $post['dirname'] = func_preg_replace([' ', '　'], '', $post['dirname']);
                $dirname         = $this->arctypeLogic->get_dirname($post['typename'], $post['dirname'], $post['id']);
                // 检测
                if (!empty($post['dirname']) && !$this->arctypeLogic->dirname_unique($post['dirname'], $post['id'])) {
                    $arctype_is_del = Db::name('arctype')->where(['dirname'=>$post['dirname'], 'lang'=>$this->admin_lang])->value('is_del');
                    if (empty($arctype_is_del)) {
                        $this->error('目录名称与系统内置冲突，请更改！');
                    } else {
                        $this->error('目录名称与回收站里的栏目冲突，请更改！');
                    }
                }
                /*--end*/
                $dirpath  = rtrim($post['dirpath'], '/');
                /* ------临时代码，当能支持静态页面生成，再去掉 */
                $dirpath = $dirpath . '/' . $dirname;
                /* -----------end----------- */
                $typelink = !empty($post['is_part']) ? $post['typelink'] : '';
                /*封面图的本地/远程图片处理*/
                $is_remote = !empty($post['is_remote']) ? $post['is_remote'] : 0;
                $litpic    = '';
                if ($is_remote == 1) {
                    $litpic = $post['litpic_remote'];
                } else {
                    $litpic = $post['litpic_local'];
                }
                /*--end*/
                // 顶级栏目ID
                $topid = 0;
                // 最顶级模型ID
                $channeltype = $post['channeltype'];
                // 当前更改的等级
                $grade = $post['grade'];
                // 根据栏目ID获取最新的最顶级模型ID
                if (intval($post['parent_id']) > 0) {
                    $parentInfo = Db::name('arctype')->field('id,topid,grade,channeltype')->where('id', $post['parent_id'])->find();
                    $channeltype = $parentInfo['channeltype'];
                    $grade       = $parentInfo['grade'] + 1;
                    $topid = !empty($parentInfo['topid']) ? $parentInfo['topid'] : $parentInfo['id'];
                }
                /*SEO描述*/
                $seo_description = $post['seo_description'];
                /*--end*/

                /*处理自定义字段值*/
                $addonField = array();
                if (!empty($post['addonField'])) {
                    $addonField = $this->fieldLogic->handleAddonField($this->arctype_channel_id, $post['addonField']);
                }
                /*--end*/

                $newData = array(
                    'topid' => $topid,
                    'dirname' => $dirname,
                    'dirpath'   => $dirpath,
                    'typelink' => $typelink,
                    'litpic'    => $litpic,
                    'channeltype'   => $channeltype,
                    'grade' => $grade,
                    'seo_keywords' => str_replace('，', ',', $post['seo_keywords']),
                    'seo_description' => $seo_description,
                    'lang'  => $this->admin_lang,
                    'update_time'  => getTime(),
                );
                $data = array_merge($post, $newData, $addonField);
                $r = model('Arctype')->updateData($data);
                if(false !== $r){
                    
                    // 删除多余的问答栏目
                    if (51 == $post['current_channel']){
                        Db::name('arctype')->where(['current_channel' => $post['current_channel'], 'id' => ['NEQ', $post['id']]])->delete();
                    }

                    adminLog('编辑栏目：'.$data['typename']);

                    // 生成静态页面代码
                    $this->success("操作成功!", url('Arctype/index', ['typeid'=>$post['id']]));
                    exit;
                }
            }
            $this->error("操作失败!");
            exit;
        }

        $assign_data = array();

        $id = input('id/d');
        $info = Db::name('arctype')->where([
                'id'    => $id,
                'lang'  => $this->admin_lang,
            ])->find();
        if (empty($info)) {
            $this->error('数据不存在，请联系管理员！');
            exit;
        }
        // 栏目图片处理
        if (is_http_url($info['litpic'])) {
            $info['is_remote'] = 1;
            $info['litpic_remote'] = handle_subdir_pic($info['litpic']);
        } else {
            $info['is_remote'] = 0;
            $info['litpic_local'] = handle_subdir_pic($info['litpic']);
        }
        $this->assign('field',$info);

        // 获得上级目录路径
        if (!empty($info['parent_id'])) {
            $predirpath = Db::name('arctype')->where(['id'=>$info['parent_id']])->value('dirpath');
        } else {
            $predirpath = ''; // 生成静态页面代码
        }
        $this->assign('predirpath',$predirpath);

        // 是否有子栏目
        $is_edit_parent_id = 1; // 是否可编辑所属栏目
        $arctype_max_level = intval(config('global.arctype_max_level'));
        $hierarchy = model('Arctype')->getHierarchy($id);
        if ($hierarchy >= $arctype_max_level) {
            $is_edit_parent_id = 0; // 不可编辑，因为可能会导致超过所限制的最大层级
            $select_html = Db::name('arctype')->where('id', $info['parent_id'])->getField('typename');
            $select_html = !empty($select_html) ? $select_html : '顶级栏目';
        } else {
            // 所属栏目
            $select_html       = '<option value="0" data-grade="-1" data-dirpath="">顶级栏目</option>';
            $selected          = $info['parent_id'];
            $arctypeWhere      = ['is_del' => 0];
            $options           = $this->arctypeLogic->arctype_list(0, $selected, false, $arctype_max_level - $hierarchy, $arctypeWhere);
            foreach ($options AS $var)
            {
                $select_html .= '<option value="' . $var['id'] . '" data-grade="' . $var['grade'] . '" data-dirpath="'.$var['dirpath'].'"';
                $select_html .= ($selected == $var['id']) ? "selected='true'" : '';
                $select_html .= ($id == $var['id'] || ($hierarchy + $var['grade'] > $arctype_max_level - 1)) ? "disabled='true' style='background-color:#f5f5f5;' " : '';
                $select_html .= '>';
                if ($var['level'] > 0)
                {
                    $select_html .= str_repeat('&nbsp;', $var['level'] * 4);
                }
                $select_html .= htmlspecialchars(addslashes($var['typename'])) . '</option>';
            }
        }
        $this->assign('select_html',$select_html);
        $this->assign('is_edit_parent_id',$is_edit_parent_id);

        /* 模型 */
        $map = "status = 1 OR id = '".$info['current_channel']."'";
        $channeltype_list = model('Channeltype')->getAll('id,title,nid,ctl_name', $map, 'id');
        // 模型对应模板文件不存在报错
        if (!isset($channeltype_list[$info['current_channel']])) {
            $row = model('Channeltype')->getInfo($info['current_channel']);
            $file = 'lists_'.$row['nid'].'.htm';
            $this->error($row['title'].'缺少模板文件'.$file);
        }
        // 选项卡内容的链接
        $ctl_name = $channeltype_list[$info['current_channel']]['ctl_name'];
        $list_url = url("{$ctl_name}/index")."?typeid={$id}";
        $this->assign('list_url', $list_url);
        $this->assign('channeltype_list', $channeltype_list);
        
        /*发布文档的模型ID，用于是否显示文档模板列表*/
        $js_allow_channel_arr = '[';
        foreach ($this->allowReleaseChannel as $key => $val) {
            if (51 == $val) continue; // 问答模型
            if ($key > 0) {
                $js_allow_channel_arr .= ',';
            }
            $js_allow_channel_arr .= $val;
        }
        $js_allow_channel_arr = $js_allow_channel_arr.']';
        $this->assign('js_allow_channel_arr', $js_allow_channel_arr);
        /*--end*/

        /*选项卡*/
        $tab = input('param.tab/d', 1);
        $this->assign('tab', $tab);
        /*--end*/

        /*模板列表*/
        $templateList = $this->ajax_getTemplateList('edit', $info['templist'], $info['tempview']);
        $this->assign('templateList', $templateList);
        /*--end*/

        /*自定义字段*/
        $assign_data['addonFieldExtList'] = model('Field')->getTabelFieldList(config('global.arctype_channel_id'), $id);
        $assign_data['aid'] = $id;
        $assign_data['channeltype'] = 6;
        $assign_data['nid'] = 'arctype';
        /*--end*/

        /*之前标记旧参数可用，就开放入口*/
        $assign_data['is_old_product_attr'] = tpSetting('system.system_old_product_attr', [], 'cn');
        /*end*/

        $this->assign($assign_data);
        return $this->fetch();
    }
    
    /**
     * 内容管理
     */
    public function single_edit()
    {
        if (IS_POST) {
            $post = input('post.');
            $typeid = input('post.typeid/d', 0);
            if(!empty($typeid)){
                $info = Db::name('arctype')->field('id,typename,current_channel')
                    ->where([
                        'id'    => $typeid,
                        'lang'  => $this->admin_lang,
                    ])->find();
                $aid = Db::name('archives')->where([
                        'typeid'    => $typeid,
                        'channel'   => 6,
                        'lang'  => $this->admin_lang,
                    ])->getField('aid');
                
                /*修复新增单页栏目的关联数据不完善，进行修复*/
                if (empty($aid)) {
                    $archivesData = array(
                        'title' => $info['typename'],
                        'typeid'=> $info['id'],
                        'channel'   => $info['current_channel'],
                        'sort_order'    => 100,
                        'add_time'  => getTime(),
                        'update_time'     => getTime(),
                        'lang'  => $this->admin_lang,
                    );
                    $aid = Db::name('archives')->insertGetId($archivesData);
                }
                /*--end*/

                if (!isset($post['addonFieldExt'])) {
                    $post['addonFieldExt'] = array();
                }
                $updateData = array(
                    'aid'   => $aid,
                    'typename' => $info['typename'],
                    'addonFieldExt' => $post['addonFieldExt'],
                );
                model('Single')->afterSave($aid, $updateData, 'edit');

                \think\Cache::clear("arctype");
                adminLog('编辑栏目：'.$info['typename']);

                // 生成静态页面代码
                $this->success("操作成功!", $post['gourl'].'&typeid='.$typeid);
                exit;
            }
            $this->error("操作失败!");
            exit;
        }

        $assign_data = array();

        $typeid = input('typeid/d');
        $info = Db::name('arctype')->where([
                'id'    => $typeid,
                'lang'  => $this->admin_lang,
            ])->find();
        if (empty($info)) {
            $this->error('数据不存在，请联系管理员！');
            exit;
        }
        $assign_data['info'] = $info;

        /*自定义字段*/
        $addonFieldExtList = model('Field')->getChannelFieldList(6, 0, $typeid, $info);
        $channelfieldBindRow = Db::name('channelfield_bind')->where([
                'typeid'    => ['IN', [0,$typeid]],
            ])->column('field_id');
        if (!empty($channelfieldBindRow)) {
            foreach ($addonFieldExtList as $key => $val) {
                if (!in_array($val['id'], $channelfieldBindRow)) {
                    unset($addonFieldExtList[$key]);
                }
            }
        }
        $assign_data['addonFieldExtList'] = $addonFieldExtList;
        $assign_data['aid'] = $typeid;
        $assign_data['channeltype'] = 6;
        $assign_data['nid'] = 'single';
        /*--end*/

        /*返回上一层*/
        $gourl = input('param.gourl/s', '');
        if (empty($gourl)) {
            $gourl = url('Arctype/index');
        }
        $assign_data['gourl'] = $gourl;
        /*--end*/

        $this->assign($assign_data);
        
        /* 生成静态页面代码 */
        $this->assign('typeid',$typeid);
        /* end */
        
        return $this->fetch();
    }
    
    //伪删除 del->彻底删除 pseudo->伪删除
    public function pseudo_del()
    {
        if (IS_POST) {
            $this->language_access();
            $post = input('post.');
            $del_id = eyIntval($post['del_id']);
            $row = Db::name('arctype')->field('id, current_channel, typename')
                ->where([
                    'id'    => $del_id,
                    'lang'  => $this->admin_lang,
                ])
                ->find();
            if ('del' == $post['deltype']){
                $id_arr = $row['id'];
                // 多语言处理逻辑
                if (is_language()) {
                    $attr_name_arr = 'tid'.$row['id'];
                    $id_arr_tmp = Db::name('language_attr')->where([
                        'attr_name'  => $attr_name_arr,
                        'attr_group' => 'arctype',
                    ])->column('attr_value');
                    if (!empty($id_arr_tmp)) {
                        $id_arr = $id_arr_tmp;
                    }

                    $list = $this->arctype_db->field('id,del_method')
                        ->where([
                            'id' => ['IN', $id_arr],
                        ])
                        ->whereOr([
                            'parent_id' => ['IN', $id_arr],
                        ])->select();
                }else{
                    $list = $this->arctype_db->field('id,del_method')
                        ->where([
                            'parent_id' => ['IN', $id_arr],
                        ])
                        ->fetchsql(true)
                        ->select();
                }
                // 删除条件逻辑
                // 栏目逻辑
                $map = 'is_del = 0 ';
                // 多语言处理逻辑
                if (is_language()) {
                    $where = $map.' and (';
                    $ids = get_arr_column($list, 'id');
                    !empty($ids) && $where .= 'id IN ('.implode(',', $ids).') OR parent_id IN ('.implode(',', $ids).')';
                }else{
                    $ids = [$del_id];
                    $where  = $map.' and (id='.$del_id.' or parent_id='.$del_id;
                    if (0 == intval($row['parent_id'])) {
                        foreach ($list as $value) {
                            if (2 == intval($value['del_method'])) {
                                $where .= ' or parent_id='.$value['id'];
                            }
                        }
                    }
                }
                $where .= ')';

                // 文章逻辑
                $where1 = $map.' and typeid in (';

                // 查询栏目回收站数据并拼装删除文章逻辑
                $arctype  = $this->arctype_db->field('id')->where($where)->select();
                foreach ($arctype as $key => $value) {
                    $where1 .= $value['id'].',';
                }
                $where1 = rtrim($where1,',');
                $where1 .= ')';

                // 栏目数据删除
                $r = $this->arctype_db->where($where)->delete();
                if($r){
                    // Tag标签删除
                    Db::name('taglist')->where([
                        'typeid'    => ['IN', $ids],
                    ])->delete();
                    // 内容数据删除
                    $r = $this->archives->where($where1)->delete();
                    $msg = '';
                    if (!$r) {
                        $msg = '，文档清空失败！';
                    }
                    adminLog('删除栏目：'.$row['typename']);
                    /*清空sql_cache_table数据缓存表 并 添加查询执行语句到mysql缓存表*/
                    Db::name('sql_cache_table')->query('TRUNCATE TABLE '.config('database.prefix').'sql_cache_table');
                    model('SqlCacheTable')->InsertSqlCacheTable(true);
                    /* END */
                    $this->success("操作成功".$msg);
                }
            }elseif ('pseudo' == $post['deltype']){
                $r = model('arctype')->pseudo_del($del_id);
                if (false !== $r) {
                    adminLog('伪删除栏目：'.$row['typename']);
                    /*清空sql_cache_table数据缓存表 并 添加查询执行语句到mysql缓存表*/
                    Db::name('sql_cache_table')->query('TRUNCATE TABLE '.config('database.prefix').'sql_cache_table');
                    model('SqlCacheTable')->InsertSqlCacheTable(true);
                    /* END */
                    $this->success('删除成功');
                } else {
                    $this->error('删除失败');
                }
            }
        }
        $this->error('非法访问');
    }

    /**
     * 批量伪删除
     */
    public function batch_pseudo_del()
    {
        if (IS_POST) {
            $this->language_access(); // 多语言功能操作权限

            $post = input('post.');
            $post['del_id'] = eyIntval($post['del_id']);
            if (!$post['del_id']) $this->error('未选择栏目');
            $typename = '';
            foreach ($post['del_id'] as $item) {
                /*当前栏目信息*/
                $row = Db::name('arctype')->field('id, current_channel, typename')
                    ->where([
                        'id'    => $item,
                        'lang'  => $this->admin_lang,
                    ])
                    ->find();

                $typename .= $row['typename'].',';
                if ('del' == $post['deltype']){
                    $id_arr = $row['id'];
                    // 多语言处理逻辑
                    if (is_language()) {
                        $attr_name_arr = 'tid'.$row['id'];
                        $id_arr_tmp = Db::name('language_attr')->where([
                            'attr_name'  => $attr_name_arr,
                            'attr_group' => 'arctype',
                        ])->column('attr_value');
                        if (!empty($id_arr_tmp)) {
                            $id_arr = $id_arr_tmp;
                        }
                        $list = $this->arctype_db->field('id,del_method')
                            ->where([
                                'id' => ['IN', $id_arr],
                            ])
                            ->whereOr([
                                'parent_id' => ['IN', $id_arr],
                            ])->select();
                    }else{
                        $list = $this->arctype_db->field('id,del_method')
                            ->where([
                                'parent_id' => ['IN', $id_arr],
                            ])
                            ->select();
                    }

                    // 删除条件逻辑
                    // 栏目逻辑
//                    $map = 'is_del=1';
                    $map = '1=1';
                    // 多语言处理逻辑
                    if (is_language()) {
                        $where = $map.' and (';
                        $ids = get_arr_column($list, 'id');
                        !empty($ids) && $where .= 'id IN ('.implode(',', $ids).') OR parent_id IN ('.implode(',', $ids).')';
                    }else{
                        $ids = [$item];
                        $where  = $map.' and (id='.$item.' or parent_id='.$item;
                        if (0 == intval($row['parent_id'])) {
                            foreach ($list as $value) {
                                if (2 == intval($value['del_method'])) {
                                    $where .= ' or parent_id='.$value['id'];
                                }
                            }
                        }
                    }
                    $where .= ')';

                    // 文章逻辑
                    $where1 = $map.' and typeid in (';

                    // 查询栏目回收站数据并拼装删除文章逻辑
                    $arctype  = $this->arctype_db->field('id')->where($where)->select();
                    foreach ($arctype as $key => $value) {
                        $where1 .= $value['id'].',';
                    }
                    $where1 = rtrim($where1,',');
                    $where1 .= ')';

                    // 栏目数据删除
                    $r = $this->arctype_db->where($where)->delete();
                    if($r){
                        // Tag标签删除
                        Db::name('taglist')->where([
                            'typeid'    => ['IN', $ids],
                        ])->delete();
                        // 内容数据删除
                        $this->archives->where($where1)->delete();
                    }
                    $typename .= $row['typename'].',';
                }elseif ('pseudo' == $post['deltype']) {
                    model('arctype')->pseudo_del($item);
                }
            }
            if ('del' == $post['deltype']){
                adminLog('删除栏目：'.trim($typename,','));
            }elseif ('pseudo' == $post['deltype']) {
                adminLog('伪删除栏目：' . trim($typename, ','));
            }
            /*清空sql_cache_table数据缓存表 并 添加查询执行语句到mysql缓存表*/
            Db::name('sql_cache_table')->query('TRUNCATE TABLE '.config('database.prefix').'sql_cache_table');
            model('SqlCacheTable')->InsertSqlCacheTable(true);
            /* END */
            $this->success('删除成功');

        }

        $this->error('非法访问');
    }

    /**
     * 删除[1.2.7之后废弃]
     */
    public function del()
    {
        $this->language_access(); // 多语言功能操作权限
        
        $post = input('post.');
        $post['del_id'] = eyIntval($post['del_id']);

        /*当前栏目信息*/
        $row = Db::name('arctype')->field('id, current_channel, typename')
            ->where([
                'id'    => $post['del_id'],
                'lang'  => $this->admin_lang,
            ])
            ->find();
        
        $r = model('arctype')->del($post['del_id']);
        if (false !== $r) {
            adminLog('删除栏目：'.$row['typename']);
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 通过模型获取栏目
     */
    public function ajax_get_arctype($channeltype = 0)
    {
        $arctype_max_level = intval(config('global.arctype_max_level'));
        $options           = $this->arctypeLogic->arctype_list(0, 0, false, $arctype_max_level, array('channeltype' => $channeltype));
        $select_html       = '<option value="0" data-grade="-1">顶级栏目</option>';
        foreach ($options AS $var)
        {
            $select_html .= '<option value="' . $var['id'] . '" data-grade="' . $var['grade'] . '" data-dirpath="'.$var['dirpath'].'"';
            $select_html .= '>';
            if ($var['level'] > 0)
            {
                $select_html .= str_repeat('&nbsp;', $var['level'] * 4);
            }
            $select_html .= htmlspecialchars(addslashes($var['typename'])) . '</option>';
        }

        $returndata = array(
            'status' => 1,
            'select_html' => $select_html,
        );
        
        respose($returndata);
    }

    /**
     * 获取栏目的拼音
     */
    public function ajax_get_dirpinyin($typename = '')
    {
        $typename = input('post.typename/s');
        $pinyin = get_pinyin($typename);

        respose(array(
            'status'    => 1,
            'msg'   => $pinyin
        ));
    }

    /**
     * 检测文件保存目录是否存在
     */
    public function ajax_check_dirpath()
    {
        $dirpath = input('post.dirpath/s');
        $id = input('post.id/d');
        $map = array(
            'dirpath' => $dirpath,
            'lang'  => $this->admin_lang,
        );
        if (intval($id) > 0) {
            $map['id'] = array('neq', $id);
        }
        $result = Db::name('arctype')->where($map)->find();
        if (!empty($result)) {
            respose(array(
                'status'    => 0,
                'msg'   => '文件保存目录已存在，请更改',
            ));
        } else {
            respose(array(
                'status'    => 1,
                'msg'   => '文件保存目录可用',
            ));
        }
    }

    public function ajax_getTemplateList($opt = 'add', $templist = '', $tempview = '')
    {   
        $planPath = 'template/'.TPL_THEME.'pc';
        $dirRes   = opendir($planPath);
        $view_suffix = config('template.view_suffix');

        /*模板PC目录文件列表*/
        $templateArr = array();
        while($filename = readdir($dirRes))
        {
            if (in_array($filename, array('.','..'))) {
                continue;
            }
            array_push($templateArr, $filename);
        }
        !empty($templateArr) && asort($templateArr);
        /*--end*/

        /*多语言全部标识*/
        $markArr = Db::name('language_mark')->column('mark');
        /*--end*/
        
        $is_language = false;
        $web_language_switch = tpCache('web.web_language_switch');
        if (!empty($web_language_switch)) {
            $languageCount = Db::name('language')->where(['status'=>1])->count('id');
            if (1 < $languageCount) {
                $is_language = true;
            }
        }

        $templateList = array();
        $channelList = model('Channeltype')->getAll();
        foreach ($channelList as $k1 => $v1) {
            $l = 1;
            $v = 1;
            $lists = ''; // 销毁列表模板
            $view = ''; // 销毁文档模板
            $templateList[$v1['id']] = array();
            foreach ($templateArr as $k2 => $v2) {
                $v2 = iconv('GB2312', 'UTF-8', $v2);

                if ('add' == $opt) {
                    $selected = 0; // 默认选中状态
                } else {
                    $selected = 1; // 默认选中状态
                }

                preg_match('/^(lists|view)_'.$v1['nid'].'(_(.*))?(_'.$this->admin_lang.')?\.'.$view_suffix.'/i', $v2, $matches1);
                $langtpl = preg_replace('/\.'.$view_suffix.'$/i', "_{$this->admin_lang}.{$view_suffix}", $v2);
                if (file_exists(realpath($planPath.DS.$langtpl))) {
                    continue;
                } else if (true == $is_language && preg_match('/^(.*)_([a-zA-z]{2,2})\.'.$view_suffix.'$/i',$v2,$matches2)) {
                    if (in_array($matches2[2], $markArr) && $matches2[2] != $this->admin_lang) {
                        continue;
                    }
                }

                if (!empty($matches1)) {
                    $selectefile = '';
                    if ('lists' == $matches1[1]) {
                        $lists .= '<option value="'.$v2.'" ';
                        $lists .= ($templist == $v2 || $selected == $l) ? " selected='true' " : '';
                        $lists .= '>'.$v2.'</option>';
                        $l++;
                    } else if ('view' == $matches1[1]) {
                        $view .= '<option value="'.$v2.'" ';
                        $view .= ($tempview == $v2 || $selected == $v) ? " selected='true' " : '';
                        $view .= '>'.$v2.'</option>';
                        $v++;
                    }
                }
            }
            $nofileArr = [];
            if (in_array($v1['nid'], ['ask'])) { // 问答模型

            } else { // 其他模型
                if ('add' == $opt) {
                    if (empty($lists)) {
                        $lists = '<option value="">无</option>';
                        $nofileArr[] = "lists_{$v1['nid']}.{$view_suffix}";
                    }
                    
                    if (empty($view)) {
                        $view = '<option value="">无</option>';
                        if (!in_array($v1['nid'], ['single','guestbook'])) {
                            $nofileArr[] = "view_{$v1['nid']}.{$view_suffix}";
                        }
                    }
                } else {
                    if (empty($lists)) {
                        $nofileArr[] = "lists_{$v1['nid']}.{$view_suffix}";
                    }
                    $lists = '<option value="">请选择模板…</option>'.$lists;

                    if (empty($view)) {
                        if (!in_array($v1['nid'], ['single','guestbook'])) {
                            $nofileArr[] = "view_{$v1['nid']}.{$view_suffix}";
                        }
                    }
                    $view = '<option value="">请选择模板…</option>'.$view;
                }
            }

            $msg = '';
            if (!empty($nofileArr)) {
                $msg = '<font color="red">该模型缺少模板文件：'.implode(' 和 ', $nofileArr).'</font>';
            }

            $templateList[$v1['id']] = array(
                'lists' => $lists,
                'view' => $view,
                'msg'    => $msg,
                'nid'    => $v1['nid'],
            );
        }

        if (IS_AJAX) {
            $this->success('请求成功', null, ['templateList'=>$templateList]);
        } else {
            return $templateList;
        }
    }

    /**
     * 新建模板文件
     */
    public function ajax_newtpl()
    {
        if (IS_POST) {
            $post = input('post.', '', null);
            $content = input('post.content', '', null);
            $view_suffix = config('template.view_suffix');
            if (!empty($post['filename'])) {
                if (!preg_match("/^[\w\-\_]{1,}$/u", $post['filename'])) {
                    $this->error('文件名称只允许字母、数字、下划线、连接符的任意组合！');
                }
                $filename = "{$post['type']}_{$post['nid']}_{$post['filename']}.{$view_suffix}";
            } else {
                $filename = "{$post['type']}_{$post['nid']}.{$view_suffix}";
            }

            $content = !empty($content) ? $content : '';
            $tpldirpath = !empty($post['tpldir']) ? '/template/'.TPL_THEME.trim($post['tpldir']) : '/template/'.TPL_THEME.'pc';
            if (file_exists(ROOT_PATH.ltrim($tpldirpath, '/').'/'.$filename)) {
                $this->error('文件名称已经存在，请重新命名！', null, ['focus'=>'filename']);
            }

            $nosubmit = input('param.nosubmit/d');
            if (1 == $nosubmit) {
                $this->success('检测通过');
            }

            $filemanagerLogic = new \app\admin\logic\FilemanagerLogic;
            $r = $filemanagerLogic->editFile($filename, $tpldirpath, $content);
            if ($r === true) {
                $this->success('操作成功', null, ['filename'=>$filename,'type'=>$post['type']]);
            } else {
                $this->error($r);
            }
        }
        $type = input('param.type/s');
        $nid = input('param.nid/s');
        $tpldirList = glob('template/'.TPL_THEME.'*');
        $tpl_theme = str_replace('/', '\\/', TPL_THEME);
        foreach ($tpldirList as $key => $val) {
            if (!preg_match('/template\/'.$tpl_theme.'(pc|mobile)$/i', $val)) {
                unset($tpldirList[$key]);
            } else {
                $tpldirList[$key] = preg_replace('/^(.*)template\/'.$tpl_theme.'(pc|mobile)$/i', '$2', $val);
            }
        }
        !empty($tpldirList) && arsort($tpldirList);
        $this->assign('tpldirList', $tpldirList);
        $this->assign('type', $type);
        $this->assign('nid', $nid);
        $this->assign('tpl_theme', TPL_THEME);
        return $this->fetch();
    }

    /**
     * 批量增加栏目
     */
    public function batch_add()
    {
        //防止php超时
        function_exists('set_time_limit') && set_time_limit(0);
        
        $this->language_access(); // 多语言功能操作权限

        if (IS_POST) {
            $post = input('post.');
            if ($post) {

                if (empty($post['parent_id'])) { // 增加顶级栏目
                    foreach ($post['toptype'] as $key => $val) {
                        $val = trim($val);
                        if (empty($val)) {
                            unset($post['toptype'][$key]);
                        }
                    }
                    if (empty($post['toptype'])) {
                        $this->error('顶级栏目名称不能为空！');
                    }
                    $this->batch_add_toptype($post);
                } 
                else { // 增加下级栏目
                    foreach ($post['reltype'] as $key => $val) {
                        $val = trim($val);
                        if (empty($val)) {
                            unset($post['reltype'][$key]);
                        }
                    }
                    if (empty($post['reltype'])) {
                        $this->error('栏目名称不能为空！');
                    }
                    $this->batch_add_subtype($post);
                }
            }
            $this->error("操作失败！");
            exit;
        }

        /* 模型 */
        $map = array(
            'status'    => 1,
            'id'        => ['neq', 51], // 排除问答模型
        );
        $channeltype_list = model('Channeltype')->getAll('id,title,nid', $map, 'id');
        $this->assign('channeltype_list', $channeltype_list);
        
        /*发布文档的模型ID，用于是否显示文档模板列表*/
        $js_allow_channel_arr = '[';
        foreach ($this->allowReleaseChannel as $key => $val) {
            if ($key > 0) {
                $js_allow_channel_arr .= ',';
            }
            $js_allow_channel_arr .= $val;
        }
        $js_allow_channel_arr = $js_allow_channel_arr.']';
        $this->assign('js_allow_channel_arr', $js_allow_channel_arr);
        /*--end*/

        // 所属栏目
        $select_html = '<option value="0" data-grade="-1" data-dirpath="">顶级栏目</option>';
        $selected = 0;
        $arctype_max_level = intval(config('global.arctype_max_level'));
        $arctypeWhere = ['is_del'=>0];
        $options = $this->arctypeLogic->arctype_list(0, $selected, false, $arctype_max_level - 1, $arctypeWhere);
        foreach ($options AS $var)
        {
            $select_html .= '<option value="' . $var['id'] . '" data-grade="' . $var['grade'] . '" data-dirpath="'.$var['dirpath'].'">';
            if ($var['level'] > 0)
            {
                $select_html .= str_repeat('&nbsp;', $var['level'] * 4);
            }
            $select_html .= htmlspecialchars(addslashes($var['typename'])) . '</option>';
        }
        $this->assign('select_html',$select_html);

        /*模板列表*/
        $templateList = $this->ajax_getTemplateList('add');
        $this->assign('templateList', $templateList);
        /*--end*/

        return $this->fetch();
    }

    /**
     * 批量增加顶级栏目
     */
    private function batch_add_toptype($post = [])
    {
        $saveData = [];
        $dirnameArr = [];
        foreach ($post['toptype'] as $key => $val) {
            $typename = func_preg_replace([',','，'], '', trim($val));
            if (empty($typename)) continue;

            // 子栏目
            if (!empty($post['sontype'][$key])) {
                $sontype = str_replace('，', ',', $post['sontype'][$key]);
                $post['sontype'][$key] = explode(',', $sontype);
            }

            // 目录名称
            $dirname = $this->arctypeLogic->get_dirname($typename, '', 0, $dirnameArr);
            array_push($dirnameArr, $dirname);

            $dirpath = '/'.$dirname;

            $data = [
                'typename'  => $typename,
                'channeltype'   => $post['current_channel'],
                'current_channel'   => $post['current_channel'],
                'parent_id' => 0,
                'dirname'   => $dirname,
                'dirpath'   => $dirpath,
                'grade' => 0,
                'templist'  => !empty($post['templist']) ? $post['templist'] : '',
                'tempview'  => !empty($post['tempview']) ? $post['tempview'] : '',
                'is_hidden'  => $post['is_hidden'],
                'seo_description'   => '',
                'admin_id'  => session('admin_info.admin_id'),
                'lang'  => $this->admin_lang,
                'sort_order'    => !empty($post['sort_order'][$key]) ? intval($post['sort_order'][$key]) : 100,
                'add_time'  => getTime(),
                'update_time'  => getTime(),
            ];

            $saveData[] = $data;
        }

        if (!empty($saveData)) {
            $result = model('Arctype')->batchAddTopData($saveData, $post);
            if (!empty($result)) {
                $typenameArr = get_arr_column($result, 'typename');
                $typenameStr = implode(',', $typenameArr);
                adminLog('批量增加栏目：'.$typenameStr);

                // 生成静态页面代码
                $msg = '操作成功！';
                $seo_pseudo = config('tpcache.seo_pseudo');
                if (2 == $seo_pseudo) {
                    $msg = '操作成功，请手工生成静态页面！';
                }
                $this->success($msg, url('Arctype/index'));
                exit;
            }
        }
        $this->error("操作失败！");
    }

    /**
     * 批量增加下级栏目
     */
    private function batch_add_subtype($post = [])
    {
        // 获取所属栏目信息
        $arctypeInfo = Db::name('arctype')->field('id,channeltype,topid,parent_id,grade')->where('id', $post['parent_id'])->find();

        $topid = 0;
        if (!empty($arctypeInfo['topid'])) {
            $topid = $arctypeInfo['topid'];
        } else {
            if ($arctypeInfo['grade'] == 0) {
                $topid = $arctypeInfo['id'];
            } else if ($arctypeInfo['grade'] == 1) {
                $topid = $arctypeInfo['parent_id'];
            }
        }

        $saveData = [];
        $dirnameArr = [];
        foreach ($post['reltype'] as $key => $val) {
            $typename = func_preg_replace([',','，'], '', trim($val));
            if (empty($typename)) continue;

            // 目录名称
            $dirname = $this->arctypeLogic->get_dirname($typename, '', 0, $dirnameArr);
            array_push($dirnameArr, $dirname);

            $dirpath = $post['dirpath'].'/'.$dirname;

            $data = [
                'typename'  => $typename,
                'channeltype'   => $arctypeInfo['channeltype'],
                'current_channel'   => $post['current_channel'],
                'parent_id' => intval($post['parent_id']),
                'topid'     => $topid,
                'dirname'   => $dirname,
                'dirpath'   => $dirpath,
                'grade' => intval($post['grade']),
                'templist'  => !empty($post['templist']) ? $post['templist'] : '',
                'tempview'  => !empty($post['tempview']) ? $post['tempview'] : '',
                'is_hidden'  => $post['is_hidden'],
                'seo_description'   => '',
                'admin_id'  => session('admin_info.admin_id'),
                'lang'  => $this->admin_lang,
                'sort_order'    => !empty($post['sort_order_1'][$key]) ? intval($post['sort_order_1'][$key]) : 100,
                'add_time'  => getTime(),
                'update_time'  => getTime(),
            ];

            $saveData[] = $data;
        }

        if (!empty($saveData)) {
            $result = model('Arctype')->batchAddSubData($saveData);
            if (!empty($result)) {
                $typenameArr = get_arr_column($result, 'typename');
                $typenameStr = implode(',', $typenameArr);
                adminLog('批量增加栏目：'.$typenameStr);

                // 生成静态页面代码
                $msg = '操作成功！';
                $seo_pseudo = config('tpcache.seo_pseudo');
                if (2 == $seo_pseudo) {
                    $msg = '操作成功，请手工生成静态页面！';
                }
                $this->success($msg, url('Arctype/index'));
                exit;
            }
        }
        $this->error("操作失败！");
    }

    /**
     * 单页可视化入口
     */
    public function single_uiset()
    {
        $tid = input('param.tid/d');
        $v = input('param.v/s', 'pc');
        if (!empty($tid)) {
            if ('mobile' == $v) {
                $gourl = ROOT_DIR."/index.php?m=home&c=Lists&a=index&tid={$tid}&uiset=on&v={$v}&lang=".$this->admin_lang;
                $url = ROOT_DIR."/index.php?m=api&c=Uiset&a=mobileTpl&tid={$tid}&gourl=".base64_encode($gourl);
            } else {
                $url = ROOT_DIR."/index.php?m=home&c=Lists&a=index&tid={$tid}&uiset=on&v={$v}&lang=".$this->admin_lang;
            }
            $this->redirect($url);
        }
    }
}