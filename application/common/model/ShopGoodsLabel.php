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
 * 商城商品服务标签模型
 */
load_trait('controller/Jump');
class ShopGoodsLabel extends Model
{
    use \traits\controller\Jump;

    // 初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        // 时间戳
        $this->times = getTime();
        // 商品服务标签列表
        $this->shopGoodsLabelDb = Db::name('shop_goods_label');
        // 商城商品服务标签与商品ID关联绑定表
        $this->shopGoodsLabelBindDb = Db::name('shop_goods_label_bind');
    }

    // 获取商品服务标签列表
    public function getGoodsLabelList($aid = 0, $isPort = false, $labelID = 0)
    {
        // 查询商品服务标签与商品关联绑定数据
        $goodsLabelBind = [];
        if (!empty($aid)) {
            $where = [
                'aid' => intval($aid)
            ];
            if (!empty($labelID)) $where['label_id'] = intval($labelID);
            $goodsLabelBind = $this->shopGoodsLabelBindDb->where($where)->select();
            $goodsLabelBind = !empty($goodsLabelBind) ? convert_arr_key($goodsLabelBind, 'label_id') : [];
        }

        // 查询商品标签列表
        $where = [
            'status' => 1,
        ];
        if (!empty($labelID)) $where['label_id'] = intval($labelID);
        $goodsLabel = $this->shopGoodsLabelDb->where($where)->select();
        foreach ($goodsLabel as $key => $value) {
            // 图片处理
            if (!empty($isPort)) {
                $value['label_pic'] = handle_subdir_pic($value['label_pic'], 'img', true);
            } else {
                $value['label_pic'] = handle_subdir_pic($value['label_pic']);
            }

            // 是否选中
            $value['checked'] = '';
            if (!empty($goodsLabelBind[$value['label_id']])) $value['checked'] = 'checked';

            // 覆盖原数据
            $goodsLabel[$key] = $value;

            // 如果是接口访问则去掉未选中的服务标签
            if (!empty($isPort) && empty($value['checked'])) unset($goodsLabel[$key]);
        }
        return $goodsLabel;
    }

    // 保存商品服务标签与商品关联绑定数据
    public function saveGoodsLabelBind($aid = 0, $goodsLabelID = [])
    {
        // 删除指定商品服务标签与商品关联绑定数据
        if (!empty($aid)) $this->shopGoodsLabelBindDb->where(['aid' => intval($aid)])->delete(true);

        // 保存指定商品服务标签与商品关联绑定数据
        if (!empty($goodsLabelID)) {
            $insertAll = [];
            foreach ($goodsLabelID as $key => $value) {
                $insertAll[] = [
                    'aid' => intval($aid),
                    'label_id' => intval($value),
                    'add_time' => $this->times,
                    'update_time' => $this->times,
                ];
            }
            if (!empty($insertAll)) $this->shopGoodsLabelBindDb->insertAll($insertAll);
        }
    }

}