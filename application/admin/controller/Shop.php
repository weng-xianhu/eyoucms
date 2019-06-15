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
 * Date: 2019-03-26
 */

namespace app\admin\controller;

use think\Page;
use think\Db;
use think\Config;
use app\admin\logic\ShopLogic;

class Shop extends Base {

    private $UsersConfigData = [];

    /**
     * 构造方法
     */
    public function __construct(){
        parent::__construct();
        $this->users_db              = Db::name('users');                   // 用户信息表
        $this->shop_order_db         = Db::name('shop_order');              // 订单主表
        $this->shop_order_details_db = Db::name('shop_order_details');      // 订单明细表
        $this->shop_address_db       = Db::name('shop_address');            // 收货地址表
        $this->shop_express_db       = Db::name('shop_express');            // 物流名字表
        $this->shop_order_log_db  = Db::name('shop_order_log');             // 订单操作表
        $this->shipping_template_db  = Db::name('shop_shipping_template');  // 运费模板表

        // 会员中心配置信息
        $this->UsersConfigData = getUsersConfigData('all');
        $this->assign('userConfig',$this->UsersConfigData);
    }

    /**
     * 商城设置
     */
    public function conf(){
        if (IS_POST) {
            $post = input('post.');
            if (!empty($post)) {
                foreach ($post as $key => $val) {
                    getUsersConfigData($key, $val);
                }
                $this->success('设置成功！');
            }
        }

        // 商城配置信息
        $ConfigData = getUsersConfigData('shop');
        $this->assign('Config',$ConfigData);
        return $this->fetch('conf');
    }

    /**
     *  订单列表
     */
    public function index()
    {
        // 初始化数组和条件
        $list  = array();
        $Where = [
            'lang'   => $this->admin_lang,
        ];
        // 订单号查询
        $order_code = input('order_code/s');
        if (!empty($order_code)) {
            $Where['order_code'] = array('LIKE', "%{$order_code}%");
        }
        // 订单状态查询
        $order_status = input('order_status/s');
        if (!empty($order_status)) {
            $Where['order_status'] = $order_status;
        }
        // 查询满足要求的总记录数
        $count = $this->shop_order_db->where($Where)->count('order_id');
        // 实例化分页类 传入总记录数和每页显示的记录数
        $pageObj = new Page($count, config('paginate.list_rows'));
        // 订单主表数据查询
        $list = $this->shop_order_db->where($Where)
            ->order('order_id desc')
            ->limit($pageObj->firstRow.','.$pageObj->listRows)
            ->select();
        // 分页显示输出
        $pageStr = $pageObj->show();
        // 获取订单状态
        $admin_order_status_arr = Config::get('global.admin_order_status_arr');
        // 获取订单方式名称
        $pay_method_arr = Config::get('global.pay_method_arr');
        // 订单状态筛选数组
        $OrderStatus = array(
            0 => array(
                'order_status' => '1',
                'status_name'  => '待发货',
            ),
            1 => array(
                'order_status' => '2',
                'status_name'  => '已发货',
            ),
            2 => array(
                'order_status' => '3',
                'status_name'  => '已完成',
            ),
        );
        // 数据加载
        $this->assign('pageObj', $pageObj);
        $this->assign('list', $list);
        $this->assign('pageStr', $pageStr);
        $this->assign('admin_order_status_arr',$admin_order_status_arr);
        $this->assign('pay_method_arr',$pay_method_arr);
        $this->assign('OrderStatus', $OrderStatus);

        /*检测是否存在订单中心模板*/
        if ('v1.0.1' > getVersion('version_themeshop') && !empty($this->UsersConfigData['shop_open'])) {
            $is_syn_theme_shop = 1;
        } else {
            $is_syn_theme_shop = 0;
        }
        $this->assign('is_syn_theme_shop',$is_syn_theme_shop);
        /*--end*/

        return $this->fetch();
    }

    /**
     *  订单详情
     */
    public function order_details()
    {
        $order_id = input('param.order_id');
        if (!empty($order_id)) {
            // 查询订单信息
            $this->GetOrderData($order_id);
            // 查询订单操作记录
            $Action = $this->shop_order_log_db->where('order_id',$order_id)->order('action_id desc')->select();
            // 操作记录数据处理
            foreach ($Action as $key => $value) {
                if ('0' == $value['action_user']) {
                    // 若action_user为0，表示会员操作，根据订单号中的ID获取会员名。
                    $username = $this->users_db->field('username')->where('users_id',$value['users_id'])->find();
                    $Action[$key]['username'] = '会 &nbsp; 员: '.$username['username'];
                }else{
                    // 若action_user不为0，表示管理员操作，根据ID获取管理员名。
                    $user_name = Db::name('admin')->field('user_name')->where('admin_id',$value['action_user'])->find();
                    $Action[$key]['username'] = '管理员: '.$user_name['user_name'];
                }

                // 操作时，订单发货状态
                $Action[$key]['express_status'] = '未发货';
                if ('1' == $value['express_status']) {
                    $Action[$key]['express_status'] = '已发货';
                }

                // 操作时，订单付款状态
                $Action[$key]['pay_status'] = '未支付';
                if ('1' == $value['pay_status']) {
                    $Action[$key]['pay_status'] = '已支付';
                }
            }

            $this->assign('Action', $Action);
            return $this->fetch('order_details');
        }else{
            $this->error('非法访问！');
        }
    }

    /**
     *  订单发货
     */
    public function order_send()
    {
        $order_id = input('param.order_id');
        if ($order_id) {
            // 查询订单信息
            $this->GetOrderData($order_id);
            return $this->fetch('order_send');
        }
    }

    /**
     *  订单发货操作
     */
    public function order_send_operating()
    {
        if (IS_POST) {
            $post = input('post.');
            // 条件数组
            $Where = [
                'order_id'   => $post['order_id'],
                'users_id'   => $post['users_id'],
                'lang'       => $this->admin_lang,
            ];

            // 更新数组
            $UpdateData = [
                'order_status'  => 2,
                'express_order' => $post['express_order'],
                'express_name'  => $post['express_name'],
                'express_code'  => $post['express_code'],
                'express_time'  => getTime(),
                'consignee'     => $post['consignee'],
                'update_time'   => getTime(),
                'note'          => $post['note'],
                'virtual_delivery' => $post['virtual_delivery'],
            ];
            
            // 订单操作记录逻辑
            $LogWhere = [
                'order_id'       => $post['order_id'],
                'express_status' => 1,
            ];
            $LogData   = $this->shop_order_log_db->where($LogWhere)->count();
            if (!empty($LogData)) {
                // 数据存在则表示为修改发货内容
                $OrderData = $this->shop_order_db->where($Where)->field('prom_type')->find();
                $Desc = '修改发货内容！';
                if ('1' == $post['prom_type']) {
                    // 提交的数据为虚拟订单
                    if ($OrderData['prom_type'] != $post['prom_type']) {
                        // 此处判断后，提交的订单类型和数据库中的订单类型不相同，表示普通订单修改为虚拟订单
                        $Note = '管理员将普通订单修改为虚拟订单！';
                        if (!empty($post['virtual_delivery'])) {
                            // 若存在数据则拼装
                            $Note .= '给买家回复：'.$post['virtual_delivery'];
                        }
                    }else{
                        // 继续保持为虚拟订单修改
                        $Note = '虚拟订单，无需物流。';
                        if (!empty($post['virtual_delivery'])) {
                            // 若存在数据则拼装
                            $Note .= '给买家回复：'.$post['virtual_delivery'];
                        }
                    }
                }else{
                    // 提交的数据为普通订单
                    if ($OrderData['prom_type'] != $post['prom_type']) {
                        // 这一段暂时无用，因为发货时，暂时无法选择将虚拟订单修改为普通订单
                        $Note = '管理员将虚拟订单修改为普通订单！';
                        if (!empty($post['virtual_delivery'])) {
                            // 若存在数据则拼装
                            $Note .= '给买家回复：'.$post['virtual_delivery'];
                        }
                    }else{
                        // 继续保持为普通订单修改
                        $Note = '使用'.$post['express_name'].'发货成功！';
                    }
                }
                $UpdateData['prom_type'] = $post['prom_type'];
            }else{
                // 数据不存在则表示为初次发货，拼装发货内容
                $Desc = '发货成功！';
                $Note = '使用'.$post['express_name'].'发货成功！';
                if ('1' == $post['prom_type']) {
                    // 若为虚拟订单，无需发货物流。
                    $UpdateData['prom_type'] = $post['prom_type'];
                    $Note = '虚拟订单，无需物流。';
                    if (!empty($post['virtual_delivery'])) {
                        // 若存在数据则拼装
                        $Note .= '给买家回复：'.$post['virtual_delivery'];
                    }
                }
            }

            if (empty($post['prom_type']) && empty($post['express_order'])) {
                $this->error('配送单号不能为空！');
            }

            // 更新订单主表信息
            $IsOrder = $this->shop_order_db->where($Where)->update($UpdateData);
            if (!empty($IsOrder)) {
                // 更新订单明细表信息
                $Data['update_time'] = getTime();
                $this->shop_order_details_db->where('order_id',$post['order_id'])->update($Data);
                // 添加订单操作记录
                AddOrderAction($post['order_id'],'0',session('admin_id'),'2','1','1',$Desc,$Note);
                $this->success('发货成功');
            } else {
                $this->error('发货失败');
            }
        }
    }

    /**
     * 查询快递名字及Code
     */
    public function order_express()
    {
        $ExpressData = array();
        $Where = array();
        $keywords = input('keywords/s');
        if (!empty($keywords)) {
            $Where['express_name'] = array('LIKE', "%{$keywords}%");
        }

        $count = $this->shop_express_db->where($Where)->count('express_id');// 查询满足要求的总记录数
        $pageObj = new Page($count, '10');// 实例化分页类 传入总记录数和每页显示的记录数
        $ExpressData = $this->shop_express_db->where($Where)
            ->order('sort_order asc,express_id asc')
            ->limit($pageObj->firstRow.','.$pageObj->listRows)
            ->select();

        $pageStr = $pageObj->show(); 
        $this->assign('ExpressData', $ExpressData);
        $this->assign('pageStr', $pageStr);
        $this->assign('pageObj', $pageObj);
        return $this->fetch('order_express');
    }

    /**
     *  管理员后台标记订单状态
     */
    public function order_mark_status()
    {
        if (IS_POST) {
            $post = input('post.');
            // 条件数组
            $Where = [
                'order_id' => $post['order_id'],
                'users_id' => $post['users_id'],
                'lang'     => $this->admin_lang,
            ];

            if ('ddsc' == $post['status_name']) {
                // 订单删除
                $IsDelete = $this->shop_order_db->where($Where)->delete();
                if (!empty($IsDelete)) {
                    // 同步删除订单下的产品
                    $this->shop_order_details_db->where($Where)->delete();
                    // 同步删除订单下的操作记录
                    $this->shop_order_log_db->where($Where)->delete();
                    $this->success('删除成功！');
                }else{
                    $this->error('数据错误！');
                }
            }else{
                $OrderData = $this->shop_order_db->where($Where)->find();

                // 更新数组
                $UpdateData = [
                    'update_time'  => getTime(),
                ];

                // 根据不同操作标记不同操作内容
                if ('yfk' == $post['status_name']) {
                    // 订单标记为付款，追加更新数组
                    $UpdateData['order_status'] = '1';
                    $UpdateData['pay_time']     = getTime();
                    // 管理员付款
                    $UpdateData['pay_name']     = 'admin_pay';

                    /*用于添加订单操作记录*/
                    $order_status   = '1'; // 订单状态
                    $express_status = '0'; // 发货状态
                    $pay_status     = '1'; // 支付状态
                    $action_desc    = '付款成功！'; // 操作明细
                    $action_note    = '管理员确认订单付款！'; // 操作备注
                    /*结束*/

                }else if ('ysh' == $post['status_name']) {
                    // 订单确认收货，追加更新数组
                    $UpdateData['order_status'] = '3';
                    $UpdateData['confirm_time'] = getTime();

                    /*用于添加订单操作记录*/
                    $order_status   = '3'; // 订单状态
                    $express_status = '1'; // 发货状态
                    $pay_status     = '1'; // 支付状态
                    $action_desc    = '确认收货！'; // 操作明细
                    $action_note    = '管理员确认订单已收货！'; // 操作备注
                    /*结束*/

                }else if ('gbdd' == $post['status_name']) {
                    // 订单关闭，追加更新数组
                    $UpdateData['order_status'] = '-1';

                    /*用于添加订单操作记录*/
                    $order_status = '-1'; // 订单状态
                    if ('0' == $OrderData['order_status'] || '1' == $OrderData['order_status']) {
                        $express_status = '0'; // 发货状态
                        $pay_status     = '0'; // 支付状态
                    }else{
                        $express_status = '1'; // 发货状态
                        $pay_status     = '1'; // 支付状态
                    }
                    $action_desc  = '订单关闭！'; // 操作明细
                    $action_note  = '管理员关闭订单！'; // 操作备注
                    /*结束*/
                }

                // 更新订单主表
                $IsOrder = $this->shop_order_db->where($Where)->update($UpdateData);
                if (!empty($IsOrder)) {
                    // 更新订单明细表
                    $Data['update_time'] = getTime();
                    $this->shop_order_details_db->where('order_id',$post['order_id'])->update($Data);

                    // 添加订单操作记录
                    AddOrderAction($post['order_id'],'0',session('admin_id'),$order_status,$express_status,$pay_status,$action_desc,$action_note);

                    $this->success('操作成功！');
                }
            }
        }else{
            $this->error('非法访问！');
        }
    }

    /*
     *  更新管理员备注
     */
    public function update_note()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            if (!empty($post['order_id'])) {
                $UpdateData = [
                    'admin_note'  => $post['admin_note'],
                    'update_time' => getTime(),
                ];
                $return = $this->shop_order_db->where('order_id',$post['order_id'])->update($UpdateData);
                if (!empty($return)) {
                    $this->success('保存成功！');
                }
            }else{
                $this->error('非法访问！');
            }
        }else{
            $this->error('非法访问！');
        }
    }

    /*
     *  运费模板列表
     */
    public function shipping_template()
    {
        $Where = [
            'a.level' => 1,
        ];

        $region_name = input('param.region_name');
        if (!empty($region_name)) {
            $Where['a.name'] = $region_name;
        }

        // 省份
        $Template = M('region')->field('a.id, a.name,b.template_money,b.template_id')
            ->alias('a')
            ->join('__SHOP_SHIPPING_TEMPLATE__ b', 'a.id = b.province_id', 'LEFT')
            ->where($Where)
            ->getAllWithIndex('id');
        $this->assign('Template', $Template);
        
        // 统一配送
        $info = $this->shipping_template_db->where('province_id','100000')->find();
        $this->assign('info', $info);

        return $this->fetch('shipping_template');
    }

    // 订单批量删除
    public function order_del()
    {
        $order_id = input('del_id/a');
        $order_id = eyIntval($order_id);
        if (IS_AJAX_POST && !empty($order_id)) {
            // 条件数组
            $Where = [
                'order_id'  => ['IN', $order_id],
                'lang'      => $this->admin_lang,
            ];
            // 查询数据，存在adminlog日志
            $result = $this->shop_order_db->field('order_code')->where($Where)->select();
            $order_code_list = get_arr_column($result, 'order_code');
            // 删除订单列表数据
            $return = $this->shop_order_db->where($Where)->delete();
            if ($return) {
                // 同步删除订单下的产品
                $this->shop_order_details_db->where($Where)->delete();
                // 同步删除订单下的操作记录
                $this->shop_order_log_db->where($Where)->delete();

                adminLog('删除订单：'.implode(',', $order_code_list));
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }
        $this->error('参数有误');
    }

    /*
     *  查询会员订单数据并加载，无返回
     */
    function GetOrderData($order_id)
    {
        // 获取订单数据
        $OrderData = $this->shop_order_db->find($order_id);

        // 获取会员数据
        $UsersData = $this->users_db->find($OrderData['users_id']);
        // 当前单条订单信息的会员ID，存入session，用于添加订单操作表
        session('OrderUsersId',$OrderData['users_id']);

        // 获取订单详细表数据
        $DetailsData = $this->shop_order_details_db->where('order_id',$OrderData['order_id'])->select();

        // 获取订单状态，后台专用
        $admin_order_status_arr = Config::get('global.admin_order_status_arr');

        // 获取订单方式名称
        $pay_method_arr = Config::get('global.pay_method_arr');

        // 处理订单主表的地址数据处理，显示中文名字
        $OrderData['country']  = '中国';
        $OrderData['province'] = get_province_name($OrderData['province']);
        $OrderData['city']     = get_city_name($OrderData['city']);
        $OrderData['district'] = get_area_name($OrderData['district']);

        $array_new = get_archives_data($DetailsData,'product_id');
        // 处理订单详细表数据处理
        foreach ($DetailsData as $key => $value) {
            // 产品属性处理
            $value['data'] = unserialize($value['data']);
            $attr_value = htmlspecialchars_decode($value['data']['attr_value']);
            $attr_value = htmlspecialchars_decode($attr_value);
            $DetailsData[$key]['data'] = $attr_value;

            // 产品内页地址
            $DetailsData[$key]['arcurl'] = get_arcurl($array_new[$value['product_id']]);
            
            // 小计
            $DetailsData[$key]['subtotal'] = $value['product_price'] * $value['num'];
        }

        // 订单类型
        if (empty($OrderData['prom_type'])) {
            $OrderData['prom_type_name'] = '普通订单';
        }else{
            $OrderData['prom_type_name'] = '虚拟订单';
        }

        // 移动端查询物流链接
        $MobileExpressUrl = "//m.kuaidi100.com/index_all.html?type=".$OrderData['express_code']."&postid=".$OrderData['express_order'];

        // 加载数据
        $this->assign('MobileExpressUrl', $MobileExpressUrl);
        $this->assign('OrderData', $OrderData);
        $this->assign('DetailsData', $DetailsData);
        $this->assign('UsersData', $UsersData);
        $this->assign('admin_order_status_arr',$admin_order_status_arr);
        $this->assign('pay_method_arr',$pay_method_arr);
    }

    // 检测并第一次从官方同步订单中心的前台模板
    public function ajax_syn_theme_shop()
    {
        $msg = '下载订单中心模板包异常，请第一时间联系技术支持，排查问题！';
        $shopLogic = new ShopLogic;
        $data = $shopLogic->syn_theme_shop();
        if (true !== $data) {
            if (1 <= intval($data['code'])) {
                $this->success('初始化成功！', url('Shop/index'));
            } else {
                if (is_array($data)) {
                    $msg = $data['msg'];
                }
            }
        }
        getUsersConfigData('shop', ['shop_open' => 0]);
        $this->error($msg);
    }
}