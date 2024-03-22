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

use app\home\logic\FieldLogic;

/**
 * 相关文章列表
 */
class TagLikearticle extends Base
{
    public $fieldLogic;
    public $archives_db;
    public $version = 'v1.0.0';

    //初始化
    protected function _initialize()
    {
        parent::_initialize();
        $this->fieldLogic  = new FieldLogic();
        $this->archives_db = Db::name('archives');
        $this->version = getCmsVersion();
    }

    /**
     *  likearticle解析函数
     *
     * @author wengxianhu by 2018-4-20
     * @access    public
     * @param     array $param 查询数据条件集合
     * @param     int $row 调用行数
     * @param     string $tagid 标签id
     * @return    array
     */
    public function getLikearticle($channelid = '', $typeid = '', $limit = 12, $byabs = 0, $thumb = '')
    {
        $result = false;

        /*相关文档标签*/
        $LikearticleRow = [];
        if (is_dir('./weapp/Likearticle/')) {
            $LikearticleRow = model('Weapp')->getWeappList('Likearticle');
            if (!empty($LikearticleRow) && 1 != intval($LikearticleRow['status'])) {
                return false;
            }
        } else {
            return false;
        }
        /*end*/

        $channelid = str_replace('，', ',', $channelid);

        $typeid      = !empty($typeid) ? $typeid : '';
        $typeidArr  = [];
        if (!empty($typeid)) {
            if (!preg_match('/^([\d\,]*)$/i', $typeid)) {
                echo '标签likearticle报错：typeid属性值语法错误，请正确填写栏目ID。';
                return false;
            }
            if( !preg_match('#,#', $typeid) ) {
                $typeid = model('Arctype')->getHasChildren($typeid);
                $typeid = get_arr_column($typeid, 'id');
            }
            if (!is_array($typeid)) {
                $typeid = explode(',', $typeid);
            }
            $typeidArr = $typeid;
        }

        $keywords = [];
        $tags_arr = [];

        //tag标签
        if (3 > count($keywords)) {
            $where_taglist = [];
            if (!empty($typeidArr)) {
                $where_taglist['typeid'] = ['IN', $typeidArr];
                if (!empty($this->aid)) {
                    $tids = Db::name('taglist')->where(['aid'=>$this->aid])->column('tid');
                    $where_taglist['tid'] = ['IN', $tids];
                }
            } else {
                !empty($this->aid) && $where_taglist['aid'] = $this->aid;
            }
            $tag                  = Db::name('taglist')->field('tag')->where($where_taglist)->select();
            if (!empty($tag)) {
                foreach ($tag as $key => $value) {
                    $keywords[] = $value['tag'];
                    $tags_arr[] = $value['tag'];
                }
            }
        }

        //seo关键词
        if (3 > count($keywords)) {
            $seo_keywords = $this->archives_db->where('aid', $this->aid)->getField('seo_keywords');
            if (!empty($seo_keywords)) {
                //先根据逗号分割成数组
                $seo_key_arr = explode(',', $seo_keywords);
                foreach ($seo_key_arr as $key => $value) {
                    $keywords[] = $value;
                }
            }
        }

        $where_keyword = '';

        //如果关键词不为空,进行查询
        if (!empty($keywords)) {
            $n = 1;
            foreach ($keywords as $k) {

                if ($n > 3) break;

                if (trim($k) == '') {
                    continue;
                }
                else {
                    $k = addslashes($k);
                }

                //关键词查询条件
                if (empty($LikearticleRow['data']['relationtitle_status'])) {
                    $where_keyword .= ($where_keyword == '' ? " CONCAT(a.seo_keywords,' ',a.title) LIKE '%$k%' " : " OR CONCAT(a.seo_keywords,' ',a.title) LIKE '%$k%' ");
                } else {
                    $where_keyword .= ($where_keyword == '' ? " a.seo_keywords LIKE '%$k%' " : " OR a.seo_keywords LIKE '%$k%' ");
                }

                $n++;
            }
        } else {
            return false;
        }

        //排序
        if ($byabs == 0) {
            $orderquery = " a.aid desc ";
        } else {
            $orderquery = " ABS(a.aid - " . $this->aid . ") ";
        }

        $channelidArr = [];
        $aidArr = array();
        $field  = "b.*, a.*, a.aid";
        $map = [];
        if (!empty($typeidArr)) {
            $map['a.typeid'] = ['IN', $typeidArr];
        } else {
            if (!empty($channelid)) {
                $channelidArr = explode(',', $channelid);
                $map['a.channel'] = ['IN', $channelidArr];
            }
        }

        !isset($map['a.channel']) && $map['a.channel'] = ['IN', config('global.allow_release_channel')];

        $map['a.arcrank'] = ['gt', -1];
        $map['a.status'] = 1;
        $map['a.is_del'] = 0;
        $map['a.lang'] = ($this->version >= 'v1.5.5') ? self::$home_lang : $this->home_lang;
        $map['a.aid'] = ['NEQ', $this->aid];
        /*定时文档显示插件*/
        if (is_dir('./weapp/TimingTask/')) {
            $TimingTaskRow = model('Weapp')->getWeappList('TimingTask');
            if (!empty($TimingTaskRow['status']) && 1 == $TimingTaskRow['status']) {
                $map['a.add_time'] = ['elt', getTime()]; // 只显当天或之前的文档
            }
        }
        /*end*/

        // 预定相关文档的数量
        $limit_arr = explode(',', $limit);
        if (!empty($limit_arr[1])){
            $limit_start = current($limit_arr);
            $limit_end = end($limit_arr);
            $limit_num = abs(intval($limit_end - $limit_start));
            $limit = $limit_start.','.$limit_num;
        }else{
            $limit_start = 0;
            $limit_num = $limit;
        }


        $result = $this->archives_db
            ->field($field)
            ->alias('a')
            ->join('__ARCTYPE__ b', 'b.id = a.typeid', 'LEFT')
            ->where($where_keyword)
            ->where($map)
            ->orderRaw($orderquery)
            ->limit($limit)
            ->select();


        /*文档条不够，取相同tag标签的文档补充*/
        if ($limit_num > count($result)) {
            // 相同tag标签显示
            if (!empty($tags_arr)) {
                $aids = [];
                !empty($result) && $aids = get_arr_column($result, 'aid');
                array_push($aids, $this->aid);
                $tagmap = [
                    'tag'   => ['IN', $tags_arr],
                    'aid'   => ['NOTIN', $aids],
                    'arcrank'   => ['gt', -1],
                    'lang'  => ($this->version >= 'v1.5.5') ? self::$home_lang : $this->home_lang,
                ];
                if (!empty($typeidArr)) {
                    $tagmap['typeid'] = ['IN', $typeidArr];
                }
//                $tag_aids = Db::name('taglist')->where($tagmap)->group('aid')->order('aid desc')->limit($limit_start,100)->column('aid');
                $tag_aids = Db::name('taglist')->where($tagmap)->group('aid')->order('aid desc')->column('aid');

                $map = [];
                $map['a.aid'] = ['IN', $tag_aids];
                $map['a.arcrank'] = ['gt', -1];
                $map['a.status'] = 1;
                $map['a.is_del'] = 0;
                $map['a.lang'] = ($this->version >= 'v1.5.5') ? self::$home_lang : $this->home_lang;
                if (!empty($channelid)) {
                    $map['a.channel'] = ['IN', explode(',', $channelid)];
                } else {
                    $map['a.channel'] = ['IN', config('global.allow_release_channel')];
                }
                $limit2 = $limit_num - count($result);
                $result2 = $this->archives_db
                    ->field($field)
                    ->alias('a')
                    ->join('__ARCTYPE__ b', 'b.id = a.typeid', 'LEFT')
                    ->where($map)
                    ->orderRaw('a.aid desc')
                    ->limit($limit_start,$limit2)
                    ->select();
                $result = array_merge($result, $result2);
            }
        }
        /*end*/

        /*文档条不够，取同指定分类下的最新文档补充*/
        if (!empty($LikearticleRow['data']['fillarchives_status']) && $limit_num > count($result)) {
            // 相同分类下的文档显示
            $aids = [];
            !empty($result) && $aids = get_arr_column($result, 'aid');
            array_push($aids, $this->aid);
            $map = [];
            if (!empty($typeidArr)) {
                $map['a.typeid'] = ['IN', $typeidArr];
            }
            if (!empty($channelid)) {
                $map['a.channel'] = ['IN', explode(',', $channelid)];
            } else {
                $map['a.channel'] = ['IN', config('global.allow_release_channel')];
            }
            $map['a.aid'] = ['NOTIN', $aids];
            $map['a.arcrank'] = ['gt', -1];
            $map['a.status'] = 1;
            $map['a.is_del'] = 0;
            $map['a.lang'] = ($this->version >= 'v1.5.5') ? self::$home_lang : $this->home_lang;
            $limit2 = $limit_num - count($result);
            $result2 = $this->archives_db
                ->field($field)
                ->alias('a')
                ->join('__ARCTYPE__ b', 'b.id = a.typeid', 'LEFT')
                ->where($map)
                ->orderRaw('a.aid desc')
                ->limit($limit_start,$limit2)
                ->select();
            $result = array_merge($result, $result2);
        }
        /*end*/

        // 获取所有模型的控制器名
        $channeltypeRow = model('Channeltype')->getAll('id,ctl_name');
        $channeltypeRow = convert_arr_key($channeltypeRow, 'id');

        foreach ($result as $key => $val) {
            array_push($aidArr, $val['aid']); // 收集文档ID
            $controller_name = $channeltypeRow[$val['channel']]['ctl_name'];

            /*栏目链接*/
            if ($val['is_part'] == 1) {
                $val['typeurl'] = $val['typelink'];
            } else {
                $val['typeurl'] = typeurl('home/' . $controller_name . "/lists", $val);
            }
            /*--end*/
            /*文档链接*/
            if ($val['is_jump'] == 1) {
                $val['arcurl'] = $val['jumplinks'];
            } else {
                $val['arcurl'] = arcurl('home/' . $controller_name . '/view', $val);
            }
            /*--end*/
            /*封面图*/
            $val['litpic'] = get_default_pic($val['litpic']); // 默认封面图
            if ('on' == $thumb) { // 属性控制是否使用缩略图
                $val['litpic'] = thumb_img($val['litpic']);
            }
            /*--end*/

            $result[$key] = $val;
        }

        return $result;
    }
}