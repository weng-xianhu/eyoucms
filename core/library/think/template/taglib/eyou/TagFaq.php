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
 * 常见问题问答列表
 */
class TagFaq extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 常见问题问答列表
     */
    public function getFaq($group_id = '', $where = '', $orderby = '')
    {
        if (empty($group_id)) {
            echo '标签faq报错：缺少属性 group_id 。';
            return false;
        }

        $isCount = Db::name('faq_group')->where(['group_id'=>$group_id, 'lang'=>self::$home_lang])->cache(true, EYOUCMS_CACHE_TIME, 'faq_group')->count();
        if (empty($isCount)) {
            echo "标签faq报错：该问答ID(".$group_id.")不存在。";
            return false;
        }

        // 新逻辑
        if (empty($where)) {
            // 排序
            switch ($orderby) {
                case 'now':
                case 'new': // 兼容写法
                    $orderby = 'b.add_time desc';
                    break;
                    
                case 'id':
                    $orderby = 'b.asklist_id desc';
                    break;

                case 'sort_order':
                    $orderby = 'b.sort_order asc';
                    break;

                case 'rand':
                    $orderby = 'rand()';
                    break;
                
                default:
                    if (empty($orderby)) {
                        $orderby = 'b.sort_order asc, b.asklist_id desc';
                    }
                    break;
            }
            $where = [
                'a.group_id' => $group_id,
                'a.status'  => 1,
                'a.lang' => self::$home_lang,
            ];
            $result = Db::name("faq_group")->alias('a')
                ->field("b.*")
                ->join('__FAQ_ASKLIST__ b', 'a.group_id = b.group_id AND a.lang = b.lang', 'LEFT')
                ->where($where)
                ->orderRaw($orderby)
                ->select();
        } else {
            // 排序
            switch ($orderby) {
                case 'now':
                case 'new': // 兼容写法
                    $orderby = 'add_time desc';
                    break;
                    
                case 'id':
                    $orderby = 'asklist_id desc';
                    break;

                case 'sort_order':
                    $orderby = 'sort_order asc';
                    break;

                case 'rand':
                    $orderby = 'rand()';
                    break;
                
                default:
                    if (empty($orderby)) {
                        $orderby = 'sort_order asc, asklist_id desc';
                    }
                    break;
            }
            $result = Db::name("faq_asklist")->where($where)->orderRaw($orderby)->select();
        }

        foreach ($result as $key => $val) {
            $val['asklist_title'] = htmlspecialchars_decode($val['asklist_title']);
            $val['asklist_content'] = htmlspecialchars_decode($val['asklist_content']);
            $val['asklist_content'] = str_replace(PHP_EOL, '<br/>', $val['asklist_content']);
            $result[$key] = $val;
        }

        return $result;
    }
}