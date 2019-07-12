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
use think\template\driver\File;

class Buildhtml extends Base
{

    public function _initialize() {
        parent::_initialize();
    }

    /*
     * 清理缓存
     */
    private function clearCache(){
        cache("channel_page_total_serialize",null);
        cache("channel_info_serialize",null);
        cache("has_children_Row_serialize",null);
        cache("article_info_serialize",null);
        cache("article_page_total_serialize",null);
        cache("article_tags_serialize",null);
        cache("article_attr_info_serialize",null);
        cache("article_children_row_serialize",null);
    }
    
    /*
     * 获取全站生成时，需要生成的页面的个数
     */
    public function buildIndexAll(){
        $this->clearCache();
        $channelData = $this->getChannelData(0);
        $articleData = $this->getArticleData(0,0);
        $msg = $this->handleBuildIndex();
        $allpagetotal = 1+ $channelData['pagetotal'] + $articleData['pagetotal'];

        $this->success($msg,null,["achievepage"=>1,"channelpagetotal"=>$channelData['pagetotal']
            ,"articlepagetotal"=>$articleData['pagetotal'],"allpagetotal"=>$allpagetotal]);
    }

    /*
     * 生成首页静态页面
     */
    public function buildIndex()
    {
        $msg = $this->handleBuildIndex();
        $this->success($msg);
    }

    /*
     * 处理生成首页
     */
    private function handleBuildIndex(){
        /*获取当前页面URL*/
        $result['pageurl'] = $this->request->domain().ROOT_DIR;
        /*--end*/
        $eyou = array(
            'field' => $result,
        );
        $this->eyou = array_merge($this->eyou, $eyou);
        $this->assign('eyou', $this->eyou);
        $msg = '';
        try{
            $savepath = './index.html';
            $tpl = 'index';
            $this->request->get(['m'=>'Index']); // 首页焦点
            $this->filePutContents($savepath,$tpl,'pc');
//            $msg .= '<span>index.html生成成功</span><br>';
        }catch(\Exception $e){
            $msg .= '<span>index.html生成失败！'.$e->getMessage().'</span><br>';
        }

        return $msg;
    }
    
    /*
     * 写入静态页面
     */
    private function filePutContents($savepath,$tpl,$model='pc',$pages=0,$dir='/',$tid=0,$top=1)
    {
        ob_start();
        static $templateConfig = null;
        null === $templateConfig && $templateConfig = \think\Config::get('template');
        $templateConfig['view_path'] = "./template/pc/";
        $template = "./template/{$model}/{$tpl}.{$templateConfig['view_suffix']}";
        $content = $this->fetch($template, [], [], $templateConfig);
        
        if($pages>0){
            $page = "/<a(.*?)href(\s*)=(\s*)[\'|\"](.*?)page=([0-9]*)(.*?)data-ey_fc35fdc=[\'|\"]html[\'|\"](.*?)>/i";
            preg_match_all($page,$content,$matchpage);

            $dir = trim($dir, '.');
            foreach ( $matchpage[0] as $key1 => $value1 ) {
                if($matchpage[5][$key1] == 1){
                    if($top==1){
                        $url = $dir;
                    }elseif($top==2){
                        $url = $dir.'/lists_'.$tid.'.html';
                    }else{
                        $url = $dir.'/lists_'.$tid.'.html';
                    }
                }else{
                    $url = $dir.'/lists_'.$tid.'_'.$matchpage[5][$key1].'.html';
                }
                $url = ROOT_DIR.'/'.trim($url, '/');
                $value1_new = preg_replace('/href(\s*)=(\s*)[\'|\"]([^\'\"]*)[\'|\"]/i', '', $value1);
                $value1_new = str_replace('data-ey_fc35fdc=', " href=\"{$url}\" data-ey_fc35fdc=", $value1_new);
                $content = str_ireplace ( $value1, $value1_new, $content );
            }
        }
        $content = $this->pc_to_mobile_js($content); // 生成静态模式下，自动加上PC端跳转移动端的JS代码
        echo $content;
        $_cache=ob_get_contents();
        ob_end_clean();
        static $File = null;
        null === $File && $File = new File;
        $File->fwrite($savepath, $_cache);   
    }

    /*
     * 生成文档静态页面
     */
    public function buildArticle()
    {
        function_exists('set_time_limit') && set_time_limit(0);

        $typeid =  input("param.id/d",0); // 栏目ID
        $fid =  input("param.fid/d",0);
        $achievepage = input("param.achieve/d",0); // 已完成文档数
        $this->clearCache();
        $data = $this->handelBuildArticle($typeid,0,$fid,$achievepage);

        $this->success($data[0],null,$data[1]);
    }

    /**
     * 获取详情页数据
     * $typeid      栏目id
     * $aid     文章id
     * $type    类型，0：aid指定的内容，1：上一篇，2：下一篇
     */
    private function getArticleData($typeid,$aid,$type = 0){
        $info_serialize = cache("article_info_serialize","");
        if (empty($info_serialize)){
            if ($type == 0){
                $data = getAllArchives($this->home_lang,$typeid,$aid);
            }else if ($type == 1){
                $data = getPreviousArchives($this->home_lang,$typeid,$aid);
            }else if ($type == 2){
                $data = getNextArchives($this->home_lang,$typeid,$aid);
            }
            $info = $data['info'];
            $pagetotal = count($info);
            $aid_arr = get_arr_column($info,'aid');
            $allTags = getAllTags($aid_arr);
            $allAttrInfo = getAllAttrInfo($aid_arr);
            /*获取所有栏目是否有子栏目的数组*/
            $has_children_Row = model('Arctype')->hasChildren(get_arr_column($info, 'typeid'));
            cache("article_info_serialize",serialize($data));
            cache("article_page_total_serialize",$pagetotal);
            cache("article_tags_serialize",serialize($allTags));
            cache("article_attr_info_serialize",serialize($allAttrInfo));
            cache("article_children_row_serialize",serialize($has_children_Row));
        }else{
            $data = unserialize($info_serialize);
            $pagetotal = cache("article_page_total_serialize","");
            $allTags = unserialize(cache("article_tags_serialize",""));
            $allAttrInfo = unserialize(cache("article_attr_info_serialize",""));
            $has_children_Row = unserialize(cache("article_children_row_serialize",""));
        }

        return ['data'=>$data,'pagetotal'=>$pagetotal,'allTags'=>$allTags,'allAttrInfo'=>$allAttrInfo,'has_children_Row'=>$has_children_Row];
    }

    /**
     * 处理生成内容页
     * $typeid      栏目id
     * $aid         内容页id
     * $fid         下一次执行栏目id
     * $achievepage 已完成文档数
     * $batch       是否分批次执行，true：分批，false：不分批
     * limit        每次执行多少条数据
     * type         执行类型，0：aid指定的文档页，1：上一篇，2：下一篇
     *
     */
    private function handelBuildArticle($typeid,$aid = 0,$nextid = 0,$achievepage = 0,$batch = true,$limit = 20,$type = 0){
        $msg = "";
        $globalConfig = $this->eyou['global'];
        $result = $this->getArticleData($typeid,$aid,$type);
        $info = $result['data']['info'];
        $arctypeRow = $result['data']['arctypeRow'];
        $allTags = $result['allTags'];
        $has_children_Row = $result['has_children_Row'];
        $allAttrInfo = $result['allAttrInfo'];
        $data['allpagetotal'] = $pagetotal = $result['pagetotal'];
        $data['achievepage'] = $achievepage;
        $data['pagetotal'] = 0;

        if ($batch && $pagetotal > $achievepage){
            while ($limit && isset($info[$nextid])){
                $row = $info[$nextid];
                $msg .= $msg_temp = $this->createArticle($row,$globalConfig,$arctypeRow,$allTags,$has_children_Row,$allAttrInfo);
                $data['achievepage'] +=  1;
                $limit--;
                $nextid++;
            }
            $data['fid'] = $nextid;
        }else if (!$batch){
            foreach ($info as $key=>$row){
                $msg .= $msg_temp = $this->createArticle($row,$globalConfig,$arctypeRow,$allTags,$has_children_Row,$allAttrInfo);
                $data['achievepage'] += 1;
                $data['fid'] = $key;
            }
        }
        if ($data['allpagetotal'] == $data['achievepage']){  //生成完全部页面，删除缓存
            cache("article_info_serialize",null);
            cache("article_page_total_serialize",null);
            cache("article_tags_serialize",null);
            cache("article_attr_info_serialize",null);
            cache("article_children_row_serialize",null);
        }

        return [$msg,$data];
    }

    /*
     * 生成详情页静态页面
     */
    private function createArticle($result,$globalConfig,$arctypeRow,$allTags,$has_children_Row,$allAttrInfo){
        $msg = "";
        $aid =  $result['aid'];
        static $arc_seo_description_length = null;
        null === $arc_seo_description_length && $arc_seo_description_length = config('global.arc_seo_description_length');
        $this->request->post(['aid'=>$aid]);
        $this->request->post(['tid'=>$result['typeid']]);

        $arctypeInfo = $arctypeRow[$result['typeid']];
        $arctypeInfo = model('Arctype')->parentAndTopInfo($result['typeid'], $arctypeInfo);
        /*自定义字段的数据格式处理*/
        $arctypeInfo = $this->fieldLogic->getTableFieldList($arctypeInfo, config('global.arctype_channel_id'));
        /*是否有子栏目，用于标记【全部】选中状态*/
        $arctypeInfo['has_children'] = !empty($has_children_Row[$result['typeid']]) ? 1 : 0;
        /*--end*/
        // 文档模板文件，不指定文档模板，默认以栏目设置的为主
        empty($result['tempview']) && $result['tempview'] = $arctypeInfo['tempview'];

        /*给没有type前缀的字段新增一个带前缀的字段，并赋予相同的值*/
        foreach ($arctypeInfo as $key => $val) {
            if (!preg_match('/^type/i',$key)) {
                $key_new = 'type'.$key;
                !array_key_exists($key_new, $arctypeInfo) && $arctypeInfo[$key_new] = $val;
            }
        }
        /*--end*/

        $result = array_merge($arctypeInfo, $result);

        // 获取当前页面URL
        $result['arcurl'] = $result['pageurl'] = '';
        if ($result['is_jump'] != 1) {
            $result['arcurl'] = $result['pageurl'] = arcurl('home/View/index', $result, true, true);
        }
        /*--end*/

        // seo
        $result['seo_title'] = set_arcseotitle($result['title'], $result['seo_title'], $result['typename']);
        $result['seo_description'] = @msubstr(checkStrHtml($result['seo_description']), 0, $arc_seo_description_length, false);

        /*支持子目录*/
        $result['litpic'] = handle_subdir_pic($result['litpic']);
        /*--end*/

        $result = view_logic($aid, $result['channel'], $result, $allAttrInfo); // 模型对应逻辑

        /*自定义字段的数据格式处理*/
        $result = $this->fieldLogic->getChannelFieldList($result, $result['channel']);
        /*--end*/

        $eyou = array(
            'type'  => $arctypeInfo,
            'field' => $result,
        );
        $this->eyou = array_merge($this->eyou, $eyou);
        $this->assign('eyou', $this->eyou);

        /*模板文件*/
        $tpl = !empty($result['tempview'])
        ? str_replace('.'.$this->view_suffix, '',$result['tempview'])
        : 'view_'.$result['nid'];
        /*--end*/

        $dir = $this->getArticleDir($result['dirpath']);
        $savepath = $dir.'/'.$aid.'.html';

        try{
            $this->filePutContents('./'.$savepath,$tpl,'pc');
        }catch(\Exception $e){
             $msg .= '<span>'.$savepath.'生成失败！'.$e->getMessage().'</span><br>';
        }

        return $msg;
    }

    private function getArticleDir($dirpath){
        $dir = "";
        $seo_html_pagename = $this->eyou['global']['seo_html_pagename'];
        $seo_html_arcdir = $this->eyou['global']['seo_html_arcdir'];
        if($seo_html_pagename == 1){//存放顶级目录
            $dirpath_arr = explode('/',$dirpath);
            if(count($dirpath_arr) > 2){
                $dir = '.'.$seo_html_arcdir.'/'.$dirpath_arr[1];
            }else{
                $dir = '.'.$seo_html_arcdir.$dirpath;
            }
        }else{ //存放父级目录
            $dir = '.'.$seo_html_arcdir.$dirpath;
        }

        return $dir;
    }

    /*
     * 生成栏目静态页面
     * $id  tpyeid  栏目id
     * $fid         下一次执行栏目id
     * $achievepage 已完成页数
     *$batch        是否分批次执行，true：分批，false：不分批
     *
     */
    public function buildChannel()
    {
        function_exists('set_time_limit') && set_time_limit(0);
        $id =  input("param.id/d",0); // 栏目ID
        $fid =  input("param.fid/d",0);
        $achievepage = input("param.achieve/d",0);
        $this->clearCache();
        $data = $this->handleBuildChannel($id,$fid,$achievepage);

        $this->success($data[0],null,$data[1]);
    }

    /*
     * 获取栏目数据
     * $id      栏目id
     * $parent        是否获取下级栏目    true：获取，false：不获取
     */
    private function getChannelData($id,$parent = true,$aid = 0){
        $info_serialize = cache("channel_info_serialize","");
        if (empty($info_serialize)){
            $result = getAllArctype($this->home_lang,$id,$this->view_suffix,$parent,$aid);
            $info = $result["info"];
            $pagetotal = $result["pagetotal"];
            $has_children_Row = model('Arctype')->hasChildren(get_arr_column($info, 'typeid'));

            cache("channel_page_total_serialize",$pagetotal);
            cache("channel_info_serialize",serialize($info));
            cache("has_children_Row_serialize",serialize($has_children_Row));
        }else{
            $info = unserialize($info_serialize);
            $pagetotal = cache("channel_page_total_serialize","");
            $has_children_Row = unserialize(cache("has_children_Row_serialize",""));
        }

        return ['info'=>$info,'pagetotal'=>$pagetotal,'has_children_Row'=>$has_children_Row];
    }

    /*
     * 处理生成栏目页
     * $id           typeid
     * $fid         下一次执行栏目id
     * $achievepage 已完成页数
     * $batch        是否分批次执行，true：分批，false：不分批
     * $parent        是否获取下级栏目    true：获取，false：不获取
     * $aid           文章页id，不等于0时，表示只获取文章页所在的列表页重新生成静态(在添加或者编辑文档内容时使用)
     */
    private function handleBuildChannel($id,$fid = 0,$achievepage = 0,$batch = true,$parent = true,$aid = 0){
        $msg = '';
        $globalConfig = $this->eyou['global'];
        $result = $this->getChannelData($id,$parent,$aid);
        $info = $result['info'];
        $has_children_Row = $result['has_children_Row'];
        $data['allpagetotal'] = $pagetotal = $result['pagetotal'];
        $data['achievepage'] = $achievepage;
        if ($batch && $data['allpagetotal'] > $data['achievepage']  && isset($info[$fid])){
            $row = $info[$fid];
            $msg .= $msg_temp = $this->createChannel($row,$globalConfig,$has_children_Row);
            $data['pagetotal'] = $row['pagetotal'];
            $data['achievepage'] += $row['pagetotal'];
            $data['fid'] = $fid+1;
            $data['typeid'] = $row['typeid'];
        }else if (!$batch){
            foreach ($info as $key=>$row){
                $msg .= $msg_temp = $this->createChannel($row,$globalConfig,$has_children_Row,$aid);
                $data['pagetotal'] = $row['pagetotal'];
                $data['achievepage'] += $row['pagetotal'];
                $data['fid'] = $key;
                $data['typeid'] = $row['typeid'];
            }
        }
        if ($data['allpagetotal'] == $data['achievepage']){  //生成完全部页面，删除缓存
            cache("channel_page_total_serialize",null);
            cache("channel_info_serialize",null);
            cache("has_children_Row_serialize",null);
        }

        return [$msg,$data];
    }

    /*
     * 生成栏目页面
     */
    private function createChannel($row,$globalConfig,$has_children_Row,$aid = 0){
        $msg = "";
        $seo_html_listname = $this->eyou['global']['seo_html_listname'];
        $seo_html_arcdir = $this->eyou['global']['seo_html_arcdir'];
        $tid = $row['typeid'];
        $this->request->post(['tid'=>$tid]);

        $row = $this->lists_logic($row, $has_children_Row);  // 模型对应逻辑
        $eyou = array(
            'field' => $row,
        );
        $this->eyou = array_merge($this->eyou, $eyou);
        $this->assign('eyou', $this->eyou);

        $tpl = !empty($row['templist']) ? str_replace('.'.$this->view_suffix, '',$row['templist']) : 'lists_'. $row['nid'];

        if(in_array($row['current_channel'], [6,8])){   //留言模型或单页模型，不存在多页
            $this->request->get(['page'=>'']);
            $dirpath = explode('/',$eyou['field']['dirpath']);
            if($seo_html_listname == 1){  //存放顶级目录
                $savepath  = '.'.$seo_html_arcdir.'/'.$dirpath[1]."/lists_".$eyou['field']['typeid'].".html";
            }else{
                $savepath  = '.'.$seo_html_arcdir.$eyou['field']['dirpath'].'/'.'lists_'.$eyou['field']['typeid'].".html";
            }
            try{
                $this->filePutContents($savepath,$tpl,'pc');
                if ($seo_html_listname != 1 || count($dirpath) < 3){
                    copy($savepath,'.'.$seo_html_arcdir.$eyou['field']['dirpath'].'/index.html');
                }
            }catch(\Exception $e){
                $msg .= '<span>'.$savepath.'生成失败！'.$e->getMessage().'</span><br>';
            }
        }else if(!empty($aid)){     //只更新aid所在的栏目页码
            $orderby = getOrderBy($row['orderby'],$row['orderway']);
            $limit = getLocationPages($tid,$aid,$orderby);
            $i = !empty($limit) ? ceil($limit/$row['pagesize']):1;
            $msg .= $this->createMultipageChannel($i,$tid,$row,$has_children_Row,$seo_html_listname,$seo_html_arcdir,$tpl);

        }else{    //多条信息的栏目
            $totalpage = $row['pagetotal'];
            for ($i=1; $i <= $totalpage; $i++){
                $msg .= $this->createMultipageChannel($i,$tid,$row,$has_children_Row,$seo_html_listname,$seo_html_arcdir,$tpl);
            }
        }

        return $msg;
    }
    /*
     * 创建有文档列表模型的静态栏目页面
     */
    private function createMultipageChannel($i,$tid,$row,$has_children_Row,$seo_html_listname,$seo_html_arcdir,$tpl){
        $msg = "";
        $this->request->get(['page'=>$i]);
        $row = $this->lists_logic($row, $has_children_Row);  // 模型对应逻辑
        $eyou = array(
            'field' => $row,
        );
        $this->eyou = array_merge($this->eyou, $eyou);
        $this->assign('eyou', $this->eyou);
        $dirpath = explode('/',$eyou['field']['dirpath']);
        if($seo_html_listname == 1){  //存放顶级目录
            $dir = '.'.$seo_html_arcdir.'/'.$dirpath[1];
            $savepath  = '.'.$seo_html_arcdir.'/'.$dirpath[1]."/lists_".$eyou['field']['typeid'];
        }else{
            $dir = '.'.$seo_html_arcdir.$eyou['field']['dirpath'];
            $savepath  = '.'.$seo_html_arcdir.$eyou['field']['dirpath'].'/'.'lists_'.$eyou['field']['typeid'];
        }
        if ($i > 1){
            $savepath .= '_'.$i.'.html';;
        }else{
            $savepath .= '.html';
        }
        $top = $i > 1 && $seo_html_listname == 1 && count($dirpath) >2 ? 2 :1;
        try{
            $this->filePutContents($savepath,$tpl,'pc',$i,$dir,$tid,$top);
            if ($i==1 && ($seo_html_listname != 1 || count($dirpath) < 3)){
                copy($savepath,'.'.$seo_html_arcdir.$eyou['field']['dirpath'].'/index.html');
            }
        }catch(\Exception $e){
            $msg .= '<span>'.$savepath.'生成失败！'.$e->getMessage().'</span><br>';
        }

        return $msg;
    }
    
    /**
     * 更新静态生成页
     * @param int $aid 文章id 
     * @param int $typeid 栏目id 
     * @return boolean
     * $del_ids       删除的文章数组
     */
    public function upHtml(){
        $aid =  input("param.aid/d");
        $typeid =  input("param.typeid/d");
        $del_ids = input('param.del_ids/a');
        $type = input('param.type/s');
        $lang =  input("param.lang/s", 'cn');

        /*由于全站共用删除JS代码，这里排除不能发布文档的模型的控制器*/
        $ctl_name =  input("param.ctl_name/s");
        $channeltypeRow = Db::name('channeltype')
            ->where('nid','NOT IN', ['guestbook','single'])
            ->column('ctl_name');
        array_push($channeltypeRow, 'Archives', 'Arctype');
        if (!in_array($ctl_name, $channeltypeRow)) {
            $this->error("排除非发布文档的模型");
        }
        /*end*/

        $seo_pseudo = $this->eyou['global']['seo_pseudo'];
        $this->clearCache();
        if ($seo_pseudo != 2){
            $this->error("当前非静态模式，不做静态处理");
        }
        if(!empty($del_ids)){    //删除文章页面
            $info = db('archives')->field('a.*,b.dirpath')
                ->alias('a')
                ->join('__ARCTYPE__ b', 'a.typeid = b.id', 'LEFT')
                ->where([
                        'a.aid'     => ['in',$del_ids],
                        'a.lang'    => $lang,
                    ])
                ->select();
            foreach($info as $key=>$row){
                $dir = $this->getArticleDir($row['dirpath']);
                $filename = $row['aid'];
                $path = $dir."/".$filename.".html";
                if(file_exists($path)){
                    @unlink($path);
                }
            }
        }else if (!empty($aid) && !empty($typeid)){   //变更文档信息，更新文档页及相关的栏目页
            if ('view' == $type) {
                $this->handelBuildArticle($typeid,$aid,0,0,false,1,0);
                $this->handelBuildArticle($typeid,$aid,0,0,false,1,1); // 更新上篇
                $this->handelBuildArticle($typeid,$aid,0,0,false,1,2); // 更新下篇
            } else if ('lists' == $type) {
                $data = $this->handleBuildChannel($typeid,0,0,false,false,$aid);
                // $this->handelBuildArticle($typeid,$aid,0,0,false,1,1); // 更新上篇
                // $this->handelBuildArticle($typeid,$aid,0,0,false,1,2); // 更新下篇
            } else {
                $this->handleBuildChannel($typeid,0,0,false,false,$aid);
                $this->handelBuildArticle($typeid,$aid,0,0,false);
            }
        }else if (!empty($typeid)){     //变更栏目信息，更新栏目页
            $this->handleBuildIndex();
            $data = $this->handleBuildChannel($typeid,0,0,false,false);
        }

        $this->success("静态页面生成完成");
    }

    /**
     * 读取指定栏目ID下有内容的栏目信息，只读取每一级的第一个栏目
     * @param intval $typeid 栏目ID
     * @return array
     */
    private function readContentFirst($typeid)
    {
        $result = false;
        while (true)
        {
            $result = model('Single')->getInfoByTypeid($typeid);
            if (empty($result['content']) && 'lists_single.htm' == strtolower($result['templist'])) {
                $map = array(
                    'parent_id' => $result['typeid'],
                    'current_channel' => 6,
                    'is_hidden' => 0,
                    'status'    => 1,
                );
                $row = M('arctype')->where($map)->field('*')->order('sort_order asc')->find(); // 查找下一级的单页模型栏目
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

    /*
     * 拓展页面相关信息
     */
    private function lists_logic($result = [], $has_children_Row = [])
    {
        if (empty($result)) {
            return [];
        }

        $tid = $result['typeid'];

        switch ($result['current_channel']) {
            case '6': // 单页模型
            {
                $arctype_info = model('Arctype')->parentAndTopInfo($tid, $result);
                if ($arctype_info) {
                    // 读取当前栏目的内容，否则读取每一级第一个子栏目的内容，直到有内容或者最后一级栏目为止。
                    $result_new = $this->readContentFirst($tid);
                    // 阅读权限 或 外部链接跳转
                    if ($result_new['arcrank'] == -1 || $result_new['is_part'] == 1) {
                        return fasle;
                    }
                    /*自定义字段的数据格式处理*/
                    $result_new = $this->fieldLogic->getChannelFieldList($result_new, $result_new['current_channel']);
                    /*--end*/

                    $result = array_merge($arctype_info, $result_new);

                    $result['templist'] = !empty($arctype_info['templist']) ? $arctype_info['templist'] : 'lists_'. $arctype_info['nid'];
                    $result['dirpath'] = $arctype_info['dirpath'];
                    $result['typeid'] = $arctype_info['typeid'];
                }
                break;
            }

            default:
            {
                $result = model('Arctype')->parentAndTopInfo($tid, $result);
                break;
            }
        }

        if (!empty($result)) {
            /*自定义字段的数据格式处理*/
            $result = $this->fieldLogic->getTableFieldList($result, config('global.arctype_channel_id'));
            /*--end*/
        }

        /*是否有子栏目，用于标记【全部】选中状态*/
        $result['has_children'] = !empty($has_children_Row[$tid]) ? 1 : 0;
        /*--end*/

        // seo
        $result['seo_title'] = set_typeseotitle($result['typename'], $result['seo_title']);

        /*获取当前页面URL*/
        $result['pageurl'] = $result['typeurl'];
        /*--end*/

        /*给没有type前缀的字段新增一个带前缀的字段，并赋予相同的值*/
        foreach ($result as $key => $val) {
            if (!preg_match('/^type/i',$key)) {
                $key_new = 'type'.$key;
                !array_key_exists($key_new, $result) && $result[$key_new] = $val;
            }
        }
        /*--end*/

        return $result;
    }

    /**
     * 生成静态模式下且PC和移动端模板分离，就自动给PC端加上跳转移动端的JS代码
     * @access public
     */
    private function pc_to_mobile_js($html = '')
    {
        if (file_exists('./template/mobile')) {
            $url = $this->request->domain().ROOT_DIR.'/index.php';
            $jsStr = <<<EOF
    <meta http-equiv="mobile-agent" content="format=xhtml;url={$url}">
    <script type="text/javascript">if(window.location.toString().indexOf('pref=padindex') != -1){}else{if(/applewebkit.*mobile/i.test(navigator.userAgent.toLowerCase()) || (/midp|symbianos|nokia|samsung|lg|nec|tcl|alcatel|bird|dbtel|dopod|philips|haier|lenovo|mot-|nokia|sonyericsson|sie-|amoi|zte/.test(navigator.userAgent.toLowerCase()))){try{if(/android|windows phone|webos|iphone|ipod|blackberry/i.test(navigator.userAgent.toLowerCase())){window.location.href="{$url}";}else if(/ipad/i.test(navigator.userAgent.toLowerCase())){}else{}}catch(e){}}}</script>
EOF;
            $html = str_ireplace('</head>', $jsStr."\n</head>", $html);
        }

        return $html;
    }
}