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

namespace think\template\taglib\eyou;

/**
 * 上传背景图片
 */
class TagUibackground extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 上传图片
     * @author wengxianhu by 2018-4-20
     */
    public function getUibackground($e_id = '', $e_page = '', $e_img = '')
    {
        if (empty($e_id) || empty($e_page)) {
            echo '标签uibackground报错：缺少属性 e-id | e-page | e-img 。';
            return false;
        }

        $result = false;
        $inc = get_ui_inc_params($e_page);
        $inckey = self::$home_lang."_background_{$e_id}";
        if (empty($inc[$inckey])) {
            $inckey = "background_{$e_id}"; // 兼容v1.2.1之前的数据
        }

        $info = false;
        if ($inc && !empty($inc[$inckey])) {
            $data = json_decode($inc[$inckey], true);
            $info = $data['info'];
        }

        $value = $this->root_dir.'/template/'.THEME_STYLE_PATH.'/'.$e_img;
        if (is_array($info) && !empty($info)) {
            $value = isset($info['value']) ? trim($info['value']) : $value;
            $value = handle_subdir_pic($value, 'img');
        }

        return $value;
    }
}