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
use think\template\driver\File;

class Sitemap extends Base
{
    public function _initialize() {
        parent::_initialize();
    }

    /*
     * Sitemap
     */
    public function index()
    {
        $inc_type =  'sitemap';
        if (IS_POST) {
            $param = input('post.');
            $param['sitemap_not1'] = isset($param['sitemap_not1']) ? $param['sitemap_not1'] : 0;
            $param['sitemap_not2'] = isset($param['sitemap_not2']) ? $param['sitemap_not2'] : 0;
            $param['sitemap_xml'] = isset($param['sitemap_xml']) ? $param['sitemap_xml'] : 0;
            $param['sitemap_html'] = isset($param['sitemap_html']) ? $param['sitemap_html'] : 0;
            $param['sitemap_txt'] = isset($param['sitemap_txt']) ? $param['sitemap_txt'] : 0;
            $param['sitemap_archives_num'] = isset($param['sitemap_archives_num']) ? intval($param['sitemap_archives_num']) : 100;
            $sitemap_lang = isset($param['sitemap_lang']) ? $param['sitemap_lang'] : [$this->admin_lang];
            $param['sitemap_lang'] = json_encode($sitemap_lang);
            $param['ai_content_index_html'] = isset($param['ai_content_index_html']) ? $param['ai_content_index_html'] : 0;
            $param['llms_txt'] = $this->php_servicemeal >= 5 && isset($param['llms_txt']) ? $param['llms_txt'] : 0;
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
            $langs = $param['sitemap_lang'];
            $is_home_lang = get_current_lang();
            $langs = !empty($langs) ? json_decode($langs, true) : [];
            if (!empty($is_home_lang) && !in_array($is_home_lang, $langs)) {
                array_push($langs, $is_home_lang);
            }
            sitemap_all('all', $langs);
            $this->success('操作成功', url('Sitemap/index'), $sitemap_lang);
        }
        $config = tpCache($inc_type);
        $config['sitemap_lang'] = !empty($config['sitemap_lang']) ? json_decode($config['sitemap_lang'], true) : [];
        $this->assign('config',$config);//当前配置项
        if($this->globalConfig['web_mobile_domain_open']){
            $mobile_domain = preg_replace('/^(.*)(\/\/)([^\/]*)(\.?)(' . request()->rootDomain() . ')(.*)$/i', '${1}${2}' . $this->globalConfig['web_mobile_domain'] . '.${5}${6}', request()->domain());
            $this->assign('mobile_domain',$mobile_domain);
        }
        $web_basehost = preg_replace('/^(([^\:\.]+):)?(\/\/)?([^\/\:]*)(.*)$/i', '${1}${3}${4}', $this->globalConfig['web_basehost']);
        $this->assign('web_basehost',$web_basehost);

        $langlist = Db::name('language')->field('id,title,is_home_default,mark')->where(['status'=>1])->order('id asc')->cache(true, EYOUCMS_CACHE_TIME, 'language')->select();
        $is_home_lang = get_current_lang();
        $this->assign('is_home_lang',$is_home_lang);
        $this->assign('langlist',$langlist);

        return $this->fetch('seo/sitemap');
    }

    /**
     * 生成相应的搜索引擎sitemap
     */
    public function create($ver = 'xml')
    {
        if (empty($ver)) {
            sitemap_all();
        } else {
            $fun_name = 'sitemap_'.$ver;
            $fun_name();
        }
    }
}
