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

namespace think\template\taglib\api;

use think\Db;
use think\Request;

/**
 * 基类
 */
class Base
{
    /**
     * 主体语言（语言列表中最早一条）
     */
    public $main_lang = 'cn';

    /**
     * 子目录
     */
    public $root_dir = '';

    public $request = null;

    /**
     * 当前栏目ID
     */
    public $tid = 0;

    /**
     * 当前文档aid
     */
    public $aid = 0;

    //构造函数
    function __construct()
    {
        // 控制器初始化
        $this->_initialize();
    }

    // 初始化
    protected function _initialize()
    {
        $this->tid = input("param.typeid/d", 0);
        $this->aid = input("param.aid/d", 0);
        $this->main_lang = get_main_lang();
        $this->root_dir = ROOT_DIR;
        if (null == $this->request) $this->request = Request::instance();
    }

    //查询虎皮椒支付有没有配置相应的(微信or支付宝)支付
    public function findHupijiaoIsExis($type = '')
    {
        $hupijiaoInfo = Db::name('weapp')->where(['code'=>'Hupijiaopay','status'=>1])->find();
        $HupijiaoPay = Db::name('pay_api_config')->where(['pay_mark'=>'Hupijiaopay'])->find();
        if (empty($HupijiaoPay) || empty($hupijiaoInfo)) return true;
        if (empty($HupijiaoPay['pay_info'])) return true;
        $PayInfo = unserialize($HupijiaoPay['pay_info']);
        if (empty($PayInfo)) return true;
        if (!isset($PayInfo['is_open_pay']) || $PayInfo['is_open_pay'] == 1) return true;
        $type .= '_appid';
        if (!isset($PayInfo[$type]) || empty($PayInfo[$type])) return true;

        return false;
    }
    
    /**
     * 【新的默认图片】 图片不存在，显示默认无图封面
     * @param string $pic_url 图片路径
     * @param string|boolean $domain 完整路径的域名
     */
    public function get_default_pic($pic_url = '', $domain = true, $tcp = '')
    {
        $pic_url = get_default_pic($pic_url, $domain);
        $pic_url = str_replace('/public/static/common/images/not_adv.jpg', '/public/static/common/images/default_litpic_1.png', $pic_url);

        if (!empty($tcp) && preg_match('/^\/\/.*$/', $pic_url)) {
            $pic_url = $tcp . ':' . $pic_url;
        }

        return $pic_url;
    }

    /**
     * html内容里的图片地址替换成http路径
     * @param string $content 内容
     * @return    string
     */
    public function html_httpimgurl($content = '', $timeVersion = false)
    {
        if (!empty($content)) {
            $t = '';
            if (true === $timeVersion) {
                $t = '?t='.getTime();
            }

            // 图片远程化
            $pregRule = "/<img(.*?)src(\s*)=(\s*)[\'|\"](\/[\/\w]+)?(\/public\/upload\/|\/uploads\/|\/\/)(.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp|\.ico]))[\'|\"](.*?)[\/]?(\s*)>/i";
            $content  = preg_replace($pregRule, '<img ${1} src="' . request()->domain().ROOT_DIR . '${5}${6}'.$t.'" ${7} />', $content);

            // 视频远程化
            $pregRule = "/<source(.*?)src(\s*)=(\s*)[\'|\"](\/[\/\w]+)?(\/public\/upload\/|\/uploads\/|\/\/)(.*?(?:[\.mp4|\.mov|\.m4v|\.3gp|\.avi|\.m3u8|\.webm]))[\'|\"](.*?)[\/]?(\s*)>/i";
            $content  = preg_replace($pregRule, '<source ${1} src="' . request()->domain().ROOT_DIR . '${5}${6}" ${7} />', $content);
            $pregRule = "/<video(.*?)src(\s*)=(\s*)[\'|\"](\/[\/\w]+)?(\/public\/upload\/|\/uploads\/|\/\/)(.*?(?:[\.mp4|\.mov|\.m4v|\.3gp|\.avi|\.m3u8|\.webm]))[\'|\"](.*?)[\/]?(\s*)>/i";
            $content  = preg_replace($pregRule, '<video ${1} src="' . request()->domain().ROOT_DIR . '${5}${6}" ${7} />', $content);

            $content = str_replace(request()->domain().ROOT_DIR.'//', 'http://', $content);
        }

        return $content;
    }

    /**
     * 设置内容标题
     */
    public function set_arcseotitle($title = '', $seo_title = '', $typename = '')
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
                    $seo_title = $title.'_'.$web_name;
                    break;
            }
        }
        /*--end*/

        return $seo_title;
    }
}