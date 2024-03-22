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

use think\Config;
use think\Page;
use think\Db;

class Memgift extends Base
{

    public function _initialize()
    {
        parent::_initialize();
        $functionLogic = new \app\common\logic\FunctionLogic;
        $functionLogic->validate_authorfile(2);

        // 积分兑换是否已在用
        $shopLogic = new \app\admin\logic\ShopLogic;
        $useFunc = $shopLogic->useFuncLogic();
        if (!in_array('memgift', $useFunc)) {
            $this->error('内置功能已废弃！');
        }

        //积分名称
        $score = getUsersConfigData('score');
        $this->score_name = $score['score_name'];
        $this->assign('score', $score);
    }

    //积分商品列表
    public function index()
    {
        $condition = ['is_del' => 0];
        // 获取到所有GET参数
        $param = input('param.');
        if (isset($param['status']) && is_numeric($param['status'])) {
            $condition['status'] = intval($param['status']);
        }
        if (!empty($param['price1']) && !empty($param['price2'])) {
            $condition['score'] = ['between', $param['price1'] . ',' . $param['price2']];
        } else if (!empty($param['price1'])) {
            $condition['score'] = ['egt', $param['price1']];
        } else if (!empty($param['price2'])) {
            $condition['score'] = ['elt', $param['price2']];
        }

        $count = Db::name('memgift')->alias('a')->where($condition)->count();// 查询满足要求的总记录数
        $Page = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = Db::name('memgift')->field('a.*')
            ->alias('a')
            ->where($condition)
            ->order('a.sort_order desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();

        foreach ($list as $key => $val) {
            $list[$key]['litpic'] = get_default_pic($val['litpic']); // 支持子目录
        }
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('list', $list);// 赋值数据集
        $this->assign('pager', $Page);// 赋值分页集

        return $this->fetch();
    }

    //添加礼物
    public function add()
    {
        if (IS_POST) {
            $post = input('post.');
            if ($post['type'] > 0){
                $post['type_id'] = $post['type'];
                $post['type'] = 2;
            }
            $post['add_time'] = getTime();
            $r = Db::name("memgift")->insert($post);
            if ($r !== false) {
                $this->success("添加成功", url("Memgift/index"));
            } else {
                $this->error("添加失败");
            }
        }
        $users_level = Db::name('users_type_manage')
            ->alias('a')
            ->join('users_level b','a.level_id = b.level_id','left')
            ->where(['a.lang'=>$this->admin_lang])
            ->order('sort_order asc')
            ->field('b.level_name,a.level_id,a.limit_id,a.type_id,a.type_name')
            ->select();
        $limit_arr = Config::get('global.admin_member_limit_arr');
        if (!empty($users_level)){
            foreach ($users_level as &$v){
                $v['level_name'] = $v['level_name'].'('.$limit_arr[$v['limit_id']]['limit_name'].')';
            }
        }
        $this->assign('users_level', $users_level);// 赋值分页集
        return $this->fetch();
    }

    //编辑礼物
    public function edit()
    {
        if (IS_POST) {
            $post = input('post.');
            $gift_id = intval($post['gift_id']);
            if ($post['type'] > 0){
                $post['type_id'] = $post['type'];
                $post['type'] = 2;
            }
            $post['update_time'] = getTime();
            $r = Db::name("memgift")->where(['gift_id' => $gift_id])->update($post);
            if ($r !== false) {
                $this->success("修改成功", url("Memgift/index"));
            } else {
                $this->error("修改失败");
            }
        }
        $gift_id = input('gift_id/d');
        $info = Db::name('memgift')->alias('a')->where(['gift_id' => $gift_id])->find();

        $users_level = Db::name('users_type_manage')
            ->alias('a')
            ->join('users_level b','a.level_id = b.level_id','left')
            ->where(['a.lang'=>$this->admin_lang])
            ->order('sort_order asc')
            ->field('b.level_name,a.level_id,a.limit_id,a.type_id,a.type_name')
            ->select();
        $limit_arr = Config::get('global.admin_member_limit_arr');
        if (!empty($users_level)){
            foreach ($users_level as &$v){
                $v['level_name'] = $v['level_name'].'('.$limit_arr[$v['limit_id']]['limit_name'].')';
            }
        }
        $this->assign('users_level', $users_level);
        $this->assign('info', $info);
        return $this->fetch();
    }

    //兑换列表
    public function gift_exchange_list()
    {
        $condition = [];
        // 获取到所有GET参数
        $param = input('param.');
        if (!empty($param['gift_id'])) {
            $condition['a.gift_id'] = $param['gift_id'];
            $syb = 1;
            $this->assign('syb', $syb);
        }
        $status = input('param.status/s', 0);
        if (!empty($status)) {
            if ($status != -1) {
                $condition['a.status'] = $status;
            } else {
                $condition['a.status'] = 0;
            }
        }
        $keywords = input('param.keywords/s', "");
        if (!empty($keywords)) {
            $condition['b.username|b.nickname'] = $keywords;
        }
        $count = Db::name('memgiftget')->alias('a')->join("users b", "a.users_id = b.users_id", 'left')->where($condition)->count();// 查询满足要求的总记录数
        $Page = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = Db::name('memgiftget')->field('a.*,b.username,b.nickname,b.head_pic,b.sex,c.type')
            ->alias('a')
            ->join("users b", "a.users_id = b.users_id", 'left')
            ->join("memgift c", "a.gift_id = c.gift_id", 'left')
            ->where($condition)
            ->order('a.gid desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();

        foreach ($list as $key => $val) {
            $list[$key]['litpic'] = get_default_pic($val['litpic']); // 支持子目录
        }
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('list', $list);// 赋值数据集
        $this->assign('pager', $Page);// 赋值分页集
        //统计
        $count = Db::name('memgiftget')->field('count(*) as count,status')->group('status')->getAllWithIndex('status');
        $this->assign('count', $count);

        return $this->fetch();
    }

    //积分商品发货、退货
    public function give()
    {
        $param = input('param.');
        $gid = !empty($param['gid']) ? $param['gid'] : '';
        $syb = !empty($param['syb']) ? $param['syb'] : '';
        $do = !empty($param['do']) ? $param['do'] : '';
        $row = Db::name("memgiftget")
            ->alias('g')
            ->field("g.*,m.username,t.stock")
            ->join("users m", "m.users_id=g.users_id", "left")
            ->join("memgift t", "t.gift_id=g.gift_id", "left")
            ->where(['g.gid' => $gid])->find();
        $users_id = $row['users_id'];
        $score = $row['score'];
        if ($do == 'give') {        //发货
            Db::name('memgiftget')->where(["gid" => $gid])->update(['status' => 1]);
            Db::name('users_notice')->insert(['title'=>$this->score_name.'兑换商品发货','users_id'=>$users_id,'usernames'=>'','remark'=>'您的'.$this->score_name.'兑换商品发货啦,请注意查收~','add_time'=>getTime(),'update_time'=>getTime()]);
            if (!empty($syb) && $syb == 1) {
                $url = url('Memgift/gift_exchange_list', ['gift_id' => $row['gift_id']]);
            } else {
                $url = url('Memgift/gift_exchange_list');
            }
            $this->success("成功发货！已站内信通知会员", $url);

        } else {      //退回
            $r = Db::name('users')->where(['users_id' => $users_id])->update(['scores' => Db::raw("scores+" . $score)]);
            if ($r !== false) {
                //日志记录
                $users = Db::name('users')->where('users_id', $users_id)->field('scores,devote')->find();
                $data = [
                    'users_id'    => $users_id,
                    'info'        => "{$this->score_name}兑换取消,{$this->score_name}退回,gid={$gid}",
                    'score'       => '+' . $score,
                    'type'        => 9,
                    'add_time'    => getTime(),
                    'update_time' => getTime(),
                    'current_score' => $users['scores'],
                    'current_devote' => $users['devote'],
                ];
                Db::name('users_score')->insert($data);

                Db::name('memgift')->where(['gift_id' => $row['gift_id']])->update(['stock' => Db::raw("stock+1"),'gid' => 0]);
                Db::name('memgiftget')->where(['gid' => $gid])->update(['status' => 2]);

                if (!isset($next)) {
                    Db::name('users_notice')->insert(['title'=>$this->score_name.'兑换商品取消发货','users_id'=>$users_id,'usernames'=>'','remark'=>'您的'.$this->score_name.'兑换商品已取消发货!','add_time'=>getTime(),'update_time'=>getTime()]);

                    $this->success("成功取消发货！已站内信通知会员", url('Memgift/gift_exchange_list'));
                } else {
                    $url = url('Memgift/del', ['next' => 'ok', 'gift_id' => $row['gift_id']]);
                    echo "<script type='text/javascript'>location.href='{$url}';</script>";
                }
            }
        }
    }

    //重发
    public function again()
    {
        $param = input('param.');
        $gid = !empty($param['gid']) ? $param['gid'] : '';
        $gift_id = !empty($param['gift_id']) ? $param['gift_id'] : '';
        $r = Db::name("memgift")->where(['gift_id' => $gift_id])->update(['stock' => Db::raw("stock+1")]);
        if ($r !== false) {
            Db::name("memgiftget")->where(['gid' => $gid])->update(['status' => 3]);

            $this->success("重发成功，该商品库存已补充", url('Memgift/index'));
        } else {
            $this->error("出问题了！");
        }
    }

    //删除积分商品
    public function del()
    {
        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if (IS_POST && !empty($id_arr)) {
            $result = Db::name("memgift")->field("giftname")->where(['gift_id' => ['in', $id_arr]])->select();
            $name_list = get_arr_column($result, 'giftname');

            $r = Db::name("memgift")->where(['gift_id' => ['in', $id_arr]])->cache(true, null, "memgift")
                ->delete();
            if ($r !== false) {
                adminLog('删除区域：' . implode(',', $name_list));
                $this->success('删除成功');
            }
        }
        $this->error('删除失败');
    }
}