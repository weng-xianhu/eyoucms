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

namespace app\home\controller;

class Tags extends Base
{
    public function _initialize() {
        parent::_initialize();
    }

    /**
     * 标签主页
     */
    public function index()
    {
        return $this->lists();
    }

    /**
     * 标签列表
     */
    public function lists()
    {
        $param = I('param.');
        
        $tagid = isset($param['tagid']) ? $param['tagid'] : '';
        $tag = isset($param['tag']) ? trim($param['tag']) : '';
        if (!empty($tag)) {
            $tagindexInfo = M('tagindex')->where([
                    'tag'   => $tag,
                    'lang'  => $this->home_lang,
                ])->find();
        } elseif (intval($tagid) > 0) {
            $tagindexInfo = M('tagindex')->where([
                    'id'   => $tagid,
                    'lang'  => $this->home_lang,
                ])->find();
        }

        if (!empty($tagindexInfo)) {
            $tagid = $tagindexInfo['id'];
            $tag = $tagindexInfo['tag'];
            //更新浏览量和记录数
            $map = array(
                'tid'   => array('eq', $tagid),
                'arcrank'   => array('gt', -1),
                'lang'  => $this->home_lang,
            );
            $total = M('taglist')->where($map)
                ->count('tid');
            M('tagindex')->where([
                    'id'    => $tagid,
                    'lang'  => $this->home_lang,
                ])->inc('count')
                ->inc('weekcc')
                ->inc('monthcc')
                ->update(array('total'=>$total));

            $ntime = getTime();
            $oneday = 24 * 3600;

            //周统计
            if(ceil( ($ntime - $tagindexInfo['weekup'])/$oneday ) > 7)
            {
                M('tagindex')->where([
                        'id'    => $tagid,
                        'lang'  => $this->home_lang,
                    ])->update(array('weekcc'=>0, 'weekup'=>$ntime));
            }

            //月统计
            if(ceil( ($ntime - $tagindexInfo['monthup'])/$oneday ) > 30)
            {
                M('tagindex')->where([
                        'id'    => $tagid,
                        'lang'  => $this->home_lang,
                    ])->update(array('monthcc'=>0, 'monthup'=>$ntime));
            }
        }

        $field_data = array(
            'tag'   => $tag,
            'tagid'   => $tagid,
        );
        $eyou = array(
            'field'  => $field_data,
        );
        $this->eyou = array_merge($this->eyou, $eyou);
        $this->assign('eyou', $this->eyou);

        /*模板文件*/
        $viewfile = 'lists_tags';
        /*--end*/

        /*多语言内置模板文件名*/
        if (!empty($this->home_lang)) {
            $viewfilepath = TEMPLATE_PATH.$this->theme_style.DS.$viewfile."_{$this->home_lang}.".$this->view_suffix;
            if (file_exists($viewfilepath)) {
                $viewfile .= "_{$this->home_lang}";
            }
        }
        /*--end*/

        return $this->fetch(":{$viewfile}");
    }
}