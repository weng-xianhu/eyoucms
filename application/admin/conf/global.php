<?php
/**
 * 配置常量
 */
$other_rele_menu = [];   //限制不显示的内容
$main_lang = get_main_lang();
$admin_lang = get_admin_lang();
$globalConfig = tpCache('global');
//其他语言，限制入口
if ($main_lang != $admin_lang) {
    $other_rele_menu = [1004,2004008,2004009,2004010,2004013,2004001,2004002,2004007,2004004,2004015,2004003,2004019,2004017,2004021,2008,2008001
        ,2008002,2008003,2008008,2008004,2008005,2003,2004016,2003001,2003002,2005,2006];
}
//非静态模式下，不显示 2004016 HTML生成
$seo_pseudo = empty($globalConfig['seo_pseudo']) ? 0 : $globalConfig['seo_pseudo'];
if (empty($seo_pseudo) || $seo_pseudo != 2){
    $other_rele_menu[] = 2004016;
}
//关闭回收站，不显示回收站入口
$web_recycle_switch = empty($globalConfig['web_recycle_switch']) ? 0 : $globalConfig['web_recycle_switch'];
if (!empty($web_recycle_switch) && $web_recycle_switch == 1){
    $other_rele_menu[] = 2004006;
}
//关闭插件功能，全部插件入口屏蔽
$web_weapp_switch = empty($globalConfig['web_weapp_switch']) ? 0 : $globalConfig['web_weapp_switch'];
$module_rele_menu_web_weapp_switch = [2005];
if (1 != $web_weapp_switch){
    $menu_id_arr = M("admin_menu")->where(['controller_name'=>'Weapp'])->getField("menu_id",true);
    if (!empty($menu_id_arr)){
        $module_rele_menu_web_weapp_switch = array_merge($module_rele_menu_web_weapp_switch,$menu_id_arr);
    }
}
//2006004 - 文章订单 --  会员中心与开启文章付费都开启才出来
//$channeltype_list  = M("channeltype")->where(['id'=>['in',[1,5]]])->getField("id,data,status");
//if (!empty($channeltype_list[1]['data'])){
//    $data = json_decode($channeltype_list[1]['data'], true);
//    if (empty($data['is_article_pay'])){
//        $other_rele_menu[] = 2006004;
//    }
//}else{
//    $other_rele_menu[] = 2006004;
//}
//"视频订单"  2006002  会员中心与视频模型启用才出来
//if (empty($channeltype_list[5]['status'])){
//    $other_rele_menu[] = 2006002;
//}

// 商城中心关联
$shop_open_com = [2008,2008001,2008004,2008003,2008002,2008005,2008006,2008008];

return array(
    'module_rele_menu' => [    //模块开关控制入口导航,模块开启后，里面关联的开关才会打开
        'web_users_switch' => [2006],  //会员中心
//        'level_member_upgrade' => [],    //会员升级
        'shop_open' =>  array_merge($shop_open_com, [2004021]),    //开启商城中心
        'shop_open-close' =>  $shop_open_com,    //关闭商城中心
        'web_weapp_switch' => $module_rele_menu_web_weapp_switch,     //插件应用
        'pay_open' => [2004021],  //支付功能
        'web_citysite_open' => [2004019],  //多城市站点
    ],
    'other_rele_menu' => $other_rele_menu,  //其他因素影响入口导航
    'module_default_menu' => [     //模块默认关联入口（打开模块，无论原来的入口是否打开，都需要进入到打开状态）
        'web_users_switch' => [2006],   //会员中心
        'shop_open' =>  [2008], //[2008001,2008002,2008003,2008004,2008005,2008008],    //商城中心
        'web_weapp_switch' => [2005],   //插件应用
        'pay_open' => [2004021],  //支付功能
    ],
    'module_reverse_menu' => [   //模块开关控制入口导航,模块关闭时，里面关联的开关才会打开（与module_rele_menu相反）
        'web_citysite_open' => [2004016],  //多城市站点
    ]
);
