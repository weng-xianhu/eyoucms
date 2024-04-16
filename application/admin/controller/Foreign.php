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
use app\admin\logic\ForeignLogic;

class Foreign extends Base {

    /**
     * 实例化模型
     */
    private $logic;

    /**
     * 构造方法
     */
    public function __construct() {
        parent::__construct();
        $this->logic = new ForeignLogic;
        $functionLogic = new \app\common\logic\FunctionLogic;
        $foreign_authorize = tpSetting('foreign.foreign_authorize', [], 'cn');
        if (!empty($foreign_authorize)) {
            $functionLogic->validate_authorfile(1);
        } else {
            $functionLogic->validate_authorfile(2);
        }
    }

    /**
     * 基本设置
     * @return [type] [description]
     */
    public function index()
    {
        $assign_data = [];
        // 基本设置
        $foreignData = tpSetting('foreign', [], 'cn');
        // 外贸助手插件的配置同步到内置里
        $admin_logic_1693909371 = tpSetting('syn.admin_logic_1693909371', [], 'cn');
        if (empty($admin_logic_1693909371)) {
            if (is_dir('./weapp/Waimao/')) {
                $row = Db::name('weapp')->where(['code'=>'Waimao'])->find();
                $row['data'] = !empty($$row['data']) ? json_decode($$row['data'], true) : [];
                $foreignData['foreign_is_status'] = (!empty($row['status']) && 1 == $row['status']) ? 1 : 0;
                $foreignData['foreign_clear_htmlfilename'] = empty($row['data']['clear_htmlfilename']) ? 0 : $row['data']['clear_htmlfilename'];
                tpSetting('foreign', $foreignData, 'cn');
            }
            tpSetting('syn', ['admin_logic_1693909371'=>1], 'cn');
        }
        $assign_data['foreignData'] = empty($foreignData) ? [] : $foreignData;
        $this->assign($assign_data);
        return $this->fetch();
    }

    /**
     * 保存基本设置
     * @return [type] [description]
     */
    public function conf_save()
    {
        if (IS_POST) {
            $post = input('post.');
            if (!empty($this->globalConfig['seo_pseudo']) && 1 == $this->globalConfig['seo_pseudo']) {
                $post['seo_titleurl_format'] = 0;
                $post['foreign_clear_htmlfilename'] = 0;
            }
            $seoData = [
                'seo_titleurl_format' => intval($post['seo_titleurl_format']),
            ];
            tpCache('seo', $seoData);

            $basicData = [
                'basic_indexname' => $post['basic_indexname'],
            ];
            tpCache('basic', $basicData);

            // 清空文档的自定义文件名
            if (empty($post['seo_titleurl_format'])) {
                if (!empty($post['foreign_clear_htmlfilename'])) {
                    Db::name('archives')->where(['aid'=>['gt', 0]])->update(['htmlfilename'=>'']);
                }
            }

            // 基本设置
            $foreignData = tpSetting('foreign', [], 'cn');
            $foreignData['foreign_is_status'] = intval($post['foreign_is_status']);
            $foreignData['foreign_clear_htmlfilename'] = intval($post['foreign_clear_htmlfilename']);
            tpSetting('foreign', $foreignData, 'cn');
            // 生成语言包文件
            model('ForeignPack')->updateLangFile();

            // 插件配置
            if (is_dir('./weapp/Waimao/')) {
                $weappData = Db::name('weapp')->where(['code'=>'Waimao'])->value('data');
                $weappData = !empty($weappData) ? json_decode($weappData, true) : [];
                $weappData['clear_htmlfilename'] = intval($post['foreign_clear_htmlfilename']);
                $saveData = [
                    'data' => json_encode($weappData),
                    'update_time' => getTime(),
                ];
                Db::name('weapp')->where(['code'=>'Waimao'])->update($saveData);
            }

            Cache::clear('foreign_pack');
            $this->success("操作成功");
        }
        $this->error("操作失败");
    }

    /**
     * 批量更新文档URL
     */
    // public function htmlfilename_index()
    // {
    //     $foreignData = tpSetting('foreign', [], 'cn');
    //     if (empty($foreignData['foreign_is_status'])) {
    //         $this->error('尚未启用外贸助手功能', url('Foreign/index'));
    //     }
    //     $seo_titleurl_format = empty($this->globalConfig['seo_titleurl_format']) ? 0 : $this->globalConfig['seo_titleurl_format'];
    //     if (empty($seo_titleurl_format)) {
    //         $this->error('文档URL尚未开启外贸格式', url('Foreign/index'));
    //     }

    //     // 基本设置
    //     $assign_data = [];
    //     $foreignData = tpSetting('foreign', [], 'cn');
    //     $assign_data['foreignData'] = empty($foreignData) ? [] : $foreignData;
    //     $this->assign($assign_data);

    //     return $this->fetch();
    // }

    /**
     * 执行 - 批量更新文档URL
     */
    public function htmlfilename_handel()
    {
        @ini_set('memory_limit', '-1');
        function_exists('set_time_limit') && set_time_limit(0);

        if (!empty($this->globalConfig['seo_pseudo']) && 1 == $this->globalConfig['seo_pseudo']) {
            $this->error("动态模式下不支持外贸链接格式");
        }

        if (IS_POST) {
            $foreignData = tpSetting('foreign', [], 'cn');
            if (empty($foreignData['foreign_is_status'])) {
                $this->error('外贸助手已关闭', url('Foreign/index'));
            }
            $seo_titleurl_format = empty($this->globalConfig['seo_titleurl_format']) ? 0 : $this->globalConfig['seo_titleurl_format'];
            if (empty($seo_titleurl_format)) {
                $this->error('请勾选文档URL格式为外贸链接', url('Foreign/index'));
            }

            $foreign_htmlfilename_mode = input('param.foreign_htmlfilename_mode/d');
            if (!empty($foreign_htmlfilename_mode)) {
                Db::name('archives')->where(['aid'=>['gt', 0]])->update(['htmlfilename'=>'']);
                $this->success('', null, ['achievepage'=>0,'allpagetotal'=>0,'pagetotal'=>0]);
            } else {
                $achievepage = input("param.achieve/d", 0); // 已完成文档数
                $data = $this->logic->handelUpdateArticle($foreign_htmlfilename_mode, $achievepage);
                $this->success($data[0], null, $data[1]);
            }
        }

        $foreign_htmlfilename_mode = input('param.foreign_htmlfilename_mode/d');
        $this->assign('foreign_htmlfilename_mode', $foreign_htmlfilename_mode);

        // 批量更新URL的配置
        $foreignData = tpSetting('foreign', [], 'cn');
        $foreignData['foreign_htmlfilename_mode'] = $foreign_htmlfilename_mode;
        tpSetting('foreign', $foreignData, 'cn');

        return $this->fetch();
    }

    /**
     * 清除数据缓存+页面缓存
     * @return [type] [description]
     */
    public function clear_cache_htmlfilename()
    {
        Cache::clear();
        delFile(RUNTIME_PATH);
    }

    /**
     * 语言包变量
     * @return [type] [description]
     */
    public function official_pack_index()
    {
        $foreign_is_status = tpSetting('foreign.foreign_is_status', '', 'cn');
        if (empty($foreign_is_status)) {
            $this->error('先启用外贸助手并保存');
        }

        $type = input('param.type/d', 1);
        $condition = [
            'type' => $type,
            'lang' => 'cn',
        ];
        $count = Db::name('foreign_pack')->where($condition)->count('id');// 查询满足要求的总记录数
        $Page = $pager = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $name_arr = Db::name('foreign_pack')->where($condition)->limit($Page->firstRow.','.$Page->listRows)->column('name');

        $list = [];
        $packList = Db::name('foreign_pack')->field('id,type,name,value,lang')->where(['name'=>['IN', $name_arr], 'type'=>$type])->order('lang asc, id asc')->select();
        foreach ($packList as $key => $val) {
            $list[$val['name']][$val['lang']] = $val;
        }
        
        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pager',$pager);// 赋值分页对象
        $this->assign('type', $type);
        return $this->fetch();
    }

    /**
     * 保存语言包变量
     * @return [type] [description]
     */
    public function official_pack_save()
    {
        if (IS_POST) {
            $post = input('post.');
            $data = empty($post['data']) ? [] : $post['data'];
            $saveData = [];
            foreach ($data as $key => $val) {
                $saveData[] = [
                    'id' => intval($key),
                    'value' => htmlspecialchars_decode($val),
                    'update_time' => getTime(),
                ];
            }
            $r = true;
            if (!empty($saveData)) {
                $r = model('ForeignPack')->saveAll($saveData);
            }
            if ($r !== false) {
                Cache::clear('foreign_pack');
                $this->success("操作成功");
            }
        }
        $this->error("操作失败");
    }
}