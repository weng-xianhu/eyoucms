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
 * Date: 2021-04-27
 */

namespace app\common\model;

use think\Db;
use think\Model;
use think\Cache;

/**
 * 订单预处理模型
 */
class OrderPreHandle extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        // 接收参数
        $this->users_id = 0;
        $this->usersConfig = [];
    }
    
    // 订单预处理 (自动关闭未付款订单  发货后自动确认收货  收货后超过维权时间则关闭维权入口)
    public function eyou_shopOrderPreHandle($users_id = 0, $usersConfig = [], $action = 'users')
    {
        $orderHandleTimes = Cache::get('orderHandleTimes_' . $users_id);
        if (empty($orderHandleTimes)) {
            // 设置1秒过期
            Cache::set('orderHandleTimes_' . $users_id, true, 1);

            // 参数处理
            $this->users_id = !empty($users_id) ? intval($users_id) : 0;
            $this->usersConfig = !empty($usersConfig) ? $usersConfig : getUsersConfigData('all');

            // 超过 未付款自动关闭时间 则修改为已订单过期，仅针对待付款订单
            if (!empty($this->usersConfig['order_unpay_close_time'])) $this->eyou_paymentOrderHandle();

            // 超过 发货后自动收货时间 则修改自动确认收货，仅针对待收货订单
            if (!empty($this->usersConfig['order_auto_receipt_time'])) $this->eyou_receivedOrderHandle();

            // 超过 收货后可维权时间 则修改自动更新为不允许申请维权，仅针对已收货订单
            if (!empty($this->usersConfig['order_right_protect_time'])) $this->eyou_receiptOrderHandle();

            // 查询 (需要赠送积分 && 不可维权) 订单，执行赠送积分
            $this->eyou_consumObtainScoresHandle();

            // 查询 分销商的分销订单执行分销订单结算分佣处理
            if ('users' == $action) $this->eyou_dealerOrderSettlementHandle();

            // 查询 (尚未累计用户消费额 && 不可维权) 订单，执行累计用户消费额
            if ('users' == $action) $this->eyou_handleUsersOrderTotalAmount();
        }
    }

    // 超过 未付款自动关闭时间 则修改为已订单过期，仅针对待付款订单
    private function eyou_paymentOrderHandle()
    {
        // 计算订单过期时间
        $orderUnpayCloseTime = intval($this->usersConfig['order_unpay_close_time']) * 60;
        // 查询过期的订单
        $time = getTime() - intval($orderUnpayCloseTime);
        $where = [
            'order_status' => 0,
            'add_time' => ['<', $time],
        ];
        //秒杀订单走秒杀独立的关闭逻辑
        if (is_dir('./weapp/Seckill/')) {
            $where['is_seckill_order'] = 0;
        }
        // 查询条件-会员ID处理
        if (!empty($this->users_id)) $where['users_id'] = $this->users_id;
        $shopOrder = Db::name('shop_order')->where($where)->select();
        if (!empty($shopOrder)) {
            $where = [
                'order_id' => ['IN', get_arr_column($shopOrder, 'order_id')]
            ];
            // 恢复优惠券
            $this->restoreCoupon($where);

            // 商品库存恢复
            // $orderStock = $shopOrder;
            // foreach ($orderStock as $key => $value) {
            //     // 如果订单是付款减库存则去除，订单过期不恢复库存
            //     if (isset($value['order_stock_type']) && 1 === intval($value['order_stock_type'])) unset($orderStock[$key]);
            // }
            $this->restoreGoodsStock($shopOrder);

            // 删除未付款订单及关联数据
            Db::name('shop_order')->where($where)->delete(true);
            Db::name('shop_order_log')->where($where)->delete(true);
            Db::name('shop_order_details')->where($where)->delete(true);

            // 更新订单为已过期
            // $update = [
            //     'order_status' => 4,
            //     'update_time' => getTime(),
            // ];
            // Db::name('shop_order')->where($where)->update($update);

            // 添加订单操作记录
            // $actionNote = '订单未在' . $this->usersConfig['order_unpay_close_time'] . '分钟内完成支付，系统自动关闭！';
            // AddOrderAction($shopOrder, 0, 0, 4, 0, 0, '订单过期', $actionNote);
        }
        //秒杀订单走秒杀独立的关闭逻辑
        if (is_dir('./weapp/Seckill/')) {
            $seckill_config = getUsersConfigData('seckill');
            if (!empty($seckill_config['seckill_close_order_type']) && !empty($seckill_config['seckill_close_order_time'])){
                $time = intval($seckill_config['seckill_close_order_time']) * 60;
                $where = [
                    'order_status' => 0,
                    'add_time' => ['<', $time],
                    'is_seckill_order' => ['>', 0],
                ];
                $shopOrder = Db::name('shop_order')->field('order_id,users_id')->where($where)->select();
                if (!empty($shopOrder)) {
                    $del_where = [
                        'order_id' => ['IN', get_arr_column($shopOrder, 'order_id')]
                    ];
                    // 调用秒杀逻辑层方法
                    $weappSeckillLogic = new \weapp\Seckill\logic\SeckillLogic;
                    foreach ($shopOrder as $k => $v){
                        $weappSeckillLogic->cancelOrderHandle($v['order_id'], $v['users_id']);
                    }

                    // 删除未付款订单及关联数据
                    Db::name('shop_order')->where($del_where)->delete(true);
                    Db::name('shop_order_log')->where($del_where)->delete(true);
                    Db::name('shop_order_details')->where($del_where)->delete(true);
                }
            }
        }
    }

    // 恢复优惠券
    private function restoreCoupon($where = [])
    {
        $useID = Db::name('shop_order')->where($where)->column('use_id');
        if (!empty($useID)) {
            $times = getTime();
            $where = [
                'use_id' => ['IN', $useID]
            ];
            $couponUse = Db::name('shop_coupon_use')->where($where)->select();
            if (!empty($couponUse)) {
                foreach ($couponUse as $key => $value) {
                    $where = [
                        'use_id' => $value['use_id'],
                        'users_id' => $value['users_id'],
                    ];
                    $update = [
                        'use_time' => 0,
                        'use_status' => 2,
                        'update_time' => $times
                    ];
                    if ($value['start_time'] <= $times && $value['end_time'] >= $times) $update['use_status'] = 0;
                    Db::name('shop_coupon_use')->where($where)->update($update);
                }
            }
        }
    }

    // 恢复商品库存
    public function restoreGoodsStock($order_id)
    {
        // 查询订单商品
        $where = [];
        // 查询条件-会员ID处理
        if (!empty($this->users_id)) $where['a.users_id'] = $this->users_id;
        // 查询条件-订单ID处理
        if (is_array($order_id)) {
            $where['a.order_id'] = ['IN', get_arr_column($order_id, 'order_id')];
        } else {
            $where['a.order_id'] = $order_id;
        }
        $field = 'a.product_id, a.num as product_num, a.data as product_data, b.aid, b.value_id, b.spec_value_id';
        $orderDetails = Db::name('shop_order_details')
            ->alias('a')
            ->where($where)
            ->field($field)
            ->join('__PRODUCT_SPEC_VALUE__ b', 'a.product_id = b.aid', 'LEFT')
            ->select();
        if (!empty($orderDetails)) {
            // 循环组装数据
            $arcData = $specData = $pointsGoods = [];
            foreach ($orderDetails as $key => $value) {
                $productData = !empty($value['product_data']) ? unserialize($value['product_data']) : [];
                if (!empty($productData['pointsGoodsBuyField'])) {
                    !in_array($productData['pointsGoodsBuyField'], $pointsGoods) && array_push($pointsGoods, $productData['pointsGoodsBuyField']);
                } else {
                    $spec_value_id = !empty($productData['spec_value_id']) ? $productData['spec_value_id'] : 0;
                    if (!empty($value['value_id']) && !empty($value['spec_value_id'])) {
                        $where = [
                            'aid' => $value['product_id'],
                            'spec_value_id' => $spec_value_id,
                        ];
                        $value_id = Db::name('product_spec_value')->where($where)->getField('value_id');
                        if (intval($value['value_id']) === intval($value_id) && intval($value['spec_value_id']) === intval($spec_value_id)) {
                            // 有规格
                            $specData[] = [
                                'value_id' => $value['value_id'],
                                'spec_stock' => Db::raw('spec_stock+' . ($value['product_num'])),
                                'spec_sales_num' => Db::raw('spec_sales_num-' . ($value['product_num'])),
                            ];
                            // 无规格
                            $arcData[] = [
                                'aid' => $value['product_id'],
                                'stock_count' => Db::raw('stock_count+' . ($value['product_num'])),
                                'sales_num'   => Db::raw('sales_num-' . ($value['product_num']))
                            ];
                        }
                    } else {
                        // 无规格
                        $arcData[] = [
                            'aid' => $value['product_id'],
                            'stock_count' => Db::raw('stock_count+' . ($value['product_num'])),
                            'sales_num'   => Db::raw('sales_num-' . ($value['product_num']))
                        ];
                    }
                }
            }

            // 更新规格库存销量
            if (!empty($specData)) {
                $productSpecValueModel = new \app\user\model\ProductSpecValue();
                $productSpecValueModel->saveAll($specData);
                Db::name('product_spec_value')->where(['spec_sales_num'=>['lt',0]])->update(['spec_sales_num'=>0, 'update_time'=>getTime()]);
            }

            // 更新商品库存销量
            if (!empty($arcData)) {
                $archivesModel = new \app\user\model\Archives();
                $archivesModel->saveAll($arcData);
                Db::name('archives')->where(['sales_num'=>['lt',0]])->update(['sales_num'=>0, 'update_time'=>getTime()]);
            }

            // 积分商品库存处理
            if (!empty($pointsGoods)) {
                $weappInfo = model('ShopPublicHandle')->getWeappPointsShop();
                if (!empty($weappInfo)) {
                    $pointsGoodsModel = new \app\plugins\model\PointsGoods();
                    $pointsGoodsModel->updatePointsGoodsStock($pointsGoods, 'increase');
                }
            }
        }
    }

    // 超过 发货后自动收货时间 则修改自动确认收货，仅针对待收货订单
    private function eyou_receivedOrderHandle()
    {
        // 计算订单自动收货时间
        $orderAutoReceiptTime = intval($this->usersConfig['order_auto_receipt_time']) * 86400;
        // 查询待收货订单
        $time = getTime() - intval($orderAutoReceiptTime);
        $where = [
            // 'prom_type' => 0,
            'order_status' => 2,
            'express_time' => ['<', $time],
        ];
        // 查询条件-会员ID处理
        if (!empty($this->users_id)) $where['users_id'] = $this->users_id;
        $order_ids = Db::name('shop_order')->field('order_id')->where($where)->select();
        if (!empty($order_ids)) {
            // 更新订单为已收货
            $update = [
                'order_status' => 3,
                'update_time' => getTime(),
                'confirm_time' => getTime(),
            ];
            Db::name('shop_order')->where($where)->update($update);

            // 添加订单操作记录
            $actionNote = '订单已超过' . $this->usersConfig['order_auto_receipt_time'] . '天，用户未确认收货，系统自动收货！';
            AddOrderAction($order_ids, 0, 0, 3, 0, 0, '自动收货', $actionNote);
        }
    }

    // 超过 收货后可维权时间 则修改自动更新为不允许申请维权，仅针对已收货订单
    private function eyou_receiptOrderHandle()
    {
        // 计算订单可维权时间
        $orderRightProtectTime = intval($this->usersConfig['order_right_protect_time']) * 86400;
        // 查询待收货订单
        $time = getTime() - intval($orderRightProtectTime);
        $where = [
            'order_status' => 3,
            'allow_service' => 0,
            'confirm_time' => ['<', $time],
        ];
        // 查询条件-会员ID处理
        if (!empty($this->users_id)) $where['users_id'] = $this->users_id;
        $order_ids = Db::name('shop_order')->field('order_id')->where($where)->select();
        if (!empty($order_ids)) {
            // 更新订单为不允许申请维权
            $update = [
                'allow_service' => 1,
                'update_time' => getTime(),
            ];
            Db::name('shop_order')->where($where)->update($update);

            // 添加订单操作记录
            $actionNote = '订单已超过' . $this->usersConfig['order_right_protect_time'] . '天，不再允许申请售后维权！';
            AddOrderAction($order_ids, 0, 0, 3, 0, 0, '关闭维权', $actionNote);
        }
    }

    // 查询 (需要赠送积分 && 不可维权) 订单，执行赠送积分
    private function eyou_consumObtainScoresHandle()
    {
        // 查询订单
        $where = [
            'order_status' => 3,
            'allow_service' => 1,
            'is_obtain_scores' => 0,
            'obtain_scores' => ['>', 0],
        ];
        // 查询条件-会员ID处理
        if (!empty($this->users_id)) $where['users_id'] = $this->users_id;
        // 查询订单id数组用于添加订单操作记录
        $shopOrder = Db::name('shop_order')->where($where)->select();
        if (!empty($shopOrder)) {
            // 查询该订单是否已申请过售后
            $where1 = [
                'order_id' => ['IN', get_arr_column($shopOrder, 'order_id')],
            ];
            $orderService = Db::name('shop_order_service')->where($where1)->field('service_id, order_id, status')->getAllWithIndex('order_id');
            if (!empty($orderService)) {
                foreach ($shopOrder as $key => $value) {
                    if (!empty($orderService[$value['order_id']])) unset($shopOrder[$key]);
                }
            }

            foreach ($shopOrder as $key => $value) {
                if (!empty($value['users_id']) && !empty($value['obtain_scores'])) {
                    // 赠送会员积分
                    $insert = [
                        'type' => 8,
                        'users_id' => $value['users_id'],
                        'score' => $value['obtain_scores'],
                        'info' => '商城消费赠送',
                        'remark' => '商城消费赠送',
                    ];
                    addConsumObtainScores($insert, 2, true);

                    // 添加订单操作记录
                    if (!empty($value['order_id'])) {
                        $actionNote = '订单已完成，赠送会员' . $value['obtain_scores'] . $this->usersConfig['score_name'];
                        AddOrderAction($value['order_id'], 0, 0, 3, 1, 1, '消费赠送', $actionNote);
                    }
                }
            }

            // 批量修改订单状态
            $update = [
                'is_obtain_scores' => 1,
                'update_time' => getTime(),
            ];
            Db::name('shop_order')->where($where)->update($update);
        }
    }

    // 查询 分销商的分销订单执行分销订单结算分佣处理
    private function eyou_dealerOrderSettlementHandle()
    {
        // 如果安装了分销插件则执行
        if (is_dir('./weapp/DealerPlugin/')) {
            // 开启分销插件则执行
            $data = model('Weapp')->getWeappList('DealerPlugin');
            if (!empty($data['status']) && 1 == $data['status']) {
                // 调用分销逻辑层方法
                $dealerCommonLogic = new \weapp\DealerPlugin\logic\DealerCommonLogic;
                $orderData = $dealerCommonLogic->dealerOrderSettlementHandle($this->users_id, $this->usersConfig);
            }
        }
    }

    // 查询 (尚未累计用户消费额 && 不可维权) 订单，执行累计用户消费额
    private function eyou_handleUsersOrderTotalAmount()
    {
        // 查询订单
        $where = [
            'order_status' => 3,
            'allow_service' => 1,
            'is_total_amount' => 0,
            'order_amount' => ['>', 0],
            'users_id' => intval($this->users_id),
        ];
        $usersData = GetUsersLatestData($this->users_id);
        // 查询订单id数组用于添加订单操作记录
        $shopOrder = Db::name('shop_order')->field('order_id, users_id, order_amount as unified_amount, is_total_amount')->where($where)->select();
        if (!empty($shopOrder)) {
            // 处理会员订单累计总额，用于会员自动升级
            $usersLevelModel = model('UsersLevel');
            foreach ($shopOrder as $key => $value) {
                if (!empty($usersData)) $usersLevelModel->handleUsersOrderTotalAmount($usersData, $value);
            }
            // 批量修改订单状态
            $update = [
                'is_total_amount' => 1,
                'update_time' => getTime(),
            ];
            Db::name('shop_order')->where($where)->update($update);
        }
    }
}