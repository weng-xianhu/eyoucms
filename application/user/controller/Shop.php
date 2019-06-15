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
 * Date: 2019-3-20
 */

namespace app\user\controller;

use think\Db;
use think\Config;
use think\Page;

class Shop extends Base
{
    // 初始化
    public function _initialize() {
        parent::_initialize();
        $this->users_db              = Db::name('users');               // 会员数据表
        $this->users_money_db        = Db::name('users_money');         // 会员金额明细表

        $this->shop_cart_db          = Db::name('shop_cart');           // 购物车表
        $this->shop_order_db         = Db::name('shop_order');          // 订单主表
        $this->shop_order_details_db = Db::name('shop_order_details');  // 订单明细表
        $this->shop_address_db       = Db::name('shop_address');        // 收货地址表

        $this->archives_db           = Db::name('archives');            // 产品表
        $this->product_attr_db       = Db::name('product_attr');        // 产品属性表
        $this->product_attribute_db  = Db::name('product_attribute');   // 产品属性标题表

        $this->region_db             = Db::name('region');                 // 三级联动地址总表
        $this->shipping_template_db  = Db::name('shop_shipping_template'); // 运费模板表

        $this->shop_model = model('Shop');  // 商城模型
	    // 商城微信配置信息
        $this->pay_wechat_config = unserialize(getUsersConfigData('pay.pay_wechat_config'));

        // 订单中心是否开启
        $redirect_url = '';
        $shop_open = getUsersConfigData('shop.shop_open');
        $web_users_switch = tpCache('web.web_users_switch');
        if (empty($shop_open)) { 
            // 订单功能关闭，立马跳到会员中心
            $redirect_url = url('user/Users/index');
            $msg = '订单中心尚未开启！';
        } else if (empty($web_users_switch)) { 
            // 前台会员中心已关闭，跳到首页
            $redirect_url = ROOT_DIR.'/';
            $msg = '会员中心尚未开启！';
        }
        if (!empty($redirect_url)) {
            Db::name('users_menu')->where([
                    'mca'   => 'user/Shop/shop_centre',
                    'lang'  => $this->home_lang,
                ])->update([
                    'status'    => 0,
                    'update_time' => getTime(),
                ]);
            $this->error($msg, $redirect_url);
            exit;
        }
        // --end
    }

    // 购物车列表
    public function shop_cart_list()
    {
        // 数据由标签调取生成
        return $this->fetch('users/shop_cart_list');
    }

    // 订单管理列表，订单中心
    public function shop_centre()
    {
        $result = [];
        // 应用搜索条件
        $keywords      = input('param.keywords/s');
        // 订单状态搜索
        $select_status = input('param.select_status');
        // 查询订单是否为空
        $result['data'] = $this->shop_model->GetOrderIsEmpty($this->users_id,$keywords,$select_status);
        // 是否移动端，1表示手机端，0表示PC端
        $result['IsMobile'] = isMobile() ? 1 : 0;
        // 菜单名称
        $result['title'] = Db::name('users_menu')->where([
                'mca'   => 'user/Shop/shop_centre',
                'lang'  => $this->home_lang,
            ])->getField('title');
        // 加载数据
        $eyou = [
            'field' => $result,
        ];
        $this->assign('eyou',$eyou);
        return $this->fetch('users/shop_centre');
    }

    // 订单数据详情
    public function shop_order_details()
    {
        if (IS_GET) {
            // 数据由标签调取生成
            return $this->fetch('users/shop_order_details');
        }else{
            $this->error('非法访问！');
        }
    }

    // 订单提交
    public function shop_under_order($error='true')
    {
        if (empty($error)) {
            $this->error('没有提交数据！');
        }
        // 获取当前页面URL，存入session，若操作添加地址后返回当前页面
        session($this->users_id.'_EyouShopOrderUrl', $this->request->url(true));
        // 数据由标签调取生成
        return $this->fetch('users/shop_under_order');
    }

    // 收货地址管理列表
    public function shop_address_list()
    {
        // 获取当前页面URL，存入session，若操作添加地址后返回当前页面
        session($this->users_id.'_EyouShopOrderUrl', $this->request->url(true));
        // 数据由标签调取生成
        return $this->fetch('users/shop_address_list');
    }

    // 取消订单
    public function shop_order_cancel()
    {
        if (IS_AJAX_POST) {
            $order_id = input('param.order_id');
            if (!empty($order_id)) {
                // 更新条件
                $Where = [
                    'order_id' => $order_id,
                    'users_id' => $this->users_id,
                    'lang'     => $this->home_lang,
                ];
                // 更新数据
                $Data  = [
                    'order_status' => '-1',
                    'update_time'  => getTime(),
                ];
                // 更新订单主表
                $return = $this->shop_order_db->where($Where)->update($Data);
                if (!empty($return)) {
                    // 添加订单操作记录
                    AddOrderAction($order_id,$this->users_id,'0','0','0','0','订单取消！','会员关闭订单！');
                    $this->success('订单已取消！');
                }else{
                    $this->error('操作失败！');
                }
            }
        }
    }

    // 立即购买
    public function shop_buy_now()
    {
        if (IS_AJAX_POST) {
            $param = input('param.');
            // 数量不可为空
            if (empty($param['num']) || 0 > $param['num']) {
                $this->error('请选择数量！');
            }
            // 查询条件
            $archives_where = [
                'arcrank' => array('egt','0'), //带审核稿件不查询
                'aid'     => $param['aid'],
                'lang'    => $this->home_lang,
            ];
            $count = $this->archives_db->where($archives_where)->count();
            // 跳转下单页
            if (!empty($count)) {
                // 对ID和订单号加密，拼装url路径
                $querydata = [
                    'aid'         => $param['aid'],
                    'product_num' => $param['num'],
                ];
                $querystr   = base64_encode(serialize($querydata));
                $url = urldecode(url('user/Shop/shop_under_order', ['querystr'=>$querystr]));
                $this->success('立即购买！',$url);
            }else{
                $this->error('该商品不存在或已下架！');
            }
        }else {
            $this->error('非法访问！');
        }
    }

    // 添加购物车数据
    public function shop_add_cart()
    {
        if (IS_AJAX_POST) {
            $param = input('param.');
            // 数量不可为空
            if (empty($param['num']) || 0 > $param['num']) {
                $this->error('请选择数量！');
            }
            // 查询条件
            $archives_where = [
                'arcrank' => array('egt','0'), //带审核稿件不查询
                'aid'     => $param['aid'],
                'lang'    => $this->home_lang,
            ];
            $count = $this->archives_db->where($archives_where)->count();
            // 加入购物车处理
            if (!empty($count)) {
                // 查询条件
                $cart_where = [
                    'users_id'   => $this->users_id,
                    'product_id' => $param['aid'],
                    'lang'       => $this->home_lang,
                ];
                $product_num = $this->shop_cart_db->where($cart_where)->getField('product_num');
                if (!empty($product_num)) {
                    // 购物车内已有相同产品，进行数量更新。
                    $data['product_num'] = $param['num'] + $product_num; //与购物车数量进行叠加
                    $data['update_time'] = getTime();
                    $cart_id = $this->shop_cart_db->where($cart_where)->update($data);
                }else{
                    // 购物车内还未有相同产品，进行添加。
                    $data['users_id']    = $this->users_id;
                    $data['product_id']  = $param['aid'];
                    $data['product_num'] = $param['num'];
                    $data['add_time']    = getTime();
                    $cart_id = $this->shop_cart_db->add($data);
                }
                if (!empty($cart_id)) {
                    $this->success('加入购物车成功！');
                }else{
                    $this->error('加入购物车失败！');
                }
            }else{
                $this->error('该商品不存在或已下架！');
            }
        }else {
            $this->error('非法访问！');
        }
    }

    // 统一修改购物车数量
    // symbol 加或减数量或直接修改数量
    public function cart_unified_algorithm(){
        if (IS_AJAX_POST) {
            $aid    = input('post.aid');
            $symbol = input('post.symbol');
            $num    = input('post.num');
            // 查询条件
            $archives_where = [
                'arcrank' => array('egt','0'),
                'aid'     => $aid,
                'lang'    => $this->home_lang,
            ];
            $archives_count = $this->archives_db->where($archives_where)->count();
            if (!empty($archives_count)) {
                // 查询条件
                $cart_where = [
                    'users_id'    => $this->users_id,
                    'product_id'  => $aid,
                    'lang'        => $this->home_lang,
                ];
                // 判断追加查询条件，当减数量时，商品数量最少为1
                if ('-' == $symbol) {
                    $cart_where['product_num'] = array('gt','1');
                }
                $product_num = $this->shop_cart_db->where($cart_where)->getField('product_num');
                // 处理购物车产品数量
                if (!empty($product_num)) {
                    // 更新数组
                    if ('+' == $symbol) {
                        $data['product_num'] = $product_num + 1;
                    }else if ('-' == $symbol) {
                        $data['product_num'] = $product_num - 1;
                    }else if ('change' == $symbol) {
                        $data['product_num'] = $num;
                    }
                    $data['update_time'] = getTime();
                    // 更新数据
                    $cart_id = $this->shop_cart_db->where($cart_where)->update($data);

                    // 计算金额数量
                    $CaerWhere = [
                        'a.users_id' => $this->users_id,
                        'a.lang'     => $this->home_lang,
                        'a.selected' => 1,
                    ];
                    $CartData = $this->shop_cart_db
                        ->field('sum(a.product_num) as num, sum(a.product_num * b.users_price) as price')
                        ->alias('a') 
                        ->join('__ARCHIVES__ b', 'a.product_id = b.aid', 'LEFT')
                        ->where($CaerWhere)
                        ->find();
                    if (empty($CartData['num']) && empty($CartData['price'])) {
                        $CartData['num']   = '0';
                        $CartData['price'] = '0.00';
                    }
                    if (!empty($cart_id)) {
                        $this->success('操作成功！','',['NumberVal'=>$CartData['num'],'AmountVal'=>$CartData['price']]);
                    }
                }else{
                    $this->error('商品数量最少为1','',['error'=>'0']);
                }
            }else{
                $this->error('该商品不存在或已下架！');
            }
        }
    }

    // 删除购物车内的产品
    public function cart_del()
    {
        if (IS_AJAX_POST) {
            $cart_id = input('post.cart_id');
            if (!empty($cart_id)) {
                // 删除条件
                $cart_where = [
                    'cart_id'  => $cart_id,
                    'users_id' => $this->users_id,
                    'lang'     => $this->home_lang,
                ];
                // 删除数据
                $return = $this->shop_cart_db->where($cart_where)->delete();
            }
            if (!empty($return)) {
                $this->success('操作成功！');
            }else{
                $this->error('删除失败！');
            }
        }
    }

    // 选中产品
    public function cart_checked()
    {
        if (IS_AJAX_POST) {
            $cart_id  = input('post.cart_id');
            $selected = input('post.selected');
            // 更新数组
            if (!empty($selected)) {
                $selected = '0';
            }else{
                $selected = '1';
            }
            $data['selected']    = $selected;
            $data['update_time'] = getTime();
            // 更新条件
            if ('*' == $cart_id) {
                $cart_where = [
                    'users_id' => $this->users_id,
                    'lang'     => $this->home_lang,
                ];
            }else{
                $cart_where = [
                    'cart_id'  => $cart_id,
                    'users_id' => $this->users_id,
                    'lang'     => $this->home_lang,
                ];
            }
            // 更新数据
            $return = $this->shop_cart_db->where($cart_where)->update($data);
            if (!empty($return)) {
                $this->success('操作成功！');
            }else{
                $this->error('操作失败！');
            }
        }
    }

    public function shop_wechat_pay_select()
    {
        $ReturnOrderData = session($this->users_id.'_ReturnOrderData');
        if (empty($ReturnOrderData)) {
            $url = session($this->users_id.'_EyouShopOrderUrl');
            $this->error('订单支付异常，请刷新重新下单~',$url);
        }
        $eyou = [
            'field' => $ReturnOrderData,
        ];
        $this->assign('eyou',$eyou);
        return $this->fetch('users/shop_wechat_pay_select');
    }

    // 订单提交处理逻辑，添加商品信息及计算价格等
    public function shop_payment_page()
    {
        if (IS_POST) {
            // 提交的订单信息判断
            $post = input('post.');
            if (empty($post)) {
                $this->error('订单生成失败，商品数据有误！'); 
            }
            if (!empty($post['aid'])) {
                $aid  = unserialize(base64_decode($post['aid']));
            }
            if (!empty($post['num'])) {
                $num  = unserialize(base64_decode($post['num']));
            }
            if (!empty($post['type'])) {
                $type = unserialize(base64_decode($post['type']));
            }

            // 产品ID是否存在
            if (!empty($aid)) {
                // 商品数量判断
                if ($num <= '0') {
                    $this->error('订单生成失败，商品数量有误！');
                }
                // 订单来源判断
                if ($type != '1') {
                    $this->error('订单生成失败，提交来源有误！');
                }
                // 立即购买查询条件
                $ArchivesWhere = [
                    'aid'  => $aid,
                    'lang' => $this->home_lang,
                ];
                $list = $this->archives_db->field('aid,title,litpic,users_price,prom_type')->where($ArchivesWhere)->select();
                $list[0]['product_num']      = $num;
                $list[0]['under_order_type'] = $type;
            }else{
                // 购物车查询条件
                $cart_where = [
                    'a.users_id' => $this->users_id,
                    'a.lang'     => $this->home_lang,
                    'a.selected' => 1,
                ];
                $list = $this->shop_cart_db->field('a.*,b.aid,b.title,b.litpic,b.users_price,b.prom_type')
                    ->alias('a') 
                    ->join('__ARCHIVES__ b', 'a.product_id = b.aid', 'LEFT')
                    ->where($cart_where)
                    ->select();                 
            }
            
            // 没有相应的产品
            if (empty($list)) {
                $this->error('订单生成失败，没有相应的产品！');
            }

            // 产品数据处理
            $PromType = '1'; // 1表示为虚拟订单
            $TotalAmount = $TotalNumber = '';
            foreach ($list as $value) {
                if (!empty($value['users_price']) && !empty($value['product_num'])) {
                    // 合计金额
                    $TotalAmount += sprintf("%.2f", $value['users_price'] * $value['product_num']);
                    // 合计数量
                    $TotalNumber += $value['product_num'];
                    // 判断订单类型，目前逻辑：一个订单中，只要存在一个普通产品(实物产品，需要发货物流)，则为普通订单
                    if (empty($value['prom_type'])) {
                        $PromType = '0';// 0表示为普通订单
                    }
                }
            }

            $AddrData = [];
            // 非虚拟订单则查询运费信息
            if (empty($PromType)) {
                // 没有选择收货地址
                if (empty($post['addr_id'])) {
                    // 在微信端并且不在小程序中
                    if (isWeixin() && !isWeixinApplets()) {
                        // 跳转至收货地址添加选择页
                        $get_addr_url = url('user/Shop/shop_get_wechat_addr');
                        $is_gourl['is_gourl'] = 1;
                        $this->success('101:选择添加地址方式',$get_addr_url,$is_gourl);exit;
                    }else{
                        $this->error('订单生成失败，请添加收货地址！');
                    }
                }

                // 查询收货地址
                $AddrWhere = [
                    'addr_id'  => $post['addr_id'],
                    'users_id' => $this->users_id,
                    'lang'     => $this->home_lang,
                ];
                $AddressData = $this->shop_address_db->where($AddrWhere)->find();
                if (empty($AddressData)) {
                    if (isWeixin() && !isWeixinApplets()) {
                        // 跳转至收货地址添加选择页
                        $get_addr_url = url('user/Shop/shop_get_wechat_addr');
                        $is_gourl['is_gourl'] = 1;
                        $this->success('102:选择添加地址方式',$get_addr_url,$is_gourl);exit;
                    }else{
                        $this->error('订单生成失败，请添加收货地址！');
                    }
                }

                $shop_open_shipping = getUsersConfigData('shop.shop_open_shipping');
                $template_money = '0.00';
                if (!empty($shop_open_shipping)) {
                    // 通过省份获取运费模板中的运费价格
                    $template_money = $this->shipping_template_db->where('province_id',$AddressData['province'])->getField('template_money');
                    if ('0.00' == $template_money) {
                        // 省份运费价格为0时，使用统一的运费价格，固定ID为100000
                        $template_money = $this->shipping_template_db->where('province_id','100000')->getField('template_money');
                    }
                    // 合计金额加上运费价格
                    $TotalAmount += $template_money;
                }

                // 拼装数组
                $AddrData = [
                    'consignee'    => $AddressData['consignee'],
                    'country'      => $AddressData['country'],
                    'province'     => $AddressData['province'],
                    'city'         => $AddressData['city'],
                    'district'     => $AddressData['district'],
                    'address'      => $AddressData['address'],
                    'mobile'       => $AddressData['mobile'],
                    'shipping_fee' => $template_money,
                ];
            }

            // 添加到订单主表
            $time = getTime();
            $OrderData = [
                'order_code'        => date('Ymd').$time.rand(10,100), //订单生成规则
                'users_id'          => $this->users_id,
                'order_status'      => 0, // 订单未付款
                'add_time'          => $time,
                'payment_method'    => $post['payment_method'],
                'order_total_amount'=> $TotalAmount,
                'order_amount'      => $TotalAmount,
                'order_total_num'   => $TotalNumber,
                'prom_type'         => $PromType,
                'user_note'         => $post['message'],
                'lang'              => $this->home_lang,
            ];
            
            // 存在收货地址则追加合并到主表数组
            if (!empty($AddrData)) {
                $OrderData = array_merge($OrderData, $AddrData);
            }

            if (isMobile() && isWeixin()) {
                $OrderData['pay_name'] = 'wechat';// 如果在微信端中则默认为微信支付
                $OrderData['wechat_pay_type'] = 'WeChatInternal';// 如果在微信端中则默认为微信端调起支付
            }

            if ('1' == $post['payment_method']) {
                // 追加添加到订单主表的数组
                $OrderData['order_status'] = 1; // 标记已付款
                $OrderData['pay_time']     = $time;
                $OrderData['pay_name']     = 'delivery_pay';// 货到付款
                $OrderData['wechat_pay_type'] = ''; // 选择货到付款，则去掉微信端调起支付标记
                $OrderData['update_time']  = $time;
            }

            // 数据验证
            $rule = [
                'payment_method' => 'require|token',
            ];
            $message = [
                'payment_method.require' => '不可为空！',
            ];
            $validate = new \think\Validate($rule, $message);
            if(!$validate->check($post)){
                $this->error('不可连续提交订单！');
            }

            $OrderId = $this->shop_order_db->add($OrderData);
            if (!empty($OrderId)) {
                $cart_ids   = '';
                $attr_value = '';
                // 添加到订单明细表
                foreach ($list as $key => $value) {
                    // 产品属性处理
                    $AttrWhere = [
                        'a.aid'     => $value['aid'],
                        'b.lang'    => $this->home_lang,
                    ];
                    $AttrData = Db::name('product_attr')
                        ->alias('a')
                        ->field('a.attr_value,b.attr_name')
                        ->join('__PRODUCT_ATTRIBUTE__ b', 'a.attr_id = b.attr_id', 'LEFT')
                        ->where($AttrWhere)
                        ->order('b.sort_order asc, a.attr_id asc')
                        ->select();
                    foreach ($AttrData as $val) {
                        $attr_value .= $val['attr_name'].'：'.$val['attr_value'].'<br/>';
                    }

                    // 处理产品属性
                    $Data = [
                        'attr_value' => htmlspecialchars($attr_value),
                        // 后续添加
                    ];

                    $OrderDetailsData[] = [
                        'order_id'      => $OrderId,
                        'users_id'      => $this->users_id,
                        'product_id'    => $value['aid'],
                        'product_name'  => $value['title'],
                        'num'           => $value['product_num'],
                        'data'          => serialize($Data),
                        'product_price' => $value['users_price'],
                        'prom_type'     => $value['prom_type'],
                        'litpic'        => $value['litpic'],
                        'add_time'      => $time,
                        'lang'          => $this->home_lang,
                    ];
                    if (empty($value['under_order_type'])) {
                        // 处理购物车ID
                        if ($key > '0') {
                            $cart_ids .= ',';
                        }
                        $cart_ids .= $value['cart_id'];
                    }
                }
                $DetailsId = $this->shop_order_details_db->insertAll($OrderDetailsData);

                if (!empty($OrderId) && !empty($DetailsId)) {
                    // 清理购物车中已下单的ID
                    if (!empty($cart_ids)) {
                        $this->shop_cart_db->where('cart_id','IN',$cart_ids)->delete();
                    }

                    // 添加订单操作记录
                    AddOrderAction($OrderId,$this->users_id);

                    if ('0' == $post['payment_method']) {
                        // 选择在线付款并且在手机微信端、小程序中则返回订单ID，订单号，订单交易类型
                        if (isMobile() && isWeixin()) {
                            if (!empty($this->users['open_id'])) {
                                $ReturnOrderData = [
                                    'unified_id'       => $OrderId,
                                    'unified_number'   => $OrderData['order_code'],
                                    'transaction_type' => 2, // 订单支付购买
                                    'order_total_amount' => $TotalAmount,
                                    'order_source'     => 1, // 提交订单页
                                    'is_gourl'         => 1,
                                ];
                                if ($this->users['users_money'] <= '0.00') {
                                    // 余额为0
                                    $ReturnOrderData['is_gourl'] = 0;
                                    $this->success('订单已生成！', null, $ReturnOrderData);
                                }else{
                                    // 余额不为0
                                    $url = url('user/Shop/shop_wechat_pay_select');
                                    session($this->users_id.'_ReturnOrderData',$ReturnOrderData);
                                    $this->success('订单已生成！', $url, $ReturnOrderData);
                                }
                            }else{
                                // 如果会员没有openid则跳转到支付页面进行支付
                                // 在线付款时，跳转至付款页
                                // 对ID和订单号加密，拼装url路径
                                $querydata = [
                                    'order_id'   => $OrderId,
                                    'order_code' => $OrderData['order_code'],
                                ];
                                $querystr   = base64_encode(serialize($querydata));
                                $PaymentUrl = urldecode(url('user/Pay/pay_recharge_detail',['querystr'=>$querystr]));
                                $ReturnOrderData = [
                                    'is_gourl'         => 1,
                                ];
                                $this->success('订单已生成！',$PaymentUrl,$ReturnOrderData);
                            }
                        }else{
                            // 在线付款时，跳转至付款页
                            // 对ID和订单号加密，拼装url路径
                            $querydata = [
                                'order_id'   => $OrderId,
                                'order_code' => $OrderData['order_code'],
                            ];
                            $querystr   = base64_encode(serialize($querydata));
                            $PaymentUrl = urldecode(url('user/Pay/pay_recharge_detail',['querystr'=>$querystr]));
                        }
                    }else{
                        // 无需跳转付款页，直接跳转订单列表页
                        $PaymentUrl = urldecode(url('user/Shop/shop_centre'));
                        
                        // 货到付款时，再次添加一条订单操作记录
                        AddOrderAction($OrderId,$this->users_id,'0','1','0','1','货到付款！','会员选择货到付款，款项由快递代收！');
                        $ReturnOrderData = [
                            'is_gourl'         => 1,
                        ];
                        $this->success('订单已生成！',$PaymentUrl,$ReturnOrderData);
                    }
                    $this->success('订单已生成！',$PaymentUrl);
                }else{
                    $this->error('订单生成失败，商品数据有误！');
                }
            }else{
                $this->error('订单生成失败，商品数据有误！');
            }
        }
    }

    // 添加收货地址
    public function shop_add_address()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            if (empty($post['consignee'])) {
                $this->error('收货人姓名不可为空！');
            }
            if (empty($post['mobile'])) {
                $this->error('收货人手机不可为空！');
            }
            if (empty($post['province'])) {
                $this->error('收货省份不可为空！');
            }
            if (empty($post['address'])) {
                $this->error('详细地址不可为空！');
            }
            // 添加数据
            $post['users_id'] = $this->users_id;
            $post['add_time'] = getTime();
            $post['lang']     = $this->home_lang;
            if (isMobile() && isWeixin()) {
                // 在手机微信端、小程序中则把新增的收货地址设置为默认地址
                $post['is_default'] = 1;// 设置为默认地址
            }
            $addr_id = $this->shop_address_db->add($post);
            if (isMobile() && isWeixin() && !empty($addr_id)) {
                // 把对应会员下的所有地址改为非默认
                $AddressWhere = [
                    'addr_id'  => array('NEQ',$addr_id),
                    'users_id' => $this->users_id,
                    'lang'     => $this->home_lang,
                ];
                $data_new['is_default']  = 0;// 设置为非默认地址
                $data_new['update_time'] = getTime();
                $this->shop_address_db->where($AddressWhere)->update($data_new);
                $this->success('添加成功！',session($this->users_id.'_EyouShopOrderUrl'));exit;
            }

            // 根据地址ID查询相应的中文名字
            $post['country']  = '中国';
            $post['province'] = get_province_name($post['province']);
            $post['city']     = get_city_name($post['city']);
            $post['district'] = get_area_name($post['district']);
            if (!empty($addr_id)) {
                $post['addr_id'] = $addr_id;
                $this->success('添加成功！','',$post);
            }else{
                $this->error('数据有误！');
            }
        }

        $types = input('param.type');
        if ('list' == $types || 'order' == $types || 'order_new' == $types) {
            $Where = [
                'users_id' => $this->users_id,
                'lang'     => $this->home_lang,
            ];
            $addr_num = $this->shop_address_db->where($Where)->count();

            $eyou = [
                'field'    => [
                    'Province' => get_province_list(),
                    'types'    => $types,
                    'addr_num' => $addr_num,
                ],
            ];
            $this->assign('eyou',$eyou);
        }else{
            $this->error('非法来源！');
        }

        return $this->fetch('users/shop_add_address');
    }

    // 更新收货地址
    public function shop_edit_address()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            if (empty($post['consignee'])) {
                $this->error('收货人姓名不可为空！');
            }
            if (empty($post['mobile'])) {
                $this->error('收货人手机不可为空！');
            }
            if (empty($post['province'])) {
                $this->error('收货省份不可为空！');
            }
            if (empty($post['address'])) {
                $this->error('详细地址不可为空！');
            }
            // 更新条件及数据
            $post['users_id'] = $this->users_id;
            $post['add_time'] = getTime();
            $post['lang']     = $this->home_lang;

            $AddrWhere = [
                'addr_id'  => $post['addr_id'],
                'users_id' => $this->users_id,
                'lang'     => $this->home_lang,
            ];

            $addr_id = $this->shop_address_db->where($AddrWhere)->update($post);

            // 根据地址ID查询相应的中文名字
            $post['country']  = '中国';
            $post['province'] = get_province_name($post['province']);
            $post['city']     = get_city_name($post['city']);
            $post['district'] = get_area_name($post['district']);
            if (!empty($addr_id)) {
                $this->success('修改成功！','',$post);
            }else{
                $this->error('数据有误！');
            }
        }

        $AddrId   = input('param.addr_id');
        $AddrWhere = [
            'addr_id'  => $AddrId,
            'users_id' => $this->users_id,
            'lang'     => $this->home_lang,
        ];
        // 根据地址ID查询相应的中文名字
        $AddrData = $this->shop_address_db->where($AddrWhere)->find();
        if (empty($AddrData)) {
            $this->error('数据有误！');
        }
        $AddrData['country']  = '中国'; //国家
        $AddrData['Province'] = get_province_list(); // 省份
        $AddrData['City']     = $this->region_db->where('parent_id',$AddrData['province'])->select(); // 城市
        $AddrData['District'] = $this->region_db->where('parent_id',$AddrData['city'])->select(); // 县/区/镇
        $eyou = [
            'field' => $AddrData,
        ];
        $this->assign('eyou',$eyou);
        return $this->fetch('users/shop_edit_address');
    }

    // 删除收货地址
    public function shop_del_address()
    {
        if (IS_POST) {
            $addr_id = input('post.addr_id/d');
            $Where = [
                'addr_id'  => $addr_id,
                'users_id' => $this->users_id,
                'lang'     => $this->home_lang,
            ];
            $return = $this->shop_address_db->where($Where)->delete();
            if ($return) {
                $this->success('删除成功！');
            }else{
                $this->error('删除失败！');
            }
        }
    }

    // 更新收货地址，设置为默认地址
    public function shop_set_default_address()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 更新条件及数据
            $post['users_id']   = $this->users_id;
            $post['is_default'] = '1'; //设置为默认
            $post['add_time']   = getTime();
            $post['lang']       = $this->home_lang;

            $AddrWhere = [
                'addr_id'  => $post['addr_id'],
                'users_id' => $this->users_id,
                'lang'     => $this->home_lang,
            ];
            $addr_id = $this->shop_address_db->where($AddrWhere)->update($post);
            if (!empty($addr_id)) {
                // 把对应会员下的所有地址改为非默认
                $AddressWhere = [
                    'addr_id'  => array('NEQ',$post['addr_id']),
                    'users_id' => $this->users_id,
                    'lang'     => $this->home_lang,
                ];
                $data['is_default']  = '0';// 设置为非默认
                $data['update_time'] = getTime();
                $this->shop_address_db->where($AddressWhere)->update($data);
                $this->success('设置成功！');
            }else{
                $this->error('数据有误！');
            }
        }
    }

    // 查询运费
    public function shop_inquiry_shipping()
    {
        if (IS_AJAX_POST) {
            $shop_open_shipping = getUsersConfigData('shop.shop_open_shipping');
            if (empty($shop_open_shipping)) {
                $this->success('未开启运费！','',0);
            }
            // 查询会员收货地址，获取省份
            $addr_id = input('post.addr_id');
            $where = [
                'addr_id'  => $addr_id,
                'users_id' => $this->users_id,
                'lang'     => $this->home_lang,
            ];
            $province = $this->shop_address_db->where($where)->getField('province');

            // 通过省份获取运费模板中的运费价格
            $template_money = $this->shipping_template_db->where('province_id',$province)->getField('template_money');
            if ('0.00' == $template_money) {
                // 省份运费价格为0时，使用统一的运费价格，固定ID为100000
                $template_money = $this->shipping_template_db->where('province_id','100000')->getField('template_money');
            }
            $this->success('查询成功！','',$template_money);
        }else{
            $this->error('订单号错误');
        }
    }

    // 联动地址获取
    public function get_region_data(){
        $parent_id  = input('param.parent_id/d');
        $RegionData = $this->region_db->where("parent_id",$parent_id)->select();
        $html = '';
        if($RegionData){
            // 拼装下拉选项
            foreach($RegionData as $value){
                $html .= "<option value='{$value['id']}'>{$value['name']}</option>";
            }
        }
        echo json_encode($html);
    }

    // 会员提醒收货
    public function shop_order_remind()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 添加订单操作记录
            AddOrderAction($post['order_id'],$this->users_id,'0','1','0','1','提醒成功！','会员提醒管理员及时发货！');
            $this->success('提醒成功！');
        }else{
            $this->error('订单号错误');
        }
    }

    // 会员确认收货
    public function shop_member_confirm()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 更新条件
            $Where = [
                'order_id' => $post['order_id'],
                'users_id' => $this->users_id,
                'lang'     => $this->home_lang,
            ];
            // 更新数据
            $Data = [
                'order_status' => 3,
                'confirm_time' => getTime(),
                'update_time'  => getTime(),
            ];
            // 更新订单主表
            $return = $this->shop_order_db->where($Where)->update($Data);
            if (!empty($return)) {
                // 更新数据
                $Data = [
                    'update_time'  => getTime(),
                ];
                // 更新订单明细表
                $this->shop_order_details_db->where($Where)->update($Data);
                // 添加订单操作记录
                AddOrderAction($post['order_id'],$this->users_id,'0','3','1','1','确认收货！','会员已确认收到货物，订单完成！');
                $this->success('会员确认收货');
            }else{
                $this->error('订单号错误');
            }
        }
    }
	
    // 获取微信收货地址
    public function shop_get_wechat_addr()
    {
        if (IS_AJAX_POST) {
            // 微信配置信息
            $appid     = $this->pay_wechat_config['appid'];
            $appsecret = $this->pay_wechat_config['appsecret'];
            if (empty($appid)) {
                $this->error('后台微信配置尚未配置AppId，不可以获取微信地址！');
            }else if (empty($appsecret)) {
                $this->error('后台微信配置尚未配置AppSecret，不可以获取微信地址！');
            }

            // 当前时间戳
            $time = getTime();
            // 微信access_token和jsapi_ticket信息
            $WechatData  = getUsersConfigData('wechat');
            // access_token信息判断
            $accesstoken = $WechatData['wechat_token_value'];
            if (empty($accesstoken)) {
                // 如果配置表中的accesstoken为空则执行
                // 获取公众号access_token，接口限制10万次/天
                $return = $this->shop_model->GetWeChatAccessToken($appid,$appsecret);
                if (empty($return['status'])) {
                    $this->error($return['prompt']);
                }else{
                    $accesstoken = $return['token'];
                }
            }else if ($time > ($WechatData['wechat_token_time']+7000)) {
                // 如果配置表中的时间超过过期时间则执行
                // 获取公众号access_token，接口限制10万次/天
                $return = $this->shop_model->GetWeChatAccessToken($appid,$appsecret);
                if (empty($return['status'])) {
                    $this->error($return['prompt']);
                }else{
                    $accesstoken = $return['token'];
                }
            }

            // jsapi_ticket信息判断
            $jsapi_ticket = $WechatData['wechat_ticket_value'];
            if (empty($jsapi_ticket)) {
                // 获取公众号jsapi_ticket，接口限制500万次/天
                $return = $this->shop_model->GetWeChatJsapiTicket($accesstoken);
                if (empty($return['status'])) {
                    $this->error($return['prompt']);
                }else{
                    $jsapi_ticket = $return['ticket'];
                }
            }else if ($time > ($WechatData['wechat_ticket_time']+7000)) {
                // 获取公众号jsapi_ticket，接口限制500万次/天
                $return = $this->shop_model->GetWeChatJsapiTicket($accesstoken);
                if (empty($return['status'])) {
                    $this->error($return['prompt']);
                }else{
                    $jsapi_ticket = $return['ticket'];
                }
            }

            // <---- 加密参数开始 
            // 微信公众号jsapi_ticket
            // $jsapi_ticket = $jsapi_ticket;
            // 随机字符串
            $noncestr  = $this->shop_model->GetRandomString('16');
            $noncestr  = "$noncestr";
            // 当前时间戳
            $timestamp = time();
            $timestamp = "$timestamp";
            // 当前访问接口URL
            $url       = $this->request->url(true);
            // 加密参数结束 ----->
            
            // 参数加密，顺序固定不可改变
            $string    = 'jsapi_ticket='.$jsapi_ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;
            $signature = SHA1($string);

            // 返回结果
            $result = [
                // 用于调试，不影响正常业务(如不需要，可直接清理)
                'token'     => $accesstoken,
                'ticket'    => $jsapi_ticket,
                'url'       => $url, // 传入接口调用参数(必须返回)
                'appid'     => $appid,
                'timestamp' => $timestamp,
                'noncestr'  => $noncestr,
                'signature' => $signature,
            ];
            $this->success('数据获取！',null,$result);
        }

        $result = [
            'wechat_url'   => url("user/Shop/shop_get_wechat_addr"),
            'add_addr_url' => url("user/Shop/shop_add_address"),
        ];
        $eyou = array(
            'field' => $result,
        );
        $this->assign('eyou', $eyou);
        return $this->fetch('users/shop_get_wechat_addr');
    }

    // 添加微信的收货地址到数据库
    public function add_wechat_addr()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 省
            $province = $this->region_db->where('name',$post['provinceName'])->getField('id');
            // 市
            $city     = $this->region_db->where('name',$post['cityName'])->getField('id');
            // 县
            $district = $this->region_db->where('name',$post['countryName'])->getField('id');

            // 查询这个收货地址是否存在
            $where  = [
                'users_id'   => $this->users_id,
                'consignee'  => $post['userName'],
                'mobile'     => $post['telNumber'],
                'province'   => $province,
                'city'       => $city,
                'district'   => $district,
                'address'    => $post['detailInfo'],
                'lang'       => $this->home_lang,
            ];
            $return = $this->shop_address_db->where($where)->find();
            if (!empty($return)) {
                $this->success('获取成功！',session($this->users_id.'_EyouShopOrderUrl'));
            }else{
                $data = [
                    'users_id'   => $this->users_id,
                    'consignee'  => $post['userName'],
                    'mobile'     => $post['telNumber'],
                    'province'   => $province,
                    'city'       => $city,
                    'district'   => $district,
                    'address'    => $post['detailInfo'],
                    'is_default' => 1, // 设置为默认地址
                    'lang'       => $this->home_lang,
                    'add_time'   => getTime(),
                ];
                $addr_id = $this->shop_address_db->add($data);
                if (!empty($addr_id)) {
                    // 把对应会员下的所有地址改为非默认
                    $AddressWhere = [
                        'addr_id'  => array('NEQ',$addr_id),
                        'users_id' => $this->users_id,
                        'lang'     => $this->home_lang,
                    ];
                    $data_new['is_default']  = '0';// 设置为非默认地址
                    $data_new['update_time'] = getTime();
                    $this->shop_address_db->where($AddressWhere)->update($data_new);
                    $this->success('获取成功！',session($this->users_id.'_EyouShopOrderUrl'));
                }else{
                    $this->success('获取失败，请刷新后重试！');
                }
            }
        }
    }
}