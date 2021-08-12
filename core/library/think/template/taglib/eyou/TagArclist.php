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
use app\home\logic\FieldLogic;

/**
 * 文章列表
 */
class TagArclist extends Base
{
    public $fieldLogic;
    public $archives_db;
    public $url_screen_var;

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->fieldLogic = new FieldLogic();
        $this->archives_db = Db::name('archives');
        /*应用于文档列表*/
        if ($this->aid > 0) {
            $this->tid = $this->archives_db->where('aid', $this->aid)->getField('typeid');
        }
        /*--end*/
        
        // 定义筛选标识
        $this->url_screen_var = config('global.url_screen_var');
    }

    /**
     *  arclist解析函数
     *
     * @author wengxianhu by 2018-4-20
     * @access    public
     * @param     array  $param  查询数据条件集合
     * @param     int  $row  调用行数
     * @param     string  $orderby  排列顺序
     * @param     string  $addfields  附加表字段，以逗号隔开
     * @param     string  $orderway  排序方式
     * @param     string  $tagid  标签id
     * @param     string  $tag  标签属性集合
     * @param     string  $pagesize  分页显示条数
     * @param     string  $thumb  是否开启缩略图
     * @param     string  $arcrank  是否显示会员权限
     * @return    array
     */
    public function getArclist($param = array(),  $row = 15, $orderby = '', $addfields = '', $orderway = '', $tagid = '', $tag = '', $pagesize = 0, $thumb = '', $arcrank = '')
    {
        $condition = array();

        /*自定义字段筛选*/
        $url_screen_var = 0;
        if (!empty($tag['url_params'])) {
            $paramNew = $tag['url_params'] = json_decode(base64_decode($tag['url_params']), true);
            $url_screen_var = !empty($paramNew[$this->url_screen_var]) ? $paramNew[$this->url_screen_var] : 0;
        } else {
            $UrlParams = $paramNew = input('param.');
            $url_screen_var = !empty($paramNew[$this->url_screen_var]) ? $paramNew[$this->url_screen_var] : 0;
            foreach ($UrlParams as $key => $value) {
                if (in_array($key, ['m', 'c', 'a'])) {
                    unset($UrlParams[$key]);
                }
            }
            $tag['url_params'] = base64_encode(json_encode($UrlParams));
        }
        if (1 == $url_screen_var) {
            /*自定义字段筛选*/
            $field_where = [
                'is_screening' => 1,
                // 根据需求新增条件
            ];
            // 所有应用于搜索的自定义字段
            $channelfield = Db::name('channelfield')->where($field_where)->field('channel_id,id,name,dtype,dfvalue')->select();
            // 查询当前栏目所属模型
            $channel_id = Db::name('arctype')->where('id', $paramNew['tid'])->getField('current_channel');
            // 所有模型类别
            $channeltype_list = config('global.channeltype_list');
            $channel_table = array_search($channel_id, $channeltype_list);
            // 查询获取aid初始sql语句
            $wheres = [];
            $where_multiple = [];
            foreach ($channelfield as $key => $value) {
                // 值不为空则执行
                $fieldname = $value['name'];
                if (!empty($fieldname) && !empty($paramNew[$fieldname])) {
                    // 分割参数，判断多选或单选，拼装sql语句
                    $val_arr  = explode('|', trim($paramNew[$fieldname], '|'));
                    if (!empty($val_arr)) {
                        if ('' == $val_arr[0]) {
                            // 选择全部时拼装sql语句
                            // $wheres[$fieldname] = ['NEQ', null];
                        } else {
                            if (1 == count($val_arr)) {
                                // 多选字段类型
                                if ('checkbox' == $value['dtype']) {
                                    $val_arr[0] = addslashes($val_arr[0]);
                                    $dfvalue_tmp = explode(',', $value['dfvalue']);
                                    if (in_array($val_arr[0], $dfvalue_tmp)) {
                                        array_push($where_multiple, "FIND_IN_SET('".$val_arr[0]."',{$fieldname})");
                                    }
                                } else {
                                    $wheres[$fieldname] = $val_arr[0];
                                }
                            }
                        }
                    }
                }
            }

            $where_multiple_str = "";
            !empty($where_multiple) && $where_multiple_str = implode(' AND ', $where_multiple);
            $aid_result = Db::name($channel_table.'_content')->field('aid')
                ->where($wheres)
                ->where($where_multiple_str)
                ->select();
            if (!empty($aid_result)) {
                array_push($condition, "a.aid IN (".implode(',', get_arr_column($aid_result, "aid")).")");
            } else {
                $pages = Db::name('archives')->field("aid")->where("aid=0")->paginate($pagesize);
                $result['pages'] = $pages; // 分页显示输出
                $result['list'] = []; // 赋值数据集
                return $result;
            }
            /*结束*/
        }
        /*--end*/

        $result = false;

        $channeltype = ("" != $param['channel'] && is_numeric($param['channel'])) ? intval($param['channel']) : '';
        $param['typeid'] = !empty($param['typeid']) ? $param['typeid'] : $this->tid;
        empty($orderway) && $orderway = 'desc';
        $pagesize = empty($pagesize) ? intval($row) : intval($pagesize);
        $limit = $row;

        if (!empty($param['typeid'])) {
            if (!preg_match('/^\d+([\d\,]*)$/i', $param['typeid'])) {
                echo '标签arclist报错：typeid属性值语法错误，请正确填写栏目ID。';
                return false;
            }

            // 过滤typeid中含有空值的栏目ID
            $typeidArr_tmp = explode(',', $param['typeid']);
            $typeidArr_tmp = array_unique($typeidArr_tmp);
            foreach($typeidArr_tmp as $k => $v){   
                if (empty($v)) unset($typeidArr_tmp[$k]);  
            }
            $param['typeid'] = implode(',', $typeidArr_tmp);
            
            // 多语言
            $param['typeid'] = model('LanguageAttr')->getBindValue($param['typeid'], 'arctype');
            if (empty($param['typeid'])) {
                echo '标签arclist报错：找不到与第一套【'.$this->main_lang.'】语言关联绑定的属性 typeid 值。';
                return false;
            }
        }

        $typeid = $param['typeid'];

        $allow_release_channel = config('global.allow_release_channel');
        /*不指定模型ID、栏目ID，默认显示所有可以发布文档的模型ID下的文档*/
        if (("" === $channeltype && empty($typeid)) || 0 === $channeltype) {
            $channeltype = $param['channel'] = implode(',', $allow_release_channel);
        }
        /*--end*/

        if (!empty($param['joinaid'])) {
            $joinaid = intval($param['joinaid']);
            if (!isset($tag['channel'])) unset($param['channel']);
            if (!isset($tag['typeid'])) {
                unset($param['typeid']);
            } else {
                $channeltype = $param['channel'] = M('arctype')->where('id', intval($param['typeid']))->getField('current_channel');
            }

        } else {
            if (!empty($channeltype)) { // 如果指定了频道ID，则频道下的所有文档都展示
                unset($param['typeid']);
            } else {
                // unset($param['channel']);
                if (!empty($typeid)) {
                    $typeidArr = explode(',', $typeid);
                    if (count($typeidArr) == 1) {
                        $typeid = intval($typeid);
                        $channel_info = M('Arctype')->field('id,current_channel')->where(array('id'=>array('eq', $typeid)))->find();
                        if (empty($channel_info)) {
                            echo '标签arclist报错：指定属性 typeid 的栏目ID不存在。';
                            return false;
                        }
                        $channeltype = !empty($channel_info) ? $channel_info["current_channel"] : ''; // 当前栏目ID所属模型ID
                        /*当前模型ID不属于含有列表模型，直接返回无数据*/
                        if (false === array_search($channeltype, $allow_release_channel)) {
                            return false;
                        }
                        /*end*/
                        /*获取当前栏目下的所有同模型的子孙栏目*/
                        $arctype_list = model("Arctype")->getHasChildren($channel_info['id']);
                        foreach ($arctype_list as $key => $val) {
                            if ($channeltype != $val['current_channel']) {
                                unset($arctype_list[$key]);
                            }
                        }
                        $typeids = get_arr_column($arctype_list, "id");
                        !in_array($typeid, $typeids) && $typeids[] = $typeid;
                        $typeid = implode(",", $typeids);
                        /*--end*/
                    } elseif (count($typeidArr) > 1) {
                        $firstTypeid = intval($typeidArr[0]);
                        $channeltype = M('Arctype')->where('id', $firstTypeid)->getField('current_channel');
                    }
                    $param['channel'] = $channeltype;
                }
            }
        }

        // 查询条件
        foreach (array('keyword','typeid','notypeid','flag','noflag','channel','joinaid') as $key) {
            if (isset($param[$key]) && $param[$key] !== '') {
                if ($key == 'channel') {
                    array_push($condition, "a.channel IN ({$channeltype})");
                } elseif ($key == 'typeid') {
                    array_push($condition, "a.typeid IN ({$typeid})");
                } elseif ($key == 'notypeid') {
                    $param[$key] = str_replace('，', ',', $param[$key]);
                    array_push($condition, "a.typeid NOT IN (".$param[$key].")");
                } elseif ($key == 'flag') {
                    $flag_arr = explode(",", $param[$key]);
                    $where_or_flag = array();
                    foreach ($flag_arr as $k2 => $v2) {
                        if ($v2 == "c") {
                            array_push($where_or_flag, "a.is_recom = 1");
                        } elseif ($v2 == "h") {
                            array_push($where_or_flag, "a.is_head = 1");
                        } elseif ($v2 == "a") {
                            array_push($where_or_flag, "a.is_special = 1");
                        } elseif ($v2 == "j") {
                            array_push($where_or_flag, "a.is_jump = 1");
                        } elseif ($v2 == "p") {
                            array_push($where_or_flag, "a.is_litpic = 1");
                        } elseif ($v2 == "b") {
                            array_push($where_or_flag, "a.is_b = 1");
                        } elseif ($v2 == "s") {
                            array_push($where_or_flag, "a.is_slide = 1");
                        } elseif ($v2 == "r") {
                            array_push($where_or_flag, "a.is_roll = 1");
                        } elseif ($v2 == "d") {
                            array_push($where_or_flag, "a.is_diyattr = 1");
                        }
                    }
                    if (!empty($where_or_flag)) {
                        $where_flag_str = " (".implode(" OR ", $where_or_flag).") ";
                        array_push($condition, $where_flag_str);
                    }
                } elseif ($key == 'noflag') {
                    $flag_arr = explode(",", $param[$key]);
                    $where_or_flag = array();
                    foreach ($flag_arr as $nk2 => $nv2) {
                        if ($nv2 == "c") {
                            array_push($where_or_flag, "a.is_recom <> 1");
                        } elseif ($nv2 == "h") {
                            array_push($where_or_flag, "a.is_head <> 1");
                        } elseif ($nv2 == "a") {
                            array_push($where_or_flag, "a.is_special <> 1");
                        } elseif ($nv2 == "j") {
                            array_push($where_or_flag, "a.is_jump <> 1");
                        } elseif ($nv2 == "p") {
                            array_push($where_or_flag, "a.is_litpic <> 1");
                        } elseif ($nv2 == "b") {
                            array_push($where_or_flag, "a.is_b <> 1");
                        } elseif ($nv2 == "s") {
                            array_push($where_or_flag, "a.is_slide <> 1");
                        } elseif ($nv2 == "r") {
                            array_push($where_or_flag, "a.is_roll <> 1");
                        } elseif ($nv2 == "d") {
                            array_push($where_or_flag, "a.is_diyattr <> 1");
                        }
                    }
                    if (!empty($where_or_flag)) {
                        $where_flag_str = " (".implode(" OR ", $where_or_flag).") ";
                        array_push($condition, $where_flag_str);
                    }
                } elseif ($key == 'keyword' && !empty($param[$key])) {
                    $keyword = str_replace('，', ',', $param[$key]);
                    $keywordArr = explode(',', $keyword);
                    $keywordArr = array_unique($keywordArr); // 去重
                    foreach ($keywordArr as $_k => $_v) {
                        $_v = trim($_v);
                        if (empty($_v)) {
                            unset($keywordArr[$_k]);
                            break;
                        } else {
                            $keywordArr[$_k] = addslashes($_v);
                        }
                    }
                    $keyword = implode('|', $keywordArr);
                    $condition[] = Db::raw(" CONCAT(a.title,a.seo_keywords) REGEXP '$keyword' ");
                } else {
                    array_push($condition, "a.{$key} = '".$param[$key]."'");
                }
            }
        }

        // 默认查询条件
        array_push($condition, "a.arcrank > -1");
        array_push($condition, "a.status = 1");
        array_push($condition, "a.is_del = 0");

        // 定时文档显示插件
        if (is_dir('./weapp/TimingTask/')) {
            $TimingTaskRow = model('Weapp')->getWeappList('TimingTask');
            if (!empty($TimingTaskRow['status']) && 1 == $TimingTaskRow['status']) {
                array_push($condition, "a.add_time <= ".getTime()); // 只显当天或之前的文档
            }
        }

        // 处理拼接查询条件
        $where_str = 0 < count($condition) ? implode(" AND ", $condition) : "";
        
        // 给排序字段加上表别名
        $orderby = getOrderBy($orderby, $orderway, true);
        
        // 获取排序信息 --- 陈风任
        $orderby = $this->GetSortData($orderby, $paramNew);
        
        // 用于arclist标签的分页
        if (0 < $pagesize) {
            $tag['typeid'] = $typeid;
            isset($tag['channelid']) && $tag['channelid'] = $channeltype;
            $tagidmd5 = $this->attDef($tag); // 进行tagid的默认处理
        }

        // 获取查询的控制器名
        $channeltype_info = model('Channeltype')->getInfo($channeltype);
        $controller_name = $channeltype_info['ctl_name'];
        $channeltype_table = $channeltype_info['table'];
        
        // 是否显示会员权限
        $users_level_list = $users_level_list2 = [];
        if ('on' == $arcrank || stristr(','.$addfields.',', ',arc_level_name,')) {
            $users_level_list = Db::name('users_level')->field('level_id,level_name,level_value')->where('lang',$this->home_lang)->order('is_system desc, level_value asc')->getAllWithIndex('level_value');
            if (stristr(','.$addfields.',', ',arc_level_name,')) {
                $users_level_list2 = convert_arr_key($users_level_list, 'level_id');
            }
        }

        // 查询数据处理
        $aidArr = array();
        $addtableName = ''; // 附加字段的数据表名
        switch ($channeltype) {
            case '-1':
            {
                break;
            }
            
            default:
            {
                $field = "b.*, a.*";
                $result = $this->archives_db
                    ->field($field)
                    ->alias('a')
                    ->join('__ARCTYPE__ b', 'b.id = a.typeid', 'LEFT')
                    ->where($where_str)
                    ->where('a.lang', $this->home_lang)
                    ->orderRaw($orderby)
                    ->limit($limit)
                    ->select();
                $querysql = $this->archives_db->getLastSql(); // 用于arclist标签的分页
                // if ('rand()' == $orderby) {
                //     $result = $this->archives_db
                //         ->field("b.*, a.*")
                //         ->alias('a')
                //         ->join('__ARCTYPE__ b', 'b.id = a.typeid', 'LEFT')
                //         ->where($where_str)
                //         ->where('a.lang', $this->home_lang)
                //         ->limit(500)
                //         ->select();
                //     shuffle($result);
                //     $result = array_slice($result, 1, 10);
                // } else {
                //     $result = $this->archives_db
                //         ->field($field)
                //         ->alias('a')
                //         ->join('__ARCTYPE__ b', 'b.id = a.typeid', 'LEFT')
                //         ->where($where_str)
                //         ->where('a.lang', $this->home_lang)
                //         ->orderRaw($orderby)
                //         ->limit($limit)
                //         ->select();
                // }
                // $querysql = $this->archives_db->getLastSql(); // 用于arclist标签的分页

                foreach ($result as $key => $val) {
                    array_push($aidArr, $val['aid']); // 收集文档ID

                    // 栏目链接
                    if ($val['is_part'] == 1) {
                        $val['typeurl'] = $val['typelink'];
                    } else {
                        $val['typeurl'] = typeurl('home/'.$controller_name."/lists", $val);
                    }

                    // 文档链接
                    if ($val['is_jump'] == 1) {
                        $val['arcurl'] = $val['jumplinks'];
                    } else {
                        $val['arcurl'] = arcurl('home/'.$controller_name.'/view', $val);
                    }

                    // 封面图
                    $val['litpic'] = get_default_pic($val['litpic']);
                    if ('on' == $thumb) {
                        $val['litpic'] = thumb_img($val['litpic']);
                    }

                    // 是否显示会员权限
                    !isset($val['level_name']) && $val['level_name'] = $val['arcrank'];
                    !isset($val['level_value']) && $val['level_value'] = 0;
                    if ('on' == $arcrank) {
                        if (!empty($users_level_list[$val['arcrank']])) {
                            $val['level_name'] = $users_level_list[$val['arcrank']]['level_name'];
                            $val['level_value'] = $users_level_list[$val['arcrank']]['level_value'];
                        } else if (empty($val['arcrank'])) {
                            $firstUserLevel = current($users_level_list);
                            $val['level_name'] = $firstUserLevel['level_name'];
                            $val['level_value'] = $firstUserLevel['level_value'];
                        }
                    }
                    
                    // 显示下载权限
                    if (!empty($users_level_list2)) {
                        $val['arc_level_name'] = !empty($users_level_list2[$val['arc_level_id']]) ? $users_level_list2[$val['arc_level_id']]['level_name'] : '不限会员';
                    }

                    $result[$key] = $val;
                }

                /*附加表*/
                if (5 == $channeltype) {
                    $addtableName = $channeltype_table.'_content';
                    $addfields .= ',courseware,courseware_free,total_duration,total_video';
                    $addfields = str_replace('，', ',', $addfields); // 替换中文逗号
                    $addfields = trim($addfields, ',');
                    /*过滤不相关的字段*/
                    $addfields_arr = explode(',', $addfields);
                    $addfields_arr = array_unique($addfields_arr);
                    $extFields = Db::name($addtableName)->getTableFields();
                    $addfields_arr = array_intersect($addfields_arr, $extFields);
                    if (!empty($addfields_arr) && is_array($addfields_arr)) {
                        $addfields = implode(',', $addfields_arr);
                    } else {
                        $addfields = '';
                    }
                    /*end*/
                    !empty($addfields) && $addfields = ','.$addfields;
                    $resultExt = M($addtableName)->field("aid {$addfields}")->where('aid','in',$aidArr)->getAllWithIndex('aid');
                    /*自定义字段的数据格式处理*/
                    $resultExt = $this->fieldLogic->getChannelFieldList($resultExt, $channeltype, true);
                    /*--end*/
                    foreach ($result as $key => $val) {
                        $valExt = !empty($resultExt[$val['aid']]) ? $resultExt[$val['aid']] : array();
                        $val = array_merge($valExt, $val);
                        isset($val['total_duration']) && $val['total_duration'] = gmSecondFormat($val['total_duration'], ':');
                        $result[$key] = $val;
                    }
                } else if (!empty($addfields) && !empty($aidArr)) {
                    $addtableName = $channeltype_table.'_content';
                    $addfields = str_replace('，', ',', $addfields); // 替换中文逗号
                    $addfields = trim($addfields, ',');
                    /*过滤不相关的字段*/
                    $addfields_arr = explode(',', $addfields);
                    $extFields = Db::name($addtableName)->getTableFields();
                    $addfields_arr = array_intersect($addfields_arr, $extFields);
                    if (!empty($addfields_arr) && is_array($addfields_arr)) {
                        $addfields = implode(',', $addfields_arr);
                    } else {
                        $addfields = '';
                    }
                    /*end*/
                    !empty($addfields) && $addfields = ','.$addfields;
                    $resultExt = M($addtableName)->field("aid {$addfields}")->where('aid','in',$aidArr)->getAllWithIndex('aid');
                    /*自定义字段的数据格式处理*/
                    $resultExt = $this->fieldLogic->getChannelFieldList($resultExt, $channeltype, true);
                    /*--end*/
                    foreach ($result as $key => $val) {
                        $valExt = !empty($resultExt[$val['aid']]) ? $resultExt[$val['aid']] : array();
                        $val = array_merge($valExt, $val);
                        $result[$key] = $val;
                    }
                }
                /*--end*/
                break;
            }
        }

        // 分页特殊处理
        if (false !== $tagidmd5 && 0 < $pagesize) {
            $arcmulti_db = \think\Db::name('arcmulti');
            $arcmultiRow = $arcmulti_db->field('tagid')->where(['tagid'=>$tagidmd5])->find();
            $attstr = addslashes(serialize($tag)); //记录属性,以便分页样式统一调用
            if (empty($arcmultiRow)) {
                $arcmulti_db->insert([
                    'tagid' => $tagidmd5,
                    'tagname' => 'arclist',
                    'innertext' => '',
                    'pagesize' => $pagesize,
                    'querysql' => $querysql,
                    'ordersql' => $orderby,
                    'addfieldsSql' => $addfields,
                    'addtableName' => $addtableName,
                    'attstr' => $attstr,
                    'add_time' => getTime(),
                    'update_time' => getTime(),
                ]);
            } else {
                $arcmulti_db->where([
                    'tagid' => $tagidmd5,
                    'tagname' => 'arclist',
                ])->update([
                    'innertext' => '',
                    'pagesize' => $pagesize,
                    'querysql' => $querysql,
                    'ordersql' => $orderby,
                    'addfieldsSql' => $addfields,
                    'addtableName' => $addtableName,
                    'attstr' => $attstr,
                    'update_time' => getTime(),
                ]);
            }
        }

        $data = [
            'tag' => $tag,
            'list' => $result,
        ];
        return $data;
    }
    
    // 排序处理
    private function GetSortData($orderby = '', $Param = [])
    {
        if (!empty($Param['sort']) && 'sales' == $Param['sort']) {
            $orderby = 'a.sales_num desc, ' . $orderby;
        } else if (!empty($Param['sort']) && 'price' == $Param['sort']) {
            $orderby = 'a.users_price ' . $Param['sort_asc'] . ', ' . $orderby;
        } else if (!empty($Param['sort']) && 'appraise' == $Param['sort']) {
            $orderby = 'a.appraise desc, ' . $orderby;
        } else if (!empty($Param['sort']) && 'new' == $Param['sort']) {
            $orderby = 'a.add_time desc, ' . $orderby;
        } else if (!empty($Param['sort']) && 'collection' == $Param['sort']) {
            $orderby = 'a.collection desc, ' . $orderby;
        } else if (!empty($Param['sort']) && 'click' == $Param['sort']) {
            $orderby = 'a.click desc, ' . $orderby;
        } else if (!empty($Param['sort']) && 'download' == $Param['sort']) {
            $orderby = 'a.downcount desc, ' . $orderby;
        }
        return $orderby;
    }

    // 生成hash唯一串
    private function attDef($tag)
    {
        $tagmd5 = md5(serialize($tag));
        if (!empty($tag['tagid'])) {
            $tagidmd5 = $tag['tagid'].'_'.$tagmd5;
        } else {
            $tagidmd5 = false;
            // $tagidmd5 = 'arclist_'.$tagmd5;
        }

        return $tagidmd5;
    }
}