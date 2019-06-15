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

class Lists extends Base
{
    // 模型标识
    public $nid = '';
    // 模型ID
    public $channel = '';

    public function _initialize() {
        parent::_initialize();
    }

    /**
     * 栏目列表
     */
    public function index($tid = '')
    {
        /*获取当前栏目ID以及模型ID*/
        $page_tmp = input('param.page/s', 0);
        if (empty($tid) || !is_numeric($page_tmp)) {
            abort(404,'页面不存在');
        }

        $map = [];
        /*URL上参数的校验*/
        $seo_pseudo = config('ey_config.seo_pseudo');
        if (3 == $seo_pseudo)
        {
            if (stristr($this->request->url(), '&c=Lists&a=index&')) {
                abort(404,'页面不存在');
            }
            $map = array('a.dirname'=>$tid);
        }
        else if (1 == $seo_pseudo)
        {
            $seo_dynamic_format = config('ey_config.seo_dynamic_format');
            if (2 == $seo_dynamic_format && stristr($this->request->url(), '&c=Lists&a=index&')) {
                abort(404,'页面不存在');
            } else if (!is_numeric($tid) || strval(intval($tid)) !== strval($tid)) {
                abort(404,'页面不存在');
            }
            $map = array('a.id'=>$tid);
        }
        /*--end*/
        $map['a.is_del'] = 0; // 回收站功能
        $map['a.lang'] = $this->home_lang; // 多语言
        $row = M('arctype')->field('a.id, a.current_channel, b.nid')
            ->alias('a')
            ->join('__CHANNELTYPE__ b', 'a.current_channel = b.id', 'LEFT')
            ->where($map)
            ->find();
        if (empty($row)) {
            abort(404,'页面不存在');
        }
        $tid = $row['id'];
        $this->nid = $row['nid'];
        $this->channel = intval($row['current_channel']);
        /*--end*/

        $result = $this->logic($tid); // 模型对应逻辑

        $eyou = array(
            'field' => $result,
        );
        $this->eyou = array_merge($this->eyou, $eyou);
        $this->assign('eyou', $this->eyou);

        /*模板文件*/
        $viewfile = !empty($result['templist'])
        ? str_replace('.'.$this->view_suffix, '',$result['templist'])
        : 'lists_'.$this->nid;
        /*--end*/

        /*多语言内置模板文件名*/
        if (!empty($this->home_lang)) {
            $viewfilepath = TEMPLATE_PATH.$this->theme_style.DS.$viewfile."_{$this->home_lang}.".$this->view_suffix;
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
        // $viewfilepath = TEMPLATE_PATH.$this->theme_style.DS.$filename."_{$result['id']}.".$this->view_suffix;
        // if (file_exists($viewfilepath)) {
        //     $viewfile = $filename."_{$result['id']}";
        // }
        // /*--end*/

        // /*多语言内置模板文件名*/
        // if (!empty($this->home_lang)) {
        //     $viewfilepath = TEMPLATE_PATH.$this->theme_style.DS.$filename."_{$this->home_lang}.".$this->view_suffix;
        //     if (file_exists($viewfilepath)) {
        //         $viewfile = $filename."_{$this->home_lang}";
        //     }
        //     /*每个栏目内置模板文件名*/
        //     $viewfilepath = TEMPLATE_PATH.$this->theme_style.DS.$filename."_{$result['id']}_{$this->home_lang}.".$this->view_suffix;
        //     if (file_exists($viewfilepath)) {
        //         $viewfile = $filename."_{$result['id']}_{$this->home_lang}";
        //     }
        //     /*--end*/
        // }
        // /*--end*/

        return $this->fetch(":{$viewfile}");
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
                    $result = $this->readContentFirst($tid);
                    // 阅读权限
                    if ($result['arcrank'] == -1) {
                        $this->success('待审核稿件，你没有权限阅读！');
                        exit;
                    }
                    // 外部链接跳转
                    if ($result['is_part'] == 1) {
                        header('Location: '.$result['typelink']);
                        exit;
                    }
                    /*自定义字段的数据格式处理*/
                    $result = $this->fieldLogic->getChannelFieldList($result, $this->channel);
                    /*--end*/
                    $result = array_merge($arctype_info, $result);
                }
                break;
            }

            default:
            {
                $result = model('Arctype')->getInfo($tid);
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
        $result['pageurl'] = request()->url(true);
        /*--end*/

        /*给没有type前缀的字段新增一个带前缀的字段，并赋予相同的值*/
        foreach ($result as $key => $val) {
            if (!preg_match('/^type/i',$key)) {
                $result['type'.$key] = $val;
            }
        }
        /*--end*/

        return $result;
    }

    /**
     * 读取指定栏目ID下有内容的栏目信息，只读取每一级的第一个栏目
     * @param intval $typeid 栏目ID
     * @return array
     */
    private function readContentFirst($typeid)
    {
        $result = false;
        while (true)
        {
            $result = model('Single')->getInfoByTypeid($typeid);
            if (empty($result['content']) && 'lists_single.htm' == strtolower($result['templist'])) {
                $map = array(
                    'parent_id' => $result['typeid'],
                    'current_channel' => 6,
                    'is_hidden' => 0,
                    'status'    => 1,
                );
                $row = M('arctype')->where($map)->field('*')->order('sort_order asc')->find(); // 查找下一级的单页模型栏目
                if (empty($row)) { // 不存在并返回当前栏目信息
                    break;
                } elseif (6 == $row['current_channel']) { // 存在且是单页模型，则进行继续往下查找，直到有内容为止
                    $typeid = $row['id'];
                }
            } else {
                break;
            }
        }

        return $result;
    }

    /**
     * 留言提交 
     */
    public function gbook_submit()
    {
        $typeid = input('post.typeid/d');

        if (IS_POST && !empty($typeid)) {
            $post = input('post.');

            $token = '__token__';
            foreach ($post as $key => $val) {
                if (preg_match('/^__token__/i', $key)) {
                    $token = $key;
                    continue;
                }
            }
            $ip = clientIP();
            $map = array(
                'ip'    => $ip,
                'typeid'    => $typeid,
                'lang'      => $this->home_lang,
                'add_time'  => array('gt', getTime() - 60),
            );
            $count = M('guestbook')->where($map)->count('aid');
            if ($count > 0) {
                $this->error('同一个IP在60秒之内不能重复提交！');
            }

            $this->channel = Db::name('arctype')->where(['id'=>$typeid])->getField('current_channel');

            $newData = array(
                'typeid'    => $typeid,
                'channel'   => $this->channel,
                'ip'    => $ip,
                'lang'  => $this->home_lang,
                'add_time'  => getTime(),
                'update_time' => getTime(),
            );
            $data = array_merge($post, $newData);

            // 数据验证
            $rule = [
                'typeid'    => 'require|token:'.$token,
            ];
            $message = [
                'typeid.require' => '表单缺少标签属性{$field.hidden}',
            ];
            $validate = new \think\Validate($rule, $message);
            if(!$validate->batch()->check($data))
            {
                $error = $validate->getError();
                $error_msg = array_values($error);
                $this->error($error_msg[0]);
            } else {
                $guestbookRow = [];
                /*处理是否重复表单数据的提交*/
                $formdata = $data;
                foreach ($formdata as $key => $val) {
                    if (in_array($key, ['typeid','lang']) || preg_match('/^attr_(\d+)$/i', $key)) {
                        continue;
                    }
                    unset($formdata[$key]);
                }
                $md5data = md5(serialize($formdata));
                $data['md5data'] = $md5data;
                $guestbookRow = M('guestbook')->field('aid')->where(['md5data'=>$md5data])->find();
                /*--end*/
                if (empty($guestbookRow)) { // 非重复表单的才能写入数据库
                    $aid = M('guestbook')->insertGetId($data);
                    if ($aid > 0) {
                        $this->saveGuestbookAttr($aid, $typeid);
                    }
                    /*插件 - 邮箱发送*/
                    $data = [
                        'gbook_submit',
                        $typeid,
                        $aid,
                    ];
                    $dataStr = implode('|', $data);
                    /*--end*/
                } else {
                    // 存在重复数据的表单，将在后台显示在最前面
                    M('guestbook')->where('aid',$guestbookRow['aid'])->update([
                            'update_time'   => getTime(),
                        ]);
                }
                $this->success('操作成功！', null, $dataStr, 3);
            }  
        }

        $this->error('表单缺少标签属性{$field.hidden}');
    }

    /**
     *  给指定留言添加表单值到 guestbook_attr
     * @param int $aid  留言id
     * @param int $typeid  留言栏目id
     */
    private function saveGuestbookAttr($aid, $typeid)
    {  
        // post 提交的属性  以 attr_id _ 和值的 组合为键名    
        $post = input("post.");
        $attrArr = [];

        /*多语言*/
        if (is_language()) {
            foreach($post as $key => $val) {
                if (preg_match_all('/^attr_(\d+)$/i', $key, $matchs)) {
                    $attr_value = intval($matchs[1][0]);
                    $attrArr[$attr_value] = [
                        'attr_id'   => $attr_value,
                    ];
                }
            }
            $attrArr = model('LanguageAttr')->getBindValue($attrArr, 'guestbook_attribute'); // 多语言
        }
        /*--end*/

        foreach($post as $k => $v)
        {
            if(!strstr($k, 'attr_'))
                continue;                                 

            $attr_id = str_replace('attr_','',$k);

            /*多语言*/
            if (!empty($attrArr)) {
                $attr_id = $attrArr[$attr_id]['attr_id'];
            }
            /*--end*/

            //$v = str_replace('_', '', $v); // 替换特殊字符
            //$v = str_replace('@', '', $v); // 替换特殊字符
            $v = trim($v);
            $adddata = array(
                'aid'   => $aid,
                'attr_id'   => $attr_id,
                'attr_value'   => filter_line_return($v, '。'),
                'lang'  => $this->home_lang,
                'add_time'   => getTime(),
                'update_time'   => getTime(),
            );
            M('GuestbookAttr')->add($adddata);                       
        }
    }
}