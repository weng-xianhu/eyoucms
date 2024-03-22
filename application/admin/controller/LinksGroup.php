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

namespace app\admin\controller;

use think\Db;
use think\Page;
use think\Cache;

class LinksGroup extends Base
{
    public function index()
    {
        $list = array();
        $keywords = input('keywords/s');

        $condition = array();
        if (!empty($keywords)) {
            $condition['group_name'] = array('LIKE', "%{$keywords}%");
        }
        $condition['lang'] = array('eq', $this->admin_lang);

        $linksgroupsM =  Db::name('links_group');
        $count = $linksgroupsM->where($condition)->count('id');// 查询满足要求的总记录数
        $Page = $pager = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $linksgroupsM->where($condition)->order('sort_order asc, id asc')->limit($Page->firstRow.','.$Page->listRows)->select();
        $min_group_id = 0;
        foreach ($list as $key => $val) {
            if ($min_group_id > $val['id'] || empty($min_group_id)) {
                $min_group_id = $val['id'];
            }
        }
        $this->assign('min_group_id',$min_group_id);

        /*多语言模式下，分组ID显示主体语言的ID*/
        $main_group_list = [];
        if ($this->admin_lang != $this->main_lang && empty($this->globalConfig['language_split'])) {
            $attr_values = get_arr_column($list, 'id');
            $languageAttrRow = Db::name('language_attr')->field('attr_name,attr_value')->where([
                    'attr_value'    => ['IN', $attr_values],
                    'attr_group'    => 'links_group',
                    'lang'          => $this->admin_lang,
                ])->getAllWithIndex('attr_value');
            $groupids = [];
            foreach ($languageAttrRow as $key => $val) {
                $gid_tmp = str_replace('linksgroup', '', $val['attr_name']);
                array_push($groupids, intval($gid_tmp));
            }
            $main_GroupRow = Db::name('links_group')->field("id,CONCAT('linksgroup', id) AS attr_name")
                ->where([
                    'id'    => ['IN', $groupids],
                    'lang'  => $this->main_lang,
                ])->getAllWithIndex('attr_name');
            foreach ($list as $key => $val) {
                $key_tmp = !empty($languageAttrRow[$val['id']]['attr_name']) ? $languageAttrRow[$val['id']]['attr_name'] : '';
                $main_group_list[$val['id']] = [
                    'id'        => !empty($main_GroupRow[$key_tmp]['id']) ? $main_GroupRow[$key_tmp]['id'] : 0,
                ];
            }
        }
        $this->assign('main_group_list', $main_group_list);
        /*end*/

        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pager',$pager);// 赋值分页对象
        return $this->fetch();
    }

    /**
     * 保存友情链接分组
     */
    public function linksgroup_save()
    {
        if (IS_AJAX_POST) {
            $post = input('post.');

            if (empty($post['group_name'])) {
                $this->error('至少新增一个链接分组！');
            } else {
                $is_empty = true;
                foreach ($post['group_name'] as $key => $val) {
                    $val = trim($val);
                    if (!empty($val)) {
                        $is_empty = false;
                        break;
                    }
                }
                if (true === $is_empty) {
                    $this->error('分组名称不能为空！');
                }
            }

            // 数据拼装
            $now_time = getTime();
            $addData = $editData = [];
            foreach ($post['group_name'] as $key => $val) {
                $group_name  = trim($val);
                if (!empty($group_name)) {
                    if (empty($post['id'][$key])) {
                        if ($this->admin_lang == $this->main_lang || !empty($this->globalConfig['language_split'])) {
                            $addData[] = [
                                'group_name' => $group_name,
                                'sort_order' => $post['sort_order'][$key] ? :100,
                                'lang' => $this->admin_lang,
                                'add_time' => $now_time,
                                'update_time' => $now_time,
                            ];
                        }
                    } else {
                        $id = intval($post['id'][$key]);
                        $editData[] = [
                            'id' => $id,
                            'group_name' => $group_name,
                            'sort_order' => $post['sort_order'][$key] ? :100,
                            'lang' => $this->admin_lang,
                            'update_time' => $now_time,
                        ];
                    }
                }
            }

            if (!empty($addData)) {
                $rdata = model('LinksGroup')->saveAll($addData);
                /*多语言*/
                if (is_language()) {
                    foreach ($rdata as $k1 => $v1) {
                        $attr_data = $v1->getData();
                        // 同步分组ID到多语言的模板变量里，添加多语言分组位
                        $this->syn_add_language_linksgroup($attr_data['id']);
                    }
                }
                /*end*/
            }

            $r = true;
            if (!empty($editData)) {
                $r = model('LinksGroup')->saveAll($editData);
            }

            if ($r !== false) {
                Cache::clear('links');
                Cache::clear('links_group');
                adminLog('保存链接分组：'.implode(',', $post['group_name']));
                $this->success('操作成功');
            }
        }
        $this->error('操作失败');
    }

    /**
     * 同步新增分组ID到多语言的模板变量里
     */
    private function syn_add_language_linksgroup($group_id)
    {
        /*单语言情况下不执行多语言代码*/
        if (!is_language() || tpCache('language.language_split')) {
            return true;
        }
        /*--end*/

        $attr_group = 'links_group';
        $languageRow = Db::name('language')->field('mark')->order('id asc')->select();
        if (!empty($languageRow) && $this->admin_lang == $this->main_lang) { // 当前语言是主体语言，即语言列表最早新增的语言
            $links_group_db = Db::name('links_group');
            $result = $links_group_db->find($group_id);
            $attr_name = 'linksgroup'.$group_id;
            $r = Db::name('language_attribute')->save([
                'attr_title'    => $result['group_name'],
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
                        $addsaveData['group_name'] = $val['mark'].$addsaveData['group_name'];
                        unset($addsaveData['id']);
                        $group_id = $links_group_db->insertGetId($addsaveData);
                    }
                    /*--end*/
                    
                    /*所有语言绑定在主语言的ID容器里*/
                    $data[] = [
                        'attr_name' => $attr_name,
                        'attr_value'    => $group_id,
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
     * 删除友情链接分组
     */
    public function del()
    {
        if (is_language() && empty($this->globalConfig['language_split'])) {
            $this->language_access(); // 多语言功能操作权限
        }

        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if(IS_POST && !empty($id_arr)){
            $linksGroupRow = Db::name('links_group')->where(['lang'=>$this->admin_lang])->order('id asc')->column('id');
            if (!empty($linksGroupRow)) {
                if (1 < count($linksGroupRow)) {
                    $firstRow = current($linksGroupRow);
                    $min_group_id = empty($firstRow['id']) ? 0 : $firstRow['id'];
                    if (in_array($min_group_id, $id_arr)) {
                        $this->error('禁止删除默认分组');
                    }
                } else {
                    $this->error('至少保留一个分组');
                }
            }

            /*多语言*/
            $attr_name_arr = [];
            foreach ($id_arr as $key => $val) {
                $attr_name_arr[] = 'linksgroup'.$val;
            }
            if (is_language() && empty($this->globalConfig['language_split'])) {
                $new_id_arr = Db::name('language_attr')->where([
                        'attr_name' => ['IN', $attr_name_arr],
                        'attr_group'    => 'links_group',
                    ])->column('attr_value');
                !empty($new_id_arr) && $id_arr = $new_id_arr;
            }
            /*--end*/

            $group_name_list = Db::name('links_group')->where([
                    'id'    => ['IN', $id_arr],
                ])->column('group_name');

            $r = Db::name('links_group')->where([
                    'id'    => ['IN', $id_arr],
                ])->delete();
            if ($r !== false) {

                /*删除多语言友情链接分组关联绑定*/
                if (!empty($attr_name_arr)) {
                    if (get_admin_lang() == get_main_lang()) {
                        Db::name('language_attribute')->where([
                                'attr_name' => ['IN', $attr_name_arr],
                                'attr_group'    => 'links_group',
                            ])->delete();
                    }
                    if (empty($this->globalConfig['language_split'])) {
                        Db::name('language_attr')->where([
                                'attr_name' => ['IN', $attr_name_arr],
                                'attr_group'    => 'links_group',
                            ])->delete();
                    } else {
                        Db::name('language_attr')->where([
                                'attr_value' => ['IN', $id_arr],
                                'attr_group'    => 'links_group',
                            ])->delete();
                    }
                }
                /*--end*/

                Db::name('links')->where([
                    'groupid'    => ['IN', $id_arr],
                ])->delete();

                Cache::clear('links');
                Cache::clear('links_group');
                adminLog('删除友情链接分组：'.implode(',', $group_name_list));
                $this->success('删除成功');
            }
        }
        $this->error('删除失败');
    }
}