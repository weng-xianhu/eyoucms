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
use think\Page;
use think\Db;
use app\home\logic\FieldLogic;

/**
 * 文档主表
 */
class Archives extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        $this->fieldLogic = new FieldLogic();
    }

    /**
     * 获取单条文档记录
     * @author wengxianhu by 2017-7-26
     */
    public function getViewInfo($aid, $litpic_remote = false)
    {
        $result = array();
        $row = db('archives')->field('*')->find($aid);
        if (!empty($row)) {
            /*封面图*/
            if (empty($row['litpic'])) {
                $row['is_litpic'] = 0; // 无封面图
            } else {
                $row['is_litpic'] = 1; // 有封面图
            }
            $row['litpic'] = get_default_pic($row['litpic'], $litpic_remote); // 默认封面图

            /*文档基本信息*/
            if (1 == $row['channel']) { // 文章模型
                $articleModel = new \app\home\model\Article();
                $rowExt = $articleModel->getInfo($aid);
            } else if (2 == $row['channel']) { // 产品模型
                $productModel = new \app\home\model\Product();
                $rowExt = $productModel->getInfo($aid);
                /*产品参数*/
                $productAttrModel = new \app\home\model\ProductAttr();
                $attr_list = $productAttrModel->getProAttr($aid);
                $row['attr_list'] = $attr_list;
                // 产品相册
                $productImgModel = new \app\home\model\ProductImg();
                $image_list = $productImgModel->getProImg($aid);
                foreach ($image_list as $key => $val) {
                    $val['image_url'] = get_default_pic($val['image_url'], $litpic_remote);
                    $image_list[$key] = $val;
                }
                $row['image_list'] = $image_list;
            } else if (3 == $row['channel']) { // 图集模型
                $imagesModel = new \app\home\model\Images();
                $rowExt = $imagesModel->getInfo($aid);
                // 图集相册
                $imagesUploadModel = new \app\home\model\ImagesUpload();
                $image_list = $imagesUploadModel->getImgUpload($aid);
                foreach ($image_list as $key => $val) {
                    $val['image_url'] = get_default_pic($val['image_url'], $litpic_remote);
                    $image_list[$key] = $val;
                }
                $row['image_list'] = $image_list;
            } else if (4 == $row['channel']) { // 下载模型
                $downloadModel = new \app\home\model\Download();
                $rowExt = $downloadModel->getInfo($aid);
            }
            $rowExt = $this->fieldLogic->getChannelFieldList($rowExt, $row['channel']); // 自定义字段的数据格式处理
            /*--end*/

            $result = array_merge($rowExt, $row);
        }

        return $result;
    }

    /**
     * 获取单页栏目记录
     * @author wengxianhu by 2017-7-26
     */
    public function getSingleInfo($typeid, $litpic_remote = false)
    {
        $result = array();
        /*文档基本信息*/
        $singleModel = new \app\home\model\Single();
        $row = $singleModel->getInfoByTypeid($typeid);
        /*--end*/
        if (!empty($row)) {
            /*封面图*/
            if (empty($row['litpic'])) {
                $row['is_litpic'] = 0; // 无封面图
            } else {
                $row['is_litpic'] = 1; // 有封面图
            }
            $row['litpic'] = get_default_pic($row['litpic'], $litpic_remote); // 默认封面图
            /*--end*/

            $row = $this->fieldLogic->getTableFieldList($row, config('global.arctype_channel_id')); // 自定义字段的数据格式处理
            /*--end*/
            $row = $this->fieldLogic->getChannelFieldList($row, $row['channel']); // 自定义字段的数据格式处理

            $result = $row;
        }

        return $result;
    }
}