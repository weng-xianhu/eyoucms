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

namespace app\admin\logic;

use think\Db;
use think\Model;
/**
 * 产品逻辑定义
 * Class CatsLogic
 * @package admin\Logic
 */
class ProductLogic extends Model
{
    /**
     * 动态获取产品参数输入框 根据不同的数据返回不同的输入框类型
     * @param int $aid 产品id
     * @param int $typeid 产品栏目id
     */
    public function getAttrInput($aid, $typeid)
    {
        header("Content-type: text/html; charset=utf-8");
        $aid = intval($aid);
        $typeid = intval($typeid);
        $productAttribute = model('ProductAttribute');
        $attributeList = $productAttribute->where(['typeid'=>$typeid, 'is_del'=>0])->order('sort_order asc, attr_id asc')->select();
        $str = '';
        foreach($attributeList as $key => $val)
        {
            $attr_id = $val['attr_id'];
            $curAttrVal = $this->getProductAttrVal(NULL,$aid, $attr_id);
             //促使他 循环
            if(empty($curAttrVal))
                $curAttrVal[] = array('product_attr_id' =>'','aid' => '','attr_id' => '','attr_value' => '');
            foreach($curAttrVal as $k =>$v)
            {
                $str .= "<dl class='row attr_{$attr_id}'>";
                $addDelAttr = ''; // 加减符号
                $str .= "<dt class='tit pl5'><label for='attr_{$attr_id}'>$addDelAttr {$val['attr_name']}</label></dt>";
                        
                // 单行文本框
                if($val['attr_input_type'] == 0)
                {
                    $str .= "<dd class='opt'><input type='text' size='40' value='".($aid ? $v['attr_value'] : $val['attr_values'])."' name='attr_{$attr_id}[]' /><span class='err' tyle='color:#F00; display:none;'></span><p class='notic'></p></dd>";
                }
                // 下拉列表框（一行代表一个可选值）
                if($val['attr_input_type'] == 1)
                {
                    $str .= "<dd class='opt'><select name='attr_{$attr_id}[]'><option value='0'>无</option>";
                    $tmp_option_val = explode(PHP_EOL, $val['attr_values']);
                    foreach($tmp_option_val as $k2=>$v2)
                    {
                        // 编辑的时候 有选中值
                        $v2 = preg_replace("/\s/","",$v2);
                        if($v['attr_value'] == $v2)
                            $str .= "<option selected='selected' value='{$v2}'>{$v2}</option>";
                        else
                            $str .= "<option value='{$v2}'>{$v2}</option>";
                    }
                    $str .= "</select><span class='err' tyle='color:#F00; display:none;'></span><p class='notic'></p></dd>";                
                }
                // 多行文本框
                if($val['attr_input_type'] == 2)
                {
                    $str .= "<dd class='opt'><textarea cols='40' rows='3' name='attr_{$attr_id}[]'>".($aid ? $v['attr_value'] : $val['attr_values'])."</textarea><span class='err' tyle='color:#F00; display:none;'></span><p class='notic'></p></dd>";
                }
                // 富文本编辑器
                if($val['attr_input_type'] == 3)
                {
                    $str .= "<dd class='opt'><textarea class='span12 ckeditor' id='attr_{$attr_id}' name='attr_{$attr_id}[]'>".($aid ? $v['attr_value'] : $val['attr_values'])."</textarea><span class='err' tyle='color:#F00; display:none;'></span><p class='notic'></p></dd>";
                    $url = url('Ueditor/index', array('savepath'=>'allimg'));
                    $str .= <<<EOF
<script type="text/javascript">
    UE.getEditor("attr_{$attr_id}",{
        serverUrl :"{$url}",
        zIndex: 999,
        initialFrameWidth: "100%", //初化宽度
        initialFrameHeight: 300, //初化高度            
        focus: false, //初始化时，是否让编辑器获得焦点true或false
        maximumWords: 99999,
        removeFormatAttributes: 'class,style,lang,width,height,align,hspace,valign',//允许的最大字符数 'fullscreen',
        pasteplain:false, //是否默认为纯文本粘贴。false为不使用纯文本粘贴，true为使用纯文本粘贴
        autoHeightEnabled: false,
        toolbars: ueditor_toolbars
    });
</script>
EOF;
                }
                $str .=  "</dl>";
            }                        

        }        
        return  $str;
    }

    /**
     * 动态获取产品参数输入框 根据不同的数据返回不同的输入框类型
     * @param int $aid 产品id
     * @param int $typeid 产品栏目id
     */
    public function getShopAttrInput($aid, $typeid, $list_id)
    {
        header("Content-type: text/html; charset=utf-8");
        $aid = intval($aid);
        $typeid = intval($typeid);
        $list_id = intval($list_id);
        $where = [
            'is_del' => 0
        ];
        if (!empty($list_id)) {
            $where['list_id'] = $list_id;
            $where['status'] = 1;
        }
        $attributeList = Db::name('ShopProductAttribute')->where($where)->order('sort_order asc, attr_id asc')->select();
        
        // 根据排序号升序排序
        $attrList = Db::name('ShopProductAttr')->where(['aid'=>$aid])->order('sort_order asc')->getAllWithIndex('attr_id');
        $sort_order = [];
        foreach($attributeList as $key => $val) {
            if (isset($attrList[$val['attr_id']]['sort_order'])) {
                $val['sort_order'] = $attrList[$val['attr_id']]['sort_order'];
            }
            $sort_order[$key]  = $val['sort_order'];
            $attributeList[$key] = $val;
        }
        array_multisort($sort_order, SORT_ASC, $attributeList);
        // end

        $str = '';
        foreach($attributeList as $key => $val) {
            $attr_id = $val['attr_id'];
            $curAttrVal = $this->getShopProductAttrVal(NULL, $aid, $attr_id);

            //促使他 循环
            if(empty($curAttrVal)) $curAttrVal[] = array('product_attr_id'=>'', 'aid'=>'', 'attr_id'=>'', 'attr_value'=>'');
            
            foreach($curAttrVal as $k =>$v) {
                $str .= "<dl class='row attr_{$attr_id}'>";
                $addDelAttr = ''; // 加减符号
                $str .= "<dt class='sort-e pl0'><input size='4' type='text' size='10' value='". $v['sort_order'] ."' name='new_attr_sort_order[{$attr_id}]' placeholder='100'></dt>";
                $str .= "<dt class='tit pl5'><input type='text' size='10' value='$addDelAttr {$val['attr_name']}' name='attr_{$attr_id}' readonly='readonly'/></dt>";
                        
                // 单行文本框
                if($val['attr_input_type'] == 0) {
                    $str .= "<dd class='opt pl5'><input type='text' size='40' value='".($aid ? $v['attr_value'] : $val['attr_values'])."' name='shop_attr_{$attr_id}[]' /><a class='text_a grey' href='javascript:void(0);' >&nbsp;&nbsp;删除</a></dd>";
                }

                // 下拉列表框（一行代表一个可选值）
                if($val['attr_input_type'] == 1) {
                    // <option value='0'>无</option>
                    $str .= "<dd class='opt pl5'><select name='shop_attr_{$attr_id}[]' style='width: 306px;'>"; $tmp_option_val = explode(PHP_EOL, $val['attr_values']); foreach($tmp_option_val as $k2=>$v2)
                    {
                        // 编辑的时候 有选中值
                        // $v2 = preg_replace("/\s/","",$v2);
                        if(trim($v['attr_value']) == trim($v2))
                            $str .= "<option selected='selected' value='{$v2}'>{$v2}</option>";
                        else
                            $str .= "<option value='{$v2}'>{$v2}</option>";
                    }
                    $str .= "</select><a class='text_a grey' href='javascript:void(0);' >&nbsp;&nbsp;删除</a></dd>";                
                }

                // 多行文本框
                if($val['attr_input_type'] == 2) {
                    $str .= "<dd class='opt'><textarea cols='40' rows='3' name='shop_attr_{$attr_id}[]'>".($aid ? $v['attr_value'] : $val['attr_values'])."</textarea><span class='err' tyle='color:#F00; display:none;'></span><p class='notic'></p></dd>";
                }

                // 富文本编辑器
                if($val['attr_input_type'] == 3) {
                    $str .= "<dd class='opt'><textarea class='span12 ckeditor' id='attr_{$attr_id}' name='shop_attr_{$attr_id}[]'>".($aid ? $v['attr_value'] : $val['attr_values'])."</textarea><span class='err' tyle='color:#F00; display:none;'></span><p class='notic'></p></dd>";
                    $url = url('Ueditor/index', array('savepath'=>'allimg'));
                    $str .= <<<EOF
<script type="text/javascript">
    UE.getEditor("attr_{$attr_id}",{
        serverUrl :"{$url}",
        zIndex: 999,
        initialFrameWidth: "100%", //初化宽度
        initialFrameHeight: 300, //初化高度            
        focus: false, //初始化时，是否让编辑器获得焦点true或false
        maximumWords: 99999,
        removeFormatAttributes: 'class,style,lang,width,height,align,hspace,valign',//允许的最大字符数 'fullscreen',
        pasteplain:false, //是否默认为纯文本粘贴。false为不使用纯文本粘贴，true为使用纯文本粘贴
        autoHeightEnabled: false,
        toolbars: ueditor_toolbars
    });
</script>
EOF;
                }
                $str .=  "</dl>";
            }                        

        }        
        return  $str;
    }
    
    /**
     * 获取 product_attr 表中指定 aid  指定 attr_id  或者 指定 product_attr_id 的值 可是字符串 可是数组
     * @param int $product_attr_id product_attr表id
     * @param int $aid 产品id
     * @param int $attr_id 产品参数id
     * @return array 返回数组
     */
    public function getProductAttrVal($product_attr_id = 0 ,$aid = 0, $attr_id = 0)
    {
        $product_attr_id = intval($product_attr_id);
        $aid = intval($aid);
        $attr_id = intval($attr_id);

        $productAttr = Db::name('ProductAttr');
        if($product_attr_id > 0)
            return $productAttr->where(['product_attr_id'=>$product_attr_id])->select(); 
        if($aid > 0 && $attr_id > 0)
            return $productAttr->where(['aid'=>$aid,'attr_id'=>$attr_id])->select();        
    }

    /**
     * 获取 shop_product_attr 表中指定 aid  指定 attr_id  或者 指定 product_attr_id 的值 可是字符串 可是数组
     * @param int $product_attr_id product_attr表id
     * @param int $aid 产品id
     * @param int $attr_id 产品参数id
     * @return array 返回数组
     */
    public function getShopProductAttrVal($product_attr_id = 0 ,$aid = 0, $attr_id = 0)
    {
        $product_attr_id = intval($product_attr_id);
        $aid = intval($aid);
        $attr_id = intval($attr_id);

        $ShopProductAttr = Db::name('ShopProductAttr');
        
        if($product_attr_id > 0) {
            return $ShopProductAttr->where(['product_attr_id'=>$product_attr_id])->order('sort_order asc')->select();
        }

        if($aid > 0 && $attr_id > 0) {
            return $ShopProductAttr->where(['aid'=>$aid,'attr_id'=>$attr_id])->order('sort_order asc')->select();
        }
    }

    /**
     *  给指定产品添加属性 或修改属性 更新到 product_attr
     * @param int $aid  产品id
     * @param int $typeid  产品栏目id
     */
    public function saveProductAttr($aid, $typeid, $postData = [])
    {  
        $aid = intval($aid);
        $typeid = intval($typeid);

        $productAttr = Db::name('ProductAttr');
                
        // 属性类型被更改了 就先删除以前的属性类型 或者没有属性 则删除        
        if($typeid == 0)  
        {
            $productAttr->where('aid = '.$aid)->delete(); 
            return;
        }
    
        $productAttrList = $productAttr->where('aid = '.$aid)->select();
        
        $old_product_attr = array(); // 数据库中的的属性  以 attr_id _ 和值的 组合为键名
        foreach($productAttrList as $k => $v)
        {                
            $old_product_attr[$v['attr_id'].'_'.$v['attr_value']] = $v;
        }            
                          
        // post 提交的属性  以 attr_id _ 和值的 组合为键名    
        $post = input("post.");
        if (!isset($post['title'])) {
            $post = $postData;
        }
        foreach($post as $k => $v)
        {
            $attr_id = str_replace('attr_','',$k);
            if(!strstr($k, 'attr_'))
                continue;                                 
            foreach ($v as $k2 => $v2)
            {                      
                //$v2 = str_replace('_', '', $v2); // 替换特殊字符
                //$v2 = str_replace('@', '', $v2); // 替换特殊字符
                $v2 = trim($v2);

                if(empty($v2))
                    continue;

                $tmp_key = $attr_id."_".$v2;
                if(!array_key_exists($tmp_key , $old_product_attr)) // 数据库中不存在 说明要做删除操作
                {
                    $adddata = array(
                        'aid'   => $aid,
                        'attr_id'   => $attr_id,
                        'attr_value'   => $v2,
                        'add_time'   => getTime(),
                        'update_time'   => getTime(),
                    );
                    $productAttr->add($adddata);                       
                }
                unset($old_product_attr[$tmp_key]);
           }
            
        }     
        // 没有被 unset($old_product_attr[$tmp_key]); 掉是 说明 数据库中存在 表单中没有提交过来则要删除操作
        foreach($old_product_attr as $k => $v)
        {                
            $productAttr->where('product_attr_id = '.$v['product_attr_id'])->delete(); // 
        }                       
    }

    /**
     *  给指定产品添加属性 或修改属性 更新到 shop_product_attr
     * @param int $aid  产品id
     * @param int $typeid  产品栏目id
     */
    public function saveShopProductAttr($aid, $typeid, $postData = [])
    {  
        $aid = intval($aid);
        $typeid = intval($typeid);
        
        $ShopProductAttr = Db::name('ShopProductAttr');
        // 属性类型被更改了 就先删除以前的属性类型 或者没有属性 则删除
        if($typeid == 0) {
            $ShopProductAttr->where('aid = '.$aid)->delete(); 
            return false;
        }

        $productAttrList = $ShopProductAttr->where('aid = '.$aid)->select();
        $old_product_attr = array(); // 数据库中的的属性  以 attr_id _ 和值的 组合为键名
        foreach($productAttrList as $k => $v) {
            $old_product_attr[$v['attr_id'].'_'.$v['attr_value'].'_'.$v['sort_order']] = $v;
        }

        // post 提交的属性  以 attr_id _ 和值的 组合为键名
        $post = input("post.");
        if (!isset($post['title'])) {
            $post = $postData;
        }
        $sort_order = $post['new_attr_sort_order'];
        foreach($post as $k => $v) {
            $attr_id = str_replace('shop_attr_', '', $k);
            if(!strstr($k, 'shop_attr_')) continue;
            foreach ($v as $k2 => $v2) {
                $v2 = trim($v2);
                if(empty($v2)) continue;

                $tmp_key = $attr_id . "_" . $v2 . "_" . $sort_order[$attr_id];
                if(!array_key_exists($tmp_key, $old_product_attr)) {
                    // 数据库中不存在 说明要做删除操作
                    $adddata = array(
                        'aid'         => $aid,
                        'attr_id'     => $attr_id,
                        'attr_value'  => $v2,
                        'sort_order'  => $sort_order[$attr_id],
                        'add_time'    => getTime(),
                        'update_time' => getTime(),
                    );
                    $ShopProductAttr->add($adddata);
                }
                unset($old_product_attr[$tmp_key]);
            }
        }

        // 没有被 unset($old_product_attr[$tmp_key]); 掉是 说明 数据库中存在 表单中没有提交过来则要删除操作
        foreach($old_product_attr as $k => $v) {                
            $ShopProductAttr->where('product_attr_id = '.$v['product_attr_id'])->delete();
        }                       
    }
}