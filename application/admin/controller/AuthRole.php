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
use think\Validate;

class AuthRole extends Base {
    
    public function _initialize() {
        parent::_initialize();
        $this->language_access(); // 多语言功能操作权限
    }
    
    /**
     * 权限组管理
     */
    public function index()
    {   
        $map = array();
        $pid = input('pid/d');
        $keywords = input('keywords/s');
        $keywords = addslashes(trim($keywords));

        if (!empty($keywords)) {
            $map['c.name'] = array('LIKE', "%{$keywords}%");
        }

        $AuthRole =  Db::name('auth_role');
        $count = $AuthRole->alias('c')->where($map)->count();// 查询满足要求的总记录数
        $Page = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $fields = "c.*,s.name AS pname";
        $list = DB::name('auth_role')
            ->field($fields)
            ->alias('c')
            ->join('__AUTH_ROLE__ s','s.id = c.pid','LEFT')
            ->where($map)
            ->order('c.id asc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pager',$Page);// 赋值分页集

        return $this->fetch();
    }
    
    /**
     * 新增权限组
     */
    public function add()
    {
        if (IS_POST) {
            $rule = array(
                'name'  => 'require',
            );
            $msg = array(
                'name.require' => '权限组名称不能为空！',
            );
            $data = array(
                'name' => trim(input('name/s')),
            );
            $validate = new Validate($rule, $msg);
            $result   = $validate->check($data);
            if(!$result){
                $this->error($validate->getError());
            }

            $model = model('AuthRole');
            $count = $model->where('name', $data['name'])->count();
            if(! empty($count)){
                $this->error('该权限组名称已存在，请检查');
            }
            $role_id = $model->saveAuthRole(input());
            if($role_id){
                adminLog('新增权限组：'.$data['name']);
                $admin_role_list = model('AuthRole')->getRoleAll();
                $this->success('操作成功', url('AuthRole/index'), ['role_id'=>$role_id,'role_name'=>$data['name'],'admin_role_list'=>json_encode($admin_role_list)]);
            }else{
                $this->error('操作失败');
            }
        }

        // 权限组
        $admin_role_list = model('AuthRole')->getRoleAll();
        $this->assign('admin_role_list', $admin_role_list);

        // 模块组
        $modules = getAllMenu();
        $this->assign('modules', $modules);

        // 权限集
        // $singleArr = array_multi2single($modules, 'child'); // 多维数组转为一维
        $auth_rules = get_auth_rule(['is_modules'=>1]);
        $auth_rule_list = group_same_key($auth_rules, 'menu_id');
        foreach ($auth_rule_list as $key => $val) {
            if (is_array($val)) {
                $sort_order = [];
                foreach ($val as $_k => $_v) {
                    $sort_order[$_k]  = $_v['sort_order'];
                }
                array_multisort($sort_order, SORT_ASC, $val);
                $auth_rule_list[$key] = $val;
            }
        }
        $this->assign('auth_rule_list', $auth_rule_list);

        // 栏目
        $arctype_list = Db::name('arctype')->where([
                'is_del'    => 0,
            ])->order("grade desc")->select();
        $arctype_p_html = $arctype_child_html = "";
        $arctype_all = list_to_tree($arctype_list);
        foreach ($arctype_all as $key => $arctype) {
            if (!empty($arctype['children'])) {
                if ($key > 0) {
                    $arctype_p_html .= '<em class="arctype_bg expandable"></em>';
                } else {
                    $arctype_p_html .= '<em class="arctype_bg collapsable"></em>';
                }
                $arctype_child_html .= '<div class="arctype_child" id="arctype_child_' . $arctype['id'] . '"';
                if ($arctype_all[0]['id'] == $arctype['id']) {
                    $arctype_child_html .= ' style="display: block;" ';
                }
                $arctype_child_html .= '>';
                $arctype_child_html .= $this->get_arctype_child_html($arctype);
                $arctype_child_html .= '</div>';
            }
            $arctype_p_html .= '<label>' .
                '<input type="checkbox" class="arctype_cbox arctype_id_' . $arctype['id'] . '" value="' . $arctype['id'] . '" ';
            $arctype_p_html .= ' />' . $arctype['typename'] . '</label>&nbsp;';
        }
        $this->assign('arctype_p_html', $arctype_p_html);
        $this->assign('arctype_child_html', $arctype_child_html);

        // 插件
        $plugins = false;
        $web_weapp_switch = tpCache('global.web_weapp_switch');
        if (1 == $web_weapp_switch) {
            $plugins = model('Weapp')->getList(['status'=>1]);
        }
        $this->assign('plugins', $plugins);

        return $this->fetch();
    }

    //xyz修改20220315
    public function edit()
    {
        $id = input('param.id/d', 0);
        if ($id <= 0) {
            $this->error('非法访问');
        }
        if (IS_POST) {
            $rule = array(
                'name' => 'require',
            );
            $msg = array(
                'name.require' => '权限组名称不能为空！',
            );
            $data = array(
                'name' => trim(input('name/s')),
            );
            $validate = new Validate($rule, $msg);
            $result = $validate->check($data);
            if (!$result) {
                $this->error($validate->getError());
            }

            $model = model('AuthRole');
            $count = $model->where('name', $data['name'])
                ->where('id', '<>', $id)
                ->count();
            if (!empty($count)) {
                $this->error('该权限组名称已存在，请检查');
            }
            $role_id = $model->saveAuthRole(input(), true);
            if ($role_id) {
                adminLog('编辑权限组：' . $data['name']);
                $this->success('操作成功', url('AuthRole/index'), ['role_id' => $role_id, 'role_name' => $data['name']]);
            } else {
                $this->error('操作失败');
            }
        }
        $model = model('AuthRole');
        $info = $model->getRole(array('id' => $id));
        if (empty($info)) {
            $this->error('数据不存在，请联系管理员！');
        }
        $this->assign('info', $info);
        // 权限组
        $admin_role_list = model('AuthRole')->getRoleAll();
        $this->assign('admin_role_list', $admin_role_list);
        // 模块组
        $modules = getAllMenu();
        $this->assign('modules', $modules);
        // 权限集
        $auth_rules = get_auth_rule(['is_modules' => 1]);
        $auth_rule_list = group_same_key($auth_rules, 'menu_id');
        foreach ($auth_rule_list as $key => $val) {
            if (is_array($val)) {
                $sort_order = [];
                foreach ($val as $_k => $_v) {
                    $sort_order[$_k] = $_v['sort_order'];
                }
                array_multisort($sort_order, SORT_ASC, $val);
                $auth_rule_list[$key] = $val;
            }
        }
        $this->assign('auth_rule_list', $auth_rule_list);

        // 栏目
        $arctype_list = Db::name('arctype')->where([
            'is_del' => 0,
        ])->order("grade desc")->select();
        $arctype_p_html = $arctype_child_html = "";
        $arctype_all = list_to_tree($arctype_list);
        foreach ($arctype_all as $key => $arctype) {
            if (!empty($arctype['children'])) {
                if ($key > 0) {
                    $arctype_p_html .= '<em class="arctype_bg expandable"></em>';
                } else {
                    $arctype_p_html .= '<em class="arctype_bg collapsable"></em>';
                }
                $arctype_child_html .= '<div class="arctype_child" id="arctype_child_' . $arctype['id'] . '"';
                if ($arctype_all[0]['id'] == $arctype['id']) {
                    $arctype_child_html .= ' style="display: block;" ';
                }
                $arctype_child_html .= '>';
                $arctype_child_html .= $this->get_arctype_child_html($arctype,$info);
                $arctype_child_html .= '</div>';
            }
            $arctype_p_html .= '<label>' .
                '<input type="checkbox" class="arctype_cbox arctype_id_' . $arctype['id'] . '" value="' . $arctype['id'] . '" ';
            if (!empty($info['permission']['arctype']) && in_array($arctype['id'], $info['permission']['arctype'])) {
                $arctype_p_html .= ' checked="checked" ';
            }
            $arctype_p_html .= ' />' . $arctype['typename'] . '</label>&nbsp;';

        }
        $this->assign('arctype_p_html', $arctype_p_html);
        $this->assign('arctype_child_html', $arctype_child_html);
        
        // 插件
        $plugins = false;
        $web_weapp_switch = tpCache('global.web_weapp_switch');
        if (1 == $web_weapp_switch) {
            $plugins = model('Weapp')->getList(['status'=>1]);
        }
        $this->assign('plugins', $plugins);

        return $this->fetch();
    }
    /*
     *  递归生成$arctype_child_html
     *  $vo             栏目tree
     *  $info           权限集合（用于edit是否已经选中）
     *  return          完整html
     */
    private function get_arctype_child_html($vo,$info = []){
        $arctype_child_html = "";
        if (!empty($vo['children'])) {
            $arctype_child_html .= '<div class="arctype_child1" id="arctype_child_' . $vo['id'] . '">';
            //判断当前下级是否还存在下级,true为竖着，false为横着
            $has_chldren = true;
            if ($vo['grade'] != 0 && !empty($vo['has_chldren']) && $vo['has_chldren'] == count($vo['children'])){
                $has_chldren = false;
            }
            if ($has_chldren){
                foreach ($vo['children'] as $vo1) {
                    $arctype_child_html .= '<div class="arctype_child1">';
                    $arctype_child_html .= ' <span class="button level1 switch center_docu"></span>
                                            <label><input type="checkbox" class="arctype_cbox arctype_id_' . $vo1['id'] . '" value="' . $vo1['id'] . '" data-pid="' . $vo1['parent_id'] . '" data-tpid="' . $vo['parent_id'] . '"';
                    if (!empty($info['permission']['arctype']) && in_array($vo1['id'], $info['permission']['arctype'])) {
                        $arctype_child_html .= ' checked="checked" ';
                    }
                    $arctype_child_html .= '/>' . $vo1['typename'] . '</label></div>';
                    $arctype_child_html .= $this->get_arctype_child_html($vo1,$info);
                }
            }else{
                $arctype_child_html .= '<div class="arctype_child2"> <span class="button level1 switch center_docu"></span>';
                foreach ($vo['children'] as $vo1) {
                    $arctype_child_html .= '<label><input type="checkbox" class="arctype_cbox arctype_id_' . $vo1['id'] . '" value="' . $vo1['id'] . '" data-pid="' . $vo1['parent_id'] . '" data-tpid="' . $vo['parent_id'] . '"';
                    if (!empty($info['permission']['arctype']) && in_array($vo1['id'], $info['permission']['arctype'])) {
                        $arctype_child_html .= ' checked="checked" ';
                    }
                    $arctype_child_html .= '/>' . $vo1['typename'] . '</label>';
                    $arctype_child_html .= $this->get_arctype_child_html($vo1,$info);
                }
                $arctype_child_html .= '</div>';
            }
            $arctype_child_html .= '</div>';
        }

        return $arctype_child_html;
    }
    
    public function del()
    {
        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if (!empty($id_arr)) {

            $count = Db::name('auth_role')->where(['built_in'=>1,'id'=>['IN',$id_arr]])->count();
            if (!empty($count)) {
                $this->error('系统内置不允许删除！');
            }

            $role = Db::name('auth_role')->where("pid",'IN',$id_arr)->select();
            if ($role) {
                $this->error('请先清空该权限组下的子权限组');
            }

            $role_admin = Db::name('admin')->where("role_id",'IN',$id_arr)->select();
            if ($role_admin) {
                $this->error('请先清空所属该权限组的管理员');
            } else {
                $r = Db::name('auth_role')->where("id",'IN',$id_arr)->delete();
                if($r){
                    adminLog('删除权限组');
                    $this->success('删除成功');
                }else{
                    $this->error('删除失败');
                }
            }
        } else {
            $this->error('参数有误');
        }
    }
}