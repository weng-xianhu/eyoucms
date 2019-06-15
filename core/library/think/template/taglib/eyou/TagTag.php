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


/**
 * 标签
 */
class TagTag extends Base
{
    public $aid = 0;

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->aid = I('param.aid/d', 0);
    }

    /**
     * 获取标签
     * @author wengxianhu by 2018-4-20
     */
    public function getTag($getall = 0, $typeid = '', $aid = 0, $row = 30, $sort = 'new')
    {
        $aid = !empty($aid) ? $aid : $this->aid;
        $getall = intval($getall);
        $result = false;
        $condition = array();

        if ($getall == 0 && $aid > 0) {
            $condition['aid'] = array('eq', $aid);
            $result = db('taglist')
                ->field('*, tid AS tagid')
                ->where($condition)
                ->where('lang', $this->home_lang)
                ->limit($row)
                ->select();

        } else {
            /*多语言*/
            if (!empty($typeid)) {
                $typeid = model('LanguageAttr')->getBindValue($typeid, 'arctype');
            }
            /*--end*/
            
            if (!empty($typeid)) {
                $condition['typeid'] = array('in', $typeid);
            }
            if($sort == 'rand') $orderby = 'rand() ';
            else if($sort == 'week') $orderby=' weekcc DESC ';
            else if($sort == 'month') $orderby=' monthcc DESC ';
            else if($sort == 'hot') $orderby=' count DESC ';
            else if($sort == 'total') $orderby=' total DESC ';
            else $orderby = 'add_time DESC  ';

            $result = db('tagindex')
                ->field('*, id AS tagid')
                ->where($condition)
                ->where('lang', $this->home_lang)
                ->orderRaw($orderby)
                ->limit($row)
                ->select();
        }

        foreach ($result as $key => $val) {
            $val['link'] = url('home/Tags/lists', array('tagid'=>$val['tagid']));
            $result[$key] = $val;
        }

        return $result;
    }
}