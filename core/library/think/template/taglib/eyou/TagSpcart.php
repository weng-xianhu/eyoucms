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
 * Date: 2019-4-13
 */

namespace think\template\taglib\eyou;

/**
 * 购物车列表
 */
class TagSpcart extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取购物车数据
     */
    public function getSpcart($limit = '')
    {
        // 查询条件
        $condition = [
            'a.users_id' => session('users_id'),
            'a.lang'     => $this->home_lang,
            'b.arcrank'  => array('egt','0'),  // 带审核稿件不查询(同等伪删除)
        ];

        $list = M("shop_cart")->field('a.*,b.title,b.litpic,b.users_price')
            ->alias('a')
            ->join('__ARCHIVES__ b', 'a.product_id = b.aid', 'LEFT')
            ->where($condition)
            ->limit($limit)
            ->order('a.add_time desc')
            ->select();

        if (empty($list)) { return false; }

        // 订单数据处理
        $result = [
            'TotalAmount' => 0,
            'TotalNumber' => 0,
            'AllSelected' => 0,
        ];
        $selected = 0;

        $controller_name = 'Product';
        $array_new = get_archives_data($list,'product_id');

        foreach ($list as $key => $value) {
            if (!empty($value['users_price']) && !empty($value['product_num'])) {
                // 计算小计
                $list[$key]['subtotal'] = $value['users_price'] * $value['product_num'];
                $list[$key]['subtotal'] = sprintf("%.2f", $list[$key]['subtotal']);
                // 计算购物车中已勾选的产品总数和总额
                if (!empty($value['selected'])) {
                    // 合计金额
                    $result['TotalAmount'] += $list[$key]['subtotal'];
                    $result['TotalAmount'] = sprintf("%.2f", $result['TotalAmount']);
                    // 合计数量
                    $result['TotalNumber'] += $value['product_num'];
                    // 选中的产品个数
                    $selected++;
                }
            }

            // 产品内页地址
            $list[$key]['arcurl'] = urldecode(arcurl('home/'.$controller_name.'/view', $array_new[$value['product_id']]));
            
            // 图片处理
            $list[$key]['litpic'] = handle_subdir_pic(get_default_pic($value['litpic']));

            // 产品属性处理
            $list[$key]['attr_value'] = '';
            if (!empty($value['product_id'])) {
                $attrData = M("product_attr")->where('aid',$value['product_id'])->field('attr_value,attr_id')->select();
                $attr_value = '';
                foreach ($attrData as $val) {
                    $attr_name = M("product_attribute")->where('attr_id',$val['attr_id'])->field('attr_name')->find();
                    $attr_value .= $attr_name['attr_name'].'：'.$val['attr_value'].'<br/>';
                    $list[$key]['attr_value'] = $attr_value;
                }
            }

            $list[$key]['CartChecked'] = " name=\"ey_buynum\" id=\"{$value['cart_id']}_checked\" cart-id=\"{$value['cart_id']}\" product-id=\"{$value['product_id']}\" onclick=\"Checked('{$value['cart_id']}','{$value['selected']}');\" ";
            $list[$key]['hidden']   = <<<EOF
<input type="hidden" id="{$value['cart_id']}_Selected" value="{$value['selected']}">
<script type="text/javascript"> 
$(function(){
    if ('1' == $('#'+{$value['cart_id']}+'_Selected').val()) {
        $('#'+{$value['cart_id']}+'_checked').prop('checked','true');
    }
}); 
</script>
EOF;
            $list[$key]['ReduceQuantity'] = " onclick=\"CartUnifiedAlgorithm('{$value['product_id']}','-','{$value['selected']}');\" ";
            $list[$key]['UpdateQuantity'] = " onkeyup=\"this.value=this.value.replace(/[^0-9\.]/g,'')\" onafterpaste=\"this.value=this.value.replace(/[^0-9\.]/g,'')\"  onchange=\"CartUnifiedAlgorithm('{$value['product_id']}','change','{$value['selected']}');\" value=\"{$value['product_num']}\" id=\"{$value['product_id']}_num\" ";
            $list[$key]['IncreaseQuantity'] = " onclick=\"CartUnifiedAlgorithm('{$value['product_id']}','+','{$value['selected']}');\" ";
            $list[$key]['ProductId']     = " id=\"{$value['cart_id']}_product\" ";
            $list[$key]['SubTotalId']    = " id=\"{$value['product_id']}_subtotal\" ";
            $list[$key]['UsersPriceId']  = " id=\"{$value['product_id']}_price\" ";
            $list[$key]['CartDel']       = " onclick=\"CartDel('{$value['cart_id']}','{$value['title']}');\" ";

        }
        
        $result['list'] = $list;
        
        // 是否购物车的产品全部选中
        $listcount = count($list);
        if ($listcount == $selected) {
            $result['AllSelected'] = '1';
        }

        // 下单地址
        $result['ShopOrderUrl']  = urldecode(url('user/Shop/shop_under_order'));
        $result['InputChecked']  = " id=\"AllChecked\" onclick=\"Checked('*','{$result['AllSelected']}');\" ";
        $result['InputHidden']   = " <input type=\"hidden\" id=\"AllSelected\" value='{$result['AllSelected']}'> ";
        $result['TotalNumberId'] = " id=\"TotalNumber\" ";
        $result['TotalAmountId'] = " id=\"TotalAmount\" ";
         
        // 传入JS文件的参数
        $data['cart_unified_algorithm_url'] = url('user/Shop/cart_unified_algorithm');
        $data['cart_checked_url']           = url('user/Shop/cart_checked');
        $data['cart_del_url']               = url('user/Shop/cart_del');
        $data_json = json_encode($data);
        $version = getCmsVersion();
        $result['hidden'] = <<<EOF
<script type="text/javascript">
    var b82ac06cf24687eba9bc5a7ba92be4c8 = {$data_json};
</script>
<script type="text/javascript" src="{$this->root_dir}/public/static/common/js/tag_spcart.js?v={$version}"></script>
EOF;
        return $result;
    }
}