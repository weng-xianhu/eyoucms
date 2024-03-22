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
 * Date: 2023-3-8
 */

namespace app\admin\logic;

use think\Model;
use think\Db;

class FormLogic extends Model
{
    public $form_db;
    public $main_lang = 'cn';
    public $admin_lang = 'cn';

    /**
     * 初始化操作
     */
    public function initialize() {
        parent::initialize();
        $this->form_db = Db::name('form');
        $this->main_lang = get_main_lang();
        $this->admin_lang = get_admin_lang();
    }

    /**
     * 同步新增表单ID到多语言的模板变量里
     */
    public function syn_add_language_form($form_id)
    {
        /*单语言情况下不执行多语言代码*/
        if (!is_language() || tpCache('language.language_split')) {
            return true;
        }
        /*--end*/

        $attr_group = 'form';
        $languageRow = Db::name('language')->field('mark')->order('id asc')->select();
        if (!empty($languageRow) && $this->admin_lang == $this->main_lang) { // 当前语言是主体语言，即语言列表最早新增的语言
            $result = $this->form_db->find($form_id);
            $attr_name = 'form'.$form_id;
            $r = Db::name('language_attribute')->save([
                'attr_title'    => $result['form_name'],
                'attr_name'     => $attr_name,
                'attr_group'    => $attr_group,
                'add_time'      => getTime(),
                'update_time'   => getTime(),
            ]);
            if (false !== $r) {
                $data = [];
                foreach ($languageRow as $key => $val) {
                    /*同步新分组到其他语言分组列表*/
                    if ($val['mark'] != $this->admin_lang) {
                        $addsaveData = $result;
                        $addsaveData['lang']  = $val['mark'];
                        $addsaveData['form_name'] = $val['mark'].$addsaveData['form_name'];
                        unset($addsaveData['form_id']);
                        $form_id = $this->form_db->insertGetId($addsaveData);
                    }
                    /*--end*/
                    
                    /*所有语言绑定在主语言的ID容器里*/
                    $data[] = [
                        'attr_name' => $attr_name,
                        'attr_value'    => $form_id,
                        'lang'  => $val['mark'],
                        'attr_group'    => $attr_group,
                        'add_time'      => getTime(),
                        'update_time'   => getTime(),
                    ];
                    /*--end*/
                }
                if (!empty($data)) {
                    model('LanguageAttr')->saveAll($data);
                }
            }
        }
    }

    /**
     * 同步新增表单属性ID到多语言的模板变量里
     */
    public function syn_add_language_attribute($attr_id)
    {
        /*单语言情况下不执行多语言代码*/
        if (!is_language() || tpCache('language.language_split')) {
            return true;
        }
        /*--end*/

        $attr_group  = 'form_attribute';
        $languageRow = Db::name('language')->field('mark')->order('id asc')->select();
        if (!empty($languageRow) && $this->admin_lang == $this->main_lang) { // 当前语言是主体语言，即语言列表最早新增的语言
            $result    = Db::name('guestbook_attribute')->find($attr_id);
            $attr_name = 'attr_' . $attr_id;
            $r         = Db::name('language_attribute')->save([
                'attr_title'  => $result['attr_name'],
                'attr_name'   => $attr_name,
                'attr_group'  => $attr_group,
                'add_time'    => getTime(),
                'update_time' => getTime(),
            ]);
            if (false !== $r) {
                $data = [];
                foreach ($languageRow as $key => $val) {
                    /*同步新留言属性到其他语言留言属性列表*/
                    if ($val['mark'] != $this->admin_lang) {
                        $addsaveData           = $result;
                        $addsaveData['lang']   = $val['mark'];
                        $newTypeid             = Db::name('language_attr')->where([
                            'attr_name'  => 'form' . $result['typeid'],
                            'attr_group' => 'form',
                            'lang'       => $val['mark'],
                        ])->getField('attr_value');
                        $addsaveData['typeid'] = $newTypeid;
                        unset($addsaveData['attr_id']);
                        $attr_id = Db::name('guestbook_attribute')->insertGetId($addsaveData);
                    }
                    /*--end*/

                    /*所有语言绑定在主语言的ID容器里*/
                    $data[] = [
                        'attr_name'   => $attr_name,
                        'attr_value'  => $attr_id,
                        'lang'        => $val['mark'],
                        'attr_group'  => $attr_group,
                        'add_time'    => getTime(),
                        'update_time' => getTime(),
                    ];
                    /*--end*/
                }
                if (!empty($data)) {
                    model('LanguageAttr')->saveAll($data);
                }
            }
        }
    }
}