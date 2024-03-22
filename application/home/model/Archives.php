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
    public function getViewInfo($aid, $litpic_remote = false, $where = [])
    {
        $result = array();
        $row = Db::name('archives')->field('*')->where($where)->find($aid);
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
                $extFields = Db::name('article_content')->getTableFields();
                $extFields = array_flip($extFields);
                unset($extFields['id']);
                $rowExt = $articleModel->getInfo($aid);
                $rowExt = array_diff_key($extFields, $row);
            } else if (2 == $row['channel']) { // 产品模型
                /*产品参数*/
                $productAttrModel = new \app\home\model\ProductAttr();
                $attr_list = $productAttrModel->getProAttr($aid);
                $row['attr_list'] = !empty($attr_list[$aid]) ? $attr_list[$aid] : [];
                // 产品相册
                $image_list = [];
                $productImgModel = new \app\home\model\ProductImg();
                $image_list_tmp = $productImgModel->getProImg($aid);
                if (!empty($image_list_tmp[$aid])) {
                    foreach ($image_list_tmp[$aid] as $key => $val) {
                        $val['image_url'] = get_default_pic($val['image_url'], $litpic_remote);
                        isset($val['intro']) && $val['intro'] = htmlspecialchars_decode($val['intro']);
                        $image_list[$key] = $val;
                    }
                }
                $row['image_list'] = $image_list;

                $productModel = new \app\home\model\Product();
                $extFields = Db::name('product_content')->getTableFields();
                $extFields = array_flip($extFields);
                unset($extFields['id']);
                $rowExt = $productModel->getInfo($aid);
                $rowExt = array_diff_key($extFields, $row);
            } else if (3 == $row['channel']) { // 图集模型
                // 图集相册
                $image_list = [];
                $imagesUploadModel = new \app\home\model\ImagesUpload();
                $image_list_tmp = $imagesUploadModel->getImgUpload($aid);
                if (!empty($image_list_tmp[$aid])) {
                    foreach ($image_list_tmp[$aid] as $key => $val) {
                        $val['image_url'] = get_default_pic($val['image_url'], $litpic_remote);
                        isset($val['intro']) && $val['intro'] = htmlspecialchars_decode($val['intro']);
                        $image_list[$key] = $val;
                    }
                }
                $row['image_list'] = $image_list;

                $imagesModel = new \app\home\model\Images();
                $extFields = Db::name('images_content')->getTableFields();
                $extFields = array_flip($extFields);
                unset($extFields['id']);
                $rowExt = $imagesModel->getInfo($aid);
                $rowExt = array_diff_key($extFields, $row);
            } else if (4 == $row['channel']) { // 下载模型
                $downloadModel = new \app\home\model\Download();
                $extFields = Db::name('download_content')->getTableFields();
                $extFields = array_flip($extFields);
                unset($extFields['id']);
                $rowExt = $downloadModel->getInfo($aid);
                $rowExt = array_diff_key($extFields, $row);
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
        $row = $this->readContentFirst($typeid);
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

    /**
     * 读取指定栏目ID下有内容的栏目信息，只读取每一级的第一个栏目
     * @param intval $typeid 栏目ID
     * @return array
     */
    public function readContentFirst($typeid)
    {
        $result = false;
        while (true)
        {
            $singleModel = new \app\home\model\Single;
            $result = $singleModel->getInfoByTypeid($typeid);
            if (!empty($result['empty_logic']) && empty($result['content']) && preg_match('/^lists_single(_(.*))?\.htm$/i', $result['templist'])) {
                $map = array(
                    'parent_id'       => $result['typeid'],
                    'current_channel' => 6,
                    'is_hidden'       => 0,
                    'status'          => 1,
                    'is_del'          => 0,
                );
                $row = \think\Db::name('arctype')->where($map)->field('*')->order('sort_order asc')->find(); // 查找下一级的单页模型栏目
                if (empty($row)) { // 不存在并返回当前栏目信息
                    break;
                } elseif (6 == $row['current_channel']) { // 存在且是单页模型，则进行继续往下查找，直到有内容为止
                    $typeid = $row['id'];
                }
            } else {
                break;
            }
        }

        return $result;
    }
}