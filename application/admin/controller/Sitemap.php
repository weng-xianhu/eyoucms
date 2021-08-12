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
            $param['sitemap_txt'] = isset($param['sitemap_txt']) ? $param['sitemap_txt'] : 0;
            $param['sitemap_archives_num'] = isset($param['sitemap_archives_num']) ? intval($param['sitemap_archives_num']) : 100;

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
            
            /* 生成sitemap */
            sitemap_all();
            $this->success('操作成功', url('Sitemap/index'));
        }

        $config = tpCache($inc_type);
        $this->assign('config',$config);//当前配置项
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

    public function ajax_update_sitemap()
    {
        if (IS_POST) {
            /* 生成sitemap */
            sitemap_all();
            $this->success('更新成功');
        }
        $this->error('更新失败！');
    }
}
