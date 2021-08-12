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
 * 搜索表单
 */
class TagSearchurl extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取搜索表单
     * @author wengxianhu by 2018-4-20
     */
    public function getSearchurl()
    {
        $url = url("home/Search/lists");
        $ey_config = config('ey_config'); // URL模式
        if (2 == $ey_config['seo_pseudo'] || (1 == $ey_config['seo_pseudo'] && 1 == $ey_config['seo_dynamic_format'])) {
            $result = '';
            $result .= $url.'"><input type="hidden" name="m" value="home" />';
            $result .= '<input type="hidden" name="c" value="Search" />';
            $result .= '<input type="hidden" name="a" value="lists';

            /*手机端域名*/
            $goto = input('param.goto/s');
            $goto = trim($goto, '/');
            !empty($goto) && $result .= '" /><input type="hidden" name="goto" value="'.$goto;
            /*--end*/

            /*多语言*/
            $lang = Request::instance()->param('lang/s');
            !empty($lang) && $result .= '" /><input type="hidden" name="lang" value="'.$lang;
            /*--end*/
        } else {
            $result = $url;
        }
        
        return $result;
    }
}