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

$cacheKey = "extra_global_channeltype";
$channeltype_row = \think\Cache::get($cacheKey);
if (empty($channeltype_row)) {
    $channeltype_row = \think\Db::name('channeltype')->field('id,nid')
        ->where([
            'status' => 1,
        ])
        ->order('id asc')
        ->select();
    \think\Cache::set($cacheKey, $channeltype_row, EYOUCMS_CACHE_TIME, "channeltype");
}

$channeltype_list = [];
$allow_release_channel = [];
foreach ($channeltype_row as $key => $val) {
    $channeltype_list[$val['nid']] = $val['id'];
    if (!in_array($val['nid'], ['guestbook','single'])) {
        array_push($allow_release_channel, $val['id']);
    }
}

return array(
    // CMS根目录文件夹
    'wwwroot_dir' => ['application','core','data','extend','html','public','template','uploads','vendor','weapp'],
    // 禁用的目录名称
    'disable_dirname' => ['application','core','data','extend','html','install','public','plugins','uploads','template','vendor','weapp','tags','search','user','users','member','reg','centre','login'],
    // 发送邮箱默认有效时间，会员中心，邮箱验证时用到
    'email_default_time_out' => 3600,
    // 邮箱发送倒计时 2分钟
    'email_send_time' => 120,
    // 充值订单默认有效时间，会员中心用到，2小时
    'get_order_validity' => 7200,
    // 支付订单默认有效时间，商城中心用到，2小时
    'get_shop_order_validity' => 7200,
    // 文档SEO描述截取长度，一个字符表示一个汉字或字母
    'arc_seo_description_length' => 125,
    // 栏目最多级别
    'arctype_max_level' => 3,
    // 模型标识
    'channeltype_list' => $channeltype_list,
    // 发布文档的模型ID
    'allow_release_channel' => $allow_release_channel,
    // 广告类型
    'ad_media_type' => array(
        1   => '图片',
        // 2   => 'flash',
        // 3   => '文字',
    ),
    'attr_input_type_arr' => array(
        0   => '单行文本',
        1   => '下拉框',
        2   => '多行文本',
        3   => 'HTML文本',
    ),
    // 栏目自定义字段的channel_id值
    'arctype_channel_id' => -99,
    // 栏目表原始字段
    'arctype_table_fields' => array('id','channeltype','current_channel','parent_id','typename','dirname','dirpath','englist_name','grade','typelink','litpic','templist','tempview','seo_title','seo_keywords','seo_description','sort_order','is_hidden','is_part','admin_id','is_del','del_method','status','lang','add_time','update_time'),
    // 网络图片扩展名
    'image_ext' => 'jpg,jpeg,gif,bmp,ico,png,webp',
    // 后台语言Cookie变量
    'admin_lang' => 'admin_lang',
    // 前台语言Cookie变量
    'home_lang' => 'home_lang',
    // URL全局参数（比如：可视化uiset、多模板v、多语言lang）
    'parse_url_param'   => ['uiset','v','lang'],
    // 用户金额明细类型
    'pay_cause_type_arr' => array(
        0   => '消费',
        1   => '账户充值',
        // 2   => '后续添加',
    ),
    // 充值状态
    'pay_status_arr' => array(
        // 0   => '失败',
        1   => '未付款',
        // 2   => '已付款',
        3   => '已充值',
        4   => '订单取消',
        // 5   => '后续添加',
    ),
    // 支付方式
    'pay_method_arr' => array(
        'wechat'     => '微信',
        'alipay'     => '支付宝',
        'artificial' => '手工充值',
        'balance'    => '余额',
        'admin_pay'  => '管理员代付',
        'delivery_pay' => '货到付款',
    ),
    // 缩略图默认宽高度
    'thumb' => [
        'open'  => 0,
        'mode'  => 2,
        'color' => '#FFFFFF',
        'width' => 300,
        'height' => 300,
    ],
    // 订单状态
    'order_status_arr' => array(
        -1  => '已关闭',
        0   => '待付款',
        1   => '待发货',
        2   => '待收货',
        3   => '订单完成',
        4   => '订单过期',
        // 5   => '后续添加',
    ),
    // 订单状态，后台使用
    'admin_order_status_arr' => array(
        -1  => '订单关闭',
        0   => '未付款',
        1   => '待发货',
        2   => '已发货',
        3   => '已完成',
        4   => '订单过期',
    ),
    // 清理文件时，需要查询的数据表和字段
    'get_tablearray' => array(
        0 => array(
            'table' => 'ad',
            'field' => 'litpic',
        ),
        1 => array(
            'table' => 'archives',
            'field' => 'litpic',
        ),
        2 => array(
            'table' => 'arctype',
            'field' => 'litpic',
        ),
        3 => array(
            'table' => 'images_upload',
            'field' => 'image_url',
        ),
        4 => array(
            'table' => 'links',
            'field' => 'logo',
        ),
        5 => array(
            'table' => 'product_img',
            'field' => 'image_url',
        ),
        6 => array(
            'table' => 'ad',
            'field' => 'intro',
        ),
        7 => array(
            'table' => 'article_content',
            'field' => 'content',
        ),
        8 => array(
            'table' => 'download_content',
            'field' => 'content',
        ),
        9 => array(
            'table' => 'images_content',
            'field' => 'content',
        ),
        10 => array(
            'table' => 'product_content',
            'field' => 'content',
        ),
        11 => array(
            'table' => 'single_content',
            'field' => 'content',
        ),
        12 => array(
            'table' => 'config',
            'field' => 'value',
        ),
        13 => array(
            'table' => 'ui_config',
            'field' => 'value',
        ),
        14 => array(
            'table' => 'download_file',
            'field' => 'file_url',
        ),
        15 => array(
            'table' => 'users',
            'field' => 'head_pic',
        ),
        16 => array(
            'table' => 'shop_order_details',
            'field' => 'litpic',
        ),
        // 后续可持续添加数据表和字段，格式参照以上
    ),
);
