<?php
namespace Think;

class Page{
    public $firstRow; // 起始行数
    public $listRows; // 列表每页显示行数
    public $parameter; // 分页跳转时要带的参数
    public $totalRows; // 总行数
    public $totalPages; // 分页总页面数
    public $rollPage   = 5;// 分页栏每页显示的页数
    public $lastSuffix = true; // 最后一页是否显示总页数

    public $p       = 'p'; //分页参数名
    public $url     = ''; //当前链接URL
    public $nowPage = 1;

    // 分页显示定制
    private $config  = array();

    /**
     * 架构函数
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     */
    public function __construct($totalRows, $listRows=20, $parameter = array()) {
       // config('VAR_PAGE') && $this->p = config('VAR_PAGE'); //设置分页参数名称
        /* 基础设置 */
        $p = input('param.p/d');
        $pagesize = input('param.pagesize/d');
        $this->totalRows  = $totalRows; //设置总记录数
        $this->listRows   = empty($pagesize) ? $listRows : $pagesize;  //设置每页显示行数
        $this->parameter  = empty($parameter) ? input() : $parameter;
        $this->nowPage    = empty($p) ? 1 : $p;
        // $this->parameter  = empty($parameter) ? $_REQUEST : $parameter;
        // $this->nowPage    = empty($_REQUEST[$this->p]) ? 1 : intval($_REQUEST[$this->p]);        
        $this->nowPage    = $this->nowPage>0 ? $this->nowPage : 1;
        $this->firstRow   = $this->listRows * ($this->nowPage - 1);
        /* 计算分页信息 */
        $this->totalPages = ceil($this->totalRows / $this->listRows)>0 ? ceil($this->totalRows / $this->listRows) : 1; //总页数
        
        if (isMobile()) {
            $this->config = [
                'header' => '<span class="rows">共 %TOTAL_ROW% 条记录</span>',
                'prev'   => lang('sys2'),
                'next'   => lang('sys3'),
                'first'  => lang('sys1'),
                'last'   => lang('sys4'),
                'theme'  => '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
            ];
        } else {
            $this->config  = array(
                'header' => '<span class="rows">共 %TOTAL_ROW% 条记录</span>',
                /*
                'prev'   => '<<',
                'next'   => '>>',
                'first'  => '1...',
                'last'   => '...%TOTAL_PAGE%',
                */
                'prev'   => '<',
                'next'   => '>',
                'first'  => lang('sys1'),
                'last'   => lang('sys4'),
                'theme'  => '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%',
            );
        }
    }

    /**
     * 定制分页链接设置
     * @param string $name  设置名称
     * @param string $value 设置值
     */
    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }

    /**
     * 生成链接URL
     * @param  integer $page 页码
     * @return string
     */
    public function url($page){
        if (1 >= $page) {
            $this->url = str_replace('_p'.urlencode('[PAGE]'), '', $this->url);
        }
        static $seo_pseudo = null;
        null === $seo_pseudo && $seo_pseudo = config('ey_config.seo_pseudo');
        if ($seo_pseudo == 3 || $seo_pseudo == 2) {
            if (!strstr($this->url, '.htm') && !preg_match('/m=([^&]+)&c=([^&]+)&a=([^&]+)/i', $this->url)){
                $this->url = rtrim($this->url, '/').'/';
            }
        }
        return str_replace(urlencode('[PAGE]'), $page, $this->url);
    }

    /**
     * 组装分页链接
     * @return string
     */
    public function show() {
        if(0 == $this->totalRows) return '';

        /* 生成URL */
        $this->parameter[$this->p] = '[PAGE]';
        $mca = MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME;
        $this->url = U($mca, $this->parameter);

        if (!empty($this->totalPages) && $this->nowPage > $this->totalPages) {
            $this->nowPage = $this->totalPages;
        }

        /* 计算分页临时变量 */
        $now_cool_page      = $this->rollPage/2;
        $now_cool_page_ceil = ceil($now_cool_page);
        $this->lastSuffix && $this->config['last'] = $this->totalPages;
        $this->config['last'] = lang('sys4');
        //上一页
        $up_row  = $this->nowPage - 1;
        $up_page = $up_row > 0 ? '<li id="example1_previous" class="paginate_button previous"><a class="prev" href="' . $this->url($up_row) . '">' . $this->config['prev'] . '</a></li>' : '';
        if ($this->totalPages > 1 && $up_row <= 0) {
            $up_page = '<li id="example1_previous" class="paginate_button previous disabled"><a class="prev" href="javascript:void(0);">' . $this->config['prev'] . '</a></li>'.$up_page;
        }

        //下一页
        $down_row  = $this->nowPage + 1;
        $down_page = ($down_row <= $this->totalPages) ? '<li id="example1_next" class="paginate_button next"><a class="next" href="' . $this->url($down_row) . '">' . $this->config['next'] . '</a></li>' : '';
        if ($this->totalPages > 1 && $down_row > $this->totalPages) {
            $down_page = $down_page.'<li id="example1_next" class="paginate_button next disabled"><a class="next" href="javascript:void(0);">' . $this->config['next'] . '</a></li>';
        }

        //第一页
        $the_first = '';
        if($this->nowPage > 1 || ($this->totalPages > $this->rollPage && ($this->nowPage - $now_cool_page) >= 1)){
            $the_first = '<li id="example1_previous" class="paginate_button previous"><a class="first" href="' . $this->url(1) . '">' . $this->config['first'] . '</a></li>';
        } else if ($this->totalPages > 1) {
            $the_first = '<li id="example1_previous" class="paginate_button previous disabled"><a class="first" href="javascript:void(0);">' . $this->config['first'] . '</a></li>';
        }

        //最后一页
        $the_end = '';
        if($this->nowPage < $this->totalPages || ($this->totalPages > $this->rollPage && ($this->nowPage + $now_cool_page) < $this->totalPages)){
            $the_end = '<li id="example1_previous" class="paginate_button previous"><a class="end" href="' . $this->url($this->totalPages) . '">' . $this->config['last'] . '</a></li>';
        } else if ($this->totalPages > 1) {
            $the_end = '<li id="example1_previous" class="paginate_button previous disabled"><a class="end" href="javascript:void(0);">' . $this->config['last'] . '</a></li>';
        }

        //数字连接
        $link_page = "";
        if (!isMobile()) {
            for ($i = 1; $i <= $this->rollPage; $i++) {
                if(($this->nowPage - $now_cool_page) <= 0 ){
                    $page = $i;
                }elseif(($this->nowPage + $now_cool_page - 1) >= $this->totalPages){
                    $page = $this->totalPages - $this->rollPage + $i;
                }else{
                    $page = $this->nowPage - $now_cool_page_ceil + $i;
                }
                if($page > 0 && $page != $this->nowPage){

                    if($page <= $this->totalPages){
                        $link_page .= '<li class="paginate_button"><a class="num" href="' . $this->url($page) . '">' . $page . '</a></li>';
                    }else{
                        break;
                    }
                }else{
                    if($page > 0 && $this->totalPages != 1){
                        // $link_page .= '<span class="current">' . $page . '</span>';
                        $link_page .= '<li class="paginate_button active"><a tabindex="0" data-dt-idx="1" aria-controls="example1" href="javascript:void(0);">' . $page . '</a></li>';

                    }
                }
            }
        }

        //替换分页内容
        $page_str = str_replace(
            array('%HEADER%', '%NOW_PAGE%', '%UP_PAGE%', '%DOWN_PAGE%', '%FIRST%', '%LINK_PAGE%', '%END%', '%TOTAL_ROW%', '%TOTAL_PAGE%'),
            array($this->config['header'], $this->nowPage, $up_page, $down_page, $the_first, $link_page, $the_end, $this->totalRows, $this->totalPages),
            $this->config['theme']);
        return "<div class='dataTables_paginate paging_simple_numbers'><ul class='pagination'>{$page_str}</ul></div>";
    }
}
