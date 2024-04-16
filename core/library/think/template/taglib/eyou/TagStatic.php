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

namespace think\template\taglib\eyou;

use think\Request;

/**
 * 资源文件加载
 */
class TagStatic extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 资源文件加载
     * @author 小虎哥 by 2018-4-20
     */
    public function getStatic($file = '', $lang = '', $href = '', $code='', $site = '', $version = null)
    {
        if (empty($file)) {
            return '标签static报错：缺少属性 file 或 href 。';
        }

        /*多语言*/
        $paramlang = self::$home_lang;
        if (!empty($lang)) {
            $paramlang = $lang;
        }
        /*--end*/

        /*多城市站点*/
        $paramsite = self::$home_site;
        if (!empty($site)) {
            $paramsite = $site;
        }
        /*--end*/

        static $web_users_tpl_theme = null;
        if (null == $web_users_tpl_theme) {
            $web_users_tpl_theme = config('ey_config.web_users_tpl_theme');
        }

        static $users_wap_tpl_dir = null;
        if (null == $users_wap_tpl_dir) {
            $users_wap_tpl_dir = config('ey_config.users_wap_tpl_dir');
        }

        static $is_mobile = null;
        if (null == $is_mobile) {
            $is_mobile = isMobile();
        }

        static $site_config = null;
        if (self::$city_switch_on && null == $site_config) {
            $site_config = tpCache('site');
        }

        $file = !empty($href) ? $href : $file;

        $parseStr = '';

        // 文件方式导入
        $array = explode(',', $file);
        foreach ($array as $val) {
            $file = $val;
            // ---判断本地文件是否存在，否则返回false，以免@get_headers方法导致崩溃
            if (is_http_url($file)) { // 判断http路径
                $update_time = getTime();
                if (preg_match('/^http(s?):\/\/'.self::$request->host(true).'/i', $file)) { // 判断当前域名的本地服务器文件(这仅用于单台服务器，多台稍作修改便可)
                    // $pattern = '/^http(s?):\/\/([^.]+)\.([^.]+)\.([^\/]+)\/(.*)$/';
                    $pattern = '/^http(s?):\/\/([^\/]+)(.*)$/';
                    preg_match_all($pattern, $file, $matches);//正则表达式
                    if (!empty($matches)) {
                        $filename = $matches[count($matches) - 1][0];
                        /*多语言内置静态资源文件名*/
                        if (!empty($paramlang)) {
                            $lang_filename = preg_replace('/(.*)\.([^.]+)$/i', '$1_'.$paramlang.'.$2', $filename);
                            if (file_exists(realpath(ltrim($lang_filename, '/')))) {
                                $filename = $lang_filename;
                            }
                        }
                        /*--end*/
                        if (!file_exists(realpath(ltrim($filename, '/')))) {
                            continue;
                        }
                        $file = self::$request->domain().$filename;
                    }
                } else { // 不是本地文件禁止使用该方法
                    return $this->toHtml($file, $update_time, $version);
                }
            } else {
                if (!preg_match('/^\//i',$file)) {
                    if (!empty($is_mobile)) {
                        if (file_exists('./template/'.TPL_THEME.'pc/'.$web_users_tpl_theme.'/'.$users_wap_tpl_dir.'/users_login.htm') && preg_match('/^users(_([^\/]*))?\//i', $file)) {
                            $file = str_ireplace("{$web_users_tpl_theme}/", "{$web_users_tpl_theme}/{$users_wap_tpl_dir}/", $file);
                        }
                        else if (file_exists('./template/'.TPL_THEME.'pc/ask/'.$users_wap_tpl_dir) && preg_match('/^ask(_([^\/]*))?\//i', $file)) {
                            $file = str_ireplace("ask/", "ask/{$users_wap_tpl_dir}/", $file);
                        }
                    }
                    $file = preg_replace('/^users(_([^\/]*))?\//', $web_users_tpl_theme.'/', $file); // 支持会员中心模板切换
                    if (empty($code)) {
                        if (!empty($is_mobile) && preg_match('/^([a-zA-Z0-9_-]+)\/'.$users_wap_tpl_dir.'\//i', $file)) {
                            $file = '/template/'.TPL_THEME.'pc/'.$file;
                        } else {
                            if (self::$city_switch_on && !empty($site_config['site_template']) && !empty($paramsite) && !preg_match('/^users(_([^\/]*))?\//i', $file)) { // 多城市站点的独立模板
                                if (file_exists('./template/'.THEME_STYLE_PATH.'/city/'.$paramsite)) {
                                    $file = '/template/'.THEME_STYLE_PATH.'/city/'.$paramsite.'/'.$file;
                                } else if (file_exists('./template/'.THEME_STYLE_PATH.'/'.$paramsite)) {
                                    $file = '/template/'.THEME_STYLE_PATH.'/'.$paramsite.'/'.$file;
                                } else {
                                    $file = '/template/'.THEME_STYLE_PATH.'/'.$file;
                                }
                            } else { // 默认模板
                                $file = '/template/'.THEME_STYLE_PATH.'/'.$file;
                            }
                        }
                    } else {
                        if ($is_mobile && file_exists('./template/plugins/'.$code.'/mobile/'.$file)) {
                            $file = '/template/plugins/'.$code.'/mobile/'.$file;
                        } else {
                            $file = '/template/plugins/'.$code.'/'.THEME_STYLE.'/'.$file;
                        }
                    }
                } else {
/*
                    if (!empty($is_mobile)) {
                        if (file_exists('./template/'.TPL_THEME.'pc/'.$web_users_tpl_theme.'/'.$users_wap_tpl_dir.'/users_login.htm') && preg_match('/\/users(_([^\/]*))?\//i', $file)) {
                            $file = str_ireplace("{$web_users_tpl_theme}/", "{$web_users_tpl_theme}/{$users_wap_tpl_dir}/", $file);
                        }
                        else if (file_exists('./template/'.TPL_THEME.'pc/ask/'.$users_wap_tpl_dir) && preg_match('/\/ask(_([^\/]*))?\//i', $file)) {
                            $file = str_ireplace("ask/", "ask/{$users_wap_tpl_dir}/", $file);
                        }
                    }
                    */
                    if (empty($code)) {
                        $tpl_theme = trim(TPL_THEME, '/');
                        // 支持前台模板切换
                        $file = preg_replace('/^\/template\/(pc|mobile)\//', '/template/'.$tpl_theme.'/${1}/', $file);
                        // 支持会员中心模板切换
                        $file = preg_replace('/^\/template\/'.$tpl_theme.'\/(pc|mobile)\/users(_([^\/]*))?\//', '/template/'.$tpl_theme.'/${1}/'.$web_users_tpl_theme.'/', $file);
                    }
                }
                /*多语言内置静态资源文件名*/
                if (!empty($paramlang)) {
                    $lang_filename = preg_replace('/(.*)\.([^.]+)$/i', '$1_'.$paramlang.'.$2', $file);
                    if (file_exists(realpath(ltrim($lang_filename, '/')))) {
                        $file = $lang_filename;
                    }
                }
                /*--end*/
                $new_file = preg_replace('/\?(.*)$/i', '', $file);
                if (!file_exists(ltrim($new_file, '/'))) {
                    continue;
                }

                try{
                    if (self::$request->controller() == 'Buildhtml') {
                        $update_time = getTime();
                    } else {
                        $fileStat = stat(ROOT_PATH . ltrim($new_file, '/'));
                        $update_time = !empty($fileStat['mtime']) ? $fileStat['mtime'] : getTime();
                    }
                } catch (\Exception $e) {
                    $update_time = getTime();
                }
            }
            // -------------end---------------

            $parseStr .= $this->toHtml($file, $update_time, $version);
        }

        return $parseStr;
    }

    /**
     * 资源文件转化为html代码
     * @param string $file 文件路径|url路径
     * @param intval $update_time 文件时间戳
     * @author 小虎哥 by 2018-4-20
     */
    private function toHtml($file = '', $update_time = '', $version = null)
    {
        $parseStr = '';
        if (!is_http_url($file)) {
            $file = $this->root_dir.$file; // 支持子目录
        }
        if (!empty($update_time) && !empty($version)) {
            $update_time_str = '?v='.$update_time;
        } else {
            $update_time_str = '';
        }
        $type = preg_replace('/^(.*)\.([a-z]+)([^a-z]*)(.*)$/i', '${2}', strtolower($file));
        switch ($type) {
            case 'js':
                $file = get_absolute_url($file);
                $parseStr =<<<EOF
<script language="javascript" type="text/javascript" src="{$file}{$update_time_str}"></script>

EOF;
                break;
            case 'css':
                $file = get_absolute_url($file);
                $parseStr =<<<EOF
<link href="{$file}{$update_time_str}" rel="stylesheet" media="screen" type="text/css" />

EOF;
                break;
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'ico':
            case 'bmp':
            case 'gif':
            case 'webp':
            case 'svg':
                $file = get_absolute_url($file);
                $parseStr .= $file . $update_time_str;
                break;
            case 'php':
                $parseStr .= '<?php include "' . $file . '"; ?>';
                break;
        }

        return $parseStr;
    }
}