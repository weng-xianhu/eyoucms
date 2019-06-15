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
 * 文档基本信息
 */
class TagArcview extends Base
{
    public $aid = '';
    public $fieldLogic;
    
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->fieldLogic = new FieldLogic();
        /*应用于文档列表*/
        $this->aid = I('param.aid/d', 0);
        /*--end*/
    }

    /**
     * 获取栏目基本信息
     * @author wengxianhu by 2018-4-20
     */
    public function getArcview($aid = '', $addfields = '')
    {
        $aid = !empty($aid) ? $aid : $this->aid;

        if (empty($aid)) {
            echo '标签arcview报错：缺少属性 aid 值，或文档ID不存在。';
            return false;
        }

        /*文档信息*/
        $result = M("archives")->field('b.*, a.*')
            ->alias('a')
            ->join('__ARCTYPE__ b', 'b.id = a.typeid', 'LEFT')
            ->where('a.lang', $this->home_lang)
            ->find($aid);
        if (empty($result)) {
            echo '标签arcview报错：该文档ID('.$aid.')不存在。';
            return false;
        }
        /*--end*/
        $result['litpic'] = get_default_pic($result['litpic']); // 默认封面图

        // 获取查询的控制器名
        $channelInfo = model('Channeltype')->getInfo($result['channel']);
        $controller_name = $channelInfo['ctl_name'];
        $channeltype_table = $channelInfo['table'];

        /*栏目链接*/
        if ($result['is_part'] == 1) {
            $result['typeurl'] = $result['typelink'];
        } else {
            $result['typeurl'] = typeurl('home/'.$controller_name."/lists", $result);
        }
        /*--end*/

        /*文档链接*/
        if ($result['is_jump'] == 1) {
            $result['arcurl'] = $result['jumplinks'];
        } else {
            $result['arcurl'] = arcurl('home/'.$controller_name.'/view', $result);
        }
        /*--end*/

        /*附加表*/
        if (!empty($addfields)) {
            $addfields = str_replace('，', ',', $addfields); // 替换中文逗号
            $addfields = trim($addfields, ',');
        } else {
            $addfields = '*';
        }
        $tableContent = $channeltype_table.'_content';
        $row = M($tableContent)->field($addfields)->where('aid',$aid)->find();
        $result = array_merge($result, $row);
        $result = $this->fieldLogic->getChannelFieldList($result, $result['channel']); // 自定义字段的数据格式处理
        /*--end*/

        $result = view_logic($aid, $result['channel'], $result);

        return $result;
    }
}