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

        return $result;
    }

    /**
     * 删除单条下载文章的所有文件
     * @author 小虎哥 by 2018-4-3
     */
    public function delDownFile($aid = array())
    {
        if (!is_array($aid)) {
            $aid = array($aid);
        }
        $result = db('DownloadFile')->where(array('aid'=>array('IN', $aid)))->delete();

        return $result;
    }



    /**
     * 保存下载文章的文件
     * @author 小虎哥 by 2018-4-3
     */
    public function savefile($aid, $post = array())
    {
        $fileupload = isset($post['fileupload']) ? $post['fileupload'] : array();
        if (!empty($fileupload)) {

            // 删除
            $this->delDownFile($aid);

             // 添加文件
            $data = array();
            $sort_order = 0;
            foreach($fileupload as $key => $val)
            {
                if($val == null || empty($val))  continue;    

                $title = !empty($post['title']) ? $post['title'] : '';
                $file_size = isset($post['fileSize'][$key]) ? $post['fileSize'][$key] : 0;
                $file_mime = isset($post['fileMime'][$key]) ? $post['fileMime'][$key] : '';
                $uhash = isset($post['uhash'][$key]) ? $post['uhash'][$key] : '';
                $md5file = isset($post['md5file'][$key]) ? $post['md5file'][$key] : '';
                $file_ext = pathinfo($val, PATHINFO_EXTENSION);
                $file_name = pathinfo($val, PATHINFO_BASENAME);
                ++$sort_order;
                $data[] = array(
                    'aid' => $aid,
                    'title' => $title,
                    'file_url'   => $val,
                    'file_size'  => $file_size,
                    'file_ext'  => $file_ext,
                    'file_name'  => $file_name,
                    'file_mime'  => $file_mime,
                    'uhash'  => $uhash,
                    'md5file'  => $md5file,
                    'sort_order'    => $sort_order,
                    'add_time' => getTime(),
                );
            }
            if (!empty($data)) {
                M('DownloadFile')->insertAll($data);
            }
        }
    }
}