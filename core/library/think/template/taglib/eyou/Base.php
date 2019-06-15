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

use think\Config;
use think\Cookie;

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
     * 前台当前语言
     */
    public $home_lang = 'cn';

    /**
     * 子目录
     */
    public $root_dir = '';

    //构造函数
    function __construct()
    {
        // 控制器初始化
        $this->_initialize();
    }

    // 初始化
    protected function _initialize()
    {
        /*多语言*/
        $this->main_lang = get_main_lang();
        $this->home_lang = get_home_lang();
        /*--end*/
        // 子目录安装路径
        $this->root_dir = ROOT_DIR;
    }

    /**
     * 在typeid传值为目录名称的情况下，获取栏目ID
     */
    public function getTrueTypeid($typeid = '')
    {
        /*tid为目录名称的情况下*/
        if (!empty($typeid) && strval($typeid) != strval(intval($typeid))) {
            $typeid = M('Arctype')->where([
                    'dirname'   => $typeid,
                    'lang'  => $this->home_lang,
                ])->cache(true,EYOUCMS_CACHE_TIME,"arctype")
                ->getField('id');
        }
        /*--end*/

        return $typeid;
    }
}