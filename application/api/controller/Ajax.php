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
    public function _initialize() {
        parent::_initialize();
    }

    /**
     * 获取下级地区
     */
    public function get_region()
    {
        if (IS_AJAX) {
            $pid  = input('pid/d', 0);
            $res = Db::name('region')->where('parent_id',$pid)->select();
            $this->success('请求成功', null, $res);
        }
    }

    /**
     * 内容页浏览量的自增接口
     */
    public function arcclick()
    {
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        if (IS_AJAX) {
            $click = 0;
            $aid = input('aid/d', 0);
            $type = input('type/s', '');
            if ($aid > 0) {
                $archives_db = Db::name('archives');
                if ('view' == $type) {
                    $archives_db->where(array('aid'=>$aid))->setInc('click'); 
                }
                $click = $archives_db->where(array('aid'=>$aid))->getField('click');
            }
            echo($click);
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
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        if (IS_AJAX) {
            $downcount = 0;
            $aid = input('aid/d', 0);
            if ($aid > 0) {
                $downcount = Db::name('archives')->where(array('aid'=>$aid))->getField('downcount');
            }
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
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        if (IS_AJAX) {
            $collectnum = 0;
            $aid = input('aid/d', 0);
            if ($aid > 0) {
                $collectnum = Db::name('users_collection')->where(array('aid'=>$aid))->count();
            }
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
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        if (IS_AJAX) {
            $unread_notice_num = 0;
            $users_id = session('users_id');
            if ($users_id > 0) {
                $unread_notice_num = Db::name('users')->where(array('users_id'=>$users_id))->value('unread_notice_num');
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
        !empty($tagid) && $tagid = preg_replace("/[^a-zA-Z0-9-_]/",'', $tagid);
        !empty($tagidmd5) && $tagidmd5 = preg_replace("/[^a-zA-Z0-9_]/",'', $tagidmd5);

        if (empty($tagid) || empty($pnum) || empty($tagidmd5)) {
            $this->error('参数有误');
        }

        $data = [
            'code' => 1,
            'msg'   => '',
            'lastpage'  => 0,
        ];

        $arcmulti_db = Db::name('arcmulti');
        $arcmultiRow = $arcmulti_db->where(['tagid'=>$tagidmd5])->find();
        if(!empty($arcmultiRow) && !empty($arcmultiRow['querysql']))
        {
            // arcpagelist标签属性pagesize优先级高于arclist标签属性pagesize
            if (0 < intval($pagesize)) {
                $arcmultiRow['pagesize'] = $pagesize;
            }

            // 取出属性并解析为变量
            $attarray = unserialize(stripslashes($arcmultiRow['attstr']));
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
                $filename = './template/'.THEME_STYLE_PATH.'/'.'system/arclist_'.$tagid.'.'.\think\Config::get('template.view_suffix');
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
                    if (in_array($key, ['tagid','offset','row'])) {
                        continue;
                    }
                    $innertext .= " {$key}='{$val}'";
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
        if (IS_AJAX && !preg_match('/^(admin_|users)/i', $name)) {
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
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        if (IS_AJAX) {
            $type = input('param.type/s', 'default');
            $img = input('param.img/s');
            $afterhtml = input('param.afterhtml/s');
            $users_id = session('users_id');
            if ('login' == $type) {
                if (!empty($users_id)) {
                    $currentstyle = input('param.currentstyle/s');
                    $users = M('users')->field('username,nickname,head_pic')
                        ->where([
                            'users_id'  => $users_id,
                            'lang'      => $this->home_lang,  
                        ])->find();
                    if (!empty($users)) {
                        $nickname = $users['nickname'];
                        if (empty($nickname)) {
                            $nickname = $users['username'];
                        }
                        $head_pic = get_head_pic(htmlspecialchars_decode($users['head_pic']));
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
                $this->success('请先登录', null, ['ey_is_login'=>0]);
            }
            else if ('reg' == $type)
            {
                if (!empty($users_id)) {
                    $users['ey_is_login'] = 1;
                } else {
                    $users['ey_is_login'] = 0;
                }
                $this->success('请求成功', null, $users);
            }
            else if ('logout' == $type)
            {
                if (!empty($users_id)) {
                    $users['ey_is_login'] = 1;
                } else {
                    $users['ey_is_login'] = 0;
                }
                $this->success('请求成功', null, $users);
            }
            else if ('cart' == $type)
            {
                if (!empty($users_id)) {
                    $users['ey_is_login'] = 1;
                    $users['ey_cart_num_20191212'] = Db::name('shop_cart')->where(['users_id'=>$users_id])->sum('product_num');
                } else {
                    $users['ey_is_login'] = 0;
                    $users['ey_cart_num_20191212'] = 0;
                }
                $this->success('请求成功', null, $users);
            }
            $this->error('访问错误');
        } else {
            abort(404);
        }
    }

    /**
     * 获取用户信息
     */
    public function get_tag_user_info()
    {
        \think\Session::pause(); // 暂停session，防止session阻塞机制
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
                        'a.lang'     => $this->home_lang,
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
                $head_pic = get_head_pic(htmlspecialchars_decode($users['head_pic']));
                $users['head_pic'] = func_preg_replace(['http://thirdqq.qlogo.cn'], ['https://thirdqq.qlogo.cn'], $head_pic);
                $users['url'] = url('user/Users/centre');
                $dtypes = [];
                foreach ($users as $key => $val) {
                    $html_key = md5($key.'-'.$t_uniqid);
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
                    'ey_is_login'   => 1,
                    'users'  => $users,
                    'dtypes'  => $dtypes,
                ];
                $this->success('请求成功', null, $data);
            }
            $this->success('请先登录', null, ['ey_is_login'=>0]);
        }
        $this->error('访问错误');
    }

    // 验证码获取
    public function vertify()
    {
        $time = getTime();
        $type = input('param.type/s', 'default');
        $token = input('param.token/s', '');
        $configList = \think\Config::get('captcha');
        $captchaArr = array_keys($configList);
        if (in_array($type, $captchaArr)) {
            /*验证码插件开关*/
            $admin_login_captcha = config('captcha.'.$type);
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
     * 邮箱发送
     */
    public function send_email()
    {
        // 超时后，断掉邮件发送
        function_exists('set_time_limit') && set_time_limit(10);

        $type = input('param.type/s');
        
        // 留言发送邮件
        if (IS_AJAX_POST && 'gbook_submit' == $type) {
            $tid = input('param.tid/d');
            $aid = input('param.aid/d');

            $send_email_scene = config('send_email_scene');
            $scene = $send_email_scene[1]['scene'];

            $web_name = tpCache('web.web_name');
            // 判断标题拼接
            $arctype  = M('arctype')->field('typename')->find($tid);
            $web_name = $arctype['typename'].'-'.$web_name;

            // 拼装发送的字符串内容
            $row = M('guestbook_attribute')->field('a.attr_name, b.attr_value')
                ->alias('a')
                ->join('__GUESTBOOK_ATTR__ b', 'a.attr_id = b.attr_id AND a.typeid = '.$tid, 'LEFT')
                ->where([
                    'b.aid' => $aid,
                ])
                ->order('a.attr_id sac')
                ->select();
            $content = '';
            foreach ($row as $key => $val) {
                if (preg_match('/(\.(jpg|gif|png|bmp|jpeg|ico|webp))$/i', $val['attr_value'])) {
                    if (!stristr($val['attr_value'], '|')) {
                        $val['attr_value'] = $this->request->domain().handle_subdir_pic($val['attr_value']);
                        $val['attr_value'] = "<a href='".$val['attr_value']."' target='_blank'><img src='".$val['attr_value']."' width='150' height='150' /></a>";
                    }
                } else {
                    $val['attr_value'] = str_replace(PHP_EOL, ' | ', $val['attr_value']);
                }
                $content .= $val['attr_name'] . '：' . $val['attr_value'].'<br/>';
            }
            $html = "<p style='text-align: left;'>{$web_name}</p><p style='text-align: left;'>{$content}</p>";
            if (isMobile()) {
                $html .= "<p style='text-align: left;'>——来源：移动端</p>";
            } else {
                $html .= "<p style='text-align: left;'>——来源：电脑端</p>";
            }
            
            // 发送邮件
            $res = send_email(null,null,$html, $scene);
            if (intval($res['code']) == 1) {
                $this->success($res['msg']);
            } else {
                $this->error($res['msg']);
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

        // 发送手机验证码
        if (IS_AJAX_POST) {
            $post = input('post.');
            $source = !empty($post['source']) ? $post['source'] : 0;
            if (isset($post['scene']) && in_array($post['scene'], [5, 6])) {
                if (empty($post['mobile'])) return false;
                /*发送并返回结果*/
                $data = $post['data'];
                //兼容原先消息通知的发送短信的逻辑
                //查询消息通知模板的内容
                $sms_type = tpCache('sms.sms_type') ? : 1;
                $tpl_content = Db::name('sms_template')->where(["send_scene"=> $post['scene'],"sms_type"=> $sms_type])->value('tpl_content');
                if (!$tpl_content) return false;
                $preg_res = preg_match('/订单/', $tpl_content);
                switch ($data['type']) {
                    case '1':
                        $content = $preg_res ? '待发货' : '您有新的待发货订单';
                        break;
                    case '2':
                        $content = $preg_res ? '待收货' : '您有新的待收货订单';
                        break;
                    default:
                        $content = '';
                        break;
                }
                $Result = sendSms($post['scene'], $post['mobile'], array('content'=>$content));
                if (intval($Result['status']) == 1) {
                    $this->success('发送成功！');
                } else {
                    $this->error($Result['msg']);
                }
                /* END */
            } else {
                if (isset($post['type']) && in_array($post['type'], ['users_mobile_reg','users_mobile_login'])) {
                    $post['is_mobile'] = 'true';
                }
                $mobile = !empty($post['mobile']) ? $post['mobile'] : session('mobile');
                $is_mobile = !empty($post['is_mobile']) ? $post['is_mobile'] : false;
                if (empty($mobile)) $this->error('请先绑定手机号码');
                if ('true' === $is_mobile) {
                    /*是否存在手机号码*/
                    $where = [
                        'mobile' => $mobile
                    ];
                    $users_id = session('users_id');
                    if (!empty($users_id)) $where['users_id'] = ['NEQ', $users_id];
                    $Result = Db::name('users')->where($where)->count();
                    /* END */
                    if (0 == $post['source']) {
                        if (!empty($Result)) $this->error('手机号码已注册');
                    } else if (2 == $post['source']) {
                        if (empty($Result)) $this->error('手机号码未注册');
                    } else if (4 == $post['source']) {
                        if (empty($Result)) $this->error('手机号码不存在');
                    } else {
                        if (!empty($Result)) $this->error('手机号码已存在');
                    }
                }

                /*是否允许再次发送*/
                $where = [
                    'mobile'   => $mobile,
                    'source'   => $source,
                    'status'   => 1,
                    'is_use'   => 0,
                    'add_time' => ['>', getTime() - 120]
                ];
                $Result = Db::name('sms_log')->where($where)->order('id desc')->count();

                if (!empty($Result) && false == config('sms_debug')) $this->error('120秒内只能发送一次！');
                /* END */

                /*图形验证码判断*/
                if (!empty($post['IsVertify']) || (isset($post['type']) && in_array($post['type'], ['users_mobile_reg','users_mobile_login']))) {
                    if (empty($post['vertify'])) $this->error('请输入图形验证码！');
                    $verify = new \think\Verify();
                    if (!$verify->check($post['vertify'], $post['type'])) $this->error('图形验证码错误！', null, ['code'=>'vertify']);
                }
                /* END */

                /*发送并返回结果*/
                $Result = sendSms($source, $mobile, array('content' => mt_rand(1000, 9999)));
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
    public function get_arcrank($aid = '', $vars = '')
    {
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        $aid = intval($aid);
        $vars = intval($vars);
        if ((IS_AJAX || !empty($vars)) && !empty($aid)) {
            // 用户ID
            $users_id = session('users_id');
            // 文章查看所需等级值
            $Arcrank = M('archives')->alias('a')
                ->field('a.users_id, a.arcrank, b.level_value, b.level_name')
                ->join('__USERS_LEVEL__ b', 'a.arcrank = b.level_value', 'LEFT')
                ->where(['a.aid' => $aid])
                ->find();

            if (!empty($users_id)) {
                // 会员级别等级值
                $UsersDataa = Db::name('users')->alias('a')
                    ->field('a.users_id,b.level_value,b.level_name')
                    ->join('__USERS_LEVEL__ b', 'a.level = b.level_id', 'LEFT')
                    ->where(['a.users_id'=>$users_id])
                    ->find();
                if (0 == $Arcrank['arcrank']) {
                    if (IS_AJAX) {
                        $this->success('允许查阅！');
                    } else {
                        return true;
                    }
                }else if (-1 == $Arcrank['arcrank']) {
                    $is_admin = session('?admin_id') ? 1 : 0;
                    $param_admin_id = input('param.admin_id/d');
                    if ($users_id == $Arcrank['users_id']) {
                        if (IS_AJAX) {
                            $this->success('允许查阅！', null, ['is_admin'=>$is_admin, 'msg'=>'待审核稿件，仅限自己查看！']);
                        } else {
                            return true;
                        }
                    }else if(!empty($is_admin) && !empty($param_admin_id)){
                        if (IS_AJAX) {
                            $this->success('允许查阅！', null, ['is_admin'=>$is_admin, 'msg'=>'待审核稿件，仅限管理员查看！']);
                        } else {
                            return true;
                        }
                    }else{
                        $msg = '待审核稿件，你没有权限阅读！';
                    }
                }else if ($UsersDataa['level_value'] < $Arcrank['level_value']) {
                    $msg = '内容需要【'.$Arcrank['level_name'].'】才可以查看，您为【'.$UsersDataa['level_name'].'】，请先升级！';
                }else{
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
            }else{
                if (0 == $Arcrank['arcrank']) {
                    if (IS_AJAX) {
                        $this->success('允许查阅！');
                    } else {
                        return true;
                    }
                }else if (-1 == $Arcrank['arcrank']) {
                    $is_admin = session('?admin_id') ? 1 : 0;
                    $param_admin_id = input('param.admin_id/d');
                    if (!empty($is_admin) && !empty($param_admin_id)) {
                        $this->success('允许查阅！', null, ['is_admin'=>$is_admin, 'msg'=>'待审核稿件，仅限管理员查看！']);
                    } else {
                        $msg = '待审核稿件，你没有权限阅读！';
                    }
                }else if (!empty($Arcrank['level_name'])) {
                    $msg = '文章需要【'.$Arcrank['level_name'].'】才可以查看，游客不可查看，请登录！';
                }else{
                    $msg = '游客不可查看，请登录！';
                }
                if (IS_AJAX) {
                    $data = [
                        'is_login' => !empty($users_id) ? 1 : 0,
                        'gourl' => url('user/Users/login'),
                    ];
                    $this->error($msg, null, $data);
                } else {
                    return $msg;
                }
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
        $this->error('暂时没用上！');
        if (IS_AJAX_POST) {
            $htmlcode = input('post.htmlcode/s');
            $htmlcode = htmlspecialchars_decode($htmlcode);
            $htmlcode = preg_replace('/<\?(\s*)php(\s+)/i', '', $htmlcode);

            $attarray = input('post.attarray/s');
            $attarray = htmlspecialchars_decode($attarray);
            $attarray = json_decode(base64_decode($attarray));

            /*拼接完整的memberlist标签语法*/
            $eyou = new \think\template\taglib\Eyou('');
            $tagsList = $eyou->getTags();
            $tagsAttr = $tagsList['memberlist'];
            
            $innertext = "{eyou:memberlist";
            foreach ($attarray as $key => $val) {
                if (!in_array($key, $tagsAttr) || in_array($key, ['js'])) {
                    continue;
                }
                $innertext .= " {$key}='{$val}'";
            }
            $innertext .= " js='on'}";
            $innertext .= $htmlcode;
            $innertext .= "{/eyou:memberlist}";
            /*--end*/
            $msg = $this->display($innertext); // 渲染模板标签语法
            $data['msg'] = $msg;

            $this->success('读取成功！', null, $data);
        }
        $this->error('加载失败！');
    }

    /**
     * 发布或编辑文档时，百度自动推送
     */
    public function push_zzbaidu($url = '', $type = 'add')
    {
        $msg = '百度推送URL失败！';
        if (IS_AJAX_POST) {
            \think\Session::pause(); // 暂停session，防止session阻塞机制

            // 获取token的值：http://ziyuan.baidu.com/linksubmit/index?site=http://www.eyoucms.com/
            $sitemap_zzbaidutoken = config('tpcache.sitemap_zzbaidutoken');
            if (empty($sitemap_zzbaidutoken)) {
                $this->error('尚未配置实时推送Url的token！', null, ['code'=>0]);
            } else if (!function_exists('curl_init')) {
                $this->error('请开启php扩展curl_init', null, ['code'=>1]);
            }

            $urlsArr[] = $url;
            $type = ('edit' == $type) ? 'update' : 'urls';

            if (is_http_url($sitemap_zzbaidutoken)) {
                $searchs = ["/urls?","/update?"];
                $replaces = ["/{$type}?", "/{$type}?"];
                $api = str_replace($searchs, $replaces, $sitemap_zzbaidutoken);
            } else {
                $api = 'http://data.zz.baidu.com/'.$type.'?site='.$this->request->host(true).'&token='.trim($sitemap_zzbaidutoken);
            }

            $ch = curl_init();
            $options =  array(
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

    /*
     * 视频权限播放逻辑
     */
    public function video_logic()
    {
        \think\Session::pause(); // 暂停session，防止session阻塞机制
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
                $UsersID = $UsersData['users_id'];
                $result['status_value'] = 0; // status_value 0-所有人免费 1-所有人付费 2-会员免费 3-会员付费
                $result['status_name'] = ''; //status_name 要求会员等级时会员级别名称
                $result['play_auth'] = 0; //播放权限
                $result['vip_status'] = 0; //status_value=3时使用 vip_status=1则已升级会员暂未购买

                /*是否需要付费*/
                if (0 < $archivesInfo['users_price'] && empty($archivesInfo['users_free'])) {
                    if (empty($archivesInfo['arc_level_id'])){
                        //不限会员 付费
                        $result['status_value'] = 1;
                    }else{
                        //3-限制会员 付费
                        $result['status_value'] = 3;
                        if ($archivesInfo['level_value'] <= $UsersData['level_value']){
                            $result['vip_status'] = 1;//已升级会员未购买
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
                                if (1 == $result['vip_status']){
                                    $result['play_auth'] = 1;
                                    $result['vip_status'] = 3;//已升级会员已经购买
                                }else{
                                    $result['play_auth'] = 0;
                                    $result['vip_status'] = 2;//未升级会员已经购买
                                }
                            }else{
                                $result['play_auth'] = 1;
                                $result['vip_status'] = 4;//不限会员已经购买
                            }
                        }
                    }
                }else{
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
                if (in_array($result['status_value'], [1,3])){ // 所有人、会员付费
                    $is_pay = Db::name('media_order')->where($where)->count();
                }
                $result['is_pay'] = $is_pay;

                if (in_array($result['status_value'], [2,3])){ // 已满足会员级别要求
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

                if ($result['status_value'] == 0){
                    $result['button'] = '免费';
                    $result['status_name'] = '免费';
                }else if ($result['status_value'] == 1){ // 所有人付费
                    $result['button'] = '付费';
                    if (!empty($is_pay)){
                        $result['button'] = '观看';
                    }
                }else if ($result['status_value'] == 2){
                    $result['button'] = 'VIP';
                    if (!empty($result['play_auth'])){
                        $result['button'] = '观看';
                    }
                }else if ($result['status_value'] == 3){
                    // if(1 == $result['vip_status']){
                    //     $result['button'] = '立即购买';
                    //     $result['button_url'] = 'MediaOrderBuy_1592878548();';
                    // }else
                    if (2 == $result['vip_status']){
                        $result['button'] = 'VIP';
                        $result['button_url'] = "window.location.href = '" . url('user/Level/level_centre') . "'";
                    } else if (3 == $result['vip_status']) {
                        $result['button'] = '观看';
                    }else{
                        $result['button'] = 'VIP付费';
                        $result['button_url'] = "window.location.href = '" . url('user/Level/level_centre') . "'";
                    }
                    // if (!empty($is_pay) && !empty($result['vip_status'])){
                    //     $result['button'] = '观看';
                    // }
                }
                if ('观看' == $result['button']){
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
        if (!empty($id) && !empty($users_id)) {
            $count = Db::name('users_notice_read')
                ->where(['id' => $id])
                ->value("id");
            if (empty($count)) $this->error('未知错误！');

            //未读消息数-1
            $unread_num = Db::name('users')->where(['users_id' => $users_id])->value("unread_notice_num");
            if ($unread_num>0){
                $unread_num = $unread_num-1;
                Db::name('users')->where(['users_id' => $users_id])->update(['unread_notice_num'=>$unread_num]);
            }
            Db::name('users_notice_read')->where(['id'=>$id])->update(['is_read'=>1]);
            $this->success('保存成功',null,['unread_num'=>$unread_num]);
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
                'users_id'  => $users_id,
                'aid'   => $aid,
            ])->find();
            if (empty($row)) {
                $archivesInfo = Db::name('archives')->field('aid,title,litpic,channel,typeid')->find($aid);
                if (!empty($archivesInfo)) {
                    $r = Db::name('users_collection')->add([
                        'users_id'  => $users_id,
                        'title' => $archivesInfo['title'],
                        'aid' => $aid,
                        'litpic' => $archivesInfo['litpic'],
                        'channel' => $archivesInfo['channel'],
                        'typeid' => $archivesInfo['typeid'],
                        'lang'  => $this->home_lang,
                        'add_time'  => getTime(),
                        'update_time' => getTime(),
                    ]);
                    if (!empty($r)) {
                        Db::name('archives')->where('aid', $aid)->setInc('collection');
                        $this->success('收藏成功', null, ['opt'=>'add']);
                    }
                }
            } else {
                $r = Db::name('users_collection')->where([
                    'users_id'  => $users_id,
                    'aid' => $aid,
                ])->delete();
                Db::name('archives')->where('aid', $aid)->setDec('collection');
                $this->success('取消成功', null, ['opt'=>'cancel']);
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
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        if (IS_AJAX) {
            $aid = input('param.aid/d');
            $users_id = session('users_id');
            $total = Db::name('users_collection')->where([
                'aid'   => $aid,
            ])->count();
            if (!empty($users_id)) {
                $count = Db::name('users_collection')->where([
                    'aid'   => $aid,
                    'users_id'  => $users_id,
                ])->count();
                if (!empty($count)) {
                    $this->success('已收藏', null, ['total'=>$total]);
                }
            }
            $this->error('未收藏', null, ['total'=>$total]);
        }
        abort(404);
    }

    /**
     * 保存足迹
     */
    public function footprint_save()
    {
        \think\Session::pause();
        $users_id = session('users_id');
        $aid = input('param.aid/d');
        if (IS_AJAX && !empty($aid) && !empty($users_id)) {
            //查询标题模型缩略图信息
            $arc = Db::name('archives')
                ->field('aid,channel,typeid,title,litpic')
                ->find($aid);
            if (!empty($arc)) {
                $count = Db::name('users_footprint')->where([
                    'users_id' => $users_id,
                    'aid'      => $aid,
                ])->count();

                if (empty($count)) {
                    // 足迹记录条数限制
                    $user_footprint_limit = config('global.user_footprint_limit');
                    if (!$user_footprint_limit) {
                        $user_footprint_limit = 20;
                        config('global.user_footprint_limit',$user_footprint_limit);
                    }
                    $user_footprint_record = Db::name('users_footprint')->where(['users_id'=>$users_id])->count("id");
                    if ($user_footprint_record == $user_footprint_limit) {
                        Db::name('users_footprint')->where(['users_id' => $users_id])->order("update_time ASC")->limit(1)->delete();
                    }elseif ($user_footprint_record > $user_footprint_limit) {
                        $del_count = $user_footprint_record-$user_footprint_limit+1;
                        $del_ids = Db::name('users_footprint')->field("id")->where(['users_id' => $this->users_id])->order("update_time ASC")->limit($del_count)->select();
                        $del_ids = get_arr_column($del_ids,'id');
                        Db::name('users_footprint')->where(['id' => ['IN',$del_ids]])->delete();
                    }

                    $arc['users_id']    = $users_id;
                    $arc['lang']        = $this->home_lang;
                    $arc['add_time']    = getTime();
                    $arc['update_time'] = getTime();
                    Db::name('users_footprint')->add($arc);
                } else {
                    Db::name('users_footprint')->where([
                        'users_id' => $users_id,
                        'aid'      => $aid
                    ])->update([
                        'update_time' => getTime(),
                    ]);
                }
                $this->success('保存成功');
            }
        } else if (IS_AJAX && !empty($aid) && empty($users_id)) {
            $this->success('请求成功');
        }
        abort(404);
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
            $today_start = mktime(0,0,0,date("m",$now_time),date("d",$now_time),date("Y",$now_time));
            $today_end = mktime(23,59,59,date("m",$now_time),date("d",$now_time),date("Y",$now_time));
            $row = Db::name('users_signin')->where(['users_id'=>$users_id,'add_time'=>['BETWEEN',[$today_start,$today_end]]])->value("id");

            if (!$row) {
                $r = Db::name('users_signin')->add([
                    'users_id'  => $users_id,
                    'lang'  => $this->home_lang,
                    'add_time'  => getTime(),
                ]);
                if (!empty($r)) {
                    $scores_step = $signin_conf['score_signin_score'] ?:0;
                    Db::name('users')->where(['users_id'=>$users_id])->setInc('scores',$scores_step);
                    $users_scores = Db::name('users')->where(['users_id'=>$users_id])->value("scores");

                    Db::name('users_score')->add([
                        'type'  => 5,//每日签到
                        'users_id'  => $users_id,
                        'ask_id'  => 0,
                        'reply_id'  => 0,
                        'score'  => $scores_step,
                        'devote'  => $scores_step,
                        'money'  => 0.00,
                        'info'  => '每日签到',
                        'lang'  => $this->home_lang,
                        'add_time'  => getTime(),
                        'update_time'  => getTime(),
                    ]);
                    $this->success('签到成功', null,['scores'=>$users_scores]);
                }
                $this->error('未知错误', null);
            }
            $this->error('今日已签过到', null);
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
            $ajaxLogic = new \app\admin\logic\AjaxLogic;
            $ajaxLogic->clear_session_file();
        } else {
            abort(404);
        }
    }

    //文章付费
    public function ajax_get_content($aid=0)
    {
        \think\Session::pause(); // 暂停session，防止session阻塞机制
        if (empty($aid)){
            $this->error('缺少文档id');
        }
        $artData = Db::name('archives')
            ->alias('a')
            ->field('a.users_price, b.content')
            ->join('article_content b','a.aid = b.aid')
            ->where('a.aid',$aid)
            ->find();
        if (0 < $artData['users_price']) { // 付费阅读
            $users_id = session('users_id');
            $pay_data = Db::name('article_pay')->field('part_free,free_content')->where('aid',$aid)->find();
            $free_content = '';
            if (!empty($pay_data['part_free'])) { // 允许试看
                $free_content = !empty($pay_data['free_content']) ? $pay_data['free_content'] : '';
            }
            if (empty($users_id)) {
                $result['display'] = 1; // 1-显示购买 0-不显示
                $result['content'] = $free_content;
            } else {
                $is_pay = Db::name('article_order')->where(['users_id'=>$users_id,'order_status'=>1,'product_id'=>$aid])->find();
                if (empty($is_pay)){ // 没有购买
                    $result['display'] = 1;// 1-显示购买 0-不显示
                    $result['content'] = $free_content;
                }else{ // 已经购买
                    $result['display'] = 0;
                    $result['content'] = $artData['content'];
                }
            }
        }
        else { // 免费阅读
            $result['display'] = 0; // 1-显示购买 0-不显示
            $result['content'] = $artData['content'];
        }

        $result['content'] = htmlspecialchars_decode($result['content']);
        $titleNew = !empty($data['title']) ? $data['title'] : '';
        $result['content'] = img_style_wh($result['content'], $titleNew);
        $result['content'] = handle_subdir_pic($result['content'], 'html');

        $this->success('success', null,$result);
    }

    //获取第三方上传的域名
    public function get_third_domain()
    {
        $weappList = \think\Db::name('weapp')->field('code,data,status,config')->where([
            'status'    => 1,
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
        }
        $this->success('success', null,$third_domain);

    }
}