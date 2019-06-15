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
 * 在内容页模板追加显示浏览量
 */
class TagArcclick extends Base
{
    public $aid = 0;

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->aid = input('param.aid/d', 0);
    }

    /**
     * 在内容页模板追加显示浏览量
     * @author wengxianhu by 2018-4-20
     */
    public function getArcclick($aid = '', $value = '')
    {
        $aid = !empty($aid) ? $aid : $this->aid;

        if (empty($aid)) {
            return '标签arcclick报错：缺少属性 aid 值。';
        }

        if (empty($value)) {
            $value = M('archives')->where([
                    'aid'   => $aid,
                    'lang'  => $this->home_lang,
                ])->getField('click');
        }

        $parseStr = <<<EOF
<i id="eyou_arcclick" class="eyou_arcclick" style="font-style:normal">{$value}</i> 
<script type="text/javascript">
    function tag_arcclick()
    {
        //步骤一:创建异步对象
        var ajax = new XMLHttpRequest();
        //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
        ajax.open("get", "{$this->root_dir}/index.php?m=api&c=Ajax&a=arcclick&aid={$aid}", true);
        // 给头部添加ajax信息
        ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
        //步骤三:发送请求
        ajax.send();
        //步骤四:注册事件 onreadystatechange 状态改变就会调用
        ajax.onreadystatechange = function () {
            //步骤五 如果能够进到这个判断 说明 数据 完美的回来了,并且请求的页面是存在的
            if (ajax.readyState==4 && ajax.status==200) {
        　　　　document.getElementById("eyou_arcclick").innerHTML = ajax.responseText;
          　}
        } 
    }
    tag_arcclick();
</script>
EOF;

        return $parseStr;
    }
}