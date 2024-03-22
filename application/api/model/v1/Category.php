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

namespace app\api\model\v1;

use think\Db;
use think\Cache;

/**
 * 小程序分类模板
 */
class Category extends Base
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 获取所有分类
     * string $index_key 数组键名
     * @return mixed
     */
    public static function getProductCategory()
    {   
        $data = [
            'list'  => [],
            'arclist'  => [],
        ];

        // 查询分类数据
        $where = [
            'current_channel' => 2,
            'status' => 1,
            'is_hidden'   => 0,
            'is_del' => 0, // 回收站功能
            'lang' => parent::$lang,
        ];
        $order = [
            'sort_order' => 'asc',
            'id'  => 'asc'
        ];
        $ArctypeData = Db::name('arctype')->where($where)->order($order)->select();

        // 商品信息列表
        $ArchivesData = [];
        if (!empty($ArctypeData[0]['id'])) $ArchivesData = self::GetProductData($ArctypeData[0]['id']);

        // 返回结果
        $data['list'] = $ArctypeData;
        $data['arclist'] = $ArchivesData;

        return $data;
    }

    // 查询商品信息
    public static function GetProductData($InitialID = null, $limit = 0)
    {
        // 查询条件
        $where = [
            'typeid'  => ['IN', $InitialID],
            'arcrank' => ['gt', -1],
            'status'  => 1,
            'is_del'  => 0,
            'lang'    => parent::$lang,
        ];

        // 数据排序
        $order = [
            'sort_order' => 'asc',
            'aid'  => 'asc'
        ];

        // 分页处理
        $limit ='0, 20';

        // 查询数据
        $ArchivesData = Db::name('archives')->where($where)->order($order)->limit($limit)->select();
        if (!empty($ArchivesData)) {
            $diyminiproCmsBaseModel = model('v1.Base');
            foreach ($ArchivesData as $key => $value) {
                $ArchivesData[$key]['litpic'] = $diyminiproCmsBaseModel->get_default_pic($value['litpic'], true); // 默认封面图
            }
        } else {
            $ArchivesData = [];
        }
        
        // 返回结果
        return $ArchivesData;
    }

    /**
     * 所有分类
     * @param  string $showAllTxt 显示“全部”字样
     * @return mixed
     */
    public static function getALL($type = '', $showAllTxt = 'off')
    {
        $cacheKey = 'api-'.md5(__CLASS__.__FUNCTION__.json_encode(func_get_args())."-".parent::$appId);
        $returnData = Cache::get($cacheKey);
        if (empty($returnData)) {
            $data = Db::name('arctype')->field('id,current_channel,parent_id,typename,grade,litpic,seo_title,seo_keywords,seo_description,sort_order, 0 as sub_level')->where([
                'is_hidden'   => 0,
                'status'  => 1,
                'is_del'  => 0, // 回收站功能
                'lang'  => parent::$lang,
            ])->order(['sort_order' => 'asc', 'id' => 'asc'])->select();
            $all = !empty($data) ? $data : [];
            $tree = [];
            $diyminiproCmsBaseModel = model('v1.Base');
            foreach ($all as $_k => $first) {

                // 过滤插件产生的栏目
                if (!empty($first['weapp_code'])) continue;
                
                $all[$_k]['typename'] = $first['typename'] = htmlspecialchars_decode($first['typename']);
                $all[$_k]['litpic'] = $first['litpic'] = $diyminiproCmsBaseModel->get_default_pic($first['litpic'], true);
                if ($first['parent_id'] != 0) continue;
                $twoTree = [];
                foreach ($all as $_k2 => $two) {
                    $all[$_k2]['typename'] = $two['typename'] = htmlspecialchars_decode($two['typename']);
                    $all[$_k2]['litpic'] = $two['litpic'] = $diyminiproCmsBaseModel->get_default_pic($two['litpic'], true);
                    if ($two['parent_id'] != $first['id']) continue;
                    $threeTree = [];
                    foreach ($all as $_k3 => $three) {
                        $all[$_k3]['typename'] = $three['typename'] = htmlspecialchars_decode($three['typename']);
                        $all[$_k3]['litpic'] = $three['litpic'] = $diyminiproCmsBaseModel->get_default_pic($three['litpic'], true);
                        $three['parent_id'] == $two['id'] && $threeTree[$three['id']] = $three;
                    }
                    if (!empty($threeTree)) {
                        /*没有子栏目时，同级栏目是否有“全部”字样*/
                        $currentData = current($threeTree);
                        $two['sub_level'] = intval($currentData['sub_level']) + 1;
                        if ('notSub' == $showAllTxt) {
                            $tmp1 = $two;
                            $tmp1['typename'] = '全部';
                            $tmp1['sub_level'] = 0;
                            $threeTree = array_merge([$tmp1], $threeTree);
                        }
                        /*end*/
                        $two['children'] = $threeTree;
                    }
                    $twoTree[$two['id']] = $two;
                }
                if (!empty($twoTree)) {
                    array_multisort(get_arr_column($twoTree, 'sort_order'), SORT_ASC, $twoTree);
                    /*没有子栏目时，同级栏目是否有“全部”字样*/
                    $currentData = current($twoTree);
                    $first['sub_level'] = intval($currentData['sub_level']) + 1;
                    if ('notSub' == $showAllTxt) {
                        $tmp2 = $first;
                        $tmp2['typename'] = '全部';
                        $tmp2['sub_level'] = 0;
                        $twoTree = array_merge([$tmp2], $twoTree);
                    }
                    /*end*/
                    $first['children'] = $twoTree;
                }
                $tree[$first['id']] = $first;
            }
            $returnData = [
                'all'   => $all,
                'tree'   => $tree,
            ];
            Cache::tag('arctype')->set($cacheKey, $returnData);
        }

        if (!empty($type)) {
            isset($returnData[$type]) && $returnData = $returnData[$type];
        }

        return $returnData;
    }

    /**
     * 获取所有分类
     * string $index_key 数组键名
     * @return mixed
     */
    public static function getCacheAll($index_key = '')
    {
        $result = self::getALL('all');
        if (!empty($index_key)) {
            $result = convert_arr_key($result, $index_key);
        }
        return $result;
    }

    /**
     * 获取所有分类(只包含id和名称)
     * string $index_key 数组键名
     * @return mixed
     */
    public static function getCacheAllName()
    {
        $cacheKey = 'api-'.md5(__CLASS__.__FUNCTION__.parent::$appId);
        $result = Cache::get($cacheKey);
        if (empty($result)) {
            $arctypeAll = self::getALL('all');
            $result = [];
            foreach ($arctypeAll as $key => $val) {
                $result[$val['id']] = [
                    'id'  => $val['id'],
                    'typename'  => $val['typename'],
                ];  
            }
            Cache::tag('arctype')->set($cacheKey, $result);
        }
        
        return $result;
    }

    /**
     * 获取单个分类
     * string $index_key 数组键名
     * @return mixed
     */
    public static function getCacheInfo($index_value = '', $index_key = 'id')
    {
        $result = [];
        if (!empty($index_key)) {
            $result = self::getALL('all');
            $result = convert_arr_key($result, $index_key);
            $result = empty($result[$index_value]) ? [] : $result[$index_value];
        }
        return $result;
    }

    /**
     * 获取树状结构分类
     * @param  string  $typeid  栏目ID
     * @param  string $showAllTxt 显示“全部”字样
     * @return [type]           [description]
     */
    public static function getCacheTree($typeid = '', $showAllTxt = 'off')
    {
        $treeData = self::getALL('tree', $showAllTxt);
        if (empty($typeid)) {
            $result = $treeData;
        } else {
            $cacheKey = 'api-'.md5(__CLASS__.__FUNCTION__.json_encode(func_get_args()));
            $result = Cache::get($cacheKey);
            if (empty($result)) {
                if (isset($treeData[$typeid])) {
                    /*栏目显示“全部”*/
                    if ('all' == $showAllTxt) {
                        $allchildren = $treeData[$typeid];
                        $allchildren['typename'] = '全部';
                        $allchildren['children'] = [];
                        $children = $treeData[$typeid]['children'];
                        $children = array_merge([$allchildren], $children);
                        $treeData[$typeid]['children'] = $children;
                    }
                    /*end*/
                    $result = [$treeData[$typeid]];
                } else {
                    $result = [];
                    $arctypeInfo = Db::name('arctype')->field('id,parent_id,grade')->find($typeid);
                    if (1 == $arctypeInfo['grade'] && !empty($treeData[$arctypeInfo['parent_id']]['children'])) {
                        foreach ($treeData[$arctypeInfo['parent_id']]['children'] as $key => $val) {
                            if ($typeid == $val['id']) {
                                /*栏目显示“全部”*/
                                if ('all' == $showAllTxt) {
                                    $allchildren = $val;
                                    $allchildren['typename'] = '全部';
                                    $allchildren['children'] = [];
                                    $children = !empty($val['children']) ? $val['children'] : [];
                                    $children = array_merge([$allchildren], $children);
                                    $val['children'] = $children;
                                }
                                /*end*/
                                $result[] = $val;
                            }
                        }
                    }
                }
                Cache::tag('arctype')->set($cacheKey, $result);
            }
        }

        return $result;
    }

    /**
     * 获取指定分类下的所有子分类id
     * @param $parent_id
     * @param array $all
     * @return array
     */
    public static function getSubCategoryId($parent_id, $all = [])
    {
        $arrIds = [$parent_id];
        empty($all) && $all = self::getCacheAll();
        foreach ($all as $key => $item) {
            if ($item['parent_id'] == $parent_id) {
                unset($all[$key]);
                $subIds = self::getSubCategoryId($item['id'], $all);
                !empty($subIds) && $arrIds = array_merge($arrIds, $subIds);
            }
        }
        return $arrIds;
    }

    /**
     * 指定的分类下是否存在子分类
     * @param $parentId
     * @return bool
     */
    protected static function hasSubCategory($parentId)
    {
        $all = self::getCacheAll();
        foreach ($all as $item) {
            if ($item['parent_id'] == $parentId) {
                return true;
            }
        }
        return false;
    }
}