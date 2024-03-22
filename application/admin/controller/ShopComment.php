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
 * Date: 2021-01-26
 */

namespace app\admin\controller;

use think\Page;
use think\Db;
use think\Config;
use app\admin\logic\ShopLogic;

class ShopComment extends Base
{
    // 模型ID
    public $channeltype = 2;
    public $ShopLogic;

    public function _initialize()
    {
        parent::_initialize();

        $functionLogic = new \app\common\logic\FunctionLogic;
        $functionLogic->check_authorfile(2);

        // 产品属性表
        $this->shop_order_comment_db = Db::name('shop_order_comment');
        $this->ShopLogic = new ShopLogic;
        
        // 列出营销功能里已使用的模块
        $marketFunc = $this->ShopLogic->marketLogic();
        $this->assign('marketFunc', $marketFunc);
    }

    // 评价列表
    public function comment_index()
    {
        $functionLogic = new \app\common\logic\FunctionLogic;
        $assign_data = $functionLogic->comment_index();
        $this->assign($assign_data);

        $shopOpenComment = getUsersConfigData('shop.shop_open_comment');
        if (!empty($shopOpenComment) && 1 === intval($shopOpenComment)) {
            tpCache('web', ['web_shopcomment_switch' => 1]);
        } else if (isset($shopOpenComment) && 0 === intval($shopOpenComment)) {
            tpCache('web', ['web_shopcomment_switch' => 0]);
        }
        $web_shopcomment_switch = tpCache('global.web_shopcomment_switch');
        $this->assign('web_shopcomment_switch', intval($web_shopcomment_switch));

        return $this->fetch();
    }

    // 评价详情
    public function comment_details()
    {
        $comment_id = input('param.comment_id/d');
        if (!empty($comment_id)) {
            // 退换货信息
            $field = 'a.comment_id, a.order_id, a.users_id, a.order_code, a.product_id, a.upload_img, a.admin_reply, a.content, a.total_score, a.is_show, a.add_time, b.product_name, b.product_price, b.litpic as product_img, b.num as product_num, c.username, c.nickname, c.head_pic, d.title, d.users_price, d.litpic';
            $Comment[0] = $this->shop_order_comment_db->alias('a')->where('comment_id', $comment_id)
                ->field($field)
                ->join('__SHOP_ORDER_DETAILS__ b', 'a.details_id = b.details_id', 'LEFT')
                ->join('__USERS__ c', 'a.users_id = c.users_id')
                ->join('__ARCHIVES__ d', 'a.product_id = d.aid')
                ->find();
            $array_new = get_archives_data($Comment, 'product_id');
            $Comment = $Comment[0];
            // 如果不存在商品标题则执行
            if (empty($Comment['product_name'])) $Comment['product_name'] = $Comment['title'];
            // 如果不存在商品价格则执行
            if (empty($Comment['product_price'])) $Comment['product_price'] = $Comment['users_price'];
            // 如果不存在商品数量则执行
            if (empty($Comment['product_num'])) $Comment['product_num'] = 1;
            // 评价上传的图片
            $Comment['upload_img'] = !empty($Comment['upload_img']) ? explode(',', unserialize($Comment['upload_img'])) : [];
            foreach ($Comment['upload_img'] as $key => $value) {
                if (empty($value)) {
                    unset($Comment['upload_img'][$key]);
                } else {
                    $Comment['upload_img'][$key] = handle_subdir_pic(get_default_pic($value));
                }
            }
            // 如果不存在商品图则执行
            if (empty($Comment['product_img'])) $Comment['product_img'] = $Comment['litpic'];
            // 商品图片
            $Comment['product_img']  = handle_subdir_pic(get_default_pic($Comment['product_img']));
            // 商品链接
            $Comment['arcurl'] = get_arcurl($array_new[$Comment['product_id']]);
            // 商品评价评分
            $Comment['order_total_score'] = Config::get('global.order_total_score')[$Comment['total_score']];
            // 评价转换星级评分，注释暂停使用，显示实际星评分
            // $Comment['total_score'] = GetScoreArray($Comment['total_score']);
            // 评价的内容
            $Comment['content'] = !empty($Comment['content']) ? htmlspecialchars_decode(unserialize($Comment['content'])) : '';
            // 回复的内容
            $adminReply = !empty($Comment['admin_reply']) ? unserialize($Comment['admin_reply']) : [];
            $adminReply['adminReply'] = !empty($adminReply['adminReply']) ? htmlspecialchars_decode($adminReply['adminReply']) : '';
            $Comment['admin_reply'] = $adminReply;
            // 会员信息
            // $Users = Db::name('users')->field('users_id, username, nickname, mobile')->find($Comment['users_id']);
            $Comment['nickname'] = empty($Comment['nickname']) ? $Comment['username'] : $Comment['nickname'];
            $Comment['head_pic'] = handle_subdir_pic(get_default_pic($Comment['head_pic']));
            // 是否属于后台系统评价
            $Comment['systemComment'] = 0;
            if (empty($Comment['order_id']) && empty($Comment['order_code']) && empty($Comment['details_id'])) {
                $Comment['systemComment'] = 1;
            }
            // dump($Comment);exit;
            // 加载数据
            // $this->assign('Users', $Users);
            $this->assign('Comment', $Comment);
            return $this->fetch('comment_details');
        } else {
            $this->error('非法访问！');
        }
    }

    // 评价删除
    public function comment_del()
    {
        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if(IS_POST && !empty($id_arr)){
            $ResultID = $this->shop_order_comment_db->where(['comment_id' => ['IN', $id_arr]])->delete();
            if (!empty($ResultID)) {
                foreach ($id_arr as $key => $val) {
                    cache('EyouHomeAjaxComment_' . $val, null, null, 'shop_order_comment');
                }
                adminLog('删除评价-id：'.implode(',', $id_arr));
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
    }

    // 商家回复
    public function comment_admin_reply()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 参数判断
            empty($post['comment_id']) && $this->error('请选择回复的评价');
            empty($post['admin_reply']) && $this->error('请填写回复的内容');
            $time = getTime();
            // 拼装评价数据
            $adminReply = [
                'adminReply' => $post['admin_reply'],
                'replyTime' => date('Y-m-d H:i:s', $time),
            ];
            $update = [
                'comment_id' => intval($post['comment_id']),
                'admin_reply' => serialize($adminReply),
                'update_time' => $time,
            ];
            // 添加商品自定义评价
            $updateID = $this->shop_order_comment_db->update($update);
            if (!empty($updateID)) {
                $this->success('回复成功');
            } else {
                $this->error('回复失败');
            }
        }
    }

    // 评价添加
    public function comment_add()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 参数判断
            empty($post['aid']) && $this->error('请选择商品', null, ['post'=>true]);
            empty($post['users_id']) && $this->error('请选择会员', null, ['post'=>true]);
            empty($post['add_time']) && $this->error('请选择评价时段');
            // 评分处理
            // $PostTotalScore = 1;
            // if (in_array($post['total_score'], [3, 4])) {
            //  $PostTotalScore = 2;
            // } else if (in_array($post['total_score'], [1, 2])) {
            //  $PostTotalScore = 3;
            // }
            // 时间处理
            $PostAddTime = explode(' ~ ', $post['add_time']);
            $s_time = strtotime($PostAddTime[0]);
            $e_time = strtotime($PostAddTime[1]);
            // 处理会员
            $users_id = explode(',', $post['users_id']);
            // 添加数据
            $insertAll = [];
            foreach ($post['content'] as $key => $value) {
                // 如果没有评价内容则提示
                empty($value) && $this->error('请添加评价内容', null, ['obj'=>'#comment_add_textarea_' . $key]);
                // 图片处理
                $upload_img = !empty($post['upload_img'][$key]) ? implode(',', $post['upload_img'][$key]) : '';
                // 随机时间处理
                $time = mt_rand($s_time, $e_time);
                $time = !empty($time) ? $time : getTime();
                // 拼装评价数据
                $insertAll[] = [
                    'users_id' => intval($users_id[intval($key) - 1]),
                    'order_id' => 0,
                    'order_code' => 0,
                    'details_id' => 0,
                    'product_id' => intval($post['aid']),
                    'total_score' => intval($post['total_score']),
                    'content' => serialize($value),
                    'upload_img' => serialize($upload_img),
                    'admin_reply' => '',
                    'ip_address' => clientIP(),
                    'is_show' => intval($post['is_show']),
                    'is_anonymous' => 0,
                    'is_new_comment' => 1,
                    'lang' => $this->admin_lang,
                    'add_time' => $time,
                    'update_time' => $time,
                ];
            }
            // 添加商品自定义评价
            $insertID = $this->shop_order_comment_db->insertAll($insertAll);
            if (!empty($insertID)) {
                $this->success("添加成功", url('ShopComment/comment_index'));
            } else {
                $this->error("添加失败");
            }
        }

        return $this->fetch();
    }

    public function comment_goods_list()
    {
        // 查询处理
        $where = [];
        // 关键字查询
        $keywords = input('param.keywords', '');
        if (!empty($keywords)) $where['a.title'] = ['LIKE', "%{$keywords}%"];
        // 栏目查询
        $typeids = Db::name('arctype')->where('current_channel', $this->channeltype)->column('id');
        if (!empty($typeids)) $where['a.typeid'] = ['IN', $typeids];
        // 合并查询条件
        $where = array_merge($where, ['a.channel' => $this->channeltype, 'a.lang' => $this->admin_lang, 'a.is_del' => 0]);
        $whereNew = "(a.users_id = 0 OR (a.users_id > 0 AND a.arcrank >= 0))";

        // 数据查询，搜索出主键ID的值
        $SqlQuery = Db::name('archives')->alias('a')->where($where)->where($whereNew)->fetchSql()->count('aid');
        $count = Db::name('sql_cache_table')->where(['sql_md5'=>md5($SqlQuery)])->getField('sql_result');
        $count = ($count < 0) ? 0 : $count;
        if (empty($count)) {
            $count = Db::name('archives')->alias('a')->where($where)->where($whereNew)->count('aid');
            /*添加查询执行语句到mysql缓存表*/
            $SqlCacheTable = [
                'sql_name' => '|product|' . $this->channeltype . '|',
                'sql_result' => $count,
                'sql_md5' => md5($SqlQuery),
                'sql_query' => $SqlQuery,
                'add_time' => getTime(),
                'update_time' => getTime(),
            ];
            if (!empty($keywords)) $SqlCacheTable['sql_name'] = '|product|keywords|';
            Db::name('sql_cache_table')->insertGetId($SqlCacheTable);
            /*END*/
        }

        // 自定义排序
        $orderby = input('param.orderby/s');
        $orderway = input('param.orderway/s');
        if (!empty($orderby) && !empty($orderway)) {
            $orderby = "a.{$orderby} {$orderway}, a.aid desc";
        } else {
            $orderby = "a.aid desc";
        }

        // 查询商品数据
        $Page = new Page($count, config('paginate.list_rows'));
        $list = [];
        if (!empty($count)) {
            $limit = $count > config('paginate.list_rows') ? $Page->firstRow.','.$Page->listRows : $count;
            $list = Db::name('archives')
                ->field("a.aid")
                ->alias('a')
                ->where($where)
                ->where($whereNew)
                ->order($orderby)
                ->limit($limit)
                ->getAllWithIndex('aid');
            if (!empty($list)) {
                $aids = array_keys($list);
                $fields = "b.*, a.*, a.aid as aid";
                $row = Db::name('archives')
                    ->field($fields)
                    ->alias('a')
                    ->join('__ARCTYPE__ b', 'a.typeid = b.id', 'LEFT')
                    ->where('a.aid', 'in', $aids)
                    ->getAllWithIndex('aid');
                foreach ($list as $key => $val) {
                    $row[$val['aid']]['arcurl'] = get_arcurl($row[$val['aid']]);
                    $row[$val['aid']]['litpic'] = get_default_pic($row[$val['aid']]['litpic']);
                    $list[$key] = $row[$val['aid']];
                }
            }
        }

        $assign_data['page'] = $Page->show();
        $assign_data['list'] = $list;
        $assign_data['pager'] = $Page;
        $this->assign($assign_data);
        return $this->fetch();
    }

    public function comment_users_list()
    {
        // 查询处理
        $where = [];
        // 关键字查询
        $keywords = input('param.keywords/s', '');
        if (!empty($keywords)) $where['a.username|a.nickname|a.mobile|a.email|a.users_id'] = ['LIKE', "%{$keywords}%"];
        // 会员等级
        $level = input('param.level/d', 0);
        if (!empty($level)) $where['a.level'] = $level;
        // 合并查询条件
        $where = array_merge($where, ['a.lang' => $this->admin_lang, 'a.is_del' => 0]);

        // 查询会员信息
        $count = Db::name('users')->alias('a')->where($where)->count();
        $Page = new Page($count, config('paginate.list_rows'));
        $list = Db::name('users')->field('a.*, b.level_name')
            ->alias('a')
            ->join('__USERS_LEVEL__ b', 'a.level = b.level_id', 'LEFT')
            ->where($where) 
            ->order('a.users_id desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();
        $users_ids = [];
        foreach ($list as $key => $value) {
            $users_ids[] = $value['users_id'];
            $value['username'] = !empty($value['nickname']) ? $value['nickname'] : $value['username'];
            $value['head_pic'] = get_head_pic($value['head_pic']);
            $list[$key] = $value;
        }
        $this->assign('list', $list);
        $this->assign('pager', $Page);
        $this->assign('page', $Page->show());

        // 微信登录插件
        $wxlogin = [];
        if (is_dir('./weapp/WxLogin/')) {
            $wxlogin = Db::name('weapp_wxlogin')->where(['users_id'=>['IN', $users_ids]])->getAllWithIndex('users_id');
        }
        $this->assign('wxlogin', $wxlogin);
        
        // QQ登录插件
        $qqlogin = [];
        if (is_dir('./weapp/QqLogin/')) {
            $qqlogin = Db::name('weapp_qqlogin')->where(['users_id'=>['IN', $users_ids]])->getAllWithIndex('users_id');
        }
        $this->assign('qqlogin', $qqlogin);

        // 计算会员人数
        $levelCountList = [
            'all' => [
                'level_id'      => 0,
                'level_name'    => '全部会员',
                'level_count'   => 0,
            ],
        ];
        $LevelData = model('UsersLevel')->getList('level_id, level_name', [], 'level_id');
        $levelCountRow = Db::name('users')->field('count(users_id) as num, level')->order('level asc')->group('level')->getAllWithIndex('level');
        foreach ($LevelData as $key => $val) {
            $level_num = empty($levelCountRow[$val['level_id']]) ? 0 : $levelCountRow[$val['level_id']]['num'];
            $levelCountList[$val['level_id']] = [
                'level_id'      => $val['level_id'],
                'level_name'    => $val['level_name'],
                'level_count'   => $level_num,
            ];
            $levelCountList['all']['level_count'] += $level_num;
        }
        $this->assign('levelCountList', $levelCountList);

        return $this->fetch();
    }

    // 评价添加
    public function comment_edit()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            // 参数判断
            empty($post['content']) && $this->error('请添加评价内容');
            if (!empty($post['isPost'])) {
                empty($post['add_time']) && $this->error('请选择评价时段');
                // 评分处理
                // $PostTotalScore = 1;
                // if (in_array($post['total_score'], [3, 4])) {
                //  $PostTotalScore = 2;
                // } else if (in_array($post['total_score'], [1, 2])) {
                //  $PostTotalScore = 3;
                // }
                // 图片处理
                $upload_img = !empty($post['upload_img']) ? implode(',', $post['upload_img']) : '';
                // 拼装评价数据
                $update = [
                    'comment_id' => intval($post['comment_id']),
                    'total_score' => intval($post['total_score']),
                    'content' => serialize($post['content']),
                    'upload_img' => serialize($upload_img),
                    'ip_address' => clientIP(),
                    'is_show' => intval($post['is_show']),
                    'add_time' => strtotime($post['add_time']),
                    'update_time' => getTime(),
                ];
            } else {
                $upload_img = !empty($post['upload_img']) ? implode(',', $post['upload_img']) : '';
                $update = [
                    'comment_id' => intval($post['comment_id']),
                    'content' => serialize($post['content']),
                    'is_show' => intval($post['is_show']),
                    'upload_img' => serialize($upload_img),
                    'update_time' => getTime(),
                ];
            }
            // 添加商品自定义评价
            $updateID = $this->shop_order_comment_db->update($update);
            if (!empty($updateID)) {
                $this->success("编辑成功", url('ShopComment/comment_index'));
            } else {
                $this->error("编辑失败");
            }
        }
    }

    /**
     * 开启/关闭评价模块功能
     * @return [type] [description]
     */
    public function ajax_open_close()
    {
        if (IS_AJAX_POST) {
            $value = input('param.value/d');
            if (1 == $value) {
                $web_shopcomment_switch = 0;
            } else {
                $web_shopcomment_switch = 1;
            }
            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache('web', ['web_shopcomment_switch'=>$web_shopcomment_switch], $val['mark']);
                }
            } else { // 单语言
                tpCache('web', ['web_shopcomment_switch'=>$web_shopcomment_switch]);
            }
            /*--end*/
            $this->success("操作成功");
        }
        $this->error("操作失败");
    }
}