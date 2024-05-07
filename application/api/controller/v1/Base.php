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

namespace app\api\controller\v1;

use think\Db;
use app\api\logic\v1\ApiLogic;
use app\api\logic\v1\WechatLogic;

class Base extends \app\api\controller\Base
{
    public $appId = 0;

    /**
     * 实例化业务逻辑对象
     */
    public $apiLogic;
    public $wechatLogic;

    /**
     * 系统配置
     */
    public $globalConfig = [];
    public $usersConfig = [];
    public $php_servicemeal = 0;
    /**
     * 初始化操作
     */
    public function _initialize() {
        parent::_initialize();
        $dataConf = tpSetting("OpenMinicode.conf", [], $this->main_lang);
        $dataConf = json_decode($dataConf, true);
        if (!empty($dataConf['apiopen'])) {
            $this->error('API接口已关闭不可用！');
        } else {
            if (!empty($dataConf['apiverify'])) {
                $times = getTime();
                $apikey_token = input('param.apikey_token/s');
                $arr = explode('-', $apikey_token);
                $request_token = !empty($arr[0]) ? $arr[0] : '';
                $request_time = !empty($arr[1]) ? intval($arr[1]) : 0;
                $new_request_token = md5($request_time.md5($dataConf['apikey']));
                $request_token_arr = [$new_request_token];
                
                /* 修改接口密钥后，小程序发布审核需要一定的时间，旧密钥在7天内继续可用，延期将不可用 start */
                $apikey_uptime = !empty($dataConf['apikey_uptime']) ? intval($dataConf['apikey_uptime']) : 0; // 新密钥修改时间
                $apikey_uptime += (7 * 86400);
                if (!empty($dataConf['old_apikey']) && $apikey_uptime > getTime()) {
                    $old_request_token = md5($request_time.md5($dataConf['old_apikey']));
                    array_push($request_token_arr, $old_request_token);
                }
                /* end */

                if (!in_array($request_token, $request_token_arr)) {
                    $this->error('API请求验证不通过！');
                } else if ($times - $request_time > 300) { // 每个接口时效是5分钟
                    $this->error('当前API请求已过期！');
                }
            }
        }

        $this->appId = input('param.appId/s');
        $this->apiLogic = new ApiLogic;
        $this->wechatLogic = new WechatLogic;
        $this->globalConfig = tpCache('global');
        $this->php_servicemeal = $this->globalConfig['php_servicemeal'];
        $this->get_name();
        $this->usersConfig = getUsersConfigData('all');
    }

    /**
     * 获取当前用户信息
     * @param bool $is_force 是否返回报错提示，还是直接返回值
     * @return UserModel|bool|null
     * @throws BaseException
     * @throws \think\exception\DbException
     */
    protected function getUser($is_force = true)
    {
        $token = $this->request->param('token');
        if (empty($token)) {
            $is_force && $this->error('缺少必要的参数：token', null, ['code'=>-1]);
            return false;
        }

        $users = model('v1.User')->getUser();
        if (empty($users)) {
            $is_force && $this->error('没有找到用户信息', null, ['code'=>-1]);
            return false;
        }

        return $users;
    }

    /**
     * 返回操作成功（附带一些后台配置等数据）
     * @param array $data
     * @param string|array $msg
     * @return array
     */
    protected function renderSuccess($data = [], $msg = 'success', $url = null)
    {
        if (!empty($url) && is_array($data)) {
            $data['url'] = $url;
        }

        $usersConf = [];
        // 开启商城中心才生效
        if (!empty($this->usersConfig['shop_open'])) {
            $users = $this->getUser(false);
            if (!empty($users['users_id'])){
                $data['cart_total_num'] = Db::name('shop_cart')->where(['users_id' => $users['users_id']])->sum('product_num');
            }else{
                $data['cart_total_num'] = 0;
            }
        }
        $usersConf['shop_open'] = empty($this->usersConfig['shop_open']) ? 0 : intval($this->usersConfig['shop_open']);
        $usersConf['dealer_open'] = empty($this->usersConfig['dealer_open']) ? 0 : intval($this->usersConfig['dealer_open']);
        $data['usersConf'] = $usersConf;

        return $this->result($data, 1, $msg);
    }
    //获取信息
    private function get_name(){
        if ($this->php_servicemeal <= 1){
            $controller = $this->request->param("c");
            $action = $this->request->param("a");
            $arr = [
                "djEuVXNlcnNAb3JkZXJfbGlzdHM=",
                "djEuVXNlcnNAaGFuZGxlT3JkZXJTZXJ2aWNlQWN0aW9u",
                "djEuVXNlcnNASGFuZGxlVXNlck1vbmV5QWN0aW9u"
            ];
            if (in_array(base64_encode($controller."@".$action),$arr)){
                $this->error(base64_decode('6K+35YWI5byA5ZCv5ZWG5Z+O5Lit5b+D'), null, ['code'=>0]);
                return false;
            }
        }
    }
    /**
     * 返回操作失败
     * @param array $data
     * @param string|array $msg
     * @return array
     */
    protected function renderError($msg = '', $url = null, $data = [], $wait = 1, array $header = [], $target = '_self')
    {
        if (!empty($url) && is_array($data)) {
            $data['url'] = $url;
        }

        return $this->result($data, 0, $msg);
    }

    /**
     * 返回操作成功
     * @param array $data
     * @param string|array $msg
     * @return array
     */
    protected function success($msg = '', $url = null, $data = [], $wait = 1, array $header = [], $target = '_self')
    {
        if (!empty($url) && is_array($data)) {
            $data['url'] = $url;
        }

        return $this->result($data, 1, $msg);
    }

    /**
     * 返回操作失败
     * @param array $data
     * @param string|array $msg
     * @return array
     */
    protected function error($msg = '', $url = null, $data = [], $wait = 1, array $header = [], $target = '_self')
    {
        if (!empty($url) && is_array($data)) {
            $data['url'] = $url;
        }

        return $this->result($data, 0, $msg);
    }
}