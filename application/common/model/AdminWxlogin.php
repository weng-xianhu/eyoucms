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

namespace app\common\model;

use think\Db;
use think\Model;

/**
 * 后台微信登录
 */
class AdminWxlogin extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    public function save_data($admin_id = 0, $type = 0, $data =[])
    {
        $row = Db::name('admin_wxlogin')->where(['admin_id'=>$admin_id, 'type'=>$type])->find();
        if (!empty($row)) {
            $saveData = [
                'admin_id'   => $admin_id,
                'nickname'   => '',
                'headimgurl' => '',
                'openid'    => $data['openid'],
                'unionid'    => empty($data['unionid']) ? '' : $data['unionid'],
                'update_time'=> getTime(),
            ];
            $r = Db::name('admin_wxlogin')->where([
                    'wx_id' => $row['wx_id'],
                ])->update($saveData);
        } else {
            $saveData = [
                'admin_id'  => $admin_id,
                'nickname'   => '',
                'headimgurl' => '',
                'type'  => $type,
                'openid'    => $data['openid'],
                'unionid'    => empty($data['unionid']) ? '' : $data['unionid'],
                'add_time'=> getTime(),
                'update_time'=> getTime(),
            ];
            $r = Db::name('admin_wxlogin')->insert($saveData);
        }

        return ($r !== false) ? true : false;
    }
}