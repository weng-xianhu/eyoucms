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
 * arclist列表分页标签
 */
class TagArcpagelist extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     *  获取ajax分页
     *
     * @author wengxianhu by 2018-4-20
     * @access    public
     * @param     string  $tagid  标签id
     * @param     string  $pagesize  分页显示条数
     * @return    array
     */
    public function getArcpagelist($tagid = '', $pagesize = 0, $tips = '', $loading = '')
    {
        if (empty($tagid)) {
            return '标签arcpagelist报错：缺少属性 tagid 。';
        }

        empty($tips) && $tips = '没有数据了';

        if (empty($pagesize)) {
            $arcmulti_db = Db::name('arcmulti');
            $arcmultiRow = $arcmulti_db->field('pagesize')->where(['tagid'=>$tagid])->find();
            $pagesize = $arcmultiRow['pagesize'];
        }

        $arcmulti_db = Db::name('arcmulti');
        $arcmultiRow = $arcmulti_db->field('attstr,querysql')->where(['tagid'=>$tagid])->find();
        if (empty($arcmultiRow)) {
            return false;
        } else {
            // 取出属性并解析为变量
            $attarray = unserialize(stripslashes($arcmultiRow['attstr']));

            $querysql = preg_replace('#LIMIT(\s+)(\d+)(,\d+)?#i', '', $arcmultiRow['querysql']);
            $querysql = preg_replace('#SELECT(\s+)(.*)(\s+)FROM#i', 'SELECT COUNT(*) AS totalNum FROM', $querysql);
            $queryRow = Db::query($querysql);
            $totalNum = !empty($queryRow) ? $queryRow[0]['totalNum'] : 0;
            if (intval($attarray['row']) >= $totalNum) {
                return false;
            }
        }

        $version = getCmsVersion();
        $result['onclick'] = ' data-page="1" data-tips="'.$tips.'" data-loading="'.$loading.'" data-root_dir="'.$this->root_dir.'" onClick="tag_arcpagelist_multi(this,\''.$tagid.'\','.intval($pagesize).');" ';
        $result['js'] = <<<EOF
<script type="text/javascript" src="{$this->root_dir}/public/static/common/js/tag_arcpagelist.js?v={$version}"></script>
EOF;

        return $result;
    }
}