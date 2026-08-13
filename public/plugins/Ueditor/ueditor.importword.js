/* UEditor 导入Word插件：基于 mammoth.js 在浏览器本地将 .docx 转换为 HTML，图片通过编辑器标准图片上传接口保存到服务器 */
(function () {
    if (!window.UE) return;

    var style = document.createElement("style");
    style.innerHTML =
        ".edui-for-importword .edui-icon{" +
        "background:url(\"data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='16'%20height='16'%3E%3Ctext%20x='8'%20y='12.5'%20font-family='Arial,Helvetica,sans-serif'%20font-size='12'%20font-weight='bold'%20fill='%23444444'%20text-anchor='middle'%3EW%3C/text%3E%3C/svg%3E\") no-repeat center !important;" +
        "background-size:16px 16px !important;}" +
        ".edui-for-importword .edui-icon:before{content:''!important;}";
    document.getElementsByTagName("head")[0].appendChild(style);

    function getHomeUrl() {
        return (window.UEDITOR_CONFIG && UEDITOR_CONFIG.UEDITOR_HOME_URL) || window.UEDITOR_HOME_URL || "";
    }

    function loadMammoth(cb, onerror) {
        if (window.mammoth) return cb();
        var s = document.createElement("script");
        s.src = getHomeUrl() + "third-party/mammoth/mammoth.browser.min.js";
        s.onload = function () { cb(); };
        s.onerror = function () { onerror("mammoth.js 加载失败，请检查 third-party/mammoth 目录"); };
        document.getElementsByTagName("head")[0].appendChild(s);
    }

    var loading = (function () {
        var mask, txt;
        function ensure() {
            if (mask) return;
            if (!document.getElementById("__iw_style")) {
                var st = document.createElement("style");
                st.id = "__iw_style";
                st.innerHTML = "@keyframes iwspin{to{transform:rotate(360deg);}}";
                document.getElementsByTagName("head")[0].appendChild(st);
            }
            mask = document.createElement("div");
            mask.style.cssText = "position:fixed;left:0;top:0;right:0;bottom:0;z-index:2147483647;background:rgba(0,0,0,0.45);";
            var box = document.createElement("div");
            box.style.cssText = "position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);min-width:180px;padding:22px 30px;background:#fff;border-radius:6px;box-shadow:0 2px 14px rgba(0,0,0,0.3);font-size:14px;color:#333;text-align:center;line-height:1.6;";
            var sp = document.createElement("div");
            sp.style.cssText = "width:26px;height:26px;margin:0 auto 12px;border:3px solid #e5e5e5;border-top-color:#1a8cff;border-radius:50%;animation:iwspin 0.8s linear infinite;";
            txt = document.createElement("div");
            box.appendChild(sp);
            box.appendChild(txt);
            mask.appendChild(box);
        }
        return {
            show: function (t) { ensure(); txt.innerHTML = t || "处理中..."; if (!mask.parentNode) document.body.appendChild(mask); },
            update: function (t) { if (mask && txt) txt.innerHTML = t; },
            hide: function () { if (mask && mask.parentNode) mask.parentNode.removeChild(mask); }
        };
    })();

    var MIME_EXT = {
        "image/png": "png", "image/jpeg": "jpg", "image/jpg": "jpg", "image/gif": "gif",
        "image/bmp": "bmp", "image/webp": "webp", "image/x-icon": "ico"
    };

    function dataURItoBlob(dataURI) {
        var comma = dataURI.indexOf(",");
        var meta = dataURI.substring(5, comma);
        var mime = (meta.split(";")[0] || "image/png").toLowerCase();
        var isBase64 = /;base64/i.test(meta);
        var body = dataURI.substring(comma + 1);
        var bytes;
        if (isBase64) {
            var bin = atob(body);
            bytes = new Uint8Array(bin.length);
            for (var i = 0; i < bin.length; i++) bytes[i] = bin.charCodeAt(i);
        } else {
            var txt = decodeURIComponent(body);
            bytes = new Uint8Array(txt.length);
            for (var j = 0; j < txt.length; j++) bytes[j] = txt.charCodeAt(j);
        }
        return { blob: new Blob([bytes], { type: mime }), mime: mime };
    }

    function uploadImage(editor, dataURI, cb) {
        var serverUrl = editor.getOpt("serverUrl");
        if (!serverUrl) return cb(null, "编辑器未配置上传地址(serverUrl)");
        var action = editor.getOpt("imageActionName") || "uploadimage";
        var fieldName = editor.getOpt("imageFieldName") || "upfile";
        var prefix = editor.getOpt("imageUrlPrefix") || "";
        var url = serverUrl + (serverUrl.indexOf("?") === -1 ? "?" : "&") + "action=" + encodeURIComponent(action);

        var parsed;
        try { parsed = dataURItoBlob(dataURI); } catch (e) { return cb(null, "图片解码失败"); }
        var ext = MIME_EXT[parsed.mime] || "png";
        var name = "word_" + (+new Date()) + "_" + Math.floor(Math.random() * 100000) + "." + ext;

        var fd = new FormData();
        fd.append(fieldName, parsed.blob, name);

        var xhr = new XMLHttpRequest();
        xhr.open("POST", url, true);
        xhr.withCredentials = true;
        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) return;
            var json = null;
            try { json = JSON.parse(xhr.responseText); } catch (e) {}
            if (xhr.status === 200 && json && json.state === "SUCCESS" && json.url) {
                cb(prefix + json.url);
            } else {
                cb(null, (json && json.state) ? json.state : ("HTTP " + xhr.status));
            }
        };
        xhr.onerror = function () { cb(null, "网络错误"); };
        fd && xhr.send(fd);
    }

    function insertHtmlWithImages(editor, html) {
        var div = document.createElement("div");
        div.innerHTML = html;
        var all = div.getElementsByTagName("img");
        var imgs = [];
        for (var i = 0; i < all.length; i++) {
            if ((all[i].getAttribute("src") || "").indexOf("data:") === 0) imgs.push(all[i]);
        }
        var total = imgs.length;
        var pending = total;
        var done = 0;
        var failed = 0;
        var lastErr = "";

        if (!pending) return finish();
        loading.update("正在上传图片 0/" + total + " ...");
        for (var j = 0; j < imgs.length; j++) {
            (function (img) {
                uploadImage(editor, img.getAttribute("src"), function (url, err) {
                    if (url) {
                        img.setAttribute("src", url);
                        img.removeAttribute("width");
                        img.removeAttribute("height");
                        img.removeAttribute("style");
                    } else {
                        failed++;
                        lastErr = err || lastErr;
                        if (img.parentNode) img.parentNode.removeChild(img);
                    }
                    done++;
                    loading.update("正在上传图片 " + done + "/" + total + " ...");
                    if (--pending === 0) finish();
                });
            })(imgs[j]);
        }

        function finish() {
            loading.hide();
            editor.execCommand("inserthtml", div.innerHTML);
            editor.fireEvent("contentchange");
            if (failed > 0) {
                alert("Word 导入完成，但有 " + failed + "/" + total + " 张图片上传失败（已跳过）。\n服务器返回：" + lastErr);
            }
        }
    }

    function importFile(editor, file) {
        var reader = new FileReader();
        reader.onload = function (e) {
            loading.update("正在解析 Word 文档...");
            window.mammoth.convertToHtml({ arrayBuffer: e.target.result })
                .then(function (result) { insertHtmlWithImages(editor, result.value || ""); })
                ["catch"](function (err) { loading.hide(); alert("Word 转换失败：" + (err && err.message ? err.message : err)); });
        };
        reader.onerror = function () { loading.hide(); alert("文件读取失败"); };
        reader.readAsArrayBuffer(file);
    }

    UE.registerUI("importword", function (editor, uiName) {
        editor.registerCommand(uiName, {
            execCommand: function () {
                var input = document.createElement("input");
                input.type = "file";
                input.accept = ".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document";
                input.style.position = "fixed";
                input.style.left = "-9999px";
                input.onchange = function () {
                    var file = input.files && input.files[0];
                    if (input.parentNode) input.parentNode.removeChild(input);
                    if (!file) return;
                    if (!/\.docx$/i.test(file.name)) { alert("仅支持 .docx 格式（.doc 请先用 Word 另存为 .docx）"); return; }
                    loading.show("正在导入 Word，请稍候...");
                    loadMammoth(function () { importFile(editor, file); }, function (msg) { loading.hide(); alert(msg); });
                };
                document.body.appendChild(input);
                input.click();
            }
        });
        editor.addListener("ready", function () {
            try {
                var root = editor.ui && editor.ui.getDom && editor.ui.getDom();
                if (!root) return;
                var word = root.querySelector(".edui-for-importword");
                var frame = root.querySelector(".edui-for-insertframe");
                if (word && frame && frame.parentNode && word !== frame.nextSibling) {
                    frame.parentNode.insertBefore(word, frame.nextSibling);
                }
            } catch (e) {}
        });

        return new UE.ui.Button({
            name: uiName,
            title: "导入Word文档（.docx）",
            cssRules: "",
            onclick: function () { editor.execCommand(uiName); }
        });
    });
})();
