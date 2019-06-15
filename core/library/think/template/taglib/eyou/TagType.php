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

use app\home\logic\FieldLogic;

/**
 * 栏目基本信息
 */
class TagType extends Base
{
    public $tid = '';
    public $fieldLogic;
    
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->fieldLogic = new FieldLogic();
        $this->tid = I("param.tid/s", ''); // 应用于栏目列表
        /*应用于文档列表*/
        $aid = I('param.aid/d', 0);
        if ($aid > 0) {
            $this->tid = M('archives')->where('aid', $aid)->getField('typeid');
        }
        /*--end*/
        /*typeid|tid为目录名称的情况下*/
        $this->tid = $this->getTrueTypeid($this->tid);
        /*--end*/
    }

    /**
     * 获取栏目基本信息
     * @author wengxianhu by 2018-4-20
     */
    public function getType($typeid = '', $type = 'self', $addfields = '')
    {
        $typeid = !empty($typeid) ? $typeid : $this->tid;

        if (empty($typeid)) {
            echo '标签type报错：缺少属性 typeid 值，或栏目ID不存在。';
            return false;
        }

        if (!empty($typeid)) {
            $typeid = model('LanguageAttr')->getBindValue($typeid, 'arctype'); // 多语言
            if (empty($typeid)) {
                echo '标签type报错：找不到与第一套【'.$this->main_lang.'】语言关联绑定的属性 typeid 值 。';
                return false;
            }
        }

        $result = array();

        switch ($type) {
            case 'top':
                $result = $this->getTop($typeid);
                break;
            
            default:
                $result = $this->getSelf($typeid);
                break;
        }
        $result = $this->fieldLogic->getTableFieldList($result, config('global.arctype_channel_id'));
        
        /*当前单页栏目的内容信息*/
        if (!empty($addfields) && isset($result['nid']) && $result['nid'] == 'single') {
            $addfields = str_replace('single_content', 'content', $addfields); // 兼容1.0.9之前的版本
            $addfields = str_replace('，', ',', $addfields); // 替换中文逗号
            $addfields = trim($addfields, ',');
            $row = M('single_content')->field($addfields)->where('typeid',$result['id'])->find();
            $row = $this->fieldLogic->getChannelFieldList($row, $result['current_channel']);
            $result = array_merge($row, $result);
        }
        /*--end*/

        return $result;
    }

    /**
     * 获取当前栏目基本信息
     * @author wengxianhu by 2018-4-20
     */
    public function getSelf($typeid)
    {
        $result = model("Arctype")->getInfo($typeid); // 当前栏目信息

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