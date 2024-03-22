<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2022/7/20
 * Time: 16:01
 */

namespace think\template\taglib\api;

use think\Db;

class TagDownloadlist extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }
    public function getDownloadlist($aid = 0,$users = []){
        empty($aid) && $aid = $this->aid;
        if (empty($aid)) {
            return '标签downloadpay报错：缺少属性 aid 值。';
        }
        $artData = Db::name('archives')->where('aid', $aid)->find();
        $artData['arc_level_value'] = 0;
        if (0 < $artData['arc_level_id']) {
            $artData['arc_level_value'] = Db::name('users_level')->where(['level_id'=>$artData['arc_level_id']])->value('level_value');
        }
        $canDownload = 0;
        if (empty($artData['restric_type'])) { // 免费
            $canDownload = 1;
        } else if (1 == $artData['restric_type']) { // 付费
            if (!empty($users['users_id'])){
                // 查询是否已购买
                $where = [
                    'order_status' => 1,
                    'product_id' => intval($aid),
                    'users_id' => $users['users_id']
                ];
                $count = Db::name('download_order')->where($where)->count();
                if (!empty($count)){     //已经付费
                    $canDownload = 1;
                }
            }
        }else if (2 == $artData['restric_type']) { // 会员专享
            if ($artData['arc_level_id'] == 0){
                $canDownload = 1;
            }else if (!empty($users['users_id'])) {
                $level_value = Db::name('users_level')->where('level_id',$users['level'])->value('level_value');
                if ($level_value >= $artData['arc_level_value']){
                    $canDownload = 1;
                }else{
                    if (0 == $artData['no_vip_pay']){
                        $buyVip = 1;
                    }else{
                        $where = [
                            'order_status' => 1,
                            'product_id' => intval($aid),
                            'users_id' => $users['users_id']
                        ];
                        $count = Db::name('download_order')->where($where)->count();
                        if (!empty($count)){
                            $canDownload = 1;
                        }
                    }
                }
            }
        }else if (3 == $artData['restric_type']) { // 会员付费
            if (!empty($users['users_id'])) {
                $level_value = Db::name('users_level')->where('level_id',$users['level'])->value('level_value');
                if ($level_value >= $artData['arc_level_value']){
                    // 查询是否已购买
                    $where = [
                        'order_status' => 1,
                        'product_id' => intval($aid),
                        'users_id' => $users['users_id']
                    ];
                    $count = Db::name('download_order')->where($where)->count();
                    if (!empty($count)){
                        $canDownload = 1;
                    }
                }else{
                    $buyVip = 1;
                }
            }
        }
        $result = [];
        if (1 == $canDownload){
            $result = Db::name('DownloadFile')->field("*")
                ->where(['aid'=>$aid])
                ->order('sort_order asc')
                ->select();
            foreach ($result as $key=>$val){
                $n = $key + 1;
                if (is_http_url($val['file_url'])) {
                    $result[$key]['server_name'] = !empty($val['server_name']) ? $val['server_name'] : "远程服务器({$n})";
                } else {
                    $result[$key]['server_name'] = !empty($val['server_name']) ? $val['server_name'] : "本地服务器({$n})";
                }
                $result[$key]['file_url'] = get_absolute_url($val['file_url'],'default',true);
            }
        }
        $redata = [
            'data'=> !empty($result) ? $result : false,
        ];
        return $redata;
    }
}