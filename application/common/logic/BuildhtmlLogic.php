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
namespace app\common\logic;

use think\Model;
use think\Db;

/**
 * 逻辑定义
 * @package common\Logic
 */
load_trait('controller/Jump');
class BuildhtmlLogic extends Model
{
    use \traits\controller\Jump;
    
    /**
     * 初始化操作
     */
    public function _initialize() {
        parent::_initialize();
    }

    /*
     * 保存URL配置
     */
    public function seo_handle($post = [])
    {
        $param = $post;
        $inc_type = 'seo';
        $globalConfig = tpCache('global');
        $seo_pseudo_new = $param['seo_pseudo'];

        /*伪静态格式*/
        if (3 == $seo_pseudo_new && in_array($param['seo_rewrite_format'], [1,3,4])) {
            $param['seo_rewrite_format'] = !empty($param['seo_rewrite_view_format']) ? $param['seo_rewrite_view_format'] : 1;
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
                if (stristr(request()->host(), '.mylightsite.com')) {
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

        // 用于静态模式，发布编辑文档后更新
        $param['seo_uphtml_after_home'] = !empty($param['seo_uphtml_after_home']) ? $param['seo_uphtml_after_home'] : 0;
        $param['seo_uphtml_after_channel'] = !empty($param['seo_uphtml_after_channel']) ? $param['seo_uphtml_after_channel'] : 0;
        $param['seo_uphtml_after_pernext'] = !empty($param['seo_uphtml_after_pernext']) ? $param['seo_uphtml_after_pernext'] : 0;
        $param['seo_uphtml_editafter_home'] = !empty($param['seo_uphtml_editafter_home']) ? $param['seo_uphtml_editafter_home'] : 0;
        $param['seo_uphtml_editafter_channel'] = !empty($param['seo_uphtml_editafter_channel']) ? $param['seo_uphtml_editafter_channel'] : 0;
        $param['seo_uphtml_editafter_pernext'] = !empty($param['seo_uphtml_editafter_pernext']) ? $param['seo_uphtml_editafter_pernext'] : 0;

        // 用于动态、伪静态模式，在运营模式下 - 发布编辑文档后清除缓存
        $param['seo_uphtml_after_home13'] = !empty($param['seo_uphtml_after_home13']) ? $param['seo_uphtml_after_home13'] : 0;
        $param['seo_uphtml_after_channel13'] = !empty($param['seo_uphtml_after_channel13']) ? $param['seo_uphtml_after_channel13'] : 0;
        $param['seo_uphtml_after_pernext13'] = !empty($param['seo_uphtml_after_pernext13']) ? $param['seo_uphtml_after_pernext13'] : 0;

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
    }

    /**
     * 生成静态专用 - 获取全部栏目的数据
     */
    public function get_arctype_all($field = '*')
    {
        $cacheKey = md5('common_buildhtmllogic_get_arctype_all_'.$field);
        $result = cache($cacheKey);
        if (empty($result)) {
            $result = Db::name('arctype')->field($field)->getAllWithIndex('id');
            cache($cacheKey, $result, null, 'arctype');
        }

        return !empty($result) ? $result : [];
    }

    /**
     * 生成静态专用 - 获取全部文档对应的栏目id
     */
    public function get_archives_all()
    {
        $empty_num = 0;
        $pagesize = 15000;
        for ($i=0; $i < 1000; $i++) {
            $result = [];
            $start = $i * $pagesize;
            $end = ($i + 1) * $pagesize;
            $field = 'aid,typeid';
            $row = Db::name('archives')->where([
                    'aid' => ['BETWEEN', [$start + 1, $end]],
                ])->field($field)->select();
            if (empty($row)) {
                if ($empty_num < 2) {
                    $empty_num++;
                    continue;
                } else {
                    break;
                }
            }

            foreach ($row as $key => $val) {
                $result[$val['aid']] = $val['typeid'];
            }

            $cacheKey = "table_archives_{$start}_{$end}";
            cache($cacheKey, $result, null, 'archives');
        }
    }

    /**
     * 删除文档对应的html页面文件
     * @param  array  $aids [description]
     * @return [type]       [description]
     */
    public function delViewHtml($aids = [], $globalConfig = [])
    {
        if (empty($globalConfig)) {
            $globalConfig = tpCache('global');
        }
        if (2 == $globalConfig['seo_pseudo']) {
            $info = Db::name('archives')->field('b.topid,b.dirpath,b.diy_dirpath,b.rulelist,b.ruleview,a.*')
                ->alias('a')
                ->join('__ARCTYPE__ b', 'a.typeid = b.id', 'LEFT')
                ->where([
                    'a.aid'  => ['in', $aids],
                ])
                ->select();

            $seo_html_pagename = $globalConfig['seo_html_pagename'];
            foreach ($info as $key => $row) {
                $filename = $row['aid'];
                if (!empty($row['htmlfilename'])) {
                    $filename = $row['htmlfilename'];
                }
                $dir      = $this->getArticleDir($row, $globalConfig);
                if (4 == $seo_html_pagename) {
                    if (!empty($row['ruleview'])) {
                        $path = $dir;
                    }else{
                        $path     = $dir . "/" . $filename . ".html";
                    }
                } else {
                    $path     = $dir . "/" . $filename . ".html";
                }

                if (file_exists($path)) @unlink($path);
            }
        }
    }

    public function getArticleDir($row = [], $globalConfig = [])
    {
        if (empty($globalConfig)) {
            $globalConfig = tpCache('global');
        }
        $dir               = "";
        $seo_html_pagename = $globalConfig['seo_html_pagename'];
        $seo_html_arcdir   = $globalConfig['seo_html_arcdir'];
        $dirpath = !empty($row['dirpath']) ? $row['dirpath'] : '';
        $aid = !empty($row['htmlfilename']) ? $row['htmlfilename'] : $row['aid'];
        if ($seo_html_pagename == 1) {//存放顶级目录
            $dirpath_arr = explode('/', $dirpath);
            if (count($dirpath_arr) > 2) {
                $dir = '.' . $seo_html_arcdir . '/' . $dirpath_arr[1];
            } else {
                $dir = '.' . $seo_html_arcdir . $dirpath;
            }
        } else if ($seo_html_pagename == 3) { //存放子级目录
            $dirpath_arr = explode('/', $dirpath);
            if (count($dirpath_arr) > 2) {
                $dir = '.' . $seo_html_arcdir . '/' . end($dirpath_arr);
            } else {
                $dir = '.' . $seo_html_arcdir . $dirpath;
            }
        } else if ($seo_html_pagename == 4) { //自定义存放目录
            $dir = '.' . $seo_html_arcdir;
            $diy_dirpath = !empty($row['diy_dirpath']) ? $row['diy_dirpath'] : '';
            if (!empty($row['ruleview'])) {
                $y = $m = $d = 1;
                if (!empty($row['add_time'])) {
                    $y = date('Y', $row['add_time']);
                    $m = date('m', $row['add_time']);
                    $d = date('d', $row['add_time']);
                }
                $ruleview = ltrim($row['ruleview'], '/');
                $ruleview = str_ireplace("{aid}", $aid, $ruleview);
                $ruleview = str_ireplace("{Y}", $y, $ruleview);
                $ruleview = str_ireplace("{M}", $m, $ruleview);
                $ruleview = str_ireplace("{D}", $d, $ruleview);
                $ruleview = preg_replace('/{(栏目目录|typedir)}(\/?)/i', $diy_dirpath.'/', $ruleview);
                $ruleview = '/'.ltrim($ruleview, '/');
                $dir .= $ruleview;
            }else{
                $dir .= $diy_dirpath;
            }
        } else { //存放父级目录
            $dir = '.' . $seo_html_arcdir . $dirpath;
        }

        return $dir;
    }
}
