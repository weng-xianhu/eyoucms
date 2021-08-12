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

use think\Db;
use think\Page;
use app\common\logic\ArctypeLogic;

class Archives extends Base
{
    // 允许发布文档的模型ID
    public $allowReleaseChannel = array();
    
    public function _initialize() {
        parent::_initialize();
        $this->allowReleaseChannel = config('global.allow_release_channel');
    }

    /**
     * 内容管理
     */
    public function index()
    {
        $arctype_list = array();
        // 目录列表
        $arctypeLogic = new ArctypeLogic(); 
        $where['is_del'] = '0'; // 回收站功能
        $where['current_channel'] = ['neq',51]; // 问答模型
        $where['weapp_code'] = '';
        $arctype_list = $arctypeLogic->arctype_list(0, 0, false, 0, $where, false);
        $zNodes = "[";
        foreach ($arctype_list as $key => $val) {
            if ($val['current_channel'] == 5 && 1.5 > $this->php_servicemeal) {
                continue;
            }
            $current_channel = $val['current_channel'];
            if (!empty($val['weapp_code'])) {
                // 插件栏目
                $typeurl = weapp_url($val['weapp_code'].'/'.$val['weapp_code'].'/index');
            } else {
                if (6 == $current_channel) {
                    $gourl = url('Arctype/single_edit', array('typeid'=>$val['id']));
                    $typeurl = url("Arctype/single_edit", array('typeid'=>$val['id'],'gourl'=>$gourl));
                } else if (8 == $current_channel) {
                    $typeurl = url("Guestbook/index", array('typeid'=>$val['id'], 'archives'=>1));
                } else {
                    $typeurl = url('Archives/index_archives', array('typeid'=>$val['id']));
                }
            }
            $typename = addslashes($val['typename']);
            $zNodes .= "{id:{$val['id']}, pId:{$val['parent_id']}, name:'{$typename}', url:'{$typeurl}',target:'content_body'";
            /*默认展开一级栏目*/
            if (empty($val['parent_id'])) {
                $zNodes .= ",open:true";
            }
            /*--end*/
            /*栏目有下级栏目时，显示图标*/
            if (1 == $val['has_children']) {
                $zNodes .= ",isParent:true";
            } else {
                $zNodes .= ",isParent:false";
            }
            /*--end*/
            $zNodes .= "},";
        }
        $zNodes .= "]";
        $this->assign('zNodes', $zNodes);

        return $this->fetch();
    }

    /**
     * 内容管理 - 所有文档列表风格（只针对ey_archives表，排除单页记录）
     */
    public function index_archives()
    {
        $assign_data = array();
        $condition = array();
        // 获取到所有URL参数
        $param = input('param.');
        $flag = input('flag/s');
        $typeid = input('typeid/d', 0);

        //跳转到指定栏目的文档列表
        if (0 < intval($typeid)) {
            $row = Db::name('arctype')
                ->alias('a')
                ->field('b.ctl_name,b.id')
                ->join('__CHANNELTYPE__ b', 'a.current_channel = b.id', 'LEFT')
                ->where('a.id', 'eq', $typeid)
                ->find();
            $ctl_name = $row['ctl_name'];
            $current_channel = $row['id'];
            if (6 == $current_channel) {
                $gourl = url('Arctype/single_edit', array('typeid'=>$typeid));
                $gourl = url("Arctype/single_edit", array('typeid'=>$typeid,'gourl'=>$gourl));
                $this->redirect($gourl);
            } else if (8 == $current_channel) {
                $gourl = url("Guestbook/index", array('typeid'=>$typeid));
                $this->redirect($gourl);
            }
        }

        foreach (['keywords','typeid','flag','is_release'] as $key) {
            if (isset($param[$key]) && $param[$key] !== '') {
                if ($key == 'keywords') {
                    $condition['a.title'] = array('LIKE', "%{$param[$key]}%");
                } else if ($key == 'typeid') {
                    $typeid = $param[$key];
                    $hasRow = model('Arctype')->getHasChildren($typeid);
                    $typeids = get_arr_column($hasRow, 'id');
                    //权限控制 by 小虎哥
                    $admin_info = session('admin_info');
                    if (0 < intval($admin_info['role_id'])) {
                        $auth_role_info = $admin_info['auth_role_info'];
                        if(! empty($auth_role_info)){
                            if(isset($auth_role_info['only_oneself']) && 1 == $auth_role_info['only_oneself']){
                                $condition['a.admin_id'] = $admin_info['admin_id'];
                            }
                            if(! empty($auth_role_info['permission']['arctype'])){
                                if (!empty($typeid)) {
                                    $typeids = array_intersect($typeids, $auth_role_info['permission']['arctype']);
                                }
                            }
                        }
                    }
                    $condition['a.typeid'] = array('IN', $typeids);
                } else if ($key == 'flag') {
                    if ('is_release' == $param[$key]) {
                        $condition['a.users_id'] = array('gt', 0);
                    } else {
                        $FlagNew = $param[$key];
                        $condition['a.'.$param[$key]] = array('eq', 1);
                    }
                // } else if ($key == 'is_release') {
                //     if (0 < intval($param[$key])) {
                //         $condition['a.users_id'] = array('gt', intval($param[$key]));
                //     }
                } else {
                    $condition['a.'.$key] = array('eq', $param[$key]);
                }
            }
        }
        
        //权限控制 by 小虎哥
        if (empty($typeid)) {
            $typeids = [];
            $admin_info = session('admin_info');
            if (0 < intval($admin_info['role_id'])) {
                $auth_role_info = $admin_info['auth_role_info'];
                if(! empty($auth_role_info)){
                    if(isset($auth_role_info['only_oneself']) && 1 == $auth_role_info['only_oneself']){
                        $condition['a.admin_id'] = $admin_info['admin_id'];
                    }
                    if(! empty($auth_role_info['permission']['arctype'])){
                        $typeids = $auth_role_info['permission']['arctype'];
                    }
                }
            }
            if (!empty($typeids)) {
                $condition['a.typeid'] = array('IN', $typeids); 
            }
        }

        $channelIds = [];
        if (empty($typeid)) {
            $id_tmp = [6,8];
            // 只显示允许发布文档的模型，且是开启状态
            $channelIds = Db::name('channeltype')->where('status',0)
                ->whereOr('id','IN',$id_tmp)->column('id');
            $condition['a.channel'] = array('NOT IN', $channelIds);
        } else {
            // 只显示当前栏目对应模型下的文档
            $current_channel = Db::name('arctype')->where('id', $typeid)->getField('current_channel');
            $condition['a.channel'] = array('eq', $current_channel);
        }

        $condition['a.lang'] = array('eq', $this->admin_lang);
        $condition['a.is_del'] = array('eq', 0);
        $continueNew = "(a.users_id = 0 OR (a.users_id > 0 AND a.arcrank >= 0))";

        $orderby = input('param.orderby/s');
        $orderway = input('param.orderway/s');
        if (!empty($orderby)) {
            $orderby = "a.{$orderby} {$orderway}, a.aid desc";
        } else {
            $orderby = "a.aid desc";
        }

        // 数据查询，搜索出主键ID的值
        $SqlQuery = Db::name('archives')->alias('a')->where($condition)->where($continueNew)->fetchSql()->count('aid');
        $count = Db::name('sql_cache_table')->where(['sql_md5'=>md5($SqlQuery)])->getField('sql_result');
        if (!isset($count)) {
            $count = Db::name('archives')->alias('a')->where($condition)->where($continueNew)->count('aid');
            /*添加查询执行语句到mysql缓存表*/
            $SqlCacheTable = [
                'sql_name' => '|archives|!=' . implode(',', $channelIds) . '|',
                'sql_result' => $count,
                'sql_md5' => md5($SqlQuery),
                'sql_query' => $SqlQuery,
                'add_time' => getTime(),
                'update_time' => getTime(),
            ];
            if (!empty($FlagNew)) $SqlCacheTable['sql_name'] = $SqlCacheTable['sql_name'] . $FlagNew . '|';
            if (!empty($typeid)) {
                $channeltype_list = config('global.channeltype_list');
                foreach ($channeltype_list as $key => $value) {
                    if ($value == $current_channel) {
                        $ModelMark = $key;
                        break;
                    }
                }
                $SqlCacheTable['sql_name'] = '|' . $ModelMark . '|' . $current_channel . '|' . $typeid . '|';
            }
            Db::name('sql_cache_table')->insertGetId($SqlCacheTable);
            /*END*/
        }

        $Page = new Page($count, config('paginate.list_rows'));
        $list = [];
        if (!empty($count)) {
            $limit = $count > config('paginate.list_rows') ? $Page->firstRow.','.$Page->listRows : $count;
            $list = Db::name('archives')
                ->field("a.aid, a.channel")
                ->alias('a')
                ->where($condition)
            	->where($continueNew)
                ->order($orderby)
                ->limit($limit)
                ->getAllWithIndex('aid');
            // 在数据量大的情况下，经过优化的搜索逻辑，先搜索出主键ID，再通过ID将其他信息补充完整；
            if ($list) {
                $aids = array_keys($list);
                $fields = "b.*, a.*, a.aid as aid";
                $row = Db::name('archives')
                    ->field($fields)
                    ->alias('a')
                    ->join('__ARCTYPE__ b', 'a.typeid = b.id', 'LEFT')
                    ->where('a.aid', 'in', $aids)
                    ->getAllWithIndex('aid');

                /*获取当页文档的所有模型*/
                $channelIds = get_arr_column($list, 'channel');
                $channelRow = Db::name('channeltype')->field('id, ctl_name, ifsystem')
                    ->where('id','IN',$channelIds)
                    ->getAllWithIndex('id');
                $assign_data['channelRow'] = $channelRow;

                $aids_channel2 = []; // 产品模型的文档ID
                foreach ($list as $key => $val) {
                    if (2 == $val['channel']) array_push($aids_channel2, $val['aid']);
                    $row[$val['aid']]['arcurl'] = get_arcurl($row[$val['aid']]);
                    $row[$val['aid']]['litpic'] = handle_subdir_pic($row[$val['aid']]['litpic']); // 支持子目录
                    $list[$key] = $row[$val['aid']];
                }

                // 产品参数
                $product_attr_row = [];
                if (!empty($aids_channel2)) {
                    $product_attr_row = Db::name('product_attr')->field('count(product_attr_id) as num, aid')->where(['aid'=>['IN', $aids_channel2]])->group('aid')->getAllWithIndex('aid');
                }
                $assign_data['product_attr_row'] = $product_attr_row;
            }
        }

        $show = $Page->show();
        $assign_data['page'] = $show;
        $assign_data['list'] = $list;
        $assign_data['pager'] = $Page;

        $assign_data['typeid'] = $typeid; // 栏目ID
        //当前栏目信息
        $arctype_info = array();
        if ($typeid > 0) {
            $arctype_info = Db::name('arctype')->field('typename,current_channel')->find($typeid);
        }
        $assign_data['arctype_info'] = $arctype_info;

        $assign_data['arctype_html'] = allow_release_arctype($typeid, array());//允许发布文档列表的栏目
        $assign_data['seo_pseudo'] = tpCache('seo.seo_pseudo');//前台URL模式
        $assign_data['archives_flags'] = model('ArchivesFlag')->getList();//文档属性
        $assign_data['shop_open'] = getUsersConfigData('shop.shop_open');//商城开关
        //是否存在栏目
        $assign_data['is_arctype'] = Db::name('arctype')->where([
                'is_del'    => 0,
                'lang'      => get_current_lang(),
            ])->count();
        $this->assign($assign_data);
        $recycle_switch = tpSetting('recycle.recycle_switch');//回收站开关
        $this->assign('recycle_switch', $recycle_switch);
        return $this->fetch('index_archives');
    }

    /**
     * 内容管理 - 栏目展开风格
     */
    private function index_arctype() {
        $arctype_list = array();
        // 目录列表
        $arctypeLogic = new ArctypeLogic(); 
        $arctype_list = $arctypeLogic->arctype_list(0, 0, false, 0, array(), false);
        $this->assign('arctype_list', $arctype_list);

        // 模型列表
        $channeltype_list = getChanneltypeList();
        $this->assign('channeltype_list', $channeltype_list);

        // 栏目最多级别
        $arctype_max_level = intval(config('global.arctype_max_level'));
        $this->assign('arctype_max_level', $arctype_max_level);

        // 允许发布文档的模型
        $this->assign('allow_release_channel', $this->allowReleaseChannel);

        return $this->fetch('index_arctype');
    }

    /**
     * 发布文档
     */
    public function add()
    {
        $typeid = input('param.typeid/d', 0);
        if (!empty($typeid)) {
            $row = Db::name('arctype')
                ->alias('a')
                ->field('b.ctl_name,b.id,b.ifsystem')
                ->join('__CHANNELTYPE__ b', 'a.current_channel = b.id', 'LEFT')
                ->where('a.id', 'eq', $typeid)
                ->find();
            $data = [
                'typeid'    => $typeid,
            ];
            if (empty($row['ifsystem'])) {
                $ctl_name = 'Custom';
                $data['channel'] = $row['id'];
            } else {
                $ctl_name = $row['ctl_name'];
            }
            $gourl = url('Archives/index_archives', array('typeid'=>$typeid));
            $data['gourl'] = $gourl;
            $jumpUrl = url("{$ctl_name}/add", $data);
        } else {
            $jumpUrl = url("Archives/release");
        }
        $this->redirect($jumpUrl);
    }

    /**
     * 编辑文档
     */
    public function edit()
    {
        $id = input('param.id/d', 0);
        $typeid = input('param.typeid/d', 0);
        $row = Db::name('archives')
            ->alias('a')
            ->field('a.channel,b.ctl_name,b.id,b.ifsystem')
            ->join('__CHANNELTYPE__ b', 'a.channel = b.id', 'LEFT')
            ->where('a.aid', 'eq', $id)
            ->find();
        if (empty($row['channel'])) {
            $channelRow = Db::name('channeltype')->field('id as channel, ctl_name')
                ->where('nid','article')
                ->find();
            $row = array_merge($row, $channelRow);
        }
        $data = [
            'id'    => $id,
        ];
        if (empty($row['ifsystem'])) {
            $ctl_name = 'Custom';
            $data['channel'] = $row['id'];
        } else {
            $ctl_name = $row['ctl_name'];
        }
        $arcurl = input('param.arcurl/s');
        $data['arcurl'] = $arcurl;
        $jumpUrl = url("{$ctl_name}/edit", $data);
        $this->redirect($jumpUrl);
    }

    /**
     * 删除文档
     */
    public function del()
    {
        if (IS_POST) {
            $del_id = input('del_id/a');
            $thorough = input('thorough/d', 0);
            $archivesLogic = new \app\admin\logic\ArchivesLogic;
            $archivesLogic->del($del_id, $thorough);
        }
    }
    
    /**
     *  审核文档
     */
    public function check()
    {
        if (IS_POST) {
            $aids = input('ids/a');
            $aids = !empty($aids) ? eyIntval($aids) : '';
            if (!empty($aids)){
                $info = [
                    'arcrank' => 0,
                    'update_time'=>getTime(),
                ];
                $r = Db::name('archives')->where('aid','IN',$aids)->cache(true,null,'archives')->save($info);
                if ($r !== false) {
                    adminLog('审核文档-id：'.implode(',', $aids));
                    /*清空sql_cache_table数据缓存表 并 添加查询执行语句到mysql缓存表*/
                    Db::name('sql_cache_table')->query('TRUNCATE TABLE '.config('database.prefix').'sql_cache_table');
                    model('SqlCacheTable')->InsertSqlCacheTable(true);
                    /* END */
                    $this->success('操作成功！');
                } else {
                    $this->error('操作失败！');
                }
            }
        }
    }

    /**
     *  取消审核文档
     */
    public function uncheck()
    {
        if (IS_POST) {
            $aids = input('ids/a');
            $aids = !empty($aids) ? eyIntval($aids) : '';
            if (!empty($aids)){
                $info = [
                    'arcrank' => -1,
                    'update_time'=>getTime(),
                ];
                $r = Db::name('archives')->where('aid','IN',$aids)->cache(true,null,'archives')->save($info);
                if ($r !== false) {
                    adminLog('取消审核-id：'.implode(',', $aids));
                    /*清空sql_cache_table数据缓存表 并 添加查询执行语句到mysql缓存表*/
                    Db::name('sql_cache_table')->query('TRUNCATE TABLE '.config('database.prefix').'sql_cache_table');
                    model('SqlCacheTable')->InsertSqlCacheTable(true);
                    /* END */
                    $this->success('操作成功！');
                } else {
                    $this->error('操作失败！');
                }
            }
        }
    }
    
    /**
     * 移动
     */
    public function move()
    {
        if (IS_POST) {
            $post = input('post.');
            $typeid = !empty($post['typeid']) ? eyIntval($post['typeid']) : '';
            $aids = !empty($post['aids']) ? eyIntval($post['aids']) : '';

            if (empty($typeid) || empty($aids)) {
                $this->error('参数有误，请联系技术支持');
            }

            // 获取移动栏目的模型ID
            $current_channel = Db::name('arctype')->where([
                    'id'    => $typeid,
                    'lang'  => $this->admin_lang,
                ])->getField('current_channel');
            // 抽取相符合模型ID的文档aid
            $aids = Db::name('archives')->where([
                    'aid'   =>  ['IN', $aids],
                    'channel'   =>  $current_channel,
                    'lang'  => $this->admin_lang,
                ])->column('aid');
            // 移动文档处理
            $update_data = array(
                'typeid'    => $typeid,
                'update_time'   => getTime(),
            );
            $r = Db::name('archives')->where([
                    'aid' => ['IN', $aids],
                ])->update($update_data);
            if($r){
                /*清空sql_cache_table数据缓存表 并 添加查询执行语句到mysql缓存表*/
                Db::name('sql_cache_table')->query('TRUNCATE TABLE '.config('database.prefix').'sql_cache_table');
                model('SqlCacheTable')->InsertSqlCacheTable(true);
                /* END */
                adminLog('移动文档-id：'.$aids);
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }

        $typeid = input('param.typeid/d', 0);

        /*允许发布文档列表的栏目*/
        $allowReleaseChannel = [];
        if (!empty($typeid)) {
            $channelId = Db::name('arctype')->where('id',$typeid)->getField('current_channel');
            $allowReleaseChannel[] = $channelId;
        }
        $arctype_html = allow_release_arctype($typeid, $allowReleaseChannel);
        $this->assign('arctype_html', $arctype_html);
        /*--end*/

        /*不允许发布文档的模型ID，用于JS判断*/
        // $js_allow_channel_arr = '[]';
        // if (!empty($allowReleaseChannel)) {
        //     $js_allow_channel_arr = '[';
        //     foreach ($allowReleaseChannel as $key => $val) {
        //         if ($key > 0) {
        //             $js_allow_channel_arr .= ',';
        //         }
        //         $js_allow_channel_arr .= $val;
        //     }
        //     $js_allow_channel_arr = $js_allow_channel_arr.']';
        // }
        // $this->assign('js_allow_channel_arr', $js_allow_channel_arr);
        /*--end*/

        /*表单提交URL*/
        $form_action = url('Archives/move');
        $this->assign('form_action', $form_action);
        /*--end*/

        return $this->fetch();
    }

    /**
     * 发布内容
     */
    public function release()
    {
        $typeid = input('param.typeid/d', 0);
        if (0 < $typeid) {
            $param = input('param.');
            $row = Db::name('arctype')
                ->field('b.ctl_name,b.id,b.ifsystem')
                ->alias('a')
                ->join('__CHANNELTYPE__ b', 'a.current_channel = b.id', 'LEFT')
                ->where('a.id', 'eq', $typeid)
                ->find();
            /*针对不支持发布文档的模型*/
            if (!in_array($row['id'], $this->allowReleaseChannel)) {
                $this->error('该栏目不支持发布文档！', url('Archives/release'));
                exit;
            }
            /*-----end*/

            $data = [
                'typeid'    => $typeid,
            ];
            if (empty($row['ifsystem'])) {
                $ctl_name = 'Custom';
                $data['channel'] = $row['id'];
            } else {
                $ctl_name = $row['ctl_name'];
            }
            $gourl = url('Archives/index_archives', array('typeid'=>$typeid), true, true);
            $data['gourl'] = $gourl;
            $jumpUrl = url("{$ctl_name}/add", $data, true, true);
            header('Location: '.$jumpUrl);
            exit;
        }

        $iframe = input('param.iframe/d',0);

        /*允许发布文档列表的栏目*/
        $select_html = allow_release_arctype();
        $this->assign('select_html',$select_html);
        /*--end*/

        /*不允许发布文档的模型ID，用于JS判断*/
        $js_allow_channel_arr = '[';
        foreach ($this->allowReleaseChannel as $key => $val) {
            if ($key > 0) {
                $js_allow_channel_arr .= ',';
            }
            $js_allow_channel_arr .= $val;
        }
        $js_allow_channel_arr = $js_allow_channel_arr.']';
        $this->assign('js_allow_channel_arr', $js_allow_channel_arr);
        /*--end*/

        if (!empty($iframe)) {
            $template = 'release_iframe';
            $attribute_row = Db::name('product_attribute')->field('typeid, count(attr_id) as num')
                ->where([
                    'is_del'    => 0,
                    'lang'      => $this->admin_lang,
                ])
                ->group('typeid')
                ->getAllWithIndex('typeid');
            $this->assign('attribute_row', $attribute_row);
        } else {
            $template = 'release';
        }
        $this->assign('iframe', $iframe);

        return $this->fetch($template);
    }

    public function ajax_get_arctype()
    {
        $pid = input('pid/d');
        $html = '';
        $status = 0;
        if (0 < $pid) {
            $map = array(
                'current_channel'    => array('IN', $this->allowReleaseChannel),
                'parent_id' => $pid,
            );
            $row = model('Arctype')->getAll('id,typename', $map, 'id');
            if (!empty($row)) {
                $status = 1;
                $html = '<option value="0">请选择栏目…</option>';
                foreach ($row as $key => $val) {
                    $html .= '<option value="'.$val['id'].'">'.$val['typename'].'</option>';
                }
            }
        }

        respose(array(
            'status'    => $status,
            'msg'   => $html,
        ));
    }

    /**
     * 复制
     */
    public function batch_copy()
    {
        if (IS_AJAX_POST) {
            $typeid = input('post.typeid/d');
            $aids = input('post.aids/s');
            $num = input('post.num/d');

            if (empty($typeid) || empty($aids)) {
                $this->error('复制失败！');
            } else if (empty($num)) {
                $this->error('复制数量至少一篇！');
            }

            // 获取复制栏目的模型ID
            $current_channel = Db::name('arctype')->where([
                    'id'    => $typeid,
                ])->getField('current_channel');
            // 抽取相符合模型ID的文档aid
            $aids = Db::name('archives')->where([
                    'aid'   =>  ['IN', $aids],
                    'channel'   =>  $current_channel,
                ])->column('aid');
            // 复制文档处理
            $archivesLogic = new \app\admin\logic\ArchivesLogic;
            $r = $archivesLogic->batch_copy($aids, $typeid, $current_channel, $num);
            if($r){
                adminLog('复制文档-id：'.$aids);
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }

        $typeid = input('param.typeid/d', 0);

        /*允许发布文档列表的栏目*/
        $allowReleaseChannel = [];
        if (!empty($typeid)) {
            $channelId = Db::name('arctype')->where('id',$typeid)->getField('current_channel');
            $allowReleaseChannel[] = $channelId;
        }
        $arctype_html = allow_release_arctype($typeid, $allowReleaseChannel);
        $this->assign('arctype_html', $arctype_html);
        /*--end*/

        /*表单提交URL*/
        $form_action = url('Archives/batch_copy');
        $this->assign('form_action', $form_action);
        /*--end*/

        return $this->fetch();
    }

    /**
     * 批量属性操作
     */
    public function batch_attr()
    {
        if (IS_AJAX_POST) {
            $opt = input('post.opt/s');
            $aids = input('post.aids/s');
            $attrType = input('post.attrType/s');

            if (empty($opt)) {
                $this->error('操作失败！');
            } else if (empty($attrType)) {
                $this->error('请勾选属性！');
            } else if (empty($aids)) {
                $this->error('文档ID不能为空！');
            }

            $value = ($opt == 'add') ? 1 : 0;
            $aids = str_replace('，', ',', $aids);
            $r = Db::name('archives')->where([
                    'aid'   => ['IN', explode(',', $aids)],
                    'lang'  => $this->admin_lang,
                ])->update([
                    $attrType => $value,
                    'update_time'   => getTime(),
                ]);
            if($r !== false){
                adminLog('批量处理属性-id：'.$aids);
                $this->success('操作成功');
            }else{
                $this->error('操作失败');
            }
        }

        /*文档属性*/
        $assign_data['archives_flags'] = model('ArchivesFlag')->getList();

        $this->assign($assign_data);
        return $this->fetch();
    }

    /**
     *  远程图片本地化
     *
     * @access    public
     * @return    string
     */
    public function ajax_remote_to_local()
    {
        if (IS_AJAX_POST) {
            $body = input('post.body/s', '', null);
            $body = remote_to_local($body);
            $this->success('本地化成功！', null, ['body'=>$body]);
        }
        $this->error('本地化失败！');
    }

    /**
     *  清除非站内链接
     *
     * @access    public
     * @return    string
     */
    public function ajax_replace_links()
    {
        if (IS_AJAX_POST) {
            $body = input('post.body/s', '', null);
            $body = replace_links($body);
            $this->success('清除成功！', null, ['body'=>$body]);
        }
        $this->error('清除失败！');
    }

    /**
     * 自定义字段
     */
    public function ajax_get_addonextitem()
    {
        $aid = input('param.aid/d', 0);
        $typeid = input('param.typeid/d', 0);
        $channeltype = input('param.channeltype/d', 0);

        if (!empty($typeid) && !empty($channeltype)) {
            // 存在aid则执行，查询文档数据
            $info = !empty($aid) ? model('Archives')->UnifiedGetInfo($aid, null, false) : [];
            // 查询对应的自定义字段
            $addonFieldExtList = model('Field')->getChannelFieldList($channeltype, 0, $aid, $info);
            $field_id_row = Db::name('channelfield_bind')->where([
                    'field_id'    => ['IN', get_arr_column($addonFieldExtList, 'id')],
                ])->column('field_id');
            // 匹配显示的自定义字段
            $htmltextField = []; // 富文本的字段名
            if (!empty($field_id_row)) {
                // 查询绑定的自定义字段
                $channelfieldBindRow = Db::name('channelfield_bind')->where([
                        'typeid'    => ['IN', [0, $typeid]],
                    ])->column('field_id');
                foreach ($addonFieldExtList as $key => $val) {
                    if (in_array($val['id'], $field_id_row) && !in_array($val['id'], $channelfieldBindRow)) {
                        unset($addonFieldExtList[$key]);
                        continue;
                    }
                    if ($val['dtype'] == 'htmltext') {
                        array_push($htmltextField, $val['name']);
                    }
                }
            }
            $assign_data['addonFieldExtList'] = $addonFieldExtList;

            // 加载模板
            $assign_data['params'] = input('param.');
            $assign_data['field'] = $info;
            $this->assign($assign_data);
            // 渲染模板
            
            $controller_name = input('param.controller_name/s');
            if (!empty($controller_name) && 'Custom' == $controller_name) {
                $html = $this->fetch('field/modelfield');
            } else {
                $html = $this->fetch('field/addonextitem');
            }
            $this->success('请求成功', null, ['html'=>$html, 'htmltextField'=>$htmltextField]);
        }
    }

    /**
     * 新建模板文件
     */
    public function ajax_newtpl()
    {
        if (IS_POST) {
            $post = input('post.', '', null);
            $content = input('post.content', '', null);
            $view_suffix = config('template.view_suffix');
            if (!empty($post['filename'])) {
                if (!preg_match("/^[\w\-\_]{1,}$/u", $post['filename'])) {
                    $this->error('文件名称只允许字母、数字、下划线、连接符的任意组合！');
                }
                $filename = "{$post['type']}_{$post['nid']}_{$post['filename']}.{$view_suffix}";
            } else {
                $filename = "{$post['type']}_{$post['nid']}.{$view_suffix}";
            }

            $content = !empty($content) ? $content : '';
            $tpldirpath = !empty($post['tpldir']) ? '/template/'.TPL_THEME.trim($post['tpldir']) : '/template/'.TPL_THEME.'pc';
            if (file_exists(ROOT_PATH.ltrim($tpldirpath, '/').'/'.$filename)) {
                $this->error('文件名称已经存在，请重新命名！', null, ['focus'=>'filename']);
            }

            $nosubmit = input('param.nosubmit/d');
            if (1 == $nosubmit) {
                $this->success('检测通过');
            }

            $filemanagerLogic = new \app\admin\logic\FilemanagerLogic;
            $r = $filemanagerLogic->editFile($filename, $tpldirpath, $content);
            if ($r === true) {
                $this->success('操作成功', null, ['filename'=>$filename,'type'=>$post['type']]);
            } else {
                $this->error($r);
            }
        }
        $type = input('param.type/s');
        $nid = input('param.nid/s');
        $tpldirList = glob('template/'.TPL_THEME.'*');
        $tpl_theme = str_replace('/', '\\/', TPL_THEME);
        foreach ($tpldirList as $key => $val) {
            if (!preg_match('/template\/'.$tpl_theme.'(pc|mobile)$/i', $val)) {
                unset($tpldirList[$key]);
            } else {
                $tpldirList[$key] = preg_replace('/^(.*)template\/'.$tpl_theme.'(pc|mobile)$/i', '$2', $val);
            }
        }
        !empty($tpldirList) && arsort($tpldirList);
        $this->assign('tpldirList', $tpldirList);

        $content = '';
        if ('special' == $nid) {
            $fileContent = @file_get_contents('./data/model/template/pc/view_custommodel.htm');
            if (!empty($fileContent)) {
                $content = $fileContent;
                $replace = <<<EOF
<section class="article-list">
                            {eyou:specnode code="default1" id="field"}
                            <article>
                                {eyou:notempty name="\$field.is_litpic"}
                                <a href="{\$field.arcurl}" target="_blank" title="{\$field.title}" style="float: left; margin-right: 10px"> <img src="{\$field.litpic}" alt="{\$field.title}" height="100" /> </a>
                                {/eyou:notempty} 
                                <h2><a href="{\$field.arcurl}" target="_blank">{\$field.title}</a><span>{\$field.click}°C</span></h2>
                                <div class="excerpt">
                                    <p>{\$field.seo_description}</p>
                                </div>
                                <div class="meta">
                                    <span class="item"><time>{\$field.add_time|MyDate='Y-m-d',###}</time></span>
                                    <span class="item"><a href="{\$field.typeurl}" target="_blank">{\$field.typename}</a></span>
                                </div>
                            </article>
                            {/eyou:specnode}
                        </section>
EOF;
                $content = str_replace("<!-- #special# -->", $replace, $content);
            }
        }
        $this->assign('content', $content);

        $this->assign('type', $type);
        $this->assign('nid', $nid);
        $this->assign('tpl_theme', TPL_THEME);
        return $this->fetch();
    }

    /**
     * 检测自定义文件名是否存在
     */
    public function ajax_check_htmlfilename()
    {
        $htmlfilename = input('post.htmlfilename/s');
        $htmlfilename = trim($htmlfilename);
        if (!empty($htmlfilename)) {
            $aid = input('post.aid/d');
            $htmlfilename = preg_replace("/[^a-zA-Z0-9_-]+/", "-", $htmlfilename);
            $htmlfilename = strtolower($htmlfilename);
            $map = array(
                'htmlfilename' => $htmlfilename,
                'lang'  => $this->admin_lang,
            );
            if ($aid > 0) {
                $map['aid'] = array('neq', $aid);
            }
            $result = Db::name('archives')->where($map)->find();
            if (!empty($result)) {
                $this->error('自定义文件名已存在，请更改！');
            }
        }
        $this->success('自定义文件名可用！');
    }

    //投稿列表
    public function index_draft()
    {
        $assign_data = array();
        $condition = array();
        $param = input('param.');
        $typeid = input('typeid/d', 0);
        foreach (['keywords','typeid'] as $key) {
            if (isset($param[$key]) && $param[$key] !== '') {
                if ($key == 'keywords') {
                    $condition['a.title'] = array('LIKE', "%{$param[$key]}%");
                } else if ($key == 'typeid') {
                    $typeid = $param[$key];
                    $hasRow = model('Arctype')->getHasChildren($typeid);
                    $typeids = get_arr_column($hasRow, 'id');
                    //权限控制 by 小虎哥
                    $admin_info = session('admin_info');
                    if (0 < intval($admin_info['role_id'])) {
                        $auth_role_info = $admin_info['auth_role_info'];
                        if(! empty($auth_role_info)){
                            if(isset($auth_role_info['only_oneself']) && 1 == $auth_role_info['only_oneself']){
                                $condition['a.admin_id'] = $admin_info['admin_id'];
                            }
                            if(! empty($auth_role_info['permission']['arctype'])){
                                if (!empty($typeid)) {
                                    $typeids = array_intersect($typeids, $auth_role_info['permission']['arctype']);
                                }
                            }
                        }
                    }
                    $condition['a.typeid'] = array('IN', $typeids);
                } else {
                    $condition['a.'.$key] = array('eq', $param[$key]);
                }
            }
        }
        
        //权限控制 by 小虎哥
        if (empty($typeid)) {
            $typeids = [];
            $admin_info = session('admin_info');
            if (0 < intval($admin_info['role_id'])) {
                $auth_role_info = $admin_info['auth_role_info'];
                if(! empty($auth_role_info)){
                    if(isset($auth_role_info['only_oneself']) && 1 == $auth_role_info['only_oneself']){
                        $condition['a.admin_id'] = $admin_info['admin_id'];
                    }
                    if (!empty($auth_role_info['permission']['arctype'])) $typeids = $auth_role_info['permission']['arctype'];
                }
            }
            if (!empty($typeids)) $condition['a.typeid'] = array('IN', $typeids); 
        }

        if (empty($typeid)) {
            $id_tmp = [6,8];
            // 只显示允许发布文档的模型，且是开启状态
            $channelIds = Db::name('channeltype')->where('status',0)
                ->whereOr('id','IN',$id_tmp)->column('id');
            $condition['a.channel'] = array('NOT IN', $channelIds);
        } else {
            // 只显示当前栏目对应模型下的文档
            $current_channel = Db::name('arctype')->where('id',$typeid)->getField('current_channel');
            $condition['a.channel'] = array('eq', $current_channel);
        }

        $condition['a.users_id'] = array('gt', 0);
        $condition['a.arcrank'] = array('lt', 0);
        $condition['a.lang'] = array('eq', $this->admin_lang);
        $condition['a.is_del'] = array('eq', 0);

        // 自定义排序
        $orderby = input('param.orderby/s');
        $orderway = input('param.orderway/s');
        $orderby = !empty($orderby) ? "a.{$orderby} {$orderway}, a.aid desc" : "a.aid desc";

        // 查询并处理缓存表
        $SqlQuery = Db::name('archives')->alias('a')->where($condition)->fetchSql()->count('aid');
        $count = Db::name('sql_cache_table')->where(['sql_md5'=>md5($SqlQuery)])->getField('sql_result');
        if (!isset($count)) {
            $count = Db::name('archives')->alias('a')->where($condition)->count('aid');
            // 添加查询执行语句到mysql缓存表
            $SqlCacheTable = [
                'sql_name' => '|archives|draft|',
                'sql_result' => $count,
                'sql_md5' => md5($SqlQuery),
                'sql_query' => $SqlQuery,
                'add_time' => getTime(),
                'update_time' => getTime()
            ];
            if (!empty($typeid)) $SqlCacheTable['sql_name'] = $SqlCacheTable['sql_name'] . $typeid . '|';
            Db::name('sql_cache_table')->insertGetId($SqlCacheTable);
        }
        // 分页
        $Page = new Page($count, config('paginate.list_rows'));
        $list = [];
        if (!empty($count)) {
            // 数据查询，搜索出主键ID的值
            $limit = $count > config('paginate.list_rows') ? $Page->firstRow.','.$Page->listRows : $count;
            $list = Db::name('archives')
                ->field("a.aid,a.channel")
                ->alias('a')
                ->where($condition)
                ->order($orderby)
                ->limit($limit)
                ->getAllWithIndex('aid');

            // 在数据量大的情况下，经过优化的搜索逻辑，先搜索出主键ID，再通过ID将其他信息补充完整；
            if ($list) {
                $aids = array_keys($list);
                $fields = "b.*, a.*, a.aid as aid";
                $row = Db::name('archives')
                    ->field($fields)
                    ->alias('a')
                    ->join('__ARCTYPE__ b', 'a.typeid = b.id', 'LEFT')
                    ->where('a.aid', 'in', $aids)
                    ->getAllWithIndex('aid');

                // 获取当页文档的所有模型
                $channelIds = get_arr_column($list, 'channel');
                $assign_data['channelRow'] = Db::name('channeltype')->field('id, ctl_name, ifsystem')
                    ->where('id', 'IN', $channelIds)
                    ->getAllWithIndex('id');

                // 产品模型的文档ID
                $aids_channel2 = [];
                foreach ($list as $key => $val) {
                    if (2 == $val['channel']) array_push($aids_channel2, $val['aid']);
                    $row[$val['aid']]['arcurl'] = get_arcurl($row[$val['aid']]);
                    $row[$val['aid']]['litpic'] = handle_subdir_pic($row[$val['aid']]['litpic']);
                    $list[$key] = $row[$val['aid']];
                }

                // 产品参数
                $product_attr_row = [];
                if (!empty($aids_channel2)) {
                    $product_attr_row = Db::name('product_attr')->field('count(product_attr_id) as num, aid')->where(['aid'=>['IN', $aids_channel2]])->group('aid')->getAllWithIndex('aid');
                }
                $assign_data['product_attr_row'] = $product_attr_row;
            }
        }

        // 加载信息
        $assign_data['page'] = $Page->show();
        $assign_data['list'] = $list;
        $assign_data['pager'] = $Page;
        $assign_data['typeid'] = $typeid;
        $arctype_info = array(); // 当前栏目信息
        if ($typeid > 0) $arctype_info = Db::name('arctype')->field('typename,current_channel')->find($typeid);
        $assign_data['arctype_info'] = $arctype_info;
        $assign_data['arctype_html'] = allow_release_arctype($typeid, array()); // 允许发布文档列表的栏目
        $assign_data['seo_pseudo'] = tpCache('seo.seo_pseudo'); // 前台URL模式
        $assign_data['archives_flags'] = model('ArchivesFlag')->getList(); // 文档属性
        $assign_data['shop_open'] = getUsersConfigData('shop.shop_open'); // 商城开关
        $assign_data['is_arctype'] = Db::name('arctype')->where(['is_del'=>0,'lang'=>get_current_lang()])->count(); // 是否存在栏目
        $this->assign($assign_data);
        $recycle_switch = tpSetting('recycle.recycle_switch');//回收站开关
        $this->assign('recycle_switch', $recycle_switch);
        return $this->fetch('index_draft');
    }

    //文档标题重复检测
    public function check_title_repeat($title='',$aid=0)
    {
        $map['title'] = $title;
        if (!empty($aid)){
            $map['aid'] = ['NEQ', $aid];
        }
        $count = Db::name('archives')->where($map)->count('aid');
        if (!empty($count)){
            $this->error("<font color='black'>系统已存在标题为'<font color='red'>".$title."'</font>的文档! </font><a href='javascript:void(0);' onclick='layer.closeAll();'>[<font color='red'>关闭</font>]</a>");
        }
        $this->success("没有重复!");

    }
}