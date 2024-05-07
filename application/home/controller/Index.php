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

namespace app\home\controller;

use think\Db;
use app\user\logic\PayLogic;

class Index extends Base
{
    public function _initialize() {
        parent::_initialize();
        $this->wechat_return();
        $this->alipay_return();
        $this->Express100();
        $this->ey_agent();
        $this->tikTok_return();
        $this->baiduPay_return();
    }

    public function index()
    {
        $preview_templet = input('param.templet/s');
        /*处理多语言首页链接最后带斜杆，进行301跳转*/
        $lang = input('param.lang/s');
        if (preg_match("/\?lang=".$this->home_lang."\/$/i", $this->request->url(true)) && $lang == $this->home_lang.'/') {
            $langurl = $this->request->url(true);
            $langurl = rtrim($langurl, '/');
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: '.$langurl);
            exit;
        }
        /*end*/

        /*首页焦点*/
        $m = input('param.m/s');
        if (empty($m)) {
            $this->request->get(['m'=>'Index']);
        }
        /*end*/

        $filename = 'index.html';
        $seo_pseudo = config('ey_config.seo_pseudo');
        if (!isset($_GET['clear']) && file_exists($filename) && 2 == $seo_pseudo) {
            if ((isMobile() && !file_exists('./template/'.TPL_THEME.'mobile')) || !isMobile()) {
                header('HTTP/1.1 301 Moved Permanently');
                header('Location:'.$filename);
                exit;
            }
        }
        else if (!isset($_GET['clear']) && 2 == $seo_pseudo && !empty($this->eyou['global']['seo_showmod']) && !empty($this->eyou['global']['seo_html_position'])) {
            $seo_html_position_arr = explode('/', $this->eyou['global']['seo_html_position']);
            $filename = end($seo_html_position_arr);
            if (file_exists($filename)) {
                $html = @file_get_contents($filename);
                if (!empty($html)) {
                    echo $html;
                    exit; 
                }
            }
        }

        $result['pageurl'] = $this->request->url(true); // 获取当前页面URL
        $result['pageurl_m'] = pc_to_mobile_url($result['pageurl']); // 获取当前页面对应的移动端URL

        if (!config('city_switch_on')) {
            if (3 != $seo_pseudo && stristr($result['pageurl'], '/Index/index')) {
                abort(404, '页面不存在');
            } else if (3 == $seo_pseudo && preg_match('/m=([^&]+)&c=([^&]+)&a=([^&]+)/i', $result['pageurl'])) {
                abort(404, '页面不存在');
            }
        }

        // 移动端域名
        $result['mobile_domain'] = '';
        if (!empty($this->eyou['global']['web_mobile_domain_open']) && !empty($this->eyou['global']['web_mobile_domain'])) {
            $result['mobile_domain'] = $this->eyou['global']['web_mobile_domain'] . '.' . $this->request->rootDomain(); 
        }
        $eyou = array(
            'field' => $result,
        );
        $this->eyou = array_merge($this->eyou, $eyou);
        $this->assign('eyou', $this->eyou);
        
        /*模板文件*/
        $viewfile = 'index';
        if (config('city_switch_on') && !empty($this->home_site)) { // 多站点内置模板文件名
            $viewfilepath = TEMPLATE_PATH.$this->theme_style_path.DS.$this->home_site;
            $viewfilepath2 = TEMPLATE_PATH.$this->theme_style_path.DS.'city'.DS.$this->home_site;
            if (!empty($this->eyou['global']['site_template'])) {
                if (file_exists($viewfilepath2)) {
                    $viewfile = "city/{$this->home_site}/{$viewfile}";
                } else if (file_exists($viewfilepath)) {
                    $viewfile = "{$this->home_site}/{$viewfile}";
                }
            }
        } else if (config('lang_switch_on') && !empty($this->home_lang)) { // 多语言内置模板文件名
            $viewfilepath = TEMPLATE_PATH.$this->theme_style_path.DS.$viewfile."_{$this->home_lang}.".$this->view_suffix;
            if (file_exists($viewfilepath)) {
                $viewfile .= "_{$this->home_lang}";
            }
        }
        
        $html = '';
        // 招聘插件内置代码 start 
        if (file_exists('./weapp/Recruits/model/RecruitsModel.php')) {
            $recruitsModel = new \weapp\Recruits\model\RecruitsModel;
            $recruitsViewfile = $recruitsModel->setHome($this->eyou);
            if (!empty($recruitsViewfile)) {
                $this->assign('eyou', $this->eyou);
                $html = $this->fetch($recruitsViewfile);
                return $html;
            }
        }
        // 招聘插件内置代码 end

        // 预览主页
        if (!empty($preview_templet)) {
            $viewfile = preg_replace('/\.(.*)$/i', '', $preview_templet);
        }
        $html = $this->fetch(":{$viewfile}");

        return $html;
    }

    /**
     * 微信支付回调
     */
    private function wechat_return()
    {
        // 获取回调的参数
        $inputXml = file_get_contents("php://input");
        if (!empty($inputXml)) {
            // 解析参数
            $jsonXml = json_encode(simplexml_load_string($inputXml, 'SimpleXMLElement', LIBXML_NOCDATA));
            // 转换数组
            $jsonArr = json_decode($jsonXml, true);
            // 是否与支付成功
            if (!empty($jsonArr) && 'SUCCESS' == $jsonArr['result_code'] && 'SUCCESS' == $jsonArr['return_code']) {
                // 解析判断参数是否为微信支付
                $attach = explode('|,|', $jsonArr['attach']);
                if (!empty($attach) && 'wechat' == $attach[0] && 'is_notify' == $attach[1] && !empty($attach[2])) {
                    if (!empty($attach[4]) && !empty($attach[5]) && 'applets' == $attach[5]) {
                        $config = model('ShopPublicHandle')->getSpecifyAppletsConfig($attach[4], 'weixin');
                    } else if (empty($attach[4]) && !empty($attach[5]) && 'applets' == $attach[5] && !empty($attach[6])) {
                        $config = model('ShopPublicHandle')->getSpecifyAppletsConfig(0, '', $attach[6]);
                    } else {
                        $config = Db::name('pay_api_config')->where('pay_mark', $attach[0])->value('pay_info');
                        $config = !empty($config) ? unserialize($config) : [];
                    }
                    if (!empty($config) && !empty($config['appid']) || stristr($inputXml, "[{$config['appid']}]")) {
                        model('ShopPublicHandle')->getWeChatPayResult($attach[3], $jsonArr, $attach[2], $config, true, true);
                    }
                }
            }
        }
    }

    /**
     * 抖音支付回调
     */
    private function tikTok_return()
    {
        $inputData = file_get_contents("php://input");
        if (!empty($inputData)) {
            $inputData = json_decode($inputData, true);
            $inputData['msg'] = !empty($inputData['msg']) ? json_decode($inputData['msg'], true) : [];
            if (!empty($inputData['msg'])) model('TikTok')->tikTokAppletsPayDealWith($inputData, true);
        }
    }

    /**
     * 支付宝支付回调
     */
    private function alipay_return()
    {
        $param = input('param.');
        if (isset($param['transaction_type']) && isset($param['is_notify']) && isset($param['person_pay']) && isset($param['users_id'])) {
            // 跳转处理回调信息
            $personPayWeapp = model('ShopPublicHandle')->getWeappInfo('PersonPay');
            if (!empty($personPayWeapp['status'])) {
                $personPayLogic = new \weapp\PersonPay\logic\PersonPayLogic($personPayWeapp['config']);
                $personPayLogic->asyncNotifyHandle($param);
            }
        } else if (isset($param['transaction_type']) && isset($param['is_notify'])) {
            // 跳转处理回调信息
            $pay_logic = new PayLogic();
            $pay_logic->alipay_return();
        }
    }

    /**
     * 百度支付回调
     */
    private function baiduPay_return()
    {
        $param = input('param.');
        if (!empty($param['returnData'])) {
            $param['returnData'] = json_decode(htmlspecialchars_decode($param['returnData']), true);
            if (empty($param['tpOrderId']) || empty($param['status']) || 2 !== intval($param['status'])) return false;
            if (empty($param['returnData']['payType']) || 'baiduPay' !== trim($param['returnData']['payType'])) return false;
            if (empty($param['returnData']['usersID']) || empty($param['returnData']['orderCode']) || empty($param['returnData']['table'])) return false;
            // 查询配置信息
            $config = model('ShopPublicHandle')->getSpecifyAppletsConfig($param['returnData']['appletsID'], 'baidu');
            $baiduPayModel = new \app\common\model\BaiduPay($config);
            $baiduPayModel->baiDuAppletsPayDealWith($param, true, $param['returnData']['table']);
        }
    }

    /**
     * 快递100返回时，重定向关闭父级弹框
     */
    private function Express100()
    {
        $coname = input('param.coname/s', '');
        $m = input('param.m/s', '');
        if (!empty($coname) || 'user' == $m) {
            if (isWeixin()) {
                $this->redirect(url('user/Shop/shop_centre'));
                exit;
            }else{
                $this->redirect(url('api/Rewrite/close_parent_layer'));
                exit;
            }
        }
    }

    /**
     * 无效链接跳转404
     */
    private function ey_agent()
    {
        $ey_agent = input('param.ey_agent/d', 0);
        if (!IS_AJAX && !empty($ey_agent) && 'home' == MODULE_NAME) {
            abort(404, '页面不存在');
        }
    }
}