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
use think\Cache;
use app\home\logic\FieldLogic;
/**
 * 根据标签ID获取文章列表
 */
class TagTagList extends Base
{ 
    public $fieldLogic;
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->fieldLogic = new FieldLogic();
    }

    /**
     * 根据标签ID获取文章列表
     * @param int $tag_id 标签ID
     * @param string $limit 条数限制
     * @param string $orderby 排序方式
     * @param int $typeid 栏目ID（可选，用于筛选指定栏目下的文章）
     * @param int $child 是否包含子栏目（0=否，1=是，默认0）
     * @return array
     */
    public function getTagList($tag_id = 0, $limit = 10, $orderby = '', $typeid = 0, $child = 0)
    {
        $result = [];
        
        if (empty($tag_id)) {
            return [
                'data' => $result,
                'total' => 0,
                'tag_info' => null,
            ];
        }

        try {

        // 处理limit参数
        if (!stristr($limit, ',')) {
            $limit = "0, {$limit}";
        }
        $limitArr = explode(',', $limit);
        $offset = intval(trim($limitArr[0]));
        $length = intval(trim($limitArr[1]));

        // 获取标签信息
        $tagInfo = Db::name('tagindex')
            ->field('id, tag')
            ->where(['id' => $tag_id])
            ->find();

        if (empty($tagInfo)) {
            return [
                'data' => $result,
                'total' => 0,
                'tag_info' => null,
            ];
        }
        // 查询该标签下的文章ID列表
        $tagListQuery = Db::name('taglist')
            ->alias('tl')
            ->field('tl.aid')
            ->where(['tl.tid' => $tag_id]);

        // 关联文章表获取文章详情
        $prefix = config('database.prefix');
        
        // 处理排序参数
        if (empty($orderby)) {
            $orderby = 'a.aid desc';
        }
        
        // 先构建基础查询条件
        $where = [
            'tl.tid' => $tag_id,
            'a.status' => 1,
            'a.is_del' => 0,
            'a.arcrank' => ['>=', 0]
        ];
        
        // 只有在语言字段存在时才添加语言条件
        if (!empty(self::$home_lang)) {
            $where['a.lang'] = self::$home_lang;
        }
        
        // 如果指定了typeid，则添加栏目筛选条件
        if (!empty($typeid)) {
            if (!empty($child)) {
                // 包含子栏目：获取所有子栏目ID
                $typeids = $this->getChildTypeids($typeid);
                $where['a.typeid'] = ['IN', $typeids];
            } else {
                // 仅当前栏目
                $where['a.typeid'] = $typeid;
            }
        }
        
        $articleList = Db::name('archives')
            ->alias('a')
            ->field('a.*')
            ->join($prefix.'taglist tl', 'a.aid = tl.aid', 'INNER')
            ->where($where)
            ->order($orderby)
            ->limit($offset, $length)
            ->select();
            
        // 调试信息
        if (empty($articleList)) {
            // 检查是否有标签关联数据
            $tagCount = Db::name('taglist')->where(['tid' => $tag_id])->count();
            error_log("Tag ID: {$tag_id}, Taglist count: {$tagCount}, Lang: " . self::$home_lang);
        }

        if (!empty($articleList)) {
            // 批量获取文章ID
            $aids = array_column($articleList, 'aid');
            
            // 获取文章自定义字段数据
            $customFieldData = [];
            $customFieldFormatData = [];  // 格式化后的自定义字段数据
            if (!empty($aids)) {
                // 获取所有文章的栏目ID
                $typeids = array_unique(array_column($articleList, 'typeid'));
                
                // 获取栏目对应的模型ID和表名
                $arctypeList = Db::name('arctype')
                    ->where('id', 'in', $typeids)
                    ->field('id, current_channel')
                    ->select();
                
                $channelTypeMap = [];
                foreach ($arctypeList as $arctype) {
                    $channelTypeMap[$arctype['id']] = $arctype['current_channel'];
                }
                
                // 根据模型ID获取对应的表名和自定义字段
                $channelList = Db::name('channeltype')->field('id, table, ifsystem')->select();
                $channelTableMap = [];
                $channelSystemMap = [];
                foreach ($channelList as $channel) {
                    $channelTableMap[$channel['id']] = $channel['table'];
                    $channelSystemMap[$channel['id']] = $channel['ifsystem'];
                }
                
                // 按模型分组查询自定义字段
                $fieldLogic = new \app\home\logic\FieldLogic();
                foreach ($articleList as $article) {
                    $channelId = isset($channelTypeMap[$article['typeid']]) ? $channelTypeMap[$article['typeid']] : 0;
                    $tableName = isset($channelTableMap[$channelId]) ? $channelTableMap[$channelId] . '_content' : '';
                    
                    if (!empty($tableName)) {
                        try {
                            $customData = Db::name($tableName)
                                ->where('aid', $article['aid'])
                                ->find();
                            if (!empty($customData)) {
                                // 保存原始数据
                                unset($customData['id'], $customData['aid'], $customData['add_time'], $customData['update_time']);
                                $customFieldData[$article['aid']] = $customData;
                                
                                // 格式化自定义字段数据
                                $detailExt = $fieldLogic->getChannelFieldList($customData, $channelId);
                                
                                // 处理自定义模型的content字段
                                if (empty($channelSystemMap[$channelId])) { // 自定义模型
                                    if (!isset($detailExt['content'])) {
                                        $contentField = Db::name('channelfield')->where([
                                                'channel_id'    => $channelId,
                                                'dtype'         => 'htmltext',
                                            ])->value('name');
                                        $detailExt['content'] = !empty($detailExt[$contentField]) ? $detailExt[$contentField] : '';
                                    }
                                }
                                
                                // 处理手机端内容
                                if (isset($detailExt['content_ey_m']) && !empty($detailExt['content_ey_m'])) {
                                    $detailExt['content'] = $detailExt['content_ey_m'];
                                    unset($detailExt['content_ey_m']);
                                }
                                
                                // 获取自定义字段的详细信息
                                $customFieldFormat = [];
                                if (!empty($detailExt)) {
                                    $field = 'name, title, dtype';
                                    $customFieldInfo = Db::name('channelfield')->field($field)->where([
                                        'name' => ['IN', array_keys($detailExt)],
                                        'channel_id' => $channelId,
                                        'ifeditable' => 1
                                    ])->getAllWithIndex('name');
                                    
                                    if (!empty($customFieldInfo)) {
                                        foreach ($customFieldInfo as $key => $value) {
                                            if ('img' == $value['dtype']) {
                                                $customFieldFormat[$key] = [
                                                    'name' => $value['name'],
                                                    'title' => $value['title'],
                                                    'dtype' => $value['dtype'],
                                                    'value' => $this->get_default_pic($detailExt[$key])
                                                ];
                                            } else if ('media' == $value['dtype']) {
                                                $customFieldFormat[$key] = [
                                                    'name' => $value['name'],
                                                    'title' => $value['title'],
                                                    'dtype' => $value['dtype'],
                                                    'value' => $this->get_default_pic($detailExt[$key])
                                                ];
                                            } else if ('imgs' == $value['dtype']) {
                                                $imgsValue = $detailExt[$key];
                                                if (is_array($imgsValue)) {
                                                    foreach ($imgsValue as $kk => $vv) {
                                                        if (isset($vv['image_url'])) {
                                                            $imgsValue[$kk]['image_url'] = $this->get_default_pic($vv['image_url']);
                                                        }
                                                    }
                                                }
                                                $customFieldFormat[$key] = [
                                                    'name' => $value['name'],
                                                    'title' => $value['title'],
                                                    'dtype' => $value['dtype'],
                                                    'value' => $imgsValue
                                                ];
                                            } else {
                                                $customFieldFormat[$key] = [
                                                    'name' => $value['name'],
                                                    'title' => $value['title'],
                                                    'dtype' => $value['dtype'],
                                                    'value' => $detailExt[$key]
                                                ];
                                            }
                                        }
                                    }
                                }
                                $customFieldFormatData[$article['aid']] = array_values($customFieldFormat);
                            }
                        } catch (\Exception $e) {
                            // 表不存在或查询失败，继续处理下一个
                        }
                    }
                }
            }
            
            foreach ($articleList as $key => $val) {
                // 合并自定义字段数据
                if (isset($customFieldData[$val['aid']])) {
                    $val = array_merge($val, $customFieldData[$val['aid']]);
                }
                
                // 添加格式化后的自定义字段数组
                if (isset($customFieldFormatData[$val['aid']])) {
                    $val['customField'] = $customFieldFormatData[$val['aid']];
                } else {
                    $val['customField'] = false;
                }
                
                // 处理文章标题和描述
                $val['title'] = htmlspecialchars_decode($val['title']);
                $val['description'] = htmlspecialchars_decode($val['description']);
                
                // 处理封面图
                $val['litpic'] = $this->get_default_pic($val['litpic']);
                
                // 时间格式化
                $val['add_time_format'] = $this->time_format($val['add_time']);
                $val['update_time_format'] = $this->time_format($val['update_time']);
                
                // 获取栏目信息
                $arctypeInfo = Db::name('arctype')
                    ->field('id, typename, litpic')
                    ->where(['id' => $val['typeid']])
                    ->find();
                if (!empty($arctypeInfo)) {
                    $val['arctype'] = [
                        'id' => $arctypeInfo['id'],
                        'typename' => htmlspecialchars_decode($arctypeInfo['typename']),
                        'litpic' => $this->get_default_pic($arctypeInfo['litpic']),
                    ];
                } else {
                    $val['arctype'] = null;
                }
                
                $result[] = $val;
            }
        }

        // 统计总数 - 使用相同的查询条件
        $totalWhere = [
            'tl.tid' => $tag_id,
            'a.status' => 1,
            'a.is_del' => 0,
            'a.arcrank' => ['>=', 0],
        ];
        
        if (!empty(self::$home_lang)) {
            $totalWhere['a.lang'] = self::$home_lang;
        }
        
        // 如果指定了typeid，统计时也要加上栏目筛选
        if (!empty($typeid)) {
            if (!empty($child)) {
                $typeids = $this->getChildTypeids($typeid);
                $totalWhere['a.typeid'] = ['IN', $typeids];
            } else {
                $totalWhere['a.typeid'] = $typeid;
            }
        }
        
        $total = Db::name('archives')
            ->alias('a')
            ->join($prefix.'taglist tl', 'a.aid = tl.aid', 'INNER')
            ->where($totalWhere)
            ->count();

            return [
                'data' => $result,
                'total' => $total,
                'tag_info' => [
                    'id' => $tagInfo['id'],
                    'tag_name' => htmlspecialchars_decode($tagInfo['tag']),
                    'tag_desc' => '',
                ],
            ];
            
        } catch (\Exception $e) {
            // 如果表不存在或字段不存在，返回空数据
            return [
                'data' => [],
                'total' => 0,
                'tag_info' => null,
            ];
        }
    }
    
    /**
     * 递归获取指定栏目及其所有子栏目的ID
     * @param int $typeid 栏目ID
     * @return array 栏目ID数组
     */
    private function getChildTypeids($typeid)
    {
        $typeids = [$typeid]; // 包含自己
        
        // 递归查询子栏目
        $children = Db::name('arctype')
            ->where('parent_id', $typeid)
            ->column('id');
        
        if (!empty($children)) {
            foreach ($children as $childId) {
                $typeids = array_merge($typeids, $this->getChildTypeids($childId));
            }
        }
        
        return array_unique($typeids);
    }
}
