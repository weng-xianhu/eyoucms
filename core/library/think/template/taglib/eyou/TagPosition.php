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

namespace think\template\taglib\eyou;

use think\Db;

/**
 * 栏目位置
 */
class TagPosition extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        /*应用于文档列表*/
        if ($this->aid > 0) {
            $this->tid = $this->get_aid_typeid($this->aid);
        }
        /*--end*/
    }

    /**
     * 获取面包屑位置
     * @author wengxianhu by 2018-4-20
     */
    public function getPosition($typeid = '', $symbol = '', $style = 'crumb')
    {
        $typeid = !empty($typeid) ? $typeid : $this->tid;

        /*多语言*/
        if (!empty($typeid)) {
            $typeid = model('LanguageAttr')->getBindValue($typeid, 'arctype');
        }
        /*--end*/

        $basicConfig = tpCache('basic');
        $basic_indexname = !empty($basicConfig['basic_indexname']) ? $basicConfig['basic_indexname'] : '首页';
        $symbol = !empty($symbol) ? $symbol : $basicConfig['list_symbol'];

        /*首页链接*/
        $inletStr = '/index.php';
        $seo_inlet = config('ey_config.seo_inlet');
        1 == intval($seo_inlet) && $inletStr = '';

        if (self::$city_switch_on) { // 多城市站点
            if (empty(self::$home_site)) {
                $home_url = $this->root_dir.'/'; // 支持子目录
            } else {
                $seoConfig = tpCache('seo');
                $seo_pseudo = !empty($seoConfig['seo_pseudo']) ? $seoConfig['seo_pseudo'] : config('ey_config.seo_pseudo');
                if (1 == $seo_pseudo) {
                    $home_url = self::$request->domain().$this->root_dir; // 支持子目录
                    if (!self::$site_info['is_open']) {
                        $home_url .= $inletStr; // 支持子目录
                        if (!empty($inletStr)) {
                            $home_url .= '?';
                        } else {
                            $home_url .= '/?';
                        }
                        $home_url .= http_build_query(['site'=>self::$home_site]);
                    }
                } else if (2 == $seo_pseudo) { // 生成静态页面代码
                    $site_default_home = config('ey_config.site_default_home');
                    if (!empty($site_default_home) && self::$siteid == $site_default_home) {
                        $home_url = $this->root_dir.'/';
                    } else {
                        $home_url = $this->root_dir.'/'; // 支持子目录
                        if (!self::$site_info['is_open']) {
                            $home_url .= self::$home_site.'/';
                        }
                    }
                } else {
                    $home_url = $this->root_dir.$inletStr.'/'; // 支持子目录
                    if (!self::$site_info['is_open']) {
                        $home_url .= self::$home_site.'/';
                    }
                }
            }
        }
        else { // 多语言
            $lang = input('param.lang/s', '');
            if (empty($lang)) {
                $home_url = $this->root_dir.'/'; // 支持子目录
            } else {
                $seoConfig = tpCache('seo', [], $lang);
                $seo_pseudo = !empty($seoConfig['seo_pseudo']) ? $seoConfig['seo_pseudo'] : config('ey_config.seo_pseudo');
                if (1 == $seo_pseudo) {
                    $home_url = self::$request->domain().$this->root_dir.$inletStr; // 支持子目录
                    if (!empty($inletStr)) {
                        $home_url .= '?';
                    } else {
                        $home_url .= '/?';
                    }
                    $home_url .= http_build_query(['lang'=>$lang]);
                } else if (2 == $seo_pseudo) { // 生成静态页面代码
                    if ($lang == self::$main_lang) {
                        $home_url = $this->root_dir.'/';
                    } else {
                        $home_url = $this->root_dir.'/'.$lang;
                    }
                } else {
                    $home_url = $this->root_dir.$inletStr.'/'.$lang; // 支持子目录
                }
            }
        }
        /*--end*/
		
        $home_url = get_absolute_url($home_url,'url');
        // $symbol = htmlspecialchars_decode($symbol);
        $str = "<a href='{$home_url}' class='{$style}'>{$basic_indexname}</a>";
        $result = model('Arctype')->getAllPid($typeid);
        $i = 1;
        foreach ($result as $key => $val) {
            if (self::$city_switch_on && self::$site_info) {
                $val['typename'] = self::$site_info['name'].$val['typename'];
            }
            
            if ($i < count($result)) {
                $str .= " {$symbol} <a href='{$val['typeurl']}' class='{$style}'>{$val['typename']}</a>";
            } else {
                $str .= " {$symbol} <a href='{$val['typeurl']}'>{$val['typename']}</a>";
            }
            ++$i;
        }

        return $str;
    }
}