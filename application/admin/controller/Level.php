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
 * Date: 2019-06-21
 */

namespace app\admin\controller;

use think\Page;
use think\Db;
use think\Config;

class Level extends Base {

    /**
     * 构造方法
     */
    public function __construct(){
        parent::__construct();
        /*会员中心数据表*/
        $this->users_db        = Db::name('users');         // 会员信息表
        $this->users_level_db  = Db::name('users_level');   // 会员等级表
        $this->users_money_db  = Db::name('users_money');   // 会员充值表
        $this->users_type_manage_db  = Db::name('users_type_manage');   // 会员等级表
        /*结束*/

        // 是否开启支付功能设置
        $UsersConfigData = getUsersConfigData('all');
        $this->assign('userConfig',$UsersConfigData);

        // 模型是否开启
        $channeltype_row = \think\Cache::get('extra_global_channeltype');
        $this->assign('channeltype_row',$channeltype_row);
    }

    /**
     *  列表
     */
    public function index()
    {
        // 会员级别
        $list = $this->users_level_db->where(['is_system'=>0])->order('level_value asc, level_id asc')->select();
        $this->assign('list',$list);

        // 会员升级期限
        $member_limit_arr = Config::get('global.admin_member_limit_arr');
        $this->assign('member_limit_arr',$member_limit_arr);

        // 会员升级产品分类
        $users_type = $this->users_type_manage_db->order('sort_order asc, type_id asc')->select();
        $this->assign('users_type',$users_type);

        return $this->fetch();
    }

    /**
     *  会员升级业务列表
     */
    public function upgrade_index()
    {
        // 查询条件
        $where = [
            // 升级消费
            'a.cause_type' => 0,
            // 已完成状态
            'a.status'     => 2,
            // 语言标识
            'a.lang'       => $this->admin_lang,
        ];
        // 订单号查询
        $order_number = input('order_number/s');
        if (!empty($order_number)) {
            $where['order_number'] = array('LIKE', "%{$order_number}%");
        }

        //时间检索条件
        $begin    = strtotime(input('param.add_time_begin/s'));
        $end    = input('param.add_time_end/s');
        !empty($end) && $end .= ' 23:59:59';
        $end    = strtotime($end);
        // 时间检索
        if ($begin > 0 && $end > 0) {
            $where['a.add_time'] = array('between',"$begin,$end");
        } else if ($begin > 0) {
            $where['a.add_time'] = array('egt', $begin);
        } else if ($end > 0) {
            $where['a.add_time'] = array('elt', $end);
        }

        // 查询满足要求的总记录数
        $count = $this->users_money_db->alias('a')->where($where)->count('moneyid');
        $total_money = $this->users_money_db->alias('a')->where($where)->value("SUM(`money`) as `total_money`");
        $this->assign('total_money', $total_money);

        // 实例化分页类 传入总记录数和每页显示的记录数
        $pageObj = new Page($count, config('paginate.list_rows'));
        // 订单表数据查询
        $list = $this->users_money_db->field('a.*,b.username')
            ->alias('a')
            ->join('__USERS__ b', 'a.users_id = b.users_id', 'LEFT')
            ->where($where)
            ->order('moneyid desc')
            ->limit($pageObj->firstRow.','.$pageObj->listRows)
            ->select();
        foreach ($list as $key => $value) {
            // 反序列化参数
            $list[$key]['cause'] = unserialize($value['cause']);
        }

        // 分页显示
        $pageStr = $pageObj->show();
        $this->assign('page', $pageStr);
        $this->assign('pager', $pageObj);

        // 订单数据
        $this->assign('list',$list);

        // 支付方式
        $pay_method_arr = config('global.pay_method_arr');
        $this->assign('pay_method_arr',$pay_method_arr);

        // 支付状态
        $pay_status_arr = config('global.pay_status_arr');
        $this->assign('pay_status_arr',$pay_status_arr);

        //是否开启文章付费
        $channelRow = Db::name('channeltype')->where('nid', 'article')->find();
        $channelRow['data'] = json_decode($channelRow['data'], true);
        $this->assign('channelRow',$channelRow);
        
        return $this->fetch();
    }
    
    /**
     * 删除会员升级
     */
    public function upgrade_del()
    {
        if (IS_POST) {
            $id_arr = input('del_id/a');
            $id_arr = eyIntval($id_arr);
            if(!empty($id_arr)){
                $result = Db::name('users_money')->field('order_number')
                    ->where([
                        'moneyid'    => ['IN', $id_arr],
                        'cause_type'    => 0,
                        'lang'  => $this->admin_lang,
                    ])->select();
                $order_number_list = get_arr_column($result, 'order_number');

                $r = Db::name('users_money')->where([
                        'moneyid'    => ['IN', $id_arr],
                        'cause_type'    => 0,
                        'lang'  => $this->admin_lang,
                    ])
                    ->cache(true, null, "users_money")
                    ->delete();
                if($r !== false){
                    adminLog('删除会员升级记录：'.implode(',', $order_number_list));
                    $this->success('删除成功');
                }
            }
            $this->error('删除失败');
        }
        $this->error('非法访问');
    }
    
    // 删除
    public function level_type_del()
    {
        $type_id = input('type_id/d');
        if (IS_AJAX_POST && !empty($type_id)) {
            $where = [
                'type_id' => $type_id,
                'lang'    => $this->admin_lang,
            ];
            $type_name_list = $this->users_type_manage_db->where($where)->getField('type_name');
            // 删除会员升级级别
            $return = $this->users_type_manage_db->where($where)->delete();
            if ($return) {
                adminLog('删除会员升级级别：'.$type_name_list);
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }
        $this->error('参数有误');
    }

    // 新增/修改
    public function add_level_data()
    {
        if (IS_POST) {
            $post = input('post.');

            // 处理新增数据
            $AddLevelData = [];
            foreach ($post['type_name'] as $key => $value) {
                $type_id    = $post['type_id'][$key];
                $type_name  = trim($value);
                $level_id   = $post['level_id'][$key];
                $price      = $post['price'][$key];
                $limit_id   = $post['limit_id'][$key];
                $sort_order = $post['sort_order'][$key];

                if (empty($type_name))  $this->error('产品名称不可为空');
                if (empty($level_id))   $this->error('会员级别不可为空');
                if (empty($price))      $this->error('产品价格不可为空');
                if (empty($limit_id))   $this->error('会员期限不可为空');

                $AddLevelData[] = [
                    'type_id'     => $type_id,
                    'type_name'   => $type_name,
                    'level_id'    => $level_id,
                    'price'       => $price,
                    'limit_id'    => $limit_id,
                    'sort_order'  => $sort_order,
                    'update_time' => getTime(),
                ];

                if (empty($type_id)) {
                    $AddLevelData[$key]['lang']     = $this->admin_lang;
                    $AddLevelData[$key]['add_time'] = getTime();
                    unset($AddLevelData[$key]['type_id']);
                }
            }

            if (!empty($AddLevelData)) {
                $ReturnId = model('UsersTypeManage')->saveAll($AddLevelData);
            }

            // 返回
            if (!empty($ReturnId)) {
                $this->success('保存成功');
            }else{
                $this->error('保存失败');
            }
        }
    }
}