var aiConfig = editor.getOpt('ai');

var aiFunctions = editor.getOpt('aiFunctions');

var ai_driver = '';

var isMultiLine = function (text) {
    return text.indexOf('\n') !== -1;
}

var fetchStream = function (url, option, onStream, onFinish) {
    fetch(url, Object.assign({
        method: 'POST',
    }, option)).then(response => {
        if (!response.ok) {
            onFinish({code: -1, msg: `HTTP error! status: ${response.status}，请检查插件[Ai智能创作平台]的${ai_driver}的AI配置`})
            return
        }
        const reader = response.body.getReader();
        const decoder = new TextDecoder('utf-8');
        let buffer = '';
        const textList = []

        function processChunk(chunk) {
            buffer += decoder.decode(chunk, {stream: true});
            // 分割事件流，每个事件以"data:"开头，以两个换行符结束
            const lines = buffer.split('\n');
            for (let line of lines) {
                line = line.trim();
                if (line.startsWith('data:')) {
                    const jsonStr = line.replace('data:', '').trim();
                    if (jsonStr === '[DONE]') {
                        onFinish({code: 0, msg: 'ok', data: {text: textList.join('')}})
                        return;
                    }
                    try {
                        let text = null
                        const data = JSON.parse(jsonStr);
                        if (data.choices && data.choices.length > 0 && data.choices[0].delta) {
                            text = data.choices[0].delta.content
                        } else if (data.choices && data.choices.length > 0 && data.choices[0].content) {
                            // 兼容百度文心一言格式
                            text = data.choices[0].content
                        } else if (data.result) {
                            // 兼容百度文心一言另一种格式
                            text = data.result
                        } else if (data.type) {
                            // 兼容ModStart
                            // {"type":"error","data":"xxx"}
                            // {"type":"end","data":"xxx"}
                            // {"type":"data","data":"xxx"}
                            if (data.type === 'error') {
                                onFinish({code: -1, msg: data.data})
                                return;
                            } else if (data.type === 'end') {
                                onFinish({code: 0, msg: 'ok', data: {text: textList.join('')}})
                                return;
                            } else if (data.type === 'data') {
                                text = data.data
                            }
                        } else if (data.sentence) {
                            // 兼容文心一言sentence格式
                            text = data.sentence
                        } else if (data.is_end === true) {
                            // 文心一言结束标志
                            onFinish({code: 0, msg: 'ok', data: {text: textList.join('')}})
                            return;
                        } else if (data.sentence_id !== undefined) {
                            // 从截图看到的文心一言格式
                            // 检查是否有text或content字段
                            if (data.is_end === false) {
                                if (data.sentence !== undefined && data.sentence !== null && data.sentence !== '') {
                                    text = data.sentence;
                                } else if (data.content !== undefined && data.content !== null && data.content !== '') {
                                    text = data.content;
                                } else if (data.text !== undefined && data.text !== null && data.text !== '') {
                                    text = data.text;
                                }
                            } else if (data.is_end === true) {
                                onFinish({code: 0, msg: 'ok', data: {text: textList.join('')}});
                                return;
                            }
                        }
                        if (text !== null) {
                            textList.push(text)
                            onStream({code: 0, msg: 'ok', data: {text: text}})
                        } else {
                            // 尝试获取完整数据用于调试
                            console.log('完整数据:', data);
                            
                            // 看看文心一言响应是否有其他可能包含内容的字段
                            for (let key in data) {
                                if (typeof data[key] === 'string' && data[key].trim() !== '') {
                                    console.log('尝试使用字段:', key, '值:', data[key]);
                                    text = data[key];
                                    textList.push(text);
                                    onStream({code: 0, msg: 'ok', data: {text: text}});
                                    return;
                                }
                            }
                            
                            onFinish({code: -1, msg: 'No text found!'});
                            console.log('无法解析的数据:', data);
                        }
                    } catch (e) {
                        onFinish({code: -1, msg: 'JSON parse error!' + e})
                    }
                }
            }
            buffer = lines.pop() || '';
        }

        function read() {
            reader.read().then(({done, value}) => {
                if (done) {
                    if (buffer) processChunk(new Uint8Array());
                    return;
                }
                processChunk(value);
                read();
            }).catch(error => {
                onFinish({code: -1, msg: 'Stream error!' + error})
            });
        }

        read();
    }).catch(error => {
        onFinish({code: -1, msg: 'Request error!' + error})
    });
}

var openAiCompletion = function (redata, param, option) {
    var ai_config = redata.ai_config;
    ai_driver = ai_config.driver;
    option = Object.assign({
        body: null
    }, ai_config.option)
    if (!option.body) {
        option.body = {
            model: ai_config.model,
            messages: [{role: 'user', content: param.promptText}],
            stream: true,
        }
    }
    fetchStream(
        ai_config.url,
        {
            headers: {
                'Authorization': `Bearer ${ai_config.key}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(option.body)
        },
        param.onStream,
        param.onFinish
    )
}

var drivers = {
    'Default': function (redata,param) {
        openAiCompletion(redata, param)
    },
}

function getRequest(driver) {
    if (aiConfig.driverRequest) {
        return aiConfig.driverRequest
    }
    if (driver in drivers) {
        return drivers[driver] ? drivers[driver] : drivers['Default'];
    }
    return null
}

var converter = new window.showdown.Converter();

var Ai = {
    runtime: {
        range: null,
    },
    init: function () {
        new Vue({
            el: '#app',
            data: {
                loading: false,
                selectText: '',
                inputText: '',
                promptText: '',
                resultText: '',
                resultError: '',
                functions: [],
            },
            mounted: function () {
                // 获取选区范围
                Ai.runtime.range = editor.selection.getRange();
                var cloneContents = Ai.runtime.range.cloneContents();

                // 检测是否可能是全选情况（通过特殊处理）
                var isFullEditor = false;
                try {
                    // 如果当前没有明确的选区内容，检查编辑器状态
                    if (!cloneContents || !cloneContents.textContent.trim()) {
                        // 如果编辑区域大小与选区大小接近，可能是全选
                        var editorBody = editor.body;
                        if (editorBody && editorBody.textContent && editorBody.textContent.trim().length > 0) {
                            isFullEditor = true;
                        }
                    }
                } catch(e) {
                    console.log('检测全选异常:', e);
                }
                
                // 处理选区内容
                if (false && isFullEditor) {
                    // 是全选情况，获取编辑器全部内容
                    this.selectText = editor.getContent({format: 'text'}) || editor.body.textContent.trim();
                    console.log('检测到全选，获取内容:', this.selectText.substring(0, 50) + '...');
                } else if (cloneContents) {
                    this.selectText = cloneContents.textContent.trim();
                } else {
                    this.selectText = '';
                }

                this.buildFunctions();
                this.$nextTick(() => {
                    this.$refs.inputField.focus();
                });
            },
            computed: {
                resultHtml: function () {
                    if (!this.resultText) {
                        return '';
                    }
                    return converter.makeHtml(this.resultText);
                },
                resultHeight: function () {
                    let height = 255
                    if (this.selectText) {
                        height -= 45
                    }
                    if (this.resultError) {
                        height -= 45
                    }
                    return height + 'px'
                }
            },
            methods: {
                buildFunctions: function () {
                    var enableParam = {
                        selectText: this.selectText
                    }
                    this.functions = aiFunctions.map(function (f) {
                        if (!f.enable(enableParam)) {
                            return null;
                        }
                        // 保存原始prompt模板（如果尚未保存）
                        if (!f.originalPrompt) {
                            f.originalPrompt = f.prompt;
                        }
                        // 基于原始模板进行替换，而不是当前的f.prompt
                        f.prompt = f.originalPrompt.replace(/\{selectText\}/g, enableParam.selectText);
                        return f;
                    }).filter(function (f) {
                        return !!f;
                    });
                },
                doSubmit: function () {
                    var that = this;
                    if (this.loading) {
                        return;
                    }
                    if (this.inputText) {
                        if (this.selectText) {
                            this.promptText = this.selectText + '\n\n' + this.inputText;
                        } else {
                            this.promptText = this.inputText;
                        }
                    }
                    if (!this.promptText) {
                        this.$nextTick(() => {
                            this.$refs.inputField.focus();
                        });
                        editor.tipError('请输入内容');
                        return;
                    }
                    this.loading = true;
                    this.resultError = '';
                    this.resultText = '';
                    var driverRequest = getRequest(aiConfig.driver)
                    if (!driverRequest) {
                        editor.tipError('未接入['+aiConfig.driver+']AI智能平台');
                        return;
                    }
                    $.ajax({
                        type : 'post',
                        url : top.window.location.pathname + "?m=admin&c=Ueditor&a=ai_helper_check",
                        data : {_ajax:1, promptText:this.promptText},
                        dataType : 'json',
                        success : function(res){
                            if (res.code == 1) {
                                driverRequest(res.data, {
                                    promptText: that.promptText,
                                    onStream: (res) => {
                                        if (res.code) {
                                            that.resultError = res.msg;
                                            return;
                                        }
                                        that.resultText += res.data.text;
                                        that.$nextTick(() => {
                                            if (that.$refs.resultContainer) {
                                                that.$refs.resultContainer.scrollTop = that.$refs.resultContainer.scrollHeight;
                                            }
                                        })
                                    },
                                    onFinish: (res) => {
                                        that.loading = false;
                                        if (res.code) {
                                            that.resultError = res.msg;
                                            return;
                                        }
                                        that.resultText = res.data.text;
                                        that.$nextTick(() => {
                                            if (that.$refs.resultContainer) {
                                                that.$refs.resultContainer.scrollTop = that.$refs.resultContainer.scrollHeight;
                                            }
                                        });
                                    }
                                })
                            } else {
                                that.loading = false;
                                editor.tipError(res.msg);
                            }
                        },
                        error: function(e){
                            that.loading = false;
                            editor.tipError(e.responseText);
                        }
                    });
                    return;
                },
                doSubmitFunction: function (f) {
                    this.promptText = f.prompt;
                    this.doSubmit()
                },
                doInsert: function () {
                    editor.fireEvent('saveScene');
                    if (this.selectText) {
                        if (isMultiLine(this.resultText)) {
                            Ai.runtime.range.insertNode(document.createRange().createContextualFragment(this.resultHtml));
                        } else {
                            Ai.runtime.range.insertNode(document.createTextNode(this.resultText));
                        }
                    } else {
                        if (isMultiLine(this.resultText)) {
                            editor.execCommand('insertHtml', this.resultHtml);
                        } else {
                            editor.execCommand('insertHtml', this.resultText);
                        }
                    }
                    dialog.close(true);
                },
                doReplace: function () {
                    editor.fireEvent('saveScene');
                    Ai.runtime.range.deleteContents();
                    if (isMultiLine(this.resultText)) {
                        Ai.runtime.range.insertNode(document.createRange().createContextualFragment(this.resultHtml));
                    } else {
                        Ai.runtime.range.insertNode(document.createTextNode(this.resultText));
                    }
                    dialog.close(true);
                },
            }
        });
    },
};
utils.domReady(function () {
    Ai.init();
});
