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

namespace think\template\taglib\api;

use think\Db;

/**
 * 提货点列表
 */
class TagPickUpPointsList extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取订单评论列表
     */
    public function apiPickUpPointsList($page = 1, $pagesize = 10, $parse = [])
    {

        $where['status'] = 0;
        $field = '*';
        $order = 'id desc';
        if (!empty($parse['latitude']) && !empty($parse['longitude'])) {
            $res = Convert_GCJ02_To_BD09($parse['latitude'], $parse['longitude']);
            $lat = $res['lat'];
            $lng = $res['lng'];
            if (!empty($lat) && !empty($lng)) {
                $earth = 6378.138;
                $f_lat = 'lat';
                $f_lng = 'lng';
                $d_field = ",({$earth}*2*ASIN(SQRT(POW(SIN(({$f_lat}*PI()/180-{$lat}*PI()/180)/2),2)+COS({$f_lat}*PI()/180)*COS({$lat}*PI()/180)* POW(SIN(({$f_lng}*PI()/180-{$lng}*PI()/180)/2),2)))*1000) as distance";
                $field .= $d_field;
                $order = 'distance asc';
            }
        }

        $paginate = array(
            'page' => $page,
        );
        $pages = Db::name('pick_up_points')
            ->field($field)
            ->where($where)
            ->order($order)
            ->paginate($pagesize, false, $paginate);
        $result = $pages->toArray();
        if (!empty($result['data'])) {
            foreach ($result['data'] as $key => $val) {
                $val['province'] = get_region_name($val['province']);
                $val['city'] = get_region_name($val['city']);
                $val['area'] = get_region_name($val['area']);
                if (empty($val['logo'])){
                    $val['logo'] = '/public/static/common/images/logo.png';
                }
                $val['logo'] = $this->get_default_pic($val['logo']);
                $val['litpic'] = $this->get_default_pic($val['litpic']);
                if (!empty($val['distance'])) {
                    if ($val['distance'] < 1000) {
                        $val['distance'] = ceil($val['distance']);
                        $val['unit'] = 'm';
                    } else {
                        $val['distance'] = round($val['distance'] / 1000, 1);
                        $val['unit'] = 'km';
                    }
                }
                $location = Convert_BD09_To_GCJ02($val['lat'], $val['lng']);
                $val['lat'] = $location['lat'];
                $val['lng'] = $location['lng'];
                $result['data'][$key] = $val;
            }
        }
        return $result;
    }


}

