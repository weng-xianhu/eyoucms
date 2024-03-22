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

namespace think\template\taglib\api;

use think\Db;
use think\Cache;

/**
 * 栏目列表分页
 */
class TagChannellist extends Base
{
    public $currentstyle = '';

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取指定级别的栏目列表
     * @param string type son表示下一级栏目,self表示同级栏目,top顶级栏目
     * @param boolean $self 包括自己本身
     * @author wengxianhu by 2018-4-26
     */
    public function getChannellist($param = [], $typeid = '', $channelid = '')
    {
        $type = !empty($param['type']) ? $param['type'] : 'top';
        $param['addfields'] = !empty($param['addfields']) ? str_replace(["'", '"'], '', $param['addfields']) : '';
        $typeid = intval($typeid);
        $result = array();
        switch ($type) {
            case 'son': // 下级栏目
                $result = $this->getSon($param,$typeid,$channelid);
                break;

            case 'top': // 顶级栏目
                $result = $this->getTop($param,$channelid);
                break;
        }

        /*处理自定义表字段的值*/
        if (!empty($result['data'])) {
            /*获取自定义表字段信息*/
            $map = array(
                'channel_id'    => config('global.arctype_channel_id'),
            );
            $fieldInfo = model('Channelfield')->getListByWhere($map, '*', 'name');
            /*--end*/
            $fieldLogic = new \app\home\logic\FieldLogic;
            foreach ($result['data'] as $key => $val) {
                if (!empty($val)) {
                    $val = $fieldLogic->handleAddonFieldList($val, $fieldInfo, false, true);
                    $result['data'][$key] = $val;
                }
            }
        }
        /*--end*/

        /*栏目的内容信息*/
        // if (!empty($result['data']) && !empty($param['addfields'])) {
        //     $typeid_arr = get_arr_column($result['data'], 'id');
        //     $addtableName = 'single_content';
        //     $addfields = str_replace('，', ',', $param['addfields']); // 替换中文逗号
        //     $addfields = trim($addfields, ',');
        //     /*过滤不相关的字段*/
        //     $addfields_arr = explode(',', $addfields);
        //     $extFields = Db::name($addtableName)->getTableFields();
        //     $addfields_arr = array_intersect($addfields_arr, $extFields);
        //     if (!empty($addfields_arr) && is_array($addfields_arr)) {
        //         $addfields = implode(',', $addfields_arr);
        //     } else {
        //         $addfields = '';
        //     }
        //     /*end*/
        //     !empty($addfields) && $addfields = ','.$addfields;
        //     if (strstr(",{$addfields},", ',content,')){
        //         $addfields .= ',content_ey_m';
        //     }
        //     if (!empty($addfields)) {
        //         $resultExt = M($addtableName)->field("typeid {$addfields}")->where('typeid','in',$typeid_arr)->getAllWithIndex('typeid');
        //         /*自定义字段的数据格式处理*/
        //         $resultExt = $this->fieldLogic->getChannelFieldList($resultExt, 6, true);
        //         /*--end*/
        //         foreach ($result['data'] as $key => $val) {
        //             $valExt = !empty($resultExt[$val['typeid']]) ? $resultExt[$val['typeid']] : array();
        //             if (strstr(",{$addfields},", ',content,') && !empty($valExt['content_ey_m'])){
        //                 $valExt['content'] = $valExt['content_ey_m'];
        //             }
        //             if (isset($valExt['content_ey_m'])) {unset($valExt['content_ey_m']);}
        //             $val = array_merge($valExt, $val);
        //             $result['data'][$key] = $val;
        //         }
        //     }
        // }
        /*--end*/

        $res = [
            'data'=> !empty($result) ? $result : false,
        ];
        return $res;
    }


    /**
     * 获取下一级栏目
     * @param string $self true表示没有子栏目时，获取同级栏目
     * @author wengxianhu by 2017-7-26
     */
    public function getSon($param = [],$typeid = 0,$channelid = 0)
    {
        $result = array();
        if (empty($typeid)) {
            return $result;
        }
        $page = !empty($param['page']) ? $param['page'] : 1;
        $limit = !empty($param['limit']) ? $param['limit'] : 10;
        $orderby = !empty($param['orderby']) ? $param['orderby'] : 'id';
        $orderway = !empty($param['orderway']) ? $param['orderway'] : 'desc';
        $order = $orderby . ' ' .$orderway;

        $map = array(
            'is_hidden'   => 0,
            'status'  => 1,
            'is_del'    => 0, // 回收站功能
            'lang'    => self::$home_lang,
            'parent_id'=>$typeid
        );
        if (!empty($channelid)){
            $map['current_channel'] = $channelid;
        }
        $paginate = array(
            'page'  => $page,
        );
        $pages = Db::name('arctype')
            ->where($map)
            ->order($order)
            ->cache(true,EYOUCMS_CACHE_TIME,"arctype")
            ->paginate($limit, false, $paginate);
        $res = $pages->toArray();

        if ($res['data']) {
            $typeids = [];
            foreach ($res['data'] as $key => $val) {
                $typeids[] = $val['id'];
                $val['typename'] = htmlspecialchars_decode($val['typename']);
                $val['litpic'] = $this->get_default_pic($val['litpic']); // 封面图
                $val['article_number'] = 0;//文章数量
                $res['data'][$key] = $val;
            }
//            栏目下的所有子栏目文章数量也要统计进来
            $c_where['parent_id'] = ['in',$typeids];
            if (!empty($channelid)){
                $c_where['current_channel'] = $channelid;
            }
            $child_typeid_arr = Db::name('arctype')->where($c_where)->field('id,parent_id')->select();
            foreach ($child_typeid_arr as $k => $v){
                $typeids[] = $v['id'];
            }
            $count_arr = Db::name('archives')->field('count(*) as count,typeid')->where('typeid','in',$typeids)->group('typeid')->getAllWithIndex('typeid');

            foreach ($child_typeid_arr as $k => $v) {
                if (!empty($count_arr[$v['id']])){
                    if (!empty($count_arr[$v['parent_id']])){
                        $count_arr[$v['parent_id']]['count'] += $count_arr[$v['id']]['count'];
                    }else{
                        $count_arr[$v['parent_id']] = ['count'=> $count_arr[$v['id']]['count'],'typeid'=>$v['parent_id']];
                    }
                }
            }
            foreach ($res['data'] as $key => $val) {
                if (!empty($count_arr[$val['id']])){
                    $res['data'][$key]['article_number'] = $count_arr[$val['id']]['count'];
                }
            }

        }

        return $res;
    }

    /**
     * 获取顶级栏目
     * @author wengxianhu by 2017-7-26
     */
    public function getTop($param = [],$channelid = 0)
    {
        $page = !empty($param['page']) ? $param['page'] : 1;
        $limit = !empty($param['limit']) ? $param['limit'] : 10;
        $orderby = !empty($param['orderby']) ? $param['orderby'] : 'id';
        $orderway = !empty($param['orderway']) ? $param['orderway'] : 'desc';
        $order = $orderby . ' ' .$orderway;

        $map = array(
            'is_hidden'   => 0,
            'status'  => 1,
            'is_del'    => 0, // 回收站功能
            'lang'    => self::$home_lang,
            'topid'=> 0
        );
        if (!empty($channelid)){
            $map['current_channel'] = $channelid;
        }
        $paginate = array(
            'page'  => $page,
        );
        $pages = Db::name('arctype')
            ->where($map)
            ->order($order)
            ->cache(true,EYOUCMS_CACHE_TIME,"arctype")
            ->paginate($limit, false, $paginate);
        $res = $pages->toArray();

        if ($res['data']) {
            $typeids = [];
            foreach ($res['data'] as $key => $val) {
                $typeids[] = $val['id'];
                $val['typename'] = htmlspecialchars_decode($val['typename']);
                $val['litpic'] = $this->get_default_pic($val['litpic']); // 封面图
                $val['article_number'] = 0;//文章数量
                $res['data'][$key] = $val;
            }
//            栏目下的所有子栏目文章数量也要统计进来
            $c_where['topid'] = ['in',$typeids];
            if (!empty($channelid)){
                $c_where['current_channel'] = $channelid;
            }
            $child_typeid_arr = Db::name('arctype')->where('topid','in',$typeids)->field('id,topid')->select();
            foreach ($child_typeid_arr as $k => $v){
                $typeids[] = $v['id'];
            }
            $count_arr = Db::name('archives')->field('count(*) as count,typeid')->where('typeid','in',$typeids)->group('typeid')->getAllWithIndex('typeid');

            foreach ($child_typeid_arr as $k => $v) {
                if (!empty($count_arr[$v['id']])){
                    if (!empty($count_arr[$v['topid']])){
                        $count_arr[$v['topid']]['count'] += $count_arr[$v['id']]['count'];
                    }else{
                        $count_arr[$v['topid']] = ['count'=> $count_arr[$v['id']]['count'],'typeid'=>$v['topid']];
                    }
                }
            }
            foreach ($res['data'] as $key => $val) {
                if (!empty($count_arr[$val['id']])){
                    $res['data'][$key]['article_number'] = $count_arr[$val['id']]['count'];
                }
            }

        }

        return $res;
    }

}