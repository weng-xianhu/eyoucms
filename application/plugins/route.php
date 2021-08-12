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

$plugins_route = [];

/*引入全部插件的路由配置*/
$weappRow = \think\Db::name('weapp')->field('code')->where([
    'status'    => 1,
])->cache(true, null, "weapp")->select();
foreach ($weappRow as $key => $val) {
    $file = WEAPP_DIR_NAME.DS.$val['code'].DS.'route.php';
    if (file_exists($file)) {
        $route_value = include_once $file;
        if (!empty($route_value) && is_array($route_value)) {
            $plugins_route = array_merge($route_value, $plugins_route);
        }
    }
}
/*--end*/

$route = array(
    '__pattern__' => array(),
    '__alias__' => array(),
    '__domain__' => array(),
);

$route = array_merge($route, $plugins_route);

return $route;
