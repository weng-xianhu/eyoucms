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

namespace app\api\logic\v1;

use think\Model;
use think\Db;

/**
 * 业务逻辑
 */
class WechatLogic extends Model
{
    /**
     * 设置公众号的access_token
     * @param array $res  [description]
     * @param array $info [description]
     */
    public function set_access_token($res = [], $info = [])
    {
        $setting_info = [
            'appid'  => $info['appid'],
            'secret' => $info['appsecret'],
            'access_token' => $res['access_token'],
            'expires_time' => getTime() + $res['expires_in'] - 200 //提前200s过期
        ];
        tpSetting(md5($info['appid']), $setting_info);
    }

    /**
     * 更新公众号ticket信息
     * @return [type] [description]
     */
    public function update_ticket($appid, $update = [])
    {
        $setting = tpSetting(md5($appid));
        $setting['ticket'] = $update['ticket'];
        $setting['ticket_expires_time'] = $update['ticket_expires_time'];
        tpSetting(md5($appid), $setting);
    }

    /**
     * 获取微信支付配置信息
     * @return [type] [description]
     */
    public function get_wechat_pay_config()
    {
        $pay_info = Db::name('pay_api_config')->where('pay_mark','wechat')->value('pay_info');
        if (!empty($pay_info)) $pay_info = unserialize($pay_info);
        return !empty($pay_info['is_open_wechat']) ? [] : $pay_info;
    }

    /**
     * 获取公众号配置信息
     * @return [type] [description]
     */
    public function get_wechat_config()
    {
        $info = tpSetting("OpenMinicode.conf_wechat");
        if (!empty($info)) {
            $info = json_decode($info, true);
        }
        $pay_info = Db::name('pay_api_config')->where('pay_mark','wechat')->value('pay_info');
        if(!empty($pay_info)){
            $pay_info = unserialize($pay_info);
        }
        // $info['appid'] = 'wxf0a192919a62bd1e';
        // $info['appsecret'] = '2d838af9f33173e41089c6040c238c18';
        $info['mchid'] = !empty($pay_info['mchid']) ? $pay_info['mchid'] : '';
        $info['key'] = !empty($pay_info['key']) ? $pay_info['key'] : '';
        return empty($info) ? [] : $info;
    }

    /**
     * 获取公众号类型
     */
    public function get_wechat_type($type = '')
    {
        $list = array(
            // 1 => '订阅号',
            2 => '认证订阅号',
            // 3 => '服务号',
            4 => '认证服务号',
        );
        if (!empty($type)) {
            if (isset($list[$type])) {
                $list = $list[$type];
            } else {
                $list = '';
            }
        }

        return $list;
    }

    /**
     * 关键字类型
     */
    public function get_keyword_type($type = '')
    {
        $list = array(
            'TEXT' => '文本',
            'PIC' => '图片',
            'IMG' => '单图文',
            'NEWS' => '组合图文',
        );
        if (!empty($type)) {
            if (isset($list[$type])) {
                $list = $list[$type];
            } else {
                $list = '';
            }
        }

        return $list;
    }

    /**
     * 抓取远程图片
     */
    public function save_remote($fieldName, $maxSize = 5242880){
        $imgUrl = htmlspecialchars($fieldName);
        $imgUrl = str_replace("&amp;","&",$imgUrl);

        //http开头验证
        if(strpos($imgUrl,"http") !== 0){
            $data=array(
                'state' => '链接不是http链接',
            );
            return json_encode($data);
        }
        //获取请求头并检测死链
        $heads = get_headers($imgUrl);
        if(!(stristr($heads[0],"200") && stristr($heads[0],"OK"))){
            $data=array(
                'state' => '链接不可用',
            );
            return json_encode($data);
        }
        //格式验证(扩展名验证和Content-Type验证)
        if(preg_match("/^http(s?):\/\/mmbiz.qpic.cn\/(.*)/", $imgUrl) != 1){
            $allowFiles = [".png", ".jpg", ".jpeg", ".gif", ".bmp", ".ico", ".webp"];
            $fileType = strtolower(strrchr($imgUrl,'.'));
            if(!in_array($fileType, $allowFiles) || (isset($heads['Content-Type']) && stristr($heads['Content-Type'],"image"))){
                $data=array(
                    'state' => '链接contentType不正确',
                );
                return json_encode($data);
            }
        }

        //打开输出缓冲区并获取远程图片
        ob_start();
        $context = stream_context_create(
            array('http' => array(
                'follow_location' => false // don't follow redirects
            ))
        );
        readfile($imgUrl,false,$context);
        $img = ob_get_contents();
        ob_end_clean();
        preg_match("/[\/]([^\/]*)[\.]?[^\.\/]*$/",$imgUrl,$m);

        $dirname = './'.UPLOAD_PATH.'remote/'.date('Y/m/d').'/';
        $file['oriName'] = $m ? $m[1] : "";
        $file['filesize'] = strlen($img);
        $file['ext'] = strtolower(strrchr('remote.jpg','.'));
        $file['name'] = uniqid().$file['ext'];
        $file['fullName'] = $dirname.$file['name'];
        $fullName = $file['fullName'];

        //检查文件大小是否超出限制
        if($file['filesize'] >= $maxSize){
            $data=array(
                'state' => '文件大小超出网站限制',
            );
            return json_encode($data);
        }

        //创建目录失败
        if(!file_exists($dirname) && !mkdir($dirname,0777,true)){
            $data=array(
                'state' => '目录创建失败',
            );
            return json_encode($data);
        }else if(!is_writeable($dirname)){
            $data=array(
                'state' => '目录没有写权限',
            );
            return json_encode($data);
        }

        //移动文件
        if(!(file_put_contents($fullName, $img) && file_exists($fullName))){ //移动失败
            $data=array(
                'state' => '写入文件内容错误',
            );
            return json_encode($data);
        }else{ //移动成功
            $data=array(
                'state' => 'SUCCESS',
                'url' => substr($file['fullName'],1),
                'title' => $file['name'],
                'original' => $file['oriName'],
                'type' => $file['ext'],
                'size' => $file['filesize'],
            );
        }

        return json_encode($data);
    }
}