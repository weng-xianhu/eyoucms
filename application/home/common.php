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

// 模板错误提示
switch_exception();

if (!function_exists('set_home_url_mode')) 
{
    // 设置前台URL模式
    function set_home_url_mode() {
        $uiset = I('param.uiset/s', 'off');
        $uiset = trim($uiset, '/');
        $seo_pseudo = tpCache('seo.seo_pseudo');
        if ($seo_pseudo == 1 || $uiset == 'on') {
            config('url_common_param', true);
            config('url_route_on', false);
        } elseif ($seo_pseudo == 2 && $uiset != 'on') {
            config('url_common_param', false);
            config('url_route_on', true);
        } elseif ($seo_pseudo == 3 && $uiset != 'on') {
            config('url_common_param', false);
            config('url_route_on', true);
        }
    }
}

if (!function_exists('set_arcseotitle')) 
{
    /**
     * 设置内容标题
     */
    function set_arcseotitle($title = '', $seo_title = '', $typename = '', $typeid = 0)
    {
        /*针对没有自定义SEO标题的文档*/
        $title = trim($title);
        $seo_title = trim($seo_title);
        $typename = trim($typename);
        if (empty($seo_title)) {
            static $web_name = null;
            if (null === $web_name) {
                $web_name = tpCache('web.web_name');
                $web_name = trim($web_name);
            }
            static $seo_viewtitle_format = null;
            null === $seo_viewtitle_format && $seo_viewtitle_format = tpCache('seo.seo_viewtitle_format');
            switch ($seo_viewtitle_format) {
                case '1':
                    $seo_title = $title;
                    break;
                
                case '3':
                    $seo_title = $title;
                    if (!empty($typename)) {
                        $seo_title .= '_'.$typename;
                    }
                    $seo_title .= '_'.$web_name;
                    break;
                
                case '2':
                default:
                    $opencodetype = config('global.opencodetype');
                    if (1 == $opencodetype && in_array($typeid, [3,9,10])) {
                        $seo_title = '';
                    } else {
                        $seo_title = $title.'_'.$web_name;
                    }
                    break;
            }
        }
        /*--end*/

        return $seo_title;
    }
}

if (!function_exists('set_typeseotitle')) 
{
    /**
     * 设置栏目标题
     */
    function set_typeseotitle($typename = '', $seo_title = '')
    {
        static $lang = null;
        $lang === null && $lang = get_home_lang();
        if (empty($seo_title)) { // 针对没有自定义SEO标题的列表
            $web_name = tpCache('web.web_name');
            $seo_liststitle_format = tpCache('seo.seo_liststitle_format');
            switch ($seo_liststitle_format) {
                case '1':
                    $seo_title = $typename.'_'.$web_name;
                    break;
                
                case '2':
                default:
                    $page = I('param.page/d', 1);
                    if ($page > 1) {
                        if (in_array($lang, ['cn'])) {
                            $typename .= "_第{$page}页";
                        } else {
                            $typename .= "_{$page}";
                        }
                    }
                    $seo_title = $typename.'_'.$web_name;
                    break;
            }
        } else {
            $page = I('param.page/d', 1);
            if ($page > 1) {
                if (in_array($lang, ['cn'])) {
                    $seo_title .= "_第{$page}页";
                } else {
                    $seo_title .= "_{$page}";
                }
            }
        }

        return $seo_title;
    }
}

if (!function_exists('set_tagseotitle')) 
{
    /**
     * 设置Tag标题
     */
    function set_tagseotitle($tag = '', $seo_title = '')
    {
        $page = I('param.page/d', 1);
        static $lang = null;
        $lang === null && $lang = get_home_lang();
        if (empty($seo_title)) { // 针对没有自定义SEO标题的Tag
            $web_name = tpCache('web.web_name');
            if ($page > 1) {
                if (in_array($lang, ['cn'])) {
                    $tag .= "_第{$page}页";
                } else {
                    $tag .= "_{$page}";
                }
            }
            $seo_title = $tag.'_'.$web_name;
        } else {
            if ($page > 1) {
                if (in_array($lang, ['cn'])) {
                    $seo_title .= "_第{$page}页";
                } else {
                    $seo_title .= "_{$page}";
                }
            }
        }

        return $seo_title;
    }
}
