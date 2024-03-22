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

/**
 * 会员列表
 */
class TagMemberlist extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取会员列表
     * @author 小虎哥 by 2018-4-20
     */
    public function getMemberlist($limit = '', $orderby = '', $ordermode = '', $js = '', $attarray = '')
    {
        /*加载js*/
        $data = $this->getMemberlistJs($attarray);
        return $data;
        // if (empty($js)) {
            
        // }
        /*end*/

        // $condition = [
        //     'admin_id'  => 0,
        //     'lang'      => self::$home_lang,
        // ];

        // switch ($orderby) {
        //     case 'logintime': // 兼容写法
        //     case 'last_login':
        //         $orderby = "last_login {$ordermode}";
        //         break;

        //     case 'users_id':
        //         $orderby = "users_id {$ordermode}";
        //         break;
                
        //     case 'regtime':
        //     case 'reg_time':
        //         $orderby = "reg_time {$ordermode}";
        //         break;

        //     default:
        //     {
        //         $fieldList = Db::name('users')->getTableFields();
        //         if (in_array($orderby, $fieldList)) {
        //             $orderby = "{$orderby} {$ordermode}";
        //         } else {
        //             $orderby = "users_id desc";
        //         }
        //         break;
        //     }
        // }

        // $list = Db::name("users")->field('password,paypwd', true)
        //     ->where($condition)
        //     ->order($orderby)
        //     ->limit($limit)
        //     ->select();
        // if (empty($list)) {
        //     return false;
        // }

        // foreach ($list as $key => $val) {
        //     $val['head_pic'] = get_head_pic($val['head_pic'], false, $val['sex']);
        //     $list[$key] = $val;
        // }

        // return $list;
    }

    /**
     * 获取会员列表的JS
     * @author 小虎哥 by 2018-4-20
     */
    private function getMemberlistJs($attarray = '')
    {
        $result = [];
        $t_uniqid = md5(getTime().uniqid(mt_rand(), TRUE));
        $txtid = "ey_".md5("memberlist_txt_{$t_uniqid}");
        $result['txtid'] = $txtid;
        $result['root_dir'] = $this->root_dir;
        $result['attarray'] = $attarray;
        $result_json = json_encode($result);
        $version = getCmsVersion();
        $srcurl = get_absolute_url("{$this->root_dir}/public/static/common/js/tag_memberlist.js?v={$version}");
        $hidden = <<<EOF
<script language="javascript" type="text/javascript" src="{$srcurl}"></script>
<script type="text/javascript">
    var eyResultJson230614 = {$result_json};
    eyGetMemberlist230614();
</script>
EOF;
        $data = [
            'txtid'     => $txtid,
            'hidden'    => $hidden,
        ];

        // 会员表内置字段
        $usersFields = Db::name("users")->getTableFields();
        foreach ($usersFields as $key => $value) {
            $data[$value] = $value . '_eyoucms_fields';
        }

        // 会员表自定义字段
        $where = [
            'is_hidden' => 0,
        ];
        $usersParams = Db::name("users_parameter")->field('para_id, name')->where($where)->order('para_id asc')->select();
        foreach ($usersParams as $key => $value) {
            $data[$value['name']] = $value['name'] . '_eyoucms_params';
        }

        // dump($data);exit;
        return $data;
    }
}