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
use think\Config;
use think\Cookie;
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
     * 前台当前语言
     */
    public $home_lang = 'cn';

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
        /*多语言*/
        $this->main_lang = get_main_lang();
        $this->home_lang = get_home_lang();
        /*--end*/
        // 子目录安装路径
        $this->root_dir = ROOT_DIR;
            
        if (null == $this->request) {
            $this->request = Request::instance();
        }

        $this->tid = input("param.tid/s", '');
        /*tid为目录名称的情况下*/
        $this->tid = getTrueTypeid($this->tid);
        /*--end*/

        $this->aid = input("param.aid/s", '');
        /*在aid传值为自定义文件名的情况下*/
        $this->aid = getTrueAid($this->aid);
        /*--end*/
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
}