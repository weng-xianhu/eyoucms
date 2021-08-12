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
use think\Cookie;

/**
 * 订单列表
 */
class TagSporderlist extends Base
{
    public $users_id = 0;

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->users_id = session('users_id');
    }

    /**
     * 获取订单列表数据
     */
    public function getSporderlist($pagesize = '10')
    {
        // 基础查询条件
        $OrderWhere = [
            'users_id' => $this->users_id,
            'lang'     => $this->home_lang,
        ];

        // 应用搜索条件
        $keywords = input('param.keywords/s');
        if (!empty($keywords)) $OrderWhere['order_code'] = ['LIKE', "%{$keywords}%"];

        // 订单状态搜索
        $select_status = input('param.select_status');
        if (!empty($select_status)) {
            if ('daifukuan' === $select_status) $select_status = 0;
            if (3 == $select_status) $OrderWhere['is_comment'] = 0;
            $OrderWhere['order_status'] = $select_status;
        }

        // 分页查询逻辑
        $paginate_type = isMobile() ? 'usersmobile' : 'userseyou';
        $query_get = input('get.');
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
            $Data = model('Shop')->QueryOrderList($pagesize, $this->users_id, $keywords, $query_get);
            $result['list']  = $Data['list'];
            $result['pages'] = $Data['pages'];
        }

        /*规格值ID预处理*/
        $SpecValueArray = Db::name('product_spec_value')->field('aid,spec_value_id')->select();
        $SpecValueArray = group_same_key($SpecValueArray, 'aid');
        $ReturnData  = [];
        foreach ($SpecValueArray as $key => $value) {
            $ReturnData[$key] = [];
            foreach ($value as $kk => $vv) {
                array_push($ReturnData[$key], $vv['spec_value_id']);
            }
        }
        /* END */

        if (!empty($result['list'])) {
            // 订单数据处理
            $controller_name = 'Product';
            // 获取当前链接及参数，用于手机端查询快递时返回页面
            $OrderIds = [];
            $ReturnUrl = request()->url(true);
            foreach ($result['list'] as $key => $value) {
                $DetailsWhere['a.users_id'] = $value['users_id'];
                $DetailsWhere['a.order_id'] = $value['order_id'];
                // 查询订单明细表数据
                $result['list'][$key]['details'] = Db::name('shop_order_details')->alias('a')
                    ->field('a.*, b.service_id, c.is_del')
                    ->join('__SHOP_ORDER_SERVICE__ b', 'a.details_id = b.details_id', 'LEFT')
                    ->join('__ARCHIVES__ c', 'a.product_id = c.aid', 'LEFT')
                    ->order('a.product_price desc, a.product_name desc')
                    ->where($DetailsWhere)
                    ->select();
                $array_new = get_archives_data($result['list'][$key]['details'], 'product_id');

                foreach ($result['list'][$key]['details'] as $kk => $vv) {
                    // 产品规格处理
                    if (!in_array($vv['order_id'], $OrderIds) && 0 == $value['order_status']) {
                        $spec_value_id = unserialize($vv['data'])['spec_value_id'];
                        if (!empty($spec_value_id)) {
                            if (!in_array($spec_value_id, $ReturnData[$vv['product_id']])) {
                                // 用于更新订单数据
                                array_push($OrderIds, $vv['order_id']);
                                // 修改循环内的订单状态进行逻辑计算
                                $value['order_status'] = 4;
                                // 修改主表数据，确保输出数据正确
                                $result['list'][$key]['order_status'] = 4;
                                // 用于追加订单操作记录
                                $OrderIds_[]['order_id'] = $vv['order_id'];
                            }
                        }
                    }

                    // 产品内页地址
                    if (!empty($array_new[$vv['product_id']]) && 0 == $vv['is_del']) {
                        // 商品存在
                        $arcurl = urldecode(arcurl('home/'.$controller_name.'/view', $array_new[$vv['product_id']]));
                        $has_deleted = 0;
                        $msg_deleted = '';
                    } else {
                        // 商品不存在
                        $arcurl = urldecode(url('home/View/index', ['aid'=>$vv['product_id']]));
                        $has_deleted = 1;
                        $msg_deleted = '[商品已停售]';
                    }
                    $result['list'][$key]['details'][$kk]['arcurl'] = $arcurl;
                    $result['list'][$key]['details'][$kk]['has_deleted'] = $has_deleted;
                    $result['list'][$key]['details'][$kk]['msg_deleted'] = $msg_deleted;

                    // 图片处理
                    $result['list'][$key]['details'][$kk]['litpic'] = handle_subdir_pic(get_default_pic($vv['litpic']));

                    // 申请退换货
                    $result['list'][$key]['details'][$kk]['ApplyService'] = urldecode(url('user/Shop/after_service_apply', ['details_id' => $vv['details_id']]));

                    // 查看售后详情单
                    $result['list'][$key]['details'][$kk]['ViewAfterSale'] = urldecode(url('user/Shop/after_service_details', ['service_id' => $vv['service_id']]));

                    // 商品评价
                    $result['list'][$key]['details'][$kk]['CommentProduct'] = urldecode(url('user/ShopComment/product', ['details_id' => $vv['details_id']]));
                }

                if (empty($value['order_status'])) {
                    // 付款地址处理，对ID和订单号加密，拼装url路径
                    // $querydata = [
                    //     'order_id'   => $value['order_id'],
                    //     'order_code' => $value['order_code']
                    // ];
                    // /*修复1.4.2漏洞 -- 加密防止利用序列化注入SQL*/
                    // $querystr = '';
                    // foreach($querydata as $_qk => $_qv)
                    // {
                    //     $querystr .= $querystr ? "&$_qk=$_qv" : "$_qk=$_qv";
                    // }
                    // $querystr = str_replace('=', '', mchStrCode($querystr));
                    // $auth_code = tpCache('system.system_auth_code');
                    // $hash = md5("payment".$querystr.$auth_code);
                    // /*end*/
                    // $result['list'][$key]['PaymentUrl'] = urldecode(url('user/Pay/pay_recharge_detail', ['querystr'=>$querystr,'hash'=>$hash]));

                    // 付款地址处理，对ID和订单号加密，拼装url路径
                    $Paydata = [
                        'order_id'   => $value['order_id'],
                        'order_code' => $value['order_code']
                    ];

                    // 先 json_encode 后 md5 加密信息
                    $Paystr = md5(json_encode($Paydata));

                    // 清除之前的 cookie
                    Cookie::delete($Paystr);

                    // 存入 cookie
                    cookie($Paystr, $Paydata);

                    // 跳转链接
                    $result['list'][$key]['PaymentUrl'] = urldecode(url('user/Pay/pay_recharge_detail',['paystr'=>$Paystr]));
                }

                // 获取订单状态
                $order_status_arr = Config::get('global.order_status_arr');
                $result['list'][$key]['order_status_name'] = $order_status_arr[$value['order_status']];

                // 获取订单支付方式名称
                $pay_method_arr = Config::get('global.pay_method_arr');
                if (!empty($value['payment_method']) && !empty($value['pay_name'])) {
                    $result['list'][$key]['pay_name'] = !empty($pay_method_arr[$value['pay_name']]) ? $pay_method_arr[$value['pay_name']] : '第三方支付';
                } else {
                    if (!empty($value['pay_name'])) {
                        $result['list'][$key]['pay_name'] = !empty($pay_method_arr[$value['pay_name']]) ? $pay_method_arr[$value['pay_name']] : '第三方支付';
                    } else {
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
                    if (isMobile()) {
                        $ExpressUrl = "https://m.kuaidi100.com/index_all.html?type=".$value['express_code']."&postid=".$value['express_order']."&callbackurl=".$ReturnUrl;
                    } else {
                        $ExpressUrl = "https://www.kuaidi100.com/chaxun?com=".$value['express_code']."&nu=".$value['express_order'];
                    }
                    // 微信端、小程序使用跳转方式进行物流查询
                    $result['list'][$key]['MobileExpressUrl'] = $ExpressUrl;
                    // PC端，手机浏览器使用弹框方式进行物流查询
                    $result['list'][$key]['LogisticsInquiry'] = " onclick=\"LogisticsInquiry('{$ExpressUrl}');\" ";
                }

                // 默认为空
                $result['list'][$key]['hidden'] = '';
            }

            // 更新产品规格异常的订单，更新为订单过期
            if (!empty($OrderIds)) {
                // 更新订单
                $UpData = [
                    'order_status' => 4,
                    'update_time'  => getTime()
                ];
                Db::name('shop_order')->where('order_id', 'IN', $OrderIds)->update($UpData);
                // 追加订单操作记录
                AddOrderAction($OrderIds_, $this->users_id, 0, 4, 0, 0, '订单过期！', '规格更新后部分产品规格不存在，订单过期！');
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
            'users_id' => $this->users_id,
            'lang'     => $this->home_lang,
        ];
        $ShopOrder = Db::name('shop_order')->where($Where)->field('order_status, is_comment')->select();
        $result['All'] = $result['PendingPayment'] = $result['PendingReceipt'] = $result['Completed'] = 0;
        foreach ($ShopOrder as $key => $value) {
            if (0 == $value['order_status']) {
                // 待支付个数总计(同等未付款，已下单)
                $result['PendingPayment']++;
            } else if (2 == $value['order_status']) {
                // 待收货个数总计(同等已发货)
                $result['PendingReceipt']++;
            } else if (3 == $value['order_status'] && 0 == $value['is_comment']) {
                // 待收货个数总计(同等已发货)
                $result['Completed']++;
            }
        }
        $result['All'] = count($ShopOrder);

        // 退换货个数总计
        $result['AfterService'] = Db::name('shop_order_service')->where($Where)->count();

        // 评价个数总计
        $result['CommentList'] = Db::name('shop_order_comment')->where($Where)->count();
        
        $result['select_status'] = input('param.select_status');
        $result['access_controller'] = request()->controller();
        $result['access_action'] = request()->action();

        return $result;
    }
}