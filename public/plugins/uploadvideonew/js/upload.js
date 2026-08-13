// 当前窗口的对象
var parentObj = parent.layer.getFrameIndex(window.name);

/*----------------------------- 视频目录的树形结构 start ----------------------------------*/
var zNodes;
var myLayout;
jQuery(document).ready(function () {
    // 仅在页面包含完整的 jQuery Layout 结构时才初始化
    if (jQuery(".ui-layout-west").length > 0 || jQuery(".ui-layout-north").length > 0) {
        try {
            myLayout = jQuery("body").layout({
            /*  全局配置 */
                closable:                   false    /* 是否显示点击关闭隐藏按钮*/
            ,   resizable:                  true    /* 是否允许拉动*/
            ,   maskContents:               true    /* 加入此参数，框架内容页就可以拖动了*/
            /*  顶部配置 */
            ,   north__spacing_open:        0       /* 顶部边框大小*/
            /*  底部配置 */
            ,   south__spacing_open:        0       /* 底部边框大小*/
            ,   west__spacing_open:         0       /* 左部边框大小*/
            ,   east__spacing_open:         1       /* 右部边框大小*/
            /*  some pane-size settings*/
            ,   west__minSize:              140     /*左侧最小宽度*/
            ,   west__maxSize:              143     /*左侧最大宽度*/
            /*  左侧配置 */
            ,   west__slidable:             false
            ,   west__animatePaneSizing:    false
            ,   west__fxSpeed_size:         "slow"  /* 'fast' animation when resizing west-pane*/
            ,   west__fxSpeed_open:         1000    /* 1-second animation when opening west-pane*/
            ,   west__fxSettings_open:      { easing: "easeOutBounce" } // 'bounce' effect when opening*/
            ,   west__fxName_close:         "none"  /* NO animation when closing west-pane*/
            ,   stateManagement__enabled:   false   /*是否读取cookies*/
            ,   showDebugMessages:          false ,
            });
        } catch (e) {
            // 忽略 layout 初始化错误
        }
    }
});

var setting = {
    view:{
        dblClickExpand:false
        ,showLine:true
        // ,showIcon: false
    },
    data:{
        simpleData:{
            enable:true
        }
    },
    callback:{
        beforeExpand:beforeExpand
        ,onExpand:onExpand
        ,onClick:onClick
    }
};
var curExpandNode=null;
function beforeExpand(treeId,treeNode) {
    var pNode=curExpandNode?curExpandNode.getParentNode():null;
    var treeNodeP=treeNode.parentTId?treeNode.getParentNode():null;
    var zTree=$.fn.zTree.getZTreeObj("tree");
    for(var i=0,l=!treeNodeP?0:treeNodeP.children.length;i<l; i++){
        if(treeNode!==treeNodeP.children[i]){zTree.expandNode(treeNodeP.children[i],false);}
    };
    while (pNode){
        if(pNode===treeNode){break;}
        pNode=pNode.getParentNode();
    };
    if(!pNode){singlePath(treeNode);}
};
function singlePath(newNode) {
    if (newNode === curExpandNode) return;
    if (curExpandNode && curExpandNode.open==true) {
        var zTree = $.fn.zTree.getZTreeObj("tree");
        if (newNode.parentTId === curExpandNode.parentTId) {
            zTree.expandNode(curExpandNode, false);
        } else {
            var newParents = [];
            while (newNode) {
                newNode = newNode.getParentNode();
                if (newNode === curExpandNode) {
                    newParents = null;
                    break;
                } else if (newNode) {
                    newParents.push(newNode);
                }
            }
            if (newParents!=null) {
                var oldNode = curExpandNode;
                var oldParents = [];
                while (oldNode) {
                    oldNode = oldNode.getParentNode();
                    if (oldNode) {
                        oldParents.push(oldNode);
                    }
                }
                if (newParents.length>0) {
                    zTree.expandNode(oldParents[Math.abs(oldParents.length-newParents.length)-1], false);
                } else {
                    zTree.expandNode(oldParents[oldParents.length-1], false);
                }
            }
        }
    }
    curExpandNode = newNode;
};

function onExpand(event,treeId,treeNode){curExpandNode=treeNode;};

function onClick(e,treeId,treeNode){
    // var zTree=$.fn.zTree.getZTreeObj("tree");
    // zTree.expandNode(treeNode,null,null,null,true);
}

function get_treedir(selectType)
{
    $.ajax({
        type : 'get',
        url : eyou_basefile + "?m="+module_name+"&c=Uploadvideonew&a=ajax_get_treedir&lang=" + __lang__,
        data : {num:num, _ajax:1},
        dataType : 'json',
        beforeSend: function(xhr){
            $('#tree').html('<div style="text-align: center;"><img src="'+__root_dir__+'/public/static/common/images/loading.gif" style="width: 15px;"></div>');
        },
        success : function(res){
            $('#tree').html('');
            zNodes = JSON.parse(res.data.zNodes);
            $.fn.zTree.init($("#tree"),setting, zNodes);
            $(".ui-layout-north li:first-child").click();
            if (2 == selectType) {
                var src = '';
                var treeCurObj = $('#tree a.curSelectedNode');
                if (treeCurObj.length == 1) {
                    src = treeCurObj.attr('href');
                } else {
                    src = $('#tree_1_a').attr('href');
                }
                $('#content_body').attr('src', src);
            }
        },
        error: function(e){
            showErrorMsg(e.responseText);
        }
    });
}

$(function(){
    // get_treedir(0);

    // $.fn.zTree.init($("#tree"),setting,zNodes);
    // $(".ui-layout-north li:first-child").click();
});

/*----------------------------- 视频目录的树形结构 end ----------------------------------*/
var init_treedir = 0;
$(function(){
    // 左侧分组、视频目录选项卡切换
    $('#tab>li').click(function(){
        if (!$(this).hasClass('active')) {
            var value = $(this).data('value');
            $('#container>div').hide();
            $('#container>div#content'+value).show();
            $('#tab>li').removeClass('active');
            $(this).addClass('active');
            $.cookie("video_id_upload", ""); // 清除选中的视频
            $.cookie("videoname_id_upload", "");
            if (1 == value) {
                var type_id = $('#input_type_id').val();
                $('#typename_'+type_id).trigger("click");
            } else if (2 == value) {
                if (0 == init_treedir) {
                    get_treedir(2);
                    init_treedir = 1;
                } else {
                    var src = '';
                    var treeCurObj = $('#tree a.curSelectedNode');
                    if (treeCurObj.length == 1) {
                        src = treeCurObj.attr('href');
                    } else {
                        src = $('#tree_1_a').attr('href');
                    }
                    $('#content_body').attr('src', src);
                }
            }
        }
    });

    // 分组列表
    // $(".upload-group-con").scrollBar({
    //     barWidth: 4,
    //     position: "y",
    //     wheelDis: 15
    // });

    // 远程视频切换
    $("#tiqu").click(function() {
        $('#input_top_tab').val('tiqu');
    });

    // 切换本地视频时清空远程视频链接
    $("#bendi").click(function() {
        $('input[name=videoremoteurl]').val('');
        $('#input_top_tab').val('bendi');
    });

    // 远程视频链接
    $('input[name=videoremoteurl]').on('input', function(e) {
    }); 

    // 确定选中视频
    $(".layui-btn-yes").click(function() {
        var input_top_tab = $('#input_top_tab').val();
        var fileurl_tmp = [];
        var filename_tmp = [];
        var filesize_tmp = [];
        var videotime_tmp = [];
        if (callback != "undefined") {
            var arrvideo = new Array();
            var arrvideoname = new Array();
            var arrvideosize = new Array();
            var arrvideotime = new Array();
            var videoremoteurl = $.trim($('input[name=videoremoteurl]').val());
            var errmsg = '';
            if ('tiqu' == input_top_tab) { // 不提取视频，而直接点击确定，获取远程视频地址
                if (videoremoteurl == '') {
                    errmsg = '请输入视频地址！';
                } else {
                    arrvideo.push(videoremoteurl);
                    arrvideoname.push("");
                    arrvideosize.push("0"); // 远程视频无法获取大小，使用0
                    arrvideotime.push("0"); // 远程视频无法获取时长，使用0
                }
            }
            else {
                var video_id_upload = $.cookie("video_id_upload");
                if (undefined != video_id_upload && video_id_upload.length > 0) {
                    arrvideo = video_id_upload.split(",");
                    $.cookie("video_id_upload", "");
                }
                var videoname_id_upload = $.cookie("videoname_id_upload");
                if (undefined != videoname_id_upload && videoname_id_upload.length > 0) {
                    arrvideoname = videoname_id_upload.split(",");
                    $.cookie("videoname_id_upload", "");
                }
                var videosize_id_upload = $.cookie("videosize_id_upload");
                if (undefined != videosize_id_upload && videosize_id_upload.length > 0) {
                    arrvideosize = videosize_id_upload.split(",");
                    $.cookie("videosize_id_upload", "");
                }
                var videotime_id_upload = $.cookie("videotime_id_upload");
                if (undefined != videotime_id_upload && videotime_id_upload.length > 0) {
                    arrvideotime = videotime_id_upload.split(",");
                    $.cookie("videotime_id_upload", "");
                }
                // 只有在没有选择视频时才设置错误消息
                if (arrvideo.length == 0) {
                    errmsg = '请至少选择一个视频！';
                }
            }

            if (num > 1) {
                $.each(arrvideo, function(index, item) {
                    fileurl_tmp[index] = item;
                });
                $.each(arrvideoname, function(index, item) {
                    filename_tmp[index] = item;
                });
                $.each(arrvideosize, function(index, item) {
                    filesize_tmp[index] = item;
                });
                $.each(arrvideotime, function(index, item) {
                    videotime_tmp[index] = item;
                });
            } else {
                if ($.isArray(arrvideo)) {
                    fileurl_tmp = arrvideo[0];
                } else {
                    fileurl_tmp = arrvideo;
                }
                if ($.isArray(arrvideoname)) {
                    filename_tmp = arrvideoname[0];
                } else {
                    filename_tmp = arrvideoname;
                }
                if ($.isArray(arrvideosize)) {
                    filesize_tmp = arrvideosize[0];
                } else {
                    filesize_tmp = arrvideosize.length > 0 ? arrvideosize[0] : "0";
                }
                if ($.isArray(arrvideotime)) {
                    videotime_tmp = arrvideotime[0];
                } else {
                    videotime_tmp = arrvideotime.length > 0 ? arrvideotime[0] : "0";
                }
            }

            // 防止视频上传过程中用户点击确定，导致获取视频失败
            if (fileurl_tmp == undefined || fileurl_tmp.length == 0) {
                if ('tiqu' == input_top_tab) {
                    $('input[name=videoremoteurl]').focus();
                }
                layer.msg(errmsg, {icon: 5,time: 1500});
                return false;
            }
            // 优先直接在顶层或父级窗口查找回调函数，避免复杂的 iframe 嵌套导致取错窗口
            try {
                if (typeof window.top[callback] === 'function') {
                    window.top[callback](fileurl_tmp, filename_tmp, filesize_tmp, videotime_tmp);
                } else if (typeof window.parent[callback] === 'function') {
                    window.parent[callback](fileurl_tmp, filename_tmp, filesize_tmp, videotime_tmp);
                } else if (typeof window[callback] === 'function') {
                    // 兜底：当前窗口也有可能被注册了回调
                    window[callback](fileurl_tmp, filename_tmp, filesize_tmp, videotime_tmp);
                } else {
                    // 兼容旧版逻辑：按照原来的 iframe 结构一层层去找
                    var evaljs = '';
                    evaljs += 'if (undefined != window.top.$("iframe#workspace")[0]) {'; // 一级框架内，比如：栏目管理
                    evaljs += '  if (undefined != window.top.$("iframe#workspace")[0].contentWindow.$("iframe#content_body")[0]) {'; // 二级框架，比如：内容管理框架
                    evaljs += '     if (undefined != window.top.$("iframe#workspace")[0].contentWindow.$("iframe#content_body")[0].contentWindow.$("iframe[name^=layui-layer-iframe]")[0]) {'; // 内容管理的下一级框架
                    evaljs += '        console.log(1);window.top.$("iframe#workspace")[0].contentWindow.$("iframe#content_body")[0].contentWindow.$("iframe[name^=layui-layer-iframe]")[0].contentWindow.'+callback+'(fileurl_tmp,filename_tmp)';
                    evaljs += '     } else {';
                    evaljs += '        console.log(2);window.top.$("iframe#workspace")[0].contentWindow.$("iframe#content_body")[0].contentWindow.'+callback+'(fileurl_tmp,filename_tmp)';
                    evaljs += '     }';
                    evaljs += '  } else {'; // 二级的layer框架里，比如：广告管理的新增广告，是额外弹出一个 layer.open
                    evaljs += '     if (undefined != window.top.$("iframe#workspace")[0].contentWindow.$("iframe[name^=layui-layer-iframe]")[0]) {'; // 二级框架，新增广告
                    evaljs += '        console.log(3);window.top.$("iframe#workspace")[0].contentWindow.$("iframe[name^=layui-layer-iframe]")[0].contentWindow.'+callback+'(fileurl_tmp,filename_tmp)';
                    evaljs += '     } else {';
                    evaljs += '        console.log(4);window.top.$("iframe#workspace")[0].contentWindow.'+callback+'(fileurl_tmp,filename_tmp)';
                    evaljs += '     }';
                    evaljs += '  }';
                    evaljs += '} else {';
                    evaljs += '  console.log(5);window.parent.'+callback+'(fileurl_tmp,filename_tmp)';
                    evaljs += '}';
                    eval(evaljs);
                }
            } catch(e) {
                console.error('执行视频回调失败:', e);
                layer.msg('视频回调执行失败，请刷新页面后重试！', {icon: 5,time: 1500});
                return false;
            }
            window.parent.layer.closeAll();
            // if ('uploadImgProimgCallBack' !== String(callback)) {
            //     window.parent.layer.closeAll();
            // }
            // eval('window.parent.'+callback+'(fileurl_tmp,filename_tmp)');
            return;
        } else {
            showErrorMsg('视频地址不能为空！');
        }
        window.parent.layer.closeAll();
        // if ('uploadImgProimgCallBack' !== String(callback)) {
        //     window.parent.layer.closeAll();
        // }
    });

    // 关闭视频选择框
    $(".layui-btn-off").click(function(){
        $.cookie("video_id_upload", "");
        $.cookie("videoname_id_upload", "");
        $.cookie("videosize_id_upload", "");
        window.parent.layer.closeAll();
    });
});

// 远程视频本地化
function remote_to_videolocal() {
    var videoremoteurl = $.trim($('input[name=videoremoteurl]').val());
    if (videoremoteurl == '') {
        showErrorMsg('视频地址不能为空！');
        $('input[name=videoremoteurl]').focus();
        return false;
    } else {
        if (!checkURL(videoremoteurl)) {
            showErrorMsg('请输入有效的视频地址！');
            $('input[name=videoremoteurl]').focus();
            return false;
        }
    }

    var arrvideo = new Array();
    var arrvideoname =  new Array();
    var arrimgsize = new Array();
    if (num > 1) {
        var video_id_upload_tmp = $.cookie("video_id_upload");
        if (undefined != video_id_upload_tmp && video_id_upload_tmp.length > 0) {
            arrvideo = video_id_upload_tmp.split(",");
        } else {
            arrvideo = [];
        }
        var videoname_id_upload_tmp = $.cookie("videoname_id_upload");
        if (undefined != videoname_id_upload_tmp && videoname_id_upload_tmp.length > 0) {
            arrvideoname = videoname_id_upload_tmp.split(",");
        } else {
            arrvideoname = [];
        }
        var videosize_id_upload_tmp = $.cookie("videosize_id_upload");
        if (undefined != videosize_id_upload_tmp && videosize_id_upload_tmp.length > 0) {
            arrimgsize = videosize_id_upload_tmp.split(",");
        } else {
            arrimgsize = [];
        }
    }

    var type_id = $('#input_type_id').val();
    layer_loading('提取中');
    $.ajax({
        type: 'POST',
        url : eyou_basefile + "?m="+module_name+"&c=Uploadvideonew&a=ajax_remote_to_videolocal&lang=" + __lang__,
        data: {videoremoteurl: videoremoteurl, type_id: type_id, _ajax: 1},
        dataType: "JSON",
        success: function(res) {
            layer.closeAll();
            if (res.code == 1) {
                arrvideo.push(res.data.image_url);
                $.cookie("video_id_upload", arrvideo.join());
                arrvideoname.push(res.data.title);
                $.cookie("videoname_id_upload", arrvideoname.join());
                var filesize = res.data.filesize || 0;
                arrimgsize.push(filesize.toString());
                $.cookie("videosize_id_upload", arrimgsize.join());

                layer.msg(res.msg, {icon: 6, time: 1000}, function() {
                    $('#bendi').trigger("click");
                    $('#li_tag_item_group').trigger("click");
                    $('#typename_'+type_id).trigger("click");
                });
            } else {
                showErrorMsg(res.msg);
            }
        },
        error: function(e) {
            layer.closeAll();
            showErrorAlert(e.responseText);
        }
    });
}

//同步旧数据
function syn_old_imgdata(is_estop)
{
    if (is_estop) {
        event.stopPropagation();    //  阻止事件冒泡
    }
    var index = layer.open({
        type: 2,
        title: '开始同步',
        area: ['500px', '300px'],
        fix: false,
        maxmin: false,
        content: eyou_basefile + "?m="+module_name+"&c=Uploadimgnew&a=site&lang=" + __lang__,
        end: function(){
            window.location.reload();
        }
    });
}

/**
 * 切换分组
 * @param  {[type]} obj [description]
 * @return {[type]}     [description]
 */
function openIframes(obj)
{
    $('#input_type_id').val($(obj).data('type_id'));
    $('#content1>.upload-group-con .group-item').removeClass('active');
    $(obj).parent().parent().addClass('active');
    var src = $(obj).data('src');
    $('#content_body').attr('src', src);
}

// 新增分组
function addcate(obj) {
    layer.prompt({
        title: '添加分组'
    }, function(val, index) {
        layer_loading('正在处理');
        $.ajax({
            type: 'post',
            url : eyou_basefile + "?m="+module_name+"&c=Uploadvideonew&a=Addtype&lang=" + __lang__,
            data: {upload_type: val, _ajax: 1},
            dataType: 'json',
            success: function (res) {
                layer.closeAll();
                if (res.code == 1) {
                    layer.msg(res.msg, {icon: 6, time: 1000}, function() {
                        window.location.reload();
                    });
                } else {
                    layer.msg(res.msg, {icon: 5});
                }
            },
            error : function(e) {
                layer.closeAll();
                showErrorAlert(e.responseText);
            }
        });
        layer.close(index);
    });
}

// 编辑分组
function editcate(obj, type_id) {
    layer.prompt({
        title: '编辑分组',
        value: $('#typename_' + type_id).html(),
    }, function(val, index) {
        layer_loading('正在处理');
        $.ajax({
            type: 'post',
            url : eyou_basefile + "?m="+module_name+"&c=Uploadvideonew&a=EditType&lang=" + __lang__,
            data: {upload_type: val, type_id: type_id, _ajax: 1},
            dataType: 'json',
            success: function (res) {
                layer.closeAll();
                if (res.code == 1) {
                    $('#typename_'+type_id).html(val);
                    layer.msg(res.msg, {icon: 6 ,time: 1000});
                } else {
                    showErrorMsg(res.msg);
                }
            },
            error : function(e) {
                layer.closeAll();
                showErrorAlert(e.responseText);
            }
        });
        layer.close(index);
    });
}

// 删除分组
function delcate(obj, type_id) {
    layer.confirm('此操作不可恢复，确定删除？', {
        title: false,
        btn: ['确定', '取消']
    }, function() {
        layer_loading('正在处理');
        $.ajax({
            type: 'post',
            url : eyou_basefile + "?m="+module_name+"&c=Uploadvideonew&a=DelType&lang=" + __lang__,
            data: {type_id: type_id, _ajax: 1},
            dataType: 'json',
            success: function (res) {
                layer.closeAll();
                if (res.code == 1) {
                    $('#typename_'+type_id).parent().parent().remove();
                    layer.msg(res.msg, {icon: 6, time: 1000}, function(){
                        $('#typename_0').trigger("click");
                    });
                } else {
                    showErrorMsg(res.msg);
                }
            },
            error : function(e) {
                layer.closeAll();
                showErrorAlert(e.responseText);
            }
        });
    });
}

// 加载框
function layer_loading(msg) {
    var loading = layer.msg(
    msg+'...&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;请勿刷新页面', 
    {
        icon: 6,
        time: 3600000, //1小时后后自动关闭
        shade: [0.2] //0.1透明度的白色背景
    });
    //loading层
    var index = layer.load(3, {
        shade: [0.1,'#fff'] //0.1透明度的白色背景
    });
    return loading;
}

function showErrorMsg(msg){
    layer.msg(msg, {icon: 5,time: 2000});
}

function showErrorAlert(msg, icon){
    if (!icon && icon != 0) {
        icon = 5;
    }
    layer.alert(msg, {icon: icon, title: false, closeBtn: false});
}

/**
 * 判断URL是否合法http(s)
 * @param  {[type]} URL [description]
 * @return {[type]}     [description]
 */
function checkURL(URL) {
    var str = URL,
        Expression = /http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/,
        objExp = new RegExp(Expression);
    if(objExp.test(str) == true) {
        return true
    } else {
        return false
    }
}