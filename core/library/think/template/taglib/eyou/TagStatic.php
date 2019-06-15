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
    public function getStatic($file = '', $lang = '', $href = '', $code='')
    {
        if (empty($file)) {
            return '标签static报错：缺少属性 file 或 href 。';
        }

        /*多语言*/
        $paramlang = input('param.lang/s');
        if (!empty($lang)) {
            $paramlang = $lang;
        }
        /*--end*/

        $file = !empty($href) ? $href : $file;

        static $request = null;
        null == $request && $request = Request::instance();
        $parseStr = '';

        // 文件方式导入
        $array = explode(',', $file);
        foreach ($array as $val) {
            $file = $val;
            // ---判断本地文件是否存在，否则返回false，以免@get_headers方法导致崩溃
            if (is_http_url($file)) { // 判断http路径
                if (preg_match('/^http(s?):\/\/'.$request->host(true).'/i', $file)) { // 判断当前域名的本地服务器文件(这仅用于单台服务器，多台稍作修改便可)
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
                        $http_url = $file = $request->domain().$filename;
                    }
                } else { // 不是本地文件禁止使用该方法
                    return $this->toHtml($file);
                }
                
            } else {
                if (!preg_match('/^\//i',$file)) {
                    if (empty($code)) {
                        $file = '/template/'.THEME_STYLE.'/'.$file;
                    } else {
                        $file = '/template/plugins/'.$code.'/'.THEME_STYLE.'/'.$file;
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
                if (!file_exists(ltrim($file, '/'))) {
                    continue;
                }
                $http_url = $request->domain().$this->root_dir.$file; // 支持子目录
            }
            // -------------end---------------

            $headInf = @get_headers($http_url,1); 
            $update_time = !empty($headInf['Last-Modified']) ? strtotime($headInf['Last-Modified']) : '';
            $parseStr .= $this->toHtml($file, $update_time);
        }

        return $parseStr;
    }

    /**
     * 资源文件转化为html代码
     * @param string $file 文件路径|url路径
     * @param intval $update_time 文件时间戳
     * @author 小虎哥 by 2018-4-20
     */
    private function toHtml($file = '', $update_time = '')
    {
        $parseStr = '';
        $file = $this->root_dir.$file; // 支持子目录
        $update_time_str = !empty($update_time) ? '?t='.$update_time : '';
        $type = strtolower(substr(strrchr($file, '.'), 1));
        switch ($type) {
            case 'js':
                $parseStr .= '<script type="text/javascript" src="' . $file . $update_time_str.'"></script>';
                break;
            case 'css':
                $parseStr .= '<link rel="stylesheet" type="text/css" href="' . $file . $update_time_str.'" />';
                break;
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'ico':
            case 'bmp':
            case 'gif':
            case 'webp':
                $parseStr .= '<img src="' . $file . $update_time_str.'" width="" height="" alt="" title="" class="" id="" style="" />';
                break;
            case 'php':
                $parseStr .= '<?php include "' . $file . '"; ?>';
                break;
        }

        return $parseStr;
    }
}