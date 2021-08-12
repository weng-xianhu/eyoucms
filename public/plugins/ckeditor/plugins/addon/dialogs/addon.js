CKEDITOR.dialog.add('addon', function(a) {
    var b = CKEDITOR.plugins.link,
        d = function() {
            var F = this.getDialog(),
                G = ['urlOptions'],
                H = this.getValue(),
                I = F.definition.getContents('upload'),
                J = I && I.hidden;
            if (H == 'url') {
                if (a.config.linkShowTargetTab) F.showPage('target');
                if (!J) F.showPage('upload');
            } else {
                F.hidePage('target');
                if (!J) F.hidePage('upload');
            }
            for (var K = 0; K < G.length; K++) {
                var L = F.getContentElement('info', G[K]);
                if (!L) continue;
                L = L.getElement().getParent().getParent();
                if (G[K] == H + 'Options') L.show();
                else L.hide();
            }
            F.layout();
        },
        j = /^((?:http|https|ftp|news):\/\/)?(.*)$/,

        p = function(F, G) {
            var H = G && (G.data('cke-saved-href') || G.getAttribute('href')) || '', L, M = {};

        if (!M.type)
            if (H && (L = H.match(j))) {
                M.type = 'url';
                M.url = {};
                M.url.protocol = L[1];
                M.url.url = L[2];
            } else M.type = 'url';
            this._.selectedElement = G;
            return M;
        };

    var D = a.lang.common,
        E = a.lang.link;
    return {
        title: '附件上传',
        minWidth: 350,
        minHeight: 230,
        contents: [
            {
                id: 'upload',
                label: E.upload,
                title: E.upload,
                hidden: true,
                filebrowser: 'uploadButton',
                elements: [{
                    type: 'file',
                    id: 'upload',
                    label: D.upload,
                    style: 'height:40px',
                    size: 29
                }, {
                    type: 'fileButton',
                    id: 'uploadButton',
                    label: D.uploadSubmit,
                    filebrowser: 'info:url',
                    'for': ['upload', 'upload']
                }]
            },
            {
            id: 'info',
            label: '附件信息',
            title: '附件信息',
            elements: [{
                type: 'vbox',
                id: 'urlOptions',
                children: [{
                    id : 'title',
                    type : 'text',
                    label : '附件标题',
                    style : 'width: 60%',
                    'default' : ''
                },{
                    type: 'hbox',
                    children: [ {
                        type: 'text',
                        id: 'url',
                        label: '附件地址',
                        required: true,
                        onLoad: function() {
                            this.allowOnChange = true;
                        },
                        onKeyUp: function() {
                            var K = this;
                            K.allowOnChange = false;
                            K.allowOnChange = true;
                        },
                        onChange: function() {
                            if (this.allowOnChange) this.onKeyUp();
                        },
                        validate: function() {
                            var H = this;
                            var F = H.getDialog();
                            if (!H.getValue()) {
                                alert('请输入附件地址');
                                return false;
                            }

                            // if (F.getContentElement('info', 'linkType') && F.getValueOf('info', 'linkType') != 'url') return true;
                            // if (/javascript\:/.test(H.getValue())) {
                            //     alert(D.invalidValue);
                            //     return false;
                            // }
                            if (H.getDialog().fakeObj) return true;
                            var G = CKEDITOR.dialog.validate.notEmpty(E.noUrl);
                            return G.apply(H);
                        },
                        setup: function(F) {
                            this.allowOnChange = false;
                            if (F.url) this.setValue(F.url.url);
                            this.allowOnChange = true;
                        },
                        commit: function(F) {
                            this.onChange();
                            if (!F.url) F.url = {};
                            F.url.url = this.getValue();
                            this.allowOnChange = false;
                        }
                    }]
                }]
            }]
        }],
        onShow: function() {
        },
        onOk: function() {
            var addonUrl = this.getValueOf( 'info', 'url' );
            var addonTitle = this.getValueOf( 'info', 'title');
            if (!addonTitle) addonTitle = addonUrl;
            var tempvar='<table width="450">\r    <tbody>\r        <tr>\r            <td width="20" height="30"><a target="_blank" href="'+addonUrl+'"><img border="0" align="middle" src="./public/plugins/ckeditor/plugins/addon/images/addon.gif" alt="" /></a></td>\r            <td><a target="_blank" href="'+addonUrl+'"><u>'+addonTitle+'</u></a></td>\r        </tr>\r    </tbody>\r</table>';
            a.insertHtml(tempvar);
        },
        onLoad: function() {
        },
        onFocus: function() {
        }
    };
});
