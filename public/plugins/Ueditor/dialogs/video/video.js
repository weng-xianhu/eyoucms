/**
 * Created by JetBrains PhpStorm.
 * User: taoqili
 * Date: 12-2-20
 * Time: 上午11:19
 * To change this template use File | Settings | File Templates.
 */

(function(){

    var video = {},
        uploadVideoList = [],
        isModifyUploadVideo = false,
        uploadFile;

    var useNewVideoLibrary = detectVideoLibraryContext();

    window.onload = function(){
        $focus($G("videoUrl"));
        initTabs();
        initVideo();
        // 在初始化上传之前，先拦截文件选择按钮
        interceptFilePicker();
        initUpload();
    };
    
    // 拦截文件选择按钮，在WebUploader创建之前就绑定事件
    function interceptFilePicker() {
        // 这个函数现在不需要了，因为我们在initUploader中处理
        // 保留空函数避免错误
    }

    function detectVideoLibraryContext() {
        try {
            var topWin = window.top || window.parent || window;
            var href = '';
            try {
                if (topWin && topWin.location) {
                    href = topWin.location.href || '';
                } else if (window.location) {
                    href = window.location.href || '';
                }
            } catch (e) {
                href = (window.location && window.location.href) || '';
            }
            if (href) {
                var lowerHref = href.toLowerCase();
                if (lowerHref.indexOf('/user/') !== -1 || lowerHref.indexOf('?m=user') !== -1 || lowerHref.indexOf('/index.php?m=user') !== -1) {
                    return false;
                }
            }
            var moduleName = '';
            try {
                if (topWin && topWin.module_name) {
                    moduleName = String(topWin.module_name).toLowerCase();
                } else if (window.module_name) {
                    moduleName = String(window.module_name).toLowerCase();
                }
            } catch (err) {}
            if (moduleName === 'user') {
                return false;
            }
            if (topWin && topWin.APP_MODULE && String(topWin.APP_MODULE).toLowerCase() === 'user') {
                return false;
            }
        } catch (err2) {}
        return true;
    }
    
    // 处理从视频列表选择的视频（全局函数，供回调使用）
    function handleVideoSelection(fileurl_tmp, filename_tmp, filesize_tmp) {
        var videoUrl = '';
        var videoName = '';
        var videoSize = 0;
        
        // 处理单个或多个视频URL
        if (typeof fileurl_tmp === 'string') {
            videoUrl = fileurl_tmp;
        } else if (Array.isArray(fileurl_tmp) && fileurl_tmp.length > 0) {
            videoUrl = fileurl_tmp[0];
        }
        
        if (typeof filename_tmp === 'string') {
            videoName = filename_tmp;
        } else if (Array.isArray(filename_tmp) && filename_tmp.length > 0) {
            videoName = filename_tmp[0];
        }
        
        // 处理文件大小
        if (typeof filesize_tmp === 'string') {
            videoSize = parseInt(filesize_tmp, 10) || 0;
        } else if (Array.isArray(filesize_tmp) && filesize_tmp.length > 0) {
            videoSize = parseInt(filesize_tmp[0], 10) || 0;
        } else if (typeof filesize_tmp === 'number') {
            videoSize = filesize_tmp;
        }
        
        if (!videoUrl) {
            return;
        }
        
        // 关闭视频列表弹窗
        try {
            var layerObj = window.parent.layer || window.top.layer;
            if (layerObj && window._ueditorVideoLayerIndex !== undefined) {
                layerObj.close(window._ueditorVideoLayerIndex);
            }
        } catch (e) {
            console.error('关闭弹窗失败:', e);
        }
        
        // 获取视频文件扩展名
        var urlParts = videoUrl.split('.');
        var ext = urlParts.length > 1 ? urlParts[urlParts.length - 1].toLowerCase().split('?')[0] : 'mp4';
        
        // 将视频添加到上传列表
        // 创建一个虚拟的文件对象，模拟上传成功
        var fakeFile = {
            id: 'video_' + new Date().getTime(),
            name: videoName || '视频文件',
            url: videoUrl,
            size: videoSize, // 使用从服务器获取的真实文件大小
            ext: ext,
            getStatus: function() { return 'complete'; }
        };
        
        // 添加到上传视频列表
        uploadVideoList.push({
            'url': videoUrl,
            'type': ext,
            'original': videoName || '视频文件'
        });
        
        // 创建文件视图（延迟执行，确保addFileFromUrl已定义）
        setTimeout(function() {
            if (typeof window.addFileFromUrl === 'function') {
                window.addFileFromUrl(fakeFile, videoUrl);
            } else {
                // 如果addFileFromUrl还未定义，尝试直接操作DOM
                var $queue = $('#queueList .filelist');
                var $filePickerBlock = $queue.find('#filePickerBlock');
                if ($queue.length > 0) {
                    var $li = $('<li id="' + fakeFile.id + '">' +
                            '<p class="title">' + fakeFile.name + '</p>' +
                            '<p class="imgWrap"></p>' +
                            '<p class="progress"><span></span></p>' +
                            '</li>');
                    var $wrap = $li.find('p.imgWrap');
                    $wrap.empty().addClass('notimage').append('<i class="file-preview file-type-' + fakeFile.ext.toLowerCase() + '"></i>' +
                        '<span class="file-title">' + fakeFile.name + '</span>');
                    $li.find('p.progress span').css('width', '100%');
                    $li.addClass('state-complete');
                    $li.append('<span class="success"></span>');
                    if ($filePickerBlock.length > 0) {
                        $li.insertBefore($filePickerBlock);
                    } else {
                        $queue.append($li);
                    }
                }
            }
        }, 100);
    }
    
    // 打开视频列表对话框
    function openVideoListDialog() {
        if (!useNewVideoLibrary) {
            return false;
        }
        // 获取视频列表URL，优先从父页面获取，否则构建默认URL
        var videoListUrl = '';
        try {
            // 尝试从父页面获取视频列表URL
            if (window.parent && window.parent.uploadVideoSysLayerUrl) {
                videoListUrl = window.parent.uploadVideoSysLayerUrl;
            } else if (window.top && window.top.uploadVideoSysLayerUrl) {
                videoListUrl = window.top.uploadVideoSysLayerUrl;
            } else {
                // 构建默认的视频列表URL（统一走后台入口文件，避免某些入口 302 重定向）
                var langParam = '';
                try {
                    // 从上层窗口取语言参数
                    if (window.top && window.top.__lang__) {
                        langParam = window.top.__lang__;
                    } else if (window.parent && window.parent.__lang__) {
                        langParam = window.parent.__lang__;
                    } else if (window.opener && window.opener.__lang__) {
                        langParam = window.opener.__lang__;
                    }
                } catch (err) {}

                // 优先使用系统定义的后台入口 eyou_basefile（如 login.php）
                if (window.top && window.top.eyou_basefile) {
                    videoListUrl = window.top.eyou_basefile
                        + "?m=admin&c=Uploadvideonew&a=upload&func=ueditorVideoCallback&path=media&open_source=bendi&num=1";
                } else {
                    // 退化方案：根据当前路径大致推算后台入口
                    var locationRef;
                    try {
                        locationRef = (window.top && window.top.location) ? window.top.location : window.location;
                    } catch (err2) {
                        locationRef = window.location;
                    }
                    var baseUrl = locationRef.origin + locationRef.pathname.replace(/\/[^\/]*$/, '');
                    baseUrl = baseUrl.replace(/\/public\/plugins\/Ueditor\/dialogs\/video$/i, '');
                    // 大多数环境下后台入口是 login.php 或 index.php，这里默认用 login.php，减少 302 概率
                    videoListUrl = baseUrl + '/login.php?m=admin&c=Uploadvideonew&a=upload&func=ueditorVideoCallback&path=media&open_source=bendi&num=1';
                }

                // 追加语言参数（从父级/顶级窗口取），避免没有 lang 导致后台重定向
                if (langParam && videoListUrl.indexOf('lang=') === -1) {
                    videoListUrl += '&lang=' + encodeURIComponent(langParam);
                }
            }
        } catch (e) {
            console.error('获取视频列表URL失败:', e);
        }
        
        if (!videoListUrl) {
            alert('无法获取视频列表地址，请刷新页面后重试');
            return false;
        }
        
        // 打开视频列表弹窗
        var layerObj = window.parent.layer || window.top.layer || (window.parent && window.parent.parent && window.parent.parent.layer);
        if (!layerObj) {
            // 尝试从不同层级获取layer
            try {
                if (window.top && window.top.layer) {
                    layerObj = window.top.layer;
                } else if (window.parent && window.parent.layer) {
                    layerObj = window.parent.layer;
                }
            } catch (e) {
                console.error('获取layer对象失败:', e);
            }
        }
        
        if (layerObj) {
            // 创建全局回调函数，供视频列表页面调用
            // 将回调函数注册到多个可能的window对象，以便视频列表页面能够调用
            var callbackName = 'ueditorVideoCallback';
            var parentWindow = window.parent;
            var topWindow = window.top;
            
            // 定义回调函数
            var callbackFunc = function(fileurl_tmp, filename_tmp, filesize_tmp) {
                // 处理从视频列表选择的视频
                handleVideoSelection(fileurl_tmp, filename_tmp, filesize_tmp);
            };
            
            // 在多个window对象上注册回调函数，确保视频列表页面能够找到
            try {
                if (parentWindow) {
                    parentWindow[callbackName] = callbackFunc;
                }
                if (topWindow && topWindow !== parentWindow) {
                    topWindow[callbackName] = callbackFunc;
                }
                // 也在当前窗口注册，作为备用
                window[callbackName] = callbackFunc;
            } catch (e) {
                console.error('注册回调函数失败:', e);
            }
            
            // 在URL中添加回调函数名
            if (videoListUrl.indexOf('func=') > -1) {
                videoListUrl = videoListUrl.replace(/func=[^&]*/i, 'func=' + callbackName);
            } else {
                var separator = videoListUrl.indexOf('?') > -1 ? '&' : '?';
                videoListUrl += separator + 'func=' + callbackName;
            }
            
            layerObj.open({
                type: 2,
                title: '选择视频',
                area: ['1000px', '625px'],
                content: videoListUrl,
                success: function(layero, index) {
                    // 存储layer索引，用于关闭弹窗
                    window._ueditorVideoLayerIndex = index;
                },
                end: function() {
                    // 清理回调函数
                    try {
                        if (parentWindow && parentWindow[callbackName]) {
                            delete parentWindow[callbackName];
                        }
                        if (topWindow && topWindow[callbackName] && topWindow !== parentWindow) {
                            delete topWindow[callbackName];
                        }
                        if (window[callbackName]) {
                            delete window[callbackName];
                        }
                        delete window._ueditorVideoLayerIndex;
                    } catch (e) {
                        console.error('清理回调函数失败:', e);
                    }
                }
            });
        } else {
            alert('无法打开视频列表，请确保页面已加载layer组件');
        }
    }

    /* 初始化tab标签 */
    function initTabs(){
        var tabs = $G('tabHeads').children;
        for (var i = 0; i < tabs.length; i++) {
            domUtils.on(tabs[i], "click", function (e) {
                var j, bodyId, target = e.target || e.srcElement;
                for (j = 0; j < tabs.length; j++) {
                    bodyId = tabs[j].getAttribute('data-content-id');
                    if(tabs[j] == target){
                        domUtils.addClass(tabs[j], 'focus');
                        domUtils.addClass($G(bodyId), 'focus');
                    }else {
                        domUtils.removeClasses(tabs[j], 'focus');
                        domUtils.removeClasses($G(bodyId), 'focus');
                    }
                }
            });
        }
    }

    function initVideo(){
        createAlignButton( ["videoFloat", "upload_alignment"] );
        addUrlChangeListener($G("videoUrl"));
        addOkListener();

        //编辑视频时初始化相关信息
        (function(){
            var img = editor.selection.getRange().getClosedNode(),url;
            if(img && img.className){
                var hasFakedClass = (img.className == "edui-faked-video"),
                    hasUploadClass = img.className.indexOf("edui-upload-video")!=-1;
                if(hasFakedClass || hasUploadClass) {
                    $G("videoUrl").value = url = img.getAttribute("_url");
                    $G("videoWidth").value = img.width;
                    $G("videoHeight").value = img.height;
                    var align = domUtils.getComputedStyle(img,"float"),
                        parentAlign = domUtils.getComputedStyle(img.parentNode,"text-align");
                    updateAlignButton(parentAlign==="center"?"center":align);
                }
                if(hasUploadClass) {
                    isModifyUploadVideo = true;
                }
            }
            createPreviewVideo(url);
        })();
    }

    /**
     * 监听确认和取消两个按钮事件，用户执行插入或者清空正在播放的视频实例操作
     */
    function addOkListener(){
        dialog.onok = function(){
            $G("preview").innerHTML = "";
            var currentTab =  findFocus("tabHeads","tabSrc");
            switch(currentTab){
                case "video":
                    return insertSingle();
                    break;
                case "videoSearch":
                    return insertSearch("searchList");
                    break;
                case "upload":
                    return insertUpload();
                    break;
            }
        };
        dialog.oncancel = function(){
            $G("preview").innerHTML = "";
        };
    }

    /**
     * 依据传入的align值更新按钮信息
     * @param align
     */
    function updateAlignButton( align ) {
        var aligns = $G( "videoFloat" ).children;
        for ( var i = 0, ci; ci = aligns[i++]; ) {
            if ( ci.getAttribute( "name" ) == align ) {
                if ( ci.className !="focus" ) {
                    ci.className = "focus";
                }
            } else {
                if ( ci.className =="focus" ) {
                    ci.className = "";
                }
            }
        }
    }

    /**
     * 将单个视频信息插入编辑器中
     */
    function insertSingle(){
        var width = $G("videoWidth"),
            height = $G("videoHeight"),
            url=$G('videoUrl').value,
            align = findFocus("videoFloat","name");
        if(!url) return false;
        if ( !checkNum( [width, height] ) ) return false;
        editor.execCommand('insertvideo', {
            url: convert_url(url),
            width: width.value,
            height: height.value,
            align: align
        }, isModifyUploadVideo ? 'upload':null);
    }

    /**
     * 将元素id下的所有代表视频的图片插入编辑器中
     * @param id
     */
    function insertSearch(id){
        var imgs = domUtils.getElementsByTagName($G(id),"img"),
            videoObjs=[];
        for(var i=0,img; img=imgs[i++];){
            if(img.getAttribute("selected")){
                videoObjs.push({
                    url:img.getAttribute("ue_video_url"),
                    width:420,
                    height:280,
                    align:"none"
                });
            }
        }
        editor.execCommand('insertvideo',videoObjs);
    }

    /**
     * 找到id下具有focus类的节点并返回该节点下的某个属性
     * @param id
     * @param returnProperty
     */
    function findFocus( id, returnProperty ) {
        var tabs = $G( id ).children,
                property;
        for ( var i = 0, ci; ci = tabs[i++]; ) {
            if ( ci.className=="focus" ) {
                property = ci.getAttribute( returnProperty );
                break;
            }
        }
        return property;
    }
    function convert_url(url){
        if ( !url ) return '';
        url = utils.trim(url)
            .replace(/v\.youku\.com\/v_show\/id_([\w\-=]+)\.html/i, 'player.youku.com/player.php/sid/$1/v.swf')
            .replace(/(www\.)?youtube\.com\/watch\?v=([\w\-]+)/i, "www.youtube.com/v/$2")
            .replace(/youtu.be\/(\w+)$/i, "www.youtube.com/v/$1")
            .replace(/v\.ku6\.com\/.+\/([\w\.]+)\.html.*$/i, "player.ku6.com/refer/$1/v.swf")
            .replace(/www\.56\.com\/u\d+\/v_([\w\-]+)\.html/i, "player.56.com/v_$1.swf")
            .replace(/www.56.com\/w\d+\/play_album\-aid\-\d+_vid\-([^.]+)\.html/i, "player.56.com/v_$1.swf")
            .replace(/v\.pps\.tv\/play_([\w]+)\.html.*$/i, "player.pps.tv/player/sid/$1/v.swf")
            .replace(/www\.letv\.com\/ptv\/vplay\/([\d]+)\.html.*$/i, "i7.imgs.letv.com/player/swfPlayer.swf?id=$1&autoplay=0")
            .replace(/www\.tudou\.com\/programs\/view\/([\w\-]+)\/?/i, "www.tudou.com/v/$1")
            .replace(/v\.qq\.com\/cover\/[\w]+\/[\w]+\/([\w]+)\.html/i, "static.video.qq.com/TPout.swf?vid=$1")
            .replace(/v\.qq\.com\/.+[\?\&]vid=([^&]+).*$/i, "static.video.qq.com/TPout.swf?vid=$1")
            .replace(/my\.tv\.sohu\.com\/[\w]+\/[\d]+\/([\d]+)\.shtml.*$/i, "share.vrs.sohu.com/my/v.swf&id=$1");

        return url;
    }

    /**
      * 检测传入的所有input框中输入的长宽是否是正数
      * @param nodes input框集合，
      */
     function checkNum( nodes ) {
         for ( var i = 0, ci; ci = nodes[i++]; ) {
             var value = ci.value;
             if ( !isNumber( value ) && value) {
                 alert( lang.numError );
                 ci.value = "";
                 ci.focus();
                 return false;
             }
         }
         return true;
     }

    /**
     * 数字判断
     * @param value
     */
    function isNumber( value ) {
        return /(0|^[1-9]\d*$)/.test( value );
    }

    /**
      * 创建图片浮动选择按钮
      * @param ids
      */
     function createAlignButton( ids ) {
         for ( var i = 0, ci; ci = ids[i++]; ) {
             var floatContainer = $G( ci ),
                     nameMaps = {"none":lang['default'], "left":lang.floatLeft, "right":lang.floatRight, "center":lang.block};
             for ( var j in nameMaps ) {
                 var div = document.createElement( "div" );
                 div.setAttribute( "name", j );
                 if ( j == "none" ) div.className="focus";
                 div.style.cssText = "background:url(images/" + j + "_focus.jpg);";
                 div.setAttribute( "title", nameMaps[j] );
                 floatContainer.appendChild( div );
             }
             switchSelect( ci );
         }
     }

    /**
     * 选择切换
     * @param selectParentId
     */
    function switchSelect( selectParentId ) {
        var selects = $G( selectParentId ).children;
        for ( var i = 0, ci; ci = selects[i++]; ) {
            domUtils.on( ci, "click", function () {
                for ( var j = 0, cj; cj = selects[j++]; ) {
                    cj.className = "";
                    cj.removeAttribute && cj.removeAttribute( "class" );
                }
                this.className = "focus";
            } )
        }
    }

    /**
     * 监听url改变事件
     * @param url
     */
    function addUrlChangeListener(url){
        if (browser.ie) {
            url.onpropertychange = function () {
                createPreviewVideo( this.value );
            }
        } else {
            url.addEventListener( "input", function () {
                createPreviewVideo( this.value );
            }, false );
        }
    }

    /**
     * 根据url生成视频预览
     * @param url
     */
    function createPreviewVideo(url){
        if ( !url )return;

        var conUrl = convert_url(url);

        conUrl = utils.unhtmlForUrl(conUrl);

        var times = Math.floor(Math.random()*10000000);

        // 对flash和其他格式处理 by 小虎哥
        if(conUrl.indexOf(".swf") >= 0 ) {
            $G("preview").innerHTML = '<div class="previewMsg" id="previewMsg_'+times+'" style="display:none;z-index:1;"><span>'+lang.urlError+'</span></div>'+
            '<embed class="previewVideo" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"' +
                ' src="' + conUrl + '"' +
                ' width="' + 420  + '"' +
                ' height="' + 280  + '"' +
                ' onerror="$G(\'previewMsg_'+times+'\').style.display=\'\';"' +
                ' wmode="transparent" play="true" loop="false" menu="false" allowscriptaccess="never" allowfullscreen="true" >' +
            '</embed>';
        } else {
            $G("preview").innerHTML = '<div class="previewMsg" id="previewMsg_'+times+'" style="display:none;z-index:1;"><span>'+lang.urlError+'</span></div>'+
            '<video controls="controls" autoplay="autoplay" '+
                ' width="' + 420  + '"' +
                ' height="' + 280  + '"' +
                ' style="max-width:100%;">' +
                ' <source src="' + conUrl + '"' + ' type="video/mp4" onerror="$G(\'previewMsg_'+times+'\').style.display=\'\';" />' +
            '</video>';
        }
    }


    /* 插入上传视频 */
    function insertUpload(){
        var videoObjs=[],
            uploadDir = editor.getOpt('videoUrlPrefix'),
            width = parseInt($G('upload_width').value, 10) || 420,
            height = parseInt($G('upload_height').value, 10) || 280,
            align = findFocus("upload_alignment","name") || 'none';
        for(var key in uploadVideoList) {
            var file = uploadVideoList[key];
            videoObjs.push({
                url: uploadDir + file.url,
                width:width,
                height:height,
                align:align
            });
        }

        var count = uploadFile.getQueueCount();
        if (count) {
            $('.info', '#queueList').html('<span style="color:red;">' + '还有2个未上传文件'.replace(/[\d]/, count) + '</span>');
            return false;
        } else {
            editor.execCommand('insertvideo', videoObjs, 'upload');
        }
    }

    /*初始化上传标签*/
    function initUpload(){
        uploadFile = new UploadFile('queueList');
    }


    /* 上传附件 */
    function UploadFile(target) {
        this.$wrap = target.constructor == String ? $('#' + target) : $(target);
        this.init();
    }
    UploadFile.prototype = {
        init: function () {
            this.fileList = [];
            this.initContainer();
            this.initUploader();
        },
        initContainer: function () {
            this.$queue = this.$wrap.find('.filelist');
        },
        /* 初始化容器 */
        initUploader: function () {
            var _this = this,
                $ = jQuery,    // just in case. Make sure it's not an other libaray.
                $wrap = _this.$wrap,
            // 图片容器
                $queue = $wrap.find('.filelist'),
            // 状态栏，包括进度和控制按钮
                $statusBar = $wrap.find('.statusBar'),
            // 文件总体选择信息。
                $info = $statusBar.find('.info'),
            // 上传按钮
                $upload = $wrap.find('.uploadBtn'),
            // 上传按钮
                $filePickerBtn = $wrap.find('.filePickerBtn'),
            // 上传按钮
                $filePickerBlock = $wrap.find('.filePickerBlock'),
            // 没选择文件之前的内容。
                $placeHolder = $wrap.find('.placeholder'),
            // 总体进度条
                $progress = $statusBar.find('.progress').hide(),
            // 添加的文件数量
                fileCount = 0,
            // 添加的文件总大小
                fileSize = 0,
            // 优化retina, 在retina下这个值是2
                ratio = window.devicePixelRatio || 1,
            // 缩略图大小
                thumbnailWidth = 113 * ratio,
                thumbnailHeight = 113 * ratio,
            // 可能有pedding, ready, uploading, confirm, done.
                state = '',
            // 所有文件的进度信息，key为file id
                percentages = {},
                supportTransition = (function () {
                    var s = document.createElement('p').style,
                        r = 'transition' in s ||
                            'WebkitTransition' in s ||
                            'MozTransition' in s ||
                            'msTransition' in s ||
                            'OTransition' in s;
                    s = null;
                    return r;
                })(),
            // WebUploader实例
                uploader,
                actionUrl = editor.getActionUrl(editor.getOpt('videoActionName')),
                fileMaxSize = editor.getOpt('videoMaxSize'),
                acceptExtensions = (editor.getOpt('videoAllowFiles') || []).join('').replace(/\./g, ',').replace(/^[,]/, '');;

            if (!WebUploader.Uploader.support()) {
                $('#filePickerReady').after($('<div>').html(lang.errorNotSupport)).hide();
                return;
            } else if (!editor.getOpt('videoActionName')) {
                $('#filePickerReady').after($('<div>').html(lang.errorLoadConfig)).hide();
                return;
            }

            if (useNewVideoLibrary) {
                // 不再创建隐藏元素，直接使用#filePickerReady，只拦截点击事件
                
                // 拦截文件选择按钮的点击事件，改为打开视频列表
                // 在WebUploader创建之后，拦截其创建的按钮点击事件
                // 使用MutationObserver监听DOM变化，当WebUploader创建按钮后立即拦截
                var observer = new MutationObserver(function(mutations) {
                    var $filePickerReady = $('#filePickerReady');
                    if ($filePickerReady.length > 0) {
                        // 查找WebUploader创建的按钮（通常是内部的input或button）
                        var $pickerBtn = $filePickerReady.find('input[type="file"], .webuploader-pick, .webuploader-element-invisible');
                        if ($pickerBtn.length > 0) {
                            // 拦截所有可能的点击事件
                            $pickerBtn.off('click').on('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                e.stopImmediatePropagation();
                                openVideoListDialog();
                                return false;
                            });
                            
                            // 也拦截父元素的点击
                            $filePickerReady.off('click').on('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                e.stopImmediatePropagation();
                                openVideoListDialog();
                                return false;
                            });
                            
                            // 使用原生事件在capture阶段拦截
                            $pickerBtn.each(function() {
                                this.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    e.stopImmediatePropagation();
                                    openVideoListDialog();
                                    return false;
                                }, true);
                            });
                            
                            observer.disconnect(); // 找到按钮后停止观察
                        }
                    }
                });
                
                // 开始观察#filePickerReady元素的变化
                var $filePickerReady = $('#filePickerReady');
                if ($filePickerReady.length > 0) {
                    observer.observe($filePickerReady[0], {
                        childList: true,
                        subtree: true,
                        attributes: false
                    });
                    
                    // 也直接拦截#filePickerReady的点击（作为备用）
                    $filePickerReady.off('click').on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        openVideoListDialog();
                        return false;
                    });
                    
                    // 使用原生事件在capture阶段拦截
                    $filePickerReady[0].addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        openVideoListDialog();
                        return false;
                    }, true);
                }
                
                // 延迟检查，确保WebUploader已经创建了按钮
                setTimeout(function() {
                    var $filePickerReady = $('#filePickerReady');
                    if ($filePickerReady.length > 0) {
                        var $pickerBtn = $filePickerReady.find('input[type="file"], .webuploader-pick, .webuploader-element-invisible, button, a');
                        $pickerBtn.each(function() {
                            $(this).off('click').on('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                e.stopImmediatePropagation();
                                openVideoListDialog();
                                return false;
                            });
                            this.addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                e.stopImmediatePropagation();
                                openVideoListDialog();
                                return false;
                            }, true);
                        });
                    }
                    observer.disconnect();
                }, 500);
            }
            
            // 从URL添加文件视图（暴露到全局作用域，供handleVideoSelection调用）
            // 注意：这个函数在initUploader作用域内，可以直接访问fileCount、fileSize等变量
            if (useNewVideoLibrary) {
                window.addFileFromUrl = function(file, videoUrl) {
                    var $li = $('<li id="' + file.id + '">' +
                            '<p class="title">' + file.name + '</p>' +
                            '<p class="imgWrap"></p>' +
                            '<p class="progress"><span></span></p>' +
                            '</li>'),
                        $btns = $('<div class="file-panel">' +
                            '<span class="cancel">' + lang.uploadDelete + '</span></div>').appendTo($li),
                        $prgress = $li.find('p.progress span'),
                        $wrap = $li.find('p.imgWrap'),
                        $info = $('<p class="error"></p>').hide().appendTo($li);
                    
                    // 显示视频预览
                    $wrap.empty().addClass('notimage').append('<i class="file-preview file-type-' + file.ext.toLowerCase() + '"></i>' +
                        '<span class="file-title">' + file.name + '</span>');
                    
                    $prgress.css('width', '100%');
                    $li.addClass('state-complete');
                    $li.append('<span class="success"></span>');
                    
                    // 更新文件计数和大小（模拟文件已添加）
                    fileCount++;
                    // 如果文件有size属性，使用它；否则使用默认值
                    // 注意：file.size可能是0，需要检查是否大于0
                    var fileSizeValue = 0;
                    if (file.size && file.size > 0) {
                        fileSizeValue = parseInt(file.size, 10);
                    } else {
                        // 使用默认值，但每个文件使用不同的默认值以避免重复
                        // 这里先使用一个合理的默认值，实际应该从服务器获取
                        fileSizeValue = 5 * 1024 * 1024; // 默认5MB，更合理的视频文件大小
                    }
                    // 累加文件大小（直接访问作用域内的fileSize变量）
                    // 确保fileSize是数字类型并正确累加
                    if (typeof fileSize !== 'number' || isNaN(fileSize)) {
                        fileSize = 0;
                    }
                    fileSize = fileSize + fileSizeValue;
                    
                    // 更新状态显示
                    if (fileCount === 1) {
                        $placeHolder.addClass('element-invisible');
                        $statusBar.removeClass('element-invisible');
                        $queue.removeClass('element-invisible');
                    }
                    
                    // 更新进度和状态
                    percentages[file.id] = [fileSizeValue, 1]; // 标记为已完成
                    
                    // 先更新状态，再更新总进度
                    updateStatus();
                    updateTotalProgress();
                    
                    // 删除按钮事件
                    $btns.on('click', 'span.cancel', function() {
                        var index = uploadVideoList.findIndex(function(item) {
                            return item.url === videoUrl;
                        });
                        if (index > -1) {
                            uploadVideoList.splice(index, 1);
                        }
                        // 更新文件计数和大小
                        fileCount--;
                        fileSize -= fileSizeValue;
                        delete percentages[file.id];
                        $li.remove();
                        updateTotalProgress();
                        updateStatus();
                        // 如果没有文件了，恢复初始状态
                        if (fileCount === 0) {
                            $placeHolder.removeClass('element-invisible');
                            $queue.addClass('element-invisible');
                            $statusBar.addClass('element-invisible');
                            setState('pedding');
                        }
                    });
                    
                    $li.on('mouseenter', function () {
                        $btns.stop().animate({height: 30});
                    });
                    $li.on('mouseleave', function () {
                        $btns.stop().animate({height: 0});
                    });
                    
                    $li.insertBefore($filePickerBlock);
                    setState('ready');
                    
                    // 确保继续添加按钮显示（延迟执行，确保DOM已更新）
                    setTimeout(function() {
                        var $filePickerBtn = $('#filePickerBtn');
                        if ($filePickerBtn.length > 0) {
                            // 如果按钮是空的，创建按钮
                            if ($filePickerBtn.children().length === 0) {
                                var continueAddBtn = $('<div class="webuploader-pick" style="display: inline-block; padding: 0 18px; border: 1px solid #cfcfcf; background: #ffffff; color: #565656; border-radius: 3px; margin-left: 10px; cursor: pointer; font-size: 14px; line-height: 30px; text-align: center;">继续添加</div>');
                                $filePickerBtn.append(continueAddBtn);
                                
                                // 绑定点击事件
                                continueAddBtn.on('click', function(e) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    e.stopImmediatePropagation();
                                    openVideoListDialog();
                                    return false;
                                });
                            }
                            $filePickerBtn.show().removeClass('element-invisible').css({
                                'display': 'block',
                                'visibility': 'visible'
                            });
                        }
                        
                        // 隐藏开始上传按钮
                        var $uploadBtn = $('.uploadBtn');
                        if ($uploadBtn.length > 0) {
                            $uploadBtn.hide().css({
                                'display': 'none',
                                'visibility': 'hidden'
                            });
                        }
                    }, 100);
                };
            } else {
                window.addFileFromUrl = function() {};
            }

            uploader = _this.uploader = WebUploader.create({
                pick: {
                    id: '#filePickerReady',
                    label: lang.uploadSelectFile
                },
                swf: '../../third-party/webuploader/Uploader.swf',
                server: actionUrl,
                fileVal: editor.getOpt('videoFieldName'),
                duplicate: true,
                fileSingleSizeLimit: fileMaxSize,
                compress: false
            });
            if (!useNewVideoLibrary) {
                uploader.addButton({
                    id: '#filePickerBlock'
                });
                uploader.addButton({
                    id: '#filePickerBtn',
                    label: lang.uploadAddFile
                });
            }
            // 不直接绑定WebUploader到filePickerBlock和filePickerBtn，而是拦截它们的点击事件
            
            // 手动创建"继续添加"按钮，因为WebUploader不再创建它
            var $filePickerBtn = $('#filePickerBtn');
            if (useNewVideoLibrary && $filePickerBtn.length > 0 && $filePickerBtn.children().length === 0) {
                // 如果filePickerBtn是空的，手动创建按钮
                var continueAddBtn = $('<div class="webuploader-pick" style="display: inline-block; padding: 0 18px; border: 1px solid #cfcfcf; background: #ffffff; color: #565656; border-radius: 3px; margin-left: 10px; cursor: pointer; font-size: 14px; line-height: 30px; text-align: center;">' + (lang.uploadAddFile || '继续添加') + '</div>');
                $filePickerBtn.append(continueAddBtn);
                
                // 绑定点击事件
                continueAddBtn.on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    openVideoListDialog();
                    return false;
                });
                continueAddBtn[0].addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    openVideoListDialog();
                    return false;
                }, true);
            }
            
            // 拦截filePickerBlock的点击事件
            var $filePickerBlock = $('#filePickerBlock');
            if (useNewVideoLibrary && $filePickerBlock.length > 0) {
                // 使用MutationObserver监听DOM变化，当WebUploader创建按钮后立即拦截
                var observerBlock = new MutationObserver(function(mutations) {
                    var $blockBtn = $filePickerBlock.find('input[type="file"], .webuploader-pick, .webuploader-element-invisible, button, a, div');
                    if ($blockBtn.length > 0) {
                        $blockBtn.each(function() {
                            $(this).off('click').on('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                e.stopImmediatePropagation();
                                openVideoListDialog();
                                return false;
                            });
                            this.addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                e.stopImmediatePropagation();
                                openVideoListDialog();
                                return false;
                            }, true);
                        });
                        observerBlock.disconnect();
                    }
                });
                observerBlock.observe($filePickerBlock[0], {
                    childList: true,
                    subtree: true,
                    attributes: false
                });
                
                // 直接拦截filePickerBlock的点击
                $filePickerBlock.off('click').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    openVideoListDialog();
                    return false;
                });
                $filePickerBlock[0].addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    openVideoListDialog();
                    return false;
                }, true);
            }
            
            // 拦截filePickerBtn（继续添加按钮）的点击事件
            var $filePickerBtn = $('#filePickerBtn');
            if (useNewVideoLibrary && $filePickerBtn.length > 0) {
                // 使用MutationObserver监听DOM变化，当WebUploader创建按钮后立即拦截
                var observerBtn = new MutationObserver(function(mutations) {
                    var $btn = $filePickerBtn.find('input[type="file"], .webuploader-pick, .webuploader-element-invisible, button, a, div');
                    if ($btn.length > 0) {
                        $btn.each(function() {
                            $(this).off('click').on('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                e.stopImmediatePropagation();
                                openVideoListDialog();
                                return false;
                            });
                            this.addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                e.stopImmediatePropagation();
                                openVideoListDialog();
                                return false;
                            }, true);
                        });
                        observerBtn.disconnect();
                    }
                });
                observerBtn.observe($filePickerBtn[0], {
                    childList: true,
                    subtree: true,
                    attributes: false
                });
                
                // 直接拦截filePickerBtn的点击
                $filePickerBtn.off('click').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    openVideoListDialog();
                    return false;
                });
                $filePickerBtn[0].addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    openVideoListDialog();
                    return false;
                }, true);
            }
            
            // 延迟检查，确保WebUploader已经创建了按钮
            if (useNewVideoLibrary) {
                setTimeout(function() {
                    // 拦截filePickerBlock内的所有按钮
                    var $blockBtn = $filePickerBlock.find('input[type="file"], .webuploader-pick, .webuploader-element-invisible, button, a, div');
                    $blockBtn.each(function() {
                        $(this).off('click').on('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                            openVideoListDialog();
                            return false;
                        });
                        this.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                            openVideoListDialog();
                            return false;
                        }, true);
                    });
                    
                    // 拦截filePickerBtn内的所有按钮
                    var $btn = $filePickerBtn.find('input[type="file"], .webuploader-pick, .webuploader-element-invisible, button, a, div');
                    $btn.each(function() {
                        $(this).off('click').on('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                            openVideoListDialog();
                            return false;
                        });
                        this.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            e.stopImmediatePropagation();
                            openVideoListDialog();
                            return false;
                        }, true);
                    });
                    
                    observerBlock.disconnect();
                    observerBtn.disconnect();
                }, 500);
            }

            setState('pedding');
            
            // 初始化时隐藏开始上传按钮，显示继续添加按钮
            if (useNewVideoLibrary) {
                setTimeout(function() {
                    $upload.hide().css({
                        'display': 'none',
                        'visibility': 'hidden'
                    });
                    
                    // 确保继续添加按钮存在并显示
                    var $filePickerBtn = $('#filePickerBtn');
                    if ($filePickerBtn.length > 0) {
                        // 如果按钮是空的，创建按钮
                        if ($filePickerBtn.children().length === 0) {
                            var continueAddBtn = $('<div class="webuploader-pick" style="display: inline-block; padding: 0 18px; border: 1px solid #cfcfcf; background: #ffffff; color: #565656; border-radius: 3px; margin-left: 10px; cursor: pointer; font-size: 14px; line-height: 30px; text-align: center;">继续添加</div>');
                            $filePickerBtn.append(continueAddBtn);
                            
                            // 绑定点击事件
                            continueAddBtn.on('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                e.stopImmediatePropagation();
                                openVideoListDialog();
                                return false;
                            });
                            continueAddBtn[0].addEventListener('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                e.stopImmediatePropagation();
                                openVideoListDialog();
                                return false;
                            }, true);
                        }
                        $filePickerBtn.show().removeClass('element-invisible').css({
                            'display': 'block',
                            'visibility': 'visible'
                        });
                    }
                }, 100);
            } else {
                $upload.show().css({
                    'display': '',
                    'visibility': ''
                });
            }

            // 当有文件添加进来时执行，负责view的创建
            function addFile(file) {
                var $li = $('<li id="' + file.id + '">' +
                        '<p class="title">' + file.name + '</p>' +
                        '<p class="imgWrap"></p>' +
                        '<p class="progress"><span></span></p>' +
                        '</li>'),

                    $btns = $('<div class="file-panel">' +
                        '<span class="cancel">' + lang.uploadDelete + '</span>' +
                        '<span class="rotateRight">' + lang.uploadTurnRight + '</span>' +
                        '<span class="rotateLeft">' + lang.uploadTurnLeft + '</span></div>').appendTo($li),
                    $prgress = $li.find('p.progress span'),
                    $wrap = $li.find('p.imgWrap'),
                    $info = $('<p class="error"></p>').hide().appendTo($li),

                    showError = function (code) {
                        switch (code) {
                            case 'exceed_size':
                                text = lang.errorExceedSize;
                                break;
                            case 'interrupt':
                                text = lang.errorInterrupt;
                                break;
                            case 'http':
                                text = lang.errorHttp;
                                break;
                            case 'not_allow_type':
                                text = lang.errorFileType;
                                break;
                            default:
                                text = lang.errorUploadRetry;
                                break;
                        }
                        setTimeout(function(){
                            $info.text(text).show();
                        },1);
                    };

                if (file.getStatus() === 'invalid') {
                    showError(file.statusText);
                } else {
                    $wrap.text(lang.uploadPreview);
                    if ('|png|jpg|jpeg|bmp|gif|'.indexOf('|'+file.ext.toLowerCase()+'|') == -1) {
                        $wrap.empty().addClass('notimage').append('<i class="file-preview file-type-' + file.ext.toLowerCase() + '"></i>' +
                            '<span class="file-title">' + file.name + '</span>');
                    } else {
                        if (browser.ie && browser.version <= 7) {
                            $wrap.text(lang.uploadNoPreview);
                        } else {
                            uploader.makeThumb(file, function (error, src) {
                                if (error || !src || (/^data:/.test(src) && browser.ie && browser.version <= 7)) {
                                    $wrap.text(lang.uploadNoPreview);
                                } else {
                                    var $img = $('<img src="' + src + '">');
                                    $wrap.empty().append($img);
                                    $img.on('error', function () {
                                        $wrap.text(lang.uploadNoPreview);
                                    });
                                }
                            }, thumbnailWidth, thumbnailHeight);
                        }
                    }
                    percentages[ file.id ] = [ file.size, 0 ];
                    file.rotation = 0;

                    /* 检查文件格式 */
                    if (!file.ext || acceptExtensions.indexOf(file.ext.toLowerCase()) == -1) {
                        showError('not_allow_type');
                        uploader.removeFile(file);
                    }
                }

                file.on('statuschange', function (cur, prev) {
                    if (prev === 'progress') {
                        $prgress.hide().width(0);
                    } else if (prev === 'queued') {
                        $li.off('mouseenter mouseleave');
                        $btns.remove();
                    }
                    // 成功
                    if (cur === 'error' || cur === 'invalid') {
                        showError(file.statusText);
                        percentages[ file.id ][ 1 ] = 1;
                    } else if (cur === 'interrupt') {
                        showError('interrupt');
                    } else if (cur === 'queued') {
                        percentages[ file.id ][ 1 ] = 0;
                    } else if (cur === 'progress') {
                        $info.hide();
                        $prgress.css('display', 'block');
                    } else if (cur === 'complete') {
                    }

                    $li.removeClass('state-' + prev).addClass('state-' + cur);
                });

                $li.on('mouseenter', function () {
                    $btns.stop().animate({height: 30});
                });
                $li.on('mouseleave', function () {
                    $btns.stop().animate({height: 0});
                });

                $btns.on('click', 'span', function () {
                    var index = $(this).index(),
                        deg;

                    switch (index) {
                        case 0:
                            uploader.removeFile(file);
                            return;
                        case 1:
                            file.rotation += 90;
                            break;
                        case 2:
                            file.rotation -= 90;
                            break;
                    }

                    if (supportTransition) {
                        deg = 'rotate(' + file.rotation + 'deg)';
                        $wrap.css({
                            '-webkit-transform': deg,
                            '-mos-transform': deg,
                            '-o-transform': deg,
                            'transform': deg
                        });
                    } else {
                        $wrap.css('filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation=' + (~~((file.rotation / 90) % 4 + 4) % 4) + ')');
                    }

                });

                $li.insertBefore($filePickerBlock);
            }

            // 负责view的销毁
            function removeFile(file) {
                var $li = $('#' + file.id);
                delete percentages[ file.id ];
                updateTotalProgress();
                $li.off().find('.file-panel').off().end().remove();
            }

            function updateTotalProgress() {
                var loaded = 0,
                    total = 0,
                    spans = $progress.children(),
                    percent;

                $.each(percentages, function (k, v) {
                    total += v[ 0 ];
                    loaded += v[ 0 ] * v[ 1 ];
                });

                percent = total ? loaded / total : 0;

                spans.eq(0).text(Math.round(percent * 100) + '%');
                spans.eq(1).css('width', Math.round(percent * 100) + '%');
                updateStatus();
            }

            function setState(val, files) {

                if (val != state) {

                    var stats = uploader.getStats();

                    $upload.removeClass('state-' + state);
                    $upload.addClass('state-' + val);

                    switch (val) {

                        /* 未选择文件 */
                        case 'pedding':
                            $queue.addClass('element-invisible');
                            $statusBar.addClass('element-invisible');
                            $placeHolder.removeClass('element-invisible');
                            $progress.hide(); $info.hide();
                            uploader.refresh();
                            if (useNewVideoLibrary) {
                                // 确保继续添加按钮显示
                                var $filePickerBtn = $('#filePickerBtn');
                                if ($filePickerBtn.length > 0) {
                                    // 如果按钮是空的，创建按钮
                                    if ($filePickerBtn.children().length === 0) {
                                        var continueAddBtn = $('<div class="webuploader-pick" style="display: inline-block; padding: 0 18px; border: 1px solid #cfcfcf; background: #ffffff; color: #565656; border-radius: 3px; margin-left: 10px; cursor: pointer; font-size: 14px; line-height: 30px; text-align: center;">继续添加</div>');
                                        $filePickerBtn.append(continueAddBtn);
                                        
                                        // 绑定点击事件
                                        continueAddBtn.on('click', function(e) {
                                            e.preventDefault();
                                            e.stopPropagation();
                                            e.stopImmediatePropagation();
                                            openVideoListDialog();
                                            return false;
                                        });
                                    }
                                    $filePickerBtn.show().removeClass('element-invisible').css({
                                        'display': 'block',
                                        'visibility': 'visible'
                                    });
                                }
                                // 隐藏开始上传按钮
                                $upload.hide().css({
                                    'display': 'none',
                                    'visibility': 'hidden'
                                });
                            } else {
                                $upload.show().css({
                                    'display': '',
                                    'visibility': ''
                                });
                                $('#filePickerBtn').show().removeClass('element-invisible');
                            }
                            break;

                        /* 可以开始上传 */
                        case 'ready':
                            $placeHolder.addClass('element-invisible');
                            $queue.removeClass('element-invisible');
                            $statusBar.removeClass('element-invisible');
                            $progress.hide(); $info.show();
                            $upload.text(lang.uploadStart);
                            uploader.refresh();
                            if (useNewVideoLibrary) {
                                // 确保继续添加按钮显示
                                var $filePickerBtn = $('#filePickerBtn');
                                if ($filePickerBtn.length > 0) {
                                    // 如果按钮是空的，创建按钮
                                    if ($filePickerBtn.children().length === 0) {
                                        var continueAddBtn = $('<div class="webuploader-pick" style="display: inline-block; padding: 0 18px; border: 1px solid #cfcfcf; background: #ffffff; color: #565656; border-radius: 3px; margin-left: 10px; cursor: pointer; font-size: 14px; line-height: 30px; text-align: center;">继续添加</div>');
                                        $filePickerBtn.append(continueAddBtn);
                                        
                                        // 绑定点击事件
                                        continueAddBtn.on('click', function(e) {
                                            e.preventDefault();
                                            e.stopPropagation();
                                            e.stopImmediatePropagation();
                                            openVideoListDialog();
                                            return false;
                                        });
                                    }
                                    $filePickerBtn.show().removeClass('element-invisible').css({
                                        'display': 'block',
                                        'visibility': 'visible'
                                    });
                                }
                                // 隐藏开始上传按钮
                                $upload.hide().css({
                                    'display': 'none',
                                    'visibility': 'hidden'
                                });
                            } else {
                                $upload.show().css({
                                    'display': '',
                                    'visibility': ''
                                });
                                $('#filePickerBtn').show().removeClass('element-invisible');
                            }
                            break;

                        /* 上传中 */
                        case 'uploading':
                            $progress.show(); $info.hide();
                            $upload.text(lang.uploadPause);
                            if (useNewVideoLibrary) {
                                // 隐藏开始上传按钮，显示继续添加按钮
                                $upload.hide();
                                $('#filePickerBtn').show().removeClass('element-invisible').css('display', 'block');
                            } else {
                                $upload.show().css({'display':'', 'visibility':''});
                            }
                            break;

                        /* 暂停上传 */
                        case 'paused':
                            $progress.show(); $info.hide();
                            $upload.text(lang.uploadContinue);
                            if (useNewVideoLibrary) {
                                // 隐藏开始上传按钮，显示继续添加按钮
                                $upload.hide();
                                $('#filePickerBtn').show().removeClass('element-invisible').css('display', 'block');
                            } else {
                                $upload.show().css({'display':'', 'visibility':''});
                            }
                            break;

                        case 'confirm':
                            $progress.show(); $info.hide();
                            $upload.text(lang.uploadStart);
                            if (useNewVideoLibrary) {
                                // 隐藏开始上传按钮，显示继续添加按钮
                                $upload.hide();
                                $('#filePickerBtn').show().removeClass('element-invisible');
                            } else {
                                $upload.show().css({'display':'', 'visibility':''});
                            }

                            stats = uploader.getStats();
                            if (stats.successNum && !stats.uploadFailNum) {
                                setState('finish');
                                return;
                            }
                            break;

                        case 'finish':
                            $progress.hide(); $info.show();
                            if (stats.uploadFailNum) {
                                $upload.text(lang.uploadRetry);
                            } else {
                                $upload.text(lang.uploadStart);
                            }
                            if (useNewVideoLibrary) {
                                // 隐藏开始上传按钮，显示继续添加按钮
                                $upload.hide();
                                $('#filePickerBtn').show().removeClass('element-invisible');
                            } else {
                                $upload.show().css({'display':'', 'visibility':''});
                            }
                            break;
                    }

                    state = val;
                    updateStatus();

                }

                if (!_this.getQueueCount()) {
                    $upload.addClass('disabled')
                } else {
                    $upload.removeClass('disabled')
                }

            }

            function updateStatus() {
                var text = '', stats;
                
                // 从percentages对象计算总大小，确保与updateTotalProgress一致
                var calculatedTotalSize = 0;
                $.each(percentages, function (k, v) {
                    calculatedTotalSize += v[0]; // v[0]是文件大小
                });
                
                // 同时更新fileSize变量，保持同步
                if (calculatedTotalSize > 0) {
                    fileSize = calculatedTotalSize;
                }
                
                // 确保fileSize是数字类型
                var currentFileSize = typeof fileSize === 'number' && fileSize > 0 ? fileSize : calculatedTotalSize;

                if (state === 'ready') {
                    text = lang.updateStatusReady.replace('_', fileCount).replace('_KB', WebUploader.formatSize(currentFileSize));
                } else if (state === 'confirm') {
                    stats = uploader.getStats();
                    if (stats.uploadFailNum) {
                        text = lang.updateStatusConfirm.replace('_', stats.successNum).replace('_', stats.successNum);
                    }
                } else {
                    stats = uploader.getStats();
                    text = lang.updateStatusFinish.replace('_', fileCount).
                        replace('_KB', WebUploader.formatSize(currentFileSize)).
                        replace('_', stats.successNum);

                    if (stats.uploadFailNum) {
                        text += lang.updateStatusError.replace('_', stats.uploadFailNum);
                    }
                }
                
                $info.html(text);
            }

            uploader.on('fileQueued', function (file) {
                fileCount++;
                fileSize += file.size;

                if (fileCount === 1) {
                    $placeHolder.addClass('element-invisible');
                    $statusBar.show();
                }

                addFile(file);
            });

            uploader.on('fileDequeued', function (file) {
                fileCount--;
                fileSize -= file.size;

                removeFile(file);
                updateTotalProgress();
            });

            uploader.on('filesQueued', function (file) {
                if (!uploader.isInProgress() && (state == 'pedding' || state == 'finish' || state == 'confirm' || state == 'ready')) {
                    setState('ready');
                }
                updateTotalProgress();
            });

            uploader.on('all', function (type, files) {
                switch (type) {
                    case 'uploadFinished':
                        setState('confirm', files);
                        break;
                    case 'startUpload':
                        /* 添加额外的GET参数 */
                        var params = utils.serializeParam(editor.queryCommandValue('serverparam')) || '',
                            url = utils.formatUrl(actionUrl + (actionUrl.indexOf('?') == -1 ? '?':'&') + 'encode=utf-8&' + params);
                        uploader.option('server', url);
                        setState('uploading', files);
                        break;
                    case 'stopUpload':
                        setState('paused', files);
                        break;
                }
            });

            uploader.on('uploadBeforeSend', function (file, data, header) {
                //这里可以通过data对象添加POST参数
                header['X_Requested_With'] = 'XMLHttpRequest';
            });

            uploader.on('uploadProgress', function (file, percentage) {
                var $li = $('#' + file.id),
                    $percent = $li.find('.progress span');

                $percent.css('width', percentage * 100 + '%');
                percentages[ file.id ][ 1 ] = percentage;
                updateTotalProgress();
            });

            uploader.on('uploadSuccess', function (file, ret) {
                var $file = $('#' + file.id);
                try {
                    var responseText = (ret._raw || ret),
                        json = utils.str2json(responseText);
                    if (json.state == 'SUCCESS') {
                        uploadVideoList.push({
                            'url': json.url,
                            'type': json.type,
                            'original':json.original
                        });
                        $file.append('<span class="success"></span>');
                    } else {
                        $file.find('.error').text(json.state).show();
                    }
                } catch (e) {
                    $file.find('.error').text(lang.errorServerUpload).show();
                }
            });

            uploader.on('uploadError', function (file, code) {
            });
            uploader.on('error', function (code, file) {
                if (code == 'Q_TYPE_DENIED' || code == 'F_EXCEED_SIZE') {
                    addFile(file);
                }
            });
            uploader.on('uploadComplete', function (file, ret) {
            });

            $upload.on('click', function () {
                if ($(this).hasClass('disabled')) {
                    return false;
                }

                if (state === 'ready') {
                    uploader.upload();
                } else if (state === 'paused') {
                    uploader.upload();
                } else if (state === 'uploading') {
                    uploader.stop();
                }
            });

            $upload.addClass('state-' + state);
            updateTotalProgress();
        },
        getQueueCount: function () {
            var file, i, status, readyFile = 0, files = this.uploader.getFiles();
            for (i = 0; file = files[i++]; ) {
                status = file.getStatus();
                if (status == 'queued' || status == 'uploading' || status == 'progress') readyFile++;
            }
            return readyFile;
        },
        refresh: function(){
            this.uploader.refresh();
        }
    };

})();
