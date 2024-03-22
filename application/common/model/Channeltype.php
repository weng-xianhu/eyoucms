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

use think\Db;
use think\Model;

/**
 * 模型
 */
class Channeltype extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 获取单条记录
     * @author 小虎哥 by 2018-4-16
     */
    public function getInfo($id)
    {
        $result = Db::name('channeltype')->field('*')->cache(true,EYOUCMS_CACHE_TIME,"channeltype")->find($id);

        return $result;
    }

    /**
     * 获取单条记录
     * @author 小虎哥 by 2018-4-16
     */
    public function getInfoByWhere($where, $field = '*')
    {
        $result = Db::name('channeltype')->field($field)->where($where)->find();

        return $result;
    }

    /**
     * 获取多条记录
     * @author 小虎哥 by 2018-4-16
     */
    public function getListByIds($ids, $field = '*')
    {
        $map = array(
            'id'   => array('IN', $ids),
        );
        $result = Db::name('Channeltype')->field($field)
            ->where($map)
            ->order('sort_order asc')
            ->select();

        return $result;
    }

    /**
     * 默认获取全部
     * @author 小虎哥 by 2018-4-16
     */
    public function getAll($field = '*', $map = array(), $index_key = '')
    {
        $cacheKey = array(
            'common',
            'model',
            'Channeltype',
            'getAll',
            $field,
            $map,
            $index_key
        );
        $cacheKey = md5(json_encode($cacheKey));
        $result = cache($cacheKey);
        if (empty($result)) {
            $result = Db::name('channeltype')->field($field)
                ->where($map)
                ->order('sort_order asc, id asc')
                ->select();

            if (!empty($index_key)) {
                $result = convert_arr_key($result, $index_key);
            }

            cache($cacheKey, $result, null, 'channeltype');
        }

        return $result;
    }

    /**
     * 获取有栏目的模型列表
     * @param string $type yes表示存在栏目的模型列表，no表示不存在栏目的模型列表
     * @author 小虎哥 by 2018-4-16
     */
    public function getArctypeChannel($type = 'yes')
    {
        if ($type == 'yes') {
            $map = array(
                'b.status'    => 1,
            );
            $result = Db::name('Channeltype')->field('b.*, a.*, b.id as typeid')
                ->alias('a')
                ->join('__ARCTYPE__ b', 'b.current_channel = a.id', 'LEFT')
                ->where($map)
                ->group('a.id')
                ->cache(true,EYOUCMS_CACHE_TIME,"arctype")
                ->getAllWithIndex('nid');

        } else {
            $result = Db::name('Channeltype')->field('b.*, a.*, b.id as typeid')
                ->alias('a')
                ->join('__ARCTYPE__ b', 'b.current_channel = a.id', 'LEFT')
                ->group('a.id')
                ->cache(true,EYOUCMS_CACHE_TIME,"arctype")
                ->getAllWithIndex('nid');

            if ($result) {
                foreach ($result as $key => $val) {
                    if (intval($val['channeltype']) > 0) {
                        unset($result[$key]);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * 根据文档ID获取模型信息
     * @author 小虎哥 by 2018-4-16
     */
    public function getInfoByAid($aid)
    {
        $result = array();
        $res1 = Db::name('archives')->where(array('aid'=>$aid))->find();
        $res2 = Db::name('Channeltype')->where(array('id'=>$res1['channel']))->find();

        if (is_array($res1) && is_array($res2)) {
            $result = array_merge($res1, $res2);
        }

        return $result;
    }

    /**
     * 根据前端模板自动开启系统模型
     */
    public function setChanneltypeStatus()
    {
        $planPath = 'template/'.TPL_THEME.'pc';
        $planPath = realpath($planPath);
        if (!file_exists($planPath)) {
            return true;
        }
        $ctl_name_arr = array();
        $dirRes   = opendir($planPath);
        $view_suffix = config('template.view_suffix');
        while($filename = readdir($dirRes))
        {
            if(preg_match('/^(lists|view)?_/i', $filename) == 1)
            {
                $tplname = preg_replace('/([^_]+)?_([^\.]+)\.'.$view_suffix.'$/i', '${2}', $filename);
                $ctl_name_arr[] = ucwords($tplname);
            } elseif (preg_match('/\.'.$view_suffix.'$/i', $filename) == 1) {
                $tplname = preg_replace('/\.'.$view_suffix.'$/i', '', $filename);
                $ctl_name_arr[] = ucwords($tplname);
            }
        }
        $ctl_name_arr = array_unique($ctl_name_arr);

        if (!empty($ctl_name_arr)) {
            \think\Db::name('Channeltype')->where('id > 0')->cache(true,null,"channeltype")->update(array('status'=>0, 'update_time'=>getTime()));
            $map = array(
                'ctl_name'  => array('IN', $ctl_name_arr),
            );
            \think\Db::name('Channeltype')->where($map)->cache(true,null,"channeltype")->update(array('status'=>1, 'update_time'=>getTime()));
        } 
    }
}