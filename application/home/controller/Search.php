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

use think\Db;

class Search extends Base
{
    private $searchword_db;

    public function _initialize() {
        parent::_initialize();
        $this->searchword_db = Db::name('search_word');
    }

    /**
     * 搜索主页
     */
    public function index()
    {
        return $this->lists();
    }

    /**
     * 搜索列表
     */
    public function lists()
    {
        $param = input('param.');

        /*记录搜索词*/
        $word = $this->request->param('keywords');
        $page = $this->request->param('page');
        if(!empty($word) && 2 > $page)
        {
            $nowTime = getTime();
            $row = $this->searchword_db->field('id')->where(['word'=>$word, 'lang'=>$this->home_lang])->find();
            if(empty($row))
            {
                $this->searchword_db->insert([
                    'word'      => $word,
                    'sort_order'    => 100,
                    'lang'      => $this->home_lang,
                    'add_time'  => $nowTime,
                    'update_time'  => $nowTime,
                ]);
            }else{
                $this->searchword_db->where(['id'=>$row['id']])->update([
                    'searchNum'         =>  Db::raw('searchNum+1'),
                    'update_time'       => $nowTime,
                ]);
            }
        }
        /*--end*/

        $result = $param;
        $eyou = array(
            'field' => $result,
        );
        $this->eyou = array_merge($this->eyou, $eyou);
        $this->assign('eyou', $this->eyou);

        /*模板文件*/
        $viewfile = 'lists_search';
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