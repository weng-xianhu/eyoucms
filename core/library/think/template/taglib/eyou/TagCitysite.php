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
 * Date: 2021-7-18
 */

namespace think\template\taglib\eyou;

use think\Db;
use think\Request;

/**
 * 多城市列表
 */
class TagCitysite extends Base
{
    public $currentclass = '';

    /**
     * 获取指定级别的城市站点列表
     * @param string type son表示下一级城市站点,self表示同级城市站点,top顶级城市站点
     * @param boolean $self 包括自己本身
     * @author wengxianhu by 2018-4-26
     */
    public function getCitysite($siteid = '', $type = 'top', $currentclass = '', $nositeid = '')
    {
        if (!self::$city_switch_on) {
            return false;
        }
        $this->currentclass = $currentclass;
        $siteid  = !empty($siteid) ? $siteid : self::$siteid;
        $result = $this->getSwitchType($siteid, $type, $nositeid);

        return $result;
    }

    /**
     * 获取指定级别的城市站点列表
     * @param string type son表示下一级城市站点,self表示同级城市站点,top顶级城市站点
     * @param boolean $self 包括自己本身
     * @author wengxianhu by 2018-4-26
     */
    public function getSwitchType($siteid = '', $type = 'top', $nositeid = '')
    {
        $result = array();
        switch ($type) {
            case 'son': // 下级城市站点
                $result = $this->getSon($siteid, false);
                break;

            case 'self': // 同级城市站点
                $result = $this->getSelf($siteid);
                break;

            case 'top': // 顶级城市站点
                $result = $this->getTop($nositeid);
                break;

            case 'sonself': // 下级、同级城市站点
                $result = $this->getSon($siteid, true);
                break;

            case 'first': // 第一级城市站点
                $result = $this->getFirst($siteid);
                break;
        }

        return $result;
    }

    /**
     * 获取下一级城市站点
     * @param string $self true表示没有子城市站点时，获取同级城市站点
     * @author wengxianhu by 2017-7-26
     */
    public function getSon($siteid, $self = false)
    {
        $result = array();
        if (empty($siteid)) {
            return $result;
        }

        $citysite_max_level = intval(config('global.citysite_max_level')); // 城市站点最多级别
        /*获取所有显示且有效的城市站点列表*/
        $map = array(
            'c.status'  => 1,
        );
        $fields = "c.*, c.id as siteid, count(s.id) as has_children, '' as children";
        $args = [$fields, $map];
        $cacheKey = 'citysite-'.md5(__CLASS__.__FUNCTION__.json_encode($args));
        $res = cache($cacheKey);
        if (empty($res)) {
            $res = Db::name('citysite')
                ->field($fields)
                ->alias('c')
                ->join('__CITYSITE__ s','s.parent_id = c.id','LEFT')
                ->where($map)
                ->group('c.id')
                ->order('c.parent_id asc, c.sort_order asc, c.id')
                ->cache(true,EYOUCMS_CACHE_TIME,"citysite")
                ->select();
            cache($cacheKey, $res, null, 'citysite');
        }
        /*--end*/
        if ($res) {
            $citysiteList = get_citysite_list();
            foreach ($res as $key => $val) {
                $val['siteurl'] = siteurl($val);
                /*标记城市站点被选中效果*/
                if (in_array($val['id'], [self::$siteid, self::$site_info['parent_id'], self::$site_info['topid']])) {
                    $val['currentclass'] = $val['currentstyle'] = $this->currentclass;
                } else {
                    $val['currentclass'] = $val['currentstyle'] = '';
                }
                /*--end*/
                $res[$key] = $val;
            }
        }

        /*城市站点层级归类成阶梯式*/
        $arr = group_same_key($res, 'parent_id');
        for ($i=0; $i < $citysite_max_level; $i++) {
            foreach ($arr as $key => $val) {
                foreach ($arr[$key] as $key2 => $val2) {
                    if (!isset($arr[$val2['id']])) continue;
                    $val2['children'] = $arr[$val2['id']];
                    $arr[$key][$key2] = $val2;
                }
            }
        }
        /*--end*/

        /*取得指定城市站点ID对应的阶梯式所有子孙等城市站点*/
        $result = array();
        $siteidArr = explode(',', $siteid);
        foreach ($siteidArr as $key => $val) {
            if (!isset($arr[$val])) continue;
            if (is_array($arr[$val])) {
                foreach ($arr[$val] as $key2 => $val2) {
                    array_push($result, $val2);
                }
            } else {
                array_push($result, $arr[$val]);
            }
        }
        /*--end*/

        /*没有子城市站点时，获取同级城市站点*/
        if (empty($result) && $self == true) {
            $result = $this->getSelf($siteid);
        }
        /*--end*/

        return $result;
    }

    /**
     * 获取当前城市站点的第一级城市站点下的子城市站点
     * @author wengxianhu by 2017-7-26
     */
    public function getFirst($siteid)
    {
        $result = array();
        if (empty($siteid)) {
            return $result;
        }

        $row = model('Citysite')->getAllPid($siteid); // 当前城市站点往上一级级父城市站点
        if (!empty($row)) {
            reset($row); // 重置排序
            $firstResult = current($row); // 顶级城市站点下的第一级父城市站点
            $siteid = isset($firstResult['id']) ? $firstResult['id'] : '';
            $sonRow = $this->getSon($siteid, false); // 获取第一级城市站点下的子孙城市站点，为空时不获得同级城市站点
            $result = $sonRow;
        }

        return $result;
    }

    /**
     * 获取同级城市站点
     * @author wengxianhu by 2017-7-26
     */
    public function getSelf($siteid)
    {
        $result = array();
        if (empty($siteid)) {
            return $result;
        }

        /*获取指定城市站点ID的上一级城市站点ID列表*/
        $args = [$siteid];
        $cacheKey = 'citysite-'.md5(__CLASS__.__FUNCTION__.json_encode($args));
        $res = cache($cacheKey);
        if (empty($res)) {
            $map = array(
                'id'   => array('in', $siteid),
                'status'  => 1,
            );
            $res = Db::name('citysite')->field('parent_id')
                ->where($map)
                ->group('parent_id')
                ->select();
            cache($cacheKey, $res, null, 'citysite');
        }
        /*--end*/

        /*获取上一级城市站点ID对应下的子孙城市站点*/
        if ($res) {
            $siteidArr = get_arr_column($res, 'parent_id');
            $siteid = implode(',', $siteidArr);
            if ($siteid == 0) {
                $result = $this->getTop();
            } else {
                $result = $this->getSon($siteid, false);
            }
        }
        /*--end*/

        return $result;
    }

    /**
     * 获取顶级城市站点
     * @author wengxianhu by 2017-7-26
     */
    public function getTop($nositeid = '')
    {
        $result = array();

        /*获取所有城市站点*/
        $citysiteLogic = new \app\common\logic\CitysiteLogic();
        $citysite_max_level = intval(config('global.citysite_max_level'));
        $map = array(
            'status'    => 1,
        );
        !empty($nositeid) && $map['id'] = ['NOTIN', $nositeid]; // 排除指定城市站点ID
        $res = $citysiteLogic->citysite_list(0, 0, false, $citysite_max_level, $map);
        /*--end*/

        if (count($res) > 0) {
            $citysiteList = get_citysite_list();
            $siteInfo = !empty($citysiteList[self::$siteid]) ? $citysiteList[self::$siteid] : [];
            $currentclassArr = [];
            $currentclassArr_tmp = [self::$siteid];
            !empty($siteInfo['parent_id']) && array_push($currentclassArr_tmp, $siteInfo['parent_id']);
            !empty($siteInfo['topid']) && array_push($currentclassArr_tmp, $siteInfo['topid']);
            foreach ($res as $key => $val) {
                $val['siteurl'] = siteurl($val);
                /*标记城市站点被选中效果*/
                $val['currentclass'] = $val['currentstyle'] = '';
                if (in_array($val['id'], $currentclassArr_tmp)) {
                    array_push($currentclassArr, $val['id']);
                }
                /*--end*/

                $res[$key] = $val;
            }

            // 循环处理选中城市站点的标识
            foreach ($res as $key => $val) {
                if (in_array($val['id'], $currentclassArr)) {
                    $val['currentclass'] = $val['currentstyle'] = $this->currentclass;
                }
                $res[$key] = $val;
            }

            /*城市站点层级归类成阶梯式*/
            $arr = group_same_key($res, 'parent_id');
            for ($i=0; $i < $citysite_max_level; $i++) { 
                foreach ($arr as $key => $val) {
                    foreach ($arr[$key] as $key2 => $val2) {
                        if (!isset($arr[$val2['id']])) continue;
                        $val2['children'] = $arr[$val2['id']];
                        $arr[$key][$key2] = $val2;
                    }
                }
            }
            /*--end*/

            reset($arr);  // 重置数组
            /*获取第一个数组*/
            $firstResult = current($arr);
            $result = $firstResult;
            /*--end*/
        }

        return $result;
    }

    /**
     * 获取最顶级父城市站点ID
     */
    public function getTopSiteid($siteid)
    {
        $topSiteid = 0;
        if ($siteid > 0) {
            $result = model('Citysite')->getAllPid($siteid); // 当前城市站点往上一级级父城市站点
            reset($result); // 重置数组
            /*获取最顶级父城市站点ID*/
            $firstVal = current($result);
            $topSiteid = $firstVal['id'];
            /*--end*/
        }

        return intval($topSiteid);
    }
}