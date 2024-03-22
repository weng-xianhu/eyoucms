<?php
/**
 * User: xyz
 * Date: 2022/8/12
 * Time: 9:31
 */

namespace app\api\logic\v1;

use think\Db;

class AskLogic
{
// 拼装html代码
    public function GetReplyHtml($data = array())
    {
        $ReplyHtml = '';
        // 如果是需要审核的评论则返回空
        if (empty($data['is_review'])) return $ReplyHtml;

        /*拼装html代码*/
        // 友好显示时间
        $data['add_time'] = friend_date($data['add_time']);
        // 处理内容格式
        $data['content']  = htmlspecialchars_decode($data['content']);
        if (!empty($data['at_users_id'])) {
            $data['content'] = '回复 @'.$data['at_usersname'].':&nbsp;'.$data['content'];
        }
        // 删除评论回答URL
        $DelAnswerUrl = $this->GetUrlData($data, 'DelAnswerUrl');

        // 拼装html
        $ReplyHtml = <<<EOF
<li class="secend-li" id="{$data['answer_id']}_answer_li">
    <div class="head-secend">
        <a><img src="{$data['head_pic']}" style="width:30px;height:30px;border-radius:100%;margin-right: 16px;"></a>
        <strong>{$data['username']}</strong>
        <span style="margin:0 10px"> | </span>
        <span>{$data['add_time']}</span>
        <div style="flex-grow:1"></div>
        <span id="{$data['answer_id']}_replyA" onclick="replyUser('{$data['answer_pid']}','{$data['users_id']}','{$data['username']}','{$data['answer_id']}')" class="secend-huifu-btn" style="cursor: pointer;">回复</span>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        <a data-url="{$DelAnswerUrl}" onclick="DataDel(this, '{$data['answer_id']}', 2)" class="secend-huifu-btn" style="cursor: pointer; color:red;">删除</a>
    </div>
    <div class="secend-huifu-text">
        {$data['content']}
    </div>
</li>
EOF;
        // 返回html
        $ReturnHtml = ['review' => false, 'htmlcode' => $ReplyHtml];
        return $ReturnHtml;
    }

    // Url处理
    public function GetUrlData($param = array(), $SpecifyUrl = null)
    {
        if (empty($param['ask_id'])) $param['ask_id'] = 0;
        $result = [];
        // 最新问题url
        $result['NewDateUrl'] = url('plugins/Ask/index');
        // ROOT_DIR.'/index.php?m=plugins&c=Ask&a=index';

        // 问题详情页url
        $result['AskDetailsUrl'] = url('plugins/Ask/details', ['ask_id'=>$param['ask_id']]);
        // ROOT_DIR.'/index.php?m=plugins&c=Ask&a=details&ask_id='.$param['ask_id'];

        // 推荐问题url
        $result['RecomDateUrl'] = url('plugins/Ask/index', ['type_id'=>0, 'is_recom'=>1]);
        // ROOT_DIR.'/index.php?m=plugins&c=Ask&a=index&is_recom=1';
        // 悬赏问题列表url
        $result['RewardUrl'] = url('plugins/Ask/index', ['type_id'=>0, 'is_recom'=>3]);

        // 等待回答url
        $result['PendingAnswerUrl'] = url('plugins/Ask/index', ['type_id'=>0, 'is_recom'=>2]);
        // ROOT_DIR.'/index.php?m=plugins&c=Ask&a=index&is_recom=2';

        // 提交回答url
        $result['AddAnswerUrl'] = url('plugins/Ask/ajax_add_answer', ['ask_id'=>$param['ask_id'], '_ajax'=>1], true, false, 1, 1, 0);
        // ROOT_DIR.'/index.php?m=plugins&c=Ask&a=ajax_add_answer&ask_id='.$param['ask_id'];

        // 删除回答url
        $result['DelAnswerUrl'] = url('plugins/Ask/ajax_del_answer', ['ask_id'=>$param['ask_id'], '_ajax'=>1], true, false, 1, 1, 0);
        // ROOT_DIR.'/index.php?m=plugins&c=Ask&a=ajax_del_answer&ask_id='.$param['ask_id'];

        // 点赞回答url
        $result['ClickLikeUrl'] = url('plugins/Ask/ajax_click_like', ['_ajax'=>1], true, false, 1, 1, 0);
        // ROOT_DIR.'/index.php?m=plugins&c=Ask&a=ajax_click_like';

        // 发布问题url
        $result['AddAskUrl'] = url('plugins/Ask/add_ask');
        // 提交问题url
        $result['SubmitAddAsk'] = url('plugins/Ask/add_ask', ['_ajax'=>1], true, false, 1, 1, 0);
        // ROOT_DIR.'/index.php?m=plugins&c=Ask&a=add_ask';

        // 编辑问题url
        $result['EditAskUrl'] = url('plugins/Ask/edit_ask', ['ask_id'=>$param['ask_id']]);
        // ROOT_DIR.'/index.php?m=plugins&c=Ask&a=edit_ask&ask_id='.$param['ask_id'];

        // 用户问题首页
        $result['UsersIndexUrl'] = url('plugins/Ask/ask_index');
        // ROOT_DIR.'/index.php?m=plugins&c=Ask&a=ask_index';

        // 编辑回答url
        $result['EditAnswer'] = url('plugins/Ask/ajax_edit_answer');
        // ROOT_DIR.'/index.php?m=plugins&c=Ask&a=ajax_edit_answer';
        if ('ajax_edit_answer' == request()->action()) {
            $result['EditAnswer'] = url('plugins/Ask/ajax_edit_answer', ['_ajax'=>1], true, false, 1, 1, 0);
        }

        // 采纳最佳答案url
        $result['BestAnswerUrl'] = url('plugins/Ask/ajax_best_answer', ['ask_id'=>$param['ask_id'], '_ajax'=>1], true, false, 1, 1, 0);
        // ROOT_DIR.'/index.php?m=plugins&c=Ask&a=ajax_best_answer&ask_id='.$param['ask_id'];

        // 获取指定数量的评论数据（分页）
        $result['ShowCommentUrl'] = url('plugins/Ask/ajax_show_comment', ['ask_id'=>$param['ask_id'], '_ajax'=>1], true, false, 1, 1, 0);
        // ROOT_DIR.'/index.php?m=plugins&c=Ask&a=ajax_show_comment&ask_id='.$param['ask_id'].'&_ajax=1';

        // 创始人审核评论URL(前台)
        $result['ReviewCommentUrl'] = url('plugins/Ask/ajax_review_comment', ['ask_id'=>$param['ask_id'], '_ajax'=>1], true, false, 1, 1, 0);
        // ROOT_DIR.'/index.php?m=plugins&c=Ask&a=ajax_review_comment&ask_id='.$param['ask_id'].'&_ajax=1';

        // 创始人审核问题URL(前台)
        $result['ReviewAskUrl'] = url('plugins/Ask/ajax_review_ask', ['_ajax'=>1], true, false, 1, 1, 0);
        // ROOT_DIR.'/index.php?m=plugins&c=Ask&a=ajax_review_ask&_ajax=1';

        // 按点赞量排序url
        $result['AnswerLikeNum'] = url('plugins/Ask/details', ['ask_id' => $param['ask_id']], true, false, 1, 1, 0);

        // 等待回答url
        if (!empty($param['type_id'])) {
            $result['PendingAnswerUrl'] = url('plugins/Ask/index', ['type_id'=>$param['type_id'], 'is_recom'=>2]);
            // ROOT_DIR.'/index.php?m=plugins&c=Ask&a=index&type_id='.$param['type_id'].'&is_recom=2';
        }

        if (!empty($SpecifyUrl)) {
            if (!empty($result[$SpecifyUrl])) {
                return $result[$SpecifyUrl];
            }else{
                return $result['NewDateUrl'];
            }
        }else{
            return $result;
        }
    }
    /*
    * 获取限制提交频率
    */
    public function GetRateData(){
        $rate = [];
        $data = Db::name('weapp')->where(['code'=>'Ask'])->getField('data');
        $dataArr = unserialize($data);
        !empty($dataArr['rate']) && $rate = $dataArr['rate'];

        return $rate;
    }

    // 内容转义处理
    public function ContentDealWith($param = null)
    {
        if (!empty($param['content'])) {
            $content = $param['content'];
        }else if(!empty($param['ask_content'])){
            $content = $param['ask_content'];
        }else{
            return false;
        }

        // 斜杆转义
        $content = addslashes($content);
        // 过滤内容的style属性
        $content = preg_replace('/style(\s*)=(\s*)[\'|\"](.*?)[\'|\"]/i', '', $content);
        // 过滤内容的class属性
        $content = preg_replace('/class(\s*)=(\s*)[\'|\"](.*?)[\'|\"]/i', '', $content);

        return $content;
    }
    /*
        * 获取敏感词过滤设置信息
        */
    public function GetSensitiveData(){
        $sensitive = [];
        $data = Db::name('weapp')->where(['code'=>'Ask'])->getField('data');
        $dataArr = unserialize($data);
        !empty($dataArr['sensitive']) && $sensitive = $dataArr['sensitive'];
        if (!empty($sensitive['sensitive_data'])){
            $sensitive['sensitive_data'] = str_replace("，",",",$sensitive['sensitive_data']);
            $sensitive['sensitive_data'] = explode(",",$sensitive['sensitive_data']);
        }

        return $sensitive;
    }
    /*
     * 判断是否存在敏感词
     * @paramarray $list 定义敏感词一维数组
     * @paramstring $string 要过滤的内容
     * @returnstring $log 处理结果[敏感词个数，敏感词内容，替换后的字符串]
     */
    public function sensitive($list, $string){
        $count = 0; //违规词的个数
        $sensitiveWord = ''; //违规词
        $stringAfter = $string; //替换后的内容
        $pattern = "/" . implode("|", $list) . "/i"; //定义正则表达式
        if (preg_match_all($pattern, $string, $matches)) { //匹配到了结果
            $patternList = $matches[0]; //匹配到的数组
            $count = count($patternList);
            $sensitiveWord = implode(',', $patternList); //敏感词数组转字符串
            $replaceArray = array_combine($patternList, array_fill(0, count($patternList), '**')); //把匹配到的数组进行合并，替换使用
            $stringAfter = strtr($string, $replaceArray); //结果替换
        }

        return [$count,$sensitiveWord,$stringAfter];

    }

}