<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */

namespace app\api\controller\v1;

use think\Db;
use think\Request;

class Users extends Base
{
    public $users;
    public $users_id;

    /**
     * 初始化操作
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->users    = $this->getUser();   // 用户信息
        $this->users_id = !empty($this->users['users_id']) ? intval($this->users['users_id']) : null;
        if (empty($this->users_id)) $this->error('请先登录');
        
        // 用户登录后检查拼团状态并处理退款
        $this->checkAndProcessUserExpiredGroups();
    }
    
    /**
     * 检查并处理用户相关的过期拼团退款
     */
    private function checkAndProcessUserExpiredGroups()
    {
        try {
            // 检查是否安装了拼团插件
            if (!is_dir(ROOT_PATH . 'weapp/Groupbuy/')) {
                return;
            }
            
            // 检查拼团插件是否启用
            $groupBuyPlugin = Db::name('weapp')->where([
                'code' => 'Groupbuy',
                'status' => 1
            ])->find();
            
            if (empty($groupBuyPlugin)) {
                return;
            }
            
            $currentTime = time();
            
            // 查找当前用户参与的所有过期拼团
            $userExpiredTeams = Db::name('weapp_groupbuy_team_member')->alias('m')
                ->join('__WEAPP_GROUPBUY_TEAM__ t', 'm.team_id = t.id')
                ->where([
                    'm.user_id' => $this->users_id,
                    't.status' => 0, // 拼团中
                    't.expire_time' => ['<', $currentTime] // 已过期
                ])
                ->field('t.*')
                ->select();
            
            if (empty($userExpiredTeams)) {
                return;
            }
            
            // 记录日志
            file_put_contents('./error_log.php', '用户 ' . $this->users_id . ' 登录检查：发现 ' . count($userExpiredTeams) . ' 个过期拼团需要处理退款' . PHP_EOL, FILE_APPEND);
            
            foreach ($userExpiredTeams as $team) {
                // 更新团单状态为失败
                Db::name('weapp_groupbuy_team')->where('id', $team['id'])->update([
                    'status' => 2, // 拼团失败
                    'fail_time' => $currentTime,
                    'update_time' => $currentTime
                ]);
                
                // 处理退款
                $this->processGroupFailureRefund($team['id']);
                
                // 记录日志
                file_put_contents('./error_log.php', '用户登录检查：拼团ID ' . $team['id'] . ' 已过期，已处理退款' . PHP_EOL, FILE_APPEND);
            }
            
        } catch (\Exception $e) {
            // 记录异常但不影响正常业务
            file_put_contents('./error_log.php', '用户登录检查拼团状态异常：' . $e->getMessage() . PHP_EOL, FILE_APPEND);
        }
    }
    
    /**
     * 处理拼团失败退款
     */
    private function processGroupFailureRefund($team_id)
    {
        try {
            $currentTime = time();
            
            // 获取团单下所有相关订单
            $orders = Db::name('shop_order')->where([
                'group_id' => $team_id
            ])->select();
            
            if (empty($orders)) {
                // 如果没有找到订单，尝试使用其他条件再次查询
                $orders = Db::name('shop_order')->where([
                    'group_id' => $team_id,
                    'prom_type' => 3 // 拼团订单类型
                ])->select();
                
                if (empty($orders)) {
                    file_put_contents('./error_log.php', '用户退款：拼团ID ' . $team_id . ' 未找到相关订单' . PHP_EOL, FILE_APPEND);
                    return false;
                }
            }
            
            foreach ($orders as $order) {
                // 只处理已支付的订单
                if ($order['order_status'] == 1) {
                    // 检查订单是否已经退款过
                    $orderStatus = Db::name('shop_order')->where('order_id', $order['order_id'])->value('order_status');
                    
                    if ($orderStatus == -1) {
                        // 订单已退款，跳过
                        continue;
                    }
                    
                    // 开启事务
                    Db::startTrans();
                    
                    try {
                        // 更新订单状态为已退款
                        $updateResult = Db::name('shop_order')->where('order_id', $order['order_id'])->update([
                            'order_status' => -1, // 已退款
                            'refund_time' => $currentTime,
                            'refund_way' => 1, // 退款到余额
                            'order_source' => 0, // 改为普通订单
                            'update_time' => $currentTime
                        ]);
                        
                        if (!$updateResult) {
                            throw new \Exception('更新订单状态失败');
                        }
                        
                        // 退款到用户余额
                        $refundAmount = floatval($order['order_amount']);
                        if ($refundAmount > 0) {
                            // 获取用户当前余额
                            $userInfo = Db::name('users')->where('users_id', $order['users_id'])->find();
                            if (!$userInfo) {
                                throw new \Exception('用户不存在');
                            }
                            
                            // 增加用户余额
                            $newBalance = floatval($userInfo['users_money']) + $refundAmount;
                            $updateUserResult = Db::name('users')->where('users_id', $order['users_id'])->update([
                                'users_money' => $newBalance,
                                'update_time' => $currentTime
                            ]);
                            
                            if (!$updateUserResult) {
                                throw new \Exception('更新用户余额失败');
                            }
                            
                            // 记录余额变动（如果函数存在的话）
                            if (function_exists('UsersMoneyRecording')) {
                                UsersMoneyRecording(
                                    $order['order_code'],
                                    $userInfo,
                                    $refundAmount,
                                    '拼团失败自动退款',
                                    2
                                );
                            }
                            
                            // 添加订单操作记录（如果函数存在的话）
                            if (function_exists('AddOrderAction')) {
                                AddOrderAction($order['order_id'], $order['users_id'], 0, -1, 0, 0, '拼团失败自动退款', '拼团超时失败，系统自动退款到余额');
                            }
                            
                            file_put_contents('./error_log.php', '用户退款：订单ID ' . $order['order_id'] . ' 退款成功，金额：' . $refundAmount . PHP_EOL, FILE_APPEND);
                        }
                        
                        // 恢复库存
                        $this->restoreGroupGoodsStock($order);
                        
                        // 更新团成员状态
                        Db::name('weapp_groupbuy_team_member')->where([
                            'order_id' => $order['order_id']
                        ])->update([
                            'status' => -1, // 已退款
                            'update_time' => $currentTime
                        ]);
                        
                        // 提交事务
                        Db::commit();
                        
                    } catch (\Exception $e) {
                        // 回滚事务
                        Db::rollback();
                        file_put_contents('./error_log.php', '用户退款失败：' . $e->getMessage() . '，订单ID：' . $order['order_id'] . PHP_EOL, FILE_APPEND);
                    }
                } elseif ($order['order_status'] == 0) {
                    // 未支付订单：直接取消并恢复库存
                    try {
                        // 开启事务
                        Db::startTrans();
                        
                        // 恢复库存
                        $this->restoreGroupGoodsStock($order);
                        
                        // 更新订单状态
                        Db::name('shop_order')->where('order_id', $order['order_id'])->update([
                            'order_status' => 5, // 已取消
                            'cancel_time' => $currentTime,
                            'order_source' => 0,
                            'update_time' => $currentTime
                        ]);
                        
                        // 更新团成员状态
                        Db::name('weapp_groupbuy_team_member')->where([
                            'order_id' => $order['order_id']
                        ])->update([
                            'status' => 3, // 拼团失败
                            'update_time' => $currentTime
                        ]);
                        
                        // 提交事务
                        Db::commit();
                        
                    } catch (\Exception $e) {
                                            // 回滚事务
                    Db::rollback();
                    file_put_contents('./error_log.php', '取消订单失败：' . $e->getMessage() . '，订单ID：' . $order['order_id'] . PHP_EOL, FILE_APPEND);
                    }
                }
            }
            
            // 更新所有团成员状态为失败
            Db::name('weapp_groupbuy_team_member')->where([
                'team_id' => $team_id,
                'status' => ['not in', [-1, 3]] // 不包括已退款和已取消的
            ])->update([
                'status' => 3, // 拼团失败
                'update_time' => $currentTime
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            file_put_contents('./error_log.php', '用户退款处理异常：' . $e->getMessage() . '，拼团ID：' . $team_id . PHP_EOL, FILE_APPEND);
            return false;
        }
    }
    
    /**
     * 恢复拼团商品库存
     */
    private function restoreGroupGoodsStock($order)
    {
        try {
            // 获取订单详情
            $orderDetail = Db::name('shop_order_details')->where('order_id', $order['order_id'])->find();
            if (empty($orderDetail)) {
                file_put_contents('./error_log.php', '恢复库存失败：订单详情不存在，订单ID：' . $order['order_id'] . PHP_EOL, FILE_APPEND);
                return;
            }
            
            // 获取拼团商品信息
            $groupGoods = Db::name('weapp_groupbuy_goods')->where('aid', $orderDetail['aid'])->find();
            if (empty($groupGoods)) {
                file_put_contents('./error_log.php', '恢复库存失败：拼团商品不存在，订单ID：' . $order['order_id'] . PHP_EOL, FILE_APPEND);
                return;
            }
            
            // 构造库存恢复数据
            $stockData = [
                'aid' => $orderDetail['aid'],
                'goods_id' => $groupGoods['goods_id'],
                'goodsBuyNumber' => $orderDetail['num'],
                'goodsSpecKeys' => '' // 默认单规格
            ];
            
            // 添加调试日志
            file_put_contents('./error_log.php', '用户退款准备恢复库存，数据：' . json_encode($stockData) . PHP_EOL, FILE_APPEND);
            
            // 直接更新库存，不使用模型方法
            $this->directUpdateStock($stockData);
            
            file_put_contents('./error_log.php', '拼团订单退款，已恢复库存，订单ID：' . $order['order_id'] . '，商品ID：' . $groupGoods['goods_id'] . '，数量：' . $orderDetail['num'] . PHP_EOL, FILE_APPEND);
            
        } catch (\Exception $e) {
            file_put_contents('./error_log.php', '恢复拼团商品库存失败：' . $e->getMessage() . '，订单ID：' . $order['order_id'] . PHP_EOL, FILE_APPEND);
        }
    }
    
    /**
     * 直接更新库存，绕过模型方法
     */
    private function directUpdateStock($stockData)
    {
        try {
            $aid = intval($stockData['aid']);
            $goods_id = intval($stockData['goods_id']);
            $goodsBuyNumber = intval($stockData['goodsBuyNumber']);
            
            // 1. 更新系统商品库存（archives表）
            $updateResult1 = Db::name('archives')->where('aid', $aid)->update([
                'stock_count' => Db::raw('stock_count+' . $goodsBuyNumber),
                'sales_num' => Db::raw('sales_num-' . $goodsBuyNumber),
                'update_time' => time()
            ]);
            
            file_put_contents('./error_log.php', '系统商品库存更新结果：' . ($updateResult1 ? '成功' : '失败') . '，AID：' . $aid . PHP_EOL, FILE_APPEND);
            
            // 2. 更新拼团商品库存（weapp_groupbuy_goods表）
            $updateResult2 = Db::name('weapp_groupbuy_goods')->where('goods_id', $goods_id)->update([
                'goods_stock' => Db::raw('goods_stock+' . $goodsBuyNumber),
                'update_time' => time()
            ]);
            
            file_put_contents('./error_log.php', '拼团商品库存更新结果：' . ($updateResult2 ? '成功' : '失败') . '，商品ID：' . $goods_id . PHP_EOL, FILE_APPEND);
            
            // 3. 验证库存是否真的增加了
            $newStock = Db::name('weapp_groupbuy_goods')->where('goods_id', $goods_id)->value('goods_stock');
            file_put_contents('./error_log.php', '拼团商品更新后库存：' . $newStock . PHP_EOL, FILE_APPEND);
            
        } catch (\Exception $e) {
            file_put_contents('./error_log.php', '直接更新库存失败：' . $e->getMessage() . PHP_EOL, FILE_APPEND);
        }
    }
    
    /**
     * 写入调试日志到文件
     */


    /**
     * 我的订单列表
     * @param $dataType
     * @return array
     * @throws \think\exception\DbException
     */
    public function order_lists($dataType)
    {
        $list = model('v1.Shop')->getOrderList($this->users_id, $dataType);
        return $this->renderSuccess(compact('list'));
    }

    //获取留言列表
    public function guestbook_list()
    {
        if (IS_AJAX) {
            $list = model('v1.User')->guestbookList();
            return $this->renderSuccess(compact('list'));
        }
        $this->error('请求错误！');


    }

    //获取我的预约详情
    public function get_book_detail()
    {
        if (IS_AJAX) {
            $param = input('param.');
            $list = model('v1.User')->GetMyBookDetail($param);

            return $this->renderSuccess(compact('list'));
        }
        $this->error('请求错误！');
    }

    //取消预约
    public function cancel_book()
    {
        if (IS_AJAX) {
            $aid = input('param.aid/d',0);
            if (empty($aid)){
                $this->error('缺少参数aid！');
            }
            $r = Db::name('guestbook')
                ->where(['aid'=>$aid,'users_id'=>$this->users_id])
                ->delete();
            if (false !== $r){
                Db::name('guestbook_attr')->where('aid',$aid)->delete();
                $this->success('取消成功！');
            }else{
                $this->error('取消失败！');
            }
        }
        $this->error('请求错误！');
    }


    /**
     * 取消订单
     * @param $order_id
     * @return array
     * @throws \Exception
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function order_cancel($order_id)
    {
        if (IS_AJAX_POST && !empty($order_id)) {
            model('v1.Shop')->orderCancel($order_id, $this->users_id);
        }
        $this->error('订单取消失败！');
    }

    /**
     * 订单提醒发货
     * @param $order_id
     * @return array
     * @throws \Exception
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function order_remind($order_id)
    {
        if (IS_AJAX_POST && !empty($order_id)) {
            model('v1.Shop')->orderRemind($order_id, $this->users_id);
        }
        $this->error('订单取消失败！');
    }

    /**
     * 订单详情信息
     * @param $order_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function order_detail($order_id)
    {
        if (IS_AJAX) {
            // 检查 Querykd 插件是否安装
            $is_querykd = 0;
            if (is_dir('./weapp/Querykd/')) {
                $count = Db::name('weapp')->where(['code'=>'Querykd', 'status'=>1])->count();
                $is_querykd = empty($count) ? $is_querykd : 1;
            }
            
            // 订单详情
            $detail = model('v1.Shop')->getOrderDetail($order_id, $this->users_id);
            $assign_data = [
                'order'   => $detail,  // 订单详情
                'is_querykd' => $is_querykd,
                'setting' => [],
            ];
            
            // 微信物流助手插件
            if (is_dir('./weapp/Logisticswx/')) {
                if (file_exists('./weapp/Logisticswx/model/LogisticswxModel.php')) {
                    $logisticswxModel = new \weapp\Logisticswx\model\LogisticswxModel;
                    $logisticswxModel->api_order_detail($assign_data); 
                } else {
                    $is_Logisticswx = 0;
                    // 获取该记录的 data 字段内容
                    $data = Db::name('weapp')->where(['code' => 'Logisticswx', 'status' => 1])->value('data');
                    // 如果 data 字段存在且不为空
                    if (!empty($data)) {
                        $dataArray = safe_unserialize($data);
                        if (!empty($dataArray['logisticswx_open'])) {
                            $is_Logisticswx = 1;
                        }
                    }
                    $assign_data['is_Logisticswx'] = $is_Logisticswx;
                }
            }

            return $this->renderSuccess($assign_data);
        }
        $this->error('订单读取失败！');
    }

    /**
     * 订单支付
     * @param int $order_id 订单id
     * @param int $payType 支付方式
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function order_pay($order_id, $payType = 20)
    {
        // 订单支付事件
        $order =  model('v1.Shop')->getOrderDetail($order_id, $this->users_id);
        // 判断订单状态
        if (!isset($order['order_status']) || $order['order_status'] != 0) {
            $this->error('很抱歉，当前订单不合法，无法支付');
        }
        // 构建微信支付请求
        $payment = model('v.Shop')->onOrderPayment($this->users, $order, $payType);
        if (isset($payment['code']) && empty($payment['code'])) {
            $this->error($payment['msg'] ?: '订单支付失败');
        }
        // 支付状态提醒
        $this->renderSuccess([
            'order_id' => $order['order_id'],   // 订单id
            'pay_type' => $payType,             // 支付方式
            'payment'  => $payment               // 微信支付参数
        ], ['success' => '支付成功', 'error' => '订单未支付']);
    }

    /**
     * 获取物流信息
     * @param $order_id
     * @return array
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function order_express($order_id, $timestamp = '')
    {
        // 订单详情
        $detail = model('v1.Shop')->getOrderDetail($order_id, $this->users_id);
        if (empty($detail['express_order'])) {
            return $this->error('没有物流信息');
        }
        // 获取物流信息
        /* @var \app\store\model\Express $model */
        $express = model('v1.Shop')->orderExpress($detail['express_name'], $detail['express_code'], $detail['express_order'], $timestamp);
        if (!empty($express)) {
            return $this->renderSuccess(compact('express'));
        }
        $this->error('没有找到物流信息！');
    }

    /**
     * 确认收货
     * @param $order_id
     * @return array
     * @throws \Exception
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function order_receipt($order_id)
    {
        if (IS_AJAX_POST && !empty($order_id)) {
            model('v1.Shop')->orderReceipt($order_id, $this->users_id);
        }
        $this->error('确认收货失败！');
    }


    /* -------------陈风任------------- */
    /**
     * 添加商品到购物车
     */
    public function shop_add_cart()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 数量判断
            if (empty($post['product_num']) || 0 > $post['product_num']) $this->error('请输入数量');

            // 默认会员ID
            $post['users_id'] = $this->users_id;

            // 商城模型
            $ShopModel = model('v1.Shop');

            // 商品是否已售罄
            $ShopModel->IsSoldOut($post);

            // 添加购物车
            $ShopModel->ShopAddCart($post);
        }
    }

    /**
     * 添加商品到购物车
     */
    public function shop_page_add_cart()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 数量判断
            if (empty($post['product_num']) || 0 > $post['product_num']) $this->error('请输入数量');
            // 默认会员ID
            $post['users_id'] = $this->users_id;

            // 商城模型
            $ShopModel = model('v1.Shop');

            // 添加购物车
            $ShopModel->ShopPageAddCart($post);
        }
    }

    /**
     * 立即购买
     */
    public function shop_buy_now()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 数量判断
            if (empty($post['product_num']) || 0 > $post['product_num']) $this->error('请输入数量');

            // 默认会员ID
            $post['users_id'] = $this->users_id;

            // 商城模型
            $ShopModel = model('v1.Shop');

            // 商品是否已售罄
            $ShopModel->IsSoldOut($post);

            // 立即购买
            $ShopModel->ShopBuyNow($post);
        }
    }

    /**
     * 产品购买
     */
    public function shop_product_buy()
    {
        if (IS_AJAX_POST) {
            // 获取解析数据
            $querystr = input('param.querystr/s');
            // if (empty($querystr)) $this->error('无效链接！');

            // 商城模型
            $ShopModel = model('v1.Shop');

            // 获取商品信息进行展示
            $post = input('post.');
            $ShopModel->GetProductData($querystr, $this->users_id, $this->users['level_discount'], $this->users, $post);
        }
    }

    /**
     * 订单结算
     */
    public function shop_order_pay()
    {
        if (IS_AJAX_POST) {
            $post           = input('post.');
            $post['action'] = !empty($post['action']) ? $post['action'] : 'CreatePay';

            // 默认会员ID
            $post['users_id'] = $this->users_id;
            $post['openid']   = Db::name('wx_users')->where('users_id', $this->users_id)->getField('openid');

            // 商城模型
            $ShopModel = model('v1.Shop');

            // 操作分发
            if ('DirectPay' == $post['action']) {
                // 订单直接支付
                $ShopModel->OrderDirectPay($post);
            } else if ('CreatePay' == $post['action']) {
                // 获取解析数据
                // if (empty($post['querystr'])) $this->error('无效链接！');

                // 获取商品信息生成订单并支付
                $ShopModel->ShopOrderPay($post, $this->users['level_discount'], $this->users['level_id'], $this->users);
            }
        }
    }

    /**
     * 商城订单支付后续处理
     */
    public function shop_order_pay_deal_with()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            
            // 处理订单类型参数
            if (!empty($post['transaction_type'])) {
                // 前端传递了transaction_type，直接使用
                $post['transaction_type'] = intval($post['transaction_type']);
            } else {
                // 前端没有传递，默认为普通订单，并自动检测多商家订单
                $post['transaction_type'] = 2;
                
                // 检查是否为多商家订单
                if (!empty($post['order_id'])) {
                    $unifiedPay = Db::name('shop_order_unified_pay')->where([
                        'users_id' => $this->users_id,
                        'pay_status' => 0
                    ])->select();
                    
                    foreach ($unifiedPay as $unifiedPayItem) {
                        $orderIds = !empty($unifiedPayItem['order_ids']) ? safe_unserialize($unifiedPayItem['order_ids']) : [];
                        if (in_array($post['order_id'], $orderIds)) {
                            $post['transaction_type'] = 99; // 多商家订单
                            break;
                        }
                    }
                }
            }

            // 默认会员ID
            $post['users_id'] = $this->users_id;
            $post['openid']   = Db::name('wx_users')->where('users_id', $this->users_id)->getField('openid');
            // 抖音支付
            if (9 === intval($post['curPayType'])) {
                model('TikTok')->tikTokAppletsPayDealWith($post);
            } 
            // 百度支付
            else if (10 === intval($post['curPayType'])) {
                model('BaiduPay')->baiDuAppletsPayDealWith($post);
            }
             // 支付宝支付
            else if (12 === intval($post['curPayType'])) {
                model('AliPay')->aliPayAppletsPayDealWith($post);
            }
            // 微信支付
            else {
                model('v1.Shop')->WechatAppletsPayDealWith($post);
            }
        }
    }

    /**
     * 购物车操作(改变数量、选中状态、删除)
     */
    public function shop_cart_action()
    {
        if (IS_AJAX) {
            $param = input('param.');
            if (empty($param['action'])) $param['action'] = null;
            $param['users_id'] = $this->users_id;

            // 商城模型
            $ShopModel = model('v1.Shop');

            /* 购物车操作 */
            if ('add' == $param['action']) {
                // 数量 + 1
                $ShopModel->ShopCartNumAdd($param);
            } else if ('less' == $param['action']) {
                // 数量 - 1
                $ShopModel->ShopCartNumLess($param);
            } else if ('selected' == $param['action']) {
                // 是否选中
                $ShopModel->ShopCartSelected($param);
            } else if ('all_selected' == $param['action']) {
                // 是否全部选中
                $ShopModel->ShopCartAllSelected($param);
            } else if ('del' == $param['action']) {
                // 删除购物车商品
                $ShopModel->ShopCartDelete($param);
            } else {
                $this->error('请正确操作');
            }
            /* END */

        }
    }

    /**
     * 收货地址列表
     */
    public function shop_address_list()
    {
        if (IS_AJAX) {
            // 商城模型
            $ShopModel = model('v1.Shop');

            // 收货地址列表
            $ReturnData = $ShopModel->GetAllAddressList($this->users);
        }
    }

    /**
     * 收货地址操作分发
     */
    public function shop_address_action()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 商城模型
            $ShopModel = model('v1.Shop');

            // 操作分发
            if ('find_add' == $post['action']) {
                // 添加单条收货地址
                $ShopModel->FindAddAddr($post, $this->users_id);
            } else if ('find_edit' == $post['action']) {
                // 设置默认收货地址
                $ShopModel->FindEditAddr($post, $this->users_id);
            } else if ('default' == $post['action']) {
                // 设置默认收货地址
                $ShopModel->SetDefaultAddr($post, $this->users_id);
            } else if ('find_detail' == $post['action']) {
                // 获取单条收货地址
                $ShopModel->GetFindAddrDetail($post, $this->users);
            } else if ('find_del' == $post['action']) {
                // 删除单条收货地址
                $ShopModel->FindDelAddr($post, $this->users_id);
            } else {
                $this->error('请正确操作');
            }
        }
    }
    /* -------------END------------- */

    /**
     * 获取评价订单商品列表
     * @param $order_id
     * @return array
     * @throws \Exception
     * @throws \app\common\exception\BaseException
     * @throws \think\exception\DbException
     */
    public function order_comment($order_id)
    {
        if (IS_AJAX) {
            $data = model('v1.Shop')->getOrderComment($order_id, $this->users_id);
            return $this->renderSuccess([
                'goods'   => $data,  // 订单详情
            ]);
        }
        $this->error('读取失败！');
    }

    /**
     * 保存评价
     * @return array
     */
    public function save_comment()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            return model('v1.Shop')->getSaveComment($post,$this->users_id);
        }
        $this->error('评价失败！');
    }

    /**
     * 用户领取优惠券
     */
    public function get_coupon($coupon_id)
    {
        if (IS_AJAX) {
            $coupon_table = 'shop_coupon';
            $coupon_use_table = 'shop_coupon_use';
            $weappInfo = model('ShopPublicHandle')->getWeappInfo("Coupons");
            if (!empty($weappInfo['status']) && 1 == $weappInfo['status']) {
                $coupon_use_table = 'weapp_coupons_use';
                $coupon_table = 'weapp_coupons';
            }

            $where = [
                'coupon_id' => $coupon_id,
            ];
            //查询优惠券信息,结束发放时间/库存
            $coupon = Db::name($coupon_table)->where($where)->find();
            if (!empty($coupon)) {
                if (1 > $coupon['coupon_stock']) {
                    $this->error('优惠券库存不足！');
                }
                if (getTime() > $coupon['end_date']) {
                    $this->error('优惠券发放已结束！');
                }
                $where['users_id']   = $this->users_id;
                $where['use_status'] = 0;
                $where['start_time'] = ['<=',getTime()];
                $where['end_time'] = ['>=',getTime()];

                $count = Db::name($coupon_use_table)->where($where)->find();

                if (!empty($count)) {
                    $this->error('请勿重复领取！');
                } else {
                    //如果是插件 需要判断每人领取张数/会员限制
                    if ('weapp_coupons' == $coupon_table){
                        if (!empty($coupon['use_limit_num'])){
                            $limit_count = Db::name($coupon_use_table)->where('users_id',$this->users_id)->where('coupon_id',$coupon_id)->find();
                            if ($limit_count >= $coupon['use_limit_num']) $this->error('已达最大领取数量，不可领取！');
                        }
                        if (!empty($coupon['redeem_authority'])){
                            $coupon['redeem_authority'] = explode(',', $coupon['redeem_authority']);
                            $level = Db::name('users')->where('users_id',$this->users_id)->value('level');
                            if (!in_array($level,$coupon['redeem_authority'])){
                                $this->error('指定会员级别可领取，您不可领取！');
                            }
                        }
                    }
                    $insert['coupon_id']   = $coupon_id;
                    $insert['coupon_code'] = $coupon['coupon_code'];
                    $insert['users_id']    = $this->users_id;
                    $insert['use_status']  = 0;
                    $insert['get_time']    = getTime();
                    $insert['add_time']    = getTime();
                    $insert['update_time'] = getTime();
                    //根据使用期限不同插入开始/结束使用时间
                    if (1 == $coupon['use_type']) {//固定期限
                        $insert['start_time'] = $coupon['use_start_time'];
                        $insert['end_time']   = $coupon['use_end_time'];
                    } else if (2 == $coupon['use_type']) {//当日开始N天有效
                        $insert['start_time'] = strtotime(date("Y-m-d", time()));
                        $insert['end_time']   = $insert['start_time'] + $coupon['valid_days'] * 86400;
                    } else if (3 == $coupon['use_type']) {//次日开始N天有效
                        $insert['start_time'] = strtotime(date("Y-m-d", time())) + 86400;
                        $insert['end_time']   = $insert['start_time'] + $coupon['valid_days'] * 86400;
                    }
                    if (!empty($insert)) {
                        $use_insert = Db::name($coupon_use_table)->insert($insert);
                        if (!empty($use_insert)) {
                            //减库存
                            Db::name($coupon_table)->where('coupon_id', $coupon_id)->setDec('coupon_stock');
                            $this->success('领取成功！');
                        }
                    }
                }
            }
        }
        $this->error('优惠券领取失败！');
    }

    /**
     * 获取我的优惠券
     */
    public function get_my_coupon($dataType)
    {
        $list = model('v1.Shop')->GetMyCouponList($this->users_id, $dataType);

        return $this->renderSuccess(compact('list'));
    }
    /**
     * 领券中心
     */
    public function get_coupon_center()
    {
        $list = model('v1.Shop')->GetCouponCenter($this->users_id);

        return $this->renderSuccess(compact('list'));
    }

    /**
     * 收藏(喜欢)/取消
     * 收藏 type users_collection
     * 喜欢 type users_like
     */
    public function get_collect()
    {
        if (IS_AJAX_POST) {
            $aid = input('param.aid/d');
            $type = input('param.type/s','users_collection');
            $success = '已收藏';
            $cancel = '已取消';
            if ('users_collection' != $type){
                $success = 'success';
            }else{
                $cancel = 'cancel';
            }
            if(empty($aid)){
                $this->error('缺少文档ID！');
            }
            $count = Db::name($type)->where([
                'aid'   => $aid,
                'users_id'  => $this->users_id,
            ])->count();
            if (empty($count)) {
                $addSave = Db::name('archives')->field('aid,channel,typeid,lang,title,litpic')->where('aid',$aid)->find();
                if(empty($addSave)){
                    $this->error('文档不存在！');
                }
                $addSave['lang']        = $this->home_lang;
                $addSave['add_time']  = getTime();
                $addSave['users_id']  = $this->users_id;
                $r = Db::name($type)->insert($addSave);
                if ($r !== false){
                    Db::name('archives')->where('aid', $aid)->setInc('collection');
                    if ('users_collection' == $type){
                        $ret_data = ['is_collect'=>1];
                    }else if ('users_like' == $type){
                        $ret_data = ['is_like'=>1];
                    }
                    $this->success($success, null, $ret_data);
                }
            }else{
                $r = Db::name($type)->where(['aid'=>$aid,'users_id'=>$this->users_id])->delete();
                if ($r !== false){
                    Db::name('archives')->where('aid', $aid)->setDec('collection');
                    if ('users_collection' == $type){
                        $ret_data = ['is_collect'=>0];
                    }else if ('users_like' == $type){
                        $ret_data = ['is_like'=>0];
                    }
                    $this->success($cancel, null,$ret_data);
                }
            }
        }
        $this->error('请求错误！');
    }

    //获取收藏列表
    public function get_collect_list()
    {
        if (IS_AJAX) {
            $param = input('param.');
            $list = model('v1.User')->GetMyCollectList($param);

            return $this->renderSuccess(compact('list'));
        }
        $this->error('请求错误！');
    }

    //修改头像/昵称/手机号
    public function save_user_info()
    {
        if (IS_AJAX_POST) {
            $head_pic = input('param.head_pic/s');
            $nickname = input('param.nickname/s');
            $nickname = !empty($nickname) ? filterNickname($nickname) : '';
            $mobile = input('param.mobile/s');
            if(!empty($head_pic) || !empty($nickname) || !empty($mobile)){
                $update = ['update_time'=>getTime()];
                if (!empty($head_pic)){
                    $update['head_pic'] = $head_pic;
                }
                if (!empty($nickname)){
                    $update['nickname'] = $nickname;
                }
                if (!empty($mobile)){
                    $is_mobile = check_mobile($mobile);
                    if (!$is_mobile){
                        $this->error('手机号格式不正确!');
                    }
                    $update['mobile'] = $mobile;
                    $update['is_mobile'] = 1;
                }
                $r = Db::name('users')->where(['users_id'=>$this->users_id])->update($update);
                if (!empty($r)){
                    if(!empty($update['mobile'])){
                        $para_mobile = Db::name('users_list')->where(['users_id'=>$this->users_id,'para_id'=>1])->find();
                        if (!empty($para_mobile)){
                            Db::name('users_list')->where(['users_id'=>$this->users_id,'para_id'=>1])->update(['info'=>$mobile,'update_time'=>getTime()]);
                        }else{
                            $insert = ['users_id'=>$this->users_id,'para_id'=>1,'info'=>$mobile,'update_time'=>getTime(),'add_time'=>getTime()];
                            Db::name('users_list')->insert($insert);
                        }
                    }

                    $this->success('保存成功');
                }
            }
            $this->error('保存失败');
        }
    }

    // 会员充值套餐操作处理
    public function usersRechargePackAction()
    {
        if (IS_AJAX_POST) {
            // 处理传入的参数
            $param = input('param.');
            $param['terminal'] = 'applets';
            $param['action'] = !empty($param['action']) ? strval($param['action']) : '';

            // 会员充值套餐全局模型
            $packModel = new \app\common\model\UsersRechargePack($param, $this->users);

            // 会员余额中心
            if ('moneyCenter' == $param['action']) {
                $packModel->usersMoneyCenter();
            }
            // 会员充值套餐页面
            else if ('recharge' == $param['action']) {
                $packModel->usersRechargePackPage();
            }
            // 会员余额充值记录
            else if ('rechargeLog' == $param['action']) {
                $packModel->usersMoneyRechargeLog();
            }
            // 会员充值套餐充值记录
            else if ('orderList' == $param['action']) {
                $packModel->usersRechargePackOrder();
            }
            // 会员充值套餐订单创建
            else if ('orderCreate' == $param['action']) {
                // 会员充值套餐下单
                if (!empty($param['pack_id'])) {
                    $packModel->usersRechargePackOrderCreate();
                }
                // 会员充值余额下单
                else if (!empty($param['users_money'])) {
                    $packModel->usersRechargeMoneyOrderCreate();
                }
                // 错误提示
                else {
                    $this->error('请选择充值套餐或输入充值金额');
                }
            }
            // 会员充值套餐订单支付后续处理
            else if ('orderPayHandle' == $param['action']) {
                $packModel->usersRechargePackOrderPayHandle();
            }
        }
    }

    // 执行会员每日签到送积分
    public function usersCheckIns()
    {
        // 执行会员每日签到送积分
        $result = model('ShopPublicHandle')->executeUsersDailyCheckIns($this->users_id);
        if (isset($result['code']) && 1 === intval($result['code'])) {
            $this->success($result['msg'], null, ['scores' => $result['scores'], 'checkInsPoints' => $result['checkInsPoints']]);
        } else {
            $this->error($result['msg']);
        }
    }

    public function usersPointsList()
    {
        $param = input('param.');
        // 执行会员每日签到送积分
        $result = model('ShopPublicHandle')->getBaseUsersPointsDetails($this->users_id, $param);
        // 会员信息
        $result['users'] = $this->users;
        $this->success('查询成功', null, $result);
    }

    //获取手机号
    public function get_phone()
    {
        if (IS_AJAX_POST) {
            $post = input('param.');
            $provider = empty($post['provider']) ? 'weixin' : $post['provider'];
            $res = false;
            if ($provider == 'baidu') {
                // 百度获取手机号
                $res = model('v1.User')->getBdPhone($post);
            } elseif ($provider == 'toutiao') {

            } else {
                // 微信获取手机号
                $res = model('v1.User')->getPhone($post['code']);
            }
            if (!empty($res)){
                $this->success('获取成功','',$res);
            }
        }
        $this->error('请求失败');
    }

    // 处理会员余额方法(集合方法)
    public function HandleUserMoneyAction()
    {
        if (IS_AJAX) {
            // 传入参数
            $param = input('param.');

            // 处理传入的参数
            $param['users_id'] = intval($this->users_id);
            $param['moneyid'] = !empty($param['moneyid']) ? intval($param['moneyid']) : 0;
            $param['usersMoney'] = !empty($param['usersMoney']) ? strval($param['usersMoney']) : 0;
            $param['order_number'] = !empty($param['order_number']) ? strval($param['order_number']) : '';

            $v1ShopModel = model('v1.Shop');
            // 判断执行操作
            $action = !empty($param['action']) ? strval($param['action']) : '';
            if ('details' == $action) {
                $v1ShopModel->getUsersMoneyDetails($this->users, $param);
            } else if ('recharge' == $action) {
                // 会员余额充值
                $v1ShopModel->getUsersMoneyRecharge($param);
            } else if ('rechargePay' == $action) {
                // 会员余额充值后续处理
                $v1ShopModel->getUsersMoneyRechargePay($param);
            }
        }
    }

    // 处理订单服务方法(集合方法)
    public function handleOrderServiceAction()
    {
        if (IS_AJAX) {
            // 传入参数
            $param = input('param.');

            // 处理传入的参数
            $param['users_id'] = intval($this->users_id);
            $param['order_id'] = !empty($param['order_id']) ? intval($param['order_id']) : 0;
            $param['product_id'] = !empty($param['product_id']) ? intval($param['product_id']) : 0;
            $param['details_id'] = !empty($param['details_id']) ? intval($param['details_id']) : 0;
            $param['service_id'] = !empty($param['service_id']) ? intval($param['service_id']) : 0;

            $v1ShopModel = model('v1.Shop');
            // 判断执行操作
            $action = !empty($param['action']) ? strval($param['action']) : '';
            if ('goodsDetect' == $action) {
                // 查询订单内的指定的某个商品，判断是否已申请售后或已评价商品
                $v1ShopModel->getOrderGoodsDetect($param);
            } else if ('getServiceList' == $action) {
                // 查询售后服务单列表
                $v1ShopModel->getOrderGoodsServiceList($param);
            } else if ('getService' == $action) {
                // 查询申请售后的订单商品
                $v1ShopModel->getOrderGoodsService($param);
            } else if ('addService' == $action) {
                $v1ShopModel->addOrderGoodsService($param);
            } else if ('cancelService' == $action) {
                // 取消售后订单会员邮寄物流信息
                $v1ShopModel->cancelOrderGoodsService($param);
            }  else if ('addUsersDelivery' == $action) {
                // 添加售后订单会员邮寄物流信息
                $v1ShopModel->addServiceUsersDelivery($param);
            }
        }
    }

    // 分销商操作(集合方法)
    public function dealer_action()
    {
        if (IS_AJAX) {
            // 如果安装了分销插件则执行
            if (is_dir('./weapp/DealerPlugin/')) {
                // 开启分销插件则执行
                $data = model('Weapp')->getWeappList('DealerPlugin');
                if (!empty($data['status']) && 1 == $data['status']) {
                    // 调用分销逻辑层方法
                    $dealerPluginLogic = new \app\plugins\logic\DealerPluginLogic($this->users);
                    $dealerPluginLogic->dealerAction('', '', 'openSource');
                } else {
                    $this->error('请先启用分销插件！');
                }
            } else {
                $this->error('请先安装分销插件！');
            }
        }
        $this->error('请求错误！');
    }

    //获取我的足迹列表
    public function get_footprint_list()
    {
        if (IS_AJAX) {
            $param = input('param.');
            $list = model('v1.User')->GetMyFootprintList($param);

            return $this->renderSuccess(compact('list'));
        }
        $this->error('请求错误！');
    }

    /**
     * 添加转发记录
     */
    public function set_forward()
    {
        if (IS_AJAX) {
            $users_id = $this->users_id;
            $aid = input('param.aid/d');
            if (empty($aid)){
                $this->error('缺少文档ID!');
            }
            //查询标题模型缩略图信息
            $arc = Db::name('archives')
                ->field('aid,channel,typeid,title,litpic')
                ->find($aid);
            if (!empty($arc)) {
                $arc['users_id']    = $users_id;
                $arc['lang']        = $this->home_lang;
                $arc['add_time']    = getTime();
                $arc['update_time'] = getTime();
                Db::name('users_forward')->insert($arc);
                $this->success('保存成功');
            }
        }
        $this->error('请求错误！');
    }
    /**
     * 留言栏目
     */
    public function guestbook($typeid = '')
    {
        $param = input('param.');
        if (IS_POST && !isset($param['apiGuestbookform'])) {
            $post = input('post.');
            $typeid = !empty($post['typeid']) ? intval($post['typeid']) : $typeid;
            if (empty($typeid)) {
                $this->error('post接口缺少typeid的参数与值！');
            }

            /*留言间隔限制*/
            $channel_guestbook_interval = tpSetting('channel_guestbook.channel_guestbook_interval');
            $channel_guestbook_interval = is_numeric($channel_guestbook_interval) ? intval($channel_guestbook_interval) : 60;
            if (0 < $channel_guestbook_interval) {
                $map = array(
                    'ip'    => clientIP(),
                    'typeid'    => $typeid,
                    'add_time'  => array('gt', getTime() - $channel_guestbook_interval),
                );
                $count = Db::name('guestbook')->where($map)->count('aid');
                if (!empty($count)) {
                    $this->error("同一个IP在{$channel_guestbook_interval}秒之内不能重复提交！");
                }
            }
            /*end*/

            // 提取表单令牌的token变量名
            $token = '__token__';
            foreach ($post as $key => $val) {
                if (preg_match('/^__token__/i', $key)) {
                    $token = $key;
                    continue;
                }
            }

            //判断必填项
            foreach ($post as $key => $value) {
                if (stripos($key, "attr_") !== false) {
                    //处理得到自定义属性id
                    $attr_id = substr($key, 5);
                    $attr_id = intval($attr_id);
                    $ga_data = Db::name('guestbook_attribute')->where([
                        'attr_id'   => $attr_id,
                    ])->find();
                    if ($ga_data['required'] == 1 && empty($value)) {
                        $this->error($ga_data['attr_name'] . '不能为空！');
                    }

                    if ($ga_data['validate_type'] == 6 && !empty($value)) {
                        $form_field_grep_list = config('global.form_field_grep_list');
                        $pattern  = $form_field_grep_list['mobile']['value'];
                        if (!preg_match($pattern, $value)) {
                            $this->error($ga_data['attr_name'] . '格式不正确！');
                        }
                    } elseif ($ga_data['validate_type'] == 7 && !empty($value)) {
                        $pattern  = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
                        if (preg_match($pattern, $value) == false) {
                            $this->error($ga_data['attr_name'] . '格式不正确！');
                        }
                    }
                }
            }

            $newData = array(
                'typeid'    => $typeid,
                'users_id'  => $this->users_id,
                'channel'   => 8,
                'ip'    => clientIP(),
                'lang'  => get_main_lang(),
                'add_time'  => getTime(),
                'update_time' => getTime(),
            );
            $data    = array_merge($post, $newData);

            /*表单令牌*/
            $token_value = !empty($data[$token]) ? $data[$token] : '';
            $session_path = \think\Config::get('session.path');
            $session_file = ROOT_PATH . $session_path . "/sess_".str_replace('__token__', '', $token);
            $filesize = @filesize($session_file);
            if(file_exists($session_file) && !empty($filesize)) {
                $fp = fopen($session_file, 'r');
                $token_v = fread($fp, $filesize);
                fclose($fp);
                if ($token_v != $token_value) {
                    $this->error('表单令牌无效！');
                }
            } else {
                $this->error('表单令牌无效！');
            }
            /*end*/

            $guestbookRow = [];
            /*处理是否重复表单数据的提交*/
            $formdata = $data;
            foreach ($formdata as $key => $val) {
                if (in_array($key, ['typeid', 'lang']) || preg_match('/^attr_(\d+)$/i', $key)) {
                    continue;
                }
                unset($formdata[$key]);
            }
            $md5data         = md5(serialize($formdata));
            $data['md5data'] = $md5data;
            $guestbookRow    = M('guestbook')->field('aid')->where(['md5data' => $md5data])->find();
            /*--end*/

            $aid = !empty($guestbookRow['aid']) ? $guestbookRow['aid'] : 0;
            if (empty($guestbookRow)) { // 非重复表单的才能写入数据库
                $aid = M('guestbook')->insertGetId($data);
                if ($aid > 0) {
                    $res = model('v1.Api')->saveGuestbookAttr($post, $aid, $typeid);
                    if ($res){
                        $this->error($res);
                    }
                }
            } else {
                // 存在重复数据的表单，将在后台显示在最前面
                Db::name('guestbook')->where('aid', $aid)->update([
                    'add_time' => getTime(),
                    'update_time' => getTime(),
                ]);
            }
            @unlink($session_file);
            $this->renderSuccess(['aid'=>$aid], '提交成功');
        }
        $this->error('请求错误！');
    }
    //点赞
    public function ask_like(){
        model('v1.Ask')->askLike($this->users);
    }
    //提交问题
    public function add_ask(){
        model('v1.Ask')->addAsk($this->users);
    }
    //提交回答
    public function add_answer(){
        model('v1.Ask')->addAnswer($this->users);
    }

    //视频订单列表
    public function media_order_list()
    {
        if (IS_AJAX) {
            $list = model('v1.Order')->mediaOrderList();
            return $this->renderSuccess(compact('list'));
        }
        $this->error('请求错误！');
    }

    //播放记录列表
    public function media_play_list()
    {
        if (IS_AJAX) {
            $list = model('v1.Order')->playList();
            return $this->renderSuccess(compact('list'));
        }
        $this->error('请求错误！');
    }

    //视频订单详情
    public function media_order_detial()
    {
        if (IS_AJAX) {
            $list = model('v1.Order')->mediaOrderDetails();
            return $this->renderSuccess(compact('list'));
        }
        $this->error('请求错误！');
    }

    // 处理会员升级方法(集合方法)
    public function HandleUserLevelAction()
    {
        if (IS_AJAX) {
            // 传入参数
            $param = input('param.');
            // 处理传入参数
            $param['users_id'] = intval($this->users_id);
            $param['action'] = !empty($param['action']) ? strval($param['action']) : '';
            $param['type_id'] = !empty($param['type_id']) ? intval($param['type_id']) : 0;

            $v1ShopModel = model('v1.Shop');
            // 会员升级
            if ('upgrade_level' == $param['action']) {
                $v1ShopModel->upgradeUserLevel($param, $this->users);
            }
            // 会员升级后续处理
            else if ('upgrade_level_pay' == $param['action']) {
                $v1ShopModel->handleUpgradeUserLevel($param, $this->users);
            }
        }
    }

    // 处理视频购买方法(集合方法)
    public function HandleBuyMediaAction()
    {
        if (IS_AJAX) {
            // 传入参数
            $param = input('param.');

            // 处理传入的参数
            $param['users_id'] = intval($this->users_id);
            $param['aid'] = !empty($param['aid']) ? intval($param['aid']) : 0;

            $v1ShopModel = model('v1.Shop');
            // 判断执行操作
            $action = !empty($param['action']) ? strval($param['action']) : '';
            if ('media' == $action) {
                // 会员升级
                $v1ShopModel->buyMedia($param);
            } else if ('media_pay' == $action) {
                //会员升级后续处理
                $v1ShopModel->handleBuyMedia($param);
            }
        }
    }

    public function verify_action()
    {
        if (IS_AJAX) {
            // 是否安装核销插件
            $weappInfo = model('ShopPublicHandle')->getWeappVerifyInfo();
            if (!empty($weappInfo) && 1 === intval($weappInfo['status'])) {
                // 调用分销逻辑层方法
                $verifyLogic = new \app\plugins\logic\VerifyLogic($this->users);
                $verifyLogic->verifyAction($weappInfo);
            }
        }
        $this->error('请求错误！');
    }

    /**
     * 注销账号(目前仅App端使用,使用手机号注销)
     */
    public function log_off()
    {
        $res = Db::name('users')->where('users_id',$this->users_id)->update(['is_del'=>1,'update_time'=>getTime()]);
        if ($res !== false){
            $this->success('注销成功！');
        }
        $this->error('注销失败！');
    }

    /**
     * 秒杀插件 立即购买
     */
    public function seckill_shop_buy_now()
    {
        //检测是否安装秒杀插件
        if (is_dir('./weapp/Seckill/')) {
            $SeckillRow = model('Weapp')->getWeappList('Seckill');
            if (!empty($SeckillRow) && 1 != intval($SeckillRow['status'])) {
                $this->error('请先安装并启用秒杀抢购插件！');
            }
        } else {
            $this->error('请先安装并启用秒杀抢购插件！');
        }

        if (IS_AJAX_POST) {
            $post = input('post.');
            // 数量判断
            if (empty($post['product_num']) || 0 > $post['product_num']) $this->error('请输入数量');

            // 默认会员ID
            $post['users_id'] = $this->users_id;

            // 商城模型
            $WeappSeckillModel = model('v1.WeappSeckill');

            // 商品是否已售罄
            $WeappSeckillModel->IsSaledOut($post);

            //限购
            $WeappSeckillModel->LimitBuy($post,$this->users);

            // 立即购买
            $WeappSeckillModel->ShopBuyNow($post);
        }
    }

    /**
     * 秒杀插件 产品购买
     */
    public function seckill_shop_product_buy()
    {
        //检测是否安装秒杀插件
        if (is_dir('./weapp/Seckill/')) {
            $SeckillRow = model('Weapp')->getWeappList('Seckill');
            if (!empty($SeckillRow) && 1 != intval($SeckillRow['status'])) {
                $this->error('请先安装并启用秒杀抢购插件！');
            }
        } else {
            $this->error('请先安装并启用秒杀抢购插件！');
        }

        if (IS_AJAX_POST) {
            // 获取解析数据
            $querystr = input('param.querystr/s');
            // if (empty($querystr)) $this->error('无效链接！');

            // 商城模型
            $ShopModel = model('v1.WeappSeckill');

            // 获取商品信息进行展示
            $ShopModel->GetProductData($querystr,$this->users);
        }
    }

    /**
     * 秒杀插件 订单结算
     */
    public function seckill_shop_order_pay()
    {
        //检测是否安装秒杀插件
        if (is_dir('./weapp/Seckill/')) {
            $SeckillRow = model('Weapp')->getWeappList('Seckill');
            if (!empty($SeckillRow) && 1 != intval($SeckillRow['status'])) {
                $this->error('请先安装并启用秒杀抢购插件！');
            }
        } else {
            $this->error('请先安装并启用秒杀抢购插件！');
        }

        if (IS_AJAX_POST) {
            $post           = input('post.');
            $post['action'] = !empty($post['action']) ? $post['action'] : 'CreatePay';

            // 默认会员ID
            $post['users_id'] = $this->users_id;
            $post['openid']   = Db::name('wx_users')->where('users_id', $this->users_id)->getField('openid');

            // 商城模型
            $ShopModel = model('v1.WeappSeckill');

            // 操作分发
            if ('DirectPay' == $post['action']) {
                // 订单直接支付
                $ShopModel->OrderDirectPay($post);
            } else if ('CreatePay' == $post['action']) {
                // 获取解析数据
                // if (empty($post['querystr'])) $this->error('无效链接！');

                // 获取商品信息生成订单并支付
                $ShopModel->ShopOrderPay($post, $this->users);
            }
        }
    }
    
    // 查询会员等级列表(已开启并且设置订单升级)
    public function getUsersUpgradeLevelList()
    {
        // 查询会员等级列表
        $where = [
            'status' => 1,
            'upgrade_type' => 1
        ];
        $upgrade_level = Db::name('users_level')->where($where)->order('level_value asc')->select();
    
        // 返回数据
        $result = [
            'users' => $this->users,
            'upgrade_level' => $upgrade_level,
        ];
        // dump($result);exit;
        return $this->renderSuccess($result);
    }
     // 活动报名操作(集合方法)
    public function actreg_userAction()
    {
        if (IS_AJAX) {
           if (is_dir('./weapp/Actreg/')) {
                // 是否安装活动报名插件
                $weappInfo = model('ShopPublicHandle')->getWeappInfo('Actreg');
                if (!empty($weappInfo)) {
                    // 调用开源小程序逻辑层方法
                    $ActregLogic = new \app\plugins\logic\ActregLogic($this->users);
                    $ActregLogic->ActregUserAction($weappInfo);
                }
            } else {
                $this->error('请先安装小程序活动报名插件！');
            }
        }
         $this->error('请求错误！');
    
    }

    // 招聘 - 用户接口（需要登录）
    public function recruitment_user_action()
    {
        if (IS_AJAX) {
            // 创建招聘逻辑层实例
            $users = $this->getUser();
            $recruitLogic = new \app\plugins\logic\RecruitLogic($users);
            $recruitLogic->userAction([]);
        }
        $this->error('请求错误！');
    }
}