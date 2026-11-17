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

class Ajax extends Base
{
    /*
     * 初始化操作
     */
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 安全验证模型名称，防止动态类实例化RCE攻击
     */
    private function validateCtlName($ctl_name)
    {
        if (empty($ctl_name)) {
            return false;
        }
        // 只允许字母和数字，防止注入
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', $ctl_name)) {
            return false;
        }
        // 从 channeltype 表读取允许的模型名称
        $allowed_models = Db::name('channeltype')->column('ctl_name');
        return in_array($ctl_name, $allowed_models);
    }

    public function multistation()
    {
        // 增加令牌验证
        $token = input('param.token/s');
        if (empty($token) || !is_string($token) || strlen($token) < 100 || strlen($token) > 2048 || !preg_match('/^[A-Za-z0-9\+\/\=]{100,}$/', $token)) {
            $this->error('无效的令牌');
        }

        $auth_code = msubstr($token, 6, 32);
        $request_time = msubstr($token, 38, 10);
        $querystr = msubstr($token, 48, strlen($token));
    
        // 时间戳验证，防止重放攻击
        if (abs(time() - intval($request_time)) > 1800) {
            $this->error('令牌已过期');
        }

        // 解码参数
        $querystr = mchStrCode($querystr, 'DECODE', $auth_code);
        if (!$querystr) {
            $this->error('令牌解析失败');
        }

        // 验证内容格式
        if (!preg_match('/^(.*)-#eyou#([\w\-]+)#cms#-(.*)$/i', $querystr)) {
            $this->error('非法的访问请求');
        }

        $verify_content = preg_replace('/^(.*)-#eyou#([\w\-]+)#cms#-(.*)$/i', '${2}', $querystr);
        $file = ROOT_PATH . "EY_{$verify_content}.txt";
        $_ajax = input('param._ajax/d');

        if (!is_file($file)) {
            if (empty($_ajax)) {
                $this->error('验证文件不存在，请重新操作');
            }
            $data = [
                'code' => 0,
                'msg'  => '验证文件不存在，请重新按照教程操作',
            ];
            respose($data);
        }

        $file_size = filesize($file);
        if (empty($file_size) || $file_size > 1024) {
            if (empty($_ajax)) {
                $this->error('验证文件失败，请重新按照教程操作');
            }
            $data = [
                'code' => 0,
                'msg'  => '验证文件失败，请重新按照教程操作',
            ];
            respose($data);
        }
        $fp = fopen($file, 'r');
        $file_content = fread($fp, $file_size);
        fclose($fp);
        $file_content = is_string($file_content) ? trim($file_content) : '';

        if (empty($file_content) || !hash_equals(strval($file_content), strval($verify_content))) {
            if (empty($_ajax)) {
                $this->error('验证文件失败，请重新按照教程操作');
            }
            $data = [
                'code' => 0,
                'msg'  => '验证文件失败，请重新按照教程操作',
            ];
            respose($data);
        }

        $arr = explode("-#eyou#{$verify_content}#cms#-", $querystr);
        $user_name = empty($arr[0]) ? '' : mchStrCode($arr[0], 'DECODE', $auth_code);
        $password = empty($arr[1]) ? '' : mchStrCode($arr[1], 'DECODE', $auth_code);
        $user_name = is_string($user_name) ? $user_name : '';
        $password = is_string($password) ? $password : '';

        // 安全修复：站群快捷登录纳入后台登录失败锁定，避免该接口被当作无验证码、无限频的密码爆破入口
        $clientIP = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        $loginLimitConf = $this->multistation_login_limit_conf();
        $login_errnum_key = 'adminlogin_'.md5('login_errnum_'.$user_name.$clientIP);
        $login_errtime_key = 'adminlogin_'.md5('login_errtime_'.$user_name.$clientIP);
        $login_lock_key = 'adminlogin_'.md5('login_lock_'.$user_name.$clientIP);
        if (!empty($loginLimitConf['open'])) {
            $loginErrnum = (int)tpSetting('adminlogin.'.$login_errnum_key);
            $loginErrtime = (int)tpSetting('adminlogin.'.$login_errtime_key);
            if ($loginErrnum >= $loginLimitConf['total'] && getTime() < $loginErrtime + $loginLimitConf['expire']) {
                $surplus_time = ($loginErrtime + $loginLimitConf['expire']) - getTime();
                $surplus_time = $surplus_time > 0 ? $surplus_time : 1;
                $this->error('多次登录失败，距离解锁还有'.ceil($surplus_time/60).'分钟！');
            }
        }

        $admin_info = Db::name('admin')->where(['user_name'=>$user_name])->find();
        if (empty($admin_info)) {
            $this->multistation_login_fail($login_errnum_key, $login_errtime_key, $login_lock_key, $loginLimitConf);
            $this->error('登录失败(账号/密码错误)');
        }
        $password = func_encrypt($password, true, pwd_encry_type($admin_info['password']));
        if ($password != $admin_info['password']) {
            $this->multistation_login_fail($login_errnum_key, $login_errtime_key, $login_lock_key, $loginLimitConf);
            $this->error('登录失败(账号/密码错误)');
        } else if (empty($admin_info['status'])) {
            $this->error('登录失败(账号已禁用)');
        }

        // 登录成功，清空失败计数
        if (!empty($loginLimitConf['open'])) {
            tpSetting('adminlogin', [
                $login_errnum_key => 0,
                $login_errtime_key => 0,
                $login_lock_key => 0,
            ]);
        }

        $opt = input('param.opt/s', '', 'trim,strip_tags');
        // 防止SQL注入
        if (!in_array($opt, ['push_archives', 'edit_archives', 'del_archives', ''])) {
            $this->error('非法的操作参数');
        }

        if ('push_archives' == $opt) { // 群发文档
            $this->multistation_push_archives('add');
        } else if ('edit_archives' == $opt) { // 编辑文档
            $this->multistation_push_archives('edit');
        } else if ('del_archives' == $opt) { // 删除文档
            $this->multistation_del_archives();
        } else { // 登录
            $third_type = '';
            $thirdata = login_third_type();
            if ('EyouGzhLogin' == $thirdata['type']) {
                $openid = Db::name('admin_wxlogin')->where(['admin_id'=>$admin_info['admin_id'], 'type'=>1])->value('openid');
                if (!empty($openid)) {
                    $third_type = 'EyouGzhLogin';
                }
            } else if ('WechatLogin' == $thirdata['type']) {
                $openid = Db::name('admin_wxlogin')->where(['admin_id'=>$admin_info['admin_id'], 'type'=>2])->value('openid');
                if (!empty($openid)) {
                    $third_type = 'WechatLogin';
                }
            }

            // 记录登录IP和时间
            $update_data = [
                'last_login' => getTime(),
                'last_ip' => $this->request->ip()
            ];
            Db::name('admin')->where(['admin_id'=>$admin_info['admin_id']])->update($update_data);

            $admin_info = adminLoginAfter($admin_info['admin_id'], $this->session_id, $third_type);
            adminLog('后台登录(站群快捷管理)');
            session('isset_author', null); // 内置勿动

            $web_adminbasefile = tpCache('global.web_adminbasefile');
            $web_adminbasefile = !empty($web_adminbasefile) ? $web_adminbasefile : $this->root_dir.'/login.php';
            if (stristr($web_adminbasefile, 'index.php')) {
                $baseFile = explode('/', request()->baseFile());
                $web_adminbasefile = end($baseFile);
                $web_adminbasefile = $this->root_dir.'/'.$web_adminbasefile;
            }
            $this->success('正在登录', $web_adminbasefile);
        }
        abort(404);
    }

    /**
     * 站群快捷登录：后台登录失败锁定配置
     * @return array
     */
    private function multistation_login_limit_conf()
    {
        $globalConfig = tpCache('global');
        // 与后台登录页保持一致：仅明确设置为0才关闭锁定
        $open = !(isset($globalConfig['web_login_lockopen']) && empty($globalConfig['web_login_lockopen']));
        $total = !empty($globalConfig['web_login_errtotal']) ? intval($globalConfig['web_login_errtotal']) : 3;
        $expire = !empty($globalConfig['web_login_errexpire']) ? intval($globalConfig['web_login_errexpire']) : 1800;
        return [
            'open'   => $open ? 1 : 0,
            'total'  => $total > 0 ? $total : 3,
            'expire' => $expire > 0 ? $expire : 1800,
        ];
    }

    /**
     * 站群快捷登录：记录一次登录失败
     * @param string $errnumKey 错误次数键
     * @param string $errtimeKey 最后错误时间键
     * @param string $lockKey 锁定标记键
     * @param array  $conf 限流配置
     */
    private function multistation_login_fail($errnumKey, $errtimeKey, $lockKey, $conf = [])
    {
        if (empty($conf['open'])) {
            return;
        }
        $errnum = (int)tpSetting('adminlogin.' . $errnumKey) + 1;
        $data = [
            $errnumKey  => $errnum,
            $errtimeKey => getTime(),
        ];
        if ($errnum >= $conf['total']) {
            $data[$lockKey] = 1;
            $clientIP = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
            adminLog("站群快捷登录错误次数过多，账号被锁定: IP({$clientIP})");
        }
        tpSetting('adminlogin', $data);
    }

    private function multistation_push_archives($opt = 'add')
    {
        $data = [
            'push_code' => 0,
            'push_msg'  => '发布失败',
        ];
        $post = input('post.');
        $archivesInfo = empty($post['archives']) ? '' : json_decode(base64_decode($post['archives']), true);
        
        // 安全处理远程图片
        if (!empty($archivesInfo['litpic']) && !empty($archivesInfo['is_syn_local'])) {
            // 远程图片保存
            $ret_litpic = saveRemote($archivesInfo['litpic'],'allimg');
            $ret_litpic = json_decode($ret_litpic,true);
            $archivesInfo['litpic'] = empty($ret_litpic['url']) ? $archivesInfo['litpic'] : $ret_litpic['url'];
        }
        $archivesInfo['update_time'] = getTime();

        // 验证栏目权限
        $arctypeInfo = Db::name('arctype')->where(['id'=>intval($archivesInfo['typeid'])])->find();
        if (empty($arctypeInfo)) {
            $data['push_msg'] = '网站栏目不存在';
        }
        $channeltypeInfo = Db::name('channeltype')->where(['id'=>$archivesInfo['channel']])->find();
        $arctypeInfo['typeurl'] = typeurl("home/{$channeltypeInfo['ctl_name']}/lists", $arctypeInfo, true, true);
        if ('add' == $opt) {
            // 新增时不允许由请求方指定主键
            unset($archivesInfo['aid']);
            $archivesInfo['add_time'] = getTime();
            $r = $aid = Db::name('archives')->insertGetId($archivesInfo);
        } else {
            $aid = (isset($archivesInfo['aid']) && is_scalar($archivesInfo['aid'])) ? intval($archivesInfo['aid']) : 0;
            if (empty($aid)) {
                respose($data);
            }
            $archivesInfo['aid'] = $aid;
            $r = Db::name('archives')->where(['aid' => $aid])->update($archivesInfo);
        }
        if ($r !== false) {
            //内容远程图片本地化
            if (!empty($archivesInfo['is_syn_local'])) {
                $archivesInfo = $this->content_remote_to_local($archivesInfo);
            }

            $ctl_name = $channeltypeInfo['ctl_name'];
            // 安全验证：验证模型名称
            if (!$this->validateCtlName($ctl_name)) {
                $data['push_msg'] = '无效的模型类型';
                respose($data);
                return;
            }
            $class     = "\\app\\admin\\model\\{$ctl_name}";
            $model     = new $class;
            try {
                $archivesInfo['aid'] = $aid;
                $archivesInfo['arcurl'] = arcurl("home/{$channeltypeInfo['ctl_name']}/view", array_merge($arctypeInfo, $archivesInfo), true, true);
                if (!empty($archivesInfo['articlePayInfo'])) {
                    $archivesInfo['articlePayInfo']['aid'] = $aid;
                    if ('add' == $opt) {
                        Db::name('article_pay')->insert($archivesInfo['articlePayInfo']);
                    } else {
                        $is_in = Db::name('article_pay')->where('aid',$aid)->find();
                        if (empty($is_in)){
                            Db::name('article_pay')->insert($archivesInfo['articlePayInfo']);
                        }else{
                            $archivesInfo['articlePayInfo']['update_time'] = getTime();
                            Db::name('article_pay')->where('aid',$aid)->update($archivesInfo['articlePayInfo']);
                        }
                    }
                }
                $model->afterSave($aid, $archivesInfo, $opt);
                // 清除前台缓存
                clearHtmlCache([$aid], [$arctypeInfo['id']]);
                // 添加查询执行语句到mysql缓存表
                model('SqlCacheTable')->InsertSqlCacheTable();
                if ('add' == $opt) {
                    $push_msg = '发布成功';
                    adminLog('群发新增文档：'.$archivesInfo['title']);
                } else {
                    $push_msg = '编辑成功';
                    adminLog('群发编辑文档：'.$archivesInfo['title']);
                }
                $globalConfig = tpCache('global');
                $data = [
                    'push_code' => 1,
                    'push_msg'  => $push_msg,
                    'arctypeInfo' => [
                        'typeurl' => $arctypeInfo['typeurl'],
                        'typename' => $arctypeInfo['typename'],
                    ],
                    'archivesInfo' => [
                        'aid' => $archivesInfo['aid'],
                        'arcurl' => $archivesInfo['arcurl'],
                    ],
                    'globalConfig' => [
                        'seo_pseudo' => $globalConfig['seo_pseudo'],
                    ],
                ];
            } catch (\Exception $e) {
                Db::name('archives')->where(['aid'=>$aid])->delete();
                $model->afterDel([$aid]);
                $data['push_msg'] = $e->getMessage();
            }
        }
        respose($data);
    }

    //内容远程图片本地化
    private function content_remote_to_local($archivesInfo = []){
        foreach (['content','content_ey_m','free_content'] as $k => $v){
            $first = 'addonFieldExt';
            if ('free_content' == $v){
                $first = 'articlePayInfo';
            }
            $archivesInfo[$first][$v] = htmlspecialchars_decode($archivesInfo[$first][$v]);
            $archivesInfo[$first][$v] = remote_to_local($archivesInfo[$first][$v]);
            $archivesInfo[$first][$v] = htmlspecialchars($archivesInfo[$first][$v]);
        }
        return $archivesInfo;
    }

    private function multistation_del_archives()
    {
        $data = [
            'push_code' => 0,
            'push_msg'  => '删除失败',
        ];
        $post = input('post.');
        $aid = (isset($post['aid']) && is_scalar($post['aid'])) ? intval($post['aid']) : 0;
        if (!empty($aid)) {
            try {
                $archivesInfo = Db::name('archives')->where(['aid'=>$aid])->find();
                $channeltypeInfo = Db::name('channeltype')->where(['id'=>$archivesInfo['channel']])->find();
                $ctl_name = $channeltypeInfo['ctl_name'];
                // 安全验证：验证模型名称
                if (!$this->validateCtlName($ctl_name)) {
                    $data['push_msg'] = '无效的模型类型';
                    respose($data);
                    return;
                }
                $class     = "\\app\\admin\\model\\{$ctl_name}";
                $model     = new $class;
                Db::name('archives')->where(['aid'=>$aid])->delete();
                $model->afterDel([$aid]);
                adminLog('群发删除文档：'.$archivesInfo['title']);
                $data = [
                    'push_code' => 1,
                    'push_msg'  => '删除成功',
                ];
            } catch (\Exception $e) {
                $data['push_msg'] = $e->getMessage();
            }
        }
        respose($data);
    }

    /**
     * 清除缓存接口
     * @return [type] [description]
     */
    public function clear_cache()
    {
        // 安全修复：该接口仅用于服务器定时任务，必须携带由站点安装序列号生成的签名 token，
        // 防止匿名请求反复清理缓存导致站点性能下降
        $token = input('param.token/s', '');
        $token = is_string($token) ? $token : '';
        if (empty($token) || !hash_equals(get_clear_cache_token(), $token)) {
            abort(404);
        }
        \think\Cache::clear();
        delFile(RUNTIME_PATH);
        exit('success');
    }

    /**
     * 获取下级地区
     */
    public function get_region()
    {
        if (IS_AJAX) {
            $pid = input('pid/d', 0);
            $res = Db::name('region')->where('parent_id', $pid)->select();
            $this->success('请求成功', null, $res);
        }
    }

    /**
     * 浏览计数限频：同一 IP 对同一文档 60 秒内只计一次
     */
    private function arcclickAllow($aid)
    {
        $aid = intval($aid);
        if ($aid <= 0) return false;
        $cacheKey = 'arcclick_' . md5(clientIP() . '_' . $aid);
        if (cache($cacheKey)) return false;
        cache($cacheKey, 1, 60);
        return true;
    }

    public function arcclick()
    {
        if (!IS_AJAX) {
            // 第一种方案，js输出
            $aids = input('param.aids/d', 0);
            if (!empty($aids)) {
                $type = input('param.type/s', '');
                $archives_db = Db::name('archives');
                if ('view' == $type && $this->arcclickAllow($aids)) {
                    $archives_db->where(['aid'=>['IN', $aids]])->setInc('click');
                    eyou_statistics_data(1); // 统计浏览数
                }
                $click = $archives_db->where(['aid'=>['IN', $aids]])->value('click');
                echo "document.write('" . $click . "');\r\n";
                exit;
            }
        } else {
            // 第二种方案，执行ajax
            $param = input('param.');
            if (isset($param['aids'])) {
                $aids = $param['aids'];
                if (!empty($aids)) {
                    $aid_arr = explode(',', $aids);
                    $aid_arr = array_slice(array_unique(array_filter(array_map('intval', $aid_arr))), 0, 50); // 去重并限制单次最多 50 个
                    $type = input('param.type/s', '');
                    $archives_db = Db::name('archives');
                    if ('view' == $type && !empty($aid_arr)) {
                        // 仅对未在限频窗口内的文档计数，防止刷浏览量
                        $allow_arr = [];
                        foreach ($aid_arr as $aid_item) {
                            if ($this->arcclickAllow($aid_item)) $allow_arr[] = $aid_item;
                        }
                        if (!empty($allow_arr)) {
                            $archives_db->where(['aid' => ['IN', $allow_arr]])->update([
                                'click' => Db::raw('click + 1'),
                            ]);
                            eyou_statistics_data(1, count($allow_arr)); // 统计浏览数
                        }
                    }
                    $data = $archives_db->field('aid,click')->where(['aid' => ['IN', $aid_arr]])->getAllWithIndex('aid');
                    respose($data);
                }
            } else {
                $click = 0;
                $aid = input('param.aid/d', 0);
                $type = input('param.type/s', '');
                if ($aid > 0) {
                    $archives_db = Db::name('archives');
                    if ('view' == $type && $this->arcclickAllow($aid)) {
                        $archives_db->where(['aid' => $aid])->setInc('click');
                        eyou_statistics_data(1); // 统计浏览数
                    }
                    $click = $archives_db->where(['aid' => $aid])->getField('click');
                }
                echo($click);
                exit;
            }
        }
        abort(404);
    }

    /**
     * 付费文档的订单数/用户数
     */
    public function freebuynum()
    {
        $aid = input('param.aid/d', 0);
        if (IS_AJAX && !empty($aid)) {
            $freebuynum = 0;
            $modelid = input('modelid/d', 0);
            $modelid = input('channelid/d', $modelid);

            if (empty($modelid)) {
                $modelid = Db::name('archives')->where(['aid' => $aid])->value('channel');
            }

            if (1 == $modelid) {
                $freebuynum = Db::name('article_order')->where(['product_id' => $aid, 'order_status' => 1])->count();
            } else if (5 == $modelid) {
                $freebuynum = Db::name('media_order')->where(['product_id' => $aid, 'order_status' => 1])->count();
            } else if (4 == $modelid) {
                $freebuynum = Db::name('download_order')->where(['product_id' => $aid, 'order_status' => 1])->count();
            }

            echo($freebuynum);
            exit;
        } else {
            abort(404);
        }
    }

    /**
     * 文档下载次数
     */
    public function downcount()
    {
        $aid = input('param.aid/d', 0);
        if (IS_AJAX && !empty($aid)) {
            $downcount = Db::name('archives')->where(array('aid' => $aid))->getField('downcount');
            echo($downcount);
            exit;
        } else {
            abort(404);
        }
    }

    /**
     * 文档收藏次数
     */
    public function collectnum()
    {
        $aid = input('param.aid/d', 0);
        if (IS_AJAX && !empty($aid)) {
            $collectnum = Db::name('users_collection')->where(array('aid' => $aid))->count();
            echo($collectnum);
            exit;
        } else {
            abort(404);
        }
    }

    /**
     * 站内通知数量
     */
    public function notice()
    {
        if (IS_AJAX) {
            $unread_notice_num = 0;
            $users_id = session('users_id');
            if ($users_id > 0) {
                $unread_notice_num = Db::name('users')->where(array('users_id' => $users_id))->value('unread_notice_num');
            }
            echo($unread_notice_num);
            exit;
        } else {
            abort(404);
        }
    }

    /**
     * arclist列表分页arcpagelist标签接口
     */
    public function arcpagelist()
    {
        if (!IS_AJAX) {
            abort(404);
        }

        $pnum = input('page/d', 0);
        $pagesize = input('pagesize/d', 0);
        $tagid = input('tagid/s', '');
        $tagidmd5 = input('tagidmd5/s', '');
        !empty($tagid) && $tagid = preg_replace("/[^a-zA-Z0-9-_]/", '', $tagid);
        !empty($tagidmd5) && $tagidmd5 = preg_replace("/[^a-zA-Z0-9_]/", '', $tagidmd5);

        if (empty($tagid) || empty($pnum) || empty($tagidmd5)) {
            $this->error('参数有误');
        }

        $data = [
            'code' => 1,
            'msg' => '',
            'lastpage' => 0,
        ];

        $arcmulti_db = Db::name('arcmulti');
        $arcmultiRow = $arcmulti_db->where(['tagid' => $tagidmd5])->find();
        if (!empty($arcmultiRow) && !empty($arcmultiRow['querysql'])) {
            // arcpagelist标签属性pagesize优先级高于arclist标签属性pagesize
            if (0 < intval($pagesize)) {
                $arcmultiRow['pagesize'] = $pagesize;
            }

            // 取出属性并解析为变量
            $attarray = json_decode($arcmultiRow['attstr'], true);
            // extract($attarray, EXTR_SKIP); // 把数组中的键名直接注册为了变量

            // 通过页面及总数解析当前页面数据范围
            $pnum < 2 && $pnum = 2;
            $strnum = intval($attarray['row']) + ($pnum - 2) * $arcmultiRow['pagesize'];

            // 拼接完整的SQL
            $querysql = preg_replace('#LIMIT(\s+)(\d+)(,\d+)?#i', '', $arcmultiRow['querysql']);
            $querysql = preg_replace('#SELECT(\s+)(.*)(\s+)FROM#i', 'SELECT COUNT(*) AS totalNum FROM', $querysql);
            $queryRow = Db::query($querysql);
            if (!empty($queryRow)) {
                $tpl_content = '';
                $filename = './template/' . THEME_STYLE_PATH . '/' . 'system/arclist_' . $tagid . '.' . \think\Config::get('template.view_suffix');
                if (!file_exists($filename)) {
                    $data['code'] = -1;
                    $data['msg'] = "模板追加文件 arclist_{$tagid}.htm 不存在！";
                    $this->error("标签模板不存在", null, $data);
                } else {
                    $tpl_content = @file_get_contents($filename);
                }
                if (empty($tpl_content)) {
                    $data['code'] = -1;
                    $data['msg'] = "模板追加文件 arclist_{$tagid}.htm 没有HTML代码！";
                    $this->error("标签模板不存在", null, $data);
                }

                /*拼接完整的arclist标签语法*/
                $offset = intval($strnum);
                $row = intval($offset) + intval($arcmultiRow['pagesize']);
                $innertext = "{eyou:arclist";
                foreach ($attarray as $key => $val) {
                    if (in_array($key, ['tagid', 'offset', 'row'])) {
                        continue;
                    }
                    // 属性名白名单，防止数据库中的异常属性名破坏标签语法
                    if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_\-]*$/', $key)) {
                        continue;
                    }
                    if ($key == 'keyword') {
                        if (empty($val)) {
                            continue;
                        } else if (preg_match('/^\$eyou(\.|\[)(.*)$/i', $val)) {
                            $val = input('param.keywords/s');
                            // 白名单：剔除会闭合属性或注入标签语法的字符，防止模板注入
                            $val = is_string($val) ? $val : '';
                            $val = str_replace(array("'", '"', '{', '}', '\\', '<', '>', "\r", "\n", "\t"), '', $val);
                            $val = msubstr($val, 0, 100, false);
                        }
                    }
                    $innertext .= " {$key}='{$val}'";
                }
                if($tagid){
                   $innertext .= " tagid='{$tagid}'";
                }
                $innertext .= " limit='{$offset},{$row}'}";
                $innertext .= $tpl_content;
                $innertext .= "{/eyou:arclist}";
                /*--end*/
                $msg = $this->display($innertext); // 渲染模板标签语法
                $data['msg'] = $msg;

                //是否到了最终页
                if (!empty($queryRow[0]['totalNum']) && $queryRow[0]['totalNum'] <= $row) {
                    $data['lastpage'] = 1;
                }

            } else {
                $data['lastpage'] = 1;
            }
        }

        $this->success('请求成功', null, $data);
    }

    /**
     * 获取表单令牌
     */
    public function get_token($name = '__token__')
    {
        $name = preg_replace('/([^\w\-]+)/i', '', $name);
        if (IS_AJAX && strstr($name, '_token_')) {
            echo $this->request->token($name);
            exit;
        } else {
            abort(404);
        }
    }

    /**
     * 检验会员登录
     */
    public function check_user()
    {
        if (IS_AJAX) {
            $type = input('param.type/s', 'default');
            $img = input('param.img/s');
            $afterhtml = input('param.afterhtml/s');
            $users_id = session('users_id');
            if ('login' == $type) {
                if (!empty($users_id)) {
                    $currentstyle = input('param.currentstyle/s');
                    $users = M('users')->field('username,nickname,head_pic,sex')
                        ->where([
                            'users_id' => $users_id,
                            'lang' => $this->home_lang,
                        ])->find();
                    if (!empty($users)) {
                        $nickname = $users['nickname'];
                        if (empty($nickname)) {
                            $nickname = $users['username'];
                        }
                        $head_pic = get_head_pic(htmlspecialchars_decode($users['head_pic']), false, $users['sex']);
                        $users['head_pic'] = func_preg_replace(['http://thirdqq.qlogo.cn'], ['https://thirdqq.qlogo.cn'], $head_pic);
                        if (!empty($afterhtml)) {
                            preg_match_all('/~(\w+)~/iUs', $afterhtml, $userfields);
                            if (!empty($userfields[1])) {
                                $users['url'] = url('user/Users/login');
                                foreach ($userfields[1] as $key => $val) {
                                    $replacement = !empty($users[$val]) ? $users[$val] : '';
                                    $afterhtml = str_replace($userfields[0][$key], $users[$val], $afterhtml);
                                }
                                $users['html'] = htmlspecialchars_decode($afterhtml);
                            } else {
                                $users['html'] = $nickname;
                            }
                        } else {
                            if ('on' == $img) {
                                $users['html'] = "<img class='{$currentstyle}' alt='{$nickname}' src='{$users['head_pic']}' />";
                            } else {
                                $users['html'] = $nickname;
                            }
                        }
                        $users['ey_is_login'] = 1;
                        cookie('users_id', $users_id);
                        $this->success('请求成功', null, $users);
                    }
                }

                $data = [
                    'ey_is_login' => 0,
                    'ey_third_party_login' => $this->is_third_party_login(),
                    'ey_third_party_qqlogin' => $this->is_third_party_login('qq'),
                    'ey_third_party_wxlogin' => $this->is_third_party_login('wx'),
                    'ey_third_party_wblogin' => $this->is_third_party_login('wb'),
                    'ey_login_vertify' => $this->is_login_vertify(),
                ];
                $this->success('请先登录', null, $data);
            } else if ('reg' == $type) {
                if (!empty($users_id)) {
                    $users['ey_is_login'] = 1;
                } else {
                    $users['ey_is_login'] = 0;
                }
                $this->success('请求成功', null, $users);
            } else if ('logout' == $type) {
                if (!empty($users_id)) {
                    $users['ey_is_login'] = 1;
                } else {
                    $users['ey_is_login'] = 0;
                }
                $this->success('请求成功', null, $users);
            } else if ('cart' == $type) {
                if (!empty($users_id)) {
                    $users['ey_is_login'] = 1;
                    $users['ey_cart_num_20191212'] = Db::name('shop_cart')->where(['users_id' => $users_id])->sum('product_num');
                } else {
                    $users['ey_is_login'] = 0;
                    $users['ey_cart_num_20191212'] = 0;
                }
                $this->success('请求成功', null, $users);
            } else if ('collect' == $type) {
                if (!empty($users_id)) {
                    $users['ey_is_login'] = 1;
                    $users['ey_collect_num_20191212'] = Db::name('users_collection')->where(['users_id' => $users_id])->count();
                } else {
                    $users['ey_is_login'] = 0;
                    $users['ey_collect_num_20191212'] = 0;
                }
                $this->success('请求成功', null, $users);
            }
            $this->error('访问错误');
        } else {
            abort(404);
        }
    }

    public function get_info()
    {
        $str = '5piT5LyYQ01TLQ==';
        exit(base64_decode($str) . getCmsVersion());
    }

    /**
     * 是否启用并开启第三方登录
     * @return boolean [description]
     */
    private function is_third_party_login($type = '')
    {
        static $result = null;
        if (null === $result) {
            $result = Db::name('weapp')->field('id,code,data')->where([
                'code' => ['IN', ['QqLogin', 'WxLogin', 'Wblogin']],
                'status' => 1,
            ])->getAllWithIndex('code');
        }
        $value = 0;
        if (empty($type)) {
            $qqlogin = 0;
            if (!empty($result['QqLogin']['data'])) {
                $qqData = safe_unserialize($result['QqLogin']['data']);
                if (!empty($qqData['login_show'])) {
                    $qqlogin = 1;
                }
            }

            $wxlogin = 0;
            if (!empty($result['WxLogin']['data'])) {
                $wxData = safe_unserialize($result['WxLogin']['data']);
                if (!empty($wxData['login_show'])) {
                    $wxlogin = 1;
                }
            }

            $wblogin = 0;
            if (!empty($result['Wblogin']['data'])) {
                $wbData = safe_unserialize($result['Wblogin']['data']);
                if (!empty($wbData['login_show'])) {
                    $wblogin = 1;
                }
            }

            if ($qqlogin == 1 || $wxlogin == 1 || $wblogin == 1) {
                $value = 1;
            }
        } else {
            if ('qq' == $type) {
                if (!empty($result['QqLogin']['data'])) {
                    $qqData = safe_unserialize($result['QqLogin']['data']);
                    if (!empty($qqData['login_show'])) {
                        $value = 1;
                    }
                }
            } else if ('wx' == $type) {
                if (!empty($result['WxLogin']['data'])) {
                    $wxData = safe_unserialize($result['WxLogin']['data']);
                    if (!empty($wxData['login_show'])) {
                        $value = 1;
                    }
                }
            } else if ('wb' == $type) {
                if (!empty($result['Wblogin']['data'])) {
                    $wbData = safe_unserialize($result['Wblogin']['data']);
                    if (!empty($wbData['login_show'])) {
                        $value = 1;
                    }
                }
            }
        }

        return $value;
    }

    /**
     * 是否开启登录图形验证码
     * @return boolean [description]
     */
    private function is_login_vertify()
    {
        $row = tpSetting('system.system_vertify', [], 'cn');
        // 获取验证码配置信息
        $row = json_decode($row, true);
        $baseConfig = \think\Config::get("captcha");
        if (!empty($row)) {
            foreach ($row['captcha'] as $key => $val) {
                if ('default' == $key) {
                    $baseConfig[$key] = array_merge($baseConfig[$key], $val);
                } else {
                    $baseConfig[$key]['is_on'] = $val['is_on'];
                    $baseConfig[$key]['config'] = array_merge($baseConfig['default'], $val['config']);
                }
            }
            \think\Config::set('captcha', $baseConfig);
        }

        // 默认开启验证码
        $is_vertify = 1;
        $users_login_captcha = empty($baseConfig['users_login']) ? [] : $baseConfig['users_login'];
        if (!function_exists('imagettftext') || empty($users_login_captcha['is_on'])) {
            $is_vertify = 0; // 函数不存在，不符合开启的条件
        }

        return $is_vertify;
    }

    /**
     * 获取用户信息
     */
    public function get_tag_user_info()
    {
        if (!IS_AJAX) {
            abort(404);
        }

        $t_uniqid = input('param.t_uniqid/s', '');
        if (IS_AJAX && !empty($t_uniqid)) {
            $users_id = session('users_id');
            if (!empty($users_id)) {
                $users = Db::name('users')->field('b.*, a.*')
                    ->alias('a')
                    ->join('__USERS_LEVEL__ b', 'a.level = b.level_id', 'LEFT')
                    ->where([
                        'a.users_id' => $users_id,
                        'a.lang' => $this->home_lang,
                    ])->find();
                if (!empty($users)) {
                    $users['reg_time'] = MyDate('Y-m-d H:i:s', $users['reg_time']);
                    $users['update_time'] = MyDate('Y-m-d H:i:s', $users['update_time']);
                } else {
                    $users = [];
                    $tableFields1 = Db::name('users')->getTableFields();
                    $tableFields2 = Db::name('users_level')->getTableFields();
                    $tableFields = array_merge($tableFields1, $tableFields2);
                    foreach ($tableFields as $key => $val) {
                        $users[$val] = '';
                    }
                }
                unset($users['password']);
                unset($users['paypwd']);
                // 头像处理
                $head_pic = get_head_pic(htmlspecialchars_decode($users['head_pic']), false, $users['sex']);
                $users['head_pic'] = func_preg_replace(['http://thirdqq.qlogo.cn'], ['https://thirdqq.qlogo.cn'], $head_pic);
                $users['url'] = url('user/Users/centre');
                $dtypes = [];
                foreach ($users as $key => $val) {
                    $html_key = md5($key . '-' . $t_uniqid);
                    $users[$html_key] = $val;

                    $dtype = 'txt';
                    if (in_array($key, ['head_pic'])) {
                        $dtype = 'img';
                    } else if (in_array($key, ['url'])) {
                        $dtype = 'href';
                    }
                    $dtypes[$html_key] = $dtype;

                    unset($users[$key]);
                }

                $data = [
                    'ey_is_login' => 1,
                    'users' => $users,
                    'dtypes' => $dtypes,
                ];
                $this->success('请求成功', null, $data);
            }
            $this->success('请先登录', null, ['ey_is_login' => 0]);
        }
        $this->error('访问错误');
    }

    // 验证码获取
    public function vertify()
    {
        $time = getTime();
        $type = input('param.type/s', 'default');
        $type = preg_replace('/([^\w\-]+)/i', '', $type);
        $token = input('param.token/s', '');
        $token = preg_replace('/([^\w\-]+)/i', '', $token);
        $configList = \think\Config::get('captcha');
        $captchaArr = array_keys($configList);
        if (in_array($type, $captchaArr)) {
            /*验证码插件开关*/
            $admin_login_captcha = config('captcha.' . $type);
            $config = (!empty($admin_login_captcha['is_on']) && !empty($admin_login_captcha['config'])) ? $admin_login_captcha['config'] : config('captcha.default');
            /*--end*/
        } else {
            $config = config('captcha.default');
        }

        ob_clean(); // 清空缓存，才能显示验证码
        $Verify = new \think\Verify($config);
        if (!empty($token)) {
            $Verify->entry($token);
        } else {
            $Verify->entry($type);
        }
        exit();
    }
    
    /**
     * 表单提交完成之后操作----邮箱发送
     */
    public function send_email()
    {
        // 超时后，断掉邮件发送
        function_exists('set_time_limit') && set_time_limit(10);
        \think\Session::pause(); // 暂停session，防止session阻塞机制

        $type = input('param.type/s');

        if (IS_AJAX_POST) {
            // 留言发送邮件
            if ('gbook_submit' == $type) {
                // 是否满足发送邮箱的条件
                $is_open = Db::name('smtp_tpl')->where(['send_scene' => 1, 'lang' => $this->home_lang])->value('is_open');
                $smtp_config = tpCache('smtp', [], $this->home_lang);
                if (empty($is_open) || empty($smtp_config['smtp_user']) || empty($smtp_config['smtp_pwd']) || empty($smtp_config['smtp_from_eamil'])) {
                    $this->error("邮箱尚未配置，发送失败");
                }

                $tid = input('param.tid/d');
                $aid = input('param.aid/d');
                $form_type = input('param.form_type/d', 0);

                $send_email_scene = config('send_email_scene');
                $scene = $send_email_scene[1]['scene'];

                if (1 == $form_type) {
                    $info = Db::name('guestbook')->field('a.*, b.form_name')
                        ->alias('a')
                        ->join('form b','a.typeid = b.form_id','left')
                        ->where(['a.aid'=>$aid, 'a.form_type'=>$form_type])
                        ->find();
                } else {
                    $info = Db::name('guestbook')->field('a.*, b.typename as form_name')
                        ->alias('a')
                        ->join('arctype b','a.typeid = b.id','left')
                        ->where(['a.aid'=>$aid, 'a.form_type'=>$form_type])
                        ->find();
                }
                $city = "";
                try {
                    $city_arr = getCityLocation($info['ip']);
                    if (!empty($city_arr)) {
                        !empty($city_arr['location']) && $city .= $city_arr['location'];
                    }
                } catch (\Exception $e) {}
                $info['city'] = $city;

                // 判断标题拼接
                $web_name = tpCache('web.web_name');
                $web_name = $info['form_name'] . '-' . $web_name;

                // 拼装发送的字符串内容
                $attr_list = Db::name('guestbook_attribute')->where(['typeid'=>$tid,'form_type'=>$form_type,'is_del'=>0])->order('sort_order asc, attr_id asc')->select();
                $attr_values = Db::name('guestbook_attr')->field('attr_id,attr_value')->where(['aid'=>$aid,'form_type'=>$form_type])->getAllWithIndex('attr_id');
                foreach ($attr_list as $key => $val) {
                    $val['attr_value'] = empty($attr_values[$val['attr_id']]) ? '' : $attr_values[$val['attr_id']]['attr_value'];
                    $attr_list[$key] = $val;
                }
                $content = '';
                foreach ($attr_list as $key => $val) {
                    if ($val['attr_input_type'] == 9) {
                        $val['attr_value'] = Db::name('region')->where('id', 'in', $val['attr_value'])->column('name');
                        $val['attr_value'] = implode('', $val['attr_value']);
                    } else if ($val['attr_input_type'] == 4) {
                        $val['attr_value'] = filter_line_return($val['attr_value'], '、');
                    } else if (5 == $val['attr_input_type']) {//单张图
                        $val['attr_value'] = handle_subdir_pic($val['attr_value'], 'img', true);
                        $val['attr_value'] = "<a href='{$val['attr_value']}' target='_blank'><img src='{$val['attr_value']}' width='60' height='60' style='float: unset;cursor: pointer;' /></a>";
                    } else if (10 == $val['attr_input_type']) {//时间类型
                        $val['attr_value'] = date('Y-m-d H:i:s', $val['attr_value']);
                    } else if (11 == $val['attr_input_type']) {//多张图
                        $attr_value_arr = explode(",", $val['attr_value']);
                        $attr_value_str = "";
                        foreach ($attr_value_arr as $attr_value_k => $attr_value_v) {
                            $attr_value_v = handle_subdir_pic($attr_value_v, 'img', true);
                            $attr_value_str .= "<a href='{$attr_value_v}' target='_blank'><img src='{$attr_value_v}' width='60' height='60' style='float: unset;cursor: pointer;' /></a>";
                        }
                        $val['attr_value'] = $attr_value_str;
                    } else {
                        if (preg_match('/(\.(jpg|gif|png|bmp|jpeg|ico|webp))$/i', $val['attr_value'])) {
                            if (!stristr($val['attr_value'], '|')) {
                                $val['attr_value'] = handle_subdir_pic($val['attr_value'], 'img', true);
                                $val['attr_value'] = "<a href='{$val['attr_value']}' target='_blank'><img src='{$val['attr_value']}' width='60' height='60' style='float: unset;cursor: pointer;' /></a>";
                            }
                        } elseif (preg_match('/(\.(' . tpCache('basic.file_type') . '))$/i', $val['attr_value'])) {
                            if (!stristr($val['attr_value'], '|')) {
                                $val['attr_value'] = handle_subdir_pic($val['attr_value'], 'img', true);
                                $val['attr_value'] = "<a href='{$val['attr_value']}' download='" . time() . "'><img src=\"" . $this->request->domain() . ROOT_DIR . "/public/static/common/images/file.png\" alt=\"\" style=\"width: 16px;height:  16px;\">点击下载</a>";
                            }
                        }
                    }
                    $content .= $val['attr_name'] . '：' . $val['attr_value'] . '<br/>';
                }
                $content .= '所属表单：' . $info['form_name'] . '<br/>';
                $content .= 'IP来源：' . $info['ip'];
                if (!empty($info['city'])) {
                    $content .= "({$info['city']})";
                } else {
                    $content .= "(<a href=\"https://www.baidu.com/s?wd={$info['ip']}\" target=\"_blank\">查看地区</a>)";
                }
                $content .= '<br/>';
                if (2 == $info['source']) {
                    $content .= "提交来源：手机端<br/>";
                } else {
                    $content .= "提交来源：电脑端<br/>";
                }
                $content .= '提交时间：' . MyDate('Y-m-d H:i:s', $info['add_time']) . '<br/>';

                // 美化邮件模板
                $year = date('Y');
                $source_text = (2 == $info['source']) ? '手机端' : '电脑端';
                $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background-color:#f5f5f5;font-family:'Microsoft YaHei',Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f5f5;padding:30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:30px;border-radius:8px 8px 0 0;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:24px;font-weight:normal;">新的留言消息</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px 30px;">
                            <p style="margin-bottom:20px;color:#333;">您好，管理员：</p>
                            <p style="color:#333;">您收到一条新的留言，详情如下：</p>
                            <div style="background:#f8f9fa;border-radius:6px;padding:20px;margin:20px 0;">
                                <table width="100%" style="font-size:14px;color:#333;">
                                    {$content}
                                </table>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#f8f9fa;padding:20px 30px;border-radius:0 0 8px 8px;border-top:1px solid #eee;">
                            <p style="margin:0;color:#999999;font-size:12px;text-align:center;">
                                来源：{$source_text} | {$web_name}<br>
                                &copy; {$year} {$web_name} 版权所有
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

                // 发送邮件
                $res = send_email(null, null, $html, $scene);
                if (intval($res['code']) == 1) {
                    $this->success($res['msg']);
                } else {
                    $this->error($res['msg']);
                }
            }
            else if ('admin_login' == $type) {
                // 是否满足发送邮箱的条件
                $tplInfo = Db::name('smtp_tpl')->where(['send_scene' => 30, 'lang' => $this->home_lang])->find();
                $smtp_config = tpCache('smtp');
                if (empty($tplInfo['is_open']) || empty($smtp_config['smtp_user']) || empty($smtp_config['smtp_pwd'])) {
                    $this->error("邮箱尚未配置，发送失败");
                }

                /* 只允许已通过账号密码校验、等待二次验证的会话触发，且验证码只发给该管理员绑定的邮箱 */
                $pending = session('admin_login_twofactor_check');
                $pending = !empty($pending) ? mchStrCode($pending, 'DECODE', '', false) : '';
                if (empty($pending)) {
                    $this->error('登录状态已失效，请重新登录');
                }
                $email = Db::name('admin')->where('user_name', $pending)->value('email');
                if (empty($email) || !check_email($email)) {
                    $smtp_from_eamil = str_replace('，', ',', tpCache('smtp.smtp_from_eamil'));
                    $eamil_arr = explode(',', $smtp_from_eamil);
                    $email = trim(current($eamil_arr));
                }
                if (empty($email) || !check_email($email)) {
                    $this->error('当前管理员没有绑定Email邮箱！');
                }
                // 同一邮箱 120 秒内只能发送一次
                $sendCount = Db::name('smtp_record')->where([
                    'source' => 30,
                    'email' => $email,
                    'status' => 0,
                    'lang' => $this->home_lang,
                    'add_time' => ['>', getTime() - 120],
                ])->count();
                if (!empty($sendCount)) {
                    $this->error('120秒内只能发送一次！');
                }

                // 生成验证码
                $web_name = tpCache('web.web_name');
                $code = get_rand_str(10, 0, 1);
                $year = date('Y');

                // 美化邮件模板
                $html = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background-color:#f5f5f5;font-family:'Microsoft YaHei',Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f5f5;padding:30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#e74c3c 0%,#c0392b 100%);padding:30px;border-radius:8px 8px 0 0;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:24px;font-weight:normal;">后台登录验证</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px 30px;">
                            <p style="margin-bottom:20px;color:#333;">您好，管理员：</p>
                            <p style="color:#333;">您正在进行后台登录操作，验证码如下：</p>
                            <div style="text-align:center;margin:30px 0;">
                                <div style="display:inline-block;background:linear-gradient(135deg,#e74c3c 0%,#c0392b 100%);color:#fff;font-size:28px;font-weight:bold;letter-spacing:6px;padding:15px 35px;border-radius:8px;">{$code}</div>
                            </div>
                            <p style="color:#999;font-size:12px;">验证码10分钟内有效，请勿泄露于他人。</p>
                            <p style="color:#999;font-size:12px;">如非本人操作，请检查账号安全。</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#f8f9fa;padding:20px 30px;border-radius:0 0 8px 8px;border-top:1px solid #eee;">
                            <p style="margin:0;color:#999999;font-size:12px;text-align:center;">&copy; {$year} {$web_name} 版权所有</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

                // 发送邮件
                $send_email_scene = config('send_email_scene');
                $scene = $send_email_scene[1]['scene'];
                $res = send_email($email, $tplInfo['tpl_title'], $html, $scene);
                if (intval($res['code']) == 1) {
                    // 数据添加
                    $datas = [];
                    $datas['source']   = 30;
                    $datas['email']    = $email;
                    $datas['code']     = $code;
                    $datas['lang']     = $this->home_lang;
                    $datas['add_time'] = getTime();
                    $insertId = Db::name('smtp_record')->insertGetId($datas);
                    if ($insertId !== false) {
                        $where = [
                            'source' => 30,
                            'email' => $email,
                            'lang' => $this->home_lang,
                            'record_id' => ['NEQ', $insertId],
                        ];
                        Db::name('smtp_record')->where($where)->update(['status'=>1]);
                    }
                    $this->success($res['msg']);
                } else {
                    $this->error($res['msg']);
                }
            }
            else if ('gbook_real_validate' == $type) {
                // 是否满足发送邮箱的条件
                $tplInfo = Db::name('smtp_tpl')->where(['send_scene' => 40, 'lang' => $this->home_lang])->find();
                $smtp_config = tpCache('smtp');
                if (empty($tplInfo['is_open']) || empty($smtp_config['smtp_user']) || empty($smtp_config['smtp_pwd'])) {
                    $this->error("邮箱尚未配置，发送失败");
                }

                $code = input('param.code/s', '');
                $code_token = input('param.code_token/s', '');
                // 图形验证码判断
                if (empty($code)) $this->error('请输入图形验证码！');
                $verify = new \think\Verify();
                if (!$verify->check($code, $code_token)) $this->error('图形验证码错误！', null, ['code' => 'code']);

                // 判断标题拼接
                $web_name = tpCache('web.web_name');
                $code = mt_rand(1000, 9999);
                $year = date('Y');

                // 美化邮件模板
                $html = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background-color:#f5f5f5;font-family:'Microsoft YaHei',Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f5f5;padding:30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:30px;border-radius:8px 8px 0 0;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:24px;font-weight:normal;">邮箱验证</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:40px 30px;">
                            <p style="margin-bottom:20px;color:#333;">您好：</p>
                            <p style="color:#333;">您正在进行邮箱验证操作，验证码如下：</p>
                            <div style="text-align:center;margin:30px 0;">
                                <div style="display:inline-block;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;font-size:32px;font-weight:bold;letter-spacing:8px;padding:15px 40px;border-radius:8px;">{$code}</div>
                            </div>
                            <p style="color:#999;font-size:12px;">验证码10分钟内有效，请勿泄露于他人。</p>
                            <p style="color:#999;font-size:12px;">如非本人操作，请忽略此邮件。</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#f8f9fa;padding:20px 30px;border-radius:0 0 8px 8px;border-top:1px solid #eee;">
                            <p style="margin:0;color:#999999;font-size:12px;text-align:center;">&copy; {$year} {$web_name} 版权所有</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;

                // 发送邮件
                $send_email_scene = config('send_email_scene');
                $scene = $send_email_scene[1]['scene'];
                $email = input('param.email/s');
                $email = preg_replace('/([\,]*)/i', '', $email);
                if (!empty($email) && !check_email($email)) $this->error("Email邮箱格式不正确！");
                $res = send_email($email, $tplInfo['tpl_title'], $html, $scene);
                if (intval($res['code']) == 1) {
                    // 数据添加
                    $datas = [];
                    $datas['source']   = 40;
                    $datas['email']    = $email;
                    $datas['code']     = $code;
                    $datas['lang']     = $this->home_lang;
                    $datas['add_time'] = getTime();
                    $insertId = Db::name('smtp_record')->insertGetId($datas);
                    if ($insertId !== false) {
                        $where = [
                            'source' => 40,
                            'email' => $email,
                            'lang' => $this->home_lang,
                            'record_id' => ['NEQ', $insertId],
                        ];
                        Db::name('smtp_record')->where($where)->update(['status'=>1]);
                    }
                    $this->success($res['msg']);
                } else {
                    $this->error($res['msg']);
                }
            }
        }
    }

    /**
     * 手机短信发送
     */
    public function SendMobileCode()
    {
        // 超时后，断掉发送
        function_exists('set_time_limit') && set_time_limit(5);
        // \think\Session::pause(); // 暂停session，防止session阻塞机制

        /*$pretime1 = getTime() - 120; // 3分钟内
        $ip_prefix = preg_replace('/\d+\.\d+$/i', '', clientIP());
        $count = Db::name('sms_log')->where([
                'ip'    => ['LIKE', "{$ip_prefix}%"],
                'is_use'    => 1,
                'add_time'  => ['gt', $pretime1],
            ])->count();
        if (!empty($count) && 5 <= $count) {
            $this->error('发送短信异常~');
        }*/

        // 发送手机验证码
        if (IS_AJAX_POST) {
            $post = input('post.');
            $source = (!empty($post['source']) && is_scalar($post['source'])) ? intval($post['source']) : 0;
            $scene = (isset($post['scene']) && is_scalar($post['scene'])) ? intval($post['scene']) : 0;
            // !empty($post['phone']) && $post['phone'] = preg_replace('/([^0-9]+)/i', '', $post['phone']);
            // !empty($post['mobile']) && $post['mobile'] = preg_replace('/([^0-9]+)/i', '', $post['mobile']);
            $openMobileFormat = \think\Cache::get("foreign_open_mobile_format");
            if (empty($openMobileFormat)) {
                if (!empty($post['phone']) && is_string($post['phone']) && !check_mobile($post['phone'])) {
                    $this->error(foreign_lang('users29', $this->home_lang));
                }
                else if (!empty($post['mobile']) && is_string($post['mobile']) && !check_mobile($post['mobile'])) {
                    $this->error(foreign_lang('users29', $this->home_lang));
                }
            }

            // 留言验证类型发送短信处理
            if (7 == $scene) {
                $phone = (isset($post['phone']) && is_string($post['phone'])) ? trim($post['phone']) : '';
                if (empty($phone)) $this->error(foreign_lang('users29', $this->home_lang));
                // 是否允许再次发送
                $where = [
                    'source' => 7,
                    'mobile' => $phone,
                    'status' => 1,
                    'is_use' => 0,
                    'add_time' => ['>', getTime() - 120]
                ];
                $Result = Db::name('sms_log')->where($where)->order('id desc')->count();
                if (!empty($Result)) $this->error('120秒内只能发送一次');

                // 图形验证码判断
                if (empty($post['code']) || !is_string($post['code'])) $this->error(foreign_lang('users19', $this->home_lang));
                $code_token = (isset($post['code_token']) && is_string($post['code_token'])) ? $post['code_token'] : '';
                $verify = new \think\Verify();
                if (!$verify->check($post['code'], $code_token)) $this->error('图片验证码错误');

                // 多维频控：IP / 会话 / 手机号
                if (!sms_send_limit_check($phone, 7)) $this->error('发送过于频繁，请稍后再试');

                // 发送并返回结果
                $Result = sendSms(7, $phone, array('content' => mt_rand(1000, 9999)));
                if (1 === intval($Result['status'])) {
                    $this->success('发送成功');
                } else {
                    $this->error($Result['msg']);
                }
            }
            // 订单付款、发货、投稿提醒：仅允许已登录用户触发，接收号码由服务端决定
            else if (in_array($scene, [5, 6, 20], true)) {
                $is_admin = !empty(session('admin_id')) || !empty(session('admin_info.admin_id'));
                $users_id = intval(session('users_id'));
                if (!$is_admin && $users_id <= 0) $this->error('请先登录');

                // 处理发送的内容
                $data = !empty($post['data']) ? $post['data'] : [];
                if (!is_array($data)) {
                    $data = json_decode(htmlspecialchars_decode(htmlspecialchars_decode($data)), true);
                }
                if (!is_array($data)) $data = [];
                $type = (isset($data['type']) && is_scalar($data['type'])) ? intval($data['type']) : 0;

                // 场景与通知类型必须匹配，防止拼接任意模板内容
                $scene_type_map = [5 => 1, 6 => 2, 20 => 3];
                if (!isset($scene_type_map[$scene]) || $scene_type_map[$scene] !== $type) $this->error('参数错误');

                // 查询消息通知模板的内容
                $sms_config = tpCache('sms');
                $sms_type = !empty($sms_config['sms_type']) ? intval($sms_config['sms_type']) : 1;
                $tpl_content = Db::name('sms_template')->where(["send_scene" => $scene, "sms_type" => $sms_type, 'is_open' => 1])->value('tpl_content');
                if (empty($tpl_content)) $this->error('短信模板未配置或已关闭');

                $order_code = (isset($data['order_code']) && is_scalar($data['order_code'])) ? trim((string)$data['order_code']) : '';
                if (20 == $scene) {
                    // 会员投稿提醒：只发给后台配置的管理员手机号
                    $mobile = !empty($sms_config['sms_test_mobile']) ? trim($sms_config['sms_test_mobile']) : '';
                    if (empty($mobile)) $this->error('未配置接收短信的管理员手机号');
                    $sendData = ['content' => $tpl_content];
                    $onceKey = 'notice|20|' . $users_id . '|' . intval(session('admin_id'));
                } else {
                    if (empty($order_code) || !preg_match('/^[A-Za-z0-9_\-]{6,64}$/', $order_code)) $this->error('订单号格式错误');
                    $shopOrder = Db::name('shop_order')->where('order_code', $order_code)->field('order_id,order_code,users_id,mobile,express_time')->find();
                    if (empty($shopOrder)) $this->error('订单不存在');
                    // 会员只能触发自己订单的短信，后台不受此限制
                    if (!$is_admin && $users_id !== intval($shopOrder['users_id'])) $this->error('无权操作该订单');
                    if (5 == $scene) {
                        // 订单付款提醒：只发给后台配置的管理员手机号
                        $mobile = !empty($sms_config['sms_test_mobile']) ? trim($sms_config['sms_test_mobile']) : '';
                        if (empty($mobile)) $this->error('未配置接收短信的管理员手机号');
                        $sendData = ['content' => $shopOrder['order_code']];
                    } else {
                        // 订单发货提醒：只发给该订单买家，忽略请求方指定的号码
                        $mobile = !empty($shopOrder['mobile']) ? trim($shopOrder['mobile']) : '';
                        if (empty($mobile)) {
                            $mobile = Db::name('users')->where('users_id', intval($shopOrder['users_id']))->value('mobile');
                        }
                        if (empty($mobile)) $this->error('订单未填写手机号');
                        $sendData = [
                            'content' => $shopOrder['order_code'],
                            'express_time' => $shopOrder['express_time'],
                            'product_name' => Db::name('shop_order_details')->where('order_id', intval($shopOrder['order_id']))->value('product_name'),
                        ];
                    }
                    $onceKey = 'notice|' . $scene . '|' . $order_code;
                }

                // 同一订单/场景 60 秒内只发一次，叠加 IP / 会话 / 号码多维频控
                $onceCacheKey = 'sms_send_once_' . md5($onceKey);
                if (\think\Cache::get($onceCacheKey)) $this->error('发送过于频繁，请稍后再试');
                if (!sms_send_limit_check($mobile, $scene, 200, 100, 60)) $this->error('发送过于频繁，请稍后再试');
                \think\Cache::set($onceCacheKey, 1, 60);

                $Result = sendSms($scene, $mobile, $sendData);
                if (intval($Result['status']) == 1) {
                    $this->success('发送成功！');
                } else {
                    $this->error($Result['msg']);
                }
            }
            //发送表单提醒
            else if (11 == $scene) {
                //查询消息通知模板的内容
                $sms_config = tpCache('sms');
                $sms_type = !empty($sms_config['sms_type']) ? intval($sms_config['sms_type']) : 1;
                $tpl_content = Db::name('sms_template')->where(["send_scene" => 11, "sms_type" => $sms_type, 'is_open' => 1])->value('tpl_content');
                if (!$tpl_content) $this->error('短信模板未配置或已关闭');

                // 只发给后台配置的管理员手机号，忽略请求方参数
                $mobile = !empty($sms_config['sms_test_mobile']) ? trim($sms_config['sms_test_mobile']) : '';
                if (empty($mobile)) $this->error('未配置接收短信的管理员手机号');
                if (!sms_send_limit_check($mobile, 11, 100, 20, 60)) $this->error('发送过于频繁，请稍后再试');

                $Result = sendSms(11, $mobile, []);
                if (intval($Result['status']) == 1) {
                    $this->success('发送成功！');
                } else {
                    $this->error($Result['msg']);
                }
                /* END */
            }
            // 其他类型发送短信处理
            else {
                // 仅允许注册、绑定、登录、找回密码、后台二次验证等业务场景
                if (!in_array($source, [0, 1, 2, 4, 30], true)) $this->error('参数错误');

                $sms_config = tpCache('sms');
                $sms_type = isset($sms_config['sms_type']) ? $sms_config['sms_type'] : 1;
                $smsTemp = Db::name('sms_template')->where(["send_scene"=>$source,"sms_type"=>$sms_type,'lang'=>get_admin_lang()])->find();
                if (empty($smsTemp) || empty($smsTemp['sms_sign']) || empty($smsTemp['sms_tpl_code']) || empty($smsTemp['tpl_content'])){
                    $this->error('尚未正确配置短信模板，请联系管理员！');
                }

                $users_id = intval(session('users_id'));

                // 后台二次验证：验证码只发给当前待验证的管理员，禁止请求方指定手机号
                if (30 == $source) {
                    $pending = session('admin_login_twofactor_check');
                    $pending = !empty($pending) ? mchStrCode($pending, 'DECODE', '', false) : '';
                    if (empty($pending)) $this->error('登录状态已失效，请重新登录');
                    $mobile = Db::name('admin')->where('user_name', $pending)->value('mobile');
                    if (empty($mobile) || !check_mobile($mobile)) {
                        $mobile = !empty($sms_config['sms_test_mobile']) ? trim($sms_config['sms_test_mobile']) : '';
                    }
                    if (empty($mobile)) $this->error('管理员未绑定手机号');

                    // 是否允许再次发送
                    $where = [
                        'mobile' => $mobile,
                        'source' => 30,
                        'status' => 1,
                        'is_use' => 0,
                        'add_time' => ['>', getTime() - 120]
                    ];
                    $Result = Db::name('sms_log')->where($where)->order('id desc')->count();
                    if (!empty($Result) && false == config('sms_debug')) $this->error('120秒内只能发送一次！');

                    if (!sms_send_limit_check($mobile, 30)) $this->error('发送过于频繁，请稍后再试');

                    /*发送并返回结果：统一使用6位验证码，降低被爆破风险*/
                    $content = mt_rand(100000, 999999);
                    $Result = sendSms($source, $mobile, array('content' => $content));
                    if (intval($Result['status']) == 1) {
                        $this->success('发送成功！');
                    } else {
                        $this->error($Result['msg']);
                    }
                }

                if (isset($post['type']) && in_array($post['type'], ['users_mobile_reg', 'users_mobile_login', 'reg'])) {
                    $post['is_mobile'] = true;
                }
                $post_mobile = (isset($post['mobile']) && is_string($post['mobile'])) ? trim($post['mobile']) : '';
                $mobile = !empty($post_mobile) ? $post_mobile : session('mobile');
                if (empty($mobile) || !is_string($mobile)) $this->error('请先绑定手机号码');
                if (!check_mobile($mobile)) $this->error(foreign_lang('users29', $this->home_lang));
                // 手机可用性验证
                $is_mobile = !empty($post['is_mobile']) ? $post['is_mobile'] : false;
                if (!empty($is_mobile)) {
                    /*是否存在手机号码*/
                    $where = [
                        'mobile' => $mobile
                    ];
                    if (!empty($users_id)) $where['users_id'] = ['NEQ', $users_id];
                    $Result = Db::name('users')->where($where)->count();
                    /* END */
                    if (0 == $source) {
                        if (!empty($Result)) $this->error('手机号码已注册');
                    } else if (2 == $source) {
                        // 手机登录：根据后台开关判断是否允许未注册手机号发送验证码
                        if (empty($Result)) {
                            $users_mobile_login_auto_reg = getUsersConfigData('users.users_mobile_login_auto_reg');
                            if (empty($users_mobile_login_auto_reg)) {
                                $this->error('手机号码未注册');
                            }
                        }
                    } else if (4 == $source) {
                        if (empty($Result)) $this->error('手机号码不存在');
                    } else {
                        if (!empty($Result)) $this->error('手机号码已存在');
                    }
                }

                /*是否允许再次发送*/
                $where = [
                    'mobile' => $mobile,
                    'source' => $source,
                    'status' => 1,
                    'is_use' => 0,
                    'add_time' => ['>', getTime() - 120]
                ];
                $Result = Db::name('sms_log')->where($where)->order('id desc')->count();

                if (!empty($Result) && false == config('sms_debug')) $this->error('120秒内只能发送一次！');
                /* END */

                /*图形验证码判断：除“已登录会员绑定手机”“注册页自定义手机字段（携带服务端表单令牌）”外，一律强制校验*/
                $captchaPassed = false;
                $captchaAvailable = function_exists('imagettftext');
                $vertify = (isset($post['vertify']) && is_string($post['vertify'])) ? trim($post['vertify']) : '';
                if ($captchaAvailable && !empty($vertify)) {
                    $captcha_type_map = [0 => 'users_mobile_reg', 1 => 'bind', 2 => 'users_mobile_login', 4 => 'users_retrieve_password'];
                    $captcha_type = isset($captcha_type_map[$source]) ? $captcha_type_map[$source] : '';
                    $verify = new \think\Verify();
                    if (!$verify->check($vertify, $captcha_type)) $this->error('图形验证码错误！', null, ['code' => 'vertify']);
                    $captchaPassed = true;
                }
                if (false === $captchaPassed && 1 == $source && $users_id > 0) {
                    // 已登录会员绑定/更换手机：验证码只下发到本人填写的号码，叠加多维频控
                    $captchaPassed = true;
                }
                if (false === $captchaPassed && 0 == $source && isset($post['type']) && 'reg' == $post['type'] && !empty($post['__mobile_1_token__'])) {
                    // 注册页自定义手机字段未展示图形码，以服务端表单令牌作为凭据（下方统一校验）
                    $captchaPassed = true;
                }
                if (false === $captchaPassed && 4 == $source) {
                    // 找回密码：未开启图形验证码或GD库不可用时，只能依赖多维频控兜底
                    $captchaConf = config('captcha.users_retrieve_password');
                    $captchaOn = !empty($captchaConf) && !empty($captchaConf['is_on']);
                    if (!$captchaAvailable || !$captchaOn) {
                        $captchaPassed = true;
                    }
                }
                if (false === $captchaPassed) $this->error('请输入图形验证码！');
                /* END */
                
                if (isset($post['is_mobile']) && true === $post['is_mobile']) {
                    // 数据验证
                    $rule = [
                        'mobile' => 'require|token:__mobile_1_token__',
                    ];
                    $message = [
                        'mobile.require' => foreign_lang('users28', $this->home_lang),
                    ];
                    $validate = new \think\Validate($rule, $message);
                    if (!$validate->batch()->check($post)) {
                        $this->error('表单令牌过期，请尝试刷新页面~');
                    }
                }

                // 多维频控：IP / 会话 / 手机号
                if (!sms_send_limit_check($mobile, $source)) $this->error('发送过于频繁，请稍后再试');

                /*发送并返回结果：统一使用6位验证码，降低被爆破风险*/
                $content = mt_rand(100000, 999999);
                $Result = sendSms($source, $mobile, array('content' => $content));
                if (intval($Result['status']) == 1) {
                    $this->success('发送成功！');
                } else {
                    $this->error($Result['msg']);
                }
                /* END */
            }
        }
    }

    // 判断文章内容阅读权限
    public function get_arcrank($aid = '', $vars = '',$tid = '')
    {
        $aid = intval($aid);
        $tid = intval($tid);
        $vars = intval($vars);
        $gourl = input('param.gourl/s');
        $gourl = urldecode($gourl);
        $gourl = !empty($gourl) ? urldecode($gourl) : ROOT_DIR . '/';
        if ((IS_AJAX || !empty($vars)) && !empty($aid)) {
            // 用户ID
            $users_id = session('users_id');
            // 文章查看所需等级值
            $Arcrank = Db::name('archives')->alias('a')
                ->field('a.users_id, a.arcrank,c.typearcrank,c.page_limit')
                ->join('__ARCTYPE__ c', 'a.typeid = c.id', 'LEFT')
                ->where(['a.aid' => $aid])
                ->find();
            $Arcrank['page_limit'] = empty($Arcrank['page_limit']) ? [] : explode(',', $Arcrank['page_limit']);
            //文章存在限制条件，优先使用文章限制条件；如不存在，则使用栏目限制条件。
            if (empty($Arcrank['arcrank']) && (!empty($Arcrank['typearcrank']) && $Arcrank['typearcrank'] > 0 && in_array(2, $Arcrank['page_limit']))) {
                $Arcrank['arcrank'] = $Arcrank['typearcrank'];
            }

            if (!empty($users_id)) {
                // 会员级别等级值
                $UsersDataa = Db::name('users')->alias('a')
                    ->field('a.users_id,b.level_value,b.level_name')
                    ->join('__USERS_LEVEL__ b', 'a.level = b.level_id', 'LEFT')
                    ->where(['a.users_id' => $users_id])
                    ->find();
                if (0 == $Arcrank['arcrank']) {
                    if (IS_AJAX) {
                        $this->success('允许查阅！');
                    } else {
                        return true;
                    }
                } else if (-1 == $Arcrank['arcrank']) {
                    $is_admin = session('?admin_id') ? 1 : 0;
                    $param_admin_id = input('param.admin_id/d');
                    if ($users_id == $Arcrank['users_id']) {
                        if (IS_AJAX) {
                            $this->success('允许查阅！', null, ['is_admin' => $is_admin, 'msg' => '待审核稿件，仅限自己查看！']);
                        } else {
                            return true;
                        }
                    } else if (!empty($is_admin) && !empty($param_admin_id)) {
                        if (IS_AJAX) {
                            $this->success('允许查阅！', null, ['is_admin' => $is_admin, 'msg' => '待审核稿件，仅限管理员查看！']);
                        } else {
                            return true;
                        }
                    } else {
                        $msg = '待审核稿件，你没有权限阅读！';
                    }
                } else if ($UsersDataa['level_value'] < $Arcrank['arcrank']) {
                    $level_name = Db::name('users_level')->where(['level_value' => $Arcrank['arcrank']])->getField('level_name');
                    $msg = '__html__内容需要【' . $level_name . '】才可以查看<br/>您为【' . $UsersDataa['level_name'] . '】，请先升级！';

                } else {
                    if (IS_AJAX) {
                        $this->success('允许查阅！');
                    } else {
                        return true;
                    }
                }
                if (IS_AJAX) {
                    $this->error($msg);
                } else {
                    return $msg;
                }
            } else {
                if (0 == $Arcrank['arcrank']) {
                    if (IS_AJAX) {
                        $this->success('允许查阅！');
                    } else {
                        return true;
                    }
                } else if (-1 == $Arcrank['arcrank']) {
                    $is_admin = session('?admin_id') ? 1 : 0;
                    $param_admin_id = input('param.admin_id/d');
                    if (!empty($is_admin) && !empty($param_admin_id)) {
                        $this->success('允许查阅！', null, ['is_admin' => $is_admin, 'msg' => '待审核稿件，仅限管理员查看！']);
                    } else {
                        $msg = '待审核稿件，你没有权限阅读！';
                    }
                } else if (!empty($Arcrank['arcrank'])) {
                    $level_name = Db::name('users_level')->where(['level_value' => $Arcrank['arcrank']])->getField('level_name');
                    $msg = '文章需要【' . $level_name . '】才可以查看，游客不可查看，请登录！';
                } else {
                    $msg = '游客不可查看，请登录！';
                }
                if (IS_AJAX) {
                    $loginUrl = url('user/Users/login');
                    if (stristr($loginUrl, '?')) {
                        $gourl = $loginUrl . "&referurl=" . urlencode($gourl);
                    } else {
                        $gourl = $loginUrl . "?referurl=" . urlencode($gourl);
                    }
                    $data = [
                        'is_login' => 0,
                        'gourl' => $gourl,
                    ];
                    $this->error($msg, null, $data);
                } else {
                    return $msg;
                }
            }
        } else if ((IS_AJAX || !empty($vars)) && !empty($tid)) {
            // 用户ID
            $users_id = session('users_id');
            // 文章查看所需等级值
            $Arcrank = Db::name('arctype')
                ->field('typearcrank,page_limit')
                ->where(['id' => $tid])
                ->find();
            $Arcrank['page_limit'] = empty($Arcrank['page_limit']) ? [] : explode(',', $Arcrank['page_limit']);
            //文章存在限制条件，优先使用文章限制条件；如不存在，则使用栏目限制条件。
            if (!empty($Arcrank['typearcrank']) && $Arcrank['typearcrank'] > 0 && in_array(1, $Arcrank['page_limit'])) {
                if (!empty($users_id)) {
                    // 会员级别等级值
                    $UsersDataa = Db::name('users')->alias('a')
                        ->field('a.users_id,b.level_value,b.level_name')
                        ->join('__USERS_LEVEL__ b', 'a.level = b.level_id', 'LEFT')
                        ->where(['a.users_id' => $users_id])
                        ->find();
                    if ($UsersDataa['level_value'] < $Arcrank['typearcrank']) {
                        $level_name = Db::name('users_level')->where(['level_value' => $Arcrank['typearcrank']])->getField('level_name');
                        $msg = '__html__内容需要【' . $level_name . '】才可以查看<br/>您为【' . $UsersDataa['level_name'] . '】，请先升级！';
                        if (IS_AJAX) {
                            $this->error($msg);
                        } else {
                            return $msg;
                        }
                    }
                } else {
                    $level_name = Db::name('users_level')->where(['level_value' => $Arcrank['typearcrank']])->getField('level_name');
                    $msg = '文章需要【' . $level_name . '】才可以查看，游客不可查看，请登录！';
                    if (IS_AJAX) {
                        $loginUrl = url('user/Users/login');
                        if (stristr($loginUrl, '?')) {
                            $gourl = $loginUrl . "&referurl=" . urlencode($gourl);
                        } else {
                            $gourl = $loginUrl . "?referurl=" . urlencode($gourl);
                        }
                        $data = [
                            'is_login' => 0,
                            'gourl' => $gourl,
                        ];
                        $this->error($msg, null, $data);
                    } else {
                        return $msg;
                    }
                }
            }
            if (IS_AJAX) {
                $this->success('允许查阅！');
            } else {
                return true;
            }
        } else {
            abort(404);
        }
    }

    /**
     * 获取会员列表
     * @author 小虎哥 by 2018-4-20
     */
    public function get_tag_memberlist()
    {
        if (IS_AJAX_POST) {
            $publicConfig = get_memberlist_public_config();

            // 接收html实体代码
            $htmlcode = input('post.htmlcode/s', '');
            $htmlcode = htmlspecialchars_decode($htmlcode);
            $htmlcode = preg_replace('/<\?(\s*)php(\s+)/i', '', $htmlcode);

            // 接收标签参数
            $attarray = input('post.attarray/s', '');
            $attarray = htmlspecialchars_decode($attarray);
            $attarray = json_decode(base64_decode($attarray), true);
            $attarray = is_array($attarray) ? $attarray : [];

            // 查询会员表字段
            $usersField = Db::name("users")->getTableFields();

            // 只允许公开字段参与输出替换，手机、邮箱、余额、openid 等敏感字段一律不替换
            $search = [];
            $searchField = [];
            foreach ($publicConfig['fields'] as $value) {
                if (in_array($value, $usersField)) {
                    $search[] = $value . '_eyoucms_fields';
                    $searchField[] = $value;
                }
            }

            // 会员表自定义字段（只允许结构化字段，手机、邮箱、附件、图片、自由文本等一律不输出）
            $where = [
                'is_hidden' => 0,
                'lang' => $this->home_lang,
                'dtype' => ['IN', $publicConfig['allow_dtype']],
            ];
            $usersParams = Db::name("users_parameter")->field('para_id, name')->where($where)->order('para_id asc')->select();
            $searchParam = [];
            foreach ($usersParams as $value) {
                $search[] = $value['name'] . '_eyoucms_params';
                $searchParam[] = $value['para_id'];
            }

            // 查询排序（只允许内置公开字段，禁止按 mobile/users_money 等敏感列排序）
            $orderby = (!empty($attarray['orderby']) && is_string($attarray['orderby'])) ? $attarray['orderby'] : '';
            switch ($orderby) {
                case 'logintime': // 兼容写法
                case 'last_login':
                    $orderby = "last_login desc";
                    break;

                case 'users_id':
                    $orderby = "users_id desc";
                    break;

                case 'regtime':
                case 'reg_time':
                    $orderby = "reg_time desc";
                    break;

                default:
                    {
                        if (in_array($orderby, $publicConfig['fields'], true)) {
                            $orderby = "{$orderby} desc";
                        } else {
                            $orderby = "users_id desc";
                        }
                        break;
                    }
            }

            // 查询会员表数据（排除已注销、被锁定的会员）
            $where = [
                'admin_id' => 0,
                'is_del' => 0,
                'is_lock' => 0,
                'lang' => $this->home_lang,
            ];
            $limit = !empty($attarray['row']) ? intval($attarray['row']) : 10;
            $limit = max(1, min(50, $limit)); // 单次最多返回 50 条，防止被批量导出
            $list = Db::name("users")->where($where)->order($orderby)->limit($limit)->select();

            // 查询会员自定义数据（只查询当前列表涉及到的会员，避免全表扫描）
            $usersList = [];
            if (!empty($list)) {
                $users_ids = get_arr_column($list, 'users_id');
                $where = [
                    'lang' => $this->home_lang,
                    'users_id' => ['IN', $users_ids],
                ];
                $usersList = Db::name("users_list")->field('para_id, users_id, info')->where($where)->select();
                $usersList = group_same_key($usersList, 'users_id');
            }

            // 查询会员等级数据
            $where = [
                'lang' => $this->home_lang,
            ];
            $usersLevel = Db::name("users_level")->where($where)->order('level_id asc')->getField('level_id, level_name');

            // 循环处理会员html显示代码
            $html = '';
            foreach ($list as $key => $value) {
                // 内置数据处理
                $value['head_pic'] = get_head_pic($value['head_pic'], false, $value['sex']);
                $value['level'] = !empty($usersLevel[$value['level']]) ? $usersLevel[$value['level']] : '';
                // 会员自定义数据按 para_id 对齐
                $paramValues = [];
                if (!empty($usersList[$value['users_id']])) {
                    foreach ($usersList[$value['users_id']] as $item) {
                        $paramValues[$item['para_id']] = $item['info'];
                    }
                }
                // 组装与占位符一一对应的替换值
                $replace = [];
                foreach ($searchField as $name) {
                    $replace[] = isset($value[$name]) ? $value[$name] : '';
                }
                foreach ($searchParam as $para_id) {
                    $replace[] = isset($paramValues[$para_id]) ? $paramValues[$para_id] : '';
                }
                // 替换指定字段数据
                if (!empty($search)) {
                    $html .= str_replace($search, $replace, $htmlcode);
                }
            }

            // dump($html);exit;
            $this->success('读取成功！', null, ['html' => htmlspecialchars_decode($html)]);
        }
        $this->error('加载失败！');
    }

    /**
     * 发布或编辑文档时，百度自动推送
     */
    public function push_zzbaidu($url, $type = 'add')
    {
        if (empty($url)) {
            $this->error('无效url推送');
        }
        $msg = '百度推送URL失败！';
        if (IS_AJAX_POST) {
            \think\Session::pause(); // 暂停session，防止session阻塞机制

            // 获取token的值：http://ziyuan.baidu.com/linksubmit/index?site=http://www.eyoucms.com/
            $sitemap_zzbaidutoken = config('tpcache.sitemap_zzbaidutoken');
            if (empty($sitemap_zzbaidutoken)) {
                $this->error('尚未配置实时推送Url的token！', null, ['code' => 0]);
            } else if (!function_exists('curl_init')) {
                $this->error('请开启php扩展curl_init', null, ['code' => 1]);
            }

            $urlsArr = is_array($url) ? $url : [$url];
            // $type = ('edit' == $type) ? 'update' : 'urls';
            $type = 'urls';

            if (is_http_url($sitemap_zzbaidutoken)) {
                $searchs = ["/urls?", "/update?"];
                $replaces = ["/{$type}?", "/{$type}?"];
                $api = str_replace($searchs, $replaces, $sitemap_zzbaidutoken);
            } else {
                $api = 'http://data.zz.baidu.com/' . $type . '?site=' . $this->request->host(true) . '&token=' . trim($sitemap_zzbaidutoken);
            }

            $ch = curl_init();
            $options = array(
                CURLOPT_URL => $api,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => implode("\n", $urlsArr),
                CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
            );
            curl_setopt_array($ch, $options);
            $result = curl_exec($ch);
            !empty($result) && $result = json_decode($result, true);
            if (!empty($result['success'])) {
                $this->success('百度推送URL成功！');
            } else {
                $msg = !empty($result['message']) ? $result['message'] : $msg;
            }
        }

        $this->error($msg);
    }

    /**
     * 发布或编辑文档/栏目时，小程序 API 提交
     * 将小程序资源 path 路径，提交到 API 接口中
     */
    public function push_bdminipro($aid = 0, $typeid = 0)
    {
        //先判断是否安装百度小程序插件
        $BdDiyminipro = Db::name('weapp')->where('code', 'BdDiyminipro')->where('status', 1)->find();
        if (empty($BdDiyminipro)) {
            $this->error('未安装可视化百度小程序！');
        } else {
            $data = Db::name('weapp_bd_diyminipro_setting')->where('name', 'setting')->order('mini_id desc')->find();
            $value = json_decode($data['value'], true);
            if (empty($value['appKey']) || empty($value['appSecret'])) {
                $this->error('未配置可视化百度小程序！');
            }
            $access_token = '';
            if (empty($value['access_token']) || empty($value['access_token_extime']) || $value['access_token_extime'] < getTime()) {
                if (!empty($value['appId'])) {
                    $values = [];
                    $values['appId'] = $value['appId'];
                    $url = "http://service.eyysz.cn/index.php?m=api&c=BaiduMiniproClient&a=minipro&" . http_build_query($values);
                    $response = httpRequest($url);
                    $params = array();
                    $params = json_decode($response, true);
                    if (!empty($params) && $params['errcode'] == 0) {
                        $at_url = 'https://openapi.baidu.com/oauth/2.0/token?grant_type=client_credentials&client_id=' . $params['errmsg']['appKey'] . '&client_secret=' . $params['errmsg']['appSecret'] . '&scope=smartapp_snsapi_base';
                        $response1 = httpRequest($at_url);
                        $params1 = array();
                        $params1 = json_decode($response1, true);
                        if (!empty($params1['access_token'])) {
                            $value['access_token'] = $access_token = $params1['access_token'];
                            $value['access_token_extime'] = getTime() + $params1['expires_in'];
                            $updateValue = json_encode($value);
                            Db::name('weapp_bd_diyminipro_setting')->where('mini_id', $data['mini_id'])->update(['value' => $updateValue, 'update_time' => getTime()]);
                        }
                    }
                }
            } else {
                $access_token = $value['access_token'];
            }
            if (!empty($access_token)) {
                $res = push_bdminiproapi($access_token, 1, $aid, $typeid);
                if (!empty($res)) {
                    $msg = !empty($res['msg']) ? $res['msg'] : $res['error_msg'];
                    $this->success($msg);
                }
            }
        }
    }

    /*
     * 视频权限播放逻辑
     */
    public function video_logic()
    {
        $post = input('post.');
        if (IS_AJAX_POST && !empty($post['aid'])) {

            // 查询文档信息 
            $field = 'a.*,b.*,c.*';
            $where = [
                'a.aid' => $post['aid'],
                'a.is_del' => 0
            ];
            $archivesInfo = Db::name('archives')
                ->alias('a')
                ->field($field)
                ->join('__USERS_LEVEL__ b', 'a.arc_level_id = b.level_id', 'LEFT')
                ->join('__ARCTYPE__ c', 'c.id = a.typeid', 'LEFT')
                ->where($where)
                ->find();

            if (5 == $archivesInfo['channel']) {
                // 获取用户最新信息
                $UsersData = GetUsersLatestData();
                $UsersID = !empty($UsersData['users_id']) ? $UsersData['users_id'] : 0;
                $result['status_value'] = 0; // status_value 0-所有人免费 1-所有人付费 2-会员免费 3-会员付费
                $result['status_name'] = ''; //status_name 要求会员等级时会员级别名称
                $result['play_auth'] = 0; //播放权限
                $result['vip_status'] = 0; //status_value=3时使用 vip_status=1则已升级会员暂未购买
                $users_price = get_discount_price($UsersData, $archivesInfo['users_price']);

                /*是否需要付费*/
                if (0 < $archivesInfo['users_price'] && (empty($archivesInfo['users_free']) || !empty($archivesInfo['no_vip_pay']))) {
                    if (empty($archivesInfo['arc_level_id'])) {
                        //不限会员 付费
                        $result['status_value'] = 1;
                        $result['users_price'] = $users_price;
                    } else {
                        $result['users_price'] = $users_price;
                        if (!empty($archivesInfo['no_vip_pay'])) { // 单独付费
                            $result['status_value'] = 2;
                            if ($archivesInfo['level_value'] <= $UsersData['level_value']) {
                                $result['play_auth'] = 1;
                            } else {
                                $result['play_auth'] = 0;
                            }
                        } else { //3-限制会员 付费
                            $result['status_value'] = 3;
                            if ($archivesInfo['level_value'] <= $UsersData['level_value']) {
                                $result['vip_status'] = 1;//已升级会员未购买
                            }
                        }
                    }

                    if (!empty($UsersID)) {
                        $where = [
                            'users_id' => intval($UsersID),
                            'product_id' => intval($post['aid']),
                            'order_status' => 1
                        ];
                        // 存在数据则已付费
                        $Paid = Db::name('media_order')->where($where)->count();
                        //已购买
                        if (!empty($Paid)) {
                            if (3 == $result['status_value']) {
                                if (1 == $result['vip_status']) {
                                    $result['play_auth'] = 1;
                                    $result['vip_status'] = 3;//已升级会员已经购买
                                } else {
                                    $result['play_auth'] = 0;
                                    $result['vip_status'] = 2;//未升级会员已经购买
                                }
                            } else {
                                $result['play_auth'] = 1;
                                $result['vip_status'] = 4;//不限会员已经购买
                            }
                        }
                    }
                } else {
                    if (0 < intval($archivesInfo['arc_level_id'])) { // 会员免费
                        $result['status_value'] = 2;
                        if (!empty($UsersID) && $archivesInfo['level_value'] <= $UsersData['level_value']) {
                            $result['play_auth'] = 1;
                        }
                    } else { // 所有人免费
                        $result['play_auth'] = 1;
                    }
                }
                /*END*/

                /**注册会员免费但是没有登录*/
                // if (empty($UsersID) && !empty($result['status_value'])) {
                //     $this->error('请先登录', url('user/Users/login'));
                // }

                $where = [
                    'users_id' => intval($UsersID),
                    'product_id' => intval($post['aid']),
                    'order_status' => 1
                ];
                // 存在数据则已付费
                /*END*/

                $is_pay = 0;
                if (in_array($result['status_value'], [1, 2, 3])) { // 所有人、会员付费
                    $is_pay = Db::name('media_order')->where($where)->count();
                }
                $result['is_pay'] = $is_pay;

                if (in_array($result['status_value'], [2, 3])) { // 已满足会员级别要求
                    $result['status_name'] = Db::name('users_level')->where('level_id', intval($archivesInfo['arc_level_id']))->value('level_name');
                }

                /*
                if (in_array($result['status_value'], [2,3])){ // 会员免费与会员付费
                    $result['status_name'] = Db::name('users_level')->where('level_id', $archivesInfo['arc_level_id'])->value('level_name');
                    $vip_status = 0;
                    if ($archivesInfo['level_value'] <= $UsersData['level_value']) {
                        $vip_status = 1; // 已满足会员级别要求
                    }
                }*/

                if ($result['status_value'] == 0) {
                    $result['button'] = '免费观看';
                    $result['status_name'] = '';
                } else if ($result['status_value'] == 1) { // 所有人付费
                    $result['button'] = '付费';
                    if (!empty($is_pay)) {
                        $result['button'] = '观看';
                    }
                } else if ($result['status_value'] == 2) { // 会员免费/单独付费
                    $result['button'] = 'VIP';
                    $result['button_url'] = "window.location.href = '" . url('user/Level/level_centre', ['aid' => $archivesInfo['aid']]) . "'";
                    $result['no_vip_pay'] = $archivesInfo['no_vip_pay'];
                    if (!empty($result['play_auth'])) {
                        $result['button'] = '观看';
                    } else {
                        if (!empty($result['no_vip_pay'])) {
                            $result['status_name'] = "{$result['status_name']} 或 单独购买 观看视频";
                        }
                    }
                } else if ($result['status_value'] == 3) { // 会员付费
//                     if(1 == $result['vip_status']){
//                         $result['button'] = '立即购买';
//                         $result['button_url'] = 'MediaOrderBuy_v878548();';
//                     }else
                    if (2 == $result['vip_status']) {
                        $result['button'] = 'VIP';
                        $result['button_url'] = "window.location.href = '" . url('user/Level/level_centre', ['aid' => $archivesInfo['aid']]) . "'";
                    } else if (3 == $result['vip_status']) {
                        $result['button'] = '观看';
                    } else {
                        $result['button'] = 'VIP付费';
                        if (1 == $result['vip_status']) {
                            $result['button_url'] = "MediaOrderBuy_v878548();";
                        } else {
                            $result['button_url'] = "window.location.href = '" . url('user/Level/level_centre', ['aid' => $archivesInfo['aid']]) . "'";
                        }
                    }
                    // if (!empty($is_pay) && !empty($result['vip_status'])){
                    //     $result['button'] = '观看';
                    // }
                }
                if ('观看' == $result['button']) {
                    $result['button_url'] = arcurl('home/Media/view', $archivesInfo);
                }

                $this->success('查询成功', null, $result);
            } else {
                $this->error('非视频模型的文档！');
            }
        }
        abort(404);
    }

    /**
     * 查看站内通知
     */
    public function notice_read()
    {
        $id = input('param.id/d');
        $users_id = session('users.users_id');
        $users_id = intval($users_id);
        if (!empty($id) && !empty($users_id)) {
            $notice = Db::name('users_notice_read')
                ->where(['id' => $id, 'users_id' => $users_id])
                ->field('id,is_read')
                ->find();
            if (empty($notice)) $this->error('未知错误！');

            $unread_num = Db::name('users')->where(['users_id' => $users_id])->value("unread_notice_num");
            if (empty($notice['is_read'])) {
                // 只更新属于当前用户的记录，且已读后不再重复扣减未读数
                $update = Db::name('users_notice_read')->where(['id' => $id, 'users_id' => $users_id, 'is_read' => 0])->update(['is_read' => 1]);
                if (!empty($update) && $unread_num > 0) {
                    //未读消息数-1
                    $unread_num = $unread_num - 1;
                    Db::name('users')->where(['users_id' => $users_id])->update(['unread_notice_num' => $unread_num]);
                }
            }
            $this->success('保存成功', null, ['unread_num' => intval($unread_num)]);
        }
    }

    /**
     * 收藏与取消
     * @return [type] [description]
     */
    public function collect_save()
    {
        $aid = input('param.aid/d');
        if (IS_AJAX && !empty($aid)) {

            $users_id = session('users_id');
            if (empty($users_id)) {
                $this->error('请先登录！');
            }

            $row = Db::name('users_collection')->where([
                'users_id' => $users_id,
                'aid' => $aid,
            ])->find();
            if (empty($row)) {
                $archivesInfo = Db::name('archives')->field('aid,title,litpic,channel,typeid')->find($aid);
                if (!empty($archivesInfo)) {
                    $r = Db::name('users_collection')->add([
                        'users_id' => $users_id,
                        'title' => $archivesInfo['title'],
                        'aid' => $aid,
                        'litpic' => $archivesInfo['litpic'],
                        'channel' => $archivesInfo['channel'],
                        'typeid' => $archivesInfo['typeid'],
                        'lang' => $this->home_lang,
                        'add_time' => getTime(),
                        'update_time' => getTime(),
                    ]);
                    if (!empty($r)) {
                        Db::name('archives')->where('aid', $aid)->setInc('collection');
                        $this->success('收藏成功', null, ['opt' => 'add']);
                    }
                }
            } else {
                $r = Db::name('users_collection')->where([
                    'users_id' => $users_id,
                    'aid' => $aid,
                ])->delete();
                Db::name('archives')->where('aid', $aid)->setDec('collection');
                $this->success('取消成功', null, ['opt' => 'cancel']);
            }
            $this->error('收藏失败', null);
        }
        abort(404);
    }

    /**
     * 判断是否收藏
     * @return [type] [description]
     */
    public function get_collection()
    {
        if (IS_AJAX) {
            $aid = input('param.aid/d');
            $users_id = session('users_id');
            $total = Db::name('users_collection')->where([
                'aid' => $aid,
            ])->count();
            if (!empty($users_id)) {
                $count = Db::name('users_collection')->where([
                    'aid' => $aid,
                    'users_id' => $users_id,
                ])->count();
                if (!empty($count)) {
                    $this->success('已收藏', null, ['total' => $total]);
                }
            }
            $this->error('未收藏', null, ['total' => $total]);
        }
        abort(404);

        /*$aid = input('param.aid/s');
        if (IS_AJAX_POST && !empty($aid)) {
            $aid_arr = explode(',', $aid);
            foreach ($aid_arr as $key => $val) {
                $aid_arr[$key] = intval($val);
            }
            $data1 = Db::name('users_collection')->field('aid, count(aid) as total')->where([
                'aid'   => ['IN', $aid_arr],
            ])->group('aid')->getAllWithIndex('aid');
            $users_id = session('users_id');
            if (!empty($users_id)) {
                $data2 = Db::name('users_collection')->field('aid, count(aid) as total')->where([
                    'aid'   => ['IN', $aid_arr],
                    'users_id'  => $users_id,
                ])->group('aid')->getAllWithIndex('aid');
                $this->success('已收藏', null, ['data1'=>$data1, 'data2'=>$data2]);
            }
            $this->error('未收藏', null, ['data1'=>$data1]);
        }
        abort(404);*/
    }

    /**
     * 保存足迹
     */
    public function footprint_save()
    {
        $aid = input('param.aid/d');
        $ajaxLogic = new \app\api\logic\AjaxLogic;
        $data = $ajaxLogic->footprint_save($aid);
        if ($data === false) {
            abort(404);
        } else {
            $this->success('ok');
        }
    }

    /**
     * 签到
     * @return [type] [description]
     */
    public function signin_save()
    {
        if (IS_AJAX) {
            $users_id = session('users_id');
            if (empty($users_id)) {
                $this->error('请先登录！');
            }
            $signin_conf = getUsersConfigData('score');
            if (!$signin_conf || !isset($signin_conf['score_signin_status']) || $signin_conf['score_signin_status'] != 1) {
                $this->error('未开启签到配置！');
            }

            //今日签到信息
            $now_time = time();
            $today_start = mktime(0, 0, 0, date("m", $now_time), date("d", $now_time), date("Y", $now_time));
            $today_end = mktime(23, 59, 59, date("m", $now_time), date("d", $now_time), date("Y", $now_time));
            $row = Db::name('users_signin')->where(['users_id' => $users_id, 'add_time' => ['BETWEEN', [$today_start, $today_end]]])->value("id");

            if (!$row) {
                $r = Db::name('users_signin')->add([
                    'users_id' => $users_id,
                    'lang' => $this->home_lang,
                    'add_time' => getTime(),
                ]);
                if (!empty($r)) {
                    $scores_step = $signin_conf['score_signin_score'] ?: 0;
                    Db::name('users')->where(['users_id' => $users_id])->setInc('scores', $scores_step);
                    $users_scores = Db::name('users')->where(['users_id' => $users_id])->value("scores");

                    Db::name('users_score')->add([
                        'type' => 5,//每日签到
                        'users_id' => $users_id,
                        'ask_id' => 0,
                        'reply_id' => 0,
                        'score' => '+' . $scores_step,
                        'devote' => $scores_step,
                        'money' => 0.00,
                        'info' => foreign_lang('users12', $this->home_lang),
                        'lang' => $this->home_lang,
                        'add_time' => getTime(),
                        'update_time' => getTime(),
                        'current_score' => $users_scores,
                    ]);
                    $this->success(foreign_lang('users8', $this->home_lang), null, ['scores' => $users_scores,'score'=>$scores_step,'score_name'=>getUsersConfigData('score.score_name')]);
                }
                $this->error('未知错误', null);
            }
            $this->error(foreign_lang('users9', $this->home_lang), null);
        }
        abort(404);
    }

    /**
     * 登录页面清除session多余文件
     */
    public function clear_session()
    {
        if (IS_AJAX_POST) {
            \think\Session::pause(); // 暂停session，防止session阻塞机制
            clear_session_file(); // 清理过期的data/session文件
        } else {
            abort(404);
        }
    }

    //文章付费
    public function ajax_get_content($aid = 0)
    {
        if (empty($aid)) {
            $this->error('缺少文档id');
        }
        $artData = Db::name('archives')
            ->alias('a')
            ->field('a.channel,a.title,a.restric_type,a.arc_level_id,a.users_price,a.users_score,a.no_vip_pay,a.add_time,a.editor_ai_create,b.content,b.content_ey_m')
            ->join('article_content b', 'a.aid = b.aid')
            ->where([
                'a.aid'     => intval($aid),
                'a.is_del'  => 0,
                'a.status'  => 1,
                'a.arcrank' => ['egt', 0],
            ])
            ->find();
        if (empty($artData)) {
            $this->error('文档不存在或无权访问');
        }
        if (is_dir('./weapp/CompressContent/')) {                            
            try {                    
                $compressLogic = new \weapp\CompressContent\logic\CompressContentLogic;                
                $artData = $compressLogic->decrypt_base64_encode(2,$artData);                        
            } catch (Exception $e) {
            }                
        }
        if (isMobile() && !empty($artData['content_ey_m'])) {
            $artData['content'] = $artData['content_ey_m'];
        }

        $artData['arc_level_value'] = 0;
        if (0 < $artData['arc_level_id']) {
            $artData['arc_level_value'] = Db::name('users_level')->where(['level_id' => $artData['arc_level_id']])->value('level_value');
        }

        if (empty($artData['restric_type'])) { // 免费
            $result['display'] = 0; // 1-显示购买 0-不显示
            // 兼容旧版会员等级限制：未达到等级不返回正文
            if (intval($artData['arc_level_id']) > 0) {
                $users_id  = session('users_id');
                $UsersData = empty($users_id) ? [] : GetUsersLatestData();
                if (empty($UsersData) || floatval($UsersData['level_value']) < floatval($artData['arc_level_value'])) {
                    $result['vipDisplay'] = 1;
                    $result['content']    = '';
                    $this->success('请先登录或升级会员', null, $result);
                }
            }
            $result['content'] = $artData['content'];
        } else {
            /*预览内容*/
            $free_content = '';
            $pay_data = Db::name('article_pay')->field('part_free,free_content')->where('aid', $aid)->find();
            if (!empty($pay_data['part_free'])) {
                $free_content = !empty($pay_data['free_content']) ? $pay_data['free_content'] : '';
                if (3 == $pay_data['part_free']) {
                    // 替换付费内容标签为隐藏提示
                    $free_content = htmlspecialchars_decode($artData['content']);
                    $free_content = free_taglib_handle($free_content, $artData, 'hide');
                    $free_content = htmlspecialchars($free_content);
                } else {
                    // 移除付费内容标签
                    // if (stripos($free_content, '[付费内容开始]') !== false && stripos($free_content, '[/付费内容结束]') !== false) {
                    //     $pattern = '/\[付费内容开始\](.*?)\[\/付费内容结束\]/is';
                    //     $free_content = preg_replace($pattern, '', $free_content);
                    // }
                }
            }
            /*end*/
            $users_id = session('users_id');
            $UsersData = empty($users_id) ? [] : GetUsersLatestData();

            $result['content'] = $free_content;
            $result['users_price'] = get_discount_price($UsersData, $artData['users_price']);

            // 积分购买
            $result['users_score'] = !empty($artData['users_score']) ? intval($artData['users_score']) : 0;
            
            if (1 == $artData['restric_type']) { // 付费
                $result['display'] = 1; // 1-显示购买 0-不显示

                if (!empty($users_id)) {
                    $is_pay = Db::name('article_order')->where(['users_id' => $users_id, 'order_status' => 1, 'product_id' => $aid])->find();
                    // 已经购买
                    if (!empty($is_pay)) {
                        $result['display'] = 0; // 1-显示购买 0-不显示
                        $result['content'] = $artData['content'];
                    }
                }
            }elseif (2 == $artData['restric_type']){ //指定会员 || 非会员付费
                $result['vipDisplay'] = 1;// 1-显示会员限制 0-不显示

                if (!empty($users_id)) {
                    if ($UsersData['level_value'] >= $artData['arc_level_value']){
                        $result['vipDisplay'] = 0;// 1-显示会员限制 0-不显示
                        $result['content'] = $artData['content'];
                    }else if (!empty($artData['no_vip_pay'])){
                        $is_pay = Db::name('article_order')->where(['users_id' => $users_id, 'order_status' => 1, 'product_id' => $aid])->find();
                        // 已经购买
                        if (!empty($is_pay)) {
                            $result['vipDisplay'] = 0; // 1-显示购买 0-不显示
                            $result['content'] = $artData['content'];
                        }else{
                            if (getCmsVersion() < 'v1.6.2') {
                                $result['display'] = 1; // 1-显示购买 0-不显示
                            } else {
                                $result['display'] = 0; // 1-显示购买 0-不显示
                                $result['vipDisplay'] = 1;// 1-显示会员限制 0-不显示
                            }
                        }
                    }
                }
            }elseif (3 == $artData['restric_type']){ //指定会员付费
                $result['vipDisplay'] = 1;// 1-显示会员限制 0-不显示

                $is_pay = Db::name('article_order')->where(['users_id' => $users_id, 'order_status' => 1, 'product_id' => $aid])->find();
                if ($UsersData['level_value'] >= $artData['arc_level_value'] && !empty($is_pay)){
                    $result['vipDisplay'] = 0;// 1-显示会员限制 0-不显示
                    $result['content'] = $artData['content'];
                }else if (empty($is_pay)){
                    if (getCmsVersion() < 'v1.6.2') {
                        $result['display'] = 1; // 1-显示购买 0-不显示
                    } else {
                        $result['display'] = 0; // 1-显示购买 0-不显示
                        $result['vipDisplay'] = 1;// 1-显示会员限制 0-不显示
                    }
                    if ($UsersData['level_value'] >= $artData['arc_level_value']){
                        if (isMobile()){
                            $result['buy_onclick'] = "ey_article_v968479({$aid});";//第一种跳转页面支付
                        }else {
                            $result['buy_onclick'] = "ArticleBuyNow({$aid});";//第二种弹框页支付
                        }
                    }
                }
            }
            // 会员积分购买
            else if (4 === intval($artData['restric_type'])) {
                $result['display'] = 1;

                if (!empty($users_id)) {
                    $is_pay = Db::name('article_order')->where(['users_id' => $users_id, 'order_status' => 1, 'product_id' => $aid])->find();
                    // 已经购买
                    if (!empty($is_pay)) {
                        $result['display'] = 0; // 1-显示购买 0-不显示
                        $result['content'] = $artData['content'];
                    }
                }
            }
        }

        $result['content'] = htmlspecialchars_decode($result['content']);
        if (0 == $result['display']) {
            // 移除付费内容标签
            if (stripos($result['content'], '[付费内容开始]') !== false && stripos($result['content'], '[/付费内容结束]') !== false) {
                $pattern = '/\[付费内容开始\](.*?)\[\/付费内容结束\]/is';
                $result['content'] = preg_replace($pattern, '${1}', $result['content']);
            }
        }
        $titleNew = !empty($artData['title']) ? $artData['title'] : '';
        $result['content'] = img_style_wh($result['content'], $titleNew);
        $result['content'] = handle_subdir_pic($result['content'], 'html');
        if (is_dir('./weapp/Linkkeyword')) {
            $LinkkeywordModel = new \weapp\Linkkeyword\model\LinkkeywordModel();
            if (method_exists($LinkkeywordModel, 'handle_content')) {
                $result['content'] = $LinkkeywordModel->handle_content($result['content']);
            }
        }

        /* AI创作声明 --- chenfy --- 2025.09.04*/
        if (isset($result['content']) && !empty($artData['editor_ai_create'])) {
            $result['content'] .= '<div class="ai-statement">此内容由AI生成</div>';
        }
        /* END */

        $this->success('success', null, $result);
    }

    //获取第三方上传的域名
    public function get_third_domain()
    {
        $weappList = \think\Db::name('weapp')->field('code,data,status,config')->where([
            'status' => 1,
        ])->cache(true, EYOUCMS_CACHE_TIME, 'weapp')
            ->getAllWithIndex('code');
        $third_domain = '';
        if (!empty($weappList['Qiniuyun']) && 1 == $weappList['Qiniuyun']['status']) {
            // 七牛云
            $qnyData = json_decode($weappList['Qiniuyun']['data'], true);
            $third_domain = $qnyData['domain'];
        } else if (!empty($weappList['AliyunOss']) && 1 == $weappList['AliyunOss']['status']) {
            // 到OSS
            $ossData = json_decode($weappList['AliyunOss']['data'], true);
            $third_domain = $ossData['domain'];
        } else if (!empty($weappList['Cos']) && 1 == $weappList['Cos']['status']) {
            // 同步图片到COS
            $CosData = json_decode($weappList['Cos']['data'], true);
            $third_domain = $CosData['domain'];
        } else if (!empty($weappList['AwsOss']) && 1 == $weappList['AwsOss']['status']) {
            // 同步图片到亚马逊S3
            $awsData = json_decode($weappList['AwsOss']['data'], true);
            $third_domain = $awsData['domain'];
        }
        $this->success('success', null, $third_domain);
    }

    /**
     * 获取表单数据信息
     */
//    public function form_submit()
//    {
//        $form_id = input('post.form_id/d');
//        if (IS_POST && !empty($form_id)) {
//            $post = input('post.');
//            $ip = clientIP();
//
//            /*提交间隔限制*/
//            $channel_guestbook_interval = tpSetting('channel_guestbook.channel_guestbook_interval');
//            $channel_guestbook_interval = is_numeric($channel_guestbook_interval) ? intval($channel_guestbook_interval) : 60;
//            if (0 < $channel_guestbook_interval) {
//                $map = array(
//                    'ip' => $ip,
//                    'form_id' => $form_id,
//                    'lang' => $this->home_lang,
//                    'add_time' => array('gt', getTime() - $channel_guestbook_interval),
//                );
//                $count = Db::name('form_list')->where($map)->count('list_id');
//                if ($count > 0) {
//                    if ($this->home_lang == 'cn') {
//                        $msg = '同一个IP在' . $channel_guestbook_interval . '秒之内不能重复提交！';
//                    } else if ($this->home_lang == 'zh') {
//                        $msg = '同一個IP在' . $channel_guestbook_interval . '秒之內不能重複提交！';
//                    } else {
//                        $msg = 'The same IP cannot be submitted repeatedly within ' . $channel_guestbook_interval . ' seconds!';
//                    }
//                    $this->error($msg);
//                }
//            }
//            /*end*/
//
//            $come_url = input('post.come_url/s');
//            $parent_come_url = input('post.parent_come_url/s');
//            $come_url = !empty($parent_come_url) ? $parent_come_url : $come_url;
//            $come_from = input('post.come_from/s');
//            $city = "";
//            $newData = array(
//                'form_id' => $form_id,
//                'ip' => $ip,
//                'aid' => !empty($post['aid']) ? $post['aid'] : 0,
//                'come_from' => $come_from,
//                'come_url' => $come_url,
//                'city' => $city,
//                'lang' => $this->home_lang,
//                'add_time' => getTime(),
//                'update_time' => getTime(),
//            );
//            $data = array_merge($post, $newData);
//            // 数据验证
//            $token = '__token__';
//            foreach ($post as $key => $val) {
//                if (preg_match('/^__token__/i', $key)) {
//                    $token = $key;
//                    break;
//                }
//            }
//            $rule = [
//                'form_id' => 'require|token:' . $token,
//            ];
//            $message = [
//                'form_id.require' => '表单缺少标签属性{$field.hidden}',
//            ];
//            $validate = new \think\Validate($rule, $message);
//            if (!$validate->batch()->check($data)) {
//                $error = $validate->getError();
//                $error_msg = array_values($error);
//                $this->error($error_msg[0]);
//            } else {
//                $formlistRow = [];
//                /*处理是否重复表单数据的提交*/
//                $formdata = $data;
//                foreach ($formdata as $key => $val) {
//                    if (in_array($key, ['form_id', 'ip', 'aid']) || preg_match('/^field_(\d+)$/i', $key)) {
//                        continue;
//                    }
//                    unset($formdata[$key]);
//                }
//                $md5data = md5(serialize($formdata));
//                $data['md5data'] = $md5data;
//                $formlistRow = Db::name('form_list')->field('list_id')->where(['md5data' => $md5data])->find();
//                /*--end*/
//                if (empty($formlistRow)) { // 非重复表单的才能写入数据库
//                    $list_id = Db::name('form_list')->insertGetId($data);
//                    if ($list_id > 0) {
//                        $this->saveFormValue($list_id, $form_id, $post);
//                    }
//                } else {
//                    // 存在重复数据的表单，将在后台显示在最前面
//                    $list_id = $formlistRow['list_id'];
//                    Db::name('form_list')->where('list_id', $list_id)->update([
//                        'is_read' => 0,
//                        'add_time' => getTime(),
//                        'update_time' => getTime(),
//                    ]);
//                }
//
//                if ($this->home_lang == 'cn') {
//                    $msg = '操作成功';
//                } else if ($this->home_lang == 'zh') {
//                    $msg = '操作成功';
//                } else {
//                    $msg = 'success';
//                }
//                $this->success($msg, null, ['form_id' => $form_id, 'list_id' => $list_id]);
//            }
//        }
//
//        $this->error('表单缺少标签属性{$field.hidden}');
//    }

    /**
     *  给指定报名信息添加表单值到 form_value
     * @param int $list_id 报名id
     * @param int $form_id 表单id
     */
//    private function saveFormValue($list_id, $form_id, $post)
//    {
//        /*上传图片或附件*/
//        $arr = explode(',', config('global.image_ext'));
//        foreach ($_FILES as $fileElementId => $file) {
//            try {
//                if (!empty($file['name']) && !is_array($file['name'])) {
//                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
//                    if (in_array($ext, $arr)) {
//                        $uplaod_data = func_common($fileElementId, 'allimg');
//                    } else {
//                        $uplaod_data = func_common_doc($fileElementId, 'files');
//                    }
//                    if (0 == $uplaod_data['errcode']) {
//                        $post[$fileElementId] = $uplaod_data['img_url'];
//                    } else {
////                                return $uplaod_data['errmsg'];
//                        $post[$fileElementId] = '';
//                    }
//                }
//            } catch (\Exception $e) {
//            }
//        }
//        /*end*/
//        $notify_content_arr = []; // 添加站内信所需参数
//        $send_content_str = "";   //发送短信内容
//        $field_list = Db::name("form_field")->where(['form_id' => $form_id])->getField("field_id,field_name,field_type");
//        // post 提交的属性  以 field_id _ 和值的 组合为键名
//        foreach ($post as $key => $val) {
//            if (!strstr($key, 'field_'))
//                continue;
//            $val = addslashes(htmlspecialchars(strip_tags($val)));
//            $field_id = str_replace('field_', '', $key);
//            $field_id = intval($field_id);
//            is_array($val) && $val = implode(',', $val);
//            $val = trim($val);
//            $field_value = stripslashes(filter_line_return($val, '。'));
//            $adddata = array(
//                'form_id' => $form_id,
//                'list_id' => $list_id,
//                'field_id' => $field_id,
//                'field_value' => $field_value,
//                'add_time' => getTime(),
//                'update_time' => getTime(),
//            );
//            Db::name('form_value')->add($adddata);
//            $field_value = get_form_read_value($field_value, $field_list[$field_id]['field_type'], true);
//            array_push($notify_content_arr, $field_value);  //添加站内信数据
//            $send_content_str .= $field_list[$field_id]['field_name'] . '：' . $field_value . '<br/>';
//
//        }
//        /*发送站内信给后台*/
//        SendNotifyMessage($notify_content_arr, 1, 1, 0);
//        /* END */
//        /* 发送短信 */
//        $is_open = Db::name('smtp_tpl')->where(['send_scene' => 1, 'lang' => $this->home_lang])->value('is_open');
//        $smtp_config = tpCache('smtp');
//        if (empty($is_open) || empty($smtp_config['smtp_user']) || empty($smtp_config['smtp_pwd'])) {
//            return false;
//        }
//        $send_email_scene = config('send_email_scene');
//        $scene = $send_email_scene[1]['scene'];
//        $web_name = tpCache('web.web_name');    //title
//        $web_name .= "-表单消息";
//        $html = "<p style='text-align: left;'>{$web_name}</p><p style='text-align: left;'>{$send_content_str}</p>";
//        if (isMobile()) {
//            $html .= "<p style='text-align: left;'>——来源：移动端</p>";
//        } else {
//            $html .= "<p style='text-align: left;'>——来源：电脑端</p>";
//        }
//        // 发送邮件
//        $res = send_email(null, null, $html, $scene);
//        /* END */
//        return $res;
//    }

    //下载付费
    public function get_download($aid = 0)
    {
        $result = [];
        if (empty($aid)) {
            $this->error('缺少文档id');
        }
        $artData = Db::name('archives')->alias('a')
            ->field('a.*, b.content')
            ->join('download_content b', 'a.aid = b.aid', 'left')
            ->where([
                'a.aid'     => intval($aid),
                'a.is_del'  => 0,
                'a.status'  => 1,
                'a.arcrank' => ['egt', 0],
            ])
            ->find();
        if (empty($artData)) {
            $this->error('文档不存在或无权访问');
        }

        $artData['arc_level_value'] = 0;
        if (0 < $artData['arc_level_id']) {
            $artData['arc_level_value'] = Db::name('users_level')->where(['level_id' => $artData['arc_level_id']])->value('level_value');
        }

        $users_id = session('users_id');
        $UsersData = empty($users_id) ? [] : GetUsersLatestData();

        $canDownload = 0;
        $buyVip = 0;
        $msg = '';
        $download_tips = '';
        if (empty($artData['restric_type'])) { // 免费
            // 兼容旧版按会员等级限制下载
            if (0 < intval($artData['arc_level_id'])) {
                if (!empty($UsersData) && floatval($UsersData['level_value']) >= floatval($artData['arc_level_value'])) {
                    $canDownload   = 1;
                    $download_tips = "您已是{$UsersData['level_name']}，可直接下载";
                } else {
                    $buyVip        = 1;
                    $download_tips = '请升级会员后下载';
                }
            } else {
                $canDownload = 1;
            }
        } else if (1 == $artData['restric_type']) { // 付费
            // 查询是否已购买
            $where = [
                'order_status' => 1,
                'product_id' => intval($aid),
                'users_id' => $users_id
            ];
            $count = Db::name('download_order')->where($where)->count();
            if (!empty($count)) {
                $canDownload = 1;
                $download_tips = '您已购买，可直接下载';
            }
        } else if (2 == $artData['restric_type']) { // 会员专享
            if ($UsersData['level_value'] >= $artData['arc_level_value']) {
                $canDownload = 1;
                $download_tips = "您已是{$UsersData['level_name']}，可直接下载";
            } else {
                if (0 == $artData['no_vip_pay']) {
                    $buyVip = 1;
                } else {
                    $where = [
                        'order_status' => 1,
                        'product_id' => intval($aid),
                        'users_id' => $users_id
                    ];
                    $count = Db::name('download_order')->where($where)->count();
                    if (!empty($count)) {
                        $canDownload = 1;
                        $download_tips = '您已购买，可直接下载';
                    }
                }
            }
        } else if (3 == $artData['restric_type']) { // 会员付费
            if ($UsersData['level_value'] >= $artData['arc_level_value']) {
                // 查询是否已购买
                $where = [
                    'order_status' => 1,
                    'product_id' => intval($aid),
                    'users_id' => $users_id
                ];
                $count = Db::name('download_order')->where($where)->count();
                if (!empty($count)) {
                    $canDownload = 1;
                    $download_tips = '您已购买，可直接下载';
                }
            } else {
                $buyVip = 1;
            }
        }
        $result['canDownload'] = $canDownload;
        if (1 == $canDownload) {
            $result['art_content'] = htmlspecialchars_decode($artData['content']);
        }
        $result['download_tips'] = $download_tips;

        if (1 == $buyVip) {
            $result['onclick'] = 'BuyVipClick();';
        } else {
            if (isMobile()) {
                $result['onclick'] = 'ey_download_v866225(' . $aid . ');';//第一种跳转页面支付
            } else {
                $result['onclick'] = 'DownloadBuyNow1655866225(' . $aid . ');';//第二种弹框页支付
            }
        }


        $this->success('success', null, $result);
    }

    public function sendNotice()
    {
        $users_id = input('post.users_id/d');
        $order_id = input('post.order_id/d');
        $send_scene = input('post.send_scene/d');
        if (7 == $send_scene) {
            $params = [
                'users_id' => $users_id,
                'result_id' => $order_id,
            ];
            eyou_send_notice($send_scene, $params);
        }
    }

    public function defaultAuthorize()
    {
        // 扫码绑定必须携带服务端签发的短时 token，禁止直接传 admin_id/users_id 绑定任意账号
        $token = input('param.token/s', '');
        $bind = wechat_bind_token_check($token);
        if (empty($bind)) {
            // 兼容旧链接：仅允许已登录用户为自己的账号重新签发（微信内无会话时请使用后台生成的二维码）
            $admin_id = input('param.admin_id/d', 0);
            $users_id = input('param.users_id/d', 0);
            $admin_info = session('?admin_info') ? session('admin_info') : [];
            $login_admin_id = session('?admin_id') ? intval(session('admin_id')) : (!empty($admin_info['admin_id']) ? intval($admin_info['admin_id']) : 0);
            if (!empty($admin_id) && !empty($login_admin_id) && $login_admin_id === intval($admin_id)) {
                $token = wechat_bind_token_create('admin', $admin_id, 600);
            } else if (!empty($users_id) && !empty(session('users_id')) && intval(session('users_id')) === intval($users_id)) {
                $token = wechat_bind_token_create('users', $users_id, 600);
            } else {
                $this->error('获取用户信息错误，请重新生成二维码并扫码！');
            }
        }

        // 公众号配置
        $conf_wechat = tpSetting("OpenMinicode.conf_wechat");
        $conf_wechat = !empty($conf_wechat) ? json_decode($conf_wechat, true) : [];

        // 回调链接
        $url = urlencode(url('api/Ajax/defaultGetWechatUserinfo', ['token' => $token], true, true));
        // 静默授权链接
        $redirect = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . strval($conf_wechat['appid']) . "&redirect_uri=" . $url . "&response_type=code&scope=snsapi_base&state=eyoucms&#wechat_redirect";

        // 重定向链接
        $this->redirect($redirect);
    }

    public function defaultGetWechatUserinfo()
    {
        // 校验签名 token，admin_id/users_id 只能从 token 中解析，客户端不可指定
        $token = input('param.token/s', '');
        $bind = wechat_bind_token_check($token);
        if (empty($bind)) $this->error('二维码已过期，请重新生成二维码并扫码！');
        $admin_id = ('admin' == $bind['scope']) ? intval($bind['id']) : 0;
        $users_id = ('users' == $bind['scope']) ? intval($bind['id']) : 0;

        // 通过微信code获取用户access_token
        $code = input('param.code/s', '');
        if (empty($code)) $this->error('获取用户信息错误，请重新生成二维码并扫码！');

        // 公众号配置
        $conf_wechat = tpSetting("OpenMinicode.conf_wechat");
        $conf_wechat = !empty($conf_wechat) ? json_decode($conf_wechat, true) : [];

        // 获取微信用户信息
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . strval($conf_wechat['appid']) . '&secret=' . strval($conf_wechat['appsecret']) . '&code=' . $code . '&grant_type=authorization_code';
        $userInfo = json_decode(httpRequest($url), true);
        if (empty($userInfo) || (!empty($userInfo['errcode']) && !empty($userInfo['errmsg']))) $this->error('Code已过期，请重新生成二维码并扫码！');

        // 获取成功则进行关联绑定
        if (!empty($userInfo['openid']) && !empty($admin_id)) {
            // 检测是否已经关联绑定过其他账号
            $row = Db::name('admin')->where(['wechat_open_id'=>$userInfo['openid']])->find();
            if (!empty($row)) {
                $this->error('当前微信已被绑定', null, '', 10);
            }
            // 已绑定其他微信的账号不允许被覆盖，需先解绑
            $currentOpenid = Db::name('admin')->where('admin_id', $admin_id)->getField('wechat_open_id');
            if (!empty($currentOpenid) && $currentOpenid !== $userInfo['openid']) {
                $this->error('当前账号已绑定其他微信，请先解绑', null, '', 10);
            }
            $update = [
                'wechat_open_id' => $userInfo['openid'],
                'update_time' => getTime(),
            ];
            Db::name('admin')->where('admin_id', $admin_id)->update($update);
            // 后台扫码登录插件
            model('AdminWxlogin')->save_data($admin_id, 3, $userInfo);
        }
        else if (!empty($userInfo['openid']) && !empty($users_id)) {
            // 该微信已绑定其他会员时不允许抢占
            $boundUsersId = Db::name('weapp_ask_users_join')->where(['join_open_id' => $userInfo['openid']])->where('users_id', 'neq', $users_id)->value('users_id');
            if (!empty($boundUsersId)) {
                $this->error('当前微信已被绑定', null, '', 10);
            }
            $usersJoin = Db::name('weapp_ask_users_join')->where(['users_id' => $users_id])->find();
            if (!empty($usersJoin)) {
                // 已绑定其他微信的会员不允许被覆盖，需先解绑
                if (!empty($usersJoin['join_open_id']) && $usersJoin['join_open_id'] !== $userInfo['openid']) {
                    $this->error('当前账号已绑定其他微信，请先解绑', null, '', 10);
                }
                $update = [
                    'join_open_id' => $userInfo['openid'],
                    'update_time' => getTime(),
                ];
                Db::name('weapp_ask_users_join')->where('users_id', $users_id)->update($update);
            } else {
                $insert = [
                    'users_id' => intval($users_id),
                    'join_type' => 1,
                    'join_open_id' => $userInfo['openid'],
                    'add_time' => getTime(),
                    'update_time' => getTime(),
                ];
                Db::name('weapp_ask_users_join')->insert($insert);
            }
        }

        // 查询用户是否关注了公众号
        $tokenData = get_wechat_access_token();
        if (!empty($tokenData)) {
            if (!empty($admin_id)) {
                $result['openid'] = Db::name('admin')->where('admin_id', $admin_id)->getField('wechat_open_id');
            } else if (!empty($users_id)) {
                $result['openid'] = Db::name('weapp_ask_users_join')->where('users_id', $users_id)->getField('join_open_id');
            }
            $userInfo = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $tokenData['access_token'] . '&openid=' . $result['openid'] . '&lang=zh_CN';
            $userInfo = json_decode(httpRequest($userInfo), true);
            // 关注则执行关联关注
            if (!empty($userInfo['subscribe']) && $userInfo['openid'] == $result['openid']) {
                if (!empty($admin_id)) {
                    $update = [
                        'wechat_appid' => $conf_wechat['appid'],
                        'wechat_followed' => 1,
                        'union_id' => empty($userInfo['unionid']) ? '' : $userInfo['unionid'],
                        'update_time' => getTime(),
                    ];
                    Db::name('admin')->where('admin_id', $admin_id)->update($update);
                    // 后台扫码登录插件
                    model('AdminWxlogin')->save_data($admin_id, 3, $userInfo);
                } else if (!empty($users_id)) {
                    $update = [
                        'join_appid' => $conf_wechat['appid'],
                        'join_union_id' => $userInfo['unionid'],
                        'join_followed' => 1,
                        'update_time' => getTime(),
                    ];
                    Db::name('weapp_ask_users_join')->where('users_id', $users_id)->update($update);
                }
                // 显示成功信息
                $this->redirect(url('api/Ajax/showSuccessInfo', ['info' => '绑定成功'], true, true));
            }
        } else {
            $this->error($tokenData['msg']);
        }

        // 用户尚未关注公众号，显示公众号二维码
        $this->redirect(url('api/Ajax/showWechatQrCode', ['token' => $token], true, true));
    }

    public function showSuccessInfo()
    {
        $info = input('param.info/s', '');
        $html =<<<EOF
<div style="text-align: center; padding-top: 50%;">
    <div style="color: red; font-size: 60px;">
        {$info}
    </div>
    <div style="margin-top: 50px; font-size: 40px;">
        <span href="javascript:void(0)" id="closeWindow">关闭窗口</span>
    </div>
</div>
<script type="text/javascript">
    var readyFunc = function onBridgeReady() {
        var curid;
        var curAudioId;
        var playStatus = 0;
        
        // 关闭当前webview窗口 - closeWindow
        document.querySelector('#closeWindow').addEventListener('click', function(e){
            WeixinJSBridge.invoke('closeWindow',{
            },function(res){
                //alert(res.err_msg);
           });
        });
    }

    if (typeof WeixinJSBridge === "undefined") {
        document.addEventListener('WeixinJSBridgeReady', readyFunc, false);
    } else {
        readyFunc();
    }
</script>
EOF;
        echo $html;
        exit;
    }

    public function showWechatQrCode()
    {
        // admin_id/users_id 只能从签名 token 中解析，禁止直接传参
        $token = input('param.token/s', '');
        $bind = wechat_bind_token_check($token);
        if (empty($bind)) $this->error('获取用户信息错误，请重新生成二维码并扫码！');
        $admin_id = ('admin' == $bind['scope']) ? intval($bind['id']) : 0;
        $users_id = ('users' == $bind['scope']) ? intval($bind['id']) : 0;

        // 公众号配置
        $conf_wechat = tpSetting("OpenMinicode.conf_wechat");
        $conf_wechat = !empty($conf_wechat) ? json_decode($conf_wechat, true) : [];

        // 生成图片名称及径路
        if (!empty($admin_id)) {
            $qrcodeName = 'wechat_' . md5($admin_id) . ".jpg";
            $qrcodePath = UPLOAD_PATH . 'system/wechat_followed/' . $admin_id . '/';
        } else if (!empty($users_id)) {
            $qrcodeName = 'wechat_' . md5($users_id) . ".jpg";
            $qrcodePath = UPLOAD_PATH . 'system/wechat_followed/users/' . $users_id . '/';
        }
        $qrcodeUrl = $qrcodePath . $qrcodeName;

        // 检测图片是否存在
        if (file_exists($qrcodeUrl)) {
            $showQrcode = request()->domain() . ROOT_DIR . '/' . $qrcodeUrl;
            echo '<div style="color: red; text-align: center; padding-top: 25%;"> <img src=' . $showQrcode . ' style="width: 90%;"> <div style="font-size: 40px;"> 第二步：长按识别并关注公众号 </div> </div>';
            exit;
        }

        // 获取公众号永久二维码
        if (!empty($conf_wechat['appid'])) {
            $tokenData = get_wechat_access_token();
            if (!empty($tokenData)) {
                // 调用微信接口，返回生成二维码的ticket参数
                $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $tokenData['access_token'];
                // 永久二维码，目前暂时没用到 scene_str 里参数值
                $id = !empty($users_id) ? intval($users_id) : intval($admin_id);
                $data = '{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "bind|'.$id.'"}}}';
                // 调用接口获取数据，解析返回的数据
                $ticketData = json_decode(httpRequest($url, 'POST', $data), true);
                if (empty($ticketData['ticket'])) $this->error('数据错误，请重新生成二维码并扫码！');

                // 调用微信接口，返回已生成二维码图片资源
                $ticket = urlencode($ticketData['ticket']);
                // 调用接口获取图片资源
                $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $ticket;
                $result = httpRequest($url, 'GET');

                // 创建指定文件夹
                tp_mkdir($qrcodePath);
                // 拼装完成图片路径
                $qrcodeUrl = $qrcodePath . "/" . $qrcodeName;
                // 将资源加载到图片并返回
                if (@file_put_contents($qrcodeUrl, $result)) {
                    $showQrcode = request()->domain() . ROOT_DIR . '/' . $qrcodeUrl;
                    echo '<div style="color: red; text-align: center; padding-top: 25%;"> <img src=' . $showQrcode . ' style="width: 90%;"> <div style="font-size: 40px;"> 第二步：长按识别并关注公众号 </div> </div>';
                    exit;
                }
            } else {
                $this->error($tokenData['msg']);
            }
        }
    }

    //留言回复列表
    public function get_formreply_list()
    {
        if (!IS_AJAX) {
            abort(404);
        }

        $page = input('page/d', 1);
        $pagesize = input('pagesize/d', 20);
        $typeid = input('typeid/d', 0);
        $totalpage = input('totalpage/d', 1);
        $ordermode = input('ordermode/s', 'desc');
        // 白名单：排序方式只允许 asc/desc，防止拼接进模板标签造成模板注入
        $ordermode = is_string($ordermode) ? strtolower(trim($ordermode)) : 'desc';
        if (!in_array($ordermode, ['asc', 'desc'], true)) {
            $ordermode = 'desc';
        }
        $tagid = 'block001';
        if (empty($typeid) || empty($page)) {
            $this->error('参数有误');
        }

        $tagFormreply = new \think\template\taglib\eyou\TagFormreply;
        $attr_list = $tagFormreply->getFormreply($typeid, $page,$pagesize,$ordermode);
        if (!empty($attr_list) ) {
            $tpl_content = '';
            $filename = './template/' . THEME_STYLE_PATH . '/' . 'system/formreply_' . $tagid . '.' . \think\Config::get('template.view_suffix');
            if (!file_exists($filename)) {
                $data['code'] = -1;
                $data['msg'] = "模板追加文件 formreply_{$tagid}.htm 不存在！";
                $this->error("标签模板不存在", null, $data);
            } else {
                $tpl_content = @file_get_contents($filename);
            }
            if (empty($tpl_content)) {
                $data['code'] = -1;
                $data['msg'] = "模板追加文件 formreply_{$tagid}.htm 没有HTML代码！";
                $this->error("标签模板不存在", null, $data);
            }

            /*拼接完整的formreply标签语法*/
            $innertext = "{eyou:formreply typeid='{$typeid}' page='{$page}' pagesize='{$pagesize}' ordermode='{$ordermode}'}";
            $innertext .= $tpl_content;
            $innertext .= "{/eyou:formreply}";
            /*--end*/
            $msg = $this->display($innertext); // 渲染模板标签语法
            $data['msg'] = $msg;

            //是否到了最终页
            if ($totalpage == $page) {
                $data['lastpage'] = 1;
            }
        }

        $this->success('请求成功', null, $data);
    }
}