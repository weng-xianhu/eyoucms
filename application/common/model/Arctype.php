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
 * 栏目
 */
class Arctype extends Model
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
    public function getInfo($id, $field = '', $get_parent = false)
    {
        if (empty($field)) {
            $field = 'c.*, a.*';
        }
        $field .= ', a.id as typeid';

        /*当前栏目信息*/
        $result = db('Arctype')->field($field)
            ->alias('a')
            ->where('a.id', $id)
            ->join('__CHANNELTYPE__ c', 'c.id = a.current_channel', 'LEFT')
            ->cache(true,EYOUCMS_CACHE_TIME,"arctype")
            ->find();
        /*--end*/
        if (!empty($result)) {
            $result['typeurl'] = $this->getTypeUrl($result); // 当前栏目的URL

            if ($get_parent) {
                /*获取当前栏目父级栏目信息*/
                if ($result['parent_id'] > 0) {
                    $parent_row = db('Arctype')->field($field)
                        ->alias('a')
                        ->where('a.id', $result['parent_id'])
                        ->join('__CHANNELTYPE__ c', 'c.id = a.current_channel', 'LEFT')
                        ->cache(true,EYOUCMS_CACHE_TIME,"arctype")
                        ->find();
                    $ptypeurl = $this->getTypeUrl($parent_row);
                    $parent_row['typeurl'] = $ptypeurl;
                } else {
                    $parent_row = $result;
                }
                /*--end*/
                
                /*给每个父类字段开头加上p*/
                foreach ($parent_row as $key => $val) {
                    $newK = 'p'.$key;
                    $parent_row[$newK] = $val;
                }
                /*--end*/
                $result = array_merge($result, $parent_row);
            } else {
                if (!empty($result['parent_id'])) {
                    // 当前栏目的父级栏目信息
                    $parent_row = M('arctype')->where('id', $result['parent_id'])
                        ->cache(true,EYOUCMS_CACHE_TIME,"arctype")
                        ->find();
                    $ptypeurl = $this->getTypeUrl($parent_row);
                    $ptypename = $parent_row['typename'];
                    $pdirname = $parent_row['dirname'];
                    // 当前栏目的顶级栏目信息
                    if (!isset($result['toptypeurl'])) {
                        $allPid = $this->getAllPid($id);
                        $toptypeinfo = current($allPid);
                        $toptypeurl = $this->getTypeUrl($toptypeinfo);
                        $toptypename = $ptypename;
                        $topdirname = $pdirname;
                    }
                    // end
                } else {
                    // 当前栏目的父级栏目信息 或 顶级栏目的信息
                    $toptypeurl = $ptypeurl = $result['typeurl'];
                    $toptypename = $ptypename = $result['typename'];
                    $topdirname = $pdirname = $result['dirname'];
                }
                // 当前栏目的父级栏目信息
                $result['ptypeurl'] = $ptypeurl;
                $result['ptypename'] = $ptypename;
                $result['pdirname'] = $pdirname;
                // 当前栏目的顶级栏目信息
                !isset($result['toptypeurl']) && $result['toptypeurl'] = $toptypeurl;
                !isset($result['toptypename']) && $result['toptypename'] = $toptypename;
                !isset($result['topdirname']) && $result['topdirname'] = $topdirname;
                // end
            }
        }

        return $result;
    }

    /**
     * 根据目录名称获取单条记录
     * @author wengxianhu by 2018-4-20
     */
    public function getInfoByDirname($dirname)
    {
        $field = 'c.*, a.*, a.id as typeid';

        $result = db('Arctype')->field($field)
            ->alias('a')
            ->where('a.dirname', $dirname)
            ->join('__CHANNELTYPE__ c', 'c.id = a.current_channel', 'LEFT')
            ->cache(true,EYOUCMS_CACHE_TIME,"arctype")
            ->find();
        if (!empty($result)) {
            $result['typeurl'] = $this->getTypeUrl($result);

            $result_tmp = M('arctype')->where('id', $result['parent_id'])->find();
            $result['ptypeurl'] = $this->getTypeUrl($result_tmp);
        }

        return $result;
    }

    /**
     * 检测是否有子栏目
     * @author wengxianhu by 2017-7-26
     */
    public function hasChildren($id)
    {
        $count = db('Arctype')->where('parent_id', $id)->count('id');

        return ($count > 0 ? 1 : 0);
    }

    /**
     * 获取栏目的URL
     */
    public function getTypeUrl($res)
    {
        if ($res['is_part'] == 1) {
            $typeurl = $res['typelink'];
        } else {
            $ctl_name = get_controller_byct($res['current_channel']);
            $typeurl = typeurl('home/'.$ctl_name."/lists", $res);
        }

        return $typeurl;
    }


    /**
     * 获取指定级别的栏目列表
     * @param string type son表示下一级栏目,self表示同级栏目,top顶级栏目
     * @param boolean $self 包括自己本身
     * @author wengxianhu by 2018-4-26
     */
    public function getChannelList($id = '', $type = 'son')
    {
        $result = array();
        switch ($type) {
            case 'son':
                $result = $this->getSon($id, false);
                break;

            case 'self':
                $result = $this->getSelf($id);
                break;

            case 'top':
                $result = $this->getTop();
                break;

            case 'sonself':
                $result = $this->getSon($id, true);
                break;
        }

        return $result;
    }

    /**
     * 获取下一级栏目
     * @param string $self true表示没有子栏目时，获取同级栏目
     * @author wengxianhu by 2017-7-26
     */
    public function getSon($id, $self = false)
    {
        $result = array();
        if (empty($id)) {
            return $result;
        }

        $arctypeLogic = new \app\common\logic\ArctypeLogic();
        $arctype_max_level = intval(config('global.arctype_max_level'));
        $map = array(
            'is_hidden'   => 0,
            'status'  => 1,
            'is_del'  => 0, // 回收站功能
        );
        $res = $arctypeLogic->arctype_list($id, 0, false, $arctype_max_level, $map);

        if (!empty($res)) {
            $arr = group_same_key($res, 'parent_id');
            for ($i=0; $i < $arctype_max_level; $i++) {
                foreach ($arr as $key => $val) {
                    foreach ($arr[$key] as $key2 => $val2) {
                        if (!isset($arr[$val2['id']])) continue;
                        $val2['children'] = $arr[$val2['id']];
                        $arr[$key][$key2] = $val2;
                    }
                }
            }
            if (isset($arr[$id])) {
                $result = $arr[$id];
            }
        }

        if (empty($result) && $self == true) {
            $result = $this->getSelf($id);
        }

        return $result;
    }

    /**
     * 获取同级栏目
     * @author wengxianhu by 2017-7-26
     */
    public function getSelf($id)
    {
        $result = array();
        if (empty($id)) {
            return $result;
        }

        $map = array(
            'id'   => $id,
            'is_hidden'   => 0,
            'status'  => 1,
            'is_del'  => 0, // 回收站功能
        );
        $res = M('arctype')->field('parent_id')->where($map)->find();

        if ($res) {
            $newId = $res['parent_id'];
            $arctypeLogic = new \app\common\logic\ArctypeLogic();
            $arctype_max_level = intval(config('global.arctype_max_level'));
            $map = array(
                'is_hidden'   => 0,
                'status'  => 1,
            );
            $res = $arctypeLogic->arctype_list($newId, 0, false, $arctype_max_level, $map);

            if (!empty($res)) {
                $arr = group_same_key($res, 'parent_id');
                for ($i=0; $i < $arctype_max_level; $i++) { 
                    foreach ($arr as $key => $val) {
                        foreach ($arr[$key] as $key2 => $val2) {
                            if (!isset($arr[$val2['id']])) continue;
                            $val2['children'] = $arr[$val2['id']];
                            $arr[$key][$key2] = $val2;
                        }
                    }
                }
                $result = $arr[$newId];
            }
        }
        return $result;
    }

    /**
     * 获取顶级栏目
     * @author wengxianhu by 2017-7-26
     */
    public function getTop()
    {
        $arctypeLogic = new \app\common\logic\ArctypeLogic();
        $arctype_max_level = intval(config('global.arctype_max_level'));
        $map = array(
            'is_hidden'   => 0,
            'status'  => 1,
            'is_del'  => 0, // 回收站功能
        );
        $res = $arctypeLogic->arctype_list(0, 0, false, $arctype_max_level, $map);

        $result = array();
        if (!empty($res)) {
            $arr = group_same_key($res, 'parent_id');
            for ($i=0; $i < $arctype_max_level; $i++) { 
                foreach ($arr as $key => $val) {
                    foreach ($arr[$key] as $key2 => $val2) {
                        if (!isset($arr[$val2['id']])) continue;
                        $val2['children'] = $arr[$val2['id']];
                        $arr[$key][$key2] = $val2;
                    }
                }
            }
            reset($arr);
            $firstResult = current($arr);
            $result = $firstResult;
        }

        return $result;
    }

    /**
     * 获取当前栏目及所有子栏目
     * @param boolean $self 包括自己本身
     * @author wengxianhu by 2017-7-26
     */
    public function getHasChildren($id, $self = true)
    {
        $lang = get_current_lang(); // 多语言
        $cacheKey = "common_model_Arctype_getHasChildren_{$id}_{$self}_{$lang}";
        $result = cache($cacheKey);
        if (empty($result)) {
            $where = array(
                'c.status'  => 1,
                'c.lang'    => $lang,
                'c.is_del'  => 0,
            );
            $fields = "c.*, count(s.id) as has_children";
            $res = db('arctype')
                ->field($fields)
                ->alias('c')
                ->join('__ARCTYPE__ s','s.parent_id = c.id','LEFT')
                ->where($where)
                ->group('c.id')
                ->order('c.parent_id asc, c.sort_order asc, c.id')
                ->select();

            $result = arctype_options($id, $res, 'id', 'parent_id');

            if (!$self) {
                array_shift($result);
            }

            cache($cacheKey, $result, null, "arctype");
        }

        return $result;
    }

    /**
     * 获取所有栏目
     * @param   int     $id     栏目的ID
     * @param   int     $selected   当前选中栏目的ID
     * @param   int     $channeltype      查询条件
     * @author wengxianhu by 2017-7-26
     */
    public function getList($id = 0, $select = 0, $re_type = true, $map = array())
    {
        $id = $id ? intval($id) : 0;
        $select = $select ? intval($select) : 0;

        $arctypeLogic = new \app\common\logic\ArctypeLogic();
        $arctype_max_level = intval(config('global.arctype_max_level'));
        $options = $arctypeLogic->arctype_list($id, $select, $re_type, $arctype_max_level, $map);

        return $options;
    }


    /**
     * 默认获取全部
     * @author 小虎哥 by 2018-4-16
     */
    public function getAll($field = '*', $map = array(), $index_key = '')
    {
        $lang = get_current_lang(); // 多语言
        $result = db('arctype')->field($field)
            ->where($map)
            ->where('lang',$lang)
            ->order('sort_order asc')
            ->cache(true,EYOUCMS_CACHE_TIME,"arctype")
            ->select();

        if (!empty($index_key)) {
            $result = convert_arr_key($result, $index_key);
        }

        return $result;
    }

    /**
     * 获取当前栏目的所有父级
     * @author wengxianhu by 2018-4-26
     */
    public function getAllPid($id)
    {
        $cacheKey = array(
            'common',
            'model',
            'Arctype',
            'getAllPid',
            $id,
        );
        $cacheKey = json_encode($cacheKey);
        $data = cache($cacheKey);
        if (empty($data)) {
            $data = array();
            $typeid = $id;
            $arctype_list = M('Arctype')->field('*, id as typeid')
                ->where([
                    'status'    => 1,
                    'is_del'    => 0,
                ])
                ->getAllWithIndex('id');
            if (isset($arctype_list[$typeid])) {
                // 第一个先装起来
                $arctype_list[$typeid]['typeurl'] = $this->getTypeUrl($arctype_list[$typeid]);
                $data[$typeid] = $arctype_list[$typeid];
            } else {
                return $data;
            }

            while (true)
            {
                $typeid = $arctype_list[$typeid]['parent_id'];
                if($typeid > 0){
                    if (isset($arctype_list[$typeid])) {
                        $arctype_list[$typeid]['typeurl'] = $this->getTypeUrl($arctype_list[$typeid]);
                        $data[$typeid] = $arctype_list[$typeid];
                    }
                } else {
                    break;
                }
            }
            $data = array_reverse($data, true);

            cache($cacheKey, $data, null, "arctype");
        }

        return $data;
    }

    /**
     * 伪删除指定栏目（包括子栏目、所有相关文档）
     */
    public function pseudo_del($typeid)
    {
        $childrenList = $this->getHasChildren($typeid); // 获取当前栏目以及所有子栏目
        $typeidArr = get_arr_column($childrenList, 'id'); // 获取栏目数组里的所有栏目ID作为新的数组
        $typeidArr2 = $typeidArr;

        /*多语言*/
        $attr_name_arr = [];
        foreach ($typeidArr as $key => $val) {
            $attr_name = 'tid'.$val;
            $attr_name_arr[$key] = $attr_name;
        }
        $attr_values = Db::name('language_attr')->where([
                'attr_name'    => ['IN', $attr_name_arr],
                'attr_group'    => 'arctype',
            ])->column('attr_value');
        !empty($attr_values) && $typeidArr = $attr_values;
        /*--end*/

        /*标记当前栏目以及子栏目为被动伪删除*/
        $sta1 = Db::name('arctype')
            ->where([
                'id'    => ['IN', $typeidArr],
                'is_del'    => 0,
                'del_method' => 0,
            ])
            ->cache(true,null,"arctype")
            ->update([
                'is_del'    => 1,
                'del_method'    => 2, // 1为主动删除，2为跟随上级栏目被动删除
                'update_time'   => getTime(),
            ]); // 伪删除栏目
        /*--end*/

        /*标记当前栏目为主动伪删除*/
        // 多语言
        $attr_values = Db::name('language_attr')->where([
                'attr_name'    => 'tid'.$typeid,
                'attr_group'    => 'arctype',
            ])->column('attr_value');
        !empty($attr_values) && $typeidArr2 = $attr_values;
        // --end
        $sta2 = Db::name('arctype')
            ->where([
                'id'    => ['IN', $typeidArr2],
            ])
            ->cache(true,null,"arctype")
            ->update([
                'is_del'    => 1,
                'del_method'    => 1, // 1为主动删除，2为跟随上级栏目被动删除
                'update_time'   => getTime(),
            ]); // 伪删除栏目
        /*--end*/

        if ($sta1 && $sta2) {
            model('Archives')->pseudo_del($typeidArr); // 删除文档
            // 删除多语言栏目关联绑定
            if (!empty($attr_name_arr)) {
                Db::name('language_attribute')->where([
                    'attr_name'     => ['IN',$attr_name_arr],
                ])->update([
                    'is_del'    => 1,
                    'update_time'   => getTime(),
                ]);
            }
            /*--end*/

            /*清除页面缓存*/
            // $htmlCacheLogic = new \app\common\logic\HtmlCacheLogic;
            // $htmlCacheLogic->clear_arctype();
            /*--end*/

            return true;
        }

        return false;
    }

    /**
     * 删除指定栏目（包括子栏目、所有相关文档）
     */
    public function del($typeid)
    {
        $childrenList = $this->getHasChildren($typeid); // 获取当前栏目以及所有子栏目
        $typeidArr = get_arr_column($childrenList, 'id'); // 获取栏目数组里的所有栏目ID作为新的数组
        /*多语言*/
        $attr_name_arr = [];
        foreach ($typeidArr as $key => $val) {
            $attr_name = 'tid'.$val;
            $attr_name_arr[$key] = $attr_name;
        }
        $attr_values = Db::name('language_attr')->where([
                'attr_name'    => ['IN', $attr_name_arr],
                'attr_group'    => 'arctype',
            ])->column('attr_value');
        !empty($attr_values) && $typeidArr = $attr_values;
        /*--end*/
        $sta = Db::name('arctype')
            ->where([
                'id'    => ['IN', $typeidArr],
            ])
            ->cache(true,null,"arctype")
            ->delete(); // 删除栏目
        if ($sta) {
            model('Archives')->del($typeidArr); // 删除文档
            // 删除多语言栏目关联绑定
            if (!empty($attr_name_arr)) {
                Db::name('language_attribute')->where([
                    'attr_name'     => ['IN',$attr_name_arr],
                ])->update([
                    'is_del'    => 1,
                    'update_time'   => getTime(),
                ]);
            }
            /*--end*/

            /*清除页面缓存*/
            // $htmlCacheLogic = new \app\common\logic\HtmlCacheLogic;
            // $htmlCacheLogic->clear_arctype();
            /*--end*/

            return true;
        }

        return false;
    }

    /**
     * 每个栏目的顶级栏目的目录名称
     */
    public function getEveryTopDirnameList()
    {
        $result = extra_cache('common_getEveryTopDirnameList_model');
        if ($result === false)
        {
            $lang = get_current_lang(); // 多语言
            $fields = "c.id, c.parent_id, c.dirname, c.grade, count(s.id) as has_children";
            $row = db('arctype')
                ->field($fields)
                ->alias('c')
                ->join('__ARCTYPE__ s','s.parent_id = c.id','LEFT')
                ->where('c.lang',$lang)
                ->group('c.id')
                ->order('c.parent_id asc, c.sort_order asc, c.id')
                ->cache(true,EYOUCMS_CACHE_TIME,"arctype")
                ->select();
            $row = arctype_options(0, $row, 'id', 'parent_id');

            $result = array();
            foreach ($row as $key => $val) {
                if (empty($val['parent_id'])) {
                    $val['tdirname'] = $val['dirname'];
                } else {
                    $val['tdirname'] = isset($row[$val['parent_id']]['tdirname']) ? $row[$val['parent_id']]['tdirname'] : $val['dirname'];
                }
                $row[$key] = $val;
                $result[md5($val['dirname'])] = $val;
            }

            extra_cache('common_getEveryTopDirnameList_model', $result);
        }

        return $result;
    }

    /**
     * 新增栏目数据
     *
     * @param array $data
     * @return intval|boolean
     */
    public function addData($data = [])
    {
        $insertId = false;
        if (!empty($data)) {
            $insertId = M('arctype')->insertGetId($data);
            if($insertId){
                // --存储单页模型
                if ($data['current_channel'] == 6) {
                    $archivesData = array(
                        'title' => $data['typename'],
                        'typeid'=> $insertId,
                        'channel'   => $data['current_channel'],
                        'sort_order'    => 100,
                        'lang'  => $data['lang'],
                        'add_time'  => getTime(),
                    );
                    // $archivesData = array_merge($archivesData, $data);
                    $aid = M('archives')->insertGetId($archivesData);
                    if ($aid) {
                        // ---------后置操作
                        if (!isset($post['addonFieldExt'])) {
                            $post['addonFieldExt'] = array(
                                'typeid'    => $archivesData['typeid'],
                            );
                        } else {
                            $post['addonFieldExt']['typeid'] = $archivesData['typeid'];
                        }
                        $addData = array(
                            'addonFieldExt' => $post['addonFieldExt'],
                        );
                        $addData = array_merge($addData, $archivesData);
                        model('Single')->afterSave($aid, $addData, 'add');
                        // ---------end
                    }
                }

                /*同步栏目ID到权限组，默认是赋予该栏目的权限*/
                model('AuthRole')->syn_auth_role($insertId);
                /*--end*/

                /*清除页面缓存*/
                // $htmlCacheLogic = new \app\common\logic\HtmlCacheLogic;
                // $htmlCacheLogic->clear_arctype();
                /*--end*/

                // \think\Cache::clear("arctype");
                // extra_cache('admin_all_menu', NULL);
                // \think\Cache::clear('admin_archives_release');
            }
        }
        return $insertId;
    }

    /**
     * 编辑栏目数据
     *
     * @param array $data
     * @return intval|boolean
     */
    public function updateData($data = [])
    {
        $bool = false;
        if (!empty($data)) {
            $admin_lang = get_admin_lang();
            $bool = M('arctype')->where([
                    'id'    => $data['id'],
                    'lang'  => $admin_lang,
                ])
                ->cache(true,null,"arctype")
                ->update($data);
            if($bool){
                /*批量更新所有子孙栏目的最顶级模型ID*/
                $allSonTypeidArr = $this->getHasChildren($data['id'], false); // 获取当前栏目的所有子孙栏目（不包含当前栏目）
                if (!empty($allSonTypeidArr)) {
                    $i = 1;
                    $minuendGrade = 0;
                    foreach ($allSonTypeidArr as $key => $val) {
                        if ($i == 1) {
                            $firstGrade = intval($post['oldgrade']);
                            $minuendGrade = intval($grade) - $firstGrade;
                        }
                        $update_data = array(
                            'channeltype'        => $data['channeltype'],
                            'update_time'        => getTime(),
                            'grade'   =>  Db::raw('grade+'.$minuendGrade),
                        );
                        M('arctype')->where([
                                'id'    => $val['id'],
                                'lang'  => $admin_lang,
                            ])
                            ->cache(true,null,"arctype")
                            ->update($update_data);
                        ++$i;
                    }
                }
                /*--end*/

                // --存储单页模型
                if ($data['current_channel'] == 6) {
                    $archivesData = array(
                        'title' => $data['typename'],
                        'typeid'=> $data['id'],
                        'channel'   => $data['current_channel'],
                        'sort_order'    => 100,
                        'update_time'     => getTime(),
                    );
                    // $archivesData = array_merge($archivesData, $data);
                    $aid = M('single_content')->where(array('typeid'=>$data['id']))->getField('aid');
                    if (empty($aid)) {
                        $opt = 'add';
                        $archivesData['lang'] = get_admin_lang();
                        $archivesData['add_time'] = getTime();
                        $up = $aid = M('archives')->insertGetId($archivesData);
                    } else {
                        $opt = 'edit';
                        $up = M('archives')->where([
                                'aid'   => $aid,
                                'lang'  => get_admin_lang(),
                            ])->update($archivesData);
                    }
                    if ($up) {
                        // ---------后置操作
                        if (!isset($post['addonFieldExt'])) {
                            $post['addonFieldExt'] = array(
                                'typeid'    => $data['id'],
                            );
                        } else {
                            $post['addonFieldExt']['typeid'] = $data['id'];
                        }
                        $updateData = array(
                            'addonFieldExt' => $post['addonFieldExt'],
                        );
                        $updateData = array_merge($updateData, $archivesData);
                        model('Single')->afterSave($aid, $updateData, $opt);
                        // ---------end
                    }
                }

                /*同步更改其他语言关联绑定的栏目模型*/
                $attr_name = Db::name('language_attr')->where([
                        'attr_value'     => $data['id'],
                        'attr_group'    => 'arctype',
                        'lang'  => get_admin_lang(),
                    ])->getField('attr_name');
                $attr_values = Db::name('language_attr')->where([
                        'attr_name'     => $attr_name,
                        'attr_group'    => 'arctype',
                    ])->column('attr_value');
                Db::name('arctype')->where([
                        'id'    => ['IN', $attr_values],
                    ])->update([
                        'current_channel'   => $data['current_channel'],
                        'update_time'   => getTime(),
                    ]);
                /*--end*/

                /*清除页面缓存*/
                // $htmlCacheLogic = new \app\common\logic\HtmlCacheLogic;
                // $htmlCacheLogic->clear_arctype();
                /*--end*/

                // \think\Cache::clear("arctype");
                // extra_cache('admin_all_menu', NULL);
                // \think\Cache::clear('admin_archives_release');
            }
        }
        return $bool;
    }
}