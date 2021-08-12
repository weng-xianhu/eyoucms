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
use think\Cookie;
use app\common\logic\ShopCommonLogic;

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
        $this->shop_order_service_db = Db::name('shop_order_service');  // 订单售后服务表
        $this->shop_address_db       = Db::name('shop_address');        // 收货地址表

        $this->archives_db           = Db::name('archives');            // 产品表
        $this->product_attr_db       = Db::name('product_attr');        // 产品属性表
        $this->product_attribute_db  = Db::name('product_attribute');   // 产品属性标题表

        $this->region_db             = Db::name('region');                 // 三级联动地址总表
        $this->shipping_template_db  = Db::name('shop_shipping_template'); // 运费模板表

        $this->shop_model = model('Shop');  // 商城模型
        $this->shop_common = new ShopCommonLogic(); // common商城业务层，前后台共用

        // 订单中心是否开启
        $redirect_url = '';
        $shop_open = getUsersConfigData('shop.shop_open');
        $web_users_switch = tpCache('web.web_users_switch');
        if (empty($shop_open)) { 
            // 订单功能关闭，立马跳到会员中心
            $redirect_url = url('user/Users/index');
            $msg = '商城中心尚未开启！';
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
    public function shop_under_order($error = true)
    {
        if (empty($error)) $this->error('您的购物车还没有商品！');
        // 获取当前页面URL，存入session，若操作添加地址后返回当前页面
        session($this->users_id.'_EyouShopOrderUrl', $this->request->url(true));
        // 数据由标签调取生成
        return $this->fetch('users/shop_under_order');
    }

    // 购物车库存检测
    public function cart_stock_detection()
    {
        if (IS_AJAX_POST) {
            // 购物车查询条件
            $CartWhere = [
                'a.users_id' => $this->users_id,
                'a.lang'     => $this->home_lang,
                'a.selected' => 1,
            ];
            $list = $this->shop_cart_db->field('a.product_num, b.stock_count, c.spec_value_id, c.spec_stock')
                ->alias('a')
                ->join('__ARCHIVES__ b', 'a.product_id = b.aid', 'LEFT')
                ->join('__PRODUCT_SPEC_VALUE__ c', 'a.spec_value_id = c.spec_value_id and a.product_id = c.aid', 'LEFT')
                ->where($CartWhere)
                ->order('a.add_time desc')
                ->select();
            if (empty($list)) $this->error('请选择要购买的商品！');
            
            $ExceedingStock = 0;
            // 处理商品库存检测
            foreach ($list as $key => $value) {
                // 购物车商品存在规格并且库存不为空，则覆盖商品原来的库存
                if (!empty($value['spec_value_id'])) {
                    $value['stock_count'] = $value['spec_stock'];
                }
                // 检测是否有超出库存的产品
                if (empty($value['product_num']) || $value['product_num'] > $value['stock_count']) {
                    $ExceedingStock = 1;
                    break;
                }
                // 若库存为空则清除这条数据
                if (empty($value['stock_count'])) {
                    unset($list[$key]);
                    continue;
                }
            }

            $this->success('检测完成', null, $ExceedingStock);
        }
    }

    // 收货地址管理列表
    public function shop_address_list()
    {
        // 获取当前页面URL，存入session，若操作添加地址后返回当前页面
        session($this->users_id.'_EyouShopOrderUrl', $this->request->url(true));
        // 指定返回上一级URL
        $gourl = input('param.gourl/s');
        $gourl = urldecode($gourl);
        $this->assign('gourl', $gourl);
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
                    // 订单取消，还原单内产品数量 
                    model('ProductSpecValue')->SaveProducSpecValueStock($order_id, $this->users_id);
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
            $param['aid'] = intval($param['aid']);
            $param['num'] = intval($param['num']);

            // 商品是否已售罄
            $this->IsSoldOut($param);

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

                /*若开启多规格则执行*/
                if (!empty($this->usersConfig['shop_open_spec']) && !empty($param['spec_value_id'])) {
                    $querydata['spec_value_id'] = $param['spec_value_id'];
                }
                /* END */

                /*特定场景专用*/
                $opencodetype = config('global.opencodetype');
                if (1 == $opencodetype) {
                    if (!empty($param['mini_id']) && intval($param['mini_id']) > 0) {
                        $querydata['mini_id'] = intval($param['mini_id']);
                    }
                }
                /*end*/

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
                // $url = urldecode(url('user/Shop/shop_under_order', ['querystr'=>$querystr,'hash'=>$hash]));

                // 先 json_encode 后 md5 加密信息
                $querystr = md5(json_encode($querydata));

                // 存入 cookie
                cookie($querystr, $querydata);

                // 跳转链接
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
            // 商品是否已售罄
            $this->IsSoldOut($param);

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

                /*若开启多规格则执行*/
                if (!empty($this->usersConfig['shop_open_spec']) && !empty($param['spec_value_id'])) {
                    $cart_where['spec_value_id'] = $param['spec_value_id'];
                }
                /* END */

                $cartInfo = $this->shop_cart_db->field('product_num')->where($cart_where)->find();
                if (!empty($cartInfo)) {
                    // 购物车内已有相同产品，进行数量更新。
                    $data['product_num'] = $param['num'] + intval($cartInfo['product_num']); //与购物车数量进行叠加
                    $data['update_time'] = getTime();
                    $cart_id = $this->shop_cart_db->where($cart_where)->update($data);
                }else{
                    // 购物车内还未有相同产品，进行添加。
                    $data['users_id']    = $this->users_id;
                    $data['product_id']  = $param['aid'];
                    $data['product_num'] = $param['num'];
                    $data['spec_value_id'] = $param['spec_value_id'];
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
            $spec_value_id = input('post.spec_value_id');

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
                    'spec_value_id' => $spec_value_id,
                ];
                // 判断追加查询条件，当减数量时，商品数量最少为1
                if ('-' == $symbol) $cart_where['product_num'] = array('gt','1');
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
                        ->field('a.product_num, b.users_price, c.spec_price')
                        ->alias('a') 
                        ->join('__ARCHIVES__ b', 'a.product_id = b.aid', 'LEFT')
                        ->join('__PRODUCT_SPEC_VALUE__ c', 'a.spec_value_id = c.spec_value_id and a.product_id = c.aid', 'LEFT')
                        ->where($CaerWhere)
                        ->select();

                    $level_discount = $this->users['level_discount'];
                    $discount_price = $level_discount / 100;
                    $spec_price = $users_price = $num_new = 0;
                    foreach ($CartData as $key => $value) {
                        if (!empty($value['spec_price'])) {
                            if (!empty($level_discount)) $value['spec_price'] = $value['spec_price'] * $discount_price;
                            $spec_price += $value['product_num'] * $value['spec_price'];
                        } else {
                            if (!empty($level_discount)) $value['users_price'] = $value['users_price'] * $discount_price;
                            $users_price += $value['product_num'] * $value['users_price'];
                        }
                        $num_new += $value['product_num'];
                    }
                    $CartData['num']   = $num_new;
                    $CartData['price'] = sprintf("%.2f", $spec_price + $users_price);
                    if (empty($CartData['num']) && empty($CartData['price'])) {
                        $CartData['num']   = '0';
                        $CartData['price'] = '0.00';
                    }

                    if (!empty($cart_id)) {
                        $this->success('操作成功！', null, ['NumberVal'=>$CartData['num'], 'AmountVal'=>$CartData['price']]);
                    }
                } else {
                    $this->error('商品数量最少为1', null, ['error'=>'0']);
                }
            } else {
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
                /*计算购物车选中商品的总数总额*/
                $CaerWhere = [
                    'a.users_id' => $this->users_id,
                    'a.lang'     => $this->home_lang,
                    'a.selected' => 1
                ];
                $CartData = $this->shop_cart_db
                    ->field('a.product_num, b.users_price, c.spec_price')
                    ->alias('a') 
                    ->join('__ARCHIVES__ b', 'a.product_id = b.aid', 'LEFT')
                    ->join('__PRODUCT_SPEC_VALUE__ c', 'a.spec_value_id = c.spec_value_id and a.product_id = c.aid', 'LEFT')
                    ->where($CaerWhere)
                    ->select();

                $level_discount = $this->users['level_discount'];
                $discount_price = $level_discount / 100;
                $spec_price = $users_price = $num_new = 0;
                foreach ($CartData as $key => $value) {
                    if (!empty($value['spec_price'])) {
                        if (!empty($level_discount)) $value['spec_price'] = $value['spec_price'] * $discount_price;
                        $spec_price += $value['product_num'] * $value['spec_price'];
                    } else {
                        if (!empty($level_discount)) $value['users_price'] = $value['users_price'] * $discount_price;
                        $users_price += $value['product_num'] * $value['users_price'];
                    }
                    $num_new += $value['product_num'];
                }
                $CartData['num']   = empty($num_new) ? 0 : $num_new;
                $CartData['price'] = sprintf("%.2f", $spec_price + $users_price);
                $CartData['price'] = empty($CartData['price']) ? '0.00' : $CartData['price'];
                /*END*/

                /*购物车是否还存在商品*/
                $CartCount = $this->shop_cart_db->where([
                        'users_id' => $this->users_id,
                        'lang'     => $this->home_lang,
                    ])->count();
                /*end*/

                $data = [
                    'NumberVal' => $CartData['num'],
                    'AmountVal' => $CartData['price'],
                    'CartCount' => $CartCount,
                ];
                $this->success('操作成功！', null, $data);
            } else {
                $this->error('删除失败！');
            }
        }
    }

    // 移入收藏
    public function move_to_collection()
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

                /*加入收藏*/
                $aid = $this->shop_cart_db->where($cart_where)->value('product_id');
                $row = Db::name('users_collection')->where([
                    'users_id'  => $this->users_id,
                    'aid'   => $aid,
                ])->find();
                if (empty($row)) {
                    $archivesInfo = Db::name('archives')->field('aid,title,litpic,channel,typeid')->find($aid);
                    if (!empty($archivesInfo)) {
                        Db::name('users_collection')->add([
                            'users_id'  => $this->users_id,
                            'title' => $archivesInfo['title'],
                            'aid' => $aid,
                            'litpic' => $archivesInfo['litpic'],
                            'channel' => $archivesInfo['channel'],
                            'typeid' => $archivesInfo['typeid'],
                            'lang'  => $this->home_lang,
                            'add_time'  => getTime(),
                            'update_time' => getTime(),
                        ]);
                    }
                }
                /*end*/

                // 删除数据
                $return = $this->shop_cart_db->where($cart_where)->delete();
            }
            if (!empty($return)) {
                /*计算购物车总数总额*/
                $CaerWhere = [
                    'a.users_id' => $this->users_id,
                    'a.lang'     => $this->home_lang,
                    'a.selected' => 1
                ];
                $CartData = $this->shop_cart_db
                    ->field('a.product_num, b.users_price, c.spec_price')
                    ->alias('a') 
                    ->join('__ARCHIVES__ b', 'a.product_id = b.aid', 'LEFT')
                    ->join('__PRODUCT_SPEC_VALUE__ c', 'a.spec_value_id = c.spec_value_id and a.product_id = c.aid', 'LEFT')
                    ->where($CaerWhere)
                    ->select();

                $level_discount = $this->users['level_discount'];
                $discount_price = $level_discount / 100;
                $spec_price = $users_price = $num_new = 0;
                foreach ($CartData as $key => $value) {
                    if (!empty($value['spec_price'])) {
                        if (!empty($level_discount)) $value['spec_price'] = $value['spec_price'] * $discount_price;
                        $spec_price += $value['product_num'] * $value['spec_price'];
                    } else {
                        if (!empty($level_discount)) $value['users_price'] = $value['users_price'] * $discount_price;
                        $users_price += $value['product_num'] * $value['users_price'];
                    }
                    $num_new += $value['product_num'];
                }
                $CartData['num']   = empty($num_new) ? 0 : $num_new;
                $CartData['price'] = sprintf("%.2f", $spec_price + $users_price);
                $CartData['price'] = empty($CartData['price']) ? '0.00' : $CartData['price'];
                /*END*/

                $this->success('操作成功！', null, ['NumberVal'=>$CartData['num'], 'AmountVal'=>$CartData['price']]);
            } else {
                $this->error('操作失败！');
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
                $selected = 0;
            } else {
                $selected = 1;
            }
            $data['selected'] = $selected;
            $data['update_time'] = getTime();
            // 更新条件
            if ('*' == $cart_id) {
                $cart_where = [
                    'product_num' => ['>', 0],
                    'users_id' => $this->users_id,
                    'lang'     => $this->home_lang,
                ];
            } else {
                $cart_where = [
                    'cart_id'  => $cart_id,
                    'product_num' => ['>', 0],
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

    /**
     * 快速下单支付流程 - 添加商品信息及计算价格等
     * @return [type] [description]
     */
    public function fastSubmitOrder()
    {
        $aid = input('post.aid_1607507428/d');
        $pay_code = input('post.pay_code_1607507428/s'); // 支付方式

        if (IS_POST && !empty($aid) && !empty($pay_code)) {

            $OrderData = [];

            $mini_id = input('post.mini_id_1607507428/d');
            !empty($mini_id) && $OrderData['mini_id'] = $mini_id;

            // 规格值
            $spec_value_id = input('post.spec_value_id_1607507428/s');
            if (!empty($spec_value_id)) {
                $spec_value_id = preg_replace('/[^\d\_]/i', '', $spec_value_id);
                $spec_value_id = trim($spec_value_id, '_');
                $OrderData['spec_value_id'] = "_{$spec_value_id}_";
            }

            $ArchivesWhere = [
                'a.aid'  => $aid,
            ];
            if (!empty($spec_value_id)) $ArchivesWhere['b.spec_value_id'] = $spec_value_id;
            $field = 'a.aid, a.aid as product_id, a.title, a.litpic, a.users_price, a.stock_count, a.prom_type, a.attrlist_id, b.spec_price, b.spec_stock, b.spec_value_id, b.value_id';
            $list = Db::name('archives')->field($field)
                ->alias('a')
                ->join('__PRODUCT_SPEC_VALUE__ b', 'a.aid = b.aid', 'LEFT')
                ->where($ArchivesWhere)
                ->limit('0, 1')
                ->select();

            // 没有相应的产品
            if (empty($list[0])) $this->error('订单生成失败，没有相应的商品！');
            $list[0]['product_num']      = 1;
            $list[0]['under_order_type'] = 2; // 快速下单支付

            // 生成订单之前的产品数据整理
            $retData = $this->shop_model->handlerOrderData('fast', $OrderData, $list, $this->users);
            if (empty($retData['code'])) {
                $this->error($retData['msg']);
            }
            $OrderData = array_merge($OrderData, $retData['data']['OrderData']);
            $list = $retData['data']['list'];

            $OrderId = Db::name('shop_order')->insertGetId($OrderData);
            $OrderData['order_id'] = $OrderId;
            if (!empty($OrderId)) {

                // 生成订单之后的订单明细整理
                $retData = $this->shop_model->handlerDetailsData('fast', $OrderData, $list, $this->users);
                if (empty($retData['code'])) {
                    $this->error($retData['msg']);
                }
                $cart_ids = $retData['data']['cart_ids'];
                $OrderDetailsData = $retData['data']['OrderDetailsData'];
                $UpSpecValue = $retData['data']['UpSpecValue'];

                $DetailsId = Db::name('shop_order_details')->insertAll($OrderDetailsData);

                if (!empty($DetailsId)) {
                    // 清理购物车中已下单的ID
                    if (!empty($cart_ids)) Db::name('shop_cart')->where('cart_id', 'IN', $cart_ids)->delete();

                    // 产品库存、销量处理
                    $this->shop_model->ProductStockProcessing($UpSpecValue);

                    // 添加订单操作记录
                    AddOrderAction($OrderId, $this->users_id);

                    if (0 == $OrderData['payment_method']) {
                        // 选择在线付款并且在手机微信端、小程序中则返回订单ID，订单号，订单交易类型
                        if (isMobile() && isWeixin()) {
                            // $where = [
                            //     'pay_id' => 1,
                            //     'pay_mark' => 'wechat'
                            // ];
                            // $PayInfo = Db::name('pay_api_config')->where($where)->getField('pay_info');
                            // if (!empty($PayInfo)) $PayInfo = unserialize($PayInfo);

                            // if (!empty($this->users['open_id']) && !empty($PayInfo) && 0 == $PayInfo['is_open_wechat']) {
                            //     $ReturnOrderData = [
                            //         'unified_id'       => $OrderId,
                            //         'unified_number'   => $OrderData['order_code'],
                            //         'transaction_type' => 2, // 订单支付购买
                            //         'order_total_amount' => $TotalAmount,
                            //         'order_source'     => 1, // 提交订单页
                            //         'is_gourl'         => 1,
                            //     ];
                            //     if ($this->users['users_money'] <= '0.00') {
                            //         // 余额为0
                            //         $ReturnOrderData['is_gourl'] = 0;
                            //         $this->success('订单已生成！', null, $ReturnOrderData);
                            //     } else {
                            //         // 余额不为0
                            //         $url = url('user/Shop/shop_wechat_pay_select');
                            //         session($this->users_id.'_ReturnOrderData', $ReturnOrderData);
                            //         $this->success('订单已生成！', $url, $ReturnOrderData);
                            //     }
                            // } else {
                            //     // 如果会员没有openid则跳转到支付页面进行支付
                            //     // 在线付款时，跳转至付款页
                            //     // 对ID和订单号加密，拼装url路径
                            //     $Paydata = [
                            //         'order_id'   => $OrderId,
                            //         'order_code' => $OrderData['order_code'],
                            //     ];

                            //     // 先 json_encode 后 md5 加密信息
                            //     $Paystr = md5(json_encode($Paydata));

                            //     // 存入 cookie
                            //     cookie($Paystr, $Paydata);

                            //     // 跳转链接
                            //     $PaymentUrl = urldecode(url('user/Pay/pay_recharge_detail',['paystr'=>$Paystr]));

                            //     $this->success('订单已生成！', $PaymentUrl, ['is_gourl' => 1]);
                            // }
                        } else {
                            if ('alipay' == $pay_code) {
                                // 重要参数，支付宝配置信息
                                $PayInfo = Db::name('pay_api_config')->where([
                                        'pay_id' => 2,
                                        'pay_mark' => 'alipay'
                                    ])->getField('pay_info');
                                if (empty($PayInfo)) $this->error('请先配置支付宝！');
                                $alipay = unserialize($PayInfo);
                                $vars = [
                                    'unified_number'   => $OrderData['order_code'],
                                    'unified_amount'   => $OrderData['order_amount'],
                                    'transaction_type' => 2,
                                ];
                                $PayApiLogic = new \app\user\logic\PayApiLogic;
                                $retData = $PayApiLogic->UseAliPayPay(['transaction_type'=>2], $vars, $alipay, true);
                                if (!empty($retData['alipay_url'])) {
                                    $PaymentUrl = $retData['alipay_url'];
                                    $this->redirect($PaymentUrl);
                                } else {
                                    $this->error('调起支付宝页面失败！');
                                }
                            }
                            else if ('weipay' == $pay_code) {
                                $vars = [
                                    'unified_number' => $OrderData['order_code'],
                                    'transaction_type' => 2
                                ];
                                $data = [
                                    'url_qrcode'        => url('user/PayApi/pay_wechat_png', $vars),
                                    'pay_id'            => 1,
                                    'pay_mark'          => 'wechat',
                                    'unified_id'        => $OrderId,
                                    'unified_number'    => $OrderData['order_code'],
                                    'transaction_type'  => 2,
                                ];
                                $this->success('正在支付中', null, $data);
                            }
                        }
                    }
                }
            }
        }
        $this->error('操作失败！');
    }

    // 订单提交处理逻辑，添加商品信息及计算价格等
    public function shop_payment_page()
    {
        if (IS_POST) {
            // 提交的订单信息判断
            $post = input('post.');
            if (empty($post['payment_type'])) $this->error('网站支付配置未完善，购买服务暂停使用');
            if (empty($post)) $this->error('订单生成失败，商品数据有误！'); 
            $Md5Value = !empty($post['Md5Value']) ? $post['Md5Value'] : null;
            
            // if (!empty($post['aid'])) $aid  = intval(mchStrCode($post['aid'],'DECODE'));
            // if (!empty($post['num'])) $num  = intval(mchStrCode($post['num'],'DECODE'));
            // if (!empty($post['type'])) $type  = intval(mchStrCode($post['type'],'DECODE'));
            
            $OrderData = [];

            // 产品ID是否存在
            if (!empty($Md5Value)) {
                $querystr = cookie($Md5Value);
                $aid = !empty($querystr['aid']) ? intval($querystr['aid']) : 0;
                $num = !empty($querystr['product_num']) ? intval($querystr['product_num']) : 0;
                $mini_id = !empty($querystr['mini_id']) ? intval($querystr['mini_id']) : 0;
                !empty($mini_id) && $OrderData['mini_id'] = $mini_id;
                $spec_value_id = !empty($querystr['spec_value_id']) ? $querystr['spec_value_id'] : '';
                !empty($spec_value_id) && $OrderData['spec_value_id'] = $spec_value_id;
                $type = !empty($post['type']) ? intval($post['type']) : 0;
                $OrderData['order_md5'] = md5($aid.$spec_value_id);

                // if (!empty($post['spec_value_id'][0])) $spec_value_id  = mchStrCode($post['spec_value_id'][0],'DECODE');
                // 商品数量判断
                if ($num <= 0) $this->error('订单生成失败，商品数量有误！');
                // 订单来源判断
                if ($type != 1) $this->error('订单生成失败，提交来源有误！');
                
                // 立即购买查询条件
                $ArchivesWhere = [
                    'a.aid'  => $aid,
                ];
                if (!empty($spec_value_id)) $ArchivesWhere['b.spec_value_id'] = $spec_value_id;
                $field = 'a.aid, a.aid as product_id, a.title, a.litpic, a.users_price, a.stock_count, a.prom_type, a.attrlist_id, b.spec_price, b.spec_stock, b.spec_value_id, b.value_id, c.spec_is_select';
                $list = $this->archives_db->field($field)
                    ->alias('a')
                    ->join('__PRODUCT_SPEC_VALUE__ b', 'a.aid = b.aid', 'LEFT')
                    ->join('__PRODUCT_SPEC_DATA__ c', 'a.aid = c.aid', 'LEFT')
                    ->where($ArchivesWhere)
                    ->limit('0, 1')
                    ->select();
                $list[0]['product_num']      = $num;
                $list[0]['under_order_type'] = $type;
                if (empty($list[0]['spec_is_select'])) {
                    $list[0]['spec_price']    = '';
                    $list[0]['spec_stock']    = '';
                    $list[0]['spec_value_id'] = '';
                }
            }else{
                // 购物车查询条件
                $cart_where = [
                    'a.users_id' => $this->users_id,
                    'a.selected' => 1,
                ];
                $list = $this->shop_cart_db->field('a.*, b.aid, b.title, b.litpic, b.users_price, b.stock_count, b.prom_type, b.attrlist_id, c.spec_price, c.spec_stock, c.value_id')
                    ->alias('a')
                    ->join('__ARCHIVES__ b', 'a.product_id = b.aid', 'LEFT')
                    ->join('__PRODUCT_SPEC_VALUE__ c', 'a.spec_value_id = c.spec_value_id and a.product_id = c.aid', 'LEFT')
                    ->where($cart_where)
                    ->select();
            }

            // 没有相应的产品
            if (empty($list)) $this->error('订单生成失败，没有相应的产品！');

            // 生成订单之前的产品数据整理
            $retData = $this->shop_model->handlerOrderData('normal', $OrderData, $list, $this->users, $post);
            if (empty($retData['code'])) $this->error($retData['msg']);
            
            // 合并订单数据
            $OrderData = array_merge($OrderData, $retData['data']['OrderData']);
            $list = $retData['data']['list'];

            $AddrData = [];
            // 非虚拟订单则查询运费信息
            if (empty($OrderData['prom_type'])) {
                // 没有选择收货地址
                if (empty($post['addr_id'])) {
                    // 在微信端并且不在小程序中
                    if (isWeixin() && !isWeixinApplets()) {
                        // 跳转至收货地址添加选择页
                        $get_addr_url = url('user/Shop/shop_get_wechat_addr');
                        $is_gourl['is_gourl'] = 1;
                        $this->success('101:选择添加地址方式', $get_addr_url, $is_gourl);
                    } else {
                        $paramNew['add_addr'] = 1;
                        $this->error('订单生成失败，请添加收货地址！', null, $paramNew);
                    }
                }

                // 查询收货地址
                $AddrWhere = [
                    'addr_id'  => $post['addr_id'],
                    'users_id' => $this->users_id,
                ];
                $AddressData = $this->shop_address_db->where($AddrWhere)->find();
                if (empty($AddressData)) {
                    if (isWeixin() && !isWeixinApplets()) {
                        // 跳转至收货地址添加选择页
                        $get_addr_url = url('user/Shop/shop_get_wechat_addr');
                        $is_gourl['is_gourl'] = 1;
                        $this->success('102:选择添加地址方式', $get_addr_url, $is_gourl);
                    } else {
                        $paramNew['add_addr'] = 1;
                        $this->error('订单生成失败，请添加收货地址！', null, $paramNew);
                    }
                }

                $shop_open_shipping = getUsersConfigData('shop.shop_open_shipping');
                $template_money = '0.00';
                if (!empty($shop_open_shipping)) {
                    // 通过省份获取运费模板中的运费价格
                    $template_money = $this->shipping_template_db->where('province_id', $AddressData['province'])->getField('template_money');
                    if (0 >= $template_money) {
                        // 省份运费价格为0时，使用统一的运费价格，固定ID为100000
                        $template_money = $this->shipping_template_db->where('province_id', '100000')->getField('template_money');
                    }
                    // 合计金额加上运费价格
                    $OrderData['order_total_amount'] += $template_money;
                    $OrderData['order_amount'] += $template_money;
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
            // 存在收货地址则追加合并到主表数组
            if (!empty($AddrData)) $OrderData = array_merge($OrderData, $AddrData);

            // 数据验证
            $rule = ['payment_method' => 'require|token'];
            $message = ['payment_method.require' => '不可为空！'];
            $validate = new \think\Validate($rule, $message);
            if (!$validate->check($post)) $this->error('不可连续提交订单！');
            
            $OrderId = $this->shop_order_db->insertGetId($OrderData);
            if (!empty($OrderId)) {
                $OrderData['order_id'] = $OrderId;

                // 生成订单之后的订单明细整理
                $retData = $this->shop_model->handlerDetailsData('normal', $OrderData, $list, $this->users);
                if (empty($retData['code'])) $this->error($retData['msg']);
                
                // 清理购物车中已下单的ID
                $cart_ids = $retData['data']['cart_ids'];

                // 产品库存、销量处理
                $UpSpecValue = $retData['data']['UpSpecValue'];

                // 添加订单明细表
                $OrderDetailsData = $retData['data']['OrderDetailsData'];
                $DetailsId = $this->shop_order_details_db->insertAll($OrderDetailsData);

                if (!empty($DetailsId)) {
                    // 清理购物车中已下单的ID
                    if (!empty($cart_ids)) $this->shop_cart_db->where('cart_id', 'IN', $cart_ids)->delete();

                    // 产品库存、销量处理
                    $this->shop_model->ProductStockProcessing($UpSpecValue);

                    // 添加订单操作记录
                    AddOrderAction($OrderId, $this->users_id);

                    if (getUsersTplVersion() == 'v1') { // 第一版会员中心
                        /*清除下单的Cookie数据*/
                        if (!empty($Md5Value)) Cookie::delete($Md5Value);
                        /* END */
                    }

                    if (0 == $post['payment_method']) {
                        if (isMobile() && isWeixin()) {
                            // 选择在线付款并且在手机微信端、小程序中则返回订单ID，订单号，订单交易类型
                            $where = [
                                'pay_id' => 1,
                                'pay_mark' => 'wechat'
                            ];
                            $PayInfo = Db::name('pay_api_config')->where($where)->getField('pay_info');
                            if (!empty($PayInfo)) $PayInfo = unserialize($PayInfo);

                            if (!empty($this->users['open_id']) && !empty($PayInfo) && 0 == $PayInfo['is_open_wechat']) {
                                $payment_type = $post['payment_type'];
                                if ('yezf_balance' == $payment_type) {
                                    // 余额支付
                                    if ($this->users['users_money'] < $OrderData['order_amount']) {
                                        // 余额不足，支付失败
                                        $this->error('余额不足，支付失败！', null, ['url' => url('user/Shop/shop_centre')]);
                                    } else {
                                        // 余额充足，进行支付
                                        $ret = Db::name('users')->where(['users_id' => $this->users_id])->update([
                                            'users_money' => Db::raw('users_money-'.$OrderData['order_amount']),
                                            'update_time' => getTime(),
                                        ]);
                                        if (false !== $ret) {
                                            $pay_details = [
                                                'unified_id'        => $OrderData['order_id'],
                                                'unified_number'    => $OrderData['order_code'],
                                                'transaction_type'  => 2,
                                                'payment_amount'    => $OrderData['order_amount'],
                                                'payment_type'      => "余额支付",
                                            ];
                                            $returnData = pay_success_logic($this->users_id, $OrderData['order_code'], $pay_details, 'balance');
                                            if (is_array($returnData)) {
                                                if (1 == $returnData['code']) {
                                                    $this->success($returnData['msg'], $returnData['url'], $returnData['data']);
                                                } else {
                                                    $this->error($returnData['msg'], null, ['url'=>url('user/Shop/shop_centre')]);
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    // 微信内支付、小程序内支付 -- 微信端不存在支付宝支付
                                    $ReturnOrderData = [
                                        'unified_id'       => $OrderId,
                                        'unified_number'   => $OrderData['order_code'],
                                        'transaction_type' => 2, // 订单支付购买
                                        'order_source'     => 1, // 提交订单页
                                        'is_gourl'         => 1,
                                        'order_total_amount' => 0 // 好像没有用处了
                                    ];
                                    if ($this->users['users_money'] <= '0.00') {
                                        // 余额为0
                                        $ReturnOrderData['is_gourl'] = 0;
                                        $this->success('订单已生成！', null, $ReturnOrderData);
                                    } else {
                                        // 余额不为0
                                        $url = url('user/Shop/shop_wechat_pay_select');
                                        session($this->users_id.'_ReturnOrderData', $ReturnOrderData);
                                        $this->success('订单已生成！', $url, $ReturnOrderData);
                                    }
                                }
                            } else {
                                // 执行如果会员没有openid则跳转到支付页面进行支付
                                $Paydata = [
                                    'order_id'   => $OrderId,
                                    'order_code' => $OrderData['order_code'],
                                ];

                                // 先 json_encode 后 md5 加密信息
                                $Paystr = md5(json_encode($Paydata));

                                // 存入 cookie
                                cookie($Paystr, $Paydata);

                                // 跳转链接
                                $PaymentUrl = urldecode(url('user/Pay/pay_recharge_detail',['paystr'=>$Paystr]));
                                $this->success('订单已生成！', $PaymentUrl, ['is_gourl' => 1]);
                            }
                        } else {
                            // PC端、手机浏览器端 -- 在线付款时执行
                            if (getUsersTplVersion() == 'v2' && isset($post['payment_type'])) {
                                // 第二套模板 -- 余额支付、微信支付、支付宝支付、其他第三方支付
                                $payment_type = $post['payment_type'];
                                if ('yezf_balance' == $payment_type) {
                                    // 余额支付
                                    if ($this->users['users_money'] < $OrderData['order_amount']) {
                                        $url = url('user/Shop/shop_centre');
                                        $this->error('余额不足，支付失败！', null, ['url'=>$url]);
                                    } else {
                                        $ret = Db::name('users')->where(['users_id'=>$this->users_id])->update([
                                            'users_money' => Db::raw('users_money-'.$OrderData['order_amount']),
                                            'update_time' => getTime(),
                                        ]);
                                        if (false !== $ret) {
                                            $pay_details = [
                                                'unified_id'        => $OrderData['order_id'],
                                                'unified_number'    => $OrderData['order_code'],
                                                'transaction_type'  => 2,
                                                'payment_amount'    => $OrderData['order_amount'],
                                                'payment_type'      => "余额支付",
                                            ];
                                            $returnData = pay_success_logic($this->users_id, $OrderData['order_code'], $pay_details, 'balance');
                                            if (is_array($returnData)) {
                                                if (1 == $returnData['code']) {
                                                    $this->success($returnData['msg'], $returnData['url'], $returnData['data']);
                                                } else {
                                                    $this->error($returnData['msg'], null, ['url'=>url('user/Shop/shop_centre')]);
                                                }
                                            }
                                        }
                                    }
                                } else if (in_array($payment_type, ['zxzf_wechat', 'zxzf_alipay'])) {
                                    // 内置第三方在线支付
                                    $payment_type_arr = explode('_', $payment_type);
                                    $pay_mark = !empty($payment_type_arr[1]) ? $payment_type_arr[1] : '';
                                    $payApiRow = Db::name('pay_api_config')->where(['pay_mark' => $pay_mark, 'lang' => $this->home_lang])->find();
                                    if (empty($payApiRow)) $this->error('请选择正确的支付方式！');

                                    // 返回支付所需参数
                                    $data = [
                                        'code'              => 'order_status_0',
                                        'pay_id'            => $payApiRow['pay_id'],
                                        'pay_mark'          => $pay_mark,
                                        'unified_id'        => $OrderData['order_id'],
                                        'unified_number'    => $OrderData['order_code'],
                                        'transaction_type'  => 2,
                                    ];
                                    $this->success('正在支付中', url('user/Shop/shop_centre'), $data);
                                }
                            } else {
                                // 第一套模板 -- 执行跳转至订单支付页
                                $Paydata = [
                                    'order_id'   => $OrderId,
                                    'order_code' => $OrderData['order_code'],
                                ];

                                // 先 json_encode 后 md5 加密信息
                                $Paystr = md5(json_encode($Paydata));

                                // 存入 cookie
                                cookie($Paystr, $Paydata);

                                // 跳转链接
                                $PaymentUrl = urldecode(url('user/Pay/pay_recharge_detail',['paystr'=>$Paystr]));
                                
                                $this->success('订单已生成！', $PaymentUrl);
                            }
                        }
                    } else {
                        // 货到付款 -- 无需跳转付款页，直接跳转订单列表页
                        $PaymentUrl = urldecode(url('user/Shop/shop_centre'));
                        
                        // 再次添加一条订单操作记录
                        AddOrderAction($OrderId, $this->users_id, 0, 1, 0, 1, '货到付款！', '会员选择货到付款，款项由快递代收！');

                        // 邮箱发送
                        $SmtpConfig = tpCache('smtp');
                        $ReturnData['email'] = GetEamilSendData($SmtpConfig, $this->users, $OrderData, 1, 'delivery_pay');
                            
                        // 手机发送
                        $SmsConfig = tpCache('sms');
                        $ReturnData['mobile'] = GetMobileSendData($SmsConfig, $this->users, $OrderData, 1, 'delivery_pay');

                        // 发送站内信给后台
                        $OrderData['pay_method'] = '货到付款';
                        SendNotifyMessage($OrderData, 5, 1, 0);

                        // 返回结束
                        $ReturnData['is_gourl'] = 1;
                        $this->success('订单已生成！', $PaymentUrl, $ReturnData);
                    }
                } else {
                    $this->error('订单生成失败，商品数据有误！');
                }
            } else {
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
            } else {
                $post['is_default'] = !empty($post['is_default']) ? 1 : 0;
            }
            $addr_id = $this->shop_address_db->add($post);

            if (!empty($addr_id)) {
                // 把对应会员下的所有地址改为非默认
                if (1 == $post['is_default']) {
                    $AddressWhere = [
                        'addr_id'  => array('NEQ',$addr_id),
                        'users_id' => $this->users_id,
                        'lang'     => $this->home_lang,
                    ];
                    $data_new['is_default']  = 0;// 设置为非默认地址
                    $data_new['update_time'] = getTime();
                    $this->shop_address_db->where($AddressWhere)->update($data_new);
                }

                if (isMobile() && isWeixin()) {
                    $ResultD = [
                        'url' => session($this->users_id.'_EyouShopOrderUrl')
                    ];
                    $this->success('添加成功！', session($this->users_id.'_EyouShopOrderUrl'), $ResultD);
                }

                // 根据地址ID查询相应的中文名字
                $post['country']  = '中国';
                $post['province'] = get_province_name($post['province']);
                $post['city']     = get_city_name($post['city']);
                $post['district'] = get_area_name($post['district']);
                $post['addr_id'] = $addr_id;
                $post['is_mobile'] = $this->is_mobile;
                $this->success('添加成功！', null, $post);
            } else {
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
            $post['update_time'] = getTime();
            $post['lang']     = $this->home_lang;
            $post['country'] = (empty($post['country']) || $post['country'] == '中国') ? 0 : 1;
            $post['is_default'] = !empty($post['is_default']) ? 1 : 0;

            $AddrWhere = [
                'addr_id'  => $post['addr_id'],
                'users_id' => $this->users_id,
                'lang'     => $this->home_lang,
            ];

            $addr_id = $this->shop_address_db->where($AddrWhere)->update($post);

            if (!empty($addr_id)) {
                // 把对应会员下的所有地址改为非默认
                if (1 == $post['is_default']) {
                    $AddressWhere = [
                        'addr_id'  => array('NEQ',$post['addr_id']),
                        'users_id' => $this->users_id,
                        'lang'     => $this->home_lang,
                    ];
                    $data_new['is_default']  = 0;// 设置为非默认地址
                    $data_new['update_time'] = getTime();
                    $this->shop_address_db->where($AddressWhere)->update($data_new);
                }

                // 根据地址ID查询相应的中文名字
                $post['country']  = '中国';
                $post['province'] = get_province_name($post['province']);
                $post['city']     = get_city_name($post['city']);
                $post['district'] = get_area_name($post['district']);
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
        $AddrData['onDelAddress'] = " onclick=\"DelAddress('{$AddrId}', this);\" ";
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
            if (empty($shop_open_shipping)) $this->success('未开启运费', '', 0);
            
            // 查询会员收货地址，获取省份
            $addr_id = input('post.addr_id');
            $where = [
                'addr_id'  => $addr_id,
                'users_id' => $this->users_id,
                'lang'     => $this->home_lang,
            ];
            $province = $this->shop_address_db->where($where)->getField('province');

            // 通过省份获取运费模板中的运费价格
            $template_money = $this->shipping_template_db->where('province_id', $province)->getField('template_money');
            if ('0.00' == $template_money) {
                // 省份运费价格为0时，使用统一的运费价格，固定ID为100000
                $template_money = $this->shipping_template_db->where('province_id', '100000')->getField('template_money');
            }
            $this->success('查询成功！', '', $template_money);
        } else {
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
            $WeChatLoginConfig = !empty($this->usersConfig['wechat_login_config']) ? unserialize($this->usersConfig['wechat_login_config']) : [];
            $appid     = !empty($WeChatLoginConfig['appid']) ? $WeChatLoginConfig['appid'] : '';
            $appsecret = !empty($WeChatLoginConfig['appsecret']) ? $WeChatLoginConfig['appsecret'] : '';
            
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

    // 判断商品是否库存为0
    private function IsSoldOut($param = array())
    {
       if (!empty($param['aid'])) {
            if (!empty($param['spec_value_id'])) {
                $SpecWhere = [
                    'aid'  => $param['aid'],
                    'lang' => $this->home_lang,
                    'spec_stock' => ['>',0],
                    'spec_value_id' => $param['spec_value_id'],
                ];
                $spec_stock = Db::name('product_spec_value')->where($SpecWhere)->getField('spec_stock');
                if (empty($spec_stock)) {
                    $data['code'] = -1; // 已售罄
                    $this->error('商品已售罄！', null, $data);
                }
                if ($spec_stock < $param['num']) {
                    $data['code'] = -1; // 库存不足
                    $this->error('商品最大库存：'.$spec_stock, null, $data);
                }
            } else {
                $archives_where = [
                    'arcrank' => array('egt','0'), //带审核稿件不查询
                    'aid'     => $param['aid'],
                    'lang'    => $this->home_lang,
                ];
                $stock_count = $this->archives_db->where($archives_where)->getField('stock_count');
                if (empty($stock_count)) {
                    $data['code'] = -1; // 已售罄
                    $this->error('商品已售罄！', null, $data);
                }
                if ($stock_count < $param['num']) {
                    $data['code'] = -1; // 库存不足
                    $this->error('商品最大库存：'.$stock_count, null, $data);
                }
            }
        }
    }


    /*------陈风任---2021-1-12---售后服务(退换货)------开始------*/
    // 申请退换货服务
    public function after_service_apply()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            if (empty($post)) $this->error('提交数据有误！');
            if (empty($post['service_type'])) $this->error('请选择服务类型！');
            if (empty($post['content'])) $this->error('请填写问题描述！');
            if (empty($post['address'])) $this->error('请填写您的收货地址！');
            if (empty($post['consignee'])) $this->error('请填写您的姓名！');
            if (empty($post['mobile'])) $this->error('请填写您的手机号码！');
            $time = getTime();
            $data = [
                'users_id'    => $this->users_id,
                'address'     => $post['addrinfo'].' '.$post['address'],
                'upload_img'  => !empty($post['upload_img']) ? implode(',', $post['upload_img']) : '',
                'refund_balance' => '',
                'refund_point' => '',
                'refund_code' => 'HH' . $time . rand(10,100),
                'add_time'    => $time,
                'update_time' => $time,
            ];
            if (2 == $post['service_type'] && !empty($post['refund_price'])) $data['refund_code'] = 'TK' . $time . rand(10,100);
            $ServiceData = array_merge($post, $data);
            $ResultID = $this->shop_order_service_db->add($ServiceData);
            if (!empty($ResultID)) {
                /*更新订单明细表中对应商品为申请服务*/
                $update = [
                    'apply_service' => 1,
                    'update_time' => getTime()
                ];
                $this->shop_order_details_db->where('details_id', $post['details_id'])->update($update);
                /* END */

                /*添加订单服务记录*/
                $LogNote = 1 == $post['service_type'] ? '会员提交换货申请，待管理员审核！' : '会员提交退货申请，待管理员审核！';
                OrderServiceLog($ResultID, $post['order_id'], $this->users_id, 0, $LogNote);
                /* END */

                $url = url('user/Shop/after_service_details', ['service_id' => $ResultID]);
                $this->success('已申请，待审核', $url);
            } else {
                $this->error('申请失败！');
            }
        }

        $details_id = input('param.details_id/d');
        if (empty($details_id)) $this->error('售后服务单不存在！');

        // 查询订单中单个商品信息
        $data = $this->shop_model->GetOrderDetailsInfo($details_id, $this->users_id);

        // 返回错误提示
        if (!empty($data['msg']) && isset($data['code']) && 0 == $data['code']) $this->error($data['msg']);
        
        // 商户收货信息
        $maddr  = getUsersConfigData('addr');
        
        $eyou = [
            'url'   => url('user/Shop/after_service_apply', ['_ajax'=>1]),
            'maddr' => $maddr,
            'field' => $data
        ];
        $this->assign('eyou', $eyou);
        return $this->fetch('users/shop_after_service_apply');
    }

    // 退货换服务列表
    public function after_service()
    {
        $order_code = input('param.order_code');
        $ServiceInfo = $this->shop_model->GetAllServiceInfo($this->users_id, $order_code);
        $eyou = [
            'field' => [
               'service' => $ServiceInfo['Service'],
               'pageStr' => $ServiceInfo['pageStr'],
            ],
        ];
        $this->assign('eyou', $eyou);
        return $this->fetch('users/shop_after_service');
    }

    // 申请服务详情
    public function after_service_details() {
        /*取消服务单*/
        if (IS_AJAX_POST) {
            $param = input('param.');
            if (empty($param['service_id'])) $this->error('请选择需要取消的服务单！');
            /*取消服务单*/
            $where = [
                'users_id'   => $this->users_id,
                'service_id' => $param['service_id'],
            ];
            $update = [
                'status' => 8,
                'update_time' => getTime(),
            ];
            $ResultID = $this->shop_order_service_db->where($where)->update($update);
            /* END */

            if (!empty($ResultID)) {
                /*更新订单明细表中对应商品为未申请服务*/
                $where = [
                    'users_id'   => $this->users_id,
                    'details_id' => $param['details_id']
                ];
                $update = [
                    'apply_service' => 0,
                    'update_time' => getTime()
                ];
                $this->shop_order_details_db->where($where)->update($update);
                /* END */

                /*添加记录单*/
                $param['users_id'] = $this->users_id;
                $param['status'] = 8;
                $this->shop_common->AddOrderServiceLog($param, 1);
                /* END */

                $this->success('取消成功！');
            } else {
                $this->error('取消失败！');
            }
        }
        /* END */

        $service_id = input('param.service_id/d');

        // 商户收货信息
        $maddr  = getUsersConfigData('addr');

        // 加载模板
        $eyou = [
            'maddr'      => $maddr,
            'field'      => $this->shop_model->GetServiceDetailsInfo($service_id, $this->users_id),
            'ServiceUrl' => url('user/Shop/after_service_update', ['_ajax' => 1]),
            'StatusArr'  => [6, 7, 8],
            'StatusLog'  => $this->shop_model->GetOrderServiceLog($service_id, $this->users),
        ];

        $this->assign('eyou', $eyou);
        return $this->fetch('users/shop_after_service_details');
    }

    // 更新服务单状态
    public function after_service_update()
    {
        if (IS_AJAX_POST) {
            if (empty($this->users_id)) $this->redirect('user/Login/login');
            $post = input('post.');
            if (empty($post['delivery']['cost'])) $post['delivery']['cost'] = 0;

            /*查询条件*/
            $Where = [
                'users_id' => $this->users_id,
                'service_id' => $post['service_id'],
            ];
            /* END */

            /*更新数据*/
            $UpDate = [
                'status' => 4,
                'users_delivery' => serialize($post['delivery']),
                'update_time' => getTime(),
            ];
            /* END */
            
            $ResultID = $this->shop_order_service_db->where($Where)->update($UpDate);
            if (!empty($ResultID)) {
                /*添加记录单*/
                $post['users_id'] = $this->users_id;
                $this->shop_common->AddOrderServiceLog($post, 1);
                /* END */

                $this->success('操作成功！');
            } else {
                $this->error('操作失败！');
            }
        }
    }
    /*------陈风任---2021-1-12---售后服务(退换货)------结束------*/
}