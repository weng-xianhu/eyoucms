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
 * Date: 2019-1-7
 */

namespace app\common\model;

use think\Db;

/**
 * 商城售后处理模型
 */
load_trait('controller/Jump');
class ShopServiceHandle
{
    use \traits\controller\Jump;

    // 构造函数
    public function __construct()
    {
        // 统一接收参数处理
        $this->times = getTime();
    }

    public function addServiceDataHandle($data = [])
    {
        // 售后类型
        if (1 === intval($data['service_type'])) {
            $data['refund_code'] = 'HH';
            $data['service_type_text'] = '换货';
        }
        else if (2 === intval($data['service_type'])) {
            $data['refund_code'] = 'TK';
            $data['service_type_text'] = '退货退款';
        }
        else if (3 === intval($data['service_type'])) {
            $data['refund_code'] = 'JTK';
            $data['service_type_text'] = '仅退款';
        }
        else {
            $this->error('申请类型异常，重新申请');
        }
        $data['refund_code'] .= $this->times . rand(10, 99);

        // 申请退款原由
        $refundReason = input('param.refund_reason', '');
        if (!empty($refundReason)) $data['refund_reason'] = htmlspecialchars($refundReason);
        
        // 返回处理数据
        return $data;
    }
}