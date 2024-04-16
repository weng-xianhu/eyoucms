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
namespace app\common\model;

use think\Db;
use think\Page;
use think\Model;
use think\Config;

/**
 * 会员级别
 */
load_trait('controller/Jump');
class UsersLevel extends Model
{
    use \traits\controller\Jump;

    private $lang = 'cn';
    private $main_lang = 'cn';

    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        $this->times = getTime();
        $this->lang = get_current_lang();
        $this->main_lang = get_main_lang();
        $this->users_db  = Db::name('users');
        $this->users_level_db  = Db::name('users_level');
        $this->users_type_manage_db = Db::name('users_type_manage');
    }

    /**
     * 校验唯一性
     * @author wengxianhu by 2017-7-26
     */
    public function isRequired($id_name='',$id_value='',$field='',$value='')
    {
        $return = true;
        if ('ask_is_release' == $field || 'ask_is_review' == $field) return $return;
        $value = trim($value);
        if (!empty($value)) {
            $field == 'level_value' && $value = intval($value);

            $count = $this->where([
                    $field      => $value,
                    $id_name    => ['NEQ', $id_value],
                ])->count();
            if (!empty($count)) {
                $return = [
                    'msg'   => '数据不可重复',
                ];
            }
        }

        return $return;
    }

    public function getList($field = '*', $where = [], $index_key = '')
    {
        $map = [];
        if (!empty($where)) {
            $map = array_merge($map, $where);
        }
        if (!isset($map['lang'])) {
            $map['lang'] = $this->main_lang;
        }
        $result = Db::name('users_level')->field($field)->where($map)->cache(true, EYOUCMS_CACHE_TIME, "users_level")->order('level_value asc, level_id asc')->select();
        if (!empty($index_key)) {
            $result = convert_arr_key($result, $index_key);
        }
        
        return $result;
    }

    // 获取会员级别列表
    public function getUsersLevelList()
    {
        // 查询条件
        $where = [];
        $keywords = input('keywords/s');
        if (!empty($keywords)) $where['level_name'] = ['LIKE', "%{$keywords}%"];

        // 查询数据
        $count = $this->users_level_db->where($where)->count();
        $Page = new Page($count, config('paginate.list_rows'));
        $list = $this->users_level_db->where($where)->order('level_value asc, level_id asc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $level_ids = get_arr_column($list, 'level_id');
        if (!empty($level_ids)) {
            // 查询使用会员级别的会员数
            $field = 'level, count(users_id) as users_num';
            $usersNum = $this->users_db->field($field)->where(['level' => ['IN', $level_ids]])->group('level')->getAllWithIndex('level');

            // 查询会员级别是否已有升级套餐数
            $field = 'level_id, count(type_id) as manage_num';
            $manageNum = $this->users_type_manage_db->field($field)->where(['level_id' => ['IN', $level_ids]])->group('level_id')->getAllWithIndex('level_id');
        }

        foreach ($list as $key => $value) {
            // 查询使用会员级别的会员数
            $value['users_num'] = !empty($usersNum[$value['level_id']]) ? $usersNum[$value['level_id']]['users_num'] : 0;

            // 查询会员级别是否已有升级套餐数
            $value['manage_num'] = !empty($manageNum[$value['level_id']]) ? $manageNum[$value['level_id']]['manage_num'] : 0;

            // 价格处理
            $value['upgrade_order_money'] = !empty($value['upgrade_order_money']) ? unifyPriceHandle($value['upgrade_order_money']) : 0;

            // 折扣权益处理
            $value['discount'] = 100 === intval($value['discount']) || empty($value['discount_type']) ? 0 : $this->handleLevelDiscount($value['discount']);
            $list[$key] = $value;
        }

        // 返回内容
        return [
            'list' => $list,
            'pager' => $Page,
            'page' => $Page->show()
        ];
    }

    // 获取会员级别详情信息
    public function getUsersLevelDetails()
    {
        // 查询会员级别信息
        $level_id = input('level_id/d', 0);
        $where = [
            'level_id' => intval($level_id),
        ];
        $usersLevel = $this->users_level_db->where($where)->find();
        if (empty($usersLevel)) $this->error('会员级别不存在');

        // 处理会员级别信息
        if (1 === intval($usersLevel['discount_type']) && 100 === intval($usersLevel['discount'])) {
            $usersLevel['discount'] = '';
            $usersLevel['discount_type'] = 0;
        }
        if (!empty($usersLevel['discount'])) $usersLevel['discount'] = $this->handleLevelDiscount($usersLevel['discount']);

        // 返回结束
        return $usersLevel;
    }

    // 保存会员级别信息
    public function saveUsersLevelDetails($action = 'insert')
    {
        // 会员级别ID
        $level_id = input('level_id/d', 0);
        if (empty($level_id) && 'update' == $action) $this->error('会员级别ID丢失，刷新重试！');

        // 获取提交的数据
        $saveData = input('post.');

        // 数据处理
        if (empty($saveData['level_value'])) $this->error('请填写级别权重');
        if (empty($saveData['level_name'])) $this->error('请填写级别名称');
        if (!empty($saveData['upgrade_type']) && empty($saveData['upgrade_order_money'])) $this->error('请填写订单金额');
        if (!empty($saveData['discount_type']) && empty($saveData['discount'])) $this->error('请填写级别折扣权益');

        // 查询是否存在相同会员等级值
        $where = [
            'level_value' => $saveData['level_value'],
        ];
        if (!empty($level_id)) $where['level_id'] = ['NEQ', $level_id];
        $isCount = $this->users_level_db->where($where)->count();
        if (!empty($isCount)) $this->error('级别权重已存在');
        // 查询是否存在相同会员等级名称
        $where = [
            'level_name' => $saveData['level_name'],
        ];
        if (!empty($level_id)) $where['level_id'] = ['NEQ', $level_id];
        $isCount = $this->users_level_db->where($where)->count();
        if (!empty($isCount)) $this->error('级别名称已存在');

        // 保存数据处理
        $saveData['update_time'] = $this->times;
        $saveData['discount'] = !empty($saveData['discount']) ? $this->handleLevelDiscount($saveData['discount'], 1) : 0;
        if (0 === intval($saveData['discount_type'])) $saveData['discount'] = 100;
        if ('insert' == $action) {
            $saveData['add_time'] = $this->times;
            $result = $this->users_level_db->insert($saveData);
        } else if ('update' == $action) {
            $where = [
                'level_id' => intval($level_id),
            ];
            $result = $this->users_level_db->where($where)->update($saveData);
        }
        if (!empty($result)) {
            \think\Cache::clear('users_level');
            return true;
        }
        $this->error('操作失败，刷新重试！');
    }

    // 删除会员级别信息
    public function delUsersLevelDetails()
    {
        // 查询会员级别信息
        $level_id = input('level_id/d', 0);
        if (empty($level_id)) $this->error('会员级别ID丢失，刷新重试！');
        $where = [
            'level_id' => intval($level_id),
        ];
        $usersLevel = $this->users_level_db->where($where)->find();
        if (empty($usersLevel)) $this->error('会员级别不存在');

        // 删除指定会员级别
        $result = $this->users_level_db->where($where)->delete(true);
        if (!empty($result)) {
            // 更新使用被删除级别的会员为默认会员级别
            $where = [
                'level' => intval($level_id),
            ];
            $update = [
                'level' => intval($this->getDefaultLevelID()),
                'update_time' => $this->times,
            ];
            $this->users_db->where($where)->update($update);

            // 删除指定升级套餐列表
            $where = [
                'level_id' => intval($level_id),
            ];
            $this->users_type_manage_db->where($where)->delete(true);

            // 返回结束
            \think\Cache::clear('users_level');
            return true;
        }
        $this->error('操作失败，刷新重试！');
    }

    // 更新会员级别状态
    public function updateUsersLevelStatus()
    {
        // 会员等级ID
        $level_id = input('post.level_id/d', 0);
        if (empty($level_id)) $this->error('会员级别ID丢失，刷新重试！');
        // 更新条件
        $where = [
            'level_id' => intval($level_id)
        ];
        // 更新内容
        $status = input('post.status/d', 0);
        $update = [
            'status' => !empty($status) ? 0 : 1,
            'update_time' => getTime()
        ];
        // 执行更新
        $result = $this->users_level_db->where($where)->update($update);
        if (!empty($result)) {
            \think\Cache::clear('users_level');
            return true;
        }
        $this->error('操作失败，刷新重试！');
    }

    // 折扣百分比转换成显示折扣 (80% == 8折)
    public function handleLevelDiscount($discount = 0, $type = 0)
    {
        if (0 === intval($type)) {
            return floatval(sprintf("%.1f", $discount / 10));
        } else if (1 === intval($type)) {
            return floatval(sprintf("%.1f", $discount * 10));
        }
    }

    // 处理会员订单累计总额，用于会员自动升级
    public function handleUsersOrderTotalAmount($users = [], $order = [])
    {
        if (!empty($users['users_id']) && !empty($order['users_id']) && intval($users['users_id']) === intval($order['users_id'])) {
            // 增加会员订单累计总额
            $where = [
                'users_id' => intval($order['users_id'])
            ];
            $result = $this->users_db->where($where)->setInc('order_total_amount', $order['unified_amount']);
            if (!empty($result)) {
                // 判断是否足够自动升级会员
                $field = 'users_id, username, nickname, level, order_total_amount, open_level_time, level_maturity_days';
                $users = $this->users_db->field($field)->where($where)->find();

                // 查询当前会员级别的权重值
                $levelValue = $this->users_level_db->where(['level_id'=>intval($users['level'])])->getField('level_value');
                $levelValue = !empty($levelValue) ? intval($levelValue) : 10;

                // 查询满足订单金额自动升级的会员级别列表
                $field = 'level_id, level_name, level_value, upgrade_type, upgrade_order_money, status';
                $where = [
                    'status' => 1,
                    'upgrade_type' => 1,
                    'level_value' => ['GT', $levelValue]
                ];
                $levelList = $this->users_level_db->field($field)->where($where)->order('upgrade_order_money desc')->select();

                // 提取升级级别信息
                $update = [];
                $level_name = '';
                if (!empty($users['order_total_amount']) && !empty($levelList)) {
                    // 提取升级级别ID
                    $level_id = 0;
                    foreach ($levelList as $key => $value) {
                        if (unifyPriceHandle($users['order_total_amount']) >= unifyPriceHandle($value['upgrade_order_money'])) {
                            $level_id = intval($value['level_id']);
                            $level_name = strval($value['level_name']);
                            break;
                        }
                    }
                    if (!empty($level_id) && intval($level_id) !== intval($users['level'])) {
                        // 会员期限定义数组
                        // $limitArr = Config::get('global.admin_member_limit_arr');
                        // 查询当前会员级别是否有会员升级套餐设置，如果没有则默认为6=终身会员天数
                        // $limitID = $this->users_type_manage_db->where(['level_id'=>intval($level_id)])->getField('limit_id');
                        // $limitID = !empty($limitID) ? intval($limitID) : 6;
                        // 到期天数
                        $maturity_days = 36600;//!empty($limitArr[$limitID]['maturity_days']) ? intval($limitArr[$limitID]['maturity_days']) : 36600;
                        // 更新会员属性表的数组
                        $update = [
                            'level' => intval($level_id),
                            'update_time' => $this->times,
                            'level_maturity_days' => $maturity_days,
                            // 'level_maturity_days' => Db::raw('level_maturity_days+'.($maturity_days)),
                        ];
                        // 判断是否需要追加天数，maturity_code在Base层已计算，1表示终身会员天数
                        // 判断是否到期，到期则执行，3表示会员在期限内，不需要进行下一步操作
                        if (!in_array($users['maturity_code'], [1, 3]) || (0 === intval($users['open_level_time']) && 0 === intval($users['level_maturity_days']))) {
                            // 追加天数数组
                            $update['open_level_time'] = $this->times;
                            // $update['level_maturity_days'] = $maturity_days;
                        }
                    }
                }

                // 处理会员自动升级
                if (!empty($update)) {
                    $where = [
                        'users_id' => intval($order['users_id'])
                    ];
                    $result = $this->users_db->where($where)->update($update);
                    if (!empty($result)) {
                        // 发送站内信提醒用户
                        $data = [
                            'level_name' => $level_name,
                            'total_amount' => $users['order_total_amount'],
                        ];
                        $nickName = !empty($users['nickname']) ? $users['nickname'] : $users['username'];
                        SendNotifyMessage($data, 21, 0, $users['users_id'], $nickName);
                    }
                }
            }
        }
    }

    // 获取系统默认会员级别ID
    public function getDefaultLevelID()
    {
        $id = $this->users_level_db->where(['is_system' => 1])->getField('level_id');
        return !empty($id) ? intval($id) : 1;
    }
}