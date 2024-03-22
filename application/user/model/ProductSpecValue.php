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
 * Date: 2019-7-9
 */
namespace app\user\model;

use think\Model;
use think\Config;
use think\Db;

/**
 * 产品规格值ID，价格，库存表
 */
class ProductSpecValue extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 还原普通订单库存
     * @param null $order_id
     * @param null $users_id
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function SaveProducSpecValueStock($order_id = null, $users_id = null)
    {
        if (empty($order_id)) return false;

        $ProductSpecValue = [];
        $Where = [
            'users_id' => $users_id,
            'lang'     => get_home_lang(),
        ];
        if (is_array($order_id)) {
            $order_id = get_arr_column($order_id, 'order_id');
            $Where['order_id'] = ['IN',$order_id];
        }else{
            $Where['order_id'] = $order_id;
        }

        $OrderDetails = Db::name('shop_order_details')->where($Where)->field('product_id,num,data')->select();
        if (!empty($OrderDetails)) {
            $ProductIdWhere = $SpecValueIdWhere = $Array_new = $ArcUpData =[];
            foreach ($OrderDetails as $key => $value) {
                $value['data'] = unserialize($value['data']);
                if (!empty($value['data']['spec_value_id'])){
                    //多规格
                    $spec_value_id = htmlspecialchars_decode($value['data']['spec_value_id']);
                    array_push($SpecValueIdWhere, $spec_value_id);
                    array_push($ProductIdWhere, $value['product_id']);

                    $Array_new[] = [
                        'spec_value_id' => $spec_value_id,
                        'spec_stock'    => $value['num'],
                    ];
                }else{
                    //单规格
                    $ArcUpData[] = [
                        'aid'         => $value['product_id'],
                        'stock_count' => Db::raw('stock_count+' . ($value['num'])),
                        'sales_num'   => Db::raw('sales_num-' . ($value['num'])),
                        'sales_all'   => Db::raw('sales_all-' . ($value['num'])),
                    ];
                }
            }

            $ValueWhere = [
                'aid'  => ['IN',$ProductIdWhere],
                'lang' => get_home_lang(),
                'spec_value_id' => ['IN',$SpecValueIdWhere],
            ];
            $ProductSpecValue = Db::name('product_spec_value')->where($ValueWhere)->field('value_id,spec_value_id')->select();

            foreach ($ProductSpecValue as $key => $value) {
                foreach ($Array_new as $kk => $vv) {
                    if ($value['spec_value_id'] == $vv['spec_value_id']) {
                        $ProductSpecValue[$key]['spec_stock'] = Db::raw('spec_stock+'.($vv['spec_stock']));
                        $ProductSpecValue[$key]['spec_sales_num'] = Db::raw('spec_sales_num-'.($vv['spec_stock']));
                    }
                }
            }
            
            $this->saveAll($ProductSpecValue);

            //还原规格库存
            if (!empty($ArcUpData)){
                $ArchivesModel = new \app\user\model\Archives();
                $ArchivesModel->saveAll($ArcUpData);
                Db::name('archives')->where(['sales_num'=>['lt',0]])->update(['sales_num'=>0, 'update_time'=>getTime()]);
                Db::name('archives')->where(['sales_all'=>['lt',0]])->update(['sales_all'=>0, 'update_time'=>getTime()]);
            }
        }
    }

    /**
     * 还原秒杀订单库存
     * 2020/12/18  大黄
     */
    public function SaveSharpStock($order_id = null, $users_id = null,$order_info = [])
    {
        if (empty($order_id)) return false;

        $ProductSpecValue = [];
        $Where = [
            'users_id' => $users_id,
            'lang'     => get_home_lang(),
        ];
        if (is_array($order_id)) {
            $order_id = get_arr_column($order_id, 'order_id');
            $Where['order_id'] = ['IN',$order_id];
        }else{
            $Where['order_id'] = $order_id;
        }

        $OrderDetails = Db::name('shop_order_details')->where($Where)->field('product_id,num,data')->select();
        if (!empty($OrderDetails)) {

            $ProductIdWhere = $SpecValueIdWhere = $Array_new =[];
            foreach ($OrderDetails as $key => $value) {
                //先查询商品是多规格还是单规格
                $is_sku = Db::name('sharp_goods')->where('aid',$value['product_id'])->value('is_sku');
                if (empty($is_sku)){
                    //单规格直接还原sharp_goods数量
                    $SharpGoodsupdate['seckill_stock'] =  Db::raw('seckill_stock+'.($value['num']));
                    $SharpGoodsupdate['sales'] =  Db::raw('sales-'.($value['num']));
                    Db::name('sharp_goods')->where('aid',$value['aid'])->update($SharpGoodsupdate);
                    return true;
                }
                $value['data'] = unserialize($value['data']);
                $spec_value_id = htmlspecialchars_decode($value['data']['spec_value_id']);
                array_push($SpecValueIdWhere, $spec_value_id);
                array_push($ProductIdWhere, $value['product_id']);

                $Array_new[] = [
                    'spec_value_id' => $spec_value_id,
                    'seckill_stock'    => $value['num'],
                ];
            }

            $ValueWhere = [
                'aid'  => ['IN',$ProductIdWhere],
                'lang' => get_home_lang(),
                'spec_value_id' => ['IN',$SpecValueIdWhere],
            ];
            $ProductSpecValue = Db::name('product_spec_value')->where($ValueWhere)->field('value_id,spec_value_id,aid')->select();

            foreach ($ProductSpecValue as $key => $value) {
                foreach ($Array_new as $kk => $vv) {
                    if ($value['spec_value_id'] == $vv['spec_value_id']) {
                        $ProductSpecValue[$key]['seckill_stock'] = Db::raw('seckill_stock+'.($vv['seckill_stock']));
                        $ProductSpecValue[$key]['seckill_sales_num'] = Db::raw('seckill_sales_num-'.($vv['seckill_stock']));
                        //还原sharp_goods数量
                        $SharpGoodsupdate['seckill_stock'] =  Db::raw('seckill_stock+'.($vv['seckill_stock']));
                        $SharpGoodsupdate['sales'] =  Db::raw('sales-'.($vv['seckill_stock']));
                        Db::name('sharp_goods')->where('aid',$value['aid'])->update($SharpGoodsupdate);
                    }
                }
            }

            $this->saveAll($ProductSpecValue);
        }
    }
}