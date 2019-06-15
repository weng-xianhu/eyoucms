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

use think\Config;
use think\Request;
use think\Db;

/**
 * 提交订单
 */
class TagSpsubmitorder extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取提交订单数据
     */
    public function getSpsubmitorder()
    {
        // 获取解析数据
        $querystr   = input('param.querystr/s');
        $querydata  = unserialize(base64_decode($querystr));
        $aid = $querydata['aid'];
        $num = $querydata['product_num'];
        if (!empty($aid)) {
            if ($num >= '1') {
                // 立即购买查询条件
                $ArchivesWhere = [
                    'aid'  => $aid,
                    'lang' => $this->home_lang,
                ]; 
                $result['list'] = Db::name('archives')->field('aid,title,litpic,users_price,prom_type')->where($ArchivesWhere)->select();
                $result['list'][0]['product_num'] = $num;
                $submit_order_type = '1';
                // 加密不允许更改的数据值
                $aid  = base64_encode(serialize($aid));
                $num  = base64_encode(serialize($num));
                $type = base64_encode(serialize('1'));// 1表示直接下单购买，不走购物车
                $result['list'][0]['ProductHidden'] = '<input type="hidden" name="aid" value="'.$aid.'"> <input type="hidden" name="num" value="'.$num.'"> <input type="hidden" name="type" value="'.$type.'">';
            }else{
                action('user/Shop/shop_under_order', false);
                exit;
            }
        }else{
            // 购物车查询条件
            $CartWhere = [
                'a.users_id' => session('users_id'),
                'a.lang'     => $this->home_lang,
                'a.selected' => 1,
            ];
            $result['list'] = Db::name('shop_cart')->field('a.*,b.aid,b.title,b.litpic,b.users_price,b.prom_type')
                ->alias('a') 
                ->join('__ARCHIVES__ b', 'a.product_id = b.aid', 'LEFT')
                ->where($CartWhere)
                ->order('a.add_time desc')
                ->select();
            $submit_order_type = '0';
        }

        // 如果产品数据为空则调用商城控制器的方式返回提示,中止运行
        if (empty($result['list'])) {
            action('user/Shop/shop_under_order', false);
            exit;
        }

        $controller_name = 'Product';
        $array_new = get_archives_data($result['list'],'aid');

        // 获取商城配置信息
        $ConfigData = getUsersConfigData('shop');
        $result['data'] = [
            // 温馨提示内容,为空则不展示
            'shop_prompt'        => !empty($ConfigData['shop_prompt']) ? $ConfigData['shop_prompt'] : '',
            // 是否开启线下支付(货到付款)
            'shop_open_offline'  => !empty($ConfigData['shop_open_offline']) ? $ConfigData['shop_open_offline'] : 0,
            // 是否开启运费设置
            'shop_open_shipping' => !empty($ConfigData['shop_open_shipping']) ? $ConfigData['shop_open_shipping'] : 0,
            // 初始化总额
            'TotalAmount'        => 0,
            // 初始化总数
            'TotalNumber'        => 0,
            // 提交来源:0购物车;1直接下单
            'submit_order_type'  => $submit_order_type,
            // 1表示为虚拟订单
            'PromType'           => 1,
        ];

        // 产品数据处理
        foreach ($result['list'] as $key => $value) {
            if (!empty($value['users_price']) && !empty($value['product_num'])) {
                // 计算小计
                $result['list'][$key]['subtotal'] = sprintf("%.2f", $value['users_price'] * $value['product_num']);
                // 计算合计金额
                $result['data']['TotalAmount']    += $result['list'][$key]['subtotal'];
                $result['data']['TotalAmount']    = sprintf("%.2f", $result['data']['TotalAmount']);
                // 计算合计数量
                $result['data']['TotalNumber']    += $value['product_num'];
                // 判断订单类型，目前逻辑：一个订单中，只要存在一个普通产品(实物产品，需要发货物流)，则为普通订单
                // 0表示为普通订单，1表示为虚拟订单，虚拟订单无需发货物流，无需选择收货地址，无需计算运费
                if (empty($value['prom_type'])) {
                    $result['data']['PromType'] = '0';
                }
            }

            // 产品页面链接
            $result['list'][$key]['arcurl'] = urldecode(arcurl('home/'.$controller_name.'/view', $array_new[$value['aid']]));

            // 图片处理
            $result['list'][$key]['litpic'] = handle_subdir_pic(get_default_pic($value['litpic']));
            
            // 产品属性处理
            if (!empty($value['aid'])) { 
                $attrData   = Db::name('product_attr')->where('aid',$value['aid'])->field('attr_value,attr_id')->select();
                $attr_value = '';
                foreach ($attrData as $val) {
                    $attr_name  = Db::name('product_attribute')->where('attr_id',$val['attr_id'])->field('attr_name')->find();
                    $attr_value .= $attr_name['attr_name'].'：'.$val['attr_value'].'<br/>';
                    $result['list'][$key]['attr_value'] = $attr_value;
                }
            }

            // 购物车不需要这个产品,若不存在空则定义为空,避免报错
            if (empty($result['list'][$key]['ProductHidden'])) {
                $result['list'][$key]['ProductHidden'] = '';
            }
        }

        // 封装初始金额隐藏域
        $result['data']['TotalAmountOld'] = '<input type="hidden" id="TotalAmount_old" value="'.$result['data']['TotalAmount'].'">';
        // 封装订单支付方式隐藏域
        $result['data']['PayTypeHidden']  = '<input type="hidden" name="payment_method" id="payment_method" value="0">';
        // 封装添加收货地址JS
        if (isWeixin() && !isWeixinApplets()) {
            $result['data']['ShopAddAddr'] = " onclick=\"GetWeChatAddr();\" ";
            $data['shop_add_address']        = url('user/Shop/shop_get_wechat_addr');
        }else{
            $result['data']['ShopAddAddr']  = " onclick=\"ShopAddAddress();\" ";
            $data['shop_add_address']       = url('user/Shop/shop_add_address');
        }

        // 封装UL的ID,用于添加收货地址
        $result['data']['UlHtmlId']       = " id=\"UlHtml\" ";
        // 封装选择支付方式JS
        $result['data']['OnlinePay']      = " onclick=\"ColorS('zxzf')\" id=\"zxzf\"  ";
        $result['data']['DeliveryPay']    = " onclick=\"ColorS('hdfk')\" id=\"hdfk\"  ";
        // 封装运费信息
        if (empty($result['data']['shop_open_shipping'])) {
            $result['data']['Shipping'] = " 免运费 ";
        }else{
            $result['data']['Shipping'] = " <span id=\"template_money\">￥0.00</span> ";
        }
        // 封装全部产品总额ID,用于计算总额
        $result['data']['TotalAmountId'] = " id=\"TotalAmount\" ";
        // 封装返回购物车链接
        $result['data']['ReturnCartUrl'] = url('user/Shop/shop_cart_list');
        // 封装提交订单JS
        $result['data']['ShopPaymentPage'] = " onclick=\"ShopPaymentPage();\" ";
        // 封装表单验证隐藏域
        static $request = null;
        if (null == $request) { $request = Request::instance(); }  
        $token = $request->token();
        $result['data']['TokenValue'] = " <input type=\"hidden\" name=\"__token__\" value=\"{$token}\"/> ";

        // 传入JS参数
        $data['shop_edit_address'] = url('user/Shop/shop_edit_address');
        $data['shop_del_address']  = url('user/Shop/shop_del_address');
        $data['shop_inquiry_shipping']  = url('user/Shop/shop_inquiry_shipping');
        $data['shop_payment_page'] = url('user/Shop/shop_payment_page');
        if (isWeixin() || isMobile()) {
            $data['addr_width']  = '100%';
            $data['addr_height'] = '100%';
        }else{
            $data['addr_width']  = '350px';
            $data['addr_height'] = '550px';
        }
        $data_json = json_encode($data);
        $version   = getCmsVersion();
        // 循环中第一个数据带上JS代码加载
        $result['data']['hidden'] = <<<EOF
<script type="text/javascript">
    var b1decefec6b39feb3be1064e27be2a9 = {$data_json};
</script>
<script type="text/javascript" src="{$this->root_dir}/public/static/common/js/tag_spsubmitorder.js?v={$version}"></script>
EOF;
        return $result;
    }
}