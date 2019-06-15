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

namespace app\admin\validate;
use think\Validate;

class ConfigAttribute extends Validate
{
    // 验证规则
    protected $rule = array(
        array('inc_type','checkIncType'),
        array('attr_name','require','变量名称不能为空'),
        array('attr_input_type', 'require', '请选择表单类型'),
    );
      
    /**
     *  自定义函数 判断 用户选择 从下面的列表中选择 可选值列表：不能为空
     * @param type $attr_values
     * @return boolean
     */
    protected function checkIncType($inc_type, $rule)
    {
        if(empty($inc_type) || I('param.inc_type/s', '') == '')         
            return '缺少变量前缀';
        else
            return true;
    }  
      
    /**
     *  自定义函数 判断 用户选择 从下面的列表中选择 可选值列表：不能为空
     * @param type $attr_values
     * @return boolean
     */
    protected function checkAttrValues($attr_values,$rule)
    {
        if(empty($attr_values) && I('param.attr_input_type/d') == '1')        
            return '可选值列表不能为空';
        else
            return true;
    }    
}