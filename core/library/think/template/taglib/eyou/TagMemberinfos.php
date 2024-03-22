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
 * 会员信息
 */
class TagMemberinfos extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取会员信息
     * @author wengxianhu by 2018-4-20
     */
    public function getMemberinfos($aid = '', $users_id = '', $addfields = '')
    {
        $aid = !empty($aid) ? $aid : $this->aid;
        if (empty($aid) && empty($users_id)) {
//            echo '标签memberinfos报错：缺少属性 mid 值。';
            return false;
        } else if (!empty($aid) && empty($users_id)) {
            $archivesInfo = Db::name('archives')->field('users_id,admin_id')->where(['aid'=>$aid])->find();
            $users_id = $archivesInfo['users_id'];
            if (empty($users_id) && empty($archivesInfo['admin_id'])) {
                return false;
            } else if (empty($users_id) && !empty($archivesInfo['admin_id'])) {
                $users_id = Db::name('admin')->where(['admin_id'=>$archivesInfo['admin_id']])->getField('syn_users_id');
                if (empty($users_id)) {
                    return false;
                }
            }
        }

        if (empty($addfields)) {
            $result = Db::name('users')->field('a.*')
                ->alias('a')
                ->where(['a.users_id' => $users_id])
                ->find();
        } else {
            $addfields = str_replace('，', ',', $addfields);
            $addfields = trim($addfields, ',');
            $addfields_arr = explode(',', $addfields);
            if (false !== array_search('level_name', $addfields_arr) || false !== array_search('level_value', $addfields_arr)) {
                $result = Db::name('users')->field('a.*,b.level_name,b.level_value')
                    ->alias('a')
                    ->join('__USERS_LEVEL__ b', 'a.level=b.level_id', 'LEFT')
                    ->where(['a.users_id' => $users_id])
                    ->find();
            } else {
                $result = Db::name('users')->field('a.*')
                    ->alias('a')
                    ->where(['a.users_id' => $users_id])
                    ->find();
            }
        }

        if (empty($result)) {
            return false;
        } else {
            unset($result['password']);
            unset($result['paypwd']);
            $result['reg_time'] = Mydate('Y-m-d h:i:s', $result['reg_time']);
            $result['last_login'] = Mydate('Y-m-d h:i:s', $result['last_login']);
            $result['open_level_time'] = Mydate('Y-m-d h:i:s', $result['open_level_time']);
            $result['update_time'] = Mydate('Y-m-d h:i:s', $result['update_time']);
            $result['head_pic'] = get_head_pic($result['head_pic'], false, $result['sex']);

            /*组装会员属性字段值*/
            if (preg_match('#,para_(\d|\*){1,},#i', ','.$addfields.',')) {
                $users_lists = [];
                $row = Db::name('users_list')->field('a.para_id, a.info')
                    ->alias('a')
                    ->where(['a.users_id' => $users_id])
                    ->cache(true,EYOUCMS_CACHE_TIME,"users_list")
                    ->select();

                $para_list = [];
                if (preg_match('#,para_\*,#i', ','.$addfields.',')) {
                    $users_parameter_list = Db::name('users_parameter')->where([
                            'name'  => ['LIKE', 'para_%'],
                        ])->column('name');
                } else {
                    $users_parameter_list = explode(',', $addfields);
                }
                foreach ($users_parameter_list as $key => $val) {
                    if (preg_match('#para_(\d|\*){1,}#i', $val)) {
                        $para_list[$val] = '';
                    }
                }
                foreach ($row as $key => $val) {
                    $users_lists['para_'.$val['para_id']] = filter_line_return($val['info'], '<br/>');
                }
                $result = array_merge($result, $para_list, $users_lists);
            }
            /*end*/

            $result['hidden'] = '';

            return $result;
        }

    }
}