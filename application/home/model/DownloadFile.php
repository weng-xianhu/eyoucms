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

namespace app\home\model;

use think\Model;

/**
 * 下载文件
 */
class DownloadFile extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 获取单条下载文章的所有文件
     * @author 小虎哥 by 2018-4-3
     */
    public function getDownFile($aid, $field = '*')
    {
        $result = db('DownloadFile')->field($field)
            ->where('aid', $aid)
            ->order('sort_order asc')
            ->select();

        if (!empty($result)) {
            foreach ($result as $key => $val) {
                $downurl = url('home/View/downfile', array('id'=>$val['file_id'], 'uhash'=>$val['uhash']));
                $result[$key]['downurl'] = $downurl;
            }
        }

        return $result;
    }
}