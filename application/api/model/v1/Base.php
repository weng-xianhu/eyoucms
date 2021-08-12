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

namespace app\api\model\v1;

use think\Db;
use think\Model;
use think\Request;
use think\template\taglib\api\Base as BaseTag;

/**
 * 小程序基类模型
 */
class Base extends Model
{
    /**
     * 当前Request对象实例
     * @var null
     */
    public static $request = null; // 当前Request对象实例

    /**
     * 小程序appid
     * @var null
     */
    public static $appId = null;

    /**
     * 语言标识
     */
    public static $lang = 'cn';

    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        self::$lang = get_main_lang();
        self::$appId = input('param.appId/s');
        null === self::$request && self::$request = Request::instance();
        $this->baseTag = new BaseTag;
    }

    /**
     * html内容里的图片地址替换成http路径
     * @param string $content 内容
     * @return    string
     */
    public function html_httpimgurl($content = '', $timeVersion = false)
    {
        return $this->baseTag->html_httpimgurl($content, $timeVersion);
    }

    /**
     * diy页面详情
     * @param int $page_id
     * @throws \think\exception\DbException
     */
    public function get_default_pic($pic_url = '', $domain = true, $tcp = 'http')
    {
        return $this->baseTag->get_default_pic($pic_url, $domain, $tcp);
    }

    /**
     * 设置内容标题
     */
    public function set_arcseotitle($title = '', $seo_title = '', $typename = '')
    {
        return $this->baseTag->set_arcseotitle($title, $seo_title, $typename);
    }
}