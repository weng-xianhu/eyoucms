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
    // 栏目对应模型ID
    public $arctype_channel_id = '';
    // 允许发布文档的模型ID
    public $allowReleaseChannel = array();
    // 禁用的目录名称
    public $disableDirname = [];
    
    public function _initialize() {
        parent::_initialize();
        $this->fieldLogic = new FieldLogic();
        $this->allowReleaseChannel = config('global.allow_release_channel');
        $this->arctype_channel_id = config('global.arctype_channel_id');
        $this->disableDirname = config('global.disable_dirname');

        /*兼容每个用户的自定义字段，重新生成数据表字段缓存文件*/
        $arctypeFieldInfo = include DATA_PATH.'schema/'.PREFIX.'arctype.php';
        foreach (['del_method'] as $key => $val) {
            if (!isset($arctypeFieldInfo[$val])) {
                try {
                    schemaTable('arctype');
                } catch (\Exception $e) {}
                break;
            }
        }
        /*--end*/
    }

    public function index()
    {
        $arctype_list = array();
        // 目录列表
        $arctypeLogic = new ArctypeLogic(); 
        $where['is_del'] = '0'; // 回收站功能
        $arctype_list = $arctypeLogic->arctype_list(0, 0, false, 0, $where, false);
        $this->assign('arctype_list', $arctype_list);

        // 模型列表
        $channeltype_list = getChanneltypeList();
        $this->assign('channeltype_list', $channeltype_list);

        // 栏目最多级别
        $arctype_max_level = intval(config('global.arctype_max_level'));
        $this->assign('arctype_max_level', $arctype_max_level);

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
                /*目录名称*/
                $dirname = $this->get_dirpinyin($post['typename'], $post['dirname']);
                $dirname = preg_replace('/(\s)+/', '_', $dirname);
                if (2 >= strlen($dirname)) { // 避免与多语言标识冲突
                    $this->error('目录名称不能少于2个字符');
                }
                // 多语言
                $langMark = Db::name('language')->column('mark');
                $this->disableDirname = array_merge($this->disableDirname, $langMark);
                // 检测
                if (in_array(strtolower($dirname), $this->disableDirname)) {
                    $this->error('目录名称与系统内置重名，请更改！');
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
                } else {
                    $channeltype = M('arctype')->where('id', $post['parent_id'])->getField('channeltype');
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
                if($insertId){
                    $_POST['id'] = $insertId;

                    /*同步栏目ID到多语言的模板栏目变量里*/
                    $this->syn_add_language_attribute($insertId);
                    /*--end*/

                    adminLog('新增栏目：'.$data['typename']);
                    $this->success("操作成功!", url('Arctype/index'));
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
        $predirpath = tpCache('seo.seo_arcdir');
        $ptypename = '';
        if (0 < $parent_id) {
            $info = M('arctype')->where(array('id'=>$parent_id))->find();
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
        $assign_data['addonFieldList'] = model('Field')->getTabelFieldList(config('global.arctype_channel_id'));
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
            if(!empty($post['id'])){

                /*自己的上级不能是自己*/
                if (intval($post['id']) == intval($post['parent_id'])) {
                    $this->error('自己不能成为自己的上级栏目');
                }
                /*--end*/

                /*目录名称*/
                $dirname = $this->get_dirpinyin($post['typename'], $post['dirname'], $post['id']);
                $dirname = preg_replace('/(\s)+/', '_', $dirname);
                if (2 >= strlen($dirname)) { // 避免与多语言标识冲突
                    $this->error('目录名称不能少于2个字符');
                }
                // 多语言
                $langMark = Db::name('language')->column('mark');
                $this->disableDirname = array_merge($this->disableDirname, $langMark);
                // 检测
                if (in_array(strtolower($dirname), $this->disableDirname)) {
                    $this->error('目录名称与系统内置重名，请更改！');
                }
                /*--end*/
                $dirpath = rtrim($post['dirpath'], '/');
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
                // 最顶级模型ID
                $channeltype = $post['channeltype'];
                // 当前更改的等级
                $grade = $post['grade']; 
                // 根据栏目ID获取最新的最顶级模型ID
                if (intval($post['parent_id']) > 0) {
                    $arctype_row = M('arctype')->field('grade,channeltype')->where('id', $post['parent_id'])->find();
                    $channeltype = $arctype_row['channeltype'];
                    $grade = $arctype_row['grade'] + 1;
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
                if($r){
                    adminLog('编辑栏目：'.$data['typename']);
                    $this->success("操作成功!", url('Arctype/index'));
                    exit;
                }
            }
            $this->error("操作失败!");
            exit;
        }

        $assign_data = array();

        $id = input('id/d');
        $info = M('arctype')->where([
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
        if (!empty($info['dirpath'])) {
            $predirpath = preg_replace('/\/([^\/]*)$/i', '', $info['dirpath']);
        } else {
            $predirpath = tpCache('seo.seo_arcdir');
        }
        $this->assign('predirpath',$predirpath);

        // 是否有子栏目
        $hasChildren = model('Arctype')->hasChildren($id);
        if ($hasChildren > 0) {
            $select_html = M('arctype')->where('id', $info['parent_id'])->getField('typename');
            $select_html = !empty($select_html) ? $select_html : '顶级栏目';
        } else {
            // 所属栏目
            // $channeltype = $info['channeltype'];
            $select_html = '<option value="0" data-grade="-1" data-dirpath="'.tpCache('seo.seo_arcdir').'">顶级栏目</option>';
            $selected = $info['parent_id'];
            $arctype_max_level = intval(config('global.arctype_max_level'));
            $arctypeLogic = new ArctypeLogic();
            $options = $arctypeLogic->arctype_list(0, $selected, false, $arctype_max_level - 1);
            foreach ($options AS $var)
            {
                $select_html .= '<option value="' . $var['id'] . '" data-grade="' . $var['grade'] . '" data-dirpath="'.$var['dirpath'].'"';
                $select_html .= ($selected == $var['id']) ? "selected='ture'" : '';
                $select_html .= ($id == $var['id']) ? "disabled='ture' style='background-color:#f5f5f5;' " : '';
                $select_html .= '>';
                if ($var['level'] > 0)
                {
                    $select_html .= str_repeat('&nbsp;', $var['level'] * 4);
                }
                $select_html .= htmlspecialchars(addslashes($var['typename'])) . '</option>';
            }
        }
        $this->assign('select_html',$select_html);
        $this->assign('hasChildren',$hasChildren);

        /* 模型 */
        $map = array(
            'status'    => 1,
        );
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
        $assign_data['addonFieldList'] = model('Field')->getTabelFieldList(config('global.arctype_channel_id'), $id);
        $assign_data['aid'] = $id;
        $assign_data['channeltype'] = 6;
        $assign_data['nid'] = 'arctype';
        /*--end*/

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
                $info = M('arctype')->field('id,typename,current_channel')
                    ->where([
                        'id'    => $typeid,
                        'lang'  => $this->admin_lang,
                    ])->find();
                $aid = M('archives')->where([
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
                    $aid = M('archives')->insertGetId($archivesData);
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
                $this->success("操作成功!", $post['gourl']);
                exit;
            }
            $this->error("操作失败!");
            exit;
        }

        $assign_data = array();

        $typeid = input('typeid/d');
        $info = M('arctype')->where([
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
        return $this->fetch();
    }
    
    /**
     * 伪删除
     */
    public function pseudo_del()
    {
        if (IS_POST) {
            $this->language_access(); // 多语言功能操作权限
            
            $post = input('post.');
            $post['del_id'] = eyIntval($post['del_id']);

            /*当前栏目信息*/
            $row = M('arctype')->field('id, current_channel, typename')
                ->where([
                    'id'    => $post['del_id'],
                    'lang'  => $this->admin_lang,
                ])
                ->find();
            
            $r = model('arctype')->pseudo_del($post['del_id']);
            if (false !== $r) {
                adminLog('伪删除栏目：'.$row['typename']);
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
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
        $row = M('arctype')->field('id, current_channel, typename')
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
     * 获取栏目的拼音，确保唯一性
     */
    private function get_dirpinyin($typename = '', $dirname = '', $id = 0)
    {
        if (empty($dirname)) {
            $dirname = get_pinyin($typename);
        }
        if (strval(intval($dirname)) == strval($dirname)) {
            $dirname .= get_rand_str(3,0,0);
        }
        $map = array(
            'dirname'   => $dirname,
            'lang'  => $this->admin_lang,
        );
        if (intval($id) > 0) {
            $map['id']  = array('neq', $id);
        }
        $result = M('arctype')->field('id')->where($map)->find();
        if (!empty($result)) {
            $nowDirname = $dirname.get_rand_str(3,0,0);
            return $this->get_dirpinyin($typename, $nowDirname, $id);
        }

        return $dirname;
    }

    /**
     * 通过模型获取栏目
     */
    public function ajax_get_arctype($channeltype = 0)
    {
        $arctypeLogic = new ArctypeLogic();
        $arctype_max_level = intval(config('global.arctype_max_level'));
        $options = $arctypeLogic->arctype_list(0, 0, false, $arctype_max_level, array('channeltype'=>$channeltype));
        $select_html = '<option value="0" data-grade="-1">顶级栏目</option>';
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
        $result = M('arctype')->where($map)->find();
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
        $planPath = 'template/pc';
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
        /*--end*/

        /*多语言全部标识*/
        $markArr = Db::name('language_mark')->column('mark');
        /*--end*/

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
                } else if (preg_match('/^(.*)_([a-zA-z]{2,2})\.'.$view_suffix.'$/i',$v2,$matches2)) {
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
     * 同步新增栏目ID到多语言的模板栏目变量里
     */
    private function syn_add_language_attribute($typeid)
    {
        /*单语言情况下不执行多语言代码*/
        if (!is_language()) {
            return true;
        }
        /*--end*/

        $attr_group = 'arctype';
        $admin_lang = $this->admin_lang;
        $main_lang = get_main_lang();
        $languageRow = Db::name('language')->field('mark')->order('id asc')->select();
        if (!empty($languageRow) && $admin_lang == $main_lang) { // 当前语言是主体语言，即语言列表最早新增的语言
            $arctypeRow = Db::name('arctype')->find($typeid);
            $attr_name = 'tid'.$typeid;
            $r = Db::name('language_attribute')->save([
                'attr_title'    => $arctypeRow['typename'],
                'attr_name'     => $attr_name,
                'attr_group'    => $attr_group,
                'add_time'      => getTime(),
                'update_time'   => getTime(),
            ]);
            if (false !== $r) {
                $data = [];
                foreach ($languageRow as $key => $val) {
                    /*同步新栏目到其他语言栏目列表*/
                    if ($val['mark'] != $admin_lang) {
                        $addsaveData = $arctypeRow;
                        $addsaveData['lang'] = $val['mark'];
                        $addsaveData['typename'] = $val['mark'].$addsaveData['typename']; // 临时测试
                        $parent_id = Db::name('language_attr')->where([
                                'attr_name' => 'tid'.$arctypeRow['parent_id'],
                                'lang'  => $val['mark'],
                            ])->getField('attr_value');
                        $addsaveData['parent_id'] = intval($parent_id);
                        unset($addsaveData['id']);
                        $typeid = model('Arctype')->addData($addsaveData);
                    }
                    /*--end*/
                    
                    /*所有语言绑定在主语言的ID容器里*/
                    $data[] = [
                        'attr_name' => $attr_name,
                        'attr_value'    => $typeid,
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
            $tpldirpath = !empty($post['tpldir']) ? '/template/'.trim($post['tpldir']) : '/template/pc';
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
        $tpldirList = glob('template/*');
        foreach ($tpldirList as $key => $val) {
            if (!preg_match('/template\/(pc|mobile)$/i', $val)) {
                unset($tpldirList[$key]);
            } else {
                $tpldirList[$key] = preg_replace('/^(.*)template\/(pc|mobile)$/i', '$2', $val);
            }
        }
        $this->assign('tpldirList', $tpldirList);
        $this->assign('type', $type);
        $this->assign('nid', $nid);
        return $this->fetch();
    }
}