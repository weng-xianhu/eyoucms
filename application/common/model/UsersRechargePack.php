<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海口快推科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 陈风任 <491085389@qq.com>
 * Date: 2019-1-7
 */

namespace app\common\model;

use think\Db;
use think\Cache;
use think\Config;

/**
 * 会员充值套餐模型
 */
load_trait('controller/Jump');
class UsersRechargePack
{
    use \traits\controller\Jump;

    private $times = 0;
    private $param = [];
    private $users = [];
    private $users_id = 0;
    private $appletsApi = 'openSource';

    // 构造函数
    public function __construct($param = [], $users = [], $appletsApi = '')
    {
        // 统一接收参数处理
        $this->times = getTime();
        $this->param = !empty($param) ? $param : [];
        $this->users = !empty($users) ? $users : [];
        $this->users_id = !empty($this->users['users_id']) ? intval($this->users['users_id']) : 0;
        $this->appletsApi = !empty($appletsApi) ? trim($appletsApi) : $this->appletsApi;
    }

    // 会员余额中心
    public function usersMoneyCenter()
    {
        // 查询条件
        $condition = [];
        array_push($condition, "status IN (2, 3)");
        array_push($condition, "users_id = " . $this->users_id);
        // 余额类型查询(收入、支出)
        $decrease_type = [3, 5, 6];
        $increase_type = [1, 2, 4, 7];

        // 查询总收入和总支出
        $where = "";
        if (0 < count($condition)) $where = implode(" AND ", $condition);
        $allIncrease = $allDecrease = 0;
        $money = Db::name('users_money')->where($where)->select();
        foreach ($money as $key => $value) {
            // 收入
            if (in_array($value['cause_type'], $increase_type)) {
                $allIncrease = unifyPriceHandle(unifyPriceHandle($allIncrease) + unifyPriceHandle($value['money']));
            }
            // 支出
            else if (in_array($value['cause_type'], $decrease_type) || (0 === intval($value['cause_type']) && 'balance' == $value['pay_method'])) {
                $allDecrease = unifyPriceHandle(unifyPriceHandle($allDecrease) + unifyPriceHandle($value['money']));
            }
        }
        $result['allIncrease'] = $allIncrease;
        $result['allDecrease'] = $allDecrease;

        // 金额类型条件
        if (!empty($this->param['moneyType']) && in_array($this->param['moneyType'], [10, 20])) {
            // 收入
            if (10 === intval($this->param['moneyType'])) {
                $cause_type = implode(',', $increase_type);
                array_push($condition, "cause_type IN ({$cause_type})");
            }
            // 支出
            else {
                $cause_type = implode(',', $decrease_type);
                array_push($condition, "(cause_type IN ({$cause_type}) OR (cause_type = 0 AND pay_method ='balance'))");
            }
        } else {
            $cause_type = implode(',', array_merge($increase_type, $decrease_type));
            array_push($condition, "(cause_type IN ({$cause_type}) OR (cause_type = 0 AND pay_method ='balance'))");
        }
        // 余额操作时间查询
        if (!empty($this->param['date'])) {
            $firstday = date('Y-m-01', strtotime($this->param['date']));
            $lastday  = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
            $firstday = strtotime($firstday);
            $lastday = strtotime($lastday);
            // 时间检索
            if (!empty($firstday) && !empty($lastday)) array_push($condition, "update_time BETWEEN ".$firstday." AND " . $lastday);
        }

        // 查询数据
        $where = "";
        if (0 < count($condition)) $where = implode(" AND ", $condition);
        $list = Db::name('users_money')->where($where)->order('update_time desc, moneyid desc')->select();
        // 处理数据
        $payCauseTypeArr = Config::get('global.pay_cause_type_arr');
        foreach ($list as $key => $value) {
            $value['money'] = unifyPriceHandle($value['money']);
            $value['users_money'] = unifyPriceHandle($value['users_money']);
            $value['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
            $value['update_time'] = date('Y-m-d H:i:s', $value['update_time']);
            if (!strstr($value['cause'], '(充值套餐)')) {
                $value['cause'] = !empty($payCauseTypeArr[$value['cause_type']]) ? $payCauseTypeArr[$value['cause_type']] : '';
            }
            $value['moneyType'] = 0;
            // 收入
            if (in_array($value['cause_type'], $increase_type)) {
                $value['moneyType'] =  10;
            }
            // 支出
            else if (in_array($value['cause_type'], $decrease_type) || (0 === intval($value['cause_type']) && 'balance' == $value['pay_method'])) {
                $value['moneyType'] =  20;
            }
            $list[$key] = $value;
        }

        // 返回数据
        $result['list'] = $list;
        $result['tabBar'] = [['id' => 0, 'name' => '全部'], ['id' => 10, 'name' => '收入'], ['id' => 20, 'name' => '支出']];
        $this->unifyParamSuccess('查询成功', null, $result);
    }

    // 会员充值套餐页面
    public function usersRechargePackPage()
    {
        // 查询数据
        $where = [
            'status' => 1,
        ];
        $result['list'] = Db::name('users_recharge_pack')->where($where)->order('pack_face_value asc, pack_pay_prices asc')->select();
        foreach ($result['list'] as $key => $value) {
            $value['pack_face_value'] = unifyPriceHandle($value['pack_face_value']);
            $value['pack_pay_prices'] = unifyPriceHandle($value['pack_pay_prices']);
            $result['list'][$key] = $value;
        }
        // 返回数据
        $this->unifyParamSuccess('查询成功', null, $result);
    }

    // 会员余额充值记录
    public function usersMoneyRechargeLog()
    {
        // 查询数据
        $where = [
            'status' => 3,
            'cause_type' => 1,
            'users_id' => $this->users_id,
        ];
        $result['list'] = Db::name('users_money')->where($where)->order('add_time desc')->select();
        foreach ($result['list'] as $key => $value) {
            $value['money'] = unifyPriceHandle($value['money']);
            $value['users_money'] = unifyPriceHandle($value['users_money']);
            $value['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
            $value['update_time'] = date('Y-m-d H:i:s', $value['update_time']);
            $result['list'][$key] = $value;
        }
        // 返回数据
        $this->unifyParamSuccess('查询成功', null, $result);
    }

    // 会员充值套餐充值记录
    public function usersRechargePackOrder()
    {
        // 查询数据
        $where = [
            'order_status' => 3,
            'users_id' => $this->users_id,
        ];
        $result['list'] = Db::name('users_recharge_pack_order')->where($where)->order('add_time desc, order_id desc')->select();
        foreach ($result['list'] as $key => $value) {
            $value['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
            $value['order_pay_time'] = date('Y-m-d H:i:s', $value['order_pay_time']);
            $value['order_face_value'] = unifyPriceHandle($value['order_face_value']);
            $value['order_pay_prices'] = unifyPriceHandle($value['order_pay_prices']);
            $result['list'][$key] = $value;
        }
        // 返回数据
        $this->unifyParamSuccess('查询成功', null, $result);
    }

    // 会员充值套餐订单创建
    public function usersRechargePackOrderCreate()
    {
        if (empty($this->param['pack_id'])) $this->error('充值套餐不存在，请重新选择');
        // 查询数据
        $where = [
            'status' => 1,
            'pack_id' => intval($this->param['pack_id'])
        ];
        $pack = Db::name('users_recharge_pack')->where($where)->find();
        if (empty($pack)) $this->error('充值套餐不存在，请重新选择');

        // 订单编号
        $orderCode = date('Ymd') . $this->times . rand(10, 99);
        // 订单支付类型(默认微信小程序)
        $orderPayName = 'wechat';
        // 订单支付终端(默认微信小程序)
        $orderPayTerminal = 3;
        // 抖音小程序
        if (9 === intval($this->param['pay_type'])) {
            $orderPayName = 'tikTokPay';
            $orderPayTerminal = 4;
        }
        // 百度小程序
        if (10 === intval($this->param['pay_type'])) {
            $orderPayName = 'baiduPay';
            $orderPayTerminal = 5;
        }
        // 查询会员是否有未支付的订单
        $orderData = $this->usersRechargePackOrderFind();
        // 当前订单完成后的会员储值余额
        $stored_money = unifyPriceHandle($this->users['users_money'] + $pack['pack_face_value']);
        // 存在未支付订单则执行订单更新
        if (!empty($orderData['order_id']) && !empty($orderData['order_code']) && !empty($orderData['order_pay_code'])) {
            $where = [
                'users_id' => $this->users_id,
                'order_id' => $orderData['order_id'],
                'order_code' => $orderData['order_code'],
                'order_pay_code' => $orderData['order_pay_code'],
            ];
            $update = [
                'pack_id' => intval($pack['pack_id']),
                'stored_money' => $stored_money,
                'order_pack_names' => $pack['pack_names'],
                'order_face_value' => unifyPriceHandle($pack['pack_face_value']),
                'order_pay_prices' => unifyPriceHandle($pack['pack_pay_prices']),
                'order_pay_code' => $orderCode,
                'order_pay_name' => $orderPayName,
                'order_pay_terminal' => $orderPayTerminal,
                'update_time' => $this->times,
            ];
            $result = Db::name('users_recharge_pack_order')->where($where)->update($update);
            if (!empty($result)) {
                $orderID = intval($orderData['order_id']);
                $orderData = array_merge($orderData, $update);
            }
        }
        // 订单创建
        else {
            $orderData = [
                'pack_id' => intval($pack['pack_id']),
                'users_id' => intval($this->users_id),
                'stored_money' => $stored_money,
                'order_code' => 'CZ' . $orderCode,
                'order_status' => 1,
                'order_pack_names' => $pack['pack_names'],
                'order_face_value' => unifyPriceHandle($pack['pack_face_value']),
                'order_pay_prices' => unifyPriceHandle($pack['pack_pay_prices']),
                'order_pay_code' => $orderCode,
                'order_pay_time' => 0,
                'order_pay_name' => $orderPayName,
                'order_pay_terminal' => $orderPayTerminal,
                'order_pay_details' => '',
                'add_time' => $this->times,
                'update_time' => $this->times,
            ];
            $orderID = Db::name('users_recharge_pack_order')->insertGetId($orderData);
        }

        // 订单支付信息
        if (!empty($orderID)) {
            // 订单ID
            $orderData['order_id'] = intval($orderID);
            // 微信小程序支付
            if (1 === intval($this->param['pay_type'])) {
                // 调用微信支付接口
                $weChatPay = model('ShopPublicHandle')->getWechatAppletsPay($this->users_id, $orderData['order_pay_code'], $orderData['order_pay_prices'], 20);
                $result = [
                    'weChatPay' => $weChatPay,
                    'orderData' => [
                        'order_id' => intval($orderID),
                        'order_code' => $orderData['order_code'],
                        'order_pay_code' => $orderData['order_pay_code']
                    ]
                ];
                // 返回提示
                $this->success('正在支付', null, $result);
            }
            // 抖音小程序支付
            else if (9 === intval($this->param['pay_type'])) {
                $tikTokPay = model('TikTok')->getTikTokAppletsPay($orderID, $orderData['order_pay_code'], $orderData['order_pay_prices'], 30, 'users_recharge_pack_order', 20);
                $result = [
                    'tikTokPay' => $tikTokPay,
                    'orderData' => [
                        'order_id' => $orderID,
                        'order_code' => $orderData['order_code'],
                        'order_pay_code' => $orderData['order_pay_code']
                    ]
                ];
                // 返回提示
                $this->success('正在支付', null, $result);
            }
            // 百度小程序支付
            else if (10 === intval($this->param['pay_type'])) {
                $baiduPay = model('BaiduPay')->getBaiDuAppletsPay($orderID, $orderData['order_pay_code'], $orderData['order_pay_prices'], 'users_recharge_pack_order', 20);
                $result = [
                    'baiduPay' => $baiduPay,
                    'orderData' => [
                        'order_id' => $orderID,
                        'order_code' => $orderData['order_code'],
                        'order_pay_code' => $orderData['order_pay_code']
                    ]
                ];
                // 返回提示
                $this->success('正在支付', null, $result);
            }
        }
    }

    // 会员充值余额下单
    public function usersRechargeMoneyOrderCreate()
    {
        if (empty($this->param['users_money'])) $this->error('请输入充值金额');
        // 订单类型(1:会员充值余额)
        $causeType = 1;
        // 订单编号
        $orderNumber = date('Ymd') . $this->times . rand(10, 99);
        // 订单支付类型
        $payMethod = 9 === intval($this->param['pay_type']) ? 'tikTokPay' : 'wechat';
        // 会员当前余额
        $usersMoney = Db::name('users')->where('users_id', $this->users_id)->getField('users_money');
        // 查询会员充值余额订单
        $moneyData = $this->usersRechargeMoneyOrderFind();
        // 数据添加到订单表
        $payCauseTypeArr = Config::get('global.pay_cause_type_arr');
        if (!empty($moneyData['moneyid']) && !empty($moneyData['order_number'])) {
            $where = [
                'users_id' => $this->users_id,
                'moneyid' => $moneyData['moneyid'],
                'order_number' => $moneyData['order_number'],
            ];
            $update = [
                'money' => unifyPriceHandle($this->param['users_money']),
                'users_money' => unifyPriceHandle($usersMoney + $this->param['users_money']),
                'order_number' => $orderNumber,
                'update_time' => $this->times,
            ];
            $result = Db::name('users_money')->where($where)->update($update);
            if (!empty($result)) {
                $moneyID = intval($moneyData['moneyid']);
                $moneyData = array_merge($moneyData, $update);
            }
        } else {
            $moneyData = [
                'users_id' => $this->users_id,
                'cause_type' => $causeType,
                'pay_method' => $payMethod,
                'cause' => $payCauseTypeArr[$causeType],
                'money' => unifyPriceHandle($this->param['users_money']),
                'users_money' => unifyPriceHandle($usersMoney + $this->param['users_money']),
                'pay_details' => '',
                'order_number' => $orderNumber,
                'status' => 1,
                'lang' => get_home_lang(),
                'add_time' => $this->times,
                'update_time' => $this->times,
            ];
            $moneyID = Db::name('users_money')->insertGetId($moneyData);
        }

        // 订单支付信息
        if (!empty($moneyID)) {
            // 订单ID
            $moneyData['moneyid'] = intval($moneyID);
            // 微信小程序支付
            if (1 === intval($this->param['pay_type'])) {
                // 调用微信支付接口
                $weChatPay = model('ShopPublicHandle')->getWechatAppletsPay($this->users_id, $moneyData['order_number'], $moneyData['money'], 1);
                $result = [
                    'weChatPay' => $weChatPay,
                    'orderData' => [
                        'moneyid' => $moneyID,
                        'order_number' => $moneyData['order_number'],
                    ]
                ];
                // 返回提示
                $this->success('正在支付', null, $result);
            }
            // 抖音小程序支付
            else if (9 === intval($this->param['pay_type'])) {
                $tikTokPay = model('TikTok')->getTikTokAppletsPay($moneyID, $moneyData['order_number'], $moneyData['money'], 30, 'users_money', 1);
                $result = [
                    'tikTokPay' => $tikTokPay,
                    'orderData' => [
                        'moneyid' => $moneyID,
                        'order_number' => $moneyData['order_number'],
                    ]
                ];
                // 返回提示
                $this->success('正在支付', null, $result);
            }
            // 百度小程序支付
            else if (10 === intval($this->param['pay_type'])) {
                $baiduPay = model('BaiduPay')->getBaiDuAppletsPay($moneyID, $moneyData['order_number'], $moneyData['money'], 'users_money', 1);
                $result = [
                    'baiduPay' => $baiduPay,
                    'orderData' => [
                        'moneyid' => $moneyID,
                        'order_number' => $moneyData['order_number'],
                    ]
                ];
                // 返回提示
                $this->success('正在支付', null, $result);
            }
        }
    }

    // 会员充值套餐订单支付后续处理
    public function usersRechargePackOrderPayHandle()
    {
        $orderData = [];
        if (!empty($this->param['moneyid']) && !empty($this->param['order_number'])) {
            // 数据表
            $table = 'users_money';
            // 查询会员充值余额订单
            $orderData = $this->usersRechargeMoneyOrderFind(true);
            $orderData['unified_id'] = $orderData['moneyid'];
            $orderData['unified_number'] = $orderData['order_number'];
            $orderData['transaction_type'] = 1;
        } else if (!empty($this->param['order_id']) && !empty($this->param['order_code']) && !empty($this->param['order_pay_code'])) {
            // 数据表
            $table = 'users_recharge_pack_order';
            // 查询会员充值套餐订单
            $orderData = $this->usersRechargePackOrderFind(true);
            $orderData['unified_id'] = $orderData['order_id'];
            $orderData['unified_number'] = $orderData['order_pay_code'];
            $orderData['transaction_type'] = 20;
        }

        // 查询是否真实支付并完成支付后续操作
        if (!empty($orderData)) {
            // 微信支付查询
            if (1 === intval($this->param['pay_type'])) {
                model('ShopPublicHandle')->getWeChatPayResult($this->users_id, $orderData, $orderData['transaction_type']);
            }
            // 抖音支付查询
            else if (9 === intval($this->param['pay_type'])) {
                model('TikTok')->tikTokAppletsPayDealWith($orderData, false, $table);
            }
            // 百度小程序支付
            else if (10 === intval($this->param['pay_type'])) {
                model('BaiduPay')->baiDuAppletsPayDealWith($orderData, false, $table);
            }
        }
    }

    // 查询会员充值套餐订单
    private function usersRechargePackOrderFind($error = false)
    {
        $orderData = [];
        // 存在订单信息则查询订单号是否真实存在
        if (!empty($this->param['order_id']) && !empty($this->param['order_code']) && !empty($this->param['order_pay_code'])) {
            $where = [
                'users_id' => $this->users_id,
                'order_id' => $this->param['order_id'],
                'order_code' => $this->param['order_code'],
                'order_pay_code' => $this->param['order_pay_code'],
            ];
            if (empty($error)) $where['order_status'] = 1;
            $orderData = Db::name('users_recharge_pack_order')->where($where)->order('order_id desc')->find();
            if (!empty($error) && empty($orderData)) $this->error('订单查询失败');
            if (!empty($error) && !empty($orderData['order_status']) && 4 === intval($orderData['order_status'])) $this->error('订单已过期');
            // 订单已完成
            if (!empty($error) && !empty($orderData['order_status']) && in_array($orderData['order_status'], [2, 3])) {
                if ('wechat' === trim($orderData['order_pay_name'])) {
                    model('ShopPublicHandle')->pushWxShippingInfo($this->users_id, $orderData['order_pay_code'], 20, '充值');
                }
                $this->success('订单已完成');
            }
        }

        // 查询会员是否存在未支付的订单
        if (empty($orderData)) {
            $where = [
                'users_id' => $this->users_id,
                'order_status' => 1,
            ];
            $orderData = Db::name('users_recharge_pack_order')->where($where)->order('order_id desc')->find();
        }

        return $orderData;
    }

    // 查询会员充值余额订单
    private function usersRechargeMoneyOrderFind($error = false)
    {
        $moneyData = [];
        // 存在订单信息则查询订单号是否真实存在
        if (!empty($this->param['moneyid']) && !empty($this->param['order_number'])) {
            $where = [
                'moneyid' => $this->param['moneyid'],
                'users_id' => $this->users_id,
                'cause_type' => 1,
                'order_number' => $this->param['order_number'],
            ];
            if (empty($error)) $where['status'] = 1;
            $moneyData = Db::name('users_money')->where($where)->order('moneyid desc')->find();
            if (!empty($error) && empty($moneyData)) $this->error('订单查询失败');
            if (!empty($error) && !empty($moneyData['status']) && 0 === intval($moneyData['status'])) $this->error('订单支付失败');
            if (!empty($error) && !empty($moneyData['status']) && 4 === intval($moneyData['status'])) $this->error('订单已过期');
            // 订单已完成
            if (!empty($error) && !empty($moneyData['status']) && in_array($moneyData['status'], [2, 3])) {
                if ('wechat' === trim($moneyData['pay_method'])) {
                    model('ShopPublicHandle')->pushWxShippingInfo($this->users_id, $moneyData['order_number'], 1, '充值');
                }
                $this->success('订单已完成');
            }
        }

        // 查询会员是否存在未支付的订单
        if (empty($moneyData)) {
            $where = [
                'users_id' => $this->users_id,
                'cause_type' => 1,
                'status' => 1,
            ];
            $moneyData = Db::name('users_money')->where($where)->order('moneyid desc')->find();
        }

        return $moneyData;
    }

    // 统一携带默认参数返回
    private function unifyParamSuccess($msg = '操作成功', $url = null, $result = [])
    {
        // 是否加载显示
        $result['loadShow'] = 1;
        // 会员信息
        $result['users'] = $this->users;
        // 返回结果
        if (model('ShopPublicHandle')->detectH5Terminal($this->param['terminal'])) {
            // $pointsShopController = new \app\plugins\controller\PointsShop;
            // $paramA = !empty($this->param['a']) ? $this->param['a'] : 'errorMsg';
            // $pointsShopController->$paramA($result);
        } else {
            $this->success($msg, $url, $result);
        }
    }
}
