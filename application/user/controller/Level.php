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
 * Date: 2019-6-21
 */
 
namespace app\user\controller;

use think\Db;
use think\Config;
use think\Page;

class Level extends Base
{
    // 初始化
    public function _initialize() {
        parent::_initialize();
        // 会员数据表
        $this->users_db              = Db::name('users');
        // 会员金额明细表
        $this->users_money_db        = Db::name('users_money');
        // 会员级别表
        $this->users_level_db        = Db::name('users_level');
        // 会员等级管理表
        $this->users_type_manage_db  = Db::name('users_type_manage');
        // 商城微信配置信息
        $this->pay_wechat_config = '';
        $where = [
            'pay_id' => 1,
            'pay_mark' => 'wechat'
        ];
        $PayInfo = Db::name('pay_api_config')->where($where)->getField('pay_info');
        if (!empty($PayInfo)) $this->pay_wechat_config = unserialize($PayInfo);

        // 商城支付宝配置信息
        $this->pay_alipay_config = '';
        $where = [
            'pay_id' => 2,
            'pay_mark' => 'alipay'
        ];
        $PayInfo = Db::name('pay_api_config')->where($where)->getField('pay_info');
        if (!empty($PayInfo)) $this->pay_alipay_config = unserialize($PayInfo);

        // 判断PHP版本信息
        if (version_compare(PHP_VERSION,'5.5.0','<')) {
            $this->php_version = 1; // PHP5.5.0以下版本，可使用旧版支付方式
        }else{
            $this->php_version = 0; // PHP5.5.0以上版本，可使用新版支付方式，兼容旧版支付方式
        }

        // 支付功能是否开启
        $redirect_url = '';
        $pay_open = getUsersConfigData('pay.pay_open');
        $web_users_switch = tpCache('web.web_users_switch');
        if (empty($pay_open)) { 
            // 支付功能关闭，立马跳到会员中心
            $redirect_url = url('user/Users/index');
            $msg = '支付功能尚未开启！';
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
        }
        // --end
    }

    // 等级管理列表
    public function level_centre()
    {
        // 查询升级产品分类表
        $users_type = $this->users_type_manage_db->order('sort_order asc')->select();
        $this->assign('users_type',$users_type);

        // 会员期限
        $member_limit_arr = Config::get('global.admin_member_limit_arr');
        foreach($member_limit_arr as $key => $value){
            // 下标从 1 开始，重组数组，$key初始为 1 
            $member_limit_arr[$key] = $value['limit_name'];
        }
        $this->assign('member_limit_arr',$member_limit_arr);

        // 查询订单号
        $OrderNumber = $this->GetMoneyData('order_number');
        $this->assign('OrderNumber',$OrderNumber);

        // 是否开启微信支付方式
        $is_open_wechat = 1;
        if (!empty($this->pay_wechat_config)) {
            $is_open_wechat = !empty($this->pay_wechat_config['is_open_wechat']) ? $this->pay_wechat_config['is_open_wechat'] : 0;
        } else {
            $where = [
                'pay_id' => 1,
                'pay_mark' => 'wechat'
            ];
            $PayInfo = Db::name('pay_api_config')->where($where)->getField('pay_info');
            if (!empty($PayInfo)) {
                $wechat = unserialize($PayInfo);
                $is_open_wechat = !empty($wechat['is_open_wechat']) ? $wechat['is_open_wechat'] : 0;
            }
        }
        $this->assign('is_open_wechat', $is_open_wechat);

        // 是否开启支付宝支付方式
        $is_open_alipay = 1;
        if (!empty($this->pay_alipay_config)) {
            $is_open_alipay = !empty($this->pay_alipay_config['is_open_alipay']) ? $this->pay_alipay_config['is_open_alipay'] : 0;
        } else {
            $where = [
                'pay_id' => 2,
                'pay_mark' => 'alipay'
            ];
            $PayInfo = Db::name('pay_api_config')->where($where)->getField('pay_info');
            if (!empty($PayInfo)) {
                $alipay = unserialize($PayInfo);
                $is_open_alipay = !empty($alipay['is_open_wechat']) ? $alipay['is_open_wechat'] : 0;
            }
        }
        $this->assign('is_open_alipay', $is_open_alipay);

        $result = [];
        // 菜单名称
        $result['title'] = Db::name('users_menu')->where([
                'mca'  => 'user/Level/level_centre',
                'lang' => $this->home_lang,
            ])->getField('title');

        /*余额开关*/
        $pay_balance_open = getUsersConfigData('pay.pay_balance_open');
        if (!is_numeric($pay_balance_open) && empty($pay_balance_open)) {
            $pay_balance_open = 1;
        }
        $result['pay_balance_open'] = $pay_balance_open;
        /*end*/

        $eyou = array(
            'field' => $result,
        );
        $this->assign('eyou', $eyou);

        return $this->fetch('users/level_centre');
    }

    // 升级支付处理
    public function level_type_pay()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 是否选择产品
            if (empty($post['type_id'])) $this->error('请选择购买产品！');
            // 是否选择支付方式
            if (empty($post['pay_id']))  $this->error('请选择支付方式！');
            // 查询会员升级选择的数据
            $UsersTypeData = $this->users_type_manage_db->where('type_id',$post['type_id'])->find();
            // 查询提交过来级别等级值
            $LevelValue = $this->users_level_db->where('level_id',$UsersTypeData['level_id'])->getField('level_value');
            // 查询当前会员等级值
            $UsersValue = $this->users_level_db->where('level_id',$this->users['level'])->getField('level_value');
            // 提交的等级是否比现有等级高
            if ($UsersValue > $LevelValue) $this->error('选择升级的等级不可以比目前持有的等级低');

            if (!empty($UsersTypeData)) {
                // 将查询数据存入 session，微信和支付宝回调时需要查询数据
                session('UsersTypeData',$UsersTypeData);
            }
            // 判断支付方式
            switch ($post['pay_id']) {
                // 余额支付
                case '1':
                    $this->BalancePayment($UsersTypeData, $post['order_number']);
                    break;
                
                // 微信支付
                case '2':
                    // 是否配置微信支付信息
                    if (empty($this->pay_wechat_config)) {
                        $WechatMsg = '微信支付配置尚未配置完成。<br/>请前往基本信息-支付接口-微信支付<br/>填入收款的微信支付配置信息！';
                        $this->error($WechatMsg);
                    }else{
                        $this->WeChatPayment($UsersTypeData, $post['order_number']);
                    }
                    break;

                // 支付宝支付
                case '3':
                    // 是否配置支付宝支付信息
                    if (empty($this->pay_alipay_config)) {
                        $AlipayMsg = '支付宝支付配置尚未配置完成。<br/>请前往基本信息-支付接口-支付宝支付<br/>填入收款的支付宝支付配置信息！';
                        $this->error($AlipayMsg);
                    }else{
                        $this->AliPayPayment($UsersTypeData, $post['order_number']);
                    }
                    break;
            }
        }
    }

    // 余额支付
    private function BalancePayment($UsersTypeData = array(), $order_number = null)
    {
        if (!empty($UsersTypeData)) {
            $UsersMoney = $this->users_db->where('users_id',$this->users_id)->getField('users_money');
            if ($UsersMoney < $UsersTypeData['price']) {
                // 若会员余额不足支付则返回
                $ReturnData = $this->GetReturnData();
                $this->success($ReturnData);
            }else{
                if (!empty($order_number)) {
                    // 获取更新金额明细表数据数组
                    $UpMoneyData = $this->GetUpMoneyData($UsersTypeData);
                    $ReturnId    = $this->users_money_db->where('order_number',$order_number)->update($UpMoneyData);
                }else{
                    // 获取生成的订单信息
                    $AddMoneyData = $this->GetAddMoneyData($UsersTypeData);
                    // 存入会员金额明细表
                    $ReturnId     = $this->users_money_db->add($AddMoneyData);
                }

                if (!empty($ReturnId)) {
                    $Where = [
                        'users_id' => $this->users_id,
                        'lang'     => $this->home_lang,
                    ];
                    // 获取更新会员数据数组
                    $UpUsersData = $this->GetUpUsersData($UsersTypeData, true);
                    $ReturnId    = $this->users_db->where($Where)->update($UpUsersData);
                    if (!empty($ReturnId)) {
                        // 支付完成返回
                        $ReturnData = $this->GetReturnData(1, 1, '余额支付完成！', url('user/Level/level_centre'));
                        $this->success($ReturnData);
                    }
                }
            }
        }else{
            $this->error('商品不存在！');
        }
    }

    // 微信支付
    private function WeChatPayment($UsersTypeData = array(), $order_number = null)
    {
        if (!empty($UsersTypeData)) {
            // 读取购买会员升级订单信息
            $MoneyData = $this->GetMoneyData('*', $order_number);
            if (isMobile() && !isWeixin()) {
                // 手机浏览器端支付
                $this->WeChatUnifiedPay($MoneyData, $UsersTypeData, 'WeChatH5');
            } else if (isMobile() && isWeixin()) {
                // 手机微信端支付
                $this->WeChatUnifiedPay($MoneyData, $UsersTypeData, 'WeChatInternal');
            } else {
                // PC端扫码支付
                $this->WeChatUnifiedPay($MoneyData, $UsersTypeData, 'WeChatScanCode');
            }
        }else{
            $this->error('商品不存在！');
        }
    }

    // 微信统一支付处理
    private function WeChatUnifiedPay($MoneyData = array(), $UsersTypeData = array(), $PayType = null)
    {
        if (empty($PayType)) $this->error('微信支付客户端异常，请更换设备支付！');

        if (empty($MoneyData)) {
            // 获取生成的订单信息
            $AddMoneyData = $this->GetAddMoneyData($UsersTypeData, 'wechat', 1);
            // 存入会员金额明细表
            $ReturnId = $this->users_money_db->add($AddMoneyData);
            if (!empty($ReturnId)) {
                // 返回订单数据
                $AddMoneyData['moneyid'] = $ReturnId;
                $this->ReturnMoneyPayData($AddMoneyData);
            }
        }else{
            $MoneyDataCause = unserialize($MoneyData['cause']);
            if ($MoneyDataCause['level_id'] == $UsersTypeData['level_id'] && $MoneyData['money'] == $UsersTypeData['price'] && $MoneyData['wechat_pay_type'] == $PayType) {
                // 提交的订单与上一次是同一类型产品，直接返回数据
                $this->ReturnMoneyPayData($MoneyData);
            }else{
                // 生成新订单覆盖原来的订单返回
                $UpMoneyData = $this->GetUpMoneyData($UsersTypeData, $PayType);
                $UpMoneyData['status'] = 1;
                $UpMoneyData['order_number'] = date('Ymd').getTime().rand(10,100);
                $this->users_money_db->where('moneyid',$MoneyData['moneyid'])->update($UpMoneyData);
                // 返回订单数据
                $UpMoneyData['moneyid'] = $MoneyData['moneyid'];
                $this->ReturnMoneyPayData($UpMoneyData);
            }
        }
    }

    // 处理微信订单支付信息并加载回页面
    private function ReturnMoneyPayData($MoneyData = array())
    {
        if (empty($MoneyData)) $this->error('订单生成错误，请刷新后重试~');
        // 订单信息
        $ReturnOrderData = [
            'unified_id'         => $MoneyData['moneyid'],
            'unified_number'     => $MoneyData['order_number'],
            'transaction_type'   => 1, // 订单支付购买
            'order_total_amount' => $MoneyData['money'],
        ];
        if (isMobile() && !isWeixin()) {
            // 手机浏览器端支付
            $out_trade_no = $MoneyData['order_number'];
            if (empty($out_trade_no)) {
                $this->error('支付异常，请刷新后重试~');
            }
            $total_fee    = $MoneyData['money'];
            if (empty($total_fee)) {
                $this->error('支付异常，请刷新后重试~');
            }
            $url          = model('Pay')->getMobilePay($out_trade_no,$total_fee);
            if (isset($url['return_code']) && 'FAIL' == $url['return_code']) {
                $this->error('商户公众号尚未成功开通H5支付，请开通成功后重试~');
            }
        } else if (isMobile() && isWeixin()) {
            // 手机微信端支付
            if (empty($this->users['open_id'])) {
                // 如果会员没有openid则使用扫码支付方式
                $arrayData = [
                    'unified_id'       => $MoneyData['moneyid'],
                    'unified_number'   => $MoneyData['order_number'],
                    'transaction_type' => 1,
                    'level_pay'        => true,
                ];
                $url = url('user/Pay/pay_method', $arrayData);
            }
        } else {
            // PC端
            $arrayData = [
                'unified_id'       => $MoneyData['moneyid'],
                'unified_number'   => $MoneyData['order_number'],
                'transaction_type' => 1,
                'level_pay'        => true,
            ];
            $url = url('user/Pay/pay_method', $arrayData);
        }

        $ReturnData = $this->GetReturnData(2, 0, '订单生成！', $url, $MoneyData['order_number']);
        $this->success($ReturnData, null, $ReturnOrderData);
    }

    // 支付宝支付
    private function AliPayPayment($UsersTypeData = array(), $order_number = null)
    {
        if (!empty($UsersTypeData)) {
            $MoneyData = $this->GetMoneyData('*', $order_number);
            if (empty($MoneyData)) {
                // 获取生成的订单信息
                $AddMoneyData = $this->GetAddMoneyData($UsersTypeData, 'alipay', 1);
                // 存入会员金额明细表
                $ReturnId = $this->users_money_db->add($AddMoneyData);
                if (!empty($ReturnId)) {
                    // 支付宝处理返回信息
                    $this->AliPayProcessing($AddMoneyData);
                }
            }else{
                // 获取生成的订单信息
                $UpMoneyData = $this->GetUpMoneyData($UsersTypeData, 'alipay');
                $UpMoneyData['status'] = 1;
                // 更新会员金额明细表
                $ReturnId = $this->users_money_db->where('moneyid',$MoneyData['moneyid'])->update($UpMoneyData);
                if (!empty($ReturnId)) {
                    // 支付宝处理返回信息
                    $MoneyData = $this->GetMoneyData('*', $order_number);
                    $this->AliPayProcessing($MoneyData);
                }
            }
        }else{
            $this->error('商品不存在！');
        }
    }

    // 支付宝订单处理逻辑
    private function AliPayProcessing($MoneyData = array())
    {
        // 返回订单数据
        $AliPayUrl = '';
        // 支付宝支付所需参数信息拼装
        $Data = [
            'unified_number' => $MoneyData['order_number'],
            'unified_amount' => $MoneyData['money'],
            'transaction_type' => 1,
        ];
        if ($this->php_version == 1) {
            // 低于5.5版本，仅可使用旧版支付宝支付
            $AliPayUrl = model('Pay')->getOldAliPayPayUrl($Data, $this->pay_alipay_config);
        }else if($this->php_version == 0){
            // 高于或等于5.5版本，可使用新版支付宝支付
            if (empty($this->pay_alipay_config['version'])) {
                // 新版
                $AliPayUrl = url('user/Pay/newAlipayPayUrl',$Data);
            }else if($this->pay_alipay_config['version'] == 1){
                // 旧版
                $AliPayUrl = model('Pay')->getOldAliPayPayUrl($Data, $this->pay_alipay_config);
            }
        }
        if (!empty($AliPayUrl)) {
            $ReturnData = $this->GetReturnData(3, 0, '订单生成！', $AliPayUrl, $Data['unified_number']);
        }else{
            $this->error('数据异常，请联系管理员！');
        }
        $this->success($ReturnData);
    }

    // 拼装订单数组
    private function GetAddMoneyData($UsersTypeData = array(), $pay_method = 'balance', $status = 2, $details = '')
    {
        $wechat_pay_type = '';
        if ('balance' == $pay_method) {
            $pay_method_new = '余额';
        } else if ('alipay' == $pay_method) {
            $pay_method_new = '支付宝';
        } else if ('wechat' == $pay_method) {
            if (isMobile() && !isWeixin()) {
                // 手机浏览器端支付
                $wechat_pay_type = 'WeChatH5';
            } else if (isMobile() && isWeixin()) {
                // 手机微信端支付
                $wechat_pay_type = 'WeChatInternal';
            } else {
                // PC端扫码支付
                $wechat_pay_type = 'WeChatScanCode';
            }
            $pay_method_new = '微信';
        }

        $details = '会员当前级别为【'.$this->users['level_name'].'】，使用'.$pay_method_new.'支付【 '.$UsersTypeData['type_name'].'】，支付金额为'.$UsersTypeData['price'];

        $time = getTime();
        // 拼装数组存入会员购买等级表
        $AddMoneyData = [
            'users_id'     => $this->users_id,
            // 订单生成规则
            'order_number' => date('Ymd') . $time . rand(10,100),
            // 金额
            'money'        => $UsersTypeData['price'],
            // 购买的产品等级ID(level_id)
            'cause'        => serialize($UsersTypeData),
            // 购买消费标记
            'cause_type'   => 0,
            // 支付状态，默认2为支付完成
            'status'       => $status,
            // 支付方式，默认余额支付
            'pay_method'   => $pay_method,
            'wechat_pay_type' => $wechat_pay_type,
            // 支付详情
            'pay_details'  => serialize($details),
            'lang'         => $this->home_lang,
            'add_time'     => $time,
            'update_time'  => $time,
        ];

        return $AddMoneyData;
    }

    // 拼装更新金额明细表数据数组
    private function GetUpMoneyData($data = array(), $pay_method = 'balance')
    {
        $wechat_pay_type = '';
        if ('balance' == $pay_method) {
            $pay_method_new = '余额';
        } else if ('alipay' == $pay_method) {
            $pay_method_new = '支付宝';
        } else {
            $wechat_pay_type= $pay_method;
            $pay_method_new = '微信';
            $pay_method     = 'wechat';
        }

        $details = '会员当前级别为【'.$this->users['level_name'].'】，使用'.$pay_method_new.'支付【 '.$data['type_name'].'】，支付金额为'.$data['price'];

        $result = [
            'cause'           => serialize($data),
            'money'           => $data['price'],
            'status'          => 2,
            'pay_method'      => $pay_method,
            'wechat_pay_type' => $wechat_pay_type,
            'pay_details'     => serialize($details),
            'update_time'     => getTime(),
        ];

        return $result;
    }

    // 拼装返回数组
    private function GetReturnData($ReturnCode = 1, $ReturnPay = 0, $ReturnMsg = null, $ReturnUrl = null, $ReturnOrder = null)
    {
        if (empty($ReturnUrl)) { $ReturnUrl = url('user/Pay/pay_account_recharge'); }
        if (1 == $ReturnCode && empty($ReturnMsg)) { $ReturnMsg = '余额不足，若要使用余额支付，请先充值！'; }
        $ReturnData = [
            // 返回判断支付类型，1为余额支付，2为微信支付，3为支付宝支付
            'ReturnCode' => $ReturnCode,
            // 返回判断是否已支付，0为未支付，1为完成支付
            'ReturnPay'  => $ReturnPay,
            // 返回提示的信息
            'ReturnMsg'  => $ReturnMsg,
            // 返回跳转的链接
            'ReturnUrl'  => $ReturnUrl,
            // 支付订单号
            'ReturnOrder'=> $ReturnOrder,
        ];

        // 微信支付才需要的返回字段
        if ('2' == $ReturnCode) {
            if (isMobile() && !isWeixin()) {
                // 手机浏览器端支付
                $ReturnData['WeChatType'] = 'WeChatH5';
            } else if (isMobile() && isWeixin()) {
                // 手机微信端支付
                $ReturnData['WeChatType'] = 'WeChatInternal';
            } else {
                // PC端扫码支付
                $ReturnData['WeChatType'] = 'WeChatScanCode';
            }
        }

        return $ReturnData;
    }

    // 拼装更新会员数据数组
    private function GetUpUsersData($data = array(), $balance = false)
    {
        $time = getTime();
        // 会员期限定义数组
        $limit_arr = Config::get('global.admin_member_limit_arr');
        // 到期天数
        $maturity_days = $limit_arr[$data['limit_id']]['maturity_days'];
        // 更新会员属性表的数组
        $result = [
            'level' => $data['level_id'],
            'update_time' => $time,
            'level_maturity_days' => Db::raw('level_maturity_days+'.($maturity_days)),
        ];

        // 如果是余额支付则追加数组
        if (!empty($balance)) {
            $result['users_money'] = Db::raw('users_money-'.($data['price']));
        }
        
        // 判断是否需要追加天数，maturity_code在Base层已计算，1表示终身会员天数
        if (1 != $this->users['maturity_code']) {
            // 判断是否到期，到期则执行，3表示会员在期限内，不需要进行下一步操作
            if (3 != $this->users['maturity_code']) {
                // 追加天数数组
                $result['open_level_time']     = $time;
                $result['level_maturity_days'] = $maturity_days;
            }
        }

        // // 选择天数和价格较高的级别作为最终更新级别
        // $a1 = $limit_arr[$data['limit_id']]['maturity_days'];
        // // 会员当前天数
        // $a2 = $this->users_type_manage_db->where('level_id',$this->users['level'])->field('price,limit_id')->find();
        // $a2['days'] = $limit_arr[$a2['limit_id']]['maturity_days'];
        // // 若会员已经有级别的天数和价格都低于会员将要购买的级别则追加会员当前选择的会员级别
        // if ($a2['days'] < $a1 && $a2['price'] < $data['price']) {
        //     $result['level'] = $data['level_id'];
        // }
        return $result;
    }

    // 查询
    // field  字段信息，若不传入则默认值为*
    // 值为*：find方式查询，查询所有字段，返回一维数组
    // 值为多个：find方式查询，查询指定字段，返回一维数组
    // 值为单个：getField方式查询，返回单个字段值
    // return 返回查询结果
    private function GetMoneyData($field = '*', $order_number = null)
    {
        $data = [];

        // 查询条件
        $where = [
            'users_id'   => $this->users_id,
            'cause_type' => 0, // 消费类型
            'status'     => 1, // 未付款状态
            'lang'       => $this->home_lang,
        ];

        // 若存在则执行
        if (!empty($order_number)) $where['order_number'] = $order_number;

        // 查询数据
        if ('*' == $field) {
            // 查询所有字段
            $data = $this->users_money_db->where($where)->find();
        } else {
            $info = explode(',', $field);
            if (1 < count($info)) {
                // 查询指定的多个字段
                $data = $this->users_money_db->where($where)->field($field)->find();
            } else {
                // 查询指定的单个字段
                $data = $this->users_money_db->where($where)->getField($field);
            }
        }
        return $data;
    }

    // 微信订单查询
    public function wechat_order_inquiry()
    {
        if (IS_POST) {
            $unified_number   = input('post.unified_number/s');
            $transaction_type = input('post.transaction_type/s');
            $is_applets_pay   = input('post.is_applets_pay/s');

            if(!empty($unified_number)){
                // ajax异步查询订单是否完成并处理相应逻辑返回。
                vendor('wechatpay.lib.WxPayApi');
                vendor('wechatpay.lib.WxPayConfig');

                // 实例化加载订单号
                $input  = new \WxPayOrderQuery;
                $input->SetOut_trade_no($unified_number);

                // 处理微信配置数据
                $config_data['app_id'] = $this->pay_wechat_config['appid'];
                $config_data['mch_id'] = $this->pay_wechat_config['mchid'];
                $config_data['key']    = $this->pay_wechat_config['key'];

                // 若为小程序接入则执行
                if (1 == $is_applets_pay) {
                    $MiniproValue = Db::name('weapp_minipro0002')->where('type', 'minipro')->getField('value');
                    $MiniproValue = !empty($MiniproValue) ? json_decode($MiniproValue, true) : [];
                    $config_data['app_id'] = !empty($MiniproValue) ? $MiniproValue['appId'] : null;
                }

                // 实例化微信配置
                $config = new \WxPayConfig($config_data);
                $wxpayapi = new \WxPayApi;
                if (empty($config->app_id)) $this->error('微信支付配置尚未配置完成。');
                
                // 返回结果
                $result = $wxpayapi->orderQuery($config, $input);

                // 业务处理
                if (isset($result['return_code']) && $result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS'){
                    if ($result['trade_state'] == 'SUCCESS' && !empty($result['transaction_id'])) {
                        if (1 == $transaction_type) {
                            // 支付成功
                            $Where = [
                                'order_number' => $result['out_trade_no'],
                            ];
                            $MoneyData = $this->users_money_db->where($Where)->find();
                            if (empty($MoneyData)) $this->error('支付异常，请刷新页面后重试');

                            // 微信支付成功后，订单并未修改状态时，修改订单状态并返回
                            if ($MoneyData['status'] == 1) {
                                // 更新条件
                                $Where = [
                                    'moneyid'  => $MoneyData['moneyid'],
                                    'users_id' => $MoneyData['users_id'],
                                ];
                                // 获取更新金额明细表数据数组
                                $UpMoneyData = $this->GetUpMoneyData(session('UsersTypeData'), $MoneyData['wechat_pay_type']);
                                $ReturnId = $this->users_money_db->where($Where)->update($UpMoneyData);
                                if (!empty($ReturnId)) {
                                    $Where = [
                                        'users_id' => $MoneyData['users_id'],
                                    ];
                                    // 获取更新会员数据数组
                                    $UpUsersData = $this->GetUpUsersData(session('UsersTypeData'));
                                    $ReturnId = $this->users_db->where($Where)->update($UpUsersData);
                                    if (!empty($ReturnId)) {
                                        $url = url('user/Level/level_centre');
                                        $this->success('支付成功，请勿刷新，即将跳转', $url, ['status'=>1]);    
                                    } else {
                                        $this->success('支付成功，升级更新失败，请联系管理员', null, ['status'=>2]);
                                    }
                                } else {
                                    $this->success('支付成功，数据更新失败，请联系管理员', null, ['status'=>2]);
                                }
                            }

                            if ($MoneyData['status'] == 2 && !empty($MoneyData['pay_details'])) {
                                // 订单已支付
                                $url = url('user/Level/level_centre');
                                $this->success('订单已支付，即将跳转，请勿刷新', $url, ['status'=>1]);
                            }
                        }
                    } else if ($result['trade_state'] == 'NOTPAY') {
                        // 支付中
                        $this->success('微信已扫码，正在支付中，请勿刷新', null, ['status'=>0]);
                    }
                } else {
                    // 支付中
                    $this->success('订单号：'.$unified_number.'，正在支付中', null, ['status'=>0]);
                }
            }
        }
    }
}
