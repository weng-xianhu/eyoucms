<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海口快推科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */

namespace app\home\controller;

class Tags extends Base
{
    public function _initialize() {
        parent::_initialize();
    }

    /**
     * 标签主页
     */
    public function index()
    {
        $result['pageurl'] = tagurl('home/Tags/index'); // 获取当前页面URL
        $result['pageurl_m'] = pc_to_mobile_url($result['pageurl']); // 获取当前页面对应的移动端URL
        // 移动端域名
        $result['mobile_domain'] = '';
        if (!empty($this->eyou['global']['web_mobile_domain_open']) && !empty($this->eyou['global']['web_mobile_domain'])) {
            $result['mobile_domain'] = $this->eyou['global']['web_mobile_domain'] . '.' . $this->request->rootDomain(); 
        }
        $result['seo_title'] = !empty($this->eyou['global']['tag_seo_title']) ? $this->eyou['global']['tag_seo_title'] : '标签页_'.$this->eyou['global']['web_name'];
        $result['seo_keywords'] = !empty($this->eyou['global']['tag_seo_keywords']) ? $this->eyou['global']['tag_seo_keywords'] : '';
        $result['seo_description'] = !empty($this->eyou['global']['tag_seo_description']) ? $this->eyou['global']['tag_seo_description'] : '';
        $eyou = array(
            'field' => $result,
        );
        $this->eyou = array_merge($this->eyou, $eyou);
        $this->assign('eyou', $this->eyou);
        
        /*模板文件*/
        $viewfile = 'index_tags';
        /*--end*/

        if (config('city_switch_on') && !empty($this->home_site)) { // 多站点内置模板文件名
            $viewfilepath = TEMPLATE_PATH.$this->theme_style_path.DS.$this->home_site;
            $viewfilepath2 = TEMPLATE_PATH.$this->theme_style_path.DS.'city'.DS.$this->home_site;
            if (!empty($this->eyou['global']['site_template'])) {
                if (file_exists($viewfilepath2)) {
                    $viewfile = "city/{$this->home_site}/{$viewfile}";
                } else if (file_exists($viewfilepath)) {
                    $viewfile = "{$this->home_site}/{$viewfile}";
                }
            }
        } else if (config('lang_switch_on') && !empty($this->home_lang)) { // 多语言内置模板文件名
            $viewfilepath = TEMPLATE_PATH.$this->theme_style_path.DS.$viewfile."_{$this->home_lang}.".$this->view_suffix;
            if (file_exists($viewfilepath)) {
                $viewfile .= "_{$this->home_lang}";
            }
        }

        return $this->fetch(":{$viewfile}");
    }

    /**
     * 标签列表
     */
    public function lists()
    {
        $param = I('param.');
        
        $tagid = isset($param['tagid']) ? $param['tagid'] : '';
        $tag = isset($param['tag']) ? trim($param['tag']) : '';
        if (!empty($tag)) {
            $tag = addslashes($tag);
            $tagindexInfo = M('tagindex')->where([
                    'tag'   => $tag,
                    'lang'  => $this->home_lang,
                ])->find();
        } elseif (intval($tagid) > 0) {
            if (!is_numeric($tagid)) abort(404);
            $tagindexInfo = M('tagindex')->where([
                    'id'   => intval($tagid),
                    'lang'  => $this->home_lang,
                ])->find();
        }

        if (!empty($tagindexInfo)) {
            $tagid = $tagindexInfo['id'];
            $tag = $tagindexInfo['tag'];
            $row = M('taglist')->where([
                    'tid'   => $tagid,
                ])->order('arcrank desc')->find();
            if (!empty($row) && -1 >= intval($row['arcrank'])) {
                $this->error('标签的文档待审核');
            }
            //更新浏览量和记录数
            $map = array(
                'tid'   => array('eq', $tagid),
                'arcrank'   => array('gt', -1),
                'lang'  => $this->home_lang,
            );
            $total = M('taglist')->where($map)
                ->count('tid');
            M('tagindex')->where([
                    'id'    => $tagid,
                    'lang'  => $this->home_lang,
                ])->inc('count')
                ->inc('weekcc')
                ->inc('monthcc')
                ->update(array('total'=>$total));

            $ntime = getTime();
            $oneday = 24 * 3600;

            //周统计
            if(ceil( ($ntime - $tagindexInfo['weekup'])/$oneday ) > 7)
            {
                M('tagindex')->where([
                        'id'    => $tagid,
                        'lang'  => $this->home_lang,
                    ])->update(array('weekcc'=>0, 'weekup'=>$ntime));
            }

            //月统计
            if(ceil( ($ntime - $tagindexInfo['monthup'])/$oneday ) > 30)
            {
                M('tagindex')->where([
                        'id'    => $tagid,
                        'lang'  => $this->home_lang,
                    ])->update(array('monthcc'=>0, 'monthup'=>$ntime));
            }
        } else {
            abort(404);
        }

        $field_data = array(
            'tag'   => $tag,
            'tagid'   => $tagid,
            'litpic'   => !empty($tagindexInfo['litpic']) ? handle_subdir_pic($tagindexInfo['litpic']) : $tagindexInfo['litpic'],
            'seo_title'   => set_tagseotitle($tag, $tagindexInfo['seo_title']),
            'seo_keywords'   => !empty($tagindexInfo['seo_keywords']) ? $tagindexInfo['seo_keywords'] : $tagindexInfo['seo_keywords'],
            'seo_description'   => !empty($tagindexInfo['seo_description']) ? $tagindexInfo['seo_description'] : $tagindexInfo['seo_description'],
            'pageurl' => tagurl('home/Tags/lists', array('tagid'=>$tagid)),
        );
        $eyou = array(
            'field'  => $field_data,
        );
        $this->eyou = array_merge($this->eyou, $eyou);
        $this->assign('eyou', $this->eyou);

        /*模板文件*/
        $viewfile = 'lists_tags';
        /*--end*/

        if (config('city_switch_on') && !empty($this->home_site)) { // 多站点内置模板文件名
            $viewfilepath = TEMPLATE_PATH.$this->theme_style_path.DS.$this->home_site;
            $viewfilepath2 = TEMPLATE_PATH.$this->theme_style_path.DS.'city'.DS.$this->home_site;
            if (!empty($this->eyou['global']['site_template'])) {
                if (file_exists($viewfilepath2)) {
                    $viewfile = "city/{$this->home_site}/{$viewfile}";
                } else if (file_exists($viewfilepath)) {
                    $viewfile = "{$this->home_site}/{$viewfile}";
                }
            }
        } else if (config('lang_switch_on') && !empty($this->home_lang)) { // 多语言内置模板文件名
            $viewfilepath = TEMPLATE_PATH.$this->theme_style_path.DS.$viewfile."_{$this->home_lang}.".$this->view_suffix;
            if (file_exists($viewfilepath)) {
                $viewfile .= "_{$this->home_lang}";
            }
        }

        return $this->fetch(":{$viewfile}");
    }
}