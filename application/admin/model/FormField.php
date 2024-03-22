<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2022/2/24
 * Time: 15:11
 */

namespace app\admin\model;

use think\Db;
use think\Model;

class FormField extends Model
{
    // 初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();

    }
    // 获取表单数据(单条\全部)
    public function GetFormFieldData($FormID = null, $FieldID = null, $FieID = null, $Order = null)
    {
        $Order = !empty($Order) ? $Order : 'sort_order asc, form_id desc, field_id asc';
        $FieID = !empty($FieID) ? $FieID : '*';

        // 查询条件
        $where = [
            'lang' => get_admin_lang(),
        ];
        if (!empty($FormID)) $where['form_id'] = $FormID;

        // 执行查询
        if (!empty($FieldID)) {
            $where['field_id'] = $FieldID;
            $form_field = $this->where($where)->field($FieID)->find();
        } else {
            $form_field = $this->where($where)->field($FieID)->order($Order)->select();
        }
        //指定不出现下级的
        $field_region_all_type = config('global.field_region_all_type');
        //拆解关联区域的默认值字段
        foreach ($form_field as $key=>$val){
            $region = [
                'parent_id'    => '-1',
                'region_id'    => '-1',
                'region_names' => '',
                'region_ids'   => '',
            ];
            if ($val['field_type'] == 'region'){
                $dfvalue = unserialize($val['field_value']);
                if (0 == $dfvalue['region_id']) {
                    $parent_id = $dfvalue['region_id'];
                } else {
                    // 查询当前选中的区域父级ID
                    $parent_id = Db::name('region')->where("id", $dfvalue['region_id'])->getField('parent_id');
                    if (0 == $parent_id) {
                        $parent_id = $dfvalue['region_id'];
                    }
                }
                $city_list = [];
                if ($parent_id && !in_array($parent_id,$field_region_all_type)){
                    $city_list = Db::name('region')->where("parent_id", $parent_id)->select();
                }
                // 加载数据到模板
                $region = [
                    'city_list'    => $city_list,
                    'parent_id'    => $parent_id,
                    'region_id'    => $dfvalue['region_id'],
                    'region_names' => $dfvalue['region_names'],
                    'region_ids'   => $dfvalue['region_ids'],
                ];
            }
            $form_field[$key]['region'] = $region;
        }

        // 返回结果
        return $form_field;
    }

    // 删除表单字段
    public function FormFieldDelete($post = [])
    {
        // 删除条件
        $where = [
            'lang' => get_admin_lang(),
            'form_id' => $post['form_id'],
            'field_id' => ['NOT IN', $post['field_id']]
        ];

        // 执行删除
        $ResultID = $this->where($where)->delete();

        // 返回结果
        return $ResultID;
    }

    // 查询字段数量
    public function GetFormFieldTotal($form_ids = [])
    {
        // 查询条件
        $where = [
            'form_id'   => ['IN', $form_ids],
        ];

        // 执行查询
        $form_field_total = $this ->field('form_id, count(form_id) AS total')
            ->where($where)
            ->group('form_id')
            ->getAllWithIndex('form_id');

        // 返回结果
        return $form_field_total;
    }
}