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
use app\admin\logic\ProductSpecLogic; // 用于产品规格逻辑功能处理
use app\user\model\Pay as PayModel;  //用于虚拟网盘商品付款后自动走流程处理

class Shop extends Base {

    private $UsersConfigData = [];

    /**
     * 构造方法
     */
    public function __construct(){
        parent::__construct();
        $this->language_access(); // 多语言功能操作权限
        $this->users_db              = Db::name('users');                   // 会员信息表
        $this->shop_order_db         = Db::name('shop_order');              // 订单主表
        $this->shop_order_details_db = Db::name('shop_order_details');      // 订单明细表
        $this->shop_address_db       = Db::name('shop_address');            // 收货地址表
        $this->shop_express_db       = Db::name('shop_express');            // 物流名字表
        $this->shop_order_log_db     = Db::name('shop_order_log');          // 订单操作表
        $this->shipping_template_db  = Db::name('shop_shipping_template');  // 运费模板表
        $this->product_spec_preset_db = Db::name('product_spec_preset');    // 产品规格预设表

        // 会员中心配置信息
        $this->UsersConfigData = getUsersConfigData('all');
        $this->assign('userConfig',$this->UsersConfigData);

        $this->ProductSpecLogic = new ProductSpecLogic;

        // 过期订单预处理
        $this->ShopLogic = new ShopLogic;
        $this->ShopLogic->OverdueOrderHandle();
    }

    public function home()
    {
        $this->redirect(url('Statistics/index', [], true, true));
        exit;
    }

    /**
     * 商城设置
     */
    public function conf()
    {
        if (IS_POST) {
            $post = input('post.');
            if (!empty($post)) {
                /*邮件提醒*/
                $smtp['smtp_shop_order_pay']  = !empty($post['smtp_shop_order_pay'])  ? 1 : 0;
                $smtp['smtp_shop_order_send'] = !empty($post['smtp_shop_order_send']) ? 1 : 0;
                tpCache('smtp', $smtp);
                unset($post['smtp_shop_order_pay']);
                unset($post['smtp_shop_order_send']);
                /*END*/

                $TestPass = $post['TestPass'];
                unset($post['TestPass']);
                if (0 == $TestPass) unset($post['shop']['shop_open_spec']);

                foreach ($post as $key => $val) {
                    is_array($val) && getUsersConfigData($key, $val);
                }
                $this->success('设置成功！', url('Shop/conf'));
            }
        }

        $Result = VerifyLatestTemplate();
        if (!empty($Result)) getUsersConfigData('shop', ['shop_open_spec' => 0]);
        $TestPass = empty($Result) ? 1 : 0;
        $this->assign('TestPass', $TestPass);

        // 商城配置信息
        $smtp = tpCache('smtp');
        $this->assign('smtp', $smtp);
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
            'a.lang'   => $this->admin_lang,
        ];
        // 订单号查询
        $order_code = input('order_code/s');
        if (!empty($order_code)) $Where['a.order_code'] = array('LIKE', "%{$order_code}%");

        //时间检索条件
        $begin    = strtotime(input('param.add_time_begin/s'));
        $end    = input('param.add_time_end/s');
        !empty($end) && $end .= ' 23:59:59';
        $end    = strtotime($end);
        // 时间检索
        if ($begin > 0 && $end > 0) {
            $Where['a.add_time'] = array('between',"$begin,$end");
        } else if ($begin > 0) {
            $Where['a.add_time'] = array('egt', $begin);
        } else if ($end > 0) {
            $Where['a.add_time'] = array('elt', $end);
        }

        // 订单状态查询
        $order_status = input('order_status/s');
        if (!empty($order_status)) $Where['a.order_status'] = (10 == $order_status) ? 0 : $order_status;
        // 查询满足要求的总记录数
        $count = $this->shop_order_db->alias('a')->where($Where)->count('order_id');
        // 实例化分页类 传入总记录数和每页显示的记录数
        $pageObj = new Page($count, config('paginate.list_rows'));
        // 订单主表数据查询
        $list = $this->shop_order_db->where($Where)
            ->field('a.*, b.username')
            ->alias('a')
            ->join('__USERS__ b', 'a.users_id = b.users_id', 'LEFT')
            ->order('a.order_id desc')
            ->limit($pageObj->firstRow.','.$pageObj->listRows)
            ->select();
        $order_ids = [];
        $OrderReminderID = [];
        foreach ($list as $key => $value) {
            array_push($order_ids, $value['order_id']);
            if (1 == $value['order_status']) array_push($OrderReminderID, $value['order_id']);
        }

        $DetailsData = $this->shop_order_details_db->where('order_id','IN',$order_ids)->order('details_id asc')->select();
        $DetailsDataGroup = group_same_key($DetailsData, 'order_id');
        foreach ($list as $key => $value) {
            // 查询订单明细表数据
            $ArchivesData = get_archives_data($DetailsData, 'product_id');

            // 处理订单详细表数据处理
            $value['litpic'] = get_default_pic();
            foreach ($DetailsDataGroup[$value['order_id']] as $kkk => $vvv) {
                if (0 == $kkk) {
                    // 产品图片
                    if (!empty($vvv['litpic'])) {
                        $value['litpic'] = handle_subdir_pic(get_default_pic($vvv['litpic']));
                    }
                    // 产品前台链接
                    $value['arcurl'] = get_arcurl($ArchivesData[$vvv['product_id']]);
                    break;
                }
            }
            $list[$key] = $value;
        }
        
        if (!empty($OrderReminderID)) {
            $field = 'order_id, count(action_id) as action_count';
            $group = 'order_id';
            $LogWhere = [
                'action_desc' => '提醒成功！',
                'order_id' => ['IN', $OrderReminderID]
            ];
            $LogData = $this->shop_order_log_db->field($field)->group($group)->where($LogWhere)->getAllWithIndex('order_id');
        }
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

        // 计算单页总额
        $total_money = $this->shop_order_db->alias('a')->where($Where)->where('order_id','IN',$order_ids)->value("SUM(`order_amount`) as `total_money`");

        /*查询已退还的总额*/
        $list_order_id = get_arr_column($list, 'order_id');
        $where_new = [
            'service_type' => 2,
            'status' => ['IN', [2, 4, 5, 7]],
            'order_id' => ['IN', $list_order_id]
        ];
        $refund_balance = Db::name('shop_order_service')->where($where_new)->value("SUM(`refund_balance`) as `refund_balance`");
        /* END */

        // 计算单页总额 - 已退还的总额
        if (!empty($refund_balance)) $total_money = $total_money - $refund_balance;
        
        // 数据加载
        $this->assign('pager', $pageObj);
        $this->assign('list', $list);
        $this->assign('LogData', $LogData);
        $this->assign('page', $pageStr);
        $this->assign('total_money', $total_money);
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
                $Action[$key]['action_desc'] = str_replace("！", "", $value['action_desc']);

                if (0 == $value['action_user']) {
                    // 若action_user为0，表示会员操作，根据订单号中的ID获取会员名。
                    $username = $this->users_db->field('username')->where('users_id',$value['users_id'])->find();
                    $Action[$key]['username'] = '会 &nbsp; 员: '.$username['username'];
                } else {
                    // 若action_user不为0，表示管理员操作，根据ID获取管理员名。
                    $user_name = Db::name('admin')->field('user_name')->where('admin_id',$value['action_user'])->find();
                    $Action[$key]['username'] = '管理员: '.$user_name['user_name'];
                }

                // 操作时，订单发货状态
                $Action[$key]['express_status'] = '未发货';
                if (1 == $value['express_status']) {
                    $Action[$key]['express_status'] = '已发货';
                }

                // 操作时，订单付款状态
                $Action[$key]['pay_status'] = '未支付';
                if (1 == $value['pay_status']) {
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
                if (1 == $post['prom_type']) {
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

                // 查询会员信息
                $Field = 'username, nickname, email, mobile';
                $Users = $this->users_db->field($Field)->where('users_id', $post['users_id'])->find();

                // 邮箱发送
                $SmtpConfig = tpCache('smtp');
                $Result['email'] = GetEamilSendData($SmtpConfig, $Users, $post, 2);

                // 手机发送
                $SmsConfig = tpCache('sms');
                $Result['mobile'] = GetMobileSendData($SmsConfig, $Users, $post, 2);

                // 发送站内信给会员
                $NickName = !empty($Users['nickname']) ? $Users['nickname'] : $Users['username'];
                SendNotifyMessage($UpdateData, 6, 0, $post['users_id'], $NickName);

                $this->success('发货成功', null, $Result);
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

                    // 如果是关闭订单操作则执行还原产品库存
                    if ('gbdd' == $post['status_name']) {
                        $UpWhere = $this->shop_order_details_db->where('order_id',$post['order_id'])->field('product_id as aid,num,data')->find();
                        // 读取规格值ID，拼装作为更新条件
                        $UpWhere['spec_value_id'] = unserialize($UpWhere['data'])['spec_value_id'];
                        // 更新数据
                        $UpData['spec_stock']     = Db::raw('spec_stock+'.($UpWhere['num']));
                        $UpData['spec_sales_num'] = Db::raw('spec_sales_num-'.($UpWhere['num']));
                        // 清除多余num数据
                        unset($UpWhere['num']); 
                        // 清除多余data数据
                        unset($UpWhere['data']);
                        // 更新库存及销量
                        Db::name('product_spec_value')->where($UpWhere)->update($UpData);
                    }

                    // 添加订单操作记录
                    AddOrderAction($post['order_id'], 0, session('admin_id'), $order_status, $express_status, $pay_status, $action_desc, $action_note);

                    // 判断是否为虚拟商品
                    if ('yfk' == $post['status_name'] && $OrderData['prom_type'] == 1) {
                        PayModel::afterVirtualProductPay($Where);
                    }
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
            $Where['a.name'] = array('LIKE', "%{$region_name}%");
        }

        // 省份
        $Template = Db::name('region')->field('a.id, a.name,b.template_money,b.template_id')
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
        $OrderData['prom_type_virtual'] = false;
        // 处理订单详细表数据处理
        foreach ($DetailsData as $key => $value) {
            if ($value['prom_type'] == 1) {
                $OrderData['prom_type_virtual'] = true;
            }
            // 产品属性处理
            $ValueData = unserialize($value['data']);
            // 规制值
            $spec_value = !empty($ValueData['spec_value']) ? htmlspecialchars_decode($ValueData['spec_value']) : '';
            $spec_value = htmlspecialchars_decode($spec_value);
            // 旧参数
            $attr_value = !empty($ValueData['attr_value']) ? htmlspecialchars_decode($ValueData['attr_value']) : '';
            $attr_value = htmlspecialchars_decode($attr_value);
            // 新参数
            $attr_value_new = !empty($ValueData['attr_value_new']) ? htmlspecialchars_decode($ValueData['attr_value_new']) : '';
            $attr_value_new = htmlspecialchars_decode($attr_value_new);
            // 优先显示新参数
            $attr_value = !empty($attr_value_new) ? $attr_value_new : $attr_value;
            $DetailsData[$key]['data'] = $spec_value . $attr_value;

            // 产品内页地址
            $DetailsData[$key]['arcurl'] = get_arcurl($array_new[$value['product_id']]);
            
            // 小计
            $DetailsData[$key]['subtotal'] = $value['product_price'] * $value['num'];
            
            $DetailsData[$key]['litpic'] = handle_subdir_pic($DetailsData[$key]['litpic']); // 支持子目录
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
        $icon = 2;
        $msg = '下载订单中心模板包异常，请第一时间联系技术支持，排查问题！';
        $shopLogic = new ShopLogic;
        $data = $shopLogic->syn_theme_shop();
        if (true !== $data) {
            if (1 <= intval($data['code'])) {
                $this->success('初始化成功！', url('Shop/index'));
            } else {
                if (is_array($data)) {
                    $msg = $data['msg'];
                    $icon = !empty($data['icon']) ? $data['icon'] : $icon;
                }
            }
        }
        getUsersConfigData('shop', ['shop_open' => 0]);
        $this->error($msg, null, ['icon'=>$icon]);
    }

    // ------------------------------------------------------------------------------------------------------
    // 以下所有代码都是产品规格处理逻辑 2019-07-08 陈风任
    // ------------------------------------------------------------------------------------------------------
    // 规格列表管理，包含新增、更新
    public function spec_index()
    {
        if (IS_AJAX_POST) {
            // 新增、更新
            $post = input('post.');
            // 当前时间戳
            $time = getTime();
            /*新增数据处理*/
            $post_new = [];
            foreach ($post['preset_new'] as $key => $value) {
                // 规格名称不允许为空
                $preset_name = $post['preset_name_'.$value][0];
                if (empty($preset_name)) continue;
                // 是否同步到已发布的商品规格
                $spec_sync = !empty($post['spec_sync_'.$value]) ? 1 : 0;
                // 排序号
                $sort_order  = $post['sort_order_'.$value];

                // 拼装三维数组
                foreach ($post['preset_value_'.$value] as $kk => $vv) {
                    if (empty($vv)) continue;
                    $post_new[$key][$kk]['preset_mark_id'] = $value; // 标记ID，一整条规格信息中的唯一标识
                    $post_new[$key][$kk]['preset_name']    = $preset_name;
                    $post_new[$key][$kk]['preset_value']   = $vv;
                    $post_new[$key][$kk]['spec_sync']      = $spec_sync;
                    $post_new[$key][$kk]['sort_order']     = $sort_order;
                    $post_new[$key][$kk]['lang']           = $this->admin_lang;
                    $post_new[$key][$kk]['add_time']       = $time;
                    $post_new[$key][$kk]['update_time']    = $time;
                }
            }

            // 三维数组降为二维数组
            $data_new = $this->ProductSpecLogic->ArrayDowngrade($post_new);
            /* END */

            /*原有数据处理*/
            $post_old = [];
            $spec_sync_where = $spec_sync_mark_data = $spec_sync_value_data = [];
            foreach ($post['preset_old'] as $key => $value) {
                // 规格名称不允许为空
                $preset_name = $post['preset_name_old_'.$value][0];
                if (empty($preset_name)) continue;

                // 是否同步到已发布的商品规格
                $spec_sync = !empty($post['spec_sync_'.$value]) ? 1 : 0;

                // 排序号
                $sort_order  = $post['sort_order_'.$value];

                // 需要同步的规格名称
                if (!empty($spec_sync)) {
                    array_push($spec_sync_where, $value);
                    $spec_sync_mark_data[$value]['spec_name'] = $preset_name;
                }

                // 拼装三维数组
                foreach ($post['preset_value_old_'.$value] as $kk => $vv) {
                    if (empty($vv)) continue;
                    $preset_id = $post['preset_id_old_'.$value][$kk];
                    // 如果ID是否为空
                    if (!empty($preset_id)) {
                        // 有ID则为更新
                        $post_old[$key][$kk]['preset_id'] = $preset_id;

                        // 需要同步的规格值
                        if (!empty($spec_sync)) $spec_sync_value_data[$preset_id]['spec_value'] = $vv;
                    } else {
                        // 无ID则为新增
                        $post_old[$key][$kk]['lang']     = $this->admin_lang;
                        $post_old[$key][$kk]['add_time'] = $time;
                        $post_old[$key][$kk]['preset_mark_id'] = $value; // 标记ID，一整条规格信息中的唯一标识
                    }
                    $post_old[$key][$kk]['preset_name']  = $preset_name;
                    $post_old[$key][$kk]['preset_value'] = $vv;
                    $post_old[$key][$kk]['spec_sync']    = $spec_sync;
                    $post_old[$key][$kk]['sort_order']   = $sort_order;
                    $post_old[$key][$kk]['update_time']  = $time;
                }
            }
            // 三维数组降为二维数组
            $data_old = $this->ProductSpecLogic->ArrayDowngrade($post_old);
            /* END */

            // 合并数组并且更新数据
            $UpData = array_merge($data_old, $data_new);
            model('ProductSpecPreset')->saveAll($UpData);

            /*执行同步数据*/
            if (!empty($spec_sync_where)) {
                $w_1['spec_mark_id'] = ['IN', $spec_sync_where];
                $f_1 = 'spec_id, spec_mark_id, spec_name, spec_value_id, spec_value';
                $SpecMarkData = Db::name('product_spec_data')->where($w_1)->field($f_1)->order('spec_mark_id asc')->select();
                foreach ($SpecMarkData as $key => $value) {
                    $SpecMarkData[$key]['spec_name'] = $spec_sync_mark_data[$value['spec_mark_id']]['spec_name'];
                    $SpecMarkData[$key]['spec_value'] = $spec_sync_value_data[$value['spec_value_id']]['spec_value'];
                }
                model('ProductSpecData')->saveAll($SpecMarkData);
            }
            /* END */

            $this->success('更新成功！');
        }

        // 查询规格数据
        $PresetData = $this->product_spec_preset_db->where('lang',$this->admin_lang)->order('sort_order asc, preset_id asc')->select();
        // 数组转化
        $ResultData = $this->ProductSpecLogic->GetPresetData($PresetData);
        // 获取预设规格中最大的标记MarkId
        $PresetMarkId = model('ProductSpecPreset')->GetMaxPresetMarkId();
        // 加载参数
        $this->assign('info', $ResultData);
        $this->assign('PresetMarkId', $PresetMarkId);
        return $this->fetch('spec_index');
    }


    // 规格列表管理，包含新增、更新
    public function spec_template()
    {
        if (IS_AJAX_POST) {
            // 新增、更新
            $post = input('post.');
            // 当前时间戳
            $time = getTime();
            /*新增数据处理*/
            $post_new = [];
            foreach ($post['preset_new'] as $key => $value) {
                // 规格名称不允许为空
                $preset_name = $post['preset_name_'.$value][0];
                if (empty($preset_name)) continue;
                // 是否同步到已发布的商品规格
                $spec_sync = !empty($post['spec_sync_'.$value]) ? 1 : 0;
                // 排序号
                $sort_order  = $post['sort_order_'.$value];

                // 拼装三维数组
                foreach ($post['preset_value_'.$value] as $kk => $vv) {
                    if (empty($vv)) continue;
                    $post_new[$key][$kk]['preset_mark_id'] = $value; // 标记ID，一整条规格信息中的唯一标识
                    $post_new[$key][$kk]['preset_name']    = $preset_name;
                    $post_new[$key][$kk]['preset_value']   = $vv;
                    $post_new[$key][$kk]['spec_sync']      = $spec_sync;
                    $post_new[$key][$kk]['sort_order']     = $sort_order;
                    $post_new[$key][$kk]['lang']           = $this->admin_lang;
                    $post_new[$key][$kk]['add_time']       = $time;
                    $post_new[$key][$kk]['update_time']    = $time;
                }
            }

            // 三维数组降为二维数组
            $data_new = $this->ProductSpecLogic->ArrayDowngrade($post_new);
            /* END */

            /*原有数据处理*/
            $post_old = [];
            $spec_sync_where = $spec_sync_mark_data = $spec_sync_value_data = [];
            foreach ($post['preset_old'] as $key => $value) {
                // 规格名称不允许为空
                $preset_name = $post['preset_name_old_'.$value][0];
                if (empty($preset_name)) continue;

                // 是否同步到已发布的商品规格
                $spec_sync = !empty($post['spec_sync_'.$value]) ? 1 : 0;

                // 排序号
                $sort_order  = $post['sort_order_'.$value];

                // 需要同步的规格名称
                if (!empty($spec_sync)) {
                    array_push($spec_sync_where, $value);
                    $spec_sync_mark_data[$value]['spec_name'] = $preset_name;
                }

                // 拼装三维数组
                foreach ($post['preset_value_old_'.$value] as $kk => $vv) {
                    if (empty($vv)) continue;
                    $preset_id = $post['preset_id_old_'.$value][$kk];
                    // 如果ID是否为空
                    if (!empty($preset_id)) {
                        // 有ID则为更新
                        $post_old[$key][$kk]['preset_id'] = $preset_id;

                        // 需要同步的规格值
                        if (!empty($spec_sync)) $spec_sync_value_data[$preset_id]['spec_value'] = $vv;
                    } else {
                        // 无ID则为新增
                        $post_old[$key][$kk]['lang']     = $this->admin_lang;
                        $post_old[$key][$kk]['add_time'] = $time;
                        $post_old[$key][$kk]['preset_mark_id'] = $value; // 标记ID，一整条规格信息中的唯一标识
                    }
                    $post_old[$key][$kk]['preset_name']  = $preset_name;
                    $post_old[$key][$kk]['preset_value'] = $vv;
                    $post_old[$key][$kk]['spec_sync']    = $spec_sync;
                    $post_old[$key][$kk]['sort_order']   = $sort_order;
                    $post_old[$key][$kk]['update_time']  = $time;
                }
            }
            // 三维数组降为二维数组
            $data_old = $this->ProductSpecLogic->ArrayDowngrade($post_old);
            /* END */

            // 合并数组并且更新数据
            $UpData = array_merge($data_old, $data_new);
            model('ProductSpecPreset')->saveAll($UpData);

            /*执行同步数据*/
            if (!empty($spec_sync_where)) {
                $w_1['spec_mark_id'] = ['IN', $spec_sync_where];
                $f_1 = 'spec_id, spec_mark_id, spec_name, spec_value_id, spec_value';
                $SpecMarkData = Db::name('product_spec_data')->where($w_1)->field($f_1)->order('spec_mark_id asc')->select();
                foreach ($SpecMarkData as $key => $value) {
                    $SpecMarkData[$key]['spec_name'] = $spec_sync_mark_data[$value['spec_mark_id']]['spec_name'];
                    $SpecMarkData[$key]['spec_value'] = $spec_sync_value_data[$value['spec_value_id']]['spec_value'];
                }
                model('ProductSpecData')->saveAll($SpecMarkData);
            }
            /* END */

            $this->success('更新成功！');
        }

        // 查询规格数据
        $PresetData = $this->product_spec_preset_db->where('lang',$this->admin_lang)->order('sort_order asc, preset_id asc')->select();
        // 数组转化
        $ResultData = $this->ProductSpecLogic->GetPresetData($PresetData);
        // 获取预设规格中最大的标记MarkId
        $PresetMarkId = model('ProductSpecPreset')->GetMaxPresetMarkId();
        // 加载参数
        $this->assign('info', $ResultData);
        $this->assign('PresetMarkId', $PresetMarkId);
        return $this->fetch('spec_template');
    }

    // 删除规格名称\规格值
    public function spec_delete()
    {
        if (IS_AJAX_POST) {
            $post = input('post.'); 
            $where = $this->ProductSpecLogic->GetDeleteSpecWhere($post);
            if (!empty($where)) {
                $result = $this->product_spec_preset_db->where($where)->delete();
                if (!empty($result)) {
                    $this->success('删除成功！');
                }
            }
            $this->error('删除失败！');
        }
    }

    // 选中规格名称，追加html到页面展示
    public function spec_select()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');

            // 当选中的规格名称超过三个，不允许再添加
            if (3 == count(session('spec_arr'))) {
                $this->error('最多只能添加三种规格大类！');
            }

            // 获取预设规格标记ID数组
            $PresetMarkIdArray = $this->ProductSpecLogic->GetPresetMarkIdArray($post);

            // 拼装预设名称下拉选项
            if (!empty($PresetMarkIdArray)) {
                // 添加选中的规格数据
                model('ProductSpecData')->PresetSpecAddData($post);
                // 拼装更新预设名称下拉选项
                $Result = $this->ProductSpecLogic->GetPresetNameOption($PresetMarkIdArray, $post);
            }else{
                $this->error('最多只能添加三种规格大类！');
            }
            
            if (isset($post['aid']) && !empty($post['aid'])) {
                $ResultData = $this->ProductSpecLogic->GetPresetValueOption('', $post['spec_mark_id'], $post['aid'], 2);
                $PresetName = $ResultData['PresetName'];
                $PresetValueOption = $ResultData['PresetValueOption'];
            }else{
                // 拼装预设值下拉选项
                $PresetValue = $this->product_spec_preset_db->where('preset_mark_id','IN',$post['preset_mark_id'])->field('preset_id,preset_name,preset_value')->select();
                $PresetName = $PresetValue[0]['preset_name'];
                $PresetValueOption = $this->ProductSpecLogic->GetPresetValueOption($PresetValue);
            }

            if (isset($post['aid']) && !empty($post['aid'])) {
                // 结果返回
                $ReturnHtml = [
                    'preset_name'         => $PresetName,
                    'preset_name_option'  => $Result['Option'],
                    'spec_mark_id_arr'    => $Result['MarkId'],
                    'preset_value_option' => $PresetValueOption,
                ];
            }else{
                // 结果返回
                $ReturnHtml = [
                    'preset_name'         => $PresetName,
                    'preset_name_option'  => $Result['Option'],
                    'preset_mark_id_arr'  => $Result['MarkId'],
                    'preset_value_option' => $PresetValueOption,
                ];
            }
            $this->success('加载成功！', null, $ReturnHtml);
        }
    }

    // 当规格库更新后，调用此方式及时更新选择预设规格的下拉框信息及规格框信息
    public function update_spec_info()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');

            // 拼装更新预设名称下拉选项
            $ResultData = $this->ProductSpecLogic->GetPresetNameOption($post['preset_mark_id_arr']);

            // 获取规格拼装后的html表格
            $ResultArray = $this->ProductSpecLogic->GetPresetSpecAssembly($post);

            // 结果返回
            if (isset($post['aid']) && !empty($post['aid'])) {
                $ReturnHtml = [
                    'HtmlTable' => $ResultArray['HtmlTable'],
                    'spec_name_option' => $ResultData['Option'],
                    'spec_mark_id_arr' => $ResultArray['PresetMarkIdArray'],
                ];
            }else{
                // 拼装更新预设名称下拉选项
                $where = [
                    'preset_mark_id' => ['IN', $post['preset_mark_id_arr']],
                ];
                $PresetData = $this->product_spec_preset_db->where($where)->order('preset_id asc')->select();
                $sessionData = session('spec_arr');
                foreach ($PresetData as $key => $value) {
                    if (!empty($sessionData[$value['preset_mark_id']])) {
                        if (in_array($value['preset_id'], $sessionData[$value['preset_mark_id']])) {
                            unset($PresetData[$key]);
                        }
                    }
                }

                $PresetData = group_same_key($PresetData, 'preset_mark_id');
                $result = [];
                foreach ($PresetData as $key => $value) {
                    $result[$key] .= "<option value='0'>选择规格值</option>";
                    if(!empty($value)){
                        foreach($value as $sub_value){
                            $result[$key] .= "<option value='{$sub_value['preset_id']}'>{$sub_value['preset_value']}</option>";
                        }
                    }
                }
                
                $ReturnHtml = [
                    'HtmlTable' => $ResultArray['HtmlTable'],
                    'preset_name_option' => $ResultData['Option'],
                    'preset_mark_id_arr' => $ResultArray['PresetMarkIdArray'],
                    'preset_value_id'     => explode(',', $post['preset_mark_id_arr']),
                    'preset_value_option' => $result,
                ];
            }
            $this->success('更新成功！', null, $ReturnHtml);
        }
    }

    // 获取或更新规格组合的数据
    // preset_id：预设值ID
    // preset_mark_id：预设参数标记ID，同一预设规格名称下的所有规格值统一使用，可理解为规格名称唯一ID。
    public function assemble_spec_data()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');

            // 刷新或重新进入产品添加页则清除关于产品session
            if (isset($post['initialization']) && !empty($post['initialization'])) {
                session('spec_arr', null);
                $this->success('初始化完成');
            }

            // 若清除一整条规格信息则清除session中相应的数据并且重置规格名称下拉框选项
            $ResultArray = $this->ProductSpecLogic->GetResetPresetNameOption($post);

            // 删除单个规格值则清除session对应的值
            $ValueArray  = $this->ProductSpecLogic->ClearSpecValueID($post);

            // 把session中的数据和提交的数据组合
            $SpecArray   = $this->ProductSpecLogic->GetSessionPostArrayMerge($post);
            if (isset($SpecArray['error']) && !empty($SpecArray['error'])) {
                $this->error($SpecArray['error']);
            }

            // 获取规格拼装后的html表格
            if (isset($post['aid']) && !empty($post['aid'])) {
                // 编辑
                $HtmlTable = $this->ProductSpecLogic->SpecAssemblyEdit($SpecArray, $post['aid']);
            }else{
                // 新增
                $HtmlTable = $this->ProductSpecLogic->SpecAssembly($SpecArray);
            }

            if (!empty($ValueArray['Option'])) {
                // 删除规格值后的规格值下拉框
                $PresetValueOption = $ValueArray['Option'];
                $ResultValue['Value'] = null;
            }else{
                $ResultValue = model('ProductSpecPreset')->GetPresetNewData(session('spec_arr'), $post);
                // 获取新增规格值后的下拉框
                if (empty($post['aid'])) {
                    $PresetValueOption = $this->ProductSpecLogic->GetPresetValueOption($ResultValue['Option']);
                }else{
                    $PresetValueOption = $ResultValue['Option'];
                }
            }

            // 返回数据
            $ReturnData = [
                'HtmlTable'         => $HtmlTable,
                'PresetNameOption'  => $ResultArray['Option'],
                'PresetMarkIdArray' => $ResultArray['MarkId'],
                'SelectPresetValue' => $ResultValue['Value'],
                'PresetValueOption' => $PresetValueOption,
            ];
            $this->success('加载成功！', null, $ReturnData);
        }
    }

    // 同步规格值到产品规格中，刷新规格值下拉框
    public function refresh_spec_value()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 是否提交完整数据
            if (empty($post['spec_mark_id']) || empty($post['aid'])) $this->error('数据有误，同步失败，请刷新重试！');

            /*查询产品已选规格数据*/
            $where = [
                'aid' => $post['aid'],
                'spec_mark_id' => $post['spec_mark_id'],
            ];
            $SpecData = Db::name('product_spec_data')->where($where)->order('spec_value_id asc')->select();
            // 以规格值ID为键名
            $SpecData = group_same_key($SpecData, 'spec_value_id');
            /* END */

            /*查询规格库数据*/
            $where = [
                'preset_mark_id' => $post['spec_mark_id'],
            ];
            $PresetData = $this->product_spec_preset_db->where($where)->order('preset_id asc')->select();
            /* END */

            // 初始化数组
            $AddData = $UpData = $SpecIds = $UpSpecWhere = $UpSpecName = [];
            // 数据处理
            foreach ($PresetData as $pd_k => $pd_v) {
                if (!empty($SpecData[$pd_v['preset_id']]) && $pd_v['preset_name'] != $SpecData[$pd_v['preset_id']][0]['spec_name']) {
                    // 更新规格名称数组
                    if (empty($UpSpecWhere) && empty($UpSpecName)) {
                        $UpSpecWhere = [
                            'aid'          => $post['aid'],
                            'spec_mark_id' => $pd_v['preset_mark_id'],
                        ];
                        $UpSpecName = [
                            'spec_name' => $pd_v['preset_name'],
                        ];
                    }
                }

                if (empty($SpecData[$pd_v['preset_id']])) {
                    // 添加规格值数据
                    $AddData[] = [
                        'aid'            => $post['aid'],
                        'spec_mark_id'   => $pd_v['preset_mark_id'],
                        'spec_name'      => $pd_v['preset_name'],
                        'spec_value_id'  => $pd_v['preset_id'],
                        'spec_value'     => $pd_v['preset_value'],
                        'spec_is_select' => 0,
                        'lang'           => $this->admin_lang,
                        'add_time'       => getTime(),
                        'update_time'    => getTime(),
                    ];
                } else if (!empty($SpecData[$pd_v['preset_id']]) && $pd_v['preset_value'] != $SpecData[$pd_v['preset_id']][0]['spec_value']) {
                    // 更新规格值数据
                    $UpData[] = [
                        'spec_id'        => $SpecData[$pd_v['preset_id']][0]['spec_id'],
                        'spec_mark_id'   => $pd_v['preset_mark_id'],
                        'spec_name'      => $pd_v['preset_name'],
                        'spec_value_id'  => $pd_v['preset_id'],
                        'spec_value'     => $pd_v['preset_value'],
                        'update_time'    => getTime(),
                    ];
                    // 删除产品已选规格表中对应需要更新的规格值数据
                    unset($SpecData[$pd_v['preset_id']]);
                } else {
                    // 删除规格库中不存在的规格值数据
                    unset($SpecData[$pd_v['preset_id']]);
                }
            }

            // 合并添加、编辑数据，统一处理
            $SaveData = array_merge($AddData, $UpData);
            
            /*处理需要删除的规格值数据*/
            if (!empty($SpecData)) {
                $DelIsSelect = 0;
                foreach ($SpecData as $key => $value) {
                    $SpecIds[] = $value[0]['spec_id'];
                    if (1 == $value[0]['spec_is_select']) {
                        $DelIsSelect = 1;
                    }
                    $spec_mark_id = $value[0]['spec_mark_id'];
                }
            }
            /* END */

            $HtmlTable = $SpecMarks = '';
            if (!empty($SpecIds)) {
                // 删除废弃的规格值数据
                Db::name('product_spec_data')->where('spec_id', 'IN', $SpecIds)->delete();
                if (1 == $DelIsSelect) {
                    session('spec_arr', null);
                    $SpecWhere = [
                        'aid' => $post['aid'],
                        'lang' => $this->admin_lang,
                        'spec_is_select' => 1,// 已选中的
                    ];
                    $order = 'spec_value_id asc, spec_id asc';
                    $product_spec_data = Db::name('product_spec_data')->where($SpecWhere)->order($order)->select();
                    if (!empty($product_spec_data)) {
                        $spec_arr_new = group_same_key($product_spec_data, 'spec_mark_id');
                        $DelAllSpec = $spec_mark_id;
                        foreach ($spec_arr_new as $key => $value) {
                            $spec_mark_id_arr[] = $key;
                            for ($i=0; $i<count($value); $i++) {
                                $spec_arr_new[$key][$i] = $value[$i]['spec_value_id'];
                            }
                            if ($spec_mark_id == $key) {
                                $DelAllSpec = 0;
                            }
                        }

                        session('spec_arr', $spec_arr_new);
                        $HtmlTable = $this->ProductSpecLogic->SpecAssemblyEdit($spec_arr_new, $post['aid']);
                        $SpecMarks = implode(',', $spec_mark_id_arr);
                    }
                }
            }

            if (!empty($SaveData)) {
                // 批量保存更新新规格
                model('ProductSpecData')->saveAll($SaveData);
            }

            if (!empty($UpSpecWhere) && !empty($UpSpecName)) {
                // 更新当前产品下对应的规格名称
                Db::name('product_spec_data')->where($UpSpecWhere)->update($UpSpecName);
                if (empty($UpData)) {
                    $UpData[0] = [
                        'spec_name' => $UpSpecName['spec_name'],
                        'spec_mark_id' => $UpSpecWhere['spec_mark_id']
                    ];
                }
            }

            $ValueOption = $this->ProductSpecLogic->GetPresetValueOption('', $post['spec_mark_id'], $post['aid']);
            $ResultData = [
                'UpData'      => $UpData,
                'SpecIds'     => $SpecIds,
                'HtmlTable'   => $HtmlTable,
                'SpecMarks'   => $SpecMarks,
                'DelAllSpec'  => $DelAllSpec,
                'ValueOption' => $ValueOption,
            ];
            $this->success('同步成功，规格值已刷新！', null, $ResultData);
        }
    }

    // 新增产品时更新同步规格数据
    public function refresh_preset_value()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            if (!empty($post)) {
                $HtmlTable = $DelAllPreset = $PresetData = $MarkData = '';
                if ((isset($post['mark_mark_ids']) && !empty($post['mark_mark_ids'])) || (isset($post['mark_preset_ids']) && !empty($post['mark_preset_ids']))) {
                    if (!empty($post['mark_mark_ids'])) {
                        $MarkData = $this->product_spec_preset_db->where('preset_mark_id', 'IN', $post['mark_mark_ids'])->field('preset_mark_id, preset_name')->select();
                    }
                    if (!empty($post['mark_preset_ids'])) {
                        $PresetData = $this->product_spec_preset_db->where('preset_id', 'IN', $post['mark_preset_ids'])->field('preset_id, preset_value')->select();
                    }
                } else {
                    $DelAllPreset = 0;
                    $spec_arr_ses = session('spec_arr');
                    foreach ($spec_arr_ses[$post['preset_mark_id']] as $key => $value) {
                        if ($value == $post['preset_id']) {
                            unset($spec_arr_ses[$post['preset_mark_id']][$key]);
                        }
                    }
                    if (empty($spec_arr_ses[$post['preset_mark_id']])) {
                        unset($spec_arr_ses[$post['preset_mark_id']]);
                        $count = $this->product_spec_preset_db->where('preset_mark_id', $post['preset_mark_id'])->count();
                        if (empty($count)) {
                            $DelAllPreset = 1;
                        }
                    }
                    session('spec_arr',$spec_arr_ses);
                    $HtmlTable = $this->ProductSpecLogic->SpecAssembly($spec_arr_ses);
                }

                $ResultData = [
                    'MarkData'     => $MarkData,
                    'HtmlTable'    => $HtmlTable,
                    'PresetData'   => $PresetData,
                    'DelAllPreset' => $DelAllPreset,
                ];
                $this->success('同步成功！', null, $ResultData);
            }
        }
    }

    // 检查是否最新的购物车标签
    public function VerifyLatestTemplate()
    {
        // 验证最新模板
        $ResultData = VerifyLatestTemplate();
        if (empty($ResultData)) {
            // 更新开启多规格
            getUsersConfigData('shop', ['shop_open_spec' => 1]);
            // 返回提示
            $this->success('模板检测通过，规格已开启！');
        }else{
            if (5 == count($ResultData)) {
                $msg = '未检测到规格标签，请根据提示手工调用后再重新验证！';
            }else{
                $msg = '规格标签缺少变量：<br/><span style="color: red;">'.implode('， ', $ResultData).'</span><br/>请检查模板核实后再次验证！';
            }
            // 更新关闭多规格
            getUsersConfigData('shop', ['shop_open_spec' => 0]);
            // 返回提示
            $this->error($msg);
        }
    }

    // 暂时已废弃，暂时不清理删除代码
    public function FindSmptConfig() {
        $Smtp = tpCache('smtp');
        if (empty($Smtp['smtp_server']) || empty($Smtp['smtp_port']) || empty($Smtp['smtp_user']) || empty($Smtp['smtp_pwd']) || empty($Smtp['smtp_from_eamil'])) {
            $this->error('邮箱配置尚未配置完成，前往配置？', url('System/api_conf'));
        } else {
            // tpCache('smtp', [input('post.field') => 1]);
            $this->success('配置完成');
        }
    }

    // 未付款订单改价
    public function order_change_price()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            if (empty($post['order_id']) || empty($post['order_amount'])) $this->error('操作错误，刷新重试');
            // 更新条件
            $where = [
                'order_id' => $post['order_id'],
                'order_status' => 0
            ];
            // 更新数据
            $Code = date('Ymd') . getTime() . rand(10, 100);
            $update = [
                'order_code' => $Code,
                'update_time' => getTime(),
                'order_amount' => $post['order_amount']
            ];
            // 更新操作
            $ResultID = $this->shop_order_db->where($where)->update($update);
            // 更新后续操作
            if (!empty($ResultID)) {
                // 添加订单的操作记录
                $action_note = '应付金额改为：' . $post['order_amount'] . '，原价：' . $post['order_amount_old'];
                AddOrderAction($post['order_id'], 0, session('admin_id'), 0, 0, 0, '商家改价！', $action_note);
                $this->success('改价完成');
            } else {
                $this->error('改价失败，刷新重试');
            }
        }
    }

    /**
     * 营销功能
     * @return [type] [description]
     */
    public function market_index()
    {
        return $this->fetch();
    }
}