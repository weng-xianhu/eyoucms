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
 * Date: 2020-05-22
 */

namespace app\user\logic;

use think\Db;
use think\Model;
use think\Config;
use think\Request;

/**
 * 支付API逻辑处理
 * @package user\Logic
 */
load_trait('controller/Jump');
class MultiMerchantLogic extends Model
{
    use \traits\controller\Jump;
    private $home_lang = 'cn';

    /**
     * 初始化操作
     */
    public function initialize() {
        parent::initialize();
        // 多语言
        $this->home_lang = get_home_lang();
        $this->users_db = Db::name('users');
        $this->shop_cart_db = Db::name('shop_cart');
        $this->shop_order_db = Db::name('shop_order');
        $this->shop_address_db = Db::name('shop_address');
        $this->pay_api_config_db = Db::name('pay_api_config');
        $this->shop_order_details_db = Db::name('shop_order_details');
        $this->shipping_template_db = Db::name('shop_shipping_template');
        // 会员信息
        $this->users = GetUsersLatestData();
        $this->users_id = !empty($this->users) ? $this->users['users_id'] : 0;
        // 终端判断信息
        $this->isMobile = isMobile();
        $this->isWeixin = isWeixin();
        $this->isWeixinApplets = isWeixinApplets();
    }

    // 多商家订单处理
    public function multiMerchantOrderHandle($list = [], $post = [])
    {
        // 请选择支付方式
        if (empty($post['payment_type'])) $this->error('请选择支付方式');

        // 对商品数据以商家进行商家分组
        $groupList = group_same_key($list, 'merchant_id');

        // 对商品数据以商家为分组进行拆分单操作
        $resultOrder = $this->merchantSplitOrderHandle($groupList, $post);

        // 查询收货地址及运费处理合并到订单后返回
        $merchantOrder = $resultOrder['merchantOrder'];
        if (!empty($resultOrder['needQuery'])) $merchantOrder = $this->merchantOrderAddrInfoHandle($merchantOrder, $post);

        // 数据验证
        $rule = ['payment_method' => 'require|token'];
        $message = ['payment_method.require' => '不可为空'];
        $validate = new \think\Validate($rule, $message);
        if (!$validate->check($post)) $this->error('请勿重复提交订单');

        // 删除数据表不存在的字段
        foreach ($merchantOrder as $key => $value) {
            if (isset($merchantOrder[$key]['free_shipping'])) unset($merchantOrder[$key]['free_shipping']);
        }

        // 执行批量添加
        $insertAll = $this->shop_order_db->insertAll($merchantOrder);
        if (!empty($insertAll)) {
            // 查询订单信息，补全订单信息
            $orderCode = get_arr_column($merchantOrder, 'order_code');
            $where = [
                'users_id' => $this->users_id,
                'order_code' => ['IN', $orderCode],
            ];
            $merchantOrder = $this->shop_order_db->where($where)->getAllWithIndex('merchant_id');

            // 对商家订单数据创建订单明细数据
            $resultDetails = $this->merchantOrderDetailsHandle($merchantOrder, $list, $this->users_id);
            if (empty($resultDetails['code'])) $this->error($resultDetails['msg']);

            // 批量添加订单明细信息
            $insertAll = !empty($resultDetails['data']['orderDetails']) ? $this->shop_order_details_db->insertAll($resultDetails['data']['orderDetails']) : 0;
            if (!empty($insertAll)) {
                // 删除商品购物车信息
                if (!empty($resultDetails['data']['delCartID'])) $this->delCartProductData($resultDetails['data']['delCartID']);

                // 产品库存、销量处理
                model('Shop')->ProductStockProcessing($resultDetails['data']['productStock']);

                // 添加订单操作记录
                AddOrderAction($merchantOrder, $this->users_id);

                // 订单支付调用
                if (0 === intval($post['payment_method'])) {
                    if ('yezf_balance' === strval($post['payment_type'])) {
                        // 使用余额支付订单
                        $this->useBalancePayOrder($merchantOrder, $post);
                    } else {
                        // 微信、支付宝、第三方在线支付
                        if (empty($this->isMobile) && empty($this->isWeixin)) {
                            // 电脑PC端下单 WeChatScanCode
                            $this->computerPCSideSubmitOrder($merchantOrder, $post);
                        } else if (!empty($this->isMobile) && !empty($this->isWeixin)) {
                            // 手机端微信下单 WeChatInternal  
                            $this->mobileWeChatSubmitOrder($merchantOrder, $post);
                        } else if (!empty($this->isMobile) && empty($this->isWeixinApplets)) {
                            // 手机端浏览器下单 WeChatH5
                            $this->mobileBrowserSubmitOrder($merchantOrder, $post);
                        } else if (!empty($this->isMobile) && !empty($this->isWeixinApplets)) {
                            // 手机微信小程序端下单
                            dump(4);exit;
                        }
                    }
                } else {
                    // 使用货到付款完成订单
                    $this->useCashOnDeliveryCompletedOrder($merchantOrder, $post);
                }
            } else {
                // 删除刚新增的订单主表数据
                $this->shop_order_db->where($where)->delete(true);
                $this->error('错误代码：401，订单生成失败，商品数据有误');
            }
        } else {
            $this->error('错误代码：402，订单生成失败，商品数据有误');
        }
    }

    // 对商品数据以商家为分组进行拆分单操作
    private function merchantSplitOrderHandle($list = [], $post = [])
    {
        $needQuery = 0;
        $merchantOrder = [];
        foreach ($list as $key => $value) {
            // 如果为空则跳过
            if (empty($value)) continue;
            $time = getTime();
            $merchantOrder[$key] = [
                'order_code'    => date('Ymd') . $time . rand(1000, 9999),
                'users_id'      => intval($this->users_id),
                'merchant_id'   => intval($key),
                'free_shipping' => 1,
                'order_status'  => 0,
                'payment_method' => !empty($post['payment_method']) ? intval($post['payment_method']) : 0,
                'lang'          => $this->home_lang,
                'add_time'      => $time,
                'pay_details'   => '',
                'virtual_delivery' => '',
                'admin_note'    => '',
                'user_note'     => !empty($post['message']) ? strval($post['message']) : '',
            ];

            // 判断订单来源
            if (empty($this->isMobile) && empty($this->isWeixin)) {
                // 电脑PC端
                $merchantOrder[$key]['order_terminal'] = 1;
            } else if (!empty($this->isMobile) && empty($this->isWeixinApplets)) {
                // 手机端
                $merchantOrder[$key]['order_terminal'] = 2;
            } else if (!empty($this->isMobile) && !empty($this->isWeixinApplets)) {
                // 微信小程序
                $merchantOrder[$key]['order_terminal'] = 3;
            }

            // 手机微信端则执行
            if (!empty($this->isMobile) && !empty($this->isWeixin)) {
                $merchantOrder[$key]['pay_name'] = 'wechat';
                $merchantOrder[$key]['wechat_pay_type'] = 'WeChatInternal';
            }

            // 选择货到付款则执行
            if (1 === intval($merchantOrder[$key]['payment_method'])) {
                $merchantOrder[$key]['order_status'] = 1;
                $merchantOrder[$key]['pay_time'] = $time;
                $merchantOrder[$key]['pay_name'] = 'delivery_pay';
                $merchantOrder[$key]['wechat_pay_type'] = '';
                $merchantOrder[$key]['update_time']  = $time;
            }

            // 产品数据处理
            $PromType = $ContainsVirtual = 1;
            $TotalAmount = $TotalNumber = 0;
            foreach ($value as $v_key => $v_value) {
                // 金额、数量计算
                if ($v_value['users_price'] >= 0 && !empty($v_value['product_num'])) {
                    // 合计金额
                    $TotalAmount += sprintf("%.2f", $v_value['users_price'] * $v_value['product_num']);
                    // 合计数量
                    $TotalNumber += $v_value['product_num'];
                    // 判断订单类型，目前逻辑：一个订单中，只要存在一个普通产品(实物产品，需要发货物流)，则为普通订单，0表示为普通订单
                    if (empty($v_value['prom_type'])) $PromType = 0;
                    // 判断是否包含虚拟商品，只要存在一个虚拟商品则表示包含虚拟商品
                    if (!empty($value['prom_type']) && intval($value['prom_type']) >= 1) $ContainsVirtual = 2;
                }
                $merchantOrder[$key]['order_amount'] = $TotalAmount;
                $merchantOrder[$key]['order_total_amount'] = $TotalAmount;
                $merchantOrder[$key]['order_total_num'] = $TotalNumber;
                $merchantOrder[$key]['prom_type'] = $PromType;
                $merchantOrder[$key]['contains_virtual']  = $ContainsVirtual;
                if (empty($v_value['free_shipping'])) $merchantOrder[$key]['free_shipping'] = 0;
            }

            // 是否需要查看地址信息
            if (0 === intval($PromType)) $needQuery = 1;
        }

        return [
            'needQuery' => $needQuery,
            'merchantOrder' => $merchantOrder
        ];
    }

    // 查询收货地址及运费处理
    private function merchantOrderAddrInfoHandle($merchantOrder = [], $post = [])
    {
        // 如果没有提交地址信息则执行
        if (empty($post['addr_id'])) {
            // 在微信端并且不在小程序中，跳转至收货地址添加选择页
            if (!empty($this->isWeixin) && !empty($this->isWeixinApplets)) {
                $this->success('101：选择添加地址方式', url('user/Shop/shop_get_wechat_addr'), ['is_gourl'=>1]);
            } else {
                $this->error('101：订单生成失败，请添加收货地址', null, ['add_addr' => 1, 'is_mobile' => $this->isMobile]);
            }
        }

        // 查询收货地址
        $where = [
            'addr_id'  => $post['addr_id'],
            'users_id' => $this->users_id,
        ];
        $addrInfo = $this->shop_address_db->where($where)->find();
        if (empty($addrInfo)) {
            // 在微信端并且不在小程序中，跳转至收货地址添加选择页
            if (!empty($this->isWeixin) && !empty($this->isWeixinApplets)) {
                $this->success('102：选择添加地址方式', url('user/Shop/shop_get_wechat_addr'), ['is_gourl'=>1]);
            } else {
                $this->error('102：订单生成失败，请添加收货地址', null, ['add_addr' => 1, 'is_mobile' => $this->isMobile]);
            }
        }

        // 查询运费问题
        $shippingFee = 0;
        $shopOpenShipping = getUsersConfigData('shop.shop_open_shipping');
        if (!empty($shopOpenShipping)) {
            // 通过省份获取运费模板中的运费价格
            $shippingFee = $this->shipping_template_db->where('province_id', $addrInfo['province'])->getField('template_money');
            if (0 >= $shippingFee) {
                // 省份运费价格为0时，使用统一的运费价格，固定ID为100000
                $shippingFee = $this->shipping_template_db->where('province_id', '100000')->getField('template_money');
            }
        }

        // 订单地址信息
        $addrData = [
            'consignee' => $addrInfo['consignee'],
            'country'   => $addrInfo['country'],
            'province'  => $addrInfo['province'],
            'city'      => $addrInfo['city'],
            'district'  => $addrInfo['district'],
            'address'   => $addrInfo['address'],
            'mobile'    => $addrInfo['mobile'],
            'shipping_fee' => $shippingFee
        ];

        // 合并收货地址并计算运费
        if (!empty($addrData)) {
            $merchantOrder = !empty($merchantOrder) ? $merchantOrder : [];
            foreach ($merchantOrder as $key => $value) {
                if (isset($value['free_shipping']) && 0 === intval($value['free_shipping']) && !empty($addrData['shipping_fee'])) {
                    $value['order_amount'] += $addrData['shipping_fee'];
                } else {
                    $addrData['shipping_fee'] = 0;
                }
                // 删除数据表不存在的字段
                unset($value['free_shipping']);
                // 整合订单数据
                $merchantOrder[$key] = array_merge($value, $addrData);
            }
        }

        return $merchantOrder;
    }

    // 商家订单详情处理
    private function merchantOrderDetailsHandle($merchantOrder = [], $list = [], $users_id = 0)
    {
        // 订单详情、删除的购物车ID、商品更新库存信息
        $orderDetails = $delCartID = $productStock = [];

        // 添加到订单明细表
        foreach ($list as $key => $value) {
            // 旧产品属性处理
            $attrValue = model('Shop')->ProductAttrProcessing($value);
            // 新产品属性处理
            $attrValueNew = model('Shop')->ProductNewAttrProcessing($value);
            // 产品规格处理
            $specValue = model('Shop')->ProductSpecProcessing($value);
            // 商品自定义参数
            $customParam = $this->getProductCustomParam($value['aid']);
            $data = [
                // 产品属性
                'attr_value' => htmlspecialchars($attrValue),
                // 产品属性
                'attr_value_new' => htmlspecialchars($attrValueNew),
                // 产品规格
                'spec_value' => htmlspecialchars($specValue),
                // 产品规格值ID
                'spec_value_id' => $value['spec_value_id'],
                // 对应规格值ID的唯一标识ID，数据表主键ID
                'value_id' => $value['value_id'],
                // 商品自定义参数
                'custom_param' => $customParam,
                // 后续添加
            ];

            // 订单副表添加数组
            $orderDetails[] = [
                'order_id'      => intval($merchantOrder[$value['merchant_id']]['order_id']),
                'users_id'      => intval($users_id),
                'product_id'    => intval($value['aid']),
                'product_name'  => strval($value['title']),
                'num'           => intval($value['product_num']),
                'data'          => serialize($data),
                'product_price' => floatval($value['users_price']),
                'prom_type'     => intval($value['prom_type']),
                'litpic'        => strval($value['litpic']),
                'add_time'      => getTime(),
                'lang'          => $this->home_lang
            ];

            // 处理购物车ID
            if (!empty($value['cart_id'])) array_push($delCartID, $value['cart_id']);

            // 产品库存处理
            $productStock[] = [
                'aid' => $value['aid'],
                'value_id' => $value['value_id'],
                'quantity' => $value['product_num'],
                'spec_value_id' => $value['spec_value_id']
            ];
        }

        $result = [
            'code' => 1,
            'msg'  => 'ok',
            'data' => [
                'delCartID' => $delCartID,
                'productStock' => $productStock,
                'orderDetails' => $orderDetails,
            ]
        ];
        return $result;
    }

    // 获取商品自定义参数
    private function getProductCustomParam($aid = 0)
    {
        $result = '';
        if (!empty($aid)) {
            $where = [
                'aid' => $aid
            ];
            $customParam = Db::name('product_custom_param')->field('param_name, param_value')->where($where)->order('sort_order asc')->select();
            if (empty($customParam)) return $result;
            foreach ($customParam as $value) {
                $result .= $value['param_name'] . '：' . $value['param_value'] . '<br/>';
            }
        }
        return $result;
    }

    // 删除商品购物车信息
    private function delCartProductData($delCartID = [])
    {
        $where = [
            'cart_id' => ['IN', $delCartID]
        ];
        $this->shop_cart_db->where($where)->delete(true);
    }

    // 使用余额支付订单
    private function useBalancePayOrder($merchantOrder = [], $post = [])
    {
        // 跳转链接
        $url = urldecode(url('user/Shop/shop_centre'));

        // 创建统一支付订单(合并订单进行支付)
        $unifiedPay = $this->createShopOrderUnifiedPay($merchantOrder, 'balance');

        // 判断余额是否足够支付
        if (floatval($this->users['users_money']) < floatval($unifiedPay['unified_amount'])) {
            $this->error('余额不足，支付失败', null, ['url' => $url]);
        } else {
            // 进行余额支付
            $where = [
                'users_id' => $this->users_id
            ];
            $update = [
                'users_money' => Db::raw('users_money-' . $unifiedPay['unified_amount']),
                'update_time' => getTime(),
            ];
            $updateID = Db::name('users')->where($where)->update($update);
            if (!empty($updateID)) {
                // 添加余额记录
                $users = empty($this->users) ? Db::name('users')->find($this->users_id) : $this->users;
                UsersMoneyRecording($unifiedPay['unified_number'], $users, $unifiedPay['unified_amount'], '商品购买', 3);

                // 统一支付成功处理
                $this->unifiedPaySuccessHandle($unifiedPay, 'balance');
            } else {
                $this->error('错误代码：403，余额支付异常，请在订单列表进行支付', null, ['url' => $url]);
            }
        }
    }

    // 电脑PC端下单
    private function computerPCSideSubmitOrder($merchantOrder = [], $post = [])
    {
        // 查询支付方式
        $payApiConfig = $this->getSpecifyPayApiConfig($post['payment_type']);

        // 创建统一支付订单(合并订单进行支付)
        $unifiedPay = $this->createShopOrderUnifiedPay($merchantOrder, $payApiConfig['pay_mark'], 'WeChatScanCode');

        // 判断是否生成统一支付订单信息
        if (!empty($unifiedPay)) {
            // 返回支付所需参数
            $payData = [
                'transaction_type' => 99, // 多商家处理标识
                'code'           => 'order_status_0',
                'pay_id'         => $payApiConfig['pay_id'],
                'pay_mark'       => $payApiConfig['pay_mark'],
                'unified_id'     => $unifiedPay['unified_id'],
                'unified_number' => $unifiedPay['unified_number'],
            ];
            $this->success('正在支付中', url('user/Shop/shop_centre'), $payData);
        } else {
            $this->error('错误代码：404，批量支付异常，请在订单列表进行支付', urldecode(url('user/Shop/shop_centre')));
        }
    }

    // 手机端浏览器下单
    private function mobileBrowserSubmitOrder($merchantOrder = [], $post = [])
    {
        // 查询支付方式
        $payApiConfig = $this->getSpecifyPayApiConfig($post['payment_type']);

        // 创建统一支付订单(合并订单进行支付)
        $unifiedPay = $this->createShopOrderUnifiedPay($merchantOrder, $payApiConfig['pay_mark'], 'WeChatH5');

        // 判断是否生成统一支付订单信息
        if (!empty($unifiedPay)) {
            // 返回支付所需参数
            $payData = [
                'transaction_type' => 99, // 多商家处理标识
                'code'           => 'order_status_0',
                'pay_id'         => $payApiConfig['pay_id'],
                'pay_mark'       => $payApiConfig['pay_mark'],
                'unified_id'     => $unifiedPay['unified_id'],
                'unified_number' => $unifiedPay['unified_number'],
            ];
            $this->success('正在支付中', url('user/Shop/shop_centre'), $payData);
        } else {
            $this->error('错误代码：405，批量支付异常，请在订单列表进行支付', urldecode(url('user/Shop/shop_centre')));
        }
    }

    // 手机端微信下单
    private function mobileWeChatSubmitOrder($merchantOrder = [], $post = [])
    {
        // 查询支付方式
        $payApiConfig = $this->getSpecifyPayApiConfig($post['payment_type']);

        // 创建统一支付订单(合并订单进行支付)
        $unifiedPay = $this->createShopOrderUnifiedPay($merchantOrder, $payApiConfig['pay_mark'], 'WeChatInternal');

        // 判断是否生成统一支付订单信息
        if (!empty($unifiedPay)) {
            // 返回支付所需参数
            $payData = [
                'transaction_type' => 99, // 多商家处理标识
                'code'           => 'order_status_0',
                'pay_id'         => $payApiConfig['pay_id'],
                'pay_mark'       => $payApiConfig['pay_mark'],
                'unified_id'     => $unifiedPay['unified_id'],
                'unified_number' => $unifiedPay['unified_number'],
            ];
            $this->success('正在支付中', url('user/Shop/shop_centre'), $payData);
        } else {
            $this->error('错误代码：406，批量支付异常，请在订单列表进行支付', urldecode(url('user/Shop/shop_centre')));
        }
    }

    // 使用货到付款完成订单
    private function useCashOnDeliveryCompletedOrder($merchantOrder = [], $post = [])
    {
        // 跳转链接
        $url = urldecode(url('user/Shop/shop_centre'));

        // 添加订单操作记录
        $orderID = get_arr_column($merchantOrder, 'order_id');
        AddOrderAction($orderID, $this->users_id, 0, 1, 0, 1, '货到付款', '会员选择货到付款，款项由快递代收');

        // 邮箱发送
        $data['email'] = GetEamilSendData();
        // $SmtpConfig = tpCache('smtp');
        // $data['email'] = GetEamilSendData($SmtpConfig, $this->users, $merchantOrder, 1, 'delivery_pay');

        // 手机发送
        $data['mobile'] = GetMobileSendData();
        // $SmsConfig = tpCache('sms');
        // $data['mobile'] = GetMobileSendData($SmsConfig, $this->users, $merchantOrder, 1, 'delivery_pay');

        // 返回结束
        $this->success('下单成功，跳转订单列表...', $url, $data);
    }

    // 创建统一支付订单(合并订单进行支付)
    private function createShopOrderUnifiedPay($merchantOrder = [], $payName = '', $wechatPayType = '')
    {
        // 支付总额处理
        $unifiedAmount = 0;
        $orderID = [];
        foreach ($merchantOrder as $value) {
            $unifiedAmount += $value['order_amount'];
            array_push($orderID, $value['order_id']);
        }
        // 添加统一支付订单数据
        $time = getTime();
        $insert = [
            'unified_number' => date('Ymd') . $time . rand(1000, 9999),
            'unified_amount' => $unifiedAmount,
            'users_id' => $this->users_id,
            'order_ids' => serialize($orderID),
            'pay_status' => 0,
            'pay_time' => '',
            'pay_name' => $payName,
            'wechat_pay_type' => $wechatPayType,
            'add_time' => $time,
            'update_time' => $time
        ];
        $insertID = Db::name('shop_order_unified_pay')->insertGetId($insert);
        if (!empty($insertID)) {
            $insert['unified_id'] = $insertID;
            return $insert;
        } else {
            $this->error('错误代码：490，批量支付异常，请在订单列表进行支付', urldecode(url('user/Shop/shop_centre')));
        }
    }

    // 查询支付方式
    private function getSpecifyPayApiConfig($paymentType = '')
    {
        // 内置第三方在线支付
        $paymentTypeArr = explode('_', $paymentType);
        $payMark = !empty($paymentTypeArr[1]) ? $paymentTypeArr[1] : '';
        $payApiRow = Db::name('pay_api_config')->where(['pay_mark' => $payMark, 'lang' => $this->home_lang])->find();
        if (empty($payApiRow)) $this->error('错误代码：491，请选择正确的支付方式');
        return $payApiRow;
    }

    // 统一支付成功后续订单处理
    public function unifiedPaySuccessHandle($unifiedPay = [], $payName = '', $payDetails = [], $notify = true)
    {
        // 跳转链接
        $url = urldecode(url('user/Shop/shop_centre'));

        // 判断是否进行下一步操作
        if (empty($unifiedPay)) $this->error('错误代码：492，支付异常，请在订单列表进行支付', null, ['url' => $url]);

        // 下单成功
        if (!empty($unifiedPay['pay_status']) && 1 === intval($unifiedPay['pay_status'])) $this->success('下单成功，跳转订单列表...', $url);

        // 订单未处理，进行处理
        if (isset($unifiedPay['pay_status']) && 0 === intval($unifiedPay['pay_status'])) {
            // 解析统一订单包含的子订单ID
            $unifiedPay['order_ids'] = !empty($unifiedPay['order_ids']) ? unserialize($unifiedPay['order_ids']) : [];

            // 更新订单为已支付
            $where = [
                'users_id' => $unifiedPay['users_id'],
                'unified_id' => $unifiedPay['unified_id']
            ];
            $update = [
                'pay_status' => 1,
                'pay_name' => $payName,
                'pay_time' => getTime(),
                'update_time' => getTime(),
            ];
            if ('balance' === strval($payName)) {
                $update['wechat_pay_type'] = '';
            } else if ('alipay' === strval($payName)) {
                $update['wechat_pay_type'] = '';
            }
            $updateID = Db::name('shop_order_unified_pay')->where($where)->update($update);
            if (!empty($updateID)) {
                // 更新订单变量，保存最新数据
                $unifiedPay = array_merge($unifiedPay, $update);

                // 更新统一支付订单下的所有订单
                if (empty($payDetails)) {
                    $payDetails = [
                        'transaction_type' => 2,
                        'payment_type'     => "余额支付",
                        'unified_id'       => $unifiedPay['unified_id'],
                        'unified_number'   => $unifiedPay['unified_number'],
                        'unified_amount'   => $unifiedPay['unified_amount']
                    ];
                }
                $where = [
                    'users_id' => $unifiedPay['users_id'],
                    'order_id' => ['IN', $unifiedPay['order_ids']]
                ];
                $update = [
                    'order_status' => 1,
                    'pay_time' => $unifiedPay['pay_time'],
                    'pay_name' => $unifiedPay['pay_name'],
                    'wechat_pay_type' => $unifiedPay['wechat_pay_type'],
                    'pay_details'  => serialize($payDetails),
                    'update_time'  => getTime(),
                ];
                $this->shop_order_db->where($where)->update($update);

                // 添加订单操作记录
                $orderID = [];
                foreach ($unifiedPay['order_ids'] as $key => $value) {
                    $orderID[$key]['order_id'] = $value;
                }
                $pay_method_arr = config('global.pay_method_arr');
                AddOrderAction($orderID, $unifiedPay['users_id'], 0, 1, 0, 1, "支付成功", "使用{$pay_method_arr[$unifiedPay['pay_name']]}完成支付");

                // 下单成功
                $this->success('下单成功，跳转订单列表...', $url);
            } else {
                $this->error('错误代码：493，订单处理异常，请联系管理员处理', null, ['url' => $url]);
            }
        }
    }

    // 获取多商家地址信息(用于售后服务商家地址显示)
    public function getMultiMerchantContact($merchantID = 0)
    {
        $where = [
            'merchant_id' => $merchantID,
        ];
        $merchantContact = Db::name('weapp_multi_merchant')->where($where)->getField('merchant_contact');
        if (!empty($merchantContact)) {
            $merchantContact = unserialize($merchantContact);
            $contactProvince = !empty($merchantContact['contactProvince']) ? get_province_name($merchantContact['contactProvince']) : '';
            $contactCity = !empty($merchantContact['contactCity']) ? get_city_name($merchantContact['contactCity']) : '';
            $contactDistrict = !empty($merchantContact['contactDistrict']) ? get_area_name($merchantContact['contactDistrict']) : '';
            $result = [
                'addr_contact_person' => $merchantContact['contactName'],
                'addr_contact_phone' => $merchantContact['contactPhone'],
                'addr_shipping_addr' => $contactProvince . ' ' . $contactCity . ' ' . $contactDistrict . ' ' . $merchantContact['contactAddress'],
            ];
            return $result;
        } else {
            $this->error('商家未设置收货地址，请联系商家..');
        }
    }

    // 处理多商家订单金额结算
    public function handleMultiMerchantOrderSettle($shopOrder = [])
    {
        if (!empty($shopOrder['merchant_id']) && !empty($shopOrder['order_amount'])) {
            // 将订单金额存入商家可用余额
            $where = [
                'is_del' => 0,
                'audit_status' => 2,
                'merchant_status' => 1,
                'merchant_id' => $shopOrder['merchant_id']
            ];
            Db::name('weapp_multi_merchant')->where($where)->setInc('available_balance', $shopOrder['order_amount']);
        }
    }
}