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
 * Date: 2021-7-18
 */
namespace app\common\model;

use think\Db;
use think\Model;

/**
 * 外贸助手语言包变量
 */
class ForeignPack extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    public function getPackValue($name = '', $lang = 'cn')
    {
        static $foreignData = null;
        if (null === $foreignData) {
            $foreignData = tpSetting('foreign', [], 'cn');
        }
        if (!empty($foreignData['foreign_is_status'])) {
            $lang = 'en';
        } else {
            $lang = 'cn';
        }

        $cacheKey = md5('common_ForeignPack_getForeignPack_list');
        $result = cache($cacheKey);
        if (empty($result)) {
            $result = [];
            $list = Db::name('foreign_pack')->where(['id'=>['gt',0]])->select();
            foreach ($list as $key => $val) {
                $index_key = md5($val['name'].'_'.$val['lang']);
                $result[$index_key] = $val;
            }
            cache($cacheKey, $result, null, 'foreign_pack');
        }

        if (empty($name)) {
            $data = $result;
        } else {
            $index_key = md5($name.'_'.$lang);
            $data = empty($result[$index_key]) ? '' : $result[$index_key]['value'];
        }

        return $data;
    }
}