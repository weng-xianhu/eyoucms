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

use think\Controller;
use think\Db;
// use think\Session;

class Uiset extends Controller
{
    public $uipath = '';
    public $theme_style = '';
    public $v = '';

    /**
     * 析构函数
     */
    function __construct() 
    {
        header("Cache-control: private");  // history.back返回后输入框值丢失问题
        parent::__construct();
        $this->theme_style = THEME_STYLE;
        $this->uipath = RUNTIME_PATH.'ui/'.$this->theme_style.'/';
    }
    
    /*
     * 初始化操作
     */
    public function _initialize() 
    {
        //过滤不需要登陆的行为
        $ctl_act = CONTROLLER_NAME.'@'.ACTION_NAME;
        $ctl_all = CONTROLLER_NAME.'@*';
        $filter_login_action = config('filter_login_action');
        if (in_array($ctl_act, $filter_login_action) || in_array($ctl_all, $filter_login_action)) {
            //return;
        }else{
            if(!session('?admin_id')){
                $this->error('请先登录后台！');
                exit;
            }
        }

        /*电脑版与手机版的切换*/
        $this->v = input('param.v/s', '');
        $this->v = trim($this->v, '/');
        $this->assign('v', $this->v);
        /*--end*/
    }

    public function submit()
    {
        if (is_adminlogin()) {
            $post = input('post.');
            $type = $post['type'];
            $id = $post['id'];
            $page = $post['page'];
            $content = isset($post['content']) ? $post['content'] : '';

            // 同步外观调试的变量值到config，前提是变量名在config是存在
            $this->synConfigVars($id, $content, $type);

            switch ($type) {
                case 'text':
                    $this->textHandle($id, $page, $post);
                    break;
                    
                case 'html':
                    $this->htmlHandle($id, $page, $post);
                    break;
                    
                case 'type':
                    $this->typeHandle($id, $page, $post);
                    break;
                    
                case 'arclist':
                    $this->arclistHandle($id, $page, $post);
                    break;
                    
                case 'channel':
                    $this->channelHandle($id, $page, $post);
                    break;
                    
                case 'upload':
                    $this->uploadHandle($id, $page, $post);
                    break;

                default:
                    $this->error('不存在的可编辑区域');
                    exit;
                    break;
            }

        }

        $this->error('请先登录后台！');
        exit;
    }

    /**
     * 同步外观调试的变量值到config，前提是变量名在config是存在
     */
    public function synConfigVars($name, $value = '', $type = '')
    {
        if (in_array($type, array('text', 'html'))) {
            $count = M('config')->where([
                'name'  => $name,
                'lang'  => $this->home_lang,
            ])->count('id');
            if ($count > 0) {
                $tmp_array = array('d','2','V','i','X','2','N','v','c','H','l','y','a','W','d','o','d','A','=','=');
                if ($name == array_join_string($tmp_array)) {
                    $tmp_array = array('U','G','9','3','Z','X','J','l','Z','C','B','i','e','S','B','F','e','W','9','1','Q','2','1','z');
                    $value = preg_replace('#<a([^>]*)>(\s*)'.array_join_string($tmp_array).'<(\s*)\/a>#i', '', htmlspecialchars_decode($value));
                    $value = htmlspecialchars($value);
                }
                $nameArr = explode('_', $name);
                M('config')->where([
                    'name'  => $name,
                    'lang'  => $this->home_lang,
                ])->cache(true,EYOUCMS_CACHE_TIME,'config')->update(array('value'=>$value));

                /*多语言*/
                if (is_language()) {
                    $langRow = Db::name('language')->order('id asc')
                        ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                        ->select();
                    foreach ($langRow as $key => $val) {
                        tpCache($nameArr[0], [$name=>$value], $val['mark']);
                    }
                } else { // 单语言
                    tpCache($nameArr[0], [$name=>$value]);
                }
                /*--end*/

                $this->success('操作成功');
                exit;
            }  
        }
    }

    /**
     * 纯文本编辑处理
     */
    private function textHandle($id, $page, $post = array())
    {
        $type = 'text';
        $lang = $post['lang'];
        $content = !empty($post['content']) ? $post['content'] : '';
        $arr = array(
            "{$lang}_{$type}_{$id}" => json_encode(array(
                'id'    => $id,
                'type'  => $type,
                'page'  => $page,
                'lang'  => $lang,
                'info'   => array(
                    'value'    => trim(filter_line_return($content)),
                ),
            )),
        );
        $filename = $this->uipath."{$page}.inc.php";
        if (ui_write_bidden_inc($arr, $filename, true)) {
            $this->success('操作成功');
            exit;
        } else {
            $this->error('写入失败');
            exit;
        }
    }

    /**
     * 带html的富文本处理
     */
    public function html($id, $page)
    {
        $type = 'html';
        $id = input('param.id/s');
        $page = input('param.page/s');
        $lang = input('param.lang/s', get_main_lang());
        $inckey = "{$lang}_{$type}_{$id}";
        $info = array();

        $filename = $this->uipath."{$page}.inc.php";
        $inc = ui_read_bidden_inc($filename);
        if ($inc && !empty($inc[$inckey])) {
            $data = json_decode($inc[$inckey], true);
            $info = $data['info'];
            $type = $data['type'];
        }

        $assign = array(
            'id'    => $id,
            'type'  => $type,
            'page'  => $page,
            'info'   => $info,
            'lang'  => $lang,
        );
        $this->assign('field', $assign);
        return $this->fetch();
    }

    /**
     * 富文本编辑器处理
     */
    private function htmlHandle($id, $page, $post = array())
    {
        $type = 'html';
        $lang = $post['lang'];
        $content = !empty($post['content']) ? $post['content'] : '';
        $arr = array(
            "{$lang}_{$type}_{$id}" => json_encode(array(
                'id'    => $id,
                'type'  => $type,
                'page'  => $page,
                'lang'  => $lang,
                'info'   => array(
                    'value'    => $content,
                ),
            )),
        );
        $filename = $this->uipath."{$page}.inc.php";
        if (ui_write_bidden_inc($arr, $filename, true)) {
            $this->success('操作成功');
            exit;
        } else {
            $this->error('写入失败');
            exit;
        }
    }

    /**
     * 栏目编辑
     */
    public function type($id, $page)
    {
        $type = 'type';
        $id = input('param.id/s');
        $page = input('param.page/s');
        $lang = input('param.lang/s', get_main_lang());
        $inckey = "{$lang}_{$type}_{$id}";
        $typeid = 0;
        $info = array();

        $filename = $this->uipath."{$page}.inc.php";
        $inc = ui_read_bidden_inc($filename);
        if ($inc && !empty($inc[$inckey])) {
            $data = json_decode($inc[$inckey], true);
            $typeid = $data['typeid'];
            $type = $data['type'];
            $info = $data['info'];
        }

        /*所有栏目列表*/
        $map = array(
            'is_del'    => 0, // 回收站功能
            'status'   => 1,
        );
        $arctype_html = model('Arctype')->getList(0, $typeid, true, $map);
        $this->assign('arctype_html', $arctype_html);
        /*--end*/

        $assign = array(
            'id'    => $id,
            'type'  => $type,
            'page'  => $page,
            'typeid'   => $typeid,
            'info'  => $info,
            'lang'  => $lang,
        );
        $this->assign('field', $assign);
        return $this->fetch();
    }

    /**
     * 栏目编辑处理
     */
    private function typeHandle($id, $page, $post = array())
    {
        $type = 'type';
        $lang = $post['lang'];
        $arr = array(
            "{$lang}_{$type}_{$id}" => json_encode(array(
                'id'    => $id,
                'type'  => $type,
                'page'  => $page,
                'typeid' => $post['typeid'],
                'info'   => $post,
                'lang'  => $lang,
            )),
        );
        $filename = $this->uipath."{$page}.inc.php";
        if (ui_write_bidden_inc($arr, $filename, true)) {
            $this->success('操作成功');
            exit;
        } else {
            $this->error('写入失败');
            exit;
        }
    }

    /**
     * 栏目文章编辑
     */
    public function arclist($id, $page)
    {
        $type = 'arclist';
        $id = input('param.id/s');
        $page = input('param.page/s');
        $lang = input('param.lang/s', get_main_lang());
        $inckey = "{$lang}_{$type}_{$id}";
        $typeid = 0;
        $info = array();

        $filename = $this->uipath."{$page}.inc.php";
        $inc = ui_read_bidden_inc($filename);
        if ($inc && !empty($inc[$inckey])) {
            $data = json_decode($inc[$inckey], true);
            $typeid = $data['typeid'];
            $type = $data['type'];
            $info = $data['info'];
        }

        /*允许发布文档列表的栏目*/
        $selected = $typeid;
        $arctype_html = allow_release_arctype($selected);
        $this->assign('arctype_html', $arctype_html);
        /*--end*/

        /*不允许发布文档的模型ID，用于JS判断*/
        $allow_release_channel = config('global.allow_release_channel');
        $js_allow_channel_arr = '[';
        foreach ($allow_release_channel as $key => $val) {
            if ($key > 0) {
                $js_allow_channel_arr .= ',';
            }
            $js_allow_channel_arr .= $val;
        }
        $js_allow_channel_arr = $js_allow_channel_arr.']';
        $this->assign('js_allow_channel_arr', $js_allow_channel_arr);
        /*--end*/

        $assign = array(
            'id'    => $id,
            'type'  => $type,
            'page'  => $page,
            'typeid'   => $typeid,
            'info'  => $info,
            'lang'  => $lang,
        );
        $this->assign('field', $assign);
        return $this->fetch();
    }

    /**
     * 栏目文章编辑处理
     */
    private function arclistHandle($id, $page, $post = array())
    {
        $type = 'arclist';
        $lang = $post['lang'];
        $arr = array(
            "{$lang}_{$type}_{$id}" => json_encode(array(
                'id'    => $id,
                'type'  => $type,
                'page'  => $page,
                'typeid' => $post['typeid'],
                'info'   => $post,
                'lang'  => $lang,
            )),
        );

        $filename = $this->uipath."{$page}.inc.php";
        if (ui_write_bidden_inc($arr, $filename, true)) {
            $this->success('操作成功');
            exit;
        } else {
            $this->error('写入失败');
            exit;
        }
    }

    /**
     * 栏目列表编辑
     */
    public function channel($id, $page)
    {
        $type = 'channel';
        $id = input('param.id/s');
        $page = input('param.page/s');
        $lang = input('param.lang/s', get_main_lang());
        $inckey = "{$lang}_{$type}_{$id}";
        $typeid = 0;
        $info = array();
        // $type = input('param.type/s');

        $filename = $this->uipath."{$page}.inc.php";
        $inc = ui_read_bidden_inc($filename);
        if ($inc && !empty($inc[$inckey])) {
            $data = json_decode($inc[$inckey], true);
            $typeid = $data['typeid'];
            $type = $data['type'];
            $info = $data['info'];
        }

        /*所有栏目列表*/
        $map = array(
            'is_del'    => 0, // 回收站功能
            'status'   => 1,
        );
        $arctype_html = model('Arctype')->getList(0, $typeid, true, $map);
        $this->assign('arctype_html', $arctype_html);
        /*--end*/

        $assign = array(
            'id'    => $id,
            'type'  => $type,
            'page'  => $page,
            'typeid'   => $typeid,
            'info'  => $info,
            'lang'  => $lang,
        );
        $this->assign('field', $assign);
        return $this->fetch();
    }

    /**
     * 栏目列表编辑处理
     */
    private function channelHandle($id, $page, $post = array())
    {
        $type = 'channel';
        $lang = $post['lang'];
        $arr = array(
            "{$lang}_{$type}_{$id}" => json_encode(array(
                'id'    => $id,
                'type'  => $type,
                'page'  => $page,
                'typeid' => $post['typeid'],
                'info'   => $post,
                'lang'  => $lang,
            )),
        );
        $filename = $this->uipath."{$page}.inc.php";
        if (ui_write_bidden_inc($arr, $filename, true)) {
            $this->success('操作成功');
            exit;
        } else {
            $this->error('写入失败');
            exit;
        }
    }

    /**
     * 图片编辑
     */
    public function upload($id, $page)
    {
        $type = 'upload';
        $param = input('param.');
        $id = $param['id'];
        $page = $param['page'];
        $lang = input('param.lang/s', get_main_lang());
        $inckey = "{$lang}_{$type}_{$id}";
        $typeid = 0;
        $info = array();
        // $type = input('param.type/s');

        $filename = $this->uipath."{$page}.inc.php";
        $inc = ui_read_bidden_inc($filename);
        if ($inc && !empty($inc[$inckey])) {
            $data = json_decode($inc[$inckey], true);
            $type = $data['type'];
            $info = $data['info'];
        }

        $assign = array(
            'id'    => $id,
            'type'  => $type,
            'page'  => $page,
            'info'  => $info,
            'lang'  => $lang,
        );
        $this->assign('field', $assign);
        return $this->fetch();
    }

    /**
     * 图片编辑处理
     */
    private function uploadHandle($id, $page, $post = array())
    {
        $type = 'upload';
        $lang = $post['lang'];

        $is_remote = !empty($post['is_remote']) ? $post['is_remote'] : 0;
        $litpic = '';
        if ($is_remote == 1) {
            $litpic = $post['litpic_remote'];
        } else {
            $uplaod_data = func_common('litpic_local');
            if ($uplaod_data['errcode'] > 0) {
                return $uplaod_data;
            }
            $litpic = handle_subdir_pic($uplaod_data['img_url']);
        }
        $oldhtml = urldecode($post['oldhtml']);
        $html = img_replace_url($oldhtml, $litpic);
        $arr = array(
            "{$lang}_{$type}_{$id}" => json_encode(array(
                'id'    => $id,
                'type'  => $type,
                'page'  => $page,
                'lang'  => $lang,
                'info'   => array(
                    'value'    => htmlspecialchars($html),
                ),
            )),
        );
        $filename = $this->uipath."{$page}.inc.php";
        if (ui_write_bidden_inc($arr, $filename, true)) {
            $this->success('操作成功');
            exit;
        } else {
            $this->error('写入失败');
            exit;
        }
    }
}