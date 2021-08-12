<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 陈风任 <491085389@qq.com>
 * Date: 2019-07-08
 */

namespace app\admin\logic;

use think\Model;
use think\Db;

/**
 * 逻辑定义
 * 用于产品规格逻辑功能处理
 * @package admin\Logic
 */
class ProductSpecLogic extends Model
{
    /**
     * 初始化操作
     */
    public function _initialize() {
        parent::_initialize();
    }

    // 数组降级返回
    public function ArrayDowngrade($GetData = array())
    {
        $ReturnData = [];
        foreach ($GetData as $value){
            foreach ($value as $vv){
                $ReturnData[] = $vv;
            }
        }
        return $ReturnData;
    }

    // 一维数组转为二级数组，携带子数组
    public function GetPresetData($GetData = array())
    {
        $GetData = group_same_key($GetData, 'preset_mark_id');
        $ReturnData  = [];
        foreach ($GetData as $key => $value) {
            $ReturnData[] = [
                'preset_id'      => $value[0]['preset_id'],
                'preset_mark_id' => $value[0]['preset_mark_id'],
                'preset_name'    => $value[0]['preset_name'],
                'spec_sync'      => $value[0]['spec_sync'],
                'sort_order'     => $value[0]['sort_order'],
                'preset_value'   => $value,
            ];
        }
        return $ReturnData;
    }

    // 删除规格名称\规格值条件
    public function GetDeleteSpecWhere($GetData = array())
    {
        $where = [];
        // 删除规格值
        if (isset($GetData['preset_id']) && !empty($GetData['preset_id'])) {
            $where = [
                'lang'      => get_admin_lang(),
                'preset_id' => $GetData['preset_id'],
            ];
        }
        // 删除规格名称，包扣规格名称下的所有规格值
        if (isset($GetData['preset_mark_id']) && !empty($GetData['preset_mark_id'])) {
            $where = [
                'lang'      => get_admin_lang(),
                'preset_mark_id' => ['IN', $GetData['preset_mark_id']],
            ];
        }
        return $where;
    }

    // 拼装预设值下拉选项
    public function GetPresetValueOption($GetData = array(), $mark_id = null, $aid = null, $type = null)
    {   
        $result = $PresetValueOption = '';
        if (!empty($mark_id)) {
            $where = [
                'lang' => get_admin_lang(),
                'aid'  => $aid,
                'spec_is_select' => 0, // 未选中的
                'spec_mark_id' => ['IN',$mark_id],
            ];
            $GetData = Db::name('product_spec_data')->where($where)->field('spec_value_id,spec_name,spec_value')->select();
            $PresetValueOption .= "<option value='0'>选择规格值</option>";
            foreach($GetData as $value){
                $PresetValueOption .= "<option value='{$value['spec_value_id']}'>{$value['spec_value']}</option>";
            }
            if (2 == $type) {
                $result = [
                    'PresetName' => $GetData[0]['spec_name'],
                    'PresetValueOption' => $PresetValueOption,
                ];
            }else{
                $result = $PresetValueOption;
            }
        }else{
            $result .= "<option value='0'>选择规格值</option>";
            if(!empty($GetData)){
                foreach($GetData as $value){
                    $result .= "<option value='{$value['preset_id']}'>{$value['preset_value']}</option>";
                }
            }
        }
        return $result;
    }

    // 预设规格标记ID数组
    public function GetPresetMarkIdArray($GetData = array())
    {
        $result = '';
        if (isset($GetData['aid']) && !empty($GetData['aid'])) {
            $spec_mark_id_arr = $GetData['spec_mark_id_arr'];
            if (empty($spec_mark_id_arr)) {
                $result = $GetData['spec_mark_id'];
            }else{
                $return_spec_mark_id = $spec_mark_id_arr.','.$GetData['spec_mark_id'];
                if (3 < count(explode(',', $return_spec_mark_id))) {
                    $result = false;
                }else{
                    $result = $return_spec_mark_id;
                }
            }
        }else{
            $preset_mark_id_arr = $GetData['preset_mark_id_arr'];
            if (empty($preset_mark_id_arr)) {
                $result = $GetData['preset_mark_id'];
            }else{
                $return_preset_mark_id = $preset_mark_id_arr.','.$GetData['preset_mark_id'];
                if (3 < count(explode(',', $return_preset_mark_id))) {
                    $result = false;
                }else{
                    $result = $return_preset_mark_id;
                }
            }
        }
        return $result;
    }

    // 拼装规格名称下拉选项
    public function GetPresetNameOption($preset_mark_id_arr = null)
    {
        $MarkIdWhere = explode(',', $preset_mark_id_arr);
        $where = [
            'lang' => get_admin_lang(),
            'preset_mark_id' => ['NOT IN', $MarkIdWhere],
        ];
        $PresetName  = Db::name('product_spec_preset')->where($where)->field('preset_id,preset_mark_id,preset_name')->group('preset_mark_id')->order('preset_mark_id desc')->select();
        if($PresetName){
            // 拼装下拉选项
            foreach($PresetName as $value){
                $Option .= "<option value='{$value['preset_mark_id']}'>{$value['preset_name']}</option>";
            }
        }
        $result = [
            'MarkId' => implode(',', $MarkIdWhere),
            'Option' => $Option,
        ];
        return $result;
    }

    public function GetPresetSpecAssembly($GetData = array())
    {
        $preset_mark_id = $GetData['preset_mark_id'];
        if (isset($GetData['aid']) && !empty($GetData['aid'])) {
            $PresetMarkIdArray = $GetData['spec_mark_id_arr'];
        }else{
            $PresetMarkIdArray = $GetData['preset_mark_id_arr'];
        }
        
        $i = 0;
        if (!empty($PresetMarkIdArray) && !empty($preset_mark_id)) {
            $mark_id_arr = explode(',', $PresetMarkIdArray);
            foreach ($mark_id_arr as $key => $value) {
                if ($value == $preset_mark_id) {
                    unset($mark_id_arr[$key]);
                    $i++;
                }
            }
            if (!empty($i)) {
                $PresetMarkIdArray = implode(',', $mark_id_arr);
            }
        }

        $HtmlTable = '';
        if (!empty($i) && !empty($preset_mark_id)) {
            $spec_arr_ses = session('spec_arr');
            unset($spec_arr_ses[$preset_mark_id]);
            session('spec_arr', $spec_arr_ses);
            $HtmlTable = $this->SpecAssembly($spec_arr_ses);
        }

        $return = [
            'HtmlTable' => $HtmlTable,
            'PresetMarkIdArray' => $PresetMarkIdArray,
        ];        
        return $return;
    }

    public function GetResetPresetNameOption($GetData = array())
    {
        $del_mark_id = $GetData['del_mark_id'];
        $return = [
            'Option' => '',
            'MarkId' => '',
        ];
        if (isset($del_mark_id) && !empty($del_mark_id) && !isset($GetData['del_preset_id'])) {
            $spec_arr_ses = session('spec_arr');
            unset($spec_arr_ses[$del_mark_id]);
            session('spec_arr',$spec_arr_ses);

            /*在数据库中更新所清除的规格名称，更新为未选中状态*/
            if (isset($GetData['aid']) && !empty($GetData['aid'])) {
                $WhereData = [
                    'aid'          => $GetData['aid'],
                    'spec_mark_id' => $del_mark_id,
                ];
                $Update = [
                    'spec_is_select' => 0,
                    'update_time'    => getTime(),
                ];
                Db::name('product_spec_data')->where($WhereData)->update($Update);
            }
            /* END */

            $PresetMarkIdArray = $GetData['preset_mark_id_arr'];
            if (empty($PresetMarkIdArray)) $PresetMarkIdArray = $GetData['spec_mark_id_arr'];

            if (!empty($PresetMarkIdArray)) {
                $mark_id_arr = explode(',', $PresetMarkIdArray);
                foreach ($mark_id_arr as $key => $value) {
                    if ($value == $del_mark_id) {
                        unset($mark_id_arr[$key]);
                    }
                }
                $PresetMarkIdArray = implode(',', $mark_id_arr);
            }
            $ReturnData = $this->GetPresetNameOption($PresetMarkIdArray);
            $return = $ReturnData;
        }
        return $return;
    }

    public function ClearSpecValueID($GetData = array())
    {
        // 清除单个规格值
        $del_mark_id = $GetData['del_mark_id'];
        $return = [
            'Option' => '',
        ];
        if (isset($del_mark_id) && !empty($del_mark_id) && isset($GetData['del_preset_id'])) {
            $spec_arr_ses = session('spec_arr');
            foreach ($spec_arr_ses[$del_mark_id] as $key => $value) {
                if ($value == $GetData['del_preset_id']) {
                    unset($spec_arr_ses[$del_mark_id][$key]);
                }
            }
            if (empty($spec_arr_ses[$del_mark_id])) {
                unset($spec_arr_ses[$del_mark_id]);
            }
            session('spec_arr',$spec_arr_ses);
            $preset_id = $spec_arr_ses[$del_mark_id];
            if (empty($GetData['aid'])) {
                $WhereNew = [
                    'preset_mark_id' => $del_mark_id,
                    'preset_id' => ['NOT IN',$preset_id],
                ];
                // 加载选中的规格值
                $PresetValue = Db::name('product_spec_preset')->where($WhereNew)->select();
                $return['Option'] = $this->GetPresetValueOption($PresetValue);
            }else{
                // 更新规格值
                $Where = [
                    'aid'           => $GetData['aid'],
                    'spec_mark_id'  => $del_mark_id,
                    'spec_value_id' => $GetData['del_preset_id'],
                    'spec_is_select'=> 1,
                ];
                Db::name('product_spec_data')->where($Where)->update(['spec_is_select'=>0, 'update_time'=>getTime()]);

                // 拼装新的下拉框数据
                $WhereNew = [
                    'aid'           => $GetData['aid'],
                    'spec_mark_id'  => $del_mark_id,
                    'spec_is_select' => 0,
                ];
                $Data = Db::name('product_spec_data')->where($WhereNew)->field('spec_value_id,spec_value')->select();
                $return['Option'] .= "<option value='0'>选择规格值</option>";
                foreach($Data as $value){
                    $return['Option'] .= "<option value='{$value['spec_value_id']}'>{$value['spec_value']}</option>";
                }
            }
        }
        return $return;
    }

    public function GetSessionPostArrayMerge($GetData = array())
    {
        $preset_id = $GetData['preset_id'];
        $preset_mark_id = $GetData['preset_mark_id'];
        $spec_arr_ses = session('spec_arr');

        if (!empty($preset_mark_id) && !empty($preset_id)) {
            $spec_arr_new = [
                $preset_mark_id => [
                    $preset_id,
                ],
            ];
            foreach ($spec_arr_new as $key => $value) {
                if (isset($spec_arr_ses[$key])) {
                    if (in_array($value[0], $spec_arr_ses[$key])) {
                        $msg['error'] = '已有相同规格值，无需重复添加！';
                        return $msg;
                    }else{
                        // session存在数据，但选中参数在数据中还没有，合并数据
                        $spec_arr_ses[$key] = array_merge($spec_arr_ses[$key], $value);
                    }
                }else{
                    if (!empty($spec_arr_ses)) {
                        // 不存在
                        $spec_arr_ses = $spec_arr_ses + $spec_arr_new;
                    }else{
                        $spec_arr_ses = $spec_arr_new;
                    }
                }
            }
        }
        session('spec_arr',$spec_arr_ses);
        return $spec_arr_ses;
    }

    public function SpecAssembly($GetData = array())
    {
        if (empty($GetData)) return ' ';

        // 参数处理
        foreach ($GetData as $k => $v) {
            $spec_arr_sort[$k] = count($v);
        }
        asort($spec_arr_sort);        
        foreach ($spec_arr_sort as $key =>$val) {
            $spec_arr2[$key] = $GetData[$key];
        }
        // 获取KEY值
        $clo_name  = array_keys($spec_arr2);
        // 数据组合
        $spec_arr2 = $this->DataCombination($spec_arr2);
        // 查询预设名称
        $spec = Db::name('product_spec_preset')->group('preset_mark_id')->getField('preset_mark_id,preset_name');
        // 查询预设值
        $specItem = Db::name('product_spec_preset')->getField('preset_id,preset_value,preset_mark_id');
        // 组合HTML
        $ReturnHtml  = "<table class='table table-bordered' id='spec_input_tab' border='1' cellpadding='10' cellspacing='10'><thead><tr>";
        // 显示第一行的数据
        foreach ($clo_name as $k => $v) {
            $ReturnHtml .= "<td><b><input type='hidden' class='prese_name_input_{$v}' name='preset_mark_id[$v][preset_name]' value='{$spec[$v]}'><span class='preset_name_span_{$v}'>{$spec[$v]}</span></b></td>";
        }
        $ReturnHtml .= "<td><b>价格 <a href=\"javascript:void(0);\" onclick=\"BulkSetPrice(this);\">批量设置 </a></b></td>";
        $ReturnHtml .= "<td><b>库存 <a href=\"javascript:void(0);\" onclick=\"BulkSetStock(this);\">批量设置 </a></b></td>";
        $ReturnHtml .= "<td><b>销量</b></td>";
        $ReturnHtml .= "</tr></thead><tbody>";

       // 显示第二行开始
       foreach ($spec_arr2 as $k => $v) {
            $ReturnHtml .= "<tr id='preset_value_tr_{$v[0]}'>";
            $preset_key_name = array();
            foreach($v as $k2 => $v2) {
                $ReturnHtml .= "<td><input type='hidden' class='preset_value_input_{$v2}' name='preset_id[$v2][preset_value]' value='{$specItem[$v2]['preset_value']}'><span class='preset_value_span_{$v2}'>{$specItem[$v2]['preset_value']}</span></td>";                
                $preset_key_name[$v2] = $spec[$specItem[$v2]['preset_mark_id']].':'.$specItem[$v2]['preset_value'];
            }
            ksort($preset_key_name);
            $preset_key = implode('_', array_keys($preset_key_name));

            $ReturnHtml .="<td><input class='users_price' name='preset_price[$preset_key][users_price]' value='0' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\");UpPrice(this);' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")'/></td>";

            $ReturnHtml .="<td><input class='stock_count' name='preset_stock[$preset_key][stock_count]' value='0' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\");UpStock(this);' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' data-old_stock='0'/></td>";

            $ReturnHtml .="<td><input type='text' name='spec_sales[$spec_key][spec_sales_num]' value='0' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")'></td>";

            $ReturnHtml .="</tr>";
        }
        $ReturnHtml .= "</tbody>";
        $ReturnHtml .= "</table>";
        // $ReturnHtml .= '批量设置：<a href="javascript:void(0);" onclick="BulkSetPrice(this);" >价格</a> <a href="javascript:void(0);" onclick="BulkSetStock(this);" >库存</a>';

        return $ReturnHtml;
    }

    public function SpecAssemblyEdit($GetData = array(), $aid = null)
    {
        if (empty($GetData)) return ' ';
        // 参数处理
        foreach ($GetData as $k => $v) {
            $spec_arr_sort[$k] = count($v);
        }
        asort($spec_arr_sort);
        foreach ($spec_arr_sort as $key =>$val) {
            $spec_arr2[$key] = $GetData[$key];
        }
        // 排序
        $order = 'spec_value_id asc, spec_id asc, spec_mark_id asc';
        // 获取KEY值
        $clo_name  = array_keys($spec_arr2);
        // 数据组合
        $spec_arr2 = $this->DataCombination($spec_arr2);
        // 查询规格名称
        $spec = Db::name('product_spec_data')->where('aid',$aid)->order($order)->group('spec_mark_id')->getField('spec_mark_id, spec_name');
        // 查询规格值
        $specItem = Db::name('product_spec_data')->where('aid',$aid)->order($order)->getField('spec_value_id, spec_value, spec_mark_id');
        // 查询规格价格
        $specPrice = Db::name('product_spec_value')->where('aid', $aid)->order('spec_price asc')->getField('spec_value_id, spec_price, spec_stock, spec_sales_num');
        // 组合HTML
        $ReturnHtml  = "<table class='table table-bordered' id='spec_input_tab' border='1' cellpadding='10' cellspacing='10'><thead><tr>";
        // 显示第一行的数据
        foreach ($clo_name as $k => $v) {
            $ReturnHtml .= "<td><b><input type='hidden' class='spec_name_input_{$v}' name='spec_mark_id[$v][spec_name]' value='{$spec[$v]}'><span class='spec_name_span_{$v}'>{$spec[$v]}</span></b></td>";
        }
        $ReturnHtml .= "<td><b>价格 <a href=\"javascript:void(0);\" onclick=\"BulkSetPrice(this);\">批量设置 </a></b></td>";
        $ReturnHtml .= "<td><b>库存 <a href=\"javascript:void(0);\" onclick=\"BulkSetStock(this);\" >批量设置 </a></b></td>";
        $ReturnHtml .= "<td><b>销量</b></td>";
        $ReturnHtml .= "</tr></thead><tbody>";

       // 显示第二行开始
       foreach ($spec_arr2 as $k => $v) {
            $ReturnHtml .= "<tr>";
            $spec_key_name = array();
            foreach($v as $k2 => $v2) {
                $ReturnHtml .= "<td><input type='hidden' class='spec_value_input_{$v2}' name='spec_value_id[$v2][spec_value]' value='{$specItem[$v2]['spec_value']}'><span class='spec_value_span_{$v2}'>{$specItem[$v2]['spec_value']}</span></td>";
                $spec_key_name[$v2] = $spec[$specItem[$v2]['spec_mark_id']].':'.$specItem[$v2]['spec_value'];
            }   
            ksort($spec_key_name);
            $spec_key = implode('_', array_keys($spec_key_name));
            
            $ReturnHtml .="<td><input class='users_price' name='spec_price[$spec_key][users_price]' value='{$specPrice[$spec_key]['spec_price']}' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\");UpPrice(this);'/></td>";

            $ReturnHtml .="<td><input class='stock_count' name='spec_stock[$spec_key][stock_count]' value='{$specPrice[$spec_key]['spec_stock']}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\");UpStock(this);' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' data-old_stock='{$specPrice[$spec_key]['spec_stock']}'/></td>";

            $ReturnHtml .="<td><input type='text' name='spec_sales[$spec_key][spec_sales_num]' value='{$specPrice[$spec_key]['spec_sales_num']}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\")' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")'></td>";

            $ReturnHtml .="</tr>";
        }
        $ReturnHtml .= "</tbody>";
        $ReturnHtml .= "</table>";
        // $ReturnHtml .= '批量设置：<a href="javascript:void(0);" onclick="BulkSetPrice(this);" >批量设置</a> <a href="javascript:void(0);" onclick="BulkSetStock(this);" >批量设置</a>';

        return $ReturnHtml;
    }

    /**
     * 2020/12/18 大黄 秒杀 多规格设置
     */
    public function SharpSpecAssemblyEdit($GetData = array(), $aid = null)
    {
        if (empty($GetData)) return ' ';
        // 参数处理
        foreach ($GetData as $k => $v) {
            $spec_arr_sort[$k] = count($v);
        }
        asort($spec_arr_sort);
        foreach ($spec_arr_sort as $key =>$val) {
            $spec_arr2[$key] = $GetData[$key];
        }
        // 排序
        $order = 'spec_value_id asc, spec_id asc, spec_mark_id asc';
        // 获取KEY值
        $clo_name  = array_keys($spec_arr2);
        // 数据组合
        $spec_arr2 = $this->DataCombination($spec_arr2);
        // 查询规格名称
        $spec = Db::name('product_spec_data')->where('aid',$aid)->order($order)->group('spec_mark_id')->getField('spec_mark_id, spec_name');
        // 查询规格值
        $specItem = Db::name('product_spec_data')->where('aid',$aid)->order($order)->getField('spec_value_id, spec_value, spec_mark_id');
        // 查询规格价格
        $specPrice = Db::name('product_spec_value')->where('aid', $aid)->order('spec_price asc')->getField('spec_value_id, spec_price, spec_stock, seckill_price, seckill_stock');
        // 组合HTML
        $ReturnHtml  = "<table class='table table-bordered' id='spec_input_tab' border='1' cellpadding='10' cellspacing='10'><thead><tr>";
        // 显示第一行的数据
        foreach ($clo_name as $k => $v) {
            $ReturnHtml .= "<td><b><input type='hidden' class='spec_name_input_{$v}' name='spec_mark_id[$v][spec_name]' value='{$spec[$v]}'><span class='spec_name_span_{$v}'>{$spec[$v]}</span></b></td>";
        }
        $ReturnHtml .= "<td><b>价格 </b></td>";
        $ReturnHtml .= "<td><b>库存 </b></td>";
        $ReturnHtml .= "<td><b><em class='red'>*</em>秒杀价格  <a href=\"javascript:void(0);\" onclick=\"BulkSetPrice(this);\">批量设置 </a></b></td>";
        $ReturnHtml .= "<td><b><em class='red'>*</em>秒杀库存  <a href=\"javascript:void(0);\" onclick=\"BulkSetStock(this);\" >批量设置 </a></b></td>";
        $ReturnHtml .= "</tr></thead><tbody>";

        // 显示第二行开始
        foreach ($spec_arr2 as $k => $v) {
            $ReturnHtml .= "<tr>";
            $spec_key_name = array();
            foreach($v as $k2 => $v2) {
                $ReturnHtml .= "<td><input type='hidden' class='spec_value_input_{$v2}' name='spec_value_id[$v2][spec_value]' value='{$specItem[$v2]['spec_value']}'><span class='spec_value_span_{$v2}'>{$specItem[$v2]['spec_value']}</span></td>";
                $spec_key_name[$v2] = $spec[$specItem[$v2]['spec_mark_id']].':'.$specItem[$v2]['spec_value'];
            }
            ksort($spec_key_name);
            $spec_key = implode('_', array_keys($spec_key_name));

            $ReturnHtml .="<td>{$specPrice[$spec_key]['spec_price']}<input type='hidden' class='users_price' name='spec_price[$spec_key][users_price]' value='{$specPrice[$spec_key]['spec_price']}' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\");'/></td>";

            $ReturnHtml .="<td>{$specPrice[$spec_key]['spec_stock']}<input type='hidden' class='stock_count' name='spec_stock[$spec_key][stock_count]' value='{$specPrice[$spec_key]['spec_stock']}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\");' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' data-old_stock='{$specPrice[$spec_key]['spec_stock']}'/></td>";

            if ($specPrice[$spec_key]['seckill_price'] > 0){
                $seckill_price = $specPrice[$spec_key]['seckill_price'];
            }else{
                $seckill_price = '';
            }
            $ReturnHtml .="<td><input class='spec_seckill_price' name='seckill_price[$spec_key][spec_seckill_price]' value='{$seckill_price}' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\");'/></td>";
            if ($specPrice[$spec_key]['seckill_stock'] > 0){
                $seckill_stock = $specPrice[$spec_key]['seckill_stock'];
            }else{
                $seckill_stock = '';
            }
            $ReturnHtml .="<td><input class='spec_seckill_stock' name='seckill_stock[$spec_key][spec_seckill_stock]' value='{$seckill_stock}' onkeyup='this.value=this.value.replace(/[^\d.]/g,\"\");' onpaste='this.value=this.value.replace(/[^\d.]/g,\"\")' data-old_stock='{$specPrice[$spec_key]['seckill_stock']}'/></td>";

            $ReturnHtml .="</tr>";
        }
        $ReturnHtml .= "</tbody>";
        $ReturnHtml .= "</table>";
        // $ReturnHtml .= '批量设置：<a href="javascript:void(0);" onclick="BulkSetPrice(this);" >批量设置</a> <a href="javascript:void(0);" onclick="BulkSetStock(this);" >批量设置</a>';

        return $ReturnHtml;
    }

    // 数据组合
    private function DataCombination()
    {
        $data   = func_get_args();
        $data   = current($data);
        $result = array();
        $arr1   = array_shift($data);
        foreach($arr1 as $key => $item) {
            $result[] = array($item);
        }       

        foreach($data as $key => $item) {                                
            $result = $this->DataCombinationArray($result, $item);
        }
        return $result;
    }

    private function DataCombinationArray($arr1, $arr2)
    {
        $result = array();
        foreach ($arr1 as $item1) {
            foreach ($arr2 as $item2) {
                $temp     = $item1;
                $temp[]   = $item2;
                $result[] = $temp;
            }
        }
        return $result;
    }
}
