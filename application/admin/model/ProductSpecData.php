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
            // 查询规格库规格信息
            $where = [
                'lang' => get_admin_lang(),
                'preset_mark_id' => ['IN', $Data['spec_mark_id']],
            ];
            $PresetData = Db::name('product_spec_preset')->where($where)->order('preset_mark_id desc')->select();

            // 查询商品规格库是否存在选中的规格，不存在则执行添加
            $Where = [
                'aid' => $Data['aid'],
                'lang' => $this->admin_lang,
                'spec_mark_id' => $Data['spec_mark_id'],
            ];
            $count = Db::name('product_spec_data_handle')->where($Where)->count();
            if (empty($count)) {
                $insertAll = [];
                $spec_id = date('is');
                foreach ($PresetData as $key => $value) {
                    $spec_id++;
                    $insertAll[] = [
                        'spec_id'        => $spec_id,
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
            if (!empty($insertAll)) Db::name('product_spec_data_handle')->insertAll($insertAll);
        }
    }

    public function ProducSpecNameEditSave($post = array(), $action = 'edit')
    {
        if (!empty($post['aid']) && !empty($post['spec_value_id']) && !empty($post['spec_mark_id'])) {
            // $spec_mark_ids = array_keys($post['spec_mark_id']);
            // $spec_value_ids = array_keys($post['spec_value_id']);

            // 查询条件
            $where = [
                'aid' => $post['aid'],
                'lang' => get_admin_lang(),
                // 'spec_mark_id' => ['IN', $spec_mark_ids],
            ];
            
            $handleAID = session('handleAID');
            if ('add' === strval($action) && !empty($handleAID)) $where['aid'] = $handleAID;

            // 查询规格数据
            $handle = Db::name('product_spec_data_handle')->where($where)->select();

            // 删除当前产品下的所有规格数据
            if ('edit' === strval($action)) Db::name('product_spec_data')->where($where)->delete(true);

            // 添加数组拼装
            $time = getTime();
            $insertAll = [];
            foreach ($handle as $key => $value) {
                $value['aid'] = $post['aid'];
                $insertAll[$key] = [
                    'aid'           => $value['aid'],
                    'spec_mark_id'  => $value['spec_mark_id'],
                    'spec_value_id' => $value['spec_value_id'],
                    'spec_name'     => $value['spec_name'],
                    'spec_value'    => $value['spec_value'],
                    'spec_is_select'=> $value['spec_is_select'],
                    'open_image'    => $value['open_image'],
                    'spec_image'    => $value['spec_image'],
                    'lang'          => get_admin_lang(),
                    'add_time'      => $time,
                    'update_time'   => $time,
                ];
                // if (in_array($value['spec_value_id'], $spec_value_ids)) $insertAll[$key]['spec_is_select'] = 1;
            }
            if (!empty($insertAll)) {
                // 批量添加商品规格
                Db::name('product_spec_data')->insertAll($insertAll);
                // 删除产品规格数据处理表
                Db::name("product_spec_data_handle")->where($where)->delete();
            }
        }
    }

    // 编辑产品时，规格原数据处理
    public function GetProductSpecData($id)
    {
        $assign_data = ['spec_mark_id_arr' => 0];
        // 商城配置
        $shopConfig = getUsersConfigData('shop');
        $assign_data['shopConfig'] = $shopConfig;
        // 已选规格处理
        if (!empty($shopConfig['shop_open']) && isset($shopConfig['shop_open_spec']) && 1 == $shopConfig['shop_open_spec']) {
            // session('spec_arr', null);
            $SpecWhere = [
                'aid' => $id,
                'lang' => $this->admin_lang,
                // 'spec_is_select' => 1,// 已选中的
            ];
            // 删除商品规格处理表的规格数据
            Db::name('product_spec_data_handle')->where($SpecWhere)->delete(true);
            Db::name('product_spec_value_handle')->where($SpecWhere)->delete(true);
            $order = 'spec_value_id asc, spec_id asc';
            $product_spec_data = Db::name('product_spec_data')->where($SpecWhere)->order($order)->select();
            // dump($product_spec_data);
            $order = 'spec_value_id asc, value_id asc';
            $product_spec_value = Db::name('product_spec_value')->where($SpecWhere)->order($order)->select();
            // dump($product_spec_value);
            // exit;
            // 参数预定义
            $assign_data['useSpecNum'] = 0;
            $assign_data['SpecSelectName'] = $assign_data['HtmlTable'] = $assign_data['spec_mark_id_arr'] = '';
            if (!empty($product_spec_data)) {
                // 添加商品规格处理表的规格信息
                $resultID1 = Db::name('product_spec_data_handle')->insertAll($product_spec_data);
                $resultID2 = Db::name('product_spec_value_handle')->insertAll($product_spec_value);
                if (empty($resultID1) || empty($resultID2)) $this->error('信息错误，请重新进入');
                // 查询规格库现有规格preset_mark_id
                $preset_mark_ids = Db::name('product_spec_preset')->column('preset_mark_id');
                $preset_mark_ids = array_unique($preset_mark_ids);
                $ProductSpecLogic = new ProductSpecLogic;
                $spec_arr_new = group_same_key($product_spec_data, 'spec_mark_id');
                $spec_mark_id_arr = [];
                $index = 0;
                foreach ($spec_arr_new as $key => $value) {
                    // dump($key);
                    $assign_data['useSpecNum']++;
                    // 规格库规格显示处理
                    if (in_array($key, $preset_mark_ids)) {
                        $spec_mark_id_arr[] = $key;
                        $SpecSelectName[$key]  = '<div class="prset-box" id="spec_'.$key.'">';
                        $SpecSelectName[$key] .= '<div id="div_'.$key.'">';
                        $SpecSelectName[$key] .= '<div><span class="mr10 specNameSpan" style="display: flex;align-items: baseline;"><span class="preset-bt w150 mr10 "><span class="spec_name_span_'.$key.'">'.$value[0]['spec_name'].'</span><em data-name="'.$value[0]['spec_name'].'" data-mark_id="'.$key.'" onclick="clearPresetSpec(this, '.$key.')"><i class="fa fa-times-circle" title="关闭"></i></em></span>';

                        $none0 = /*0 === intval($index) && */!empty($value[0]["open_image"]) ? '' : 'style="display: none;"';
                        $checked = !empty($value[0]['open_image']) ? 'checked' : '';
                        $SpecSelectName[$key] .= <<<EOF
<label {$none0}><input type="checkbox" onclick="openGoodsSpecImage(this);" value="{$key}" {$checked}>添加图片</label>
EOF;
                        $SpecSelectName[$key] .= '</span>';
                        $SpecSelectName[$key] .= '<span class="set-preset-box"></span>';

                        $SpecSelectName[$key] .= '<span class="set-preset-con"> <span class="d-flex" id="SelectEd_'.$key.'">';
                        $openImage = !empty($value[0]['open_image']) ? 'style="display: block;"' : 'style="display: none;"';
                        for ($i = 0; $i < count($value); $i++) {
                            if (!empty($value[$i]['spec_is_select'])) {
                                $spec_arr_new[$key][$i] = $value[$i]['spec_value_id'];
                                $SpecSelectName[$key] .= '<span class="d-flex mr10" id="preset-bt2_'.$value[$i]['spec_id'].'">';

                                $show0 = !empty($value[$i]["spec_image"]) ? '' : 'style="display: none;"';
                                $none1 = !empty($value[$i]["spec_image"]) ? 'style="display: none;"' : '';
                                $SpecSelectName[$key] .= <<<EOF
<div class='spec-dan-pane openGoodsSpecImage_{$key}' {$openImage}>
    <div class='images_upload'>
        <a href="javascript:void(0);" onclick="goodsSpecImageAdd({$value[$i]["spec_mark_id"]}, {$value[$i]["spec_value_id"]});" class="img-upload" title="上传图片" style="width: 30px; height: 30px;">
            <div class="y-line spec_image_y_line_{$value[$i]["spec_value_id"]}" {$none1}></div>
            <div class="x-line spec_image_x_line_{$value[$i]["spec_value_id"]}" {$none1}></div>
            <img src="{$value[$i]["spec_image"]}" class="pic_con spec_image_src_{$value[$i]["spec_value_id"]}" {$show0}>
        </a>
    </div>
</div>
EOF;
                                $SpecSelectName[$key] .= '<span class="preset-bt2"><span class="spec_value_span_'.$value[$i]['spec_value_id'].'">'.$value[$i]['spec_value'].'</span><em data-value="'.$value[$i]['spec_value'].'" data-spec_mark_id="'.$value[$i]['spec_mark_id'].'" data-spec_value_id="'.$value[$i]['spec_value_id'].'" onclick="clearPresetSpecValue(this)"><i class="fa fa-times-circle" title="关闭"></i></em> &nbsp; </span></span>';
                            } else {
                                unset($spec_arr_new[$key][$i]);
                            }
                        }

                        $SpecSelectName[$key] .= '</span>';
                        $SpecSelectName[$key] .= '<select class="preset-select" name="spec_value" id="spec_value_'.$key.'" onchange="addPresetSpecValue(this,'.$key.')">';
                        $SpecSelectName[$key] .= $ProductSpecLogic->GetPresetValueOption('', $key, $id, 1);
                        $SpecSelectName[$key] .= '</select>';
                        $SpecSelectName[$key] .= '<span class="tongbu" title="同步规格数据" data-mark_id="'.$key.'" data-name="'.$value[0]['spec_name'].'" onclick="RefreshSpecValue(this);"><i class="fa fa-refresh"></i></span>';
                        $SpecSelectName[$key] .= ' </span></div></div></div>';

                        $index++;
                    }
                    // 自定义规格显示处理
                    else {
                        $SpecSelectName[$key]  = '<div class="prset-box">';
                        $SpecSelectName[$key] .= '<span class="set-preset-bt mr10 specNameSpan" style="display: block;">';
                        $SpecSelectName[$key] .= '<input type="text" name="set_spec_name" class="zdy-ggname w150" value="' . $value[0]['spec_name'] . '" placeholder="规格名称.." onchange="setSpecName(this, ' . $key . ');">';
                        $SpecSelectName[$key] .= '<em onclick="setSpecNameClear(this, ' . $key . ');"> <i class="fa fa-times-circle" title="关闭" style="margin-left: -20px; margin-top: 8px;"></i> </em>';

                        $none0 = 0 === intval($index) ? '' : 'style="display: none;"';
                        $checked = !empty($value[0]['open_image']) ? 'checked' : '';
                        $SpecSelectName[$key] .= <<<EOF
<label {$none0}><input type="checkbox" onclick="openGoodsSpecImage(this);" value="{$key}" {$checked}>添加图片</label>
EOF;
                        $SpecSelectName[$key] .= '</span>';
                        $SpecSelectName[$key] .= '<span class="set-preset-box"></span>';
                        $SpecSelectName[$key] .= '<span class="set-preset-con">';
                        $openImage = !empty($value[0]['open_image']) ? 'style="display: block;"' : 'style="display: none;"';
                        for ($i = 0; $i < count($value); $i++) {
                            $spec_arr_new[$key][$i] = $value[$i]['spec_value_id'];
                            $SpecSelectName[$key] .= '<span class="set-preset-bt mr10 ">';
                            
                            $show0 = !empty($value[$i]["spec_image"]) ? '' : 'style="display: none;"';
                            $none1 = !empty($value[$i]["spec_image"]) ? 'style="display: none;"' : '';
                            $SpecSelectName[$key] .= <<<EOF
<div class='spec-dan-pane openGoodsSpecImage_{$key}' {$openImage}>
    <div class='images_upload'>
        <a href="javascript:void(0);" onclick="goodsSpecImageAdd({$value[$i]["spec_mark_id"]}, {$value[$i]["spec_value_id"]});" class="img-upload" title="上传图片" style="width: 30px; height: 30px;">
            <div class="y-line spec_image_y_line_{$value[$i]["spec_value_id"]}" {$none1}></div>
            <div class="x-line spec_image_x_line_{$value[$i]["spec_value_id"]}" {$none1}></div>
            <img src="{$value[$i]["spec_image"]}" class="pic_con spec_image_src_{$value[$i]["spec_value_id"]}" {$show0}>
        </a>
    </div>
</div>
EOF;
                            $SpecSelectName[$key] .= '<input type="hidden" value="' . $value[$i]['spec_value_id'] . '">';
                            $SpecSelectName[$key] .= '<input type="text" class="zdy-ggshuzi w150" value="' . $value[$i]['spec_value'] . '" placeholder="规格值.." onchange="setSpecValue(this, ' . $value[$i]['spec_mark_id'] . ');">';
                            if (0 !== intval($i)) {
                                $SpecSelectName[$key] .= '<em data-spec_mark_id="' . $value[$i]['spec_mark_id'] . '" data-spec_value_id="' . $value[$i]['spec_value_id'] . '" onclick="setSpecValueClear(this);"><i class="fa fa-times-circle" title="关闭" style="margin-left: -22px; margin-top: 8px; cursor: pointer;"></i></em>';
                            }
                            $SpecSelectName[$key] .= '</span>';
                        }
                        $SpecSelectName[$key] .= '<a href="javascript:void(0);" onclick="addCustomSpecValue(this, ' . $key . ');" class="preset-bt-shuzi mr10">+增加规格值</a>';
                        $SpecSelectName[$key] .= '</span>';
                        $SpecSelectName[$key] .= '</div>';

                        $index++;
                    }
                }

                // session('spec_arr', $spec_arr_new);
                $assign_data['SpecSelectName'] = $SpecSelectName;
                $assign_data['HtmlTable'] = $ProductSpecLogic->SpecAssemblyEdit($spec_arr_new, $id);
                $assign_data['spec_mark_id_arr'] = implode(',', $spec_mark_id_arr);
            }

            // 预设值名称
            $where = ['lang' => $this->admin_lang];
            if (!empty($spec_mark_id_arr)) $where['preset_mark_id'] = ['NOT IN',$spec_mark_id_arr];
            $assign_data['preset_value'] = Db::name('product_spec_preset')->where($where)->field('preset_id,preset_mark_id,preset_name')->group('preset_mark_id')->order('preset_mark_id desc')->select();
            $assign_data['maxPresetMarkID'] = $assign_data['preset_value'][0]['preset_mark_id'];
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

                    $SpecSelectName[$key] .= '<span class="d-flex" id="SelectEd_'.$key.'">';
                    for ($i=0; $i<count($value); $i++) {
                        $spec_arr_new[$key][$i] = $value[$i]['spec_value_id'];
                        $SpecSelectName[$key] .= '<span class="preset-bt2" id="preset-bt2_'.$value[$i]['spec_id'].'"><span class="spec_value_span_'.$value[$i]['spec_value_id'].'">'.$value[$i]['spec_value'].'</span><em data-value="'.$value[$i]['spec_value'].'" data-mark_id="'.$value[$i]['spec_mark_id'].'" data-preset_id="'.$value[$i]['spec_value_id'].'" onclick="DelValue(this)"><i class="fa fa-times-circle" title="关闭"></i></em> &nbsp; </span>';
                    }

                    $SpecSelectName[$key] .= '</span>';
                    $SpecSelectName[$key] .= '<select class="preset-select" name="spec_value" id="spec_value_'.$key.'" onchange="AppEndPreset(this,'.$key.')">';
                    $SpecSelectName[$key] .= $ProductSpecLogic->GetPresetValueOption('', $key, $id, 1);
                    $SpecSelectName[$key] .= '</select><span class="tongbu" title="同步规格数据" data-mark_id="'.$key.'" data-name="'.$value[0]['spec_name'].'" onclick="RefreshSpecValue(this);"><i class="fa fa-refresh"></i></span>';
                    $SpecSelectName[$key] .= '</div></div></div>';
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
    /**
     * 2022/03/08 大黄 限时折扣 编辑限时折扣商品，规格原数据处理
     */
    public function GetDiscountProductSpecData($id)
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

                    $SpecSelectName[$key] .= '<span class="d-flex" id="SelectEd_'.$key.'">';
                    for ($i=0; $i<count($value); $i++) {
                        $spec_arr_new[$key][$i] = $value[$i]['spec_value_id'];
                        $SpecSelectName[$key] .= '<span class="preset-bt2" id="preset-bt2_'.$value[$i]['spec_id'].'"><span class="spec_value_span_'.$value[$i]['spec_value_id'].'">'.$value[$i]['spec_value'].'</span><em data-value="'.$value[$i]['spec_value'].'" data-mark_id="'.$value[$i]['spec_mark_id'].'" data-preset_id="'.$value[$i]['spec_value_id'].'" onclick="DelValue(this)"><i class="fa fa-times-circle" title="关闭"></i></em> &nbsp; </span>';
                    }

                    $SpecSelectName[$key] .= '</span>';
                    $SpecSelectName[$key] .= '<select class="preset-select" name="spec_value" id="spec_value_'.$key.'" onchange="AppEndPreset(this,'.$key.')">';
                    $SpecSelectName[$key] .= $ProductSpecLogic->GetPresetValueOption('', $key, $id, 1);
                    $SpecSelectName[$key] .= '</select><span class="tongbu" title="同步规格数据" data-mark_id="'.$key.'" data-name="'.$value[0]['spec_name'].'" onclick="RefreshSpecValue(this);"><i class="fa fa-refresh"></i></span>';
                    $SpecSelectName[$key] .= '</div></div></div>';
                }

                session('spec_arr',$spec_arr_new);
                $assign_data['SpecSelectName']   = $SpecSelectName;
                $assign_data['HtmlTable']        = $ProductSpecLogic->DiscountSpecAssemblyEdit($spec_arr_new, $id);
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