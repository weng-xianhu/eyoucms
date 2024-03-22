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

//文章付费阅读标签
use think\Db;

class TagDownloadpay extends Base
{
    public $users = [];
    public $users_id = 0;
    public $users_level = 0;

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->users = session('users');
        $this->users_id = empty($this->users['users_id']) ? 0 : intval($this->users['users_id']);
        $this->users_level = empty($this->users['level']) ? 0 : intval($this->users['level']);
    }

    /**
     * 下载付费标签
     * @author hln by 2021-04-20
     */
    public function getDownloadpay()
    {
        $aid = $this->aid;
        if (empty($aid)) {
            return '标签downloadpay报错：缺少属性 aid 值。';
        }
        $artData = Db::name('archives')->where('aid', $aid)->find();
        $artData['arc_level_value'] = 0;
        if (0 < $artData['arc_level_id']) {
            $artData['arc_level_value'] = Db::name('users_level')->where(['level_id'=>$artData['arc_level_id']])->value('level_value');
        }

        $canDownload = 0;
        $buyVip = 0;
        $msg = '';
        if (empty($artData['restric_type'])) { // 免费
            $canDownload = 1;
        } else if (1 == $artData['restric_type']) { // 付费
            // 查询是否已购买
            $where = [
                'order_status' => 1,
                'product_id' => intval($aid),
                'users_id' => $this->users_id
            ];
            $count = Db::name('download_order')->where($where)->count();
            if (!empty($count)){
                $canDownload = 1;
            }
        }else if (2 == $artData['restric_type']) { // 指定会员
            if ($artData['arc_level_id'] == 0) { // 免费
                $canDownload = 1;
            }else{
                $level_value = Db::name('users_level')->where('level_id',$this->users_level)->value('level_value');
                if ($level_value >= $artData['arc_level_value']){ // 会员专享
                    $canDownload = 1;
                }else{
                    if (0 == $artData['no_vip_pay']){
                        $buyVip = 1;
                    }else{ // 非会员付费
                        $where = [
                            'order_status' => 1,
                            'product_id' => intval($aid),
                            'users_id' => $this->users_id
                        ];
                        $count = Db::name('download_order')->where($where)->count();
                        if (!empty($count)){
                            $canDownload = 1;
                        }
                    }
                }
            }
        }else if (3 == $artData['restric_type']) { // 会员付费
            $level_value = Db::name('users_level')->where('level_id',$this->users_level)->value('level_value');
            if ($level_value >= $artData['arc_level_value']){
                // 查询是否已购买
                $where = [
                    'order_status' => 1,
                    'product_id' => intval($aid),
                    'users_id' => $this->users_id
                ];
                $count = Db::name('download_order')->where($where)->count();
                if (!empty($count)){
                    $canDownload = 1;
                }
            }else{
                $buyVip = 1;
            }
        }

        if (1 == $canDownload){
            $result['buyId'] = " style='display:none;' id='download_buy_v866225' ";
            $result['downloadId'] = "id='download_v866225' ";
        }else{
            $result['buyId'] = " id='download_buy_v866225' ";
            $result['downloadId'] = " style='display:none;' id='download_v866225' ";
        }

        if (1 == $buyVip){
            $result['onclick'] = ' href="javascript:void(0);" id="buy_button_v866225" onclick="BuyVipClick();" ';
        }else{
            if (isMobile()) {
                $result['onclick'] = ' href="javascript:void(0);" id="buy_button_v866225" onclick="ey_download_v866225(' . $aid . ');" ';//第一种跳转页面支付
            } else {
                $result['onclick'] = ' href="javascript:void(0);" id="buy_button_v866225" onclick="DownloadBuyNow1655866225(' . $aid . ');" ';//第二种弹框页支付
            }
        }

        $version = getCmsVersion();
        $buy_url = ROOT_DIR . "/index.php?m=user&c=Download&a=buy";
        $get_download_url = ROOT_DIR . "/index.php?m=api&c=Ajax&a=get_download";
        $buy_vip_url = url('user/Level/level_centre', ['aid'=>$aid]);
        $srcurl = get_absolute_url("{$this->root_dir}/public/static/common/js/tag_downloadpay.js?v={$version}");
        $result['downloadTipsId'] = " style='display:none;' id='download_tips_v866225' ";
        $result['hidden'] = <<<EOF
<script type="text/javascript">
    var buy_url_v866225 = '{$buy_url}';
    var aid_v866225 = {$aid};
    var root_dir_v866225 = '{$this->root_dir}';
    var buy_vip_url_v866225 = '{$buy_vip_url}';
</script>
<script language="javascript" type="text/javascript" src="{$srcurl}"></script>
<script type="text/javascript">
    ey_ajax_get_download_v866225({$aid},'{$get_download_url}');
</script>
EOF;
        return $result;
    }
}