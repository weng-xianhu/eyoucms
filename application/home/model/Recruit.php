<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 陈风任 <491085389@qq.com>
 * Date: 2019-1-7
 */

namespace app\home\model;

use think\Model;
use think\Page;
use think\Db;

/**
 * 文章
 */
class Recruit extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 获取单条记录
     * @author wengxianhu by 2017-7-26
     */
    public function getInfo($aid, $field = '', $isshowbody = true)
    {
        $data = array();
        if (!empty($field)) {
            $field_arr = explode(',', $field);
            foreach ($field_arr as $key => $val) {
                $val = trim($val);
                if (preg_match('/^([a-z]+)\./i', $val) == 0) {
                    array_push($data, 'a.'.$val);
                } else {
                    array_push($data, $val);
                }
            }
            $field = implode(',', $data);
        }

        $result = array();
        if ($isshowbody) {
            $field = !empty($field) ? $field : 'b.*, a.*';
            $result = db('archives')->field($field)
                ->alias('a')
                ->join('__RECRUIT_CONTENT__ b', 'b.aid = a.aid', 'LEFT')
                ->find($aid);
        } else {
            $field = !empty($field) ? $field : 'a.*';
            $result = db('archives')->field($field)
                ->alias('a')
                ->find($aid);
        }

        // 文章TAG标签
        if (!empty($result)) {
            $typeid = isset($result['typeid']) ? $result['typeid'] : 0;
            $tags = model('Taglist')->getListByAid($aid, $typeid);
            $result['tags'] = $tags;
        }

        return $result;
    }
}