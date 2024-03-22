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

namespace think\template\taglib\api;

use think\Db;
use think\Request;

/**
 * 留言表单
 */
class TagGuestbookform extends Base
{
    public $form_type = 0;

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取留言表单
     * @author wengxianhu by 2018-4-20
     */
    public function getGuestbookform($typeid = '', $form_type = 0)
    {
        // 表单类型 0:留言表单 1:自由表单
        $this->form_type = $form_type;

        if (!empty($this->form_type) && 1 === intval($this->form_type)) {
            // 如果没有传入自由表单ID则获取第一个自由表单ID
            $typeid = !empty($typeid) ? intval($typeid) : Db::name('form')->where('status', 1)->order('form_id asc')->getField('form_id');
        } else {
            $typeid = !empty($typeid) ? $typeid : $this->tid;
        }

        if (empty($typeid)) return false;
        
        if (!empty($this->form_type) && 1 === intval($this->form_type)) {
            $detail = Db::name('form')->where(['form_id' => $typeid])->find();
        } else {
            $detail = Db::name('arctype')->field('id,id as typeid,typename,seo_title,seo_keywords,seo_description')->where(['id' => $typeid])->find();
            $detail['seo_title'] = $this->set_arcseotitle($detail['typename'], $detail['seo_title']);
        }

        $attr_list_row = Db::name('GuestbookAttribute')->field('attr_id,attr_name,attr_input_type,attr_values,required')
            ->where([
                'typeid'    => $typeid,
                'is_del'    => 0,
            ])
            ->order('attr_id asc')
            ->getAllWithIndex("attr_id");
        $attr_list = [];
        foreach ($attr_list_row as $key => $val) {
            $arr = [];
            $attr_id = $val['attr_id'];
            $name = 'attr_'.$attr_id;
            $itemname = 'itemname_'.$attr_id;
            // 字段名称
            $arr[$name] = $name;
            // 表单提示文字
            $arr[$itemname] = $val['attr_name'];
            $attr_list_row[$key]['options'] = [];
            // 针对下拉选择框
            if (in_array($val['attr_input_type'], [1,3,4])) {  //下拉、单选、多选
                $options = array();
                $tmp_option_val = explode(PHP_EOL, $val['attr_values']);
                foreach($tmp_option_val as $k2=>$v2)
                {
                    $tmp_val = array(
                        'value' => $v2,
                    );
                    array_push($options, $tmp_val);
                }
                $arr['options_'.$attr_id] = $tmp_option_val;
                $attr_list_row[$key]['options'] = $tmp_option_val;
                $attr_list_row[$key]['selected'] = "请选择";
            }elseif ($val['attr_input_type'] == 9) {      //区域
                // $arr['first_id_'.$attr_id]=" id='first_id_$attr_id' onchange=\"getNext1598839807('second_id_$attr_id',$attr_id,1);\" ";
                // $arr['second_id_'.$attr_id]=" id='second_id_$attr_id' onchange=\"getNext1598839807('third_id_$attr_id',$attr_id,2);\" style='display:none;'";
                // $arr['third_id_'.$attr_id]=" id='third_id_$attr_id' style='display:none;'  onchange=\"getNext1598839807('', $attr_id,3);\" ";
                // $arr['hidden_'.$attr_id]= "<input type='hidden' name='{$name}' id='{$name}'>";
                 $val['attr_values'] = unserialize($val['attr_values']);
                 $options = Db::name('region')->field("id,name")->where('id','in',$val['attr_values']['region_ids'])->select();
                // $arr['options_'.$attr_id] = $options;
                if (!empty($options)){
                    array_unshift($options,['id'=>'','name'=>'请选择']);
                }
                 $attr_list_row[$key]['options1'] = $options;
                 $attr_list_row[$key]['selected'] = "";
                $attr_list_row[$key]['selected_name'] = "请选择";
                 $attr_list_row[$key]['selected_id'] = [];
                $attr_list_row[$key]['show'] = 0;
            }elseif ($val['attr_input_type'] == 10){   //日期类型
                $attr_list_row[$key]['selected'] = "选择日期";//date("Y-m-d");
            }elseif(in_array($val['attr_input_type'],[5,8])){  //单图、附件
                $attr_list_row[$key]['selected'] = "";
                $attr_list_row[$key]['selected_name'] = "";
            }elseif($val['attr_input_type'] == 11){     //多图
                $attr_list_row[$key]['selected'] = [];
                $attr_list_row[$key]['selected_name'] = [];
            }
                /*--end*/
            $attr_list = array_merge($attr_list, $arr);
        }

        /*表单令牌*/
        $token_name = md5('guestbookform_token_'.$typeid.md5(getTime().uniqid(mt_rand(), TRUE)));
        $token_value = md5($_SERVER['REQUEST_TIME_FLOAT']);
        $session_path = \think\Config::get('session.path');
        $session_file = ROOT_PATH . $session_path . "/sess_".$token_name;
        $fp = fopen($session_file, "w+");
        if (!empty($fp)) {
            if (fwrite($fp, $token_value)) {
                fclose($fp);
            }
        } else {
            file_put_contents ( $session_file,  $token_value);
        }
        /*end*/

        $result = array(
            'detail'    => $detail,
            'attr_list' => $attr_list,
            'attr_list_row' => $attr_list_row,
            'token' => [
                'name'  => '__token__'.$token_name,
                'value' => $token_value,
            ],
        );
        
        return $result;
    }
}