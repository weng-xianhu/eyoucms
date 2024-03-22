<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海口快推科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */

namespace app\common\model;

use think\Db;
use think\Config;

/**
 * 抖音公共模型
 */
load_trait('controller/Jump');
class TikTok
{
    use \traits\controller\Jump;

    // 构造函数
    public function __construct($tikTokConfig = [])
    {
        // 统一接收参数处理
        $this->times = getTime();
        // 处理会员信息
        $this->handleUsersInfo();
        // 接口请求头
        $this->headers  = ['Content-Type: application/json'];
        // 如果是传入配置访问则不获取 access_token
        $this->ajaxRequest = !empty($tikTokConfig) ? false : true;
        // 抖音信息
        $this->tikTokConfig = !empty($tikTokConfig) ? $tikTokConfig : model('ShopPublicHandle')->getSpecifyAppletsConfig();
        // 判断是否需要获取 access_token
        if (!empty($this->ajaxRequest) && ( empty($this->tikTokConfig['access_token']) || $this->times > $this->tikTokConfig['expire_time'] )) {
            $result = getToutiaoAccessToken($this->tikTokConfig['appid'], $this->tikTokConfig['appsecret'], $this->tikTokConfig['salt'], false);
            if (!empty($result['code']) && 1 === intval($result['code']) && !empty($result['access_token'])) {
                $this->tikTokConfig['expire_time'] = intval($result['expire_time']);
                $this->tikTokConfig['access_token'] = trim($result['access_token']);
                $applets_id = input('param.applets_id/d', 0);
                if (!empty($applets_id)) {
                    $where = [
                        'applets_id' => intval($applets_id),
                        'applets_type' => 3,
                        'applets_mark' => 'toutiao',
                    ];
                    $update = [
                        'applets_config' => !empty($this->tikTokConfig) ? serialize($this->tikTokConfig) : '',
                        'update_time' => $this->times,
                    ];
                    Db::name('weapp_applets_config_list')->where($where)->update($update);
                }
            } else {
                $this->error('抖音小程序配置异常，请联系客服');
            }
        }
    }

    // 处理会员信息
    private function handleUsersInfo()
    {
        // 获取会员信息
        $this->users = session('users') ? session('users') : GetUsersLatestData();
        $this->users_id = $this->users['users_id'] ? intval($this->users['users_id']) : 0;
        // 获取会员openid
        $this->open_id = Db::name('wx_users')->where('users_id', $this->users_id)->getField('openid');
    }

    // 获取抖音支付订单号(order_id)、订单token(order_token)
    public function getTikTokAppletsPay($orderID = '', $orderCode = '', $orderAmount = 0, $orderUnpayCloseTime = 2880, $table = 'shop_order', $type = 2)
    {
        // 接口参数
        $postData = [
            'app_id'       => $this->tikTokConfig['appid'],
            'out_order_no' => strval($orderCode),
            'total_amount' => floatval($orderAmount * 100),
            'subject'      => '抖音支付',
            'body'         => '商品购买',
            'valid_time'   => intval(intval($orderUnpayCloseTime) * 60),
            'cp_extra'     => "tikTok|,|" . session('users_id') . "|,|" . $orderID . "|,|" . $orderCode . "|,|" . $table . "|,|" . $type,
            'notify_url'   => request()->domain() . ROOT_DIR . '/index.php',
        ];
        // MD5加密签名
        $postData['sign'] = $this->getSignParam($postData);
        // 接口API
        $url = 'https://developer.toutiao.com/api/apps/ecpay/v1/create_order';
        $jsonData = json_decode(httpRequest($url, 'POST', json_encode($postData), $this->headers, 300000), true);
        // 返回订单创建信息
        if (isset($jsonData['err_no']) && 0 === intval($jsonData['err_no']) && isset($jsonData['err_tips']) && 'success' == strval($jsonData['err_tips'])) {
            // 订单同步到抖音APP订单中心
            $this->synchTikTokOrder($orderID, $orderCode, $orderAmount, 0, $table, $type);
            // 返回支付信息
            return [
                'success' => true,
                'order_id' => $jsonData['data']['order_id'],
                'order_token' => $jsonData['data']['order_token'],
            ];
        } else {
            if (stristr($jsonData['err_tips'], '签名')) $jsonData['err_tips'] = 'AppID或SALT错误，请逐个检查再提交';
            return [
                'success' => false,
                'err_tips' => $jsonData['err_tips'],
            ];
        }
    }

    // 订单同步到抖音APP订单中心
    public function synchTikTokOrder($orderID = '', $orderCode = '', $orderAmount = 0, $orderStatus = 0, $table = 'shop_order', $type = 2)
    {
        // 查询订单信息
        $post = [
            'users_id'   => intval(session('users_id')),
            'order_id'   => intval($orderID),
            'order_code' => strval($orderCode),
        ];
        $order = $this->getSystemOrder($post, false, $table, true);
        // 订单数量总计
        $amount = 1;
        // 订单商品列表
        $itemList = [];
        // 会员订单
        if (2 === intval($type)) {
            $where = [
                'order_id' => intval($orderID),
                'users_id' => intval(session('users_id')),
            ];
            $details = Db::name('shop_order_details')->where($where)->select();
            foreach ($details as $key => $value) {
                $amount++;
                $itemList[] = [
                    'item_code' => !empty($value['product_id']) ? strval($value['product_id']) : strval($value['details_id']),
                    'img' => handle_subdir_pic($value['litpic'], 'img', true),
                    'title' => strval($value['product_name']),
                    'amount' => intval($value['num']),
                    'price' => floatval($value['product_price'] * 100),
                ];
            }
            $detailUrl = 'otherpages/mall/order/detail?order_id=' . $orderID;
        }
        // 会员充值
        else if (in_array($type, [1, 20])) {
            $amount = 1;
            $itemList[] = [
                'item_code' => !empty($order['pack_id']) ? strval($order['pack_id']) : strval($order['moneyid']),
                'img' => strval('https://002.5fa.cn/mall8615/public/static/common/images/users_recharge.png'),
                'title' => strval('会员充值'),
                'amount' => intval($amount),
                'price' => floatval($orderAmount * 100),
            ];
            $detailUrl = 'otherpages/user/wallet/withdrawal/record';
        }
        // 会员升级
        else if (3 === intval($type)) {
            $amount = 1;
            $itemList[] = [
                'item_code' => !empty($order['level_id']) ? strval($order['level_id']) : strval($order['moneyid']),
                'img' => strval('https://002.5fa.cn/mall8615/public/static/common/images/users_upgrade.png'),
                'title' => strval('会员升级'),
                'amount' => intval($amount),
                'price' => floatval($orderAmount * 100),
            ];
            $detailUrl = 'otherpages/user/upgradeMember/upgradeMember';
        }
        $statusName = '待支付';
        if (1 === intval($orderStatus)) {
            $statusName = '已支付';
        }
        // 接口参数
        $orderDetail = [
            'order_id' => strval($orderCode),
            'create_time' => getMsectime(),
            'status' => strval($statusName),
            'amount' => $amount,
            'total_price' => floatval($orderAmount * 100),
            'detail_url' => $detailUrl,
            'item_list' => $itemList,
        ];
        $postData = [
            'access_token' => $this->tikTokConfig['access_token'],
            'app_name'     => 'douyin',
            'open_id'      => $this->open_id,
            'order_detail' => json_encode($orderDetail),
            'order_status' => intval($orderStatus),
            'order_type'   => 0,
            'update_time'  => $this->times,
        ];
        // 接口API
        httpRequest('https://developer.toutiao.com/api/apps/order/v2/push', 'POST', json_encode($postData), $this->headers, 300000);
    }

    // 抖音小程序商品购买支付后续处理
    public function tikTokAppletsPayDealWith($post = [], $notify = false, $table = 'shop_order')
    {
        // 异步回调时执行
        $cpExtra = !empty($post['msg']['cp_extra']) ? explode('|,|', $post['msg']['cp_extra']) : [];
        if (true === $notify && !empty($cpExtra[0]) && 'tikTok' == $cpExtra[0]) {
            $post['users_id'] = !empty($cpExtra[1]) ? $cpExtra[1] : 0;
            $post['order_id'] = !empty($cpExtra[2]) ? $cpExtra[2] : 0;
            $post['order_code'] = !empty($cpExtra[3]) ? $cpExtra[3] : '';
            $post['transaction_type'] = !empty($cpExtra[5]) ? $cpExtra[5] : 0;
            $table = !empty($cpExtra[4]) ? $cpExtra[4] : $table;
        }

        if (!empty($post['users_id'])) {
            // 获取系统订单
            $order = $this->getSystemOrder($post, $notify, $table);
            // 查询抖音支付订单是否真实完成支付
            $jsonData = $this->queryOrderPayResult($order['unified_number']);
            if (isset($jsonData['err_no']) && 0 === intval($jsonData['err_no']) && !empty($jsonData['payment_info']['order_status'])) {
                // 支付成功
                if ('SUCCESS' == $jsonData['payment_info']['order_status']) {
                    // 订单同步到抖音APP订单中心
                    $this->synchTikTokOrder($order['unified_id'], $order['unified_number'], $order['unified_amount'], 1, $table, $post['transaction_type']);

                    // 处理系统订单
                    $post['unified_id'] = $order['unified_id'];
                    $post['unified_number'] = $order['unified_number'];
                    $payApiLogic = new \app\user\logic\PayApiLogic($post['users_id'], true);
                    $payApiLogic->OrderProcessing($post, $order, $jsonData);
                }
                // 订单尚未支付
                else if ('PROCESSING' == $jsonData['payment_info']['order_status']) {
                    if (true !== $notify) $this->error('订单尚未支付');
                }
                // 超时未支付
                else if ('TIMEOUT' == $jsonData['payment_info']['order_status']) {
                    if (true !== $notify) $this->error($jsonData['err_tips']);
                }
                // 支付失败
                else if ('FAIL' == $jsonData['payment_info']['order_status']) {
                    if (true !== $notify) $this->error($jsonData['err_tips']);
                }
            } else {
                if (true !== $notify) $this->error($jsonData['err_tips']);
            }
        }
    }

    // 查询系统订单
    private function getSystemOrder($post = [], $notify = false, $table = 'shop_order', $other = false)
    {
        // 商城商品订单
        if ('shop_order' == $table) {
            // 查询系统订单信息
            $where = [
                'users_id'   => intval($post['users_id']),
                'order_id'   => intval($post['order_id']),
                'order_code' => strval($post['order_code']),
            ];
            $order = Db::name('shop_order')->where($where)->find();
            if (empty($other)) {
                if (empty($order)) {
                    // 同步
                    if (true !== $notify) $this->error('无效订单');
                } else if (0 < $order['order_status']) {
                    // 异步
                    if (true === $notify) {
                        exit(json_encode(['err_no' => 0, 'err_tips' => 'success']));
                    }
                    // 同步
                    else {
                        $usersData = Db::name('users')->where('users_id', $post['users_id'])->find();
                        // 邮箱发送
                        $resultData['email'] = GetEamilSendData(tpCache('smtp'), $usersData, $order, 1, 'wechat');
                        // 短信发送
                        $resultData['mobile'] = GetMobileSendData(tpCache('sms'), $usersData, $order, 1, 'wechat');
                        // 跳转链接
                        $url = 1 == input('param.fenbao/d') ? '' : '/pages/order/index';
                        $resultData['url'] = $url;
                        $this->success('支付完成', $url, $resultData);
                    }
                }
            }
            $order['unified_id'] = intval($order['order_id']);
            $order['unified_number'] = strval($order['order_code']);
            $order['unified_amount'] = floatval($order['order_amount']);
        }
        // 会员充值套餐订单
        else if ('users_recharge_pack_order' == $table) {
            if (empty($other)) {
                $where = [
                    'users_id' => intval($post['users_id']),
                    'order_id' => intval($post['order_id']),
                    'order_code' => strval($post['order_code']),
                    'order_pay_code' => strval($post['order_pay_code']),
                ];
            } else {
                $where = [
                    'users_id' => intval($post['users_id']),
                    'order_id' => intval($post['order_id']),
                    'order_pay_code' => strval($post['order_code']),
                ];
            }
            $order = Db::name('users_recharge_pack_order')->where($where)->find();
            if (empty($other)) {
                if (empty($order)) {
                    // 同步
                    if (true !== $notify) $this->error('无效订单');
                } else if (1 < $order['order_status']) {
                    // 异步
                    if (true === $notify) {
                        exit(json_encode(['err_no' => 0, 'err_tips' => 'success']));
                    }
                    // 同步
                    else {
                        $this->success('支付完成');
                    }
                }
            }
            $order['unified_id'] = intval($order['order_id']);
            $order['unified_number'] = strval($order['order_pay_code']);
            $order['unified_amount'] = floatval($order['order_pay_prices']);
        }
        // 会员(充值余额 or 升级)订单
        else if ('users_money' == $table) {
            $post['moneyid'] = !empty($post['order_id']) ? $post['order_id'] : $post['moneyid'];
            $post['order_number'] = !empty($post['order_code']) ? $post['order_code'] : $post['order_number'];
            $where = [
                'moneyid' => intval($post['moneyid']),
                'users_id' => intval($post['users_id']),
                'order_number' => strval($post['order_number']),
            ];
            $order = Db::name('users_money')->where($where)->find();
            if (empty($other)) {
                if (empty($order)) {
                    // 同步
                    if (true !== $notify) $this->error('无效订单');
                } else if (1 < $order['status']) {
                    // 异步
                    if (true === $notify) {
                        exit(json_encode(['err_no' => 0, 'err_tips' => 'success']));
                    }
                    // 同步
                    else {
                        $this->success('支付完成');
                    }
                }
            }
            $order['unified_id'] = intval($order['moneyid']);
            $order['unified_number'] = strval($order['order_number']);
            $order['unified_amount'] = floatval($order['money']);
        }

        return $order;
    }

    // 查询抖音支付订单是否真实完成支付
    public function queryOrderPayResult($orderCode = '')
    {
        // 接口参数
        $postData = [
            'app_id'       => $this->tikTokConfig['appid'],
            'out_order_no' => strval($orderCode),
        ];
        // MD5加密签名
        $postData['sign'] = $this->getSignParam($postData);
        // 接口API
        $url = 'https://developer.toutiao.com/api/apps/ecpay/v1/query_order';
        // 返回结果
        return json_decode(httpRequest($url, 'POST', json_encode($postData), $this->headers, 300000), true);
    }

    private function getSignParam($map = [])
    {
        $param = [];
        foreach($map as $key => $value) {
            // 排除不参与加密的字段
            if ($key == "other_settle_params" || $key == "app_id" || $key == "sign" || $key == "thirdparty_id") continue;

            // 参数处理
            $valueNew = trim(strval($value));
            if (is_array($value)) $valueNew = arrayToStr($value);

            $len = strlen($valueNew);
            if ($len > 1 && substr($valueNew, 0,1)=="\"" && substr($valueNew, $len-1)=="\"") $valueNew = substr($valueNew,1, $len-1);

            $valueNew = trim($valueNew);
            if ($valueNew == "" || $valueNew == "null") continue;

            $param[] = $valueNew;
        }
        $param[] = $this->tikTokConfig['salt'];
        sort($param, SORT_STRING);
        return md5(trim(implode('&', $param)));
    }

    private function arrayToStr($map = [])
    {
        $isMap = isArrMap($map);

        $result = "";
        if (!empty($isMap)) $result = "map[";

        $keyArr = array_keys($map);
        if (!empty($isMap)) sort($keyArr);

        $paramsArr = array();
        foreach($keyArr as $key) {
            $v = $map[$key];
            if (!empty($isMap)) {
                if (is_array($v)) {
                    $paramsArr[] = sprintf("%s:%s", $key, arrayToStr($v));
                } else  {
                    $paramsArr[] = sprintf("%s:%s", $key, trim(strval($v)));
                }
            } else {
                if (is_array($v)) {
                    $paramsArr[] = arrayToStr($v);
                } else  {
                    $paramsArr[] = trim(strval($v));
                }
            }
        }

        $result = sprintf("%s%s", $result, join(" ", $paramsArr));
        if (empty($isMap)) {
            $result = sprintf("[%s]", $result);
        } else {
            $result = sprintf("%s]", $result);
        }

        return $result;
    }

    private function isArrMap($map = [])
    {
        foreach($map as $k =>$v) {
            if (is_string($k)) return true;
        }
        return false;
    }

}