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
 * Date: 2020-05-25
 */

namespace think\template\taglib\eyou;

use think\Config;
use think\Request;
use think\Db;

/**
 * 支付API列表
 */
load_trait('controller/Jump');
class TagSppayapilist extends Base
{ 
    use \traits\controller\Jump;

    /**
     * 会员ID
     */
    public $users_id = 0;
    public $users    = [];
    
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        // 会员信息
        $this->users    = session('users');
        $this->users_id = session('users_id');
        $this->users_id = !empty($this->users_id) ? $this->users_id : 0;
    }

    /**
     * 获取提交订单数据
     */
    public function getSppayapilist()
    {
        // 接收数据读取解析
        $Paystr = input('param.paystr/s');
        $PayData = cookie($Paystr);

        if (!empty($PayData['moneyid']) && !empty($PayData['order_number'])) {
            // 充值信息
            $money_id = !empty($PayData['moneyid']) ? intval($PayData['moneyid']) : 0;
            $money_code = !empty($PayData['order_number']) ? $PayData['order_number'] : '';
        } else if (!empty($PayData['order_id']) && !empty($PayData['order_code'])) {
            // 订单信息
            $order_id   = !empty($PayData['order_id']) ? intval($PayData['order_id']) : 0;
            $order_code = !empty($PayData['order_code']) ? $PayData['order_code'] : '';
        }
        
        $JsonData['unified_id']       = '';
        $JsonData['unified_amount']   = '';
        $JsonData['unified_number']   = '';
        $JsonData['transaction_type'] = 3; // 交易类型，3为会员升级

        $Result = [];
        if (is_array($PayData) && (!empty($order_id) || !empty($money_id)) && (!empty($money_code) || !empty($order_code))) {
            $Result = [];
            if (!empty($money_id)) {
                // 获取会员充值信息
                $where = [
                    'moneyid'      => $money_id,
                    'order_number' => $money_code,
                    'users_id'     => $this->users_id,
                    'lang'         => $this->home_lang
                ];
                $Result = Db::name('users_money')->where($where)->find();
                if (empty($Result)) $this->error('订单不存在或已变更', url('user/Pay/pay_consumer_details'));

                // 组装数据返回
                $JsonData['transaction_type'] = 1; // 交易类型，1为充值
                $JsonData['unified_id']       = $Result['moneyid'];
                $JsonData['unified_amount']   = $Result['money'];
                $JsonData['unified_number']   = $Result['order_number'];

            } else if (!empty($order_id)) {
                if (!empty($PayData['type']) && 8 == $PayData['type']) {
                    // 获取支付订单
                    $where = [
                        'order_id'   => $order_id,
                        'order_code' => $order_code,
                        'users_id'   => $this->users_id,
                        'lang'       => $this->home_lang
                    ];
                    $Result = Db::name('media_order')->where($where)->find();
                    if (empty($Result)) $this->error('订单不存在或已变更', url('user/Media/index'));
                    
                    $url = url('user/Media/index');
                    if (in_array($Result['order_status'], [1])) $this->error('订单已支付，即将跳转！', $url);

                    // 组装数据返回
                    $JsonData['transaction_type'] = 8; // 交易类型，8为购买视频
                    $JsonData['unified_id']       = $Result['order_id'];
                    $JsonData['unified_amount']   = $Result['order_amount'];
                    $JsonData['unified_number']   = $Result['order_code'];

                }else if (!empty($PayData['type']) && 9 == $PayData['type']) {
                    // 获取支付订单
                    $where = [
                        'order_id'   => $order_id,
                        'order_code' => $order_code,
                        'users_id'   => $this->users_id,
                        'lang'       => $this->home_lang
                    ];
                    $Result = Db::name('article_order')->where($where)->find();
                    if (empty($Result)) $this->error('订单不存在或已变更', url('user/Article/index'));

                    $url = url('user/Article/index');
                    if (in_array($Result['order_status'], [1])) $this->error('订单已支付，即将跳转！', $url);

                    // 组装数据返回
                    $JsonData['transaction_type'] = 9; // 交易类型，8为购买视频
                    $JsonData['unified_id']       = $Result['order_id'];
                    $JsonData['unified_amount']   = $Result['order_amount'];
                    $JsonData['unified_number']   = $Result['order_code'];

                } else {
                    // 获取支付订单
                    $where = [
                        'order_id'   => $order_id,
                        'order_code' => $order_code,
                        'users_id'   => $this->users_id,
                        'lang'       => $this->home_lang
                    ];
                    $Result = Db::name('shop_order')->where($where)->find();
                    if (empty($Result)) $this->error('订单不存在或已变更', url('user/Shop/shop_centre'));
                    
                    // 判断订单状态，1已付款(待发货)，2已发货(待收货)，3已完成(确认收货)，-1订单取消(已关闭)，4订单过期
                    $url = urldecode(url('user/Shop/shop_order_details', ['order_id' => $Result['order_id']]));
                    if (in_array($Result['order_status'], [1, 2, 3])) {
                        $this->error('订单已支付，即将跳转', $url);
                    } elseif ($Result['order_status'] == 4) {
                        $this->error('订单已过期，即将跳转', $url);
                    } elseif ($Result['order_status'] == -1) {
                        $this->error('订单已关闭，即将跳转', $url);
                    }

                    // 组装数据返回
                    $JsonData['transaction_type'] = 2; // 交易类型，2为购买
                    $JsonData['unified_id']       = $Result['order_id'];
                    $JsonData['unified_amount']   = $Result['order_amount'];
                    $JsonData['unified_number']   = $Result['order_code'];
                    
                }
                
            }
        }

        $where = [
            'status' => 1,
        ];
        if ((isMobile() && isWeixin()) || isWeixinApplets()) $where['pay_mark'] = ['NEQ', 'alipay'];
        $PayApiList = Db::name('pay_api_config')->where($where)->select();

        if (!empty($PayApiList)) {
            foreach ($PayApiList as $key => $value) {
                $PayApiList[$key]['pay_img'] = '';
                $PayApiList[$key]['hidden'] = '';
                $PayInfo = unserialize($value['pay_info']);
                if ('wechat' == $value['pay_mark']) {
                    if (!empty($PayInfo['is_open_wechat'])) {
                        $r1 = $this->findHupijiaoIsExis('wechat');
                        if ($r1 == true) unset($PayApiList[$key]);
                    } 
                    /*
                    if (0 == $PayInfo['is_open_wechat']) {
                        if (empty($PayInfo['appid']) || empty($PayInfo['mchid']) || empty($PayInfo['key'])) {
                            $r1 = $this->findHupijiaoIsExis('wechat');
                            if ($r1 == true) unset($PayApiList[$key]);
                        }
                    } else {
                        $r1 = $this->findHupijiaoIsExis('wechat');
                        if ($r1 == true) unset($PayApiList[$key]);
                    }
                    */
                } else if ('alipay' == $value['pay_mark']) {
                    if (!empty($PayInfo['is_open_alipay'])) {
                        $r1 = $this->findHupijiaoIsExis('alipay');
                        if ($r1 == true) unset($PayApiList[$key]);
                    }
                    /*
                    if (0 == $PayInfo['is_open_alipay']) {
                        if (version_compare(PHP_VERSION,'5.5.0','<')) {
                            // 旧版支付宝
                            if (empty($PayInfo['account']) || empty($PayInfo['code']) || empty($PayInfo['id'])) {
                                $r1 = $this->findHupijiaoIsExis('alipay');
                                if ($r1 == true) unset($PayApiList[$key]);
                            }
                        } else {
                            if (1 == $PayInfo['version']) {
                                // 旧版支付宝
                                if (empty($PayInfo['account']) || empty($PayInfo['code']) || empty($PayInfo['id'])) {
                                    $r1 = $this->findHupijiaoIsExis('alipay');
                                    if ($r1 == true) unset($PayApiList[$key]);
                                }
                            } else {
                                // 新版支付宝
                                if (empty($PayInfo['app_id']) || empty($PayInfo['merchant_private_key']) || empty($PayInfo['alipay_public_key'])) {
                                    $r1 = $this->findHupijiaoIsExis('alipay');
                                    if ($r1 == true) unset($PayApiList[$key]);
                                }
                            }
                        }
                    } else {
                        $r1 = $this->findHupijiaoIsExis('alipay');
                        if ($r1 == true) unset($PayApiList[$key]);
                    }
                    */
                } else if (0 == $value['system_built']) {
                    if (0 == $PayInfo['is_open_pay']) {
                        foreach ($PayInfo as $kk => $vv) {
                            if ('is_open_pay' != $kk && empty($vv)) {
                                unset($PayApiList[$key]); break;
                            }
                        }
                    } else {
                        unset($PayApiList[$key]);
                    }
                    if (!empty($PayApiList[$key])) {
                        $PayApiList[$key]['pay_img'] = get_default_pic('/weapp/'.$value['pay_mark'].'/pay.png');
                    }
                }
            }
        }
        $PayApiList = array_merge([], $PayApiList);

        // 传入JS参数
        $JsonData['IsMobile']        = isMobile() ? 1 : 0;
        $JsonData['PayDealWith']     = url('user/Pay/pay_deal_with', ['_ajax' => 1], true, false, 1, 1, 0);
        $JsonData['SelectPayMethod'] = url('user/PayApi/select_pay_method', ['_ajax' => 1], true, false, 1, 1, 0);
        $JsonData['OrderPayPolling'] = url('user/PayApi/order_pay_polling', ['_ajax' => 1], true, false, 1, 1, 0);
        $JsonData['UsersUpgradePay'] = url('user/PayApi/users_upgrade_pay', ['_ajax' => 1], true, false, 1, 1, 0);
        $JsonData['get_token_url']   = url('api/Ajax/get_token', ['name'=>'__token__'], true, false, 1, 1, 0);
        $JsonData = json_encode($JsonData);
        $version   = getCmsVersion();
        $usersTplVersion = getUsersTplVersion();
        $jsfile = ('v2' == $usersTplVersion) ? 'tag_sppayapilist_v2.js' : 'tag_sppayapilist.js';
        // 循环中第一个数据带上JS代码加载
        if (!empty($PayApiList)) {
            foreach ($PayApiList as $key => $value) {
                if (!empty($value)) {
                    $PayApiList[$key]['hidden'] = <<<EOF
<script type="text/javascript">
    var eyou_data_json_1590627847 = {$JsonData};
</script>
<script type="text/javascript" src="{$this->root_dir}/public/static/common/js/{$jsfile}?v={$version}"></script>
EOF;
                break;
                }
            }
        } else {
            $PayApiList[0]['hidden'] = <<<EOF
<script type="text/javascript">
    var eyou_data_json_1590627847 = {$JsonData};
</script>
<script type="text/javascript" src="{$this->root_dir}/public/static/common/js/{$jsfile}?v={$version}"></script>
EOF;
        }

        return $PayApiList;
    }
}