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

namespace app\admin\controller;

use think\Config;

class Vertify extends Base
{

    public function _initialize() {
        parent::_initialize();
    }

    /**
     * 验证码管理
     */
    public function index()
    {
        // 获取插件数据
        $row = tpSetting('system.system_vertify');
        if (IS_POST) {
            // 获取post参数
            $inc_type = input('inc_type/s', 'admin_login');
            $param = $this->request->only('captcha');
            $config = json_decode($row, true);

            if ('default' == $inc_type) {
                if (isset($config[$inc_type])) {
                    $config['captcha'][$inc_type] = array_merge($config['captcha'][$inc_type], $param['captcha'][$inc_type]);
                } else {
                    $config['captcha'][$inc_type] = $param['captcha'][$inc_type];
                }
            } else {
                $config['captcha'][$inc_type]['is_on'] = $param['captcha'][$inc_type]['is_on'];
                if (isset($config['captcha'][$inc_type]['config'])) {
                    $config['captcha'][$inc_type]['config'] = array_merge($config['captcha'][$inc_type]['config'], $param['captcha'][$inc_type]['config']);
                } else {
                    $config['captcha'][$inc_type]['config'] = $param['captcha'][$inc_type]['config'];
                }
            }
            // 转json赋值
            $row = json_encode($config);
            $r = tpSetting('system',['system_vertify'=>$row]);

            if ($r !== false) {
                adminLog('编辑验证码配置'); // 写入操作日志
                $this->success("操作成功!", url('Vertify/index', ['inc_type'=>$inc_type]));
            }
            $this->error("操作失败!");
        }

        $inc_type = input('param.inc_type/s', 'admin_login');
        $inc_type = preg_replace('/([^\w\-]+)/i', '', $inc_type);

        // 获取配置JSON信息转数组
        $config = json_decode($row, true);
        $baseConfig = Config::get("captcha");

        if ('default' == $inc_type) {
            $row = isset($config['captcha']) ? $config['captcha'] : $baseConfig;
        } else {
            if (isset($config['captcha'][$inc_type])) {
                $row = $config['captcha'][$inc_type];
            } else {
                $baseConfig[$inc_type]['config'] = !empty($config['captcha']['default']) ? $config['captcha']['default'] : $baseConfig['default'];
                $row = $baseConfig[$inc_type];
            }
        }

        $this->assign('row', $row);
        $this->assign('inc_type', $inc_type);
        return $this->fetch($inc_type);
    }
}