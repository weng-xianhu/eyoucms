<?php
/**
 * 易购CMS
 * ============================================================================
 * 版权所有 2016-2028 海南易而优科技有限公司，并保留所有权利。
 * 网站地址: http://www.ebuycms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 易而优团队 by 陈风任 <491085389@qq.com>
 * Date: 2019-11-28
 */

namespace app\admin\logic;

use think\Model;
use think\Db;
/**
 * 栏目逻辑定义
 * @package common\Logic
 */
class NavigationLogic extends Model
{
    /**
     * 全部栏目
     */
    public function GetAllArctype($type_id = 0)
    {
        $where = [
            'weapp_code' => '',
            'is_del' => 0,
            'status' => 1,
            'lang' => get_current_lang(),
        ];
        $field = 'id, parent_id, typename, dirname, litpic';

        // 查询所有可投稿的栏目
        $ArcTypeData = Db::name('arctype')->field($field)->where($where)->select();
        $ids = array_column($ArcTypeData, 'id');
        $id_arr = [];
        $parent_ids = array_column($ArcTypeData, 'parent_id');
        foreach ($parent_ids as $k => $v){
            if (0 == $v){
                $id_arr[] = $ids[$k];
            }
        }
        $ParentIds = array_unique($parent_ids);
        if (!empty($id_arr)){
            foreach ($id_arr as $k){
                if (!in_array($k,$parent_ids)){
                    $ParentIds[] = $k;
                }
            }
        }
        // 读取上级ID并去重读取上级栏目 $ParentIds
        $PidData = Db::name('arctype')->field($field)->where('id', 'IN', $ParentIds)->select();

        static $seo_pseudo = null;
        if (null === $seo_pseudo) {
            $seoConfig = tpCache('seo');
            $seo_pseudo = !empty($seoConfig['seo_pseudo']) ? $seoConfig['seo_pseudo'] : config('ey_config.seo_pseudo');
        }

        // 下拉框拼装
        $HtmlCode = '<select name="type_id" id="type_id" onchange="SyncData(this);">';
        $HtmlCode .= '<option id="arctype_default" value="0">请选择栏目</option>';

        foreach ($PidData as $yik => $yiv) {
            /*栏目路径*/
            if (2 == $seo_pseudo) {
                // 生成静态
                $typeurl = ROOT_DIR . "/index.php?m=home&c=Lists&a=index&tid={$yiv['id']}";
            } else {
                // 动态或伪静态
                $typeurl = typeurl("home/Lists/index", $yiv, true, false, $seo_pseudo, null);
                $typeurl = auto_hide_index($typeurl);
            }
            /* END */
            /*是否选中*/
            $style1 = $type_id == $yiv['id'] ? 'selected' : '';
            /* END */
            if (0 == $yiv['parent_id']) {
                /*一级下拉框*/
                $HtmlCode .= '<option value="'.$yiv['id'].'" data-typeurl="'.$typeurl.'" data-typename="'.$yiv['typename'].'" '.$style1.'>'.$yiv['typename'].'</option>';
                /* END */
                $type = 0;
            } else {
                /*二级下拉框*/
                $HtmlCode .= '<option value="'.$yiv['id'].'" data-typeurl="'.$typeurl.'" data-typename="'.$yiv['typename'].'" '.$style1.'>&nbsp; &nbsp;'.$yiv['typename'].'</option>';
                /* END */
                $type = 1;
            }

            foreach ($ArcTypeData as $erk => $erv) {
                /*栏目路径*/
                if (2 == $seo_pseudo) {
                    // 生成静态
                    $typeurl = ROOT_DIR . "/index.php?m=home&c=Lists&a=index&tid={$erv['id']}";
                } else {
                    // 动态或伪静态
                    $typeurl = typeurl("home/Lists/index", $erv, true, false, $seo_pseudo, null);
                    $typeurl = auto_hide_index($typeurl);
                }
                /* END */

                if ($erv['parent_id'] == $yiv['id']) {
                    if (0 == $type) {
                        /*是否选中*/
                        $style1 = $type_id == $erv['id'] ? 'selected' : '';
                        /* END */
                        /*二级下拉框*/
                        $HtmlCode .= '<option value="'.$erv['id'].'" data-typeurl="'.$typeurl.'" data-typename="'.$erv['typename'].'" '.$style1.'>&nbsp; &nbsp;'.$erv['typename'].'</option>';
                        /* END */
                    } else {
                        /*三级下拉框*/
                        $HtmlCode .= '<option value="'.$erv['id'].'" data-typeurl="'.$typeurl.'" data-typename="'.$erv['typename'].'">&nbsp; &nbsp; &nbsp; &nbsp;'.$erv['typename'].'</option>';
                        /* END */
                    }
                }
            }
        }
        $HtmlCode .= '</select>';
        return $HtmlCode;
    }
    // 前台功能列表
    public function ForegroundFunction()
    {
        return $ReturnData = [
            0 => [
                'title' => '首页',
                'url'   => "web_cmsurl"
            ],
            1 => [
                'title' => '个人中心',
                'url'   => "index"
            ],
            2 => [
                'title' => '我的信息',
                'url'   => "user_info"
            ],
            3 => [
                'title' => '我的收藏',
                'url'   => "my_collect"
            ],
            4 => [
                'title' => '财务明细',
                'url'   => "consumer_details"
            ],
            5 => [
                'title' => '购物车',
                'url'   => 'shop_cart_list'
            ],
            6 => [
                'title' => '收货地址',
                'url'   => "shop_address_list"
            ],
            7 => [
                'title' => '我的订单',
                'url'   => "shop_centre"
            ],
            8 => [
                'title' => '我的评价',
                'url'   => "my_comment"
            ],
            9 => [
                'title' => '投稿列表',
                'url'   => "release_centre"
            ],
            10 => [
                'title' => '我要投稿',
                'url'   => "article_add"
            ],

        ];
    }
}