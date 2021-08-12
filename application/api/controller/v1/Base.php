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

namespace app\api\controller\v1;

use think\Db;
use app\api\logic\v1\ApiLogic;

class Base extends \app\api\controller\Base
{
    public $appId = 0;

    /**
     * 实例化业务逻辑对象
     */
    public $apiLogic;

    /**
     * 初始化操作
     */
    public function _initialize() {
        parent::_initialize();
        $this->appId = input('param.appId/s');
        $this->apiLogic = new ApiLogic;
    }

    /**
     * 获取当前用户信息
     * @param bool $is_force
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

        $users = model('v1.User')->getUser($token);
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
        // 会员登录之后的配置
        // $usersConf = [];
        // $usersConf['shop_open'] = (int)getUsersConfigData('shop.shop_open');
        // $data['usersConf'] = $usersConf;
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