<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海口快推科技有限公司，并保留所有权利。
 * 网站地址: http://www.eyoucms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 陈风任 <491085389@qq.com>
 * Date: 2020-05-25
 */

namespace think\template\taglib\eyou;

use think\Config;
use think\Request;
use think\Db;

/**
 * 专题文章节点列表
 */
load_trait('controller/Jump');
class TagSpecnodelist extends Base
{ 
    use \traits\controller\Jump;

    /**
     * 会员ID
     */
    public $users_id = 0;
    public $users    = [];
    public $usersTplVersion    = '';
    
    //初始化
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 获取提交订单数据
     */
    public function getSpecnodelist()
    {
        $aid = input('param.aid/d', 0);
        $aid = !empty($aid) ? intval($aid) : $this->aid;
        if (empty($aid)) {
            echo '标签 specnodelist 报错：页面URL缺少属性 aid 值，请填写专题文档ID。';
            return false;
        }

        // 查询专题文章节点列表
        $where = [
            'aid'    => $aid,
            'status' => 1,
            'is_del' => 0,
            'lang'   => self::$home_lang,
        ];
        $result = Db::name('special_node')->where($where)->order('sort_order asc, node_id desc')->select();

        return $result;
    }
}