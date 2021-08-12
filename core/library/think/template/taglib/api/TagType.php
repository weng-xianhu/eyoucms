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
 * 栏目基本信息
 */
class TagType extends Base
{
    public $fieldLogic;
    
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->fieldLogic = new FieldLogic();
        if ($this->aid > 0) { // 应用于文档详情页
            $this->tid = Db::name('archives')->where('aid', $this->aid)->cache(true,EYOUCMS_CACHE_TIME,"archives")->getField('typeid');
        }
    }

    /**
     * 获取栏目基本信息
     * @author wengxianhu by 2018-4-20
     */
    public function getType($typeid = '', $type = 'self', $addfields = '', $infolen = '')
    {
        $typeid = !empty($typeid) ? $typeid : $this->tid;
        if (empty($typeid)) {
            $redata = [
                'data'  => false,
            ];
            return $redata;
        }

        $args = [$typeid,$type,$addfields,$infolen];
        $cacheKey = 'think\template\taglib\api\TagType-getType-'.json_encode($args);
        $redata = cache($cacheKey);
        if (!empty($redata['data'])) { // 启用缓存
            return $redata;
        }

        switch ($type) {
            case 'top':
                $result = $this->getTop($typeid);
                break;
            
            default:
                $result = $this->getSelf($typeid, $addfields);
                break;
        }
        isset($result['litpic']) && $result['litpic'] = $this->get_default_pic($result['litpic']);
        isset($result['seo_title']) && $result['seo_title'] = $this->set_arcseotitle($result['typename'], $result['seo_title']);
        if (!empty($infolen) && !empty($result['seo_description'])) {
            $result['seo_description'] = text_msubstr($result['seo_description'], 0, $infolen, false);
        }
        $result = $this->fieldLogic->getTableFieldList($result, config('global.arctype_channel_id'));
        
        /*当前单页栏目的内容信息*/
        if (!empty($addfields) && $result['current_channel'] == 6) {
            $addfields = str_replace('，', ',', $addfields); // 替换中文逗号
            $addfields = trim($addfields, ',');
            $rowExt = Db::name('single_content')->field($addfields)->where('typeid', $result['id'])->find();
            $rowExt = $this->fieldLogic->getChannelFieldList($rowExt, $result['current_channel'], false, true);
            is_array($rowExt) && $result = array_merge($result, $rowExt);
        }
        /*--end*/

        $redata = [
            'data'  => !empty($result) ? $result : false,
        ];
        cache($cacheKey, $redata, null, 'arctype');

        return $redata;
    }

    /**
     * 获取当前栏目基本信息
     * @author wengxianhu by 2018-4-20
     */
    public function getSelf($typeid, $addfields = '')
    {
        $field = 'id,id as typeid,typename,current_channel,parent_id,topid,grade,litpic,seo_title,seo_keywords,seo_description';
        $result = Db::name('arctype')->field($field)
            ->where(['id'=>$typeid])
            ->find();

        return $result;
    }

    /**
     * 获取当前栏目的第一级栏目基本信息
     * @author wengxianhu by 2018-4-20
     */
    public function getTop($typeid)
    {
        $parent_list = model('Arctype')->getAllPid($typeid); // 获取当前栏目的所有父级栏目
        $result = current($parent_list); // 第一级栏目

        return $result;
    }
}