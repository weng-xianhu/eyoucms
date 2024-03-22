<?php

namespace app\admin\behavior;

/**
 * 管理员权限控制
 */
load_trait('controller/Jump');
class AuthRoleBehavior
{
    use \traits\controller\Jump;
    protected static $actionName;
    protected static $controllerName;
    protected static $moduleName;
    protected static $method;
    protected static $admin_info;

    /**
     * 构造方法
     * @param Request $request Request对象
     * @access public
     */
    public function __construct()
    {
        !isset(self::$moduleName) && self::$moduleName = request()->module();
        !isset(self::$controllerName) && self::$controllerName = request()->controller();
        !isset(self::$actionName) && self::$actionName = request()->action();
        !isset(self::$method) && self::$method = request()->method();
        !isset(self::$admin_info) && self::$admin_info = session('admin_info');
    }

    /**
     * 模块初始化
     * @param array $params 传入参数
     * @access public
     */
    public function moduleInit(&$params)
    {

    }

    /**
     * 操作开始执行
     * @param array $params 传入参数
     * @access public
     */
    public function actionBegin(&$params)
    {
        if (0 < intval(self::$admin_info['role_id'])) {
            // 检测全局的增、改、删的权限——优先级最高
            $this->cud_access();
            // 检测每个小插件的权限
            $this->weapp_access();
            // 检测栏目管理的每个栏目权限
            $this->arctype_access();
            // 检测内容管理每个栏目对应的内容里列表等权限
            $this->archives_access();
        }
    }

    /**
     * 视图内容过滤
     * @param array $params 传入参数
     * @access public
     */
    public function viewFilter(&$params)
    {

    }

    /**
     * 应用结束
     * @param array $params 传入参数
     * @access public
     */
    public function appEnd(&$params)
    {

    }

    /**
     * 检测全局的增、改、删的权限
     * @access private
     */
    private function cud_access()
    {
        /*只有相应的控制器和操作名才执行，以便提高性能*/
        $ctl = strtolower(self::$controllerName);
        $act = strtolower(self::$actionName);
        if ('archives' == $ctl && in_array($act, ['check','uncheck'])) { // 审核信息\取消审核信息
            $act = 'changetableval';
        }

        $actArr = ['add','edit','del','rebackdraft'];
        if ('weapp' == $ctl && 'execute' == $act) {
            $sa = input('param.sa/s');
            foreach ($actArr as $key => $cud) {
                $sa = preg_replace('/^(.*)_('.$cud.')$/i', '$2', $sa); // 同名add 或者以_add类似结尾都符合
                if ($sa == $cud) {
                    $admin_info = self::$admin_info;
                    $auth_role_info = !empty($admin_info['auth_role_info']) ? $admin_info['auth_role_info'] : [];
                    $cudArr = !empty($auth_role_info['cud']) ? $auth_role_info['cud'] : [];
                    if (!in_array($sa, $cudArr)) {
                        $this->error('您没有操作权限，请联系超级管理员分配权限');
                    }
                    break;
                }
            }
        } else {
            $post = input('post.');
            array_push($actArr, 'changetableval'); // 审核信息
            foreach ($actArr as $key => $cud) {
                $act = preg_replace('/^(.*)_('.$cud.')$/i', '$2', $act); // 同名add 或者以_add类似结尾都符合
                if ($act == $cud) {
                    $admin_info = self::$admin_info;
                    $auth_role_info = !empty($admin_info['auth_role_info']) ? $admin_info['auth_role_info'] : [];
                    $cudArr = !empty($auth_role_info['cud']) ? $auth_role_info['cud'] : [];
                    if (!in_array($act, $cudArr)) {
                        if ('changetableval' == $act && 'index' == $ctl) {
                            // 审核信息
                            if ('archives' == $post['table'] && 'arcrank' == $post['field']) {
                                $this->error('您没有操作权限，请联系超级管理员分配权限', null, '', 2);
                            }
                        } else {
                            $this->error('您没有操作权限，请联系超级管理员分配权限');
                        }
                    } else {
                        if (!in_array('changetableval', $cudArr)) {
                            // 审核信息
                            if (IS_POST && 'edit' == $act) {
                                $archivesInfo = M('archives')->field('arcrank,admin_id')->find($post['aid']);
                                if (-1 == $archivesInfo['arcrank'] && isset($post['arcrank']) && $archivesInfo['arcrank'] != $post['arcrank']) {
                                    $this->error('您没有操作权限，请联系超级管理员分配权限', url('Archives/edit', ['id'=>$post['aid']]), '', 3);
                                }
                            }
                        }
                    }
                    break;
                }
            }
        }
        /*--end*/
    }

    /**
     * 检测每个小插件的权限
     * @access private
     */
    private function weapp_access()
    {
        /*只有相应的控制器和操作名才执行，以便提高性能*/
        $ctl = strtolower(self::$controllerName);
        $act = strtolower(self::$actionName);
        if ('weapp' == $ctl) {
            if ('execute' == $act) {
                $sc = input('param.sc/s');
                $sm = input('param.sm/s');
                $sa = input('param.sa/s');
                $admin_info = self::$admin_info;
                $auth_role_info = !empty($admin_info['auth_role_info']) ? $admin_info['auth_role_info'] : [];
                $plugins = !empty($auth_role_info['permission']['plugins']) ? $auth_role_info['permission']['plugins'] : [];
                // 插件本身设置的权限列表
                $config = include WEAPP_PATH.$sm.DS.'config.php';
                $plugins_permission = !empty($config['permission']) ? array_keys($config['permission']) : [];
                // 管理员拥有的插件具体权限
                $admin_permission = !empty($plugins[$sm]['child']) ? $plugins[$sm]['child'] : [];
                // 没有赋给管理员的插件具体权限
                $diff_plugins_perm = array_diff($plugins_permission, $admin_permission);
                // 检测插件权限
                $bool = true;
                if (empty($plugins_permission) && !isset($plugins[$sm])) {
                    $bool = false;
                } else if (!empty($plugins_permission)) {
                    foreach ($diff_plugins_perm as $key => $val) {
                        if (strtolower($sm.'@'.$sa) == strtolower($val)) {
                            $bool = false;
                            break;
                        }
                    }
                }
                if (!$bool) {
                    $this->error('您没有操作权限，请联系超级管理员分配权限');
                }
            } else if (in_array($act, ['plugin','mybuy'])) {
                if (0 < intval(session('admin_info.role_id'))) {
                    $this->error('您没有操作权限，只允许创始人和超级管理员操作');
                }
            }
        }
        /*--end*/
    }

    /**
     * 检测栏目管理的每个栏目权限
     * @access private
     */
    private function arctype_access()
    {
        /*只有相应的控制器和操作名才执行，以便提高性能*/
        $ctl_all = strtolower(self::$controllerName.'@*');
        $ctlArr = ['arctype@*'];
        if (in_array($ctl_all, $ctlArr)) {
            $typeids = [];
            if (in_array(strtolower(self::$actionName), ['edit','del'])) {
                $typeids[] = input('id/d', 0);
            } else if (in_array(strtolower(self::$actionName), ['add'])) {
                $typeids[] = input('parent_id/d', 0);
            }
            if (!$this->is_check_arctype($typeids)) {
                $this->error('您没有操作权限，请联系超级管理员分配权限');
            }
        }
        /*--end*/
    }

    /**
     * 检测内容管理每个栏目对应的内容里列表等权限
     * @access private
     */
    private function archives_access()
    {
        /*只有相应的控制器和操作名才执行，以便提高性能*/
        $ctl = strtolower(self::$controllerName);
        $act = strtolower(self::$actionName);
        $ctl_act = $ctl.'@'.$act;
        $ctl_all = $ctl.'@*';

        $ctlArr= ['arctype@single','archives@*'];
        $row = \think\Db::name('channeltype')
            ->where('nid','NOTIN', ['single'])
            ->column('ctl_name');
        foreach ($row as $key => $val) {
            array_push($ctlArr, strtolower($val).'@*');
        }
        if (in_array($ctl_act, $ctlArr) || in_array($ctl_all, $ctlArr)) {
            $typeids = [];
            if (in_array($act, ['add','edit','del'])) {
                $aids = [];
                switch ($act) {
                    case 'edit':
                        $aids = input('id/a', []);
                        break;

                    case 'del':
                        $aids = input('del_id/a', []);
                        break;
                    
                    default:
                        # code...
                        break;
                }
                if (!empty($aids)) {
                    $typeids = M('archives')->where('aid','IN',$aids)->column('typeid');
                }
            } else {
                $typeids[] = input('typeid/d', 0);
            }
            if (!$this->is_check_arctype($typeids)) {
                $this->error('您没有操作权限，请联系超级管理员分配权限');
            }
        }
        /*--end*/
    }

    /**
     * 检测栏目是否有权限
     */
    private function is_check_arctype($typeids = []) {  
        $bool_flag = true;
        $admin_info = self::$admin_info;
        if (0 < intval($admin_info['role_id'])) {
            $auth_role_info = $admin_info['auth_role_info'];
            $permission = $auth_role_info['permission'];

            foreach ($typeids as $key => $tid) {
                if (0 < intval($tid) && !in_array($tid, $permission['arctype'])) {
                    $bool_flag = false;
                    break;
                }
            }
        }

        return $bool_flag;
    }
}