<?php

namespace app\admin\behavior;

/**
 * 系统行为扩展：新增/更新/删除之后的后置操作
 */
load_trait('controller/Jump');
class ActionBeginBehavior {
    use \traits\controller\Jump;
    protected static $actionName;
    protected static $controllerName;
    protected static $moduleName;
    protected static $method;

    /**
     * 构造方法
     * @param Request $request Request对象
     * @access public
     */
    public function __construct()
    {

    }

    // 行为扩展的执行入口必须是run
    public function run(&$params){
        self::$actionName = request()->action();
        self::$controllerName = request()->controller();
        self::$moduleName = request()->module();
        self::$method = request()->method();
        // file_put_contents ( DATA_PATH."log.txt", date ( "Y-m-d H:i:s" ) . "  " . var_export('admin_AfterSaveBehavior',true) . "\r\n", FILE_APPEND );
        $this->_initialize();
    }

    private function _initialize() {
        if ('POST' == self::$method) {
            $this->checkRepeatTitle();
            $this->clearWeapp();
        }
    }

    /**
     * 插件每次post提交都清除插件相关缓存
     * @access private
     */
    private function clearWeapp()
    {
        /*只有相应的控制器和操作名才执行，以便提高性能*/
        $ctlActArr = array(
            'Weapp@*',
        );
        $ctlActStr = self::$controllerName.'@*';
        if (in_array($ctlActStr, $ctlActArr)) {
            \think\Cache::clear('hooks');
        }
        /*--end*/
    }

    /**
     * 发布或编辑时，检测文档标题的重复性
     * @access private
     */
    private function checkRepeatTitle()
    {
        /*只有相应的控制器和操作名才执行，以便提高性能*/
        $ctlArr = \think\Db::name('channeltype')->field('id,ctl_name,is_repeat_title')
            ->where('nid','NOTIN', ['guestbook','single'])
            ->getAllWithIndex('ctl_name');
        $actArr = ['add','edit'];
        if (!empty($ctlArr[self::$controllerName]) && in_array(self::$actionName, $actArr)) {
            /*模型否开启文档重复标题的检测*/
            if (empty($ctlArr[self::$controllerName]['is_repeat_title'])) {
                $map = array(
                    'title' => $_POST['title'],
                );
                if ('edit' == self::$actionName) {
                    $map['aid'] = ['NEQ', $_POST['aid']];
                }
                $count = \think\Db::name('archives')->where($map)->count('aid');
                if(!empty($count)){
                    $this->error('该标题已存在，请更改');
                }
            }
            /*--end*/
        }
        /*--end*/
    }
}
