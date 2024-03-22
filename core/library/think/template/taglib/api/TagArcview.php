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
use app\home\logic\FieldLogic;

/**
 * 文档基本信息
 */
class TagArcview extends Base
{
    public $fieldLogic;
    
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->fieldLogic = new FieldLogic();
    }

    /**
     * 获取文档详情
     * @author wengxianhu by 2018-4-20
     */
    public function getArcview($aid = '', $typeid = '', $addfields = '', $titlelen = 100, $joinaid = '')
    {
        $result = [];
        if (empty($aid) && !empty($typeid)) { // 单页栏目详情页
            $result = $this->getSingleView($typeid, $addfields, $titlelen);
        } else { // 文档详情页
            $result = $this->getArchivesView($aid, $addfields, $titlelen, $joinaid);
        }

        return $result;
    }

    /**
     * 获取文档数据
     * @author wengxianhu by 2018-4-20
     */
    public function getArchivesView($aid = '', $addfields = '', $titlelen = '', $joinaid = '')
    {
        $aid = !empty($aid) ? $aid : $this->aid;
        $joinaid !== '' && $aid = $joinaid;

        if (empty($aid)) {
            return false;
        }

        /*文档信息*/
        $field = 'a.*,b.typename';
        $result = Db::name("archives")->field($field)
            ->alias('a')
            ->join('__ARCTYPE__ b', 'b.id = a.typeid', 'LEFT')
            ->find($aid);
        if (empty($result)) {
            return false;
        }
        /*--end*/
        $result['title'] = text_msubstr($result['title'], 0, $titlelen, false);
        $result['litpic'] = $this->get_default_pic($result['litpic']); // 默认封面图
        $result['add_time_format'] = $this->time_format($result['add_time']);
        $result['add_time'] = date('Y-m-d', $result['add_time']);
        $result['real_sales'] = $result['sales_num']; // 真实总销量
        $result['sales_num'] = $result['sales_all']; // 总虚拟销量
        // 获取查询的控制器名
        $channelInfo = model('Channeltype')->getInfo($result['channel']);
        $channeltype_table = $channelInfo['table'];
        /*附加表*/
        $addtableName = $channeltype_table.'_content';
        if (!empty($addfields)) {
            $addfields = str_replace('，', ',', $addfields); // 替换中文逗号
            $addfields = trim($addfields, ',');
            /*过滤不相关的字段*/
            $addfields_arr = explode(',', $addfields);
            $extFields = Db::name($addtableName)->getTableFields();
            $addfields_arr = array_intersect($addfields_arr, $extFields);
            if (!empty($addfields_arr) && is_array($addfields_arr)) {
                $addfields = implode(',', $addfields_arr);
                if (strstr(",{$addfields},", ',content,')){
                    $addfields .= ',content_ey_m';
                }
            } else {
                $addfields = '*';
            }
            /*end*/
        } else {
            $addfields = '*';
        }
        $row = Db::name($addtableName)->field($addfields)->where('aid',$aid)->find();
        if (is_array($row)) {
            $result = array_merge($result, $row);
            isset($result['total_duration']) && $result['total_duration'] = gmSecondFormat($result['total_duration'], ':');
        } else {
            $saveData = [
                'aid'           => $aid,
                'add_time'      => getTime(),
                'update_time'   => getTime(),
            ];
            Db::name($addtableName)->save($saveData);
        }
        $result = $this->fieldLogic->getChannelFieldList($result, $result['channel']); // 自定义字段的数据格式处理
        /*--end*/
        $result['users_discount_price'] = $result['users_price'];
        $users = session('users');
        if (!empty($result['users_price']) && !empty($users['users_id'])){
            $discount = Db::name('users_level')->where('level_id',$users['level'])->value('discount');
            if (100 > $discount){
                $result['users_discount_price'] = sprintf("%.2f",$result['users_price']*($discount/100));
            }
        }
        $result = view_logic($aid, $result['channel'], $result, true);
        // 手机端详情内容
        if (isset($result['content_ey_m'])) {
            if (!empty($result['content_ey_m'])) {
                $result['content'] = $result['content_ey_m'];
            }
            unset($result['content_ey_m']);
        }

        //检测是否安装秒杀插件
        if (is_dir('./weapp/Seckill/')) {
            $SeckillRow = model('Weapp')->getWeappList('Seckill');
            if (!empty($SeckillRow) && 1 != intval($SeckillRow['status'])) {
                $result['seckill_goods_id'] = false;
            }else{
                $tagWeappSeckill = new \think\template\taglib\api\TagWeappSeckill;
                $result['seckill_goods_id'] = $tagWeappSeckill->guideSeckill($aid);
            }
        } else {
            $result['seckill_goods_id'] = false;
        }

        // 是否安装积分商城插件
        $result['points_goods_id'] = false;
        $weappInfo = model('ShopPublicHandle')->getWeappPointsShop();
        if (!empty($weappInfo)) {
            // 调用积分商城逻辑层方法
            $pointsGoodsModel = new \app\plugins\model\PointsGoods();
            $result['points_goods_id'] = $pointsGoodsModel->getPointsShopGoodsID($aid);
        }

        return [
            'data'=> !empty($result) ? $result : false,
        ];
    }

    /**
     * 单页栏目详情
     * @param int $typeid 栏目ID
     */
    private function getSingleView($typeid = '', $addfields = '', $titlelen = '')
    {
        if (empty($typeid)) {
            return false;
        }

        $cacheKey = 'api-'.md5(__CLASS__.__FUNCTION__.json_encode(func_get_args()));
        $redata = cache($cacheKey);
        if (empty($redata['data'])) {
            $result = $this->getSingleInfo($typeid, $addfields);
            $result['typename'] = text_msubstr($result['typename'], 0, $titlelen, false);
            $redata = [
                'data'=> !empty($result) ? $result : false,
            ];
            cache($cacheKey, $redata, null, 'arctype');
        }

        return $redata;
    }

    /**
     * 获取单页栏目记录
     * @author wengxianhu by 2017-7-26
     */
    private function getSingleInfo($typeid, $addfields = '')
    {
        $result = $this->readContentFirst($typeid, $addfields); // 文档基本信息
        if (!empty($result)) {
            $result['seo_title'] = $this->set_arcseotitle($result['typename'], $result['seo_title']);
            $result['add_time'] = date('Y-m-d H:i:s', $result['update_time']); // 格式化更新时间
            // 手机端详情内容
            if (isset($result['content_ey_m'])) {
                if (!empty($result['content_ey_m'])) {
                    $result['content'] = $result['content_ey_m'];
                }
                unset($result['content_ey_m']);
            }
            $result['content'] = $this->html_httpimgurl($result['content'], true); // 转换内容图片为http路径
            if (empty($result['litpic'])) {
                $result['is_litpic'] = 0; // 无封面图
            } else {
                $result['is_litpic'] = 1; // 有封面图
            }
            $result['litpic'] = $this->get_default_pic($result['litpic']); // 默认封面图
            // $result['typeurl'] = '/pages/article/single?typeid='.$result['typeid'];
            // $result['arcurl'] = '/pages/article/single?typeid='.$result['typeid'];
            unset($result['id']);
            unset($result['aid']);
            unset($result['update_time']);
            unset($result['templist']);

            if (!empty($addfields)) {
                $result = $this->fieldLogic->getTableFieldList($result, config('global.arctype_channel_id')); // 自定义字段的数据格式处理
                $result = $this->fieldLogic->getChannelFieldList($result, $result['channel']); // 自定义字段的数据格式处理
            }
        }

        return $result;
    }

    /**
     * 读取指定栏目ID下有内容的栏目信息，只读取每一级的第一个栏目
     * @param intval $typeid 栏目ID
     * @return array
     */
    private function readContentFirst($typeid, $addfields = '')
    {
        $result = false;
        while (true)
        {
            $result = $this->getInfoByTypeid($typeid, $addfields);
            if (empty($result['content']) && preg_match('/^lists_single(_(.*))?\.htm$/i', $result['templist'])) {
                $map = [
                    'parent_id'       => $result['typeid'],
                    'current_channel' => 6,
                    'is_hidden'       => 0,
                    'status'          => 1,
                    'is_del'          => 0,
                ];
                $row = Db::name('arctype')->field('id,current_channel')->where($map)->order('sort_order asc')->find(); // 查找下一级的单页模型栏目
                if (empty($row)) { // 不存在并返回当前栏目信息
                    break;
                } elseif (6 == $row['current_channel']) { // 存在且是单页模型，则进行继续往下查找，直到有内容为止
                    $typeid = $row['id'];
                }
            } else {
                break;
            }
        }

        return $result;
    }

    /**
     * 获取单条记录
     * @author wengxianhu by 2017-7-26
     */
    private function getInfoByTypeid($typeid, $addfields = '')
    {
        $field = 'c.typeid,a.typename,a.litpic,a.seo_title,a.seo_keywords,a.seo_description,a.templist';
        if (empty($addfields)) {
            $field .= ',c.*';
        } else {
            $addfields = str_replace('，', ',', $addfields); // 替换中文逗号
            $addfields = trim($addfields, ',');
            /*过滤不相关的字段*/
            $addfields_arr = explode(',', $addfields);
            $extFields = Db::name('single_content')->getTableFields();
            $addfields_arr = array_intersect($addfields_arr, $extFields);
            if (!empty($addfields_arr) && is_array($addfields_arr)) {
                foreach ($addfields_arr as $key => $val) {
                    $addfields_arr[$key] = 'c.'.$val;
                }
                $addfields = implode(',', $addfields_arr);
                if (strstr(",{$addfields},", ',c.content,')){
                    $addfields .= ',c.content_ey_m';
                }
            } else {
                $addfields = 'c.*';
            }
            /*end*/
            $field .= ','.$addfields;
        }

        $result = Db::name('arctype')->field($field)
            ->alias('a')
            ->join('__SINGLE_CONTENT__ c', 'c.typeid = a.id', 'LEFT')
            ->where(['a.id'=>$typeid,'a.current_channel'=>6])
            ->cache(true,EYOUCMS_CACHE_TIME,"arctype")
            ->find();
        // 手机端详情内容
        if (isset($result['content_ey_m'])) {
            if (!empty($result['content_ey_m'])) {
                $result['content'] = $result['content_ey_m'];
            }
            unset($result['content_ey_m']);
        }

        return $result;
    }
}