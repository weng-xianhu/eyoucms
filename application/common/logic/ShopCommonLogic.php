<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 易而优团队 by 陈风任 <491085389@qq.com>
 * Date: 2021-01-14
 */

namespace app\common\logic;
use think\Model;
use think\Db;

/**
 * 商城公共逻辑业务层
 * @package common\Logic
 */
class ShopCommonLogic extends Model
{
    /**
     * 初始化操作
     */
    public function initialize() {
        parent::initialize();
        $this->users_db              = Db::name('users');               // 会员数据表
        $this->users_money_db        = Db::name('users_money');         // 会员余额明细表
        $this->shop_order_db         = Db::name('shop_order');          // 订单主表
        $this->shop_order_details_db = Db::name('shop_order_details');  // 订单明细表
    }

    public function GetOrderStatusIfno($Where = array())
    {   
        // 查询订单信息
        if (empty($Where)) return '条件错误！';
        $OrderData = $this->shop_order_db->where($Where)->field('order_code, use_balance, use_point, order_status')->find();
        if (empty($OrderData)) return '订单不存在！';

        // 返回处理
        switch ($OrderData['order_status']) {
            case '-1':
                return '订单已取消，不可取消订单！';
            break;
            
            case '1':
                return '订单已支付，不可取消订单！';
            break;

            case '2':
                return '订单已发货，不可取消订单！';
            break;

            case '3':
                return '订单已完成，不可取消订单！';
            break;

            case '4':
                return '订单已过期，不可取消订单！';
            break;

            default:
                // 订单仅在未支付时返回数组
                return $OrderData;
            break;
        }
    }

    public function UpdateUsersProcess($GetOrder = array(), $GetUsers = array())
    {
        // 如果没有传入会员信息则获取session
        $Users = $GetUsers ? $GetUsers : session('users');
        // 若数据为空则返回false
        if (empty($Users) || empty($GetOrder)) return false;
        // 当前时间
        $time = getTime();

        /*返还余额支付的金额*/
        if (!empty($GetOrder['use_balance']) && $GetOrder['use_balance'] > 0) {
            $UsersMoney['users_money'] = Db::raw('users_money+'.($GetOrder['use_balance']));
            $this->users_db->where('users_id', $Users['users_id'])->update($UsersMoney);

            /*使用余额支付时，同时添加一条记录到金额明细表*/
            $AddMoneyData = [
                'users_id'     => $Users['users_id'],
                'money'        => $GetOrder['use_balance'],
                'users_old_money' => $Users['users_money'],
                'users_money'  => $Users['users_money'] + $GetOrder['use_balance'],
                'cause'        => '订单取消，退还使用余额，订单号：' . $GetOrder['order_code'],
                'cause_type'   => 2,
                'status'       => 3,
                'order_number' => $GetOrder['order_code'],
                'add_time'     => $time,
                'update_time'  => $time,
            ];
            $this->users_money_db->add($AddMoneyData);
            /* END */
        }
        /* END */
    }

    // 前后台通过，记录商品退换货服务单信息
    public function AddOrderServiceLog($param = [], $IsUsers = 1)
    {
        if (empty($param)) return false;
        if (2 == $param['status']) {
            $LogNote = '商家通过申请，等待会员将货物寄回商家！';
        } else if (3 == $param['status']) {
            $LogNote = '商家拒绝申请，请联系商家处理！';
        } else if (4 == $param['status']) {
            $LogNote = '会员已将货物发出，等待商家收货！';
        } else if (5 == $param['status']) {
            $LogNote = '商家已收到货物，待管理员进行退换货处理！';
        } else if (6 == $param['status']) {
            $LogNote = '商家已将新货物重新发出，换货完成，服务结束！';
        } else if (7 == $param['status']) {
            $LogNote = '商家已将金额、余额、积分退回，退款完成，服务结束！';
        } else if (8 == $param['status']) {
            $LogNote = '服务单被取消，服务结束！';
        }
        if (1 == $IsUsers) {
            $users_id = $param['users_id'];
            $admin_id = 0;
        } else {
            $admin_id = session('admin_id');
            $users_id = 0;
        }
        OrderServiceLog($param['service_id'], $param['order_id'], $users_id, $admin_id, $LogNote);
    }
}