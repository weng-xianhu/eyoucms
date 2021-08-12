// 滑动到指定位置，仅允许传入ID
function SwipeSwitch(specified) {
    var div_top = document.getElementById(specified).offsetTop;
    window.scrollTo(0, div_top + 70);
}

// 发布/编辑提问数据
function SubmitAskData(obj) {
    if (!$('#title').val()) {
        layer.msg('请填写问题标题！', {time: 1500});
        $('#title').focus();
        return false;
    }
    if (0 == $('#ask_type_id').val()) {
        layer.msg('请选择问题分类！', {time: 1500});
        $('#ask_type_id').focus();
        return false;
    }
    if (!$('textarea[name="content"]').val()) {
        layer.msg('请填写问题描述！', {time: 1500});
        return false;
    }
    layer_loading('正在处理');
    $.ajax({
        url : $(obj).data('url'),
        data: $('#SubmitAskFormData').serialize(),
        type:'post',
        dataType:'json',
        success:function(res){
            layer.closeAll();
            if (1 == res.code) {
                layer.msg(res.msg, {time: 1000},function() {
                    window.location.href = res.url;
                });
            } else {
                layer.msg(res.msg, {time: 1500});
            }
        },
        error : function() {
            layer.closeAll();
            layer.alert('未知错误，请刷新页面后重试', {icon: 5, closeBtn: 0, title: false});
        }
    });
}

// 管理员审核发布的问题
function ReviewAsk(obj, ask_id) {
    layer.confirm('确认审核该问题？', {
        btn: ['确认', '取消'],
        title: false,
        closeBtn: 0
    }, function () {
        layer_loading('正在处理');
        $.ajax({
            url : $(obj).data('url'),
            type: 'post',
            data: {ask_id: ask_id},
            dataType:'json',
            success:function(res) {
                layer.closeAll();
                if (1 == res.code) {
                    // 删除对应的审核按钮
                    $(obj).remove();
                    layer.msg(res.msg, {time: 1500});
                } else {
                    layer.msg(res.msg, {time: 2000});
                }
            }
        });
    });
}

// 管理员审核发布的回答、评论、回复
function Review(obj, answer_id, status) {
    layer.confirm('确认审核该评论？', {
        btn: ['确认', '取消'],
        title: false,
        closeBtn: 0
    }, function () {
        layer_loading('正在处理');
        $.ajax({
            url : $(obj).data('url'),
            type:'post',
            data: {answer_id:answer_id},
            dataType:'json',
            success:function(res){
                layer.closeAll();
                if (1 == res.code) {
                    // 删除对应的审核按钮
                    $(obj).remove();
                    // 显示对应的采纳最佳答案按钮
                    if (1 == status) $('#BestAnswer_'+answer_id).show();
                    layer.msg(res.msg, {time: 1500});
                } else {
                    layer.msg(res.msg, {time: 2000});
                }
            }
        });
    });
}

// 回答问题
function AnswerQuestions(obj) {
    if (!$('textarea[name="ask_content"]').val()) {
        layer.msg('请写下你的回答！', {time: 1500, icon: 2});
        return false;
    }

    layer_loading('正在处理');
    $.ajax({
        url: $(obj).data('url'),
        data: $('#AnswerQuestions1616551871').serialize(),
        type:'post',
        dataType:'json',
        success:function(res){
            layer.closeAll();
            if (1 == res.code) {
                var times = res.data.review ? 2500 : 1000;
                layer.msg(res.msg, {time: times}, function() {
                    window.location.reload();
                });
            } else {
                layer.msg(res.msg, {time: 1500, icon: 2});
            }
        },
        error : function() {
            layer.closeAll();
            layer.alert('未知错误，请刷新页面后重试', {icon: 5, closeBtn: 0, title: false});
        }
    });
}

// 采纳最佳回答
function BestAnswer(obj, answer_id, users_id) {
    if (!answer_id) layer.msg('请选择采纳的回答！', {time: 1500, icon: 2});
    //提交服务器
    layer_loading('正在处理');
    $.ajax({
        url : $(obj).data('url'),
        type: 'post',
        data: {answer_id:answer_id,users_id:users_id},
        dataType: 'json',
        success: function(res){
            layer.closeAll();
            if (1 == res.code) {
                layer.msg(res.msg, {time: 1000},function(){
                    window.location.reload();
                });
            } else {
                layer.msg(res.msg, {time: 1500, icon: 2});
            }
        }
    });
}

// 显示评论框
function ShowCommentFrame(answer_id) {
    var _this = $("#comment_answer_"+answer_id);
    var display = $("#comment_answer_"+answer_id).css('display'); 
    if (display == 'none') {
        $('.reply-form, .CommentReplyBox').css('display', 'none');
        $('.btn-reply').text('回复');
        $(_this).css('display', 'flex'); 
        $(_this).children(':input').focus();
    } else if (display == 'flex') {
         $(_this).css('display', 'none');
    }
}

// 提交评论内容
function SubmitCommentData(obj, answer_id) {
    if ($('#comment_answer_input_'+answer_id).val() == '') {
        layer.msg('请输入评论内容', {time: 1500});
        $('#comment_answer_input_'+answer_id).focus();
        return false;
    }
    //提交服务器
    layer_loading('正在处理');
    $.ajax({
        url : $(obj).data('url'),
        type: 'post',
        dataType: 'json',
        data: $('#comment_answer_form_'+answer_id).serialize(),
        success: function(res) {
            layer.closeAll();
            if (1 == res.code) {
                // 提示及追加html处理
                var times = res.data.review ? 2000 : 1000;
                if (res.data.htmlcode) $("#comment_answer_div_"+res.data.answer_pid).append(res.data.htmlcode);
                if (res.data.comment_reply_num) $("#comment_reply_num_"+res.data.answer_pid).html(res.data.comment_reply_num);
                $("#comment_answer_"+res.data.answer_pid).css('display', 'none');
                $('#comment_answer_input_'+res.data.answer_pid).val('');
                layer.msg(res.msg, {time: times});
            } else {
                layer.msg(res.msg, {time: 1500, icon: 2});
            }
        },
        error : function() {
            layer.closeAll();
            layer.alert('未知错误，请刷新页面后重试', {icon: 5});
        }
    });
}

// 显示回复框
function ShowReplyFrame(obj, answer_id) {
    var _this = $("#reply_comment_"+answer_id);
    var display = $("#reply_comment_"+answer_id).css('display'); 
    if (display == 'none') {
        $('.reply-form, .CommentReplyBox').css('display', 'none');
        $(_this).css('display', 'flex'); 
        $(_this).children(':input').focus();
        $(obj).text('收起回复');
        $(obj).parent().parent().parent().siblings().children('.tool').children('.tool-l').children('.btn-reply').text('回复');
    } else if (display == 'flex') {
        $(_this).css('display', 'none');
        $(obj).text('回复');
    }
}

// 提交回复内容
function SubmitReplyData(obj, answer_id) {
    if ($('#reply_comment_input_'+answer_id).val() == '') {
        layer.msg('请输入回复内容', {time: 1500});
        $('#reply_comment_input_'+answer_id).focus();
        return false;
    }
    //提交服务器
    layer_loading('正在处理');
    $.ajax({
        url : $(obj).data('url'),
        type: 'post',
        dataType: 'json',
        data: $('#reply_comment_form_'+answer_id).serialize(),
        success: function(res) {
            layer.closeAll();
            if (1 == res.code) {
                // 提示及追加html处理
                var times = res.data.review ? 2000 : 1000;
                if (res.data.htmlcode) $("#comment_answer_div_"+res.data.answer_pid).append(res.data.htmlcode);
                if (res.data.comment_reply_num) $("#comment_reply_num_"+res.data.answer_pid).html(res.data.comment_reply_num);
                $('#show_reply_frame_'+answer_id).text('回复');
                $("#reply_comment_"+answer_id).css('display', 'none');
                $('#reply_comment_input_'+answer_id).val('');
                layer.msg(res.msg, {time: times});
            } else {
                layer.msg(res.msg, {time: 1500, icon: 2});
            }
        },
        error : function() {
            layer.closeAll();
            layer.alert('未知错误，请刷新页面后重试', {icon: 5});
        }
    });
}

// 删除提问、评论、回复操作
function DataDel(obj, answer_id, type, answer_pid) {
    if (type == 1) {
        // 删除整个提问及提问下的所有回答、评论、回复内容
        DelAskData($(obj).data('url'), type);
    } else if (type == 2) {
        // 删除整个回答及回答下的所有评论、回复内容
        DelAnswerData($(obj).data('url'), answer_id, type);
    } else if (type == 3) {
        // 删除评论回复
        DelCommentData($(obj).data('url'), answer_id, type, answer_pid);
    }
}

// 删除整个提问及提问下的所有回答、评论、回复内容
function DelAskData(url, type) {
    layer.confirm('您确定要删除该提问？', {
        btn: ['确认', '取消'],
        title: false,
        closeBtn: 0
    }, function () {
        layer_loading('正在处理');
        $.ajax({
            url : url,
            type:'post',
            data: {type: type},
            dataType:'json',
            success:function(res) {
                layer.closeAll();
                if (1 == res.code) {
                    layer.msg(res.msg, {time: 1500},function(){
                        window.location.href = res.url;
                    });
                } else {
                    layer.msg(res.msg, {time: 2000});
                }
            }
        });
    });
}

// 删除整个回答及回答下的所有评论、回复内容
function DelAnswerData(url, answer_id, type) {
    if (!answer_id) layer.msg('请选择删除内容', {time: 1500});
    layer.confirm('您确定要删除该回答？', {
        btn: ['确认', '取消'],
        title: false,
        closeBtn: 0
    }, function () {
        layer_loading('正在处理');
        $.ajax({
            url : url,
            type:'post',
            data: {answer_id: answer_id, type: type},
            dataType:'json',
            success:function(res) {
                layer.closeAll();
                if (1 == res.code) {
                    layer.msg(res.msg, {time: 1000});
                    $('#comment_info_div_'+answer_id).remove();
                } else {
                    layer.msg(res.msg, {time: 2000});
                }
            }
        });
    });
}

// 删除评论回复
function DelCommentData(url, answer_id, type, answer_pid) {
    if (!answer_id) layer.msg('请选择删除内容', {time: 1500});
    layer_loading('正在处理');
    $.ajax({
        url :  url,
        type: 'post',
        data: {answer_id: answer_id, type: type},
        dataType: 'json',
        success: function(res) {
            layer.closeAll();
            if (1 == res.code) {
                $('#reply_comment_div_'+answer_id).remove();
                var CommentReplyNum = $("#comment_reply_num_"+answer_pid).html() - 1;
                $("#comment_reply_num_"+answer_pid).html(CommentReplyNum);
            } else {
                layer.msg(res.msg, {time: 1500});
            }
        }
    });
}

// 编辑问题
function EditAnswer(obj, answer_id) {
    var url = $(obj).attr('data-url');
    if (url.indexOf('?') > -1) {
        url += '&';
    } else {
        url += '?';
    }
    url += 'answer_id=' + answer_id;
    window.location.href = url;
    return false;
}

// 提交编辑回答内容
function SubmitAnswerData(obj) {
    if (!$('textarea[name="content"]').val()) {
        layer.msg('请填写问题描述！', {time: 1500, icon: 2});
        return false;
    }
    layer_loading('正在处理');
    $.ajax({
        url : $(obj).data('url'),
        data: $('#SubmitAnswerFormData').serialize(),
        type:'post',
        dataType:'json',
        success:function(res) {
            layer.closeAll();
            if (1 == res.code) {
                var times = res.data.review ? 2500 : 1000;
                layer.msg(res.msg, {time: times},function() {
                    window.location.href = res.url;
                });
            } else {
                layer.msg(res.msg, {time: 1500, icon: 2});
            }
        }
    });
}

// 点赞
function ClickLike(obj, ask_id, answer_id, like_source) {
    if ($(obj).attr('data-is_like')) {
        layer.msg('您已赞过！', {time: 1500});
        return false;
    }
    $.ajax({
        url: $(obj).data('url'),
        type: 'POST',
        dataType: 'json',
        data: {ask_id: ask_id, answer_id: answer_id, like_source: like_source},
        success: function(res) {
            if (1 == res.code) {
                // 点赞次数
                if (1 == like_source) {
                    $('#AskLikeNum_'+ask_id).html(res.data.LikeCount);
                } else if (2 == like_source) {
                    $('#CommentLikeNum_'+answer_id).html(res.data.LikeCount);
                } else if (3 == like_source) {
                    $('#ReplyLikeNum_'+answer_id).html(res.data.LikeCount);
                }
            } else {
                layer.msg(res.msg, {time: 1500});
            }
            // 设置当前用户已点赞过，用户再次点击则不需要执行ajax
            $(obj).attr('data-is_like', true);
        }
    });
}

// 获取指定数量的评论数据（分页）
function ShowComment(obj, answer_id, is_comment) {
    // 处理查询数据
    var firstRow = $(obj).attr('data-firstRow');
    var listRows = $(obj).attr('data-listRows');
    firstRow = Number(firstRow) + 5;
    //提交服务器
    layer_loading('正在处理');
    var loading = '正在加载...<img src="data:image/gif;base64,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" />';
    $(obj).html(loading);
    $.ajax({
        url : $(obj).data('url'),
        type: 'post',
        data: {answer_id:answer_id, firstRow:firstRow, listRows:listRows, is_comment:is_comment},
        dataType: 'json',
        success: function(res){
            layer.closeAll();
            if (1 == res.code) {
                console.log(firstRow);
                // 追加html处理
                if (res.data.htmlcode) $("#comment_answer_div_"+answer_id).append(res.data.htmlcode);
                // 更新下一次提交查询数量
                $(obj).attr('data-firstRow', firstRow).html('查看更多');
            } else {
                layer.msg(res.msg, {time: 1500});
                $(obj).hide();
            }
        }
    });
}

// 对输入框限制的内容与字数处理
function dealInputContentAndSize(obj){
    var _obj = $(obj);
    str = _obj.val();
    var maxLength = _obj.attr("maxlength");
    var returnValue = ''; 
    var count = 0; 
    var temp = 0;
    for (var i = 0; i < str.length; i++) { 
        count += 1; 
        temp = 1;
        if (count > maxLength) {
            count -= temp;
            break; 
        }
        returnValue += str[i]; 
    } 
    _obj.val(returnValue);
}

// 按点赞量排序
function AnswerLike(obj) {
    var url = $(obj).data('url');
    var sort_order = $(obj).data('sort_order');
    if (url.indexOf('?') > -1) {
        url += '&';
    } else {
        url += '?';
    }
    url += 'click_like=' + sort_order + '#comment';
    window.location.href = url;
}

function VerifyBalance(obj, UsersMoney) {
    var InputValue = $(obj).val();
    if (Number(InputValue) > Number(UsersMoney)) {
        layer.msg('账户最大余额：' + UsersMoney, {time: 1500});
        return false;
    }
}

// 加载层
function layer_loading(msg){
    var loading = layer.msg(
    msg+'...&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;请勿刷新页面', 
    {
        icon: 1,
        time: 3600000, //1小时后后自动关闭
        shade: [0.2] //0.1透明度的白色背景
    });
    //loading层
    var index = layer.load(3, {
        shade: [0.1,'#fff'] //0.1透明度的白色背景
    });
    return loading;
}