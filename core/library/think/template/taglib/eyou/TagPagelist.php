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


/**
 * 列表分页代码
 */
class TagPagelist extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取列表分页代码
     * @author wengxianhu by 2018-4-20
     */
    public function getPagelist($pages = '', $listitem = '', $listsize = '')
    {
        if (empty($pages)) {
            echo '标签pagelist报错：只适用在标签list之后。';
            return false;
        }
        $listitem = !empty($listitem) ? $listitem : 'info,index,end,pre,next,pageno';
        $listsize = !empty($listsize) ? $listsize : '3';

        if ($this->home_lang != $this->main_lang) {
            // $listitem = str_replace('info', ',', $listitem);
            // $listitem = str_replace(',,', ',', $listitem);
        }

        $value = $pages->render($listitem, $listsize);

        return $value;
    }
}