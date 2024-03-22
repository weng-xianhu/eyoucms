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

namespace think\template\taglib\eyou;

use think\Db;
use think\Request;

/**
 * 视频播放
 */
class TagVideoplay extends Base
{
    public $fid = '';

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->fid = input('param.fid/d', 0);
    }

    /**
     * 获取每篇文章的属性
     * @author wengxianhu by 2018-4-20
     */
    public function getVideoplay($aid = '', $autoplay = '')
    {
        $aid = !empty($aid) ? $aid : $this->aid;
        if (empty($aid)) {
            echo '标签videoplay报错：缺少属性 aid 值。';
            return false;
        }

        //当前文章的视频列表
        $where = [
            'aid'   => $aid,
        ];
        !empty($this->fid) && $where['file_id'] = $this->fid;
        $row = Db::name('media_file')
            ->where($where)
            ->order('sort_order asc, file_id asc')
            ->cache(true,EYOUCMS_CACHE_TIME,"media_file")
            ->select();
        /*--end*/

        if (empty($row)) {
            return false;
        } else {
            // 获取文档数据
            $archives  = Db::name('archives')->where(['aid' => $aid])->field('users_price, users_free, arc_level_id')->find();
            $UsersData = session('users');
            $UsersID   = !empty($UsersData['users_id']) ? $UsersData['users_id'] : 0;

            $MediaOrder = [];
            if (!empty($UsersID)) {
                $where = [
                    'order_status' => 1,
                    'users_id' => $UsersID
                ];
                $field = 'order_id, users_id, product_id';
                $MediaOrder = Db::name('media_order')->field($field)->where($where)->getAllWithIndex('product_id');
            }

            if (0 < intval($archives['arc_level_id']) && !empty($UsersData)) {
                // 查询会员信息
                $users = Db::name('users')
                    ->alias('a')
                    ->field('a.users_id,b.level_value,b.level_name')
                    ->join('__USERS_LEVEL__ b', 'a.level = b.level_id', 'LEFT')
                    ->where(['a.users_id'=>$UsersID])
                    ->find();
                // 查询播放所需等级值
                $file_level = Db::name('archives')
                    ->alias('a')
                    ->field('b.level_value,b.level_name')
                    ->join('__USERS_LEVEL__ b', 'a.arc_level_id = b.level_id', 'LEFT')
                    ->where(['a.aid'=>$aid])
                    ->find();
            }

            // 处理视频数据
            $result = [];
            foreach ($row as $key => $val) {
                if (!empty($val['file_url'])) {
                    $val['txy_video_id'] = '';
                    $FileUrl = explode('txy_video_', $val['file_url']);
                    if (empty($FileUrl[0]) && !empty($FileUrl[1])) {
                        $val['txy_video_id'] = $FileUrl[1];// 腾讯云视频ID
                    }

                    if (empty($val['txy_video_id']) && !is_http_url($val['file_url'])) {
                        $val['file_url'] = handle_subdir_pic($val['file_url'], 'media', true);
                    }

                    if (empty($val['gratis'])) {
                        if (empty($MediaOrder[$aid])) {
                            if (0 < $archives['users_price'] && empty($archives['users_free'])) {
                                $val['file_url'] = '';
                            }
                            else if (0 < intval($archives['arc_level_id'])) {
                                if (empty($UsersID)) {
                                    $val['file_url'] = '';
                                } else {
                                    if ($users['level_value'] < $file_level['level_value']) {
                                        $val['file_url'] = '';
                                    }
                                }
                            }
                        }
                    }

                    $result = $val;
                    break;
                }
            }
            
            if (!empty($result)) {
                // 腾讯云点播视频所需内置JS及CSS样式
                $result['txy_video_file'] = <<<EOF
<link href="https://imgcache.qq.com/open/qcloud/video/tcplayer/tcplayer.css" rel="stylesheet">
<script src="https://imgcache.qq.com/open/qcloud/video/tcplayer/libs/hls.min.0.13.2m.js"></script>
<script src="https://imgcache.qq.com/open/qcloud/video/tcplayer/tcplayer.v4.1.min.js"></script>
EOF;
                $from_id = "video_play_20200520_{$aid}";
                $result['id'] = " id='{$from_id}' ";
                if ('on' == $autoplay) {
                    $autoplay_str = "document.getElementById('{$from_id}').autoplay = 'autoplay';";
                } else {
                    $autoplay_str = '';
                }
                $buy_url = ROOT_DIR . "/index.php?m=user&c=Media&a=media_order_buy";
                $record_process_url = ROOT_DIR . "/index.php?m=home&c=Media&a=record_process";
                $result['hidden'] = <<<EOF
                <input type="hidden" id="fid1616057948" value="{$this->fid}">
<script type="text/javascript">
    // if (window.jQuery) {
    //     $('#{$from_id}').bind('contextmenu',function() { return false; });
    // }
    if ('video' == document.getElementById('{$from_id}').tagName.toLowerCase()) {
        if (!document.getElementById('{$from_id}').controls) {
            document.getElementById('{$from_id}').controls = 'controls';
        }
        document.getElementById('{$from_id}').setAttribute('controlslist', 'nodownload');
        document.getElementById('{$from_id}').setAttribute('oncontextmenu', 'return false');
        {$autoplay_str}
    }
    /**记录播放时长**/
    
    var video = document.getElementById('{$from_id}');
    var timeDisplay;
    var times = 0;
     video.addEventListener('pause', function () { //暂停开始执行的函数
           submitPlayRecord();
    });
   video.addEventListener('waiting', function() {
       if(times == 1){
           timeDisplay = this.duration;
           submitPlayRecord();
       }
        times ++;
    }, false);
//   video.addEventListener('canplaythrough', function() {
//         if(times == 1){
//           timeDisplay = this.duration;
//           submitPlayRecord();
//       }
//        times ++;
//    }, false);
   
    video.addEventListener('ended', function () { //结束
          submitPlayRecord();
    }, false);
    //监听播放时间
    //使用事件监听方式捕捉事件
    video.addEventListener("timeupdate",function(){
        //用秒数来显示当前播放进度
        timeDisplay = Math.floor(this.currentTime);
    },false);
    
    window.addEventListener('unload', function() {
        //窗口关闭后
        submitPlayRecord();
    });
    function submitPlayRecord() {
        if (document.getElementById('fid1616057948')) {
            var fid = document.getElementById('fid1616057948').value;
            if (fid > 0) {
                var ajax = new XMLHttpRequest();
                ajax.open("post", "{$record_process_url}", true);
                ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
                ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                ajax.send("aid={$aid}&file_id="+fid+"&timeDisplay="+timeDisplay);
                ajax.onreadystatechange = function () {
                    
                };
            }
        }
    }
   /**记录播放时长**/

    // 视频购买
    function MediaOrderBuy_v878548() {
        var ajax = new XMLHttpRequest();
        ajax.open("post", '{$buy_url}', true);
        ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
        ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        ajax.send('aid={$aid}');
        ajax.onreadystatechange = function () {
            if (ajax.readyState==4 && ajax.status==200) {
                var json = ajax.responseText;  
                var res  = JSON.parse(json);
                if (1 == res.code && res.url) {
                    window.location.href = res.url;
                } else if (0 == res.code && res.url) {
                    window.location.href = res.url;
                } else {
                    if (!window.layer) {
                        alert(res.msg);
                    } else {
                        layer.alert(res.msg, {icon: 5, title: false, closeBtn: false});
                    }
                }
          　}
        };
    }
    function MediaOrderBuy_1592878548() {
        MediaOrderBuy_v878548();
    }

</script>
EOF;
            }

            return $result;
        }

    }
}