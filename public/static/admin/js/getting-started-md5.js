// 文件上传
jQuery(function() {
    var $ = jQuery,
        $list = $('#thelist'),
        $btn = $('#ctlBtn'),
        state = 'pending',
        uploader;

        uploader = WebUploader.create({

            // 不压缩image
            resize: false,

            // 文件接收服务端。
            server: server_url,

            // 选择文件的按钮。可选。
            // 内部根据当前运行是创建，可能是input元素，也可能是flash.
            pick: '#picker'
        }).on('fileQueued', function( file ) {
            $list.append( '<div id="' + file.id + '" class="item">' +
                '<h4 class="info">' + file.name + '</h4>' +
                '<p class="state">等待上传...</p>' +
            '</div>' );

            // 返回的是 promise 对象
            this.md5File(file, 0, 2000 * 1024 * 1024)

                // 可以用来监听进度
                .progress(function(percentage) {
                    var $li = $( '#'+file.id );
                    $li.find('p.state').text('上传中……'+ Math.floor(percentage * 100) + '%');
                    if (percentage == 1) {
                        $( '#'+file.id ).find('p.state').text('已上传，处理中……');
                    }
                })
                // 完成
                .then(function(val) {
                    $( '#'+file.id ).find('p.state').text('上传成功');
                });
        });

        uploader.on( 'all', function( type ) {
            if ( type === 'startUpload' ) {
                state = 'uploading';
            } else if ( type === 'stopUpload' ) {
                state = 'paused';
            } else if ( type === 'uploadFinished' ) {
                state = 'done';
            }

            if ( state === 'uploading' ) {
                $btn.text('暂停上传');
            } else {
                $btn.text('开始上传');
            }
        });

        $btn.on( 'click', function() {
            if ( state === 'uploading' ) {
                uploader.stop();
            } else {
                uploader.upload();
            }
        });
});