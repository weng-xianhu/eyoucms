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
 * Date: 2019-4-13
 */

namespace think\template\taglib\eyou;

use think\Config;
use think\Db;

/**
 * 地址管理列表
 */
class TagSpaddress extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取地址管理数据
     */
    public function getSpaddress($type = '')
    {
        if ($type == 'add') {
            $UlHtmlId = 'UlHtml';
            // 封装删除收货地址JS
            $AddressData[0]['ShopAddAddr'] = " onclick=\"ShopAddAddress();\" ";
            $AddressData[0]['UlHtmlId']    = " id=\"{$UlHtmlId}\" ";
            // 传入JS参数
            $shop_get_wechat_addr_url = '';
            if (isMobile() && isWeixin()) {
                // 用于获取微信收货地址
                $shop_get_wechat_addr_url  = url('user/Shop/shop_get_wechat_addr');
            }
            $data['UlHtmlId']  = $UlHtmlId;
            $data['shop_get_wechat_addr_url'] = $shop_get_wechat_addr_url;
            $data['shop_add_address']  = url('user/Shop/shop_add_address');
            $data['shop_edit_address'] = url('user/Shop/shop_edit_address');
            $data['shop_del_address']  = url('user/Shop/shop_del_address');
            $data['shop_set_default']  = url('user/Shop/shop_set_default_address');
            if (isWeixin() || isMobile()) {
                $data['addr_width']  = '100%';
                $data['addr_height'] = '100%';
            }else{
                $data['addr_width']  = '350px';
                $data['addr_height'] = '550px';
            }
            $data_json = json_encode($data);
            $version   = getCmsVersion();
            // 循环中第一个数据带上JS代码加载
            $AddressData[0]['hidden'] = <<<EOF
<script type="text/javascript">
    var aeb461fdb660da59b0bf4777fab9eea = {$data_json};
</script>
<script type="text/javascript" src="{$this->root_dir}/public/static/common/js/tag_spaddress.js?v={$version}"></script>
EOF;
            return $AddressData;
            exit;    
        }

        // 查询条件
        $AddressWhere = [
            'users_id' => session('users_id'),
            'lang'     => $this->home_lang,
        ];

        $AddressData = Db::name("shop_address")->where($AddressWhere)->order('is_default desc')->select();
        if (empty($AddressData)) {
            return false;
        }

        // 根据地址ID查询相应的中文名字
        foreach ($AddressData as $key => $value) {
            $AddressData[$key]['DefaultHidden'] = '';
            if (!empty($value['is_default'])) {
                $DefaultAddress = $value['addr_id'];
                $AddressData[$key]['DefaultHidden'] = '<input type="hidden" name="addr_id" id="addr_id" value="'.$value['addr_id'].'">';
            }

            $AddressData[$key]['country']  = '中国';
            $AddressData[$key]['province'] = get_province_name($value['province']);
            $AddressData[$key]['city']     = get_city_name($value['city']);
            $AddressData[$key]['district'] = get_area_name($value['district']);

            // 封装Ul的ID
            $AddressData[$key]['ul_il_id'] = " id=\"{$value['addr_id']}_ul_li\" ";

            // 封装设置默认JS
            $AddressData[$key]['SetDefault'] = " onclick=\"SetDefault(this, '{$value['addr_id']}');\" data-is_default=\"{$value['is_default']}\" id=\"{$value['addr_id']}_color\" ";

            // 封装修改收货地址JS
            $AddressData[$key]['ShopEditAddr'] = " onclick=\"ShopEditAddress('{$value['addr_id']}');\" ";

            // 封装删除收货地址JS
            $AddressData[$key]['ShopDelAddr'] = " onclick=\"ShopDelAddress('{$value['addr_id']}');\" ";

            // 封装收货人ID
            $AddressData[$key]['ConsigneeId'] = " id=\"{$value['addr_id']}_consignee\" ";

            // 封装收货人手机号ID
            $AddressData[$key]['MobileId'] = " id=\"{$value['addr_id']}_mobile\" ";

            // 封装收货地址信息
            $AddressData[$key]['Info'] = $AddressData[$key]['country'].' '.$AddressData[$key]['province'].' '.$AddressData[$key]['city'].' '.$AddressData[$key]['district'];

            // 封装收货地址信息ID
            $AddressData[$key]['InfoId'] = " id=\"{$value['addr_id']}_info\" ";

            // 封装收货地址信息ID
            $AddressData[$key]['AddressId'] = " id=\"{$value['addr_id']}_address\" ";

            // 封装下单页选中JS
            $AddressData[$key]['SelectEd'] = " onclick=\"SelectEd('addr_id','{$value['addr_id']}');\" ";
        }

        // 若没有默认地址，则默认第一条数据为此次订单收货地址
        if (!empty($AddressData) && empty($DefaultAddress)) {
            $AddressData[0]['DefaultHidden'] = '<input type="hidden" name="addr_id" id="addr_id" value="'.$AddressData[0]['addr_id'].'">';
        }

        if (!empty($AddressData)) {
            return $AddressData;
        }else{
            return false;
        }
    }
}