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

namespace app\admin\model;

use think\Db;
use think\Model;

/**
 * 文档主表
 */
class Archives extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 统计每个栏目文档数
     * @param int $aid 产品id
     */
    public function afterSave($aid, $post)
    {
        if (isset($post['aid']) && intval($post['aid']) > 0) {
            $opt = 'edit';
           Db::name('article_content')->where('aid', $aid)->update($post);
        } else {
            $opt = 'add';
            $post['aid'] = $aid;
           Db::name('article_content')->insert($post);
        }

        // --处理TAG标签
        model('Taglist')->savetags($aid, $post['typeid'], $post['tags'],$post['arcrank']);
    }

    /**
     * 获取单条记录
     * @author wengxianhu by 2017-7-26
     */
    public function getInfo($aid, $field = '', $isshowbody = true)
    {
        $result = array();
        if ($isshowbody) {
            $field = !empty($field) ? $field : 'b.*, a.*, a.aid as aid';
            $result = Db::name('archives')->field($field)
                ->alias('a')
                ->join('__ARTICLE_CONTENT__ b', 'b.aid = a.aid', 'LEFT')
                ->find($aid);
        } else {
            $field = !empty($field) ? $field : 'a.*';
            $result = Db::name('archives')->field($field)
                ->alias('a')
                ->find($aid);
        }

        // 文章TAG标签
        if (!empty($result)) {
            $typeid = isset($result['typeid']) ? $result['typeid'] : 0;
            $tags = model('Taglist')->getListByAid($aid, $typeid);
            $result['tags'] = $tags['tag_arr'];
            $result['tag_id'] = $tags['tid_arr'];
        }

        return $result;
    }

    /**
     * 伪删除栏目下所有文档
     */
    public function pseudo_del($typeidArr)
    {
        // 伪删除文档
       Db::name('archives')->where([
                'typeid'    => ['IN', $typeidArr],
                'is_del'    => 0,
            ])
            ->update([
                'is_del'    => 1,
                'del_method'    => 2,
                'update_time'   => getTime(),
            ]);

        return true;
    }

    /**
     * 删除栏目下所有文档
     */
    public function del($typeidArr)
    {
        /*获取栏目下所有文档，并取得每个模型下含有的文档ID集合*/
        $channelAidList = array(); // 模型下的文档ID列表
        $arcrow =Db::name('archives')->where(array('typeid'=>array('IN', $typeidArr)))
            ->order('channel asc')
            ->select();
        foreach ($arcrow as $key => $val) {
            if (!isset($channelAidList[$val['channel']])) {
                $channelAidList[$val['channel']] = array();
            }
            array_push($channelAidList[$val['channel']], $val['aid']);
        }
        /*--end*/

        /*在相关模型下删除文档残余的关联记录*/
        $sta =Db::name('archives')->where(array('typeid'=>array('IN', $typeidArr)))->delete(); // 删除文档
        if ($sta) {
            foreach ($channelAidList as $key => $val) {
                $aidArr = $val;
                /*删除其余相关联的表记录*/
                switch ($key) {
                    case '1': // 文章模型
                        model('Article')->afterDel($aidArr);
                        break;
                    
                    case '2': // 产品模型
                        model('Product')->afterDel($aidArr);
                       Db::name('product_attribute')->where(array('typeid'=>array('IN', $typeidArr)))->delete();
                        break;
                    
                    case '3': // 图集模型
                        model('Images')->afterDel($aidArr);
                        break;
                    
                    case '4': // 下载模型
                        model('Download')->afterDel($aidArr);
                        break;
                    
                    case '6': // 单页模型
                        model('Single')->afterDel($typeidArr);
                        break;

                    default:
                        # code...
                        break;
                }
                /*--end*/
            }
        }
        /*--end*/

        /*删除留言模型下的关联内容*/
        $guestbookList =Db::name('guestbook')->where(array('typeid'=>array('IN', $typeidArr)))->select();
        if (!empty($guestbookList)) {
            $aidArr = get_arr_column($guestbookList, 'aid');
            $typeidArr = get_arr_column($guestbookList, 'typeid');
            if ($aidArr && $typeidArr) {
                $sta =Db::name('guestbook')->where(array('typeid'=>array('IN', $typeidArr)))->delete();
                if ($sta) {
                   Db::name('guestbook_attribute')->where(array('typeid'=>array('IN', $typeidArr)))->delete();
                    model('Guestbook')->afterDel($aidArr);
                }
            }
        }
        /*--end*/

        return true;
    }

    /**
     * 获取单条记录
     * @author 陈风任 by 2020-06-08
     */
    public function UnifiedGetInfo($aid, $field = '', $isshowbody = true)
    {
        $result = array();
        $field = !empty($field) ? $field : '*';
        $result = Db::name('archives')->field($field)
            ->where([
                'aid'   => $aid,
                'lang'  => get_admin_lang(),
            ])
            ->find();
        if ($isshowbody) {
            $tableName = Db::name('channeltype')->where('id','eq',$result['channel'])->getField('table');
            $result['addonFieldExt'] = Db::name($tableName.'_content')->where('aid',$aid)->find();
        }

        // 产品TAG标签
        if (!empty($result)) {
            $typeid = isset($result['typeid']) ? $result['typeid'] : 0;
            $tags = model('Taglist')->getListByAid($aid, $typeid);
            $result['tags'] = $tags;
        }

        return $result;
    }
}