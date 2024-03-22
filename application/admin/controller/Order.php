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

namespace app\admin\controller;

use think\Db;
use think\Page;
use think\Config;

class Order extends Base
{
    public function _initialize() {
        parent::_initialize();
    }

    public function index()
    {
        // 商城中心开启状态
        $shopOpen = getUsersConfigData('shop.shop_open');
        // 会员中心开启状态
        $webUsersSwitch = tpCache('global.web_users_switch');
        // 如果商城中心开启则执行跳转到商城中心，默认待发货订单列表
        if (!empty($shopOpen) && 1 === intval($shopOpen) && 1 == is_check_access('Shop@index')) {
            $url = url('Shop/index', ["order_status"=>1], true, true);
            $url = preg_replace('/^http(s?)/i', $this->request->scheme(), $url);
            $this->redirect($url);
            exit;
        }
        // 如果商城中心未开启，会员中心开启已开启则执行跳转到会员充值订单列表
        else if (!empty($webUsersSwitch) && 1 === intval($webUsersSwitch) && 1 == is_check_access('Member@money_index')) {
            $url = url('Member/money_index', ['status'=>2], true, true);
            $url = preg_replace('/^http(s?)/i', $this->request->scheme(), $url);
            $this->redirect($url);
            exit;
        } else {
            $this->error('您没有操作权限，请联系超级管理员分配权限');
        }
        // 其他逻辑待添加...

    }

    // AJAX下载订单Excel文档
    public function ajax_order_excel_export()
    {
        // 设置最大内存
        ini_set("memory_limit", "-1");

        // 防止php超时
        function_exists('set_time_limit') && set_time_limit(0);

        if (file_exists('./vendor/PHPExcel.zip')) {
            $zip = new \ZipArchive();//新建一个ZipArchive的对象
            if ($zip->open(ROOT_PATH.'vendor'.DS.'PHPExcel.zip') === true) {
                $zip->extractTo(ROOT_PATH.'vendor'.DS.'PHPExcel'.DS);
                $zip->close();//关闭处理的zip文件
                if (is_dir('./vendor/PHPExcel/')) {
                    @unlink('./vendor/PHPExcel.zip');
                }
            }
        }

        // 执行操作
        if (IS_AJAX_POST) {
            $post = input('post.');
            $ResultUrl = null;
            if (1 == $post['export_type']) {
                // 查询商城订单并处理
                $ExportData = $this->GetShopOrder($post);
            } else if (2 == $post['export_type']) {
                // 查询充值订单并处理
                $ExportData = $this->GetMoneyOrder(1, $post);
            } else if (3 == $post['export_type']) {
                // 查询升级订单并处理
                $ExportData = $this->GetMoneyOrder(0, $post);
            } else if (4 == $post['export_type']) {
                // 查询视频订单并处理
                $ExportData = $this->GetMediaOrder($post);
            } else if (5 == $post['export_type']) {
                // 查询文章订单并处理
                $ExportData = $this->GetArticleOrder($post);
            } else if (6 == $post['export_type']) {
                // 查询文章订单并处理
                $ExportData = $this->GetServiceOrder($post);
            }
            if (!empty($ExportData['ExcelData'])) {
                // 执行导出下载
                $ResultUrl = $this->PerformExport($ExportData['ExcelData'], $ExportData['ExcelField'], $ExportData['ExcelTitle'], $post['export_type']);
            } else if (isset($ExportData['ExcelData'])) {
                $this->error('没有导出的数据');
            }
        }

        if (!empty($ResultUrl)) {
            $this->success('正在下载', $ResultUrl);
        } else {
            $this->error('导出失败');
        }
    }

    // 查询商城订单并处理
    private function GetShopOrder($post = [])
    {
        // 查询条件
        $where = [
            'a.lang' => $this->admin_lang
        ];

        // 订单号查询
        $order_code = !empty($post['order_code']) ? $post['order_code'] : '';
        if (!empty($order_code)) $where['a.order_code'] = ['LIKE', "%{$order_code}%"];

        // 支付方式查询
        $pay_name = !empty($post['pay_name']) ? $post['pay_name'] : '';
        if (!empty($pay_name)) $where['a.pay_name'] = $pay_name;

        // 订单下单终端查询
        $order_terminal = !empty($post['order_terminal']) ? $post['order_terminal'] : 0;
        if (!empty($order_terminal)) $where['a.order_terminal'] = $order_terminal;
        
        // 商品类型查询
        $contains_virtual = !empty($post['contains_virtual']) ? $post['contains_virtual'] : 0;
        if (!empty($contains_virtual)) $where['a.contains_virtual'] = $contains_virtual;

        // 指定订单状态导出
        $order_status = !empty($post['order_status']) ? $post['order_status'] : 0;
        if (!empty($order_status)) $where['a.order_status'] = 10 == $order_status ? 0 : $order_status;

        // 根据日期导出
        $start_time = $post['start_time'];
        $end_time = $post['end_time'];
        if (!empty($start_time) && !empty($end_time)) {
            $start_time        = strtotime($start_time);
            $end_time          = strtotime("+1 day", strtotime($end_time)) - 1;
            $where['a.add_time'] = ['between', [$start_time, $end_time]];
        } elseif (!empty($start_time) && empty($end_time)) {
            $start_time        = strtotime($start_time);
            $where['a.add_time'] = ['>=', $start_time];
        } elseif (empty($start_time) && !empty($end_time)) {
            $end_time          = strtotime("+1 day", strtotime($end_time)) - 1;
            $where['a.add_time'] = ['<=', $end_time];
        }

        // 查询字段
        $Field = 'a.order_id, a.order_code, a.order_status, a.pay_time, a.pay_name, a.express_name, a.express_order, a.consignee, a.order_amount, a.order_total_num, a.province, a.city, a.district, a.address, a.mobile, a.add_time, b.username';
        // 执行查询
        $OrderData = Db::name('shop_order')
            ->alias('a')
            ->field($Field)
            ->join('__USERS__ b', 'a.users_id = b.users_id', 'LEFT')
            ->where($where)
            ->order('a.order_id desc')
            ->select();
        if (empty($OrderData) && !empty($order_code)) {
            // 通过商品名称查询订单号
            $where_1['product_name'] = ['LIKE', "%{$order_code}%"];
            $order_details_list = Db::name('shop_order_details')->where($where_1)->order('order_id asc, details_id asc')->select();
            $order_details_list = group_same_key($order_details_list, 'order_id');
            $order_ids = array_keys($order_details_list);
            // 重新查询订单主表
            unset($where['a.order_code']);
            $where['a.order_id'] = ['IN', $order_ids];
            // 订单主表数据查询
            $OrderData = Db::name('shop_order')
                ->alias('a')
                ->field($Field)
                ->join('__USERS__ b', 'a.users_id = b.users_id', 'LEFT')
                ->where($where)
                ->order('a.order_id desc')
                ->select();
        } else {
            if (!empty($OrderData)) {
                $order_ids = get_arr_column($OrderData, 'order_id');
                $where_1 = [];
                $where_1['order_id'] = ['IN', $order_ids];
                $order_details_list = Db::name('shop_order_details')->where($where_1)->order('order_id asc, details_id asc')->select();
                $order_details_list = group_same_key($order_details_list, 'order_id');
            }
        }

        // 获取订单状态，后台专用
        $admin_order_status_arr = Config::get('global.admin_order_status_arr');
        // 获取订单方式名称
        $pay_method_arr = Config::get('global.pay_method_arr');
        // 处理订单导出数据
        $ExcelData = [];
        foreach ($OrderData as $key => $value) {
            // 匹配省、市中文
            $Province = get_province_name($value['province']);
            // 匹配市、县中文
            $City     = get_city_name($value['city']);
            // 匹配县、区、镇中文
            $District = get_area_name($value['district']);
            // 订单商品列表
            $product_name_all = '';
            $order_details_info = !empty($order_details_list[$value['order_id']]) ? $order_details_list[$value['order_id']] : [];
            if (!empty($order_details_info)) {
                foreach ($order_details_info as $k => $v) {
                    if (!empty($product_name_all)) {
                        $product_name_all .= PHP_EOL;
                    }
                    $product_name_all .= $v['product_name']. "\t";
                }
            }
            // 拼装追加数据
            $PushData = [
                // 订单信息
                'order_code'   => $value['order_code']. "\t",
                'product_name'    => $product_name_all,
                'order_num'    => $value['order_total_num'],
                'order_amount' => $value['order_amount'],
                'add_time'     => date('Y-m-d H:i:s', $value['add_time']),
                'order_status' => $admin_order_status_arr[$value['order_status']],
                // 支付信息
                'pay_time' => !empty($value['pay_time']) ? date('Y-m-d H:i:s', $value['pay_time']) : '',
                'pay_name' => in_array($value['order_status'], [1, 2, 3]) ? $pay_method_arr[$value['pay_name']] : '',
                // 快递信息
                'express_name'  => $value['express_name'],
                'express_order' => $value['express_order']. "\t",
                // 会员账号
                'username' => $value['username'],
                // 收货信息
                'consignee' => $value['consignee'],
                'mobile'    => $value['mobile'],
                'addr_info' => $Province . ' ' . $City . ' ' . $District . ' ' . $value['address'],
            ];
            // 追加数据，用于导出
            array_push($ExcelData, $PushData);
        }
        // 导出字段设置
        $ExcelField = ['order_code', 'product_name', 'order_num', 'order_amount', 'add_time', 'order_status', 'pay_time', 'pay_name', 'express_name', 'express_order', 'username', 'consignee', 'mobile', 'addr_info'];
        // 导出标题设置
        $ExcelTitle = ['订单号', '商品名称', '商品数量', '订单总额', '下单时间', '订单状态', '支付时间', '支付方式', '快递公司', '快递单号', '会员账号', '收货人', '联系电话', '收货地址'];

        // 返回导出所需数据及参数
        $ReturnData = [
            'ExcelData' => $ExcelData,
            'ExcelField' => $ExcelField,
            'ExcelTitle' => $ExcelTitle
        ];
        return $ReturnData;
    }

    // 查询充值订单并处理
    private function GetMoneyOrder($cause_type = 0, $post = [])
    {
        // 查询条件
        $where = [
            // 多语言
            'a.lang' => $this->admin_lang,
            // 查询充值订单
            'a.cause_type' => $cause_type
        ];

        // 订单号或会员名查询
        $keywords = !empty($post['keywords']) ? $post['keywords'] : '';
        if (!empty($keywords)) $where['a.order_number|b.username'] = array('LIKE', "%{$keywords}%");

        // 支付方式查询
        $pay_method = !empty($post['pay_method']) ? $post['pay_method'] : '';
        if (!empty($pay_method)) $where['a.pay_method'] = $pay_method;

        // 会员级别查询
        $level = !empty($post['level']) ? $post['level'] : 0;
        if (!empty($level)) $where['b.level'] = $level;

        // 会员级别查询
        $level_id = !empty($post['level_id']) ? $post['level_id'] : 0;
        if (!empty($level_id)) $where['a.level_id'] = $level_id;

        // 指定订单状态导出
        $status = !empty($post['status']) ? $post['status'] : 0;
        if (!empty($post['status'])) $where['a.status'] = in_array($status, [2, 3]) ? ['IN', [2, 3]] : $status; 
        // 如果传入的类型是0则强制查询已完成的订单
        if (0 === $cause_type) $where['a.status'] = ['IN', [2, 3]];

        // 根据日期导出
        $start_time = $post['start_time'];
        $end_time = $post['end_time'];
        if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime("+1 day", strtotime($end_time)) - 1;
            $where['a.add_time'] = ['between', [$start_time, $end_time]];
        } elseif (!empty($start_time) && empty($end_time)) {
            $start_time = strtotime($start_time);
            $where['a.add_time'] = ['>=', $start_time];
        } elseif (empty($start_time) && !empty($end_time)) {
            $end_time = strtotime("+1 day", strtotime($end_time)) - 1;
            $where['a.add_time'] = ['<=', $end_time];
        }

        // 查询字段
        $Field = 'a.order_number, a.money, b.username, a.add_time, a.status, a.update_time, a.pay_method, a.cause, b.level';
        // 执行查询
        $MoneyData = Db::name('users_money')
            ->alias('a')
            ->field($Field)
            ->join('__USERS__ b', 'a.users_id = b.users_id', 'LEFT')
            ->where($where)
            ->order('a.status desc, a.moneyid desc')
            ->select();

        // 充值状态
        $pay_status_arr = config('global.pay_status_arr');
        // 支付方式
        $pay_method_arr = config('global.pay_method_arr');
        // 处理订单导出数据
        $ExcelData = [];
        if (0 === $cause_type) {
            // 升级订单
            foreach ($MoneyData as $key => $value) {
                $value['cause'] = unserialize($value['cause']);
                // 拼装追加数据
                $PushData = [
                    // 订单信息
                    'order_number' => $value['order_number']. "\t",
                    'type_name'    => $value['cause']['type_name'],
                    'money'        => $value['money'],
                    'add_time'     => date('Y-m-d H:i:s', $value['add_time']),
                    'status'       => $pay_status_arr[$value['status']],
                    // 会员账号
                    'username'     => $value['username'],
                    // 支付信息
                    'update_time'  => in_array($value['status'], [2, 3]) ? date('Y-m-d H:i:s', $value['update_time']) : '',
                    'pay_method'   => in_array($value['status'], [2, 3]) ? $pay_method_arr[$value['pay_method']] : ''
                ];
                // 追加数据，用于导出
                array_push($ExcelData, $PushData);
                // 导出字段设置
                $ExcelField = ['order_number', 'type_name', 'money', 'username', 'add_time', 'status', 'update_time', 'pay_method'];
                // 导出标题设置
                $ExcelTitle = ['订单号', '产品名称', '订单金额', '会员账号', '升级时间', '订单状态', '支付时间', '支付方式'];
            }
        } else {
            // 充值订单
            foreach ($MoneyData as $key => $value) {
                // 拼装追加数据
                $PushData = [
                    // 订单信息
                    'order_number' => $value['order_number']. "\t",
                    'money'        => $value['money'],
                    'add_time'     => date('Y-m-d H:i:s', $value['add_time']),
                    'status'       => $pay_status_arr[$value['status']],
                    // 会员账号
                    'username'     => $value['username'],
                    // 支付信息
                    'update_time'  => in_array($value['status'], [2, 3]) ? date('Y-m-d H:i:s', $value['update_time']) : '',
                    'pay_method'   => in_array($value['status'], [2, 3]) ? $pay_method_arr[$value['pay_method']] : ''
                ];
                // 追加数据，用于导出
                array_push($ExcelData, $PushData);
            }
            // 导出字段设置
            $ExcelField = ['order_number', 'money', 'username', 'add_time', 'status', 'update_time', 'pay_method'];
            // 导出标题设置
            $ExcelTitle = ['订单号', '充值金额', '会员账号', '充值时间', '订单状态', '支付时间', '支付方式'];
        }
        
        // 返回导出所需数据及参数
        $ReturnData = [
            'ExcelData' => $ExcelData,
            'ExcelField' => $ExcelField,
            'ExcelTitle' => $ExcelTitle
        ];
        return $ReturnData;
    }

    // 查询视频订单并处理
    private function GetMediaOrder($post = [])
    {
        // 查询条件
        $where = [
            'a.lang' => $this->admin_lang
        ];

        // 订单状态搜索
        $order_status = !empty($post['order_status']) ? $post['order_status'] : 0;
        if (!empty($order_status)) $where['a.order_status'] = intval($order_status) === 1 ? intval($order_status) : 0;

        // 订单号或用户名搜索
        $keywords = !empty($post['keywords']) ? $post['keywords'] : '';
        if (!empty($keywords)) $where['a.order_code|b.username'] = ['LIKE', "%{$keywords}%"];

        // 支付方式查询
        $pay_name = !empty($post['pay_name']) ? $post['pay_name'] : '';
        if (!empty($pay_name)) $where['a.pay_name'] = $pay_name;

        // 根据日期导出
        $start_time = $post['start_time'];
        $end_time = $post['end_time'];
        if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime("+1 day", strtotime($end_time)) - 1;
            $where['a.add_time'] = ['between', [$start_time, $end_time]];
        } elseif (!empty($start_time) && empty($end_time)) {
            $start_time = strtotime($start_time);
            $where['a.add_time'] = ['>=', $start_time];
        } elseif (empty($start_time) && !empty($end_time)) {
            $end_time = strtotime("+1 day", strtotime($end_time)) - 1;
            $where['a.add_time'] = ['<=', $end_time];
        }

        // 查询字段
        $Field = 'a.order_code, a.order_amount, b.username, a.mobile, a.add_time, a.order_status';
        // 执行查询
        $MediaData = Db::name('media_order')
            ->alias('a')
            ->field($Field)
            ->join('__USERS__ b', 'a.users_id = b.users_id', 'LEFT')
            ->where($where)
            ->order('a.order_status desc, a.order_id desc')
            ->select();
        // 处理订单导出数据
        $ExcelData = [];
        foreach ($MediaData as $key => $value) {
            // 拼装追加数据
            $PushData = [
                // 订单信息
                'order_code'   => $value['order_code']. "\t",
                'order_amount' => $value['order_amount'],
                'add_time'     => date('Y-m-d H:i:s', $value['add_time']),
                'order_status' => 1 == $value['order_status'] ? '已付款' : '未付款',
                // 会员账号
                'mobile'       => $value['mobile'],
                'username'     => $value['username']
            ];
            // 追加数据，用于导出
            array_push($ExcelData, $PushData);
        }
        // 导出字段设置
        $ExcelField = ['order_code', 'order_amount', 'username', 'mobile', 'add_time', 'order_status'];
        // 导出标题设置
        $ExcelTitle = ['订单号', '订单金额', '会员账号', '会员手机', '下单时间', '订单状态'];

        // 返回导出所需数据及参数
        $ReturnData = [
            'ExcelData' => $ExcelData,
            'ExcelField' => $ExcelField,
            'ExcelTitle' => $ExcelTitle
        ];
        return $ReturnData;
    }

    // 查询文章订单并处理
    private function GetArticleOrder($post = [])
    {
        // 查询条件
        $where = [
            'a.lang' => $this->admin_lang
        ];

        // 订单状态搜索
        $order_status = !empty($post['order_status']) ? $post['order_status'] : 0;
        if (!empty($order_status)) $where['a.order_status'] = intval($order_status) === 1 ? intval($order_status) : 0;

        // 订单号或用户名搜索
        $keywords = !empty($post['keywords']) ? $post['keywords'] : '';
        if (!empty($keywords)) $where['a.order_code|b.username'] = ['LIKE', "%{$keywords}%"];

        // 支付方式查询
        $pay_name = !empty($post['pay_name']) ? $post['pay_name'] : '';
        if (!empty($pay_name)) $where['a.pay_name'] = $pay_name;

        // 根据日期导出
        $start_time = $post['start_time'];
        $end_time = $post['end_time'];
        if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime("+1 day", strtotime($end_time)) - 1;
            $where['a.add_time'] = ['between', [$start_time, $end_time]];
        } elseif (!empty($start_time) && empty($end_time)) {
            $start_time = strtotime($start_time);
            $where['a.add_time'] = ['>=', $start_time];
        } elseif (empty($start_time) && !empty($end_time)) {
            $end_time = strtotime("+1 day", strtotime($end_time)) - 1;
            $where['a.add_time'] = ['<=', $end_time];
        }

        // 查询字段
        $Field = 'a.order_code, a.order_amount, b.username, a.add_time, a.order_status';
        // 执行查询
        $ArticleData = Db::name('article_order')
            ->alias('a')
            ->field($Field)
            ->join('__USERS__ b', 'a.users_id = b.users_id', 'LEFT')
            ->where($where)
            ->order('a.order_status desc, a.order_id desc')
            ->select();

        // 处理订单导出数据
        $ExcelData = [];
        foreach ($ArticleData as $key => $value) {
            // 拼装追加数据
            $PushData = [
                // 订单信息
                'order_code'   => $value['order_code']. "\t",
                'order_amount' => $value['order_amount'],
                'add_time'     => date('Y-m-d H:i:s', $value['add_time']),
                'order_status' => 1 == $value['order_status'] ? '已付款' : '未付款',
                // 会员账号
                'username'     => $value['username']
            ];
            // 追加数据，用于导出
            array_push($ExcelData, $PushData);
        }
        // 导出字段设置
        $ExcelField = ['order_code', 'order_amount', 'username', 'add_time', 'order_status'];
        // 导出标题设置
        $ExcelTitle = ['订单号', '订单金额', '会员账号', '下单时间', '订单状态'];

        // 返回导出所需数据及参数
        $ReturnData = [
            'ExcelData' => $ExcelData,
            'ExcelField' => $ExcelField,
            'ExcelTitle' => $ExcelTitle
        ];
        return $ReturnData;
    }

    // 查询售后订单并处理
    private function GetServiceOrder($post = [])
    {
        // 初始化数组和条件
        $where =[];

        // 订单号查询
        $order_code = !empty($post['order_code']) ? trim($post['order_code']) : '';
        if (!empty($order_code)) $where['a.order_code|a.product_name'] = ['LIKE', "%{$order_code}%"];

        // 支付方式查询
        $pay_name = !empty($post['pay_name']) ? $post['pay_name'] : '';
        if (!empty($pay_name)) $where['c.pay_name'] = $pay_name;

        // 订单下单终端查询
        $order_terminal = !empty($post['order_terminal']) ? $post['order_terminal'] : 0;
        if (!empty($order_terminal)) $where['c.order_terminal'] = $order_terminal;

        // 根据日期导出
        $start_time = $post['start_time'];
        $end_time = $post['end_time'];
        if (!empty($start_time) && !empty($end_time)) {
            $start_time = strtotime($start_time);
            $end_time = strtotime("+1 day", strtotime($end_time)) - 1;
            $where['a.add_time'] = ['between', [$start_time, $end_time]];
        } elseif (!empty($start_time) && empty($end_time)) {
            $start_time = strtotime($start_time);
            $where['a.add_time'] = ['>=', $start_time];
        } elseif (empty($start_time) && !empty($end_time)) {
            $end_time = strtotime("+1 day", strtotime($end_time)) - 1;
            $where['a.add_time'] = ['<=', $end_time];
        }

        $Service = Db::name('shop_order_service')->alias('a')
            ->field('a.*, b.product_price, b.num as product_num, d.username')
            ->join('__SHOP_ORDER_DETAILS__ b', 'a.details_id = b.details_id', 'LEFT')
            ->join('__SHOP_ORDER__ c', 'a.order_id = c.order_id', 'LEFT')
            ->join('__USERS__ d', 'a.users_id = d.users_id', 'LEFT')
            ->where($where)
            ->order('a.service_id desc')
            ->select();
        // 获取订单状态
        $ServiceStatus = Config::get('global.order_service_status');
        // 处理订单导出数据
        $ExcelData = [];
        foreach ($Service as $key => $value) {
            $service_type = '换货';
            if (2 === intval($value['service_type'])) {
                $service_type = '退货';
            } else if (3 === intval($value['service_type'])) {
                $service_type = '维修';
            }
            // 拼装追加数据
            $PushData = [
                // 订单信息
                'order_code' => $value['order_code'] . "\t",
                'refund_code' => $value['refund_code'] . "\t",
                'product_name' => $value['product_name'],
                'product_price' => $value['product_price'],
                'product_num' => $value['product_num'],
                'refund_price' => $value['refund_price'],
                'service_type' => $service_type,
                'status' => $ServiceStatus[$value['status']],
                'add_time' => date('Y-m-d H:i:s', $value['add_time']),
                // 会员账号
                'username' => $value['username'],
                // 收货信息
                'consignee' => $value['consignee'],
                'mobile' => $value['mobile'],
                'address' => trim($value['address']),
            ];
            // 追加数据，用于导出
            array_push($ExcelData, $PushData);
        }

        // 导出字段设置
        $ExcelField = ['order_code', 'refund_code', 'product_name', 'product_price', 'product_num', 'refund_price', 'service_type', 'status', 'add_time', 'username', 'consignee', 'mobile', 'address'];
        // 导出标题设置
        $ExcelTitle = ['订单号', '服务单号', '商品名称', '商品价格', '商品数量', '退还金额', '售后类型', '处理状态', '售后时间', '会员账号', '收货人', '联系电话', '收货地址'];

        // 返回导出所需数据及参数
        $ReturnData = [
            'ExcelData' => $ExcelData,
            'ExcelField' => $ExcelField,
            'ExcelTitle' => $ExcelTitle
        ];
        return $ReturnData;
    }

    // 执行导出下载
    private function PerformExport($ExcelData = [], $ExcelField = [], $ExcelTitle = [], $export_type = [])
    {
        // 引入SDK
        vendor("PHPExcel.Classes.PHPExcel");

        // Excel表格坐标
        $cell_arr = ['A','B','C','D','E','F','G','H','I','J','K','L','M', 'N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];

        // 执行导出
        $objPHPExcel = new \PHPExcel();  
        $objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')->setSize(12);

        // 设置表格标题栏长度
        $objActSheet = $objPHPExcel->getActiveSheet();
        if (1 == $export_type) {// 商城订单
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(13);
            $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(40);
            // 设置导出表格名称
            $FileName = 'shop-order-'.date("YmdHis");

        } else if (2 == $export_type) {// 充值订单
            // 设置导出表格标题宽度
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
            // 设置导出表格名称
            $FileName = 'money-order-'.date("YmdHis");

        } else if (3 == $export_type) {// 充值订单
            // 设置导出表格标题宽度
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
            // 设置导出表格名称
            $FileName = 'upgrade-order-'.date("YmdHis");

        } else if (4 == $export_type) {// 视频订单
            // 设置导出表格标题宽度
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
            // 设置导出表格名称
            $FileName = 'media-order-'.date("YmdHis");

        } else if (5 == $export_type) {// 文章订单
            // 设置导出表格标题宽度
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
            // 设置导出表格名称
            $FileName = 'article-order-'.date("YmdHis");

        } else if (6 == $export_type) {// 售后订单
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(22);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(13);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(40);
            // 设置导出表格名称
            $FileName = 'service-order-'.date("YmdHis");

        }

        // 循环设置表格标题栏数据
        $startRow = 1;
        if(!empty($ExcelTitle) || count($ExcelTitle) > 0) {
            foreach($ExcelTitle as $k => $v) {
                $objActSheet->setCellValue($cell_arr[$k] . $startRow, $v);
            }
            $startRow = 2;
        }

        // 循环设置表格字段内容数据
        foreach($ExcelData as $v) {
            $columnField = 'A';
            foreach($ExcelField as $key => $value) {
                $objActSheet->setCellValue($cell_arr[$key] . $startRow, $v[$value]);
                if (1 == $export_type) {// 商城订单
                    $product_name_arr = explode(PHP_EOL, $v['product_name']);
                    $product_name_num = count($product_name_arr);
                    $height = ($product_name_num * 15) + 5;
                    $objActSheet->getRowDimension($startRow)->setRowHeight($height);// 设置行高
                    $objActSheet->getStyle($columnField.($startRow))->getAlignment()->setWrapText(true);//设置自动换行
                    $objActSheet->getStyle($columnField. $startRow)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
                    $columnField++;
                }
            }
            $startRow++;
        }
        
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $FileName . '.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        // 文件目录
        $ExcelPath = UPLOAD_PATH . 'excel/';
        // 保存前清空删除原先的excel
        delFile(UPLOAD_PATH . 'excel/', true);
        // 创建文件夹
        @mkdir($ExcelPath, 0777, true);
        // excel文件路径
        $filePath = $ExcelPath . $FileName . '.xlsx';
        // 保存excel文件
        $objWriter->save($filePath);
        // 返回excel文件路径到AJAX下载
        return request()->domain() . ROOT_DIR . '/' . $filePath;
    }
}
