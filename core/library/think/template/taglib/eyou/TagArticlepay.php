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

//文章付费阅读标签
use think\Db;

class TagArticlepay extends Base
{
    public $users_id = 0;

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->users_id = session('users_id');
        $this->users_id = !empty($this->users_id) ? $this->users_id : 0;
    }

    /**
     * 文章付费阅读标签
     * @author hln by 2021-04-20
     */
    public function getArticlepay()
    {
        $aid = $this->aid;
        if (empty($aid)) {
            return '标签articlepay报错：缺少属性 aid 值。';
        }
        $artData = Db::name('archives')
            ->alias('a')
            ->field('a.users_price,b.content')
            ->join('article_content b','a.aid = b.aid')
            ->where('a.aid',$aid)
            ->find();
        $result['displayId'] = ' id="article_display_'.$aid.'_1619061972" style="display:none;" ';

        $pay_data = Db::name('article_pay')->field('part_free,free_content')->where('aid',$aid)->find();

        if (0<$artData['users_price'] && !empty($pay_data)){
            $is_pay = Db::name('article_order')->where(['users_id'=>$this->users_id,'order_status'=>1,'product_id'=>$aid])->find();
            if (empty($is_pay)){
                if(0 == $pay_data['part_free']){
                    $result['content'] = '';
                }else if(in_array($pay_data['part_free'],[1,2])){
                    $result['content'] = $pay_data['free_content'];
                }
            }else{
                $result['displayId'] = ' id="article_display_'.$aid.'_1619061972" ';
                if(0 == $pay_data['part_free']){
                    $result['content'] = $artData['content'];
                }else if(1 == $pay_data['part_free']){
                    $result['content'] = $pay_data['free_content'].$artData['content'];
                }else if(2 == $pay_data['part_free']){
                    $result['content'] = $artData['content'];
                }
            }
        }else{
            $result['content'] = $artData['content'];
        }

        $result['content'] = htmlspecialchars_decode($result['content']);
        $titleNew = !empty($data['title']) ? $data['title'] : '';
        $result['content'] = img_style_wh($result['content'], $titleNew);
        $result['content'] = handle_subdir_pic($result['content'], 'html');

        $result['contentId'] = ' id="article_content_'.$aid.'_1619061972" ';
        if (isMobile()){
            $result['onclick'] = ' href="javascript:void(0);" onclick="ey_article_1618968479('.$aid.');" ';//第一种跳转页面支付
        }else{
            $result['onclick'] = ' href="javascript:void(0);" onclick="ArticleBuyNow('.$aid.');" ';//第二种弹框页支付
        }
        $version = getCmsVersion();
        $get_content_url = "{$this->root_dir}/index.php?m=api&c=Ajax&a=ajax_get_content";
        $buy_url = url('user/Article/buy');

        $result['hidden'] = <<<EOF
<script type="text/javascript">
    var buy_url_1618968479 = '{$buy_url}';
    var aid_1618968479 = {$aid};
    var root_dir_1618968479 = '{$this->root_dir}';
</script>
<script type="text/javascript" src="{$this->root_dir}/public/static/common/js/tag_articlepay.js?v={$version}"></script>
<script type="text/javascript">
    ey_ajax_get_content_1618968479({$aid},'{$get_content_url}');
</script>
EOF;
        return $result;
    }
}