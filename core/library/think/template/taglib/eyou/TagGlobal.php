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

use \think\Request;
use \think\Config;

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
        if (empty($name)) {
            return '标签global报错：缺少属性 name 。';
        }

        $param = explode('|', $name);
        $name = trim($param[0], '$');
        $value = '';

        $uiset = I('param.uiset/s', 'off');
        $uiset = trim($uiset, '/');

        /*PC端与手机端的变量名自适应，可彼此通用*/
        if (in_array($name, ['web_thirdcode_pc','web_thirdcode_wap'])) { // 第三方代码
            $name = 'web_thirdcode_' . (isMobile() ? 'wap' : 'pc');
        }
        /*--end*/

        $globalTpCache = tpCache('global');
        if ($globalTpCache) {
            $value = \think\Coding::setcr($name, $globalTpCache);

            switch ($name) {
                // case 'web_basehost':
                case 'web_cmsurl':
                    {
                        $request = Request::instance();

                        // if(empty($value)) {
                        //     if (1 == $globalTpCache['seo_pseudo']) {
                        //         $value = '/';
                        //     }
                        // } && $value = url('home/Index/index');

                        /*URL全局参数（比如：可视化uiset、多模板v、多语言lang）*/
                        $urlParam = $request->param();
                        foreach ($urlParam as $key => $val) {
                            if (in_array($key, Config::get('global.parse_url_param'))) {
                                $urlParam[$key] = trim($val, '/');
                            } else {
                                unset($urlParam[$key]);
                            }
                        }
                        /*--end*/

                        if ('on' == $uiset) {
                            $value = url('home/Index/index', $urlParam);
                            if (!stristr($value, '?')) {
                                $value .= '?';
                            } else {
                                $value .= '&';
                            }
                            $value .= http_build_query(['tmp'=>'']);
                        } else {
                            $value = $request->domain().$this->root_dir;
                            if (1 == $globalTpCache['seo_pseudo']) {
                                if (!empty($urlParam)) {
                                    /*是否隐藏小尾巴 index.php*/
                                    $seo_inlet = config('ey_config.seo_inlet');
                                    if (0 == intval($seo_inlet)) {
                                        $value .= '/index.php';
                                    } else {
                                        $value .= '/';
                                    }
                                    /*--end*/
                                    if (!stristr($value, '?')) {
                                        $value .= '?';
                                    } else {
                                        $value .= '&';
                                    }
                                    $value .= http_build_query($urlParam);
                                }
                            } else {
                                if (get_default_lang() != get_home_lang()) {
                                    $value = rtrim(url('home/Index/index'), '/');
                                }
                            }
                        }
                    }
                    break;
                
                case 'web_recordnum':
                    if (!empty($value)) {
                        $value = '<a href="http://www.beian.miit.gov.cn/" rel="nofollow" target="_blank">'.$value.'</a>';
                    }
                    break;

                case 'web_templets_pc':
                case 'web_templets_m':
                    $value = $this->root_dir.$value;
                    break;

                case 'web_thirdcode_pc':
                case 'web_thirdcode_wap':
                    $value = '';
                    break;

                default:
                    /*支持子目录*/
                    $value = handle_subdir_pic($value, 'html');
                    $value = handle_subdir_pic($value);
                    /*--end*/
                    break;
            }

            foreach ($param as $key => $val) {
                if ($key == 0) continue;
                $value = $val($value);
            }
            // $value = str_replace('"', '\"', $value);

/*            switch ($name) {
                case 'web_thirdcode_wap':
                case 'web_thirdcode_pc':
                    $value = htmlspecialchars_decode($value);
                    break;
                
                default:
                    # code...
                    break;
            }*/
            $value = htmlspecialchars_decode($value);
        }
        
        return $value;
    }
}