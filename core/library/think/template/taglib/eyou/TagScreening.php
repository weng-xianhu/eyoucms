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
 * Date: 2019-6-5
 */

namespace think\template\taglib\eyou;

use think\Request;
use think\Db;

/**
 * 搜索表单
 */
class TagScreening extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->channelfield_db = Db::name('channelfield');
        $this->dirname = input('param.tid/s');
        $this->tid = 0;
    }

    // URL中隐藏index.php入口文件，此方法仅此控制器使用到
    private function auto_hide_index($url = '', $seo_pseudo = 1)
    {
        if (2 != $seo_pseudo) {
            if (empty($url)) return false;
            // 是否开启去除index.php文件
            $seo_inlet = null;
            $seo_inlet === null && $seo_inlet = config('ey_config.seo_inlet');
            if (1 == $seo_inlet) {
                $url = str_replace('/index.php', '/', $url);
            }
        }
        return $url;
    }

    /**
     * 获取搜索表单
     */
    public function getScreening($currentclass='', $addfields='', $addfieldids='', $alltxt='', $typeid='', $anchor='')
    {
        // if (self::$home_lang != self::$main_lang) return false;

        $param = input('param.');
        // 定义筛选标识
        $url_screen_var = config('global.url_screen_var');
        // 隐藏域参数处理
        $hidden  = '';
        // 是否在伪静态下搜索
        $seo_pseudo = config('ey_config.seo_pseudo');
        if (!isset($param[$url_screen_var]) && 3 == $seo_pseudo && !is_numeric($this->dirname)) {
            $arctype_where = [
                'dirname' => $this->dirname,
                'lang'    => self::$home_lang,
            ];
            $this->tid = Db::name('arctype')->where($arctype_where)->getField('id');
        } else {
            $this->tid = input('param.tid/d');
        }

        if (!empty($typeid)) $this->tid = $typeid;
        
        // 查询数据条件
        $where = [
            'a.is_screening' => 1,
            'a.ifeditable'   => 1,
            'b.typeid'       => $this->tid,
            // 根据需求新增条件
        ];

        // 是否指定参数读取
        if (!empty($addfields)) {
            $addfieldids = '';
            $where['a.name'] = array('IN',$addfields);
        } else if (!empty($addfieldids)) {
            $where['a.id'] = array('IN',$addfieldids);
        }

        // 数据查询
        $row = $this->channelfield_db
            ->field('a.id,a.title,a.name,a.dfvalue,a.dtype,a.set_type,a.channel_id,a.is_multiselect')
            ->alias('a')
            ->join('__CHANNELFIELD_BIND__ b', 'b.field_id = a.id', 'LEFT')
            ->where($where)
            ->order('a.sort_order asc, a.id asc')
            ->select();
        // 特殊地区(中国四个省直辖市)
        $globalFieldRegionType = config('global.field_region_type');
        // Onclick点击事件方法名称加密，防止冲突
        $OnclickScreening  = 'ey_'.md5('OnclickScreening');
        // Onchange改变事件方法名称加密，防止冲突
        $OnchangeScreening = 'ey_'.md5('OnchangeScreening');
        // 定义搜索点击的name值
        $is_data = '';
        // 数据处理输出
        foreach ($row as $key => $value) {
            // 搜索的name值
            $name = $value['name'];
            // 封装onClick事件
            $row[$key]['onClick']  = "onClick='{$OnclickScreening}(this);'";
            // 封装onchange事件
            $row[$key]['onChange'] = "onChange='{$OnchangeScreening}(this);'";
            // 在伪静态下拼装控制器方式参数名
            if (!isset($param[$url_screen_var]) && 3 == $seo_pseudo) {
                $param_query = [];
                $param_query['m'] = 'home';
                $param_query['c'] = 'Lists';
                $param_query['a'] = 'index';
                $param_query['tid'] = $this->tid;
                $param_new = request()->param();
                unset($param_new['tid']);
                $param_query = array_merge($param_query, $param_new);
            } else {
                $param_query = request()->param();
            }
            // 生成静态页面代码
            // 增加对 EYOUCMS_BUILDING_HTML 常量的判断，支持手机后台生成静态
            $is_building_html = defined('EYOUCMS_BUILDING_HTML') && EYOUCMS_BUILDING_HTML === true;
            // 当正在生成静态页面时，需要从数据库重新读取seo_pseudo配置
            // 因为在手机后台生成静态时，config('ey_config.seo_pseudo')可能返回错误的值
            $real_seo_pseudo = $seo_pseudo;
            if ($is_building_html) {
                $real_seo_pseudo = tpCache('seo.seo_pseudo');
            }
            // 条件：静态模式(seo_pseudo=2) 且 (不是手机端 或 正在生成静态)
            // 当正在生成静态时，强制使用静态模式的URL格式
            if ((2 == $real_seo_pseudo && !isMobile()) || $is_building_html) {
                $param_query['m'] = 'home';
                $param_query['c'] = 'Lists';
                $param_query['a'] = 'index';
                unset($param_query['_ajax']);
                unset($param_query['id']);
                unset($param_query['fid']);
                // unset($param_query['lang']);
                // 清理手机后台插件特有的参数，避免生成的URL包含多余参数
                unset($param_query['sm']);
                unset($param_query['sc']);
                unset($param_query['sa']);
                unset($param_query['isMobile']);
                unset($param_query['weappAjax']);
                unset($param_query['index']);
                unset($param_query['findex']);
                unset($param_query['achieve']);
            }

            // 筛选时，去掉url上的页码page参数
            unset($param_query['page']);

            // 筛选值处理
            if ('region' == $value['dtype']) {
                // 类型为区域则执行，处理自定义参数名称
                $region_alltxt = $alltxt;
                if (!empty($region_alltxt)) {
                    // 等于OFF表示关闭，不需要此项
                    if ('off' == $region_alltxt) $region_alltxt = '';
                } else {
                    $region_alltxt = '全部';

                }
                $all = [];
                if (!empty($region_alltxt)) {
                    // 拼装数组
                    $all[0] = [
                        'id'   => '',
                        'name' => $region_alltxt,
                    ];
                }

                // 搜索点击的name值
                $is_data = !empty($param[$name]) ? $param[$name] : $region_alltxt;
                $is_data = explode('_', $is_data);

                $threeLevelArr = [];
                if (!empty($value['set_type']) && !empty($is_data[1]) && is_numeric($is_data[1])) {
                    $where = [
                        'parent_id' => intval($is_data[1])
                    ];
                    $threeLevelArr = Db::name('region')->where($where)->select();
                    if (!empty($threeLevelArr)) {
                        // 封装二级区域筛选链接
                        $threeLevelArr = array_merge($all, $threeLevelArr);
                        // 处理参数输出
                        $subLevelParam = $param_query;
                        foreach ($threeLevelArr as $p_key_1 => $p_val_1) {
                            // 参数拼装URL
                            if (!empty($p_val_1['id'])) {
                                $subLevelParam[$name] = $is_data[0] . '_' . $is_data[1] . '_' . $p_val_1['id'];
                            } else {
                                $subLevelParam[$name] = $is_data[0] . '_' . $is_data[1];
                                $threeLevelArr[$p_key_1]['parent_id'] = intval($is_data[1]);
                            }
                            // 筛选标识始终追加在最后
                            unset($subLevelParam[$url_screen_var]);
                            $subLevelParam[$url_screen_var] = 1;
                            foreach (['index','findex','achieve','s'] as $_uk_1 => $_uv) {
                                if (isset($subLevelParam[$_uk_1])) unset($subLevelParam[$_uk_1]);
                            }
                            // 清理手机后台插件特有的参数
                            foreach (['sm','sc','sa','isMobile','weappAjax'] as $_wk => $_wv) {
                                if (isset($subLevelParam[$_wv])) unset($subLevelParam[$_wv]);
                            }
                            if (!empty($typeid)) {
                                // 存在typeid表示在首页展示
                                foreach (['m','c','a','tid'] as $_uk_2 => $_uv) {
                                    if (isset($subLevelParam[$_uk_2])) unset($subLevelParam[$_uk_2]);
                                }
                                if (empty($subLevelParam['page'])) $subLevelParam['page'] = 1;
                                $url = ROOT_DIR.'/index.php?m=home&c=Lists&a=index&tid='.$typeid.'&'.urlencode(http_build_query($subLevelParam));
                            } else {
                                $url = ROOT_DIR.'/index.php?'.urlencode(http_build_query($subLevelParam));
                            }
                            $url = $this->auto_hide_index(urldecode($url), $seo_pseudo) . $anchor;
                            // dump($url);
                            // 拼装onClick事件
                            $threeLevelArr[$p_key_1]['onClick'] = empty($anchor) ? $row[$key]['onClick']." data-url='{$url}'" : " href='{$url}' ";
                            // 拼装onchange参数
                            $threeLevelArr[$p_key_1]['SelectUrl'] = "data-url='{$url}'";
                            // 初始化参数，默认未选中
                            $threeLevelArr[$p_key_1]['name']         = "{$p_val_1['name']}";
                            $threeLevelArr[$p_key_1]['SelectValue']  = "";
                            $threeLevelArr[$p_key_1]['currentclass'] = $threeLevelArr[$p_key_1]['currentstyle'] = "";
                            // 选中时执行
                            if ($p_val_1['id'] == $is_data[2]) {
                                $threeLevelArr[$p_key_1]['name']         = "<b>{$p_val_1['name']}</b>";
                                $threeLevelArr[$p_key_1]['SelectValue']  = "selected";
                                $threeLevelArr[$p_key_1]['currentclass'] = $threeLevelArr[$p_key_1]['currentstyle'] = $currentclass;
                            } else if ($p_val_1['id'] == $is_data[2] && 0 === intval($p_key_1)) {
                                $threeLevelArr[$p_key_1]['name']         = "<b>{$p_val_1['name']}</b>";
                                $threeLevelArr[$p_key_1]['SelectValue']  = "selected";
                                $threeLevelArr[$p_key_1]['currentclass'] = $threeLevelArr[$p_key_1]['currentstyle'] = $currentclass;
                            } else if ($p_val_1['name'] == $region_alltxt && $is_data[2] == $region_alltxt) {
                                $threeLevelArr[$p_key_1]['name']         = "<b>{$p_val_1['name']}</b>";
                                $threeLevelArr[$p_key_1]['SelectValue']  = "selected";
                                $threeLevelArr[$p_key_1]['currentclass'] = $threeLevelArr[$p_key_1]['currentstyle'] = $currentclass;
                            }
                        }
                        $threeLevelArr = group_same_key($threeLevelArr, 'parent_id');
                        // dump($threeLevelArr);exit;
                    }
                }

                $twoLevelArr = [];
                // 如果是数字则表示已选择某地区，如果已开启三级联动则查询下级城市地区
                if (!empty($value['set_type']) && !empty($is_data[0]) && is_numeric($is_data[0])) {
                    $where = [
                        'parent_id' => intval($is_data[0])
                    ];
                    $twoLevelArr = Db::name('region')->where($where)->select();
                    if (in_array($is_data[0], $globalFieldRegionType)) {
                        $twoLevelID = !empty($twoLevelArr) ? get_arr_column($twoLevelArr, 'id') : [];
                        $where = [
                            'parent_id' => ['IN', $twoLevelID]
                        ];
                        $twoLevelArr = Db::name('region')->where($where)->select();
                    }
                    // 封装二级区域筛选链接
                    $twoLevelArr = array_merge($all, $twoLevelArr);

                    // 处理参数输出
                    $subLevelParam = $param_query;
                    foreach ($twoLevelArr as $p_key_2 => $p_val_2) {
                        // 参数拼装URL
                        if (!empty($p_val_2['id'])) {
                            $subLevelParam[$name] = $is_data[0] . '_' . $p_val_2['id'];
                        } else {
                            $subLevelParam[$name] = intval($is_data[0]);
                            $twoLevelArr[$p_key_2]['parent_id'] = intval($is_data[0]);
                        }
                        if (in_array($is_data[0], $globalFieldRegionType)) {
                            $twoLevelArr[$p_key_2]['parent_id'] = intval($is_data[0]);
                        }
                        // 筛选标识始终追加在最后
                        unset($subLevelParam[$url_screen_var]);
                        $subLevelParam[$url_screen_var] = 1;
                        foreach (['index','findex','achieve','s'] as $_uk_1 => $_uv) {
                            if (isset($subLevelParam[$_uk_1])) unset($subLevelParam[$_uk_1]);
                        }
                        // 清理手机后台插件特有的参数
                        foreach (['sm','sc','sa','isMobile','weappAjax'] as $_wk => $_wv) {
                            if (isset($subLevelParam[$_wv])) unset($subLevelParam[$_wv]);
                        }
                        if (!empty($typeid)) {
                            // 存在typeid表示在首页展示
                            foreach (['m','c','a','tid'] as $_uk_2 => $_uv) {
                                if (isset($subLevelParam[$_uk_2])) unset($subLevelParam[$_uk_2]);
                            }
                            if (empty($subLevelParam['page'])) $subLevelParam['page'] = 1;
                            $url = ROOT_DIR.'/index.php?m=home&c=Lists&a=index&tid='.$typeid.'&'.urlencode(http_build_query($subLevelParam));
                        } else {
                            $url = ROOT_DIR.'/index.php?'.urlencode(http_build_query($subLevelParam));
                        }
                        $url = $this->auto_hide_index(urldecode($url), $seo_pseudo) . $anchor;
                        // dump($url);
                        // 拼装onClick事件
                        $twoLevelArr[$p_key_2]['onClick'] = empty($anchor) ? $row[$key]['onClick']." data-url='{$url}'" : " href='{$url}' ";
                        // 拼装onchange参数
                        $twoLevelArr[$p_key_2]['SelectUrl'] = "data-url='{$url}'";
                        // 初始化参数，默认未选中
                        $twoLevelArr[$p_key_2]['name']         = "{$p_val_2['name']}";
                        $twoLevelArr[$p_key_2]['SelectValue']  = "";
                        $twoLevelArr[$p_key_2]['currentclass'] = $twoLevelArr[$p_key_2]['currentstyle'] = "";
                        // 选中时执行
                        if ($p_val_2['id'] == $is_data[1]) {
                            $twoLevelArr[$p_key_2]['name']         = "<b>{$p_val_2['name']}</b>";
                            $twoLevelArr[$p_key_2]['SelectValue']  = "selected";
                            $twoLevelArr[$p_key_2]['currentclass'] = $twoLevelArr[$p_key_2]['currentstyle'] = $currentclass;
                        } else if ($p_val_2['id'] == $is_data[1] && 0 === intval($p_key_2)) {
                            $twoLevelArr[$p_key_2]['name']         = "<b>{$p_val_2['name']}</b>";
                            $twoLevelArr[$p_key_2]['SelectValue']  = "selected";
                            $twoLevelArr[$p_key_2]['currentclass'] = $twoLevelArr[$p_key_2]['currentstyle'] = $currentclass;
                        } else if ($p_val_2['name'] == $region_alltxt && $is_data[1] == $region_alltxt) {
                            $twoLevelArr[$p_key_2]['name']         = "<b>{$p_val_2['name']}</b>";
                            $twoLevelArr[$p_key_2]['SelectValue']  = "selected";
                            $twoLevelArr[$p_key_2]['currentclass'] = $twoLevelArr[$p_key_2]['currentstyle'] = $currentclass;
                        }

                        $twoLevelArr[$p_key_2]['threeLevelArr'] = !empty($threeLevelArr[$p_val_2['id']]) ? $threeLevelArr[$p_val_2['id']] :  [];
                    }
                    $twoLevelArr = group_same_key($twoLevelArr, 'parent_id');
                    // dump($twoLevelArr);exit;
                }

                // 参数值含有单引号、双引号、分号，直接跳转404
                if (preg_match('#(\'|\"|;)#', $is_data[0])) abort(404,'页面不存在');

                // 处理后台添加的区域数据
                $oneLevelArr = [];
                // 反序列化参数值
                $dfvalue = safe_unserialize($value['dfvalue']);
                // 拆分ID值
                $region_ids = explode(',', $dfvalue['region_ids']);
                foreach ($region_ids as $id_key => $id_value) {
                    $oneLevelArr[$id_key]['id'] = $id_value;
                }
                // 拆分name值
                $region_names = explode('，', $dfvalue['region_names']);
                foreach ($region_names as $name_key => $name_value) {
                    $oneLevelArr[$name_key]['name'] = $name_value;
                }
                // 合并数组
                $oneLevelArr = array_merge($all, $oneLevelArr);

                // 处理参数输出
                foreach ($oneLevelArr as $dfk => $dfv) {
                    $vv = $dfv;
                    // 参数拼装URL
                    if (!empty($vv['id'])) {
                        $param_query[$name] = $vv['id'];
                    } else {
                        unset($param_query[$name]);
                    }
                    // 筛选标识始终追加在最后
                    unset($param_query[$url_screen_var]);
                    $param_query[$url_screen_var] = 1;
                    foreach (['index','findex','achieve','s'] as $_uk => $_uv) {
                        if (isset($param_query[$_uv])) unset($param_query[$_uv]);
                    }
                    // 清理手机后台插件特有的参数
                    foreach (['sm','sc','sa','isMobile','weappAjax'] as $_wk => $_wv) {
                        if (isset($param_query[$_wv])) unset($param_query[$_wv]);
                    }
                    if (!empty($typeid)) {
                        // 存在typeid表示在首页展示
                        foreach (['m','c','a','tid'] as $_uk => $_uv) {
                            if (isset($param_query[$_uv])) unset($param_query[$_uv]);
                        }
                        if (empty($param_query['page'])) $param_query['page'] = 1;
                        $url = ROOT_DIR.'/index.php?m=home&c=Lists&a=index&tid='.$typeid.'&'.urlencode(http_build_query($param_query));
                    } else {
                        $url = ROOT_DIR.'/index.php?'.urlencode(http_build_query($param_query));
                    }
                    $url = $this->auto_hide_index(urldecode($url), $seo_pseudo) . $anchor;
                    
                    // 拼装onClick事件
                    $oneLevelArr[$dfk]['onClick'] = empty($anchor) ? $row[$key]['onClick']." data-url='{$url}'" : " href='{$url}' ";
                    // 拼装onchange参数
                    $oneLevelArr[$dfk]['SelectUrl'] = "data-url='{$url}'";
                    // 初始化参数，默认未选中
                    $oneLevelArr[$dfk]['name']         = "{$vv['name']}";
                    $oneLevelArr[$dfk]['SelectValue']  = "";
                    $oneLevelArr[$dfk]['currentclass'] = $oneLevelArr[$dfk]['currentstyle'] = "";
                    // 选中时执行
                    if ($vv['id'] == $is_data[0]) {
                        $oneLevelArr[$dfk]['name']         = "<b>{$vv['name']}</b>";
                        $oneLevelArr[$dfk]['SelectValue']  = "selected";
                        $oneLevelArr[$dfk]['currentclass'] = $oneLevelArr[$dfk]['currentstyle'] = $currentclass;
                    } else if ($vv['name'] == $region_alltxt && $is_data[0] == $region_alltxt) {
                        $oneLevelArr[$dfk]['name']         = "<b>{$vv['name']}</b>";
                        $oneLevelArr[$dfk]['SelectValue']  = "selected";
                        $oneLevelArr[$dfk]['currentclass'] = $oneLevelArr[$dfk]['currentstyle'] = $currentclass;
                    }

                    $oneLevelArr[$dfk]['twoLevelArr'] = !empty($twoLevelArr[$vv['id']]) ? $twoLevelArr[$vv['id']] :  [];
                }
                // 数据赋值到数组中
                $row[$key]['dfvalue'] = $oneLevelArr;

            } else if ('cascade' == $value['dtype']) {
                // 联动选择类型筛选
                $cascade_alltxt = $alltxt;
                if (!empty($cascade_alltxt)) {
                    if ('off' == $cascade_alltxt) $cascade_alltxt = '';
                } else {
                    $cascade_alltxt = '全部';
                }
                $all = [];
                if (!empty($cascade_alltxt)) {
                    $all[0] = [
                        'id'   => '',
                        'name' => $cascade_alltxt,
                    ];
                }

                // 搜索点击的name值
                $is_data = !empty($param[$name]) ? $param[$name] : '';
                $is_data_arr = !empty($is_data) ? explode('_', $is_data) : [];

                // 参数值安全过滤
                if (!empty($is_data) && preg_match('#(\'|\"|;)#', $is_data)) abort(404,'页面不存在');

                // 查询三级选项
                $threeLevelArr = [];
                if (!empty($is_data_arr[1]) && is_numeric($is_data_arr[1])) {
                    $cascadeLevel3 = Db::name('field_cascade_data')->where([
                        'field_id'  => $value['id'],
                        'parent_id' => intval($is_data_arr[1]),
                        'level'     => 3,
                    ])->order('sort_order asc, id asc')->select();
                    if (!empty($cascadeLevel3)) {
                        $cascadeLevel3 = array_merge($all, $cascadeLevel3);
                        $subLevelParam = $param_query;
                        foreach ($cascadeLevel3 as $p_key_1 => $p_val_1) {
                            if (!empty($p_val_1['id'])) {
                                $subLevelParam[$name] = $is_data_arr[0] . '_' . $is_data_arr[1] . '_' . $p_val_1['id'];
                            } else {
                                $subLevelParam[$name] = $is_data_arr[0] . '_' . $is_data_arr[1];
                                $cascadeLevel3[$p_key_1]['parent_id'] = intval($is_data_arr[1]);
                            }
                            unset($subLevelParam[$url_screen_var]);
                            $subLevelParam[$url_screen_var] = 1;
                            foreach (['index','findex','achieve','s'] as $_uv) {
                                if (isset($subLevelParam[$_uv])) unset($subLevelParam[$_uv]);
                            }
                            foreach (['sm','sc','sa','isMobile','weappAjax'] as $_wv) {
                                if (isset($subLevelParam[$_wv])) unset($subLevelParam[$_wv]);
                            }
                            if (!empty($typeid)) {
                                foreach (['m','c','a','tid'] as $_uv) {
                                    if (isset($subLevelParam[$_uv])) unset($subLevelParam[$_uv]);
                                }
                                if (empty($subLevelParam['page'])) $subLevelParam['page'] = 1;
                                $url = ROOT_DIR.'/index.php?m=home&c=Lists&a=index&tid='.$typeid.'&'.urlencode(http_build_query($subLevelParam));
                            } else {
                                $url = ROOT_DIR.'/index.php?'.urlencode(http_build_query($subLevelParam));
                            }
                            $url = $this->auto_hide_index(urldecode($url), $seo_pseudo) . $anchor;
                            $cascadeLevel3[$p_key_1]['onClick'] = empty($anchor) ? $row[$key]['onClick']." data-url='{$url}'" : " href='{$url}' ";
                            $cascadeLevel3[$p_key_1]['SelectUrl'] = "data-url='{$url}'";
                            $cascadeLevel3[$p_key_1]['name']         = "{$p_val_1['name']}";
                            $cascadeLevel3[$p_key_1]['SelectValue']  = "";
                            $cascadeLevel3[$p_key_1]['currentclass'] = $cascadeLevel3[$p_key_1]['currentstyle'] = "";
                            if (!empty($is_data_arr[2]) && $p_val_1['id'] == $is_data_arr[2]) {
                                $cascadeLevel3[$p_key_1]['name']         = "<b>{$p_val_1['name']}</b>";
                                $cascadeLevel3[$p_key_1]['SelectValue']  = "selected";
                                $cascadeLevel3[$p_key_1]['currentclass'] = $cascadeLevel3[$p_key_1]['currentstyle'] = $currentclass;
                            } else if (empty($p_val_1['id']) && empty($is_data_arr[2])) {
                                $cascadeLevel3[$p_key_1]['name']         = "<b>{$p_val_1['name']}</b>";
                                $cascadeLevel3[$p_key_1]['SelectValue']  = "selected";
                                $cascadeLevel3[$p_key_1]['currentclass'] = $cascadeLevel3[$p_key_1]['currentstyle'] = $currentclass;
                            }
                        }
                        $threeLevelArr = group_same_key($cascadeLevel3, 'parent_id');
                    }
                }

                // 查询二级选项
                $twoLevelArr = [];
                if (!empty($is_data_arr[0]) && is_numeric($is_data_arr[0])) {
                    $cascadeLevel2 = Db::name('field_cascade_data')->where([
                        'field_id'  => $value['id'],
                        'parent_id' => intval($is_data_arr[0]),
                        'level'     => 2,
                    ])->order('sort_order asc, id asc')->select();
                    if (!empty($cascadeLevel2)) {
                        $cascadeLevel2 = array_merge($all, $cascadeLevel2);
                        $subLevelParam = $param_query;
                        foreach ($cascadeLevel2 as $p_key_2 => $p_val_2) {
                            if (!empty($p_val_2['id'])) {
                                $subLevelParam[$name] = $is_data_arr[0] . '_' . $p_val_2['id'];
                            } else {
                                $subLevelParam[$name] = intval($is_data_arr[0]);
                                $cascadeLevel2[$p_key_2]['parent_id'] = intval($is_data_arr[0]);
                            }
                            unset($subLevelParam[$url_screen_var]);
                            $subLevelParam[$url_screen_var] = 1;
                            foreach (['index','findex','achieve','s'] as $_uv) {
                                if (isset($subLevelParam[$_uv])) unset($subLevelParam[$_uv]);
                            }
                            foreach (['sm','sc','sa','isMobile','weappAjax'] as $_wv) {
                                if (isset($subLevelParam[$_wv])) unset($subLevelParam[$_wv]);
                            }
                            if (!empty($typeid)) {
                                foreach (['m','c','a','tid'] as $_uv) {
                                    if (isset($subLevelParam[$_uv])) unset($subLevelParam[$_uv]);
                                }
                                if (empty($subLevelParam['page'])) $subLevelParam['page'] = 1;
                                $url = ROOT_DIR.'/index.php?m=home&c=Lists&a=index&tid='.$typeid.'&'.urlencode(http_build_query($subLevelParam));
                            } else {
                                $url = ROOT_DIR.'/index.php?'.urlencode(http_build_query($subLevelParam));
                            }
                            $url = $this->auto_hide_index(urldecode($url), $seo_pseudo) . $anchor;
                            $cascadeLevel2[$p_key_2]['onClick'] = empty($anchor) ? $row[$key]['onClick']." data-url='{$url}'" : " href='{$url}' ";
                            $cascadeLevel2[$p_key_2]['SelectUrl'] = "data-url='{$url}'";
                            $cascadeLevel2[$p_key_2]['name']         = "{$p_val_2['name']}";
                            $cascadeLevel2[$p_key_2]['SelectValue']  = "";
                            $cascadeLevel2[$p_key_2]['currentclass'] = $cascadeLevel2[$p_key_2]['currentstyle'] = "";
                            if (!empty($is_data_arr[1]) && $p_val_2['id'] == $is_data_arr[1]) {
                                $cascadeLevel2[$p_key_2]['name']         = "<b>{$p_val_2['name']}</b>";
                                $cascadeLevel2[$p_key_2]['SelectValue']  = "selected";
                                $cascadeLevel2[$p_key_2]['currentclass'] = $cascadeLevel2[$p_key_2]['currentstyle'] = $currentclass;
                            } else if (empty($p_val_2['id']) && empty($is_data_arr[1])) {
                                $cascadeLevel2[$p_key_2]['name']         = "<b>{$p_val_2['name']}</b>";
                                $cascadeLevel2[$p_key_2]['SelectValue']  = "selected";
                                $cascadeLevel2[$p_key_2]['currentclass'] = $cascadeLevel2[$p_key_2]['currentstyle'] = $currentclass;
                            }
                            $cascadeLevel2[$p_key_2]['threeLevelArr'] = !empty($threeLevelArr[$p_val_2['id']]) ? $threeLevelArr[$p_val_2['id']] : [];
                        }
                        $twoLevelArr = group_same_key($cascadeLevel2, 'parent_id');
                    }
                }

                // 查询一级选项
                $oneLevelArr = Db::name('field_cascade_data')->where([
                    'field_id'  => $value['id'],
                    'parent_id' => 0,
                    'level'     => 1,
                ])->order('sort_order asc, id asc')->select();
                $oneLevelArr = array_merge($all, $oneLevelArr);

                foreach ($oneLevelArr as $dfk => $dfv) {
                    $vv = $dfv;
                    if (!empty($vv['id'])) {
                        $param_query[$name] = $vv['id'];
                    } else {
                        unset($param_query[$name]);
                    }
                    unset($param_query[$url_screen_var]);
                    $param_query[$url_screen_var] = 1;
                    foreach (['index','findex','achieve','s'] as $_uv) {
                        if (isset($param_query[$_uv])) unset($param_query[$_uv]);
                    }
                    foreach (['sm','sc','sa','isMobile','weappAjax'] as $_wv) {
                        if (isset($param_query[$_wv])) unset($param_query[$_wv]);
                    }
                    if (!empty($typeid)) {
                        foreach (['m','c','a','tid'] as $_uv) {
                            if (isset($param_query[$_uv])) unset($param_query[$_uv]);
                        }
                        if (empty($param_query['page'])) $param_query['page'] = 1;
                        $url = ROOT_DIR.'/index.php?m=home&c=Lists&a=index&tid='.$typeid.'&'.urlencode(http_build_query($param_query));
                    } else {
                        $url = ROOT_DIR.'/index.php?'.urlencode(http_build_query($param_query));
                    }
                    $url = $this->auto_hide_index(urldecode($url), $seo_pseudo) . $anchor;
                    $oneLevelArr[$dfk]['onClick'] = empty($anchor) ? $row[$key]['onClick']." data-url='{$url}'" : " href='{$url}' ";
                    $oneLevelArr[$dfk]['SelectUrl'] = "data-url='{$url}'";
                    $oneLevelArr[$dfk]['name']         = "{$vv['name']}";
                    $oneLevelArr[$dfk]['SelectValue']  = "";
                    $oneLevelArr[$dfk]['currentclass'] = $oneLevelArr[$dfk]['currentstyle'] = "";
                    if (!empty($is_data_arr[0]) && $vv['id'] == $is_data_arr[0]) {
                        $oneLevelArr[$dfk]['name']         = "<b>{$vv['name']}</b>";
                        $oneLevelArr[$dfk]['SelectValue']  = "selected";
                        $oneLevelArr[$dfk]['currentclass'] = $oneLevelArr[$dfk]['currentstyle'] = $currentclass;
                    } else if (empty($vv['id']) && empty($is_data_arr[0])) {
                        $oneLevelArr[$dfk]['name']         = "<b>{$vv['name']}</b>";
                        $oneLevelArr[$dfk]['SelectValue']  = "selected";
                        $oneLevelArr[$dfk]['currentclass'] = $oneLevelArr[$dfk]['currentstyle'] = $currentclass;
                    }
                    $oneLevelArr[$dfk]['twoLevelArr'] = !empty($twoLevelArr[$vv['id']]) ? $twoLevelArr[$vv['id']] : [];
                }
                // 数据赋值到数组中
                $row[$key]['dfvalue'] = $oneLevelArr;

            } else {
                // 多选项、单选项、下拉框[checkboxs]
                if (in_array($value['dtype'], ['checkboxs','radios','selects'])) {
                    static $fieldCustomParamList = null;
                    if (null === $fieldCustomParamList) {
                        $fieldCustomParamList = array();
                        $fieldCustomParamRow = Db::name('field_custom_param')->field('id,dfvalue,name,channel_id')->order('dtype ASC, sort_order ASC, id ASC')->select();
                        foreach ($fieldCustomParamRow as $k => $v) {
                            $fieldCustomParamList[$v['channel_id']][$v['name']][$v['id']] = $v;
                        }
                    }
                    $dfvalueArr = [];
                    $fieldCustomParamInfo = empty($fieldCustomParamList[$value['channel_id']][$value['name']]) ? [] : $fieldCustomParamList[$value['channel_id']][$value['name']];
                    foreach ($fieldCustomParamInfo as $_fpval) {
                        $dfvalueArr[] = [
                            'id' => $_fpval['id'],
                            'name' => $_fpval['dfvalue'],
                        ];
                    }
                    $dfvalue = $dfvalueArr;
                } else {
                    $dfvalue = explode(',', $value['dfvalue']);
                }

                $all[0] = [];
                if (!empty($alltxt)) {
                    // 等于OFF表示关闭，不需要此项
                    if ('off' != $alltxt) $all[0] = $alltxt;
                } else {
                    $all[0] = '全部';
                }

                // 搜索点击的name值
                $is_data = isset($param[$name]) && !empty($param[$name]) ? $param[$name] : $alltxt;

                // 参数值含有单引号、双引号、分号，直接跳转404
                if (preg_match('#(\'|\"|;)#', $is_data)) abort(404,'页面不存在');

                // 多选类型：解析已选中的多个值
                $is_data_multi = [];
                $is_multi_on = !empty($value['is_multiselect']);
                if ($is_multi_on && in_array($value['dtype'], ['checkbox', 'select', 'checkboxs', 'selects'])) {
                    $is_data_clean = rtrim($is_data, '|');
                    $is_data_multi = (!empty($is_data_clean) && $is_data_clean != $alltxt) ? explode(',', $is_data_clean) : [];
                }

                // 合并数组
                $dfvalue  = array_merge($all, $dfvalue);
                // 处理参数输出
                $data_new = [];
                foreach ($dfvalue as $dfk => $dfv) {
                    if (is_array($dfv) && array_key_exists('name', $dfv)) {
                        $vv_id = $dfv['id'];
                        $vv = $dfv['name'];
                    } else {
                        $vv = $vv_id = $dfv;
                    }

                    if ('off' == $alltxt && empty($vv)) {
                        continue;
                    }
                    $param_query[$name]    = $vv_id;
                    $data_new[$dfk]['id']           = $vv_id;
                    $data_new[$dfk]['name']         = "{$vv}";
                    $data_new[$dfk]['SelectValue']  = "";
                    $data_new[$dfk]['currentclass'] = $data_new[$dfk]['currentstyle'] = "";

                    if ($is_multi_on && in_array($value['dtype'], ['checkbox', 'select', 'checkboxs', 'selects'])) {
                        $check_val = (in_array($value['dtype'], ['checkboxs', 'selects'])) ? (string)$vv_id : (string)$vv;
                        if (!empty($is_data_multi) && in_array($check_val, $is_data_multi)) {
                            $data_new[$dfk]['name']         = "<b>{$vv}</b>";
                            $data_new[$dfk]['SelectValue']  = "selected";
                            $data_new[$dfk]['currentclass'] = $data_new[$dfk]['currentstyle'] = $currentclass;
                        } else if ($vv == $all[0] && empty($is_data_multi)) {
                            $data_new[$dfk]['name']         = "<b>{$vv}</b>";
                            $data_new[$dfk]['SelectValue']  = "selected";
                            $data_new[$dfk]['currentclass'] = $data_new[$dfk]['currentstyle'] = $currentclass;
                        }
                    } else {
                        if ($vv_id == $is_data) {
                            $data_new[$dfk]['name']         = "<b>{$vv}</b>";
                            $data_new[$dfk]['SelectValue']  = "selected";
                            $data_new[$dfk]['currentclass'] = $data_new[$dfk]['currentstyle'] = $currentclass;
                        } else if ($vv == $all[0] && empty($is_data)) {
                            $data_new[$dfk]['name']         = "<b>{$vv}</b>";
                            $data_new[$dfk]['SelectValue']  = "selected";
                            $data_new[$dfk]['currentclass'] = $data_new[$dfk]['currentstyle'] = $currentclass;
                        }
                    }

                    if ($all[0] == $vv) {
                        unset($param_query[$name]);
                    } else if ($is_multi_on && in_array($value['dtype'], ['checkbox', 'select'])) {
                        $toggle_val = (string)$vv;
                        $new_multi = $is_data_multi;
                        if (in_array($toggle_val, $new_multi)) {
                            $new_multi = array_values(array_diff($new_multi, [$toggle_val]));
                        } else {
                            $new_multi[] = $toggle_val;
                        }
                        if (empty($new_multi)) {
                            unset($param_query[$name]);
                        } else {
                            $param_query[$name] = implode(',', $new_multi) . '|';
                        }
                    } else if ($is_multi_on && in_array($value['dtype'], ['checkboxs', 'selects'])) {
                        $toggle_val = (string)$vv_id;
                        $new_multi = $is_data_multi;
                        if (in_array($toggle_val, $new_multi)) {
                            $new_multi = array_values(array_diff($new_multi, [$toggle_val]));
                        } else {
                            $new_multi[] = $toggle_val;
                        }
                        if (empty($new_multi)) {
                            unset($param_query[$name]);
                        } else {
                            $param_query[$name] = implode(',', $new_multi) . '|';
                        }
                    }
                    /* 筛选标识始终追加在最后 */
                    unset($param_query[$url_screen_var]);
                    $param_query[$url_screen_var] = 1;
                    /* end */
                    foreach (['index','findex','achieve','s'] as $_uk => $_uv) {
                        if (isset($param_query[$_uv])) {
                            unset($param_query[$_uv]);
                        }
                    }
                    // 清理手机后台插件特有的参数
                    foreach (['sm','sc','sa','isMobile','weappAjax'] as $_wk => $_wv) {
                        if (isset($param_query[$_wv])) unset($param_query[$_wv]);
                    }
                    // 参数拼装URL
                    if (!empty($typeid)) {
                        // 存在typeid表示在首页展示
                        foreach (['m','c','a','tid'] as $_uk => $_uv) {
                            if (isset($param_query[$_uv])) {
                                unset($param_query[$_uv]);
                            }
                        }
                        if (empty($param_query['page'])) $param_query['page'] = 1;
                        $url = ROOT_DIR.'/index.php?m=home&c=Lists&a=index&tid='.$typeid.'&'.urlencode(http_build_query($param_query));
                    }else{
                        $url = ROOT_DIR.'/index.php?'.urlencode(http_build_query($param_query));
                    }
                    $url = $this->auto_hide_index(urldecode($url), $seo_pseudo) . $anchor;
                    // 封装onClick
                    $data_new[$dfk]['onClick'] = empty($anchor) ? $row[$key]['onClick']." data-url='{$url}'" : " href='{$url}' ";
                    // 封装onchange事件
                    $data_new[$dfk]['SelectUrl'] = "data-url='{$url}'";
                }

                // 数据赋值到数组中
                $row[$key]['dfvalue'] = $data_new;
            }
        }
        
        $resetUrl = ROOT_DIR."/index.php?m=home&c=Lists&a=index&tid={$this->tid}&{$url_screen_var}=1";

        $hidden .= <<<EOF
<script type="text/javascript">
    function {$OnclickScreening}(obj) {
        var dataurl = obj.getAttribute("data-url");
        if (dataurl) {
            window.location.href = dataurl;
        } else {
            alert('没有选择筛选项');
        }
    }

    function {$OnchangeScreening}(obj) {
        var dataurl = obj.options[obj.selectedIndex].getAttribute("data-url");
        if (dataurl) {
            window.location.href = dataurl;
        } else {
            alert('没有选择筛选项');
        }
    }
</script>
EOF;
        $result = array(
            'list' => $row,
            'hidden' => $hidden,
            'resetUrl' => $resetUrl,
        );
        return $result;
    }
}