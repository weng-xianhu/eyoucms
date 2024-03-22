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
 * 多城市站点
 */
class Citysite extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    public function setCitysiteOpen()
    {
        $web_citysite_open = tpCache('web.web_citysite_open');
        $web_citysite_open = intval($web_citysite_open);
        $tfile = DATA_PATH.'conf'.DS.'citysite.txt';
        $fp = @fopen($tfile,'w');
        if(!$fp) {
            @file_put_contents($tfile, $web_citysite_open);
        }
        else {
            fwrite($fp, $web_citysite_open);
            fclose($fp);
        }
    }

    /**
     * 获取单条地区
     * @author wengxianhu by 2017-7-26
     */
    public function getInfo($id, $field = '*')
    {
        $result = Db::name('citysite')->field($field)->find($id);

        return $result;
    }

    /**
     * 获取多个地区
     * @author wengxianhu by 2017-7-26
     */
    public function getListByIds($ids = array(), $field = '*', $index_key = '')
    {
        $args = [$ids, $field, $index_key];
        $cacheKey = 'citysite-'.md5(__CLASS__.__FUNCTION__.json_encode($args));
        $result = cache($cacheKey);
        if (empty($result)) {
            $map = array(
                'id'   => array('IN', $ids),
            );
            $result = Db::name('citysite')->field($field)
                ->where($map)
                ->select();

            if (!empty($index_key)) {
                $result = convert_arr_key($result, $index_key);
            }

            cache($cacheKey, $result, null, "citysite");
        }

        return $result;
    }

    /**
     * 获取子地区
     * @author wengxianhu by 2017-7-26
     */
    public function getList($parent_id = 0, $field = '*', $index_key = '',$level = 0)
    {
        $result = $this->getAll($parent_id, $field, $index_key,$level);

        return $result;
    }

    /**
     * 获取全部地区
     * @author wengxianhu by 2017-7-26
     */
    public function getAll($parent_id = false, $field = '*', $index_key = '',$level = 0)
    {
        $args = [$parent_id, $field, $index_key, $level];
        $cacheKey = 'citysite-'.md5(__CLASS__.__FUNCTION__.json_encode($args));
        $result = cache($cacheKey);
        if (empty($result)) {
            $map['status'] = 1;
            if (false !== $parent_id) {
                $map['parent_id'] = $parent_id;
            }
            if (0 !== $level) {
                $map['level'] = $level;
            }
            $result = Db::name('citysite')->field($field)
                ->where($map)
                ->select();

            if (!empty($index_key)) {
                $result = convert_arr_key($result, $index_key);
            }

            cache($cacheKey, $result, null, "citysite");
        }

        return $result;
    }

    /**
     * 获取级别的地区
     * @author wengxianhu by 2017-7-26
     */
    public function getListByLevel($level = 1, $field = '*', $index_key = '')
    {
        $map = array(
            'level' => $level,
        );

        $result = Db::name('citysite')->field($field)
            ->where($map)
            ->select();

        if (!empty($index_key)) {
            $result = convert_arr_key($result, $index_key);
        }

        return $result;
    }

    /**
     * 获取当前城市站点的所有父级
     * @author wengxianhu by 2018-4-26
     */
    public function getAllPid($id)
    {
        $args = [THEME_STYLE, $id];
        $cacheKey = 'citysite-'.md5(__CLASS__.__FUNCTION__.json_encode($args));
        $data = cache($cacheKey);
        if (empty($data)) {
            $data = array();
            $siteid = $id;
            $map = [
                'status'    => 1,
            ];
            $citysite_list = Db::name('citysite')->field('*, id as siteid')
                ->where($map)
                ->getAllWithIndex('id');
            if (isset($citysite_list[$siteid])) {
                // 第一个先装起来
                $citysite_list[$siteid]['siteurl'] = $this->getSiteUrl($citysite_list[$siteid]);
                $data[$siteid] = $citysite_list[$siteid];
            } else {
                return $data;
            }

            while (true)
            {
                $siteid = $citysite_list[$siteid]['parent_id'];
                if($siteid > 0){
                    if (isset($citysite_list[$siteid])) {
                        $citysite_list[$siteid]['siteurl'] = $this->getSiteUrl($citysite_list[$siteid]);
                        $data[$siteid] = $citysite_list[$siteid];
                    }
                } else {
                    break;
                }
            }
            $data = array_reverse($data, true);

            cache($cacheKey, $data, null, "citysite");
        }

        return $data;
    }

    /**
     * 获取城市站点的URL
     */
    public function getSiteUrl($res)
    {
        $siteurl = siteurl($res);

        return $siteurl;
    }

    /**
     * 当后台使用分站域名访问，在发布文档时自动定位所属区域
     * @return [type] [description]
     */
    public function auto_location_select(&$assign_data = [])
    {
        if (config('city_switch_on')) {
            $subDomain = request()->subDomain();
            if (!empty($subDomain) && 'www' != $subDomain) {
                $siteinfo = Db::name('citysite')->where(['domain'=>$subDomain, 'is_open'=>1])->find();
                if (!empty($siteinfo)) {
                    if (1 == $siteinfo['level']) {
                        $assign_data['site_province_id'] = $siteinfo['id'];
                    } else if (2 == $siteinfo['level']) {
                        $assign_data['site_province_id'] = $siteinfo['topid'];
                        $assign_data['site_city_id'] = $siteinfo['id'];
                    } else if (3 == $siteinfo['level']) {
                        $assign_data['site_province_id'] = $siteinfo['topid'];
                        $assign_data['site_city_id'] = $siteinfo['parent_id'];
                        $assign_data['site_area_id'] = $siteinfo['id'];
                    }
                }
            }
        }
    }
}