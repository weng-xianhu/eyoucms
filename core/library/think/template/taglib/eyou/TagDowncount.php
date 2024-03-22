<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海口快推科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */

namespace think\template\taglib\eyou;

/**
 * 在内容页模板显示下载次数
 */
class TagDowncount extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 在内容页模板显示下载次数
     * @author wengxianhu by 2018-4-20
     */
    public function getDowncount($aid = '')
    {
        $aid = !empty($aid) ? intval($aid) : $this->aid;

        if (empty($aid)) {
            return '标签downcount报错：缺少属性 aid 值。';
        }

        $times = getTime();
        static $downcount_js = null;
        static $static_aid = 0;
        if (null === $downcount_js || $static_aid != $aid) {
            $static_aid = $aid;
            $downcount_js = <<<EOF
<script type="text/javascript">
    function tag_downcount_v670918(aid)
    {
        var ajax = new XMLHttpRequest();
        ajax.open("post", "{$this->root_dir}/index.php?m=api&c=Ajax&a=downcount", true);
        ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
        ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        ajax.send("aid="+aid);
        ajax.onreadystatechange = function () {
            if (ajax.readyState==4 && ajax.status==200) {
        　　　　document.getElementById("eyou_downcount_{$times}_"+aid).innerHTML = ajax.responseText;
          　}
        } 
    }
</script>
EOF;
        } else {
            $downcount_js = '';
        }

        $parseStr = <<<EOF
<i id="eyou_downcount_{$times}_{$aid}" class="eyou_downcount" style="font-style:normal"></i> 
<script type="text/javascript">tag_downcount_v670918({$aid});</script>
EOF;

        $parseStr = $downcount_js . $parseStr;

        return $parseStr;
    }
}