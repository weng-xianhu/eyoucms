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

namespace app\common\logic;

use think\Model;
use think\Db;
/**
 * 栏目逻辑定义
 * @package common\Logic
 */
class ArctypeLogic extends Model
{

    /**
     * 获得指定栏目下的子栏目的数组
     *
     * @access  public
     * @param   int     $id     栏目的ID
     * @param   int     $selected   当前选中栏目的ID
     * @param   boolean $re_type    返回的类型: 值为真时返回下拉列表,否则返回数组
     * @param   int     $level      限定返回的级数。为0时返回所有级数
     * @param   array   $map      查询条件
     * @return  mix
     */
    public function arctype_list($id = 0, $selected = 0, $re_type = true, $level = 0, $map = array(), $is_cache = true)
    {
        static $res = NULL;
        // $res = NULL;
        
        if ($res === NULL)
        {
            $where = array(
                'status' => 1,
            );

            /*权限控制 by 小虎哥*/
            $admin_info = session('admin_info');
            if (in_array(MODULE_NAME, ['admin']) && 0 < intval($admin_info['role_id'])) {
                $auth_role_info = $admin_info['auth_role_info'];
                if(! empty($auth_role_info)){
                    if(! empty($auth_role_info['permission']['arctype'])){
                        $where['id'] = array('IN', $auth_role_info['permission']['arctype']);
                    }
                }
            }
            /*--end*/

            /*多语言 by 小虎哥*/
            if (empty($map['lang'])) {
                $where['lang'] = get_current_lang();
            }
            /*--end*/

            if (!empty($map)) {
                $where = array_merge($where, $map);
            }
            foreach ($where as $key => $val) {
                $key_tmp = 'c.'.$key;
                $where[$key_tmp] = $val;
                unset($where[$key]);
            }
            $fields = "c.*, c.id as typeid, count(s.id) as has_children, '' as children";
            $res = DB::name('arctype')
                ->field($fields)
                ->alias('c')
                ->join('__ARCTYPE__ s','s.parent_id = c.id','LEFT')
                ->where($where)
                ->group('c.id')
                ->order('c.parent_id asc, c.sort_order asc, c.id')
                ->cache($is_cache,EYOUCMS_CACHE_TIME,"arctype")
                ->select();
        }

        if (empty($res) == true)
        {
            return $re_type ? '' : array();
        }
    
        $options = $this->arctype_options($id, $res); // 获得指定栏目下的子栏目的数组

        /* 截取到指定的缩减级别 */
        if ($level > 0)
        {
            if ($id == 0)
            {
                $end_level = $level;
            }
            else
            {
                $first_item = reset($options); // 获取第一个元素
                $end_level  = $first_item['level'] + $level;
            }
    
            /* 保留level小于end_level的部分 */
            foreach ($options AS $key => $val)
            {
                if ($val['level'] >= $end_level)
                {
                    unset($options[$key]);
                }
            }
        }
    
        $pre_key = 0;
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
        }
    
        if ($re_type == true)
        {
            $select = '';
            foreach ($options AS $var)
            {
                $select .= '<option value="' . $var['id'] . '" ';
                $select .= ($selected == $var['id']) ? "selected='true'" : '';
                $select .= '>';
                if ($var['level'] > 0)
                {
                    $select .= str_repeat('&nbsp;', $var['level'] * 4);
                }
                $select .= htmlspecialchars(addslashes($var['typename'])) . '</option>';
            }
    
            return $select;
        }
        else
        {
            foreach ($options AS $key => $value)
            {
                ///$options[$key]['url'] = build_uri('article_cat', array('acid' => $value['id']), $value['cat_name']);
            }
            return $options;
        }
    }
    
    /**
     * 过滤和排序所有文章栏目，返回一个带有缩进级别的数组
     *
     * @access  private
     * @param   int     $id     上级栏目ID
     * @param   array   $arr        含有所有栏目的数组
     * @param   int     $level      级别
     * @return  void
     */
    public function arctype_options($spec_id, $arr)
    {
        static $cat_options = array();

        // $cat_options = array();
    
        if (isset($cat_options[$spec_id]))
        {
            return $cat_options[$spec_id];
        }
    
        if (!isset($cat_options[0]))
        {
            $level = $last_id = 0;
            $options = $id_array = $level_array = array();
            while (!empty($arr))
            {
                foreach ($arr AS $key => $value)
                {
                    $id = $value['id'];
                    if ($level == 0 && $last_id == 0)
                    {
                        if ($value['parent_id'] > 0)
                        {
                            break;
                        }
    
                        $options[$id]          = $value;
                        $options[$id]['level'] = $level;
                        $options[$id]['id']    = $id;
                        $options[$id]['typename']  = $value['typename'];
                        unset($arr[$key]);
    
                        if ($value['has_children'] == 0)
                        {
                            continue;
                        }
                        $last_id  = $id;
                        $id_array = array($id);
                        $level_array[$last_id] = ++$level;
                        continue;
                    }
    
                    if ($value['parent_id'] == $last_id)
                    {
                        $options[$id]          = $value;
                        $options[$id]['level'] = $level;
                        $options[$id]['id']    = $id;
                        $options[$id]['typename']  = $value['typename'];
                        unset($arr[$key]);
    
                        if ($value['has_children'] > 0)
                        {
                            if (end($id_array) != $last_id)
                            {
                                $id_array[] = $last_id;
                            }
                            $last_id    = $id;
                            $id_array[] = $id;
                            $level_array[$last_id] = ++$level;
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
                        $level = 0;
                        $last_id = 0;
                        $id_array = array();
                        continue;
                    }
                }
    
                if ($last_id && isset($level_array[$last_id]))
                {
                    $level = $level_array[$last_id];
                }
                else
                {
                    $level = 0;
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
    
            $spec_id_level = $options[$spec_id]['level'];
    
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
                if (($spec_id_level == $value['level'] && $value['id'] != $spec_id) ||
                    ($spec_id_level > $value['level']))
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