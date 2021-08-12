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

use think\Model;
use think\Db;
/**
 * 文档逻辑定义
 * Class CatsLogic
 * @package admin\Logic
 */
load_trait('controller/Jump');
class ArchivesLogic extends Model
{
    use \traits\controller\Jump;
    
    private $admin_lang = 'cn';

    /**
     * 析构函数
     */
    function  __construct() {
        $this->admin_lang = get_admin_lang();
    }

    /**
     * 删除文档
     */
    public function del($del_id = array(), $thorough = 0, $table = '')
    {
        $del_id = !empty($del_id) ? $del_id : input('del_id/a');
        $id_arr = eyIntval($del_id);
        $thorough = !empty($thorough) ? $thorough : input('thorough/d');
        if (!empty($id_arr)) {
            /*分离并组合相同模型下的文档ID*/
            $field = 'a.aid, a.typeid, a.channel, a.arcrank, a.is_recom, a.is_special, a.is_b, a.is_head, a.is_litpic, a.is_jump, a.is_slide, a.is_roll, a.is_diyattr, a.users_id, b.table, b.ctl_name, b.ifsystem';
            $row = Db::name('archives')
                ->alias('a')
                ->field($field)
                ->join('__CHANNELTYPE__ b', 'a.channel = b.id', 'LEFT')
                ->where([
                    'a.aid' => ['IN', $id_arr],
                    'a.lang'    => $this->admin_lang,
                ])
                ->select();

            $data = array();
            foreach ($row as $key => $val) {
                $data[$val['channel']]['aid'][] = $val['aid'];
                $data[$val['channel']]['table'] = $val['table'];
                if (empty($val['ifsystem'])) {
                    $ctl_name = 'Custom';
                } else {
                    $ctl_name = $val['ctl_name'];
                }
                $data[$val['channel']]['ctl_name'] = $ctl_name;
            }
            /*--end*/

            if (1 == $thorough) { // 直接删除，跳过回收站
                $err = 0;
                foreach ($data as $key => $val) {
                    $r = Db::name('archives')->where('aid','IN',$val['aid'])->delete();
                    if ($r) {
                        if (empty($val['ifsystem'])) {
                            model($val['ctl_name'])->afterDel($val['aid'], $val['table']);
                        } else {
                            model($val['ctl_name'])->afterDel($val['aid']);
                        }
                        adminLog('删除文档-id：'.implode(',', $val['aid']));
                    } else {
                        $err++;
                    }
                }
            } else {
                $info['is_del']     = 1; // 伪删除状态
                $info['update_time']= getTime(); // 更新修改时间
                $info['del_method'] = 1; // 恢复删除方式为默认

                $err = 0;
                foreach ($data as $key => $val) {
                    $r = Db::name('archives')->where('aid','IN',$val['aid'])->update($info);
                    if ($r) {
                        adminLog('删除文档-id：'.implode(',', $val['aid']));
                    } else {
                        $err++;
                    }
                }
            }

            if (0 == $err) {
                // 处理mysql缓存表数据
                $DraftData = [
                    'TypeID' => [],
                    'UsersID' => [],
                ];
                foreach ($row as $key => $value) {
                    if (-1 === $value['arcrank'] && !empty($value['users_id'])) {
                        array_push($DraftData['TypeID'], $value['typeid']);
                        array_push($DraftData['UsersID'], $value['users_id']);
                        unset($row[$key]);
                    }
                }
                if (!empty($row)) model('SqlCacheTable')->UpdateSqlCacheTable($row, 'del', $table);
                if (!empty($DraftData)) model('SqlCacheTable')->UpdateDraftSqlCacheTable($DraftData, 'admin_del');
                $this->success('删除成功！');
            } else if ($err < count($data)) {
                $this->success('删除部分成功！');
            } else {
                $this->error('删除失败！');
            }
        }else{
            $this->error('文档不存在！');
        }
    }

    /**
     * 获取文档模板文件列表
     */
    public function getTemplateList($nid = 'article')
    {   
        $planPath = 'template/'.TPL_THEME.'pc';
        $dirRes   = opendir($planPath);
        $view_suffix = config('template.view_suffix');

        /*模板PC目录文件列表*/
        $templateArr = array();
        while($filename = readdir($dirRes))
        {
            if (in_array($filename, array('.','..'))) {
                continue;
            }
            array_push($templateArr, $filename);
        }
        /*--end*/

        /*多语言全部标识*/
        $markArr = Db::name('language_mark')->column('mark');
        /*--end*/

        $templateList = array();
        foreach ($templateArr as $k2 => $v2) {
            $v2 = iconv('GB2312', 'UTF-8', $v2);
            preg_match('/^(view)_'.$nid.'(_(.*))?(_'.$this->admin_lang.')?\.'.$view_suffix.'/i', $v2, $matches1);
            $langtpl = preg_replace('/\.'.$view_suffix.'$/i', "_{$this->admin_lang}.{$view_suffix}", $v2);
            if (file_exists(realpath($planPath.DS.$langtpl))) {
                continue;
            } else if (preg_match('/^(.*)_([a-zA-z]{2,2})\.'.$view_suffix.'$/i',$v2,$matches2)) {
                if (in_array($matches2[2], $markArr) && $matches2[2] != $this->admin_lang) {
                    continue;
                }
            }

            if (!empty($matches1)) {
                if ('view' == $matches1[1]) {
                    array_push($templateList, $v2);
                }
            }
        }

        return $templateList;
    }

    /**
     * 复制文档
     */
    public function batch_copy($aids = [], $typeid, $channel, $num = 1)
    {
        // 获取复制栏目的模型ID
        $channeltypeRow = Db::name('channeltype')->field('nid,table')
            ->where([
                'id'    => $channel,
            ])->find();
        if (!empty($channeltypeRow)) {
            // 主表数据
            $archivesRow = Db::name('archives')->where(['aid'=>['IN', $aids]])->select();
            // 内容扩展表数据
            $tableExt = $channeltypeRow['table']."_content";
            $contentRow = Db::name($tableExt)->field('id', true)->where(['aid'=>['IN', $aids]])->getAllWithIndex('aid');

            // 拥有特性模型的其他数据处理
            if ('images' == $channeltypeRow['nid']) { // 图集模型的特性表数据
                $imgUploadRow = Db::name('images_upload')->field('img_id', true)->where(['aid'=>['IN', $aids]])->select();
                $imgUploadRow = group_same_key($imgUploadRow, 'aid');
            } 
            else if ('download' == $channeltypeRow['nid']) { // 下载模型的特性表数据
                // 附件表
                $downloadFileRow = Db::name('download_file')->field('file_id', true)->where(['aid'=>['IN', $aids]])->select();
                $downloadFileRow = group_same_key($downloadFileRow, 'aid');
                // 附件下载记录表
                $downloadLogRow = Db::name('download_log')->field('log_id', true)->where(['aid'=>['IN', $aids]])->select();
                $downloadLogRow = group_same_key($downloadLogRow, 'aid');
            }
            else if ('product' == $channeltypeRow['nid']) { // 产品模型的特性表数据
                // 属性值表
                $productAttrRow = Db::name('product_attr')->field('product_attr_id', true)->where(['aid'=>['IN', $aids]])->select();
                $productAttrRow = group_same_key($productAttrRow, 'aid');
                // 产品图集表
                $productImgRow = Db::name('product_img')->field('img_id', true)->where(['aid'=>['IN', $aids]])->select();
                $productImgRow = group_same_key($productImgRow, 'aid');
                // 产品虚拟表
                $productNetdiskRow = Db::name('product_netdisk')->field('nd_id', true)->where(['aid'=>['IN', $aids]])->select();
                $productNetdiskRow = group_same_key($productNetdiskRow, 'aid');
                // 产品规格数据表
                $productSpecRow = Db::name('product_spec_data')->field('spec_id', true)->where(['aid'=>['IN', $aids]])->select();
                $productSpecRow = group_same_key($productSpecRow, 'aid');
                // 产品多规格组装表
                $productSpecValueRow = Db::name('product_spec_value')->field('value_id', true)->where(['aid'=>['IN', $aids]])->select();
                $productSpecValueRow = group_same_key($productSpecValueRow, 'aid');
            }

            foreach ($archivesRow as $key => $val) {
                // 原先数据的栏目ID
                $typeid_old = $val['typeid'];

                // 同步数据
                $archivesData = [];
                for ($i = 0; $i < $num; $i++) {
                    // 主表
                    $archivesInfo = $val;
                    unset($archivesInfo['aid']);
                    $archivesInfo['typeid'] = $typeid;
                    $archivesInfo['add_time'] = getTime();
                    $archivesInfo['update_time'] = getTime();
                    $archivesData[] = $archivesInfo;
                }
                if (!empty($archivesData)) {
                    $rdata = model('Archives')->saveAll($archivesData);
                    if ($rdata) {
                        // 内容扩展表的数据
                        $contentData = [];
                        $contentInfo = $contentRow[$val['aid']];

                        // 拥有特性模型的其他数据处理
                        $imgUploadInfo = $imgUploadData = [];
                        $downloadFileInfo = $downloadLogInfo = [];
                        $downloadFileData = $downloadLogData = [];
                        $productAttrInfo = $productImgInfo = $productNetdiskInfo = $productSpecInfo = $productSpecValueInfo = [];
                        $productAttrData = $productImgData = $productNetdiskData = $productSpecData = $productSpecValueData = [];
                        if ('images' == $channeltypeRow['nid']) { // 图集模型的特性表数据
                            $imgUploadInfo = !empty($imgUploadRow[$val['aid']]) ? $imgUploadRow[$val['aid']] : [];
                        } else if ('download' == $channeltypeRow['nid']) { // 下载模型的特性表数据
                            $downloadFileInfo = !empty($downloadFileRow[$val['aid']]) ? $downloadFileRow[$val['aid']] : [];
                            $downloadLogInfo = !empty($downloadLogRow[$val['aid']]) ? $downloadLogRow[$val['aid']] : [];
                        } else if ('product' == $channeltypeRow['nid']) { // 新房模型的特性表数据
                            // 属性值表 - 只复制同栏目的属性值
                            if ($typeid_old == $typeid) {
                                $productAttrInfo = !empty($productAttrRow[$val['aid']]) ? $productAttrRow[$val['aid']] : [];
                            }
                            // 产品图集表
                            $productImgInfo = !empty($productImgRow[$val['aid']]) ? $productImgRow[$val['aid']] : [];
                            // 产品虚拟表
                            $productNetdiskInfo = !empty($productNetdiskRow[$val['aid']]) ? $productNetdiskRow[$val['aid']] : [];
                            // 产品规格数据表
                            $productSpecInfo = !empty($productSpecRow[$val['aid']]) ? $productSpecRow[$val['aid']] : [];
                            // 产品多规格组装表
                            $productSpecValueInfo = !empty($productSpecValueRow[$val['aid']]) ? $productSpecValueRow[$val['aid']] : [];
                        }

                        // 需要复制的数据与新产生的文档ID进行关联
                        foreach ($rdata as $k1 => $v1) {
                            $aid_new = $v1->getData('aid');

                            // 内容扩展表的数据
                            $contentInfo['aid'] = $aid_new;
                            $contentData[] = $contentInfo;

                            // 图集模型
                            if ('images' == $channeltypeRow['nid']) {
                                foreach ($imgUploadInfo as $img_k => $img_v) {
                                    $img_v['aid'] = $aid_new;
                                    $imgUploadData[] = $img_v;
                                }
                            } else if ('download' == $channeltypeRow['nid']) {
                                // 附件表
                                foreach ($downloadFileInfo as $file_k => $file_v) {
                                    $file_v['aid'] = $aid_new;
                                    $downloadFileData[] = $file_v;
                                }
                                // 附件下载记录表
                                foreach ($downloadLogInfo as $log_k => $log_v) {
                                    $log_v['aid'] = $aid_new;
                                    $downloadLogData[] = $log_v;
                                }
                            } else if ('product' == $channeltypeRow['nid']) {
                                // 属性值表
                                foreach ($productAttrInfo as $attr_k => $attr_v) {
                                    $attr_v['aid'] = $aid_new;
                                    $productAttrData[] = $attr_v;
                                }
                                // 产品图集表
                                foreach ($productImgInfo as $img_k => $img_v) {
                                    $img_v['aid'] = $aid_new;
                                    $productImgData[] = $img_v;
                                }
                                // 产品虚拟表
                                foreach ($productNetdiskInfo as $nd_k => $nd_v) {
                                    $nd_v['aid'] = $aid_new;
                                    $productNetdiskData[] = $nd_v;
                                }
                                // 产品规格数据表
                                foreach ($productSpecInfo as $spec_k => $spec_v) {
                                    $spec_v['aid'] = $aid_new;
                                    $productSpecData[] = $spec_v;
                                }
                                // 产品多规格组装表
                                foreach ($productSpecValueInfo as $specv_k => $specv_v) {
                                    $specv_v['aid'] = $aid_new;
                                    $productSpecValueData[] = $specv_v;
                                }
                            }
                        }

                        // 批量写入内容扩展表
                        if (!empty($contentData)) {
                            Db::name($tableExt)->insertAll($contentData);
                        }
                        // 批量写入图集模型的图片表
                        if ('images' == $channeltypeRow['nid']) {
                            !empty($imgUploadData) && model('ImagesUpload')->saveAll($imgUploadData);
                        } else if ('download' == $channeltypeRow['nid']) {
                            // 附件表
                            !empty($downloadFileData) && model('DownloadFile')->saveAll($downloadFileData);
                            // 附件下载记录表
                            !empty($downloadLogData) && Db::name('download_log')->insertAll($downloadLogData);
                        } else if ('product' == $channeltypeRow['nid']) {
                            // 属性值表
                            !empty($productAttrData) && Db::name('product_attr')->insertAll($productAttrData);
                            // 产品图集表
                            !empty($productImgData) && model('ProductImg')->saveAll($productImgData);
                            // 产品虚拟表
                            !empty($productNetdiskData) && model('ProductNetdisk')->saveAll($productNetdiskData);
                            // 产品规格数据表
                            !empty($productSpecData) && model('ProductSpecData')->saveAll($productSpecData);
                            // 产品多规格组装表
                            !empty($productSpecValueData) && model('ProductSpecValue')->saveAll($productSpecValueData);
                        }
                    }
                    else {
                        $this->error('复制失败！');
                    }
                }
            }
            /*清空sql_cache_table数据缓存表 并 添加查询执行语句到mysql缓存表*/
            Db::name('sql_cache_table')->query('TRUNCATE TABLE '.config('database.prefix').'sql_cache_table');
            model('SqlCacheTable')->InsertSqlCacheTable(true);
            /* END */
            $this->success('复制成功！');
        } else {
            $this->error('模型不存在！');
        }
    }
}
