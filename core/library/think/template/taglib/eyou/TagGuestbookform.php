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

use think\Db;
use think\Request;

/**
 * 留言表单
 */
class TagGuestbookform extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取留言表单
     * @author wengxianhu by 2018-4-20
     */
    public function getGuestbookform($typeid = '', $type = 'default', $beforeSubmit = '')
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
        $row = Db::name('guestbook_attribute')
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
            //检测规则
            $validate_type_list = config("global.validate_type_list");
            $check_js = '';
            foreach ($row as $key => $val) {
                $attr_id = $val['attr_id'];
                /*字段名称*/
                $name = 'attr_'.$attr_id;
                if (in_array($val['attr_input_type'], [4])) { // 多选框、上传图片或附件
                    $newAttribute[$name] = $name."[]";
                } else {
                    $newAttribute[$name] = $name;
                }
                /*--end*/
                /*表单提示文字*/
                $itemname = 'itemname_'.$attr_id;
                $newAttribute[$itemname] = $val['attr_name'];
                /*--end*/
                /*针对下拉选择框*/
                if (in_array($val['attr_input_type'], [1,3,4])) {
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
                }elseif ($val['attr_input_type']==9) {
                    $newAttribute['first_id_'.$attr_id]=" id='first_id_$attr_id' onchange=\"getNext1598839807('second_id_$attr_id',$attr_id,1);\" ";
                    $newAttribute['second_id_'.$attr_id]=" id='second_id_$attr_id' onchange=\"getNext1598839807('third_id_$attr_id',$attr_id,2);\" style='display:none;'";
                    $newAttribute['third_id_'.$attr_id]=" id='third_id_$attr_id' style='display:none;'  onchange=\"getNext1598839807('', $attr_id,3);\" ";
                    $newAttribute['hidden_'.$attr_id]= "<input type='hidden' name='{$name}' id='{$name}'>";
                    $val['attr_values'] = unserialize($val['attr_values']);
                    $newAttribute['options_'.$attr_id] = Db::name('region')->where('id','in',$val['attr_values']['region_ids'])->select();
                }
                /*--end*/

                //是否必填（js判断）
                if (!empty($val['required'])){
                    
                    if ($val['attr_input_type'] == 4) { // 多选框
                        $check_js .= "
                            if(x[i].name == 'attr_".$val['attr_id']."[]'){
                                var names = document.getElementsByName('attr_".$val['attr_id']."[]');    
                                var flag = false ; //标记判断是否选中一个               
                                for(var j=0; j<names.length; j++){
                                    if(names[j].checked){
                                        flag = true ;
                                        break ;
                                     }
                                 }
                                 if(!flag){
                                    alert('".$val['attr_name']."至少选择一项！');
                                    return false;
                                 }
                            }
                        ";
                    } else if ($val['attr_input_type'] == 3) { // 单选框
                        $check_js .= "
                            if(x[i].name == 'attr_".$val['attr_id']."'){
                                var names = document.getElementsByName('attr_".$val['attr_id']."');    
                                var flag = false ; //标记判断是否选中一个               
                                for(var j=0; j<names.length; j++){
                                    if(names[j].checked){
                                        flag = true ;
                                        break ;
                                     }
                                 }
                                 if(!flag){
                                    alert('请选择".$val['attr_name']."！');
                                    return false;
                                 }
                            }
                        ";
                    } else {
                        $check_js .= "
                            if(x[i].name == 'attr_".$val['attr_id']."' && x[i].value.length == 0){
                                alert('".$val['attr_name']."不能为空！');
                                return false;
                            }
                        ";
                    }
                }

                //是否正则限制（js判断）
                if (!empty($val['validate_type']) && !empty($validate_type_list[$val['validate_type']]['value'])){
                    $check_js .= " 
                    if(x[i].name == 'attr_".$val['attr_id']."' && !(".$validate_type_list[$val['validate_type']]['value'].".test( x[i].value))){
                        alert('".$val['attr_name']."格式不正确！');
                        return false;
                    }
                   ";
                }
            }

            if (!empty($check_js)) {
                $check_js = <<<EOF
    var x = elements;
    for (var i=0;i<x.length;i++) {
        {$check_js}
    }
EOF;
            }

            if (!empty($beforeSubmit)) {
                $beforeSubmit = "try{if(false=={$beforeSubmit}()){return false;}}catch(e){}";
            }

            $token_id = md5('guestbookform_token_'.$typeid.md5(getTime().uniqid(mt_rand(), TRUE)));
            $funname = 'f'.md5("ey_guestbookform_token_{$typeid}");
            $submit = 'submit'.$token_id;
            $tokenStr = <<<EOF
<script type="text/javascript">
    function {$submit}(elements)
    {
        if (document.getElementById('gourl_{$token_id}')) {
            document.getElementById('gourl_{$token_id}').value = window.location.href;
        }
        {$check_js}
        {$beforeSubmit}
        elements.submit();
    }

    function ey_fleshVerify(id)
    {
        var src = "{$this->root_dir}/index.php?m=api&c=Ajax&a=vertify&type=guestbook&lang={$this->home_lang}";
        src += "&r="+ Math.floor(Math.random()*100);
        document.getElementById(id).src = src;
    }

    function {$funname}()
    {
        //步骤一:创建异步对象
        var ajax = new XMLHttpRequest();
        //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
        ajax.open("post", "{$this->root_dir}/index.php?m=api&c=Ajax&a=get_token&name=__token__{$token_id}", true);
        // 给头部添加ajax信息
        ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
        // 如果需要像 HTML 表单那样 POST 数据，请使用 setRequestHeader() 来添加 HTTP 头。然后在 send() 方法中规定您希望发送的数据：
        ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        //步骤三:发送请求+数据
        ajax.send('_ajax=1');
        //步骤四:注册事件 onreadystatechange 状态改变就会调用
        ajax.onreadystatechange = function () {
            //步骤五 如果能够进到这个判断 说明 数据 完美的回来了,并且请求的页面是存在的
            if (ajax.readyState==4 && ajax.status==200) {
                document.getElementById("{$token_id}").value = ajax.responseText;
                document.getElementById("gourl_{$token_id}").value = window.location.href;
          　}
        } 
    }
    {$funname}();
    function getNext1598839807(id,name,level) {
        var input = document.getElementById('attr_'+name);
        var first = document.getElementById('first_id_'+name);
        var second = document.getElementById('second_id_'+name);
        var third = document.getElementById('third_id_'+name);
        var findex ='', fvalue = '',sindex = '',svalue = '',tindex = '',tvalue = '',value='';

        if (level == 1){
            if (second) {
                second.style.display = 'none';
                second.innerHTML  = ''; 
            }
            if (third) {
                third.style.display = 'none';
                third.innerHTML  = '';
            }
            findex = first.selectedIndex;
            fvalue = first.options[findex].value;
            input.value = fvalue;
            value = fvalue;
        } else if (level == 2){
            if (third) {
                third.style.display = 'none';
                third.innerHTML  = '';
            }
            findex = first.selectedIndex;
            fvalue = first.options[findex].value;
            sindex = second.selectedIndex;
            svalue = second.options[sindex].value;
            if (svalue) {
                input.value = fvalue+','+svalue;
                value = svalue;
            }else{
                input.value = fvalue;
            }
        } else if (level == 3){
            findex = first.selectedIndex;
            fvalue = first.options[findex].value;
            sindex = second.selectedIndex;
            svalue = second.options[sindex].value;
            tindex = third.selectedIndex;
            tvalue = third.options[tindex].value;
            if (tvalue) {
                input.value = fvalue+','+svalue+','+tvalue;
                value = tvalue;
            }else{
                input.value = fvalue+','+svalue;
            }
        } 
        if (value) {
            if(document.getElementById(id))
            {
                document.getElementById(id).options.add(new Option('请选择','')); 
                var ajax = new XMLHttpRequest();
                //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
                ajax.open("post", "{$this->root_dir}/index.php?m=api&c=Ajax&a=get_region&pid="+value, true);
                // 给头部添加ajax信息
                ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
                // 如果需要像 HTML 表单那样 POST 数据，请使用 setRequestHeader() 来添加 HTTP 头。然后在 send() 方法中规定您希望发送的数据：
                ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                //步骤三:发送请求+数据
                ajax.send('_ajax=1');
                //步骤四:注册事件 onreadystatechange 状态改变就会调用
                ajax.onreadystatechange = function () {
                    //步骤五 如果能够进到这个判断 说明 数据 完美的回来了,并且请求的页面是存在的
                    if (ajax.readyState==4 && ajax.status==200) {
                        var data = JSON.parse(ajax.responseText).data;
                        if (data) {
                            data.forEach(function(item) {
                           document.getElementById(id).options.add(new Option(item.name,item.id)); 
                           document.getElementById(id).style.display = "block";

                        });
                        }
                  　}
                }
            }
        }
    }
</script>
EOF;
            $seo_pseudo = tpCache('seo.seo_pseudo');
            $gourl = $this->request->url(true);
            if (2 == $seo_pseudo) {
                $gourl = $this->request->domain().$this->root_dir;
            }
            $hidden = '<input type="hidden" name="gourl" id="gourl_'.$token_id.'" value="'.$gourl.'" /><input type="hidden" name="typeid" value="'.$typeid.'" /><input type="hidden" name="__token__'.$token_id.'" id="'.$token_id.'" value="" />'.$tokenStr;
            $newAttribute['hidden'] = $hidden;

            $action = $this->root_dir."/index.php?m=home&c=Lists&a=gbook_submit&lang={$this->home_lang}";
            $newAttribute['action'] = $action;
            $newAttribute['formhidden'] = ' enctype="multipart/form-data" ';
            $newAttribute['submit'] = "return {$submit}(this);";

            /*验证码处理*/
            // 默认开启验证码
            $IsVertify = 1;
            $guestbook_captcha = config('captcha.guestbook');
            if (!function_exists('imagettftext') || empty($guestbook_captcha['is_on'])) {
                $IsVertify = 0; // 函数不存在，不符合开启的条件
            }
            $newAttribute['IsVertify'] = $IsVertify;
            if (1 == $IsVertify) {
                // 留言验证码数据
                $VertifyUrl = url('api/Ajax/vertify',['type'=>'guestbook','token'=>'__token__'.$token_id,'r'=>mt_rand(0,10000)]);
                $newAttribute['VertifyData'] = " src='{$VertifyUrl}' id='verify_{$token_id}' onclick='ey_fleshVerify(\"verify_{$token_id}\");' ";
            }
            /* END */

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
        $attributeList = Db::name('GuestbookAttribute')->where("typeid = $typeid")
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