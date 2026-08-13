/**
 * 易优CMS - 留言表单公共JS
 * 从 TagGuestbookform.php 内联脚本中提取
 */
(function () {
    window.EyouGbook = {
        /**
         * 初始化留言表单
         * @param {Object} cfg 配置参数
         */
        init: function (cfg) {
            this.bindSubmit(cfg);
            this.fetchToken(cfg);
        },

        /**
         * 绑定表单提交验证
         */
        bindSubmit: function (cfg) {
            window[cfg.submitFn] = function (form) {
                var gourl = document.getElementById('gourl_' + cfg.tokenId);
                if (gourl) gourl.value = encodeURIComponent(window.location.href);

                var x = form.elements;
                for (var i = 0; i < x.length; i++) {
                    for (var j = 0; j < cfg.rules.length; j++) {
                        var rule = cfg.rules[j];
                        if (x[i].name !== rule.name) continue;

                        // 必填检查 - 多选框
                        if (rule.required && rule.type === 'checkbox') {
                            var names = document.getElementsByName(rule.name);
                            var checked = false;
                            for (var k = 0; k < names.length; k++) {
                                if (names[k].checked) { checked = true; break; }
                            }
                            if (!checked) { alert(rule.emptyMsg); return false; }
                        }
                        // 必填检查 - 单选框
                        else if (rule.required && rule.type === 'radio') {
                            var names = document.getElementsByName(rule.name);
                            var checked = false;
                            for (var k = 0; k < names.length; k++) {
                                if (names[k].checked) { checked = true; break; }
                            }
                            if (!checked) { alert(rule.emptyMsg); return false; }
                        }
                        // 必填检查 - 普通输入
                        else if (rule.required && x[i].value.length === 0) {
                            alert(rule.emptyMsg);
                            return false;
                        }

                        // 正则格式检查
                        if (rule.pattern && x[i].value.length > 0 && !(new RegExp(rule.pattern)).test(x[i].value)) {
                            alert(rule.patternMsg);
                            return false;
                        }
                    }
                }

                // 自定义提交前回调
                if (cfg.beforeSubmit) {
                    try { if (false === window[cfg.beforeSubmit]()) return false; } catch (e) {}
                }
                form.submit();
            };
        },

        /**
         * 获取CSRF Token
         */
        fetchToken: function (cfg) {
            var ajax = new XMLHttpRequest();
            ajax.open('post', cfg.rootDir + '/index.php?m=api&c=Ajax&a=get_token', true);
            ajax.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            ajax.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            ajax.send('name=__token__' + cfg.tokenId);
            ajax.onreadystatechange = function () {
                if (ajax.readyState === 4 && ajax.status === 200) {
                    document.getElementById(cfg.tokenId).value = ajax.responseText;
                    document.getElementById('gourl_' + cfg.tokenId).value = encodeURIComponent(window.location.href);
                }
            };
        },

        /**
         * 刷新验证码
         */
        fleshVerify: function (id, rootDir, lang) {
            var token = id.replace(/verify_/g, '__token__');
            var src = rootDir + '/index.php?m=api&c=Ajax&a=vertify&type=guestbook&lang=' + lang + '&token=' + token;
            src += '&r=' + Math.floor(Math.random() * 100);
            document.getElementById(id).src = src;
        },

        /**
         * 区域级联选择
         */
        getNextRegion: function (id, name, level, rootDir, placeholder) {
            var input = document.getElementById('attr_' + name);
            var first = document.getElementById('first_id_' + name);
            var second = document.getElementById('second_id_' + name);
            var third = document.getElementById('third_id_' + name);
            var fvalue = '', svalue = '', tvalue = '', value = '';

            if (level == 1) {
                if (second) { second.style.display = 'none'; second.innerHTML = ''; }
                if (third) { third.style.display = 'none'; third.innerHTML = ''; }
                fvalue = first.options[first.selectedIndex].value;
                input.value = fvalue;
                value = fvalue;
            } else if (level == 2) {
                if (third) { third.style.display = 'none'; third.innerHTML = ''; }
                fvalue = first.options[first.selectedIndex].value;
                svalue = second.options[second.selectedIndex].value;
                input.value = svalue ? fvalue + ',' + svalue : fvalue;
                value = svalue;
            } else if (level == 3) {
                fvalue = first.options[first.selectedIndex].value;
                svalue = second.options[second.selectedIndex].value;
                tvalue = third.options[third.selectedIndex].value;
                input.value = tvalue ? fvalue + ',' + svalue + ',' + tvalue : fvalue + ',' + svalue;
                value = tvalue;
            }

            if (value && document.getElementById(id)) {
                var el = document.getElementById(id);
                el.options.add(new Option(placeholder, ''));
                var ajax = new XMLHttpRequest();
                ajax.open('post', rootDir + '/index.php?m=api&c=Ajax&a=get_region', true);
                ajax.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                ajax.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                ajax.send('pid=' + value);
                ajax.onreadystatechange = function () {
                    if (ajax.readyState === 4 && ajax.status === 200) {
                        var data = JSON.parse(ajax.responseText).data;
                        if (data) {
                            data.forEach(function (item) {
                                el.options.add(new Option(item.name, item.id));
                                el.style.display = 'block';
                            });
                        }
                    }
                };
            }
        }
    };
})();
