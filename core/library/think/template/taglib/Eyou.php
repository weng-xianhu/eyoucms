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

namespace think\template\taglib;

use think\template\TagLib;

/**
 * eyou标签库解析类
 * @category   Think
 * @package  Think
 * @subpackage  Driver.Taglib
 * @author    小虎哥 <1105415366@qq.com>
 */
class Eyou extends Taglib
{

    // 标签定义
    protected $tags = [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'php'        => ['attr' => ''],
        'channel'    => ['attr' => 'typeid,reid,type,row,currentstyle,id,name,key,empty,mod,titlelen,offset,limit'],
        'channelartlist' => ['attr' => 'typeid,type,row,id,key,empty,titlelen,mod'],
        'arclist'    => ['attr' => 'channelid,typeid,notypeid,row,offset,titlelen,limit,orderby,orderWay,noflag,flag,infolen,empty,mod,name,id,key,addfields,tagid,pagesize'],
        'arcpagelist'=> ['attr' => 'tagid,pagesize,id,tips,loading'],
        'list'       => ['attr' => 'channelid,typeid,notypeid,pagesize,titlelen,orderby,orderWay,noflag,flag,infolen,empty,mod,id,key,addfields'],
        'pagelist'   => ['attr' => 'listitem,listsize', 'close' => 0],
        'position'   => ['attr' => 'symbol,style', 'close' => 0],
        'type'       => ['attr' => 'typeid,type,empty,dirname,id,addfields,addtable'],
        'arcview'    => ['attr' => 'aid,empty,id,addfields'],
        'arcclick'   => ['attr' => '', 'close' => 0],
        'load'       => ['attr' => 'file,href,type,value,basepath', 'close' => 0, 'alias' => ['import,css,js', 'type']],
        'guestbookform'=> ['attr' => 'typeid,type,empty,id,mod,key'],
        'assign'     => ['attr' => 'name,value', 'close' => 0],
        'empty'      => ['attr' => 'name'],
        'notempty'   => ['attr' => 'name'],
        'foreach'    => ['attr' => 'name,id,item,key,offset,length,mod', 'expression' => true],
        'volist'     => ['attr' => 'name,id,offset,length,key,mod,limit,row', 'alias' => 'iterate'],
        'if'         => ['attr' => 'condition', 'expression' => true],
        'elseif'     => ['attr' => 'condition', 'close' => 0, 'expression' => true],
        'else'       => ['attr' => '', 'close' => 0],
        'switch'     => ['attr' => 'name', 'expression' => true],
        'case'       => ['attr' => 'value,break', 'expression' => true],
        'default'    => ['attr' => '', 'close' => 0],
        'compare'    => ['attr' => 'name,value,type', 'alias' => ['eq,equal,notequal,neq,gt,lt,egt,elt,heq,nheq', 'type']],
        'ad'         => ['attr' => 'aid,id', 'close'=>1], 
        'adv'        => ['attr' => 'pid,row,order,where,id,empty,key,mod,currentstyle', 'close'=>1],  
        'global'     => ['attr' => 'name', 'close' => 0],
        'static'     => ['attr' => 'file,lang,href,code', 'close' => 0], 
        'prenext'    => ['attr' => 'get,titlelen,id,empty'],
        'field'      => ['attr' => 'name,addfields,aid', 'close' => 0], 
        'searchurl'  => ['attr' => '', 'close' => 0],
        'searchform' => ['attr' => 'channel,typeid,notypeid,flag,noflag,type,empty,id,mod,key', 'close'=>1], 
        'tag'        => ['attr' => 'aid,name,row,id,key,mod,typeid,getall,sort,empty,style'],
        'flink'      => ['attr' => 'type,row,id,key,mod,titlelen,empty,limit'],
        'language'   => ['attr' => 'type,row,id,key,mod,titlelen,empty,limit,currentstyle'], 
        'lang'       => ['attr' => 'name,const', 'close' => 0],
        'ui'         => ['attr' => 'open', 'close' => 0],
        'uitext'     => ['attr' => 'e-id,e-page,id'],
        'uihtml'     => ['attr' => 'e-id,e-page,id'],
        'uiupload'   => ['attr' => 'e-id,e-page,id'],
        'uitype'     => ['attr' => 'e-id,e-page,id,typeid'],
        'uiarclist'  => ['attr' => 'e-id,e-page,id,typeid'],
        'uichannel'  => ['attr' => 'e-id,e-page,id,typeid'],
        // 'sql'        => ['attr' => 'sql,key,id,mod,cachetime,empty', 'close'=>1, 'level'=>3], // eyou sql 万能标签
        'weapp'      => ['attr' => 'type', 'close' => 0], // 网站应用插件
        'range'      => ['attr' => 'name,value,type', 'alias' => ['in,notin,between,notbetween', 'type']],
        'present'    => ['attr' => 'name'],
        'notpresent' => ['attr' => 'name'],
        'defined'    => ['attr' => 'name'],
        'notdefined' => ['attr' => 'name'],
        'define'     => ['attr' => 'name,value', 'close' => 0],
        'for'        => ['attr' => 'start,end,name,comparison,step'],
        'url'        => ['attr' => 'link,vars,suffix,domain', 'close' => 0, 'expression' => true],
        'function'   => ['attr' => 'name,vars,use,call'],
        'diyfield'   => ['attr' => 'name,id,key,mod,type,empty,limit'],
        'attribute'  => ['attr' => 'aid,type,empty,id,mod,key'],
        'attr'       => ['attr' => 'aid,name', 'close' => 0],
        'user'       => ['attr' => 'type,id,key,mod,empty,currentstyle,img,txt,txtid'],
        'weapplist'  => ['attr' => 'type,id,key,mod,empty,currentstyle'], // 网站应用插件列表
        'usermenu'   => ['attr' => 'row,id,empty,key,mod,currentstyle,limit'], 
        // 购物行为标签
        'sppurchase' => ['attr' => 'row,id,key,mod,empty'],
        // 购物车大标签
        'spcart'     => ['attr' => 'row,id,key,mod,empty,limit'],
        // 订单明细大标签
        'sporder'    => ['attr' => 'row,id,key,mod,empty,limit'],
        // 订单提交大标签
        'spsubmitorder'=> ['attr' => 'row,id,key,mod,empty,limit'],
        // 订单管理页大标签
        'sporderlist'=> ['attr' => 'row,id,key,mod,empty,limit,pagesize'],
        // 地址标签
        'spaddress'  => ['attr' => 'type,row,id,key,mod,empty,limit'],
        // 订单产品标签
        'spordergoods'=> ['attr' => 'row,id,key,mod,empty,limit,name,titlelen'],
        // 订单状态标签
        'spstatus'   => ['attr' => 'row,id,key,mod,empty,limit'],
        // 订单管理页，分页标签
        'sppageorder'  => ['attr' => 'listitem,listsize', 'close' => 0],
        // 订单管理页搜索标签
        'spsearch' => ['attr' => 'empty,id,mod,key'],
    ];

    /**
     * 自动识别构建变量，传值可以使变量也可以是值
     * @access private
     * @param string $value 值或变量
     * @return string
     */
    private function varOrvalue($value)
    {
        $flag  = substr($value, 0, 1);
        if ('$' == $flag || ':' == $flag) {
            $value = $this->autoBuildVar($value);
        } else {
            $value = str_replace('"', '\"', $value);
            $value = '"' . $value . '"';
        }

        return $value;
    }

    /**
     * 万能的SQL标签
     */
    public function tagSql($tag, $content)
    {
        $sql = $tag['sql']; // sql 语句
        $sql  = $this->varOrvalue($sql);
                                            
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $id  =  !empty($tag['id']) ? $tag['id'] : 'field';// 返回的变量
        $cachetime  =  !empty($tag['cachetime']) ? $tag['cachetime'] : '';// 缓存时间
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);

        $parseStr = '<?php ';
        $parseStr .= ' $tagSql = new \think\template\taglib\eyou\TagSql;';
        $parseStr .= ' $_result = $tagSql->getSql('.$sql.', "'.$cachetime.'");';

        $parseStr .= 'if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        $parseStr .= ' $__LIST__ = $_result;';

        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$mod = ($e % ' . $mod . ' );';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * 重置美化标签的变量，以免相互干扰
     */
    private function resetUiVal()
    {
        return '<?php ?>';
    }

    /**
     * ui 标签解析
     * 是否开启页面装饰
     * 格式： {eyou:ui open="off" /}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagUi($tag)
    {
        $open  = !empty($tag['open']) ? $tag['open'] : '';

        $parseStr = '<?php ';
        $parseStr .= ' $tagUi = new \think\template\taglib\eyou\TagUi;';
        $parseStr .= ' $__VALUE__ = $tagUi->getUi();';
        $parseStr .= ' echo $__VALUE__;';
        $parseStr .= ' ?>';

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * 美化标签-图片编辑
     */
    public function tagUiupload($tag, $content)
    {
        $e_id = isset($tag['e-id']) ? $tag['e-id'] : '';
        $e_page = isset($tag['e-page']) ? $tag['e-page'] : '';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';

        $parseStr = '';
        $parseStr .= ' <?php ';
        $parseStr .= ' $tagUiupload = new \think\template\taglib\eyou\TagUiupload;';
        $parseStr .= ' $__LIST__ = $tagUiupload->getUiupload("'.$e_id.'", "'.$e_page.'");';
        $parseStr .= ' ?>';

        $parseStr .= '<?php if((is_array($__LIST__)) && (!empty($__LIST__["value"]) || (($__LIST__["value"] instanceof \think\Collection || $__LIST__["value"] instanceof \think\Paginator ) && $__LIST__["value"]->isEmpty()))): ?>';
        $parseStr .= '<?php $'.$id.' = $__LIST__; ?>';
        $parseStr .= $content;
        $parseStr .= '<?php endif; ?>';

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * 美化标签-栏目列表编辑
     */
    public function tagUichannel($tag, $content)
    {
        $typeid = isset($tag['typeid']) ? $tag['typeid'] : '';
        $e_id = isset($tag['e-id']) ? $tag['e-id'] : '';
        $e_page = isset($tag['e-page']) ? $tag['e-page'] : '';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';

        $parseStr = '';
        $parseStr .= ' <?php ';
        $parseStr .= ' $tagUichannel = new \think\template\taglib\eyou\TagUichannel;';
        $parseStr .= ' $__LIST__ = $tagUichannel->getUichannel("'.$typeid.'","'.$e_id.'", "'.$e_page.'"); ?>';

        $parseStr .= '<?php if((is_array($__LIST__)) && (!empty($__LIST__["info"]) || (($__LIST__["info"] instanceof \think\Collection || $__LIST__["info"] instanceof \think\Paginator ) && $__LIST__["info"]->isEmpty()))): ?>';
        $parseStr .= '<?php $'.$id.' = $__LIST__; ';
        $parseStr .= ' $ui_typeid = !empty($'.$id.'["info"]["typeid"]) ? $'.$id.'["info"]["typeid"] : "";';
        // $parseStr .= ' $ui_row = !empty($'.$id.'["info"]["row"]) ? $'.$id.'["info"]["row"] : "";';
        $parseStr .= '?>';
        $parseStr .= $content;
        $parseStr .= '<?php $ui_typeid = $ui_row = ""; ?>';
        $parseStr .= '<?php endif; ?>';

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * 美化标签-栏目编辑
     */
    public function tagUitype($tag, $content)
    {
        $typeid = isset($tag['typeid']) ? $tag['typeid'] : '';
        $e_id = isset($tag['e-id']) ? $tag['e-id'] : '';
        $e_page = isset($tag['e-page']) ? $tag['e-page'] : '';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';

        $parseStr = '';
        $parseStr .= ' <?php ';
        $parseStr .= ' $tagUitype = new \think\template\taglib\eyou\TagUitype;';
        $parseStr .= ' $__LIST__ = $tagUitype->getUitype("'.$typeid.'","'.$e_id.'", "'.$e_page.'"); ?>';

        $parseStr .= '<?php if((is_array($__LIST__)) && (!empty($__LIST__["info"]) || (($__LIST__["info"] instanceof \think\Collection || $__LIST__["info"] instanceof \think\Paginator ) && $__LIST__["info"]->isEmpty()))): ?>';
        $parseStr .= '<?php $'.$id.' = $__LIST__; ';
        $parseStr .= ' $ui_typeid = !empty($'.$id.'["info"]["typeid"]) ? $'.$id.'["info"]["typeid"] : "";';
        $parseStr .= '?>';
        $parseStr .= $content;
        $parseStr .= '<?php $ui_typeid = ""; ?>';
        $parseStr .= '<?php endif; ?>';

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * 美化标签-栏目文章编辑
     */
    public function tagUiarclist($tag, $content)
    {
        $typeid = isset($tag['typeid']) ? $tag['typeid'] : '';
        $e_id = isset($tag['e-id']) ? $tag['e-id'] : '';
        $e_page = isset($tag['e-page']) ? $tag['e-page'] : '';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';

        $parseStr = '';
        $parseStr .= ' <?php ';
        $parseStr .= ' $tagUiarclist = new \think\template\taglib\eyou\TagUiarclist;';
        $parseStr .= ' $__LIST__ = $tagUiarclist->getUiarclist("'.$typeid.'","'.$e_id.'", "'.$e_page.'"); ?>';

        $parseStr .= '<?php if((is_array($__LIST__)) && (!empty($__LIST__["info"]) || (($__LIST__["info"] instanceof \think\Collection || $__LIST__["info"] instanceof \think\Paginator ) && $__LIST__["info"]->isEmpty()))): ?>';
        $parseStr .= '<?php $'.$id.' = $__LIST__; ';
        $parseStr .= ' $ui_typeid = !empty($'.$id.'["info"]["typeid"]) ? $'.$id.'["info"]["typeid"] : "";';
        // $parseStr .= ' $ui_row = !empty($'.$id.'["info"]["row"]) ? $'.$id.'["info"]["row"] : "";';
        $parseStr .= '?>';
        $parseStr .= $content;
        $parseStr .= '<?php $ui_typeid = $ui_row = ""; ?>';
        $parseStr .= '<?php endif; ?>';

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * 美化标签-富文本编辑器
     */
    public function tagUihtml($tag, $content)
    {
        $e_id = isset($tag['e-id']) ? $tag['e-id'] : '';
        $e_page = isset($tag['e-page']) ? $tag['e-page'] : '';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';

        $parseStr = '';
        $parseStr .= ' <?php ';
        $parseStr .= ' $tagUihtml = new \think\template\taglib\eyou\TagUihtml;';
        $parseStr .= ' $__LIST__ = $tagUihtml->getUihtml("'.$e_id.'", "'.$e_page.'");';
        $parseStr .= ' ?>';

        $parseStr .= '<?php if((is_array($__LIST__)) && (!empty($__LIST__["value"]) || (($__LIST__["value"] instanceof \think\Collection || $__LIST__["value"] instanceof \think\Paginator ) && $__LIST__["value"]->isEmpty()))): ?>';
        $parseStr .= '<?php $'.$id.' = $__LIST__; ?>';
        $parseStr .= $content;
        $parseStr .= '<?php endif; ?>';

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * 美化标签-纯文本编辑
     */
    public function tagUitext($tag, $content)
    {
        $e_id = isset($tag['e-id']) ? $tag['e-id'] : '';
        $e_page = isset($tag['e-page']) ? $tag['e-page'] : '';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';

        $parseStr = '';
        $parseStr .= ' <?php ';
        $parseStr .= ' $tagUitext = new \think\template\taglib\eyou\TagUitext;';
        $parseStr .= ' $__LIST__ = $tagUitext->getUitext("'.$e_id.'", "'.$e_page.'");';
        // $parseStr .= ' $__LIST__["value"] = ';
        $parseStr .= ' ?>';

        $parseStr .= '<?php if((is_array($__LIST__)) && (!empty($__LIST__["value"]) || (($__LIST__["value"] instanceof \think\Collection || $__LIST__["value"] instanceof \think\Paginator ) && $__LIST__["value"]->isEmpty()))): ?>';
        $parseStr .= '<?php $'.$id.' = $__LIST__; ?>';
        $parseStr .= $content;
        $parseStr .= '<?php endif; ?>';

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * load 标签解析 {load file="/static/js/base.js" /}
     * 格式：{load file="/static/css/base.css" /}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagLoad($tag)
    {
        $file     = isset($tag['file']) ? $tag['file'] : $tag['href'];
        $type     = isset($tag['type']) ? strtolower($tag['type']) : '';
        $ver     = !empty($tag['ver']) ? $tag['ver'] : 'on';
        $startStr = '';
        $parseStr = '';
        $endStr   = '';
        // 判断是否存在加载条件 允许使用函数判断(默认为isset)
        if (isset($tag['value'])) {
            $name = $tag['value'];
            $name = $this->autoBuildVar($name);
            $name = 'isset(' . $name . ')';
            $startStr = '<?php if(' . $name . '): ?>';
            $endStr = '<?php endif; ?>';
        }

        $parseStr .= $startStr;
        $parseStr .= ' <? $tagLoad = new \think\template\taglib\eyou\TagLoad;';
        $parseStr .= ' $__VALUE__ = $tagLoad->getLoad("'.$file.'", "'.$ver.'");';
        $parseStr .= ' echo $__VALUE__;';
        $parseStr .= ' ?>';
        $parseStr .= $endStr;

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * static 标签解析 {eyou:static file="/static/js/base.js" /}
     * 格式：{eyou:static file="/static/css/base.css" /}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagStatic($tag)
    {
        $file  = isset($tag['file']) ? $tag['file'] : '';
        $file = $this->varOrvalue($file);
        $href  = isset($tag['href']) ? $tag['href'] : '';
        $href = $this->varOrvalue($href);
        $lang = !empty($tag['lang']) ? $tag['lang'] : '';
        $lang = $this->varOrvalue($lang);
        $code = !empty($tag['code']) ? $tag['code'] : '';

        $parseStr = '<?php ';

        // 查询数据库获取的数据集
        $parseStr .= ' $tagStatic = new \think\template\taglib\eyou\TagStatic;';
        $parseStr .= ' $__VALUE__ = $tagStatic->getStatic('.$file.','.$lang.','.$href.',"'.$code.'");';
        $parseStr .= ' echo $__VALUE__;';
        $parseStr .= ' ?>';

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * channel 标签解析 用于获取栏目列表
     * 格式：type:son表示下级栏目,self表示同级栏目,top顶级栏目
     * {eyou:channel typeid='1' type='son' row='10' reid='0' empty='' name='' id='' key='' titlelen='' offset='' mod='' currentstyle='active'}
     *  <li><a href='{$field:typelink}'>{$field:typename}</a> </li> 
     * {/eyou:channel}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagChannel($tag, $content)
    {
        $typeid  = !empty($tag['typeid']) ? $tag['typeid'] : '';
        $typeid  = $this->varOrvalue($typeid);

        $name   = !empty($tag['name']) ? $tag['name'] : '';
        $type   = !empty($tag['type']) ? $tag['type'] : 'son';
        $currentstyle   = !empty($tag['currentstyle']) ? $tag['currentstyle'] : '';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $titlelen = !empty($tag['titlelen']) && is_numeric($tag['titlelen']) ? intval($tag['titlelen']) : 100;
        $offset = !empty($tag['offset']) && is_numeric($tag['offset']) ? intval($tag['offset']) : 0;
        $row = !empty($tag['row']) && is_numeric($tag['row']) ? intval($tag['row']) : 10;
        if (!empty($tag['limit'])) {
            $limitArr = explode(',', $tag['limit']);
            $offset = !empty($limitArr[0]) ? intval($limitArr[0]) : 0;
            $row = !empty($limitArr[1]) ? intval($limitArr[1]) : 0;
        }

        // 获取最顶级父栏目ID
        // $topTypeId = 0;
        // if ($tid >0 && $type == 'top') {
        //     $result = model('Arctype')->getAllPid($tid);
        //     reset($result);
        //     $firstVal = current($result);
        //     $topTypeId = $firstVal['id'];
        // }

        $parseStr = '<?php ';
        // 声明变量
        /*typeid的优先级别从高到低：装修数据 -> 标签属性值 -> 外层标签channelartlist属性值*/
        $parseStr .= ' if(isset($ui_typeid) && !empty($ui_typeid)) : $typeid = $ui_typeid; else: $typeid = '.$typeid.'; endif;';
        $parseStr .= ' if(empty($typeid) && isset($channelartlist["id"]) && !empty($channelartlist["id"])) : $typeid = intval($channelartlist["id"]); endif; ';
        /*--end*/
        $parseStr .= ' if(isset($ui_row) && !empty($ui_row)) : $row = $ui_row; else: $row = '.$row.'; endif;';

        if ($name) { // 从模板中传入数据集
            $symbol     = substr($name, 0, 1);
            if (':' == $symbol) {
                $name = $this->autoBuildVar($name);
                $parseStr .= '$_result=' . $name . ';';
                $name = '$_result';
            } else {
                $name = $this->autoBuildVar($name);
            }

            $parseStr .= 'if(is_array(' . $name . ') || ' . $name . ' instanceof \think\Collection || ' . $name . ' instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
            // 设置了输出数组长度
            if (0 != $offset || 'null' != $row) {
                $parseStr .= '$__LIST__ = is_array(' . $name . ') ? array_slice(' . $name . ',' . $offset . ',' . $row . ', true) : ' . $name . '->slice(' . $offset . ',' . $row . ', true); ';
            } else {
                $parseStr .= ' $__LIST__ = ' . $name . ';';
            }

        } else { // 查询数据库获取的数据集
            $parseStr .= ' $tagChannel = new \think\template\taglib\eyou\TagChannel;';
            $parseStr .= ' $_result = $tagChannel->getChannel($typeid, "'.$type.'", "'.$currentstyle.'");';
            $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
            // 设置了输出数组长度
            if (0 != $offset || 'null' != $row) {
                $parseStr .= '$__LIST__ = is_array($_result) ? array_slice($_result,' . $offset . ', $row, true) : $_result->slice(' . $offset . ', $row, true); ';
            } else {
                $parseStr .= ' if(intval($row) > 0) :';
                $parseStr .= ' $__LIST__ = is_array($_result) ? array_slice($_result,' . $offset . ', $row, true) : $_result->slice(' . $offset . ', $row, true); ';
                $parseStr .= ' else:';
                $parseStr .= ' $__LIST__ = $_result;';
                $parseStr .= ' endif;';
            }
        }

        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$' . $id . '["typename"] = text_msubstr($' . $id . '["typename"], 0, '.$titlelen.', false);';

        // $parseStr .= ' $' . $id . '["typeurl"] = model("Arctype")->getTypeUrl($' . $id . ');';
        
        // $parseStr .= ' if (strval($'.$id.'["id"]) == strval($typeid) || strval($'.$id.'["id"]) == '.$topTypeId.') :';
        // $parseStr .= ' $'.$id.'["currentstyle"] = "'.$currentstyle.'";';
        // $parseStr .= ' else: ';
        // $parseStr .= ' $'.$id.'["currentstyle"] = "";';
        // $parseStr .= ' endif;';

        $parseStr .= ' $__LIST__[$key] = $_result[$key] = $' . $id . ';';
        $parseStr .= '$mod = ($e % ' . $mod . ' );';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * channelartlist 标签解析 用于获取栏目列表
     * 格式：type:son表示下级栏目,self表示同级栏目,top顶级栏目
     * {eyou:channelartlist typeid='1' type='son' row='10'}
     *  <li><a href='{$field:typelink}'>{$field:typename}</a> </li> 
     * {/eyou:channelartlist}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagChannelartlist($tag, $content)
    {
        $typeid  = !empty($tag['typeid']) ? $tag['typeid'] : '';
        $typeid  = $this->varOrvalue($typeid);

        $type   = !empty($tag['type']) ? $tag['type'] : 'self';
        $id     = 'channelartlist';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $row = !empty($tag['row']) && is_numeric($tag['row']) ? intval($tag['row']) : 10;
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $titlelen = !empty($tag['titlelen']) && is_numeric($tag['titlelen']) ? intval($tag['titlelen']) : 100;

        $parseStr = '<?php ';
        // 声明变量
        $parseStr .= ' if(isset($ui_typeid) && !empty($ui_typeid)) : $typeid = $ui_typeid; else: $typeid = '.$typeid.'; endif;';
        $parseStr .= ' if(isset($ui_row) && !empty($ui_row)) : $row = $ui_row; else: $row = '.$row.'; endif;';

        // 查询数据库获取的数据集
        $parseStr .= ' $tagChannelartlist = new \think\template\taglib\eyou\TagChannelartlist;';
        $parseStr .= ' $_result = $tagChannelartlist->getChannelartlist($typeid, "'.$type.'");';
        $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        // 设置了输出数组长度
        if ('null' != $row) {
            $parseStr .= '$__LIST__ = is_array($_result) ? array_slice($_result,0, $row, true) : $_result->slice(0, $row, true); ';
        } else {
            $parseStr .= ' if(intval($row) > 0) :';
            $parseStr .= ' $__LIST__ = is_array($_result) ? array_slice($_result,0, $row, true) : $_result->slice(0, $row, true); ';
            $parseStr .= ' else:';
            $parseStr .= ' $__LIST__ = $_result;';
            $parseStr .= ' endif;';
        }

        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$' . $id . '["typename"] = text_msubstr($' . $id . '["typename"], 0, '.$titlelen.', false);';

        $parseStr .= ' $__LIST__[$key] = $_result[$key] = $' . $id . ';';
        $parseStr .= '$mod = ($e % ' . $mod . ' );';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= ' <?php $typeid = $row = ""; unset($'.$id.'); ?>';

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * arclist标签解析 获取指定文档列表（兼容tp的volist标签语法）
     * 格式：
     * {eyou:arclist channelid='1' typeid='1' row='10' offset='0' titlelen='30' orderby ='aid desc' flag='' infolen='160' empty='' id='field' mod='' name=''}
     *  {$field.title}
     *  {$field.typeid}
     * {/eyou:arclist}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagArclist($tag, $content)
    {
        $typeid     = !empty($tag['typeid']) ? $tag['typeid'] : '';
        $typeid  = $this->varOrvalue($typeid);

        $notypeid     = !empty($tag['notypeid']) ? $tag['notypeid'] : '';
        $notypeid  = $this->varOrvalue($notypeid);

        $channelid   = isset($tag['channelid']) ? $tag['channelid'] : '';
        $channelid  = $this->varOrvalue($channelid);

        $addfields     = isset($tag['addfields']) ? $tag['addfields'] : '';
        $addfields  = $this->varOrvalue($addfields);

        $name   = !empty($tag['name']) ? $tag['name'] : '';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $orderby    = isset($tag['orderby']) ? $tag['orderby'] : '';
        $orderWay    = isset($tag['orderWay']) ? $tag['orderWay'] : 'desc';
        $flag    = isset($tag['flag']) ? $tag['flag'] : '';
        $noflag    = isset($tag['noflag']) ? $tag['noflag'] : '';
        $tagid    = isset($tag['tagid']) ? $tag['tagid'] : ''; // 标签ID
        $pagesize = !empty($tag['pagesize']) && is_numeric($tag['pagesize']) ? intval($tag['pagesize']) : 0;
        $titlelen = !empty($tag['titlelen']) && is_numeric($tag['titlelen']) ? intval($tag['titlelen']) : 100;
        $infolen = !empty($tag['infolen']) && is_numeric($tag['infolen']) ? intval($tag['infolen']) : 160;
        $offset = !empty($tag['offset']) && is_numeric($tag['offset']) ? intval($tag['offset']) : 0;
        $row = !empty($tag['row']) && is_numeric($tag['row']) ? intval($tag['row']) : 10;
        if (!empty($tag['limit'])) {
            $limitArr = explode(',', $tag['limit']);
            $offset = !empty($limitArr[0]) ? intval($limitArr[0]) : 0;
            $row = !empty($limitArr[1]) ? intval($limitArr[1]) : 0;
        }

        $parseStr = '<?php ';
        // 声明变量
        /*typeid的优先级别从高到低：装修数据 -> 标签属性值 -> 外层标签channelartlist属性值*/
        $parseStr .= ' if(isset($ui_typeid) && !empty($ui_typeid)) : $typeid = $ui_typeid; else: $typeid = '.$typeid.'; endif;';
        $parseStr .= ' if(empty($typeid) && isset($channelartlist["id"]) && !empty($channelartlist["id"])) : $typeid = intval($channelartlist["id"]); endif; ';
        /*--end*/
        $parseStr .= ' if(isset($ui_row) && !empty($ui_row)) : $row = $ui_row; else: $row = '.$row.'; endif;';
        $parseStr .= ' $channelid = '.$channelid.';';

        if ($name) { // 从模板中传入数据集
            $symbol     = substr($name, 0, 1);
            if (':' == $symbol) {
                $name = $this->autoBuildVar($name);
                $parseStr .= '$_result=' . $name . ';';
                $name = '$_result';
            } else {
                $name = $this->autoBuildVar($name);
            }

            $parseStr .= 'if(is_array(' . $name . ') || ' . $name . ' instanceof \think\Collection || ' . $name . ' instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
            // 设置了输出数组长度
            if (0 != $offset || 'null' != $row) {
                $parseStr .= '$__LIST__ = is_array(' . $name . ') ? array_slice(' . $name . ',' . $offset . ',' . $row . ', true) : ' . $name . '->slice(' . $offset . ',' . $row . ', true); ';
            } else {
                $parseStr .= ' $__LIST__ = ' . $name . ';';
            }

        } else { // 查询数据库获取的数据集
            $parseStr .= ' $param = array(';
            $parseStr .= '      "typeid"=> $typeid,';
            $parseStr .= '      "notypeid"=> '.$notypeid.',';
            $parseStr .= '      "flag"=> "'.$flag.'",';
            $parseStr .= '      "noflag"=> "'.$noflag.'",';
            $parseStr .= '      "channel"=> $channelid,';
            $parseStr .= ' );';
            $parseStr .= ' $tag = '.var_export($tag,true).';';
            $parseStr .= ' $tagArclist = new \think\template\taglib\eyou\TagArclist;';
            $parseStr .= ' $_result = $tagArclist->getArclist($param, $row, "'.$orderby.'", '.$addfields.',"'.$orderWay.'","'.$tagid.'",$tag,"'.$pagesize.'");';

            $parseStr .= 'if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
            // 设置了输出数组长度
            if (0 != $offset || 'null' != $row) {
                $parseStr .= '$__LIST__ = is_array($_result) ? array_slice($_result,' . $offset . ', $row, true) : $_result->slice(' . $offset . ', $row, true); ';
            } else {
                $parseStr .= ' $__LIST__ = $_result;';
            }
        }
        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$aid = $'.$id.'["aid"];';
        $parseStr .= '$' . $id . '["title"] = text_msubstr($' . $id . '["title"], 0, '.$titlelen.', false);';
        $parseStr .= '$' . $id . '["seo_description"] = text_msubstr($' . $id . '["seo_description"], 0, '.$infolen.', true);';

        $parseStr .= '$mod = ($e % ' . $mod . ' );';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php $aid = 0; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * list 标签解析 获取指定文档分页列表（兼容tp的volist标签语法）
     * 格式：
     * {eyou:list channelid='1' typeid='1' row='10' titlelen='30' orderby ='aid desc' flag='' infolen='160' empty='' id='field' mod='' name=''}
     *  {$field.title}
     *  {$field.typeid}
     * {/eyou:list}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagList($tag, $content)
    {
        $typeid     = !empty($tag['typeid']) ? $tag['typeid'] : '';
        $typeid  = $this->varOrvalue($typeid);

        $notypeid     = !empty($tag['notypeid']) ? $tag['notypeid'] : '';
        $notypeid  = $this->varOrvalue($notypeid);

        $channelid   = isset($tag['channelid']) ? $tag['channelid'] : '';
        $channelid  = $this->varOrvalue($channelid);
        
        $addfields     = isset($tag['addfields']) ? $tag['addfields'] : '';
        $addfields  = $this->varOrvalue($addfields);

        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $pagesize = !empty($tag['pagesize']) && is_numeric($tag['pagesize']) ? intval($tag['pagesize']) : 10;
        $orderby    = isset($tag['orderby']) ? $tag['orderby'] : '';
        $orderWay    = isset($tag['orderWay']) ? $tag['orderWay'] : 'desc';
        $flag    = isset($tag['flag']) ? $tag['flag'] : '';
        $noflag    = isset($tag['noflag']) ? $tag['noflag'] : '';
        $titlelen = !empty($tag['titlelen']) && is_numeric($tag['titlelen']) ? intval($tag['titlelen']) : 100;
        $infolen = !empty($tag['infolen']) && is_numeric($tag['infolen']) ? intval($tag['infolen']) : 160;

        $parseStr = '<?php ';
        // 声明变量
        /*typeid的优先级别从高到低：装修数据 -> 标签属性值 -> 外层标签channelartlist属性值*/
        $parseStr .= ' $typeid = '.$typeid.'; ';
        $parseStr .= ' if(empty($typeid) && isset($channelartlist["id"]) && !empty($channelartlist["id"])) : $typeid = intval($channelartlist["id"]); endif; ';
        /*--end*/

        // 查询数据库获取的数据集
        $parseStr .= ' $param = array(';
        $parseStr .= '      "typeid"=> $typeid,';
        $parseStr .= '      "notypeid"=> '.$notypeid.',';
        $parseStr .= '      "flag"=> "'.$flag.'",';
        $parseStr .= '      "noflag"=> "'.$noflag.'",';
        $parseStr .= '      "channel"=> '.$channelid.',';
        $parseStr .= ' );';
        // $parseStr .= ' $orderby = "'.$orderby.'";';
        $parseStr .= ' $tagList = new \think\template\taglib\eyou\TagList;';
        $parseStr .= ' $_result_tmp = $tagList->getList($param, '.$pagesize.', "'.$orderby.'", '.$addfields.', "'.$orderWay.'");';

        $parseStr .= 'if(is_array($_result_tmp) || $_result_tmp instanceof \think\Collection || $_result_tmp instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        $parseStr .= ' $__LIST__ = $_result = $_result_tmp["list"];';
        $parseStr .= ' $__PAGES__ = $_result_tmp["pages"];';

        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$aid = $'.$id.'["aid"];';
        $parseStr .= '$' . $id . '["title"] = text_msubstr($' . $id . '["title"], 0, '.$titlelen.', false);';
        $parseStr .= '$' . $id . '["seo_description"] = text_msubstr($' . $id . '["seo_description"], 0, '.$infolen.', true);';

        $parseStr .= '$mod = ($e % ' . $mod . ' );';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php $aid = 0; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * pagelist 标签解析
     * 在模板中获取列表的分页
     * 格式： {eyou:pagelist listitem='info,index,end,pre,next,pageno' listsize='2'/}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagPagelist($tag)
    {
        $listitem = !empty($tag['listitem']) ? $tag['listitem'] : '';
        $listsize   = !empty($tag['listsize']) ? intval($tag['listsize']) : '';

        $parseStr = ' <?php ';
        $parseStr .= ' $__PAGES__ = isset($__PAGES__) ? $__PAGES__ : "";';
        $parseStr .= ' $tagPagelist = new \think\template\taglib\eyou\TagPagelist;';
        $parseStr .= ' $__VALUE__ = $tagPagelist->getPagelist($__PAGES__, "'.$listitem.'", "'.$listsize.'");';
        $parseStr .= ' echo $__VALUE__;';
        $parseStr .= ' ?>';

        return $parseStr;
    }

    /**
     * arcpagelist 标签解析
     * 在模板中获取arclist标签列表的ajax分页
     * 格式： {eyou:arcpagelist tagid='' pagesize='2'} {/eyou:arcpagelist}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagArcpagelist($tag, $content)
    {
        $tagid = !empty($tag['tagid']) ? $tag['tagid'] : '';
        $pagesize = !empty($tag['pagesize']) && is_numeric($tag['pagesize']) ? intval($tag['pagesize']) : 0;
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $tips     = isset($tag['tips']) ? $tag['tips'] : '';
        $loading     = isset($tag['loading']) ? $tag['loading'] : '';
        $loading  = $this->varOrvalue($loading);

        $parseStr = ' <?php ';

        // 查询数据库获取的数据集
        $parseStr .= ' $tagArcpagelist = new \think\template\taglib\eyou\TagArcpagelist;';
        $parseStr .= ' $_result = $tagArcpagelist->getArcpagelist("'.$tagid.'","'.$pagesize.'","'.$tips.'",'.$loading.');';

        $parseStr .= ' if(!empty($_result) || (($_result instanceof \think\Collection || $_result instanceof \think\Paginator ) && $_result->isEmpty())): ?>';
        $parseStr .= '<?php $'.$id.' = $_result; ?>';
        $parseStr .= $content;
        $parseStr .= '<?php ';
        $parseStr .= ' endif; ?>';
        $parseStr .= '<?php echo $_result["js"]; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * position 标签解析
     * 在模板中获取列表的分页
     * 格式： {eyou:position typeid="" symbol=" > "/}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagPosition($tag)
    {
        $typeid = !empty($tag['typeid']) ? $tag['typeid'] : '';
        $typeid = $this->varOrvalue($typeid);

        $symbol     = isset($tag['symbol']) ? $tag['symbol'] : '';
        $style   = !empty($tag['style']) ? $tag['style'] : '';

        $parseStr = ' <?php ';

        /*typeid的优先级别从高到低：装修数据 -> 标签属性值 -> 外层标签channelartlist属性值*/
        $parseStr .= ' $typeid = '.$typeid.';';
        $parseStr .= ' if(empty($typeid) && isset($channelartlist["id"]) && !empty($channelartlist["id"])) : $typeid = intval($channelartlist["id"]); endif; ';
        /*--end*/
        
        $parseStr .= ' $tagPosition = new \think\template\taglib\eyou\TagPosition;';
        $parseStr .= ' $__VALUE__ = $tagPosition->getPosition($typeid, "'.$symbol.'", "'.$style.'");';
        $parseStr .= ' echo $__VALUE__;';
        $parseStr .= ' ?>';

        return $parseStr;
    }

    /**
     * searchurl 标签解析
     * 在模板中获取搜索的URL
     * 格式： {eyou:searchurl /}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagSearchurl($tag)
    {
        $parseStr = '<?php ';

        // 查询数据库获取的数据集
        $parseStr .= ' $tagSearchurl = new \think\template\taglib\eyou\TagSearchurl;';
        $parseStr .= ' $__VALUE__ = $tagSearchurl->getSearchurl();';
        $parseStr .= ' echo $__VALUE__';
        $parseStr .= '?>';
        
        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * searchform 搜索表单标签解析 TAG调用
     * {eyou:searchform type='default'}
        {$field.searchurl}
     * {/eyou:searchform}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagSearchform($tag, $content)
    {
        $channel   = !empty($tag['channelid']) ? $tag['channelid'] : '';
        $channel  = $this->varOrvalue($channel);
        $typeid   = !empty($tag['typeid']) ? $tag['typeid'] : '';
        $typeid  = $this->varOrvalue($typeid);
        $notypeid   = !empty($tag['notypeid']) ? $tag['notypeid'] : '';
        $notypeid  = $this->varOrvalue($notypeid);
        $flag   = !empty($tag['flag']) ? $tag['flag'] : '';
        $flag  = $this->varOrvalue($flag);
        $noflag   = !empty($tag['noflag']) ? $tag['noflag'] : '';
        $noflag  = $this->varOrvalue($noflag);
        $type   = !empty($tag['type']) ? $tag['type'] : 'default';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);

        $parseStr = '<?php ';

        // 查询数据库获取的数据集
        $parseStr .= ' $tagSearchform = new \think\template\taglib\eyou\TagSearchform;';
        $parseStr .= ' $_result = $tagSearchform->getSearchform('.$typeid.','.$channel.','.$notypeid.','.$flag.','.$noflag.');';
        $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        $parseStr .= ' $__LIST__ = $_result;';

        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$mod = ($e % ' . $mod . ' );';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach;';
        $parseStr .= 'endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * type标签解析 指定的单个栏目的链接
     * 格式：
     * {eyou:type typeid='' empty=''}
     *  <a href="{$field:typelink}">{$field:typename}</a>
     * {/eyou:type}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagType($tag, $content)
    {
        $typeid  = isset($tag['typeid']) ? $tag['typeid'] : '';
        $typeid  = $this->varOrvalue($typeid);

        $type  = !empty($tag['type']) ? $tag['type'] : 'self';
        $empty  = !empty($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $id     = isset($tag['id']) ? $tag['id'] : 'field';

        $addfields     = isset($tag['addfields']) ? $tag['addfields'] : '';
        if (!empty($tag['addtable'])) {
            $addfields = $tag['addtable'];
        }
        $addfields  = $this->varOrvalue($addfields);

        $parseStr = '<?php ';
        // 声明变量
        /*typeid的优先级别从高到低：装修数据 -> 标签属性值 -> 外层标签channelartlist属性值*/
        $parseStr .= ' if(isset($ui_typeid) && !empty($ui_typeid)) : $typeid = $ui_typeid; else: $typeid = '.$typeid.'; endif;';
        $parseStr .= ' if(empty($typeid) && isset($channelartlist["id"]) && !empty($channelartlist["id"])) : $typeid = intval($channelartlist["id"]); endif; ';
        /*--end*/
        $parseStr .= ' $tagType = new \think\template\taglib\eyou\TagType;';
        $parseStr .= ' $_result = $tagType->getType($typeid, "'.$type.'", '.$addfields.');';
        $parseStr .= ' ?>';

        /*方式一*/
        /*$parseStr .= '<?php if((!empty($_result) || (($_result instanceof \think\Collection || $_result instanceof \think\Paginator ) && $_result->isEmpty()))): ?>';
        $parseStr .= '<?php $'.$id.' = $_result; ?>';
        $parseStr .= $content;
        $parseStr .= '<?php endif; ?>';*/
        /*--end*/

        /*方式二*/
        $parseStr .= '<?php if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): ';
        $parseStr .= ' $__LIST__ = $_result;';
        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= '$'.$id.' = $__LIST__;';
        $parseStr .= '?>';
        $parseStr .= $content;
        $parseStr .= '<?php endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用
        /*--end*/

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * arcview标签解析 指定的单个栏目的链接
     * 格式：
     * {eyou:arcview aid='' empty=''}
     *  <a href="{$field:arcurl}">{$field:title}</a>
     * {/eyou:arcview}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagArcview($tag, $content)
    {
        $aid  = isset($tag['aid']) ? $tag['aid'] : '0';
        $aid  = $this->varOrvalue($aid);

        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $addfields     = isset($tag['addfields']) ? $tag['addfields'] : '';
        $addfields  = $this->varOrvalue($addfields);

        $parseStr = '<?php ';
        // 声明变量
        $parseStr .= ' if(!isset($aid) || empty($aid)) : $aid = '.$aid.'; endif;';

        $parseStr .= ' $tagArcview = new \think\template\taglib\eyou\TagArcview;';
        $parseStr .= ' $_result = $tagArcview->getArcview($aid, '.$addfields.');';
        $parseStr .= ' ?>';

        /*方式一*/
        /*$parseStr .= '<?php if((!empty($_result) || (($_result instanceof \think\Collection || $_result instanceof \think\Paginator ) && $_result->isEmpty()))): ?>';
        $parseStr .= '<?php $'.$id.' = $_result; ?>';
        $parseStr .= $content;
        $parseStr .= '<?php endif; ?>';*/
        /*--end*/

        /*方式一*/
        $parseStr .= '<?php if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): ';
        $parseStr .= ' $__LIST__ = $_result;';
        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= '$'.$id.' = $__LIST__;';
        $parseStr .= '?>';
        $parseStr .= $content;
        $parseStr .= '<?php endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php unset($aid); ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用
        /*--end*/

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * tag 标签解析 TAG调用
     * 格式：sort:排序方式 month，rand，week
     *       getall:获取类型 0 为当前内容页TAG标记，1为获取全部TAG标记
     * {eyou:tag row='1' getall='0' sort=''}
     *  <li><a href='{$field.link}'>{$field.tag}</a> </li> 
     * {/eyou:tag}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagTag($tag, $content)
    {
        $aid   = !empty($tag['aid']) ? $tag['aid'] : '0';
        $aid  = $this->varOrvalue($aid);
        $typeid   = !empty($tag['typeid']) ? $tag['typeid'] : '';
        $typeid  = $this->varOrvalue($typeid);
        $getall   = !empty($tag['getall']) ? $tag['getall'] : '0';
        $name   = !empty($tag['name']) ? $tag['name'] : '';
        $style   = !empty($tag['style']) ? $tag['style'] : '';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $row = !empty($tag['row']) && is_numeric($tag['row']) ? intval($tag['row']) : 100;
        $sort   = !empty($tag['sort']) ? $tag['sort'] : 'new';

        $parseStr = '<?php ';

        /*typeid的优先级别从高到低：装修数据 -> 标签属性值 -> 外层标签channelartlist属性值*/
        $parseStr .= ' $typeid = '.$typeid.';';
        $parseStr .= ' if(empty($typeid) && isset($channelartlist["id"]) && !empty($channelartlist["id"])) : $typeid = intval($channelartlist["id"]); endif; ';
        // 声明变量
        $parseStr .= ' if(!isset($aid) || empty($aid)) : $aid = '.$aid.'; endif;';
        /*--end*/

        // 查询数据库获取的数据集
        $parseStr .= ' $tagTag = new \think\template\taglib\eyou\TagTag;';
        $parseStr .= ' $_result = $tagTag->getTag('.$getall.', $typeid, $aid, '.$row.', "'.$sort.'");';
        $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        // 设置了输出数组长度
        if ('null' != $row) {
            $parseStr .= '$__LIST__ = is_array($_result) ? array_slice($_result,0, '.$row.', true) : $_result->slice(0, '.$row.', true); ';
        } else {
            $parseStr .= ' $__LIST__ = $_result;';
        }

        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$mod = ($e % ' . $mod . ' );';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php unset($aid); ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * flink 标签解析 TAG调用
     * 格式：sort:排序方式 month，rand，week
     *       getall:获取类型 0 为当前内容页TAG标记，1为获取全部TAG标记
     * {eyou:flink row='1' titlelen='20'}
     *  <li><a href='{$field:url}'>{$field:title}</a> </li> 
     * {/eyou:flink}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagFlink($tag, $content)
    {
        $type   = !empty($tag['type']) ? $tag['type'] : 'text';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $titlelen = !empty($tag['titlelen']) && is_numeric($tag['titlelen']) ? intval($tag['titlelen']) : 100;
        $row = !empty($tag['row']) ? intval($tag['row']) : 0;
        $limit   = !empty($tag['limit']) ? $tag['limit'] : '';
        if (empty($limit) && !empty($row)) {
            $limit = "0,{$row}";
        }

        $parseStr = '<?php ';

        // 查询数据库获取的数据集
        $parseStr .= ' $tagFlink = new \think\template\taglib\eyou\TagFlink;';
        $parseStr .= ' $_result = $tagFlink->getFlink("'.$type.'", "'.$limit.'");';
        $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        $parseStr .= ' $__LIST__ = $_result;';

        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$' . $id . '["title"] = text_msubstr($' . $id . '["title"], 0, '.$titlelen.', false);';
        $parseStr .= ' $__LIST__[$key] = $_result[$key] = $' . $id . ';';
        $parseStr .= '$mod = ($e % ' . $mod . ' );';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * language 标签解析 TAG调用
     * {eyou:language row='1' type='default'}
     *  <li><a href='{$field:url}'>{$field:name}</a> </li> 
     * {/eyou:language}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagLanguage($tag, $content)
    {
        $type   = !empty($tag['type']) ? $tag['type'] : '';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $currentstyle   = !empty($tag['currentstyle']) ? $tag['currentstyle'] : '';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $titlelen = !empty($tag['titlelen']) && is_numeric($tag['titlelen']) ? intval($tag['titlelen']) : 100;
        $row = !empty($tag['row']) ? intval($tag['row']) : 0;
        $limit   = !empty($tag['limit']) ? $tag['limit'] : '';
        if (empty($limit) && !empty($row)) {
            $limit = "0,{$row}";
        }

        $parseStr = '<?php ';

        // 查询数据库获取的数据集
        $parseStr .= ' $tagLanguage = new \think\template\taglib\eyou\TagLanguage;';
        $parseStr .= ' $_result = $tagLanguage->getLanguage("'.$type.'", "'.$limit.'", "'.$currentstyle.'");';
        $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        $parseStr .= ' $__LIST__ = $_result;';

        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$' . $id . '["title"] = text_msubstr($' . $id . '["title"], 0, '.$titlelen.', false);';
        $parseStr .= ' $__LIST__[$key] = $_result[$key] = $' . $id . ';';
        $parseStr .= '$mod = ($e % ' . $mod . ' );';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * lang 标签解析
     * 在模板中获取多语言模板变量值
     * 格式： {eyou:lang name="" /}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagLang($tag)
    {
        $param = [];
        $name     = isset($tag['name']) ? $tag['name'] : '';
        !empty($name) && $param['name'] = $name;

        $const     = isset($tag['const']) ? $tag['const'] : '';
        !empty($const) && $param['const'] = $const;

        $parseStr = '<?php ';

        // 查询数据库获取的数据集
        $parseStr .= ' $tagLang = new \think\template\taglib\eyou\TagLang;';
        $parseStr .= ' $__VALUE__ = $tagLang->getLang(\''.serialize($param).'\');';
        $parseStr .= ' echo $__VALUE__;';
        $parseStr .= ' ?>';

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * ad标签解析 指定的单个广告的信息
     * 格式：
     * {eyou:ad aid=''}
     *  <a href="{$field:links}">{$field:title}</a>
     * {/eyou:ad}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagAd($tag, $content)
    {
        $aid  = isset($tag['aid']) ? $tag['aid'] : '0';
        $aid  = $this->varOrvalue($aid);

        $id     = isset($tag['id']) ? $tag['id'] : 'field';

        $parseStr = '<?php ';
        // 声明变量
        $parseStr .= ' $tagAd = new \think\template\taglib\eyou\TagAd;';
        $parseStr .= ' $_result = $tagAd->getAd('.$aid.');';
        $parseStr .= ' ?>';

        $parseStr .= '<?php if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): ';
        $parseStr .= ' $__LIST__ = $_result;';
        $parseStr .= 'if( count($__LIST__)==0 ) : echo "";';
        $parseStr .= 'else: ';
        $parseStr .= '$'.$id.' = $__LIST__;';
        $parseStr .= '?>';
        $parseStr .= $content;
        $parseStr .= '<?php endif; else: echo "";endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * adv 广告标签
     * 在模板中给某个变量赋值 支持变量赋值
     * 格式：
     * {eyou:adv pid='' limit=''}
     *  <a href="{$field:links}" {$field.target}>{$field:title}</a>
     * {/eyou:adv}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagAdv($tag, $content)
    {
        $pid  =  !empty($tag['pid']) ? $tag['pid'] : '0';// 返回的变量pid
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $orderby = !empty($tag['orderby']) ? $tag['orderby'] : ''; //排序
        $row = !empty($tag['row']) ? $tag['row'] : '10'; 
        $where = !empty($tag['where']) ? $tag['where'] : ''; //查询条件
        $key  =  !empty($tag['key']) ? $tag['key'] : 'key';// 返回的变量key
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $currentstyle   = !empty($tag['currentstyle']) ? $tag['currentstyle'] : '';

        $parseStr = '<?php ';

        // 查询数据库获取的数据集
        $parseStr .= ' $tagAdv = new \think\template\taglib\eyou\TagAdv;';
        $parseStr .= ' $_result = $tagAdv->getAdv('.$pid.', "'.$where.'", "'.$orderby.'");';
        $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        // 设置了输出数组长度
        if ('null' != $row) {
            $parseStr .= '$__LIST__ = is_array($_result) ? array_slice($_result,0, '.$row.', true) : $_result->slice(0, '.$row.', true); ';
        } else {
            $parseStr .= ' $__LIST__ = $_result;';
        }
        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';

        $parseStr .= ' if ($' . $key . ' == 0) :';
        $parseStr .= ' $'.$id.'["currentstyle"] = "'.$currentstyle.'";';
        $parseStr .= ' else: ';
        $parseStr .= ' $'.$id.'["currentstyle"] = "";';
        $parseStr .= ' endif;';

        $parseStr .= '$mod = ($e % ' . $mod . ' );';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * prenext 标签解析
     * 在模板中获取内容页的上下篇
     * 格式：
     * {eyou:prenext get='pre'}
     *  <a href="{$field:arcurl}">{$field:title}</a>
     * {/eyou:prenext}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagPrenext($tag, $content)
    {
        $get  =  !empty($tag['get']) ? $tag['get'] : 'pre';
        $titlelen = !empty($tag['titlelen']) && is_numeric($tag['titlelen']) ? intval($tag['titlelen']) : 100;
        $id     = isset($tag['id']) ? $tag['id'] : 'field';

        if (isset($tag['empty'])) {
            $style = 1; // 第一种默认标签写法，带属性empty
        } else {
            $style = 2; // 第二种支持判断写法，可以 else
        }

        if (1 == $style) {
            $empty     = isset($tag['empty']) ? $tag['empty'] : '暂无';
            $empty  = htmlspecialchars($empty);
            
            $parseStr = '<?php ';
            $parseStr .= ' $tagPrenext = new \think\template\taglib\eyou\TagPrenext;';
            $parseStr .= ' $_result = $tagPrenext->getPrenext("'.$get.'");';
            $parseStr .= 'if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): ';
            $parseStr .= ' $__LIST__ = $_result;';
            $parseStr .= 'if( empty($__LIST__) ) : echo htmlspecialchars_decode("' . $empty . '");';
            $parseStr .= 'else: ';
            $parseStr .= '$'.$id.' = $__LIST__;';
            $parseStr .= '$' . $id . '["title"] = text_msubstr($' . $id . '["title"], 0, '.$titlelen.', false);';

            $parseStr .= '?>';
            $parseStr .= $content;
            $parseStr .= '<?php endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        
        } else {
            $parseStr = '<?php ';
            $parseStr .= ' $tagPrenext = new \think\template\taglib\eyou\TagPrenext;';
            $parseStr .= ' $_result = $tagPrenext->getPrenext("'.$get.'");';
            $parseStr .= '?>';

            $parseStr .= '<?php if(!empty($_result) || (($_result instanceof \think\Collection || $_result instanceof \think\Paginator ) && $_result->isEmpty())): ?>';
            $parseStr .= '<?php $'.$id.' = $_result; ?>';
            $parseStr .= $content;
            $parseStr .= '<?php endif; ?>';
        }

        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * guestbookform 留言表单标签解析 TAG调用
     * {eyou:guestbookform type='default'}
        {$field.value}
     * {/eyou:guestbookform}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagGuestbookform($tag, $content)
    {
        $typeid   = !empty($tag['typeid']) ? $tag['typeid'] : '';
        $typeid  = $this->varOrvalue($typeid);
        $type   = !empty($tag['type']) ? $tag['type'] : 'default';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);

        $parseStr = '<?php ';

        /*typeid的优先级别从高到低：装修数据 -> 标签属性值 -> 外层标签channelartlist属性值*/
        $parseStr .= ' $typeid = '.$typeid.';';
        $parseStr .= ' if(empty($typeid) && isset($channelartlist["id"]) && !empty($channelartlist["id"])) : $typeid = intval($channelartlist["id"]); endif; ';
        /*--end*/

        // 查询数据库获取的数据集
        $parseStr .= ' $tagGuestbookform = new \think\template\taglib\eyou\TagGuestbookform;';
        $parseStr .= ' $_result = $tagGuestbookform->getGuestbookform($typeid, "'.$type.'");';
        $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        $parseStr .= ' $__LIST__ = $_result;';

        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$mod = ($e % ' . $mod . ' );';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach;';
        $parseStr .= 'endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * field 标签解析
     * 在模板中获取变量值，只适用于标签channelartlist
     * 格式： {eyou:field name="typename|html_msubstr=###,0,2" /}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagField($tag)
    {
        $name  = isset($tag['name']) ? $tag['name'] : '';
        $addfields    = isset($tag['addfields']) ? $tag['addfields'] : '';
        $aid  = isset($tag['aid']) ? $tag['aid'] : '';
        $aid  = $this->varOrvalue($aid);

        $parseStr = '';

        if (!empty($name)) {
            $arr = explode('|', $name);
            $name = $arr[0];

            // 查询数据库获取的数据集
            $parseStr .= '<?php ';
            $parseStr .= ' $__VALUE__ = isset($channelartlist["'.$name.'"]) ? $channelartlist["'.$name.'"] : "变量名不存在";';

            if (1 < count($arr)) {
                $funcArr = explode('=', $arr[1]);
                $funcName = $funcArr[0]; // 函数名
                $funcParam = !empty($funcArr[1]) ? $funcArr[1] : ''; // 函数参数
                if (!empty($funcParam)) {
                    $funcParamStr = '';
                    foreach (explode(',', $funcParam) as $key => $val) {
                        if ('###' == $val) {
                            $val = '$__VALUE__';
                        }
                        if (0 < $key) {
                            $funcParamStr .= ', ';
                        }
                        $funcParamStr .= $val;
                    }
                    $parseStr .= '$__VALUE__ = '.$funcName.'('.$funcParamStr.');';
                }
            }

            $parseStr .= ' echo $__VALUE__;';
            $parseStr .= ' ?>';

        } else if (!empty($addfields)) {

            $addfieldsArr = explode('|', $addfields);

            $parseStr .= '<?php ';

            // 查询数据库获取的数据集
            $parseStr .= ' $tagField = new \think\template\taglib\eyou\TagField;';
            $parseStr .= ' $__VALUE__ = $tagField->getField("'.$addfieldsArr[0].'", '.$aid.');';

            // 字段指定的函数
            if (!empty($addfieldsArr[1])) {
                $funcArr = explode('=', $addfieldsArr[1]);
                $funcName = $funcArr[0]; // 函数名
                $funcParam = !empty($funcArr[1]) ? $funcArr[1] : ''; // 函数参数
                if (!empty($funcParam)) {
                    $funcParamStr = '';
                    foreach (explode(',', $funcParam) as $key => $val) {
                        if ('###' == $val) {
                            $val = '$__VALUE__';
                        }
                        if (0 < $key) {
                            $funcParamStr .= ', ';
                        }
                        $funcParamStr .= $val;
                    }
                    $parseStr .= '$__VALUE__ = '.$funcName.'('.$funcParamStr.');';
                }
            }

            $parseStr .= ' echo $__VALUE__;';
            $parseStr .= ' ?>';
        }

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * empty标签解析
     * 如果某个变量为empty 则输出内容
     * 格式： {eyou:empty name="" }content{/eyou:empty}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagEmpty($tag, $content)
    {
        $name     = $tag['name'];
        $name     = $this->autoBuildVar($name);
        $parseStr = '<?php if(empty(' . $name . ') || ((' . $name . ' instanceof \think\Collection || ' . $name . ' instanceof \think\Paginator ) && ' . $name . '->isEmpty())): ?>' . $content . '<?php endif; ?>';
        return $parseStr;
    }

    /**
     * notempty 标签解析
     * 如果某个变量不为empty 则输出内容
     * 格式： {eyou:notempty name="" }content{/eyou:notempty}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagNotempty($tag, $content)
    {
        $name     = $tag['name'];
        $name     = $this->autoBuildVar($name);
        $parseStr = '<?php if(!(empty(' . $name . ') || ((' . $name . ' instanceof \think\Collection || ' . $name . ' instanceof \think\Paginator ) && ' . $name . '->isEmpty()))): ?>' . $content . '<?php endif; ?>';
        return $parseStr;
    }

    /**
     * assign标签解析
     * 在模板中给某个变量赋值 支持变量赋值
     * 格式： {eyou:assign name="" value="" /}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagAssign($tag, $content)
    {
        $name = $this->autoBuildVar($tag['name']);
        $flag = substr($tag['value'], 0, 1);
        if ('$' == $flag || ':' == $flag) {
            $value = $this->autoBuildVar($tag['value']);
        } else {
            $value = '\'' . $tag['value'] . '\'';
        }
        $parseStr = '<?php ' . $name . ' = ' . $value . '; ?>';
        return $parseStr;
    }

    /**
     * foreach标签解析 循环输出数据集
     * 格式：
     * {eyou:foreach name="userList" id="user" key="key" index="i" mod="2" offset="3" length="5" empty=""}
     * {user.username}
     * {/eyou:foreach}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagForeach($tag, $content)
    {
        // 直接使用表达式
        if (!empty($tag['expression'])) {
            $expression = ltrim(rtrim($tag['expression'], ')'), '(');
            $expression = $this->autoBuildVar($expression);
            $parseStr   = '<?php foreach(' . $expression . '): ?>';
            $parseStr .= $content;
            $parseStr .= '<?php endforeach; ?>';
            return $parseStr;
        }
        $name   = $tag['name'];
        $key    = !empty($tag['key']) ? $tag['key'] : 'key';
        $item   = !empty($tag['id']) ? $tag['id'] : $tag['item'];
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $offset = !empty($tag['offset']) && is_numeric($tag['offset']) ? intval($tag['offset']) : 0;
        $length = !empty($tag['length']) && is_numeric($tag['length']) ? intval($tag['length']) : 'null';

        $parseStr = '<?php ';
        // 支持用函数传数组
        if (':' == substr($name, 0, 1)) {
            $var  = '$_' . uniqid();
            $name = $this->autoBuildVar($name);
            $parseStr .= $var . '=' . $name . '; ';
            $name = $var;
        } else {
            $name = $this->autoBuildVar($name);
        }
        $parseStr .= 'if(is_array(' . $name . ') || ' . $name . ' instanceof \think\Collection || ' . $name . ' instanceof \think\Paginator): ';
        // 设置了输出数组长度
        if (0 != $offset || 'null' != $length) {
            if (!isset($var)) {
                $var = '$_' . uniqid();
            }
            $parseStr .= $var . ' = is_array(' . $name . ') ? array_slice(' . $name . ',' . $offset . ',' . $length . ', true) : ' . $name . '->slice(' . $offset . ',' . $length . ', true); ';
        } else {
            $var = &$name;
        }

        $parseStr .= 'if( count(' . $var . ')==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';

        // 设置了索引项
        if (isset($tag['index'])) {
            $index = $tag['index'];
            $parseStr .= '$' . $index . '=0; $e = 1;';
        }
        $parseStr .= 'foreach(' . $var . ' as $' . $key . '=>$' . $item . '): ';
        // 设置了索引项
        if (isset($tag['index'])) {
            $index = $tag['index'];
            if (isset($tag['mod'])) {
                $mod = (int) $tag['mod'];
                $parseStr .= '$mod = ($e % ' . $mod . '); ';
            }
            $parseStr .= '++$' . $index . ';';
        }
        $parseStr .= '?>';
        // 循环体中的内容
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * if标签解析
     * 格式：
     * {eyou:if condition=" $a eq 1"}
     * {eyou:elseif condition="$a eq 2" /}
     * {eyou:else /}
     * {/eyou:if}
     * 表达式支持 eq neq gt egt lt elt == > >= < <= or and || &&
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagIf($tag, $content)
    {
        $condition = !empty($tag['expression']) ? $tag['expression'] : $tag['condition'];
        $condition = $this->parseCondition($condition);
        $parseStr  = '<?php if(' . $condition . '): ?>' . $content . '<?php endif; ?>';
        return $parseStr;
    }

    /**
     * elseif标签解析
     * 格式：见if标签
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagElseif($tag, $content)
    {
        $condition = !empty($tag['expression']) ? $tag['expression'] : $tag['condition'];
        $condition = $this->parseCondition($condition);
        $parseStr  = '<?php elseif(' . $condition . '): ?>';
        return $parseStr;
    }

    /**
     * else 标签解析
     * 格式：见if标签
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagElse($tag)
    {
        $parseStr = '<?php else: ?>';
        return $parseStr;
    }

    /**
     * switch标签解析
     * 格式：
     * {eyou:switch name="a.name"}
     * {eyou:case value="1" break="false"}1{/case}
     * {eyou:case value="2" }2{/case}
     * {eyou:default /}other
     * {/eyou:switch}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagSwitch($tag, $content)
    {
        $name     = !empty($tag['expression']) ? $tag['expression'] : $tag['name'];
        $name     = $this->autoBuildVar($name);
        $parseStr = '<?php switch(' . $name . '): ?>' . $content . '<?php endswitch; ?>';
        return $parseStr;
    }

    /**
     * case标签解析 需要配合switch才有效
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagCase($tag, $content)
    {
        $value = !empty($tag['expression']) ? $tag['expression'] : $tag['value'];
        $flag  = substr($value, 0, 1);
        if ('$' == $flag || ':' == $flag) {
            $value = $this->autoBuildVar($value);
            $value = 'case ' . $value . ':';
        } elseif (strpos($value, '|')) {
            $values = explode('|', $value);
            $value  = '';
            foreach ($values as $val) {
                $value .= 'case "' . addslashes($val) . '":';
            }
        } else {
            $value = 'case "' . $value . '":';
        }
        $parseStr = '<?php ' . $value . ' ?>' . $content;
        $isBreak  = isset($tag['break']) ? $tag['break'] : '';
        if ('' == $isBreak || $isBreak) {
            $parseStr .= '<?php break; ?>';
        }
        return $parseStr;
    }

    /**
     * default标签解析 需要配合switch才有效
     * 使用： {eyou:default /}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagDefault($tag)
    {
        $parseStr = '<?php default: ?>';
        return $parseStr;
    }

    /**
     * compare标签解析
     * 用于值的比较 支持 eq neq gt lt egt elt heq nheq 默认是eq
     * 格式： {eyou:compare name="" type="eq" value="" }content{/eyou:compare}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagCompare($tag, $content)
    {
        $name  = $tag['name'];
        $value = $tag['value'];
        $type  = isset($tag['type']) ? $tag['type'] : 'eq'; // 比较类型
        $name  = $this->autoBuildVar($name);
        $flag  = substr($value, 0, 1);
        if ('$' == $flag || ':' == $flag) {
            $value = $this->autoBuildVar($value);
        } else {
            $value = '\'' . $value . '\'';
        }
        switch ($type) {
            case 'equal':
                $type = 'eq';
                break;
            case 'notequal':
                $type = 'neq';
                break;
        }
        $type     = $this->parseCondition(' ' . $type . ' ');
        $parseStr = '<?php if(' . $name . ' ' . $type . ' ' . $value . '): ?>' . $content . '<?php endif; ?>';
        return $parseStr;
    }

    /**
     * volist标签解析 循环输出数据集
     * 格式：
     * {eyou:volist name="userList" id="user" empty=""}
     * {user.username}
     * {user.email}
     * {/eyou:volist}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagVolist($tag, $content)
    {
        $name   = $tag['name'];
        $id  = isset($tag['id']) ? $tag['id'] : 'field';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $offset = !empty($tag['offset']) && is_numeric($tag['offset']) ? intval($tag['offset']) : 0;
        $length = !empty($tag['length']) && is_numeric($tag['length']) ? intval($tag['length']) : 'null';
        if (!empty($tag['row'])) {
            $length = !empty($tag['row']) && is_numeric($tag['row']) ? intval($tag['row']) : 'null';
        }
        if (!empty($tag['limit'])) {
            $limitArr = explode(',', $tag['limit']);
            $offset = !empty($limitArr[0]) ? intval($limitArr[0]) : 0;
            $length = !empty($limitArr[1]) ? intval($limitArr[1]) : 'null';
        }
        // 允许使用函数设定数据集 <volist name=":fun('arg')" id="vo">{$vo.name}</volist>
        $parseStr = '<?php ';
        $flag     = substr($name, 0, 1);
        if (':' == $flag) {
            $name = $this->autoBuildVar($name);
            $parseStr .= '$_result=' . $name . ';';
            $name = '$_result';
        } else {
            $name = $this->autoBuildVar($name);
        }

        $parseStr .= 'if(is_array(' . $name . ') || ' . $name . ' instanceof \think\Collection || ' . $name . ' instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        // 设置了输出数组长度
        if (0 != $offset || 'null' != $length) {
            $parseStr .= '$__LIST__ = is_array(' . $name . ') ? array_slice(' . $name . ',' . $offset . ',' . $length . ', true) : ' . $name . '->slice(' . $offset . ',' . $length . ', true); ';
        } else {
            $parseStr .= ' $__LIST__ = ' . $name . ';';
        }
        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$mod = ($e % ' . $mod . ' );';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * global 标签解析
     * 在模板中获取系统的变量值
     * 格式： {eyou:global name="" /}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagGlobal($tag)
    {
        $name = $tag['name'];
        $name  = $this->varOrvalue($name);

        $parseStr = '<?php ';

        // 查询数据库获取的数据集
        $parseStr .= ' $tagGlobal = new \think\template\taglib\eyou\TagGlobal;';
        $parseStr .= ' $__VALUE__ = $tagGlobal->getGlobal('.$name.');';
        $parseStr .= ' echo $__VALUE__;';
        $parseStr .= ' ?>';

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * arcclick 标签解析
     * 在内容页模板追加显示浏览量
     * 格式： {eyou:arcclick aid='' /}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagArcclick($tag)
    {
        $aid = isset($tag['aid']) ? $tag['aid'] : '';
        $aid  = $this->varOrvalue($aid);

        $value = isset($tag['value']) ? $tag['value'] : '';
        $value  = $this->varOrvalue($value);

        $parseStr = '<?php ';

        // 查询数据库获取的数据集
        $parseStr .= ' $tagArcclick = new \think\template\taglib\eyou\TagArcclick;';
        $parseStr .= ' $__VALUE__ = $tagArcclick->getArcclick('.$aid.', '.$value.');';
        $parseStr .= ' echo $__VALUE__;';
        $parseStr .= ' ?>';

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * php标签解析
     * 格式：
     * {eyou:php}echo $name{/eyou:php}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagPhp($tag, $content)
    {
        $parseStr = '<?php ' . $content . ' ?>';
        return $parseStr;
    }

    /**
     * weapp标签解析
     * 安装网站应用插件时自动在页面上追加代码
     * 格式： {eyou:weapp type='default'}content{/eyou:weapp}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagWeapp($tag, $content)
    {
        $type     = isset($tag['type']) ? $tag['type'] : 'default';

        $parseStr = ' <?php ';
        $parseStr .= ' $tagWeapp = new \think\template\taglib\eyou\TagWeapp;';
        $parseStr .= ' $__VALUE__ = $tagWeapp->getWeapp("'.$type.'");';
        $parseStr .= ' echo $__VALUE__;';
        $parseStr .= ' ?>';

        return $parseStr;
    }

    /**
     * range标签解析
     * 如果某个变量存在于某个范围 则输出内容 type= in 表示在范围内 否则表示在范围外
     * 格式： {eyou:range name="var|function"  value="val" type='in|notin' }content{/eyou:range}
     * example: {eyou:range name="a"  value="1,2,3" type='in' }content{/eyou:range}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagRange($tag, $content)
    {
        $name  = $tag['name'];
        $value = $tag['value'];
        $type  = isset($tag['type']) ? $tag['type'] : 'in'; // 比较类型

        $name = $this->autoBuildVar($name);
        $flag = substr($value, 0, 1);
        if ('$' == $flag || ':' == $flag) {
            $value = $this->autoBuildVar($value);
            $str   = 'is_array(' . $value . ')?' . $value . ':explode(\',\',' . $value . ')';
        } else {
            $value = '"' . $value . '"';
            $str   = 'explode(\',\',' . $value . ')';
        }
        if ('between' == $type) {
            $parseStr = '<?php $_RANGE_VAR_=' . $str . ';if(' . $name . '>= $_RANGE_VAR_[0] && ' . $name . '<= $_RANGE_VAR_[1]):?>' . $content . '<?php endif; ?>';
        } elseif ('notbetween' == $type) {
            $parseStr = '<?php $_RANGE_VAR_=' . $str . ';if(' . $name . '<$_RANGE_VAR_[0] || ' . $name . '>$_RANGE_VAR_[1]):?>' . $content . '<?php endif; ?>';
        } else {
            $fun      = ('in' == $type) ? 'in_array' : '!in_array';
            $parseStr = '<?php if(' . $fun . '((' . $name . '), ' . $str . ')): ?>' . $content . '<?php endif; ?>';
        }
        return $parseStr;
    }

    /**
     * present标签解析
     * 如果某个变量已经设置 则输出内容
     * 格式： {eyou:present name="" }content{/eyou:present}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagPresent($tag, $content)
    {
        $name     = $tag['name'];
        $name     = $this->autoBuildVar($name);
        $parseStr = '<?php if(isset(' . $name . ')): ?>' . $content . '<?php endif; ?>';
        return $parseStr;
    }

    /**
     * notpresent标签解析
     * 如果某个变量没有设置，则输出内容
     * 格式： {eyou:notpresent name="" }content{/eyou:notpresent}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagNotpresent($tag, $content)
    {
        $name     = $tag['name'];
        $name     = $this->autoBuildVar($name);
        $parseStr = '<?php if(!isset(' . $name . ')): ?>' . $content . '<?php endif; ?>';
        return $parseStr;
    }

    /**
     * 判断是否已经定义了该常量
     * {eyou:defined name='TXT'}已定义{/eyou:defined}
     * @param array $tag
     * @param string $content
     * @return string
     */
    public function tagDefined($tag, $content)
    {
        $name     = $tag['name'];
        $parseStr = '<?php if(defined("' . $name . '")): ?>' . $content . '<?php endif; ?>';
        return $parseStr;
    }

    /**
     * for标签解析
     * 格式：
     * {eyou:for start="" end="" comparison="" step="" name=""}
     * content
     * {/eyou:for}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagFor($tag, $content)
    {
        //设置默认值
        $start      = 0;
        $end        = 0;
        $step       = 1;
        $comparison = 'lt';
        $name       = 'i';
        $rand       = rand(); //添加随机数，防止嵌套变量冲突
        //获取属性
        foreach ($tag as $key => $value) {
            $value = trim($value);
            $flag  = substr($value, 0, 1);
            if ('$' == $flag || ':' == $flag) {
                $value = $this->autoBuildVar($value);
            }

            switch ($key) {
                case 'start':
                    $start = $value;
                    break;
                case 'end':
                    $end = $value;
                    break;
                case 'step':
                    $step = $value;
                    break;
                case 'comparison':
                    $comparison = $value;
                    break;
                case 'name':
                    $name = $value;
                    break;
            }
        }

        $parseStr = '<?php $__FOR_START_' . $rand . '__=' . $start . ';$__FOR_END_' . $rand . '__=' . $end . ';';
        $parseStr .= 'for($' . $name . '=$__FOR_START_' . $rand . '__;' . $this->parseCondition('$' . $name . ' ' . $comparison . ' $__FOR_END_' . $rand . '__') . ';$' . $name . '+=' . $step . '){ ?>';
        $parseStr .= $content;
        $parseStr .= '<?php } ?>';
        return $parseStr;
    }

    /**
     * url函数的tag标签
     * 格式：{eyou:url link="模块/控制器/方法" vars="参数" suffix="true或者false 是否带有后缀" domain="true或者false 是否携带域名" /}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagUrl($tag, $content)
    {
        $url    = isset($tag['link']) ? $tag['link'] : '';
        $vars   = isset($tag['vars']) ? $tag['vars'] : '';
        $suffix = isset($tag['suffix']) ? $tag['suffix'] : 'true';
        $domain = isset($tag['domain']) ? $tag['domain'] : 'false';
        return '<?php echo url("' . $url . '","' . $vars . '",' . $suffix . ',' . $domain . ');?>';
    }

    /**
     * function标签解析 匿名函数，可实现递归
     * 使用：
     * {eyou:function name="func" vars="$data" call="$list" use="&$a,&$b"}
     *      {eyou:if is_array($data)}
     *          {eyou:foreach $data as $val}
     *              {~func($val) /}
     *          {/eyou:foreach}
     *      {eyou:else /}
     *          {$data}
     *      {/eyou:if}
     * {/eyou:function}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagFunction($tag, $content)
    {
        $name = !empty($tag['name']) ? $tag['name'] : 'func';
        $vars = !empty($tag['vars']) ? $tag['vars'] : '';
        $call = !empty($tag['call']) ? $tag['call'] : '';
        $use  = ['&$' . $name];
        if (!empty($tag['use'])) {
            foreach (explode(',', $tag['use']) as $val) {
                $use[] = '&' . ltrim(trim($val), '&');
            }
        }
        $parseStr = '<?php $' . $name . '=function(' . $vars . ') use(' . implode(',', $use) . ') {';
        $parseStr .= ' ?>' . $content . '<?php }; ';
        $parseStr .= $call ? '$' . $name . '(' . $call . '); ?>' : '?>';
        return $parseStr;
    }

    /**
     * diyfield标签解析 循环输出自定义字段图集
     * 格式：
     * {eyou:diyfield type="default" name="$eyou.field.imgs" id="field"}
     * <img src="{$field.image_url}" />
     * {/eyou:diyfield}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagDiyfield($tag, $content)
    {
        $name   = $tag['name'];
        $id  = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $type    = isset($tag['type']) ? $tag['type'] : 'default';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $offset = 0;
        $length = 'null';
        if (!empty($tag['limit'])) {
            $limitArr = explode(',', $tag['limit']);
            $offset = !empty($limitArr[0]) ? intval($limitArr[0]) : 0;
            $length = !empty($limitArr[1]) ? intval($limitArr[1]) : 'null';
        }

        $parseStr = '<?php ';
        $flag     = substr($name, 0, 1);
        if (':' == $flag) {
            $name = $this->autoBuildVar($name);
            $parseStr .= '$_result=' . $name . ';';
            $name = '$_result';
        } else {
            $name = $this->autoBuildVar($name);
        }

        // 查询数据库获取的数据集
        $parseStr .= ' $tagDiyfield = new \think\template\taglib\eyou\TagDiyfield;';
        $parseStr .= $name . ' = $tagDiyfield->getDiyfield('.$name.', "'.$type.'");';

        $parseStr .= 'if(is_array(' . $name . ') || ' . $name . ' instanceof \think\Collection || ' . $name . ' instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        // 设置了输出数组长度
        if (0 != $offset || 'null' != $length) {
            $parseStr .= '$__LIST__ = is_array(' . $name . ') ? array_slice(' . $name . ',' . $offset . ',' . $length . ', true) : ' . $name . '->slice(' . $offset . ',' . $length . ', true); ';
        } else {
            $parseStr .= ' $__LIST__ = ' . $name . ';';
        }
        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$mod = ($e % ' . $mod . ' );';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * attribute 栏目属性标签解析 TAG调用
     * {eyou:attribute type='default'}
        {$field.itemname_2}:{$field.attr_2}
     * {/eyou:attribute}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagAttribute($tag, $content)
    {
        $aid   = !empty($tag['aid']) ? $tag['aid'] : '';
        $aid  = $this->varOrvalue($aid);
        $type   = !empty($tag['type']) ? $tag['type'] : 'default';
        $id     = isset($tag['id']) ? $tag['id'] : 'attr';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);

        $parseStr = '<?php ';

        /*aid的优先级别从高到低：标签属性值 -> 外层标签list/arclist属性值*/
        $parseStr .= ' if(empty($aid)) : $aid_tmp = '.$aid.'; endif; ';
        $parseStr .= ' $taid = 0; ';
        $parseStr .= ' if(!empty($aid_tmp)) : $taid = $aid_tmp; elseif(!empty($aid)) : $taid = $aid; endif;';
        /*--end*/

        // 查询数据库获取的数据集
        $parseStr .= ' $tagAttribute = new \think\template\taglib\eyou\TagAttribute;';
        $parseStr .= ' $_result = $tagAttribute->getAttribute($taid, "'.$type.'");';
        $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        $parseStr .= ' $__LIST__ = $_result;';

        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$mod = ($e % ' . $mod . ' );';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach;';
        $parseStr .= 'endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * attr 标签解析
     * 在模板中获取栏目属性值
     * 格式： {eyou:attr name="" /}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagAttr($tag)
    {
        $aid   = !empty($tag['aid']) ? $tag['aid'] : '';
        $aid  = $this->varOrvalue($aid);
        $name     = isset($tag['name']) ? $tag['name'] : '';

        $parseStr = '<?php ';

        /*aid的优先级别从高到低：标签属性值 -> 外层标签list/arclist属性值*/
        $parseStr .= ' $aid_tmp = '.$aid.'; ';
        $parseStr .= ' if(!empty($aid_tmp)) : $taid = $aid_tmp; else : $taid = $aid; endif;';
        /*--end*/

        // 查询数据库获取的数据集
        $parseStr .= ' $tagAttr = new \think\template\taglib\eyou\TagAttr;';
        $parseStr .= ' $__VALUE__ = $tagAttr->getAttr($taid,"'.$name.'");';
        $parseStr .= ' echo $__VALUE__;';
        $parseStr .= ' ?>';
        $parseStr .= '<?php unset($aid_tmp); ?>';

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * user 标签解析
     * 在模板中获取会员登录入口
     * 格式：
     * {eyou:user type='default'}
     *  <a href="{$field.url}">{$field.username}</a>
     * {/eyou:user}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagUser($tag, $content)
    {
        $type  =  !empty($tag['type']) ? $tag['type'] : 'default';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key     = isset($tag['key']) ? $tag['key'] : 'i';
        $txt  =  !empty($tag['txt']) ? $tag['txt'] : '';
        $txt  = $this->varOrvalue($txt);
        $txtid  =  !empty($tag['txtid']) ? $tag['txtid'] : '';
        $img  =  !empty($tag['img']) ? $tag['img'] : 'off';
        $currentstyle   = !empty($tag['currentstyle']) ? $tag['currentstyle'] : '';

        $parseStr = '<?php ';
        $parseStr .= ' $tagUser = new \think\template\taglib\eyou\TagUser;';
        $parseStr .= ' $__LIST__ = $tagUser->getUser("'.$type.'", "'.$img.'", "'.$currentstyle.'", '.$txt.', "'.$txtid.'");';
        $parseStr .= '?>';

        $parseStr .= '<?php if(!empty($__LIST__) || (($__LIST__ instanceof \think\Collection || $__LIST__ instanceof \think\Paginator ) && $__LIST__->isEmpty())): ?>';
        $parseStr .= '<?php $'.$id.' = $__LIST__; ?>';
        $parseStr .= $content;
        $parseStr .= '<?php endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * weapplist标签解析
     * 安装网站应用插件时自动在页面上追加代码
     * 格式： {eyou:weapplist type='default'}content{/eyou:weapplist}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagWeapplist($tag, $content)
    {
        $type     = isset($tag['type']) ? $tag['type'] : 'default';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key     = isset($tag['key']) ? $tag['key'] : 'i';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $currentstyle   = !empty($tag['currentstyle']) ? $tag['currentstyle'] : '';

        $parseStr = '<?php ';

        // 查询数据库获取的数据集
        $parseStr .= ' $tagWeapplist = new \think\template\taglib\eyou\TagWeapplist;';
        $parseStr .= ' $_result = $tagWeapplist->getWeapplist("'.$type.'","'.$currentstyle.'");';
        $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        $parseStr .= ' $__LIST__ = $_result;';

        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$mod = ($e % ' . $mod . ' );';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * usermenu 会员左侧菜单
     * 格式：
     * {eyou:usermenu currentstyle=''}
     *  <a href="{$field:url}">{$field:title}</a>
     * {/eyou:usermenu}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string
     */
    public function tagUsermenu($tag, $content)
    {
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $currentstyle   = !empty($tag['currentstyle']) ? $tag['currentstyle'] : '';
        $row = !empty($tag['row']) ? intval($tag['row']) : 0;
        $limit   = !empty($tag['limit']) ? $tag['limit'] : '';
        if (empty($limit) && !empty($row)) {
            $limit = "0,{$row}";
        }

        $parseStr = '<?php ';

        // 查询数据库获取的数据集
        $parseStr .= ' $tagUsermenu = new \think\template\taglib\eyou\TagUsermenu;';
        $parseStr .= ' $_result = $tagUsermenu->getUsermenu("'.$currentstyle.'", "'.$limit.'");';
        $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        $parseStr .= ' $__LIST__ = $_result;';
        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$mod = ($e % ' . $mod . ' );';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * sppurchase 标签解析
     * 在模板中获取会员登录入口
     * 格式：
     * {eyou:sppurchase id='field'}
     *  <li><a href='{$field:url}'>{$field:title}</a> </li> 
     * {/eyou:sppurchase}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagSppurchase($tag, $content)
    {
        $type  =  !empty($tag['type']) ? $tag['type'] : 'default';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key     = isset($tag['key']) ? $tag['key'] : 'i';
        $txt  =  !empty($tag['txt']) ? $tag['txt'] : '';
        $txt  = $this->varOrvalue($txt);
        $img  =  !empty($tag['img']) ? $tag['img'] : 'off';
        $currentstyle   = !empty($tag['currentstyle']) ? $tag['currentstyle'] : '';

        $parseStr = '<?php ';
        $parseStr .= ' $tagSppurchase = new \think\template\taglib\eyou\TagSppurchase;';
        $parseStr .= ' $__LIST__ = $tagSppurchase->getSppurchase();';
        $parseStr .= '?>';

        $parseStr .= '<?php if(!empty($__LIST__) || (($__LIST__ instanceof \think\Collection || $__LIST__ instanceof \think\Paginator ) && $__LIST__->isEmpty())): ?>';
        $parseStr .= '<?php $'.$id.' = $__LIST__; ?>';
        $parseStr .= $content;
        $parseStr .= '<?php endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * spcart 标签解析 TAG调用
     * 格式：sort:排序方式 month，rand，week
     *       getall:获取类型 0 为当前内容页TAG标记，1为获取全部TAG标记
     * {eyou:spcart row='1' titlelen='20'}
     *  <li><a href='{$field:url}'>{$field:title}</a> </li> 
     * {/eyou:spcart}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagSpcart($tag, $content)
    {
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $row    = !empty($tag['row']) ? intval($tag['row']) : 0;
        $limit  = !empty($tag['limit']) ? $tag['limit'] : '';
        if (empty($limit) && !empty($row)) {
            $limit = "0,{$row}";
        }

        $parseStr = '<?php ';
        $parseStr .= ' $tagSpcart = new \think\template\taglib\eyou\TagSpcart;';
        $parseStr .= ' $_result = $tagSpcart->getSpcart("'.$limit.'");';
        $parseStr .= '?>';

        $parseStr .= '<?php if(!empty($_result["list"]) || (($_result["list"] instanceof \think\Collection || $_result["list"] instanceof \think\Paginator ) && $_result["list"]->isEmpty())): ?>';
        $parseStr .= '<?php $'.$id.' = $_result; ?>';
        $parseStr .= '<?php $__SHOPCART_LIST__ = $_result["list"]; ?>';
        $parseStr .= $content;
        $parseStr .= '<?php endif; ?>';
        $parseStr .= '<?php $__SHOPCART_LIST__ = ""; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用
        
        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * sporder 标签解析 TAG调用
     * 格式：sort:排序方式 month，rand，week
     *       getall:获取类型 0 为当前内容页TAG标记，1为获取全部TAG标记
     * {eyou:sporder row='1' titlelen='20'}
     *  <li><a href='{$field:url}'>{$field:title}</a> </li> 
     * {/eyou:sporder}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagSporder($tag, $content)
    {
        $name   = isset($tag['name']) ? $tag['name'] : '';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $row    = !empty($tag['row']) ? intval($tag['row']) : 0;
        $limit  = !empty($tag['limit']) ? $tag['limit'] : '';
        $order_id  = !empty($tag['order_id']) ? $tag['order_id'] : '';
        $order_id  = $this->varOrvalue($order_id);

        // 查询数据库获取的数据集
        $parseStr = '<?php ';
        $parseStr .= ' $tagSporder = new \think\template\taglib\eyou\TagSporder;';
        $parseStr .= ' $_result = $tagSporder->getSporder('.$order_id.');';
        $parseStr .= '?>';

        // 赋值数据
        $parseStr .= '<?php if(!empty($_result["OrderData"]) || (($_result["OrderData"] instanceof \think\Collection || $_result["OrderData"] instanceof \think\Paginator ) && $_result["OrderData"]->isEmpty())): ?>';
        $parseStr .= '<?php $'.$id.' = $_result["OrderData"]; ?>';
        $parseStr .= '<?php $__SHOPCART_LIST__ = $_result["DetailsData"]; ?>';
        $parseStr .= $content;
        $parseStr .= '<?php endif; ?>';
        $parseStr .= '<?php $__SHOPCART_LIST__ = ""; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * spsubmitorder 标签解析 TAG调用
     * 格式：sort:排序方式 month，rand，week
     *       getall:获取类型 0 为当前内容页TAG标记，1为获取全部TAG标记
     * {eyou:spsubmitorder row='1' titlelen='20'}
     *  <li><a href='{$field:url}'>{$field:title}</a> </li> 
     * {/eyou:spsubmitorder}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagSpsubmitorder($tag, $content)
    {
        $name   = isset($tag['name']) ? $tag['name'] : '';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $row    = !empty($tag['row']) ? intval($tag['row']) : 0;
        $limit  = !empty($tag['limit']) ? $tag['limit'] : '';

        // 查询数据库获取的数据集
        $parseStr = '<?php ';
        $parseStr .= ' $tagSpsubmitorder = new \think\template\taglib\eyou\TagSpsubmitorder;';
        $parseStr .= ' $_result = $tagSpsubmitorder->getSpsubmitorder();';
        $parseStr .= '?>';

        // 赋值数据
        $parseStr .= '<?php if(!empty($_result["data"]) || (($_result["data"] instanceof \think\Collection || $_result["data"] instanceof \think\Paginator ) && $_result["data"]->isEmpty())): ?>';
        $parseStr .= '<?php $'.$id.' = $_result["data"]; ?>';
        $parseStr .= '<?php $__SHOPCART_LIST__ = $_result["list"]; ?>';
        $parseStr .= $content;
        $parseStr .= '<?php endif; ?>';
        $parseStr .= '<?php $__SHOPCART_LIST__ = ""; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * sporderlist 标签解析 TAG调用
     * 格式：sort:排序方式 month，rand，week
     *       getall:获取类型 0 为当前内容页TAG标记，1为获取全部TAG标记
     * {eyou:sporderlist row='1' titlelen='20'}
     *  <li><a href='{$field:url}'>{$field:title}</a> </li> 
     * {/eyou:sporderlist}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagSporderlist($tag, $content)
    {
        $name   = isset($tag['name']) ? $tag['name'] : '';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $row    = !empty($tag['row']) ? intval($tag['row']) : 0;
        $limit  = !empty($tag['limit']) ? $tag['limit'] : '';
        $pagesize = !empty($tag['pagesize']) && is_numeric($tag['pagesize']) ? intval($tag['pagesize']) : 10;
        if (empty($limit) && !empty($row)) {
            $limit = "0,{$row}";
        }

        $parseStr = '<?php ';
        // 查询数据库获取的数据集
        $parseStr .= ' $tagSporderlist = new \think\template\taglib\eyou\TagSporderlist;';
        $parseStr .= ' $_result = $tagSporderlist->getSporderlist("'.$pagesize.'");';

        $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        $parseStr .= ' $__LIST__ = $_result["list"];';
        $parseStr .= ' $__PAGES_ORDER__ = $_result["pages"];';
        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        // 遍及数据
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= ' $__SHOPCART_LIST__ = $' . $id . '["details"];';
        $parseStr .= ' $mod = ($e % ' . $mod . ' );';
        $parseStr .= ' $' . $key . '= intval($key) + 1;?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php $__SHOPCART_LIST__ = ""; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * sppageorder 标签解析
     * 在模板中获取列表的分页
     * 格式： {eyou:sppageorder listitem='info,index,end,pre,next,pageno' listsize='2'/}
     * @access public
     * @param array $tag 标签属性
     * @return string
     */
    public function tagSppageorder($tag)
    {
        $listitem  = !empty($tag['listitem']) ? $tag['listitem'] : '';
        $listsize  = !empty($tag['listsize']) ? intval($tag['listsize']) : '';

        $parseStr  = ' <?php ';
        $parseStr .= ' $__PAGES_ORDER__ = isset($__PAGES_ORDER__) ? $__PAGES_ORDER__ : "";';
        $parseStr .= ' $tagPagelist = new \think\template\taglib\eyou\TagPagelist;';
        $parseStr .= ' $__VALUE__ = $tagPagelist->getPagelist($__PAGES_ORDER__, "'.$listitem.'", "'.$listsize.'");';
        $parseStr .= ' echo $__VALUE__;';
        $parseStr .= ' ?>';

        return $parseStr;
    }

    /**
     * spordergoods 标签解析 TAG调用
     * 格式：sort:排序方式 month，rand，week
     *       getall:获取类型 0 为当前内容页TAG标记，1为获取全部TAG标记
     * {eyou:spordergoods row='1' titlelen='20'}
     *  <li><a href='{$field:url}'>{$field:title}</a> </li> 
     * {/eyou:spordergoods}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagSpordergoods($tag, $content)
    {
        $name   = isset($tag['name']) ? $tag['name'] : '';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $row = !empty($tag['row']) ? intval($tag['row']) : 0;
        // $titlelen = !empty($tag['titlelen']) && is_numeric($tag['titlelen']) ? intval($tag['titlelen']) : 100;
        $offset = !empty($tag['offset']) && is_numeric($tag['offset']) ? intval($tag['offset']) : 0;
        $row = !empty($tag['row']) && is_numeric($tag['row']) ? intval($tag['row']) : 10;
        if (!empty($tag['limit'])) {
            $limitArr = explode(',', $tag['limit']);
            $offset = !empty($limitArr[0]) ? intval($limitArr[0]) : 0;
            $row = !empty($limitArr[1]) ? intval($limitArr[1]) : 0;
        }

        $parseStr = '<?php ';

        if ($name) { // 从模板中传入数据集
            $symbol     = substr($name, 0, 1);
            if (':' == $symbol) {
                $name = $this->autoBuildVar($name);
                $parseStr .= '$_result=' . $name . ';';
                $name = '$_result';
            } else {
                $name = $this->autoBuildVar($name);
            }

            $parseStr .= 'if(is_array(' . $name . ') || ' . $name . ' instanceof \think\Collection || ' . $name . ' instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
            // 设置了输出数组长度
            if (0 != $offset || 'null' != $row) {
                $parseStr .= '$__LIST__ = is_array(' . $name . ') ? array_slice(' . $name . ',' . $offset . ',' . $row . ', true) : ' . $name . '->slice(' . $offset . ',' . $row . ', true); ';
            } else {
                $parseStr .= ' $__LIST__ = ' . $name . ';';
            }

        } else { // 查询数据库获取的数据集
            $parseStr .= ' if(isset($__SHOPCART_LIST__) && !empty($__SHOPCART_LIST__)) : $_result = $__SHOPCART_LIST__; else : $_result = []; endif;';
            $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
            // 设置了输出数组长度
            if (0 != $offset || 'null' != $row) {
                $parseStr .= '$__LIST__ = is_array($_result) ? array_slice($_result,' . $offset . ',' . $row . ', true) : $_result->slice(' . $offset . ',' . $row . ', true); ';
            } else {
                $parseStr .= ' $__LIST__ = $_result;';
            }
        }

        // 查询数据库获取的数据集
        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        // $parseStr .= '$' . $id . '["title"] = text_msubstr($' . $id . '["title"], 0, '.$titlelen.', false);';
        $parseStr .= ' $__LIST__[$key] = $_result[$key] = $' . $id . ';';
        $parseStr .= '$mod = ($e % ' . $mod . ' );';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用
        
        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * spaddress 标签解析 TAG调用
     * 格式：sort:排序方式 month，rand，week
     *       getall:获取类型 0 为当前内容页TAG标记，1为获取全部TAG标记
     * {eyou:spaddress row='1' titlelen='20'}
     *  <li><a href='{$field:url}'>{$field:title}</a> </li> 
     * {/eyou:spaddress}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagSpaddress($tag, $content)
    {
        $name   = isset($tag['name']) ? $tag['name'] : '';
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $row    = !empty($tag['row']) ? intval($tag['row']) : 0;
        $limit  = !empty($tag['limit']) ? $tag['limit'] : '';
        $type   = !empty($tag['type']) ? $tag['type'] : 'data';

        $parseStr = '<?php ';
        // 查询数据库获取的数据集
        $parseStr .= ' $tagSpaddress = new \think\template\taglib\eyou\TagSpaddress;';
        $parseStr .= ' $_result = $tagSpaddress->getSpaddress("'.$type.'");';
        $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        $parseStr .= ' $__LIST__ = $_result;';
        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        // 遍及数据
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$mod = ($e % ' . $mod . ' );';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach; endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }


    /**
     * spsearch 订单搜索标签解析 TAG调用
     * {eyou:spsearch id='field'}
        {$field.searchurl}
     * {/eyou:spsearch}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     * @return string|void
     */
    public function tagSpsearch($tag, $content)
    {
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);

        $parseStr = '<?php ';

        // 查询数据库获取的数据集
        $parseStr .= ' $tagSpsearch = new \think\template\taglib\eyou\TagSpsearch;';
        $parseStr .= ' $_result = $tagSpsearch->getSpsearch();';
        $parseStr .= ' if(is_array($_result) || $_result instanceof \think\Collection || $_result instanceof \think\Paginator): $' . $key . ' = 0; $e = 1;';
        $parseStr .= ' $__LIST__ = $_result;';

        $parseStr .= 'if( count($__LIST__)==0 ) : echo htmlspecialchars_decode("' . $empty . '");';
        $parseStr .= 'else: ';
        $parseStr .= 'foreach($__LIST__ as $key=>$' . $id . '): ';
        $parseStr .= '$mod = ($e % ' . $mod . ' );';
        $parseStr .= '$' . $key . '= intval($key) + 1;?>';
        $parseStr .= $content;
        $parseStr .= '<?php ++$e; ?>';
        $parseStr .= '<?php endforeach;';
        $parseStr .= 'endif; else: echo htmlspecialchars_decode("' . $empty . '");endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用

        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }

    /**
     * spstatus 
     * 格式：
     * {eyou:spstatus }
     *  <em>{$field3.PendingPayment}</em> 
     * {/eyou:spstatus}
     * @access public
     * @param array $tag 标签属性
     * @param string $content 标签内容
     */
    public function tagSpstatus($tag, $content)
    {
        $id     = isset($tag['id']) ? $tag['id'] : 'field';
        $key    = !empty($tag['key']) ? $tag['key'] : 'i';
        $empty  = isset($tag['empty']) ? $tag['empty'] : '';
        $empty  = htmlspecialchars($empty);
        $mod    = isset($tag['mod']) ? $tag['mod'] : '2';
        $row    = !empty($tag['row']) ? intval($tag['row']) : 0;

        $parseStr = '<?php ';
        $parseStr .= ' $tagSporderlist = new \think\template\taglib\eyou\TagSporderlist;';
        $parseStr .= ' $_result = $tagSporderlist->getSpstatus();';
        $parseStr .= '?>';

        $parseStr .= '<?php if(!empty($_result) || (($_result instanceof \think\Collection || $_result instanceof \think\Paginator ) && $_result->isEmpty())): ?>';
        $parseStr .= '<?php $'.$id.' = $_result; ?>';
        $parseStr .= $content;
        $parseStr .= '<?php endif; ?>';
        $parseStr .= '<?php $'.$id.' = []; ?>'; // 清除变量值，只限于在标签内部使用
        
        if (!empty($parseStr)) {
            return $parseStr;
        }
        return;
    }
}
