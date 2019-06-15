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

namespace app\common\model;

use think\Db;
use think\Model;

/**
 * 模型
 */
class Language extends Model
{
    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
    }

    /**
     * 后置操作方法
     * 自定义的一个函数 用于数据新增之后做的相应处理操作, 使用时手动调用
     * @param int $aid 产品id
     * @param array $post post数据
     * @param string $opt 操作
     */
    public function afterAdd($insertId = '', $post = [])
    {
        $mark = trim($post['mark']);

        /*设置默认语言，只允许有一个是默认，其他取消*/
        if (1 == intval($post['is_home_default'])) {
            $this->where('id','NEQ',$insertId)->update([
                'is_home_default' => 0,
                'update_time' => getTime(),
            ]);
            /*多语言 设置默认前台语言*/
            if (is_language()) {
                $langRow = \think\Db::name('language')->order('id asc')
                    ->cache(true, EYOUCMS_CACHE_TIME, 'language')
                    ->select();
                foreach ($langRow as $key => $val) {
                    tpCache('system', ['system_home_default_lang'=>$mark], $val['mark']);
                }
            } else { // 单语言
                tpCache('system', ['system_home_default_lang'=>$mark]);
            }
            /*--end*/
        }
        /*--end*/

        /*复制栏目表以及关联数据*/
        $syn_status = $this->syn_arctype($mark, $post['copy_lang']);
        if (false === $syn_status) {
            return $syn_status;
        }
        /*--end*/

        /*复制阅读权限表数据*/
        $arcrank_db = Db::name('arcrank');
        $arcrankCount = $arcrank_db->where('lang',$mark)->count();
        if (empty($arcrankCount)) {
            $arcrankRow = $arcrank_db->field('id,lang',true)
                ->where('lang', $post['copy_lang'])
                ->order('id asc')
                ->select();
            if (!empty($arcrankRow)) {
                foreach ($arcrankRow as $key => $val) {
                    $arcrankRow[$key]['lang'] = $mark;
                }
                $insertNum = $arcrank_db->insertAll($arcrankRow);
                if ($insertNum != count($arcrankRow)) {
                    return false;
                }
            }
        }
        /*--end*/

        /*复制基础信息表数据*/
        $config_db = Db::name('config');
        $configCount = $config_db->where('lang',$mark)->count();
        if (empty($configCount)) {
            $configRow = $config_db->field('id,lang',true)
                ->where('lang', $post['copy_lang'])
                ->order('id asc')
                ->select();
            if (!empty($configRow)) {
                foreach ($configRow as $key => $val) {
                    $configRow[$key]['lang'] = $mark;
                    /*临时测试*/
                    if ($val['name'] == 'web_name') {
                        $configRow[$key]['value'] = $mark.$val['value'];
                    }
                    /*--end*/
                }
                $insertObject = model('Config')->saveAll($configRow);
                $insertNum = count($insertObject);
                if ($insertNum != count($configRow)) {
                    return false;
                }
            }
        }
        /*--end*/

        /*复制自定义变量表数据*/
        $configattribute_db = Db::name('config_attribute');
        $configattributeCount = $configattribute_db->where('lang',$mark)->count();
        if (empty($configattributeCount)) {
            $configAttrRow = $configattribute_db->field('attr_id,lang',true)
                ->where('lang', $post['copy_lang'])
                ->order('attr_id asc')
                ->select();
            if (!empty($configAttrRow)) {
                foreach ($configAttrRow as $key => $val) {
                    $configAttrRow[$key]['lang'] = $mark;
                }
                $insertObject = model('ConfigAttribute')->saveAll($configAttrRow);
                $insertNum = count($insertObject);
                if ($insertNum != count($configAttrRow)) {
                    return false;
                }
            }
        }
        /*--end*/

        /*复制广告位置表以及广告表数据*/
        $syn_status = $this->syn_ad_position($mark, $post['copy_lang']);
        if (false === $syn_status) {
            return $syn_status;
        }
        /*--end*/

        /*复制友情链接表数据*/
        $links_db = Db::name('links');
        $linksCount = $links_db->where('lang',$mark)->count();
        if (empty($linksCount)) {
            $linksRow = $links_db->field('id,lang',true)
                ->where('lang', $post['copy_lang'])
                ->order('id asc')
                ->select();
            if (!empty($linksRow)) {
                foreach ($linksRow as $key => $val) {
                    $linksRow[$key]['lang'] = $mark;
                    $linksRow[$key]['title'] = $mark.$val['title']; // 临时测试
                }
                $insertObject = model('Links')->saveAll($linksRow);
                $insertNum = count($insertObject);
                if ($insertNum != count($linksRow)) {
                    return false;
                }
            }
        }
        /*--end*/

        /*复制邮件模板表数据*/
        $smtp_tpl_db = Db::name('smtp_tpl');
        $smtptplCount = $smtp_tpl_db->where('lang',$mark)->count();
        if (empty($smtptplCount)) {
            $smtptplRow = $smtp_tpl_db->field('tpl_id,lang',true)
                ->where('lang', $post['copy_lang'])
                ->order('tpl_id asc')
                ->select();
            if (!empty($smtptplRow)) {
                foreach ($smtptplRow as $key => $val) {
                    $smtptplRow[$key]['lang'] = $mark;
                }
                $insertObject = model('SmtpTpl')->saveAll($smtptplRow);
                $insertNum = count($insertObject);
                if ($insertNum != count($smtptplRow)) {
                    return false;
                }
            }
        }
        /*--end*/

        /*复制模板语言包变量表数据*/
        $langpack_db = Db::name('language_pack');
        $langpackCount = $langpack_db->where('lang',$mark)->count();
        if (empty($langpackCount)) {
            $langpackRow = $langpack_db->field('id,lang',true)
                ->where([
                    'lang'  => $post['copy_lang'],
                    'is_syn'    => 0,
                ])
                ->order('id asc')
                ->select();
            if (!empty($langpackRow)) {
                foreach ($langpackRow as $key => $val) {
                    $langpackRow[$key]['lang'] = $mark;
                }
                $insertObject = model('LanguagePack')->saveAll($langpackRow);
                $insertNum = count($insertObject);
                if ($insertNum != count($langpackRow)) {
                    return false;
                }
            }
        }
        /*--end*/
        
        /*统计多语言数量*/
        $this->setLangNum();
        /*--end*/

        \think\Cache::clear('language');
        delFile(RUNTIME_PATH.'cache'.DS.$mark, true);

        return true;
    }

    /**
     * 统计多语言数量
     */
    public function setLangNum()
    {
        \think\Cache::clear('system_langnum');
        $languageRow = Db::name('language')->field('mark')->select();
        foreach ($languageRow as $key => $val) {
            tpCache('system', ['system_langnum'=>count($languageRow)], $val['mark']);
        }
    }

    /**
     * 后置操作方法
     * 自定义的一个函数 用于数据删除之后做的相应处理操作, 使用时手动调用
     * @param int $aid 产品id
     * @param array $post post数据
     * @param string $opt 操作
     */
    public function afterDel($id_arr = [], $lang_list = [])
    {
        if (!empty($id_arr) && !empty($lang_list)) {
            \think\Cache::clear('language');
            foreach ($lang_list as $key => $lang) {
                delFile(RUNTIME_PATH.'cache'.DS.$lang, true);
                @unlink(APP_PATH."lang/{$lang}.php");
            }
            /*统计多语言数量*/
            $this->setLangNum();
            /*同步删除模板栏目绑定表数据*/
            Db::name('language_attr')->where("lang",'IN',$lang_list)->delete();
            /*同步删除模板语言变量表数据*/
            Db::name('language_pack')->where("lang",'IN',$lang_list)->delete();
            /*同步删除阅读权限表数据*/
            Db::name('arcrank')->where("lang",'IN',$lang_list)->delete();
            /*同步删除基础信息表数据*/
            Db::name('config')->where("lang",'IN',$lang_list)->delete();
            /*同步删除自定义变量表数据*/
            Db::name('config_attribute')->where("lang",'IN',$lang_list)->delete();
            /*同步删除栏目表以及文档表数据*/
            $typeids = Db::name('arctype')->where("lang",'IN',$lang_list)->column('id');
            //待删除栏目ID集合
            Db::name('arctype')->where("lang",'IN',$lang_list)->delete(); // 栏目表
            $aids = Db::name('archives')->where("typeid",'IN',$typeids)->column('aid'); 
            //待删除文档ID集合
            Db::name('archives')->where("aid",'IN',$aids)->delete(); // 文档主表
            Db::name('article_content')->where("aid",'IN',$aids)->delete(); // 文章内容表
            Db::name('download_content')->where("aid",'IN',$aids)->delete(); // 软件内容表
            Db::name('download_file')->where("aid",'IN',$aids)->delete(); // 软件附件表
            Db::name('guestbook')->where("aid",'IN',$aids)->delete(); // 留言主表
            Db::name('guestbook_attr')->where("aid",'IN',$aids)->delete(); // 留言内容表
            Db::name('images_content')->where("aid",'IN',$aids)->delete(); // 图集内容表
            Db::name('images_upload')->where("aid",'IN',$aids)->delete(); // 图集图片表
            Db::name('product_content')->where("aid",'IN',$aids)->delete(); // 产品内容表
            Db::name('product_img')->where("aid",'IN',$aids)->delete(); // 产品图集表
            Db::name('single_content')->where("aid",'IN',$aids)->delete(); // 单页内容表
            /*同步删除产品属性表数据*/
            Db::name('product_attribute')->where("lang",'IN',$lang_list)->delete();
            /*同步删除留言属性表数据*/
            Db::name('guestbook_attribute')->where("lang",'IN',$lang_list)->delete();
            /*同步删除广告表数据*/
            Db::name('ad')->where("lang",'IN',$lang_list)->delete();
            /*同步删除广告位置表数据*/
            Db::name('ad_position')->where("lang",'IN',$lang_list)->delete();
            /*同步删除友情链接表数据*/
            Db::name('links')->where("lang",'IN',$lang_list)->delete();
            /*同步删除可视化表数据*/
            Db::name('ui_config')->where("lang",'IN',$lang_list)->delete();
            /*同步删除Tag标签表数据*/
            Db::name('taglist')->where("lang",'IN',$lang_list)->delete();
            /*同步删除标签索引表数据*/
            Db::name('tagindex')->where("lang",'IN',$lang_list)->delete();
            /*同步删除邮件模板表数据*/
            Db::name('smtp_tpl')->where("lang",'IN',$lang_list)->delete();
            /*同步删除邮件发送记录表数据*/
            Db::name('smtp_record')->where("lang",'IN',$lang_list)->delete();
        }
    }

    /**
     * 创建语言时，同步第一个语言的栏目到新语言里
     *
     * @param string $mark 新增语言
     * @param string $copy_lang 复制语言
     */
    private function syn_arctype($mark = '', $copy_lang = 'cn')
    {
        $arctype_db = Db::name('arctype');

        /*删除新增语言之前的多余数据*/
        $count = $arctype_db->where('lang',$mark)->count();
        if (!empty($count)) {
            $arctype_db->where("lang",$mark)->delete();
        }
        /*--end*/

        $bindArctypeArr = []; // 源栏目ID与目标栏目ID的对应数组
        $arctypeLogic = new \app\common\logic\ArctypeLogic;
        $arctypeList = $arctypeLogic->arctype_list(0, 0, false, 0, ['lang'=>$copy_lang]);

        if (empty($mark) || empty($arctypeList)) {
            return -1;
        }

        /*复制产品属性表数据*/
        $bindProductAttributeArr = []; // 源产品属性ID与目标产品属性ID的对应数组
        $product_attribute_db = Db::name('product_attribute');
        $productAttributeRow = $product_attribute_db->where('lang',$copy_lang)
            ->order('attr_id asc')
            ->select();
        $productAttributeRow = group_same_key($productAttributeRow, 'typeid');
        /*--end*/

        /*复制留言属性表数据*/
        $bindgbookAttributeArr = []; // 源留言属性ID与目标留言属性ID的对应数组
        $guestbook_attribute_db = Db::name('guestbook_attribute');
        $gbookAttributeRow = $guestbook_attribute_db->where('lang',$copy_lang)
            ->order('attr_id asc')
            ->select();
        $gbookAttributeRow = group_same_key($gbookAttributeRow, 'typeid');
        /*--end*/

        /*复制栏目表数据*/
        $arctype_M = model('Arctype');
        foreach ($arctypeList as $key => $val) {
            $data = $val;
            unset($data['id']);
            $data['lang'] = $mark;
            $data['typename'] = $mark.$data['typename']; // 临时测试
            $data['parent_id'] = !empty($bindArctypeArr[$val['parent_id']]) ? $bindArctypeArr[$val['parent_id']] : 0;
            $typeid = $arctype_M->addData($data);
            if (empty($typeid)) {
                return false; // 同步失败
            }
            $bindArctypeArr[$val['id']] = $typeid;
            /*复制产品属性表数据*/
            if (!empty($productAttributeRow[$val['id']])) {
                foreach ($productAttributeRow[$val['id']] as $k2 => $v2) {
                    $proArr = $v2;
                    $proArr['typeid'] = $typeid;
                    $proArr['lang'] = $mark;
                    unset($proArr['attr_id']);
                    $proArr['attr_name'] = $mark.$proArr['attr_name']; // 临时测试
                    $new_attr_id = $product_attribute_db->insertGetId($proArr);
                    if (empty($new_attr_id)) {
                        return false; // 同步失败
                    }
                    $bindProductAttributeArr[$v2['attr_id']] = $new_attr_id;
                }
            }
            /*--end*/
            /*复制留言属性表数据*/
            if (!empty($gbookAttributeRow[$val['id']])) {
                foreach ($gbookAttributeRow[$val['id']] as $k2 => $v2) {
                    $gbArr = $v2;
                    $gbArr['typeid'] = $typeid;
                    $gbArr['lang'] = $mark;
                    unset($gbArr['attr_id']);
                    $gbArr['attr_name'] = $mark.$gbArr['attr_name']; // 临时测试
                    $new_attr_id = $guestbook_attribute_db->insertGetId($gbArr);
                    if (empty($new_attr_id)) {
                        return false; // 同步失败
                    }
                    $bindgbookAttributeArr[$v2['attr_id']] = $new_attr_id;
                }
            }
            /*--end*/
        }
        /*--end*/

        $langAttrData = [];

        /*新增栏目ID与源栏目ID的绑定*/
        foreach ($bindArctypeArr as $key => $val) {
            $langAttrData[] = [
                'attr_name' => 'tid'.$key,
                'attr_value'    => $val,
                'lang'  => $mark,
                'attr_group' => 'arctype',
                'add_time'  => getTime(),
                'update_time'  => getTime(),
            ];
        }
        /*--end*/
        /*新增产品属性ID与源产品属性ID的绑定*/
        foreach ($bindProductAttributeArr as $key => $val) {
            $langAttrData[] = [
                'attr_name' => 'attr_'.$key,
                'attr_value'    => $val,
                'lang'  => $mark,
                'attr_group' => 'product_attribute',
                'add_time'  => getTime(),
                'update_time'  => getTime(),
            ];
        }
        /*--end*/
        /*新增留言属性ID与源留言属性ID的绑定*/
        foreach ($bindgbookAttributeArr as $key => $val) {
            $langAttrData[] = [
                'attr_name' => 'attr_'.$key,
                'attr_value'    => $val,
                'lang'  => $mark,
                'attr_group' => 'guestbook_attribute',
                'add_time'  => getTime(),
                'update_time'  => getTime(),
            ];
        }
        /*--end*/

        // 批量存储
        if (!empty($langAttrData)) {
            $insertObject = model('LanguageAttr')->saveAll($langAttrData);
            $insertNum = count($insertObject);
            if ($insertNum != count($langAttrData)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 创建语言时，同步广告位置以及广告数据，并进行多语言关联绑定
     *
     * @param string $mark 新增语言
     * @param string $copy_lang 复制语言
     */
    private function syn_ad_position($mark = '', $copy_lang = 'cn')
    {
        $ad_position_db = Db::name('ad_position');

        /*删除新增语言之前的多余数据*/
        $count = $ad_position_db->where('lang',$mark)->count();
        if (!empty($count)) {
            $ad_position_db->where("lang",$mark)->delete();
        }
        /*--end*/

        // 广告位置列表
        $bindAdpositionArr = []; // 源广告位置ID与目标广告位置ID的对应数组
        $adpositionList = $ad_position_db->where([
            'lang'=>$copy_lang
            ])->order('id asc')
            ->select();

        if (empty($mark) || empty($adpositionList)) {
            return -1;
        }

        /*复制广告表数据*/
        $bindAdArr = []; // 源广告ID与目标广告ID的对应数组
        $ad_db = Db::name('ad');
        $adRow = $ad_db->where('lang',$copy_lang)
            ->order('id asc')
            ->select();
        $adRow = group_same_key($adRow, 'pid');
        /*--end*/

        /*复制广告位置表数据*/
        foreach ($adpositionList as $key => $val) {
            $data = $val;
            unset($data['id']);
            $data['lang'] = $mark;
            $data['title'] = $mark.$data['title']; // 临时测试
            $pid = $ad_position_db->insertGetId($data);
            if (empty($pid)) {
                return false; // 同步失败
            }
            $bindAdpositionArr[$val['id']] = $pid;
            /*复制广告表数据*/
            if (!empty($adRow[$val['id']])) {
                foreach ($adRow[$val['id']] as $k2 => $v2) {
                    $adArr = $v2;
                    $adArr['pid'] = $pid;
                    $adArr['lang'] = $mark;
                    unset($adArr['id']);
                    $adArr['title'] = $mark.$adArr['title']; // 临时测试
                    $new_ad_id = $ad_db->insertGetId($adArr);
                    if (empty($new_ad_id)) {
                        return false; // 同步失败
                    }
                    $bindAdArr[$v2['id']] = $new_ad_id;
                }
            }
            /*--end*/
        }
        /*--end*/

        $langAttrData = [];

        /*新增广告位置ID与源广告位置ID的绑定*/
        foreach ($bindAdpositionArr as $key => $val) {
            $langAttrData[] = [
                'attr_name' => 'adp'.$key,
                'attr_value'    => $val,
                'lang'  => $mark,
                'attr_group' => 'ad_position',
                'add_time'  => getTime(),
                'update_time'  => getTime(),
            ];
        }
        /*--end*/
        /*新增广告ID与源广告ID的绑定*/
        foreach ($bindAdArr as $key => $val) {
            $langAttrData[] = [
                'attr_name' => 'ad'.$key,
                'attr_value'    => $val,
                'lang'  => $mark,
                'attr_group' => 'ad',
                'add_time'  => getTime(),
                'update_time'  => getTime(),
            ];
        }
        /*--end*/

        // 批量存储
        if (!empty($langAttrData)) {
            $insertObject = model('LanguageAttr')->saveAll($langAttrData);
            $insertNum = count($insertObject);
            if ($insertNum != count($langAttrData)) {
                return false;
            }
        }

        return true;
    }
}