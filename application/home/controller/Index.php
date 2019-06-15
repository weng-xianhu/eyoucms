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

class Index extends Base
{
    public function _initialize() {
        parent::_initialize();
    }

    public function index()
    {
        // 快递100返回时，重定向关闭父级弹框
        $coname = input('coname', '');
        $m = input('m', '');
        if (!empty($coname) || 'user' == $m) {
            if (isWeixin()) {
                $this->redirect(url('user/Shop/shop_centre'));
                exit;
            }else{
                $this->redirect(url('api/Rewrite/close_parent_layer'));
                exit;
            }
        }
        // end

        if (config('is_https')) {
            $filename = 'indexs.html';
        } else {
            $filename = 'index.html';
        }

        if (file_exists($filename)) {
            @unlink($filename);
        }

        //自动生成HTML版
        if(isset($_GET['clear']) || !file_exists($filename))
        {
            /*获取当前页面URL*/
            $result['pageurl'] = request()->url(true);
            /*--end*/
            $eyou = array(
                'field' => $result,
            );
            $this->eyou = array_merge($this->eyou, $eyou);
            $this->assign('eyou', $this->eyou);
            
            /*模板文件*/
            $viewfile = 'index';
            /*--end*/

            /*多语言内置模板文件名*/
            if (!empty($this->home_lang)) {
                $viewfilepath = TEMPLATE_PATH.$this->theme_style.DS.$viewfile."_{$this->home_lang}.".$this->view_suffix;
                if (file_exists($viewfilepath)) {
                    $viewfile .= "_{$this->home_lang}";
                }
            }
            /*--end*/

            $html = $this->fetch(":{$viewfile}");
            // @file_put_contents($filename, $html);
            return $html;
        }
        else
        {
            // header('HTTP/1.1 301 Moved Permanently');
            // header('Location:'.$filename);
        }
    }
}