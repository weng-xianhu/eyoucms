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
class TagSearchform extends Base
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
    public function getSearchform($typeid = '', $channel = '', $notypeid = '', $flag = '', $noflag = '')
    {
        $searchurl = url('home/Search/lists');

        $hidden = '';
        $ey_config = config('ey_config'); // URL模式
        if (1 == $ey_config['seo_pseudo'] && 1 == $ey_config['seo_dynamic_format']) {
            $hidden .= '<input type="hidden" name="m" value="home" />';
            $hidden .= '<input type="hidden" name="c" value="Search" />';
            $hidden .= '<input type="hidden" name="a" value="lists" />';
            /*多语言*/
            $lang = Request::instance()->param('lang/s');
            !empty($lang) && $hidden .= '<input type="hidden" name="lang" value="'.$lang.'" />';
            /*--end*/
        }
        $hidden .= '<input type="hidden" name="typeid" id="typeid" value="'.$typeid.'" />';
        $hidden .= '<input type="hidden" name="channel" id="channel" value="'.$channel.'" />';
        $hidden .= '<input type="hidden" name="notypeid" id="notypeid" value="'.$notypeid.'" />';
        $hidden .= '<input type="hidden" name="flag" id="flag" value="'.$flag.'" />';
        $hidden .= '<input type="hidden" name="noflag" id="noflag" value="'.$noflag.'" />';

        $result[0] = array(
            'searchurl' => $searchurl,
            'action' => $searchurl,
            'hidden'    => $hidden,
        );
        
        return $result;
    }
}