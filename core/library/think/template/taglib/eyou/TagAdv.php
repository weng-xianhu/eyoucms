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

namespace think\template\taglib\eyou;

use think\Db;

/**
 * 广告
 */
class TagAdv extends Base
{
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取广告
     * @author wengxianhu by 2018-4-20
     */
    public function getAdv($pid = '', $where = '', $orderby = '')
    {
        if (empty($pid)) {
            echo '标签adv报错：缺少属性 pid 。';
            return false;
        }

        /*多语言*/
        $pid = model('LanguageAttr')->getBindValue($pid, 'ad_position');
        if (empty($pid)) {
            echo '标签adv报错：找不到与第一套【'.self::$main_lang.'】语言关联绑定的属性 pid 值。';
            return false;
        } else {
            if (self::$language_split) {
                $this->lang = Db::name('ad_position')->where(['id'=>$pid])->cache(true, EYOUCMS_CACHE_TIME, 'ad')->value('lang');
                if ($this->lang != self::$home_lang) {
                    $lang_title = Db::name('language_mark')->where(['mark'=>self::$home_lang])->value('cn_title');
                    echo "标签adv报错：【{$lang_title}】语言 pid 值不存在。";
                    return false;
                }
            }
        }
        /*--end*/

        $uiset = I('param.uiset/s', 'off');
        $uiset = trim($uiset, '/');

        $args = [$pid, $where, $orderby, $uiset];
        $cacheKey = 'taglib-'.md5(__CLASS__.__FUNCTION__.json_encode($args));
        $result = cache($cacheKey);
        if (empty($result) || 'rand' == $orderby) {
            if (empty($where)) { // 新逻辑
                // 排序
                switch ($orderby) {
                    case 'hot':
                    case 'click':
                        $orderby = 'b.click desc';
                        break;

                    case 'now':
                    case 'new': // 兼容写法
                        $orderby = 'b.add_time desc';
                        break;
                        
                    case 'id':
                        $orderby = 'b.id desc';
                        break;

                    case 'sort_order':
                        $orderby = 'b.sort_order asc';
                        break;

                    case 'rand':
                        $orderby = 'rand()';
                        break;
                    
                    default:
                        if (empty($orderby)) {
                            $orderby = 'b.sort_order asc, b.id desc';
                        }
                        break;
                }
                $where = [
                    'a.id' => $pid,
                    'a.status'  => 1,
                ];
                $result = M("ad_position")->alias('a')
                    ->field("b.*")
                    ->join('__AD__ b', 'a.id = b.pid AND b.status = 1', 'LEFT')
                    ->where($where)
                    ->orderRaw($orderby)
                    ->cache(true,EYOUCMS_CACHE_TIME,"ad") // 如果查询条件有时间字段，一定要去掉这行，避免产生一堆缓存文件
                    ->select();
            } else {
                $adpRow = M("ad_position")->where(['id'=>$pid, 'status'=>1])->count();
                if (empty($adpRow)) {
                    return false;
                }
                // 排序
                switch ($orderby) {
                    case 'hot':
                    case 'click':
                        $orderby = 'click desc';
                        break;

                    case 'now':
                    case 'new': // 兼容写法
                        $orderby = 'add_time desc';
                        break;
                        
                    case 'id':
                        $orderby = 'id desc';
                        break;

                    case 'sort_order':
                        $orderby = 'sort_order asc';
                        break;

                    case 'rand':
                        $orderby = 'rand()';
                        break;
                    
                    default:
                        if (empty($orderby)) {
                            $orderby = 'sort_order asc, id desc';
                        }
                        break;
                }
                $result = M("ad")->field("*")
                    ->where($where)
                    ->orderRaw($orderby)
                    ->cache(true,EYOUCMS_CACHE_TIME,"ad")
                    ->select();
            }
            
            foreach ($result as $key => $val) {
                if (1 == $val['media_type']) {
                    $val['litpic'] = handle_subdir_pic(get_default_pic($val['litpic'])); // 默认无图封面
                } else if (2 == $val['media_type']) {
                    $val['litpic'] = handle_subdir_pic($val['litpic'], 'media');
                }
                $val['target'] = ($val['target'] == 1) ? 'target="_blank"' : 'target="_self"';
                $val['intro'] = htmlspecialchars_decode($val['intro']);
                /*支持子目录*/
                $val['intro'] = handle_subdir_pic($val['intro'], 'html');
                /*--end*/
                if ($uiset == 'on') {
                    $val['links'] = "javascript:void(0);";
                }
                $result[$key] = $val;
            }

            cache($cacheKey, $result, null, 'ad');
        }

        return $result;
    }
}