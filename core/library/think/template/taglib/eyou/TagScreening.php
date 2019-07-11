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
 * Date: 2019-6-5
 */

namespace think\template\taglib\eyou;

use think\Request;
use think\Db;

/**
 * 搜索表单
 */
class TagScreening extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->channelfield_db = Db::name('channelfield');
        $this->dirname = input('param.tid/s');
        $this->tid = 0;
    }

    // URL中隐藏index.php入口文件，此方法仅此控制器使用到
    private function auto_hide_index($url = '')
    {
        if (empty($url)) return false;
        // 是否开启去除index.php文件
        $seo_inlet = null;
        $seo_inlet === null && $seo_inlet = config('ey_config.seo_inlet');
        if (1 == $seo_inlet) {
            $url = str_replace('/index.php', '/', $url);
        }
        return $url;
    }

    /**
     * 获取搜索表单
     */
    public function getScreening($currentstyle='', $addfields='', $addfieldids='', $alltxt='')
    {
        if ($this->home_lang != $this->main_lang) {
            return false;
        }
        
        $param = input('param.');
        // 定义筛选标识
        $url_screen_var = config('global.url_screen_var');
        // 隐藏域参数处理
        $hidden  = '';
        // 是否在伪静态下搜索
        $seo_pseudo = config('ey_config.seo_pseudo');
        if (!isset($param[$url_screen_var]) && 3 == $seo_pseudo) {
            $arctype_where = [
                'dirname' => $this->dirname,
                'lang'    => $this->home_lang,
            ];
            $this->tid = Db::name('arctype')->where($arctype_where)->getField('id');
        }else{
            $this->tid = input('param.tid/d');
        }

        // 查询数据条件
        $where = [
            'a.is_screening' => 1,
            'a.ifeditable'   => 1,
            'b.typeid'       => $this->tid,
            // 根据需求新增条件
        ];

        // 是否指定参数读取
        if (!empty($addfields)) {
            $addfieldids = '';
            $where['a.name'] = array('IN',$addfields);
        }else if (!empty($addfieldids)){
            $where['a.id'] = array('IN',$addfieldids);
        }

        // 数据查询
        $row = $this->channelfield_db
            ->field('a.id,a.title,a.name,a.dfvalue,a.dtype')
            ->alias('a')
            ->join('__CHANNELFIELD_BIND__ b', 'b.field_id = a.id', 'LEFT')
            ->where($where)
            ->select();

        // Onclick点击事件方法名称加密，防止冲突
        $OnclickScreening  = 'ey_'.md5('OnclickScreening');
        // Onchange改变事件方法名称加密，防止冲突
        $OnchangeScreening = 'ey_'.md5('OnchangeScreening');
        // 定义搜索点击的name值
        $is_data = '';
        // 数据处理输出
        foreach ($row as $key => $value) {
            // 搜索的name值
            $name = $value['name'];
            // 封装onClick事件
            $row[$key]['onClick']  = "onClick='{$OnclickScreening}(this);'";
            // 封装onchange事件
            $row[$key]['onChange'] = "onChange='{$OnchangeScreening}(this);'";
            // 在伪静态下拼装控制器方式参数名
            $seo_pseudo  = config('ey_config.seo_pseudo');
            if (!isset($param[$url_screen_var]) && 3 == $seo_pseudo) {
                $param_query = [];
                $param_query['m'] = 'home';
                $param_query['c'] = 'Lists';
                $param_query['a'] = 'index';
                $param_query['tid'] = $this->tid;
                $param_new = request()->param();
                unset($param_new['tid']);
                $param_query = array_merge($param_query, $param_new);
            } else {
                $param_query = request()->param();
            }

            /* 生成静态页面代码 */
            if (2 == $seo_pseudo && !isMobile()) {
                $param_query['m'] = 'home';
                $param_query['c'] = 'Lists';
                $param_query['a'] = 'index';
                unset($param_query['_ajax']);
            }
            /* end */
            
            // 筛选值处理
            if ('region' == $value['dtype']) {
                // 类型为区域则执行
                // 处理自定义参数名称
                if (!empty($alltxt)) {
                    // 等于OFF表示关闭，不需要此项
                    if ('off' == $alltxt) {
                        $alltxt = '';    
                    }
                }else{
                    $alltxt = '全部';
                }
                // 拼装数组
                $all[0] = [
                    'id'   => '',
                    'name' => $alltxt,
                ];
                if (isset($param[$name]) && !empty($param[$name])) {
                    // 搜索点击的name值
                    $is_data = $param[$name];
                }else{
                    $is_data = $alltxt;
                }

                // 处理后台添加的区域数据
                $RegionData = [];
                // 反序列化参数值
                $dfvalue = unserialize($value['dfvalue']);
                // 拆分ID值
                $region_ids = explode(',', $dfvalue['region_ids']);
                foreach ($region_ids as $id_key => $id_value) {
                    $RegionData[$id_key]['id'] = $id_value;
                }
                // 拆分name值
                $region_names = explode('，', $dfvalue['region_names']);
                foreach ($region_names as $name_key => $name_value) {
                    $RegionData[$name_key]['name'] = $name_value;
                }
                // 合并数组
                $RegionData = array_merge($all,$RegionData);

                // 处理参数输出
                foreach ($RegionData as $kk => $vv) {
                    // 参数拼装URL
                    if (!empty($vv['id'])) {
                        $param_query[$name] = $vv['id'];
                    }else{
                        unset($param_query[$name]);
                    }
                    /* 筛选标识始终追加在最后 */
                    unset($param_query[$url_screen_var]);
                    $param_query[$url_screen_var] = 1;
                    /* end */
                    $url = ROOT_DIR.'/index.php?'.http_build_query($param_query);
                    $url = urldecode($url);
                    $url = $this->auto_hide_index($url);
                    // 拼装onClick事件
                    $RegionData[$kk]['onClick'] = $row[$key]['onClick']." data-url='{$url}'";
                    // 拼装onchange参数
                    $RegionData[$kk]['SelectUrl'] = "data-url='{$url}'";
                    // 初始化参数，默认未选中
                    $RegionData[$kk]['name']         = "{$vv['name']}";
                    $RegionData[$kk]['SelectValue']  = "";
                    $RegionData[$kk]['currentstyle'] = "";
                    // 选中时执行
                    if ($vv['id'] == $is_data) {
                        $RegionData[$kk]['name']         = "<b>{$vv['name']}</b>";
                        $RegionData[$kk]['SelectValue']  = "selected";
                        $RegionData[$kk]['currentstyle'] = $currentstyle;
                    }else if ($vv['name'] == $alltxt && $is_data == $alltxt) {
                        $RegionData[$kk]['name']         = "<b>{$vv['name']}</b>";
                        $RegionData[$kk]['SelectValue']  = "selected";
                        $RegionData[$kk]['currentstyle'] = $currentstyle;
                    }
                }
                // 数据赋值到数组中
                $row[$key]['dfvalue'] = $RegionData;
            }else{
                // 类型不为区域则执行
                $dfvalue = explode(',', $value['dfvalue']);
                $all[0] = '全部';
                if (!empty($alltxt)) {
                    // 等于OFF表示关闭，不需要此项
                    if ('off' == $alltxt) {
                        $all[0] = '';    
                    }else{
                        $all[0] = $alltxt;
                    }
                }

                if (isset($param[$name]) && !empty($param[$name])) {
                    // 搜索点击的name值
                    $is_data = $param[$name];
                }else{
                    $is_data = $alltxt;
                }
                
                // 合并数组
                $dfvalue  = array_merge($all,$dfvalue);
                // 处理参数输出
                $data_new = [];
                foreach ($dfvalue as $kk => $vv) {
                    $param_query[$name]    = $vv;
                    $data_new[$kk]['id']           = $vv;
                    $data_new[$kk]['name']         = "{$vv}";
                    $data_new[$kk]['SelectValue']  = "";
                    $data_new[$kk]['currentstyle'] = "";

                    // 目前单选类型选中和多选类型选中的数据处理是相同的，后续可能会有优化，暂时保留两个判断
                    if ($vv == $is_data) {
                        // 单选/下拉类型选中
                        $data_new[$kk]['name']         = "<b>{$vv}</b>";
                        $data_new[$kk]['SelectValue']  = "selected";
                        $data_new[$kk]['currentstyle'] = $currentstyle;

                    }else if ($vv.'|' == $is_data) {
                        // 多选类型选中
                        $data_new[$kk]['name']         = "<b>{$vv}</b>";
                        $data_new[$kk]['SelectValue']  = "selected";
                        $data_new[$kk]['currentstyle'] = $currentstyle;

                    }else if ($vv == $all[0] && empty($is_data)) {
                        // “全部” 按钮选中
                        $data_new[$kk]['name']         = "<b>{$vv}</b>";
                        $data_new[$kk]['SelectValue']  = "selected";
                        $data_new[$kk]['currentstyle'] = $currentstyle;

                    }

                    if ($all[0] == $vv) {
                        // 若选中 “全部” 按钮则清除这个字段参数
                        unset($param_query[$name]);
                    }else if ('checkbox' == $value['dtype']) {
                        // 等于多选类型，则拼装上-号，用于搜索时分割，可匹配数据
                        $param_query[$name] = $vv.'|';
                    }
                    /* 筛选标识始终追加在最后 */
                    unset($param_query[$url_screen_var]);
                    $param_query[$url_screen_var] = 1;
                    /* end */
                    // 参数拼装URL
                    $url = ROOT_DIR.'/index.php?'.http_build_query($param_query);
                    $url = urldecode($url);
                    $url = $this->auto_hide_index($url);
                    // 封装onClick
                    $data_new[$kk]['onClick'] = $row[$key]['onClick']." data-url='{$url}'";
                    // 封装onchange事件
                    $data_new[$kk]['SelectUrl'] = "data-url='{$url}'";
                }

                // 数据赋值到数组中
                $row[$key]['dfvalue'] = $data_new;
            }
        }
        
        $resetUrl = ROOT_DIR.'/index.php?m=home&c=Lists&a=index&tid='.$this->tid.'&'.$url_screen_var.'=1';

        $hidden .= <<<EOF
<script type="text/javascript">
    function {$OnclickScreening}(obj) {
        var dataurl = $(obj).attr('data-url');
        if (dataurl) {
            window.location.href = dataurl;
        }else{
            layer.msg(res.msg, {time: 2000, icon: 2});
        }
    }

    function {$OnchangeScreening}(obj) {
        var dataurl = $(obj).find("option:selected").attr('data-url');
        if (dataurl) {
            window.location.href = dataurl;
        }else{
            layer.msg(res.msg, {time: 2000, icon: 2});
        }
    }
</script>
EOF;
        $result = array(
            'hidden'    => $hidden,
            'resetUrl' => $resetUrl,
            'list'       => $row,
        );
        return $result;
    }
}