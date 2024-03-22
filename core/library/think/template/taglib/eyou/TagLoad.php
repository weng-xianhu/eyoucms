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


/**
 * 资源文件加载
 */
class TagLoad extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 资源文件加载
     * @author wengxianhu by 2018-4-20
     */
    public function getLoad($file = '', $ver = 'on')
    {
        if (empty($file)) {
            return '标签load报错：缺少属性 file 或 href 。';
        }

        $parseStr = '';

        // 文件方式导入
        $array = explode(',', $file);
        foreach ($array as $val) {
            $type = preg_replace('/^(.*)\.([a-z]+)([^a-z]*)(.*)$/i', '${2}', strtolower($val));
            $version = ''; // '?v='.getCmsVersion();
            switch ($type) {
                case 'js':
                    if ($ver == 'on') {
                        $parseStr .= static_version($val);
                    } else {
                        $val = get_absolute_url($val);
                        $parseStr =<<<EOF
<script language="javascript" type="text/javascript" src="{$val}{$version}"></script>

EOF;
                    }
                    break;
                case 'css':
                    if ($ver == 'on') {
                        $parseStr .= static_version($val);
                    } else {
                        $val = get_absolute_url($val);
                        $parseStr =<<<EOF
<link href="{$val}{$version}" rel="stylesheet" media="screen" type="text/css" />

EOF;
                    }
                    break;
                case 'php':
                    $parseStr .= '<?php include "' . $val . '"; ?>';
                    break;
            }
        }

        return $parseStr;
    }
}