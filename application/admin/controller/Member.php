<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 陈风任 <491085389@qq.com>
 * Date: 2019-2-12
 */

namespace app\admin\controller;

use think\Page;
use think\Db;
use think\Config;
use app\admin\logic\MemberLogic;

class Member extends Base {

    /**
     * 构造方法
     */
    public function __construct(){
        parent::__construct();
        /*会员中心数据表*/
        $this->users_db        = Db::name('users');         // 用户信息表
        $this->users_list_db   = Db::name('users_list');    // 用户资料表
        $this->users_level_db  = Db::name('users_level');   // 用户等级表
        $this->users_config_db = Db::name('users_config');  // 用户配置表
        $this->users_money_db  = Db::name('users_money');   // 用户充值表
        $this->field_type_db   = Db::name('field_type');    // 字段属性表
        $this->users_parameter_db = Db::name('users_parameter'); // 用户属性表
        /*结束*/

        /*订单中心数据表*/
        $this->shop_address_db   = Db::name('shop_address');    // 用户地址表
        $this->shop_cart_db      = Db::name('shop_cart');       // 用户购物车表
        $this->shop_order_db     = Db::name('shop_order');      // 用户订单主表
        $this->shop_order_log_db = Db::name('shop_order_log');  // 用户订单操作记录表
        $this->shop_order_details_db = Db::name('shop_order_details');  // 用户订单副表
        /*结束*/

        // 是否开启支付功能设置
        $UsersConfigData = getUsersConfigData('all');
        $this->assign('userConfig',$UsersConfigData);
    }

    // 用户列表
    public function users_index()
    {
        $list = array();
        $keywords = input('keywords/s');

        $condition = array();
        // 应用搜索条件
        if (!empty($keywords)) {
            $condition['a.username'] = array('LIKE', "%{$keywords}%");
        }

        $condition['a.is_del'] = 0;
        // 多语言
        $condition['a.lang'] = array('eq', $this->admin_lang);

        /**
         * 数据查询
         */
        $count = $this->users_db->alias('a')->where($condition)->count();// 查询满足要求的总记录数
        $Page = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $this->users_db->field('a.*,b.level_name')
            ->alias('a')
            ->join('__USERS_LEVEL__ b', 'a.level = b.level_id', 'LEFT')
            ->where($condition) 
            ->order('a.users_id desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pager',$Page);// 赋值分页集

        /*纠正数据*/
        $web_is_authortoken = tpCache('web.web_is_authortoken');
        (is_realdomain() && !empty($web_is_authortoken)) && getUsersConfigData('shop', ['shop_open'=>0]);
        
        /*检测是否存在会员中心模板*/
        if ('v1.0.1' > getVersion('version_themeusers')) {
            $is_syn_theme_users = 1;
        } else {
            $is_syn_theme_users = 0;
        }
        $this->assign('is_syn_theme_users',$is_syn_theme_users);
        /*--end*/

        return $this->fetch();
    }

    // 检测并第一次从官方同步会员中心的前台模板
    public function ajax_syn_theme_users()
    {
        $msg = '下载会员中心模板包异常，请第一时间联系技术支持，排查问题！';
        $memberLogic = new MemberLogic;
        $data = $memberLogic->syn_theme_users();
        if (true !== $data) {
            if (1 <= intval($data['code'])) {
                $this->success('初始化成功！', url('Member/users_index'));
            } else {
                if (is_array($data)) {
                    $msg = $data['msg'];
                }
            }
        }

        /*多语言*/
        if (is_language()) {
            $langRow = \think\Db::name('language')->order('id asc')
                ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                ->select();
            foreach ($langRow as $key => $val) {
                tpCache('web', ['web_users_switch'=>0], $val['mark']);
            }
        } else { // 单语言
            tpCache('web', ['web_users_switch'=>0]);
        }
        /*--end*/

        $this->error($msg);
    }

    // 用户批量新增
    public function users_batch_add()
    {
        if (IS_POST) {
            $post = input('post.');

            $username = $post['username'];
            if (empty($username)) {
                $this->error('用户名不能为空！');
            }

            if (empty($post['password'])) {
                $this->error('登录密码不能为空！');
            }
            
            $password = func_encrypt($post['password']);

            $usernameArr = explode("\r\n", $username);
            $usernameArr = array_filter($usernameArr);//去除数组空值
            $usernameArr = array_unique($usernameArr); //去重

            $addData = [];
            $usernameList = $this->users_db->where([
                    'username'  => ['IN', $usernameArr],
                    'lang'      => $this->admin_lang,
                ])->column('username');
            foreach ($usernameArr as $key => $val) {
                if(trim($val) == '' || empty($val) || in_array($val, $usernameList) || !preg_match("/^[\x{4e00}-\x{9fa5}\w\-\_\@\#]{2,30}$/u", $val))
                {
                    continue;
                }

                $addData[] = [
                    'username'       => $val,
                    'nickname'       => $val,
                    'password'       => $password,
                    'level'          => $post['level'],
                    'register_place' => 1,
                    'reg_time'       => getTime(),
                    'lang'           => $this->admin_lang,
                    'add_time'       => getTime(),
                ];
            }
            if (!empty($addData)) {
                $r = model('Member')->saveAll($addData);
                if (!empty($r)) {
                    adminLog('批量新增用户：'.get_arr_column($addData, 'username'));
                    $this->success('操作成功！', url('Member/users_index'));
                } else {
                    $this->error('操作失败');
                }
            } else {
                $this->success('操作成功！', url('Member/users_index'));
            }
        }

        $user_level = $this->users_level_db->field('level_id,level_name')
            ->where(['lang'=>$this->admin_lang])
            ->order('level_value asc')
            ->select();
        $this->assign('user_level',$user_level);

        return $this->fetch();
    }

    // 用户新增
    // public function users_add()
    // {
    //     if (IS_POST) {
    //         $post = input('post.');

    //         $count = $this->users_db->where([
    //                 'username'  => $post['username'],
    //                 'lang'      => $this->admin_lang,
    //             ])->count();
    //         if (!empty($count)) {
    //             $this->error('用户名已存在！');
    //         }

    //         if (empty($post['password']) && empty($post['password2'])) {
    //             $this->error('登录密码不能为空！');
    //         } else {
    //             if ($post['password'] != $post['password2']) {
    //                 $this->error('两次密码输入不一致！');
    //             }
    //         }

    //         $ParaData = [];
    //         if (is_array($post['users_'])) {
    //             $ParaData = $post['users_'];
    //         }
    //         unset($post['users_']);
    //         // 处理提交的用户属性中必填项是否为空
    //         // 必须传入提交的用户属性数组
    //         $EmptyData = model('Member')->isEmpty($ParaData);
    //         if ($EmptyData) {
    //             $this->error($EmptyData);
    //         }
            
    //         // 处理提交的用户属性中邮箱和手机是否已存在
    //         // isRequired方法传入的参数有2个
    //         // 第一个必须传入提交的用户属性数组
    //         // 第二个users_id，注册时不需要传入，修改时需要传入。
    //         $RequiredData = model('Member')->isRequired($ParaData);
    //         if ($RequiredData) {
    //             $this->error($RequiredData);
    //         }

    //         $post['password'] = func_encrypt($post['password']);// MD5加密
    //         unset($post['password2']);

    //         $post['register_place'] = 1; // 注册位置，后台注册不受注册验证影响，1为后台注册，2为前台注册。
    //         $post['reg_time'] = getTime();
    //         $post['lang'] = $this->admin_lang;
    //         $users_id = $this->users_db->add($post);
    //         // 判断用户添加是否成功
    //         if (!empty($users_id)) {
    //             // 批量添加用户属性到属性信息表
    //             if (!empty($ParaData)) {
    //                 $betchData = [];
    //                 $usersparaRow = $this->users_parameter_db->where([
    //                         'lang'  => $this->admin_lang,
    //                         'is_hidden' => 0,
    //                     ])->getAllWithIndex('name');
    //                 foreach ($ParaData as $key => $value) {
    //                     $para_id = intval($usersparaRow[$key]['para_id']);
    //                     $betchData[] = [
    //                         'users_id'  => $users_id,
    //                         'para_id'   => $para_id,
    //                         'info'      => $value,
    //                         'lang'      => $this->admin_lang,
    //                         'add_time'  => getTime(),
    //                     ];
    //                 }
    //                 $this->users_list_db->insertAll($betchData);
    //             }

    //             // 查询属性表的手机号码和邮箱地址，同步修改用户信息。
    //             $UsersListData = model('Member')->getUsersListData('*',$users_id);
    //             $UsersListData['update_time'] = getTime(); 
    //             $this->users_db->where([
    //                     'users_id'  => $users_id,
    //                     'lang'      => $this->admin_lang,
    //                 ])->update($UsersListData);

    //             adminLog('新增用户：'.$post['username']);
    //             $this->success('操作成功！', url('Member/users_index'));
    //         }else{
    //             $this->error('操作失败');
    //         }
    //     }

    //     $user_level = $this->users_level_db->field('level_id,level_name')
    //         ->where(['lang'=>$this->admin_lang])
    //         ->order('level_value asc')
    //         ->select();
    //     $this->assign('user_level',$user_level);

    //     // 资料信息
    //     $users_para = model('Member')->getDataPara();
    //     $this->assign('users_para',$users_para);

    //     return $this->fetch();
    // }

    // 用户编辑
    public function users_edit()
    {
        if (IS_POST) {
            $post = input('post.');

            if (isset($post['users_money'])) {
                $post['users_money'] = input('post.users_money/f');
            }

            if (!empty($post['password'])) {
                $post['password'] = func_encrypt($post['password']); // MD5加密
            } else {
                unset($post['password']);
            }

            $users_id = $post['users_id'];
            $ParaData = [];
            if (is_array($post['users_'])) {
                $ParaData = $post['users_'];
            }
            unset($post['users_']);

            // 处理提交的用户属性中必填项是否为空
            // 必须传入提交的用户属性数组
            /*$EmptyData = model('Member')->isEmpty($ParaData);
            if ($EmptyData) {
                $this->error($EmptyData);
            }*/
            
            // 处理提交的用户属性中邮箱和手机是否已存在
            // isRequired方法传入的参数有2个
            // 第一个必须传入提交的用户属性数组
            // 第二个users_id，注册时不需要传入，修改时需要传入。
            $RequiredData = model('Member')->isRequired($ParaData,$users_id);
            if ($RequiredData) {
                $this->error($RequiredData);
            }

            $users_where = [
                'users_id' => $users_id,
                'lang'     => $this->admin_lang,
            ];
            $userinfo = $this->users_db->where($users_where)->find();

            $post['update_time'] = getTime();
            unset($post['username']);
            $r = $this->users_db->where($users_where)->update($post);

            if ($r) {
                $row2 = $this->users_parameter_db->field('para_id,name')->getAllWithIndex('name');
                foreach ($ParaData as $key => $value) {
                    $data    = [];
                    $para_id = intval($row2[$key]['para_id']);
                    $where   = [
                        'users_id' => $post['users_id'],
                        'para_id'  => $para_id,
                        'lang'     => $this->admin_lang,
                    ];
                    $data['info']        = $value;
                    $data['update_time'] = getTime();

                    // 若信息表中无数据则添加
                    $row = $this->users_list_db->where($where)->count();
                    if (empty($row)) {
                        $data['users_id'] = $post['users_id'];
                        $data['para_id']  = $para_id;
                        $data['lang']     = $this->admin_lang;
                        $data['add_time'] = getTime();
                        $this->users_list_db->add($data);
                    } else {
                        $this->users_list_db->where($where)->update($data);
                    }
                }

                // 查询属性表的手机号码和邮箱地址，同步修改用户信息。
                $UsersListData = model('Member')->getUsersListData('*',$users_id);
                $UsersListData['update_time'] = getTime(); 
                $this->users_db->where($users_where)->update($UsersListData);

                adminLog('编辑用户：'.$userinfo['username']);
                $this->success('操作成功', url('Member/users_index'));
            }else{
                $this->error('操作失败');
            }
        }

        $users_id = input('param.id/d');

        // 用户信息
        $info = $this->users_db->where([
                'users_id'  => $users_id,
                'lang'      => $this->admin_lang,
            ])->find();
        $this->assign('info',$info);

        // 等级信息
        $level = $this->users_level_db->field('level_id,level_name')
            ->where(['lang'=>$this->admin_lang])
            ->order('level_value asc')
            ->select();
        $this->assign('level',$level);

        // 属性信息
        $users_para = model('Member')->getDataParaList($users_id);
        $this->assign('users_para',$users_para);

        // 上一个页面来源
        $from = input('param.from/s');
        if ('money_index' == $from) {
            $backurl = url('Member/money_index');
        } else {
            $backurl = url('Member/users_index');
        }
        $this->assign('backurl', $backurl);

        return $this->fetch();
    }

    // 用户删除
    public function users_del()
    {
        $users_id = input('del_id/a');
        $users_id = eyIntval($users_id);
        if (IS_AJAX_POST && !empty($users_id)) {
            // 删除统一条件
            $Where = [
                'users_id'  => ['IN', $users_id],
                'lang'      => $this->admin_lang,
            ];

            $result = $this->users_db->field('username')->where($Where)->select();
            $username_list = get_arr_column($result, 'username');

            $return = $this->users_db->where($Where)->delete();
            if ($return) {
                /*删除会员中心关联数据表*/
                // 删除用户下的属性
                $this->users_list_db->where($Where)->delete();
                // 删除用户下的属性
                $this->users_money_db->where($Where)->delete();
                /*结束*/

                /*删除订单中心关联数据表*/
                // 删除用户下的购物车表
                $this->shop_cart_db->where($Where)->delete();
                // 删除用户下的收货地址表
                $this->shop_address_db->where($Where)->delete();
                // 删除用户下的订单主表
                $this->shop_order_db->where($Where)->delete();
                // 删除用户下的订单副表
                $this->shop_order_log_db->where($Where)->delete();
                // 删除用户下的订单操作记录表
                $this->shop_order_details_db->where($Where)->delete();
                /*结束*/

                adminLog('删除用户：'.implode(',', $username_list));
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }
        $this->error('参数有误');
    }

    // 级别列表
    public function level_index()
    {
        $list = array();
        $keywords = input('keywords/s');

        $condition = array();
        // 应用搜索条件
        if (!empty($keywords)) {
            $condition['a.level_name'] = array('LIKE', "%{$keywords}%");
        }
        // 多语言
        $condition['a.lang'] = array('eq', $this->admin_lang);

        /**
         * 数据查询
         */
        $count = $this->users_level_db->alias('a')->where($condition)->count();// 查询满足要求的总记录数
        $Page = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $this->users_level_db->field('a.*')
            ->alias('a')
            ->where($condition)
            ->order('a.level_value asc, a.level_id asc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pager',$Page);// 赋值分页集

        // 用于判断是否可以删除用户级别，当用户级别下存在用户时，不可删除。
        $levelgroup = $this->users_db->field('level')
            ->where(['lang'=>$this->admin_lang])
            ->group('level')
            ->getAllWithIndex('level');
        $this->assign('levelgroup',$levelgroup);

        return $this->fetch();
    }

    // 级别 - 新增
    public function level_add()
    {   
        if (IS_POST) {
            $post = input('post.');
            $post['level_name'] = trim($post['level_name']);
            $post['level_value'] = intval(trim($post['level_value']));

            $levelRow = $this->users_level_db->field('level_name,level_value')
                ->where(['lang'=>$this->admin_lang])
                ->select();
            foreach ($levelRow as $key => $val) {
                if ($val['level_name'] == $post['level_name']) {
                    $this->error('级别名称已存在！');
                } else if (intval($val['level_value']) == $post['level_value']) {
                    $this->error('用户等级值不能重复！');
                }
            }

            $newData = [
                'level_value'   => intval($post['level_value']),
                'lang'  => $this->admin_lang,
                'add_time'  => getTime(),
            ];
            $data = array_merge($post, $newData);
            $r = $this->users_level_db->add($data);
            if ($r) {
                adminLog('新增用户级别：'.$data['level_name']);
                $this->success('操作成功', url('Member/level_index'));
            } else {
                $this->error('操作失败');
            }
        }

        return $this->fetch();
    }

    // 级别 - 编辑
    public function level_edit()
    {
        if (IS_POST) {
            $post = input('post.');
            $post['level_name'] = trim($post['level_name']);
            $post['level_value'] = intval(trim($post['level_value']));

            $levelRow = $this->users_level_db->field('level_name,level_value')
                ->where([
                    'level_id'      => ['NEQ', $post['level_id']],
                    'lang'      => $this->admin_lang,
                ])
                ->select();
            foreach ($levelRow as $key => $val) {
                if ($val['level_name'] == $post['level_name']) {
                    $this->error('级别名称已存在！');
                } else if (intval($val['level_value']) == $post['level_value']) {
                    $this->error('用户等级值不能重复！');
                }
            }

            $newData = [
                'level_value'   => intval($post['level_value']),
                'update_time'  => getTime(),
            ];
            $data = array_merge($post, $newData);
            $r = $this->users_level_db->where([
                    'level_id'  => $post['level_id'],
                    'lang'      => $this->admin_lang,
                ])->update($data);
            if ($r) {
                adminLog('编辑用户级别：'.$data['level_name']);
                $this->success('操作成功', url('Member/level_index'));
            } else {
                $this->error('操作失败');
            }
        }

        $id = input('get.id/d');

        $info = $this->users_level_db->where([
                'level_id'  => $id,
                'lang'  => $this->admin_lang,
            ])->find();
        $this->assign('info',$info);

        return $this->fetch();
    }

    // 级别 - 删除
    public function level_del()
    {
        $level_id = input('del_id/a');
        $level_id = eyIntval($level_id);

        if (IS_AJAX_POST && !empty($level_id)) {
            $result = $this->users_level_db->field('level_name,is_system')
                ->where([
                    'level_id'  => ['IN', $level_id],
                    'lang'      => $this->admin_lang,
                ])
                ->select();
            $level_name_list = get_arr_column($result, 'level_name');

            foreach ($result as $val) {
                if (1 == intval($val['is_system'])) {
                    $this->error('系统内置，不可删除！');
                }
            }

            $info = $this->users_db->where([
                    'level' => ['IN', $level_id],
                    'lang'  => $this->admin_lang,
                ])->count();
            if (!empty($info)) {
                $this->error('选中的级别存在用户，不可删除！');
            }

            $return = $this->users_level_db->where([
                    'level_id'  => ['IN', $level_id],
                    'lang'      => $this->admin_lang,
                ])->delete();
            if ($return) {
                adminLog('删除用户级别：'.implode(',', $level_name_list));
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }
        $this->error('参数有误');
    }

    // 属性列表
    public function attr_index()
    {
        //属性数据
        $info = $this->users_parameter_db->field('a.*,a.title,b.title as dtypetitle')
            ->alias('a')
            ->join('__FIELD_TYPE__ b', 'a.dtype = b.name', 'LEFT')
            ->order('a.is_system desc,a.sort_order asc,a.para_id desc')
            ->where('a.lang',$this->admin_lang)
            ->select();
        foreach ($info as $key => $value) {
            if ('email' == $value['dtype']) {
                $info[$key]['dtypetitle'] = '邮箱地址';
            } else if ('mobile' == $value['dtype']) {
                $info[$key]['dtypetitle'] = '手机号码';
            }
        }
        $this->assign('info',$info);
        return $this->fetch();
    }

    // 属性添加
    public function attr_add()
    {   
        if (IS_POST) {
            $post = input('post.');
            $post['title'] = trim($post['title']);

            if (empty($post['title'])) {
                $this->error('属性标题不能为空！');
            }
            if (empty($post['dtype'])) {
                $this->error('请选择属性类型！');
            }

            $count = $this->users_parameter_db->where([
                    'title'=>$post['title']
                ])->count();
            if (!empty($count)) {
                $this->error('属性标题已存在！');
            }

            $post['dfvalue']     = str_replace('，', ',', $post['dfvalue']);
            $post['dfvalue'] = trim($post['dfvalue'], ',');
            $post['add_time'] = getTime();
            $post['lang']        = $this->admin_lang;
            $post['sort_order'] = '100';
            $para_id = $this->users_parameter_db->insertGetId($post);
            if (!empty($para_id)) {
                $name = 'para_'.$para_id;
                $return = $this->users_parameter_db->where('para_id',$para_id)
                    ->update([
                        'name'  => $name,
                        'update_time'   => getTime(),
                    ]);
                if ($return) {
                    adminLog('新增用户属性：'.$post['title']);
                    $this->success('操作成功',url('Member/attr_index'));
                }
            }
            $this->error('操作失败');
        }

        $field = $this->field_type_db->field('name,title,ifoption')
            ->where([
                'name'  => ['IN', ['text','checkbox','multitext','radio','select']]
            ])
            ->select();
        $this->assign('field',$field);
        return $this->fetch();
    }

    // 属性修改
    public function attr_edit()
    {
        $para_id = input('param.id/d');

        if (IS_POST && !empty($para_id)) {
            $post = input('post.');
            $post['title'] = trim($post['title']);

            if (empty($post['title'])) {
                $this->error('属性标题不能为空！');
            }
            if (empty($post['dtype'])) {
                $this->error('请选择属性类型！');
            }

            $count = $this->users_parameter_db->where([
                    'title'     => $post['title'],
                    'para_id'   => ['NEQ', $post['para_id']],
                ])->count();
            if ($count) {
                $this->error('属性标题已存在！');
            }

            $post['dfvalue'] = str_replace('，', ',', $post['dfvalue']);
            $post['dfvalue'] = trim($post['dfvalue'], ',');
            $post['update_time'] = getTime();
            $return = $this->users_parameter_db->where([
                    'para_id'   => $para_id,
                    'lang'      => $this->admin_lang,
                ])->update($post);
            if ($return) {
                adminLog('编辑用户属性：'.$post['title']);
                $this->success('操作成功',url('Member/attr_index'));
            }else{
                $this->error('操作失败');
            }
        }

        $info = $this->users_parameter_db->where([
                'para_id'   => $para_id,
                'lang'      => $this->admin_lang,
            ])->find();
        $this->assign('info',$info);

        $field = $this->field_type_db->field('name,title,ifoption')
            ->where([
                'name'  => ['IN', ['text','checkbox','multitext','radio','select']]
            ])
            ->select();
        $this->assign('field',$field);

        return $this->fetch();
    }

    // 属性删除
    public function attr_del()
    {
        $para_id = input('del_id/a');
        $para_id = eyIntval($para_id);

        if (IS_AJAX_POST && !empty($para_id)) {
            $result = $this->users_parameter_db->field('title')
                ->where([
                    'para_id'  => ['IN', $para_id],
                    'lang'      => $this->admin_lang,
                ])
                ->select();
            $title_list = get_arr_column($result, 'title');

            // 删除用户属性表数据
            $return = $this->users_parameter_db->where([
                    'para_id'  => ['IN', $para_id],
                    'lang'      => $this->admin_lang,
                ])->delete();

            if ($return) {
                // 删除用户属性信息表数据
                $this->users_list_db->where([
                        'para_id'  => ['IN', $para_id],
                        'lang'      => $this->admin_lang,
                    ])->delete();
                adminLog('删除用户属性：'.implode(',', $title_list));
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }
        $this->error('参数有误');
    }

    // 功能设置
    public function users_config()
    {
        if (IS_POST) {
            $post = input('post.');

            /*商城入口*/
            $shop_open = $post['shop']['shop_open'];
            $shop_open_old = getUsersConfigData('shop.shop_open');
            /*--end*/

            // 邮件验证的检测
            if (2 == $post['users']['users_verification']) {
                $users_config_email = $this->users_config_email();
                if (!empty($users_config_email)) {
                   $this->error($users_config_email);
                }
            }
            // 第三方登录
            if (1 == $post['oauth']['oauth_open']) {
                empty($post['oauth']['oauth_qq']) && $post['oauth']['oauth_qq'] = 0;
                empty($post['oauth']['oauth_weixin']) && $post['oauth']['oauth_weixin'] = 0;
                empty($post['oauth']['oauth_weibo']) && $post['oauth']['oauth_weibo'] = 0;
            }
            foreach ($post as $key => $val) {
                getUsersConfigData($key, $val);
            }
            $this->success('操作成功');
        }

        // 获取用户配置信息
        $UsersConfigData = getUsersConfigData('all');
        $this->assign('info',$UsersConfigData);

        /*检测是否存在订单中心模板*/
        if ('v1.0.1' > getVersion('version_themeshop') && !empty($UsersConfigData['shop_open'])) {
            $is_syn_theme_shop = 1;
        } else {
            $is_syn_theme_shop = 0;
        }
        $this->assign('is_syn_theme_shop',$is_syn_theme_shop);
        /*--end*/

        return $this->fetch();
    }

    // 第三方登录配置
    public function ajax_set_oauth_config()
    {
        $oauth = input('param.oauth/s', 'qq');

        return $this->fetch();
    }

    // 邮件验证的检测
    public function ajax_users_config_email()
    {   
        if (IS_AJAX) {
            // 邮件验证的检测
            $users_config_email = $this->users_config_email();
            if (!empty($users_config_email)) {
               $this->error($users_config_email);
            }
            $this->success('检验通过');
        }
        $this->error('参数有误');
    }
        
    private function users_config_email(){
        // 用户属性信息
        $where = array(
            'name'      => ['LIKE', "email_%"],
            'lang'      => $this->admin_lang,
            'is_system' => 1,
        );
        // 是否要为必填项
        $param = $this->users_parameter_db->where($where)->field('title,is_hidden')->find();
        if (empty($param) || 1 == $param['is_hidden']) {
            return "请先把用户属性的<font color='red'>{$param['title']}</font>设置为显示，且为必填项！";
        }

        $param = $this->users_parameter_db->where($where)->field('title,is_required')->find();
        if (empty($param) || 0 == $param['is_required']) {
            return "请先把用户属性的<font color='red'>{$param['title']}</font>设置为必填项！";
        }

        // 是否开启邮箱发送扩展
        $openssl_funcs = get_extension_funcs('openssl');
        if (!$openssl_funcs) {
            return "请联系空间商，开启php的 <font color='red'>openssl</font> 扩展！";
        }

        $send_email_scene = config('send_email_scene');
        $scene = $send_email_scene[2]['scene'];

        // 自动启用注册邮件模板
        Db::name('smtp_tpl')->where([
                'send_scene'    => $scene,
                'lang'          => $this->admin_lang,
            ])->update([
                'is_open'       => 1,
                'update_time'   => getTime(),
            ]);

        // 是否填写邮件配置
        $smtp_config = tpCache('smtp');
        foreach ($smtp_config as $val) {
            if (empty($val)) {
                return "请先完善<font color='red'>(邮件配置)</font>，具体步骤【基本信息】->【接口配置】->【邮件配置】";
            }
        }

        return '';
    }

    // 支付方式配置
    public function pay_set(){
        $payConfig = getUsersConfigData('pay');

        /*微信支付配置*/
        $wechat = !empty($payConfig['pay_wechat_config']) ? $payConfig['pay_wechat_config'] : [];
        $this->assign('wechat',unserialize($wechat));
        /*--end*/

        /*支付宝支付配置*/
        $alipay = !empty($payConfig['pay_alipay_config']) ? $payConfig['pay_alipay_config'] : [];
        $this->assign('alipay',unserialize($alipay));
        if (version_compare(PHP_VERSION,'5.5.0','<')) {
            $php_version = 1; // PHP5.4.0或更低版本，可使用旧版支付方式
        }else{
            $php_version = 0;// PHP5.5.0或更高版本，可使用新版支付方式，兼容旧版支付方式
        }
        $this->assign('php_version',$php_version);
        /*--end*/

        return $this->fetch();
    }
    
    // 微信配信信息
    public function wechat_set(){
        if (IS_POST) {
            $post = input('post.');
            if (empty($post['wechat']['appid'])) {
                $this->error('微信AppId不能为空！');
            }
            if (empty($post['wechat']['mchid'])) {
                $this->error('微信商户号不能为空！');
            }
            if (empty($post['wechat']['key'])) {
                $this->error('微信KEY值不能为空！');
            }
            if (empty($post['wechat']['appsecret'])) {
                $this->error('微信AppSecret值不能为空！');
            }

            $data = model('Pay')->payForQrcode($post['wechat']);
            if ($data['return_code'] == 'FAIL') {
                if ('签名错误' == $data['return_msg']) {
                    $this->error('微信KEY值错误！');
                }else if ('appid不存在' == $data['return_msg']) {
                    $this->error('微信AppId错误！');
                }else if ('商户号mch_id或sub_mch_id不存在' == $data['return_msg']) {
                    $this->error('微信商户号错误！');
                }
            }

            foreach ($post as $key => $val) {
                getUsersConfigData('pay', ['pay_wechat_config'=>serialize($val)]);
            }
            $this->success('操作成功');
        }
    }

    // 支付宝配信信息
    public function alipay_set(){
        if (IS_POST) {
            $post = input('post.');
            $php_version = $post['alipay']['version'];
            if (0 == $php_version) {
                if (empty($post['alipay']['app_id'])) {
                    $this->error('支付APPID不能为空！');
                }
                if (empty($post['alipay']['merchant_private_key'])) {
                    $this->error('商户私钥不能为空！');
                }
                if (empty($post['alipay']['alipay_public_key'])) {
                    $this->error('支付宝公钥不能为空！');
                }

                $order_number = getTime();
                $return = $this->check_alipay_order($order_number,'admin_pay',$post['alipay']);
                if ('ok' != $return) {
                    $this->error($return);
                }
            }else if (1 == $php_version) {
                if (empty($post['alipay']['account'])) {
                    $this->error('支付宝账号不能为空！');
                }
                if (empty($post['alipay']['code'])) {
                    $this->error('交易安全校验码不能为空！');
                }
                if (empty($post['alipay']['id'])) {
                    $this->error('合作者身份ID不能为空！');
                }
            }

            // 处理数据中的空格和换行
            $post['alipay']['app_id']               = preg_replace('/\r|\n/', '', $post['alipay']['app_id']);
            $post['alipay']['merchant_private_key'] = preg_replace('/\r|\n/', '', $post['alipay']['merchant_private_key']);
            $post['alipay']['alipay_public_key']    = preg_replace('/\r|\n/', '', $post['alipay']['alipay_public_key']);

            foreach ($post as $key => $val) {
                getUsersConfigData('pay', ['pay_alipay_config'=>serialize($val)]);
            }
            $this->success('操作成功');
        }
    }

    // 充值记录列表
    public function money_index()
    {
        $list = array();
        $keywords = input('keywords/s');

        $condition = array();
        // 应用搜索条件
        if (!empty($keywords)) {
            $condition['a.order_number'] = array('LIKE', "%{$keywords}%");
        }

        // 多语言
        $condition['a.lang'] = array('eq', $this->admin_lang);

        /**
         * 数据查询
         */
        $count = $this->users_money_db->alias('a')->where($condition)->count();// 查询满足要求的总记录数
        $Page = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $this->users_money_db->field('a.*,b.username')
            ->alias('a')
            ->join('__USERS__ b', 'a.users_id = b.users_id', 'LEFT')
            ->where($condition) 
            ->order('a.moneyid desc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pager',$Page);// 赋值分页集

        // 订单类型
        $pay_cause_type_arr = config('global.pay_cause_type_arr');
        $this->assign('pay_cause_type_arr',$pay_cause_type_arr);

        // 充值状态
        $pay_status_arr = config('global.pay_status_arr');
        $this->assign('pay_status_arr',$pay_status_arr);

        // 支付方式
        $pay_method_arr = config('global.pay_method_arr');
        $this->assign('pay_method_arr',$pay_method_arr);

        return $this->fetch();
    }

    // 充值记录编辑
    public function money_edit()
    {   
        $param = input('param.');
        $MoneyData = $this->users_money_db->find($param['moneyid']);
        $this->assign('MoneyData',$MoneyData);
        $UsersData = $this->users_db->find($MoneyData['users_id']);
        $this->assign('UsersData',$UsersData);
        
        // 支付宝查询订单
        if ('alipay' == $MoneyData['pay_method']) {
            $return = $this->check_alipay_order($MoneyData['order_number']);
            $this->assign('return',$return);
        }

        // 微信查询订单
        if ('wechat' == $MoneyData['pay_method']) {
            $return = $this->check_wechat_order($MoneyData['order_number']);
            $this->assign('return',$return);
        }

        // 人为处理订单
        if ('artificial' == $MoneyData['pay_method']) {
            $return = '人为处理';
            $this->assign('return',$return);
        }

        // 获取订单状态
        $pay_status_arr = Config::get('global.pay_status_arr');
        $this->assign('pay_status_arr',$pay_status_arr);

        // 支付方式
        $pay_method_arr = config('global.pay_method_arr');
        $this->assign('pay_method_arr',$pay_method_arr);

        return $this->fetch();
    }

    // 标记订单逻辑
    public function money_mark_order()
    {
        if (IS_POST) {
            $moneyid     = input('param.moneyid/d');

            // 查询订单信息
            $MoneyData = $this->users_money_db->where([
                'moneyid'     => $moneyid,
                'lang'         => $this->admin_lang,
            ])->find();

            // 处理订单逻辑
            if (in_array($MoneyData['status'], [1,3])) {

                $users_id = $MoneyData['users_id'];
                $order_number = $MoneyData['order_number'];
                $return = '';
                if ('alipay' == $MoneyData['pay_method']) { // 支付宝查询订单
                    $return = $this->check_alipay_order($order_number);
                } else if ('wechat' == $MoneyData['pay_method']) { // 微信查询订单
                    $return = $this->check_wechat_order($order_number);
                } else if ('artificial' == $MoneyData['pay_method']) { // 手工充值订单
                    $return = '手工充值';
                }
                
                $result = [
                    'users_id'    => $users_id,
                    'order_number'=> $order_number,
                    'status'      => '手动标记为已充值订单',
                    'details'     => '充值详情：'.$return,
                    'pay_method'  => 'artificial', //人为处理
                    'money'       => $MoneyData['money'],
                    'users_money' => $MoneyData['users_money'],
                ];

                // 标记为未付款
                if (3 == $MoneyData['status']) {
                    $result['status'] = '手动标记为未付款订单';
                } else if (1 == $MoneyData['status']) {
                    $result['status'] = '手动标记为已充值订单';
                }

                // 修改用户充值明细表对应的订单数据，存入返回的数据，订单标记为已付款
                $Where = [
                    'moneyid'  => $MoneyData['moneyid'],
                    'users_id'  => $users_id,
                ];
                
                $UpdateData = [
                    'pay_details'   => serialize($result),
                    'update_time'   => getTime(),
                ];

                // 标记为未付款时则状态更新为1
                if (3 == $MoneyData['status']) {
                    $UpdateData['status'] = 1;
                } else if (1 == $MoneyData['status']) {
                    $UpdateData['status'] = 3;
                }

                $IsMoney = $this->users_money_db->where($Where)->update($UpdateData);

                if (!empty($IsMoney)) {
                    // 同步修改用户的金额
                    $UsersData = [
                        'update_time' => getTime(),
                    ];

                    // 标记为未付款时则减去金额
                    if (3 == $MoneyData['status']) {
                        $UsersData = $this->users_db->field('users_money')->find($users_id);
                        if ($UsersData['users_money'] <= $MoneyData['money']) {
                            $UsersData['users_money'] = 0;
                        }else{
                            $UsersData['users_money'] = Db::raw('users_money-'.$MoneyData['money']);
                        }
                    } else if (1 == $MoneyData['status']) {
                        $UsersData['users_money'] = Db::raw('users_money+'.$MoneyData['money']);
                    }

                    $IsUsers = $this->users_db->where('users_id',$users_id)->update($UsersData);
                    if (!empty($IsUsers)) {
                        $this->success('操作成功');
                    }
                }
            }
            $this->error('操作失败');
        }
    }

    // 查询订单付款状态(微信)
    private function check_wechat_order($order_number)
    {
        if (!empty($order_number)) {
            // 引入文件
            vendor('wechatpay.lib.WxPayApi');
            vendor('wechatpay.lib.WxPayConfig');
            // 实例化加载订单号
            $input  = new \WxPayOrderQuery;
            $input->SetOut_trade_no($order_number);

            // 处理微信配置数据
            $pay_wechat_config = getUsersConfigData('pay.pay_wechat_config');
            $pay_wechat_config = unserialize($pay_wechat_config);
            $config_data['app_id'] = $pay_wechat_config['appid'];
            $config_data['mch_id'] = $pay_wechat_config['mchid'];
            $config_data['key']    = $pay_wechat_config['key'];

            // 实例化微信配置
            $config = new \WxPayConfig($config_data);
            $wxpayapi = new \WxPayApi;

            // 返回结果
            $result = $wxpayapi->orderQuery($config, $input);

            // 判断结果
            if ('ORDERNOTEXIST' == $result['err_code'] && 'FAIL' == $result['result_code']) {
                return '订单在微信中不存在！';
            }else if ('NOTPAY' == $result['trade_state'] && 'SUCCESS' == $result['result_code']) {
                return '订单在微信中生成，但并未支付完成！';
            }else if ('SUCCESS' == $result['trade_state'] && 'SUCCESS' == $result['result_code']) {
                return '订单已使用'.$result['attach'].'完成！';
            }
        }else{
            return false;
        }
    }

    // 查询订单付款状态(支付宝)
    private function check_alipay_order($order_number,$admin_pay='',$alipay='')
    {
        if (!empty($order_number)) {
            // 引入文件
            vendor('alipay.pagepay.service.AlipayTradeService');
            vendor('alipay.pagepay.buildermodel.AlipayTradeQueryContentBuilder');

            // 实例化加载订单号
            $RequestBuilder = new \AlipayTradeQueryContentBuilder;
            $out_trade_no   = trim($order_number);
            $RequestBuilder->setOutTradeNo($out_trade_no);

            // 处理支付宝配置数据
            if (empty($alipay)) {
                $pay_alipay_config = getUsersConfigData('pay.pay_alipay_config');
                if (empty($pay_alipay_config)) {
                    return false;
                }
                $alipay = unserialize($pay_alipay_config);
            }
            $config['app_id']     = $alipay['app_id'];
            $config['merchant_private_key'] = $alipay['merchant_private_key'];
            $config['charset']    = 'UTF-8';
            $config['sign_type']  = 'RSA2';
            $config['gatewayUrl'] = 'https://openapi.alipay.com/gateway.do';
            $config['alipay_public_key'] = $alipay['alipay_public_key'];

            // 实例化支付宝配置
            $aop = new \AlipayTradeService($config);

            // 返回结果
            if (!empty($admin_pay)) {
                $result = $aop->IsQuery($RequestBuilder,$admin_pay);
            }else{
                $result = $aop->Query($RequestBuilder);
            }

            $result = json_decode(json_encode($result),true);

            // 判断结果
            if ('40004' == $result['code'] && 'Business Failed' == $result['msg']) {
                // 用于支付宝支付配置验证
                if (!empty($admin_pay)) { return 'ok'; }
                // 用于订单查询
                return '订单在支付宝中不存在！';
            }else if ('10000' == $result['code'] && 'WAIT_BUYER_PAY' == $result['trade_status']) {
                return '订单在支付宝中生成，但并未支付完成！';
            }else if ('10000' == $result['code'] && 'TRADE_SUCCESS' == $result['trade_status']) {
                return '订单已使用支付宝支付完成！';
            }

            // 用于支付宝支付配置验证
            if (!empty($admin_pay) && !empty($result)) {
                if ('40001' == $result['code'] && 'Missing Required Arguments' == $result['msg']) {
                    return '商户私钥错误！';
                }
                if (!is_array($result)) {
                    return $result;
                }
            }
        }
    }

    /**
     * 版本检测更新弹窗
     */
    public function ajax_check_upgrade_version()
    {
        $memberLogic = new MemberLogic;
        $upgradeMsg = $memberLogic->checkVersion(); // 升级包消息
        $this->success('检测成功', null, $upgradeMsg);  
    }

    /**
    * 一键升级
    */
    public function OneKeyUpgrade(){
        header('Content-Type:application/json; charset=utf-8');
        function_exists('set_time_limit') && set_time_limit(0);

        /*权限控制 by 小虎哥*/
        $auth_role_info = session('admin_info.auth_role_info');
        if(0 < intval(session('admin_info.role_id')) && ! empty($auth_role_info) && intval($auth_role_info['online_update']) <= 0){
            $this->error('您没有操作权限，请联系超级管理员分配权限');
        }
        /*--end*/

        $memberLogic = new MemberLogic;
        $data = $memberLogic->OneKeyUpgrade(); //升级包消息
        if (1 <= intval($data['code'])) {
            $this->success($data['msg'], null, ['code'=>$data['code']]);
        } else {
            $msg = '模板升级异常，请第一时间联系技术支持，排查问题！';
            if (is_array($data)) {
                $msg = $data['msg'];
            }
            $this->error($msg);
        }
    }

    /**
    * 检测目录权限
    */
    public function check_authority()
    {
        $filelist = input('param.filelist/s');
        $memberLogic = new MemberLogic;
        $data = $memberLogic->checkAuthority($filelist); //检测目录权限
        if (is_array($data)) {
            if (1 == $data['code']) {
                $this->success($data['msg']);
            } else {
                $this->error($data['msg'], null, $data['data']);
            }
        } else {
            $this->error('检测模板失败', null, ['code'=>1]);
        }
    }

    // 前台会员左侧菜单
    public function ajax_menu_index()
    {
        $list = array();
        $condition = array();

        // 多语言
        $condition['a.lang'] = array('eq', $this->admin_lang);

        /**
         * 数据查询
         */
        $count = Db::name('users_menu')->alias('a')->where($condition)->count();// 查询满足要求的总记录数
        $Page = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $row = Db::name('users_menu')->field('a.*')
            ->alias('a')
            ->where($condition)
            ->order('a.sort_order asc, a.id asc')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

        $list = [];
        $pay_open = getUsersConfigData('pay.pay_open');
        foreach ($row as $key => $val) {
            /*是否开启支付功能*/
            if (1 != $pay_open && 'user/Pay/pay_consumer_details' == $val['mca']) {
                continue;
            }
            /*--end*/
            $list[] = $val;
        }

        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pager',$Page);// 赋值分页集

        return $this->fetch();
    }
}