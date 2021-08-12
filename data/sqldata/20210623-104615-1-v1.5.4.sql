-- ----------------------------------------
-- EyouCms MySQL Data Transfer 
-- 
-- Server         : 127.0.0.1_3306
-- Server Version : 5.7.26
-- Host           : 127.0.0.1:3306
-- Database       : e5
-- 
-- Part : #1
-- Version : #v1.5.4
-- Date : 2021-06-23 10:46:15
-- -----------------------------------------

SET FOREIGN_KEY_CHECKS = 0;


-- -----------------------------
-- Table structure for `ey_ad`
-- -----------------------------
DROP TABLE IF EXISTS `ey_ad`;
CREATE TABLE `ey_ad` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告id',
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '广告位置ID',
  `media_type` tinyint(1) DEFAULT '0' COMMENT '广告类型',
  `title` varchar(60) DEFAULT '' COMMENT '广告名称',
  `links` varchar(255) DEFAULT '' COMMENT '广告链接',
  `litpic` varchar(255) DEFAULT '' COMMENT '图片地址',
  `start_time` int(11) DEFAULT '0' COMMENT '投放时间',
  `end_time` int(11) DEFAULT '0' COMMENT '结束时间',
  `intro` text COMMENT '描述',
  `link_man` varchar(60) DEFAULT '' COMMENT '添加人',
  `link_email` varchar(60) DEFAULT '' COMMENT '添加人邮箱',
  `link_phone` varchar(60) DEFAULT '' COMMENT '添加人联系电话',
  `click` int(11) DEFAULT '0' COMMENT '点击量',
  `bgcolor` varchar(30) DEFAULT '' COMMENT '背景颜色',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '1=显示，0=屏蔽',
  `sort_order` int(11) DEFAULT '0' COMMENT '排序',
  `target` varchar(50) DEFAULT '' COMMENT '是否开启浏览器新窗口',
  `admin_id` int(10) DEFAULT '0' COMMENT '管理员ID',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '伪删除，1=是，0=否',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '多语言',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `position_id` (`pid`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='广告表';

-- -----------------------------
-- Records of `ey_ad`
-- -----------------------------
INSERT INTO `ey_ad` VALUES ('1', '1', '1', '共展蓝图', 'http://www.eyoucms.com', '/uploads/allimg/20190730/84c8489fe403f189c5efede63be93786.jpg', '1524215594', '0', '&lt;p&gt;填写广告的备注信息，方便于后期的跟进&lt;/p&gt;', '', '', '', '0', '', '1', '3', '0', '0', '0', 'cn', '1524215652', '1564477760');
INSERT INTO `ey_ad` VALUES ('2', '1', '1', '易优模板库', 'http://www.eyoucms.com', '/uploads/allimg/20190730/87da15986aaca96134704c7a27154711.jpg', '0', '0', '&lt;p&gt;填写广告的备注信息，方便于后期的跟进&lt;/p&gt;', '', '', '', '0', '', '1', '2', '0', '0', '0', 'cn', '1524214017', '1564477760');
INSERT INTO `ey_ad` VALUES ('5', '1', '1', '第三组广告', 'http://www.eyoucms.com', '/uploads/allimg/20190730/8f5e1882536879c2220c7a5bf1930b96.jpg', '0', '0', '', '', '', '', '0', '', '1', '1', '1', '1', '0', 'cn', '1553046945', '1564477760');
INSERT INTO `ey_ad` VALUES ('7', '3', '1', '', '', '/uploads/allimg/20190722/8735c46928779e1f3057d4859aa22be9.jpg', '0', '0', '', '', '', '', '0', '', '1', '2', '0', '1', '0', 'cn', '1563764411', '1563764739');
INSERT INTO `ey_ad` VALUES ('9', '3', '1', '', '', '/uploads/allimg/20190722/08536d1944213a1632dde5489c0f2e1a.jpg', '0', '0', '', '', '', '', '0', '', '1', '1', '0', '1', '0', 'cn', '1563764411', '1563764739');
INSERT INTO `ey_ad` VALUES ('11', '3', '1', '', '', '/uploads/allimg/20190722/21550a2a81a09d8bf109bcaa826ec487.jpg', '0', '0', '', '', '', '', '0', '', '1', '3', '0', '1', '0', 'cn', '1563764411', '1563764739');

-- -----------------------------
-- Table structure for `ey_ad_position`
-- -----------------------------
DROP TABLE IF EXISTS `ey_ad_position`;
CREATE TABLE `ey_ad_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL DEFAULT '' COMMENT '广告位置名称',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '广告展示类型，1图片类型，2媒体类型，3HTML代码',
  `width` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '广告位宽度',
  `height` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '广告位高度',
  `intro` text NOT NULL COMMENT '广告描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0关闭1开启',
  `lang` varchar(50) NOT NULL DEFAULT 'cn' COMMENT '多语言',
  `admin_id` int(10) NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '伪删除，1=是，0=否',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='广告位置表';

-- -----------------------------
-- Records of `ey_ad_position`
-- -----------------------------
INSERT INTO `ey_ad_position` VALUES ('1', '首页-大幻灯片', '1', '1920', '550', '广告图片的宽高度随着浏览器大小而改变', '1', 'cn', '0', '0', '1524209276', '1564477760');
INSERT INTO `ey_ad_position` VALUES ('3', '手机端首页头部幻灯', '1', '0', '0', '', '1', 'cn', '1', '0', '1563764323', '1563764739');

-- -----------------------------
-- Table structure for `ey_admin`
-- -----------------------------
DROP TABLE IF EXISTS `ey_admin`;
CREATE TABLE `ey_admin` (
  `admin_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `user_name` varchar(20) NOT NULL DEFAULT '' COMMENT '用户名',
  `pen_name` varchar(50) DEFAULT '' COMMENT '笔名（发布文章后显示责任编辑的名字）',
  `true_name` varchar(20) DEFAULT '' COMMENT '真实姓名',
  `mobile` varchar(11) DEFAULT '' COMMENT '手机号码',
  `email` varchar(60) DEFAULT '' COMMENT 'email',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `head_pic` varchar(255) DEFAULT '' COMMENT '头像',
  `last_login` int(11) DEFAULT '0' COMMENT '最后登录时间',
  `last_ip` varchar(15) DEFAULT '' COMMENT '最后登录ip',
  `login_cnt` int(11) DEFAULT '0' COMMENT '登录次数',
  `session_id` varchar(50) DEFAULT '' COMMENT 'session_id',
  `parent_id` int(10) DEFAULT '0' COMMENT '父管理员ID',
  `role_id` int(10) NOT NULL DEFAULT '-1' COMMENT '角色组ID（-1表示超级管理员）',
  `mark_lang` varchar(50) DEFAULT 'cn' COMMENT '当前语言标识',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态(0=屏蔽，1=正常)',
  `syn_users_id` int(10) DEFAULT '0' COMMENT '同步注册到会员表',
  `add_time` int(11) DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`admin_id`),
  KEY `user_name` (`user_name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员表';

-- -----------------------------
-- Records of `ey_admin`
-- -----------------------------
INSERT INTO `ey_admin` VALUES ('1', 'admin', '', 'admin', '', '', '18e6a423f5a873ab50c1c55c59c69517', '', '1624416358', '127.0.0.1', '3', 'ev9vbfinbmkrrl47671t77shi3', '0', '-1', 'cn', '1', '1', '1624416294', '1624416303');

-- -----------------------------
-- Table structure for `ey_admin_log`
-- -----------------------------
DROP TABLE IF EXISTS `ey_admin_log`;
CREATE TABLE `ey_admin_log` (
  `log_id` bigint(16) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `admin_id` int(10) NOT NULL DEFAULT '-1' COMMENT '管理员id',
  `log_info` text COMMENT '日志描述',
  `log_ip` varchar(30) DEFAULT '' COMMENT 'ip地址',
  `log_url` varchar(255) DEFAULT '' COMMENT 'url',
  `log_time` int(11) DEFAULT '0' COMMENT '日志时间',
  PRIMARY KEY (`log_id`),
  KEY `admin_id` (`admin_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=592 DEFAULT CHARSET=utf8 COMMENT='管理员操作日志表';

-- -----------------------------
-- Records of `ey_admin_log`
-- -----------------------------
INSERT INTO `ey_admin_log` VALUES ('583', '1', '安全退出', '127.0.0.1', '/login.php', '1623813178');
INSERT INTO `ey_admin_log` VALUES ('584', '-1', '访问后台', '127.0.0.1', '/login.php', '1623813179');
INSERT INTO `ey_admin_log` VALUES ('585', '-1', '访问后台', '127.0.0.1', '/login.php', '1623813180');
INSERT INTO `ey_admin_log` VALUES ('586', '1', '后台登录', '127.0.0.1', '/login.php', '1623813184');
INSERT INTO `ey_admin_log` VALUES ('587', '-1', '访问后台', '127.0.0.1', '/login.php', '1624416296');
INSERT INTO `ey_admin_log` VALUES ('588', '1', '后台登录', '127.0.0.1', '/login.php', '1624416303');
INSERT INTO `ey_admin_log` VALUES ('589', '1', '安全退出', '127.0.0.1', '/login.php', '1624416352');
INSERT INTO `ey_admin_log` VALUES ('590', '-1', '访问后台', '127.0.0.1', '/login.php', '1624416353');
INSERT INTO `ey_admin_log` VALUES ('591', '1', '后台登录', '127.0.0.1', '/login.php', '1624416358');

-- -----------------------------
-- Table structure for `ey_archives`
-- -----------------------------
DROP TABLE IF EXISTS `ey_archives`;
CREATE TABLE `ey_archives` (
  `aid` int(10) NOT NULL AUTO_INCREMENT,
  `typeid` int(10) NOT NULL DEFAULT '0' COMMENT '当前栏目',
  `channel` int(10) NOT NULL DEFAULT '0' COMMENT '模型ID',
  `is_b` tinyint(1) DEFAULT '0' COMMENT '加粗',
  `title` varchar(200) DEFAULT '' COMMENT '标题',
  `litpic` varchar(250) DEFAULT '' COMMENT '缩略图',
  `is_head` tinyint(1) DEFAULT '0' COMMENT '头条（0=否，1=是）',
  `is_special` tinyint(1) DEFAULT '0' COMMENT '特荐（0=否，1=是）',
  `is_top` tinyint(1) DEFAULT '0' COMMENT '置顶（0=否，1=是）',
  `is_recom` tinyint(1) DEFAULT '0' COMMENT '推荐（0=否，1=是）',
  `is_jump` tinyint(1) DEFAULT '0' COMMENT '跳转链接（0=否，1=是）',
  `is_litpic` tinyint(1) DEFAULT '0' COMMENT '图片（0=否，1=是）',
  `is_roll` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '滚动（0=否，1=是）',
  `is_slide` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '幻灯（0=否，1=是）',
  `is_diyattr` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '自定义（0=否，1=是）',
  `author` varchar(200) DEFAULT '' COMMENT '作者',
  `click` int(10) DEFAULT '0' COMMENT '浏览量',
  `arcrank` int(10) DEFAULT '0' COMMENT '阅读权限：0=开放浏览，-1=待审核稿件',
  `jumplinks` varchar(200) DEFAULT '' COMMENT '外链跳转',
  `ismake` tinyint(1) DEFAULT '0' COMMENT '是否静态页面（0=动态，1=静态）',
  `seo_title` varchar(200) DEFAULT '' COMMENT 'SEO标题',
  `seo_keywords` varchar(200) DEFAULT '' COMMENT 'SEO关键词',
  `seo_description` text COMMENT 'SEO描述',
  `attrlist_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '参数列表ID',
  `users_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '会员价',
  `users_free` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否会员免费，默认0不免费，1为免费',
  `old_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '产品旧价',
  `sales_num` int(10) NOT NULL DEFAULT '0' COMMENT '销售量',
  `stock_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品库存量',
  `stock_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '商品库存在产品详情页是否显示，1为显示，0为不显示',
  `prom_type` tinyint(1) unsigned DEFAULT '0' COMMENT '产品类型：0=普通产品，1=虚拟(默认手动发货)，2=虚拟(网盘)，3=虚拟(自定义文本)',
  `tempview` varchar(200) DEFAULT '' COMMENT '文档模板文件名',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态(0=屏蔽，1=正常)',
  `sort_order` int(10) DEFAULT '0' COMMENT '排序号',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `admin_id` int(10) DEFAULT '0' COMMENT '管理员ID',
  `users_id` int(10) DEFAULT '0' COMMENT '会员ID',
  `arc_level_id` int(10) DEFAULT '0' COMMENT '文档会员权限ID',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '伪删除，1=是，0=否',
  `del_method` tinyint(1) DEFAULT '0' COMMENT '伪删除状态，1为主动删除，2为跟随上级栏目被动删除',
  `joinaid` int(10) DEFAULT '0' COMMENT '关联文档ID',
  `downcount` int(10) DEFAULT '0' COMMENT '下载次数',
  `appraise` int(10) DEFAULT '0' COMMENT '评价数',
  `collection` int(10) DEFAULT '0' COMMENT '收藏数',
  `htmlfilename` varchar(250) DEFAULT '' COMMENT '自定义文件名',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`aid`),
  KEY `aid` (`typeid`,`channel`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=106 DEFAULT CHARSET=utf8 COMMENT='文档主表';

-- -----------------------------
-- Records of `ey_archives`
-- -----------------------------
INSERT INTO `ey_archives` VALUES ('1', '1', '6', '0', '关于我们', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '', '0', '', '', '', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1526539465', '1609929468');
INSERT INTO `ey_archives` VALUES ('2', '8', '6', '0', '公司简介', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '0', '0', '', '0', '', '', '', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1526540452', '1609929701');
INSERT INTO `ey_archives` VALUES ('4', '11', '1', '1', 'seo是什么？', '/abc/uploads/allimg/20210107/1-21010G00910428.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '132', '0', '', '0', '', '', '在了解seo是什么意思之后，才能学习seo。什么是seo，从官方解释来看，seo=Search（搜索）Engine（引擎）Optimization（优化），即搜索引擎优化。使用过百度或其他搜索引擎，在', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1526545072', '1610615837');
INSERT INTO `ey_archives` VALUES ('39', '12', '1', '0', '回顾中国饮料40年发展史，总有一款是你儿时记忆的味道', '/abc/uploads/allimg/20210107/1-21010G0125Ia.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '186', '0', '', '0', '', '', '对于记忆来说，味道往往是最美的，儿时喝过的饮料，至今回想起来依然觉得津津有味。今天是六一儿童节，青山资本梳理了中国40年来饮料发展的简史，权当节日的小消遣，顺便看看能否找到你记忆深处的那个味道？第一阶段：国人味蕾的开启时代百事可乐在华第一家工厂开业1', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1527824652', '1610615741');
INSERT INTO `ey_archives` VALUES ('104', '66', '3', '0', '手机摄影', '/abc/uploads/allimg/20190808/17268e40477444ecbf11bcb643f321c2.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '261', '0', '', '0', '', '', 'SEO（搜索引擎优化）和有效的网站设计是齐头并进的。好的网站设计是关于创建一个吸引目标受众的网站，并让他们采取某种行动。但是，如果该网站不遵循目前的 SEO 最佳做法，它的排名将会受到影响，从而会导致真正参与该网站的访问者的数量的较少。相反地，如果将', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', 'view_images.htm', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1609986111', '1610615576');
INSERT INTO `ey_archives` VALUES ('9', '10', '1', '0', '用户界面设计和体验设计的差别', '/abc/uploads/allimg/20210107/1-21010G0093N30.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '169', '0', '', '0', '', '', '有时候我们需要获取图集中的第一张图片，下面给出解决办法： 第一步：修改include/extend.func.php 添加  // 提取图集第一张大图', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1526552582', '1610615832');
INSERT INTO `ey_archives` VALUES ('10', '10', '1', '0', '新手科普文！什么是用户界面和体验设计？', '/abc/uploads/allimg/20210107/1-21010G0095IU.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '130', '0', '', '0', '', '', '在仿站时，我们常常会自定义很多字段，那么如何在首页调用呢，下面给出方法：一、指定channelid属性（channelid=‘17‘ 17是指内容模型里面指定的模型ID) 二、指定要调用出来的字段ad', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1526552685', '1610615827');
INSERT INTO `ey_archives` VALUES ('12', '10', '1', '1', '一文读懂互联网女皇和她的报告：互联网领域的投资圣经、选股指南', '/abc/uploads/allimg/20210107/1-21010G0101L43.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '267', '0', '', '0', '', '', '北京时间 5 月 31 日凌晨，有“互联网女皇”之称的玛丽·米克尔发布了 2018 年的互联网趋势报告，这也是她第 23 年公布互联网报告。每年的互联网女皇报告几乎都会成为每个互联网创业者的必读报告。那么，互联网女皇是谁?为什么她的报告会如此受关注呢', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1526552714', '1610615823');
INSERT INTO `ey_archives` VALUES ('13', '12', '1', '0', '网站建设的五大核心要素', '/abc/uploads/allimg/20210107/1-21010G01035129.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '160', '0', '', '0', 'SEO标', 'O关键', 'SEO描述', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1526608216', '1610615818');
INSERT INTO `ey_archives` VALUES ('14', '10', '1', '0', '网站建设，静态页面和动态页面如何选择', '/abc/uploads/allimg/20190114/1621fb9e84a97e78b1c1cac6ec6b37bd.png', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '151', '0', '', '0', '', '', '网站建设，静态页面和动态页面如何选择　　电商网站建设为什么要使用静态页面制作。我们都知道，网站制作有分为静态页面制作和动态网页制作，那么建设电商网站采用哪种网站设计技术更好呢?　　我们建设网站最终目的', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1526609496', '1610615811');
INSERT INTO `ey_archives` VALUES ('19', '12', '1', '0', '从三方面完美的体验企业网站的核心价值', '/abc/uploads/allimg/20210107/1-21010G011414W.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '146', '0', '', '0', '', '', '从三方面完美的体验企业网站的核心价值　　随着互联网的迅猛发展，一个企业的发展离不开互联网的发展，企业注重企业网站建设，那么必然会给其带来不错的效果。企业网站建设其核心价值直接体现在网站对于用户和商家而', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1526610848', '1610615806');
INSERT INTO `ey_archives` VALUES ('20', '11', '1', '0', 'CMS是如何应运而生的？', '/abc/uploads/allimg/20210107/1-21010G0120L29.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '180', '0', '', '0', '', '', '随着网络应用的丰富和发展，很多网站往往不能迅速跟进大量信息衍生及业务模式变革的脚步，常常需要花费许多时间、人力和物力来处理信息更新和维护工作；遇到网站扩充的时候，整合内外网及分支网站的工作就变得更加复', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1526611606', '1610615801');
INSERT INTO `ey_archives` VALUES ('21', '11', '1', '0', '网站设计与SEO的关系，高手是从这4个维度分析的！', '/abc/uploads/allimg/20210107/1-21010G012205c.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '296', '0', '', '0', '', '', 'SEO（搜索引擎优化）和有效的网站设计是齐头并进的。好的网站设计是关于创建一个吸引目标受众的网站，并让他们采取某种行动。但是，如果该网站不遵循目前的SEO最佳做法，它的排名将会受到影响，从而会导致真正', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1526611744', '1610615796');
INSERT INTO `ey_archives` VALUES ('27', '24', '2', '0', '华为HUAWEI NOTE 8', '/abc/uploads/allimg/20190319/ef3caff1fe91f367fe4939d664a8a5da.jpg', '0', '0', '0', '1', '0', '1', '0', '0', '0', 'admin', '289', '0', '', '0', '', '', '全向录音/指向回放、定向免提、指关节手势、分屏多窗口、语音控制、情景智能、单手操作、杂志锁屏、手机找回、无线WIFI打印、学生模式、多屏互动、运动健康全向录音/指向回放、定向免提、指关节手势、分屏多窗', '1', '1909.00', '0', '0.00', '0', '2991', '1', '0', 'view_product.htm', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1526613043', '1610615791');
INSERT INTO `ey_archives` VALUES ('28', '26', '2', '0', '小米笔记本Air 13.3', '/abc/uploads/allimg/20190114/66109e989148356eadb4ff1eee285826.jpg', '0', '0', '0', '1', '0', '1', '0', '0', '0', 'admin', '149', '0', '', '0', '', '', '轻薄全金属机身/256GBSSD/第八代Intel酷睿i5处理器/FHD全贴合屏幕/指纹解锁/office激活不支持7天无理由退货...', '2', '4530.00', '0', '0.00', '0', '2997', '1', '0', 'view_product.htm', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1526613271', '1610615782');
INSERT INTO `ey_archives` VALUES ('29', '27', '2', '0', ' 小米蓝牙项圈耳机', '/abc/uploads/allimg/20190114/252a53e6fbc8f441b2570f755d2bbeb8.jpg', '0', '0', '0', '1', '0', '1', '0', '0', '0', 'admin', '211', '0', '', '0', '', '', '特性M3平板定制AKG品牌高保真耳机，配合M3平板享受HiFi音质...', '3', '198.00', '0', '0.00', '0', '2997', '1', '0', 'view_product.htm', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1526613739', '1610615773');
INSERT INTO `ey_archives` VALUES ('30', '5', '4', '0', '工程机械推土挖掘机类网站模板', '/abc/uploads/allimg/20190114/4873105f54a14f3785047bd8ecc8b5ac.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '261', '0', '', '0', '', '', '织梦新内核开发的模板，该模板属于企业通用、HTML5响应式、工程机械、挖掘机、推土机类企业使用，一款适用性很强的模板，基本可以适合各行业的企业网站！', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', 'view_download.htm', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1526614069', '1610615767');
INSERT INTO `ey_archives` VALUES ('31', '5', '4', '0', ' dom编程-window对象', '/abc/uploads/allimg/20190731/81e62e05fe7cdfb5e9abc50852047dcf.gif', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'admin', '248', '0', '', '0', '', '', 'DOM编程－window对象 回顾 请简述一下脚本执行的原理。 JavaScript中有哪些控制语句及其含义？ 如何创建一个有参函数以及如何调用它？ 预习检查 解释名词“根节点”、“子节点”和“相邻节点“。 window对象常用的属性有哪些？ 请解', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', 'view_download.htm', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1526614168', '1610615761');
INSERT INTO `ey_archives` VALUES ('40', '12', '1', '0', '社交媒体时代，如何对粉丝估值？', '/abc/uploads/allimg/20210107/1-21010G01311136.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '242', '0', '', '0', '', '', '约翰·奎尔奇说，社交媒体有很多营销挑战，如何为粉丝来估值是一个大问题。从营销角度来思考，要关注强纽带和弱纽带。你可能以为，强纽带的密友产生最大的营销影响，研究发现不是这样的，产生更大的影响反而是跟你更疏远的人。演讲者｜约翰·奎尔奇（哈佛商学院教授，曾', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', 'view_article.htm', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1527824837', '1610615736');
INSERT INTO `ey_archives` VALUES ('37', '24', '2', '0', '华为无线快充手机', '/abc/uploads/allimg/20190319/8a405e72e2acf9c5a29da7341a0eff89.jpg', '0', '0', '0', '1', '0', '1', '0', '0', '0', 'admin', '300', '0', '', '0', '', '', '全身都是科技亮点！7nm麒麟芯片，问鼎性能巅峰，4000万超广角徕卡三摄，随手捕捉大场面', '1', '3109.00', '0', '0.00', '0', '2997', '1', '0', 'view_product.htm', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1527507844', '1610615751');
INSERT INTO `ey_archives` VALUES ('38', '11', '1', '0', '商梦网校：单页SEO站群技术，用10个网站优化排名！', '/abc/uploads/allimg/20210107/1-21010G012425K.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '120', '0', '', '0', '', '', 'SEO很多伙伴都了解，就是搜索引擎排名优化，通过对网站内部和外部进行优化当用户搜索相应关键词时网站能够排名在搜索引擎前面，具体可以百度搜索“网络营销课程”查看商梦网校操作的案例！但单页SEO很多伙伴可能会有点陌生，单页SEO是将单页网站与内容内容结合', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1527555069', '1610615746');
INSERT INTO `ey_archives` VALUES ('105', '66', '3', '0', '数码蓝牙耳机产品渲染', '/abc/uploads/allimg/20190808/1c3dabff0cbf24fb6667899396a866aa.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '281', '0', '', '0', '', '', 'SEO（搜索引擎优化）和有效的网站设计是齐头并进的。好的网站设计是关于创建一个吸引目标受众的网站，并让他们采取某种行动。但是，如果该网站不遵循目前的 SEO 最佳做法，它的排名将会受到影响，从而会导致真正参与该网站的访问者的数量的较少。相反地，如果将', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', 'view_images.htm', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1609986111', '1610615570');
INSERT INTO `ey_archives` VALUES ('41', '12', '1', '0', '《颠覆营销:大数据时代的商业革命》：大数据“多即少，少即多”各种行销手段早已令人眼花缭乱', '/abc/uploads/allimg/20210107/1-21010G0132R20.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '224', '0', '', '0', '', '', '各种行销手段早已令人眼花缭乱，但究其本质都是在研究客户（消费者），研究客户的所想、所需，使产品或服务有的放矢。大数据时代又给它赋予了新名词：精准营销。大数据最先应用的领域多为面对客户的行业，最先应用的情景也多为精准营销。“酒好也怕巷子深”，产品或服务', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', 'view_article.htm', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1527825125', '1610615730');
INSERT INTO `ey_archives` VALUES ('42', '64', '3', '0', 'VIVO X27 手机摄影', '/uploads/allimg/20190808/17268e40477444ecbf11bcb643f321c2.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '261', '0', '', '0', '', '', 'SEO（搜索引擎优化）和有效的网站设计是齐头并进的。好的网站设计是关于创建一个吸引目标受众的网站，并让他们采取某种行动。但是，如果该网站不遵循目前的 SEO 最佳做法，它的排名将会受到影响，从而会导致真正参与该网站的访问者的数量的较少。相反地，如果将', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', 'view_images.htm', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1531731387', '1565234124');
INSERT INTO `ey_archives` VALUES ('43', '64', '3', '0', '3C数码蓝牙耳机产品渲染', '/abc/uploads/allimg/20190808/1c3dabff0cbf24fb6667899396a866aa.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '279', '0', '', '0', '', '', 'SEO（搜索引擎优化）和有效的网站设计是齐头并进的。好的网站设计是关于创建一个吸引目标受众的网站，并让他们采取某种行动。但是，如果该网站不遵循目前的 SEO 最佳做法，它的排名将会受到影响，从而会导致真正参与该网站的访问者的数量的较少。相反地，如果将', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', 'view_images.htm', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1531732591', '1610615719');
INSERT INTO `ey_archives` VALUES ('44', '64', '3', '0', '喷油耳机 建模渲染', '/abc/uploads/allimg/20190808/45b6f3f95d30a97cfa4a83d315b5c4f1.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '315', '0', '', '0', '', '', 'SEO（搜索引擎优化）和有效的网站设计是齐头并进的。好的网站设计是关于创建一个吸引目标受众的网站，并让他们采取某种行动。但是，如果该网站不遵循目前的 SEO 最佳做法，它的排名将会受到影响，从而会导致真正参与该网站的访问者的数量的较少。相反地，如果将', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', 'view_images.htm', '1', '100', 'cn', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '1531732811', '1610615714');
INSERT INTO `ey_archives` VALUES ('82', '23', '9', '0', '业务推广专员', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '261', '0', '', '0', '', '', '工作内容：1、负责公司手机游戏产品的在线推广；2、做好每天的推广统计，定制有效的投放策略并执行；3、完成每天的业绩要求，只要你努力，月入过万不是梦职位要求：1学历不限，欢迎优秀应届生（优秀者可放宽）；男女不限，19~24岁2.亲和力强、沟通流畅、重点', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '1563528028', '1563528211');
INSERT INTO `ey_archives` VALUES ('83', '23', '9', '0', '网络编辑员', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '470', '0', '', '0', '', '', '岗位职责：1、负责网站相关栏目、信息的搜集、编辑、发布等工作。2、完成信息内容的策划和日常信息的更新与维护。3、编写网站宣传资料及相关产品信息。4、配合部门编辑策划推广活动。5、部门总监下发的其他任务。任职资格：1、编辑、新闻、中文等相关专业优先，大', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '1563528241', '1563528292');
INSERT INTO `ey_archives` VALUES ('84', '9', '1', '0', '荣誉证书一', '/abc/uploads/allimg/20190722/7a6063154f58f9b76042c01674cfeb34.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '191', '0', '', '0', '', '', '', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '1563761544', '1610615703');
INSERT INTO `ey_archives` VALUES ('85', '9', '1', '0', '荣誉证书二', '/abc/uploads/allimg/20190722/9bcc063da2a8b6cfa394f8ce55264c86.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '215', '0', '', '0', '', '', '', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '1563761561', '1610615688');
INSERT INTO `ey_archives` VALUES ('86', '9', '1', '0', '荣誉证书三', '/abc/uploads/allimg/20190722/72c23e63ccabc9e3f42b16dfab17cf4e.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '245', '0', '', '0', '', '', '', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '1563761575', '1610615683');
INSERT INTO `ey_archives` VALUES ('87', '9', '1', '0', '荣誉证书四', '/abc/uploads/allimg/20190722/a519a45ce1f695da6bb656fb6f4ddcb5.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '182', '0', '', '0', '', '', '', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '1563761589', '1610615678');
INSERT INTO `ey_archives` VALUES ('89', '20', '2', '0', 'Apple iPhone 8 Plus (A1899) 64GB 深空灰色 移动联通4G手机', '/abc/uploads/allimg/20190731/582042862ba0d06c9408a9a1e669a067.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '233', '0', '', '0', '', '', '主体品牌Apple型号iPhone 8 Plus入网型号A1899上市年份2017年上市月份以官网信息为准基本信息机身颜色深空灰色机身长度（mm）158.4机身宽度（mm）78.1机身厚度（mm）7.5机身重量（g）202输入方式触控运营商标志或内容', '1', '1599.00', '0', '0.00', '0', '99999', '1', '0', 'view_product.htm', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '1564539099', '1610615667');
INSERT INTO `ey_archives` VALUES ('90', '24', '2', '0', '小米8屏幕指纹版 6GB+128GB 黑色 全网通4G 双卡双待 全面屏拍照智能游戏手机', '/abc/uploads/allimg/20190731/c4539460b957fea39a9db19e61eb0afe.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '132', '0', '', '0', '', '', '主体品牌小米（MI）型号小米8屏幕指纹版入网型号以官网信息为准上市年份2018年上市月份9月基本信息机身颜色黑色机身长度（mm）154.9机身宽度（mm）74.8机身厚度（mm）7.6机身重量（g）177运营商标志或内容无机身材质分类玻璃后盖操作系统', '1', '1709.00', '0', '0.00', '0', '2997', '1', '0', 'view_product.htm', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '1564539940', '1610615660');
INSERT INTO `ey_archives` VALUES ('91', '5', '4', '0', '计算机软件系统故障及维护', '/abc/uploads/allimg/20190731/0c8845e11a94b0f765ab24259c5b06b9.gif', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '276', '0', '', '0', '', '', 'WindowsXP操作系统原理使用系统维护工具系统启动故障的修复病毒防治的一般方法循辱魂币禾赫促陛醛放忆蛔睡钱佯回改波坏敏寄锈掳长提每臣传遥抄个似计算机软件系统故障及维护计算机软件系统故障及维护13.1.1WindowsXP的架构特点WindowsX', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', 'view_download.htm', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '1564565735', '1610615652');
INSERT INTO `ey_archives` VALUES ('92', '5', '4', '0', '计算机软件系统故障及维护2', '/abc/uploads/allimg/20190808/682be7153d02d14890144bef217149d1.jpg', '0', '0', '0', '0', '0', '0', '0', '0', '0', 'admin', '236', '0', '', '0', '', '', 'Windows操作系统只能使用2 GB,64位可使用4 GB)。接下来,NTLDR启动内建的微型文件系统驱动通过这个步骤,使NTLDR可以识别每一个用NTFS或FAT文件系统格式操作系统只能使用2 GB,64位可使用4 GB)', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', 'view_download.htm', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '1', '', '1564566264', '1610615642');
INSERT INTO `ey_archives` VALUES ('93', '64', '3', '0', '鼠标封面设计', '/uploads/allimg/20190808/b1f94bd8a0feba4062fa19d795099af4.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '264', '0', '', '0', '', '', '软件开发是根据用户要求建造出软件系统或者系统中的软件部分的过程。软件开发是一项包括需求捕捉，需求分析，设计，实现和测试的系统工程。软件一般是用某种程序设计语言来实现的。通常采用软件开发工具可以进行开发。软件分为系统软件和应用软件。 软件并不', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '1565161115', '1565232857');
INSERT INTO `ey_archives` VALUES ('94', '23', '9', '0', '网络推广', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '227', '0', '', '0', '', '', '1、负责客户开发、提供客户服务、公司服务的推广、建立与维护客户关系；2、根据市场营销计划和个人销售目标，完成各阶段销售目标；3、进行市场调研，确定目标市场，收集分析竞争对象信息，制订、执行销售对策；4、与内部相关部门建立并维持良好的协作关系，以客户和', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '1565225494', '1565225547');
INSERT INTO `ey_archives` VALUES ('95', '23', '9', '0', '网络销售', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '174', '0', '', '0', '', '', '岗位职责：1、利用网络进行公司产品的销售及推广2、了解网络销售，有信心和良好的学习能力3、完成团队目标4、通过网络进行渠道开发和业务拓展5、熟悉互联网络，熟练使用网络交流工具和各种办公软件6、有较强的沟通能力任职要求：1、年龄18～25之间，有空杯心', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '1565225576', '1565225608');
INSERT INTO `ey_archives` VALUES ('96', '23', '9', '0', '网络安全专员', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '212', '0', '', '0', '', '', '任职要求： 1、年龄25-35岁，本科及以上学历，网络安全相关专业，持网络安全证书，2年以上同岗位工作经验； 2、熟知防火墙、入侵检测、网络流量识别控制等信息安全产品相关技术；熟悉网络协议、网络编程及相关网络产品开发技术； 3、具备良好的安全意识能力', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '1565225609', '1565225638');
INSERT INTO `ey_archives` VALUES ('97', '23', '9', '0', '网络运营专员', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '', '263', '0', '', '0', '', '', '岗位职责1、组织参与重要项目的创意构思、文案及客户提案, 给予前期提案、设计创意说明及后期结案报告等服务；2、执行并监督所负责项目的创意构思和文案；3、稿件思路清晰，能够完成稿件写作思路规划；4、协助领导进行创意提案，保证工作的顺利推进；5、独立撰写', '0', '0.00', '0', '0.00', '0', '99999', '1', '0', '', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '1565225663', '1565225697');
INSERT INTO `ey_archives` VALUES ('98', '26', '2', '0', 'MIIX520 二合一笔记本12.2英寸 i7', '/abc/uploads/allimg/20190808/7dd05a89099c482a51be7faf1bb38ad4.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '133', '0', '', '0', '', '', '', '2', '6939.00', '0', '0.00', '0', '2997', '1', '0', 'view_product.htm', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '1565228564', '1610615629');
INSERT INTO `ey_archives` VALUES ('99', '26', '2', '0', 'MIIX 520 酷睿i5笔记本', '/abc/uploads/allimg/20190808/821fcaa266d291b4f504fb9a1d412c1c.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '169', '0', '', '0', '', '', '', '2', '5239.00', '0', '0.00', '0', '2997', '1', '0', 'view_product.htm', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '1565228905', '1610615619');
INSERT INTO `ey_archives` VALUES ('100', '26', '2', '0', '小新 Air 超轻薄笔记本', '/abc/uploads/allimg/20190808/a4b1ab346ae389e638f4a424b7396ee2.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '281', '0', '', '0', '', '', '', '2', '5499.00', '0', '0.00', '0', '2997', '1', '0', 'view_product.htm', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '1565229115', '1610615609');
INSERT INTO `ey_archives` VALUES ('101', '27', '2', '0', '联想 X1无线运动蓝牙耳机', '/abc/uploads/allimg/20190808/3ade68e134d3f8fbbd3401c545541106.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '110', '0', '', '0', '', '', '', '3', '99.00', '0', '0.00', '0', '2997', '1', '0', 'view_product.htm', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '1565229341', '1610615602');
INSERT INTO `ey_archives` VALUES ('102', '28', '2', '0', '联想智能音箱MINI', '/abc/uploads/allimg/20190808/989d19deb2377e199ec63d5ef9244be8.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '223', '0', '', '0', '', '', 'CMS人性化推荐 它更懂你；可轻松实现联想SIOT设备控制', '3', '319.00', '0', '0.00', '0', '2997', '1', '0', 'view_product.htm', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '1565229484', '1610615586');
INSERT INTO `ey_archives` VALUES ('103', '28', '2', '0', '联想智能音箱G1', '/abc/uploads/allimg/20190808/13fba5d0f2454c4b8fee4ada1d3fb39b.jpg', '0', '0', '0', '0', '0', '1', '0', '0', '0', 'admin', '172', '0', '', '0', '', '', '怦然心动的多彩生活 | 贴心的智能体验', '3', '539.00', '0', '0.00', '0', '2997', '1', '0', 'view_product.htm', '1', '100', 'cn', '1', '0', '0', '0', '0', '0', '0', '0', '0', '', '1565229770', '1610615580');

-- -----------------------------
-- Table structure for `ey_archives_flag`
-- -----------------------------
DROP TABLE IF EXISTS `ey_archives_flag`;
CREATE TABLE `ey_archives_flag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `flag_name` varchar(255) NOT NULL DEFAULT '' COMMENT '文档属性名称',
  `flag_attr` varchar(10) NOT NULL DEFAULT '' COMMENT '属性值',
  `flag_fieldname` varchar(255) NOT NULL DEFAULT '' COMMENT '字段名',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态， 1---显示， 0---隐藏',
  `sort_order` int(11) unsigned NOT NULL DEFAULT '0',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `flag_attr` (`flag_attr`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='文档属性配置表';

-- -----------------------------
-- Records of `ey_archives_flag`
-- -----------------------------
INSERT INTO `ey_archives_flag` VALUES ('1', '头条', 'h', 'is_head', '1', '1', 'cn', '1606272350', '1606272350');
INSERT INTO `ey_archives_flag` VALUES ('2', '推荐', 'c', 'is_recom', '1', '2', 'cn', '1606272350', '1606272350');
INSERT INTO `ey_archives_flag` VALUES ('3', '特荐', 'a', 'is_special', '1', '3', 'cn', '1606272350', '1606272350');
INSERT INTO `ey_archives_flag` VALUES ('4', '加粗', 'b', 'is_b', '1', '4', 'cn', '1606272350', '1606272350');
INSERT INTO `ey_archives_flag` VALUES ('5', '图片', 'p', 'is_litpic', '1', '5', 'cn', '1606272350', '1606272350');
INSERT INTO `ey_archives_flag` VALUES ('6', '跳转', 'j', 'is_jump', '1', '6', 'cn', '1606272350', '1606272350');
INSERT INTO `ey_archives_flag` VALUES ('7', '幻灯', 's', 'is_slide', '0', '7', 'cn', '1606272350', '1606272350');
INSERT INTO `ey_archives_flag` VALUES ('8', '滚动', 'r', 'is_roll', '0', '8', 'cn', '1606272350', '1606272350');
INSERT INTO `ey_archives_flag` VALUES ('9', '最热', 'd', 'is_diyattr', '0', '9', 'cn', '1606272350', '1606272350');

-- -----------------------------
-- Table structure for `ey_arcmulti`
-- -----------------------------
DROP TABLE IF EXISTS `ey_arcmulti`;
CREATE TABLE `ey_arcmulti` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `tagid` varchar(60) NOT NULL DEFAULT '' COMMENT '标签ID',
  `tagname` varchar(60) NOT NULL DEFAULT '' COMMENT '标签名',
  `innertext` text NOT NULL COMMENT '标签模板代码',
  `pagesize` int(10) NOT NULL DEFAULT '0' COMMENT '分页列表',
  `querysql` text NOT NULL COMMENT '完整SQL',
  `ordersql` varchar(200) DEFAULT '' COMMENT '排序SQL',
  `addfieldsSql` varchar(255) DEFAULT '' COMMENT '附加字段SQL',
  `addtableName` varchar(50) DEFAULT '' COMMENT '附加字段的数据表，不包含表前缀',
  `attstr` text COMMENT '属性字符串',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COMMENT='多页标记存储数据表';

-- -----------------------------
-- Records of `ey_arcmulti`
-- -----------------------------
INSERT INTO `ey_arcmulti` VALUES ('1', 'arclistf2a4757664ac7fcfdcf8be671b05a309', 'arclist', '', '3', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (3,20,24,25,21,26,22,27,28,29,3) AND a.channel IN (2) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 3', 'a.sort_order asc, a.aid desc', '', '', 'a:4:{s:3:\\\"row\\\";s:1:\\\"3\\\";s:8:\\\"titlelen\\\";s:2:\\\"20\\\";s:7:\\\"infolen\\\";s:2:\\\"22\\\";s:6:\\\"typeid\\\";s:30:\\\"3,20,24,25,21,26,22,27,28,29,3\\\";}', '1563497171', '1563497261');
INSERT INTO `ey_arcmulti` VALUES ('2', 'arcliste18e8e9ae2f28bbc276f84b4cfa1cc20', 'arclist', '', '3', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (20,24,25,20) AND a.channel IN (2) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 3', 'a.sort_order asc, a.aid desc', '', '', 'a:5:{s:6:\\\"typeid\\\";s:13:\\\"$field.typeid\\\";s:3:\\\"row\\\";s:1:\\\"3\\\";s:8:\\\"titlelen\\\";s:2:\\\"20\\\";s:7:\\\"infolen\\\";s:2:\\\"22\\\";s:2:\\\"id\\\";s:6:\\\"field2\\\";}', '1563497171', '1563497261');
INSERT INTO `ey_arcmulti` VALUES ('3', 'arclist6e38efb655ec8077ac057e8ede710cc2', 'arclist', '', '3', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (21,26,21) AND a.channel IN (2) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 3', 'a.sort_order asc, a.aid desc', '', '', 'a:5:{s:6:\\\"typeid\\\";s:13:\\\"$field.typeid\\\";s:3:\\\"row\\\";s:1:\\\"3\\\";s:8:\\\"titlelen\\\";s:2:\\\"20\\\";s:7:\\\"infolen\\\";s:2:\\\"22\\\";s:2:\\\"id\\\";s:6:\\\"field2\\\";}', '1563497171', '1563497261');
INSERT INTO `ey_arcmulti` VALUES ('4', 'arclist69886b691f3cb73ec088e82960c570d3', 'arclist', '', '3', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (22,27,28,29,22) AND a.channel IN (2) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 3', 'a.sort_order asc, a.aid desc', '', '', 'a:5:{s:6:\\\"typeid\\\";s:13:\\\"$field.typeid\\\";s:3:\\\"row\\\";s:1:\\\"3\\\";s:8:\\\"titlelen\\\";s:2:\\\"20\\\";s:7:\\\"infolen\\\";s:2:\\\"22\\\";s:2:\\\"id\\\";s:6:\\\"field2\\\";}', '1563497171', '1563497261');
INSERT INTO `ey_arcmulti` VALUES ('5', 'arclist1e0c928687c4ee0ef6f56bba67833123', 'arclist', '', '4', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (11,11) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 4', 'a.sort_order asc, a.aid desc', '', '', 'a:4:{s:3:\\\"row\\\";s:1:\\\"4\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:7:\\\"infolen\\\";s:3:\\\"160\\\";s:6:\\\"typeid\\\";s:5:\\\"11,11\\\";}', '1563497171', '1563497261');
INSERT INTO `ey_arcmulti` VALUES ('6', 'arclist24a73d0a30993ffa5758581ebd9dcdd3', 'arclist', '', '4', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (12,12) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 4', 'a.sort_order asc, a.aid desc', '', '', 'a:4:{s:3:\\\"row\\\";s:1:\\\"4\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:7:\\\"infolen\\\";s:3:\\\"160\\\";s:6:\\\"typeid\\\";s:5:\\\"12,12\\\";}', '1563497171', '1563497261');
INSERT INTO `ey_arcmulti` VALUES ('7', 'arclist5d5058c21066e560145b811503352441', 'arclist', '', '4', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (3,20,24,25,21,26,22,27,28,29,3) AND a.channel IN (2) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 4', 'a.sort_order asc, a.aid desc', '', '', 'a:4:{s:3:\\\"row\\\";s:1:\\\"4\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:7:\\\"infolen\\\";s:3:\\\"160\\\";s:6:\\\"typeid\\\";s:30:\\\"3,20,24,25,21,26,22,27,28,29,3\\\";}', '1563497171', '1563497261');
INSERT INTO `ey_arcmulti` VALUES ('8', 'arclist2db1f317fabb3c9a6866ed9ee96f54a1', 'arclist', '', '8', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (3,20,24,25,21,26,22,27,28,29,3) AND a.channel IN (2) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 8', 'a.sort_order asc, a.aid desc', '', '', 'a:3:{s:3:\\\"row\\\";s:1:\\\"8\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:6:\\\"typeid\\\";s:30:\\\"3,20,24,25,21,26,22,27,28,29,3\\\";}', '1563500085', '1563500379');
INSERT INTO `ey_arcmulti` VALUES ('9', 'arclist79d9069963de4b5e5d82be990b99e905', 'arclist', '', '8', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (20,24,25,20) AND a.channel IN (2) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 8', 'a.sort_order asc, a.aid desc', '', '', 'a:3:{s:6:\\\"typeid\\\";s:13:\\\"$field.typeid\\\";s:3:\\\"row\\\";s:1:\\\"8\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";}', '1563500446', '1566369132');
INSERT INTO `ey_arcmulti` VALUES ('10', 'arclist4d066f52156e1a4efe0748654fa3b6ea', 'arclist', '', '8', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (21,26,21) AND a.channel IN (2) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 8', 'a.sort_order asc, a.aid desc', '', '', 'a:3:{s:6:\\\"typeid\\\";s:13:\\\"$field.typeid\\\";s:3:\\\"row\\\";s:1:\\\"8\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";}', '1563500446', '1566369132');
INSERT INTO `ey_arcmulti` VALUES ('11', 'arclist8574c2aca2a5b22c50b15ef3bc7e1a33', 'arclist', '', '8', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (22,27,28,29,22) AND a.channel IN (2) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 8', 'a.sort_order asc, a.aid desc', '', '', 'a:3:{s:6:\\\"typeid\\\";s:13:\\\"$field.typeid\\\";s:3:\\\"row\\\";s:1:\\\"8\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";}', '1563500446', '1566369132');
INSERT INTO `ey_arcmulti` VALUES ('12', 'arclist9fddb7407c081410e941aa19f0923b52', 'arclist', '', '8', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (2,10,11,12,2) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 8', 'a.sort_order asc, a.aid desc', '', '', 'a:3:{s:3:\\\"row\\\";s:1:\\\"8\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:6:\\\"typeid\\\";s:12:\\\"2,10,11,12,2\\\";}', '1563501248', '1564556739');
INSERT INTO `ey_arcmulti` VALUES ('13', 'arclistac104eb66a4eaa937221a93d44f94435', 'arclist', '', '4', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (4,4) AND a.channel IN (3) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 4', 'a.sort_order asc, a.aid desc', '', '', 'a:3:{s:3:\\\"row\\\";s:1:\\\"4\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:6:\\\"typeid\\\";s:3:\\\"4,4\\\";}', '1563502612', '1565081822');
INSERT INTO `ey_arcmulti` VALUES ('14', 'arclistd207eb63dbc7b74cf6259da13d2e97b9', 'arclist', '', '10', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (13,13) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 10', 'a.sort_order asc, a.aid desc', '', '', 'a:3:{s:3:\\\"row\\\";s:2:\\\"10\\\";s:8:\\\"titlelen\\\";s:2:\\\"20\\\";s:6:\\\"typeid\\\";s:2:\\\"13\\\";}', '1563502998', '1565081822');
INSERT INTO `ey_arcmulti` VALUES ('15', 'arclist47dc220d8dbfeb7c17b04fed59055adc', 'arclist', '', '3', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (24,24) AND a.channel IN (2) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY rand() LIMIT 3', 'rand()', '', '', 'a:4:{s:3:\\\"row\\\";s:1:\\\"3\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:7:\\\"orderby\\\";s:4:\\\"rand\\\";s:6:\\\"typeid\\\";s:5:\\\"24,24\\\";}', '1563517778', '1563517778');
INSERT INTO `ey_arcmulti` VALUES ('16', 'arclist0febed8cab600f8b7593035770fff792', 'arclist', '', '3', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (27,27) AND a.channel IN (2) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY rand() LIMIT 3', 'rand()', '', '', 'a:4:{s:3:\\\"row\\\";s:1:\\\"3\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:7:\\\"orderby\\\";s:4:\\\"rand\\\";s:6:\\\"typeid\\\";s:5:\\\"27,27\\\";}', '1563518605', '1563518605');
INSERT INTO `ey_arcmulti` VALUES ('17', 'arclist1f972a30c9f459309e43e87ca3cb6121', 'arclist', '', '5', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (12,12) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY rand() LIMIT 5', 'rand()', '', '', 'a:4:{s:5:\\\"limit\\\";s:3:\\\"0,5\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:7:\\\"orderby\\\";s:4:\\\"rand\\\";s:6:\\\"typeid\\\";s:5:\\\"12,12\\\";}', '1563519320', '1563519320');
INSERT INTO `ey_arcmulti` VALUES ('18', 'arclist99926dd7c30a868477fd17d16a81681d', 'arclist', '', '5', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.channel IN (1,2,3,4,9) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY rand() LIMIT 5', 'rand()', '', '', 'a:4:{s:5:\\\"limit\\\";s:3:\\\"0,5\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:7:\\\"orderby\\\";s:4:\\\"rand\\\";s:6:\\\"typeid\\\";s:0:\\\"\\\";}', '1563520605', '1563762367');
INSERT INTO `ey_arcmulti` VALUES ('19', 'arclistf8d0d499899593c6555a607bbb539040', 'arclist', '', '6', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (12,12) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.aid desc LIMIT 6', 'a.aid desc', '', '', 'a:4:{s:7:\\\"orderby\\\";s:4:\\\"rand\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:3:\\\"row\\\";s:1:\\\"6\\\";s:6:\\\"typeid\\\";s:5:\\\"12,12\\\";}', '1563520951', '1564561400');
INSERT INTO `ey_arcmulti` VALUES ('20', 'arclistb5d6628401c13b0f1a9132863e811a4a', 'arclist', '', '4', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (12,12) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.aid desc LIMIT 4', 'a.aid desc', '', '', 'a:4:{s:7:\\\"orderby\\\";s:4:\\\"rand\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:3:\\\"row\\\";s:1:\\\"4\\\";s:6:\\\"typeid\\\";s:5:\\\"12,12\\\";}', '1563520971', '1566369138');
INSERT INTO `ey_arcmulti` VALUES ('21', 'arclist3c9fddda7d05cad277bdbd93402d9d22', 'arclist', '', '5', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (24,24) AND  (a.is_recom = 1)  AND a.channel IN (2) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.add_time desc LIMIT 5', 'a.add_time desc', '', '', 'a:5:{s:4:\\\"flag\\\";s:1:\\\"c\\\";s:7:\\\"orderby\\\";s:3:\\\"now\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:3:\\\"row\\\";s:1:\\\"5\\\";s:6:\\\"typeid\\\";s:5:\\\"24,24\\\";}', '1563522655', '1563522655');
INSERT INTO `ey_arcmulti` VALUES ('22', 'arclistea2c53d97a3dab716932b55304e9ee24', 'arclist', '', '5', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (3,20,24,25,21,26,22,27,28,29,3) AND  (a.is_recom = 1)  AND a.channel IN (2) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.add_time desc LIMIT 5', 'a.add_time desc', '', '', 'a:5:{s:4:\\\"flag\\\";s:1:\\\"c\\\";s:7:\\\"orderby\\\";s:3:\\\"now\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:3:\\\"row\\\";s:1:\\\"5\\\";s:6:\\\"typeid\\\";s:1:\\\"3\\\";}', '1563522685', '1565756004');
INSERT INTO `ey_arcmulti` VALUES ('23', 'arclistc734ae28fa130543e8506769264e84aa', 'arclist', '', '6', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (4,4) AND a.channel IN (3) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY rand() LIMIT 6', 'rand()', '', '', 'a:4:{s:7:\\\"orderby\\\";s:4:\\\"rand\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:3:\\\"row\\\";s:1:\\\"6\\\";s:6:\\\"typeid\\\";s:3:\\\"4,4\\\";}', '1563524659', '1563524997');
INSERT INTO `ey_arcmulti` VALUES ('24', 'arclistbcfc777e0b75150b35450efa0e6550fc', 'arclist', '', '8', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (23,23) AND a.channel IN (9) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.aid desc LIMIT 8', 'a.aid desc', '', '', 'a:4:{s:7:\\\"orderby\\\";s:4:\\\"rand\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:3:\\\"row\\\";s:1:\\\"8\\\";s:6:\\\"typeid\\\";s:5:\\\"23,23\\\";}', '1563529284', '1564978558');
INSERT INTO `ey_arcmulti` VALUES ('25', 'arclistd2ed60241171156d35617cfd6d145f51', 'arclist', '', '5', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (5,5) AND a.channel IN (4) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY rand() LIMIT 5', 'rand()', '', '', 'a:4:{s:6:\\\"typeid\\\";s:1:\\\"5\\\";s:5:\\\"limit\\\";s:3:\\\"0,5\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:7:\\\"orderby\\\";s:4:\\\"rand\\\";}', '1563534168', '1563758872');
INSERT INTO `ey_arcmulti` VALUES ('26', 'arclist05d9a84635addf2046b9749be19a231a', 'arclist', '', '6', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (5,5) AND a.channel IN (4) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.aid desc LIMIT 6', 'a.aid desc', '', '', 'a:4:{s:7:\\\"orderby\\\";s:4:\\\"rand\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:3:\\\"row\\\";s:1:\\\"6\\\";s:6:\\\"typeid\\\";s:3:\\\"5,5\\\";}', '1563759870', '1565248541');
INSERT INTO `ey_arcmulti` VALUES ('27', 'arclist4911e95ea34e622a2316fb5fd3a060d3', 'arclist', '', '6', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (11,11) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.aid desc LIMIT 6', 'a.aid desc', '', '', 'a:4:{s:7:\\\"orderby\\\";s:4:\\\"rand\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:3:\\\"row\\\";s:1:\\\"6\\\";s:6:\\\"typeid\\\";s:5:\\\"11,11\\\";}', '1563762324', '1564545300');
INSERT INTO `ey_arcmulti` VALUES ('28', 'arclist8cdfe13915331df05c886ef2cec53b01', 'arclist', '', '4', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (3,20,24,25,21,26,22,27,28,29,3) AND a.channel IN (2) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 4', 'a.sort_order asc, a.aid desc', '', '', 'a:3:{s:3:\\\"row\\\";s:1:\\\"4\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:6:\\\"typeid\\\";s:30:\\\"3,20,24,25,21,26,22,27,28,29,3\\\";}', '1563765202', '1565081609');
INSERT INTO `ey_arcmulti` VALUES ('29', 'arclist620dab4fd1ea8e8a826d038fc8c76ab5', 'arclist', '', '5', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (2,10,11,12,2) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 5', 'a.sort_order asc, a.aid desc', '', '', 'a:3:{s:3:\\\"row\\\";s:1:\\\"5\\\";s:8:\\\"titlelen\\\";s:2:\\\"40\\\";s:6:\\\"typeid\\\";s:12:\\\"2,10,11,12,2\\\";}', '1563765202', '1564476617');
INSERT INTO `ey_arcmulti` VALUES ('30', 'arclist3d6622ce1045a5890999a09ac5efe11a', 'arclist', '', '4', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (23,23) AND a.channel IN (9) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 4', 'a.sort_order asc, a.aid desc', 'gzdd,xzdy', 'recruit_content', 'a:4:{s:3:\\\"row\\\";s:1:\\\"4\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:9:\\\"addfields\\\";s:9:\\\"gzdd,xzdy\\\";s:6:\\\"typeid\\\";s:5:\\\"23,23\\\";}', '1563765219', '1565081609');
INSERT INTO `ey_arcmulti` VALUES ('31', 'arclistb45393652dbaf27dd422ecb18ba6556d', 'arclist', '', '4', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (11,11) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.aid desc LIMIT 4', 'a.aid desc', '', '', 'a:4:{s:7:\\\"orderby\\\";s:4:\\\"rand\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:3:\\\"row\\\";s:1:\\\"4\\\";s:6:\\\"typeid\\\";s:5:\\\"11,11\\\";}', '1563778302', '1565148971');
INSERT INTO `ey_arcmulti` VALUES ('32', 'arclist47e63d1daa34906533daa4604b9990f2', 'arclist', '', '4', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (23,23) AND a.channel IN (9) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.aid desc LIMIT 4', 'a.aid desc', '', '', 'a:4:{s:7:\\\"orderby\\\";s:4:\\\"rand\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:3:\\\"row\\\";s:1:\\\"4\\\";s:6:\\\"typeid\\\";s:5:\\\"23,23\\\";}', '1563780846', '1565248066');
INSERT INTO `ey_arcmulti` VALUES ('33', 'arclist5fd45fc06338850f9a81b0034f155d3f', 'arclist', '', '4', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (5,5) AND a.channel IN (4) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.aid desc LIMIT 4', 'a.aid desc', '', '', 'a:4:{s:7:\\\"orderby\\\";s:4:\\\"rand\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:3:\\\"row\\\";s:1:\\\"4\\\";s:6:\\\"typeid\\\";s:3:\\\"5,5\\\";}', '1563784777', '1564624222');
INSERT INTO `ey_arcmulti` VALUES ('34', 'arclist8fd20121b30e976f2cdc6adec826c6c3', 'arclist', '', '6', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (2,10,11,12,2) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 6', 'a.sort_order asc, a.aid desc', '', '', 'a:3:{s:3:\\\"row\\\";s:1:\\\"6\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:6:\\\"typeid\\\";s:12:\\\"2,10,11,12,2\\\";}', '1564544784', '1566369132');
INSERT INTO `ey_arcmulti` VALUES ('35', 'arclistac5acf1c77d2762da6f5cbceb003356f', 'arclist', '', '6', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (2,10,11,12,2) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 6', 'a.sort_order asc, a.aid desc', '', '', 'a:3:{s:3:\\\"row\\\";s:1:\\\"6\\\";s:8:\\\"titlelen\\\";s:2:\\\"40\\\";s:6:\\\"typeid\\\";s:12:\\\"2,10,11,12,2\\\";}', '1564545092', '1564545092');
INSERT INTO `ey_arcmulti` VALUES ('36', 'arclistdd8b07e4c8175aba9a7cdfc75846934e', 'arclist', '', '6', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (2,10,56,11,12,2) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 6', 'a.sort_order asc, a.aid desc', '', '', 'a:3:{s:3:\\\"row\\\";s:1:\\\"6\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:6:\\\"typeid\\\";s:15:\\\"2,10,56,11,12,2\\\";}', '1564625043', '1564632486');
INSERT INTO `ey_arcmulti` VALUES ('37', 'arclistdef33cd20eedde16fff9fa4d0a7eb441', 'arclist', '', '6', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (2,10,56,11,58,12,2) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 6', 'a.sort_order asc, a.aid desc', '', '', 'a:3:{s:3:\\\"row\\\";s:1:\\\"6\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:6:\\\"typeid\\\";s:18:\\\"2,10,56,11,58,12,2\\\";}', '1564632570', '1564632624');
INSERT INTO `ey_arcmulti` VALUES ('38', 'arclistdf739fb67eaeec266be0dba6cc76dc04', 'arclist', '', '6', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (2,10,56,60,11,58,12,2) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 6', 'a.sort_order asc, a.aid desc', '', '', 'a:3:{s:3:\\\"row\\\";s:1:\\\"6\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:6:\\\"typeid\\\";s:21:\\\"2,10,56,60,11,58,12,2\\\";}', '1564632681', '1564632681');
INSERT INTO `ey_arcmulti` VALUES ('39', 'arclist600324cc7c844844852e875669ef7d42', 'arclist', '', '6', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (2,10,56,60,62,11,58,12,2) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 6', 'a.sort_order asc, a.aid desc', '', '', 'a:3:{s:3:\\\"row\\\";s:1:\\\"6\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:6:\\\"typeid\\\";s:24:\\\"2,10,56,60,62,11,58,12,2\\\";}', '1564632721', '1565081822');
INSERT INTO `ey_arcmulti` VALUES ('40', 'arclist60e65cb0a7c69bb4f505d32aa9e0bd6f', 'arclist', '', '6', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (23,23) AND a.channel IN (9) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.aid desc LIMIT 6', 'a.aid desc', '', '', 'a:4:{s:7:\\\"orderby\\\";s:4:\\\"rand\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:3:\\\"row\\\";s:1:\\\"6\\\";s:6:\\\"typeid\\\";s:5:\\\"23,23\\\";}', '1564978722', '1565248473');
INSERT INTO `ey_arcmulti` VALUES ('41', 'arclist031dac4c91d7ab3607722fdb1705316d', 'arclist', '', '5', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (2,10,56,60,62,11,58,12,2) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 5', 'a.sort_order asc, a.aid desc', '', '', 'a:3:{s:3:\\\"row\\\";s:1:\\\"5\\\";s:8:\\\"titlelen\\\";s:2:\\\"40\\\";s:6:\\\"typeid\\\";s:24:\\\"2,10,56,60,62,11,58,12,2\\\";}', '1565080638', '1565081609');
INSERT INTO `ey_arcmulti` VALUES ('42', 'arclist46740127f31460ef6da55d0e79f9c2d7', 'arclist', '', '4', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (4,64,66,4) AND a.channel IN (3) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 4', 'a.sort_order asc, a.aid desc', '', '', 'a:3:{s:3:\\\"row\\\";s:1:\\\"4\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:6:\\\"typeid\\\";s:9:\\\"4,64,66,4\\\";}', '1565084028', '1566369132');
INSERT INTO `ey_arcmulti` VALUES ('43', 'arclist985346079685352bfbe0e0633afbc119', 'arclist', '', '10', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (13) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 10', 'a.sort_order asc, a.aid desc', '', '', 'a:3:{s:3:\\\"row\\\";s:2:\\\"10\\\";s:8:\\\"titlelen\\\";s:2:\\\"20\\\";s:6:\\\"typeid\\\";s:2:\\\"13\\\";}', '1565084028', '1565084028');
INSERT INTO `ey_arcmulti` VALUES ('44', 'arclist_5a76c5f011c2a588ce0e333c00b423e9', 'arclist', '', '8', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (20,24,25,20) AND a.channel IN (2) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 8', 'a.sort_order asc, a.aid desc', '', '', 'a:4:{s:6:\\\"typeid\\\";s:11:\\\"20,24,25,20\\\";s:3:\\\"row\\\";s:1:\\\"8\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:7:\\\"channel\\\";i:2;}', '1567578955', '1571040555');
INSERT INTO `ey_arcmulti` VALUES ('45', 'arclist_063a53b5d2473d808e305da149f05c61', 'arclist', '', '8', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (21,26,21) AND a.channel IN (2) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 8', 'a.sort_order asc, a.aid desc', '', '', 'a:4:{s:6:\\\"typeid\\\";s:8:\\\"21,26,21\\\";s:3:\\\"row\\\";s:1:\\\"8\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:7:\\\"channel\\\";i:2;}', '1567578955', '1571040555');
INSERT INTO `ey_arcmulti` VALUES ('46', 'arclist_f0baa6c505a2ed9d3e6216131c3065f6', 'arclist', '', '8', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (22,27,28,29,22) AND a.channel IN (2) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 8', 'a.sort_order asc, a.aid desc', '', '', 'a:4:{s:6:\\\"typeid\\\";s:14:\\\"22,27,28,29,22\\\";s:3:\\\"row\\\";s:1:\\\"8\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:7:\\\"channel\\\";i:2;}', '1567578955', '1571040555');
INSERT INTO `ey_arcmulti` VALUES ('47', 'arclist_4a1e4b2a3c164a2513efaabc7695ff8c', 'arclist', '', '6', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (2,10,11,12,2) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 6', 'a.sort_order asc, a.aid desc', '', '', 'a:4:{s:3:\\\"row\\\";s:1:\\\"6\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:6:\\\"typeid\\\";s:12:\\\"2,10,11,12,2\\\";s:7:\\\"channel\\\";i:1;}', '1567578955', '1571040555');
INSERT INTO `ey_arcmulti` VALUES ('48', 'arclist_a60ee9dfcfd02e30cc0ff48a07f2abf0', 'arclist', '', '4', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (4,64,66,4) AND a.channel IN (3) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 4', 'a.sort_order asc, a.aid desc', '', '', 'a:4:{s:3:\\\"row\\\";s:1:\\\"4\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:6:\\\"typeid\\\";s:9:\\\"4,64,66,4\\\";s:7:\\\"channel\\\";i:3;}', '1567578955', '1571040555');
INSERT INTO `ey_arcmulti` VALUES ('49', 'arclist_47b16318fb008e048bb81470255204cb', 'arclist', '', '5', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (3,20,24,25,21,26,22,27,28,29,3) AND  (a.is_recom = 1)  AND a.channel IN (2) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.add_time desc LIMIT 5', 'a.add_time desc', '', '', 'a:6:{s:4:\\\"flag\\\";s:1:\\\"c\\\";s:7:\\\"orderby\\\";s:3:\\\"now\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:3:\\\"row\\\";s:1:\\\"5\\\";s:6:\\\"typeid\\\";s:30:\\\"3,20,24,25,21,26,22,27,28,29,3\\\";s:7:\\\"channel\\\";i:2;}', '1571037681', '1571040561');
INSERT INTO `ey_arcmulti` VALUES ('50', 'arclist_c8f764156fe6c94913aefc34656aa01b', 'arclist', '', '4', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (3,20,24,25,21,26,22,27,28,29,3) AND a.channel IN (2) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 4', 'a.sort_order asc, a.aid desc', '', '', 'a:4:{s:3:\\\"row\\\";s:1:\\\"4\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:6:\\\"typeid\\\";s:30:\\\"3,20,24,25,21,26,22,27,28,29,3\\\";s:7:\\\"channel\\\";i:2;}', '1571038596', '1571038596');
INSERT INTO `ey_arcmulti` VALUES ('51', 'arclist_ffec36b5ef0c45fc8bfcf7f0caa1248c', 'arclist', '', '5', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (2,10,11,12,2) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 5', 'a.sort_order asc, a.aid desc', '', '', 'a:4:{s:3:\\\"row\\\";s:1:\\\"5\\\";s:8:\\\"titlelen\\\";s:2:\\\"40\\\";s:6:\\\"typeid\\\";s:12:\\\"2,10,11,12,2\\\";s:7:\\\"channel\\\";i:1;}', '1571038596', '1571038596');
INSERT INTO `ey_arcmulti` VALUES ('52', 'arclist_ea010e69008d127156c4c0e05800db3e', 'arclist', '', '4', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (23,23) AND a.channel IN (9) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 4', 'a.sort_order asc, a.aid desc', 'gzdd,xzdy', 'recruit_content', 'a:5:{s:3:\\\"row\\\";s:1:\\\"4\\\";s:8:\\\"titlelen\\\";s:2:\\\"30\\\";s:9:\\\"addfields\\\";s:9:\\\"gzdd,xzdy\\\";s:6:\\\"typeid\\\";s:5:\\\"23,23\\\";s:7:\\\"channel\\\";i:9;}', '1571038596', '1571038596');
INSERT INTO `ey_arcmulti` VALUES ('53', 'block001_f51fde2f40161fb7ae3615f749985935', 'arclist', '', '10', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (2,10,11,12) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 10', 'a.sort_order asc, a.aid desc', '', '', 'a:4:{s:3:\\\"row\\\";s:2:\\\"10\\\";s:5:\\\"tagid\\\";s:8:\\\"block001\\\";s:7:\\\"infolen\\\";s:2:\\\"50\\\";s:6:\\\"typeid\\\";s:10:\\\"2,10,11,12\\\";}', '1609929404', '1623809725');
INSERT INTO `ey_arcmulti` VALUES ('54', 'block001_625a78bf91ffacc7aa2786d7ce719aca', 'arclist', '', '10', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (10) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 10', 'a.sort_order asc, a.aid desc', '', '', 'a:4:{s:3:\\\"row\\\";s:2:\\\"10\\\";s:5:\\\"tagid\\\";s:8:\\\"block001\\\";s:7:\\\"infolen\\\";s:2:\\\"50\\\";s:6:\\\"typeid\\\";s:2:\\\"10\\\";}', '1609984295', '1623809724');
INSERT INTO `ey_arcmulti` VALUES ('55', 'block001_908e507b6c696ce5684d3994c7719972', 'arclist', '', '10', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (11) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 10', 'a.sort_order asc, a.aid desc', '', '', 'a:4:{s:3:\\\"row\\\";s:2:\\\"10\\\";s:5:\\\"tagid\\\";s:8:\\\"block001\\\";s:7:\\\"infolen\\\";s:2:\\\"50\\\";s:6:\\\"typeid\\\";s:2:\\\"11\\\";}', '1609984299', '1623809724');
INSERT INTO `ey_arcmulti` VALUES ('56', 'block001_29747ad7a75ac7c99e02e5346036e70f', 'arclist', '', '10', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (12) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 10', 'a.sort_order asc, a.aid desc', '', '', 'a:4:{s:3:\\\"row\\\";s:2:\\\"10\\\";s:5:\\\"tagid\\\";s:8:\\\"block001\\\";s:7:\\\"infolen\\\";s:2:\\\"50\\\";s:6:\\\"typeid\\\";s:2:\\\"12\\\";}', '1609984300', '1623809724');
INSERT INTO `ey_arcmulti` VALUES ('57', 'block001_11d7603abb62aae993f1dc59d4fc239e', 'arclist', '', '1', 'SELECT `b`.*,`a`.* FROM `ey_archives` `a` LEFT JOIN `ey_arctype` `b` ON `b`.`id`=`a`.`typeid` WHERE  (  a.typeid IN (10) AND a.channel IN (1) AND a.arcrank > -1 AND a.status = 1 AND a.is_del = 0 )  AND `a`.`lang` = \'cn\' ORDER BY a.sort_order asc, a.aid desc LIMIT 1', 'a.sort_order asc, a.aid desc', '', '', 'a:4:{s:3:\\\"row\\\";s:1:\\\"1\\\";s:5:\\\"tagid\\\";s:8:\\\"block001\\\";s:7:\\\"infolen\\\";s:2:\\\"50\\\";s:6:\\\"typeid\\\";s:2:\\\"10\\\";}', '1610349340', '1610349340');

-- -----------------------------
-- Table structure for `ey_arcrank`
-- -----------------------------
DROP TABLE IF EXISTS `ey_arcrank`;
CREATE TABLE `ey_arcrank` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '权限ID',
  `rank` smallint(6) DEFAULT '0' COMMENT '权限值',
  `name` char(20) DEFAULT '' COMMENT '会员名称',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='文档阅读权限表';

-- -----------------------------
-- Records of `ey_arcrank`
-- -----------------------------
INSERT INTO `ey_arcrank` VALUES ('1', '0', '开放浏览', 'cn', '0', '1552376880');
INSERT INTO `ey_arcrank` VALUES ('2', '-1', '待审核稿件', 'cn', '0', '1552376880');

-- -----------------------------
-- Table structure for `ey_arctype`
-- -----------------------------
DROP TABLE IF EXISTS `ey_arctype`;
CREATE TABLE `ey_arctype` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '栏目ID',
  `channeltype` int(10) DEFAULT '0' COMMENT '栏目顶级模型ID',
  `current_channel` int(10) DEFAULT '0' COMMENT '栏目当前模型ID',
  `parent_id` int(10) DEFAULT '0' COMMENT '栏目上级ID',
  `topid` int(10) DEFAULT '0' COMMENT '顶级栏目ID',
  `typename` varchar(200) DEFAULT '' COMMENT '栏目名称',
  `dirname` varchar(200) DEFAULT '' COMMENT '目录英文名',
  `dirpath` varchar(200) DEFAULT '' COMMENT '目录存放HTML路径',
  `englist_name` varchar(200) DEFAULT '' COMMENT '栏目英文名',
  `grade` tinyint(1) DEFAULT '0' COMMENT '栏目等级',
  `typelink` varchar(200) DEFAULT '' COMMENT '栏目链接',
  `litpic` varchar(250) DEFAULT '' COMMENT '栏目图片',
  `templist` varchar(200) DEFAULT '' COMMENT '列表模板文件名',
  `tempview` varchar(200) DEFAULT '' COMMENT '文档模板文件名',
  `seo_title` varchar(200) DEFAULT '' COMMENT 'SEO标题',
  `seo_keywords` varchar(200) DEFAULT '' COMMENT 'seo关键字',
  `seo_description` text COMMENT 'seo描述',
  `sort_order` int(10) DEFAULT '0' COMMENT '排序号',
  `is_hidden` tinyint(1) DEFAULT '0' COMMENT '是否隐藏栏目：0=显示，1=隐藏',
  `is_part` tinyint(1) DEFAULT '0' COMMENT '栏目属性：0=内容栏目，1=外部链接',
  `admin_id` int(10) DEFAULT '0' COMMENT '管理员ID',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '伪删除，1=是，0=否',
  `del_method` tinyint(1) DEFAULT '0' COMMENT '伪删除状态，1为主动删除，2为跟随上级栏目被动删除',
  `status` tinyint(1) DEFAULT '1' COMMENT '启用 (1=正常，0=屏蔽)',
  `is_release` tinyint(1) DEFAULT '0' COMMENT '栏目是否应用于会员投稿发布，1是，0否',
  `weapp_code` varchar(50) DEFAULT '' COMMENT '插件栏目唯一标识',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `dirname` (`dirname`,`lang`) USING BTREE,
  KEY `parent_id` (`channeltype`,`parent_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=68 DEFAULT CHARSET=utf8 COMMENT='文档栏目表';

-- -----------------------------
-- Records of `ey_arctype`
-- -----------------------------
INSERT INTO `ey_arctype` VALUES ('1', '6', '6', '0', '0', '关于我们', 'guanyuwomen', '/guanyuwomen', '', '0', '', '/uploads/allimg/20210106/1-2101061SJ5D4.jpg', 'lists_single.htm', '', '', '', '', '1', '0', '0', '0', '0', '0', '1', '0', '', 'cn', '1526539465', '1609929468');
INSERT INTO `ey_arctype` VALUES ('2', '1', '1', '0', '0', '新闻动态', 'xinwendongtai', '/xinwendongtai', 'News &amp; Trends', '0', '', '/uploads/allimg/20210106/1-2101061SQ3C4.jpg', 'lists_article.htm', 'view_article.htm', '', '', '', '2', '0', '0', '0', '0', '0', '1', '1', '', 'cn', '1526539487', '1609929495');
INSERT INTO `ey_arctype` VALUES ('3', '2', '2', '0', '0', '产品展示', 'chanpinzhanshi', '/chanpinzhanshi', 'Product show', '0', '', '/uploads/allimg/20210106/1-2101061SR5120.jpg', 'lists_product.htm', 'view_product.htm', '', '', '未来，期待与用户携手缔造一个更好的易而优CMS', '3', '0', '0', '0', '0', '0', '1', '0', '', 'cn', '1526539505', '1609929507');
INSERT INTO `ey_arctype` VALUES ('4', '3', '3', '0', '0', '解决方案', 'kehuanli', '/kehuanli', 'Case', '0', '', '/uploads/allimg/20210106/1-2101061ST0127.jpg', 'lists_images.htm', 'view_images.htm', '', '', '', '4', '0', '0', '0', '0', '0', '1', '0', '', 'cn', '1526539517', '1609929522');
INSERT INTO `ey_arctype` VALUES ('5', '4', '4', '0', '0', '资料下载', 'ziliaoxiazai', '/ziliaoxiazai', 'Download', '0', '', '/uploads/allimg/20210106/1-2101061S911K5.jpg', 'lists_download.htm', 'view_download.htm', '', '', '', '5', '0', '0', '0', '0', '0', '1', '0', '', 'cn', '1526539530', '1609929553');
INSERT INTO `ey_arctype` VALUES ('6', '1', '8', '23', '23', '在线应聘', 'zaixianyingpin', '/rencaizhaopin/zaixianyingpin', '', '1', '', '', 'lists_guestbook.htm', '', '', '', '', '100', '1', '0', '0', '0', '0', '1', '0', '', 'cn', '1526539546', '1609929573');
INSERT INTO `ey_arctype` VALUES ('8', '6', '6', '1', '1', '公司简介', 'gongsijianjie', '/guanyuwomen/gongsijianjie', 'About Us', '1', '', '', 'lists_single.htm', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', '0', '', 'cn', '1526540452', '1609929701');
INSERT INTO `ey_arctype` VALUES ('9', '6', '1', '1', '1', '公司荣誉', 'gsry', '/guanyuwomen/gsry', 'GLORIES Glories', '1', '', '', 'lists_article_img.htm', 'view_article.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', '1', '', 'cn', '1526540478', '1609929468');
INSERT INTO `ey_arctype` VALUES ('10', '1', '1', '2', '2', '公司动态', 'gongsidongtai', '/xinwendongtai/gongsidongtai', '', '1', '', '', 'lists_article.htm', 'view_article.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', '1', '', 'cn', '1526540530', '1609929495');
INSERT INTO `ey_arctype` VALUES ('11', '1', '1', '2', '2', '行业资讯', 'xingyezixun', '/xinwendongtai/xingyezixun', '', '1', '', '', 'lists_article.htm', 'view_article.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', '1', '', 'cn', '1526540543', '1609929495');
INSERT INTO `ey_arctype` VALUES ('12', '1', '1', '2', '2', '媒体报道', 'meitibaodao', '/xinwendongtai/meitibaodao', '', '1', '', '', 'lists_article.htm', 'view_article.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', '1', '', 'cn', '1526540554', '1609929495');
INSERT INTO `ey_arctype` VALUES ('20', '2', '2', '3', '3', '手机数码', 'shouji', '/chanpinzhanshi/shouji', '', '1', '', '', 'lists_product.htm', 'view_product.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', '0', '', 'cn', '1526612114', '1609929507');
INSERT INTO `ey_arctype` VALUES ('21', '2', '2', '3', '3', '电脑产品', 'diannao', '/chanpinzhanshi/diannao', '', '1', '', '', 'lists_product.htm', 'view_product.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', '0', '', 'cn', '1526612188', '1609929507');
INSERT INTO `ey_arctype` VALUES ('22', '2', '2', '3', '3', '周边配件', 'peijian', '/chanpinzhanshi/peijian', '', '1', '', '', 'lists_product.htm', 'view_product.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', '0', '', 'cn', '1526612218', '1609929507');
INSERT INTO `ey_arctype` VALUES ('23', '1', '9', '0', '0', '人才招聘', 'rencaizhaopin', '/rencaizhaopin', 'Recruitment ', '0', '', '/uploads/allimg/20210106/1-2101061S931938.jpg', 'lists_recruit.htm', 'view_recruit.htm', '', '', '', '6', '0', '0', '0', '0', '0', '1', '0', '', 'cn', '1526612255', '1609929573');
INSERT INTO `ey_arctype` VALUES ('24', '2', '2', '20', '3', '智能手机', 'zhinenshouji', '/chanpinzhanshi/shouji/zhinenshouji', '', '2', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', '0', '', 'cn', '1526612571', '1610334638');
INSERT INTO `ey_arctype` VALUES ('25', '2', '2', '20', '3', '畅玩手机', 'changwanshouji', '/chanpinzhanshi/shouji/changwanshouji', '', '2', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', '0', '', 'cn', '1526612606', '1610334638');
INSERT INTO `ey_arctype` VALUES ('26', '2', '2', '21', '3', '笔记本电脑', 'bijibendiannao', '/chanpinzhanshi/diannao/bijibendiannao', '', '2', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', '0', '', 'cn', '1526612635', '1610334638');
INSERT INTO `ey_arctype` VALUES ('27', '2', '2', '22', '3', '耳机', 'erji', '/chanpinzhanshi/peijian/erji', '', '2', '', '', 'lists_product.htm', 'view_product.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', '0', '', 'cn', '1526612661', '1610334638');
INSERT INTO `ey_arctype` VALUES ('28', '2', '2', '22', '3', '音箱', 'yinxiang', '/chanpinzhanshi/peijian/yinxiang', '', '2', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', '0', '', 'cn', '1526612678', '1610334638');
INSERT INTO `ey_arctype` VALUES ('29', '2', '2', '22', '3', '充电宝', 'chongdianbao', '/chanpinzhanshi/peijian/chongdianbao', '', '2', '', '', 'lists_product.htm', 'view_product.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', '0', '', 'cn', '1526612691', '1610334638');
INSERT INTO `ey_arctype` VALUES ('30', '6', '8', '0', '0', '联系我们', 'lianxiwomen986', '/lianxiwomen986', 'Online Message', '0', '', '/uploads/allimg/20210106/1-2101061T032D7.jpg', 'lists_guestbook.htm', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', '0', '', 'cn', '1526634493', '1610347982');
INSERT INTO `ey_arctype` VALUES ('64', '3', '3', '4', '4', '系统方案', 'xitong', '/kehuanli/xitong', '', '1', '', '', 'lists_images.htm', 'view_images.htm', '', '', '', '100', '0', '0', '1', '0', '0', '1', '0', '', 'cn', '1565083870', '1609929522');
INSERT INTO `ey_arctype` VALUES ('66', '3', '3', '4', '4', '应用方案', 'yingyong', '/kehuanli/yingyong', '', '1', '', '', 'lists_images.htm', 'view_images.htm', '', '', '', '100', '0', '0', '1', '0', '0', '1', '0', '', 'cn', '1565083875', '1609929522');

-- -----------------------------
-- Table structure for `ey_article_content`
-- -----------------------------
DROP TABLE IF EXISTS `ey_article_content`;
CREATE TABLE `ey_article_content` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aid` int(10) DEFAULT '0' COMMENT '文档ID',
  `content` longtext COMMENT '内容详情',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `news_id` (`aid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COMMENT='文章附加表';

-- -----------------------------
-- Records of `ey_article_content`
-- -----------------------------
INSERT INTO `ey_article_content` VALUES ('1', '4', '&lt;p&gt;在了解seo是什么意思之后，才能学习seo。&lt;br/&gt;&lt;/p&gt;&lt;p&gt;什么是seo，从官方解释来看，seo=Search（搜索） Engine（引擎） Optimization（优化），即搜索引擎优化。&lt;/p&gt;&lt;p&gt;使用过百度或其他搜索引擎，在搜索框中输入某一个关键词，如铁艺大门，排名靠前带有广告字样，背景略不同的是竞价位置，为俗称的&lt;a href=&quot;http://www.xminseo.com/2376.html&quot; title=&quot;&quot;&gt;sem&lt;/a&gt;位置。&lt;/p&gt;&lt;p&gt;seo是基于搜索引擎营销的一种网络营销方式，通过seo技术，提升网站关键词排名，获得展现，继而获得曝光，继而获得用户点击，继而获得转化。&lt;/p&gt;&lt;p&gt;一：seo分类。&lt;/p&gt;&lt;p&gt;细化来看，所有有利于网站关键词排名提升的点，都可以归纳于seo，为便于理解，我们将seo分为站内seo和站外seo。&lt;/p&gt;&lt;p&gt;1：站内seo。&lt;/p&gt;&lt;p&gt;什么是站内seo？通俗来讲，就是指网站内部优化，即网站本身内部的优化，包括代码标签优化、内容优化、安全建设、用户体验等。&lt;/p&gt;&lt;p&gt;2：站外seo。&lt;/p&gt;&lt;p&gt;什么是站外seo？通俗来讲，就是网站的外部优化，包括外链建设，品牌建设，速度优化，引流等。&lt;/p&gt;&lt;p&gt;二：seo相关建议。&lt;/p&gt;&lt;p&gt;1：建议把seo定位于一种网络营销方式，在学习，使用seo的过程中，将他作为一种获取流量的渠道。&lt;/p&gt;&lt;p&gt;2：新手学习seo的理想平台是百度搜索资源平台而非其他；理论联系实际操作是更为有效的学习方式；有经验的seo高手教会更快的掌握好seo；多思考，多总结，才能领悟seo的精髓。&lt;/p&gt;&lt;p&gt;3：学习seo之前，熟悉掌握相关seo术语很有必要。&lt;/p&gt;&lt;p&gt;4：很多时候，seo的理论与现实是相违背的，也就是说seo的理论点不复杂，操作点却很难达到。&lt;/p&gt;&lt;p&gt;新手接触seo，感觉无所适从，请熟读seo术语，后面会越来越轻松。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1610615837', '1610615837');
INSERT INTO `ey_article_content` VALUES ('5', '9', '&lt;p&gt;注：用户界面（UI，User Interface）设计是设计软件产品所涉及到的几个交叉学科之一。不论是用户体验（UX，User Experience）、交互设计（ID，Interaction Design），还是视觉/图形设计（Visual / Graphic Design），都能牵扯到用户界面设计。&lt;/p&gt;&lt;p&gt;一、什么是用户界面设计？&lt;/p&gt;&lt;p&gt;广泛来讲，用户界面是人与机器交流的媒介。用户向机器发出指令，机器随即开始一段进程，回复信息，并给出反馈。用户可以根据用户反馈进行下一步操作的决策。&lt;/p&gt;&lt;p&gt;人机交互（HCI，Human Computer Interaciton）所关注的主要是数字界面，即过去的打孔机、命令行，直至今天的图形界面（GUI，Graphic Design）。&lt;/p&gt;&lt;p&gt;用户界面设计对于数码产品来说主要关注的是布局、信息结构，以及界面元素在显示屏和各种终端平台上的展示。电子游戏和电视界面也包括其中。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1610615832', '1610615832');
INSERT INTO `ey_article_content` VALUES ('15', '39', '&lt;p&gt;对于记忆来说，味道往往是最美的，儿时喝过的饮料，至今回想起来依然觉得津津有味。&lt;br/&gt;&lt;/p&gt;&lt;p&gt;今天是六一儿童节，青山资本梳理了中国40年来饮料发展的简史，权当节日的小消遣，顺便看看能否找到你记忆深处的那个味道？&lt;/p&gt;&lt;h2&gt;第一阶段：国人味蕾的开启时代&lt;/h2&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;百事可乐在华第一家工厂开业&lt;/p&gt;&lt;p&gt;1981年，可口可乐在中国第一条生产线正式投产，主要供应旅游饭店，卖给外国人收取外汇，百事可乐也在深圳建立了第一家罐装厂。&lt;/p&gt;&lt;p&gt;1982年，国家把饮料纳入“国家计划管理产品”，可口可乐开始在北京市场进行内销。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1610615741', '1610615741');
INSERT INTO `ey_article_content` VALUES ('16', '40', '&lt;p&gt;约翰·奎尔奇说， 社交媒体有很多营销挑战，如何为粉丝来估值是一个大问题。从营销角度来思考，要关注强纽带和弱纽带。你可能以为，强纽带的密友产生最大的营销影响，研究发现不是这样的，产生更大的影响反而是跟你更疏远的人。&lt;/p&gt;&lt;p&gt;演讲者｜ 约翰·奎尔奇&lt;/p&gt;&lt;p&gt;（ 哈佛商学院教授， 曾任伦敦商学院院长、中欧国际工商学院副院长）&lt;/p&gt;&lt;p&gt;非常感谢大家在周日早上回来听我讲课。对于你们这些创业者，或者希望成为创业者的人，我今天准备了一个特别的讲座。&lt;/p&gt;&lt;p&gt;很多创业者没有把最终愿景很好界定，所以每天都忙于灭火，忙于生存。&lt;/p&gt;&lt;p&gt;创业营销，你必须做好规划&lt;/p&gt;&lt;p&gt;今天将从创业营销这个话题开始，包括你如何生存和成功。创业营销包括四个关键领域，你必须很好地去规划：&lt;/p&gt;&lt;ul style=&quot;list-style-type: inherit;&quot; class=&quot; list-paddingleft-2&quot;&gt;&lt;li&gt;&lt;p&gt;要有正确的目标客户和最终用户；&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p&gt;要有正确的产品和服务&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p&gt;要有一个非常好的人才团队，使得商业创意能够实现；&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p&gt;要有好的合作伙伴，不是分销商，而是会计、律师等服务伙伴。&lt;/p&gt;&lt;/li&gt;&lt;/ul&gt;&lt;p&gt;那么，何为创业营销？ ？&lt;/p&gt;&lt;p&gt;第一，这是从愿景到行动的逆向工程设计&lt;/p&gt;&lt;p&gt;当星巴克只有 5 家店时，创始人就有一个愿景，让星巴克成为你生活中的第三空间。&lt;/p&gt;&lt;p&gt;对创业者要从愿景开始，向后进行逆向工程的设计：看一下需要有什么样的行动，才能实现愿景。 很多创业者没有把最终愿景很好界定，所以每天都忙于灭火，忙于生存。&lt;/p&gt;&lt;p&gt;第二，快速的周期，低成本进行试验，以提供证据&lt;/p&gt;&lt;p&gt;有了愿景要去思考，怎样做一些快速的低成本实验测试创意，向合作伙伴、客户等证明，这是一个非常好的愿景。换句话说， 你需要短期的成就作为证据。&lt;/p&gt;&lt;p&gt;第三，与高瞻远瞩的客户共同开发&lt;/p&gt;&lt;p&gt;大多数的客户是保守的，不想浪费时间在新公司上。 你必须要找到有远见的客户，他们愿意在你身上冒风险。 他们可能是小的新兴客户，不是你想要进入的那个市场的好根基客户。&lt;/p&gt;&lt;p&gt;第四：创建小步快跑的综合路线图&lt;/p&gt;&lt;p&gt;包括创建产品路线图、客户图、合作伙伴路线图、人才路线图。 创业者应该有一个长达一年甚至三年的路线图，看下你希望这个公司在这四个维度上应该怎么样取得进步。&lt;/p&gt;&lt;p&gt;举个例子&lt;/p&gt;&lt;p&gt;上世纪 90 年代末， John Osher 发明了 SpinBrush ，这是一个低成本的电动牙刷。因为 他洞察到市场上存在着一个很大的空白：普通手动牙刷每支两美元，电动牙刷要 50 美元。 但是这两者之间，没有任何中间产品。&lt;/p&gt;&lt;p&gt;他想开发一个牙刷，价格介于两者之间。他思考了下新牙刷成功的性能标准：&lt;/p&gt;&lt;ul style=&quot;list-style-type: inherit;&quot; class=&quot; list-paddingleft-2&quot;&gt;&lt;li&gt;&lt;p&gt;清洁上要优于手动牙刷，不然消费者不会付出更高的价格；&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p&gt;自带电池能用三个月，如果每周都要换电池太崩溃；&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p&gt;包装中有试用的特点，大家愿意看看牙刷启动后是怎么旋转的；&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p&gt;零售价不到 6 美元。&lt;/p&gt;&lt;/li&gt;&lt;/ul&gt;&lt;p&gt;他对新牙刷的定位是：是更好的手动牙刷，而不是一个更便宜的电动牙刷。&lt;/p&gt;&lt;p&gt;对于消费者，是从 2 美元增加到 6 美元，而不是从 50 美元降到 6 美元。因为如果是后者，零售商会觉得赔了：消费者只花了6美元，而以前是50美元。但是现在，消费者从花2块提高到了花6块。&lt;/p&gt;&lt;p&gt;所以创业者不仅要考虑最终用户，还要思考如何让分销商多赚钱，因为你必须通过他们，产品才能到最终客户那里。 界定竞争的时候，好的定位声明非常重要。 最后，他把公司卖给了 宝洁，一共赚了4.8亿美元。&lt;/p&gt;&lt;p&gt;大家看，其实非常简单，就是因为他有大量的消费者洞察，填补了没有任何人看见的市场空白。&lt;/p&gt;&lt;p&gt;再举个例子&lt;/p&gt;&lt;p&gt;这家公司叫 Intuit ，创始人在20年前就发现，好多人在应对自己税务处理的时候，每年要填一个纳税申报单再交给政府，很麻烦。&lt;/p&gt;&lt;p&gt;Intuit 是第一个开发个人理财软件的公司，尤其是做纳税管理方面的软件，不管是个人还是小企业都可以用。 但是这个好用的软件包，不知道卖向哪里，没人相信它能用。&lt;/p&gt;&lt;p&gt;有时候你最大的问题就是，你的新产品如何把分销商搞定。他们分销很多东西，根本没时间花五小时检查你这个不知名的产品能不能用。&lt;/p&gt;&lt;p&gt;最后他直接向消费者保证： 如果买了这个产品，六分钟内没学会怎么用，钱退给你，产品也送给你。&lt;/p&gt;&lt;p&gt;除了退钱，他们还做了什么与众不同的事情呢？&lt;/p&gt;&lt;ul style=&quot;list-style-type: inherit;&quot; class=&quot; list-paddingleft-2&quot;&gt;&lt;li&gt;&lt;p&gt;在买家允许下，跟着买家观察他的首次使用过程。&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p&gt;公司所有高管每个月必须花两小时做客户的技术支持，听客户遇到的问题；&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p&gt;做客户服务的技术支持，是公司里晋升的必经路径；&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p&gt;把客户的信当着所有高管的面大声朗读，不管是感谢还是指责。&lt;/p&gt;&lt;/li&gt;&lt;/ul&gt;&lt;p&gt;这使得他们 50% 的销售是来自于口碑， 20% 的销售是来自于技术支持的推荐。&lt;/p&gt;&lt;p&gt;“ 客户真正想要的和技术真正能做好的交叉点 —— 在此处才能找到真正的伟大。 ”&lt;/p&gt;&lt;p&gt;“ 我们不管做什么，都是有客户存在的。 ”&lt;/p&gt;&lt;p&gt;——ScottCook（ Intuit创始人）&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1610615736', '1610615736');
INSERT INTO `ey_article_content` VALUES ('17', '41', '&lt;p&gt;各种行销手段早已令人眼花缭乱，但究其本质都是在研究客户（消费者），研究客户的所想、所需，使产品或服务有的放矢。大数据时代又给它赋予了新名词：精准营销。大数据最先应用的领域多为面对客户的行业，最先应用的情景也多为精准营销。&lt;/p&gt;&lt;p&gt;“酒好也怕巷子深”，产品或服务的信息要送达客户才可能促成交易。一般认为，向客户传达产品或服务信息要靠广告。广告古已有之，“三碗不过岗”的酒幌子就是广告。没有互联网的时代，我们熟悉的是电视广告、广播广告、印刷品平面广告、户外广告牌等，当然，也包括吆喝叫卖。但过去的广告是千人一面、不区分受众的。后来商家对客户的信息有所采集就有了CRM，经过客户分类，可以更好地服务于不同的客户群体。互联网+大数据时代让CRM有了新的发展机遇，管理客户不再是简单的数字统计和没有个性的（或简单聚类的）直邮、定投。随着商家对客户知道更多、了解更深，便有机会为客户提供个性化的营销方案，进一步改善客户体验，成为了个性化营销或叫精准营销。大数据时代，让很多过去的不可能变为可能，营销活动也赢来了新的发展机遇。&lt;/p&gt;&lt;p&gt;时代不同，商业经营的形式会变化，但本质就是两件事：开源，节流。开源是开拓新客户，发现新商机；节流是减少内部运营成本，提高资源利用效率。要实现这一切都需要以数据为依据的决策。过去，人们也在长期的经营活动中，采集和运用了与经营活动相关的很多强相关数据，也形成了选择客户的标准。鉴于当时的技术瓶颈，做大样本的数据采集及数据分析成本都过高，无法在更大范围推广运用。大数据时代，人们有了廉价采集数据和存储数据的可能，廉价的计算资源让数据分析成为了可能。&lt;/p&gt;&lt;p&gt;大数据精准营销的背后，是用多维度的数据来观察客户，描述客户，就是说为客户画像。说“依托大数据，可以让营销人员比过去更了解客户，比客户自己更了解客户的需求”并不为过。营销人员无不想知道客户是谁、在哪里、消费习惯是什么、需要什么、什么时候需要、用什么方式向他们传递信息更为有效等等，通过数据采集和数据分析分析可以找到答案。精准营销不仅可以帮助商家开源---发现潜在客户，还可以帮助商家节流---发现潜在风险。当我们对客户了解更多，就会知道哪位客户可能在经营中存在风险。&lt;/p&gt;&lt;p&gt;若问每个经营者是否会运用从业经验来进行营销，多数答案是肯定的。但若问经营者是否会利用数据进行营销，恐怕答案就是五花八门。一般认为，应用数据进行营销是大公司的事情，与小公司无缘。其实，大到跨国公司，小到街边小贩，运用数据进行营销，都会收到意想不到的结果。不相信吗？街边小贩留意一下天气预报（刮风，下雨，还是暴晒）就知道明天有哪些生意的机会，进而知道该如何备货。建议中小公司的人不要拒绝精准营销的理念，不妨学学精准营销的思想方法。即便是经营者有丰富的经验，把经验数据化对经营也会很有帮助。&lt;/p&gt;&lt;p&gt;《颠覆营销》一书就是在教读者如何运用大数据来做营销。书中案例丰富、语言可读性强。值得关心大数据营销的各界朋友读一读。&lt;/p&gt;&lt;p&gt;我认同书中的不少观点：“大数据重新定义产业竞争规则，比的不是数据规模大小，不是统计技术，也不是强大的计算能力，而是核心数据的解读能力”。在很多人纠结于大数据定义的今天，我们确实更应该关注数据的核心价值理解与应用。书中提出的“问对问题”也很重要。经营者平时的问题一定不少，但追问究竟时，就可能出现偏差，导致“失之毫厘谬以千里”。问对问题能力的提高涉及思想方法，需要在锻炼中提高。验证问题是否问对了，恰恰就是数据分析师可以做贡献的地方。&lt;/p&gt;&lt;p&gt;本书还引起了二个值得更深入思考的问题：&lt;/p&gt;&lt;p&gt;仅仅发现不同客户群体的消费习惯，适时提醒客户去消费，还远远不够。比如：某消费者一个月的正常理性消费在两千元的水平，一般在A，B两家商店消费。A商店运用了精准营销的理念会让消费者把这两千元都花在A商店，随着B商店的后来居上，消费者又可能重新回到B商店消费这两千元。在供给过剩需求不足的今天，既有的消费额在不同商家中进行分配或迁移都不能带来社会消费总量的增加。大数据营销的更高水平应用是提前知晓客户尚未被满足、甚至尚未被发现的需求。大数据的价值挖掘有机会把商家（含厂家）和客户连在一起，让商家提供更多的满足客户个性化需求的产品或服务，让客户的消费意愿提高。这是数据价值挖掘工作者面临的新挑战。&lt;/p&gt;&lt;p&gt;数据真的越多越好吗？不少大数据公司热衷于用爬虫软件在网上“爬”各种数据。然而同一数据集在不同的应用场景价值密度是不一样的，针对特定应用场景也并非是数据维度越多就越好，一定要围绕应用目标来采集数据和使用数据。提升维度来采集更多数据一定是有助于更详尽地描述事物，但无疑也增加了处理数据的复杂性。每一次技术的进步，都给人类带来新的想象空间，难免欲望膨胀自信满满，对世界的认知也随之升维，甚至是无节制地升维。之后发现升维带来资源的占用，智慧跟不上，无节制地升维反而是解决方案复杂化，冷静下来会重新启动降维思考。也许人类的认知与智慧就是在升维、降维、再升维、再降维中交替前行的。本书的降维思考，必要时回归本元的思考给人们启示。&lt;/p&gt;&lt;p&gt;大数据时代工具手段固然重要，思想方法更为重要。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1610615730', '1610615730');
INSERT INTO `ey_article_content` VALUES ('6', '10', '&lt;p&gt;Z Yuhan：用户界面（UI，User Interface）设计是设计软件产品所涉及到的几个交叉学科之一。不论是用户体验（UX，User Experience）、交互设计（ID，Interaction Design），还是视觉/图形设计（Visual / Graphic Design），都能牵扯到用户界面设计。&lt;/p&gt;&lt;h4&gt;一、什么是用户界面设计？&lt;/h4&gt;&lt;p&gt;广泛来讲，用户界面是人与机器交流的媒介。用户向机器发出指令，机器随即开始一段进程，回复信息，并给出反馈。用户可以根据用户反馈进行下一步操作的决策。&lt;/p&gt;&lt;p&gt;人机交互（HCI，Human Computer Interaciton）所关注的主要是数字界面，即过去的打孔机、命令行，直至今天的图形界面（GUI，Graphic Design）。&lt;/p&gt;&lt;p&gt;用户界面设计对于数码产品来说主要关注的是布局、信息结构，以及界面元素在显示屏和各种终端平台上的展示。电子游戏和电视界面也包括其中。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1610615827', '1610615827');
INSERT INTO `ey_article_content` VALUES ('7', '12', '&lt;p&gt;北京时间 5 月 31 日凌晨，有“互联网女皇”之称的玛丽·米克尔发布了 2018 年的互联网趋势报告，这也是她第 23 年公布互联网报告。&lt;br/&gt;&lt;/p&gt;&lt;p&gt;每年的互联网女皇报告几乎都会成为每个互联网创业者的必读报告。那么，互联网女皇是谁?为什么她的报告会如此受关注呢?&lt;/p&gt;&lt;p&gt;互联网女皇： 90 年代华尔街的象征&lt;/p&gt;&lt;p&gt;1958 年 9 月，玛丽·米克尔(Mary Meeker)出生于美国印第安纳州。&lt;/p&gt;&lt;p&gt;1982 年，米克尔加入了当时最负盛名的券商美林公司，担任股票经纪人。&lt;/p&gt;&lt;p&gt;米克尔的明星分析师之路开始于 1991 年，这年她加入了知名投行摩根士丹利，开始了自己辉煌的科技分析师生涯。&lt;/p&gt;&lt;p&gt;自 1995 年以来，米克尔的工作随着网络潮流变化而变化，她逐重于研究雅虎、美国在线及亚马孙等知名公司将如何调整结构并相互竞争。&lt;/p&gt;&lt;p&gt;1996 年，玛丽·米克尔如愿地成为摩根·斯坦利技术股票分析部的负责人，还创造出了华尔街闪耀的新职业——互联网分析师。就像垃圾债券代表了 80 年代华尔街一样，玛丽·米克尔成了 90 年代华尔街的象征。&lt;/p&gt;&lt;p&gt;2010 年底，米克尔辞去摩根士丹利董事总经理的职位，离开华尔街，去到加州成为知名风投KPCB的合伙人。KPCB公司(Kleiner\r\n Perkins Caufield &amp;amp; Byers)成立于 1972 年，是美国最大的风险基金，其最得意的杰作是网景公司的创立。&lt;/p&gt;&lt;p&gt;互联网女皇报告：互联网领域的投资圣经、选股指南&lt;/p&gt;&lt;p&gt;1994 年，米克尔在《纽约时报》上偶然看到一篇讲述创业公司Mosaic研发网络浏览器的报道。米克尔立即意识到，这种网络浏览器可能会改变人们获取信息的方式。她随后就联系了Mosaic的两位创始人，并向华尔街投资者大力介绍这家公司。&lt;/p&gt;&lt;p&gt;Mosaic后来改名为网景，并在 1995 年在纽约上市。得益于米克尔与网景两位创始人的良好关系，摩根士丹利成为网景首次公开募股(IPO)的主承销商。&lt;/p&gt;&lt;p&gt;当年 8 月 9 日，网景上市首日收盘，股价从 14 美元的发行价暴增至 75 美元，创下了当时的上市公司首日涨幅记录。当年网景IPO也成为互联网时代到来的一大标志。&lt;/p&gt;&lt;p&gt;1995 年，除了负责网景的上市交易外，米克尔还与同事克里斯o德普开始发布《互联网报告》，并最早提出了“页面浏览量”等网络类股分析指标。这份报告被投资者视为互联网领域的投资圣经，并且成书公开发行，在整个科技行业引发了巨大反响。&lt;/p&gt;&lt;p&gt;1996- 1997 年，米克尔和摩根士丹利发布了《互联网广告报告》与《互联网零售业报告》，一举奠定了米克尔互联网领域第一分析师的地位。互联网女皇报告几乎成为当时每个互联网创业者的必读报告。&lt;/p&gt;&lt;p&gt;互联网女皇报告，无异于选股指南。她向投资者推荐的美国在线、戴尔、亚马逊、eBay等公司股票，都很快带来了超过十倍的投资回报。&lt;/p&gt;&lt;p&gt;互联网女皇报告中的“神预测”&lt;/p&gt;&lt;p&gt;业界如此看重互联网女皇报告的最主要原因，在于米克尔的那些神预测。以下，我们简单罗列了几点互联网女皇报告中的神预测例子。&lt;/p&gt;', '1610615823', '1610615823');
INSERT INTO `ey_article_content` VALUES ('8', '13', '&lt;p&gt;网站建设的五大核心要素&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;/abc/uploads/ueditor/20190114/75c3c73acccc98cc5553d39eabf5fb38.jpg&quot; title=&quot;网站建设的五大核心要素(图1)&quot; alt=&quot;网站建设的五大核心要素(图1)&quot;/&gt;&lt;/p&gt;&lt;p&gt;　　企业要实行网络营销，首先需要进行网站制作。网站是由众多的Web页面组成的，而这些页面设计的好坏，直接影响到这个网站能否得到用户的欢迎。判断一个主页设计的好坏，要从多方面综合考虑，不能仅仅看它设计得是否生动漂亮，而应该看这个网站能否最大限度地替用户考虑。&lt;/p&gt;&lt;p&gt;&lt;img title=&quot;网站建设的五大核心要素(图2)&quot; alt=&quot;网站建设的五大核心要素(图2)&quot; class=&quot;limg&quot; src=&quot;http://www.eyoucms.com/uploads/allimg/180426/1510032P3-1.jpg&quot;/&gt;&lt;/p&gt;&lt;p&gt;　　3、以产品为核心原则&lt;/p&gt;&lt;p&gt;　　网站制作最重要的目的及功能就是为产品展示。顾客访问网站的主要目的是为了对产品和服务进行深入的了解，网站的价值也就在于灵活地向用户展示产品说明及图片甚至多媒体信息，即使一个功能简单的网站至少也相当于一本可以随时更新的产品宣传资料。过时的产品信息或者产品信息不完善不仅无法促进销售，同时也影响顾客的信心。顾客在访问网站时，关心的不是个人的信息，而是能够提供什么样的产品、产品的优势是什么。所以，以产品为核心是网站成功的一首要前提。&lt;/p&gt;&lt;p&gt;　　产品信息一般应该包括以下几方面内容：产品名称产品规格、产品用途、产品特性、产品认证情况及产品图片等。其次，产品规格、产品用途和产品特性等信息应尽可能详细地描述。&lt;/p&gt;&lt;p&gt;　　4、以网站的信息交互能力强为原则&lt;/p&gt;&lt;p&gt;　　如果一个网站只能提供浏览者浏览，而不能引导浏览者参与到网站内容的一部分建设中，那么它的吸引力是有限的。只有当浏览者能够很方便地和信息发布者交流信息时，该网站的魅力才能充分体现出来。虚拟论坛的设计在产品使用者之间、产品使用者与产品开发经理之间展开对产品的各种讨论。在线营销人员还可以借此收集市场信息，制定有效的营销计划。而网站消费者的反馈信息直接在网上公布，能够吸引消费者回访该网站，并由此可形成与顾客的固定关系。&lt;/p&gt;&lt;p&gt;　　当顾客在网上找到感兴趣的产品时，如何针对该产品及时进行询价和反馈?这不仅仅是通过电子函件方式就能够实现的。网站上应该提供相应的信息反馈模块，使顾客能够针对某个或多个产品方便快捷地进行询价或反馈。同时，企业的业务员应该能够及时查到顾客的反馈信息并及时回复：每个业务部门或业务员应该能够针对其发布的产品，方便地管理顾客的信息和反馈信息。通过网站可以为顾客提供各种在线服务和帮助信息，比如常见问题解答(FAQ)、详尽的联系信息、在线填写寻求帮助的表单、通过聊天实时回答顾客的咨询等。同时，利用网站还可以实现增进顾客关系的目的，比如通过发行各种免费邮件列表、提供有奖竞猜等方式吸引用户的参与。通过网站上的在线调查表，可以获得用户的反馈信息，用于产品调查、消费者行为调查、品牌形象调查等，是获得第一手市场资料有效的调查工具。&lt;/p&gt;&lt;p&gt;　　5、以完善的检索能力为原则&lt;/p&gt;&lt;p&gt;　　对于一个网站来说，如何合理地组织自己要发布的信息内容，以便让浏览者能够快速、准确地找到要找的信息，这是一个网站内容组织是否成功的关键。如果网站的结构设计不能使顾客方便、快捷地找到所需的信息，再好的设计也不能吸引长久的客户。即使将他吸引到了网站主页，将来也会中断访问。为了达到上述设计目标，一些网站在网页上设计了信息索引和目录索引。使用者能很快地找到感兴趣的那部分信息。&lt;/p&gt;&lt;p&gt;　　因此，为了网站内容的实用，有一定规模的网站一定要提供检索功能，以便于用户查找本网站的信息。为了给浏览者创造方便条件，网页设计者经常将网页内容设计成树形结构，方便纵向查询。访问者从主页开始就可以层层深入到所有“树权”和“树梢”的信息内容。另外，还可以设计一个搜索系统，让访问者很容易地就找到相关的内容。网址的搜索系统，设计应相当周全，允许访问者从任一页面进入。同时，在网站的任何一个页面都要设计有“返回主页”的链接，以方便访问者回到“树干”。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1610615818', '1610615818');
INSERT INTO `ey_article_content` VALUES ('9', '14', '&lt;p&gt;网站建设，静态页面和动态页面如何选择&lt;/p&gt;&lt;p&gt;　　电商网站建设为什么要使用静态页面制作。我们都知道，网站制作有分为静态页面制作和动态网页制作，那么建设电商网站采用哪种网站设计技术更好呢?&lt;/p&gt;&lt;p&gt;　　我们建设网站最终目的是为了给用户浏览，所以从用户的角度出发进行思考才是最实际的，使用动态网页制作技术虽然网页美观度大大提升了，但是却不利于网站优化，今天小编重点和大家谈谈，网站建设为什么要使用静态页面制作。&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;/abc/uploads/ueditor/20190114/47caf8cc457ff50c8a66f4c4a23cfeb1.png&quot; title=&quot;网站建设，静态页面和动态页面如何选择(图1)&quot; alt=&quot;网站建设，静态页面和动态页面如何选择(图1)&quot;/&gt;&lt;/p&gt;&lt;p&gt;　　做静态网站建设所采用的技术原理是一对一的形式，也就是说这样的网站上面，一个内容对应的就是一个页面，无论网站访问者如何操作都只是让服务器把固有的数据传送给请求者，没有脚本计算和后台数据库读取过程，大大降低了部分安全隐患。静态网站设计除了拥有上述的速度快，安全性高这两个特点之外还具有跨平台，跨服务器功能。&lt;/p&gt;&lt;p&gt;　　现在熟悉搜索引擎原理工作原理的朋友应该都知道，它所提供给广大用户的信息是本身就存在于数据库当中的信息而不是实时的信息，固定的信息内容更容易接受和保存。我们可能常常会遇到这样的问题，当我们搜索自己所需要的信息时得出来的结果可能已经失效，这就是静态页面网站设计的不足之处，但又因为它的稳定，所以久久不会被删除。&lt;/p&gt;&lt;p&gt;　　与静态页面网站设计不同，生成的动态页面信息不但不易被搜索引擎所检索，而且打开速度慢，再者也不稳定，这就是为什么这么多专业网站建设公司都一再建议客户使用静态形式的网站设计的原因，有些网站建设公司会考虑把页面进行伪静态处理，但不知道大家有没有注意过，伪静态处理的URL通常是不规则的。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1610615811', '1610615811');
INSERT INTO `ey_article_content` VALUES ('10', '19', '&lt;p&gt;从三方面完美的体验企业网站的核心价值&lt;/p&gt;&lt;p&gt;　　随着互联网的迅猛发展，一个企业的发展离不开互联网的发展，企业注重企业网站建设，那么必然会给其带来不错的效果。企业网站建设其核心价值直接体现在网站对于用户和商家而言，是否能够满足他们利益需求，能否提高企业发展，提高企业的发展渠道。&lt;/p&gt;&lt;p&gt;&lt;img title=&quot;从三方面完美的体验企业网站的核心价值(图1)&quot; alt=&quot;从三方面完美的体验企业网站的核心价值(图1)&quot; class=&quot;rimg&quot; src=&quot;http://www.eyoucms.com/uploads/allimg/180426/150RQ155-0.jpg&quot;/&gt;&lt;/p&gt;&lt;p&gt;　　一个好的导航系统就是一个好的导游，认为每一个网站设计方案都有权利与义务帮助客户及时准确的找到自己感兴趣的内容主体和需要的东西。&lt;/p&gt;&lt;p&gt;　　另一方面体现在网站对商家现金利益需求的满足，而此却建立在网站对用户需求满足的基础之上。因为，如果网站不能够满足用户利益的需求，用户就不会为网站创造价值，不能吸引更多的用户参与到网站中来，不能实现网站价值循环式的增长，用户规模将会无法得到较大发展，很难实现对商家现金利益需求的满足，商家在网站投放广告是基于网站促进发生交易可能性的大小，交易可能性越大，商家才可以获得更大的现金利益，否则，将会白白浪费广告费。&lt;/p&gt;&lt;p&gt;　　其次体现在对用户利益需求的满足，网站在发展初期更多的是要为用户提供他们需求的内容，积极的创造内容价值，满足用户各种基础性利益的需求，尤其是各类疑问的解答，相关兴趣或者专业资料的提供，各种资讯信息的发布。让用户能够基于某一种原因留下来，在基础性的工作做好的前提下，您可以着力于用户交易利益需求的满足，或者开始就将交易与用户的相关需求结合起来，打造一个个活跃度高的交易类版块，为用户提供此类交易最全面、最方便的资料和场所，积极促进用户活跃度的提高和迅速实现网站盈利。&lt;/p&gt;&lt;p&gt;　　我们认为让客户在首页即可看到与自己寻找的讯息高度相关的行业信息是非常明智的抉择，一个没有大量行业专业信息体现的网站设计称不上合格的网站设计，也无法真正的为客户从根本上解决问题。&lt;/p&gt;&lt;p&gt;　　我们只有尽量的在网站设计当中体现出如何才能在众多的行业竞争对手中脱颖而出，让客户可以信任我们呢?网站建设公司认为唯有尽量表现出自己的专业实力方可,当然除了这三点之外，网站设计仍旧有很多需要注意的地方，但不管怎么样，核心价值还是应该要重点体现，将重点放在核心内容上才是网站设计的真谛， 我们知道网站运营的核心理念是价值，站长们务必牢牢树立，一切从用户出发，积极满足用户需求，让用户发挥创造力，为网站创造价值，实现网站价值循环式增长，让站长运营变成用户运营是我们的终极目标，一劳永逸，盈利不断。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1610615806', '1610615806');
INSERT INTO `ey_article_content` VALUES ('11', '20', '&lt;p&gt;随着网络应用的丰富和发展，很多网站往往不能迅速跟进大量信息衍生及业务模式变革的脚步，常常需要花费许多时间、人力和物力来处理信息更新和维护工作；遇到网站扩充的时候，整合内外网及分支网站的工作就变得更加复杂，甚至还需重新建设网站；如此下去，用户始终在一个高成本、低效率的循环中升级、整合…&lt;/p&gt;&lt;p&gt;于是，我们听到许多用户这样的反馈：&lt;/p&gt;&lt;p&gt;页面制作无序，网站风格不统一，大量信息堆积，发布显得异常沉重；&lt;/p&gt;&lt;p&gt;内容繁杂，手工管理效率低下，手工链接视音频信息经常无法实现；&lt;/p&gt;&lt;p&gt;应用难度较高，许多工作需要技术人员配合才能完成，角色分工不明确；&lt;/p&gt;&lt;p&gt;改版工作量大，系统扩展能力差，集成其它应用时更是降低了灵活性；&lt;/p&gt;&lt;p&gt;对于网站建设和信息发布人员来说，他们最关注的系统的易用性和的功能的完善性，因此，这对网站建设和信息发布工具提出了一个很高的要求。&lt;/p&gt;&lt;p&gt;首先，角色定位明确，以充分保证工作人员的工作效率；其次，功能完整，满足各门道&amp;quot;把关人&amp;quot;应用所需，使信息发布准确无误。比如，为编辑、美工、主编及运维人员设置权限和实时管理功能。&lt;/p&gt;&lt;p&gt;此外，保障网站架构的安全性也是用户关注的焦点。能有效管理网站访问者的登陆权限，使内网数据库不受攻击，从而时刻保证网站的安全稳定，免于用户的后顾之忧。&lt;/p&gt;&lt;p&gt;根据以上需求，一套专业的内容管理系统CMS应运而生，来有效解决用户网站建设与信息发布中常见的问题和需求。对网站内容管理是该软件的最大优势，它流程完善、功能丰富，可把稿件分门别类并授权给合法用户编辑管理，而不需要用户去理会那些难懂的SQL语法。&lt;/p&gt;', '1610615802', '1610615802');
INSERT INTO `ey_article_content` VALUES ('12', '21', '&lt;p&gt;SEO（搜索引擎优化）和有效的网站设计是齐头并进的。好的网站设计是关于创建一个吸引目标受众的网站，并让他们采取某种行动。但是，如果该网站不遵循目前的 SEO 最佳做法，它的排名将会受到影响，从而会导致真正参与该网站的访问者的数量的较少。&lt;/p&gt;&lt;p&gt;相反地，如果将关注的焦点放在搜索引擎优化以及如何取悦搜索引擎蜘蛛上，那么网站可能会排名很高，并且会获得大量的搜索引擎流量，但是如果设计很不尽人意，那就不一样了。为了在当今的数字环境中取得成功，必须将重点放在网站设计和搜索引擎优化上。&lt;/p&gt;&lt;p&gt;一、但是，SEO 不会扼杀掉网页设计师的创造力吗？&lt;/p&gt;&lt;p&gt;在过去的五年中，对“优化设计”的巨大需求已经被网页设计师所接受。在此之前，设计师们主要关注的是用户的体验，而不是“机器人”的体验。&lt;/p&gt;&lt;p&gt;如今，设计师不仅要让网站看起来有吸引力，而且要确保行为召唤必须符合网站页面“折叠”的要求，网站的加载速度必须很快，必须使用面包屑路径，清晰明了的导航选择，必须使用&amp;nbsp;CSS，JavaScript 文件必须保持在最低限度…这是一项艰巨的任务。&lt;/p&gt;&lt;p&gt;一些设计师可能想知道，所有这些新的 SEO 规则是否会损害创建网站的自由？&lt;/p&gt;&lt;p&gt;对于“干净”的网站设计而言，它可以帮助一个网站快速加载，容易被搜索引擎蜘蛛抓取。因此，在现实中，创造力和最优化需要能够同时在一起“蓬勃发展”。&lt;/p&gt;&lt;p&gt;二、把它们结合在一起&lt;/p&gt;&lt;p&gt;有一些核心元素支持每一个 SEO 策略和网站设计项目：&lt;/p&gt;&lt;p&gt;1.　关键词分析&lt;/p&gt;&lt;p&gt;在启动一个商业网站项目时，必须进行彻底的关键词分析。为了做到这一点，网页设计师必须紧密深入地了解客户的目标受众，并定义受众中的人口结构是如何融入到企业正试图达到的更大的目标市场。然后，应该对网站进行适当的关键词/长尾关键词优化。&lt;/p&gt;&lt;p&gt;2.　内容层次结构&lt;/p&gt;&lt;p&gt;对于一个企业来说，创建好的内容是不够的，他们还必须在战略上规划内容的位置。&lt;/p&gt;&lt;p&gt;有效的计划意味着将相关的内容放到虚拟的容器中，通过创造性的设计和内部链接让内容层级结构一目了然。并且，一个经过优化的网站是对用户和搜索引擎蜘蛛都很友好的网站。&lt;/p&gt;&lt;p&gt;3.　从用户的角度思考&lt;/p&gt;&lt;p&gt;通常情况下，你的网站有越多的页面或文章，目标用户找到你的机会就越多。当他们着陆这些特定的页面的时候，你需要确保你能帮助他们轻松的找到你。&lt;/p&gt;&lt;p&gt;所以你必须从用户的角度进行思考，要让用户立即清楚地知道他们在进行访问的页面的当前位置，并帮助用户在尽可能少的点击下从页面转换到另一页面。&lt;/p&gt;&lt;p&gt;三、为什么&amp;nbsp;SEO 策略如此重要？&lt;/p&gt;&lt;p&gt;拥有合适的网站结构和信息架构，最终将会帮助企业提供一种引人入胜的用户体验，同时减少对每一次新增长的需求。但是，除非你的品牌是众所周知的，否则通常是搜索引擎对网站所收到的大部分流量负责。SEO 策略有能力利用重要的客户数据，挖掘新的潜在收入流。&lt;/p&gt;&lt;p&gt;对于那些试图进行搜索引擎优化的网站所有者来说，有一些地方经常是麻烦的。现在，我将为网站所有者提供搜索引擎优化建议，以获得更高排名的页面。&lt;/p&gt;&lt;p&gt;1. &amp;nbsp;URL 结构&lt;/p&gt;&lt;p&gt;大多数网站创建的 URL 都包含很多随机字符，比如问号，没有关键词或任何有价值的内容。当搜索引擎的 URL 包含 SEO 的关键词或短语时，页面将会在搜索引擎中排名更高。因此，在 URL 中设置关键词非常重要。&lt;/p&gt;&lt;p&gt;2.　页面的标题&lt;/p&gt;&lt;p&gt;搜索引擎排名中最重要的因素之一是页面标题。不过，许多网站并没有改变他们的网页标题。在青柠建站平台中，你可以通过使用 SEO 标题标签插件，它很容易让你为你的文章和页面创建标题。&lt;/p&gt;&lt;p&gt;3.　重复的内容&lt;/p&gt;&lt;p&gt;没有一个搜索引擎喜欢看到重复的内容。重复内容是一些网站的主要问题，因为类别页面和日历/日期页面经常会导致搜索引擎在多个页面上找到相同的内容。&lt;/p&gt;&lt;p&gt;对于网站所有者来说，有几种方法可以克服重复的内容问题。其中一种方法是使用 robot.txt 文件，用来指导搜索引擎哪些页面应该被忽略，只留下要索引的主要页面。&lt;/p&gt;&lt;p&gt;4. &amp;nbsp;Meta 标签&lt;/p&gt;&lt;p&gt;在设计一个传统的静态网站时，你可以为每个页面输入元标签（描述）。尽管这些标签在搜索引擎排名上的影响力没有以前那么大，但在你的页面上有这些标签并不会带来什么坏处。&lt;/p&gt;&lt;p&gt;然而，大多数建站平台并没有给用户在写文章时添加元标签的选项。对于 青柠建站平台 用户来说，添加元标签插件将允许你为任何页面输入元标签。&lt;/p&gt;&lt;p&gt;四、网页设计师在 SEO 方面的职责是什么？&lt;/p&gt;&lt;p&gt;搜索引擎优化是一个持续的过程，它不能通过以特定的方式设计一个网站来实现。当然，网页设计师应该付出相当大的努力来帮助客户构建一个优化的站点，但是网页设计师在 SEO 方面的职责是什么，以及客户的职责是什么？&lt;/p&gt;&lt;p&gt;作为一个企业主，你的网站的优化对你来说比设计师更重要（这并不是说设计师不关心，但是设计师的注意力通常集中在网站的视觉和功能上）。你比设计师更了解你的客户 / 潜在客户，所以你应该对你的目标有更多的建设性意见。&lt;/p&gt;&lt;p&gt;也许有些客户对 SEO 和目标关键词可能不太了解，那么理想的情况是让客户和你在这个问题上协同工作。&lt;/p&gt;&lt;p&gt;根据我的经验，让客户参与其中的最简单方法之一就是简单地解释网站上使用的词语和短语（标题、文案等）会对网站排名有直接的影响。&lt;/p&gt;&lt;p&gt;我通常会要求客户给我一份他们认为潜在访问者可能会在搜索中使用的词语和短语列表。在我不太熟悉的行业中设计网站时，这一点尤其重要。&lt;/p&gt;&lt;p&gt;当然，可能需要做一些研究。客户应该承担起关键词研究的责任，还是应该由设计师来负责？&lt;/p&gt;&lt;p&gt;我的经验是，如果客户参与进来，这项研究通常会更有效，但这并不总是可能的。设计师应该有足够的知识来为客户提供建议，并且应该愿意提供帮助，但是最终最好还是让客户尽可能地参与进来。事实上，如果客户关心 SEO，参与过程会达到一个更加合理的期望。&lt;/p&gt;&lt;p&gt;设定现实的期望也可能是设计师的责任。&lt;/p&gt;&lt;p&gt;我有一些潜在的客户来找我说：“我被 SEO 专家告知，只要在网站页面上插入竞争热门的关键词就可以让我的网站排名第一或第二”。&lt;/p&gt;&lt;p&gt;在这种情况下，我会很明显地会指出，“搜索引擎优化需要持续的工作，而这种工作通常不能通过以某种方式创建网站来完成的。”&lt;/p&gt;&lt;p&gt;我经常建议客户在他们的网站上添加一个博客，以获得更多的内容，并提高排名的机会。&lt;/p&gt;&lt;p&gt;结语&lt;/p&gt;&lt;p&gt;虽然这只是一个简短的总结，但这些是网站所有者和设计师将面对的最重要的 SEO 话题。通过了解这些知识，你可以更好地创建出对用户和搜索引擎都友好的网站。&lt;/p&gt;&lt;p&gt;本文由易优小编设计 原创授权发布易优网站，未经授权，转载必究。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1610615796', '1610615796');
INSERT INTO `ey_article_content` VALUES ('14', '38', '&lt;p&gt;SEO很多伙伴都了解，就是搜索引擎排名优化，通过对网站内部和外部进行优化当用户搜索相应关键词时网站能够排名在搜索引擎前面，具体可以百度搜索“网络营销课程”查看商梦网校操作的案例！&lt;/p&gt;&lt;p&gt;但单页SEO很多伙伴可能会有点陌生，单页SEO是将单页网站与内容内容结合为一体的SEO优化方案，主要是提升网站流量利用率让用户打开网站就能看到目标页面，转换更多订单，创造更多收益。单页SEO的操作理念也是由商梦网校提出，并一起推荐操作大家的模式。&lt;/p&gt;&lt;p&gt;那什么又是单页SEO站群呢，因为操作SEO成功率并不是100%，也就是意味着你做了并不会绝对有排名。因为在任何时候搜索引擎，特别是百度的索引数据库里，只有60%的网页数量。也就是说，大量的网页它是没有收录进来，它本身的能力所限无法做到中文的所有几百亿个网页都收录进来。所以，对于大部分网站，都有被删除网页，没有排名，或被K的经历，或没有排名。处理办法：坦然面对这一切。一个网站的成本才多少钱？如果因此对SEO失去信心，那就是最大的失去了。&lt;/p&gt;&lt;p&gt;不过我们也想到了一个更好的解决方案，这个方案在最早期我们开始操作，并且取得了非常不错的成绩就是“站群”，我们可以假设一个网站排名的机会为1，如果我们用 10 个网站来进行优化排名机会可以提升 10 倍， 10 个网站我们也不要求都获得排名只需要有1- 3 个网站获得排名这个操作就是成功的，因为对于我们做站群来说投入 10 个网站的成本也就 1000 块左右；这个投资也是非常划算的，这个思路其实有点像竞价，不像传统的SEO，因为传统的SEO我们投资一个网站成本一两百，就想获得排名，然后给我们几百上千倍的回报。结果就相当于我们把希望寄托在一颗树上，结果这颗树没有开花结果，我们就饿死了。&lt;/p&gt;&lt;p&gt;想给一个项目建立数十个网站也是需要掌握很多技术的，特别是批量建站方面，以及后期的维护。这次商梦网校升级加入的单页SEO站群操作方法，没有长篇大论直接给你演示怎么干，你只需要复制我们提供的方法就可以了。当然这里面也有很多核心的技术，比如域名注册和空间购买技巧虽然非常简单，但是直接会影响我们后期操作结果，我们给提供的技巧也会将你的成本降到低，如果投资建立 10 个网站域名与空间的成本不到 1000 元。相当于 1000 你就可以启动一个站群项目。核心的还是文章的采集，我们的原理是利用火车头采集原创文章然后实现挂机自动发布，只需要设置好每天几点运行软件就会自动更新网站文章，还会自动网站自动瞄文本，自动加入关键词。这些很多同学可能会问会不会太复杂，可以这样告诉你复杂的工作我们已经帮你搞定，到你使用的时候已经是打包好的解决方案。&lt;/p&gt;&lt;p&gt;网站前期整体搭建只要花时间就能搞定，但真正考验人的基实还是在于后期优化，对于网站后期优化特别是外链增加收录和权重这一块，我们还是没有长篇大论会直接给你演示实用、高效的方法让你的站群快速的获得收录，增加权重，获得排名，你需要做的就拷贝我们商梦网校的方法和模式；这些经验都是我们长期操作整理下来的，并非几天修炼的结果。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1610615746', '1610615746');
INSERT INTO `ey_article_content` VALUES ('31', '72', '', '1563502397', '1563502397');
INSERT INTO `ey_article_content` VALUES ('32', '73', '', '1563502433', '1563502433');
INSERT INTO `ey_article_content` VALUES ('33', '74', '', '1563502458', '1563502458');
INSERT INTO `ey_article_content` VALUES ('34', '75', '', '1563502473', '1563502473');
INSERT INTO `ey_article_content` VALUES ('35', '76', '', '1563502499', '1563502499');
INSERT INTO `ey_article_content` VALUES ('36', '77', '', '1563502542', '1563502542');
INSERT INTO `ey_article_content` VALUES ('37', '78', '', '1563502559', '1563502559');
INSERT INTO `ey_article_content` VALUES ('38', '79', '', '1563502578', '1563502578');
INSERT INTO `ey_article_content` VALUES ('39', '80', '', '1563502596', '1563502596');
INSERT INTO `ey_article_content` VALUES ('40', '81', '', '1563502609', '1563502609');
INSERT INTO `ey_article_content` VALUES ('41', '84', '', '1610615703', '1610615703');
INSERT INTO `ey_article_content` VALUES ('42', '85', '', '1610615688', '1610615688');
INSERT INTO `ey_article_content` VALUES ('43', '86', '', '1610615683', '1610615683');
INSERT INTO `ey_article_content` VALUES ('44', '87', '', '1610615678', '1610615678');

-- -----------------------------
-- Table structure for `ey_article_order`
-- -----------------------------
DROP TABLE IF EXISTS `ey_article_order`;
CREATE TABLE `ey_article_order` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '文章订单ID',
  `order_code` varchar(20) NOT NULL DEFAULT '' COMMENT '媒体订单编号',
  `users_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `order_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单状态：0未付款，1已付款',
  `order_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单应付总金额',
  `pay_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间',
  `pay_name` varchar(20) NOT NULL DEFAULT '' COMMENT '支付方式名称',
  `wechat_pay_type` varchar(20) NOT NULL DEFAULT '' COMMENT '微信支付时，标记使用的支付类型（扫码支付，微信内部，微信H5页面）',
  `pay_details` text COMMENT '支付时返回的数据，以serialize序列化后存入，用于后续查询。',
  `product_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '视频文档ID',
  `product_name` varchar(100) DEFAULT '' COMMENT '视频文档名称',
  `product_litpic` varchar(500) DEFAULT '' COMMENT '视频文档封面图片',
  `lang` varchar(30) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned DEFAULT '0' COMMENT '下单时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_code` (`order_code`) USING BTREE,
  KEY `users_id` (`users_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章订单表';


-- -----------------------------
-- Table structure for `ey_article_pay`
-- -----------------------------
DROP TABLE IF EXISTS `ey_article_pay`;
CREATE TABLE `ey_article_pay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `aid` int(11) DEFAULT '0',
  `part_free` tinyint(1) DEFAULT '0' COMMENT '是否试看 0-否 1-是',
  `size` int(11) DEFAULT '1' COMMENT 'KB',
  `free_content` longtext COMMENT '试看内容',
  `add_time` int(11) DEFAULT '0',
  `update_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章付费预览表';


-- -----------------------------
-- Table structure for `ey_ask`
-- -----------------------------
DROP TABLE IF EXISTS `ey_ask`;
CREATE TABLE `ey_ask` (
  `ask_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `type_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '问题栏目ID',
  `users_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `ask_title` varchar(80) NOT NULL DEFAULT '' COMMENT '问题标题',
  `is_recom` tinyint(1) NOT NULL DEFAULT '0' COMMENT '问题是否推荐',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '问题状态：0未解决，1已解决，2已关闭',
  `click` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览点击量',
  `replies` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '问题回复量',
  `content` text NOT NULL COMMENT '问题内容',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '问题网址',
  `users_ip` varchar(50) NOT NULL DEFAULT '' COMMENT '问题发布时IP地址',
  `is_review` tinyint(1) NOT NULL DEFAULT '1' COMMENT '问题是否审核，1是，0否',
  `follow` tinyint(1) NOT NULL DEFAULT '0' COMMENT '关注问题则表示有回复时发送邮件通知到问题发布人',
  `solve_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '解决时间(这个问题存在最佳答案则表示已解决)',
  `bestanswer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最佳答案',
  `sort_order` int(10) NOT NULL DEFAULT '100' COMMENT '排序号',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `money` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '悬赏金额',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '1-删除',
  PRIMARY KEY (`ask_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='问题表';


-- -----------------------------
-- Table structure for `ey_ask_answer`
-- -----------------------------
DROP TABLE IF EXISTS `ey_ask_answer`;
CREATE TABLE `ey_ask_answer` (
  `answer_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `ask_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '问题ID',
  `is_bestanswer` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否最佳答案，0否，1是',
  `is_review` tinyint(1) NOT NULL DEFAULT '1' COMMENT '问题是否审核，1是，0否',
  `type_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '问题栏目ID',
  `users_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
  `click_like` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点赞量',
  `users_ip` varchar(30) NOT NULL DEFAULT '' COMMENT '用户IP地址',
  `content` text NOT NULL COMMENT '内容',
  `ifcheck` tinyint(1) NOT NULL DEFAULT '1',
  `answer_pid` int(10) NOT NULL DEFAULT '0' COMMENT '子答案的父答案',
  `at_users_id` int(10) NOT NULL DEFAULT '0' COMMENT '被@的用户ID',
  `at_answer_id` int(10) NOT NULL DEFAULT '0' COMMENT '@答案ID',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-删除',
  PRIMARY KEY (`answer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='答案表';


-- -----------------------------
-- Table structure for `ey_ask_answer_like`
-- -----------------------------
DROP TABLE IF EXISTS `ey_ask_answer_like`;
CREATE TABLE `ey_ask_answer_like` (
  `like_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `ask_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '问题ID',
  `answer_id` int(10) NOT NULL DEFAULT '0' COMMENT '答案ID',
  `users_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `click_like` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点赞',
  `like_source` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '点赞来源，1=点赞提问(ask_id)，2=点赞评论(answer_id)，3=点赞回复(answer_id)，默认值为2，兼容以前的那些评论数据',
  `users_ip` varchar(30) NOT NULL DEFAULT '' COMMENT '用户IP地址',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`like_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='答案点赞表';


-- -----------------------------
-- Table structure for `ey_ask_score_level`
-- -----------------------------
DROP TABLE IF EXISTS `ey_ask_score_level`;
CREATE TABLE `ey_ask_score_level` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) DEFAULT '',
  `min` mediumint(8) DEFAULT '0',
  `max` mediumint(8) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='积分等级表';

-- -----------------------------
-- Records of `ey_ask_score_level`
-- -----------------------------
INSERT INTO `ey_ask_score_level` VALUES ('1', '青铜', '0', '1000');
INSERT INTO `ey_ask_score_level` VALUES ('2', '白银', '1001', '5000');
INSERT INTO `ey_ask_score_level` VALUES ('3', '黄金', '5001', '20000');
INSERT INTO `ey_ask_score_level` VALUES ('4', '王者', '20001', '0');

-- -----------------------------
-- Table structure for `ey_ask_type`
-- -----------------------------
DROP TABLE IF EXISTS `ey_ask_type`;
CREATE TABLE `ey_ask_type` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '栏目自增',
  `type_name` varchar(100) NOT NULL DEFAULT '' COMMENT '栏目名称',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级ID',
  `seo_title` varchar(200) DEFAULT '' COMMENT 'SEO标题',
  `seo_keywords` varchar(200) DEFAULT '' COMMENT 'seo关键字',
  `seo_description` text COMMENT 'seo描述',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '100' COMMENT '排序号',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='问题栏目分类表';

-- -----------------------------
-- Records of `ey_ask_type`
-- -----------------------------
INSERT INTO `ey_ask_type` VALUES ('1', '问题求助', '0', '', '', '', '100', '1565770890', '1611910466');
INSERT INTO `ey_ask_type` VALUES ('2', '功能建议', '0', '', '', '', '100', '1565770890', '1611910466');
INSERT INTO `ey_ask_type` VALUES ('3', 'BUG反馈', '1', '', '', '', '100', '1565771021', '1611910466');
INSERT INTO `ey_ask_type` VALUES ('4', '其他问题', '1', '', '', '', '100', '1565771021', '1611910466');
INSERT INTO `ey_ask_type` VALUES ('5', '业务咨询', '0', '', '', '', '100', '1611910466', '1611910466');

-- -----------------------------
-- Table structure for `ey_auth_role`
-- -----------------------------
DROP TABLE IF EXISTS `ey_auth_role`;
CREATE TABLE `ey_auth_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '' COMMENT '角色名',
  `pid` int(10) DEFAULT '0' COMMENT '父角色ID',
  `remark` text COMMENT '备注信息',
  `grade` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '级别',
  `language` text COMMENT '多语言权限',
  `online_update` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '在线升级',
  `only_oneself` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '只看自己发布',
  `check_oneself` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '发布文档自动通过审核，1--是，0--否',
  `cud` varchar(255) DEFAULT '' COMMENT '增改删',
  `permission` text COMMENT '已允许的权限',
  `built_in` tinyint(1) DEFAULT '0' COMMENT '内置用户组，1表示内置',
  `sort_order` int(10) DEFAULT '0' COMMENT '排序号',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态(1=正常，0=屏蔽)',
  `admin_id` int(10) DEFAULT '0' COMMENT '操作管理员ID',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='管理员角色表';

-- -----------------------------
-- Records of `ey_auth_role`
-- -----------------------------
INSERT INTO `ey_auth_role` VALUES ('1', '优化推广员', '0', '', '0', 'a:1:{i:0;s:2:\"cn\";}', '0', '1', '1', 'a:3:{i:0;s:3:\"add\";i:1;s:4:\"edit\";i:2;s:3:\"del\";}', 'a:2:{s:5:\"rules\";a:8:{i:0;s:1:\"1\";i:1;s:1:\"3\";i:2;s:1:\"4\";i:3;s:1:\"8\";i:4;s:1:\"9\";i:5;s:2:\"10\";i:6;s:2:\"14\";i:7;i:2;}s:7:\"arctype\";a:77:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";i:5;s:1:\"6\";i:6;s:2:\"33\";i:7;s:2:\"34\";i:8;s:2:\"35\";i:9;s:2:\"36\";i:10;s:2:\"37\";i:11;s:2:\"38\";i:12;s:2:\"39\";i:13;s:2:\"40\";i:14;s:2:\"41\";i:15;s:2:\"42\";i:16;s:2:\"43\";i:17;s:2:\"44\";i:18;s:2:\"45\";i:19;s:2:\"46\";i:20;s:2:\"47\";i:21;s:2:\"48\";i:22;s:1:\"8\";i:23;s:2:\"32\";i:24;s:1:\"9\";i:25;s:2:\"30\";i:26;s:2:\"31\";i:27;s:2:\"11\";i:28;s:2:\"12\";i:29;s:2:\"13\";i:30;s:2:\"23\";i:31;s:2:\"20\";i:32;s:2:\"24\";i:33;s:2:\"25\";i:34;s:2:\"21\";i:35;s:2:\"26\";i:36;s:2:\"22\";i:37;s:2:\"27\";i:38;s:2:\"28\";i:39;s:2:\"29\";i:40;s:2:\"31\";i:41;s:2:\"32\";i:42;s:2:\"33\";i:43;s:2:\"34\";i:44;s:2:\"35\";i:45;s:2:\"36\";i:46;s:2:\"37\";i:47;s:2:\"38\";i:48;s:2:\"39\";i:49;s:2:\"40\";i:50;s:2:\"41\";i:51;s:2:\"42\";i:52;s:2:\"43\";i:53;s:2:\"44\";i:54;s:2:\"45\";i:55;s:2:\"46\";i:56;s:2:\"47\";i:57;s:2:\"48\";i:58;s:2:\"49\";i:59;s:2:\"50\";i:60;s:2:\"51\";i:61;s:2:\"52\";i:62;s:2:\"53\";i:63;s:2:\"54\";i:64;s:2:\"55\";i:65;s:2:\"56\";i:66;s:2:\"57\";i:67;s:2:\"58\";i:68;s:2:\"59\";i:69;s:2:\"60\";i:70;s:2:\"61\";i:71;s:2:\"62\";i:72;s:2:\"63\";i:73;s:2:\"64\";i:74;s:2:\"65\";i:75;s:2:\"66\";i:76;s:2:\"67\";}}', '1', '100', '1', '0', '1541207843', '0');
INSERT INTO `ey_auth_role` VALUES ('2', '内容管理员', '0', '', '0', 'a:1:{i:0;s:2:\"cn\";}', '0', '1', '1', 'a:3:{i:0;s:3:\"add\";i:1;s:4:\"edit\";i:2;s:3:\"del\";}', 'a:2:{s:5:\"rules\";a:4:{i:0;s:1:\"1\";i:1;s:2:\"10\";i:2;s:2:\"14\";i:3;i:2;}s:7:\"arctype\";a:77:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";i:5;s:1:\"6\";i:6;s:2:\"33\";i:7;s:2:\"34\";i:8;s:2:\"35\";i:9;s:2:\"36\";i:10;s:2:\"37\";i:11;s:2:\"38\";i:12;s:2:\"39\";i:13;s:2:\"40\";i:14;s:2:\"41\";i:15;s:2:\"42\";i:16;s:2:\"43\";i:17;s:2:\"44\";i:18;s:2:\"45\";i:19;s:2:\"46\";i:20;s:2:\"47\";i:21;s:2:\"48\";i:22;s:1:\"8\";i:23;s:2:\"32\";i:24;s:1:\"9\";i:25;s:2:\"30\";i:26;s:2:\"31\";i:27;s:2:\"11\";i:28;s:2:\"12\";i:29;s:2:\"13\";i:30;s:2:\"23\";i:31;s:2:\"20\";i:32;s:2:\"24\";i:33;s:2:\"25\";i:34;s:2:\"21\";i:35;s:2:\"26\";i:36;s:2:\"22\";i:37;s:2:\"27\";i:38;s:2:\"28\";i:39;s:2:\"29\";i:40;s:2:\"31\";i:41;s:2:\"32\";i:42;s:2:\"33\";i:43;s:2:\"34\";i:44;s:2:\"35\";i:45;s:2:\"36\";i:46;s:2:\"37\";i:47;s:2:\"38\";i:48;s:2:\"39\";i:49;s:2:\"40\";i:50;s:2:\"41\";i:51;s:2:\"42\";i:52;s:2:\"43\";i:53;s:2:\"44\";i:54;s:2:\"45\";i:55;s:2:\"46\";i:56;s:2:\"47\";i:57;s:2:\"48\";i:58;s:2:\"49\";i:59;s:2:\"50\";i:60;s:2:\"51\";i:61;s:2:\"52\";i:62;s:2:\"53\";i:63;s:2:\"54\";i:64;s:2:\"55\";i:65;s:2:\"56\";i:66;s:2:\"57\";i:67;s:2:\"58\";i:68;s:2:\"59\";i:69;s:2:\"60\";i:70;s:2:\"61\";i:71;s:2:\"62\";i:72;s:2:\"63\";i:73;s:2:\"64\";i:74;s:2:\"65\";i:75;s:2:\"66\";i:76;s:2:\"67\";}}', '1', '100', '1', '0', '1541207846', '0');

-- -----------------------------
-- Table structure for `ey_channelfield`
-- -----------------------------
DROP TABLE IF EXISTS `ey_channelfield`;
CREATE TABLE `ey_channelfield` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '字段名称',
  `channel_id` int(10) NOT NULL DEFAULT '0' COMMENT '所属文档模型id',
  `title` varchar(250) NOT NULL DEFAULT '' COMMENT '字段标题',
  `dtype` varchar(32) NOT NULL DEFAULT '' COMMENT '字段类型',
  `define` text NOT NULL COMMENT '字段定义',
  `maxlength` int(10) NOT NULL DEFAULT '0' COMMENT '最大长度，文本数据必须填写，大于255为text类型',
  `dfvalue` text NOT NULL COMMENT '默认值',
  `dfvalue_unit` varchar(50) NOT NULL DEFAULT '' COMMENT '数值单位',
  `remark` varchar(256) NOT NULL DEFAULT '' COMMENT '提示说明',
  `is_screening` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否应用于条件筛选',
  `is_release` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否应用于会员投稿发布',
  `ifeditable` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否在编辑页显示',
  `ifrequire` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否必填',
  `ifsystem` tinyint(1) NOT NULL DEFAULT '0' COMMENT '字段分类，1=系统(不可修改)，0=自定义',
  `ifmain` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否主表字段',
  `ifcontrol` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态，控制该条数据是否允许被控制，1为不允许控制，0为允许控制',
  `sort_order` int(5) NOT NULL DEFAULT '100' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=408 DEFAULT CHARSET=utf8 COMMENT='自定义字段表';

-- -----------------------------
-- Records of `ey_channelfield`
-- -----------------------------
INSERT INTO `ey_channelfield` VALUES ('1', 'add_time', '0', '新增时间', 'datetime', 'int(11)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533091575', '1533091575');
INSERT INTO `ey_channelfield` VALUES ('2', 'update_time', '0', '更新时间', 'datetime', 'int(11)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533091601', '1533091601');
INSERT INTO `ey_channelfield` VALUES ('3', 'aid', '0', '文档ID', 'int', 'int(11)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533091624', '1533091624');
INSERT INTO `ey_channelfield` VALUES ('4', 'typeid', '0', '当前栏目ID', 'int', 'int(11)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533091930', '1533091930');
INSERT INTO `ey_channelfield` VALUES ('5', 'channel', '0', '模型ID', 'int', 'int(11)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092214', '1533092214');
INSERT INTO `ey_channelfield` VALUES ('6', 'is_b', '0', '是否加粗', 'switch', 'tinyint(1)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092246', '1533092246');
INSERT INTO `ey_channelfield` VALUES ('7', 'title', '0', '文档标题', 'text', 'varchar(250)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092381', '1533092381');
INSERT INTO `ey_channelfield` VALUES ('8', 'litpic', '0', '缩略图', 'img', 'varchar(250)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092398', '1533092398');
INSERT INTO `ey_channelfield` VALUES ('9', 'is_head', '0', '是否头条', 'switch', 'tinyint(1)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092420', '1533092420');
INSERT INTO `ey_channelfield` VALUES ('10', 'is_special', '0', '是否特荐', 'switch', 'tinyint(1)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092439', '1533092439');
INSERT INTO `ey_channelfield` VALUES ('11', 'is_top', '0', '是否置顶', 'switch', 'tinyint(1)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092454', '1533092454');
INSERT INTO `ey_channelfield` VALUES ('12', 'is_recom', '0', '是否推荐', 'switch', 'tinyint(1)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092468', '1533092468');
INSERT INTO `ey_channelfield` VALUES ('13', 'is_jump', '0', '是否跳转', 'switch', 'tinyint(1)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092484', '1533092484');
INSERT INTO `ey_channelfield` VALUES ('14', 'author', '0', '编辑者', 'text', 'varchar(250)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092498', '1533092498');
INSERT INTO `ey_channelfield` VALUES ('15', 'click', '0', '浏览量', 'int', 'int(11)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092512', '1533092512');
INSERT INTO `ey_channelfield` VALUES ('16', 'arcrank', '0', '阅读权限', 'select', 'tinyint(1)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092534', '1533092534');
INSERT INTO `ey_channelfield` VALUES ('17', 'jumplinks', '0', '跳转链接', 'text', 'varchar(250)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092553', '1533092553');
INSERT INTO `ey_channelfield` VALUES ('18', 'ismake', '0', '是否静态页面', 'switch', 'tinyint(1)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092698', '1533092698');
INSERT INTO `ey_channelfield` VALUES ('19', 'seo_title', '0', 'SEO标题', 'text', 'varchar(250)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092713', '1533092713');
INSERT INTO `ey_channelfield` VALUES ('20', 'seo_keywords', '0', 'SEO关键词', 'text', 'varchar(250)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092725', '1533092725');
INSERT INTO `ey_channelfield` VALUES ('21', 'seo_description', '0', 'SEO描述', 'text', 'varchar(250)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092739', '1533092739');
INSERT INTO `ey_channelfield` VALUES ('22', 'status', '0', '状态', 'switch', 'tinyint(1)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092753', '1533092753');
INSERT INTO `ey_channelfield` VALUES ('23', 'sort_order', '0', '排序号', 'int', 'int(11)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092766', '1533092766');
INSERT INTO `ey_channelfield` VALUES ('24', 'content', '2', '内容详情', 'htmltext', 'longtext', '250', '', '', '', '0', '1', '1', '0', '1', '0', '0', '100', '1', '1533359739', '1533359739');
INSERT INTO `ey_channelfield` VALUES ('25', 'content', '3', '内容详情', 'htmltext', 'longtext', '250', '', '', '', '0', '1', '1', '0', '1', '0', '0', '100', '1', '1533359588', '1533359588');
INSERT INTO `ey_channelfield` VALUES ('26', 'content', '4', '内容详情', 'htmltext', 'longtext', '250', '', '', '', '0', '1', '1', '0', '1', '0', '0', '100', '1', '1533359752', '1533359752');
INSERT INTO `ey_channelfield` VALUES ('27', 'content', '6', '内容详情', 'htmltext', 'longtext', '250', '', '', '', '0', '1', '1', '0', '1', '0', '0', '100', '1', '1533464715', '1533464715');
INSERT INTO `ey_channelfield` VALUES ('29', 'content', '1', '内容详情', 'htmltext', 'longtext', '250', '', '', '', '0', '1', '1', '0', '1', '0', '0', '100', '1', '1533464713', '1533464713');
INSERT INTO `ey_channelfield` VALUES ('30', 'update_time', '-99', '更新时间', 'datetime', 'int(11)', '11', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('31', 'add_time', '-99', '新增时间', 'datetime', 'int(11)', '11', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('32', 'status', '-99', '启用 (1=正常，0=屏蔽)', 'switch', 'tinyint(1)', '1', '1', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('33', 'is_part', '-99', '栏目属性：0=内容栏目，1=外部链接', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('34', 'is_hidden', '-99', '是否隐藏栏目：0=显示，1=隐藏', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('35', 'sort_order', '-99', '排序号', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('36', 'seo_description', '-99', 'seo描述', 'multitext', 'text', '0', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('37', 'seo_keywords', '-99', 'seo关键字', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('38', 'seo_title', '-99', 'SEO标题', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('39', 'tempview', '-99', '文档模板文件名', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('40', 'templist', '-99', '列表模板文件名', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('41', 'litpic', '-99', '栏目图片', 'img', 'varchar(250)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('42', 'typelink', '-99', '栏目链接', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('43', 'grade', '-99', '栏目等级', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('44', 'englist_name', '-99', '栏目英文名', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('45', 'dirpath', '-99', '目录存放HTML路径', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('46', 'dirname', '-99', '目录英文名', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('47', 'typename', '-99', '栏目名称', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('48', 'parent_id', '-99', '栏目上级ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('49', 'current_channel', '-99', '栏目当前模型ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('50', 'channeltype', '-99', '栏目顶级模型ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('51', 'id', '-99', '栏目ID', 'int', 'int(10)', '10', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('52', 'del_method', '-99', '伪删除状态，1为主动删除，2为跟随上级栏目被动删除', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1547890773', '1547890773');
INSERT INTO `ey_channelfield` VALUES ('53', 'is_del', '0', '是否伪删除', 'switch', 'tinyint(1)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1547890773', '1547890773');
INSERT INTO `ey_channelfield` VALUES ('54', 'del_method', '0', '伪删除状态，1为主动删除，2为跟随上级栏目被动删除', 'switch', 'tinyint(1)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1547890773', '1547890773');
INSERT INTO `ey_channelfield` VALUES ('55', 'prom_type', '0', '产品类型：0普通产品，1虚拟产品', 'switch', 'tinyint(1) unsigned', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('56', 'users_price', '0', '价格', 'decimal', 'decimal(10,2)', '10', '0.00', '', '', '0', '0', '1', '0', '1', '1', '0', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('57', 'prom_type', '2', '产品类型：0普通产品，1虚拟产品', 'switch', 'tinyint(1) unsigned', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('58', 'users_price', '2', '价格', 'decimal', 'decimal(10,2)', '10', '0.00', '', '', '0', '0', '1', '0', '1', '1', '0', '100', '1', '1557042574', '1563498415');
INSERT INTO `ey_channelfield` VALUES ('59', 'update_time', '2', '更新时间', 'datetime', 'int(11)', '11', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('60', 'add_time', '2', '新增时间', 'datetime', 'int(11)', '11', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('61', 'del_method', '2', '伪删除状态，1为主动删除，2为跟随上级栏目被动删除', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('62', 'is_del', '2', '伪删除，1=是，0=否', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('63', 'admin_id', '2', '管理员ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('64', 'lang', '2', '语言标识', 'text', 'varchar(50)', '50', 'cn', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('65', 'sort_order', '2', '排序号', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('66', 'status', '2', '状态(0=屏蔽，1=正常)', 'switch', 'tinyint(1)', '1', '1', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('67', 'tempview', '2', '文档模板文件名', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('68', 'seo_description', '2', 'SEO描述', 'multitext', 'text', '0', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('69', 'seo_keywords', '2', 'SEO关键词', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('70', 'seo_title', '2', 'SEO标题', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('71', 'ismake', '2', '是否静态页面（0=动态，1=静态）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('72', 'jumplinks', '2', '外链跳转', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('73', 'arcrank', '2', '阅读权限：0=开放浏览，-1=待审核稿件', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('74', 'click', '2', '浏览量', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('75', 'author', '2', '作者', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('76', 'is_litpic', '2', '图片（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('77', 'is_jump', '2', '跳转链接（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('78', 'is_recom', '2', '推荐（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('79', 'is_top', '2', '置顶（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('80', 'is_special', '2', '特荐（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('81', 'is_head', '2', '头条（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('82', 'litpic', '2', '缩略图', 'img', 'varchar(250)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('83', 'title', '2', '标题', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('84', 'is_b', '2', '加粗', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('85', 'channel', '2', '模型ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('86', 'typeid', '2', '当前栏目', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('87', 'aid', '2', 'aid', 'int', 'int(10)', '10', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563518642', '1563518642');
INSERT INTO `ey_channelfield` VALUES ('88', 'fxrq', '2', '发行日期', 'radio', 'enum(\'2019年\',\'2018年\',\'2017年\')', '0', '2019年,2018年,2017年', '', '', '1', '0', '1', '0', '0', '0', '0', '100', '1', '1563518738', '1563518738');
INSERT INTO `ey_channelfield` VALUES ('89', 'update_time', '9', '更新时间', 'datetime', 'int(11)', '11', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('90', 'add_time', '9', '新增时间', 'datetime', 'int(11)', '11', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('91', 'del_method', '9', '伪删除状态，1为主动删除，2为跟随上级栏目被动删除', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('92', 'is_del', '9', '伪删除，1=是，0=否', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('93', 'admin_id', '9', '管理员ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('94', 'lang', '9', '语言标识', 'text', 'varchar(50)', '50', 'cn', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('95', 'sort_order', '9', '排序号', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('96', 'status', '9', '状态(0=屏蔽，1=正常)', 'switch', 'tinyint(1)', '1', '1', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('97', 'tempview', '9', '文档模板文件名', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('98', 'prom_type', '9', '产品类型：0普通产品，1虚拟产品', 'switch', 'tinyint(1) unsigned', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('99', 'users_price', '9', '价格', 'decimal', 'decimal(10,2)', '10', '0.00', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('100', 'seo_description', '9', 'SEO描述', 'multitext', 'text', '0', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('101', 'seo_keywords', '9', 'SEO关键词', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('102', 'seo_title', '9', 'SEO标题', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('103', 'ismake', '9', '是否静态页面（0=动态，1=静态）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('104', 'jumplinks', '9', '外链跳转', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('105', 'arcrank', '9', '阅读权限：0=开放浏览，-1=待审核稿件', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('106', 'click', '9', '浏览量', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('107', 'author', '9', '作者', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '0', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('108', 'is_litpic', '9', '图片（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('109', 'is_jump', '9', '跳转链接（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('110', 'is_recom', '9', '推荐（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('111', 'is_top', '9', '置顶（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('112', 'is_special', '9', '特荐（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('113', 'is_head', '9', '头条（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('114', 'litpic', '9', '缩略图', 'img', 'varchar(250)', '250', '', '', '', '0', '0', '0', '0', '1', '1', '0', '100', '1', '1563526560', '1563526567');
INSERT INTO `ey_channelfield` VALUES ('115', 'title', '9', '标题', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('116', 'is_b', '9', '加粗', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('117', 'channel', '9', '模型ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('118', 'typeid', '9', '当前栏目', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('119', 'aid', '9', 'aid', 'int', 'int(10)', '10', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1563526560', '1563526560');
INSERT INTO `ey_channelfield` VALUES ('120', 'gzdd', '9', '工作地点', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '0', '0', '0', '1', '1', '1563526665', '1563528016');
INSERT INTO `ey_channelfield` VALUES ('121', 'xlyq', '9', '学历要求', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '0', '0', '0', '2', '1', '1563526681', '1563528016');
INSERT INTO `ey_channelfield` VALUES ('122', 'xzdy', '9', '薪资待遇', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '0', '0', '0', '3', '1', '1563526694', '1563528018');
INSERT INTO `ey_channelfield` VALUES ('123', 'gzxz', '9', '工作性质', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '0', '0', '0', '4', '1', '1563526705', '1563528018');
INSERT INTO `ey_channelfield` VALUES ('124', 'gznx', '9', '工作年限', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '0', '0', '0', '5', '1', '1563526716', '1563528019');
INSERT INTO `ey_channelfield` VALUES ('125', 'zprs', '9', '招聘人数', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '0', '0', '0', '6', '1', '1563526727', '1563528021');
INSERT INTO `ey_channelfield` VALUES ('126', 'nnxq', '9', '内容详情', 'htmltext', 'longtext', '0', '', '', '', '0', '0', '1', '0', '0', '0', '0', '7', '1', '1563526769', '1563528023');
INSERT INTO `ey_channelfield` VALUES ('127', 'users_id', '0', '会员ID', 'int', 'int(11)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('128', 'arc_level_id', '0', '文档会员权限ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('129', 'arc_level_id', '4', '文档会员权限ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('130', 'arc_level_id', '2', '文档会员权限ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1565662106', '1565662106');
INSERT INTO `ey_channelfield` VALUES ('131', 'users_id', '2', '会员ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1565662106', '1565662106');
INSERT INTO `ey_channelfield` VALUES ('132', 'jiawei', '2', '价位区段', 'radio', 'enum(\'0-1000\',\'1000-1699\',\'1700-2799\',\'2800-3500\',\'3500-10000\')', '0', '0-1000,1000-1699,1700-2799,2800-3500,3500-10000', '', '', '1', '0', '1', '0', '0', '0', '0', '100', '1', '1565662216', '1565663341');
INSERT INTO `ey_channelfield` VALUES ('133', 'yanse', '2', '机身颜色', 'radio', 'enum(\'银色\',\'绿色\',\'黑色\',\'灰色\')', '0', '银色,绿色,黑色,灰色', '', '', '1', '0', '1', '0', '0', '0', '0', '100', '1', '1565662279', '1565662740');
INSERT INTO `ey_channelfield` VALUES ('136', 'weapp_code', '-99', '插件栏目唯一标识', 'text', 'varchar(200)', '200', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('137', 'is_release', '-99', '栏目是否应用于会员投稿发布，1是，0否', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('138', 'old_price', '0', '产品旧价', 'decimal', 'decimal(10,2)', '10', '0.00', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('139', 'stock_count', '0', '商品库存量', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('140', 'stock_show', '0', '商品库存在产品详情页是否显示，1为显示，0为不显示', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('141', 'joinaid', '0', '关联文档ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('142', 'downcount', '0', '下载次数', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('143', 'downcount', '4', '下载次数', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('144', 'update_time', '1', '更新时间', 'datetime', 'int(11)', '11', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('145', 'add_time', '1', '新增时间', 'datetime', 'int(11)', '11', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('146', 'downcount', '1', '下载次数', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('147', 'joinaid', '1', '关联文档ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('148', 'del_method', '1', '伪删除状态，1为主动删除，2为跟随上级栏目被动删除', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('149', 'is_del', '1', '伪删除，1=是，0=否', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('150', 'arc_level_id', '1', '文档会员权限ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('151', 'users_id', '1', '会员ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('152', 'admin_id', '1', '管理员ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('153', 'lang', '1', '语言标识', 'text', 'varchar(50)', '50', 'cn', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('154', 'sort_order', '1', '排序号', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('155', 'status', '1', '状态(0=屏蔽，1=正常)', 'switch', 'tinyint(1)', '1', '1', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('156', 'tempview', '1', '文档模板文件名', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('157', 'prom_type', '1', '产品类型：0普通产品，1虚拟产品', 'switch', 'tinyint(1) unsigned', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('158', 'stock_show', '1', '商品库存在产品详情页是否显示，1为显示，0为不显示', 'switch', 'tinyint(1) unsigned', '1', '1', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('159', 'stock_count', '1', '商品库存量', 'int', 'int(10) unsigned', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('160', 'old_price', '1', '产品旧价', 'decimal', 'decimal(10,2)', '10', '0.00', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('161', 'users_price', '1', '会员价', 'decimal', 'decimal(10,2)', '10', '0.00', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('162', 'seo_description', '1', 'SEO描述', 'multitext', 'text', '0', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('163', 'seo_keywords', '1', 'SEO关键词', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('164', 'seo_title', '1', 'SEO标题', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('165', 'ismake', '1', '是否静态页面（0=动态，1=静态）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('166', 'jumplinks', '1', '外链跳转', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('167', 'arcrank', '1', '阅读权限：0=开放浏览，-1=待审核稿件', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('168', 'click', '1', '浏览量', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('169', 'author', '1', '作者', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('170', 'is_litpic', '1', '图片（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('171', 'is_jump', '1', '跳转链接（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('172', 'is_recom', '1', '推荐（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('173', 'is_top', '1', '置顶（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('174', 'is_special', '1', '特荐（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('175', 'is_head', '1', '头条（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('176', 'litpic', '1', '缩略图', 'img', 'varchar(250)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('177', 'title', '1', '标题', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('178', 'is_b', '1', '加粗', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('179', 'channel', '1', '模型ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('180', 'typeid', '1', '当前栏目', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('181', 'aid', '1', 'aid', 'int', 'int(10)', '10', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233787', '1574233787');
INSERT INTO `ey_channelfield` VALUES ('182', 'downcount', '2', '下载次数', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233793', '1574233793');
INSERT INTO `ey_channelfield` VALUES ('183', 'joinaid', '2', '关联文档ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233793', '1574233793');
INSERT INTO `ey_channelfield` VALUES ('184', 'stock_show', '2', '商品库存在产品详情页是否显示，1为显示，0为不显示', 'switch', 'tinyint(1) unsigned', '1', '1', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233793', '1574233793');
INSERT INTO `ey_channelfield` VALUES ('185', 'stock_count', '2', '商品库存量', 'int', 'int(10) unsigned', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233793', '1574233793');
INSERT INTO `ey_channelfield` VALUES ('186', 'old_price', '2', '产品旧价', 'decimal', 'decimal(10,2)', '10', '0.00', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233793', '1574233793');
INSERT INTO `ey_channelfield` VALUES ('187', 'update_time', '3', '更新时间', 'datetime', 'int(11)', '11', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('188', 'add_time', '3', '新增时间', 'datetime', 'int(11)', '11', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('189', 'downcount', '3', '下载次数', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('190', 'joinaid', '3', '关联文档ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('191', 'del_method', '3', '伪删除状态，1为主动删除，2为跟随上级栏目被动删除', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('192', 'is_del', '3', '伪删除，1=是，0=否', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('193', 'arc_level_id', '3', '文档会员权限ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('194', 'users_id', '3', '会员ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('195', 'admin_id', '3', '管理员ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('196', 'lang', '3', '语言标识', 'text', 'varchar(50)', '50', 'cn', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('197', 'sort_order', '3', '排序号', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('198', 'status', '3', '状态(0=屏蔽，1=正常)', 'switch', 'tinyint(1)', '1', '1', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('199', 'tempview', '3', '文档模板文件名', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('200', 'prom_type', '3', '产品类型：0普通产品，1虚拟产品', 'switch', 'tinyint(1) unsigned', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('201', 'stock_show', '3', '商品库存在产品详情页是否显示，1为显示，0为不显示', 'switch', 'tinyint(1) unsigned', '1', '1', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('202', 'stock_count', '3', '商品库存量', 'int', 'int(10) unsigned', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('203', 'old_price', '3', '产品旧价', 'decimal', 'decimal(10,2)', '10', '0.00', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('204', 'users_price', '3', '会员价', 'decimal', 'decimal(10,2)', '10', '0.00', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('205', 'seo_description', '3', 'SEO描述', 'multitext', 'text', '0', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('206', 'seo_keywords', '3', 'SEO关键词', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('207', 'seo_title', '3', 'SEO标题', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('208', 'ismake', '3', '是否静态页面（0=动态，1=静态）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('209', 'jumplinks', '3', '外链跳转', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('210', 'arcrank', '3', '阅读权限：0=开放浏览，-1=待审核稿件', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('211', 'click', '3', '浏览量', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('212', 'author', '3', '作者', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('213', 'is_litpic', '3', '图片（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('214', 'is_jump', '3', '跳转链接（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('215', 'is_recom', '3', '推荐（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('216', 'is_top', '3', '置顶（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('217', 'is_special', '3', '特荐（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('218', 'is_head', '3', '头条（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('219', 'litpic', '3', '缩略图', 'img', 'varchar(250)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('220', 'title', '3', '标题', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('221', 'is_b', '3', '加粗', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('222', 'channel', '3', '模型ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('223', 'typeid', '3', '当前栏目', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('224', 'aid', '3', 'aid', 'int', 'int(10)', '10', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233796', '1574233796');
INSERT INTO `ey_channelfield` VALUES ('225', 'update_time', '4', '更新时间', 'datetime', 'int(11)', '11', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('226', 'add_time', '4', '新增时间', 'datetime', 'int(11)', '11', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('227', 'joinaid', '4', '关联文档ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('228', 'del_method', '4', '伪删除状态，1为主动删除，2为跟随上级栏目被动删除', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('229', 'is_del', '4', '伪删除，1=是，0=否', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('230', 'users_id', '4', '会员ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('231', 'admin_id', '4', '管理员ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('232', 'lang', '4', '语言标识', 'text', 'varchar(50)', '50', 'cn', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('233', 'sort_order', '4', '排序号', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('234', 'status', '4', '状态(0=屏蔽，1=正常)', 'switch', 'tinyint(1)', '1', '1', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('235', 'tempview', '4', '文档模板文件名', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('236', 'prom_type', '4', '产品类型：0普通产品，1虚拟产品', 'switch', 'tinyint(1) unsigned', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('237', 'stock_show', '4', '商品库存在产品详情页是否显示，1为显示，0为不显示', 'switch', 'tinyint(1) unsigned', '1', '1', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('238', 'stock_count', '4', '商品库存量', 'int', 'int(10) unsigned', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('239', 'old_price', '4', '产品旧价', 'decimal', 'decimal(10,2)', '10', '0.00', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('240', 'users_price', '4', '会员价', 'decimal', 'decimal(10,2)', '10', '0.00', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('241', 'seo_description', '4', 'SEO描述', 'multitext', 'text', '0', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('242', 'seo_keywords', '4', 'SEO关键词', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('243', 'seo_title', '4', 'SEO标题', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('244', 'ismake', '4', '是否静态页面（0=动态，1=静态）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('245', 'jumplinks', '4', '外链跳转', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('246', 'arcrank', '4', '阅读权限：0=开放浏览，-1=待审核稿件', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('247', 'click', '4', '浏览量', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('248', 'author', '4', '作者', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('249', 'is_litpic', '4', '图片（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('250', 'is_jump', '4', '跳转链接（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('251', 'is_recom', '4', '推荐（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('252', 'is_top', '4', '置顶（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('253', 'is_special', '4', '特荐（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('254', 'is_head', '4', '头条（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('255', 'litpic', '4', '缩略图', 'img', 'varchar(250)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('256', 'title', '4', '标题', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('257', 'is_b', '4', '加粗', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('258', 'channel', '4', '模型ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('259', 'typeid', '4', '当前栏目', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('260', 'aid', '4', 'aid', 'int', 'int(10)', '10', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233799', '1574233799');
INSERT INTO `ey_channelfield` VALUES ('261', 'update_time', '6', '更新时间', 'datetime', 'int(11)', '11', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('262', 'add_time', '6', '新增时间', 'datetime', 'int(11)', '11', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('263', 'downcount', '6', '下载次数', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('264', 'joinaid', '6', '关联文档ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('265', 'del_method', '6', '伪删除状态，1为主动删除，2为跟随上级栏目被动删除', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('266', 'is_del', '6', '伪删除，1=是，0=否', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('267', 'arc_level_id', '6', '文档会员权限ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('268', 'users_id', '6', '会员ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('269', 'admin_id', '6', '管理员ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('270', 'lang', '6', '语言标识', 'text', 'varchar(50)', '50', 'cn', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('271', 'sort_order', '6', '排序号', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('272', 'status', '6', '状态(0=屏蔽，1=正常)', 'switch', 'tinyint(1)', '1', '1', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('273', 'tempview', '6', '文档模板文件名', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('274', 'prom_type', '6', '产品类型：0普通产品，1虚拟产品', 'switch', 'tinyint(1) unsigned', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('275', 'stock_show', '6', '商品库存在产品详情页是否显示，1为显示，0为不显示', 'switch', 'tinyint(1) unsigned', '1', '1', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('276', 'stock_count', '6', '商品库存量', 'int', 'int(10) unsigned', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('277', 'old_price', '6', '产品旧价', 'decimal', 'decimal(10,2)', '10', '0.00', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('278', 'users_price', '6', '会员价', 'decimal', 'decimal(10,2)', '10', '0.00', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('279', 'seo_description', '6', 'SEO描述', 'multitext', 'text', '0', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('280', 'seo_keywords', '6', 'SEO关键词', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('281', 'seo_title', '6', 'SEO标题', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('282', 'ismake', '6', '是否静态页面（0=动态，1=静态）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('283', 'jumplinks', '6', '外链跳转', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('284', 'arcrank', '6', '阅读权限：0=开放浏览，-1=待审核稿件', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('285', 'click', '6', '浏览量', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('286', 'author', '6', '作者', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('287', 'is_litpic', '6', '图片（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('288', 'is_jump', '6', '跳转链接（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('289', 'is_recom', '6', '推荐（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('290', 'is_top', '6', '置顶（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('291', 'is_special', '6', '特荐（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('292', 'is_head', '6', '头条（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('293', 'litpic', '6', '缩略图', 'img', 'varchar(250)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('294', 'title', '6', '标题', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('295', 'is_b', '6', '加粗', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('296', 'channel', '6', '模型ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('297', 'typeid', '6', '当前栏目', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('298', 'aid', '6', 'aid', 'int', 'int(10)', '10', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233802', '1574233802');
INSERT INTO `ey_channelfield` VALUES ('299', 'downcount', '9', '下载次数', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233808', '1574233808');
INSERT INTO `ey_channelfield` VALUES ('300', 'joinaid', '9', '关联文档ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233808', '1574233808');
INSERT INTO `ey_channelfield` VALUES ('301', 'arc_level_id', '9', '文档会员权限ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233808', '1574233808');
INSERT INTO `ey_channelfield` VALUES ('302', 'users_id', '9', '会员ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233808', '1574233808');
INSERT INTO `ey_channelfield` VALUES ('303', 'stock_show', '9', '商品库存在产品详情页是否显示，1为显示，0为不显示', 'switch', 'tinyint(1) unsigned', '1', '1', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233808', '1574233808');
INSERT INTO `ey_channelfield` VALUES ('304', 'stock_count', '9', '商品库存量', 'int', 'int(10) unsigned', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233808', '1574233808');
INSERT INTO `ey_channelfield` VALUES ('305', 'old_price', '9', '产品旧价', 'decimal', 'decimal(10,2)', '10', '0.00', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1574233808', '1574233808');
INSERT INTO `ey_channelfield` VALUES ('306', 'htmlfilename', '0', '自定义文件名', 'text', 'varchar(50)', '50', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('307', 'htmlfilename', '1', '自定义文件名', 'text', 'varchar(50)', '50', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('308', 'htmlfilename', '2', '自定义文件名', 'text', 'varchar(50)', '50', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('309', 'htmlfilename', '3', '自定义文件名', 'text', 'varchar(50)', '50', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('310', 'htmlfilename', '4', '自定义文件名', 'text', 'varchar(50)', '50', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('311', 'htmlfilename', '6', '自定义文件名', 'text', 'varchar(50)', '50', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('312', 'attrlist_id', '0', '参数列表ID', 'int', 'int(10) unsigned', '10', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533091930', '1533091930');
INSERT INTO `ey_channelfield` VALUES ('313', 'sales_num', '0', '销售量', 'int', 'int(10) unsigned', '10', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533091930', '1533091930');
INSERT INTO `ey_channelfield` VALUES ('314', 'update_time', '5', '更新时间', 'datetime', 'int(11)', '11', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('315', 'add_time', '5', '新增时间', 'datetime', 'int(11)', '11', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('316', 'htmlfilename', '5', '自定义文件名', 'text', 'varchar(50)', '50', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('317', 'downcount', '5', '下载次数', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('318', 'joinaid', '5', '关联文档ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('319', 'del_method', '5', '伪删除状态，1为主动删除，2为跟随上级栏目被动删除', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('320', 'is_del', '5', '伪删除，1=是，0=否', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('321', 'arc_level_id', '5', '文档会员权限ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('322', 'users_id', '5', '会员ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('323', 'admin_id', '5', '管理员ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('324', 'lang', '5', '语言标识', 'text', 'varchar(50)', '50', 'cn', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('325', 'sort_order', '5', '排序号', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('326', 'status', '5', '状态(0=屏蔽，1=正常)', 'switch', 'tinyint(1)', '1', '1', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('327', 'tempview', '5', '文档模板文件名', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('328', 'prom_type', '5', '产品类型：0=普通产品，1=虚拟(默认手动发货)，2=虚拟(网盘', 'switch', 'tinyint(1) unsigned', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('329', 'stock_show', '5', '商品库存在产品详情页是否显示，1为显示，0为不显示', 'switch', 'tinyint(1) unsigned', '1', '1', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('330', 'stock_count', '5', '商品库存量', 'int', 'int(10) unsigned', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('331', 'sales_num', '5', '销售量', 'int', 'int(10) unsigned', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('332', 'old_price', '5', '产品旧价', 'decimal', 'decimal(10,2)', '10', '0.00', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('333', 'users_price', '5', '会员价', 'decimal', 'decimal(10,2)', '10', '0.00', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('334', 'attrlist_id', '5', '参数列表ID', 'int', 'int(10) unsigned', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('335', 'seo_description', '5', 'SEO描述', 'multitext', 'text', '0', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('336', 'seo_keywords', '5', 'SEO关键词', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('337', 'seo_title', '5', 'SEO标题', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('338', 'ismake', '5', '是否静态页面（0=动态，1=静态）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('339', 'jumplinks', '5', '外链跳转', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('340', 'arcrank', '5', '阅读权限：0=开放浏览，-1=待审核稿件', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('341', 'click', '5', '浏览量', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('342', 'author', '5', '作者', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('343', 'is_litpic', '5', '图片（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('344', 'is_jump', '5', '跳转链接（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('345', 'is_recom', '5', '推荐（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('346', 'is_top', '5', '置顶（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('347', 'is_special', '5', '特荐（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('348', 'is_head', '5', '头条（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('349', 'litpic', '5', '缩略图', 'img', 'varchar(250)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('350', 'title', '5', '标题', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('351', 'is_b', '5', '加粗', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('352', 'channel', '5', '模型ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('353', 'typeid', '5', '当前栏目', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('354', 'aid', '5', 'aid', 'int', 'int(10)', '10', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('355', 'content', '5', '内容详情', 'htmltext', 'longtext', '0', '', '', '', '0', '0', '1', '0', '1', '0', '0', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('356', 'courseware', '5', '课件地址', 'text', 'varchar(200)', '200', '', '', '', '0', '1', '0', '0', '1', '0', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('357', 'courseware_free', '5', '课件收费', 'select', 'enum(\'免费\',\'收费\')', '0', '免费,收费', '', '', '0', '1', '0', '0', '1', '0', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('358', 'total_duration', '5', '视频总时长', 'int', 'int(10)', '10', '0', '', '', '0', '1', '0', '0', '1', '0', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('359', 'total_video', '5', '视频数', 'int', 'int(10)', '10', '0', '', '', '0', '1', '0', '0', '1', '0', '1', '100', '1', '1591957363', '1591957363');
INSERT INTO `ey_channelfield` VALUES ('404', 'topid', '-99', '顶级栏目ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('361', 'update_time', '7', '更新时间', 'datetime', 'int(11)', '11', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('362', 'add_time', '7', '新增时间', 'datetime', 'int(11)', '11', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('363', 'htmlfilename', '7', '自定义文件名', 'text', 'varchar(50)', '50', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('364', 'downcount', '7', '下载次数', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('365', 'joinaid', '7', '关联文档ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('366', 'del_method', '7', '伪删除状态，1为主动删除，2为跟随上级栏目被动删除', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('367', 'is_del', '7', '伪删除，1=是，0=否', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('368', 'arc_level_id', '7', '文档会员权限ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('369', 'users_id', '7', '会员ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('370', 'admin_id', '7', '管理员ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('371', 'lang', '7', '语言标识', 'text', 'varchar(50)', '50', 'cn', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('372', 'sort_order', '7', '排序号', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('373', 'status', '7', '状态(0=屏蔽，1=正常)', 'switch', 'tinyint(1)', '1', '1', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('374', 'tempview', '7', '文档模板文件名', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('375', 'prom_type', '7', '产品类型：0=普通产品，1=虚拟(默认手动发货)，2=虚拟(网盘', 'switch', 'tinyint(1) unsigned', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('376', 'stock_show', '7', '商品库存在产品详情页是否显示，1为显示，0为不显示', 'switch', 'tinyint(1) unsigned', '1', '1', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('377', 'stock_count', '7', '商品库存量', 'int', 'int(10) unsigned', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('378', 'sales_num', '7', '销售量', 'int', 'int(10) unsigned', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('379', 'old_price', '7', '产品旧价', 'decimal', 'decimal(10,2)', '10', '0.00', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('380', 'users_free', '7', '是否会员免费，默认0不免费，1为免费', 'switch', 'tinyint(1) unsigned', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('381', 'users_price', '7', '会员价', 'decimal', 'decimal(10,2)', '10', '0.00', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('382', 'attrlist_id', '7', '参数列表ID', 'int', 'int(10) unsigned', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('383', 'seo_description', '7', 'SEO描述', 'multitext', 'text', '0', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('384', 'seo_keywords', '7', 'SEO关键词', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('385', 'seo_title', '7', 'SEO标题', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('386', 'ismake', '7', '是否静态页面（0=动态，1=静态）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('387', 'jumplinks', '7', '外链跳转', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('388', 'arcrank', '7', '阅读权限：0=开放浏览，-1=待审核稿件', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('389', 'click', '7', '浏览量', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('390', 'author', '7', '作者', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('391', 'is_litpic', '7', '图片（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('392', 'is_jump', '7', '跳转链接（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('393', 'is_recom', '7', '推荐（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('394', 'is_top', '7', '置顶（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('395', 'is_special', '7', '特荐（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('396', 'is_head', '7', '头条（0=否，1=是）', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('397', 'litpic', '7', '缩略图', 'img', 'varchar(250)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('398', 'title', '7', '标题', 'text', 'varchar(200)', '200', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('399', 'is_b', '7', '加粗', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('400', 'channel', '7', '模型ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('401', 'typeid', '7', '当前栏目', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('402', 'aid', '7', 'aid', 'int', 'int(10)', '10', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('403', 'content', '7', '内容详情', 'htmltext', 'longtext', '0', '', '', '', '0', '1', '1', '0', '1', '0', '0', '100', '1', '1602320145', '1602320145');
INSERT INTO `ey_channelfield` VALUES ('405', 'is_slide', '0', '是否幻灯', 'switch', 'tinyint(1)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092420', '1533092420');
INSERT INTO `ey_channelfield` VALUES ('406', 'is_roll', '0', '是否幻灯', 'switch', 'tinyint(1)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092420', '1533092420');
INSERT INTO `ey_channelfield` VALUES ('407', 'is_diyattr', '0', '是否自定义', 'switch', 'tinyint(1)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533092420', '1533092420');

-- -----------------------------
-- Table structure for `ey_channelfield_bind`
-- -----------------------------
DROP TABLE IF EXISTS `ey_channelfield_bind`;
CREATE TABLE `ey_channelfield_bind` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `typeid` int(10) DEFAULT '0' COMMENT '栏目ID',
  `field_id` int(10) DEFAULT '0' COMMENT '自定义字段ID',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=280 DEFAULT CHARSET=utf8 COMMENT='栏目与自定义字段绑定表';

-- -----------------------------
-- Records of `ey_channelfield_bind`
-- -----------------------------
INSERT INTO `ey_channelfield_bind` VALUES ('1', '0', '24', '1563518642', '1563518642');
INSERT INTO `ey_channelfield_bind` VALUES ('2', '0', '25', '1563518642', '1563518642');
INSERT INTO `ey_channelfield_bind` VALUES ('3', '0', '26', '1563518642', '1563518642');
INSERT INTO `ey_channelfield_bind` VALUES ('4', '0', '27', '1563518642', '1563518642');
INSERT INTO `ey_channelfield_bind` VALUES ('5', '0', '29', '1563518642', '1563518642');
INSERT INTO `ey_channelfield_bind` VALUES ('6', '3', '88', '1563518738', '1563518738');
INSERT INTO `ey_channelfield_bind` VALUES ('7', '20', '88', '1563518738', '1563518738');
INSERT INTO `ey_channelfield_bind` VALUES ('8', '21', '88', '1563518738', '1563518738');
INSERT INTO `ey_channelfield_bind` VALUES ('9', '22', '88', '1563518738', '1563518738');
INSERT INTO `ey_channelfield_bind` VALUES ('10', '24', '88', '1563518738', '1563518738');
INSERT INTO `ey_channelfield_bind` VALUES ('11', '25', '88', '1563518738', '1563518738');
INSERT INTO `ey_channelfield_bind` VALUES ('12', '26', '88', '1563518738', '1563518738');
INSERT INTO `ey_channelfield_bind` VALUES ('13', '27', '88', '1563518738', '1563518738');
INSERT INTO `ey_channelfield_bind` VALUES ('14', '28', '88', '1563518738', '1563518738');
INSERT INTO `ey_channelfield_bind` VALUES ('15', '29', '88', '1563518738', '1563518738');
INSERT INTO `ey_channelfield_bind` VALUES ('16', '41', '88', '1563518738', '1563518738');
INSERT INTO `ey_channelfield_bind` VALUES ('17', '42', '88', '1563518738', '1563518738');
INSERT INTO `ey_channelfield_bind` VALUES ('18', '43', '88', '1563518738', '1563518738');
INSERT INTO `ey_channelfield_bind` VALUES ('19', '44', '88', '1563518738', '1563518738');
INSERT INTO `ey_channelfield_bind` VALUES ('20', '45', '88', '1563518738', '1563518738');
INSERT INTO `ey_channelfield_bind` VALUES ('21', '46', '88', '1563518738', '1563518738');
INSERT INTO `ey_channelfield_bind` VALUES ('22', '47', '88', '1563518738', '1563518738');
INSERT INTO `ey_channelfield_bind` VALUES ('23', '48', '88', '1563518738', '1563518738');
INSERT INTO `ey_channelfield_bind` VALUES ('24', '49', '88', '1563518738', '1563518738');
INSERT INTO `ey_channelfield_bind` VALUES ('25', '50', '88', '1563518738', '1563518738');
INSERT INTO `ey_channelfield_bind` VALUES ('26', '0', '120', '1563526665', '1563526665');
INSERT INTO `ey_channelfield_bind` VALUES ('27', '0', '121', '1563526681', '1563526681');
INSERT INTO `ey_channelfield_bind` VALUES ('28', '0', '122', '1563526694', '1563526694');
INSERT INTO `ey_channelfield_bind` VALUES ('29', '0', '123', '1563526705', '1563526705');
INSERT INTO `ey_channelfield_bind` VALUES ('30', '0', '124', '1563526716', '1563526716');
INSERT INTO `ey_channelfield_bind` VALUES ('31', '0', '125', '1563526727', '1563526727');
INSERT INTO `ey_channelfield_bind` VALUES ('32', '0', '126', '1563526769', '1563526769');
INSERT INTO `ey_channelfield_bind` VALUES ('232', '50', '132', '1565663341', '1565663341');
INSERT INTO `ey_channelfield_bind` VALUES ('231', '49', '132', '1565663341', '1565663341');
INSERT INTO `ey_channelfield_bind` VALUES ('230', '48', '132', '1565663341', '1565663341');
INSERT INTO `ey_channelfield_bind` VALUES ('229', '47', '132', '1565663341', '1565663341');
INSERT INTO `ey_channelfield_bind` VALUES ('228', '46', '132', '1565663341', '1565663341');
INSERT INTO `ey_channelfield_bind` VALUES ('227', '45', '132', '1565663341', '1565663341');
INSERT INTO `ey_channelfield_bind` VALUES ('226', '44', '132', '1565663341', '1565663341');
INSERT INTO `ey_channelfield_bind` VALUES ('225', '43', '132', '1565663341', '1565663341');
INSERT INTO `ey_channelfield_bind` VALUES ('224', '42', '132', '1565663341', '1565663341');
INSERT INTO `ey_channelfield_bind` VALUES ('223', '41', '132', '1565663341', '1565663341');
INSERT INTO `ey_channelfield_bind` VALUES ('222', '29', '132', '1565663341', '1565663341');
INSERT INTO `ey_channelfield_bind` VALUES ('221', '28', '132', '1565663341', '1565663341');
INSERT INTO `ey_channelfield_bind` VALUES ('220', '27', '132', '1565663341', '1565663341');
INSERT INTO `ey_channelfield_bind` VALUES ('219', '26', '132', '1565663341', '1565663341');
INSERT INTO `ey_channelfield_bind` VALUES ('218', '25', '132', '1565663341', '1565663341');
INSERT INTO `ey_channelfield_bind` VALUES ('217', '24', '132', '1565663341', '1565663341');
INSERT INTO `ey_channelfield_bind` VALUES ('216', '22', '132', '1565663341', '1565663341');
INSERT INTO `ey_channelfield_bind` VALUES ('215', '21', '132', '1565663341', '1565663341');
INSERT INTO `ey_channelfield_bind` VALUES ('214', '20', '132', '1565663341', '1565663341');
INSERT INTO `ey_channelfield_bind` VALUES ('213', '3', '132', '1565663341', '1565663341');
INSERT INTO `ey_channelfield_bind` VALUES ('212', '50', '133', '1565662740', '1565662740');
INSERT INTO `ey_channelfield_bind` VALUES ('211', '49', '133', '1565662740', '1565662740');
INSERT INTO `ey_channelfield_bind` VALUES ('210', '48', '133', '1565662740', '1565662740');
INSERT INTO `ey_channelfield_bind` VALUES ('209', '47', '133', '1565662740', '1565662740');
INSERT INTO `ey_channelfield_bind` VALUES ('208', '46', '133', '1565662740', '1565662740');
INSERT INTO `ey_channelfield_bind` VALUES ('207', '45', '133', '1565662740', '1565662740');
INSERT INTO `ey_channelfield_bind` VALUES ('206', '44', '133', '1565662740', '1565662740');
INSERT INTO `ey_channelfield_bind` VALUES ('205', '43', '133', '1565662740', '1565662740');
INSERT INTO `ey_channelfield_bind` VALUES ('204', '42', '133', '1565662740', '1565662740');
INSERT INTO `ey_channelfield_bind` VALUES ('203', '41', '133', '1565662740', '1565662740');
INSERT INTO `ey_channelfield_bind` VALUES ('202', '29', '133', '1565662740', '1565662740');
INSERT INTO `ey_channelfield_bind` VALUES ('201', '28', '133', '1565662740', '1565662740');
INSERT INTO `ey_channelfield_bind` VALUES ('200', '27', '133', '1565662740', '1565662740');
INSERT INTO `ey_channelfield_bind` VALUES ('199', '26', '133', '1565662740', '1565662740');
INSERT INTO `ey_channelfield_bind` VALUES ('198', '25', '133', '1565662740', '1565662740');
INSERT INTO `ey_channelfield_bind` VALUES ('197', '24', '133', '1565662740', '1565662740');
INSERT INTO `ey_channelfield_bind` VALUES ('196', '22', '133', '1565662740', '1565662740');
INSERT INTO `ey_channelfield_bind` VALUES ('195', '21', '133', '1565662740', '1565662740');
INSERT INTO `ey_channelfield_bind` VALUES ('194', '20', '133', '1565662740', '1565662740');
INSERT INTO `ey_channelfield_bind` VALUES ('193', '3', '133', '1565662740', '1565662740');
INSERT INTO `ey_channelfield_bind` VALUES ('274', '0', '355', '1591957363', '1591957363');
INSERT INTO `ey_channelfield_bind` VALUES ('275', '0', '356', '1591957363', '1591957363');
INSERT INTO `ey_channelfield_bind` VALUES ('276', '0', '357', '1591957363', '1591957363');
INSERT INTO `ey_channelfield_bind` VALUES ('277', '0', '358', '1591957363', '1591957363');
INSERT INTO `ey_channelfield_bind` VALUES ('278', '0', '359', '1591957363', '1591957363');
INSERT INTO `ey_channelfield_bind` VALUES ('279', '0', '403', '1602320145', '1602320145');

-- -----------------------------
-- Table structure for `ey_channelfield_log`
-- -----------------------------
DROP TABLE IF EXISTS `ey_channelfield_log`;
CREATE TABLE `ey_channelfield_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '' COMMENT '字段名称',
  `channel_id` int(10) DEFAULT '0' COMMENT '模型ID',
  `dtype` varchar(32) DEFAULT '' COMMENT '字段类型',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='自定义字段日志表';


-- -----------------------------
-- Table structure for `ey_channeltype`
-- -----------------------------
DROP TABLE IF EXISTS `ey_channeltype`;
CREATE TABLE `ey_channeltype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nid` varchar(50) NOT NULL DEFAULT '' COMMENT '识别id',
  `title` varchar(30) DEFAULT '' COMMENT '名称',
  `ntitle` varchar(30) DEFAULT '' COMMENT '左侧菜单名称',
  `table` varchar(50) DEFAULT '' COMMENT '表名',
  `ctl_name` varchar(50) DEFAULT '' COMMENT '控制器名称（区分大小写）',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态(1=启用，0=屏蔽)',
  `ifsystem` tinyint(1) DEFAULT '0' COMMENT '字段分类，1=系统(不可修改)，0=自定义',
  `is_repeat_title` tinyint(1) DEFAULT '1' COMMENT '文档标题重复，1=允许，0=不允许',
  `is_release` tinyint(1) DEFAULT '0' COMMENT '模型是否允许应用于会员投稿发布，1是，0否',
  `is_litpic_users_release` tinyint(1) DEFAULT '1' COMMENT '缩略图是否应用于会员投稿，1=允许，0=不允许',
  `data` text COMMENT '额外序列化存储数据',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '伪删除，1=是，0=否',
  `sort_order` smallint(6) DEFAULT '50' COMMENT '排序',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idention` (`nid`) USING BTREE,
  UNIQUE KEY `ctl_name` (`ctl_name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=101 DEFAULT CHARSET=utf8 COMMENT='系统模型表';

-- -----------------------------
-- Records of `ey_channeltype`
-- -----------------------------
INSERT INTO `ey_channeltype` VALUES ('1', 'article', '文章模型', '文章', 'article', 'Article', '1', '1', '1', '1', '1', '', '0', '1', '0', '1564532747');
INSERT INTO `ey_channeltype` VALUES ('4', 'download', '下载模型', '下载', 'download', 'Download', '1', '1', '1', '1', '1', '', '0', '4', '0', '1564532747');
INSERT INTO `ey_channeltype` VALUES ('2', 'product', '产品模型', '产品', 'product', 'Product', '1', '1', '1', '1', '1', '', '0', '2', '0', '1564532747');
INSERT INTO `ey_channeltype` VALUES ('8', 'guestbook', '留言模型', '留言', 'guestbook', 'Guestbook', '1', '1', '1', '1', '1', '', '0', '8', '1509197711', '1564532747');
INSERT INTO `ey_channeltype` VALUES ('6', 'single', '单页模型', '单页', 'single', 'Single', '1', '1', '1', '1', '1', '', '0', '6', '1523091961', '1564532747');
INSERT INTO `ey_channeltype` VALUES ('3', 'images', '图集模型', '图集', 'images', 'Images', '1', '1', '1', '1', '1', '', '0', '3', '1523929121', '1564532747');
INSERT INTO `ey_channeltype` VALUES ('9', 'recruit', '招聘模型', '人才招聘', 'recruit', 'Recruit', '1', '0', '1', '1', '1', '', '0', '50', '1563526560', '1564532747');
INSERT INTO `ey_channeltype` VALUES ('5', 'media', '视频模型', '视频', 'media', 'Media', '0', '1', '1', '1', '1', '', '0', '5', '1509197711', '1564532747');
INSERT INTO `ey_channeltype` VALUES ('7', 'special', '专题模型', '专题', 'special', 'Special', '0', '1', '1', '1', '1', '', '0', '7', '1509197711', '1564532747');
INSERT INTO `ey_channeltype` VALUES ('51', 'ask', '问答模型', '问答', 'ask', 'Ask', '0', '1', '1', '1', '1', '', '0', '9', '1509197711', '1564532747');

-- -----------------------------
-- Table structure for `ey_common_pic`
-- -----------------------------
DROP TABLE IF EXISTS `ey_common_pic`;
CREATE TABLE `ey_common_pic` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '常用图片ID',
  `pic_path` varchar(255) NOT NULL DEFAULT '' COMMENT '图片地址',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '多语言',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='常用图片';

-- -----------------------------
-- Records of `ey_common_pic`
-- -----------------------------
INSERT INTO `ey_common_pic` VALUES ('1', '/uploads/allimg/20190724/d6a98b35168da05665fd3eb635e7f2fe.gif', 'cn', '1564475514', '1564475514');
INSERT INTO `ey_common_pic` VALUES ('2', '/uploads/ueditor/20181220/5c1af35580c9a.png', 'cn', '1564483787', '1564483787');
INSERT INTO `ey_common_pic` VALUES ('3', '/uploads/allimg/20190731/9d5ca6bb09d1d728f18ccbaa5cfce8a3.jpg', 'cn', '1564539808', '1564539808');
INSERT INTO `ey_common_pic` VALUES ('4', '/uploads/allimg/20190719/822c2b26ca76dc393b36ae4f8addc108.jpg', 'cn', '1565225121', '1565225121');
INSERT INTO `ey_common_pic` VALUES ('5', '/uploads/allimg/20190808/7dd05a89099c482a51be7faf1bb38ad4.jpg', 'cn', '1565228612', '1565228612');
INSERT INTO `ey_common_pic` VALUES ('6', '/uploads/allimg/20190808/821fcaa266d291b4f504fb9a1d412c1c.jpg', 'cn', '1565229013', '1565229013');
INSERT INTO `ey_common_pic` VALUES ('7', '/uploads/allimg/20190808/a4b1ab346ae389e638f4a424b7396ee2.jpg', 'cn', '1565229242', '1565229242');
INSERT INTO `ey_common_pic` VALUES ('8', '/uploads/allimg/20190808/3ade68e134d3f8fbbd3401c545541106.jpg', 'cn', '1565229431', '1565229431');
INSERT INTO `ey_common_pic` VALUES ('9', '/uploads/allimg/20190808/989d19deb2377e199ec63d5ef9244be8.jpg', 'cn', '1565229559', '1565229559');
INSERT INTO `ey_common_pic` VALUES ('10', '/uploads/allimg/20190808/13fba5d0f2454c4b8fee4ada1d3fb39b.jpg', 'cn', '1565229807', '1565229807');
INSERT INTO `ey_common_pic` VALUES ('11', '/uploads/allimg/20190808/45b6f3f95d30a97cfa4a83d315b5c4f1.jpg', 'cn', '1565233144', '1565233144');
INSERT INTO `ey_common_pic` VALUES ('12', '/uploads/allimg/20190808/1c3dabff0cbf24fb6667899396a866aa.jpg', 'cn', '1565233662', '1565233662');
INSERT INTO `ey_common_pic` VALUES ('13', '/uploads/allimg/20190808/17268e40477444ecbf11bcb643f321c2.jpg', 'cn', '1565234123', '1565234123');

-- -----------------------------
-- Table structure for `ey_config`
-- -----------------------------
DROP TABLE IF EXISTS `ey_config`;
CREATE TABLE `ey_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '' COMMENT '配置的key键名',
  `value` text,
  `inc_type` varchar(64) DEFAULT '' COMMENT '配置分组',
  `desc` varchar(50) DEFAULT '' COMMENT '描述',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否已删除，0=否，1=是',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=331 DEFAULT CHARSET=utf8 COMMENT='系统配置表';

-- -----------------------------
-- Records of `ey_config`
-- -----------------------------
INSERT INTO `ey_config` VALUES ('1', 'is_mark', '0', 'water', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('2', 'mark_txt', '易优Cms', 'water', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('3', 'mark_img', '/uploads/allimg/20190114/f0d5e5830502125f5077212a90ef3a33.png', 'water', '', 'cn', '0', '1547463466');
INSERT INTO `ey_config` VALUES ('4', 'mark_width', '200', 'water', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('5', 'mark_height', '50', 'water', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('6', 'mark_degree', '54', 'water', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('7', 'mark_quality', '56', 'water', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('8', 'mark_sel', '9', 'water', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('9', 'sms_time_out', '120', 'sms', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('10', 'theme_style', '1', 'basic', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('11', 'file_size', '2', 'basic', '', 'cn', '0', '1580983826');
INSERT INTO `ey_config` VALUES ('12', 'image_type', 'jpg|gif|png|bmp|jpeg|ico', 'basic', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('13', 'file_type', 'zip|gz|rar|iso|doc|xls|ppt|wps', 'basic', '', 'cn', '0', '1591262356');
INSERT INTO `ey_config` VALUES ('14', 'media_type', 'swf|mpg|mp3|rm|rmvb|wmv|wma|wav|mid|mov|mp4', 'basic', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('15', 'web_keywords', '', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('17', 'sms_platform', '1', 'sms', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('18', 'seo_viewtitle_format', '2', 'seo', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('24', 'mark_type', 'img', 'water', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('25', 'mark_txt_size', '30', 'water', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('26', 'mark_txt_color', '#000000', 'water', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('27', 'oss_switch', '0', 'oss', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('28', 'web_name', '某某网络科技有限公司', 'web', '', 'cn', '0', '1565096132');
INSERT INTO `ey_config` VALUES ('29', 'web_logo', '/abf/uploads/allimg/20210114/1-2101140933194M.png', 'web', '', 'cn', '0', '1610696352');
INSERT INTO `ey_config` VALUES ('30', 'web_ico', '/favicon.ico', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('31', 'web_basehost', 'http://127.0.0.5', 'web', '', 'cn', '0', '1591263789');
INSERT INTO `ey_config` VALUES ('32', 'web_description', '', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('79', 'web_recordnum', '琼ICP备xxxxxxxx号', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('33', 'web_copyright', 'Copyright © 2012-2021 EYOUCMS. 易优CMS 版权所有', 'web', '', 'cn', '0', '1610589933');
INSERT INTO `ey_config` VALUES ('34', 'web_thirdcode_pc', '', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('35', 'web_thirdcode_wap', '', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('39', 'seo_arcdir', '/html', 'seo', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('40', 'seo_pseudo', '1', 'seo', '', 'cn', '0', '1623813164');
INSERT INTO `ey_config` VALUES ('41', 'list_symbol', '&gt;', 'basic', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('42', 'sitemap_auto', '1', 'sitemap', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('43', 'sitemap_not1', '0', 'sitemap', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('44', 'sitemap_not2', '1', 'sitemap', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('45', 'sitemap_xml', '1', 'sitemap', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('46', 'sitemap_txt', '0', 'sitemap', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('47', 'sitemap_zzbaidutoken', '', 'sitemap', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('48', 'seo_expires_in', '7200', 'seo', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('55', 'web_title', '易优Demo站点 -  Powered by Eyoucms.com', 'web', '', 'cn', '0', '1565096132');
INSERT INTO `ey_config` VALUES ('57', 'web_authortoken', '', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('60', 'web_attr_1', '400-123-4567', 'web', '', 'cn', '0', '1563503492');
INSERT INTO `ey_config` VALUES ('62', 'seo_inlet', '1', 'seo', '', 'cn', '0', '1553566003');
INSERT INTO `ey_config` VALUES ('63', 'web_cmspath', '', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('64', 'web_sqldatapath', '/data/sqldata', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('65', 'web_cmsurl', '', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('66', 'web_templets_dir', '/template', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('67', 'web_templeturl', '/template', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('68', 'web_templets_pc', '/template/pc', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('69', 'web_templets_m', '/template/mobile', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('70', 'web_eyoucms', 'http://www.eyoucms.com', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('78', '_cmscopyright', 'cbQ1AmriPQ7LHyth9xeHH6Lj', 'php', '', 'cn', '0', '1571040555');
INSERT INTO `ey_config` VALUES ('76', 'seo_liststitle_format', '2', 'seo', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('77', 'web_status', '0', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('80', 'web_is_authortoken', '-1', 'web', '', 'cn', '0', '1624416375');
INSERT INTO `ey_config` VALUES ('81', 'web_adminbasefile', '/login.php', 'web', '', 'cn', '0', '1614152866');
INSERT INTO `ey_config` VALUES ('82', 'seo_rewrite_format', '1', 'seo', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('83', 'web_cmsmode', '2', 'web', '', 'cn', '0', '1610588047');
INSERT INTO `ey_config` VALUES ('84', 'web_htmlcache_expires_in', '0', 'web', '', 'cn', '0', '1546477337');
INSERT INTO `ey_config` VALUES ('85', 'web_show_popup_upgrade', '-1', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('86', 'web_weapp_switch', '1', 'web', '', 'cn', '0', '1563498417');
INSERT INTO `ey_config` VALUES ('88', 'seo_dynamic_format', '1', 'seo', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('89', 'system_sql_mode', 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION', 'system', '', 'cn', '0', '1623322071');
INSERT INTO `ey_config` VALUES ('90', 'system_home_default_lang', 'cn', 'system', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('91', 'system_langnum', '1', 'system', '', 'cn', '0', '1610357910');
INSERT INTO `ey_config` VALUES ('170', 'web_exception', '0', 'web', '', 'cn', '0', '1546477337');
INSERT INTO `ey_config` VALUES ('171', 'web_language_switch', '0', 'web', '', 'cn', '0', '1610357915');
INSERT INTO `ey_config` VALUES ('174', 'web_is_https', '0', 'web', '', 'cn', '0', '1552968816');
INSERT INTO `ey_config` VALUES ('176', 'smtp_syn_weapp', '1', 'smtp', '', 'cn', '0', '1553566547');
INSERT INTO `ey_config` VALUES ('178', 'php_eyou_blacklist', '', 'php', '', 'cn', '0', '1553654429');
INSERT INTO `ey_config` VALUES ('190', 'system_auth_code', 'JwAhf0gPmHIJIZbiPlca', 'system', '', 'cn', '0', '1557733856');
INSERT INTO `ey_config` VALUES ('192', 'system_upgrade_filelist', 'YXBwbGljYXRpb24vY29tbW9uL2JlaGF2aW9yL0FwcEluaXRCZWhhdmlvci5waHA8YnI+YXBwbGljYXRpb24vY29tbW9uL2JlaGF2aW9yL0luaXRIb29rQmVoYXZpb3IucGhwPGJyPmFwcGxpY2F0aW9uL2NvbW1vbi9sb2dpYy9Pc3NMb2dpYy5waHA8YnI+YXBwbGljYXRpb24vY29tbW9uL2xvZ2ljL1Ntc0xvZ2ljLnBocDxicj5hcHBsaWNhdGlvbi9jb21tb24vbG9naWMvQXJjdHlwZUxvZ2ljLnBocDxicj5hcHBsaWNhdGlvbi9jb21tb24vbW9kZWwvQXJjdHlwZS5waHA8YnI+YXBwbGljYXRpb24vY29tbW9uL21vZGVsL1NxbENhY2hlVGFibGUucGhwPGJyPmFwcGxpY2F0aW9uL2NvbW1vbi9tb2RlbC9DaGFubmVsdHlwZS5waHA8YnI+YXBwbGljYXRpb24vY29tbW9uL21vZGVsL0V5b3VVc2Vycy5waHA8YnI+YXBwbGljYXRpb24vY29tbW9uL21vZGVsL1RhZ2xpc3QucGhwPGJyPmFwcGxpY2F0aW9uL2NvbW1vbi9tb2RlbC9MYW5ndWFnZS5waHA8YnI+YXBwbGljYXRpb24vcm91dGUucGhwPGJyPmFwcGxpY2F0aW9uL3VzZXIvY29udHJvbGxlci9QYXlBcGkucGhwPGJyPmFwcGxpY2F0aW9uL3VzZXIvY29udHJvbGxlci9QYXkucGhwPGJyPmFwcGxpY2F0aW9uL3VzZXIvY29udHJvbGxlci9NZWRpYS5waHA8YnI+YXBwbGljYXRpb24vdXNlci9jb250cm9sbGVyL0FydGljbGUucGhwPGJyPmFwcGxpY2F0aW9uL3VzZXIvY29udHJvbGxlci9Vc2Vyc1JlbGVhc2UucGhwPGJyPmFwcGxpY2F0aW9uL3VzZXIvY29udHJvbGxlci9CYXNlLnBocDxicj5hcHBsaWNhdGlvbi91c2VyL2NvbnRyb2xsZXIvRG93bmxvYWQucGhwPGJyPmFwcGxpY2F0aW9uL3VzZXIvY29udHJvbGxlci9Vc2Vyc05vdGljZS5waHA8YnI+YXBwbGljYXRpb24vdXNlci9jb250cm9sbGVyL1VzZXJzLnBocDxicj5hcHBsaWNhdGlvbi91c2VyL2NvbnRyb2xsZXIvVXBsb2FkaWZ5LnBocDxicj5hcHBsaWNhdGlvbi91c2VyL2xvZ2ljL1BheUxvZ2ljLnBocDxicj5hcHBsaWNhdGlvbi91c2VyL2xvZ2ljL1BheUFwaUxvZ2ljLnBocDxicj5hcHBsaWNhdGlvbi91c2VyL21vZGVsL0Rvd25sb2FkRmlsZS5waHA8YnI+YXBwbGljYXRpb24vdXNlci9tb2RlbC9Vc2Vyc1JlbGVhc2UucGhwPGJyPmFwcGxpY2F0aW9uL3VzZXIvbW9kZWwvUHJvZHVjdFNwZWNWYWx1ZS5waHA8YnI+YXBwbGljYXRpb24vdXNlci90ZW1wbGF0ZS91cGxvYWRpZnkvdXBsb2FkLmh0bTxicj5hcHBsaWNhdGlvbi9jb21tb24ucGhwPGJyPmFwcGxpY2F0aW9uL2Z1bmN0aW9uLnBocDxicj5hcHBsaWNhdGlvbi9hcGkvY29udHJvbGxlci9VaXNldC5waHA8YnI+YXBwbGljYXRpb24vYXBpL2NvbnRyb2xsZXIvdjEvQXBpLnBocDxicj5hcHBsaWNhdGlvbi9hcGkvY29udHJvbGxlci92MS9CYXNlLnBocDxicj5hcHBsaWNhdGlvbi9hcGkvY29udHJvbGxlci92MS9Vc2Vycy5waHA8YnI+YXBwbGljYXRpb24vYXBpL2NvbnRyb2xsZXIvQWpheC5waHA8YnI+YXBwbGljYXRpb24vYXBpL2xvZ2ljL3YxL0FwaUxvZ2ljLnBocDxicj5hcHBsaWNhdGlvbi9hcGkvbW9kZWwvdjEvQXBpLnBocDxicj5hcHBsaWNhdGlvbi9hcGkvbW9kZWwvdjEvVXNlci5waHA8YnI+YXBwbGljYXRpb24vYXBpL21vZGVsL3YxL0Jhc2UucGhwPGJyPmFwcGxpY2F0aW9uL2FwaS9tb2RlbC92MS9DYXRlZ29yeS5waHA8YnI+YXBwbGljYXRpb24vYXBpL21vZGVsL3YxL1VzZXJCYXNlLnBocDxicj5hcHBsaWNhdGlvbi9hcGkvbW9kZWwvdjEvU2hvcC5waHA8YnI+YXBwbGljYXRpb24vYXBpL3RlbXBsYXRlL3Vpc2V0L2FyY2xpc3RfbS5odG08YnI+YXBwbGljYXRpb24vYXBpL3RlbXBsYXRlL3Vpc2V0L2NvZGUuaHRtPGJyPmFwcGxpY2F0aW9uL2FwaS90ZW1wbGF0ZS91aXNldC9jb2RlX20uaHRtPGJyPmFwcGxpY2F0aW9uL2FwaS90ZW1wbGF0ZS91aXNldC9iYWNrZ3JvdW5kX20uaHRtPGJyPmFwcGxpY2F0aW9uL2FwaS90ZW1wbGF0ZS91aXNldC9tYXAuaHRtPGJyPmFwcGxpY2F0aW9uL2FwaS90ZW1wbGF0ZS91aXNldC9hcmNsaXN0Lmh0bTxicj5hcHBsaWNhdGlvbi9hcGkvdGVtcGxhdGUvdWlzZXQvbW9iaWxlX3RwbC5odG08YnI+YXBwbGljYXRpb24vYXBpL3RlbXBsYXRlL3Vpc2V0L2h0bWxfbS5odG08YnI+YXBwbGljYXRpb24vYXBpL3RlbXBsYXRlL3Vpc2V0L3VwbG9hZC5odG08YnI+YXBwbGljYXRpb24vYXBpL3RlbXBsYXRlL3Vpc2V0L3VwbG9hZF9tLmh0bTxicj5hcHBsaWNhdGlvbi9hcGkvdGVtcGxhdGUvdWlzZXQvdHlwZV9tLmh0bTxicj5hcHBsaWNhdGlvbi9hcGkvdGVtcGxhdGUvdWlzZXQvdGV4dF9tLmh0bTxicj5hcHBsaWNhdGlvbi9hcGkvdGVtcGxhdGUvdWlzZXQvY2hhbm5lbC5odG08YnI+YXBwbGljYXRpb24vYXBpL3RlbXBsYXRlL3Vpc2V0L2JhY2tncm91bmQuaHRtPGJyPmFwcGxpY2F0aW9uL2FwaS90ZW1wbGF0ZS91aXNldC9odG1sLmh0bTxicj5hcHBsaWNhdGlvbi9hcGkvdGVtcGxhdGUvdWlzZXQvbWFwX20uaHRtPGJyPmFwcGxpY2F0aW9uL2FwaS90ZW1wbGF0ZS91aXNldC90eXBlLmh0bTxicj5hcHBsaWNhdGlvbi9hcGkvdGVtcGxhdGUvdWlzZXQvY2hhbm5lbF9tLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi9jb21tb24ucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2JlaGF2aW9yL01vZHVsZUluaXRCZWhhdmlvci5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vYmVoYXZpb3IvQWN0aW9uQmVnaW5CZWhhdmlvci5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29udHJvbGxlci9GaWVsZC5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29udHJvbGxlci9JbWFnZXMucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvRmlsZW1hbmFnZXIucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvV2VhcHAucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvVWlzZXQucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvU3RhdGlzdGljcy5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29udHJvbGxlci9BZG1pbi5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29udHJvbGxlci9BcmN0eXBlLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9jb250cm9sbGVyL01lZGlhLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9jb250cm9sbGVyL0FyY2hpdmVzLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9jb250cm9sbGVyL0FydGljbGUucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvT3RoZXIucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvVWVkaXRvci5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29udHJvbGxlci9Vc2Vyc1JlbGVhc2UucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvU2hhcnAucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvTGV2ZWwucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvVGFncy5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29udHJvbGxlci9Qcm9kdWN0LnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9jb250cm9sbGVyL0Jhc2UucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvRW5jb2Rlcy5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29udHJvbGxlci9TcGVjaWFsLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9jb250cm9sbGVyL0d1ZXN0Ym9vay5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29udHJvbGxlci9BcmNoaXZlc0ZsYWcucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvVXBncmFkZS5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29udHJvbGxlci9Eb3dubG9hZC5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29udHJvbGxlci9TZW8ucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvQWRQb3NpdGlvbi5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29udHJvbGxlci9JbmRleC5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29udHJvbGxlci9BdXRoUm9sZS5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29udHJvbGxlci9SZWN5Y2xlQmluLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9jb250cm9sbGVyL0NoYW5uZWx0eXBlLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9jb250cm9sbGVyL01lbWJlci5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29udHJvbGxlci9TeXN0ZW0ucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvU2hvcFByb2R1Y3QucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvQ3VzdG9tLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9jb250cm9sbGVyL1Nob3AucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvQXNrLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9jb250cm9sbGVyL1VzZXJzTm90aWNlLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9jb250cm9sbGVyL1VwbG9hZGlmeS5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29udHJvbGxlci9BamF4LnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9jb250cm9sbGVyL0xpbmtzLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9jb250cm9sbGVyL0xpbmtzR3JvdXAucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvVXNlcnNTY29yZS5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29udHJvbGxlci9MYW5ndWFnZS5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vbG9naWMvV2VhcHBMb2dpYy5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vbG9naWMvQWpheExvZ2ljLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9sb2dpYy9GaWxlbWFuYWdlckxvZ2ljLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9sb2dpYy9VcGdyYWRlTG9naWMucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2xvZ2ljL0Fza0xvZ2ljLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9sb2dpYy9TaG9wTG9naWMucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2xvZ2ljL0FyY2hpdmVzTG9naWMucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2xvZ2ljL0ZpZWxkTG9naWMucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2xvZ2ljL01lbWJlckxvZ2ljLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9sb2dpYy9Qcm9kdWN0TG9naWMucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbmYvbWVudS5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29uZi9hdXRoX3J1bGUucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL21vZGVsL0ZpZWxkLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9tb2RlbC9JbWFnZXMucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL21vZGVsL1dlYXBwLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9tb2RlbC9NZWRpYS5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vbW9kZWwvRG93bmxvYWRGaWxlLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9tb2RlbC9BcmNoaXZlcy5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vbW9kZWwvQXJ0aWNsZS5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vbW9kZWwvU2luZ2xlLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9tb2RlbC9Qcm9kdWN0LnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9tb2RlbC9Qcm9kdWN0SW1nLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9tb2RlbC9TcGVjaWFsLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9tb2RlbC9Vc2Vyc1BhcmFtZXRlci5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vbW9kZWwvR3Vlc3Rib29rLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9tb2RlbC9Eb3dubG9hZC5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vbW9kZWwvQXV0aFJvbGUucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL21vZGVsL01lbWJlci5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vbW9kZWwvUHJvZHVjdFNwZWNWYWx1ZS5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vbW9kZWwvTWVkaWFGaWxlLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9tb2RlbC9DdXN0b20ucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL21vZGVsL0ltYWdlc1VwbG9hZC5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29uZmlnLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zaG9wX2NvbW1lbnQvY29tbWVudF9pbmRleC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvYXNrL2NvbmYuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2FyY3R5cGUvZWRpdC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvYXJjdHlwZS9hZGQuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2FyY3R5cGUvaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2FyY3R5cGUvc2luZ2xlX2VkaXQuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2FyY3R5cGUvYmF0Y2hfYWRkLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9maWVsZC9hdHRyaWJ1dGVfaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2ZpZWxkL2NoYW5uZWxfYWRkLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9maWVsZC9hZGRvbml0ZW0uaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2ZpZWxkL2NoYW5uZWxfZWRpdC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvZmllbGQvbW9kZWxmaWVsZC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvZmllbGQvYWRkb25leHRpdGVtLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9maWVsZC9jaGFubmVsX2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9hdXRoX3JvbGUvZWRpdC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvYXV0aF9yb2xlL2FkZC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvaW5kZXgvaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2luZGV4L3N3aXRjaF9tYXAuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2luZGV4L3dlbGNvbWUuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3NwZWNpYWwvZWRpdC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvc3BlY2lhbC9hZGQuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3NwZWNpYWwvaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3Vpc2V0L3VpX2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9ndWVzdGJvb2svYXR0cmlidXRlX2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9ndWVzdGJvb2svaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL21lZGlhL2VkaXQuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL21lZGlhL2FkZC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvbWVkaWEvaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2xpbmtzL2VkaXQuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2xpbmtzL2FkZC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvbGlua3MvaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL290aGVyL3VpX2FkZC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvb3RoZXIvZWRpdC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvb3RoZXIvYWRkLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9vdGhlci9pbmRleC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvb3RoZXIvdWlfZWRpdC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvc2hvcC9sZWZ0Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zaG9wL2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zaG9wL3Nob3BfYmFyLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zaG9wL2NvbmYuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3Rvb2xzL2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS90b29scy9yZXN0b3JlLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9sYW5ndWFnZS9jdXN0b212YXJfYXJjdHlwZS5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvbGFuZ3VhZ2Uvb2ZmaWNpYWxfcGFja19pbmRleC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvbGFuZ3VhZ2UvaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2xhbmd1YWdlL3BhY2tfaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3JlY3ljbGVfYmluL2FyY2hpdmVzX2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9yZWN5Y2xlX2Jpbi9jdXN0b212YXJfaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3JlY3ljbGVfYmluL2dib29rYXR0cl9pbmRleC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvcmVjeWNsZV9iaW4vYXJjdHlwZV9pbmRleC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvcmVjeWNsZV9iaW4vcHJvYXR0cl9pbmRleC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvc2hhcnAvZWRpdC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvc2hhcnAvYWN0aXZlX3RpbWVfaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3NoYXJwL2FkZC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvc2hhcnAvaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3NoYXJwL2dvb2RzX2xpc3QuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3NoYXJwL2FjdGl2ZV9pbmRleC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvc2VvL3NpdGUuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3Nlby9hcnRpY2xlLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zZW8vc2VvLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zZW8vY2hhbm5lbC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvdGFncy9lZGl0Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS90YWdzL2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS90YWdzL3JlbGF0aW9uX2FyY2hpdmVzLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS90YWdzL2VkaXRfaW5kZXhfc2VvLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zaG9wX3Byb2R1Y3QvYXR0cmlidXRlX2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zaG9wX3Byb2R1Y3QvZWRpdC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvc2hvcF9wcm9kdWN0L2FkZC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvc2hvcF9wcm9kdWN0L2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zaG9wX3Byb2R1Y3QvdGFnc19idG4uaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3Nob3BfcHJvZHVjdC9hdHRybGlzdF9pbmRleC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvYWRfcG9zaXRpb24vZWRpdC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvYWRfcG9zaXRpb24vYWRkLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9hZF9wb3NpdGlvbi9pbmRleC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvY3VzdG9tL2VkaXQuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2N1c3RvbS9hZGQuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2N1c3RvbS9pbmRleC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvdXNlcnNfc2NvcmUvaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2FkbWluL2xvZ2luX3p5Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9hZG1pbi9sb2dpbi5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvc3lzdGVtL2Jhc2ljLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zeXN0ZW0vY3VzdG9tdmFyX2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zeXN0ZW0vbm90aWZ5Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zeXN0ZW0vd2F0ZXIuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3N5c3RlbS9zbXRwLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zeXN0ZW0vd2ViMi5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvc3lzdGVtL2FwaV9jb25mLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zeXN0ZW0vbWljcm9zaXRlLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zeXN0ZW0vd2ViLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9hcnRpY2xlL2VkaXQuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2FydGljbGUvYWRkLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9hcnRpY2xlL2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9hcnRpY2xlL2ZyZWVfY29udGVudC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvbGV2ZWwvdXBncmFkZV9pbmRleC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvYXJjaGl2ZXMvaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2FyY2hpdmVzL2luZGV4X2RyYWZ0Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9hcmNoaXZlcy9pbmRleF9hcmNoaXZlcy5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvYXJjaGl2ZXMvZmxhZ19idG4uaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2FyY2hpdmVzL2dldF9maWVsZF9hZGRvbmV4dGl0ZW0uaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3Byb2R1Y3QvYXR0cmlidXRlX2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9wcm9kdWN0L2VkaXQuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3Byb2R1Y3QvYWRkLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9wcm9kdWN0L2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9jaGFubmVsdHlwZS9lZGl0Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zdGF0aXN0aWNzL2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9lbmNvZGVzL3RoZW1lX2NvbmYuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL21lbWJlci9tZWRpYV9pbmRleC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvbWVtYmVyL3VzZXJzX2ZpZWxkLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9tZW1iZXIvYXJ0aWNsZV9vcmRlcl9kZXRhaWxzLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9tZW1iZXIvdXNlcnNfaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL21lbWJlci9hcnRpY2xlX2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9tZW1iZXIvbW9uZXlfaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL21lbWJlci91c2Vyc19lZGl0Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9tZW1iZXIvb3JkZXJfYmFyLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9kb3dubG9hZC9lZGl0Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9kb3dubG9hZC9hZGQuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2Rvd25sb2FkL2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS91c2Vyc19ub3RpY2UvYWRtaW5fbm90aWNlX2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS91c2Vyc19ub3RpY2UvZWRpdC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvdXNlcnNfbm90aWNlL2FkZC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvdXNlcnNfbm90aWNlL2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9wdWJsaWMvbGVmdC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvcHVibGljL3RoZW1lX2Nzcy5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvc2hvcF9zZXJ2aWNlL2FmdGVyX3NlcnZpY2UuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2ltYWdlcy9lZGl0Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9pbWFnZXMvYWRkLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9pbWFnZXMvaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2V4dHJhL2Vycm9yX2NvZGUucGhwPGJyPmFwcGxpY2F0aW9uL2V4dHJhL2dsb2JhbC5waHA8YnI+YXBwbGljYXRpb24vaG9tZS9jb21tb24ucGhwPGJyPmFwcGxpY2F0aW9uL2hvbWUvYmVoYXZpb3IvVmlld0ZpbHRlckJlaGF2aW9yLnBocDxicj5hcHBsaWNhdGlvbi9ob21lL2NvbnRyb2xsZXIvTGlzdHMucGhwPGJyPmFwcGxpY2F0aW9uL2hvbWUvY29udHJvbGxlci9WaWV3LnBocDxicj5hcHBsaWNhdGlvbi9ob21lL2NvbnRyb2xsZXIvVGFncy5waHA8YnI+YXBwbGljYXRpb24vaG9tZS9jb250cm9sbGVyL0Jhc2UucGhwPGJyPmFwcGxpY2F0aW9uL2hvbWUvY29udHJvbGxlci9TZWFyY2gucGhwPGJyPmFwcGxpY2F0aW9uL2hvbWUvY29udHJvbGxlci9Bc2sucGhwPGJyPmFwcGxpY2F0aW9uL2hvbWUvY29udHJvbGxlci9CdWlsZGh0bWwucGhwPGJyPmFwcGxpY2F0aW9uL2hvbWUvbG9naWMvQXNrTG9naWMucGhwPGJyPmFwcGxpY2F0aW9uL2hvbWUvbG9naWMvRmllbGRMb2dpYy5waHA8YnI+YXBwbGljYXRpb24vaG9tZS9tb2RlbC9BcnRpY2xlLnBocDxicj5hcHBsaWNhdGlvbi9ob21lL21vZGVsL1Byb2R1Y3RBdHRyLnBocDxicj5hcHBsaWNhdGlvbi9ob21lL21vZGVsL0Fzay5waHA8YnI+Y29yZS9saWJyYXJ5L3RoaW5rL1JlcXVlc3QucGhwPGJyPmNvcmUvbGlicmFyeS90aGluay9kYi9kcml2ZXIvRHJpdmVyLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvcHJvY2Vzcy9iaHZjb3JlL0JodmFkbWluQUJlZ2luLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvcHJvY2Vzcy9iaHZjb3JlL0JodnVzZXJBQmVnaW4ucGhwPGJyPmNvcmUvbGlicmFyeS90aGluay9UZW1wbGF0ZS5waHA8YnI+Y29yZS9saWJyYXJ5L3RoaW5rL3ZpZXcvZHJpdmVyL1RoaW5rLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2V5b3UvVGFnVWl0eXBlLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2V5b3UvVGFnVWkucGhwPGJyPmNvcmUvbGlicmFyeS90aGluay90ZW1wbGF0ZS90YWdsaWIvZXlvdS9UYWdUYWdhcmNsaXN0LnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2V5b3UvVGFnU3BjYXJ0LnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2V5b3UvVGFnU3BzdWJtaXRvcmRlci5waHA8YnI+Y29yZS9saWJyYXJ5L3RoaW5rL3RlbXBsYXRlL3RhZ2xpYi9leW91L1RhZ1VpdGV4dC5waHA8YnI+Y29yZS9saWJyYXJ5L3RoaW5rL3RlbXBsYXRlL3RhZ2xpYi9leW91L1RhZ1VpY29kZS5waHA8YnI+Y29yZS9saWJyYXJ5L3RoaW5rL3RlbXBsYXRlL3RhZ2xpYi9leW91L1RhZ1VpY2hhbm5lbC5waHA8YnI+Y29yZS9saWJyYXJ5L3RoaW5rL3RlbXBsYXRlL3RhZ2xpYi9leW91L1RhZ1VpdXBsb2FkLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2V5b3UvQmFzZS5waHA8YnI+Y29yZS9saWJyYXJ5L3RoaW5rL3RlbXBsYXRlL3RhZ2xpYi9leW91L1RhZ0xpc3QucGhwPGJyPmNvcmUvbGlicmFyeS90aGluay90ZW1wbGF0ZS90YWdsaWIvZXlvdS9UYWdTcG9yZGVybGlzdC5waHA8YnI+Y29yZS9saWJyYXJ5L3RoaW5rL3RlbXBsYXRlL3RhZ2xpYi9leW91L1RhZ1VpYXJjbGlzdC5waHA8YnI+Y29yZS9saWJyYXJ5L3RoaW5rL3RlbXBsYXRlL3RhZ2xpYi9leW91L1RhZ0NoYW5uZWwucGhwPGJyPmNvcmUvbGlicmFyeS90aGluay90ZW1wbGF0ZS90YWdsaWIvZXlvdS9UYWdGbGluay5waHA8YnI+Y29yZS9saWJyYXJ5L3RoaW5rL3RlbXBsYXRlL3RhZ2xpYi9leW91L1RhZ1VpaHRtbC5waHA8YnI+Y29yZS9saWJyYXJ5L3RoaW5rL3RlbXBsYXRlL3RhZ2xpYi9leW91L1RhZ0RpeXVybC5waHA8YnI+Y29yZS9saWJyYXJ5L3RoaW5rL3RlbXBsYXRlL3RhZ2xpYi9leW91L1RhZ1ZpZGVvbGlzdC5waHA8YnI+Y29yZS9saWJyYXJ5L3RoaW5rL3RlbXBsYXRlL3RhZ2xpYi9leW91L1RhZ0FyY2xpc3QucGhwPGJyPmNvcmUvbGlicmFyeS90aGluay90ZW1wbGF0ZS90YWdsaWIvZXlvdS9UYWdTcHBheWFwaWxpc3QucGhwPGJyPmNvcmUvbGlicmFyeS90aGluay90ZW1wbGF0ZS90YWdsaWIvZXlvdS9UYWdDaGFubmVsYXJ0bGlzdC5waHA8YnI+Y29yZS9saWJyYXJ5L3RoaW5rL3RlbXBsYXRlL3RhZ2xpYi9leW91L1RhZ1NwZWNub2RlLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2V5b3UvVGFnVWliYWNrZ3JvdW5kLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2V5b3UvVGFnQXJ0aWNsZXBheS5waHA8YnI+Y29yZS9saWJyYXJ5L3RoaW5rL3RlbXBsYXRlL3RhZ2xpYi9leW91L1RhZ1ZpZGVvcGxheS5waHA8YnI+Y29yZS9saWJyYXJ5L3RoaW5rL3RlbXBsYXRlL3RhZ2xpYi9leW91L1RhZ1VpbWFwLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2V5b3UvVGFnU3RhdGljLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2FwaS9UYWdQcmVuZXh0LnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2FwaS9UYWdBcmN2aWV3LnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2FwaS9UYWdBZHYucGhwPGJyPmNvcmUvbGlicmFyeS90aGluay90ZW1wbGF0ZS90YWdsaWIvYXBpL1RhZ0FkLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2FwaS9UYWdDb2xsZWN0LnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2FwaS9CYXNlLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2FwaS9UYWdHbG9iYWwucGhwPGJyPmNvcmUvbGlicmFyeS90aGluay90ZW1wbGF0ZS90YWdsaWIvYXBpL1RhZ0xpc3QucGhwPGJyPmNvcmUvbGlicmFyeS90aGluay90ZW1wbGF0ZS90YWdsaWIvYXBpL1RhZ0NoYW5uZWwucGhwPGJyPmNvcmUvbGlicmFyeS90aGluay90ZW1wbGF0ZS90YWdsaWIvYXBpL1RhZ0ZsaW5rLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2FwaS9UYWdBcmNsaXN0LnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2FwaS9UYWdHdWVzdGJvb2tmb3JtLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2FwaS9UYWdUeXBlLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL0V5b3UucGhwPGJyPmRhdGEvc2NoZW1hL2V5X21lZGlhX29yZGVyLnBocDxicj5kYXRhL3NjaGVtYS9leV9hcnRpY2xlX3BheS5waHA8YnI+ZGF0YS9zY2hlbWEvZXlfYXJjaGl2ZXMucGhwPGJyPmRhdGEvc2NoZW1hL2V5X3Nob3Bfb3JkZXIucGhwPGJyPmRhdGEvc2NoZW1hL2V5X3NoYXJwX2dvb2RzLnBocDxicj5kYXRhL3NjaGVtYS9leV9saW5rcy5waHA8YnI+ZGF0YS9zY2hlbWEvZXlfYXJ0aWNsZV9vcmRlci5waHA8YnI+ZGF0YS9zY2hlbWEvZXlfdGFnaW5kZXgucGhwPGJyPmRhdGEvc2NoZW1hL2V5X3NxbF9jYWNoZV90YWJsZS5waHA8YnI+ZGF0YS9zY2hlbWEvZXlfY2hhbm5lbGZpZWxkLnBocDxicj5kYXRhL3NjaGVtYS9leV93eF91c2Vycy5waHA8YnI+ZGF0YS9zY2hlbWEvZXlfdXNlcnMucGhwPGJyPmRhdGEvc2NoZW1hL2V5X3VpX2NvbmZpZy5waHA8YnI+ZGF0YS9zY2hlbWEvZXlfYXNrX2Fuc3dlcl9saWtlLnBocDxicj5kYXRhL2NvbmYvdmVyc2lvbi50eHQ8YnI+ZGF0YS9jb25mL3RhZ2FpZHNfMTYxOTE0MTU3NC50eHQ8YnI+ZGF0YS93ZWFwcC9TYW1wbGUvYXBwbGljYXRpb24vcGx1Z2lucy9jb250cm9sbGVyL1NhbXBsZS5waHA8YnI+ZGF0YS93ZWFwcC9TYW1wbGUvYXBwbGljYXRpb24vcGx1Z2lucy9sb2dpYy9TYW1wbGVMb2dpYy5waHA8YnI+ZGF0YS93ZWFwcC9TYW1wbGUvd2VhcHAvU2FtcGxlL3RlbXBsYXRlL2hlYWRlci5odG08YnI+ZGF0YS93ZWFwcC9TYW1wbGUvd2VhcHAvU2FtcGxlL3RlbXBsYXRlL2VkaXQuaHRtPGJyPmRhdGEvd2VhcHAvU2FtcGxlL3dlYXBwL1NhbXBsZS90ZW1wbGF0ZS9hZGQuaHRtPGJyPmRhdGEvd2VhcHAvU2FtcGxlL3dlYXBwL1NhbXBsZS90ZW1wbGF0ZS9pbmRleC5odG08YnI+ZGF0YS93ZWFwcC9TYW1wbGUvd2VhcHAvU2FtcGxlL3RlbXBsYXRlL3NraW4vY3NzL3BhZ2UuY3NzPGJyPmRhdGEvd2VhcHAvU2FtcGxlL3dlYXBwL1NhbXBsZS90ZW1wbGF0ZS9za2luL2Nzcy9tYWluLmNzczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9ja2VkaXRvcl9iYXNpYy5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2NsaXBib2FyZC9kaWFsb2dzL3Bhc3RlLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMveG1sL3BsdWdpbi5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3NtaWxleS9kaWFsb2dzL3NtaWxleS5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3NtaWxleS9pbWFnZXMvdG91bmdlX3NtaWxlLmdpZjxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3NtaWxleS9pbWFnZXMvZW52ZWxvcGUuZ2lmPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc21pbGV5L2ltYWdlcy90aHVtYnNfZG93bi5naWY8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zbWlsZXkvaW1hZ2VzL3doYXRjaHV0YWxraW5nYWJvdXRfc21pbGUuZ2lmPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc21pbGV5L2ltYWdlcy9zYWRfc21pbGUuZ2lmPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc21pbGV5L2ltYWdlcy93aW5rX3NtaWxlLmdpZjxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3NtaWxleS9pbWFnZXMvZGV2aWxfc21pbGUuZ2lmPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc21pbGV5L2ltYWdlcy90aHVtYnNfdXAuZ2lmPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc21pbGV5L2ltYWdlcy9jcnlfc21pbGUuZ2lmPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc21pbGV5L2ltYWdlcy9icm9rZW5faGVhcnQuZ2lmPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc21pbGV5L2ltYWdlcy9jb25mdXNlZF9zbWlsZS5naWY8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zbWlsZXkvaW1hZ2VzL2xpZ2h0YnVsYi5naWY8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zbWlsZXkvaW1hZ2VzL3JlZ3VsYXJfc21pbGUuZ2lmPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc21pbGV5L2ltYWdlcy9vbWdfc21pbGUuZ2lmPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc21pbGV5L2ltYWdlcy9oZWFydC5naWY8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zbWlsZXkvaW1hZ2VzL3RlZXRoX3NtaWxlLmdpZjxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3NtaWxleS9pbWFnZXMva2lzcy5naWY8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zbWlsZXkvaW1hZ2VzL2FuZ2VsX3NtaWxlLmdpZjxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3NtaWxleS9pbWFnZXMvZW1iYXJlc3NlZF9zbWlsZS5naWY8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zbWlsZXkvaW1hZ2VzL2FuZ3J5X3NtaWxlLmdpZjxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3NtaWxleS9pbWFnZXMvc2hhZGVzX3NtaWxlLmdpZjxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2ZpbmQvZGlhbG9ncy9maW5kLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvYWRvYmVhaXIvcGx1Z2luLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvZG9jcHJvcHMvZGlhbG9ncy9kb2Nwcm9wcy5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2RvY3Byb3BzL3BsdWdpbi5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3ByZXZpZXcvcHJldmlldy5odG1sPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvcGFnZWJyZWFrL2ltYWdlcy9wYWdlYnJlYWsuZ2lmPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvaWZyYW1lZGlhbG9nL3BsdWdpbi5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2ltYWdlL2RpYWxvZ3MvaW1hZ2UuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9mb3Jtcy9kaWFsb2dzL3RleHRhcmVhLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvZm9ybXMvZGlhbG9ncy9yYWRpby5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2Zvcm1zL2RpYWxvZ3MvYnV0dG9uLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvZm9ybXMvZGlhbG9ncy9zZWxlY3QuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9mb3Jtcy9kaWFsb2dzL2NoZWNrYm94LmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvZm9ybXMvZGlhbG9ncy9mb3JtLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvZm9ybXMvZGlhbG9ncy9oaWRkZW5maWVsZC5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2Zvcm1zL2RpYWxvZ3MvdGV4dGZpZWxkLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvZm9ybXMvaW1hZ2VzL2hpZGRlbmZpZWxkLmdpZjxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3NwZWNpYWxjaGFyL2RpYWxvZ3Mvc3BlY2lhbGNoYXIuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zcGVjaWFsY2hhci9sYW5nL3VnLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc3BlY2lhbGNoYXIvbGFuZy9lbC5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3NwZWNpYWxjaGFyL2xhbmcvX3RyYW5zbGF0aW9uc3RhdHVzLnR4dDxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3NwZWNpYWxjaGFyL2xhbmcvbHYuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zcGVjaWFsY2hhci9sYW5nL2l0LmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc3BlY2lhbGNoYXIvbGFuZy9oZS5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3NwZWNpYWxjaGFyL2xhbmcvdHIuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zcGVjaWFsY2hhci9sYW5nL25iLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc3BlY2lhbGNoYXIvbGFuZy96aC1jbi5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3NwZWNpYWxjaGFyL2xhbmcvZnIuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zcGVjaWFsY2hhci9sYW5nL25sLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc3BlY2lhbGNoYXIvbGFuZy9rdS5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3NwZWNpYWxjaGFyL2xhbmcvZW8uanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zcGVjaWFsY2hhci9sYW5nL2RlLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc3BlY2lhbGNoYXIvbGFuZy9jcy5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3NwZWNpYWxjaGFyL2xhbmcvY3kuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zcGVjaWFsY2hhci9sYW5nL25vLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc3BlY2lhbGNoYXIvbGFuZy9mYS5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3NwZWNpYWxjaGFyL2xhbmcvZXQuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zcGVjaWFsY2hhci9sYW5nL3B0LWJyLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc3BlY2lhbGNoYXIvbGFuZy9lbi5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3NwZWNpYWxjaGFyL2xhbmcvZmkuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zcGVjaWFsY2hhci9sYW5nL2hyLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc3BlY2lhbGNoYXIvbGFuZy9zay5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2NvbG9yZGlhbG9nL2RpYWxvZ3MvY29sb3JkaWFsb2cuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9hZGRvbi9kaWFsb2dzL2FkZG9uLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvYWRkb24vcGx1Z2luLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvYWRkb24vaW1hZ2VzL2FkZG9uLmdpZjxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2RpYWxvZy9kaWFsb2dEZWZpbml0aW9uLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvYTExeWhlbHAvZGlhbG9ncy9hMTF5aGVscC5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2ExMXloZWxwL2xhbmcvdWcuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9hMTF5aGVscC9sYW5nL2VsLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvYTExeWhlbHAvbGFuZy9fdHJhbnNsYXRpb25zdGF0dXMudHh0PGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvYTExeWhlbHAvbGFuZy9sdi5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2ExMXloZWxwL2xhbmcvaXQuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9hMTF5aGVscC9sYW5nL2hlLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvYTExeWhlbHAvbGFuZy9yby5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2ExMXloZWxwL2xhbmcvdHIuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9hMTF5aGVscC9sYW5nL25iLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvYTExeWhlbHAvbGFuZy96aC1jbi5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2ExMXloZWxwL2xhbmcvZnIuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9hMTF5aGVscC9sYW5nL25sLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvYTExeWhlbHAvbGFuZy9rdS5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2ExMXloZWxwL2xhbmcvZW8uanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9hMTF5aGVscC9sYW5nL2RlLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvYTExeWhlbHAvbGFuZy9tay5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2ExMXloZWxwL2xhbmcvY3MuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9hMTF5aGVscC9sYW5nL2N5LmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvYTExeWhlbHAvbGFuZy9ndS5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2ExMXloZWxwL2xhbmcvbm8uanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9hMTF5aGVscC9sYW5nL3ZpLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvYTExeWhlbHAvbGFuZy9mYS5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2ExMXloZWxwL2xhbmcvcHQtYnIuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9hMTF5aGVscC9sYW5nL2VuLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvYTExeWhlbHAvbGFuZy9kYS5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2ExMXloZWxwL2xhbmcvZmkuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9hMTF5aGVscC9sYW5nL3NrLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvZGV2dG9vbHMvbGFuZy91Zy5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2RldnRvb2xzL2xhbmcvZWwuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9kZXZ0b29scy9sYW5nL190cmFuc2xhdGlvbnN0YXR1cy50eHQ8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9kZXZ0b29scy9sYW5nL2x2LmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvZGV2dG9vbHMvbGFuZy9pdC5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2RldnRvb2xzL2xhbmcvYmcuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9kZXZ0b29scy9sYW5nL2hlLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvZGV2dG9vbHMvbGFuZy90ci5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2RldnRvb2xzL2xhbmcvbmIuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9kZXZ0b29scy9sYW5nL3poLWNuLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvZGV2dG9vbHMvbGFuZy9mci5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2RldnRvb2xzL2xhbmcvbmwuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9kZXZ0b29scy9sYW5nL3VrLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvZGV2dG9vbHMvbGFuZy9rdS5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2RldnRvb2xzL2xhbmcvcGwuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9kZXZ0b29scy9sYW5nL2VvLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvZGV2dG9vbHMvbGFuZy9kZS5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2RldnRvb2xzL2xhbmcvY3MuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9kZXZ0b29scy9sYW5nL2N5LmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvZGV2dG9vbHMvbGFuZy9ndS5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2RldnRvb2xzL2xhbmcvbm8uanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9kZXZ0b29scy9sYW5nL3ZpLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvZGV2dG9vbHMvbGFuZy9mYS5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2RldnRvb2xzL2xhbmcvZXQuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9kZXZ0b29scy9sYW5nL3B0LWJyLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvZGV2dG9vbHMvbGFuZy9lbi5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2RldnRvb2xzL2xhbmcvZGEuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9kZXZ0b29scy9sYW5nL2ZpLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvZGV2dG9vbHMvbGFuZy9oci5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2RldnRvb2xzL2xhbmcvc2suanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9kZXZ0b29scy9wbHVnaW4uanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9saXN0c3R5bGUvZGlhbG9ncy9saXN0c3R5bGUuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9wYXN0ZXRleHQvZGlhbG9ncy9wYXN0ZXRleHQuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zY2F5dC9kaWFsb2dzL3Rvb2xiYXIuY3NzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc2NheXQvZGlhbG9ncy9vcHRpb25zLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvaWZyYW1lL2RpYWxvZ3MvaWZyYW1lLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvaWZyYW1lL2ltYWdlcy9wbGFjZWhvbGRlci5wbmc8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9wYXN0ZWZyb213b3JkL2ZpbHRlci9kZWZhdWx0LmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvYXV0b2dyb3cvcGx1Z2luLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvdGFibGVyZXNpemUvcGx1Z2luLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvdGFibGV0b29scy9kaWFsb2dzL3RhYmxlQ2VsbC5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3VpY29sb3IvZGlhbG9ncy91aWNvbG9yLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvdWljb2xvci9sYW5nL3VnLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvdWljb2xvci9sYW5nL2VsLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvdWljb2xvci9sYW5nL190cmFuc2xhdGlvbnN0YXR1cy50eHQ8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvbHYuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvaXQuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvYmcuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvaGUuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvdHIuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvbmIuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvemgtY24uanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvZnIuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvbmwuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvdWsuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcva3UuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvcGwuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvZW8uanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvZGUuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvbWsuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvY3MuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvY3kuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvbm8uanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvdmkuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvZmEuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvZXQuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvcHQtYnIuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvZW4uanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvZGEuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvZmkuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvaHIuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL2xhbmcvc2suanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL3BsdWdpbi5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3VpY29sb3IvdWljb2xvci5naWY8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL3l1aS9hc3NldHMvcGlja2VyX21hc2sucG5nPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvdWljb2xvci95dWkvYXNzZXRzL2h1ZV90aHVtYi5wbmc8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL3l1aS9hc3NldHMveXVpLmNzczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3VpY29sb3IveXVpL2Fzc2V0cy9waWNrZXJfdGh1bWIucG5nPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvdWljb2xvci95dWkvYXNzZXRzL2h1ZV9iZy5wbmc8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy91aWNvbG9yL3l1aS95dWkuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9kaXYvZGlhbG9ncy9kaXYuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9hYm91dC9kaWFsb2dzL2xvZ29fY2tlZGl0b3IucG5nPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvYWJvdXQvZGlhbG9ncy9hYm91dC5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2ZsYXNoL2RpYWxvZ3MvZmxhc2guanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9mbGFzaC9pbWFnZXMvcGxhY2Vob2xkZXIucG5nPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvYWpheC9wbHVnaW4uanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy90ZW1wbGF0ZXMvZGlhbG9ncy90ZW1wbGF0ZXMuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy90ZW1wbGF0ZXMvdGVtcGxhdGVzL2RlZmF1bHQuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy90ZW1wbGF0ZXMvdGVtcGxhdGVzL2ltYWdlcy90ZW1wbGF0ZTIuZ2lmPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvdGVtcGxhdGVzL3RlbXBsYXRlcy9pbWFnZXMvdGVtcGxhdGUzLmdpZjxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3RlbXBsYXRlcy90ZW1wbGF0ZXMvaW1hZ2VzL3RlbXBsYXRlMS5naWY8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy90YWJsZS9kaWFsb2dzL3RhYmxlLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc2hvd2Jsb2Nrcy9pbWFnZXMvYmxvY2tfaDEucG5nPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc2hvd2Jsb2Nrcy9pbWFnZXMvYmxvY2tfYmxvY2txdW90ZS5wbmc8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zaG93YmxvY2tzL2ltYWdlcy9ibG9ja19oMy5wbmc8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zaG93YmxvY2tzL2ltYWdlcy9ibG9ja19oMi5wbmc8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zaG93YmxvY2tzL2ltYWdlcy9ibG9ja19oNi5wbmc8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zaG93YmxvY2tzL2ltYWdlcy9ibG9ja19wcmUucG5nPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvc2hvd2Jsb2Nrcy9pbWFnZXMvYmxvY2tfYWRkcmVzcy5wbmc8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zaG93YmxvY2tzL2ltYWdlcy9ibG9ja19wLnBuZzxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3Nob3dibG9ja3MvaW1hZ2VzL2Jsb2NrX2Rpdi5wbmc8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zaG93YmxvY2tzL2ltYWdlcy9ibG9ja19oNC5wbmc8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zaG93YmxvY2tzL2ltYWdlcy9ibG9ja19oNS5wbmc8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy93c2MvZGlhbG9ncy93c2MuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy93c2MvZGlhbG9ncy90bXBGcmFtZXNldC5odG1sPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvd3NjL2RpYWxvZ3Mvd3NjLmNzczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3dzYy9kaWFsb2dzL2NpZnJhbWUuaHRtbDxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2xpbmsvZGlhbG9ncy9saW5rLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvbGluay9kaWFsb2dzL2FuY2hvci5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL2xpbmsvaW1hZ2VzL2FuY2hvci5naWY8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9zdHlsZXNoZWV0cGFyc2VyL3BsdWdpbi5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3BsYWNlaG9sZGVyL2RpYWxvZ3MvcGxhY2Vob2xkZXIuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9wbGFjZWhvbGRlci9sYW5nL3VnLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvcGxhY2Vob2xkZXIvbGFuZy9lbC5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3BsYWNlaG9sZGVyL2xhbmcvX3RyYW5zbGF0aW9uc3RhdHVzLnR4dDxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3BsYWNlaG9sZGVyL2xhbmcvbHYuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9wbGFjZWhvbGRlci9sYW5nL2l0LmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvcGxhY2Vob2xkZXIvbGFuZy9iZy5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3BsYWNlaG9sZGVyL2xhbmcvaGUuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9wbGFjZWhvbGRlci9sYW5nL3RyLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvcGxhY2Vob2xkZXIvbGFuZy9uYi5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3BsYWNlaG9sZGVyL2xhbmcvemgtY24uanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9wbGFjZWhvbGRlci9sYW5nL2ZyLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvcGxhY2Vob2xkZXIvbGFuZy9ubC5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3BsYWNlaG9sZGVyL2xhbmcvdWsuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9wbGFjZWhvbGRlci9sYW5nL2t1LmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvcGxhY2Vob2xkZXIvbGFuZy9wbC5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3BsYWNlaG9sZGVyL2xhbmcvZW8uanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9wbGFjZWhvbGRlci9sYW5nL2RlLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvcGxhY2Vob2xkZXIvbGFuZy9jcy5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3BsYWNlaG9sZGVyL2xhbmcvY3kuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9wbGFjZWhvbGRlci9sYW5nL25vLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvcGxhY2Vob2xkZXIvbGFuZy92aS5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3BsYWNlaG9sZGVyL2xhbmcvZmEuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9wbGFjZWhvbGRlci9sYW5nL2V0LmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvcGxhY2Vob2xkZXIvbGFuZy9wdC1ici5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3BsYWNlaG9sZGVyL2xhbmcvZW4uanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9wbGFjZWhvbGRlci9sYW5nL2RhLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvcGxhY2Vob2xkZXIvbGFuZy9maS5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3BsYWNlaG9sZGVyL2xhbmcvaHIuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvcGx1Z2lucy9wbGFjZWhvbGRlci9sYW5nL3NrLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvcGxhY2Vob2xkZXIvcGx1Z2luLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvcGxhY2Vob2xkZXIvcGxhY2Vob2xkZXIuZ2lmPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3BsdWdpbnMvYmJjb2RlL3BsdWdpbi5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9wbHVnaW5zL3N0eWxlcy9zdHlsZXMvZGVmYXVsdC5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9MSUNFTlNFLmh0bWw8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvY29uZmlnLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL2NrZWRpdG9yLnBocDxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9sYW5nL19sYW5ndWFnZXMuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvbGFuZy96aC1jbi5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9sYW5nL3poLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL2xhbmcvZW4tYXUuanM8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvbGFuZy9lbi5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9jb250ZW50cy5jc3M8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3IvY2tlZGl0b3JfYmFzaWNfc291cmNlLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL2NrZWRpdG9yX3BocDUucGhwPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL2NrZWRpdG9yLmpzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL2FkYXB0ZXJzL2pxdWVyeS5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci90aGVtZXMvZGVmYXVsdC90aGVtZS5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9ja2VkaXRvcl9waHA0LnBocDxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9pbWFnZXMvc3BhY2VyLmdpZjxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9za2lucy9rYW1hL2RpYWxvZy5jc3M8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3Ivc2tpbnMva2FtYS90ZW1wbGF0ZXMuY3NzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3NraW5zL2thbWEvc2tpbi5qczxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9za2lucy9rYW1hL2ljb25zX3J0bC5wbmc8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3Ivc2tpbnMva2FtYS9lZGl0b3IuY3NzPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3NraW5zL2thbWEvaWNvbnMucG5nPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3NraW5zL2thbWEvaW1hZ2VzL25vaW1hZ2UucG5nPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3NraW5zL2thbWEvaW1hZ2VzL2RpYWxvZ19zaWRlc19ydGwucG5nPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3NraW5zL2thbWEvaW1hZ2VzL21pbmkuZ2lmPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3NraW5zL2thbWEvaW1hZ2VzL3Nwcml0ZXNfaWU2LnBuZzxicj5wdWJsaWMvcGx1Z2lucy9ja2VkaXRvci9za2lucy9rYW1hL2ltYWdlcy9kaWFsb2dfc2lkZXMucG5nPGJyPnB1YmxpYy9wbHVnaW5zL2NrZWRpdG9yL3NraW5zL2thbWEvaW1hZ2VzL2RpYWxvZ19zaWRlcy5naWY8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3Ivc2tpbnMva2FtYS9pbWFnZXMvc3ByaXRlcy5wbmc8YnI+cHVibGljL3BsdWdpbnMvY2tlZGl0b3Ivc2tpbnMva2FtYS9pbWFnZXMvdG9vbGJhcl9zdGFydC5naWY8YnI+cHVibGljL3BsdWdpbnMvenRyZWUvY3NzL2lmcmFtZS5jc3M8YnI+cHVibGljL3BsdWdpbnMvbGF5ZXItdjMuMS4wL3RoZW1lL2RlZmF1bHQvbGF5ZXIuY3NzPGJyPnB1YmxpYy9wbHVnaW5zL1VlZGl0b3IvdWVkaXRvci5hbGwubWluLmpzPGJyPnB1YmxpYy9wbHVnaW5zL1VlZGl0b3IvdGhlbWVzL2lmcmFtZS5jc3M8YnI+cHVibGljL3BsdWdpbnMvVWVkaXRvci90aGVtZXMvZGVmYXVsdC9jc3MvdWVkaXRvci5jc3M8YnI+cHVibGljL3BsdWdpbnMvVWVkaXRvci91ZWRpdG9yLmFsbC5taW5fb2xkLmpzPGJyPnB1YmxpYy9wbHVnaW5zL1VlZGl0b3IvdWVkaXRvci5hbGwuanM8YnI+cHVibGljL3N0YXRpYy9jb21tb24vY3NzL2V5b3UuY3NzPGJyPnB1YmxpYy9zdGF0aWMvY29tbW9uL2Nzcy9leW91X20uY3NzPGJyPnB1YmxpYy9zdGF0aWMvY29tbW9uL2pzL2V5b3VfbW9iaWxlX3RwbC5qczxicj5wdWJsaWMvc3RhdGljL2NvbW1vbi9qcy9leW91X20uanM8YnI+cHVibGljL3N0YXRpYy9jb21tb24vanMvdGFnX2FydGljbGVwYXkuanM8YnI+cHVibGljL3N0YXRpYy9jb21tb24vanMvdmlld19hcmNyYW5rLmpzPGJyPnB1YmxpYy9zdGF0aWMvY29tbW9uL2pzL3RhZ192aWRlb2xpc3QuanM8YnI+cHVibGljL3N0YXRpYy9jb21tb24vanMvZXlvdS5qczxicj5wdWJsaWMvc3RhdGljL2NvbW1vbi9qcy90YWdfc3BwYXlhcGlsaXN0LmpzPGJyPnB1YmxpYy9zdGF0aWMvY29tbW9uL2pzL3RhZ19zcHB1cmNoYXNlLmpzPGJyPnB1YmxpYy9zdGF0aWMvY29tbW9uL2pzL3RhZ19nbG9iYWwuanM8YnI+cHVibGljL3N0YXRpYy9jb21tb24vanMvdGFnX3NwcGF5YXBpbGlzdF92Mi5qczxicj5wdWJsaWMvc3RhdGljL2NvbW1vbi9pbWFnZXMvcGhvbmVfYmcucG5nPGJyPnB1YmxpYy9zdGF0aWMvY29tbW9uL2ltYWdlcy91aXNldF9iYWNrLWJ0bi5wbmc8YnI+cHVibGljL3N0YXRpYy9jb21tb24vaW1hZ2VzL2RlZmF1bHRfbGl0cGljXzEucG5nPGJyPnB1YmxpYy9zdGF0aWMvY29tbW9uL2Rpc3BhdGNoX2p1bXAuaHRtPGJyPnB1YmxpYy9zdGF0aWMvYWRtaW4vY3NzL21haW4uY3NzPGJyPnB1YmxpYy9zdGF0aWMvYWRtaW4vbG9naW4vbG9naW4tbG9nb196eS5wbmc8YnI+cHVibGljL3N0YXRpYy9hZG1pbi9sb2dpbmJnL2xvZ2luLWJnLTMucG5nPGJyPnB1YmxpYy9zdGF0aWMvYWRtaW4vZm9udC9jc3MvaWNvbmZvbnQuY3NzPGJyPnB1YmxpYy9zdGF0aWMvYWRtaW4vZm9udC9mb250cy9pY29uZm9udC53b2ZmMjxicj5wdWJsaWMvc3RhdGljL2FkbWluL2ZvbnQvZm9udHMvaWNvbmZvbnQudHRmPGJyPnB1YmxpYy9zdGF0aWMvYWRtaW4vZm9udC9mb250cy9pY29uZm9udC53b2ZmPGJyPnB1YmxpYy9zdGF0aWMvYWRtaW4vanMvZ2xvYmFsLmpzPGJyPnB1YmxpYy9zdGF0aWMvYWRtaW4vanMvbXlBamF4Mi5qczxicj5wdWJsaWMvc3RhdGljL2FkbWluL2ltYWdlcy9tYXBfaWNvbl9zaHVpeWluLnBuZzxicj5wdWJsaWMvc3RhdGljL2FkbWluL2ltYWdlcy9tYXBfaWNvbl93ZW5kYW5nLnBuZzxicj5wdWJsaWMvc3RhdGljL2FkbWluL2ltYWdlcy9tYXBfaWNvbl9odWlzaG91emhhbi5wbmc8YnI+cHVibGljL3N0YXRpYy9hZG1pbi9pbWFnZXMvbWFwX2ljb25fcGluZGFvLnBuZzxicj5wdWJsaWMvc3RhdGljL2FkbWluL2ltYWdlcy9tYXBfaWNvbl9tb2Jhbi5wbmc8YnI+cHVibGljL3N0YXRpYy9hZG1pbi9pbWFnZXMvbG9naW4tbG9nb196eS5wbmc8YnI+cHVibGljL3N0YXRpYy9hZG1pbi9pbWFnZXMvbWFwX2ljb25fbGFubXV6aWR1YW4ucG5nPGJyPnB1YmxpYy9zdGF0aWMvYWRtaW4vaW1hZ2VzL2xvZ2luLWJnMi5qcGc8YnI+cHVibGljL3N0YXRpYy9hZG1pbi9pbWFnZXMvbWFwX2ljb25fdGFnLnBuZzxicj5wdWJsaWMvc3RhdGljL2FkbWluL2ltYWdlcy9tYXBfaWNvbl9ndWFubGl5dWFuLnBuZzxicj5wdWJsaWMvc3RhdGljL2FkbWluL2ltYWdlcy9tYXBfaWNvbl9zdW9sdWV0dS5wbmc8YnI+cHVibGljL3N0YXRpYy9hZG1pbi9pbWFnZXMvbWFwX2ljb25fYmVpZmVuaHVhbnl1YW4ucG5nPGJyPnB1YmxpYy9zdGF0aWMvdGVtcGxhdGUvdXNlcnNfdjIvdXNlcnNfcmVsZWFzZV9maWVsZC5odG08YnI+cHVibGljL3N0YXRpYy90ZW1wbGF0ZS91c2Vyc192Mi91c2Vyc19yZWxlYXNlX2ZpZWxkX20uaHRt', 'system', '', 'cn', '0', '1623322076');
INSERT INTO `ey_config` VALUES ('247', 'syn_admin_logic_video_addfields', '5', 'syn', '', 'cn', '0', '1614152870');
INSERT INTO `ey_config` VALUES ('248', 'syn_admin_logic_add_tag', '1', 'syn', '', 'cn', '0', '1591957363');
INSERT INTO `ey_config` VALUES ('249', 'syn_admin_logic_users_parameter', '1', 'syn', '', 'cn', '0', '1591957363');
INSERT INTO `ey_config` VALUES ('227', 'seo_html_arcdir', '', 'seo', '', 'cn', '0', '1623813160');
INSERT INTO `ey_config` VALUES ('228', 'seo_html_listname', '2', 'seo', '', 'cn', '0', '1567578996');
INSERT INTO `ey_config` VALUES ('229', 'seo_html_pagename', '2', 'seo', '', 'cn', '0', '1567578996');
INSERT INTO `ey_config` VALUES ('230', 'seo_force_inlet', '1', 'seo', '', 'cn', '0', '1567578996');
INSERT INTO `ey_config` VALUES ('193', 'system_version', 'v1.5.4', 'system', '', 'cn', '0', '1623322095');
INSERT INTO `ey_config` VALUES ('195', 'web_users_switch', '1', 'web', '', 'cn', '0', '1563498413');
INSERT INTO `ey_config` VALUES ('199', 'system_correctarctypedirpath', '1', 'system', '', 'cn', '0', '1563503940');
INSERT INTO `ey_config` VALUES ('203', 'web_attr_13', '/abf/uploads/allimg/20210115/1-210115153Z9511.png', 'web', '', 'cn', '0', '1610696352');
INSERT INTO `ey_config` VALUES ('225', 'system_synleveldata', '1', 'system', '', 'cn', '0', '1564532901');
INSERT INTO `ey_config` VALUES ('235', 'system_robots_edit', '1', 'system', '', 'cn', '0', '1571038279');
INSERT INTO `ey_config` VALUES ('237', 'syn_gb_attribute_showlist', '1', 'syn', '', 'cn', '0', '1576764161');
INSERT INTO `ey_config` VALUES ('238', 'system_smtp_tpl_5', '1', 'system', '', 'cn', '0', '1587364685');
INSERT INTO `ey_config` VALUES ('240', 'syn_admin_logic_sms_template', '1', 'syn', '', 'cn', '0', '1591262356');
INSERT INTO `ey_config` VALUES ('241', 'php_weapp_plugin_open', '1', 'php', '', 'cn', '0', '1624416375');
INSERT INTO `ey_config` VALUES ('243', 'syn_admin_logic_unlink', '1', 'syn', '', 'cn', '0', '1591262356');
INSERT INTO `ey_config` VALUES ('244', 'syn_admin_logic_update_basic', '1', 'syn', '', 'cn', '0', '1591262356');
INSERT INTO `ey_config` VALUES ('245', 'syn_admin_logic_update_tag', '1', 'syn', '', 'cn', '0', '1591262356');
INSERT INTO `ey_config` VALUES ('246', 'syn_admin_logic_update_arctype', '1', 'syn', '', 'cn', '0', '1591262356');
INSERT INTO `ey_config` VALUES ('250', 'syn_admin_logic_users_download', '1', 'syn', '', 'cn', '0', '1602320126');
INSERT INTO `ey_config` VALUES ('251', 'syn_admin_logic_special_addfields', '5', 'syn', '', 'cn', '0', '1614152870');
INSERT INTO `ey_config` VALUES ('252', 'syn_admin_logic_session_conf', '1', 'syn', '', 'cn', '0', '1602320145');
INSERT INTO `ey_config` VALUES ('253', 'syn_admin_logic_arctype_topid', '2', 'syn', '', 'cn', '0', '1610334638');
INSERT INTO `ey_config` VALUES ('254', 'syn_admin_logic_arctype_topid2', '1', 'syn', '', 'cn', '0', '1609929250');
INSERT INTO `ey_config` VALUES ('255', 'web_attr_15', 'demo@eyoucms.com', 'web', '', 'cn', '0', '1609930161');
INSERT INTO `ey_config` VALUES ('256', 'web_attr_16', '广东省广州市天河区某某科技园', 'web', '', 'cn', '0', '1609930161');
INSERT INTO `ey_config` VALUES ('257', 'web_attr_17', '/abf/uploads/allimg/20210106/1-2101061T919343.jpg', 'web', '', 'cn', '0', '1610696352');
INSERT INTO `ey_config` VALUES ('261', 'web_recordnum_mode', '0', 'web', '', 'cn', '0', '1609930161');
INSERT INTO `ey_config` VALUES ('262', 'thumb_open', '0', 'thumb', '', 'cn', '0', '1609985492');
INSERT INTO `ey_config` VALUES ('263', 'thumb_mode', '2', 'thumb', '', 'cn', '0', '1609985492');
INSERT INTO `ey_config` VALUES ('264', 'thumb_color', '#FFFFFF', 'thumb', '', 'cn', '0', '1609985492');
INSERT INTO `ey_config` VALUES ('265', 'thumb_width', '300', 'thumb', '', 'cn', '0', '1609985492');
INSERT INTO `ey_config` VALUES ('266', 'thumb_height', '300', 'thumb', '', 'cn', '0', '1609985492');
INSERT INTO `ey_config` VALUES ('297', 'system_usecodelist', 'a944VVVTVAUHAAEFUQwMBwJSWFFWAAIEUFMFB1BqFnYOVFcCDA1GGRVnVFsJWkFBHBMnCw1DU0ZfUBBDHRN2XAxRVgwVEQsFBwAAF00XcApJXAgKC0dEWxMdRCNVdV1BDFFWDBURCxcbEnVcGFhdDVlBEwsvVlpYEx1EK1hfTlAIGhRHPBoPUFFFExlDYFMEEh1DMQlSUEETHUQpREFdUghZVxUEGkYZFXFdXBhAWixDQkNIQHpZQUNfDw9WExgaJl1CDAIERhkVclBcBUBHC19EDRFAGxRuUF9ETRNyW1QNXVsRDAwKFxsSd1oOQUQRWV8VRk4Vcl1CUhMSQhMYGjJQVxAUFgVbFRwTbQhAWQplVENIQHVXQFJZMQBFVEYaTRp8ChINCFpWVEIXTRdgDFlfBQEaFRoWZlAID1hEYUoNGhRHMRYUXFZeVlAIT10EVxNNRjBYVFtFQkRNE39bTAhbXUdJQTddWEd3QA1ZYAZIRUNIQHRZWUFDAxJCYV1bFU1KAEdPRmZeV19QBRcYQX1YDw0BWENaRRNKQ3JQR1AOTUxHSUE3UFlDWEEIQ1FBHBMiAAlSTxYdEyoIUlRaSwQaFEcxEQVbRFZUR0MZFjRRQwQXQBsUZ1hDAw8THRZqCF9QEQYPDVZcEh0XKVxQBhIdQycHWUVBQhNKQ3ZEUUsVW1QQB0FIF3FHcloFUBZPEmATVkAbFHtBVBQARVhCXUMUGjcAFAVHUxIdFyxUREEcEzEREV9XWF0TSkN8XkFLBEsaSUczBUxFVVBRQxkWNEh8BAoXFRoWZl4UCl5DUF0TGhRHKQwHWk4SHRc1VEcIdl0OE0AbFHBYSFZRABMYGihWTgwRBkYZFWVCUBNGFk8SfQgRG1ZYFh0TLQRIXV1fCUwaSUc3BVJHXERGQxkWKFVIFgsQU0UWHRMxA11eU1EPGhRHJBYQWlVZRBdNF1cBAAAjVTNzdHJgeSQnfHN1aSB3ehI3MSZzdn9zQidhdQRqZjcyFXtjcgBwIApBdXxuWGpvNyciJl5bdmVZDVBwFH5rJVUNcmBfe2kjIEJmdQkJWWknESUxcAdycmQ7YGZTSGgkMgF6YF9rfSNQe1JsXw11fCMkLTxzfVV3BA1jZiV2YDYyVnR3YmN5MiR8c2VqEnx+MwwgFQBZU20aEFNgDEVtTjIKQWobY0AsVlRWRGw1C2E1BhpReWQHemU3B31UVAMUDgRaWQJ0Qi8DaUt/UjAADFI2Dj5tUl5DeiJcATB+RRABLl9sTXlWPyp7A38IN3ppJDw7D3R/ZlZBIm0EIXFjCjEmcHNAdGAeLGtkZkgEanknMzIxUn9nd1I2cGIxdGA0VSNgXnZ0YApQZWBmXCl8fSgpIVVdZnJWTRR0cgdncBsXM3FjBHBiCidpd1MIM3dUMzw1Ml1TeFZYJUZtUXJCKE8uXUddekdRVFBiBWEtem1QBjtUZ1QSbA', 'system', '', 'cn', '0', '1610350612');
INSERT INTO `ey_config` VALUES ('298', 'web_theme_color_model', '1', 'web', '', 'cn', '0', '1610357887');
INSERT INTO `ey_config` VALUES ('299', 'web_adminlogo', '/public/static/admin/images/logo_ey.png', 'web', '', 'cn', '0', '1610357887');
INSERT INTO `ey_config` VALUES ('300', 'web_loginlogo', '/public/static/admin/images/login-logo_ey.png', 'web', '', 'cn', '0', '1610357887');
INSERT INTO `ey_config` VALUES ('301', 'web_loginbgimg_model', '1', 'web', '', 'cn', '0', '1610352403');
INSERT INTO `ey_config` VALUES ('302', 'web_loginlogo', '/public/static/admin/images/login-logo_ey.png', 'web', '', 'cn', '0', '1610357887');
INSERT INTO `ey_config` VALUES ('303', 'web_loginbgimg', '/public/static/admin/images/login-bg.jpg', 'web', '', 'cn', '0', '1610352403');
INSERT INTO `ey_config` VALUES ('304', 'syn_admin_logic_1608884981', '1', 'syn', '', 'cn', '0', '1610352406');
INSERT INTO `ey_config` VALUES ('305', 'web_users_tpl_theme', '', 'web', '', 'cn', '0', '1610352449');
INSERT INTO `ey_config` VALUES ('272', 'php_serviceinfo', 'ebd3VgcGUlMDVQcABgRZUwAJBgReAAcFBVIEBwVDG1oOU1BEDRRATxESUlFGEmcVX01XXFY9WkBWCxQPBhQbWBRDXQlFUAsPVghEVxRYGjlGAFJTDz5ABQFWBmlCDVsAWWtAUVEDUz9GUVIAVD5NXVUPVGlNVlABADlDAVIJWGUUAAdSD2oXVQBdD2xDVA9RAGRFAw1aAmxGUVNTAWRMXwcHBEQbFAMWRw1YQlsRX1QRAhJpTVpXVQY5QwAFAV9lFA8FAFNqF1dWBgJsQ1sOUANkRQANVgNsRlFTBFZkTA9SDw06QgBVVwA5QgUBBl45Rg0EBVw+QFZVVQcXGxpYTBVfWhRaRQVREV8VbENaWgAGZEUACltTbEZdBlNTZEwNBFQAOkIPVFYDOUIEU1MLOUYMVQRZPkAHAVEOaUIOCgFZa0BQAAJRP0ZQAFRQPk1QBwhUaU0EUwACRxoXVk1NUQ5FWBVQB0xWEV8VbENaWgAGZEUACltTbEZdBlNTZEwNBFQAOkIPVFYDOUIEU1MLOUYMVQRZPkAHAVEOaUIOCgFZa0BQAAJRP0ZQAFRQPk1QBwhUaU0EUwACR0s', 'php', '', 'cn', '0', '1624416375');
INSERT INTO `ey_config` VALUES ('273', 'php_servicemeal', '0', 'php', '', 'cn', '0', '1616460897');
INSERT INTO `ey_config` VALUES ('274', 'php_servicecode', '', 'php', '', 'cn', '0', '1610614819');
INSERT INTO `ey_config` VALUES ('278', 'syn_admin_logic_check_oneself', '1', 'syn', '', 'cn', '0', '1610334638');
INSERT INTO `ey_config` VALUES ('279', 'syn_admin_logic_links_group', '1', 'syn', '', 'cn', '0', '1610334638');
INSERT INTO `ey_config` VALUES ('280', 'sms_type', '1', 'sms', '', 'cn', '0', '1610334638');
INSERT INTO `ey_config` VALUES ('282', 'syn_admin_logic_1608189503', '1', 'syn', '', 'cn', '0', '1610334638');
INSERT INTO `ey_config` VALUES ('306', 'download_select_servername', 'a:6:{i:0;s:12:\"立即下载\";i:1;s:15:\"本地服务器\";i:2;s:15:\"远程服务器\";i:3;s:12:\"百度网盘\";i:4;s:15:\"七牛云存储\";i:5;s:12:\"腾讯网盘\";}', 'download', '', 'cn', '0', '1610439420');
INSERT INTO `ey_config` VALUES ('285', 'syn_admin_logic_1608191377', '1', 'syn', '', 'cn', '0', '1610334638');
INSERT INTO `ey_config` VALUES ('286', 'system_paginate_pagesize', '100', 'system', '', 'cn', '0', '1616490137');
INSERT INTO `ey_config` VALUES ('287', 'web_theme_color', '#3398cc', 'web', '', 'cn', '0', '1610357887');
INSERT INTO `ey_config` VALUES ('288', 'web_assist_color', '#2189be', 'web', '', 'cn', '0', '1610357887');
INSERT INTO `ey_config` VALUES ('294', 'syn_admin_logic_1609039608', '1', 'syn', '', 'cn', '0', '1610334638');
INSERT INTO `ey_config` VALUES ('295', 'syn_admin_logic_1609291091', '1', 'syn', '', 'cn', '0', '1610334638');
INSERT INTO `ey_config` VALUES ('296', 'admin_logic_1610086647', '1', 'syn', '', 'cn', '0', '1610334638');
INSERT INTO `ey_config` VALUES ('307', 'web_mobile_domain_open', '0', 'web', '', 'cn', '0', '1610585089');
INSERT INTO `ey_config` VALUES ('308', 'web_login_expiretime', '3600', 'web', '', 'cn', '0', '1610585089');
INSERT INTO `ey_config` VALUES ('309', 'web_tpl_theme', '', 'web', '', 'cn', '0', '1610585089');
INSERT INTO `ey_config` VALUES ('310', 'syn_admin_logic_video_addfields_2', '1', 'syn', '', 'cn', '0', '1614152870');
INSERT INTO `ey_config` VALUES ('311', 'syn_admin_logic_1608884981_2', '1', 'syn', '', 'cn', '0', '1614152870');
INSERT INTO `ey_config` VALUES ('312', 'basic_indexname', '首页', 'basic', '', 'cn', '0', '1614152874');
INSERT INTO `ey_config` VALUES ('313', 'basic_img_style_wh', '0', 'basic', '', 'cn', '0', '1614152874');
INSERT INTO `ey_config` VALUES ('314', 'max_filesize', '100', 'basic', '', 'cn', '0', '1614152874');
INSERT INTO `ey_config` VALUES ('315', 'max_sizeunit', 'MB', 'basic', '', 'cn', '0', '1614152874');
INSERT INTO `ey_config` VALUES ('316', 'other_arcclick', '500|1000', 'other', '', 'cn', '0', '1614152874');
INSERT INTO `ey_config` VALUES ('317', 'other_arcdownload', '100|500', 'other', '', 'cn', '0', '1614152874');
INSERT INTO `ey_config` VALUES ('318', 'syn_admin_logic_balance_pay', '1', 'syn', '', 'cn', '0', '1616460912');
INSERT INTO `ey_config` VALUES ('319', 'syn_admin_logic_1610086648', '1', 'syn', '', 'cn', '0', '1616460912');
INSERT INTO `ey_config` VALUES ('320', 'syn_admin_logic_1614829120', '1', 'syn', '', 'cn', '0', '1616460912');
INSERT INTO `ey_config` VALUES ('321', 'syn_admin_logic_1616123192', '1', 'syn', '', 'cn', '0', '1616460912');
INSERT INTO `ey_config` VALUES ('322', 'syn_admin_logic_1614829121', '1', 'syn', '', 'cn', '0', '1623322108');
INSERT INTO `ey_config` VALUES ('323', 'syn_admin_logic_ask_answer_like', '1', 'syn', '', 'cn', '0', '1623322108');
INSERT INTO `ey_config` VALUES ('324', 'syn_admin_logic_archives_1618279798', '1', 'syn', '', 'cn', '0', '1623322108');
INSERT INTO `ey_config` VALUES ('325', 'syn_admin_logic_1623036205', '1', 'syn', '', 'cn', '0', '1623322108');
INSERT INTO `ey_config` VALUES ('326', 'admin_logic_1623055490', '1', 'syn', '', 'cn', '0', '1623322108');
INSERT INTO `ey_config` VALUES ('327', 'system_use_language', '0', 'system', '', 'cn', '0', '1623322108');
INSERT INTO `ey_config` VALUES ('328', 'admin_logic_1623133485', '1', 'syn', '', 'cn', '0', '1623322108');
INSERT INTO `ey_config` VALUES ('329', 'recycle_switch', '1', 'web', '', 'cn', '0', '1623809302');
INSERT INTO `ey_config` VALUES ('330', 'seo_rewrite_view_format', '1', 'seo', '', 'cn', '0', '1623809665');

-- -----------------------------
-- Table structure for `ey_config_attribute`
-- -----------------------------
DROP TABLE IF EXISTS `ey_config_attribute`;
CREATE TABLE `ey_config_attribute` (
  `attr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '表单id',
  `inc_type` varchar(20) DEFAULT '' COMMENT '变量分组',
  `attr_name` varchar(60) DEFAULT '' COMMENT '变量标题',
  `attr_var_name` varchar(50) DEFAULT '' COMMENT '变量名',
  `attr_input_type` tinyint(1) unsigned DEFAULT '0' COMMENT ' 0=文本框，1=下拉框，2=多行文本框，3=上传图片',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`attr_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='自定义变量表';

-- -----------------------------
-- Records of `ey_config_attribute`
-- -----------------------------
INSERT INTO `ey_config_attribute` VALUES ('1', 'web', '客服电话', 'web_attr_1', '0', 'cn', '1525962574', '1609930141');
INSERT INTO `ey_config_attribute` VALUES ('13', 'web', '手机端LOGO', 'web_attr_13', '3', 'cn', '1563793690', '1609930141');
INSERT INTO `ey_config_attribute` VALUES ('15', 'web', '邮箱地址', 'web_attr_15', '0', 'cn', '1609930141', '1609930141');
INSERT INTO `ey_config_attribute` VALUES ('16', 'web', '公司地址', 'web_attr_16', '0', 'cn', '1609930141', '1609930141');
INSERT INTO `ey_config_attribute` VALUES ('17', 'web', '公众号二维码', 'web_attr_17', '3', 'cn', '1609930141', '1609930141');

-- -----------------------------
-- Table structure for `ey_diyminipro`
-- -----------------------------
DROP TABLE IF EXISTS `ey_diyminipro`;
CREATE TABLE `ey_diyminipro` (
  `mini_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '模板ID',
  `categoryid` int(11) NOT NULL DEFAULT '0' COMMENT '分类ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '模板标题',
  `litpic` varchar(250) NOT NULL DEFAULT '' COMMENT '封面图',
  `component` text NOT NULL COMMENT '组件库',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：4=审核中，5=发布',
  `cloud_id` int(10) NOT NULL DEFAULT '0' COMMENT '云ID',
  `config` text NOT NULL COMMENT '相关序列化信息',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '伪删除，1=是，0=否',
  `lang` varchar(10) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`mini_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信小程序记录表';


-- -----------------------------
-- Table structure for `ey_diyminipro_page`
-- -----------------------------
DROP TABLE IF EXISTS `ey_diyminipro_page`;
CREATE TABLE `ey_diyminipro_page` (
  `page_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '页面id',
  `page_type` tinyint(3) NOT NULL DEFAULT '-1' COMMENT '页面类型(1首页 -1自定义页)',
  `page_name` varchar(255) NOT NULL DEFAULT '' COMMENT '页面名称',
  `page_data` longtext NOT NULL COMMENT '页面数据',
  `mini_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '微信小程序id',
  `is_home` tinyint(1) NOT NULL DEFAULT '0' COMMENT '设为首页：0=否，1=是',
  `is_system` tinyint(1) NOT NULL DEFAULT '0' COMMENT '系统内置',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示：1=显示，0=隐藏',
  `is_del` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '伪删除，1=是，0=否',
  `lang` varchar(10) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`page_id`),
  KEY `mini_id` (`mini_id`,`lang`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信小程序diy页面表';


-- -----------------------------
-- Table structure for `ey_diyminipro_setting`
-- -----------------------------
DROP TABLE IF EXISTS `ey_diyminipro_setting`;
CREATE TABLE `ey_diyminipro_setting` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '页面组',
  `value` text NOT NULL COMMENT '组装之后的值',
  `mini_id` int(11) NOT NULL DEFAULT '0' COMMENT '小程序ID',
  `lang` varchar(10) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `type` (`name`) USING BTREE,
  KEY `mini_id` (`mini_id`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信小程序多功能配置表';


-- -----------------------------
-- Table structure for `ey_download_attr_field`
-- -----------------------------
DROP TABLE IF EXISTS `ey_download_attr_field`;
CREATE TABLE `ey_download_attr_field` (
  `field_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `field_name` varchar(32) DEFAULT '' COMMENT '字段名称',
  `field_title` varchar(32) DEFAULT '' COMMENT '字段标题',
  `field_use` tinyint(1) DEFAULT '0' COMMENT '字段是否使用，0未使用，1为使用',
  `sort_order` smallint(5) DEFAULT '0' COMMENT '排序',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned DEFAULT '0' COMMENT '上传时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`field_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='上传文件属性表';

-- -----------------------------
-- Records of `ey_download_attr_field`
-- -----------------------------
INSERT INTO `ey_download_attr_field` VALUES ('1', 'extract_code', '提取码', '1', '1', 'cn', '1561001807', '1561024954');
INSERT INTO `ey_download_attr_field` VALUES ('2', 'server_name', '服务器名称', '1', '2', 'cn', '1561001807', '1561078673');

-- -----------------------------
-- Table structure for `ey_download_content`
-- -----------------------------
DROP TABLE IF EXISTS `ey_download_content`;
CREATE TABLE `ey_download_content` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aid` int(10) DEFAULT '0' COMMENT '文档ID',
  `content` longtext COMMENT '内容详情',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `news_id` (`aid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='下载附加表';

-- -----------------------------
-- Records of `ey_download_content`
-- -----------------------------
INSERT INTO `ey_download_content` VALUES ('1', '30', '&lt;p style=&quot;margin-top: 0px; margin-bottom: 0px; padding: 0px; -webkit-tap-highlight-color: transparent; color: rgb(51, 51, 51); font-family: 微软雅黑, &amp;quot;microsoft yahei&amp;quot;, ����, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;模板名称：&lt;br style=&quot;margin: 0px auto;&quot;/&gt;响应式工程机械挖掘机类网站织梦模板（自适应手机端）+利于SEO优化&lt;br style=&quot;margin: 0px auto;&quot;/&gt;模板介绍：&lt;br style=&quot;margin: 0px auto;&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;color: rgb(51, 51, 51); font-family: 微软雅黑, &amp;quot;microsoft yahei&amp;quot;, ����, Arial, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);&quot;&gt;织梦新内核开发的模板，该模板属于企业通用、HTML5响应式、工程机械、挖掘机、推土机类企业使用，一款适用性很强的模板，基本可以适合各行业的企业网站！响应式自适应各种移动设备，同一个后台，数据即时同步，简单适用！原创设计、手工书写DIV+CSS，兼容IE7+、Firefox、Chrome、360浏览器等；主流浏览器；页面简洁简单，容易管理，DEDE内核都可以使用；附带测试数据！&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 0px; padding: 0px; -webkit-tap-highlight-color: transparent; color: rgb(51, 51, 51); font-family: 微软雅黑, &amp;quot;microsoft yahei&amp;quot;, ����, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;模板特点：&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 0px; padding: 0px; -webkit-tap-highlight-color: transparent; color: rgb(51, 51, 51); font-family: 微软雅黑, &amp;quot;microsoft yahei&amp;quot;, ����, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;1：织梦自适应产品模板，代码简洁，风格大气高端，页面干净。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 0px; padding: 0px; -webkit-tap-highlight-color: transparent; color: rgb(51, 51, 51); font-family: 微软雅黑, &amp;quot;microsoft yahei&amp;quot;, ����, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;2：首页带新闻展示，服务介绍，案例展示等。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 0px; padding: 0px; -webkit-tap-highlight-color: transparent; color: rgb(51, 51, 51); font-family: 微软雅黑, &amp;quot;microsoft yahei&amp;quot;, ����, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;5：采用现在流行的HTML5框架，兼容主流的浏览器，响应式，自适应，支持移动设备&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 0px; padding: 0px; -webkit-tap-highlight-color: transparent; color: rgb(51, 51, 51); font-family: 微软雅黑, &amp;quot;microsoft yahei&amp;quot;, ����, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;6：整站界面设计大气，展现出你的实力。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 0px; padding: 0px; -webkit-tap-highlight-color: transparent; color: rgb(51, 51, 51); font-family: 微软雅黑, &amp;quot;microsoft yahei&amp;quot;, ����, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;7：后台直接修改联系方式、地址、版权信息，网站内容等，修改更加方便。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 0px; padding: 0px; -webkit-tap-highlight-color: transparent; color: rgb(51, 51, 51); font-family: 微软雅黑, &amp;quot;microsoft yahei&amp;quot;, ����, Arial, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;&amp;nbsp;使用程序：织梦DEDECMS版本都可以使用。&amp;nbsp;&lt;/p&gt;&lt;p&gt;&lt;a href=&quot;http://www.eyoucms.com/moban/16/668.html&quot; target=&quot;_self&quot;&gt;&lt;/a&gt;&lt;/p&gt;&lt;p&gt;&lt;img title=&quot;工程机械推土挖掘机类网站模板(图1)&quot; alt=&quot;工程机械推土挖掘机类网站模板(图1)&quot; style=&quot;max-width:100%!important;height:auto;&quot; src=&quot;/abc/uploads/allimg/20190114/4873105f54a14f3785047bd8ecc8b5ac.jpg&quot;/&gt;&lt;/p&gt;', '1610615767', '1610615767');
INSERT INTO `ey_download_content` VALUES ('2', '31', '&lt;p style=&quot;line-height: 1.75em;&quot;&gt;&lt;span style=&quot;color: rgb(51, 51, 51); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, FontAwesome, sans-serif; background-color: rgb(255, 255, 255);&quot;&gt;&lt;/span&gt;&lt;span style=&quot;font-size: 14px;&quot;&gt;&amp;nbsp; &amp;nbsp; &amp;nbsp; &amp;nbsp;DOM编程－window对象 回顾 请简述一下脚本执行的原理。 在JavaScript中有哪些控制语句及其含义？ 如何创建一个有参函数以及如何调用它？ 预习检查 解释名词“根节点”、“子节点”和“相邻节点“。 window对象常用的属性有哪些？ 请解释setTimeout( )方法的功能。 本章任务 本章目标 会运用DOM模型查找某个HTML元素 会使用window对象的open( )方法制作各种样式的广告窗口 会使用window对象的setTimeout( )方法和Date对象制作日期显示效果 HTML文档的树状结构 什么是DOM DOM－Document Object Model ,它是W3C国际组织的一套Web标准，它定义了访问HTML文档对象的一套属性、方法和事件 。 DOM对象模型-1 DOM对象模型-2 Window对象常用的属性 window对象常用的方法和事件 如何使用window对象-1 如何使用window对象-2-1 如何使用window对象-2-2 如何使用window对象-3 如何使用window对象-4 如何使用window对象-5 小结1 Date对象做时钟显示-1 Date对象做时钟显示-2 Date 对象存储的日期为自 1970 年 1 月 1 日 00:00:00 以来的毫秒数 Date对象做时钟显示-3 Date 方法的分组 Date对象做时钟显示-4 Date对象做时钟显示-5-1 Date对象做时钟显示-5-2 setTimeout的用法： setTimeout（“调用的函数”,”定时的时间”） 例：var myTime＝setTimeout( “disptime( )”, 1000 ) ; Date对象做时钟显示-6 小结2 history 和location对象-1 history 对象 方法 history 和location对象-2 Location 对象 属性 方法 history 和location对象-3 常见错误 总结 请简述HTML文档的树状结构？ window对象有哪些常用的方法及其含义？ 请列举Date对象有哪些方法？ 请解释setTimeout方法适用场合？&lt;/span&gt;&lt;a href=&quot;http://www.eyoucms.com/moban/10/673.html&quot; target=&quot;_self&quot; style=&quot;box-sizing: border-box; text-decoration-line: none; color: rgb(0, 102, 204); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, FontAwesome, sans-serif; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;&lt;/a&gt;&lt;/p&gt;', '1610615761', '1610615761');
INSERT INTO `ey_download_content` VALUES ('6', '91', '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;img style=&quot;max-width:100%!important;height:auto;&quot; src=&quot;/abc/uploads/ueditor/20190731/30ed041f3206e83d2435216560ee3db1.jpg&quot; title=&quot;计算机软件系统故障及维护(图1)&quot; alt=&quot;计算机软件系统故障及维护(图1)&quot;/&gt;&lt;/p&gt;&lt;p style=&quot;line-height: 1.75em;&quot;&gt;&lt;span style=&quot;font-size: 14px;&quot;&gt;WindowsXP操作系统原理使用系统维护工具系统启动故障的修复病毒防治的一般方法循辱魂币禾赫促陛醛放忆蛔睡钱佯回改波坏敏寄锈掳长提每臣传遥抄个似计算机软件系统故障及维护计算机软件系统故障及维护13.1.1WindowsXP的架构特点WindowsXP已不再完整支持16位DOS应用程序,仅有的CMD命令窗口(“开始”→“运行”→“输入CMD”)也只能执行一些基本DOS命令,对大多数直接控制硬件的16位应用程序是不支持的。从图13.1可以看出,WindowsXP还引入了用户模式和内核模式,以提高内核稳定性,在与硬件交互时增加了硬件抽象层(HAL)以提供抽象的硬件访问接口,避免了直接操作硬件,这些都是WindowsXP稳定性的主要保障。图13.1WindowsXP体系结构多芥批酒墙诚宰囱苍怜钧镊蝴父得似饲碾忻蔫姬烷岗富哉侈靡贝渡轮瞳憾计算机软件系统故障及维护计算机软件系统故障及维护(1)内核模式(KernelMode)。当CPU运行于内核模式时,一切程序都可运行。(2)用户模式(UserMode)。在这个模式中,硬件防止特权指令的执行,并对内存和I/O空间的访问操作进行检查。允许WindowsXP限制程序对各种I/O操作的访问,并捕捉违反系统完整性的任何行为。Windows系统文件夹下文件数量很多,并且随着安装软件的增加而递增。WindowsXP架构中的核心系统文件如表13.1所示。柔邢部酸边呀规踢矽阶护园烤膳曾按邢堆秀肛移软隋智天须吾肿屋存司汐计算机软件系统故障及维护计算机软件系统故障及维护1.预引导阶段在按下计算机电源到操作系统开始加载第一个文件前这段时间,称为预引导(Pre-Boot)阶段。此时,计算机首先运行PowerOnSelfTest(POST),POST检测系统的处理器、内存等硬件设备的状况。所有硬件设备都被自动识别和配置后,BIOS将会定位引导设备(如硬盘或光驱),然后MBR(MasterBootRecord)被加载并运行。在预引导阶段的最后将加载WindowsXP的NTLDR文件。2.引导阶段WindowsXP引导阶段包含4个步骤。&lt;/span&gt;&lt;/p&gt;', '1610615652', '1610615652');
INSERT INTO `ey_download_content` VALUES ('7', '92', '&lt;p style=&quot;margin-top: 5px; line-height: 1.75em;&quot;&gt;&lt;span style=&quot;color: rgb(51, 51, 51); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, FontAwesome, sans-serif; background-color: rgb(255, 255, 255); font-size: 14px;&quot;&gt;WindowsXP引导阶段包含4个步骤。Step1.引导载入程序的初始化NTLDR程序会将处理器由实模式(RealMode)切换为32位平面内存模式(32-bitFlatMemoryMode)。在实模式下,内存中的前640KB是为MS-DOS保留的,而剩余内存则被当作扩展内存使用,这样WindowsXP将无法使用全部的物理内存。而32位平面内存模式让WindowsXP能使用计算机上安装的所有内存(由于设计使然,32位Windows操作系统只能使用2&amp;amp;nbsp;GB,64位可使用4&amp;amp;nbsp;GB)。接下来,NTLDR启动内建的微型文件系统驱动(mini-filesystemdrivers),通过这个步骤,使NTLDR可以识别每一个用NTFS或FAT文件系统格式化的分区,以便发现和加载WindowsXP。绍面浅谣嚏腆莲罗蕉湿炊连抛伪俏哉认鼎苟幸砧常吱遁端克舌灼该发拎露计算机软件系统故障及维护计算机软件系统故障及维护Step2.选择操作系统这一步在单操作系统的计算机上不是必须的。如果计算机安装了不止一个操作系统(也就是多操作系统),而且正确设置了boot.ini,计算机会显示一个操作系统选项,这是NTLDR读取boot.ini的结果,操作系统选项的设置操作步骤是“系统属性”→“高级”→“启动和故障恢复”,如图13.2所示。图13.2操作系统选项设备询壶涨阔喇读灯短阔爱乘赠面因表澈拣讽械髓被蚕妮混告强司的咕链圃池计算机软件系统故障及维护计算机软件系统故障及维护例如,安装了WindowsXP和Windows2000的计算机系统分区根目录下&lt;/span&gt;&lt;/p&gt;', '1610615642', '1610615642');

-- -----------------------------
-- Table structure for `ey_download_file`
-- -----------------------------
DROP TABLE IF EXISTS `ey_download_file`;
CREATE TABLE `ey_download_file` (
  `file_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `aid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '产品ID',
  `title` varchar(200) DEFAULT '' COMMENT '产品标题',
  `file_url` varchar(255) DEFAULT '' COMMENT '文件存储路径',
  `extract_code` varchar(20) DEFAULT '' COMMENT '文件提取码',
  `file_size` varchar(255) DEFAULT '' COMMENT '文件大小',
  `file_ext` varchar(50) DEFAULT '' COMMENT '文件后缀名',
  `file_name` varchar(200) DEFAULT '' COMMENT '文件名',
  `server_name` varchar(200) DEFAULT '' COMMENT '服务器名称',
  `file_mime` varchar(200) DEFAULT '' COMMENT '文件类型',
  `uhash` varchar(200) DEFAULT '' COMMENT '自定义的一种加密方式，用于文件下载权限验证',
  `md5file` varchar(200) DEFAULT '' COMMENT 'md5_file加密，可以检测上传/下载的文件包是否损坏',
  `is_remote` tinyint(1) DEFAULT '0' COMMENT '是否远程',
  `downcount` int(10) DEFAULT '0' COMMENT '下载次数',
  `sort_order` smallint(5) DEFAULT '0' COMMENT '排序',
  `add_time` int(10) unsigned DEFAULT '0' COMMENT '上传时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`file_id`),
  KEY `arcid` (`aid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=78 DEFAULT CHARSET=utf8 COMMENT='下载附件表';

-- -----------------------------
-- Records of `ey_download_file`
-- -----------------------------
INSERT INTO `ey_download_file` VALUES ('77', '30', '工程机械推土挖掘机类网站模板', '/abc/uploads/soft/20190114/4b0f01441b5a246badf158fa99c140ac.zip', '', '9268', 'zip', '4b0f01441b5a246badf158fa99c140ac.zip', '', 'application/x-zip-compressed', '1837c4067aa99f7005e62b20bdb1a67f', '1837c4067aa99f7005e62b20bdb1a67f', '0', '0', '1', '1610615767', '0');
INSERT INTO `ey_download_file` VALUES ('76', '31', ' dom编程-window对象', '/abc/uploads/soft/20190114/44bbd259222c81bd3c41112a73c904a0.zip', '', '9268', 'zip', '44bbd259222c81bd3c41112a73c904a0.zip', '', 'application/x-zip-compressed', '1837c4067aa99f7005e62b20bdb1a67f', '1837c4067aa99f7005e62b20bdb1a67f', '0', '0', '2', '1610615761', '0');
INSERT INTO `ey_download_file` VALUES ('75', '31', ' dom编程-window对象', '/abc/uploads/soft/20190114/3b3f753af0f13e6e0237b9577e0bcd17.zip', '', '9268', 'zip', '3b3f753af0f13e6e0237b9577e0bcd17.zip', '', 'application/x-zip-compressed', '1837c4067aa99f7005e62b20bdb1a67f', '1837c4067aa99f7005e62b20bdb1a67f', '0', '0', '1', '1610615761', '0');
INSERT INTO `ey_download_file` VALUES ('74', '91', '计算机软件系统故障及维护', '/abc/uploads/soft/20190731/下载资料测试3.txt', '', '8', '.txt', '下载资料测试3.txt', '', 'text/plain', '24f90e7da3fdc4ed35ca6699b89ba59d', '24f90e7da3fdc4ed35ca6699b89ba59d', '0', '0', '3', '1610615652', '0');
INSERT INTO `ey_download_file` VALUES ('73', '91', '计算机软件系统故障及维护', '/abc/uploads/soft/20190731/下载资料测试 2.txt', '', '8', '.txt', '下载资料测试 2.txt', '', 'text/plain', '24f90e7da3fdc4ed35ca6699b89ba59d', '24f90e7da3fdc4ed35ca6699b89ba59d', '0', '0', '2', '1610615652', '0');
INSERT INTO `ey_download_file` VALUES ('72', '91', '计算机软件系统故障及维护', '/abc/uploads/soft/20190731/下载资料测试.txt', '', '8', '.txt', '下载资料测试.txt', '', 'text/plain', '24f90e7da3fdc4ed35ca6699b89ba59d', '24f90e7da3fdc4ed35ca6699b89ba59d', '0', '0', '1', '1610615652', '0');
INSERT INTO `ey_download_file` VALUES ('71', '92', '计算机软件系统故障及维护2', '/abc/uploads/soft/20190731/下载资料测试3.txt', '', '8', '.txt', '下载资料测试3.txt', '', 'text/plain', '24f90e7da3fdc4ed35ca6699b89ba59d', '24f90e7da3fdc4ed35ca6699b89ba59d', '0', '0', '3', '1610615642', '0');
INSERT INTO `ey_download_file` VALUES ('70', '92', '计算机软件系统故障及维护2', '/abc/uploads/soft/20190731/下载资料测试.txt', '', '8', '.txt', '下载资料测试.txt', '', 'text/plain', '24f90e7da3fdc4ed35ca6699b89ba59d', '24f90e7da3fdc4ed35ca6699b89ba59d', '0', '0', '2', '1610615642', '0');
INSERT INTO `ey_download_file` VALUES ('69', '92', '计算机软件系统故障及维护2', '/abc/uploads/soft/20190731/下载资料测试 2.txt', '', '8', '.txt', '下载资料测试 2.txt', '', 'text/plain', '24f90e7da3fdc4ed35ca6699b89ba59d', '24f90e7da3fdc4ed35ca6699b89ba59d', '0', '0', '1', '1610615642', '0');

-- -----------------------------
-- Table structure for `ey_download_log`
-- -----------------------------
DROP TABLE IF EXISTS `ey_download_log`;
CREATE TABLE `ey_download_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `users_id` int(10) DEFAULT '0' COMMENT '用户ID',
  `aid` int(10) DEFAULT '0' COMMENT '文档ID',
  `file_id` int(10) DEFAULT '0' COMMENT '附件ID',
  `ip` varchar(20) DEFAULT '' COMMENT 'ip',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '编辑时间',
  PRIMARY KEY (`log_id`),
  KEY `file_id` (`file_id`,`aid`,`users_id`) USING BTREE,
  KEY `aid` (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='下载记录表';


-- -----------------------------
-- Table structure for `ey_field_type`
-- -----------------------------
DROP TABLE IF EXISTS `ey_field_type`;
CREATE TABLE `ey_field_type` (
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '字段类型',
  `title` varchar(64) NOT NULL DEFAULT '' COMMENT '中文类型名',
  `ifoption` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否需要设置选项',
  `sort_order` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`name`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='字段类型表';

-- -----------------------------
-- Records of `ey_field_type`
-- -----------------------------
INSERT INTO `ey_field_type` VALUES ('text', '单行文本', '0', '1', '1532485708', '1532485708');
INSERT INTO `ey_field_type` VALUES ('checkbox', '多选项', '1', '5', '1532485708', '1532485708');
INSERT INTO `ey_field_type` VALUES ('multitext', '多行文本', '0', '2', '1532485708', '1532485708');
INSERT INTO `ey_field_type` VALUES ('radio', '单选项', '1', '4', '1532485708', '1532485708');
INSERT INTO `ey_field_type` VALUES ('switch', '开关', '0', '13', '1532485708', '1532485708');
INSERT INTO `ey_field_type` VALUES ('select', '下拉框', '1', '6', '1532485708', '1532485708');
INSERT INTO `ey_field_type` VALUES ('img', '单张图', '0', '10', '1532485708', '1532485708');
INSERT INTO `ey_field_type` VALUES ('int', '整数类型', '0', '7', '1532485708', '1532485708');
INSERT INTO `ey_field_type` VALUES ('datetime', '日期和时间', '0', '12', '1532485708', '1532485708');
INSERT INTO `ey_field_type` VALUES ('htmltext', 'HTML文本', '0', '3', '1532485708', '1532485708');
INSERT INTO `ey_field_type` VALUES ('imgs', '多张图', '0', '11', '1532485708', '1532485708');
INSERT INTO `ey_field_type` VALUES ('decimal', '金额类型', '0', '9', '1532485708', '1532485708');
INSERT INTO `ey_field_type` VALUES ('float', '小数类型', '0', '8', '1532485708', '1532485708');
INSERT INTO `ey_field_type` VALUES ('region', '区域类型', '1', '6', '1532485708', '1532485708');
INSERT INTO `ey_field_type` VALUES ('file', '附件类型', '0', '11', '1532485708', '1532485708');
INSERT INTO `ey_field_type` VALUES ('media', '多媒体类型', '0', '11', '1532485708', '1532485708');

-- -----------------------------
-- Table structure for `ey_guestbook`
-- -----------------------------
DROP TABLE IF EXISTS `ey_guestbook`;
CREATE TABLE `ey_guestbook` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `typeid` int(11) DEFAULT '0' COMMENT '栏目ID',
  `channel` smallint(5) DEFAULT '0' COMMENT '模型ID',
  `md5data` varchar(50) DEFAULT '' COMMENT '数据序列化之后的MD5加密，提交内容的唯一性',
  `ip` varchar(255) DEFAULT '' COMMENT 'ip地址',
  `is_read` tinyint(1) DEFAULT '0' COMMENT '0=未读，1=已读',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='留言主表';

-- -----------------------------
-- Records of `ey_guestbook`
-- -----------------------------
INSERT INTO `ey_guestbook` VALUES ('10', '30', '8', '847bebad4a52e53b4114355f07410c9e', '36.101.193.241', '0', 'cn', '1610349820', '1610349820');
INSERT INTO `ey_guestbook` VALUES ('9', '6', '8', 'dfb8ea4389a9d9a7442c7efa2968f6bf', '36.101.193.241', '0', 'cn', '1610347671', '1610347671');

-- -----------------------------
-- Table structure for `ey_guestbook_attr`
-- -----------------------------
DROP TABLE IF EXISTS `ey_guestbook_attr`;
CREATE TABLE `ey_guestbook_attr` (
  `guest_attr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '留言表单id自增',
  `aid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '留言id',
  `attr_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '表单id',
  `attr_value` text COMMENT '表单值',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`guest_attr_id`),
  KEY `attr_id` (`attr_id`) USING BTREE,
  KEY `guest_id` (`aid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COMMENT='留言表单属性值';

-- -----------------------------
-- Records of `ey_guestbook_attr`
-- -----------------------------
INSERT INTO `ey_guestbook_attr` VALUES ('52', '10', '24', '测试', 'cn', '1610349820', '1610349820');
INSERT INTO `ey_guestbook_attr` VALUES ('51', '10', '3', '功能建议', 'cn', '1610349820', '1610349820');
INSERT INTO `ey_guestbook_attr` VALUES ('50', '10', '2', '13800000000', 'cn', '1610349820', '1610349820');
INSERT INTO `ey_guestbook_attr` VALUES ('49', '10', '1', '张生', 'cn', '1610349820', '1610349820');
INSERT INTO `ey_guestbook_attr` VALUES ('44', '9', '4', '网络安全专员', 'cn', '1610347671', '1610347671');
INSERT INTO `ey_guestbook_attr` VALUES ('45', '9', '5', 'zhan', 'cn', '1610347671', '1610347671');
INSERT INTO `ey_guestbook_attr` VALUES ('46', '9', '6', '男', 'cn', '1610347671', '1610347671');
INSERT INTO `ey_guestbook_attr` VALUES ('47', '9', '16', '13800000000', 'cn', '1610347671', '1610347671');
INSERT INTO `ey_guestbook_attr` VALUES ('48', '9', '20', '测试', 'cn', '1610347671', '1610347671');

-- -----------------------------
-- Table structure for `ey_guestbook_attribute`
-- -----------------------------
DROP TABLE IF EXISTS `ey_guestbook_attribute`;
CREATE TABLE `ey_guestbook_attribute` (
  `attr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '表单id',
  `attr_name` varchar(60) DEFAULT '' COMMENT '表单名称',
  `typeid` int(11) unsigned DEFAULT '0' COMMENT '栏目ID',
  `attr_input_type` tinyint(1) unsigned DEFAULT '0' COMMENT ' 0=文本框，1=下拉框，2=多行文本框',
  `attr_values` text COMMENT '可选值列表',
  `is_showlist` tinyint(1) DEFAULT '0' COMMENT '在列表显示 0=隐藏，1=显示',
  `required` tinyint(1) DEFAULT '0' COMMENT '必填 0=否，1=是',
  `validate_type` smallint(5) DEFAULT '0' COMMENT '验证格式，0=不验证，1=手机，2=Email',
  `sort_order` int(11) unsigned DEFAULT '0' COMMENT '表单排序',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否已删除，0=否，1=是',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`attr_id`),
  KEY `guest_id` (`typeid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='留言表单属性';

-- -----------------------------
-- Records of `ey_guestbook_attribute`
-- -----------------------------
INSERT INTO `ey_guestbook_attribute` VALUES ('1', '姓名', '30', '0', '', '1', '1', '0', '100', 'cn', '0', '1526616441', '1580985056');
INSERT INTO `ey_guestbook_attribute` VALUES ('2', '手机号码', '30', '6', '', '1', '1', '6', '100', 'cn', '0', '1526616453', '1580985048');
INSERT INTO `ey_guestbook_attribute` VALUES ('3', '需求选择', '30', '1', '意见反馈\r\n功能建议', '1', '0', '0', '100', 'cn', '0', '1526616497', '1580985296');
INSERT INTO `ey_guestbook_attribute` VALUES ('4', '申请职位', '6', '0', '', '1', '1', '0', '100', 'cn', '0', '1526634369', '1580985319');
INSERT INTO `ey_guestbook_attribute` VALUES ('5', '姓名', '6', '0', '', '1', '1', '0', '100', 'cn', '0', '1526634383', '1580985015');
INSERT INTO `ey_guestbook_attribute` VALUES ('6', '性别', '6', '1', '男\r\n女', '1', '0', '0', '100', 'cn', '0', '1526634393', '1576764161');
INSERT INTO `ey_guestbook_attribute` VALUES ('16', '手机', '6', '6', '', '1', '1', '6', '100', 'cn', '0', '1563533188', '1580985030');
INSERT INTO `ey_guestbook_attribute` VALUES ('20', '工作经验', '6', '2', '', '0', '0', '0', '100', 'cn', '0', '1563533261', '1563533261');
INSERT INTO `ey_guestbook_attribute` VALUES ('24', '留言内容', '30', '2', '', '0', '0', '0', '100', 'cn', '0', '1609930411', '1609930411');

-- -----------------------------
-- Table structure for `ey_hooks`
-- -----------------------------
DROP TABLE IF EXISTS `ey_hooks`;
CREATE TABLE `ey_hooks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '钩子名称',
  `description` text COMMENT '描述',
  `module` varchar(50) DEFAULT '' COMMENT '钩子挂载的插件',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '状态：0=无效，1=有效',
  `add_time` int(10) DEFAULT NULL,
  `update_time` int(10) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='插件钩子表';


-- -----------------------------
-- Table structure for `ey_images_content`
-- -----------------------------
DROP TABLE IF EXISTS `ey_images_content`;
CREATE TABLE `ey_images_content` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aid` int(10) DEFAULT '0' COMMENT '文档ID',
  `content` longtext COMMENT '内容详情',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `news_id` (`aid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='图集附加表';

-- -----------------------------
-- Records of `ey_images_content`
-- -----------------------------
INSERT INTO `ey_images_content` VALUES ('7', '42', '&lt;p&gt;我们将用高端的平面设计与网络技术，为您打造真正属于您的网站。我们用专业的团队解决您网站内容更新，设计更新的难题，您只要提供原始素材，我们将为您整体包装在网站呈现。同时，通过媒介资源整合，打造国际化语言宣传通道，向世界进行全方位的品牌形象报道传播，实现传播价值的最大化。&lt;/p&gt;', '1565234124', '1565234124');
INSERT INTO `ey_images_content` VALUES ('15', '93', '&lt;p&gt;软件开发是根据用户要求建造出软件系统或者系统中的软件部分的过程。软件开发是一项包括需求捕捉，需求分析，设计，实现和测试的系统工程。软件一般是用某种程序设计语言来实现的。通常采用软件开发工具可以进行开发。软件分为系统软件和应用软件。&lt;/p&gt;&lt;p&gt;&amp;nbsp;软件并不只是包括可以在计算机上运行的程序，与这些程序相关的文件一般也被认为是软件的一部分。 软件设计思路和方法的一般过程，包括设计软件的功能和实现的算法和方法、软件的总体结构设计和模块设计、编程和调试、程序联调和测试以及编写、提交程序。&lt;/p&gt;', '1565232857', '1565232857');
INSERT INTO `ey_images_content` VALUES ('8', '43', '&lt;p&gt;&lt;span style=&quot;color: rgb(42, 51, 60); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; background-color: rgb(255, 255, 255);&quot;&gt;软件开发是根据用户要求建造出软件系统或者系统中的软件部分的过程。软件开发是一项包括需求捕捉，需求分析，设计，实现和测试的系统工程。软件一般是用某种程序设计语言来实现的。通常采用软件开发工具可以进行开发。软件分为系统软件和应用软件。&amp;nbsp;&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;color: rgb(42, 51, 60); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; background-color: rgb(255, 255, 255);&quot;&gt;软件并不只是包括可以在计算机上运行的程序，与这些程序相关的文件一般也被认为是软件的一部分。 软件设计思路和方法的一般过程，包括设计软件的功能和实现的算法和方法、软件的总体结构设计和模块设计、编程和调试、程序联调和测试以及编写、提交程序。&lt;/span&gt;&lt;/p&gt;', '1610615719', '1610615719');
INSERT INTO `ey_images_content` VALUES ('9', '44', '&lt;p&gt;SEO（搜索引擎优化）和有效的网站设计是齐头并进的。好的网站设计是关于创建一个吸引目标受众的网站，并让他们采取某种行动。但是，如果该网站不遵循目前的 SEO 最佳做法，它的排名将会受到影响，从而会导致真正参与该网站的访问者的数量的较少。&lt;/p&gt;&lt;p&gt;相反地，如果将关注的焦点放在搜索引擎优化以及如何取悦搜索引擎蜘蛛上，那么网站可能会排名很高，并且会获得大量的搜索引擎流量，但是如果设计很不尽人意，那就不一样了。为了在当今的数字环境中取得成功，必须将重点放在网站设计和搜索引擎优化上。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1610615714', '1610615714');
INSERT INTO `ey_images_content` VALUES ('16', '104', '&lt;p&gt;我们将用高端的平面设计与网络技术，为您打造真正属于您的网站。我们用专业的团队解决您网站内容更新，设计更新的难题，您只要提供原始素材，我们将为您整体包装在网站呈现。同时，通过媒介资源整合，打造国际化语言宣传通道，向世界进行全方位的品牌形象报道传播，实现传播价值的最大化。&lt;/p&gt;', '1610615576', '1610615576');
INSERT INTO `ey_images_content` VALUES ('17', '105', '&lt;p&gt;&lt;span style=&quot;color: rgb(42, 51, 60); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; background-color: rgb(255, 255, 255);&quot;&gt;软件开发是根据用户要求建造出软件系统或者系统中的软件部分的过程。软件开发是一项包括需求捕捉，需求分析，设计，实现和测试的系统工程。软件一般是用某种程序设计语言来实现的。通常采用软件开发工具可以进行开发。软件分为系统软件和应用软件。&amp;nbsp;&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;color: rgb(42, 51, 60); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; background-color: rgb(255, 255, 255);&quot;&gt;软件并不只是包括可以在计算机上运行的程序，与这些程序相关的文件一般也被认为是软件的一部分。 软件设计思路和方法的一般过程，包括设计软件的功能和实现的算法和方法、软件的总体结构设计和模块设计、编程和调试、程序联调和测试以及编写、提交程序。&lt;/span&gt;&lt;/p&gt;', '1610615570', '1610615570');

-- -----------------------------
-- Table structure for `ey_images_upload`
-- -----------------------------
DROP TABLE IF EXISTS `ey_images_upload`;
CREATE TABLE `ey_images_upload` (
  `img_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `aid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '图集ID',
  `title` varchar(200) DEFAULT '' COMMENT '产品标题',
  `image_url` varchar(255) DEFAULT '' COMMENT '文件存储路径',
  `intro` varchar(2000) DEFAULT '' COMMENT '图集描述',
  `width` int(11) DEFAULT '0' COMMENT '图片宽度',
  `height` int(11) DEFAULT '0' COMMENT '图片高度',
  `filesize` mediumint(8) unsigned DEFAULT '0' COMMENT '文件大小',
  `mime` varchar(50) DEFAULT '' COMMENT '图片类型',
  `sort_order` smallint(5) DEFAULT '0' COMMENT '排序',
  `add_time` int(10) unsigned DEFAULT '0' COMMENT '上传时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`img_id`),
  KEY `arcid` (`aid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=89 DEFAULT CHARSET=utf8 COMMENT='图集图片表';

-- -----------------------------
-- Records of `ey_images_upload`
-- -----------------------------
INSERT INTO `ey_images_upload` VALUES ('88', '43', '3C数码蓝牙耳机产品渲染', '/abc/uploads/allimg/20190808/1c3dabff0cbf24fb6667899396a866aa.jpg', '', '0', '0', '0', '', '2', '1610615719', '0');
INSERT INTO `ey_images_upload` VALUES ('86', '44', '喷油耳机 建模渲染', '/abc/uploads/allimg/20190808/45b6f3f95d30a97cfa4a83d315b5c4f1.jpg', '', '0', '0', '0', '', '2', '1610615714', '0');
INSERT INTO `ey_images_upload` VALUES ('85', '44', '喷油耳机 建模渲染', '/abc/uploads/allimg/20190808/8e87c585976aa71baf5348e28611196b.jpg', '', '0', '0', '0', '', '1', '1610615714', '0');
INSERT INTO `ey_images_upload` VALUES ('87', '43', '3C数码蓝牙耳机产品渲染', '/abc/uploads/allimg/20190808/f6b182e9952b2afab33faa0d07ef373a.jpg', '', '0', '0', '0', '', '1', '1610615719', '0');
INSERT INTO `ey_images_upload` VALUES ('68', '42', 'VIVO X27 手机摄影', '/uploads/allimg/20190808/6e2ed9b79d6d8beb998a4838ec3dde0c.jpg', '', '680', '520', '31841', 'image/jpeg', '2', '1565234124', '0');
INSERT INTO `ey_images_upload` VALUES ('67', '42', 'VIVO X27 手机摄影', '/uploads/allimg/20190808/17268e40477444ecbf11bcb643f321c2.jpg', '', '680', '520', '81344', 'image/jpeg', '1', '1565234124', '0');
INSERT INTO `ey_images_upload` VALUES ('59', '93', '鼠标封面设计', '/uploads/allimg/20190808/0951948f9135e85b78f51d69611c6ac4.jpg', '', '680', '520', '44630', 'image/jpeg', '3', '1565232857', '0');
INSERT INTO `ey_images_upload` VALUES ('58', '93', '鼠标封面设计', '/uploads/allimg/20190808/abd694dbe8683fce0fa639b80f7726bf.jpg', '', '680', '520', '23269', 'image/jpeg', '2', '1565232857', '0');
INSERT INTO `ey_images_upload` VALUES ('57', '93', '鼠标封面设计', '/uploads/allimg/20190808/ecc9bb4583dc47abef98663c7a4cad8d.jpg', '', '680', '520', '37442', 'image/jpeg', '1', '1565232857', '0');
INSERT INTO `ey_images_upload` VALUES ('60', '93', '鼠标封面设计', '/uploads/allimg/20190808/8edd1cd82df2afd55836b1a095e4395d.jpg', '', '680', '520', '39116', 'image/jpeg', '4', '1565232857', '0');
INSERT INTO `ey_images_upload` VALUES ('84', '104', '手机摄影', '/abc/uploads/allimg/20190808/6e2ed9b79d6d8beb998a4838ec3dde0c.jpg', '', '0', '0', '0', '', '2', '1610615576', '0');
INSERT INTO `ey_images_upload` VALUES ('83', '104', '手机摄影', '/abc/uploads/allimg/20190808/17268e40477444ecbf11bcb643f321c2.jpg', '', '0', '0', '0', '', '1', '1610615576', '0');
INSERT INTO `ey_images_upload` VALUES ('82', '105', '数码蓝牙耳机产品渲染', '/abc/uploads/allimg/20190808/1c3dabff0cbf24fb6667899396a866aa.jpg', '', '0', '0', '0', '', '2', '1610615570', '0');
INSERT INTO `ey_images_upload` VALUES ('81', '105', '数码蓝牙耳机产品渲染', '/abc/uploads/allimg/20190808/f6b182e9952b2afab33faa0d07ef373a.jpg', '', '0', '0', '0', '', '1', '1610615570', '0');

-- -----------------------------
-- Table structure for `ey_language`
-- -----------------------------
DROP TABLE IF EXISTS `ey_language`;
CREATE TABLE `ey_language` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '信息ID，自增',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '语言名称',
  `mark` varchar(50) NOT NULL DEFAULT '' COMMENT '语言标识（唯一）',
  `url` varchar(200) NOT NULL DEFAULT '' COMMENT '单独域名(外部链接)',
  `target` tinyint(1) NOT NULL DEFAULT '0' COMMENT '新窗口打开，0=否，1=是',
  `is_home_default` tinyint(1) DEFAULT '0' COMMENT '默认前台语言，1=是，0=否',
  `is_admin_default` tinyint(1) DEFAULT '0' COMMENT '默认后台语言，1=是，0=否',
  `syn_pack_id` int(10) DEFAULT '0' COMMENT '最后一次同步官方语言包ID',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '语言状态，0=关闭，1=开启',
  `sort_order` int(10) DEFAULT '0' COMMENT '排序号',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='多语言主表';

-- -----------------------------
-- Records of `ey_language`
-- -----------------------------
INSERT INTO `ey_language` VALUES ('1', '简体中文', 'cn', '', '0', '1', '1', '24', '1', '100', '1541583096', '1543890743');

-- -----------------------------
-- Table structure for `ey_language_attr`
-- -----------------------------
DROP TABLE IF EXISTS `ey_language_attr`;
CREATE TABLE `ey_language_attr` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '信息ID，自增',
  `attr_name` varchar(200) NOT NULL DEFAULT '' COMMENT '来自ey_weapp_language_attr表的attr_name',
  `attr_value` text NOT NULL COMMENT '变量值',
  `attr_group` varchar(50) DEFAULT '' COMMENT '分组，以表名划分（不含表前缀）',
  `lang` varchar(50) NOT NULL DEFAULT '' COMMENT '所属语言',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `attr_value` (`attr_name`,`lang`)
) ENGINE=MyISAM AUTO_INCREMENT=131 DEFAULT CHARSET=utf8 COMMENT='多语言模板变量关联绑定表';

-- -----------------------------
-- Records of `ey_language_attr`
-- -----------------------------
INSERT INTO `ey_language_attr` VALUES ('1', 'tid1', '1', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('2', 'tid2', '2', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('3', 'tid3', '3', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('4', 'tid4', '4', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('5', 'tid5', '5', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('6', 'tid6', '6', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('7', 'tid8', '8', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('8', 'tid9', '9', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('9', 'tid10', '10', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('10', 'tid11', '11', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('11', 'tid12', '12', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('12', 'tid13', '13', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('13', 'tid20', '20', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('14', 'tid21', '21', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('15', 'tid22', '22', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('16', 'tid23', '23', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('17', 'tid24', '24', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('18', 'tid25', '25', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('19', 'tid26', '26', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('20', 'tid27', '27', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('21', 'tid28', '28', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('22', 'tid29', '29', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('23', 'tid30', '30', 'arctype', 'cn', '1545267517', '1545267517');
INSERT INTO `ey_language_attr` VALUES ('24', 'attr_1', '1', 'guestbook_attribute', 'cn', '1545267518', '1545267518');
INSERT INTO `ey_language_attr` VALUES ('25', 'attr_2', '2', 'guestbook_attribute', 'cn', '1545267518', '1545267518');
INSERT INTO `ey_language_attr` VALUES ('26', 'attr_3', '3', 'guestbook_attribute', 'cn', '1545267518', '1545267518');
INSERT INTO `ey_language_attr` VALUES ('27', 'attr_4', '4', 'guestbook_attribute', 'cn', '1545267518', '1545267518');
INSERT INTO `ey_language_attr` VALUES ('28', 'attr_5', '5', 'guestbook_attribute', 'cn', '1545267518', '1545267518');
INSERT INTO `ey_language_attr` VALUES ('29', 'attr_6', '6', 'guestbook_attribute', 'cn', '1545267518', '1545267518');
INSERT INTO `ey_language_attr` VALUES ('30', 'attr_7', '7', 'guestbook_attribute', 'cn', '1545267518', '1545267518');
INSERT INTO `ey_language_attr` VALUES ('31', 'attr_1', '1', 'product_attribute', 'cn', '1545267518', '1545267518');
INSERT INTO `ey_language_attr` VALUES ('32', 'attr_2', '2', 'product_attribute', 'cn', '1545267518', '1545267518');
INSERT INTO `ey_language_attr` VALUES ('33', 'attr_3', '3', 'product_attribute', 'cn', '1545267518', '1545267518');
INSERT INTO `ey_language_attr` VALUES ('34', 'attr_4', '4', 'product_attribute', 'cn', '1545267518', '1545267518');
INSERT INTO `ey_language_attr` VALUES ('35', 'attr_5', '5', 'product_attribute', 'cn', '1545267518', '1545267518');
INSERT INTO `ey_language_attr` VALUES ('36', 'attr_6', '6', 'product_attribute', 'cn', '1545267518', '1545267518');
INSERT INTO `ey_language_attr` VALUES ('37', 'attr_7', '7', 'product_attribute', 'cn', '1545267518', '1545267518');
INSERT INTO `ey_language_attr` VALUES ('38', 'attr_8', '8', 'product_attribute', 'cn', '1545267518', '1545267518');
INSERT INTO `ey_language_attr` VALUES ('39', 'ad1', '1', 'ad', 'cn', '1545267518', '1545267518');
INSERT INTO `ey_language_attr` VALUES ('40', 'ad2', '2', 'ad', 'cn', '1545267518', '1545267518');
INSERT INTO `ey_language_attr` VALUES ('41', 'adp1', '1', 'ad_position', 'cn', '1545267518', '1545267518');
INSERT INTO `ey_language_attr` VALUES ('83', 'ad5', '5', 'ad', 'cn', '1553046945', '1553046945');
INSERT INTO `ey_language_attr` VALUES ('85', 'attr_16', '16', 'guestbook_attribute', 'cn', '1563533188', '1563533188');
INSERT INTO `ey_language_attr` VALUES ('87', 'attr_18', '18', 'guestbook_attribute', 'cn', '1563533246', '1563533246');
INSERT INTO `ey_language_attr` VALUES ('89', 'attr_20', '20', 'guestbook_attribute', 'cn', '1563533261', '1563533261');
INSERT INTO `ey_language_attr` VALUES ('91', 'attr_22', '22', 'guestbook_attribute', 'cn', '1563533269', '1563533269');
INSERT INTO `ey_language_attr` VALUES ('93', 'tid54', '54', 'arctype', 'cn', '1563761937', '1563761937');
INSERT INTO `ey_language_attr` VALUES ('95', 'adp3', '3', 'ad_position', 'cn', '1563764323', '1563764323');
INSERT INTO `ey_language_attr` VALUES ('97', 'ad7', '7', 'ad', 'cn', '1563764323', '1563764323');
INSERT INTO `ey_language_attr` VALUES ('99', 'ad9', '9', 'ad', 'cn', '1563764323', '1563764323');
INSERT INTO `ey_language_attr` VALUES ('101', 'ad11', '11', 'ad', 'cn', '1563764411', '1563764411');
INSERT INTO `ey_language_attr` VALUES ('103', 'attr_19', '19', 'product_attribute', 'cn', '1564539436', '1564539436');
INSERT INTO `ey_language_attr` VALUES ('105', 'attr_21', '21', 'product_attribute', 'cn', '1564539503', '1564539503');
INSERT INTO `ey_language_attr` VALUES ('107', 'attr_23', '23', 'product_attribute', 'cn', '1564539517', '1564539517');
INSERT INTO `ey_language_attr` VALUES ('109', 'attr_25', '25', 'product_attribute', 'cn', '1564539530', '1564539530');
INSERT INTO `ey_language_attr` VALUES ('111', 'attr_27', '27', 'product_attribute', 'cn', '1564539541', '1564539541');
INSERT INTO `ey_language_attr` VALUES ('113', 'tid56', '56', 'arctype', 'cn', '1564625026', '1564625026');
INSERT INTO `ey_language_attr` VALUES ('115', 'tid58', '58', 'arctype', 'cn', '1564632567', '1564632567');
INSERT INTO `ey_language_attr` VALUES ('117', 'tid60', '60', 'arctype', 'cn', '1564632676', '1564632676');
INSERT INTO `ey_language_attr` VALUES ('119', 'tid62', '62', 'arctype', 'cn', '1564632717', '1564632717');
INSERT INTO `ey_language_attr` VALUES ('121', 'tid64', '64', 'arctype', 'cn', '1565083870', '1565083870');
INSERT INTO `ey_language_attr` VALUES ('123', 'tid66', '66', 'arctype', 'cn', '1565083875', '1565083875');
INSERT INTO `ey_language_attr` VALUES ('127', 'ad13', '13', 'ad', 'cn', '1565225126', '1565225126');
INSERT INTO `ey_language_attr` VALUES ('129', 'attr_24', '24', 'guestbook_attribute', 'cn', '1609930411', '1609930411');

-- -----------------------------
-- Table structure for `ey_language_attribute`
-- -----------------------------
DROP TABLE IF EXISTS `ey_language_attribute`;
CREATE TABLE `ey_language_attribute` (
  `attr_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '信息ID，自增',
  `attr_title` varchar(200) NOT NULL DEFAULT '' COMMENT '变量标题',
  `attr_name` varchar(200) NOT NULL DEFAULT '' COMMENT '变量名称',
  `attr_group` varchar(50) DEFAULT '' COMMENT '分组，以表名划分（不含表前缀）',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '伪删除，0=否，1=是',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`attr_id`)
) ENGINE=MyISAM AUTO_INCREMENT=66 DEFAULT CHARSET=utf8 COMMENT='多语言模板变量表';

-- -----------------------------
-- Records of `ey_language_attribute`
-- -----------------------------
INSERT INTO `ey_language_attribute` VALUES ('1', '关于我们', 'tid1', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('2', '新闻动态', 'tid2', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('3', '产品展示', 'tid3', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('4', '解决方案', 'tid4', 'arctype', '0', '1545267517', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('5', '资料下载', 'tid5', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('6', '在线应聘', 'tid6', 'arctype', '0', '1545267517', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('7', '公司简介', 'tid8', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('8', '公司荣誉', 'tid9', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('9', '公司动态', 'tid10', 'arctype', '0', '1545267517', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('10', '行业资讯', 'tid11', 'arctype', '0', '1545267517', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('11', '媒体报道', 'tid12', 'arctype', '0', '1545267517', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('12', '单页面', 'tid13', 'arctype', '1', '1545267517', '1565084026');
INSERT INTO `ey_language_attribute` VALUES ('13', '手机数码', 'tid20', 'arctype', '0', '1545267517', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('14', '电脑产品', 'tid21', 'arctype', '0', '1545267517', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('15', '周边配件', 'tid22', 'arctype', '0', '1545267517', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('16', '人才招聘', 'tid23', 'arctype', '0', '1545267517', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('17', '智能手机', 'tid24', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('18', '畅玩手机', 'tid25', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('19', '笔记本电脑', 'tid26', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('20', '耳机', 'tid27', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('21', '音箱', 'tid28', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('22', '充电宝', 'tid29', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('23', '联系我们', 'tid30', 'arctype', '0', '1545267517', '1610357904');
INSERT INTO `ey_language_attribute` VALUES ('24', '姓名', 'attr_1', 'guestbook_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('25', '手机号码', 'attr_2', 'guestbook_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('26', '需求选择', 'attr_3', 'guestbook_attribute', '0', '1545267518', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('27', '申请职位', 'attr_4', 'guestbook_attribute', '0', '1545267518', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('28', '姓名', 'attr_5', 'guestbook_attribute', '0', '1545267518', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('29', '性别', 'attr_6', 'guestbook_attribute', '0', '1545267518', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('30', '模式', 'attr_7', 'guestbook_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('31', '用户界面', 'attr_1', 'product_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('32', '操作系统', 'attr_2', 'product_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('33', '键盘类型', 'attr_3', 'product_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('34', ' 产品型号', 'attr_4', 'product_attribute', '0', '1545267518', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('35', '屏幕大小', 'attr_5', 'product_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('36', '整机净重', 'attr_6', 'product_attribute', '0', '1545267518', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('37', '产品型号', 'attr_7', 'product_attribute', '0', '1545267518', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('38', '支持蓝牙', 'attr_8', 'product_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('39', '共展蓝图', 'ad1', 'ad', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('40', '易优模板库', 'ad2', 'ad', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('41', '首页-大幻灯片', 'adp1', 'ad_position', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('42', '第三组广告', 'ad5', 'ad', '0', '1553046945', '1553046945');
INSERT INTO `ey_language_attribute` VALUES ('43', '手机', 'attr_16', 'guestbook_attribute', '0', '1563533188', '1563533188');
INSERT INTO `ey_language_attribute` VALUES ('44', '学历', 'attr_18', 'guestbook_attribute', '0', '1563533246', '1563533246');
INSERT INTO `ey_language_attribute` VALUES ('45', '工作经验', 'attr_20', 'guestbook_attribute', '0', '1563533261', '1563533261');
INSERT INTO `ey_language_attribute` VALUES ('46', '自我评价', 'attr_22', 'guestbook_attribute', '0', '1563533269', '1563533269');
INSERT INTO `ey_language_attribute` VALUES ('47', '联系我们', 'tid54', 'arctype', '1', '1563761937', '1609929617');
INSERT INTO `ey_language_attribute` VALUES ('48', '手机端首页头部幻灯', 'adp3', 'ad_position', '0', '1563764323', '1563764323');
INSERT INTO `ey_language_attribute` VALUES ('49', '', 'ad7', 'ad', '0', '1563764323', '1563764323');
INSERT INTO `ey_language_attribute` VALUES ('50', '', 'ad9', 'ad', '0', '1563764323', '1563764323');
INSERT INTO `ey_language_attribute` VALUES ('51', '', 'ad11', 'ad', '0', '1563764411', '1563764411');
INSERT INTO `ey_language_attribute` VALUES ('52', '商品名称', 'attr_19', 'product_attribute', '0', '1564539436', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('53', '商品毛重', 'attr_21', 'product_attribute', '0', '1564539503', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('54', '商品产地', 'attr_23', 'product_attribute', '0', '1564539517', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('55', '多卡支持', 'attr_25', 'product_attribute', '0', '1564539530', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('56', '机身内存', 'attr_27', 'product_attribute', '0', '1564539541', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('57', '国内媒体', 'tid56', 'arctype', '1', '1564625026', '1565083902');
INSERT INTO `ey_language_attribute` VALUES ('58', '软文选写', 'tid58', 'arctype', '1', '1564632567', '1565083896');
INSERT INTO `ey_language_attribute` VALUES ('59', '国外媒体', 'tid60', 'arctype', '1', '1564632676', '1565083908');
INSERT INTO `ey_language_attribute` VALUES ('60', '岛内媒体', 'tid62', 'arctype', '1', '1564632717', '1565083886');
INSERT INTO `ey_language_attribute` VALUES ('61', '系统方案', 'tid64', 'arctype', '0', '1565083870', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('62', '应用方案', 'tid66', 'arctype', '0', '1565083875', '1574233888');
INSERT INTO `ey_language_attribute` VALUES ('64', '', 'ad13', 'ad', '0', '1565225126', '1565225126');
INSERT INTO `ey_language_attribute` VALUES ('65', '留言内容', 'attr_24', 'guestbook_attribute', '0', '1609930411', '1609930411');

-- -----------------------------
-- Table structure for `ey_language_mark`
-- -----------------------------
DROP TABLE IF EXISTS `ey_language_mark`;
CREATE TABLE `ey_language_mark` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '国家语言名称',
  `cn_title` varchar(50) NOT NULL DEFAULT '' COMMENT '中文名称',
  `mark` varchar(50) DEFAULT '' COMMENT '多语言标识',
  `pinyin` varchar(100) DEFAULT '' COMMENT '拼音',
  `sort_order` int(10) NOT NULL DEFAULT '0' COMMENT '排序号',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=utf8 COMMENT='国家语言表';

-- -----------------------------
-- Records of `ey_language_mark`
-- -----------------------------
INSERT INTO `ey_language_mark` VALUES ('1', '简体中文', '简体中文', 'cn', 'zhongwenjianti', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('2', 'Vietnamese', '越南语', 'vi', 'yuenanyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('3', '繁体中文', '繁体中文', 'zh', 'zhongwenfanti', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('4', 'English', '英语', 'en', 'yingyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('5', 'Indonesian', '印尼语', 'id', 'yinniyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('6', 'Urdu', '乌尔都语', 'ur', 'wuerduyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('7', 'Yiddish', '意第绪语', 'yi', 'yidixuyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('8', 'Italian', '意大利语', 'it', 'yidaliyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('9', 'Greek', '希腊语', 'el', 'xilayu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('10', 'Spanish Basque', '西班牙的巴斯克语', 'eu', 'xibanyadebasikeyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('11', 'Spanish', '西班牙语', 'es', 'xibanyayu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('12', 'Hungarian', '匈牙利语', 'hu', 'xiongyaliyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('13', 'Hebrew', '希伯来语', 'iw', 'xibolaiyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('14', 'Ukrainian', '乌克兰语', 'uk', 'wukelanyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('15', 'Welsh', '威尔士语', 'cy', 'weiershiyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('16', 'Thai', '泰语', 'th', 'taiyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('17', 'Turkish', '土耳其语', 'tr', 'tuerqiyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('18', 'Swahili', '斯瓦希里语', 'sw', 'siwaxiliyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('19', 'Japanese', '日语', 'ja', 'riyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('20', 'Swedish', '瑞典语', 'sv', 'ruidianyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('21', 'Serbian', '塞尔维亚语', 'sr', 'saierweiyayu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('22', 'Slovak', '斯洛伐克语', 'sk', 'siluofakeyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('23', 'Slovenian', '斯洛文尼亚语', 'sl', 'siluowenniyayu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('24', 'Portuguese', '葡萄牙语', 'pt', 'putaoyayu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('25', 'Norwegian', '挪威语', 'no', 'nuoweiyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('26', 'Macedonian', '马其顿语', 'mk', 'maqidunyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('27', 'Malay', '马来语', 'ms', 'malaiyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('28', 'Maltese', '马耳他语', 'mt', 'maertayu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('29', 'Romanian', '罗马尼亚语', 'ro', 'luomaniyayu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('30', 'Lithuanian', '立陶宛语', 'lt', 'litaowanyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('31', 'Latvian', '拉脱维亚语', 'lv', 'latuoweiyayu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('32', 'Latin', '拉丁语', 'la', 'ladingyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('33', 'Croatian', '克罗地亚语', 'hr', 'keluodiyayu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('34', 'Czech', '捷克语', 'cs', 'jiekeyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('35', 'Catalan', '加泰罗尼亚语', 'ca', 'jiatailuoniyayu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('36', 'Galician', '加利西亚语', 'gl', 'jialixiyayu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('37', 'Dutch', '荷兰语', 'nl', 'helanyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('38', 'Korean', '韩语', 'ko', 'hanyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('39', 'Haitian Creole', '海地克里奥尔语', 'ht', 'haidikeliaoeryu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('40', 'Finnish', '芬兰语', 'fi', 'fenlanyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('41', 'Filipino', '菲律宾语', 'tl', 'feilvbinyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('42', 'Russian', '俄语', 'ru', 'eyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('43', 'Boolean (Afrikaans)', '布尔语(南非荷兰语)', 'af', 'bueryunanfeihelanyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('44', 'French', '法语', 'fr', 'fayu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('45', 'Danish', '丹麦语', 'da', 'danmaiyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('46', 'German', '德语', 'de', 'deyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('47', 'Azerbaijani', '阿塞拜疆语', 'az', 'asaibaijiangyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('48', 'Irish', '爱尔兰语', 'ga', 'aierlanyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('49', 'Estonian', '爱沙尼亚语', 'et', 'aishaniyayu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('50', 'Belarusian', '白俄罗斯语', 'be', 'baieluosiyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('51', 'Bulgarian', '保加利亚语', 'bg', 'baojialiyayu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('52', 'Icelandic', '冰岛语', 'is', 'bingdaoyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('53', 'Polish', '波兰语', 'pl', 'bolanyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('54', 'Persian', '波斯语', 'fa', 'bosiyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('55', 'Arabic', '阿拉伯语', 'ar', 'alaboyu', '100', '0', '1541583096');
INSERT INTO `ey_language_mark` VALUES ('56', 'Albanian', '阿尔巴尼亚语', 'sq', 'aerbaniyayu', '100', '0', '1541583096');

-- -----------------------------
-- Table structure for `ey_language_pack`
-- -----------------------------
DROP TABLE IF EXISTS `ey_language_pack`;
CREATE TABLE `ey_language_pack` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '变量名',
  `value` text NOT NULL COMMENT '变量值',
  `is_syn` tinyint(1) DEFAULT '0' COMMENT '同步官方语言包：0=否，1=是',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `sort_order` int(10) DEFAULT '0' COMMENT '排序号',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COMMENT='模板语言包变量';

-- -----------------------------
-- Records of `ey_language_pack`
-- -----------------------------
INSERT INTO `ey_language_pack` VALUES ('1', 'sys1', '首页', '1', 'cn', '100', '1543890216', '1543890216');
INSERT INTO `ey_language_pack` VALUES ('2', 'sys2', '上一页', '1', 'cn', '100', '1543890216', '1543890216');
INSERT INTO `ey_language_pack` VALUES ('3', 'sys3', '下一页', '1', 'cn', '100', '1543890216', '1543890216');
INSERT INTO `ey_language_pack` VALUES ('4', 'sys4', '末页', '1', 'cn', '100', '1543890216', '1543890216');
INSERT INTO `ey_language_pack` VALUES ('5', 'sys5', '共<strong>%s</strong>页 <strong>%s</strong>条', '1', 'cn', '100', '1543890216', '1543890216');
INSERT INTO `ey_language_pack` VALUES ('6', 'sys6', '全部', '1', 'cn', '100', '1543890216', '1543890216');
INSERT INTO `ey_language_pack` VALUES ('7', 'sys7', '搜索', '1', 'cn', '100', '1543890216', '1543890216');
INSERT INTO `ey_language_pack` VALUES ('8', 'sys8', '查看详情', '1', 'cn', '100', '1543890216', '1543890216');
INSERT INTO `ey_language_pack` VALUES ('9', 'sys9', '网站首页', '1', 'cn', '100', '1543890216', '1543890216');
INSERT INTO `ey_language_pack` VALUES ('10', 'sys10', '暂无', '1', 'cn', '100', '1543890216', '1543890216');
INSERT INTO `ey_language_pack` VALUES ('11', 'sys11', '上一篇', '1', 'cn', '100', '1543890216', '1543890216');
INSERT INTO `ey_language_pack` VALUES ('12', 'sys12', '下一篇', '1', 'cn', '100', '1543890216', '1543890216');
INSERT INTO `ey_language_pack` VALUES ('25', 'yybl1', '网站首页', '0', 'cn', '100', '1545272835', '1545272835');
INSERT INTO `ey_language_pack` VALUES ('27', 'yybl2', '全部', '0', 'cn', '100', '1545272897', '1545272897');
INSERT INTO `ey_language_pack` VALUES ('29', 'yybl3', '查看更多', '0', 'cn', '100', '1545272961', '1545272961');
INSERT INTO `ey_language_pack` VALUES ('31', 'yybl4', '联系热线', '0', 'cn', '100', '1545273023', '1545273023');
INSERT INTO `ey_language_pack` VALUES ('33', 'yybl5', '你的位置', '0', 'cn', '100', '1545273158', '1545273158');
INSERT INTO `ey_language_pack` VALUES ('35', 'yybl6', '请输入关键词', '0', 'cn', '100', '1545273239', '1545273239');
INSERT INTO `ey_language_pack` VALUES ('37', 'yybl7', '为您推荐', '0', 'cn', '100', '1545273292', '1545273292');
INSERT INTO `ey_language_pack` VALUES ('39', 'yybl8', '热门推荐', '0', 'cn', '100', '1545273376', '1545273376');
INSERT INTO `ey_language_pack` VALUES ('41', 'yybl9', '详细信息', '0', 'cn', '100', '1545273418', '1545273418');
INSERT INTO `ey_language_pack` VALUES ('43', 'yybl10', '下载包', '0', 'cn', '100', '1545273596', '1545273596');
INSERT INTO `ey_language_pack` VALUES ('45', 'yybl11', '文件附件列表', '0', 'cn', '100', '1545273655', '1545273655');
INSERT INTO `ey_language_pack` VALUES ('47', 'yybl12', '结果', '0', 'cn', '100', '1545274437', '1545274437');
INSERT INTO `ey_language_pack` VALUES ('49', 'yybl13', '没有数据了', '0', 'cn', '100', '1545274472', '1547516837');

-- -----------------------------
-- Table structure for `ey_links`
-- -----------------------------
DROP TABLE IF EXISTS `ey_links`;
CREATE TABLE `ey_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typeid` tinyint(1) DEFAULT '1' COMMENT '类型：1=文字链接，2=图片链接',
  `groupid` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '分组id， 默认分组值为1',
  `title` varchar(50) DEFAULT '' COMMENT '网站标题',
  `url` varchar(100) DEFAULT '' COMMENT '网站地址',
  `logo` varchar(255) DEFAULT '' COMMENT '网站LOGO',
  `sort_order` int(11) DEFAULT '0' COMMENT '排序号',
  `target` tinyint(1) DEFAULT '0' COMMENT '是否开启浏览器新窗口',
  `nofollow` tinyint(1) DEFAULT '0',
  `email` varchar(50) DEFAULT NULL,
  `intro` text COMMENT '网站简况',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态(1=显示，0=屏蔽)',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `delete_time` int(11) DEFAULT '0' COMMENT '软删除时间',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='友情链接表';

-- -----------------------------
-- Records of `ey_links`
-- -----------------------------
INSERT INTO `ey_links` VALUES ('1', '1', '1', '百度', 'http://www.baidu.com', '/abf/uploads/allimg/20210111/1-210111145019107.gif', '1', '1', '0', '', '', '1', 'cn', '0', '1524975826', '1610702995');
INSERT INTO `ey_links` VALUES ('2', '1', '1', '腾讯', 'http://www.qq.com', '/abf/uploads/allimg/20210111/1-2101111450594J.gif', '3', '1', '0', '', '', '1', 'cn', '0', '1524976095', '1610702997');
INSERT INTO `ey_links` VALUES ('3', '1', '1', '新浪', 'http://www.sina.com.cn', '', '5', '1', '0', '', '', '1', 'cn', '0', '1532414285', '1610702999');
INSERT INTO `ey_links` VALUES ('4', '1', '1', '淘宝', 'http://www.taobao.com', '', '7', '1', '0', '', '', '1', 'cn', '0', '1532414529', '1610703002');
INSERT INTO `ey_links` VALUES ('5', '1', '1', '微博', 'http://www.weibo.com', '', '100', '1', '0', '', '', '1', 'cn', '0', '1532414726', '1610334638');
INSERT INTO `ey_links` VALUES ('11', '1', '1', 'eyoucms', 'https://www.eyoucms.com', '', '2', '1', '0', '', '', '1', 'cn', '0', '1610702923', '1610702996');
INSERT INTO `ey_links` VALUES ('12', '1', '1', '广州网站制作', 'https://www.tbadc.com', '', '4', '1', '0', '', '', '1', 'cn', '0', '1610702946', '1610702998');
INSERT INTO `ey_links` VALUES ('13', '1', '1', '快推云', 'https://www.5fa.cn', '', '6', '1', '0', '', '', '1', 'cn', '0', '1610702968', '1610703000');
INSERT INTO `ey_links` VALUES ('14', '1', '1', '房产网站制作', 'https://www.ejucms.com', '', '8', '1', '0', '', '', '1', 'cn', '0', '1610702985', '1610703003');

-- -----------------------------
-- Table structure for `ey_links_group`
-- -----------------------------
DROP TABLE IF EXISTS `ey_links_group`;
CREATE TABLE `ey_links_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) NOT NULL DEFAULT '' COMMENT '分组名称',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '状态(1=显示，0=屏蔽)',
  `sort_order` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序号',
  `lang` varchar(50) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='友情链接分组';

-- -----------------------------
-- Records of `ey_links_group`
-- -----------------------------
INSERT INTO `ey_links_group` VALUES ('1', '默认分组', '1', '100', 'cn', '1610334638', '1610334638');

-- -----------------------------
-- Table structure for `ey_media_content`
-- -----------------------------
DROP TABLE IF EXISTS `ey_media_content`;
CREATE TABLE `ey_media_content` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aid` int(10) NOT NULL DEFAULT '0' COMMENT '文档ID',
  `content` longtext COMMENT '内容详情',
  `courseware` varchar(200) NOT NULL DEFAULT '' COMMENT '课件地址',
  `courseware_free` enum('免费','收费') NOT NULL DEFAULT '免费' COMMENT '课件收费',
  `total_duration` int(10) NOT NULL DEFAULT '0' COMMENT '视频总时长',
  `total_video` int(10) NOT NULL DEFAULT '0' COMMENT '视频数',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `aid` (`aid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='视频附加表';


-- -----------------------------
-- Table structure for `ey_media_file`
-- -----------------------------
DROP TABLE IF EXISTS `ey_media_file`;
CREATE TABLE `ey_media_file` (
  `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '视频模型文件表',
  `aid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文档ID',
  `title` varchar(200) NOT NULL DEFAULT '' COMMENT '文档标题',
  `file_name` varchar(200) NOT NULL DEFAULT '' COMMENT '文件名称',
  `file_title` varchar(200) NOT NULL DEFAULT '' COMMENT '选集标题',
  `file_url` text NOT NULL COMMENT '存储路径',
  `file_time` int(8) NOT NULL DEFAULT '0' COMMENT '文件时长',
  `file_ext` varchar(50) NOT NULL DEFAULT '' COMMENT '文件后缀名',
  `file_size` varchar(255) NOT NULL DEFAULT '' COMMENT '文件大小',
  `file_mime` varchar(200) NOT NULL DEFAULT '' COMMENT '文件类型',
  `uhash` varchar(200) NOT NULL DEFAULT '' COMMENT '自定义的一种加密方式，用于视频播放的权限验证',
  `md5file` varchar(200) NOT NULL DEFAULT '' COMMENT 'md5_file加密，可以检测上传/播放的视频文件是否损坏',
  `is_remote` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否远程 1-远程',
  `playcount` int(10) NOT NULL DEFAULT '0' COMMENT '播放次数',
  `gratis` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否试看，0不试看，1试看',
  `sort_order` smallint(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`file_id`) USING BTREE,
  KEY `aid` (`aid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='视频附件表';


-- -----------------------------
-- Table structure for `ey_media_log`
-- -----------------------------
DROP TABLE IF EXISTS `ey_media_log`;
CREATE TABLE `ey_media_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `users_id` int(10) DEFAULT '0' COMMENT '用户ID',
  `aid` int(10) DEFAULT '0' COMMENT '文档ID',
  `file_id` int(10) DEFAULT '0' COMMENT '视频ID',
  `ip` varchar(20) DEFAULT '' COMMENT 'ip',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '编辑时间',
  PRIMARY KEY (`log_id`),
  KEY `file_id` (`file_id`,`aid`,`users_id`) USING BTREE,
  KEY `aid` (`aid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='视频日志表';


-- -----------------------------
-- Table structure for `ey_media_order`
-- -----------------------------
DROP TABLE IF EXISTS `ey_media_order`;
CREATE TABLE `ey_media_order` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '媒体订单ID',
  `order_code` varchar(30) NOT NULL DEFAULT '' COMMENT '媒体订单编号',
  `users_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `mobile` varchar(20) DEFAULT '' COMMENT '手机',
  `order_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单状态：0未付款，1已付款',
  `order_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单应付总金额',
  `pay_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间',
  `pay_name` varchar(20) NOT NULL DEFAULT '' COMMENT '支付方式名称',
  `wechat_pay_type` varchar(20) NOT NULL DEFAULT '' COMMENT '微信支付时，标记使用的支付类型（扫码支付，微信内部，微信H5页面）',
  `pay_details` text COMMENT '支付时返回的数据，以serialize序列化后存入，用于后续查询。',
  `product_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '视频文档ID',
  `product_name` varchar(100) DEFAULT '' COMMENT '视频文档名称',
  `product_litpic` varchar(500) DEFAULT '' COMMENT '视频文档封面图片',
  `lang` varchar(30) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned DEFAULT '0' COMMENT '下单时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_code` (`order_code`) USING BTREE,
  KEY `users_id` (`users_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='视频订单表';


-- -----------------------------
-- Table structure for `ey_media_play_record`
-- -----------------------------
DROP TABLE IF EXISTS `ey_media_play_record`;
CREATE TABLE `ey_media_play_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `users_id` int(10) DEFAULT '0' COMMENT '用户id',
  `aid` int(10) DEFAULT '0' COMMENT '课程id',
  `file_id` int(10) DEFAULT '0' COMMENT '文件id',
  `play_time` int(10) DEFAULT '0' COMMENT '播放时间',
  `add_time` int(10) DEFAULT '0' COMMENT '添加时间',
  `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='视频播放时长表';


-- -----------------------------
-- Table structure for `ey_pay_api_config`
-- -----------------------------
DROP TABLE IF EXISTS `ey_pay_api_config`;
CREATE TABLE `ey_pay_api_config` (
  `pay_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '支付接口配置ID，自增',
  `pay_name` varchar(64) NOT NULL DEFAULT '' COMMENT '支付接口配置名称，微信支付，支付宝支付...',
  `pay_mark` varchar(64) NOT NULL DEFAULT '' COMMENT '支付接口配置标识，wechat，alipay...',
  `pay_info` text NOT NULL COMMENT '支付接口配置信息，数组以序列化存储',
  `pay_terminal` varchar(100) NOT NULL DEFAULT '' COMMENT '支付时的终端，暂时预留',
  `system_built` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否属于系统内置，0否，1是',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(0=关闭，1=开启)',
  `lang` varchar(30) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`pay_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='支付接口配置表';

-- -----------------------------
-- Records of `ey_pay_api_config`
-- -----------------------------
INSERT INTO `ey_pay_api_config` VALUES ('1', '微信支付', 'wechat', 'a:4:{s:14:\"is_open_wechat\";s:1:\"1\";s:5:\"appid\";s:0:\"\";s:5:\"mchid\";s:0:\"\";s:3:\"key\";s:0:\"\";}', 'a:3:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";}', '1', '1', 'cn', '1590111253', '1616490076');
INSERT INTO `ey_pay_api_config` VALUES ('2', '支付宝支付', 'alipay', 'a:8:{s:14:\"is_open_alipay\";s:1:\"1\";s:7:\"version\";s:1:\"0\";s:6:\"app_id\";s:0:\"\";s:20:\"merchant_private_key\";s:0:\"\";s:17:\"alipay_public_key\";s:0:\"\";s:7:\"account\";s:0:\"\";s:4:\"code\";s:0:\"\";s:2:\"id\";s:0:\"\";}', 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}', '1', '1', 'cn', '1590111253', '1616490080');

-- -----------------------------
-- Table structure for `ey_product_attr`
-- -----------------------------
DROP TABLE IF EXISTS `ey_product_attr`;
CREATE TABLE `ey_product_attr` (
  `product_attr_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '产品属性id自增',
  `aid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '产品id',
  `attr_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '属性id',
  `attr_value` text COMMENT '属性值',
  `attr_price` varchar(255) DEFAULT '' COMMENT '属性价格',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`product_attr_id`),
  KEY `aid` (`aid`) USING BTREE,
  KEY `attr_id` (`attr_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=utf8 COMMENT='产品表单属性值';


-- -----------------------------
-- Table structure for `ey_product_attribute`
-- -----------------------------
DROP TABLE IF EXISTS `ey_product_attribute`;
CREATE TABLE `ey_product_attribute` (
  `attr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性id',
  `attr_name` varchar(60) DEFAULT '' COMMENT '属性名称',
  `typeid` int(11) unsigned DEFAULT '0' COMMENT '栏目id',
  `attr_index` tinyint(1) unsigned DEFAULT '0' COMMENT '0不需要检索 1关键字检索 2范围检索',
  `attr_input_type` tinyint(1) unsigned DEFAULT '0' COMMENT ' 0=文本框，1=下拉框，2=多行文本框',
  `attr_values` text COMMENT '可选值列表',
  `sort_order` int(11) unsigned DEFAULT '0' COMMENT '属性排序',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否已删除，0=否，1=是',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`attr_id`),
  KEY `cat_id` (`typeid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='产品表单属性表';


-- -----------------------------
-- Table structure for `ey_product_content`
-- -----------------------------
DROP TABLE IF EXISTS `ey_product_content`;
CREATE TABLE `ey_product_content` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aid` int(10) DEFAULT '0' COMMENT '文档ID',
  `content` longtext COMMENT '内容详情',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  `fxrq` enum('2019年','2018年','2017年') DEFAULT '2019年' COMMENT '发行日期',
  `jiawei` enum('0-1000','1000-1699','1700-2799','2800-3500','3500-10000') DEFAULT '0-1000' COMMENT '价位区段',
  `yanse` enum('银色','绿色','黑色','灰色') DEFAULT '银色' COMMENT '机身颜色',
  PRIMARY KEY (`id`),
  KEY `news_id` (`aid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='产品附加表';

-- -----------------------------
-- Records of `ey_product_content`
-- -----------------------------
INSERT INTO `ey_product_content` VALUES ('2', '27', '&lt;p style=&quot;text-align:center&quot;&gt;&lt;img src=&quot;/abc/uploads/ueditor/20210111/1-210111144620C3.jpg&quot; title=&quot;华为HUAWEI NOTE 8(图1)&quot; alt=&quot;华为HUAWEI NOTE 8(图1)&quot;/&gt;&lt;/p&gt;', '1610615791', '1610615791', '2018年', '1700-2799', '银色');
INSERT INTO `ey_product_content` VALUES ('3', '28', '&lt;p&gt;轻薄全金属机身 / 256GB SSD / 第八代 Intel 酷睿i5 处理器 / FHD 全贴合屏幕 / 指纹解锁 / office激活不支持7天无理由退货&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;img style=&quot;max-width:100%!important;height:auto;&quot; src=&quot;/abc/uploads/ueditor/20190114/aa0555d4f00163878c1d39ab046bb742.jpg&quot; title=&quot;小米笔记本Air 13.3(图1)&quot; alt=&quot;小米笔记本Air 13.3(图1)&quot;/&gt;&lt;/p&gt;', '1610615782', '1610615782', '2019年', '3500-10000', '黑色');
INSERT INTO `ey_product_content` VALUES ('4', '29', '&lt;p style=&quot;text-align: center;&quot;&gt;特性	M3平板定制AKG品牌高保真耳机，配合M3平板享受HiFi音质&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;img src=&quot;/abc/uploads/ueditor/20210111/1-210111144012118.jpg&quot; title=&quot; 小米蓝牙项圈耳机(图1)&quot; alt=&quot; 小米蓝牙项圈耳机(图1)&quot;/&gt;&lt;img src=&quot;/abc/uploads/ueditor/20210111/1-21011114414R57.jpg&quot; title=&quot; 小米蓝牙项圈耳机(图2)&quot; alt=&quot; 小米蓝牙项圈耳机(图2)&quot;/&gt;&lt;/p&gt;', '1610615773', '1610615773', '2017年', '0-1000', '银色');
INSERT INTO `ey_product_content` VALUES ('5', '37', '&lt;p&gt;全身都是科技亮点！7nm麒麟芯片，问鼎性能巅峰，4000万超广角徕卡三摄，随手捕捉大场面，支持25mm微距拍摄，解锁大波新题材，充电也有无线、反向玩法，快充之快刷新世界观。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;&lt;img style=&quot;max-width:100%!important;height:auto;&quot; src=&quot;/abc/uploads/ueditor/20190319/e44a5110abdd18ef314e34769272bb44.jpg&quot; title=&quot;华为无线快充手机(图1)&quot; alt=&quot;华为无线快充手机(图1)&quot;/&gt;&lt;br/&gt;&lt;/p&gt;', '1610615751', '1610615751', '2019年', '2800-3500', '绿色');
INSERT INTO `ey_product_content` VALUES ('10', '89', '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;img src=&quot;/abc/uploads/ueditor/20210111/1-210111143T0R0.png&quot; title=&quot;Apple iPhone 8 Plus (A1899) 64GB 深空灰色 移动联通4G手机(图1)&quot; alt=&quot;Apple iPhone 8 Plus (A1899) 64GB 深空灰色 移动联通4G手机(图1)&quot;/&gt;&lt;/p&gt;', '1610615667', '1610615667', '2017年', '1000-1699', '黑色');
INSERT INTO `ey_product_content` VALUES ('11', '90', '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;img src=&quot;/abc/uploads/ueditor/20210111/1-21011114234VL.jpg&quot; title=&quot;小米8屏幕指纹版 6GB+128GB 黑色 全网通4G 双卡双待 全面屏拍照智能游戏手机(图1)&quot; alt=&quot;小米8屏幕指纹版 6GB+128GB 黑色 全网通4G 双卡双待 全面屏拍照智能游戏手机(图1)&quot;/&gt; &amp;nbsp; &amp;nbsp;&lt;img src=&quot;/abc/uploads/ueditor/20210111/1-2101111424243S.jpg&quot; title=&quot;小米8屏幕指纹版 6GB+128GB 黑色 全网通4G 双卡双待 全面屏拍照智能游戏手机(图2)&quot; alt=&quot;小米8屏幕指纹版 6GB+128GB 黑色 全网通4G 双卡双待 全面屏拍照智能游戏手机(图2)&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1610615660', '1610615660', '2018年', '1700-2799', '黑色');
INSERT INTO `ey_product_content` VALUES ('12', '98', '&lt;p&gt;&lt;img style=&quot;max-width:100%!important;height:auto;&quot; src=&quot;/abc/uploads/ueditor/20190808/dea649468185da6603ff8ceb4dc0bd02.jpg&quot; title=&quot;MIIX520 二合一笔记本12.2英寸 i7(图1)&quot; alt=&quot;MIIX520 二合一笔记本12.2英寸 i7(图1)&quot;/&gt;&lt;/p&gt;', '1610615629', '1610615629', '2019年', '3500-10000', '黑色');
INSERT INTO `ey_product_content` VALUES ('13', '99', '&lt;p&gt;&lt;img style=&quot;max-width:100%!important;height:auto;&quot; src=&quot;/abc/uploads/ueditor/20190808/a008be68bc7b5743b93199d321bac0d6.jpg&quot; title=&quot;MIIX 520 酷睿i5笔记本(图1)&quot; alt=&quot;MIIX 520 酷睿i5笔记本(图1)&quot;/&gt;&lt;/p&gt;', '1610615619', '1610615619', '2018年', '3500-10000', '黑色');
INSERT INTO `ey_product_content` VALUES ('14', '100', '&lt;p&gt;&lt;img style=&quot;max-width:100%!important;height:auto;&quot; src=&quot;/abc/uploads/ueditor/20190808/847b3f2651ecfcf10a8efb17dbb8b27f.jpg&quot; title=&quot;小新 Air 超轻薄笔记本(图1)&quot; alt=&quot;小新 Air 超轻薄笔记本(图1)&quot;/&gt;&lt;/p&gt;', '1610615609', '1610615609', '2017年', '3500-10000', '银色');
INSERT INTO `ey_product_content` VALUES ('15', '101', '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;img style=&quot;max-width:100%!important;height:auto;&quot; src=&quot;/abc/uploads/ueditor/20190808/cd0d121db4625c6b9ace794c921ac645.jpg&quot; title=&quot;联想 X1无线运动蓝牙耳机(图1)&quot; alt=&quot;联想 X1无线运动蓝牙耳机(图1)&quot;/&gt;&lt;/p&gt;', '1610615602', '1610615602', '2019年', '0-1000', '黑色');
INSERT INTO `ey_product_content` VALUES ('16', '102', '&lt;p style=&quot;text-align:center&quot;&gt;&lt;img style=&quot;max-width:100%!important;height:auto;&quot; src=&quot;/abc/uploads/ueditor/20190808/a7bc6e179f0ae1ff8499c3bb34a1a233.jpg&quot; title=&quot;联想智能音箱MINI(图1)&quot; alt=&quot;联想智能音箱MINI(图1)&quot;/&gt;&lt;/p&gt;', '1610615586', '1610615586', '2018年', '0-1000', '绿色');
INSERT INTO `ey_product_content` VALUES ('17', '103', '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;img style=&quot;max-width:100%!important;height:auto;&quot; src=&quot;/abc/uploads/ueditor/20190808/aedbc2e4cd0257c38a1677f5825a3662.jpg&quot; title=&quot;联想智能音箱G1(图1)&quot; alt=&quot;联想智能音箱G1(图1)&quot;/&gt;&lt;/p&gt;', '1610615580', '1610615580', '2019年', '0-1000', '灰色');

-- -----------------------------
-- Table structure for `ey_product_img`
-- -----------------------------
DROP TABLE IF EXISTS `ey_product_img`;
CREATE TABLE `ey_product_img` (
  `img_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `aid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '产品ID',
  `title` varchar(200) DEFAULT '' COMMENT '产品标题',
  `image_url` varchar(255) DEFAULT '' COMMENT '文件存储路径',
  `intro` varchar(2000) DEFAULT '' COMMENT '图集描述',
  `width` int(11) DEFAULT '0' COMMENT '图片宽度',
  `height` int(11) DEFAULT '0' COMMENT '图片高度',
  `filesize` varchar(255) DEFAULT '' COMMENT '文件大小',
  `mime` varchar(50) DEFAULT '' COMMENT '图片类型',
  `sort_order` smallint(5) DEFAULT '0' COMMENT '排序',
  `add_time` int(10) unsigned DEFAULT '0' COMMENT '上传时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`img_id`),
  KEY `arcid` (`aid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=239 DEFAULT CHARSET=utf8 COMMENT='产品图片表';

-- -----------------------------
-- Records of `ey_product_img`
-- -----------------------------
INSERT INTO `ey_product_img` VALUES ('238', '27', '华为HUAWEI NOTE 8', '/abc/uploads/allimg/20190319/644f02053cba0e7925a12afc4d97f473.jpg', '', '0', '0', '0', '', '2', '1610615791', '0');
INSERT INTO `ey_product_img` VALUES ('236', '28', '小米笔记本Air 13.3', '/abc/uploads/allimg/20190114/ab5d06499e319c69ea0f07e7443846f0.jpg', '', '0', '0', '0', '', '2', '1610615782', '0');
INSERT INTO `ey_product_img` VALUES ('235', '28', '小米笔记本Air 13.3', '/abc/uploads/allimg/20190114/44bfc4ca62d0c69d750716616410ff96.jpg', '', '0', '0', '0', '', '1', '1610615782', '0');
INSERT INTO `ey_product_img` VALUES ('234', '29', ' 小米蓝牙项圈耳机', '/abc/uploads/allimg/20190114/e4c13f6756671c2352699564e72090cd.jpg', '', '0', '0', '0', '', '2', '1610615773', '0');
INSERT INTO `ey_product_img` VALUES ('233', '29', ' 小米蓝牙项圈耳机', '/abc/uploads/allimg/20190114/252a53e6fbc8f441b2570f755d2bbeb8.jpg', '', '0', '0', '0', '', '1', '1610615773', '0');
INSERT INTO `ey_product_img` VALUES ('237', '27', '华为HUAWEI NOTE 8', '/abc/uploads/allimg/20190319/26ae26bc48454c5504f5e73ab40c2ef7.jpg', '', '0', '0', '0', '', '1', '1610615791', '0');
INSERT INTO `ey_product_img` VALUES ('232', '37', '华为无线快充手机', '/abc/uploads/allimg/20190319/7d9262ce87a9596a20c82fd04828c322.jpg', '', '0', '0', '0', '', '3', '1610615751', '0');
INSERT INTO `ey_product_img` VALUES ('231', '37', '华为无线快充手机', '/abc/uploads/allimg/20190319/d86971fb735a22e0a29ba039c5a4179b.jpg', '', '0', '0', '0', '', '2', '1610615751', '0');
INSERT INTO `ey_product_img` VALUES ('230', '37', '华为无线快充手机', '/abc/uploads/allimg/20190319/db90933b59102a0f3fa0d2588bd8e2fb.jpg', '', '0', '0', '0', '', '1', '1610615751', '0');
INSERT INTO `ey_product_img` VALUES ('229', '89', 'Apple iPhone 8 Plus (A1899) 64GB 深空灰色 移动联通4G手机', '/abc/uploads/allimg/20190731/9d5ca6bb09d1d728f18ccbaa5cfce8a3.jpg', '', '0', '0', '0', '', '3', '1610615667', '0');
INSERT INTO `ey_product_img` VALUES ('228', '89', 'Apple iPhone 8 Plus (A1899) 64GB 深空灰色 移动联通4G手机', '/abc/uploads/allimg/20190731/788152105227529ea8ce3e222ee166e6.jpg', '', '0', '0', '0', '', '2', '1610615667', '0');
INSERT INTO `ey_product_img` VALUES ('227', '89', 'Apple iPhone 8 Plus (A1899) 64GB 深空灰色 移动联通4G手机', '/abc/uploads/allimg/20190731/b0a46af69ac9c4148b345eb71104c811.jpg', '', '0', '0', '0', '', '1', '1610615667', '0');
INSERT INTO `ey_product_img` VALUES ('226', '90', '小米8屏幕指纹版 6GB+128GB 黑色 全网通4G 双卡双待 全面屏拍照智能游戏手机', '/abc/uploads/allimg/20190731/2a8aa14ac6f3823617e9aa20ddcb2a05.jpg', '', '0', '0', '0', '', '3', '1610615660', '0');
INSERT INTO `ey_product_img` VALUES ('225', '90', '小米8屏幕指纹版 6GB+128GB 黑色 全网通4G 双卡双待 全面屏拍照智能游戏手机', '/abc/uploads/allimg/20190731/de250eebee6f348b1e3d4c3416d6a541.jpg', '', '0', '0', '0', '', '2', '1610615660', '0');
INSERT INTO `ey_product_img` VALUES ('224', '90', '小米8屏幕指纹版 6GB+128GB 黑色 全网通4G 双卡双待 全面屏拍照智能游戏手机', '/abc/uploads/allimg/20190731/b3cc010ae2ff9784be8f19758fe56b6b.jpg', '', '0', '0', '0', '', '1', '1610615660', '0');
INSERT INTO `ey_product_img` VALUES ('223', '98', 'MIIX520 二合一笔记本12.2英寸 i7', '/abc/uploads/allimg/20190808/7dd05a89099c482a51be7faf1bb38ad4.jpg', '', '0', '0', '0', '', '1', '1610615629', '0');
INSERT INTO `ey_product_img` VALUES ('222', '99', 'MIIX 520 酷睿i5笔记本', '/abc/uploads/allimg/20190808/821fcaa266d291b4f504fb9a1d412c1c.jpg', '', '0', '0', '0', '', '1', '1610615619', '0');
INSERT INTO `ey_product_img` VALUES ('221', '100', '小新 Air 超轻薄笔记本', '/abc/uploads/allimg/20190808/a4b1ab346ae389e638f4a424b7396ee2.jpg', '', '0', '0', '0', '', '1', '1610615609', '0');
INSERT INTO `ey_product_img` VALUES ('220', '101', '联想 X1无线运动蓝牙耳机', '/abc/uploads/allimg/20190808/3ade68e134d3f8fbbd3401c545541106.jpg', '', '0', '0', '0', '', '1', '1610615602', '0');
INSERT INTO `ey_product_img` VALUES ('219', '102', '联想智能音箱MINI', '/abc/uploads/allimg/20190808/989d19deb2377e199ec63d5ef9244be8.jpg', '', '0', '0', '0', '', '1', '1610615586', '0');
INSERT INTO `ey_product_img` VALUES ('218', '103', '联想智能音箱G1', '/abc/uploads/allimg/20190808/13fba5d0f2454c4b8fee4ada1d3fb39b.jpg', '', '0', '0', '0', '', '1', '1610615580', '0');

-- -----------------------------
-- Table structure for `ey_product_netdisk`
-- -----------------------------
DROP TABLE IF EXISTS `ey_product_netdisk`;
CREATE TABLE `ey_product_netdisk` (
  `nd_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '网盘商品id',
  `aid` int(10) DEFAULT '0' COMMENT '产品ID',
  `netdisk_url` varchar(255) NOT NULL DEFAULT '' COMMENT '网盘地址',
  `netdisk_pwd` varchar(50) NOT NULL DEFAULT '' COMMENT '提取码',
  `unzip_pwd` varchar(50) NOT NULL DEFAULT '' COMMENT '解压密码',
  `text_content` text NOT NULL COMMENT '文本内容',
  `lang` varchar(10) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`nd_id`) USING BTREE,
  KEY `aid` (`aid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品虚拟表';


-- -----------------------------
-- Table structure for `ey_product_spec_data`
-- -----------------------------
DROP TABLE IF EXISTS `ey_product_spec_data`;
CREATE TABLE `ey_product_spec_data` (
  `spec_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `aid` int(10) DEFAULT '0' COMMENT '产品ID',
  `spec_mark_id` int(10) DEFAULT '0' COMMENT '规格标记ID',
  `spec_name` varchar(100) DEFAULT '' COMMENT '规格名称',
  `spec_value_id` int(10) DEFAULT '0' COMMENT '规格值ID',
  `spec_value` varchar(100) DEFAULT '' COMMENT '规格值',
  `spec_is_select` tinyint(1) DEFAULT '0' COMMENT '是否选中（0=否，1=是）',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`spec_id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COMMENT='产品规格数据表';

-- -----------------------------
-- Records of `ey_product_spec_data`
-- -----------------------------
INSERT INTO `ey_product_spec_data` VALUES ('1', '27', '1', '产品颜色', '1', '红', '1', 'cn', '1571037785', '1571037789');
INSERT INTO `ey_product_spec_data` VALUES ('2', '27', '1', '产品颜色', '2', '蓝', '1', 'cn', '1571037785', '1571037790');
INSERT INTO `ey_product_spec_data` VALUES ('3', '27', '1', '产品颜色', '3', '黄', '1', 'cn', '1571037785', '1571037792');
INSERT INTO `ey_product_spec_data` VALUES ('4', '37', '1', '产品颜色', '1', '红', '1', 'cn', '1571037916', '1571037918');
INSERT INTO `ey_product_spec_data` VALUES ('5', '37', '1', '产品颜色', '2', '蓝', '1', 'cn', '1571037916', '1571037919');
INSERT INTO `ey_product_spec_data` VALUES ('6', '37', '1', '产品颜色', '3', '黄', '1', 'cn', '1571037916', '1571037920');
INSERT INTO `ey_product_spec_data` VALUES ('7', '90', '1', '产品颜色', '1', '红', '1', 'cn', '1571038017', '1571038018');
INSERT INTO `ey_product_spec_data` VALUES ('8', '90', '1', '产品颜色', '2', '蓝', '1', 'cn', '1571038017', '1571038019');
INSERT INTO `ey_product_spec_data` VALUES ('9', '90', '1', '产品颜色', '3', '黄', '1', 'cn', '1571038017', '1571038020');
INSERT INTO `ey_product_spec_data` VALUES ('10', '102', '1', '产品颜色', '1', '红', '1', 'cn', '1571038068', '1571038070');
INSERT INTO `ey_product_spec_data` VALUES ('11', '102', '1', '产品颜色', '2', '蓝', '1', 'cn', '1571038068', '1571038071');
INSERT INTO `ey_product_spec_data` VALUES ('12', '102', '1', '产品颜色', '3', '黄', '1', 'cn', '1571038068', '1571038072');
INSERT INTO `ey_product_spec_data` VALUES ('13', '103', '1', '产品颜色', '1', '红', '1', 'cn', '1571038093', '1571038095');
INSERT INTO `ey_product_spec_data` VALUES ('14', '103', '1', '产品颜色', '2', '蓝', '1', 'cn', '1571038093', '1571038097');
INSERT INTO `ey_product_spec_data` VALUES ('15', '103', '1', '产品颜色', '3', '黄', '1', 'cn', '1571038093', '1571038098');
INSERT INTO `ey_product_spec_data` VALUES ('16', '101', '1', '产品颜色', '1', '红', '1', 'cn', '1571038124', '1571038126');
INSERT INTO `ey_product_spec_data` VALUES ('17', '101', '1', '产品颜色', '2', '蓝', '1', 'cn', '1571038124', '1571038127');
INSERT INTO `ey_product_spec_data` VALUES ('18', '101', '1', '产品颜色', '3', '黄', '1', 'cn', '1571038124', '1571038129');
INSERT INTO `ey_product_spec_data` VALUES ('19', '29', '1', '产品颜色', '1', '红', '1', 'cn', '1571038143', '1571038146');
INSERT INTO `ey_product_spec_data` VALUES ('20', '29', '1', '产品颜色', '2', '蓝', '1', 'cn', '1571038143', '1571038147');
INSERT INTO `ey_product_spec_data` VALUES ('21', '29', '1', '产品颜色', '3', '黄', '1', 'cn', '1571038143', '1571038148');
INSERT INTO `ey_product_spec_data` VALUES ('22', '28', '1', '产品颜色', '1', '红', '1', 'cn', '1571038168', '1571038170');
INSERT INTO `ey_product_spec_data` VALUES ('23', '28', '1', '产品颜色', '2', '蓝', '1', 'cn', '1571038168', '1571038171');
INSERT INTO `ey_product_spec_data` VALUES ('24', '28', '1', '产品颜色', '3', '黄', '1', 'cn', '1571038168', '1571038172');
INSERT INTO `ey_product_spec_data` VALUES ('25', '98', '1', '产品颜色', '1', '红', '1', 'cn', '1571038214', '1571038215');
INSERT INTO `ey_product_spec_data` VALUES ('26', '98', '1', '产品颜色', '2', '蓝', '1', 'cn', '1571038214', '1571038216');
INSERT INTO `ey_product_spec_data` VALUES ('27', '98', '1', '产品颜色', '3', '黄', '1', 'cn', '1571038214', '1571038217');
INSERT INTO `ey_product_spec_data` VALUES ('28', '99', '1', '产品颜色', '1', '红', '1', 'cn', '1571038241', '1571038242');
INSERT INTO `ey_product_spec_data` VALUES ('29', '99', '1', '产品颜色', '2', '蓝', '1', 'cn', '1571038241', '1571038244');
INSERT INTO `ey_product_spec_data` VALUES ('30', '99', '1', '产品颜色', '3', '黄', '1', 'cn', '1571038241', '1571038243');
INSERT INTO `ey_product_spec_data` VALUES ('31', '100', '1', '产品颜色', '1', '红', '1', 'cn', '1571038261', '1571038263');
INSERT INTO `ey_product_spec_data` VALUES ('32', '100', '1', '产品颜色', '2', '蓝', '1', 'cn', '1571038261', '1571038264');
INSERT INTO `ey_product_spec_data` VALUES ('33', '100', '1', '产品颜色', '3', '黄', '1', 'cn', '1571038261', '1571038264');

-- -----------------------------
-- Table structure for `ey_product_spec_preset`
-- -----------------------------
DROP TABLE IF EXISTS `ey_product_spec_preset`;
CREATE TABLE `ey_product_spec_preset` (
  `preset_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `preset_mark_id` int(10) DEFAULT '0' COMMENT '预设参数标记ID',
  `preset_name` varchar(100) DEFAULT '' COMMENT '规格名称',
  `preset_value` varchar(100) DEFAULT '' COMMENT '规格值',
  `spec_sync` tinyint(1) unsigned DEFAULT '0' COMMENT '是否同步到已发布的商品规格：0否，1是。',
  `sort_order` int(10) DEFAULT '100' COMMENT '排序号',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`preset_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='产品规格预设表';

-- -----------------------------
-- Records of `ey_product_spec_preset`
-- -----------------------------
INSERT INTO `ey_product_spec_preset` VALUES ('1', '1', '产品颜色', '红', '0', '100', 'cn', '1565752372', '1565752623');
INSERT INTO `ey_product_spec_preset` VALUES ('2', '1', '产品颜色', '蓝', '0', '100', 'cn', '1565752372', '1565752623');
INSERT INTO `ey_product_spec_preset` VALUES ('3', '1', '产品颜色', '黄', '0', '100', 'cn', '1565752372', '1565752623');

-- -----------------------------
-- Table structure for `ey_product_spec_value`
-- -----------------------------
DROP TABLE IF EXISTS `ey_product_spec_value`;
CREATE TABLE `ey_product_spec_value` (
  `value_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `aid` int(10) NOT NULL DEFAULT '0' COMMENT '产品ID',
  `spec_value_id` varchar(100) NOT NULL DEFAULT '' COMMENT '规格值ID',
  `spec_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '规格价格',
  `spec_stock` int(10) NOT NULL DEFAULT '0' COMMENT '规格库存',
  `spec_sales_num` int(10) NOT NULL DEFAULT '0' COMMENT '销售量',
  `seckill_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '秒杀价格',
  `seckill_stock` int(10) NOT NULL DEFAULT '0' COMMENT '秒杀库存(独立库存，与spec_stock库存不同步)',
  `seckill_sales_num` int(10) NOT NULL DEFAULT '0' COMMENT '秒杀销售量',
  `is_seckill` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-普通 1-秒杀',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`value_id`)
) ENGINE=MyISAM AUTO_INCREMENT=121 DEFAULT CHARSET=utf8 COMMENT='产品多规格组装表';

-- -----------------------------
-- Records of `ey_product_spec_value`
-- -----------------------------
INSERT INTO `ey_product_spec_value` VALUES ('120', '27', '3', '1999.00', '996', '3', '0.00', '0', '0', '0', 'cn', '1610615791', '1610615791');
INSERT INTO `ey_product_spec_value` VALUES ('119', '27', '2', '1959.00', '997', '2', '0.00', '0', '0', '0', 'cn', '1610615791', '1610615791');
INSERT INTO `ey_product_spec_value` VALUES ('118', '27', '1', '1909.00', '998', '1', '0.00', '0', '0', '0', 'cn', '1610615791', '1610615791');
INSERT INTO `ey_product_spec_value` VALUES ('111', '37', '3', '3200.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615751', '1610615751');
INSERT INTO `ey_product_spec_value` VALUES ('110', '37', '2', '3169.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615751', '1610615751');
INSERT INTO `ey_product_spec_value` VALUES ('109', '37', '1', '3109.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615751', '1610615751');
INSERT INTO `ey_product_spec_value` VALUES ('108', '90', '3', '1799.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615660', '1610615660');
INSERT INTO `ey_product_spec_value` VALUES ('107', '90', '2', '1759.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615660', '1610615660');
INSERT INTO `ey_product_spec_value` VALUES ('106', '90', '1', '1709.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615660', '1610615660');
INSERT INTO `ey_product_spec_value` VALUES ('93', '102', '3', '339.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615586', '1610615586');
INSERT INTO `ey_product_spec_value` VALUES ('92', '102', '2', '329.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615586', '1610615586');
INSERT INTO `ey_product_spec_value` VALUES ('91', '102', '1', '319.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615586', '1610615586');
INSERT INTO `ey_product_spec_value` VALUES ('90', '103', '3', '599.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615580', '1610615580');
INSERT INTO `ey_product_spec_value` VALUES ('89', '103', '2', '569.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615580', '1610615580');
INSERT INTO `ey_product_spec_value` VALUES ('88', '103', '1', '539.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615580', '1610615580');
INSERT INTO `ey_product_spec_value` VALUES ('96', '101', '3', '99.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615602', '1610615602');
INSERT INTO `ey_product_spec_value` VALUES ('95', '101', '2', '99.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615602', '1610615602');
INSERT INTO `ey_product_spec_value` VALUES ('94', '101', '1', '99.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615602', '1610615602');
INSERT INTO `ey_product_spec_value` VALUES ('114', '29', '3', '198.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615773', '1610615773');
INSERT INTO `ey_product_spec_value` VALUES ('113', '29', '2', '198.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615773', '1610615773');
INSERT INTO `ey_product_spec_value` VALUES ('112', '29', '1', '198.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615773', '1610615773');
INSERT INTO `ey_product_spec_value` VALUES ('117', '28', '3', '4600.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615782', '1610615782');
INSERT INTO `ey_product_spec_value` VALUES ('116', '28', '2', '4560.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615782', '1610615782');
INSERT INTO `ey_product_spec_value` VALUES ('115', '28', '1', '4530.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615782', '1610615782');
INSERT INTO `ey_product_spec_value` VALUES ('105', '98', '3', '6999.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615629', '1610615629');
INSERT INTO `ey_product_spec_value` VALUES ('104', '98', '2', '6969.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615629', '1610615629');
INSERT INTO `ey_product_spec_value` VALUES ('103', '98', '1', '6939.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615629', '1610615629');
INSERT INTO `ey_product_spec_value` VALUES ('102', '99', '3', '5269.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615619', '1610615619');
INSERT INTO `ey_product_spec_value` VALUES ('101', '99', '2', '5299.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615619', '1610615619');
INSERT INTO `ey_product_spec_value` VALUES ('100', '99', '1', '5239.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615619', '1610615619');
INSERT INTO `ey_product_spec_value` VALUES ('99', '100', '3', '5499.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615609', '1610615609');
INSERT INTO `ey_product_spec_value` VALUES ('98', '100', '2', '5499.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615609', '1610615609');
INSERT INTO `ey_product_spec_value` VALUES ('97', '100', '1', '5499.00', '999', '0', '0.00', '0', '0', '0', 'cn', '1610615609', '1610615609');

-- -----------------------------
-- Table structure for `ey_quickentry`
-- -----------------------------
DROP TABLE IF EXISTS `ey_quickentry`;
CREATE TABLE `ey_quickentry` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) DEFAULT '' COMMENT '名称',
  `laytext` varchar(50) DEFAULT '' COMMENT '完整标题',
  `type` smallint(5) DEFAULT '0' COMMENT '归类，1=快捷入口，2=内容统计',
  `controller` varchar(20) DEFAULT '' COMMENT '控制器名',
  `action` varchar(20) DEFAULT '' COMMENT '操作名',
  `vars` varchar(100) DEFAULT '' COMMENT 'URL参数字符串',
  `groups` smallint(5) DEFAULT '0' COMMENT '分组，1=模型',
  `checked` tinyint(4) DEFAULT '0' COMMENT '选中，0=否，1=是',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态，1=有效，0=无效',
  `sort_order` int(10) DEFAULT '0' COMMENT '排序',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `type` (`type`,`status`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 COMMENT='快捷入口表';

-- -----------------------------
-- Records of `ey_quickentry`
-- -----------------------------
INSERT INTO `ey_quickentry` VALUES ('1', '产品', '产品列表', '1', 'Product', 'index', 'channel=2', '1', '0', '1', '3', '1569232484', '1624416370');
INSERT INTO `ey_quickentry` VALUES ('2', '下载', '下载列表', '1', 'Download', 'index', 'channel=4', '1', '0', '1', '4', '1569232484', '1624416370');
INSERT INTO `ey_quickentry` VALUES ('3', '文章', '文章列表', '1', 'Article', 'index', 'channel=1', '1', '0', '1', '6', '1569232484', '1624416370');
INSERT INTO `ey_quickentry` VALUES ('4', '图集', '图集列表', '1', 'Images', 'index', 'channel=3', '1', '0', '1', '7', '1569232484', '1624416370');
INSERT INTO `ey_quickentry` VALUES ('5', '内容管理', '内容列表', '1', 'Archives', 'index', '', '0', '0', '1', '13', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('7', '回收站', '回收站', '1', 'RecycleBin', 'archives_index', '', '0', '1', '1', '4', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('8', '栏目管理', '栏目管理', '1', 'Arctype', 'index', '', '0', '0', '1', '5', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('9', '留言', '留言列表', '1', 'Guestbook', 'index', 'channel=8', '1', '0', '1', '6', '1569232484', '1624416370');
INSERT INTO `ey_quickentry` VALUES ('10', '网站信息', '网站信息', '1', 'System', 'web', '', '0', '0', '1', '7', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('11', '水印配置', '水印配置', '1', 'System', 'water', '', '0', '1', '1', '8', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('12', '缩略图配置', '缩略图配置', '1', 'System', 'thumb', '', '0', '1', '1', '9', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('13', '数据备份', '数据备份', '1', 'Tools', 'index', '', '0', '0', '1', '11', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('14', 'URL配置', 'URL配置', '1', 'Seo', 'seo', '', '0', '1', '1', '1', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('15', '模板管理', '模板管理', '1', 'Filemanager', 'index', '', '0', '1', '1', '6', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('16', 'SiteMap', 'SiteMap', '1', 'Sitemap', 'index', '', '0', '1', '1', '12', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('17', '频道模型', '频道模型', '1', 'Channeltype', 'index', '', '0', '1', '1', '2', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('18', '广告管理', '广告管理', '1', 'AdPosition', 'index', '', '0', '0', '1', '3', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('19', '友情链接', '友情链接', '1', 'Links', 'index', '', '0', '0', '1', '10', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('20', 'Tags管理', 'Tags管理', '1', 'Tags', 'index', '', '0', '1', '1', '14', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('21', '管理员管理', '管理员管理', '1', 'Admin', 'index', '', '0', '0', '1', '15', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('22', '接口配置', '接口配置', '1', 'System', 'api_conf', '', '0', '1', '1', '16', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('23', '文章', '文章列表', '2', 'Article', 'index', 'channel=1', '1', '1', '1', '1', '1569310798', '1624416370');
INSERT INTO `ey_quickentry` VALUES ('24', '产品', '产品列表', '2', 'Product', 'index', 'channel=2', '1', '0', '1', '2', '1569310798', '1624416370');
INSERT INTO `ey_quickentry` VALUES ('25', '下载', '下载列表', '2', 'Download', 'index', 'channel=4', '1', '0', '1', '4', '1569310798', '1624416370');
INSERT INTO `ey_quickentry` VALUES ('26', '图集', '图集列表', '2', 'Images', 'index', 'channel=3', '1', '0', '1', '3', '1569310798', '1624416370');
INSERT INTO `ey_quickentry` VALUES ('27', '留言', '留言列表', '2', 'Guestbook', 'index', 'channel=8', '1', '0', '1', '5', '1569310798', '1624416370');
INSERT INTO `ey_quickentry` VALUES ('28', '广告', '广告管理', '2', 'AdPosition', 'index', '', '0', '1', '1', '8', '1569232484', '1571898872');
INSERT INTO `ey_quickentry` VALUES ('29', '友情链接', '友情链接', '2', 'Links', 'index', '', '0', '1', '1', '9', '1569232484', '1571898872');
INSERT INTO `ey_quickentry` VALUES ('30', 'Tags标签', 'Tags管理', '2', 'Tags', 'index', '', '0', '1', '1', '10', '1569232484', '1571898872');
INSERT INTO `ey_quickentry` VALUES ('31', '会员', '会员管理', '2', 'Member', 'users_index', '', '0', '0', '1', '7', '1569232484', '1624416370');
INSERT INTO `ey_quickentry` VALUES ('32', '插件应用', '插件应用', '1', 'Weapp', 'index', '', '0', '0', '1', '17', '1569232484', '1624416370');
INSERT INTO `ey_quickentry` VALUES ('33', '会员中心', '会员中心', '1', 'Member', 'users_index', '', '0', '0', '1', '18', '1569232484', '1624416370');
INSERT INTO `ey_quickentry` VALUES ('34', '商城中心', '商城中心', '1', 'Shop', 'index', '', '0', '0', '0', '19', '1569232484', '1624416370');
INSERT INTO `ey_quickentry` VALUES ('35', '订单', '订单管理', '2', 'Shop', 'index', '', '0', '0', '0', '6', '1569232484', '1624416370');
INSERT INTO `ey_quickentry` VALUES ('36', '人才招聘', '人才招聘列表', '1', 'Custom', 'index', 'channel=9', '1', '0', '1', '100', '1574233851', '1624416370');
INSERT INTO `ey_quickentry` VALUES ('37', '人才招聘', '人才招聘列表', '2', 'Custom', 'index', 'channel=9', '1', '0', '1', '100', '1574233853', '1624416370');
INSERT INTO `ey_quickentry` VALUES ('39', '专题', '专题列表', '2', 'Special', 'index', 'channel=7', '1', '0', '0', '7', '1600078966', '1624416370');
INSERT INTO `ey_quickentry` VALUES ('41', '视频', '视频列表', '2', 'Media', 'index', 'channel=5', '1', '0', '0', '4', '1569310798', '1624416370');

-- -----------------------------
-- Table structure for `ey_recruit_content`
-- -----------------------------
DROP TABLE IF EXISTS `ey_recruit_content`;
CREATE TABLE `ey_recruit_content` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aid` int(10) DEFAULT '0' COMMENT '文档ID',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  `gzdd` varchar(200) NOT NULL DEFAULT '' COMMENT '工作地点',
  `xlyq` varchar(200) NOT NULL DEFAULT '' COMMENT '学历要求',
  `xzdy` varchar(200) NOT NULL DEFAULT '' COMMENT '薪资待遇',
  `gzxz` varchar(200) NOT NULL DEFAULT '' COMMENT '工作性质',
  `gznx` varchar(200) NOT NULL DEFAULT '' COMMENT '工作年限',
  `zprs` varchar(200) NOT NULL DEFAULT '' COMMENT '招聘人数',
  `nnxq` longtext COMMENT '内容详情',
  PRIMARY KEY (`id`),
  KEY `aid` (`aid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='附加表';

-- -----------------------------
-- Records of `ey_recruit_content`
-- -----------------------------
INSERT INTO `ey_recruit_content` VALUES ('1', '82', '1563528211', '1563528211', '广州市', '中专以上学历', '5000-10000元', '全职', '1年以上', '若干', '&lt;p&gt;工作内容：&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;1、负责公司手机游戏产品的在线推广；&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;2、做好每天的推广统计，定制有效的投放策略并执行；&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;3、完成每天的业绩要求，只要你努力，月入过万不是梦&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;职位要求：&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;1学历不限，欢迎优秀应届生（优秀者可放宽）；男女不限，19~24岁&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;2.亲和力强、沟通流畅、重点突出；&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;3.个性开朗、反应敏捷，有较强的服务意识和责任心；&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;4.能熟练运用QQ及微信聊天软件，懂得电脑的基本操作，打字速度不限&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;我们期待怀揣梦想的你加入我们！&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;5、有经验者优先考虑！！！！！&lt;/p&gt;');
INSERT INTO `ey_recruit_content` VALUES ('2', '83', '1563528292', '1563528292', '广州市', '中专以上学历', '5000-8000元', '全职', '2年以上', '2位', '&lt;p&gt;&lt;span style=&quot;color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);&quot;&gt;岗位职责：&lt;/span&gt;&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;&lt;span style=&quot;color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);&quot;&gt;1、负责网站相关栏目、信息的搜集、编辑、发布等工作。&lt;/span&gt;&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;&lt;span style=&quot;color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);&quot;&gt;2、完成信息内容的策划和日常信息的更新与维护。&lt;/span&gt;&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;&lt;span style=&quot;color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);&quot;&gt;3、编写网站宣传资料及相关产品信息。&lt;/span&gt;&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;&lt;span style=&quot;color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);&quot;&gt;4、配合部门编辑策划推广活动。&lt;/span&gt;&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;&lt;span style=&quot;color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);&quot;&gt;5、部门总监下发的其他任务。&lt;/span&gt;&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;&lt;span style=&quot;color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);&quot;&gt;任职资格：&lt;/span&gt;&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;&lt;span style=&quot;color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);&quot;&gt;1、编辑、新闻、中文等相关专业优先，大专及以上学历。&lt;/span&gt;&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;&lt;span style=&quot;color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);&quot;&gt;2、有编辑或从事相关工作经验优先。&lt;/span&gt;&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;&lt;span style=&quot;color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);&quot;&gt;3、熟练使用网页制作软件和网络搜索工具，了解网站运行、维护相关知识。&lt;/span&gt;&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;&lt;span style=&quot;color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);&quot;&gt;4、良好的文字功底，信息采编能力。&lt;/span&gt;&lt;br style=&quot;outline: 0px; text-size-adjust: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0.05); color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; white-space: normal; background-color: rgb(255, 255, 255);&quot;/&gt;&lt;span style=&quot;color: rgb(46, 52, 59); font-family: &amp;quot;Microsoft YaHei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, 微软雅黑, arial, Tahoma, SimSun, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);&quot;&gt;5、欢迎应届毕业生来应聘。&lt;/span&gt;&lt;/p&gt;');
INSERT INTO `ey_recruit_content` VALUES ('3', '94', '1565225547', '1565225547', '广州市', '专科以上学历', '5000-8000元', '全职', '2年以上', '若干', '&lt;p&gt;1、负责客户开发、提供客户服务、公司服务的推广、建立与维护客户关系；&lt;/p&gt;&lt;p&gt;2、根据市场营销计划和个人销售目标，完成各阶段销售目标；&lt;/p&gt;&lt;p&gt;3、进行市场调研，确定目标市场，收集分析竞争对象信息，制订、执行销售对策；&lt;/p&gt;&lt;p&gt;4、与内部相关部门建立并维持良好的协作关系，以客户和市场为导向，协调各方面关系，确保合同的顺利执行。&lt;/p&gt;&lt;p&gt;【岗位要求】&lt;/p&gt;&lt;p&gt;1、专科学历，有一定客户服务工作经验或销售经验，有一定的客户服务知识和能力。&lt;/p&gt;&lt;p&gt;2、计算机操作熟练，office办公软件使用熟练，有一定的网络基础知识，熟练使用Photoshop等制图工具着优先考虑。&lt;/p&gt;&lt;p&gt;3、要求一定要有“客户优先”的服务精神，一切从帮助客户、满足客户角度出发。&lt;/p&gt;&lt;p&gt;4、性格要求沉稳、隐忍，善于倾听，有同理心，乐观、积极。普通话标准、流利，反应灵敏。&lt;/p&gt;&lt;p&gt;5、热爱工作，敬业、勤恳、乐于思考，具有自我发展的主观的主题愿望和自我学习能力。可适当加班者优先。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;');
INSERT INTO `ey_recruit_content` VALUES ('4', '95', '1565225608', '1565225608', '广州市', '专科以上学历', '5000-10000元', '全职', '2年以上', '若干', '&lt;p&gt;岗位职责：1、利用网络进行公司产品的销售及推广2、了解网络销售，有信心和良好的学习能力3、完成团队目标4、通过网络进行渠道开发和业务拓展5、熟悉互联网络，熟练使用网络交流工具和各种办公软件6、有较强的沟通能力&lt;/p&gt;&lt;p&gt;任职要求：1、年龄18～25之间，有空杯心态者优先2、性格开朗，喜欢与人沟通，表达流畅，普通话标准3、具备一定的客户服务精神，准确把握客户需求4、认真细致，能接受挑战性任务5、有无销售经验均可，欢迎应届毕业生应聘&lt;/p&gt;&lt;p&gt;薪资待遇：底薪+高抽成+奖金+年底双薪+生日聚餐，不定期的聚会和旅游等&lt;/p&gt;&lt;p&gt;晋升空间：业务基础—精英业务员—销售组长—销售主管—核心主管&lt;/p&gt;&lt;p&gt;销售激励：礼品奖励、现金奖金、出单奖励、周业绩奖励、月销售前三奖励。依法享受法定节假日（带薪旅游、带薪年假、婚假。、产假、员工生日庆祝）&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;');
INSERT INTO `ey_recruit_content` VALUES ('5', '96', '1565225638', '1565225638', '广州市', '专科以上学历', '5000-10000元', '全职', '2年以上', '若干', '&lt;p&gt;任职要求： 1、年龄25-35岁，本科及以上学历，网络安全相关专业，持网络安全证书，2年以上同岗位工作经验； 2、熟知防火墙、入侵检测、网络流量识别控制等信息安全产品相关技术；熟悉网络协议、网络编程及相关网络产品开发技术； 3、具备良好的安全意识能力、语言表达能力、逻辑思维能力、执行力、责任心、团队协作能力。 岗位职责： 1、安全设备的日常监控与维护，及时进行安全设备策略的变更与调整；安全运维的结果文档、技术资料、设备日志，配置信息等相关资料的管理及维护； 2、定期关注安全设备系统的升级补丁，合适准确性后及时更新补丁，完成安全设备系统升级； 3、安全设备的健康检查、健康检查表、基线统计、流量统计、特殊事件报告、日报、周报等日常工作； 4、参与信息安全事件的分析工作和应急处理工作；定期安全评估、扫描和加固，协助进行安全加固； 5、协助制定网络安全规范与方案，优化现有网络安全架构，定期评估网络安全风险，负责针对运行故障、安全事件开展分析和总结，定期开展运行维护、故障处理及安全事件分析； 6、完成上级领导交办的其他任务，并做好与其他部门的协调配合工作。&lt;/p&gt;');
INSERT INTO `ey_recruit_content` VALUES ('6', '97', '1565225697', '1565225697', '广州市', '专科以上学历', '5000-8000元', '全职', '1年以上', '2位', '&lt;p&gt;岗位职责1、组织参与重要项目的创意构思、文案及客户提案, 给予前期提案、设计创意说明及后期结案报告等服务；2、执行并监督所负责项目的创意构思和文案；3、稿件思路清晰，能够完成稿件写作思路规划；4、协助领导进行创意提案，保证工作的顺利推进；5、独立撰写各类稿件（新闻稿、综述稿、评论稿、专访稿等）、策划方案、报告等任职资格1、大专及以上学历；2、一年以上市场策划及文案工作经验；3、能够准确捕捉产品亮点，具备恰如其分的文字表现能力；4、熟悉专业创意方法，思维敏捷，洞察力强，文字功底扎实，语言表达能力强；福利待遇：1.公司为员工提供以市场标杆，以能力、绩效为标准，具有市场竞争力的薪资待遇；2.为员工缴纳社会保险和办理住房公积金、带薪年假、法定节假日、小暑假；3.员工享有专业持续的提升培训机制、不定期的拓展福利、稳健的晋升机制；4.生日福利、传统节日福利、年终奖、年终分红等多种特色福利；试用期工资3500职能类别：网站&lt;/p&gt;');

-- -----------------------------
-- Table structure for `ey_region`
-- -----------------------------
DROP TABLE IF EXISTS `ey_region`;
CREATE TABLE `ey_region` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `name` varchar(32) DEFAULT '' COMMENT '地区名称',
  `level` tinyint(4) DEFAULT '0' COMMENT '地区等级 分省市县区',
  `parent_id` int(10) DEFAULT '0' COMMENT '父id',
  `initial` varchar(5) DEFAULT '' COMMENT '首字母',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`) USING BTREE,
  KEY `level` (`level`) USING BTREE,
  KEY `initial` (`initial`)
) ENGINE=MyISAM AUTO_INCREMENT=47964 DEFAULT CHARSET=utf8 COMMENT='区域表';

-- -----------------------------
-- Records of `ey_region`
-- -----------------------------
INSERT INTO `ey_region` VALUES ('1', '北京市', '1', '0', 'B');
INSERT INTO `ey_region` VALUES ('2', '北京市', '2', '1', 'B');
INSERT INTO `ey_region` VALUES ('3', '东城区', '3', '2', 'D');
INSERT INTO `ey_region` VALUES ('14', '西城区', '3', '2', 'X');
INSERT INTO `ey_region` VALUES ('22', '崇文区', '3', '2', 'C');
INSERT INTO `ey_region` VALUES ('30', '宣武区', '3', '2', 'X');
INSERT INTO `ey_region` VALUES ('39', '朝阳区', '3', '2', 'C');
INSERT INTO `ey_region` VALUES ('83', '丰台区', '3', '2', 'F');
INSERT INTO `ey_region` VALUES ('105', '石景山区', '3', '2', 'S');
INSERT INTO `ey_region` VALUES ('115', '海淀区', '3', '2', 'H');
INSERT INTO `ey_region` VALUES ('145', '门头沟区', '3', '2', 'M');
INSERT INTO `ey_region` VALUES ('159', '房山区', '3', '2', 'F');
INSERT INTO `ey_region` VALUES ('188', '通州区', '3', '2', 'T');
INSERT INTO `ey_region` VALUES ('204', '顺义区', '3', '2', 'S');
INSERT INTO `ey_region` VALUES ('227', '昌平区', '3', '2', 'C');
INSERT INTO `ey_region` VALUES ('245', '大兴区', '3', '2', 'D');
INSERT INTO `ey_region` VALUES ('264', '怀柔区', '3', '2', 'H');
INSERT INTO `ey_region` VALUES ('281', '平谷区', '3', '2', 'P');
INSERT INTO `ey_region` VALUES ('301', '密云区', '3', '2', 'M');
INSERT INTO `ey_region` VALUES ('322', '延庆区', '3', '2', 'Y');
INSERT INTO `ey_region` VALUES ('338', '天津市', '1', '0', 'T');
INSERT INTO `ey_region` VALUES ('339', '天津市', '2', '338', 'T');
INSERT INTO `ey_region` VALUES ('340', '和平区', '3', '339', 'H');
INSERT INTO `ey_region` VALUES ('347', '河东区', '3', '339', 'H');
INSERT INTO `ey_region` VALUES ('361', '河西区', '3', '339', 'H');
INSERT INTO `ey_region` VALUES ('375', '南开区', '3', '339', 'N');
INSERT INTO `ey_region` VALUES ('388', '河北区', '3', '339', 'H');
INSERT INTO `ey_region` VALUES ('399', '红桥区', '3', '339', 'H');
INSERT INTO `ey_region` VALUES ('410', '塘沽区', '3', '339', 'T');
INSERT INTO `ey_region` VALUES ('425', '汉沽区', '3', '339', 'H');
INSERT INTO `ey_region` VALUES ('435', '大港区', '3', '339', 'D');
INSERT INTO `ey_region` VALUES ('445', '东丽区', '3', '339', 'D');
INSERT INTO `ey_region` VALUES ('460', '西青区', '3', '339', 'X');
INSERT INTO `ey_region` VALUES ('473', '津南区', '3', '339', 'J');
INSERT INTO `ey_region` VALUES ('488', '北辰区', '3', '339', 'B');
INSERT INTO `ey_region` VALUES ('504', '武清区', '3', '339', 'W');
INSERT INTO `ey_region` VALUES ('538', '宝坻区', '3', '339', 'B');
INSERT INTO `ey_region` VALUES ('570', '宁河区', '3', '339', 'N');
INSERT INTO `ey_region` VALUES ('586', '静海区', '3', '339', 'J');
INSERT INTO `ey_region` VALUES ('608', '蓟州区', '3', '339', 'J');
INSERT INTO `ey_region` VALUES ('636', '河北省', '1', '0', 'H');
INSERT INTO `ey_region` VALUES ('637', '石家庄市', '2', '636', 'S');
INSERT INTO `ey_region` VALUES ('638', '市辖区', '3', '637', 'S');
INSERT INTO `ey_region` VALUES ('639', '长安区', '3', '637', 'C');
INSERT INTO `ey_region` VALUES ('651', '桥东区', '3', '637', 'Q');
INSERT INTO `ey_region` VALUES ('662', '桥西区', '3', '637', 'Q');
INSERT INTO `ey_region` VALUES ('675', '新华区', '3', '637', 'X');
INSERT INTO `ey_region` VALUES ('691', '井陉矿区', '3', '637', 'J');
INSERT INTO `ey_region` VALUES ('697', '裕华区', '3', '637', 'Y');
INSERT INTO `ey_region` VALUES ('708', '井陉县', '3', '637', 'J');
INSERT INTO `ey_region` VALUES ('726', '正定县', '3', '637', 'Z');
INSERT INTO `ey_region` VALUES ('736', '栾城县', '3', '637', 'L');
INSERT INTO `ey_region` VALUES ('745', '行唐县', '3', '637', 'X');
INSERT INTO `ey_region` VALUES ('761', '灵寿县', '3', '637', 'L');
INSERT INTO `ey_region` VALUES ('777', '高邑县', '3', '637', 'G');
INSERT INTO `ey_region` VALUES ('783', '深泽县', '3', '637', 'S');
INSERT INTO `ey_region` VALUES ('790', '赞皇县', '3', '637', 'Z');
INSERT INTO `ey_region` VALUES ('802', '无极县', '3', '637', 'W');
INSERT INTO `ey_region` VALUES ('814', '平山县', '3', '637', 'P');
INSERT INTO `ey_region` VALUES ('838', '元氏县', '3', '637', 'Y');
INSERT INTO `ey_region` VALUES ('854', '赵县', '3', '637', 'Z');
INSERT INTO `ey_region` VALUES ('866', '辛集市', '3', '637', 'X');
INSERT INTO `ey_region` VALUES ('882', '藁城市', '3', '637', 'G');
INSERT INTO `ey_region` VALUES ('898', '晋州市', '3', '637', 'J');
INSERT INTO `ey_region` VALUES ('909', '新乐市', '3', '637', 'X');
INSERT INTO `ey_region` VALUES ('922', '鹿泉市', '3', '637', 'L');
INSERT INTO `ey_region` VALUES ('936', '唐山市', '2', '636', 'T');
INSERT INTO `ey_region` VALUES ('937', '市辖区', '3', '936', 'S');
INSERT INTO `ey_region` VALUES ('938', '路南区', '3', '936', 'L');
INSERT INTO `ey_region` VALUES ('952', '路北区', '3', '936', 'L');
INSERT INTO `ey_region` VALUES ('965', '古冶区', '3', '936', 'G');
INSERT INTO `ey_region` VALUES ('977', '开平区', '3', '936', 'K');
INSERT INTO `ey_region` VALUES ('989', '丰南区', '3', '936', 'F');
INSERT INTO `ey_region` VALUES ('1007', '丰润区', '3', '936', 'F');
INSERT INTO `ey_region` VALUES ('1034', '滦县', '3', '936', 'L');
INSERT INTO `ey_region` VALUES ('1048', '滦南县', '3', '936', 'L');
INSERT INTO `ey_region` VALUES ('1067', '乐亭县', '3', '936', 'L');
INSERT INTO `ey_region` VALUES ('1085', '迁西县', '3', '936', 'Q');
INSERT INTO `ey_region` VALUES ('1104', '玉田县', '3', '936', 'Y');
INSERT INTO `ey_region` VALUES ('1125', '唐海县', '3', '936', 'T');
INSERT INTO `ey_region` VALUES ('1140', '遵化市', '3', '936', 'Z');
INSERT INTO `ey_region` VALUES ('1168', '迁安市', '3', '936', 'Q');
INSERT INTO `ey_region` VALUES ('1188', '秦皇岛市', '2', '636', 'Q');
INSERT INTO `ey_region` VALUES ('1189', '市辖区', '3', '1188', 'S');
INSERT INTO `ey_region` VALUES ('1190', '海港区', '3', '1188', 'H');
INSERT INTO `ey_region` VALUES ('1208', '山海关区', '3', '1188', 'S');
INSERT INTO `ey_region` VALUES ('1218', '北戴河区', '3', '1188', 'B');
INSERT INTO `ey_region` VALUES ('1223', '青龙县', '3', '1188', 'Q');
INSERT INTO `ey_region` VALUES ('1249', '昌黎县', '3', '1188', 'C');
INSERT INTO `ey_region` VALUES ('1266', '抚宁县', '3', '1188', 'F');
INSERT INTO `ey_region` VALUES ('1278', '卢龙县', '3', '1188', 'L');
INSERT INTO `ey_region` VALUES ('1291', '邯郸市', '2', '636', 'H');
INSERT INTO `ey_region` VALUES ('1292', '市辖区', '3', '1291', 'S');
INSERT INTO `ey_region` VALUES ('1293', '邯山区', '3', '1291', 'H');
INSERT INTO `ey_region` VALUES ('1307', '丛台区', '3', '1291', 'C');
INSERT INTO `ey_region` VALUES ('1319', '复兴区', '3', '1291', 'F');
INSERT INTO `ey_region` VALUES ('1329', '峰峰矿区', '3', '1291', 'F');
INSERT INTO `ey_region` VALUES ('1339', '邯郸县', '3', '1291', 'H');
INSERT INTO `ey_region` VALUES ('1350', '临漳县', '3', '1291', 'L');
INSERT INTO `ey_region` VALUES ('1365', '成安县', '3', '1291', 'C');
INSERT INTO `ey_region` VALUES ('1375', '大名县', '3', '1291', 'D');
INSERT INTO `ey_region` VALUES ('1396', '涉县', '3', '1291', 'S');
INSERT INTO `ey_region` VALUES ('1414', '磁县', '3', '1291', 'C');
INSERT INTO `ey_region` VALUES ('1434', '肥乡县', '3', '1291', 'F');
INSERT INTO `ey_region` VALUES ('1444', '永年县', '3', '1291', 'Y');
INSERT INTO `ey_region` VALUES ('1465', '邱县', '3', '1291', 'Q');
INSERT INTO `ey_region` VALUES ('1473', '鸡泽县', '3', '1291', 'J');
INSERT INTO `ey_region` VALUES ('1481', '广平县', '3', '1291', 'G');
INSERT INTO `ey_region` VALUES ('1489', '馆陶县', '3', '1291', 'G');
INSERT INTO `ey_region` VALUES ('1498', '魏县', '3', '1291', 'W');
INSERT INTO `ey_region` VALUES ('1520', '曲周县', '3', '1291', 'Q');
INSERT INTO `ey_region` VALUES ('1531', '武安市', '3', '1291', 'W');
INSERT INTO `ey_region` VALUES ('1554', '邢台市', '2', '636', 'X');
INSERT INTO `ey_region` VALUES ('1555', '市辖区', '3', '1554', 'S');
INSERT INTO `ey_region` VALUES ('1556', '桥东区', '3', '1554', 'Q');
INSERT INTO `ey_region` VALUES ('1564', '桥西区', '3', '1554', 'Q');
INSERT INTO `ey_region` VALUES ('1572', '邢台县', '3', '1554', 'X');
INSERT INTO `ey_region` VALUES ('1593', '临城县', '3', '1554', 'L');
INSERT INTO `ey_region` VALUES ('1602', '内邱县', '3', '1554', 'N');
INSERT INTO `ey_region` VALUES ('1612', '柏乡县', '3', '1554', 'B');
INSERT INTO `ey_region` VALUES ('1619', '隆尧县', '3', '1554', 'L');
INSERT INTO `ey_region` VALUES ('1633', '任县', '3', '1554', 'R');
INSERT INTO `ey_region` VALUES ('1642', '南和县', '3', '1554', 'N');
INSERT INTO `ey_region` VALUES ('1651', '宁晋县', '3', '1554', 'N');
INSERT INTO `ey_region` VALUES ('1669', '巨鹿县', '3', '1554', 'J');
INSERT INTO `ey_region` VALUES ('1680', '新河县', '3', '1554', 'X');
INSERT INTO `ey_region` VALUES ('1687', '广宗县', '3', '1554', 'G');
INSERT INTO `ey_region` VALUES ('1696', '平乡县', '3', '1554', 'P');
INSERT INTO `ey_region` VALUES ('1704', '威县', '3', '1554', 'W');
INSERT INTO `ey_region` VALUES ('1721', '清河县', '3', '1554', 'Q');
INSERT INTO `ey_region` VALUES ('1728', '临西县', '3', '1554', 'L');
INSERT INTO `ey_region` VALUES ('1738', '南宫市', '3', '1554', 'N');
INSERT INTO `ey_region` VALUES ('1754', '沙河市', '3', '1554', 'S');
INSERT INTO `ey_region` VALUES ('1772', '保定市', '2', '636', 'B');
INSERT INTO `ey_region` VALUES ('1773', '市辖区', '3', '1772', 'S');
INSERT INTO `ey_region` VALUES ('1774', '新市区', '3', '1772', 'X');
INSERT INTO `ey_region` VALUES ('1787', '北市区', '3', '1772', 'B');
INSERT INTO `ey_region` VALUES ('1796', '南市区', '3', '1772', 'N');
INSERT INTO `ey_region` VALUES ('1806', '满城区', '3', '1772', 'M');
INSERT INTO `ey_region` VALUES ('1820', '清苑区', '3', '1772', 'Q');
INSERT INTO `ey_region` VALUES ('1839', '涞水县', '3', '1772', 'L');
INSERT INTO `ey_region` VALUES ('1856', '阜平县', '3', '1772', 'F');
INSERT INTO `ey_region` VALUES ('1870', '徐水区', '3', '1772', 'X');
INSERT INTO `ey_region` VALUES ('1885', '定兴县', '3', '1772', 'D');
INSERT INTO `ey_region` VALUES ('1902', '唐县', '3', '1772', 'T');
INSERT INTO `ey_region` VALUES ('1923', '高阳县', '3', '1772', 'G');
INSERT INTO `ey_region` VALUES ('1933', '容城县', '3', '1772', 'R');
INSERT INTO `ey_region` VALUES ('1942', '涞源县', '3', '1772', 'L');
INSERT INTO `ey_region` VALUES ('1960', '望都县', '3', '1772', 'W');
INSERT INTO `ey_region` VALUES ('1969', '安新县', '3', '1772', 'A');
INSERT INTO `ey_region` VALUES ('1982', '易县', '3', '1772', 'Y');
INSERT INTO `ey_region` VALUES ('2010', '曲阳县', '3', '1772', 'Q');
INSERT INTO `ey_region` VALUES ('2029', '蠡县', '3', '1772', 'L');
INSERT INTO `ey_region` VALUES ('2043', '顺平县', '3', '1772', 'S');
INSERT INTO `ey_region` VALUES ('2054', '博野县', '3', '1772', 'B');
INSERT INTO `ey_region` VALUES ('2062', '雄县', '3', '1772', 'X');
INSERT INTO `ey_region` VALUES ('2072', '涿州市', '3', '1772', 'Z');
INSERT INTO `ey_region` VALUES ('2088', '定州市', '3', '1772', 'D');
INSERT INTO `ey_region` VALUES ('2114', '安国市', '3', '1772', 'A');
INSERT INTO `ey_region` VALUES ('2126', '高碑店市', '3', '1772', 'G');
INSERT INTO `ey_region` VALUES ('2142', '张家口市', '2', '636', 'Z');
INSERT INTO `ey_region` VALUES ('2143', '市辖区', '3', '2142', 'S');
INSERT INTO `ey_region` VALUES ('2144', '桥东区', '3', '2142', 'Q');
INSERT INTO `ey_region` VALUES ('2154', '桥西区', '3', '2142', 'Q');
INSERT INTO `ey_region` VALUES ('2164', '宣化区', '3', '2142', 'X');
INSERT INTO `ey_region` VALUES ('2176', '下花园区', '3', '2142', 'X');
INSERT INTO `ey_region` VALUES ('2183', '宣化县', '3', '2142', 'X');
INSERT INTO `ey_region` VALUES ('2198', '张北县', '3', '2142', 'Z');
INSERT INTO `ey_region` VALUES ('2220', '康保县', '3', '2142', 'K');
INSERT INTO `ey_region` VALUES ('2237', '沽源县', '3', '2142', 'G');
INSERT INTO `ey_region` VALUES ('2256', '尚义县', '3', '2142', 'S');
INSERT INTO `ey_region` VALUES ('2271', '蔚县', '3', '2142', 'W');
INSERT INTO `ey_region` VALUES ('2294', '阳原县', '3', '2142', 'Y');
INSERT INTO `ey_region` VALUES ('2309', '怀安县', '3', '2142', 'H');
INSERT INTO `ey_region` VALUES ('2321', '万全县', '3', '2142', 'W');
INSERT INTO `ey_region` VALUES ('2333', '怀来县', '3', '2142', 'H');
INSERT INTO `ey_region` VALUES ('2351', '涿鹿县', '3', '2142', 'Z');
INSERT INTO `ey_region` VALUES ('2369', '赤城县', '3', '2142', 'C');
INSERT INTO `ey_region` VALUES ('2388', '崇礼县', '3', '2142', 'C');
INSERT INTO `ey_region` VALUES ('2400', '承德市', '2', '636', 'C');
INSERT INTO `ey_region` VALUES ('2401', '市辖区', '3', '2400', 'S');
INSERT INTO `ey_region` VALUES ('2402', '双桥区', '3', '2400', 'S');
INSERT INTO `ey_region` VALUES ('2415', '双滦区', '3', '2400', 'S');
INSERT INTO `ey_region` VALUES ('2422', '鹰手营子矿区', '3', '2400', 'Y');
INSERT INTO `ey_region` VALUES ('2427', '承德县', '3', '2400', 'C');
INSERT INTO `ey_region` VALUES ('2453', '兴隆县', '3', '2400', 'X');
INSERT INTO `ey_region` VALUES ('2474', '平泉县', '3', '2400', 'P');
INSERT INTO `ey_region` VALUES ('2494', '滦平县', '3', '2400', 'L');
INSERT INTO `ey_region` VALUES ('2517', '隆化县', '3', '2400', 'L');
INSERT INTO `ey_region` VALUES ('2543', '丰宁县', '3', '2400', 'F');
INSERT INTO `ey_region` VALUES ('2570', '宽城县', '3', '2400', 'K');
INSERT INTO `ey_region` VALUES ('2589', '围场县', '3', '2400', 'W');
INSERT INTO `ey_region` VALUES ('2629', '沧州市', '2', '636', 'C');
INSERT INTO `ey_region` VALUES ('2630', '市辖区', '3', '2629', 'S');
INSERT INTO `ey_region` VALUES ('2631', '新华区', '3', '2629', 'X');
INSERT INTO `ey_region` VALUES ('2639', '运河区', '3', '2629', 'Y');
INSERT INTO `ey_region` VALUES ('2648', '沧县', '3', '2629', 'C');
INSERT INTO `ey_region` VALUES ('2668', '青县', '3', '2629', 'Q');
INSERT INTO `ey_region` VALUES ('2680', '东光县', '3', '2629', 'D');
INSERT INTO `ey_region` VALUES ('2690', '海兴县', '3', '2629', 'H');
INSERT INTO `ey_region` VALUES ('2701', '盐山县', '3', '2629', 'Y');
INSERT INTO `ey_region` VALUES ('2714', '肃宁县', '3', '2629', 'S');
INSERT INTO `ey_region` VALUES ('2724', '南皮县', '3', '2629', 'N');
INSERT INTO `ey_region` VALUES ('2734', '吴桥县', '3', '2629', 'W');
INSERT INTO `ey_region` VALUES ('2745', '献县', '3', '2629', 'X');
INSERT INTO `ey_region` VALUES ('2765', '孟村县', '3', '2629', 'M');
INSERT INTO `ey_region` VALUES ('2772', '泊头市', '3', '2629', 'B');
INSERT INTO `ey_region` VALUES ('2788', '任邱市', '3', '2629', 'R');
INSERT INTO `ey_region` VALUES ('2809', '黄骅市', '3', '2629', 'H');
INSERT INTO `ey_region` VALUES ('2828', '河间市', '3', '2629', 'H');
INSERT INTO `ey_region` VALUES ('2849', '廊坊市', '2', '636', 'L');
INSERT INTO `ey_region` VALUES ('2850', '市辖区', '3', '2849', 'S');
INSERT INTO `ey_region` VALUES ('2851', '安次区', '3', '2849', 'A');
INSERT INTO `ey_region` VALUES ('2862', '广阳区', '3', '2849', 'G');
INSERT INTO `ey_region` VALUES ('2873', '固安县', '3', '2849', 'G');
INSERT INTO `ey_region` VALUES ('2883', '永清县', '3', '2849', 'Y');
INSERT INTO `ey_region` VALUES ('2895', '香河县', '3', '2849', 'X');
INSERT INTO `ey_region` VALUES ('2906', '大城县', '3', '2849', 'D');
INSERT INTO `ey_region` VALUES ('2918', '文安县', '3', '2849', 'W');
INSERT INTO `ey_region` VALUES ('2932', '大厂县', '3', '2849', 'D');
INSERT INTO `ey_region` VALUES ('2939', '霸州市', '3', '2849', 'B');
INSERT INTO `ey_region` VALUES ('2953', '三河市', '3', '2849', 'S');
INSERT INTO `ey_region` VALUES ('2968', '衡水市', '2', '636', 'H');
INSERT INTO `ey_region` VALUES ('2969', '市辖区', '3', '2968', 'S');
INSERT INTO `ey_region` VALUES ('2970', '桃城区', '3', '2968', 'T');
INSERT INTO `ey_region` VALUES ('2983', '枣强县', '3', '2968', 'Z');
INSERT INTO `ey_region` VALUES ('2995', '武邑县', '3', '2968', 'W');
INSERT INTO `ey_region` VALUES ('3005', '武强县', '3', '2968', 'W');
INSERT INTO `ey_region` VALUES ('3012', '饶阳县', '3', '2968', 'R');
INSERT INTO `ey_region` VALUES ('3020', '安平县', '3', '2968', 'A');
INSERT INTO `ey_region` VALUES ('3029', '故城县', '3', '2968', 'G');
INSERT INTO `ey_region` VALUES ('3043', '景县', '3', '2968', 'J');
INSERT INTO `ey_region` VALUES ('3060', '阜城县', '3', '2968', 'F');
INSERT INTO `ey_region` VALUES ('3071', '冀州市', '3', '2968', 'J');
INSERT INTO `ey_region` VALUES ('3083', '深州市', '3', '2968', 'S');
INSERT INTO `ey_region` VALUES ('3102', '山西', '1', '0', 'S');
INSERT INTO `ey_region` VALUES ('3103', '太原市', '2', '3102', 'T');
INSERT INTO `ey_region` VALUES ('3104', '市辖区', '3', '3103', 'S');
INSERT INTO `ey_region` VALUES ('3105', '小店区(人口含高新经济区)', '3', '3103', 'X');
INSERT INTO `ey_region` VALUES ('3117', '迎泽区', '3', '3103', 'Y');
INSERT INTO `ey_region` VALUES ('3126', '杏花岭区', '3', '3103', 'X');
INSERT INTO `ey_region` VALUES ('3140', '尖草坪区', '3', '3103', 'J');
INSERT INTO `ey_region` VALUES ('3155', '万柏林区', '3', '3103', 'W');
INSERT INTO `ey_region` VALUES ('3171', '晋源区', '3', '3103', 'J');
INSERT INTO `ey_region` VALUES ('3178', '清徐县', '3', '3103', 'Q');
INSERT INTO `ey_region` VALUES ('3188', '阳曲县', '3', '3103', 'Y');
INSERT INTO `ey_region` VALUES ('3200', '娄烦县', '3', '3103', 'L');
INSERT INTO `ey_region` VALUES ('3209', '古交市', '3', '3103', 'G');
INSERT INTO `ey_region` VALUES ('3224', '大同市', '2', '3102', 'D');
INSERT INTO `ey_region` VALUES ('3225', '市辖区', '3', '3224', 'S');
INSERT INTO `ey_region` VALUES ('3226', '大同市城区', '3', '3224', 'D');
INSERT INTO `ey_region` VALUES ('3241', '矿区', '3', '3224', 'K');
INSERT INTO `ey_region` VALUES ('3266', '南郊区', '3', '3224', 'N');
INSERT INTO `ey_region` VALUES ('3277', '新荣区', '3', '3224', 'X');
INSERT INTO `ey_region` VALUES ('3286', '阳高县', '3', '3224', 'Y');
INSERT INTO `ey_region` VALUES ('3300', '天镇县', '3', '3224', 'T');
INSERT INTO `ey_region` VALUES ('3312', '广灵县', '3', '3224', 'G');
INSERT INTO `ey_region` VALUES ('3322', '灵丘县', '3', '3224', 'L');
INSERT INTO `ey_region` VALUES ('3335', '浑源县', '3', '3224', 'H');
INSERT INTO `ey_region` VALUES ('3354', '左云县', '3', '3224', 'Z');
INSERT INTO `ey_region` VALUES ('3364', '大同县', '3', '3224', 'D');
INSERT INTO `ey_region` VALUES ('3379', '阳泉市', '2', '3102', 'Y');
INSERT INTO `ey_region` VALUES ('3380', '市辖区', '3', '3379', 'S');
INSERT INTO `ey_region` VALUES ('3381', '城区', '3', '3379', 'C');
INSERT INTO `ey_region` VALUES ('3388', '矿区', '3', '3379', 'K');
INSERT INTO `ey_region` VALUES ('3395', '郊区', '3', '3379', 'J');
INSERT INTO `ey_region` VALUES ('3405', '平定县', '3', '3379', 'P');
INSERT INTO `ey_region` VALUES ('3416', '盂县', '3', '3379', 'Y');
INSERT INTO `ey_region` VALUES ('3431', '长治市', '2', '3102', 'C');
INSERT INTO `ey_region` VALUES ('3432', '市辖区', '3', '3431', 'S');
INSERT INTO `ey_region` VALUES ('3433', '长治市城区', '3', '3431', 'C');
INSERT INTO `ey_region` VALUES ('3445', '长治市郊区', '3', '3431', 'C');
INSERT INTO `ey_region` VALUES ('3454', '长治县', '3', '3431', 'C');
INSERT INTO `ey_region` VALUES ('3466', '襄垣县', '3', '3431', 'X');
INSERT INTO `ey_region` VALUES ('3478', '屯留县', '3', '3431', 'T');
INSERT INTO `ey_region` VALUES ('3493', '平顺县', '3', '3431', 'P');
INSERT INTO `ey_region` VALUES ('3506', '黎城县', '3', '3431', 'L');
INSERT INTO `ey_region` VALUES ('3516', '壶关县', '3', '3431', 'H');
INSERT INTO `ey_region` VALUES ('3530', '长子县', '3', '3431', 'C');
INSERT INTO `ey_region` VALUES ('3543', '武乡县', '3', '3431', 'W');
INSERT INTO `ey_region` VALUES ('3558', '沁县', '3', '3431', 'Q');
INSERT INTO `ey_region` VALUES ('3572', '沁源县', '3', '3431', 'Q');
INSERT INTO `ey_region` VALUES ('3587', '潞城市', '3', '3431', 'L');
INSERT INTO `ey_region` VALUES ('3597', '晋城市', '2', '3102', 'J');
INSERT INTO `ey_region` VALUES ('3598', '市辖区', '3', '3597', 'S');
INSERT INTO `ey_region` VALUES ('3599', '晋城市城区', '3', '3597', 'J');
INSERT INTO `ey_region` VALUES ('3608', '沁水县', '3', '3597', 'Q');
INSERT INTO `ey_region` VALUES ('3623', '阳城县', '3', '3597', 'Y');
INSERT INTO `ey_region` VALUES ('3642', '陵川县', '3', '3597', 'L');
INSERT INTO `ey_region` VALUES ('3655', '泽州县', '3', '3597', 'Z');
INSERT INTO `ey_region` VALUES ('3673', '高平市', '3', '3597', 'G');
INSERT INTO `ey_region` VALUES ('3690', '朔州市', '2', '3102', 'S');
INSERT INTO `ey_region` VALUES ('3691', '市辖区', '3', '3690', 'S');
INSERT INTO `ey_region` VALUES ('3692', '朔城区', '3', '3690', 'S');
INSERT INTO `ey_region` VALUES ('3709', '平鲁区', '3', '3690', 'P');
INSERT INTO `ey_region` VALUES ('3723', '山阴县', '3', '3690', 'S');
INSERT INTO `ey_region` VALUES ('3739', '应县', '3', '3690', 'Y');
INSERT INTO `ey_region` VALUES ('3752', '右玉县', '3', '3690', 'Y');
INSERT INTO `ey_region` VALUES ('3763', '怀仁县', '3', '3690', 'H');
INSERT INTO `ey_region` VALUES ('3776', '晋中市', '2', '3102', 'J');
INSERT INTO `ey_region` VALUES ('3777', '市辖区', '3', '3776', 'S');
INSERT INTO `ey_region` VALUES ('3778', '榆次区', '3', '3776', 'Y');
INSERT INTO `ey_region` VALUES ('3799', '榆社县', '3', '3776', 'Y');
INSERT INTO `ey_region` VALUES ('3810', '左权县', '3', '3776', 'Z');
INSERT INTO `ey_region` VALUES ('3822', '和顺县', '3', '3776', 'H');
INSERT INTO `ey_region` VALUES ('3833', '昔阳县', '3', '3776', 'X');
INSERT INTO `ey_region` VALUES ('3846', '寿阳县', '3', '3776', 'S');
INSERT INTO `ey_region` VALUES ('3861', '太谷县', '3', '3776', 'T');
INSERT INTO `ey_region` VALUES ('3871', '祁县', '3', '3776', 'Q');
INSERT INTO `ey_region` VALUES ('3880', '平遥县', '3', '3776', 'P');
INSERT INTO `ey_region` VALUES ('3895', '灵石县', '3', '3776', 'L');
INSERT INTO `ey_region` VALUES ('3908', '介休市', '3', '3776', 'J');
INSERT INTO `ey_region` VALUES ('3925', '运城市', '2', '3102', 'Y');
INSERT INTO `ey_region` VALUES ('3926', '市辖区', '3', '3925', 'S');
INSERT INTO `ey_region` VALUES ('3927', '盐湖区', '3', '3925', 'Y');
INSERT INTO `ey_region` VALUES ('3950', '临猗县', '3', '3925', 'L');
INSERT INTO `ey_region` VALUES ('3967', '万荣县', '3', '3925', 'W');
INSERT INTO `ey_region` VALUES ('3982', '闻喜县', '3', '3925', 'W');
INSERT INTO `ey_region` VALUES ('3996', '稷山县', '3', '3925', 'J');
INSERT INTO `ey_region` VALUES ('4004', '新绛县', '3', '3925', 'X');
INSERT INTO `ey_region` VALUES ('4013', '绛县', '3', '3925', 'J');
INSERT INTO `ey_region` VALUES ('4024', '垣曲县', '3', '3925', 'Y');
INSERT INTO `ey_region` VALUES ('4036', '夏县', '3', '3925', 'X');
INSERT INTO `ey_region` VALUES ('4048', '平陆县', '3', '3925', 'P');
INSERT INTO `ey_region` VALUES ('4059', '芮城县', '3', '3925', 'R');
INSERT INTO `ey_region` VALUES ('4070', '永济市', '3', '3925', 'Y');
INSERT INTO `ey_region` VALUES ('4082', '河津市', '3', '3925', 'H');
INSERT INTO `ey_region` VALUES ('4093', '忻州市', '2', '3102', 'X');
INSERT INTO `ey_region` VALUES ('4094', '市辖区', '3', '4093', 'S');
INSERT INTO `ey_region` VALUES ('4095', '忻府区', '3', '4093', 'X');
INSERT INTO `ey_region` VALUES ('4116', '定襄县', '3', '4093', 'D');
INSERT INTO `ey_region` VALUES ('4126', '五台县', '3', '4093', 'W');
INSERT INTO `ey_region` VALUES ('4146', '代县', '3', '4093', 'D');
INSERT INTO `ey_region` VALUES ('4158', '繁峙县', '3', '4093', 'F');
INSERT INTO `ey_region` VALUES ('4172', '宁武县', '3', '4093', 'N');
INSERT INTO `ey_region` VALUES ('4189', '静乐县', '3', '4093', 'J');
INSERT INTO `ey_region` VALUES ('4204', '神池县', '3', '4093', 'S');
INSERT INTO `ey_region` VALUES ('4215', '五寨县', '3', '4093', 'W');
INSERT INTO `ey_region` VALUES ('4228', '岢岚县', '3', '4093', 'K');
INSERT INTO `ey_region` VALUES ('4241', '河曲县', '3', '4093', 'H');
INSERT INTO `ey_region` VALUES ('4255', '保德县', '3', '4093', 'B');
INSERT INTO `ey_region` VALUES ('4269', '偏关县', '3', '4093', 'P');
INSERT INTO `ey_region` VALUES ('4280', '原平市', '3', '4093', 'Y');
INSERT INTO `ey_region` VALUES ('4304', '临汾市', '2', '3102', 'L');
INSERT INTO `ey_region` VALUES ('4305', '市辖区', '3', '4304', 'S');
INSERT INTO `ey_region` VALUES ('4306', '尧都区', '3', '4304', 'Y');
INSERT INTO `ey_region` VALUES ('4333', '曲沃县', '3', '4304', 'Q');
INSERT INTO `ey_region` VALUES ('4341', '翼城县', '3', '4304', 'Y');
INSERT INTO `ey_region` VALUES ('4352', '襄汾县', '3', '4304', 'X');
INSERT INTO `ey_region` VALUES ('4366', '洪洞县', '3', '4304', 'H');
INSERT INTO `ey_region` VALUES ('4383', '古县', '3', '4304', 'G');
INSERT INTO `ey_region` VALUES ('4391', '安泽县', '3', '4304', 'A');
INSERT INTO `ey_region` VALUES ('4399', '浮山县', '3', '4304', 'F');
INSERT INTO `ey_region` VALUES ('4409', '吉县', '3', '4304', 'J');
INSERT INTO `ey_region` VALUES ('4418', '乡宁县', '3', '4304', 'X');
INSERT INTO `ey_region` VALUES ('4429', '大宁县', '3', '4304', 'D');
INSERT INTO `ey_region` VALUES ('4436', '隰县', '3', '4304', 'X');
INSERT INTO `ey_region` VALUES ('4445', '永和县', '3', '4304', 'Y');
INSERT INTO `ey_region` VALUES ('4453', '蒲县', '3', '4304', 'P');
INSERT INTO `ey_region` VALUES ('4463', '汾西县', '3', '4304', 'F');
INSERT INTO `ey_region` VALUES ('4472', '侯马市', '3', '4304', 'H');
INSERT INTO `ey_region` VALUES ('4481', '霍州市', '3', '4304', 'H');
INSERT INTO `ey_region` VALUES ('4494', '吕梁市', '2', '3102', 'L');
INSERT INTO `ey_region` VALUES ('4495', '市辖区', '3', '4494', 'S');
INSERT INTO `ey_region` VALUES ('4496', '离石区', '3', '4494', 'L');
INSERT INTO `ey_region` VALUES ('4509', '文水县', '3', '4494', 'W');
INSERT INTO `ey_region` VALUES ('4522', '交城县', '3', '4494', 'J');
INSERT INTO `ey_region` VALUES ('4533', '兴县', '3', '4494', 'X');
INSERT INTO `ey_region` VALUES ('4551', '临县', '3', '4494', 'L');
INSERT INTO `ey_region` VALUES ('4575', '柳林县', '3', '4494', 'L');
INSERT INTO `ey_region` VALUES ('4591', '石楼县', '3', '4494', 'S');
INSERT INTO `ey_region` VALUES ('4601', '岚县', '3', '4494', 'L');
INSERT INTO `ey_region` VALUES ('4614', '方山县', '3', '4494', 'F');
INSERT INTO `ey_region` VALUES ('4622', '中阳县', '3', '4494', 'Z');
INSERT INTO `ey_region` VALUES ('4630', '交口县', '3', '4494', 'J');
INSERT INTO `ey_region` VALUES ('4638', '孝义市', '3', '4494', 'X');
INSERT INTO `ey_region` VALUES ('4655', '汾阳市', '3', '4494', 'F');
INSERT INTO `ey_region` VALUES ('4670', '内蒙古', '1', '0', 'N');
INSERT INTO `ey_region` VALUES ('4671', '呼和浩特市', '2', '4670', 'H');
INSERT INTO `ey_region` VALUES ('4672', '市辖区', '3', '4671', 'S');
INSERT INTO `ey_region` VALUES ('4673', '新城区', '3', '4671', 'X');
INSERT INTO `ey_region` VALUES ('4684', '回民区', '3', '4671', 'H');
INSERT INTO `ey_region` VALUES ('4693', '玉泉区', '3', '4671', 'Y');
INSERT INTO `ey_region` VALUES ('4702', '赛罕区', '3', '4671', 'S');
INSERT INTO `ey_region` VALUES ('4715', '土左旗', '3', '4671', 'T');
INSERT INTO `ey_region` VALUES ('4727', '托克托县', '3', '4671', 'T');
INSERT INTO `ey_region` VALUES ('4733', '和林格尔县', '3', '4671', 'H');
INSERT INTO `ey_region` VALUES ('4742', '清水河县', '3', '4671', 'Q');
INSERT INTO `ey_region` VALUES ('4749', '武川县', '3', '4671', 'W');
INSERT INTO `ey_region` VALUES ('4759', '包头市', '2', '4670', 'B');
INSERT INTO `ey_region` VALUES ('4760', '市辖区', '3', '4759', 'S');
INSERT INTO `ey_region` VALUES ('4761', '东河区', '3', '4759', 'D');
INSERT INTO `ey_region` VALUES ('4775', '昆都仑区', '3', '4759', 'K');
INSERT INTO `ey_region` VALUES ('4791', '青山区', '3', '4759', 'Q');
INSERT INTO `ey_region` VALUES ('4803', '石拐区', '3', '4759', 'S');
INSERT INTO `ey_region` VALUES ('4810', '白云鄂博矿区', '3', '4759', 'B');
INSERT INTO `ey_region` VALUES ('4813', '九原区', '3', '4759', 'J');
INSERT INTO `ey_region` VALUES ('4823', '土默特右旗', '3', '4759', 'T');
INSERT INTO `ey_region` VALUES ('4833', '固阳县', '3', '4759', 'G');
INSERT INTO `ey_region` VALUES ('4840', '达茂联合旗', '3', '4759', 'D');
INSERT INTO `ey_region` VALUES ('4849', '乌海市', '2', '4670', 'W');
INSERT INTO `ey_region` VALUES ('4850', '乌海市辖区', '3', '4849', 'W');
INSERT INTO `ey_region` VALUES ('4851', '海勃湾区', '3', '4849', 'H');
INSERT INTO `ey_region` VALUES ('4859', '海南区', '3', '4849', 'H');
INSERT INTO `ey_region` VALUES ('4865', '乌达区', '3', '4849', 'W');
INSERT INTO `ey_region` VALUES ('4874', '赤峰市', '2', '4670', 'C');
INSERT INTO `ey_region` VALUES ('4875', '市辖区', '3', '4874', 'S');
INSERT INTO `ey_region` VALUES ('4876', '红山区', '3', '4874', 'H');
INSERT INTO `ey_region` VALUES ('4888', '元宝山区', '3', '4874', 'Y');
INSERT INTO `ey_region` VALUES ('4896', '松山区', '3', '4874', 'S');
INSERT INTO `ey_region` VALUES ('4919', '阿鲁科尔沁旗', '3', '4874', 'A');
INSERT INTO `ey_region` VALUES ('4932', '巴林左旗', '3', '4874', 'B');
INSERT INTO `ey_region` VALUES ('4944', '巴林右旗', '3', '4874', 'B');
INSERT INTO `ey_region` VALUES ('4953', '林西县', '3', '4874', 'L');
INSERT INTO `ey_region` VALUES ('4963', '克什克腾旗', '3', '4874', 'K');
INSERT INTO `ey_region` VALUES ('4975', '翁牛特旗', '3', '4874', 'W');
INSERT INTO `ey_region` VALUES ('4988', '喀喇沁旗', '3', '4874', 'K');
INSERT INTO `ey_region` VALUES ('4999', '宁城县', '3', '4874', 'N');
INSERT INTO `ey_region` VALUES ('5013', '敖汉旗', '3', '4874', 'A');
INSERT INTO `ey_region` VALUES ('5029', '通辽市', '2', '4670', 'T');
INSERT INTO `ey_region` VALUES ('5030', '市辖区', '3', '5029', 'S');
INSERT INTO `ey_region` VALUES ('5031', '科尔沁区', '3', '5029', 'K');
INSERT INTO `ey_region` VALUES ('5062', '科尔沁左翼中旗', '3', '5029', 'K');
INSERT INTO `ey_region` VALUES ('5079', '科左后旗', '3', '5029', 'K');
INSERT INTO `ey_region` VALUES ('5104', '开鲁县', '3', '5029', 'K');
INSERT INTO `ey_region` VALUES ('5118', '库伦旗', '3', '5029', 'K');
INSERT INTO `ey_region` VALUES ('5125', '奈曼旗', '3', '5029', 'N');
INSERT INTO `ey_region` VALUES ('5139', '扎鲁特旗', '3', '5029', 'Z');
INSERT INTO `ey_region` VALUES ('5155', '霍林郭勒市', '3', '5029', 'H');
INSERT INTO `ey_region` VALUES ('5162', '鄂尔多斯市', '2', '4670', 'E');
INSERT INTO `ey_region` VALUES ('5163', '东胜区', '3', '5162', 'D');
INSERT INTO `ey_region` VALUES ('5176', '达拉特旗', '3', '5162', 'D');
INSERT INTO `ey_region` VALUES ('5185', '准格尔旗', '3', '5162', 'Z');
INSERT INTO `ey_region` VALUES ('5195', '鄂托克前旗', '3', '5162', 'E');
INSERT INTO `ey_region` VALUES ('5201', '鄂托克旗', '3', '5162', 'E');
INSERT INTO `ey_region` VALUES ('5210', '杭锦旗', '3', '5162', 'H');
INSERT INTO `ey_region` VALUES ('5219', '乌审旗', '3', '5162', 'W');
INSERT INTO `ey_region` VALUES ('5228', '伊金霍洛旗', '3', '5162', 'Y');
INSERT INTO `ey_region` VALUES ('5236', '呼伦贝尔市', '2', '4670', 'H');
INSERT INTO `ey_region` VALUES ('5237', '市辖区', '3', '5236', 'S');
INSERT INTO `ey_region` VALUES ('5238', '海拉尔区', '3', '5236', 'H');
INSERT INTO `ey_region` VALUES ('5249', '阿荣旗', '3', '5236', 'A');
INSERT INTO `ey_region` VALUES ('5262', '莫力达瓦旗', '3', '5236', 'M');
INSERT INTO `ey_region` VALUES ('5277', '鄂伦春旗', '3', '5236', 'E');
INSERT INTO `ey_region` VALUES ('5303', '鄂温旗', '3', '5236', 'E');
INSERT INTO `ey_region` VALUES ('5314', '陈巴尔虎旗镇', '3', '5236', 'C');
INSERT INTO `ey_region` VALUES ('5323', '新巴尔虎左旗', '3', '5236', 'X');
INSERT INTO `ey_region` VALUES ('5330', '新巴尔虎右旗', '3', '5236', 'X');
INSERT INTO `ey_region` VALUES ('5337', '满洲里市', '3', '5236', 'M');
INSERT INTO `ey_region` VALUES ('5354', '牙克石市', '3', '5236', 'Y');
INSERT INTO `ey_region` VALUES ('5371', '扎兰屯市', '3', '5236', 'Z');
INSERT INTO `ey_region` VALUES ('5397', '额尔古纳市', '3', '5236', 'E');
INSERT INTO `ey_region` VALUES ('5410', '根河市', '3', '5236', 'G');
INSERT INTO `ey_region` VALUES ('5418', '巴彦淖尔市', '2', '4670', 'B');
INSERT INTO `ey_region` VALUES ('5419', '市辖区', '3', '5418', 'S');
INSERT INTO `ey_region` VALUES ('5420', '临河区', '3', '5418', 'L');
INSERT INTO `ey_region` VALUES ('5440', '五原县', '3', '5418', 'W');
INSERT INTO `ey_region` VALUES ('5450', '磴口县', '3', '5418', 'D');
INSERT INTO `ey_region` VALUES ('5461', '乌拉特前旗', '3', '5418', 'W');
INSERT INTO `ey_region` VALUES ('5477', '乌拉特中旗', '3', '5418', 'W');
INSERT INTO `ey_region` VALUES ('5489', '乌拉特后旗', '3', '5418', 'W');
INSERT INTO `ey_region` VALUES ('5495', '杭锦后旗', '3', '5418', 'H');
INSERT INTO `ey_region` VALUES ('5505', '乌兰察布市', '2', '4670', 'W');
INSERT INTO `ey_region` VALUES ('5506', '市辖区', '3', '5505', 'S');
INSERT INTO `ey_region` VALUES ('5507', '集宁区', '3', '5505', 'J');
INSERT INTO `ey_region` VALUES ('5518', '卓资县', '3', '5505', 'Z');
INSERT INTO `ey_region` VALUES ('5526', '化德县', '3', '5505', 'H');
INSERT INTO `ey_region` VALUES ('5532', '商都县', '3', '5505', 'S');
INSERT INTO `ey_region` VALUES ('5542', '兴和县', '3', '5505', 'X');
INSERT INTO `ey_region` VALUES ('5551', '凉城县', '3', '5505', 'L');
INSERT INTO `ey_region` VALUES ('5562', '察哈尔右翼前旗', '3', '5505', 'C');
INSERT INTO `ey_region` VALUES ('5571', '察右中旗', '3', '5505', 'C');
INSERT INTO `ey_region` VALUES ('5582', '察哈尔右翼后旗', '3', '5505', 'C');
INSERT INTO `ey_region` VALUES ('5590', '四子王旗', '3', '5505', 'S');
INSERT INTO `ey_region` VALUES ('5603', '丰镇市', '3', '5505', 'F');
INSERT INTO `ey_region` VALUES ('5616', '兴安盟', '2', '4670', 'X');
INSERT INTO `ey_region` VALUES ('5617', '乌兰浩特市', '3', '5616', 'W');
INSERT INTO `ey_region` VALUES ('5629', '阿尔山市', '3', '5616', 'A');
INSERT INTO `ey_region` VALUES ('5636', '科右前旗', '3', '5616', 'K');
INSERT INTO `ey_region` VALUES ('5655', '科右中旗', '3', '5616', 'K');
INSERT INTO `ey_region` VALUES ('5677', '扎赉特旗', '3', '5616', 'Z');
INSERT INTO `ey_region` VALUES ('5692', '突泉县', '3', '5616', 'T');
INSERT INTO `ey_region` VALUES ('5702', '锡林郭勒盟', '2', '4670', 'X');
INSERT INTO `ey_region` VALUES ('5703', '二连浩特市', '3', '5702', 'E');
INSERT INTO `ey_region` VALUES ('5709', '锡林浩特市', '3', '5702', 'X');
INSERT INTO `ey_region` VALUES ('5723', '阿巴嘎旗', '3', '5702', 'A');
INSERT INTO `ey_region` VALUES ('5731', '苏尼特左旗', '3', '5702', 'S');
INSERT INTO `ey_region` VALUES ('5738', '苏尼特右旗', '3', '5702', 'S');
INSERT INTO `ey_region` VALUES ('5745', '东乌珠穆沁旗', '3', '5702', 'D');
INSERT INTO `ey_region` VALUES ('5758', '西乌珠穆沁旗', '3', '5702', 'X');
INSERT INTO `ey_region` VALUES ('5766', '太仆寺旗', '3', '5702', 'T');
INSERT INTO `ey_region` VALUES ('5774', '镶黄旗', '3', '5702', 'X');
INSERT INTO `ey_region` VALUES ('5778', '正镶白旗', '3', '5702', 'Z');
INSERT INTO `ey_region` VALUES ('5785', '正蓝旗', '3', '5702', 'Z');
INSERT INTO `ey_region` VALUES ('5794', '多伦县', '3', '5702', 'D');
INSERT INTO `ey_region` VALUES ('5799', '阿拉善盟', '2', '4670', 'A');
INSERT INTO `ey_region` VALUES ('5800', '阿拉善左旗', '3', '5799', 'A');
INSERT INTO `ey_region` VALUES ('5814', '阿拉善右旗', '3', '5799', 'A');
INSERT INTO `ey_region` VALUES ('5820', '额济纳旗', '3', '5799', 'E');
INSERT INTO `ey_region` VALUES ('5827', '辽宁省', '1', '0', 'L');
INSERT INTO `ey_region` VALUES ('5828', '沈阳市', '2', '5827', 'S');
INSERT INTO `ey_region` VALUES ('5829', '市辖区', '3', '5828', 'S');
INSERT INTO `ey_region` VALUES ('5830', '和平区', '3', '5828', 'H');
INSERT INTO `ey_region` VALUES ('5848', '沈河区', '3', '5828', 'S');
INSERT INTO `ey_region` VALUES ('5859', '大东区', '3', '5828', 'D');
INSERT INTO `ey_region` VALUES ('5873', '皇姑区', '3', '5828', 'H');
INSERT INTO `ey_region` VALUES ('5894', '铁西区', '3', '5828', 'T');
INSERT INTO `ey_region` VALUES ('5909', '苏家屯区', '3', '5828', 'S');
INSERT INTO `ey_region` VALUES ('5934', '东陵区', '3', '5828', 'D');
INSERT INTO `ey_region` VALUES ('5954', '新城子区', '3', '5828', 'X');
INSERT INTO `ey_region` VALUES ('5975', '于洪区', '3', '5828', 'Y');
INSERT INTO `ey_region` VALUES ('5998', '辽中县', '3', '5828', 'L');
INSERT INTO `ey_region` VALUES ('6020', '康平县', '3', '5828', 'K');
INSERT INTO `ey_region` VALUES ('6038', '法库县', '3', '5828', 'F');
INSERT INTO `ey_region` VALUES ('6058', '新民市', '3', '5828', 'X');
INSERT INTO `ey_region` VALUES ('6088', '大连市', '2', '5827', 'D');
INSERT INTO `ey_region` VALUES ('6089', '市辖区', '3', '6088', 'S');
INSERT INTO `ey_region` VALUES ('6090', '中山区', '3', '6088', 'Z');
INSERT INTO `ey_region` VALUES ('6099', '西岗区', '3', '6088', 'X');
INSERT INTO `ey_region` VALUES ('6107', '沙河口区', '3', '6088', 'S');
INSERT INTO `ey_region` VALUES ('6117', '甘井子区', '3', '6088', 'G');
INSERT INTO `ey_region` VALUES ('6137', '旅顺口区', '3', '6088', 'L');
INSERT INTO `ey_region` VALUES ('6153', '金州区', '3', '6088', 'J');
INSERT INTO `ey_region` VALUES ('6178', '长海县', '3', '6088', 'C');
INSERT INTO `ey_region` VALUES ('6184', '瓦房店市', '3', '6088', 'W');
INSERT INTO `ey_region` VALUES ('6217', '普兰店市', '3', '6088', 'P');
INSERT INTO `ey_region` VALUES ('6239', '庄河市', '3', '6088', 'Z');
INSERT INTO `ey_region` VALUES ('6266', '鞍山市', '2', '5827', 'A');
INSERT INTO `ey_region` VALUES ('6267', '市辖区', '3', '6266', 'S');
INSERT INTO `ey_region` VALUES ('6268', '铁东区', '3', '6266', 'T');
INSERT INTO `ey_region` VALUES ('6282', '铁西区', '3', '6266', 'T');
INSERT INTO `ey_region` VALUES ('6293', '立山区', '3', '6266', 'L');
INSERT INTO `ey_region` VALUES ('6303', '千山区', '3', '6266', 'Q');
INSERT INTO `ey_region` VALUES ('6316', '台安县', '3', '6266', 'T');
INSERT INTO `ey_region` VALUES ('6331', '岫岩县', '3', '6266', 'X');
INSERT INTO `ey_region` VALUES ('6354', '海城市', '3', '6266', 'H');
INSERT INTO `ey_region` VALUES ('6384', '抚顺市', '2', '5827', 'F');
INSERT INTO `ey_region` VALUES ('6385', '市辖区', '3', '6384', 'S');
INSERT INTO `ey_region` VALUES ('6386', '新抚区', '3', '6384', 'X');
INSERT INTO `ey_region` VALUES ('6395', '东洲区', '3', '6384', 'D');
INSERT INTO `ey_region` VALUES ('6409', '望花区', '3', '6384', 'W');
INSERT INTO `ey_region` VALUES ('6422', '顺城区', '3', '6384', 'S');
INSERT INTO `ey_region` VALUES ('6432', '抚顺县', '3', '6384', 'F');
INSERT INTO `ey_region` VALUES ('6445', '新宾县', '3', '6384', 'X');
INSERT INTO `ey_region` VALUES ('6461', '清原县', '3', '6384', 'Q');
INSERT INTO `ey_region` VALUES ('6476', '本溪市', '2', '5827', 'B');
INSERT INTO `ey_region` VALUES ('6477', '市辖区', '3', '6476', 'S');
INSERT INTO `ey_region` VALUES ('6478', '平山区', '3', '6476', 'P');
INSERT INTO `ey_region` VALUES ('6488', '溪湖区', '3', '6476', 'X');
INSERT INTO `ey_region` VALUES ('6499', '明山区', '3', '6476', 'M');
INSERT INTO `ey_region` VALUES ('6509', '南芬区', '3', '6476', 'N');
INSERT INTO `ey_region` VALUES ('6515', '本溪县', '3', '6476', 'B');
INSERT INTO `ey_region` VALUES ('6528', '桓仁县', '3', '6476', 'H');
INSERT INTO `ey_region` VALUES ('6542', '丹东市', '2', '5827', 'D');
INSERT INTO `ey_region` VALUES ('6543', '市辖区', '3', '6542', 'S');
INSERT INTO `ey_region` VALUES ('6544', '元宝区', '3', '6542', 'Y');
INSERT INTO `ey_region` VALUES ('6552', '振兴区', '3', '6542', 'Z');
INSERT INTO `ey_region` VALUES ('6563', '振安区', '3', '6542', 'Z');
INSERT INTO `ey_region` VALUES ('6573', '宽甸县', '3', '6542', 'K');
INSERT INTO `ey_region` VALUES ('6596', '东港市', '3', '6542', 'D');
INSERT INTO `ey_region` VALUES ('6621', '凤城市', '3', '6542', 'F');
INSERT INTO `ey_region` VALUES ('6643', '锦州市', '2', '5827', 'J');
INSERT INTO `ey_region` VALUES ('6644', '市辖区', '3', '6643', 'S');
INSERT INTO `ey_region` VALUES ('6645', '古塔区', '3', '6643', 'G');
INSERT INTO `ey_region` VALUES ('6655', '凌河区', '3', '6643', 'L');
INSERT INTO `ey_region` VALUES ('6668', '太和区', '3', '6643', 'T');
INSERT INTO `ey_region` VALUES ('6683', '黑山县', '3', '6643', 'H');
INSERT INTO `ey_region` VALUES ('6706', '义县', '3', '6643', 'Y');
INSERT INTO `ey_region` VALUES ('6725', '凌海市', '3', '6643', 'L');
INSERT INTO `ey_region` VALUES ('6750', '北镇市', '3', '6643', 'B');
INSERT INTO `ey_region` VALUES ('6771', '营口市', '2', '5827', 'Y');
INSERT INTO `ey_region` VALUES ('6772', '市辖区', '3', '6771', 'S');
INSERT INTO `ey_region` VALUES ('6773', '站前区', '3', '6771', 'Z');
INSERT INTO `ey_region` VALUES ('6781', '西市区', '3', '6771', 'X');
INSERT INTO `ey_region` VALUES ('6789', '鲅鱼圈区', '3', '6771', 'B');
INSERT INTO `ey_region` VALUES ('6797', '老边区', '3', '6771', 'L');
INSERT INTO `ey_region` VALUES ('6804', '盖州市', '3', '6771', 'G');
INSERT INTO `ey_region` VALUES ('6832', '大石桥市', '3', '6771', 'D');
INSERT INTO `ey_region` VALUES ('6851', '阜新市', '2', '5827', 'F');
INSERT INTO `ey_region` VALUES ('6852', '市辖区', '3', '6851', 'S');
INSERT INTO `ey_region` VALUES ('6853', '海州区', '3', '6851', 'H');
INSERT INTO `ey_region` VALUES ('6865', '新邱区', '3', '6851', 'X');
INSERT INTO `ey_region` VALUES ('6871', '太平区', '3', '6851', 'T');
INSERT INTO `ey_region` VALUES ('6878', '清河门区', '3', '6851', 'Q');
INSERT INTO `ey_region` VALUES ('6885', '细河区', '3', '6851', 'X');
INSERT INTO `ey_region` VALUES ('6893', '阜新县', '3', '6851', 'F');
INSERT INTO `ey_region` VALUES ('6930', '彰武县', '3', '6851', 'Z');
INSERT INTO `ey_region` VALUES ('6955', '辽阳市', '2', '5827', 'L');
INSERT INTO `ey_region` VALUES ('6956', '市辖区', '3', '6955', 'S');
INSERT INTO `ey_region` VALUES ('6957', '白塔区', '3', '6955', 'B');
INSERT INTO `ey_region` VALUES ('6964', '文圣区', '3', '6955', 'W');
INSERT INTO `ey_region` VALUES ('6971', '宏伟区', '3', '6955', 'H');
INSERT INTO `ey_region` VALUES ('6977', '弓长岭区', '3', '6955', 'G');
INSERT INTO `ey_region` VALUES ('6983', '太子河区', '3', '6955', 'T');
INSERT INTO `ey_region` VALUES ('6989', '辽阳县', '3', '6955', 'L');
INSERT INTO `ey_region` VALUES ('7007', '灯塔市', '3', '6955', 'D');
INSERT INTO `ey_region` VALUES ('7024', '盘锦市', '2', '5827', 'P');
INSERT INTO `ey_region` VALUES ('7025', '市辖区', '3', '7024', 'S');
INSERT INTO `ey_region` VALUES ('7026', '双台子区', '3', '7024', 'S');
INSERT INTO `ey_region` VALUES ('7036', '兴隆台区', '3', '7024', 'X');
INSERT INTO `ey_region` VALUES ('7055', '大洼县', '3', '7024', 'D');
INSERT INTO `ey_region` VALUES ('7072', '盘山县', '3', '7024', 'P');
INSERT INTO `ey_region` VALUES ('7088', '铁岭市', '2', '5827', 'T');
INSERT INTO `ey_region` VALUES ('7089', '市辖区', '3', '7088', 'S');
INSERT INTO `ey_region` VALUES ('7090', '银州区', '3', '7088', 'Y');
INSERT INTO `ey_region` VALUES ('7099', '清河区', '3', '7088', 'Q');
INSERT INTO `ey_region` VALUES ('7105', '铁岭县', '3', '7088', 'T');
INSERT INTO `ey_region` VALUES ('7121', '西丰县', '3', '7088', 'X');
INSERT INTO `ey_region` VALUES ('7140', '昌图县', '3', '7088', 'C');
INSERT INTO `ey_region` VALUES ('7180', '调兵山市', '3', '7088', 'D');
INSERT INTO `ey_region` VALUES ('7186', '开原市', '3', '7088', 'K');
INSERT INTO `ey_region` VALUES ('7208', '朝阳市', '2', '5827', 'C');
INSERT INTO `ey_region` VALUES ('7209', '市辖区', '3', '7208', 'S');
INSERT INTO `ey_region` VALUES ('7210', '双塔区', '3', '7208', 'S');
INSERT INTO `ey_region` VALUES ('7225', '龙城区', '3', '7208', 'L');
INSERT INTO `ey_region` VALUES ('7238', '朝阳县', '3', '7208', 'C');
INSERT INTO `ey_region` VALUES ('7267', '建平县', '3', '7208', 'J');
INSERT INTO `ey_region` VALUES ('7299', '喀喇沁左翼县', '3', '7208', 'K');
INSERT INTO `ey_region` VALUES ('7322', '北票市', '3', '7208', 'B');
INSERT INTO `ey_region` VALUES ('7360', '凌源市', '3', '7208', 'L');
INSERT INTO `ey_region` VALUES ('7391', '葫芦岛市', '2', '5827', 'H');
INSERT INTO `ey_region` VALUES ('7392', '市辖区', '3', '7391', 'S');
INSERT INTO `ey_region` VALUES ('7393', '连山区', '3', '7391', 'L');
INSERT INTO `ey_region` VALUES ('7419', '龙港区', '3', '7391', 'L');
INSERT INTO `ey_region` VALUES ('7433', '南票区', '3', '7391', 'N');
INSERT INTO `ey_region` VALUES ('7446', '绥中县', '3', '7391', 'S');
INSERT INTO `ey_region` VALUES ('7474', '建昌县', '3', '7391', 'J');
INSERT INTO `ey_region` VALUES ('7503', '兴城市', '3', '7391', 'X');
INSERT INTO `ey_region` VALUES ('7531', '吉林省', '1', '0', 'J');
INSERT INTO `ey_region` VALUES ('7532', '长春市', '2', '7531', 'C');
INSERT INTO `ey_region` VALUES ('7533', '长春市辖区', '3', '7532', 'C');
INSERT INTO `ey_region` VALUES ('7534', '南关区', '3', '7532', 'N');
INSERT INTO `ey_region` VALUES ('7552', '宽城区', '3', '7532', 'K');
INSERT INTO `ey_region` VALUES ('7569', '朝阳区', '3', '7532', 'C');
INSERT INTO `ey_region` VALUES ('7582', '二道区', '3', '7532', 'E');
INSERT INTO `ey_region` VALUES ('7597', '绿园区', '3', '7532', 'L');
INSERT INTO `ey_region` VALUES ('7610', '双阳区', '3', '7532', 'S');
INSERT INTO `ey_region` VALUES ('7619', '农安县', '3', '7532', 'N');
INSERT INTO `ey_region` VALUES ('7642', '九台市', '3', '7532', 'J');
INSERT INTO `ey_region` VALUES ('7658', '榆树市', '3', '7532', 'Y');
INSERT INTO `ey_region` VALUES ('7687', '德惠市', '3', '7532', 'D');
INSERT INTO `ey_region` VALUES ('7706', '吉林市', '2', '7531', 'J');
INSERT INTO `ey_region` VALUES ('7707', '吉林市辖区', '3', '7706', 'J');
INSERT INTO `ey_region` VALUES ('7708', '昌邑区', '3', '7706', 'C');
INSERT INTO `ey_region` VALUES ('7731', '龙潭区', '3', '7706', 'L');
INSERT INTO `ey_region` VALUES ('7752', '船营区', '3', '7706', 'C');
INSERT INTO `ey_region` VALUES ('7768', '丰满区', '3', '7706', 'F');
INSERT INTO `ey_region` VALUES ('7781', '永吉县', '3', '7706', 'Y');
INSERT INTO `ey_region` VALUES ('7792', '蛟河市', '3', '7706', 'J');
INSERT INTO `ey_region` VALUES ('7810', '桦甸市', '3', '7706', 'H');
INSERT INTO `ey_region` VALUES ('7828', '舒兰市', '3', '7706', 'S');
INSERT INTO `ey_region` VALUES ('7849', '磐石市', '3', '7706', 'P');
INSERT INTO `ey_region` VALUES ('7868', '四平市', '2', '7531', 'S');
INSERT INTO `ey_region` VALUES ('7869', '四平市辖区', '3', '7868', 'S');
INSERT INTO `ey_region` VALUES ('7870', '铁西区', '3', '7868', 'T');
INSERT INTO `ey_region` VALUES ('7878', '铁东区', '3', '7868', 'T');
INSERT INTO `ey_region` VALUES ('7892', '梨树县', '3', '7868', 'L');
INSERT INTO `ey_region` VALUES ('7916', '伊通县', '3', '7868', 'Y');
INSERT INTO `ey_region` VALUES ('7933', '公主岭市', '3', '7868', 'G');
INSERT INTO `ey_region` VALUES ('7964', '双辽市', '3', '7868', 'S');
INSERT INTO `ey_region` VALUES ('7986', '辽源市', '2', '7531', 'L');
INSERT INTO `ey_region` VALUES ('7987', '辽源市辖区', '3', '7986', 'L');
INSERT INTO `ey_region` VALUES ('7988', '龙山区', '3', '7986', 'L');
INSERT INTO `ey_region` VALUES ('8000', '西安区', '3', '7986', 'X');
INSERT INTO `ey_region` VALUES ('8008', '东丰县', '3', '7986', 'D');
INSERT INTO `ey_region` VALUES ('8023', '东辽县', '3', '7986', 'D');
INSERT INTO `ey_region` VALUES ('8037', '通化市', '2', '7531', 'T');
INSERT INTO `ey_region` VALUES ('8038', '通化市辖区', '3', '8037', 'T');
INSERT INTO `ey_region` VALUES ('8039', '东昌区', '3', '8037', 'D');
INSERT INTO `ey_region` VALUES ('8051', '二道江区', '3', '8037', 'E');
INSERT INTO `ey_region` VALUES ('8058', '通化县', '3', '8037', 'T');
INSERT INTO `ey_region` VALUES ('8076', '辉南县', '3', '8037', 'H');
INSERT INTO `ey_region` VALUES ('8088', '柳河县', '3', '8037', 'L');
INSERT INTO `ey_region` VALUES ('8104', '梅河口市', '3', '8037', 'M');
INSERT INTO `ey_region` VALUES ('8129', '集安市', '3', '8037', 'J');
INSERT INTO `ey_region` VALUES ('8144', '白山市', '2', '7531', 'B');
INSERT INTO `ey_region` VALUES ('8145', '白山市辖区', '3', '8144', 'B');
INSERT INTO `ey_region` VALUES ('8146', '八道江区', '3', '8144', 'B');
INSERT INTO `ey_region` VALUES ('8159', '江源区', '3', '8144', 'J');
INSERT INTO `ey_region` VALUES ('8168', '抚松县', '3', '8144', 'F');
INSERT INTO `ey_region` VALUES ('8183', '靖宇县', '3', '8144', 'J');
INSERT INTO `ey_region` VALUES ('8192', '长白县', '3', '8144', 'C');
INSERT INTO `ey_region` VALUES ('8202', '临江市', '3', '8144', 'L');
INSERT INTO `ey_region` VALUES ('8216', '松原市', '2', '7531', 'S');
INSERT INTO `ey_region` VALUES ('8217', '松原市辖区', '3', '8216', 'S');
INSERT INTO `ey_region` VALUES ('8218', '宁江区', '3', '8216', 'N');
INSERT INTO `ey_region` VALUES ('8239', '前郭县', '3', '8216', 'Q');
INSERT INTO `ey_region` VALUES ('8266', '长岭县', '3', '8216', 'C');
INSERT INTO `ey_region` VALUES ('8300', '乾安县', '3', '8216', 'Q');
INSERT INTO `ey_region` VALUES ('8311', '扶余县', '3', '8216', 'F');
INSERT INTO `ey_region` VALUES ('8333', '白城市', '2', '7531', 'B');
INSERT INTO `ey_region` VALUES ('8334', '白城市辖区', '3', '8333', 'B');
INSERT INTO `ey_region` VALUES ('8335', '洮北区', '3', '8333', 'T');
INSERT INTO `ey_region` VALUES ('8362', '镇赉县', '3', '8333', 'Z');
INSERT INTO `ey_region` VALUES ('8375', '通榆县', '3', '8333', 'T');
INSERT INTO `ey_region` VALUES ('8393', '洮南市', '3', '8333', 'T');
INSERT INTO `ey_region` VALUES ('8420', '大安市', '3', '8333', 'D');
INSERT INTO `ey_region` VALUES ('8445', '延边州', '2', '7531', 'Y');
INSERT INTO `ey_region` VALUES ('8446', '延吉市', '3', '8445', 'Y');
INSERT INTO `ey_region` VALUES ('8456', '图们市', '3', '8445', 'T');
INSERT INTO `ey_region` VALUES ('8464', '敦化市', '3', '8445', 'D');
INSERT INTO `ey_region` VALUES ('8489', '珲春市', '3', '8445', 'H');
INSERT INTO `ey_region` VALUES ('8504', '龙井市', '3', '8445', 'L');
INSERT INTO `ey_region` VALUES ('8515', '和龙市', '3', '8445', 'H');
INSERT INTO `ey_region` VALUES ('8530', '汪清县', '3', '8445', 'W');
INSERT INTO `ey_region` VALUES ('8545', '安图县', '3', '8445', 'A');
INSERT INTO `ey_region` VALUES ('8558', '黑龙江省', '1', '0', 'H');
INSERT INTO `ey_region` VALUES ('8559', '哈尔滨市', '2', '8558', 'H');
INSERT INTO `ey_region` VALUES ('8560', '市辖区', '3', '8559', 'S');
INSERT INTO `ey_region` VALUES ('8561', '道里区', '3', '8559', 'D');
INSERT INTO `ey_region` VALUES ('8585', '南岗区', '3', '8559', 'N');
INSERT INTO `ey_region` VALUES ('8606', '道外区', '3', '8559', 'D');
INSERT INTO `ey_region` VALUES ('8633', '平房区', '3', '8559', 'P');
INSERT INTO `ey_region` VALUES ('8642', '松北区', '3', '8559', 'S');
INSERT INTO `ey_region` VALUES ('8650', '香坊区', '3', '8559', 'X');
INSERT INTO `ey_region` VALUES ('8676', '呼兰区', '3', '8559', 'H');
INSERT INTO `ey_region` VALUES ('8694', '阿城区', '3', '8559', 'A');
INSERT INTO `ey_region` VALUES ('8714', '依兰县', '3', '8559', 'Y');
INSERT INTO `ey_region` VALUES ('8729', '方正县', '3', '8559', 'F');
INSERT INTO `ey_region` VALUES ('8740', '宾县', '3', '8559', 'B');
INSERT INTO `ey_region` VALUES ('8758', '巴彦县', '3', '8559', 'B');
INSERT INTO `ey_region` VALUES ('8778', '木兰县', '3', '8559', 'M');
INSERT INTO `ey_region` VALUES ('8788', '通河县', '3', '8559', 'T');
INSERT INTO `ey_region` VALUES ('8800', '延寿县', '3', '8559', 'Y');
INSERT INTO `ey_region` VALUES ('8812', '双城市', '3', '8559', 'S');
INSERT INTO `ey_region` VALUES ('8838', '尚志市', '3', '8559', 'S');
INSERT INTO `ey_region` VALUES ('8858', '五常市', '3', '8559', 'W');
INSERT INTO `ey_region` VALUES ('8884', '齐齐哈尔市', '2', '8558', 'Q');
INSERT INTO `ey_region` VALUES ('8885', '市辖区', '3', '8884', 'S');
INSERT INTO `ey_region` VALUES ('8886', '龙沙区', '3', '8884', 'L');
INSERT INTO `ey_region` VALUES ('8894', '建华区', '3', '8884', 'J');
INSERT INTO `ey_region` VALUES ('8901', '铁锋区', '3', '8884', 'T');
INSERT INTO `ey_region` VALUES ('8911', '昂昂溪区', '3', '8884', 'A');
INSERT INTO `ey_region` VALUES ('8918', '富拉尔基区', '3', '8884', 'F');
INSERT INTO `ey_region` VALUES ('8928', '碾子山区', '3', '8884', 'N');
INSERT INTO `ey_region` VALUES ('8934', '梅里斯达斡尔族区', '3', '8884', 'M');
INSERT INTO `ey_region` VALUES ('8943', '龙江县', '3', '8884', 'L');
INSERT INTO `ey_region` VALUES ('8958', '依安县', '3', '8884', 'Y');
INSERT INTO `ey_region` VALUES ('8976', '泰来县', '3', '8884', 'T');
INSERT INTO `ey_region` VALUES ('8998', '甘南县', '3', '8884', 'G');
INSERT INTO `ey_region` VALUES ('9011', '富裕县', '3', '8884', 'F');
INSERT INTO `ey_region` VALUES ('9024', '克山县', '3', '8884', 'K');
INSERT INTO `ey_region` VALUES ('9048', '克东县', '3', '8884', 'K');
INSERT INTO `ey_region` VALUES ('9067', '拜泉县', '3', '8884', 'B');
INSERT INTO `ey_region` VALUES ('9084', '讷河市', '3', '8884', 'N');
INSERT INTO `ey_region` VALUES ('9117', '鸡西市', '2', '8558', 'J');
INSERT INTO `ey_region` VALUES ('9118', '市辖区', '3', '9117', 'S');
INSERT INTO `ey_region` VALUES ('9119', '鸡冠区', '3', '9117', 'J');
INSERT INTO `ey_region` VALUES ('9129', '恒山区', '3', '9117', 'H');
INSERT INTO `ey_region` VALUES ('9139', '滴道区', '3', '9117', 'D');
INSERT INTO `ey_region` VALUES ('9146', '梨树区', '3', '9117', 'L');
INSERT INTO `ey_region` VALUES ('9153', '城子河区', '3', '9117', 'C');
INSERT INTO `ey_region` VALUES ('9161', '麻山区', '3', '9117', 'M');
INSERT INTO `ey_region` VALUES ('9164', '鸡东县', '3', '9117', 'J');
INSERT INTO `ey_region` VALUES ('9178', '虎林市', '3', '9117', 'H');
INSERT INTO `ey_region` VALUES ('9199', '密山市', '3', '9117', 'M');
INSERT INTO `ey_region` VALUES ('9222', '鹤岗市', '2', '8558', 'H');
INSERT INTO `ey_region` VALUES ('9223', '市辖区', '3', '9222', 'S');
INSERT INTO `ey_region` VALUES ('9224', '向阳区', '3', '9222', 'X');
INSERT INTO `ey_region` VALUES ('9230', '工农区', '3', '9222', 'G');
INSERT INTO `ey_region` VALUES ('9237', '南山区', '3', '9222', 'N');
INSERT INTO `ey_region` VALUES ('9244', '兴安区', '3', '9222', 'X');
INSERT INTO `ey_region` VALUES ('9250', '东山区', '3', '9222', 'D');
INSERT INTO `ey_region` VALUES ('9261', '兴山区', '3', '9222', 'X');
INSERT INTO `ey_region` VALUES ('9266', '萝北县', '3', '9222', 'L');
INSERT INTO `ey_region` VALUES ('9283', '绥滨县', '3', '9222', 'S');
INSERT INTO `ey_region` VALUES ('9296', '双鸭山市', '2', '8558', 'S');
INSERT INTO `ey_region` VALUES ('9297', '市辖区', '3', '9296', 'S');
INSERT INTO `ey_region` VALUES ('9298', '尖山区', '3', '9296', 'J');
INSERT INTO `ey_region` VALUES ('9307', '岭东区', '3', '9296', 'L');
INSERT INTO `ey_region` VALUES ('9317', '四方台区', '3', '9296', 'S');
INSERT INTO `ey_region` VALUES ('9323', '宝山区', '3', '9296', 'B');
INSERT INTO `ey_region` VALUES ('9335', '集贤县', '3', '9296', 'J');
INSERT INTO `ey_region` VALUES ('9356', '友谊县', '3', '9296', 'Y');
INSERT INTO `ey_region` VALUES ('9370', '宝清县', '3', '9296', 'B');
INSERT INTO `ey_region` VALUES ('9393', '饶河县', '3', '9296', 'R');
INSERT INTO `ey_region` VALUES ('9419', '大庆市', '2', '8558', 'D');
INSERT INTO `ey_region` VALUES ('9420', '市辖区', '3', '9419', 'S');
INSERT INTO `ey_region` VALUES ('9421', '萨尔图区', '3', '9419', 'S');
INSERT INTO `ey_region` VALUES ('9431', '龙凤区', '3', '9419', 'L');
INSERT INTO `ey_region` VALUES ('9440', '让胡路区', '3', '9419', 'R');
INSERT INTO `ey_region` VALUES ('9451', '红岗区', '3', '9419', 'H');
INSERT INTO `ey_region` VALUES ('9458', '大同区', '3', '9419', 'D');
INSERT INTO `ey_region` VALUES ('9474', '肇州县', '3', '9419', 'Z');
INSERT INTO `ey_region` VALUES ('9489', '肇源县', '3', '9419', 'Z');
INSERT INTO `ey_region` VALUES ('9514', '林甸县', '3', '9419', 'L');
INSERT INTO `ey_region` VALUES ('9527', '杜尔伯特县', '3', '9419', 'D');
INSERT INTO `ey_region` VALUES ('9553', '伊春市', '2', '8558', 'Y');
INSERT INTO `ey_region` VALUES ('9554', '市辖区', '3', '9553', 'S');
INSERT INTO `ey_region` VALUES ('9555', '伊春区', '3', '9553', 'Y');
INSERT INTO `ey_region` VALUES ('9561', '南岔区', '3', '9553', 'N');
INSERT INTO `ey_region` VALUES ('9581', '友好区', '3', '9553', 'Y');
INSERT INTO `ey_region` VALUES ('9599', '西林区', '3', '9553', 'X');
INSERT INTO `ey_region` VALUES ('9603', '翠峦区', '3', '9553', 'C');
INSERT INTO `ey_region` VALUES ('9614', '新青区', '3', '9553', 'X');
INSERT INTO `ey_region` VALUES ('9631', '美溪区', '3', '9553', 'M');
INSERT INTO `ey_region` VALUES ('9647', '金山屯区', '3', '9553', 'J');
INSERT INTO `ey_region` VALUES ('9660', '五营区', '3', '9553', 'W');
INSERT INTO `ey_region` VALUES ('9671', '乌马河区', '3', '9553', 'W');
INSERT INTO `ey_region` VALUES ('9685', '汤旺河区', '3', '9553', 'T');
INSERT INTO `ey_region` VALUES ('9702', '带岭区', '3', '9553', 'D');
INSERT INTO `ey_region` VALUES ('9715', '乌伊岭区', '3', '9553', 'W');
INSERT INTO `ey_region` VALUES ('9729', '红星区', '3', '9553', 'H');
INSERT INTO `ey_region` VALUES ('9742', '上甘岭区', '3', '9553', 'S');
INSERT INTO `ey_region` VALUES ('9756', '嘉荫县', '3', '9553', 'J');
INSERT INTO `ey_region` VALUES ('9772', '铁力市', '3', '9553', 'T');
INSERT INTO `ey_region` VALUES ('9785', '佳木斯市', '2', '8558', 'J');
INSERT INTO `ey_region` VALUES ('9786', '市辖区', '3', '9785', 'S');
INSERT INTO `ey_region` VALUES ('9787', '向阳区', '3', '9785', 'X');
INSERT INTO `ey_region` VALUES ('9795', '前进区', '3', '9785', 'Q');
INSERT INTO `ey_region` VALUES ('9802', '东风区', '3', '9785', 'D');
INSERT INTO `ey_region` VALUES ('9810', '郊区', '3', '9785', 'J');
INSERT INTO `ey_region` VALUES ('9825', '桦南县', '3', '9785', 'H');
INSERT INTO `ey_region` VALUES ('9839', '桦川县', '3', '9785', 'H');
INSERT INTO `ey_region` VALUES ('9851', '汤原县', '3', '9785', 'T');
INSERT INTO `ey_region` VALUES ('9866', '抚远县', '3', '9785', 'F');
INSERT INTO `ey_region` VALUES ('9879', '同江市', '3', '9785', 'T');
INSERT INTO `ey_region` VALUES ('9907', '富锦市', '3', '9785', 'F');
INSERT INTO `ey_region` VALUES ('9930', '七台河市', '2', '8558', 'Q');
INSERT INTO `ey_region` VALUES ('9931', '市辖区', '3', '9930', 'S');
INSERT INTO `ey_region` VALUES ('9932', '新兴区', '3', '9930', 'X');
INSERT INTO `ey_region` VALUES ('9944', '桃山区', '3', '9930', 'T');
INSERT INTO `ey_region` VALUES ('9952', '茄子河区', '3', '9930', 'Q');
INSERT INTO `ey_region` VALUES ('9962', '勃利县', '3', '9930', 'B');
INSERT INTO `ey_region` VALUES ('9981', '牡丹江市', '2', '8558', 'M');
INSERT INTO `ey_region` VALUES ('9982', '市辖区', '3', '9981', 'S');
INSERT INTO `ey_region` VALUES ('9983', '东安区', '3', '9981', 'D');
INSERT INTO `ey_region` VALUES ('9989', '阳明区', '3', '9981', 'Y');
INSERT INTO `ey_region` VALUES ('9996', '爱民区', '3', '9981', 'A');
INSERT INTO `ey_region` VALUES ('10005', '西安区', '3', '9981', 'X');
INSERT INTO `ey_region` VALUES ('10014', '东宁县', '3', '9981', 'D');
INSERT INTO `ey_region` VALUES ('10022', '林口县', '3', '9981', 'L');
INSERT INTO `ey_region` VALUES ('10036', '绥芬河市', '3', '9981', 'S');
INSERT INTO `ey_region` VALUES ('10039', '海林市', '3', '9981', 'H');
INSERT INTO `ey_region` VALUES ('10056', '宁安市', '3', '9981', 'N');
INSERT INTO `ey_region` VALUES ('10072', '穆棱市', '3', '9981', 'M');
INSERT INTO `ey_region` VALUES ('10084', '黑河市', '2', '8558', 'H');
INSERT INTO `ey_region` VALUES ('10085', '市辖区', '3', '10084', 'S');
INSERT INTO `ey_region` VALUES ('10086', '爱辉区', '3', '10084', 'A');
INSERT INTO `ey_region` VALUES ('10122', '嫩江县', '3', '10084', 'N');
INSERT INTO `ey_region` VALUES ('10150', '逊克县', '3', '10084', 'X');
INSERT INTO `ey_region` VALUES ('10168', '孙吴县', '3', '10084', 'S');
INSERT INTO `ey_region` VALUES ('10192', '北安市', '3', '10084', 'B');
INSERT INTO `ey_region` VALUES ('10214', '五大连池市', '3', '10084', 'W');
INSERT INTO `ey_region` VALUES ('10252', '绥化市', '2', '8558', 'S');
INSERT INTO `ey_region` VALUES ('10253', '市辖区', '3', '10252', 'S');
INSERT INTO `ey_region` VALUES ('10254', '北林区', '3', '10252', 'B');
INSERT INTO `ey_region` VALUES ('10281', '望奎县', '3', '10252', 'W');
INSERT INTO `ey_region` VALUES ('10301', '兰西县', '3', '10252', 'L');
INSERT INTO `ey_region` VALUES ('10320', '青冈县', '3', '10252', 'Q');
INSERT INTO `ey_region` VALUES ('10342', '庆安县', '3', '10252', 'Q');
INSERT INTO `ey_region` VALUES ('10360', '明水县', '3', '10252', 'M');
INSERT INTO `ey_region` VALUES ('10380', '绥棱县', '3', '10252', 'S');
INSERT INTO `ey_region` VALUES ('10401', '安达市', '3', '10252', 'A');
INSERT INTO `ey_region` VALUES ('10425', '肇东市', '3', '10252', 'Z');
INSERT INTO `ey_region` VALUES ('10452', '海伦市', '3', '10252', 'H');
INSERT INTO `ey_region` VALUES ('10483', '大兴安岭地区', '2', '8558', 'D');
INSERT INTO `ey_region` VALUES ('10484', '加格达奇区', '3', '10483', 'J');
INSERT INTO `ey_region` VALUES ('10495', '松岭区', '3', '10483', 'S');
INSERT INTO `ey_region` VALUES ('10500', '新林区', '3', '10483', 'X');
INSERT INTO `ey_region` VALUES ('10509', '呼中区', '3', '10483', 'H');
INSERT INTO `ey_region` VALUES ('10515', '呼玛县', '3', '10483', 'H');
INSERT INTO `ey_region` VALUES ('10525', '塔河县', '3', '10483', 'T');
INSERT INTO `ey_region` VALUES ('10534', '漠河县', '3', '10483', 'M');
INSERT INTO `ey_region` VALUES ('10543', '上海市', '1', '0', 'S');
INSERT INTO `ey_region` VALUES ('10544', '上海市', '2', '10543', 'S');
INSERT INTO `ey_region` VALUES ('10545', '黄浦区', '3', '10544', 'H');
INSERT INTO `ey_region` VALUES ('10555', '卢湾区', '3', '10544', 'L');
INSERT INTO `ey_region` VALUES ('10560', '徐汇区', '3', '10544', 'X');
INSERT INTO `ey_region` VALUES ('10575', '长宁区', '3', '10544', 'C');
INSERT INTO `ey_region` VALUES ('10586', '静安区', '3', '10544', 'J');
INSERT INTO `ey_region` VALUES ('10592', '普陀区', '3', '10544', 'P');
INSERT INTO `ey_region` VALUES ('10602', '闸北区', '3', '10544', 'Z');
INSERT INTO `ey_region` VALUES ('10612', '虹口区', '3', '10544', 'H');
INSERT INTO `ey_region` VALUES ('10623', '杨浦区', '3', '10544', 'Y');
INSERT INTO `ey_region` VALUES ('10636', '闵行区', '3', '10544', 'M');
INSERT INTO `ey_region` VALUES ('10650', '宝山区', '3', '10544', 'B');
INSERT INTO `ey_region` VALUES ('10664', '嘉定区', '3', '10544', 'J');
INSERT INTO `ey_region` VALUES ('10678', '浦东新区', '3', '10544', 'P');
INSERT INTO `ey_region` VALUES ('10704', '金山区', '3', '10544', 'J');
INSERT INTO `ey_region` VALUES ('10715', '松江区', '3', '10544', 'S');
INSERT INTO `ey_region` VALUES ('10735', '青浦区', '3', '10544', 'Q');
INSERT INTO `ey_region` VALUES ('10747', '南汇区', '3', '10544', 'N');
INSERT INTO `ey_region` VALUES ('10765', '奉贤区', '3', '10544', 'F');
INSERT INTO `ey_region` VALUES ('10780', '崇明区', '3', '10544', 'C');
INSERT INTO `ey_region` VALUES ('10808', '江苏省', '1', '0', 'J');
INSERT INTO `ey_region` VALUES ('10809', '南京市', '2', '10808', 'N');
INSERT INTO `ey_region` VALUES ('10810', '市辖区', '3', '10809', 'S');
INSERT INTO `ey_region` VALUES ('10811', '玄武区', '3', '10809', 'X');
INSERT INTO `ey_region` VALUES ('10820', '白下区', '3', '10809', 'B');
INSERT INTO `ey_region` VALUES ('10831', '秦淮区', '3', '10809', 'Q');
INSERT INTO `ey_region` VALUES ('10837', '建邺区', '3', '10809', 'J');
INSERT INTO `ey_region` VALUES ('10845', '鼓楼区', '3', '10809', 'G');
INSERT INTO `ey_region` VALUES ('10853', '下关区', '3', '10809', 'X');
INSERT INTO `ey_region` VALUES ('10860', '浦口区', '3', '10809', 'P');
INSERT INTO `ey_region` VALUES ('10876', '栖霞区', '3', '10809', 'Q');
INSERT INTO `ey_region` VALUES ('10894', '雨花台区', '3', '10809', 'Y');
INSERT INTO `ey_region` VALUES ('10903', '江宁区', '3', '10809', 'J');
INSERT INTO `ey_region` VALUES ('10916', '六合区', '3', '10809', 'L');
INSERT INTO `ey_region` VALUES ('10937', '溧水县', '3', '10809', 'L');
INSERT INTO `ey_region` VALUES ('10947', '高淳县', '3', '10809', 'G');
INSERT INTO `ey_region` VALUES ('10960', '无锡市', '2', '10808', 'W');
INSERT INTO `ey_region` VALUES ('10961', '市辖区', '3', '10960', 'S');
INSERT INTO `ey_region` VALUES ('10962', '崇安区', '3', '10960', 'C');
INSERT INTO `ey_region` VALUES ('10969', '南长区', '3', '10960', 'N');
INSERT INTO `ey_region` VALUES ('10976', '北塘区', '3', '10960', 'B');
INSERT INTO `ey_region` VALUES ('10981', '锡山区', '3', '10960', 'X');
INSERT INTO `ey_region` VALUES ('10990', '惠山区', '3', '10960', 'H');
INSERT INTO `ey_region` VALUES ('10999', '滨湖区', '3', '10960', 'B');
INSERT INTO `ey_region` VALUES ('11018', '江阴市', '3', '10960', 'J');
INSERT INTO `ey_region` VALUES ('11039', '宜兴市', '3', '10960', 'Y');
INSERT INTO `ey_region` VALUES ('11067', '徐州市', '2', '10808', 'X');
INSERT INTO `ey_region` VALUES ('11068', '市辖区', '3', '11067', 'S');
INSERT INTO `ey_region` VALUES ('11069', '鼓楼区', '3', '11067', 'G');
INSERT INTO `ey_region` VALUES ('11081', '云龙区', '3', '11067', 'Y');
INSERT INTO `ey_region` VALUES ('11089', '九里区', '3', '11067', 'J');
INSERT INTO `ey_region` VALUES ('11103', '贾汪区', '3', '11067', 'J');
INSERT INTO `ey_region` VALUES ('11115', '泉山区', '3', '11067', 'Q');
INSERT INTO `ey_region` VALUES ('11126', '丰县', '3', '11067', 'F');
INSERT INTO `ey_region` VALUES ('11142', '沛县', '3', '11067', 'P');
INSERT INTO `ey_region` VALUES ('11160', '铜山县', '3', '11067', 'T');
INSERT INTO `ey_region` VALUES ('11182', '睢宁县', '3', '11067', 'S');
INSERT INTO `ey_region` VALUES ('11200', '新沂市', '3', '11067', 'X');
INSERT INTO `ey_region` VALUES ('11218', '邳州市', '3', '11067', 'P');
INSERT INTO `ey_region` VALUES ('11245', '常州市', '2', '10808', 'C');
INSERT INTO `ey_region` VALUES ('11246', '常州市区', '3', '11245', 'C');
INSERT INTO `ey_region` VALUES ('11247', '天宁区', '3', '11245', 'T');
INSERT INTO `ey_region` VALUES ('11254', '钟楼区', '3', '11245', 'Z');
INSERT INTO `ey_region` VALUES ('11262', '戚墅堰区', '3', '11245', 'Q');
INSERT INTO `ey_region` VALUES ('11266', '新北区', '3', '11245', 'X');
INSERT INTO `ey_region` VALUES ('11276', '武进区', '3', '11245', 'W');
INSERT INTO `ey_region` VALUES ('11311', '溧阳市', '3', '11245', 'L');
INSERT INTO `ey_region` VALUES ('11331', '金坛市', '3', '11245', 'J');
INSERT INTO `ey_region` VALUES ('11348', '苏州市', '2', '10808', 'S');
INSERT INTO `ey_region` VALUES ('11349', '市辖区', '3', '11348', 'S');
INSERT INTO `ey_region` VALUES ('11350', '沧浪区', '3', '11348', 'C');
INSERT INTO `ey_region` VALUES ('11357', '平江区', '3', '11348', 'P');
INSERT INTO `ey_region` VALUES ('11368', '金阊区', '3', '11348', 'J');
INSERT INTO `ey_region` VALUES ('11374', '苏州高新区虎丘区', '3', '11348', 'S');
INSERT INTO `ey_region` VALUES ('11387', '吴中区', '3', '11348', 'W');
INSERT INTO `ey_region` VALUES ('11409', '相城区', '3', '11348', 'X');
INSERT INTO `ey_region` VALUES ('11419', '常熟市', '3', '11348', 'C');
INSERT INTO `ey_region` VALUES ('11433', '张家港市', '3', '11348', 'Z');
INSERT INTO `ey_region` VALUES ('11448', '昆山市', '3', '11348', 'K');
INSERT INTO `ey_region` VALUES ('11460', '吴江市', '3', '11348', 'W');
INSERT INTO `ey_region` VALUES ('11472', '太仓市', '3', '11348', 'T');
INSERT INTO `ey_region` VALUES ('11482', '南通市', '2', '10808', 'N');
INSERT INTO `ey_region` VALUES ('11483', '市辖区', '3', '11482', 'S');
INSERT INTO `ey_region` VALUES ('11484', '崇川区', '3', '11482', 'C');
INSERT INTO `ey_region` VALUES ('11502', '港闸区', '3', '11482', 'G');
INSERT INTO `ey_region` VALUES ('11510', '海安县', '3', '11482', 'H');
INSERT INTO `ey_region` VALUES ('11526', '如东', '3', '11482', 'R');
INSERT INTO `ey_region` VALUES ('11542', '启东市', '3', '11482', 'Q');
INSERT INTO `ey_region` VALUES ('11568', '如皋市', '3', '11482', 'R');
INSERT INTO `ey_region` VALUES ('11600', '通州市', '3', '11482', 'T');
INSERT INTO `ey_region` VALUES ('11627', '海门市', '3', '11482', 'H');
INSERT INTO `ey_region` VALUES ('11663', '连云港市', '2', '10808', 'L');
INSERT INTO `ey_region` VALUES ('11664', '市辖区', '3', '11663', 'S');
INSERT INTO `ey_region` VALUES ('11665', '连云区', '3', '11663', 'L');
INSERT INTO `ey_region` VALUES ('11678', '新浦区', '3', '11663', 'X');
INSERT INTO `ey_region` VALUES ('11692', '海州区', '3', '11663', 'H');
INSERT INTO `ey_region` VALUES ('11699', '赣榆县', '3', '11663', 'G');
INSERT INTO `ey_region` VALUES ('11722', '东海县', '3', '11663', 'D');
INSERT INTO `ey_region` VALUES ('11747', '灌云县', '3', '11663', 'G');
INSERT INTO `ey_region` VALUES ('11771', '灌南县', '3', '11663', 'G');
INSERT INTO `ey_region` VALUES ('11786', '淮安市', '2', '10808', 'H');
INSERT INTO `ey_region` VALUES ('11787', '市辖区', '3', '11786', 'S');
INSERT INTO `ey_region` VALUES ('11788', '清河区', '3', '11786', 'Q');
INSERT INTO `ey_region` VALUES ('11801', '楚州区', '3', '11786', 'C');
INSERT INTO `ey_region` VALUES ('11830', '淮阴区', '3', '11786', 'H');
INSERT INTO `ey_region` VALUES ('11853', '清浦区', '3', '11786', 'Q');
INSERT INTO `ey_region` VALUES ('11863', '涟水县', '3', '11786', 'L');
INSERT INTO `ey_region` VALUES ('11896', '洪泽县', '3', '11786', 'H');
INSERT INTO `ey_region` VALUES ('11909', '盱眙县', '3', '11786', 'X');
INSERT INTO `ey_region` VALUES ('11931', '金湖县', '3', '11786', 'J');
INSERT INTO `ey_region` VALUES ('11947', '盐城市', '2', '10808', 'Y');
INSERT INTO `ey_region` VALUES ('11948', '市辖区', '3', '11947', 'S');
INSERT INTO `ey_region` VALUES ('11949', '亭湖区', '3', '11947', 'T');
INSERT INTO `ey_region` VALUES ('11967', '盐都区', '3', '11947', 'Y');
INSERT INTO `ey_region` VALUES ('11982', '响水县', '3', '11947', 'X');
INSERT INTO `ey_region` VALUES ('11998', '滨海县', '3', '11947', 'B');
INSERT INTO `ey_region` VALUES ('12017', '阜宁县', '3', '11947', 'F');
INSERT INTO `ey_region` VALUES ('12040', '射阳县', '3', '11947', 'S');
INSERT INTO `ey_region` VALUES ('12066', '建湖县', '3', '11947', 'J');
INSERT INTO `ey_region` VALUES ('12083', '东台市', '3', '11947', 'D');
INSERT INTO `ey_region` VALUES ('12117', '大丰市', '3', '11947', 'D');
INSERT INTO `ey_region` VALUES ('12135', '扬州市', '2', '10808', 'Y');
INSERT INTO `ey_region` VALUES ('12136', '市辖区', '3', '12135', 'S');
INSERT INTO `ey_region` VALUES ('12137', '广陵区', '3', '12135', 'G');
INSERT INTO `ey_region` VALUES ('12144', '邗江区', '3', '12135', 'H');
INSERT INTO `ey_region` VALUES ('12160', '维扬区', '3', '12135', 'W');
INSERT INTO `ey_region` VALUES ('12175', '宝应县', '3', '12135', 'B');
INSERT INTO `ey_region` VALUES ('12191', '仪征市', '3', '12135', 'Y');
INSERT INTO `ey_region` VALUES ('12212', '高邮市', '3', '12135', 'G');
INSERT INTO `ey_region` VALUES ('12235', '江都市', '3', '12135', 'J');
INSERT INTO `ey_region` VALUES ('12249', '镇江市', '2', '10808', 'Z');
INSERT INTO `ey_region` VALUES ('12250', '市区', '3', '12249', 'S');
INSERT INTO `ey_region` VALUES ('12251', '京口区', '3', '12249', 'J');
INSERT INTO `ey_region` VALUES ('12265', '润州区', '3', '12249', 'R');
INSERT INTO `ey_region` VALUES ('12273', '丹徒区', '3', '12249', 'D');
INSERT INTO `ey_region` VALUES ('12282', '丹阳市', '3', '12249', 'D');
INSERT INTO `ey_region` VALUES ('12300', '扬中市', '3', '12249', 'Y');
INSERT INTO `ey_region` VALUES ('12312', '句容市', '3', '12249', 'J');
INSERT INTO `ey_region` VALUES ('12343', '泰州市', '2', '10808', 'T');
INSERT INTO `ey_region` VALUES ('12344', '市辖区', '3', '12343', 'S');
INSERT INTO `ey_region` VALUES ('12345', '海陵区', '3', '12343', 'H');
INSERT INTO `ey_region` VALUES ('12362', '高港区', '3', '12343', 'G');
INSERT INTO `ey_region` VALUES ('12370', '兴化市', '3', '12343', 'X');
INSERT INTO `ey_region` VALUES ('12407', '靖江市', '3', '12343', 'J');
INSERT INTO `ey_region` VALUES ('12423', '泰兴市', '3', '12343', 'T');
INSERT INTO `ey_region` VALUES ('12450', '姜堰市', '3', '12343', 'J');
INSERT INTO `ey_region` VALUES ('12475', '宿迁市', '2', '10808', 'S');
INSERT INTO `ey_region` VALUES ('12476', '市辖区', '3', '12475', 'S');
INSERT INTO `ey_region` VALUES ('12477', '宿城区', '3', '12475', 'S');
INSERT INTO `ey_region` VALUES ('12496', '宿豫区', '3', '12475', 'S');
INSERT INTO `ey_region` VALUES ('12515', '沭阳县', '3', '12475', 'S');
INSERT INTO `ey_region` VALUES ('12551', '泗阳县', '3', '12475', 'S');
INSERT INTO `ey_region` VALUES ('12570', '泗洪县', '3', '12475', 'S');
INSERT INTO `ey_region` VALUES ('12596', '浙江省', '1', '0', 'Z');
INSERT INTO `ey_region` VALUES ('12597', '杭州市', '2', '12596', 'H');
INSERT INTO `ey_region` VALUES ('12598', '市辖区', '3', '12597', 'S');
INSERT INTO `ey_region` VALUES ('12599', '上城区', '3', '12597', 'S');
INSERT INTO `ey_region` VALUES ('12606', '下城区', '3', '12597', 'X');
INSERT INTO `ey_region` VALUES ('12615', '江干区', '3', '12597', 'J');
INSERT INTO `ey_region` VALUES ('12626', '拱墅区', '3', '12597', 'G');
INSERT INTO `ey_region` VALUES ('12637', '西湖区', '3', '12597', 'X');
INSERT INTO `ey_region` VALUES ('12652', '滨江区', '3', '12597', 'B');
INSERT INTO `ey_region` VALUES ('12656', '萧山区', '3', '12597', 'X');
INSERT INTO `ey_region` VALUES ('12685', '余杭区', '3', '12597', 'Y');
INSERT INTO `ey_region` VALUES ('12705', '桐庐县', '3', '12597', 'T');
INSERT INTO `ey_region` VALUES ('12719', '淳安县', '3', '12597', 'C');
INSERT INTO `ey_region` VALUES ('12743', '建德市', '3', '12597', 'J');
INSERT INTO `ey_region` VALUES ('12760', '富阳市', '3', '12597', 'F');
INSERT INTO `ey_region` VALUES ('12786', '临安市', '3', '12597', 'L');
INSERT INTO `ey_region` VALUES ('12813', '宁波市', '2', '12596', 'N');
INSERT INTO `ey_region` VALUES ('12814', '市辖区', '3', '12813', 'S');
INSERT INTO `ey_region` VALUES ('12815', '海曙区', '3', '12813', 'H');
INSERT INTO `ey_region` VALUES ('12824', '江东区', '3', '12813', 'J');
INSERT INTO `ey_region` VALUES ('12832', '江北区', '3', '12813', 'J');
INSERT INTO `ey_region` VALUES ('12841', '北仑区', '3', '12813', 'B');
INSERT INTO `ey_region` VALUES ('12851', '镇海区', '3', '12813', 'Z');
INSERT INTO `ey_region` VALUES ('12858', '鄞州区', '3', '12813', 'Y');
INSERT INTO `ey_region` VALUES ('12881', '象山县', '3', '12813', 'X');
INSERT INTO `ey_region` VALUES ('12900', '宁海县', '3', '12813', 'N');
INSERT INTO `ey_region` VALUES ('12919', '余姚市', '3', '12813', 'Y');
INSERT INTO `ey_region` VALUES ('12941', '慈溪市', '3', '12813', 'C');
INSERT INTO `ey_region` VALUES ('12962', '奉化市', '3', '12813', 'F');
INSERT INTO `ey_region` VALUES ('12974', '温州市', '2', '12596', 'W');
INSERT INTO `ey_region` VALUES ('12975', '市辖区', '3', '12974', 'S');
INSERT INTO `ey_region` VALUES ('12976', '鹿城区', '3', '12974', 'L');
INSERT INTO `ey_region` VALUES ('12998', '龙湾区', '3', '12974', 'L');
INSERT INTO `ey_region` VALUES ('13009', '瓯海区', '3', '12974', 'O');
INSERT INTO `ey_region` VALUES ('13023', '洞头县', '3', '12974', 'D');
INSERT INTO `ey_region` VALUES ('13030', '永嘉县', '3', '12974', 'Y');
INSERT INTO `ey_region` VALUES ('13069', '平阳县', '3', '12974', 'P');
INSERT INTO `ey_region` VALUES ('13101', '苍南县', '3', '12974', 'C');
INSERT INTO `ey_region` VALUES ('13138', '文成县', '3', '12974', 'W');
INSERT INTO `ey_region` VALUES ('13172', '泰顺县', '3', '12974', 'T');
INSERT INTO `ey_region` VALUES ('13209', '瑞安市', '3', '12974', 'R');
INSERT INTO `ey_region` VALUES ('13248', '乐清市', '3', '12974', 'L');
INSERT INTO `ey_region` VALUES ('13280', '嘉兴市', '2', '12596', 'J');
INSERT INTO `ey_region` VALUES ('13281', '市辖区', '3', '13280', 'S');
INSERT INTO `ey_region` VALUES ('13282', '南湖区', '3', '13280', 'N');
INSERT INTO `ey_region` VALUES ('13295', '秀洲区', '3', '13280', 'X');
INSERT INTO `ey_region` VALUES ('13304', '嘉善县', '3', '13280', 'J');
INSERT INTO `ey_region` VALUES ('13316', '海盐县', '3', '13280', 'H');
INSERT INTO `ey_region` VALUES ('13325', '海宁市', '3', '13280', 'H');
INSERT INTO `ey_region` VALUES ('13339', '平湖市', '3', '13280', 'P');
INSERT INTO `ey_region` VALUES ('13350', '桐乡市', '3', '13280', 'T');
INSERT INTO `ey_region` VALUES ('13364', '湖州市', '2', '12596', 'H');
INSERT INTO `ey_region` VALUES ('13365', '市辖区', '3', '13364', 'S');
INSERT INTO `ey_region` VALUES ('13366', '吴兴区', '3', '13364', 'W');
INSERT INTO `ey_region` VALUES ('13382', '南浔区', '3', '13364', 'N');
INSERT INTO `ey_region` VALUES ('13392', '德清县', '3', '13364', 'D');
INSERT INTO `ey_region` VALUES ('13404', '长兴县', '3', '13364', 'C');
INSERT INTO `ey_region` VALUES ('13421', '安吉县', '3', '13364', 'A');
INSERT INTO `ey_region` VALUES ('13437', '绍兴市', '2', '12596', 'S');
INSERT INTO `ey_region` VALUES ('13438', '市辖区', '3', '13437', 'S');
INSERT INTO `ey_region` VALUES ('13439', '越城区', '3', '13437', 'Y');
INSERT INTO `ey_region` VALUES ('13453', '绍兴县', '3', '13437', 'S');
INSERT INTO `ey_region` VALUES ('13473', '新昌县', '3', '13437', 'X');
INSERT INTO `ey_region` VALUES ('13490', '诸暨市', '3', '13437', 'Z');
INSERT INTO `ey_region` VALUES ('13518', '上虞市', '3', '13437', 'S');
INSERT INTO `ey_region` VALUES ('13542', '嵊州市', '3', '13437', 'S');
INSERT INTO `ey_region` VALUES ('13564', '金华市', '2', '12596', 'J');
INSERT INTO `ey_region` VALUES ('13565', '市辖区', '3', '13564', 'S');
INSERT INTO `ey_region` VALUES ('13566', '婺城区', '3', '13564', 'W');
INSERT INTO `ey_region` VALUES ('13594', '金东区', '3', '13564', 'J');
INSERT INTO `ey_region` VALUES ('13606', '武义县', '3', '13564', 'W');
INSERT INTO `ey_region` VALUES ('13625', '浦江县', '3', '13564', 'P');
INSERT INTO `ey_region` VALUES ('13641', '磐安县', '3', '13564', 'P');
INSERT INTO `ey_region` VALUES ('13662', '兰溪市', '3', '13564', 'L');
INSERT INTO `ey_region` VALUES ('13678', '义乌市', '3', '13564', 'Y');
INSERT INTO `ey_region` VALUES ('13692', '东阳市', '3', '13564', 'D');
INSERT INTO `ey_region` VALUES ('13711', '永康市', '3', '13564', 'Y');
INSERT INTO `ey_region` VALUES ('13726', '衢州市', '2', '12596', 'Q');
INSERT INTO `ey_region` VALUES ('13727', '市辖区', '3', '13726', 'S');
INSERT INTO `ey_region` VALUES ('13728', '柯城区', '3', '13726', 'K');
INSERT INTO `ey_region` VALUES ('13746', '衢江区', '3', '13726', 'Q');
INSERT INTO `ey_region` VALUES ('13768', '常山县', '3', '13726', 'C');
INSERT INTO `ey_region` VALUES ('13783', '开化县', '3', '13726', 'K');
INSERT INTO `ey_region` VALUES ('13802', '龙游县', '3', '13726', 'L');
INSERT INTO `ey_region` VALUES ('13818', '江山市', '3', '13726', 'J');
INSERT INTO `ey_region` VALUES ('13840', '舟山市', '2', '12596', 'Z');
INSERT INTO `ey_region` VALUES ('13841', '市辖区', '3', '13840', 'S');
INSERT INTO `ey_region` VALUES ('13842', '定海区', '3', '13840', 'D');
INSERT INTO `ey_region` VALUES ('13859', '普陀区', '3', '13840', 'P');
INSERT INTO `ey_region` VALUES ('13874', '岱山县', '3', '13840', 'D');
INSERT INTO `ey_region` VALUES ('13882', '嵊泗县', '3', '13840', 'S');
INSERT INTO `ey_region` VALUES ('13890', '台州市', '2', '12596', 'T');
INSERT INTO `ey_region` VALUES ('13891', '市辖区', '3', '13890', 'S');
INSERT INTO `ey_region` VALUES ('13892', '椒江区', '3', '13890', 'J');
INSERT INTO `ey_region` VALUES ('13903', '黄岩区', '3', '13890', 'H');
INSERT INTO `ey_region` VALUES ('13923', '路桥区', '3', '13890', 'L');
INSERT INTO `ey_region` VALUES ('13934', '玉环县', '3', '13890', 'Y');
INSERT INTO `ey_region` VALUES ('13944', '三门县', '3', '13890', 'S');
INSERT INTO `ey_region` VALUES ('13959', '天台县', '3', '13890', 'T');
INSERT INTO `ey_region` VALUES ('13975', '仙居县', '3', '13890', 'X');
INSERT INTO `ey_region` VALUES ('13996', '温岭市', '3', '13890', 'W');
INSERT INTO `ey_region` VALUES ('14013', '临海市', '3', '13890', 'L');
INSERT INTO `ey_region` VALUES ('14033', '丽水市', '2', '12596', 'L');
INSERT INTO `ey_region` VALUES ('14034', '市辖区', '3', '14033', 'S');
INSERT INTO `ey_region` VALUES ('14035', '莲都区', '3', '14033', 'L');
INSERT INTO `ey_region` VALUES ('14054', '青田县', '3', '14033', 'Q');
INSERT INTO `ey_region` VALUES ('14086', '缙云县', '3', '14033', 'J');
INSERT INTO `ey_region` VALUES ('14111', '遂昌县', '3', '14033', 'S');
INSERT INTO `ey_region` VALUES ('14132', '松阳县', '3', '14033', 'S');
INSERT INTO `ey_region` VALUES ('14153', '云和县', '3', '14033', 'Y');
INSERT INTO `ey_region` VALUES ('14168', '庆元县', '3', '14033', 'Q');
INSERT INTO `ey_region` VALUES ('14189', '景宁县', '3', '14033', 'J');
INSERT INTO `ey_region` VALUES ('14214', '龙泉市', '3', '14033', 'L');
INSERT INTO `ey_region` VALUES ('14234', '安徽省', '1', '0', 'A');
INSERT INTO `ey_region` VALUES ('14235', '合肥市', '2', '14234', 'H');
INSERT INTO `ey_region` VALUES ('14236', '市辖区', '3', '14235', 'S');
INSERT INTO `ey_region` VALUES ('14237', '瑶海区', '3', '14235', 'Y');
INSERT INTO `ey_region` VALUES ('14254', '庐阳区', '3', '14235', 'L');
INSERT INTO `ey_region` VALUES ('14269', '蜀山区', '3', '14235', 'S');
INSERT INTO `ey_region` VALUES ('14286', '包河区', '3', '14235', 'B');
INSERT INTO `ey_region` VALUES ('14297', '长丰县', '3', '14235', 'C');
INSERT INTO `ey_region` VALUES ('14314', '肥东县', '3', '14235', 'F');
INSERT INTO `ey_region` VALUES ('14334', '肥西县', '3', '14235', 'F');
INSERT INTO `ey_region` VALUES ('14351', '芜湖市', '2', '14234', 'W');
INSERT INTO `ey_region` VALUES ('14352', '市辖区', '3', '14351', 'S');
INSERT INTO `ey_region` VALUES ('14353', '镜湖区', '3', '14351', 'J');
INSERT INTO `ey_region` VALUES ('14366', '弋江区', '3', '14351', 'Y');
INSERT INTO `ey_region` VALUES ('14374', '鸠江区', '3', '14351', 'J');
INSERT INTO `ey_region` VALUES ('14382', '三山区', '3', '14351', 'S');
INSERT INTO `ey_region` VALUES ('14387', '芜湖县', '3', '14351', 'W');
INSERT INTO `ey_region` VALUES ('14394', '繁昌县', '3', '14351', 'F');
INSERT INTO `ey_region` VALUES ('14401', '南陵县', '3', '14351', 'N');
INSERT INTO `ey_region` VALUES ('14410', '蚌埠市', '2', '14234', 'B');
INSERT INTO `ey_region` VALUES ('14411', '市辖区', '3', '14410', 'S');
INSERT INTO `ey_region` VALUES ('14412', '龙子湖区', '3', '14410', 'L');
INSERT INTO `ey_region` VALUES ('14422', '蚌山区', '3', '14410', 'B');
INSERT INTO `ey_region` VALUES ('14434', '禹会区', '3', '14410', 'Y');
INSERT INTO `ey_region` VALUES ('14443', '淮上区', '3', '14410', 'H');
INSERT INTO `ey_region` VALUES ('14449', '怀远县', '3', '14410', 'H');
INSERT INTO `ey_region` VALUES ('14471', '五河县', '3', '14410', 'W');
INSERT INTO `ey_region` VALUES ('14487', '固镇县', '3', '14410', 'G');
INSERT INTO `ey_region` VALUES ('14500', '淮南市', '2', '14234', 'H');
INSERT INTO `ey_region` VALUES ('14501', '市辖区', '3', '14500', 'S');
INSERT INTO `ey_region` VALUES ('14502', '大通区', '3', '14500', 'D');
INSERT INTO `ey_region` VALUES ('14508', '田家庵区', '3', '14500', 'T');
INSERT INTO `ey_region` VALUES ('14523', '谢家集区', '3', '14500', 'X');
INSERT INTO `ey_region` VALUES ('14535', '八公山区', '3', '14500', 'B');
INSERT INTO `ey_region` VALUES ('14542', '潘集区', '3', '14500', 'P');
INSERT INTO `ey_region` VALUES ('14554', '凤台县', '3', '14500', 'F');
INSERT INTO `ey_region` VALUES ('14575', '马鞍山市', '2', '14234', 'M');
INSERT INTO `ey_region` VALUES ('14576', '市辖区', '3', '14575', 'S');
INSERT INTO `ey_region` VALUES ('14577', '金家庄区', '3', '14575', 'J');
INSERT INTO `ey_region` VALUES ('14583', '花山区', '3', '14575', 'H');
INSERT INTO `ey_region` VALUES ('14589', '雨山区', '3', '14575', 'Y');
INSERT INTO `ey_region` VALUES ('14597', '当涂县', '3', '14575', 'D');
INSERT INTO `ey_region` VALUES ('14612', '淮北市', '2', '14234', 'H');
INSERT INTO `ey_region` VALUES ('14613', '市辖区', '3', '14612', 'S');
INSERT INTO `ey_region` VALUES ('14614', '杜集区', '3', '14612', 'D');
INSERT INTO `ey_region` VALUES ('14620', '相山区', '3', '14612', 'X');
INSERT INTO `ey_region` VALUES ('14632', '烈山区', '3', '14612', 'L');
INSERT INTO `ey_region` VALUES ('14641', '濉溪县', '3', '14612', 'S');
INSERT INTO `ey_region` VALUES ('14653', '铜陵市', '2', '14234', 'T');
INSERT INTO `ey_region` VALUES ('14654', '市辖区', '3', '14653', 'S');
INSERT INTO `ey_region` VALUES ('14655', '铜官山区', '3', '14653', 'T');
INSERT INTO `ey_region` VALUES ('14663', '狮子山区', '3', '14653', 'S');
INSERT INTO `ey_region` VALUES ('14671', '铜陵市郊区', '3', '14653', 'T');
INSERT INTO `ey_region` VALUES ('14678', '铜陵县', '3', '14653', 'T');
INSERT INTO `ey_region` VALUES ('14687', '安庆市', '2', '14234', 'A');
INSERT INTO `ey_region` VALUES ('14688', '市辖区', '3', '14687', 'S');
INSERT INTO `ey_region` VALUES ('14689', '迎江区', '3', '14687', 'Y');
INSERT INTO `ey_region` VALUES ('14700', '大观区', '3', '14687', 'D');
INSERT INTO `ey_region` VALUES ('14712', '宜秀区', '3', '14687', 'Y');
INSERT INTO `ey_region` VALUES ('14720', '怀宁县', '3', '14687', 'H');
INSERT INTO `ey_region` VALUES ('14741', '枞阳县', '3', '14687', 'C');
INSERT INTO `ey_region` VALUES ('14764', '潜山县', '3', '14687', 'Q');
INSERT INTO `ey_region` VALUES ('14782', '太湖县', '3', '14687', 'T');
INSERT INTO `ey_region` VALUES ('14798', '宿松县', '3', '14687', 'S');
INSERT INTO `ey_region` VALUES ('14823', '望江县', '3', '14687', 'W');
INSERT INTO `ey_region` VALUES ('14834', '岳西县', '3', '14687', 'Y');
INSERT INTO `ey_region` VALUES ('14859', '桐城市', '3', '14687', 'T');
INSERT INTO `ey_region` VALUES ('14887', '黄山市', '2', '14234', 'H');
INSERT INTO `ey_region` VALUES ('14888', '市辖区', '3', '14887', 'S');
INSERT INTO `ey_region` VALUES ('14889', '屯溪区', '3', '14887', 'T');
INSERT INTO `ey_region` VALUES ('14900', '黄山区', '3', '14887', 'H');
INSERT INTO `ey_region` VALUES ('14917', '徽州区', '3', '14887', 'H');
INSERT INTO `ey_region` VALUES ('14926', '歙县', '3', '14887', 'S');
INSERT INTO `ey_region` VALUES ('14955', '休宁县', '3', '14887', 'X');
INSERT INTO `ey_region` VALUES ('14977', '黟县', '3', '14887', 'Y');
INSERT INTO `ey_region` VALUES ('14986', '祁门县', '3', '14887', 'Q');
INSERT INTO `ey_region` VALUES ('15005', '滁州市', '2', '14234', 'C');
INSERT INTO `ey_region` VALUES ('15006', '市辖区', '3', '15005', 'S');
INSERT INTO `ey_region` VALUES ('15007', '琅琊区', '3', '15005', 'L');
INSERT INTO `ey_region` VALUES ('15016', '南谯区', '3', '15005', 'N');
INSERT INTO `ey_region` VALUES ('15034', '来安县', '3', '15005', 'L');
INSERT INTO `ey_region` VALUES ('15053', '全椒县', '3', '15005', 'Q');
INSERT INTO `ey_region` VALUES ('15071', '定远县', '3', '15005', 'D');
INSERT INTO `ey_region` VALUES ('15109', '凤阳县', '3', '15005', 'F');
INSERT INTO `ey_region` VALUES ('15136', '天长市', '3', '15005', 'T');
INSERT INTO `ey_region` VALUES ('15166', '明光市', '3', '15005', 'M');
INSERT INTO `ey_region` VALUES ('15194', '阜阳市', '2', '14234', 'F');
INSERT INTO `ey_region` VALUES ('15195', '市辖区', '3', '15194', 'S');
INSERT INTO `ey_region` VALUES ('15196', '颍州区', '3', '15194', 'Y');
INSERT INTO `ey_region` VALUES ('15211', '颍东区', '3', '15194', 'Y');
INSERT INTO `ey_region` VALUES ('15224', '颍泉区', '3', '15194', 'Y');
INSERT INTO `ey_region` VALUES ('15231', '临泉县', '3', '15194', 'L');
INSERT INTO `ey_region` VALUES ('15264', '太和县', '3', '15194', 'T');
INSERT INTO `ey_region` VALUES ('15296', '阜南县', '3', '15194', 'F');
INSERT INTO `ey_region` VALUES ('15328', '颍上县', '3', '15194', 'Y');
INSERT INTO `ey_region` VALUES ('15359', '界首市', '3', '15194', 'J');
INSERT INTO `ey_region` VALUES ('15378', '宿州市', '2', '14234', 'S');
INSERT INTO `ey_region` VALUES ('15379', '市辖区', '3', '15378', 'S');
INSERT INTO `ey_region` VALUES ('15380', '墉桥区', '3', '15378', 'Y');
INSERT INTO `ey_region` VALUES ('15417', '砀山县', '3', '15378', 'D');
INSERT INTO `ey_region` VALUES ('15437', '萧县', '3', '15378', 'X');
INSERT INTO `ey_region` VALUES ('15461', '灵璧县', '3', '15378', 'L');
INSERT INTO `ey_region` VALUES ('15482', '泗县', '3', '15378', 'S');
INSERT INTO `ey_region` VALUES ('15499', '巢湖市', '2', '14234', 'C');
INSERT INTO `ey_region` VALUES ('15500', '市辖区', '3', '15499', 'S');
INSERT INTO `ey_region` VALUES ('15501', '居巢区', '3', '15499', 'J');
INSERT INTO `ey_region` VALUES ('15520', '庐江县', '3', '15499', 'L');
INSERT INTO `ey_region` VALUES ('15542', '无为县', '3', '15499', 'W');
INSERT INTO `ey_region` VALUES ('15566', '含山县', '3', '15499', 'H');
INSERT INTO `ey_region` VALUES ('15575', '和县', '3', '15499', 'H');
INSERT INTO `ey_region` VALUES ('15586', '六安市', '2', '14234', 'L');
INSERT INTO `ey_region` VALUES ('15587', '市辖区', '3', '15586', 'S');
INSERT INTO `ey_region` VALUES ('15588', '金安区', '3', '15586', 'J');
INSERT INTO `ey_region` VALUES ('15612', '裕安区', '3', '15586', 'Y');
INSERT INTO `ey_region` VALUES ('15635', '寿县', '3', '15586', 'S');
INSERT INTO `ey_region` VALUES ('15662', '霍邱县', '3', '15586', 'H');
INSERT INTO `ey_region` VALUES ('15698', '舒城县', '3', '15586', 'S');
INSERT INTO `ey_region` VALUES ('15720', '金寨县', '3', '15586', 'J');
INSERT INTO `ey_region` VALUES ('15747', '霍山县', '3', '15586', 'H');
INSERT INTO `ey_region` VALUES ('15764', '亳州市', '2', '14234', 'H');
INSERT INTO `ey_region` VALUES ('15765', '市辖区', '3', '15764', 'S');
INSERT INTO `ey_region` VALUES ('15766', '谯城区', '3', '15764', 'Q');
INSERT INTO `ey_region` VALUES ('15795', '涡阳县', '3', '15764', 'W');
INSERT INTO `ey_region` VALUES ('15823', '蒙城县', '3', '15764', 'M');
INSERT INTO `ey_region` VALUES ('15843', '利辛县', '3', '15764', 'L');
INSERT INTO `ey_region` VALUES ('15871', '池州市', '2', '14234', 'C');
INSERT INTO `ey_region` VALUES ('15872', '市辖区', '3', '15871', 'S');
INSERT INTO `ey_region` VALUES ('15873', '贵池区', '3', '15871', 'G');
INSERT INTO `ey_region` VALUES ('15900', '东至县', '3', '15871', 'D');
INSERT INTO `ey_region` VALUES ('15930', '石台县', '3', '15871', 'S');
INSERT INTO `ey_region` VALUES ('15944', '青阳县', '3', '15871', 'Q');
INSERT INTO `ey_region` VALUES ('15958', '宣城市', '2', '14234', 'X');
INSERT INTO `ey_region` VALUES ('15959', '市辖区', '3', '15958', 'S');
INSERT INTO `ey_region` VALUES ('15960', '宣州区', '3', '15958', 'X');
INSERT INTO `ey_region` VALUES ('15987', '郎溪县', '3', '15958', 'L');
INSERT INTO `ey_region` VALUES ('16001', '广德县', '3', '15958', 'G');
INSERT INTO `ey_region` VALUES ('16013', '泾县', '3', '15958', 'J');
INSERT INTO `ey_region` VALUES ('16025', '绩溪县', '3', '15958', 'J');
INSERT INTO `ey_region` VALUES ('16037', '旌德县', '3', '15958', 'J');
INSERT INTO `ey_region` VALUES ('16048', '宁国市', '3', '15958', 'N');
INSERT INTO `ey_region` VALUES ('16068', '福建省', '1', '0', 'F');
INSERT INTO `ey_region` VALUES ('16069', '福州市', '2', '16068', 'F');
INSERT INTO `ey_region` VALUES ('16070', '市辖区', '3', '16069', 'S');
INSERT INTO `ey_region` VALUES ('16071', '鼓楼区', '3', '16069', 'G');
INSERT INTO `ey_region` VALUES ('16082', '台江区', '3', '16069', 'T');
INSERT INTO `ey_region` VALUES ('16093', '仓山区', '3', '16069', 'C');
INSERT INTO `ey_region` VALUES ('16108', '马尾区', '3', '16069', 'M');
INSERT INTO `ey_region` VALUES ('16113', '晋安区', '3', '16069', 'J');
INSERT INTO `ey_region` VALUES ('16123', '闽侯县', '3', '16069', 'M');
INSERT INTO `ey_region` VALUES ('16140', '连江县', '3', '16069', 'L');
INSERT INTO `ey_region` VALUES ('16164', '罗源县', '3', '16069', 'L');
INSERT INTO `ey_region` VALUES ('16177', '闽清县', '3', '16069', 'M');
INSERT INTO `ey_region` VALUES ('16194', '永泰县', '3', '16069', 'Y');
INSERT INTO `ey_region` VALUES ('16216', '平潭县', '3', '16069', 'P');
INSERT INTO `ey_region` VALUES ('16232', '福清市', '3', '16069', 'F');
INSERT INTO `ey_region` VALUES ('16259', '长乐市', '3', '16069', 'C');
INSERT INTO `ey_region` VALUES ('16278', '厦门市', '2', '16068', 'X');
INSERT INTO `ey_region` VALUES ('16279', '市辖区', '3', '16278', 'S');
INSERT INTO `ey_region` VALUES ('16280', '思明区', '3', '16278', 'S');
INSERT INTO `ey_region` VALUES ('16294', '海沧区', '3', '16278', 'H');
INSERT INTO `ey_region` VALUES ('16303', '湖里区', '3', '16278', 'H');
INSERT INTO `ey_region` VALUES ('16315', '集美区', '3', '16278', 'J');
INSERT INTO `ey_region` VALUES ('16326', '同安区', '3', '16278', 'T');
INSERT INTO `ey_region` VALUES ('16341', '翔安区', '3', '16278', 'X');
INSERT INTO `ey_region` VALUES ('16348', '莆田市', '2', '16068', 'P');
INSERT INTO `ey_region` VALUES ('16349', '市辖区', '3', '16348', 'S');
INSERT INTO `ey_region` VALUES ('16350', '城厢区', '3', '16348', 'C');
INSERT INTO `ey_region` VALUES ('16358', '涵江区', '3', '16348', 'H');
INSERT INTO `ey_region` VALUES ('16372', '荔城区', '3', '16348', 'L');
INSERT INTO `ey_region` VALUES ('16379', '秀屿区', '3', '16348', 'X');
INSERT INTO `ey_region` VALUES ('16393', '仙游县', '3', '16348', 'X');
INSERT INTO `ey_region` VALUES ('16412', '三明市', '2', '16068', 'S');
INSERT INTO `ey_region` VALUES ('16413', '市辖区', '3', '16412', 'S');
INSERT INTO `ey_region` VALUES ('16414', '梅列区', '3', '16412', 'M');
INSERT INTO `ey_region` VALUES ('16421', '三元区', '3', '16412', 'S');
INSERT INTO `ey_region` VALUES ('16430', '明溪县', '3', '16412', 'M');
INSERT INTO `ey_region` VALUES ('16440', '清流县', '3', '16412', 'Q');
INSERT INTO `ey_region` VALUES ('16455', '宁化县', '3', '16412', 'N');
INSERT INTO `ey_region` VALUES ('16472', '大田县', '3', '16412', 'D');
INSERT INTO `ey_region` VALUES ('16492', '尤溪县', '3', '16412', 'Y');
INSERT INTO `ey_region` VALUES ('16508', '沙县', '3', '16412', 'S');
INSERT INTO `ey_region` VALUES ('16521', '将乐县', '3', '16412', 'J');
INSERT INTO `ey_region` VALUES ('16535', '泰宁县', '3', '16412', 'T');
INSERT INTO `ey_region` VALUES ('16545', '建宁县', '3', '16412', 'J');
INSERT INTO `ey_region` VALUES ('16556', '永安市', '3', '16412', 'Y');
INSERT INTO `ey_region` VALUES ('16572', '泉州市', '2', '16068', 'Q');
INSERT INTO `ey_region` VALUES ('16573', '市辖区', '3', '16572', 'S');
INSERT INTO `ey_region` VALUES ('16574', '鲤城区', '3', '16572', 'L');
INSERT INTO `ey_region` VALUES ('16584', '丰泽区', '3', '16572', 'F');
INSERT INTO `ey_region` VALUES ('16593', '洛江区', '3', '16572', 'L');
INSERT INTO `ey_region` VALUES ('16600', '泉港区', '3', '16572', 'Q');
INSERT INTO `ey_region` VALUES ('16608', '惠安县', '3', '16572', 'H');
INSERT INTO `ey_region` VALUES ('16625', '安溪县', '3', '16572', 'A');
INSERT INTO `ey_region` VALUES ('16650', '永春县', '3', '16572', 'Y');
INSERT INTO `ey_region` VALUES ('16673', '德化县', '3', '16572', 'D');
INSERT INTO `ey_region` VALUES ('16692', '金门县', '3', '16572', 'J');
INSERT INTO `ey_region` VALUES ('16693', '石狮市', '3', '16572', 'S');
INSERT INTO `ey_region` VALUES ('16703', '晋江市', '3', '16572', 'J');
INSERT INTO `ey_region` VALUES ('16726', '南安市', '3', '16572', 'N');
INSERT INTO `ey_region` VALUES ('16754', '漳州市', '2', '16068', 'Z');
INSERT INTO `ey_region` VALUES ('16755', '市辖区', '3', '16754', 'S');
INSERT INTO `ey_region` VALUES ('16756', '芗城区', '3', '16754', 'X');
INSERT INTO `ey_region` VALUES ('16772', '龙文区', '3', '16754', 'L');
INSERT INTO `ey_region` VALUES ('16778', '云霄县', '3', '16754', 'Y');
INSERT INTO `ey_region` VALUES ('16790', '漳浦县', '3', '16754', 'Z');
INSERT INTO `ey_region` VALUES ('16821', '诏安县', '3', '16754', 'Z');
INSERT INTO `ey_region` VALUES ('16842', '长泰县', '3', '16754', 'C');
INSERT INTO `ey_region` VALUES ('16852', '东山县', '3', '16754', 'D');
INSERT INTO `ey_region` VALUES ('16860', '南靖县', '3', '16754', 'N');
INSERT INTO `ey_region` VALUES ('16872', '平和县', '3', '16754', 'P');
INSERT INTO `ey_region` VALUES ('16889', '华安县', '3', '16754', 'H');
INSERT INTO `ey_region` VALUES ('16899', '龙海市', '3', '16754', 'L');
INSERT INTO `ey_region` VALUES ('16924', '南平市', '2', '16068', 'N');
INSERT INTO `ey_region` VALUES ('16925', '市辖区', '3', '16924', 'S');
INSERT INTO `ey_region` VALUES ('16926', '延平区', '3', '16924', 'Y');
INSERT INTO `ey_region` VALUES ('16948', '顺昌县', '3', '16924', 'S');
INSERT INTO `ey_region` VALUES ('16961', '浦城县', '3', '16924', 'P');
INSERT INTO `ey_region` VALUES ('16982', '光泽县', '3', '16924', 'G');
INSERT INTO `ey_region` VALUES ('16991', '松溪县', '3', '16924', 'S');
INSERT INTO `ey_region` VALUES ('17001', '政和县', '3', '16924', 'Z');
INSERT INTO `ey_region` VALUES ('17012', '邵武市', '3', '16924', 'S');
INSERT INTO `ey_region` VALUES ('17033', '武夷山市', '3', '16924', 'W');
INSERT INTO `ey_region` VALUES ('17044', '建瓯市', '3', '16924', 'J');
INSERT INTO `ey_region` VALUES ('17063', '建阳市', '3', '16924', 'J');
INSERT INTO `ey_region` VALUES ('17077', '龙岩市', '2', '16068', 'L');
INSERT INTO `ey_region` VALUES ('17078', '市辖区', '3', '17077', 'S');
INSERT INTO `ey_region` VALUES ('17079', '新罗区', '3', '17077', 'X');
INSERT INTO `ey_region` VALUES ('17099', '长汀县', '3', '17077', 'C');
INSERT INTO `ey_region` VALUES ('17118', '永定县', '3', '17077', 'Y');
INSERT INTO `ey_region` VALUES ('17143', '上杭县', '3', '17077', 'S');
INSERT INTO `ey_region` VALUES ('17166', '武平县', '3', '17077', 'W');
INSERT INTO `ey_region` VALUES ('17184', '连城县', '3', '17077', 'L');
INSERT INTO `ey_region` VALUES ('17202', '漳平市', '3', '17077', 'Z');
INSERT INTO `ey_region` VALUES ('17219', '宁德市　', '2', '16068', 'N');
INSERT INTO `ey_region` VALUES ('17220', '市辖区', '3', '17219', 'S');
INSERT INTO `ey_region` VALUES ('17221', '蕉城区', '3', '17219', 'J');
INSERT INTO `ey_region` VALUES ('17239', '霞浦县', '3', '17219', 'X');
INSERT INTO `ey_region` VALUES ('17254', '古田县', '3', '17219', 'G');
INSERT INTO `ey_region` VALUES ('17269', '屏南县', '3', '17219', 'P');
INSERT INTO `ey_region` VALUES ('17281', '寿宁县', '3', '17219', 'S');
INSERT INTO `ey_region` VALUES ('17296', '周宁县', '3', '17219', 'Z');
INSERT INTO `ey_region` VALUES ('17306', '柘荣县', '3', '17219', 'Z');
INSERT INTO `ey_region` VALUES ('17316', '福安市', '3', '17219', 'F');
INSERT INTO `ey_region` VALUES ('17341', '福鼎市', '3', '17219', 'F');
INSERT INTO `ey_region` VALUES ('17359', '江西省', '1', '0', 'J');
INSERT INTO `ey_region` VALUES ('17360', '南昌市', '2', '17359', 'N');
INSERT INTO `ey_region` VALUES ('17361', '市辖区', '3', '17360', 'S');
INSERT INTO `ey_region` VALUES ('17362', '东湖区', '3', '17360', 'D');
INSERT INTO `ey_region` VALUES ('17374', '西湖区', '3', '17360', 'X');
INSERT INTO `ey_region` VALUES ('17387', '青云谱区', '3', '17360', 'Q');
INSERT INTO `ey_region` VALUES ('17395', '湾里区', '3', '17360', 'W');
INSERT INTO `ey_region` VALUES ('17402', '青山湖区', '3', '17360', 'Q');
INSERT INTO `ey_region` VALUES ('17420', '南昌县', '3', '17360', 'N');
INSERT INTO `ey_region` VALUES ('17443', '新建县', '3', '17360', 'X');
INSERT INTO `ey_region` VALUES ('17471', '安义县', '3', '17360', 'A');
INSERT INTO `ey_region` VALUES ('17485', '进贤县', '3', '17360', 'J');
INSERT INTO `ey_region` VALUES ('17508', '景德镇市', '2', '17359', 'J');
INSERT INTO `ey_region` VALUES ('17509', '市辖区', '3', '17508', 'S');
INSERT INTO `ey_region` VALUES ('17510', '昌江区', '3', '17508', 'C');
INSERT INTO `ey_region` VALUES ('17534', '珠山区', '3', '17508', 'Z');
INSERT INTO `ey_region` VALUES ('17545', '浮梁县', '3', '17508', 'F');
INSERT INTO `ey_region` VALUES ('17568', '乐平市', '3', '17508', 'L');
INSERT INTO `ey_region` VALUES ('17589', '萍乡市', '2', '17359', 'P');
INSERT INTO `ey_region` VALUES ('17590', '市辖区', '3', '17589', 'S');
INSERT INTO `ey_region` VALUES ('17591', '安源区', '3', '17589', 'A');
INSERT INTO `ey_region` VALUES ('17604', '湘东区', '3', '17589', 'X');
INSERT INTO `ey_region` VALUES ('17616', '莲花县', '3', '17589', 'L');
INSERT INTO `ey_region` VALUES ('17630', '上栗县', '3', '17589', 'S');
INSERT INTO `ey_region` VALUES ('17640', '芦溪县', '3', '17589', 'L');
INSERT INTO `ey_region` VALUES ('17651', '九江市', '2', '17359', 'J');
INSERT INTO `ey_region` VALUES ('17652', '市辖区', '3', '17651', 'S');
INSERT INTO `ey_region` VALUES ('17653', '庐山区', '3', '17651', 'L');
INSERT INTO `ey_region` VALUES ('17667', '浔阳区', '3', '17651', 'X');
INSERT INTO `ey_region` VALUES ('17676', '九江县', '3', '17651', 'J');
INSERT INTO `ey_region` VALUES ('17693', '武宁县', '3', '17651', 'W');
INSERT INTO `ey_region` VALUES ('17714', '修水县', '3', '17651', 'X');
INSERT INTO `ey_region` VALUES ('17751', '永修县', '3', '17651', 'Y');
INSERT INTO `ey_region` VALUES ('17773', '德安县', '3', '17651', 'D');
INSERT INTO `ey_region` VALUES ('17792', '星子县', '3', '17651', 'X');
INSERT INTO `ey_region` VALUES ('17807', '都昌县', '3', '17651', 'D');
INSERT INTO `ey_region` VALUES ('17834', '湖口县', '3', '17651', 'H');
INSERT INTO `ey_region` VALUES ('17849', '彭泽县', '3', '17651', 'P');
INSERT INTO `ey_region` VALUES ('17872', '瑞昌市', '3', '17651', 'R');
INSERT INTO `ey_region` VALUES ('17894', '新余市', '2', '17359', 'X');
INSERT INTO `ey_region` VALUES ('17895', '市辖区', '3', '17894', 'S');
INSERT INTO `ey_region` VALUES ('17896', '渝水区', '3', '17894', 'Y');
INSERT INTO `ey_region` VALUES ('17917', '分宜县', '3', '17894', 'F');
INSERT INTO `ey_region` VALUES ('17934', '鹰潭市', '2', '17359', 'Y');
INSERT INTO `ey_region` VALUES ('17935', '市辖区', '3', '17934', 'S');
INSERT INTO `ey_region` VALUES ('17936', '月湖区', '3', '17934', 'Y');
INSERT INTO `ey_region` VALUES ('17945', '余江县', '3', '17934', 'Y');
INSERT INTO `ey_region` VALUES ('17966', '贵溪市', '3', '17934', 'G');
INSERT INTO `ey_region` VALUES ('17999', '赣州市', '2', '17359', 'G');
INSERT INTO `ey_region` VALUES ('18000', '市辖区', '3', '17999', 'S');
INSERT INTO `ey_region` VALUES ('18001', '章贡区', '3', '17999', 'Z');
INSERT INTO `ey_region` VALUES ('18016', '赣县', '3', '17999', 'G');
INSERT INTO `ey_region` VALUES ('18037', '信丰县', '3', '17999', 'X');
INSERT INTO `ey_region` VALUES ('18055', '大余县', '3', '17999', 'D');
INSERT INTO `ey_region` VALUES ('18068', '上犹县', '3', '17999', 'S');
INSERT INTO `ey_region` VALUES ('18084', '崇义县', '3', '17999', 'C');
INSERT INTO `ey_region` VALUES ('18102', '安远县', '3', '17999', 'A');
INSERT INTO `ey_region` VALUES ('18122', '龙南县', '3', '17999', 'L');
INSERT INTO `ey_region` VALUES ('18139', '定南县', '3', '17999', 'D');
INSERT INTO `ey_region` VALUES ('18148', '全南县', '3', '17999', 'Q');
INSERT INTO `ey_region` VALUES ('18161', '宁都县', '3', '17999', 'N');
INSERT INTO `ey_region` VALUES ('18187', '于都县', '3', '17999', 'Y');
INSERT INTO `ey_region` VALUES ('18212', '兴国县', '3', '17999', 'X');
INSERT INTO `ey_region` VALUES ('18239', '会昌县', '3', '17999', 'H');
INSERT INTO `ey_region` VALUES ('18260', '寻乌县', '3', '17999', 'X');
INSERT INTO `ey_region` VALUES ('18276', '石城县', '3', '17999', 'S');
INSERT INTO `ey_region` VALUES ('18287', '瑞金市', '3', '17999', 'R');
INSERT INTO `ey_region` VALUES ('18306', '南康市', '3', '17999', 'N');
INSERT INTO `ey_region` VALUES ('18330', '吉安市', '2', '17359', 'J');
INSERT INTO `ey_region` VALUES ('18331', '市辖区', '3', '18330', 'S');
INSERT INTO `ey_region` VALUES ('18332', '吉州区', '3', '18330', 'J');
INSERT INTO `ey_region` VALUES ('18345', '青原区', '3', '18330', 'Q');
INSERT INTO `ey_region` VALUES ('18356', '吉安县', '3', '18330', 'J');
INSERT INTO `ey_region` VALUES ('18378', '吉水县', '3', '18330', 'J');
INSERT INTO `ey_region` VALUES ('18398', '峡江县', '3', '18330', 'X');
INSERT INTO `ey_region` VALUES ('18411', '新干县', '3', '18330', 'X');
INSERT INTO `ey_region` VALUES ('18429', '永丰县', '3', '18330', 'Y');
INSERT INTO `ey_region` VALUES ('18454', '泰和县', '3', '18330', 'T');
INSERT INTO `ey_region` VALUES ('18483', '遂川县', '3', '18330', 'S');
INSERT INTO `ey_region` VALUES ('18510', '万安县', '3', '18330', 'W');
INSERT INTO `ey_region` VALUES ('18529', '安福县', '3', '18330', 'A');
INSERT INTO `ey_region` VALUES ('18550', '永新县', '3', '18330', 'Y');
INSERT INTO `ey_region` VALUES ('18575', '井冈山市', '3', '18330', 'J');
INSERT INTO `ey_region` VALUES ('18598', '宜春市', '2', '17359', 'Y');
INSERT INTO `ey_region` VALUES ('18599', '市辖区', '3', '18598', 'S');
INSERT INTO `ey_region` VALUES ('18600', '袁州区', '3', '18598', 'Y');
INSERT INTO `ey_region` VALUES ('18639', '奉新县', '3', '18598', 'F');
INSERT INTO `ey_region` VALUES ('18659', '万载县', '3', '18598', 'W');
INSERT INTO `ey_region` VALUES ('18678', '上高县', '3', '18598', 'S');
INSERT INTO `ey_region` VALUES ('18696', '宜丰县', '3', '18598', 'Y');
INSERT INTO `ey_region` VALUES ('18714', '靖安县', '3', '18598', 'J');
INSERT INTO `ey_region` VALUES ('18727', '铜鼓县', '3', '18598', 'T');
INSERT INTO `ey_region` VALUES ('18741', '丰城市', '3', '18598', 'F');
INSERT INTO `ey_region` VALUES ('18777', '樟树市', '3', '18598', 'Z');
INSERT INTO `ey_region` VALUES ('18799', '高安市', '3', '18598', 'G');
INSERT INTO `ey_region` VALUES ('18829', '抚州市', '2', '17359', 'F');
INSERT INTO `ey_region` VALUES ('18830', '市辖区', '3', '18829', 'S');
INSERT INTO `ey_region` VALUES ('18831', '临川区', '3', '18829', 'L');
INSERT INTO `ey_region` VALUES ('18869', '南城县', '3', '18829', 'N');
INSERT INTO `ey_region` VALUES ('18882', '黎川县', '3', '18829', 'L');
INSERT INTO `ey_region` VALUES ('18900', '南丰县', '3', '18829', 'N');
INSERT INTO `ey_region` VALUES ('18915', '崇仁县', '3', '18829', 'C');
INSERT INTO `ey_region` VALUES ('18931', '乐安县', '3', '18829', 'L');
INSERT INTO `ey_region` VALUES ('18949', '宜黄县', '3', '18829', 'Y');
INSERT INTO `ey_region` VALUES ('18965', '金溪县', '3', '18829', 'J');
INSERT INTO `ey_region` VALUES ('18980', '资溪县', '3', '18829', 'Z');
INSERT INTO `ey_region` VALUES ('18988', '东乡县', '3', '18829', 'D');
INSERT INTO `ey_region` VALUES ('19010', '广昌县', '3', '18829', 'G');
INSERT INTO `ey_region` VALUES ('19024', '上饶市', '2', '17359', 'S');
INSERT INTO `ey_region` VALUES ('19025', '市辖区', '3', '19024', 'S');
INSERT INTO `ey_region` VALUES ('19026', '信州区', '3', '19024', 'X');
INSERT INTO `ey_region` VALUES ('19038', '上饶县', '3', '19024', 'S');
INSERT INTO `ey_region` VALUES ('19062', '广丰县', '3', '19024', 'G');
INSERT INTO `ey_region` VALUES ('19088', '玉山县', '3', '19024', 'Y');
INSERT INTO `ey_region` VALUES ('19108', '铅山县', '3', '19024', 'Q');
INSERT INTO `ey_region` VALUES ('19136', '横峰县', '3', '19024', 'H');
INSERT INTO `ey_region` VALUES ('19151', '弋阳县', '3', '19024', 'Y');
INSERT INTO `ey_region` VALUES ('19171', '余干县', '3', '19024', 'Y');
INSERT INTO `ey_region` VALUES ('19202', '鄱阳县', '3', '19024', 'P');
INSERT INTO `ey_region` VALUES ('19234', '万年县', '3', '19024', 'W');
INSERT INTO `ey_region` VALUES ('19248', '婺源县', '3', '19024', 'W');
INSERT INTO `ey_region` VALUES ('19265', '德兴市', '3', '19024', 'D');
INSERT INTO `ey_region` VALUES ('19280', '山东省', '1', '0', 'S');
INSERT INTO `ey_region` VALUES ('19281', '济南市', '2', '19280', 'J');
INSERT INTO `ey_region` VALUES ('19282', '市辖区', '3', '19281', 'S');
INSERT INTO `ey_region` VALUES ('19283', '历下区', '3', '19281', 'L');
INSERT INTO `ey_region` VALUES ('19295', '市中区', '3', '19281', 'S');
INSERT INTO `ey_region` VALUES ('19311', '槐荫区', '3', '19281', 'H');
INSERT INTO `ey_region` VALUES ('19326', '天桥区', '3', '19281', 'T');
INSERT INTO `ey_region` VALUES ('19342', '历城区', '3', '19281', 'L');
INSERT INTO `ey_region` VALUES ('19359', '长清区', '3', '19281', 'C');
INSERT INTO `ey_region` VALUES ('19370', '平阴县', '3', '19281', 'P');
INSERT INTO `ey_region` VALUES ('19378', '济阳县', '3', '19281', 'J');
INSERT INTO `ey_region` VALUES ('19387', '商河县', '3', '19281', 'S');
INSERT INTO `ey_region` VALUES ('19400', '章丘市', '3', '19281', 'Z');
INSERT INTO `ey_region` VALUES ('19421', '青岛市', '2', '19280', 'Q');
INSERT INTO `ey_region` VALUES ('19422', '市辖区', '3', '19421', 'S');
INSERT INTO `ey_region` VALUES ('19423', '市南区', '3', '19421', 'S');
INSERT INTO `ey_region` VALUES ('19438', '市北区', '3', '19421', 'S');
INSERT INTO `ey_region` VALUES ('19456', '四方区', '3', '19421', 'S');
INSERT INTO `ey_region` VALUES ('19464', '黄岛区', '3', '19421', 'H');
INSERT INTO `ey_region` VALUES ('19471', '崂山区', '3', '19421', 'L');
INSERT INTO `ey_region` VALUES ('19476', '李沧区', '3', '19421', 'L');
INSERT INTO `ey_region` VALUES ('19488', '城阳区', '3', '19421', 'C');
INSERT INTO `ey_region` VALUES ('19497', '胶州市', '3', '19421', 'J');
INSERT INTO `ey_region` VALUES ('19516', '即墨市', '3', '19421', 'J');
INSERT INTO `ey_region` VALUES ('19540', '平度市', '3', '19421', 'P');
INSERT INTO `ey_region` VALUES ('19572', '胶南市', '3', '19421', 'J');
INSERT INTO `ey_region` VALUES ('19590', '莱西市', '3', '19421', 'L');
INSERT INTO `ey_region` VALUES ('19608', '淄博市', '2', '19280', 'Z');
INSERT INTO `ey_region` VALUES ('19609', '市辖区', '3', '19608', 'S');
INSERT INTO `ey_region` VALUES ('19610', '淄川区', '3', '19608', 'Z');
INSERT INTO `ey_region` VALUES ('19632', '张店区', '3', '19608', 'Z');
INSERT INTO `ey_region` VALUES ('19649', '博山区', '3', '19608', 'B');
INSERT INTO `ey_region` VALUES ('19663', '临淄区', '3', '19608', 'L');
INSERT INTO `ey_region` VALUES ('19678', '周村区', '3', '19608', 'Z');
INSERT INTO `ey_region` VALUES ('19688', '桓台县', '3', '19608', 'H');
INSERT INTO `ey_region` VALUES ('19700', '高青县', '3', '19608', 'G');
INSERT INTO `ey_region` VALUES ('19710', '沂源县', '3', '19608', 'Y');
INSERT INTO `ey_region` VALUES ('19724', '枣庄市', '2', '19280', 'Z');
INSERT INTO `ey_region` VALUES ('19725', '市辖区', '3', '19724', 'S');
INSERT INTO `ey_region` VALUES ('19726', '市中区', '3', '19724', 'S');
INSERT INTO `ey_region` VALUES ('19738', '薛城区', '3', '19724', 'X');
INSERT INTO `ey_region` VALUES ('19748', '峄城区', '3', '19724', 'Y');
INSERT INTO `ey_region` VALUES ('19756', '台儿庄区', '3', '19724', 'T');
INSERT INTO `ey_region` VALUES ('19763', '山亭区', '3', '19724', 'S');
INSERT INTO `ey_region` VALUES ('19774', '滕州市', '3', '19724', 'T');
INSERT INTO `ey_region` VALUES ('19796', '东营市', '2', '19280', 'D');
INSERT INTO `ey_region` VALUES ('19797', '市辖区', '3', '19796', 'S');
INSERT INTO `ey_region` VALUES ('19798', '东营区', '3', '19796', 'D');
INSERT INTO `ey_region` VALUES ('19809', '河口区', '3', '19796', 'H');
INSERT INTO `ey_region` VALUES ('19817', '垦利县', '3', '19796', 'K');
INSERT INTO `ey_region` VALUES ('19825', '利津县', '3', '19796', 'L');
INSERT INTO `ey_region` VALUES ('19835', '广饶县', '3', '19796', 'G');
INSERT INTO `ey_region` VALUES ('19846', '烟台市', '2', '19280', 'Y');
INSERT INTO `ey_region` VALUES ('19847', '市辖区', '3', '19846', 'S');
INSERT INTO `ey_region` VALUES ('19848', '芝罘区', '3', '19846', 'Z');
INSERT INTO `ey_region` VALUES ('19861', '福山区', '3', '19846', 'F');
INSERT INTO `ey_region` VALUES ('19873', '牟平区', '3', '19846', 'M');
INSERT INTO `ey_region` VALUES ('19887', '莱山区', '3', '19846', 'L');
INSERT INTO `ey_region` VALUES ('19893', '长岛县', '3', '19846', 'C');
INSERT INTO `ey_region` VALUES ('19902', '龙口市', '3', '19846', 'L');
INSERT INTO `ey_region` VALUES ('19916', '莱阳市', '3', '19846', 'L');
INSERT INTO `ey_region` VALUES ('19935', '莱州市', '3', '19846', 'L');
INSERT INTO `ey_region` VALUES ('19952', '蓬莱市', '3', '19846', 'P');
INSERT INTO `ey_region` VALUES ('19965', '招远市', '3', '19846', 'Z');
INSERT INTO `ey_region` VALUES ('19980', '栖霞市', '3', '19846', 'Q');
INSERT INTO `ey_region` VALUES ('19996', '海阳市', '3', '19846', 'H');
INSERT INTO `ey_region` VALUES ('20012', '潍坊市', '2', '19280', 'W');
INSERT INTO `ey_region` VALUES ('20013', '市辖区', '3', '20012', 'S');
INSERT INTO `ey_region` VALUES ('20014', '潍城区', '3', '20012', 'W');
INSERT INTO `ey_region` VALUES ('20023', '寒亭区', '3', '20012', 'H');
INSERT INTO `ey_region` VALUES ('20034', '坊子区', '3', '20012', 'F');
INSERT INTO `ey_region` VALUES ('20043', '奎文区', '3', '20012', 'K');
INSERT INTO `ey_region` VALUES ('20055', '临朐县', '3', '20012', 'L');
INSERT INTO `ey_region` VALUES ('20074', '昌乐县', '3', '20012', 'C');
INSERT INTO `ey_region` VALUES ('20091', '青州市', '3', '20012', 'Q');
INSERT INTO `ey_region` VALUES ('20113', '诸城市', '3', '20012', 'Z');
INSERT INTO `ey_region` VALUES ('20137', '寿光市', '3', '20012', 'S');
INSERT INTO `ey_region` VALUES ('20155', '安丘市', '3', '20012', 'A');
INSERT INTO `ey_region` VALUES ('20179', '高密市', '3', '20012', 'G');
INSERT INTO `ey_region` VALUES ('20200', '昌邑市', '3', '20012', 'C');
INSERT INTO `ey_region` VALUES ('20216', '济宁市', '2', '19280', 'J');
INSERT INTO `ey_region` VALUES ('20217', '市辖区', '3', '20216', 'S');
INSERT INTO `ey_region` VALUES ('20218', '市中区', '3', '20216', 'S');
INSERT INTO `ey_region` VALUES ('20227', '任城区', '3', '20216', 'R');
INSERT INTO `ey_region` VALUES ('20257', '鱼台县', '3', '20216', 'Y');
INSERT INTO `ey_region` VALUES ('20268', '金乡县', '3', '20216', 'J');
INSERT INTO `ey_region` VALUES ('20282', '嘉祥县', '3', '20216', 'J');
INSERT INTO `ey_region` VALUES ('20298', '汶上县', '3', '20216', 'W');
INSERT INTO `ey_region` VALUES ('20313', '泗水县', '3', '20216', 'S');
INSERT INTO `ey_region` VALUES ('20327', '梁山县', '3', '20216', 'L');
INSERT INTO `ey_region` VALUES ('20342', '曲阜市', '3', '20216', 'Q');
INSERT INTO `ey_region` VALUES ('20355', '兖州市', '3', '20216', 'Y');
INSERT INTO `ey_region` VALUES ('20368', '邹城市', '3', '20216', 'Z');
INSERT INTO `ey_region` VALUES ('20386', '泰安市', '2', '19280', 'T');
INSERT INTO `ey_region` VALUES ('20387', '市辖区', '3', '20386', 'S');
INSERT INTO `ey_region` VALUES ('20388', '泰山区', '3', '20386', 'T');
INSERT INTO `ey_region` VALUES ('20397', '岱岳区', '3', '20386', 'D');
INSERT INTO `ey_region` VALUES ('20416', '宁阳县', '3', '20386', 'N');
INSERT INTO `ey_region` VALUES ('20429', '东平县', '3', '20386', 'D');
INSERT INTO `ey_region` VALUES ('20444', '新泰市', '3', '20386', 'X');
INSERT INTO `ey_region` VALUES ('20465', '肥城市', '3', '20386', 'F');
INSERT INTO `ey_region` VALUES ('20480', '威海市', '2', '19280', 'W');
INSERT INTO `ey_region` VALUES ('20481', '市辖区', '3', '20480', 'S');
INSERT INTO `ey_region` VALUES ('20482', '环翠区', '3', '20480', 'H');
INSERT INTO `ey_region` VALUES ('20500', '文登市', '3', '20480', 'W');
INSERT INTO `ey_region` VALUES ('20519', '荣成市', '3', '20480', 'R');
INSERT INTO `ey_region` VALUES ('20542', '乳山市', '3', '20480', 'R');
INSERT INTO `ey_region` VALUES ('20558', '日照市', '2', '19280', 'R');
INSERT INTO `ey_region` VALUES ('20559', '市辖区', '3', '20558', 'S');
INSERT INTO `ey_region` VALUES ('20560', '东港区', '3', '20558', 'D');
INSERT INTO `ey_region` VALUES ('20573', '岚山区', '3', '20558', 'L');
INSERT INTO `ey_region` VALUES ('20583', '五莲县', '3', '20558', 'W');
INSERT INTO `ey_region` VALUES ('20596', '莒县', '3', '20558', 'J');
INSERT INTO `ey_region` VALUES ('20618', '莱芜市', '2', '19280', 'L');
INSERT INTO `ey_region` VALUES ('20619', '市辖区', '3', '20618', 'S');
INSERT INTO `ey_region` VALUES ('20620', '莱城区', '3', '20618', 'L');
INSERT INTO `ey_region` VALUES ('20636', '钢城区', '3', '20618', 'G');
INSERT INTO `ey_region` VALUES ('20642', '临沂市', '2', '19280', 'L');
INSERT INTO `ey_region` VALUES ('20643', '临沂市辖区', '3', '20642', 'L');
INSERT INTO `ey_region` VALUES ('20644', '兰山区', '3', '20642', 'L');
INSERT INTO `ey_region` VALUES ('20656', '罗庄区', '3', '20642', 'L');
INSERT INTO `ey_region` VALUES ('20665', '河东区', '3', '20642', 'H');
INSERT INTO `ey_region` VALUES ('20678', '沂南县', '3', '20642', 'Y');
INSERT INTO `ey_region` VALUES ('20696', '郯城县', '3', '20642', 'T');
INSERT INTO `ey_region` VALUES ('20714', '沂水县', '3', '20642', 'Y');
INSERT INTO `ey_region` VALUES ('20734', '苍山县', '3', '20642', 'C');
INSERT INTO `ey_region` VALUES ('20756', '费县', '3', '20642', 'F');
INSERT INTO `ey_region` VALUES ('20775', '平邑县', '3', '20642', 'P');
INSERT INTO `ey_region` VALUES ('20792', '莒南县', '3', '20642', 'J');
INSERT INTO `ey_region` VALUES ('20811', '蒙阴县', '3', '20642', 'M');
INSERT INTO `ey_region` VALUES ('20823', '临沭县', '3', '20642', 'L');
INSERT INTO `ey_region` VALUES ('20836', '德州市', '2', '19280', 'D');
INSERT INTO `ey_region` VALUES ('20837', '市辖区', '3', '20836', 'S');
INSERT INTO `ey_region` VALUES ('20838', '德城区', '3', '20836', 'D');
INSERT INTO `ey_region` VALUES ('20850', '陵县', '3', '20836', 'L');
INSERT INTO `ey_region` VALUES ('20864', '宁津县', '3', '20836', 'N');
INSERT INTO `ey_region` VALUES ('20876', '庆云县', '3', '20836', 'Q');
INSERT INTO `ey_region` VALUES ('20886', '临邑县', '3', '20836', 'L');
INSERT INTO `ey_region` VALUES ('20899', '齐河县', '3', '20836', 'Q');
INSERT INTO `ey_region` VALUES ('20914', '平原县', '3', '20836', 'P');
INSERT INTO `ey_region` VALUES ('20927', '夏津县', '3', '20836', 'X');
INSERT INTO `ey_region` VALUES ('20942', '武城县', '3', '20836', 'W');
INSERT INTO `ey_region` VALUES ('20952', '乐陵市', '3', '20836', 'L');
INSERT INTO `ey_region` VALUES ('20969', '禹城市', '3', '20836', 'Y');
INSERT INTO `ey_region` VALUES ('20981', '聊城市', '2', '19280', 'L');
INSERT INTO `ey_region` VALUES ('20982', '市辖区', '3', '20981', 'S');
INSERT INTO `ey_region` VALUES ('20983', '东昌府区', '3', '20981', 'D');
INSERT INTO `ey_region` VALUES ('21004', '阳谷县', '3', '20981', 'Y');
INSERT INTO `ey_region` VALUES ('21023', '莘县', '3', '20981', 'S');
INSERT INTO `ey_region` VALUES ('21046', '茌平县', '3', '20981', 'C');
INSERT INTO `ey_region` VALUES ('21063', '东阿县', '3', '20981', 'D');
INSERT INTO `ey_region` VALUES ('21075', '冠县', '3', '20981', 'G');
INSERT INTO `ey_region` VALUES ('21093', '高唐县', '3', '20981', 'G');
INSERT INTO `ey_region` VALUES ('21106', '临清市', '3', '20981', 'L');
INSERT INTO `ey_region` VALUES ('21123', '滨州市', '2', '19280', 'B');
INSERT INTO `ey_region` VALUES ('21124', '市辖区', '3', '21123', 'S');
INSERT INTO `ey_region` VALUES ('21125', '滨城区', '3', '21123', 'B');
INSERT INTO `ey_region` VALUES ('21141', '惠民县', '3', '21123', 'H');
INSERT INTO `ey_region` VALUES ('21156', '阳信县', '3', '21123', 'Y');
INSERT INTO `ey_region` VALUES ('21166', '无棣县', '3', '21123', 'W');
INSERT INTO `ey_region` VALUES ('21178', '沾化县', '3', '21123', 'Z');
INSERT INTO `ey_region` VALUES ('21190', '博兴县', '3', '21123', 'B');
INSERT INTO `ey_region` VALUES ('21201', '邹平县', '3', '21123', 'Z');
INSERT INTO `ey_region` VALUES ('21218', '菏泽市', '2', '19280', 'H');
INSERT INTO `ey_region` VALUES ('21219', '市辖区', '3', '21218', 'S');
INSERT INTO `ey_region` VALUES ('21220', '牡丹区', '3', '21218', 'M');
INSERT INTO `ey_region` VALUES ('21245', '曹县', '3', '21218', 'C');
INSERT INTO `ey_region` VALUES ('21271', '单县', '3', '21218', 'D');
INSERT INTO `ey_region` VALUES ('21292', '成武县', '3', '21218', 'C');
INSERT INTO `ey_region` VALUES ('21305', '巨野县', '3', '21218', 'J');
INSERT INTO `ey_region` VALUES ('21322', '郓城县', '3', '21218', 'Y');
INSERT INTO `ey_region` VALUES ('21344', '鄄城县', '3', '21218', 'J');
INSERT INTO `ey_region` VALUES ('21361', '定陶县', '3', '21218', 'D');
INSERT INTO `ey_region` VALUES ('21373', '东明县', '3', '21218', 'D');
INSERT INTO `ey_region` VALUES ('21387', '河南省', '1', '0', 'H');
INSERT INTO `ey_region` VALUES ('21388', '郑州市', '2', '21387', 'Z');
INSERT INTO `ey_region` VALUES ('21389', '市辖区', '3', '21388', 'S');
INSERT INTO `ey_region` VALUES ('21390', '中原区', '3', '21388', 'Z');
INSERT INTO `ey_region` VALUES ('21404', '二七区', '3', '21388', 'E');
INSERT INTO `ey_region` VALUES ('21420', '管城回族区', '3', '21388', 'G');
INSERT INTO `ey_region` VALUES ('21435', '金水区', '3', '21388', 'J');
INSERT INTO `ey_region` VALUES ('21453', '上街区', '3', '21388', 'S');
INSERT INTO `ey_region` VALUES ('21460', '惠济区', '3', '21388', 'H');
INSERT INTO `ey_region` VALUES ('21469', '中牟县', '3', '21388', 'Z');
INSERT INTO `ey_region` VALUES ('21487', '巩义市', '3', '21388', 'G');
INSERT INTO `ey_region` VALUES ('21508', '荥阳市', '3', '21388', 'X');
INSERT INTO `ey_region` VALUES ('21523', '新密市', '3', '21388', 'X');
INSERT INTO `ey_region` VALUES ('21542', '新郑市', '3', '21388', 'X');
INSERT INTO `ey_region` VALUES ('21558', '登封市', '3', '21388', 'D');
INSERT INTO `ey_region` VALUES ('21575', '开封市', '2', '21387', 'K');
INSERT INTO `ey_region` VALUES ('21576', '市辖区', '3', '21575', 'S');
INSERT INTO `ey_region` VALUES ('21577', '龙亭区', '3', '21575', 'L');
INSERT INTO `ey_region` VALUES ('21584', '顺河区', '3', '21575', 'S');
INSERT INTO `ey_region` VALUES ('21593', '鼓楼区', '3', '21575', 'G');
INSERT INTO `ey_region` VALUES ('21602', '禹王台区', '3', '21575', 'Y');
INSERT INTO `ey_region` VALUES ('21610', '金明区', '3', '21575', 'J');
INSERT INTO `ey_region` VALUES ('21618', '杞县', '3', '21575', 'Q');
INSERT INTO `ey_region` VALUES ('21640', '通许县', '3', '21575', 'T');
INSERT INTO `ey_region` VALUES ('21653', '尉氏县', '3', '21575', 'W');
INSERT INTO `ey_region` VALUES ('21671', '开封县', '3', '21575', 'K');
INSERT INTO `ey_region` VALUES ('21687', '兰考县', '3', '21575', 'L');
INSERT INTO `ey_region` VALUES ('21711', '洛阳市', '2', '21387', 'L');
INSERT INTO `ey_region` VALUES ('21712', '市辖区', '3', '21711', 'S');
INSERT INTO `ey_region` VALUES ('21713', '老城区', '3', '21711', 'L');
INSERT INTO `ey_region` VALUES ('21722', '西工区', '3', '21711', 'X');
INSERT INTO `ey_region` VALUES ('21733', '廛河回族区', '3', '21711', 'C');
INSERT INTO `ey_region` VALUES ('21742', '涧西区', '3', '21711', 'J');
INSERT INTO `ey_region` VALUES ('21758', '吉利区', '3', '21711', 'J');
INSERT INTO `ey_region` VALUES ('21761', '洛龙区', '3', '21711', 'L');
INSERT INTO `ey_region` VALUES ('21770', '孟津县', '3', '21711', 'M');
INSERT INTO `ey_region` VALUES ('21781', '新安县', '3', '21711', 'X');
INSERT INTO `ey_region` VALUES ('21794', '栾川县', '3', '21711', 'L');
INSERT INTO `ey_region` VALUES ('21809', '嵩县', '3', '21711', 'S');
INSERT INTO `ey_region` VALUES ('21829', '汝阳县', '3', '21711', 'R');
INSERT INTO `ey_region` VALUES ('21844', '宜阳县', '3', '21711', 'Y');
INSERT INTO `ey_region` VALUES ('21862', '洛宁县', '3', '21711', 'L');
INSERT INTO `ey_region` VALUES ('21881', '伊川县', '3', '21711', 'Y');
INSERT INTO `ey_region` VALUES ('21896', '偃师市', '3', '21711', 'Y');
INSERT INTO `ey_region` VALUES ('21913', '平顶山市', '2', '21387', 'P');
INSERT INTO `ey_region` VALUES ('21914', '市辖区', '3', '21913', 'S');
INSERT INTO `ey_region` VALUES ('21915', '新华区', '3', '21913', 'X');
INSERT INTO `ey_region` VALUES ('21928', '卫东区', '3', '21913', 'W');
INSERT INTO `ey_region` VALUES ('21940', '石龙区', '3', '21913', 'S');
INSERT INTO `ey_region` VALUES ('21945', '湛河区', '3', '21913', 'Z');
INSERT INTO `ey_region` VALUES ('21954', '宝丰县', '3', '21913', 'B');
INSERT INTO `ey_region` VALUES ('21968', '叶  县', '3', '21913', 'Y');
INSERT INTO `ey_region` VALUES ('21987', '鲁山县', '3', '21913', 'L');
INSERT INTO `ey_region` VALUES ('22009', '郏  县', '3', '21913', 'J');
INSERT INTO `ey_region` VALUES ('22024', '舞钢市', '3', '21913', 'W');
INSERT INTO `ey_region` VALUES ('22037', '汝州市', '3', '21913', 'R');
INSERT INTO `ey_region` VALUES ('22058', '安阳市', '2', '21387', 'A');
INSERT INTO `ey_region` VALUES ('22059', '市辖区', '3', '22058', 'S');
INSERT INTO `ey_region` VALUES ('22060', '文峰区', '3', '22058', 'W');
INSERT INTO `ey_region` VALUES ('22080', '北关区', '3', '22058', 'B');
INSERT INTO `ey_region` VALUES ('22090', '殷都区', '3', '22058', 'Y');
INSERT INTO `ey_region` VALUES ('22101', '龙安区', '3', '22058', 'L');
INSERT INTO `ey_region` VALUES ('22111', '安阳县', '3', '22058', 'A');
INSERT INTO `ey_region` VALUES ('22133', '汤阴县', '3', '22058', 'T');
INSERT INTO `ey_region` VALUES ('22144', '滑县', '3', '22058', 'H');
INSERT INTO `ey_region` VALUES ('22167', '内黄县', '3', '22058', 'N');
INSERT INTO `ey_region` VALUES ('22185', '林州市', '3', '22058', 'L');
INSERT INTO `ey_region` VALUES ('22206', '鹤壁市', '2', '21387', 'H');
INSERT INTO `ey_region` VALUES ('22207', '市辖区', '3', '22206', 'S');
INSERT INTO `ey_region` VALUES ('22208', '鹤山区', '3', '22206', 'H');
INSERT INTO `ey_region` VALUES ('22216', '山城区', '3', '22206', 'S');
INSERT INTO `ey_region` VALUES ('22224', '淇滨区', '3', '22206', 'Q');
INSERT INTO `ey_region` VALUES ('22232', '浚县', '3', '22206', 'J');
INSERT INTO `ey_region` VALUES ('22243', '淇县', '3', '22206', 'Q');
INSERT INTO `ey_region` VALUES ('22251', '新乡市', '2', '21387', 'X');
INSERT INTO `ey_region` VALUES ('22252', '市辖区', '3', '22251', 'S');
INSERT INTO `ey_region` VALUES ('22253', '红旗区', '3', '22251', 'H');
INSERT INTO `ey_region` VALUES ('22264', '卫滨区', '3', '22251', 'W');
INSERT INTO `ey_region` VALUES ('22273', '凤泉区', '3', '22251', 'F');
INSERT INTO `ey_region` VALUES ('22279', '牧野区', '3', '22251', 'M');
INSERT INTO `ey_region` VALUES ('22290', '新乡县', '3', '22251', 'X');
INSERT INTO `ey_region` VALUES ('22299', '获嘉县', '3', '22251', 'H');
INSERT INTO `ey_region` VALUES ('22312', '原阳县', '3', '22251', 'Y');
INSERT INTO `ey_region` VALUES ('22330', '延津县', '3', '22251', 'Y');
INSERT INTO `ey_region` VALUES ('22347', '封丘县', '3', '22251', 'F');
INSERT INTO `ey_region` VALUES ('22367', '长垣县', '3', '22251', 'C');
INSERT INTO `ey_region` VALUES ('22386', '卫辉市', '3', '22251', 'W');
INSERT INTO `ey_region` VALUES ('22400', '辉县市', '3', '22251', 'H');
INSERT INTO `ey_region` VALUES ('22423', '焦作市', '2', '21387', 'J');
INSERT INTO `ey_region` VALUES ('22424', '市辖区', '3', '22423', 'S');
INSERT INTO `ey_region` VALUES ('22425', '解放区', '3', '22423', 'J');
INSERT INTO `ey_region` VALUES ('22435', '中站区', '3', '22423', 'Z');
INSERT INTO `ey_region` VALUES ('22446', '马村区', '3', '22423', 'M');
INSERT INTO `ey_region` VALUES ('22454', '山阳区', '3', '22423', 'S');
INSERT INTO `ey_region` VALUES ('22465', '修武县', '3', '22423', 'X');
INSERT INTO `ey_region` VALUES ('22475', '博爱县', '3', '22423', 'B');
INSERT INTO `ey_region` VALUES ('22487', '武陟县', '3', '22423', 'W');
INSERT INTO `ey_region` VALUES ('22503', '温县', '3', '22423', 'W');
INSERT INTO `ey_region` VALUES ('22515', '济源市', '3', '22423', 'J');
INSERT INTO `ey_region` VALUES ('22532', '沁阳市', '3', '22423', 'Q');
INSERT INTO `ey_region` VALUES ('22546', '孟州市', '3', '22423', 'M');
INSERT INTO `ey_region` VALUES ('22558', '濮阳市', '2', '21387', 'P');
INSERT INTO `ey_region` VALUES ('22559', '市辖区', '3', '22558', 'S');
INSERT INTO `ey_region` VALUES ('22560', '华龙区', '3', '22558', 'H');
INSERT INTO `ey_region` VALUES ('22578', '清丰县', '3', '22558', 'Q');
INSERT INTO `ey_region` VALUES ('22596', '南乐县', '3', '22558', 'N');
INSERT INTO `ey_region` VALUES ('22609', '范县', '3', '22558', 'F');
INSERT INTO `ey_region` VALUES ('22622', '台前县', '3', '22558', 'T');
INSERT INTO `ey_region` VALUES ('22632', '濮阳县', '3', '22558', 'P');
INSERT INTO `ey_region` VALUES ('22655', '许昌市', '2', '21387', 'X');
INSERT INTO `ey_region` VALUES ('22656', '市辖区', '3', '22655', 'S');
INSERT INTO `ey_region` VALUES ('22657', '魏都区', '3', '22655', 'W');
INSERT INTO `ey_region` VALUES ('22671', '许昌县', '3', '22655', 'X');
INSERT INTO `ey_region` VALUES ('22688', '鄢陵县', '3', '22655', 'Y');
INSERT INTO `ey_region` VALUES ('22701', '襄城县', '3', '22655', 'X');
INSERT INTO `ey_region` VALUES ('22718', '禹州市', '3', '22655', 'Y');
INSERT INTO `ey_region` VALUES ('22745', '长葛市', '3', '22655', 'C');
INSERT INTO `ey_region` VALUES ('22762', '漯河市', '2', '21387', 'L');
INSERT INTO `ey_region` VALUES ('22763', '市辖区', '3', '22762', 'S');
INSERT INTO `ey_region` VALUES ('22764', '源汇区', '3', '22762', 'Y');
INSERT INTO `ey_region` VALUES ('22773', '郾城区', '3', '22762', 'Y');
INSERT INTO `ey_region` VALUES ('22783', '召陵区', '3', '22762', 'Z');
INSERT INTO `ey_region` VALUES ('22793', '舞阳县', '3', '22762', 'W');
INSERT INTO `ey_region` VALUES ('22808', '临颖县', '3', '22762', 'L');
INSERT INTO `ey_region` VALUES ('22824', '三门峡市', '2', '21387', 'S');
INSERT INTO `ey_region` VALUES ('22825', '市辖区', '3', '22824', 'S');
INSERT INTO `ey_region` VALUES ('22826', '湖滨区', '3', '22824', 'H');
INSERT INTO `ey_region` VALUES ('22838', '渑池县', '3', '22824', 'M');
INSERT INTO `ey_region` VALUES ('22851', '陕县', '3', '22824', 'S');
INSERT INTO `ey_region` VALUES ('22865', '卢氏县', '3', '22824', 'L');
INSERT INTO `ey_region` VALUES ('22885', '义马市', '3', '22824', 'Y');
INSERT INTO `ey_region` VALUES ('22893', '灵宝市', '3', '22824', 'L');
INSERT INTO `ey_region` VALUES ('22910', '南阳市', '2', '21387', 'N');
INSERT INTO `ey_region` VALUES ('22911', '市辖区', '3', '22910', 'S');
INSERT INTO `ey_region` VALUES ('22912', '宛城区', '3', '22910', 'W');
INSERT INTO `ey_region` VALUES ('22930', '卧龙区', '3', '22910', 'W');
INSERT INTO `ey_region` VALUES ('22951', '南召县', '3', '22910', 'N');
INSERT INTO `ey_region` VALUES ('22973', '方城县', '3', '22910', 'F');
INSERT INTO `ey_region` VALUES ('22992', '西峡县', '3', '22910', 'X');
INSERT INTO `ey_region` VALUES ('23013', '镇平县', '3', '22910', 'Z');
INSERT INTO `ey_region` VALUES ('23036', '内乡县', '3', '22910', 'N');
INSERT INTO `ey_region` VALUES ('23053', '淅川县', '3', '22910', 'X');
INSERT INTO `ey_region` VALUES ('23071', '社旗县', '3', '22910', 'S');
INSERT INTO `ey_region` VALUES ('23087', '唐河县', '3', '22910', 'T');
INSERT INTO `ey_region` VALUES ('23108', '新野县', '3', '22910', 'X');
INSERT INTO `ey_region` VALUES ('23123', '桐柏县', '3', '22910', 'T');
INSERT INTO `ey_region` VALUES ('23140', '邓州市', '3', '22910', 'D');
INSERT INTO `ey_region` VALUES ('23170', '商丘市', '2', '21387', 'S');
INSERT INTO `ey_region` VALUES ('23171', '市辖区', '3', '23170', 'S');
INSERT INTO `ey_region` VALUES ('23172', '梁园区', '3', '23170', 'L');
INSERT INTO `ey_region` VALUES ('23192', '睢阳区', '3', '23170', 'S');
INSERT INTO `ey_region` VALUES ('23211', '民权县', '3', '23170', 'M');
INSERT INTO `ey_region` VALUES ('23232', '睢县', '3', '23170', 'S');
INSERT INTO `ey_region` VALUES ('23253', '宁陵县', '3', '23170', 'N');
INSERT INTO `ey_region` VALUES ('23268', '柘城县', '3', '23170', 'Z');
INSERT INTO `ey_region` VALUES ('23290', '虞城县', '3', '23170', 'Y');
INSERT INTO `ey_region` VALUES ('23317', '夏邑县', '3', '23170', 'X');
INSERT INTO `ey_region` VALUES ('23342', '永城市', '3', '23170', 'Y');
INSERT INTO `ey_region` VALUES ('23372', '信阳市', '2', '21387', 'X');
INSERT INTO `ey_region` VALUES ('23373', '市辖区', '3', '23372', 'S');
INSERT INTO `ey_region` VALUES ('23374', '浉河区', '3', '23372', 'S');
INSERT INTO `ey_region` VALUES ('23393', '平桥区', '3', '23372', 'P');
INSERT INTO `ey_region` VALUES ('23414', '罗山县', '3', '23372', 'L');
INSERT INTO `ey_region` VALUES ('23435', '光山县', '3', '23372', 'G');
INSERT INTO `ey_region` VALUES ('23455', '新县', '3', '23372', 'X');
INSERT INTO `ey_region` VALUES ('23471', '商城县', '3', '23372', 'S');
INSERT INTO `ey_region` VALUES ('23492', '固始县', '3', '23372', 'G');
INSERT INTO `ey_region` VALUES ('23525', '潢川县', '3', '23372', 'H');
INSERT INTO `ey_region` VALUES ('23549', '淮滨县', '3', '23372', 'H');
INSERT INTO `ey_region` VALUES ('23567', '息县', '3', '23372', 'X');
INSERT INTO `ey_region` VALUES ('23589', '周口市', '2', '21387', 'Z');
INSERT INTO `ey_region` VALUES ('23590', '市辖区', '3', '23589', 'S');
INSERT INTO `ey_region` VALUES ('23591', '川汇区', '3', '23589', 'C');
INSERT INTO `ey_region` VALUES ('23604', '扶沟县', '3', '23589', 'F');
INSERT INTO `ey_region` VALUES ('23621', '西华县', '3', '23589', 'X');
INSERT INTO `ey_region` VALUES ('23647', '商水县', '3', '23589', 'S');
INSERT INTO `ey_region` VALUES ('23672', '沈丘县', '3', '23589', 'S');
INSERT INTO `ey_region` VALUES ('23695', '郸城县', '3', '23589', 'D');
INSERT INTO `ey_region` VALUES ('23716', '淮阳县', '3', '23589', 'H');
INSERT INTO `ey_region` VALUES ('23736', '太康县', '3', '23589', 'T');
INSERT INTO `ey_region` VALUES ('23766', '鹿邑县', '3', '23589', 'L');
INSERT INTO `ey_region` VALUES ('23796', '项城市', '3', '23589', 'X');
INSERT INTO `ey_region` VALUES ('23818', '驻马店市', '2', '21387', 'Z');
INSERT INTO `ey_region` VALUES ('23819', '市辖区', '3', '23818', 'S');
INSERT INTO `ey_region` VALUES ('23820', '驿城区', '3', '23818', 'Y');
INSERT INTO `ey_region` VALUES ('23840', '西平县', '3', '23818', 'X');
INSERT INTO `ey_region` VALUES ('23861', '上蔡县', '3', '23818', 'S');
INSERT INTO `ey_region` VALUES ('23886', '平舆县', '3', '23818', 'P');
INSERT INTO `ey_region` VALUES ('23905', '正阳县', '3', '23818', 'Z');
INSERT INTO `ey_region` VALUES ('23926', '确山县', '3', '23818', 'Q');
INSERT INTO `ey_region` VALUES ('23940', '泌阳县', '3', '23818', 'M');
INSERT INTO `ey_region` VALUES ('23965', '汝南县', '3', '23818', 'R');
INSERT INTO `ey_region` VALUES ('23983', '遂平县', '3', '23818', 'S');
INSERT INTO `ey_region` VALUES ('23999', '新蔡县', '3', '23818', 'X');
INSERT INTO `ey_region` VALUES ('24022', '湖北省', '1', '0', 'H');
INSERT INTO `ey_region` VALUES ('24023', '武汉市', '2', '24022', 'W');
INSERT INTO `ey_region` VALUES ('24024', '市辖区', '3', '24023', 'S');
INSERT INTO `ey_region` VALUES ('24025', '江岸区', '3', '24023', 'J');
INSERT INTO `ey_region` VALUES ('24043', '江汉区', '3', '24023', 'J');
INSERT INTO `ey_region` VALUES ('24057', '硚口区', '3', '24023', 'Q');
INSERT INTO `ey_region` VALUES ('24069', '汉阳区', '3', '24023', 'H');
INSERT INTO `ey_region` VALUES ('24082', '武昌区', '3', '24023', 'W');
INSERT INTO `ey_region` VALUES ('24098', '青山区', '3', '24023', 'Q');
INSERT INTO `ey_region` VALUES ('24111', '洪山区', '3', '24023', 'H');
INSERT INTO `ey_region` VALUES ('24129', '东西湖区', '3', '24023', 'D');
INSERT INTO `ey_region` VALUES ('24142', '汉南区', '3', '24023', 'H');
INSERT INTO `ey_region` VALUES ('24150', '蔡甸区', '3', '24023', 'C');
INSERT INTO `ey_region` VALUES ('24165', '江夏区', '3', '24023', 'J');
INSERT INTO `ey_region` VALUES ('24185', '黄陂区', '3', '24023', 'H');
INSERT INTO `ey_region` VALUES ('24205', '武汉市新洲区', '3', '24023', 'W');
INSERT INTO `ey_region` VALUES ('24224', '黄石市', '2', '24022', 'H');
INSERT INTO `ey_region` VALUES ('24225', '市辖区', '3', '24224', 'S');
INSERT INTO `ey_region` VALUES ('24226', '黄石港区', '3', '24224', 'H');
INSERT INTO `ey_region` VALUES ('24233', '西塞山区', '3', '24224', 'X');
INSERT INTO `ey_region` VALUES ('24242', '下陆区', '3', '24224', 'X');
INSERT INTO `ey_region` VALUES ('24247', '铁山区', '3', '24224', 'T');
INSERT INTO `ey_region` VALUES ('24250', '阳新县', '3', '24224', 'Y');
INSERT INTO `ey_region` VALUES ('24273', '大冶市', '3', '24224', 'D');
INSERT INTO `ey_region` VALUES ('24291', '十堰市', '2', '24022', 'S');
INSERT INTO `ey_region` VALUES ('24292', '市辖区', '3', '24291', 'S');
INSERT INTO `ey_region` VALUES ('24293', '茅箭区', '3', '24291', 'M');
INSERT INTO `ey_region` VALUES ('24302', '张湾区', '3', '24291', 'Z');
INSERT INTO `ey_region` VALUES ('24314', '郧县', '3', '24291', 'Y');
INSERT INTO `ey_region` VALUES ('24335', '郧西县', '3', '24291', 'Y');
INSERT INTO `ey_region` VALUES ('24354', '竹山县', '3', '24291', 'Z');
INSERT INTO `ey_region` VALUES ('24374', '竹溪县', '3', '24291', 'Z');
INSERT INTO `ey_region` VALUES ('24405', '房县', '3', '24291', 'F');
INSERT INTO `ey_region` VALUES ('24435', '丹江口市', '3', '24291', 'D');
INSERT INTO `ey_region` VALUES ('24453', '宜昌市', '2', '24022', 'Y');
INSERT INTO `ey_region` VALUES ('24454', '市辖区', '3', '24453', 'S');
INSERT INTO `ey_region` VALUES ('24455', '西陵区', '3', '24453', 'X');
INSERT INTO `ey_region` VALUES ('24465', '伍家岗区', '3', '24453', 'W');
INSERT INTO `ey_region` VALUES ('24471', '点军区', '3', '24453', 'D');
INSERT INTO `ey_region` VALUES ('24477', '猇亭区', '3', '24453', 'X');
INSERT INTO `ey_region` VALUES ('24481', '夷陵区', '3', '24453', 'Y');
INSERT INTO `ey_region` VALUES ('24495', '远安县', '3', '24453', 'Y');
INSERT INTO `ey_region` VALUES ('24503', '兴山县', '3', '24453', 'X');
INSERT INTO `ey_region` VALUES ('24512', '秭归县', '3', '24453', 'Z');
INSERT INTO `ey_region` VALUES ('24525', '长阳县', '3', '24453', 'C');
INSERT INTO `ey_region` VALUES ('24537', '五峰县', '3', '24453', 'W');
INSERT INTO `ey_region` VALUES ('24546', '宜都市', '3', '24453', 'Y');
INSERT INTO `ey_region` VALUES ('24559', '当阳市', '3', '24453', 'D');
INSERT INTO `ey_region` VALUES ('24570', '枝江市', '3', '24453', 'Z');
INSERT INTO `ey_region` VALUES ('24580', '襄樊市', '2', '24022', 'X');
INSERT INTO `ey_region` VALUES ('24581', '市辖区', '3', '24580', 'S');
INSERT INTO `ey_region` VALUES ('24582', '襄城区', '3', '24580', 'X');
INSERT INTO `ey_region` VALUES ('24591', '樊城区', '3', '24580', 'F');
INSERT INTO `ey_region` VALUES ('24608', '襄阳区', '3', '24580', 'X');
INSERT INTO `ey_region` VALUES ('24623', '南漳县', '3', '24580', 'N');
INSERT INTO `ey_region` VALUES ('24635', '谷城县', '3', '24580', 'G');
INSERT INTO `ey_region` VALUES ('24647', '保康县', '3', '24580', 'B');
INSERT INTO `ey_region` VALUES ('24659', '老河口市', '3', '24580', 'L');
INSERT INTO `ey_region` VALUES ('24674', '枣阳市', '3', '24580', 'Z');
INSERT INTO `ey_region` VALUES ('24692', '宜城市', '3', '24580', 'Y');
INSERT INTO `ey_region` VALUES ('24706', '鄂州市', '2', '24022', 'E');
INSERT INTO `ey_region` VALUES ('24707', '市辖区', '3', '24706', 'S');
INSERT INTO `ey_region` VALUES ('24708', '粱子湖区', '3', '24706', 'L');
INSERT INTO `ey_region` VALUES ('24714', '华容区', '3', '24706', 'H');
INSERT INTO `ey_region` VALUES ('24722', '鄂城区', '3', '24706', 'E');
INSERT INTO `ey_region` VALUES ('24737', '荆门市', '2', '24022', 'J');
INSERT INTO `ey_region` VALUES ('24738', '市辖区', '3', '24737', 'S');
INSERT INTO `ey_region` VALUES ('24739', '东宝区', '3', '24737', 'D');
INSERT INTO `ey_region` VALUES ('24749', '掇刀区', '3', '24737', 'D');
INSERT INTO `ey_region` VALUES ('24755', '京山县', '3', '24737', 'J');
INSERT INTO `ey_region` VALUES ('24778', '沙洋县', '3', '24737', 'S');
INSERT INTO `ey_region` VALUES ('24794', '钟祥市', '3', '24737', 'Z');
INSERT INTO `ey_region` VALUES ('24816', '孝感市', '2', '24022', 'X');
INSERT INTO `ey_region` VALUES ('24817', '市辖区', '3', '24816', 'S');
INSERT INTO `ey_region` VALUES ('24818', '孝南区', '3', '24816', 'X');
INSERT INTO `ey_region` VALUES ('24838', '孝昌县', '3', '24816', 'X');
INSERT INTO `ey_region` VALUES ('24853', '大悟县', '3', '24816', 'D');
INSERT INTO `ey_region` VALUES ('24871', '云梦县', '3', '24816', 'Y');
INSERT INTO `ey_region` VALUES ('24885', '应城市', '3', '24816', 'Y');
INSERT INTO `ey_region` VALUES ('24903', '安陆市', '3', '24816', 'A');
INSERT INTO `ey_region` VALUES ('24920', '汉川市', '3', '24816', 'H');
INSERT INTO `ey_region` VALUES ('24949', '荆州市', '2', '24022', 'J');
INSERT INTO `ey_region` VALUES ('24950', '市辖区', '3', '24949', 'S');
INSERT INTO `ey_region` VALUES ('24951', '沙市区', '3', '24949', 'S');
INSERT INTO `ey_region` VALUES ('24965', '荆州区', '3', '24949', 'J');
INSERT INTO `ey_region` VALUES ('24978', '公安县', '3', '24949', 'G');
INSERT INTO `ey_region` VALUES ('24995', '监利县', '3', '24949', 'J');
INSERT INTO `ey_region` VALUES ('25019', '江陵县', '3', '24949', 'J');
INSERT INTO `ey_region` VALUES ('25032', '石首市', '3', '24949', 'S');
INSERT INTO `ey_region` VALUES ('25048', '洪湖市', '3', '24949', 'H');
INSERT INTO `ey_region` VALUES ('25069', '松滋市', '3', '24949', 'S');
INSERT INTO `ey_region` VALUES ('25086', '黄冈市', '2', '24022', 'H');
INSERT INTO `ey_region` VALUES ('25087', '市辖区', '3', '25086', 'S');
INSERT INTO `ey_region` VALUES ('25088', '黄州区', '3', '25086', 'H');
INSERT INTO `ey_region` VALUES ('25099', '团风县', '3', '25086', 'T');
INSERT INTO `ey_region` VALUES ('25112', '红安县', '3', '25086', 'H');
INSERT INTO `ey_region` VALUES ('25126', '罗田县', '3', '25086', 'L');
INSERT INTO `ey_region` VALUES ('25143', '英山县', '3', '25086', 'Y');
INSERT INTO `ey_region` VALUES ('25158', '浠水县', '3', '25086', 'X');
INSERT INTO `ey_region` VALUES ('25175', '蕲春县', '3', '25086', 'Q');
INSERT INTO `ey_region` VALUES ('25192', '黄梅县', '3', '25086', 'H');
INSERT INTO `ey_region` VALUES ('25211', '麻城市', '3', '25086', 'M');
INSERT INTO `ey_region` VALUES ('25235', '武穴市', '3', '25086', 'W');
INSERT INTO `ey_region` VALUES ('25249', '咸宁市', '2', '24022', 'X');
INSERT INTO `ey_region` VALUES ('25250', '市辖区', '3', '25249', 'S');
INSERT INTO `ey_region` VALUES ('25251', '咸安区', '3', '25249', 'X');
INSERT INTO `ey_region` VALUES ('25266', '嘉鱼县', '3', '25249', 'J');
INSERT INTO `ey_region` VALUES ('25276', '通城县', '3', '25249', 'T');
INSERT INTO `ey_region` VALUES ('25290', '崇阳县', '3', '25249', 'C');
INSERT INTO `ey_region` VALUES ('25303', '通山县', '3', '25249', 'T');
INSERT INTO `ey_region` VALUES ('25317', '赤壁市', '3', '25249', 'C');
INSERT INTO `ey_region` VALUES ('25335', '随州市', '2', '24022', 'S');
INSERT INTO `ey_region` VALUES ('25336', '市辖区', '3', '25335', 'S');
INSERT INTO `ey_region` VALUES ('25337', '曾都区', '3', '25335', 'Z');
INSERT INTO `ey_region` VALUES ('25367', '广水市', '3', '25335', 'G');
INSERT INTO `ey_region` VALUES ('25388', '恩施州', '2', '24022', 'E');
INSERT INTO `ey_region` VALUES ('25389', '恩施市', '3', '25388', 'E');
INSERT INTO `ey_region` VALUES ('25406', '利川市', '3', '25388', 'L');
INSERT INTO `ey_region` VALUES ('25422', '建始县', '3', '25388', 'J');
INSERT INTO `ey_region` VALUES ('25433', '巴东县', '3', '25388', 'B');
INSERT INTO `ey_region` VALUES ('25446', '宣恩县', '3', '25388', 'X');
INSERT INTO `ey_region` VALUES ('25456', '咸丰县', '3', '25388', 'X');
INSERT INTO `ey_region` VALUES ('25467', '来凤县', '3', '25388', 'L');
INSERT INTO `ey_region` VALUES ('25476', '鹤峰县', '3', '25388', 'H');
INSERT INTO `ey_region` VALUES ('25487', '省直辖行政单位', '2', '24022', 'S');
INSERT INTO `ey_region` VALUES ('25488', '仙桃市', '3', '25487', 'X');
INSERT INTO `ey_region` VALUES ('25516', '潜江市', '3', '25487', 'Q');
INSERT INTO `ey_region` VALUES ('25541', '天门市', '3', '25487', 'T');
INSERT INTO `ey_region` VALUES ('25570', '神农架林区', '3', '25487', 'S');
INSERT INTO `ey_region` VALUES ('25579', '湖南省', '1', '0', 'H');
INSERT INTO `ey_region` VALUES ('25580', '长沙市', '2', '25579', 'C');
INSERT INTO `ey_region` VALUES ('25581', '市辖区', '3', '25580', 'S');
INSERT INTO `ey_region` VALUES ('25582', '芙蓉区', '3', '25580', 'F');
INSERT INTO `ey_region` VALUES ('25596', '天心区', '3', '25580', 'T');
INSERT INTO `ey_region` VALUES ('25607', '岳麓区', '3', '25580', 'Y');
INSERT INTO `ey_region` VALUES ('25620', '开福区', '3', '25580', 'K');
INSERT INTO `ey_region` VALUES ('25634', '雨花区', '3', '25580', 'Y');
INSERT INTO `ey_region` VALUES ('25645', '长沙县', '3', '25580', 'C');
INSERT INTO `ey_region` VALUES ('25666', '望城县', '3', '25580', 'W');
INSERT INTO `ey_region` VALUES ('25686', '宁乡县', '3', '25580', 'N');
INSERT INTO `ey_region` VALUES ('25720', '浏阳市', '3', '25580', 'L');
INSERT INTO `ey_region` VALUES ('25758', '株洲市', '2', '25579', 'Z');
INSERT INTO `ey_region` VALUES ('25759', '市辖区', '3', '25758', 'S');
INSERT INTO `ey_region` VALUES ('25760', '荷塘区', '3', '25758', 'H');
INSERT INTO `ey_region` VALUES ('25768', '芦淞区', '3', '25758', 'L');
INSERT INTO `ey_region` VALUES ('25777', '石峰区', '3', '25758', 'S');
INSERT INTO `ey_region` VALUES ('25785', '天元区', '3', '25758', 'T');
INSERT INTO `ey_region` VALUES ('25791', '株洲县', '3', '25758', 'Z');
INSERT INTO `ey_region` VALUES ('25810', '攸县', '3', '25758', 'Y');
INSERT INTO `ey_region` VALUES ('25836', '茶陵县', '3', '25758', 'C');
INSERT INTO `ey_region` VALUES ('25863', '炎陵县', '3', '25758', 'Y');
INSERT INTO `ey_region` VALUES ('25881', '醴陵市', '3', '25758', 'L');
INSERT INTO `ey_region` VALUES ('25912', '湘潭市', '2', '25579', 'X');
INSERT INTO `ey_region` VALUES ('25913', '市辖区', '3', '25912', 'S');
INSERT INTO `ey_region` VALUES ('25914', '雨湖区', '3', '25912', 'Y');
INSERT INTO `ey_region` VALUES ('25929', '岳塘区', '3', '25912', 'Y');
INSERT INTO `ey_region` VALUES ('25947', '湘潭县', '3', '25912', 'X');
INSERT INTO `ey_region` VALUES ('25970', '湘乡市', '3', '25912', 'X');
INSERT INTO `ey_region` VALUES ('25993', '韶山市', '3', '25912', 'S');
INSERT INTO `ey_region` VALUES ('26001', '衡阳市', '2', '25579', 'H');
INSERT INTO `ey_region` VALUES ('26002', '市辖区', '3', '26001', 'S');
INSERT INTO `ey_region` VALUES ('26003', '珠晖区', '3', '26001', 'Z');
INSERT INTO `ey_region` VALUES ('26019', '雁峰区', '3', '26001', 'Y');
INSERT INTO `ey_region` VALUES ('26028', '石鼓区', '3', '26001', 'S');
INSERT INTO `ey_region` VALUES ('26037', '蒸湘区', '3', '26001', 'Z');
INSERT INTO `ey_region` VALUES ('26045', '南岳区', '3', '26001', 'N');
INSERT INTO `ey_region` VALUES ('26051', '衡阳县', '3', '26001', 'H');
INSERT INTO `ey_region` VALUES ('26080', '衡南县', '3', '26001', 'H');
INSERT INTO `ey_region` VALUES ('26112', '衡山县', '3', '26001', 'H');
INSERT INTO `ey_region` VALUES ('26130', '衡东县', '3', '26001', 'H');
INSERT INTO `ey_region` VALUES ('26155', '祁东县', '3', '26001', 'Q');
INSERT INTO `ey_region` VALUES ('26179', '耒阳市', '3', '26001', 'L');
INSERT INTO `ey_region` VALUES ('26215', '常宁市', '3', '26001', 'C');
INSERT INTO `ey_region` VALUES ('26242', '邵阳市', '2', '25579', 'S');
INSERT INTO `ey_region` VALUES ('26243', '市辖区', '3', '26242', 'S');
INSERT INTO `ey_region` VALUES ('26244', '双清区', '3', '26242', 'S');
INSERT INTO `ey_region` VALUES ('26257', '大祥区', '3', '26242', 'D');
INSERT INTO `ey_region` VALUES ('26272', '北塔区', '3', '26242', 'B');
INSERT INTO `ey_region` VALUES ('26279', '邵东县', '3', '26242', 'S');
INSERT INTO `ey_region` VALUES ('26306', '新邵县', '3', '26242', 'X');
INSERT INTO `ey_region` VALUES ('26322', '邵阳县', '3', '26242', 'S');
INSERT INTO `ey_region` VALUES ('26348', '隆回县', '3', '26242', 'L');
INSERT INTO `ey_region` VALUES ('26375', '洞口县', '3', '26242', 'D');
INSERT INTO `ey_region` VALUES ('26399', '绥宁县', '3', '26242', 'S');
INSERT INTO `ey_region` VALUES ('26425', '新宁县', '3', '26242', 'X');
INSERT INTO `ey_region` VALUES ('26444', '城步县', '3', '26242', 'C');
INSERT INTO `ey_region` VALUES ('26465', '武冈市', '3', '26242', 'W');
INSERT INTO `ey_region` VALUES ('26485', '岳阳市', '2', '25579', 'Y');
INSERT INTO `ey_region` VALUES ('26486', '市辖区', '3', '26485', 'S');
INSERT INTO `ey_region` VALUES ('26487', '岳阳楼区', '3', '26485', 'Y');
INSERT INTO `ey_region` VALUES ('26511', '云溪区', '3', '26485', 'Y');
INSERT INTO `ey_region` VALUES ('26521', '君山区', '3', '26485', 'J');
INSERT INTO `ey_region` VALUES ('26529', '岳阳县', '3', '26485', 'Y');
INSERT INTO `ey_region` VALUES ('26551', '华容县', '3', '26485', 'H');
INSERT INTO `ey_region` VALUES ('26572', '湘阴县', '3', '26485', 'X');
INSERT INTO `ey_region` VALUES ('26592', '平江县', '3', '26485', 'P');
INSERT INTO `ey_region` VALUES ('26620', '汩罗市', '3', '26485', 'G');
INSERT INTO `ey_region` VALUES ('26657', '临湘市', '3', '26485', 'L');
INSERT INTO `ey_region` VALUES ('26683', '常德市', '2', '25579', 'C');
INSERT INTO `ey_region` VALUES ('26684', '市辖区', '3', '26683', 'S');
INSERT INTO `ey_region` VALUES ('26685', '武陵区', '3', '26683', 'W');
INSERT INTO `ey_region` VALUES ('26702', '鼎城区', '3', '26683', 'D');
INSERT INTO `ey_region` VALUES ('26741', '安乡县', '3', '26683', 'A');
INSERT INTO `ey_region` VALUES ('26762', '汉寿县', '3', '26683', 'H');
INSERT INTO `ey_region` VALUES ('26793', '澧县', '3', '26683', 'L');
INSERT INTO `ey_region` VALUES ('26826', '临澧县', '3', '26683', 'L');
INSERT INTO `ey_region` VALUES ('26844', '桃源县', '3', '26683', 'T');
INSERT INTO `ey_region` VALUES ('26885', '石门县', '3', '26683', 'S');
INSERT INTO `ey_region` VALUES ('26912', '津市市', '3', '26683', 'J');
INSERT INTO `ey_region` VALUES ('26925', '张家界市', '2', '25579', 'Z');
INSERT INTO `ey_region` VALUES ('26926', '市辖区', '3', '26925', 'S');
INSERT INTO `ey_region` VALUES ('26927', '永定区', '3', '26925', 'Y');
INSERT INTO `ey_region` VALUES ('26959', '武陵源区', '3', '26925', 'W');
INSERT INTO `ey_region` VALUES ('26966', '慈利县', '3', '26925', 'C');
INSERT INTO `ey_region` VALUES ('26998', '桑植县', '3', '26925', 'S');
INSERT INTO `ey_region` VALUES ('27038', '益阳市', '2', '25579', 'Y');
INSERT INTO `ey_region` VALUES ('27039', '市辖区', '3', '27038', 'S');
INSERT INTO `ey_region` VALUES ('27040', '资阳区', '3', '27038', 'Z');
INSERT INTO `ey_region` VALUES ('27049', '赫山区', '3', '27038', 'H');
INSERT INTO `ey_region` VALUES ('27069', '南县', '3', '27038', 'N');
INSERT INTO `ey_region` VALUES ('27087', '桃江县', '3', '27038', 'T');
INSERT INTO `ey_region` VALUES ('27106', '安化县', '3', '27038', 'A');
INSERT INTO `ey_region` VALUES ('27130', '沅江市', '3', '27038', 'Y');
INSERT INTO `ey_region` VALUES ('27147', '郴州市', '2', '25579', 'C');
INSERT INTO `ey_region` VALUES ('27148', '市辖区', '3', '27147', 'S');
INSERT INTO `ey_region` VALUES ('27149', '北湖区', '3', '27147', 'B');
INSERT INTO `ey_region` VALUES ('27168', '苏仙区', '3', '27147', 'S');
INSERT INTO `ey_region` VALUES ('27188', '桂阳县', '3', '27147', 'G');
INSERT INTO `ey_region` VALUES ('27228', '宜章县', '3', '27147', 'Y');
INSERT INTO `ey_region` VALUES ('27256', '永兴县', '3', '27147', 'Y');
INSERT INTO `ey_region` VALUES ('27282', '嘉禾县', '3', '27147', 'J');
INSERT INTO `ey_region` VALUES ('27300', '临武县', '3', '27147', 'L');
INSERT INTO `ey_region` VALUES ('27323', '汝城县', '3', '27147', 'R');
INSERT INTO `ey_region` VALUES ('27347', '桂东县', '3', '27147', 'G');
INSERT INTO `ey_region` VALUES ('27367', '安仁县', '3', '27147', 'A');
INSERT INTO `ey_region` VALUES ('27389', '资兴市', '3', '27147', 'Z');
INSERT INTO `ey_region` VALUES ('27418', '永州市', '2', '25579', 'Y');
INSERT INTO `ey_region` VALUES ('27419', '市辖区', '3', '27418', 'S');
INSERT INTO `ey_region` VALUES ('27420', '零陵区', '3', '27418', 'L');
INSERT INTO `ey_region` VALUES ('27437', '冷水滩区', '3', '27418', 'L');
INSERT INTO `ey_region` VALUES ('27459', '祁阳县', '3', '27418', 'Q');
INSERT INTO `ey_region` VALUES ('27492', '东安县', '3', '27418', 'D');
INSERT INTO `ey_region` VALUES ('27511', '双牌县', '3', '27418', 'S');
INSERT INTO `ey_region` VALUES ('27527', '道县', '3', '27418', 'D');
INSERT INTO `ey_region` VALUES ('27554', '江永县', '3', '27418', 'J');
INSERT INTO `ey_region` VALUES ('27567', '宁远县', '3', '27418', 'N');
INSERT INTO `ey_region` VALUES ('27585', '蓝山县', '3', '27418', 'L');
INSERT INTO `ey_region` VALUES ('27606', '新田县', '3', '27418', 'X');
INSERT INTO `ey_region` VALUES ('27626', '江华县', '3', '27418', 'J');
INSERT INTO `ey_region` VALUES ('27650', '怀化市', '2', '25579', 'H');
INSERT INTO `ey_region` VALUES ('27651', '市辖区', '3', '27650', 'S');
INSERT INTO `ey_region` VALUES ('27652', '鹤城区', '3', '27650', 'H');
INSERT INTO `ey_region` VALUES ('27667', '中方县', '3', '27650', 'Z');
INSERT INTO `ey_region` VALUES ('27690', '沅陵县', '3', '27650', 'Y');
INSERT INTO `ey_region` VALUES ('27714', '辰溪县', '3', '27650', 'C');
INSERT INTO `ey_region` VALUES ('27745', '溆浦县', '3', '27650', 'X');
INSERT INTO `ey_region` VALUES ('27789', '会同县', '3', '27650', 'H');
INSERT INTO `ey_region` VALUES ('27815', '麻阳县', '3', '27650', 'M');
INSERT INTO `ey_region` VALUES ('27839', '新晃县', '3', '27650', 'X');
INSERT INTO `ey_region` VALUES ('27863', '芷江县', '3', '27650', 'Z');
INSERT INTO `ey_region` VALUES ('27892', '靖州苗族侗族县', '3', '27650', 'J');
INSERT INTO `ey_region` VALUES ('27906', '通道县', '3', '27650', 'T');
INSERT INTO `ey_region` VALUES ('27930', '洪江市', '3', '27650', 'H');
INSERT INTO `ey_region` VALUES ('27963', '娄底市', '2', '25579', 'L');
INSERT INTO `ey_region` VALUES ('27964', '市辖区', '3', '27963', 'S');
INSERT INTO `ey_region` VALUES ('27965', '娄星区', '3', '27963', 'L');
INSERT INTO `ey_region` VALUES ('27980', '双峰县', '3', '27963', 'S');
INSERT INTO `ey_region` VALUES ('27997', '新化县', '3', '27963', 'X');
INSERT INTO `ey_region` VALUES ('28027', '冷水江市', '3', '27963', 'L');
INSERT INTO `ey_region` VALUES ('28044', '涟源市', '3', '27963', 'L');
INSERT INTO `ey_region` VALUES ('28065', '湘西州', '2', '25579', 'X');
INSERT INTO `ey_region` VALUES ('28066', '吉首市', '3', '28065', 'J');
INSERT INTO `ey_region` VALUES ('28082', '泸溪县', '3', '28065', 'L');
INSERT INTO `ey_region` VALUES ('28099', '凤凰县', '3', '28065', 'F');
INSERT INTO `ey_region` VALUES ('28124', '花垣县', '3', '28065', 'H');
INSERT INTO `ey_region` VALUES ('28143', '保靖县', '3', '28065', 'B');
INSERT INTO `ey_region` VALUES ('28161', '古丈县', '3', '28065', 'G');
INSERT INTO `ey_region` VALUES ('28174', '永顺县', '3', '28065', 'Y');
INSERT INTO `ey_region` VALUES ('28205', '龙山县', '3', '28065', 'L');
INSERT INTO `ey_region` VALUES ('28240', '广东省', '1', '0', 'G');
INSERT INTO `ey_region` VALUES ('28241', '广州市', '2', '28240', 'G');
INSERT INTO `ey_region` VALUES ('28242', '市辖区', '3', '28241', 'S');
INSERT INTO `ey_region` VALUES ('28243', '荔湾区', '3', '28241', 'L');
INSERT INTO `ey_region` VALUES ('28266', '越秀区', '3', '28241', 'Y');
INSERT INTO `ey_region` VALUES ('28289', '海珠区', '3', '28241', 'H');
INSERT INTO `ey_region` VALUES ('28308', '天河区', '3', '28241', 'T');
INSERT INTO `ey_region` VALUES ('28330', '白云区', '3', '28241', 'B');
INSERT INTO `ey_region` VALUES ('28349', '黄埔区', '3', '28241', 'H');
INSERT INTO `ey_region` VALUES ('28359', '番禺区', '3', '28241', 'F');
INSERT INTO `ey_region` VALUES ('28377', '花都区', '3', '28241', 'H');
INSERT INTO `ey_region` VALUES ('28386', '南沙区', '3', '28241', 'N');
INSERT INTO `ey_region` VALUES ('28392', '萝岗区', '3', '28241', 'L');
INSERT INTO `ey_region` VALUES ('28399', '增城市', '3', '28241', 'Z');
INSERT INTO `ey_region` VALUES ('28409', '从化市', '3', '28241', 'C');
INSERT INTO `ey_region` VALUES ('28421', '韶关市', '2', '28240', 'S');
INSERT INTO `ey_region` VALUES ('28422', '市辖区', '3', '28421', 'S');
INSERT INTO `ey_region` VALUES ('28423', '武江区', '3', '28421', 'W');
INSERT INTO `ey_region` VALUES ('28431', '浈江区', '3', '28421', 'Z');
INSERT INTO `ey_region` VALUES ('28448', '曲江区', '3', '28421', 'Q');
INSERT INTO `ey_region` VALUES ('28463', '始兴县', '3', '28421', 'S');
INSERT INTO `ey_region` VALUES ('28475', '仁化县', '3', '28421', 'R');
INSERT INTO `ey_region` VALUES ('28488', '翁源县', '3', '28421', 'W');
INSERT INTO `ey_region` VALUES ('28497', '乳源县', '3', '28421', 'R');
INSERT INTO `ey_region` VALUES ('28509', '新丰县', '3', '28421', 'X');
INSERT INTO `ey_region` VALUES ('28517', '乐昌市', '3', '28421', 'L');
INSERT INTO `ey_region` VALUES ('28539', '南雄市', '3', '28421', 'N');
INSERT INTO `ey_region` VALUES ('28558', '深圳市', '2', '28240', 'S');
INSERT INTO `ey_region` VALUES ('28559', '市辖区', '3', '28558', 'S');
INSERT INTO `ey_region` VALUES ('28560', '罗湖区', '3', '28558', 'L');
INSERT INTO `ey_region` VALUES ('28571', '福田区', '3', '28558', 'F');
INSERT INTO `ey_region` VALUES ('28581', '南山区', '3', '28558', 'N');
INSERT INTO `ey_region` VALUES ('28590', '宝安区', '3', '28558', 'B');
INSERT INTO `ey_region` VALUES ('28604', '龙岗区', '3', '28558', 'L');
INSERT INTO `ey_region` VALUES ('28619', '盐田区', '3', '28558', 'Y');
INSERT INTO `ey_region` VALUES ('28626', '珠海市', '2', '28240', 'Z');
INSERT INTO `ey_region` VALUES ('28627', '市辖区', '3', '28626', 'S');
INSERT INTO `ey_region` VALUES ('28628', '香洲区', '3', '28626', 'X');
INSERT INTO `ey_region` VALUES ('28646', '斗门区', '3', '28626', 'D');
INSERT INTO `ey_region` VALUES ('28654', '金湾区', '3', '28626', 'J');
INSERT INTO `ey_region` VALUES ('28659', '汕头市', '2', '28240', 'S');
INSERT INTO `ey_region` VALUES ('28660', '市辖区', '3', '28659', 'S');
INSERT INTO `ey_region` VALUES ('28661', '龙湖区', '3', '28659', 'L');
INSERT INTO `ey_region` VALUES ('28669', '金平区', '3', '28659', 'J');
INSERT INTO `ey_region` VALUES ('28687', '濠江区', '3', '28659', 'H');
INSERT INTO `ey_region` VALUES ('28695', '潮阳区', '3', '28659', 'C');
INSERT INTO `ey_region` VALUES ('28709', '潮南区', '3', '28659', 'C');
INSERT INTO `ey_region` VALUES ('28721', '澄海区', '3', '28659', 'C');
INSERT INTO `ey_region` VALUES ('28733', '南澳县', '3', '28659', 'N');
INSERT INTO `ey_region` VALUES ('28737', '佛山市', '2', '28240', 'F');
INSERT INTO `ey_region` VALUES ('28738', '市辖区', '3', '28737', 'S');
INSERT INTO `ey_region` VALUES ('28739', '禅城区', '3', '28737', 'C');
INSERT INTO `ey_region` VALUES ('28744', '南海区', '3', '28737', 'N');
INSERT INTO `ey_region` VALUES ('28753', '顺德区', '3', '28737', 'S');
INSERT INTO `ey_region` VALUES ('28764', '三水区', '3', '28737', 'S');
INSERT INTO `ey_region` VALUES ('28776', '高明区', '3', '28737', 'G');
INSERT INTO `ey_region` VALUES ('28785', '江门市', '2', '28240', 'J');
INSERT INTO `ey_region` VALUES ('28786', '市辖区', '3', '28785', 'S');
INSERT INTO `ey_region` VALUES ('28787', '蓬江区', '3', '28785', 'P');
INSERT INTO `ey_region` VALUES ('28797', '江海区', '3', '28785', 'J');
INSERT INTO `ey_region` VALUES ('28803', '新会区', '3', '28785', 'X');
INSERT INTO `ey_region` VALUES ('28818', '台山市', '3', '28785', 'T');
INSERT INTO `ey_region` VALUES ('28837', '开平市', '3', '28785', 'K');
INSERT INTO `ey_region` VALUES ('28853', '鹤山市', '3', '28785', 'H');
INSERT INTO `ey_region` VALUES ('28867', '恩平市', '3', '28785', 'E');
INSERT INTO `ey_region` VALUES ('28880', '湛江市', '2', '28240', 'Z');
INSERT INTO `ey_region` VALUES ('28881', '市辖区', '3', '28880', 'S');
INSERT INTO `ey_region` VALUES ('28882', '湛江市赤坎区', '3', '28880', 'Z');
INSERT INTO `ey_region` VALUES ('28891', '湛江市霞山区', '3', '28880', 'Z');
INSERT INTO `ey_region` VALUES ('28904', '湛江市坡头区', '3', '28880', 'Z');
INSERT INTO `ey_region` VALUES ('28914', '湛江市麻章区', '3', '28880', 'Z');
INSERT INTO `ey_region` VALUES ('28923', '遂溪县', '3', '28880', 'S');
INSERT INTO `ey_region` VALUES ('28941', '徐闻县', '3', '28880', 'X');
INSERT INTO `ey_region` VALUES ('28962', '廉江市', '3', '28880', 'L');
INSERT INTO `ey_region` VALUES ('28984', '雷州市', '3', '28880', 'L');
INSERT INTO `ey_region` VALUES ('29010', '吴川市', '3', '28880', 'W');
INSERT INTO `ey_region` VALUES ('29026', '茂名市', '2', '28240', 'M');
INSERT INTO `ey_region` VALUES ('29027', '市辖区', '3', '29026', 'S');
INSERT INTO `ey_region` VALUES ('29028', '茂南区', '3', '29026', 'M');
INSERT INTO `ey_region` VALUES ('29045', '茂港区', '3', '29026', 'M');
INSERT INTO `ey_region` VALUES ('29053', '电白县', '3', '29026', 'D');
INSERT INTO `ey_region` VALUES ('29075', '高州市', '3', '29026', 'G');
INSERT INTO `ey_region` VALUES ('29107', '化州市', '3', '29026', 'H');
INSERT INTO `ey_region` VALUES ('29138', '信宜市', '3', '29026', 'X');
INSERT INTO `ey_region` VALUES ('29159', '肇庆市', '2', '28240', 'Z');
INSERT INTO `ey_region` VALUES ('29160', '市辖区', '3', '29159', 'S');
INSERT INTO `ey_region` VALUES ('29161', '端州区', '3', '29159', 'D');
INSERT INTO `ey_region` VALUES ('29169', '鼎湖区', '3', '29159', 'D');
INSERT INTO `ey_region` VALUES ('29178', '广宁县', '3', '29159', 'G');
INSERT INTO `ey_region` VALUES ('29196', '怀集县', '3', '29159', 'H');
INSERT INTO `ey_region` VALUES ('29217', '封开县', '3', '29159', 'F');
INSERT INTO `ey_region` VALUES ('29234', '德庆县', '3', '29159', 'D');
INSERT INTO `ey_region` VALUES ('29248', '高要市', '3', '29159', 'G');
INSERT INTO `ey_region` VALUES ('29266', '四会市', '3', '29159', 'S');
INSERT INTO `ey_region` VALUES ('29282', '惠州市', '2', '28240', 'H');
INSERT INTO `ey_region` VALUES ('29283', '市辖区', '3', '29282', 'S');
INSERT INTO `ey_region` VALUES ('29284', '惠城区', '3', '29282', 'H');
INSERT INTO `ey_region` VALUES ('29304', '惠阳区', '3', '29282', 'H');
INSERT INTO `ey_region` VALUES ('29317', '博罗县', '3', '29282', 'B');
INSERT INTO `ey_region` VALUES ('29335', '惠东县', '3', '29282', 'H');
INSERT INTO `ey_region` VALUES ('29355', '龙门县', '3', '29282', 'L');
INSERT INTO `ey_region` VALUES ('29371', '梅州市', '2', '28240', 'M');
INSERT INTO `ey_region` VALUES ('29372', '市辖区', '3', '29371', 'S');
INSERT INTO `ey_region` VALUES ('29373', '梅江区', '3', '29371', 'M');
INSERT INTO `ey_region` VALUES ('29380', '梅县', '3', '29371', 'M');
INSERT INTO `ey_region` VALUES ('29400', '大埔县', '3', '29371', 'D');
INSERT INTO `ey_region` VALUES ('29418', '丰顺县', '3', '29371', 'F');
INSERT INTO `ey_region` VALUES ('29436', '五华县', '3', '29371', 'W');
INSERT INTO `ey_region` VALUES ('29453', '平远县', '3', '29371', 'P');
INSERT INTO `ey_region` VALUES ('29466', '蕉岭县', '3', '29371', 'J');
INSERT INTO `ey_region` VALUES ('29477', '兴宁市', '3', '29371', 'X');
INSERT INTO `ey_region` VALUES ('29498', '汕尾市', '2', '28240', 'S');
INSERT INTO `ey_region` VALUES ('29499', '市辖区', '3', '29498', 'S');
INSERT INTO `ey_region` VALUES ('29500', '城区', '3', '29498', 'C');
INSERT INTO `ey_region` VALUES ('29511', '海丰县', '3', '29498', 'H');
INSERT INTO `ey_region` VALUES ('29529', '陆河县', '3', '29498', 'L');
INSERT INTO `ey_region` VALUES ('29538', '陆丰市', '3', '29498', 'L');
INSERT INTO `ey_region` VALUES ('29568', '河源市', '2', '28240', 'H');
INSERT INTO `ey_region` VALUES ('29569', '市辖区', '3', '29568', 'S');
INSERT INTO `ey_region` VALUES ('29570', '源城区', '3', '29568', 'Y');
INSERT INTO `ey_region` VALUES ('29578', '紫金县', '3', '29568', 'Z');
INSERT INTO `ey_region` VALUES ('29599', '龙川县', '3', '29568', 'L');
INSERT INTO `ey_region` VALUES ('29625', '连平县', '3', '29568', 'L');
INSERT INTO `ey_region` VALUES ('29639', '和平县', '3', '29568', 'H');
INSERT INTO `ey_region` VALUES ('29657', '东源县', '3', '29568', 'D');
INSERT INTO `ey_region` VALUES ('29679', '阳江市', '2', '28240', 'Y');
INSERT INTO `ey_region` VALUES ('29680', '市辖区', '3', '29679', 'S');
INSERT INTO `ey_region` VALUES ('29681', '江城区', '3', '29679', 'J');
INSERT INTO `ey_region` VALUES ('29698', '阳西县', '3', '29679', 'Y');
INSERT INTO `ey_region` VALUES ('29709', '阳东县', '3', '29679', 'Y');
INSERT INTO `ey_region` VALUES ('29729', '阳春市', '3', '29679', 'Y');
INSERT INTO `ey_region` VALUES ('29755', '清远市', '2', '28240', 'Q');
INSERT INTO `ey_region` VALUES ('29756', '市辖区', '3', '29755', 'S');
INSERT INTO `ey_region` VALUES ('29757', '清城区', '3', '29755', 'Q');
INSERT INTO `ey_region` VALUES ('29766', '佛冈县', '3', '29755', 'F');
INSERT INTO `ey_region` VALUES ('29773', '阳山县', '3', '29755', 'Y');
INSERT INTO `ey_region` VALUES ('29787', '连山县', '3', '29755', 'L');
INSERT INTO `ey_region` VALUES ('29797', '连南县', '3', '29755', 'L');
INSERT INTO `ey_region` VALUES ('29805', '清新县', '3', '29755', 'Q');
INSERT INTO `ey_region` VALUES ('29816', '英德市', '3', '29755', 'Y');
INSERT INTO `ey_region` VALUES ('29842', '连州市', '3', '29755', 'L');
INSERT INTO `ey_region` VALUES ('29855', '东莞市', '2', '28240', 'D');
INSERT INTO `ey_region` VALUES ('29890', '中山市', '2', '28240', 'Z');
INSERT INTO `ey_region` VALUES ('29915', '潮州市', '2', '28240', 'C');
INSERT INTO `ey_region` VALUES ('29916', '市辖区', '3', '29915', 'S');
INSERT INTO `ey_region` VALUES ('29917', '潮州市湘桥区', '3', '29915', 'C');
INSERT INTO `ey_region` VALUES ('29930', '潮州市潮安县', '3', '29915', 'C');
INSERT INTO `ey_region` VALUES ('29954', '潮州市饶平县', '3', '29915', 'C');
INSERT INTO `ey_region` VALUES ('29977', '揭阳市', '2', '28240', 'J');
INSERT INTO `ey_region` VALUES ('29978', '市辖区', '3', '29977', 'S');
INSERT INTO `ey_region` VALUES ('29979', '榕城区', '3', '29977', 'R');
INSERT INTO `ey_region` VALUES ('29990', '揭东县', '3', '29977', 'J');
INSERT INTO `ey_region` VALUES ('30008', '揭西县', '3', '29977', 'J');
INSERT INTO `ey_region` VALUES ('30032', '惠来县', '3', '29977', 'H');
INSERT INTO `ey_region` VALUES ('30054', '普宁市', '3', '29977', 'P');
INSERT INTO `ey_region` VALUES ('30086', '云浮市', '2', '28240', 'Y');
INSERT INTO `ey_region` VALUES ('30087', '市辖区', '3', '30086', 'S');
INSERT INTO `ey_region` VALUES ('30088', '云城区', '3', '30086', 'Y');
INSERT INTO `ey_region` VALUES ('30096', '新兴县', '3', '30086', 'X');
INSERT INTO `ey_region` VALUES ('30112', '郁南县', '3', '30086', 'Y');
INSERT INTO `ey_region` VALUES ('30132', '云安县', '3', '30086', 'Y');
INSERT INTO `ey_region` VALUES ('30141', '罗定市', '3', '30086', 'L');
INSERT INTO `ey_region` VALUES ('30164', '广西', '1', '0', 'G');
INSERT INTO `ey_region` VALUES ('30165', '南宁市', '2', '30164', 'N');
INSERT INTO `ey_region` VALUES ('30166', '市辖区', '3', '30165', 'S');
INSERT INTO `ey_region` VALUES ('30167', '兴宁区', '3', '30165', 'X');
INSERT INTO `ey_region` VALUES ('30174', '青秀区', '3', '30165', 'Q');
INSERT INTO `ey_region` VALUES ('30186', '江南区', '3', '30165', 'J');
INSERT INTO `ey_region` VALUES ('30196', '西乡塘区', '3', '30165', 'X');
INSERT INTO `ey_region` VALUES ('30214', '良庆区', '3', '30165', 'L');
INSERT INTO `ey_region` VALUES ('30222', '邕宁区', '3', '30165', 'Y');
INSERT INTO `ey_region` VALUES ('30228', '武鸣县', '3', '30165', 'W');
INSERT INTO `ey_region` VALUES ('30245', '隆安县', '3', '30165', 'L');
INSERT INTO `ey_region` VALUES ('30257', '马山县', '3', '30165', 'M');
INSERT INTO `ey_region` VALUES ('30270', '上林县', '3', '30165', 'S');
INSERT INTO `ey_region` VALUES ('30282', '宾阳县', '3', '30165', 'B');
INSERT INTO `ey_region` VALUES ('30300', '横县', '3', '30165', 'H');
INSERT INTO `ey_region` VALUES ('30319', '柳州市', '2', '30164', 'L');
INSERT INTO `ey_region` VALUES ('30320', '市辖区', '3', '30319', 'S');
INSERT INTO `ey_region` VALUES ('30321', '城中区', '3', '30319', 'C');
INSERT INTO `ey_region` VALUES ('30329', '鱼峰区', '3', '30319', 'Y');
INSERT INTO `ey_region` VALUES ('30338', '柳南区', '3', '30319', 'L');
INSERT INTO `ey_region` VALUES ('30348', '柳北区', '3', '30319', 'L');
INSERT INTO `ey_region` VALUES ('30361', '柳江县', '3', '30319', 'L');
INSERT INTO `ey_region` VALUES ('30374', '柳城县', '3', '30319', 'L');
INSERT INTO `ey_region` VALUES ('30387', '鹿寨县', '3', '30319', 'L');
INSERT INTO `ey_region` VALUES ('30398', '融安县', '3', '30319', 'R');
INSERT INTO `ey_region` VALUES ('30411', '融水县', '3', '30319', 'R');
INSERT INTO `ey_region` VALUES ('30432', '三江县', '3', '30319', 'S');
INSERT INTO `ey_region` VALUES ('30448', '桂林市', '2', '30164', 'G');
INSERT INTO `ey_region` VALUES ('30449', '市辖区', '3', '30448', 'S');
INSERT INTO `ey_region` VALUES ('30450', '秀峰区', '3', '30448', 'X');
INSERT INTO `ey_region` VALUES ('30454', '叠彩区', '3', '30448', 'D');
INSERT INTO `ey_region` VALUES ('30458', '象山区', '3', '30448', 'X');
INSERT INTO `ey_region` VALUES ('30463', '七星区', '3', '30448', 'Q');
INSERT INTO `ey_region` VALUES ('30469', '雁山区', '3', '30448', 'Y');
INSERT INTO `ey_region` VALUES ('30475', '阳朔县', '3', '30448', 'Y');
INSERT INTO `ey_region` VALUES ('30485', '临桂县', '3', '30448', 'L');
INSERT INTO `ey_region` VALUES ('30497', '灵川县', '3', '30448', 'L');
INSERT INTO `ey_region` VALUES ('30509', '全州县', '3', '30448', 'Q');
INSERT INTO `ey_region` VALUES ('30528', '兴安县', '3', '30448', 'X');
INSERT INTO `ey_region` VALUES ('30539', '永福县', '3', '30448', 'Y');
INSERT INTO `ey_region` VALUES ('30549', '灌阳县', '3', '30448', 'G');
INSERT INTO `ey_region` VALUES ('30559', '龙胜县', '3', '30448', 'L');
INSERT INTO `ey_region` VALUES ('30570', '资源县', '3', '30448', 'Z');
INSERT INTO `ey_region` VALUES ('30578', '平乐县', '3', '30448', 'P');
INSERT INTO `ey_region` VALUES ('30589', '荔浦县', '3', '30448', 'L');
INSERT INTO `ey_region` VALUES ('30603', '恭城县', '3', '30448', 'G');
INSERT INTO `ey_region` VALUES ('30613', '梧州市', '2', '30164', 'W');
INSERT INTO `ey_region` VALUES ('30614', '市辖区', '3', '30613', 'S');
INSERT INTO `ey_region` VALUES ('30615', '万秀区', '3', '30613', 'W');
INSERT INTO `ey_region` VALUES ('30622', '蝶山区', '3', '30613', 'D');
INSERT INTO `ey_region` VALUES ('30628', '长洲区', '3', '30613', 'C');
INSERT INTO `ey_region` VALUES ('30633', '苍梧县', '3', '30613', 'C');
INSERT INTO `ey_region` VALUES ('30646', '藤县', '3', '30613', 'T');
INSERT INTO `ey_region` VALUES ('30663', '蒙山县', '3', '30613', 'M');
INSERT INTO `ey_region` VALUES ('30673', '岑溪市', '3', '30613', 'C');
INSERT INTO `ey_region` VALUES ('30688', '北海市', '2', '30164', 'B');
INSERT INTO `ey_region` VALUES ('30689', '市辖区', '3', '30688', 'S');
INSERT INTO `ey_region` VALUES ('30690', '海城区', '3', '30688', 'H');
INSERT INTO `ey_region` VALUES ('30699', '银海区', '3', '30688', 'Y');
INSERT INTO `ey_region` VALUES ('30704', '铁山港区', '3', '30688', 'T');
INSERT INTO `ey_region` VALUES ('30708', '合浦县', '3', '30688', 'H');
INSERT INTO `ey_region` VALUES ('30724', '防城港市', '2', '30164', 'F');
INSERT INTO `ey_region` VALUES ('30725', '市辖区', '3', '30724', 'S');
INSERT INTO `ey_region` VALUES ('30726', '港口区', '3', '30724', 'G');
INSERT INTO `ey_region` VALUES ('30732', '防城区', '3', '30724', 'F');
INSERT INTO `ey_region` VALUES ('30748', '上思县', '3', '30724', 'S');
INSERT INTO `ey_region` VALUES ('30758', '东兴市', '3', '30724', 'D');
INSERT INTO `ey_region` VALUES ('30762', '钦州市', '2', '30164', 'Q');
INSERT INTO `ey_region` VALUES ('30763', '市辖区', '3', '30762', 'S');
INSERT INTO `ey_region` VALUES ('30764', '钦南区', '3', '30762', 'Q');
INSERT INTO `ey_region` VALUES ('30783', '钦北区', '3', '30762', 'Q');
INSERT INTO `ey_region` VALUES ('30796', '灵山县', '3', '30762', 'L');
INSERT INTO `ey_region` VALUES ('30817', '浦北县', '3', '30762', 'P');
INSERT INTO `ey_region` VALUES ('30834', '贵港市', '2', '30164', 'G');
INSERT INTO `ey_region` VALUES ('30835', '市辖区', '3', '30834', 'S');
INSERT INTO `ey_region` VALUES ('30836', '港北区', '3', '30834', 'G');
INSERT INTO `ey_region` VALUES ('30845', '港南区', '3', '30834', 'G');
INSERT INTO `ey_region` VALUES ('30855', '覃塘区', '3', '30834', 'Q');
INSERT INTO `ey_region` VALUES ('30866', '平南县', '3', '30834', 'P');
INSERT INTO `ey_region` VALUES ('30888', '桂平市', '3', '30834', 'G');
INSERT INTO `ey_region` VALUES ('30915', '玉林市', '2', '30164', 'Y');
INSERT INTO `ey_region` VALUES ('30916', '市辖区', '3', '30915', 'S');
INSERT INTO `ey_region` VALUES ('30917', '玉州区', '3', '30915', 'Y');
INSERT INTO `ey_region` VALUES ('30933', '容县', '3', '30915', 'R');
INSERT INTO `ey_region` VALUES ('30949', '陆川县', '3', '30915', 'L');
INSERT INTO `ey_region` VALUES ('30964', '博白县', '3', '30915', 'B');
INSERT INTO `ey_region` VALUES ('30993', '兴业县', '3', '30915', 'X');
INSERT INTO `ey_region` VALUES ('31007', '北流市', '3', '30915', 'B');
INSERT INTO `ey_region` VALUES ('31033', '百色市', '2', '30164', 'B');
INSERT INTO `ey_region` VALUES ('31034', '市辖区', '3', '31033', 'S');
INSERT INTO `ey_region` VALUES ('31035', '右江区', '3', '31033', 'Y');
INSERT INTO `ey_region` VALUES ('31045', '田阳县', '3', '31033', 'T');
INSERT INTO `ey_region` VALUES ('31056', '田东县', '3', '31033', 'T');
INSERT INTO `ey_region` VALUES ('31067', '平果县', '3', '31033', 'P');
INSERT INTO `ey_region` VALUES ('31081', '德保县', '3', '31033', 'D');
INSERT INTO `ey_region` VALUES ('31095', '靖西县', '3', '31033', 'J');
INSERT INTO `ey_region` VALUES ('31115', '那坡县', '3', '31033', 'N');
INSERT INTO `ey_region` VALUES ('31125', '凌云县', '3', '31033', 'L');
INSERT INTO `ey_region` VALUES ('31134', '乐业县', '3', '31033', 'L');
INSERT INTO `ey_region` VALUES ('31143', '田林县', '3', '31033', 'T');
INSERT INTO `ey_region` VALUES ('31158', '西林县', '3', '31033', 'X');
INSERT INTO `ey_region` VALUES ('31167', '隆林县', '3', '31033', 'L');
INSERT INTO `ey_region` VALUES ('31184', '贺州市', '2', '30164', 'H');
INSERT INTO `ey_region` VALUES ('31185', '市辖区', '3', '31184', 'S');
INSERT INTO `ey_region` VALUES ('31186', '八步区', '3', '31184', 'B');
INSERT INTO `ey_region` VALUES ('31208', '昭平县', '3', '31184', 'Z');
INSERT INTO `ey_region` VALUES ('31221', '钟山县', '3', '31184', 'Z');
INSERT INTO `ey_region` VALUES ('31236', '富川县', '3', '31184', 'F');
INSERT INTO `ey_region` VALUES ('31249', '河池市', '2', '30164', 'H');
INSERT INTO `ey_region` VALUES ('31250', '市辖区', '3', '31249', 'S');
INSERT INTO `ey_region` VALUES ('31251', '金城江区', '3', '31249', 'J');
INSERT INTO `ey_region` VALUES ('31264', '南丹县', '3', '31249', 'N');
INSERT INTO `ey_region` VALUES ('31276', '天峨县', '3', '31249', 'T');
INSERT INTO `ey_region` VALUES ('31286', '凤山县', '3', '31249', 'F');
INSERT INTO `ey_region` VALUES ('31296', '东兰县', '3', '31249', 'D');
INSERT INTO `ey_region` VALUES ('31311', '罗城县', '3', '31249', 'L');
INSERT INTO `ey_region` VALUES ('31323', '环江县', '3', '31249', 'H');
INSERT INTO `ey_region` VALUES ('31336', '巴马县', '3', '31249', 'B');
INSERT INTO `ey_region` VALUES ('31347', '都安县', '3', '31249', 'D');
INSERT INTO `ey_region` VALUES ('31367', '大化县', '3', '31249', 'D');
INSERT INTO `ey_region` VALUES ('31384', '宜州市', '3', '31249', 'Y');
INSERT INTO `ey_region` VALUES ('31401', '来宾市', '2', '30164', 'L');
INSERT INTO `ey_region` VALUES ('31402', '市辖区', '3', '31401', 'S');
INSERT INTO `ey_region` VALUES ('31403', '兴宾区', '3', '31401', 'X');
INSERT INTO `ey_region` VALUES ('31427', '忻城县', '3', '31401', 'X');
INSERT INTO `ey_region` VALUES ('31440', '象州县', '3', '31401', 'X');
INSERT INTO `ey_region` VALUES ('31452', '武宣县', '3', '31401', 'W');
INSERT INTO `ey_region` VALUES ('31463', '金秀县', '3', '31401', 'J');
INSERT INTO `ey_region` VALUES ('31474', '合山市', '3', '31401', 'H');
INSERT INTO `ey_region` VALUES ('31478', '崇左市', '2', '30164', 'C');
INSERT INTO `ey_region` VALUES ('31479', '市辖区', '3', '31478', 'S');
INSERT INTO `ey_region` VALUES ('31480', '江州区', '3', '31478', 'J');
INSERT INTO `ey_region` VALUES ('31490', '扶绥县', '3', '31478', 'F');
INSERT INTO `ey_region` VALUES ('31502', '宁明县', '3', '31478', 'N');
INSERT INTO `ey_region` VALUES ('31516', '龙州县', '3', '31478', 'L');
INSERT INTO `ey_region` VALUES ('31529', '大新县', '3', '31478', 'D');
INSERT INTO `ey_region` VALUES ('31544', '天等县', '3', '31478', 'T');
INSERT INTO `ey_region` VALUES ('31558', '凭祥市', '3', '31478', 'P');
INSERT INTO `ey_region` VALUES ('31563', '海南省', '1', '0', 'H');
INSERT INTO `ey_region` VALUES ('31564', '海口市', '2', '31563', 'H');
INSERT INTO `ey_region` VALUES ('31565', '市辖区', '3', '31564', 'S');
INSERT INTO `ey_region` VALUES ('31566', '秀英区', '3', '31564', 'X');
INSERT INTO `ey_region` VALUES ('31575', '龙华区', '3', '31564', 'L');
INSERT INTO `ey_region` VALUES ('31587', '琼山区', '3', '31564', 'Q');
INSERT INTO `ey_region` VALUES ('31601', '美兰区', '3', '31564', 'M');
INSERT INTO `ey_region` VALUES ('31618', '三亚市', '2', '31563', 'S');
INSERT INTO `ey_region` VALUES ('31619', '市辖区', '3', '31618', 'S');
INSERT INTO `ey_region` VALUES ('31634', '五指山市', '2', '31563', 'W');
INSERT INTO `ey_region` VALUES ('31635', '冲山镇', '3', '31634', 'C');
INSERT INTO `ey_region` VALUES ('31636', '南圣镇', '3', '31634', 'N');
INSERT INTO `ey_region` VALUES ('31637', '毛阳镇', '3', '31634', 'M');
INSERT INTO `ey_region` VALUES ('31638', '番阳镇', '3', '31634', 'F');
INSERT INTO `ey_region` VALUES ('31639', '畅好乡', '3', '31634', 'C');
INSERT INTO `ey_region` VALUES ('31640', '毛道乡', '3', '31634', 'M');
INSERT INTO `ey_region` VALUES ('31641', '水满乡', '3', '31634', 'S');
INSERT INTO `ey_region` VALUES ('31642', '国营畅好农场', '3', '31634', 'G');
INSERT INTO `ey_region` VALUES ('31643', '琼海市', '2', '31563', 'Q');
INSERT INTO `ey_region` VALUES ('31644', '嘉积镇', '3', '31643', 'J');
INSERT INTO `ey_region` VALUES ('31645', '万泉镇', '3', '31643', 'W');
INSERT INTO `ey_region` VALUES ('31646', '石壁镇', '3', '31643', 'S');
INSERT INTO `ey_region` VALUES ('31647', '中原镇', '3', '31643', 'Z');
INSERT INTO `ey_region` VALUES ('31648', '博敖镇', '3', '31643', 'B');
INSERT INTO `ey_region` VALUES ('31649', '阳江镇', '3', '31643', 'Y');
INSERT INTO `ey_region` VALUES ('31650', '龙江镇', '3', '31643', 'L');
INSERT INTO `ey_region` VALUES ('31651', '潭门镇', '3', '31643', 'T');
INSERT INTO `ey_region` VALUES ('31652', '塔洋镇', '3', '31643', 'T');
INSERT INTO `ey_region` VALUES ('31653', '长坡镇', '3', '31643', 'C');
INSERT INTO `ey_region` VALUES ('31654', '大路镇', '3', '31643', 'D');
INSERT INTO `ey_region` VALUES ('31655', '会山镇', '3', '31643', 'H');
INSERT INTO `ey_region` VALUES ('31656', '国营东太农场', '3', '31643', 'G');
INSERT INTO `ey_region` VALUES ('31657', '国营东平农场', '3', '31643', 'G');
INSERT INTO `ey_region` VALUES ('31658', '国营东红农场', '3', '31643', 'G');
INSERT INTO `ey_region` VALUES ('31659', '国营东升农场', '3', '31643', 'G');
INSERT INTO `ey_region` VALUES ('31660', '国营南俸农场', '3', '31643', 'G');
INSERT INTO `ey_region` VALUES ('31661', '彬村山华侨农场', '3', '31643', 'B');
INSERT INTO `ey_region` VALUES ('31662', '儋州市', '2', '31563', 'D');
INSERT INTO `ey_region` VALUES ('31663', '那大镇', '3', '31662', 'N');
INSERT INTO `ey_region` VALUES ('31664', '和庆镇', '3', '31662', 'H');
INSERT INTO `ey_region` VALUES ('31665', '南丰镇', '3', '31662', 'N');
INSERT INTO `ey_region` VALUES ('31666', '大成镇', '3', '31662', 'D');
INSERT INTO `ey_region` VALUES ('31667', '雅星镇', '3', '31662', 'Y');
INSERT INTO `ey_region` VALUES ('31668', '兰洋镇', '3', '31662', 'L');
INSERT INTO `ey_region` VALUES ('31669', '光村镇', '3', '31662', 'G');
INSERT INTO `ey_region` VALUES ('31670', '木棠镇', '3', '31662', 'M');
INSERT INTO `ey_region` VALUES ('31671', '海头镇', '3', '31662', 'H');
INSERT INTO `ey_region` VALUES ('31672', '峨蔓镇', '3', '31662', 'E');
INSERT INTO `ey_region` VALUES ('31673', '三都镇', '3', '31662', 'S');
INSERT INTO `ey_region` VALUES ('31674', '王五镇', '3', '31662', 'W');
INSERT INTO `ey_region` VALUES ('31675', '白马井镇', '3', '31662', 'B');
INSERT INTO `ey_region` VALUES ('31676', '中和镇', '3', '31662', 'Z');
INSERT INTO `ey_region` VALUES ('31677', '排浦镇', '3', '31662', 'P');
INSERT INTO `ey_region` VALUES ('31678', '东成镇', '3', '31662', 'D');
INSERT INTO `ey_region` VALUES ('31679', '新州镇', '3', '31662', 'X');
INSERT INTO `ey_region` VALUES ('31680', '国营西培农场', '3', '31662', 'G');
INSERT INTO `ey_region` VALUES ('31681', '国营西华农场', '3', '31662', 'G');
INSERT INTO `ey_region` VALUES ('31682', '国营西庆农场', '3', '31662', 'G');
INSERT INTO `ey_region` VALUES ('31683', '国营西流农场', '3', '31662', 'G');
INSERT INTO `ey_region` VALUES ('31684', '国营西联农场', '3', '31662', 'G');
INSERT INTO `ey_region` VALUES ('31685', '国营蓝洋农场', '3', '31662', 'G');
INSERT INTO `ey_region` VALUES ('31686', '国营新盈农场', '3', '31662', 'G');
INSERT INTO `ey_region` VALUES ('31687', '国营八一农场东山分场', '3', '31662', 'G');
INSERT INTO `ey_region` VALUES ('31688', '国营八一农场金川分场', '3', '31662', 'G');
INSERT INTO `ey_region` VALUES ('31689', '国营八一农场长岭分场', '3', '31662', 'G');
INSERT INTO `ey_region` VALUES ('31690', '国营八一农场英岛分场', '3', '31662', 'G');
INSERT INTO `ey_region` VALUES ('31691', '国营八一农场春江分场', '3', '31662', 'G');
INSERT INTO `ey_region` VALUES ('31692', '国营八一农场强打管区', '3', '31662', 'G');
INSERT INTO `ey_region` VALUES ('31693', '国营龙山农场', '3', '31662', 'G');
INSERT INTO `ey_region` VALUES ('31694', '国营红岭农场', '3', '31662', 'G');
INSERT INTO `ey_region` VALUES ('31695', '洋浦经济开发区', '3', '31662', 'Y');
INSERT INTO `ey_region` VALUES ('31696', '华南热作学院', '3', '31662', 'H');
INSERT INTO `ey_region` VALUES ('31697', '文昌市', '2', '31563', 'W');
INSERT INTO `ey_region` VALUES ('31698', '文城镇', '3', '31697', 'W');
INSERT INTO `ey_region` VALUES ('31699', '重兴镇', '3', '31697', 'Z');
INSERT INTO `ey_region` VALUES ('31700', '蓬莱镇', '3', '31697', 'P');
INSERT INTO `ey_region` VALUES ('31701', '会文镇', '3', '31697', 'H');
INSERT INTO `ey_region` VALUES ('31702', '东路镇', '3', '31697', 'D');
INSERT INTO `ey_region` VALUES ('31703', '潭牛镇', '3', '31697', 'T');
INSERT INTO `ey_region` VALUES ('31704', '东阁镇', '3', '31697', 'D');
INSERT INTO `ey_region` VALUES ('31705', '文教镇', '3', '31697', 'W');
INSERT INTO `ey_region` VALUES ('31706', '东郊镇', '3', '31697', 'D');
INSERT INTO `ey_region` VALUES ('31707', '龙楼镇', '3', '31697', 'L');
INSERT INTO `ey_region` VALUES ('31708', '昌洒镇', '3', '31697', 'C');
INSERT INTO `ey_region` VALUES ('31709', '翁田镇', '3', '31697', 'W');
INSERT INTO `ey_region` VALUES ('31710', '抱罗镇', '3', '31697', 'B');
INSERT INTO `ey_region` VALUES ('31711', '冯坡镇', '3', '31697', 'F');
INSERT INTO `ey_region` VALUES ('31712', '锦山镇', '3', '31697', 'J');
INSERT INTO `ey_region` VALUES ('31713', '铺前镇', '3', '31697', 'P');
INSERT INTO `ey_region` VALUES ('31714', '国营东路农场', '3', '31697', 'G');
INSERT INTO `ey_region` VALUES ('31715', '国营南阳农场', '3', '31697', 'G');
INSERT INTO `ey_region` VALUES ('31716', '国营罗豆农场', '3', '31697', 'G');
INSERT INTO `ey_region` VALUES ('31717', '国营文昌橡胶研究所', '3', '31697', 'G');
INSERT INTO `ey_region` VALUES ('31718', '万宁市', '2', '31563', 'W');
INSERT INTO `ey_region` VALUES ('31719', '万城镇', '3', '31718', 'W');
INSERT INTO `ey_region` VALUES ('31720', '龙滚镇', '3', '31718', 'L');
INSERT INTO `ey_region` VALUES ('31721', '和乐镇', '3', '31718', 'H');
INSERT INTO `ey_region` VALUES ('31722', '后安镇', '3', '31718', 'H');
INSERT INTO `ey_region` VALUES ('31723', '大茂镇', '3', '31718', 'D');
INSERT INTO `ey_region` VALUES ('31724', '东澳镇', '3', '31718', 'D');
INSERT INTO `ey_region` VALUES ('31725', '礼纪镇', '3', '31718', 'L');
INSERT INTO `ey_region` VALUES ('31726', '长丰镇', '3', '31718', 'C');
INSERT INTO `ey_region` VALUES ('31727', '山根镇', '3', '31718', 'S');
INSERT INTO `ey_region` VALUES ('31728', '北大镇', '3', '31718', 'B');
INSERT INTO `ey_region` VALUES ('31729', '南桥镇', '3', '31718', 'N');
INSERT INTO `ey_region` VALUES ('31730', '三更罗镇', '3', '31718', 'S');
INSERT INTO `ey_region` VALUES ('31731', '国营东兴农场', '3', '31718', 'G');
INSERT INTO `ey_region` VALUES ('31732', '国营东和农场', '3', '31718', 'G');
INSERT INTO `ey_region` VALUES ('31733', '国营东岭农场', '3', '31718', 'G');
INSERT INTO `ey_region` VALUES ('31734', '国营南林农场', '3', '31718', 'G');
INSERT INTO `ey_region` VALUES ('31735', '国营新中农场', '3', '31718', 'G');
INSERT INTO `ey_region` VALUES ('31736', '兴隆华侨农场', '3', '31718', 'X');
INSERT INTO `ey_region` VALUES ('31737', '地方国营六连林场', '3', '31718', 'D');
INSERT INTO `ey_region` VALUES ('31738', '东方市', '2', '31563', 'D');
INSERT INTO `ey_region` VALUES ('31739', '八所镇', '3', '31738', 'B');
INSERT INTO `ey_region` VALUES ('31740', '东河镇', '3', '31738', 'D');
INSERT INTO `ey_region` VALUES ('31741', '大田镇', '3', '31738', 'D');
INSERT INTO `ey_region` VALUES ('31742', '感城镇', '3', '31738', 'G');
INSERT INTO `ey_region` VALUES ('31743', '板桥镇', '3', '31738', 'B');
INSERT INTO `ey_region` VALUES ('31744', '三家镇', '3', '31738', 'S');
INSERT INTO `ey_region` VALUES ('31745', '四更镇', '3', '31738', 'S');
INSERT INTO `ey_region` VALUES ('31746', '新龙镇', '3', '31738', 'X');
INSERT INTO `ey_region` VALUES ('31747', '天安乡', '3', '31738', 'T');
INSERT INTO `ey_region` VALUES ('31748', '江边乡', '3', '31738', 'J');
INSERT INTO `ey_region` VALUES ('31749', '国营广坝农场', '3', '31738', 'G');
INSERT INTO `ey_region` VALUES ('31750', '国营公爱农场', '3', '31738', 'G');
INSERT INTO `ey_region` VALUES ('31751', '国营红泉农场', '3', '31738', 'G');
INSERT INTO `ey_region` VALUES ('31752', '省国营东方华侨农场', '3', '31738', 'S');
INSERT INTO `ey_region` VALUES ('31753', '定安县', '2', '31563', 'D');
INSERT INTO `ey_region` VALUES ('31754', '定城镇', '3', '31753', 'D');
INSERT INTO `ey_region` VALUES ('31755', '新竹镇', '3', '31753', 'X');
INSERT INTO `ey_region` VALUES ('31756', '龙湖镇', '3', '31753', 'L');
INSERT INTO `ey_region` VALUES ('31757', '黄竹镇', '3', '31753', 'H');
INSERT INTO `ey_region` VALUES ('31758', '雷鸣镇', '3', '31753', 'L');
INSERT INTO `ey_region` VALUES ('31759', '龙门镇', '3', '31753', 'L');
INSERT INTO `ey_region` VALUES ('31760', '龙河镇', '3', '31753', 'L');
INSERT INTO `ey_region` VALUES ('31761', '岭口镇', '3', '31753', 'L');
INSERT INTO `ey_region` VALUES ('31762', '翰林镇', '3', '31753', 'H');
INSERT INTO `ey_region` VALUES ('31763', '富文镇', '3', '31753', 'F');
INSERT INTO `ey_region` VALUES ('31764', '国营中瑞农场', '3', '31753', 'G');
INSERT INTO `ey_region` VALUES ('31765', '国营南海农场', '3', '31753', 'G');
INSERT INTO `ey_region` VALUES ('31766', '国营金鸡岭农场', '3', '31753', 'G');
INSERT INTO `ey_region` VALUES ('31767', '定安热作研究所', '3', '31753', 'D');
INSERT INTO `ey_region` VALUES ('31768', '屯昌县', '2', '31563', 'T');
INSERT INTO `ey_region` VALUES ('31769', '屯城镇', '3', '31768', 'T');
INSERT INTO `ey_region` VALUES ('31770', '新兴镇', '3', '31768', 'X');
INSERT INTO `ey_region` VALUES ('31771', '枫木镇', '3', '31768', 'F');
INSERT INTO `ey_region` VALUES ('31772', '乌坡镇', '3', '31768', 'W');
INSERT INTO `ey_region` VALUES ('31773', '南吕镇', '3', '31768', 'N');
INSERT INTO `ey_region` VALUES ('31774', '南坤镇', '3', '31768', 'N');
INSERT INTO `ey_region` VALUES ('31775', '坡心镇', '3', '31768', 'P');
INSERT INTO `ey_region` VALUES ('31776', '西昌镇', '3', '31768', 'X');
INSERT INTO `ey_region` VALUES ('31777', '国营中建农场', '3', '31768', 'G');
INSERT INTO `ey_region` VALUES ('31778', '国营中坤农场', '3', '31768', 'G');
INSERT INTO `ey_region` VALUES ('31779', '国营黄岭农场', '3', '31768', 'G');
INSERT INTO `ey_region` VALUES ('31780', '国营南吕农场', '3', '31768', 'G');
INSERT INTO `ey_region` VALUES ('31781', '国营广青农场', '3', '31768', 'G');
INSERT INTO `ey_region` VALUES ('31782', '国营晨星农场', '3', '31768', 'G');
INSERT INTO `ey_region` VALUES ('31783', '澄迈县', '2', '31563', 'C');
INSERT INTO `ey_region` VALUES ('31784', '金江镇', '3', '31783', 'J');
INSERT INTO `ey_region` VALUES ('31785', '老城镇', '3', '31783', 'L');
INSERT INTO `ey_region` VALUES ('31786', '瑞溪镇', '3', '31783', 'R');
INSERT INTO `ey_region` VALUES ('31787', '永发镇', '3', '31783', 'Y');
INSERT INTO `ey_region` VALUES ('31788', '加乐镇', '3', '31783', 'J');
INSERT INTO `ey_region` VALUES ('31789', '文儒镇', '3', '31783', 'W');
INSERT INTO `ey_region` VALUES ('31790', '中兴镇', '3', '31783', 'Z');
INSERT INTO `ey_region` VALUES ('31791', '仁兴镇', '3', '31783', 'R');
INSERT INTO `ey_region` VALUES ('31792', '福山镇', '3', '31783', 'F');
INSERT INTO `ey_region` VALUES ('31793', '桥头镇', '3', '31783', 'Q');
INSERT INTO `ey_region` VALUES ('31794', '国营红光农场', '3', '31783', 'G');
INSERT INTO `ey_region` VALUES ('31795', '国营红岗农场', '3', '31783', 'G');
INSERT INTO `ey_region` VALUES ('31796', '国营西达农场', '3', '31783', 'G');
INSERT INTO `ey_region` VALUES ('31797', '国营昆仑农场', '3', '31783', 'G');
INSERT INTO `ey_region` VALUES ('31798', '国营和岭农场', '3', '31783', 'G');
INSERT INTO `ey_region` VALUES ('31799', '国营金安农场', '3', '31783', 'G');
INSERT INTO `ey_region` VALUES ('31800', '澄迈县华侨农场', '3', '31783', 'C');
INSERT INTO `ey_region` VALUES ('31801', '临高县', '2', '31563', 'L');
INSERT INTO `ey_region` VALUES ('31802', '临城镇', '3', '31801', 'L');
INSERT INTO `ey_region` VALUES ('31803', '波莲镇', '3', '31801', 'B');
INSERT INTO `ey_region` VALUES ('31804', '东英镇', '3', '31801', 'D');
INSERT INTO `ey_region` VALUES ('31805', '博厚镇', '3', '31801', 'B');
INSERT INTO `ey_region` VALUES ('31806', '皇桐镇', '3', '31801', 'H');
INSERT INTO `ey_region` VALUES ('31807', '多文镇', '3', '31801', 'D');
INSERT INTO `ey_region` VALUES ('31808', '和舍镇', '3', '31801', 'H');
INSERT INTO `ey_region` VALUES ('31809', '南宝镇', '3', '31801', 'N');
INSERT INTO `ey_region` VALUES ('31810', '新盈镇', '3', '31801', 'X');
INSERT INTO `ey_region` VALUES ('31811', '调楼镇', '3', '31801', 'D');
INSERT INTO `ey_region` VALUES ('31812', '国营红华农场', '3', '31801', 'G');
INSERT INTO `ey_region` VALUES ('31813', '国营加来农场', '3', '31801', 'G');
INSERT INTO `ey_region` VALUES ('31814', '白沙县', '2', '31563', 'B');
INSERT INTO `ey_region` VALUES ('31815', '牙叉镇', '3', '31814', 'Y');
INSERT INTO `ey_region` VALUES ('31816', '七坊镇', '3', '31814', 'Q');
INSERT INTO `ey_region` VALUES ('31817', '邦溪镇', '3', '31814', 'B');
INSERT INTO `ey_region` VALUES ('31818', '打安镇', '3', '31814', 'D');
INSERT INTO `ey_region` VALUES ('31819', '细水乡', '3', '31814', 'X');
INSERT INTO `ey_region` VALUES ('31820', '元门乡', '3', '31814', 'Y');
INSERT INTO `ey_region` VALUES ('31821', '南开乡', '3', '31814', 'N');
INSERT INTO `ey_region` VALUES ('31822', '阜龙乡', '3', '31814', 'F');
INSERT INTO `ey_region` VALUES ('31823', '青松乡', '3', '31814', 'Q');
INSERT INTO `ey_region` VALUES ('31824', '金波乡', '3', '31814', 'J');
INSERT INTO `ey_region` VALUES ('31825', '荣邦乡', '3', '31814', 'R');
INSERT INTO `ey_region` VALUES ('31826', '国营金波农场', '3', '31814', 'G');
INSERT INTO `ey_region` VALUES ('31827', '国营白沙农场', '3', '31814', 'G');
INSERT INTO `ey_region` VALUES ('31828', '国营牙叉农场', '3', '31814', 'G');
INSERT INTO `ey_region` VALUES ('31829', '国营卫星农场', '3', '31814', 'G');
INSERT INTO `ey_region` VALUES ('31830', '国营龙江农场', '3', '31814', 'G');
INSERT INTO `ey_region` VALUES ('31831', '国营珠碧江农场', '3', '31814', 'G');
INSERT INTO `ey_region` VALUES ('31832', '国营芙蓉田农场', '3', '31814', 'G');
INSERT INTO `ey_region` VALUES ('31833', '国营大岭农场', '3', '31814', 'G');
INSERT INTO `ey_region` VALUES ('31834', '国营邦溪农场', '3', '31814', 'G');
INSERT INTO `ey_region` VALUES ('31835', '昌江县', '2', '31563', 'C');
INSERT INTO `ey_region` VALUES ('31836', '石碌镇', '3', '31835', 'S');
INSERT INTO `ey_region` VALUES ('31837', '叉河镇', '3', '31835', 'C');
INSERT INTO `ey_region` VALUES ('31838', '十月田镇', '3', '31835', 'S');
INSERT INTO `ey_region` VALUES ('31839', '乌烈镇', '3', '31835', 'W');
INSERT INTO `ey_region` VALUES ('31840', '昌化镇', '3', '31835', 'C');
INSERT INTO `ey_region` VALUES ('31841', '海尾镇', '3', '31835', 'H');
INSERT INTO `ey_region` VALUES ('31842', '七叉镇', '3', '31835', 'Q');
INSERT INTO `ey_region` VALUES ('31843', '王下乡', '3', '31835', 'W');
INSERT INTO `ey_region` VALUES ('31844', '国营红田农场', '3', '31835', 'G');
INSERT INTO `ey_region` VALUES ('31845', '国营红林农场', '3', '31835', 'G');
INSERT INTO `ey_region` VALUES ('31846', '国营坝王岭林场', '3', '31835', 'G');
INSERT INTO `ey_region` VALUES ('31847', '海南钢铁公司', '3', '31835', 'H');
INSERT INTO `ey_region` VALUES ('31848', '乐东县', '2', '31563', 'L');
INSERT INTO `ey_region` VALUES ('31849', '抱由镇', '3', '31848', 'B');
INSERT INTO `ey_region` VALUES ('31850', '万冲镇', '3', '31848', 'W');
INSERT INTO `ey_region` VALUES ('31851', '大安镇', '3', '31848', 'D');
INSERT INTO `ey_region` VALUES ('31852', '志仲镇', '3', '31848', 'Z');
INSERT INTO `ey_region` VALUES ('31853', '千家镇', '3', '31848', 'Q');
INSERT INTO `ey_region` VALUES ('31854', '九所镇', '3', '31848', 'J');
INSERT INTO `ey_region` VALUES ('31855', '利国镇', '3', '31848', 'L');
INSERT INTO `ey_region` VALUES ('31856', '黄流镇', '3', '31848', 'H');
INSERT INTO `ey_region` VALUES ('31857', '佛罗镇', '3', '31848', 'F');
INSERT INTO `ey_region` VALUES ('31858', '尖峰镇', '3', '31848', 'J');
INSERT INTO `ey_region` VALUES ('31859', '莺歌海镇', '3', '31848', 'Y');
INSERT INTO `ey_region` VALUES ('31860', '国营乐中农场', '3', '31848', 'G');
INSERT INTO `ey_region` VALUES ('31861', '国营山荣农场', '3', '31848', 'G');
INSERT INTO `ey_region` VALUES ('31862', '国营乐光农场', '3', '31848', 'G');
INSERT INTO `ey_region` VALUES ('31863', '国营报伦农场', '3', '31848', 'G');
INSERT INTO `ey_region` VALUES ('31864', '国营福报农场', '3', '31848', 'G');
INSERT INTO `ey_region` VALUES ('31865', '国营保国农场', '3', '31848', 'G');
INSERT INTO `ey_region` VALUES ('31866', '国营保显农场', '3', '31848', 'G');
INSERT INTO `ey_region` VALUES ('31867', '国营尖峰岭林业公司', '3', '31848', 'G');
INSERT INTO `ey_region` VALUES ('31868', '国营莺歌海盐场', '3', '31848', 'G');
INSERT INTO `ey_region` VALUES ('31869', '陵水县', '2', '31563', 'L');
INSERT INTO `ey_region` VALUES ('31870', '椰林镇', '3', '31869', 'Y');
INSERT INTO `ey_region` VALUES ('31871', '光坡镇', '3', '31869', 'G');
INSERT INTO `ey_region` VALUES ('31872', '三才镇', '3', '31869', 'S');
INSERT INTO `ey_region` VALUES ('31873', '英州镇', '3', '31869', 'Y');
INSERT INTO `ey_region` VALUES ('31874', '隆广镇', '3', '31869', 'L');
INSERT INTO `ey_region` VALUES ('31875', '文罗镇', '3', '31869', 'W');
INSERT INTO `ey_region` VALUES ('31876', '本号镇', '3', '31869', 'B');
INSERT INTO `ey_region` VALUES ('31877', '新村镇', '3', '31869', 'X');
INSERT INTO `ey_region` VALUES ('31878', '黎安镇', '3', '31869', 'L');
INSERT INTO `ey_region` VALUES ('31879', '提蒙乡', '3', '31869', 'T');
INSERT INTO `ey_region` VALUES ('31880', '群英乡', '3', '31869', 'Q');
INSERT INTO `ey_region` VALUES ('31881', '国营岭门农场', '3', '31869', 'G');
INSERT INTO `ey_region` VALUES ('31882', '国营南平农场', '3', '31869', 'G');
INSERT INTO `ey_region` VALUES ('31883', '国营吊罗山林业公司', '3', '31869', 'G');
INSERT INTO `ey_region` VALUES ('31884', '保亭县', '2', '31563', 'B');
INSERT INTO `ey_region` VALUES ('31885', '保城镇', '3', '31884', 'B');
INSERT INTO `ey_region` VALUES ('31886', '什玲镇', '3', '31884', 'S');
INSERT INTO `ey_region` VALUES ('31887', '加茂镇', '3', '31884', 'J');
INSERT INTO `ey_region` VALUES ('31888', '响水镇', '3', '31884', 'X');
INSERT INTO `ey_region` VALUES ('31889', '新政镇', '3', '31884', 'X');
INSERT INTO `ey_region` VALUES ('31890', '三道镇', '3', '31884', 'S');
INSERT INTO `ey_region` VALUES ('31891', '六弓乡', '3', '31884', 'L');
INSERT INTO `ey_region` VALUES ('31892', '南林乡', '3', '31884', 'N');
INSERT INTO `ey_region` VALUES ('31893', '毛感乡', '3', '31884', 'M');
INSERT INTO `ey_region` VALUES ('31894', '国营五指山茶场', '3', '31884', 'G');
INSERT INTO `ey_region` VALUES ('31895', '国营新星农场', '3', '31884', 'G');
INSERT INTO `ey_region` VALUES ('31896', '国营保亭热作所', '3', '31884', 'G');
INSERT INTO `ey_region` VALUES ('31897', '国营金江农场', '3', '31884', 'G');
INSERT INTO `ey_region` VALUES ('31898', '国营南茂农场', '3', '31884', 'G');
INSERT INTO `ey_region` VALUES ('31899', '国营三道农场', '3', '31884', 'G');
INSERT INTO `ey_region` VALUES ('31900', '琼中县', '2', '31563', 'Q');
INSERT INTO `ey_region` VALUES ('31901', '营根镇', '3', '31900', 'Y');
INSERT INTO `ey_region` VALUES ('31902', '湾岭镇', '3', '31900', 'W');
INSERT INTO `ey_region` VALUES ('31903', '黎母山镇', '3', '31900', 'L');
INSERT INTO `ey_region` VALUES ('31904', '和平镇', '3', '31900', 'H');
INSERT INTO `ey_region` VALUES ('31905', '长征镇', '3', '31900', 'C');
INSERT INTO `ey_region` VALUES ('31906', '红毛镇', '3', '31900', 'H');
INSERT INTO `ey_region` VALUES ('31907', '中平镇', '3', '31900', 'Z');
INSERT INTO `ey_region` VALUES ('31908', '吊罗山乡', '3', '31900', 'D');
INSERT INTO `ey_region` VALUES ('31909', '上安乡', '3', '31900', 'S');
INSERT INTO `ey_region` VALUES ('31910', '什运乡', '3', '31900', 'S');
INSERT INTO `ey_region` VALUES ('31911', '国营新进农场', '3', '31900', 'G');
INSERT INTO `ey_region` VALUES ('31912', '国营大丰农场', '3', '31900', 'G');
INSERT INTO `ey_region` VALUES ('31913', '国营阳江农场', '3', '31900', 'G');
INSERT INTO `ey_region` VALUES ('31914', '国营乌石农场', '3', '31900', 'G');
INSERT INTO `ey_region` VALUES ('31915', '国营南方农场', '3', '31900', 'G');
INSERT INTO `ey_region` VALUES ('31916', '国营岭头农场', '3', '31900', 'G');
INSERT INTO `ey_region` VALUES ('31917', '国营加钗农场', '3', '31900', 'G');
INSERT INTO `ey_region` VALUES ('31918', '国营长征农场', '3', '31900', 'G');
INSERT INTO `ey_region` VALUES ('31919', '国营乘坡农场', '3', '31900', 'G');
INSERT INTO `ey_region` VALUES ('31920', '国营太平农场', '3', '31900', 'G');
INSERT INTO `ey_region` VALUES ('31921', '国营新伟农场', '3', '31900', 'G');
INSERT INTO `ey_region` VALUES ('31922', '国营黎母山林业公司', '3', '31900', 'G');
INSERT INTO `ey_region` VALUES ('31923', '西沙群岛', '3', '31924', 'X');
INSERT INTO `ey_region` VALUES ('31924', '三沙市', '2', '31563', 'S');
INSERT INTO `ey_region` VALUES ('31925', '南沙群岛', '3', '31924', 'N');
INSERT INTO `ey_region` VALUES ('47499', '香港岛', '2', '47494', 'X');
INSERT INTO `ey_region` VALUES ('31927', '中沙群岛的岛礁及其海域', '3', '31924', 'Z');
INSERT INTO `ey_region` VALUES ('47498', '九龙', '2', '47494', 'J');
INSERT INTO `ey_region` VALUES ('31929', '重庆市', '1', '0', 'Z');
INSERT INTO `ey_region` VALUES ('31930', '重庆市', '2', '31929', 'C');
INSERT INTO `ey_region` VALUES ('31931', '万州区', '3', '31930', 'W');
INSERT INTO `ey_region` VALUES ('31984', '涪陵区', '3', '31930', 'F');
INSERT INTO `ey_region` VALUES ('32031', '渝中区', '3', '31930', 'Y');
INSERT INTO `ey_region` VALUES ('32044', '大渡口区', '3', '31930', 'D');
INSERT INTO `ey_region` VALUES ('32053', '江北区', '3', '31930', 'J');
INSERT INTO `ey_region` VALUES ('32066', '沙坪坝区', '3', '31930', 'S');
INSERT INTO `ey_region` VALUES ('32093', '九龙坡区', '3', '31930', 'J');
INSERT INTO `ey_region` VALUES ('32112', '南岸区', '3', '31930', 'N');
INSERT INTO `ey_region` VALUES ('32127', '北碚区', '3', '31930', 'B');
INSERT INTO `ey_region` VALUES ('32145', '万盛区', '3', '31930', 'W');
INSERT INTO `ey_region` VALUES ('32156', '双桥区', '3', '31930', 'S');
INSERT INTO `ey_region` VALUES ('32160', '渝北区', '3', '31930', 'Y');
INSERT INTO `ey_region` VALUES ('32191', '巴南区', '3', '31930', 'B');
INSERT INTO `ey_region` VALUES ('32213', '黔江区', '3', '31930', 'Q');
INSERT INTO `ey_region` VALUES ('32244', '长寿区', '3', '31930', 'C');
INSERT INTO `ey_region` VALUES ('32263', '江津区', '3', '31930', 'J');
INSERT INTO `ey_region` VALUES ('32291', '合川区', '3', '31930', 'H');
INSERT INTO `ey_region` VALUES ('32322', '永川区', '3', '31930', 'Y');
INSERT INTO `ey_region` VALUES ('32345', '南川区', '3', '31930', 'N');
INSERT INTO `ey_region` VALUES ('32380', '县', '2', '31929', 'X');
INSERT INTO `ey_region` VALUES ('32381', '綦江县', '3', '32380', 'Q');
INSERT INTO `ey_region` VALUES ('32401', '潼南县', '3', '32380', 'T');
INSERT INTO `ey_region` VALUES ('32424', '铜梁县', '3', '32380', 'T');
INSERT INTO `ey_region` VALUES ('32453', '大足县', '3', '32380', 'D');
INSERT INTO `ey_region` VALUES ('32478', '荣昌县', '3', '32380', 'R');
INSERT INTO `ey_region` VALUES ('32499', '璧山县', '3', '32380', 'B');
INSERT INTO `ey_region` VALUES ('32513', '梁平县', '3', '32380', 'L');
INSERT INTO `ey_region` VALUES ('32549', '城口县', '3', '32380', 'C');
INSERT INTO `ey_region` VALUES ('32574', '丰都县', '3', '32380', 'F');
INSERT INTO `ey_region` VALUES ('32606', '垫江县', '3', '32380', 'D');
INSERT INTO `ey_region` VALUES ('32632', '武隆县', '3', '32380', 'W');
INSERT INTO `ey_region` VALUES ('32659', '忠县', '3', '32380', 'Z');
INSERT INTO `ey_region` VALUES ('32688', '开县', '3', '32380', 'K');
INSERT INTO `ey_region` VALUES ('32727', '云阳县', '3', '32380', 'Y');
INSERT INTO `ey_region` VALUES ('32771', '奉节县', '3', '32380', 'F');
INSERT INTO `ey_region` VALUES ('32802', '巫山县', '3', '32380', 'W');
INSERT INTO `ey_region` VALUES ('32829', '巫溪县', '3', '32380', 'W');
INSERT INTO `ey_region` VALUES ('32861', '石柱县', '3', '32380', 'S');
INSERT INTO `ey_region` VALUES ('32894', '秀山县', '3', '32380', 'X');
INSERT INTO `ey_region` VALUES ('32927', '酉阳县', '3', '32380', 'Y');
INSERT INTO `ey_region` VALUES ('32967', '彭水县', '3', '32380', 'P');
INSERT INTO `ey_region` VALUES ('33007', '四川省', '1', '0', 'S');
INSERT INTO `ey_region` VALUES ('33008', '成都市', '2', '33007', 'C');
INSERT INTO `ey_region` VALUES ('33009', '市辖区', '3', '33008', 'S');
INSERT INTO `ey_region` VALUES ('33010', '锦江区', '3', '33008', 'J');
INSERT INTO `ey_region` VALUES ('33027', '青羊区', '3', '33008', 'Q');
INSERT INTO `ey_region` VALUES ('33042', '金牛区', '3', '33008', 'J');
INSERT INTO `ey_region` VALUES ('33058', '武侯区', '3', '33008', 'W');
INSERT INTO `ey_region` VALUES ('33076', '成华区', '3', '33008', 'C');
INSERT INTO `ey_region` VALUES ('33091', '龙泉驿区', '3', '33008', 'L');
INSERT INTO `ey_region` VALUES ('33104', '青白江区', '3', '33008', 'Q');
INSERT INTO `ey_region` VALUES ('33116', '新都区', '3', '33008', 'X');
INSERT INTO `ey_region` VALUES ('33130', '温江区', '3', '33008', 'W');
INSERT INTO `ey_region` VALUES ('33141', '金堂县', '3', '33008', 'J');
INSERT INTO `ey_region` VALUES ('33163', '双流县', '3', '33008', 'S');
INSERT INTO `ey_region` VALUES ('33189', '郫县', '3', '33008', 'P');
INSERT INTO `ey_region` VALUES ('33205', '大邑县', '3', '33008', 'D');
INSERT INTO `ey_region` VALUES ('33226', '蒲江县', '3', '33008', 'P');
INSERT INTO `ey_region` VALUES ('33239', '新津县', '3', '33008', 'X');
INSERT INTO `ey_region` VALUES ('33252', '都江堰市', '3', '33008', 'D');
INSERT INTO `ey_region` VALUES ('33272', '彭州市', '3', '33008', 'P');
INSERT INTO `ey_region` VALUES ('33293', '邛崃市', '3', '33008', 'Q');
INSERT INTO `ey_region` VALUES ('33318', '崇州市', '3', '33008', 'C');
INSERT INTO `ey_region` VALUES ('33344', '自贡市', '2', '33007', 'Z');
INSERT INTO `ey_region` VALUES ('33345', '市辖区', '3', '33344', 'S');
INSERT INTO `ey_region` VALUES ('33346', '自流井区', '3', '33344', 'Z');
INSERT INTO `ey_region` VALUES ('33360', '贡井区', '3', '33344', 'G');
INSERT INTO `ey_region` VALUES ('33374', '大安区', '3', '33344', 'D');
INSERT INTO `ey_region` VALUES ('33391', '沿滩区', '3', '33344', 'Y');
INSERT INTO `ey_region` VALUES ('33405', '荣县', '3', '33344', 'R');
INSERT INTO `ey_region` VALUES ('33433', '富顺县', '3', '33344', 'F');
INSERT INTO `ey_region` VALUES ('33460', '攀枝花市', '2', '33007', 'P');
INSERT INTO `ey_region` VALUES ('33461', '市辖区', '3', '33460', 'S');
INSERT INTO `ey_region` VALUES ('33462', '攀枝花东区', '3', '33460', 'P');
INSERT INTO `ey_region` VALUES ('33473', '西区', '3', '33460', 'X');
INSERT INTO `ey_region` VALUES ('33481', '仁和区', '3', '33460', 'R');
INSERT INTO `ey_region` VALUES ('33497', '米易县', '3', '33460', 'M');
INSERT INTO `ey_region` VALUES ('33511', '盐边县', '3', '33460', 'Y');
INSERT INTO `ey_region` VALUES ('33528', '泸州市', '2', '33007', 'L');
INSERT INTO `ey_region` VALUES ('33529', '市辖区', '3', '33528', 'S');
INSERT INTO `ey_region` VALUES ('33530', '江阳区', '3', '33528', 'J');
INSERT INTO `ey_region` VALUES ('33548', '纳溪区', '3', '33528', 'N');
INSERT INTO `ey_region` VALUES ('33563', '龙马潭区', '3', '33528', 'L');
INSERT INTO `ey_region` VALUES ('33577', '泸县', '3', '33528', 'L');
INSERT INTO `ey_region` VALUES ('33597', '合江县', '3', '33528', 'H');
INSERT INTO `ey_region` VALUES ('33625', '叙永县', '3', '33528', 'X');
INSERT INTO `ey_region` VALUES ('33654', '古蔺县', '3', '33528', 'G');
INSERT INTO `ey_region` VALUES ('33681', '德阳市', '2', '33007', 'D');
INSERT INTO `ey_region` VALUES ('33682', '市辖区', '3', '33681', 'S');
INSERT INTO `ey_region` VALUES ('33683', '旌阳区', '3', '33681', 'J');
INSERT INTO `ey_region` VALUES ('33701', '中江县', '3', '33681', 'Z');
INSERT INTO `ey_region` VALUES ('33747', '罗江县', '3', '33681', 'L');
INSERT INTO `ey_region` VALUES ('33758', '广汉市', '3', '33681', 'G');
INSERT INTO `ey_region` VALUES ('33778', '什邡市', '3', '33681', 'S');
INSERT INTO `ey_region` VALUES ('33795', '绵竹市', '3', '33681', 'M');
INSERT INTO `ey_region` VALUES ('33817', '绵阳市', '2', '33007', 'M');
INSERT INTO `ey_region` VALUES ('33818', '市辖区', '3', '33817', 'S');
INSERT INTO `ey_region` VALUES ('33819', '涪城区', '3', '33817', 'F');
INSERT INTO `ey_region` VALUES ('33844', '游仙区', '3', '33817', 'Y');
INSERT INTO `ey_region` VALUES ('33873', '三台县', '3', '33817', 'S');
INSERT INTO `ey_region` VALUES ('33937', '盐亭县', '3', '33817', 'Y');
INSERT INTO `ey_region` VALUES ('33974', '安县', '3', '33817', 'A');
INSERT INTO `ey_region` VALUES ('33995', '梓潼县', '3', '33817', 'Z');
INSERT INTO `ey_region` VALUES ('34028', '北川县', '3', '33817', 'B');
INSERT INTO `ey_region` VALUES ('34049', '平武县', '3', '33817', 'P');
INSERT INTO `ey_region` VALUES ('34075', '江油市', '3', '33817', 'J');
INSERT INTO `ey_region` VALUES ('34120', '广元市', '2', '33007', 'G');
INSERT INTO `ey_region` VALUES ('34121', '市辖区', '3', '34120', 'S');
INSERT INTO `ey_region` VALUES ('34122', '市中区', '3', '34120', 'S');
INSERT INTO `ey_region` VALUES ('34143', '元坝区', '3', '34120', 'Y');
INSERT INTO `ey_region` VALUES ('34173', '朝天区', '3', '34120', 'C');
INSERT INTO `ey_region` VALUES ('34199', '旺苍县', '3', '34120', 'W');
INSERT INTO `ey_region` VALUES ('34238', '青川县', '3', '34120', 'Q');
INSERT INTO `ey_region` VALUES ('34276', '剑阁县', '3', '34120', 'J');
INSERT INTO `ey_region` VALUES ('34334', '苍溪县', '3', '34120', 'C');
INSERT INTO `ey_region` VALUES ('34376', '遂宁市', '2', '33007', 'S');
INSERT INTO `ey_region` VALUES ('34377', '市辖区', '3', '34376', 'S');
INSERT INTO `ey_region` VALUES ('34378', '船山区', '3', '34376', 'C');
INSERT INTO `ey_region` VALUES ('34404', '安居区', '3', '34376', 'A');
INSERT INTO `ey_region` VALUES ('34426', '蓬溪县', '3', '34376', 'P');
INSERT INTO `ey_region` VALUES ('34458', '射洪县', '3', '34376', 'S');
INSERT INTO `ey_region` VALUES ('34489', '大英县', '3', '34376', 'D');
INSERT INTO `ey_region` VALUES ('34501', '内江市', '2', '33007', 'N');
INSERT INTO `ey_region` VALUES ('34502', '市辖区', '3', '34501', 'S');
INSERT INTO `ey_region` VALUES ('34503', '市中区', '3', '34501', 'S');
INSERT INTO `ey_region` VALUES ('34524', '东兴区', '3', '34501', 'D');
INSERT INTO `ey_region` VALUES ('34554', '威远县', '3', '34501', 'W');
INSERT INTO `ey_region` VALUES ('34575', '资中县', '3', '34501', 'Z');
INSERT INTO `ey_region` VALUES ('34609', '隆昌县', '3', '34501', 'L');
INSERT INTO `ey_region` VALUES ('34628', '乐山市', '2', '33007', 'L');
INSERT INTO `ey_region` VALUES ('34629', '市辖区', '3', '34628', 'S');
INSERT INTO `ey_region` VALUES ('34630', '市中区', '3', '34628', 'S');
INSERT INTO `ey_region` VALUES ('34661', '沙湾区', '3', '34628', 'S');
INSERT INTO `ey_region` VALUES ('34676', '五通桥区', '3', '34628', 'W');
INSERT INTO `ey_region` VALUES ('34689', '金口河区', '3', '34628', 'J');
INSERT INTO `ey_region` VALUES ('34696', '犍为县', '3', '34628', 'Q');
INSERT INTO `ey_region` VALUES ('34727', '井研县', '3', '34628', 'J');
INSERT INTO `ey_region` VALUES ('34755', '夹江县', '3', '34628', 'J');
INSERT INTO `ey_region` VALUES ('34778', '沐川县', '3', '34628', 'M');
INSERT INTO `ey_region` VALUES ('34799', '峨边县', '3', '34628', 'E');
INSERT INTO `ey_region` VALUES ('34819', '马边县', '3', '34628', 'M');
INSERT INTO `ey_region` VALUES ('34840', '峨眉山市', '3', '34628', 'E');
INSERT INTO `ey_region` VALUES ('34859', '南充市', '2', '33007', 'N');
INSERT INTO `ey_region` VALUES ('34860', '市辖区', '3', '34859', 'S');
INSERT INTO `ey_region` VALUES ('34861', '顺庆区', '3', '34859', 'S');
INSERT INTO `ey_region` VALUES ('34890', '高坪区', '3', '34859', 'G');
INSERT INTO `ey_region` VALUES ('34923', '嘉陵区', '3', '34859', 'J');
INSERT INTO `ey_region` VALUES ('34967', '南部县', '3', '34859', 'N');
INSERT INTO `ey_region` VALUES ('35040', '营山县', '3', '34859', 'Y');
INSERT INTO `ey_region` VALUES ('35094', '蓬安县', '3', '34859', 'P');
INSERT INTO `ey_region` VALUES ('35134', '仪陇县', '3', '34859', 'Y');
INSERT INTO `ey_region` VALUES ('35193', '西充县', '3', '34859', 'X');
INSERT INTO `ey_region` VALUES ('35238', '阆中市', '3', '34859', 'L');
INSERT INTO `ey_region` VALUES ('35288', '眉山市', '2', '33007', 'M');
INSERT INTO `ey_region` VALUES ('35289', '市辖区', '3', '35288', 'S');
INSERT INTO `ey_region` VALUES ('35290', '东坡区', '3', '35288', 'D');
INSERT INTO `ey_region` VALUES ('35317', '仁寿县', '3', '35288', 'R');
INSERT INTO `ey_region` VALUES ('35378', '彭山县', '3', '35288', 'P');
INSERT INTO `ey_region` VALUES ('35392', '洪雅县', '3', '35288', 'H');
INSERT INTO `ey_region` VALUES ('35408', '丹棱县', '3', '35288', 'D');
INSERT INTO `ey_region` VALUES ('35416', '青神县', '3', '35288', 'Q');
INSERT INTO `ey_region` VALUES ('35427', '宜宾市', '2', '33007', 'Y');
INSERT INTO `ey_region` VALUES ('35428', '市辖区', '3', '35427', 'S');
INSERT INTO `ey_region` VALUES ('35429', '翠屏区', '3', '35427', 'C');
INSERT INTO `ey_region` VALUES ('35454', '宜宾县', '3', '35427', 'Y');
INSERT INTO `ey_region` VALUES ('35481', '南溪县', '3', '35427', 'N');
INSERT INTO `ey_region` VALUES ('35497', '江安县', '3', '35427', 'J');
INSERT INTO `ey_region` VALUES ('35516', '长宁县', '3', '35427', 'C');
INSERT INTO `ey_region` VALUES ('35535', '高县', '3', '35427', 'G');
INSERT INTO `ey_region` VALUES ('35555', '珙县', '3', '35427', 'G');
INSERT INTO `ey_region` VALUES ('35573', '筠连县', '3', '35427', 'J');
INSERT INTO `ey_region` VALUES ('35592', '兴文县', '3', '35427', 'X');
INSERT INTO `ey_region` VALUES ('35608', '屏山县', '3', '35427', 'P');
INSERT INTO `ey_region` VALUES ('35625', '广安市', '2', '33007', 'G');
INSERT INTO `ey_region` VALUES ('35626', '市辖区', '3', '35625', 'S');
INSERT INTO `ey_region` VALUES ('35627', '广安区', '3', '35625', 'G');
INSERT INTO `ey_region` VALUES ('35677', '岳池县', '3', '35625', 'Y');
INSERT INTO `ey_region` VALUES ('35721', '武胜县', '3', '35625', 'W');
INSERT INTO `ey_region` VALUES ('35753', '邻水县', '3', '35625', 'L');
INSERT INTO `ey_region` VALUES ('35799', '华蓥市', '3', '35625', 'H');
INSERT INTO `ey_region` VALUES ('35813', '达州市', '2', '33007', 'D');
INSERT INTO `ey_region` VALUES ('35814', '市辖区', '3', '35813', 'S');
INSERT INTO `ey_region` VALUES ('35815', '通川区', '3', '35813', 'T');
INSERT INTO `ey_region` VALUES ('35829', '达县', '3', '35813', 'D');
INSERT INTO `ey_region` VALUES ('35894', '宣汉县', '3', '35813', 'X');
INSERT INTO `ey_region` VALUES ('35949', '开江县', '3', '35813', 'K');
INSERT INTO `ey_region` VALUES ('35970', '大竹县', '3', '35813', 'D');
INSERT INTO `ey_region` VALUES ('36021', '渠县', '3', '35813', 'Q');
INSERT INTO `ey_region` VALUES ('36082', '万源市', '3', '35813', 'W');
INSERT INTO `ey_region` VALUES ('36136', '雅安市', '2', '33007', 'Y');
INSERT INTO `ey_region` VALUES ('36137', '市辖区', '3', '36136', 'S');
INSERT INTO `ey_region` VALUES ('36138', '雨城区', '3', '36136', 'Y');
INSERT INTO `ey_region` VALUES ('36161', '名山县', '3', '36136', 'M');
INSERT INTO `ey_region` VALUES ('36182', '荥经县', '3', '36136', 'Y');
INSERT INTO `ey_region` VALUES ('36204', '汉源县', '3', '36136', 'H');
INSERT INTO `ey_region` VALUES ('36245', '石棉县', '3', '36136', 'S');
INSERT INTO `ey_region` VALUES ('36263', '天全县', '3', '36136', 'T');
INSERT INTO `ey_region` VALUES ('36279', '芦山县', '3', '36136', 'L');
INSERT INTO `ey_region` VALUES ('36289', '宝兴县', '3', '36136', 'B');
INSERT INTO `ey_region` VALUES ('36299', '巴中市', '2', '33007', 'B');
INSERT INTO `ey_region` VALUES ('36300', '市辖区', '3', '36299', 'S');
INSERT INTO `ey_region` VALUES ('36301', '巴州区', '3', '36299', 'B');
INSERT INTO `ey_region` VALUES ('36354', '通江县', '3', '36299', 'T');
INSERT INTO `ey_region` VALUES ('36404', '南江县', '3', '36299', 'N');
INSERT INTO `ey_region` VALUES ('36453', '平昌县', '3', '36299', 'P');
INSERT INTO `ey_region` VALUES ('36497', '资阳市', '2', '33007', 'Z');
INSERT INTO `ey_region` VALUES ('36498', '市辖区', '3', '36497', 'S');
INSERT INTO `ey_region` VALUES ('36499', '雁江区', '3', '36497', 'Y');
INSERT INTO `ey_region` VALUES ('36527', '安岳县', '3', '36497', 'A');
INSERT INTO `ey_region` VALUES ('36597', '乐至县', '3', '36497', 'L');
INSERT INTO `ey_region` VALUES ('36623', '简阳市', '3', '36497', 'J');
INSERT INTO `ey_region` VALUES ('36679', '阿坝州', '2', '33007', 'A');
INSERT INTO `ey_region` VALUES ('36680', '汶川县', '3', '36679', 'W');
INSERT INTO `ey_region` VALUES ('36694', '理县', '3', '36679', 'L');
INSERT INTO `ey_region` VALUES ('36708', '茂县', '3', '36679', 'M');
INSERT INTO `ey_region` VALUES ('36731', '松潘县', '3', '36679', 'S');
INSERT INTO `ey_region` VALUES ('36759', '九寨沟县', '3', '36679', 'J');
INSERT INTO `ey_region` VALUES ('36778', '金川县', '3', '36679', 'J');
INSERT INTO `ey_region` VALUES ('36802', '小金县', '3', '36679', 'X');
INSERT INTO `ey_region` VALUES ('36824', '黑水县', '3', '36679', 'H');
INSERT INTO `ey_region` VALUES ('36842', '马尔康县', '3', '36679', 'M');
INSERT INTO `ey_region` VALUES ('36858', '壤塘县', '3', '36679', 'R');
INSERT INTO `ey_region` VALUES ('36871', '阿坝县', '3', '36679', 'A');
INSERT INTO `ey_region` VALUES ('36893', '若尔盖县', '3', '36679', 'R');
INSERT INTO `ey_region` VALUES ('36914', '红原县', '3', '36679', 'H');
INSERT INTO `ey_region` VALUES ('36926', '甘孜州', '2', '33007', 'G');
INSERT INTO `ey_region` VALUES ('36927', '康定县', '3', '36926', 'K');
INSERT INTO `ey_region` VALUES ('36949', '泸定县', '3', '36926', 'L');
INSERT INTO `ey_region` VALUES ('36962', '丹巴县', '3', '36926', 'D');
INSERT INTO `ey_region` VALUES ('36978', '九龙县', '3', '36926', 'J');
INSERT INTO `ey_region` VALUES ('36997', '雅江县', '3', '36926', 'Y');
INSERT INTO `ey_region` VALUES ('37015', '道孚县', '3', '36926', 'D');
INSERT INTO `ey_region` VALUES ('37038', '炉霍县', '3', '36926', 'L');
INSERT INTO `ey_region` VALUES ('37055', '甘孜县', '3', '36926', 'G');
INSERT INTO `ey_region` VALUES ('37078', '新龙县', '3', '36926', 'X');
INSERT INTO `ey_region` VALUES ('37098', '德格县', '3', '36926', 'D');
INSERT INTO `ey_region` VALUES ('37125', '白玉县', '3', '36926', 'B');
INSERT INTO `ey_region` VALUES ('37143', '石渠县', '3', '36926', 'S');
INSERT INTO `ey_region` VALUES ('37166', '色达县', '3', '36926', 'S');
INSERT INTO `ey_region` VALUES ('37184', '理塘县', '3', '36926', 'L');
INSERT INTO `ey_region` VALUES ('37209', '巴塘县', '3', '36926', 'B');
INSERT INTO `ey_region` VALUES ('37229', '乡城县', '3', '36926', 'X');
INSERT INTO `ey_region` VALUES ('37242', '稻城县', '3', '36926', 'D');
INSERT INTO `ey_region` VALUES ('37257', '得荣县', '3', '36926', 'D');
INSERT INTO `ey_region` VALUES ('37270', '凉山州', '2', '33007', 'L');
INSERT INTO `ey_region` VALUES ('37271', '西昌市', '3', '37270', 'X');
INSERT INTO `ey_region` VALUES ('37315', '木里县', '3', '37270', 'M');
INSERT INTO `ey_region` VALUES ('37345', '盐源县', '3', '37270', 'Y');
INSERT INTO `ey_region` VALUES ('37380', '德昌', '3', '37270', 'D');
INSERT INTO `ey_region` VALUES ('37404', '会理县', '3', '37270', 'H');
INSERT INTO `ey_region` VALUES ('37455', '会东县', '3', '37270', 'H');
INSERT INTO `ey_region` VALUES ('37509', '宁南县', '3', '37270', 'N');
INSERT INTO `ey_region` VALUES ('37535', '普格县', '3', '37270', 'P');
INSERT INTO `ey_region` VALUES ('37570', '布拖县', '3', '37270', 'B');
INSERT INTO `ey_region` VALUES ('37601', '金阳县', '3', '37270', 'J');
INSERT INTO `ey_region` VALUES ('37636', '昭觉县', '3', '37270', 'Z');
INSERT INTO `ey_region` VALUES ('37684', '喜德县', '3', '37270', 'X');
INSERT INTO `ey_region` VALUES ('37709', '冕宁县', '3', '37270', 'M');
INSERT INTO `ey_region` VALUES ('37748', '越西县', '3', '37270', 'Y');
INSERT INTO `ey_region` VALUES ('37790', '甘洛县', '3', '37270', 'G');
INSERT INTO `ey_region` VALUES ('37819', '美姑县', '3', '37270', 'M');
INSERT INTO `ey_region` VALUES ('37856', '雷波县', '3', '37270', 'L');
INSERT INTO `ey_region` VALUES ('37906', '贵州省', '1', '0', 'G');
INSERT INTO `ey_region` VALUES ('37907', '贵阳市', '2', '37906', 'G');
INSERT INTO `ey_region` VALUES ('37908', '市辖区', '3', '37907', 'S');
INSERT INTO `ey_region` VALUES ('37909', '南明区', '3', '37907', 'N');
INSERT INTO `ey_region` VALUES ('37927', '云岩区', '3', '37907', 'Y');
INSERT INTO `ey_region` VALUES ('37944', '花溪区', '3', '37907', 'H');
INSERT INTO `ey_region` VALUES ('37961', '乌当区', '3', '37907', 'W');
INSERT INTO `ey_region` VALUES ('37977', '白云区', '3', '37907', 'B');
INSERT INTO `ey_region` VALUES ('37987', '小河区', '3', '37907', 'X');
INSERT INTO `ey_region` VALUES ('37991', '开阳县', '3', '37907', 'K');
INSERT INTO `ey_region` VALUES ('38008', '息烽县', '3', '37907', 'X');
INSERT INTO `ey_region` VALUES ('38019', '修文县', '3', '37907', 'X');
INSERT INTO `ey_region` VALUES ('38030', '清镇市', '3', '37907', 'Q');
INSERT INTO `ey_region` VALUES ('38042', '六盘水市', '2', '37906', 'L');
INSERT INTO `ey_region` VALUES ('38043', '钟山区', '3', '38042', 'Z');
INSERT INTO `ey_region` VALUES ('38053', '六枝特区', '3', '38042', 'L');
INSERT INTO `ey_region` VALUES ('38073', '水城县', '3', '38042', 'S');
INSERT INTO `ey_region` VALUES ('38107', '盘县', '3', '38042', 'P');
INSERT INTO `ey_region` VALUES ('38145', '遵义市', '2', '37906', 'Z');
INSERT INTO `ey_region` VALUES ('38146', '市辖区', '3', '38145', 'S');
INSERT INTO `ey_region` VALUES ('38147', '红花岗区', '3', '38145', 'H');
INSERT INTO `ey_region` VALUES ('38164', '汇川区', '3', '38145', 'H');
INSERT INTO `ey_region` VALUES ('38174', '遵义县', '3', '38145', 'Z');
INSERT INTO `ey_region` VALUES ('38206', '桐梓县', '3', '38145', 'T');
INSERT INTO `ey_region` VALUES ('38231', '绥阳县', '3', '38145', 'S');
INSERT INTO `ey_region` VALUES ('38247', '正安县', '3', '38145', 'Z');
INSERT INTO `ey_region` VALUES ('38267', '道真县', '3', '38145', 'D');
INSERT INTO `ey_region` VALUES ('38282', '务川县', '3', '38145', 'W');
INSERT INTO `ey_region` VALUES ('38298', '凤冈县', '3', '38145', 'F');
INSERT INTO `ey_region` VALUES ('38313', '湄潭县', '3', '38145', 'M');
INSERT INTO `ey_region` VALUES ('38329', '余庆县', '3', '38145', 'Y');
INSERT INTO `ey_region` VALUES ('38340', '习水县', '3', '38145', 'X');
INSERT INTO `ey_region` VALUES ('38364', '赤水市', '3', '38145', 'C');
INSERT INTO `ey_region` VALUES ('38382', '仁怀市', '3', '38145', 'R');
INSERT INTO `ey_region` VALUES ('38402', '安顺市', '2', '37906', 'A');
INSERT INTO `ey_region` VALUES ('38403', '市辖区', '3', '38402', 'S');
INSERT INTO `ey_region` VALUES ('38404', '西秀区', '3', '38402', 'X');
INSERT INTO `ey_region` VALUES ('38429', '平坝县', '3', '38402', 'P');
INSERT INTO `ey_region` VALUES ('38440', '普定县', '3', '38402', 'P');
INSERT INTO `ey_region` VALUES ('38452', '镇宁县', '3', '38402', 'Z');
INSERT INTO `ey_region` VALUES ('38469', '关岭县', '3', '38402', 'G');
INSERT INTO `ey_region` VALUES ('38484', '紫云县', '3', '38402', 'Z');
INSERT INTO `ey_region` VALUES ('38497', '铜仁地区', '2', '37906', 'T');
INSERT INTO `ey_region` VALUES ('38498', '铜仁市', '3', '38497', 'T');
INSERT INTO `ey_region` VALUES ('38516', '江口县', '3', '38497', 'J');
INSERT INTO `ey_region` VALUES ('38526', '玉屏县', '3', '38497', 'Y');
INSERT INTO `ey_region` VALUES ('38533', '石阡县', '3', '38497', 'S');
INSERT INTO `ey_region` VALUES ('38552', '思南县　', '3', '38497', 'S');
INSERT INTO `ey_region` VALUES ('38580', '印江县', '3', '38497', 'Y');
INSERT INTO `ey_region` VALUES ('38598', '德江县', '3', '38497', 'D');
INSERT INTO `ey_region` VALUES ('38619', '沿河县', '3', '38497', 'Y');
INSERT INTO `ey_region` VALUES ('38642', '松桃县', '3', '38497', 'S');
INSERT INTO `ey_region` VALUES ('38671', '万山特区', '3', '38497', 'W');
INSERT INTO `ey_region` VALUES ('38677', '黔西南州', '2', '37906', 'Q');
INSERT INTO `ey_region` VALUES ('38678', '兴义市', '3', '38677', 'X');
INSERT INTO `ey_region` VALUES ('38705', '兴仁县', '3', '38677', 'X');
INSERT INTO `ey_region` VALUES ('38722', '普安县', '3', '38677', 'P');
INSERT INTO `ey_region` VALUES ('38737', '晴隆县', '3', '38677', 'Q');
INSERT INTO `ey_region` VALUES ('38752', '贞丰县', '3', '38677', 'Z');
INSERT INTO `ey_region` VALUES ('38766', '望谟县', '3', '38677', 'W');
INSERT INTO `ey_region` VALUES ('38784', '册亨县', '3', '38677', 'C');
INSERT INTO `ey_region` VALUES ('38799', '安龙县', '3', '38677', 'A');
INSERT INTO `ey_region` VALUES ('38816', '毕节地区', '2', '37906', 'B');
INSERT INTO `ey_region` VALUES ('38817', '毕节市', '3', '38816', 'B');
INSERT INTO `ey_region` VALUES ('38859', '大方县', '3', '38816', 'D');
INSERT INTO `ey_region` VALUES ('38896', '黔西县', '3', '38816', 'Q');
INSERT INTO `ey_region` VALUES ('38925', '金沙县', '3', '38816', 'J');
INSERT INTO `ey_region` VALUES ('38952', '织金县', '3', '38816', 'Z');
INSERT INTO `ey_region` VALUES ('38985', '纳雍县', '3', '38816', 'N');
INSERT INTO `ey_region` VALUES ('39011', '威宁县', '3', '38816', 'W');
INSERT INTO `ey_region` VALUES ('39047', '赫章县', '3', '38816', 'H');
INSERT INTO `ey_region` VALUES ('39075', '黔东南州', '2', '37906', 'Q');
INSERT INTO `ey_region` VALUES ('39076', '凯里市', '3', '39075', 'K');
INSERT INTO `ey_region` VALUES ('39092', '黄平县', '3', '39075', 'H');
INSERT INTO `ey_region` VALUES ('39107', '施秉县', '3', '39075', 'S');
INSERT INTO `ey_region` VALUES ('39116', '三穗县', '3', '39075', 'S');
INSERT INTO `ey_region` VALUES ('39126', '镇远县', '3', '39075', 'Z');
INSERT INTO `ey_region` VALUES ('39139', '岑巩县', '3', '39075', 'C');
INSERT INTO `ey_region` VALUES ('39151', '天柱县', '3', '39075', 'T');
INSERT INTO `ey_region` VALUES ('39168', '锦屏县', '3', '39075', 'J');
INSERT INTO `ey_region` VALUES ('39184', '剑河县', '3', '39075', 'J');
INSERT INTO `ey_region` VALUES ('39197', '台江县', '3', '39075', 'T');
INSERT INTO `ey_region` VALUES ('39206', '黎平县', '3', '39075', 'L');
INSERT INTO `ey_region` VALUES ('39232', '榕江县', '3', '39075', 'R');
INSERT INTO `ey_region` VALUES ('39252', '从江县', '3', '39075', 'C');
INSERT INTO `ey_region` VALUES ('39274', '雷山县', '3', '39075', 'L');
INSERT INTO `ey_region` VALUES ('39284', '麻江县', '3', '39075', 'M');
INSERT INTO `ey_region` VALUES ('39294', '丹寨县', '3', '39075', 'D');
INSERT INTO `ey_region` VALUES ('39302', '黔南州', '2', '37906', 'Q');
INSERT INTO `ey_region` VALUES ('39303', '都匀市', '3', '39302', 'D');
INSERT INTO `ey_region` VALUES ('39327', '福泉市', '3', '39302', 'F');
INSERT INTO `ey_region` VALUES ('39345', '荔波县', '3', '39302', 'L');
INSERT INTO `ey_region` VALUES ('39363', '贵定县', '3', '39302', 'G');
INSERT INTO `ey_region` VALUES ('39384', '瓮安县', '3', '39302', 'W');
INSERT INTO `ey_region` VALUES ('39408', '独山县', '3', '39302', 'D');
INSERT INTO `ey_region` VALUES ('39427', '平塘县', '3', '39302', 'P');
INSERT INTO `ey_region` VALUES ('39447', '罗甸县', '3', '39302', 'L');
INSERT INTO `ey_region` VALUES ('39474', '长顺县', '3', '39302', 'C');
INSERT INTO `ey_region` VALUES ('39493', '龙里县', '3', '39302', 'L');
INSERT INTO `ey_region` VALUES ('39508', '惠水县', '3', '39302', 'H');
INSERT INTO `ey_region` VALUES ('39534', '三都县', '3', '39302', 'S');
INSERT INTO `ey_region` VALUES ('39556', '云南省', '1', '0', 'Y');
INSERT INTO `ey_region` VALUES ('39557', '昆明市', '2', '39556', 'K');
INSERT INTO `ey_region` VALUES ('39558', '市辖区', '3', '39557', 'S');
INSERT INTO `ey_region` VALUES ('39559', '五华区', '3', '39557', 'W');
INSERT INTO `ey_region` VALUES ('39571', '盘龙区', '3', '39557', 'P');
INSERT INTO `ey_region` VALUES ('39582', '官渡区', '3', '39557', 'G');
INSERT INTO `ey_region` VALUES ('39594', '西山区', '3', '39557', 'X');
INSERT INTO `ey_region` VALUES ('39605', '东川区', '3', '39557', 'D');
INSERT INTO `ey_region` VALUES ('39614', '呈贡县', '3', '39557', 'C');
INSERT INTO `ey_region` VALUES ('39622', '晋宁县', '3', '39557', 'J');
INSERT INTO `ey_region` VALUES ('39632', '富民县', '3', '39557', 'F');
INSERT INTO `ey_region` VALUES ('39640', '宜良县', '3', '39557', 'Y');
INSERT INTO `ey_region` VALUES ('39650', '石林县', '3', '39557', 'S');
INSERT INTO `ey_region` VALUES ('39659', '嵩明县', '3', '39557', 'S');
INSERT INTO `ey_region` VALUES ('39667', '禄劝县', '3', '39557', 'L');
INSERT INTO `ey_region` VALUES ('39684', '寻甸县', '3', '39557', 'X');
INSERT INTO `ey_region` VALUES ('39701', '安宁市', '3', '39557', 'A');
INSERT INTO `ey_region` VALUES ('39710', '曲靖市', '2', '39556', 'Q');
INSERT INTO `ey_region` VALUES ('39711', '市辖区', '3', '39710', 'S');
INSERT INTO `ey_region` VALUES ('39712', '麒麟区', '3', '39710', 'Q');
INSERT INTO `ey_region` VALUES ('39724', '马龙县', '3', '39710', 'M');
INSERT INTO `ey_region` VALUES ('39733', '陆良县', '3', '39710', 'L');
INSERT INTO `ey_region` VALUES ('39744', '师宗县', '3', '39710', 'S');
INSERT INTO `ey_region` VALUES ('39753', '罗平县', '3', '39710', 'L');
INSERT INTO `ey_region` VALUES ('39766', '富源县', '3', '39710', 'F');
INSERT INTO `ey_region` VALUES ('39778', '会泽县', '3', '39710', 'H');
INSERT INTO `ey_region` VALUES ('39800', '沾益县', '3', '39710', 'Z');
INSERT INTO `ey_region` VALUES ('39809', '宣威市', '3', '39710', 'X');
INSERT INTO `ey_region` VALUES ('39836', '玉溪市', '2', '39556', 'Y');
INSERT INTO `ey_region` VALUES ('39837', '市辖区', '3', '39836', 'S');
INSERT INTO `ey_region` VALUES ('39838', '红塔区', '3', '39836', 'H');
INSERT INTO `ey_region` VALUES ('39850', '江川县', '3', '39836', 'J');
INSERT INTO `ey_region` VALUES ('39858', '澄江县', '3', '39836', 'C');
INSERT INTO `ey_region` VALUES ('39865', '通海县', '3', '39836', 'T');
INSERT INTO `ey_region` VALUES ('39875', '华宁县', '3', '39836', 'H');
INSERT INTO `ey_region` VALUES ('39881', '易门县', '3', '39836', 'Y');
INSERT INTO `ey_region` VALUES ('39889', '峨山县', '3', '39836', 'E');
INSERT INTO `ey_region` VALUES ('39899', '新平县', '3', '39836', 'X');
INSERT INTO `ey_region` VALUES ('39912', '元江县', '3', '39836', 'Y');
INSERT INTO `ey_region` VALUES ('39923', '保山市', '2', '39556', 'B');
INSERT INTO `ey_region` VALUES ('39924', '市辖区', '3', '39923', 'S');
INSERT INTO `ey_region` VALUES ('39925', '隆阳区', '3', '39923', 'L');
INSERT INTO `ey_region` VALUES ('39946', '施甸县', '3', '39923', 'S');
INSERT INTO `ey_region` VALUES ('39960', '腾冲县', '3', '39923', 'T');
INSERT INTO `ey_region` VALUES ('39979', '龙陵县', '3', '39923', 'L');
INSERT INTO `ey_region` VALUES ('39990', '昌宁县', '3', '39923', 'C');
INSERT INTO `ey_region` VALUES ('40004', '昭通市', '2', '39556', 'Z');
INSERT INTO `ey_region` VALUES ('40005', '市辖区', '3', '40004', 'S');
INSERT INTO `ey_region` VALUES ('40006', '昭阳区', '3', '40004', 'Z');
INSERT INTO `ey_region` VALUES ('40027', '鲁甸县', '3', '40004', 'L');
INSERT INTO `ey_region` VALUES ('40040', '巧家县', '3', '40004', 'Q');
INSERT INTO `ey_region` VALUES ('40057', '盐津县', '3', '40004', 'Y');
INSERT INTO `ey_region` VALUES ('40068', '大关县', '3', '40004', 'D');
INSERT INTO `ey_region` VALUES ('40078', '永善县', '3', '40004', 'Y');
INSERT INTO `ey_region` VALUES ('40094', '绥江县', '3', '40004', 'S');
INSERT INTO `ey_region` VALUES ('40100', '镇雄县', '3', '40004', 'Z');
INSERT INTO `ey_region` VALUES ('40129', '彝良县', '3', '40004', 'Y');
INSERT INTO `ey_region` VALUES ('40145', '威信县', '3', '40004', 'W');
INSERT INTO `ey_region` VALUES ('40156', '水富县', '3', '40004', 'S');
INSERT INTO `ey_region` VALUES ('40160', '丽江市', '2', '39556', 'L');
INSERT INTO `ey_region` VALUES ('40161', '市辖区', '3', '40160', 'S');
INSERT INTO `ey_region` VALUES ('40162', '古城区', '3', '40160', 'G');
INSERT INTO `ey_region` VALUES ('40172', '玉龙县', '3', '40160', 'Y');
INSERT INTO `ey_region` VALUES ('40189', '永胜县', '3', '40160', 'Y');
INSERT INTO `ey_region` VALUES ('40205', '华坪县', '3', '40160', 'H');
INSERT INTO `ey_region` VALUES ('40214', '宁蒗县', '3', '40160', 'N');
INSERT INTO `ey_region` VALUES ('40230', '思茅市', '2', '39556', 'S');
INSERT INTO `ey_region` VALUES ('40231', '市辖区', '3', '40230', 'S');
INSERT INTO `ey_region` VALUES ('40232', '翠云区', '3', '40230', 'C');
INSERT INTO `ey_region` VALUES ('40240', '普洱县', '3', '40230', 'P');
INSERT INTO `ey_region` VALUES ('40250', '墨江县', '3', '40230', 'M');
INSERT INTO `ey_region` VALUES ('40266', '景东县', '3', '40230', 'J');
INSERT INTO `ey_region` VALUES ('40280', '景谷县', '3', '40230', 'J');
INSERT INTO `ey_region` VALUES ('40291', '镇沅县', '3', '40230', 'Z');
INSERT INTO `ey_region` VALUES ('40301', '江城县', '3', '40230', 'J');
INSERT INTO `ey_region` VALUES ('40310', '孟连县', '3', '40230', 'M');
INSERT INTO `ey_region` VALUES ('40318', '澜沧县', '3', '40230', 'L');
INSERT INTO `ey_region` VALUES ('40340', '西盟县', '3', '40230', 'X');
INSERT INTO `ey_region` VALUES ('40348', '临沧市', '2', '39556', 'L');
INSERT INTO `ey_region` VALUES ('40349', '市辖区', '3', '40348', 'S');
INSERT INTO `ey_region` VALUES ('40350', '临翔区', '3', '40348', 'L');
INSERT INTO `ey_region` VALUES ('40361', '凤庆县', '3', '40348', 'F');
INSERT INTO `ey_region` VALUES ('40375', '云县', '3', '40348', 'Y');
INSERT INTO `ey_region` VALUES ('40388', '永德县', '3', '40348', 'Y');
INSERT INTO `ey_region` VALUES ('40400', '镇康县', '3', '40348', 'Z');
INSERT INTO `ey_region` VALUES ('40408', '双江县', '3', '40348', 'S');
INSERT INTO `ey_region` VALUES ('40417', '耿马县', '3', '40348', 'G');
INSERT INTO `ey_region` VALUES ('40429', '沧源县', '3', '40348', 'C');
INSERT INTO `ey_region` VALUES ('40441', '楚雄州', '2', '39556', 'C');
INSERT INTO `ey_region` VALUES ('40442', '楚雄市', '3', '40441', 'C');
INSERT INTO `ey_region` VALUES ('40458', '双柏县', '3', '40441', 'S');
INSERT INTO `ey_region` VALUES ('40467', '牟定县', '3', '40441', 'M');
INSERT INTO `ey_region` VALUES ('40475', '南华县', '3', '40441', 'N');
INSERT INTO `ey_region` VALUES ('40486', '姚安县', '3', '40441', 'Y');
INSERT INTO `ey_region` VALUES ('40496', '大姚县', '3', '40441', 'D');
INSERT INTO `ey_region` VALUES ('40509', '永仁县', '3', '40441', 'Y');
INSERT INTO `ey_region` VALUES ('40517', '元谋县', '3', '40441', 'Y');
INSERT INTO `ey_region` VALUES ('40528', '武定县', '3', '40441', 'W');
INSERT INTO `ey_region` VALUES ('40540', '禄丰县', '3', '40441', 'L');
INSERT INTO `ey_region` VALUES ('40555', '红河州', '2', '39556', 'H');
INSERT INTO `ey_region` VALUES ('40556', '个旧市', '3', '40555', 'G');
INSERT INTO `ey_region` VALUES ('40567', '开远市', '3', '40555', 'K');
INSERT INTO `ey_region` VALUES ('40576', '蒙自县', '3', '40555', 'M');
INSERT INTO `ey_region` VALUES ('40588', '屏边县', '3', '40555', 'P');
INSERT INTO `ey_region` VALUES ('40596', '建水县', '3', '40555', 'J');
INSERT INTO `ey_region` VALUES ('40611', '石屏县', '3', '40555', 'S');
INSERT INTO `ey_region` VALUES ('40621', '弥勒县', '3', '40555', 'M');
INSERT INTO `ey_region` VALUES ('40635', '泸西县', '3', '40555', 'L');
INSERT INTO `ey_region` VALUES ('40644', '元阳县', '3', '40555', 'Y');
INSERT INTO `ey_region` VALUES ('40659', '红河县', '3', '40555', 'H');
INSERT INTO `ey_region` VALUES ('40673', '金平县', '3', '40555', 'J');
INSERT INTO `ey_region` VALUES ('40688', '绿春县', '3', '40555', 'L');
INSERT INTO `ey_region` VALUES ('40698', '河口县', '3', '40555', 'H');
INSERT INTO `ey_region` VALUES ('40705', '文山州', '2', '39556', 'W');
INSERT INTO `ey_region` VALUES ('40706', '文山县', '3', '40705', 'W');
INSERT INTO `ey_region` VALUES ('40722', '砚山县', '3', '40705', 'Y');
INSERT INTO `ey_region` VALUES ('40734', '西畴县', '3', '40705', 'X');
INSERT INTO `ey_region` VALUES ('40744', '麻栗坡县', '3', '40705', 'M');
INSERT INTO `ey_region` VALUES ('40756', '马关县', '3', '40705', 'M');
INSERT INTO `ey_region` VALUES ('40770', '丘北县', '3', '40705', 'Q');
INSERT INTO `ey_region` VALUES ('40783', '广南县', '3', '40705', 'G');
INSERT INTO `ey_region` VALUES ('40802', '富宁县', '3', '40705', 'F');
INSERT INTO `ey_region` VALUES ('40816', '西双版纳州', '2', '39556', 'X');
INSERT INTO `ey_region` VALUES ('40817', '景洪市', '3', '40816', 'J');
INSERT INTO `ey_region` VALUES ('40829', '勐海县', '3', '40816', 'M');
INSERT INTO `ey_region` VALUES ('40841', '勐腊县', '3', '40816', 'M');
INSERT INTO `ey_region` VALUES ('40852', '大理州', '2', '39556', 'D');
INSERT INTO `ey_region` VALUES ('40853', '大理市', '3', '40852', 'D');
INSERT INTO `ey_region` VALUES ('40866', '漾濞县', '3', '40852', 'Y');
INSERT INTO `ey_region` VALUES ('40876', '祥云县', '3', '40852', 'X');
INSERT INTO `ey_region` VALUES ('40887', '宾川县', '3', '40852', 'B');
INSERT INTO `ey_region` VALUES ('40901', '弥渡县', '3', '40852', 'M');
INSERT INTO `ey_region` VALUES ('40910', '南涧县', '3', '40852', 'N');
INSERT INTO `ey_region` VALUES ('40919', '巍山县', '3', '40852', 'W');
INSERT INTO `ey_region` VALUES ('40930', '永平县', '3', '40852', 'Y');
INSERT INTO `ey_region` VALUES ('40938', '云龙县', '3', '40852', 'Y');
INSERT INTO `ey_region` VALUES ('40950', '洱源县', '3', '40852', 'E');
INSERT INTO `ey_region` VALUES ('40960', '剑川县', '3', '40852', 'J');
INSERT INTO `ey_region` VALUES ('40969', '鹤庆县', '3', '40852', 'H');
INSERT INTO `ey_region` VALUES ('40979', '德宏州', '2', '39556', 'D');
INSERT INTO `ey_region` VALUES ('40980', '瑞丽市', '3', '40979', 'R');
INSERT INTO `ey_region` VALUES ('40988', '潞西市', '3', '40979', 'L');
INSERT INTO `ey_region` VALUES ('41000', '梁河县', '3', '40979', 'L');
INSERT INTO `ey_region` VALUES ('41010', '盈江县', '3', '40979', 'Y');
INSERT INTO `ey_region` VALUES ('41026', '陇川县', '3', '40979', 'L');
INSERT INTO `ey_region` VALUES ('41036', '怒江州', '2', '39556', 'N');
INSERT INTO `ey_region` VALUES ('41037', '泸水县', '3', '41036', 'L');
INSERT INTO `ey_region` VALUES ('41047', '福贡县', '3', '41036', 'F');
INSERT INTO `ey_region` VALUES ('41055', '贡山县', '3', '41036', 'G');
INSERT INTO `ey_region` VALUES ('41061', '兰坪县', '3', '41036', 'L');
INSERT INTO `ey_region` VALUES ('41070', '迪庆州', '2', '39556', 'D');
INSERT INTO `ey_region` VALUES ('41071', '香格里拉县', '3', '41070', 'X');
INSERT INTO `ey_region` VALUES ('41083', '德钦县', '3', '41070', 'D');
INSERT INTO `ey_region` VALUES ('41092', '维西县', '3', '41070', 'W');
INSERT INTO `ey_region` VALUES ('41103', '西藏', '1', '0', 'X');
INSERT INTO `ey_region` VALUES ('41104', '拉萨市', '2', '41103', 'L');
INSERT INTO `ey_region` VALUES ('41105', '市辖区', '3', '41104', 'S');
INSERT INTO `ey_region` VALUES ('41106', '城关区', '3', '41104', 'C');
INSERT INTO `ey_region` VALUES ('41118', '林周县', '3', '41104', 'L');
INSERT INTO `ey_region` VALUES ('41129', '当雄县', '3', '41104', 'D');
INSERT INTO `ey_region` VALUES ('41138', '尼木县', '3', '41104', 'N');
INSERT INTO `ey_region` VALUES ('41147', '曲水县', '3', '41104', 'Q');
INSERT INTO `ey_region` VALUES ('41154', '堆龙德庆', '3', '41104', 'D');
INSERT INTO `ey_region` VALUES ('41162', '达孜县', '3', '41104', 'D');
INSERT INTO `ey_region` VALUES ('41169', '墨竹工卡县', '3', '41104', 'M');
INSERT INTO `ey_region` VALUES ('41178', '昌都地区', '2', '41103', 'C');
INSERT INTO `ey_region` VALUES ('41179', '昌都县', '3', '41178', 'C');
INSERT INTO `ey_region` VALUES ('41195', '江达县', '3', '41178', 'J');
INSERT INTO `ey_region` VALUES ('41209', '贡觉县', '3', '41178', 'G');
INSERT INTO `ey_region` VALUES ('41222', '类乌齐县', '3', '41178', 'L');
INSERT INTO `ey_region` VALUES ('41233', '丁青县', '3', '41178', 'D');
INSERT INTO `ey_region` VALUES ('41247', '察亚县', '3', '41178', 'C');
INSERT INTO `ey_region` VALUES ('41261', '八宿县', '3', '41178', 'B');
INSERT INTO `ey_region` VALUES ('41276', '左贡县', '3', '41178', 'Z');
INSERT INTO `ey_region` VALUES ('41287', '芒康县', '3', '41178', 'M');
INSERT INTO `ey_region` VALUES ('41304', '洛隆县', '3', '41178', 'L');
INSERT INTO `ey_region` VALUES ('41316', '边坝县', '3', '41178', 'B');
INSERT INTO `ey_region` VALUES ('41328', '山南地区', '2', '41103', 'S');
INSERT INTO `ey_region` VALUES ('41329', '乃东县', '3', '41328', 'N');
INSERT INTO `ey_region` VALUES ('41337', '扎囊县', '3', '41328', 'Z');
INSERT INTO `ey_region` VALUES ('41343', '贡嘎县', '3', '41328', 'G');
INSERT INTO `ey_region` VALUES ('41352', '桑日县', '3', '41328', 'S');
INSERT INTO `ey_region` VALUES ('41357', '琼结县', '3', '41328', 'Q');
INSERT INTO `ey_region` VALUES ('41362', '曲松县', '3', '41328', 'Q');
INSERT INTO `ey_region` VALUES ('41368', '措美县', '3', '41328', 'C');
INSERT INTO `ey_region` VALUES ('41373', '洛扎县', '3', '41328', 'L');
INSERT INTO `ey_region` VALUES ('41381', '加查县', '3', '41328', 'J');
INSERT INTO `ey_region` VALUES ('41389', '隆子县', '3', '41328', 'L');
INSERT INTO `ey_region` VALUES ('41401', '错那县', '3', '41328', 'C');
INSERT INTO `ey_region` VALUES ('41412', '浪卡子县', '3', '41328', 'L');
INSERT INTO `ey_region` VALUES ('41423', '日喀则地区', '2', '41103', 'R');
INSERT INTO `ey_region` VALUES ('41424', '日喀则市', '3', '41423', 'R');
INSERT INTO `ey_region` VALUES ('41437', '南木林县', '3', '41423', 'N');
INSERT INTO `ey_region` VALUES ('41455', '江孜县', '3', '41423', 'J');
INSERT INTO `ey_region` VALUES ('41475', '定日县', '3', '41423', 'D');
INSERT INTO `ey_region` VALUES ('41489', '萨迦县', '3', '41423', 'S');
INSERT INTO `ey_region` VALUES ('41501', '拉孜县', '3', '41423', 'L');
INSERT INTO `ey_region` VALUES ('41513', '昂仁县', '3', '41423', 'A');
INSERT INTO `ey_region` VALUES ('41531', '谢通门县', '3', '41423', 'X');
INSERT INTO `ey_region` VALUES ('41551', '白朗县', '3', '41423', 'B');
INSERT INTO `ey_region` VALUES ('41563', '仁布县', '3', '41423', 'R');
INSERT INTO `ey_region` VALUES ('41573', '康马县', '3', '41423', 'K');
INSERT INTO `ey_region` VALUES ('41583', '定结县', '3', '41423', 'D');
INSERT INTO `ey_region` VALUES ('41594', '仲巴县', '3', '41423', 'Z');
INSERT INTO `ey_region` VALUES ('41608', '亚东县', '3', '41423', 'Y');
INSERT INTO `ey_region` VALUES ('41616', '吉隆县', '3', '41423', 'J');
INSERT INTO `ey_region` VALUES ('41622', '聂拉木县', '3', '41423', 'N');
INSERT INTO `ey_region` VALUES ('41630', '萨嘎县', '3', '41423', 'S');
INSERT INTO `ey_region` VALUES ('41639', '岗巴县', '3', '41423', 'G');
INSERT INTO `ey_region` VALUES ('41645', '那曲地区', '2', '41103', 'N');
INSERT INTO `ey_region` VALUES ('41646', '那曲县', '3', '41645', 'N');
INSERT INTO `ey_region` VALUES ('41659', '嘉黎县', '3', '41645', 'J');
INSERT INTO `ey_region` VALUES ('41670', '比如县', '3', '41645', 'B');
INSERT INTO `ey_region` VALUES ('41681', '聂荣县', '3', '41645', 'N');
INSERT INTO `ey_region` VALUES ('41692', '安多县', '3', '41645', 'A');
INSERT INTO `ey_region` VALUES ('41706', '申扎县', '3', '41645', 'S');
INSERT INTO `ey_region` VALUES ('41715', '索县', '3', '41645', 'S');
INSERT INTO `ey_region` VALUES ('41726', '班戈县', '3', '41645', 'B');
INSERT INTO `ey_region` VALUES ('41737', '巴青县', '3', '41645', 'B');
INSERT INTO `ey_region` VALUES ('41748', '尼玛县', '3', '41645', 'N');
INSERT INTO `ey_region` VALUES ('41770', '阿里地区', '2', '41103', 'A');
INSERT INTO `ey_region` VALUES ('41771', '普兰县', '3', '41770', 'P');
INSERT INTO `ey_region` VALUES ('41775', '札达县', '3', '41770', 'Z');
INSERT INTO `ey_region` VALUES ('41782', '噶尔县', '3', '41770', 'G');
INSERT INTO `ey_region` VALUES ('41788', '日土县', '3', '41770', 'R');
INSERT INTO `ey_region` VALUES ('41794', '革吉县', '3', '41770', 'G');
INSERT INTO `ey_region` VALUES ('41800', '改则县', '3', '41770', 'G');
INSERT INTO `ey_region` VALUES ('41808', '措勤县', '3', '41770', 'C');
INSERT INTO `ey_region` VALUES ('41814', '林芝地区', '2', '41103', 'L');
INSERT INTO `ey_region` VALUES ('41815', '林芝县', '3', '41814', 'L');
INSERT INTO `ey_region` VALUES ('41823', '工布江达县', '3', '41814', 'G');
INSERT INTO `ey_region` VALUES ('41833', '米林县', '3', '41814', 'M');
INSERT INTO `ey_region` VALUES ('41842', '墨脱县', '3', '41814', 'M');
INSERT INTO `ey_region` VALUES ('41851', '波密县', '3', '41814', 'B');
INSERT INTO `ey_region` VALUES ('41863', '察隅县', '3', '41814', 'C');
INSERT INTO `ey_region` VALUES ('41870', '朗县', '3', '41814', 'L');
INSERT INTO `ey_region` VALUES ('41877', '陕西省', '1', '0', 'S');
INSERT INTO `ey_region` VALUES ('41878', '西安市', '2', '41877', 'X');
INSERT INTO `ey_region` VALUES ('41879', '市辖区', '3', '41878', 'S');
INSERT INTO `ey_region` VALUES ('41880', '新城区', '3', '41878', 'X');
INSERT INTO `ey_region` VALUES ('41890', '碑林区', '3', '41878', 'B');
INSERT INTO `ey_region` VALUES ('41899', '莲湖区', '3', '41878', 'L');
INSERT INTO `ey_region` VALUES ('41909', '灞桥区', '3', '41878', 'B');
INSERT INTO `ey_region` VALUES ('41919', '未央区', '3', '41878', 'W');
INSERT INTO `ey_region` VALUES ('41930', '雁塔区', '3', '41878', 'Y');
INSERT INTO `ey_region` VALUES ('41939', '阎良区', '3', '41878', 'Y');
INSERT INTO `ey_region` VALUES ('41947', '临潼区', '3', '41878', 'L');
INSERT INTO `ey_region` VALUES ('41971', '长安区', '3', '41878', 'C');
INSERT INTO `ey_region` VALUES ('41997', '蓝田县', '3', '41878', 'L');
INSERT INTO `ey_region` VALUES ('42020', '周至县', '3', '41878', 'Z');
INSERT INTO `ey_region` VALUES ('42043', '户县', '3', '41878', 'H');
INSERT INTO `ey_region` VALUES ('42060', '高陵县', '3', '41878', 'G');
INSERT INTO `ey_region` VALUES ('42069', '铜川市', '2', '41877', 'T');
INSERT INTO `ey_region` VALUES ('42070', '市辖区', '3', '42069', 'S');
INSERT INTO `ey_region` VALUES ('42071', '王益区', '3', '42069', 'W');
INSERT INTO `ey_region` VALUES ('42079', '印台区', '3', '42069', 'Y');
INSERT INTO `ey_region` VALUES ('42091', '耀州区', '3', '42069', 'Y');
INSERT INTO `ey_region` VALUES ('42108', '宜君县', '3', '42069', 'Y');
INSERT INTO `ey_region` VALUES ('42119', '宝鸡市', '2', '41877', 'B');
INSERT INTO `ey_region` VALUES ('42120', '市辖区', '3', '42119', 'S');
INSERT INTO `ey_region` VALUES ('42121', '渭滨区', '3', '42119', 'W');
INSERT INTO `ey_region` VALUES ('42133', '金台区', '3', '42119', 'J');
INSERT INTO `ey_region` VALUES ('42146', '陈仓区', '3', '42119', 'C');
INSERT INTO `ey_region` VALUES ('42165', '凤翔县', '3', '42119', 'F');
INSERT INTO `ey_region` VALUES ('42183', '岐山县', '3', '42119', 'Q');
INSERT INTO `ey_region` VALUES ('42198', '扶风县', '3', '42119', 'F');
INSERT INTO `ey_region` VALUES ('42211', '眉县', '3', '42119', 'M');
INSERT INTO `ey_region` VALUES ('42224', '陇县', '3', '42119', 'L');
INSERT INTO `ey_region` VALUES ('42240', '千阳县', '3', '42119', 'Q');
INSERT INTO `ey_region` VALUES ('42252', '麟游县', '3', '42119', 'L');
INSERT INTO `ey_region` VALUES ('42263', '凤县', '3', '42119', 'F');
INSERT INTO `ey_region` VALUES ('42278', '太白县', '3', '42119', 'T');
INSERT INTO `ey_region` VALUES ('42287', '咸阳市', '2', '41877', 'X');
INSERT INTO `ey_region` VALUES ('42288', '市辖区', '3', '42287', 'S');
INSERT INTO `ey_region` VALUES ('42289', '秦都区', '3', '42287', 'Q');
INSERT INTO `ey_region` VALUES ('42302', '杨凌区', '3', '42287', 'Y');
INSERT INTO `ey_region` VALUES ('42308', '渭城区', '3', '42287', 'W');
INSERT INTO `ey_region` VALUES ('42319', '三原县', '3', '42287', 'S');
INSERT INTO `ey_region` VALUES ('42334', '泾阳县', '3', '42287', 'J');
INSERT INTO `ey_region` VALUES ('42351', '乾县', '3', '42287', 'Q');
INSERT INTO `ey_region` VALUES ('42372', '礼泉县', '3', '42287', 'L');
INSERT INTO `ey_region` VALUES ('42388', '永寿县', '3', '42287', 'Y');
INSERT INTO `ey_region` VALUES ('42402', '彬县', '3', '42287', 'B');
INSERT INTO `ey_region` VALUES ('42419', '长武县', '3', '42287', 'C');
INSERT INTO `ey_region` VALUES ('42431', '旬邑县', '3', '42287', 'X');
INSERT INTO `ey_region` VALUES ('42446', '淳化县', '3', '42287', 'C');
INSERT INTO `ey_region` VALUES ('42462', '武功县', '3', '42287', 'W');
INSERT INTO `ey_region` VALUES ('42475', '兴平市', '3', '42287', 'X');
INSERT INTO `ey_region` VALUES ('42490', '渭南市', '2', '41877', 'W');
INSERT INTO `ey_region` VALUES ('42491', '市辖区', '3', '42490', 'S');
INSERT INTO `ey_region` VALUES ('42492', '临渭区', '3', '42490', 'L');
INSERT INTO `ey_region` VALUES ('42523', '华县', '3', '42490', 'H');
INSERT INTO `ey_region` VALUES ('42538', '潼关县', '3', '42490', 'T');
INSERT INTO `ey_region` VALUES ('42547', '大荔县', '3', '42490', 'D');
INSERT INTO `ey_region` VALUES ('42577', '合阳县', '3', '42490', 'H');
INSERT INTO `ey_region` VALUES ('42594', '澄城县', '3', '42490', 'C');
INSERT INTO `ey_region` VALUES ('42609', '蒲城县', '3', '42490', 'P');
INSERT INTO `ey_region` VALUES ('42634', '白水县', '3', '42490', 'B');
INSERT INTO `ey_region` VALUES ('42649', '富平县', '3', '42490', 'F');
INSERT INTO `ey_region` VALUES ('42674', '韩城市', '3', '42490', 'H');
INSERT INTO `ey_region` VALUES ('42691', '华阴市', '3', '42490', 'H');
INSERT INTO `ey_region` VALUES ('42703', '延安市', '2', '41877', 'Y');
INSERT INTO `ey_region` VALUES ('42704', '市辖区', '3', '42703', 'S');
INSERT INTO `ey_region` VALUES ('42705', '宝塔区', '3', '42703', 'B');
INSERT INTO `ey_region` VALUES ('42729', '延长县', '3', '42703', 'Y');
INSERT INTO `ey_region` VALUES ('42742', '延川县', '3', '42703', 'Y');
INSERT INTO `ey_region` VALUES ('42757', '子长县', '3', '42703', 'Z');
INSERT INTO `ey_region` VALUES ('42771', '安塞县', '3', '42703', 'A');
INSERT INTO `ey_region` VALUES ('42784', '志丹县', '3', '42703', 'Z');
INSERT INTO `ey_region` VALUES ('42796', '吴起县', '3', '42703', 'W');
INSERT INTO `ey_region` VALUES ('42809', '甘泉县', '3', '42703', 'G');
INSERT INTO `ey_region` VALUES ('42818', '富县', '3', '42703', 'F');
INSERT INTO `ey_region` VALUES ('42833', '洛川县', '3', '42703', 'L');
INSERT INTO `ey_region` VALUES ('42850', '宜川县', '3', '42703', 'Y');
INSERT INTO `ey_region` VALUES ('42863', '黄龙县', '3', '42703', 'H');
INSERT INTO `ey_region` VALUES ('42874', '黄陵县', '3', '42703', 'H');
INSERT INTO `ey_region` VALUES ('42888', '汉中市', '2', '41877', 'H');
INSERT INTO `ey_region` VALUES ('42889', '市辖区', '3', '42888', 'S');
INSERT INTO `ey_region` VALUES ('42890', '汉台区', '3', '42888', 'H');
INSERT INTO `ey_region` VALUES ('42908', '南郑县', '3', '42888', 'N');
INSERT INTO `ey_region` VALUES ('42939', '城固县', '3', '42888', 'C');
INSERT INTO `ey_region` VALUES ('42965', '洋县', '3', '42888', 'Y');
INSERT INTO `ey_region` VALUES ('42992', '西乡县', '3', '42888', 'X');
INSERT INTO `ey_region` VALUES ('43016', '勉县', '3', '42888', 'M');
INSERT INTO `ey_region` VALUES ('43042', '宁强县', '3', '42888', 'N');
INSERT INTO `ey_region` VALUES ('43069', '略阳县', '3', '42888', 'L');
INSERT INTO `ey_region` VALUES ('43091', '镇巴县', '3', '42888', 'Z');
INSERT INTO `ey_region` VALUES ('43116', '留坝县', '3', '42888', 'L');
INSERT INTO `ey_region` VALUES ('43126', '佛坪县', '3', '42888', 'F');
INSERT INTO `ey_region` VALUES ('43136', '榆林市', '2', '41877', 'Y');
INSERT INTO `ey_region` VALUES ('43137', '市辖区', '3', '43136', 'S');
INSERT INTO `ey_region` VALUES ('43138', '榆阳区', '3', '43136', 'Y');
INSERT INTO `ey_region` VALUES ('43170', '神木县', '3', '43136', 'S');
INSERT INTO `ey_region` VALUES ('43190', '府谷县', '3', '43136', 'F');
INSERT INTO `ey_region` VALUES ('43211', '横山县', '3', '43136', 'H');
INSERT INTO `ey_region` VALUES ('43230', '靖边县', '3', '43136', 'J');
INSERT INTO `ey_region` VALUES ('43253', '定边县', '3', '43136', 'D');
INSERT INTO `ey_region` VALUES ('43279', '绥德县', '3', '43136', 'S');
INSERT INTO `ey_region` VALUES ('43300', '米脂县', '3', '43136', 'M');
INSERT INTO `ey_region` VALUES ('43314', '佳县', '3', '43136', 'J');
INSERT INTO `ey_region` VALUES ('43335', '吴堡县', '3', '43136', 'W');
INSERT INTO `ey_region` VALUES ('43344', '清涧县', '3', '43136', 'Q');
INSERT INTO `ey_region` VALUES ('43360', '子洲县', '3', '43136', 'Z');
INSERT INTO `ey_region` VALUES ('43379', '安康市', '2', '41877', 'A');
INSERT INTO `ey_region` VALUES ('43380', '市辖区', '3', '43379', 'S');
INSERT INTO `ey_region` VALUES ('43381', '汉滨区', '3', '43379', 'H');
INSERT INTO `ey_region` VALUES ('43428', '汉阴县', '3', '43379', 'H');
INSERT INTO `ey_region` VALUES ('43447', '石泉县', '3', '43379', 'S');
INSERT INTO `ey_region` VALUES ('43463', '宁陕县', '3', '43379', 'N');
INSERT INTO `ey_region` VALUES ('43478', '紫阳县', '3', '43379', 'Z');
INSERT INTO `ey_region` VALUES ('43504', '岚皋县', '3', '43379', 'L');
INSERT INTO `ey_region` VALUES ('43522', '平利县', '3', '43379', 'P');
INSERT INTO `ey_region` VALUES ('43535', '镇坪县', '3', '43379', 'Z');
INSERT INTO `ey_region` VALUES ('43546', '旬阳县', '3', '43379', 'X');
INSERT INTO `ey_region` VALUES ('43575', '白河县', '3', '43379', 'B');
INSERT INTO `ey_region` VALUES ('43592', '商洛市', '2', '41877', 'S');
INSERT INTO `ey_region` VALUES ('43593', '市辖区', '3', '43592', 'S');
INSERT INTO `ey_region` VALUES ('43594', '商州区', '3', '43592', 'S');
INSERT INTO `ey_region` VALUES ('43628', '洛南县', '3', '43592', 'L');
INSERT INTO `ey_region` VALUES ('43654', '丹凤县', '3', '43592', 'D');
INSERT INTO `ey_region` VALUES ('43676', '商南县', '3', '43592', 'S');
INSERT INTO `ey_region` VALUES ('43699', '山阳县', '3', '43592', 'S');
INSERT INTO `ey_region` VALUES ('43730', '镇安县', '3', '43592', 'Z');
INSERT INTO `ey_region` VALUES ('43759', '柞水县', '3', '43592', 'Z');
INSERT INTO `ey_region` VALUES ('43776', '甘肃省', '1', '0', 'G');
INSERT INTO `ey_region` VALUES ('43777', '兰州市', '2', '43776', 'L');
INSERT INTO `ey_region` VALUES ('43778', '市辖区', '3', '43777', 'S');
INSERT INTO `ey_region` VALUES ('43779', '城关区', '3', '43777', 'C');
INSERT INTO `ey_region` VALUES ('43804', '七里河区', '3', '43777', 'Q');
INSERT INTO `ey_region` VALUES ('43820', '兰州市西固区', '3', '43777', 'L');
INSERT INTO `ey_region` VALUES ('43836', '安宁区', '3', '43777', 'A');
INSERT INTO `ey_region` VALUES ('43845', '红古区', '3', '43777', 'H');
INSERT INTO `ey_region` VALUES ('43853', '永登县', '3', '43777', 'Y');
INSERT INTO `ey_region` VALUES ('43872', '皋兰县', '3', '43777', 'G');
INSERT INTO `ey_region` VALUES ('43880', '榆中县', '3', '43777', 'Y');
INSERT INTO `ey_region` VALUES ('43904', '嘉峪关市', '2', '43776', 'J');
INSERT INTO `ey_region` VALUES ('43905', '市辖', '3', '43904', 'S');
INSERT INTO `ey_region` VALUES ('43914', '金昌市', '2', '43776', 'J');
INSERT INTO `ey_region` VALUES ('43915', '市辖区', '3', '43914', 'S');
INSERT INTO `ey_region` VALUES ('43916', '金川区', '3', '43914', 'J');
INSERT INTO `ey_region` VALUES ('43925', '永昌县', '3', '43914', 'Y');
INSERT INTO `ey_region` VALUES ('43936', '白银市', '2', '43776', 'B');
INSERT INTO `ey_region` VALUES ('43937', '市辖区', '3', '43936', 'S');
INSERT INTO `ey_region` VALUES ('43938', '白银区', '3', '43936', 'B');
INSERT INTO `ey_region` VALUES ('43949', '平川区', '3', '43936', 'P');
INSERT INTO `ey_region` VALUES ('43961', '靖远县', '3', '43936', 'J');
INSERT INTO `ey_region` VALUES ('43980', '会宁县', '3', '43936', 'H');
INSERT INTO `ey_region` VALUES ('44009', '景泰县', '3', '43936', 'J');
INSERT INTO `ey_region` VALUES ('44022', '天水市', '2', '43776', 'T');
INSERT INTO `ey_region` VALUES ('44023', '市辖区', '3', '44022', 'S');
INSERT INTO `ey_region` VALUES ('44024', '秦州区', '3', '44022', 'Q');
INSERT INTO `ey_region` VALUES ('44048', '麦积区', '3', '44022', 'M');
INSERT INTO `ey_region` VALUES ('44069', '清水县', '3', '44022', 'Q');
INSERT INTO `ey_region` VALUES ('44088', '秦安县', '3', '44022', 'Q');
INSERT INTO `ey_region` VALUES ('44106', '甘谷县', '3', '44022', 'G');
INSERT INTO `ey_region` VALUES ('44122', '武山县', '3', '44022', 'W');
INSERT INTO `ey_region` VALUES ('44138', '张家川县', '3', '44022', 'Z');
INSERT INTO `ey_region` VALUES ('44154', '武威市', '2', '43776', 'W');
INSERT INTO `ey_region` VALUES ('44155', '市辖区', '3', '44154', 'S');
INSERT INTO `ey_region` VALUES ('44156', '凉州区', '3', '44154', 'L');
INSERT INTO `ey_region` VALUES ('44202', '民勤县', '3', '44154', 'M');
INSERT INTO `ey_region` VALUES ('44221', '古浪县', '3', '44154', 'G');
INSERT INTO `ey_region` VALUES ('44242', '天祝县', '3', '44154', 'T');
INSERT INTO `ey_region` VALUES ('44265', '张掖市', '2', '43776', 'Z');
INSERT INTO `ey_region` VALUES ('44266', '市辖区', '3', '44265', 'S');
INSERT INTO `ey_region` VALUES ('44267', '甘州区', '3', '44265', 'G');
INSERT INTO `ey_region` VALUES ('44294', '肃南县', '3', '44265', 'S');
INSERT INTO `ey_region` VALUES ('44305', '民乐县', '3', '44265', 'M');
INSERT INTO `ey_region` VALUES ('44317', '临泽县', '3', '44265', 'L');
INSERT INTO `ey_region` VALUES ('44331', '高台县', '3', '44265', 'G');
INSERT INTO `ey_region` VALUES ('44341', '山丹县', '3', '44265', 'S');
INSERT INTO `ey_region` VALUES ('44352', '平凉市', '2', '43776', 'P');
INSERT INTO `ey_region` VALUES ('44353', '市辖区', '3', '44352', 'S');
INSERT INTO `ey_region` VALUES ('44354', '崆峒区', '3', '44352', 'K');
INSERT INTO `ey_region` VALUES ('44375', '泾川县', '3', '44352', 'J');
INSERT INTO `ey_region` VALUES ('44392', '灵台县', '3', '44352', 'L');
INSERT INTO `ey_region` VALUES ('44408', '崇信县', '3', '44352', 'C');
INSERT INTO `ey_region` VALUES ('44418', '华亭县', '3', '44352', 'H');
INSERT INTO `ey_region` VALUES ('44431', '庄浪县', '3', '44352', 'Z');
INSERT INTO `ey_region` VALUES ('44451', '静宁县', '3', '44352', 'J');
INSERT INTO `ey_region` VALUES ('44477', '酒泉市', '2', '43776', 'J');
INSERT INTO `ey_region` VALUES ('44478', '市辖区', '3', '44477', 'S');
INSERT INTO `ey_region` VALUES ('44479', '肃州区', '3', '44477', 'S');
INSERT INTO `ey_region` VALUES ('44504', '金塔县', '3', '44477', 'J');
INSERT INTO `ey_region` VALUES ('44516', '瓜州县', '3', '44477', 'G');
INSERT INTO `ey_region` VALUES ('44531', '肃北县', '3', '44477', 'S');
INSERT INTO `ey_region` VALUES ('44535', '阿克塞县', '3', '44477', 'A');
INSERT INTO `ey_region` VALUES ('44539', '玉门市', '3', '44477', 'Y');
INSERT INTO `ey_region` VALUES ('44557', '敦煌市', '3', '44477', 'D');
INSERT INTO `ey_region` VALUES ('44569', '庆阳市', '2', '43776', 'Q');
INSERT INTO `ey_region` VALUES ('44570', '市辖区', '3', '44569', 'S');
INSERT INTO `ey_region` VALUES ('44571', '西峰区', '3', '44569', 'X');
INSERT INTO `ey_region` VALUES ('44582', '庆城县', '3', '44569', 'Q');
INSERT INTO `ey_region` VALUES ('44598', '环县', '3', '44569', 'H');
INSERT INTO `ey_region` VALUES ('44620', '华池县', '3', '44569', 'H');
INSERT INTO `ey_region` VALUES ('44636', '合水县', '3', '44569', 'H');
INSERT INTO `ey_region` VALUES ('44649', '正宁县', '3', '44569', 'Z');
INSERT INTO `ey_region` VALUES ('44660', '宁县', '3', '44569', 'N');
INSERT INTO `ey_region` VALUES ('44679', '镇原县', '3', '44569', 'Z');
INSERT INTO `ey_region` VALUES ('44699', '定西市', '2', '43776', 'D');
INSERT INTO `ey_region` VALUES ('44700', '市辖区', '3', '44699', 'S');
INSERT INTO `ey_region` VALUES ('44701', '安定区', '3', '44699', 'A');
INSERT INTO `ey_region` VALUES ('44723', '通渭县', '3', '44699', 'T');
INSERT INTO `ey_region` VALUES ('44742', '陇西县', '3', '44699', 'L');
INSERT INTO `ey_region` VALUES ('44760', '渭源县', '3', '44699', 'W');
INSERT INTO `ey_region` VALUES ('44777', '临洮县', '3', '44699', 'L');
INSERT INTO `ey_region` VALUES ('44796', '漳县', '3', '44699', 'Z');
INSERT INTO `ey_region` VALUES ('44810', '岷县', '3', '44699', 'M');
INSERT INTO `ey_region` VALUES ('44829', '陇南市', '2', '43776', 'L');
INSERT INTO `ey_region` VALUES ('44830', '市辖区', '3', '44829', 'S');
INSERT INTO `ey_region` VALUES ('44831', '武都区', '3', '44829', 'W');
INSERT INTO `ey_region` VALUES ('44868', '成县', '3', '44829', 'C');
INSERT INTO `ey_region` VALUES ('44886', '文县', '3', '44829', 'W');
INSERT INTO `ey_region` VALUES ('44907', '宕昌县', '3', '44829', 'D');
INSERT INTO `ey_region` VALUES ('44933', '康县', '3', '44829', 'K');
INSERT INTO `ey_region` VALUES ('44955', '西和县', '3', '44829', 'X');
INSERT INTO `ey_region` VALUES ('44976', '礼县', '3', '44829', 'L');
INSERT INTO `ey_region` VALUES ('45006', '徽县', '3', '44829', 'H');
INSERT INTO `ey_region` VALUES ('45022', '两当县', '3', '44829', 'L');
INSERT INTO `ey_region` VALUES ('45035', '临夏州', '2', '43776', 'L');
INSERT INTO `ey_region` VALUES ('45036', '临夏市', '3', '45035', 'L');
INSERT INTO `ey_region` VALUES ('45047', '临夏县', '3', '45035', 'L');
INSERT INTO `ey_region` VALUES ('45073', '康乐县', '3', '45035', 'K');
INSERT INTO `ey_region` VALUES ('45089', '永靖县', '3', '45035', 'Y');
INSERT INTO `ey_region` VALUES ('45107', '广河县', '3', '45035', 'G');
INSERT INTO `ey_region` VALUES ('45117', '和政县', '3', '45035', 'H');
INSERT INTO `ey_region` VALUES ('45131', '东乡县', '3', '45035', 'D');
INSERT INTO `ey_region` VALUES ('45156', '积石山县', '3', '45035', 'J');
INSERT INTO `ey_region` VALUES ('45174', '甘南州', '2', '43776', 'G');
INSERT INTO `ey_region` VALUES ('45175', '合作市', '3', '45174', 'H');
INSERT INTO `ey_region` VALUES ('45186', '临潭县', '3', '45174', 'L');
INSERT INTO `ey_region` VALUES ('45203', '卓尼县', '3', '45174', 'Z');
INSERT INTO `ey_region` VALUES ('45219', '舟曲县', '3', '45174', 'Z');
INSERT INTO `ey_region` VALUES ('45239', '迭部县', '3', '45174', 'D');
INSERT INTO `ey_region` VALUES ('45251', '玛曲县', '3', '45174', 'M');
INSERT INTO `ey_region` VALUES ('45263', '碌曲县', '3', '45174', 'L');
INSERT INTO `ey_region` VALUES ('45272', '夏河县', '3', '45174', 'X');
INSERT INTO `ey_region` VALUES ('45286', '青海省', '1', '0', 'Q');
INSERT INTO `ey_region` VALUES ('45287', '西宁市', '2', '45286', 'X');
INSERT INTO `ey_region` VALUES ('45288', '市辖区', '3', '45287', 'S');
INSERT INTO `ey_region` VALUES ('45289', '城东区', '3', '45287', 'C');
INSERT INTO `ey_region` VALUES ('45299', '城中区', '3', '45287', 'C');
INSERT INTO `ey_region` VALUES ('45306', '城西区', '3', '45287', 'C');
INSERT INTO `ey_region` VALUES ('45314', '城北区', '3', '45287', 'C');
INSERT INTO `ey_region` VALUES ('45320', '大通县', '3', '45287', 'D');
INSERT INTO `ey_region` VALUES ('45341', '湟中县', '3', '45287', 'H');
INSERT INTO `ey_region` VALUES ('45358', '湟源县', '3', '45287', 'H');
INSERT INTO `ey_region` VALUES ('45368', '海东地区', '2', '45286', 'H');
INSERT INTO `ey_region` VALUES ('45369', '平安县', '3', '45368', 'P');
INSERT INTO `ey_region` VALUES ('45378', '民和县', '3', '45368', 'M');
INSERT INTO `ey_region` VALUES ('45401', '乐都县', '3', '45368', 'L');
INSERT INTO `ey_region` VALUES ('45421', '互助县', '3', '45368', 'H');
INSERT INTO `ey_region` VALUES ('45441', '化隆县', '3', '45368', 'H');
INSERT INTO `ey_region` VALUES ('45461', '循化县', '3', '45368', 'X');
INSERT INTO `ey_region` VALUES ('45471', '海北州', '2', '45286', 'H');
INSERT INTO `ey_region` VALUES ('45472', '门源县', '3', '45471', 'M');
INSERT INTO `ey_region` VALUES ('45487', '祁连县', '3', '45471', 'Q');
INSERT INTO `ey_region` VALUES ('45495', '海晏县', '3', '45471', 'H');
INSERT INTO `ey_region` VALUES ('45502', '刚察县', '3', '45471', 'G');
INSERT INTO `ey_region` VALUES ('45510', '黄南州', '2', '45286', 'H');
INSERT INTO `ey_region` VALUES ('45511', '同仁县', '3', '45510', 'T');
INSERT INTO `ey_region` VALUES ('45523', '尖扎县', '3', '45510', 'J');
INSERT INTO `ey_region` VALUES ('45533', '泽库县', '3', '45510', 'Z');
INSERT INTO `ey_region` VALUES ('45542', '河南县', '3', '45510', 'H');
INSERT INTO `ey_region` VALUES ('45548', '海南州', '2', '45286', 'H');
INSERT INTO `ey_region` VALUES ('45549', '共和县', '3', '45548', 'G');
INSERT INTO `ey_region` VALUES ('45566', '同德县', '3', '45548', 'T');
INSERT INTO `ey_region` VALUES ('45573', '贵德县', '3', '45548', 'G');
INSERT INTO `ey_region` VALUES ('45581', '兴海县', '3', '45548', 'X');
INSERT INTO `ey_region` VALUES ('45589', '贵南县', '3', '45548', 'G');
INSERT INTO `ey_region` VALUES ('45597', '果洛州', '2', '45286', 'G');
INSERT INTO `ey_region` VALUES ('45598', '玛沁县', '3', '45597', 'M');
INSERT INTO `ey_region` VALUES ('45607', '班玛县', '3', '45597', 'B');
INSERT INTO `ey_region` VALUES ('45617', '甘德县', '3', '45597', 'G');
INSERT INTO `ey_region` VALUES ('45625', '达日县', '3', '45597', 'D');
INSERT INTO `ey_region` VALUES ('45636', '久治县', '3', '45597', 'J');
INSERT INTO `ey_region` VALUES ('45643', '玛多县', '3', '45597', 'M');
INSERT INTO `ey_region` VALUES ('45648', '玉树州', '2', '45286', 'Y');
INSERT INTO `ey_region` VALUES ('45649', '玉树县', '3', '45648', 'Y');
INSERT INTO `ey_region` VALUES ('45659', '杂多县', '3', '45648', 'Z');
INSERT INTO `ey_region` VALUES ('45668', '称多县', '3', '45648', 'C');
INSERT INTO `ey_region` VALUES ('45676', '治多县', '3', '45648', 'Z');
INSERT INTO `ey_region` VALUES ('45683', '囊谦县', '3', '45648', 'N');
INSERT INTO `ey_region` VALUES ('45694', '曲麻莱县', '3', '45648', 'Q');
INSERT INTO `ey_region` VALUES ('45701', '海西州', '2', '45286', 'H');
INSERT INTO `ey_region` VALUES ('45702', '格尔木市', '3', '45701', 'G');
INSERT INTO `ey_region` VALUES ('45714', '德令哈市', '3', '45701', 'D');
INSERT INTO `ey_region` VALUES ('45727', '乌兰县', '3', '45701', 'W');
INSERT INTO `ey_region` VALUES ('45733', '都兰县', '3', '45701', 'D');
INSERT INTO `ey_region` VALUES ('45742', '天峻县', '3', '45701', 'T');
INSERT INTO `ey_region` VALUES ('45753', '宁夏', '1', '0', 'N');
INSERT INTO `ey_region` VALUES ('45754', '银川市', '2', '45753', 'Y');
INSERT INTO `ey_region` VALUES ('45755', '市辖区', '3', '45754', 'S');
INSERT INTO `ey_region` VALUES ('45756', '兴庆区', '3', '45754', 'X');
INSERT INTO `ey_region` VALUES ('45772', '西夏区', '3', '45754', 'X');
INSERT INTO `ey_region` VALUES ('45784', '金凤区', '3', '45754', 'J');
INSERT INTO `ey_region` VALUES ('45794', '永宁县', '3', '45754', 'Y');
INSERT INTO `ey_region` VALUES ('45803', '贺兰县', '3', '45754', 'H');
INSERT INTO `ey_region` VALUES ('45813', '灵武市', '3', '45754', 'L');
INSERT INTO `ey_region` VALUES ('45825', '石嘴山市', '2', '45753', 'S');
INSERT INTO `ey_region` VALUES ('45826', '市辖区', '3', '45825', 'S');
INSERT INTO `ey_region` VALUES ('45827', '大武口区', '3', '45825', 'D');
INSERT INTO `ey_region` VALUES ('45839', '惠农区', '3', '45825', 'H');
INSERT INTO `ey_region` VALUES ('45856', '平罗县', '3', '45825', 'P');
INSERT INTO `ey_region` VALUES ('45871', '吴忠市', '2', '45753', 'W');
INSERT INTO `ey_region` VALUES ('45872', '市辖区', '3', '45871', 'S');
INSERT INTO `ey_region` VALUES ('45877', '利通区', '3', '45871', 'L');
INSERT INTO `ey_region` VALUES ('45892', '盐池县', '3', '45871', 'Y');
INSERT INTO `ey_region` VALUES ('45903', '同心县', '3', '45871', 'T');
INSERT INTO `ey_region` VALUES ('45914', '青铜峡市', '3', '45871', 'Q');
INSERT INTO `ey_region` VALUES ('45926', '固原市', '2', '45753', 'G');
INSERT INTO `ey_region` VALUES ('45927', '市辖区', '3', '45926', 'S');
INSERT INTO `ey_region` VALUES ('45928', '原州区', '3', '45926', 'Y');
INSERT INTO `ey_region` VALUES ('45944', '西吉县', '3', '45926', 'X');
INSERT INTO `ey_region` VALUES ('45964', '隆德县', '3', '45926', 'L');
INSERT INTO `ey_region` VALUES ('45978', '泾源县', '3', '45926', 'J');
INSERT INTO `ey_region` VALUES ('45986', '彭阳县', '3', '45926', 'P');
INSERT INTO `ey_region` VALUES ('45999', '中卫市', '2', '45753', 'Z');
INSERT INTO `ey_region` VALUES ('46000', '市辖区', '3', '45999', 'S');
INSERT INTO `ey_region` VALUES ('46012', '沙坡头区', '3', '45999', 'S');
INSERT INTO `ey_region` VALUES ('46013', '中宁县', '3', '45999', 'Z');
INSERT INTO `ey_region` VALUES ('46026', '海原县', '3', '45999', 'H');
INSERT INTO `ey_region` VALUES ('46047', '新疆', '1', '0', 'X');
INSERT INTO `ey_region` VALUES ('46048', '乌鲁木齐市', '2', '46047', 'W');
INSERT INTO `ey_region` VALUES ('46049', '市辖区', '3', '46048', 'S');
INSERT INTO `ey_region` VALUES ('46050', '天山区', '3', '46048', 'T');
INSERT INTO `ey_region` VALUES ('46065', '沙依巴克区', '3', '46048', 'S');
INSERT INTO `ey_region` VALUES ('46079', '新市区', '3', '46048', 'X');
INSERT INTO `ey_region` VALUES ('46095', '水磨沟区', '3', '46048', 'S');
INSERT INTO `ey_region` VALUES ('46104', '头屯河区', '3', '46048', 'T');
INSERT INTO `ey_region` VALUES ('46114', '达坂城区', '3', '46048', 'D');
INSERT INTO `ey_region` VALUES ('46123', '东山区', '3', '46048', 'D');
INSERT INTO `ey_region` VALUES ('46128', '乌鲁木齐县', '3', '46048', 'W');
INSERT INTO `ey_region` VALUES ('46138', '克拉玛依市', '2', '46047', 'K');
INSERT INTO `ey_region` VALUES ('46139', '市辖区', '3', '46138', 'S');
INSERT INTO `ey_region` VALUES ('46140', '独山子区', '3', '46138', 'D');
INSERT INTO `ey_region` VALUES ('46144', '克拉玛依区', '3', '46138', 'K');
INSERT INTO `ey_region` VALUES ('46155', '白碱滩区', '3', '46138', 'B');
INSERT INTO `ey_region` VALUES ('46158', '乌尔禾区', '3', '46138', 'W');
INSERT INTO `ey_region` VALUES ('46162', '吐鲁番地区', '2', '46047', 'T');
INSERT INTO `ey_region` VALUES ('46163', '吐鲁番市', '3', '46162', 'T');
INSERT INTO `ey_region` VALUES ('46178', '鄯善县', '3', '46162', 'S');
INSERT INTO `ey_region` VALUES ('46189', '托克逊县', '3', '46162', 'T');
INSERT INTO `ey_region` VALUES ('46197', '哈密地区', '2', '46047', 'H');
INSERT INTO `ey_region` VALUES ('46198', '哈密市', '3', '46197', 'H');
INSERT INTO `ey_region` VALUES ('46230', '巴里坤县', '3', '46197', 'B');
INSERT INTO `ey_region` VALUES ('46246', '伊吾县', '3', '46197', 'Y');
INSERT INTO `ey_region` VALUES ('46255', '昌吉州', '2', '46047', 'C');
INSERT INTO `ey_region` VALUES ('46256', '昌吉市', '3', '46255', 'C');
INSERT INTO `ey_region` VALUES ('46275', '阜康市', '3', '46255', 'F');
INSERT INTO `ey_region` VALUES ('46289', '米泉市', '3', '46255', 'M');
INSERT INTO `ey_region` VALUES ('46299', '呼图壁县', '3', '46255', 'H');
INSERT INTO `ey_region` VALUES ('46316', '玛纳斯', '3', '46255', 'M');
INSERT INTO `ey_region` VALUES ('46337', '奇台县', '3', '46255', 'Q');
INSERT INTO `ey_region` VALUES ('46355', '吉木萨尔县', '3', '46255', 'J');
INSERT INTO `ey_region` VALUES ('46366', '木垒县', '3', '46255', 'M');
INSERT INTO `ey_region` VALUES ('46380', '博州', '2', '46047', 'B');
INSERT INTO `ey_region` VALUES ('46381', '博乐市', '3', '46380', 'B');
INSERT INTO `ey_region` VALUES ('46399', '精河县', '3', '46380', 'J');
INSERT INTO `ey_region` VALUES ('46410', '温泉县', '3', '46380', 'W');
INSERT INTO `ey_region` VALUES ('46422', '巴州', '2', '46047', 'B');
INSERT INTO `ey_region` VALUES ('46423', '库尔勒市', '3', '46422', 'K');
INSERT INTO `ey_region` VALUES ('46451', '轮台县', '3', '46422', 'L');
INSERT INTO `ey_region` VALUES ('46463', '尉犁县', '3', '46422', 'W');
INSERT INTO `ey_region` VALUES ('46476', '若羌县', '3', '46422', 'R');
INSERT INTO `ey_region` VALUES ('46486', '且末县', '3', '46422', 'Q');
INSERT INTO `ey_region` VALUES ('46500', '焉耆县', '3', '46422', 'Y');
INSERT INTO `ey_region` VALUES ('46512', '和静县', '3', '46422', 'H');
INSERT INTO `ey_region` VALUES ('46531', '和硕县', '3', '46422', 'H');
INSERT INTO `ey_region` VALUES ('46542', '博湖县', '3', '46422', 'B');
INSERT INTO `ey_region` VALUES ('46551', '阿克苏地区', '2', '46047', 'A');
INSERT INTO `ey_region` VALUES ('46552', '阿克苏市', '3', '46551', 'A');
INSERT INTO `ey_region` VALUES ('46571', '温宿县', '3', '46551', 'W');
INSERT INTO `ey_region` VALUES ('46592', '库车县', '3', '46551', 'K');
INSERT INTO `ey_region` VALUES ('46617', '沙雅县', '3', '46551', 'S');
INSERT INTO `ey_region` VALUES ('46630', '新和县', '3', '46551', 'X');
INSERT INTO `ey_region` VALUES ('46640', '拜城县', '3', '46551', 'B');
INSERT INTO `ey_region` VALUES ('46657', '乌什县', '3', '46551', 'W');
INSERT INTO `ey_region` VALUES ('46668', '阿瓦提县', '3', '46551', 'A');
INSERT INTO `ey_region` VALUES ('46682', '柯坪县', '3', '46551', 'K');
INSERT INTO `ey_region` VALUES ('46688', '克州', '2', '46047', 'K');
INSERT INTO `ey_region` VALUES ('46689', '阿图什市', '3', '46688', 'A');
INSERT INTO `ey_region` VALUES ('46704', '阿克陶县', '3', '46688', 'A');
INSERT INTO `ey_region` VALUES ('46723', '阿合奇县', '3', '46688', 'A');
INSERT INTO `ey_region` VALUES ('46733', '乌恰县', '3', '46688', 'W');
INSERT INTO `ey_region` VALUES ('46747', '喀什地区', '2', '46047', 'K');
INSERT INTO `ey_region` VALUES ('46748', '喀什市', '3', '46747', 'K');
INSERT INTO `ey_region` VALUES ('46761', '疏附县', '3', '46747', 'S');
INSERT INTO `ey_region` VALUES ('46780', '疏勒县', '3', '46747', 'S');
INSERT INTO `ey_region` VALUES ('46797', '英吉沙县', '3', '46747', 'Y');
INSERT INTO `ey_region` VALUES ('46813', '泽普县', '3', '46747', 'Z');
INSERT INTO `ey_region` VALUES ('46830', '莎车县', '3', '46747', 'S');
INSERT INTO `ey_region` VALUES ('46863', '叶城县', '3', '46747', 'Y');
INSERT INTO `ey_region` VALUES ('46885', '麦盖提县', '3', '46747', 'M');
INSERT INTO `ey_region` VALUES ('46902', '岳普湖县', '3', '46747', 'Y');
INSERT INTO `ey_region` VALUES ('46913', '伽师县', '3', '46747', 'Q');
INSERT INTO `ey_region` VALUES ('46928', '巴楚县', '3', '46747', 'B');
INSERT INTO `ey_region` VALUES ('46942', '塔什库尔干县', '3', '46747', 'T');
INSERT INTO `ey_region` VALUES ('46957', '和田地区', '2', '46047', 'H');
INSERT INTO `ey_region` VALUES ('46958', '和田市', '3', '46957', 'H');
INSERT INTO `ey_region` VALUES ('46971', '和田县', '3', '46957', 'H');
INSERT INTO `ey_region` VALUES ('46983', '墨玉县', '3', '46957', 'M');
INSERT INTO `ey_region` VALUES ('47002', '皮山县', '3', '46957', 'P');
INSERT INTO `ey_region` VALUES ('47020', '洛浦县', '3', '46957', 'L');
INSERT INTO `ey_region` VALUES ('47032', '策勒县', '3', '46957', 'C');
INSERT INTO `ey_region` VALUES ('47042', '于田县', '3', '46957', 'Y');
INSERT INTO `ey_region` VALUES ('47061', '民丰县', '3', '46957', 'M');
INSERT INTO `ey_region` VALUES ('47069', '伊犁州', '2', '46047', 'Y');
INSERT INTO `ey_region` VALUES ('47070', '伊宁市', '3', '47069', 'Y');
INSERT INTO `ey_region` VALUES ('47091', '奎屯市', '3', '47069', 'K');
INSERT INTO `ey_region` VALUES ('47099', '伊宁县', '3', '47069', 'Y');
INSERT INTO `ey_region` VALUES ('47121', '察布查尔县', '3', '47069', 'C');
INSERT INTO `ey_region` VALUES ('47143', '霍城县', '3', '47069', 'H');
INSERT INTO `ey_region` VALUES ('47164', '巩留县', '3', '47069', 'G');
INSERT INTO `ey_region` VALUES ('47180', '新源县', '3', '47069', 'X');
INSERT INTO `ey_region` VALUES ('47196', '昭苏县', '3', '47069', 'Z');
INSERT INTO `ey_region` VALUES ('47214', '特克斯县', '3', '47069', 'T');
INSERT INTO `ey_region` VALUES ('47226', '尼勒克县', '3', '47069', 'N');
INSERT INTO `ey_region` VALUES ('47241', '塔城地区', '2', '46047', 'T');
INSERT INTO `ey_region` VALUES ('47242', '塔城市', '3', '47241', 'T');
INSERT INTO `ey_region` VALUES ('47258', '乌苏市', '3', '47241', 'W');
INSERT INTO `ey_region` VALUES ('47291', '额敏县', '3', '47241', 'E');
INSERT INTO `ey_region` VALUES ('47315', '沙湾县', '3', '47241', 'S');
INSERT INTO `ey_region` VALUES ('47338', '托里县', '3', '47241', 'T');
INSERT INTO `ey_region` VALUES ('47351', '裕民县', '3', '47241', 'Y');
INSERT INTO `ey_region` VALUES ('47360', '和布县', '3', '47241', 'H');
INSERT INTO `ey_region` VALUES ('47374', '阿勒泰地区', '2', '46047', 'A');
INSERT INTO `ey_region` VALUES ('47375', '阿勒泰市', '3', '47374', 'A');
INSERT INTO `ey_region` VALUES ('47393', '布尔津县', '3', '47374', 'B');
INSERT INTO `ey_region` VALUES ('47401', '富蕴县', '3', '47374', 'F');
INSERT INTO `ey_region` VALUES ('47411', '福海县', '3', '47374', 'F');
INSERT INTO `ey_region` VALUES ('47424', '哈巴河县', '3', '47374', 'H');
INSERT INTO `ey_region` VALUES ('47433', '青河县', '3', '47374', 'Q');
INSERT INTO `ey_region` VALUES ('47441', '吉木乃县', '3', '47374', 'J');
INSERT INTO `ey_region` VALUES ('47450', '省直辖行政单位', '2', '46047', 'S');
INSERT INTO `ey_region` VALUES ('47451', '石河子市', '3', '47450', 'S');
INSERT INTO `ey_region` VALUES ('47460', '阿拉尔市', '3', '47450', 'A');
INSERT INTO `ey_region` VALUES ('47477', '图木舒克市', '3', '47450', 'T');
INSERT INTO `ey_region` VALUES ('47486', '五家渠市', '3', '47450', 'W');
INSERT INTO `ey_region` VALUES ('47493', '台湾', '1', '0', 'T');
INSERT INTO `ey_region` VALUES ('47494', '香港', '1', '0', 'X');
INSERT INTO `ey_region` VALUES ('47495', '澳门', '1', '0', 'A');
INSERT INTO `ey_region` VALUES ('47496', '龙华新区', '3', '28558', 'L');
INSERT INTO `ey_region` VALUES ('47497', '光明新区', '3', '28558', 'G');
INSERT INTO `ey_region` VALUES ('47500', '新界', '2', '47494', 'X');
INSERT INTO `ey_region` VALUES ('47501', '观塘区', '3', '47498', 'G');
INSERT INTO `ey_region` VALUES ('47502', '黄大仙区', '3', '47498', 'H');
INSERT INTO `ey_region` VALUES ('47503', '九龙城区', '3', '47498', 'J');
INSERT INTO `ey_region` VALUES ('47504', '深水埗区', '3', '47498', 'S');
INSERT INTO `ey_region` VALUES ('47505', '油尖旺区', '3', '47498', 'Y');
INSERT INTO `ey_region` VALUES ('47506', '东区', '3', '47499', 'D');
INSERT INTO `ey_region` VALUES ('47507', '南区', '3', '47499', 'N');
INSERT INTO `ey_region` VALUES ('47508', '湾仔', '3', '47499', 'W');
INSERT INTO `ey_region` VALUES ('47509', '中西区', '3', '47499', 'Z');
INSERT INTO `ey_region` VALUES ('47510', '北区', '3', '47500', 'B');
INSERT INTO `ey_region` VALUES ('47511', '大埔区', '3', '47500', 'D');
INSERT INTO `ey_region` VALUES ('47512', '葵青区', '3', '47500', 'K');
INSERT INTO `ey_region` VALUES ('47513', '离岛区', '3', '47500', 'L');
INSERT INTO `ey_region` VALUES ('47514', '荃湾区', '3', '47500', 'Q');
INSERT INTO `ey_region` VALUES ('47515', '沙田区', '3', '47500', 'S');
INSERT INTO `ey_region` VALUES ('47516', '屯门区', '3', '47500', 'T');
INSERT INTO `ey_region` VALUES ('47517', '西贡区', '3', '47500', 'X');
INSERT INTO `ey_region` VALUES ('47518', '元朗区', '3', '47500', 'Y');
INSERT INTO `ey_region` VALUES ('47519', '澳门半岛', '2', '47495', 'A');
INSERT INTO `ey_region` VALUES ('47520', '离岛', '2', '47495', 'L');
INSERT INTO `ey_region` VALUES ('47521', '大堂区', '3', '47519', 'D');
INSERT INTO `ey_region` VALUES ('47522', '风顺堂区', '3', '47519', 'F');
INSERT INTO `ey_region` VALUES ('47523', '花地玛堂区', '3', '47519', 'H');
INSERT INTO `ey_region` VALUES ('47524', '花王堂区', '3', '47519', 'H');
INSERT INTO `ey_region` VALUES ('47525', '望德堂区', '3', '47519', 'W');
INSERT INTO `ey_region` VALUES ('47526', '嘉模堂区', '3', '47520', 'J');
INSERT INTO `ey_region` VALUES ('47527', '路氹填海区', '3', '47520', 'L');
INSERT INTO `ey_region` VALUES ('47528', '圣方济各堂区', '3', '47520', 'S');
INSERT INTO `ey_region` VALUES ('47529', '高雄市', '2', '47493', 'G');
INSERT INTO `ey_region` VALUES ('47530', '花莲县', '2', '47493', 'H');
INSERT INTO `ey_region` VALUES ('47531', '基隆市', '2', '47493', 'J');
INSERT INTO `ey_region` VALUES ('47532', '嘉义市', '2', '47493', 'J');
INSERT INTO `ey_region` VALUES ('47533', '嘉义县', '2', '47493', 'J');
INSERT INTO `ey_region` VALUES ('47534', '金门县', '2', '47493', 'J');
INSERT INTO `ey_region` VALUES ('47535', '连江县', '2', '47493', 'L');
INSERT INTO `ey_region` VALUES ('47536', '苗栗县', '2', '47493', 'M');
INSERT INTO `ey_region` VALUES ('47537', '南投县', '2', '47493', 'N');
INSERT INTO `ey_region` VALUES ('47538', '澎湖县', '2', '47493', 'P');
INSERT INTO `ey_region` VALUES ('47539', '屏东县', '2', '47493', 'P');
INSERT INTO `ey_region` VALUES ('47540', '台北市', '2', '47493', 'T');
INSERT INTO `ey_region` VALUES ('47541', '台东县', '2', '47493', 'T');
INSERT INTO `ey_region` VALUES ('47542', '台南市', '2', '47493', 'T');
INSERT INTO `ey_region` VALUES ('47543', '台中市', '2', '47493', 'T');
INSERT INTO `ey_region` VALUES ('47544', '桃园市', '2', '47493', 'T');
INSERT INTO `ey_region` VALUES ('47545', '新北市', '2', '47493', 'X');
INSERT INTO `ey_region` VALUES ('47546', '新竹市', '2', '47493', 'X');
INSERT INTO `ey_region` VALUES ('47547', '新竹县', '2', '47493', 'X');
INSERT INTO `ey_region` VALUES ('47548', '宜兰县', '2', '47493', 'Y');
INSERT INTO `ey_region` VALUES ('47549', '云林县', '2', '47493', 'Y');
INSERT INTO `ey_region` VALUES ('47550', '彰化县', '2', '47493', 'Z');
INSERT INTO `ey_region` VALUES ('47551', '阿莲区', '3', '47529', 'A');
INSERT INTO `ey_region` VALUES ('47552', '大寮区', '3', '47529', 'D');
INSERT INTO `ey_region` VALUES ('47553', '大社区', '3', '47529', 'D');
INSERT INTO `ey_region` VALUES ('47554', '大树区', '3', '47529', 'D');
INSERT INTO `ey_region` VALUES ('47555', '凤山区', '3', '47529', 'F');
INSERT INTO `ey_region` VALUES ('47556', '冈山区', '3', '47529', 'G');
INSERT INTO `ey_region` VALUES ('47557', '鼓山区', '3', '47529', 'G');
INSERT INTO `ey_region` VALUES ('47558', '湖内区', '3', '47529', 'H');
INSERT INTO `ey_region` VALUES ('47559', '甲仙区', '3', '47529', 'J');
INSERT INTO `ey_region` VALUES ('47560', '林园区', '3', '47529', 'L');
INSERT INTO `ey_region` VALUES ('47561', '苓雅区', '3', '47529', 'L');
INSERT INTO `ey_region` VALUES ('47562', '六龟区', '3', '47529', 'L');
INSERT INTO `ey_region` VALUES ('47563', '路竹区', '3', '47529', 'L');
INSERT INTO `ey_region` VALUES ('47564', '茂林区', '3', '47529', 'M');
INSERT INTO `ey_region` VALUES ('47565', '美浓区', '3', '47529', 'M');
INSERT INTO `ey_region` VALUES ('47566', '弥陀区', '3', '47529', 'M');
INSERT INTO `ey_region` VALUES ('47567', '楠梓区', '3', '47529', 'N');
INSERT INTO `ey_region` VALUES ('47568', '那玛夏区', '3', '47529', 'N');
INSERT INTO `ey_region` VALUES ('47569', '内门区', '3', '47529', 'N');
INSERT INTO `ey_region` VALUES ('47570', '鸟松区', '3', '47529', 'N');
INSERT INTO `ey_region` VALUES ('47571', '旗津区', '3', '47529', 'Q');
INSERT INTO `ey_region` VALUES ('47572', '旗门区', '3', '47529', 'Q');
INSERT INTO `ey_region` VALUES ('47573', '其它区', '3', '47529', 'Q');
INSERT INTO `ey_region` VALUES ('47574', '前金区', '3', '47529', 'Q');
INSERT INTO `ey_region` VALUES ('47575', '前镇区', '3', '47529', 'Q');
INSERT INTO `ey_region` VALUES ('47576', '桥头区', '3', '47529', 'Q');
INSERT INTO `ey_region` VALUES ('47577', '茄萣区', '3', '47529', 'Q');
INSERT INTO `ey_region` VALUES ('47578', '芩雅区', '3', '47529', 'Q');
INSERT INTO `ey_region` VALUES ('47579', '仁武区', '3', '47529', 'R');
INSERT INTO `ey_region` VALUES ('47580', '三民区', '3', '47529', 'S');
INSERT INTO `ey_region` VALUES ('47581', '杉林区', '3', '47529', 'S');
INSERT INTO `ey_region` VALUES ('47582', '桃源区', '3', '47529', 'T');
INSERT INTO `ey_region` VALUES ('47583', '田寮区', '3', '47529', 'T');
INSERT INTO `ey_region` VALUES ('47584', '小港区', '3', '47529', 'X');
INSERT INTO `ey_region` VALUES ('47585', '新兴区', '3', '47529', 'X');
INSERT INTO `ey_region` VALUES ('47586', '燕巢区', '3', '47529', 'Y');
INSERT INTO `ey_region` VALUES ('47587', '盐埕区', '3', '47529', 'Y');
INSERT INTO `ey_region` VALUES ('47588', '永安区', '3', '47529', 'Y');
INSERT INTO `ey_region` VALUES ('47589', '梓官区', '3', '47529', 'Z');
INSERT INTO `ey_region` VALUES ('47590', '左营区', '3', '47529', 'Z');
INSERT INTO `ey_region` VALUES ('47591', '丰滨乡', '3', '47530', 'F');
INSERT INTO `ey_region` VALUES ('47592', '凤林镇', '3', '47530', 'F');
INSERT INTO `ey_region` VALUES ('47593', '富里乡', '3', '47530', 'F');
INSERT INTO `ey_region` VALUES ('47594', '光复乡', '3', '47530', 'G');
INSERT INTO `ey_region` VALUES ('47595', '花莲市', '3', '47530', 'H');
INSERT INTO `ey_region` VALUES ('47596', '吉安乡', '3', '47530', 'J');
INSERT INTO `ey_region` VALUES ('47597', '瑞穗乡', '3', '47530', 'R');
INSERT INTO `ey_region` VALUES ('47598', '寿丰乡', '3', '47530', 'S');
INSERT INTO `ey_region` VALUES ('47599', '太鲁阁', '3', '47530', 'T');
INSERT INTO `ey_region` VALUES ('47600', '万荣乡', '3', '47530', 'W');
INSERT INTO `ey_region` VALUES ('47601', '新城乡', '3', '47530', 'X');
INSERT INTO `ey_region` VALUES ('47602', '秀林乡', '3', '47530', 'X');
INSERT INTO `ey_region` VALUES ('47603', '玉里镇', '3', '47530', 'Y');
INSERT INTO `ey_region` VALUES ('47604', '卓溪乡', '3', '47530', 'Z');
INSERT INTO `ey_region` VALUES ('47605', '安乐区', '3', '47531', 'A');
INSERT INTO `ey_region` VALUES ('47606', '暖暖区', '3', '47531', 'N');
INSERT INTO `ey_region` VALUES ('47607', '七堵区', '3', '47531', 'Q');
INSERT INTO `ey_region` VALUES ('47608', '其它区', '3', '47531', 'Q');
INSERT INTO `ey_region` VALUES ('47609', '仁爱区', '3', '47531', 'R');
INSERT INTO `ey_region` VALUES ('47610', '信义区', '3', '47531', 'X');
INSERT INTO `ey_region` VALUES ('47611', '中山区', '3', '47531', 'Z');
INSERT INTO `ey_region` VALUES ('47612', '中正区', '3', '47531', 'Z');
INSERT INTO `ey_region` VALUES ('47613', '东区', '3', '47532', 'D');
INSERT INTO `ey_region` VALUES ('47614', '西区', '3', '47532', 'X');
INSERT INTO `ey_region` VALUES ('47615', '其它区', '3', '47532', 'Q');
INSERT INTO `ey_region` VALUES ('47616', '阿里山乡', '3', '47533', 'A');
INSERT INTO `ey_region` VALUES ('47617', '布袋镇', '3', '47533', 'B');
INSERT INTO `ey_region` VALUES ('47618', '大林镇', '3', '47533', 'D');
INSERT INTO `ey_region` VALUES ('47619', '大埔乡', '3', '47533', 'D');
INSERT INTO `ey_region` VALUES ('47620', '东石乡', '3', '47533', 'D');
INSERT INTO `ey_region` VALUES ('47621', '番路乡', '3', '47533', 'F');
INSERT INTO `ey_region` VALUES ('47622', '六脚乡', '3', '47533', 'L');
INSERT INTO `ey_region` VALUES ('47623', '鹿草乡', '3', '47533', 'L');
INSERT INTO `ey_region` VALUES ('47624', '梅山乡', '3', '47533', 'M');
INSERT INTO `ey_region` VALUES ('47625', '民雄乡', '3', '47533', 'M');
INSERT INTO `ey_region` VALUES ('47626', '朴子市', '3', '47533', 'P');
INSERT INTO `ey_region` VALUES ('47627', '水上乡', '3', '47533', 'S');
INSERT INTO `ey_region` VALUES ('47628', '太保市', '3', '47533', 'T');
INSERT INTO `ey_region` VALUES ('47629', '溪口乡', '3', '47533', 'X');
INSERT INTO `ey_region` VALUES ('47630', '新港乡', '3', '47533', 'X');
INSERT INTO `ey_region` VALUES ('47631', '义竹乡', '3', '47533', 'Y');
INSERT INTO `ey_region` VALUES ('47632', '中埔乡', '3', '47533', 'Z');
INSERT INTO `ey_region` VALUES ('47633', '竹崎乡', '3', '47533', 'Z');
INSERT INTO `ey_region` VALUES ('47634', '金城镇', '3', '47534', 'J');
INSERT INTO `ey_region` VALUES ('47635', '金湖镇', '3', '47534', 'J');
INSERT INTO `ey_region` VALUES ('47636', '金宁乡', '3', '47534', 'J');
INSERT INTO `ey_region` VALUES ('47637', '金沙镇', '3', '47534', 'J');
INSERT INTO `ey_region` VALUES ('47638', '烈屿乡', '3', '47534', 'L');
INSERT INTO `ey_region` VALUES ('47639', '乌邱乡', '3', '47534', 'W');
INSERT INTO `ey_region` VALUES ('47640', '北竿乡', '3', '47535', 'B');
INSERT INTO `ey_region` VALUES ('47641', '东引乡', '3', '47535', 'D');
INSERT INTO `ey_region` VALUES ('47642', '莒光乡', '3', '47535', 'J');
INSERT INTO `ey_region` VALUES ('47643', '南竿乡', '3', '47535', 'N');
INSERT INTO `ey_region` VALUES ('47644', '大湖乡', '3', '47536', 'D');
INSERT INTO `ey_region` VALUES ('47645', '公馆乡', '3', '47536', 'G');
INSERT INTO `ey_region` VALUES ('47646', '后龙镇', '3', '47536', 'H');
INSERT INTO `ey_region` VALUES ('47647', '苗栗市', '3', '47536', 'M');
INSERT INTO `ey_region` VALUES ('47648', '南庄乡', '3', '47536', 'N');
INSERT INTO `ey_region` VALUES ('47649', '三湾乡', '3', '47536', 'S');
INSERT INTO `ey_region` VALUES ('47650', '三义乡', '3', '47536', 'S');
INSERT INTO `ey_region` VALUES ('47651', '狮潭乡', '3', '47536', 'S');
INSERT INTO `ey_region` VALUES ('47652', '泰安乡', '3', '47536', 'T');
INSERT INTO `ey_region` VALUES ('47653', '铜锣乡', '3', '47536', 'T');
INSERT INTO `ey_region` VALUES ('47654', '通宵镇', '3', '47536', 'T');
INSERT INTO `ey_region` VALUES ('47655', '头份镇', '3', '47536', 'T');
INSERT INTO `ey_region` VALUES ('47656', '头屋乡', '3', '47536', 'T');
INSERT INTO `ey_region` VALUES ('47657', '西湖乡', '3', '47536', 'X');
INSERT INTO `ey_region` VALUES ('47658', '苑里镇', '3', '47536', 'Y');
INSERT INTO `ey_region` VALUES ('47659', '造桥乡', '3', '47536', 'Z');
INSERT INTO `ey_region` VALUES ('47660', '竹南镇', '3', '47536', 'Z');
INSERT INTO `ey_region` VALUES ('47661', '卓兰镇', '3', '47536', 'Z');
INSERT INTO `ey_region` VALUES ('47662', '草屯镇', '3', '47537', 'C');
INSERT INTO `ey_region` VALUES ('47663', '国姓乡', '3', '47537', 'G');
INSERT INTO `ey_region` VALUES ('47664', '集集镇', '3', '47537', 'J');
INSERT INTO `ey_region` VALUES ('47665', '鹿谷乡', '3', '47537', 'L');
INSERT INTO `ey_region` VALUES ('47666', '名间乡', '3', '47537', 'M');
INSERT INTO `ey_region` VALUES ('47667', '南投市', '3', '47537', 'N');
INSERT INTO `ey_region` VALUES ('47668', '埔里镇', '3', '47537', 'P');
INSERT INTO `ey_region` VALUES ('47669', '仁爱乡', '3', '47537', 'R');
INSERT INTO `ey_region` VALUES ('47670', '水里乡', '3', '47537', 'S');
INSERT INTO `ey_region` VALUES ('47671', '信义乡', '3', '47537', 'X');
INSERT INTO `ey_region` VALUES ('47672', '鱼池乡', '3', '47537', 'Y');
INSERT INTO `ey_region` VALUES ('47673', '中寮乡', '3', '47537', 'Z');
INSERT INTO `ey_region` VALUES ('47674', '竹山镇', '3', '47537', 'Z');
INSERT INTO `ey_region` VALUES ('47675', '白沙乡', '3', '47538', 'B');
INSERT INTO `ey_region` VALUES ('47676', '湖西乡', '3', '47538', 'H');
INSERT INTO `ey_region` VALUES ('47677', '马公市', '3', '47538', 'M');
INSERT INTO `ey_region` VALUES ('47678', '七美乡', '3', '47538', 'Q');
INSERT INTO `ey_region` VALUES ('47679', '望安乡', '3', '47538', 'W');
INSERT INTO `ey_region` VALUES ('47680', '西屿乡', '3', '47538', 'X');
INSERT INTO `ey_region` VALUES ('47681', '长治乡', '3', '47539', 'C');
INSERT INTO `ey_region` VALUES ('47682', '潮州镇', '3', '47539', 'C');
INSERT INTO `ey_region` VALUES ('47683', '车城乡', '3', '47539', 'C');
INSERT INTO `ey_region` VALUES ('47684', '春日乡', '3', '47539', 'C');
INSERT INTO `ey_region` VALUES ('47685', '东港镇', '3', '47539', 'D');
INSERT INTO `ey_region` VALUES ('47686', '枋寮乡', '3', '47539', 'F');
INSERT INTO `ey_region` VALUES ('47687', '枋山乡', '3', '47539', 'F');
INSERT INTO `ey_region` VALUES ('47688', '高树乡', '3', '47539', 'G');
INSERT INTO `ey_region` VALUES ('47689', '恒春镇', '3', '47539', 'H');
INSERT INTO `ey_region` VALUES ('47690', '佳冬乡', '3', '47539', 'J');
INSERT INTO `ey_region` VALUES ('47691', '九如乡', '3', '47539', 'J');
INSERT INTO `ey_region` VALUES ('47692', '崁顶乡', '3', '47539', 'K');
INSERT INTO `ey_region` VALUES ('47693', '来义乡', '3', '47539', 'L');
INSERT INTO `ey_region` VALUES ('47694', '里港乡', '3', '47539', 'L');
INSERT INTO `ey_region` VALUES ('47695', '林边乡', '3', '47539', 'L');
INSERT INTO `ey_region` VALUES ('47696', '麟洛乡', '3', '47539', 'L');
INSERT INTO `ey_region` VALUES ('47697', '琉球乡', '3', '47539', 'L');
INSERT INTO `ey_region` VALUES ('47698', '玛家乡', '3', '47539', 'M');
INSERT INTO `ey_region` VALUES ('47699', '满州乡', '3', '47539', 'M');
INSERT INTO `ey_region` VALUES ('47700', '牡丹乡', '3', '47539', 'M');
INSERT INTO `ey_region` VALUES ('47701', '南州乡', '3', '47539', 'N');
INSERT INTO `ey_region` VALUES ('47702', '内埔乡', '3', '47539', 'N');
INSERT INTO `ey_region` VALUES ('47703', '屏东市', '3', '47539', 'P');
INSERT INTO `ey_region` VALUES ('47704', '三地门乡', '3', '47539', 'S');
INSERT INTO `ey_region` VALUES ('47705', '狮子乡', '3', '47539', 'S');
INSERT INTO `ey_region` VALUES ('47706', '泰武乡', '3', '47539', 'T');
INSERT INTO `ey_region` VALUES ('47707', '万丹乡', '3', '47539', 'W');
INSERT INTO `ey_region` VALUES ('47708', '万峦乡', '3', '47539', 'W');
INSERT INTO `ey_region` VALUES ('47709', '雾台乡', '3', '47539', 'W');
INSERT INTO `ey_region` VALUES ('47710', '新埤乡', '3', '47539', 'X');
INSERT INTO `ey_region` VALUES ('47711', '新园乡', '3', '47539', 'X');
INSERT INTO `ey_region` VALUES ('47712', '盐埔乡', '3', '47539', 'Y');
INSERT INTO `ey_region` VALUES ('47713', '竹田乡', '3', '47539', 'Z');
INSERT INTO `ey_region` VALUES ('47714', '北投区', '3', '47540', 'B');
INSERT INTO `ey_region` VALUES ('47715', '大安区', '3', '47540', 'D');
INSERT INTO `ey_region` VALUES ('47716', '大同区', '3', '47540', 'D');
INSERT INTO `ey_region` VALUES ('47717', '南港区', '3', '47540', 'N');
INSERT INTO `ey_region` VALUES ('47718', '内湖区', '3', '47540', 'N');
INSERT INTO `ey_region` VALUES ('47719', '士林区', '3', '47540', 'S');
INSERT INTO `ey_region` VALUES ('47720', '松山区', '3', '47540', 'S');
INSERT INTO `ey_region` VALUES ('47721', '万华区', '3', '47540', 'W');
INSERT INTO `ey_region` VALUES ('47722', '文山区', '3', '47540', 'W');
INSERT INTO `ey_region` VALUES ('47723', '信义区', '3', '47540', 'X');
INSERT INTO `ey_region` VALUES ('47724', '中山区', '3', '47540', 'Z');
INSERT INTO `ey_region` VALUES ('47725', '中正区', '3', '47540', 'Z');
INSERT INTO `ey_region` VALUES ('47726', '其它区', '3', '47540', 'Q');
INSERT INTO `ey_region` VALUES ('47727', '卑南乡', '3', '47541', 'B');
INSERT INTO `ey_region` VALUES ('47728', '长滨乡', '3', '47541', 'C');
INSERT INTO `ey_region` VALUES ('47729', '成功镇', '3', '47541', 'C');
INSERT INTO `ey_region` VALUES ('47730', '池上乡', '3', '47541', 'C');
INSERT INTO `ey_region` VALUES ('47731', '达仁乡', '3', '47541', 'D');
INSERT INTO `ey_region` VALUES ('47732', '大武乡', '3', '47541', 'D');
INSERT INTO `ey_region` VALUES ('47733', '东河乡', '3', '47541', 'D');
INSERT INTO `ey_region` VALUES ('47734', '关山镇', '3', '47541', 'G');
INSERT INTO `ey_region` VALUES ('47735', '海端乡', '3', '47541', 'H');
INSERT INTO `ey_region` VALUES ('47736', '金峰乡', '3', '47541', 'J');
INSERT INTO `ey_region` VALUES ('47737', '兰屿乡', '3', '47541', 'L');
INSERT INTO `ey_region` VALUES ('47738', '鹿野乡', '3', '47541', 'L');
INSERT INTO `ey_region` VALUES ('47739', '绿岛乡', '3', '47541', 'L');
INSERT INTO `ey_region` VALUES ('47740', '台东市', '3', '47541', 'T');
INSERT INTO `ey_region` VALUES ('47741', '太麻里乡', '3', '47541', 'T');
INSERT INTO `ey_region` VALUES ('47742', '延平乡', '3', '47541', 'Y');
INSERT INTO `ey_region` VALUES ('47743', '中西区', '3', '47542', 'Z');
INSERT INTO `ey_region` VALUES ('47744', '东区', '3', '47542', 'D');
INSERT INTO `ey_region` VALUES ('47745', '南区', '3', '47542', 'N');
INSERT INTO `ey_region` VALUES ('47746', '北区', '3', '47542', 'B');
INSERT INTO `ey_region` VALUES ('47747', '安平区', '3', '47542', 'A');
INSERT INTO `ey_region` VALUES ('47748', '安南区', '3', '47542', 'A');
INSERT INTO `ey_region` VALUES ('47749', '其它区', '3', '47542', 'Q');
INSERT INTO `ey_region` VALUES ('47750', '永康区', '3', '47542', 'Y');
INSERT INTO `ey_region` VALUES ('47751', '归仁区', '3', '47542', 'G');
INSERT INTO `ey_region` VALUES ('47752', '新化区', '3', '47542', 'X');
INSERT INTO `ey_region` VALUES ('47753', '左镇区', '3', '47542', 'Z');
INSERT INTO `ey_region` VALUES ('47754', '玉井区', '3', '47542', 'Y');
INSERT INTO `ey_region` VALUES ('47755', '楠西区', '3', '47542', 'N');
INSERT INTO `ey_region` VALUES ('47756', '南化区', '3', '47542', 'N');
INSERT INTO `ey_region` VALUES ('47757', '仁德区', '3', '47542', 'R');
INSERT INTO `ey_region` VALUES ('47758', '关庙区', '3', '47542', 'G');
INSERT INTO `ey_region` VALUES ('47759', '龙崎区', '3', '47542', 'L');
INSERT INTO `ey_region` VALUES ('47760', '官田区', '3', '47542', 'G');
INSERT INTO `ey_region` VALUES ('47761', '麻豆区', '3', '47542', 'M');
INSERT INTO `ey_region` VALUES ('47762', '佳里区', '3', '47542', 'J');
INSERT INTO `ey_region` VALUES ('47763', '西港区', '3', '47542', 'X');
INSERT INTO `ey_region` VALUES ('47764', '七股区', '3', '47542', 'Q');
INSERT INTO `ey_region` VALUES ('47765', '将军区', '3', '47542', 'J');
INSERT INTO `ey_region` VALUES ('47766', '学甲区', '3', '47542', 'X');
INSERT INTO `ey_region` VALUES ('47767', '北门区', '3', '47542', 'B');
INSERT INTO `ey_region` VALUES ('47768', '新营区', '3', '47542', 'X');
INSERT INTO `ey_region` VALUES ('47769', '后壁区', '3', '47542', 'H');
INSERT INTO `ey_region` VALUES ('47770', '白河区', '3', '47542', 'B');
INSERT INTO `ey_region` VALUES ('47771', '东山区', '3', '47542', 'D');
INSERT INTO `ey_region` VALUES ('47772', '六甲区', '3', '47542', 'L');
INSERT INTO `ey_region` VALUES ('47773', '下营区', '3', '47542', 'X');
INSERT INTO `ey_region` VALUES ('47774', '柳营区', '3', '47542', 'L');
INSERT INTO `ey_region` VALUES ('47775', '盐水区', '3', '47542', 'Y');
INSERT INTO `ey_region` VALUES ('47776', '善化区', '3', '47542', 'S');
INSERT INTO `ey_region` VALUES ('47777', '大内区', '3', '47542', 'D');
INSERT INTO `ey_region` VALUES ('47778', '山上区', '3', '47542', 'S');
INSERT INTO `ey_region` VALUES ('47779', '新市区', '3', '47542', 'X');
INSERT INTO `ey_region` VALUES ('47780', '安定区', '3', '47542', 'A');
INSERT INTO `ey_region` VALUES ('47781', '中区', '3', '47543', 'Z');
INSERT INTO `ey_region` VALUES ('47782', '东区', '3', '47543', 'D');
INSERT INTO `ey_region` VALUES ('47783', '南区', '3', '47543', 'N');
INSERT INTO `ey_region` VALUES ('47784', '西区', '3', '47543', 'X');
INSERT INTO `ey_region` VALUES ('47785', '北区', '3', '47543', 'B');
INSERT INTO `ey_region` VALUES ('47786', '北屯区', '3', '47543', 'B');
INSERT INTO `ey_region` VALUES ('47787', '西屯区', '3', '47543', 'X');
INSERT INTO `ey_region` VALUES ('47788', '南屯区', '3', '47543', 'N');
INSERT INTO `ey_region` VALUES ('47789', '其它区', '3', '47543', 'Q');
INSERT INTO `ey_region` VALUES ('47790', '太平区', '3', '47543', 'T');
INSERT INTO `ey_region` VALUES ('47791', '大里区', '3', '47543', 'D');
INSERT INTO `ey_region` VALUES ('47792', '雾峰区', '3', '47543', 'W');
INSERT INTO `ey_region` VALUES ('47793', '乌日区', '3', '47543', 'W');
INSERT INTO `ey_region` VALUES ('47794', '丰原区', '3', '47543', 'F');
INSERT INTO `ey_region` VALUES ('47795', '后里区', '3', '47543', 'H');
INSERT INTO `ey_region` VALUES ('47796', '石冈区', '3', '47543', 'S');
INSERT INTO `ey_region` VALUES ('47797', '东势区', '3', '47543', 'D');
INSERT INTO `ey_region` VALUES ('47798', '和平区', '3', '47543', 'H');
INSERT INTO `ey_region` VALUES ('47799', '新社区', '3', '47543', 'X');
INSERT INTO `ey_region` VALUES ('47800', '潭子区', '3', '47543', 'T');
INSERT INTO `ey_region` VALUES ('47801', '大雅区', '3', '47543', 'D');
INSERT INTO `ey_region` VALUES ('47802', '神冈区', '3', '47543', 'S');
INSERT INTO `ey_region` VALUES ('47803', '大肚区', '3', '47543', 'D');
INSERT INTO `ey_region` VALUES ('47804', '沙鹿区', '3', '47543', 'S');
INSERT INTO `ey_region` VALUES ('47805', '龙井区', '3', '47543', 'L');
INSERT INTO `ey_region` VALUES ('47806', '梧栖区', '3', '47543', 'W');
INSERT INTO `ey_region` VALUES ('47807', '清水区', '3', '47543', 'Q');
INSERT INTO `ey_region` VALUES ('47808', '大甲区', '3', '47543', 'D');
INSERT INTO `ey_region` VALUES ('47809', '外埔区', '3', '47543', 'W');
INSERT INTO `ey_region` VALUES ('47810', '大安区', '3', '47543', 'D');
INSERT INTO `ey_region` VALUES ('47811', '中坜区', '3', '47544', 'Z');
INSERT INTO `ey_region` VALUES ('47812', '平镇区', '3', '47544', 'P');
INSERT INTO `ey_region` VALUES ('47813', '龙潭区', '3', '47544', 'L');
INSERT INTO `ey_region` VALUES ('47814', '杨梅区', '3', '47544', 'Y');
INSERT INTO `ey_region` VALUES ('47815', '新屋区', '3', '47544', 'X');
INSERT INTO `ey_region` VALUES ('47816', '观音区', '3', '47544', 'G');
INSERT INTO `ey_region` VALUES ('47817', '桃园区', '3', '47544', 'T');
INSERT INTO `ey_region` VALUES ('47818', '龟山区', '3', '47544', 'G');
INSERT INTO `ey_region` VALUES ('47819', '八德区', '3', '47544', 'B');
INSERT INTO `ey_region` VALUES ('47820', '大溪区', '3', '47544', 'D');
INSERT INTO `ey_region` VALUES ('47821', '复兴区', '3', '47544', 'F');
INSERT INTO `ey_region` VALUES ('47822', '大园区', '3', '47544', 'D');
INSERT INTO `ey_region` VALUES ('47823', '芦竹区', '3', '47544', 'L');
INSERT INTO `ey_region` VALUES ('47824', '万里区', '3', '47545', 'W');
INSERT INTO `ey_region` VALUES ('47825', '金山区', '3', '47545', 'J');
INSERT INTO `ey_region` VALUES ('47826', '板桥区', '3', '47545', 'B');
INSERT INTO `ey_region` VALUES ('47827', '汐止区', '3', '47545', 'X');
INSERT INTO `ey_region` VALUES ('47828', '深坑区', '3', '47545', 'S');
INSERT INTO `ey_region` VALUES ('47829', '石碇区', '3', '47545', 'S');
INSERT INTO `ey_region` VALUES ('47830', '瑞芳区', '3', '47545', 'R');
INSERT INTO `ey_region` VALUES ('47831', '平溪区', '3', '47545', 'P');
INSERT INTO `ey_region` VALUES ('47832', '双溪区', '3', '47545', 'S');
INSERT INTO `ey_region` VALUES ('47833', '贡寮区', '3', '47545', 'G');
INSERT INTO `ey_region` VALUES ('47834', '新店区', '3', '47545', 'X');
INSERT INTO `ey_region` VALUES ('47835', '坪林区', '3', '47545', 'P');
INSERT INTO `ey_region` VALUES ('47836', '乌来区', '3', '47545', 'W');
INSERT INTO `ey_region` VALUES ('47837', '永和区', '3', '47545', 'Y');
INSERT INTO `ey_region` VALUES ('47838', '中和区', '3', '47545', 'Z');
INSERT INTO `ey_region` VALUES ('47839', '土城区', '3', '47545', 'T');
INSERT INTO `ey_region` VALUES ('47840', '三峡区', '3', '47545', 'S');
INSERT INTO `ey_region` VALUES ('47841', '树林区', '3', '47545', 'S');
INSERT INTO `ey_region` VALUES ('47842', '莺歌区', '3', '47545', 'Y');
INSERT INTO `ey_region` VALUES ('47843', '三重区', '3', '47545', 'S');
INSERT INTO `ey_region` VALUES ('47844', '新庄区', '3', '47545', 'X');
INSERT INTO `ey_region` VALUES ('47845', '泰山区', '3', '47545', 'T');
INSERT INTO `ey_region` VALUES ('47846', '林口区', '3', '47545', 'L');
INSERT INTO `ey_region` VALUES ('47847', '芦洲区', '3', '47545', 'L');
INSERT INTO `ey_region` VALUES ('47848', '五股区', '3', '47545', 'W');
INSERT INTO `ey_region` VALUES ('47849', '八里区', '3', '47545', 'B');
INSERT INTO `ey_region` VALUES ('47850', '淡水区', '3', '47545', 'D');
INSERT INTO `ey_region` VALUES ('47851', '三芝区', '3', '47545', 'S');
INSERT INTO `ey_region` VALUES ('47852', '石门区', '3', '47545', 'S');
INSERT INTO `ey_region` VALUES ('47853', '东区', '3', '47546', 'D');
INSERT INTO `ey_region` VALUES ('47854', '北区', '3', '47546', 'B');
INSERT INTO `ey_region` VALUES ('47855', '香山区', '3', '47546', 'X');
INSERT INTO `ey_region` VALUES ('47856', '其它区', '3', '47546', 'Q');
INSERT INTO `ey_region` VALUES ('47857', '竹北市', '3', '47547', 'Z');
INSERT INTO `ey_region` VALUES ('47858', '湖口乡', '3', '47547', 'H');
INSERT INTO `ey_region` VALUES ('47859', '新丰乡', '3', '47547', 'X');
INSERT INTO `ey_region` VALUES ('47860', '新埔镇', '3', '47547', 'X');
INSERT INTO `ey_region` VALUES ('47861', '关西镇', '3', '47547', 'G');
INSERT INTO `ey_region` VALUES ('47862', '芎林乡', '3', '47547', 'X');
INSERT INTO `ey_region` VALUES ('47863', '宝山乡', '3', '47547', 'B');
INSERT INTO `ey_region` VALUES ('47864', '竹东镇', '3', '47547', 'Z');
INSERT INTO `ey_region` VALUES ('47865', '五峰乡', '3', '47547', 'W');
INSERT INTO `ey_region` VALUES ('47866', '横山乡', '3', '47547', 'H');
INSERT INTO `ey_region` VALUES ('47867', '尖石乡', '3', '47547', 'J');
INSERT INTO `ey_region` VALUES ('47868', '北埔乡', '3', '47547', 'B');
INSERT INTO `ey_region` VALUES ('47869', '峨眉乡', '3', '47547', 'E');
INSERT INTO `ey_region` VALUES ('47870', '宜兰市', '3', '47548', 'Y');
INSERT INTO `ey_region` VALUES ('47871', '头城镇', '3', '47548', 'T');
INSERT INTO `ey_region` VALUES ('47872', '礁溪乡', '3', '47548', 'J');
INSERT INTO `ey_region` VALUES ('47873', '壮围乡', '3', '47548', 'Z');
INSERT INTO `ey_region` VALUES ('47874', '员山乡', '3', '47548', 'Y');
INSERT INTO `ey_region` VALUES ('47875', '罗东镇', '3', '47548', 'L');
INSERT INTO `ey_region` VALUES ('47876', '三星乡', '3', '47548', 'S');
INSERT INTO `ey_region` VALUES ('47877', '大同乡', '3', '47548', 'D');
INSERT INTO `ey_region` VALUES ('47878', '五结乡', '3', '47548', 'W');
INSERT INTO `ey_region` VALUES ('47879', '冬山乡', '3', '47548', 'D');
INSERT INTO `ey_region` VALUES ('47880', '苏澳镇', '3', '47548', 'S');
INSERT INTO `ey_region` VALUES ('47881', '南澳乡', '3', '47548', 'N');
INSERT INTO `ey_region` VALUES ('47882', '钓鱼台', '3', '47548', 'D');
INSERT INTO `ey_region` VALUES ('47883', '斗南镇', '3', '47549', 'D');
INSERT INTO `ey_region` VALUES ('47884', '大埤乡', '3', '47549', 'D');
INSERT INTO `ey_region` VALUES ('47885', '虎尾镇', '3', '47549', 'H');
INSERT INTO `ey_region` VALUES ('47886', '土库镇', '3', '47549', 'T');
INSERT INTO `ey_region` VALUES ('47887', '褒忠乡', '3', '47549', 'B');
INSERT INTO `ey_region` VALUES ('47888', '东势乡', '3', '47549', 'D');
INSERT INTO `ey_region` VALUES ('47889', '台西乡', '3', '47549', 'T');
INSERT INTO `ey_region` VALUES ('47890', '仑背乡', '3', '47549', 'L');
INSERT INTO `ey_region` VALUES ('47891', '麦寮乡', '3', '47549', 'M');
INSERT INTO `ey_region` VALUES ('47892', '斗六市', '3', '47549', 'D');
INSERT INTO `ey_region` VALUES ('47893', '林内乡', '3', '47549', 'L');
INSERT INTO `ey_region` VALUES ('47894', '古坑乡', '3', '47549', 'G');
INSERT INTO `ey_region` VALUES ('47895', '莿桐乡', '3', '47549', 'C');
INSERT INTO `ey_region` VALUES ('47896', '西螺镇', '3', '47549', 'X');
INSERT INTO `ey_region` VALUES ('47897', '二仑乡', '3', '47549', 'E');
INSERT INTO `ey_region` VALUES ('47898', '北港镇', '3', '47549', 'B');
INSERT INTO `ey_region` VALUES ('47899', '水林乡', '3', '47549', 'S');
INSERT INTO `ey_region` VALUES ('47900', '口湖乡', '3', '47549', 'K');
INSERT INTO `ey_region` VALUES ('47901', '四湖乡', '3', '47549', 'S');
INSERT INTO `ey_region` VALUES ('47902', '元长乡', '3', '47549', 'Y');
INSERT INTO `ey_region` VALUES ('47903', '彰化市', '3', '47550', 'Z');
INSERT INTO `ey_region` VALUES ('47904', '芬园乡', '3', '47550', 'F');
INSERT INTO `ey_region` VALUES ('47905', '花坛乡', '3', '47550', 'H');
INSERT INTO `ey_region` VALUES ('47906', '秀水乡', '3', '47550', 'X');
INSERT INTO `ey_region` VALUES ('47907', '鹿港镇', '3', '47550', 'L');
INSERT INTO `ey_region` VALUES ('47908', '福兴乡', '3', '47550', 'F');
INSERT INTO `ey_region` VALUES ('47909', '线西乡', '3', '47550', 'X');
INSERT INTO `ey_region` VALUES ('47910', '和美镇', '3', '47550', 'H');
INSERT INTO `ey_region` VALUES ('47911', '伸港乡', '3', '47550', 'S');
INSERT INTO `ey_region` VALUES ('47912', '员林镇', '3', '47550', 'Y');
INSERT INTO `ey_region` VALUES ('47913', '社头乡', '3', '47550', 'S');
INSERT INTO `ey_region` VALUES ('47914', '永靖乡', '3', '47550', 'Y');
INSERT INTO `ey_region` VALUES ('47915', '埔心乡', '3', '47550', 'P');
INSERT INTO `ey_region` VALUES ('47916', '溪湖镇', '3', '47550', 'X');
INSERT INTO `ey_region` VALUES ('47917', '大村乡', '3', '47550', 'D');
INSERT INTO `ey_region` VALUES ('47918', '埔盐乡', '3', '47550', 'P');
INSERT INTO `ey_region` VALUES ('47919', '田中镇', '3', '47550', 'T');
INSERT INTO `ey_region` VALUES ('47920', '北斗镇', '3', '47550', 'B');
INSERT INTO `ey_region` VALUES ('47921', '田尾乡', '3', '47550', 'T');
INSERT INTO `ey_region` VALUES ('47922', '埤头乡', '3', '47550', 'P');
INSERT INTO `ey_region` VALUES ('47923', '溪州乡', '3', '47550', 'X');
INSERT INTO `ey_region` VALUES ('47924', '竹塘乡', '3', '47550', 'Z');
INSERT INTO `ey_region` VALUES ('47925', '二林镇', '3', '47550', 'E');
INSERT INTO `ey_region` VALUES ('47926', '大城乡', '3', '47550', 'D');
INSERT INTO `ey_region` VALUES ('47927', '芳苑乡', '3', '47550', 'F');
INSERT INTO `ey_region` VALUES ('47928', '二水乡', '3', '47550', 'E');
INSERT INTO `ey_region` VALUES ('47929', '莲池区', '3', '1772', 'L');
INSERT INTO `ey_region` VALUES ('47930', '竞秀区', '3', '1772', 'J');
INSERT INTO `ey_region` VALUES ('47931', '常平镇', '3', '29855', 'C');
INSERT INTO `ey_region` VALUES ('47932', '茶山镇', '3', '29855', 'C');
INSERT INTO `ey_region` VALUES ('47933', '大朗镇', '3', '29855', 'D');
INSERT INTO `ey_region` VALUES ('47934', '大岭山镇', '3', '29855', 'D');
INSERT INTO `ey_region` VALUES ('47935', '道滘镇', '3', '29855', 'D');
INSERT INTO `ey_region` VALUES ('47936', '东城街道', '3', '29855', 'D');
INSERT INTO `ey_region` VALUES ('47937', '东坑镇', '3', '29855', 'D');
INSERT INTO `ey_region` VALUES ('47938', '凤岗镇', '3', '29855', 'F');
INSERT INTO `ey_region` VALUES ('47939', '高埗镇', '3', '29855', 'G');
INSERT INTO `ey_region` VALUES ('47940', '莞城街道', '3', '29855', 'G');
INSERT INTO `ey_region` VALUES ('47941', '横沥镇', '3', '29855', 'H');
INSERT INTO `ey_region` VALUES ('47942', '洪梅镇', '3', '29855', 'H');
INSERT INTO `ey_region` VALUES ('47943', '厚街镇', '3', '29855', 'H');
INSERT INTO `ey_region` VALUES ('47944', '黄江镇', '3', '29855', 'H');
INSERT INTO `ey_region` VALUES ('47945', '虎门镇', '3', '29855', 'H');
INSERT INTO `ey_region` VALUES ('47946', '寮步镇', '3', '29855', 'L');
INSERT INTO `ey_region` VALUES ('47947', '麻涌镇', '3', '29855', 'M');
INSERT INTO `ey_region` VALUES ('47948', '南城街道', '3', '29855', 'N');
INSERT INTO `ey_region` VALUES ('47949', '桥头镇', '3', '29855', 'Q');
INSERT INTO `ey_region` VALUES ('47950', '清溪镇', '3', '29855', 'Q');
INSERT INTO `ey_region` VALUES ('47951', '企石镇', '3', '29855', 'Q');
INSERT INTO `ey_region` VALUES ('47952', '沙田镇', '3', '29855', 'S');
INSERT INTO `ey_region` VALUES ('47953', '石碣镇', '3', '29855', 'S');
INSERT INTO `ey_region` VALUES ('47954', '石龙镇', '3', '29855', 'S');
INSERT INTO `ey_region` VALUES ('47955', '石排镇', '3', '29855', 'S');
INSERT INTO `ey_region` VALUES ('47956', '松山湖管委会', '3', '29855', 'S');
INSERT INTO `ey_region` VALUES ('47957', '塘厦镇', '3', '29855', 'T');
INSERT INTO `ey_region` VALUES ('47958', '望牛墩镇', '3', '29855', 'W');
INSERT INTO `ey_region` VALUES ('47959', '万江街道', '3', '29855', 'W');
INSERT INTO `ey_region` VALUES ('47960', '谢岗镇', '3', '29855', 'X');
INSERT INTO `ey_region` VALUES ('47961', '长安镇', '3', '29855', 'Z');
INSERT INTO `ey_region` VALUES ('47962', '樟木头镇', '3', '29855', 'Z');
INSERT INTO `ey_region` VALUES ('47963', '中堂镇', '3', '29855', 'Z');

-- -----------------------------
-- Table structure for `ey_search_word`
-- -----------------------------
DROP TABLE IF EXISTS `ey_search_word`;
CREATE TABLE `ey_search_word` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `word` varchar(50) DEFAULT '' COMMENT '关键词',
  `searchNum` int(10) DEFAULT '1' COMMENT '搜索次数',
  `sort_order` int(10) DEFAULT '0' COMMENT '排序号',
  `lang` varchar(30) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `word` (`word`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='搜索词统计表';

-- -----------------------------
-- Records of `ey_search_word`
-- -----------------------------
INSERT INTO `ey_search_word` VALUES ('1', '名称', '2', '100', 'cn', '1563521156', '1563762185');
INSERT INTO `ey_search_word` VALUES ('2', '媒体', '8', '100', 'cn', '1563762250', '1610348638');
INSERT INTO `ey_search_word` VALUES ('3', '融资', '1', '100', 'cn', '1563788844', '1563788844');

-- -----------------------------
-- Table structure for `ey_setting`
-- -----------------------------
DROP TABLE IF EXISTS `ey_setting`;
CREATE TABLE `ey_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '' COMMENT '配置的key键名',
  `value` text,
  `inc_type` varchar(64) DEFAULT '' COMMENT '配置分组',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `inc_type` (`inc_type`,`lang`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='系统非全局配置表';

-- -----------------------------
-- Records of `ey_setting`
-- -----------------------------
INSERT INTO `ey_setting` VALUES ('1', 'ask_ques_steps', '1、写问题标题，描述具体现象。杜绝 “求救，大佬，小白…” 等和问题无关的词汇。\r\n2、选择问题的分类，选择正确的内容分类，能更快的得到其他人的回复。\r\n3、遇到的问题比较急需解决，可以给问题悬赏一定的金额报酬，能让更多同行参与进来出谋策划，从中选择自己心仪的答案。\r\n4、写问题内容详细描述你碰到的困难，写清楚你尝试了什么方法，错误代码，软件的版本等，更容易得到答案。\r\n5、点击发布。', 'ask', 'cn', '1623322108');
INSERT INTO `ey_setting` VALUES ('2', 'recycle_switch', '1', 'recycle', 'cn', '1623809302');
INSERT INTO `ey_setting` VALUES ('3', 'system_old_product_attr', '0', 'system', 'cn', '1623813369');
INSERT INTO `ey_setting` VALUES ('4', 'syn_admin_logic_1623377269', '1', 'syn', 'cn', '1623813369');

-- -----------------------------
-- Table structure for `ey_sharp_active`
-- -----------------------------
DROP TABLE IF EXISTS `ey_sharp_active`;
CREATE TABLE `ey_sharp_active` (
  `active_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '活动会场ID',
  `active_date` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动日期',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '活动状态(0禁用 1启用)',
  `is_del` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`active_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='整点秒杀-活动会场表';


-- -----------------------------
-- Table structure for `ey_sharp_active_goods`
-- -----------------------------
DROP TABLE IF EXISTS `ey_sharp_active_goods`;
CREATE TABLE `ey_sharp_active_goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `active_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动会场ID',
  `active_time_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动场次ID',
  `aid` int(11) NOT NULL DEFAULT '0' COMMENT '文档id',
  `sharp_goods_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '秒杀商品ID',
  `sales_actual` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '实际销量',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='整点秒杀-活动会场与商品关联表';


-- -----------------------------
-- Table structure for `ey_sharp_active_time`
-- -----------------------------
DROP TABLE IF EXISTS `ey_sharp_active_time`;
CREATE TABLE `ey_sharp_active_time` (
  `active_time_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '场次ID',
  `active_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '活动会场ID',
  `active_time` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '场次时间(0点-23点)',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '活动状态(0禁用 1启用)',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`active_time_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='整点秒杀-活动会场场次表';


-- -----------------------------
-- Table structure for `ey_sharp_goods`
-- -----------------------------
DROP TABLE IF EXISTS `ey_sharp_goods`;
CREATE TABLE `ey_sharp_goods` (
  `sharp_goods_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '秒杀商品ID',
  `aid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '商品ID->aid',
  `limit` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '限购数量',
  `seckill_stock` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '秒杀商品库存总量',
  `seckill_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '秒杀价格',
  `sales` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '累积销量',
  `virtual_sales` tinyint(3) NOT NULL DEFAULT '0' COMMENT '虚拟销量',
  `sort_order` int(11) unsigned NOT NULL DEFAULT '100' COMMENT '商品排序(数字越小越靠前)',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '商品状态(0下架 1上架)',
  `is_del` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_sku` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-多规格商品',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`sharp_goods_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='整点秒杀-商品表';


-- -----------------------------
-- Table structure for `ey_sharp_setting`
-- -----------------------------
DROP TABLE IF EXISTS `ey_sharp_setting`;
CREATE TABLE `ey_sharp_setting` (
  `key` varchar(30) NOT NULL DEFAULT '' COMMENT '设置项标示',
  `describe` varchar(255) NOT NULL DEFAULT '' COMMENT '设置项描述',
  `values` mediumtext NOT NULL COMMENT '设置内容(json格式)',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  UNIQUE KEY `unique_key` (`key`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='整点秒杀设置表';


-- -----------------------------
-- Table structure for `ey_shop_address`
-- -----------------------------
DROP TABLE IF EXISTS `ey_shop_address`;
CREATE TABLE `ey_shop_address` (
  `addr_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '地址id',
  `users_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `consignee` varchar(60) NOT NULL DEFAULT '' COMMENT '收货人',
  `country` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '国家',
  `province` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '省份',
  `city` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '城市',
  `district` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '县区',
  `address` varchar(500) NOT NULL DEFAULT '' COMMENT '详细地址',
  `zipcode` varchar(10) NOT NULL DEFAULT '' COMMENT '邮政编码',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机',
  `is_default` tinyint(1) DEFAULT '0' COMMENT '是否默认，0否，1是。',
  `lang` varchar(30) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`addr_id`),
  KEY `users_id` (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='收货地址表';


-- -----------------------------
-- Table structure for `ey_shop_cart`
-- -----------------------------
DROP TABLE IF EXISTS `ey_shop_cart`;
CREATE TABLE `ey_shop_cart` (
  `cart_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '购物车表',
  `users_id` int(10) unsigned DEFAULT '0' COMMENT '会员id',
  `product_id` int(10) unsigned DEFAULT '0' COMMENT '产品id',
  `product_num` int(10) unsigned DEFAULT '0' COMMENT '购买数量',
  `spec_value_id` varchar(100) DEFAULT '' COMMENT '规格值ID',
  `selected` tinyint(1) DEFAULT '1' COMMENT '购物车选中状态：0未选中，1选中',
  `lang` varchar(30) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '加入购物车的时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`cart_id`),
  KEY `users_id` (`users_id`,`product_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='购物车表';


-- -----------------------------
-- Table structure for `ey_shop_coupon`
-- -----------------------------
DROP TABLE IF EXISTS `ey_shop_coupon`;
CREATE TABLE `ey_shop_coupon` (
  `coupon_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `coupon_code` varchar(100) NOT NULL DEFAULT '' COMMENT '优惠券编号',
  `coupon_name` varchar(100) NOT NULL DEFAULT '' COMMENT '优惠券名称',
  `coupon_color` varchar(25) NOT NULL DEFAULT '' COMMENT '优惠券颜色',
  `coupon_form` tinyint(1) NOT NULL DEFAULT '1' COMMENT '优惠券类型 1-满减券',
  `coupon_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '可使用商品(1全站通用，2指定商品，3指定商品分类)',
  `product_id` varchar(255) NOT NULL DEFAULT '' COMMENT '指定商品ID，在coupon_type=2时使用',
  `arctype_id` varchar(255) NOT NULL DEFAULT '' COMMENT '指定商品分类ID，在coupon_type=3时使用',
  `coupon_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠券金额，例如10',
  `conditions_use` varchar(10) NOT NULL DEFAULT '' COMMENT '优惠券使用条件，例如300',
  `coupon_stock` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券库存，例如100',
  `redeem_points` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '兑换优惠券所需积分，为0则表示免费兑换',
  `redeem_authority` varchar(10) NOT NULL DEFAULT '' COMMENT '兑换权限，存入多个会员等级组ID',
  `valid_days` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '有效天数，例如30',
  `start_date` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券开放领取时间',
  `end_date` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券结束领取时间',
  `sort_order` int(11) unsigned NOT NULL DEFAULT '100' COMMENT '规格排序号',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '优惠券状态(0=关闭，1=开启)',
  `use_type` int(1) NOT NULL DEFAULT '1' COMMENT '使用期限 \r\n1-固定日期\r\n 2-领取后当天开始N(valid_days)天内有效\r\n 2-领取后次日开始N(valid_days)天内有效',
  `use_start_time` int(11) NOT NULL COMMENT '使用期限开始时间',
  `use_end_time` int(11) NOT NULL COMMENT '使用期限结束时间',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '0-未删除 1-已删除',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`coupon_id`),
  KEY `product_id` (`product_id`) USING BTREE,
  KEY `arctype_id` (`arctype_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='优惠券主表';


-- -----------------------------
-- Table structure for `ey_shop_coupon_use`
-- -----------------------------
DROP TABLE IF EXISTS `ey_shop_coupon_use`;
CREATE TABLE `ey_shop_coupon_use` (
  `use_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `users_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `coupon_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券ID',
  `coupon_code` varchar(100) NOT NULL DEFAULT '' COMMENT '优惠券编号',
  `get_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '领取时的IP地址',
  `get_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券领取时的时间',
  `use_status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '优惠券使用状态(0未使用，1已使用，2已过期，3已冻结)',
  `use_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券使用时的时间',
  `start_time` int(10) NOT NULL DEFAULT '0' COMMENT '优惠券有效开始时间',
  `end_time` int(10) NOT NULL DEFAULT '0' COMMENT '优惠券有效结束时间',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`use_id`),
  KEY `coupon_id` (`coupon_id`) USING BTREE,
  KEY `coupon_code` (`coupon_code`) USING BTREE,
  KEY `users_id` (`users_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='优惠券-领取记录表';


-- -----------------------------
-- Table structure for `ey_shop_express`
-- -----------------------------
DROP TABLE IF EXISTS `ey_shop_express`;
CREATE TABLE `ey_shop_express` (
  `express_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `express_code` varchar(32) NOT NULL DEFAULT '' COMMENT '物流code',
  `express_name` varchar(32) NOT NULL DEFAULT '' COMMENT '物流名称',
  `express_lnitials` varchar(5) DEFAULT '' COMMENT '首字母',
  `sort_order` int(10) DEFAULT '0' COMMENT '排序号',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`express_id`)
) ENGINE=MyISAM AUTO_INCREMENT=596 DEFAULT CHARSET=utf8 COMMENT='快递公司表';

-- -----------------------------
-- Records of `ey_shop_express`
-- -----------------------------
INSERT INTO `ey_shop_express` VALUES ('1', 'yuantong', '圆通快递', 'Y', '97', '1553911076', '1554974797');
INSERT INTO `ey_shop_express` VALUES ('2', 'shentong', '申通快递', 'S', '98', '1553911076', '1554974707');
INSERT INTO `ey_shop_express` VALUES ('3', 'shunfeng', '顺丰快递', 'S', '98', '1553911076', '1554974710');
INSERT INTO `ey_shop_express` VALUES ('4', 'yunda', '韵达快递', 'Y', '99', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('5', 'debangwuliu', '德邦快递', 'D', '99', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('6', 'zhongtong', '中通快递', 'Z', '99', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('7', 'huitongkuaidi', '百世快递', 'B', '99', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('8', 'youzhengguonei', '邮政包裹', 'Y', '99', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('9', 'ems', 'EMS', 'E', '99', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('10', 'youzhengguoji', '邮政国际', 'Y', '99', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('11', 'aolau', 'AOL澳通速递', 'A', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('12', 'a2u', 'A2U速递', 'A', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('13', 'aae', 'AAE快递', 'A', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('14', 'annengwuliu', '安能物流', 'A', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('15', 'anxl', '安迅物流', 'A', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('16', 'auexpress', '澳邮中国快运', 'A', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('17', 'exfresh', '安鲜达', 'A', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('18', 'anjie88', '安捷物流', 'A', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('19', 'adodoxm', '澳多多国际速递', 'A', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('20', 'ariesfar', '艾瑞斯远', 'A', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('21', 'qdants', 'ANTS EXPRESS', 'A', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('22', 'astexpress', '安世通快递', 'A', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('23', 'gda', '安的快递', 'A', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('24', 'ausexpress', '澳世速递', 'A', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('25', 'ibuy8', '爱拜物流', 'A', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('26', 'aplusex', 'Aplus物流', 'A', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('27', 'adapost', '安达速递', 'A', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('28', 'adiexpress', '安达易国际速递', 'A', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('29', 'maxeedexpress', '澳洲迈速快递', 'A', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('30', 'onway', '昂威物流', 'A', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('31', 'bcwelt', 'BCWELT', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('32', 'balunzhi', '巴伦支快递', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('33', 'xiaohongmao', '北青小红帽', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('34', 'bfdf', '百福东方物流', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('35', 'bangsongwuliu', '邦送物流', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('36', 'lbbk', '宝凯物流', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('37', 'bqcwl', '百千诚物流', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('38', 'idada', '百成大达物流', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('39', 'baishiwuliu', '百世快运', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('40', 'baitengwuliu', '百腾物流', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('41', 'birdex', '笨鸟海淘', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('42', 'bsht', '百事亨通', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('43', 'benteng', '奔腾物流', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('44', 'cuckooexpess', '布谷鸟速递', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('45', 'bgky100', '邦工快运', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('46', 'bosind', '堡昕德速递', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('47', 'banma', '斑马物联网', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('48', 'polarisexpress', '北极星快运', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('49', 'beijingfengyue', '北京丰越供应链', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('50', 'europe8', '败欧洲', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('51', 'bmlchina', '标杆物流', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('52', 'comexpress', '邦通国际', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('53', 'baotongkd', '宝通快递', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('54', 'beckygo', '佰麒快递', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('55', 'boyol', '贝业物流', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('56', 'bdatong', '八达通快递', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('57', 'bangbangpost', '帮帮发', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('58', 'baoxianda', '报通快递', 'B', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('59', 'coe', '中国东方(COE)', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('60', 'cloudexpress', 'CE易欧通国际速递', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('61', 'city100', '城市100', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('62', 'chuanxiwuliu', '传喜物流', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('63', 'chengjisudi', '城际速递', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('64', 'lijisong', '立即送', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('65', 'chukou1', '出口易', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('66', 'nanjingshengbang', '晟邦物流', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('67', 'flyway', '程光快递', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('68', 'cbo56', '钏博物流', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('69', 'cex', '城铁速递', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('70', 'cnup', 'CNUP 中联邮', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('71', 'clsp', 'CL日中速运', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('72', 'cnair', 'CNAIR', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('73', 'cangspeed', '仓鼠快递', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('74', 'spring56', '春风物流', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('75', 'cunto', '村通快递', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('76', 'longvast', '长风物流', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('77', 'changjiang', '长江国际速递', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('78', 'cncexp', 'C&C国际速递', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('79', 'parcelchina', '诚一物流', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('80', 'chengtong', '城通物流', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('81', 'otpexpress', '承诺达', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('82', 'sfpost', '曹操到', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('83', 'changwooair', '昌宇国际', 'C', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('84', 'dhl', 'DHL快递（中国件）', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('85', 'dhlen', 'DHL（国际件）', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('86', 'dhlde', 'DHL（德国件）', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('87', 'dtwl', '大田物流', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('88', 'disifang', '递四方', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('89', 'dayangwuliu', '大洋物流', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('90', 'dechuangwuliu', '德创物流', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('91', 'dskd', 'D速物流', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('92', 'donghanwl', '东瀚物流', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('93', 'dfpost', '达方物流', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('94', 'dongjun', '东骏快捷物流', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('95', 'dindon', '叮咚澳洲转运', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('96', 'dazhong', '大众佐川急便', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('97', 'ahdf', '德方物流', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('98', 'dehaoyi', '德豪驿', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('99', 'dhlpaket', 'DHL Paket', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('100', 'ubuy', '德国优拜物流', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('101', 'adlerlogi', '德国雄鹰速递', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('102', 'yunexpress', '德国云快递', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('103', 'di5pll', '递五方云仓', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('104', 'deguo8elog', '德国八易转运', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('105', 'camekong', '到了港', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('106', 'dbstation', 'db-station', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('107', 'dadaoex', '大道物流', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('108', 'dekuncn', '德坤物流', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('109', 'twkd56', '缔惠盛合', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('110', 'gslexpress', '德尚国际速递', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('111', 'eucpost', '德国 EUC POST', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('112', 'est365', '东方汇', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('113', 'ecotransite', '东西E全运', 'D', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('114', 'euexpress', 'EU-EXPRESS', 'E', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('115', 'emsguoji', 'EMS国际快递查询', 'E', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('116', 'eshunda', '俄顺达', 'E', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('117', 'ewe', 'EWE全球快递', 'E', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('118', 'easyexpress', 'EASYEXPRESS国际速递', 'E', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('119', 'edtexpress', 'e直运', 'E', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('120', 'ecallturn', 'E跨通', 'E', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('121', 'fedex', 'FedEx快递查询', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('122', 'fedexus', 'FedEx（美国）', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('123', 'fox', 'FOX国际速递', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('124', 'rufengda', '如风达快递', 'R', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('125', 'fkd', '飞康达物流', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('126', 'feibaokuaidi', '飞豹快递', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('127', 'fandaguoji', '颿达国际', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('128', 'feiyuanvipshop', '飞远配送', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('129', 'hnfy', '飞鹰物流', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('130', 'fengxingtianxia', '风行天下', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('131', 'flysman', '飞力士物流', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('132', 'fbkd', '飞邦快递', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('133', 'sccod', '丰程物流', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('134', 'crazyexpress', '疯狂快递', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('135', 'ftlexpress', '法翔速运', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('136', 'ftd', '富腾达快递', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('137', 'arkexpress', '方舟国际速递', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('138', 'fedroad', 'FedRoad 联邦转运', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('139', 'freakyquick', 'FQ狂派速递', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('140', 'fecobv', '丰客物流', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('141', 'fyex', '飞云快递系统', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('142', 'beebird', '锋鸟物流', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('143', 'shipgce', '飞洋快递', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('144', 'koali', '番薯国际货运', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('145', 'epanex', '泛捷国际速递', 'F', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('146', 'gaticn', 'GATI快递', 'G', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('147', 'gts', 'GTS快递', 'G', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('148', 'guotongkuaidi', '国通快递', 'G', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('149', 'ndkd', '能达速递', 'N', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('150', 'gongsuda', '共速达', 'G', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('151', 'gtongsudi', '广通速递（山东）', 'G', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('152', 'suteng', '速腾物流', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('153', 'gdkd', '港快速递', 'G', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('154', 'hre', '高铁速递', 'G', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('155', 'gscq365', '哥士传奇速递', 'G', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('156', 'gjwl', '冠捷物流', 'G', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('157', 'xdshipping', '国晶物流', 'G', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('158', 'ge2d', 'GE2D跨境物流', 'G', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('159', 'gaotieex', '高铁快运', 'G', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('160', 'gansuandi', '甘肃安的快递', 'G', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('161', 'gdct56', '广东诚通物流', 'G', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('162', 'ghtexpress', 'GHT物流', 'G', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('163', 'goldjet', '高捷快运', 'G', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('164', 'gtgogo', 'GT国际快运', 'G', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('165', 'gxwl', '光线速递', 'G', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('166', 'tdhy', '华宇物流', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('167', 'hl', '恒路物流', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('168', 'hlyex', '好来运快递', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('169', 'hebeijianhua', '河北建华', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('170', 'huaqikuaiyun', '华企快运', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('171', 'haosheng', '昊盛物流', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('172', 'hutongwuliu', '户通物流', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('173', 'hzpl', '华航快递', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('174', 'huangmajia', '黄马甲快递', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('175', 'ucs', '合众速递（UCS）', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('176', 'pfcexpress', '皇家物流', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('177', 'huoban', '伙伴物流', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('178', 'nedahm', '红马速递', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('179', 'huiwen', '汇文配送', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('180', 'nmhuahe', '华赫物流', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('181', 'hjs', '猴急送', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('182', 'hangyu', '航宇快递', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('183', 'huilian', '辉联物流', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('184', 'huanqiu', '环球速运', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('185', 'htwd', '华通务达物流', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('186', 'hipito', '海派通', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('187', 'hqtd', '环球通达', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('188', 'airgtc', '航空快递', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('189', 'haoyoukuai', '好又快物流', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('190', 'ccd', '河南次晨达', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('191', 'hfwuxi', '和丰同城', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('192', 'higo', '黑狗物流', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('193', 'hyytes', '恒宇运通', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('194', 'hengrui56', '恒瑞物流', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('195', 'hangrui', '上海航瑞货运', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('196', 'ghl', '环创物流', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('197', 'hnqst', '河南全速通', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('198', 'hitaoe', 'Hi淘易快递', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('199', 'hhair56', '华瀚快递', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('200', 'haimibuy', '海米派物流', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('201', 'ht22', '海淘物流', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('202', 'hivewms', '海沧无忧', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('203', 'hnht56', '鸿泰物流', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('204', 'hsgtsd', '海硕高铁速递', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('205', 'hltop', '海联快递', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('206', 'hlkytj', '互联快运', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('207', 'haidaibao', '海带宝转运', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('208', 'flowerkd', '花瓣转运', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('209', 'heimao56', '黑猫速运', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('210', 'logistics', '華信物流WTO', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('211', 'hgy56', '环国运物流', 'H', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('212', 'iparcel', 'i-parcel', 'I', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('213', 'jjwl', '佳吉物流', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('214', 'jywl', '佳怡物流', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('215', 'jymwl', '加运美快递', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('216', 'jxd', '急先达物流', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('217', 'jgsd', '京广速递快件', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('218', 'jykd', '晋越快递', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('219', 'jd', '京东物流', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('220', 'jietekuaidi', '捷特快递', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('221', 'jiuyicn', '久易快递', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('222', 'jiuyescm', '九曳供应链', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('223', 'junfengguoji', '骏丰国际速递', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('224', 'jiajiatong56', '佳家通', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('225', 'jrypex', '吉日优派', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('226', 'jinchengwuliu', '锦程国际物流', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('227', 'jgwl', '景光物流', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('228', 'pzhjst', '急顺通', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('229', 'ruexp', '捷网俄全通', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('230', 'jialidatong', '嘉里大通', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('231', 'jmjss', '金马甲', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('232', 'jiacheng', '佳成快递', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('233', 'jsexpress', '骏绅物流', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('234', 'hrex', '锦程快递', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('235', 'jieanda', '捷安达国际速递', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('236', 'newsway', '家家通快递', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('237', 'mapleexpress', '今枫国际快运', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('238', 'jixiangyouau', '吉祥邮（澳洲）', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('239', 'jjx888', '佳捷翔物流', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('240', 'polarexpress', '极地快递', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('241', 'jiazhoumao', '加州猫速递', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('242', 'juzhongda', '聚中大', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('243', 'jieborne', '捷邦物流', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('244', 'jxfex', '集先锋速递', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('245', 'jiugong', '九宫物流', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('246', 'jiujiuwl', '久久物流', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('247', 'jintongkd', '劲通快递', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('248', 'jcsuda', '嘉诚速达', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('249', 'jingshun', '景顺物流', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('250', 'fastontime', '加拿大联通快运', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('251', 'khzto', '柬埔寨中通', 'J', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('252', 'kjkd', '快捷快递', 'K', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('253', 'kangliwuliu', '康力物流', 'K', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('254', 'kuayue', '跨越速运', 'K', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('255', 'kuaiyouda', '快优达速递', 'K', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('256', 'happylink', '开心快递', 'K', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('257', 'ksudi', '快速递', 'K', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('258', 'kyue', '跨跃国际', 'K', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('259', 'kfwnet', '快服务', 'K', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('260', 'kuai8', '快8速运', 'K', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('261', 'kuaidawuliu', '快达物流', 'K', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('262', 'lianb', '联邦快递（国内）', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('263', 'lhtwl', '联昊通物流', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('264', 'lb', '龙邦速递', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('265', 'lejiedi', '乐捷递', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('266', 'lanhukuaidi', '蓝弧快递', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('267', 'ltexp', '乐天速递', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('268', 'lutong', '鲁通快运', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('269', 'ledii', '乐递供应链', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('270', 'lundao', '论道国际物流', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('271', 'lasy56', '林安物流', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('272', 'lsexpress', '6LS EXPRESS', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('273', 'szuem', '联运通物流', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('274', 'blueskyexpress', '蓝天国际航空快递', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('275', 'lfexpress', '龙枫国际速递', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('276', 'gslhkd', '联合快递', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('277', 'longfx', '龙飞祥快递', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('278', 'luben', '陆本速递 LUBEN EXPRESS', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('279', 'unitedex', '联合速运', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('280', 'lbex', '龙邦物流', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('281', 'ltparcel', '联通快递', 'L', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('282', 'macroexpressco', 'ME物流', 'M', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('283', 'mh', '民航快递', 'M', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('284', 'meiguokuaidi', '美国快递', 'M', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('285', 'menduimen', '门对门', 'M', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('286', 'mingliangwuliu', '明亮物流', 'M', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('287', 'minbangsudi', '民邦速递', 'M', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('288', 'minshengkuaidi', '闽盛快递', 'M', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('289', 'yundaexus', '美国韵达', 'M', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('290', 'mchy', '木春货运', 'M', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('291', 'meiquick', '美快国际物流', 'M', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('292', 'valueway', '美通快递', 'M', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('293', 'cnmcpl', '马珂博逻', 'M', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('294', 'mailongdy', '迈隆递运', 'M', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('295', 'zsmhwl', '明辉物流', 'M', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('296', 'mosuda', '魔速达', 'M', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('297', 'meibang', '美邦国际快递', 'M', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('298', 'nuoyaao', '偌亚奥国际', 'N', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('299', 'nuoer', '诺尔国际物流', 'N', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('300', 'nell', '尼尔快递', 'N', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('301', 'ndwl', '南方传媒物流', 'N', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('302', 'canhold', '能装能送', 'N', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('303', 'wanjiatong', '宁夏万家通', 'N', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('304', 'nlebv', '欧亚专线', 'O', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('305', 'oborexpress', 'OBOR Express', 'O', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('306', 'pcaexpress', 'PCA Express', 'P', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('307', 'pingandatengfei', '平安达腾飞', 'P', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('308', 'peixingwuliu', '陪行物流', 'P', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('309', 'pengyuanexpress', '鹏远国际速递', 'P', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('310', 'postelbe', 'PostElbe', 'P', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('311', 'papascm', '啪啪供应链', 'P', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('312', 'bazirim', '皮牙子快递', 'P', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('313', 'qfkd', '全峰快递', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('314', 'qy', '全一快递', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('315', 'qrt', '全日通快递', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('316', 'qckd', '全晨快递', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('317', 'sevendays', '7天连锁物流', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('318', 'qbexpress', '秦邦快运', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('319', 'quanxintong', '全信通快递', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('320', 'quansutong', '全速通国际快递', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('321', 'qinyuan', '秦远物流', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('322', 'qichen', '启辰国际物流', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('323', 'quansu', '全速快运', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('324', 'qzx56', '全之鑫物流', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('325', 'qskdyxgs', '千顺快递', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('326', 'zqlwl', '青旅物流', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('327', 'quanchuan56', '全川物流', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('328', 'quantwl', '全通快运', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('329', 'yatexpress', '乾坤物流', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('330', 'guexp', '全联速运', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('331', 'bjqywl', '青云物流', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('332', 'signedexpress', '签收快递', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('333', 'express7th', '7号速递', 'Q', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('334', 'riyuwuliu', '日昱物流', 'R', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('335', 'rfsd', '瑞丰速递', 'R', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('336', 'rrs', '日日顺物流', 'R', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('337', 'rytsd', '日益通速递', 'R', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('338', 'rrskx', '日日顺快线', 'R', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('339', 'gdrz58', '容智快运', 'R', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('340', 'rrthk', '日日通国际', 'R', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('341', 'homecourier', '如家国际快递', 'R', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('342', 'sewl', '速尔快递', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('343', 'haihongwangsong', '山东海红', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('344', 'sh', '盛辉物流', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('345', 'sfwl', '盛丰物流', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('346', 'shiyunkuaidi', '世运快递', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('347', 'shangda', '上大物流', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('348', 'stsd', '三态速递', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('349', 'saiaodi', '赛澳递', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('350', 'ewl', '申通E物流', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('351', 'shenganwuliu', '圣安物流', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('352', 'sxhongmajia', '山西红马甲', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('353', 'suijiawuliu', '穗佳物流', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('354', 'syjiahuier', '沈阳佳惠尔', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('355', 'shlindao', '上海林道货运', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('356', 'sfift', '十方通物流', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('357', 'shunjiefengda', '顺捷丰达', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('358', 'subida', '速必达物流', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('359', 'stcd', '速通成达物流', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('360', 'stkd', '顺通快递', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('361', 'sendtochina', '速递中国', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('362', 'sihaiet', '四海快递', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('363', 'staky', '首通快运', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('364', 'hnssd56', '顺时达物流', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('365', 'superb', 'Superb Grace', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('366', 'sfjhd', '圣飞捷快递', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('367', 'sofast56', '嗖一下同城快递', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('368', 's2c', 'S2C', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('369', 'chinasqk', 'SQK国际速递', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('370', 'shunshid', '顺士达速运', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('371', 'synship', 'SYNSHIP快递', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('372', 'shandiantu', '闪电兔', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('373', 'sdsy888', '首达速运', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('374', 'sczpds', '速呈宅配', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('375', 'sureline', 'Sureline冠泰', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('376', 'stosolution', '申通国际', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('377', 'sycawl', '狮爱高铁物流', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('378', 'sxexpress', '三象速递', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('379', 'shangqiao56', '商桥物流', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('380', 'shd56', '商海德物流', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('381', 'shenma', '神马快递', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('382', 'sihiexpress', '四海捷运', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('383', 'superoz', '速配鸥翼', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('384', 'fastgoexpress', '速派快递', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('385', 'zjstky', '苏通快运', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('386', 'suning', '苏宁物流', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('387', 'shaoke', '捎客物流', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('388', 'sdto', '速达通跨境物流', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('389', 'sut56', '速通物流', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('390', 'sundarexpress', '顺达快递', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('391', 'sxjdfreight', '顺心捷达', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('392', 'shengtongscm', '盛通快递', 'S', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('393', 'tnt', 'TNT快递', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('394', 'tt', '天天快递', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('395', 'tianzong', '天纵物流', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('396', 'chinatzx', '同舟行物流', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('397', 'nntengda', '腾达速递', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('398', 'sd138', '泰国138', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('399', 'tongdaxing', '通达兴物流', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('400', 'tlky', '天联快运', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('401', 'ibenben', '途鲜物流', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('402', 'krtao', '淘韩国际快递', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('403', 'lntjs', '特急送', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('404', 'tny', 'TNY物流', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('405', 'djy56', '天翔东捷运', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('406', 'guoeryue', '天天快物流', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('407', 'tianma', '天马迅达', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('408', 'surpassgo', '天越物流', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('409', 'tianxiang', '天翔快递', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('410', 'tywl99', '天翼物流', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('411', 'shpost', '同城快寄', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('412', 'humpline', '驼峰国际', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('413', 'transrush', 'TransRush', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('414', 'tstexp', 'TST速运通', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('415', 'ctoexp', '泰国中通CTO', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('416', 'thaizto', '泰国中通ZTO', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('417', 'tswlcloud', '天使物流云', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('418', 'tzky', '铁中快运', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('419', 'tcxbthai', 'TCXB国际物流', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('420', 'taimek', '天美快递', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('421', 'taoplus', '淘布斯国际物流', 'T', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('422', 'ups', 'UPS快递查询', 'U', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('423', 'yskd', '优速快递', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('424', 'usps', 'USPS美国邮政', 'U', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('425', 'ueq', 'UEQ快递', 'U', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('426', 'uex', 'UEX国际物流', 'U', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('427', 'utaoscm', 'UTAO 优到', 'U', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('428', 'wxwl', '万象物流', 'W', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('429', 'weitepai', '微特派', 'W', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('430', 'wjwl', '万家物流', 'W', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('431', 'wanboex', '万博快递', 'W', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('432', 'wtdchina', '威时沛运', 'W', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('433', 'wzhaunyun', '微转运', 'W', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('434', 'gswtkd', '万通快递', 'W', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('435', 'wandougongzhu', '豌豆物流', 'W', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('436', 'wjkwl', '万家康物流', 'W', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('437', 'vps', '维普恩物流', 'W', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('438', 'wykjt', '51跨境通', 'W', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('439', 'wherexpess', '威盛快递', 'W', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('440', 'weilaimingtian', '未来明天快递', 'W', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('441', 'wdm', '万达美', 'W', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('442', 'wto56kj', '温通物流', 'W', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('443', '56kuaiyun', '五六快运', 'W', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('444', 'wowvip', '沃埃家', 'W', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('445', 'grivertek', '潍鸿', 'W', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('446', 'xbwl', '新邦物流', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('447', 'xfwl', '信丰物流', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('448', 'newegg', '新蛋物流', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('449', 'xianglongyuntong', '祥龙运通物流', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('450', 'xianchengliansudi', '西安城联速递', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('451', 'xilaikd', '喜来快递', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('452', 'xsrd', '鑫世锐达', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('453', 'xtb', '鑫通宝物流', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('454', 'xintianjie', '信天捷快递', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('455', 'xaetc', '西安胜峰', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('456', 'xianfeng', '先锋快递', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('457', 'sunspeedy', '新速航', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('458', 'xipost', '西邮寄', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('459', 'sinatone', '信联通', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('460', 'sunjex', '新杰物流', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('461', 'alog', '心怡物流', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('462', 'csxss', '新时速物流', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('463', 'xiangteng', '翔腾物流', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('464', 'westwing', '西翼物流', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('465', 'littlebearbear', '小熊物流', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('466', 'huanqiuabc', '中国香港环球快运', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('467', 'xinning', '新宁物流', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('468', 'wlwex', '星空国际', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('469', 'yyexp', '西安运逸快递', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('470', 'xiyoug', '西游寄', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('471', 'xlobo', 'xLobo', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('472', 'xunsuexpress', '迅速快递', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('473', 'whgjkd', '香港伟豪国际物流', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('474', 'xyd666', '鑫远东速运', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('475', 'xdexpress', '迅达速递', 'X', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('476', 'ytkd', '运通快递', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('477', 'ycwl', '远成物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('478', 'yfsd', '亚风速递', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('479', 'yishunhang', '亿顺航', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('480', 'yfwl', '越丰物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('481', 'yad', '源安达快递', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('482', 'yfh', '原飞航物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('483', 'yinjiesudi', '银捷速递', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('484', 'yitongfeihong', '一统飞鸿', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('485', 'yuxinwuliu', '宇鑫物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('486', 'yitongda', '易通达', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('487', 'youbijia', '邮必佳', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('488', 'yiqiguojiwuliu', '一柒物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('489', 'yinsu', '音素快运', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('490', 'yilingsuyun', '亿领速运', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('491', 'yujiawuliu', '煜嘉物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('492', 'gml', '英脉物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('493', 'leopard', '云豹国际货运', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('494', 'czwlyn', '云南中诚', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('495', 'sdyoupei', '优配速运', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('496', 'yongchang', '永昌物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('497', 'yufeng', '御风速运', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('498', 'yousutongda', '优速通达', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('499', 'yongwangda', '永旺达快递', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('500', 'yingchao', '英超物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('501', 'edlogistics', '益递物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('502', 'yjxlm', '宜家行', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('503', 'onehcang', '一号仓', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('504', 'ycgky', '远成快运', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('505', 'yunfeng56', '韵丰物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('506', 'iyoungspeed', '驿扬国际速运', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('507', 'zgyzt', '一站通快递', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('508', 'eupackage', '易优包裹', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('509', 'ydglobe', '云达通', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('510', 'el56', 'YLTD', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('511', 'yundx', '运东西', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('512', 'yangbaoguo', '洋包裹', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('513', 'uluckex', '优联吉运', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('514', 'ecmscn', '易客满', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('515', 'ubonex', '优邦速运', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('516', 'yue777', '玥玛速运', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('517', 'ywexpress', '远为快递', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('518', 'ezhuanyuan', '易转运', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('519', 'yiqisong', '一起送', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('520', 'yongbangwuliu', '永邦国际物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('521', 'yyox', '邮客全球速递', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('522', 'yihangmall', '易航物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('523', 'yiouzhou', '易欧洲国际物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('524', 'ykouan', '洋口岸', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('525', 'youyou', '优优速递', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('526', 'ytky168', '运通快运', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('527', 'sixroad', '易普递', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('528', 'yourscm', '雅澳物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('529', 'euguoji', '易邮国际', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('530', 'uscbexpress', '易境达国际物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('531', 'yfsuyun', '驭丰速运', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('532', 'yimidida', '壹米滴答', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('533', 'ugoexpress', '邮鸽速运', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('534', 'youban', '邮邦国际', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('535', 'hkems', '云邮跨境快递', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('536', 'youlai', '邮来速递', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('537', 'eta100', '易达国际速递', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('538', 'yatfai', '一辉物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('539', 'yzswuliu', '亚洲顺物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('540', 'yifankd', '艺凡快递', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('541', 'mantoo', '优能物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('542', 'vctrans', '越中国际物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('543', 'yhtlogistics', '宇航通物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('544', 'ycgglobal', 'YCG物流', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('545', 'yidihui', '驿递汇速递', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('546', 'yuanhhk', '远航国际快运', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('547', 'yiyou', '易邮速运', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('548', 'eusacn', '优莎速运', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('549', 'uhi', '优海国际速递', 'Y', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('550', 'zjs', '宅急送', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('551', 'ztky', '中铁快运', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('552', 'ztwl', '中铁物流', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('553', 'zywl', '中邮物流', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('554', 'zhimakaimen', '芝麻开门', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('555', 'zhengzhoujianhua', '郑州建华', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('556', 'zhongsukuaidi', '中速快件', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('557', 'zhongtianwanyun', '中天万运', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('558', 'zhongruisudi', '中睿速递', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('559', 'zhongwaiyun', '中外运速递', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('560', 'zengyisudi', '增益速递', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('561', 'sujievip', '郑州速捷', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('562', 'ztong', '智通物流', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('563', 'zhichengtongda', '至诚通达快递', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('564', 'zhdwl', '众辉达物流', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('565', 'kuachangwuliu', '直邮易', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('566', 'topspeedex', '中运全速', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('567', 'otobv', '中欧快运', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('568', 'zsky123', '准实快运', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('569', 'cnws', '中国翼', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('570', 'zytdscm', '中宇天地', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('571', 'zhuanyunsifang', '转运四方', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('572', 'hrbzykd', '卓烨快递', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('573', 'zhuoshikuaiyun', '卓实快运', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('574', 'chinaicip', '卓志速运', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('575', 'ynztsy', '纵通速运', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('576', 'zdepost', '直德邮', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('577', 'chinapostcb', '中邮电商', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('578', 'chunghwa56', '中骅物流', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('579', 'cosco', '中远e环球', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('580', 'zf365', '珠峰速运', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('581', 'zhongtongkuaiyun', '中通快运', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('582', 'eucnrail', '中欧国际物流', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('583', 'chnexp', '中翼国际物流', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('584', 'cccc58', '中集冷云', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('585', 'auvanda', '中联速递', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('586', 'zyzoom', '增速跨境', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('587', 'zhpex', '众派速递', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('588', 'byht', '展勤快递', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('589', 'zhongchuan', '众川国际', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('590', 'zhonghuanus', '中环转运', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('591', 'zhonghuan', '中环快递', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('592', 'uszcn', '转运中国', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('593', 'zhitengwuliu', '志腾物流', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('594', 'zsda56', '转瞬达集运', 'Z', '100', '1553911076', '1553911076');
INSERT INTO `ey_shop_express` VALUES ('595', 'zjgj56', '振捷国际货运', 'Z', '100', '1553911076', '1553911076');

-- -----------------------------
-- Table structure for `ey_shop_order`
-- -----------------------------
DROP TABLE IF EXISTS `ey_shop_order`;
CREATE TABLE `ey_shop_order` (
  `order_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单id',
  `order_code` varchar(30) NOT NULL DEFAULT '' COMMENT '订单编号',
  `users_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `order_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单状态：0未付款(已下单)，1已付款(待发货)，2已发货(待收货)，3已完成(确认收货)，-1订单取消(已关闭)，4订单过期',
  `payment_method` tinyint(1) DEFAULT '0' COMMENT '订单支付方式，0为在线支付，1为货到付款，默认0',
  `pay_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间',
  `pay_name` varchar(20) NOT NULL DEFAULT '' COMMENT '支付方式名称',
  `wechat_pay_type` varchar(20) NOT NULL DEFAULT '' COMMENT '微信支付时，标记使用的支付类型（扫码支付，微信内部，微信H5页面）',
  `pay_details` text COMMENT '支付时返回的数据，以serialize序列化后存入，用于后续查询。',
  `express_order` varchar(50) DEFAULT '' COMMENT '发货物流单号',
  `express_name` varchar(32) DEFAULT '' COMMENT '发货物流名称',
  `express_code` varchar(32) DEFAULT '' COMMENT '发货物流code',
  `express_time` int(11) DEFAULT '0' COMMENT '发货时间',
  `consignee` varchar(30) NOT NULL DEFAULT '' COMMENT '收货人',
  `confirm_time` int(11) DEFAULT '0' COMMENT '收货确认时间',
  `shipping_fee` decimal(10,2) DEFAULT '0.00' COMMENT '订单运费',
  `order_total_amount` decimal(10,2) DEFAULT '0.00' COMMENT '订单总价',
  `order_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '应付款金额',
  `order_total_num` int(10) DEFAULT '0' COMMENT '订单总数',
  `country` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '国家',
  `province` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '省份',
  `city` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '城市',
  `district` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '县区',
  `address` varchar(500) NOT NULL DEFAULT '' COMMENT '收货地址',
  `mobile` varchar(20) DEFAULT '' COMMENT '手机',
  `prom_type` tinyint(1) unsigned DEFAULT '0' COMMENT '订单类型：0普通订单，1虚拟订单',
  `virtual_delivery` text COMMENT '虚拟订单时，卖家发货给买家的回复',
  `admin_note` text COMMENT '管理员操作备注',
  `is_comment` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已评论，0为否，1为是，默认0',
  `user_note` text COMMENT '会员备注',
  `group` varchar(50) DEFAULT '' COMMENT '订单分组',
  `order_md5` varchar(50) DEFAULT '' COMMENT '订单标识串，删除未付款的重复订单',
  `order_source` tinyint(3) DEFAULT '10' COMMENT '10-普通订单 20-秒杀订单',
  `order_source_id` int(10) DEFAULT '0' COMMENT '来源id(秒杀订单:active_time_id)',
  `lang` varchar(30) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned DEFAULT '0' COMMENT '下单时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  `coupon_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '优惠券数据表ID',
  `use_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会员的优惠券数据表ID',
  `coupon_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '使用的优惠券金额',
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_code` (`order_code`),
  KEY `users_id` (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订单主表';


-- -----------------------------
-- Table structure for `ey_shop_order_comment`
-- -----------------------------
DROP TABLE IF EXISTS `ey_shop_order_comment`;
CREATE TABLE `ey_shop_order_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `users_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `order_code` varchar(50) NOT NULL DEFAULT '' COMMENT '订单编号',
  `details_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单明细表ID',
  `product_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品ID',
  `total_score` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '总评分，1：好评，2中评，3差评',
  `content` varchar(1000) NOT NULL DEFAULT '' COMMENT '评论内容',
  `upload_img` varchar(3000) NOT NULL DEFAULT '' COMMENT '晒单图片',
  `admin_reply` varchar(1000) NOT NULL DEFAULT '' COMMENT '管理员回复',
  `ip_address` varchar(15) NOT NULL DEFAULT '' COMMENT 'IP地址',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示：0否，1是',
  `is_anonymous` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否匿名评价：0否，1是',
  `lang` varchar(30) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`comment_id`),
  KEY `users_id` (`users_id`),
  KEY `order_id` (`order_id`),
  KEY `details_id` (`details_id`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='商品评价表';


-- -----------------------------
-- Table structure for `ey_shop_order_details`
-- -----------------------------
DROP TABLE IF EXISTS `ey_shop_order_details`;
CREATE TABLE `ey_shop_order_details` (
  `details_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `order_id` int(10) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `users_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `product_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '产品id',
  `product_name` varchar(100) NOT NULL DEFAULT '' COMMENT '产品名称',
  `num` int(10) NOT NULL DEFAULT '0' COMMENT '单个产品数量',
  `data` text COMMENT '序列化额外数据',
  `product_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '产品单价',
  `prom_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '产品类型：0普通产品，1虚拟产品',
  `litpic` varchar(500) NOT NULL DEFAULT '' COMMENT '封面图片',
  `apply_service` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否申请退换货服务：0 未申请、1已申请',
  `is_comment` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已评论，0为否，1为是，默认0',
  `lang` varchar(30) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '下单时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`details_id`),
  KEY `users_id` (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订单详情表';


-- -----------------------------
-- Table structure for `ey_shop_order_log`
-- -----------------------------
DROP TABLE IF EXISTS `ey_shop_order_log`;
CREATE TABLE `ey_shop_order_log` (
  `action_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `users_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `action_user` int(10) DEFAULT '0' COMMENT '操作人；0:用户操作；1以上:管理员id',
  `order_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单状态，单条记录状态',
  `express_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '物流状态，0:未发货，1:已发货',
  `pay_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付状态，0:未支付，1:已支付',
  `action_desc` varchar(255) DEFAULT '' COMMENT '状态描述',
  `action_note` varchar(255) NOT NULL DEFAULT '' COMMENT '操作备注',
  `lang` varchar(30) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作时间',
  PRIMARY KEY (`action_id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订单操作记录表';


-- -----------------------------
-- Table structure for `ey_shop_order_service`
-- -----------------------------
DROP TABLE IF EXISTS `ey_shop_order_service`;
CREATE TABLE `ey_shop_order_service` (
  `service_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `service_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '类型：1换货，2退货，3维修',
  `users_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `order_code` varchar(20) NOT NULL DEFAULT '' COMMENT '订单编号',
  `details_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单明细表ID',
  `product_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '产品ID',
  `product_name` varchar(200) NOT NULL DEFAULT '' COMMENT '产品名称',
  `product_spec` varchar(200) NOT NULL DEFAULT '' COMMENT '产品规格',
  `product_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '产品数量',
  `product_img` varchar(500) NOT NULL DEFAULT '' COMMENT '产品图片',
  `content` varchar(500) NOT NULL DEFAULT '' COMMENT '退换货描述',
  `upload_img` varchar(3000) NOT NULL DEFAULT '' COMMENT '上传的图片',
  `address` varchar(500) NOT NULL DEFAULT '' COMMENT '退货的收货地址',
  `consignee` varchar(30) NOT NULL DEFAULT '' COMMENT '收货人',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机',
  `refund_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退还金额',
  `refund_balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退还余额',
  `refund_code` varchar(40) NOT NULL DEFAULT '' COMMENT '退款单号',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1审核中 2审核通过 3审核不通过 4已发货 5已收货 6换货完成 7退款完成 8服务取消',
  `users_delivery` varchar(500) NOT NULL DEFAULT '' COMMENT '会员发货信息',
  `admin_delivery` varchar(500) NOT NULL DEFAULT '' COMMENT '管理员发货信息',
  `admin_note` varchar(1000) NOT NULL DEFAULT '' COMMENT '管理员操作备注',
  `lang` varchar(30) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '申请时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`service_id`),
  KEY `users_id` (`users_id`) USING BTREE,
  KEY `order_id` (`order_id`) USING BTREE,
  KEY `order_code` (`order_code`) USING BTREE,
  KEY `product_id` (`product_id`) USING BTREE,
  KEY `details_id` (`details_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订单退换货服务表';


-- -----------------------------
-- Table structure for `ey_shop_order_service_log`
-- -----------------------------
DROP TABLE IF EXISTS `ey_shop_order_service_log`;
CREATE TABLE `ey_shop_order_service_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `service_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '服务表ID',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单ID',
  `users_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `log_note` varchar(500) NOT NULL DEFAULT '' COMMENT '记录备注',
  `lang` varchar(30) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作时间',
  PRIMARY KEY (`log_id`),
  KEY `service_id` (`service_id`) USING BTREE,
  KEY `order_id` (`order_id`) USING BTREE,
  KEY `users_id` (`users_id`) USING BTREE,
  KEY `admin_id` (`admin_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订单退换货服务记录表';


-- -----------------------------
-- Table structure for `ey_shop_product_attr`
-- -----------------------------
DROP TABLE IF EXISTS `ey_shop_product_attr`;
CREATE TABLE `ey_shop_product_attr` (
  `product_attr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '产品属性id自增',
  `aid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '产品id',
  `attr_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '属性id',
  `attr_value` text NOT NULL COMMENT '属性值',
  `attr_price` varchar(255) DEFAULT '' COMMENT '属性价格',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`product_attr_id`),
  KEY `aid` (`aid`) USING BTREE,
  KEY `attr_id` (`attr_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

-- -----------------------------
-- Records of `ey_shop_product_attr`
-- -----------------------------
INSERT INTO `ey_shop_product_attr` VALUES ('1', '27', '1', 'EMUI 4.1 + Android 6.0', '', '1591262821', '1591262821');
INSERT INTO `ey_shop_product_attr` VALUES ('2', '27', '2', 'EMUI 4.1', '', '1591262821', '1591262821');
INSERT INTO `ey_shop_product_attr` VALUES ('3', '27', '3', '虚拟键盘', '', '1591262821', '1591262821');
INSERT INTO `ey_shop_product_attr` VALUES ('4', '27', '4', 'EDI-AL10', '', '1591262821', '1591262821');
INSERT INTO `ey_shop_product_attr` VALUES ('5', '27', '9', '64GB', '', '1591262821', '1591262821');
INSERT INTO `ey_shop_product_attr` VALUES ('6', '28', '5', '13.3', '', '1591262850', '1591262850');
INSERT INTO `ey_shop_product_attr` VALUES ('7', '28', '6', '3KG', '', '1591262850', '1591262850');
INSERT INTO `ey_shop_product_attr` VALUES ('8', '29', '7', 'AKG&amp;HUAWEI', '', '1591262874', '1591262874');
INSERT INTO `ey_shop_product_attr` VALUES ('9', '29', '8', '支持', '', '1591262874', '1591262874');
INSERT INTO `ey_shop_product_attr` VALUES ('19', '89', '2', 'Apple', '', '1591263033', '1591263033');
INSERT INTO `ey_shop_product_attr` VALUES ('12', '37', '3', '触摸', '', '1591262910', '1591262910');
INSERT INTO `ey_shop_product_attr` VALUES ('18', '89', '1', 'AppleiPhone 8 Plus', '', '1591263033', '1591263033');
INSERT INTO `ey_shop_product_attr` VALUES ('14', '37', '9', '256GB', '', '1591262910', '1591262910');
INSERT INTO `ey_shop_product_attr` VALUES ('15', '37', '1', 'EMUI 9.1 + Android 9.0', '', '1591262982', '1591262982');
INSERT INTO `ey_shop_product_attr` VALUES ('16', '37', '2', 'EMUI 9.1', '', '1591262982', '1591262982');
INSERT INTO `ey_shop_product_attr` VALUES ('17', '37', '4', 'EDI-AL12', '', '1591262982', '1591262982');
INSERT INTO `ey_shop_product_attr` VALUES ('20', '89', '3', '触摸', '', '1591263033', '1591263033');
INSERT INTO `ey_shop_product_attr` VALUES ('21', '89', '4', 'iPhone 8 Plus', '', '1591263033', '1591263033');
INSERT INTO `ey_shop_product_attr` VALUES ('22', '89', '9', '128GB', '', '1591263033', '1591263033');
INSERT INTO `ey_shop_product_attr` VALUES ('23', '90', '1', 'Android', '', '1591263065', '1591263065');
INSERT INTO `ey_shop_product_attr` VALUES ('24', '90', '2', '小米UI', '', '1591263065', '1591263065');
INSERT INTO `ey_shop_product_attr` VALUES ('25', '90', '3', '触摸', '', '1591263065', '1591263065');
INSERT INTO `ey_shop_product_attr` VALUES ('26', '90', '4', '小米8屏幕指纹版', '', '1591263065', '1591263065');
INSERT INTO `ey_shop_product_attr` VALUES ('27', '90', '9', '64GB', '', '1591263065', '1591263065');
INSERT INTO `ey_shop_product_attr` VALUES ('28', '98', '5', '12.2英寸', '', '1591263088', '1591263088');
INSERT INTO `ey_shop_product_attr` VALUES ('29', '98', '6', '1250g', '', '1591263088', '1591263088');
INSERT INTO `ey_shop_product_attr` VALUES ('30', '99', '5', '12.2英寸', '', '1591263111', '1591263111');
INSERT INTO `ey_shop_product_attr` VALUES ('31', '99', '6', '1250g', '', '1591263111', '1591263111');
INSERT INTO `ey_shop_product_attr` VALUES ('32', '100', '5', '14英寸', '', '1591263132', '1591263132');
INSERT INTO `ey_shop_product_attr` VALUES ('33', '100', '6', '1.5kg', '', '1591263132', '1591263132');
INSERT INTO `ey_shop_product_attr` VALUES ('34', '101', '7', 'X1', '', '1591263152', '1591263152');
INSERT INTO `ey_shop_product_attr` VALUES ('35', '101', '8', '支持', '', '1591263152', '1591263152');
INSERT INTO `ey_shop_product_attr` VALUES ('36', '103', '7', 'G1', '', '1610352876', '1610352876');
INSERT INTO `ey_shop_product_attr` VALUES ('37', '103', '8', '支持', '', '1610352876', '1610352876');
INSERT INTO `ey_shop_product_attr` VALUES ('38', '102', '7', 'MINI', '', '1610352910', '1610352910');
INSERT INTO `ey_shop_product_attr` VALUES ('39', '102', '8', '支持', '', '1610352910', '1610352910');

-- -----------------------------
-- Table structure for `ey_shop_product_attribute`
-- -----------------------------
DROP TABLE IF EXISTS `ey_shop_product_attribute`;
CREATE TABLE `ey_shop_product_attribute` (
  `attr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性id',
  `attr_name` varchar(60) NOT NULL DEFAULT '' COMMENT '属性名称',
  `list_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '栏目id',
  `attr_index` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0不需要检索 1关键字检索 2范围检索',
  `attr_input_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT ' 0=文本框，1=下拉框，2=多行文本框',
  `attr_values` text NOT NULL COMMENT '可选值列表',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(0=禁用，1=启用)',
  `sort_order` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '属性排序',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已删除，0=否，1=是',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`attr_id`),
  KEY `cat_id` (`list_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- -----------------------------
-- Records of `ey_shop_product_attribute`
-- -----------------------------
INSERT INTO `ey_shop_product_attribute` VALUES ('1', '操作系统', '1', '0', '0', '', '1', '100', 'cn', '0', '1591262495', '1623813369');
INSERT INTO `ey_shop_product_attribute` VALUES ('2', '用户界面', '1', '0', '0', '', '1', '100', 'cn', '0', '1591262503', '1623813369');
INSERT INTO `ey_shop_product_attribute` VALUES ('3', '键盘类型', '1', '0', '0', '', '1', '100', 'cn', '0', '1591262510', '1623813369');
INSERT INTO `ey_shop_product_attribute` VALUES ('4', '产品型号', '1', '0', '0', '', '1', '100', 'cn', '0', '1591262517', '1623813369');
INSERT INTO `ey_shop_product_attribute` VALUES ('5', '屏幕大小', '2', '0', '0', '', '1', '100', 'cn', '0', '1591262613', '1623813369');
INSERT INTO `ey_shop_product_attribute` VALUES ('6', '整机净重', '2', '0', '0', '', '1', '100', 'cn', '0', '1591262620', '1623813369');
INSERT INTO `ey_shop_product_attribute` VALUES ('7', '产品型号', '3', '0', '0', '', '1', '100', 'cn', '0', '1591262712', '1623813369');
INSERT INTO `ey_shop_product_attribute` VALUES ('8', '支持蓝牙', '3', '0', '0', '', '1', '100', 'cn', '0', '1591262723', '1623813369');
INSERT INTO `ey_shop_product_attribute` VALUES ('9', '机身内存', '1', '0', '1', '64GB\r\n128GB\r\n256GB', '1', '100', 'cn', '0', '1591262771', '1623813369');

-- -----------------------------
-- Table structure for `ey_shop_product_attrlist`
-- -----------------------------
DROP TABLE IF EXISTS `ey_shop_product_attrlist`;
CREATE TABLE `ey_shop_product_attrlist` (
  `list_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '列表id',
  `list_name` varchar(60) NOT NULL DEFAULT '' COMMENT '列表名称',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(0=禁用，1=启用)',
  `attr_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '参数数量',
  `desc` text NOT NULL COMMENT '描述备注',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已删除，0=否，1=是',
  `sort_order` int(11) unsigned NOT NULL DEFAULT '100' COMMENT '列表排序',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`list_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- -----------------------------
-- Records of `ey_shop_product_attrlist`
-- -----------------------------
INSERT INTO `ey_shop_product_attrlist` VALUES ('1', '手机数码', '1', '5', '适用于手机数码栏目', '0', '100', 'cn', '1591262479', '1623813369');
INSERT INTO `ey_shop_product_attrlist` VALUES ('2', '电脑产品', '1', '2', '适用于电脑产品栏目', '0', '100', 'cn', '1591262601', '1623813369');
INSERT INTO `ey_shop_product_attrlist` VALUES ('3', '耳机', '1', '2', '适用于耳机栏目', '0', '100', 'cn', '1591262601', '1623813369');

-- -----------------------------
-- Table structure for `ey_shop_shipping_template`
-- -----------------------------
DROP TABLE IF EXISTS `ey_shop_shipping_template`;
CREATE TABLE `ey_shop_shipping_template` (
  `template_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '运费模板ID',
  `template_region` varchar(255) NOT NULL DEFAULT '' COMMENT '模板运送区域',
  `template_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '模板运费',
  `province_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'region表id',
  `lang` varchar(30) DEFAULT 'cn' COMMENT '语言标识',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`template_id`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='运费模板表';

-- -----------------------------
-- Records of `ey_shop_shipping_template`
-- -----------------------------
INSERT INTO `ey_shop_shipping_template` VALUES ('1', '北京市', '0.00', '1', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('2', '天津市', '0.00', '338', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('3', '河北省', '0.00', '636', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('4', '山西省', '0.00', '3102', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('5', '内蒙古自治区', '0.00', '4670', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('6', '辽宁省', '0.00', '5827', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('7', '吉林省', '0.00', '7531', 'cn', '1554775921');
INSERT INTO `ey_shop_shipping_template` VALUES ('8', '黑龙江省', '0.00', '8558', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('9', '上海市', '0.00', '10543', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('10', '江苏省', '0.00', '10808', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('11', '浙江省', '0.00', '12596', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('12', '安徽省', '0.00', '14234', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('13', '福建省', '0.00', '16068', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('14', '江西省', '0.00', '17359', 'cn', '1554775962');
INSERT INTO `ey_shop_shipping_template` VALUES ('15', '山东省', '0.00', '19280', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('16', '河南省', '0.00', '21387', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('17', '湖北省', '0.00', '24022', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('18', '湖南省', '0.00', '25579', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('19', '广东省', '0.00', '28240', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('20', '广西壮族自治区', '0.00', '30164', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('21', '海南省', '0.00', '31563', 'cn', '1555483193');
INSERT INTO `ey_shop_shipping_template` VALUES ('22', '重庆市', '0.00', '31929', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('23', '四川省', '0.00', '33007', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('24', '贵州省', '0.00', '37906', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('25', '云南省', '0.00', '39556', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('26', '西藏自治区', '0.00', '41103', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('27', '陕西省', '0.00', '41877', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('28', '甘肃省', '0.00', '43776', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('29', '青海省', '0.00', '45286', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('30', '宁夏回族自治区', '0.00', '45753', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('31', '新疆维吾尔自治区', '0.00', '46047', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('32', '台湾省', '0.00', '47493', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('33', '香港特别行政区', '0.00', '47494', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('34', '澳门特别行政区', '0.00', '47495', 'cn', '1554775610');
INSERT INTO `ey_shop_shipping_template` VALUES ('35', '统一配送价格', '0.00', '100000', 'cn', '1556618311');

-- -----------------------------
-- Table structure for `ey_single_content`
-- -----------------------------
DROP TABLE IF EXISTS `ey_single_content`;
CREATE TABLE `ey_single_content` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文档ID',
  `typeid` int(10) DEFAULT '0' COMMENT '栏目ID',
  `content` longtext COMMENT '内容详情',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `aid` (`aid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='单页附加表';

-- -----------------------------
-- Records of `ey_single_content`
-- -----------------------------
INSERT INTO `ey_single_content` VALUES ('1', '1', '1', '&lt;p&gt;易优内容管理系统以模板多、易优化、开源而闻名，是国内新锐的PHP开源网站管理系统，也是受用户好评的PHP类CMS系统，在经历多年的发展，目前的版本无论在功能，还是在后台易用性方面，都有了长足的发展和进步，eyoucms免费版的主要目标用户锁定在有企业建站需求的群体，当然也不乏有个人用户和学校等在使用该系统。&lt;/p&gt;&lt;p&gt;企业网站，无论大型还是中小型企业，利用网络传递信息在一定程度上提高了办事的效率，提高企业的竞争力。&lt;a href=&quot;http://www.eyoucms.com/&quot; target=&quot;_blank&quot;&gt;EyouCms&lt;/a&gt;网站建设系统适合做各类型网站，&lt;a href=&quot;http://www.eyoucms.com/&quot; target=&quot;_blank&quot;&gt;EyouCms&lt;/a&gt;是什么，&lt;a href=&quot;http://www.eyoucms.com/&quot; target=&quot;_blank&quot;&gt;EyouCms&lt;/a&gt;是一个自由和开放源码的内容管理系统，它是一个可以独立使用的内容发布系统（CMS）。在中国，&lt;a href=&quot;http://www.eyoucms.com/&quot; target=&quot;_blank&quot;&gt;EyouCms&lt;/a&gt;属于人们喜爱的CMS系统。&lt;/p&gt;&lt;p&gt;政府机关，通过建立政府门户，有利于各种信息和资源的整合，为政府和社会公众之间加强联系和沟通，从而使政府可以更快、更便捷、更有效开展工作。&lt;/p&gt;&lt;p&gt;教育机构，通过网络信息的引入，使得教育机构之间及教育机构内部和教育者之间进行信息传递，全面提升教育类网站的层面。&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;我们的愿景：成为模板建站行业的领跑者&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;企业的使命：让天下没有难做的网站&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;企业价值观：拥抱变化，创造价值！&lt;/p&gt;', '1610614937', '1610614937');
INSERT INTO `ey_single_content` VALUES ('2', '2', '8', '&lt;p&gt;易优内容管理系统以模板多、易优化、开源而闻名，是国内新锐的PHP开源网站管理系统，也是受用户好评的PHP类CMS系统，在经历多年的发展，目前的版本无论在功能，还是在后台易用性方面，都有了长足的发展和进步，eyoucms免费版的主要目标用户锁定在有企业建站需求的群体，当然也不乏有个人用户和学校等在使用该系统。&lt;/p&gt;&lt;p&gt;企业网站，无论大型还是中小型企业，利用网络传递信息在一定程度上提高了办事的效率，提高企业的竞争力。&lt;a href=&quot;http://www.eyoucms.com/&quot; target=&quot;_blank&quot;&gt;EyouCms&lt;/a&gt;网站建设系统适合做各类型网站，&lt;a href=&quot;http://www.eyoucms.com/&quot; target=&quot;_blank&quot;&gt;EyouCms&lt;/a&gt;是什么，&lt;a href=&quot;http://www.eyoucms.com/&quot; target=&quot;_blank&quot;&gt;EyouCms&lt;/a&gt;是一个自由和开放源码的内容管理系统，它是一个可以独立使用的内容发布系统（CMS）。在中国，&lt;a href=&quot;http://www.eyoucms.com/&quot; target=&quot;_blank&quot;&gt;EyouCms&lt;/a&gt;属于人们喜爱的CMS系统。&lt;/p&gt;&lt;p&gt;政府机关，通过建立政府门户，有利于各种信息和资源的整合，为政府和社会公众之间加强联系和沟通，从而使政府可以更快、更便捷、更有效开展工作。&lt;/p&gt;&lt;p&gt;教育机构，通过网络信息的引入，使得教育机构之间及教育机构内部和教育者之间进行信息传递，全面提升教育类网站的层面。&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;我们的愿景：成为模板建站行业的领跑者&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;企业的使命：让天下没有难做的网站&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;企业价值观：拥抱变化，创造价值！&lt;/p&gt;', '1610615445', '1610615445');
INSERT INTO `ey_single_content` VALUES ('3', '3', '13', '&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;针对不同服务器、虚拟空间，运行PHP的环境也有所不同，目前主要分为：Nginx、apache、IIS以及其他服务器。下面分享如何去掉URL上的index.php字符，&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;记得最后要重启服务器，在管理后台清除缓存哦！&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;nbsp;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;【IIS服务器】&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;在网站根目录下有个 web.config 文件，这个文件的作用是重写URL，让URL变得简短，易于SEO优化，以及用户的记忆。打开 web.config 文件，在原有的基础上加以下代码片段即可。&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;rewrite&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;rules&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;rule name=&amp;quot;Imported Rule 1&amp;quot; enabled=&amp;quot;true&amp;quot; stopProcessing=&amp;quot;true&amp;quot;&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;match url=&amp;quot;^(.*)$&amp;quot; /&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;conditions logicalGrouping=&amp;quot;MatchAll&amp;quot;&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;add input=&amp;quot;{HTTP_HOST}&amp;quot; pattern=&amp;quot;^(.*)$&amp;quot; /&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;add input=&amp;quot;{REQUEST_FILENAME}&amp;quot; matchType=&amp;quot;IsFile&amp;quot; negate=&amp;quot;true&amp;quot; /&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;add input=&amp;quot;{REQUEST_FILENAME}&amp;quot; matchType=&amp;quot;IsDirectory&amp;quot; negate=&amp;quot;true&amp;quot; /&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;/conditions&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;action type=&amp;quot;Rewrite&amp;quot; url=&amp;quot;index.php/{R:1}&amp;quot; /&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;/rule&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;/rules&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;/rewrite&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;nbsp;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;以下是某个香港虚拟空间的效果：&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;?xml version=&amp;quot;1.0&amp;quot; encoding=&amp;quot;UTF-8&amp;quot;?&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;configuration&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;system.webServer&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;handlers&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;remove name=&amp;quot;PHP-7.0-7i24.com&amp;quot; /&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;remove name=&amp;quot;PHP-5.6-7i24.com&amp;quot; /&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;remove name=&amp;quot;PHP-5.5-7i24.com&amp;quot; /&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;remove name=&amp;quot;PHP-5.4-7i24.com&amp;quot; /&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;remove name=&amp;quot;PHP-5.3-7i24.com&amp;quot; /&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;remove name=&amp;quot;PHP-5.2-7i24.com&amp;quot; /&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;add name=&amp;quot;PHP-5.4-7i24.com&amp;quot; path=&amp;quot;*.php&amp;quot; verb=&amp;quot;*&amp;quot; modules=&amp;quot;FastCgiModule&amp;quot; scriptProcessor=&amp;quot;c:php.4php-cgi.exe&amp;quot; resourceType=&amp;quot;Either&amp;quot; /&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;/handlers&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;rewrite&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;rules&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;rule name=&amp;quot;Imported Rule 1&amp;quot; enabled=&amp;quot;true&amp;quot; stopProcessing=&amp;quot;true&amp;quot;&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;match url=&amp;quot;^(.*)$&amp;quot; /&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;conditions logicalGrouping=&amp;quot;MatchAll&amp;quot;&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;add input=&amp;quot;{HTTP_HOST}&amp;quot; pattern=&amp;quot;^(.*)$&amp;quot; /&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;add input=&amp;quot;{REQUEST_FILENAME}&amp;quot; matchType=&amp;quot;IsFile&amp;quot; negate=&amp;quot;true&amp;quot; /&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;add input=&amp;quot;{REQUEST_FILENAME}&amp;quot; matchType=&amp;quot;IsDirectory&amp;quot; negate=&amp;quot;true&amp;quot; /&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;/conditions&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;action type=&amp;quot;Rewrite&amp;quot; url=&amp;quot;index.php/{R:1}&amp;quot; /&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;/rule&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;/rules&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;/rewrite&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;/system.webServer&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;/configuration&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;nbsp;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;【Nginx服务器】&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;在原有的nginx重写文件里新增以下代码片段：&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;location / {&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;if (!-e $request_filename) {&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;rewrite ^(.*)$ /index.php?s=/$1 last;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;break;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;}&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;}&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;nbsp;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;【apache服务器】&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;易优CMS在apache服务器环境默认自动隐藏index.php入口。&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;如果发现没隐藏，可以检查根目录.htaccess是否含有以下代码段：&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;IfModule mod_rewrite.c&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;div style=&quot;box-sizing: border-box;&quot;&gt;Options +FollowSymlinks -Multiviews&lt;/div&gt;&lt;div style=&quot;box-sizing: border-box;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;RewriteEngine on&lt;/span&gt;&lt;/div&gt;&lt;div style=&quot;box-sizing: border-box;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;RewriteCond %{REQUEST_FILENAME} !-d&lt;/span&gt;&lt;/div&gt;&lt;div style=&quot;box-sizing: border-box;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;RewriteCond %{REQUEST_FILENAME} !-f&lt;/span&gt;&lt;/div&gt;&lt;div style=&quot;box-sizing: border-box;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]&lt;/span&gt;&lt;/div&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;/IfModule&amp;gt;&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;如果存在，继续查看apache是否开启了URL重写模块 rewrite_module ， 然后重启服务就行了。&lt;/div&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '0', '1531710225');
INSERT INTO `ey_single_content` VALUES ('6', '47', '39', '&lt;p&gt;For different servers and virtual spaces, the environment for running PHP is also different, currently mainly divided into: Nginx, apache, IIS and other servers. Here&amp;#39;s how to remove the index. PHP character from the URL. Remember to restart the server and clear the cache in the management background.&lt;/p&gt;&lt;p&gt;[IIS Server]&lt;/p&gt;&lt;p&gt;There is a web. config file in the root directory of the website. The function of this file is to rewrite the URL, make the URL short, easy to optimize by SEO, and the user&amp;#39;s memory. Open the web. config file and add the following code fragments on the original basis.&lt;/p&gt;&lt;p&gt;&amp;lt;rewrite&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;rules&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;rule name= &amp;quot;Imported Rule 1&amp;quot; enabled= &amp;quot;true&amp;quot; stopProcessing= &amp;quot;true&amp;quot;&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;match url=&amp;quot;^(. *)$&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;conditions logicalGrouping=&amp;quot;MatchAll&amp;quot;&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;add input=&amp;quot;{HTTP_HOST}&amp;quot; pattern=&amp;quot;^(. *)$&amp;quot;/&amp;gt;&amp;quot;&lt;/p&gt;&lt;p&gt;&amp;lt;add input=&amp;quot;{REQUEST_FILENAME}&amp;quot; matchType= &amp;quot;IsFile&amp;quot; negate= &amp;quot;true&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;add input=&amp;quot;{REQUEST_FILENAME}&amp;quot; matchType= &amp;quot;IsDirectory&amp;quot; negate= &amp;quot;true&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/conditions&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;action type=&amp;quot;Rewrite&amp;quot; url=&amp;quot;index.php/{R:1}&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/rule&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/rules&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/rewrite&amp;gt;&lt;/p&gt;&lt;p&gt;The following is the effect of a Hong Kong virtual space:&lt;/p&gt;&lt;p&gt;&amp;lt;? XML version = &amp;quot;1.0&amp;quot; encoding = &amp;quot;UTF-8&amp;quot;?&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;configuration&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;system.webServer&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;handlers&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;remove name=&amp;quot;PHP-7.0-7i24.com&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;remove name=&amp;quot;PHP-5.6-7i24.com&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;remove name=&amp;quot;PHP-5.5-7i24.com&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;remove name=&amp;quot;PHP-5.4-7i24.com&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;remove name=&amp;quot;PHP-5.3-7i24.com&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;remove name=&amp;quot;PHP-5.2-7i24.com&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;add name=&amp;quot;PHP-5.4-7i24.com&amp;quot; path=&amp;quot;*.php&amp;quot; verb=&amp;quot;*&amp;quot; modules=&amp;quot;FastCgiModule&amp;quot; scriptProcessor=&amp;quot;c:php.4php-cgi.exe&amp;quot; resourceType=&amp;quot;Either&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/handlers&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;rewrite&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;rules&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;rule name= &amp;quot;Imported Rule 1&amp;quot; enabled= &amp;quot;true&amp;quot; stopProcessing= &amp;quot;true&amp;quot;&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;match url=&amp;quot;^(. *)$&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;conditions logicalGrouping=&amp;quot;MatchAll&amp;quot;&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;add input=&amp;quot;{HTTP_HOST}&amp;quot; pattern=&amp;quot;^(. *)$&amp;quot;/&amp;gt;&amp;quot;&lt;/p&gt;&lt;p&gt;&amp;lt;add input=&amp;quot;{REQUEST_FILENAME}&amp;quot; matchType= &amp;quot;IsFile&amp;quot; negate= &amp;quot;true&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;add input=&amp;quot;{REQUEST_FILENAME}&amp;quot; matchType= &amp;quot;IsDirectory&amp;quot; negate= &amp;quot;true&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/conditions&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;action type=&amp;quot;Rewrite&amp;quot; url=&amp;quot;index.php/{R:1}&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/rule&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/rules&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/rewrite&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/system.webServer&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/configuration&amp;gt;&lt;/p&gt;&lt;p&gt;[Nginx Server]&lt;/p&gt;&lt;p&gt;Add the following code fragments to the original nginx rewrite file:&lt;/p&gt;&lt;p&gt;Location / {&lt;/p&gt;&lt;p&gt;If (!-e $request_filename) {&lt;/p&gt;&lt;p&gt;Rewrite ^(. *)$/ index. php? S=/$1 last;&lt;/p&gt;&lt;p&gt;Break;&lt;/p&gt;&lt;p&gt;}&lt;/p&gt;&lt;p&gt;}&lt;/p&gt;&lt;p&gt;[apache server]&lt;/p&gt;&lt;p&gt;Yiyou CMS automatically hides index. PHP entries by default in Apache server environment.&lt;/p&gt;&lt;p&gt;If no hiding is found, you can check whether the root directory. htaccess contains the following code snippets:&lt;/p&gt;&lt;p&gt;&amp;lt;IfModule mod_rewrite.c&amp;gt;&lt;/p&gt;&lt;p&gt;Options + FollowSymlinks - Multiviews&lt;/p&gt;&lt;p&gt;RewriteEngine on&lt;/p&gt;&lt;p&gt;RewriteCond%{REQUEST_FILENAME}!-d&lt;/p&gt;&lt;p&gt;RewriteCond%{REQUEST_FILENAME}!-f&lt;/p&gt;&lt;p&gt;RewriteRule ^(*)$index.php/$1 [QSA, PT, L]&lt;/p&gt;&lt;p&gt;&amp;lt;/IfModule&amp;gt;&lt;/p&gt;&lt;p&gt;If it exists, go ahead and see if Apache has opened the URL rewrite_module and restart the service.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1545270877', '1545270877');
INSERT INTO `ey_single_content` VALUES ('7', '88', '54', '&lt;p style=&quot;text-align: center;&quot;&gt;电话: 400-123-4567&lt;br style=&quot;outline: none; box-sizing: border-box; color: rgb(47, 47, 47); font-family: Arial, &amp;quot;Helvetica Neue&amp;quot;, Helvetica, sans-serif; font-size: 14px; text-align: center; white-space: normal;&quot;/&gt;传真: + 86-123-4567&lt;br style=&quot;outline: none; box-sizing: border-box; color: rgb(47, 47, 47); font-family: Arial, &amp;quot;Helvetica Neue&amp;quot;, Helvetica, sans-serif; font-size: 14px; text-align: center; white-space: normal;&quot;/&gt;QQ:1234567890&lt;br style=&quot;outline: none; box-sizing: border-box; color: rgb(47, 47, 47); font-family: Arial, &amp;quot;Helvetica Neue&amp;quot;, Helvetica, sans-serif; font-size: 14px; text-align: center; white-space: normal;&quot;/&gt;邮箱: admin@youweb.com&lt;br style=&quot;outline: none; box-sizing: border-box; color: rgb(47, 47, 47); font-family: Arial, &amp;quot;Helvetica Neue&amp;quot;, Helvetica, sans-serif; font-size: 14px; text-align: center; white-space: normal;&quot;/&gt;地址: 广东省广州市天河区某某工业区88号&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;iframe class=&quot;ueditor_baidumap&quot; src=&quot;/public/plugins/Ueditor/dialogs/map/show.html#center=116.404,39.915&amp;zoom=10&amp;width=530&amp;height=340&amp;markers=116.404,39.915&amp;markerStyles=l,A&quot; frameborder=&quot;0&quot; width=&quot;534&quot; height=&quot;344&quot;&gt;&lt;/iframe&gt;&lt;/p&gt;', '1564645627', '1564645627');

-- -----------------------------
-- Table structure for `ey_sms_log`
-- -----------------------------
DROP TABLE IF EXISTS `ey_sms_log`;
CREATE TABLE `ey_sms_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '表id',
  `source` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '发送来源，与场景ID对应：0=注册，1=绑定，2=登录密码，3=支付密码，4=找回密码',
  `sms_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '短信服务商类型，1---阿里云短信， 2---腾讯云短信',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发送时间',
  `code` varchar(10) NOT NULL DEFAULT '' COMMENT '验证码',
  `status` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '发送状态,1:成功,0:失败',
  `is_use` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否使用，1:是，0:否',
  `msg` varchar(255) NOT NULL DEFAULT '' COMMENT '短信内容',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `error_msg` text NOT NULL COMMENT '发送短信异常内容',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='手机短信发送记录';


-- -----------------------------
-- Table structure for `ey_sms_template`
-- -----------------------------
DROP TABLE IF EXISTS `ey_sms_template`;
CREATE TABLE `ey_sms_template` (
  `tpl_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `sms_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '短信服务商类型，1---阿里云短信， 2---腾讯云短信',
  `tpl_title` varchar(128) NOT NULL DEFAULT '' COMMENT '短信标题',
  `sms_sign` varchar(50) NOT NULL DEFAULT '' COMMENT '短信签名',
  `sms_tpl_code` varchar(100) NOT NULL DEFAULT '' COMMENT '短信模板ID',
  `tpl_content` varchar(1000) NOT NULL DEFAULT '' COMMENT '发送短信内容',
  `send_scene` varchar(100) NOT NULL DEFAULT '' COMMENT '短信发送场景',
  `is_open` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启使用这个模板，1为是，0为否。',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`tpl_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='手机短信发送模板';

-- -----------------------------
-- Records of `ey_sms_template`
-- -----------------------------
INSERT INTO `ey_sms_template` VALUES ('1', '1', '账号注册', '', '', '验证码为 ${content} ，请在30分钟内输入验证。', '0', '1', 'cn', '1591262356', '1591262356');
INSERT INTO `ey_sms_template` VALUES ('2', '1', '手机绑定', '', '', '验证码为 ${content} ，请在30分钟内输入验证。', '1', '1', 'cn', '1591262356', '1591262356');
INSERT INTO `ey_sms_template` VALUES ('3', '1', '找回密码', '', '', '验证码为 ${content} ，请在30分钟内输入验证。', '4', '1', 'cn', '1591262356', '1591262356');
INSERT INTO `ey_sms_template` VALUES ('4', '1', '订单付款', '', '', '您有新的消息：您有新的${content}订单，请注意查收！', '5', '1', 'cn', '1591262356', '1616460912');
INSERT INTO `ey_sms_template` VALUES ('5', '1', '账号注册', '', '', '验证码为 ${content} ，请在30分钟内输入验证。', '0', '1', 'en', '1591262356', '1591262356');
INSERT INTO `ey_sms_template` VALUES ('6', '1', '手机绑定', '', '', '验证码为 ${content} ，请在30分钟内输入验证。', '1', '1', 'en', '1591262356', '1591262356');
INSERT INTO `ey_sms_template` VALUES ('7', '1', '找回密码', '', '', '验证码为 ${content} ，请在30分钟内输入验证。', '4', '1', 'en', '1591262356', '1591262356');
INSERT INTO `ey_sms_template` VALUES ('8', '1', '订单付款', '', '', '您有新的消息：您有新的${content}订单，请注意查收！', '5', '1', 'en', '1591262356', '1616460912');
INSERT INTO `ey_sms_template` VALUES ('9', '2', '账号注册', '', '', '验证码为 {1} ，请在30分钟内输入验证。', '0', '1', 'cn', '1610334638', '1610334638');
INSERT INTO `ey_sms_template` VALUES ('10', '2', '手机绑定', '', '', '验证码为 {1} ，请在30分钟内输入验证。', '1', '1', 'cn', '1610334638', '1610334638');
INSERT INTO `ey_sms_template` VALUES ('11', '2', '找回密码', '', '', '验证码为 {1} ，请在30分钟内输入验证。', '4', '1', 'cn', '1610334638', '1610334638');
INSERT INTO `ey_sms_template` VALUES ('12', '2', '订单付款', '', '', '您有新的消息：您有新的{1}订单，请注意查收！', '5', '1', 'cn', '1610334638', '1616460912');
INSERT INTO `ey_sms_template` VALUES ('13', '2', '账号注册', '', '', '验证码为 {1} ，请在30分钟内输入验证。', '0', '1', 'en', '1610334638', '1610334638');
INSERT INTO `ey_sms_template` VALUES ('14', '2', '手机绑定', '', '', '验证码为 {1} ，请在30分钟内输入验证。', '1', '1', 'en', '1610334638', '1610334638');
INSERT INTO `ey_sms_template` VALUES ('15', '2', '找回密码', '', '', '验证码为 {1} ，请在30分钟内输入验证。', '4', '1', 'en', '1610334638', '1610334638');
INSERT INTO `ey_sms_template` VALUES ('16', '2', '订单付款', '', '', '您有新的消息：您有新的{1}订单，请注意查收！', '5', '1', 'en', '1610334638', '1616460912');
INSERT INTO `ey_sms_template` VALUES ('17', '1', '订单发货', '', '', '您有新的消息：您有新的${content}订单，请注意查收！', '6', '1', 'cn', '1591262356', '1616460912');
INSERT INTO `ey_sms_template` VALUES ('18', '1', '订单发货', '', '', '您有新的消息：您有新的${content}订单，请注意查收！', '6', '1', 'en', '1591262356', '1616460912');
INSERT INTO `ey_sms_template` VALUES ('19', '2', '订单发货', '', '', '您有新的消息：您有新的{1}订单，请注意查收！', '6', '1', 'cn', '1610334638', '1616460912');
INSERT INTO `ey_sms_template` VALUES ('20', '2', '订单发货', '', '', '您有新的消息：您有新的{1}订单，请注意查收！', '6', '1', 'en', '1610334638', '1616460912');

-- -----------------------------
-- Table structure for `ey_smtp_record`
-- -----------------------------
DROP TABLE IF EXISTS `ey_smtp_record`;
CREATE TABLE `ey_smtp_record` (
  `record_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `source` tinyint(1) DEFAULT '0' COMMENT '来源，与场景ID对应：0=默认，2=注册，3=绑定邮箱，4=找回密码',
  `email` varchar(50) DEFAULT '' COMMENT '邮件地址',
  `users_id` int(10) DEFAULT '0' COMMENT '用户ID',
  `code` varchar(20) DEFAULT '' COMMENT '发送邮件内容',
  `status` tinyint(1) DEFAULT '0' COMMENT '是否使用，默认0，0为未使用，1为使用',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`record_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='邮件发送记录表';


-- -----------------------------
-- Table structure for `ey_smtp_tpl`
-- -----------------------------
DROP TABLE IF EXISTS `ey_smtp_tpl`;
CREATE TABLE `ey_smtp_tpl` (
  `tpl_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `tpl_name` varchar(200) DEFAULT '' COMMENT '模板名称',
  `tpl_title` varchar(200) DEFAULT '' COMMENT '邮件标题',
  `tpl_content` text COMMENT '发送邮件内容',
  `send_scene` tinyint(1) DEFAULT '0' COMMENT '邮件发送场景(1=留言表单）',
  `is_open` tinyint(1) DEFAULT '0' COMMENT '是否开启使用这个模板，1为是，0为否。',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`tpl_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='邮件模板表';

-- -----------------------------
-- Records of `ey_smtp_tpl`
-- -----------------------------
INSERT INTO `ey_smtp_tpl` VALUES ('1', '留言表单', '您有新的留言消息，请查收！', '${content}', '1', '1', 'cn', '1544763495', '1552638302');
INSERT INTO `ey_smtp_tpl` VALUES ('2', '会员注册', '验证码已发送至您的邮箱，请登录邮箱查看验证码！', '${content}', '2', '1', 'cn', '1544763495', '1552667056');
INSERT INTO `ey_smtp_tpl` VALUES ('3', '绑定邮箱', '验证码已发送至您的邮箱，请登录邮箱查看验证码！', '${content}', '3', '1', 'cn', '1544763495', '1552667400');
INSERT INTO `ey_smtp_tpl` VALUES ('4', '找回密码', '验证码已发送至您的邮箱，请登录邮箱查看验证码！', '${content}', '4', '1', 'cn', '1544763495', '1552663577');
INSERT INTO `ey_smtp_tpl` VALUES ('9', '订单付款', '您有新的待发货订单消息，请到商城订单查看！', '${content}', '5', '1', 'cn', '1587364685', '1616460912');
INSERT INTO `ey_smtp_tpl` VALUES ('11', '订单发货', '您有新的待收货订单消息，请到会员订单查看！', '${content}', '6', '1', 'cn', '1616460912', '1616460912');

-- -----------------------------
-- Table structure for `ey_special_content`
-- -----------------------------
DROP TABLE IF EXISTS `ey_special_content`;
CREATE TABLE `ey_special_content` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aid` int(10) DEFAULT '0' COMMENT '文档ID',
  `content` longtext COMMENT '内容详情',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `aid` (`aid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='专题附加表';


-- -----------------------------
-- Table structure for `ey_special_node`
-- -----------------------------
DROP TABLE IF EXISTS `ey_special_node`;
CREATE TABLE `ey_special_node` (
  `node_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `aid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文档ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '节点名称',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '节点标识',
  `isauto` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否自动获取文档',
  `keywords` varchar(200) NOT NULL DEFAULT '' COMMENT '关键字（多个中间用'',''分开）',
  `typeid` int(10) NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `aidlist` text NOT NULL COMMENT '关联文章列表（多个中间用'',''分开）',
  `row` int(5) NOT NULL DEFAULT '10' COMMENT '文档数',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort_order` int(11) DEFAULT '0' COMMENT '排序',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`node_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='专题节点表';


-- -----------------------------
-- Table structure for `ey_sql_cache_table`
-- -----------------------------
DROP TABLE IF EXISTS `ey_sql_cache_table`;
CREATE TABLE `ey_sql_cache_table` (
  `cache_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `sql_name` varchar(60) NOT NULL DEFAULT '' COMMENT 'mysql语句的标识名称，目前由模型名称+模型ID组成',
  `sql_result` text NOT NULL COMMENT 'mysql执行结果',
  `sql_md5` varchar(60) NOT NULL DEFAULT '' COMMENT 'mysql语句MD5的值',
  `sql_query` text NOT NULL COMMENT '完整mysql语句',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`cache_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='mysql缓存表';

-- -----------------------------
-- Records of `ey_sql_cache_table`
-- -----------------------------
INSERT INTO `ey_sql_cache_table` VALUES ('1', 'ArchivesMaxID', '105', '494a17e43cff13eeb4b9c9837c29026c', 'SELECT MAX(aid) AS tp_max FROM `ey_archives` LIMIT 1', '1623322108', '1623322108');
INSERT INTO `ey_sql_cache_table` VALUES ('2', '|model|all|count|', '{\"1\":{\"channel\":1,\"total\":17},\"2\":{\"channel\":2,\"total\":12},\"3\":{\"channel\":3,\"total\":6},\"4\":{\"channel\":4,\"total\":4},\"6\":{\"channel\":6,\"total\":2},\"9\":{\"channel\":9,\"total\":6}}', 'd5ac17fe5649c6d04f4b174c42f2d535', 'SELECT channel, count(aid) as total FROM `ey_archives` WHERE  `lang` = \'cn\'  AND `status` = 1  AND `is_del` = 0  AND (  (users_id = 0 OR (users_id > 0 AND arcrank >= 0)) ) GROUP BY `channel`', '1623322108', '1623322108');
INSERT INTO `ey_sql_cache_table` VALUES ('3', '|archives|!=8,6,5,7,51|', '45', '79384ebcdb28f5eed29a07a310ff31ee', 'SELECT COUNT(aid) AS tp_count FROM `ey_archives` `a` WHERE  `a`.`channel` NOT IN (8,6,5,7,51)  AND `a`.`lang` = \'cn\'  AND `a`.`is_del` = 0  AND (  (a.users_id = 0 OR (a.users_id > 0 AND a.arcrank >= 0)) ) LIMIT 1', '1623322151', '1623322151');
INSERT INTO `ey_sql_cache_table` VALUES ('4', '|product|2|20|', '4', 'effbf8a7f3aa3d388c0e1d1634f1949a', 'SELECT COUNT(aid) AS tp_count FROM `ey_archives` `a` WHERE  `a`.`typeid` IN (20,24,25)  AND `a`.`channel` = 2  AND `a`.`lang` = \'cn\'  AND `a`.`is_del` = 0  AND (  (a.users_id = 0 OR (a.users_id > 0 AND a.arcrank >= 0)) ) LIMIT 1', '1623322291', '1623322291');
INSERT INTO `ey_sql_cache_table` VALUES ('5', '|product|2|24|', '3', '33f19a9c28dbf39ea88e7f92bdfdadcf', 'SELECT COUNT(aid) AS tp_count FROM `ey_archives` `a` WHERE  `a`.`typeid` IN (24)  AND `a`.`channel` = 2  AND `a`.`lang` = \'cn\'  AND `a`.`is_del` = 0  AND (  (a.users_id = 0 OR (a.users_id > 0 AND a.arcrank >= 0)) ) LIMIT 1', '1623322297', '1623322297');
INSERT INTO `ey_sql_cache_table` VALUES ('6', '|arctype|all|count|', '{\"5\":{\"typeid\":5,\"num\":4},\"9\":{\"typeid\":9,\"num\":4},\"10\":{\"typeid\":10,\"num\":4},\"11\":{\"typeid\":11,\"num\":4},\"12\":{\"typeid\":12,\"num\":5},\"20\":{\"typeid\":20,\"num\":1},\"23\":{\"typeid\":23,\"num\":6},\"24\":{\"typeid\":24,\"num\":3},\"26\":{\"typeid\":26,\"num\":4},\"27\":{\"typeid\":27,\"num\":2},\"28\":{\"typeid\":28,\"num\":2},\"64\":{\"typeid\":64,\"num\":4},\"66\":{\"typeid\":66,\"num\":2}}', '8d7e2c28c692cbb5b32cf82431eba9bd', 'SELECT typeid, count(typeid) as num FROM `ey_archives` WHERE  `channel` IN (1,2,3,4,5,7,9,51)  AND `lang` = \'cn\'  AND `is_del` = 0  AND (  (users_id = 0 OR (users_id > 0 AND arcrank >= 0)) ) GROUP BY `typeid`', '1623322348', '1623322348');
INSERT INTO `ey_sql_cache_table` VALUES ('7', '|archives|draft|', '0', 'a8513cd6433a6ec3d9c80d3bb0eadf6c', 'SELECT COUNT(aid) AS tp_count FROM `ey_archives` `a` WHERE  `a`.`channel` NOT IN (8,6,5,7,51)  AND `a`.`users_id` > 0  AND `a`.`arcrank` < 0  AND `a`.`lang` = \'cn\'  AND `a`.`is_del` = 0 LIMIT 1', '1623809286', '1623809286');

-- -----------------------------
-- Table structure for `ey_tagindex`
-- -----------------------------
DROP TABLE IF EXISTS `ey_tagindex`;
CREATE TABLE `ey_tagindex` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'tagid',
  `tag` varchar(50) NOT NULL DEFAULT '' COMMENT 'tag内容',
  `typeid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `litpic` varchar(250) DEFAULT '' COMMENT '封面图',
  `seo_title` varchar(200) DEFAULT '' COMMENT 'SEO标题',
  `seo_keywords` varchar(200) DEFAULT '' COMMENT 'SEO关键词',
  `seo_description` text COMMENT 'SEO描述',
  `count` int(10) unsigned DEFAULT '0' COMMENT '点击',
  `total` int(10) unsigned DEFAULT '0' COMMENT '文档数',
  `weekcc` int(10) unsigned DEFAULT '0' COMMENT '周统计',
  `monthcc` int(10) unsigned DEFAULT '0' COMMENT '月统计',
  `weekup` int(10) unsigned DEFAULT '0' COMMENT '每周更新',
  `monthup` int(10) unsigned DEFAULT '0' COMMENT '每月更新',
  `is_common` tinyint(1) DEFAULT '0' COMMENT '是否常用标签，0=否，1=是',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
  `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `typeid` (`typeid`) USING BTREE,
  KEY `count` (`count`,`total`,`weekcc`,`monthcc`,`weekup`,`monthup`,`add_time`) USING BTREE,
  KEY `tag` (`tag`) USING BTREE,
  KEY `lang` (`lang`,`add_time`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=utf8 COMMENT='标签索引表';

-- -----------------------------
-- Records of `ey_tagindex`
-- -----------------------------
INSERT INTO `ey_tagindex` VALUES ('28', '网站', '12', '', '', '', '', '0', '1', '0', '0', '1616490155', '0', '0', 'cn', '1547462640', '0');
INSERT INTO `ey_tagindex` VALUES ('29', '建设', '12', '', '', '', '', '0', '1', '0', '0', '1616490155', '0', '0', 'cn', '1547462640', '0');
INSERT INTO `ey_tagindex` VALUES ('30', '五大核心', '12', '', '', '', '', '0', '1', '0', '0', '1616490155', '0', '0', 'cn', '1547462640', '0');
INSERT INTO `ey_tagindex` VALUES ('31', '要素', '12', '', '', '', '', '0', '1', '0', '0', '1616490155', '0', '0', 'cn', '1547462640', '0');
INSERT INTO `ey_tagindex` VALUES ('32', '华为', '24', '', '', '', '', '0', '1', '0', '0', '1616490155', '0', '0', 'cn', '1571038749', '0');
INSERT INTO `ey_tagindex` VALUES ('33', 'HUAWEI', '24', '', '', '', '', '0', '1', '0', '0', '1616490155', '0', '0', 'cn', '1571038749', '0');
INSERT INTO `ey_tagindex` VALUES ('34', 'NOTE 8', '24', '', '', '', '', '0', '1', '0', '0', '1616490155', '0', '0', 'cn', '1571038749', '0');
INSERT INTO `ey_tagindex` VALUES ('37', '一号', '5', '', '', '', '', '3', '1', '2', '2', '1563785452', '1563785452', '0', 'cn', '1526614158', '0');
INSERT INTO `ey_tagindex` VALUES ('38', '社交', '12', '', '', '', '', '10', '1', '2', '2', '1616490155', '1610348155', '0', 'cn', '1563520600', '0');
INSERT INTO `ey_tagindex` VALUES ('39', '媒体', '12', '', '', '', '', '9', '1', '7', '7', '1616490155', '1610348163', '0', 'cn', '1563520600', '0');
INSERT INTO `ey_tagindex` VALUES ('40', '营销', '12', '', '', '', '', '1', '1', '0', '0', '1616490155', '1609990101', '0', 'cn', '1564545045', '0');
INSERT INTO `ey_tagindex` VALUES ('41', '商业', '12', '', '', '', '', '3', '1', '0', '0', '1616490155', '1610348720', '0', 'cn', '1564545045', '0');
INSERT INTO `ey_tagindex` VALUES ('42', '工程', '5', '', '', '', '', '0', '1', '0', '0', '1616490155', '0', '0', 'cn', '1564565463', '0');
INSERT INTO `ey_tagindex` VALUES ('43', '机械', '5', '', '', '', '', '0', '1', '0', '0', '1616490155', '0', '0', 'cn', '1564565463', '0');
INSERT INTO `ey_tagindex` VALUES ('44', '推土', '5', '', '', '', '', '0', '1', '0', '0', '1616490155', '0', '0', 'cn', '1564565463', '0');
INSERT INTO `ey_tagindex` VALUES ('45', '挖掘', '5', '', '', '', '', '0', '1', '0', '0', '1616490155', '0', '0', 'cn', '1564565463', '0');
INSERT INTO `ey_tagindex` VALUES ('46', '网站模板', '5', '', '', '', '', '0', '1', '0', '0', '1616490155', '0', '0', 'cn', '1564565463', '0');
INSERT INTO `ey_tagindex` VALUES ('47', 'WindowsXP', '5', '', '', '', '', '0', '1', '0', '0', '1616490155', '0', '0', 'cn', '1564623458', '0');
INSERT INTO `ey_tagindex` VALUES ('48', '操作系统', '5', '', '', '', '', '0', '1', '0', '0', '1616490155', '0', '0', 'cn', '1564623458', '0');
INSERT INTO `ey_tagindex` VALUES ('49', '网络优化', '64', '', '', '', '', '0', '1', '0', '0', '1616490155', '0', '0', 'cn', '1565234125', '0');
INSERT INTO `ey_tagindex` VALUES ('50', '推广服务', '64', '', '', '', '', '0', '1', '0', '0', '1616490155', '0', '0', 'cn', '1565234125', '0');

-- -----------------------------
-- Table structure for `ey_taglist`
-- -----------------------------
DROP TABLE IF EXISTS `ey_taglist`;
CREATE TABLE `ey_taglist` (
  `tid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'tagid',
  `aid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文档ID',
  `typeid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `tag` varchar(50) DEFAULT '' COMMENT 'tag内容',
  `arcrank` tinyint(1) DEFAULT '0' COMMENT '阅读权限',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`tid`,`aid`),
  KEY `aid` (`aid`,`typeid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章标签表';

-- -----------------------------
-- Records of `ey_taglist`
-- -----------------------------
INSERT INTO `ey_taglist` VALUES ('30', '13', '12', '五大核心', '0', 'cn', '1547462639', '1610615818');
INSERT INTO `ey_taglist` VALUES ('29', '13', '12', '建设', '0', 'cn', '1547462639', '1610615818');
INSERT INTO `ey_taglist` VALUES ('34', '27', '24', 'NOTE 8', '0', 'cn', '1571038748', '1610615791');
INSERT INTO `ey_taglist` VALUES ('33', '27', '24', 'HUAWEI', '0', 'cn', '1571038748', '1610615791');
INSERT INTO `ey_taglist` VALUES ('32', '27', '24', '华为', '0', 'cn', '1571038748', '1610615791');
INSERT INTO `ey_taglist` VALUES ('44', '30', '5', '推土', '0', 'cn', '1564565462', '1610615767');
INSERT INTO `ey_taglist` VALUES ('43', '30', '5', '机械', '0', 'cn', '1564565462', '1610615767');
INSERT INTO `ey_taglist` VALUES ('42', '30', '5', '工程', '0', 'cn', '1564565462', '1610615767');
INSERT INTO `ey_taglist` VALUES ('28', '13', '12', '网站', '0', 'cn', '1547462639', '1610615818');
INSERT INTO `ey_taglist` VALUES ('31', '13', '12', '要素', '0', 'cn', '1547462639', '1610615818');
INSERT INTO `ey_taglist` VALUES ('38', '40', '12', '社交', '0', 'cn', '1563520599', '1610615736');
INSERT INTO `ey_taglist` VALUES ('39', '40', '12', '媒体', '0', 'cn', '1563520599', '1610615736');
INSERT INTO `ey_taglist` VALUES ('41', '41', '12', '商业', '0', 'cn', '1564545044', '1610615730');
INSERT INTO `ey_taglist` VALUES ('40', '41', '12', '营销', '0', 'cn', '1564545044', '1610615730');
INSERT INTO `ey_taglist` VALUES ('45', '30', '5', '挖掘', '0', 'cn', '1564565462', '1610615767');
INSERT INTO `ey_taglist` VALUES ('46', '30', '5', '网站模板', '0', 'cn', '1564565462', '1610615767');
INSERT INTO `ey_taglist` VALUES ('48', '91', '5', '操作系统', '0', 'cn', '1564623457', '1610615652');
INSERT INTO `ey_taglist` VALUES ('47', '91', '5', 'WindowsXP', '0', 'cn', '1564623457', '1610615652');
INSERT INTO `ey_taglist` VALUES ('50', '42', '64', '推广服务', '0', 'cn', '1565234124', '0');
INSERT INTO `ey_taglist` VALUES ('49', '42', '64', '网络优化', '0', 'cn', '1565234124', '0');

-- -----------------------------
-- Table structure for `ey_ui_config`
-- -----------------------------
DROP TABLE IF EXISTS `ey_ui_config`;
CREATE TABLE `ey_ui_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `md5key` varchar(100) NOT NULL DEFAULT '' COMMENT '唯一键值（由 theme_style、page、name）组成',
  `theme_style` varchar(200) DEFAULT 'pc' COMMENT '模板风格',
  `page` varchar(64) DEFAULT '' COMMENT '页面分组',
  `type` varchar(50) DEFAULT '' COMMENT '编辑类型',
  `name` varchar(50) DEFAULT '' COMMENT '与页面的e-id对应',
  `value` text COMMENT '页面美化的val值',
  `idcode` varchar(50) DEFAULT '' COMMENT '页面唯一id标识（由 标识符+栏目id或文档id等）组成',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `md5key` (`md5key`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='可视化参数设置';


-- -----------------------------
-- Table structure for `ey_users`
-- -----------------------------
DROP TABLE IF EXISTS `ey_users`;
CREATE TABLE `ey_users` (
  `users_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `username` varchar(30) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '登录密码',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '昵称',
  `is_mobile` tinyint(1) DEFAULT '0' COMMENT '绑定手机号，0为不绑定，1为绑定',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号码（仅用于登录）',
  `is_email` tinyint(1) DEFAULT '0' COMMENT '绑定邮箱，0为不绑定，1为绑定',
  `email` varchar(60) NOT NULL DEFAULT '' COMMENT '电子邮件（仅用于登录）',
  `paypwd` varchar(50) DEFAULT '' COMMENT '支付密码，暂时未用到，可保留。',
  `users_money` decimal(10,2) DEFAULT '0.00' COMMENT '用户金额',
  `frozen_money` decimal(10,2) DEFAULT '0.00' COMMENT '冻结金额',
  `scores` int(10) DEFAULT '0' COMMENT '积分',
  `devote` int(10) DEFAULT '0' COMMENT '贡献值',
  `reg_time` int(11) unsigned DEFAULT '0' COMMENT '注册时间',
  `last_login` int(11) unsigned DEFAULT '0' COMMENT '最后登录时间',
  `last_ip` varchar(15) DEFAULT '' COMMENT '最后登录ip',
  `login_count` int(11) DEFAULT '0' COMMENT '登陆次数',
  `head_pic` varchar(255) DEFAULT '' COMMENT '头像',
  `province` int(6) DEFAULT '0' COMMENT '省份',
  `city` int(6) DEFAULT '0' COMMENT '市区',
  `district` int(6) DEFAULT '0' COMMENT '县',
  `level` smallint(5) DEFAULT '0' COMMENT '会员等级',
  `open_level_time` int(11) unsigned DEFAULT '0' COMMENT '开通会员级别时间',
  `level_maturity_days` varchar(20) DEFAULT '' COMMENT '会员级别到期天数',
  `discount` decimal(10,2) DEFAULT '1.00' COMMENT '会员折扣，默认1不享受',
  `total_amount` decimal(10,2) DEFAULT '0.00' COMMENT '消费累计额度',
  `is_activation` tinyint(1) DEFAULT '1' COMMENT '是否激活，0否，1是。\r\n后台注册默认为1激活。\r\n前台注册时，当会员功能设置选择后台审核，需后台激活才可以登陆。',
  `register_place` tinyint(1) DEFAULT '2' COMMENT '注册位置。后台注册不受注册验证影响，1为后台注册，2为前台注册。默认为2。',
  `open_id` varchar(50) NOT NULL DEFAULT '' COMMENT '第三方唯一标识openid',
  `thirdparty` tinyint(1) DEFAULT '0' COMMENT '第三方注册类型：0=普通，1=微信，2=QQ',
  `is_lock` tinyint(1) DEFAULT '0' COMMENT '是否被锁定冻结',
  `admin_id` int(10) DEFAULT '0' COMMENT '关联管理员ID',
  `lang` varchar(20) DEFAULT 'cn' COMMENT '语言标识',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '伪删除，1=是，0=否',
  `unread_notice_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '未读消息数量',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  `coin` int(11) unsigned DEFAULT '0' COMMENT '金币',
  PRIMARY KEY (`users_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='会员信息表';

-- -----------------------------
-- Records of `ey_users`
-- -----------------------------
INSERT INTO `ey_users` VALUES ('1', 'admin', '18e6a423f5a873ab50c1c55c59c69517', 'admin', '0', '13644444444', '0', '123@11.com', '', '0.00', '0.00', '3', '0', '1564475243', '1624416358', '127.0.0.1', '4', '', '0', '0', '0', '1', '0', '', '1.00', '0.00', '1', '2', '', '0', '0', '1', 'cn', '0', '0', '1624416358', '0');

-- -----------------------------
-- Table structure for `ey_users_bottom_menu`
-- -----------------------------
DROP TABLE IF EXISTS `ey_users_bottom_menu`;
CREATE TABLE `ey_users_bottom_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `title` varchar(30) DEFAULT '' COMMENT '导航名称',
  `mca` varchar(50) DEFAULT '' COMMENT '分组/控制器/操作名',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `sort_order` int(10) DEFAULT '100' COMMENT '排序号',
  `status` tinyint(1) DEFAULT '1' COMMENT '功能开关状态，1=开启，0=关闭',
  `display` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示, 1--是, 0--否',
  `lang` varchar(20) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='会员中心移动端底部菜单表';

-- -----------------------------
-- Records of `ey_users_bottom_menu`
-- -----------------------------
INSERT INTO `ey_users_bottom_menu` VALUES ('1', '首页', 'home/Index/index', 'shouye', '100', '1', '1', 'cn', '1610334638', '1610334638');
INSERT INTO `ey_users_bottom_menu` VALUES ('2', '下载', 'user/Download/index', 'xiazai', '100', '1', '1', 'cn', '1610334638', '1610334638');
INSERT INTO `ey_users_bottom_menu` VALUES ('3', '发布', 'user/UsersRelease/article_add', 'fabu', '100', '1', '1', 'cn', '1610334638', '1610334638');
INSERT INTO `ey_users_bottom_menu` VALUES ('4', '我的', 'user/Users/centre', 'geren', '100', '1', '1', 'cn', '1610334638', '1610334638');
INSERT INTO `ey_users_bottom_menu` VALUES ('5', '首页', 'home/Index/index', 'shouye', '100', '1', '1', 'en', '1610334638', '1610334638');
INSERT INTO `ey_users_bottom_menu` VALUES ('6', '下载', 'user/Download/index', 'xiazai', '100', '1', '1', 'en', '1610334638', '1610334638');
INSERT INTO `ey_users_bottom_menu` VALUES ('7', '发布', 'user/UsersRelease/article_add', 'fabu', '100', '1', '1', 'en', '1610334638', '1610334638');
INSERT INTO `ey_users_bottom_menu` VALUES ('8', '我的', 'user/Users/centre', 'geren', '100', '1', '1', 'en', '1610334638', '1610334638');

-- -----------------------------
-- Table structure for `ey_users_collection`
-- -----------------------------
DROP TABLE IF EXISTS `ey_users_collection`;
CREATE TABLE `ey_users_collection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(10) DEFAULT NULL,
  `aid` int(10) DEFAULT '0' COMMENT '文档id',
  `channel` int(10) DEFAULT '0' COMMENT '模型',
  `typeid` int(10) DEFAULT '0' COMMENT '栏目',
  `title` varchar(50) DEFAULT '' COMMENT '网站标题',
  `litpic` varchar(255) DEFAULT '' COMMENT '缩略图',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='我的收藏';

-- -----------------------------
-- Records of `ey_users_collection`
-- -----------------------------
INSERT INTO `ey_users_collection` VALUES ('1', '1', '92', '4', '5', '计算机软件系统故障及维护2', '/uploads/allimg/20190808/682be7153d02d14890144bef217149d1.jpg', 'cn', '1610347624', '1610347624');

-- -----------------------------
-- Table structure for `ey_users_config`
-- -----------------------------
DROP TABLE IF EXISTS `ey_users_config`;
CREATE TABLE `ey_users_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '会员功能配置表ID',
  `name` varchar(50) DEFAULT '' COMMENT '配置的key键名',
  `value` text COMMENT '配置的value值',
  `desc` varchar(100) DEFAULT '' COMMENT '键名说明',
  `inc_type` varchar(64) DEFAULT '' COMMENT '配置分组',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='会员功能配置表';

-- -----------------------------
-- Records of `ey_users_config`
-- -----------------------------
INSERT INTO `ey_users_config` VALUES ('1', 'shop_open', '0', '', 'shop', 'cn', '1619594927');
INSERT INTO `ey_users_config` VALUES ('2', 'pay_open', '1', '', 'pay', 'cn', '1563498414');
INSERT INTO `ey_users_config` VALUES ('3', 'users_reg_notallow', 'www,bbs,ftp,mail,user,users,admin,administrator,eyoucms', '不允许注册的会员名', 'users', 'cn', '1547890773');
INSERT INTO `ey_users_config` VALUES ('4', 'level_member_upgrade', '1', '', 'level', 'cn', '1564555772');
INSERT INTO `ey_users_config` VALUES ('5', 'users_open_release', '1', '', 'users', 'cn', '1564555773');
INSERT INTO `ey_users_config` VALUES ('6', 'shop_open_spec', '1', '', 'shop', 'cn', '1571037736');
INSERT INTO `ey_users_config` VALUES ('7', 'score_signin_status', '1', '', 'score', 'cn', '1610334638');
INSERT INTO `ey_users_config` VALUES ('8', 'score_signin_score', '3', '', 'score', 'cn', '1610334638');
INSERT INTO `ey_users_config` VALUES ('9', 'score_signin_status', '1', '', 'score', 'en', '1610334638');
INSERT INTO `ey_users_config` VALUES ('10', 'score_signin_score', '3', '', 'score', 'en', '1610334638');
INSERT INTO `ey_users_config` VALUES ('11', 'users_open_register', '0', '', 'users', 'cn', '1610352452');
INSERT INTO `ey_users_config` VALUES ('12', 'users_open_reg', '0', '', 'users', 'cn', '1610352452');
INSERT INTO `ey_users_config` VALUES ('13', 'users_verification', '0', '', 'users', 'cn', '1610352452');
INSERT INTO `ey_users_config` VALUES ('14', 'theme_color', '#ff9600', '', 'theme', 'cn', '1610616432');
INSERT INTO `ey_users_config` VALUES ('15', 'pay_balance_open', '1', '', 'pay', 'cn', '1616460912');

-- -----------------------------
-- Table structure for `ey_users_footprint`
-- -----------------------------
DROP TABLE IF EXISTS `ey_users_footprint`;
CREATE TABLE `ey_users_footprint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel` int(10) DEFAULT '0' COMMENT '频道模型',
  `typeid` int(10) DEFAULT '0' COMMENT '栏目id',
  `aid` int(10) DEFAULT '0' COMMENT '文档id',
  `title` varchar(100) DEFAULT '' COMMENT '网站标题',
  `litpic` varchar(255) DEFAULT '' COMMENT '缩略图',
  `users_id` int(10) DEFAULT '0',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `users_id` (`users_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COMMENT='我的足迹';

-- -----------------------------
-- Records of `ey_users_footprint`
-- -----------------------------
INSERT INTO `ey_users_footprint` VALUES ('21', '3', '64', '93', '鼠标封面设计', '/uploads/allimg/20190808/b1f94bd8a0feba4062fa19d795099af4.jpg', '1', 'cn', '1610588278', '1610588349');
INSERT INTO `ey_users_footprint` VALUES ('2', '2', '28', '103', '联想智能音箱G1', '/uploads/allimg/20190808/13fba5d0f2454c4b8fee4ada1d3fb39b.jpg', '1', 'cn', '1610335752', '1610351881');
INSERT INTO `ey_users_footprint` VALUES ('3', '3', '64', '43', '3C数码蓝牙耳机产品渲染', '/uploads/allimg/20190808/1c3dabff0cbf24fb6667899396a866aa.jpg', '1', 'cn', '1610336370', '1610349548');
INSERT INTO `ey_users_footprint` VALUES ('4', '2', '24', '90', '小米8屏幕指纹版 6GB+128GB 黑色 全网通4G 双卡双待 全面屏拍照智能游戏手机', '/uploads/allimg/20190731/c4539460b957fea39a9db19e61eb0afe.jpg', '1', 'cn', '1610346287', '1610349050');
INSERT INTO `ey_users_footprint` VALUES ('5', '2', '28', '102', '联想智能音箱MINI', '/uploads/allimg/20190808/989d19deb2377e199ec63d5ef9244be8.jpg', '1', 'cn', '1610346779', '1610352887');
INSERT INTO `ey_users_footprint` VALUES ('22', '1', '11', '21', '网站设计与SEO的关系，高手是从这4个维度分析的！', '/uploads/allimg/20210107/1-21010G012205c.jpg', '1', 'cn', '1610588467', '1610588467');
INSERT INTO `ey_users_footprint` VALUES ('23', '1', '11', '38', '商梦网校：单页SEO站群技术，用10个网站优化排名！', '/uploads/allimg/20210107/1-21010G012425K.jpg', '1', 'cn', '1610589835', '1610589892');
INSERT INTO `ey_users_footprint` VALUES ('8', '2', '26', '99', 'MIIX 520 酷睿i5笔记本', '/uploads/allimg/20190808/821fcaa266d291b4f504fb9a1d412c1c.jpg', '1', 'cn', '1610346787', '1610348967');
INSERT INTO `ey_users_footprint` VALUES ('9', '2', '26', '98', 'MIIX520 二合一笔记本12.2英寸 i7', '/uploads/allimg/20190808/7dd05a89099c482a51be7faf1bb38ad4.jpg', '1', 'cn', '1610346789', '1610346789');
INSERT INTO `ey_users_footprint` VALUES ('10', '2', '20', '89', 'Apple iPhone 8 Plus (A1899) 64GB 深空灰色 移动联通4G手机', '/uploads/allimg/20190731/582042862ba0d06c9408a9a1e669a067.jpg', '1', 'cn', '1610346807', '1619595550');
INSERT INTO `ey_users_footprint` VALUES ('11', '2', '24', '37', '华为无线快充手机', '/uploads/allimg/20190319/8a405e72e2acf9c5a29da7341a0eff89.jpg', '1', 'cn', '1610347140', '1610347140');
INSERT INTO `ey_users_footprint` VALUES ('12', '2', '27', '29', ' 小米蓝牙项圈耳机', '/uploads/allimg/20190114/252a53e6fbc8f441b2570f755d2bbeb8.jpg', '1', 'cn', '1610347154', '1610347368');
INSERT INTO `ey_users_footprint` VALUES ('13', '2', '26', '28', '小米笔记本Air 13.3', '/uploads/allimg/20190114/66109e989148356eadb4ff1eee285826.jpg', '1', 'cn', '1610347405', '1610347405');
INSERT INTO `ey_users_footprint` VALUES ('14', '4', '5', '92', '计算机软件系统故障及维护2', '/uploads/allimg/20190808/682be7153d02d14890144bef217149d1.jpg', '1', 'cn', '1610347622', '1610588252');
INSERT INTO `ey_users_footprint` VALUES ('15', '9', '23', '96', '网络安全专员', '', '1', 'cn', '1610347652', '1610347652');
INSERT INTO `ey_users_footprint` VALUES ('16', '1', '12', '40', '社交媒体时代，如何对粉丝估值？', '/uploads/allimg/20210107/1-21010G01311136.jpg', '1', 'cn', '1610348146', '1610349151');
INSERT INTO `ey_users_footprint` VALUES ('17', '1', '12', '41', '《颠覆营销:大数据时代的商业革命》：大数据“多即少，少即多”各种行销手段早已令人眼花缭乱', '/uploads/allimg/20210107/1-21010G0132R20.jpg', '1', 'cn', '1610348718', '1610588083');
INSERT INTO `ey_users_footprint` VALUES ('18', '4', '5', '91', '计算机软件系统故障及维护', '/uploads/allimg/20190731/0c8845e11a94b0f765ab24259c5b06b9.gif', '1', 'cn', '1610349228', '1610349228');
INSERT INTO `ey_users_footprint` VALUES ('19', '3', '66', '105', '数码蓝牙耳机产品渲染', '/uploads/allimg/20190808/1c3dabff0cbf24fb6667899396a866aa.jpg', '1', 'cn', '1610349768', '1610589902');
INSERT INTO `ey_users_footprint` VALUES ('20', '9', '23', '97', '网络运营专员', '', '1', 'cn', '1610588096', '1610588096');

-- -----------------------------
-- Table structure for `ey_users_level`
-- -----------------------------
DROP TABLE IF EXISTS `ey_users_level`;
CREATE TABLE `ey_users_level` (
  `level_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `level_name` varchar(30) DEFAULT '' COMMENT '级别名称',
  `level_value` int(10) DEFAULT '0' COMMENT '会员等级值',
  `is_system` tinyint(1) DEFAULT '0' COMMENT '类型，1=系统，0=用户',
  `amount` decimal(10,2) DEFAULT '0.00' COMMENT '消费额度',
  `down_count` int(10) DEFAULT '0' COMMENT '每天下载次数限制',
  `discount` float(10,2) DEFAULT '100.00' COMMENT '折扣率，初始值为100即100%，无折扣',
  `posts_count` int(10) DEFAULT '5' COMMENT '会员投稿次数限制',
  `ask_is_release` tinyint(1) DEFAULT '1' COMMENT '允许在问答中发布问题，1=是，0=否',
  `ask_is_review` tinyint(1) DEFAULT '0' COMMENT '在问答中发布问题或回答是否需要审核，1=是，0=否',
  `lang` varchar(20) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`level_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='会员级别表';

-- -----------------------------
-- Records of `ey_users_level`
-- -----------------------------
INSERT INTO `ey_users_level` VALUES ('1', '注册会员', '10', '1', '0.00', '100', '100', '5', '1', '0', 'cn', '0', '1551151513');
INSERT INTO `ey_users_level` VALUES ('2', '中级会员', '50', '0', '0.00', '100', '100', '10', '1', '0', 'cn', '1564532901', '1564532901');
INSERT INTO `ey_users_level` VALUES ('3', '高级会员', '100', '0', '0.00', '100', '100', '20', '1', '0', 'cn', '1564532901', '1564532901');

-- -----------------------------
-- Table structure for `ey_users_list`
-- -----------------------------
DROP TABLE IF EXISTS `ey_users_list`;
CREATE TABLE `ey_users_list` (
  `list_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `users_id` int(10) NOT NULL DEFAULT '0' COMMENT '会员ID',
  `para_id` int(10) NOT NULL DEFAULT '0' COMMENT '属性ID',
  `info` text COMMENT '属性值',
  `lang` varchar(50) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`list_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='会员属性表(信息）';

-- -----------------------------
-- Records of `ey_users_list`
-- -----------------------------
INSERT INTO `ey_users_list` VALUES ('1', '1', '1', '13644444444', 'cn', '1564475243', '0');
INSERT INTO `ey_users_list` VALUES ('2', '1', '2', '123@11.com', 'cn', '1564475243', '0');

-- -----------------------------
-- Table structure for `ey_users_menu`
-- -----------------------------
DROP TABLE IF EXISTS `ey_users_menu`;
CREATE TABLE `ey_users_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `title` varchar(30) DEFAULT '' COMMENT '导航名称',
  `version` varchar(10) DEFAULT 'weapp' COMMENT '分组',
  `mca` varchar(50) DEFAULT '' COMMENT '分组/控制器/操作名',
  `active_url` varchar(500) DEFAULT '' COMMENT '标记为选中的url',
  `is_userpage` tinyint(1) DEFAULT '0' COMMENT '默认会员首页',
  `sort_order` int(10) DEFAULT '0' COMMENT '排序号',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态，1=显示，0=隐藏',
  `lang` varchar(20) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='会员菜单表';

-- -----------------------------
-- Records of `ey_users_menu`
-- -----------------------------
INSERT INTO `ey_users_menu` VALUES ('1', '个人信息', 'v1', 'user/Users/index', '', '1', '100', '1', 'cn', '1555904190', '1555917737');
INSERT INTO `ey_users_menu` VALUES ('2', '账户充值', 'v1', 'user/Pay/pay_consumer_details', '', '0', '100', '1', 'cn', '1555904190', '1563498414');
INSERT INTO `ey_users_menu` VALUES ('3', '商城中心', 'v1', 'user/Shop/shop_centre', '', '0', '100', '1', 'cn', '1555904190', '1563498415');
INSERT INTO `ey_users_menu` VALUES ('4', '会员升级', 'v1', 'user/Level/level_centre', '', '0', '100', '1', 'cn', '1555904190', '1564555772');
INSERT INTO `ey_users_menu` VALUES ('5', '会员投稿', 'v1', 'user/UsersRelease/release_centre', '', '0', '100', '1', 'cn', '1555904190', '1564555773');
INSERT INTO `ey_users_menu` VALUES ('6', '我的下载', 'v1', 'user/Download/index', '', '0', '100', '1', 'cn', '1590484667', '1602320126');
INSERT INTO `ey_users_menu` VALUES ('7', '个人中心', 'v1', 'user/Users/index', '', '1', '100', '1', 'cn', '1608708057', '1609385363');
INSERT INTO `ey_users_menu` VALUES ('11', '个人中心', 'v2', 'user/Users/index', 'user/Users/index|user/Pay/pay_account_recharge|user/Users/footprint_index|user/Level/level_centre|user/Download/index|user/Users/media_index', '1', '100', '1', 'cn', '1608708057', '1609385363');
INSERT INTO `ey_users_menu` VALUES ('10', '财务明细', 'v1', 'user/Pay/pay_consumer_details', '', '0', '100', '1', 'cn', '1608709000', '1609387813');
INSERT INTO `ey_users_menu` VALUES ('12', '我的信息', 'v2', 'user/Users/info', 'user/Users/info', '0', '100', '1', 'cn', '1608709100', '1609385363');
INSERT INTO `ey_users_menu` VALUES ('13', '我的收藏', 'v2', 'user/Users/collection_index', 'user/Users/collection_index', '0', '100', '1', 'cn', '1608708100', '1609385363');
INSERT INTO `ey_users_menu` VALUES ('14', '财务明细', 'v2', 'user/Pay/pay_consumer_details', 'user/Pay/pay_consumer_details|user/Users/score_index', '0', '100', '1', 'cn', '1608709000', '1609387813');

-- -----------------------------
-- Table structure for `ey_users_money`
-- -----------------------------
DROP TABLE IF EXISTS `ey_users_money`;
CREATE TABLE `ey_users_money` (
  `moneyid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '金额明细表ID',
  `users_id` int(10) DEFAULT '0' COMMENT '会员表ID',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '金额',
  `users_money` decimal(10,2) DEFAULT '0.00' COMMENT '此条记录的账户金额',
  `cause` text COMMENT '事由，暂时在升级消费中使用到，以serialize序列化后存入，用于后续查询。',
  `cause_type` tinyint(1) DEFAULT '0' COMMENT '数据类型，0为消费，1为充值。其余后续添加。',
  `status` tinyint(1) DEFAULT '1' COMMENT '是否成功，默认1，0失败，1未付款，2已付款，3已完成，4订单取消。',
  `pay_method` varchar(50) DEFAULT '' COMMENT '支付方式，wechat为微信支付，alipay为支付宝支付',
  `wechat_pay_type` varchar(20) NOT NULL DEFAULT '' COMMENT '微信支付时，标记使用的支付类型（扫码支付，微信内部，微信H5页面）',
  `pay_details` text COMMENT '支付时返回的数据，以serialize序列化后存入，用于后续查询。',
  `order_number` varchar(30) DEFAULT '' COMMENT '订单号',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`moneyid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='金额明细表';


-- -----------------------------
-- Table structure for `ey_users_notice`
-- -----------------------------
DROP TABLE IF EXISTS `ey_users_notice`;
CREATE TABLE `ey_users_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT '' COMMENT '通知标题',
  `users_id` text NOT NULL COMMENT '用户id',
  `usernames` text NOT NULL COMMENT '用户名字符串',
  `remark` text COMMENT '通知信息',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='站内通知';


-- -----------------------------
-- Table structure for `ey_users_notice_read`
-- -----------------------------
DROP TABLE IF EXISTS `ey_users_notice_read`;
CREATE TABLE `ey_users_notice_read` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `notice_id` int(10) DEFAULT NULL COMMENT '站内信id',
  `is_read` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已读, 1---是, 0---否',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除, 1---是, 0---否',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户已读站内通知';


-- -----------------------------
-- Table structure for `ey_users_notice_tpl`
-- -----------------------------
DROP TABLE IF EXISTS `ey_users_notice_tpl`;
CREATE TABLE `ey_users_notice_tpl` (
  `tpl_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `tpl_name` varchar(200) DEFAULT '' COMMENT '模板名称',
  `tpl_title` varchar(200) DEFAULT '' COMMENT '站内信标题',
  `tpl_content` text COMMENT '发送内容',
  `send_scene` tinyint(1) DEFAULT '0' COMMENT '站内信发送场景(1=留言表单）',
  `is_open` tinyint(1) DEFAULT '0' COMMENT '是否开启使用这个模板，1为是，0为否。',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`tpl_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='站内信模板表';

-- -----------------------------
-- Records of `ey_users_notice_tpl`
-- -----------------------------
INSERT INTO `ey_users_notice_tpl` VALUES ('1', '留言表单', '您有新的留言消息，请到内容管理中查看！', '${content}', '1', '1', 'cn', '1616460912', '1616460912');
INSERT INTO `ey_users_notice_tpl` VALUES ('5', '订单付款', '您有新的待发货订单消息，请到商城订单查看！', '${content}', '5', '1', 'cn', '1616460912', '1616460912');
INSERT INTO `ey_users_notice_tpl` VALUES ('6', '订单发货', '您有新的待收货订单消息，请到会员订单查看！', '${content}', '6', '1', 'cn', '1616460912', '1616460912');

-- -----------------------------
-- Table structure for `ey_users_notice_tpl_content`
-- -----------------------------
DROP TABLE IF EXISTS `ey_users_notice_tpl_content`;
CREATE TABLE `ey_users_notice_tpl_content` (
  `content_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `source` tinyint(1) DEFAULT '0' COMMENT '来源，对应 users_notice_tpl 表 send_scene 字段',
  `admin_id` int(10) DEFAULT '0' COMMENT '管理员ID，不为空则表示管理员接收信息',
  `users_id` int(10) DEFAULT '0' COMMENT '用户ID，不为空则表示会员接收信息，暂未使用',
  `content_title` varchar(200) DEFAULT '' COMMENT '通知标题',
  `content` text COMMENT '接收的通知内容',
  `is_read` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已读，默认0，1是，0否',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`content_id`),
  KEY `admin_id` (`admin_id`) USING BTREE,
  KEY `users_id` (`users_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='站内信发送接收记录表';


-- -----------------------------
-- Table structure for `ey_users_parameter`
-- -----------------------------
DROP TABLE IF EXISTS `ey_users_parameter`;
CREATE TABLE `ey_users_parameter` (
  `para_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `dtype` varchar(32) NOT NULL DEFAULT '' COMMENT '字段类型',
  `dfvalue` varchar(1000) NOT NULL DEFAULT '' COMMENT '默认值',
  `is_system` tinyint(1) DEFAULT '0' COMMENT '是否为系统属性，系统属性不可删除，1为是，0为否，默认0。',
  `is_hidden` tinyint(1) DEFAULT '0' COMMENT '是否禁用属性，1为是，0为否',
  `is_required` tinyint(1) DEFAULT '0' COMMENT '是否为必填属性，1为是，0为否，默认0。',
  `is_reg` tinyint(1) DEFAULT '1' COMMENT '是否为注册表单，1为是，0为否',
  `sort_order` smallint(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `lang` varchar(50) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`para_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='会员属性表(字段)';

-- -----------------------------
-- Records of `ey_users_parameter`
-- -----------------------------
INSERT INTO `ey_users_parameter` VALUES ('1', '手机号码', 'mobile_1', 'mobile', '', '1', '0', '0', '1', '1', 'cn', '0', '1591957363');
INSERT INTO `ey_users_parameter` VALUES ('2', '邮箱地址', 'email_2', 'email', '', '1', '0', '1', '1', '1', 'cn', '0', '1591957363');

-- -----------------------------
-- Table structure for `ey_users_score`
-- -----------------------------
DROP TABLE IF EXISTS `ey_users_score`;
CREATE TABLE `ey_users_score` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '积分明细表',
  `type` tinyint(1) DEFAULT '1' COMMENT '类型:1-提问,2-回答,3-最佳答案4-悬赏退回,5-每日签到',
  `users_id` int(10) DEFAULT '0' COMMENT '用户id',
  `ask_id` int(10) DEFAULT '0' COMMENT '问题id',
  `reply_id` int(10) DEFAULT '0' COMMENT '回答id',
  `score` int(10) DEFAULT '0' COMMENT '积分',
  `devote` int(10) DEFAULT '0' COMMENT '贡献值,同score',
  `money` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `info` varchar(255) DEFAULT '' COMMENT '说明',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(10) DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='积分详情表';

-- -----------------------------
-- Records of `ey_users_score`
-- -----------------------------
INSERT INTO `ey_users_score` VALUES ('1', '5', '1', '0', '0', '3', '3', '0.00', '每日签到', 'cn', '1610350037', '1610350037');

-- -----------------------------
-- Table structure for `ey_users_signin`
-- -----------------------------
DROP TABLE IF EXISTS `ey_users_signin`;
CREATE TABLE `ey_users_signin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `users_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `lang` varchar(50) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '签到时间',
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户签到表';

-- -----------------------------
-- Records of `ey_users_signin`
-- -----------------------------
INSERT INTO `ey_users_signin` VALUES ('1', '1', 'cn', '1610350037');

-- -----------------------------
-- Table structure for `ey_users_type_manage`
-- -----------------------------
DROP TABLE IF EXISTS `ey_users_type_manage`;
CREATE TABLE `ey_users_type_manage` (
  `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `type_name` varchar(30) DEFAULT '' COMMENT '类型名称',
  `level_id` int(10) DEFAULT '0' COMMENT '会员等级ID',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `limit_id` int(10) DEFAULT '0' COMMENT '会员期限限制，存储ID，值对应常量表的admin_member_limit_arr数组',
  `activity` varchar(30) DEFAULT '' COMMENT '活动文案',
  `sort_order` smallint(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `lang` varchar(20) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='会员产品类型表';

-- -----------------------------
-- Records of `ey_users_type_manage`
-- -----------------------------
INSERT INTO `ey_users_type_manage` VALUES ('1', '升级为中级会员', '2', '100.00', '2', '', '100', 'cn', '1564532901', '1610620458');
INSERT INTO `ey_users_type_manage` VALUES ('2', '升级为高级会员', '3', '200.00', '3', '', '100', 'cn', '1564532901', '1610620458');

-- -----------------------------
-- Table structure for `ey_weapp`
-- -----------------------------
DROP TABLE IF EXISTS `ey_weapp`;
CREATE TABLE `ey_weapp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT '' COMMENT '插件标识',
  `name` varchar(55) DEFAULT '' COMMENT '中文名字',
  `config` text COMMENT '配置信息',
  `data` text COMMENT '额外序列化存储数据，简单插件可以不创建表，存储这里即可',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态：0=未安装，1=启用，-1=禁用',
  `tag_weapp` tinyint(1) DEFAULT '1' COMMENT '1=自动绑定，2=手工调用。关联模板标签weapp，自动调用内置的show钩子方法',
  `thorough` tinyint(1) DEFAULT '0' COMMENT '彻底卸载：0=是，1=否',
  `position` varchar(30) DEFAULT 'default' COMMENT '插件位置',
  `is_buy` tinyint(1) DEFAULT '0' COMMENT '0-本地,1-线上购买 2-线上购买,但已删除,不显示在我的插件列表',
  `is_upgrade` tinyint(1) DEFAULT '1' COMMENT '是否提示升级',
  `sort_order` int(10) DEFAULT '100' COMMENT '排序号',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='插件应用表';

-- -----------------------------
-- Records of `ey_weapp`
-- -----------------------------
INSERT INTO `ey_weapp` VALUES ('4', 'Systemdoctor', '易优系统助手', '{\"code\":\"Systemdoctor\",\"name\":\"\\u6613\\u4f18\\u7cfb\\u7edf\\u52a9\\u624b\",\"version\":\"v1.1.0\",\"min_version\":\"1.0.0\",\"author\":\"\\u533f\\u540d\",\"litpic\":\"https:\\/\\/www.eyoucms.com\\/uploads\\/200904\\/5-200Z40U91c09.jpg\",\"description\":\"\\u3010\\u63d2\\u4ef6\\u8bf4\\u660e\\u3011 \\u6613\\u4f18\\u7cfb\\u7edf\\u533b\\u751f \\u63d2\\u4ef6\\u4e3b\\u8981\\u65b9\\u4fbf\\u7528\\u6237 \\u8bca\\u65ad\\u7cfb\\u7edf\\uff0c\\u81ea\\u884c\\u4fee\\u590d\\u7591\\u96be\\u6742\\u75c7\\uff0c\\u53ca\\u4e00\\u4e9bsql\\u64cd\\u4f5c\\u529f\\u80fd\\uff0c\\u540e\\u7eed\\u6301\\u7eed\\u66f4\\u65b0\\u589e\\u52a0\\u5b89\\u5168\\u3001\\u7ef4\\u62a4\\u65b9\\u9762\\u529f\\u80fd\\uff0c\\u65b9\\u4fbf\\u7528\\u6237\\u9632\\u62a4\\u597d\\u81ea\\u5df1\\u7684\\u7f51\\u7ad9\",\"scene\":\"0\",\"permission\":[]}', '', '0', '1', '0', 'default', '2', '1', '100', '1610351528', '1610351528');

-- -----------------------------
-- Table structure for `ey_wx_users`
-- -----------------------------
DROP TABLE IF EXISTS `ey_wx_users`;
CREATE TABLE `ey_wx_users` (
  `wxuser_id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `openid` varchar(100) NOT NULL DEFAULT '' COMMENT 'openid',
  `nickname` varchar(100) NOT NULL DEFAULT '' COMMENT '微信昵称',
  `headimgurl` varchar(200) NOT NULL DEFAULT '' COMMENT '头像',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`wxuser_id`),
  KEY `openid` (`openid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信小程序用户表';

