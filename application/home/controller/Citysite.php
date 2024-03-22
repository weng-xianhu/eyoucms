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

namespace app\home\controller;

class Citysite extends Base
{
    public function _initialize() {
        parent::_initialize();
    }

    /**
     * 多城市站点主页
     */
    public function index()
    {
        if (!config('city_switch_on') || !empty($this->home_site)) {
            abort(404); 
        }
        $result['pageurl'] = $this->request->url(true); // 获取当前页面URL
        $result['pageurl_m'] = pc_to_mobile_url($result['pageurl']); // 获取当前页面对应的移动端URL
        // 移动端域名
        $result['mobile_domain'] = '';
        if (!empty($this->eyou['global']['web_mobile_domain_open']) && !empty($this->eyou['global']['web_mobile_domain'])) {
            $result['mobile_domain'] = $this->eyou['global']['web_mobile_domain'] . '.' . $this->request->rootDomain(); 
        }
        $result['seo_title'] = !empty($this->eyou['global']['citysite_seo_title']) ? $this->eyou['global']['citysite_seo_title'] : '多城市分站_'.$this->eyou['global']['web_name'];
        $result['seo_keywords'] = !empty($this->eyou['global']['citysite_seo_keywords']) ? $this->eyou['global']['citysite_seo_keywords'] : '';
        $result['seo_description'] = !empty($this->eyou['global']['citysite_seo_description']) ? $this->eyou['global']['citysite_seo_description'] : '';
        $eyou = array(
            'field' => $result,
        );
        $this->eyou = array_merge($this->eyou, $eyou);
        $this->assign('eyou', $this->eyou);
        
        /*模板文件*/
        $viewfile = 'index_citysite';
        /*--end*/

        if (!empty($this->home_site)) { // 多站点内置模板文件名
            $viewfilepath = TEMPLATE_PATH.$this->theme_style_path.DS.$this->home_site;
            $viewfilepath2 = TEMPLATE_PATH.$this->theme_style_path.DS.'city'.DS.$this->home_site;
            if (!empty($this->eyou['global']['site_template'])) {
                if (file_exists($viewfilepath2)) {
                    $viewfile = "city/{$this->home_site}/{$viewfile}";
                } else if (file_exists($viewfilepath)) {
                    $viewfile = "{$this->home_site}/{$viewfile}";
                }
            }
        }

        return $this->fetch(":{$viewfile}");
    }
}