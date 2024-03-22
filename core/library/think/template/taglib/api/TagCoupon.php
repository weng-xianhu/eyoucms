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

/**
 * 优惠券
 */
class TagCoupon extends Base
{
    private $weapp_coupon = false;
    private $coupon_table = 'shop_coupon';
    private $coupon_use_table = 'shop_coupon_use';
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        // 是否安装优惠券插件
        $weappInfo = model('ShopPublicHandle')->getWeappInfo("Coupons");
        if (!empty($weappInfo['status']) && 1 == $weappInfo['status']) {
            $this->weapp_coupon = true;
            $this->coupon_table = 'weapp_coupons';
            $this->coupon_use_table = 'weapp_coupons_use';
        }
    }

    /**
     * 获取优惠券列表
     */
    public function getList($page = 1, $pagesize = 10,$users_id = 0,$parse = [])
    {

        $map = [
            'status' => 1,
            'is_del' => 0
        ];
        $map['start_date'] = ['<=', getTime()];
        $map['end_date'] = ['>=', getTime()];
        if (!empty($parse['coupon_type'])) $map['coupon_type'] = $parse['coupon_type'];
        $order = 'sort_order asc,coupon_price desc,coupon_id desc';

        $paginate = array(
            'page' => $page,
        );
        $pages = Db::name($this->coupon_table)->where($map)->order($order)->paginate($pagesize, false, $paginate);
        $result = $pages->toArray();

        $result_coupon_ids = [];
        if (!empty($result['data'])) {
            foreach ($result['data'] as $k => $v) {
                $v['conditions_use'] = floatval($v['conditions_use']);
                if (1 == $v['coupon_form']){
                    $v['coupon_form_name'] = '满减券';
                }elseif (2 == $v['coupon_form']){
                    $v['coupon_form_name'] = '满折券';
                    $v['coupon_discount'] = floatval( $v['coupon_discount']);
                }
                $v['start_date'] = date('Y/m/d',$v['start_date']);
                $v['end_date'] = date('Y/m/d',$v['end_date']);
                $result['data'][$k] = $v;
                array_push($result_coupon_ids, $v['coupon_id']);
            }
        }
        //已领取的-有效的-未使用-优惠券
        if (!empty($users_id)) {
            $use_where = [
                'use_status' => 0,
                'users_id' => $users_id,
            ];
            $use_where['start_time'] = ['<=', getTime()];
            $use_where['end_time'] = ['>=', getTime()];
            $use_where['coupon_id'] = ['in', $result_coupon_ids];
            $user_data = Db::name($this->coupon_use_table)->where($use_where)->select();
            if (!empty($user_data)) {
                foreach ($user_data as $k => $v) {
                    foreach ($result['data'] as $key => $val){
                        if ($v['coupon_id'] == $val['coupon_id']) $result['data'][$key]['geted'] = 1;
                    }
                }
            }
        }

        return empty($result) ? [] : $result;
    }

    //获取某优惠券可以使用的商品列表
    public function getGoodsList($param = array())
    {
        $channeltype = 2;
        if (empty($param['coupon_id'])) return false;
        $coupon = Db::name($this->coupon_table)->where('coupon_id',$param['coupon_id'])->find();
        if (3 == $coupon['coupon_type']){
            $param['typeid'] = $coupon['arctype_id'];
        }elseif (2 == $coupon['coupon_type']){
            $param['aid'] = $coupon['product_id'];
        }
        $field = 'a.*,b.typename';
        $titlelen = !empty($param['titlelen']) ? intval($param['titlelen']) : 100;
        $infolen = !empty($param['infolen']) ? intval($param['infolen']) : 160;
        $orderby = !empty($param['orderby']) ? trim($param['orderby']) : '';
        $arcrank = empty($param['arcrank']) ? 'off' : $param['arcrank'];
        $page = !empty($param['page']) ? intval($param['page']) : 1;
        if (!empty($param['limit'])) {
            $pagesize = !empty($param['limit']) ? intval($param['limit']) : 15;
        } else {
            $pagesize = !empty($param['pagesize']) ? intval($param['pagesize']) : 15;
        }

        if (!empty($param['orderway'])) {
            $ordermode = !empty($param['orderway']) ? trim($param['orderway']) : 'desc';
        } else {
            $ordermode = !empty($param['ordermode']) ? trim($param['ordermode']) : 'desc';
        }

        // 查询条件
        $condition = [];
        foreach (array('keywords','keyword','typeid','flag','noflag','aid') as $key) {
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
                }  elseif ($key == 'typeid') {
                    array_push($condition, "a.typeid IN ({$param[$key]})");
                }  elseif ($key == 'flag') {
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
                } elseif ($key == 'aid') {
                    array_push($condition, "a.aid IN ({$param[$key]})");
                } else {
                    array_push($condition, "a.{$key} = '".$param[$key]."'");
                }
            }
        }
        array_push($condition, "a.channel = 2 ");
        array_push($condition, "a.arcrank > -1");
        array_push($condition, "a.status = 1");
        array_push($condition, "a.is_del = 0"); // 回收站功能
        array_push($condition, "a.lang = '".self::$home_lang."'");
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
        $orderby = getOrderBy($orderby, $ordermode);

        // 获取查询的表名
        $controller_name = 'Product';
        $channeltype_table = $channeltype_nid = 'product';

        $paginate = array(
            'page'  => $page,
        );
        $pages = Db::name('archives')
            ->field($field)
            ->alias('a')
            ->join('__ARCTYPE__ b', 'b.id = a.typeid', 'LEFT')
            ->where($where_str)
            ->orderRaw($orderby)
            ->paginate($pagesize, false, $paginate);
        $result = $pages->toArray();

        $users = model('v1.User')->getUser(false);
        $aidArr = $adminArr = $usersArr = $mediaArr = [];
        foreach ($result['data'] as $key => $val) {
            array_push($aidArr, $val['aid']); // 收集文档ID
            array_push($adminArr, $val['admin_id']); // 收集admin_id
            array_push($usersArr, $val['users_id']); // 收集users_id
            $val['title'] = htmlspecialchars_decode($val['title']);
            $val['title'] = text_msubstr($val['title'], 0, $titlelen, false);
            $val['seo_description'] = text_msubstr($val['seo_description'], 0, $infolen, false);
            $val['seo_title'] = $this->set_arcseotitle($val['typename'], $val['seo_title']);
            $val['litpic'] = $this->get_default_pic($val['litpic']); // 默认封面图
            $val['add_time_format'] = $this->time_format($val['add_time']);
            $val['add_time'] = date('Y-m-d', $val['add_time']);

            $val['old_price'] = unifyPriceHandle($val['users_price']);
            $resultData = $this->handle_price($val['users_price'], $users, $val['aid'], $val['users_discount_type']);
            $val['users_price'] = $resultData['users_price'];
            $val['level_discount'] = $resultData['level_discount'];
            $val['users_price_arr'] = explode('.', $val['users_price']);

            $val['real_sales'] = $val['sales_num']; // 真实总销量
            $val['sales_num'] = $val['sales_all']; // 总虚拟销量

            $result['data'][$key] = $val;
            array_push($aidArr, $val['aid']); // 文档ID数组
        }

        //获取文章作者的信息 需要传值arcrank = on
        if ('on' == $arcrank) {
            $field = 'username,nickname,head_pic,users_id,admin_id,sex';
            $userslist = Db::name('users')->field($field)
                ->where('admin_id','in',$adminArr)
                ->whereOr('users_id','in',$usersArr)
                ->select();
            foreach ($userslist as $key => $val) {
                $val['head_pic'] = $this->get_head_pic($val['head_pic'], false, $val['sex']);
                empty($val['nickname']) && $val['nickname'] = $val['username'];
                if (!empty($val['admin_id'])) {
                    $adminLitpicArr[$val['admin_id']] = $val;
                }
                if (!empty($val['users_id'])) {
                    $usersLitpicArr[$val['users_id']] = $val;
                }
            }
            $adminLitpic = Db::name('users')->field($field)->where('admin_id','>',0)->order('users_id asc')->find();
            $adminLitpic['head_pic'] = $this->get_head_pic($adminLitpic['head_pic'], false, $adminLitpic['sex']);
            empty($adminLitpic['nickname']) && $adminLitpic['nickname'] = $adminLitpic['username'];

            foreach ($result['data'] as $key => $val) {
                if (!empty($val['users_id'])) {
                    $users = !empty($usersLitpicArr[$val['users_id']]) ? $usersLitpicArr[$val['users_id']] : [];
                } elseif (!empty($val['admin_id'])) {
                    $users = !empty($adminLitpicArr[$val['admin_id']]) ? $adminLitpicArr[$val['admin_id']] : [];
                } else {
                    $users = $adminLitpic;
                }
                !empty($users) && $val['users'] = $users;
                $result['data'][$key] = $val;
            }
        }

        /*附加表*/
        if (!empty($addfields) && !empty($aidArr)) {
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
            if (!empty($addfields)) {
                $addfields = ','.$addfields;
                if (strstr(",{$addfields},", ',content,')){
                    if (in_array($channeltype, [1,2,3,4,5,6,7])) {
                        $addfields .= ',content_ey_m';
                    } else {
                        if (in_array($extFields, ['content_ey_m'])) {
                            $addfields .= ',content_ey_m';
                        }
                    }
                }
                $resultExt = M($addtableName)->field("aid {$addfields}")->where('aid','in',$aidArr)->getAllWithIndex('aid');
                /*自定义字段的数据格式处理*/
                $resultExt = $this->fieldLogic->getChannelFieldList($resultExt, $channeltype, true);
                /*--end*/
                foreach ($result['data'] as $key => $val) {
                    $valExt = !empty($resultExt[$val['aid']]) ? $resultExt[$val['aid']] : array();
                    if (strstr(",{$addfields},", ',content,') && !empty($valExt['content_ey_m'])){
                        $valExt['content'] = $valExt['content_ey_m'];
                    }
                    if (isset($valExt['content_ey_m'])) {unset($valExt['content_ey_m']);}
                    if (!empty($valExt['content'])) {
                        $valExt['content_img_list'] = $this->get_content_img($valExt['content']);
                    }
                    $val = array_merge($valExt, $val);
                    $result['data'][$key] = $val;
                }
            }
        }
        /*--end*/


        empty($result['data']) && $result['data'] = false;

        $redata = $result;

        return $redata;
    }
    private function handle_price($users_price = 0, $users = [], $aid = 0, $users_discount_type = 0)
    {
        $result = [
            'level_discount' => 100,
            'users_price' => $users_price,
        ];
        if (!empty($users['level'])) {
            $level_discount = !empty($users['level_discount']) ? intval($users['level_discount']) : 100;
            $result['level_discount'] = intval($level_discount);
            if (!empty($level_discount) && 100 !== intval($level_discount)) {
                $level_discount = intval($level_discount) / intval(100);
                $users_price = 2 === intval($users_discount_type) ? floatval($users_price) : floatval($users_price) * floatval($level_discount);
            }
        }
        if (1 === intval($users_discount_type)) {
            $users_price = model('ShopPublicHandle')->handleUsersDiscountPrice($aid, $users['level']);
        }
        $result['users_price'] = unifyPriceHandle($users_price);

        return $result;
    }
}