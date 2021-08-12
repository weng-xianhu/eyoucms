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
 * Date: 2019-7-9
 */
namespace app\admin\model;

use think\Model;
use think\Config;
use think\Db;
use app\admin\logic\ProductSpecLogic; // 用于产品规格逻辑功能处理

/**
 * 产品规格预设模型
 */
class ProductSpecData extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        $this->admin_lang = get_admin_lang();
    }

    public function PresetSpecAddData($Data = array())
    {
        if (!empty($Data['aid'])) {
            $where = [
                'lang' => get_admin_lang(),
                'preset_mark_id' => ['IN', $Data['spec_mark_id']],
            ];
            $PresetData = Db::name('product_spec_preset')->where($where)->order('preset_mark_id desc')->select();

            $AddData = [];
            foreach ($PresetData as $key => $value) {
                $Where = [
                    'aid'           => $Data['aid'],
                    'lang'          => $this->admin_lang,
                    'spec_mark_id'  => $value['preset_mark_id'],
                    'spec_value_id' => $value['preset_id'],
                ];
                $count =  Db::name('product_spec_data')->where($Where)->count();
                if (empty($count)) {
                    $AddData[] = [
                        'aid'            => $Data['aid'],
                        'spec_mark_id'   => $value['preset_mark_id'],
                        'spec_name'      => $value['preset_name'],
                        'spec_value_id'  => $value['preset_id'],
                        'spec_value'     => $value['preset_value'],
                        'spec_is_select' => 0,
                        'lang'           => $this->admin_lang,
                        'add_time'       => getTime(),
                        'update_time'    => getTime(),
                    ];
                }
            }
            if (!empty($AddData)) $this->saveAll($AddData);
        }
    }

    public function ProducSpecNameEditSave($post = array())
    {
        if (!empty($post['aid']) && !empty($post['spec_value_id']) && !empty($post['spec_mark_id'])) {
            $spec_value_ids = array_keys($post['spec_value_id']);
            $spec_mark_ids  = array_keys($post['spec_mark_id']);
            // 查询条件
            $where = [
                'aid'  => $post['aid'],
                'lang' => get_admin_lang(),
                'spec_mark_id' => ['IN', $spec_mark_ids],
            ];
            
            // 查询规格数据
            $SpecData = Db::name('product_spec_data')->where($where)->select();

            // 删除当前产品下的所有规格数据
            $this->where($where)->delete();

            // 添加数组拼装
            $time = getTime();
            $UpData = [];
            foreach ($SpecData as $key => $value) {
                $UpData[$key] = [
                    'aid'           => $value['aid'],
                    'spec_mark_id'  => $value['spec_mark_id'],
                    'spec_value_id' => $value['spec_value_id'],
                    'spec_name'     => $value['spec_name'],
                    'spec_value'    => $value['spec_value'],
                    'spec_is_select'=> 0,
                    'lang'          => get_admin_lang(),
                    'add_time'      => $time,
                    'update_time'   => $time,
                ];
                if (in_array($value['spec_value_id'], $spec_value_ids)) {
                    $UpData[$key]['spec_is_select'] = 1;
                }
            }
            Db::name('product_spec_data')->insertAll($UpData);
        }
    }

    // 编辑产品时，规格原数据处理
    public function GetProductSpecData($id)
    {
        $assign_data = [];
        // 商城配置
        $shopConfig = getUsersConfigData('shop');
        $assign_data['shopConfig'] = $shopConfig;
        // 已选规格处理
        if (isset($shopConfig['shop_open_spec']) && 1 == $shopConfig['shop_open_spec']) {
            session('spec_arr',null);
            $SpecWhere = [
                'aid' => $id,
                'lang' => $this->admin_lang,
                'spec_is_select' => 1,// 已选中的
            ];
            $order = 'spec_value_id asc, spec_id asc';
            $product_spec_data = Db::name('product_spec_data')->where($SpecWhere)->order($order)->select();
            // 参数预定义
            $assign_data['SpecSelectName'] = $assign_data['HtmlTable'] = $assign_data['spec_mark_id_arr'] = '';
            if (!empty($product_spec_data)) {
                $ProductSpecLogic = new ProductSpecLogic;
                $spec_arr_new = group_same_key($product_spec_data, 'spec_mark_id');
                foreach ($spec_arr_new as $key => $value) {
                    $spec_mark_id_arr[] = $key;
                    $SpecSelectName[$key]  = '<div class="prset-box" id="spec_'.$key.'">';
                    $SpecSelectName[$key] .= '<div id="div_'.$key.'">';
                    $SpecSelectName[$key] .= '<div><span class="preset-bt"><span class="spec_name_span_'.$key.'">'.$value[0]['spec_name'].'</span><em data-name="'.$value[0]['spec_name'].'" data-mark_id="'.$key.'" onclick="DelDiv(this)"><i class="fa fa-times-circle" title="关闭"></i></em></span>';
                    
                    $SpecSelectName[$key] .= '<span id="SelectEd_'.$key.'">';
                    for ($i=0; $i<count($value); $i++) {
                        $spec_arr_new[$key][$i] = $value[$i]['spec_value_id'];
                        $SpecSelectName[$key] .= '<span class="preset-bt2" id="preset-bt2_'.$value[$i]['spec_id'].'"><span class="spec_value_span_'.$value[$i]['spec_value_id'].'">'.$value[$i]['spec_value'].'</span><em data-value="'.$value[$i]['spec_value'].'" data-mark_id="'.$value[$i]['spec_mark_id'].'" data-preset_id="'.$value[$i]['spec_value_id'].'" onclick="DelValue(this)"><i class="fa fa-times-circle" title="关闭"></i></em> &nbsp; </span>';
                    }

                    $SpecSelectName[$key] .= '</span> &nbsp; &nbsp;';
                    $SpecSelectName[$key] .= '<select name="spec_value" id="spec_value_'.$key.'" onchange="AppEndPreset(this,'.$key.')">';
                    $SpecSelectName[$key] .= $ProductSpecLogic->GetPresetValueOption('', $key, $id, 1);
                    $SpecSelectName[$key] .= '</select> &nbsp; <span title="同步规格数据" data-mark_id="'.$key.'" data-name="'.$value[0]['spec_name'].'" onclick="RefreshSpecValue(this);"><i class="fa fa-refresh"></i></span>';
                    $SpecSelectName[$key] .= '</div></div><br/></div>';
                }

                session('spec_arr',$spec_arr_new);
                $assign_data['SpecSelectName']   = $SpecSelectName;
                $assign_data['HtmlTable']        = $ProductSpecLogic->SpecAssemblyEdit($spec_arr_new, $id);
                $assign_data['spec_mark_id_arr'] = implode(',', $spec_mark_id_arr);
            }

            // 预设值名称
            $where = ['lang' => $this->admin_lang];
            if (!empty($spec_mark_id_arr)) $where['preset_mark_id'] = ['NOT IN',$spec_mark_id_arr];
            $assign_data['preset_value'] = Db::name('product_spec_preset')->where($where)->field('preset_id,preset_mark_id,preset_name')->group('preset_mark_id')->order('preset_mark_id desc')->select();
        }

        return $assign_data;
    }

    /**
     * 2020/12/18 大黄 秒杀 编辑秒杀商品，规格原数据处理
     */
    public function GetSharpProductSpecData($id)
    {
        $assign_data = [];
        // 商城配置
        $shopConfig = getUsersConfigData('shop');
        $assign_data['shopConfig'] = $shopConfig;
        // 已选规格处理
        if (isset($shopConfig['shop_open_spec']) && 1 == $shopConfig['shop_open_spec']) {
            session('spec_arr',null);
            $SpecWhere = [
                'aid' => $id,
                'lang' => $this->admin_lang,
                'spec_is_select' => 1,// 已选中的
            ];
            $order = 'spec_value_id asc, spec_id asc';
            $product_spec_data = Db::name('product_spec_data')->where($SpecWhere)->order($order)->select();
            // 参数预定义
            $assign_data['SpecSelectName'] = $assign_data['HtmlTable'] = $assign_data['spec_mark_id_arr'] = '';
            if (!empty($product_spec_data)) {
                $ProductSpecLogic = new ProductSpecLogic;
                $spec_arr_new = group_same_key($product_spec_data, 'spec_mark_id');
                foreach ($spec_arr_new as $key => $value) {
                    $spec_mark_id_arr[] = $key;
                    $SpecSelectName[$key]  = '<div class="prset-box" id="spec_'.$key.'">';
                    $SpecSelectName[$key] .= '<div id="div_'.$key.'">';
                    $SpecSelectName[$key] .= '<div><span class="preset-bt"><span class="spec_name_span_'.$key.'">'.$value[0]['spec_name'].'</span><em data-name="'.$value[0]['spec_name'].'" data-mark_id="'.$key.'" onclick="DelDiv(this)"><i class="fa fa-times-circle" title="关闭"></i></em></span>';

                    $SpecSelectName[$key] .= '<span id="SelectEd_'.$key.'">';
                    for ($i=0; $i<count($value); $i++) {
                        $spec_arr_new[$key][$i] = $value[$i]['spec_value_id'];
                        $SpecSelectName[$key] .= '<span class="preset-bt2" id="preset-bt2_'.$value[$i]['spec_id'].'"><span class="spec_value_span_'.$value[$i]['spec_value_id'].'">'.$value[$i]['spec_value'].'</span><em data-value="'.$value[$i]['spec_value'].'" data-mark_id="'.$value[$i]['spec_mark_id'].'" data-preset_id="'.$value[$i]['spec_value_id'].'" onclick="DelValue(this)"><i class="fa fa-times-circle" title="关闭"></i></em> &nbsp; </span>';
                    }

                    $SpecSelectName[$key] .= '</span> &nbsp; &nbsp;';
                    $SpecSelectName[$key] .= '<select name="spec_value" id="spec_value_'.$key.'" onchange="AppEndPreset(this,'.$key.')">';
                    $SpecSelectName[$key] .= $ProductSpecLogic->GetPresetValueOption('', $key, $id, 1);
                    $SpecSelectName[$key] .= '</select> &nbsp; <span title="同步规格数据" data-mark_id="'.$key.'" data-name="'.$value[0]['spec_name'].'" onclick="RefreshSpecValue(this);"><i class="fa fa-refresh"></i></span>';
                    $SpecSelectName[$key] .= '</div></div><br/></div>';
                }

                session('spec_arr',$spec_arr_new);
                $assign_data['SpecSelectName']   = $SpecSelectName;
                $assign_data['HtmlTable']        = $ProductSpecLogic->SharpSpecAssemblyEdit($spec_arr_new, $id);
                $assign_data['spec_mark_id_arr'] = implode(',', $spec_mark_id_arr);
            }

            // 预设值名称
            $where = ['lang' => $this->admin_lang];
            if (!empty($spec_mark_id_arr)) $where['preset_mark_id'] = ['NOT IN',$spec_mark_id_arr];
            $assign_data['preset_value'] = Db::name('product_spec_preset')->where($where)->field('preset_id,preset_mark_id,preset_name')->group('preset_mark_id')->order('preset_mark_id desc')->select();
        }

        return $assign_data;
    }
}