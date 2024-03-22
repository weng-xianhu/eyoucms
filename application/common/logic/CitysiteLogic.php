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
 * Date: 2021-7-18
 */

namespace app\common\logic;

use think\Model;
use think\Db;
/**
 * 城市站点逻辑定义
 * @package common\Logic
 */
class CitysiteLogic extends Model
{
    /**
     * 析构函数
     */
    function  __construct() {
        function_exists('set_time_limit') && set_time_limit(0);
        @ini_set('memory_limit','-1');
    }

    /**
     * 获得指定城市站点下的子城市站点的数组
     *
     * @access  public
     * @param   int     $id     城市站点的ID
     * @param   int     $selected   当前选中城市站点的ID
     * @param   boolean $re_type    返回的类型: 值为真时返回下拉列表,否则返回数组
     * @param   int     $grade      限定返回的级数。为0时返回所有级数
     * @param   array   $map      查询条件
     * @return  mix
     */
    public function citysite_list($id = 0, $selected = 0, $re_type = true, $grade = 0, $map = array(), $is_cache = true)
    {
        static $res = NULL;
        
        if ($res === NULL)
        {
            $where = array(
                'status' => 1,
            );

            if (!empty($map)) {
                $where = array_merge($where, $map);
            }
            foreach ($where as $key => $val) {
                $key_tmp = 'c.'.$key;
                $where[$key_tmp] = $val;
                unset($where[$key]);
            }
            $fields = "c.*, c.id as siteid, count(s.id) as has_children, '' as children";
            $args = [$fields, $where, $is_cache];
            $cacheKey = 'citysite-'.md5(__CLASS__.__FUNCTION__.json_encode($args));
            $res = cache($cacheKey);
            if (empty($res) || empty($is_cache)) {
                $res = Db::name('citysite')
                    ->field($fields)
                    ->alias('c')
                    ->join('__CITYSITE__ s','s.parent_id = c.id','LEFT')
                    ->where($where)
                    ->group('c.id')
                    ->order('c.parent_id asc, c.sort_order asc, c.id')
                    ->cache($is_cache,EYOUCMS_CACHE_TIME,"citysite")
                    ->select();
                cache($cacheKey, $res, null, 'citysite');
            }
        }

        if (empty($res) == true)
        {
            return $re_type ? '' : array();
        }

        $cacheKey = 'citysite-'.md5($id.json_encode($res));
        $options = cache($cacheKey);
        if (empty($options)) {
            $options = $this->citysite_options($id, $res); // 获得指定城市站点下的子城市站点的数组
            cache($cacheKey, $options, null, 'citysite');
        }

        /* 截取到指定的缩减级别 */
        $citysite_max_level = intval(config('global.citysite_max_level'));
        if ($grade > 0 && $grade < $citysite_max_level)
        {
            if ($id == 0)
            {
                $end_grade = $grade;
            }
            else
            {
                $first_item = reset($options); // 获取第一个元素
                $end_grade  = $first_item['grade'] + $grade;
            }
    
            /* 保留grade小于end_grade的部分 */
            foreach ($options AS $key => $val)
            {
                if ($val['grade'] >= $end_grade)
                {
                    unset($options[$key]);
                }
            }
        }
    
        $pre_key = 0;
        $select = '';
        foreach ($options AS $key => $value)
        {
            $options[$key]['has_children'] = 0;
            if ($pre_key > 0)
            {
                if ($options[$pre_key]['id'] == $options[$key]['parent_id'])
                {
                    $options[$pre_key]['has_children'] = 1;
                }
            }
            $pre_key = $key;

            if ($re_type == true) {
                $select .= '<option value="' . $value['id'] . '" ';
                $select .= ($selected == $value['id']) ? "selected='true'" : '';
                $select .= '>';
                if ($value['grade'] > 0)
                {
                    $select .= str_repeat('&nbsp;', $value['grade'] * 4);
                }
                $select .= htmlspecialchars_decode(addslashes($value['name'])) . '</option>';
            }
        }
    
        if ($re_type == true) {
            return $select;
        } else {
            return $options;
        }
    }
    
    /**
     * 过滤和排序所有城市站点，返回一个带有缩进级别的数组
     *
     * @access  private
     * @param   int     $id     上级城市站点ID
     * @param   array   $arr        含有所有城市站点的数组
     * @param   int     $grade      级别
     * @return  void
     */
    public function citysite_options($spec_id, $arr)
    {
        static $cat_options = array();

        if (isset($cat_options[$spec_id]))
        {
            return $cat_options[$spec_id];
        }
    
        if (!isset($cat_options[0]))
        {
            $grade = $last_id = 0;
            $options = $id_array = $grade_array = array();
            while (!empty($arr))
            {
                foreach ($arr AS $key => $value)
                {
                    $id = $value['id'];
                    if ($grade == 0 && $last_id == 0)
                    {
                        if ($value['parent_id'] > 0)
                        {
                            break;
                        }
    
                        $options[$id]          = $value;
                        $options[$id]['grade'] = $grade;
                        $options[$id]['id']    = $id;
                        $options[$id]['name']  = htmlspecialchars_decode($value['name']);
                        unset($arr[$key]);
    
                        if ($value['has_children'] == 0)
                        {
                            continue;
                        }
                        $last_id  = $id;
                        $id_array = array($id);
                        $grade_array[$last_id] = ++$grade;
                        continue;
                    }
    
                    if ($value['parent_id'] == $last_id)
                    {
                        $options[$id]          = $value;
                        $options[$id]['grade'] = $grade;
                        $options[$id]['id']    = $id;
                        $options[$id]['name']  = htmlspecialchars_decode($value['name']);
                        unset($arr[$key]);
    
                        if ($value['has_children'] > 0)
                        {
                            if (end($id_array) != $last_id)
                            {
                                $id_array[] = $last_id;
                            }
                            $last_id    = $id;
                            $id_array[] = $id;
                            $grade_array[$last_id] = ++$grade;
                        }
                    }
                    elseif ($value['parent_id'] > $last_id)
                    {
                        break;
                    }
                }
    
                $count = count($id_array);
                if ($count > 1)
                {
                    $last_id = array_pop($id_array);
                }
                elseif ($count == 1)
                {
                    if ($last_id != end($id_array))
                    {
                        $last_id = end($id_array);
                    }
                    else
                    {
                        $grade = 0;
                        $last_id = 0;
                        $id_array = array();
                        continue;
                    }
                }
    
                if ($last_id && isset($grade_array[$last_id]))
                {
                    $grade = $grade_array[$last_id];
                }
                else
                {
                    $grade = 0;
                    break;
                }
            }
            $cat_options[0] = $options;
        }
        else
        {
            $options = $cat_options[0];
        }
    
        if (!$spec_id)
        {
            return $options;
        }
        else
        {
            if (empty($options[$spec_id]))
            {
                return array();
            }
    
            $spec_id_grade = $options[$spec_id]['grade'];
    
            foreach ($options AS $key => $value)
            {
                if ($key != $spec_id)
                {
                    unset($options[$key]);
                }
                else
                {
                    break;
                }
            }
    
            $spec_id_array = array();
            foreach ($options AS $key => $value)
            {
                if (($spec_id_grade == $value['grade'] && $value['id'] != $spec_id) ||
                    ($spec_id_grade > $value['grade']))
                {
                    break;
                }
                else
                {
                    $spec_id_array[$key] = $value;
                }
            }
            $cat_options[$spec_id] = $spec_id_array;
    
            return $spec_id_array;
        }
    }
}