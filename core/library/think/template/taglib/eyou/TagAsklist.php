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

namespace think\template\taglib\eyou;

use think\Db;

/**
 * 问答列表
 */
class TagAsklist extends Base
{
    
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取分页列表
     * @author wengxianhu by 2018-4-20
     */
    public function getAsklist($limit = '', $orderby = '', $ordermode = '')
    {
        empty($ordermode) && $ordermode = 'desc';
        // 给排序字段加上表别名
        $orderby = $this->getOrderBy($orderby,$ordermode);

        $result = Db::name('ask')
            ->alias('a')
            ->field('a.*,b.type_name')
            ->join('ask_type b','a.type_id = b.type_id','left')
            ->limit($limit)
            ->orderRaw($orderby)
            ->select();
        if (!empty($result)){
            $users_ids = [];
            foreach ($result as $k => $v){
                array_push($users_ids, $v['users_id']);
                $result[$k]['ask_url'] = askurl('home/Ask/details', ['ask_id'=>$v['ask_id']]);
                $result[$k]['type_url'] = askurl('home/Ask/index', ['type_id'=>$v['type_id']]);
                $result[$k]['ey_hidden'] = ''; // 隐藏域
            }

            $usersList = [];
            $usersRow = Db::name('users')->field('a.*, b.level_name')
                ->alias('a')
                ->join('users_level b', 'a.level=b.level_id', 'LEFT')
                ->where(['a.users_id'=>['IN', $users_ids]])
                ->select();
            foreach ($usersRow as $key => $val) {
                // 会员头像
                $val['head_pic'] = get_head_pic(htmlspecialchars_decode($val['head_pic']));
                $val['head_pic'] = func_preg_replace(['http://thirdqq.qlogo.cn'], ['https://thirdqq.qlogo.cn'], $val['head_pic']);
                // 会员昵称
                $val['nickname'] = !empty($val['nickname']) ? $val['nickname'] : $val['username'];
                // 过滤密码
                unset($val['password']);
                unset($val['paypwd']);
                $usersList[$val['users_id']]['users'] = $val;
            }

            foreach ($result as $k => $v){
                !empty($usersList[$v['users_id']]) && $result[$k] += $usersList[$v['users_id']];
            }
        }

        return $result;
    }

    private function getOrderBy($orderby,$ordermode){
        switch ($orderby) {
            case 'click': // 浏览点击量
                $orderby = "a.click {$ordermode}";
                break;

            case 'id': // 兼容写法
            case 'aid':
            case 'ask_id':
                $orderby = "a.ask_id {$ordermode}";
                break;

            case 'now':
            case 'new': // 兼容写法
            case 'pubdate': // 兼容写法
            case 'add_time':
                $orderby = "a.add_time {$ordermode}";
                break;

            case 'sortrank': // 兼容写法
            case 'sort_order':
                $orderby = "a.sort_order {$ordermode}";
                break;

            case 'recom': // 推荐
                $orderby = "a.is_recom {$ordermode}";
                break;

            case 'replies': // 问题回复量
                $orderby = "a.replies {$ordermode}";
                break;

            case 'solve_time': // 解决时间
                $orderby = "a.solve_time {$ordermode}";
                break;

            case 'money': // 悬赏金额
                $orderby = "a.money {$ordermode}";
                break;

            case 'rand':
                $orderby = "rand()";
                break;

            default:
            {
                if (empty($orderby)) {
                    $orderby = 'a.sort_order asc, a.ask_id desc';
                } elseif (trim($orderby) != 'rand()') {
                    $orderbyArr = explode(',', $orderby);
                    foreach ($orderbyArr as $key => $val) {
                        $val = trim($val);
                        if (preg_match('/^([a-z]+)\./i', $val) == 0) {
                            $val = 'a.'.$val;
                            $orderbyArr[$key] = $val;
                        }
                    }
                    $orderby = implode(',', $orderbyArr);
                }
                break;
            }
        }

        return $orderby;
    }
}