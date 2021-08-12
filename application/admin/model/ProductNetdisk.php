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

namespace app\admin\model;

use think\Db;
use think\Model;

/**
 *网盘下载
 */
class ProductNetdisk extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 后置操作方法
     * 自定义的一个函数 用于数据保存后做的相应处理操作, 使用时手动调用
     * @param int $aid 产品id
     * @param array $post post数据
     * @param string $opt 操作
     */
    public function saveProductNetdisk($aid, $post)
    {
        $count = Db::name('product_netdisk')->where('aid', $aid)->count();
        $data  = [
            'aid'          => $aid,
            'netdisk_url'  => !empty($post['netdisk_url']) ? $post['netdisk_url'] : '',
            'netdisk_pwd'  => !empty($post['netdisk_pwd']) ? $post['netdisk_pwd'] : '',
            'unzip_pwd'    => !empty($post['unzip_pwd']) ? $post['unzip_pwd'] : '',
            'text_content' => !empty($post['text_content']) ? $post['text_content'] : '',
        ];
        if (!empty($count)) {
            //更新操作
            $data['update_time'] = getTime();
            Db::name('product_netdisk')->where('aid', $aid)->update($data);
        } else {
            //新增操作
            $data['lang'] = get_admin_lang();
            $data['add_time'] = getTime();
            Db::name('product_netdisk')->insert($data);
        }
    }

}