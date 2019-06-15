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
use think\Db;

/**
 * 订单明细
 */
class TagSporder extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取订单明细数据
     */
    public function getSporder($order_id)
    {
        if (empty($order_id)) {
            $order_id = input('param.order_id');
        }

        if (!empty($order_id)) {
            // 公共条件
            $Where = [
                'order_id' => $order_id,
                'users_id' => session('users_id'),
                'lang'     => $this->home_lang,
            ];

            // 订单主表
            $result['OrderData'] = Db::name("shop_order")->field('*')->where($Where)->find();
            // 获取当前链接及参数，用于手机端查询快递时返回页面
            $ReturnUrl = request()->url(true);
            // 封装查询物流链接
            $result['OrderData']['LogisticsInquiry'] = $MobileExpressUrl = '';
            if (('2' == $result['OrderData']['order_status'] || '3' == $result['OrderData']['order_status']) && empty($result['OrderData']['prom_type'])) {
                // 移动端查询物流链接
                $result['OrderData']['MobileExpressUrl'] = "//m.kuaidi100.com/app/query/?com=".$result['OrderData']['express_code']."&nu=".$result['OrderData']['express_order']."&callbackurl=".$ReturnUrl;

                $MobileExpressUrl = "//m.kuaidi100.com/index_all.html?type=".$result['OrderData']['express_code']."&postid=".$result['OrderData']['express_order']."&callbackurl=".$ReturnUrl;

                $result['OrderData']['LogisticsInquiry'] = " onclick=\"LogisticsInquiry('{$MobileExpressUrl}');\" ";
            }
            // 是否移动端，1表示移动端，0表示PC端
            $result['OrderData']['IsMobile'] = isMobile() ? 1 : 0;
            
            // 获取订单状态列表
            $order_status_arr = Config::get('global.order_status_arr');
            $result['OrderData']['order_status_name'] = $order_status_arr[$result['OrderData']['order_status']];

            $result['OrderData']['TotalAmount'] = '0';

            
            if (!empty($result['OrderData'])) {
                // 订单明细表
                $result['DetailsData'] = Db::name("shop_order_details")->field('*')->where($Where)->select();

                $controller_name = 'Product';
                $array_new = get_archives_data($result['DetailsData'],'product_id');
                
                // 产品处理
                foreach ($result['DetailsData'] as $key => $value) {
                    // 产品属性处理
                    $value['data'] = unserialize($value['data']);
                    $attr_value    = htmlspecialchars_decode($value['data']['attr_value']);
                    $attr_value    = htmlspecialchars_decode($attr_value);
                    $result['DetailsData'][$key]['data']     = $attr_value;
                    
                    // 产品内页地址
                    $result['DetailsData'][$key]['arcurl']   = urldecode(arcurl('home/'.$controller_name.'/view', $array_new[$value['product_id']]));

                    // 图片处理
                    $result['DetailsData'][$key]['litpic'] = handle_subdir_pic(get_default_pic($value['litpic']));

                    // 小计
                    $result['DetailsData'][$key]['subtotal'] = $value['product_price'] * $value['num'];
                    $result['DetailsData'][$key]['subtotal'] = sprintf("%.2f", $result['DetailsData'][$key]['subtotal']);
                    // 合计金额
                    $result['OrderData']['TotalAmount'] += $result['DetailsData'][$key]['subtotal'];
                    $result['OrderData']['TotalAmount'] = sprintf("%.2f", $result['OrderData']['TotalAmount']);
                }

                if (empty($result['OrderData']['order_status'])) {
                    // 付款地址处理，对ID和订单号加密，拼装url路径
                    $querydata = [
                        'order_id'   => $result['OrderData']['order_id'],
                        'order_code' => $result['OrderData']['order_code'],
                    ];
                    $querystr   = base64_encode(serialize($querydata));
                    $result['OrderData']['PaymentUrl'] = urldecode(url('user/Pay/pay_recharge_detail',['querystr'=>$querystr]));
                }

                // 处理订单主表的地址数据
                $result['OrderData']['country']  = '中国';
                $result['OrderData']['province'] = get_province_name($result['OrderData']['province']);
                $result['OrderData']['city']     = get_city_name($result['OrderData']['city']);
                $result['OrderData']['district'] = get_area_name($result['OrderData']['district']);
                
                // 封装获取订单支付方式名称
                $pay_method_arr = Config::get('global.pay_method_arr');
                if (!empty($result['OrderData']['payment_method'])) {
                    $result['OrderData']['pay_name'] = '货到付款（ 快递代收 ）';
                }else{
                    $pay_name = '未支付';
                    if (!empty($result['OrderData']['pay_name'])){
                        $pay_name = $pay_method_arr[$result['OrderData']['pay_name']];
                    }
                    $result['OrderData']['pay_name'] = '在线支付（ '.$pay_name.' ）';
                }
                // 封装取消订单JS
                $result['OrderData']['CancelOrder']   = " onclick=\"CancelOrder('{$order_id}');\" ";
                // 封装收货地址
                $result['OrderData']['ConsigneeInfo'] = $result['OrderData']['consignee'].' '.$result['OrderData']['mobile'].' '.$result['OrderData']['country'].' '.$result['OrderData']['province'].' '.$result['OrderData']['city'].' '.$result['OrderData']['district'].' '.$result['OrderData']['address'];

                // 传入JS参数
                $data['shop_order_cancel'] = url('user/Shop/shop_order_cancel');
                $data_json = json_encode($data);
                $version   = getCmsVersion();
                // 循环中第一个数据带上JS代码加载
                $result['OrderData']['hidden'] = <<<EOF
<script type="text/javascript">
    var eeb8a85ee533f74014310e0c0d12778 = {$data_json};
</script>
<script type="text/javascript" src="{$this->root_dir}/public/static/common/js/tag_sporder.js?v={$version}"></script>
EOF;
                return $result;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}