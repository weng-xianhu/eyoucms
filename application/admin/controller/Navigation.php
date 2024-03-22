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
use think\Cache;
use app\admin\model\NavPosition;
use app\common\logic\NavigationLogic;


/**
 * 导航管理
 */
class Navigation extends Base
{

    private $position_model;
    private $list_db;
    private $position_db;
    private $navigationlogic;

    /**
     * 构造方法
     */
    public function __construct(){
        parent::__construct();
        $this->position_model = new NavPosition;
        $this->list_db = Db::name('nav_list');
        $this->position_db = Db::name('nav_position');
        $this->navigationlogic = new NavigationLogic;

    }
    //菜单管理页面
    public function index()
    {
        $list = array();
        $position_id = input('position_id/s');
        $map = array();
        if (empty($position_id)) {
            $position_id = $this->position_db->order('position_id asc')->limit(1)->value('position_id');
        }
        $this->assign('position_id', $position_id);

        $map['c.position_id'] = $position_id;
        $nav_list = $this->navigationlogic->nav_list(0, 0, false, 0, $map, false);
        $this->assign('list', $nav_list);

        $position_list = $this->position_db->where('is_del',0)->order('sort_order asc,position_id asc')->getAllWithIndex('position_id');
        $this->assign('position_list', $position_list);

        $web_navigation_switch = tpCache('global.web_navigation_switch');
        $this->assign('web_navigation_switch', intval($web_navigation_switch));

        /*获取所有有子栏目的栏目id*/
        $parent_ids = Db::name('nav_list')->where([
                'parent_id' => ['gt', 0],
                'is_del'    => 0,
            ])->group('parent_id')->column('parent_id');
        $cookied_treeclicked =  json_decode(cookie('navigation-treeClicked-Arr'));
        empty($cookied_treeclicked) && $cookied_treeclicked = [];
        $all_treeclicked = cookie('navigation-treeClicked_All');
        empty($all_treeclicked) && $all_treeclicked = [];
        $tree = [
            'has_children'=>!empty($parent_ids) ? 1 : 0,
            'parent_ids'=>json_encode($parent_ids),
            'all_treeclicked'=>$all_treeclicked,
            'cookied_treeclicked'=>$cookied_treeclicked,
            'cookied_treeclicked_arr'=>json_encode($cookied_treeclicked),
        ];
        $this->assign('tree', $tree);
        /* end */

        return $this->fetch('index');
    }

    /**
     * 插件后台管理 - 列表
     */
    public function navigation_index()
    {
        $map = array();
        $list = array();
        $count = $this->position_db->where($map)->count('position_id');
        $pageObj = new Page($count, config('paginate.list_rows'));
        $list = $this->position_db->where($map)->order('sort_order asc,position_id desc')->limit($pageObj->firstRow.','.$pageObj->listRows)->select();
        $pageStr = $pageObj->show();
        $this->assign('list', $list);
        $this->assign('pageStr', $pageStr);
        $this->assign('pager', $pageObj);

        return $this->fetch('navigation_index');
    }

    public function add_position()
    {
        if (IS_POST) {
            $post = input('post.');
            // 导航不可重复
            $PostLevelName = array_unique($post['position_name']);
            if (count($PostLevelName) != count($post['position_name'])) {
                $this->error('导航名称不可重复！');
            }
            // 数据拼装
            $AddUsersLevelData = $where = [];
            foreach ($post['position_name'] as $key => $value) {
                $position_id    = $post['position_id'][$key];
                $sort_order    = !empty($post['sort_order'][$key]) ? $post['sort_order'][$key]:100;
                $position_name  = trim($value);
                if (empty($position_name)) {
                    if (empty($position_id)) {
                        unset($AddUsersLevelData[$key]);
                        continue;
                    }else{
                        $this->error('导航名称不可为空！');
                    }
                }

                $AddUsersLevelData[$key] = [
                    'position_id'    => $position_id,
                    'position_name'  => $position_name,
                    'sort_order'  => $sort_order,
                    'update_time' => getTime(),
                ];

                if (empty($position_id)) {
                    $AddUsersLevelData[$key]['lang']     = $this->admin_lang;
                    $AddUsersLevelData[$key]['add_time'] = getTime();
                    unset($AddUsersLevelData[$key]['position_id']);
                }
            }

            $ReturnId = $this->position_model->saveAll($AddUsersLevelData);
            if ($ReturnId) {
                $position_name = implode(",",$post['position_name']);
                adminLog('新增/编辑导航管理：'.$position_name); // 写入操作日志
                $this->success("操作成功", url('Navigation/index'));
            } else {
                $this->error('操作失败');
            }
        }
    }
    
    /**
     * 插件后台管理 - 新增
     */
    public function add()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            if (!empty($post)) {
                if (empty($post['arctype_sync'])) {
                    if (empty($post['nav_name'])) $this->error("请填写导航名称");
                    if (empty($post['nav_url'])) $this->error("请填写导航链接");
                }
                if (!empty($post['parent_id'])){
                    $post['topid'] = Db::name('nav_list')->where('nav_id',$post['parent_id'])->value('topid');
                    if (0 == $post['topid']){
                        $post['topid'] = $post['parent_id'];
                    }
                }

                /*封面图的本地/远程图片处理*/
                $is_remote = !empty($post['is_remote']) ? $post['is_remote'] : 0;
                $litpic = '';
                if (1 == $is_remote) {
                    $litpic = $post['litpic_remote'];
                } else {
                    $litpic = $post['nav_pic'];
                }
                /*--end*/

                if (!empty($post['type_id'])){
                    $post['arctype_sync'] = 1;
                }

                // url格式化
                $post['nav_url'] = htmlspecialchars_decode($post['nav_url']);

                $newData = array(
                    'target'      => !empty($post['target']) ? 1 : 0,
                    'nofollow'    => !empty($post['nofollow']) ? 1 : 0,
                    'is_del'      => 0,
                    'add_time'    => getTime(),
                    'update_time' => getTime()
                );
                $data = array_merge($post, $newData);
                $insertId = $this->list_db->insert($data);
                if (!empty($insertId)) {
                    Cache::clear('nav_list');
                    adminLog('新增导航管理菜单：'.$data['nav_name']); // 写入操作日志
                    $this->success("操作成功", url('Navigation/index', ['position_id'=>$post['position_id']]));
                }
            }
            $this->error("操作失败!");
        }

        $nav_id = input('nav_id/d', 0);
        $position_id = input('position_id/d', 0);
        // 前台功能下拉框
        $assignData['function'] =  $this->navigationlogic->ForegroundFunction();
        // 全部栏目下拉框
        $assignData['arctypeHtml'] = $this->navigationlogic->getAllArctypeList();
        // 全部导航菜单列表
        $assignData['navListHtml'] = $this->navigationlogic->getAllNavList($position_id, $nav_id);
        $assignData['nav_id'] = $assignData['topid'] = 0;
        if (!empty($nav_id)) {
            $assignData['nav_id'] = intval($nav_id);
            $assignData['topid'] = Db::name('nav_list')->where('nav_id', $nav_id)->getField('topid');
        }
        $this->assign($assignData);

        $position_name = $this->position_db->where('position_id',$position_id)->value('position_name');
        $this->assign('position_name', $position_name);
        return $this->fetch('add');
    }
    
    /**
     * 插件后台管理 - 编辑
     */
    public function edit()
    {
        if (IS_POST) {
            $post = input('post.');
            if (!empty($post)) {
                $post['nav_id'] = intval($post['nav_id']);
                if (empty($post['type_id'])){
                    if (empty($post['nav_name'])) $this->error("请填写导航名称");
                    if (empty($post['nav_url'])) $this->error("请填写导航链接");
                    $post['arctype_sync'] = 0;
                } else {
                    $post['arctype_sync'] = 1;
                }

                // url格式化
                $post['nav_url'] = htmlspecialchars_decode($post['nav_url']);

                $newData = array(
                    'target'      => !empty($post['target']) ? 1 : 0,
                    'nofollow'    => !empty($post['nofollow']) ? 1 : 0,
                    'is_del'      => 0,
                    'update_time' => getTime()
                );
                $data = array_merge($post, $newData);
                $ResultID = $this->list_db->where(['nav_id'=>$data['nav_id']])->cache(true, null, "nav_list")->update($data);
                if($ResultID !== false){
                    adminLog('编辑导航管理菜单：'.$post['nav_name']); // 写入操作日志
                    $this->success("操作成功!", url('Navigation/index', ['position_id'=>$post['position_id']]));
                }
            }
            $this->error("操作失败");
        }

        $ReturnData = array();

        $nav_id = input('nav_id/d', 0);
        if (empty($nav_id)) $this->error("请选择导航");
        $field = 'a.*, b.position_name, c.typename';
        $navigList = $this->list_db
            ->field($field)
            ->alias('a')
            ->join('nav_position b', 'a.position_id = b.position_id', 'LEFT')
            ->join('arctype c', 'a.type_id = c.id', 'LEFT')
            ->where('a.nav_id', $nav_id)
            ->find();
        $navigList['nav_name'] = !empty($navigList['arctype_sync']) ? $navigList['typename'] : $navigList['nav_name'];
        $assignData['navigList'] = $navigList;

        // 前台功能下拉框
        $assignData['function'] =  $this->navigationlogic->ForegroundFunction();
        // 全部栏目下拉框
        $assignData['arctypeHtml'] = $this->navigationlogic->getAllArctypeList($navigList['type_id']);
        // 是否允许修改URL
        $allowUpdate = 1;
        foreach ($assignData['Function'] as $key => $value) {
            if ($navigList['nav_url'] == $value['url']) {
                $allowUpdate = 0; break;
            }
        }
        $assignData['allowUpdate'] = $allowUpdate;

        $this->assign($assignData);
        return $this->fetch();
    }
    
    /**
     * 删除文档
     */
    public function del()
    {
        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if(!empty($id_arr) && IS_POST){
            $result = $this->position_db->where("position_id",'IN',$id_arr)->select();
            $title_list = get_arr_column($result, '导航名称');

            $r = $this->position_db->where("position_id",'IN',$id_arr)->delete();
            if($r !== false){
                $this->list_db->where("position_id",'IN',$id_arr)->cache(true, null, "nav_list")->delete();
                adminLog('删除导航管理：'.implode(',', $title_list));
                $this->success("操作成功!");
            }
        }
        $this->error("操作失败!");
    }

    /**
     * 删除菜单
     */
    public function list_del()
    {
        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if(!empty($id_arr) && IS_POST){
            $result = $this->list_db->where("nav_id",'IN',$id_arr)->whereOr("parent_id",'IN',$id_arr)->select();
            $title_list = get_arr_column($result, '导航名称');
            $id_list = get_arr_column($result, 'nav_id');

            $r = $this->list_db->where("nav_id",'IN',$id_list)->cache(true, null, "nav_list")->delete();
            if($r !== false){
                adminLog('删除导航管理菜单：'.implode(',', $title_list));
                $this->success("操作成功!");
            }
        }
        $this->error("操作失败!");
    }

    /**
     * 开启/关闭导航模块功能
     * @return [type] [description]
     */
    public function ajax_open_close()
    {
        if (IS_AJAX_POST) {
            $value = input('param.value/d');
            if (1 == $value) {
                $web_navigation_switch = 0;
            } else {
                $web_navigation_switch = 1;
            }
            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache('web', ['web_navigation_switch'=>$web_navigation_switch], $val['mark']);
                }
            } else { // 单语言
                tpCache('web', ['web_navigation_switch'=>$web_navigation_switch]);
            }
            /*--end*/
            $this->success("操作成功");
        }
        $this->error("操作失败");
    }

}