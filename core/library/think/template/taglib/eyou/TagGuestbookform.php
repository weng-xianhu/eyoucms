<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海口快推科技有限公司，并保留所有权利。
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
    public function getGuestbookform($typeid = '', $type = 'default', $beforeSubmit = '',$form_type = 0)
    {
        $this->form_type = $form_type;
        $result = false;
        $times = getTime();
        $ga_where = [];

        if (1 == $this->form_type) {
            if (empty($typeid)){
                echo '标签form报错：缺少属性 formid 值。';
                return false;
            }
            /*多语言*/
            if (!empty($typeid)) {
                $typeid = model('LanguageAttr')->getBindValue($typeid, 'form');
                if (empty($typeid)) {
                    echo '标签form报错：找不到与第一套【'.self::$main_lang.'】语言关联绑定的属性 formid 值 。';
                    return false;
                } else {
                    if (self::$language_split) {
                        $this->lang = Db::name('form')->where(['form_id'=>$typeid])->cache(true, EYOUCMS_CACHE_TIME, 'form')->value('lang');
                        if ($this->lang != self::$home_lang) {
                            $lang_title = Db::name('language_mark')->where(['mark'=>self::$home_lang])->value('cn_title');
                            echo "标签form报错：【{$lang_title}】语言 formid 值不存在。";
                            return false;
                        }
                    }
                }
            }
            /*--end*/
        } else {
            $typeid = !empty($typeid) ? $typeid : $this->tid;
            if (empty($typeid)) {
                echo '标签guestbookform报错：缺少属性 typeid 值。';
                return false;
            }
            /*多语言*/
            if (!empty($typeid)) {
                $typeid = model('LanguageAttr')->getBindValue($typeid, 'arctype');
                if (empty($typeid)) {
                    echo '标签guestbookform报错：找不到与第一套【'.self::$main_lang.'】语言关联绑定的属性 typeid 值 。';
                    return false;
                } else {
                    if (self::$language_split) {
                        $this->lang = Db::name('arctype')->where(['id'=>$typeid])->cache(true, EYOUCMS_CACHE_TIME, 'arctype')->value('lang');
                        if ($this->lang != self::$home_lang) {
                            $lang_title = Db::name('language_mark')->where(['mark'=>self::$home_lang])->value('cn_title');
                            echo "标签guestbookform报错：【{$lang_title}】语言 typeid 值不存在。";
                            return false;
                        }
                    }
                }
            }
            /*--end*/
        }
        $ga_where['typeid'] = $typeid;
        $ga_where['form_type'] = $this->form_type;
        $ga_where['is_del'] = 0;

        /*当前栏目/表单下的表单属性*/
        $attributeList = Db::name('guestbook_attribute')
            ->where($ga_where)
            ->order('sort_order asc, attr_id asc')
            ->select();
        /*--end*/
        if (empty($attributeList)) {
            if (1 == $this->form_type) {
                echo '标签form报错：该表单下没有新增表单属性。';
            } else {
                echo '标签guestbookform报错：该栏目下没有新增留言属性。';
            }
            return false;
        } else {
            /*获取多语言关联绑定的值*/
            if (1 == $this->form_type) {
                $attributeList = model('LanguageAttr')->getBindValue($attributeList, 'form_attribute', self::$main_lang); // 多语言
            } else {
                $attributeList = model('LanguageAttr')->getBindValue($attributeList, 'guestbook_attribute', self::$main_lang); // 多语言
            }
            /*--end*/

            $realValidate = [];
            $newAttribute = array();
            $attr_input_type_1 = 1; // 兼容v1.1.6之前的版本
            $validate_type_list = config("global.validate_type_list"); //检测规则
            $check_js = '';
            foreach ($attributeList as $key => $val) {
                $attr_id = $val['attr_id'];
                /*字段名称*/
                $name = 'attr_'.$attr_id;
                if (in_array($val['attr_input_type'], [4, 11])) { // 多选框、上传图片或附件
                    $newAttribute[$name] = $name."[]";
                } else {
                    $newAttribute[$name] = $name;
                }
                /*--end*/
                $newAttribute['itemname_'.$attr_id] = $val['attr_name']; // 表单提示文字
                if (in_array($val['attr_input_type'], [1,3,4])) { // 单选/多选/下拉选择框
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
                }elseif ($val['attr_input_type']==9) { // 区域联动
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
                        $alert_msg = sprintf(foreign_lang('gbook13', self::$home_lang), $val['attr_name']);
                        $alert_msg = str_replace("'", "\'", $alert_msg);
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
                                    alert('{$alert_msg}');
                                    return false;
                                }
                            }
                        ";
                    } else if ($val['attr_input_type'] == 3) { // 单选框
                        $alert_msg = sprintf(foreign_lang('gbook14', self::$home_lang), $val['attr_name']);
                        $alert_msg = str_replace("'", "\'", $alert_msg);
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
                                    alert('{$alert_msg}');
                                    return false;
                                }
                            }
                        ";
                    } else {
                        $alert_msg = sprintf(foreign_lang('gbook3', self::$home_lang), $val['attr_name']);
                        $alert_msg = str_replace("'", "\'", $alert_msg);
                        $check_js .= "
                            if(x[i].name == 'attr_".$val['attr_id']."' && x[i].value.length == 0){
                                alert('{$alert_msg}');
                                return false;
                            }
                        ";
                    }
                }

                //是否正则限制（js判断）
                if (!empty($val['validate_type']) && !empty($validate_type_list[$val['validate_type']]['value'])){
                    $alert_msg = sprintf(foreign_lang('gbook4', self::$home_lang), $val['attr_name']);
                    $alert_msg = str_replace("'", "\'", $alert_msg);
                    $check_js .= " 
                    if(x[i].name == 'attr_".$val['attr_id']."' && !(".$validate_type_list[$val['validate_type']]['value'].".test( x[i].value)) && x[i].value.length > 0){
                        alert('{$alert_msg}！');
                        return false;
                    }
                   ";
                }

                // 是否为手机号码类型 且 需要发进行真实验证
                if (6 === intval($val['attr_input_type']) && 1 === intval($val['real_validate'])) $realValidateData = $val;
            }

            // 如果存在需要真实验证则执行
            $realValidate = [];
            if (!empty($realValidateData)) {
                $tokenID = md5('guestbookform_token_phone_'.$typeid.md5(getTime().uniqid(mt_rand(), TRUE)));
                $vertifySrc = url('api/Ajax/vertify', ['type' => 'guestbook', 'token' => '__token__'  .$tokenID, 'r' => mt_rand(0, 10000)]);
                $realValidate = [
                    'verifyInput' => ' type="text" name="real_validate_input" id="real_validate_input" autocomplete="off" ',
                    'verifyClick' => " href=\"javascript:void(0);\" onclick=\"ey_fleshVerify_{$times}('verify_{$tokenID}');\" ",
                    'verifyImg' => " id=\"verify_{$tokenID}\" src=\"{$vertifySrc}\" onclick=\"ey_fleshVerify_{$times}('verify_{$tokenID}');\" ",
                    'phoneInput' => ' type="text" name="real_validate_phone_input" id="real_validate_phone_input" autocomplete="off" ',
                    'phoneClick' => " type=\"button\" id=\"real_validate_phone_click\" onclick=\"realValidatePhoneClick('".$realValidateData['attr_id']."');\" ",
                ];

                $mobile_alert_msg = str_replace("'", "\'", foreign_lang('gbook15', self::$home_lang));
                $verify_alert_msg = str_replace("'", "\'", foreign_lang('gbook5', self::$home_lang));

                // 真实验证所需JS
                $realValidateJS = <<<EOF
<script type="text/javascript">
    function realValidatePhoneClick(id) {
        var phone = document.getElementById('attr_' + id).value;
        var code = document.getElementById('real_validate_input').value;
        var codeToken = document.getElementById('real_validate_token').value;
        if (phone.length == 0 || phone.length != 11) {
            alert('{$mobile_alert_msg}');
            return false;
        } else if (code.length == 0) {
            alert('{$verify_alert_msg}');
            return false;
        }
        var ajaxdata = 'phone='+phone+'&code='+code+'&code_token='+codeToken+'&scene=7&_ajax=1';

        var ajax = new XMLHttpRequest();
        ajax.open("post", "{$this->root_dir}/index.php?m=api&c=Ajax&a=SendMobileCode", true);
        ajax.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send(ajaxdata);
        ajax.onreadystatechange = function () {
            if (ajax.readyState==4 && ajax.status==200) {
                var msg = JSON.parse(ajax.responseText).msg;
                alert(msg);
          　}
        }
    }
</script>
EOF;
                // 隐藏域内容
                $realValidate['verifyHidden'] = '<input type="hidden" name="real_validate" value="'.$realValidateData['real_validate'].'" /><input type="hidden" name="real_validate_attr_id" value="attr_'.$realValidateData['attr_id'].'" /><input type="hidden" name="real_validate_token" id="real_validate_token" value="__token__'.$tokenID.'" />' . $realValidateJS;
            }
            $newAttribute['realValidate'] = $realValidate;

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
            $home_lang = self::$home_lang;
            $gbook14 = sprintf(foreign_lang('gbook14', self::$home_lang), '');
	    $gbook14 = str_replace("'", "\'", $gbook14);
            $tokenStr = <<<EOF
<script type="text/javascript">
    function {$submit}(elements)
    {
        if (document.getElementById('gourl_{$token_id}')) {
            document.getElementById('gourl_{$token_id}').value = encodeURIComponent(window.location.href);
        }
        {$check_js}
        {$beforeSubmit}
        elements.submit();
    }

    function ey_fleshVerify_{$times}(id)
    {
        var token = id.replace(/verify_/g, '__token__');
        var src = "{$this->root_dir}/index.php?m=api&c=Ajax&a=vertify&type=guestbook&lang={$home_lang}&token="+token;
        src += "&r="+ Math.floor(Math.random()*100);
        document.getElementById(id).src = src;
    }

    function {$funname}()
    {
        var ajax = new XMLHttpRequest();
        ajax.open("post", "{$this->root_dir}/index.php?m=api&c=Ajax&a=get_token", true);
        ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
        ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        ajax.send("name=__token__{$token_id}");
        ajax.onreadystatechange = function () {
            if (ajax.readyState==4 && ajax.status==200) {
                document.getElementById("{$token_id}").value = ajax.responseText;
                document.getElementById("gourl_{$token_id}").value = encodeURIComponent(window.location.href);
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
                document.getElementById(id).options.add(new Option('{$gbook14}','')); 
                var ajax = new XMLHttpRequest();
                ajax.open("post", "{$this->root_dir}/index.php?m=api&c=Ajax&a=get_region", true);
                ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
                ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                ajax.send("pid="+value);
                ajax.onreadystatechange = function () {
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
            $gourl = self::$request->url(true);
            if (2 == $seo_pseudo) {
                $gourl = self::$request->domain().$this->root_dir;
            }
            $gourl = urlencode($gourl);
            $hidden = '<input type="hidden" name="gourl" id="gourl_'.$token_id.'" value="'.$gourl.'" />';
            $hidden .= '<input type="hidden" name="typeid" value="'.$typeid.'" />';
            $hidden .= '<input type="hidden" name="__token__'.$token_id.'" id="'.$token_id.'" value="" />';
            $hidden .= '<input type="hidden" name="form_type" value="'.$this->form_type.'" />';
            $hidden .= $tokenStr;
            $newAttribute['hidden'] = $hidden;

            $action = $this->root_dir."/index.php?m=home&c=Lists&a=gbook_submit&lang={$home_lang}";
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
                $newAttribute['VertifyData'] = " src=\"{$VertifyUrl}\" id=\"verify_{$token_id}\" onclick=\"ey_fleshVerify_{$times}('verify_{$token_id}');\" ";
            }
            /* END */

            if ('auto' == $type) {
                $newAttribute['attrlist'] = $this->getAutoAttrInput($typeid, $newAttribute, $attributeList, $this->form_type);
            } else {
                $newAttribute['attrlist'] = [];
            }

            $result[0] = $newAttribute;
        }
        return $result;
    }

    /**
     * 动态获取留言栏目属性输入框 根据不同的数据返回不同的输入框类型
     * @param int $typeid 栏目/表单id
     */
    private function getAutoAttrInput($typeid, $newAttribute = [], $attributeList = [], $form_type = 0)
    {
        header("Content-type: text/html; charset=utf-8");
        $form_arr = array();
        foreach($attributeList as $key => $val)
        {
            $attr_id = $val['attr_id'];
            $attr_html = "";
            switch ($val['attr_input_type']) {
                case '1': // 下拉框
                    $msg = sprintf(foreign_lang('gbook14', self::$home_lang), '');
                    $attr_html .= "<div class='eyou_form_attr' id='eyou_form_div_{$attr_id}'>\n";
                    $attr_html .= "    <select class='eyou_form_select' id='attr_{$attr_id}' name='attr_{$attr_id}'>\n";
                    $attr_html .= "        <option value=''>{$msg}{$val['attr_name']}</option>\n";
                    $option_arr = explode(PHP_EOL, $val['attr_values']);
                    foreach ($option_arr as $k2=>$v2)
                    {
                        $attr_html .= "        <option value='{$v2}'>{$v2}</option>\n";
                    }
                    $attr_html .= "   </select>\n";
                    $attr_html .= "</div>\n";
                    break;
                
                case '2': // 多行文本
                    $attr_html .= "<div class='eyou_form_attr' id='eyou_form_div_{$attr_id}'>\n";
                    $attr_html .= "    <textarea class='eyou_form_textarea' id='attr_{$attr_id}' cols='40' rows='3' name='attr_{$attr_id}' placeholder='{$val['attr_name']}' autocomplete='off'>{$val['attr_values']}</textarea>\n";
                    $attr_html .= "</div>\n";
                    break;

                case '3': // 单选框
                    $attr_html .= "<div class='eyou_form_attr' id='eyou_form_div_{$attr_id}'>\n";
                    $option_arr = explode(PHP_EOL, $val['attr_values']);
                    foreach ($option_arr as $k2=>$v2)
                    {
                        $attr_html .= "    <label><input type='radio' class='eyou_form_radio attr_{$attr_id}' name='attr_{$attr_id}' value='{$v2}'>&nbsp;{$v2}</label>\n";
                    }
                    $attr_html .= "</div>\n";
                    break;

                case '4': // 多选框
                    $attr_html .= "<div class='eyou_form_attr' id='eyou_form_div_{$attr_id}'>\n";
                    $option_arr = explode(PHP_EOL, $val['attr_values']);
                    foreach ($option_arr as $k2=>$v2)
                    {
                        $attr_html .= "    <label><input type='checkbox' class='eyou_form_checkbox attr_{$attr_id}' name='attr_{$attr_id}[]' value='{$v2}'>&nbsp;{$v2}</label>\n";
                    }
                    $attr_html .= "</div>\n";
                    break;
                
                case '5': // 单张图
                    $attr_html .= "<div class='eyou_form_attr' id='eyou_form_div_{$attr_id}'>\n";
                    $attr_html .= "    <input type='file' class='eyou_form_file' id='attr_{$attr_id}' name='attr_{$attr_id}' accept='image/*' value='' />\n";
                    $attr_html .= "</div>\n";
                    break;
                
                case '6': // 手机号码
                    $attr_html .= "<div class='eyou_form_attr' id='eyou_form_div_{$attr_id}'>\n";
                    $attr_html .= "    <input type='text' class='eyou_form_text' id='attr_{$attr_id}' name='attr_{$attr_id}' value='' placeholder='{$val['attr_name']}' autocomplete='off' />\n";
                    $attr_html .= "</div>\n";
                    if (!empty($val['real_validate'])) {
                        $verifmsg = foreign_lang('gbook16', self::$home_lang);
                        $phonemsg = foreign_lang('gbook17', self::$home_lang);
                        $phonecodemsg = foreign_lang('gbook18', self::$home_lang);
                        $verifyImgTitle = foreign_lang('gbook19', self::$home_lang);
                        $verifyImgBtn = sprintf(foreign_lang('gbook20', self::$home_lang), '<a class="eyou_form_verify_a" '.$newAttribute['realValidate']['verifyClick'].'>', '</a>');
                        $attr_html .= "<div class='eyou_form_attr' id='eyou_form_verify_{$attr_id}'>\n";
                        $attr_html .= "    <input class='eyou_form_text' {$newAttribute['realValidate']['verifyInput']} placeholder='{$verifmsg}' />\n";
                        $attr_html .= "    <img class='eyou_form_verify_img' {$newAttribute['realValidate']['verifyImg']} title='{$verifyImgTitle}' /> {$verifyImgBtn}\n";
                        $attr_html .= "</div>\n";
                        $attr_html .= "<div class='eyou_form_attr' id='eyou_form_phone_{$attr_id}'>\n";
                        $attr_html .= "    <input class='eyou_form_phone' {$newAttribute['realValidate']['phoneInput']} placeholder='{$phonemsg}'>\n";
                        $attr_html .= "    <input class='eyou_form_phone_btn' {$newAttribute['realValidate']['phoneClick']} value='{$phonecodemsg}' />\n";
                        $attr_html .= "</div>\n";
                        $attr_html .= $newAttribute['realValidate']['verifyHidden']."\n";
                    }
                    break;
                
                case '8': // 附件类型
                    $attr_html .= "<div class='eyou_form_attr' id='eyou_form_div_{$attr_id}'>\n";
                    $attr_html .= "    <input type='file' class='eyou_form_file' id='attr_{$attr_id}' name='attr_{$attr_id}' value='' />\n";
                    $attr_html .= "</div>\n";
                    break;
                
                case '9': // 区域联动
                    $msg = sprintf(foreign_lang('gbook14', self::$home_lang), '');
                    $attr_html .= "<div class='eyou_form_attr' id='eyou_form_div_{$attr_id}'>\n";
                    $attr_html .= "    <select class='eyou_form_select' ".$newAttribute['first_id_'.$attr_id].">\n";
                    $attr_html .= "        <option value=''>{$msg}{$val['attr_name']}</option>\n";
                    foreach ($newAttribute['options_'.$attr_id] as $k2=>$v2)
                    {
                        $attr_html .= "        <option value='{$v2['id']}'>{$v2['name']}</option>\n";
                    }
                    $attr_html .= "   </select>\n";
                    $attr_html .= "   <select class='eyou_form_select' ".$newAttribute['second_id_'.$attr_id]."></select>\n";
                    $attr_html .= "   <select class='eyou_form_select' ".$newAttribute['third_id_'.$attr_id]."></select>\n";
                    $attr_html .= "</div>\n";
                    $attr_html .= $newAttribute['hidden_'.$attr_id]."\n";
                    break;
                
                case '10': // 时间类型
                    $version = getCmsVersion();
                    $attr_html .= "<div class='eyou_form_attr' id='eyou_form_div_{$attr_id}'>\n";
                    $attr_html .= "    <input type='text' class='eyou_form_text' id='attr_{$attr_id}' name='attr_{$attr_id}' value='' placeholder='{$val['attr_name']}' autocomplete='off' />\n";
                    $attr_html .= "    <script language='javascript' type='text/javascript' src='{$this->root_dir}/public/plugins/laydate-v5.3.1/laydate.js?v={$version}'></script>\n";
                    $attr_html .= "    <script type='text/javascript'>\n";
                    $attr_html .= "        laydate.render({elem: '#attr_{$attr_id}',type: 'datetime'});\n";
                    $attr_html .= "    </script>\n";
                    $attr_html .= "</div>\n";
                    break;
                
                case '11': // 多张图
                    $attr_html .= "<div class='eyou_form_attr' id='eyou_form_div_{$attr_id}'>\n";
                    $attr_html .= "    <input type='file' class='eyou_form_file' id='attr_{$attr_id}' name='attr_{$attr_id}[]' multiple accept='image/*' value='' />\n";
                    $attr_html .= "</div>\n";
                    break;
                
                default: // 单行文本\Email邮箱
                    $attr_html .= "<div class='eyou_form_attr' id='eyou_form_div_{$attr_id}'>\n";
                    $attr_html .= "    <input type='text' class='eyou_form_text' id='attr_{$attr_id}' name='attr_{$attr_id}' value='{$val['attr_values']}' placeholder='{$val['attr_name']}' autocomplete='off' />\n";
                    $attr_html .= "</div>\n";
                    break;
            }

            $form_arr[$key] = array(
                'attr_html' => $attr_html,
                'attr_name' => $val['attr_name'],
            );
        }        
        return  $form_arr;
    }
}