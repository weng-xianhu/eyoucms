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
 * Date: 2019-1-25
 */

namespace app\user\controller;

use think\Page;
use think\Db;
use think\Config;
use think\Cookie;
use think\Request;

class Media extends Base
{
    public function _initialize()
    {
        parent::_initialize();
        $this->users_db = Db::name('users');
        $this->media_order_db = Db::name('media_order');
        $this->media_play_record_db = Db::name('media_play_record');

        $functionLogic = new \app\common\logic\FunctionLogic;
        $functionLogic->validate_authorfile(1.5);
    }

    // 视频订单列表页
    /*
    public function index()
    {
        // 定义数组
        $list = [];
        $condition = [
            'a.lang' => $this->home_lang, // 多语言
            'a.users_id' => $this->users_id
        ];

        // 应用搜索条件
        $order_code = input('order_code/s');
        if (!empty($order_code)) $condition['a.order_code'] = ['LIKE', "%{$order_code}%"];

        // 订单状态搜索
        $order_status = input('order_status/s');
        $this->assign('order_status', $order_status);
        if (!empty($order_status)) {
            if (-1 == $order_status) $order_status = 0;
            $condition['a.order_status'] = $order_status;
        }

        // 支付类型
        $PayMethod = Config::get('global.pay_method_arr');

        // 分页
        $count = $this->media_order_db->alias('a')->where($condition)->count();
        $Page = new Page($count, config('paginate.list_rows'));
        $show = $Page->show();
        $this->assign('page', $show);

        // 数据查询
        $list = $this->media_order_db->where($condition)
            ->field('a.*, b.username')
            ->alias('a')
            ->join('__USERS__ b', 'a.users_id = b.users_id', 'LEFT')
            ->order('a.order_id desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        
        $array_new = get_archives_data($list, 'product_id');

        // 订单处理
        foreach ($list as $key => $value) {
            if (!empty($value['order_status']) && 1 == $value['order_status']) {
                $list[$key]['OrderStatusName'] = '已完成';
            } else {
                $list[$key]['OrderStatusName'] = '待付款';

                // 支付结束后返回的URL
                $ReturnUrl = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : url('user/Media/index');
                cookie($this->users_id . '_' . $value['product_id'] . '_EyouMediaViewUrl', $ReturnUrl);

                // 付款地址处理，对ID和订单号加密，拼装url路径
                $Paydata = [
                    'type' => 8,
                    'order_id' => $value['order_id'],
                    'order_code' => $value['order_code']
                ];

                // 先 json_encode 后 md5 加密信息
                $Paystr = md5(json_encode($Paydata));

                // 清除之前的 cookie
                Cookie::delete($Paystr);

                // 存入 cookie
                cookie($Paystr, $Paydata);

                // 跳转链接
                $list[$key]['PaymentUrl'] = urldecode(url('user/Pay/pay_recharge_detail',['paystr'=>$Paystr]));
            }

            if (!empty($value['pay_name'])) {
                $list[$key]['pay_name'] = !empty($PayMethod[$value['pay_name']]) ? $PayMethod[$value['pay_name']] : '第三方支付';
            }

            $arcurl = '';
            $vars = !empty($array_new[$value['product_id']]) ? $array_new[$value['product_id']] : [];
            if (!empty($vars)) {
                $arcurl = urldecode(arcurl('home/Media/view', $vars));
            }
            $list[$key]['arcurl'] = $arcurl;
        }
        // 订单列表
        $this->assign('list', $list);

        // 订单数量查询
        $Where = [
            'users_id' => $this->users_id,
            'lang'     => $this->home_lang,
            'order_status' => 0
        ];
        $PendingPayment = $this->media_order_db->where($Where)->count();
        // 待付款
        $this->assign('PendingPayment', $PendingPayment);

        $Where['order_status'] = 1;
        $Completed = $this->media_order_db->where($Where)->count();
        // 已付款
        $this->assign('Completed', $Completed);

        // 搜索处理
        $hidden = '';
        $ey_config = config('ey_config'); // URL模式
        if (2 == $ey_config['seo_pseudo'] || (1 == $ey_config['seo_pseudo'] && 1 == $ey_config['seo_dynamic_format'])) {
            $hidden .= '<input type="hidden" name="m" value="user" />';
            $hidden .= '<input type="hidden" name="c" value="Media" />';
            $hidden .= '<input type="hidden" name="a" value="index" />';
            $lang = Request::instance()->param('lang/s');
            !empty($lang) && $hidden .= '<input type="hidden" name="lang" value="'.$lang.'" />';
        }
        // 搜索的URL
        $searchurl = url('user/Media/index');
        $search = array(
            'action' => $searchurl,
            'hidden' => $hidden,
        );
        $this->assign('search', $search);

        return $this->fetch('users/media_index');
    }
    */

    /**
     * 播放记录
     */
    public function play_index()
    {
        $condition['a.users_id']    = $this->users_id;
        $condition['b.lang']    = $this->home_lang;
        // $condition['b.users_price'] = ['>', 0];
        // 分页
        $count = $this->media_play_record_db
            ->alias('a')
            ->join('archives b', 'a.aid=b.aid', 'inner')
            ->where($condition)
            ->group('a.aid')->count();

        $Page = $pager = new Page($count, 10);
        $show = $Page->show();
        $this->assign('page', $show);
        $this->assign('pager', $pager);

        $total_field = 'select sum(file_time) as total_time from ' . PREFIX . 'media_file where aid= a.aid';

        // 数据查询
        $list = $this->media_play_record_db
            ->where($condition)
            ->alias('a')
            ->field("a.id as play_id,a.file_id,a.aid,sum(a.play_time) as sum_play_time,max(a.update_time) as last_update_time,c.*,b.*,({$total_field}) as total_time,(sum(a.play_time)/({$total_field})) as process")
            ->join('archives b', 'a.aid=b.aid', 'inner')
            ->join('arctype c', 'b.typeid=c.id', 'left')
            ->group('a.aid')
            ->order('process desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();

        $total_time = 0;
        // 订单处理
        foreach ($list as $key => $val) {
            $total_time              += $val['sum_play_time'];
            $val['process']          = (round($val['process'], 2) * 100) . "%";
            $val['sum_play_time']    = gmSecondFormat($val['sum_play_time'], ':');
            $val['sum_file_time']    = gmSecondFormat($val['total_time'], ':');
            $val['last_update_time'] = date('Y-m-d H:i:s', $val['last_update_time']);
            if (!empty($val['litpic'])) {
                $val['litpic'] = handle_subdir_pic($val['litpic']);
            } else {
                $val['litpic'] = get_default_pic();
            }
            $val['arcurl'] = urldecode(arcurl('home/Media/view', $val));
            $list[$key]    = $val;
        }
        // 订单列表
        $eyou['field']['list'] = $list;
        $total_time = gmSecondFormat($total_time, ':');
        $eyou['field']['total_time'] = $total_time;
        $this->assign('eyou', $eyou);

        $this->assign('delurl', url('user/Media/media_play_del'));
        return $this->fetch('users/users_media_play_index');
    }

    public function media_play_del()
    {
        if (IS_AJAX_POST) {
            $id_arr = input('del_id/a');
            $id_arr = eyIntval($id_arr);
            if (!empty($id_arr)) {
                $where = [
                    'id' => ['IN', $id_arr],
                    'users_id' => $this->users_id,
                ];
                $result = $this->media_play_record_db->where($where)->delete(true);
                if (!empty($result)) $this->success('删除成功');
            }
        }
        $this->error('删除失败');
    }

    // 视频产品购买接口
    public function media_order_buy()
    {
        if (IS_AJAX_POST) {
            // 提交的订单信息判断
            $post = input('post.');
            if (empty($post) || empty($post['aid'])) $this->error('操作异常，请刷新重试');

            // 查询是否已购买
            $where = [
                'order_status' => 1,
                'product_id' => intval($post['aid']),
                'users_id' => $this->users_id
            ];
            $MediaCount = Db::name('media_order')->where($where)->count();
            if (!empty($MediaCount)) $this->error('你已购买过，请直接观看');

            // 查看是否已生成过订单
            $where['order_status'] = 0;
            $mediaOrder = Db::name('media_order')->where($where)->order('order_id desc')->find();

            // 查询视频文档内容
            $where = [
                'is_del' => 0,
                'status' => 1,
                'aid' => $post['aid'],
                'arcrank' => ['>', -1]
            ];
            $list = Db::name('archives')->where($where)->find();
            if (empty($list)) $this->error('操作异常，请刷新重试');
            $list['users_price'] = get_discount_price($this->users, $list['users_price']);

            // 订单生成规则
            $time = getTime();
            $orderCode = date('Y') . $time . rand(10, 100);
            if (!empty($mediaOrder)) {
                $OrderID = $mediaOrder['order_id'];
                // 更新订单信息
                $OrderData = [
                    'order_code'      => $orderCode,
                    'users_id'        => $this->users_id,
                    'mobile'          => !empty($this->users['mobile']) ? $this->users['mobile'] : '',
                    'order_status'    => 0,
                    'order_amount'    => $list['users_price'],
                    'product_id'      => $list['aid'],
                    'product_name'    => $list['title'],
                    'product_litpic'  => get_default_pic($list['litpic']),
                    'lang'            => $this->home_lang,
                    'add_time'        => $time,
                    'update_time'     => $time
                ];
                Db::name('media_order')->where('order_id', $OrderID)->update($OrderData);
            } else {
                // 生成订单并保存到数据库
                $OrderData = [
                    'order_code'      => $orderCode,
                    'users_id'        => $this->users_id,
                    'mobile'          => !empty($this->users['mobile']) ? $this->users['mobile'] : '',
                    'order_status'    => 0,
                    'order_amount'    => $list['users_price'],
                    'pay_time'        => '',
                    'pay_name'        => '',
                    'wechat_pay_type' => '',
                    'pay_details'     => '',
                    'product_id'      => $list['aid'],
                    'product_name'    => $list['title'],
                    'product_litpic'  => get_default_pic($list['litpic']),
                    'lang'            => $this->home_lang,
                    'add_time'        => $time,
                    'update_time'     => $time
                ];
                $OrderID = Db::name('media_order')->insertGetId($OrderData);
            }

            // 保存成功
            if (!empty($OrderID)) {
                // 支付结束后返回的URL
                $return_url = input('param.return_url/s', url('user/Media/index'));
                $ReturnUrl = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $return_url;
                cookie($this->users_id . '_' . $post['aid'] . '_EyouMediaViewUrl', $ReturnUrl);
                // 对ID和订单号加密，拼装url路径
                $Paydata = [
                    'type' => 8,
                    'order_id' => $OrderID,
                    'order_code' => $OrderData['order_code'],
                ];
                // 先 json_encode 后 md5 加密信息
                $Paystr = md5(json_encode($Paydata));
                // 清除之前的 cookie
                Cookie::delete($Paystr);
                // 存入 cookie
                cookie($Paystr, $Paydata);
                // 跳转链接
                $this->success('订单已生成！', urldecode(url('user/Pay/pay_recharge_detail', ['paystr' => $Paystr])));
            }
        } else {
            abort(404);
        }
    }

}