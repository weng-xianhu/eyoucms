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
 * Date: 2020-05-07
 */

namespace app\api\model\v1;

use think\Db;
use think\Cache;
use think\Config;

/**
 * 微信小程序商城订单模型
 */
load_trait('controller/Jump');

class Order extends UserBase
{
    use \traits\controller\Jump;

    private $miniproInfo = [];

    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        $dataConf = tpSetting("OpenMinicode.conf_" . self::$provider, [], self::$lang);
        $this->miniproInfo = json_decode($dataConf, true);
    }

    //视频订单列表
    public function mediaOrderList()
    {
        $param = input('param.');
        $page = !empty($param['page']) ? intval($param['page']) : 1;
        $pagesize = empty($param['pagesize']) ? config('paginate.list_rows') : 10;

        $condition['a.users_id'] = $this->users_id;
        $condition['a.order_status'] = 1;//默认查询已购买

        $paginate = ['page' => $page];
        $pages = Db::name('media_order')->where($condition)
            ->field('a.*,c.aid,c.typeid,c.channel,d.*,a.add_time as order_add_time')
            ->alias('a')
            ->join('__ARCHIVES__ c', 'a.product_id = c.aid', 'LEFT')
            ->join('__ARCTYPE__ d', 'c.typeid = d.id', 'LEFT')
            ->order('a.order_id desc')
            ->paginate($pagesize, false, $paginate);
        $result = $pages->toArray();

        if (!empty($result['data'])) {
            foreach ($result['data'] as $key => $value) {
                $result['data'][$key]['product_litpic'] = get_default_pic($value['product_litpic'], true);
                $result['data'][$key]['pay_time'] = date('Y-m-d H:i:s',$value['pay_time']);
            }
        }

        return $result;
    }

    // 视频订单详情页
    public function mediaOrderDetails()
    {
        $order_id = input('param.order_id');
        if (!empty($order_id)) {
            // 查询订单信息
            $OrderData = Db::name('media_order')
                ->field('a.*, product_id,c.aid,c.typeid,c.channel,d.*')
                ->alias('a')
                ->join('__ARCHIVES__ c', 'a.product_id = c.aid', 'LEFT')
                ->join('__ARCTYPE__ d', 'c.typeid = d.id', 'LEFT')
                ->find($order_id);

            $OrderData['product_litpic'] = get_default_pic($OrderData['product_litpic'], true);
            return $OrderData;
        }
        return [];
    }

    //播放记录列表
    public function playList()
    {
        $param = input('param.');
        $page = !empty($param['page']) ? intval($param['page']) : 1;
        $pagesize = empty($param['pagesize']) ? config('paginate.list_rows') : 10;
        $condition['a.users_id'] = $this->users_id;

        $paginate = ['page' => $page];
        $total_field = 'select sum(file_time) as total_time from ' . PREFIX . 'media_file where aid= a.aid';
        
        // 数据查询
        $list = Db::name('media_play_record')
            ->where($condition)
            ->alias('a')
            ->field("a.id as play_id,a.file_id,a.aid,sum(a.play_time) as sum_play_time,max(a.update_time) as last_update_time,c.*,b.*,({$total_field}) as total_time,(sum(a.play_time)/({$total_field})) as process,a.users_id")
            ->join('archives b', 'a.aid=b.aid', 'inner')
            ->join('arctype c', 'b.typeid=c.id', 'left')
            ->group('a.aid')
            ->order('process desc')
            ->paginate($pagesize, false, $paginate);
        $list = $list->toArray();

        $total_time = 0;
        if (!empty($list['data'])) {
            // 订单处理
            foreach ($list['data'] as $key => $val) {
                $total_time += $val['sum_play_time'];
                $val['process'] = (round($val['process'], 2) * 100) . "%";
                $val['sum_play_time'] = gmSecondFormat($val['sum_play_time'], ':');
                $val['sum_file_time'] = gmSecondFormat($val['total_time'], ':');
                $val['last_update_time'] = date('Y-m-d H:i:s', $val['last_update_time']);
                $val['litpic'] = get_default_pic($val['litpic'], true);

                $list['data'][$key] = $val;
            }
            $total_time = gmSecondFormat($total_time, ':');
        }
        $list['total_time'] = $total_time;

        return $list;
    }

    //获取会员升级订单列表
    public function getUpgradeLevelOrderList()
    {
        $param = input('param.');
        $page = !empty($param['page']) ? intval($param['page']) : 1;
        $pagesize = empty($param['pagesize']) ? config('paginate.list_rows') : 10;

        $condition['a.users_id'] = $this->users_id;
        $condition['a.status'] = 2;//默认查询已购买
        $condition['a.cause_type'] = 0;//默认查询已购买
        $condition['a.level_id'] = ['gt', 0];//默认查询已购买

        $paginate = ['page' => $page];
        $list = Db::name('users_money')
            ->where($condition)
            ->alias('a')
            ->order('a.moneyid desc')
            ->paginate($pagesize, false, $paginate);
        $list = $list->toArray();

        if (!empty($list['data'])) {
            foreach ($list['data'] as $key => $val) {
                $list['data'][$key]['cause'] = !empty($val['cause']) ? unserialize($val['cause']) : '';
                $list['data'][$key]['pay_details'] = !empty($val['pay_details']) ? unserialize($val['pay_details']) : '';
            }
        }

        return $list;
    }
}