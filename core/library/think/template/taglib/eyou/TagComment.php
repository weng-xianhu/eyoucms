<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海口快推科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 陈风任 <491085389@qq.com>
 * Date: 2021-01-25
 */

namespace think\template\taglib\eyou;

/**
 * 调用商品评价
 */
class TagComment extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 调用商品评价
     * @author wengxianhu by 2018-4-20
     */
    public function getComment($aid = null)
    {
        $aid = !empty($aid) ? intval($aid) : $this->aid;
        if (empty($aid)) return '标签Comment报错：缺少属性 aid 值';
        $version = getCmsVersion();

        // JS所需参数
        $JsonData['IsMobile'] = isMobile() ? 1 : 0;
        $JsonData['ProductID'] = $aid;
        $JsonData['GetAllDataUrl'] = url('home/Ajax/product_comment', ['aid' => $aid, '_ajax' => 1], true, false, 1, 1, 0);
        $JsonData['GetCommentUrl'] = url('home/Ajax/comment_list', ['aid' => $aid, '_ajax' => 1], true, false, 1, 1, 0);
        $JsonData = json_encode($JsonData);

        // 页面所需参数
        $result['CommentID'] = 'id="ajax_comment_return"';
        $srcurl = get_absolute_url("{$this->root_dir}/public/static/common/js/tag_comment.js?v={$version}");
        $result['hidden'] = <<<EOF
<script type="text/javascript">
    var eyou_data_json_v563866 = {$JsonData};
</script>

<script language="javascript" type="text/javascript" src="{$srcurl}"></script>
EOF;
        return $result;
    }
}