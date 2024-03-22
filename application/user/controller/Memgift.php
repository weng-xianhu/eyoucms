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
 * Date: 2019-7-3
 */

namespace app\user\controller;

use think\Db;
use think\Config;
use think\Verify;
use think\Page;
use think\Request;

/**
 * 积分兑换
 */
class Memgift extends Base
{
    public function _initialize()
    {
        parent::_initialize();
        
        // 积分兑换是否已在用
        $shopLogic = new \app\admin\logic\ShopLogic;
        $useFunc = $shopLogic->useFuncLogic();
        if (!in_array('memgift', $useFunc)) {
            $this->error('内置功能已废弃！');
        }

        //积分名称
        $score = getUsersConfigData('score');
        $this->score_name = $score['score_name'];
        $this->assign('score_name', $this->score_name);
    }

    // 积分商品列表
    public function users_gift_list()
    {
        //全部积分商品
        $condition = ['stock' => ['gt', 0], 'is_del' => 0, 'status' => 1];
        $list = Db::name('memgift')->field('a.*')
            ->alias('a')
            ->where($condition)
            ->order('a.sort_order asc')
            ->select();
        foreach ($list as $key => $val) {
            $list[$key]['litpic'] = get_default_pic($val['litpic']);
        }
        $this->assign('list', $list);
        $pagesize = config('paginate.list_rows');

        //积分兑换记录列表
        $where = ['a.users_id' => $this->users_id];
        $count = Db::name('memgiftget')->alias('a')->where($where)->count();
        $Page = new Page($count, $pagesize);
        $order_list = Db::name('memgiftget')->alias('a')
            ->field('a.*,b.litpic')
            ->join('memgift b', 'a.gift_id = b.gift_id', 'left')
            ->where($where)
            ->order('a.add_time desc,a.gid desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();
        foreach ($order_list as $key => $val) {
            $val['key'] = ($Page->nowPage - 1) * $pagesize + $key + 1;
            $val['litpic'] = get_default_pic($val['litpic']);
            $order_list[$key] = $val;
        }

        $show = $Page->show();
        $this->assign('page', $show);
        $this->assign('order_list', $order_list);
        $this->assign('pager', $Page);
        return $this->fetch();
    }

    //展示积分商品详情
    public function users_gift_detail()
    {
        $post = input('param.');
        if (empty($post)) {
            $this->error("没有数据");
        }
        $field = Db::name('memgift')->where(['gift_id' => $post['gift_id']])->find();
        if (empty($field)) {
            $this->error("没有数据");
        }
        $field['litpic'] = get_default_pic($field['litpic']);
        $this->assign('field', $field);

        return $this->fetch();
    }

    //兑换实物
    public function users_gift_shiwu()
    {
        $post = input('param.');
        if (IS_POST) {
            $gift_id = !empty($post['gift_id']) ? intval($post['gift_id']) : 0;
            $name = !empty($post['name']) ? $post['name'] : '';
            $mobile = !empty($post['mobile']) ? $post['mobile'] : '';
            $address = !empty($post['address']) ? $post['address'] : '';
            $scores = $this->users['scores'];
            $time = getTime();
            if (empty($gift_id)) {
                $this->error('请选择正确的商品！');
            }
            $row = Db::name("memgift")->where(['gift_id' => $gift_id])->find();
            if (empty($row)) {
                $this->error('商品不存在！');
            }
            $giftname = $row['giftname'];
            $score = $row['score'];
            if (empty($row['status'])) {
                $this->error('该商品目前处于非兑换状态！');
            }
            if ($row['stock'] < 1) {
                $this->error('该商品库存不足！');
            }
            if ($score > $scores) {
                $this->error("您的{$this->score_name}还不够兑换该商品！");
            }
            $insert_data = [
                'giftname' => $giftname,
                'gift_id' => $gift_id,
                'score' => $score,
                'users_id' => $this->users_id,
                'add_time' => $time,
                'update_time' => $time,
                'name' => $name,
                'mobile' => $mobile,
                'address' => $address,
            ];
            $gid = Db::name('memgiftget')->insertGetId($insert_data);
            if ($gid) {
                //日志记录
                $data = [
                    'users_id' => $this->users_id,
                    'info' => "积分兑换,gid={$gid}",
                    'score' => '-' . $score,
                    'type' => 9,
                    'add_time' => getTime(),
                    'update_time' => getTime(),
                    'current_score' => $this->users['scores'],
                    'current_devote' => $this->users['devote'],
                ];
                Db::name('users_score')->insert($data);

                $update_users = Db::name("users")->where(['users_id' => $this->users_id])->update(['scores' => Db::Raw("scores-" . $score), 'update_time' => getTime()]);
                if ($update_users) {
                    $memgift_data = [
                        'num' => Db::raw('num+1'),
                        'stock' => Db::raw('stock-1'),
                        'update_time' => getTime(),
                    ];
                    Db::name("memgift")->where(['gift_id' => $gift_id])->update($memgift_data);
                    $this->success("成功兑换商品，请等待发货！");
                }
            }
            $this->error('兑换失败！');
        }
        $this->assign('post', $post);
        $field = Db::name("memgiftget")->field("name,mobile,address")->where(['users_id' => $this->users_id, 'name' => ['neq', '']])->find();
        $this->assign('field', $field);

        return $this->fetch();
    }

    public function users_gift_vip()
    {
        if (IS_POST) {
            $post = input('param.');
            $gift_id = !empty($post['gift_id']) ? intval($post['gift_id']) : 0;

            if (empty($gift_id)) {
                $this->error('请选择正确的商品！');
            }
            $long = !empty($post['long']) ? intval($post['long']) : 0;
            $scores = $this->users['scores'];
            $row = Db::name('memgift')->where(['gift_id' => $gift_id])->find();
            $type_id = $row['type_id'];

            if (empty($row)) {
                $this->error('商品不存在！');
            }

            $giftname = $row['giftname'];
            $score = $row['score'];
            if (empty($row['status'])) {
                $this->error('该商品目前处于非兑换状态！');
            }
            if ($row['stock'] < 1) {
                $this->error('该商品库存不足！');
            }
            if ($score > $scores) {
                $this->error("您的{$this->score_name}还不够兑换该商品！");
            }

            //才做到这里,下面的逻辑没捋完 2022.07.08 大黄
            //兑换的vip套餐
            $users_type_manage = Db::name('users_type_manage')
                ->where('type_id', $type_id)
                ->find();
            if (empty($long) && $this->users['level_maturity_days'] > 0) {
                if ($users_type_manage['level_id'] != $this->users['level_id']) {
                    $res = [
                        'code' => 2,
                        'msg' => "您已经是{$this->users['level_name']}(有效期:{$this->users['level_maturity_days']}天)啦，还要继续兑换吗?继续兑换将会覆盖原会员!",
                    ];
                    exit(json_encode($res));
                } else {
                    $res = [
                        'code' => 2,
                        'msg' => "您已经是{$this->users['level_name']}(有效期:{$this->users['level_maturity_days']}天)啦，还要继续兑换吗?",
                    ];
                    exit(json_encode($res));
                }
            }

            //会员期限定义数组
            $limit_arr = Config::get('global.admin_member_limit_arr');
            // 会员升级级别
            $limit_id = $users_type_manage['limit_id'];
            // 到期天数
            $maturity_days = $limit_arr[$limit_id]['maturity_days'];
            // 更新会员属性表的数组
            $result = [
                'level' => $users_type_manage['level_id'],
                'update_time' => getTime()
            ];
            //用户当前是某个级别会员
            if ($this->users['level_maturity_days'] > 0) {
                //当前级别与兑换会员级别不同 直接覆盖天数
                if ($users_type_manage['level_id'] != $this->users['level_id']) {
                    $result['level_maturity_days'] = $maturity_days;
                    $result['open_level_time'] = getTime();

                } else {
                    //当前级别与兑换会员级别相同 累加天数
                    $result['level_maturity_days'] = Db::raw('level_maturity_days+' . ($maturity_days));
                }
            } else {
                //不是会员
                $result['level_maturity_days'] = $maturity_days;
                $result['open_level_time'] = getTime();
            }

            $insert_data = [
                'giftname' => $giftname,
                'gift_id' => $gift_id,
                'score' => $score,
                'users_id' => $this->users_id,
                'add_time' => getTime(),
                'update_time' => getTime(),
                'status' => 1
            ];
            $gid = Db::name('memgiftget')->insertGetId($insert_data);
            if ($gid) {
                //-库存  + 兑换次数
                $memgift_update = [
                    'num' => Db::Raw('num+1'),
                    'stock' => Db::Raw('stock-1'),
                    'update_time' => getTime()
                ];
                Db::name('memgift')->where(['gift_id' => $gift_id])->update($memgift_update);
                //日志记录
                $data = [
                    'users_id' => $this->users_id,
                    'info' => "积分兑换,gid={$gift_id}",
                    'score' => '-' . $score,
                    'type' => 9,
                    'add_time' => getTime(),
                    'update_time' => getTime(),
                    'current_score' => $this->users['scores'],
                    'current_devote' => $this->users['devote'],
                ];
                Db::name('users_score')->insert($data);
                $result['scores'] = Db::Raw("scores-" . $score);
                Db::name('users')->where('users_id', $this->users_id)->update($result);

                $this->success('兑换成功!');
            } else {
                $this->error('兑换失败!');
            }
        }
    }
}