<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海口快推科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */

namespace think\template\taglib\eyou;

use think\Db;
use think\Request;

/**
 * 视频列表
 */
class TagVideolist extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取每篇文章的属性
     * @author wengxianhu by 2018-4-20
     */
    public function getVideolist($aid = '', $autoplay = '', $player = '')
    {
        $aid = !empty($aid) ? $aid : $this->aid;
        if (empty($aid)) {
            echo '标签videolist报错：缺少属性 aid 值。';
            return false;
        }

        //当前文章的视频列表
        $row = Db::name('media_file')
            ->where(['aid' => $aid])
            ->order('sort_order asc, file_id asc')
            ->cache(true,EYOUCMS_CACHE_TIME,"media_file")
            ->select();
        /*--end*/

        if (empty($row)) {
            return false;
        } else {
            $result = [];
            foreach ($row as $key => $val) {
                if (!empty($val['file_url'])) {
                    if (!is_http_url($val['file_url'])) {
                        $row[$key]['file_url'] = handle_subdir_pic($val['file_url'], 'media', true);
                    }
                    
                    $row[$key]['file_time']  = gmSecondFormat(intval($val['file_time']), ':');
                    $row[$key]['onclick'] = " onclick=\"changeVideoUrl341922('{$row[$key]['file_id']}', '{$row[$key]['aid']}', '{$row[$key]['uhash']}', '{$player}')\" ";
                    //列表页
                    $row[$key]['hidden'] = '';
                    if ($key == count($row) - 1) {
                        $data = [
                            'root_dir'      => $this->root_dir,
                            'aid'           => $aid,
                            'player'        => $player,
                            'autoplay'      => $autoplay,
                            'file_id_0'     => $row[0]['file_id'],
                            'uhash_0'       => $row[0]['uhash'],
                        ];
                        $data_json = json_encode($data);
                        $version = getCmsVersion();
                        $srcurl = get_absolute_url("{$this->root_dir}/public/static/common/js/tag_videolist.js?v={$version}");
                        $row[$key]['hidden'] = <<<EOF
<script type="text/javascript">
var vars1612143009 = {$data_json};
</script>
<script language="javascript" type="text/javascript" src="{$srcurl}"></script>
EOF;
                    }
                    $result[] = $row[$key];
                } else {
                    unset($row[$key]);
                }
            }
            return $result;
        }
    }
}