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

        if (!empty($keywords)) {
            $map['c.name'] = array('LIKE', "%{$keywords}%");
        }

        $AuthRole =  M('auth_role');     
        $count = $AuthRole->alias('c')->where($map)->count();// 查询满足要求的总记录数
        $Page = new Page($count, 10);// 实例化分页类 传入总记录数和每页显示的记录数
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
        $this->assign('auth_rule_list', $auth_rule_list);

        // 栏目
        $arctype_data = $arctype_array = array();
        $arctype = M('arctype')->select();
        if(! empty($arctype)){
            foreach ($arctype as $item){
                if($item['parent_id'] <= 0){
                    $arctype_data[] = $item;
                }
                $arctype_array[$item['parent_id']][] = $item;
            }
        }
        $this->assign('arctypes', $arctype_data);
        $this->assign('arctype_array', $arctype_array);

        // 插件
        $plugins = false;
        $web_weapp_switch = tpCache('web.web_weapp_switch');
        if (1 == $web_weapp_switch) {
            $plugins = model('Weapp')->getList(['status'=>1]);
        }
        $this->assign('plugins', $plugins);

        return $this->fetch();
    }
    
    public function edit()
    {
        $id = input('id/d', 0);
        if($id <= 0){
            $this->error('非法访问');
        }

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
            $count = $model->where('name', $data['name'])
                ->where('id', '<>', $id)
                ->count();
            if(! empty($count)){
                $this->error('该权限组名称已存在，请检查');
            }
            $role_id = $model->saveAuthRole(input(), true);
            if($role_id){
                adminLog('编辑权限组：'.$data['name']);
                $this->success('操作成功', url('AuthRole/index'), ['role_id'=>$role_id,'role_name'=>$data['name']]);
            }else{
                $this->error('操作失败');
            }
        }

        $model = model('AuthRole');
        $info = $model->getRole(array('id' => $id));
        if(empty($info)){
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
        $auth_rules = get_auth_rule(['is_modules'=>1]);
        $auth_rule_list = group_same_key($auth_rules, 'menu_id');
        $this->assign('auth_rule_list', $auth_rule_list);

        // 栏目
        $arctype_data = $arctype_array = array();
        $arctype = M('arctype')->select();
        if(! empty($arctype)){
            foreach ($arctype as $item){
                if($item['parent_id'] <= 0){
                    $arctype_data[] = $item;
                }
                $arctype_array[$item['parent_id']][] = $item;
            }
        }
        $this->assign('arctypes', $arctype_data);
        $this->assign('arctype_array', $arctype_array);

        // 插件
        $plugins = false;
        $web_weapp_switch = tpCache('web.web_weapp_switch');
        if (1 == $web_weapp_switch) {
            $plugins = model('Weapp')->getList(['status'=>1]);
        }
        $this->assign('plugins', $plugins);

        return $this->fetch();
    }
    
    public function del()
    {
        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if (!empty($id_arr)) {

            $count = M('auth_role')->where(['built_in'=>1,'id'=>['IN',$id_arr]])->count();
            if (!empty($count)) {
                $this->error('系统内置不允许删除！');
            }

            $role = M('auth_role')->where("pid",'IN',$id_arr)->select();
            if ($role) {
                $this->error('请先清空该权限组下的子权限组');
            }

            $role_admin = M('admin')->where("role_id",'IN',$id_arr)->select();
            if ($role_admin) {
                $this->error('请先清空所属该权限组的管理员');
            } else {
                $r = M('auth_role')->where("id",'IN',$id_arr)->delete();
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