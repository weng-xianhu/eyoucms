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
use think\Db;

class TagForm extends Base
{
    private $come_from = '';
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        if ($this->aid){
            $archives = Db::name('Archives')->field('title,typeid')->where([ 'aid'=> $this->aid])->find();
            $typename = Db::name('Arctype')->where(['id'=> $archives['typeid']])->getField('typename');
            $this->come_from = $typename.'>'.$archives['title'];
        }
        if(empty($this->come_from) && $this->tid){
            $this->come_from = Db::name('Arctype')->where(['id'=> $this->tid])->getField('typename');
        }
        if(empty($this->come_from)){
            $this->come_from = tpCache('web.web_title');
        }
    }
    /**
     * 获取表单数据
     */
    public function getForm($formid = '', $success = '', $beforeSubmit = '',$is_count = '',$is_list = '')
    {
        $where = [];
        if (empty($formid) ) {
            echo '标签form报错：缺少属性 formid 值。';
            return false;
        }else{
            $where['form_id'] =  intval($formid);
        }

        $form = Db::name('form')->where($where)->find();
        if (empty($form)){
            echo '标签form报错：'.$formid.'表单不存在。';
            return false;
        }
        $formid = $form['form_id'];
        $form_field = Db::name('form_field')->where([
            'form_id'   => $form['form_id'],
        ])->order("sort_order asc, field_id asc")->select();
        if (empty($form_field)){
            echo '标签form报错：表单没有新增字段。';
            return false;
        }
        $form_field_grep_list = config("global.form_field_grep_list");
        $baoming_count = 0;
        $baominglist = $value_list = [];
        if ($is_count){
            $baoming_count = Db::name('form_list')->where(["form_id"=>$formid])->count();
        }
        if ($is_list){
            $value_list = Db::name('form_value')
                ->alias('a')
                ->join('form_list b','b.list_id = a.list_id','LEFT')
                ->field('a.*')
                ->where(["a.form_id"=>$formid])
                ->order('list_id asc')
                ->limit($is_list)
                ->select();
        }
        $ajax_form = input('param.ajax_form/d'); // 是否ajax弹窗报名，还是页面显示报名
        $md5 = md5(getTime().uniqid(mt_rand(), TRUE));
        $funname = 'f'.md5("eju_form_token_{$form['form_id']}".$md5);
        $form_name = 'form_'.$funname;
        $token_id = md5('form_token_'.$form['form_id'].$md5);
        $submit = 'f'.$token_id;
//        $input_rule_list = config("global.input_rule");
        $result = json_decode( json_encode( $form),true);
        $version   = getCmsVersion();
        $check_js = "
        var x = document.getElementById('".$form_name."');
        var radio_arr = [];
        for (var i=0;i<x.length;i++){
        ";   //检测不能为空和正则规范规则
        $datetime_js = "";  //如果存在时间戳字段，则调用
        $field_arr = [];
        $is_default = 0;
        foreach ($form_field as $key=>$val){
            if ($val['is_default'] == 1){
                $is_default = $val['field_id'];
            }
            $field_id = $val['field_id'];
            $field_arr[$field_id] = "";
            /*字段name*/
            $name = 'field_'.$field_id;
            $result[$name] = $name;
            /*字段id*/
            $id_name = 'field_id_'.$field_id;
            $id_var =  'field_id_'.$token_id.'_'.$field_id;
            $result[$id_name] = $id_var;
            /*字段名称*/
            $itemname = 'itemname_'.$field_id;
            $result[$itemname] = $val['field_name'];
            //字段显示名称
            $placeholder = 'placeholder_'.$field_id;
            $result[$placeholder] = $val['field_value'];
            /*
             * 筛选内容
             */
            $options = array();
            if (!empty($val['field_value'])) {
                $tmp_option_val = explode(',', $val['field_value']);
                foreach($tmp_option_val as $k2=>$v2)
                {
                    $tmp_val = array(
                        'value' => $v2,
                    );
                    array_push($options, $tmp_val);
                }
            }
            //关联区域（特殊）
            if ($val['field_type']== 'region') {
                $result['first_id_'.$field_id]=" id='first_id_{$token_id}_{$field_id}' onchange=\"getNext1646790277('second_id_{$token_id}_{$field_id}','{$token_id}_{$field_id}',1);\" ";
                $result['second_id_'.$field_id]=" id='second_id_{$token_id}_{$field_id}' onchange=\"getNext1646790277('third_id_{$token_id}_{$field_id}','{$token_id}_{$field_id}',2);\" style='display:none;'";
                $result['third_id_'.$field_id]=" id='third_id_{$token_id}_{$field_id}' onchange=\"getNext1646790277('', '{$token_id}_{$field_id}',3);\"  style='display:none;' ";
                $result['hidden_'.$field_id]= "<input type='hidden' name='{$name}' id='{$id_var}'>";
                $field_value = unserialize($val['field_value']);
                $result['options_'.$field_id] = Db::name('region')->where('id','in',$field_value['region_ids'])->select();
            }else{
                $result['options_'.$field_id] = $options;
            }
            //是否必填（js判断）
            if ($val['is_fill']){
                $check_js .= "
                    if(x[i].name == '".'field_'.$val['field_id']."' && x[i].type == 'radio'){
                        if(radio_arr.hasOwnProperty('".$val['field_name']."') && radio_arr.".$val['field_name']." === true){
                        }else{
                            if(x[i].checked === false){
                               radio_arr.".$val['field_name']." = false;
                           }else{
                              radio_arr.".$val['field_name']." = true;
                           }
                        }
                    }else if(x[i].name == '".'field_'.$val['field_id']."' && x[i].value.length == 0){
                         alert('".$val['field_name']."不能为空！');
                         return false;
                    }
                ";
            }
            //是否正则限制（js判断）  $form_field_grep_list
            if (!empty($val['field_type']) && !empty($form_field_grep_list[$val['field_type']]['value'])){
                $check_js .= " 
                    if(x[i].name == 'field_".$val['field_id']."' && !(".$form_field_grep_list[$val['field_type']]['value'].".test( x[i].value))){
                        alert('".$val['field_name']."格式不正确！');
                        return false;
                    }
                   ";
            }
            if ($val['field_type'] == 'datetime'){  //时间戳类型
                $datetime_js .=  "layui.use('laydate', function(){
								       var layui_laydate = layui.laydate;
                                         layui_laydate.render({
									       elem: '#".$id_var."'
									       ,type: 'datetime'
								       });
								    });";
            }
        }
        $check_js .= "}";

        if (!empty($beforeSubmit)) {
            $beforeSubmit = "try{if(false=={$beforeSubmit}()){return false;}}catch(e){}";
        }

        if (!empty($success)){
            $success .= "();";
        } else if (1 == $ajax_form) {
            $success = <<<EOF
    try{
        layui.use('laydate', function(){
           var layer = layui.layer;
           var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
            parent.layer.close(index);
        });
        
    }catch(e){}
EOF;
        }
        //判断是否存在时间戳标签
        $tokenStr = <<<EOF
<script>window.jQuery || document.write('<script language="javascript" type="text/javascript" src="{$this->root_dir}/public/static/common/js/jquery.min.js?v={$version}"><\/script>')</script>
<script>window.layui || document.write('<script language="javascript" type="text/javascript" src="{$this->root_dir}/public/plugins/layui/layui.js?v={$version}"><\/script>')</script>
<script type="text/javascript">
    {$datetime_js}
    function {$submit}()
    {
        {$check_js}
        {$beforeSubmit}
        for(var i in radio_arr){
            if(radio_arr[i] == false){
                alert(i+"不能为空！");
                 return false;
            }
        }
        var elements = document.getElementById("{$form_name}");
        var formData =new FormData();
        for(var i=0; i<elements.length; i++)
        {
            if (elements[i].type == 'radio' || elements[i].type == 'checkbox'){
                if (elements[i].checked === true){
                    const entries = formData.entries();  
                    const data = Object.fromEntries(entries);
                    if (elements[i].name in data){
                        const new_value = data[elements[i].name] + "," + elements[i].value;
                        formData.append(elements[i].name,new_value);
                    }else {
                        formData.append(elements[i].name,elements[i].value);
                    }
                }
            }else if(elements[i].type == 'file'){
                 formData.append(elements[i].name,elements[i].files[0]);
            }else{
                formData.append(elements[i].name,elements[i].value);
            }
        }
        formData.append('_ajax', '1');
        var ajax = new XMLHttpRequest();
//        ajax.contentType = false;
//        ajax.processData = false;
        ajax.open("post", "{$this->root_dir}/index.php?m=api&c=Ajax&a=form_submit"); 
        ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
        ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        ajax.send(formData); 
        
        ajax.onreadystatechange = function()
        {
            if(ajax.readyState == 4 && ajax.status == 200)
            {
                var json = ajax.responseText;
                var res = JSON.parse(json);
                if (1 == res.code) {
                    reset_form(elements);
                    {$funname}();
                    alert(res.msg);
                    
                    {$success}
                } else {
                   alert(res.msg);
                    {$funname}();
                }
            }
        }
        
        return false;
    }

    function {$funname}()
    {
        //步骤一:创建异步对象
        var ajax = new XMLHttpRequest();
        //步骤二:设置请求的url参数,参数一是请求的类型,参数二是请求的url,可以带参数,动态的传递参数starName到服务端
        ajax.open("post", "{$this->root_dir}/index.php?m=api&c=Ajax&a=get_token", true);
        ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
        ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        //步骤三:发送请求+数据
        ajax.send("name=__token__{$token_id}");
        //步骤四:注册事件 onreadystatechange 状态改变就会调用
        ajax.onreadystatechange = function () {
            //步骤五 如果能够进到这个判断 说明 数据 完美的回来了,并且请求的页面是存在的
            if (ajax.readyState==4 && ajax.status==200) {
        　　　　document.getElementById("{$token_id}").value = ajax.responseText;
          　}
        }
    }
    //重置form表单
    function reset_form(Clear_From){
        for(var a = 0; a< Clear_From.elements.length; a++) {
            if(Clear_From.elements[a].type == "text") {				//类型为text的
                Clear_From.elements[a].value = "";
            } else if(Clear_From.elements[a].type == "password") {	//类型为password的
                Clear_From.elements[a].value = "";
            } else if(Clear_From.elements[a].type == "radio") {		//类型为radio的
                Clear_From.elements[a].checked = false;
            } else if(Clear_From.elements[a].type == "checkbox") {	//类型为
                Clear_From.elements[a].checked = false;
            } else if(Clear_From.elements[a].type == "select-one" && Clear_From.elements[a].options.length > 0){	//单选下拉菜单
                Clear_From.elements[a].options[0].selected = true;	//选中第一个options
            } else if(Clear_From.elements[a].type == "select-multiple") { //多选下拉菜单
                for(var b = 0; b< Clear_From.elements[a].options.length; b++) { //将所有options设为false
                    Clear_From.elements[a].options[b].selected = false;
                }
            } else if(Clear_From.elements[a].type == "textarea") {
                Clear_From.elements[a].value = "";
            }
        }
    }
    {$funname}();
    function getNext1646790277(id,name,level) {
//        var input = document.getElementById('field_'+name);
//        var first = document.getElementById('first_id_'+name);
//        var second = document.getElementById('second_id_'+name);
//        var third = document.getElementById('third_id_'+name);
        var input = document.getElementById('field_id_'+name);
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
                ajax.open("post", "{$this->root_dir}/index.php?m=api&c=Ajax&a=get_region", true);
                // 给头部添加ajax信息
                ajax.setRequestHeader("X-Requested-With","XMLHttpRequest");
                // 如果需要像 HTML 表单那样 POST 数据，请使用 setRequestHeader() 来添加 HTTP 头。然后在 send() 方法中规定您希望发送的数据：
                ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                //步骤三:发送请求+数据
                ajax.send("pid="+value);
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
        if (!empty($value_list)){
            foreach ($value_list as $k=>$v){
                if (empty($baominglist[$v['list_id']])){
                    $baominglist[$v['list_id']] = $field_arr;
                    $baominglist[$v['list_id']]['add_time'] = $v['add_time'];
                }
                if (!empty($v['field_value']) && mb_strlen($v['field_value']) > 10){
                    $baominglist[$v['list_id']][$v['field_id']] =  mb_substr($v['field_value'], 0, 3, 'utf-8') . '***' ;
                }else if(!empty($v['field_value'])){
                    $baominglist[$v['list_id']][$v['field_id']] =   mb_substr($v['field_value'], 0, 1, 'utf-8'). '**'  ;
                }
            }
        }
        $hidden = '<input type="hidden" name="ajax_form" value="'.$ajax_form.'" />
        <input type="hidden" name="come_from" value="'.$this->come_from.'" />
        <input type="hidden" name="parent_come_url" value="'.input('param.parent_url/s').'" />
        <input type="hidden" name="aid" value="'.$this->aid.'" />
        <input type="hidden" name="come_url" value="'.request()->url(true).'" />
        <input type="hidden" name="form_id" value="'.$form['form_id'].'" />
        <input type="hidden" name="is_default" value="'.$is_default.'" />
        <input type="hidden" name="__token__'.$token_id.'" id="'.$token_id.'" value="" />'.$tokenStr;
        $result['form_id'] = $form['form_id'];
        $result['name'] = $form_name;
        $result['hidden'] = $hidden;
        $result['action'] = url('api/Ajax/form_submit', [], true, false, 1);
        $result['submit'] = "return {$submit}();";
        $result['count'] = $baoming_count;  //报名总数
        $result['list'] = $baominglist;     //报名列表

        return [$result];
    }

}