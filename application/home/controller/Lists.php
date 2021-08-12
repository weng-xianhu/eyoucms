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
use think\Verify;

class Lists extends Base
{
    // 模型标识
    public $nid = '';
    // 模型ID
    public $channel = '';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 栏目列表
     */
    public function index($tid = '')
    {
        $param = input('param.');

        /*获取当前栏目ID以及模型ID*/
        $page_tmp = input('param.page/s', 0);
        if (empty($tid) || !is_numeric($page_tmp)) {
            abort(404, '页面不存在');
        }

        $map = [];
        /*URL上参数的校验*/
/*        $seo_pseudo = config('ey_config.seo_pseudo');
        $url_screen_var = config('global.url_screen_var');
        if (!isset($param[$url_screen_var]) && 3 == $seo_pseudo)
        {
            if (stristr($this->request->url(), '&c=Lists&a=index&')) {
                abort(404,'页面不存在');
            }
            $map = array('a.dirname'=>$tid);
        }
        else if (isset($param[$url_screen_var]) || 1 == $seo_pseudo || (2 == $seo_pseudo && isMobile()))
        {
            $seo_dynamic_format = config('ey_config.seo_dynamic_format');
            if (1 == $seo_pseudo && 2 == $seo_dynamic_format && stristr($this->request->url(), '&c=Lists&a=index&')) {
                abort(404,'页面不存在');
            } else if (!is_numeric($tid) || strval(intval($tid)) !== strval($tid)) {
                abort(404,'页面不存在');
            }
            $map = array('a.id'=>$tid);
            
        }else if (2 == $seo_pseudo){ // 生成静态页面代码
            
            $map = array('a.id'=>$tid);
        }*/
        /*--end*/
        if (!is_numeric($tid) || strval(intval($tid)) !== strval($tid)) {
            $map = array('a.dirname' => $tid);
        } else {
            $map = array('a.id' => intval($tid));
        }
        $map['a.is_del'] = 0; // 回收站功能
        $map['a.lang']   = $this->home_lang; // 多语言
        $row             = Db::name('arctype')->field('a.id, a.current_channel, b.nid')
            ->alias('a')
            ->join('__CHANNELTYPE__ b', 'a.current_channel = b.id', 'LEFT')
            ->where($map)
            ->find();
        if (empty($row)) {
            abort(404, '页面不存在');
        }
        $tid           = $row['id'];
        $this->nid     = $row['nid'];
        $this->channel = intval($row['current_channel']);
        /*--end*/

        $result = $this->logic($tid); // 模型对应逻辑

        $eyou       = array(
            'field' => $result,
        );
        $this->eyou = array_merge($this->eyou, $eyou);
        $this->assign('eyou', $this->eyou);

        /*模板文件*/
        $viewfile = !empty($result['templist'])
            ? str_replace('.' . $this->view_suffix, '', $result['templist'])
            : 'lists_' . $this->nid;
        /*--end*/

        /*多语言内置模板文件名*/
        if (!empty($this->home_lang)) {
            $viewfilepath = TEMPLATE_PATH . $this->theme_style_path . DS . $viewfile . "_{$this->home_lang}." . $this->view_suffix;
            if (file_exists($viewfilepath)) {
                $viewfile .= "_{$this->home_lang}";
            }
        }
        /*--end*/

        // /*模板文件*/
        // $viewfile = $filename = !empty($result['templist'])
        // ? str_replace('.'.$this->view_suffix, '',$result['templist'])
        // : 'lists_'.$this->nid;
        // /*--end*/

        // /*每个栏目内置模板文件名*/
        // $viewfilepath = TEMPLATE_PATH.$this->theme_style_path.DS.$filename."_{$result['id']}.".$this->view_suffix;
        // if (file_exists($viewfilepath)) {
        //     $viewfile = $filename."_{$result['id']}";
        // }
        // /*--end*/

        // /*多语言内置模板文件名*/
        // if (!empty($this->home_lang)) {
        //     $viewfilepath = TEMPLATE_PATH.$this->theme_style_path.DS.$filename."_{$this->home_lang}.".$this->view_suffix;
        //     if (file_exists($viewfilepath)) {
        //         $viewfile = $filename."_{$this->home_lang}";
        //     }
        //     /*每个栏目内置模板文件名*/
        //     $viewfilepath = TEMPLATE_PATH.$this->theme_style_path.DS.$filename."_{$result['id']}_{$this->home_lang}.".$this->view_suffix;
        //     if (file_exists($viewfilepath)) {
        //         $viewfile = $filename."_{$result['id']}_{$this->home_lang}";
        //     }
        //     /*--end*/
        // }
        // /*--end*/

        $view = ":{$viewfile}";
        if (51 == $this->channel) { // 问答模型
            $Ask = new \app\home\controller\Ask;
            return $Ask->index();
        }else{
            return $this->fetch($view);
        }
    }

    /**
     * 模型对应逻辑
     * @param intval $tid 栏目ID
     * @return array
     */
    private function logic($tid = '')
    {
        $result = array();

        if (empty($tid)) {
            return $result;
        }

        switch ($this->channel) {
            case '6': // 单页模型
            {
                $arctype_info = model('Arctype')->getInfo($tid);
                if ($arctype_info) {
                    // 读取当前栏目的内容，否则读取每一级第一个子栏目的内容，直到有内容或者最后一级栏目为止。
                    $archivesModel = new \app\home\model\Archives();
                    $result_new = $archivesModel->readContentFirst($tid);
                    // 阅读权限
                    if ($result_new['arcrank'] == -1) {
                        $this->success('待审核稿件，你没有权限阅读！');
                        exit;
                    }
                    // 外部链接跳转
                    if ($result_new['is_part'] == 1) {
                        $result_new['typelink'] = htmlspecialchars_decode($result_new['typelink']);
                        if (!is_http_url($result_new['typelink'])) {
                            $typeurl = '//'.$this->request->host();
                            if (!preg_match('#^'.ROOT_DIR.'(.*)$#i', $result_new['typelink'])) {
                                $typeurl .= ROOT_DIR;
                            }
                            $typeurl .= '/'.trim($result_new['typelink'], '/');
                            $result_new['typelink'] = $typeurl;
                        }
                        $this->redirect($result_new['typelink']);
                        exit;
                    }
                    /*自定义字段的数据格式处理*/
                    $result_new = $this->fieldLogic->getChannelFieldList($result_new, $this->channel);
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
                $result = model('Arctype')->getInfo($tid);
                /*外部链接跳转*/
                if ($result['is_part'] == 1) {
                    $result['typelink'] = htmlspecialchars_decode($result['typelink']);
                    if (!is_http_url($result['typelink'])) {
                        $result['typelink'] = '//'.$this->request->host().ROOT_DIR.'/'.trim($result['typelink'], '/');
                    }
                    $this->redirect($result['typelink']);
                    exit;
                }
                /*end*/
                break;
            }
        }

        if (!empty($result)) {
            /*自定义字段的数据格式处理*/
            $result = $this->fieldLogic->getTableFieldList($result, config('global.arctype_channel_id'));
            /*--end*/
        }

        /*是否有子栏目，用于标记【全部】选中状态*/
        $result['has_children'] = model('Arctype')->hasChildren($tid);
        /*--end*/

        // seo
        $result['seo_title'] = set_typeseotitle($result['typename'], $result['seo_title']);

        /*获取当前页面URL*/
        $result['pageurl'] = $this->request->url(true);
        /*--end*/

        /*给没有type前缀的字段新增一个带前缀的字段，并赋予相同的值*/
        foreach ($result as $key => $val) {
            if (!preg_match('/^type/i', $key)) {
                $key_new = 'type' . $key;
                !array_key_exists($key_new, $result) && $result[$key_new] = $val;
            }
        }
        /*--end*/

        return $result;
    }

    /**
     * 留言提交
     */
    public function gbook_submit()
    {
        $typeid = input('post.typeid/d');

        if (IS_POST && !empty($typeid)) {
            $channel_guestbook_gourl = tpSetting('channel_guestbook.channel_guestbook_gourl');
            if (!empty($channel_guestbook_gourl)) {
                $gourl = $channel_guestbook_gourl;
            } else {
                $gourl = input('post.gourl/s');
            }
            $post = input('post.');
            unset($post['gourl']);

            $token = '__token__';
            foreach ($post as $key => $val) {
                if (preg_match('/^__token__/i', $key)) {
                    $token = $key;
                    continue;
                }
            }
            $ip = clientIP();

            /*留言间隔限制*/
            $channel_guestbook_interval = tpSetting('channel_guestbook.channel_guestbook_interval');
            $channel_guestbook_interval = is_numeric($channel_guestbook_interval) ? intval($channel_guestbook_interval) : 60;
            if (0 < $channel_guestbook_interval) {
                $map   = array(
                    'ip'       => $ip,
                    'typeid'   => $typeid,
                    'lang'     => $this->home_lang,
                    'add_time' => array('gt', getTime() - $channel_guestbook_interval),
                );
                $count = Db::name('guestbook')->where($map)->count('aid');
                if ($count > 0) {
                    if ($this->home_lang == 'cn') {
                        $msg = '同一个IP在'.$channel_guestbook_interval.'秒之内不能重复提交！';
                    } else if ($this->home_lang == 'zh') {
                        $msg = '同一個IP在'.$channel_guestbook_interval.'秒之內不能重複提交！';
                    } else {
                        $msg = 'The same IP cannot be submitted repeatedly within '.$channel_guestbook_interval.' seconds!';
                    }
                    $this->error($msg);
                }
            }
            /*end*/

            //判断必填项            
            $ContentArr = []; // 添加站内信所需参数
            foreach ($post as $key => $value) {
                if (stripos($key, "attr_") !== false) {
                    //处理得到自定义属性id
                    $attr_id = substr($key, 5);
                    $attr_id = intval($attr_id);
                    $ga_data = Db::name('guestbook_attribute')->where([
                        'attr_id'   => $attr_id,
                        'lang'      => $this->home_lang,
                    ])->find();
                    if ($ga_data['required'] == 1) {
                        if (empty($value)) {
                            if ($this->home_lang == 'cn') {
                                $msg = $ga_data['attr_name'] . '不能为空！';
                            } else if ($this->home_lang == 'zh') {
                                $msg = $ga_data['attr_name'] . '不能為空！';
                            } else {
                                $msg = $ga_data['attr_name'] . 'Cannot be empty!';
                            }
                            $this->error($msg);
                        } else {
                            if ($ga_data['validate_type'] == 6) {
                                $pattern  = "/^1\d{10}$/";
                                if (!preg_match($pattern, $value)) {
                                    if ($this->home_lang == 'cn') {
                                        $msg = $ga_data['attr_name'] . '格式不正确！';
                                    } else if ($this->home_lang == 'zh') {
                                        $msg = $ga_data['attr_name'] . '格式不正確！';
                                    } else {
                                        $msg = $ga_data['attr_name'] . 'Incorrect format!';
                                    }
                                    $this->error($msg);
                                }
                            } elseif ($ga_data['validate_type'] == 7) {
                                $pattern  = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
                                if (preg_match($pattern, $value) == false) {
                                    if ($this->home_lang == 'cn') {
                                        $msg = $ga_data['attr_name'] . '格式不正确！';
                                    } else if ($this->home_lang == 'zh') {
                                        $msg = $ga_data['attr_name'] . '格式不正確！';
                                    } else {
                                        $msg = $ga_data['attr_name'] . 'Incorrect format!';
                                    }
                                    $this->error($msg);
                                }
                            }
                        }
                    }

                    // 添加站内信所需参数
                    array_push($ContentArr, $value);
                }
            }

            /* 处理判断验证码 */
            $is_vertify        = 1; // 默认开启验证码
            $guestbook_captcha = config('captcha.guestbook');
            if (!function_exists('imagettftext') || empty($guestbook_captcha['is_on'])) {
                $is_vertify = 0; // 函数不存在，不符合开启的条件
            }
            if (1 == $is_vertify) {
                if (empty($post['vertify'])) {
                    if ($this->home_lang == 'cn') {
                        $msg = '图片验证码不能为空！';
                    } else if ($this->home_lang == 'zh') {
                        $msg = '圖片驗證碼不能為空！';
                    } else {
                        $msg = 'Picture verification code cannot be empty!';
                    }
                    $this->error($msg);
                }

                $verify = new Verify();
                if (!$verify->check($post['vertify'], $token)) {
                    if ($this->home_lang == 'cn') {
                        $msg = '图片验证码不正确！';
                    } else if ($this->home_lang == 'zh') {
                        $msg = '圖片驗證碼不正確！';
                    } else {
                        $msg = 'The picture verification code is incorrect!';
                    }
                    $this->error($msg);
                }
            }
            /* END */

            $channeltype_list = config('global.channeltype_list');
            $this->channel = !empty($channeltype_list['guestbook']) ? $channeltype_list['guestbook'] : 8;

            $newData = array(
                'typeid'      => $typeid,
                'channel'     => $this->channel,
                'ip'          => $ip,
                'lang'        => $this->home_lang,
                'add_time'    => getTime(),
                'update_time' => getTime(),
            );
            $data    = array_merge($post, $newData);

            // 数据验证
            $rule     = [
                'typeid' => 'require|token:' . $token,
            ];
            $message  = [
                'typeid.require' => '表单缺少标签属性{$field.hidden}',
            ];
            $validate = new \think\Validate($rule, $message);
            if (!$validate->batch()->check($data)) {
                $error     = $validate->getError();
                $error_msg = array_values($error);
                $this->error($error_msg[0]);
            } else {
                $guestbookRow = [];
                /*处理是否重复表单数据的提交*/
                $formdata = $data;
                foreach ($formdata as $key => $val) {
                    if (in_array($key, ['typeid', 'lang']) || preg_match('/^attr_(\d+)$/i', $key)) {
                        continue;
                    }
                    unset($formdata[$key]);
                }
                $md5data         = md5(serialize($formdata));
                $data['md5data'] = $md5data;
                $guestbookRow    = Db::name('guestbook')->field('aid')->where(['md5data' => $md5data])->find();
                /*--end*/
                $dataStr = '';
                if (empty($guestbookRow)) { // 非重复表单的才能写入数据库
                    $aid = Db::name('guestbook')->insertGetId($data);
                    if ($aid > 0) {
                        $res = $this->saveGuestbookAttr($aid, $typeid);
                        if ($res){
                            $this->error($res);
                        }
                    }
                    /*插件 - 邮箱发送*/
                    $data    = [
                        'gbook_submit',
                        $typeid,
                        $aid,
                    ];
                    $dataStr = implode('|', $data);
                    /*--end*/

                    /*发送站内信给后台*/
                    SendNotifyMessage($ContentArr, 1, 1, 0);
                    /* END */
                } else {
                    // 存在重复数据的表单，将在后台显示在最前面
                    Db::name('guestbook')->where('aid', $guestbookRow['aid'])->update([
                        'add_time' => getTime(),
                        'update_time' => getTime(),
                    ]);
                }
                
                if ($this->home_lang == 'cn') {
                    $msg = '操作成功';
                } else if ($this->home_lang == 'zh') {
                    $msg = '操作成功';
                } else {
                    $msg = 'success';
                }
                $channel_guestbook_time = tpSetting('channel_guestbook.channel_guestbook_time');
                $channel_guestbook_time = !empty($channel_guestbook_time) ? intval($channel_guestbook_time) : 5;
                $this->success($msg, $gourl, $dataStr, $channel_guestbook_time);
            }
        }

        $this->error('表单缺少标签属性{$field.hidden}');
    }

    /**
     *  给指定留言添加表单值到 guestbook_attr
     * @param int $aid 留言id
     * @param int $typeid 留言栏目id
     */
    private function saveGuestbookAttr($aid, $typeid)
    {
        // post 提交的属性  以 attr_id _ 和值的 组合为键名    
        $post = input("post.");
        $arr = explode(',', config('global.image_ext'));
        /*上传图片或附件*/
        foreach ($_FILES as $fileElementId => $file) {
            try {
                if (!empty($file['name']) && !is_array($file['name'])) {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    if (in_array($ext,$arr)){
                        $uplaod_data = func_common($fileElementId, 'allimg');
                    }else{
                        $uplaod_data = func_common_doc($fileElementId, 'files');
                    }
                    if (0 == $uplaod_data['errcode']) {
                        $post[$fileElementId] = $uplaod_data['img_url'];
                    } else {
                        return $uplaod_data['errmsg'];
//                        $post[$fileElementId] = '';
                    }
                }
            } catch (\Exception $e) {}
        }
        /*end*/

        $attrArr = [];

        /*多语言*/
        if (is_language()) {
            foreach ($post as $key => $val) {
                if (preg_match_all('/^attr_(\d+)$/i', $key, $matchs)) {
                    $attr_value           = intval($matchs[1][0]);
                    $attrArr[$attr_value] = [
                        'attr_id' => $attr_value,
                    ];
                }
            }
            $attrArr = model('LanguageAttr')->getBindValue($attrArr, 'guestbook_attribute'); // 多语言
        }
        /*--end*/

        foreach ($post as $k => $v) {
            if (!strstr($k, 'attr_')) continue;
            $attr_id = str_replace('attr_', '', $k);
            is_array($v) && $v = implode(PHP_EOL, $v);

            /*多语言*/
            if (!empty($attrArr)) {
                $attr_id = $attrArr[$attr_id]['attr_id'];
            }
            /*--end*/

            //$v = str_replace('_', '', $v); // 替换特殊字符
            //$v = str_replace('@', '', $v); // 替换特殊字符
            $v       = trim($v);
            $adddata = array(
                'aid'         => $aid,
                'attr_id'     => $attr_id,
                'attr_value'  => $v,
                'lang'        => $this->home_lang,
                'add_time'    => getTime(),
                'update_time' => getTime(),
            );
            Db::name('GuestbookAttr')->add($adddata);
        }
    }
}