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

use think\Db;

class Security extends Base
{
    public $admin_info = array();

    /**
     * 初始化操作
     */
    public function _initialize() {
        parent::_initialize();
        $this->admin_info = session('admin_info');
    }

    public function index()
    {
        // if (IS_POST) {
        //     $this->handleSave();
        // }

        $is_founder = 0;
        if (-1 == $this->admin_info['role_id'] && empty($this->admin_info['parent_id'])) {
            $is_founder = 1;
        }
        $this->admin_info['is_founder'] = $is_founder;
        $this->assign('admin_info', $this->admin_info);

        //自定义后台路径名
        $baseFile = explode('/', $this->request->baseFile());
        $web_adminbasefile = end($baseFile);
        $adminbasefile = preg_replace('/^(.*)\.([^\.]+)$/i', '$1', $web_adminbasefile);
        $this->assign('adminbasefile', $adminbasefile);

        // 安全验证配置
        $security = tpSetting('security');
        if (isset($security['security_verifyfunc'])) {
            $security['security_verifyfunc'] = json_decode($security['security_verifyfunc'], true);
        }
        $security_askanswer_content = '';
        if (!empty($security['security_askanswer_list'])) {
            $security_askanswer_list = json_decode($security['security_askanswer_list'], true);
            $security['security_askanswer_list'] = $security_askanswer_list;
        }
        if (empty($security_askanswer_list)) {
            $security_askanswer_list = config('global.security_askanswer_list');
        }
        $security_askanswer_content = implode(PHP_EOL, $security_askanswer_list);
        $this->assign('security', $security);
        $this->assign('security_askanswer_content', $security_askanswer_content);

        if (!empty($security['security_ask'])) {
            $security_ask = $security['security_ask'];
            if (!in_array($security_ask, $security_askanswer_list)) {
                $security_askanswer_list[] = $security_ask;
            }
        }
        $this->assign('security_askanswer_list', $security_askanswer_list);

        return $this->fetch();
    }

    /**
     * 保存  -  全部（v1.6.2以下版本作废）
     * @return [type] [description]
     */
    // private function handleSave()
    // {
    //     // if (!empty($this->admin_info['parent_id']) || -1 != $this->admin_info['role_id']) {
    //     //     $this->error('该功能仅限于创始人操作！');
    //     // }

    //     $post = input('post.');
    //     $settingData = [];

    //     /*-------------------后台安全配置 start-------------------*/
    //     $param = [
    //         'web_login_expiretime' => $post['web_login_expiretime'],
    //         'login_expiretime_old' => $post['login_expiretime_old'],
    //         'web_login_lockopen'    => !empty($post['web_login_lockopen']) ? 1 : 0,
    //         'web_sqldatapath' => $post['web_sqldatapath'],
    //     ];
    //     // 开启锁定才修改相应的配置值
    //     if (!empty($param['web_login_lockopen'])) {
    //         $param['web_login_errtotal'] = $post['web_login_errtotal'];
    //         $param['web_login_errexpire'] = $post['web_login_errexpire'];
    //     }

    //     // 自定义后台路径名
    //     $adminbasefile = preg_replace('/([^\w\_\-])/i', '', trim($post['adminbasefile'])).'.php'; // 新的文件名
    //     $param['web_adminbasefile'] = $this->root_dir.'/'.$adminbasefile; // 支持子目录
    //     $adminbasefile_old = preg_replace('/^(.*)\/([^\/]+)$/i', '${2}', tpCache('web.web_adminbasefile')); // 旧的文件名
    //     if ('index.php' == $adminbasefile) {
    //         $this->error("后台路径禁止使用index", null, '', 1);
    //     }

    //     // 数据库备份目录
    //     $web_sqldatapath_old = tpCache('global.web_sqldatapath');
    //     $param['web_sqldatapath'] = '/'.trim($param['web_sqldatapath'], '/');

    //     // 后台登录超时
    //     $web_login_expiretime = $param['web_login_expiretime'];
    //     $login_expiretime_old = $param['login_expiretime_old'];
    //     unset($param['login_expiretime_old']);
    //     if ($login_expiretime_old != $web_login_expiretime) {
    //         $web_login_expiretime = preg_replace('/^(\d{0,})(.*)$/i', '${1}', $web_login_expiretime);
    //         empty($web_login_expiretime) && $web_login_expiretime = config('login_expire');
    //         if ($web_login_expiretime > 2592000) {
    //             $web_login_expiretime = 2592000; // 最多一个月
    //         }
    //         $param['web_login_expiretime'] = $web_login_expiretime;
    //         //前台登录超时时间
    //         $users_login_expiretime = getUsersConfigData('users.users_login_expiretime');
    //         //前台和后台谁设置的时间大就用谁的做session过期时间
    //         $max_login_expiretime = $web_login_expiretime;
    //         if ($web_login_expiretime < $users_login_expiretime){
    //             $max_login_expiretime = $users_login_expiretime;
    //         }
    //     }
    //     // 编辑器防注入
    //     $param['web_xss_filter'] = intval($post['web_xss_filter']);
    //     /*-------------------后台安全配置 end-------------------*/

    //     /*-------------------二次安全验证 start-------------------*/
    //     $this->handleAskData($settingData, $post);
    //     /*-------------------二次安全验证 end-------------------*/

    //     /*多语言*/
    //     if (is_language()) {
    //         $langRow = \think\Db::name('language')->order('id asc')
    //             ->cache(true, EYOUCMS_CACHE_TIME, 'language')
    //             ->select();
    //         foreach ($langRow as $key => $val) {
    //             tpCache('web', $param, $val['mark']);
    //             tpSetting('security', $settingData, $val['mark']);
    //         }
    //     } else {
    //         tpCache('web', $param);
    //         tpSetting('security', $settingData);
    //     }
    //     /*--end*/

    //     $refresh = false;

    //     /*-------------------后台安全配置 start-------------------*/
    //     // 更改session会员设置 - session有效期（后台登录超时）
    //     if ($login_expiretime_old != $web_login_expiretime) {
    //         $session_conf = [];
    //         $session_file = APP_PATH.'admin/conf/session_conf.php';
    //         if (file_exists($session_file)) {
    //             require_once($session_file);
    //             $session_conf_tmp = EY_SESSION_CONF;
    //             if (!empty($session_conf_tmp)) {
    //                 $session_conf_tmp = json_decode($session_conf_tmp, true);
    //                 if (!empty($session_conf_tmp) && is_array($session_conf_tmp)) {
    //                     $session_conf = $session_conf_tmp;
    //                 }
    //             }
    //         }
    //         $session_conf['expire'] = $max_login_expiretime;
    //         $str_session_conf = '<?php'.PHP_EOL.'$session_1600593464 = json_encode('.var_export($session_conf,true).');'.PHP_EOL.'define(\'EY_SESSION_CONF\', $session_1600593464);';
    //         @file_put_contents(APP_PATH . 'admin/conf/session_conf.php', $str_session_conf);
    //     }

    //     // 更改自定义后台路径名 - 刷新整个后台
    //     $gourl = request()->domain().$this->root_dir.'/'.$adminbasefile; // 支持子目录
    //     if ($adminbasefile_old != $adminbasefile && eyPreventShell($adminbasefile_old)) {
    //         if (file_exists($adminbasefile_old)) {
    //             if(rename($adminbasefile_old, $adminbasefile)) {
    //                 $refresh = true;
    //             }
    //         } else {
    //             $this->error("根目录{$adminbasefile_old}文件不存在！", null, '', 2);
    //         }
    //     }
    //     if ($web_sqldatapath_old != $param['web_sqldatapath'] && preg_match('/^\/data\/sqldata([^\/]*)$/i', $param['web_sqldatapath'])) {
    //         @rename(ROOT_PATH.ltrim($web_sqldatapath_old, '/'), ROOT_PATH.ltrim($param['web_sqldatapath'], '/'));
    //     }
    //     /*-------------------后台安全配置 end-------------------*/

    //     if ($refresh) {
    //         $this->success('操作成功', $gourl, '', 1, [], '_parent');
    //     }

    //     $this->success('操作成功', url('Security/index'));
    // }

    /**
     * 保存 - 后台安全中心
     * @return [type] [description]
     */
    public function handleSave1()
    {
        if (IS_POST) {
            $post = input('post.');
            $settingData = [];

            /*-------------------后台安全配置 start-------------------*/
            $param = [
                'web_login_expiretime' => $post['web_login_expiretime'],
                'login_expiretime_old' => $post['login_expiretime_old'],
                'web_login_lockopen'    => !empty($post['web_login_lockopen']) ? 1 : 0,
                'web_sqldatapath' => $post['web_sqldatapath'],
            ];
            // 开启锁定才修改相应的配置值
            if (!empty($param['web_login_lockopen'])) {
                $param['web_login_errtotal'] = $post['web_login_errtotal'];
                $param['web_login_errexpire'] = $post['web_login_errexpire'];
            }

            // 自定义后台路径名
            $adminbasefile = preg_replace('/([^\w\_\-])/i', '', trim($post['adminbasefile'])).'.php'; // 新的文件名
            $param['web_adminbasefile'] = $this->root_dir.'/'.$adminbasefile; // 支持子目录
            $baseFile = explode('/', $this->request->baseFile());
            $adminbasefile_old = end($baseFile); // 旧的文件名
            if ('index.php' == $adminbasefile) {
                $this->error("后台路径禁止使用index", null, '', 1);
            }

            // 数据库备份目录
            $web_sqldatapath_old = tpCache('global.web_sqldatapath');
            $param['web_sqldatapath'] = '/'.trim($param['web_sqldatapath'], '/');

            // 后台登录超时
            $web_login_expiretime = $param['web_login_expiretime'];
            $login_expiretime_old = $param['login_expiretime_old'];
            unset($param['login_expiretime_old']);
            if ($login_expiretime_old != $web_login_expiretime) {
                $web_login_expiretime = preg_replace('/^(\d{0,})(.*)$/i', '${1}', $web_login_expiretime);
                empty($web_login_expiretime) && $web_login_expiretime = config('login_expire');
                if ($web_login_expiretime > 2592000) {
                    $web_login_expiretime = 2592000; // 最多一个月
                }
                $param['web_login_expiretime'] = $web_login_expiretime;
                //前台登录超时时间
                $users_login_expiretime = getUsersConfigData('users.users_login_expiretime');
                //前台和后台谁设置的时间大就用谁的做session过期时间
                $max_login_expiretime = $web_login_expiretime;
                if ($web_login_expiretime < $users_login_expiretime){
                    $max_login_expiretime = $users_login_expiretime;
                }
            }
            // 编辑器防注入
            $param['web_xss_filter'] = intval($post['web_xss_filter']);
            // 网站防止被刷
            $param['web_anti_brushing'] = intval($post['web_anti_brushing']);
            /*-------------------后台安全配置 end-------------------*/

            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache('web', $param, $val['mark']);
                }
            } else {
                tpCache('web', $param);
            }
            /*--end*/

            $refresh = false;

            /*-------------------后台安全配置 start-------------------*/
            // 更改session会员设置 - session有效期（后台登录超时）
            if ($login_expiretime_old != $web_login_expiretime) {
                $session_conf = [];
                $session_file = APP_PATH.'admin/conf/session_conf.php';
                if (file_exists($session_file)) {
                    require_once($session_file);
                    $session_conf_tmp = EY_SESSION_CONF;
                    if (!empty($session_conf_tmp)) {
                        $session_conf_tmp = json_decode($session_conf_tmp, true);
                        if (!empty($session_conf_tmp) && is_array($session_conf_tmp)) {
                            $session_conf = $session_conf_tmp;
                        }
                    }
                }
                $session_conf['expire'] = $max_login_expiretime;
                $str_session_conf = '<?php'.PHP_EOL.'$session_1600593464 = json_encode('.var_export($session_conf,true).');'.PHP_EOL.'define(\'EY_SESSION_CONF\', $session_1600593464);';
                @file_put_contents(APP_PATH . 'admin/conf/session_conf.php', $str_session_conf);
            }

            // 更改自定义后台路径名 - 刷新整个后台
            $gourl = request()->domain().$this->root_dir.'/'.$adminbasefile; // 支持子目录
            if ($adminbasefile_old != $adminbasefile && eyPreventShell($adminbasefile_old)) {
                if (file_exists($adminbasefile_old)) {
                    if(rename($adminbasefile_old, $adminbasefile)) {
                        $refresh = true;
                    }
                } else {
                    $this->error("根目录{$adminbasefile_old}文件不存在！", null, '', 2);
                }
            }
            if ($web_sqldatapath_old != $param['web_sqldatapath'] && preg_match('/^\/data\/sqldata([^\/]*)$/i', $param['web_sqldatapath'])) {
                @rename(ROOT_PATH.ltrim($web_sqldatapath_old, '/'), ROOT_PATH.ltrim($param['web_sqldatapath'], '/'));
            }
            /*-------------------后台安全配置 end-------------------*/

            if ($refresh) {
                $this->success('操作成功', $gourl, '', 1, [], '_parent');
            }

            $this->success('操作成功', url('Security/index'));
        }
        $this->error('操作失败');
    }

    /**
     * 保存 - 安全验证中心
     * @return [type] [description]
     */
    public function handleSave2()
    {
        if (IS_POST) {
            $settingData = [];
            $post = input('post.');

            if (empty($post['security_ask_open'])) {
                $securityOld = tpSetting('security');
                if (!empty($securityOld['security_ask'])) {
                    $answer = empty($post['security_answer_old']) ? '' : trim($post['security_answer_old']);
                    if (empty($answer)) {
                        $this->error('请录入密保答案！');
                    } else {
                        $security_answer = empty($securityOld['security_answer']) ? '' : trim($securityOld['security_answer']);
                        $encrypt_answer = func_encrypt($answer, true, pwd_encry_type('bcrypt'));
                        if ($security_answer != $encrypt_answer) {
                            $this->error('密保答案不正确！');
                        }
                    }
                    $this->submit_answer_verify();
                }
            }

            /*-------------------二次安全验证 start-------------------*/
            $this->handleAskData($settingData, $post);
            /*-------------------二次安全验证 end-------------------*/

            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpSetting('security', $settingData, $val['mark']);
                }
            } else {
                tpSetting('security', $settingData);
            }
            /*--end*/

            // 设置问题答案后，自动验证通过
            $this->submit_answer_verify();

            $msg = "操作成功";
            $is_show_answer = 0;
            if (!empty($settingData['security_answer']) && !empty($settingData['security_ask_open'])) {
                $is_show_answer = 1;
                $securityData = tpSetting('security');
                $msg = "问题：{$securityData['security_ask']}<br/>答案：".mchStrCode($securityData['security_answer_bright'], 'DECODE'); 
            }
            $this->success($msg, url('Security/index'), ['is_show_answer'=>$is_show_answer,'security_ask_open'=>$settingData['security_ask_open']]);
        }
        $this->error('操作失败');
    }

    /**
     * 保存二次安全验证的数据处理
     * @param  array  &$settingData [description]
     * @param  array  &$post        [description]
     * @return [type]               [description]
     */
    private function handleAskData(&$settingData = [], &$post = [])
    {
        $securityOld = tpSetting('security');
        $security_ask = intval($post['security_ask']);
        $security_answer = trim($post['security_answer']);
        $is_ask_add_edit = empty($securityOld['security_ask']) ? 'add' : 'edit';
        if ('add' == $is_ask_add_edit) {
            if (empty($post['security_ask_open'])) {
                $this->success('操作成功', url('Security/index'), ['is_show_answer'=>0,'security_ask_open'=>0]);
            }
            if (0 > intval($security_ask)) {
                $this->error('请选择密保问题！');
            } else if ($security_answer === '') {
                $this->error('请设置密保答案！');
            }
            $encrypt_answer = func_encrypt($security_answer, true, pwd_encry_type('bcrypt'));
            $row = Db::name('admin')->where([
                    'admin_id'  => $this->admin_info['admin_id'],
                    'password'  => $encrypt_answer,
                ])->count();
            if (!empty($row)) {
                $this->error('密保答案不能与登录密码一致！');
            }
        } else {
            $security_answer_old = trim($post['security_answer_old']);
            if ($security_answer !== '' || 0 <= intval($security_ask)) {
                if ($security_answer_old === '') {
                    $this->error('密保答案不能为空！');
                } else {
                    if (0 <= intval($security_ask)) {
                        if ($security_answer === '') {
                            $this->error('请重置密保答案！');
                        } else if ($security_answer === $security_answer_old) {
                            $this->error('重置密保答案不能与原来的一致！');
                        }
                    }
                }
                $encrypt_answer_old = func_encrypt($security_answer_old, true, pwd_encry_type('bcrypt'));
                if ($encrypt_answer_old != $securityOld['security_answer']) {
                    $this->error('密保答案不正确！');
                }

                $encrypt_answer = func_encrypt($security_answer, true, pwd_encry_type('bcrypt'));
                $row = Db::name('admin')->where([
                        'admin_id'  => $this->admin_info['admin_id'],
                        'password'  => $encrypt_answer,
                    ])->count();
                if (!empty($row)) {
                    $this->error('重置密保答案不能与登录密码一致！');
                }
            } else {
                if ($security_answer_old !== '') {
                    $encrypt_answer_old = func_encrypt($security_answer_old, true, pwd_encry_type('bcrypt'));
                    if ($encrypt_answer_old != $securityOld['security_answer']) {
                        $this->error('密保答案不正确！');
                    }
                }
                unset($post['security_ask']);
                unset($post['security_answer']);
                unset($post['security_answer_old']);
            }
        }

        /**
         * 如果要关闭二次安全验证，必须要进行答案验证
         * 同IP不验证功能也会影响到这里的逻辑
         */
        // 问题列表
        $security_askanswer_list = empty($securityOld['security_askanswer_list']) ? config('global.security_askanswer_list') : json_decode($securityOld['security_askanswer_list'], true);
        // 当前管理员二次安全验证过的IP地址
        $security_answerverify_ip = !empty($securityOld['security_answerverify_ip']) ? $securityOld['security_answerverify_ip'] : '-1';
        // 1、问答要已设置；2、目前是开启；3、当前要关闭；
        if (!empty($securityOld['security_ask_open']) && empty($post['security_ask_open']) && !empty($securityOld['security_ask'])) {
            $admin_info = Db::name('admin')->field('*')->where(['admin_id'=>$this->admin_info['admin_id']])->find();
            // if (!empty($admin_info['parent_id']) || -1 != $admin_info['role_id']) {
            //     $this->error('创始人才能关闭安全验证功能！');
            // }
            if ($admin_info['last_ip'] != $security_answerverify_ip) {
                $this->error("<span style='display:none;'>__html__</span>出于安全考虑<br/>请勿非法越过密保答案验证", null, '', 3);
            }
        }
        $settingData['security_ask_open'] = intval($post['security_ask_open']);
        if (!empty($settingData['security_ask_open'])) {
            $post['security_verifyfunc'][] = 'Filemanager@*';
            $post['security_verifyfunc'][] = 'Arctype@ajax_newtpl';
            $post['security_verifyfunc'][] = 'Archives@ajax_newtpl';
            // $post['security_verifyfunc'][] = 'Security@*';
            $post['security_verifyfunc'] = array_unique($post['security_verifyfunc']);
            $settingData['security_verifyfunc'] = json_encode($post['security_verifyfunc']);
            $settingData['security_ask_ip_open'] = !empty($post['security_ask_ip_open']) ? intval($post['security_ask_ip_open']) : 0;
            if (isset($post['security_ask'])) {
                $settingData['security_ask'] = $security_askanswer_list[$post['security_ask']];
            }
            if (isset($post['security_answer'])) {
                $settingData['security_answer'] = func_encrypt($post['security_answer'], true, pwd_encry_type('bcrypt'));
                $settingData['security_answer_bright'] = mchStrCode($post['security_answer']);
            }
            if (empty($securityOld['security_askanswer_list'])) {
                $settingData['security_askanswer_list'] = json_encode($security_askanswer_list);
            }
        }
    }

    /*--------------------------------安全验证中心 start--------------------------*/

    /**
     * 设置二次安全验证的问题、答案
     */
    public function second_verify_add()
    {
        $security_askanswer_list = tpSetting('security.security_askanswer_list');
        $security_askanswer_list = json_decode($security_askanswer_list, true);
        if (empty($security_askanswer_list)) {
            $security_askanswer_list = config('global.security_askanswer_list');
        }

        if (IS_POST) {
            // 修补越权的漏洞，在重设答案时，通过抓包改成新设答案
            if (!empty($this->globalConfig['security_ask'])) {
                $this->error('已设置过密保，请重新设置');
            }

            $ask = input('post.ask/d');
            $answer = input('post.answer/s');
            $answer = trim($answer);

            if (0 > $ask) {
                $this->error('请选择密保问题！');
            } else if (empty($answer)) {
                $this->error('密保答案不能为空！');
            }

            $encrypt_answer = func_encrypt($answer, true, pwd_encry_type('bcrypt'));
            $row = Db::name('admin')->where([
                    'admin_id'  => $this->admin_info['admin_id'],
                    'password'  => $encrypt_answer,
                ])->count();
            if (!empty($row)) {
                $this->error('密保答案不能与登录密码一致！');
            }

            $data = [
                'security_ask_open'   => 1,
                'security_ask'   => $security_askanswer_list[$ask],
                'security_answer'   => $encrypt_answer,
                'security_answer_bright'   => mchStrCode($answer),
                'security_askanswer_list' => json_encode($security_askanswer_list),
            ];
            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpSetting('security', $data, $val['mark']);
                }
            } else {
                tpSetting('security', $data);
            }
            /*--end*/

            $this->success('操作成功', url('Security/index'));
        }

        $this->assign('security_askanswer_list', $security_askanswer_list);

        return $this->fetch();
    }

    /**
     * 修改二次安全验证的问题、答案
     */
    public function second_verify_edit()
    {
        $security_askanswer_list = tpSetting('security.security_askanswer_list');
        $security_askanswer_list = json_decode($security_askanswer_list, true);
        if (empty($security_askanswer_list)) {
            $security_askanswer_list = config('global.security_askanswer_list');
        }

        if (IS_POST) {
            $post = input('post.');
            $answer_old = trim($post['answer_old']);
            $ask = intval($post['ask']);
            $answer = trim($post['answer']);

            if (empty($answer_old)) {
                $this->error('密保答案不能为空！');
            } else {
                if (0 <= $ask) {
                    if (empty($answer)) {
                        $this->error('重置密保答案不能为空！');
                    } else if ($answer == $answer_old) {
                        $this->error('重置密保答案不能与原来的一致！');
                    }
                } 
            }

            $security = tpSetting('security');
            $encrypt_answer_old = func_encrypt($answer_old, true, pwd_encry_type('bcrypt'));
            if ($encrypt_answer_old != $security['security_answer']) {
                $this->error('密保答案不正确！');
            }

            $data = [];
            if (0 <= $ask) {
                $encrypt_answer = func_encrypt($answer, true, pwd_encry_type('bcrypt'));
                $row = Db::name('admin')->where([
                        'admin_id'  => $this->admin_info['admin_id'],
                        'password'  => $encrypt_answer,
                    ])->count();
                if (!empty($row)) {
                    $this->error('重置密保答案不能与登录密码一致！');
                }
                $data['security_ask'] = $security_askanswer_list[$ask];
                $data['security_answer'] = $encrypt_answer;
                $data['security_answer_bright'] = mchStrCode($answer);
                $data['security_askanswer_list'] = json_encode($security_askanswer_list);
            }

            if (!empty($data)) {
                /*多语言*/
                if (is_language()) {
                    $langRow = \think\Db::name('language')->order('id asc')
                        ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                        ->select();
                    foreach ($langRow as $key => $val) {
                        tpSetting('security', $data, $val['mark']);
                    }
                } else {
                    tpSetting('security', $data);
                }
                /*--end*/
            }

            $this->success('操作成功', url('Security/index'));
        }

        $security = tpSetting('security');
        if (!empty($security)) {
            $security_ask = $security['security_ask'];
            if (!in_array($security_ask, $security_askanswer_list)) {
                $security_askanswer_list[] = $security_ask;
            }
        }
        $this->assign('security', $security);
        $this->assign('security_askanswer_list', $security_askanswer_list);

        return $this->fetch();
    }

    /**
     * 二次安全验证答案
     * @return [type] [description]
     */
    public function ajax_answer_verify()
    {
        if (IS_POST) {
            $answer = input('param.answer/s');
            $answer = trim($answer);
            if (empty($answer)) {
                $this->error('请录入密保答案！');
            } else {
                $security_answer = tpSetting('security.security_answer');
                $encrypt_answer = func_encrypt($answer, true, pwd_encry_type('bcrypt'));
                if ($security_answer != $encrypt_answer) {
                    $this->error('密保答案不正确！');
                }
            }
            $this->submit_answer_verify();
            $this->success('密保验证成功');
        }
    }

    /**
     * 二次安全验证答案-提交
     * @return [type] [description]
     */
    private function submit_answer_verify()
    {
        /*多语言*/
        $ip = clientIP();
        if (is_language()) {
            $langRow = \think\Db::name('language')->order('id asc')
                ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                ->select();
            foreach ($langRow as $key => $val) {
                tpSetting('security', ['security_answerverify_ip'=>$ip], $val['mark']);
            }
        } else {
            tpSetting('security', ['security_answerverify_ip'=>$ip]);
        }
        /*--end*/

        // 解决个别用户安装后，登录后台没记录最后登录IP地址，导致一直弹出验证答案
        $admin_info = Db::name('admin')->field('admin_id,last_ip')->where(['admin_id'=>$this->admin_info['admin_id']])->find();
        Db::name('admin')->where(['admin_id'=>$admin_info['admin_id']])->save(['last_ip'=>$ip, 'update_time'=>getTime()]);
    }

    /**
     * 是否已验证了答案
     * @return [type] [description]
     */
    public function ajax_isverify_answer()
    {
        if (IS_POST) {
            $security = tpSetting('security');
            $security_answerverify_ip = !empty($security['security_answerverify_ip']) ? $security['security_answerverify_ip'] : '-1';
            $admin_info = Db::name('admin')->field('admin_id,last_ip')->where(['admin_id'=>$this->admin_info['admin_id']])->find();
            if ($admin_info['last_ip'] == $security_answerverify_ip) {
                $this->success('已验证');
            }
        }
        $this->error('未验证');
    }

    /**
     * 修改问题列表
     * @return [type] [description]
     */
    public function save_ask_list()
    {
        if (IS_POST) {
            $value = input('post.value/s');
            $value = str_replace(["\r\n", "\n\r", "\r", "\n"], PHP_EOL, $value);
            $arr = explode(PHP_EOL, $value);
            foreach ($arr as $key => $val) {
                $val = trim($val);
                if (empty($val)) {
                    unset($arr[$key]);
                } else {
                    $arr[$key] = $val;
                }
            }
            if (empty($arr)) {
                $this->error('问题列表不能为空！');
            }

            // 将已设置的问题加入列表中
            $security_ask = tpSetting('security.security_ask');
            $security_ask = trim($security_ask);
            if (!empty($security_ask) && !in_array($security_ask, $arr)) {
                $arr[] = $security_ask;
            }

            if (is_language()) {
                $langRow = Db::name('language')->order('id asc')->select();
                foreach ($langRow as $key => $val) {
                    tpSetting('security', ['security_askanswer_list'=>json_encode($arr)], $val['mark']);
                }
            } else { // 单语言
                tpSetting('security', ['security_askanswer_list'=>json_encode($arr)]);
            }
            $value = implode(PHP_EOL, $arr);

            $this->success('操作成功', null, ['value'=>$value, 'security_askanswer_list'=>$arr]);
        }
    }

    /**
     * 独立弹窗的安全验证中心（用于点击入口模板管理）
     * @return [type] [description]
     */
    public function second_ask_init()
    {
        if (IS_POST) {
            $settingData = [];
            $post = input('post.');
            
            /*-------------------二次安全验证 start-------------------*/
            $this->handleAskData($settingData, $post);
            /*-------------------二次安全验证 end-------------------*/

            /*多语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpSetting('security', $settingData, $val['mark']);
                }
            } else {
                tpSetting('security', $settingData);
            }
            /*--end*/

            // 设置问题答案后，自动验证通过
            $this->submit_answer_verify();

            $is_show_answer = 0;
            if (empty($settingData['security_ask_open'])) {
                $gourl = "";
                $msg = "操作成功";
            } else {
                $gourl = input('param.gourl/s', '', null);
                if (empty($settingData['security_answer'])) {
                    $msg = "操作成功";
                } else {
                    $is_show_answer = 1;
                    $securityData = tpSetting('security');
                    $msg = "问题：{$securityData['security_ask']}<br/>答案：".mchStrCode($securityData['security_answer_bright'], 'DECODE'); 
                }
            }
            $this->success($msg, null, ['gourl'=>$gourl,'is_show_answer'=>$is_show_answer]);
        }

        $is_founder = 0;
        if (-1 == $this->admin_info['role_id'] && empty($this->admin_info['parent_id'])) {
            $is_founder = 1;
        }
        $this->admin_info['is_founder'] = $is_founder;
        $this->assign('admin_info', $this->admin_info);

        // 安全验证配置
        $security = tpSetting('security');
        if (!isset($security['security_ask_open'])) {
            $security['security_ask_open'] = 1;
        }
        if (isset($security['security_verifyfunc'])) {
            $security['security_verifyfunc'] = json_decode($security['security_verifyfunc'], true);
        }
        $security_askanswer_content = '';
        if (!empty($security['security_askanswer_list'])) {
            $security_askanswer_list = json_decode($security['security_askanswer_list'], true);
            $security['security_askanswer_list'] = $security_askanswer_list;
        }
        if (empty($security_askanswer_list)) {
            $security_askanswer_list = config('global.security_askanswer_list');
        }
        $security_askanswer_content = implode(PHP_EOL, $security_askanswer_list);
        $this->assign('security', $security);
        $this->assign('security_askanswer_content', $security_askanswer_content);

        if (!empty($security['security_ask'])) {
            $security_ask = $security['security_ask'];
            if (!in_array($security_ask, $security_askanswer_list)) {
                $security_askanswer_list[] = $security_ask;
            }
        }
        $this->assign('security_askanswer_list', $security_askanswer_list);

        $gourl = input('param.gourl/s');
        $this->assign('gourl', urldecode($gourl));
        // 点击来源
        $source = input('param.source/s');
        $this->assign('source', $source);

        return $this->fetch();
    }

    public function ajax_security_ask_open()
    {
        $data = tpSetting('security');
        $data['security_ask_open'] = empty($data['security_ask_open']) ? 0 : intval($data['security_ask_open']);
        $this->success('请求成功', null, $data);
    }

    /*-----------------------ddos攻击脚本查杀 start-----------------------*/

    /**
     * DDOS攻击脚本查杀
     * @return [type] [description]
     */
    public function ddos_kill()
    {
        $Prefix = config('database.prefix');
        $isTable = Db::query('SHOW TABLES LIKE \''.$Prefix.'ddos_log\'');
        if (empty($isTable)) {
            $tableSql = <<<EOF
CREATE TABLE `{$Prefix}ddos_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `md5key` varchar(50) DEFAULT '' COMMENT 'md5值',
  `file_name` varchar(500) DEFAULT '' COMMENT '文件名',
  `file_num` int(10) DEFAULT '0' COMMENT '已扫描数',
  `file_total` int(10) DEFAULT '0' COMMENT '总文件数',
  `file_num_ky` int(10) DEFAULT '0' COMMENT '可疑恶意文件数',
  `is_suspicious` tinyint(1) DEFAULT '0' COMMENT '是否可疑',
  `html` text,
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='ddos查杀进度记录表';
EOF;
            $r = @Db::execute($tableSql);
            if ($r !== false) {
                schemaTable('ddos_log');
            }
        }

        $assign = [
            'root_path' => ROOT_PATH,
        ];
        $this->assign($assign);

        return $this->fetch();
    }

    /**
     * 扫描
     * @return [type] [description]
     */
    public function ddos_scan()
    {
        //防止超时/内存溢出
        function_exists('set_time_limit') && set_time_limit(0);
        @ini_set('memory_limit','-1');
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        if (IS_POST) {
            Db::name('ddos_log')->where(['id'=>['gt',0]])->delete();
            $start=getTime();
            $list = [];
            $html = '';
            $dir = ROOT_PATH;
            if (!is_readable($dir)) {
                $dir = str_replace('\\', '/', $dir);
                $dir = rtrim($dir, '/').'/';
            }
            $total = $num_ky = $scanned = 0;
            $auth_code = tpCache('system.system_auth_code');
            $this->ddos_getDirFile($dir, '', $list, $total);
            foreach ($list as $key => $value) {
                $md5key = md5($value.$auth_code);
                $fd = realpath($value);
                $fp = fopen($fd, "r");        
                $scanned +=1;
                $i = 0;
                $is_suspicious = 0;
                while ($buffer = fgets($fp, 4096)) {
                    $i++;
                    if ($this->ddos_checkCodeFeatures($buffer)) {
                        $num_ky += 1;
                        $j = $num_ky % 2 + 1;
                        $buffer = htmlspecialchars($this->ddos_cut_str($buffer,120,0));
                        $is_suspicious = 1;
                        $html .= <<<EOF
<tr class='alt{$j}' onmouseover='this.className="focus";' onmouseout='this.className="alt{$j}";'>
    <td align="center">{$num_ky}</td>
    <td>{$fd}</td>
    <td>第 {$i} 行</td>
    <td>{$buffer}</td>
    <td><a href="javascript:void(0);" data-md5key="{$md5key}" onclick="delfile(this);">删除</a></td>
</tr>
EOF;
                    }
                } 
                fclose($fp);

                Db::name('ddos_log')->insert([
                    'md5key'    => $md5key,
                    'file_name'   => base64_encode($value),
                    'file_num'    => $scanned,
                    'file_total'  => $total,
                    'file_num_ky'    => $num_ky,
                    'is_suspicious'=>$is_suspicious,
                    'html'        => empty($html) ? '' : htmlspecialchars($html),
                    'add_time'      => getTime(),
                ]);
            }
            $end = getTime();
            $spent = ($end - $start);
            $spent_str = '';
            $hours = intval($spent/3600);
            if (!empty($hours)) {
                $spent_str .= $hours."小时";
            }
            if ($spent >= 60) {
                $spent_str .= gmdate('i分', $spent);
            }
            $spent_str .= gmdate('s秒', $spent);

            if (empty($num_ky)) {
$html = <<<EOF
<tr>
    <td class="no-data" style="width: auto !important;" align="center" axis="col0" colspan="5">
        <i class="fa fa-exclamation-circle"></i>没有发现可疑文件
    </td>
</tr>
EOF;
            }

            $data = [
                'scanned'  => $scanned,
                'num_ky'  => $num_ky,
                'spent'  => $spent_str,
                'html'  => $html,
            ];
            $this->success("扫描完成，请自己排查处理", null, $data);
        }
    }

    /**
     * 是否是可疑恶意文件
     * @param  string $buffer [description]
     * @return [type]         [description]
     */
    private function ddos_checkCodeFeatures($buffer = '')
    {
        $bool = false;
        if (!empty($buffer)) {
            if (
                preg_match('/(pfsoc'.'kopen|fsoc'.'kopen)\("(udp|tcp)/i', $buffer) || 
                preg_match('/Php(\s+)(\d+)(\s+)Termi'.'nator/i', $buffer) || 
                preg_match('/[\$_G'.'ET|\$_REQU'.'EST]\[\'rat\']/i', $buffer) || 
                preg_match('/Tcp3(\s+)CC\.center/i', $buffer) || 
                preg_match('/xdos\.s/i', $buffer) || 
                preg_match('/儏摓煁'.'晜泟/i', $buffer) || 
                preg_match('/((FilemanagerModel\.php)|(\$qaz(\s*)=(\s*)\$qwe)|(include(\s*)\((\s*)([\"\']+)\/tmp\/)|(\$content'.'_mb(\s*)=(\s*))|(file_get_contents(\s*)\((\s*)\$auth_role_admin(\s*)\)))/i', $buffer) ||
                preg_match('/function(\s+)httpGetlai\(/i', $buffer)
            ) {
                $bool = true;
            }
        }

        return $bool;
    }

    private function ddos_cut_str($string, $sublen, $start = 0, $code = 'UTF-8') {
        if ($code == 'UTF-8') {
            $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
            preg_match_all($pa, $string, $t_string);
            if (count($t_string[0]) - $start > $sublen) {
                return join('', array_slice($t_string[0], $start, $sublen)) . "...";
            }
            return join('', array_slice($t_string[0], $start, $sublen));
        } else {
            $start = $start * 2;
            $sublen = $sublen * 2;
            $strlen = strlen($string);
            $tmpstr = '';
            for($i = 0; $i < $strlen; $i++) {
                if ($i >= $start && $i < ($start + $sublen)) {
                    if (ord(substr($string, $i, 1)) > 129) {
                        $tmpstr .= substr($string, $i, 2);
                    } else {
                        $tmpstr .= substr($string, $i, 1);
                    } 
                } 
                if (ord(substr($string, $i, 1)) > 129) {
                    $i++;
                }
            } 
            if (strlen($tmpstr) < $strlen) {
                $tmpstr .= "...";
            }

            return $tmpstr;
        } 
    }

    /**
     * 递归读取文件夹文件
     */
    private function ddos_getDirFile($directory, $dir_name = '', &$arr_file = array(), &$total = 0)
    {
        $self = '';//'Security.php';
        $mydir = dir($directory);
        while ($file = $mydir->read()) {
            if ((is_dir("$directory/$file")) && !in_array($file, ['.','..','uploads'])) {
                if ($dir_name) {
                    $this->ddos_getDirFile("$directory/$file", "$dir_name/$file", $arr_file, $total);
                } else {
                    $this->ddos_getDirFile("$directory/$file", "$file", $arr_file, $total);
                }
            } else {
                if($file != $self){
                    if (!in_array($file, ['.','..','uploads']) && preg_match("/\.(php|htm|asp|jsp)$/i", $file)) {
                        $total +=1;
                        if ($dir_name) {
                            $arr_file[] = "$dir_name/$file";
                        } else {
                            $arr_file[] = "$file";
                        }
                    } 
                }
            } 
        }
        $mydir->close();

        return $arr_file;
    }

    /**
     * 扫描进度
     * @return [type] [description]
     */
    public function ddos_progressd()
    {
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        if (IS_AJAX) {
            $progress = 0;
            $result = [];
            $init = input('param.init/d');
            if (empty($init)) {
                Db::name('ddos_log')->where(['id'=>['gt',0]])->delete();
            } else {
                $result = Db::name('ddos_log')->field('id, file_num, file_total, file_num_ky, html')->order('id desc')->find();
            }
            if (!empty($result)) {
                $progress = $result['file_num'] / $result['file_total'];
                $progress = floor($progress*100)/100;
                if ($progress >= 1) {
                    Db::name('ddos_log')->where(['id'=>['gt',0], 'is_suspicious'=>0])->delete();
                }
                $progress = strval($progress * 100);
                if (empty($result['file_num_ky'])) {
                    $html = <<<EOF
<tr>
    <td class="no-data" style="width: auto !important;" align="center" axis="col0" colspan="5">
        <i class="fa fa-exclamation-circle"></i>正在扫描中
    </td>
</tr>
EOF;
                } else {
                    $html = htmlspecialchars_decode($result['html']);
                }
                $this->success('请求成功', null, ['progress'=>$progress,'file_num'=>$result['file_num'],'file_num_ky'=>$result['file_num_ky'],'html'=>$html]);
            } else {
                $this->success('请求成功', null, ['progress'=>$progress]);
            }
        }
    }

    /**
     * 删除可疑恶意文件
     * @return [type] [description]
     */
    public function ddos_delfile()
    {
        if (IS_AJAX) {
            $md5key = input('param.md5key/s');
            $result = Db::name('ddos_log')->where(['md5key'=>$md5key, 'is_suspicious'=>1])->find();
            if (empty($result)) {
                $this->success('操作成功');
            }

            $filename = !empty($result['file_name']) ? trim($result['file_name'], '/') : '';
            if (!empty($filename) && is_file($filename)) {
                $filetype = pathinfo($filename, PATHINFO_EXTENSION);
                $phpfile = strtolower(stristr($filename,'.php'));
                if ($phpfile || in_array($filetype, ['php','js','png','gif','jpg','jpeg','ico','bmp','webp','htm','asp','jsp'])) {
                    $fd = realpath($filename);
                    $fp = fopen($fd, "r");
                    $num_ky = 0;
                    while ($buffer = fgets($fp, 4096)) {
                        if ($this->ddos_checkCodeFeatures($buffer)) {
                            $num_ky = 1;
                            break;
                        }
                    } 
                    fclose($fp);

                    if (!empty($num_ky)) {
                        @unlink('./'.$filename);
                        $this->success('操作成功');
                    }
                }
            }
        }
        $this->error('操作失败');
    }
    /*-----------------------ddos攻击脚本查杀 end-----------------------*/
}