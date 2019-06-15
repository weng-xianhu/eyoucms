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


/**
 * 栏目位置
 */
class TagPosition extends Base
{
    public $tid = '';
    
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->tid = input("param.tid/s", ''); // 应用于栏目列表
        /*应用于文档列表*/
        $aid = input('param.aid/d', 0);
        if ($aid > 0) {
            $this->tid = M('archives')->where('aid', $aid)->getField('typeid');
        }
        /*--end*/
        /*tid为目录名称的情况下*/
        $this->tid = $this->getTrueTypeid($this->tid);
        /*--end*/
    }

    /**
     * 获取面包屑位置
     * @author wengxianhu by 2018-4-20
     */
    public function getPosition($typeid = '', $symbol = '', $style = 'crumb')
    {
        $typeid = !empty($typeid) ? $typeid : $this->tid;

        /*多语言*/
        if (!empty($typeid)) {
            $typeid = model('LanguageAttr')->getBindValue($typeid, 'arctype');
        }
        /*--end*/

        $basicConfig = tpCache('basic');
        $basic_indexname = !empty($basicConfig['basic_indexname']) ? $basicConfig['basic_indexname'] : '首页';
        $symbol = !empty($symbol) ? $symbol : $basicConfig['list_symbol'];

        /*首页链接*/
        $inletStr = '/index.php';
        $seo_inlet = config('ey_config.seo_inlet');
        1 == intval($seo_inlet) && $inletStr = '';

        $lang = input('param.lang/s', '');
        if (empty($lang)) {
            $home_url = $this->root_dir.'/'; // 支持子目录
        } else {
            $seoConfig = tpCache('seo', [], $lang);
            $seo_pseudo = !empty($seoConfig['seo_pseudo']) ? $seoConfig['seo_pseudo'] : config('ey_config.seo_pseudo');
            if (1 == $seo_pseudo) {
                $home_url = request()->domain().$this->root_dir.$inletStr; // 支持子目录
                if (!empty($inletStr)) {
                    $home_url .= '?';
                } else {
                    $home_url .= '/?';
                }
                $home_url .= http_build_query(['lang'=>$lang]);
            } else {
                $home_url = $this->root_dir.$inletStr.'/'.$lang; // 支持子目录
            }
        }
        /*--end*/

        // $symbol = htmlspecialchars_decode($symbol);
        $str = "<a href='{$home_url}' class='{$style}'>{$basic_indexname}</a>";
        $result = model('Arctype')->getAllPid($typeid);
        $i = 1;
        foreach ($result as $key => $val) {
            if ($i < count($result)) {
                $str .= " {$symbol} <a href='{$val['typeurl']}' class='{$style}'>{$val['typename']}</a>";
            } else {
                $str .= " {$symbol} <a href='{$val['typeurl']}'>{$val['typename']}</a>";
            }
            ++$i;
        }

        return $str;
    }
}