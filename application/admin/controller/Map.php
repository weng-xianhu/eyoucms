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

namespace app\admin\controller;

class Map extends Base
{
    public function getLocationByAddress()
    {
        $address =  input('param.address/s');
        $ak      = base64_decode(config('global.baidu_map_ak'));
        $url = $this->request->scheme()."://api.map.baidu.com/geocoder/v2/?address={$address}&city=&output=json&ak={$ak}";
        $result = httpRequest($url);
        $result = json_decode($result, true);
        if (!empty($result) && $result['status'] == 0) {
            $data['lng'] = $result['result']['location']['lng']; // 经度
            $data['lat'] = $result['result']['location']['lat']; // 纬度
            $data['map'] = $data['lng'].','.$data['lat'];
            $this->success('请求成功', null, $data);
        }

        $this->error('请求失败，无法继续~');
    }

    public function get_coordinate()
    {
        $coordinate = input('param.coordinate/s');
        $keyword =  input('param.keyword/s');
        $func =  input('param.func/s', 'undefined');
        $lng = 0;
        $lat = 0;
        if($coordinate && strpos($coordinate,',') !== false)
        {
            $map = explode(',',$coordinate);
            $lng = $map[0];
            $lat = isset($map[1]) ? $map[1] : 0;
        }
        $this->assign('lng',$lng);
        $this->assign('lat',$lat);
        $this->assign('ak', base64_decode(config('global.baidu_map_ak')));
        $this->assign('keyword',$keyword);
        $this->assign('func',$func);
        return $this->fetch();
    }
}