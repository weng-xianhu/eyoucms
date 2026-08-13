<?php

namespace think\template\taglib\api;

use think\Db;

/**
 * 文章标签标签
 */
class TagArticleTags extends Base
{
    public $aid = 0;

    /**
     * 根据文章ID获取关联标签
     */
   public function getArticleTags($aid = '', $limit = '50', $orderby = 'sort_order asc, id asc')
{
    $aid = !empty($aid) ? intval($aid) : $this->aid;
    
    if (empty($aid)) {
        return [
            'data' => [],
            'total' => 0,
            'name' => 12,
            'aid' => $aid
        ];
    }

        // 直接查询taglist表，获取tid和tag（无需关联tagindex）
        $tagList = Db::name('taglist')
            ->field('tid as id, tag') // tid别名id保持返回结构一致
            ->where([
                'aid' => $aid,
            ])
            ->limit($limit)
            ->select();
        
      

        $result = [];
        if (!empty($tagList)) {
            foreach ($tagList as $tag) {
                $result[] = [
                    'id' => $tag['id'], // 对应tid
                    'tag' => htmlspecialchars_decode($tag['tag']) ? htmlspecialchars_decode($tag['tag']) :  '',
                ];
            }
        }

        return [
            'data' => $result,
            'name' => 12,
            'total' => count($result),
            'aid' => $aid
        ];

   
}
}
