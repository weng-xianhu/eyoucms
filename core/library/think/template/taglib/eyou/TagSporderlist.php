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
 * 订单列表
 */
class TagSporderlist extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取订单列表数据
     */
    public function getSporderlist($pagesize = '10')
    {
        // 基础查询条件
        $OrderWhere = [
            'users_id' => session('users_id'),
            'lang'     => $this->home_lang,
        ];

        // 应用搜索条件
        $keywords = input('param.keywords/s');
        if (!empty($keywords)) {
            $OrderWhere['order_code'] =  ['LIKE', "%{$keywords}%"];
        }

        // 订单状态搜索
        $select_status = input('param.select_status');
        if (!empty($select_status)) {
            if ('daifukuan' === $select_status) {
                $select_status = 0;
            }
            
            $OrderWhere['order_status'] = $select_status;
        }

        $query_get = input('get.');
        $paginate_type = 'userseyou';
        if (isMobile()) {
            $paginate_type = 'usersmobile';
        }
        $paginate = array(
            'type'     => $paginate_type,
            'var_page' => config('paginate.var_page'),
            'query'    => $query_get,
        );

        $pages = Db::name('shop_order')
            ->field("*")
            ->where($OrderWhere)
            ->order('add_time desc')
            ->paginate($pagesize, false, $paginate);

        $result['list']  = $pages->items();
        $result['pages'] = $pages;

        // 搜索名称时，查询订单明细表商品名称
        if (empty($result['list']) && !empty($keywords)) {
            $Data = model('Shop')->QueryOrderList($pagesize,session('users_id'),$keywords,$query_get);
            $result['list']  = $Data['list'];
            $result['pages'] = $Data['pages'];
        }

        if (!empty($result['list'])) {
            // 订单数据处理
            $controller_name = 'Product';
            // 获取当前链接及参数，用于手机端查询快递时返回页面
            $ReturnUrl = request()->url(true);
            foreach ($result['list'] as $key => $value) {
                $DetailsWhere['users_id'] = $value['users_id'];
                $DetailsWhere['order_id'] = $value['order_id'];
                // 查询订单明细表数据
                $result['list'][$key]['details'] = Db::name('shop_order_details')->field('*')->where($DetailsWhere)->select();

                $array_new = get_archives_data($result['list'][$key]['details'],'product_id');

                foreach ($result['list'][$key]['details'] as $kk => $vv) {
                    // 产品属性处理
                    $vv['data'] = unserialize($vv['data']);
                    $attr_value = htmlspecialchars_decode($vv['data']['attr_value']);
                    $attr_value = htmlspecialchars_decode($attr_value);
                    $result['list'][$key]['details'][$kk]['data'] = $attr_value;

                    // 产品内页地址
                    $result['list'][$key]['details'][$kk]['arcurl'] = urldecode(arcurl('home/'.$controller_name.'/view', $array_new[$vv['product_id']]));

                    // 图片处理
                    $result['list'][$key]['details'][$kk]['litpic'] = handle_subdir_pic(get_default_pic($vv['litpic']));
                }

                if (empty($value['order_status'])) {
                    // 付款地址处理，对ID和订单号加密，拼装url路径
                    $querydata = [
                        'order_id'   => $value['order_id'],
                        'order_code' => $value['order_code'],
                    ];
                    $querystr   = base64_encode(serialize($querydata));
                    $result['list'][$key]['PaymentUrl'] = urldecode(url('user/Pay/pay_recharge_detail',['querystr'=>$querystr]));
                }

                // 获取订单状态
                $order_status_arr = Config::get('global.order_status_arr');
                $result['list'][$key]['order_status_name'] = $order_status_arr[$value['order_status']];

                // 获取订单方式名称
                $pay_method_arr = Config::get('global.pay_method_arr');
                if (!empty($value['payment_method']) && !empty($value['pay_name'])) {
                    $result['list'][$key]['pay_name'] = $pay_method_arr[$value['pay_name']];
                }else{
                    if (!empty($value['pay_name'])) {
                        $result['list'][$key]['pay_name'] = $pay_method_arr[$value['pay_name']];
                    }else{
                        $result['list'][$key]['pay_name'] = '在线支付';
                    }
                }

                // 封装订单查询详情链接
                $result['list'][$key]['OrderDetailsUrl'] = urldecode(url('user/Shop/shop_order_details',['order_id'=>$value['order_id']]));

                // 封装订单催发货JS
                $result['list'][$key]['OrderRemind'] = " onclick=\"OrderRemind('{$value['order_id']}','{$value['order_code']}');\" ";
                 
                // 封装确认收货JS
                $result['list'][$key]['Confirm'] = " onclick=\"Confirm('{$value['order_id']}','{$value['order_code']}');\" ";

                // 封装查询物流链接
                $result['list'][$key]['LogisticsInquiry'] = $MobileExpressUrl = '';
                if (('2' == $value['order_status'] || '3' == $value['order_status']) && empty($value['prom_type'])) {
                    // 物流查询接口
                    $ExpressUrl = "https://m.kuaidi100.com/index_all.html?type=".$value['express_code']."&postid=".$value['express_order']."&callbackurl=".$ReturnUrl;
                    // 微信端、小程序使用跳转方式进行物流查询
                    $result['list'][$key]['MobileExpressUrl'] = $ExpressUrl;
                    // PC端，手机浏览器使用弹框方式进行物流查询
                    $result['list'][$key]['LogisticsInquiry'] = " onclick=\"LogisticsInquiry('{$ExpressUrl}');\" ";
                }

                // 默认为空
                $result['list'][$key]['hidden'] = '';
            }

            // 传入JS参数
            $data['shop_member_confirm'] = url('user/Shop/shop_member_confirm');
            $data['shop_order_remind']   = url('user/Shop/shop_order_remind');
            $data_json = json_encode($data);
            $version   = getCmsVersion();
            // 循环中第一个数据带上JS代码加载
            $result['list'][0]['hidden'] = <<<EOF
<script type="text/javascript">
    var d62a4a8743a94dc0250be0c53f833b = {$data_json};
</script>
<script type="text/javascript" src="{$this->root_dir}/public/static/common/js/tag_sporderlist.js?v={$version}"></script>
EOF;
            return $result;
        }else{
            return false;
        }
    }

    public function getSpstatus()
    {
        // 公用条件
        $Where = [
            'users_id' => session('users_id'),
            'lang'     => $this->home_lang,
        ];

        // 待支付个数总计(同等未付款，已下单)
        $newData = [
            'order_status' => 0,
        ];
        $PendingPayment = array_merge($Where, $newData);
        $result['PendingPayment'] = Db::name('shop_order')->where($PendingPayment)->count();
       
        // 待收货个数总计(同等已发货)
        $newData = [
            'order_status' => 2,
        ];
        $PendingReceipt = array_merge($Where, $newData);
        $result['PendingReceipt'] = Db::name('shop_order')->where($PendingReceipt)->count();

        // 已完成个数总计
        $newData = [
            'order_status' => 3,
        ];
        $Completed = array_merge($Where, $newData);
        $result['Completed'] = Db::name('shop_order')->where($Completed)->count();
        
        $result['select_status'] = input('param.select_status');

        return $result;
    }
}