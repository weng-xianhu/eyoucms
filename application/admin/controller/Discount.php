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

class Discount extends Base
{
    /**
     * 构造方法
     */
    public function _initialize()
    {
        parent::_initialize();

        $functionLogic = new \app\common\logic\FunctionLogic;
        $functionLogic->validate_authorfile(2);
    }
    
    /**
     * 限时折扣商品列表
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

        $Db    = Db::name('discount_goods');
        $count = $Db->alias("a")->join('archives b', "a.aid=b.aid")->where($condition)->count('a.discount_gid');// 查询满足要求的总记录数
        $Page  = $pager = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list  = $Db->alias("a")->field($fields)
            ->join('archives b', "a.aid=b.aid")
            ->where($condition)
            ->order('a.sort_order asc, a.discount_gid desc')
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
     * 添加限时折扣商品
     */
    public function add()
    {
        if (IS_POST) {
            $post = input('post.');
            if (is_array($post['discount_price'])) {
                $post['is_sku'] = 1; // 多规格限时折扣商品
                if (is_array($post['discount_stock'])) {
                    $post['discount_stock_total'] = 0;
                    foreach ($post['discount_stock'] as $k => $v) {
                        $post['discount_stock_total'] += $v['spec_discount_stock'];
                    }
                }
            }

            $newData = [
                'aid'           => $post['aid'],
                'is_sku'        => isset($post['is_sku']) ? $post['is_sku'] : 0,
                'discount_stock' => is_array($post['discount_stock']) ? $post['discount_stock_total'] : $post['discount_stock'],
                'discount_price' => is_array($post['discount_price']) ? 0 : $post['discount_price'],
                'virtual_sales' => $post['virtual_sales'],
                'add_time'      => getTime(),
                'update_time'   => getTime(),
            ];

            $insertId = Db::name('discount_goods')->insertGetId($newData);
            if (false !== $insertId) {
                if (isset($post['is_sku']) && 1 == $post['is_sku']) {
                    model('ProductSpecValue')->ProducSpecValueEditSave($post);
                }
                adminLog('新增限时折扣商品：' . $insertId);
                $this->success("操作成功", url('Discount/index'));
            } else {
                $this->error("操作失败", url('Discount/index'));
            }
            exit;
        }

        $id   = input('id/d');
        $info = Db::name('archives')->field('aid,litpic,title,users_price')->where('aid', $id)->find();
        // 获取规格数据信息
        // 包含：SpecSelectName、HtmlTable、spec_mark_id_arr、preset_value
        $assign_data = model('ProductSpecData')->GetDiscountProductSpecData($id);
        // 商城配置
        $shopConfig                = getUsersConfigData('shop');
        $assign_data['shopConfig'] = $shopConfig;

        $this->assign('info', $info);
        $this->assign($assign_data);

        return $this->fetch();
    }

    /**
     * 编辑限时折扣商品
     */
    public function edit()
    {
        if (IS_POST) {
            $post = input('post.');
            if (is_array($post['discount_price'])) {
                $post['is_sku'] = 1; // 多规格限时折扣商品
                if (is_array($post['discount_stock'])) {
                    $post['discount_stock_total'] = 0;
                    foreach ($post['discount_stock'] as $k => $v) {
                        $post['discount_stock_total'] += $v['spec_discount_stock'];
                    }
                }
            }

            $data = [
                'is_sku'        => isset($post['is_sku']) ? $post['is_sku'] : 0,
                'discount_stock' => is_array($post['discount_stock']) ? $post['discount_stock_total'] : $post['discount_stock'],
                'discount_price' => is_array($post['discount_price']) ? 0 : $post['discount_price'],
                'virtual_sales'         => $post['virtual_sales'],
                'update_time'   => getTime(),
            ];

            $r = Db::name('discount_goods')->where('discount_gid', intval($post['discount_gid']))->update($data);
            if (false !== $r) {
                if (isset($post['is_sku']) && 1 == $post['is_sku']) {
                    model('ProductSpecValue')->ProducSpecValueEditSave($post);
                }
                adminLog('编辑限时折扣商品：' . $post['discount_gid']);
                $this->success("操作成功", url('Discount/index'));
            } else {
                $this->error("操作失败", url('Discount/index'));
            }
            exit;
        }

        $id   = input('id/d'); //指discount_gid
        $info = Db::name('discount_goods')
            ->alias('a')
            ->field('a.*,b.litpic,b.title,b.users_price')
            ->join('archives b', 'a.aid = b.aid')
            ->where('a.discount_gid', $id)
            ->find();
        // 获取规格数据信息
        // 包含：SpecSelectName、HtmlTable、spec_mark_id_arr、preset_value
        $assign_data = model('ProductSpecData')->GetDiscountProductSpecData($info['aid']);
        // 商城配置
        $shopConfig                = getUsersConfigData('shop');
        $assign_data['shopConfig'] = $shopConfig;

        $this->assign('info', $info);
        $this->assign($assign_data);

        return $this->fetch();
    }

    /**
     * 删除限时折扣商品
     */
    public function del()
    {
        if (IS_POST) {
            $id_arr = input('del_id/a');
            $id_arr = eyIntval($id_arr);
            if (!empty($id_arr)) {
                $aids = Db::name('discount_goods')
                    ->where('discount_gid', 'IN', $id_arr)
                    ->column('aid');

                $r = Db::name('discount_goods')
                    ->where('discount_gid', 'IN', $id_arr)
                    ->delete();
                if ($r) {
                    Db::name('product_spec_value')
                        ->where('aid', 'IN', $aids)
                        ->update([
                            'discount_price' => 0,
                            'discount_stock' => 0,
                            'is_discount'    => 0,
                            'update_time'   => getTime()
                        ]);
                    adminLog('删除限时折扣商品,aid：' . implode(',', $aids));
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

        $Db    = Db::name('discount_active');
        $count = $Db->where($condition)->count('active_id');// 查询满足要求的总记录数
        $Page  = $pager = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list  = $Db
            ->where($condition)
            ->order('end_date desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->getAllWithIndex('active_id');

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
            if (!empty($post['start_date'])) $post['start_date'] = strtotime($post['start_date']);
            if (!empty($post['end_date'])) $post['end_date'] = strtotime($post['end_date']);
            if (!empty($post['preheat_time'])) {
                $post['preheat_time'] = strtotime($post['preheat_time']);
                if ($post['preheat_time'] > $post['start_date']){
                    $this->error('预热时间不能大于开始时间');
                }
            }

            $post['add_time'] = getTime();
            $post['update_time'] = getTime();
            $insertId = Db::name('discount_active')->insertGetId($post);

            if (false !== $insertId) {
                $goodsData = [];
                $discount_goods_id_arr = [];
                foreach ($post['goods']['discount_gid'] as $k => $v) {
                    if (!in_array($v,$discount_goods_id_arr)){
                        $discount_goods_id_arr[] = $v;
                        $goodsData[] = [
                            'active_id'      => $insertId,
                            'discount_goods_id' => $v,
                            'aid'            => $post['goods']['aid'][$k],
                            'add_time'       => getTime(),
                            'update_time'    => getTime(),
                        ];
                    }

                }

                Db::name('discount_active_goods')->insertAll($goodsData);

                adminLog('新增限时折扣活动：' . $insertId);
                $this->success("操作成功", url('Discount/active_index'));
            } else {
                $this->error("操作失败", url('Discount/index'));
            }
            exit;
        }
        return $this->fetch();
    }

    /**
     * 编辑活动
     */
    public function active_edit()
    {
        if(IS_POST){
            $post = input('post.');
            if (!empty($post['goods'])){
                Db::name('discount_active_goods')
                    ->where(['active_id'=>intval($post['active_id'])])
                    ->delete();
                $goodsData = [];
                $discount_goods_id_arr = [];
                foreach ($post['goods']['discount_gid'] as $k => $v) {
                    if (!in_array($v,$discount_goods_id_arr)){
                        $discount_goods_id_arr[] = $v;
                        $goodsData[] = [
                            'active_id'      => $post['active_id'],
                            'discount_goods_id' => $v,
                            'aid'            => $post['goods']['aid'][$k],
                            'add_time'       => getTime(),
                            'update_time'    => getTime(),
                        ];
                    }
                }
                $r = Db::name('discount_active_goods')->insertAll($goodsData);
                if ($r){
                    adminLog('编辑活动active_id：' . $post['active_id']);
                    $this->success("操作成功", url('Discount/active_index',['id'=>$post['active_id']]));
                } else {
                    $this->error("操作失败", url('Discount/active_edit',['id'=>$post['active_id']]));
                }
            }
        }
        $id = input('id/d');
        $info = Db::name('discount_active')->where('active_id',$id)->find();

        $goods = Db::name('discount_active_goods')
            ->alias('a')
            ->field('a.*,b.litpic,b.title')
            ->join('archives b','a.aid=b.aid')
            ->where('a.active_id',$id)
            ->select();

        $this->assign('info',$info);
        $this->assign('goods',$goods);
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
                $r = Db::name('discount_active')
                    ->where('active_id', 'IN', $id_arr)
                    ->delete();
                if ($r) {
                    Db::name('discount_active_goods')
                        ->where('active_id', 'IN', $id_arr)
                        ->delete();
                    adminLog('删除活动,active_id：' . implode(',', $id_arr));
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
     * 限时折扣商品选择
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

        $count = Db::name('discount_goods')->alias('b')->where($where)->count('aid');// 查询满足要求的总记录数
        $Page  = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list  = Db::name('discount_goods')
            ->alias('b')
            ->field("a.litpic,a.title,b.discount_gid,b.aid,b.update_time,b.discount_stock")
            ->where($where)
            ->where($condition)
            ->join('archives a', 'a.aid = b.aid', 'LEFT')
            ->join('arctype c', 'a.typeid = c.id', 'LEFT')
            ->order('b.sort_order asc, b.discount_gid desc')
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->getAllWithIndex('aid');
        if ($list) {
            foreach ($list as $key => $val) {
                $val['litpic']             = get_default_pic($val['litpic']);
                $json_encode_params        = [
                    'discount_gid' => $val['discount_gid'],
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

}