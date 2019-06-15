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
    public function del($del_id = array())
    {
        if (empty($del_id)) {
            $del_id = input('del_id/a');
        }

        $id_arr = eyIntval($del_id);
        if(!empty($id_arr)){
            /*分离并组合相同模型下的文档ID*/
            $row = db('archives')
                ->alias('a')
                ->field('a.channel,a.aid,b.ctl_name')
                ->join('__CHANNELTYPE__ b', 'a.channel = b.id', 'LEFT')
                ->where([
                    'a.aid' => ['IN', $id_arr],
                    'a.lang'    => $this->admin_lang,
                ])
                ->select();
            $data = array();
            foreach ($row as $key => $val) {
                $data[$val['channel']]['aid'][] = $val['aid'];
                $data[$val['channel']]['ctl_name'] = $val['ctl_name'];
            }
            /*--end*/

            $info['is_del']     = '1'; // 伪删除状态
            $info['update_time']= getTime(); // 更新修改时间
            $info['del_method'] = '1'; // 恢复删除方式为默认

            $err = 0;
            foreach ($data as $key => $val) {
                // $r = M('archives')->where('aid','IN',$val['aid'])->delete();
                $r = M('archives')->where('aid','IN',$val['aid'])->update($info);
                if ($r) {
                    // model($val['ctl_name'])->afterDel($val['aid']);
                    adminLog('删除文档-id：'.implode(',', $val['aid']));
                } else {
                    $err++;
                }
            }

            if (0 == $err) {
                $this->success('删除成功');
            } else if ($err < count($data)) {
                $this->success('删除部分成功');
            } else {
                $this->error('删除失败');
            }
        }else{
            $this->error('参数有误');
        }
    }

    /**
     * 获取文档模板文件列表
     */
    public function getTemplateList($nid = 'article')
    {   
        $planPath = 'template/pc';
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
}
