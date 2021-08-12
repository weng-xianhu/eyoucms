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

namespace app\admin\model;
use think\Model;
use think\Page;
use think\Config;
use think\Db;

/**
 * 商品退换货服务数据模型
 */
class ShopOrderService extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        // 会员表
        $this->users_db = Db::name('users');
        // 订单主表
        $this->shop_order_db = Db::name('shop_order');
        // 订单明细表
        $this->shop_order_details_db = Db::name('shop_order_details');
        // 订单退换明细表
        $this->shop_order_service_db = Db::name('shop_order_service');
        // 订单退换服务记录表
        $this->shop_order_service_log_db = Db::name('shop_order_service_log');
    }

    // 读取所有退换货服务信息处理返回
    public function GetAllServiceInfo($param = [])
    {   
        // 初始化数组和条件
        $Return  = $Where =[];

        // 订单号查询
        $order_code = $param['order_code'];
        if (!empty($order_code)) $Where['a.order_code'] = array('LIKE', "%{$order_code}%");
        
        $count   = $this->shop_order_service_db->alias('a')->where($Where)->count('service_id');
        $pageObj = new Page($count, config('paginate.list_rows'));

        /*查询退换货订单信息*/
        $Service = $this->shop_order_service_db->alias('a')
            ->field('a.*, b.username')
            ->join('__USERS__ b', 'a.users_id = b.users_id', 'LEFT')
            ->limit($pageObj->firstRow.','.$pageObj->listRows)
            ->where($Where)
            ->order('a.service_id desc')
            ->select();
        $DetailsID = get_arr_column($Service, 'details_id');
        /* END */

        /*查询订单数据*/
        $field_new = 'b.details_id, b.product_price, b.num, a.shipping_fee, a.order_total_num';
        $where_new = [
            'b.apply_service' => 1,
            'b.details_id' => ['IN', $DetailsID]
        ];
        $OrderData = $this->shop_order_db->alias('a')
            ->field($field_new)
            ->join('__SHOP_ORDER_DETAILS__ b', 'a.order_id = b.order_id', 'LEFT')
            ->where($where_new)
            ->getAllWithIndex('details_id');
        /* END */

        $Archives = get_archives_data($Service, 'product_id');
        foreach ($Service as $key => $value) {
            // 商品前台URL
            $Service[$key]['arcurl'] = get_arcurl($Archives[$value['product_id']]);

            /* 计算退还金额 */
            $DetailsData = $OrderData[$value['details_id']];
            $product_total_price = sprintf("%.2f", $DetailsData['product_price'] * (string)$DetailsData['num']);
            $Service[$key]['product_total_price'] = $product_total_price > 0 ? $product_total_price : $Service[$key]['refund_price'];
            
            if (1 == $value['service_type']) {
                $Service[$key]['ShippingFee'] = $Service[$key]['refund_price'] = '0.00';
            } else if (2 == $value['service_type']) {
                // 运费计算
                $ShippingFee = 0;
                if (!empty($DetailsData['shipping_fee'])) {
                    $ShippingFee = sprintf("%.2f", ($DetailsData['shipping_fee'] / (string)$DetailsData['order_total_num']) * (string)$value['product_num']);
                    $Service[$key]['ShippingFee'] = $ShippingFee;
                }
                // 计算退还金额
                $ProductPrice = 0;
                if (!empty($DetailsData['product_price'])) {
                    $ProductPrice = sprintf("%.2f", ($DetailsData['product_price'] * (string)$value['product_num']) - $ShippingFee);
                    $Service[$key]['refund_price'] = $ProductPrice;
                }
            }
            /* END */
        }

        $Return['Service'] = $Service;
        $Return['pageStr'] = $pageObj->show();
        $Return['pageObj'] = $pageObj;

        return $Return;
    }

    public function GetFieldServiceInfo($service_id = null)
    {
        $Return = [];
        if (empty($service_id)) return $Return;
        
        // 退换货信息
        $Service[0] = $this->shop_order_service_db->where('service_id', $service_id)->find();
        $array_new = get_archives_data($Service, 'product_id');
        $Service = $Service[0];

        $Service['arcurl'] = get_arcurl($array_new[$Service['product_id']]);
        $Service['StatusName'] = Config::get('global.order_service_status')[$Service['status']];
        $Service['upload_img'] = explode(',', $Service['upload_img']);
        $Service['product_img'] = handle_subdir_pic(get_default_pic($Service['product_img']));
        $Service['product_spec'] = str_replace("&lt;br/&gt;", " || ", $Service['product_spec']);
        $Service['TypeName'] = Config::get('global.order_service_type')[$Service['service_type']];
        $Service['users_delivery'] = !empty($Service['users_delivery']) ? unserialize($Service['users_delivery']) : '';
        $Service['admin_delivery'] = !empty($Service['admin_delivery']) ? unserialize($Service['admin_delivery']) : '';
        $Service['product_num'] = (string)$Service['product_num'];

        /*用户发货后计算退还金额、余额*/
        if (5 == $Service['status'] || 7 == $Service['status'] || 2 == $Service['service_type']) {
            // 查询订单数据
            $Where = [
                'b.order_id' => $Service['order_id'],
                'b.details_id' => $Service['details_id'],
                'b.apply_service' => 1
            ];
            $Order = $this->shop_order_db->field('a.*, b.*')
                ->alias('a')
                ->where($Where)
                ->join('__SHOP_ORDER_DETAILS__ b', 'a.order_id = b.order_id', 'LEFT')
                ->find();
            $Order['order_total_num'] = (string)$Order['order_total_num'];

            // 运费计算
            $ShippingFee = 0;
            if (!empty($Order['shipping_fee'])) {
                $ShippingFee = sprintf("%.2f", ($Order['shipping_fee'] / $Order['order_total_num']) * $Service['product_num']);
                $Service['ShippingFee'] = $ShippingFee;
            }

            // 退回应付款
            $ProductPrice = 0;
            if (!empty($Order['product_price'])) {
                $ProductPrice = sprintf("%.2f", ($Order['product_price'] * $Service['product_num']) - $ShippingFee);
                $Service['refund_price'] = $ProductPrice;
            }
        } else {
            $Service['refund_price'] = '0.00';
        }
        /* END */

        // 会员信息
        $Users = $this->users_db->field('users_id, username, nickname, mobile')->find($Service['users_id']);
        $Users['nickname'] = empty($Users['nickname']) ? $Users['username'] : $Users['nickname'];

        // 服务记录表信息
        $Log = $this->shop_order_service_log_db->order('log_id desc')->where('service_id', $Service['service_id'])->select();
        foreach ($Log as $key => $value) {
            if (!empty($value['users_id'])) {
                $Log[$key]['name'] = '会员：'.$Users['nickname'];
            } else if (!empty($value['admin_id'])) {
                $Log[$key]['name'] = '商家：'.getAdminInfo(session('admin_id'))['user_name'];
            }
        }

        // 返回
        $Service['shipping_fee']  = $ShippingFee;
        $Return['Log']     = $Log;
        $Return['Users']   = $Users;
        $Return['Service'] = $Service;
        return $Return;
    }
}