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

use think\Request;

/**
 * 留言表单
 */
class TagGuestbookform extends Base
{
    public $tid = '';
    
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->tid = I("param.tid/s", ''); // 应用于栏目列表
        /*tid为目录名称的情况下*/
        $this->tid = $this->getTrueTypeid($this->tid);
        /*--end*/
    }

    /**
     * 获取留言表单
     * @author wengxianhu by 2018-4-20
     */
    public function getGuestbookform($typeid = '', $type = 'default')
    {
        $typeid = !empty($typeid) ? $typeid : $this->tid;

        if (empty($typeid)) {
            echo '标签guestbookform报错：缺少属性 typeid 值。';
            return false;
        }

        /*多语言*/
        if (!empty($typeid)) {
            $typeid = model('LanguageAttr')->getBindValue($typeid, 'arctype');
            if (empty($typeid)) {
                echo '标签guestbookform报错：找不到与第一套【'.$this->main_lang.'】语言关联绑定的属性 typeid 值 。';
                return false;
            }
        }
        /*--end*/
        
        $result = false;

        /*当前栏目下的表单属性*/
        $row = M('guestbook_attribute')
            ->where([
                'typeid'    => $typeid,
                'lang'      => $this->home_lang,
                'is_del'    => 0,
            ])
            ->order('sort_order asc, attr_id asc')
            ->select();
        /*--end*/
        if (empty($row)) {
            echo '标签guestbookform报错：该栏目下没有新增表单属性。';
            return false;
        } else {
            /*获取多语言关联绑定的值*/
            $row = model('LanguageAttr')->getBindValue($row, 'guestbook_attribute', $this->main_lang); // 多语言
            /*--end*/

            $newAttribute = array();
            $attr_input_type_1 = 1; // 兼容v1.1.6之前的版本
            foreach ($row as $key => $val) {
                // $newKey = $key + 1;
                $attr_id = $val['attr_id'];
                /*字段名称*/
                $name = 'attr_'.$attr_id;
                $newAttribute[$name] = $name;
                /*--end*/
                /*表单提示文字*/
                $itemname = 'itemname_'.$attr_id;
                $newAttribute[$itemname] = $val['attr_name'];
                /*--end*/
                /*针对下拉选择框*/
                if ($val['attr_input_type'] == 1) {
                    $tmp_option_val = explode(PHP_EOL, $val['attr_values']);
                    $options = array();
                    foreach($tmp_option_val as $k2=>$v2)
                    {
                        $tmp_val = array(
                            'value' => $v2,
                        );
                        array_push($options, $tmp_val);
                    }
                    $newAttribute['options_'.$attr_id] = $options;

                    /*兼容v1.1.6之前的版本*/
                    if (1 == $attr_input_type_1) {
                        $newAttribute['options'] = $options;
                    }
                    ++$attr_input_type_1;
                    /*--end*/
                }
                /*--end*/
            }

            $token_id = md5('guestbookform_token_'.$typeid.md5(getTime().uniqid(mt_rand(), TRUE)));
            $funname = 'f'.md5("ey_guestbookform_token_{$typeid}");
            $tokenStr = <<<EOF
<script type="text/javascript">
    function {$funname}()
    {
        //步骤一:创建异步对象
        var ajax = new XMLHttpRequest();
        //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
        ajax.open("get", "{$this->root_dir}/index.php?m=api&c=Ajax&a=get_token&name=__token__{$token_id}", true);
        // 给头部添加ajax信息
        ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
        //步骤三:发送请求+数据
        ajax.send();
        //步骤四:注册事件 onreadystatechange 状态改变就会调用
        ajax.onreadystatechange = function () {
            //步骤五 如果能够进到这个判断 说明 数据 完美的回来了,并且请求的页面是存在的
            if (ajax.readyState==4 && ajax.status==200) {
        　　　　document.getElementById("{$token_id}").value = ajax.responseText;
          　}
        } 
    }
    {$funname}();
</script>
EOF;
            $hidden = '<input type="hidden" name="typeid" value="'.$typeid.'" /><input type="hidden" name="__token__'.$token_id.'" id="'.$token_id.'" value="" />'.$tokenStr;
            $newAttribute['hidden'] = $hidden;

            $action = url('home/Lists/gbook_submit');
            $newAttribute['action'] = $action;

            $result[0] = $newAttribute;
        }
        
        return $result;
    }

    /**
     * 动态获取留言栏目属性输入框 根据不同的数据返回不同的输入框类型
     * @param int $typeid 留言栏目id
     */
    public function getAttrInput($typeid)
    {
        header("Content-type: text/html; charset=utf-8");
        $attributeList = M('GuestbookAttribute')->where("typeid = $typeid")
            ->where('lang', $this->home_lang)
            ->order('sort_order asc')
            ->select();
        $form_arr = array();
        $i = 1;
        foreach($attributeList as $key => $val)
        {
            $str = "";
            switch ($val['attr_input_type']) {
                case '0':
                    $str = "<input class='guest-input ".$this->inputstyle."' id='attr_".$i."' type='text' value='".$val['attr_values']."' name='attr_{$val['attr_id']}[]' placeholder='".$val['attr_name']."'/>";
                    break;
                
                case '1':
                    $str = "<select class='guest-select ".$this->inputstyle."' id='attr_".$i."' name='attr_{$val['attr_id']}[]'><option value=''>无</option>";
                    $tmp_option_val = explode(PHP_EOL, $val['attr_values']);
                    foreach($tmp_option_val as $k2=>$v2)
                    {
                        $str .= "<option value='{$v2}'>{$v2}</option>";
                    }
                    $str .= "</select>";
                    break;
                
                case '2':
                    $str = "<textarea class='guest-textarea ".$this->inputstyle."' id='attr_".$i."' cols='40' rows='3' name='attr_{$val['attr_id']}[]' placeholder='".$val['attr_name']."'>".$val['attr_values']."</textarea>";
                    break;
                
                default:
                    # code...
                    break;
            }

            $i++;

            $form_arr[$key] = array(
                'value' => $str,
                'attr_name' => $val['attr_name'],
            );
        }        
        return  $form_arr;
    }
}