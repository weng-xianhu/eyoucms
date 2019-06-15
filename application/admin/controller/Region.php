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

use think\Page;

class Region extends Base
{

    /**
    * 获取子类列表
    */  
    public function ajax_get_region($pid = 0, $text = ''){
        $data = model('Region')->getList($pid);
        $html = "<option value=''>--".urldecode($text)."--</option>";
        foreach($data as $key=>$val){
            $html.="<option value='".$val['id']."'>".$val['name']."</option>";
        }

        return $html;
    }
}