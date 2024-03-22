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
use app\common\logic\ArctypeLogic;

/**
 * 插件的控制器
 */
class Ask extends Base
{
    private $arctypeLogic;
   
    /**
     * 构造方法
     */
    public function _initialize()
    {
        parent::_initialize();

        $functionLogic = new \app\common\logic\FunctionLogic;
        $functionLogic->check_authorfile(2);

        $this->arctypeLogic = new ArctypeLogic();
        // 问题表
        $this->ask_db = Db::name('ask');
        // 答案表
        $this->ask_answer_db = Db::name('ask_answer');
        // 点赞表
        $this->ask_answer_like_db = Db::name('ask_answer_like');
        // 问题分类表
        $this->ask_type_db = Db::name('ask_type');

        $score_name = getUsersConfigData('score.score_name');
        $this->assign('score_name', $score_name);
    }

    /**
     * 插件后台管理 - 栏目管理
     */
    public function index()
    {
        $list = $this->ask_type_db->order('sort_order asc, type_id asc')->select();
        foreach ($list as $key => $value) {
            // 是否顶级栏目
            if ($value['parent_id'] == 0) {
                $PidData[] = $value;
            } else {
                $TidData[] = $value;
            }
        }

        $list_new = [];
        foreach ($PidData as $P_key => $PidValue) {
            $type_name               = $PidValue['type_name'];
            $PidValue['type_name_input']   = '<input type="text" name="type_name[]" value="' . $PidValue['type_name'] . '" class="w220">';
            $PidValue['parent_name'] = '顶级栏目';
            /*一级栏目*/
            $list_new[] = $PidValue;
            /* END */
            foreach ($TidData as $T_key => $TidValue) {
                /*二级栏目*/
                if ($TidValue['parent_id'] == $PidValue['type_id']) {
                    $TidValue['type_name_input']   = '|— <input type="text" name="type_name[]" value="' . $TidValue['type_name'] . '" class="w200">';
                    $TidValue['parent_name'] = $type_name;
                    $list_new[]              = $TidValue;
                }
                /* END */
            }
        }
        $this->assign('list', $list_new);

        /*栏目处理*/
        $PidDataNew[0] = [
            'type_id'   => 0,
            'type_name' => '顶级栏目',
            'parent_id' => 0,
        ];
        $PidData       = !empty($PidData) ? array_merge($PidDataNew, $PidData) : $PidDataNew;
        $this->assign('PidData', $PidData);
        /* END */

        /*是否有数据*/
        $IsEmpty = empty($list_new) ? 0 : 1;
        $this->assign('IsEmpty', $IsEmpty);
        /* END */
        return $this->fetch();
    }
    
    /**
     * 插件后台管理 - 问题列表
     */
    public function ask_list()
    {
        $list     = array();
        $keywords = input('keywords/s');
        $map      = array();
        if (!empty($keywords)) {
            $map['a.ask_title'] = array('LIKE', "%{$keywords}%");
        }
        $map['a.is_del'] = 0;
        $count   = $this->ask_db->alias('a')->where($map)->count('ask_id');// 查询满足要求的总记录数
        $pageObj = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list    = $this->ask_db->field('a.*, b.type_name, b.parent_id')
            ->alias('a')
            ->join('__ASK_TYPE__ b', 'a.type_id = b.type_id', 'LEFT')
            ->where($map)
            ->order('a.is_review asc, a.ask_id desc')
            ->limit($pageObj->firstRow . ',' . $pageObj->listRows)
            ->select();
        // 分类处理
        if (!empty($list)) {
            // 用户ID
            $users_ids = [];
            // 总分类数据
            $TypeData = $this->ask_type_db->getField('type_id, type_name, parent_id');
            foreach ($list as $key => $value) {
                array_push($users_ids, $value['users_id']);

                /*分类处理*/
                if (!empty($value['parent_id'])) {
                    $list[$key]['sub_type_name'] = $value['type_name'];
                    $list[$key]['type_name']     = $TypeData[$value['parent_id']]['type_name'];
                } else {
                    $list[$key]['type_name']     = $value['type_name'];
                    $list[$key]['sub_type_name'] = '';
                }
                /* END */

                /*问题状态处理*/
                if (0 == $value['status']) {
                    $list[$key]['status'] = '<font color="red">未解决</font>';
                } else if (1 == $value['status']) {
                    $list[$key]['status'] = '已解决';
                } else if (2 == $value['status']) {
                    $list[$key]['status'] = '<font color="#cccccc">已关闭</font>';
                }
                /* END */

                // 访问前台url
                $list[$key]['HomeUrl'] = get_askurl("home/Ask/details", ['ask_id'=>$value['ask_id']]);
            }

            // 用户信息
            $users_list = Db::name('users')->field('users_id, username, nickname, head_pic')->where(['users_id'=>['IN', $users_ids]])->getAllWithIndex('users_id');
            $this->assign('users_list', $users_list);
        }

        $pageStr = $pageObj->show(); // 分页显示输出
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $pageStr); // 赋值分页输出
        $this->assign('pager', $pageObj); // 赋值分页对象
        return $this->fetch('ask_list');
    }

    /**
     * 插件后台管理 - 答案列表
     */
    public function answer()
    {
        $list     = array();
        $keywords = input('keywords/s');
        $map      = array();
        if (!empty($keywords)) {
            $map['a.content'] = array('LIKE', "%{$keywords}%");
        }

        $count   = $this->ask_answer_db->alias('a')->where($map)->count('answer_id');// 查询满足要求的总记录数
        $pageObj = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list    = $this->ask_answer_db->field('a.*, b.nickname, b.username, b.head_pic')
            ->alias('a')
            ->join('__USERS__ b', 'a.users_id = b.users_id', 'LEFT')
            ->where($map)
            ->order('a.is_review asc, a.answer_id desc')
            ->limit($pageObj->firstRow . ',' . $pageObj->listRows)
            ->select();

        // 用户ID
        $users_ids = [];
        foreach ($list as $key => $value) {
            array_push($users_ids, $value['users_id']);
            // 访问前台url
            $HomeAskUrl            = get_askurl("home/Ask/details", ['ask_id'=>$value['ask_id']]);
            $list[$key]['HomeUrl'] = $HomeAskUrl;
            $HomeAskUrl            .= !empty($value['answer_pid']) ? '#ul_div_li_' . $value['answer_pid'] : '#ul_div_li_' . $value['answer_id'];
            $list[$key]['HomeAnswerUrl'] = $HomeAskUrl;

            // 内容处理
            $preg                  = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
            $value['content']      = htmlspecialchars_decode($value['content']);
            $value['content']      = preg_replace($preg, '[图片]', $value['content']);
            $value['content']      = strip_tags($value['content']);
            $list[$key]['content'] = mb_strimwidth($value['content'], 0, 120, "...");
        }

        $pageStr = $pageObj->show(); // 分页显示输出
        $this->assign('list', $list); // 赋值数据集
        $this->assign('page', $pageStr); // 赋值分页输出
        $this->assign('pager', $pageObj); // 赋值分页对象

        return $this->fetch('answer');
    }

    // 删除栏目
    public function ask_type_del()
    {
        $type_id = input('del_id/a');
        $type_id = eyIntval($type_id);
        if (!empty($type_id)) {
            $result     = $this->ask_type_db->where("type_id", 'IN', $type_id)->select();
            $title_list = get_arr_column($result, 'type_name');

            $r = $this->ask_type_db->where("type_id", 'IN', $type_id)->delete();
            if ($r) {
                adminLog('删除问答栏目：' . implode(',', $title_list));
                // 同步删除顶级栏目下的子栏目
                if (empty($result[0]['parent_id'])) {
                    $this->ask_type_db->where("parent_id", 'IN', $type_id)->delete();
                }
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        } else {
            $this->error("参数有误！");
        }
    }

    /**
     * 插件后台管理 - 插件配置
     */
    public function conf()
    {
        if (IS_POST) {
            $post     = input('post.');
            $inc_type = 'ask';
            tpSetting($inc_type, $post['ask']);

            $functionLogic = new \app\common\logic\FunctionLogic;
            $functionLogic->scoreConf($post['score']);

            $this->success("操作成功");
        }
        $askConf = tpSetting('ask');
        $this->assign('askConf', $askConf);

        $score = getUsersConfigData('score');
        $this->assign('score', $score);

        return $this->fetch('conf');
    }

    public function level_set()
    {
        $LevelData = model('UsersLevel')->getList();
        $this->assign('list', $LevelData);
        return $this->fetch('level_set');

    }

    /**
     * 栏目SEO配置
     * @return [type] [description]
     */
    public function ask_type_seo()
    {
        $type_id = input('param.type_id/d');

        if (IS_POST) {
            if (empty($type_id)) {
                $this->error('操作失败');
            }

            $data = input('post.');
            $data['type_name'] = !empty($data['type_name']) ? trim($data['type_name']) : '';
            $data['seo_title'] = !empty($data['seo_title']) ? trim($data['seo_title']) : '';
            $data['seo_keywords'] = !empty($data['seo_keywords']) ? trim($data['seo_keywords']) : '';
            $data['seo_description'] = !empty($data['seo_description']) ? trim($data['seo_description']) : '';
            $data['update_time'] = getTime();

            $r = $this->ask_type_db->where('type_id', $type_id)->update($data);
            if (false !== $r) {
                $this->success('操作成功！');
            } else {
                $this->error('操作失败！');
            }
        }

        $info = $this->ask_type_db->where('type_id', $type_id)->find();
        if (empty($info)) {
            $this->error('数据不存在，请联系管理员！');
            exit;
        }
        $this->assign('info', $info);

        return $this->fetch('ask_type_seo');
    }

    /**
     * 插件后台管理 - 删除问题
     */
    public function ask_del()
    {
        $ask_id = input('del_id/a');
        $ask_id = eyIntval($ask_id);
        if (!empty($ask_id)) {
            $ask        = Db::name('ask')->where('ask_id', 'IN', $ask_id)->select();
            $result     = $this->ask_db->where("ask_id", 'IN', $ask_id)->select();
            $title_list = get_arr_column($result, 'ask_title');

            $r = $this->ask_db->where("ask_id", 'IN', $ask_id)->update(['is_del'=>1]);
            if ($r) {
                adminLog('删除问题：' . implode(',', $title_list));
                // 同步删除答案表数据
                $this->ask_answer_db->where("ask_id", 'IN', $ask_id)->update(['is_del'=>1]);
                // 同步删除点赞表数据
                $this->ask_answer_like_db->where("ask_id", 'IN', $ask_id)->update(['is_del'=>1]);
                /*afterDel start*/
                foreach ($ask as $key => $val) {
                    if (!empty($val['bestanswer_id'])){
                        continue;
                    }
                    $users_id = $val['users_id'];
                    $money    = $val['money'];
                    if ($money > 0) {
                        //退钱
                        Db::name('users')->where('users_id', $users_id)->setInc('users_money', $money);
                        $data = [
                            'ask_id'   => $val['ask_id'],
                            'users_id' => $users_id,
                            'type'     => 4,//悬赏退回
                            'money'    => $money,
                        ];
                        Db::name('users_score')->insert($data);
                    }

                }
                /*afterDel end*/
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        } else {
            $this->error("参数有误！");
        }
    }

    /**
     * 插件后台管理 - 批量审核问题
     */
    public function ask_review()
    {
        $ask_id = input('ask_id/a');
        $ask_id = eyIntval($ask_id);
        if (!empty($ask_id)) {
            $UpData = [
                'is_review'   => 1,
                'update_time' => getTime(),
            ];
            $r      = $this->ask_db->where("ask_id", 'IN', $ask_id)->update($UpData);
            if ($r) {
                $this->success("审核成功！");
            } else {
                $this->error("审核失败！");
            }
        } else {
            $this->error("参数有误！");
        }
    }

    /**
     * 插件后台管理 - 批量推荐问题
     */
    public function ask_recom()
    {
        $ask_id = input('ask_id/a');
        $ask_id = eyIntval($ask_id);
        if (!empty($ask_id)) {
            $UpData = [
                'is_recom'    => 1,
                'update_time' => getTime(),
            ];
            $r      = $this->ask_db->where("ask_id", 'IN', $ask_id)->update($UpData);
            if ($r) {
                $this->success("审核成功！");
            } else {
                $this->error("审核失败！");
            }
        } else {
            $this->error("参数有误！");
        }
    }

    /**
     * 插件后台管理 - 批量删除答案
     */
    public function answer_del()
    {
        $answer_id = input('del_id/a');
        $answer_id = eyIntval($answer_id);
        if (!empty($answer_id)) {
            $r = $this->ask_answer_db->where("answer_id", 'IN', $answer_id)->delete();
            if ($r) {
                // 同步删除点赞表数据
                $this->ask_answer_like_db->where("answer_id", 'IN', $answer_id)->delete();
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        } else {
            $this->error("参数有误！");
        }
    }

    /**
     * 插件后台管理 - 批量审核答案
     */
    public function answer_review()
    {
        $answer_id = input('ask_id/a');
        $answer_id = eyIntval($answer_id);
        if (!empty($answer_id)) {
            $UpData = [
                'is_review'   => 1,
                'update_time' => getTime(),
            ];
            $r      = $this->ask_answer_db->where("answer_id", 'IN', $answer_id)->update($UpData);
            if ($r) {
                $this->success("审核成功！");
            } else {
                $this->error("审核失败！");
            }
        } else {
            $this->error("参数有误！");
        }
    }

    /**
     * 积分级别列表
     * @return mixed
     */
    public function score_level()
    {
        if (IS_AJAX_POST){
            $post = input('post.');

            if (empty($post['name'])) {
                $this->error('至少新增一个级别名称！');
            } else {
                $is_empty = true;
                foreach ($post['name'] as $key => $val) {
                    $val = trim($val);
                    if (!empty($val)) {
                        $is_empty = false;
                        break;
                    }
                }
                if (true === $is_empty) {
                    $this->error('级别名称不能为空！');
                }
            }

            // 处理新增数据
            $AddAskData = [];
            foreach ($post['name'] as $key => $value) {
                $name  = trim($value);
                if (empty($name)) {
                    continue;
                }

                $id   = !empty($post['id'][$key]) ? intval($post['id'][$key]) : 0;
                $min  = !empty($post['min'][$key]) ? intval($post['min'][$key]+1) : 0;
                $max  = !empty($post['min'][$key+1]) ? intval($post['min'][$key+1]) : 0;

                $AddAskData[] = [
                    'id'   => $id,
                    'name' => $name,
                    'min'  => $min,
                    'max'  => $max,
                ];
                if (empty($id)) {
                    unset($AddAskData[$key]['id']);
                }
            }

            // 添加\更新
            $AskScoreLevelModel = new \app\common\model\AskScoreLevel;
            if (!empty($AddAskData)) $ReturnId = $AskScoreLevelModel->saveAll($AddAskData);
            if (!empty($ReturnId)) $this->success('保存成功');
            $this->error('保存失败');
        }

        $list = Db::name('ask_score_level')->select();
        $this->assign('list', $list);

        return $this->fetch();
    }

    /**
     * 删除积分级别
     */
    public function score_level_del()
    {
        $id = input('del_id/a');
        $id = eyIntval($id);
        if (!empty($id)) {
            $r = Db::name('ask_score_level')->where("id", 'IN', $id)->delete();
            if ($r) {
                adminLog('删除问答积分级别表,id：' . implode(',', $id));
                $this->success("删除成功！");
            } else {
                $this->error("删除失败！");
            }
        } else {
            $this->error("参数有误！");
        }
    }

    // 问答模型 - 保存栏目
    public function ajax_ask_type_save()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');

            if (empty($post['type_name'])) {
                $this->error('至少新增一个栏目名称！');
            } else {
                $is_empty = true;
                foreach ($post['type_name'] as $key => $val) {
                    $val = trim($val);
                    if (!empty($val)) {
                        $is_empty = false;
                        break;
                    }
                }
                if (true === $is_empty) {
                    $this->error('栏目名称不能为空！');
                }
            }

            // 处理新增数据
            $AddAskData = [];
            foreach ($post['type_name'] as $key => $value) {
                $type_name  = trim($value);
                if (empty($type_name)) {
                    continue;
                }

                $type_id    = $post['type_id'][$key];
                $parent_id  = $post['parent_id'][$key];
                $sort_order = $post['sort_order'][$key];

                if (empty($parent_id) || $parent_id < 0) $parent_id = 0;

                $AddAskData[] = [
                    'type_id'     => $type_id,
                    'type_name'   => $type_name,
                    'parent_id'   => $parent_id,
                    'sort_order'  => $sort_order,
                    'update_time' => getTime(),
                ];

                if (empty($type_id)) {
                    $AddAskData[$key]['seo_description'] = '';
                    $AddAskData[$key]['lang']     = $this->admin_lang;
                    $AddAskData[$key]['add_time'] = getTime();
                    unset($AddAskData[$key]['type_id']);
                }
            }

            // 添加\更新
            $AskTypeModel = new \app\common\model\AskType;
            if (!empty($AddAskData)) $ReturnId = $AskTypeModel->saveAll($AddAskData);

            // 返回
            if (!empty($ReturnId)) $this->success('操作成功');

            $this->error('操作失败');
        }
    }
}