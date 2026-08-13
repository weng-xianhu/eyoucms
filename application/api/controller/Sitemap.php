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

namespace app\api\controller;

use think\Db;
use think\template\driver\File;

class Sitemap extends Base
{
    public function _initialize() {
        parent::_initialize();
    }

    /**
     * 生成sitemap
     * @return [type] [description]
     */
    public function ajax_update_sitemap()
    {
        sitemap_all();
        if (IS_POST) {
            $this->success('更新成功');
        }
        exit('success');
    }

    /**
     * 生成sitemap.xml
     * @return [type] [description]
     */
    public function ajax_update_sitemap_xml()
    {
        if (IS_POST) {
            $langs = input('post.langs/s');
            $langs = !empty($langs) ? explode(',', $langs) : [];
            sitemap_all('xml',$langs);
            $this->success('更新成功');
        }
        exit('success');
    }

    /**
     * 生成sitemap.txt
     * @return [type] [description]
     */
    public function ajax_update_sitemap_txt()
    {
        if (IS_POST) {
            $langs = input('post.langs/s');
            $langs = !empty($langs) ? explode(',', $langs) : [];
            sitemap_all('txt',$langs);
            $this->success('更新成功');
        }
        exit('success');
    }

    /**
     * 生成sitemap.html
     * @return [type] [description]
     */
    public function ajax_update_sitemap_html(){
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        $msg = $this->handleBuildSitemap();
        if (IS_AJAX) {
            if (empty($msg)) {
                $this->success('更新成功');
            } else {
                $this->error('更新失败', null, ['msg'=>$msg]);
            }
        }
        if (empty($msg)) {
            exit('success');
        } else {
            exit($msg);
        }
    }
    
    /*
     * 处理生成html
     */
    private function handleBuildSitemap()
    {
        $is_auto = input('param.is_auto/s', 'on'); // 是否自动生成，还是手工生成
        $sitemapid = tpSetting('system.system_sitemapid1647228884');  //四个模块最后一条id，type_arc_tag_ask
        $last_type = $last_arc = $last_tag = $last_ask = 0;
        $is_create = false;
        if (!is_file('./sitemap.html') || 'off' == $is_auto) {
            $is_create = true;
        }
        if (!empty($sitemapid)){
            $sitemapid_arr = explode("_",$sitemapid);
            $last_type = !empty($sitemapid_arr[0]) ? $sitemapid_arr[0] : 0;
            $last_arc = !empty($sitemapid_arr[1]) ? $sitemapid_arr[1] : 0;
            $last_tag = !empty($sitemapid_arr[2]) ? $sitemapid_arr[2] : 0;
            $last_ask = !empty($sitemapid_arr[3]) ? $sitemapid_arr[3] : 0;
        }

        $globalConfig = tpCache('global');
        $web_name = empty($globalConfig['web_name']) ? $globalConfig['web_title'] : $globalConfig['web_name'];
        $lang = get_current_lang();
        //栏目信息
        $type_map = array(
            'channeltype'   => ['IN', config('global.channeltype_list')],
            'current_channel'   => ['IN', config('global.channeltype_list')],
            'status'    => 1,
            'is_del'    => 0,
            'lang'      => $lang,
        );
        if (is_array($globalConfig)) {
            // 过滤隐藏栏目
            if (isset($globalConfig['sitemap_not1']) && $globalConfig['sitemap_not1'] > 0) {
                $type_map['is_hidden'] = 0;
            }
            // 过滤外部模块
            if (isset($globalConfig['sitemap_not2']) && $globalConfig['sitemap_not2'] > 0) {
                $type_map['is_part'] = 0;
            }
        }
        $result_arctype = Db::name('arctype')->field("*")
            ->where($type_map)
            ->order('id asc')
            ->getAllWithIndex('id');
        $last_type_new = reset($result_arctype);
        if ($is_create == false && !empty($last_type_new['id']) && $last_type_new['id'] > $last_type){
            $is_create = true;
            $last_type = $last_type_new['id'];
        }
        $type_list = [];
        foreach ($result_arctype as $sub){
            if ($sub['is_part'] == 1 && !empty($sub['typelink'])) {
                $url = $sub['typelink'];
            } else {
                $url = get_typeurl($sub, false);
            }
            $type_list[] = [
                'url' => $url,
                'title' => $sub['typename']
            ];
        }
        //文档信息
        $arc_map = array(
            'channel'   => ['IN', config('global.allow_release_channel')],
            'arcrank'   => array('gt', -1),
            'status'    => 1,
            'is_del'    => 0,
            'lang'      => $lang,
        );
        if (is_array($globalConfig)) {
            // 过滤外部模块
            if (isset($globalConfig['sitemap_not2']) && $globalConfig['sitemap_not2'] > 0) {
                $arc_map['is_jump'] = 0;
            }
        }
        /*定时文档显示插件*/
        if (is_dir('./weapp/TimingTask/')) {
            $weappModel = new \app\admin\model\Weapp;
            $TimingTaskRow = $weappModel->getWeappList('TimingTask');
            if (!empty($TimingTaskRow['status']) && 1 == $TimingTaskRow['status']) {
                $arc_map['add_time'] = ['elt', getTime()]; // 只显当天或之前的文档
            }
        }
        /*end*/
        if (!isset($globalConfig['sitemap_archives_num']) || $globalConfig['sitemap_archives_num'] === '') {
            $sitemap_archives_num = 1000;
        } else {
            $sitemap_archives_num = intval($globalConfig['sitemap_archives_num']);
        }
        $field = "aid, channel, is_jump, jumplinks, htmlfilename, add_time, update_time, typeid,title,province_id,city_id,area_id";
        $result_archives = Db::name('archives')->field($field)
            ->where($arc_map)
            ->order('aid desc')
            ->limit($sitemap_archives_num)
            ->select();
        $arc_list = [];
        if ($is_create == false && !empty($result_archives[0]['aid']) && $result_archives[0]['aid'] > $last_arc){
            $is_create = true;
            $last_arc = $result_archives[0]['aid'];
        }
        foreach ($result_archives as $val){
            if (empty($result_arctype[$val['typeid']])){
                continue;
            }
            $val = array_merge($result_arctype[$val['typeid']], $val);
            if ($val['is_jump'] == 1) {
                $url = $val['jumplinks'];
            } else {
                $url = get_arcurl($val, false);
            }
            $arc_list[] = [
                'url' => $url,
                'title' => $val['title']
            ];
        }
        //tags页面
        if (!isset($globalConfig['sitemap_tags_num']) || $globalConfig['sitemap_tags_num'] === '') {
            $sitemap_tags_num = 1000;
        } else {
            $sitemap_tags_num = intval($globalConfig['sitemap_tags_num']);
        }
        $tags_map = array(
            'lang'      => $lang,
        );
        $field = "id, add_time, tag";
        $result_tags = Db::name('tagindex')->field($field)
            ->where($tags_map)
            ->order('id desc')
            ->limit($sitemap_tags_num)
            ->select();
        if ($is_create == false && !empty($result_tags[0]['id']) && $result_tags[0]['id'] > $last_tag){
            $is_create = true;
            $last_tag = $result_tags[0]['id'];
        }
        $tags_list = [];
        foreach ($result_tags as $val){
            $tags_list[] = [
                'url' => get_tagurl($val['id']),
                'title' => $val['tag']
            ];
        }

        // 问答插件
        $ask_list = [];
        if (is_dir('./weapp/Ask/')) {
            try{
                $askLogic = new \app\plugins\logic\AskLogic;
                $Askow = Db::name("weapp")->where(['code'=>'Ask'])->field("status,data")->find();
                if (!empty($Askow['status']) && 1 == $Askow['status']) {
                    $ask_map = [
                        'is_review' =>1,
                    ];

                    $ask_seo_pseudo = 1;
                    $Askow['data'] = safe_unserialize($Askow['data']);
                    if (!empty($Askow['data']['seo_pseudo'])) {
                        $ask_seo_pseudo = intval($Askow['data']['seo_pseudo']);
                    }
                    //问答首页
                    if (method_exists($askLogic, 'askurl')) {
                        $url = $askLogic->askurl('plugins/Ask/index', [], true, false, $ask_seo_pseudo);
                    } else {
                        $url = url('plugins/Ask/index', [], true, false, $ask_seo_pseudo);
                    }
                    $ask_list[] = [
                        'url' => auto_hide_index($url),
                        'title' => "问答首页"
                    ];
                    //问答栏目
                    $result_ask_type = Db::name("weapp_ask_type")->field("type_id,type_name")->order('sort_order asc')->select();
                    foreach ($result_ask_type as $val){
                        if (method_exists($askLogic, 'askurl')) {
                            $url = $askLogic->askurl('plugins/Ask/index', ['type_id'=>$val['type_id']],true,false,$ask_seo_pseudo);
                        } else {
                            $url = url('plugins/Ask/index', ['type_id'=>$val['type_id']],true,false,$ask_seo_pseudo);
                        }
                        $ask_list[] = [
                            'url' => auto_hide_index($url),
                            'title' => $val['type_name']
                        ];
                    }
                    //问答内容
                    $result_ask = Db::name('weapp_ask')->field('ask_id,type_id,ask_title')
                        ->where($ask_map)
                        ->order('ask_id desc')
                        ->select();
                    foreach ($result_ask as $val){
                        if (method_exists($askLogic, 'askurl')) {
                            $url = $askLogic->askurl('plugins/Ask/details', ['ask_id'=>$val['ask_id']],true,false,$ask_seo_pseudo);
                        } else {
                            $url = url('plugins/Ask/details', ['ask_id'=>$val['ask_id']],true,false,$ask_seo_pseudo);
                        }
                        $ask_list[] = [
                            'url' => auto_hide_index($url),
                            'title' => $val['ask_title']
                        ];
                    }
                    if ($is_create == false && !empty($result_ask[0]['ask_id']) && $result_ask[0]['ask_id'] > $last_ask){
                        $is_create = true;
                        $last_ask = $result_ask[0]['ask_id'];
                    }
                }
            }catch (\Exception $e){}
        }

        $msg = '';
        if ($is_create){
            //数据整合与生成
            $eyou = array(
                'seo_title' => $web_name.'_网站地图',
                'seo_keywords' => '',
                'seo_description' => '',
                'index' => ['url'=>request()->domain().ROOT_DIR.'/','title'=>$web_name],  //首页信息（url链接和title）
                'type_list' => $type_list,
                'arc_list' => $arc_list,
                'tags_list' => $tags_list,
                'ask_list' => $ask_list
            );
            $this->assign('eyou', $eyou);
            try {
                $savepath = "./sitemap.html";
                $tpl      = 'index';
                $this->filePutContents($savepath, $tpl, 'sitemap.htm');
                $sitemapid = $last_type."_".$last_arc."_".$last_tag."_".$last_ask;
                $r = tpSetting('system',['system_sitemapid1647228884'=>$sitemapid]);
            } catch (\Exception $e) {
                $msg .= '<span>sitemap.html生成失败！' . $e->getMessage() . '</span><br>';
            }
        }

        return $msg;
    }

    /*
      * 写入静态页面
      * $savepath    保存位置
      * $tpl         模板名称
      *
      */
    private function filePutContents($savepath, $tpl, $filename = 'sitemap.htm')
    {
        ob_start();
        static $templateConfig = null;
        null === $templateConfig && $templateConfig = \think\Config::get('template');
        $templateConfig['view_path'] = "./public/html/";
        $template                    = "./public/html/{$filename}";

        $content                     = $this->fetch($template, [], [], $templateConfig);

        /*解决模板里没有设置编码的情况*/
        if (!stristr($content, 'utf-8')) {
            $content = str_ireplace('<head>', "<head>\n<meta charset='utf-8'>", $content);
        }
        /*end*/
        echo $content;
        $_cache = ob_get_contents();
        ob_end_clean();

        if (preg_match('/\.txt$/i', $savepath) && substr($_cache, 0, 3) !== "\xEF\xBB\xBF") {
            $_cache = "\xEF\xBB\xBF" . $_cache;
        }

        static $File = null;
        null === $File && $File = new File;
        $File->fwrite($savepath, $_cache);
    }

    /**
     * 生成ai_content_index.html
     * @return [type] [description]
     */
    public function ajax_update_ai_content_index_html(){
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        $msg = $this->handleBuildAiContentIndex();
        if (IS_AJAX) {
            if (empty($msg)) {
                $this->success('更新成功');
            } else {
                $this->error('更新失败', null, ['msg'=>$msg]);
            }
        }
        if (empty($msg)) {
            exit('success');
        } else {
            exit($msg);
        }
    }

    /**
     * 生成llms.txt
     * @return [type] [description]
     */
    public function ajax_update_llms_txt(){
        if (empty($this->eyou['global']['php_servicemeal']) || $this->eyou['global']['php_servicemeal'] < 5) {
            if (IS_AJAX) {
                $this->error('该功能仅限AI版使用');
            }
            exit('forbidden');
        }
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        $msg = $this->handleBuildLlmsTxt();
        if (IS_AJAX) {
            if (empty($msg)) {
                $this->success('更新成功');
            } else {
                $this->error('更新失败', null, ['msg'=>$msg]);
            }
        }
        if (empty($msg)) {
            exit('success');
        } else {
            exit($msg);
        }
    }

    /*
     * 处理生成AI内容索引页html
     */
    private function handleBuildAiContentIndex()
    {
        $is_auto = input('param.is_auto/s', 'on'); // 是否自动生成，还是手工生成
        $sitemapid = tpSetting('system.system_sitemapid1647228885');  //四个模块最后一条id，type_arc_tag_ask
        $last_type = $last_arc = $last_tag = 0;
        $is_create = false;
        if (!is_file('./ai-content-index.html') || 'off' == $is_auto) {
            $is_create = true;
        }
        if (!empty($sitemapid)){
            $sitemapid_arr = explode("_",$sitemapid);
            $last_type = !empty($sitemapid_arr[0]) ? $sitemapid_arr[0] : 0;
            $last_arc = !empty($sitemapid_arr[1]) ? $sitemapid_arr[1] : 0;
            $last_tag = !empty($sitemapid_arr[2]) ? $sitemapid_arr[2] : 0;
        }

        list($eyou, $is_create, $last_type, $last_arc, $last_tag) = $this->ai_content_index_logic($is_create, $last_type, $last_arc, $last_tag);

        $msg = '';
        if ($is_create){
            $this->assign('eyou', $eyou);
            try {
                $savepath = "./ai-content-index.html";
                $tpl      = 'index';
                $this->filePutContents($savepath, $tpl, 'ai-content-index.htm');
                $sitemapid = $last_type."_".$last_arc."_".$last_tag;
                $r = tpSetting('system',['system_sitemapid1647228885'=>$sitemapid]);
            } catch (\Exception $e) {
                $msg .= '<span>ai-content-index.html生成失败！' . $e->getMessage() . '</span><br>';
            }
        }

        return $msg;
    }

    private function handleBuildLlmsTxt()
    {
        $is_auto = input('param.is_auto/s', 'on');
        $sitemapid = tpSetting('system.system_sitemapid1758600001');
        $last_type = $last_arc = $last_tag = 0;
        $is_create = false;
        if (!is_file('./llms.txt') || 'off' == $is_auto) {
            $is_create = true;
        }
        if (!empty($sitemapid)) {
            $sitemapid_arr = explode("_", $sitemapid);
            $last_type = !empty($sitemapid_arr[0]) ? $sitemapid_arr[0] : 0;
            $last_arc = !empty($sitemapid_arr[1]) ? $sitemapid_arr[1] : 0;
            $last_tag = !empty($sitemapid_arr[2]) ? $sitemapid_arr[2] : 0;
        }

        $force_lang = get_home_lang(true);
        list($eyou, $is_create, $last_type, $last_arc, $last_tag) = $this->ai_content_index_logic($is_create, $last_type, $last_arc, $last_tag, $force_lang);

        $msg = '';
        if ($is_create) {
            $this->assign('eyou', $this->llms_txt_logic($eyou));
            try {
                $savepath = './llms.txt';
                $tpl      = 'index';
                $this->filePutContents($savepath, $tpl, 'llms.txt.htm');
                $sitemapid = $last_type."_".$last_arc."_".$last_tag;
                $r = tpSetting('system',['system_sitemapid1758600001'=>$sitemapid]);
            } catch (\Exception $e) {
                $msg .= '<span>llms.txt生成失败！' . $e->getMessage() . '</span><br>';
            }
        }

        return $msg;
    }

    public function ai_content_index()
    {
        if (!empty($this->eyou['global']['php_servicemeal']) && $this->eyou['global']['php_servicemeal'] >= 5) {
            $html = '';
            $filename = "./public/html/ai-content-index.htm";
            if (file_exists($filename)) {
                list($eyou, $is_create, $last_type, $last_arc, $last_tag) = $this->ai_content_index_logic();
                $this->assign('eyou', $eyou);
                $html = $this->fetch($filename); // 渲染模板标签语法
            }

            return $html;
        }
        abort(404);
    }

    private function llms_txt_logic($eyou)
    {
        $field = !empty($eyou['field']) ? $eyou['field'] : [];
        $global = !empty($eyou['global']) ? $eyou['global'] : [];
        $basehost = !empty($global['web_basehost']) ? $global['web_basehost'] : '';
        $web_basehost = preg_replace('/^(([^\:\.]+):)?(\/\/)?([^\/\:]*)(.*)$/i', '${1}${3}${4}'.ROOT_DIR, $basehost);
        $web_name = !empty($global['web_name']) ? $global['web_name'] : (!empty($global['web_title']) ? $global['web_title'] : 'Website');
        $lang = !empty($field['lang']) ? $field['lang'] : get_home_lang(true);
        $field['llms_site_name'] = $web_name . "\n";
        $field['llms_site_description'] = (!empty($global['web_description']) ? trim($global['web_description']) : trim($field['seo_description'])) . "\n";
        $field['llms_language'] = $lang . "\n";
        $field['llms_base_url'] = $web_basehost;
        $field['llms_updated_at'] = MyDate('Y-m-d H:i:s', getTime()) . "\n";
        $field['llms_urls'] = [
            $web_basehost.'/',
            $web_basehost.'/sitemap.xml',
            $web_basehost.'/robots.txt',
            $field['ai_content_index_url'],
        ];
        $field['llms_content_types'] = [];
        if (!empty($field['channeltype_list'])) {
            foreach ($field['channeltype_list'] as $val) {
                $field['llms_content_types'][] = !empty($val['ntitle']) ? $val['ntitle'] : $val['nid'];
            }
        }
        $field['llms_high_value_sections'] = [];
        if (!empty($field['type_list'])) {
            $type_list = array_slice($field['type_list'], 0, 8);
            foreach ($type_list as $val) {
                if (empty($val['typename']) || empty($val['typeurl'])) {
                    continue;
                }
                $typeurl = rtrim($val['typeurl'], '/');
                $home_url = rtrim($web_basehost.'/', '/');
                if ($typeurl == $home_url || $typeurl == $home_url.'/index') {
                    continue;
                }
                $field['llms_high_value_sections'][] = [
                    'name' => $val['typename'],
                    'url' => $val['typeurl'],
                ];
            }
        }
        $field['llms_urls_text'] = '- '.implode(PHP_EOL.'- ', $field['llms_urls']) . "\n";
        $field['llms_content_types_text'] = '- '.implode(PHP_EOL.'- ', $field['llms_content_types']) . "\n";
        $section_lines = ['- 首页: '.$web_basehost.'/'];
        foreach ($field['llms_high_value_sections'] as $section) {
            $section_lines[] = '- '.$section['name'].': '.$section['url'];
        }
        if (!empty($field['ai_content_index_url'])) {
            $section_lines[] = '- AI 内容索引: '.$field['ai_content_index_url'];
        }
        $field['llms_high_value_sections_text'] = implode(PHP_EOL, $section_lines) . "\n";
        $field['llms_avoid_text'] = implode(PHP_EOL, [
            '- Admin pages',
            '- Login pages',
            '- User center pages',
            '- Parameter-heavy duplicate URLs',
        ]);
        $field['lang'] = $lang;
        $eyou['field'] = $field;
        return $eyou;
    }

    private function ai_content_index_logic($is_create = false, $last_type = 0, $last_arc = 0, $last_tag = 0, $lang = null)
    {
        $eyou = [];
        $lang = !empty($lang) ? $lang : get_current_lang();
        $zygn_channel_arr = $seodesc_channel_arr = [];
        $channeltype_list = model('Channeltype')->getAll('id,nid,ntitle,ctl_name', ['status'=>1,'id'=>['notin',[6,8,51]]], 'id');
        foreach ($channeltype_list as $key => $val) {
            if (1 == $val['id']) {
                $zygn_channel_arr[] = '文章发布';
                $seodesc_channel_arr[] = '文章';
            } else if (2 == $val['id']) {
                $zygn_channel_arr[] = '产品展示';
                $seodesc_channel_arr[] = '产品';
            } else if (3 == $val['id']) {
                $zygn_channel_arr[] = '图片相册';
                $seodesc_channel_arr[] = '图集';
            } else if (4 == $val['id']) {
                $zygn_channel_arr[] = '文件下载';
                $seodesc_channel_arr[] = '下载资源';
            } else if (5 == $val['id']) {
                $zygn_channel_arr[] = '视频播放';
                $seodesc_channel_arr[] = '视频';
            } else if (count($zygn_channel_arr) < 5) {
                $zygn_channel_arr[] = $val['ntitle'];
                $seodesc_channel_arr[] = $val['ntitle'];
            }
        }
        $eyou['field']['channeltype_list'] = $channeltype_list;
        $eyou['field']['zygn_channel_text'] = implode('、', $zygn_channel_arr);
        $eyou['field']['seo_title'] = '网站内容索引 - AI抓取友好页面';
        $eyou['field']['seo_keywords'] = '';
        $eyou['field']['seo_description'] = '本页面为AI爬虫提供网站完整内容索引，包括'.implode('、', $seodesc_channel_arr).'等所有内容类型，便于理解网站结构和内容';

        // 从自定义变量里找到电话、邮箱、地址
        $eyou['field']['web_telephone'] = '';
        $eyou['field']['web_email'] = '';
        $eyou['field']['web_address'] = '';
        $config_data = Db::name('config')->field('value')->where(['name'=>['like', 'web_attr_%'], 'lang'=>$lang])->select();
        foreach ($config_data as $key => $val) {
            if (check_mobile($val['value']) || check_landline($val['value'])) {
                $eyou['field']['web_telephone'] = $val['value'];
            } else if (check_email($val['value'])) {
                $eyou['field']['web_email'] = $val['value'];
            } else if (preg_match('/^(北京|天津|河北|山西|内蒙古|辽宁|吉林|黑龙江|上海|江苏|浙江|安徽|福建|江西|山东|河南|湖北|湖南|广东|广西|海南|重庆|四川|贵州|云南|西藏|陕西|甘肃|青海|宁夏|新疆|台湾|香港|澳门|中国)/i', $val['value'])) {
                $eyou['field']['web_address'] = $val['value'];
            }
        }

        //栏目信息
        $type_map = array(
            'channeltype'   => ['IN', config('global.channeltype_list')],
            'current_channel'   => ['IN', config('global.channeltype_list')],
            'status'    => 1,
            'is_del'    => 0,
            'lang'      => $lang,
        );
        // 过滤隐藏栏目
        if (isset($this->eyou['global']['sitemap_not1']) && $this->eyou['global']['sitemap_not1'] > 0) {
            $type_map['is_hidden'] = 0;
        }
        // 过滤外部模块
        if (isset($this->eyou['global']['sitemap_not2']) && $this->eyou['global']['sitemap_not2'] > 0) {
            $type_map['is_part'] = 0;
        }
        $result_arctype = Db::name('arctype')->field("*")
            ->where($type_map)
            ->order('id asc')
            ->getAllWithIndex('id');
        $last_type_new = reset($result_arctype);
        if ($is_create == false && !empty($last_type_new['id']) && $last_type_new['id'] > $last_type){
            $is_create = true;
            $last_type = $last_type_new['id'];
        }
        $type_list = [];
        foreach ($result_arctype as $sub){
            if ($sub['is_part'] == 1 && !empty($sub['typelink'])) {
                $sub['typeurl'] = $sub['typelink'];
            } else {
                $sub['typeurl'] = preg_replace('/\/index\.php\?m=/i', '/index.php?m=', get_typeurl($sub, false));
            }
            $type_list[] = $sub;
        }
        $eyou['field']['type_list'] = $type_list;

        /*定时文档显示插件*/
        if (is_dir('./weapp/TimingTask/')) {
            $weappModel = new \app\admin\model\Weapp;
            $TimingTaskRow = $weappModel->getWeappList('TimingTask');
        }
        /*end*/

        //文档信息
        foreach ($channeltype_list as $_ckey => $_cval) {
            $arc_map = array(
                'channel'   => $_cval['id'],
                'arcrank'   => array('gt', -1),
                'status'    => 1,
                'is_del'    => 0,
                'lang'      => $lang,
            );
            // 过滤外部模块
            if (isset($this->eyou['global']['sitemap_not2']) && $this->eyou['global']['sitemap_not2'] > 0) {
                $arc_map['is_jump'] = 0;
            }
            /*定时文档显示插件*/
            if (!empty($TimingTaskRow)) {
                if (!empty($TimingTaskRow['status']) && 1 == $TimingTaskRow['status']) {
                    $arc_map['add_time'] = ['elt', getTime()]; // 只显当天或之前的文档
                }
            }
            /*end*/
            if (!isset($this->eyou['global']['sitemap_archives_num']) || $this->eyou['global']['sitemap_archives_num'] === '') {
                $sitemap_archives_num = 1000;
            } else {
                $sitemap_archives_num = intval($this->eyou['global']['sitemap_archives_num']);
            }
            $arc_total = Db::name('archives')->where($arc_map)->count('aid');
            $result_archives = Db::name('archives')->field("*")
                ->where($arc_map)
                ->order('aid desc')
                ->limit($sitemap_archives_num)
                ->select();
            $arc_list = [];
            if ($is_create == false && !empty($result_archives[0]['aid']) && $result_archives[0]['aid'] > $last_arc){
                $is_create = true;
                $last_arc = $result_archives[0]['aid'];
            }
            $aidArr = array();
            foreach ($result_archives as $val){
                if (empty($result_arctype[$val['typeid']])){
                    continue;
                }
                $val = array_merge($result_arctype[$val['typeid']], $val);
                if ($val['is_jump'] == 1) {
                    $val['arcurl'] = $val['jumplinks'];
                } else {
                    $val['arcurl'] = preg_replace('/\/index\.php\?m=/i', '/index.php?m=', get_arcurl($val, false));
                }
                $arc_list[] = $val;
                array_push($aidArr, $val['aid']); // 文档ID数组
            }

            if (!empty($aidArr)) {
                if (3 == $_cval['id']) {
                    /*图集相册*/
                    $imagesUploadModel = new \app\home\model\ImagesUpload;
                    $images_uploads = $imagesUploadModel->getImgUpload($aidArr);
                    foreach ($arc_list as $key => $val) {
                        $image_list = !empty($images_uploads[$val['aid']]) ? $images_uploads[$val['aid']] : [];
                        foreach ($image_list as $k1 => $v1) {
                            $image_list[$k1]['image_url'] = handle_subdir_pic($v1['image_url']);
                            isset($v1['intro']) && $image_list[$k1]['intro'] = htmlspecialchars_decode($v1['intro']);
                        }
                        $val['image_list'] = $image_list;
                        $arc_list[$key] = $val;
                    }
                } else if (4 == $_cval['id']) {
                    /*下载资料列表*/
                    $downloadFileModel = new \app\home\model\DownloadFile;
                    $download_files = $downloadFileModel->getDownFile($aidArr);
                    foreach ($arc_list as $key => $val) {
                        $file_list = !empty($download_files[$val['aid']]) ? $download_files[$val['aid']] : [];
                        foreach ($file_list as $k1 => $v1) {
                            $val['file_size'] = '0KB';
                            $file_list[$k1]['file_url'] = handle_subdir_pic($v1['file_url'], 'soft');
                            if (empty($v1['file_size'])) {
                                $file_list[$k1]['file_size'] = '';
                            } else {
                                $file_list[$k1]['file_size'] = format_bytes($v1['file_size']);
                                $val['file_size'] = $file_list[$k1]['file_size'];
                            }
                        }
                        $val['file_list'] = $file_list;
                        $arc_list[$key] = $val;
                    }
                } else if (5 == $_cval['id']) {
                    $content_row = Db::name('media_content')->field('aid,total_duration,total_video')->where(['aid'=>['IN', $aidArr]])->getAllWithIndex('aid');
                    /*视频文件列表*/
                    // $mediaFileModel = new \app\home\model\MediaFile;
                    // $video_files = $mediaFileModel->getMediaFile($aidArr);
                    foreach ($arc_list as $key => $val) {
                        $val = array_merge($content_row[$val['aid']], $val);
                        // $file_list = !empty($video_files[$val['aid']]) ? $video_files[$val['aid']] : [];
                        // $val['file_list'] = $file_list;
                        // $val['total_video'] = count($file_list);
                        $val['courseware'] = !empty($val['courseware']) ? url('home/View/download_media_file',['aid'=>$val['aid']]) : '';
                        $val['total_duration'] = gmSecondFormat($val['total_duration'], ':');
                        $arc_list[$key] = $val;
                    }
                }
            }

            $eyou['field']['arc_list'][$_cval['id']]['list'] = $arc_list;
            $eyou['field']['arc_list'][$_cval['id']]['total'] = $arc_total;
            $eyou['field']['arc_list'][$_cval['id']]['channel_info'] = $_cval;
        }

        //tags页面
        if (!isset($this->eyou['global']['sitemap_tags_num']) || $this->eyou['global']['sitemap_tags_num'] === '') {
            $sitemap_tags_num = 1000;
        } else {
            $sitemap_tags_num = intval($this->eyou['global']['sitemap_tags_num']);
        }
        $tags_map = array(
            'lang'      => $lang,
        );
        $field = "id, add_time, tag, total";
        $result_tags = Db::name('tagindex')->field($field)
            ->where($tags_map)
            ->order('id desc')
            ->limit($sitemap_tags_num)
            ->select();
        if ($is_create == false && !empty($result_tags[0]['id']) && $result_tags[0]['id'] > $last_tag){
            $is_create = true;
            $last_tag = $result_tags[0]['id'];
        }
        $tags_list = [];
        foreach ($result_tags as $val){
            $tags_list[] = [
                'link' => get_tagurl($val['id']),
                'tag' => $val['tag'],
                'total' => $val['total'],
            ];
        }
        $eyou['field']['tags_list'] = $tags_list;

        // FAQ
        $faq_list = [];
        $result = Db::name("faq_asklist")->alias('a')
            ->field("a.*, b.group_title")
            ->join('faq_group b', 'a.group_id = b.group_id AND a.lang = b.lang', 'LEFT')
            ->where([
                'a.lang' => $lang,
                'b.status'  => 1,
            ])
            ->orderRaw('a.sort_order asc, a.asklist_id desc')
            ->select();
        foreach ($result as $key => $val) {
            $val['asklist_title'] = htmlspecialchars_decode($val['asklist_title']);
            $val['asklist_content'] = htmlspecialchars_decode($val['asklist_content']);
            $val['asklist_content'] = str_replace(PHP_EOL, '<br/>', $val['asklist_content']);
            $faq_list[$key] = $val;
        }
        $eyou['field']['faq_list'] = $faq_list;

        // ai索引页的链接
        $web_basehost = preg_replace('/^(([^\:\.]+):)?(\/\/)?([^\/\:]*)(.*)$/i', '${1}${3}${4}'.ROOT_DIR, $this->eyou['global']['web_basehost']);
        $ai_content_index_url = "{$web_basehost}/ai-content-index.html";
        $eyou['field']['ai_content_index_url'] = $ai_content_index_url;

        $eyou = array_merge($this->eyou, $eyou);

        return [$eyou, $is_create, $last_type, $last_arc, $last_tag];
    }
}
