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
        $this->_initialize();
    }

    private function _initialize() {
        if ('POST' == self::$method) {
//            $this->checkRepeatTitle();
            $this->clearWeapp();
            $this->instyes();
        } else {
            $this->unotice();
            $this->verifyfile();
        }
    }

    private function verifyfile()
    {
        $tmp1 = 'cGhwLnBocF9zZXJ2aW'.'NlaW5mbw==';
        $tmp1 = base64_decode($tmp1);
        $data = tpCache($tmp1);
        $data = mchStrCode($data, 'DECODE');
        $data = json_decode($data, true);
        if (empty($data['pid']) || 2 > $data['pid']) return true;
        $file = "./data/conf/{$data['code']}.txt";
        $tmp2 = 'cGhwX3NlcnZpY2VtZWFs';
        $tmp2 = base64_decode($tmp2);
        if (!file_exists($file)) {
            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')->select();
                foreach ($langRow as $key => $val) {
                    tpCache('php', [$tmp2=>1], $val['mark']);
                }
            } else { // 单语言
                tpCache('php', [$tmp2=>1]);
            }
            /*--end*/
        } else {
            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')->select();
                foreach ($langRow as $key => $val) {
                    tpCache('php', [$tmp2=>$data['pid']], $val['mark']);
                }
            } else { // 单语言
                tpCache('php', [$tmp2=>$data['pid']]);
            }
            /*--end*/
        }
    }

    private function unotice(){
        $str = 'VXNlcnNOb3RpY2U=';
        if (self::$controllerName == base64_decode($str)) {
            $str = 'd2ViLndlYl9pc19hdXRob3J0b2tlbg==';
            $value = tpCache(base64_decode($str));
            if (-1 == $value) {
                $str = '6K+l5Yqf6IO95LuF6ZmQ5LqO5ZWG5Lia5o6I5p2D5Z+f5ZCN77yB';
                $this->error(base64_decode($str));
            }
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
//    private function checkRepeatTitle()
//    {
//        /*只有相应的控制器和操作名才执行，以便提高性能*/
//        $ctlArr = \think\Db::name('channeltype')->field('id,ctl_name,is_repeat_title')
//            ->where('nid','NOT IN', ['guestbook','single'])
//            ->getAllWithIndex('ctl_name');
//        $actArr = ['add','edit'];
//        if (!empty($ctlArr[self::$controllerName]) && in_array(self::$actionName, $actArr)) {
//            /*模型否开启文档重复标题的检测*/
//            if (empty($ctlArr[self::$controllerName]['is_repeat_title'])) {
//                $map = array(
//                    'title' => $_POST['title'],
//                );
//                if ('edit' == self::$actionName) {
//                    $map['aid'] = ['NEQ', $_POST['aid']];
//                }
//                $count = \think\Db::name('archives')->where($map)->count('aid');
//                if(!empty($count)){
//                    $this->error('该标题已存在，请更改');
//                }
//            }
//            /*--end*/
//        }
//        /*--end*/
//    }

    /**
     * @access private
     */
    private function instyes()
    {
        $ca = md5(self::$actionName.'@'.self::$controllerName);
        if ('0e3e00da04fcf78cd9fd7dc763d956fc' == $ca) {
            $s = '5a6J'.'6KOF'.'5oiQ5'.'Yqf';
            if (1605110400 < getTime()) {
                sleep(5);
                $this->success(base64_decode($s));
            }
        }
    }
}
