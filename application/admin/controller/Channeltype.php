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

namespace app\admin\controller;

use think\Page;
use think\Db;
use app\admin\logic\FieldLogic;
use think\template\taglib\Eyou;

class Channeltype extends Base
{
    // 系统默认的模型ID，不可删除
    private $channeltype_system_id = [];

    // 系统内置不可用的模型标识，防止与home分组的控制器重名覆盖，导致网站报错
    private $channeltype_system_nid = ['base','index','lists','search','tags','view','left','right','top','bottom','ajax'];

    // 数据库对象
    public $channeltype_db;
    
    public function _initialize() {
        parent::_initialize();
        $eyou = new Eyou('');
        $this->channeltype_system_nid = array_merge($this->channeltype_system_nid, array_keys($eyou->getTags()));
        $this->channeltype_db = Db::name('channeltype');
        $this->channeltype_system_id = $this->channeltype_db->where([
                'ifsystem'  => 1,
            ])->column('id');
    }

    public function index()
    {
        // model('Channeltype')->setChanneltypeStatus(); // 根据前端模板自动开启系统模型

        $list = array();
        $param = input('param.');
        $condition = array();
        // 应用搜索条件
        foreach (['keywords'] as $key) {
            if (isset($param[$key]) && $param[$key] !== '') {
                if ($key == 'keywords') {
                    $condition['a.title'] = array('LIKE', "%{$param[$key]}%");
                } else {
                    $condition['a.'.$key] = array('eq', $param[$key]);
                }
            }
        }

        $count = $this->channeltype_db->alias('a')->where($condition)->count('id');// 查询满足要求的总记录数
        $pageObj = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $this->channeltype_db->alias('a')
            ->where($condition)
            ->order('ifsystem desc, id asc')
            ->limit($pageObj->firstRow.','.$pageObj->listRows)
            ->select();

        $pageStr = $pageObj->show();// 分页显示输出
        $this->assign('pageStr',$pageStr);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pageObj',$pageObj);// 赋值分页对象

        return $this->fetch();
    }
    
    /**
     * 新增
     */
    public function add()
    {
        if (IS_POST) {
            $post = input('post.');
            if (!empty($post)) {
                $post['title'] = trim($post['title']);
                if (empty($post['title'])) {
                    $this->error('模型名称不能为空！');
                }

                $post['nid'] = trim($post['nid']);
                if (empty($post['nid'])) {
                    $this->error('模型标识不能为空！');
                } else {
                    if (!preg_match('/^([a-z]+)([a-z0-9]*)$/i', $post['nid'])) {
                        $this->error('模型标识必须以小写字母开头！');
                    } else if (in_array($post['nid'], $this->channeltype_system_nid)) {
                        $this->error('系统禁用当前模型标识，请更改！');
                    }
                }

                $post['nid']    = strtolower($post['nid']);
                $nid = $post['nid'];
                $post['ctl_name'] = ucwords($nid);
                $post['table']    = $nid;
                
                if($this->channeltype_db->where(['nid'=>$nid])->count('id') > 0){
                    $this->error('该模型标识已存在，请检查', url('Channeltype/index'));
                }

                // 创建文件以及数据表
                $this->create_sql_file($post);

                $nowData = array(
                    'ntitle'        => $post['title'],
                    'nid'           => $nid,
                    'add_time'      => getTime(),
                    'update_time'   => getTime(),
                );
                $data = array_merge($post, $nowData);
                $insertId = $this->channeltype_db->insertGetId($data);
                $_POST['id'] = $insertId;
                if ($insertId) {
                    // 复制模型字段基础数据
                    $fieldLogic = new FieldLogic;
                    $fieldLogic->synArchivesTableColumns($insertId);
                    try {
                        schemaTable($post['table'].'_content');
                    } catch (\Exception $e) {}

                    delFile(CACHE_PATH, true);
                    extra_cache('admin_channeltype_list_logic', NULL);
                    adminLog('新增模型：'.$post['title']);
                    $this->success("操作成功", url('Channeltype/index'));
                }
            }
            $this->error("操作失败");
        }

        return $this->fetch();
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $id = input('id/d');

        if (IS_POST) {
            $post = input('post.');
            if(!empty($post['id'])){
                $post['title'] = trim($post['title']);

                if (in_array($post['id'], $this->channeltype_system_id)) {
                    unset($post['title']);
                } else {
                    if (empty($post['title'])) {
                        $this->error('模型名称不能为空！');
                    }

                    $map = array(
                        'id'    => ['NEQ', $post['id']],
                        'nid' => strtolower($post['nid']),
                    );
                    if($this->channeltype_db->where($map)->count('id') > 0){
                        $this->error('该模型标识已存在，请检查', url('Channeltype/index'));
                    }
                }

                $nowData = array(
                    'update_time'       => getTime(),
                );
                unset($post['nid']);
                $data = array_merge($post, $nowData);
                $r = $this->channeltype_db
                    ->where(['id'=>$post['id']])
                    ->cache(true,null,"channeltype")
                    ->update($data);
                if ($r) {
                    extra_cache('admin_channeltype_list_logic', NULL);
                    adminLog('编辑模型：'.$data['title']);
                    $this->success("操作成功", url('Channeltype/index'));
                }
            }
            $this->error("操作失败");
        }

        $assign_data = array();

        $info = $this->channeltype_db->field('a.*')
            ->alias('a')
            ->where(array('a.id'=>$id))
            ->find();
        if (empty($info)) {
            $this->error('数据不存在，请联系管理员！');
            exit;
        }
        $assign_data['field'] = $info;

        $this->assign($assign_data);
        return $this->fetch();
    }

    
    /**
     * 删除
     */
    public function del()
    {
        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if(IS_POST){
            if(!empty($id_arr)){
                foreach ($id_arr as $key => $val) {
                    if (array_key_exists($val, $this->channeltype_system_id)) {
                        $this->error('系统内置模型，禁止删除！');
                    }
                } 

                $result = $this->channeltype_db->field('title,nid')->where("id",'IN',$id_arr)->select();
                $title_list = get_arr_column($result, 'title');

                $r = $this->channeltype_db->where("id",'IN',$id_arr)->delete();
                if ($r) {
                    // 删除栏目
                    $arctype = Db::name('arctype')->where("channeltype",'IN',$id_arr)
                        ->whereOr("current_channel", 'IN', $id_arr)
                        ->delete();
                    // 删除文章
                    $archives = Db::name('archives')->where("channel",'IN',$id_arr)->delete();
                    // 删除自定义字段
                    $channelfield = Db::name('channelfield')->where("channel_id",'IN',$id_arr)->delete();

                    // 删除文件
                    foreach ($result as $key => $value) {
                        $nid = $value['nid'];

                        try {
                            // 删除相关数据表
                            Db::execute('DROP TABLE '.PREFIX.$nid.'_content');
                        } catch (\Exception $e) {}

                        $filelist_path = 'data/model/custom_model_path/'.$nid.'.filelist.txt';
                        $fileStr = file_get_contents($filelist_path);
                        $filelist = explode("\n\r", $fileStr);
                        foreach ($filelist as $k1 => $v1) {
                            $v1 = trim($v1);
                            if (!empty($v1)) {
                                @unlink($v1);
                            }
                        }
                        @unlink($filelist_path);
                        delFile('application/admin/template/'.$nid, true);
                    }
                    
                    delFile(CACHE_PATH, true);
                    extra_cache('admin_channeltype_list_logic', NULL);
                    adminLog('删除模型：'.implode(',', $title_list));
                    $this->success('删除成功');
                }
                $this->error('删除失败');
            }
            $this->error('参数有误');
        }
        $this->error('非法访问');
    }

    // 解析sql语句
    private function sql_split($sql, $tablepre) {
        if ($tablepre != "ey_")
            $sql = str_replace("`ey_", '`'.$tablepre, $sql);
              
        $sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sql);
        
        $sql = str_replace("\r", "\n", $sql);
        $ret = array();
        $num = 0;
        $queriesarray = explode(";\n", trim($sql));
        unset($sql);
        foreach ($queriesarray as $query) {
            $ret[$num] = '';
            $queries = explode("\n", trim($query));
            $queries = array_filter($queries);
            foreach ($queries as $query) {
                $str1 = substr($query, 0, 1);
                if ($str1 != '#' && $str1 != '-')
                    $ret[$num] .= $query;
            }
            $num++;
        }
        return $ret;
    }

    // 创建文件以及数据表
    private function create_sql_file($post) {
        $demopath = 'data/model/';
        $fileArr = []; // 生成的相关文件记录
        $filelist = getDirFile($demopath);
        foreach ($filelist as $key => $file) {
            if (stristr($file, 'custom_model_path')) {
                unset($filelist[$key]);
                continue;
            }
            $src = $demopath.$file;
            $dst = $file;
            $dst = str_replace('CustomModel', $post['ctl_name'], $dst);
            $dst = str_replace('custommodel', $post['nid'], $dst);
            /*记录相关文件*/
            if (!stristr($dst, 'custom_model_path')) {
                array_push($fileArr, $dst);
            }
            /*--end*/
            if(tp_mkdir(dirname($dst))) {
                $fileContent = @file_get_contents($src);
                $fileContent = str_replace('CustomModel', $post['ctl_name'], $fileContent);
                $fileContent = str_replace('custommodel', strtolower($post['nid']), $fileContent);
                $fileContent = str_replace('CUSTOMMODEL', strtoupper($post['nid']), $fileContent);
                $view_suffix = config('template.view_suffix');
                if (stristr($file, 'lists_custommodel.'.$view_suffix)) {
                    $replace = <<<EOF
<section class="article-list">
                    {eyou:list pagesize="10" titlelen="38"}
                    <article>
                        {eyou:notempty name="\$field.is_litpic"}
                        <a href="{\$field.arcurl}" title="{\$field.title}" style="float: left; margin-right: 10px"> <img src="{\$field.litpic}" alt="{\$field.title}" height="100" /> </a>
                        {/eyou:notempty} 
                        <h2><a href="{\$field.arcurl}" class="">{\$field.title}</a><span>{\$field.click}°C</span></h2>
                        <div class="excerpt">
                            <p>{\$field.seo_description}</p>
                        </div>
                        <div class="meta">
                            <span class="item"><time>{\$field.add_time|MyDate='Y-m-d',###}</time></span>
                            <span class="item">{\$field.typename}</span>
                        </div>
                    </article>
                    {/eyou:list}
                </section>
                <section class="list-pager">
                    {eyou:pagelist listitem='index,pre,pageno,next,end' listsize='2' /}
                    <div class="clear"></div>
                </section>
EOF;
                    $fileContent = str_replace("<!-- #list# -->", $replace, $fileContent);
                }
                $puts = @file_put_contents($dst, $fileContent);
                if (!$puts) {
                    $this->error('创建自定义模型生成相关文件失败，请检查站点目录权限！');
                }
            }
        }
        @file_put_contents($demopath.'custom_model_path/'.$post['nid'].'.filelist.txt', implode("\n\r", $fileArr));

        // 创建自定义模型附加表
        $table = 'ey_'.$post['table'].'_content';
        $tableSql = <<<EOF
CREATE TABLE `{$table}` (
  `id`          int(10) NOT NULL    AUTO_INCREMENT,
  `aid`         int(10) DEFAULT '0' COMMENT         '文档ID',
  `add_time`    int(11) DEFAULT '0' COMMENT         '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT         '更新时间',
  PRIMARY KEY (`id`),
  KEY `aid` (`aid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='附加表';
EOF;
        $sqlFormat  = $this->sql_split($tableSql, PREFIX);

        // 执行SQL语句
        try {
            $counts = count($sqlFormat);
            for ($i = 0; $i < $counts; $i++) {
                $sql = trim($sqlFormat[$i]);
                if (stristr($sql, 'CREATE TABLE')) {
                    Db::execute($sql);
                } else {
                    if(trim($sql) == '')
                       continue;
                    Db::execute($sql);
                }
            }
        } catch (\Exception $e) {
            $this->error('数据库表创建失败，请检查'.$table.'表是否存在并删除，不行就请求技术支持！');
        }
    }

    /**
     * 检测模板并启用与禁用
     */
    public function ajax_show()
    {
        if (IS_POST) {
            $id = input('id/d');
            $status = input('status/d', 0);
            if(!empty($id)){
                $row = Db::name('channeltype')->where([
                        'id'    => $id,
                    ])->find();

                $nofileArr = [];
                /*检测模板是否存在*/
                $tplplan = 'template/pc';
                $planPath = realpath($tplplan);
                if (!file_exists($planPath)) {
                    $this->success('操作成功', null, ['confirm'=>0]);
                }
                $view_suffix = config('template.view_suffix');
                // 检测列表模板是否存在
                $lists_filename = 'lists_'.$row['nid'].'.'.$view_suffix;
                if (!file_exists($planPath.DS.$lists_filename)) {
                    $filename = ROOT_DIR.DS.$tplplan.DS.$lists_filename;
                    $nofileArr[] = [
                        'type'  => 'lists',
                        'title' => '列表模板：',
                        'file'  => str_replace('\\', '/', $filename),
                    ];
                }
                // 检测文档模板是否存在
                if (!in_array($row['nid'], ['single','guestbook'])) {
                    $view_filename = 'view_'.$row['nid'].'.'.$view_suffix;
                    if (!file_exists($planPath.DS.$view_filename)) {
                        $filename = ROOT_DIR.DS.$tplplan.DS.$view_filename;
                        $nofileArr[] = [
                            'type'  => 'view',
                            'title' => '文档模板：',
                            'file'  => str_replace('\\', '/', $filename),
                        ];
                    }
                }
                /*--end*/

                if (empty($status) || (1 == $status && empty($nofileArr))) {
                    $r = Db::name('channeltype')->where([
                            'id'    => $id,
                        ])
                        ->cache(true,null,"channeltype")
                        ->update([
                            'status'    => $status,
                            'update_time'   => getTime(),
                        ]);
                    if($r){
                        extra_cache('admin_channeltype_list_logic', NULL);
                        adminLog('编辑【'.$row['title'].'】的状态为：'.(!empty($status)?'启用':'禁用'));
                        $this->success('操作成功', null, ['confirm'=>0]);
                    }else{
                        $this->error('操作失败', null, ['confirm'=>0]);
                    }
                } else {
                    $tpltype = [];
                    $msg = "该模型缺少以下模板，系统将自动创建一个简单模板文件：<br/>";
                    foreach ($nofileArr as $key => $val) {
                        $msg .= '<font color="red">'.$val['title'].$val['file']."</font><br/>";
                        $tpltype[] = $val['type'];
                    }
                    $this->success($msg, null, ['confirm'=>1,'tpltype'=>base64_encode(json_encode($tpltype))]);
                }
            } else {
                $this->error('参数有误');
            }
        }
        $this->error('非法访问');
    }

    /**
     * 启用并创建模板
     */
    public function ajax_check_tpl()
    {
        if (IS_POST) {
            $id = input('id/d');
            $status = input('status/d');
            if(!empty($id)){
                $row = Db::name('channeltype')->where([
                        'id'    => $id,
                    ])->find();
                $r = Db::name('channeltype')->where([
                        'id'    => $id,
                    ])
                    ->cache(true,null,"channeltype")
                    ->update([
                        'status'    => $status,
                        'update_time'   => getTime(),
                    ]);
                if($r){
                    $tpltype = input('post.tpltype/s');
                    $tpltype = json_decode(base64_decode($tpltype), true);
                    if (!empty($tpltype)) {
                        $view_suffix = config('template.view_suffix');
                        $themeStyleArr = ['pc','mobile'];
                        foreach ($themeStyleArr as $k1 => $theme) {
                            $tplplan = "template/{$theme}";
                            $planPath = realpath($tplplan);
                            if (file_exists($planPath)) {
                                foreach ($tpltype as $k2 => $val) {
                                    $source = realpath("data/model/template/{$theme}/{$val}_custommodel.{$view_suffix}");
                                    $dest = ROOT_PATH."template/{$theme}/{$val}_{$row['nid']}.{$view_suffix}";
                                    if (!file_exists($dest)) {
                                        $content = file_get_contents($source);
                                        if ('lists' == $val) {
                                            if ('download' == $row['nid'])
                                            {
                                                $replace = <<<EOF
<section class="article-list">
                    {eyou:list pagesize="10" titlelen="38"}
                    <article>
                        {eyou:notempty name="\$field.is_litpic"}
                        <a href="{\$field.arcurl}" title="{\$field.title}" style="float: left; margin-right: 10px"> <img src="{\$field.litpic}" alt="{\$field.title}" height="100" /> </a>
                        {/eyou:notempty} 
                        <h2><a href="{\$field.arcurl}" class="">{\$field.title}</a><span>{\$field.click}°C</span></h2>
                        <div class="excerpt">
                            <p>{\$field.seo_description}</p>
                        </div>
                        <div class="meta">
                            <span class="item"><time>{\$field.add_time|MyDate='Y-m-d',###}</time></span>
                            <span class="item">{\$field.typename}</span>
                            {eyou:arcview aid='\$field.aid' id='view'}
                                  {eyou:volist name="\$view.file_list" id='vo'}
                                  <span class="item"><a class="btn" href="{\$vo.downurl}" title="{\$vo.title}">下载包({\$i})</a></span>
                                  {/eyou:volist}
                            {/eyou:arcview}
                        </div>
                    </article>
                    {/eyou:list}
                </section>
                <section class="list-pager">
                    {eyou:pagelist listitem='index,pre,pageno,next,end' listsize='2' /}
                    <div class="clear"></div>
                </section>
EOF;
                                                $content = str_replace("<!-- #download# -->", $replace, $content);
                                            }
                                            else if ('single' == $row['nid']) 
                                            {
                                                $replace = <<<EOF
<article class="content">
                    <h1>{\$eyou.field.title}</h1>
                    <div class="post">
                        {\$eyou.field.content}
                    </div>
                </article>
EOF;
                                                $content = str_replace("<!-- #single# -->", $replace, $content);
                                            }
                                            else if ('guestbook' == $row['nid'])
                                            {
                                                $replace = <<<EOF
<article class="content">
                    <h1>{\$eyou.field.title}</h1>
                    <div class="post">
                        <div class="md_block">
                            <div style=" color: #ff0000">
                                制作易优留言表单，主要有三个步骤：<br>1，后台>开启留言模型，建立栏目并选择留言模型。<br>2，打开根目录>template>pc>lists_guestbook.htm模板文件，按照易优表单标签制作，<a href="http://www.eyoucms.com/doc/label/arc/502.html" target="_blank">点击这里查看教程</a><br>3，还有疑问可以加易优交流群（群号：<a target="_blank" href="//shang.qq.com/wpa/qunwpa?idkey=917f9a4cfe50fd94600c55eb75d9c6014a1842089b0479bc616fb79a1d85ae0b">704301718</a>）
                            </div>
                        </div>           
                    </div>
                </article>
                <section class="pager"></section>
EOF;
                                                $content = str_replace("<!-- #guestbook# -->", $replace, $content);
                                            } else {
                                                $replace = <<<EOF
<section class="article-list">
                    {eyou:list pagesize="10" titlelen="38"}
                    <article>
                        {eyou:notempty name="\$field.is_litpic"}
                        <a href="{\$field.arcurl}" title="{\$field.title}" style="float: left; margin-right: 10px"> <img src="{\$field.litpic}" alt="{\$field.title}" height="100" /> </a>
                        {/eyou:notempty} 
                        <h2><a href="{\$field.arcurl}" class="">{\$field.title}</a><span>{\$field.click}°C</span></h2>
                        <div class="excerpt">
                            <p>{\$field.seo_description}</p>
                        </div>
                        <div class="meta">
                            <span class="item"><time>{\$field.add_time|MyDate='Y-m-d',###}</time></span>
                            <span class="item">{\$field.typename}</span>
                        </div>
                    </article>
                    {/eyou:list}
                </section>
                <section class="list-pager">
                    {eyou:pagelist listitem='index,pre,pageno,next,end' listsize='2' /}
                    <div class="clear"></div>
                </section>
EOF;
                                                $content = str_replace("<!-- #list# -->", $replace, $content);
                                            }
                                        }
                                        else if ('view' == $val)
                                        { // 内置模型设有内容字段
                                            if (1 == $row['ifsystem'])
                                            {
                                                $replace = <<<EOF
<div class="md_block">
                            {\$eyou.field.content}
                        </div>
EOF;
                                                $content = str_replace('<!-- #content# -->', $replace, $content);
                                            }
                                            if ('product' == $row['nid'])
                                            {
                                                $replace = <<<EOF
<div class="md_block">
                          <!--购物车组件start--> 
                          {eyou:sppurchase id='field'}
                              <div class="ey-price"><span>￥{\$field.users_price}</span> </div>
                              <div class="ey-number">
                                <label>数量</label>
                                <div class="btn-input">
                                  <button class="layui-btn" {\$field.ReduceQuantity}>-</button>
                                  <input type="text" class="layui-input" {\$field.UpdateQuantity}>
                                  <button class="layui-btn" {\$field.IncreaseQuantity}>+</button>
                                </div>
                              </div>
                              <div class="ey-buyaction">
                              <a class="ey-joinin" href="JavaScript:void(0);" {\$field.ShopAddCart}>加入购物车</a>
                              <a class="ey-joinbuy" href="JavaScript:void(0);" {\$field.BuyNow}>立即购买</a>
                              </div>
                              {\$field.hidden}
                          {/eyou:sppurchase}
                          <!--购物车组件end--> 
                        </div>
                        <div class="md_block">
                            <fieldset>
                                <legend>图片集：</legend>
                                <div class="pic">
                                    <div class="wrap">
                                        {eyou:volist name="\$eyou.field.image_list"}
                                            <img src="{\$field.image_url}" alt="{\$eyou.field.title}" />
                                        {/eyou:volist}
                                    </div>
                                </div> 
                            </fieldset>
                        </div>
                        <div class="md_block">
                            <fieldset>
                                <legend>产品属性：</legend>
                                {eyou:attribute type='auto'}
                                    {\$attr.name}：{\$attr.value}<br/>
                                {/eyou:attribute}
                            </fieldset>
                        </div>
EOF;
                                                $content = str_replace('<!-- #product# -->', $replace, $content);
                                            } else if ('images' == $row['nid']) {
                                                $replace = <<<EOF
<div class="md_block">
                            <fieldset>
                                <legend>图片集：</legend>
                                <div class="pic">
                                    <div class="wrap">
                                        {eyou:volist name="\$eyou.field.image_list"}
                                            <img src="{\$field.image_url}" alt="{\$eyou.field.title}" />
                                        {/eyou:volist}
                                    </div>
                                </div> 
                            </fieldset>
                        </div>
EOF;
                                                $content = str_replace('<!-- #images# -->', $replace, $content);
                                            } else if ('download' == $row['nid']) {
                                                $replace = <<<EOF
<div class="md_block">
                            <fieldset>
                                <legend>下载地址：</legend>
                                 {eyou:volist name="\$eyou.field.file_list" id="field"}
                                    <a class="btn" href="{\$field.downurl}" title="{\$field.title}">下载包（{\$i}）</a> 
                                 {/eyou:volist}
                            </fieldset>
                        </div>
EOF;
                                                $content = str_replace('<!-- #download# -->', $replace, $content);
                                            }
                                        }
                                        @file_put_contents($dest, $content);
                                    }
                                }
                            }
                        }
                    }
                    extra_cache('admin_channeltype_list_logic', NULL);
                    adminLog('编辑【'.$row['title'].'】的状态为：'.(!empty($status)?'启用':'禁用'));
                    $this->success('操作成功');
                }else{
                    $this->error('操作失败');
                }
            } else {
                $this->error('参数有误');
            }
        }
        $this->error('非法访问');
    }
}