/**
 * User: Jinqn
 * Date: 14-04-08
 * Time: 下午16:34
 * 上传图片对话框逻辑代码,包括tab: 远程图片/上传图片/在线图片/搜索图片
 */

(function () {

    var uploadFile,
        onlineFile;

    window.onload = function () {
        initTabs();
        initButtons();
    };

    /* 初始化tab标签 */
    function initTabs() {
        var tabs = $G('tabhead').children;
        for (var i = 0; i < tabs.length; i++) {
            domUtils.on(tabs[i], "click", function (e) {
                var target = e.target || e.srcElement;
                setTabFocus(target.getAttribute('data-content-id'));
            });
        }

        setTabFocus('upload');
    }

    /* 初始化tabbody */
    function setTabFocus(id) {
        if(!id) return;
        var i, bodyId, tabs = $G('tabhead').children;
        for (i = 0; i < tabs.length; i++) {
            bodyId = tabs[i].getAttribute('data-content-id')
            if (bodyId == id) {
                domUtils.addClass(tabs[i], 'focus');
                domUtils.addClass($G(bodyId), 'focus');
            } else {
                domUtils.removeClasses(tabs[i], 'focus');
                domUtils.removeClasses($G(bodyId), 'focus');
            }
        }
        switch (id) {
            case 'upload':
                uploadFile = uploadFile || new UploadFile('queueList');
                break;
            case 'online':
                onlineFile = onlineFile || new OnlineFile('fileList');
                break;
        }
    }

    /* 初始化onok事件 */
    function initButtons() {

        dialog.onok = function () {
            var list = [], id, tabs = $G('tabhead').children;
            for (var i = 0; i < tabs.length; i++) {
                if (domUtils.hasClass(tabs[i], 'focus')) {
                    id = tabs[i].getAttribute('data-content-id');
                    break;
                }
            }

            switch (id) {
                case 'upload':
                    list = uploadFile.getInsertList();
                    var count = uploadFile.getQueueCount();
                    if (count) {
                        $('.info', '#queueList').html('<span style="color:red;">' + '还有2个未上传文件'.replace(/[\d]/, count) + '</span>');
                        return false;
                    }
                    break;
                case 'online':
                    list = onlineFile.getInsertList();
                    break;
            }

            editor.execCommand('insertfile', list);
        };
    }

    /* 打开附件库弹窗 */
    function openFileLibraryDialog(uploadFileInstance) {
        // 获取附件库URL
        var fileLibraryUrl = '';
        try {
            // 尝试从父页面获取附件库URL
            if (window.parent && window.parent.uploadFileSysLayerUrl) {
                fileLibraryUrl = window.parent.uploadFileSysLayerUrl;
            } else if (window.top && window.top.uploadFileSysLayerUrl) {
                fileLibraryUrl = window.top.uploadFileSysLayerUrl;
            } else {
                // 构建默认的附件库URL
                var langParam = '';
                try {
                    if (window.top && window.top.__lang__) {
                        langParam = window.top.__lang__;
                    } else if (window.parent && window.parent.__lang__) {
                        langParam = window.parent.__lang__;
                    }
                } catch (err) {}

                if (window.top && window.top.eyou_basefile) {
                    fileLibraryUrl = window.top.eyou_basefile
                        + "?m=admin&c=Uploadfilenew&a=upload&func=ueditorFileCallback&path=soft&open_source=bendi&num=100";
                } else {
                    var locationRef;
                    try {
                        locationRef = (window.top && window.top.location) ? window.top.location : window.location;
                    } catch (err2) {
                        locationRef = window.location;
                    }
                    var baseUrl = locationRef.origin + locationRef.pathname.replace(/\/[^\/]*$/, '');
                    baseUrl = baseUrl.replace(/\/public\/plugins\/Ueditor\/dialogs\/attachment$/i, '');
                    fileLibraryUrl = baseUrl + '/login.php?m=admin&c=Uploadfilenew&a=upload&func=ueditorFileCallback&path=soft&open_source=bendi&num=100';
                }

                if (langParam && fileLibraryUrl.indexOf('lang=') === -1) {
                    fileLibraryUrl += '&lang=' + encodeURIComponent(langParam);
                }
            }
        } catch (e) {
            console.error('获取附件库URL失败:', e);
        }
        
        if (!fileLibraryUrl) {
            alert('无法获取附件库地址，请刷新页面后重试');
            return false;
        }
        
        // 获取layer对象
        var layerObj = window.parent.layer || window.top.layer || (window.parent && window.parent.parent && window.parent.parent.layer);
        if (!layerObj) {
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
            // 创建全局回调函数
            var callbackName = 'ueditorFileCallback';
            var parentWindow = window.parent;
            var topWindow = window.top;
            
            // 定义回调函数
            var callbackFunc = function(fileurl_tmp, filename_tmp, filesize_tmp, videotime_tmp) {
                // 处理从附件库选择的附件
                handleFileSelection(uploadFileInstance, fileurl_tmp, filename_tmp, filesize_tmp);
            };
            
            // 在多个window对象上注册回调函数
            try {
                if (parentWindow) {
                    parentWindow[callbackName] = callbackFunc;
                }
                if (topWindow && topWindow !== parentWindow) {
                    topWindow[callbackName] = callbackFunc;
                }
                window[callbackName] = callbackFunc;
            } catch (e) {
                console.error('注册回调函数失败:', e);
            }
            
            // 在URL中添加回调函数名
            if (fileLibraryUrl.indexOf('func=') > -1) {
                fileLibraryUrl = fileLibraryUrl.replace(/func=[^&]*/i, 'func=' + callbackName);
            } else {
                var separator = fileLibraryUrl.indexOf('?') > -1 ? '&' : '?';
                fileLibraryUrl += separator + 'func=' + callbackName;
            }
            
            // 防止重复打开同一附件库弹窗（多个事件或重叠元素可能导致多次触发）
            (function(){
                // 在将要打开 layer 的那个顶级 window 对象上设置保护标识
                var layerGlobalWindow = parentWindow || topWindow || window;
                try {
                    if (layerGlobalWindow._ueditorFileLayerOpening) {
                        // 已经在打开中，直接返回，避免重复弹窗
                        return false;
                    }
                    // 标记正在打开
                    layerGlobalWindow._ueditorFileLayerOpening = true;
                } catch (err) {
                    // 若无法访问（跨域等），忽略保护并继续尝试打开
                    console.warn('设置弹窗打开保护失败:', err);
                }

                layerObj.open({
                    type: 2,
                    title: '选择附件',
                    shadeClose: false,
                    shade: 0.3,
                    maxmin: false,
                    area: ['1000px', '625px'],
                    content: fileLibraryUrl,
                    success: function(layero, index) {
                        try {
                            // 保存索引并清除打开保护
                            layerGlobalWindow._ueditorFileLayerIndex = index;
                            layerGlobalWindow._ueditorFileLayerOpening = false;
                        } catch (err) {
                            // 忽略跨域或其他访问问题
                        }
                    },
                    end: function() {
                        // 清理回调函数，并移除保护标识
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
                            // 清理我们可能设置的全局标识
                            if (layerGlobalWindow && layerGlobalWindow._ueditorFileLayerIndex) {
                                delete layerGlobalWindow._ueditorFileLayerIndex;
                            }
                            if (layerGlobalWindow && layerGlobalWindow._ueditorFileLayerOpening) {
                                delete layerGlobalWindow._ueditorFileLayerOpening;
                            }
                        } catch (e) {
                            console.error('清理回调函数失败:', e);
                        }
                    }
                });
            })();
        } else {
            alert('无法打开附件库，请确保页面已加载layer组件');
        }
    }

    /* 处理从附件库选择的附件 */
    function handleFileSelection(uploadFileInstance, fileurl_tmp, filename_tmp, filesize_tmp) {
        if (!fileurl_tmp) {
            return;
        }
        
        // 处理单个或多个附件
        var fileUrls = Array.isArray(fileurl_tmp) ? fileurl_tmp : [fileurl_tmp];
        var fileNames = Array.isArray(filename_tmp) ? filename_tmp : (filename_tmp ? [filename_tmp] : []);
        
        var $ = jQuery;
        var prefix = editor.getOpt('fileUrlPrefix') || '';
        
        // 将选中的附件添加到文件列表
        for (var i = 0; i < fileUrls.length; i++) {
            var fileUrl = fileUrls[i];
            if (!fileUrl) continue;
            
            // 处理URL：如果已经是完整URL，直接使用；否则可能需要添加前缀
            var finalUrl = fileUrl;
            if (prefix && fileUrl.indexOf('http://') !== 0 && fileUrl.indexOf('https://') !== 0 && fileUrl.indexOf('//') !== 0) {
                // 如果URL不是以http开头，可能需要添加前缀
                if (fileUrl.indexOf(prefix) !== 0) {
                    finalUrl = prefix + (fileUrl.indexOf('/') === 0 ? fileUrl.substr(1) : fileUrl);
                }
            }
            
            // 获取文件名
            var fileName = '';
            if (fileNames[i]) {
                fileName = fileNames[i];
            } else {
                // 从URL中提取文件名
                var urlParts = finalUrl.split('/');
                fileName = urlParts[urlParts.length - 1] || '未知文件';
                // 去除可能的查询参数
                if (fileName.indexOf('?') > -1) {
                    fileName = fileName.split('?')[0];
                }
            }
            
            // 构建文件数据对象，模拟上传成功的格式
            var fileData = {
                state: 'SUCCESS',
                url: finalUrl.indexOf(prefix) === 0 ? finalUrl.substr(prefix.length) : finalUrl,
                original: fileName,
                title: fileName
            };
            
            // 添加到文件列表
            uploadFileInstance.fileList.push(fileData);
            
            // 创建文件项并添加到界面
            var fileExt = '';
            var lastDotIndex = fileName.lastIndexOf('.');
            if (lastDotIndex > -1) {
                fileExt = fileName.substr(lastDotIndex + 1).toLowerCase();
            }
            
            var $li = $('<li class="file-item">' +
                '<p class="title">' + fileName + '</p>' +
                '<p class="imgWrap"></p>' +
                '</li>');
            
            var $wrap = $li.find('p.imgWrap');
            if ('|png|jpg|jpeg|bmp|gif|'.indexOf('|'+fileExt+'|') == -1) {
                $wrap.empty().addClass('notimage').append(
                    '<i class="file-preview file-type-' + (fileExt || 'file') + '"></i>' +
                    '<span class="file-title" title="' + fileName + '">' + fileName + '</span>'
                );
            } else {
                // 如果是图片，尝试显示预览
                $wrap.text(lang.uploadPreview || '预览');
                try {
                    var $img = $('<img src="' + finalUrl + '">');
                    $wrap.empty().append($img);
                    $img.on('error', function () {
                        $wrap.text(lang.uploadNoPreview || '无法预览');
                    });
                } catch (e) {
                    $wrap.text(lang.uploadPreview || '预览');
                }
            }
            
            $li.append('<span class="success"></span>');
            $li.insertBefore($('#filePickerBlock'));
            
            // 更新状态
            var $queue = uploadFileInstance.$wrap.find('.filelist');
            var $statusBar = uploadFileInstance.$wrap.find('.statusBar');
            var $placeHolder = uploadFileInstance.$wrap.find('.placeholder');
            
            if (uploadFileInstance.fileList.length === 1) {
                $placeHolder.addClass('element-invisible');
                $statusBar.removeClass('element-invisible').show();
                $queue.removeClass('element-invisible');
            }
        }
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
                actionUrl = editor.getActionUrl(editor.getOpt('fileActionName')),
                fileMaxSize = editor.getOpt('fileMaxSize'),
                acceptExtensions = (editor.getOpt('fileAllowFiles') || []).join('').replace(/\./g, ',').replace(/^[,]/, '');;

            if (!WebUploader.Uploader.support()) {
                $('#filePickerReady').after($('<div>').html(lang.errorNotSupport)).hide();
                return;
            } else if (!editor.getOpt('fileActionName')) {
                $('#filePickerReady').after($('<div>').html(lang.errorLoadConfig)).hide();
                return;
            }

            uploader = _this.uploader = WebUploader.create({
                pick: {
                    id: '#filePickerReady',
                    label: lang.uploadSelectFile
                },
                swf: '../../third-party/webuploader/Uploader.swf',
                server: actionUrl,
                fileVal: editor.getOpt('fileFieldName'),
                duplicate: true,
                fileSingleSizeLimit: fileMaxSize,
                compress: false
            });
            uploader.addButton({
                id: '#filePickerBlock'
            });
            uploader.addButton({
                id: '#filePickerBtn',
                label: lang.uploadAddFile
            });

            // 判断是否是前端投稿页面（兼容伪静态路径和参数形式）
            var isUserReleasePage = false;
            try {
                function detectUserReleaseFromUrl(url) {
                    // if (!url) return false;
                    // // 常见伪静态路径： /user/UsersRelease/article_add.html 或 /user/UsersRelease/article_add
                    // var pathMatch = url.match(/\/user\/UsersRelease\/article_(add|edit)(?:\.html|\/?|$)/i);
                    // if (pathMatch) return true;
                    // // 参数形式： ?m=user&c=UsersRelease&a=article_add
                    // if (url.indexOf('m=user') > -1 && url.indexOf('UsersRelease') > -1) return true;
                    // return false;
                    
                    var eyou_top_basefile = window.top.eyou_basefile || '/index.php';
                    const reg = /\/(?!index)[^\/]+\.php(?:\?|#|$)/i;
                    if (eyou_top_basefile.indexOf('/index.php') === -1 || true === reg.test(url)) {
                        return false;
                    } else {
                        return true;
                    }
                }

                // 检查当前窗口 URL（包含伪静态）
                var currentUrl = (window.location && window.location.href) ? window.location.href : '';
                if (detectUserReleaseFromUrl(currentUrl)) {
                    isUserReleasePage = true;
                }

                // 检查当前窗口的 pathname（更精确匹配伪静态）
                if (!isUserReleasePage && window.location && window.location.pathname) {
                    var pathname = window.location.pathname || '';
                    if (pathname.match(/\/user\/UsersRelease\/article_(add|edit)(?:\.html|\/?$)/i)) {
                        isUserReleasePage = true;
                    }
                }

                // 检查父窗口 URL（UEditor 对话框通常在 iframe 中）
                if (!isUserReleasePage && window.parent && window.parent !== window) {
                    try {
                        var parentHref = window.parent.location && window.parent.location.href ? window.parent.location.href : '';
                        if (detectUserReleaseFromUrl(parentHref)) {
                            isUserReleasePage = true;
                        } else if (window.parent.location && window.parent.location.pathname) {
                            var ppath = window.parent.location.pathname || '';
                            if (ppath.match(/\/user\/UsersRelease\/article_(add|edit)(?:\.html|\/?$)/i)) {
                                isUserReleasePage = true;
                            }
                        }
                    } catch (e) {
                        // 跨域访问可能失败，忽略
                    }
                }

                // 检查顶级窗口 URL（多层 iframe 情况）
                if (!isUserReleasePage && window.top && window.top !== window) {
                    try {
                        var topHref = window.top.location && window.top.location.href ? window.top.location.href : '';
                        if (detectUserReleaseFromUrl(topHref)) {
                            isUserReleasePage = true;
                        } else if (window.top.location && window.top.location.pathname) {
                            var tpath = window.top.location.pathname || '';
                            if (tpath.match(/\/user\/UsersRelease\/article_(add|edit)(?:\.html|\/?$)/i)) {
                                isUserReleasePage = true;
                            }
                        }
                    } catch (e) {
                        // 跨域访问可能失败，忽略
                    }
                }
            } catch (e) {
                console.error('判断页面类型失败:', e);
            }
            
            // 只有非前端投稿页面才拦截文件选择按钮，打开附件库弹窗
            if (!isUserReleasePage) {
                // 拦截文件选择按钮的点击事件，打开附件库弹窗
                setTimeout(function() {
                    // 拦截 #filePickerReady 区域的点击
                    var $filePickerReady = $('#filePickerReady');
                    if ($filePickerReady.length) {
                        // 移除 WebUploader 的点击事件，使用我们自己的
                        $filePickerReady.off('click').on('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            openFileLibraryDialog(_this);
                            return false;
                        });
                    }
                    
                    // 拦截 #filePickerBtn 的点击
                    var $filePickerBtn = $('#filePickerBtn');
                    if ($filePickerBtn.length) {
                        $filePickerBtn.off('click').on('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            openFileLibraryDialog(_this);
                            return false;
                        });
                    }
                }, 100);
            }

            setState('pedding');

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
                        $info.text(text).show();
                    };

                if (file.getStatus() === 'invalid') {
                    showError(file.statusText);
                } else {
                    $wrap.text(lang.uploadPreview);
                    if ('|png|jpg|jpeg|bmp|gif|'.indexOf('|'+file.ext.toLowerCase()+'|') == -1) {
                        $wrap.empty().addClass('notimage').append('<i class="file-preview file-type-' + file.ext.toLowerCase() + '"></i>' +
                        '<span class="file-title" title="' + file.name + '">' + file.name + '</span>');
                    } else {
                        if (browser.ie && browser.version <= 7) {
                            $wrap.text(lang.uploadNoPreview);
                        } else {
                            uploader.makeThumb(file, function (error, src) {
                                if (error || !src) {
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
                            break;

                        /* 可以开始上传 */
                        case 'ready':
                            $placeHolder.addClass('element-invisible');
                            $queue.removeClass('element-invisible');
                            $statusBar.removeClass('element-invisible');
                            $progress.hide(); $info.show();
                            $upload.text(lang.uploadStart);
                            uploader.refresh();
                            break;

                        /* 上传中 */
                        case 'uploading':
                            $progress.show(); $info.hide();
                            $upload.text(lang.uploadPause);
                            break;

                        /* 暂停上传 */
                        case 'paused':
                            $progress.show(); $info.hide();
                            $upload.text(lang.uploadContinue);
                            break;

                        case 'confirm':
                            $progress.show(); $info.hide();
                            $upload.text(lang.uploadStart);

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

                if (state === 'ready') {
                    text = lang.updateStatusReady.replace('_', fileCount).replace('_KB', WebUploader.formatSize(fileSize));
                } else if (state === 'confirm') {
                    stats = uploader.getStats();
                    if (stats.uploadFailNum) {
                        text = lang.updateStatusConfirm.replace('_', stats.successNum).replace('_', stats.successNum);
                    }
                } else {
                    stats = uploader.getStats();
                    text = lang.updateStatusFinish.replace('_', fileCount).
                        replace('_KB', WebUploader.formatSize(fileSize)).
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
                        _this.fileList.push(json);
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
        getInsertList: function () {
            var i, link, data, list = [],
                prefix = editor.getOpt('fileUrlPrefix');
            for (i = 0; i < this.fileList.length; i++) {
                data = this.fileList[i];
                link = data.url;
                list.push({
                    title: data.original || link.substr(link.lastIndexOf('/') + 1),
                    url: prefix + link
                });
            }
            return list;
        }
    };


    /* 在线附件 */
    function OnlineFile(target) {
        this.container = utils.isString(target) ? document.getElementById(target) : target;
        this.init();
    }
    OnlineFile.prototype = {
        init: function () {
            this.initContainer();
            this.initEvents();
            this.initData();
        },
        /* 初始化容器 */
        initContainer: function () {
            this.container.innerHTML = '';
            this.list = document.createElement('ul');
            this.clearFloat = document.createElement('li');

            domUtils.addClass(this.list, 'list');
            domUtils.addClass(this.clearFloat, 'clearFloat');

            this.list.appendChild(this.clearFloat);
            this.container.appendChild(this.list);
        },
        /* 初始化滚动事件,滚动到地步自动拉取数据 */
        initEvents: function () {
            var _this = this;

            /* 滚动拉取图片 */
            domUtils.on($G('fileList'), 'scroll', function(e){
                var panel = this;
                if (panel.scrollHeight - (panel.offsetHeight + panel.scrollTop) < 10) {
                    _this.getFileData();
                }
            });
            /* 选中图片 */
            domUtils.on(this.list, 'click', function (e) {
                var target = e.target || e.srcElement,
                    li = target.parentNode;

                if (li.tagName.toLowerCase() == 'li') {
                    if (domUtils.hasClass(li, 'selected')) {
                        domUtils.removeClasses(li, 'selected');
                    } else {
                        domUtils.addClass(li, 'selected');
                    }
                }
            });
        },
        /* 初始化第一次的数据 */
        initData: function () {

            /* 拉取数据需要使用的值 */
            this.state = 0;
            this.listSize = editor.getOpt('fileManagerListSize');
            this.listIndex = 0;
            this.listEnd = false;

            /* 第一次拉取数据 */
            this.getFileData();
        },
        /* 向后台拉取图片列表数据 */
        getFileData: function () {
            var _this = this;

            if(!_this.listEnd && !this.isLoadingData) {
                this.isLoadingData = true;
                ajax.request(editor.getActionUrl(editor.getOpt('fileManagerActionName')), {
                    timeout: 100000,
                    data: utils.extend({
                            start: this.listIndex,
                            size: this.listSize
                        }, editor.queryCommandValue('serverparam')),
                    method: 'get',
                    onsuccess: function (r) {
                        try {
                            var json = eval('(' + r.responseText + ')');
                            if (json.state == 'SUCCESS') {
                                _this.pushData(json.list);
                                _this.listIndex = parseInt(json.start) + parseInt(json.list.length);
                                if(_this.listIndex >= json.total) {
                                    _this.listEnd = true;
                                }
                                _this.isLoadingData = false;
                            }
                        } catch (e) {
                            if(r.responseText.indexOf('ue_separate_ue') != -1) {
                                var list = r.responseText.split(r.responseText);
                                _this.pushData(list);
                                _this.listIndex = parseInt(list.length);
                                _this.listEnd = true;
                                _this.isLoadingData = false;
                            }
                        }
                    },
                    onerror: function () {
                        _this.isLoadingData = false;
                    }
                });
            }
        },
        /* 添加图片到列表界面上 */
        pushData: function (list) {
            var i, item, img, filetype, preview, icon, _this = this,
                urlPrefix = editor.getOpt('fileManagerUrlPrefix');
            for (i = 0; i < list.length; i++) {
                if(list[i] && list[i].url) {
                    item = document.createElement('li');
                    icon = document.createElement('span');
                    filetype = list[i].url.substr(list[i].url.lastIndexOf('.') + 1);

                    if ( "png|jpg|jpeg|gif|bmp".indexOf(filetype) != -1 ) {
                        preview = document.createElement('img');
                        domUtils.on(preview, 'load', (function(image){
                            return function(){
                                _this.scale(image, image.parentNode.offsetWidth, image.parentNode.offsetHeight);
                            };
                        })(preview));
                        preview.width = 113;
                        preview.setAttribute('src', urlPrefix + list[i].url + (list[i].url.indexOf('?') == -1 ? '?noCache=':'&noCache=') + (+new Date()).toString(36) );
                    } else {
                        var ic = document.createElement('i'),
                            textSpan = document.createElement('span');
                        textSpan.innerHTML = list[i].url.substr(list[i].url.lastIndexOf('/') + 1);
                        preview = document.createElement('div');
                        preview.appendChild(ic);
                        preview.appendChild(textSpan);
                        domUtils.addClass(preview, 'file-wrapper');
                        domUtils.addClass(textSpan, 'file-title');
                        domUtils.addClass(ic, 'file-type-' + filetype);
                        domUtils.addClass(ic, 'file-preview');
                    }
                    domUtils.addClass(icon, 'icon');
                    item.setAttribute('data-url', urlPrefix + list[i].url);
                    if (list[i].original) {
                        item.setAttribute('data-title', list[i].original);
                    }

                    item.appendChild(preview);
                    item.appendChild(icon);
                    this.list.insertBefore(item, this.clearFloat);
                }
            }
        },
        /* 改变图片大小 */
        scale: function (img, w, h, type) {
            var ow = img.width,
                oh = img.height;

            if (type == 'justify') {
                if (ow >= oh) {
                    img.width = w;
                    img.height = h * oh / ow;
                    img.style.marginLeft = '-' + parseInt((img.width - w) / 2) + 'px';
                } else {
                    img.width = w * ow / oh;
                    img.height = h;
                    img.style.marginTop = '-' + parseInt((img.height - h) / 2) + 'px';
                }
            } else {
                if (ow >= oh) {
                    img.width = w * ow / oh;
                    img.height = h;
                    img.style.marginLeft = '-' + parseInt((img.width - w) / 2) + 'px';
                } else {
                    img.width = w;
                    img.height = h * oh / ow;
                    img.style.marginTop = '-' + parseInt((img.height - h) / 2) + 'px';
                }
            }
        },
        getInsertList: function () {
            var i, lis = this.list.children, list = [];
            for (i = 0; i < lis.length; i++) {
                if (domUtils.hasClass(lis[i], 'selected')) {
                    var url = lis[i].getAttribute('data-url');
                    var title = lis[i].getAttribute('data-title') || url.substr(url.lastIndexOf('/') + 1);
                    list.push({
                        title: title,
                        url: url
                    });
                }
            }
            return list;
        }
    };


})();