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
use think\Request;

/**
 * 模型
 */
class LanguageAttr extends Model
{
    public $language_split = 0;

    //初始化
    protected function initialize()
    {
        // 需要调用`Model`的`initialize`方法
        parent::initialize();
        $this->language_split = tpCache('language.language_split');
    }

    /**
     * 获取关联绑定的变量值
     * @param string|array $bind_value 绑定之前的值，或者绑定之后的值
     * @param string $group 分组
     */
    public function getBindValue($bind_value = '', $attr_group = 'arctype', $langvar = '')
    {
        /*单语言情况下不执行多语言代码*/
        if (!is_language()) {
            return $bind_value;
        }
        /*--end*/

        // 主语言
        $main_lang = get_main_lang();
        // 当前语言
        $lang = $main_lang;
        if ('admin' == request()->module()) {
            $lang = get_admin_lang();
        } else {
            $lang = get_home_lang();
        }

        if (!empty($bind_value) && $main_lang != $lang) {
            switch ($attr_group) {
                case 'arctype':
                    {
                        if (!is_array($bind_value)) { // 获取关联绑定的栏目ID
                            $typeidArr = explode(',', $bind_value);
                            $row = Db::name('language_attr')->field('attr_name')
                                ->where([
                                    'attr_value'    => ['IN', $typeidArr],
                                    'attr_group' => $attr_group,
                                ])->select();
                            if (!empty($row) && empty($this->language_split)) {
                                $row2 = Db::name('language_attr')->field('attr_name,attr_value')
                                    ->where([
                                        'attr_name' => ['IN', get_arr_column($row, 'attr_name')],
                                        'lang'  => $lang,
                                        'attr_group' => $attr_group,
                                    ])->select();
                                if (1 < count($typeidArr)) {
                                    $bind_value = implode(',', get_arr_column($row2, 'attr_value'));
                                } else {
                                    if(empty($row2)) {
                                        $bind_value = empty($this->language_split) ? '' : $bind_value;
                                    } else {
                                        $bind_value = $row2[0]['attr_value'];  
                                    }
                                }
                            }
                        }
                    }
                    break;

                case 'product_attribute':
                    {
                        if (is_array($bind_value)) {
                            !empty($langvar) && $lang = $langvar;
                            $row = Db::name('language_attr')->field('attr_name, attr_value')
                                ->where([
                                    'attr_value'    => ['IN', get_arr_column($bind_value, 'attr_id')],
                                    'attr_group' => $attr_group,
                                ])->getAllWithIndex('attr_value');
                            if (!empty($row) && empty($this->language_split)) {
                                $row2 = Db::name('language_attr')->field('attr_name, attr_value')
                                    ->where([
                                        'attr_name' => ['IN', get_arr_column($row, 'attr_name')],
                                        'lang'  => $lang,
                                        'attr_group' => $attr_group,
                                    ])->getAllWithIndex('attr_name');
                                if (!empty($row2)) {
                                    foreach ($bind_value as $key => $val) {
                                        if (!empty($row[$val['attr_id']])) {
                                            $val['attr_id'] = $row2[$row[$val['attr_id']]['attr_name']]['attr_value'];
                                        }
                                        $bind_value[$key] = $val;
                                    }
                                }
                            }
                        } else { // 获取关联绑定的产品属性ID
                            $attr_name = 'attr_'.$bind_value;
                            $attr_value = Db::name('language_attr')->where([
                                    'attr_name'    => $attr_name,
                                    'lang'  => $lang,
                                    'attr_group' => $attr_group,
                                ])->getField('attr_value');
                            if (empty($this->language_split)) {
                                $bind_value = empty($attr_value) ? '' : $attr_value;
                            } else {
                                // $bind_value = empty($attr_value) ? $bind_value : $attr_value;
                            }
                        }
                    }
                    break;

                case 'guestbook_attribute':
                    {
                        if (is_array($bind_value)) {
                            !empty($langvar) && $lang = $langvar;
                            $row = Db::name('language_attr')->field('attr_name, attr_value')
                                ->where([
                                    'attr_value'    => ['IN', get_arr_column($bind_value, 'attr_id')],
                                    'attr_group' => $attr_group,
                                ])->getAllWithIndex('attr_value');

                            if (!empty($row) && empty($this->language_split)) {
                                $row2 = Db::name('language_attr')->field('attr_name, attr_value')
                                    ->where([
                                        'attr_name' => ['IN', get_arr_column($row, 'attr_name')],
                                        'lang'  => $lang,
                                        'attr_group' => $attr_group,
                                    ])->getAllWithIndex('attr_name');
                                if (!empty($row2)) {
                                    foreach ($bind_value as $key => $val) {
                                        if (!empty($row[$val['attr_id']])) {
                                            $val['attr_id'] = $row2[$row[$val['attr_id']]['attr_name']]['attr_value'];
                                        }
                                        $bind_value[$key] = $val;
                                    }
                                }
                            }
                        } else { // 获取关联绑定的留言属性ID
                            $attr_name = 'attr_'.$bind_value;
                            $attr_value = Db::name('language_attr')->where([
                                    'attr_name'    => $attr_name,
                                    'lang'  => $lang,
                                    'attr_group' => $attr_group,
                                ])->getField('attr_value');
                            if (empty($this->language_split)) {
                                $bind_value = empty($attr_value) ? '' : $attr_value;
                            } else {
                                // $bind_value = empty($attr_value) ? $bind_value : $attr_value;
                            }
                        }
                    }
                    break;

                case 'ad_position':
                    {
                        if (!is_array($bind_value)) {// 获取关联绑定的广告位置ID
                            $attr_name = 'adp'.$bind_value;
                            $attr_value = Db::name('language_attr')->where([
                                    'attr_name'    => $attr_name,
                                    'lang'  => $lang,
                                    'attr_group' => $attr_group,
                                ])->getField('attr_value');
                            if (empty($this->language_split)) {
                                $bind_value = empty($attr_value) ? '' : $attr_value;
                            } else {
                                // $bind_value = empty($attr_value) ? $bind_value : $attr_value;
                            }
                        }
                    }
                    break;

                case 'ad':
                    {
                        if (!is_array($bind_value)) {// 获取关联绑定的广告ID
                            $attr_name = 'ad'.$bind_value;
                            $attr_value = Db::name('language_attr')->where([
                                    'attr_name'    => $attr_name,
                                    'lang'  => $lang,
                                    'attr_group' => $attr_group,
                                ])->getField('attr_value');
                            if (empty($this->language_split)) {
                                $bind_value = empty($attr_value) ? '' : $attr_value;
                            } else {
                                // $bind_value = empty($attr_value) ? $bind_value : $attr_value;
                            }
                        }
                    }
                    break;

                case 'links_group':
                    {
                        if (!is_array($bind_value)) {// 获取关联绑定的广告位置ID
                            $attr_name = 'linksgroup'.$bind_value;
                            $attr_value = Db::name('language_attr')->where([
                                    'attr_name'    => $attr_name,
                                    'lang'  => $lang,
                                    'attr_group' => $attr_group,
                                ])->getField('attr_value');
                            if (empty($this->language_split)) {
                                $bind_value = empty($attr_value) ? '' : $attr_value;
                            } else {
                                // $bind_value = empty($attr_value) ? $bind_value : $attr_value;
                            }
                        }
                    }
                    break;

                case 'form':
                    {
                        if (!is_array($bind_value)) {// 获取关联绑定的表单ID
                            $attr_name = 'form'.$bind_value;
                            $attr_value = Db::name('language_attr')->where([
                                    'attr_name'    => $attr_name,
                                    'lang'  => $lang,
                                    'attr_group' => $attr_group,
                                ])->getField('attr_value');
                            if (empty($this->language_split)) {
                                $bind_value = empty($attr_value) ? '' : $attr_value;
                            } else {
                                // $bind_value = empty($attr_value) ? $bind_value : $attr_value;
                            }
                        }
                    }
                    break;

                case 'form_attribute':
                    {
                        if (is_array($bind_value)) {
                            !empty($langvar) && $lang = $langvar;
                            $row = Db::name('language_attr')->field('attr_name, attr_value')
                                ->where([
                                    'attr_value'    => ['IN', get_arr_column($bind_value, 'attr_id')],
                                    'attr_group' => $attr_group,
                                ])->getAllWithIndex('attr_value');

                            if (!empty($row) && empty($this->language_split)) {
                                $row2 = Db::name('language_attr')->field('attr_name, attr_value')
                                    ->where([
                                        'attr_name' => ['IN', get_arr_column($row, 'attr_name')],
                                        'lang'  => $lang,
                                        'attr_group' => $attr_group,
                                    ])->getAllWithIndex('attr_name');
                                if (!empty($row2)) {
                                    foreach ($bind_value as $key => $val) {
                                        if (!empty($row[$val['attr_id']])) {
                                            $val['attr_id'] = $row2[$row[$val['attr_id']]['attr_name']]['attr_value'];
                                        }
                                        $bind_value[$key] = $val;
                                    }
                                }
                            }
                        } else { // 获取关联绑定的表单属性ID
                            $attr_name = 'attr_'.$bind_value;
                            $attr_value = Db::name('language_attr')->where([
                                    'attr_name'    => $attr_name,
                                    'lang'  => $lang,
                                    'attr_group' => $attr_group,
                                ])->getField('attr_value');
                            if (empty($this->language_split)) {
                                $bind_value = empty($attr_value) ? '' : $attr_value;
                            } else {
                                // $bind_value = empty($attr_value) ? $bind_value : $attr_value;
                            }
                        }
                    }
                    break;
                
                default:
                    # code...
                    break;
            }
        }

        return $bind_value;
    }

    /**
     * 获取关联绑定的主语言的变量值
     * @param string|array $bind_value 绑定之前的值，或者绑定之后的值
     * @param string $group 分组
     */
    public function getBindMainValue($bind_value = '', $attr_group = 'arctype')
    {
        /*单语言情况下不执行多语言代码*/
        if (!is_language()) {
            return $bind_value;
        }
        /*--end*/

        $main_lang = get_main_lang();

        if (!empty($bind_value)) {
            switch ($attr_group) {
                case 'ad':
                    {
                        if (!is_array($bind_value)) {// 获取关联绑定的广告ID
                            $attr_name = Db::name('language_attr')->where([
                                    'attr_value'    => $bind_value,
                                    'attr_group'    => $attr_group,
                                ])->getField('attr_name');
                            $attr_value = Db::name('language_attr')->where([
                                    'attr_name'    => $attr_name,
                                    'lang'  => $main_lang,
                                    'attr_group' => $attr_group,
                                ])->getField('attr_value');
                            !empty($attr_value) && $bind_value = $attr_value;
                        }
                    }
                    break;
                
                default:
                    # code...
                    break;
            }
        }

        return $bind_value;
    }
}