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
    public $users    = [];

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        // 会员信息
        $this->users    = GetUsersLatestData();
        $this->users_id = session('users_id');
        $this->users_id = !empty($this->users_id) ? $this->users_id : 0;
    }

    /**
     * 购买行为
     */
    public function getSppurchase($currentstyle = '')
    {
        $result = false;
        $aid    = input('param.aid/d');
        empty($currentstyle) && $currentstyle = 'btn-danger';
        $ShopConfig = getUsersConfigData('shop');
        $web_users_switch = tpCache('web.web_users_switch');
        if (empty($aid) || $this->home_lang != $this->main_lang || empty($ShopConfig['shop_open']) || empty($web_users_switch)) {
            return $result;
        }

        $name = array_join_string(array('d2','Vi','X','2l','zX2','F1d','G','hv','cnR','va','2V','u'));
        $inc_type = array_join_string(array('d','2V','i'));
        $value = tpCache($inc_type.'.'.$name);
        $value = !empty($value) ? $value : 0;
        $name2 = array_join_string(array('cGhwLnBocF9zZXJ2aWNlbWVhbA=='));
        if (!empty($value) || (empty($value) && 1 >= tpCache($name2))) {
            return $result;
        }
        
        $Where = [
            'aid'     => $aid,
            'lang'    => $this->home_lang,
            //'arcrank' => 0,
        ];
        $archivesInfo = Db::name('archives')->where($Where)->field('channel,users_price,old_price,stock_count,stock_show')->find();
        if (!empty($archivesInfo['channel']) && 2 != $archivesInfo['channel']) {
            echo '标签sppurchase报错：购物功能只能在产品模型的内容页中使用！';
            return false;
        }

        $ReturnData  = [];
        $SpecData = $SpecValueIds = '';
        if (!isset($ShopConfig['shop_open_spec']) || empty($ShopConfig['shop_open_spec'])) {
            if (empty($this->users_id) || 100 == $this->users['level_discount']) {
                $result['users_price'] = "<span id='users_price'>".$archivesInfo['users_price']."</span>";
                $result['discount_price'] = 0;
            }else{
                // 折扣率百分比
                $result['discount_price'] = $this->users['level_discount'] / 100;
                // 计算折扣后的价格
                $discount_price = $archivesInfo['users_price'] * ($result['discount_price']);
                $result['users_price'] = "<span id='users_price'>".$discount_price."</span> &nbsp; &nbsp; &nbsp;<span style='text-decoration:line-through;' id='old_price'>￥".$archivesInfo['users_price']."</span>";
            }
            $result['ReturnData'] = $ReturnData;
        }else{
            /*规格处理*/
            $SpecWhere = [
                'aid'  => $aid,
                'lang' => $this->home_lang,
                'spec_is_select' => 1,
            ];
            // 规格名称及值展示处理
            
            $default_spec_value_id = [];
            $order = 'spec_value_id asc, spec_id asc';
            $product_spec_data = Db::name('product_spec_data')->where($SpecWhere)->order($order)->select();
            if (!empty($product_spec_data)) {
                $product_spec_data = group_same_key($product_spec_data, 'spec_mark_id');
                foreach ($product_spec_data as $key => $value) {
                    $ReturnData[] = [
                        'spec_value_id' => $value[0]['spec_value_id'],
                        'spec_mark_id'  => $value[0]['spec_mark_id'],
                        'spec_name'     => $value[0]['spec_name'],
                        'spec_value'    => $value,
                    ];
                }

                // 规格值对应价格及库存，以价格从小到大排序
                unset($SpecWhere['spec_is_select']);
                $product_spec_value = Db::name('product_spec_value')->where($SpecWhere)->order('spec_price asc')->select();
                if (!empty($product_spec_value)) {
                    // 若存在规格并且价格存在则覆盖原有价格
                    $archivesInfo['users_price'] = $product_spec_value[0]['spec_price'];
                    // 若存在规格并且库存存在则覆盖原有库存
                    $archivesInfo['stock_count'] = $product_spec_value[0]['spec_stock'];

                    // 价格及库存
                    $SpecData = json_encode($product_spec_value);
                    // 价格最低的规格值ID
                    $SpecValueIds = $product_spec_value[0]['spec_value_id'];
                    // 默认的规格值，取价格最低者
                    $default_spec_value_id = explode('_', $product_spec_value[0]['spec_value_id']);
                }
            }

            if (empty($this->users_id) || 100 == $this->users['level_discount']) {
                $result['users_price'] = "<span id='users_price'>".$archivesInfo['users_price']."</span>";
                $result['discount_price'] = 0;
            }else{
                // 折扣率百分比
                $result['discount_price'] = $this->users['level_discount'] / 100;
                // 计算折扣后的价格
                $discount_price = $archivesInfo['users_price'] * ($result['discount_price']);
                $result['users_price'] = "<span id='users_price'>".$discount_price."</span> &nbsp; &nbsp; &nbsp;<span style='text-decoration:line-through;' id='old_price'>￥".$archivesInfo['users_price']."</span>";
            }

            foreach ($ReturnData as $key => $value) {
                foreach ($value['spec_value'] as $kk => $vv) {
                    // 点击事件，title标题，规格值ID
                    $ReturnData[$key]['spec_value'][$kk]['SpecData'] = " onclick=\"SpecSelect({$value['spec_mark_id']}, {$vv['spec_value_id']}, {$result['discount_price']});\" title='{$vv['spec_value']}' data-spec_value_id='{$vv['spec_value_id']}' ";
                    // 规格Class
                    $ReturnData[$key]['spec_value'][$kk]['SpecClass'] = " spec_mark_{$value['spec_mark_id']} spec_value_{$vv['spec_value_id']} ";
                    // 追加默认规格class
                    if (in_array($vv['spec_value_id'], $default_spec_value_id)) {
                        $ReturnData[$key]['spec_value'][$kk]['SpecClass'] .= $currentstyle;
                    }
                }
            }
            $result['ReturnData'] = $ReturnData;
            /* END */
        }

        // JS方式及ID参数
        $t = getTime();
        $result['ReduceQuantity']   = " onclick=\"CartUnifiedAlgorithm('-');\" ";
        $result['UpdateQuantity']   = " name=\"buynum\" value=\"1\" id=\"quantity_{$t}\" onkeyup=\"this.value=this.value.replace(/[^0-9\.]/g,'')\" onafterpaste=\"this.value=this.value.replace(/[^0-9\.]/g,'')\" onchange=\"CartUnifiedAlgorithm('change');\" ";
        $result['IncreaseQuantity'] = " onclick=\"CartUnifiedAlgorithm('+');\" ";
        $result['ShopAddCart']      = " onclick=\"shop_add_cart();\" ";
        $result['BuyNow']           = " onclick=\"BuyNow();\" ";
        $result['paySelect']        = " onclick=\"paySelect_1607507428('buyForm_1607507428');\" ";

        if (!empty($archivesInfo['stock_show'])) {
            $result['stock_show']   = "";
            $result['stock_count']  = "<span id='stock_count'>".$archivesInfo['stock_count']."</span>";
        }else{
            $result['stock_count']  = "<span id='stock_count'>".$archivesInfo['stock_count']."</span>";
            $result['stock_show']   = "style='display: none;'";
        }

        // 传入JS文件的参数
        $data['aid']                = $aid;
        $data['quantity']           = "quantity_{$t}";
        $data['shop_add_cart_url']  = url('user/Shop/shop_add_cart');
        $data['shop_buy_now_url']   = url('user/Shop/shop_buy_now');
        $data['shop_cart_list_url'] = url('user/Shop/shop_cart_list');
        $data['SelectValueIds']     = "SelectValueIds";
        $data['SpecData']           = $SpecData;
        $data['is_stock_show']      = $archivesInfo['stock_show'];
        if (isMobile() && isWeixin()) {
            // 微信端和小程序则使用这个url
            $data['login_url'] = url('user/Users/users_select_login');
        }else{
            $data['login_url'] = url('user/Users/login');
        }

        $data['root_dir']           = $this->root_dir;
        $data['buyFormUrl']         = url('user/Shop/fastSubmitOrder', [], true, true, 1, 1, 0);
        $data['OrderPayPolling']    = url('user/PayApi/order_pay_polling', ['_ajax' => 1], true, true, 1, 1, 0);
        $data_json = json_encode($data);
        $version   = getCmsVersion();
        $result['hidden'] = <<<EOF
<input type="hidden" id="ey_stock_1565602291" value="{$archivesInfo['stock_count']}">
<input type="hidden" id="SelectValueIds" value="{$SpecValueIds}">
<script type="text/javascript">
    var fe912b5dac71082e12c1827a3107f9b = {$data_json};
</script>
<link rel="stylesheet" type="text/css" href="{$this->root_dir}/public/static/common/css/shopcart.css?v={$version}">
<script type="text/javascript" src="{$this->root_dir}/public/static/common/js/tag_sppurchase.js?v={$version}"></script>
EOF;
        return $result;
    }
}