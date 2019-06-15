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

namespace app\common\model;

use think\Model;

/**
 * 文章Tag标签
 */
class Taglist extends Model
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
    public function getInfo($tid, $field = '*')
    {
        $result = db('Taglist')->field($field)->where('tid', $tid)->find();

        return $result;
    }

    /**
     * 获取单篇文章的标签
     * @author wengxianhu by 2017-7-26
     */
    public function getListByAid($aid = '', $typeid = 0, $field = 'tag')
    {
        $str = '';
        $result = db('Taglist')->field($field)
            ->where(array('aid'=>$aid, 'typeid'=>$typeid))
            ->order('aid asc')
            ->select();
        if ($result) {
            $tag_arr = get_arr_column($result, 'tag');
            $str = implode(',', $tag_arr);
        }

        return $str;
    }

    /**
     * 获取多篇文章的标签
     * @author wengxianhu by 2017-7-26
     */
    public function getListByAids($aids = array(), $field = '*')
    {
        $data = array();
        $result = db('Taglist')->field($field)
            ->where(array('aid'=>array('IN', $aids)))
            ->order('aid asc')
            ->select();
        if ($result) {
            foreach ($result as $key => $val) {
                if (!isset($data[$val['aid']])) $data[$val['aid']] = array();
                array_push($data[$val['aid']], $val);
            }
        }

        return $data;
    }

    /**
     * 写入文章标签
     */
    public function savetags($aid = 0, $typeid = 0, $tags = '')
    {
        if (intval($aid) > 0 && intval($typeid) > 0 && !empty($tags)) {
            // --处理TAG标签
            $tags = func_preg_replace(array('，'), ',', $tags);
            $tags_arr = explode(',', $tags);
            // 去除左右空格
            foreach ($tags_arr as $key => $val) {
                $tags_arr[$key] = trim($val);
            }
            // 移除重复值
            $tags_arr = array_unique($tags_arr);
            // 获取存在的标签
            $tagindexlist = M('tagindex')->field('id,tag')->where(array('tag'=>array('in', $tags_arr)))->select();
            foreach ($tagindexlist as $key => $val) {
                $tagmd5 = md5($val['tag']);
                $tagindexlist[$tagmd5] = $val;
                unset($tagindexlist[$key]);
            }

            // 删除标签
            M('taglist')->where(array('aid'=>$aid))->delete();

            // 组装数据写入数据表
            $time = getTime();
            $add_data = array();
            $now_data = array();
            foreach ($tags_arr as $key => $val) {
                $tagmd5 = md5($val);
                if (isset($tagindexlist[$tagmd5]) && !empty($tagindexlist[$tagmd5])) {
                    $tid = $tagindexlist[$tagmd5]['id'];
                } else {
                    $now_data = array(
                        'tag'   => $val,
                        'typeid'    => $typeid,
                        'add_time'  => $time,
                    );
                    $tid = M('tagindex')->insertGetId($now_data);
                }
                $add_data[] = array(
                    'tid'   => $tid,
                    'aid'   => $aid,
                    'typeid'   => $typeid,
                    'tag'   => $val,
                    'add_time'  => $time,
                );
            }
            // 保存标签
            M('taglist')->insertAll($add_data);
        }
    }

    /**
     * 删除文章标签
     */
    public function delByAids($aids = array())
    {
        if (!empty($aids)) {
            M('taglist')->where(array('aid'=>array('IN', $aids)))->delete();
        }
    }
}