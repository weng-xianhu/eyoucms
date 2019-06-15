var uploader;
// 文件上传
jQuery(function() {
    var $ = jQuery,
        $list = $('#thelist'),
        $btn = $('#ctlBtn'),
        $picker = $('#picker'),
        state = 'pending';

    uploader = WebUploader.create({

        // 不压缩image
        resize: false,

        // swf文件路径
        swf: uploader_swf,

        // 文件接收服务端。
        server: server_url,

        // 选择文件的按钮。可选。
        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
        pick: '#picker'
    });

    // 当有文件添加进来的时候
    uploader.on( 'fileQueued', function( file ) {
        $list.append( '<div id="' + file.id + '" class="item">' +
            '<h4 class="info">' + file.name + '&nbsp;&nbsp;' + 
            '<a href="javascript:void(0);" onclick="del_WU_FILE(\'' + file.id + '\');">[删除]</a></h4>' +
            '<p class="state">等待上传...</p>' +
            '<input type="hidden" name="fileupload[]" value=""/>' + 
            '<input type="hidden" name="fileSize[]" value=""/>' + 
            '<input type="hidden" name="fileMime[]" value=""/>' + 
            '<input type="hidden" name="uhash[]" value=""/>' + 
            '<input type="hidden" name="md5file[]" value=""/>' + 
        '</div>' );
    });

    // 文件上传过程中创建进度条实时显示。
    uploader.on( 'uploadProgress', function( file, percentage ) {
        var $li = $( '#'+file.id );
        // var $percent = $li.find('.progress .progress-bar');

        // 避免重复创建
        // if ( !$percent.length ) {
        //     $percent = $('<div class="progress progress-striped active">' +
        //       '<div class="progress-bar" role="progressbar" style="width: 0%">' +
        //       '</div>' +
        //     '</div>').appendTo( $li ).find('.progress-bar');
        // }

        $li.find('p.state').text('上传中……'+ Math.floor(percentage * 100) + '%');
        if (percentage == 1) {
            $( '#'+file.id ).find('p.state').text('处理中……');
        }

        // $percent.css( 'width', percentage * 100 + '%' );
    });

    uploader.on( 'uploadSuccess', function( file, res ) {
        if (res.error.code == 0) {
            $( '#'+file.id ).find("input[name^=fileupload]").val(res.error.path);
            $( '#'+file.id ).find("input[name^=fileSize]").val(file.size);
            $( '#'+file.id ).find("input[name^=fileMime]").val(res.error.mime);
            $( '#'+file.id ).find("input[name^=uhash]").val(res.error.uhash);
            $( '#'+file.id ).find("input[name^=md5file]").val(res.error.md5file);
            $( '#'+file.id ).find('p.state').text('已上传，记得提交保存');
        } else {
            $( '#'+file.id ).find('p.state').text(res.error.msg + '，删掉重新选择');
            try {  
                uploader.removeFile(file.id, true);
            } catch(e) {  
                // 出现异常以后执行的代码  
                // e:exception，用来捕获异常的信息  
            } 
            showErrorMsg(res.error.msg);
            return false;
        }
    });

    uploader.on( 'uploadError', function( file, e ) {
        $( '#'+file.id ).find('p.state').html('<em style="color:red;">上传出错，可能超过服务器设定的文件大小</em>');
    });

    uploader.on( 'uploadComplete', function( file ) {
        // $( '#'+file.id ).find('.progress').fadeOut();
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
            var len = $( '#thelist' ).find("input[name^=fileupload]").length;
            if (parseInt(len) > 0) {
                uploader.upload();
            } else {
                layer.alert('请选择上传的文件！', {icon: 5});
                return false;
            }
        }
    });
});

function del_WU_FILE(id)
{
    try {  
        uploader.removeFile(id, true);
    } catch(e) {  
        // 出现异常以后执行的代码  
        // e:exception，用来捕获异常的信息  
    } 
    $('#'+id).remove();
}