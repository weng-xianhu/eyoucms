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
 * 留言属性
 */
class GuestbookAttribute extends Model
{
    public $admin_lang = 'cn';
    public $main_lang = 'cn';

    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        $this->admin_lang = get_admin_lang();
        $this->main_lang  = get_main_lang();
    }

    /**
     * 同步新增留言属性ID到多语言的模板变量里
     */
    public function syn_add_language_attribute($attr_id)
    {
        /*单语言情况下不执行多语言代码*/
        if (!is_language() || tpCache('language.language_split')) {
            return true;
        }
        /*--end*/

        $attr_group  = 'guestbook_attribute';
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
                            'attr_name'  => 'tid' . $result['typeid'],
                            'attr_group' => 'arctype',
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

    /**
     * 验证后台列表显示 - 是否已超过4个
     */
    public function isValidate($id_name = '', $id_value = '', $field = '', $value = '')
    {
        $return = true;
        $value  = trim($value);
        $where  = [
            $id_name => $id_value,
            'lang'   => $this->admin_lang,
        ];
        if ($value == 1 && $field == 'is_showlist') {
            $typeid          = Db::name('guestbook_attribute')->where($where)->getField('typeid');
            $where['typeid'] = $typeid;
            $count           = Db::name('guestbook_attribute')->where([
                    'typeid'    => $typeid,
                    'is_showlist'   => 1,
                    'is_del'   => 0,
                    'lang'   => $this->admin_lang,
                ])->count();
            if ($count >= 4) {
                $return = [
                    'time'=>1,
                    'msg' => '所属栏目的列表字段显示数量已达4个',
                ];
                return $return;
            }
        }
        //更新数据库
        Db::name('guestbook_attribute')->where($where)->update([
            'is_showlist'   => $value,
            'update_time'   => getTime(),
        ]);

        return $return;
    }
}