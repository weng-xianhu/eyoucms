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
use app\api\logic\v1\WechatLogic;

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
    public static $lang = null;

    /**
     * 插件标识
     */
    public static $weapp_code = 'cn';

    /**
     * 平台标识
     */
    public static $provider = 'weixin';

    /**
     * 系统配置
     */
    public $globalConfig = [];
    public $usersConfig = [];

    public $wechatLogic = [];

    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        $this->baseTag = new BaseTag;
        self::$lang = BaseTag::$main_lang;
        self::$request = BaseTag::$request;
        self::$appId = input('param.appId/s');
        self::$provider = input('param.provider/s', 'weixin');
        self::$weapp_code = 'OpenMinicode';
        // $cmstype = input('param.cmstype');
        $this->globalConfig = BaseTag::$globalConfig;
        $this->usersConfig = BaseTag::$usersConfig;
        $this->wechatLogic = new WechatLogic;
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
     * 时间格式转换
     * @param  integer $t [description]
     * @return [type]        [description]
     */
    public function time_format($t = 0)
    {
        return $this->baseTag->time_format($t);
    }

    /**
     * 【新的默认图片】 图片不存在，显示默认无图封面
     * @param string $pic_url 图片路径
     * @param string|boolean $domain 完整路径的域名
     */
    public function get_default_pic($pic_url = '', $domain = true, $tcp = 'http')
    {
        return $this->baseTag->get_default_pic($pic_url, $domain, $tcp);
    }
    
    /**
     * 默认头像
     * @param  string  $head_pic [description]
     * @param  boolean $is_admin [description]
     * @param  string  $sex      [description]
     * @return [type]            [description]
     */
    public function get_head_pic($head_pic = '', $is_admin = false, $sex = '保密')
    {
        return $this->baseTag->get_head_pic($head_pic, $is_admin, $sex);
    }

    /**
     * 设置内容标题
     */
    public function set_arcseotitle($title = '', $seo_title = '', $typename = '')
    {
        return $this->baseTag->set_arcseotitle($title, $seo_title, $typename);
    }
}