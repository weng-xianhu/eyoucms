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
 * Date: 2019-06-25
 */

namespace app\user\logic;

use think\Model;
use think\Db;
use think\Request;
use think\Config;

/**
 * 回调逻辑处理
 * @package user\Logic
 */
class PayLogic extends Model
{
    private $home_lang = 'cn';

    /**
     * 初始化操作
     */
    public function initialize() {
        parent::initialize();

        $this->home_lang             = get_home_lang();
        $this->users_db              = Db::name('users');               // 会员数据表
        $this->users_money_db        = Db::name('users_money');         // 会员金额明细表
        $this->shop_order_db         = Db::name('shop_order');          // 订单主表
        $this->shop_order_details_db = Db::name('shop_order_details');  // 订单明细表
        $this->users_type_manage_db  = Db::name('users_type_manage');   // 会员升级分类价格表
        $this->media_order_db        = Db::name('media_order');         // 视频订单表
        $this->article_order_db      = Db::name('article_order');       // 文章订单表
        $this->download_order_db      = Db::name('download_order');       // 下载模型订单表
    }

    /*----------支付宝回调开始----------*/
    public function alipay_return()
    {
        if (!empty($_POST)) {
            foreach($_POST as $key => $value){
                $_GET[$key] = $value;
            }
        }
        $param = $data = $_GET;
        if (empty($param['total_amount']) && !empty($param['total_fee'])){
            $param['total_amount'] = $param['total_fee'];
        }
        $OrderData = $this->checkAmount($param['out_trade_no'],$param['total_amount'],$param['transaction_type']);
        if(empty($OrderData)){
            if (!empty($param['is_notify']) && 1 == $param['is_notify']) {
                echo 'fail'; exit;
            }else{
                $retData = [
                    'code' => 0,
                    'msg'  => '支付失败，支付金额与订单金额不相符',
                ];
                return $retData;
            }
        }
        // 支付宝配置信息
        $where = [
            'pay_id' => 2,
            'pay_mark' => 'alipay'
        ];
        $pay_alipay_config = Db::name('pay_api_config')->where($where)->getField('pay_info');
        if (empty($pay_alipay_config)) {
            $pay_alipay_config = getUsersConfigData('pay.pay_alipay_config');
            if (empty($pay_alipay_config)) {
                if (!empty($param['is_notify']) && 1 == $param['is_notify']) {
                    echo 'fail'; exit;
                }else{
                    $retData = [
                        'code' => 0,
                        'msg'  => '支付宝配置不正确',
                    ];
                    return $retData;
                }
            }
        }
        $pay_alipay_config = unserialize($pay_alipay_config);

        // 新旧版处理
        switch ($pay_alipay_config['version']) {
            // 新版支付宝
            case '0':
                // 新版获取RSA2加密返回，返回bool值
                $Return = $this->GetNewAliPayRsa2Return($data, $pay_alipay_config);
                if (!empty($Return)) {
                    $return = $this->NewAliPayProcessing($param);
                    if (!empty($param['is_notify']) && 1 == $param['is_notify']) {
                        echo $return; exit;
                    }else{
                        return $return;
                    }
                }else{
                    if (!empty($param['is_notify']) && 1 == $param['is_notify']) {
                        echo 'fail'; exit;
                    }else{
                        $retData = [
                            'code' => 0,
                            'msg'  => '订单验证失败！',
                        ];
                        return $retData;
                    }                      
                }
                break;
            
            // 旧版支付宝
            case '1':
                // 旧版获取MD5加密的sign
                $Sign = $this->GetOldAliPayMd5Sign($data, $pay_alipay_config['code']);
                if ($Sign == $data['sign']){
                    $return = $this->OldAliPayProcessing($param);
                    if (!empty($param['is_notify']) && 1 == $param['is_notify']) {
                        echo $return; exit;
                    }else{
                        return $return;
                    }
                }else{
                    if (!empty($param['is_notify']) && 1 == $param['is_notify']) {
                        echo "fail"; exit;
                    }else{
                        $retData = [
                            'code' => 0,
                            'msg'  => '订单验证失败！',
                        ];
                        return $retData;
                    }
                }
                break;
        }
    }

    // 新版
    private function NewAliPayProcessing($param = array())
    {   
        // 实际付款金额
        $order_amount = $param['total_amount'];
        if (2 == $param['transaction_type']) {
            // 商城订单购买支付回调处理
            $return = $this->ShopOrderProcessing($param, $order_amount);
            return $return;

        }else if (1 == $param['transaction_type'] || 3 == $param['transaction_type']) {
            // 会员充值或升级支付回调处理
            $return = $this->MoneyOrderProcessing($param, $order_amount);
            return $return;
        }else if (8 == $param['transaction_type']) {
            // 视频购买
            $return = $this->MediaOrderProcessing($param, $order_amount);
            return $return;
        }else if (9 == $param['transaction_type']) {
            // 文章购买
            $return = $this->ArticleOrderProcessing($param, $order_amount);
            return $return;
        }else if (10 == $param['transaction_type']) {
            // 下载购买
            $return = $this->DownloadOrderProcessing($param, $order_amount);
            return $return;
        }
    }

    // 旧版
    private function OldAliPayProcessing($param = array())
    {   
        // 实际付款金额
        $order_amount = $param['total_fee'];
        if (2 == $param['transaction_type']) {
            // 商城订单购买支付回调处理
            $return = $this->ShopOrderProcessing($param, $order_amount);
            return $return;

        }else if (1 == $param['transaction_type'] || 3 == $param['transaction_type']) {
            // 会员充值或升级支付回调处理
            $return = $this->MoneyOrderProcessing($param, $order_amount);
            return $return;
        }else if (8 == $param['transaction_type']) {
            // 视频购买
            $return = $this->MediaOrderProcessing($param, $order_amount);
            return $return;
        }else if (9 == $param['transaction_type']) {
            // 文章购买
            $return = $this->ArticleOrderProcessing($param, $order_amount);
            return $return;
        }else if (10 == $param['transaction_type']) {
            // 文章购买
            $return = $this->DownloadOrderProcessing($param, $order_amount);
            return $return;
        }
    }

    // 商城订单购买支付回调处理
    private function ShopOrderProcessing($param = array(), $order_amount = null)
    {
        if (!empty($param['out_trade_no']) && !empty($param['trade_no'])) {
            $OrderWhere = [
                // 订单号
                'order_code' => $param['out_trade_no'],
                // 实际支付金额
                'order_amount' => $order_amount,
            ];
            $OrderData = $this->shop_order_db->where($OrderWhere)->find();
            if (!empty($OrderData)) {
                // 支付宝付款成功后，订单并未修改状态时，修改订单状态并返回
                if (0 == $OrderData['order_status']) {
                    $returnData = pay_success_logic($OrderData['users_id'], $OrderData['order_code'], $param, 'alipay');
                    if (is_array($returnData)) {
                        if (1 == $returnData['code']) {
                            if (1 == $param['is_notify']) {
                                // 发送邮箱
                                if (!empty($returnData['data']['email']['url'])) {
                                    httpRequest(request()->domain() . $returnData['data']['email']['url'], 'POST', $returnData['data']['email']['data']);
                                }
                                // 发送手机
                                if (!empty($returnData['data']['mobile']['url'])) {
                                    httpRequest(request()->domain() . $returnData['data']['mobile']['url'], 'POST', $returnData['data']['mobile']['data']);
                                }
                                return "success";
                            } else {
                                $retData = [
                                    'code' => 1,
                                    'msg'  => '订单支付完成！',
                                    'url'  => url('user/Shop/shop_centre'),
                                ];
                                return $retData;
                            }
                        }
                    }
                }else{
                    if (1 == $param['is_notify']) {
                        return "success";
                    }else{
                        $msg = [
                            'code' => 1,
                            'msg'  => '订单支付完成！',
                            'url'  => url('user/Shop/shop_centre'),
                        ];
                        return $msg;
                    }
                }
            }
        }

        if (1 == $param['is_notify']) {
            return "fail";
        }else{
            $retData = [
                'code' => 0,
                'msg'  => '订单处理失败，如已确认付款，请联系管理员！',
                'url'  => '',
            ];
            return $retData;
        }
    }

    // 会员充值或升级支付回调处理
    private function MoneyOrderProcessing($param = array(), $order_amount = null)
    {
        if (!empty($param['out_trade_no']) && !empty($param['trade_no'])) {
            // 付款成功
            $MoneyWhere = [
                'lang'         => $this->home_lang,
                // 实际付款金额
                'money'        => $order_amount,
                // 订单号
                'order_number' => $param['out_trade_no'],
            ];
            $MoneyData = $this->users_money_db->where($MoneyWhere)->find();
            // 支付宝订单统一处理
            $msg = $this->MoneyUnifiedProcessing($param, $MoneyData, $order_amount);
            return $msg;
        }

        if (1 == $param['is_notify']) {
            return "fail";
        }else{
            $msg = [
                'code' => 1,
                'msg'  => '订单处理失败，如已确认付款，请联系管理员！',
                'url'  => '',
            ];
            return $msg;
        }
    }
    // 文章购买支付回调处理
    private function ArticleOrderProcessing($param = array(), $order_amount = null)
    {
        if (!empty($param['out_trade_no']) && !empty($param['trade_no'])) {
            $OrderWhere = [
                // 订单号
                'order_code' => $param['out_trade_no'],
                // 实际支付金额
                'order_amount' => $order_amount,
            ];
            $OrderData = $this->article_order_db->where($OrderWhere)->find();
            $ViewUrl = cookie($OrderData['users_id'] . '_' . $OrderData['product_id'] . '_EyouArticleViewUrl');
            if (!empty($OrderData)) {
                // 支付宝付款成功后，订单并未修改状态时，修改订单状态并返回
                if (0 == $OrderData['order_status']) {
                    // 订单更新数据，更新为已付款
                    $OrderData = [
                        'order_status' => 1,
                        'pay_details'  => serialize($param),
                        'pay_time'     => getTime(),
                        'update_time'  => getTime()
                    ];

                    // 订单更新
                    $returnData = $this->article_order_db->where($OrderWhere)->update($OrderData);
                    if (!empty($returnData)) {
                        if (1 == $param['is_notify']) {
                            return "success";
                        } else {
                            $retData = [
                                'code' => 1,
                                'msg'  => '订单支付完成！',
                                'url'  => $ViewUrl,
                            ];
                            return $retData;
                        }
                    }
                }else{
                    if (1 == $param['is_notify']) {
                        return "success";
                    }else{
                        $msg = [
                            'code' => 1,
                            'msg'  => '订单支付完成！',
                            'url'  => $ViewUrl
                        ];
                        return $msg;
                    }
                }
            }
        }

        if (1 == $param['is_notify']) {
            return "fail";
        }else{
            $retData = [
                'code' => 0,
                'msg'  => '订单处理失败，如已确认付款，请联系管理员！',
                'url'  => '',
            ];
            return $retData;
        }
    }

    // 视频购买支付回调处理
    private function MediaOrderProcessing($param = array(), $order_amount = null)
    {
        if (!empty($param['out_trade_no']) && !empty($param['trade_no'])) {
            $OrderWhere = [
                // 订单号
                'order_code' => $param['out_trade_no'],
                // 实际支付金额
                'order_amount' => $order_amount,
            ];
            $OrderData = $this->media_order_db->where($OrderWhere)->find();
            $ViewUrl = cookie($OrderData['users_id'] . '_' . $OrderData['product_id'] . '_EyouMediaViewUrl');
            if (!empty($OrderData)) {
                // 支付宝付款成功后，订单并未修改状态时，修改订单状态并返回
                if (0 == $OrderData['order_status']) {
                    // 订单更新数据，更新为已付款
                    $OrderData = [
                        'order_status' => 1,
                        'pay_details'  => serialize($param),
                        'pay_time'     => getTime(),
                        'update_time'  => getTime()
                    ];

                    $returnData = $this->media_order_db->where($OrderWhere)->update($OrderData);
                    if (!empty($returnData)) {
                        if (1 == $param['is_notify']) {
                            return "success";
                        } else {
                            $retData = [
                                'code' => 1,
                                'msg'  => '订单支付完成！',
                                'url'  => $ViewUrl,
                            ];
                            return $retData;
                        }
                    }
                }else{
                    if (1 == $param['is_notify']) {
                        return "success";
                    }else{
                        $msg = [
                            'code' => 1,
                            'msg'  => '订单支付完成！',
                            'url'  => $ViewUrl
                        ];
                        return $msg;
                    }
                }
            }
        }

        if (1 == $param['is_notify']) {
            return "fail";
        }else{
            $retData = [
                'code' => 0,
                'msg'  => '订单处理失败，如已确认付款，请联系管理员！',
                'url'  => '',
            ];
            return $retData;
        }
    }

    // 下载模型购买支付回调处理
    private function DownloadOrderProcessing($param = array(), $order_amount = null)
    {
        if (!empty($param['out_trade_no']) && !empty($param['trade_no'])) {
            $OrderWhere = [
                // 订单号
                'order_code' => $param['out_trade_no'],
                // 实际支付金额
                'order_amount' => $order_amount,
            ];
            $OrderData = $this->download_order_db->where($OrderWhere)->find();
            $ViewUrl = cookie($OrderData['users_id'] . '_' . $OrderData['product_id'] . '_EyouDownloadViewUrl');
            if (!empty($OrderData)) {
                // 支付宝付款成功后，订单并未修改状态时，修改订单状态并返回
                if (0 == $OrderData['order_status']) {
                    // 订单更新数据，更新为已付款
                    $OrderData = [
                        'order_status' => 1,
                        'pay_details'  => serialize($param),
                        'pay_time'     => getTime(),
                        'update_time'  => getTime()
                    ];

                    // 订单更新
                    $returnData = $this->download_order_db->where($OrderWhere)->update($OrderData);
                    if (!empty($returnData)) {
                        if (1 == $param['is_notify']) {
                            return "success";
                        } else {
                            $retData = [
                                'code' => 1,
                                'msg'  => '订单支付完成！',
                                'url'  => $ViewUrl,
                            ];
                            return $retData;
                        }
                    }
                }else{
                    if (1 == $param['is_notify']) {
                        return "success";
                    }else{
                        $msg = [
                            'code' => 1,
                            'msg'  => '订单支付完成！',
                            'url'  => $ViewUrl
                        ];
                        return $msg;
                    }
                }
            }
        }

        if (1 == $param['is_notify']) {
            return "fail";
        }else{
            $retData = [
                'code' => 0,
                'msg'  => '订单处理失败，如已确认付款，请联系管理员！',
                'url'  => '',
            ];
            return $retData;
        }
    }

    // 支付宝订单处理流程
    // 参数1为支付宝返回数据集
    // 参数2为充值记录表数据集
    // 参数3为订单实际付款金额
    private function MoneyUnifiedProcessing($param, $MoneyData, $PayMoney){
        $referurl = input('param.referurl/s', null, 'urldecode,base64_decode');
        // 支付宝付款成功后，订单并未修改状态时，修改订单状态并返回
        if ($MoneyData['status'] == 1) {
            // 当前时间
            $time = getTime();
            // 更新条件
            $where = [
                'moneyid'  => $MoneyData['moneyid'],
                'users_id' => $MoneyData['users_id'],
            ];
            // 更新数据
            $UpMoneyData = [
               'status'          => 2,
               'pay_method'      => 'alipay',
               'wechat_pay_type' => '',
               'pay_details'     => serialize($param),
               'update_time'     => $time,
            ];
            // 若类型为会员升级则删除订单详情
            if (0 == $MoneyData['cause_type']) unset($UpMoneyData['pay_details']);
            // 执行更新
            $ReturnId = $this->users_money_db->where($where)->update($UpMoneyData);
            if (!empty($ReturnId)) {
                $UpUsersData = [];
                $ReturnId    = '';
                if (1 == $MoneyData['cause_type']) {
                    // 会员充值
                    // 更新会员金额
                    $UpUsersData['users_money'] = Db::raw('users_money+'.$PayMoney);
                    $UpUsersData['update_time'] = $time;
                }else if (0 == $MoneyData['cause_type']) {
                    // 会员升级
                    // 更新会员级别和天数
                    $UpUsersData = $this->GetUsersUpgradeData($MoneyData);
                }

                if (!empty($UpUsersData)) {
                    $ReturnId = $this->users_db->where('users_id',$MoneyData['users_id'])->update($UpUsersData);
                }
                if (!empty($ReturnId)) {
                    if (1 == $MoneyData['cause_type']) {
                        // 业务处理完成，订单已完成
                        $UpMoneyData_ = [
                            'status'      => 3,
                            'update_time' => $time,
                        ];
                        $this->users_money_db->where($where)->update($UpMoneyData_);
                    }
                    if (1 == $param['is_notify']) {
                        return "success";
                    }else{
                        $msg = [
                            'code' => 1,
                            'msg'  => '支付完成',
                            'url'  => !empty($referurl) ? $referurl : url('user/Level/level_centre'),
                        ];
                        return $msg;
                    }
                }
            }
            
            if (1 == $param['is_notify']) {
                return "success";
            }else{
                $msg = [
                    'code' => 0,
                    'msg'  => '支付成功，系统未处理成功，请联系管理员',
                    'url'  => '',
                ];
                return $msg;
            }
        }

        if (1 == $param['is_notify']) {
            return "success";
        }else{
            $msg = [
                'code' => 1,
                'msg'  => '支付完成',
                'url'  => !empty($referurl) ? $referurl : url('user/Level/level_centre'),
            ];
            return $msg;
        }
    }

    // 获取会员升级更新数组
    private function GetUsersUpgradeData($MoneyData = array())
    {
        $time = getTime();
        // 会员期限定义数组
        $limit_arr = Config::get('global.admin_member_limit_arr');
        // 查询会员升级级别
        $MoneyDataCause = unserialize($MoneyData['cause']);
        // 到期天数
        $maturity_days = $limit_arr[$MoneyDataCause['limit_id']]['maturity_days'];
        // 更新会员属性表的数组
        $result = [
            'level'       => $MoneyDataCause['level_id'],
            'update_time' => $time,
            'level_maturity_days' => Db::raw('level_maturity_days+'.($maturity_days)),
        ];

        // 查询会员开通会员级别时间和天数
        $UsersData = $this->users_db->field('open_level_time, level_maturity_days')->find($MoneyData['users_id']);
        // 36600为终身天数，若数据库中的值大于则不执行，反之执行
        if ($UsersData['level_maturity_days'] < '36600') {
            // 计算逻辑，会员开通的时间戳+(会员到期天数*每天的秒数)
            $maturity_time = $UsersData['open_level_time'] + ($UsersData['level_maturity_days'] * 86400);
            // 判断是否到期，到期则执行
            if ($maturity_time < $time) {
                // 会员已到期，追加数组
                $result['open_level_time']     = $time;
                $result['level_maturity_days'] = $maturity_days;
            }
        }

        return $result;
    }

    // 旧版加密方式,验证订单是否正确
    private function GetOldAliPayMd5Sign($param = array(), $code = null)
    {
        // 对关联数组按照键名进行升序排序
        ksort($param);
        reset($param);

        // 去除指定参数并拼装成字符串
        $sign = '';
        foreach ($param as $key => $value)
        {
            if (!in_array($key, ['sign','sign_type','transaction_type','is_notify','m','c','a','referurl']))
            {
                $sign .= "$key=$value&";
            }
        }

        // 参数拼装处理并加密为MD5返回
        $sign = md5(substr($sign, 0, -1).$code);
        return $sign;
    }

    // 新版加密方式,验证订单是否正确
    private function GetNewAliPayRsa2Return($data = array(), $pay_alipay_config = array())
    {
        // 参数拼装
        $config = [
            'app_id'               => $pay_alipay_config['app_id'],
            'charset'              => 'UTF-8',
            'sign_type'            => 'RSA2',
            'gatewayUrl'           => 'https://openapi.alipay.com/gateway.do',
            'alipay_public_key'    => $pay_alipay_config['alipay_public_key'],
            'merchant_private_key' => $pay_alipay_config['merchant_private_key'],
        ];

        // 引入支付宝SDK
        vendor('alipay.pagepay.service.AlipayTradeService');
        // 实例化
        $alipaySevice = new \AlipayTradeService($config);

        // 删除参数
        unset($data['m']);
        unset($data['c']);
        unset($data['a']);
        unset($data['transaction_type']);
        unset($data['is_notify']);
        if (isset($data['referurl'])) {
            unset($data['referurl']);
        }

        // 获取返回值
        $return = $alipaySevice->check($data);
        return $return;
    } 
    /*----------支付宝回调结束----------*/


    /*----------微信回调开始----------*/
    public function wechat_return($GetData = [])
    {
        $OrderData = $this->checkAmount($GetData['out_trade_no'],$GetData['total_amount'],$GetData['transaction_type']);
        if(empty($OrderData)){
            return "支付失败，支付金额与订单金额不相符";
        }
        // 查询订单是否真实已支付
        $PayOrder = $this->WeChatPayOrderInquire($GetData['out_trade_no']);
        
        // 已完成支付则执行下列操作
        if (!empty($PayOrder)) {
            // 拆分自定义参数
            $attach = explode('|,|', $GetData['attach']);

            // 订单查询
            if (1 == $attach[2]) {
                // 会员充值
                $where = [
                    'order_number' => $GetData['out_trade_no']
                ];
                if (!empty($attach[3])) $where['users_id'] = $attach[3];
                // 充值订单查询
                $MoneyData = $this->users_money_db->where($where)->find();
                if (empty($MoneyData['status']) || in_array($MoneyData['status'], [0, 4])) {
                    // 充值订单无需处理，直接返回结束
                    echo 'FAIL'; exit;
                } else if (in_array($MoneyData['status'], [1])) {
                    // 充值订单支付成功后续处理
                    $Result = $this->RechargeProcessing($GetData, $where, $MoneyData);
                    // 返回结束
                    if (!empty($Result)) echo 'SUCCESS'; exit;
                } else {
                    // 充值订单已完成处理，直接返回结束
                    echo 'SUCCESS'; exit;
                }

            } else if (2 == $attach[2]) {
                // 商品购买
                $where = [
                    'order_code' => $GetData['out_trade_no']
                ];
                if (!empty($attach[3])) $where['users_id'] = $attach[3];
                // 购物订单查询
                $OrderStatus = $this->shop_order_db->where($where)->getField('order_status');
                if (empty($OrderStatus) && 0 == $OrderStatus) {
                    // 购物订单支付成功后续处理
                    $Result = $this->ProductPayProcessing($attach[3], $GetData['out_trade_no'], $GetData);
                    // 返回结束
                    if (!empty($Result)) echo 'SUCCESS'; exit;
                } else if (in_array($OrderStatus, [1, 2, 3])) {
                    // 购物订单已完成处理，直接返回结束
                    echo 'SUCCESS'; exit;
                } else {
                    // 购物订单无需处理，直接返回结束
                    echo 'FAIL'; exit;
                }
            } else if (3 == $attach[2]) {
                // 会员升级
                $where = [
                    'order_number' => $GetData['out_trade_no']
                ];
                if (!empty($attach[3])) $where['users_id'] = $attach[3];
                // 升级订单查询
                $MoneyData = $this->users_money_db->where($where)->find();
                if (empty($MoneyData['status']) || in_array($MoneyData['status'], [0, 4])) {
                    // 升级订单无需处理，直接返回结束
                    echo 'FAIL'; exit;
                } else if (in_array($MoneyData['status'], [1])) {
                    // 升级订单支付成功后续处理
                    $Result = $this->UpgradeProcessing($GetData, $where, $MoneyData);
                    // 返回结束
                    if (!empty($Result)) echo 'SUCCESS'; exit;
                } else {
                    // 升级订单已完成处理，直接返回结束
                    echo 'SUCCESS'; exit;
                }

            } else if (8 == $attach[2]) {
                // 视频购买
                $where = [
                    'order_code' => $GetData['out_trade_no']
                ];
                if (!empty($attach[3])) $where['users_id'] = $attach[3];
                // 视频订单查询
                $OrderStatus = $this->media_order_db->where($where)->getField('order_status');
                if (empty($OrderStatus) && 0 == $OrderStatus) {
                    // 视频订单支付成功后续处理
                    $Result = $this->MediaPayProcessing($GetData, $where);
                    // 返回结束
                    if (!empty($Result)) echo 'SUCCESS'; exit;
                } else if (in_array($OrderStatus, [1])) {
                    // 视频订单已完成处理，直接返回结束
                    echo 'SUCCESS'; exit;
                } else {
                    // 视频订单无需处理，直接返回结束
                    echo 'FAIL'; exit;
                }
            }else if (9 == $attach[2]) {
                // 文章购买
                $where = [
                    'order_code' => $GetData['out_trade_no']
                ];
                if (!empty($attach[3])) $where['users_id'] = $attach[3];
                // 文章订单查询
                $OrderStatus = $this->article_order_db->where($where)->getField('order_status');
                if (empty($OrderStatus) && 0 == $OrderStatus) {
                    // 文章订单支付成功后续处理
                    $Result = $this->ArticlePayProcessing($GetData, $where);
                    // 返回结束
                    if (!empty($Result)) echo 'SUCCESS'; exit;
                } else if (in_array($OrderStatus, [1])) {
                    // 视频订单已完成处理，直接返回结束
                    echo 'SUCCESS'; exit;
                } else {
                    // 视频订单无需处理，直接返回结束
                    echo 'FAIL'; exit;
                }
            }else if (10 == $attach[2]) {
                // 下载模型购买
                $where = [
                    'order_code' => $GetData['out_trade_no']
                ];
                if (!empty($attach[3])) $where['users_id'] = $attach[3];
                // 文章订单查询
                $OrderStatus = $this->download_order_db->where($where)->getField('order_status');
                if (empty($OrderStatus) && 0 == $OrderStatus) {
                    // 下载模型订单支付成功后续处理
                    $Result = $this->DownloadPayProcessing($GetData, $where);
                    // 返回结束
                    if (!empty($Result)) echo 'SUCCESS'; exit;
                } else if (in_array($OrderStatus, [1])) {
                    // 视频订单已完成处理，直接返回结束
                    echo 'SUCCESS'; exit;
                } else {
                    // 视频订单无需处理，直接返回结束
                    echo 'FAIL'; exit;
                }
            }else if (99 == $attach[2]){
                // 商品购买
                $where = [
                    'order_code' => $GetData['out_trade_no']
                ];
                if (!empty($attach[3])) $where['users_id'] = $attach[3];
                // 购物订单查询
                $OrderStatus = $this->shop_order_db->where($where)->getField('order_status');
                if (empty($OrderStatus) && 0 == $OrderStatus) {
                    // 购物订单支付成功后续处理
                    $Result = $this->ProductPayProcessing($attach[3], $GetData['out_trade_no'], $GetData);
                    // 返回结束
                    if (!empty($Result)) echo 'SUCCESS'; exit;
                } else if (in_array($OrderStatus, [1, 2, 3])) {
                    // 购物订单已完成处理，直接返回结束
                    echo 'SUCCESS'; exit;
                } else {
                    // 购物订单无需处理，直接返回结束
                    echo 'FAIL'; exit;
                }
            }
        }
    }

    // 查询订单是否真实已支付
    private function WeChatPayOrderInquire($out_trade_no = null)
    {
        $Result = false;

        // 查询微信支付配置
        $where = [
            'pay_id' => 1,
            'pay_mark' => 'wechat'
        ];
        $PayInfo = Db::name('pay_api_config')->where($where)->getField('pay_info');

        // 查询订单是否支付
        if (!empty($out_trade_no) && !empty($PayInfo)) {
            // 引入SDK
            vendor('wechatpay.lib.WxPayApi');
            vendor('wechatpay.lib.WxPayConfig');

            // 实例化加载订单号
            $WxPayOrderQuery  = new \WxPayOrderQuery;
            $WxPayOrderQuery->SetOut_trade_no($out_trade_no);

            // 处理微信配置数据
            $PayInfo = unserialize($PayInfo);
            $ApiConfig['app_id'] = $PayInfo['appid'];
            $ApiConfig['mch_id'] = $PayInfo['mchid'];
            $ApiConfig['key']    = $PayInfo['key'];

            // 实例化微信配置
            $WxPayConfig = new \WxPayConfig($ApiConfig);
            $WxPayApi = new \WxPayApi;

            if (!empty($WxPayConfig->app_id)) {
                // 判断结果
                $WeChatOrder = $WxPayApi->orderQuery($WxPayConfig, $WxPayOrderQuery);
                if (isset($WeChatOrder['return_code']) && $WeChatOrder['return_code'] == 'SUCCESS' && $WeChatOrder['result_code'] == 'SUCCESS') {
                    if ($WeChatOrder['trade_state'] == 'SUCCESS' && !empty($WeChatOrder['transaction_id'])) {
                        $Result = true;
                    }
                }
            }
        }

        // 返回查询结果
        return $Result;
    }

    // 会员充值处理
    private function RechargeProcessing($GetData = [], $Where = [], $MoneyData = [])
    {
        $Return = false;

        // 充值订单更新为已付款
        $data = [
            'status' => 2,
            'update_time' => getTime(),
            'pay_details' => serialize($GetData)
        ];
        // 充值订单更新
        $ResultID = $this->users_money_db->where($Where)->update($data);

        if (!empty($ResultID)) {
            // 同步修改会员的金额
            $UsersWhere = [
                'users_id' => $MoneyData['users_id']
            ];
            $UpUsersData = [
                'users_money' => Db::raw('users_money+'.($MoneyData['money']))
            ];
            $UpdateID = $this->users_db->where($UsersWhere)->update($UpUsersData);

            // 业务处理完成，订单已完成
            if (!empty($UpdateID)) {
                $data2 = [
                    'status' => 3,
                    'update_time' => getTime()
                ];
                $this->users_money_db->where($Where)->update($data2);
                $Return = true;
            }
        }
        // 返回执行结果
        return $Return;
    }

    // 商品订单处理
    private function ProductPayProcessing($users_id = null, $order_code = [], $pay_details = [])
    {
        // 商品订单处理
        $Result = pay_success_logic($users_id, $order_code, $pay_details, 'wechat', false);
        $Return = !empty($Result['code']) && 1 == $Result['code'] ? true : false;

        // 返回执行结果
        return $Return;
    }

    // 会员升级处理
    private function UpgradeProcessing($GetData = [], $Where = [], $MoneyData = [])
    {
        $Return = false;

        // 查询会员升级类型
        $CauseData = unserialize($MoneyData['cause']);
        $UsersTypeData = $this->users_type_manage_db->where('type_id', $CauseData['type_id'])->find();
        if (!empty($UsersTypeData)) {
            // 更新数据
            $UpMoneyData = [
               'status' => 2,
               'update_time' => getTime()
            ];
            // 升级订单更新
            $ResultID = $this->users_money_db->where($Where)->update($UpMoneyData);
            if (!empty($ResultID)) {
                // 更新会员数据
                $Where = [
                    'users_id' => $MoneyData['users_id']
                ];
                $UpUsersData = $this->GetUsersUpgradeData($MoneyData);
                $UpdateID = $this->users_db->where($Where)->update($UpUsersData);
                if (!empty($UpdateID)) $Return = true;
            }
        }
        // 返回执行结果
        return $Return;
    }

    // 会员购买视频处理
    private function MediaPayProcessing($GetData = [], $Where = [])
    {
        // 视频订单更新数据，更新为已付款
        $OrderData = [
            'order_status' => 1,
            'pay_details'  => serialize($GetData),
            'pay_time'     => getTime(),
            'update_time'  => getTime()
        ];

        // 视频订单更新
        $UpdateID = Db::name('media_order')->where($Where)->update($OrderData);
        $Return = !empty($UpdateID) ? true : false;

        // 返回执行结果
        return $Return;
    }
    // 会员购买文章处理
    private function ArticlePayProcessing($GetData = [], $Where = [])
    {
        // 视频订单更新数据，更新为已付款
        $OrderData = [
            'order_status' => 1,
            'pay_details'  => serialize($GetData),
            'pay_time'     => getTime(),
            'update_time'  => getTime()
        ];

        // 视频订单更新
        $UpdateID = $this->article_order_db->where($Where)->update($OrderData);
        $Return = !empty($UpdateID) ? true : false;

        // 返回执行结果
        return $Return;
    }
    // 会员购买下载模型处理
    private function DownloadPayProcessing($GetData = [], $Where = [])
    {
        // 订单更新数据，更新为已付款
        $OrderData = [
            'order_status' => 1,
            'pay_details'  => serialize($GetData),
            'pay_time'     => getTime(),
            'update_time'  => getTime()
        ];

        // 订单更新
        $UpdateID = $this->download_order_db->where($Where)->update($OrderData);
        $Return = !empty($UpdateID) ? true : false;

        // 返回执行结果
        return $Return;
    }
    
    /*----------微信回调结束----------*/
    //检验订单支付金额是否相符
    public function checkAmount($unified_number = '', $unified_amount = 0, $transaction_type = 2)
    {
        $OrderData = [];
        // 商城订单购买支付回调处理
        if (2 === intval($transaction_type)) {
            $OrderData = $this->shop_order_db->where(['order_code' => $unified_number,'order_amount' => $unified_amount])->find();
        }
        // 会员充值或升级支付回调处理
        else if (1 === intval($transaction_type) || 3 == intval($transaction_type)) {
            $OrderData = $this->users_money_db->where(['money' => $unified_amount,'order_number' => $unified_number])->find();
        }
        // 视频购买
        else if (8 === intval($transaction_type)) {
            $OrderData = $this->media_order_db->where(['order_code' => $unified_number,'order_amount' => $unified_amount])->find();
        }
        // 文章购买
        else if (9 === intval($transaction_type)) {
            $OrderData = $this->article_order_db->where(['order_code' =>$unified_number,'order_amount' => $unified_amount])->find();
        }
        // 下载模型购买
        else if (10 === intval($transaction_type)) {
            $OrderData = $this->download_order_db->where(['order_code' =>$unified_number,'order_amount' => $unified_amount])->find();
        }
        // 会员充值套餐购买
        else if (20 === intval($transaction_type)) {
            $OrderData = Db::name('users_recharge_pack_order')->where(['order_pay_code'=>$unified_number, 'order_pay_prices'=>$unified_amount])->find();
        }
        // 多商家商城购买
        else if (99 === intval($transaction_type)) {
            $OrderData = Db::name("shop_order_unified_pay")->where(['unified_number' => $unified_number,'unified_amount' => $unified_amount])->find();
        }
        return $OrderData;
    }
}