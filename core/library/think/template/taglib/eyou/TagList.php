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

use think\Page;
use think\Request;
use app\home\logic\FieldLogic;
use think\Db;
use think\Config;

/**
 * 文章分页列表
 */
class TagList extends Base
{
    public $fieldLogic;
    public $url_screen_var;
    
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->fieldLogic = new FieldLogic();
        /*应用于文档列表*/
        if ($this->aid > 0) {
            $this->tid = M('archives')->where('aid', $this->aid)->getField('typeid');
        }
        /*--end*/

        // 定义筛选标识
        $this->url_screen_var = config('global.url_screen_var');
    }

    /**
     * 获取分页列表
     * @author wengxianhu by 2018-4-20
     */
    public function getList($param = array(), $pagesize = 10, $orderby = '', $addfields = '', $orderway = '', $thumb = '', $arcrank = '')
    {
        $module_name_tmp = strtolower(request()->module());
        $ctl_name_tmp = strtolower(request()->controller());
        $action_name_tmp = strtolower(request()->action());
        empty($orderway) && $orderway = 'desc';

        /*自定义字段筛选*/
        $url_screen_var = input('param.'.$this->url_screen_var.'/d');
        if (1 == $url_screen_var) {
            return $this->GetFieldScreeningList($param,$pagesize, $orderby, $addfields, $orderway, $thumb, $arcrank);
        }
        /*--end*/

        /*搜索、标签搜索*/
        if (in_array($ctl_name_tmp, array('search','tags'))) {
            return $this->getSearchList($pagesize, $orderby, $addfields, $orderway, $thumb, $arcrank);
        }
        /*--end*/

        $result = false;

        $channeltype = ("" != $param['channel'] && is_numeric($param['channel'])) ? intval($param['channel']) : '';
        $param['typeid'] = !empty($param['typeid']) ? $param['typeid'] : $this->tid;

        if (!empty($param['typeid'])) {
            if (!preg_match('/^\d+([\d\,]*)$/i', $param['typeid'])) {
                echo '标签list报错：typeid属性值语法错误，请正确填写栏目ID。';
                return false;
            }

            // 过滤typeid中含有空值的栏目ID
            $typeidArr_tmp = explode(',', $param['typeid']);
            $typeidArr_tmp = array_unique($typeidArr_tmp);
            foreach($typeidArr_tmp as $k => $v){   
                if (empty($v)) unset($typeidArr_tmp[$k]);  
            }
            $param['typeid'] = implode(',', $typeidArr_tmp);
            // end
            
            /*多语言*/
            $param['typeid'] = model('LanguageAttr')->getBindValue($param['typeid'], 'arctype');
            if (empty($param['typeid'])) {
                echo '标签list报错：找不到与第一套【'.$this->main_lang.'】语言关联绑定的属性 typeid 值。';
                return false;
            }
            /*--end*/
        }

        $typeid = $param['typeid'];
        
        /*不指定模型ID、栏目ID，默认显示所有可以发布文档的模型ID下的文档*/
        if (("" === $channeltype && empty($typeid)) || 0 === $channeltype) {
            $allow_release_channel = config('global.allow_release_channel');
            $channeltype = $param['channel'] = implode(',', $allow_release_channel);
        }
        /*--end*/

        // 如果指定了频道ID，则频道下的所有文档都展示
        if (!empty($channeltype)) { // 优先展示模型下的文章
            unset($param['typeid']);
        } elseif (!empty($typeid)) { // 其次展示栏目下的文章
            // unset($param['channel']);
            $typeidArr = explode(',', $typeid);
            if (count($typeidArr) == 1) {
                $typeid = intval($typeid);
                $channel_info = M('Arctype')->field('id,current_channel')->where(array('id'=>$typeid))->find();
                if (empty($channel_info)) {
                    echo '标签list报错：指定属性 typeid 的栏目ID不存在。';
                    return false;
                }
                $channeltype = !empty($channel_info) ? $channel_info["current_channel"] : '';

                /*获取当前栏目下的同模型所有子孙栏目*/
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
/*                $firstTypeid = $typeidArr[0];
                $firstTypeid = M('Arctype')->where(array('id|dirname'=>array('eq', $firstTypeid)))->getField('id');
                $channeltype = M('Arctype')->where(array('id'=>array('eq', $firstTypeid)))->getField('current_channel');*/
            }
            $param['channel'] = $channeltype;
        } else { // 再次展示控制器对应的模型文章
            $controller_name = request()->controller();
            $channeltype_info = model('Channeltype')->getInfoByWhere(array('ctl_name'=>$controller_name), 'id');
            if (!empty($channeltype_info)) {
                $channeltype = $channeltype_info['id'];
                $param['channel'] = $channeltype;
            }
        }
        
/*        if (empty($typeid) && empty($channeltype)) {
            echo '标签list报错：至少指定属性 typeid | channelid 任何一个。';
            return $result;
        }*/

        // 查询条件
        $condition = array();
        foreach (array('keywords','keyword','typeid','notypeid','flag','noflag','channel') as $key) {
            if (isset($param[$key]) && $param[$key] !== '') {
                if ($key == 'keywords') {
                    array_push($condition, "a.title LIKE '%{$param[$key]}%'");
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
                } elseif ($key == 'channel') {
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
                } else {
                    array_push($condition, "a.{$key} = '".$param[$key]."'");
                }
            }
        }
        array_push($condition, "a.arcrank > -1");
        array_push($condition, "a.status = 1");
        array_push($condition, "a.is_del = 0"); // 回收站功能
        /*定时文档显示插件*/
        if (is_dir('./weapp/TimingTask/')) {
            $TimingTaskRow = model('Weapp')->getWeappList('TimingTask');
            if (!empty($TimingTaskRow['status']) && 1 == $TimingTaskRow['status']) {
                array_push($condition, "a.add_time <= ".getTime()); // 只显当天或之前的文档
            }
        }
        /*end*/
        
        $where_str = "";
        if (0 < count($condition)) {
            $where_str = implode(" AND ", $condition);
        }
        // 给排序字段加上表别名
        $orderby = getOrderBy($orderby,$orderway);

        // 获取排序信息 --- 陈风任
        $orderby = $this->GetSortData($orderby);

        // 是否显示会员权限
        $users_level_list = $users_level_list2 = [];
        if ('on' == $arcrank || stristr(','.$addfields.',', ',arc_level_name,')) {
            $users_level_list = Db::name('users_level')->field('level_id,level_name,level_value')->where('lang',$this->home_lang)->order('is_system desc, level_value asc')->getAllWithIndex('level_value');
            if (stristr(','.$addfields.',', ',arc_level_name,')) {
                $users_level_list2 = convert_arr_key($users_level_list, 'level_id');
            }
        }

        // 获取查询的表名
        $channeltype_info = model('Channeltype')->getInfo($channeltype);
        $controller_name = $channeltype_info['ctl_name'];
        $channeltype_table = $channeltype_info['table'];

        switch ($channeltype) {
            case '-1':
            {
                break;
            }
            
            default:
            {
                $list = array();
                $query_get = array();

                /*列表分页URL问号的查询部分*/
                $get_arr = input('get.');
                foreach ($get_arr as $key => $val) {
                    if (empty($val) || stristr($key, '/')) {
                        unset($get_arr[$key]);
                    }
                }
                $param_arr = input('param.');
                foreach ($param_arr as $key => $val) {
                    if (empty($val) || stristr($key, '/')) {
                        unset($param_arr[$key]);
                    }
                }
                $seo_pseudo = tpCache('seo.seo_pseudo');
                if ($seo_pseudo == 1) { // 动态URL模式
                    $query_get = $get_arr;
                } elseif ($seo_pseudo == 3) { // 伪静态URL模式
                    $uiset = input('param.uiset/s', 'off');
                    $uiset = trim($uiset, '/');
                    if ($uiset == 'on') {// 装修模式下的分类URL
                        $query_get = $get_arr;
                    } else {
                        /*正常模式下的分页链接*/
/*                        $diff_arr = array_diff_key($param_arr, $get_arr);
                        if (empty($diff_arr)) {
                            $query_get = array();
                        } else {
                            $query_get = $get_arr;
                        }*/
                        /*--end*/
                        $query_get = array(
                            'typeid_tmp'   => $this->tid,
                        );
                    }
                } elseif ($seo_pseudo == 2) {
                    $query_get = $get_arr;
                }
                /*--end*/

                $paginate_type = config('paginate.type');
                if (isMobile()) {
                    $paginate_type = 'mobile';
                }
                $paginate = array(
                    'type'  => $paginate_type,
                    'var_page' => config('paginate.var_page'),
                    'query' => $query_get,
                );
                $field = "b.*, a.*";
                $pages = Db::name('archives')
                    ->field($field)
                    ->alias('a')
                    ->join('__ARCTYPE__ b', 'b.id = a.typeid', 'LEFT')
                    ->where($where_str)
                    ->where('a.lang', $this->home_lang)
                    ->orderRaw($orderby)
                    ->paginate($pagesize, false, $paginate);

                $page = input('param.page/d');
                if ($page > 1 && $page > $pages->lastPage()) {
                    abort(404);
                }

                // $cacheKey = strtolower('taglist_lastPage_'.$module_name_tmp.$ctl_name_tmp.$action_name_tmp.$this->tid);
                // cache($cacheKey, $pages->lastPage()); // 用于静态页面的分页生成

                $aidArr = array();
                foreach ($pages->items() as $key => $val) {
                    /*获取指定路由模式下的URL*/
                    if ($val['is_part'] == 1) {
                        $val['typeurl'] = $val['typelink'];
                    } else {
                        $val['typeurl'] = typeurl('home/'.$controller_name."/lists", $val);
                    }
                    /*--end*/
                    /*文档链接*/
                    if ($val['is_jump'] == 1) {
                        $val['arcurl'] = $val['jumplinks'];
                    } else {
                        $val['arcurl'] = arcurl('home/'.$controller_name.'/view', $val);
                    }
                    /*--end*/
                    /*封面图*/
/*                    if (empty($val['litpic'])) {
                        $val['is_litpic'] = 0; // 无封面图
                    } else {
                        $val['is_litpic'] = 1; // 有封面图
                    }*/
                    $val['litpic'] = get_default_pic($val['litpic']); // 默认封面图
                    if ('on' == $thumb) { // 属性控制是否使用缩略图
                        $val['litpic'] = thumb_img($val['litpic']);
                    }
                    /*--end*/

                    /*是否显示会员权限*/
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
                    /*--end*/
                    
                    /*显示下载权限*/
                    if (!empty($users_level_list2)) {
                        $val['arc_level_name'] = !empty($users_level_list2[$val['arc_level_id']]) ? $users_level_list2[$val['arc_level_id']]['level_name'] : '不限会员';
                    }
                    /*end*/
                    
                    $list[$key] = $val;

                    array_push($aidArr, $val['aid']); // 文档ID数组
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
                    foreach ($list as $key => $val) {
                        $valExt = !empty($resultExt[$val['aid']]) ? $resultExt[$val['aid']] : array();
                        $val = array_merge($valExt, $val);
                        $val['total_duration'] = gmSecondFormat($val['total_duration'], ':');
                        $list[$key] = $val;
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
                    foreach ($list as $key => $val) {
                        $valExt = !empty($resultExt[$val['aid']]) ? $resultExt[$val['aid']] : array();
                        $val = array_merge($valExt, $val);
                        $list[$key] = $val;
                    }
                }
                /*--end*/

                /*针对下载列表*/
                if (!empty($aidArr) && strtolower($controller_name) == 'download') {
                    $downloadRow = M('download_file')->where(array('aid'=>array('IN', $aidArr)))
                        ->order('aid asc, sort_order asc')
                        ->select();
                    $downloadFileArr = array();
                    if (!empty($downloadRow)) {
                        /*获取指定文档ID的下载文件列表*/
                        foreach ($downloadRow as $key => $val) {
                            if (!isset($downloadFileArr[$val['aid']]) || empty($downloadFileArr[$val['aid']])) {
                                $downloadFileArr[$val['aid']] = array();
                            }
                            $val['downurl'] = ROOT_DIR."/index.php?m=home&c=View&a=downfile&id={$val['file_id']}&uhash={$val['uhash']}&lang={$this->home_lang}";
                            $downloadFileArr[$val['aid']][] = $val;
                        }
                        /*--end*/
                    }
                    /*将组装好的文件列表与文档相关联*/
                    foreach ($list as $key => $val) {
                        $list[$key]['file_list'] = !empty($downloadFileArr[$val['aid']]) ? $downloadFileArr[$val['aid']] : array();
                    }
                    /*--end*/
                }
                /*--end*/

                $result['pages'] = $pages; // 分页显示输出
                $result['list'] = $list; // 赋值数据集

                break;
            }
        }

        return $result;
    }

    /**
     * 获取搜索分页列表
     * @author wengxianhu by 2018-4-20
     */
    public function getSearchList($pagesize = 10, $orderby = '', $addfields = '', $orderway = '', $thumb = '', $arcrank = '')
    {
        $result = false;
        empty($orderway) && $orderway = 'desc';

        $condition = array();
        // 获取到所有URL参数
        $param = input('param.');

        if (strtolower(request()->controller()) == 'tags') {
            $tag = input('param.tag/s', '');
            $tagid = input('param.tagid/d', 0);
            if (!empty($tag)) {
                $tagidArr = M('tagindex')->where(array('tag'=>array('LIKE', "%{$tag}%")))->column('id', 'id');
                $aidArr = M('taglist')->field('aid')->where(array('tid'=>array('in', $tagidArr)))->column('aid', 'aid');
                $condition['aid'] = array('in', $aidArr);
            } elseif ($tagid > 0) {
                $aidArr = M('taglist')->field('aid')->where(array('tid'=>array('eq', $tagid)))->column('aid', 'aid');
                $condition['aid'] = array('in', $aidArr);
            }
        }

        // 应用搜索条件
        foreach (['keywords','keyword','typeid','notypeid','channelid','flag','noflag'] as $key) {
            if (isset($param[$key]) && $param[$key] !== '') {
                if ('keywords' == $key) {
                    $keywords = trim($param[$key]);
                    $condition['a.title'] = array('LIKE', "%{$keywords}%");
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
                } else if ('typeid' == $key) {
                    $search_type = input('param.type/s', 'default');
                    $param[$key] = str_replace('，', ',', $param[$key]);
                    $param[$key] = preg_replace('/([^0-9,])/i', '', $param[$key]);
                    if (stristr($param[$key], ',')) { // 指定多个栏目ID
                        $typeids = explode(',', $param[$key]);
                        if ('sonself' == $search_type) { // 当前栏目以及下级栏目
                            $current_channel_arr = Db::name('arctype')->where(['id'=>['IN', $typeids], 'is_del'=>0])->column('current_channel');
                            foreach ($typeids as $_k => $_v) {
                                $childrenRow = model('Arctype')->getHasChildren($_v);
                                foreach ($childrenRow as $k2 => $v2) {
                                    if (in_array($v2['current_channel'], $current_channel_arr)) {
                                        array_push($typeids, $v2['id']);
                                    }
                                }
                            }
                        }
                    } else {
                        if ('default' == $search_type) { // 默认只检索指定的栏目ID，不涉及下级栏目
                            $typeids = [$param[$key]];
                        } else if ('sonself' == $search_type) { // 当前栏目以及下级栏目
                            $arctype_info = Db::name('arctype')->field('id,current_channel')->where(['id'=>['eq', $param[$key]], 'is_del'=>0])->find();
                            $childrenRow = model('Arctype')->getHasChildren($param[$key]);
                            foreach ($childrenRow as $k2 => $v2) {
                                if ($arctype_info['current_channel'] != $v2['current_channel']) {
                                    unset($childrenRow[$k2]); // 排除不是同一模型的栏目
                                }
                            }
                            $typeids = get_arr_column($childrenRow, 'id');
                        }
                    }
                    $condition['a.typeid'] = ['IN', $typeids];
                } elseif ($key == 'channelid') {
                    $condition['a.channel'] = array('eq', $param[$key]);
                } elseif ($key == 'notypeid') {
                    $param[$key] = str_replace('，', ',', $param[$key]);
                    $param[$key] = preg_replace('/([^0-9,])/i', '', $param[$key]);
                    $notypeids = explode(',', $param[$key]);
                    $condition['a.typeid'] = ['NOT IN', $notypeids];
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
                        $condition[] = Db::raw($where_flag_str);
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
                        $condition[] = Db::raw($where_flag_str);
                    }
                } else {
                    $condition['a.'.$key] = array('eq', $param[$key]);
                }
            }
        }
        $condition['a.lang'] = array('eq', $this->home_lang);
        $condition['a.arcrank'] = array('gt', -1);
        $condition['a.status'] = array('eq', 1);
        $condition['a.is_del'] = array('eq', 0); // 回收站功能
        /*定时文档显示插件*/
        if (is_dir('./weapp/TimingTask/')) {
            $TimingTaskRow = model('Weapp')->getWeappList('TimingTask');
            if (!empty($TimingTaskRow['status']) && 1 == $TimingTaskRow['status']) {
                $condition['a.add_time'] = array('elt', getTime()); // 只显当天或之前的文档
            }
        }
        /*end*/

        // 给排序字段加上表别名
        $orderby = getOrderBy($orderby,$orderway);

        // 获取排序信息 --- 陈风任
        $orderby = $this->GetSortData($orderby);

        // 是否显示会员权限
        $users_level_list = $users_level_list2 = [];
        if ('on' == $arcrank || stristr(','.$addfields.',', ',arc_level_name,')) {
            $users_level_list = Db::name('users_level')->field('level_id,level_name,level_value')->where('lang',$this->home_lang)->order('is_system desc, level_value asc')->getAllWithIndex('level_value');
            if (stristr(','.$addfields.',', ',arc_level_name,')) {
                $users_level_list2 = convert_arr_key($users_level_list, 'level_id');
            }
        }

        /**
         * 数据查询，搜索出主键ID的值
         */
        $list = array();
        $query_get = input('get.');
        unset($query_get['s']);
        if (strtolower(request()->controller()) == 'tags') {
            $query_get['tagid'] = $tagid;
        }

        $paginate_type = config('paginate.type');
        if (isMobile()) {
            $paginate_type = 'mobile';
        }
        $paginate = array(
            'type'  => $paginate_type,
            'var_page' => config('paginate.var_page'),
            'query' => $query_get,
        );
        $pages = Db::name('archives')
            ->field("a.aid")
            ->alias('a')
            ->join('__ARCTYPE__ b', 'b.id = a.typeid', 'LEFT')
            ->where('a.channel','NOT IN',[6]) // 排除单页
            ->where($condition)
            ->order($orderby)
            ->paginate($pagesize, false, $paginate);

        $page = input('param.page/d');
        if ($page > 1 && $page > $pages->lastPage()) {
            abort(404);
        }

        /**
         * 完善数据集信息
         * 在数据量大的情况下，经过优化的搜索逻辑，先搜索出主键ID，再通过ID将其他信息补充完整；
         */
        if ($pages->total() > 0) {
            $list = $pages->items();
            $aids = get_arr_column($list, 'aid');
            $fields = "b.*, a.*";
            $row = Db::name('archives')
                ->field($fields)
                ->alias('a')
                ->join('__ARCTYPE__ b', 'a.typeid = b.id', 'LEFT')
                ->where('a.aid', 'in', $aids)
                ->getAllWithIndex('aid');
            // 获取模型对应的控制器名称
            $channel_list = model('Channeltype')->getAll('id, ctl_name', array(), 'id');

            foreach ($list as $key => $val) {
                $arcval = $row[$val['aid']];
                $controller_name = $channel_list[$arcval['channel']]['ctl_name'];
                /*获取指定路由模式下的URL*/
                if ($arcval['is_part'] == 1) {
                    $arcval['typeurl'] = $arcval['typelink'];
                } else {
                    $arcval['typeurl'] = typeurl('home/'.$controller_name."/lists", $arcval);
                }
                /*--end*/
                /*文档链接*/
                if ($arcval['is_jump'] == 1) {
                    $arcval['arcurl'] = $arcval['jumplinks'];
                } else {
                    $arcval['arcurl'] = arcurl('home/'.$controller_name."/view", $arcval);
                }
                /*--end*/
                /*封面图*/
                /*if (empty($arcval['litpic'])) {
                    $arcval['is_litpic'] = 0; // 无封面图
                } else {
                    $arcval['is_litpic'] = 1; // 有封面图
                }*/
                $arcval['litpic'] = get_default_pic($arcval['litpic']); // 默认封面图
                if ('on' == $thumb) { // 属性控制是否使用缩略图
                    $arcval['litpic'] = thumb_img($arcval['litpic']);
                }
                /*--end*/

                /*是否显示会员权限*/
                !isset($arcval['level_name']) && $arcval['level_name'] = $arcval['arcrank'];
                !isset($arcval['level_value']) && $arcval['level_value'] = 0;
                if ('on' == $arcrank) {
                    if (!empty($users_level_list[$arcval['arcrank']])) {
                        $arcval['level_name'] = $users_level_list[$arcval['arcrank']]['level_name'];
                        $arcval['level_value'] = $users_level_list[$arcval['arcrank']]['level_value'];
                    } else if (empty($arcval['arcrank'])) {
                        $firstUserLevel = current($users_level_list);
                        $arcval['level_name'] = $firstUserLevel['level_name'];
                        $arcval['level_value'] = $firstUserLevel['level_value'];
                    }
                }
                /*--end*/
                    
                /*显示下载权限*/
                if (!empty($users_level_list2)) {
                    $arcval['arc_level_name'] = !empty($users_level_list2[$arcval['arc_level_id']]) ? $users_level_list2[$arcval['arc_level_id']]['level_name'] : '不限会员';
                }
                /*end*/

                $list[$key] = $arcval;
            }

            /*附加表*/
            if (!empty($addfields) && !empty($list)) {
                $channeltypeRow = model('Channeltype')->getAll('id,table', [], 'id'); // 模型对应数据表
                $channelGroupRow = group_same_key($list, 'current_channel'); // 模型下的文档集合
                foreach ($channelGroupRow as $channelid => $tmp_list) {
                    $addtableName = ''; // 附加字段的数据表名
                    $tmp_aid_arr = get_arr_column($tmp_list, 'aid');
                    $channeltype_table = $channeltypeRow[$channelid]['table']; // 每个模型对应的数据表
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
                    $resultExt = M($addtableName)->field("aid {$addfields}")->where('aid','in',$tmp_aid_arr)->getAllWithIndex('aid');
                    /*自定义字段的数据格式处理*/
                    $resultExt = $this->fieldLogic->getChannelFieldList($resultExt, $channelid, true);
                    /*--end*/
                    foreach ($list as $key2 => $val2) {
                        $valExt = !empty($resultExt[$val2['aid']]) ? $resultExt[$val2['aid']] : array();
                        $val2 = array_merge($valExt, $val2);
                        $list[$key2] = $val2;
                    }
                }
            }
            /*--end*/
        }
        $result['pages'] = $pages; // 分页显示输出
        $result['list'] = $list; // 赋值数据集

        return $result;
    }


    /**
     * 获取筛选分页列表
     * @author 陈风任 by 2019-6-11
     */
    public function GetFieldScreeningList($param = array(),$pagesize = 10, $orderby = '', $addfields = '', $orderway = '', $thumb = '', $arcrank = '')
    {
        $result = false;
        empty($orderway) && $orderway = 'desc';

        $condition = array();
        // 获取到所有URL参数
        $param_new = input('param.');

        if (strtolower(request()->controller()) == 'tags') {
            $tag = input('param.tag/s', '');
            $tagid = input('param.tagid/d', 0);
            if (!empty($tag)) {
                $tagidArr = M('tagindex')->where(array('tag'=>array('LIKE', "%{$tag}%")))->column('id', 'id');
                $aidArr = M('taglist')->field('aid')->where(array('tid'=>array('in', $tagidArr)))->column('aid', 'aid');
                $condition['aid'] = array('in', $aidArr);
            } elseif ($tagid > 0) {
                $aidArr = M('taglist')->field('aid')->where(array('tid'=>array('eq', $tagid)))->column('aid', 'aid');
                $condition['aid'] = array('in', $aidArr);
            }
        } else{
            /*自定义字段筛选*/
            $where = [
                'is_screening' => 1,
                // 根据需求新增条件
            ];
            // 所有应用于搜索的自定义字段
            $channelfield = Db::name('channelfield')->where($where)->field('channel_id,id,name,dtype,dfvalue')->select();
            // 查询当前栏目所属模型
            $channel_id = Db::name('arctype')->where('id',$param_new['tid'])->getField('current_channel');
            // 所有模型类别
            $channeltype_list = config('global.channeltype_list');
            $channel_table = array_search($channel_id, $channeltype_list);

            // 查询获取aid初始sql语句
            $wheres = [];
            $where_multiple = [];
            foreach ($channelfield as $key => $value) {
                // 值不为空则执行
                $fieldname = $value['name'];
                if (!empty($fieldname) && !empty($param_new[$fieldname])) {
                    // 分割参数，判断多选或单选，拼装sql语句
                    $val_arr  = explode('|', trim($param_new[$fieldname], '|'));
                    if (!empty($val_arr)) {
                        if ('' == $val_arr[0]) {
                            // 选择全部时拼装sql语句
                            // $wheres[$fieldname] = ['NEQ', null];
                        }else{
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
                $condition['a.aid'] = ['IN', get_arr_column($aid_result, "aid")]; // array_push($condition, "a.aid IN (".implode(',', get_arr_column($aid_result, "aid")).")");
            } else {
                $pages = Db::name('archives')->field("aid")->where("aid=0")->paginate($pagesize);
                $result['pages'] = $pages; // 分页显示输出
                $result['list'] = []; // 赋值数据集
                return $result;
            }
            /*结束*/
        }

        // 应用搜索条件
        foreach (['keywords','keyword','typeid','notypeid','channel','flag','noflag'] as $key) {
            if (isset($param[$key]) && $param[$key] !== '') {
                if ('keywords' == $key) {
                    $keywords = trim($param[$key]);
                    $condition['a.title'] = array('LIKE', "%{$keywords}%");
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
                } else if (in_array($key, array('typeid','channel'))) {
                    $param[$key] = str_replace('，', ',', $param[$key]);
                    $condition['a.'.$key] = array('in', $param[$key]);
                } elseif ($key == 'notypeid') {
                    $param[$key] = str_replace('，', ',', $param[$key]);
                    $condition['a.typeid'] = array('not in', $param[$key]);
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
                        $condition[] = Db::raw($where_flag_str);
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
                        $condition[] = Db::raw($where_flag_str);
                    }
                } else {
                    $condition['a.'.$key] = array('eq', $param[$key]);
                }
            }
        }

        // 查询条件拼装
/*        
        array_push($condition, "a.lang = '".$this->home_lang."'");
        array_push($condition, "a.arcrank > -1");
        array_push($condition, "a.status = 1");
        array_push($condition, "a.is_del = 0");// 回收站功能
        //定时文档显示插件
        if (is_dir('./weapp/TimingTask/')) {
            $TimingTaskRow = model('Weapp')->getWeappList('TimingTask');
            if (!empty($TimingTaskRow['status']) && 1 == $TimingTaskRow['status']) {
                array_push($condition, "a.add_time <= ".getTime()); // 只显当天或之前的文档
            }
        }
        //end
*/
        $condition['a.lang'] = $this->home_lang;
        $condition['a.arcrank'] = ['gt', -1]; // 阅读权限
        $condition['a.status'] = 1;
        $condition['a.is_del'] = 0; // 回收站
        /*定时文档显示插件*/
        if (is_dir('./weapp/TimingTask/')) {
            $TimingTaskRow = model('Weapp')->getWeappList('TimingTask');
            if (!empty($TimingTaskRow['status']) && 1 == $TimingTaskRow['status']) {
                $condition['a.add_time'] = ['elt', getTime()]; // 只显当天或之前的文档
            }
        }
        /*end*/

        $seo_pseudo = config('ey_config.seo_pseudo');
        // 是否伪静态下
        if (!isset($param_new[$this->url_screen_var]) && 3 == $seo_pseudo) {
             $arctype_where = [
                'dirname' => $param_new['tid'],
                'lang'    => $this->home_lang,
            ];
            $param_new['tid'] = Db::name('arctype')->where($arctype_where)->getField('id');
        }
        // 最后一级栏目则查询当前栏目数据
        $condition['a.typeid'] = $param_new['tid']; // $TypeIdWhere = "a.typeid = ".$param_new['tid'];
        // 查询栏目是否存在下一级栏目
        // $arctype_ids = Db::name('arctype')->where('parent_id',$param_new['tid'])->field('id')->select();
        $arctype_ids = model('Arctype')->getHasChildren($param_new['tid']);

        if (!empty($arctype_ids)) {
            $arctype_ids = get_arr_column($arctype_ids, "id");
            $field_id = [];
            // 处理得到绑定栏目的ID
            foreach ($param_new as $key => $value) {
                foreach ($channelfield as $kk => $vv) {
                    if ($vv['name'] == $key) {
                        $field_id[] = $vv['id'];
                    }
                }
            }
            // 处理栏目ID
            if (!empty($field_id)) {
                $typeid_arr = Db::name('channelfield_bind')->where('field_id','IN',$field_id)->field('typeid')->select();
                $typeid_arr = array_unique(get_arr_column($typeid_arr, "typeid"));
                $array_new  = array_intersect($typeid_arr, $arctype_ids);
                if (!empty($array_new)) {
                    $typeid_ids = $array_new; // $typeid_ids = '('.implode(',', $array_new).')';
                }else{
                    $typeid_ids = explode(',', $param_new['tid']); // $typeid_ids = '('.$param_new['tid'].')';
                }
            }else{
                $typeid_ids = $arctype_ids; // $typeid_ids = '('.implode(',', $arctype_ids).')';
            }
            $condition['a.typeid'] = ['IN', $typeid_ids]; // $TypeIdWhere = "a.typeid IN ".$typeid_ids;
        }
        // array_push($condition, $TypeIdWhere);

        // 拼装查询所有条件成sql
/*        $condition_str = "";
        if (0 < count($condition)) {
            $condition_str = implode(" AND ", $condition);
        }*/

        // 给排序字段加上表别名
        $orderby = getOrderBy($orderby,$orderway);

        // 获取排序信息 --- 陈风任
        $orderby = $this->GetSortData($orderby);

        // 是否显示会员权限
        $users_level_list = $users_level_list2 = [];
        if ('on' == $arcrank || stristr(','.$addfields.',', ',arc_level_name,')) {
            $users_level_list = Db::name('users_level')->field('level_id,level_name,level_value')->where('lang',$this->home_lang)->order('is_system desc, level_value asc')->getAllWithIndex('level_value');
            if (stristr(','.$addfields.',', ',arc_level_name,')) {
                $users_level_list2 = convert_arr_key($users_level_list, 'level_id');
            }
        }

        /**
         * 数据查询，搜索出主键ID的值
         */
        $list = array();
        $query_get = input('get.');
        $paginate_type = config('paginate.type');
        if (isMobile()) {
            $paginate_type = 'mobile';
        }
        $paginate = array(
            'type'  => $paginate_type,
            'var_page' => config('paginate.var_page'),
            'query' => $query_get,
        );

        $pages = Db::name('archives')
            ->field("a.aid")
            ->alias('a')
            ->join('__ARCTYPE__ b', 'b.id = a.typeid', 'LEFT')
            ->where('a.channel','NOT IN',[6]) // 排除单页
            ->where($condition)
            ->order($orderby)
            ->paginate($pagesize, false, $paginate);

        $page = input('param.page/d');
        if ($page > 1 && $page > $pages->lastPage()) {
            abort(404);
        }
        
        /**
         * 完善数据集信息
         * 在数据量大的情况下，经过优化的搜索逻辑，先搜索出主键ID，再通过ID将其他信息补充完整；
         */
        if ($pages->total() > 0) {
            $list = $pages->items();
            $aids = get_arr_column($list, 'aid');
            $fields = "b.*, a.*";
            $row = Db::name('archives')
                ->field($fields)
                ->alias('a')
                ->join('__ARCTYPE__ b', 'a.typeid = b.id', 'LEFT')
                ->where('a.aid', 'in', $aids)
                ->getAllWithIndex('aid');
            // 获取模型对应的控制器名称
            $channel_list = model('Channeltype')->getAll('id, ctl_name', array(), 'id');

            foreach ($list as $key => $val) {
                $arcval = $row[$val['aid']];
                $controller_name = $channel_list[$arcval['channel']]['ctl_name'];
                /*获取指定路由模式下的URL*/
                if ($arcval['is_part'] == 1) {
                    $arcval['typeurl'] = $arcval['typelink'];
                } else {
                    $arcval['typeurl'] = typeurl('home/'.$controller_name."/lists", $arcval);
                }
                /*--end*/
                /*文档链接*/
                if ($arcval['is_jump'] == 1) {
                    $arcval['arcurl'] = $arcval['jumplinks'];
                } else {
                    $arcval['arcurl'] = arcurl('home/'.$controller_name."/view", $arcval);
                }
                /*--end*/
                /*封面图*/
                if (empty($arcval['litpic'])) {
                    $arcval['is_litpic'] = 0; // 无封面图
                } else {
                    $arcval['is_litpic'] = 1; // 有封面图
                }
                $arcval['litpic'] = thumb_img(get_default_pic($arcval['litpic'])); // 默认封面图
                /*--end*/

                /*是否显示会员权限*/
                !isset($arcval['level_name']) && $arcval['level_name'] = $arcval['arcrank'];
                !isset($arcval['level_value']) && $arcval['level_value'] = 0;
                if ('on' == $arcrank) {
                    if (!empty($users_level_list[$arcval['arcrank']])) {
                        $arcval['level_name'] = $users_level_list[$arcval['arcrank']]['level_name'];
                        $arcval['level_value'] = $users_level_list[$arcval['arcrank']]['level_value'];
                    } else if (empty($arcval['arcrank'])) {
                        $firstUserLevel = current($users_level_list);
                        $arcval['level_name'] = $firstUserLevel['level_name'];
                        $arcval['level_value'] = $firstUserLevel['level_value'];
                    }
                }
                /*--end*/
                    
                /*显示下载权限*/
                if (!empty($users_level_list2)) {
                    $arcval['arc_level_name'] = !empty($users_level_list2[$arcval['arc_level_id']]) ? $users_level_list2[$arcval['arc_level_id']]['level_name'] : '不限会员';
                }
                /*end*/

                $list[$key] = $arcval;
            }

            /*附加表*/
            if (!empty($addfields) && !empty($list)) {
                $channeltypeRow = model('Channeltype')->getAll('id,table', [], 'id'); // 模型对应数据表
                $channelGroupRow = group_same_key($list, 'current_channel'); // 模型下的文档集合
                foreach ($channelGroupRow as $channelid => $tmp_list) {
                    $addtableName = ''; // 附加字段的数据表名
                    $tmp_aid_arr = get_arr_column($tmp_list, 'aid');
                    $channeltype_table = $channeltypeRow[$channelid]['table']; // 每个模型对应的数据表
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
                    $resultExt = M($addtableName)->field("aid {$addfields}")->where('aid','in',$tmp_aid_arr)->getAllWithIndex('aid');
                    /*自定义字段的数据格式处理*/
                    $resultExt = $this->fieldLogic->getChannelFieldList($resultExt, $channelid, true);
                    /*--end*/
                    foreach ($list as $key2 => $val2) {
                        $valExt = !empty($resultExt[$val2['aid']]) ? $resultExt[$val2['aid']] : array();
                        $val2 = array_merge($valExt, $val2);
                        $list[$key2] = $val2;
                    }
                }
            }
            /*--end*/
        }
        $result['pages'] = $pages; // 分页显示输出
        $result['list'] = $list; // 赋值数据集

        return $result;
    }

    // 排序处理
    private function GetSortData($orderby = '')
    {
        $Param = $this->request->param();
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

}