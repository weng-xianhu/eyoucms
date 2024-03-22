<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海口快推科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 陈风任 <491085389@qq.com>
 * Date: 2019-3-20
 */

namespace app\user\controller;

use think\Db;
use think\Config;
use think\Page;
use think\Cookie;

class ShopComment extends Base
{
    // 初始化
    public function _initialize() {
        parent::_initialize();
        $functionLogic = new \app\common\logic\FunctionLogic;
        $functionLogic->validate_authorfile(2);
    }

    // 我的评价
    public function index()
    {
        $order_code = input('param.order_code');
        $functionLogic = new \app\user\logic\FunctionLogic;
        $ServiceInfo = $functionLogic->GetAllCommentInfo($this->users_id, $order_code);
        $eyou = [
            'field' => [
                'comment' => $ServiceInfo['Comment'],
                'pageStr' => $ServiceInfo['pageStr'],
            ],
        ];
        $this->assign('eyou', $eyou);
        return $this->fetch('users/shop_comment_list');
    }

    // 晒单评价
    public function need_comment_list()
    {
        $res = [];
        // 查询已完成待评价订单
        $where = [
            'is_comment' => 0,
            'order_status' => 3,
            'users_id' => $this->users_id,
        ];
        $keywords = input('param.keywords/s', '');
        if (!empty($keywords)) $where['order_code'] = ['LIKE', "%{$keywords}%"];
        $count = Db::name('shop_order')->where($where)->order('order_id desc')->count();
        $Page = new Page($count, config('paginate.list_rows'));
        $show = $Page->show();
        $this->assign('page', $show);
        $field = 'order_id, order_code, order_amount, add_time';
        $res = Db::name('shop_order')->field($field)->where($where)->order('order_id desc')->limit($Page->firstRow.','.$Page->listRows)->getAllWithIndex('order_id');
        if (!empty($res)) {
            $orderIds = get_arr_column($res, 'order_id');
            $goodsWhere['a.is_comment'] = 0;
            $goodsWhere['a.order_id'] = ['in',$orderIds];
            $goodsArr = Db::name('shop_order_details')
                ->alias('a')
                ->join('archives b','a.product_id = b.aid')
                ->where($goodsWhere)
                ->field('a.product_id,a.litpic,a.order_id,b.*')
                ->select();
            $New = get_archives_data($goodsArr, 'product_id');
            foreach ($goodsArr as $k=> $v){
                if (!empty($New) && !empty($New[$v['product_id']])) {
                    $v['arcurl'] = urldecode(arcurl('home/Product/view', $New[$v['product_id']]));
                } else {
                    $v['arcurl'] = urldecode(arcurl('home/View/index', ['aid'=>$v['product_id']]));
                }
                $v['litpic'] = get_default_pic($v['litpic']);
                $res[$v['order_id']]['goods'][] = $v;
            }
        }
        foreach ($res as $k => $v){
            $res[$k]['OrderDetailsUrl'] = urldecode(url('user/Shop/shop_order_details',['order_id'=>$v['order_id']]));
            $res[$k]['CommentProduct'] = urldecode(url('user/ShopComment/comment_list', ['order_id' => $v['order_id']]));
            $res[$k]['order_amount'] = floatval($v['order_amount']);
        }

        // 加载数据
        $eyou = [
            'field' => $res,
        ];
        $this->assign('eyou',$eyou);
        return $this->fetch('users/shop_need_comment_list');
    }

    // 评价中转页
    public function comment_list()
    {
        // 查询订单信息
        $order_id = input('param.order_id/d', 0);
        if (empty($order_id)) $this->error('请选择需要评价的订单');
        $where = [
            'users_id' => $this->users_id,
            'order_id' => $order_id,
        ];
        $orderData = Db::name('shop_order')->where($where)->find();
        $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : url("user/Shop/shop_centre", ['select_status'=>3]);
        if (empty($orderData)) $this->error('订单不存在', $url);
        if (!empty($orderData['is_comment']) && 1 === intval($orderData['is_comment'])) $this->error('订单已完成评价', $url);
        if (!empty($orderData['order_status']) && 3 !== intval($orderData['order_status'])) $this->error('订单暂不可评价', $url);

        // 查询订单商品信息
        $data = Db::name('shop_order_details')->where($where)->select();
        $array_new = get_archives_data($data, 'product_id');
        foreach ($data as $k => $v) {
            // 商品封面图处理
            $v['litpic'] = handle_subdir_pic(get_default_pic($v['litpic']));
            // 获取订单商品规格列表(购买时的商品规格)
            $v['product_spec_list'] = model('ShopPublicHandle')->getOrderGoodsSpecList($v);
            // 商品详情页URL
            $v['arcurl'] = urldecode(arcurl('home/View/index', ['aid'=>$v['product_id']]));
            if (!empty($array_new) && !empty($array_new[$v['product_id']])) {
                $v['arcurl'] = urldecode(arcurl('home/Product/view', $array_new[$v['product_id']]));
            }
            $data[$k] = $v;
        }

        // 返回订单数据
        $returnData = [
            'order' => $orderData,
            'goods' => $data,
        ];
        $eyou = [
            'field' => $returnData,
            'SubmitUrl' => url('user/ShopComment/add_comment', ['_ajax'=>1])
        ];
        $this->assign('eyou', $eyou);
        return $this->fetch('users/shop_comment_goods_list');
    }

    // 批量添加商品评论
    public function add_comment()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 是否选择评价分
            $post['order_id'] = intval($post['order_id']);
            if (empty($post['total_score'])) $this->error('请选择全部商品评价分');
            $count = $content = 0;
            foreach ($post['total_score'] as $key => $value) {
                if (!empty($value)) $count++;
                if (!empty($post['content'][$key])) $content++;
            }
            if (0 === intval($count) || intval($count) !== intval(count($post['details_id']))) $this->error('请选择全部商品评价分');
            if (0 === intval($content) || intval($content) !== intval(count($post['details_id']))) $this->error('请填写全部商品评价内容');

            // 再次查询确认商品是否已评价过
            $where = [
                'users_id' => $this->users_id,
                'order_id' => $post['order_id'],
                'is_comment' => 1
            ];
            $resultID = Db::name('shop_order')->where($where)->count();
            if (!empty($resultID)) $this->error('订单已评价过');

            // 批量添加商品评价(一次评价一个订单内的所有商品)
            $insertAll = $details_idArr = $product_idArr = [];
            foreach ($post['total_score'] as $k => $v) {
                if (!empty($v) && !empty($post['details_id'][$k]) && !empty($post['product_id'][$k])) {
                    // 如果是旧版评分则执行，旧版评分转换新版评分
                    if (empty($post['newCommnet'])) {
                        if (1 === intval($v)) {
                            $v = 5;
                        } else if (2 === intval($v)) {
                            $v = 3;
                        } else if (3 === intval($v)) {
                            $v = 1;
                        }
                    }
                    $details_idArr[] = intval($post['details_id'][$k]);
                    $product_idArr[] = intval($post['product_id'][$k]);
                    $insertAll[] = [
                        'product_id'  => intval($post['product_id'][$k]),
                        'users_id'    => $this->users_id,
                        'order_id'    => $post['order_id'],
                        'order_code'  => $post['order_code'],
                        'details_id'  => intval($post['details_id'][$k]),
                        'total_score' => $v,
                        'content'     => !empty($post['content'][$k]) ? serialize(htmlspecialchars($post['content'][$k])) : '',
                        'upload_img'  => !empty($post['upload_img'][$k][0]) ? serialize(implode(',', $post['upload_img'][$k])) : '',
                        'ip_address'  => clientIP(),
                        'is_new_comment' => 1,
                        'add_time'    => getTime(),
                        'update_time' => getTime()
                    ];
                }
            }
            $resultID = Db::name('shop_order_comment')->insertAll($insertAll);
            if (!empty($resultID)) {
                // 商品主表增加评价数
                if (!empty($product_idArr)) {
                    $where = [
                        'aid' => ['IN', $product_idArr],
                    ];
                    Db::name('archives')->where($where)->setInc('appraise', 1);
                }

                // 同步更新订单/商品为已评价
                if (!empty($details_idArr)) {
                    $where = [
                        'users_id' => $this->users_id,
                        'order_id' => $post['order_id'],
                        'details_id' => ['IN', $details_idArr],
                    ];
                    $update = [
                        'is_comment'  => 1,
                        'update_time' => getTime()
                    ];
                    Db::name('shop_order_details')->where($where)->update($update);
                }

                // 查询订单商品是否全部完成评价
                $where = [
                    'is_comment' => 0,
                    'users_id' => $this->users_id,
                    'order_id' => $post['order_id'],
                ];
                $isCount = Db::name('shop_order_details')->where($where)->count();
                if (0 == $isCount) {
                    // 更新订单为已完成评价
                    $where = [
                        'order_status' => 3,
                        'users_id' => $this->users_id,
                        'order_id' => $post['order_id'],
                    ];
                    $update = [
                        'is_comment' => 1,
                        'update_time' => getTime()
                    ];
                    Db::name('shop_order')->where($where)->update($update);
                }

                // 返回提示结束
                $this->success('评价成功', in_array($this->usersTplVersion, ['v3']) ? url('user/ShopComment/need_comment_list') : url('user/ShopComment/index'));
            } else {
                $this->error('评价失败，请重试');
            }
        }
    }

    // 第一、第二版会员中心 -- 添加评论
    public function product()
    {
        // 商品评价功能已关闭
        if (empty($this->usersConfig['shop_open_comment'])) $this->error('商品评价功能已关闭');
        if (IS_AJAX_POST) {
            $post = input('post.');
            if (empty($post['total_score'])) $this->error('请选择评价评分');
            // 如果是旧版评分则执行
            if (empty($post['newCommnet'])) {
                // 旧版评分转换新版评分
                if (1 === intval($post['total_score'])) {
                    $post['total_score'] = 5;
                } else if (2 === intval($post['total_score'])) {
                    $post['total_score'] = 3;
                } else if (3 === intval($post['total_score'])) {
                    $post['total_score'] = 1;
                }
            }
            $post['order_id'] = intval($post['order_id']);
            $post['details_id'] = intval($post['details_id']);
            $post['product_id'] = intval($post['product_id']);

            // 再次查询确认商品是否已评价过
            $where = [
                'users_id' => $this->users_id,
                'order_id' => $post['order_id'],
                'details_id' => $post['details_id'],
                'product_id' => $post['product_id'],
                'is_comment' => 1
            ];
            $resultID = Db::name('shop_order_details')->where($where)->count();
            if (!empty($resultID)) $this->error('商品已评价过');

            // 是否开启评价自动审核
            $shopOpenCommentAudit = getUsersConfigData('shop.shop_open_comment_audit');
            // 添加评价数据
            $insert = [
                'users_id'    => $this->users_id,
                'order_id'    => !empty($post['order_id']) ? $post['order_id'] : 0,
                'order_code'  => !empty($post['order_code']) ? $post['order_code'] : 0,
                'details_id'  => !empty($post['details_id']) ? $post['details_id'] : 0,
                'product_id'  => !empty($post['product_id']) ? $post['product_id'] : 0,
                'total_score' => !empty($post['total_score']) ? $post['total_score'] : 5,
                'content'     => !empty($post['content']) ? serialize(htmlspecialchars($post['content'])) : '',
                'upload_img'  => !empty($post['upload_img'][0]) ? serialize(implode(',', $post['upload_img'])) : '',
                'ip_address'  => clientIP(),
                'is_new_comment' => 1,
                'is_show'     => !empty($shopOpenCommentAudit) ? 0 : 1,
                'add_time'    => getTime(),
                'update_time' => getTime()
            ];
            $resultID = Db::name('shop_order_comment')->insertGetId($insert);
            if (!empty($resultID)) {
                // 商品主表增加评价数
                if (!empty($post['product_id'])) {
                    $where = [
                        'aid' => $post['product_id'],
                    ];
                    Db::name('archives')->where($where)->setInc('appraise', 1);
                }
                
                // 同步更新订单商品为已评价
                $update = [
                    'details_id'  => $insert['details_id'],
                    'is_comment'  => 1,
                    'update_time' => getTime()
                ];
                Db::name('shop_order_details')->update($update);

                // 如果订单商品已经全部评价，那么订单主表is_comment == 1
                $where = [
                    'order_id' => $post['order_id'],
                    'users_id' => $this->users_id,
                    'is_comment' => 0
                ];
                $resultID = Db::name('shop_order_details')->where($where)->count();
                if (empty($resultID)){
                    $where = [
                        'order_id' => $post['order_id'],
                        'users_id' => $this->users_id,
                        'order_status'=> 3,
                        'is_comment'  => 0,
                    ];
                    $update = [
                        'is_comment' => 1,
                        'update_time' => getTime()
                    ];
                    Db::name('shop_order')->where($where)->update($update);
                }

                // 清理缓存并返回结束
                cache('EyouHomeAjaxComment_' . $post['product_id'], null, null, 'shop_order_comment');
                $this->success('评价成功！', url('user/Shop/shop_centre'));
            } else {
                $this->error('评价失败，请重试！');
            }
        }

        // 查询订单信息
        $details_id = input('param.details_id');
        if (empty($details_id)) $this->error('请选择需要评价的商品！');
        // 排除字段
        $field1 = 'add_time, update_time, apply_service';
        // 查询字段
        $field2 = 'b.order_code, b.add_time';
        // 查询条件
        $where = [
            'a.users_id'   => $this->users_id,
            'a.details_id' => $details_id,
        ];
        // 查询数据
        $Details = Db::name('shop_order_details')
            ->alias('a')
            ->field($field1, true, PREFIX . 'shop_order_details', 'a')
            ->field($field2)
            ->where($where)
            ->join('__SHOP_ORDER__ b', 'a.order_id = b.order_id', 'LEFT')
            ->find();

        // 已评价商品跳转路径
        $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : url("user/Shop/shop_centre", ['select_status'=>3]);
        if (empty($Details)) $this->error('商品已评价过', $url);
        if (1 == !empty($Details['is_comment'])) $this->error('商品已评价过', $url);

        // 商品规格
        $Details['spec_value'] = htmlspecialchars_decode(unserialize($Details['data'])['spec_value']);
        $product_spec_list = [];
        if (!empty($Details['spec_value'])) {
            $spec_value_arr = explode('<br/>', $Details['spec_value']);
            foreach ($spec_value_arr as $sp_key => $sp_val) {
                $sp_arr = explode('：', $sp_val);
                if (trim($sp_arr[0]) && !empty($sp_arr[0])) {
                    $product_spec_list[] = [
                        'name'  => !empty($sp_arr[0]) ? trim($sp_arr[0]) : '',
                        'value' => !empty($sp_arr[1]) ? trim($sp_arr[1]) : '',
                    ];
                }
            }
        }
        $Details['product_spec_list'] = $product_spec_list;
        
        // 图片处理
        $Details['litpic'] = handle_subdir_pic(get_default_pic($Details['litpic']));

        // 产品内页地址
        $New = get_archives_data([$Details], 'product_id');
        if (!empty($New)) {
            $Details['arcurl'] = urldecode(arcurl('home/Product/view', $New[$Details['product_id']]));
        } else {
            $Details['arcurl'] = urldecode(arcurl('home/View/index', ['aid'=>$Details['product_id']]));
        }

        $eyou = [
            'field' => $Details,
            'SubmitUrl' => url('user/ShopComment/product', ['_ajax'=>1])
        ];
        $this->assign('eyou', $eyou);

        return $this->fetch('shop_comment_product');
    }
}