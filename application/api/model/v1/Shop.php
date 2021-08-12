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
 * Date: 2020-05-07
 */

namespace app\api\model\v1;

use think\Db;
use think\Cache;

/**
 * 微信小程序商城订单模型
 */
load_trait('controller/Jump');

class Shop extends UserBase
{
    use \traits\controller\Jump;

    private $diyminiproInfo = [];

    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        $Config = Db::name('pay_api_config')->where('pay_id', 1)->find();
        $this->diyminiproInfo = !empty($Config['pay_info']) ? unserialize($Config['pay_info']) : [];
    }

    /*购物车操作--Start*/
    // 页面点击直接添加购物车
    public function ShopPageAddCart($post = [])
    {
        $post['product_id']  = intval($post['product_id']);
        $post['users_id']    = intval($post['users_id']);
        $post['product_num'] = intval($post['product_num']);

        // 查询条件
        $ArchivesWhere = [
            'aid'     => $post['product_id'],
            'lang'    => get_home_lang(),
            'arcrank' => ['>=', 0]
        ];
        $count         = Db::name('archives')->where($ArchivesWhere)->count();
        // 加入购物车处理
        if (!empty($count)) {
            // 查询条件
            $CartWhere = [
                'users_id'   => $post['users_id'],
                'product_id' => $post['product_id'],
                'lang'       => get_home_lang()
            ];

            /*若开启多规格则执行*/
            $usersConfig = getUsersConfigData('shop');
            if (!empty($usersConfig['shop_open_spec'])) {
                $combiWhere = [
                    'aid'        => $post['product_id'],
                    'spec_stock' => ['>', 0],
                ];
                //获取第一个有库存的规格
                $post['spec_value_id'] = Db::name('product_spec_value')->where($combiWhere)->order('value_id asc')->getField('spec_value_id');
                if (empty($post['spec_value_id'])) {
                    $this->error('商品已售罄');
                }
                $CartWhere['spec_value_id'] = $post['spec_value_id'];
            }
            /* END */

            $ProductNum = Db::name('shop_cart')->field('product_num')->where($CartWhere)->getField('product_num');
            if (!empty($ProductNum)) {
                // 购物车内已有相同商品，进行数量更新。
                $ResultID = Db::name('shop_cart')->where($CartWhere)->setInc('product_num', $post['product_num']);
            } else {
                // 购物车内还未有相同商品，进行添加。
                $data     = [
                    'users_id'      => $post['users_id'],
                    'product_id'    => $post['product_id'],
                    'product_num'   => $post['product_num'],
                    'spec_value_id' => empty($post['spec_value_id']) || 'undefined' == $post['spec_value_id'] ? '' : $post['spec_value_id'],
                    'selected'      => 1,
                    'lang'          => get_home_lang(),
                    'add_time'      => getTime(),
                    'update_time'   => getTime()
                ];
                $ResultID = Db::name('shop_cart')->add($data);
            }
            if (!empty($ResultID)) {
                $this->success('加入成功');
            } else {
                $this->error('加入失败');
            }
        } else {
            $this->error('商品已下架');
        }
    }

    /*购物车操作--Start*/
    // 添加购物车
    public function ShopAddCart($post = [])
    {
        $post['product_id']  = intval($post['product_id']);
        $post['users_id']    = intval($post['users_id']);
        $post['product_num'] = intval($post['product_num']);

        // 查询条件
        $ArchivesWhere = [
            'aid'     => $post['product_id'],
            'lang'    => get_home_lang(),
            'arcrank' => ['>=', 0]
        ];
        $count         = Db::name('archives')->where($ArchivesWhere)->count();
        // 加入购物车处理
        if (!empty($count)) {
            // 查询条件
            $CartWhere = [
                'users_id'   => $post['users_id'],
                'product_id' => $post['product_id'],
                'lang'       => get_home_lang()
            ];

            /*若开启多规格则执行*/
            $usersConfig = getUsersConfigData('shop');
            if (!empty($post['spec_value_id']) && 'undefined' != $post['spec_value_id'] && !empty($usersConfig['shop_open_spec'])) {
                $CartWhere['spec_value_id'] = $post['spec_value_id'];
            }
            /* END */

            $ProductNum = Db::name('shop_cart')->field('product_num')->where($CartWhere)->getField('product_num');
            if (!empty($ProductNum)) {
                // 购物车内已有相同商品，进行数量更新。
                $ResultID = Db::name('shop_cart')->where($CartWhere)->setInc('product_num', $post['product_num']);
            } else {
                // 购物车内还未有相同商品，进行添加。
                $data     = [
                    'users_id'      => $post['users_id'],
                    'product_id'    => $post['product_id'],
                    'product_num'   => $post['product_num'],
                    'spec_value_id' => empty($post['spec_value_id']) || 'undefined' == $post['spec_value_id'] ? '' : $post['spec_value_id'],
                    'selected'      => 1,
                    'lang'          => get_home_lang(),
                    'add_time'      => getTime(),
                    'update_time'   => getTime()
                ];
                $ResultID = Db::name('shop_cart')->add($data);
            }
            if (!empty($ResultID)) {
                $this->success('加入成功');
            } else {
                $this->error('加入失败');
            }
        } else {
            $this->error('商品已下架');
        }
    }

    // 删除购物车商品
    public function ShopCartDelete($param = [])
    {
        if (empty($param['users_id'])) $this->error('请先登录');
        if (empty($param['action'])) $this->error('数据错误，请重试');
        if ('[]' == $param['cart_id']) $this->error('选择要删除的商品');
        $where    = [
            'users_id' => intval($param['users_id']),
            'cart_id'  => ['IN', $param['cart_id']],
        ];
        $ResultID = Db::name('shop_cart')->where($where)->delete();
        if (!empty($ResultID)) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    // 购物车数据列表
    public function ShopCartList($users_id = null, $level_discount = 100)
    {
        $result              = [];
        $result['cart_list'] = [];

        if (!empty($users_id)) {
            // 查询购物车数据
            $condition = [
                'a.users_id' => $users_id,
                'a.lang'     => get_home_lang(),
                'b.arcrank'  => array('egt', 0)
            ];
            $ShopCart  = Db::name("shop_cart")
                ->field('a.*, b.aid, b.title, b.litpic, b.users_price, b.stock_count, b.attrlist_id, c.spec_price, c.spec_stock')
                ->alias('a')
                ->join('__ARCHIVES__ b', 'a.product_id = b.aid', 'LEFT')
                ->join('__PRODUCT_SPEC_VALUE__ c', 'a.spec_value_id = c.spec_value_id and a.product_id = c.aid', 'LEFT')
                ->where($condition)
                ->order('a.selected desc, a.add_time desc')
                ->select();

            if (!empty($ShopCart)) {
                // 获取商城配置信息
                $ConfigData = getUsersConfigData('shop');

                // 会员折扣
                $users_discount = strval($level_discount) / strval(100);

                // 处理购物车数据
                $ShopCart = $this->OrderProductProcess($ShopCart, $ConfigData, $users_discount);
            }
            $result['cart_list'] = $ShopCart;
        }

//        $result['product'] = model('v1.Api')->getRecomProduct(6);

        return $result;
    }

    // 购物车数量增加
    public function ShopCartNumAdd($param = [])
    {
        if (empty($param['users_id'])) $this->error('请先登录');
        if (empty($param['action']) || empty($param['product_id'])) $this->error('数据错误，请重试');
        $where = [
            'users_id'   => intval($param['users_id']),
            'product_id' => intval($param['product_id']),
        ];
        if (!empty($param['spec_value_id']) && 'undefined' != $param['spec_value_id']) {
            $where['spec_value_id'] = $param['spec_value_id'];
        }
        $ResultID = Db::name('shop_cart')->where($where)->setInc('product_num');
        if (!empty($ResultID)) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    // 购物车数量减少
    public function ShopCartNumLess($param = [])
    {
        if (empty($param['users_id'])) $this->error('请先登录');
        if (empty($param['action']) || empty($param['product_id'])) $this->error('数据错误，请重试');
        $where = [
            'users_id'   => intval($param['users_id']),
            'product_id' => intval($param['product_id']),
        ];
        if (!empty($param['spec_value_id']) && 'undefined' != $param['spec_value_id']) {
            $where['spec_value_id'] = $param['spec_value_id'];
        }
        $product_num_tmp = Db::name('shop_cart')->where($where)->getField('product_num');
        if (!empty($product_num_tmp)) {
            if ($product_num_tmp > 1) {
                $ResultID = Db::name('shop_cart')->where($where)->setDec('product_num');
                if (!empty($ResultID)) {
                    $this->success('操作成功');
                }
            } else {
                $this->error('数量至少一件！');
            }
        }
        $this->error('操作失败');
    }

    // 购物车商品更新是否选中
    public function ShopCartSelected($param = [])
    {
        if (empty($param['users_id'])) $this->error('请先登录');
        if (empty($param['action']) || empty($param['product_id'])) $this->error('数据错误，请重试');
        $where = [
            'users_id'   => intval($param['users_id']),
            'product_id' => intval($param['product_id']),
        ];
        if (!empty($param['spec_value_id']) && 'undefined' != $param['spec_value_id']) {
            $where['spec_value_id'] = $param['spec_value_id'];
        }
        $update   = [
            'selected'    => $param['selected'] == 1 ? 0 : 1,
            'update_time' => getTime()
        ];
        $ResultID = Db::name('shop_cart')->where($where)->update($update);
        if (!empty($ResultID)) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    // 全选/反选
    public function ShopCartAllSelected($param = [])
    {
        if (empty($param['users_id'])) $this->error('请先登录');
        if (empty($param['action'])) $this->error('数据错误，请重试');
        $where    = [
            'users_id' => intval($param['users_id']),
        ];
        $update   = [
            'selected'    => $param['all_selected'],
            'update_time' => getTime()
        ];
        $ResultID = Db::name('shop_cart')->where($where)->update($update);
        if (!empty($ResultID)) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }
    /*购物车操作--END*/

    /*商城购物流程--Start*/
    // 立即购买
    public function ShopBuyNow($post = [])
    {
        // 查询条件
        $ArchivesWhere = [
            'aid'     => $post['product_id'],
            'lang'    => get_home_lang(),
            'arcrank' => ['>=', 0]
        ];
        $count         = Db::name('archives')->where($ArchivesWhere)->count();
        // 加入购物车处理
        if (!empty($count)) {
            // 对ID和订单号加密，拼装url路径
            $querydata = [
                'product_id'  => $post['product_id'],
                'product_num' => $post['product_num'],
                'active_time_id' => isset($post['active_time_id']) ? $post['active_time_id'] : 0,
            ];

            /*存在规格则执行*/
            if (!empty($post['spec_value_id']) && 'undefined' != $post['spec_value_id']) {
                $querydata['spec_value_id'] = $post['spec_value_id'];
            }
            /* END */

            // 先 json_encode 后 md5 加密信息
            $querystr = md5(json_encode($querydata));

            // 存入缓存，一天有效期
            Cache::set($querystr, $querydata, 86400);

            // 跳转链接
            $url = '/pages/product_buy/index?querystr=' . $querystr;
            $this->success('数据正确', $url);
        } else {
            $this->error('该商品不存在或已下架！');
        }
    }

    // 获取商品信息进行展示
    public function GetProductData($querystr = null, $users_id = null, $level_discount = 100)
    {
        if (empty($users_id)) $this->error('请先登录');

        /* 商品信息 */
        $CacheData = Cache::get($querystr);

        // 是否存在购买商品
        $list = $this->GetBuyOrderList($CacheData, $users_id);
        if (empty($list)) $this->error('链接失效，请重新下单');

        // 获取商城配置信息
        $ConfigData = getUsersConfigData('shop');

        // 会员折扣
        $users_discount = strval($level_discount) / strval(100);
        if (!empty($CacheData['active_time_id'])){
            //秒杀商品没有会员折扣
            $users_discount = 0;
        }

        /* 商品规格/折扣数据处理 */
        $list = $this->OrderProductProcess($list, $ConfigData, $users_discount);

        if (empty($list)) $this->error('商品库存不足或已过期！');
        /* END */

        /* 全局信息组装 */
        $GlobalInfo = $this->GetGlobalInfo($ConfigData, $CacheData);
        /* END */

        /* 商品数据整合处理 */
        foreach ($list as $key => $value) {
            /* 合计金额、合计数量计算 */
            if ($value['users_price'] >= 0 && !empty($value['product_num'])) {
                // 计算单品小计
                $list[$key]['subtotal'] = sprintf("%.2f", $value['users_price'] * $value['product_num']);
                // 计算合计金额
                $GlobalInfo['TotalAmount'] += $list[$key]['subtotal'];
                $GlobalInfo['TotalAmount'] = sprintf("%.2f", $GlobalInfo['TotalAmount']);
                // 计算合计数量
                $GlobalInfo['TotalNumber'] += $value['product_num'];
                // 判断订单类型，目前逻辑：一个订单中，只要存在一个普通商品(实物商品，需要发货物流)，则为普通订单
                // 0表示为普通订单，1表示为虚拟订单，虚拟订单无需发货物流，无需选择收货地址，无需计算运费
                if (empty($value['prom_type'])) $GlobalInfo['PromType'] = 0;
            }
            /* END */

            /* 规格处理 */
            $list[$key]['product_spec'] = '';
            if (!empty($value['spec_value_id'])) {
                $spec_value_id = explode('_', $value['spec_value_id']);
                if (!empty($spec_value_id)) {
                    $SpecWhere       = [
                        'aid'           => $value['product_id'],
                        'lang'          => get_home_lang(),
                        'spec_value_id' => ['IN', $spec_value_id]
                    ];
                    $ProductSpecData = Db::name("product_spec_data")->where($SpecWhere)->field('spec_name, spec_value')->select();
                    foreach ($ProductSpecData as $spec_value) {
                        $list[$key]['product_spec'] .= $spec_value['spec_name'] . '：' . $spec_value['spec_value'] . '；';
                    }
                }
            }
            /* END */
        }
        /* END */

        /* 默认地址 */
        $address = Db::name('shop_address')->where('users_id', $users_id)->order('is_default desc')->find();
        if (!empty($address)) {
            $address['region']['province'] = $this->GetRegionName($address['province']);
            $address['region']['city']     = $this->GetRegionName($address['city']);
            $address['region']['district'] = $this->GetRegionName($address['district']);
            $address['region']['detail']   = $address['address'];
            $address['address_all']        = $address['region']['province'] . $address['region']['city'] . $address['region']['district'] . $address['region']['detail'];

            $shop_open_shipping = getUsersConfigData('shop.shop_open_shipping');
            if (!empty($shop_open_shipping)) {
                /* 查询运费 */
                $where['province_id'] = $address['province'];
                $template_money       = Db::name('shop_shipping_template')->where($where)->getField('template_money');
                if (0 == $template_money) {
                    $where['province_id'] = 100000;
                    $template_money       = Db::name('shop_shipping_template')->where($where)->getField('template_money');
                }
                $address['template_money'] = $template_money;
                /* END */
            } else {
                $address['template_money'] = 0;
            }
        } else {
            $address['template_money'] = 0;
        }
        /* END */

        // 最终支付总金额
        $GlobalInfo['PayTotalAmount'] = sprintf("%.2f", $GlobalInfo['TotalAmount'] + $address['template_money']);

        // 数据返回
        $ResultData = [
            'address'      => $address,
            'global_info'  => $GlobalInfo,
            'product_data' => $list
        ];
        $ResultData[ 'coupon_data'] = $this->getOrderCoupon($list,$users_id);
        $this->success('数据正确', null, $ResultData);
    }

    /**
     * 获取该订单可用优惠券
     */
    public function getOrderCoupon($product_data,$user_id)
    {
        $aids = [];
        $typeids = [];
        $type_price = 0;
        $aid_price = 0;
        $totol_price = 0;
        foreach ($product_data as $k => $v){
            if (!in_array($v['aid'],$aids)){
                $aids[] = $v['aid'];
                $aid_price += $v['subtotal'];
            }
            if (!in_array($v['typeid'],$typeids)){
                $typeids[] = $v['typeid'];
                $type_price += $v['subtotal'];
            }
            $totol_price += $v['subtotal'];
        }
        //查出顶级分类
        if (!empty($typeids)){
            $type_data = Db::name('arctype')->where('id','in',$typeids)->field('id,topid')->select();
            if (!empty($type_data)){
                $typeids = [];
                foreach ($type_data as $k => $v){
                    if (0 == $v['topid'] && !in_array($v['id'],$typeids)){
                        $typeids[] = $v['id'];
                    }else if( !empty($v['topid'])  && !in_array($v['topid'],$typeids) ){
                        $typeids[] = $v['topid'];
                    }
                }
            }
        }
        $where['a.users_id'] = $user_id;
        $where['a.start_time'] = ['<=',getTime()];
        $where['a.end_time'] = ['>=',getTime()];
        $where['a.use_status'] =  0;
        $where['b.coupon_id'] =  ['>',1];
        $use_data = [];
        //查出全部的优惠券
        $data = Db::name('shop_coupon_use')
            ->alias('a')
            ->join('shop_coupon b','a.coupon_id = b.coupon_id','left')
            ->where($where)
            ->getAllWithIndex('use_id');
        if (!empty($data)){
            foreach ($data as $k => $v){
                if (1 == $v['coupon_form']){
                    $v['coupon_form_name'] = '满减券';
                }
                $v['start_time'] = date('Y/m/d', $v['start_time']);
                $v['end_time'] = date('Y/m/d', $v['end_time']);
                switch ($v['coupon_type'])
                {
                    case 2:
                        //指定商品
                        if (!empty($v['product_id'])){
                            $product_arr = explode(",",$v['product_id']);
                            $in = 0;
                            foreach ($aids as $m){
                                if (in_array($m,$product_arr) && $aid_price >= $v['conditions_use']){
                                    //此优惠券可用
                                    $in = 1;
                                    break;
                                }
                            }
                            if (!empty($in)){
                                $use_data[] = $v;
                            }
                        }
                        break;
                    case 3:
                        //指定分类
                        if (!empty($v['arctype_id'])){
                            $arctype_arr = explode(",",$v['arctype_id']);
                            $in = 0;
                            foreach ($typeids as $m){
                                if (in_array($m,$arctype_arr) && $type_price >= $v['conditions_use']){
                                    //此优惠券可用
                                    $in = 1;
                                    break;
                                }
                            }
                            if (!empty($in)){
                                $use_data[] = $v;
                            }
                        }
                        break;
                    default:
                        if ($totol_price >= $v['conditions_use']) {
                            $use_data[] = $v;
                        }
                        break;
                }
            }
        }
        return $use_data;
    }

    // 订单结算
    public function ShopOrderPay($post = [], $level_discount = 100)
    {
        // 基础判断
        if (empty($post['users_id'])) $this->error('请先登录');
        if (1 != $post['prom_type']){
            if (empty($post['addr_id']) || $post['addr_id'] == 'undefined') $this->error('请添加收货地址');
        }
        if (empty($post['pay_type'])) $this->error('请选择支付方式');

        // 获取商城配置信息
        $ConfigData = getUsersConfigData('shop');

        // 会员折扣
        $users_discount = strval($level_discount) / strval(100);
        if ( isset($post['order_source']) && 20 == $post['order_source']){
            $users_discount = 0;
        }

        // 获取下单的商品数据
        $CacheData = Cache::get($post['querystr']);
        $list      = $this->GetBuyOrderList($CacheData, $post['users_id']);
        if (empty($list)) $this->error('链接失效，请重新下单');

        // 订单商品数据处理
        $list = $this->OrderProductProcess($list, $ConfigData, $users_discount);
        if (empty($list)) $this->error('商品库存不足或已过期！');

        // 组装订单流程所需全局信息
        $GlobalInfo = $this->GetGlobalInfo($ConfigData, $CacheData);

        /* 商品数据整合处理 */
        foreach ($list as $key => $value) {
            /* 合计金额、合计数量计算 */
            if ($value['users_price'] >= 0 && !empty($value['product_num'])) {
                // 计算合计金额
                $GlobalInfo['TotalAmount'] += sprintf("%.2f", $value['users_price'] * $value['product_num']);
                $GlobalInfo['TotalAmount'] = sprintf("%.2f", $GlobalInfo['TotalAmount']);
                // 计算合计数量
                $GlobalInfo['TotalNumber'] += $value['product_num'];
                // 判断订单类型，目前逻辑：一个订单中，只要存在一个普通商品(实物商品，需要发货物流)，则为普通订单
                // 0表示为普通订单，1表示为虚拟订单，虚拟订单无需发货物流，无需选择收货地址，无需计算运费
                if (empty($value['prom_type'])) $GlobalInfo['PromType'] = 0;
            }
            /* END */
        }
        /* END */

        // 获取用户下单时提交的收货地址及运费
        $AddrData = $this->GetUsersAddrData($post, $GlobalInfo['PromType']);

        // 订单总金额 + 运费金额
        $GlobalInfo['PayTotalAmount'] = sprintf("%.2f", $GlobalInfo['TotalAmount'] + $AddrData['shipping_fee']);
        //应付总金额
        $PayAmount = $GlobalInfo['PayTotalAmount'];
        //使用优惠券
        if (!empty($post['coupon_id'])){
            $coupon_where = [
                'a.coupon_id'=>$post['coupon_id'],
                'a.use_status'=>0,
                'a.users_id'  => $post['users_id'],
            ];
            $coupon_where['a.start_time'] = ['<=',getTime()];
            $coupon_where['a.end_time'] = ['>=',getTime()];
            $coupon_info = Db::name('shop_coupon_use')
                ->alias('a')
                ->join('shop_coupon b','a.coupon_id = b.coupon_id','left')
                ->where($coupon_where)
                ->find();
            if (!empty($coupon_info)){
                $PayAmount = $PayAmount-$coupon_info['coupon_price'] < 0 ? 0 : $PayAmount-$coupon_info['coupon_price'];
            }
        }

        if (2 == $post['pay_type']) {
            $UsersMoney = Db::name('users')->where('users_id', $post['users_id'])->getField('users_money');
            if ($GlobalInfo['PayTotalAmount'] > $UsersMoney) $this->error('您的余额不足，支付失败');
        }

        // 添加到订单主表
        $time      = getTime();
        $OrderData = [
            'order_code'         => date('Ymd') . $time . rand(10, 100),
            'users_id'           => $post['users_id'],
            'order_status'       => 0, // 订单未付款
            'add_time'           => $time,
            'payment_method'     => 0,
            'pay_time'           => 0,
            'pay_name'           => 'wechat',
            'wechat_pay_type'    => 'WeChatH5',
            'pay_details'        => '',
            'order_total_amount' => $GlobalInfo['PayTotalAmount'],
            'order_amount'       => $PayAmount,
            'order_total_num'    => $GlobalInfo['TotalNumber'],
            'prom_type'          => $GlobalInfo['PromType'],
            'order_source'       => isset($post['order_source']) ? $post['order_source'] : 10,
            'order_source_id'    => isset($post['order_source_id']) ? $post['order_source_id'] : 0,
            'virtual_delivery'   => '',
            'admin_note'         => '',
            'user_note'          => $post['remark'],
            'lang'               => get_home_lang()
        ];
        if (!empty($coupon_info)){
            $OrderData['coupon_id'] = $coupon_info['coupon_id'];
            $OrderData['use_id'] = $coupon_info['use_id'];
            $OrderData['coupon_price'] = $coupon_info['coupon_price'];
        }
        // 存在收货地址则追加合并到主表数组
        if (!empty($AddrData)) $OrderData = array_merge($OrderData, $AddrData);

        $OrderId = Db::name('shop_order')->add($OrderData);
        if (!empty($OrderId)) {
            //锁定优惠券
            if (!empty($coupon_info)){
                $coupon_update = [
                    'coupon_id'=>$post['coupon_id'],
                    'use_status'=> 0,
                    'users_id'  => $post['users_id'],
                ];
                Db::name('shop_coupon_use')->where($coupon_update)->update(['use_status'=> 1,'use_time'=>getTime(),'update_time'=>getTime()]);
            }

            $cart_ids   = $UpSpecValue = [];
            foreach ($list as $key => $value) {
                $attr_value = $attr_value_new = $spec_value = '';

                // 旧商品属性处理
                $attr_value = $this->ProductAttrProcessing($value);
                // 新商品属性处理
                $attr_value_new = $this->ProductNewAttrProcessing($value);
                // 商品规格处理
                $spec_value = $this->ProductSpecProcessing($value);

                // 规格及属性
                $ParamData = [
                    // 商品属性
                    'attr_value'     => htmlspecialchars($attr_value),
                    // 商品属性
                    'attr_value_new' => htmlspecialchars($attr_value_new),
                    // 商品规格
                    'spec_value'     => htmlspecialchars($spec_value),
                    // 商品规格值ID
                    'spec_value_id'  => $value['spec_value_id'],
                    // 对应规格值ID的唯一标识ID，数据表主键ID
                    'value_id'       => $value['value_id'],
                    // 后续添加
                ];

                // 订单副表添加数组
                $OrderDetailsData[] = [
                    'order_id'      => $OrderId,
                    'users_id'      => $post['users_id'],
                    'product_id'    => $value['aid'],
                    'product_name'  => $value['title'],
                    'num'           => $value['product_num'],
                    'data'          => serialize($ParamData),
                    'product_price' => $value['users_price'],
                    'prom_type'     => $value['prom_type'],
                    'litpic'        => $value['litpic'],
                    'add_time'      => $time,
                    'lang'          => get_home_lang()
                ];

                // 处理购物车ID
                if (empty($GlobalInfo['submit_order_type'])) {
                    array_push($cart_ids, $value['cart_id']);
                }

                // 商品库存处理
                $UpSpecValue[] = [
                    'aid'           => $value['aid'],
                    'value_id'      => $value['value_id'],
                    'quantity'      => $value['product_num'],
                    'spec_value_id' => $value['spec_value_id'],
                    'order_source'       => isset($post['order_source']) ? $post['order_source'] : 10,
                    'order_source_id'    => isset($post['order_source_id']) ? $post['order_source_id'] : 0,
                ];
            }

            $DetailsId = Db::name('shop_order_details')->insertAll($OrderDetailsData);
            if (!empty($OrderId) && !empty($DetailsId)) {
                // 清理购物车中已下单的ID
                if (!empty($cart_ids)) Db::name('shop_cart')->where('cart_id', 'IN', $cart_ids)->delete();

                // 商品库存、销量处理
                $this->ProductStockProcessing($UpSpecValue);

                // 添加订单操作记录
                AddOrderAction($OrderId, $post['users_id']);

                if (1 == $post['pay_type']) {
                    // 微信支付
                    $Data       = $this->GetWechatAppletsPay($post['openid'], $OrderData['order_code'], $OrderData['order_amount']);
                    $ResultData = [
                        'WeChatPay' => $Data,
                        'OrderData' => [
                            'order_id'   => $OrderId,
                            'order_code' => $OrderData['order_code']
                        ]
                    ];
                    $this->success('正在支付', null, $ResultData);

                } else if (2 == $post['pay_type']) {
                    // 余额支付

                    $pay_details = [
                        'unified_id'     => $OrderId,
                        'unified_number' => $OrderData['order_code'],
                        'payment_amount' => $OrderData['order_amount'],
                        'payment_type'   => '余额支付'
                    ];
                    $UpOrderData = [
                        'order_status'    => 1,
                        'pay_name'        => 'balance',
                        'wechat_pay_type' => '',
                        'pay_details'     => serialize($pay_details),
                        'pay_time'        => getTime(),
                        'update_time'     => getTime()
                    ];
                    $ReturnID    = Db::name('shop_order')->where('order_id', $OrderId)->update($UpOrderData);
                    if (!empty($ReturnID)) {
                        $UsersData = [
                            'users_money' => $UsersMoney - $OrderData['order_amount'],
                            'update_time' => getTime()
                        ];
                        $users_id  = Db::name('users')->where('users_id', $post['users_id'])->update($UsersData);
                        if (!empty($users_id)) {
                            // 添加订单操作记录
                            AddOrderAction($OrderId, $post['users_id'], 0, 1, 0, 1, '支付成功！', '会员使用余额完成支付！');

                            // 虚拟自动发货
                            $ShopModel = new \app\user\model\Pay();
                            $Where     = [
                                'lang'     => get_home_lang(),
                                'users_id' => $post['users_id'],
                                'order_id' => $OrderId
                            ];
                            $ShopModel->afterVirtualProductPay($Where);

                            // 邮箱发送
                            // $SmtpConfig = tpCache('smtp');
                            // $ResultData['email'] = GetEamilSendData($SmtpConfig, $this->users, $OrderWhere, 1, 'balance');

                            $url = '/pages/order/index';
                            $this->success('支付完成', $url, ['OrderData' => $OrderData]);
                        }
                    }
                }
            } else if (!empty($OrderId) && empty($DetailsId)) {
                // 订单详情添加失败则删除已添加的订单主表数据
                Db::name('shop_order')->delete($OrderId);
                $this->error('订单生成失败，商品数据有误！');
            }
        }
        $this->error('订单生成失败，商品数据有误！');
    }

    // 微信小程序支付后续处理
    public function WechatAppletsPayDealWith($PostData = [], $notify = false)
    {
        $OpenID    = !empty($PostData['openid']) ? $PostData['openid'] : '';
        $UsersID   = !empty($PostData['users_id']) ? $PostData['users_id'] : '';
        $OrderID   = $PostData['order_id'];
        $OrderCode = $PostData['order_code'];
        if (!empty($OrderID) && !empty($OrderCode)) {
            /* 查询本站订单状态 */
            $where = [
                'users_id'   => $UsersID,
                'order_id'   => $OrderID,
                'order_code' => $OrderCode,
            ];
            $order = Db::name('shop_order')->where($where)->find();

            if (empty($order)) {
                $this->error('无效订单！');
            } else if (0 < $order['order_status']) {
                // 订单已支付
                if ($notify === true) { // 异步
                    return [
                        'code' => 1,
                        'msg'  => 'ok',
                    ];
                } else { // 同步
                    $this->success('支付完成', '/pages/order/index');
                }
            }

            // 小程序配置
            $diyminiproMallSettingModel = new \weapp\DiyminiproMall\model\DiyminiproMallSettingModel;
            $Setting                    = $diyminiproMallSettingModel->getSettingValue('setting');

            // 当前时间戳
            $time = getTime();

            // 当前时间戳 + OpenID 经 MD5加密
            $nonceStr = md5($time . $OpenID);

            // 调用支付接口参数
            $params = [
                'appid'        => $Setting['appId'],
                'mch_id'       => $this->diyminiproInfo['mchid'],
                'nonce_str'    => $nonceStr,
                'out_trade_no' => $OrderCode
            ];

            // 生成参数签名
            $params['sign'] = $this->ParamsSign($params, $this->diyminiproInfo['key']);

            // 生成参数XML格式
            $ParamsXml = $this->ParamsXml($params);

            // 调用接口返回数据
            $url    = 'https://api.mch.weixin.qq.com/pay/orderquery';
            $result = $this->HttpsPost($url, $ParamsXml);

            // 解析XML格式
            $ResultData = $this->ResultXml($result);

            // 订单是否存在
            if (!empty($ResultData) && 'SUCCESS' == $ResultData['return_code'] && 'OK' == $ResultData['return_msg']) {
                // if ('NOTPAY' == $ResultData['trade_state'] && !empty($ResultData['trade_state_desc'])) {
                //     // 订单未支付
                //     $this->error($ResultData['trade_state_desc']);
                // }
                if ('SUCCESS' == $ResultData['trade_state']) {
                    // 订单已支付，处理订单流程
                    $OrderWhere = [
                        'order_id' => $OrderID,
                        'users_id' => $order['users_id']
                    ];
                    $OrderData  = [
                        'order_status' => 1,
                        'pay_details'  => serialize($ResultData),
                        'pay_time'     => getTime(),
                        'update_time'  => getTime()
                    ];
                    $ResultID   = Db::name('shop_order')->where($OrderWhere)->update($OrderData);
                    if (!empty($ResultID)) {
                        // 添加订单操作记录
                        AddOrderAction($OrderID, $order['users_id'], 0, 1, 0, 1, '支付成功！', '会员使用微信小程序完成支付！');

                        // 虚拟自动发货
                        $ShopModel = new \app\user\model\Pay();
                        $Where     = [
                            'lang'     => get_home_lang(),
                            'users_id' => $UsersID,
                            'order_id' => $OrderID
                        ];
                        $ShopModel->afterVirtualProductPay($Where);

                        // 订单支付完成
                        if ($notify === true) { // 异步
                            return [
                                'code' => 1,
                                'msg'  => 'ok',
                            ];
                        } else { // 同步
                            $url = '/pages/order/index';
                            $this->success('支付完成', $url);
                        }
                    }
                }
            }
        }
    }

    // 用于订单列表和订单详情页直接支付
    public function OrderDirectPay($post = [])
    {
        $where  = [
            'users_id'   => $post['users_id'],
            'order_id'   => $post['order_id'],
            'order_code' => $post['order_code']
        ];
        $field  = 'order_status, order_amount';
        $Result = Db::name('shop_order')->where($where)->field($field)->find();
        if (!empty($Result)) {
            if (0 == $Result['order_status']) {
                $Data       = $this->GetWechatAppletsPay($post['openid'], $post['order_code'], $Result['order_amount']);
                $ResultData = [
                    'WeChatPay' => $Data,
                    'OrderData' => [
                        'order_id'   => $post['order_id'],
                        'order_code' => $post['order_code']
                    ]
                ];
                $url        = '/pages/order/index';
                $this->success('正在支付', $url, $ResultData);
            } else if (in_array($Result['order_status'], [1, 2, 3])) {
                $this->success('订单已支付');
            } else if ($Result['order_status'] == 4) {
                $this->success('订单已过期');
            } else if ($Result['order_status'] == -1) {
                $this->success('订单已关闭');
            }
        }
        $this->error('订单不存在');
    }

    // 微信小程序支付
    private function GetWechatAppletsPay($OpenID = null, $out_trade_no = null, $total_fee = null)
    {
        // 小程序配置
        $diyminiproMallSettingModel = new \weapp\DiyminiproMall\model\DiyminiproMallSettingModel;
        $Setting                    = $diyminiproMallSettingModel->getSettingValue('setting');

        // 当前时间戳
        $time = time();

        // 当前时间戳 + OpenID 经 MD5加密
        $nonceStr = md5($time . $OpenID);

        // 调用支付接口参数
        $params = [
            'appid'            => $Setting['appId'],
            'attach'           => "微信小程序支付",
            'body'             => "商品支付",
            'mch_id'           => $this->diyminiproInfo['mchid'],
            'nonce_str'        => $nonceStr,
            'notify_url'       => url('plugins/DiyminiproMall/wxpay_notify', [], true, true, 1, 2),
            'openid'           => $OpenID,
            'out_trade_no'     => $out_trade_no,
            'spbill_create_ip' => $this->GetClientIP(),
            'total_fee'        => strval($total_fee * 100),
            'trade_type'       => 'JSAPI'
        ];

        // 生成参数签名
        $params['sign'] = $this->ParamsSign($params, $this->diyminiproInfo['key']);

        // 生成参数XML格式
        $ParamsXml = $this->ParamsXml($params);

        // 调用接口返回数据
        $url    = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $result = $this->HttpsPost($url, $ParamsXml);

        // 解析XML格式
        $ResultData = $this->ResultXml($result);

        // 数据返回
        if ($ResultData['return_code'] == 'SUCCESS' && $ResultData['return_msg'] == 'OK') {
            // 返回支付所需参数
            $ReturnData = [
                'prepay_id' => $ResultData['prepay_id'],
                'nonceStr'  => $nonceStr,
                'timeStamp' => strval($time)
            ];
            // 生成支付所需的签名
            $ReturnData['paySign'] = $this->PaySign($Setting['appId'], $params['nonce_str'], $ResultData['prepay_id'], $time);
            return $ReturnData;
        } else if ($ResultData['return_code'] == 'FAIL') {
            if ($ResultData['return_msg'] == '签名错误') {
                $ResultData['return_msg'] = '小程序支付配置不正确！';
            }
            return $ResultData;
        } else {
            return $ResultData;
        }
    }

    private function ParamsSign($values, $apikey)
    {
        //签名步骤一：按字典序排序参数
        ksort($values);
        $string = $this->ParamsUrl($values);
        //签名步骤二：在string后加入KEY
        $string = $string . '&key=' . $apikey;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    private function ParamsUrl($values)
    {
        $Url = '';
        foreach ($values as $k => $v) {
            if ($k != 'sign' && $v != '' && !is_array($v)) {
                $Url .= $k . '=' . $v . '&';
            }
        }
        return trim($Url, '&');
    }

    private function ParamsXml($values)
    {
        if (!is_array($values)
            || count($values) <= 0
        ) {
            return false;
        }

        $xml = "<xml>";
        foreach ($values as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    private function ResultXml($xml)
    {
        // 禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    private function GetClientIP()
    {
        if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
            $ip = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches [0] : '';
    }

    private function HttpsPost($url, $data)
    {
        $result = httpRequest($url, 'POST', $data);
        return $result;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return 'Errno: ' . curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }

    private function PaySign($appid, $nonceStr, $prepay_id, $timeStamp)
    {
        $data = [
            'appId'     => $appid,
            'nonceStr'  => $nonceStr,
            'package'   => 'prepay_id=' . $prepay_id,
            'signType'  => 'MD5',
            'timeStamp' => $timeStamp,
        ];
        // 签名步骤一：按字典序排序参数
        ksort($data);
        $string = $this->ParamsUrl($data);
        // 签名步骤二：在string后加入KEY
        $string = $string . '&key=' . $this->diyminiproInfo['key'];
        // 签名步骤三：MD5加密
        $string = md5($string);
        // 签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    // 扣除商品库存
    private function ProductStockProcessing($SpecValue = [])
    {
        if (!empty($SpecValue)) {
            $SpecUpData = []; // 有规格
            $ArcUpData  = []; // 无规格
            foreach ($SpecValue as $key => $value) {
                if (!empty($value['value_id'])) {
                    if (!empty($value['order_source']) && $value['order_source'] == 20){
                        //秒杀订单
                        $SpecUpData[] = [
                            'value_id'       => $value['value_id'],
                            'seckill_stock'     => Db::raw('seckill_stock-' . ($value['quantity'])),
                            'seckill_sales_num' => Db::raw('seckill_sales_num+' . ($value['quantity'])),
                        ];
                    }else{
                        //普通订单
                        $SpecUpData[] = [
                            'value_id'       => $value['value_id'],
                            'spec_stock'     => Db::raw('spec_stock-' . ($value['quantity'])),
                            'spec_sales_num' => Db::raw('spec_sales_num+' . ($value['quantity'])),
                        ];
                        $ArcUpData[] = [
                            'aid'         => $value['aid'],
                            'stock_count' => Db::raw('stock_count-' . ($value['quantity'])),
                            'sales_num'   => Db::raw('sales_num+' . ($value['quantity']))
                        ];
                    }
                } else {
                    if ($value['order_source'] != 20){
                        //普通订单
                        $ArcUpData[] = [
                            'aid'         => $value['aid'],
                            'stock_count' => Db::raw('stock_count-' . ($value['quantity'])),
                            'sales_num'   => Db::raw('sales_num+' . ($value['quantity']))
                        ];
                    }
                }
                //秒杀订单无论单规格还是多规格都要更改sharp_goods的库存和销量
                if (!empty($value['order_source']) && $value['order_source'] == 20){
                    $SharpUpData = [
                        'aid'         => $value['aid'],
                        'seckill_stock' => Db::raw('seckill_stock-' . ($value['quantity'])),
                        'sales'   => Db::raw('sales+' . ($value['quantity']))
                    ];
                    Db::name('sharp_goods')->where('aid',$value['aid'])->update($SharpUpData);
                    Db::name('sharp_active_goods')
                        ->where(['active_time_id'=>$value['order_source_id'],'aid'=> $value['aid']])
                        ->update(['sales_actual'=>Db::raw('sales_actual+' . ($value['quantity']))]);
                }
            }

            // 更新规格库存销量
            if (!empty($SpecUpData)) {
                $ProductSpecValueModel = new \app\user\model\ProductSpecValue();
                $ProductSpecValueModel->saveAll($SpecUpData);
            }

            // 更新商品库存销量
            if (!empty($ArcUpData)) {
                $ArchivesModel = new \app\user\model\Archives();
                $ArchivesModel->saveAll($ArcUpData);
            }
        }
    }

    // 获取下单的商品数据
    private function GetBuyOrderList($CacheData = [], $users_id = null)
    {
        $list = [];
        if (!empty($CacheData)) {
            // 立即购买下单
            $ArchivesWhere['a.aid'] = $CacheData['product_id'];
            if (!empty($CacheData['spec_value_id'])) $ArchivesWhere['b.spec_value_id'] = $CacheData['spec_value_id'];
            $field                  = 'a.aid, a.typeid, a.title, a.litpic, a.users_price, a.stock_count, a.prom_type, a.attrlist_id, b.value_id, b.aid as product_id, b.spec_value_id, b.spec_price, b.spec_stock';
            if ('v1.5.1' < getVersion()) {
                $field .= ',b.seckill_price, b.seckill_stock';
            }
            $list                   = Db::name('archives')->alias('a')->field($field)
                ->join('__PRODUCT_SPEC_VALUE__ b', 'a.aid = b.aid', 'LEFT')
                ->where($ArchivesWhere)
                ->limit('0, 1')
                ->select();
            $list[0]['product_num'] = $CacheData['product_num'];
            if (isset($CacheData['active_time_id']) && !empty($CacheData['active_time_id'])){
                $list[0]['active_time_id'] = $CacheData['active_time_id'];
                //判断是否还在秒杀时间内
                $hour = date('H');
                $date = strtotime(date('Y-m-d'));
                $active_time = Db::name('sharp_active_time')
                    ->alias('a')
                    ->join('sharp_active b','a.active_id = b.active_id')
                    ->where('b.active_date',$date)
                    ->where('a.active_time',$hour)
                    ->getField('active_time_id');
                if (empty($active_time) || $CacheData['active_time_id'] != $active_time){
                    if (empty($list)) $this->error('秒杀活动已结束!');
                }
                //判断该秒杀商品是多规格还是单规格
                $is_sku = Db::name('sharp_active_goods')
                    ->alias('a')
                    ->join('sharp_goods b','a.aid = b.aid')
                    ->where('a.aid',$CacheData['product_id'])
                    ->where('a.active_time_id',$CacheData['active_time_id'])
                    ->getField('b.is_sku');
                $list[0]['is_sku'] = $is_sku;
                if (empty($is_sku)){
                    //单规格
                    $active_goods = Db::name('sharp_goods')->where('aid',$CacheData['product_id'])->field('seckill_price,seckill_stock')->find();
                    if (!empty($active_goods)){
                        $list[0]['users_price'] = $list[0]['seckill_price'] = $active_goods['seckill_price'];
                        $list[0]['stock_count'] = $list[0]['seckill_stock'] = $active_goods['seckill_stock'];
                    }
                }
            }
        } else {
            // 购物车下单
            $cart_where = [
                'a.users_id' => $users_id,
                'a.lang'     => get_home_lang(),
                'a.selected' => 1,
            ];
            $list       = Db::name('shop_cart')->field('a.*,b.typeid, b.aid, b.title, b.litpic, b.users_price, b.stock_count, b.prom_type, b.attrlist_id, c.spec_price, c.spec_stock, c.value_id')
                ->alias('a')
                ->join('__ARCHIVES__ b', 'a.product_id = b.aid', 'LEFT')
                ->join('__PRODUCT_SPEC_VALUE__ c', 'a.spec_value_id = c.spec_value_id and a.product_id = c.aid', 'LEFT')
                ->where($cart_where)
                ->select();
        }

        return $list;
    }

    // 商品规格/折扣数据处理
    private function OrderProductProcess($list = [], $ConfigData = [], $users_discount = 1)
    {
        foreach ($list as $key => $value) {
            /* 未开启多规格则执行 */
            if (!isset($ConfigData['shop_open_spec']) || empty($ConfigData['shop_open_spec'])) {
                $value['spec_value_id']      = $value['spec_price'] = $value['spec_stock'] = 0;
                $list[$key]['spec_value_id'] = $list[$key]['spec_price'] = $list[$key]['spec_stock'] = 0;
            }
            /* END */

            /* 商品存在规格且价格不为空，则覆盖商品原来的价格 */
            if (!empty($value['spec_value_id']) && 'undefined' != $value['spec_value_id'] && $value['spec_price'] >= 0) {
                // 规格价格覆盖商品原价
                $list[$key]['users_price'] = $value['users_price'] = $value['spec_price'];
            }
            /* END */

            /* 商品原价 */
            $list[$key]['old_price'] = sprintf("%.2f", $list[$key]['users_price']);
            /* END */

            /* 计算折扣后的价格 */
            if (!empty($users_discount)) {
                // 会员折扣价
                $list[$key]['users_price'] = sprintf("%.2f", $value['users_price'] * $users_discount);
            }
            /* END */

            /* 秒杀商品多规格商品，则覆盖商品原来的价格 */
            if (!empty($value['is_sku']) && !empty($value['spec_value_id']) && 'undefined' != $value['spec_value_id'] && $value['seckill_price'] >= 0) {
                //秒杀商品秒杀库存覆盖原有规格库存
                $value['spec_stock'] = $value['seckill_stock'];
                // 规格秒杀价格覆盖商品原价
                $list[$key]['old_price'] =  $list[$key]['users_price'] = $value['users_price'] = $value['seckill_price'];
            }
            /* END */

            /* 商品存在规格且库存不为空，则覆盖商品原来的库存 */
            if (!empty($value['spec_stock'])) {
                // 规格库存覆盖商品库存
                $list[$key]['stock_count'] = $value['stock_count'] = $value['spec_stock'];
            }
            if ($value['product_num'] > $value['stock_count']) {
                // 购买数量超过最大库存量则默认购买最大库存量
                $list[$key]['stock_count'] = $list[$key]['product_num'] = $value['stock_count'];
            }
            /* END */

            // 图片处理
            $list[$key]['litpic'] = $this->get_default_pic($value['litpic'], true);

            /* 若库存为空则清除这条数据 */
            if (empty($value['stock_count'])) {
                unset($list[$key]);
                continue;
            }
            /* END */
        }

        return $list;
    }

    // 组装订单流程所需全局信息
    private function GetGlobalInfo($ConfigData = [], $CacheData = [])
    {
        $GlobalInfo = [
            // 温馨提示内容,为空则不展示
            'shop_prompt'        => !empty($ConfigData['shop_prompt']) ? $ConfigData['shop_prompt'] : '',
            // 是否开启线下支付(货到付款)
            'shop_open_offline'  => !empty($ConfigData['shop_open_offline']) ? $ConfigData['shop_open_offline'] : 0,
            // 是否开启运费设置
            'shop_open_shipping' => !empty($ConfigData['shop_open_shipping']) ? $ConfigData['shop_open_shipping'] : 0,
            // 初始化支付总额
            'TotalAmount'        => 0,
            // 初始化支付总额，包含运费
            'PayTotalAmount'     => 0,
            // 初始化总数
            'TotalNumber'        => 0,
            // 提交来源:0购物车;1直接下单
            'submit_order_type'  => !empty($CacheData) ? 1 : 0,
            // 1表示为虚拟订单
            'PromType'           => 1,
            // 虚拟订单文案
            'virtual_text1'      => '1、该产品为虚拟产品，仅支持在线支付且无需选择收货地址及运费计算。',
            'virtual_text2'      => '2、若产品是充值类产品，请将您的手机号或需充值的卡号填入留言中。',

        ];

        return $GlobalInfo;
    }

    // 获取用户下单时提交的收货地址及运费
    private function GetUsersAddrData($post = [], $PromType = 0)
    {
        $AddrData = [];
        if (0 == $PromType) {
            // 查询收货地址
            $AddrWhere   = [
                'addr_id'  => $post['addr_id'],
                'users_id' => $post['users_id'],
                'lang'     => get_home_lang()
            ];
            $AddressData = Db::name('shop_address')->where($AddrWhere)->find();
            if (empty($AddressData)) $this->error('收货地址不存在！');

            $shop_open_shipping = getUsersConfigData('shop.shop_open_shipping');
            $template_money     = '0.00';
            if (!empty($shop_open_shipping)) {
                // 通过省份获取运费模板中的运费价格
                $where['province_id'] = $AddressData['province'];
                $template_money       = Db::name('shop_shipping_template')->where($where)->getField('template_money');
                if (0 == $template_money) {
                    // 省份运费价格为0时，使用统一的运费价格，固定ID为100000
                    $where['province_id'] = 100000;
                    $template_money       = Db::name('shop_shipping_template')->where($where)->getField('template_money');
                }
            }
        }

        // 拼装数组
        $AddrData = [
            'consignee'    => !empty($AddressData['consignee']) ? $AddressData['consignee'] : '',
            'country'      => !empty($AddressData['country']) ? $AddressData['country'] : '',
            'province'     => !empty($AddressData['province']) ? $AddressData['province'] : '',
            'city'         => !empty($AddressData['city']) ? $AddressData['city'] : '',
            'district'     => !empty($AddressData['district']) ? $AddressData['district'] : '',
            'address'      => !empty($AddressData['address']) ? $AddressData['address'] : '',
            'mobile'       => !empty($AddressData['mobile']) ? $AddressData['mobile'] : '',
            'shipping_fee' => !empty($template_money) ? $template_money : 0,
        ];

        return $AddrData;
    }

    // 旧产品属性处理
    private function ProductAttrProcessing($value = array())
    {
        $attr_value = '';
        $AttrWhere  = [
            'a.aid'  => $value['aid'],
            'b.lang' => get_home_lang()
        ];
        $attrData   = Db::name('product_attr')
            ->alias('a')
            ->field('a.attr_value as value, b.attr_name as name')
            ->join('__PRODUCT_ATTRIBUTE__ b', 'a.attr_id = b.attr_id', 'LEFT')
            ->where($AttrWhere)
            ->order('b.sort_order asc, a.attr_id asc')
            ->select();
        foreach ($attrData as $val) {
            $attr_value .= $val['name'] . '：' . $val['value'] . '<br/>';
        }
        return $attr_value;
    }

    // 新商品属性处理
    private function ProductNewAttrProcessing($value = array())
    {
        $attr_value = '';
        $where      = [
            'a.list_id' => $value['attrlist_id'],
            'a.status'  => 1,
            'b.aid'     => $value['aid']
        ];
        $attrData   = Db::name('shop_product_attribute')
            ->alias('a')
            ->field('a.attr_name as name, b.attr_value as value')
            ->join('__SHOP_PRODUCT_ATTR__ b', 'a.attr_id = b.attr_id', 'LEFT')
            ->where($where)
            ->order('a.sort_order asc, a.attr_id asc')
            ->select();
        foreach ($attrData as $val) {
            $attr_value .= $val['name'] . '：' . $val['value'] . '<br/>';
        }
        return $attr_value;
    }

    // 商品规格处理
    private function ProductSpecProcessing($value = array())
    {
        $spec_value_s = '';
        if (!empty($value['spec_value_id'])) {
            $spec_value_id = explode('_', $value['spec_value_id']);
            if (!empty($spec_value_id)) {
                $SpecWhere       = [
                    'aid'           => $value['aid'],
                    'lang'          => get_home_lang(),
                    'spec_value_id' => ['IN', $spec_value_id],
                ];
                $ProductSpecData = Db::name("product_spec_data")->where($SpecWhere)->field('spec_name,spec_value')->select();
                foreach ($ProductSpecData as $spec_value) {
                    $spec_value_s .= $spec_value['spec_name'] . '：' . $spec_value['spec_value'] . '<br/>';
                }
            }
        }
        return $spec_value_s;
    }
    /*商城购物流程--END*/

    /* 判断商品库存 */
    public function IsSoldOut($post = [])
    {
        if (!empty($post['product_id'])) {
            if (!empty($post['spec_value_id'])) {
                $SpecWhere  = [
                    'aid'           => $post['product_id'],
                    'lang'          => get_home_lang(),
                    'spec_stock'    => ['>', 0],
                    'spec_value_id' => $post['spec_value_id']
                ];
                $find_field = 'spec_stock';
                if (!empty($post['active_time_id'])){
                    $find_field = 'seckill_stock';
                }
                $spec_stock = Db::name('product_spec_value')->where($SpecWhere)->getField($find_field);
                if (empty($spec_stock)) {
                    $this->error('商品已售罄！');
                }
                if ($spec_stock < $post['product_num']) {
                    $this->error('商品最大库存：' . $spec_stock);
                }
            } else {
                $ArchivesWhere = [
                    'aid'     => $post['product_id'],
                    'lang'    => get_home_lang(),
                    'arcrank' => ['>=', 0]
                ];
                if (!empty($post['active_time_id'])){
                    $stock_count = Db::name('sharp_goods')->where('aid',$post['product_id'])->getField('seckill_stock');
                }else{
                    $stock_count   = Db::name('archives')->where($ArchivesWhere)->getField('stock_count');
                }
                if (empty($stock_count)) {
                    $this->error('商品已售罄！');
                }
                if ($stock_count < $post['product_num']) {
                    $this->error('商品最大库存：' . $stock_count);
                }
            }
        }
    }
    /* END */

    /*收货地址--Start*/
    // 收货地址列表
    public function GetAllAddressList($users = [])
    {
        $ReturnData = [];
        $list       = !empty($users['address_1588820149']) ? $users['address_1588820149'] : [];
        $default_id = !empty($users['address_default_1588820149']) ? $users['address_default_1588820149']['addr_id'] : 0;
        if (!empty($list)) {
            // 将地址ID转成地址名称
            foreach ($list as $key => $value) {
                $list[$key]['provinceName'] = $this->GetRegionName($value['province']);
                $list[$key]['cityName']     = $this->GetRegionName($value['city']);
                $list[$key]['districtName'] = $this->GetRegionName($value['district']);
            }

            // 若没有默认收货地址则默认第一个地址为默认地址
            if (empty($default_id)) {
                $default_id = $list[0]['addr_id'];
                $where      = [
                    'addr_id'  => $default_id,
                    'users_id' => $this->users_id
                ];
                $update     = [
                    'is_default'  => 1,
                    'update_time' => getTime()
                ];
                Db::name('shop_address')->where($where)->update($update);
            }
        }

        // 返回数据
        $ReturnData = [
            'list'       => $list,
            'default_id' => $default_id
        ];

        $this->success('查询成功', null, $ReturnData);
    }

    // 获取单条收货地址
    public function GetFindAddrDetail($post = [], $users = [])
    {
        if (!empty($users['address_1588820149'])) {
            $ResultData['detail'] = [];
            foreach ($users['address_1588820149'] as $key => $value) {
                if ($post['addr_id'] == $value['addr_id']) {
                    $ResultData['detail'] = $value;
                    break;
                }
            }
            if (!empty($ResultData)) {
                // 查询地址名称
                $ProvinceName = $this->ProcessingProvince($this->GetRegionName($ResultData['detail']['province']));
                $CityName     = $this->GetRegionName($ResultData['detail']['city']);
                $DistrictName = $this->GetRegionName($ResultData['detail']['district']);
                // 拼装地址
                $region                         = $ProvinceName . ',' . $CityName . ',' . $DistrictName;
                $ResultData['detail']['region'] = [
                    'province' => $ProvinceName,
                    'city'     => $CityName,
                    'region'   => $DistrictName
                ];
                $ResultData['region']           = explode(',', $region);


                // 查询地址名称
                $ProvinceName = $this->GetRegionName($ResultData['detail']['province']);
                // 拼装地址
                $regionNew                         = $ProvinceName . ',' . $CityName . ',' . $DistrictName;
                $ResultData['detail']['regionNew'] = [
                    'province' => $ProvinceName,
                    'city'     => $CityName,
                    'region'   => $DistrictName
                ];
                $ResultData['regionNew']           = explode(',', $regionNew);

                $this->success('查询成功', null, $ResultData);
            }
        }
        $this->error('数据错误');
    }

    // 添加单条收货地址
    public function FindAddAddr($post = [], $users_id = null)
    {
        if (!empty($post['name']) && !empty($post['phone']) && !empty($post['region']) && !empty($users_id)) {
            /* 微信地址名称换取本站地址ID */
            $AddrData = $this->GetAddrData($post['region']);
            /* END */
            $AddData  = [
                'users_id'    => $users_id,
                'consignee'   => $post['name'],
                'country'     => 1,
                'province'    => $AddrData['province'],
                'city'        => $AddrData['city'],
                'district'    => $AddrData['district'],
                'address'     => $post['detail'],
                'mobile'      => $post['phone'],
                'lang'        => get_home_lang(),
                'add_time'    => getTime(),
                'update_time' => getTime()
            ];
            $ResultID = Db::name('shop_address')->add($AddData);
            if (!empty($ResultID)) $this->success('添加成功');
        }
        $this->error('数据错误');
    }

    // 编辑单条收货地址
    public function FindEditAddr($post = [], $users_id = null)
    {
        if (!empty($post['name']) && !empty($post['phone']) && !empty($post['region']) && !empty($users_id)) {
            /* 微信地址名称换取本站地址ID */
            $AddrData = $this->GetAddrData($post['region']);
            /* END */
            $UpData   = [
                'users_id'    => $users_id,
                'consignee'   => $post['name'],
                'country'     => 1,
                'province'    => $AddrData['province'],
                'city'        => $AddrData['city'],
                'district'    => $AddrData['district'],
                'address'     => $post['detail'],
                'mobile'      => $post['phone'],
                'update_time' => getTime()
            ];
            $ResultID = Db::name('shop_address')->where('addr_id', $post['addr_id'])->update($UpData);
            if (!empty($ResultID)) $this->success('编辑成功');
        }
        $this->error('数据错误');
    }

    // 设置默认地址
    public function SetDefaultAddr($post = [], $users_id = null)
    {
        if (empty($post['addr_id']) || empty($users_id)) $this->error('请刷新重试');
        /*设置默认地址*/
        $where    = [
            'addr_id'  => $post['addr_id'],
            'users_id' => $users_id
        ];
        $update   = [
            'is_default'  => 1,
            'update_time' => getTime()
        ];
        $ResultID = Db::name('shop_address')->where($where)->update($update);
        /* END */
        if (!empty($ResultID)) {
            /*将其他地址设置为非默认地址*/
            $where  = [
                'addr_id'  => ['NEQ', $post['addr_id']],
                'users_id' => $users_id
            ];
            $update = [
                'is_default'  => 0,
                'update_time' => getTime()
            ];
            Db::name('shop_address')->where($where)->update($update);
            /* END */
            $this->success('更新成功');
        }
        $this->error('更新失败，请刷新重试');
    }

    // 删除单条收货地址
    public function FindDelAddr($post = [], $users_id = null)
    {
        if (!empty($post['addr_id']) && !empty($users_id)) {
            $where    = [
                'users_id' => $users_id,
                'addr_id'  => $post['addr_id']
            ];
            $ResultID = Db::name('shop_address')->where($where)->delete();
            if (!empty($ResultID)) $this->success('操作成功');
        }
        $this->error('数据错误');
    }

    // 微信地址名称换取本站地址ID
    private function GetAddrData($region = null)
    {
        $region                 = explode(',', $region);
        $province               = substr($region[0], 0, 6); // 截取前两个汉字
        $ResultData['province'] = $this->GetRegionID(1, $province);
        $ResultData['city']     = $this->GetRegionID(2, $region[1], $ResultData['province']);
        $ResultData['district'] = $this->GetRegionID(3, $region[2], $ResultData['city']);

        return $ResultData;
    }

    // 微信地址名称换取本站地址ID
    private function GetRegionID($level = 1, $name = 1, $parent_id = 0)
    {
        $old_name = $name;

        // 当区域名称大于2两个字是，就去除末尾指定的字
        if ($level > 1 && strlen($name) >= 9) {
            $name = preg_replace('/(省|市|县|区|乡|镇)$/i', '', $name);
        }

        $where = [
            'level' => $level,
            'parent_id' => $parent_id,
            'name'  => ['LIKE', "{$name}%"],
        ];
        $id = Db::name('region')->where($where)->getField('id');
        if (empty($id) && 2 <= $level) {
            $addData = [
                'name'  => $old_name,
                'level' => $level,
                'parent_id' => $parent_id,
                'initial'   => getFirstCharter($old_name),
            ];
            $id = Db::name('region')->insertGetId($addData);
        }

        return $id;
    }

    // 地址ID换取地址名称
    private function GetRegionName($id = 1)
    {
        $name = Db::name('region')->where('id', $id)->getField('name');
        return $name;
    }

    // 特殊地区处理
    private function ProcessingProvince($province = null)
    {
        if (empty($province)) return $province;
        if ('山西' == $province) {
            $province = '山西省';
        } else if ('内蒙古' == $province) {
            $province = '内蒙古自治区';
        } else if ('广西' == $province) {
            $province = '广西壮族自治区';
        } else if ('西藏' == $province) {
            $province = '西藏自治区';
        } else if ('宁夏' == $province) {
            $province = '宁夏回族自治区';
        } else if ('新疆' == $province) {
            $province = '新疆维吾尔族自治区';
        } else if ('香港' == $province) {
            $province = '香港特别行政区';
        } else if ('澳门' == $province) {
            $province = '澳门特别行政区';
        } else if ('台湾' == $province) {
            $province = '台湾省';
        }
        return $province;
    }

    /* 收货地址--END */

    /**
     * 获取订单总数
     * @param $user
     * @param string $type
     * @return int|string
     * @throws \think\Exception
     */
    public function getOrderCount($users, $type = 'all')
    {
        if ($users === false) {
            return false;
        }
        // 筛选条件
        $filter = [];
        // 订单数据类型
        switch ($type) {
            case 'all': // 全部订单
                break;
            case 'payment'; // 待付款订单
                $filter['order_status'] = 0;
                break;
            case 'delivery'; // 待发货
                $filter['order_status'] = 1;
                break;
            case 'received'; // 待收货订单
                $filter['order_status'] = 2;
                break;
            case 'complete'; // 已完成
                $filter['order_status'] = 3;
                break;
        }

        $filter['users_id'] = $users['users_id'];
        $filter['lang']     = get_home_lang();
        $count              = Db::name('shop_order')
            ->where($filter)
            ->count();

        return $count;
    }

    /**
     * 用户中心订单列表
     * @param $users_id
     * @param string $type
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function getOrderList($users_id, $type = 'all')
    {
        // 筛选条件
        $filter = [];
        // 订单数据类型
        switch ($type) {
            case 'all': // 全部
                break;
            case 'payment'; // 待付款
                $filter['order_status'] = 0;
                break;
            case 'delivery'; // 待发货
                $filter['order_status'] = 1;
                break;
            case 'received'; // 待收货
                $filter['order_status'] = 2;
                break;
            case 'complete'; // 待评价
                $filter['order_status'] = 3;
                $filter['is_comment'] = 0;
                break;
        }

        $filter['users_id'] = $users_id;
        $filter['lang']     = get_home_lang();
        $result             = Db::name('shop_order')
            ->where($filter)
            ->order('order_id desc')
            ->paginate(15, false, [
                'query' => request()->param()
            ]);

        !empty($result) && $result = $result->toArray();
        $order_ids = get_arr_column($result['data'], 'order_id');

        // 订单商品详情
        $orderGoods = [];
        $goodsList  = Db::name('shop_order_details')->where([
            'order_id' => ['IN', $order_ids],
            'users_id' => $users_id,
        ])
            ->order('order_id desc')
            ->select();
        foreach ($goodsList as $key => $_goods) {
            $_goods['litpic']                  = $this->get_default_pic($_goods['litpic'], true);
            $orderGoods[$_goods['order_id']][] = $_goods;
        }

        // 模型名称
        $channeltype_row = \think\Cache::get('extra_global_channeltype');
        $channeltypeInfo = !empty($channeltype_row[2]) ? $channeltype_row[2] : [];
        $channel_ntitle  = !empty($channeltypeInfo['ntitle']) ? $channeltypeInfo['ntitle'] : '商品';

        $order_status_arr = config('global.order_status_arr');
        foreach ($result['data'] as $key => &$_order) {
            $_order['order_status_text'] = $order_status_arr[$_order['order_status']];
            $_order['add_time']          = MyDate('Y-m-d H:i:s', $_order['add_time']);
            $_order['goods']             = !empty($orderGoods[$_order['order_id']]) ? $orderGoods[$_order['order_id']] : [];
            $_order['channel_ntitle']    = '商品'; // $channel_ntitle;
        }
        return $result;
    }

    // 取消订单
    public function orderCancel($order_id, $users_id)
    {
        // 更新条件
        $Where = [
            'order_id' => $order_id,
            'users_id' => $users_id,
        ];
        // 更新数据
        $Data = [
            'order_status' => -1,
            'update_time'  => getTime(),
        ];
        // 更新订单主表
        $return = Db::name('shop_order')->where($Where)->update($Data);
        if (!empty($return)) {
            //还原优惠券
            $use_id = Db::name('shop_order')->where($Where)->value('use_id');
            if (!empty($use_id)){
                $coupon_use = Db::name('shop_coupon_use')->where(['use_id'=>$use_id])->find();
                if (!empty($coupon_use)){
                    if ($coupon_use['start_time'] <= getTime() && $coupon_use['end_time'] >= getTime()){
                        $use_update = ['use_status'=>0];
                    }else{
                        $use_update = ['use_status'=>2];
                    }
                    $use_update['use_time'] = 0;
                    $use_update['update_time'] = getTime();
                    Db::name('shop_coupon_use')->where(['use_id'=>$use_id,'users_id' => $users_id])->update($use_update);
                }
            }

            //查询是否是秒杀订单
            $order_info = Db::name('shop_order')->where($Where)->find();
            $productSpecValue = new \app\user\model\ProductSpecValue;
            if (20 == $order_info['order_source']){
                $productSpecValue->SaveSharpStock($order_id, $users_id,$order_info);
            }else{
                // 订单取消，还原单内产品数量
                $productSpecValue->SaveProducSpecValueStock($order_id, $users_id);
            }
            // 添加订单操作记录
            AddOrderAction($order_id, $users_id, 0, 0, 0, 0, '订单取消！', '会员关闭订单！');
            $this->success('订单取消成功！');
        }
    }

    // 确认收货
    public function orderReceipt($order_id, $users_id)
    {
        // 更新条件
        $Where = [
            'order_id' => $order_id,
            'users_id' => $users_id,
        ];
        // 更新数据
        $Data = [
            'order_status' => 3,
            'confirm_time' => getTime(),
            'update_time'  => getTime(),
        ];
        // 更新订单主表
        $return = Db::name('shop_order')->where($Where)->update($Data);
        if (!empty($return)) {
            // 更新数据
            $Data = [
                'update_time' => getTime(),
            ];
            // 更新订单明细表
            Db::name('shop_order_details')->where($Where)->update($Data);
            // 添加订单操作记录
            AddOrderAction($order_id, $users_id, 0, 3, 1, 1, '确认收货！', '会员已确认收到货物，订单完成！');
            $this->success('会员确认收货');
        } else {
            $this->error('订单号错误');
        }
    }

    /**
     * 订单详情
     * @param $order_id
     * @param null $user_id
     * @return null|static
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    public function getOrderDetail($order_id, $users_id)
    {
        // 公共条件
        $Where = [
            'order_id' => $order_id,
            'users_id' => $users_id,
        ];
        // 订单主表
        $result = Db::name("shop_order")->where($Where)->find();
        if (empty($result)) {
            $this->error('订单不存在！');
        }

        // 模型名称
        $channeltype_row          = \think\Cache::get('extra_global_channeltype');
        $channeltypeInfo          = !empty($channeltype_row[2]) ? $channeltype_row[2] : [];
        $result['channel_ntitle'] = '商品'; // !empty($channeltypeInfo['ntitle']) ? $channeltypeInfo['ntitle'] : '商品';
        // 获取订单状态
        $order_status_arr            = config('global.order_status_arr');
        $result['order_status_text'] = $order_status_arr[$result['order_status']];
        // 收货地址
        $result['province_text'] = $this->GetRegionName($result['province'], 1);
        $result['city_text']     = $this->GetRegionName($result['city'], 2);
        $result['district_text'] = $this->GetRegionName($result['district'], 3);
        // 下单时间
        $result['add_time'] = MyDate('Y-m-d H:i:s', $result['add_time']);
        // 订单总金额
        $result['TotalAmount'] = 0.00;
        // 订单明细表
        $result['goods'] = Db::name("shop_order_details")->order('product_price desc, product_name desc')->where($Where)->select();
        //虚拟商品拼接回复
        $virtual_delivery = '';
        // 虚拟发货
        $virtual_delivery_status = false;
        // 商品处理
        foreach ($result['goods'] as $key => $value) {
            //虚拟需要自动发货的商品卖家回复拼接
            if ($value['prom_type'] == 2 && 2 <= intval($result['order_status'])) {
                $virtual_delivery_status = true;
                //查询商品名称
                $product_title = Db::name('archives')->where('aid', $value['product_id'])->getField('title');
                //查网盘信息
                $netdisk = Db::name("product_netdisk")->where('aid', $value['product_id'])->find();
                if ($netdisk) {
                    $virtual_delivery .= "商品标题：" . $product_title . "</br>";
                    $virtual_delivery .= "网盘地址：" . $netdisk['netdisk_url'] . "</br>";
                    if (!empty($netdisk['netdisk_pwd'])) {
                        $virtual_delivery .= "提取码：" . $netdisk['netdisk_pwd'] . "</br>";
                    }
                    if (!empty($netdisk['unzip_pwd'])) {
                        $virtual_delivery .= "解压密码：" . $netdisk['unzip_pwd'] . "</br>";
                    }
                    $virtual_delivery .= "--------------------";
                }
            } elseif ($value['prom_type'] == 3 && 2 <= intval($result['order_status'])) {
                $virtual_delivery_status = true;
                //查询商品名称
                $product_title = Db::name('archives')->where('aid', $value['product_id'])->getField('title');
                //查网盘信息
                $netdisk = Db::name("product_netdisk")->where('aid', $value['product_id'])->find();
                if ($netdisk) {
                    $virtual_delivery .= "商品标题：" . $product_title . "</br>";
                    $virtual_delivery .= "文本内容：" . $netdisk['text_content'] . "</br>";
                    $virtual_delivery .= "--------------------";
                }
            } elseif ($value['prom_type'] == 1) {
                $virtual_delivery_status = true;
                //查询商品名称
                if (!empty($result['virtual_delivery'])) {
                    $product_title    = Db::name('archives')->where('aid', $value['product_id'])->getField('title');
                    $virtual_delivery .= "商品标题：" . $product_title . "</br>";
                    $virtual_delivery .= filter_line_return($result['virtual_delivery'], '</br>') . "</br>";
                    $virtual_delivery .= "--------------------";
                }
            }

            if (!empty($virtual_delivery)) {
                $result['virtual_delivery'] = $virtual_delivery;
            }

            // 商品属性处理
            $ValueData  = unserialize($value['data']);
            $spec_value = !empty($ValueData['spec_value']) ? htmlspecialchars_decode($ValueData['spec_value']) : '';
            $spec_value = htmlspecialchars_decode($spec_value);

            // 旧参数+规格值
            $attr_value                    = !empty($ValueData['attr_value']) ? htmlspecialchars_decode($ValueData['attr_value']) : '';
            $attr_value                    = htmlspecialchars_decode($attr_value);
            $spec_data                     = $spec_value . $attr_value;
            $spec_data                     = str_replace('<br/>', '；', $spec_data);
            $result['goods'][$key]['data'] = trim($spec_data, '；');

            // 新参数+规格值
            $attr_value_new                    = !empty($ValueData['attr_value_new']) ? htmlspecialchars_decode($ValueData['attr_value_new']) : '';
            $attr_value_new                    = htmlspecialchars_decode($attr_value_new);
            $new_spec_data                     = $spec_value . $attr_value_new;
            $new_spec_data                     = str_replace('<br/>', '；', $new_spec_data);
            $result['goods'][$key]['new_data'] = trim($new_spec_data, '；');
            if (!empty($result['goods'][$key]['new_data'])) {
                $result['goods'][$key]['new_data'] = $result['goods'][$key]['data'];
            }

            // 图片处理
            $result['goods'][$key]['litpic'] = $this->get_default_pic($value['litpic'], true);

            // 小计
            $result['goods'][$key]['subtotal'] = $value['product_price'] * $value['num'];
            $result['goods'][$key]['subtotal'] = sprintf("%.2f", $result['goods'][$key]['subtotal']);
            // 合计金额
            $result['TotalAmount'] += $result['goods'][$key]['subtotal'];
            $result['TotalAmount'] = sprintf("%.2f", $result['TotalAmount']);
        }

        return $result;
    }

    /**
     * 获取评价订单商品列表
     * @param $order_id
     * @param $users_id
     */
    public function getOrderComment($order_id, $users_id)
    {
        //判断该订单是否已经评价
        $comment_count = Db::name('shop_order_comment')->where(['order_id' => $order_id, 'users_id' => $users_id, 'lang' => get_home_lang()])->count();
        if (!empty($comment_count)) {
            $this->error('该订单已经评价！');
        }
        $order_data = $this->getOrderDetail($order_id, $users_id);

        return $order_data['goods'];
    }

    public function getSaveComment($post, $users_id)
    {
        $order_id = intval($post['order_id']);
        //判断该订单是否已经评价
        $comment_count = Db::name('shop_order_comment')->where(['order_id' => $order_id, 'users_id' => $users_id])->count();
        if (!empty($comment_count)) {
            $this->error('该订单已经评价！');
        }

        if (!empty($post['formData'])) {
            $post['formData'] = json_decode(htmlspecialchars_decode($post['formData']), true);
            $insert           = [];
            $detail_ids = [];
            $order_code = Db::name('shop_order')->where(['order_id'=>$order_id])->value('order_code');

            foreach ($post['formData'] as $k => $v) {
                $detail_ids[] = $v['order_details_id'];
                if (!empty($v['uploaded'])){
                    $v['uploaded'] = implode(',',$v['uploaded']);
                    $v['uploaded'] = serialize($v['uploaded']);
                }else{
                    $v['uploaded'] = '';
                }
                $insert[] = [
                    'upload_img'       => $v['uploaded'],
                    'total_score'      => $v['score'],
                    'content'          => serialize($v['content']),
                    'users_id'         => $users_id,
                    'order_id'         => $order_id,
                    'order_code'       => $order_code,
                    'product_id'       => $v['goods_id'],
                    'details_id'       => $v['order_details_id'],
                    'add_time'         => getTime(),
                    'update_time'      => getTime(),
                ];
            }
            $insert_data = Db::name('shop_order_comment')->insertAll($insert);
            if ($insert_data) {
                Db::name('shop_order')
                    ->where([
                        'order_id'     => $order_id,
                        'users_id'     => $users_id,
                        'order_status' => 3,
                    ])
                    ->update(['is_comment' => 1, 'update_time' => getTime()]);

                if (!empty($detail_ids)) {
                    Db::name('shop_order_details')
                        ->where('details_id', 'in', $detail_ids)
                        ->where('users_id', $users_id)
                        ->update(['is_comment' => 1, 'update_time' => getTime()]);
                }
                \think\Cache::clear('archives');

                $this->success('评价成功！');
            }
        }
        $this->error('评价失败！');
    }

    /**
     * 获取评论列表
     */
    public function getGoodsCommentList($goods_id = 0 ,$score = 0 ,$page = 1 )
    {
        $field='a.*,u.nickname,u.head_pic';
        // 筛选条件
        $condition = [
            'a.product_id' => $goods_id,
            'a.is_show' => 1,
            'a.lang' => get_home_lang(),
        ];
        $score > 0 && $condition['total_score'] = $score;

        $pagesize = empty($pagesize) ? config('paginate.list_rows') : $pagesize;
        $args = [$goods_id,$page,$pagesize,$score];
        $cacheKey = "plugins-model-DiyminiproMallShop-getGoodsCommentList-".md5(json_encode($args));
//        $result = cache($cacheKey);
        if (true || empty($result)) {
            $paginate = array(
                'page'  => $page,
            );
            $pages = Db::name('shop_order_comment')
                ->field($field)
                ->alias('a')
                ->join('users u','a.users_id = u.users_id')
                ->where($condition)
                ->order('a.add_time desc')
                ->paginate($pagesize, false, $paginate);

            $result = $pages->toArray();

            foreach ($result['data'] as $key => $val) {
                if ($val['is_anonymous'] == 1){
                    //匿名用户
                    $val['head_pic'] = get_head_pic();
                    $val['nickname'] = '匿名用户';
                }else{
                    if (empty($val['head_pic'])){
                        $val['head_pic'] = get_head_pic();
                    }
                }
                $val['head_pic'] = get_default_pic($val['head_pic'],true);

                if (isset($val['add_time'])) {
                    $val['add_time'] = date('Y-m-d H:i:s', $val['add_time']);
                }
                if (!empty($val['upload_img'])){
                    $val['upload_img'] = unserialize($val['upload_img']);
                    $val['upload_img'] = explode(',',$val['upload_img']);
                    foreach ($val['upload_img'] as $k => $v){
                        $val['upload_img'][$k] = get_default_pic($v,true);
                    }
                }
                if (!empty($val['content'])){
                    $val['content'] = unserialize($val['content']);
                }
                $result['data'][$key] = $val;
            }
            $score_type = Db::name('shop_order_comment')
                ->where([
                    'product_id' => $goods_id,
                    'is_show' => 1,
                    'lang' => get_home_lang(),
                ])->field('count(*) as count,total_score')
                ->group('total_score')
                ->getAllWithIndex('total_score');
            $result['count']['goods'] = isset($score_type[1]) ? $score_type[1]['count'] : 0;
            $result['count']['middle'] = isset($score_type[2]) ? $score_type[2]['count'] : 0;
            $result['count']['bad'] = isset($score_type[3]) ? $score_type[3]['count'] : 0;
            $result['count']['all'] = $result['count']['goods']+$result['count']['middle']+$result['count']['bad'] ;

            cache($cacheKey, $result, null, 'getGoodsCommentList');
        }
        return $result;
    }

    /**
     * 获取秒杀tabbar
     */
    public function GetSharpTabbar()
    {
        $time = strtotime(date('Y-m-d'));
        $hour = date('H');
        $where['b.status'] = 1;
        $where['a.status'] = 1;
        $where['b.is_del'] = 0;
        //非当日预告
        $active = Db::name('sharp_active_time')
            ->alias('a')
            ->field('a.*,b.active_date')
            ->join('sharp_active b','a.active_id = b.active_id')
            ->where($where)
            ->where('b.active_date', '>',$time)
            ->order('b.active_date asc,a.active_time asc')
            ->select();
        //当天
        $data = Db::name('sharp_active_time')
            ->alias('a')
            ->field('a.*,b.active_date')
            ->join('sharp_active b','a.active_id = b.active_id')
            ->where($where)
            ->where('b.active_date', $time)
            ->where('a.active_time', '>=',$hour)
            ->order('active_time asc')
            ->select();
        if (!empty($data) && !empty($active)){
            $data[] = $active[0];
        }else if (empty($data) && !empty($active)){
            $data[] = $active;
        }
        $date = date('Y-m-d');
        foreach ($data as $k => $v){
            if ($v['active_time'] < 10){
                $v['active_time'] = '0'.$v['active_time'];
            }
            $active_time = $v['active_time'];
            $v['active_time'] = $v['active_time'].':00';
            $date_time = $date.' '.$v['active_time']; //2020-15-2-15 15:00
            $time_plus = strtotime($date_time . "+1 hours"); //2020-15-2-15 15:00
            $time_plus = date('Y-m-d H:i',$time_plus); //2020-15-2-15 16:00
            if ($time == $v['active_date']){
                if ($active_time  ==  $hour){
                    $v['status'] = 10;
                    $arr = ['正在疯抢','已开抢','正在疯抢',$time_plus,$time_plus,$date_time];
                }else if ($active_time > $hour){
                    $v['status'] = 20;
                    $arr = [$v['active_time'].' 场预告','即将开抢','即将开抢',$date_time,$time_plus,$date_time];
                }
            }else{
                $v['status'] = 30;
                $arr = [$date_time.' 开始','预告',$date_time.' 开始',$date_time,$time_plus,$date_time];
            }
            $v['sharp_modular_text'] = $arr[0];
            $v['status_text'] = $arr[1];
            $v['status_text2'] = $arr[2];
            $v['count_down_time'] = $arr[3];
            $v['end_time'] = $arr[4];
            $v['start_time'] = $arr[5];
            $data[$k] = $v;
        }
        return $data;
    }

    /**
     * 获取秒杀列表
     */
    public function GetSharpIndex( $tid=0 , $page = 1 )
    {
        $pagesize = 10;
        $pagesize = empty($pagesize) ? config('paginate.list_rows') : $pagesize;
        $args = [$tid,$page,$pagesize];
        $cacheKey = "plugins-model-DiyminiproMallShop-GetSharpIndex-".md5(json_encode($args));
//        $result = cache($cacheKey);
        if (true || empty($result)) {
            $paginate = array(
                'page'  => $page,
            );
            $pages = Db::name('sharp_active_goods')
                ->alias('a')
                ->field('c.*,b.litpic,b.title,a.*,b.users_price')
                ->join('archives b','a.aid = b.aid')
                ->join('sharp_goods c','a.sharp_goods_id = c.sharp_goods_id')
                ->where('a.active_time_id',$tid)
                ->paginate($pagesize, false, $paginate);

            $result = $pages->toArray();

            $aids = [];
            foreach ($result['data'] as $k => $v){
                $result['data'][$k]['litpic'] = get_default_pic($v['litpic'],true);
                if (1 == $v['is_sku']){
                    $aids[] = $v['aid'];
                }
            }

            //多规格
            if (!empty($aids)) {
                $sku = Db::name('product_spec_value')
                    ->field('*,min(seckill_price) as seckill_price')
                    ->where('aid', 'in', $aids)
                    ->group('aid')
                    ->getAllWithIndex('aid');
            }
            //sales_actual //
            foreach ($result['data'] as $k => $v){
                if (1 == $v['is_sku'] && !empty($sku[$v['aid']])){
                    $v['seckill_price'] = $sku[$v['aid']]['seckill_price'];
                }
                $v['progress'] = 0;
                if (0 < $v['sales_actual']){
                    $count_stock = $v['sales_actual']+$v['seckill_stock']+$v['virtual_sales'];
                    $v['progress'] = intval(($v['sales_actual']+$v['virtual_sales'])/$count_stock*100);
                }else{
                    if (0 < $v['virtual_sales']){
                        $v['progress'] = intval($v['virtual_sales']/($v['virtual_sales']+$v['seckill_stock'])*100);;
                    }
                }
                if (0 < $v['virtual_sales']){
                    $v['sales_actual'] = $v['sales_actual'] +$v['virtual_sales'];

                }
                $result['data'][$k] = $v;
            }

            cache($cacheKey, $result, null, 'GetSharpIndex');
        }
        return $result;
    }

    /**
     * 获取物流动态信息
     * @param $express_name
     * @param $express_code
     * @param $express_order
     * @return array|bool
     */
    public function orderExpress($express_name, $express_code, $express_order, $timestamp = '')
    {
        $data = [
            'express_name'  => $express_name,
            'express_order' => $express_order
        ];
        // 快递100 请求查询接口
        $data['list'] = $this->orderExpressQuery($express_code, $express_order);
        if ($data['list'] === false) {
            $this->error('没有找到物流信息！');
            return false;
        }
        return $data;
    }

    /**
     * 执行查询
     * @param $express_code
     * @param $express_order
     * @return bool
     */
    private function orderExpressQuery($express_code, $express_order)
    {
        // 缓存索引
        $cacheIndex = 'plugins_model_DiyminiproMallShop_express_' . $express_code . '_' . $express_order;
        if ($data = Cache::get($cacheIndex)) {
            return $data;
        }

        // 查询物流链接
        empty($timestamp) && $timestamp = getTime();

        $postData   = [
            'postid'   => $express_order,
            'id'       => 1,
            'valicode' => '',
            'temp'     => $timestamp,
            'type'     => $express_code,
            'phone'    => '',
            'token'    => '',
            'platform' => 'MWWW',
            'coname'   => 'indexall',
        ];
        $expressUrl = "https://m.kuaidi100.com/query";
        $response   = httpRequest($expressUrl, 'POST', $postData);
        $express    = json_decode($response, true);
        // 记录错误信息
        if (!isset($express['message']) || $express['message'] != 'ok') {
            $msg = '查询失败';
            $this->error($msg);
            return false;
        }

        // $expressUrl = "https://m.kuaidi100.com/app/query/?com=".$express_code."&nu=".$express_order."&callbackurl=";

        // $mobileExpressUrl = "https://m.kuaidi100.com/index_all.html?type=".$result['express_code']."&postid=".$result['express_order']."&callbackurl=".$ReturnUrl;


        // 记录缓存, 时效5分钟
        Cache::set($cacheIndex, $express['data'], 300);
        return $express['data'];
    }

    /**
     * 执行查询
     * @param $express_code
     * @param $express_order
     * @return bool
     */
    private function orderExpressQuery2($express_code, $express_order)
    {
        // 缓存索引
        $cacheIndex = 'plugins_model_DiyminiproMallShop_express_' . $express_code . '_' . $express_order;
        if ($data = Cache::get($cacheIndex)) {
            return $data;
        }

        $key      = 'anFrVFyu8015'; // '快递100 授权KEY';
        $customer = 'B972EB01B5B32404957BE09C8FE85A51'; // '快递100 Customer';
        // 参数设置
        $postData         = [
            'customer' => $customer,
            'param'    => json_encode([
                'resultv2' => '1',
                'com'      => $express_code,
                'num'      => $express_order
            ])
        ];
        $postData['sign'] = strtoupper(md5($postData['param'] . $key . $postData['customer']));
        // 请求快递100 api
        $url     = 'http://poll.kuaidi100.com/poll/query.do';
        $result  = httpRequest($url, 'POST', $postData);
        $express = json_decode($result, true);
        // 记录错误信息
        if (isset($express['returnCode']) || !isset($express['data'])) {
            $msg = isset($express['message']) ? $express['message'] : '查询失败';
            $this->error($msg);
            return false;
        }
        // 记录缓存, 时效5分钟
        Cache::set($cacheIndex, $express['data'], 300);
        return $express['data'];
    }

    /**
     * 构建支付请求的参数
     * @param $users
     * @param $order
     * @param $payType
     * @return array
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    public function onOrderPayment($users, $order, $payType = 20)
    {
        if ($payType == 20) { // 微信支付
            return $this->onPaymentByWechat($users, $order);
        }
        return [];
    }

    /**
     * 构建微信支付请求
     * @param $users
     * @param $order
     * @return array
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    protected function onPaymentByWechat($users, $order)
    {
        // 统一下单API
        if (empty($this->diyminiproInfo['mchid']) || empty($this->diyminiproInfo['key'])) {
            return [
                'code' => 0,
                'msg'  => '很抱歉，请在网站小程序后台，完善微信支付配置！',
            ];
        }
        $openid  = Db::name('weapp_diyminipro_mall_users')->where(['users_id' => $users['users_id']])->getField('openid');
        $payment = $this->unifiedorder($order['order_code'], $openid, $order['order_amount']);

        return $payment;
    }

    /**
     * 统一下单API
     * @param $order_code
     * @param $openid
     * @param $totalFee
     * @return array
     * @throws BaseException
     */
    public function unifiedorder($order_code, $openid, $totalFee)
    {
        // 当前时间
        $time = getTime();
        // 生成随机字符串
        $nonceStr = md5($time . $openid);
        // 小程序配置
        $diyminiproMallSettingModel = new \weapp\DiyminiproMall\model\DiyminiproMallSettingModel;
        $inc                        = $diyminiproMallSettingModel->getSettingValue('setting');
        // API参数
        $params = [
            'appid'            => $inc['appId'],
            'attach'           => '微信小程序支付',
            'body'             => $order_code,
            'mch_id'           => $this->diyminiproInfo['mchid'],
            'nonce_str'        => $nonceStr,
            'notify_url'       => url('plugins/DiyminiproMall/wxpay_notify', [], true, true, 1, 1),  // 异步通知地址
            'openid'           => $openid,
            'out_trade_no'     => $order_code,
            'spbill_create_ip' => clientIP(),
            'total_fee'        => $totalFee * 100, // 价格:单位分
            'trade_type'       => 'JSAPI',
        ];
        // 生成签名
        $params['sign'] = model('DiyminiproMallUser')->makeSign($params, $this->diyminiproInfo['key']);
        // 请求API
        $url    = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $result = httpRequest($url, 'POST', model('DiyminiproMallUser')->toXml($params));
        $prepay = model('DiyminiproMallUser')->fromXml($result);
        // 请求失败
        if ($prepay['return_code'] === 'FAIL') {
            return [
                'code' => 0,
                'msg'  => "微信支付api：{$prepay['return_msg']}",
            ];
        }
        if ($prepay['result_code'] === 'FAIL') {
            return [
                'code' => 0,
                'msg'  => "微信支付api：{$prepay['err_code_des']}",
            ];
        }
        // 生成 nonce_str 供前端使用
        $paySign = $this->makePaySign($inc['appId'], $params['nonce_str'], $prepay['prepay_id'], $time);
        return [
            'prepay_id' => $prepay['prepay_id'],
            'nonceStr'  => $nonceStr,
            'timeStamp' => (string)$time,
            'paySign'   => $paySign
        ];
    }

    /**
     * 生成paySign
     * @param $nonceStr
     * @param $prepay_id
     * @param $timeStamp
     * @return string
     */
    private function makePaySign($appid, $nonceStr, $prepay_id, $timeStamp)
    {
        $data = [
            'appId'     => $appid,
            'nonceStr'  => $nonceStr,
            'package'   => 'prepay_id=' . $prepay_id,
            'signType'  => 'MD5',
            'timeStamp' => $timeStamp,
        ];
        // 签名步骤一：按字典序排序参数
        ksort($data);
        $string = model('DiyminiproMallUser')->toUrlParams($data);
        // 签名步骤二：在string后加入KEY
        $string = $string . '&key=' . $this->diyminiproInfo['key'];
        // 签名步骤三：MD5加密
        $string = md5($string);
        // 签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 获取我的优惠券
     */
    public function GetMyCouponList($users_id, $type = 0)
    {
        // 筛选条件
        $filter = [];
        //0-未使用 1-已使用 2-已过期
        $filter['a.use_status'] = $type;
        // 订单数据类型
        switch ($type) {
            case '0': // 未使用
                $filter['a.end_time'] = ['>=',getTime()];
                break;
            case '1'; // 已使用
                break;
            case '2'; // 已过期
                $filter['a.end_time'] = ['<',getTime()];
                break;
        }
        $filter['a.users_id'] = $users_id;
        $filter['b.coupon_id'] = ['>',0];

        $result = Db::name('shop_coupon_use')
            ->alias('a')
            ->join('shop_coupon b','a.coupon_id = b.coupon_id','left')
            ->where($filter)
            ->order('a.add_time desc')
            ->paginate(10, false, [
                'query' => request()->param()
            ]);
        !empty($result) && $result = $result->toArray();

        foreach ($result['data'] as $k => $v ) {
            $v['coupon_form_name'] = '满减券';
            $v['start_time'] = date('Y/m/d',$v['start_time']);
            $v['end_time'] = date('Y/m/d',$v['end_time']);
            switch ($v['coupon_type']) {
                case '1': // 未使用
                    $v['coupon_type_name'] = '全部商品';
                    break;
                case '2'; // 已使用
                    $v['coupon_type_name'] = '指定商品';
                    break;
                case '3'; // 已过期
                    $v['coupon_type_name'] = '指定分类';
                    break;
            }
            $result['data'][$k] = $v;
        }

        return $result;
    }
    /**
     * 领券中心
     */
    public function GetCouponCenter($users_id)
    {
        // 筛选条件
        $filter = [
            'status' => 1,
        ];
        $filter['start_date'] = ['<=',getTime()];
        $filter['end_date'] = ['>=',getTime()];

        $result = Db::name('shop_coupon')
            ->where($filter)
            ->order('sort_order asc,coupon_id desc')
            ->paginate(6, false, [
                'query' => request()->param()
            ]);
        if (!empty($result)) {
            $coupon_ids = [];
            $result = $result->toArray();
            foreach ($result['data'] as $k => $v ) {
                $coupon_ids[] = $v['coupon_id'];
                $v['coupon_form_name'] = '满减券';
                switch ($v['use_type']) {
                    case '1': // 固定期限有效
                        $v['start_date'] = date('Y/m/d',$v['start_date']);
                        $v['end_date'] = date('Y/m/d',$v['end_date']);
                        $v['use_type_name'] = $v['start_date'].'-'.$v['end_date'];
                        break;
                    case '2'; // 领取当天开始有效
                        $v['use_type_name'] = '领取'.$v['valid_days'].'天内有效';
                        break;
                    case '3'; // 领取次日开始有效
                        $v['use_type_name'] =  '领取次日开始'.$v['valid_days'].'天内有效';
                        break;
                }
                $result['data'][$k] = $v;
            }
            $have_where['users_id'] = $users_id;
            $have_where['coupon_id'] = ['in',$coupon_ids];
            $have_where['use_status'] = 0;
            $have = Db::name('shop_coupon_use')->where($have_where)->column('coupon_id');
            if (!empty($have)){
                foreach ($result['data'] as $k => $v ) {
                    if (in_array($v['coupon_id'],$have)){
                        $result['data'][$k]['geted'] = 1;//当前还有已领取未使用的
                    }
                }
            }
        }

        return $result;
    }
}