<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
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
use think\Request;

/**
 * 业务逻辑
 */
class ApiLogic extends Model
{
    private $request = null; // 当前Request对象实例
    private $current_lang = 'cn'; // 当前多语言标识
    public $taglib = ['apiType','apiChannel','apiList','apiArclist','apiArcview','apiPrenext','apiGuestbookform','apiAdv','apiAd','apiFlink','apiGlobal','apiCollect'];

    /**
     * 析构函数
     */
    function  __construct() {
        null === $this->request && $this->request = Request::instance();
        $this->current_lang = get_current_lang();
    }

    public function taglibData($users_id=0)
    {
        $result = [];
        $params = input('param.');
        $aid = input('param.aid/d');
        $typeid = input('param.typeid/d');
        $channelid = input('param.channelid/d');
        foreach ($params as $key => $val) {
            $key = preg_replace('/_(\d+)$/i', '', $key);
            if (!in_array($key, $this->taglib)) { // 排除不是标签的参数
                continue;
            }
            $val = htmlspecialchars_decode($val);
            parse_str($val, $parse);
            $ekey = isset($parse['ekey']) ? intval($parse['ekey']) : 1; // 多个相同标签对应的每份不同数据
            $aid = !empty($parse['aid']) ? intval($parse['aid']) : $aid;
            $typeid = !empty($parse['typeid']) ? intval($parse['typeid']) : $typeid;

            if ($key == 'apiType') { // 单个分类标签
                $type = !empty($parse['type']) ? $parse['type'] : 'self';
                $addfields = !empty($parse['addfields']) ? $parse['addfields'] : '';
                $infolen = !empty($parse['infolen']) ? intval($parse['infolen']) : '';
                $tagType = new \think\template\taglib\api\TagType;
                $result[$key][$ekey] = $tagType->getType($typeid, $type, $addfields, $infolen);
            }
            else if ($key == 'apiChannel') { // 分类列表标签
                $type = !empty($parse['type']) ? $parse['type'] : 'top';
                $currentstyle = !empty($parse['currentstyle']) ? $parse['currentstyle'] : '';
                $showalltext = !empty($parse['showalltext']) ? $parse['showalltext'] : 'off';
                if (!empty($parse['limit'])) {
                    $limit = !empty($parse['limit']) ? $parse['limit'] : 10;
                } else {
                    $limit = !empty($parse['row']) ? intval($parse['row']) : 10;
                }
                $tagChannel = new \think\template\taglib\api\TagChannel;
                $result[$key][$ekey] = $tagChannel->getChannel($typeid, $type, $currentstyle, $showalltext, $channelid);
                if (!empty($result[$key][$ekey]['data'])) {
                    /*指定获取的条数*/
                    $limitarr = explode(',', $limit);
                    $offset = (1 == count($limitarr)) ? 0 : $limitarr[0];
                    $length = (1 == count($limitarr)) ? $limitarr[0] : end($limitarr);
                    $data = $result[$key][$ekey]['data'];
                    if ('off' == $showalltext) {
                        $data = array_slice($data, $offset, $length, true);
                        $data = array_merge($data);
                    } else {
                        $firstData = current($data);
                        $data = array_slice($data, $offset + 1, $length, true);
                        empty($data) && $data = [];
                        $data = array_merge([$firstData], $data);
                    }
                    /*end*/
                    $result[$key][$ekey]['data'] = $data;
                }
            }
            else if ($key == 'apiList') { // 文档分页列表标签
                $parse['typeid'] = $typeid;
                $parse['channelid'] = $channelid;
                $tagList = new \think\template\taglib\api\TagList;
                $result[$key][$ekey] = $tagList->getList($parse);
            }
            else if ($key == 'apiArclist') { // 文档不分页列表标签
                $parse['typeid'] = $typeid;
                if (!empty($parse['limit'])) {
                    $limit = !empty($parse['limit']) ? $parse['limit'] : 10;
                } else {
                    $limit = !empty($parse['row']) ? intval($parse['row']) : 10;
                }
                $tagArclist = new \think\template\taglib\api\TagArclist;
                $result[$key][$ekey] = $tagArclist->getArclist($parse, $limit);
            }
            else if ($key == 'apiArcview') { // 文档详情页
                $aid = !empty($parse['aid']) ? intval($parse['aid']) : $aid;
                $typeid = !empty($parse['typeid']) ? intval($parse['typeid']) : $typeid;
                $titlelen = !empty($parse['titlelen']) ? intval($parse['titlelen']) : 100;
                $addfields = !empty($parse['addfields']) ? $parse['addfields'] : '';
                $tagArcview = new \think\template\taglib\api\TagArcview;
                $result[$key][$ekey] = $tagArcview->getArcview($aid, $typeid, $addfields, $titlelen);
            }
            else if ($key == 'apiPrenext') { // 上下篇
                $aid = !empty($parse['aid']) ? intval($parse['aid']) : $aid;
                $typeid = !empty($parse['typeid']) ? intval($parse['typeid']) : $typeid;
                $get = !empty($parse['get']) ? $parse['get'] : 'all';
                $titlelen = !empty($parse['titlelen']) ? intval($parse['titlelen']) : 100;
                $tagPrenext = new \think\template\taglib\api\TagPrenext;
                $result[$key][$ekey] = $tagPrenext->getPrenext($aid, $typeid, $get, $titlelen);
            }
            else if ($key == 'apiGuestbookform') { // 留言表单
                $typeid = !empty($parse['typeid']) ? intval($parse['typeid']) : $typeid;
                $tagGuestbookform = new \think\template\taglib\api\TagGuestbookform;
                $result[$key][$ekey] = $tagGuestbookform->getGuestbookform($typeid);
            }
            else if ($key == 'apiAdv') { // 广告位置
                $pid = !empty($parse['pid']) ? intval($parse['pid']) : 0;
                $orderby = !empty($parse['orderby']) ? intval($parse['orderby']) : '';
                if (!empty($parse['limit'])) {
                    $limit = !empty($parse['limit']) ? $parse['limit'] : 10;
                } else {
                    $limit = !empty($parse['row']) ? intval($parse['row']) : 10;
                }
                $tagAdv = new \think\template\taglib\api\TagAdv;
                $result[$key][$ekey] = $tagAdv->getAdv($pid, $orderby, $limit);
            }
            else if ($key == 'apiAd') { // 单个广告
                $aid = !empty($parse['aid']) ? intval($parse['aid']) : 0;
                $tagAd = new \think\template\taglib\api\TagAd;
                $result[$key][$ekey] = $tagAd->getAd($aid);
            }
            else if ($key == 'apiFlink') { // 友情链接
                $type = !empty($parse['type']) ? $parse['type'] : 'text';
                $groupid = !empty($parse['groupid']) ? intval($parse['groupid']) : 1;
                if (!empty($parse['limit'])) {
                    $limit = !empty($parse['limit']) ? $parse['limit'] : 10;
                } else {
                    $limit = !empty($parse['row']) ? intval($parse['row']) : 10;
                }
                $titlelen = !empty($parse['titlelen']) ? intval($parse['titlelen']) : 100;
                $tagFlink = new \think\template\taglib\api\TagFlink;
                $result[$key][$ekey] = $tagFlink->getFlink($type, $limit, $groupid, $titlelen);
            }
            else if ($key == 'apiGlobal') { // 全局变量\自定义变量
                $name = !empty($parse['name']) ? $parse['name'] : '';
                $tagGlobal = new \think\template\taglib\api\TagGlobal;
                $result[$key][$ekey] = $tagGlobal->getGlobal($name);
            }
            else if ($key == 'apiCollect') { // 全局变量\自定义变量
                $type = !empty($parse['type']) ? $parse['type'] : 'default';
                $tagCollect = new \think\template\taglib\api\TagCollect;
                $result[$key][$ekey] = $tagCollect->getCollect($aid,$type,$users_id);
            }
        }

        return $result;
    }
    
    /**
     * 接口转化
     */
    public function get_api_url($query_str)
    {
        $apiUrl = 'aHR0cHM6Ly9zZXJ2aWNlLmV5eXN6LmNu';
        return base64_decode($apiUrl).$query_str;
    }

    /**
     * 获取远程最新的小程序参数配置
     */
    public function synRemoteSetting()
    {
        $diyminiproMallSettingModel = new \weapp\DiyminiproMall\model\DiyminiproMallSettingModel;
        $data = $diyminiproMallSettingModel->getSettingValue('setting');
        if (!empty($data)) {
            $vaules = [];
            $vaules['appId'] = $data['appId'];
            $query_str = http_build_query($vaules);
            $url = "/index.php?m=api&c=MiniproClient&a=minipro&".$query_str;
            $response = httpRequest($this->get_api_url($url));
            $params = array();
            $params = json_decode($response, true);
            if (!empty($params) && $params['errcode'] == 0) {
                $params['errmsg'] = array_merge($data, $params['errmsg']);
                $bool = $diyminiproMallSettingModel->setSettingValue('setting', $params['errmsg']);
                if ($bool) {
                    $data = $diyminiproMallSettingModel->getSettingValue('setting');
                } else {
                    $data = $params['errmsg'];
                }
                // 同步远程上线模板ID的状态到本地模板
                Db::name('weapp_diyminipro_mall')->where([
                    'mini_id'=>intval($data['online_mini_id']),
                ])->update([
                    'status'    => 5,
                    'update_time'   => getTime(),
                ]);
            }
        
            if (empty($data['authorizerStatus'])) {
                session('show_qrcode_total_1589417597', 0);
            }

            if (isset($data['miniproStatus']) && 4 <= $data['miniproStatus']) {
                // 清除没用的模板
                $max_mini_id = Db::name('weapp_diyminipro_mall')->where(['status'=>['egt', 4]])->max('mini_id');
                Db::name('weapp_diyminipro_mall')->where('mini_id','lt',$max_mini_id)->where('mini_id','neq',intval($data['online_mini_id']))->delete();
            }
        } else {
            // 清除没用的模板
            $max_mini_id = Db::name('weapp_diyminipro_mall')->where(['status'=>['egt', 4]])->max('mini_id');
            Db::name('weapp_diyminipro_mall')->where(['mini_id'=>['lt', $max_mini_id]])->delete();
        }

        return $data;
    }

    // 验证微信商户配置的正确性
    public function GetWechatAppletsPay($appid = '', $mch_id = '', $apikey = '')
    {
        // 当前时间戳
        $time = time();

        // 当前时间戳 + OpenID 经 MD5加密
        $nonceStr = $out_trade_no = md5($time);

        // 调用支付接口参数
        $params = [
            'appid'            => $appid,
            'attach'           => "微信小程序支付",
            'body'             => "商品支付",
            'mch_id'           => $mch_id,
            'nonce_str'        => $nonceStr,
            'notify_url'       => url('api/Api/wxpay_notify', [], true, true, 1, 2),
            'out_trade_no'     => $out_trade_no,
            'spbill_create_ip' => $this->clientIP(),
            'total_fee'        => 1,
            'trade_type'       => 'JSAPI'
        ];

        // 生成参数签名
        $params['sign'] = $this->ParamsSign($params, $apikey);

        // 生成参数XML格式
        $ParamsXml = $this->ParamsXml($params);

        // 调用接口返回数据
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $result = httpRequest($url, 'POST', $ParamsXml);

        // 解析XML格式
        $ResultData = $this->ResultXml($result);

        // 数据返回
        if ($ResultData['return_code'] == 'SUCCESS' && $ResultData['return_msg'] == 'OK') {
            return ['code'=>1, 'msg'=>'验证通过'];
        } else if ($ResultData['return_code'] == 'FAIL') {
            return ['code'=>0, 'msg'=>'支付商户号或支付密钥不正确！'];
        }
    }

    /**
     * 客户端IP
     */
    private function clientIP()
    {
        $ip = request()->ip();
        if (preg_match('/^((?:(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1 -9]?\d))))$/', $ip))
            return $ip;
        else
            return '';
    }

    private function ParamsSign($values, $apikey)
    {
        //签名步骤一：按字典序排序参数
        ksort($values);
        $string = $this->ParamsUrl($values);
        //签名步骤二：在string后加入KEY
        $string = $string . '&key=' . $apikey;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    private function ParamsUrl($values)
    {
        $Url = '';
        foreach ($values as $k => $v) {
            if ($k != 'sign' && $v != '' && !is_array($v)) {
                $Url .= $k . '=' . $v . '&';
            }
        }
        return trim($Url, '&');
    }

    private function ParamsXml($values)
    {
        if (!is_array($values)
            || count($values) <= 0
        ) {
            return false;
        }

        $xml = "<xml>";
        foreach ($values as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    private function ResultXml($xml)
    {
        // 禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }
}
