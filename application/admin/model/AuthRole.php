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

class AuthRole extends Model{
    protected $name = 'auth_role';

    protected $type = array(
        'language' => 'serialize',
        'cud' => 'serialize',
        'permission' => 'serialize',
    );

    protected function initialize(){
        parent::initialize();
    }

    public function getRole($where){
        $result =Db::name($this->name)->where($where)->find();
        if (!empty($result)) {
            $result['language'] = unserialize($result['language']);
            $result['cud'] = unserialize($result['cud']);
            $result['permission'] = unserialize($result['permission']);
        }

        return $result;
    }

    public function getRoleAll($where = ['status'=>1]){
        $result =Db::name($this->name)->where($where)->order('id asc')->select();
        foreach ($result as $key => $val) {
            $val['language'] = unserialize($val['language']);
            $val['cud'] = unserialize($val['cud']);
            $val['permission'] = unserialize($val['permission']);
            $result[$key] = $val;
        }

        return $result;
    }

    public function saveAuthRole($input, $batchAdminRole = false){

        if (!empty($input['arctype_str'])) {
            $input['permission']['arctype'] = explode(',', $input['arctype_str']);
        }

        $permission = $input['permission'] ? $input['permission'] : null;

        // 角色权限
        $permission_rules = !empty($permission['rules']) ? $permission['rules'] : [];
        /*栏目与内容权限*/
        if (!empty($permission['arctype'])) {
            $permission_rules[] = 2; // 内容管理的权限ID，在conf/auth_rule.php配置文件
        }
        /*--end*/
        /*插件应用权限*/
        if (!empty($permission['plugins'])) {
            $permission_rules[] = 15; // 插件应用的权限ID，在conf/auth_rule.php配置文件
        }
        /*--end*/
        $permission['rules'] = $permission_rules;

        $data = array(
            'name' => trim($input['name']),
            'pid' => ! empty($input['pid']) ? (int)$input['pid'] : 0,
            'grade' => ! empty($input['grade']) ? (int)$input['grade'] : 0,
            'remark' => ! empty($input['remark']) ? $input['remark'] : '',
            'language' => ! empty($input['language']) ? $input['language'] : null,
            'online_update' => ! empty($input['online_update']) ? (int)$input['online_update'] : 0,
            'switch_map' => ! empty($input['switch_map']) ? (int)$input['switch_map'] : 0,
            'editor_visual' => ! empty($input['editor_visual']) ? (int)$input['editor_visual'] : 0,
            'only_oneself' => ! empty($input['only_oneself']) ? (int)$input['only_oneself'] : 0,
            'check_oneself' => ! empty($input['check_oneself']) ? (int)$input['check_oneself'] : 0,
            'cud' => ! empty($input['cud']) ? $input['cud'] : null,
            'permission' => $permission,
            'status' => ! empty($input['status']) ? (int)$input['status'] : 1,
            'sort_order' => ! empty($input['sort_order']) ? (int)$input['sort_order'] : 100,
            'add_time'  => getTime(),
            'update_time'  => getTime(),
        );
        
        if(! empty($input['id']) && $input['id'] > 0){
            $data['id'] = $input['id'];
            $rs = parent::update($data);
            $rs = !empty($rs) ? $input['id'] : $rs;
        }else{
            $data['admin_id'] = session('admin_info.admin_id');
            parent::save($data);
            $rs = Db::name($this->name)->getLastInsID();
        }

        \think\Cache::clear('auth_role');
        return $rs;
    }

    /**
     * 同步栏目ID到权限组，默认是赋予该栏目的权限
     * @param int $typeid
     */
    public function syn_auth_role($typeid = 0)
    {
        if (0 < intval($typeid)) {
            $roleRow = $this->getRoleAll();
            if (!empty($roleRow)) {
                $saveData = [];
                foreach ($roleRow as $key => $val) {
                    $permission = $val['permission'];
                    $rules = !empty($permission['rules']) ? $permission['rules'] : [];
                    if (!in_array(1, $rules)) {
                        continue;
                    }
                    $arctype = !empty($permission['arctype']) ? $permission['arctype'] : [];
                    if (!empty($arctype)) {
                        array_push($arctype, $typeid);
                        $permission['arctype'] = $arctype;
                    }
                    $saveData[] = array(
                        'id'    => $val['id'],
                        'permission'    => $permission,
                    );
                }
                $r = $this->saveAll($saveData);
                if (false != $r && 0 < intval(session('admin_info.role_id'))) {
                    /*及时更新当前管理员权限*/
                    $auth_role_info = $this->getRole(array('id' => session('admin_info.role_id')));
                    session('admin_info.auth_role_info', $auth_role_info);
                    /*--end*/
                }
            }
        }
    }
}