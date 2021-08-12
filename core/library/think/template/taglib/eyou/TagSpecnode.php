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

/**
 * 专题节点
 */
class TagSpecnode extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取节点的文档列表
     * @author wengxianhu by 2018-4-20
     */
    public function getSpecnode($tag = '', $aid = 0, $title = '', $code = '', $typeid = 0, $aidlist = '', $isauto = 0, $keyword = '', $titlelen = '', $infolen = '', $limit = 0, $thumb = '')
    {
        $aid = !empty($aid) ? intval($aid) : $this->aid;
        if (empty($aid)) {
            echo '标签specnode报错：缺少属性 aid 值，请填写专题文档ID。';
            return false;
        }

        $code = trim($code);
        $title = trim($title);

        $map = [
            'aid'   => $aid,
        ];
        if (!empty($code)) {
            $map['code'] = $code;
        } else if (!empty($title)) {
            $map['title']   = $title;
        }
        $map['status'] = 1;
        $map['is_del'] = 0;
        $map['lang'] = $this->home_lang;
        $specialNodeInfo = Db::name('special_node')->where($map)->order('node_id asc')->find();
        if (empty($specialNodeInfo)) {
            return false;
        }
        !isset($tag['isauto']) && $isauto = !empty($specialNodeInfo['isauto']) ? $specialNodeInfo['isauto'] : 0;
        !isset($tag['keyword']) && $keyword = !empty($specialNodeInfo['keywords']) ? $specialNodeInfo['keywords'] : '';
        !isset($tag['typeid']) && $typeid = !empty($specialNodeInfo['typeid']) ? $specialNodeInfo['typeid'] : 0;
        !isset($tag['aidlist']) && $aidlist = !empty($specialNodeInfo['aidlist']) ? $specialNodeInfo['aidlist'] : '';
        !isset($tag['limit']) && $limit = !empty($specialNodeInfo['row']) ? $specialNodeInfo['row'] : 10;
        !isset($tag['titlelen']) && $titlelen = !empty($specialNodeInfo['titlelen']) ? $specialNodeInfo['titlelen'] : 100;
        !isset($tag['infolen']) && $infolen = !empty($specialNodeInfo['infolen']) ? $specialNodeInfo['infolen'] : 160;

        $typeid = intval($typeid);
        $isauto = intval($isauto);
        $titlelen = intval($titlelen);
        $infolen = intval($infolen);
        $limit = str_replace('，', ',', $limit);

        $aidlistArr = [];
        if (!empty($aidlist)) {
            $aidlist = str_replace('，', ',', $aidlist);
            $aidlistArr = explode(',', $aidlist);
            $aidlistArr = array_unique($aidlistArr); // 去重
            foreach ($aidlistArr as $key => $val) {
                $val = trim($val);
                if (empty($val) || !is_numeric($val)) {
                    unset($aidlistArr[$key]);
                    break;
                }
            }
        }

        $keywordArr = [];
        if (!empty($keyword)) {
            $keyword = str_replace('，', ',', $keyword);
            $keywordArr = explode(',', $keyword);
            $keywordArr = array_unique($keywordArr); // 去重
            foreach ($keywordArr as $key => $val) {
                $val = trim($val);
                if (empty($val)) {
                    unset($keywordArr[$key]);
                    break;
                } else {
                    $keywordArr[$key] = addslashes($val);
                }
            }
        }

        // 查询条件
        $condition = [];
        if (empty($isauto)) {
            if (empty($aidlistArr)) {
                return false;
            }
            $condition['a.aid'] = ['IN', $aidlistArr];
            $limit = count($aidlistArr);
            $aidlist = implode(',', $aidlistArr);
            $orderBy = "FIELD(a.aid, {$aidlist})";
        } else {
            if (!empty($typeid)) {
                /*获取当前栏目下的所有同模型的子孙栏目*/
                $arctype_info = M('arctype')->field('id,current_channel')->where('id', $typeid)->find();
                $arctype_list = model("Arctype")->getHasChildren($typeid);
                foreach ($arctype_list as $key => $val) {
                    if ($arctype_info['current_channel'] != $val['current_channel']) {
                        unset($arctype_list[$key]);
                    }
                }
                $typeids = get_arr_column($arctype_list, "id");
                !in_array($typeid, $typeids) && $typeids[] = $typeid;
                $typeid = implode(",", $typeids);
                /*--end*/
                $condition['a.typeid'] = ['IN', $typeid];
            }
            if (!empty($keywordArr)) {
                $keyword = implode('|', $keywordArr);
                $condition[] = Db::raw(" CONCAT(a.title,a.seo_keywords) REGEXP '$keyword' ");
            }
            $orderBy = "a.sort_order asc, a.aid desc";
        }
        $condition['a.arcrank'] = ['gt', -1];
        $condition['a.status'] = 1;
        $condition['a.is_del'] = 0;
        $condition['a.lang'] = $this->home_lang;

        $allow_release_channel = config('global.allow_release_channel');
        $index = array_search(7, $allow_release_channel); // 过滤专题模型
        unset($allow_release_channel[$index]);
        $condition['a.channel'] = ['IN', $allow_release_channel];
        
        /*定时文档显示插件*/
        if (is_dir('./weapp/TimingTask/')) {
            $TimingTaskRow = model('Weapp')->getWeappList('TimingTask');
            if (!empty($TimingTaskRow['status']) && 1 == $TimingTaskRow['status']) {
                $condition['a.add_time'] = array('elt', getTime()); // 只显当天或之前的文档
            }
        }
        /*end*/

        $query_get = input('get.');
        unset($query_get['s']);

        $paginate_type = config('paginate.type');
        if (isMobile()) {
            $paginate_type = 'mobile';
        }
        $paginate = array(
            'type'  => $paginate_type,
            'var_page' => config('paginate.var_page'),
            'query' => $query_get,
        );
        $pages = Db::name('archives')->field("b.*, a.*")
            ->alias('a')
            ->join('__ARCTYPE__ b', 'b.id = a.typeid', 'LEFT')
            ->where($condition)
            ->orderRaw($orderBy)
            ->paginate($limit, false, $paginate);
        $list = $pages->items();

        $aidArr = [];
        $channeltype_row = \think\Cache::get('extra_global_channeltype');
        foreach ($list as $key => $val) {
            array_push($aidArr, $val['aid']); // 收集文档ID

            $val['title'] = text_msubstr($val["title"], 0, $titlelen, false);
            $val['seo_description'] = text_msubstr($val["seo_description"], 0, $infolen, true);

            $controller_name = !empty($channeltype_row[$val['channel']]['ctl_name']) ? $channeltype_row[$val['channel']]['ctl_name'] : 'Article';

            /*栏目链接*/
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
            $val['litpic'] = get_default_pic($val['litpic']); // 默认封面图
            if ('on' == $thumb) { // 属性控制是否使用缩略图
                $val['litpic'] = thumb_img($val['litpic']);
            }
            /*--end*/

            /*是否显示会员权限*/
            !isset($val['level_name']) && $val['level_name'] = $val['arcrank'];
            !isset($val['level_value']) && $val['level_value'] = 0;

            $list[$key] = $val;
        }

        $result = [
            'pages' => $pages, // 分页显示输出
            'list' => !empty($list) ? $list : [], // 赋值数据集
        ];

        return $result;
    }
}