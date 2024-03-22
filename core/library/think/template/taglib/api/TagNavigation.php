<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2022/8/3
 * Time: 16:42
 */

namespace think\template\taglib\api;

use think\Db;
use think\Cache;
use app\common\logic\BuildhtmlLogic;

class TagNavigation extends Base
{
    private $arctypeAll = [];
    private $buildhtmlLogic = null;

    protected function _initialize(){
        parent::_initialize();
        $this->buildhtmlLogic = new BuildhtmlLogic;
        $this->arctypeAll = $this->buildhtmlLogic->get_arctype_all("id,current_channel,typename"); // 获取全部的栏目信息
    }
    public function getNavigation($position_id = null, $nav_id = null)
    {
        if (!empty($nav_id)) {
            //获取某菜单下的子菜单
            $res = $this->getSon($nav_id);
        } elseif (!empty($position_id)) {
            //获取某导航下所有菜单
            $res = $this->getAll($position_id);
        } else {
            return false;
        }

        return $res;
    }
    public function getAll($position_id = null)
    {
        $tid    = $this->tid;
        $args = [$position_id, $tid, self::$home_lang];
        $cacheKey = 'api-taglib-all-'.md5(__CLASS__.__FUNCTION__.json_encode($args));
        $result = cache($cacheKey);
        if (!empty($result)) {
            return $result;
        }
        // 排序
        $Order = 'sort_order asc,nav_id asc';
        $where       = [
            'position_id' => $position_id,
            'status'      => 1,
//            'type_id'    => ['gt',0]
        ];
        $arr1        = Db::name('nav_list')->where($where)->where('parent_id', 0)->order($Order)->cache(true, EYOUCMS_CACHE_TIME, "nav_list")->getAllWithIndex('nav_id');
        $parent_ids1 = get_arr_column($arr1, 'nav_id');
        $arr2        = Db::name('nav_list')->where($where)->where('parent_id', 'in', $parent_ids1)->order($Order)->cache(true, EYOUCMS_CACHE_TIME, "nav_list")->getAllWithIndex('nav_id');
        $parent_ids2 = get_arr_column($arr2, 'nav_id');
        $arr3        = Db::name('nav_list')->where($where)->where('parent_id', 'in', $parent_ids2)->order($Order)->cache(true, EYOUCMS_CACHE_TIME, "nav_list")->getAllWithIndex('nav_id');
        if (!empty($arr1)) {
            foreach ($arr1 as $key => $value) {
                if (!empty($value['type_id'])) $value = array_merge($value,$this->arctypeAll[$value['type_id']]);
                $value['nav_pic'] = get_absolute_url($value['nav_pic'],'url',true);
                foreach ($arr2 as $k => $v) {
                    if (!empty($v['type_id'])) $v = array_merge($v,$this->arctypeAll[$v['type_id']]);
                    $v['nav_pic'] = get_absolute_url($v['nav_pic'],'url',true);
                    foreach ($arr3 as $m => $n) {
                        if (!empty($n['type_id'])) $n = array_merge($n,$this->arctypeAll[$n['type_id']]);
                        $n['nav_pic'] = get_absolute_url($n['nav_pic'],'url',true);
                        if ($n['parent_id'] == $k) {
                            $v['children'][] = $n;
                        }
                    }
                    if ($v['parent_id'] == $key) {
                        $value['children'][] = $v;
                    }
                }
                $arr1[$key] = $value;
            }
        }
        cache($cacheKey, $arr1, null, 'nav_list');

        return $arr1;
    }

    public function getSon($nav_id = null)
    {
        $tid    = $this->tid;
        $args = [$nav_id, $tid, self::$home_lang];
        $cacheKey = 'api-taglib-son-'.md5(__CLASS__.__FUNCTION__.json_encode($args));
        $result = cache($cacheKey);
        if (!empty($result)) {
            return $result;
        }
        $where['status'] = 1;
        $where['type_id']    = ['gt',0];
        //先判断这个nav_id是一级菜单还是二级菜单
        $nav_info = Db::name('nav_list')->where('nav_id', $nav_id)->cache(true, EYOUCMS_CACHE_TIME, "nav_list")->find();
        //如果parent_id == topid 则是一级栏目,不等则为二级栏目
        if ($nav_info['parent_id'] == $nav_info['topid']) {
            $where['topid'] = $nav_id;
        } else {
            $where['parent_id_id'] = $nav_id;
        }
        $Order = 'sort_order asc,nav_id asc';
        $result = Db::name('nav_list')
            ->where($where)
            ->order($Order)
            ->cache(true, EYOUCMS_CACHE_TIME, "nav_list")
            ->getAllWithIndex('nav_id');
        $topArr = [];
        $sonArr = [];
        if (!empty($result)) {
            foreach ($result as $k => $v) {
                $v = array_merge($v,$this->arctypeAll[$v['type_id']]);
                $v['nav_pic'] = get_absolute_url($v['nav_pic'],'url',true);
                if ($nav_id == $v['parent_id'] ) {
                    $topArr[$k] = $v;
                } else {
                    $sonArr[$k] = $v;
                }
            }
            foreach ($topArr as $key => $val) {
                foreach ($sonArr as $k => $v) {
                    if ($v['parent_id'] == $val['nav_id']) {
                        $topArr[$key]['children'][] = $v;
                    }
                }
            }
        }
        cache($cacheKey, $topArr, null, 'nav_list');

        return $topArr;
    }
}