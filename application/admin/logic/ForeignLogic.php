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

namespace app\admin\logic;

use think\Db;
use think\Cache;

/**
 * 业务逻辑
 */
class ForeignLogic
{
    public function __construct() 
    {

    }

    /**
     * 更新文档自定义文件名
     * @param  [type] $aid  [description]
     * @param  array  $post [description]
     * @param  string $opt  [description]
     * @return [type]       [description]
     */
    public function update_htmlfilename($aid, $post = [], $opt = 'add')
    {
        if ('add' == $opt) {
            $seo_config = tpCache('seo');
            $seo_pseudo = !empty($seo_config['seo_pseudo']) ? intval($seo_config['seo_pseudo']) : 0;
            if (in_array($seo_pseudo, [2,3])) {
                $foreign_is_status = tpSetting('foreign.foreign_is_status', '', 'cn');
                $seo_titleurl_format = !empty($seo_config['seo_titleurl_format']) ? intval($seo_config['seo_titleurl_format']) : 0;
                if (!empty($foreign_is_status) && !empty($seo_titleurl_format)) {
                    $htmlfilename = $post['htmlfilename'].'_'.$aid;
                    Db::name('archives')->where(['aid'=>$aid])->update(['htmlfilename'=>$htmlfilename]);
                }
            }
        }
    }

    /**
     * 获取标题生成外贸链接字符串
     * @param  [type] $post         [description]
     * @param  string $opt          [description]
     * @param  array  $globalConfig [description]
     * @return [type]               [description]
     */
    public function get_new_htmlfilename(&$htmlfilename, $post, $opt = 'add', $globalConfig = [])
    {
        if (empty($globalConfig)) {
            $globalConfig = tpCache('global');
        }
        if (in_array($globalConfig['seo_pseudo'], [2,3])) {
            $foreign_is_status = tpSetting('foreign.foreign_is_status', '', 'cn');
            $seo_titleurl_format = (int)$globalConfig['seo_titleurl_format'];
            if (!empty($foreign_is_status) && !empty($seo_titleurl_format)) {
                $htmlfilename = $this->get_title_htmlfilename(trim($post['title']));
                if ('edit' == $opt) {
                    $htmlfilename .= '_'.$post['aid'];
                }
            }
        }
    }

    /**
     *  文章标题转成外贸指定格式字母串
     *
     * @param     string $str 字符串信息
     * @param     int $ishead 是否取头字母
     * @param     int $isclose 是否关闭字符串资源
     * @return    string
     */
    public function get_title_htmlfilename($str, $ishead = 0, $isclose = 1)
    {
        $str = str_replace(['—'], ' ', $str);
        $str = preg_replace('/(\s+)/i', ' ', $str);
        try{
            $s1 = iconv("UTF-8", "gb2312", $str);
            $s2 = iconv("gb2312", "UTF-8", $s1);
            if ($s2 == $str) {
                $str = $s1;
            }

            static $pinyins = null;
            $restr   = '';
            $str     = trim($str);
            $slen    = strlen($str);
            if ($slen < 2) {
                $str = preg_replace('/([\-\_]+)$/i', '', $str); // 去掉结尾的符号
                return $str;
            }
            if (null === $pinyins) {
                $pinyins = [];
                $fp = fopen(DATA_PATH . 'conf/pinyin.dat', 'r');
                while (!feof($fp)) {
                    $line                         = trim(fgets($fp));
                    $pinyins[$line[0] . $line[1]] = substr($line, 3, strlen($line) - 3);
                }
                fclose($fp);
            }
            for ($i = 0; $i < $slen; $i++) {
                if (ord($str[$i]) > 0x80) {
                    $c = $str[$i] . $str[$i + 1];
                    $i++;
                    if (isset($pinyins[$c])) {
                        if ($ishead == 0) {
                            $restr .= $pinyins[$c];
                        } else {
                            $restr .= $pinyins[$c][0];
                        }
                    } else {
                        $restr .= "-";
                    }
                } else if (preg_match("/[a-z0-9]/i", $str[$i])) {
                    $restr .= $str[$i];
                } else {
                    $restr .= "-";
                }
            }
            if ($isclose == 0) {
                unset($pinyins);
            }
            $restr = strtolower($restr);
            $restr = preg_replace('/([\-\_]+)$/i', '', $restr); // 去掉结尾的符号
            $restr = preg_replace('/([\-]+)/i', '-', $restr);
            $restr = preg_replace('/([\_]+)/i', '_', $restr);
            $restr = trim($restr, '-');
            return $restr;
        }catch (\Exception $e){
            return "";
        }
    }

    /**
     * 处理更新文档的自定义文件名
     * $achievepage 已完成文档数
     * $batch       是否分批次执行，true：分批，false：不分批
     * limit        每次执行多少条数据
     */
    public function handelUpdateArticle($foreign_htmlfilename_mode = 0, $achievepage = 0, $batch = true, $limit = '')
    {
        $msg                  = "";
        $result               = $this->getArticleData($achievepage, $limit);
        $info                 = $result['info'];
        $data['allpagetotal'] = $pagetotal = $result['pagetotal'];
        $data['achievepage']  = $achievepage;
        $data['pagetotal']    = 0;

        if ($batch && $pagetotal > $achievepage) {
            $msg .= $msg_temp = $this->updateHtmlfilename($foreign_htmlfilename_mode, $info);
            $data['achievepage'] += count($info);
        }

        return [$msg, $data];
    }

    /**
     * 获取详情页数据
     */
    private function getArticleData($offset = 0, $limit = 0)
    {
        empty($limit) && $limit = 500;
        $map = [];
        $allow_release_channel = config('global.allow_release_channel');
        $map['channel'] = ['IN', $allow_release_channel];
        $map['arcrank'] = ['>=', -1];
        $info = [];
        $list = Db::name('archives')->field("aid,title,htmlfilename")
            ->where($map)
            ->order('aid asc')
            ->limit($offset, $limit)
            ->select();
        foreach ($list as $key=>$val){
            $info_value = [];
            $info_value['aid'] = $val['aid'];
            $info_value['title'] = trim($val['title']);
            $info_value['htmlfilename'] = $val['htmlfilename'];
            $info[] = $info_value;
        }

        // 总文档数
        $pagetotal = Db::name('archives')->field('aid')->where($map)->count();

        return ['info' => $info, 'pagetotal' => $pagetotal];
    }

    /*
     * 更新文档的自定义文件名
     */
    private function updateHtmlfilename($foreign_htmlfilename_mode, $result)
    {
        $msg = "";
        $globalConfig = tpCache('global');
        foreach ($result as $key => $val) {
            $htmlfilename = '';
            if (empty($foreign_htmlfilename_mode)) {
                $this->get_new_htmlfilename($htmlfilename, $val, 'edit', $globalConfig);
            }
            $val['htmlfilename'] = $htmlfilename;
            $result[$key] = $val;
        }
        $archivesModel = new \app\admin\model\Archives;
        $r2 = $archivesModel->saveAll($result);
        if ($r2 !== false) {
            
        } else {
            $msg .= '<span>' . '更新失败！' . $e->getMessage() . '</span><br>';
        }

        return $msg;
    }
}
