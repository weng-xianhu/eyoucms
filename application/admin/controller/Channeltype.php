<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海口快推科技有限公司，并保留所有权利。
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
            $param[$key] = !empty($param[$key]) ? addslashes(trim($param[$key])) : '';
            if (isset($param[$key]) && $param[$key] !== '') {
                if ($key == 'keywords') {
                    $condition['a.title'] = array('LIKE', "%{$param[$key]}%");
                } else {
                    $condition['a.'.$key] = array('eq', $param[$key]);
                }
            }
        }

        $nids = [];
        if (2 > $this->php_servicemeal) array_push($nids, 'ask');
        if (1.5 > $this->php_servicemeal) array_push($nids, 'media');
        !empty($nids) && $condition['a.nid'] = ['NOTIN', $nids];
        $condition['a.is_del'] = 0;

        $count = $this->channeltype_db->alias('a')->where($condition)->count('id');// 查询满足要求的总记录数
        $pageObj = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $this->channeltype_db->alias('a')
            ->where($condition)
            ->order('ifsystem desc, id asc')
            ->limit($pageObj->firstRow.','.$pageObj->listRows)
            ->select();

        // 内容字段数
        $channelfieldRow = Db::name('channelfield')->field('channel_id, count(id) as num')
            ->where([
                'ifcontrol' => 0,
            ])->group('channel_id')
            ->getAllWithIndex('channel_id');
        // 留言属性数
        $gbAttributeRow = Db::name('guestbook_attribute')->field('8 as channel_id, count(attr_id) as num')
            ->where([
                'lang'      => $this->admin_lang,
                'is_del'    => 0,
            ])->getAllWithIndex('channel_id');
        if (is_array($channelfieldRow) && is_array($gbAttributeRow)) {
            $channelfieldRow = array_merge($channelfieldRow, $gbAttributeRow);
        }
        $channelfieldRow = convert_arr_key($channelfieldRow,'channel_id');
        $this->assign('channelfieldRow', $channelfieldRow);

        $pageStr = $pageObj->show();// 分页显示输出
        $this->assign('page',$pageStr);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pager',$pageObj);// 赋值分页对象

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
                $post['nid']    = strtolower($post['nid']);
                if (empty($post['nid'])) {
                    $this->error('模型标识不能为空！');
                } else {
                    if (!preg_match('/^([a-z]+)([a-z0-9]*)$/i', $post['nid'])) {
                        $this->error('模型标识必须以小写字母开头！');
                    } else if (preg_match('/^ey([a-z0-9]*)$/i', $post['nid'])) {
                        $this->error('模型标识禁止以ey开头！');
                    } else if (in_array($post['nid'], $this->channeltype_system_nid)) {
                        $this->error('系统禁用当前模型标识，请更改！');
                    }
                }

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
                    'data'          => '',
                    'is_release'    => 1,
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
                        /*追加到快速入口列表*/
                        $this->syn_custom_quickmenu($data, $insertId);
                        /*end*/

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
     * 同步自定义模型的快捷导航
     */
    private function syn_custom_quickmenu($data, $insertId)
    {
        $saveData = [
            [
                'title' => $data['title'],
                'laytext'   => $data['title'].'列表',
                'type' => 1,
                'controller' => 'Custom',
                'action' => 'index',
                'vars' => 'channel='.$insertId,
                'sort_order' => 100,
                'groups'    => 1,
                'add_time' => getTime(),
                'update_time' => getTime(),
            ],
            [
                'title' => $data['title'],
                'laytext'   => $data['title'].'列表',
                'type' => 2,
                'controller' => 'Custom',
                'action' => 'index',
                'vars' => 'channel='.$insertId,
                'sort_order' => 100,
                'groups'    => 1,
                'add_time' => getTime(),
                'update_time' => getTime(),
            ],
        ];
        model('Quickentry')->saveAll($saveData);
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $assign_data = array();
        $system_is_article_pay = tpSetting('system.system_is_article_pay', [], 'cn');
        $assign_data['system_is_article_pay'] = $system_is_article_pay;

        $id = input('id/d');
        if (IS_POST) {
            $post = input('post.');
            if(!empty($post['id'])){
                $post['id'] = intval($post['id']);
                $post['title'] = trim($post['title']);

                if (in_array($post['id'], $this->channeltype_system_id)) {
                    unset($post['title']);
                } else {
                    if (empty($post['title'])) {
                        $this->error('模型名称不能为空！');
                    }

                    $post['nid'] = trim($post['nid']);
                    $post['nid']    = strtolower($post['nid']);
                    if (empty($post['nid'])) {
                        $this->error('模型标识不能为空！');
                    } else {
                        if (!preg_match('/^([a-z]+)([a-z0-9]*)$/i', $post['nid'])) {
                            $this->error('模型标识必须以小写字母开头！');
                        } else if (preg_match('/^ey([a-z0-9]*)$/i', $post['nid'])) {
                            $this->error('模型标识禁止以ey开头！');
                        } else if (in_array($post['nid'], $this->channeltype_system_nid)) {
                            $this->error('系统禁用当前模型标识，请更改！');
                        }
                    }

                    $map = array(
                        'id'    => ['NEQ', $post['id']],
                        'nid' => $post['nid'],
                    );
                    if($this->channeltype_db->where($map)->count('id') > 0){
                        $this->error('该模型标识已存在，请检查', url('Channeltype/index'));
                    }
                }

                if (!empty($post['data']['is_article_pay']) && empty($system_is_article_pay) && $this->php_servicemeal < 2) {
                    $msg = array_join_string(array('5paH56u','g5LuY','6LS55Yqf','6IO95Y+q6','ZmQ5LqO5Li','T5Lia5o6I','5p2D5Z+f5','ZCN77yB'));
                    $this->error($msg, url('Channeltype/index'));
                }

                $ori_data = $this->channeltype_db->where(['id'=>$post['id']])->value('data');
                if (!empty($ori_data)){
                    $ori_data = json_decode($ori_data,'true');
                    if (!empty($post['data'])){
                        $post['data'] = array_merge($ori_data,$post['data']);
                    }else{
                        $post['data'] = $ori_data;
                    }
                }
                $nowData = array(
                    'data' => json_encode($post['data']),
                    'update_time' => getTime(),
                );
                unset($post['nid']);
                $data = array_merge($post, $nowData);
                $r = $this->channeltype_db
                    ->where(['id'=>$post['id']])
                    ->cache(true,null,"channeltype")
                    ->update($data);
                if ($r !== false) {
                    // 自动下载付费选择支付模板
                    $ajaxLogic = new \app\admin\logic\AjaxLogic;
                    $ajaxLogic->copy_tplpayfile($post['id']);
                    //下载模型开始投稿默认后自动勾选全部栏目
                    if ($post['nid'] == 'download'){
                        Db::name('arctype')->where([
                            'channeltype'    => $post['id'],
                        ])->update([
                            'is_release'   => !empty($post['is_release']) ? $post['is_release'] : 0,
                            'update_time'   => getTime(),
                        ]);
                        if (!empty($post['data']['is_download_pay'])){
                            //开启下载付费兼容旧数据   旧数据开始了会员限制的restric_type改为2
                            Db::name('archives')->where(['channel'=>$post['id'],'restric_type'=>0])->where('arc_level_id','>',0)->update(['restric_type'=>2,'update_time'=>getTime()]);
                        }
                    }
                    /*留言模型 - 同步邮箱模板的开启与关闭*/
                    if (8 == $post['id']) {
                        // Db::name('smtp_tpl')->where([
                        //         'send_scene'    => 1,
                        //     ])->update([
                        //         'is_open'   => intval($post['smtp_is_open']),
                        //         'update_time'   => getTime(),
                        //     ]);
                        
                        /*留言间隔时间 - 多语言*/
                        $paramData = [
                            'channel_guestbook_interval'    => intval($post['channel_guestbook_interval']),
                            'channel_guestbook_gourl'       => trim($post['channel_guestbook_gourl']),
                            'channel_guestbook_time'    => intval($post['channel_guestbook_time']),
                        ];
                        if (is_language()) {
                            $langRow = \think\Db::name('language')->order('id asc')
                                ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                                ->select();
                            foreach ($langRow as $key => $val) {
                                tpSetting('channel_guestbook', $paramData, $val['mark']);
                            }
                        } else {
                            tpSetting('channel_guestbook',$paramData);
                        }
                        /*--end*/
                    }
                    /*end*/
                    extra_cache('admin_channeltype_list_logic', NULL);
                    empty($data['title']) && $data['title'] = "";
                    adminLog('编辑模型：'.$data['title']);
                    $this->success("操作成功", url('Channeltype/index'));
                }
            }
            $this->error("操作失败");
        }

        $info = $this->channeltype_db->field('a.*')
            ->alias('a')
            ->where(array('a.id'=>$id))
            ->find();
        if (empty($info)) {
            $this->error('数据不存在，请联系管理员！');
            exit;
        }
        $info['data'] = json_decode($info['data'], true);
        $assign_data['field'] = $info;

        /*会员投稿设置*/
        $IsOpenRelease = Db::name('users_menu')->where([
            'mca'  => 'user/UsersRelease/release_centre',
            'lang' => $this->admin_lang,
        ])->getField('status');
        $this->assign('IsOpenRelease',$IsOpenRelease);
        /* END */

        /*留言模型*/
        $smtpTplRow = [];
        if (8 == $id) {
            /*邮箱提醒*/
            $smtpTplRow = Db::name('smtp_tpl')->field('is_open')
                ->where([
                    'send_scene'    => 1,
                    'lang'          => $this->main_lang,
                ])->find();

            /*间隔时间*/
            $channel_guestbook_interval = tpSetting('channel_guestbook.channel_guestbook_interval');
            $assign_data['channel_guestbook_interval'] = is_numeric($channel_guestbook_interval) ? intval($channel_guestbook_interval) : 60;
            /*跳转URL*/
            $assign_data['channel_guestbook_gourl'] = tpSetting('channel_guestbook.channel_guestbook_gourl');
            /*跳转时间*/
            $assign_data['channel_guestbook_time'] = tpSetting('channel_guestbook.channel_guestbook_time');
        }
        $assign_data['smtpTplRow'] = $smtpTplRow;
        /*end*/

        /*下载/视频模型*/
        $weappRow = [];
        $weappList = Db::name('weapp')->field('code, status')->where([
                'code'  => ['IN', ['Qiniuyun','AliyunOss','Cos','AwsOss']]
            ])->getAllWithIndex('code');
        if (!empty($weappList['AliyunOss']['status'])) $weappRow['AliyunOss'] = 1;
        if (!empty($weappList['Qiniuyun']['status'])) $weappRow['Qiniuyun'] = 1;
        if (!empty($weappList['Cos']['status'])) $weappRow['Cos'] = 1;
        if (!empty($weappList['AwsOss']['status'])) $weappRow['AwsOss'] = 1;
        $assign_data['weappRow'] = $weappRow;
        /*下载/视频模型*/

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

                $result = $this->channeltype_db->field('id,title,nid')->where("id",'IN',$id_arr)->select();
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
                            /*删除快速入口的相关数据*/
                            Db::name('quickentry')->where([
                                    'groups'    => 1,
                                    'controller'    => 'Custom',
                                    'action'    => 'index',
                                    'vars'  => 'channel='.$value['id'],
                                ])->delete();
                            /*end*/

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
            $dst = str_replace('template/pc/', 'template/'.TPL_THEME.'pc/', $dst);
            /*记录相关文件*/
            if (!stristr($dst, 'custom_model_path') && !stristr($dst, 'template/')) {
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
                } else {
                    // 判断是否存在手机端目录，同时生成一份
                    $tplplan = "template/".TPL_THEME."mobile";
                    $planPath = realpath($tplplan);
                    if (file_exists($planPath)) {
                        $dst_m = str_replace('template/'.TPL_THEME.'pc/', 'template/'.TPL_THEME.'mobile/', $dst);
                        @file_put_contents($dst_m, $fileContent);
                    }
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
            $this->error("创建失败，数据库已存在{$table}表，请自行排查");
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
                // if(5 == $id){action('Media/check_use');}
                $row = Db::name('channeltype')->where([
                        'id'    => $id,
                    ])->find();

                if (51 == $id) $this->ajax_ask_show($row, $status); // 问答模型

                $nofileArr = [];
                /*检测模板是否存在*/
                $tplplan = 'template/'.TPL_THEME.'pc';
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
                    if($r !== false){
                        if ($row['nid'] == 'download') {
                            /*同时开启与禁止会员中心的【我的下载】*/
                            Db::name('users_menu')->where([
                                'mca' => 'user/Download/index',
                                'lang' => get_main_lang(),
                            ])->update([
                                'status' => intval($status),
                                'update_time' => getTime(),
                            ]);
                            /*end*/

                            //同步会员中心手机端底部菜单开关  ---start
                            Db::name('users_bottom_menu')->where([
                                'mca' => 'user/Download/index',
                            ])->update([
                                'status' => intval($status),
                                'update_time' => getTime(),
                            ]);
                            //同步会员中心手机端底部菜单开关  ---end
                        }

                        delFile(CACHE_PATH, true);
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
                    delFile(CACHE_PATH, true);
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
                // if (51 == $id) return $this->ajax_ask_check_tpl(); // 问答模型
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
                            $tplplan = "template/".TPL_THEME."{$theme}";
                            $planPath = realpath($tplplan);
                            if (file_exists($planPath)) {
                                foreach ($tpltype as $k2 => $val) {
                                    $source = realpath("data/model/template/".TPL_THEME."{$theme}/{$val}_custommodel.{$view_suffix}");
                                    $dest = ROOT_PATH."template/".TPL_THEME."{$theme}/{$val}_{$row['nid']}.{$view_suffix}";
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
                                                $tpl_theme = str_replace('/', '>', TPL_THEME);
                                                $replace = <<<EOF
<article class="content">
                    <h1>{\$eyou.field.title}</h1>
                    <div class="post">
                        <div class="md_block">
                            <div style=" color: #ff0000">
                                制作易优留言表单，主要有三个步骤：<br>1，后台>开启留言模型，建立栏目并选择留言模型。<br>2，打开根目录>template>{$tpl_theme}pc>lists_guestbook.htm模板文件，按照易优表单标签制作，<a href="http://www.eyoucms.com/doc/label/arc/502.html" target="_blank">点击这里查看教程</a><br>3，还有疑问可以加易优交流群（群号：<a target="_blank" href="//shang.qq.com/wpa/qunwpa?idkey=917f9a4cfe50fd94600c55eb75d9c6014a1842089b0479bc616fb79a1d85ae0b">704301718</a>）
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
                                            if (1 == $row['ifsystem'] && 'special' != $row['nid'])
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
                                            } else if ('media' == $row['nid']) {
                                                $replace = <<<EOF
<div class="md_block">
                            <p>
                                {eyou:videoplay aid='\$eyou.field.aid' autoplay='on' id='video'}
                                    <video src="{\$video.file_url}" {\$video.id} width="600" height="400"></video>
                                    {\$video.hidden}
                                {/eyou:videoplay}

                                <br/>

                                {eyou:videolist aid='\$eyou.field.aid' id='video'}
                                    <a href="javascript:void(0);" {\$video.onclick}>{\$video.file_title} - {\$video.file_time}</a><br/>
                                    {\$video.hidden}
                                {/eyou:videolist}

                                <hr/>

                                课件：<a href="{\$eyou.field.courseware}" target="_blank">{\$eyou.field.courseware}</a><br/>
                                
                                <hr/>

                                {eyou:memberinfos mid='\$eyou.field.users_id' id="users"}
                                    会员头像：<img src="{\$users.head_pic|get_head_pic=###}" width='50' height='50' /><br/>
                                    会员昵称：{\$users.nickname}<br/>
                                {/eyou:memberinfos}

                                <br/>
                            </p>
                        </div>
EOF;
                                                $content = str_replace('<!-- #media# -->', $replace, $content);
                                            } else if ('special' == $row['nid']) {
                                                $replace = <<<EOF
{eyou:specnodelist id="list"}
                        <section class="article-list special-article">
                            <div class="special-title">{\$list.title}</div>
                            {eyou:specnode code="\$list.code" id="field"}
                            <article>
                                {eyou:notempty name="\$field.is_litpic"}
                                <a href="{\$field.arcurl}" target="_blank" title="{\$field.title}" style="float: left; margin-right: 10px"> <img src="{\$field.litpic}" alt="{\$field.title}"/> </a>
                                {/eyou:notempty} 
                                <h2><a href="{\$field.arcurl}" target="_blank">{\$field.title}</a></h2>
                                <div class="excerpt">
                                    <p>{\$field.seo_description}</p>
                                </div>
                                <div class="meta">
                                    <span class="item"><time>{\$field.add_time|MyDate='Y-m-d',###}</time></span>
                                    <span class="item"><a href="{\$field.typeurl}" target="_blank">{\$field.typename}</a></span>
                                    <span>{\$field.click}°C</span>
                                </div>
                            </article>
                            {/eyou:specnode}
                        </section>
                        {/eyou:specnodelist}
                        
EOF;
                                                $content = str_replace('<!-- #special# -->', $replace, $content);
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

    /**
     * 七牛云开关检测
     */
    public function ajax_qiniuyun_open()
    {
        if (IS_AJAX) {
            $weappInfo     = Db::name('weapp')->where('code','Qiniuyun')->field('id,status,data')->find();
            if (empty($weappInfo)) {
                $this->error('请先安装配置【七牛云图片加速】插件!', null, ['code'=>-1]);
            } else if (1 != $weappInfo['status']) {
                $this->error('请先启用【七牛云图片加速】插件!', null, ['code'=>-2,'id'=>$weappInfo['id']]);
            } else {
                $Qiniuyun = json_decode($weappInfo['data'], true);
                if (empty($Qiniuyun)) {
                    $this->error('请先配置【七牛云图片加速】插件!', null, ['code'=>-3]);
                }
            }
            $this->success('检测通过!');
        }
    }

    /**
     * oss开关检测
     */
    public function ajax_oss_open()
    {
        if (IS_AJAX) {
            $weappInfo     = Db::name('weapp')->where('code','AliyunOss')->field('id,status,data')->find();
            if (empty($weappInfo)) {
                $this->error('请先安装配置【阿里云OSS对象存储】插件!', null, ['code'=>-1]);
            } else if (1 != $weappInfo['status']) {
                $this->error('请先启用【阿里云OSS对象存储】插件!', null, ['code'=>-2,'id'=>$weappInfo['id']]);
            } else {
                $Qiniuyun = json_decode($weappInfo['data'], true);
                if (empty($Qiniuyun)) {
                    $this->error('请先配置【阿里云OSS对象存储】插件!', null, ['code'=>-3]);
                }
            }
            $this->success('检测通过!');
        }
    }

    /**
     * cos开关检测
     */
    public function ajax_cos_open()
    {
        if (IS_AJAX) {
            $weappInfo = Db::name('weapp')->where('code', 'Cos')->field('id, status, data')->find();
            if (empty($weappInfo)) {
                $this->error('请先安装配置【腾讯云OSS对象存储】插件!', null, ['code'=>-1]);
            } else if (1 != $weappInfo['status']) {
                $this->error('请先启用【腾讯云OSS对象存储】插件!', null, ['code'=>-2,'id'=>$weappInfo['id']]);
            } else {
                $Cos = json_decode($weappInfo['data'], true);
                if (empty($Cos)) {
                    $this->error('请先配置【腾讯云OSS对象存储】插件!', null, ['code'=>-3]);
                }
            }
            $this->success('检测通过!');
        }
    }

    /**
     * 亚马逊S3开关检测
     */
    public function ajax_aws_open()
    {
        if (IS_AJAX) {
            $weappInfo = Db::name('weapp')->where('code', 'AwsOss')->field('id, status, data')->find();
            if (empty($weappInfo)) {
                $this->error('请先安装配置【亚马逊S3对象存储】插件!', null, ['code'=>-1]);
            } else if (1 != $weappInfo['status']) {
                $this->error('请先安装配置【亚马逊S3对象存储】插件!', null, ['code'=>-2,'id'=>$weappInfo['id']]);
            } else {
                $Aws = json_decode($weappInfo['data'], true);
                if (empty($Aws)) {
                    $this->error('请先安装配置【亚马逊S3对象存储】插件!', null, ['code'=>-3]);
                }
            }
            $this->success('检测通过!');
        }
    }
    /*---------------------------------问答模板 start-------------------------*/

    /**
     * 检测问答模板并启用与禁用
     */
    public function ajax_ask_show($row, $status)
    {
        if (is_dir('./weapp/Ask/') && 1 == $status) {
            $this->error('检测到已安装【问答插件】，请先卸载！', null, ['confirm'=>0]);
        }

        $nofileArr = [];
        /*检测ask文件目录是否存在*/
        $tplplan = 'template/'.TPL_THEME.'pc/ask';
        $planPath = realpath($tplplan);
        if (!file_exists($planPath)) {
            $nofileArr[] = [
                'title' => '缺少文件目录：',
                'file'  => str_replace('\\', '/', $tplplan),
            ];
        }
        /*--end*/

        if (empty($status) || (1 == $status && empty($nofileArr))) {
            $r = Db::name('channeltype')->where([
                    'id'    => $row['id'],
                ])
                ->cache(true,null,"channeltype")
                ->update([
                    'status'    => $status,
                    'update_time'   => getTime(),
                ]);
            if($r !== false){
                delFile(CACHE_PATH, true);
                adminLog('编辑【'.$row['title'].'】的状态为：'.(!empty($status)?'启用':'禁用'));
                $this->success('操作成功', null, ['confirm'=>0]);
            }else{
                $this->error('操作失败', null, ['confirm'=>0]);
            }
        } else {
            $tpltype = [];
            $msg = "该模型缺少模板，系统将自动下载问答模板文件：<br/>";
            foreach ($nofileArr as $key => $val) {
                $msg .= '<font color="red">'.$val['title'].$val['file']."</font><br/>";
                $tpltype[] = $val['type'];
            }
            delFile(CACHE_PATH, true);
            $this->success($msg, null, ['confirm'=>1,'tpltype'=>base64_encode(json_encode($tpltype))]);
        }

    }

    /**
     * 启用并创建问答目录/文件
     */
    /*public function ajax_ask_check_tpl()
    {
        $themeStyleArr = ['pc','mobile'];
        foreach ($themeStyleArr as $k1 => $theme) {
            $path = DATA_PATH . '/template/ask/'.$theme;
            $path = realpath($path);
            $copy_to_path = ROOT_PATH . 'template/' . TPL_THEME . $theme;
            $copy_to_path = realpath($copy_to_path);
            if (file_exists($path) && file_exists($copy_to_path)) {
                // 递归复制文件夹
                $copy_bool = recurse_copy($path, $copy_to_path);
                if (true !== $copy_bool) {
                    $this->error($copy_bool);
                }
            }
        }
        $row = Db::name('channeltype')->find(51);
        $this->ajax_ask_show($row, 1);
        $this->success('操作成功');
    }*/

    // 检测并第一次从官方同步问答中心的前台模板
    public function ajax_syn_theme_ask()
    {
        $id = input('id/d');
        $status = input('status/d');

        $row = Db::name('channeltype')->find(51);
        $r = Db::name('channeltype')->where([
                'id'    => $row['id'],
            ])
            ->cache(true,null,"channeltype")
            ->update([
                'status'    => $status,
                'update_time'   => getTime(),
            ]);
        if($r !== false){
            delFile(CACHE_PATH, true);
            adminLog('编辑【'.$row['title'].'】的状态为：'.(!empty($status)?'启用':'禁用'));

            /*下载问答模板*/
            $icon = 2;
            $msg = '下载问答中心模板包异常，请排查问题！';
            $askLogic = new \app\admin\logic\AskLogic;
            $data = $askLogic->syn_theme_ask();
            if (true !== $data) {
                if (1 <= intval($data['code'])) {
                    $this->success('操作成功', null, ['confirm'=>0]);
                } else {
                    if (is_array($data)) {
                        $msg = $data['msg'];
                        $icon = !empty($data['icon']) ? $data['icon'] : $icon;
                    }

                    if (4 == $icon) {
                        Db::name('channeltype')->where([
                                'id'    => $row['id'],
                            ])
                            ->cache(true,null,"channeltype")
                            ->update([
                                'status'    => 0,
                                'update_time'   => getTime(),
                            ]);
                    }
                }
            }
            $this->error($msg, null, ['confirm'=>0, 'icon'=>$icon]);
            /*end*/
        }else{
            $this->error('操作失败', null, ['confirm'=>0]);
        }
    }

    /*---------------------------------问答模板 end-------------------------*/
}