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

use think\Db;

/**
 *
 * 页面缓存
 */
class HtmlCacheLogic 
{
    public function __construct() 
    {

    }

    /**
     * 清除首页的页面缓存文件
     */
    public function clear_index()
    {
        $web_cmsmode = tpCache('web.web_cmsmode');
        $html_cache_arr = config('HTML_CACHE_ARR');
        if (1 == intval($web_cmsmode)) { // 页面html静态永久缓存
            $fileList = glob(HTML_ROOT.'http*/'.$html_cache_arr['home_Index_index']['filename'].'/*_html/index*.html');
            if (!empty($fileList)) {
                foreach ($fileList as $k2 => $file) {
                    if (file_exists($file) && preg_match('/index(_\d+)?\.html$/i', $file)) {
                        @unlink($file);
                    }
                }
            }
        } else { // 页面cache自动过期缓存
            $fileList = glob(HTML_ROOT.'http*/'.$html_cache_arr['home_Index_index']['filename'].'/*_cache/index/*');
            if (!empty($fileList)) {
                foreach ($fileList as $k2 => $dir) {
                    if (file_exists($dir) && is_dir($dir)) {
                        delFile($dir, true);
                    }
                }
            }
        }
    }

    /**
     * 清除指定栏目的页面缓存文件
     * @param array $typeids 栏目ID数组
     */
    public function clear_arctype($typeids = [])
    {
        $web_cmsmode = tpCache('web.web_cmsmode');
        $html_cache_arr = config('HTML_CACHE_ARR');
        if (!empty($typeids)) {
            foreach ($typeids as $key => $tid) {
                if (1 == intval($web_cmsmode)) { // 页面html静态永久缓存
                    $fileList = glob(HTML_ROOT.'http*/'.$html_cache_arr['home_Lists_index']['filename'].'/*_html/'.$tid.'*.html');
                    if (!empty($fileList)) {
                        foreach ($fileList as $k2 => $file) {
                            if (file_exists($file) && preg_match('/'.$tid.'(_\d+)?\.html$/i', $file)) {
                                @unlink($file);
                            }
                        }
                    }
                } else { // 页面cache自动过期缓存
                    $fileList = glob(HTML_ROOT.'http*/'.$html_cache_arr['home_Lists_index']['filename'].'/*_cache/'.$tid.'/*');
                    if (!empty($fileList)) {
                        foreach ($fileList as $k2 => $dir) {
                            if (file_exists($dir) && is_dir($dir)) {
                                delFile($dir, true);
                            }
                        }
                    }
                }
            }
        } else { // 清除全部的栏目页面缓存
            $fileList = glob(HTML_ROOT.'http*/'.$html_cache_arr['home_Lists_index']['filename'].'/*');
            if (!empty($fileList)) {
                foreach ($fileList as $k2 => $dir) {
                    if (file_exists($dir) && is_dir($dir)) {
                        delFile($dir, true);
                    }
                }
            }
        }

        $this->clear_index(); // 清除首页缓存
    }

    /**
     * 清除指定文档的页面缓存文件
     * @param array $aids 文档ID数组
     */
    public function clear_archives($aids = [])
    {
        $web_cmsmode = tpCache('web.web_cmsmode');
        $html_cache_arr = config('HTML_CACHE_ARR');
        if (!empty($aids)) {
            $row = Db::name('archives')->field('aid,typeid')
                ->where([
                    'aid'   => ['IN', $aids],
                ])->select();
            foreach ($row as $key => $val) {
                $aid = $val['aid'];
                $typeid = $val['typeid'];
                if (1 == intval($web_cmsmode)) { // 页面html静态永久缓存
                    $fileList = glob(HTML_ROOT.'http*/'.$html_cache_arr['home_View_index']['filename'].'/*_html/'.$aid.'*.html');
                    if (!empty($fileList)) {
                        foreach ($fileList as $k2 => $file) {
                            if (preg_match('/'.$aid.'(_\d+)?\.html$/i', $file)) {
                                @unlink($file);
                            }
                        }
                    }
                } else { // 页面cache自动过期缓存
                    $fileList = glob(HTML_ROOT.'http*/'.$html_cache_arr['home_View_index']['filename'].'/*_cache/'.$aid.'/*');
                    if (!empty($fileList)) {
                        foreach ($fileList as $k2 => $dir) {
                            if (file_exists($dir) && is_dir($dir)) {
                                delFile($dir, true);
                            }
                        }
                    }
                }
            }
        } else { // 清除所有的文档页面缓存
            $fileList = glob(HTML_ROOT.'http*/'.$html_cache_arr['home_View_index']['filename'].'*');
            if (!empty($fileList)) {
                foreach ($fileList as $k2 => $dir) {
                    if (file_exists($dir) && is_dir($dir)) {
                        delFile($dir, true);
                    }
                }
            }
        }

        $this->clear_arctype(); // 清除所有的栏目
    }
}
