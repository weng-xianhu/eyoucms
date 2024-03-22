    function tag_collectnum_v670918(aid, root_dir)
    {
        if (document.getElementById("eyou_collectnum_220520_"+aid)) {
            if (!root_dir) root_dir = '';

            if (window.jQuery) {
                $.ajax({
                    type : 'get',
                    url : root_dir+"/index.php?m=api&c=Ajax&a=collectnum&aid="+aid,
                    data : {},
                    dataType : 'json',
                    success : function(res){
                        $("#eyou_collectnum_220520_"+aid).html(res);
                    }
                });
            } else {
                var ajax = new XMLHttpRequest();
                ajax.open("get", root_dir+"/index.php?m=api&c=Ajax&a=collectnum&aid="+aid, true);
                ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
                // ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                ajax.send();
                ajax.onreadystatechange = function () {
                    if (ajax.readyState==4 && ajax.status==200) {
                        document.getElementById("eyou_collectnum_220520_"+aid).innerHTML = ajax.responseText;
                  　}
                }
            }
        }
    }