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
 * Date: 2019-7-30
 */

namespace app\home\controller;

use think\Db;
use think\Config;
use app\home\logic\AskLogic;

class Ask extends Base
{
    public $users = [
        'users_id' => 0,
        'admin_id' => 0,
        'nickname' => '游客',
    ];
    public $users_id = 0;
    public $nickname = '游客';
    public $parent_id = -1;
    public $users_money = 0;
    public $AskModel;

    public function _initialize()
    {
        parent::_initialize();
        // 问答数据层
        $this->AskModel = model('Ask');
        // 问答业务层
        $this->AskLogic = new AskLogic;
        // 用户头像处理
        $this->users['head_pic'] = get_head_pic();
        // 问题表
        $this->ask_db = Db::name('ask');
        // 答案表
        $this->ask_answer_db = Db::name('ask_answer');
        // 问题、回答、评论、回复点赞表
        $this->ask_answer_like_db = Db::name('ask_answer_like');
        
        /*获取最新的会员信息*/
        $LatestData = $this->GetUsersLatestData();
        if (!empty($LatestData)) {
            // 会员全部信息
            $this->users = $LatestData;
            // 会员ID
            $this->users_id = $LatestData['users_id'];
            // 会员昵称
            $this->nickname = $LatestData['nickname'];
            // 会员余额
            $this->users_money = $LatestData['users_money'];
            // 后台管理员信息
            $this->parent_id = session('admin_info.parent_id');
        } else {
            //过滤不需要登陆的行为
            $ctl_act = CONTROLLER_NAME . '@' . ACTION_NAME;
            $ctl_all = CONTROLLER_NAME . '@*';
            $filter_login_action = [
                'Lists@index', // 问答内容列表
                'Ask@index',   // 问答内容列表
                'Ask@details', // 问答详情
            ];
            if (!in_array($ctl_act, $filter_login_action) && !in_array($ctl_all, $filter_login_action)) {
                if (IS_AJAX) {
                    if (empty($this->users)) $this->error('请先登录！');
                } else {
                    if (isWeixin()) {
                        //微信端
                        $this->redirect('user/Users/users_select_login');
                    } else {
                        // 其他端
                        $this->redirect('user/Users/login');
                    }
                }
            }
        }
        /* END */

        /*加载到模板*/
        $this->assign('users', $this->users);
        $this->assign('users_id', $this->users_id);
        $this->assign('nickname', $this->nickname);
        $this->assign('AdminParentId', $this->parent_id);
        /* END */

        /*会员功能是否开启*/
        $msg = $logut_redirect_url = '';
        $this->usersConfig  = getUsersConfigData('all');
        $web_users_switch   = tpCache('web.web_users_switch');
        if (empty($web_users_switch) || isset($this->usersConfig['users_open_register']) && $this->usersConfig['users_open_register'] == 1) {
            // 前台会员中心已关闭
            $msg = '会员中心尚未开启！';
            $logut_redirect_url = ROOT_DIR . '/';
        } else if (session('?users_id') && empty($this->users)) {
            // 登录的会员被后台删除，立马退出会员中心
            $msg = '当前用户名不存在！';
            $logut_redirect_url = url('user/Users/centre');
        }
        if (!empty($logut_redirect_url)) {
            // 清理session并回到首页
            session('users_id', null);
            session('users', null);
            cookie('users_id', null);
            $this->error($msg, $logut_redirect_url);
        }
        /* END */
    }

    // 问答首页
    public function index()
    {
        $functionLogic = new \app\common\logic\FunctionLogic;
        $functionLogic->validate_authorfile(2);
        $param = input('param.');
        // 查询条件处理
        $Where = $this->AskLogic->GetAskWhere($param, $this->parent_id);
        // 最新问题，默认读取20条，可传入条数及字段名称进行获取
        $ResultAsk = $this->AskModel->GetNewAskData($Where);
        // Url处理
        $UrlData = $this->AskLogic->GetUrlData($param);
        // 栏目处理
        $TypeData = $this->AskModel->GetAskTypeData($param);
        // 主页SEO信息
        $type_id = !empty($param['type_id']) ? intval($param['type_id']) : 0;
        $SeoData = $this->AskLogic->GetSeoData($type_id);
        // 加急(悬赏)问题，默认读取10条，可传入数字进行获取
        $UrgentAsk = $this->AskModel->GetUrgentAskData(10);
        // 统计会员提问、回答、被评论、被点赞次数
        $CountUsersAsk = $this->AskModel->GetCountAskData($this->users_id);
        // 问答设置内容
        $AskSetting = $this->AskModel->getAskSetting($this->request->action());
        // 贡献榜(积分)score
        $ScoreList = $this->AskModel->GetOrderList();
        // 财富榜(金币)money
        // $MoneyList = $this->AskModel->GetOrderList('money');
        // 数组合并加载到模板
        $result = array_merge($ResultAsk, $UrlData, $TypeData, $SeoData, $UrgentAsk, $CountUsersAsk, $AskSetting);
        $result['ScoreList'] = $ScoreList;
        // $result['MoneyList'] = $MoneyList;
        // dump($result);exit;
        $eyou = array(
            'field' => $result,
        );
        $this->assign('eyou', $eyou);

        return $this->fetch('ask/index');
    }

    // 问题详情页
    public function details()
    {
        $param = input('param.');
        if (empty($param['ask_id'])) $this->error('请选择浏览的问题');
        // 增加问题浏览点击量
        $this->AskModel->UpdateAskClick($param['ask_id']);
        // 问题详情数据
        $AskDetails = $this->AskModel->GetAskDetailsData($param, $this->parent_id, $this->users_id);
        if (0 == $AskDetails['code']) $this->error($AskDetails['msg']);
        // 问题回答数据，包含最佳答案
        $AskReplyData = $this->AskModel->GetAskReplyData($param, $this->parent_id);
        // 栏目处理
        $TypeData = $this->AskModel->GetAskTypeData($param);
        // 热门帖子，周榜
        $WeekList = $this->AskModel->GetAskWeekListData();
        // 热门帖子，总榜
        $TotalList = $this->AskModel->GetAskTotalListData();
        // Url处理
        $UrlData = $this->AskLogic->GetUrlData($param);
        // 加急(悬赏)问题，默认读取10条，可传入数字进行获取
        $UrgentAsk = $this->AskModel->GetUrgentAskData(10);
        // 统计会员提问、回答、被评论、被点赞次数
        $CountUsersAsk = $this->AskModel->GetCountAskData($this->users_id);
        //QQ群信息
        $AskSetting = $this->AskModel->getAskSetting($this->request->action());
        // 贡献榜(积分)score
        $ScoreList = $this->AskModel->GetOrderList();
        // 数组合并加载到模板
        $result = array_merge($AskDetails, $AskReplyData, $TypeData, $WeekList, $TotalList, $UrlData, $UrgentAsk, $CountUsersAsk, $AskSetting);
        $result['ScoreList'] = $ScoreList;
        $eyou = array(
            'field' => $result,
        );
        $this->assign('eyou', $eyou);

        return $this->fetch('ask/details');
    }

    // 提交问题
    public function add_ask()
    {
        if (IS_AJAX_POST || IS_POST) {
            $param = input('param.');
            // 是否登录、是否允许发布问题、数据判断及处理，返回内容数据
            $content = $this->ParamDealWith($param);
            if ($param['money'] > 0 && $param['money'] > $this->users['users_money']) {
                $this->error('余额不足以抵扣悬赏金额,请充值！');
            }

            /*添加数据*/
            $AddAsk = [
                'type_id'     => $param['ask_type_id'],
                'users_id'    => $this->users_id,
                'ask_title'   => $param['title'],
                'money'       => $param['money'],
                'content'     => $content,
                'users_ip'    => clientIP(),
                'add_time'    => getTime(),
                'update_time' => getTime(),
            ];
            // 如果这个会员组属于需要审核的，则追加
            if (1 == $this->users['ask_is_review']) $AddAsk['is_review'] = 0;
            /* END */
            $ResultId = $this->ask_db->add($AddAsk);
            if (!empty($ResultId)) {
                // 悬赏 积分奖励记录
                $this->AskModel->setScore($this->users_id, $ResultId,0,1,$param['money']);

                $url = $this->AskLogic->GetUrlData($param, 'NewDateUrl');
                if (1 == $this->users['ask_is_review']) {
                    $this->success('发布成功，但你的问题需要管理员审核！', $url, ['review' => true]);
                } else {
                    $this->success('发布成功！', $url);
                }
            } else {
                $this->error('发布的信息有误，请检查！');
            }
        }

        // 是否允许发布问题
        $this->IsRelease();
        // 栏目处理
        $result = $this->AskModel->GetAskTypeData(null, 'add_ask');
        $result['SubmitAddAsk'] = $this->AskLogic->GetUrlData(null, 'SubmitAddAsk');
        $result['users_money']  = $this->users_money;
        $result['AddAskUrl'] = $this->AskLogic->GetUrlData([], 'AddAskUrl');
        $result['NewDateUrl'] = $this->AskLogic->GetUrlData([], 'NewDateUrl');
        $result['SearchName'] = null;
        // 统计会员提问、回答、被评论、被点赞次数
        $CountUsersAsk = $this->AskModel->GetCountAskData($this->users_id);
        // 主页SEO信息
        $SeoData = $this->AskLogic->GetSeoData(0);
        // 问答设置内容
        $AskSetting = $this->AskModel->getAskSetting($this->request->action());
        // 数组合并加载到模板
        $result = array_merge($result, $CountUsersAsk, $SeoData, $AskSetting);
        $eyou = array(
            'field' => $result,
        );
        $this->assign('eyou', $eyou);

        return $this->fetch('ask/add_ask');
    }

    // 编辑问题
    public function edit_ask()
    {
        if (IS_AJAX_POST || IS_POST) {
            $param = input('param.');
            $param['ask_id'] = intval($param['ask_id']);
            // 是否登录、是否允许发布问题、数据判断及处理，返回内容数据
            $content = $this->ParamDealWith($param, false);
            $ori_money = Db::name('ask')->where('ask_id', $param['ask_id'])->getField('money');
            if ($ori_money > $param['money']) {
                $this->error('悬赏金额不能小于' . $ori_money . '元！');
            }
            if ($param['money'] > 0 && $param['money'] > $this->users['users_money']) {
                $this->error('余额不足以抵扣悬赏金额，请充值！');
            }

            /*添加数据*/
            $UpAsk = [
                'type_id'     => $param['ask_type_id'],
                'ask_title'   => $param['title'],
                'money'       => $param['money'],
                'content'     => $content,
                'users_ip'    => clientIP(),
                'update_time' => getTime(),
            ];
            // 如果这个会员组属于需要审核的，则追加
            if (1 == $this->users['ask_is_review']) $UpAsk['is_review'] = 0;
            /* END */

            /*条件处理*/
            $where['ask_id'] = $param['ask_id'];
            // 不是后台管理则只能修改自己的问题
            if (empty($this->users['admin_id'])) $where['users_id'] = $this->users_id;
            /* END */

            $ResultID = $this->ask_db->where($where)->update($UpAsk);
            if (!empty($ResultID)) {
                if ($ori_money < $param['money']) {
                    $this->AskModel->updateMoney($this->users_id, $param['money'], $ori_money, $param['ask_id']);
                }
                $url = $this->AskLogic->GetUrlData($param, 'AskDetailsUrl');
                $this->success('编辑成功！', $url);
            } else {
                $this->error('编辑的信息有误，请检查！');
            }
        }

        // 是否允许发布问题
        $this->IsRelease(false);
        $ask_id = input('ask_id/d');
        $where['ask_id'] = $ask_id;
        // 不是后台管理则只能修改自己的问题
        if (empty($this->users['admin_id'])) $where['users_id'] = $this->users_id;
        $Info = $this->ask_db->where($where)->find();
        if (empty($Info)) $this->error('请选择编辑的问题！');
        // 栏目处理
        $result = $this->AskModel->GetAskTypeData($Info, 'edit_ask');
        $result['Info'] = $Info;
        $result['SubmitEditAsk']  = $this->AskLogic->GetUrlData(['ask_id' => $ask_id], 'SubmitEditAsk');
        $result['users_money'] = $this->users_money;
        $result['AddAskUrl'] = $this->AskLogic->GetUrlData([], 'AddAskUrl');
        $result['NewDateUrl'] = $this->AskLogic->GetUrlData([], 'NewDateUrl');
        $result['SearchName'] = null;
        // 统计会员提问、回答、被评论、被点赞次数
        $CountUsersAsk = $this->AskModel->GetCountAskData($this->users_id);
        // 主页SEO信息
        $SeoData = $this->AskLogic->GetSeoData(0);
        // 问答设置内容
        $AskSetting = $this->AskModel->getAskSetting($this->request->action());
        // 数组合并加载到模板
        $result = array_merge($result, $CountUsersAsk, $SeoData, $AskSetting);
        $eyou = array(
            'field' => $result,
        );
        $this->assign('eyou', $eyou);

        return $this->fetch('ask/edit_ask');
    }

    // 采纳最佳答案
    public function ajax_best_answer()
    {
        if (IS_AJAX_POST) {
            $param = input('param.');
            // 数据判断处理
            if (empty($param['answer_id']) || empty($param['ask_id'])) $this->error('请选择采纳的回答');
            $param['ask_id'] = intval($param['ask_id']);
            $param['answer_id'] = intval($param['answer_id']);
            // 查询提问信息
            $AskInfo = $this->ask_db->field('users_id, status')->where('ask_id', $param['ask_id'])->find();
            if (!empty($this->users['admin_id']) || $AskInfo['users_id'] = input('post.users_id/d')) {
                // 查询问答状态
                $AskStatus = $this->ask_db->where('ask_id', $param['ask_id'])->getField('status');
                // 更新问题数据表
                $Updata = [
                    'ask_id'        => $param['ask_id'],
                    'status'        => 1,
                    'solve_time'    => getTime(),
                    'bestanswer_id' => $param['answer_id'],
                    'update_time'   => getTime()
                ];
                $ResultID = $this->ask_db->update($Updata);
                if (!empty($ResultID)) {
                    // 将这个问题下的所有答案设置为非最佳答案
                    $this->ask_answer_db->where('ask_id', $param['ask_id'])->update(['is_bestanswer' => 0]);
                    // 设置当前问题为最佳答案
                    $this->ask_answer_db->where('answer_id', $param['answer_id'])->update(['is_bestanswer' => 1]);
                    // 问题未解决并存在悬赏金则执行
                    $money = $this->ask_db->where('ask_id', $param['ask_id'])->value('money');
                    if (isset($AskInfo['status']) && 0 == $AskInfo['status'] && 0 < $money) {
                        $answer_user_id = $this->ask_answer_db->where('answer_id', $param['answer_id'])->value('users_id');
                        // 悬赏金额插入记录
                        $this->AskModel->setMoney($answer_user_id, $money, $param['ask_id'], $param['answer_id']);
                    }
                    $this->success('已采纳');
                } else {
                    $this->error('请选择采纳的回答');
                }
            } else {
                $this->error('无操作权限');
            }
        }
    }

    // 添加回答
    public function ajax_add_answer()
    {
        if (IS_AJAX_POST || IS_POST) {
            $param = input('param.');
            // 是否登录、是否允许发布问题、数据判断及处理，返回内容数据
            $content = $this->AnswerDealWith($param, false);
            /*添加数据*/
            $AddAnswer = [
                'ask_id'       => $param['ask_id'],
                // 如果这个会员组属于需要审核的，则追加。 默认1为已审核
                'is_review'    => 1 == $this->users['ask_is_review'] ? 0 : 1,
                'type_id'      => $param['type_id'],
                'users_id'     => $this->users_id,
                'username'     => $this->users['username'],
                'users_ip'     => clientIP(),
                'content'      => $content,
                // 若是回答答案则追加数据
                'answer_pid'   => !empty($param['answer_id']) ? $param['answer_id'] : 0,
                // 用户则追加数据
                'at_users_id'  => !empty($param['at_users_id']) ? $param['at_users_id'] : 0,
                'at_answer_id' => !empty($param['at_answer_id']) ? $param['at_answer_id'] : 0,
                'add_time'     => getTime(),
                'update_time'  => getTime(),
            ];
            $ResultID = $this->ask_answer_db->add($AddAnswer);
            /* END */

            if (!empty($ResultID)) {
                $ask_users_id = Db::name('ask')->where('ask_id',$param['ask_id'])->getField('users_id');
                if ($ask_users_id != $this->users_id){
                    // 回答加积分 自问自答不加积分
                    $this->AskModel->setScore($this->users_id, $param['ask_id'], $ResultID, 2);
                }
                // 增加问题回复数
                $this->AskModel->UpdateAskReplies($param['ask_id'], true);
                if (1 == $this->users['ask_is_review']) {
                    $this->success('回答成功，但你的回答需要管理员审核！', null, ['review' => true]);
                } else {
                    $AddAnswer['answer_id'] = $ResultID;
                    $AddAnswer['head_pic'] = $this->users['head_pic'];
                    $AddAnswer['at_usersname'] = '';
                    if (!empty($AddAnswer['at_users_id'])) {
                        $FindData = Db::name('users')->field('nickname, username')->where('users_id', $AddAnswer['at_users_id'])->find();
                        $AddAnswer['at_usersname'] = empty($FindData['nickname']) ? $FindData['username'] : $FindData['nickname'];
                    }
                    $ResultData = $this->AskLogic->GetReplyHtml($AddAnswer);
                    // 查询这个回答的评论回复数
                    $ResultData['comment_reply_num'] = $this->ask_answer_db->where('answer_pid', $AddAnswer['answer_pid'])->count();
                    $this->success('回答成功！', null, $ResultData);
                }
            } else {
                $this->error('提交信息有误，请刷新重试！');
            }
        }
    }

    // 编辑回答
    public function ajax_edit_answer()
    {
        if (IS_AJAX_POST || IS_POST) {
            $param = input('param.');
            $param['ask_id'] = intval($param['ask_id']);
            $param['answer_id'] = intval($param['answer_id']);
            // 是否登录、是否允许发布问题、数据判断及处理，返回内容数据
            $content = $this->AnswerDealWith($param, false);

            /*编辑数据*/
            $UpAnswerData = [
                'content'     => $content,
                'users_ip'    => clientIP(),
                'update_time' => getTime(),
            ];
            // 如果这个会员组属于需要审核的，则追加
            if (1 == $this->users['ask_is_review']) $UpAnswerData['is_review'] = 0;
            /* END */

            // 更新条件
            $where = [
                'answer_id' => $param['answer_id'],
                'ask_id'    => $param['ask_id'],
            ];
            if (empty($this->users['admin_id'])) $where['users_id'] = $this->users_id;

            // 更新数据
            $ResultId = $this->ask_answer_db->where($where)->update($UpAnswerData);
            if (!empty($ResultId)) {
                $url = $this->AskLogic->GetUrlData($param, 'AskDetailsUrl');
                if (1 == $this->users['ask_is_review']) {
                    $this->success('编辑成功，但你的回答需要管理员审核！', $url, ['review' => true]);
                } else {
                    $this->success('编辑成功！', $url);
                }
            } else {
                $this->error('编辑的信息有误，请检查！');
            }
        }

        // 是否允许发布问题
        $this->IsAnswer(false);

        $answer_id = input('param.answer_id/d');
        $where     = [
            'a.answer_id' => $answer_id,
        ];
        if (empty($this->users['admin_id'])) {
            $where['a.users_id'] = $this->users_id;
        }
        $AnswerData = $this->ask_answer_db->field('a.answer_id, a.ask_id, a.content, b.ask_title')
            ->alias('a')
            ->join('ask b', 'a.ask_id = b.ask_id', 'LEFT')
            ->where($where)
            ->find();
        if (empty($AnswerData)) $this->error('要修改的回答不存在！');

        // 更新人
        $AnswerData['nickname']  = $this->nickname;
        $result['Info'] = $AnswerData;
        $result['EditAnswerUrl'] = $this->AskLogic->GetUrlData(['answer_id' => $answer_id], 'EditAnswer');
        $result['AddAskUrl'] = $this->AskLogic->GetUrlData([], 'AddAskUrl');
        $result['NewDateUrl'] = $this->AskLogic->GetUrlData([], 'NewDateUrl');
        $result['SearchName'] = null;
        // 统计会员提问、回答、被评论、被点赞次数
        $CountUsersAsk = $this->AskModel->GetCountAskData($this->users_id);
        // 问答设置内容
        $AskSetting = $this->AskModel->getAskSetting($this->request->action());
        // 数组合并加载到模板
        $result = array_merge($result, $CountUsersAsk, $AskSetting);
        $eyou = array(
            'field' => $result,
        );
        $this->assign('eyou', $eyou);

        return $this->fetch('ask/edit_answer');
    }

    // 删除指定的问题、回答、评论、回复、点赞
    public function ajax_del_ask()
    {
        if (IS_AJAX_POST) {
            // 是否登录
            $this->UsersIsLogin();
            // 数据判断
            $type = input('param.type/d');
            $ask_id = input('param.ask_id/d');
            if (empty($this->users['admin_id'])) $this->error('无操作权限');
            if (empty($ask_id) || 1 != $type) $this->error('请选择要删除的提问问题');
            // 执行删除
            $Where['ask_id'] = $ask_id;
            $askData = $this->ask_db->where($Where)->find();
            $ResultID = $this->ask_db->where($Where)->delete();
            if (!empty($ResultID)) {
                // 同步删除对应问题下所有回答
                $this->ask_answer_db->where($Where)->delete();
                // 同步删除对应问题下所有点赞
                $this->ask_answer_like_db->where($Where)->delete();
                // 更新会员提问及回答所得积分和余额
                $this->AskModel->RebackDel($ask_id,$askData);
                // 返回跳转链接
                $url = $this->AskLogic->GetUrlData([], 'NewDateUrl');
                $this->success('删除成功！', $url);
            } else {
                $this->error('删除信息错误，请刷新重试');
            }
        }
        
    }

    // 删除指定的回答、评论、回复、点赞
    public function ajax_del_answer()
    {
        if (IS_AJAX_POST) {
            // 是否登录
            $this->UsersIsLogin();
            // 数据判断
            $type      = input('param.type/d');
            $ask_id    = input('param.ask_id/d');
            $answer_id = input('param.answer_id/d');
            if (empty($ask_id) || empty($answer_id) || 2 != $type) $this->error('请选择删除内容');
            // 执行删除
            $Where = [
                'ask_id'    => $ask_id,
                'answer_id' => $answer_id,
            ];
            // 若操作人为后台管理员则允许删除其他会员的回答、评论
            if (empty($this->users['admin_id'])) $Where['users_id'] = $this->users_id;
            // 查询整个回答评论回复共有条数的ID
            $CountWhere['answer_pid'] = $answer_id;
            $AskAnswerID = $this->ask_answer_db->where($Where)->whereOr($CountWhere)->column('answer_id');
            // 执行删除
            $ResultID = $this->ask_answer_db->where($Where)->whereOr($CountWhere)->delete();
            if (!empty($ResultID)) {
                // 同步删除对应问题下所有点赞
                $this->ask_answer_like_db->where('answer_id', 'IN', $AskAnswerID)->delete();
                // 减少问题回复数
                $this->AskModel->UpdateAskReplies($ask_id, false, count($AskAnswerID));
                $this->success('删除成功！');
            } else {
                $this->error('删除信息错误，请刷新重试');
            }
        }
    }

    // 删除问题、回答
    public function ajax_del_comment()
    {
        if (IS_AJAX_POST) {
            // 是否登录
            $this->UsersIsLogin();
            // 数据判断
            $type      = input('param.type/d');
            $ask_id    = input('param.ask_id/d');
            $answer_id = input('param.answer_id/d');
            if (empty($ask_id) || empty($answer_id) || 3 != $type) $this->error('请选择删除内容');
            // 执行删除
            $Where = [
                'ask_id'    => $ask_id,
                'answer_id' => $answer_id,
            ];
            // 若操作人为后台管理员则允许删除其他会员的回答、评论
            if (empty($this->users['admin_id'])) $Where['users_id'] = $this->users_id;
            // 执行删除
            $ResultID = $this->ask_answer_db->where($Where)->delete();
            if (!empty($ResultID)) {
                // 同步删除对应问题下所有点赞
                $this->ask_answer_like_db->where('answer_id', $answer_id)->delete();
                // 减少问题回复数
                $this->AskModel->UpdateAskReplies($ask_id, false, 1);
                $this->success('删除成功！');
            } else {
                $this->error('删除信息错误，请刷新重试');
            }
        }
    }

    // 点赞
    public function ajax_click_like()
    {
        if (IS_AJAX_POST) {
            // 是否登录
            $this->UsersIsLogin();
            $post = input('post.');
            $ask_id = !empty($post['ask_id']) ? intval($post['ask_id']) : 0;
            $answer_id = !empty($post['answer_id']) ? intval($post['answer_id']) : 0;
            $like_source = !empty($post['like_source']) ? intval($post['like_source']) : 0;
            if (empty($like_source) || (1 == $like_source && empty($ask_id))) {
                $this->error('请选择点赞信息');
            } else if (in_array($like_source, [2, 3]) && (empty($ask_id) || empty($answer_id))) {
                $this->error('请选择点赞信息');
            }
            $Where = [
                'ask_id'    => $ask_id,
                'answer_id' => $answer_id,
                'users_id'  => $this->users_id,
                'like_source' => $like_source
            ];
            $IsCount = $this->ask_answer_like_db->where($Where)->count();
            if (!empty($IsCount)) {
                $this->error('您已赞过！');
            } else {
                // 添加新的点赞记录
                $AddData  = [
                    'click_like'  => 1,
                    'like_source' => $like_source,
                    'users_ip'    => clientIP(),
                    'add_time'    => getTime(),
                    'update_time' => getTime(),
                ];
                $AddData  = array_merge($Where, $AddData);
                $ResultID = $this->ask_answer_like_db->add($AddData);
                if (!empty($ResultID)) {
                    if (1 == $like_source) {
                        unset($Where['users_id']);
                    } else if (in_array($like_source, [2, 3])) {
                        $Where = [
                            'answer_id' => $answer_id,
                            'like_source' => $like_source
                        ];
                    }
                    $LikeCount = $this->ask_answer_like_db->where($Where)->count();
                    if (1 == $LikeCount) {
                        $LikeName = '<a href="javascript:void(0);">' . $this->nickname . '</a>';
                    } else {
                        $LikeName = '<a href="javascript:void(0);">' . $this->nickname . '</a>、 ';
                    }
                    $data = [
                        // 点赞数
                        'LikeCount' => $LikeCount,
                        // 点赞人
                        'LikeName'  => $LikeName,
                    ];

                    // 同步点赞次数到答案表
                    $Where = [
                        'ask_id'    => $ask_id,
                        'answer_id' => $answer_id
                    ];
                    $UpdataNew = [
                        'click_like'  => $LikeCount,
                        'update_time' => getTime(),
                    ];
                    $this->ask_answer_db->where($Where)->update($UpdataNew);
                    $this->success('点赞成功！', null, $data);
                }
            }
        }
    }

    // 审核问题，仅创始人有权限
    public function ajax_review_ask()
    {
        if (IS_AJAX_POST) {
            // 创始人才有权限在前台审核评论
            if (0 == $this->parent_id) {
                $this->UsersIsLogin();
                $param = input('param.');
                if (empty($param['ask_id'])) $this->error('请选择需要审核的问题！');
                // 更新审核问题
                $UpAakData = [
                    'is_review'   => 1,
                    'update_time' => getTime(),
                ];
                $ResultId  = $this->ask_db->where('ask_id', intval($param['ask_id']))->update($UpAakData);
                if (!empty($ResultId)) $this->success('审核成功！');
                $this->error('审核失败！');
            } else {
                $this->error('没有操作权限！');
            }
        }
    }

    // 审核评论，仅管理员有权限
    public function ajax_review_comment()
    {
        if (IS_AJAX_POST) {
            // 创始人才有权限在前台审核评论
            if (0 == $this->parent_id) {
                $this->UsersIsLogin();
                $param = input('param.');
                if (empty($param['ask_id']) || empty($param['answer_id'])) $this->error('提交信息有误，请刷新重试！');
                // 更新审核评论
                $where        = [
                    'ask_id'    => intval($param['ask_id']),
                    'answer_id' => intval($param['answer_id']),
                ];
                $UpAnswerData = [
                    'is_review'   => 1,
                    'update_time' => getTime(),
                ];
                $ResultId     = $this->ask_answer_db->where($where)->update($UpAnswerData);
                if (!empty($ResultId)) $this->success('审核成功！');
                $this->error('审核失败！');
            } else {
                $this->error('没有操作权限！');
            }
        }
    }

    // 获取指定数量的评论数据（分页）
    public function ajax_show_comment()
    {
        if (IS_AJAX_POST) {
            $param = input('param.');
            $Comment = $this->AskModel->GetAskReplyData($param, $this->parent_id);
            $Data = !empty($param['is_comment']) ? $Comment['AnswerData'][0]['AnswerSubData'] : $Comment['BestAnswer'][0]['AnswerSubData'];
            if (!empty($Data)) {
                $ResultData = $this->AskLogic->ForeachReplyHtml($Data, $this->parent_id, $param['is_comment'], $this->users_id);
                if (empty($ResultData['htmlcode'])) $this->error('没有更多数据');
                $this->success('加载完成', null, $ResultData);
            } else {
                $this->error('没有更多数据');
            }
        }
    }

    // 加载会员中心所需数据
    private function AssignData($param = array())
    {
        // 是否登录
        $this->UsersIsLogin();

        // 主题颜色
        $this->assign('usersConfig', $this->usersConfig);
        $this->usersConfig['theme_color'] = $theme_color = !empty($this->usersConfig['theme_color']) ? $this->usersConfig['theme_color'] : '#ff6565'; // 默认主题颜色
        $this->assign('theme_color', $theme_color);

        // 是否为手机端
        $is_mobile = isMobile() ? 1 : 2;
        $this->assign('is_mobile', $is_mobile);

        // 是否为端微信
        $is_wechat = isWeixin() ? 1 : 2;
        $this->assign('is_wechat', $is_wechat);

        // 是否为微信端小程序
        $is_wechat_applets = isWeixinApplets() ? 1 : 0;
        $this->assign('is_wechat_applets', $is_wechat_applets);

        // 焦点
        $Focus = empty($param['method']) ? 1 : 2;
        $this->assign('Focus', $Focus);

        // 菜单名称
        $this->MenuTitle = Db::name('users_menu')->where([
            'mca'  => 'home/Ask/ask_index',
            'lang' => $this->home_lang,
        ])->getField('title');
        $this->assign('MenuTitle', $this->MenuTitle);
    }

    // 是否登录
    private function UsersIsLogin()
    {
        if (empty($this->users) || empty($this->users_id)) $this->error('请先登录！');
    }

    // 是否允许发布、编辑问题
    private function IsRelease($is_add = true)
    {
        if (empty($this->users['ask_is_release'])) {
            if (!empty($is_add)) {
                $this->error($this->users['level_name'] . '不允许发布问题！');
            } else {
                $this->error($this->users['level_name'] . '不允许编辑问题！');
            }
        }
    }

    // 问题数据判断及处理，返回问题内容数据
    private function ParamDealWith($param = [], $is_add = true)
    {
        // 是否登录
        $this->UsersIsLogin();
        // 是否允许发布、编辑
        $this->IsRelease($is_add);

        /*数据判断*/
        $content = '';
        if (empty($param)) $this->error('请提交完整信息！');
        if (empty($param['title'])) $this->error('请填写问题标题！');
        if (empty($param['ask_type_id'])) $this->error('请选择问题分类！');
        $content = $this->AskLogic->ContentDealWith($param);
        if (empty($content)) $this->error('请填写问题描述！');

        // 编辑时执行判断
        if (empty($is_add) && empty($param['ask_id'])) $this->error('请确认编辑问题！');
        /* END */

        return $content;
    }

    // 是否允许发布、编辑评论回复
    private function IsAnswer($is_add = true)
    {
        if (empty($this->users['ask_is_release'])) {
            if (!empty($is_add)) {
                $this->error($this->users['level_name'] . '不允许回复答案！');
            } else {
                $this->error($this->users['level_name'] . '不允许编辑答案！');
            }
        }
    }

    // 评论回复数据处理，返回评论回复内容数据
    private function AnswerDealWith($param = [], $is_add = true)
    {
        // 是否登录
        $this->UsersIsLogin();
        // 是否允许发布、编辑
        $this->IsAnswer($is_add);

        /*数据判断*/
        if (!empty($is_add)) {
            // 添加时执行判断
            if (empty($param) || empty($param['ask_id'])) $this->error('提交信息有误，请刷新重试！');
        } else {
            // 编辑时执行判断
            if (empty($is_add) && empty($param['ask_id'])) $this->error('请确认编辑问题！');
        }

        $content = '';
        $content = $this->AskLogic->ContentDealWith($param);
        if (empty($content)) $this->error('请写下你的回答！');
        /* END */

        return $content;
    }

    /**
     * 获取登录的会员最新数据
     */
    private function GetUsersLatestData($users_id = null)
    {
        $users_id = empty($users_id) ? session('users_id') : $users_id;
        if (!empty($users_id)) {
            /*读取的字段*/
            $field = 'a.*, b.*, b.discount as level_discount';
            /* END */

            /*查询数据*/
            $users = \think\Db::name('users')->field($field)
                ->alias('a')
                ->join('__USERS_LEVEL__ b', 'a.level = b.level_id', 'LEFT')
                ->where([
                    'a.users_id'      => $users_id,
                    'a.lang'          => get_home_lang(),
                    'a.is_activation' => 1,
                    'a.is_del'        => 0,
                ])->find();
            // 会员不存在则返回空
            if (empty($users)) return false;
            /* END */

            /*会员数据处理*/
            // 头像处理
            $users['head_pic'] = get_head_pic($users['head_pic']);
            // 昵称处理
            $users['nickname'] = empty($users['nickname']) ? $users['username'] : $users['nickname'];
            // 密码为空并且存在openid则表示微信注册登录，密码字段更新为0，可重置密码一次。
            $users['password'] = empty($users['password']) && !empty($users['open_id']) ? 0 : 1;
            // 删除登录密码及支付密码
            unset($users['paypwd']);
            // 级别处理
            $LevelData = [];
            if (intval($users['level_maturity_days']) >= 36600) {
                $users['maturity_code'] = 1;
                $users['maturity_date'] = '终身';
            } else if (0 == $users['open_level_time'] && 0 == $users['level_maturity_days']) {
                $users['maturity_code'] = 0;
                $users['maturity_date'] = '';// 没有升级会员，置空
            } else {
                /*计算剩余天数*/
                $days = $users['open_level_time'] + ($users['level_maturity_days'] * 86400);
                // 取整
                $days = ceil(($days - getTime()) / 86400);
                if (0 >= $days) {
                    /*更新会员的级别*/
                    $LevelData = model('EyouUsers')->UpUsersLevelData($users_id);
                    /* END */
                    $users['maturity_code'] = 2;
                    $users['maturity_date'] = '';// 会员过期，置空
                } else {
                    $users['maturity_code'] = 3;
                    $users['maturity_date'] = $days . ' 天';
                }
                /* END */
            }
            /* END */

            // 合并数据
            $LatestData = array_merge($users, $LevelData);
            /*更新session*/
            session('users', $LatestData);
            // session('open_id',  $LatestData['open_id']);
            session('users_id', $LatestData['users_id']);
            cookie('users_id', $LatestData['users_id']);
            /* END */
            // 返回数据
            return $LatestData;
        } else {
            // session中不存在会员ID则返回空
            session('users_id', null);
            session('users', null);
            cookie('users_id', null);
            return false;
        }
    }
}