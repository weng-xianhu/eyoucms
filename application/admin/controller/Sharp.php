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
use think\Cache;
use app\admin\logic\ShopLogic;

class Sharp extends Base
{
    public $ShopLogic;

    /**
     * 构造方法
     */
    public function _initialize()
    {
        parent::_initialize();

        $functionLogic = new \app\common\logic\FunctionLogic;
        $functionLogic->validate_authorfile(2);
        $this->ShopLogic = new ShopLogic;
        
        // 列出营销功能里已使用的模块
        $marketFunc = $this->ShopLogic->marketLogic();
        if (!in_array('sharp', $marketFunc)) {
            // $this->error('内置功能已废弃！');
        }
        $this->assign('marketFunc', $marketFunc);
    }
    
    /**
     * 秒杀商品列表
     */
    public function index()
    {
        $list     = array();
        $keywords = input('keywords/s');

        $condition = array();
        if (!empty($keywords)) {
            $condition['b.title'] = array('LIKE', "%{$keywords}%");
        }

        $fields = "a.*,b.title,b.litpic";

        $Db    = Db::name('sharp_goods');
        $count = $Db->alias("a")->join('archives b', "a.aid=b.aid")->where($condition)->count('a.sharp_goods_id');// 查询满足要求的总记录数
        $Page  = $pager = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list  = $Db->alias("a")->field($fields)
            ->join('archives b', "a.aid=b.aid")
            ->where($condition)
            ->order('a.sort_order asc, a.sharp_goods_id desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->select();

        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('list', $list);// 赋值数据集
        $this->assign('pager', $pager);// 赋值分页对象
        return $this->fetch();
    }

    /**
     * 商品选择
     * @return [type] [description]
     */
    public function ajax_archives_list()
    {
        $assign_data = array();
        $condition   = array();
        // 获取到所有URL参数
        $param  = input('param.');
        $typeid = input('param.typeid/d');

        // 应用搜索条件
        foreach (['keywords', 'typeid'] as $key) {
            if ($key == 'keywords' && !empty($param[$key])) {
                $condition['a.title'] = array('LIKE', "%{$param[$key]}%");
            } else if ($key == 'typeid' && !empty($param[$key])) {
                $typeid  = $param[$key];
                $hasRow  = model('Arctype')->getHasChildren($typeid);
                $typeids = get_arr_column($hasRow, 'id');
                /*权限控制 by 小虎哥*/
                $admin_info = session('admin_info');
                if (0 < intval($admin_info['role_id'])) {
                    $auth_role_info = $admin_info['auth_role_info'];
                    if (!empty($auth_role_info)) {
                        if (isset($auth_role_info['only_oneself']) && 1 == $auth_role_info['only_oneself']) {
                            $condition['a.admin_id'] = $admin_info['admin_id'];
                        }
                        if (!empty($auth_role_info['permission']['arctype'])) {
                            if (!empty($typeid)) {
                                $typeids = array_intersect($typeids, $auth_role_info['permission']['arctype']);
                            }
                        }
                    }
                }
                /*--end*/
                $condition['a.typeid'] = array('IN', $typeids);
            } else if (!empty($param[$key])) {
                $condition['a.' . $key] = array('eq', $param[$key]);
            }
        }
        // 审核通过
        $condition['a.arcrank'] = array('gt', -1);
        /*多语言*/
        $condition['a.lang'] = array('eq', $this->admin_lang);
        /*回收站数据不显示*/
        $condition['a.is_del']  = array('eq', 0);
        $channels               = 2;
        $condition['a.channel'] = $channels;

        /**
         * 数据查询，搜索出主键ID的值
         */
        $count = Db::name('archives')->alias('a')->where($condition)->count('aid');// 查询满足要求的总记录数
        $Page  = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list  = Db::name('archives')
            ->alias('a')
            ->field("a.aid,a.litpic,a.title,b.typename,a.update_time")
            ->where($condition)
            ->join('__ARCTYPE__ b', 'a.typeid = b.id', 'LEFT')
            ->order('a.sort_order asc, a.aid desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->getAllWithIndex('aid');


        $show                 = $Page->show(); // 分页显示输出
        $assign_data['page']  = $show; // 赋值分页输出
        $assign_data['list']  = $list; // 赋值数据集
        $assign_data['pager'] = $Page; // 赋值分页对象

        /*允许发布文档列表的栏目*/
        $allow_release_channel       = !empty($channels) ? explode(',', $channels) : [];
        $assign_data['arctype_html'] = allow_release_arctype($typeid, $allow_release_channel);
        /*--end*/

        // 模型名称
        $channeltype_ntitle = '文档';
        if (!stristr($channels, ',')) {
            $channeltype_row    = \think\Cache::get('extra_global_channeltype');
            $channeltype_ntitle = $channeltype_row[$channels]['ntitle'];
        }
        $assign_data['channeltype_ntitle'] = $channeltype_ntitle;

        $this->assign($assign_data);

        return $this->fetch();
    }

    /**
     * 添加秒杀商品
     */
    public function add()
    {
        if (IS_POST) {
            $post = input('post.');
            if (is_array($post['seckill_price'])) {
                $post['is_sku'] = 1; // 多规格秒杀商品
                if (is_array($post['seckill_stock'])) {
                    $post['seckill_stock_total'] = 0;
                    foreach ($post['seckill_stock'] as $k => $v) {
                        $post['seckill_stock_total'] += $v['spec_seckill_stock'];
                    }
                }
            }

            $newData = [
                'aid'           => $post['aid'],
                'limit'         => $post['limit'],
                'is_sku'        => isset($post['is_sku']) ? $post['is_sku'] : 0,
                'seckill_stock' => is_array($post['seckill_stock']) ? $post['seckill_stock_total'] : $post['seckill_stock'],
                'seckill_price' => is_array($post['seckill_price']) ? 0 : $post['seckill_price'],
                'virtual_sales'         => $post['virtual_sales'],
                'add_time'      => getTime(),
                'update_time'   => getTime(),
            ];

            $insertId = Db::name('sharp_goods')->insertGetId($newData);
            if (false !== $insertId) {
                if (isset($post['is_sku']) && 1 == $post['is_sku']) {
                    model('ProductSpecValue')->ProducSpecValueEditSave($post);
                }
                adminLog('新增秒杀商品：' . $insertId);
                $this->success("操作成功", url('Sharp/index'));
            } else {
                $this->error("操作失败", url('Sharp/index'));
            }
            exit;
        }

        $id   = input('id/d');
        $info = Db::name('archives')->field('aid,litpic,title,users_price')->where('aid', $id)->find();
        // 获取规格数据信息
        // 包含：SpecSelectName、HtmlTable、spec_mark_id_arr、preset_value
        $assign_data = model('ProductSpecData')->GetSharpProductSpecData($id);
        // 商城配置
        $shopConfig                = getUsersConfigData('shop');
        $assign_data['shopConfig'] = $shopConfig;

        $this->assign('info', $info);
        $this->assign($assign_data);

        return $this->fetch();
    }

    /**
     * 编辑秒杀商品
     */
    public function edit()
    {
        if (IS_POST) {
            $post = input('post.');
            if (is_array($post['seckill_price'])) {
                $post['is_sku'] = 1; // 多规格秒杀商品
                if (is_array($post['seckill_stock'])) {
                    $post['seckill_stock_total'] = 0;
                    foreach ($post['seckill_stock'] as $k => $v) {
                        $post['seckill_stock_total'] += $v['spec_seckill_stock'];
                    }
                }
            }

            $data = [
                'limit'         => $post['limit'],
                'is_sku'        => isset($post['is_sku']) ? $post['is_sku'] : 0,
                'seckill_stock' => is_array($post['seckill_stock']) ? $post['seckill_stock_total'] : $post['seckill_stock'],
                'seckill_price' => is_array($post['seckill_price']) ? 0 : $post['seckill_price'],
                'virtual_sales'         => $post['virtual_sales'],
                'update_time'   => getTime(),
            ];

            $r = Db::name('sharp_goods')->where('sharp_goods_id', intval($post['sharp_goods_id']))->update($data);
            if (false !== $r) {
                if (isset($post['is_sku']) && 1 == $post['is_sku']) {
                    model('ProductSpecValue')->ProducSpecValueEditSave($post);
                }
                adminLog('编辑秒杀商品：' . $post['sharp_goods_id']);
                $this->success("操作成功", url('Sharp/index'));
            } else {
                $this->error("操作失败", url('Sharp/index'));
            }
            exit;
        }

        $id   = input('id/d'); //指sharp_goods_id
        $info = Db::name('sharp_goods')
            ->alias('a')
            ->field('a.*,b.litpic,b.title,b.users_price')
            ->join('archives b', 'a.aid = b.aid')
            ->where('a.sharp_goods_id', $id)
            ->find();
        // 获取规格数据信息
        // 包含：SpecSelectName、HtmlTable、spec_mark_id_arr、preset_value
        $assign_data = model('ProductSpecData')->GetSharpProductSpecData($info['aid']);
        // 商城配置
        $shopConfig                = getUsersConfigData('shop');
        $assign_data['shopConfig'] = $shopConfig;

        $this->assign('info', $info);
        $this->assign($assign_data);

        return $this->fetch();
    }

    /**
     * 删除秒杀商品
     */
    public function del()
    {
        if (IS_POST) {
            $id_arr = input('del_id/a');
            $id_arr = eyIntval($id_arr);
            if (!empty($id_arr)) {
                $aids = Db::name('sharp_goods')
                    ->where('sharp_goods_id', 'IN', $id_arr)
                    ->column('aid');

                $r = Db::name('sharp_goods')
                    ->where('sharp_goods_id', 'IN', $id_arr)
                    ->delete();
                if ($r) {
                    Db::name('product_spec_value')
                        ->where('aid', 'IN', $aids)
                        ->update([
                            'seckill_price' => 0,
                            'seckill_stock' => 0,
                            'is_seckill'    => 0,
                            'update_time'   => getTime()
                        ]);
                    adminLog('删除秒杀商品,aid：' . implode(',', $aids));
                    $this->success('删除成功');
                } else {
                    $this->error('删除失败');
                }
            } else {
                $this->error('参数有误');
            }
        }
        $this->error('非法访问');
    }

    /**
     * 活动会场列表
     * @return mixed
     */
    public function active_index()
    {
        $list = array();
//        $keywords = input('keywords/s');
//
        $condition = array();
//        if (!empty($keywords)) {
//            $condition['title'] = array('LIKE', "%{$keywords}%");
//        }

        $Db    = Db::name('sharp_active');
        $count = $Db->where($condition)->count('active_id');// 查询满足要求的总记录数
        $Page  = $pager = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list  = $Db
            ->where($condition)
            ->order('active_date desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->getAllWithIndex('active_id');

        $ids = [];
        foreach ($list as $k => $v) {
            $ids[] = $v['active_id'];
        }
        if (!empty($ids)) {
            $count = Db::name('sharp_active_time')->where('active_id', 'in', $ids)->field('count(*) as count,active_id')->group('active_id')->select();
            if (!empty($count)) {
                foreach ($count as $k => $v) {
                    $list[$v['active_id']]['count'] = $v['count'];
                }
            }
        }
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('list', $list);// 赋值数据集
        $this->assign('pager', $pager);// 赋值分页对象
        return $this->fetch();
    }

    /**
     * 添加活动场次
     */
    public function active_add()
    {
        if (IS_POST) {
            $post = input('post.');
            if ($post['active_date'] < date('Y-m-d')) {
                $this->error('活动日期不能小于当前日期!');
            }
            //先查询该活动日期场次是否已经存在
            $have = Db::name('sharp_active')->where('active_date',strtotime($post['active_date']))->find();
            if (!empty($have)){
                $insertId = $have['active_id'];
            }else{
                $activeData = [
                    'active_date' => strtotime($post['active_date']),
                    'add_time'    => getTime(),
                    'update_time' => getTime(),
                ];
                $insertId   = Db::name('sharp_active')->insertGetId($activeData);
            }
            if (false !== $insertId) {
                //查出已经建立的秒杀活动时间
                $have_time = Db::name('sharp_active_time')
                    ->where('active_time','in',$post['active_time'])
                    ->where('active_id',$insertId)
                    ->column('active_time');
                $activeTime = [];
                foreach ($post['active_time'] as $v) {
                    if (in_array($v,$have_time)){
                        //已经存在,则跳过
                        continue;
                    }
                    $activeTime[] = [
                        'active_time' => $v,
                        'active_id'   => $insertId,
                        'add_time'    => getTime(),
                        'update_time' => getTime(),
                    ];
                }
                $sharpActiveTimeModel = new \app\admin\model\SharpActiveTime();
                $timeData             = $sharpActiveTimeModel->saveAll($activeTime);
                if ($timeData && $post['goods']) {
                    $goodsData = [];
                    foreach ($timeData as $k1 => $v1) {
                        foreach ($post['goods']['sharp_goods_id'] as $k => $v) {
                            $goodsData[] = [
                                'active_time_id' => $v1->getData('active_time_id'),
                                'active_id'      => $insertId,
                                'sharp_goods_id' => $v,
                                'aid'            => $post['goods']['aid'][$k],
                                'add_time'       => getTime(),
                                'update_time'    => getTime(),
                            ];
                        }
                    }
                    Db::name('sharp_active_goods')->insertAll($goodsData);
                }
                adminLog('新增活动场次：' . $insertId);
                $this->success("操作成功", url('Sharp/active_index'));
            } else {
                $this->error("操作失败", url('Sharp/index'));
            }
            exit;
        }
        return $this->fetch();
    }

    /**
     * 删除活动会场
     */
    public function active_del()
    {
        if (IS_POST) {
            $id_arr = input('del_id/a');
            $id_arr = eyIntval($id_arr);
            if (!empty($id_arr)) {
                $r = Db::name('sharp_active')
                    ->where('active_id', 'IN', $id_arr)
                    ->delete();
                if ($r) {
                    Db::name('sharp_active_time')
                        ->where('active_id', 'IN', $id_arr)
                        ->delete();
                    Db::name('sharp_active_goods')
                        ->where('active_id', 'IN', $id_arr)
                        ->delete();
                    adminLog('删除活动会场,active_id：' . implode(',', $id_arr));
                    $this->success('删除成功');
                } else {
                    $this->error('删除失败');
                }
            } else {
                $this->error('参数有误');
            }
        }
        $this->error('非法访问');
    }

    /**
     * 秒杀商品选择
     * @return [type] [description]
     */
    public function goods_list()
    {
        $assign_data = array();
        $condition   = array();
        // 获取到所有URL参数
        $param  = input('param.');
        $typeid = input('param.typeid/d');

        // 应用搜索条件
        foreach (['keywords', 'typeid'] as $key) {
            if ($key == 'keywords' && !empty($param[$key])) {
                $condition['a.title'] = array('LIKE', "%{$param[$key]}%");
            } else if ($key == 'typeid' && !empty($param[$key])) {
                $typeid  = $param[$key];
                $hasRow  = model('Arctype')->getHasChildren($typeid);
                $typeids = get_arr_column($hasRow, 'id');
                /*权限控制 by 小虎哥*/
                $admin_info = session('admin_info');
                if (0 < intval($admin_info['role_id'])) {
                    $auth_role_info = $admin_info['auth_role_info'];
                    if (!empty($auth_role_info)) {
                        if (isset($auth_role_info['only_oneself']) && 1 == $auth_role_info['only_oneself']) {
                            $condition['a.admin_id'] = $admin_info['admin_id'];
                        }
                        if (!empty($auth_role_info['permission']['arctype'])) {
                            if (!empty($typeid)) {
                                $typeids = array_intersect($typeids, $auth_role_info['permission']['arctype']);
                            }
                        }
                    }
                }
                /*--end*/
                $condition['a.typeid'] = array('IN', $typeids);
            }
        }
        /*回收站数据不显示*/
        $where['b.is_del'] = 0;
        $where['b.status'] = 1;

        /**
         * 数据查询，搜索出主键ID的值
         */
        $count = Db::name('sharp_goods')->alias('b')->where($where)->count('aid');// 查询满足要求的总记录数
        $Page  = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list  = Db::name('sharp_goods')
            ->alias('b')
            ->field("a.litpic,a.title,b.sharp_goods_id,b.aid,b.update_time,b.seckill_stock,b.limit")
            ->where($where)
            ->where($condition)
            ->join('archives a', 'a.aid = b.aid', 'LEFT')
            ->join('arctype c', 'a.typeid = c.id', 'LEFT')
            ->order('b.sort_order asc, b.sharp_goods_id desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->getAllWithIndex('aid');
        if ($list) {
            foreach ($list as $key => $val) {
                $val['litpic']             = get_default_pic($val['litpic']);
                $json_encode_params        = [
                    'sharp_goods_id' => $val['sharp_goods_id'],
                    'aid'            => $val['aid'],
                    'title'          => $val['title'],
                    'litpic'         => $val['litpic'],
                ];
                $val['json_encode_params'] = json_encode($json_encode_params, JSON_UNESCAPED_SLASHES);
                $list[$key]                = $val;
            }
        }

        $show                 = $Page->show(); // 分页显示输出
        $assign_data['page']  = $show; // 赋值分页输出
        $assign_data['list']  = $list; // 赋值数据集
        $assign_data['pager'] = $Page; // 赋值分页对象

        $channels = 2;
        /*允许发布文档列表的栏目*/
        $allow_release_channel       = !empty($channels) ? explode(',', $channels) : [];
        $assign_data['arctype_html'] = allow_release_arctype($typeid, $allow_release_channel);
        /*--end*/

        $this->assign($assign_data);

        return $this->fetch();
    }

    /**
     * 活动会场-场次列表
     * @return mixed
     */
    public function active_time_index()
    {
        $list = [];
        $where = [];
        $active_id = input('id/d');
        $where['a.active_id'] = $active_id;
        $Db        = Db::name('sharp_active_time')->alias('a');
        $count     = $Db->where($where)->count('a.active_time_id');// 查询满足要求的总记录数

        $Page      = $pager = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list      = Db::name('sharp_active_time')->alias('a')
            ->where($where)
            ->field('a.*,b.active_date')
            ->join('sharp_active b','a.active_id = b.active_id')
            ->order('a.active_time asc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->getAllWithIndex('active_time_id');

        $ids = [];
        foreach ($list as $k => $v) {
            if ($v['active_time']<10){
                $list[$k]['active_time'] = '0'.$v['active_time'].':00';
            }else{
                $list[$k]['active_time'] = $v['active_time'].':00';
            }
            $ids[] = $v['active_time_id'];
        }
        if (!empty($ids)) {
            $count = Db::name('sharp_active_goods')
                ->where('active_id', $active_id)
                ->where('active_time_id', 'in', $ids)
                ->field('count(*) as count,active_time_id')
                ->group('active_time_id')->select();
            if (!empty($count)) {
                foreach ($count as $k => $v) {
                    $list[$v['active_time_id']]['count'] = $v['count'];
                }
            }
        }
        $show = $Page->show();// 分页显示输出
        $this->assign('page', $show);// 赋值分页输出
        $this->assign('list', $list);// 赋值数据集
        $this->assign('pager', $pager);// 赋值分页对象
        return $this->fetch();
    }

    /**
     * 删除活动场次
     */
    public function active_time_del()
    {
        if (IS_POST) {
            $id_arr = input('del_id/a');
            $id_arr = eyIntval($id_arr);
            if (!empty($id_arr)) {
                $r = Db::name('sharp_active_time')
                    ->where('active_time_id', 'IN', $id_arr)
                    ->delete();
                if ($r) {
                    Db::name('sharp_active_goods')
                        ->where('active_time_id', 'IN', $id_arr)
                        ->delete();
                    adminLog('删除活动场次,active_time_id：' . implode(',', $id_arr));
                    $this->success('删除成功');
                } else {
                    $this->error('删除失败');
                }
            } else {
                $this->error('参数有误');
            }
        }
        $this->error('非法访问');
    }

    /**
     * 编辑活动场次
     */
    public function active_time_edit()
    {
        if(IS_POST){
            $post = input('post.');
            if (!empty($post['goods'])){
                Db::name('sharp_active_goods')
                    ->where(['active_id'=>intval($post['active_id']),'active_time_id'=>intval($post['active_time_id'])])
                    ->delete();
                $goodsData = [];
                foreach ($post['goods']['sharp_goods_id'] as $k => $v) {
                    $goodsData[] = [
                        'active_time_id' => $post['active_time_id'],
                        'active_id'      => $post['active_id'],
                        'sharp_goods_id' => $v,
                        'aid'            => $post['goods']['aid'][$k],
                        'add_time'       => getTime(),
                        'update_time'    => getTime(),
                    ];
                }
                $r = Db::name('sharp_active_goods')->insertAll($goodsData);
                if ($r){
                    adminLog('编辑活动场次active_time_id：' . $post['active_time_id']);
                    $this->success("操作成功", url('Sharp/active_time_index',['id'=>$post['active_id']]));
                } else {
                    $this->error("操作失败", url('Sharp/active_time_edit',['id'=>$post['active_time_id']]));
                }
            }
        }
        $id = input('id/d');
        $info = Db::name('sharp_active_time')->alias('a')
            ->where('active_time_id',$id)
            ->field('a.*,b.active_date')
            ->join('sharp_active b','a.active_id = b.active_id')
            ->order('a.active_time asc')
            ->find();
        if ($info['active_time']<10){
            $info['active_time'] = '0'.$info['active_time'].':00';
        }else{
            $info['active_time'] = $info['active_time'].':00';
        }
        $goods = Db::name('sharp_active_goods')
            ->alias('a')
            ->field('a.*,b.litpic,b.title')
            ->join('archives b','a.aid=b.aid')
            ->where('a.active_time_id',$id)
            ->select();
        $this->assign('info',$info);
        $this->assign('goods',$goods);
        return $this->fetch();

    }

    /**
     * 活动会场-添加场次
     * @return mixed
     */
    public function active_time_add()
    {
        if(IS_POST){
            $post = input('post.');
            if(!empty($post['active_time'])){
                foreach ($post['active_time'] as $v) {
                    $activeTime[] = [
                        'active_time' => $v,
                        'active_id'   => $post['active_id'],
                        'add_time'    => getTime(),
                        'update_time' => getTime(),
                    ];
                }
                $sharpActiveTimeModel = new \app\admin\model\SharpActiveTime();
                $timeData             = $sharpActiveTimeModel->saveAll($activeTime);
                if ($timeData && $post['goods']) {
                    $goodsData = [];
                    $active_time_ids = [];
                    foreach ($timeData as $k1 => $v1) {
                        foreach ($post['goods']['sharp_goods_id'] as $k => $v) {
                            $active_time_id = $v1->getData('active_time_id');
                            $active_time_ids[] = $active_time_id;
                            $goodsData[] = [
                                'active_time_id' => $active_time_id,
                                'active_id'      => $post['active_id'],
                                'sharp_goods_id' => $v,
                                'aid'            => $post['goods']['aid'][$k],
                                'add_time'       => getTime(),
                                'update_time'    => getTime(),
                            ];
                        }
                    }
                    Db::name('sharp_active_goods')->insertAll($goodsData);
                }
                if ($timeData){
                    adminLog('新增活动场次active_time_id：' . implode(',', $active_time_ids));
                    $this->success("操作成功", url('Sharp/active_time_index',['id'=>$post['active_id']]));
                }else {
                    $this->error("操作失败", url('Sharp/active_time_add',['id'=>$post['active_id']]));
                }

            }
            $this->error("请选择活动场次!", url('Sharp/active_time_add',['id'=>$post['active_id']]));

        }
        $active_id = input('id/d');
        $info = Db::name('sharp_active')->where('active_id',$active_id)->find();
        if (empty($info)){
            $this->error("该活动会场不存在!", url('Sharp/active_index'));
        }
        $info['active_time'] = Db::name('sharp_active_time')->where('active_id',$active_id)->column('active_time');
        $this->assign('info',$info);
        return $this->fetch();
    }
}