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

namespace app\api\model\v1;

use think\Db;
use think\Cache;

/**
 * 微信小程序个人中心模型
 */
load_trait('controller/Jump');

class User extends UserBase
{
    use \traits\controller\Jump;
    private $token;

    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 获取用户信息
     * @param $token
     * @return null|static
     * @throws \think\exception\DbException
     */
    public function getUser($token)
    {
        $users_id = Db::name('wx_users')->where(['openid' => $this->session['openid'], 'users_id' => intval($this->session['users_id'])])->getField('users_id');
        if (empty($users_id)) {
            return false;
        } else {
            $result = GetUsersLatestData($users_id);
            if (!is_http_url($result['head_pic'])) {
                $result['head_pic'] = handle_subdir_pic($result['head_pic'], 'img', true);
            }
            $address_default = []; // 默认收货地址
            $address         = Db::name('shop_address')->where(['users_id' => $users_id])->order('is_default desc')->select(); // 收货地址列表
            if (!empty($address)) {
                foreach ($address as $key => $val) {
                    if ($val['is_default'] == 1) {
                        $address_default = $val;
                        continue;
                    }
                }
            }
            $result['address_1588820149']         = !empty($address) ? $address : [];
            $result['address_default_1588820149'] = $address_default;
        }

        return $result;
    }

    /**
     * 用户登录
     * @param array $post
     * @return string
     * @throws BaseException
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function login($post)
    {
        // 微信登录 获取session_key
        $session = $this->wxlogin($post['code']);
        // 自动注册用户
        $userInfo = json_decode($post['user_info'], true);
        $users_id = $this->register($session['openid'], $userInfo);
        if (!empty($users_id)) {
            Db::name('users')->where('users_id', $users_id)->update([
                'last_ip'     => clientIP(),
                'last_login'  => getTime(),
                'login_count' => Db::raw('login_count+1'),
            ]);
        }
        // 生成token (session3rd)
        $this->token = $this->token($session['openid'], $session['session_key'], $users_id);

        return $users_id;
    }

    /**
     * 获取token
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * 微信登录
     * @param $code
     * @return array|mixed
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    private function wxlogin($code)
    {
        $weapp_data = Db::name('weapp')->where('code','OpenMinicode')->find();
        if (empty($weapp_data)) {
            $this->error('请在云插件库在线安装【开源微信小程序(企业版)】插件！');
        } else if (1 != $weapp_data['status']){
            $this->error('请启用【开源微信小程序(企业版)】插件！');
        } else if (empty($weapp_data['data'])){
            $this->error('请配置【开源微信小程序(企业版)插件！');
        }

        $inc =  json_decode($weapp_data['data'],true);
        // 微信登录 (获取session_key)
        $session = $this->wxUserSessionKey($code, $inc);
        if (isset($session['errcode'])) {
            $this->error($session['errmsg']);
        }
        return $session;
    }

    /**
     * 获取微信登录的session_key
     * @param $code
     * @return array|mixed
     */
    private function wxUserSessionKey($code, $inc)
    {
        /**
         * code 换取 session_key
         * ​这是一个 HTTPS 接口，开发者服务器使用登录凭证 code 获取 session_key 和 openid。
         * 其中 session_key 是对用户数据进行加密签名的密钥。为了自身应用安全，session_key 不应该在网络上传输。
         */
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$inc['appid']}&secret={$inc['secret']}&js_code={$code}&grant_type=authorization_code";
        $response = httpRequest($url);
        $params   = json_decode($response, true);
        if (empty($params)) {
            $params = [
                'errcode' => "-1",
                'errmsg'  => '系统繁忙',
            ];
        }
        return $params;
    }

    /**
     * 生成用户认证的token
     * @param $openid
     * @return string
     */
    private function token($openid, $session_key, $users_id)
    {
        // 随机串
        $randstr1 = get_rand_str(8, 0, 1);
        // 随机串
        $randstr2 = get_rand_str(8, 0, 0);
        // 自定义一个盐
        $salt = '_token_salt';
        // 用户认证的token
        $token = "{$randstr1}eyoucms{$users_id}eyoucms{$openid}eyoucms{$randstr2}eyoucms{$session_key}eyoucms{$salt}";

        return mchStrCode($token, 'ENCODE', '#!@diyminipro#!$');
    }

    /**
     * 自动注册用户
     * @param $openid
     * @param $userInfo
     * @return mixed
     * @throws \Exception
     * @throws \think\exception\DbException
     */
    private function register($openid, $userInfo)
    {
        // 查询用户是否已存在
        $we_user = Db::name('wx_users')->field('users_id')->where(['openid' => $openid])->find();
        if (empty($we_user)) {
            $users_id = $this->setReg($userInfo);
            if (!empty($users_id)) {
                //微信用户信息存在表里
                $wxuser_id = Db::name('wx_users')->insertGetId([
                    'users_id'   => $users_id,
                    'openid'     => $openid,
                    'nickname'   => filterNickname($userInfo['nickName']),
                    'headimgurl' => $userInfo['avatarUrl'],
                    'add_time'   => getTime(),
                ]);
                if (!empty($wxuser_id)) {
                    return $users_id;
                } else {
                    Db::name('users')->where(['users_id' => $users_id])->delete();
                }
            }
            $this->error('用户注册失败！');
        } else {
            $users = Db::name('users')->field('users_id')->where([
                'users_id' => $we_user['users_id'],
            ])->find();
            if (empty($users)) {
                $users_id = $this->setReg($userInfo);
                if (!empty($users_id)) {
                    Db::name('wx_users')->where(['openid' => $openid])->update([
                        'users_id'    => $users_id,
                        'update_time' => getTime(),
                    ]);
                    return $users_id;
                } else {
                    $this->error('用户注册失败！');
                }
            } else {
                return $we_user['users_id'];
            }
        }
    }

    /**
     * 自动注册users表用户
     */
    private function setReg($userInfo)
    {
        // 生成用户名
        $username = $this->createUsername();
        // 用户昵称
        $nickname = filterNickname($userInfo['nickName']);
        // 创建用户账号
        $addData  = [
            'username'            => $username,//用户名-生成
            'nickname'            => !empty($nickname) ? trim($nickname) : $username,//昵称，同微信用户名
            'level'               => 1,
            'thirdparty'          => 3,
            'register_place'      => 2,
            'open_level_time'     => getTime(),
            'level_maturity_days' => 0,
            'reg_time'            => getTime(),
            'head_pic'            => !empty($userInfo['avatarUrl']) ? $userInfo['avatarUrl'] : ROOT_DIR . '/public/static/common/images/dfboy.png',
            'lang'                => self::$lang,
        ];
        $users_id = Db::name('users')->insertGetId($addData);

        return $users_id;
    }

    /**
     * 生成用户名，确保唯一性
     */
    private function createUsername()
    {
        $username = 'EY' . get_rand_str(6, 0, 1);
        $username = strtoupper($username);
        $count    = Db::name('users')->where('username', $username)->count();
        if (!empty($count)) {
            return $this->createUsername();
        }

        return $username;
    }

    /**
     * 个人中心菜单列表
     * @return array
     */
    public function getMenus()
    {
        $menus = [
            // 'address' => [
            //     'name' => '收货地址',
            //     'url' => 'pages/address/index',
            //     'icon' => 'map'
            // ],
            // 'coupon' => [
            //     'name' => '领券中心',
            //     'url' => 'pages/coupon/coupon',
            //     'icon' => 'lingquan'
            // ],
            // 'my_coupon' => [
            //     'name' => '我的优惠券',
            //     'url' => 'pages/user/coupon/coupon',
            //     'icon' => 'youhuiquan'
            // ],
            // 'sharing_order' => [
            //     'name' => '拼团订单',
            //     'url' => 'pages/sharing/order/index',
            //     'icon' => 'pintuan'
            // ],
            // 'my_bargain' => [
            //     'name' => '我的砍价',
            //     'url' => 'pages/bargain/index/index?tab=1',
            //     'icon' => 'kanjia'
            // ],
            // 'dealer' => [
            //     'name' => '分销中心',
            //     'url' => 'pages/dealer/index/index',
            //     'icon' => 'fenxiaozhongxin'
            // ],
            // 'help' => [
            //     'name' => '我的帮助',
            //     'url' => 'pages/user/help/index',
            //     'icon' => 'help'
            // ],
        ];
        // 判断分销功能是否开启
        // if (DealerSettingModel::isOpen()) {
        //     $menus['dealer']['name'] = DealerSettingModel::getDealerTitle();
        // } else {
        //     unset($menus['dealer']);
        // }
        return $menus;
    }

    /**
     * 返回状态给微信服务器
     * @param boolean $returnCode
     * @param string $msg
     */
    public function returnCode($returnCode = true, $msg = null)
    {
        // 返回状态
        $return = [
            'return_code' => $returnCode ? 'SUCCESS' : 'FAIL',
            'return_msg'  => $msg ?: 'OK',
        ];

        // 记录日志
        $value = [
            'describe' => '返回微信支付状态',
            'data'     => $return
        ];
        $msg   = is_string($value) ? $value : var_export($value, true);
        \think\Log::record($msg, $type);

        die($this->toXml($return));
    }

    /**
     * 格式化参数格式化成url参数
     * @param $values
     * @return string
     */
    public function toUrlParams($values)
    {
        $buff = '';
        foreach ($values as $k => $v) {
            if ($k != 'sign' && $v != '' && !is_array($v)) {
                $buff .= $k . '=' . $v . '&';
            }
        }
        return trim($buff, '&');
    }

    /**
     * 输出xml字符
     * @param $values
     * @return bool|string
     */
    public function toXml($values)
    {
        if (!is_array($values)
            || count($values) <= 0
        ) {
            return false;
        }

        $xml = "<xml>";
        foreach ($values as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 将xml转为array
     * @param $xml
     * @return mixed
     */
    public function fromXml($xml)
    {
        // 禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    /**
     * 生成签名
     * @param $values
     * @return string 本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    public function makeSign($values, $apikey = '')
    {
        if (empty($apikey)) {
            $diyminiproInfo = model('DiyminiproMall')->detail();
            $apikey         = $diyminiproInfo['apikey'];
        }

        //签名步骤一：按字典序排序参数
        ksort($values);
        $string = $this->toUrlParams($values);
        //签名步骤二：在string后加入KEY
        $string = $string . '&key=' . $apikey;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    public function onPaySuccess($order, $payType = 20, $payData = [])
    {
        if (empty($order)) {
            return [
                'code' => 0,
                'msg'  => '未找到该订单信息',
            ];
        }
        // 更新付款状态
        $status = $this->updatePayStatus($order, $payType, $payData);

        return $status;
    }

    /**
     * 更新付款状态
     * @param $payType
     * @param array $payData
     * @return bool
     */
    private function updatePayStatus($order, $payType, $payData = [])
    {
        // 验证余额支付时用户余额是否满足
        if ($payType == 10) {
            $users_money = Db::name('users')->where(['users_id' => $this->users_id])->getField('users_money');
            if (strval($users_money) < strval($order['order_amount'])) {
                return [
                    'code' => 0,
                    'msg'  => '用户余额不足，无法使用余额支付',
                ];
            }
        }
        // 更新订单状态
        $Result = $this->updateOrderInfo($order, $payType, $payData);

        return $Result;
    }

    /**
     * 更新订单记录
     * @param $payType
     * @param $payData
     * @return false|int
     * @throws \Exception
     */
    private function updateOrderInfo($order, $payType, $payData)
    {
        $Result = [];

        $OrderWhere = [
            'order_id'   => $order['order_id'],
            'order_code' => $payData['out_trade_no'],
        ];
        // 修改会员金额明细表中，对应的订单数据，存入返回的数据，订单已付款
        $OrderData = [
            'order_status' => 1,
            // 'pay_name'     => 'wechat', //微信支付
            'pay_details'  => serialize($payData),
            'pay_time'     => getTime(),
            'update_time'  => getTime(),
        ];
        $r         = Db::name('shop_order')->where($OrderWhere)->update($OrderData);

        if (!empty($r)) {

            // 添加订单操作记录
            AddOrderAction($order['order_id'], $order['users_id'], '0', '1', '0', '1', '支付成功！', '会员使用微信小程序完成支付！');

            // $users = Db::name('users')->find($order['users_id']);

            // 邮箱发送
            // $SmtpConfig = tpCache('smtp');
            // $Result['email'] = GetEamilSendData($SmtpConfig, $users, $order, 1, 'wechat');

            // 手机发送
            // $SmsConfig = tpCache('sms');
            // $Result['mobile'] = GetMobileSendData($SmsConfig, $users, $order, 1, 'wechat');

            $Result['status'] = 1;
        }

        return $Result;
    }

    //获取收藏列表
    public function GetMyCollectList($param = [])
    {
        $orderby  = !empty($orderby) ? $orderby : 'id desc';
        $page     = !empty($param['page']) ? intval($param['page']) : 1;
        $pagesize = empty($param['pagesize']) ? config('paginate.list_rows') : $param['pagesize'];

        $paginate = ['page' => $page];
        $pages     = Db::name('users_collection')
            ->where('users_id', $this->users_id)
            ->where('aid', '>', 0)
            ->orderRaw($orderby)
            ->paginate($pagesize, false, $paginate);

        $result = $pages->toArray();

        foreach ($result['data'] as $key => $val) {
            $val['litpic'] = $this->get_default_pic($val['litpic']); // 默认封面图
            $val['add_time'] = date('Y-m-d H:i:s',$val['add_time']);
            $val['update_time'] = date('Y-m-d H:i:s',$val['update_time']);
            $result['data'][$key] = $val;
        }
        return $result;
    }
}