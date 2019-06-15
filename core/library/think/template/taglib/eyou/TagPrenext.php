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

use think\Request;

/**
 * 内容页上下篇
 */
class TagPrenext extends Base
{
    public $aid = 0;
    
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->aid = I("param.aid/d", 0);
    }

    /**
     * 获取内容页上下篇
     * @author wengxianhu by 2018-4-20
     */
    public function getPrenext($get = 'pre')
    {
        $aid = $this->aid;
        if (empty($aid)) {
            echo '标签prenext报错：只能用在内容页。';
            return false;
        }

        $channelRes = model('Channeltype')->getInfoByAid($aid);
        $channel = $channelRes['channel'];
        $typeid = $channelRes['typeid'];
        $controller_name = $channelRes['ctl_name'];

        if ($get == 'next') {
            /* 下一篇 */
            $result = M('archives')->field('b.*, a.*')
                ->alias('a')
                ->join('__ARCTYPE__ b', 'b.id = a.typeid', 'LEFT')
                ->where([
                    'a.typeid'  => $typeid,
                    'a.aid'     => ['GT', $aid],
                    'a.channel' => $channel,
                    'a.status'  => 1,
                    'a.lang'    => $this->home_lang,
                    'a.is_del'  => 0,
                    'a.arcrank' => ['EGT', 0],
                ])
                ->order('a.aid asc')
                ->find();
            if (!empty($result)) {
                $result['arcurl'] = arcurl('home/'.$controller_name.'/view', $result);
                /*封面图*/
                if (empty($result['litpic'])) {
                    $result['is_litpic'] = 0; // 无封面图
                } else {
                    $result['is_litpic'] = 1; // 有封面图
                }
                $result['litpic'] = get_default_pic($result['litpic']); // 默认封面图
                /*--end*/
            }
        } else {
            /* 上一篇 */
            $result = M('archives')->field('b.*, a.*')
                ->alias('a')
                ->join('__ARCTYPE__ b', 'b.id = a.typeid', 'LEFT')
                ->where([
                    'a.typeid'  => $typeid,
                    'a.aid'     => ['LT', $aid],
                    'a.channel' => $channel,
                    'a.status'  => 1,
                    'a.lang'    => $this->home_lang,
                    'a.is_del'  => 0,
                    'a.arcrank' => ['EGT', 0],
                ])
                ->order('a.aid desc')
                ->find();
            if (!empty($result)) {
                $result['arcurl'] = arcurl('home/'.$controller_name.'/view', $result);
                /*封面图*/
                if (empty($result['litpic'])) {
                    $result['is_litpic'] = 0; // 无封面图
                } else {
                    $result['is_litpic'] = 1; // 有封面图
                }
                $result['litpic'] = get_default_pic($result['litpic']); // 默认封面图
                /*--end*/
            }
        }

        return $result;
    }
}