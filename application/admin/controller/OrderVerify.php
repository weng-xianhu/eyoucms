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

class OrderVerify extends Base
{
    private $UsersConfigData = [];

    /**
     * 构造方法
     */
    public function __construct()
    {
        parent::__construct();
//        $functionLogic = new \app\common\logic\FunctionLogic;
//        $functionLogic->validate_authorfile(20);
        $this->shop_order_db = Db::name('shop_order');              // 订单主表
        $this->shop_order_details_db = Db::name('shop_order_details');      // 订单明细表
    }

    /**
     *  核销订单列表
     */
    public function index()
    {
        // 初始化数组和条件
        $where = [
            'a.merchant_id' => 0,
            'a.prom_type' => 1,
            'a.order_status' => ['in',[2,3]]
        ];
        // // 支付方式查询
        // $pay_name = input('pay_name/s');
        // if (!empty($pay_name)) $where['a.pay_name'] = $pay_name;
        // $this->assign('pay_name', $pay_name);

        // // 订单下单终端查询
        // $order_terminal = input('order_terminal/d');
        // if (!empty($order_terminal)) $where['a.order_terminal'] = $order_terminal;
        // $this->assign('order_terminal', $order_terminal);

        // // 商品类型查询
        // $contains_virtual = input('contains_virtual/d');
        // if (!empty($contains_virtual)) $where['a.contains_virtual'] = $contains_virtual;
        // $this->assign('contains_virtual', $contains_virtual);

        // // 下单时间查询
        // $add_time = input('param.add_time/s');
        // if (!empty($add_time)) {
        //    $add_time = explode('~', $add_time);
        //    $start = strtotime(rtrim($add_time[0]));
        //    $finish = strtotime(rtrim($add_time[1]).' 23:59:59');
        //    $where['a.add_time'] = ['between', "$start, $finish"];
        // }

        // 核销码查询
        $verify_code = input('verify_code/s');
        if (!empty($verify_code)) {
            $verify_code = str_replace(' ','',$verify_code);
            $where['b.verify_code'] = ['LIKE', "%{$verify_code}%"];
        }

        // 核销状态查询
        $status = input('status/d');
        if (!empty($status)) $where['b.status'] = $status;

        // 分页查询
        $count = $this->shop_order_db->alias('a')
            ->join('shop_order_verify b','a.order_id = b.order_id','LEFT')
            ->where($where)->count('a.order_id');
        $pageObj = new Page($count, config('paginate.list_rows'));

        // 订单主表数据查询
        $list = $this->shop_order_db->alias('a')
            ->field('a.*,b.*, c.username as u_username, c.nickname as u_nickname, c.mobile as u_mobile, b.mobile')
            ->where($where)
            ->join('shop_order_verify b','a.order_id = b.order_id','LEFT')
            ->join('__USERS__ c', 'a.users_id = c.users_id', 'LEFT')
            ->order('a.order_id desc')
            ->limit($pageObj->firstRow . ',' . $pageObj->listRows)
            ->select();

        $order_ids = get_arr_column($list,'order_id');
        // 处理订单详情数据
        $where = [
            'a.order_id' => ['IN', $order_ids]
        ];
        $DetailsData = $this->shop_order_details_db->alias('a')
            ->field('a.*, b.service_id, b.status')
            ->where($where)
            ->join('__SHOP_ORDER_SERVICE__ b', 'a.details_id = b.details_id', 'LEFT')
            ->order('details_id asc')
            ->select();
        $ArchivesData = get_archives_data($DetailsData, 'product_id');
        foreach ($DetailsData as $key => $value) {
            // 商品链接
            $value['arcurl'] = get_arcurl($ArchivesData[$value['product_id']]);
            // 商品图片处理
            $value['litpic'] = handle_subdir_pic(get_default_pic($value['litpic']));
            // 售后信息处理
            $value['service_id'] = !empty($value['service_id']) ? $value['service_id'] : 0;
            $value['status'] = !empty($value['status']) ? $value['status'] : 0;
            $value['status_name'] = !empty($value['status']) && in_array($value['status'], [6, 7]) ? '维权完成' : '维权中';
            // 产品属性处理
            $goodsData = unserialize($value['data']);
            $value['product_attr'] = !empty($goodsData['product_attr']) ? $goodsData['product_attr'] : '';
            $value['product_spec'] = !empty($goodsData['product_spec']) ? $goodsData['product_spec'] : '';
            $value['product_spec'] = !empty($goodsData['product_spec']) ? "<span class='eyou_product_spec'>".str_replace("；", "</span><span class='eyou_product_spec'>", $goodsData['product_spec'])."</span>" : '';

            $DetailsData[$key] = $value;
        }

        // 把订单详情数据植入订单数据
        $defaultDetails = [
            'details_id' => 0,
            'order_id' => 0,
            'users_id' => 0,
            'product_id' => 0,
            'product_name' => '',
            'num' => 0,
            'data' => '',
            'product_price' => 0,
            'prom_type' => 0,
            'litpic' => get_default_pic(),
            'apply_service' => 0,
            'is_comment' => 0,
            'add_time' => 0,
            'update_time' => 0,
            'service_id' => 0,
            'status' => 0,
            'status_name' => '',
            'arcurl' => '',
        ];
        // 把订单详情数据植入订单数据
        $DetailsDataGroup = group_same_key($DetailsData, 'order_id');
        foreach ($list as $key => $value) {
            // 处理会员昵称
            $value['u_nickname'] = !empty($value['u_nickname']) ? $value['u_nickname'] : $value['u_username'];
            // 处理订单详情数据
            $value['Details'] = $DetailsDataGroup[$value['order_id']];
            if (empty($value['Details'])) $value['Details'] = [$defaultDetails];
            // 商品条数
            $value['rowspan'] = count($value['Details']);
            // 添加时间
            $value['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
            // 更新时间
            $value['update_time'] = date('Y-m-d H:i:s', $value['update_time']);

            $list[$key] = $value;
        }

        // 分页显示输出
        $pageStr = $pageObj->show();
        // 获取订单方式名称
        $pay_method_arr = Config::get('global.pay_method_arr');
        // 获取订单状态
        $admin_order_status_arr = Config::get('global.admin_order_status_arr');

        // 数据加载
        $this->assign('list', $list);
        $this->assign('page', $pageStr);
        $this->assign('pager', $pageObj);
        $this->assign('pay_method_arr', $pay_method_arr);
        $this->assign('admin_order_status_arr', $admin_order_status_arr);

        return $this->fetch();
    }

    // 核销台
    public function verification()
    {
        $verify_code = input('param.verify_code/s');
        if (!empty($verify_code)) {
            $verify_code = str_replace(' ','',$verify_code);
            $where['verify_code'] = ['LIKE', "%{$verify_code}%"];
            $verify_data = Db::name('shop_order_verify')->where($where)->find();

            $OrderData = $this->shop_order_db->find($verify_data['order_id']);
            $DetailsData = $this->shop_order_details_db->where('order_id', $OrderData['order_id'])->select();
            // 处理订单详细表数据处理
            foreach ($DetailsData as $key => $value) {
                if ($value['prom_type'] == 1) $OrderData['prom_type_virtual'] = true;
                // 商品规格、属性处理
                $goodsData = unserialize($value['data']);
                $value['product_attr'] = !empty($goodsData['product_attr']) ? $goodsData['product_attr'] : '';
                $value['product_spec'] = !empty($goodsData['product_spec']) ? "<span class='eyou_product_spec'>".str_replace("；", "</span><span class='eyou_product_spec'>", $goodsData['product_spec'])."</span>" : '';
                // $value['arcurl'] = get_arcurl($array_new[$value['product_id']]);
                $value['litpic'] = handle_subdir_pic($DetailsData[$key]['litpic']);
                $value['subtotal'] = sprintf("%.2f", floatval($value['product_price']) * floatval($value['num']));

                $DetailsData[$key] = $value;
            }
            $this->assign('verify_data', $verify_data);
            $this->assign('OrderData', $OrderData);
            $this->assign('DetailsData', $DetailsData);

        }
        return $this->fetch();
    }

    //核销操作
    public function verify()
    {
        $order_id = input('param.order_id/d');
        if (!empty($order_id)) {
            $OrderData = $this->shop_order_db->find($order_id);
            if (2 != $OrderData['order_status'] || 1 != $OrderData['prom_type']){
                $this->error('订单不可核销！');
            }
            $r = $this->shop_order_db->where('order_id',$order_id)->update(['order_status'=>3,'update_time'=>getTime(),'confirm_time'=>getTime()]);
            if ($r !== false){
                $admin_id = session('admin_id');
                $update = [
                    'status' => 2,
                    'admin_id' => $admin_id,
                    'verify_time' => getTime(),
                    'update_time'=>getTime()
                ];
                Db::name('shop_order_verify')->where('order_id',$order_id)->update($update);
                AddOrderAction($order_id, 0, $admin_id, 3, 1, 1, '管理员核销订单', '核销订单成功');
                $this->success('核销成功！');
            }
            $this->error('核销失败！');
        } else {
            $this->error('缺少必要参数！');
        }
    }

    // 提货点设置
    public function drive_list()
    {
        $param = input('param.');
        $where['status'] = 0;
        if (!empty($param['keywords'])){
            $where['title|phone'] = ['like',"%{$param['keywords']}%"];
        }
        if (!empty($param['status'])){
            $where['status'] = $param['status'];
        }

        // 分页
        $count = Db::name('pick_up_points')->where($where)->count();
        $Page = new Page($count, config('paginate.list_rows'));
        $show = $Page->show();
        $this->assign('page', $show);
        $this->assign('pager', $Page);

        // 数据查询
        $list = Db::name('pick_up_points')
            ->where($where)
            ->order('id desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();

        $this->assign('list', $list);
        return $this->fetch();
    }

    // 增加提货点
    public function drive_add()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            $validate = [
                ['field' => 'title', 'name' => '请输入提货点名称'],
                // ['field' => 'intro', 'name' => '请输入提货点简介'],
                ['field' => 'phone', 'name' => '请输入提货点手机号'],
                ['field' => 'address', 'name' => '请输入详细地址'],
                ['field' => 'bussiness_time', 'name' => '请输入营业时间 '],
                ['field' => 'point', 'name' => '请选择位置'],
            ];
            $this->validatePost($validate,$post);

            $point = explode(',',$post['point']);
            $post['lng'] = $point[0];
            $post['lat'] = $point[1];
            $post['add_time'] = getTime();
            $post['update_time'] = getTime();
            $r = Db::name('pick_up_points')->insert($post);
            if (false !== $r){
                $this->success('添加成功', url('OrderVerify/drive_list'));
            } else {
                $this->error('操作失败');
            }
        }
        $province_list = get_province_list();
        $this->assign('province_list', $province_list);
        return $this->fetch();
    }

    // 编辑提货点
    public function drive_edit()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            $validate = [
                ['field' => 'title', 'name' => '请输入提货点名称'],
                // ['field' => 'intro', 'name' => '请输入提货点简介'],
                ['field' => 'phone', 'name' => '请输入提货点手机号'],
                ['field' => 'address', 'name' => '请输入详细地址'],
                ['field' => 'bussiness_time', 'name' => '请输入营业时间 '],
                ['field' => 'point', 'name' => '请选择位置'],
            ];
            $this->validatePost($validate,$post);

            $point = explode(',',$post['point']);
            $post['lng'] = $point[0];
            $post['lat'] = $point[1];
            $post['update_time'] = getTime();
            $r = Db::name('pick_up_points')->where('id',$post['id'])->update($post);
            if (false !== $r){
                $this->success('更新成功', url('OrderVerify/drive_list'));
            } else {
                $this->error('操作失败');
            }
        }
        $id = input('id/d');
        $info = Db::name('pick_up_points')->where('id',$id)->find();
        if (empty($info)){
            $this->error('数据不存在!');
        }

        $region['province_list'] = get_province_list();
        if (!empty($info['province'])) $region['city_list'] = get_city_list($info['province']);
        if (!empty($info['city'])) $region['area_list'] = get_area_list($info['city']);
        $this->assign('region', $region);
        $this->assign('info', $info);

        return $this->fetch();
    }

    //提货点删除
    public function drive_del()
    {
        $id = input('del_id/a');
        $id = eyIntval($id);
        if (IS_AJAX_POST && !empty($id)) {
            // 删除统一条件
            $Where = [
                'id' => ['IN', $id],
            ];
            $title = Db::name('pick_up_points')->where($Where)->column('title');

            $return = Db::name('pick_up_points')->where($Where)->delete();
            if ($return) {
                adminLog('删除提货点：' . implode(',', $title));
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
        $this->error('参数有误');
    }

    // 核销员
    public function verifier_list()
    {
        $param = input('param.');
        $where = [];
        if (!empty($param['keywords'])){
            $where['b.username|b.nickname|a.name'] = ['like',"%{$param['keywords']}%"];
        }

        // 分页
        $count = Db::name('verifier')->alias('a')->join('users b','a.users_id = b.users_id')->where($where)->count('a.id');
        $Page = new Page($count, config('paginate.list_rows'));
        $show = $Page->show();
        $this->assign('page', $show);
        $this->assign('pager', $Page);

        // 数据查询
        $list = Db::name('verifier')
            ->field('a.*,b.nickname,b.username,b.head_pic,c.title')
            ->alias('a')
            ->join('users b','a.users_id = b.users_id')
            ->join('pick_up_points c','a.points_id = c.id')
            ->where($where)
            ->order('id desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();
        if (!empty($list)){
            foreach ($list as $k => $v){
                if (empty($v['nickname'])) $list[$k]['nickname'] = $v['username'];
            }
        }
        $this->assign('list', $list);
        return $this->fetch();
    }

    // 增加核销员
    public function verifier_add()
    {
        if (IS_AJAX_POST){
            $post = input('post.');
            $validate = [
                ['field' => 'users_id', 'name' => '请选择用户'],
                ['field' => 'points_id', 'name' => '请选择所属提货点'],
                ['field' => 'name', 'name' => '请输入核销员名称'],
                ['field' => 'mobile', 'name' => '请输入手机号']
            ];
            $this->validatePost($validate,$post);

            $post['add_time'] = $post['update_time'] = getTime();
            $r = Db::name('verifier')->insert($post);
            if (false !== $r){
                $this->success('添加成功', url('OrderVerify/verifier_list'));
            } else {
                $this->error('操作失败');
            }
        }
        $points_list = Db::name('pick_up_points')->where('status',0)->field('id,title')->select();
        $this->assign('points_list', $points_list);

        return $this->fetch();
    }

    // 编辑核销员
    public function verifier_edit()
    {

        if (IS_AJAX_POST) {
            $post = input('post.');
            $validate = [
                ['field' => 'users_id', 'name' => '请选择用户'],
                ['field' => 'points_id', 'name' => '请选择所属提货点'],
                ['field' => 'name', 'name' => '请输入核销员名称'],
                ['field' => 'mobile', 'name' => '请输入手机号']
            ];
            $this->validatePost($validate,$post);

            $post['update_time'] = getTime();
            $r = Db::name('verifier')->where('id',$post['id'])->update($post);
            if (false !== $r){
                $this->success('更新成功', url('OrderVerify/verifier_list'));
            } else {
                $this->error('操作失败');
            }
        }
        $id = input('id/d');
        $info = Db::name('verifier')
            ->field('a.*,b.nickname,b.username,b.head_pic')
            ->alias('a')
            ->join('users b','a.users_id = b.users_id')
            ->where('id',$id)
            ->find();
        if (empty($info)){
            $this->error('数据不存在!');
        }
        if (empty($info['nickname'])) $info['nickname'] = $info['username'];
        $this->assign('info', $info);

        $points_list = Db::name('pick_up_points')->where('status',0)->field('id,title')->select();
        $this->assign('points_list', $points_list);

        return $this->fetch();
    }

    public function verifier_del(){
        $id = input('del_id/a');
        $id = eyIntval($id);
        if (IS_AJAX_POST && !empty($id)) {
            // 删除统一条件
            $Where = [
                'id' => ['IN', $id],
            ];
            $title = Db::name('verifier')->where($Where)->column('name');

            $return = Db::name('verifier')->where($Where)->delete();
            if ($return) {
                adminLog('删除核销员：' . implode(',', $title));
                $this->success('删除成功', url('OrderVerify/verifier_list'));
            } else {
                $this->error('删除失败');
            }
        }
        $this->error('参数有误');
    }

    //选择地图
    public function map()
    {
        return $this->fetch();
    }


    public function validatePost($arr = [],$post = [])
    {
        foreach ($arr as $k => $v){
            if (empty($post[$v['field']])){
                $this->error($v['name']);
            }
        }
        return true;
    }

    /**
     * 会员选择
     * @return [type] [description]
     */
    public function users_select()
    {
        $users_ids = Db::name('verifier')->column('users_id');
        // 查询条件
        $where['is_del'] = 0;
//        $where['is_real'] = 1;
        $where['users_id'] = ['not in',$users_ids];

        $keywords = input('keywords/s');
        if (!empty($keywords)) $where['username|nickname'] = ['LIKE', "%{$keywords}%"];

        $count = Db::name('users')->where($where)->count();
        $pageObj = new Page($count, config('paginate.list_rows'));
        $this->assign('page', $pageObj->show());
        $this->assign('pager', $pageObj);

        $ResultData = Db::name('users')->where($where)->order('users_id desc')
            ->field('users_id,username,nickname,head_pic')
            ->limit($pageObj->firstRow . ',' . $pageObj->listRows)
            ->select();

        foreach ($ResultData as $key => $value) {
            $ResultData[$key]['head_pic'] = get_head_pic($value['head_pic']);
        }

        $this->assign('list', $ResultData);
        return $this->fetch();
    }
}