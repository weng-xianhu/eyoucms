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

namespace think\template\taglib\api;

/**
 * 全局变量
 */
class TagGlobal extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取全局变量
     * @author wengxianhu by 2018-4-20
     */
    public function getGlobal($name = '')
    {
        $result = [];
        $globalConfig = tpCache('global', [], self::$home_lang);
        if (!empty($name)) {
            $name = str_replace('，', ',', $name);
            $names = explode(',', $name);
            foreach ($names as $key => $value) {
                $result[$value] = $globalConfig[$value];
            }
        } else {
            $result = [];
            $varslist = ['web_name','web_logo','web_basehost','web_title','web_keywords','web_description','web_copyright','web_recordnum','web_garecordnum'];
            foreach ($globalConfig as $key => $value) {
                if (in_array($key, $varslist) || preg_match('/^web_attr_(\d+)$/i', $key)) {
                    $result[$key] = $value;
                }
            }
        }

        foreach ($result as $key => $value) {
            switch ($key) {
                case 'web_basehost':
                    if (empty($value)) {
                        $value = self::$request->domain().$this->root_dir;
                    }
                    break;

                default:
                    $value = handle_subdir_pic($value, 'html'); // 支持子目录
                    $value = handle_subdir_pic($value);
                    break;
            }
            $result[$key] = htmlspecialchars_decode($value);
        }
        
        return [
            'data'=> !empty($result) ? $result : false,
        ];
    }
}