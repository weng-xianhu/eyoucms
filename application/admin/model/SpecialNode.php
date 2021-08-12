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

namespace app\admin\model;

use think\Db;
use think\Model;

/**
 * 专题节点
 */
class SpecialNode extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }
    
    public function getList($aid = 0)
    {
        $result = [];
        if (!empty($aid)) {
            $map = [
                'aid'   => $aid,
                'status'    => 1,
                'is_del'    => 0,
                'lang'      => get_admin_lang(),
            ];
            $result = Db::name('special_node')->where($map)->order('sort_order asc, node_id desc')->select();
        }

        return $result;
    }    

    public function saveNode($aid = 0, $specialNode = [], $opt = 'add')
    {
        if ('add' == $opt) {
            $this->insertNode($aid, $specialNode);
        } else if ('edit' == $opt) {
            $this->updateNode($aid, $specialNode);
        }
    }

    /**
     * 新增节点
     * @param  integer $aid         [description]
     * @param  array   $specialNode [description]
     * @return [type]               [description]
     */
    private function insertNode($aid = 0, $specialNode = [])
    {
        if (!empty($specialNode) && !empty($aid)) {
            $addData = [];
            $admin_lang = get_admin_lang();

            // 处理节点名称重复，确保节点名称的唯一性
            $specialNodeTitleArr = $specialNode['title'];
            foreach ($specialNode['title'] as $_k1 => $_v1) {
                $num = 0;
                foreach ($specialNodeTitleArr as $_k2 => $_v2) {
                    if ($_v1 == $_v2) {
                        $num++;
                    }
                }
                (1 < $num) && $specialNode['title'][$_k1] = $_v1.'_'.$num;
                unset($specialNodeTitleArr[$_k1]);
            }

            // 处理节点标识重复，确保节点标识的唯一性
            $specialNodeCodeArr = $specialNode['code'];
            foreach ($specialNode['code'] as $_k1 => $_v1) {
                $num = 0;
                foreach ($specialNodeCodeArr as $_k2 => $_v2) {
                    if ($_v1 == $_v2) {
                        $num++;
                    }
                }
                (1 < $num) && $specialNode['code'][$_k1] = $_v1.'_'.$num;
                unset($specialNodeCodeArr[$_k1]);
            }
            
            foreach ($specialNode['itemid'] as $key => $itemid) {
                if (!empty($itemid)) {
                    // 关键词
                    $keywords = !empty($specialNode['keywords'][$key]) ? trim($specialNode['keywords'][$key]) : '';
                    $keywords = str_replace('，', ',', $keywords);
                    $keywordsArr = explode(',', $keywords);
                    foreach ($keywordsArr as $_k1 => $_v1) {
                        $_v1 = trim($_v1);
                        if (!empty($_v1)) {
                            $keywordsArr[$_k1] = $_v1;
                        } else {
                            unset($keywordsArr[$_k1]);
                        }
                    }
                    $keywords = implode(',', $keywordsArr);

                    // 节点文档列表
                    $aidlist = !empty($specialNode['aidlist'][$key]) ? trim($specialNode['aidlist'][$key]) : '';
                    $aidlist = str_replace('，', ',', $aidlist);
                    $aidlistArr = explode(',', $aidlist);
                    foreach ($aidlistArr as $_k1 => $_v1) {
                        $_v1 = trim($_v1);
                        if (!empty($_v1)) {
                            $aidlistArr[$_k1] = $_v1;
                        } else {
                            unset($aidlistArr[$_k1]);
                        }
                    }
                    $aidlist = implode(',', $aidlistArr);

                    // 排序号
                    $sort_order = 100 + $key;

                    $addData[] = [
                        'aid'   => $aid,
                        'title'  => !empty($specialNode['title'][$key]) ? trim($specialNode['title'][$key]) : '默认节点',
                        'code'  => !empty($specialNode['code'][$key]) ? trim($specialNode['code'][$key]) : 'default',
                        'isauto'    => !empty($specialNode['isauto'][$itemid]) ? intval($specialNode['isauto'][$itemid]) : 0,
                        'keywords'  => $keywords,
                        'typeid'    => !empty($specialNode['typeid'][$key]) ? trim($specialNode['typeid'][$key]) : 0,
                        'aidlist'   => !empty($aidlist) ? $aidlist : '',
                        'row'    => !empty($specialNode['row'][$key]) ? intval($specialNode['row'][$key]) : 10,
                        'status'    => 1,
                        'is_del'    => 0,
                        'sort_order'    => $sort_order,
                        'lang'  => $admin_lang,
                        'add_time'  => getTime(),
                        'update_time'  => getTime(),
                    ];
                }
            }

            if (!empty($addData)) {
                $this->saveAll($addData);
            }
        }
    }

    /**
     * 编辑节点
     * @param  integer $aid         [description]
     * @param  array   $specialNode [description]
     * @return [type]               [description]
     */
    private function updateNode($aid = 0, $specialNode = [])
    {
        if (!empty($specialNode) && !empty($aid)) {
            $addData = $updateData = $delData = [];
            $admin_lang = get_admin_lang();

            // 处理节点名称重复，确保节点名称的唯一性
            $specialNodeTitleArr = $specialNode['title'];
            foreach ($specialNode['title'] as $_k1 => $_v1) {
                $num = 0;
                foreach ($specialNodeTitleArr as $_k2 => $_v2) {
                    if ($_v1 == $_v2) {
                        $num++;
                    }
                }
                (1 < $num) && $specialNode['title'][$_k1] = $_v1.'_'.$num;
                unset($specialNodeTitleArr[$_k1]);
            }

            // 处理节点标识重复，确保节点标识的唯一性
            $specialNodeCodeArr = $specialNode['code'];
            foreach ($specialNode['code'] as $_k1 => $_v1) {
                $num = 0;
                foreach ($specialNodeCodeArr as $_k2 => $_v2) {
                    if ($_v1 == $_v2) {
                        $num++;
                    }
                }
                (1 < $num) && $specialNode['code'][$_k1] = $_v1.'_'.$num;
                unset($specialNodeCodeArr[$_k1]);
            }

            foreach ($specialNode['itemid'] as $key => $itemid) {
                if (!empty($itemid)) {

                    // 关键词
                    $keywords = !empty($specialNode['keywords'][$key]) ? trim($specialNode['keywords'][$key]) : '';
                    $keywords = str_replace('，', ',', $keywords);
                    $keywordsArr = explode(',', $keywords);
                    foreach ($keywordsArr as $_k1 => $_v1) {
                        $_v1 = trim($_v1);
                        if (!empty($_v1)) {
                            $keywordsArr[$_k1] = $_v1;
                        } else {
                            unset($keywordsArr[$_k1]);
                        }
                    }
                    $keywords = implode(',', $keywordsArr);

                    // 节点文档列表
                    $aidlist = !empty($specialNode['aidlist'][$key]) ? trim($specialNode['aidlist'][$key]) : '';
                    $aidlist = str_replace('，', ',', $aidlist);
                    $aidlistArr = explode(',', $aidlist);
                    foreach ($aidlistArr as $_k1 => $_v1) {
                        $_v1 = trim($_v1);
                        if (!empty($_v1)) {
                            $aidlistArr[$_k1] = $_v1;
                        } else {
                            unset($aidlistArr[$_k1]);
                        }
                    }
                    $aidlist = implode(',', $aidlistArr);

                    // 排序号
                    $sort_order = 100 + $key;

                    // 新增或者更新
                    $node_id = !empty($specialNode['node_id'][$key]) ? intval($specialNode['node_id'][$key]) : 0;
                    if (empty($node_id)) {
                        $addData[] = [
                            'aid'   => $aid,
                            'title'  => !empty($specialNode['title'][$key]) ? trim($specialNode['title'][$key]) : '默认节点',
                            'code'  => !empty($specialNode['code'][$key]) ? trim($specialNode['code'][$key]) : 'default',
                            'isauto'    => !empty($specialNode['isauto'][$itemid]) ? intval($specialNode['isauto'][$itemid]) : 0,
                            'keywords'  => $keywords,
                            'typeid'    => !empty($specialNode['typeid'][$key]) ? trim($specialNode['typeid'][$key]) : 0,
                            'aidlist'   => !empty($aidlist) ? $aidlist : '',
                            'row'    => !empty($specialNode['row'][$key]) ? intval($specialNode['row'][$key]) : 10,
                            'status'    => 1,
                            'is_del'    => 0,
                            'sort_order'    => $sort_order,
                            'lang'  => $admin_lang,
                            'add_time'  => getTime(),
                            'update_time'  => getTime(),
                        ];
                    } else {
                        $updateData[] = [
                            'node_id'   => $node_id,
                            'title'  => !empty($specialNode['title'][$key]) ? trim($specialNode['title'][$key]) : '默认节点',
                            'code'  => !empty($specialNode['code'][$key]) ? trim($specialNode['code'][$key]) : 'default',
                            'isauto'    => !empty($specialNode['isauto'][$itemid]) ? intval($specialNode['isauto'][$itemid]) : 0,
                            'keywords'  => $keywords,
                            'typeid'    => !empty($specialNode['typeid'][$key]) ? trim($specialNode['typeid'][$key]) : 0,
                            'aidlist'   => !empty($aidlist) ? $aidlist : '',
                            'row'    => !empty($specialNode['row'][$key]) ? intval($specialNode['row'][$key]) : 10,
                            'sort_order'    => $sort_order,
                            'update_time'  => getTime(),
                        ];
                        array_push($delData, $node_id);
                    }
                }
            }

            if (!empty($delData)) {
                $this->where(['aid'=>$aid, 'node_id'=>['NOT IN', $delData]])->delete();
            }
            if (!empty($updateData)) {
                $this->saveAll($updateData);
            }
            if (!empty($addData)) {
                $this->saveAll($addData);
            }
        }
    }
}