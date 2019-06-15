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

class Seo extends Base
{

    public function _initialize() {
        parent::_initialize();
        $this->language_access(); // 多语言功能操作权限
    }
    
    /*
     * 配置入口
     */
    public function index()
    {
        $inc_type =  input('get.inc_type','seo');
        $this->assign('inc_type',$inc_type);
        $config = tpCache($inc_type);
        $config['seo_pseudo'] = tpCache('seo.seo_pseudo');
        if('seo' == $inc_type){
            $seo_pseudo_list = get_seo_pseudo_list();
            $this->assign('seo_pseudo_list', $seo_pseudo_list);
        } elseif ('html' == $inc_type) {
            // 栏目列表
            $arctypeLogic = new ArctypeLogic();
            $select_html = $arctypeLogic->arctype_list(0, 0, true, config('global.arctype_max_level'));
            $this->assign('select_html',$select_html);
        } else if ('rewrite' == $inc_type) {
            $this->assign('root_dir', ROOT_DIR); // 支持子目录
        }
        $this->assign('config',$config);//当前配置项
        return $this->fetch($inc_type);
    }
    
    /*
     * 新增修改配置（同步数据到其他语言里）
     */
    public function handle()
    {
        $param = input('post.');
        $inc_type = $param['inc_type'];
        if ($inc_type == 'seo') {
            /*检测是否开启pathinfo模式*/
            try {
                if (3 == $param['seo_pseudo'] || (1 == $param['seo_pseudo'] && 2 == $param['seo_dynamic_format'])) {
                    $fix_pathinfo = ini_get('cgi.fix_pathinfo');
                    if (stristr($_SERVER['HTTP_HOST'], '.mylightsite.com')) {
                        $this->error('腾讯云空间不支持伪静态！');
                    } else if ('' != $fix_pathinfo && 0 === $fix_pathinfo) {
                        $this->error('空间不支持伪静态，请开启pathinfo，或者在php.ini里修改cgi.fix_pathinfo=1');
                    }
                }
            } catch (\Exception $e) {}
            /*--end*/
            /*强制去除index.php*/
            if (isset($param['seo_force_inlet'])) {
                $seo_force_inlet = $param['seo_force_inlet'];
                $seo_force_inlet_old = tpCache('seo.seo_force_inlet');
                if ($seo_force_inlet_old != $seo_force_inlet) {
                    $param['seo_inlet'] = $seo_force_inlet;
                }
            }
            /*--end*/
        } else if($inc_type == 'sitemap'){
            $param['sitemap_not1'] = isset($param['sitemap_not1']) ? $param['sitemap_not1'] : 0;
            $param['sitemap_not2'] = isset($param['sitemap_not2']) ? $param['sitemap_not2'] : 0;
            $param['sitemap_xml'] = isset($param['sitemap_xml']) ? $param['sitemap_xml'] : 0;
            $param['sitemap_txt'] = isset($param['sitemap_txt']) ? $param['sitemap_txt'] : 0;
        }
        unset($param['inc_type']);
        /*多语言*/
        if (is_language()) {
            $langRow = \think\Db::name('language')->order('id asc')
                ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                ->select();
            foreach ($langRow as $key => $val) {
                tpCache($inc_type,$param,$val['mark']);
            }
        } else {
            tpCache($inc_type,$param);
        }
        /*--end*/
        
        if ($inc_type == 'seo') {
            // 清空缓存
            delFile(rtrim(HTML_ROOT, '/'));
            \think\Cache::clear();
        } else if($inc_type == 'sitemap'){
            /* 生成sitemap */
            sitemap_all();
        }
        $this->success('操作成功', url('Seo/index',array('inc_type'=>$inc_type)));
    }
    
    /*
     * 生成静态页面
     */
    public function htmlHandle()
    {
        $param = input('param.');
        $inc_type = $param['inc_type'];
        $typeid = isset($param['typeid']) ? $param['typeid'] : 0;
        $html_startid = isset($param['html_startid']) ? $param['html_startid'] : 0;
        $html_endid = isset($param['html_endid']) ? $param['html_endid'] : 0;

        $this->bindArchivesHtml($typeid, $html_startid, $html_endid);

        $this->success('操作成功', url('Seo/index',array('inc_type'=>$inc_type)));
    }

    /*
     * 生成静态页面
     */
    public function bindHtml()
    {
        $param = input('param.');
        // $inc_type = $param['inc_type'];
        $typeid = isset($param['typeid']) ? $param['typeid'] : 0;
        $html_startid = isset($param['html_startid']) ? $param['html_startid'] : 0;
        $html_endid = isset($param['html_endid']) ? $param['html_endid'] : 0;
        $updatetype = isset($param['updatetype']) ? $param['updatetype'] : 'index';

        if ($updatetype == 'index') {
            $res = $this->bindIndexHtml();
        } elseif ($updatetype == 'archives') {
            $res = $this->bindArchivesHtml($typeid, $html_startid, $html_endid);
        } elseif ($updatetype == 'arctype') {
            $res = $this->bindArctypeHtml($typeid);
        } elseif ($updatetype == 'all') {
            $res1 = $this->bindArctypeHtml($typeid);

            $res = $this->bindArchivesHtml($typeid, $html_startid, $html_endid);
        }

        respose(array(
            'urls' => $res['urls'],
            'nowurls' => $res['nowurls'],
            'total'=> count($res['urls']),
        ));
    }

    /**
     * 更新首页html
     */
    public function bindIndexHtml()
    {
        if (config('is_https')) {
            $filename = 'indexs.html';
        } else {
            $filename = 'index.html';
        }
        $filename = ROOT_PATH.$filename;
        if (file_exists($filename)) {
            @unlink($filename);
        }

        return array(
            'urls' => array(request()->domain()),
            'nowurls' => array(request()->domain()),
        );
    }

    /**
     * 更新文档html
     */
    public function bindArchivesHtml($typeid = '', $startid = '', $endid = '')
    {
        $channelList = model('Channeltype')->getAll('*', array(), 'id');
        $arctypeList = model('Arctype')->getAll('*', array(), 'id');

        if ($typeid > 0) {
            $result = model('Arctype')->getHasChildren($typeid);
            $map['typeid'] = array('in', get_arr_column($result, 'id'));
        }
        if ($startid > 0) {
            $map['aid'] = array('egt', $startid);
        }
        if ($endid > 0) {
            $map['aid'] = array('elt', $endid);
        }
        // if (intval($pagesize) == 0) {
            $pagesize = '';
        // }
        $map['is_jump'] = array('eq', 0);
        $map['channel'] = array('neq', 6);
        $map['status'] = array('eq', 1);

        $url_arr = array();
        $nowurl_arr = array();
        $result = M('archives')->where($map)->limit($pagesize)->select();
        foreach ($result as $key => $val) {
            $val = array_merge($arctypeList[$val['typeid']], $val);

            $ctl_name = $channelList[$val['channel']]['ctl_name'];
            $nowarcurl = arcurl('home/'.$ctl_name.'/view', $val, true, request()->domain(), 2);
            $arcurl = arcurl('home/'.$ctl_name.'/view', $val, true, request()->domain(), 1);

            array_push($url_arr, $arcurl);
            array_push($nowurl_arr, $nowarcurl);
        }

        return array(
            'urls' => $url_arr,
            'nowurls' => $nowurl_arr,
        );
    }

    /**
     * 更新栏目html
     */
    public function bindArctypeHtml($typeid = '')
    {
        $channelList = model('Channeltype')->getAll('*', array(), 'id');

        if (empty($typeid)) {
            $typeid = 0;
        }

        $url_arr = array();
        $nowurl_arr = array();
        $result = model('Arctype')->getHasChildren($typeid);

        $module_name_tmp = 'home';
        $action_name_tmp = 'lists';
        foreach ($result as $key => $val) {
            $ctl_name = $channelList[$val['current_channel']]['ctl_name'];
            $cacheKey = strtolower("taglist_lastPage_home{$ctl_name}lists".$val['id']);
            $lastPage = cache($cacheKey); // 用于静态页面的分页生成
            for ($i=1; $i <= $lastPage; $i++) { 
                $nowtypeurl = typeurl('home/'.$ctl_name.'/lists', $val, true, request()->domain(), 2);
                if ($i == 1) {
                    $nowtypeurl .= 'index.html';
                } else {
                    $nowtypeurl .= 'list_'.$val['id'].'_'.$i.'.html';
                }
                $typeurl = typeurl('home/'.$ctl_name.'/lists', $val, true, request()->domain(), 1).'&page='.$i;

                array_push($url_arr, $typeurl);
                array_push($nowurl_arr, $nowtypeurl); 
            }
        }

        return array(
            'urls' => $url_arr,
            'nowurls' => $nowurl_arr,
        );
    }
}