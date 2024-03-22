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

/**
 * 产品规格预设模型
 */
class ProductSpecPreset extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        $this->admin_lang = get_admin_lang();
    }

    // 预设规格中最大的标记MarkId，结果+1返回
    public function GetMaxPresetMarkId()
    {
    	$PresetMarkId = $this->where('lang',$this->admin_lang)->order('preset_mark_id desc')->getField('preset_mark_id');
        $PresetMarkId++;

        return $PresetMarkId;
    }

    public function GetPresetNewData($specArr = array(), $GetData = array())
    {
        $return = [
            'Option' => '',
            'Value'  => '',
        ];
        $preset_ids = $specArr[$GetData['preset_mark_id']];
        if (empty($GetData['aid'])) {
            $WhereNew = [
                'preset_id' => ['NOT IN', $preset_ids],
                'preset_mark_id' => $GetData['preset_mark_id'],
            ];
            $return['Option'] = $this->where($WhereNew)->select();
            // 读取选中的规格值
            $return['Value'] = $this->where('preset_id', $GetData['preset_id'])->getField('preset_value');
        } else {
            // 更新规格值
            $Where = [
                'aid' => $GetData['aid'],
                'spec_mark_id' => $GetData['preset_mark_id'],
                'spec_value_id' => $GetData['preset_id'],
                'spec_is_select'=> 0,
            ];
            // 查询添加的规格值
            $return['Value'] = Db::name('product_spec_data_handle')->where($Where)->getField('spec_value');
            // 更新状态为选中
            Db::name('product_spec_data_handle')->where($Where)->update(['spec_is_select'=>1, 'update_time'=>getTime()]);

            // 拼装新的下拉框数据
            $WhereNew = [
                'aid' => $GetData['aid'],
                'spec_mark_id'  => $GetData['preset_mark_id'],
                'spec_is_select'=> 0,
            ];
            $Data = Db::name('product_spec_data_handle')->where($WhereNew)->field('spec_value_id, spec_value')->select();
            $return['Option'] .= "<option value='0'>选择规格值</option>";
            foreach ($Data as $value) {
                $return['Option'] .= "<option value='{$value['spec_value_id']}'>{$value['spec_value']}</option>";
            }
        }

        return $return;
    }

    // 批量添加产品规格名称、规格值、规格价格库存
    public function ProductSpecInsertAll($aid = null, $post = array())
    {
        if (!empty($aid) && !empty($post['preset_id'])) {
            $preset_ids = array_keys($post['preset_id']);
            $preset_mark_ids = array_keys($post['preset_mark_id']);
            $where = [
                'lang' => get_admin_lang(),
                'preset_mark_id' => ['IN', $preset_mark_ids],
            ];
            $PresetData = Db::name('product_spec_preset')->where($where)->select();
            $time = getTime();
            // 产品规格名称及规格值
            $AddSpecData = [];
            foreach ($PresetData as $key => $value) {
                $AddSpecData[$key] = [
                    'aid'           => $aid,
                    'spec_mark_id'  => $value['preset_mark_id'],
                    'spec_name'     => $value['preset_name'],
                    'spec_value'    => $value['preset_value'],
                    'spec_value_id' => $value['preset_id'],
                    'spec_is_select'=> 0,
                    'lang'          => get_admin_lang(),
                    'add_time'      => $time,
                    'update_time'   => $time,
                ];
                if (in_array($value['preset_id'], $preset_ids)) {
                    $AddSpecData[$key]['spec_is_select'] = 1;
                }
            }
            Db::name('product_spec_data')->insertAll($AddSpecData);

            // 产品规格价格及规格库存
            $AddSpecValue = [];
            foreach ($post['preset_price'] as $kkk => $vvv) {
                $AddSpecValue[] = [
                    'aid'           => $aid,
                    'spec_value_id' => $kkk,
                    'spec_price'    => $vvv['users_price'],
                    'spec_stock'    => $post['preset_stock'][$kkk]['stock_count'],
                    'spec_sales_num' => $post['preset_sales'][$kkk]['spec_sales_num'],
                    'lang'          => get_admin_lang(),
                    'add_time'      => $time,
                    'update_time'   => $time,
                ];
            }
            Db::name('product_spec_value')->insertAll($AddSpecValue);
            
            // 删除商品添加时产生的废弃规格
            $del_spec = session('del_spec') ? session('del_spec') : [];
            if (!empty($del_spec)) {
                $del_spec = array_unique($del_spec);
                $where = [
                    'product_add' => 1,
                    'preset_mark_id' => ['IN', $del_spec]
                ];
                Db::name('product_spec_preset')->where($where)->delete(true);
                // 清除 session
                session('del_spec', null);
            }
        }
    }

}