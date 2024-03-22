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

namespace app\admin\controller;

use think\Db;
use think\Page;

class Canal extends Base
{
    public function _initialize(){
        parent::_initialize();
    }

    public function conf_api()
    {
        if (IS_AJAX_POST) {
            $data = tpSetting("OpenMinicode.conf", [], $this->main_lang);
            $data = json_decode($data, true);
            empty($data) && $data = [];

            $post = input('post.');
            // if (isset($post['apikey'])) {
            //     unset($post['apikey']);
            // }
            if ($post['apikey'] != $post['old_apikey']) {
                $post['apikey_uptime'] = getTime();
            }
            $data = array_merge($data, $post);
            tpSetting('OpenMinicode', ['conf' => json_encode($data)], $this->main_lang);
            $this->success("操作成功");
        }

        // 同步微信配置
        if (is_dir('./weapp/OpenMinicode/')) {
            $admin_logic_1649404323 = tpSetting('syn.admin_logic_1649404323', [], 'cn');
            if (empty($admin_logic_1649404323)) {
                $minicode = Db::name('weapp')->where('code', 'OpenMinicode')->value('data');
                $minicode = json_decode($minicode, true);
                if (!empty($minicode['appid'])) {
                    $data = [
                        'appid'  => $minicode['appid'],
                        'appsecret' => $minicode['secret'],
                        'mchid'  => '',
                        'apikey' => '',
                    ];
                    tpSetting('OpenMinicode', ['conf_weixin' => json_encode($data)], $this->main_lang);
                }
                tpSetting('syn', ['admin_logic_1649404323'=>1], 'cn');
            }
        }

        $data = tpSetting("OpenMinicode.conf", [], $this->main_lang);
        if (empty($data)) {
            $data = [];
            $data['apiopen'] = 0;
            $data['apiverify'] = 0;
            $data['apikey'] = get_rand_str(32, 0, 1);
            tpSetting('OpenMinicode', ['conf' => json_encode($data)], $this->main_lang);
        } else {
            $data = json_decode($data, true);
        }
        $this->assign('data', $data);

        //微信信息
        $weixin_data = tpSetting("OpenMinicode.conf_weixin", [], $this->main_lang);
        $weixin_data = json_decode($weixin_data, true);
        $this->assign('weixin_data', $weixin_data);
        // 小程序码
        $weixin_qrcodeurl = "";
        if (!empty($weixin_data['appid'])) {
            $filepath = UPLOAD_PATH."allimg/20220515/wx-{$weixin_data['appid']}.png";
            if (is_file($filepath)) {
                $weixin_qrcodeurl = "{$this->root_dir}/".$filepath;
            }
        }
        $this->assign('weixin_qrcodeurl', $weixin_qrcodeurl);
        //百度信息
        $baidu_data = tpSetting("OpenMinicode.conf_baidu", [], $this->main_lang);
        $baidu_data = json_decode($baidu_data, true);
        $this->assign('baidu_data', $baidu_data);

        // 小程序码
        $baidu_qrcodeurl = "";
        if (!empty($baidu_data['appid'])) {
            $filepath = UPLOAD_PATH."allimg/20220515/bd-{$baidu_data['appid']}.png";
            if (is_file($filepath)) {
                $baidu_qrcodeurl = "{$this->root_dir}/".$filepath;
            }
        }
        $this->assign('baidu_qrcodeurl', $baidu_qrcodeurl);

        // 抖音信息
        $toutiao_data = tpSetting("OpenMinicode.conf_toutiao", [], $this->main_lang);
        $toutiao_data = !empty($toutiao_data) ? json_decode($toutiao_data, true) : [];
        $this->assign('toutiao_data', $toutiao_data);

        return $this->fetch();
    }

    /**
     * 重置API接口密钥
     * @return [type] [description]
     */
    public function reset_apikey()
    {
        if (IS_AJAX_POST) {
            $data = tpSetting("OpenMinicode.conf", [], $this->main_lang);
            $data = json_decode($data, true);
            empty($data) && $data = [];
            $apikey = get_rand_str(32, 0, 1);
            $data['apikey'] = $apikey;
            // tpSetting('OpenMinicode', ['conf' => json_encode($data)], $this->main_lang);
            $this->success("重置成功", null, ['apikey'=>$apikey]);
        }
    }

    /**
     * 微信小程序配置
     * @return [type] [description]
     */
    public function conf_weixin()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            $appid = !empty($post['appid']) ? trim($post['appid']) : trim($post['weixin_appid']);
            $appsecret = !empty($post['appsecret']) ? trim($post['appsecret']) : trim($post['weixin_appsecret']);
            $mchid = !empty($post['mchid']) ? trim($post['mchid']) : trim($post['weixin_mchid']);
            $apikey = !empty($post['apikey']) ? trim($post['apikey']) : trim($post['weixin_apikey']);

            if (!empty($appid) || !empty($appsecret)) {
                if (empty($appid)) {
                    $this->error('AppID不能为空！');
                }
                if (empty($appsecret)) {
                    $this->error('AppSecret不能为空！');
                }
                $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
                $response = httpRequest($url);
                $params = json_decode($response, true);
                if (!empty($params['errcode'])) {
                    if ($params['errcode'] == 40164) {
                        preg_match_all('#(\d{1,3}\.){3}\d{1,3}#i', $params['errmsg'], $matches);
                        $ip = !empty($matches[0][0]) ? $matches[0][0] : '';
                        $ipTips = "<font color='red'>请将IP：{$ip} 加入微信小程序的IP白名单里！</font>";
                        $this->error($ipTips);
                    } else {
                        $this->error($params['errmsg']);
                    }
                }

                if (!empty($appid) && !empty($mchid) && !empty($apikey)) {
                    $logic = new \app\api\logic\v1\ApiLogic;
                    $returnData = $logic->GetWechatAppletsPay($appid, $mchid, $apikey);
                    if (empty($returnData['code'])) {
                        $this->error($returnData['msg']);
                    }
                }
            }

            $data = [
                'appid'  => $appid,
                'appsecret' => $appsecret,
                'mchid'  => $mchid,
                'apikey' => $apikey,
            ];
            tpSetting('OpenMinicode', ['conf_weixin' => json_encode($data)], $this->main_lang);
            $this->success("操作成功");
        }

        $data = tpSetting("OpenMinicode.conf_weixin", [], $this->main_lang);
        $data = json_decode($data, true);
        $this->assign('data', $data);

        // 小程序码
        $qrcodeurl = "";
        if (!empty($data['appid'])) {
            $filepath = UPLOAD_PATH."allimg/20220515/wx-{$data['appid']}.png";
            if (is_file($filepath)) {
                $qrcodeurl = "{$this->root_dir}/".$filepath;
            }
        }
        $this->assign('qrcodeurl', $qrcodeurl);

        return $this->fetch();
    }

    /**
     * 获取微信小程序码
     * @return [type] [description]
     */
    public function ajax_get_weixin_qrcode()
    {
        $data = tpSetting("OpenMinicode.conf_weixin", [], $this->main_lang);
        $data = json_decode($data, true);
        $appid = !empty($data['appid']) ? $data['appid'] : '';
        $appsecret = !empty($data['appsecret']) ? $data['appsecret'] : '';
        if (!empty($appid) && !empty($appsecret)) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
            $response = httpRequest($url);
            $params = json_decode($response, true);
            if (isset($params['access_token'])) {
                $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$params['access_token'];
                $post_data = array(
                    "scene" => 'test',
                    "width" => 280,
                );
                $response = httpRequest($url, 'POST', json_encode($post_data, JSON_UNESCAPED_UNICODE));
                $params = json_decode($response,true);
                if (is_array($params) || $response === false) {
                    $msg = !empty($params['errmsg']) ? $params['errmsg'] : '可能没发布小程序';
                    $this->error($msg);
                } else {
                    $qrcodeurl = UPLOAD_PATH.'allimg/20220515';
                    tp_mkdir($qrcodeurl);
                    $qrcodeurl = $qrcodeurl."/wx-{$appid}.png";
                    if (@file_put_contents($qrcodeurl, $response)){
                        $qrcodeurl = $this->root_dir.'/'.$qrcodeurl;
                        $this->success('生成小程序码成功', null, ['qrcodeurl'=>$qrcodeurl]);
                    } else {
                        $this->error('生成小程序码失败');
                    }
                }
            }
        }

        $this->error('不存在信息');
    }

    /**
     * 百度小程序配置
     * @return [type] [description]
     */
    public function conf_baidu()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            $appid = !empty($post['appid']) ? trim($post['appid']) : trim($post['baidu_appid']);
            $appkey = !empty($post['appkey']) ? trim($post['appkey']) : trim($post['baidu_appkey']);
            $appsecret = !empty($post['appsecret']) ? trim($post['appsecret']) : trim($post['baidu_appsecret']);

            if (!empty($appid) || !empty($appkey) || !empty($appsecret)) {
                if (empty($appid)) {
                    $this->error('AppID不能为空！');
                }
                if (empty($appkey)) {
                    $this->error('AppKey不能为空！');
                }
                if (empty($appsecret)) {
                    $this->error('AppSecret不能为空！');
                }

                $url = "https://openapi.baidu.com/oauth/2.0/token?grant_type=client_credentials&client_id={$appkey}&client_secret={$appsecret}&scope=smartapp_snsapi_base";
                $response = httpRequest($url);
                $params = json_decode($response, true);
                if (!isset($params['access_token'])) {
                    $this->error('AppID或AppSecret不正确！');
                }
            }

            $data = [
                'appid'  => $appid,
                'appkey' => $appkey,
                'appsecret'  => $appsecret,
            ];
            tpSetting('OpenMinicode', ['conf_baidu' => json_encode($data)], $this->main_lang);
            $this->success("操作成功");
        }

        $data = tpSetting("OpenMinicode.conf_baidu", [], $this->main_lang);
        $data = json_decode($data, true);
        $this->assign('data', $data);

        // 小程序码
        $qrcodeurl = "";
        if (!empty($data['appid'])) {
            $filepath = UPLOAD_PATH."allimg/20220515/bd-{$data['appid']}.png";
            if (is_file($filepath)) {
                $qrcodeurl = "{$this->root_dir}/".$filepath;
            }
        }
        $this->assign('qrcodeurl', $qrcodeurl);

        return $this->fetch();
    }

    /**
     * 获取百度小程序码
     * @return [type] [description]
     */
    public function ajax_get_baidu_qrcode()
    {
        $data = tpSetting("OpenMinicode.conf_baidu", [], $this->main_lang);
        $data = json_decode($data, true);
        $appid = !empty($data['appid']) ? $data['appid'] : '';
        $appkey = !empty($data['appkey']) ? $data['appkey'] : '';
        $appsecret = !empty($data['appsecret']) ? $data['appsecret'] : '';
        if (!empty($appkey) && !empty($appsecret)) {
            $url = "https://openapi.baidu.com/oauth/2.0/token?grant_type=client_credentials&client_id={$appkey}&client_secret={$appsecret}&scope=smartapp_snsapi_base";
            $response = httpRequest($url);
            $params = json_decode($response, true);
            if (isset($params['access_token'])) {
                $url = "https://openapi.baidu.com/rest/2.0/smartapp/qrcode/getv2";
                $post_data = array(
                    "access_token" => $params['access_token'],
                );
                $response = httpRequest($url, 'POST', $post_data);
                $params = json_decode($response,true);
                if (!isset($params['data']['base64_str'])) {
                    $msg = !empty($params['errmsg']) ? $params['errmsg'] : '可能没发布小程序';
                    $this->error($msg);
                } else {
                    $qrcodeurl = UPLOAD_PATH.'allimg/20220515';
                    tp_mkdir($qrcodeurl);
                    $qrcodeurl = $qrcodeurl."/bd-{$appid}.png";
                    if (@file_put_contents($qrcodeurl, base64_decode($params['data']['base64_str']))){
                        $qrcodeurl = $this->root_dir.'/'.$qrcodeurl;
                        $this->success('生成小程序码成功', null, ['qrcodeurl'=>$qrcodeurl]);
                    } else {
                        $this->error('生成小程序码失败');
                    }
                }
            }
        }
        $this->error('不存在信息');
    }

    /**
     * 微信公众号配置
     * @return [type] [description]
     */
    public function conf_wechat()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            $appid = trim($post['appid']);
            $appsecret = trim($post['appsecret']);

            if (!empty($appid) || !empty($appsecret)) {
                if (empty($appid)) {
                    $this->error('AppID不能为空！');
                }
                if (empty($appsecret)) {
                    $this->error('AppSecret不能为空！');
                }

                /*$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
                $response = httpRequest($url);
                $params = json_decode($response, true);
                if (!isset($params['access_token'])) {
                    $wechat_code = config('error_code.wechat');
                    $msg = !empty($wechat_code[$params['errcode']]) ? $wechat_code[$params['errcode']] : $params['errmsg'];
                    $this->error($msg);
                }*/
            }

            $data = [
                'appid'  => $appid,
                'appsecret' => $appsecret,
            ];

            // 兼容老数据的功能，同步保存一份到以前配置里
            $wechat_login_config = $this->usersConfig['wechat_login_config'];
            $login_config = unserialize($wechat_login_config);
            $data['wechat_name'] = !empty($login_config['wechat_name']) ? trim($login_config['wechat_name']) : '';
            $data['wechat_pic'] = !empty($login_config['wechat_pic']) ? trim($login_config['wechat_pic']) : '';
            getUsersConfigData('wechat', ['wechat_login_config'=>serialize($data)]);

            tpSetting('OpenMinicode', ['conf_wechat' => json_encode($data)], $this->main_lang);

            $this->success("操作成功");
        }

        $data = tpSetting("OpenMinicode.conf_wechat", [], $this->main_lang);
        if (empty($data)) {
            $wechat_login_config = getUsersConfigData('wechat.wechat_login_config');
            $login_config = unserialize($wechat_login_config);
            if (!empty($login_config)) {
                $data = [];
                $data['appid'] = !empty($login_config['appid']) ? trim($login_config['appid']) : '';
                $data['appsecret'] = !empty($login_config['appsecret']) ? trim($login_config['appsecret']) : '';
                $data['wechat_name'] = !empty($login_config['wechat_name']) ? trim($login_config['wechat_name']) : '';
                $data['wechat_pic'] = !empty($login_config['wechat_pic']) ? trim($login_config['wechat_pic']) : '';
                tpSetting('OpenMinicode', ['conf_wechat' => json_encode($data)], $this->main_lang);
            }
        } else {
            $data = json_decode($data, true);
        }
        $this->assign('data', $data);
        /*微站点配置*/
        $login = !empty($this->usersConfig['wechat_login_config']) ? unserialize($this->usersConfig['wechat_login_config']) : [];
        $this->assign('login', $login);
        /* END */

        /*验证IP是否加入白名单中*/
        $ipTips = '';
        if (!empty($data['appid']) && !empty($data['appsecret'])) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$data['appid']}&secret={$data['appsecret']}";
            $res = json_decode(httpRequest($url), true);
            if (!empty($res['errcode'])) {
                if ($res['errcode'] == 40164) {
                    preg_match_all('#(\d{1,3}\.){3}\d{1,3}#i', $res['errmsg'], $matches);
                    $ip = !empty($matches[0][0]) ? $matches[0][0] : '';
                    $ipTips = "<font color='red'>请将IP：{$ip} 加入微信公众号的IP白名单里，具体点击<a href=\"JavaScript:void(0);\" onclick=\"click_to_eyou_1575506523('https://www.eyoucms.com/plus/view.php?aid=9432&origin_eycms=1','IP白名单配置教程')\">查看教程</a>！</font>";
                } else {
                    $ipTips = "<font color='red'>{$res['errmsg']}</font>";
                }
            }
        }
        $this->assign('ipTips', $ipTips);
        /*--end*/

        return $this->fetch();
    }

    // 头条(抖音)配置
    public function conf_toutiao()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');
            $salt = !empty($post['salt']) ? trim($post['salt']) : trim($post['toutiao_salt']);
            $appid = !empty($post['appid']) ? trim($post['appid']) : trim($post['toutiao_appid']);
            $secret = !empty($post['appsecret']) ? trim($post['appsecret']) : trim($post['toutiao_appsecret']);
            if (empty($appid)) $this->error('AppID不能为空！');
            if (empty($secret)) $this->error('AppSecret不能为空！');
            $params = getToutiaoAccessToken($appid, $secret, $salt, true);
            if (empty($params['access_token'])) $this->error('AppID或AppSecret不正确！');
            $this->success("操作成功");
        }
        $this->error('操作失败，请刷新重试');
    }

    /**
     * 启用、关闭 开放API
     * @return [type] [description]
     */
    public function ajax_save_apiopen()
    {
        if (IS_AJAX_POST) {
            $apiopen = input('post.open_value/d');
            $data = tpSetting("OpenMinicode.conf", [], $this->main_lang);
            $data = json_decode($data, true);
            empty($data) && $data = [];
            $data['apiopen'] = $apiopen;
            tpSetting('OpenMinicode', ['conf' => json_encode($data)], $this->main_lang);
            $this->success('操作成功');
        }
    }

}