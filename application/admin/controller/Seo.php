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
use think\Db;
use think\Cache;
use app\common\logic\ArctypeLogic;
use think\paginator\driver; // 生成静态页面代码
class Seo extends Base
{

    public function _initialize() {
        parent::_initialize();
        $this->language_access(); // 多语言功能操作权限
    }

    /*
     * 生成总站
     */
    public function site(){
        return $this->fetch();
    }

    /*
     * 生成栏目页
     */
    public function channel(){
        $typeid = input('param.typeid/d','0'); // 栏目ID
        $this->assign("typeid",$typeid);
        return $this->fetch();
    }

    /*
     * 生成文档页
     */
    public function article()
    {
        $typeid = input('param.typeid/d','0'); // 栏目ID
        $this->assign("typeid",$typeid);
        return $this->fetch();
    }
    
    /*
     * URL配置
     */
    public function seo()
    {
        /* 纠正栏目的HTML目录路径字段值 */
        $this->correctArctypeDirpath();
        /* end */

        $inc_type =  'seo';
        $config = tpCache($inc_type);
        $config['seo_pseudo'] = tpCache('seo.seo_pseudo');
        $seo_pseudo_list = get_seo_pseudo_list();
        $this->assign('seo_pseudo_list', $seo_pseudo_list);

        /* 生成静态页面代码 - 多语言统一设置URL模式 */
        $seo_pseudo_lang = '';
        $web_language_switch = tpCache('web.web_language_switch');
        if (is_language() && !empty($web_language_switch)) {
            $markArr = Db::name('language')->field('mark')->order('id asc')->limit('1,1')->select();
            if (!empty($markArr[0]['mark'])) {
                $seo_pseudo_lang = tpCache('seo.seo_pseudo', [], $markArr[0]['mark']);
            }
            $seo_pseudo_lang = !empty($seo_pseudo_lang) ? $seo_pseudo_lang : 1;
        }
        $this->assign('seo_pseudo_lang', $seo_pseudo_lang);
        /* end */

        /* 限制文档HTML保存路径的名称 */
        $wwwroot_dir = config('global.wwwroot_dir'); // 网站根目录的目录列表
        $disable_dirname = config('global.disable_dirname'); // 栏目伪静态时的路由路径
        $wwwroot_dir = array_merge($wwwroot_dir, $disable_dirname);
        // 不能与栏目的一级目录名称重复
        $arctypeDirnames = Db::name('arctype')->where(['parent_id'=>0])->column('dirname');
        is_array($arctypeDirnames) && $wwwroot_dir = array_merge($wwwroot_dir, $arctypeDirnames);
        // 不能与多语言的标识重复
        $markArr = Db::name('language_mark')->column('mark');
        is_array($markArr) && $wwwroot_dir = array_merge($wwwroot_dir, $markArr);
        $wwwroot_dir = array_unique($wwwroot_dir);
        $this->assign('seo_html_arcdir_limit', implode(',', $wwwroot_dir));
        /* end */

        $seo_html_arcdir_1 = '';
        if (!empty($config['seo_html_arcdir'])) {
            $config['seo_html_arcdir'] = trim($config['seo_html_arcdir'], '/');
            $seo_html_arcdir_1 = '/'.$config['seo_html_arcdir'];
        }
        $this->assign('seo_html_arcdir_1', $seo_html_arcdir_1);

        // 栏目列表
        $map = array(
            'status'  => 1,
            'is_del'  => 0, // 回收站功能
            'current_channel'    => ['neq', 51], // 问答模型
            'weapp_code'    => '',
        );
        $arctypeLogic = new ArctypeLogic();
        $select_html = $arctypeLogic->arctype_list(0, 0, true, config('global.arctype_max_level'), $map);
        $this->assign('select_html',$select_html);
        // 允许发布文档列表的栏目
        $arc_select_html = allow_release_arctype();
        $this->assign('arc_select_html', $arc_select_html);
        // 生成完页面之后，清除缓存
        $this->buildhtml_clear_cache();

        /*标记是否第一次切换静态页面模式*/
        if (!isset($config['seo_html_arcdir'])) {
            $init_html = 1; // 第一次切换
        } else {
            $init_html = 2; // 多次切换
        }
        $this->assign('init_html', $init_html);
        /*--end*/

        $this->assign('config',$config);//当前配置项
        return $this->fetch();
    }
    
    /*
     * 保存URL配置
     */
    public function handle()
    {
        if (IS_POST) {
            $inc_type = 'seo';
            $param = input('post.');
            $globalConfig = tpCache('global');
            $seo_pseudo_new = $param['seo_pseudo'];

            /*伪静态格式*/
            if (3 == $seo_pseudo_new && in_array($param['seo_rewrite_format'], [1,3])) {
                $param['seo_rewrite_format'] = $param['seo_rewrite_view_format'];
            }
            /*--end*/

            /* 生成静态页面代码 */
            unset($param['seo_html_arcdir_limit']);
            if (!empty($param['seo_html_arcdir']) && !preg_match('/^([0-9a-zA-Z\_\-\/]+)$/i', $param['seo_html_arcdir'])) {
                $this->error('页面保存路径的格式错误！');
            }
            if (!empty($param['seo_html_arcdir'])) {
                if (preg_match('/^([0-9a-zA-Z\_\-\/]+)$/i', $param['seo_html_arcdir'])) {
                    // $param['seo_html_arcdir'] = ROOT_DIR.'/'.trim($param['seo_html_arcdir'], '/');
                    $param['seo_html_arcdir'] = '/'.trim($param['seo_html_arcdir'], '/');
                } else {
                    $this->error('页面保存路径的格式错误！');
                }
            }
            $seo_html_arcdir_old = !empty($globalConfig['seo_html_arcdir']) ? $globalConfig['seo_html_arcdir'] : '';
            /* end */

            /*检测是否开启pathinfo模式*/
            try {
                if (3 == $seo_pseudo_new || (1 == $seo_pseudo_new && 2 == $param['seo_dynamic_format'])) {
                    $fix_pathinfo = ini_get('cgi.fix_pathinfo');
                    if (stristr($_SERVER['HTTP_HOST'], '.mylightsite.com')) {
                        $this->error('腾讯云空间不支持伪静态！');
                    } else if ('' != $fix_pathinfo && 0 === $fix_pathinfo) {
                        $this->error('空间不支持伪静态，请开启pathinfo，或者在php.ini里修改cgi.fix_pathinfo=1');
                    }
                }
                /* 生成静态页面代码 - URL模式切换时删掉根目录下的index.html静态文件 */
                if(1 == $seo_pseudo_new || 3 == $seo_pseudo_new){
                    if(file_exists('./index.html')){
                        @unlink('./index.html');
                    }
                }
                /* end */
            } catch (\Exception $e) {}
            /*--end*/

            /*强制去除index.php*/
            if (isset($param['seo_force_inlet'])) {
                $seo_force_inlet = $param['seo_force_inlet'];
                $seo_force_inlet_old = !empty($globalConfig['seo_force_inlet']) ? $globalConfig['seo_force_inlet'] : '';
                if ($seo_force_inlet_old != $seo_force_inlet) {
                    $param['seo_inlet'] = $seo_force_inlet;
                }
            }
            /*--end*/

            /*多语言*/
            if (is_language()) {
                $seo_pseudo_lang = !empty($param['seo_pseudo_lang']) ? $param['seo_pseudo_lang'] : 1;
                unset($param['seo_pseudo_lang']);
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    if (2 != $seo_pseudo_new) { // 非生成静态模式下，所有语言的URL模式一致
                        tpCache($inc_type,$param,$val['mark']);
                    } else {
                        if($key == 0){ // 主体语言（第一个语言）是生成静态模式
                            tpCache($inc_type,$param,$val['mark']);
                        }else{//其他语言统一设置URL模式非静态模式
                            $param['seo_pseudo'] = $seo_pseudo_lang;
                            tpCache($inc_type,$param,$val['mark']);
                        }
                    }
                }
            } else {
                tpCache($inc_type,$param);
            }
            /*--end*/

            $is_buildhtml = input('is_buildhtml/d');
            if (!empty($is_buildhtml) && !file_exists('./index.php')) {
                $this->error('网站根目录缺少 index.php 文件，请拷贝该文件上传到空间里！');
            }
            $this->update_paginatorfile();

            delFile(rtrim(HTML_ROOT, '/'));
            \think\Cache::clear();
            $this->success('操作成功', url('Seo/seo'));
        }
        $this->error('操作失败');
    }

    /**
     * 生成静态页面代码 - 更新分页php文件支持生成静态功能
     */
    private function update_paginatorfile()
    {
        $dirpath = CORE_PATH . 'paginator/driver/*.php';
        $files = glob($dirpath);
        foreach ($files as $key => $file) {
            if (is_writable($file)) {
                $strContent = @file_get_contents($file);
                if (false != $strContent && !stristr($strContent, 'data-ey_fc35fdc="html"')) {
                    $replace = 'htmlentities($url) . \'" data-ey_fc35fdc="html" data-tmp="1\'';
                    $strContent = str_replace('htmlentities($url)', $replace, $strContent);
                    @chmod($file,0777);
                    @file_put_contents($file, $strContent);
                }
            }
        }
    }

    /*
     * 生成整站静态文件
     */
    public function buildSite(){
        $type =  input("param.type/s");
        if($type != 'site'){
            $this->error('操作失败');
        }
        $this->success('操作成功');
    }
    
    /*
     * 获取生成栏目或文章的栏目id
     */
    public function getAllType(){
        $id =  input("param.id/d");//栏目id
        $type =  input("param.type/d");//1栏目2文章
        if(empty($id)) {
            if($id == 0){
                $mark = Db::name('language')->order('id asc')->value('mark'); 
                if($type == 1){
                    $arctype = Db::name('arctype')->where(['is_del'=>0,'status'=>1,'lang'=>$mark])->getfield('id',true);
                }else{
                    $where['is_del'] = 0;
                    $where['status'] = 1;
                    $where['lang'] = $mark;
                    $where['current_channel'] = array(array('neq',6),array('neq',8));
                    $arctype = Db::name('arctype')->where($where)->getfield('id',true);                   
                }
                if(empty($arctype)){
                    $this->error('没有要更新的栏目！');
                }else{
                    $arctype = implode(',',$arctype);
                    $this->success($arctype);
                }
            }else{
                $this->error('栏目ID不能为空！');
            }
        }else{
            //递归查询所有的子类
            $arctype_child_all = array($id);
            getAllChild($arctype_child_all,$id,$type);

            $arctype_child_all = implode(',',$arctype_child_all);
            if(empty($arctype_child_all)) {
                $this->error('没有要更新的栏目！');
            }else{
                $this->success($arctype_child_all);
            }
        }
    }

    /**
     * 纠正栏目的HTML目录路径字段值
     */
    private function correctArctypeDirpath()
    {
        $system_correctArctypeDirpath = tpCache('system.system_correctArctypeDirpath');
        if (!empty($system_correctArctypeDirpath)) {
            return false;
        }

        $saveData = [];
        $arctypeList = Db::name('arctype')->field('id,parent_id,dirname,dirpath,grade')
            ->order('grade asc')
            ->getAllWithIndex('id');
        foreach ($arctypeList as $key => $val) {
            if (empty($val['parent_id'])) { // 一级栏目
                $saveData[] = [
                    'id'            => $val['id'],
                    'dirpath'       => '/'.$val['dirname'],
                    'grade'         => 0,
                    'update_time'   => getTime(),
                ];
            } else {
                $parentRow = $arctypeList[$val['parent_id']];
                if (empty($parentRow['parent_id'])) { // 二级栏目
                    $saveData[] = [
                        'id'            => $val['id'],
                        'dirpath'       => '/'.$parentRow['dirname'].'/'.$val['dirname'],
                        'grade'         => 1,
                        'update_time'   => getTime(),
                    ];
                } else { // 三级栏目
                    $topRow = $arctypeList[$parentRow['parent_id']];
                    $saveData[] = [
                        'id'            => $val['id'],
                        'dirpath'       => '/'.$topRow['dirname'].'/'.$parentRow['dirname'].'/'.$val['dirname'],
                        'grade'         => 2,
                        'update_time'   => getTime(),
                    ];
                }
            }
        }
        $r = model('Arctype')->saveAll($saveData);
        if (false !== $r) {
            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache('system', ['system_correctArctypeDirpath'=>1],$val['mark']);
                }
            } else {
                tpCache('system',['system_correctArctypeDirpath'=>1]);
            }
            /*--end*/
        }
    }

    /**
     * 静态页面模式切换为其他模式时，检测之前生成的静态目录是否存在，并提示手工删除还是自动删除
     */
    public function ajax_checkHtmlDirpath()
    {
        $seo_pseudo_new = input('param.seo_pseudo_new/d');
        if (3 == $seo_pseudo_new) {
            $dirArr = [];
            $seo_html_listname = tpCache('seo.seo_html_listname');
            $row = Db::name('arctype')->field('dirpath')->select();
            foreach ($row as $key => $val) {
                $dirpathArr = explode('/', $val['dirpath']);
                if (3 == $seo_html_listname) {
                    $dir = end($dirpathArr);
                } else {
                    $dir = !empty($dirpathArr[1]) ? $dirpathArr[1] : '';
                }
                if (!empty($dir) && !in_array($dir, $dirArr)) {
                    array_push($dirArr, $dir);
                }
            }

            $data = [];
            $data['msg'] = '';
            $num = 0;
            $wwwroot = glob('*', GLOB_ONLYDIR);
            foreach ($wwwroot as $key => $val) {
                if (in_array($val, $dirArr)) {
                    if (0 == $num) {
                        $data['msg'] .= "<font color='red'>根目录下有HTML静态目录，请先删除：</font><br/>";
                    }
                    $data['msg'] .= ($num+1)."、{$val}<br/>";
                    $num++;
                }
            }
            $data['height'] = $num * 24;

            $this->success('检测成功！', null, $data);
        }
    }

    /**
     * 自动删除静态HTML存放目录
     */
    public function ajax_delHtmlDirpath()
    {
        if (IS_AJAX_POST) {
            $error = false;
            $dirArr = [];
            $seo_html_listname = tpCache('seo.seo_html_listname');
            $row = Db::name('arctype')->field('dirpath')->select();
            foreach ($row as $key => $val) {
                $dirpathArr = explode('/', $val['dirpath']);
                if (3 == $seo_html_listname) {
                    $dir = end($dirpathArr);
                } else {
                    $dir = !empty($dirpathArr[1]) ? $dirpathArr[1] : '';
                }
                $filepath = "./{$dir}";
                if (!empty($dir) && !in_array($dir, $dirArr) && file_exists($filepath)) {
                    @unlink($filepath."/index.html");
                    $bool = delFile($filepath, true);
                    if (false !== $bool) {
                        array_push($dirArr, $dir);
                    } else {
                        $error = true;
                    }
                }
            }

            $data = [];
            $data['msg'] = '';
            if ($error) {
                $num = 0;
                $wwwroot = glob('*', GLOB_ONLYDIR);
                foreach ($wwwroot as $key => $val) {
                    if (in_array($val, $dirArr)) {
                        if (0 == $num) {
                            $data['msg'] .= "<font color='red'>部分目录删除失败，请手工删除：</font><br/>";
                        }
                        $data['msg'] .= ($num+1)."、{$val}<br/>";
                        $num++;
                    }
                }
                $data['height'] = $num * 24;
                $this->error('删除失败！', null, $data);
            }

            $this->success('删除成功！', null, $data);
        }
    }

    /**
     * 生成完页面之后，清除缓存
     */
    private function buildhtml_clear_cache()
    {
        // 文档参数缓存
        cache("article_info_serialize",null);
        cache("article_page_total_serialize",null);
        cache("article_content_serialize",null);
        cache("article_tags_serialize",null);
        cache("article_attr_info_serialize",null);
        cache("article_children_row_serialize",null);
        // 栏目参数缓存
        cache("channel_page_total_serialize",null);
        cache("channel_info_serialize",null);
        cache("has_children_Row_serialize",null);
    }
}