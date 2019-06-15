<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 陈风任 <491085389@qq.com>
 * Date: 2019-4-25
 */

namespace think\template\taglib\eyou;
use think\Db;

/**
 * 购买行为
 */
class TagSppurchase extends Base
{
    /**
     * 会员ID
     */
    public $users_id = 0;

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        // 会员信息
        $this->users_id = session('users_id');
        $this->users_id = !empty($this->users_id) ? $this->users_id : 0;
    }

    /**
     * 购买行为
     */
    public function getSppurchase()
    {
        $result = false;
        $aid    = input('param.aid/d');
        $shop_open = getUsersConfigData('shop.shop_open');
        $web_users_switch = tpCache('web.web_users_switch');
        if (empty($aid) || $this->home_lang != $this->main_lang || empty($shop_open) || empty($web_users_switch)) {
            return $result;
        }

        $name = array_join_string(array('d2','Vi','X','2l','zX2','F1d','G','hv','cnR','va','2V','u'));
        $inc_type = array_join_string(array('d','2V','i'));
        $value = tpCache($inc_type.'.'.$name);
        $value = !empty($value) ? $value : 0;
        if (is_realdomain() && !empty($value)) {
            return $result;
        }
        
        $Where = [
            'aid'     => $aid,
            'lang'    => $this->home_lang,
            'arcrank' => 0,
        ];
        $archivesInfo = Db::name('archives')->where($Where)->field('channel,users_price')->find();
        
        if (!empty($archivesInfo['channel']) && 2 != $archivesInfo['channel']) {
            echo '标签sppurchase报错：购物功能只能在产品模型的内容页中使用！';
            return false;
        }

        // JS方式及ID参数
        $t = getTime();
        $result['ReduceQuantity']   = " onclick=\"CartUnifiedAlgorithm('-');\" ";
        $result['UpdateQuantity']   = " name=\"buynum\" value=\"1\" id=\"quantity_{$t}\" onkeyup=\"this.value=this.value.replace(/[^0-9\.]/g,'')\" onafterpaste=\"this.value=this.value.replace(/[^0-9\.]/g,'')\" onchange=\"CartUnifiedAlgorithm('change');\" ";
        $result['IncreaseQuantity'] = " onclick=\"CartUnifiedAlgorithm('+');\" ";
        $result['ShopAddCart']      = " onclick=\"shop_add_cart();\" ";
        $result['BuyNow']           = " onclick=\"BuyNow();\" ";
        $result['users_price']      = $archivesInfo['users_price'];

        // 传入JS文件的参数
        $data['aid']                 = $aid;
        $data['quantity']            = "quantity_{$t}";
        $data['shop_add_cart_url']   = url('user/Shop/shop_add_cart');
        $data['shop_buy_now_url']    = url('user/Shop/shop_buy_now');
        $data['shop_cart_list_url']  = url('user/Shop/shop_cart_list');
        if (isMobile() && isWeixin()) {
            // 微信端和小程序则使用这个url
            $data['login_url'] = url('user/Users/users_select_login');
        }else{
            $data['login_url'] = url('user/Users/login');
        }

        $data_json = json_encode($data);
        $version   = getCmsVersion();
        $result['hidden'] = <<<EOF
<script type="text/javascript">
    var fe912b5dac71082e12c1827a3107f9b = {$data_json};
</script>
<link rel="stylesheet" type="text/css" href="{$this->root_dir}/public/static/common/css/shopcart.css?v={$version}">
<script type="text/javascript" src="{$this->root_dir}/public/static/common/js/tag_sppurchase.js?v={$version}"></script>
EOF;
        return $result;
    }
}