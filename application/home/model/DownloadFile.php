<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海口快推科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */

namespace app\home\model;

use think\Db;
use think\Model;

/**
 * 下载文件
 */
class DownloadFile extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }
    
    /**
     * 获取指定下载文章的所有文件
     * @author 小虎哥 by 2018-4-3
     */
    public function getDownFile($aids = [], $field = '*')
    {
        $where = [];
        !empty($aids) && $where['aid'] = ['IN', $aids];
        $result = Db::name('DownloadFile')->field($field)
            ->where($where)
            ->order('sort_order asc')
            ->select();

        if (!empty($result)) {
            $hidden = '';
            $n = 1;
            foreach ($result as $key => $val) {
                $downurl     = ROOT_DIR."/index.php?m=home&c=View&a=downfile&id={$val['file_id']}&uhash={$val['uhash']}";

                $result[$key]['title'] = $val['file_name'];
                if (!empty($val['extract_code'])) {
                    $result[$key]['title'] = '提取码：'.$val['extract_code'];
                }
                if (is_http_url($val['file_url'])) {
                    $result[$key]['server_name'] = !empty($val['server_name']) ? $val['server_name'] : "远程服务器({$n})";
                } else {
                    $result[$key]['server_name'] = !empty($val['server_name']) ? $val['server_name'] : "本地服务器({$n})";
                }
                $n++;

                $result[$key]['softlinks'] = $downurl;
                $result[$key]['downurl'] = "javascript:ey_1563185380({$val['file_id']});";
                $result[$key]['ey_1563185380'] = "<input type='hidden' id='ey_file_list_{$val['file_id']}' value='{$downurl}' /><form id='form_file_list_{$val['file_id']}' method='post' action='{$downurl}' target='_blank' style='display: none!important;'></form>";
                $result[$key]['ey_1563185376'] = $this->handleDownJs($hidden);
            }
            $result = group_same_key($result, 'aid');
        }

        return $result;
    }

    private function handleDownJs(&$hidden = '')
    {
        if (empty($hidden)) {
            $hidden = <<<EOF
                <script type="text/javascript">
                  function ey_1563185380(file_id) {
                    var downurl = document.getElementById("ey_file_list_"+file_id).value + "&_ajax=1";
                    //创建异步对象
                    var ajaxObj = new XMLHttpRequest();
                    ajaxObj.open("get", downurl, true);
                    ajaxObj.setRequestHeader("X-Requested-With","XMLHttpRequest");
                    ajaxObj.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                    //发送请求
                    ajaxObj.send();
                    ajaxObj.onreadystatechange = function () {
                        // 这步为判断服务器是否正确响应
                        if (ajaxObj.readyState == 4 && ajaxObj.status == 200) {
                          var json = ajaxObj.responseText;  
                          var res = JSON.parse(json);
                          if (0 == res.code) {
                            // 没有登录
                            if (undefined != res.data.is_login && 0 == res.data.is_login) {
                                if (document.getElementById('ey_login_id_v665117')) {
                                    $('#ey_login_id_v665117').trigger('click');
                                } else {
                                    window.location.href = res.data.url;
                                }
                            } else {
                                if (res.data.need_buy == 1){
                                    DownloadBuyNow(res.data.url,res.data.aid);
                                    return false;
                                } 
                                if (!window.layer) {
                                    alert(res.msg);
                                    if (undefined != res.data.url && res.data.url) {
                                        window.location.href = res.data.url;
                                    }
                                } else {
                                    if (undefined != res.data.url && '' != res.data.url) {
                                        layer.confirm(res.msg, {
                                            title: false
                                            , icon: 5
                                            , closeBtn: false
                                        }, function (index) {
                                            layer.close(index);
                                            window.location.href = res.data.url;
                                        });
                                    } else {
                                        layer.alert(res.msg, {icon: 5, title: false, closeBtn: false});
                                    }
                                }
                            }
                            return false;
                          }else{
                            // document.getElementById('form_file_list_'+file_id).submit();
                            window.location.href = res.url;
                            // window.open(res.url);
                          }
                        } 
                    };
                  };
                  
                  // 立即购买
                function DownloadBuyNow(url,aid){
                    // 步骤一:创建异步对象
                    var ajax = new XMLHttpRequest();
                    //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
                    ajax.open("post", url, true);
                    // 给头部添加ajax信息
                    ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
                    // 如果需要像 HTML 表单那样 POST 数据，请使用 setRequestHeader() 来添加 HTTP 头。然后在 send() 方法中规定您希望发送的数据：
                    ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                    //步骤三:发送请求+数据
                    ajax.send('_ajax=1&aid=' + aid+'&return_url='+encodeURIComponent(window.location.href));
                    //步骤四:注册事件 onreadystatechange 状态改变就会调用
                    ajax.onreadystatechange = function () {
                        //步骤五 请求成功，处理逻辑
                        if (ajax.readyState==4 && ajax.status==200) {
                            var json = ajax.responseText;
                            var res  = JSON.parse(json);
                            if (1 == res.code) {
                                layer.open({
                                    type: 2,
                                    title: '选择支付方式',
                                    shadeClose: false,
                                    maxmin: false, //开启最大化最小化按钮
                                    skin: 'WeChatScanCode_20191120',
                                    area: ['500px', '202px'],
                                    content: res.url
                                });
                            } else {
                                if (res.data.url){
                                    //登录
                                    if (document.getElementById('ey_login_id_v665117')) {
                                        $('#ey_login_id_v665117').trigger('click');
                                    } else {
                                        if (-1 == res.data.url.indexOf('?')) {
                                            window.location.href = res.data.url+'?referurl='+encodeURIComponent(window.location.href);
                                        }else{
                                            window.location.href = res.data.url+'&referurl='+encodeURIComponent(window.location.href);
                                        }
                                    }
                                }else{
                                    if (!window.layer) {
                                        alert(res.msg);
                                    } else {
                                        layer.alert(res.msg, {icon: 5, title: false, closeBtn: false});
                                    }
                                }
                            }
                        }
                    };
                }
                </script>
EOF;
        }

        return $hidden;
    }
}