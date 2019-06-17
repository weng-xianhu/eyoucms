-- ----------------------------------------
-- EyouCms MySQL Data Transfer 
-- 
-- Server         : 127.0.0.1_3306
-- Server Version : 5.5.53
-- Host           : 127.0.0.1:3306
-- Database       : e1
-- 
-- Part : #1
-- Version : #v1.3.6
-- Date : 2019-06-08 22:34:47
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
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='广告表';

-- -----------------------------
-- Records of `ey_ad`
-- -----------------------------
INSERT INTO `ey_ad` VALUES ('1', '1', '1', '共展蓝图', 'http://www.eyoucms.com', '/uploads/allimg/20190320/ef04db4998198aa3dfdf3e503ae9f22e.jpg', '1524215594', '0', '&lt;p&gt;填写广告的备注信息，方便于后期的跟进&lt;/p&gt;', '', '', '', '0', '', '1', '100', '0', '0', '0', 'cn', '1524215652', '1553046530');
INSERT INTO `ey_ad` VALUES ('2', '1', '1', '易优模板库', 'http://www.eyoucms.com', '/uploads/allimg/20190320/def47fdd6212ad6f7f037b9dbb9cd63e.jpg', '0', '0', '&lt;p&gt;填写广告的备注信息，方便于后期的跟进&lt;/p&gt;', '', '', '', '0', '', '1', '100', '0', '0', '0', 'cn', '1524214017', '1553046638');
INSERT INTO `ey_ad` VALUES ('3', '2', '1', 'en共展蓝图', 'http://www.eyoucms.com', '/uploads/allimg/20190320/ef04db4998198aa3dfdf3e503ae9f22e.jpg', '1524215594', '0', '&lt;p&gt;填写广告的备注信息，方便于后期的跟进&lt;/p&gt;', '', '', '', '0', '', '1', '100', '0', '0', '0', 'en', '1524215652', '1527824535');
INSERT INTO `ey_ad` VALUES ('4', '2', '1', 'en易优模板库', 'http://www.eyoucms.com', '/uploads/allimg/20190320/def47fdd6212ad6f7f037b9dbb9cd63e.jpg', '0', '0', '&lt;p&gt;填写广告的备注信息，方便于后期的跟进&lt;/p&gt;', '', '', '', '0', '', '1', '100', '0', '0', '0', 'en', '1524214017', '1531724625');
INSERT INTO `ey_ad` VALUES ('5', '1', '1', '第三组广告', 'http://www.eyoucms.com', '/uploads/allimg/20190320/364ec7c31e6068cd49afb2e84445fb0c.jpg', '0', '0', '', '', '', '', '0', '', '1', '100', '1', '1', '0', 'cn', '1553046945', '1553046945');
INSERT INTO `ey_ad` VALUES ('6', '2', '1', '第三组广告', 'http://www.eyoucms.com', '/uploads/allimg/20190320/364ec7c31e6068cd49afb2e84445fb0c.jpg', '0', '0', '', '', '', '', '0', '', '1', '100', '1', '1', '0', 'en', '1553046945', '1553046945');

-- -----------------------------
-- Table structure for `ey_ad_position`
-- -----------------------------
DROP TABLE IF EXISTS `ey_ad_position`;
CREATE TABLE `ey_ad_position` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL DEFAULT '' COMMENT '广告位置名称',
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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='广告位置表';

-- -----------------------------
-- Records of `ey_ad_position`
-- -----------------------------
INSERT INTO `ey_ad_position` VALUES ('1', '首页-大幻灯片', '1920', '550', '广告图片的宽高度随着浏览器大小而改变', '1', 'cn', '0', '0', '1524209276', '1524209365');
INSERT INTO `ey_ad_position` VALUES ('2', 'en首页-大幻灯片', '1920', '550', '广告图片的宽高度随着浏览器大小而改变', '1', 'en', '0', '0', '1524209276', '1524209365');

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
  `last_login` int(11) DEFAULT '0' COMMENT '最后登录时间',
  `last_ip` varchar(15) DEFAULT '' COMMENT '最后登录ip',
  `login_cnt` int(11) DEFAULT '0' COMMENT '登录次数',
  `session_id` varchar(50) DEFAULT '' COMMENT 'session_id',
  `parent_id` int(10) DEFAULT '0' COMMENT '父管理员ID',
  `role_id` int(10) NOT NULL DEFAULT '-1' COMMENT '角色组ID（-1表示超级管理员）',
  `mark_lang` varchar(50) DEFAULT 'cn' COMMENT '当前语言标识',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态(0=屏蔽，1=正常)',
  `add_time` int(11) DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`admin_id`),
  KEY `user_name` (`user_name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='管理员表';

-- -----------------------------
-- Records of `ey_admin`
-- -----------------------------
INSERT INTO `ey_admin` VALUES ('1', 'admin', '', 'admin', '', '', '7959ec68e999edd0380ff0809f76fa42', '1557733856', '127.0.0.1', '2', 'oe2ohqhhaf24g1quug2lgiml36', '0', '-1', 'cn', '1', '1557733827', '0');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='管理员操作日志表';


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
  `litpic` varchar(250) DEFAULT '' COMMENT '封面图片',
  `is_head` tinyint(1) DEFAULT '0' COMMENT '头条（0=否，1=是）',
  `is_special` tinyint(1) DEFAULT '0' COMMENT '特荐（0=否，1=是）',
  `is_top` tinyint(1) DEFAULT '0' COMMENT '置顶（0=否，1=是）',
  `is_recom` tinyint(1) DEFAULT '0' COMMENT '推荐（0=否，1=是）',
  `is_jump` tinyint(1) DEFAULT '0' COMMENT '跳转链接（0=否，1=是）',
  `is_litpic` tinyint(1) DEFAULT '0' COMMENT '图片（0=否，1=是）',
  `author` varchar(200) DEFAULT '' COMMENT '作者',
  `click` int(10) DEFAULT '0' COMMENT '浏览量',
  `arcrank` tinyint(1) DEFAULT '0' COMMENT '阅读权限：0=开放浏览，-1=待审核稿件',
  `jumplinks` varchar(200) DEFAULT '' COMMENT '外链跳转',
  `ismake` tinyint(1) DEFAULT '0' COMMENT '是否静态页面（0=动态，1=静态）',
  `seo_title` varchar(200) DEFAULT '' COMMENT 'SEO标题',
  `seo_keywords` varchar(200) DEFAULT '' COMMENT 'SEO关键词',
  `seo_description` text COMMENT 'SEO描述',
  `users_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '会员价',
  `prom_type` tinyint(1) unsigned DEFAULT '0' COMMENT '产品类型：0普通产品，1虚拟产品',
  `tempview` varchar(200) DEFAULT '' COMMENT '文档模板文件名',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态(0=屏蔽，1=正常)',
  `sort_order` int(10) DEFAULT '0' COMMENT '排序号',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `admin_id` int(10) DEFAULT '0' COMMENT '管理员ID',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '伪删除，1=是，0=否',
  `del_method` tinyint(1) DEFAULT '0' COMMENT '伪删除状态，1为主动删除，2为跟随上级栏目被动删除',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`aid`),
  KEY `aid` (`typeid`,`channel`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=utf8 COMMENT='文档主表';

-- -----------------------------
-- Records of `ey_archives`
-- -----------------------------
INSERT INTO `ey_archives` VALUES ('1', '1', '6', '0', '关于我们', '', '0', '0', '0', '0', '0', '0', '', '0', '0', '', '0', '', '', '', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1526539465', '1527836335');
INSERT INTO `ey_archives` VALUES ('2', '8', '6', '0', '公司简介', '', '0', '0', '0', '0', '0', '0', '', '0', '0', '', '0', '', '', '', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1526540452', '1527836706');
INSERT INTO `ey_archives` VALUES ('3', '13', '6', '0', '单页面', '', '0', '0', '0', '0', '0', '0', '', '4', '0', '', '0', '', '', '', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1526540573', '1531710225');
INSERT INTO `ey_archives` VALUES ('4', '11', '1', '1', 'seo是什么？', '/uploads/allimg/20190114/716fd8917b02cf71176639d7ad69da9c.png', '0', '0', '0', '0', '0', '1', '', '131', '0', '', '0', '', '', '在了解seo是什么意思之后，才能学习seo。什么是seo，从官方解释来看，seo=Search（搜索）Engine（引擎）Optimization（优化），即搜索引擎优化。使用过百度或其他搜索引擎，在', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1526545072', '1547462541');
INSERT INTO `ey_archives` VALUES ('39', '12', '1', '0', '回顾中国饮料40年发展史，总有一款是你儿时记忆的味道', '', '0', '0', '0', '0', '0', '0', '', '178', '0', '', '0', '', '', '对于记忆来说，味道往往是最美的，儿时喝过的饮料，至今回想起来依然觉得津津有味。今天是六一儿童节，青山资本梳理了中国40年来饮料发展的简史，权当节日的小消遣，顺便看看能否找到你记忆深处的那个味道？第一阶段：国人味蕾的开启时代百事可乐在华第一家工厂开业1981年，可口可乐在中国第一条生产线正式投产，主要供应旅游饭店，卖给外国人收取外汇，百事可乐也在深圳建立了第一家罐装厂。1982年，国家把饮料纳入“国家计划管理产品”，可口可乐开始在北京市场进行内销。', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1527824652', '1531709817');
INSERT INTO `ey_archives` VALUES ('9', '10', '1', '0', '用户界面设计和体验设计的差别', '', '0', '0', '0', '0', '0', '0', '', '169', '0', '', '0', '', '', '有时候我们需要获取图集中的第一张图片，下面给出解决办法： 第一步：修改include/extend.func.php 添加  // 提取图集第一张大图', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1526552582', '1531711820');
INSERT INTO `ey_archives` VALUES ('10', '10', '1', '0', '新手科普文！什么是用户界面和体验设计？', '', '0', '0', '0', '0', '0', '0', '', '130', '0', '', '0', '', '', '在仿站时，我们常常会自定义很多字段，那么如何在首页调用呢，下面给出方法：一、指定channelid属性（channelid=\'17\' 17是指内容模型里面指定的模型ID) 二、指定要调用出来的字段ad', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1526552685', '1531711845');
INSERT INTO `ey_archives` VALUES ('12', '10', '1', '1', '一文读懂互联网女皇和她的报告：互联网领域的投资圣经、选股指南', '/uploads/allimg/20190114/c0ee7f49917d9e1a8f834b625970a9d0.png', '0', '0', '0', '0', '0', '1', '', '267', '0', '', '0', '', '', '北京时间 5 月 31 日凌晨，有“互联网女皇”之称的玛丽·米克尔发布了 2018 年的互联网趋势报告，这也是她第 23 年公布互联网报告。\r\n每年的互联网女皇报告几乎都会成为每个互联网创业者的必读报告。那么，互联网女皇是谁?为什么她的报告会如此受关注呢?', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1526552714', '1547462473');
INSERT INTO `ey_archives` VALUES ('13', '12', '1', '0', '网站建设的五大核心要素', '/uploads/allimg/20190114/d09702880ccf6d5ab5893c3116f1a5f0.jpg', '0', '0', '0', '0', '0', '1', '', '160', '0', '', '0', 'SEO标', 'O关键', 'SEO描述', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1526608216', '1547462639');
INSERT INTO `ey_archives` VALUES ('14', '10', '1', '0', '网站建设，静态页面和动态页面如何选择', '/uploads/allimg/20190114/1621fb9e84a97e78b1c1cac6ec6b37bd.png', '0', '0', '0', '0', '0', '1', '', '150', '0', '', '0', '', '', '网站建设，静态页面和动态页面如何选择　　电商网站建设为什么要使用静态页面制作。我们都知道，网站制作有分为静态页面制作和动态网页制作，那么建设电商网站采用哪种网站设计技术更好呢?　　我们建设网站最终目的', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1526609496', '1547462449');
INSERT INTO `ey_archives` VALUES ('19', '12', '1', '0', '从三方面完美的体验企业网站的核心价值', 'http://www.eyoucms.com/uploads/allimg/180426/150RQ155-0.jpg', '0', '0', '0', '0', '0', '1', '', '146', '0', '', '0', '', '', '从三方面完美的体验企业网站的核心价值　　随着互联网的迅猛发展，一个企业的发展离不开互联网的发展，企业注重企业网站建设，那么必然会给其带来不错的效果。企业网站建设其核心价值直接体现在网站对于用户和商家而', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1526610848', '1526610848');
INSERT INTO `ey_archives` VALUES ('20', '11', '1', '0', 'CMS是如何应运而生的？', '', '0', '0', '0', '0', '0', '0', '', '178', '0', '', '0', '', '', '随着网络应用的丰富和发展，很多网站往往不能迅速跟进大量信息衍生及业务模式变革的脚步，常常需要花费许多时间、人力和物力来处理信息更新和维护工作；遇到网站扩充的时候，整合内外网及分支网站的工作就变得更加复', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1526611606', '1527557542');
INSERT INTO `ey_archives` VALUES ('21', '11', '1', '0', '网站设计与SEO的关系，高手是从这4个维度分析的！', '', '0', '0', '0', '0', '0', '0', '', '289', '0', '', '0', '', '', 'SEO（搜索引擎优化）和有效的网站设计是齐头并进的。好的网站设计是关于创建一个吸引目标受众的网站，并让他们采取某种行动。但是，如果该网站不遵循目前的SEO最佳做法，它的排名将会受到影响，从而会导致真正', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1526611744', '1531709637');
INSERT INTO `ey_archives` VALUES ('22', '23', '3', '0', '新闻模型下的图集', '/uploads/allimg/20190114/e00be6cd175d973a064bbd42f76bd7cf.jpg', '0', '0', '0', '0', '0', '1', '', '193', '0', '', '0', '', '', '新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1526612277', '1547462752');
INSERT INTO `ey_archives` VALUES ('23', '23', '3', '0', '新闻模型下的图集二', '/uploads/allimg/20190114/5c9151abf9ab18003f8cc47887bc2b5c.jpg', '0', '0', '0', '0', '0', '1', '', '289', '0', '', '0', '', '', '新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1526612316', '1547462702');
INSERT INTO `ey_archives` VALUES ('27', '24', '2', '0', '华为HUAWEI NOTE 8', '/uploads/allimg/20190319/ef3caff1fe91f367fe4939d664a8a5da.jpg', '0', '0', '0', '0', '0', '1', '', '289', '0', '', '0', '', '', '全向录音/指向回放、定向免提、指关节手势、分屏多窗口、语音控制、情景智能、单手操作、杂志锁屏、手机找回、无线WIFI打印、学生模式、多屏互动、运动健康全向录音/指向回放、定向免提、指关节手势、分屏多窗', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1526613043', '1552987268');
INSERT INTO `ey_archives` VALUES ('28', '26', '2', '0', '小米笔记本Air 13.3', '/uploads/allimg/20190114/66109e989148356eadb4ff1eee285826.jpg', '0', '0', '0', '0', '0', '1', '', '149', '0', '', '0', '', '', '轻薄全金属机身/256GBSSD/第八代Intel酷睿i5处理器/FHD全贴合屏幕/指纹解锁/office激活不支持7天无理由退货...', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1526613271', '1547462162');
INSERT INTO `ey_archives` VALUES ('29', '27', '2', '0', ' 小米蓝牙项圈耳机', '/uploads/allimg/20190114/252a53e6fbc8f441b2570f755d2bbeb8.jpg', '0', '0', '0', '0', '0', '1', '', '211', '0', '', '0', '', '', '特性M3平板定制AKG品牌高保真耳机，配合M3平板享受HiFi音质...', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1526613739', '1552992589');
INSERT INTO `ey_archives` VALUES ('30', '5', '4', '0', '工程机械推土挖掘机类网站模板', '/uploads/allimg/20190114/4873105f54a14f3785047bd8ecc8b5ac.jpg', '0', '0', '0', '0', '0', '1', '', '243', '0', '', '0', '', '', '宅男女神一号，多懂得...', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1526614069', '1547463959');
INSERT INTO `ey_archives` VALUES ('31', '5', '4', '0', '职业教育培训机构网站模板', '', '0', '0', '0', '0', '0', '0', '', '168', '0', '', '0', '', '', '宅男女神二号种子，高手多是不懂的...', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1526614168', '1547463404');
INSERT INTO `ey_archives` VALUES ('40', '12', '1', '0', '社交媒体时代，如何对粉丝估值？', '/uploads/allimg/20190114/0e4da9d3f8f983a36b7357b97ef4adad.jpg', '0', '0', '0', '0', '0', '1', '', '180', '0', '', '0', '', '', '约翰·奎尔奇说，社交媒体有很多营销挑战，如何为粉丝来估值是一个大问题。从营销角度来思考，要关注强纽带和弱纽带。你可能以为，强纽带的密友产生最大的营销影响，研究发现不是这样的，产生更大的影响反而是跟你更疏远的人。演讲者｜约翰·奎尔奇（哈佛商学院教授，曾任伦敦商学院院长、中欧国际工商学院副院长）非常感谢大家在周日早上回来听我讲课。对于你们这些创业者，或者希望成为创业者的人，我今天准备了一个特别的讲座。很多创业者没有把最终愿景很好界定，所以每天都忙于灭火，忙于生存。创业营销，你必须做好规划今天将从创业营销这个话题开始，包括你如何生存和成功。创业营销包括四个关键领域，你必须很好地去规划：要有正确的目标客户和最终用户；要有正确的产品和服务要有一个非常好的人才团队，使得商业创意能够实现；要有好的合作伙伴，不是分销商，而是会计、律师等服务伙伴。那么，何为创业营销？？第一，这是从愿景到行动的逆向工程设计当星巴克只有5家店时，创始人就有一个愿景，让星巴克成为你生活中的第三空间。对创业者要从愿景开始，向后进行逆向工程的设计：看一下需要有什么样的行动，才能实现愿景。很多创业者没有把最终愿景很好界定，所以每天', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1527824837', '1547462596');
INSERT INTO `ey_archives` VALUES ('37', '24', '2', '0', '华为无线快充手机', '/uploads/allimg/20190319/8a405e72e2acf9c5a29da7341a0eff89.jpg', '0', '0', '0', '0', '0', '1', '', '300', '0', '', '0', '', '', '全身都是科技亮点！7nm麒麟芯片，问鼎性能巅峰，4000万超广角徕卡三摄，随手捕捉大场面', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1527507844', '1552986270');
INSERT INTO `ey_archives` VALUES ('38', '11', '1', '0', '商梦网校：单页SEO站群技术，用10个网站优化排名！', 'http://www.eyoucms.com/uploads/allimg/180505/3-1P505101H3447.png', '0', '0', '0', '0', '0', '1', '', '113', '0', '', '0', '', '', 'SEO很多伙伴都了解，就是搜索引擎排名优化，通过对网站内部和外部进行优化当用户搜索相应关键词时网站能够排名在搜索引擎前面，具体可以百度搜索“网络营销课程”查看商梦网校操作的案例！但单页SEO很多伙伴可能会有点陌生，单页SEO是将单页网站与内容内容结合为一体的SEO优化方案，主要是提升网站流量利用率让用户打开网站就能看到目标页面，转换更多订单，创造更多收益。单页SEO的操作理念也是由商梦网校提出，并一起推荐操作大家的模式。那什么又是单页SEO站群呢，因为操作SEO成功率并不是100%，也就是意味着你做了并不会绝对有排名。因为在任何时候搜索引擎，特别是百度的索引数据库里，只有60%的网页数量。也就是说，大量的网页它是没有收录进来，它本身的能力所限无法做到中文的所有几百亿个网页都收录进来。所以，对于大部分网站，都有被删除网页，没有排名，或被K的经历，或没有排名。处理办法：坦然面对这一切。一个网站的成本才多少钱？如果因此对SEO失去信心，那就是最大的失去了。不过我们也想到了一个更好的解决方案，这个方案在最早期我们开始操作，并且取得了非常不错的成绩就是“站群”，我们可以假设一个网站排名的机会为1', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1527555069', '1531709578');
INSERT INTO `ey_archives` VALUES ('41', '12', '1', '0', '《颠覆营销:大数据时代的商业革命》：大数据“多即少，少即多”', '', '0', '0', '0', '0', '0', '0', '', '184', '0', '', '0', '', '', '各种行销手段早已令人眼花缭乱，但究其本质都是在研究客户（消费者），研究客户的所想、所需，使产品或服务有的放矢。大数据时代又给它赋予了新名词：精准营销。大数据最先应用的领域多为面对客户的行业，最先应用的情景也多为精准营销。“酒好也怕巷子深”，产品或服务的信息要送达客户才可能促成交易。一般认为，向客户传达产品或服务信息要靠广告。广告古已有之，“三碗不过岗”的酒幌子就是广告。没有互联网的时代，我们熟悉的是电视广告、广播广告、印刷品平面广告、户外广告牌等，当然，也包括吆喝叫卖。但过去的广告是千人一面、不区分受众的。后来商家对客户的信息有所采集就有了CRM，经过客户分类，可以更好地服务于不同的客户群体。互联网+大数据时代让CRM有了新的发展机遇，管理客户不再是简单的数字统计和没有个性的（或简单聚类的）直邮、定投。随着商家对客户知道更多、了解更深，便有机会为客户提供个性化的营销方案，进一步改善客户体验，成为了个性化营销或叫精准营销。大数据时代，让很多过去的不可能变为可能，营销活动也赢来了新的发展机遇。时代不同，商业经营的形式会变化，但本质就是两件事：开源，节流。开源是开拓新客户，发现新商机；节流是', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1527825125', '1527825125');
INSERT INTO `ey_archives` VALUES ('42', '4', '3', '0', '客户案例一', '/uploads/allimg/20190114/ada4963a2dba414c6a5d2f4d983d5cfb.jpg', '0', '0', '0', '0', '0', '1', '', '255', '0', '', '0', '', '', '', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1531731387', '1547462847');
INSERT INTO `ey_archives` VALUES ('43', '4', '3', '0', '客户案例二', '/uploads/allimg/20190114/a5552ebb7d08351c8ab5a39596c34db1.jpg', '0', '0', '0', '0', '0', '1', '', '269', '0', '', '0', '', '', '', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1531732591', '1547462829');
INSERT INTO `ey_archives` VALUES ('44', '4', '3', '0', '客户案例三', '/uploads/allimg/20190114/5e82997411262b71dc9b34bf2ce96dff.jpg', '0', '0', '0', '0', '0', '1', '', '291', '0', '', '0', '', '', '', '0.00', '0', '', '1', '100', 'cn', '0', '0', '0', '1531732811', '1547462806');
INSERT INTO `ey_archives` VALUES ('45', '31', '6', '0', 'About Us', '', '0', '0', '0', '0', '0', '0', '', '0', '0', '', '0', '', '', '', '0.00', '0', '', '1', '100', 'en', '0', '0', '0', '1545267531', '1545272580');
INSERT INTO `ey_archives` VALUES ('46', '32', '6', '0', 'Company Profile', '', '0', '0', '0', '0', '0', '0', '', '0', '0', '', '0', '', '', '', '0.00', '0', '', '1', '100', 'en', '0', '0', '0', '1545267531', '1545272565');
INSERT INTO `ey_archives` VALUES ('47', '39', '6', '0', 'Single page', '', '0', '0', '0', '0', '0', '0', '', '0', '0', '', '0', '', '', '', '0.00', '0', '', '1', '100', 'en', '0', '0', '0', '1545267531', '1545270877');
INSERT INTO `ey_archives` VALUES ('48', '52', '4', '0', 'Construction machinery bulldozer website template', 'http://www.eyoucms.com/uploads/allimg/180426/150RQ155-0.jpg', '0', '0', '0', '0', '0', '1', '', '163', '0', '', '0', '', '', 'Construction machinery bulldozer website template, download address：http://www.eyoucms.com/moban/16/668.html', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545268101', '1547463855');
INSERT INTO `ey_archives` VALUES ('49', '52', '4', '0', 'Website Template of Vocational Education and Training Institutions', '', '0', '0', '0', '0', '0', '0', '', '135', '0', '', '0', '', '', 'Web template for vocational education and training institutions, download address: http://www.eyoucms.com/moban/10/673.html', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545268178', '1545268305');
INSERT INTO `ey_archives` VALUES ('50', '51', '3', '0', 'Customer Case I', '/uploads/allimg/20181220/a0efe0c4c085eb9d900d580a2fc8b60d.jpg', '0', '0', '0', '0', '0', '1', '', '181', '0', '', '0', '', '', '', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545268344', '1545268466');
INSERT INTO `ey_archives` VALUES ('51', '51', '3', '0', 'Customer Case II', '/uploads/allimg/20181220/d7a2dfa72e8fad4b8c05ec5a2fb299b5.jpg', '0', '0', '0', '0', '0', '1', '', '220', '0', '', '0', '', '', '', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545268484', '1545268514');
INSERT INTO `ey_archives` VALUES ('52', '51', '3', '0', 'Customer Case III', '/uploads/allimg/20181220/3139826d5d18b0143de1f0888fcc12dc.jpg', '0', '0', '0', '0', '0', '1', '', '197', '0', '', '0', '', '', '', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545268516', '1545268563');
INSERT INTO `ey_archives` VALUES ('53', '48', '2', '0', 'Millet Bluetooth Necklace Headset', '/uploads/allimg/20181220/ed065072e34504da0ff9eb9c104f2d07.jpg', '0', '0', '0', '0', '0', '1', '', '201', '0', '', '0', '', '', 'Feature M3 Tablet Customized AKG Brand High Fidelity Headphones, with M3 Tablet Enjoy HiFi Sound Quality...', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545268730', '1545270011');
INSERT INTO `ey_archives` VALUES ('54', '46', '2', '0', 'Millet notebook Air 13.3', '/uploads/allimg/20181220/bb7a7206605841a2db4af30ba3585506.jpg', '0', '0', '0', '0', '0', '1', '', '295', '0', '', '0', '', '', 'Light and thin all-metal fuselage / 256GBSSD / 8th generation Intel Core i5 processor / FHD full-fit screen / fingerprint unlock / Office activation does not support 7 days without reason to return goods...', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545270060', '1545270139');
INSERT INTO `ey_archives` VALUES ('55', '43', '2', '0', 'HUAWEI NOTE 8', '/uploads/allimg/20181220/c63c2b41520e21784fdb272abfc77453.jpg', '0', '0', '0', '0', '0', '1', '', '278', '0', '', '0', '', '', 'Omnidirectional recording/pointing playback, directional hands-free, finger joint gesture, split screen multi-window, voice control, situational intelligence, single-handed operation, magazine lock screen, mobile phone retrieval, wireless WIFI printing, student mode, multi-screen interaction, sports health omnidirectional recording/pointing playback, directional hands-free, finger joint gesture, split screen multi-window', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545270266', '1545270361');
INSERT INTO `ey_archives` VALUES ('56', '43', '2', '0', 'Apple iPhone 6s 16GB ', '/uploads/allimg/20181220/ebbf9bd058529bfd47d2830835427e57.jpg', '0', '0', '0', '0', '0', '1', '', '175', '0', '', '0', '', '', '', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545270365', '1545270643');
INSERT INTO `ey_archives` VALUES ('57', '40', '3', '0', 'Atlas under News Model', '/uploads/allimg/20181220/d543bd229a479794186ec78b42753d6e.jpg', '0', '0', '0', '0', '0', '1', '', '228', '0', '', '0', '', '', '', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545270757', '1545270783');
INSERT INTO `ey_archives` VALUES ('58', '40', '3', '0', 'Atlas II under News Model', '/uploads/allimg/20181220/3dd707090ed329fb39e75c48292b7923.jpg', '0', '0', '0', '0', '0', '1', '', '276', '0', '', '0', '', '', '', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545270788', '1545270816');
INSERT INTO `ey_archives` VALUES ('59', '38', '1', '0', 'Five Core Elements of Website Construction', '/uploads/allimg/20181220/39229581e20d2e22e750df528a43fe71.jpg', '0', '0', '0', '0', '0', '1', '', '245', '0', '', '0', '', '', 'Five Core Elements of Website Construction　　In order to implement network marketing, enterprises need to make websites first. Website is composed of many Web pages, and the design of these pages directly affects whether the website can be welcomed by users. To judge the design of a homepage, we should consider it comprehensively from many aspects, not only whether it is vividly and beautifully designed, but also whether the website can be considered for users to the greatest extent.　　3. Product ', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545270896', '1547463745');
INSERT INTO `ey_archives` VALUES ('60', '38', '1', '0', 'Experience the Core Value of Enterprise Website Perfectly from Three Aspects', '/uploads/allimg/20181220/4d466f39dde9e64d762cd50b51566286.jpg', '0', '0', '0', '0', '0', '1', '', '226', '0', '', '0', '', '', 'Experience the Core Value of Enterprise Website Perfectly from Three Aspects　　With the rapid development of the Internet, the development of an enterprise can not be separated from the development of the Internet. Enterprises pay attention to the construction of enterprise websites, then it will inevitably bring good results. The core value of enterprise website construction is directly reflected in whether the website can satisfy the interests and needs of users and businessmen, improve the dev', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545270965', '1545270965');
INSERT INTO `ey_archives` VALUES ('61', '38', '1', '0', 'Looking back on the 40-year history of beverages in China, there is always a taste of your childhood', '', '0', '0', '0', '0', '0', '0', '', '244', '0', '', '0', '', '', 'Looking back on the 40-year history of Chinese beverage development, there is always a taste of your childhood memory. For memory, the taste is often the most beautiful. The beverage you drank in childhood is still enjoyable in retrospect.Today is the June 1 children&#39;s day. Qingshan capital has compiled a brief history of China&#39;s beverage development in the past 40 years, and it can be a holiday pastime. By the way, can you find the smell in your memory?The First Stage: The Opening Age o', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545271038', '1545271038');
INSERT INTO `ey_archives` VALUES ('62', '38', '1', '0', 'How to value fans in the age of social media?', '/uploads/allimg/20181220/040eb84875a8067b81c27177cd7ec95b.jpg', '0', '0', '0', '0', '0', '1', '', '215', '0', '', '0', '', '', 'John Quilch says that social media has many marketing challenges, and how to value fans is a big problem. From the marketing point of view, we should pay attention to strong ties and weak ties. You may think that strong bonds have the greatest marketing impact. Research has found that it&#39;s not like this, but people who are more distant from you.Speaker John Quilch(Professor of Harvard Business School, former Dean of London Business School and Vice Dean of CEIBS)Thank you very much for coming', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545271071', '1545271071');
INSERT INTO `ey_archives` VALUES ('63', '38', '1', '0', '&quot;Subversive Marketing: Business Revolution in the Big Data Age&quot;: Big Data &quot;More Is Less, Less Is Mor', '', '0', '0', '0', '0', '0', '0', '', '227', '0', '', '0', '', '', 'Various marketing methods have long been dazzling, but the essence is to study customers (consumers), research the needs and needs of customers, and make products or services targeted. The era of big data has given it a new term: precision marketing. The first areas where big data is applied are mostly for the customer-facing industry. The first application scenarios are mostly precision marketing.“Wine is good, and the alley is deep.” Information about products or services should be delivered t', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545271135', '1545271135');
INSERT INTO `ey_archives` VALUES ('64', '37', '1', '0', 'What is seo?', '/uploads/allimg/20181220/381e3fccc694fff578a861c93628ce15.jpg', '0', '0', '0', '0', '0', '1', '', '126', '0', '', '0', '', '', 'After learning what seo means, you can learn seo.What is seo, from the official explanation, seo=Search Engine Optimization, which is search engine optimization.Use Baidu or other search engines, enter a certain keyword in the search box, such as the wrought iron gate, the top is with the ad typeface, the background is slightly different is the bidding position, which is commonly known as the sem position.Seo is an online marketing method based on search engine marketing. Through seo technology,', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545271278', '1545271278');
INSERT INTO `ey_archives` VALUES ('65', '37', '1', '0', 'How did CMS come into being?', '', '0', '0', '0', '0', '0', '0', '', '106', '0', '', '0', '', '', 'With the enrichment and development of web applications, many websites often cannot quickly follow the footsteps of a large amount of information derivation and business model changes. It often takes a lot of time, manpower and material resources to handle information update and maintenance work. When encountering website expansion, The integration of internal and external networks and branch sites has become more complicated, and even the need to rebuild the website; so, users continue to upgra', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545271999', '1545271999');
INSERT INTO `ey_archives` VALUES ('66', '37', '1', '0', 'The relationship between website design and SEO, the master is analyzed from these four dimensions!', '', '0', '0', '0', '0', '0', '0', '', '113', '0', '', '0', '', '', 'SEO (Search Engine Optimization) and effective website design go hand in hand. Good website design is about creating a website that appeals to your target audience and letting them take some action. However, if the site does not follow current SEO best practices, its ranking will be affected, resulting in fewer visitors actually participating in the site.Conversely, if you focus on search engine optimization and how to please search engine spiders, then the site may rank high and get a lot of se', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545272020', '1545272020');
INSERT INTO `ey_archives` VALUES ('67', '37', '1', '0', 'Shangmeng.com: Single-page SEO station group technology, with 10 websites to optimize the ranking!', '/uploads/allimg/20181220/b20b37ff3af3fad04716faa97b5bb584.png', '0', '0', '0', '0', '0', '1', '', '167', '0', '', '0', '', '', 'Many SEO partners understand that search engine ranking optimization is optimized by internal and external websites. When users search for corresponding keywords, the website can be ranked in front of the search engine. Specifically, Baidu search &quot;network marketing course&quot; to view the operation of Shangmeng. Case!But a single page SEO many partners may be a bit strange, single-page SEO is a SEO optimization program that combines a single-page website with content content, mainly to imp', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545272059', '1545272059');
INSERT INTO `ey_archives` VALUES ('68', '36', '1', '0', 'Differences in user interface design and experience design', '', '0', '0', '0', '0', '0', '0', '', '172', '0', '', '0', '', '', 'Note: The User Interface (UI) design is one of several interdisciplinary topics involved in designing software products. Whether it is user experience (UX, User Experience), interaction design (ID, Interaction Design), or visual / graphic design (Visual / Graphic Design), can involve user interface design.First, what is the user interface design?Broadly speaking, the user interface is the medium through which people interact with machines. The user issues an instruction to the machine, and the m', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545272146', '1545272146');
INSERT INTO `ey_archives` VALUES ('69', '36', '1', '0', 'Novice science! What is the user interface and experience design?', '', '0', '0', '0', '0', '0', '0', '', '203', '0', '', '0', '', '', 'Z Yuhan: User Interface (UI) design is one of several interdisciplinary topics involved in designing software products. Whether it is user experience (UX, User Experience), interaction design (ID, Interaction Design), or visual / graphic design (Visual / Graphic Design), can involve user interface design.First, what is the user interface design?Broadly speaking, the user interface is the medium through which people interact with machines. The user issues an instruction to the machine, and the ma', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545272183', '1545272183');
INSERT INTO `ey_archives` VALUES ('70', '36', '1', '0', 'Read the Internet Queen and her report: Investment in the Internet, Bible, stock selection guide', '/uploads/allimg/20181220/bd62f1623a6a7b4b3a32e6045bff14a3.jpg', '0', '0', '0', '0', '0', '1', '', '166', '0', '', '0', '', '', 'In the early morning of May 31st, Beijing time, Mary Mickel, who is known as the “Queen of the Internet”, published the Internet Trend Report for 2018, which is the 23rd year of her Internet report.The annual Internet Queen&#39;s report will almost always become a must-read report for every Internet entrepreneur. So, who is the Internet Queen? Why is her report so concerned?Internet Queen: The symbol of Wall Street in the 90sIn September 1958, Mary Meeker was born in Indiana, USA.In 1982, Mikel ', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545272227', '1545272227');
INSERT INTO `ey_archives` VALUES ('71', '36', '1', '0', 'Website construction, static pages and dynamic pages how to choose', '/uploads/allimg/20181220/4e7474448185b797c4c1d796b3e581fe.jpg', '0', '0', '0', '0', '0', '1', '', '151', '0', '', '0', '', '', 'Website construction, static pages and dynamic pages how to chooseWhy should e-commerce website construction use static page production? We all know that website production is divided into static page production and dynamic web page production. So which website design technology is better for building e-commerce websites?The ultimate goal of our website is to provide users with a view, so it is most practical to think from the user\'s point of view. Although the dynamic webpage creation techn', '0.00', '0', '', '1', '100', 'en', '1', '0', '0', '1545272283', '1547463680');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='多页标记存储数据表';


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
INSERT INTO `ey_arcrank` VALUES ('3', '0', '开放浏览', 'en', '0', '1552376880');
INSERT INTO `ey_arcrank` VALUES ('4', '-1', '待审核稿件', 'en', '0', '1552376880');

-- -----------------------------
-- Table structure for `ey_arctype`
-- -----------------------------
DROP TABLE IF EXISTS `ey_arctype`;
CREATE TABLE `ey_arctype` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '栏目ID',
  `channeltype` int(10) DEFAULT '0' COMMENT '栏目顶级模型ID',
  `current_channel` int(10) DEFAULT '0' COMMENT '栏目当前模型ID',
  `parent_id` int(10) DEFAULT '0' COMMENT '栏目上级ID',
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
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `dirname` (`dirname`,`lang`) USING BTREE,
  KEY `parent_id` (`channeltype`,`parent_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=54 DEFAULT CHARSET=utf8 COMMENT='文档栏目表';

-- -----------------------------
-- Records of `ey_arctype`
-- -----------------------------
INSERT INTO `ey_arctype` VALUES ('1', '6', '6', '0', '关于我们', 'guanyuwomen', '/html/guanyuwomen', '', '0', '', '', 'lists_single.htm', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526539465', '1545272580');
INSERT INTO `ey_arctype` VALUES ('2', '1', '1', '0', '新闻动态', 'xinwendongtai', '/html/xinwendongtai', '', '0', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526539487', '1545272354');
INSERT INTO `ey_arctype` VALUES ('3', '2', '2', '0', '产品展示', 'chanpinzhanshi', '/html/chanpinzhanshi', '', '0', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526539505', '1545270730');
INSERT INTO `ey_arctype` VALUES ('4', '3', '3', '0', '客户案例', 'kehuanli', '/html/kehuanli', '', '0', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526539517', '1545268599');
INSERT INTO `ey_arctype` VALUES ('5', '4', '4', '0', '资料下载', 'ziliaoxiazai', '/html/ziliaoxiazai', '', '0', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526539530', '1545268321');
INSERT INTO `ey_arctype` VALUES ('6', '8', '8', '0', '报名入口', 'baomingrukou', '/html/baomingrukou', '', '0', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526539546', '1545268049');
INSERT INTO `ey_arctype` VALUES ('8', '6', '6', '1', '公司简介', 'gongsijianjie', '/html/guanyuwomen/gongsijianjie', '', '1', '', '', 'lists_single.htm', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526540452', '1545272565');
INSERT INTO `ey_arctype` VALUES ('9', '6', '1', '1', '公司荣誉', 'gsry', '/html/guanyuwomen', '', '1', '', '', 'lists_article.htm', 'view_article.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526540478', '1545272547');
INSERT INTO `ey_arctype` VALUES ('10', '1', '1', '2', '媒体报道', 'meitibaodao', '/html/xinwendongtai/meitibaodao', '', '1', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526540530', '1545272127');
INSERT INTO `ey_arctype` VALUES ('11', '1', '1', '2', 'SEO优化', 'xingyezixun', '/html/xinwendongtai/xingyezixun', '', '1', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526540543', '1545272114');
INSERT INTO `ey_arctype` VALUES ('12', '1', '1', '2', '企业运营', 'qiyexinwen', '/html/xinwendongtai/qiyexinwen', '', '1', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526540554', '1545271263');
INSERT INTO `ey_arctype` VALUES ('13', '1', '6', '2', '单页面', 'xinwendanye', '/html/xinwendongtai/xinwendanye', '', '1', '', '', 'lists_single.htm', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526540573', '1545270877');
INSERT INTO `ey_arctype` VALUES ('20', '2', '2', '3', '手机', 'shouji', '/html/chanpinzhanshi/shouji', '', '1', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526612114', '1545270715');
INSERT INTO `ey_arctype` VALUES ('21', '2', '2', '3', '电脑', 'diannao', '/html/chanpinzhanshi/diannao', '', '1', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526612188', '1545270214');
INSERT INTO `ey_arctype` VALUES ('22', '2', '2', '3', '通用配件', 'tongyongpeijian', '/html/chanpinzhanshi/tongyongpeijian', '', '1', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526612218', '1545269166');
INSERT INTO `ey_arctype` VALUES ('23', '1', '3', '2', '风景图集', 'fengjingtuji', '/html/xinwendongtai/fengjingtuji', '', '1', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526612255', '1545270830');
INSERT INTO `ey_arctype` VALUES ('24', '2', '2', '20', '智能手机', 'zhinenshouji', '/html/chanpinzhanshi/shouji/zhinenshouji', '', '2', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526612571', '1545270702');
INSERT INTO `ey_arctype` VALUES ('25', '2', '2', '20', '畅玩手机', 'changwanshouji', '/html/chanpinzhanshi/shouji/changwanshouji', '', '2', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526612606', '1545270668');
INSERT INTO `ey_arctype` VALUES ('26', '2', '2', '21', '笔记本电脑', 'bijibendiannao', '/html/chanpinzhanshi/diannao/bijibendiannao', '', '2', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526612635', '1545270191');
INSERT INTO `ey_arctype` VALUES ('27', '2', '2', '22', '耳机', 'erji', '/html/chanpinzhanshi/tongyongpeijian/erji', '', '2', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526612661', '1545269149');
INSERT INTO `ey_arctype` VALUES ('28', '2', '2', '22', '音箱', 'yinxiang', '/html/chanpinzhanshi/tongyongpeijian/yinxiang', '', '2', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526612678', '1545269134');
INSERT INTO `ey_arctype` VALUES ('29', '2', '2', '22', '充电宝', 'chongdianbao', '/html/chanpinzhanshi/tongyongpeijian/chongdianbao', '', '2', '', '', '', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526612691', '1545269115');
INSERT INTO `ey_arctype` VALUES ('30', '6', '8', '1', '预约面试', 'yuyuemianshi', '/html/guanyuwomen/yuyuemianshi', '', '1', '', '', 'lists_guestbook_30.htm', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'cn', '1526634493', '1545272506');
INSERT INTO `ey_arctype` VALUES ('31', '6', '6', '0', 'About Us', 'guanyuwomen', '/html/guanyuwomen', '', '0', '', '', 'lists_single.htm', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526539465', '1545272580');
INSERT INTO `ey_arctype` VALUES ('32', '6', '6', '31', 'Company Profile', 'gongsijianjie', '/html/guanyuwomen/gongsijianjie', '', '1', '', '', 'lists_single.htm', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526540452', '1545272580');
INSERT INTO `ey_arctype` VALUES ('33', '6', '1', '31', 'Honor', 'gsry', '/html/guanyuwomen', '', '1', '', '', 'lists_article.htm', 'view_article.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526540478', '1545272580');
INSERT INTO `ey_arctype` VALUES ('34', '6', '8', '31', 'Appointment interview', 'yuyuemianshi', '/html/guanyuwomen/yuyuemianshi', '', '1', '', '', 'lists_guestbook_30.htm', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526634493', '1545272580');
INSERT INTO `ey_arctype` VALUES ('35', '1', '1', '0', 'News', 'xinwendongtai', '/html/xinwendongtai', '', '0', '', '', 'lists_article.htm', 'view_article.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526539487', '1545272354');
INSERT INTO `ey_arctype` VALUES ('36', '1', '1', '35', 'Media coverage', 'meitibaodao', '/html/xinwendongtai/meitibaodao', '', '1', '', '', 'lists_article.htm', 'view_article.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526540530', '1545272354');
INSERT INTO `ey_arctype` VALUES ('37', '1', '1', '35', 'SEO optimization', 'xingyezixun', '/html/xinwendongtai/xingyezixun', '', '1', '', '', 'lists_article.htm', 'view_article.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526540543', '1545272354');
INSERT INTO `ey_arctype` VALUES ('38', '1', '1', '35', 'Enterprise operation', 'qiyexinwen', '/html/xinwendongtai/qiyexinwen', '', '1', '', '', 'lists_article.htm', 'view_article.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526540554', '1545272354');
INSERT INTO `ey_arctype` VALUES ('39', '1', '6', '35', 'Single page', 'xinwendanye', '/html/xinwendongtai/xinwendanye', '', '1', '', '', 'lists_single.htm', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526540573', '1545272354');
INSERT INTO `ey_arctype` VALUES ('40', '1', '3', '35', 'Landscape Atlas', 'fengjingtuji', '/html/xinwendongtai/fengjingtuji', '', '1', '', '', 'lists_images.htm', 'view_images.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526612255', '1545272354');
INSERT INTO `ey_arctype` VALUES ('41', '2', '2', '0', ' Product', 'chanpinzhanshi', '/html/chanpinzhanshi', '', '0', '', '', 'lists_product.htm', 'view_product.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526539505', '1545270730');
INSERT INTO `ey_arctype` VALUES ('42', '2', '2', '41', 'Phone', 'shouji', '/html/chanpinzhanshi/shouji', '', '1', '', '', 'lists_product.htm', 'view_product.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526612114', '1545270730');
INSERT INTO `ey_arctype` VALUES ('43', '2', '2', '42', 'Smartphone', 'zhinenshouji', '/html/chanpinzhanshi/shouji/zhinenshouji', '', '2', '', '', 'lists_product.htm', 'view_product.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526612571', '1545270730');
INSERT INTO `ey_arctype` VALUES ('44', '2', '2', '42', 'Play your mobile phone', 'changwanshouji', '/html/chanpinzhanshi/shouji/changwanshouji', '', '2', '', '', 'lists_product.htm', 'view_product.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526612606', '1545270730');
INSERT INTO `ey_arctype` VALUES ('45', '2', '2', '41', 'Computer', 'diannao', '/html/chanpinzhanshi/diannao', '', '1', '', '', 'lists_product.htm', 'view_product.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526612188', '1545270730');
INSERT INTO `ey_arctype` VALUES ('46', '2', '2', '45', 'Notebook computer', 'bijibendiannao', '/html/chanpinzhanshi/diannao/bijibendiannao', '', '2', '', '', 'lists_product.htm', 'view_product.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526612635', '1545270730');
INSERT INTO `ey_arctype` VALUES ('47', '2', '2', '41', 'General accessories', 'tongyongpeijian', '/html/chanpinzhanshi/tongyongpeijian', '', '1', '', '', 'lists_product.htm', 'view_product.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526612218', '1545270730');
INSERT INTO `ey_arctype` VALUES ('48', '2', '2', '47', 'Headset', 'erji', '/html/chanpinzhanshi/tongyongpeijian/erji', '', '2', '', '', 'lists_product.htm', 'view_product.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526612661', '1545270730');
INSERT INTO `ey_arctype` VALUES ('49', '2', '2', '47', 'Loudspeaker box', 'yinxiang', '/html/chanpinzhanshi/tongyongpeijian/yinxiang', '', '2', '', '', 'lists_product.htm', 'view_product.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526612678', '1545270730');
INSERT INTO `ey_arctype` VALUES ('50', '2', '2', '47', 'Portable battery', 'chongdianbao', '/html/chanpinzhanshi/tongyongpeijian/chongdianbao', '', '2', '', '', 'lists_product.htm', 'view_product.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526612691', '1545270730');
INSERT INTO `ey_arctype` VALUES ('51', '3', '3', '0', 'Case', 'kehuanli', '/html/kehuanli', '', '0', '', '', 'lists_images.htm', 'view_images.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526539517', '1545268599');
INSERT INTO `ey_arctype` VALUES ('52', '4', '4', '0', 'Data download', 'ziliaoxiazai', '/html/ziliaoxiazai', '', '0', '', '', 'lists_download.htm', 'view_download.htm', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526539530', '1545268321');
INSERT INTO `ey_arctype` VALUES ('53', '8', '8', '0', 'Entry entry', 'baomingrukou', '/html/baomingrukou', '', '0', '', '', 'lists_guestbook.htm', '', '', '', '', '100', '0', '0', '0', '0', '0', '1', 'en', '1526539546', '1545268049');

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
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COMMENT='文章附加表';

-- -----------------------------
-- Records of `ey_article_content`
-- -----------------------------
INSERT INTO `ey_article_content` VALUES ('1', '4', '&lt;p style=&quot;margin-top: 0px; margin-bottom: 15px; color: rgb(85, 85, 85); font-family: &amp;quot;Microsoft Yahei&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, Helvetica, Arial, sans-serif; font-size: 15px; text-indent: 30px; white-space: normal;&quot;&gt;在了解&lt;strong&gt;seo是什么意思&lt;/strong&gt;之后，才能学习seo。&lt;br/&gt;&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 15px; color: rgb(85, 85, 85); font-family: &amp;quot;Microsoft Yahei&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, Helvetica, Arial, sans-serif; font-size: 15px; text-indent: 30px; white-space: normal;&quot;&gt;什么是seo，从官方解释来看，seo=Search（搜索） Engine（引擎） Optimization（优化），即搜索引擎优化。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 15px; color: rgb(85, 85, 85); font-family: &amp;quot;Microsoft Yahei&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, Helvetica, Arial, sans-serif; font-size: 15px; text-indent: 30px; white-space: normal;&quot;&gt;使用过百度或其他搜索引擎，在搜索框中输入某一个关键词，如铁艺大门，排名靠前带有广告字样，背景略不同的是竞价位置，为俗称的&lt;a href=&quot;http://www.xminseo.com/2376.html&quot; title=&quot;&quot; style=&quot;color: rgb(0, 166, 124); text-decoration: none;&quot;&gt;sem&lt;/a&gt;位置。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 15px; color: rgb(85, 85, 85); font-family: &amp;quot;Microsoft Yahei&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, Helvetica, Arial, sans-serif; font-size: 15px; text-indent: 30px; white-space: normal;&quot;&gt;seo是基于搜索引擎营销的一种网络营销方式，通过seo技术，提升网站关键词排名，获得展现，继而获得曝光，继而获得用户点击，继而获得转化。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 15px; color: rgb(85, 85, 85); font-family: &amp;quot;Microsoft Yahei&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, Helvetica, Arial, sans-serif; font-size: 15px; text-indent: 30px; white-space: normal;&quot;&gt;一：seo分类。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 15px; color: rgb(85, 85, 85); font-family: &amp;quot;Microsoft Yahei&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, Helvetica, Arial, sans-serif; font-size: 15px; text-indent: 30px; white-space: normal;&quot;&gt;细化来看，所有有利于网站关键词排名提升的点，都可以归纳于seo，为便于理解，我们将seo分为站内seo和站外seo。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 15px; color: rgb(85, 85, 85); font-family: &amp;quot;Microsoft Yahei&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, Helvetica, Arial, sans-serif; font-size: 15px; text-indent: 30px; white-space: normal;&quot;&gt;1：站内seo。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 15px; color: rgb(85, 85, 85); font-family: &amp;quot;Microsoft Yahei&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, Helvetica, Arial, sans-serif; font-size: 15px; text-indent: 30px; white-space: normal;&quot;&gt;什么是站内seo？通俗来讲，就是指网站内部优化，即网站本身内部的优化，包括代码标签优化、内容优化、安全建设、用户体验等。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 15px; color: rgb(85, 85, 85); font-family: &amp;quot;Microsoft Yahei&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, Helvetica, Arial, sans-serif; font-size: 15px; text-indent: 30px; white-space: normal;&quot;&gt;2：站外seo。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 15px; color: rgb(85, 85, 85); font-family: &amp;quot;Microsoft Yahei&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, Helvetica, Arial, sans-serif; font-size: 15px; text-indent: 30px; white-space: normal;&quot;&gt;什么是站外seo？通俗来讲，就是网站的外部优化，包括外链建设，品牌建设，速度优化，引流等。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 15px; color: rgb(85, 85, 85); font-family: &amp;quot;Microsoft Yahei&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, Helvetica, Arial, sans-serif; font-size: 15px; text-indent: 30px; white-space: normal;&quot;&gt;二：seo相关建议。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 15px; color: rgb(85, 85, 85); font-family: &amp;quot;Microsoft Yahei&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, Helvetica, Arial, sans-serif; font-size: 15px; text-indent: 30px; white-space: normal;&quot;&gt;1：建议把seo定位于一种网络营销方式，在学习，使用seo的过程中，将他作为一种获取流量的渠道。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 15px; color: rgb(85, 85, 85); font-family: &amp;quot;Microsoft Yahei&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, Helvetica, Arial, sans-serif; font-size: 15px; text-indent: 30px; white-space: normal;&quot;&gt;2：新手学习seo的理想平台是百度搜索资源平台而非其他；理论联系实际操作是更为有效的学习方式；有经验的seo高手教会更快的掌握好seo；多思考，多总结，才能领悟seo的精髓。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 15px; color: rgb(85, 85, 85); font-family: &amp;quot;Microsoft Yahei&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, Helvetica, Arial, sans-serif; font-size: 15px; text-indent: 30px; white-space: normal;&quot;&gt;3：学习seo之前，熟悉掌握相关seo术语很有必要。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 15px; color: rgb(85, 85, 85); font-family: &amp;quot;Microsoft Yahei&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, Helvetica, Arial, sans-serif; font-size: 15px; text-indent: 30px; white-space: normal;&quot;&gt;4：很多时候，seo的理论与现实是相违背的，也就是说seo的理论点不复杂，操作点却很难达到。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 15px; color: rgb(85, 85, 85); font-family: &amp;quot;Microsoft Yahei&amp;quot;, &amp;quot;Helvetica Neue&amp;quot;, Helvetica, Arial, sans-serif; font-size: 15px; text-indent: 30px; white-space: normal;&quot;&gt;新手接触seo，感觉无所适从，请熟读seo术语，后面会越来越轻松。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1547462542', '1547462542');
INSERT INTO `ey_article_content` VALUES ('5', '9', '&lt;p style=&quot;margin-top: 0px; margin-bottom: 24px; color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;&lt;span style=&quot;font-weight: 700;&quot;&gt;注：&lt;/span&gt;用户界面（UI，User Interface）设计是设计软件产品所涉及到的几个交叉学科之一。不论是用户体验（UX，User Experience）、交互设计（ID，Interaction Design），还是视觉/图形设计（Visual / Graphic Design），都能牵扯到用户界面设计。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 24px; color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;&lt;span style=&quot;color: rgb(54, 54, 54); font-family: Tahoma, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Heiti, 黑体, sans-serif; font-size: 24px;&quot;&gt;一、什么是用户界面设计？&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 24px; color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;广泛来讲，用户界面是人与机器交流的媒介。用户向机器发出指令，机器随即开始一段进程，回复信息，并给出反馈。用户可以根据用户反馈进行下一步操作的决策。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 24px; color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;人机交互（HCI，Human Computer Interaciton）所关注的主要是数字界面，即过去的打孔机、命令行，直至今天的图形界面（GUI，Graphic Design）。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 24px; color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;用户界面设计对于数码产品来说主要关注的是布局、信息结构，以及界面元素在显示屏和各种终端平台上的展示。电子游戏和电视界面也包括其中。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1526552582', '1531711820');
INSERT INTO `ey_article_content` VALUES ('15', '39', '&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;对于记忆来说，味道往往是最美的，儿时喝过的饮料，至今回想起来依然觉得津津有味。&lt;br/&gt;&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;今天是六一儿童节，青山资本梳理了中国40年来饮料发展的简史，权当节日的小消遣，顺便看看能否找到你记忆深处的那个味道？&lt;/p&gt;&lt;h2 style=&quot;margin: 0px; padding: 0px; font-size: 16px; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;第一阶段：国人味蕾的开启时代&lt;/h2&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;&lt;br/&gt;&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;百事可乐在华第一家工厂开业&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;1981年，可口可乐在中国第一条生产线正式投产，主要供应旅游饭店，卖给外国人收取外汇，百事可乐也在深圳建立了第一家罐装厂。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;1982年，国家把饮料纳入“国家计划管理产品”，可口可乐开始在北京市场进行内销。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1527824652', '1531709817');
INSERT INTO `ey_article_content` VALUES ('16', '40', '&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;约翰·奎尔奇说， 社交媒体有很多营销挑战，如何为粉丝来估值是一个大问题。从营销角度来思考，要关注强纽带和弱纽带。你可能以为，强纽带的密友产生最大的营销影响，研究发现不是这样的，产生更大的影响反而是跟你更疏远的人。&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; background-color: rgb(255, 255, 255);&quot;&gt;演讲者｜ 约翰·奎尔奇&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;（ 哈佛商学院教授， 曾任伦敦商学院院长、中欧国际工商学院副院长）&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;非常感谢大家在周日早上回来听我讲课。对于你们这些创业者，或者希望成为创业者的人，我今天准备了一个特别的讲座。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;很多创业者没有把最终愿景很好界定，所以每天都忙于灭火，忙于生存。&lt;/p&gt;&lt;p&gt;&lt;strong style=&quot;font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;创业营销，你必须做好规划&lt;/strong&gt;&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;今天将从创业营销这个话题开始，包括你如何生存和成功。创业营销包括四个关键领域，你必须很好地去规划：&lt;/p&gt;&lt;ul style=&quot;list-style-type: inherit;&quot; class=&quot; list-paddingleft-2&quot;&gt;&lt;li&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 0px; padding: 0px;&quot;&gt;要有正确的目标客户和最终用户；&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 0px; padding: 0px;&quot;&gt;要有正确的产品和服务&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 0px; padding: 0px;&quot;&gt;要有一个非常好的人才团队，使得商业创意能够实现；&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 0px; padding: 0px;&quot;&gt;要有好的合作伙伴，不是分销商，而是会计、律师等服务伙伴。&lt;/p&gt;&lt;/li&gt;&lt;/ul&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;&lt;strong&gt;那么，何为创业营销？ ？&lt;/strong&gt;&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;第一，这是从愿景到行动的逆向工程设计&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;当星巴克只有 5 家店时，创始人就有一个愿景，让星巴克成为你生活中的第三空间。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;对创业者要从愿景开始，向后进行逆向工程的设计：看一下需要有什么样的行动，才能实现愿景。 很多创业者没有把最终愿景很好界定，所以每天都忙于灭火，忙于生存。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;第二，快速的周期，低成本进行试验，以提供证据&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;有了愿景要去思考，怎样做一些快速的低成本实验测试创意，向合作伙伴、客户等证明，这是一个非常好的愿景。换句话说， 你需要短期的成就作为证据。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;第三，与高瞻远瞩的客户共同开发&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;大多数的客户是保守的，不想浪费时间在新公司上。 你必须要找到有远见的客户，他们愿意在你身上冒风险。 他们可能是小的新兴客户，不是你想要进入的那个市场的好根基客户。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;第四：创建小步快跑的综合路线图&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;包括创建产品路线图、客户图、合作伙伴路线图、人才路线图。 创业者应该有一个长达一年甚至三年的路线图，看下你希望这个公司在这四个维度上应该怎么样取得进步。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;&lt;strong&gt;举个例子&lt;/strong&gt;&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;上世纪 90 年代末， John Osher 发明了 SpinBrush ，这是一个低成本的电动牙刷。因为 他洞察到市场上存在着一个很大的空白：普通手动牙刷每支两美元，电动牙刷要 50 美元。 但是这两者之间，没有任何中间产品。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;他想开发一个牙刷，价格介于两者之间。他思考了下新牙刷成功的性能标准：&lt;/p&gt;&lt;ul style=&quot;list-style-type: inherit;&quot; class=&quot; list-paddingleft-2&quot;&gt;&lt;li&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 0px; padding: 0px;&quot;&gt;清洁上要优于手动牙刷，不然消费者不会付出更高的价格；&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 0px; padding: 0px;&quot;&gt;自带电池能用三个月，如果每周都要换电池太崩溃；&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 0px; padding: 0px;&quot;&gt;包装中有试用的特点，大家愿意看看牙刷启动后是怎么旋转的；&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 0px; padding: 0px;&quot;&gt;零售价不到 6 美元。&lt;/p&gt;&lt;/li&gt;&lt;/ul&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;他对新牙刷的定位是：是更好的手动牙刷，而不是一个更便宜的电动牙刷。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;对于消费者，是从 2 美元增加到 6 美元，而不是从 50 美元降到 6 美元。因为如果是后者，零售商会觉得赔了：消费者只花了6美元，而以前是50美元。但是现在，消费者从花2块提高到了花6块。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;所以创业者不仅要考虑最终用户，还要思考如何让分销商多赚钱，因为你必须通过他们，产品才能到最终客户那里。 界定竞争的时候，好的定位声明非常重要。 最后，他把公司卖给了 宝洁，一共赚了4.8亿美元。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;大家看，其实非常简单，就是因为他有大量的消费者洞察，填补了没有任何人看见的市场空白。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;&lt;strong&gt;再举个例子&lt;/strong&gt;&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;这家公司叫 Intuit ，创始人在20年前就发现，好多人在应对自己税务处理的时候，每年要填一个纳税申报单再交给政府，很麻烦。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;Intuit 是第一个开发个人理财软件的公司，尤其是做纳税管理方面的软件，不管是个人还是小企业都可以用。 但是这个好用的软件包，不知道卖向哪里，没人相信它能用。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;有时候你最大的问题就是，你的新产品如何把分销商搞定。他们分销很多东西，根本没时间花五小时检查你这个不知名的产品能不能用。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;最后他直接向消费者保证： 如果买了这个产品，六分钟内没学会怎么用，钱退给你，产品也送给你。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;除了退钱，他们还做了什么与众不同的事情呢？&lt;/p&gt;&lt;ul style=&quot;list-style-type: inherit;&quot; class=&quot; list-paddingleft-2&quot;&gt;&lt;li&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 0px; padding: 0px;&quot;&gt;在买家允许下，跟着买家观察他的首次使用过程。&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 0px; padding: 0px;&quot;&gt;公司所有高管每个月必须花两小时做客户的技术支持，听客户遇到的问题；&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 0px; padding: 0px;&quot;&gt;做客户服务的技术支持，是公司里晋升的必经路径；&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 0px; padding: 0px;&quot;&gt;把客户的信当着所有高管的面大声朗读，不管是感谢还是指责。&lt;/p&gt;&lt;/li&gt;&lt;/ul&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;这使得他们 50% 的销售是来自于口碑， 20% 的销售是来自于技术支持的推荐。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;&lt;em&gt;“ 客户真正想要的和技术真正能做好的交叉点 —— 在此处才能找到真正的伟大。 ”&lt;/em&gt;&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;&lt;em&gt;“ 我们不管做什么，都是有客户存在的。 ”&lt;/em&gt;&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;&lt;em&gt;——ScottCook（ Intuit创始人）&lt;/em&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1547462596', '1547462596');
INSERT INTO `ey_article_content` VALUES ('17', '41', '&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;各种行销手段早已令人眼花缭乱，但究其本质都是在研究客户（消费者），研究客户的所想、所需，使产品或服务有的放矢。大数据时代又给它赋予了新名词：精准营销。大数据最先应用的领域多为面对客户的行业，最先应用的情景也多为精准营销。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;“酒好也怕巷子深”，产品或服务的信息要送达客户才可能促成交易。一般认为，向客户传达产品或服务信息要靠广告。广告古已有之，“三碗不过岗”的酒幌子就是广告。没有互联网的时代，我们熟悉的是电视广告、广播广告、印刷品平面广告、户外广告牌等，当然，也包括吆喝叫卖。但过去的广告是千人一面、不区分受众的。后来商家对客户的信息有所采集就有了CRM，经过客户分类，可以更好地服务于不同的客户群体。互联网+大数据时代让CRM有了新的发展机遇，管理客户不再是简单的数字统计和没有个性的（或简单聚类的）直邮、定投。随着商家对客户知道更多、了解更深，便有机会为客户提供个性化的营销方案，进一步改善客户体验，成为了个性化营销或叫精准营销。大数据时代，让很多过去的不可能变为可能，营销活动也赢来了新的发展机遇。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;时代不同，商业经营的形式会变化，但本质就是两件事：开源，节流。开源是开拓新客户，发现新商机；节流是减少内部运营成本，提高资源利用效率。要实现这一切都需要以数据为依据的决策。过去，人们也在长期的经营活动中，采集和运用了与经营活动相关的很多强相关数据，也形成了选择客户的标准。鉴于当时的技术瓶颈，做大样本的数据采集及数据分析成本都过高，无法在更大范围推广运用。大数据时代，人们有了廉价采集数据和存储数据的可能，廉价的计算资源让数据分析成为了可能。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;大数据精准营销的背后，是用多维度的数据来观察客户，描述客户，就是说为客户画像。说“依托大数据，可以让营销人员比过去更了解客户，比客户自己更了解客户的需求”并不为过。营销人员无不想知道客户是谁、在哪里、消费习惯是什么、需要什么、什么时候需要、用什么方式向他们传递信息更为有效等等，通过数据采集和数据分析分析可以找到答案。精准营销不仅可以帮助商家开源---发现潜在客户，还可以帮助商家节流---发现潜在风险。当我们对客户了解更多，就会知道哪位客户可能在经营中存在风险。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;若问每个经营者是否会运用从业经验来进行营销，多数答案是肯定的。但若问经营者是否会利用数据进行营销，恐怕答案就是五花八门。一般认为，应用数据进行营销是大公司的事情，与小公司无缘。其实，大到跨国公司，小到街边小贩，运用数据进行营销，都会收到意想不到的结果。不相信吗？街边小贩留意一下天气预报（刮风，下雨，还是暴晒）就知道明天有哪些生意的机会，进而知道该如何备货。建议中小公司的人不要拒绝精准营销的理念，不妨学学精准营销的思想方法。即便是经营者有丰富的经验，把经验数据化对经营也会很有帮助。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;《颠覆营销》一书就是在教读者如何运用大数据来做营销。书中案例丰富、语言可读性强。值得关心大数据营销的各界朋友读一读。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;我认同书中的不少观点：“大数据重新定义产业竞争规则，比的不是数据规模大小，不是统计技术，也不是强大的计算能力，而是核心数据的解读能力”。在很多人纠结于大数据定义的今天，我们确实更应该关注数据的核心价值理解与应用。书中提出的“问对问题”也很重要。经营者平时的问题一定不少，但追问究竟时，就可能出现偏差，导致“失之毫厘谬以千里”。问对问题能力的提高涉及思想方法，需要在锻炼中提高。验证问题是否问对了，恰恰就是数据分析师可以做贡献的地方。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;本书还引起了二个值得更深入思考的问题：&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;仅仅发现不同客户群体的消费习惯，适时提醒客户去消费，还远远不够。比如：某消费者一个月的正常理性消费在两千元的水平，一般在A，B两家商店消费。A商店运用了精准营销的理念会让消费者把这两千元都花在A商店，随着B商店的后来居上，消费者又可能重新回到B商店消费这两千元。在供给过剩需求不足的今天，既有的消费额在不同商家中进行分配或迁移都不能带来社会消费总量的增加。大数据营销的更高水平应用是提前知晓客户尚未被满足、甚至尚未被发现的需求。大数据的价值挖掘有机会把商家（含厂家）和客户连在一起，让商家提供更多的满足客户个性化需求的产品或服务，让客户的消费意愿提高。这是数据价值挖掘工作者面临的新挑战。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;数据真的越多越好吗？不少大数据公司热衷于用爬虫软件在网上“爬”各种数据。然而同一数据集在不同的应用场景价值密度是不一样的，针对特定应用场景也并非是数据维度越多就越好，一定要围绕应用目标来采集数据和使用数据。提升维度来采集更多数据一定是有助于更详尽地描述事物，但无疑也增加了处理数据的复杂性。每一次技术的进步，都给人类带来新的想象空间，难免欲望膨胀自信满满，对世界的认知也随之升维，甚至是无节制地升维。之后发现升维带来资源的占用，智慧跟不上，无节制地升维反而是解决方案复杂化，冷静下来会重新启动降维思考。也许人类的认知与智慧就是在升维、降维、再升维、再降维中交替前行的。本书的降维思考，必要时回归本元的思考给人们启示。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 32px; padding: 0px; text-align: justify; font-family: &amp;quot;Open Sans&amp;quot;, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, STHeiti, &amp;quot;WenQuanYi Micro Hei&amp;quot;, SimSun, sans-serif, sans-serif; font-size: 18px; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;大数据时代工具手段固然重要，思想方法更为重要。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1527825125', '1527825125');
INSERT INTO `ey_article_content` VALUES ('6', '10', '&lt;p style=&quot;margin-top: 0px; margin-bottom: 24px; color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;Z Yuhan：用户界面（UI，User Interface）设计是设计软件产品所涉及到的几个交叉学科之一。不论是用户体验（UX，User Experience）、交互设计（ID，Interaction Design），还是视觉/图形设计（Visual / Graphic Design），都能牵扯到用户界面设计。&lt;/p&gt;&lt;h4 style=&quot;margin: 28px 0px 14px; color: rgb(54, 54, 54); padding-left: 15px; border-left: 5px solid rgb(255, 200, 31); background-color: rgb(255, 255, 255); font-size: 24px; font-family: Tahoma, Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Heiti, 黑体, sans-serif; line-height: 32px; white-space: normal;&quot;&gt;一、什么是用户界面设计？&lt;/h4&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 24px; color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;广泛来讲，用户界面是人与机器交流的媒介。用户向机器发出指令，机器随即开始一段进程，回复信息，并给出反馈。用户可以根据用户反馈进行下一步操作的决策。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 24px; color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;人机交互（HCI，Human Computer Interaciton）所关注的主要是数字界面，即过去的打孔机、命令行，直至今天的图形界面（GUI，Graphic Design）。&lt;/p&gt;&lt;p style=&quot;margin-top: 0px; margin-bottom: 24px; color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;用户界面设计对于数码产品来说主要关注的是布局、信息结构，以及界面元素在显示屏和各种终端平台上的展示。电子游戏和电视界面也包括其中。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1526552685', '1531711845');
INSERT INTO `ey_article_content` VALUES ('7', '12', '&lt;p&gt;北京时间 5 月 31 日凌晨，有“互联网女皇”之称的玛丽·米克尔发布了 2018 年的互联网趋势报告，这也是她第 23 年公布互联网报告。&lt;br/&gt;&lt;/p&gt;&lt;p&gt;每年的互联网女皇报告几乎都会成为每个互联网创业者的必读报告。那么，互联网女皇是谁?为什么她的报告会如此受关注呢?&lt;/p&gt;&lt;p&gt;&lt;strong&gt;互联网女皇： 90 年代华尔街的象征&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;1958 年 9 月，玛丽·米克尔(Mary Meeker)出生于美国印第安纳州。&lt;/p&gt;&lt;p&gt;1982 年，米克尔加入了当时最负盛名的券商美林公司，担任股票经纪人。&lt;/p&gt;&lt;p&gt;米克尔的明星分析师之路开始于 1991 年，这年她加入了知名投行摩根士丹利，开始了自己辉煌的科技分析师生涯。&lt;/p&gt;&lt;p&gt;自 1995 年以来，米克尔的工作随着网络潮流变化而变化，她逐重于研究雅虎、美国在线及亚马孙等知名公司将如何调整结构并相互竞争。&lt;/p&gt;&lt;p&gt;1996 年，玛丽·米克尔如愿地成为摩根·斯坦利技术股票分析部的负责人，还创造出了华尔街闪耀的新职业——互联网分析师。就像垃圾债券代表了 80 年代华尔街一样，玛丽·米克尔成了 90 年代华尔街的象征。&lt;/p&gt;&lt;p&gt;2010 年底，米克尔辞去摩根士丹利董事总经理的职位，离开华尔街，去到加州成为知名风投KPCB的合伙人。KPCB公司(Kleiner\r\n Perkins Caufield &amp;amp; Byers)成立于 1972 年，是美国最大的风险基金，其最得意的杰作是网景公司的创立。&lt;/p&gt;&lt;p&gt;&lt;strong&gt;互联网女皇报告：互联网领域的投资圣经、选股指南&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;1994 年，米克尔在《纽约时报》上偶然看到一篇讲述创业公司Mosaic研发网络浏览器的报道。米克尔立即意识到，这种网络浏览器可能会改变人们获取信息的方式。她随后就联系了Mosaic的两位创始人，并向华尔街投资者大力介绍这家公司。&lt;/p&gt;&lt;p&gt;Mosaic后来改名为网景，并在 1995 年在纽约上市。得益于米克尔与网景两位创始人的良好关系，摩根士丹利成为网景首次公开募股(IPO)的主承销商。&lt;/p&gt;&lt;p&gt;当年 8 月 9 日，网景上市首日收盘，股价从 14 美元的发行价暴增至 75 美元，创下了当时的上市公司首日涨幅记录。当年网景IPO也成为互联网时代到来的一大标志。&lt;/p&gt;&lt;p&gt;1995 年，除了负责网景的上市交易外，米克尔还与同事克里斯o德普开始发布《互联网报告》，并最早提出了“页面浏览量”等网络类股分析指标。这份报告被投资者视为互联网领域的投资圣经，并且成书公开发行，在整个科技行业引发了巨大反响。&lt;/p&gt;&lt;p&gt;1996- 1997 年，米克尔和摩根士丹利发布了《互联网广告报告》与《互联网零售业报告》，一举奠定了米克尔互联网领域第一分析师的地位。互联网女皇报告几乎成为当时每个互联网创业者的必读报告。&lt;/p&gt;&lt;p&gt;互联网女皇报告，无异于选股指南。她向投资者推荐的美国在线、戴尔、亚马逊、eBay等公司股票，都很快带来了超过十倍的投资回报。&lt;/p&gt;&lt;p&gt;&lt;strong&gt;互联网女皇报告中的“神预测”&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;业界如此看重互联网女皇报告的最主要原因，在于米克尔的那些神预测。以下，我们简单罗列了几点互联网女皇报告中的神预测例子。&lt;/p&gt;', '1547462473', '1547462473');
INSERT INTO `ey_article_content` VALUES ('8', '13', '&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;网站建设的五大核心要素&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;&lt;img src=&quot;/uploads/ueditor/20190114/75c3c73acccc98cc5553d39eabf5fb38.jpg&quot; title=&quot;75c3c73acccc98cc5553d39eabf5fb38.jpg&quot; alt=&quot;75c3c73acccc98cc5553d39eabf5fb38.jpg&quot;/&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　企业要实行网络营销，首先需要进行网站制作。网站是由众多的Web页面组成的，而这些页面设计的好坏，直接影响到这个网站能否得到用户的欢迎。判断一个主页设计的好坏，要从多方面综合考虑，不能仅仅看它设计得是否生动漂亮，而应该看这个网站能否最大限度地替用户考虑。&lt;/p&gt;&lt;p&gt;&lt;img alt=&quot;&quot; class=&quot;limg&quot; src=&quot;http://www.eyoucms.com/uploads/allimg/180426/1510032P3-1.jpg&quot;/&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　3、以产品为核心原则&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　网站制作最重要的目的及功能就是为产品展示。顾客访问网站的主要目的是为了对产品和服务进行深入的了解，网站的价值也就在于灵活地向用户展示产品说明及图片甚至多媒体信息，即使一个功能简单的网站至少也相当于一本可以随时更新的产品宣传资料。过时的产品信息或者产品信息不完善不仅无法促进销售，同时也影响顾客的信心。顾客在访问网站时，关心的不是个人的信息，而是能够提供什么样的产品、产品的优势是什么。所以，以产品为核心是网站成功的一首要前提。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　产品信息一般应该包括以下几方面内容：产品名称产品规格、产品用途、产品特性、产品认证情况及产品图片等。其次，产品规格、产品用途和产品特性等信息应尽可能详细地描述。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　4、以网站的信息交互能力强为原则&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　如果一个网站只能提供浏览者浏览，而不能引导浏览者参与到网站内容的一部分建设中，那么它的吸引力是有限的。只有当浏览者能够很方便地和信息发布者交流信息时，该网站的魅力才能充分体现出来。虚拟论坛的设计在产品使用者之间、产品使用者与产品开发经理之间展开对产品的各种讨论。在线营销人员还可以借此收集市场信息，制定有效的营销计划。而网站消费者的反馈信息直接在网上公布，能够吸引消费者回访该网站，并由此可形成与顾客的固定关系。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　当顾客在网上找到感兴趣的产品时，如何针对该产品及时进行询价和反馈?这不仅仅是通过电子函件方式就能够实现的。网站上应该提供相应的信息反馈模块，使顾客能够针对某个或多个产品方便快捷地进行询价或反馈。同时，企业的业务员应该能够及时查到顾客的反馈信息并及时回复：每个业务部门或业务员应该能够针对其发布的产品，方便地管理顾客的信息和反馈信息。通过网站可以为顾客提供各种在线服务和帮助信息，比如常见问题解答(FAQ)、详尽的联系信息、在线填写寻求帮助的表单、通过聊天实时回答顾客的咨询等。同时，利用网站还可以实现增进顾客关系的目的，比如通过发行各种免费邮件列表、提供有奖竞猜等方式吸引用户的参与。通过网站上的在线调查表，可以获得用户的反馈信息，用于产品调查、消费者行为调查、品牌形象调查等，是获得第一手市场资料有效的调查工具。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　5、以完善的检索能力为原则&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　对于一个网站来说，如何合理地组织自己要发布的信息内容，以便让浏览者能够快速、准确地找到要找的信息，这是一个网站内容组织是否成功的关键。如果网站的结构设计不能使顾客方便、快捷地找到所需的信息，再好的设计也不能吸引长久的客户。即使将他吸引到了网站主页，将来也会中断访问。为了达到上述设计目标，一些网站在网页上设计了信息索引和目录索引。使用者能很快地找到感兴趣的那部分信息。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　因此，为了网站内容的实用，有一定规模的网站一定要提供检索功能，以便于用户查找本网站的信息。为了给浏览者创造方便条件，网页设计者经常将网页内容设计成树形结构，方便纵向查询。访问者从主页开始就可以层层深入到所有“树权”和“树梢”的信息内容。另外，还可以设计一个搜索系统，让访问者很容易地就找到相关的内容。网址的搜索系统，设计应相当周全，允许访问者从任一页面进入。同时，在网站的任何一个页面都要设计有“返回主页”的链接，以方便访问者回到“树干”。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1547462639', '1547462639');
INSERT INTO `ey_article_content` VALUES ('9', '14', '&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;网站建设，静态页面和动态页面如何选择&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　电商网站建设为什么要使用静态页面制作。我们都知道，网站制作有分为静态页面制作和动态网页制作，那么建设电商网站采用哪种网站设计技术更好呢?&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　我们建设网站最终目的是为了给用户浏览，所以从用户的角度出发进行思考才是最实际的，使用动态网页制作技术虽然网页美观度大大提升了，但是却不利于网站优化，今天小编重点和大家谈谈，网站建设为什么要使用静态页面制作。&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;/uploads/ueditor/20190114/47caf8cc457ff50c8a66f4c4a23cfeb1.png&quot; title=&quot;47caf8cc457ff50c8a66f4c4a23cfeb1.png&quot; alt=&quot;47caf8cc457ff50c8a66f4c4a23cfeb1.png&quot;/&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　做静态网站建设所采用的技术原理是一对一的形式，也就是说这样的网站上面，一个内容对应的就是一个页面，无论网站访问者如何操作都只是让服务器把固有的数据传送给请求者，没有脚本计算和后台数据库读取过程，大大降低了部分安全隐患。静态网站设计除了拥有上述的速度快，安全性高这两个特点之外还具有跨平台，跨服务器功能。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　现在熟悉搜索引擎原理工作原理的朋友应该都知道，它所提供给广大用户的信息是本身就存在于数据库当中的信息而不是实时的信息，固定的信息内容更容易接受和保存。我们可能常常会遇到这样的问题，当我们搜索自己所需要的信息时得出来的结果可能已经失效，这就是静态页面网站设计的不足之处，但又因为它的稳定，所以久久不会被删除。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　与静态页面网站设计不同，生成的动态页面信息不但不易被搜索引擎所检索，而且打开速度慢，再者也不稳定，这就是为什么这么多专业网站建设公司都一再建议客户使用静态形式的网站设计的原因，有些网站建设公司会考虑把页面进行伪静态处理，但不知道大家有没有注意过，伪静态处理的URL通常是不规则的。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1547462449', '1547462449');
INSERT INTO `ey_article_content` VALUES ('10', '19', '&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;从三方面完美的体验企业网站的核心价值&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　随着互联网的迅猛发展，一个企业的发展离不开互联网的发展，企业注重企业网站建设，那么必然会给其带来不错的效果。企业网站建设其核心价值直接体现在网站对于用户和商家而言，是否能够满足他们利益需求，能否提高企业发展，提高企业的发展渠道。&lt;/p&gt;&lt;p&gt;&lt;img class=&quot;rimg&quot; src=&quot;http://www.eyoucms.com/uploads/allimg/180426/150RQ155-0.jpg&quot;/&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　一个好的导航系统就是一个好的导游，认为每一个网站设计方案都有权利与义务帮助客户及时准确的找到自己感兴趣的内容主体和需要的东西。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　另一方面体现在网站对商家现金利益需求的满足，而此却建立在网站对用户需求满足的基础之上。因为，如果网站不能够满足用户利益的需求，用户就不会为网站创造价值，不能吸引更多的用户参与到网站中来，不能实现网站价值循环式的增长，用户规模将会无法得到较大发展，很难实现对商家现金利益需求的满足，商家在网站投放广告是基于网站促进发生交易可能性的大小，交易可能性越大，商家才可以获得更大的现金利益，否则，将会白白浪费广告费。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　其次体现在对用户利益需求的满足，网站在发展初期更多的是要为用户提供他们需求的内容，积极的创造内容价值，满足用户各种基础性利益的需求，尤其是各类疑问的解答，相关兴趣或者专业资料的提供，各种资讯信息的发布。让用户能够基于某一种原因留下来，在基础性的工作做好的前提下，您可以着力于用户交易利益需求的满足，或者开始就将交易与用户的相关需求结合起来，打造一个个活跃度高的交易类版块，为用户提供此类交易最全面、最方便的资料和场所，积极促进用户活跃度的提高和迅速实现网站盈利。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　我们认为让客户在首页即可看到与自己寻找的讯息高度相关的行业信息是非常明智的抉择，一个没有大量行业专业信息体现的网站设计称不上合格的网站设计，也无法真正的为客户从根本上解决问题。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 15px; white-space: normal; background-color: rgb(255, 255, 255); padding: 0px; line-height: 30px; color: rgb(51, 51, 51); font-family: 宋体; font-size: 14px;&quot;&gt;　　我们只有尽量的在网站设计当中体现出如何才能在众多的行业竞争对手中脱颖而出，让客户可以信任我们呢?网站建设公司认为唯有尽量表现出自己的专业实力方可,当然除了这三点之外，网站设计仍旧有很多需要注意的地方，但不管怎么样，核心价值还是应该要重点体现，将重点放在核心内容上才是网站设计的真谛， 我们知道网站运营的核心理念是价值，站长们务必牢牢树立，一切从用户出发，积极满足用户需求，让用户发挥创造力，为网站创造价值，实现网站价值循环式增长，让站长运营变成用户运营是我们的终极目标，一劳永逸，盈利不断。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1526610848', '1526610848');
INSERT INTO `ey_article_content` VALUES ('11', '20', '&lt;p&gt;随着网络应用的丰富和发展，很多网站往往不能迅速跟进大量信息衍生及业务模式变革的脚步，常常需要花费许多时间、人力和物力来处理信息更新和维护工作；遇到网站扩充的时候，整合内外网及分支网站的工作就变得更加复杂，甚至还需重新建设网站；如此下去，用户始终在一个高成本、低效率的循环中升级、整合…&lt;/p&gt;&lt;p&gt;于是，我们听到许多用户这样的反馈：&lt;/p&gt;&lt;p&gt;页面制作无序，网站风格不统一，大量信息堆积，发布显得异常沉重；&lt;/p&gt;&lt;p&gt;内容繁杂，手工管理效率低下，手工链接视音频信息经常无法实现；&lt;/p&gt;&lt;p&gt;应用难度较高，许多工作需要技术人员配合才能完成，角色分工不明确；&lt;/p&gt;&lt;p&gt;改版工作量大，系统扩展能力差，集成其它应用时更是降低了灵活性；&lt;/p&gt;&lt;p&gt;对于网站建设和信息发布人员来说，他们最关注的系统的易用性和的功能的完善性，因此，这对网站建设和信息发布工具提出了一个很高的要求。&lt;/p&gt;&lt;p&gt;首先，角色定位明确，以充分保证工作人员的工作效率；其次，功能完整，满足各门道&amp;quot;把关人&amp;quot;应用所需，使信息发布准确无误。比如，为编辑、美工、主编及运维人员设置权限和实时管理功能。&lt;/p&gt;&lt;p&gt;此外，保障网站架构的安全性也是用户关注的焦点。能有效管理网站访问者的登陆权限，使内网数据库不受攻击，从而时刻保证网站的安全稳定，免于用户的后顾之忧。&lt;/p&gt;&lt;p&gt;根据以上需求，一套专业的内容管理系统CMS应运而生，来有效解决用户网站建设与信息发布中常见的问题和需求。对网站内容管理是该软件的最大优势，它流程完善、功能丰富，可把稿件分门别类并授权给合法用户编辑管理，而不需要用户去理会那些难懂的SQL语法。&lt;/p&gt;', '1526611606', '1527555060');
INSERT INTO `ey_article_content` VALUES ('12', '21', '&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;SEO（搜索引擎优化）和有效的网站设计是齐头并进的。好的网站设计是关于创建一个吸引目标受众的网站，并让他们采取某种行动。但是，如果该网站不遵循目前的 SEO 最佳做法，它的排名将会受到影响，从而会导致真正参与该网站的访问者的数量的较少。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;相反地，如果将关注的焦点放在搜索引擎优化以及如何取悦搜索引擎蜘蛛上，那么网站可能会排名很高，并且会获得大量的搜索引擎流量，但是如果设计很不尽人意，那就不一样了。为了在当今的数字环境中取得成功，必须将重点放在网站设计和搜索引擎优化上。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; font-weight: 700;&quot;&gt;一、但是，SEO 不会扼杀掉网页设计师的创造力吗？&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;在过去的五年中，对“优化设计”的巨大需求已经被网页设计师所接受。在此之前，设计师们主要关注的是用户的体验，而不是“机器人”的体验。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;如今，设计师不仅要让网站看起来有吸引力，而且要确保行为召唤必须符合网站页面“折叠”的要求，网站的加载速度必须很快，必须使用面包屑路径，清晰明了的导航选择，必须使用&amp;nbsp;CSS，JavaScript 文件必须保持在最低限度…这是一项艰巨的任务。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;一些设计师可能想知道，所有这些新的 SEO 规则是否会损害创建网站的自由？&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;对于“干净”的网站设计而言，它可以帮助一个网站快速加载，容易被搜索引擎蜘蛛抓取。因此，在现实中，创造力和最优化需要能够同时在一起“蓬勃发展”。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; font-weight: 700;&quot;&gt;二、把它们结合在一起&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;有一些核心元素支持每一个 SEO 策略和网站设计项目：&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; font-weight: 700;&quot;&gt;1.　关键词分析&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;在启动一个商业网站项目时，必须进行彻底的关键词分析。为了做到这一点，网页设计师必须紧密深入地了解客户的目标受众，并定义受众中的人口结构是如何融入到企业正试图达到的更大的目标市场。然后，应该对网站进行适当的关键词/长尾关键词优化。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; font-weight: 700;&quot;&gt;2.　内容层次结构&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;对于一个企业来说，创建好的内容是不够的，他们还必须在战略上规划内容的位置。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;有效的计划意味着将相关的内容放到虚拟的容器中，通过创造性的设计和内部链接让内容层级结构一目了然。并且，一个经过优化的网站是对用户和搜索引擎蜘蛛都很友好的网站。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; font-weight: 700;&quot;&gt;3.　从用户的角度思考&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;通常情况下，你的网站有越多的页面或文章，目标用户找到你的机会就越多。当他们着陆这些特定的页面的时候，你需要确保你能帮助他们轻松的找到你。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;所以你必须从用户的角度进行思考，要让用户立即清楚地知道他们在进行访问的页面的当前位置，并帮助用户在尽可能少的点击下从页面转换到另一页面。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; font-weight: 700;&quot;&gt;三、为什么&amp;nbsp;SEO 策略如此重要？&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;拥有合适的网站结构和信息架构，最终将会帮助企业提供一种引人入胜的用户体验，同时减少对每一次新增长的需求。但是，除非你的品牌是众所周知的，否则通常是搜索引擎对网站所收到的大部分流量负责。SEO 策略有能力利用重要的客户数据，挖掘新的潜在收入流。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;对于那些试图进行搜索引擎优化的网站所有者来说，有一些地方经常是麻烦的。现在，我将为网站所有者提供搜索引擎优化建议，以获得更高排名的页面。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; font-weight: 700;&quot;&gt;1. &amp;nbsp;URL 结构&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;大多数网站创建的 URL 都包含很多随机字符，比如问号，没有关键词或任何有价值的内容。当搜索引擎的 URL 包含 SEO 的关键词或短语时，页面将会在搜索引擎中排名更高。因此，在 URL 中设置关键词非常重要。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; font-weight: 700;&quot;&gt;2.　页面的标题&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;搜索引擎排名中最重要的因素之一是页面标题。不过，许多网站并没有改变他们的网页标题。在青柠建站平台中，你可以通过使用 SEO 标题标签插件，它很容易让你为你的文章和页面创建标题。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; font-weight: 700;&quot;&gt;3.　重复的内容&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;没有一个搜索引擎喜欢看到重复的内容。重复内容是一些网站的主要问题，因为类别页面和日历/日期页面经常会导致搜索引擎在多个页面上找到相同的内容。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;对于网站所有者来说，有几种方法可以克服重复的内容问题。其中一种方法是使用 robot.txt 文件，用来指导搜索引擎哪些页面应该被忽略，只留下要索引的主要页面。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; font-weight: 700;&quot;&gt;4. &amp;nbsp;Meta 标签&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;在设计一个传统的静态网站时，你可以为每个页面输入元标签（描述）。尽管这些标签在搜索引擎排名上的影响力没有以前那么大，但在你的页面上有这些标签并不会带来什么坏处。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;然而，大多数建站平台并没有给用户在写文章时添加元标签的选项。对于 青柠建站平台 用户来说，添加元标签插件将允许你为任何页面输入元标签。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; font-weight: 700;&quot;&gt;四、网页设计师在 SEO 方面的职责是什么？&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;搜索引擎优化是一个持续的过程，它不能通过以特定的方式设计一个网站来实现。当然，网页设计师应该付出相当大的努力来帮助客户构建一个优化的站点，但是网页设计师在 SEO 方面的职责是什么，以及客户的职责是什么？&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;作为一个企业主，你的网站的优化对你来说比设计师更重要（这并不是说设计师不关心，但是设计师的注意力通常集中在网站的视觉和功能上）。你比设计师更了解你的客户 / 潜在客户，所以你应该对你的目标有更多的建设性意见。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;也许有些客户对 SEO 和目标关键词可能不太了解，那么理想的情况是让客户和你在这个问题上协同工作。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;根据我的经验，让客户参与其中的最简单方法之一就是简单地解释网站上使用的词语和短语（标题、文案等）会对网站排名有直接的影响。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;我通常会要求客户给我一份他们认为潜在访问者可能会在搜索中使用的词语和短语列表。在我不太熟悉的行业中设计网站时，这一点尤其重要。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;当然，可能需要做一些研究。客户应该承担起关键词研究的责任，还是应该由设计师来负责？&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;我的经验是，如果客户参与进来，这项研究通常会更有效，但这并不总是可能的。设计师应该有足够的知识来为客户提供建议，并且应该愿意提供帮助，但是最终最好还是让客户尽可能地参与进来。事实上，如果客户关心 SEO，参与过程会达到一个更加合理的期望。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;设定现实的期望也可能是设计师的责任。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;我有一些潜在的客户来找我说：“我被 SEO 专家告知，只要在网站页面上插入竞争热门的关键词就可以让我的网站排名第一或第二”。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;在这种情况下，我会很明显地会指出，“搜索引擎优化需要持续的工作，而这种工作通常不能通过以某种方式创建网站来完成的。”&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;我经常建议客户在他们的网站上添加一个博客，以获得更多的内容，并提高排名的机会。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; font-weight: 700;&quot;&gt;结语&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;虽然这只是一个简短的总结，但这些是网站所有者和设计师将面对的最重要的 SEO 话题。通过了解这些知识，你可以更好地创建出对用户和搜索引擎都友好的网站。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;本文由易优小编设计 原创授权发布易优网站，未经授权，转载必究。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1526611744', '1531709637');
INSERT INTO `ey_article_content` VALUES ('14', '38', '&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;SEO很多伙伴都了解，就是搜索引擎排名优化，通过对网站内部和外部进行优化当用户搜索相应关键词时网站能够排名在搜索引擎前面，具体可以百度搜索“网络营销课程”查看商梦网校操作的案例！&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;但单页SEO很多伙伴可能会有点陌生，单页SEO是将单页网站与内容内容结合为一体的SEO优化方案，主要是提升网站流量利用率让用户打开网站就能看到目标页面，转换更多订单，创造更多收益。单页SEO的操作理念也是由商梦网校提出，并一起推荐操作大家的模式。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;那什么又是单页SEO站群呢，因为操作SEO成功率并不是100%，也就是意味着你做了并不会绝对有排名。因为在任何时候搜索引擎，特别是百度的索引数据库里，只有60%的网页数量。也就是说，大量的网页它是没有收录进来，它本身的能力所限无法做到中文的所有几百亿个网页都收录进来。所以，对于大部分网站，都有被删除网页，没有排名，或被K的经历，或没有排名。处理办法：坦然面对这一切。一个网站的成本才多少钱？如果因此对SEO失去信心，那就是最大的失去了。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;不过我们也想到了一个更好的解决方案，这个方案在最早期我们开始操作，并且取得了非常不错的成绩就是“站群”，我们可以假设一个网站排名的机会为1，如果我们用 10 个网站来进行优化排名机会可以提升 10 倍， 10 个网站我们也不要求都获得排名只需要有1- 3 个网站获得排名这个操作就是成功的，因为对于我们做站群来说投入 10 个网站的成本也就 1000 块左右；这个投资也是非常划算的，这个思路其实有点像竞价，不像传统的SEO，因为传统的SEO我们投资一个网站成本一两百，就想获得排名，然后给我们几百上千倍的回报。结果就相当于我们把希望寄托在一颗树上，结果这颗树没有开花结果，我们就饿死了。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;想给一个项目建立数十个网站也是需要掌握很多技术的，特别是批量建站方面，以及后期的维护。这次商梦网校升级加入的单页SEO站群操作方法，没有长篇大论直接给你演示怎么干，你只需要复制我们提供的方法就可以了。当然这里面也有很多核心的技术，比如域名注册和空间购买技巧虽然非常简单，但是直接会影响我们后期操作结果，我们给提供的技巧也会将你的成本降到低，如果投资建立 10 个网站域名与空间的成本不到 1000 元。相当于 1000 你就可以启动一个站群项目。核心的还是文章的采集，我们的原理是利用火车头采集原创文章然后实现挂机自动发布，只需要设置好每天几点运行软件就会自动更新网站文章，还会自动网站自动瞄文本，自动加入关键词。这些很多同学可能会问会不会太复杂，可以这样告诉你复杂的工作我们已经帮你搞定，到你使用的时候已经是打包好的解决方案。&lt;/p&gt;&lt;p style=&quot;box-sizing: border-box; margin-top: 0px; margin-bottom: 24px; white-space: normal; background-color: rgb(255, 255, 255); color: rgb(14, 14, 14); font-family: Arial, &amp;quot;Hiragino Sans GB&amp;quot;, 冬青黑, &amp;quot;Microsoft YaHei&amp;quot;, 微软雅黑, SimSun, 宋体, Helvetica, Tahoma, &amp;quot;Arial sans-serif&amp;quot;; text-align: justify;&quot;&gt;网站前期整体搭建只要花时间就能搞定，但真正考验人的基实还是在于后期优化，对于网站后期优化特别是外链增加收录和权重这一块，我们还是没有长篇大论会直接给你演示实用、高效的方法让你的站群快速的获得收录，增加权重，获得排名，你需要做的就拷贝我们商梦网校的方法和模式；这些经验都是我们长期操作整理下来的，并非几天修炼的结果。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1527555069', '1531709578');
INSERT INTO `ey_article_content` VALUES ('18', '59', '&lt;p&gt;Five Core Elements of Website Construction&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;img src=&quot;/uploads/allimg/20181220/39229581e20d2e22e750df528a43fe71.jpg&quot; style=&quot;box-sizing: inherit; border-style: none; vertical-align: top; max-width: 100%; height: auto !important;&quot;/&gt;&lt;/p&gt;&lt;p&gt;　　In order to implement network marketing, enterprises need to make websites first. Website is composed of many Web pages, and the design of these pages directly affects whether the website can be welcomed by users. To judge the design of a homepage, we should consider it comprehensively from many aspects, not only whether it is vividly and beautifully designed, but also whether the website can be considered for users to the greatest extent.&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;img alt=&quot;&quot; class=&quot;limg&quot; src=&quot;http://www.eyoucms.com/uploads/allimg/180426/1510032P3-1.jpg&quot; style=&quot;box-sizing: inherit; border-style: none; vertical-align: top; max-width: 100%; height: auto !important;&quot;/&gt;&lt;/p&gt;&lt;p&gt;　　&lt;/p&gt;&lt;p&gt;3. Product as the Core Principle&lt;/p&gt;&lt;p&gt;The most important purpose and function of website production is product display. The main purpose of customer visiting the website is to have a deep understanding of the products and services. The value of the website lies in flexibly displaying product instructions, pictures and even multimedia information to users. Even a simple website is at least equivalent to a product promotional material that can be updated at any time. Outdated product information ｏｒ imperfect product information can not only promote sales, but also affect customer confidence. When customers visit the website, they are not concerned about personal information, but about what kind of products they can provide and what advantages they have. Therefore, product as the core is a prerequisite for the success of the website.&lt;/p&gt;&lt;p&gt;Product information should generally include the following aspects: product name, product specifications, product use, product characteristics, product certification and product pictures. Secondly, product specifications, product uses and product characteristics should be described in as much detail as possible.&lt;/p&gt;&lt;p&gt;4. On the principle of strong information exchange ability of websites&lt;/p&gt;&lt;p&gt;If a website can only provide visitors to browse, but can not guide visitors to participate in part of the construction of website content, then its attraction is limited. Only when visitors can easily exchange information with information publishers can the charm of the website be fully embodied. Virtual forums are designed to discuss products among product users and between product users and product development managers. Online marketers can also use this to collect market information and formulate effective marketing plans. And the feedback information of website consumers is published directly on the Internet, which can attract consumers to visit the website and form a fixed relationship with customers.&lt;/p&gt;&lt;p&gt;When customers find products of interest on the Internet, how to make inquiries and feedback on the products in time? This is not only achieved by electronic mail. The website should provide corresponding information feedback module, so that customers can make inquiries ｏｒ feedback for one ｏｒ more products conveniently and quickly. At the same time, the salesman of the enterprise should be able to check the customer&amp;#39;s feedback information in time and reply in time: each business department ｏｒ salesman should be able to manage the customer&amp;#39;s information and feedback information conveniently according to the products they publish. Through the website, customers can provide a variety of online services and help information, such as FAQ, detailed contact information, online help forms, real-time chat to answer customer consultation and so on. At the same time, the use of the website can also achieve the purpose of improving customer relations, such as by issuing a variety of free mailing lists, providing prize guessing and other ways to attract users to participate. Through online questionnaires on the website, users&amp;#39;feedback information can be obtained, which can be used in product survey, consumer behavior survey, brand image survey and so on. It is an effective tool to obtain first-hand market information.&lt;/p&gt;&lt;p&gt;5. On the Principle of Perfect Retrieval Ability&lt;/p&gt;&lt;p&gt;For a website, how to organize reasonably the information content to be published so that the visitors can quickly and accurately find the information they are looking for is the key to the success of the website content organization. If the structure design of the website can not make the customers find the information they need conveniently and quickly, the best design can not attract long-term customers. Even if he is attracted to the home page of the website, his visits will be interrupted in the future. In order to achieve the above design goals, some websites have designed information index and directory index on the web page. Users can quickly find the part of information they are interested in.&lt;/p&gt;&lt;p&gt;Therefore, in order to make the content of the website practical, a certain scale of website must provide retrieval function, so as to facilitate users to find the information of this website. In order to create convenient conditions for visitors, web designers often design web content into tree structure to facilitate vertical query. Visitors can penetrate into all the information content of &amp;quot;tree rights&amp;quot; and &amp;quot;treetops&amp;quot; layer by layer from the home page. In addition, a search system can be designed to make it easy for visitors to find relevant content. Website search system should be well designed to allow visitors to enter from any page. At the same time, any page of the website should be designed with a &amp;quot;back home page&amp;quot; link to facilitate visitors to return to the &amp;quot;trunk&amp;quot;.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1547463745', '1547463745');
INSERT INTO `ey_article_content` VALUES ('19', '60', '&lt;p&gt;Experience the Core Value of Enterprise Website Perfectly from Three Aspects&lt;/p&gt;&lt;p&gt;　　With the rapid development of the Internet, the development of an enterprise can not be separated from the development of the Internet. Enterprises pay attention to the construction of enterprise websites, then it will inevitably bring good results. The core value of enterprise website construction is directly reflected in whether the website can satisfy the interests and needs of users and businessmen, improve the development of enterprises and improve the development channels of enterprises.&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;img class=&quot;rimg&quot; src=&quot;http://www.eyoucms.com/uploads/allimg/180426/150RQ155-0.jpg&quot; style=&quot;box-sizing: inherit; border-style: none; vertical-align: top; max-width: 100%; height: auto !important;&quot;/&gt;&lt;/p&gt;&lt;p&gt;　　&lt;/p&gt;&lt;p&gt;A good navigation system is a good tour guide. Every website design plan has the right and obligation to help customers find the subject of content and what they need in time and accurately.&lt;/p&gt;&lt;p&gt;On the other hand, it embodies the satisfaction of the website to the cash interests of the merchants, which is based on the satisfaction of the website to the users&amp;#39;needs. Because, if the website can not meet the needs of users&amp;#39;interests, users will not create value for the website, can not attract more users to participate in the website, can not achieve the cyclical growth of the value of the website, the scale of users will not be able to achieve greater development, it is difficult to meet the needs of cash interests of businesses. Businessmen put advertisements on the website is based on the website to promote transactions. The more likely it is and the more likely it is to be traded, the more cash profit the merchants can get. Otherwise, they will waste advertising money in vain.&lt;/p&gt;&lt;p&gt;Secondly, it is reflected in the satisfaction of users&amp;#39;interests and needs. In the initial stage of development, the website is more to provide users with the content they need, actively create content value, meet the needs of users&amp;#39; basic interests, especially the answers to various questions, the provision of relevant interest ｏｒ professional information, and the release of various information. Let users stay for a certain reason. On the premise of doing a good job in basic work, you can focus on meeting the interests of users&amp;#39;transactions, ｏｒ at the beginning, combine transactions with the relevant needs of users to create a highly active trading sector, providing users with the most comprehensive and convenient information and venues for such transactions, and actively promote users&amp;#39; activity. Improve the degree and quickly realize the profit of the website.&lt;/p&gt;&lt;p&gt;We think that it is a very wise choice for customers to see the industry information highly related to the information they are looking for on the home page. A website design without a lot of professional information is not qualified website design, nor can it really solve the problem fundamentally for customers.&lt;/p&gt;&lt;p&gt;We have to try our best to show how we can stand out among many competitors in the industry, so that customers can trust us? Website construction companies believe that only by showing their professional strength as far as possible, of course, in addition to these three points, website design still has many things to pay attention to, but no matter what, the core value should still be important. Points to reflect, focusing on the core content is the essence of website design, we know that the core concept of website operation is value, stationmasters must firmly establish, all from the user, actively meet user needs, let users play their creativity, create value for the website, realize the cyclical growth of website value, let the stationmaster operation become user operation is our ultimate goal. Once and for all, profits continue.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1545271034', '1545271034');
INSERT INTO `ey_article_content` VALUES ('20', '61', '&lt;p&gt;Looking back on the 40-year history of Chinese beverage development, there is always a taste of your childhood memory. For memory, the taste is often the most beautiful. The beverage you drank in childhood is still enjoyable in retrospect.&lt;/p&gt;&lt;p&gt;Today is the June 1 children&amp;#39;s day. Qingshan capital has compiled a brief history of China&amp;#39;s beverage development in the past 40 years, and it can be a holiday pastime. By the way, can you find the smell in your memory?&lt;/p&gt;&lt;p&gt;The First Stage: The Opening Age of Chinese Taste Bud&lt;/p&gt;&lt;p&gt;Pepsi Cola opened its first factory in China&lt;/p&gt;&lt;p&gt;In 1981, Coca-Cola officially launched its first production line in China, mainly supplying tourist hotels, selling to foreigners to collect foreign exchange, and Pepsi Cola established its first canning plant in Shenzhen.&lt;/p&gt;&lt;p&gt;In 1982, the state incorporated beverages into the &amp;quot;national plan management products&amp;quot;, and Coca-Cola began to sell in Beijing market.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1545271068', '1545271068');
INSERT INTO `ey_article_content` VALUES ('21', '62', '&lt;p&gt;John Quilch says that social media has many marketing challenges, and how to value fans is a big problem. From the marketing point of view, we should pay attention to strong ties and weak ties. You may think that strong bonds have the greatest marketing impact. Research has found that it&amp;#39;s not like this, but people who are more distant from you.&lt;/p&gt;&lt;p&gt;Speaker John Quilch&lt;/p&gt;&lt;p&gt;(Professor of Harvard Business School, former Dean of London Business School and Vice Dean of CEIBS)&lt;/p&gt;&lt;p&gt;Thank you very much for coming back to listen to my lecture on Sunday morning. For you entrepreneurs, ｏｒ those who want to be entrepreneurs, I have prepared a special lecture today.&lt;/p&gt;&lt;p&gt;Many entrepreneurs do not define their ultimate vision well, so they are busy putting out fires and surviving every day.&lt;/p&gt;&lt;p&gt;Start-up marketing, you must plan well&lt;/p&gt;&lt;p&gt;Today we will start with the topic of entrepreneurship marketing, including how you survive and succeed. Start-up marketing includes four key areas, which you must plan well:&lt;/p&gt;&lt;p&gt;Have the right target customers and end users;&lt;/p&gt;&lt;p&gt;Have the right products and services&lt;/p&gt;&lt;p&gt;We should have a very good talent team to make business ideas come true.&lt;/p&gt;&lt;p&gt;We should have good partners, not distributors, but accountants, lawyers and other service partners.&lt;/p&gt;&lt;p&gt;So, what is entrepreneurial marketing?&lt;/p&gt;&lt;p&gt;First, it is reverse engineering design from vision to action.&lt;/p&gt;&lt;p&gt;When Starbucks has only five stores, the founder has a vision to make Starbucks the third space in your life.&lt;/p&gt;&lt;p&gt;Starting with the vision, entrepreneurs need to design reverse engineering backwards: see what actions are needed to achieve the vision. Many entrepreneurs do not define their ultimate vision well, so they are busy putting out fires and surviving every day.&lt;/p&gt;&lt;p&gt;Second, fast cycle, low cost trials to provide evidence&lt;/p&gt;&lt;p&gt;With a vision to think about, how to do some fast and low-cost experiments to test creativity, to partners, customers and so on, this is a very good vision. In other words, you need short-term results as evidence.&lt;/p&gt;&lt;p&gt;Third, develop with forward-looking customers&lt;/p&gt;&lt;p&gt;Most customers are conservative and don&amp;#39;t want to waste time on new companies. You have to find visionary clients who are willing to take risks on you. They may be small, emerging customers, not good base customers in the market you want to enter.&lt;/p&gt;&lt;p&gt;Fourth: Create a comprehensive road map for trotting and fast running&lt;/p&gt;&lt;p&gt;It includes product roadmap, customer roadmap, partner roadmap and talent roadmap. An entrepreneur should have a road map for a year ｏｒ even three years to see how you want the company to make progress in these four dimensions.&lt;/p&gt;&lt;p&gt;For instance&lt;/p&gt;&lt;p&gt;In the late 1990s, John Osher invented SpinBrush, a low-cost electric toothbrush. Because he saw that there was a big gap in the market: ordinary manual toothbrushes cost two dollars each, and electric toothbrushes cost 50 dollars. But between the two, there is no intermediate product.&lt;/p&gt;&lt;p&gt;He wants to develop a toothbrush at a price somewhere between the two. He thought about the performance criteria for the success of the new toothbrush:&lt;/p&gt;&lt;p&gt;Cleaner than manual toothbrushes, ｏｒ consumers will not pay higher prices;&lt;/p&gt;&lt;p&gt;The self-contained batteries can last for three months, if the batteries need to be changed every week, they will collapse.&lt;/p&gt;&lt;p&gt;The packaging has the characteristics of trial use. We would like to see how the toothbrush rotates after starting.&lt;/p&gt;&lt;p&gt;The retail price is less than $6.&lt;/p&gt;&lt;p&gt;He positioned the new toothbrush as a better manual toothbrush rather than a cheaper electric toothbrush.&lt;/p&gt;&lt;p&gt;For consumers, it increased from $2 to $6, not from $50 to $6. Because if it&amp;#39;s the latter, retailers will feel compensated: consumers only spent $6, compared with $50 before. But now, consumers have gone from two to six.&lt;/p&gt;&lt;p&gt;So entrepreneurs need to think not only about end users, but also about how to make more money for distributors, because you have to go through them to get products to end customers. When defining competition, a good positioning statement is very important. Finally, he sold the company to P&amp;amp;amp;G, making a total of $480 million.&lt;/p&gt;&lt;p&gt;You see, it&amp;#39;s very simple, because he has a lot of consumer insights, filling the market gap that nobody sees.&lt;/p&gt;&lt;p&gt;Another example&lt;/p&gt;&lt;p&gt;Intuit, the company&amp;#39;s founder, discovered 20 years ago that many people have trouble filling out a tax return every year before handing it over to the government when they are dealing with their own taxes.&lt;/p&gt;&lt;p&gt;Intuit is the first company to develop personal finance software, especially tax management software, which can be used by both individuals and small businesses. But this useful package, no one knows where to sell it, no one believes it works.&lt;/p&gt;&lt;p&gt;Sometimes your biggest problem is how to get the distributor through your new product. They distribute a lot of things and don&amp;#39;t have time to spend five hours checking whether your unknown product works ｏｒ not.&lt;/p&gt;&lt;p&gt;Finally, he directly assured the consumer that if he bought the product, he would not learn how to use it in six minutes. The money would be refunded to you and the product would be given to you.&lt;/p&gt;&lt;p&gt;Besides refunding money, what else did they do differently?&lt;/p&gt;&lt;p&gt;With the buyer&amp;#39;s permission, follow the buyer to observe his first use.&lt;/p&gt;&lt;p&gt;All executives of the company have to spend two hours a month doing customer technical support and listening to customer problems.&lt;/p&gt;&lt;p&gt;Technical support for customer service is the only way for promotion in the company.&lt;/p&gt;&lt;p&gt;Read the customer&amp;#39;s letter aloud in front of all the executives, whether it&amp;#39;s thanks ｏｒ criticisms.&lt;/p&gt;&lt;p&gt;This makes 50% of their sales come from word of mouth.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1545271106', '1545271106');
INSERT INTO `ey_article_content` VALUES ('22', '63', '&lt;p&gt;Various marketing methods have long been dazzling, but the essence is to study customers (consumers), research the needs and needs of customers, and make products ｏｒ services targeted. The era of big data has given it a new term: precision marketing. The first areas where big data is applied are mostly for the customer-facing industry. The first application scenarios are mostly precision marketing.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;“Wine is good, and the alley is deep.” Information about products ｏｒ services should be delivered to customers before they can lead to transactions. It is generally believed that it is up to advertising to convey product ｏｒ service information to customers. Advertising has existed in the past, and the &amp;quot;three bowls are not on the job&amp;quot; wine cellar is advertising. In the era of no Internet, we are familiar with TV commercials, radio advertisements, print print advertisements, outdoor billboards, etc., of course, including sipping and selling. But the past advertisements are thousands of people and do not distinguish between audiences. Later, the merchants have collected CRM information from customers, and after customer classification, they can better serve different customer groups. The Internet + Big Data era has given CRM new opportunities for development. Managing customers is no longer a simple digital statistic and no personal (ｏｒ simple clustering) direct mail ｏｒ fixed investment. As merchants know more about customers and understand more deeply, they have the opportunity to provide customers with personalized marketing solutions to further improve the customer experience and become personalized marketing ｏｒ precision marketing. In the era of big data, many of the past impossibility has become possible, and marketing activities have also won new development opportunities.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Different times, the form of business management will change, but the essence is two things: open source, throttling. Open source is to open up new customers and discover new business opportunities; throttling is to reduce internal operating costs and improve resource utilization efficiency. To achieve this requires data-driven decision making. In the past, people also collected and applied many strong related data related to business activities in long-term business activities, and also formed the criteria for selecting customers. In view of the technical bottleneck at that time, the cost of data collection and data analysis for large samples was too high to be widely used. In the era of big data, people have the possibility of collecting data and storing data cheaply. Cheap computing resources make data analysis possible.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Behind the big data precision marketing is to use multi-dimensional data to observe customers, describe customers, that is, for customer portraits. It is no exaggeration to say that relying on big data can make marketers know customers better than in the past and understand customers&amp;#39; needs better than customers themselves. Marketers don&amp;#39;t want to know who the customer is, where they are, what the consumption habits are, what they need, when they need it, and how to pass them to them more effectively. The answer can be found through data collection and data analysis. Accurate marketing can not only help businesses open source -- discover potential customers, but also help merchants cut costs -- to identify potential risks. When we learn more about our customers, we know which customers may be at risk.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;If you ask each operator whether they will use their experience to market, most of the answers are yes. But if you ask the operators whether they will use the data for marketing, I am afraid the answer is very varied. It is generally believed that the application of data for marketing is a big company&amp;#39;s business, and it has no connection with small companies. In fact, as big as multinational companies, as small as street vendors, using data for marketing, they will receive unexpected results. Don&amp;#39;t believe it? Street vendors pay attention to the weather forecast (wind, rain, ｏｒ exposure) to know what business opportunities there are tomorrow, and then know how to stock up. It is recommended that people in small and medium-sized companies should not reject the idea of precision marketing, and may wish to learn the methods of precision marketing. Even if the operator has a wealth of experience, it will be helpful to digitize the experience to operate.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;The book &amp;quot;Subversion Marketing&amp;quot; is to teach readers how to use big data to do marketing. The book is rich in cases and the language is readable. It is worth reading about all the friends of big data marketing.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;I agree with many points in the book: &amp;quot;Big data redefines the rules of industrial competition, not the size of the data, not the statistical technology, nor the powerful computing power, but the ability to interpret the core data.&amp;quot; Today, many people are entangled in the definition of big data, we really should pay more attention to the core value understanding and application of data. The question asked in the book is also very important. The operators usually have a lot of problems, but when they ask the question, there may be deviations, which leads to &amp;quot;a thousand miles of lost.&amp;quot; Asking questions about the ability to improve involves thoughts and methods that need to be improved during exercise. Verifying that the question is right is exactly where the data analyst can contribute.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;The book also raises two questions that deserve more in-depth thinking:&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;It is not enough to just find out the consumption habits of different customer groups and remind customers to spend at the right time. For example, a consumer&amp;#39;s normal rational consumption for one month is at the level of two thousand yuan, and is generally consumed in two stores, A and B. The A store uses the concept of precision marketing to allow consumers to spend both of these two thousand dollars in the A store. With the B store&amp;#39;s later, the consumer may return to the B store to spend two thousand dollars. Today, when the demand for excess supply is insufficient, the distribution ｏｒ migration of existing consumption among different businesses cannot bring about an increase in the total amount of social consumption. A higher level of application for big data marketing is to know in advance the needs of customers that have not been met ｏｒ even discovered. The value mining of big data has the opportunity to connect merchants (including manufacturers) with customers, so that merchants can provide more products ｏｒ services that meet the individual needs of customers, so that customers&amp;#39; willingness to consume will increase. This is a new challenge for data value mining workers.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;The more data, the better? Many big data companies are keen to use crawlers to &amp;quot;crawl&amp;quot; various data online. However, the same data set has different value density in different application scenarios. The specific application scenario is not the more data dimensions, the better. It is necessary to collect data and use data around the application target. Raising the dimension to collect more data must help to describe things in more detail, but undoubtedly increases the complexity of processing data. Every advancement in technology has brought new imagination to human beings. It is inevitable that desires will be inflated and confident, and the cognition of the world will be upgraded, even unrestrained. Later, it was discovered that the use of resources for the promotion of the dimension, the wisdom can not keep up, the unconstrained upgrade is the complexity of the solution, calm down will restart the thinking of dimensionality. Perhaps human cognition and wisdom are alternately moving forward in dimensioning, dimension reduction, re-elevation, and then dimension reduction. The book&amp;#39;s dimensionality thinking, if necessary, return to the element of thinking to give people a revelation.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;The tools of the big data era are of course important, and the way of thinking is more important.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1545271237', '1545271237');
INSERT INTO `ey_article_content` VALUES ('23', '64', '&lt;p&gt;After learning what seo means, you can learn seo.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;What is seo, from the official explanation, seo=Search Engine Optimization, which is search engine optimization.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Use Baidu ｏｒ other search engines, enter a certain keyword in the search box, such as the wrought iron gate, the top is with the ad typeface, the background is slightly different is the bidding position, which is commonly known as the sem position.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Seo is an online marketing method based on search engine marketing. Through seo technology, it can improve the keyword ranking of the website, get the display, and then get the exposure, and then get the user click, and then get the conversion.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;One: seo classification.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;In terms of refinement, all the points that are conducive to the promotion of the keyword ranking of the website can be summarized in the seo. For the sake of understanding, we divide the seo into the seo and the seo.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;1: SEO inside the station.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;What is a seo in the station? In general terms, it refers to the internal optimization of the website, that is, the internal optimization of the website itself, including code tag optimization, content optimization, security construction, user experience and so on.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;2: SEO outside the station.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;What is a stand outside seo? Generally speaking, it is the external optimization of the website, including external chain construction, brand building, speed optimization, and drainage.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Two: seo related advice.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;1: It is recommended to position seo in an online marketing mode. In the process of learning and using seo, it is used as a channel for obtaining traffic.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;2: The ideal platform for novices to learn seo is Baidu search resource platform rather than others; theory and practical operation is a more effective way of learning; experienced seo masters can master seo faster; more thinking, more summaries, can comprehend The essence of seo.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;3: Before learning seo, it is necessary to be familiar with the relevant seo terms.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;4: Many times, the theory and reality of seo are contrary to each other. That is to say, the theoretical point of seo is not complicated, and the operating point is difficult to achieve.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Novices are exposed to seo and feel at a loss. Please read the seo terminology and it will become easier later.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1545271307', '1545271307');
INSERT INTO `ey_article_content` VALUES ('24', '65', '&lt;p&gt;With the enrichment and development of web applications, many websites often cannot quickly follow the footsteps of a large amount of information derivation and business model changes. It often takes a lot of time, manpower and material resources to handle information ｕｐｄａｔｅ and maintenance work. When encountering website expansion, The integration of internal and external networks and branch sites has become more complicated, and even the need to rebuild the website; so, users continue to upgrade and integrate in a high-cost, low-efficiency cycle...&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;So, we heard feedback from many users:&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;The page is produced out of order, the style of the website is not uniform, and a large amount of information is accumulated, and the release is extremely heavy;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;The content is complicated, manual management is inefficient, and manual linking of video and audio information is often impossible;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;The application is difficult, and many jobs require the cooperation of technical personnel to complete, and the division of roles is not clear;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;The revision workload is large, the system expansion capability is poor, and the flexibility is reduced when integrating other applications;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;For website construction and information release personnel, they are most concerned about the ease of use and functionality of the system. Therefore, this puts a high demand on website construction and information publishing tools.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;First of all, the role is clearly defined to fully ensure the efficiency of the staff; secondly, the function is complete, to meet the needs of the doorkeeper &amp;quot;gatekeeper&amp;quot; application, so that the information is released accurately. For example, set permissions and real-time management functions for editors, artists, editors, and operations personnel.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;In addition, the security of the website architecture is also the focus of users. It can effectively manage the login rights of website visitors, so that the intranet database is not attacked, thus ensuring the security and stability of the website at all times and avoiding the worries of users.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;According to the above requirements, a professional content management system CMS came into being to effectively solve the common problems and needs in user website construction and information release. The management of website content is the biggest advantage of the software. It has complete processes and rich functions. It can classify manuscripts and authorize them to edit and manage them. It does not require users to pay attention to the incomprehensible SQL syntax.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1545272018', '1545272018');
INSERT INTO `ey_article_content` VALUES ('25', '66', '&lt;p&gt;SEO (Search Engine Optimization) and effective website design go hand in hand. Good website design is about creating a website that appeals to your target audience and letting them take some action. However, if the site does not follow current SEO best practices, its ranking will be affected, resulting in fewer visitors actually participating in the site.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Conversely, if you focus on search engine optimization and how to please search engine spiders, then the site may rank high and get a lot of search engine traffic, but if the design is not satisfactory, it will be different. It is. In order to succeed in today&amp;#39;s digital environment, the focus must be on website design and search engine optimization.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;1. But, does SEO not kill the creativity of web designers?&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;In the past five years, the huge demand for “optimized design” has been accepted by web designers. Prior to this, designers focused on the user experience rather than the &amp;quot;robot&amp;quot; experience.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Today, designers not only want to make the site look attractive, but also to ensure that the ｃａｌｌ to action must meet the &amp;quot;folding&amp;quot; requirements of the site page, the site must be loaded quickly, must use the breadcrumb path, clear navigation options, must With CSS, JavaScript files must be kept to a minimum... this is a daunting task.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Some designers may wonder if all these new SEO rules would undermine the freedom to create a website?&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;For &amp;quot;clean&amp;quot; website design, it can help a website ｌｏａｄ quickly and be easily crawled by search engine spiders. Therefore, in reality, creativity and optimization need to be able to “prosper” together at the same time.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Second, combine them together&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;There are some core elements that support every SEO strategy and website design project:&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Keyword analysis&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;A thorough keyword analysis is required when launching a commercial website project. In order to do this, web designers must understand the target audience of the customer closely and define how the demographic structure of the audience fits into the larger target market that the company is trying to achieve. Then, you should do the appropriate keyword/long tail keyword optimization for the website.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;2. Content hierarchy&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;For a company, creating good content is not enough, they must also strategically plan where the content is.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Effective planning means putting relevant content in a virtual container, with a creative design and internal links that make the content hierarchy visible at a glance. And, an optimized website is a website that is friendly to both users and search engine spiders.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;3. Think from the perspective of the user&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;In general, the more pages ｏｒ articles you have on your site, the more opportunities your target users will find. When they land on these specific pages, you need to make sure that you can help them find you easily.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;So you have to think from the user&amp;#39;s point of view, let the user know immediately the current location of the page they are visiting, and help the user switch from page to page with as few clicks as possible.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;3. Why is the SEO strategy so important?&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Having the right site structure and information architecture will ultimately help companies deliver a compelling user experience while reducing the need for every new growth. However, unless your brand is well known, it is often the search engine that is responsible for most of the traffic received by the site. The SEO strategy has the ability to leverage key customer data to mine new potential revenue streams.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;For website owners who are trying to do SEO, there are some places that are often troublesome. Now, I will provide search engine optimization suggestions for website owners to get higher ranking pages.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;1. URL structure&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;URLs created by most websites contain many random characters, such as question marks, no keywords, ｏｒ anything of value. When the search engine&amp;#39;s URL contains SEO keywords ｏｒ phrases, the page will rank higher in the search engine. Therefore, it is very important to set keywords in the URL.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;2. The title of the page&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;One of the most important factors in search engine ranking is the page title. However, many websites have not changed their page titles. In the Lime website, you can use the SEO title tag plugin, which makes it easy for you to create titles for your articles and pages.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;3. Duplicate content&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;No search engine likes to see duplicate content. Duplicate content is a major issue for some websites, as category pages and calendar/date pages often cause search engines to find the same content on multiple pages.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;There are several ways for website owners to overcome duplicate content issues. One way is to use a robot.txt file to guide the search engine which pages should be ignored, leaving only the main page to be indexed.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;4. Meta tags&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;When designing a traditional static website, you can enter a meta tag (description) for each page. Although the impact of these tags on search engine rankings is not as great as it used to be, having these tags on your page doesn&amp;#39;t hurt.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;However, most site-building platforms do not give users the option to add meta tags when writing articles. For users of the Lime website, adding a meta tag plugin will allow you to enter a meta tag for any page.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;4. What is the role of web designers in SEO?&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Search engine optimization is an ongoing process that cannot be achieved by designing a website in a specific way. Of course, web designers should put a lot of effort into helping customers build an optimized site, but what are the responsibilities of web designers for SEO and what are their responsibilities?&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;As a business owner, the optimization of your website is more important to you than the designer (this is not to say that the designer does not care, but the designer&amp;#39;s attention is usually focused on the visual and functional aspects of the website). You know your customers/leaders better than the designers, so you should have more constructive advice on your goals.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Maybe some customers may not know much about SEO and target keywords, so the ideal situation is to let customers and you work together on this issue.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;In my experience, one of the easiest ways to get customers involved is to simply explain the words and phrases (titles, copywriting, etc.) used on the site that have a direct impact on the site&amp;#39;s ranking.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;I usually ask customers to give me a list of words and phrases that they think potential visitors might use in their searches. This is especially important when designing websites in industries that I am not familiar with.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Of course, some research may be needed. Should the customer take responsibility for keyword research ｏｒ should it be the responsibility of the designer?&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;My experience is that this research is usually more effective if the customer is involved, but this is not always possible. Designers should have enough knowledge to advise clients and should be willing to help, but in the end it is best to get the customer involved as much as possible. In fact, if the customer cares about SEO, the participation process will achieve a more reasonable expectation.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Setting realistic expectations may also be the responsibility of the designer.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;I have some potential customers who come to me and say, “I was told by SEO experts that by inserting popular keywords on the website page, I can rank my website first ｏｒ second”.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;In this case, I will obviously point out that &amp;quot;search engine optimization requires continuous work, and this kind of work can usually not be done by creating a website in some way.&amp;quot;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;I often advise customers to add a blog on their website to get more content and increase their chances of ranking.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Conclusion&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;While this is a short summary, these are the most important SEO topics that website owners and designers will face. By understanding this knowledge, you can better create websites that are user-friendly and search engine friendly.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;This article is designed by Yi You Xiaobian Original Authorized to publish Yiyou website, unauthorized, reprinted.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1545272054', '1545272054');
INSERT INTO `ey_article_content` VALUES ('26', '67', '&lt;p&gt;Many SEO partners understand that search engine ranking optimization is optimized by internal and external websites. When users search for corresponding keywords, the website can be ranked in front of the search engine. Specifically, Baidu search &amp;quot;network marketing course&amp;quot; to view the operation of Shangmeng. Case!&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;But a single page SEO many partners may be a bit strange, single-page SEO is a SEO optimization program that combines a single-page website with content content, mainly to improve the website traffic utilization so that users can see the target page when they open the website, and the conversion is more More orders, create more revenue. The operation concept of the single-page SEO is also proposed by Shangmeng.com, and together it is recommended to operate the model.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;So what is a single-page SEO station group, because the success rate of operating SEO is not 100%, which means that you do not definitely have a ranking. Because at any time the search engine, especially Baidu&amp;#39;s index database, only 60% of the number of pages. That is to say, a large number of web pages are not included, and its own capabilities are limited to all the tens of billions of web pages in Chinese. So, for most sites, there are deleted pages, no rankings, ｏｒ experiences with K, ｏｒ no rankings. Deal with it: calmly face it all. How much is the cost of a website? If you lose confidence in SEO, it is the biggest loss.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;However, we also thought of a better solution. We started the operation in the earliest stage and achieved a very good result of “station group”. We can assume that the chance of a website ranking is 1, if we use 10 Websites can be upgraded by 10 times for optimal rankings. We don’t require 10 websites to get rankings. We only need 1 to 3 websites to get rankings. This operation is successful because we put 10 websites for our group. The cost is about 1000 yuan; this investment is also very cost-effective, this idea is actually a bit like bidding, unlike traditional SEO, because the traditional SEO we invest in a website cost one ｏｒ two hundred, we want to get the ranking, and then give us a few hundred Thousands of times return. The result is equivalent to pinning our hopes on a tree. As a result, the tree has not blossomed and we are starving.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Wanting to build dozens of websites for a project also requires a lot of technology, especially in terms of mass construction and post-maintenance. This single-page SEO station group operation method upgraded by Shangmeng Online School does not have a long story to show you how to do it. You only need to copy the methods we provide. Of course, there are also a lot of core technologies in it. For example, domain registration and space purchase techniques are very simple, but they will directly affect our later operation results. The skills we provide will also lower your costs. If you invest in 10 websites. The cost of domain names and space is less than $1,000. Equivalent to 1000 you can start a station group project. The core is still the collection of the article. Our principle is to use the locomotive to collect original articles and then realize the automatic release of the hang-up. It only needs to set the software to automatically ｕｐｄａｔｅ the website articles when the software is running every day, and automatically automatically advertise the text automatically. . Many of these students may ask if it will be too complicated. You can tell us the complicated work. We have already helped you to get it. When you use it, it is already a packaged solution.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;The overall construction of the website can be done in the early stage, but the real test is based on the later optimization. For the post-optimization of the website, especially the addition of the external link and the weight, we still have no long-term discussion to give you a practical and efficient demonstration. The way to get your station group to quickly get included, increase the weight, get the ranking, you need to do to copy the methods and models of our business dream school; these experiences are compiled by our long-term operation, not the result of a few days of cultivation .&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1545272097', '1545272097');
INSERT INTO `ey_article_content` VALUES ('27', '68', '&lt;p&gt;Note: The User Interface (UI) design is one of several interdisciplinary topics involved in designing software products. Whether it is user experience (UX, User Experience), interaction design (ID, Interaction Design), ｏｒ visual / graphic design (Visual / Graphic Design), can involve user interface design.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;First, what is the user interface design?&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Broadly speaking, the user interface is the medium through which people interact with machines. The user issues an instruction to the machine, and the machine begins a process, responds to the message, and gives feedback. Users can make decisions about the next step based on user feedback.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Human Computer Inter-citon (HCI) focuses on the digital interface, that is, the past punch, command line, and today&amp;#39;s graphical interface (GUI, Graphic Design).&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;User interface design For digital products, the main focus is on layout, information structure, and the display of interface elements on the display and various terminal platforms. Video games and TV interfaces are also included.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1545272159', '1545272159');
INSERT INTO `ey_article_content` VALUES ('28', '69', '&lt;p&gt;Z Yuhan: User Interface (UI) design is one of several interdisciplinary topics involved in designing software products. Whether it is user experience (UX, User Experience), interaction design (ID, Interaction Design), ｏｒ visual / graphic design (Visual / Graphic Design), can involve user interface design.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;First, what is the user interface design?&lt;/p&gt;&lt;p&gt;Broadly speaking, the user interface is the medium through which people interact with machines. The user issues an instruction to the machine, and the machine begins a process, responds to the message, and gives feedback. Users can make decisions about the next step based on user feedback.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Human Computer Inter-citon (HCI) focuses on the digital interface, that is, the past punch, command line, and today&amp;#39;s graphical interface (GUI, Graphic Design).&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;User interface design For digital products, the main focus is on layout, information structure, and the display of interface elements on the display and various terminal platforms. Video games and TV interfaces are also included.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1545272218', '1545272218');
INSERT INTO `ey_article_content` VALUES ('29', '70', '&lt;p&gt;In the early morning of May 31st, Beijing time, Mary Mickel, who is known as the “Queen of the Internet”, published the Internet Trend Report for 2018, which is the 23rd year of her Internet report.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;The annual Internet Queen&amp;#39;s report will almost always become a must-read report for every Internet entrepreneur. So, who is the Internet Queen? Why is her report so concerned?&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Internet Queen: The symbol of Wall Street in the 90s&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;In September 1958, Mary Meeker was born in Indiana, USA.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;In 1982, Mikel joined Merrill Lynch, the most prestigious brokerage company at the time, as a stockbroker.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Mickel’s star analysts began in 1991, when she joined the well-known investment bank Morgan Stanley and began her brilliant career as a technology analyst.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Since 1995, Mikel&amp;#39;s work has changed with the changing trend of the Internet. She has focused on how well-known companies such as Yahoo, AOL and Amazon will adjust their structure and compete with each other.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;In 1996, Mary Mickel became the head of Morgan Stanley&amp;#39;s technology stock analysis department, and created a new career on Wall Street – Internet analysts. Just as junk bonds represent Wall Street in the 1980s, Mary Mickel became a symbol of Wall Street in the 1990s.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;At the end of 2010, Mikel resigned as managing director of Morgan Stanley, left Wall Street, and went to California to become a partner of the well-known venture capital KPCB. KPCB (Kleiner Perkins Caufield &amp;amp; Byers) was founded in 1972 and is the largest venture fund in the United States. Its most proud masterpiece is the creation of Netscape.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Internet Queen&amp;#39;s Report: Investment in the Internet, Bible, stock selection guide&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;In 1994, Mikel accidentally saw a story about the startup&amp;#39;s Mosaic web browser in the New York Times. Mikel immediately realized that such a web browser might change the way people get information. She then contacted the two founders of Mosaic and introduced the company to Wall Street investors.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Mosaic later changed its name to Netscape and was listed in New York in 1995. Thanks to the good relationship between Mikel and Netscape&amp;#39;s founders, Morgan Stanley became the lead underwriter for Netscape&amp;#39;s initial public offering (IPO).&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;On August 9 of that year, Netscape closed on the first day of listing, and the stock price surged from the issue price of US$14 to US$75, setting a record for the first day of the listed company. Netscape IPO also became a major symbol of the arrival of the Internet era.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;In 1995, in addition to being responsible for the listing of Netscape, Mikel and his colleague Chris Opp began to publish the &amp;quot;Internet Report&amp;quot;, and first proposed the analysis of network stocks such as &amp;quot;page views&amp;quot;. This report is regarded by investors as an investment Bible in the Internet field, and it has been published in public, which has caused great repercussions in the entire technology industry.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;In 1996-1997, Mikel and Morgan Stanley released the &amp;quot;Internet Advertising Report&amp;quot; and &amp;quot;Internet Retail Report&amp;quot;, which laid the foundation for the first analyst of Mikel&amp;#39;s Internet field. The Internet Queen’s report almost became a must-read report for every Internet entrepreneur at the time.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;The Internet Queen’s report is tantamount to a stock picking guide. The stocks of companies such as AOL, Dell, Amazon, and eBay that she recommended to investors quickly brought in more than ten times the return on investment.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;&amp;quot;God Prediction&amp;quot; in the Internet Queen&amp;#39;s Report&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;The main reason why the industry values the Internet Queen’s report so much is the predictions of Mikel’s gods. Below, we briefly list some examples of God predictions in the Internet Queen&amp;#39;s report.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1545272280', '1545272280');
INSERT INTO `ey_article_content` VALUES ('30', '71', '&lt;p&gt;Website construction, static pages and dynamic pages how to choose&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Why should e-commerce website construction use static page production? We all know that website production is divided into static page production and dynamic web page production. So which website design technology is better for building e-commerce websites?&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;The ultimate goal of our website is to provide users with a view, so it is most practical to think from the user&amp;#39;s point of view. Although the dynamic webpage creation technology has greatly improved the aesthetics of the webpage, it is not conducive to website optimization. Today&amp;#39;s Xiaobian focus and Let&amp;#39;s talk about why website construction uses static pages.&lt;/p&gt;&lt;p&gt;&lt;img alt=&quot;&quot; class=&quot;limg&quot; src=&quot;/uploads/allimg/20181220/4e7474448185b797c4c1d796b3e581fe.jpg&quot; style=&quot;box-sizing: inherit; border-style: none; vertical-align: top; max-width: 100%; height: auto !important;&quot;/&gt;&lt;/p&gt;&lt;p&gt;　　&lt;/p&gt;&lt;p&gt;The technical principle adopted for static website construction is one-to-one. That is to say, on a website, one content corresponds to one page. No matter how the website visitor operates, the server only transmits the inherent data to the requester. There is no script calculation and background database reading process, which greatly reduces some security risks. In addition to the above-mentioned features of high speed and high security, the static website design also has cross-platform and cross-server functions.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Now friends who are familiar with the working principle of the search engine should know that the information it provides to the majority of users is the information that exists in the database itself rather than the real-time information. The fixed information content is easier to accept and save. We may often encounter such problems. When we search for the information we need, the results may have expired. This is the inadequacy of static page website design, but because it is stable, it will not be ｄｅｌｅｔｅ.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Different from static page website design, the generated dynamic page information is not only difficult to be searched by search engines, but also slow to open and unstable. This is why so many professional website construction companies have repeatedly suggested that customers use static forms of website design. The reason is that some website construction companies will consider pseudo-static processing of the page, but I don&amp;#39;t know if you have noticed that the pseudo-statically processed URL is usually irregular.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1547463680', '1547463680');

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
INSERT INTO `ey_auth_role` VALUES ('1', '优化推广员', '0', '', '0', 'a:1:{i:0;s:2:\"cn\";}', '0', '1', 'a:3:{i:0;s:3:\"add\";i:1;s:4:\"edit\";i:2;s:3:\"del\";}', 'a:2:{s:5:\"rules\";a:8:{i:0;s:1:\"1\";i:1;s:1:\"3\";i:2;s:1:\"4\";i:3;s:1:\"8\";i:4;s:1:\"9\";i:5;s:2:\"10\";i:6;s:2:\"14\";i:7;i:2;}s:7:\"arctype\";a:63:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";i:5;s:1:\"6\";i:6;s:2:\"33\";i:7;s:2:\"34\";i:8;s:2:\"35\";i:9;s:2:\"36\";i:10;s:2:\"37\";i:11;s:2:\"38\";i:12;s:2:\"39\";i:13;s:2:\"40\";i:14;s:2:\"41\";i:15;s:2:\"42\";i:16;s:2:\"43\";i:17;s:2:\"44\";i:18;s:2:\"45\";i:19;s:2:\"46\";i:20;s:2:\"47\";i:21;s:2:\"48\";i:22;s:1:\"8\";i:23;s:2:\"32\";i:24;s:1:\"9\";i:25;s:2:\"30\";i:26;s:2:\"31\";i:27;s:2:\"11\";i:28;s:2:\"12\";i:29;s:2:\"13\";i:30;s:2:\"23\";i:31;s:2:\"20\";i:32;s:2:\"24\";i:33;s:2:\"25\";i:34;s:2:\"21\";i:35;s:2:\"26\";i:36;s:2:\"22\";i:37;s:2:\"27\";i:38;s:2:\"28\";i:39;s:2:\"29\";i:40;s:2:\"31\";i:41;s:2:\"32\";i:42;s:2:\"33\";i:43;s:2:\"34\";i:44;s:2:\"35\";i:45;s:2:\"36\";i:46;s:2:\"37\";i:47;s:2:\"38\";i:48;s:2:\"39\";i:49;s:2:\"40\";i:50;s:2:\"41\";i:51;s:2:\"42\";i:52;s:2:\"43\";i:53;s:2:\"44\";i:54;s:2:\"45\";i:55;s:2:\"46\";i:56;s:2:\"47\";i:57;s:2:\"48\";i:58;s:2:\"49\";i:59;s:2:\"50\";i:60;s:2:\"51\";i:61;s:2:\"52\";i:62;s:2:\"53\";}}', '1', '100', '1', '0', '1541207843', '0');
INSERT INTO `ey_auth_role` VALUES ('2', '内容管理员', '0', '', '0', 'a:1:{i:0;s:2:\"cn\";}', '0', '1', 'a:3:{i:0;s:3:\"add\";i:1;s:4:\"edit\";i:2;s:3:\"del\";}', 'a:2:{s:5:\"rules\";a:4:{i:0;s:1:\"1\";i:1;s:2:\"10\";i:2;s:2:\"14\";i:3;i:2;}s:7:\"arctype\";a:63:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";i:5;s:1:\"6\";i:6;s:2:\"33\";i:7;s:2:\"34\";i:8;s:2:\"35\";i:9;s:2:\"36\";i:10;s:2:\"37\";i:11;s:2:\"38\";i:12;s:2:\"39\";i:13;s:2:\"40\";i:14;s:2:\"41\";i:15;s:2:\"42\";i:16;s:2:\"43\";i:17;s:2:\"44\";i:18;s:2:\"45\";i:19;s:2:\"46\";i:20;s:2:\"47\";i:21;s:2:\"48\";i:22;s:1:\"8\";i:23;s:2:\"32\";i:24;s:1:\"9\";i:25;s:2:\"30\";i:26;s:2:\"31\";i:27;s:2:\"11\";i:28;s:2:\"12\";i:29;s:2:\"13\";i:30;s:2:\"23\";i:31;s:2:\"20\";i:32;s:2:\"24\";i:33;s:2:\"25\";i:34;s:2:\"21\";i:35;s:2:\"26\";i:36;s:2:\"22\";i:37;s:2:\"27\";i:38;s:2:\"28\";i:39;s:2:\"29\";i:40;s:2:\"31\";i:41;s:2:\"32\";i:42;s:2:\"33\";i:43;s:2:\"34\";i:44;s:2:\"35\";i:45;s:2:\"36\";i:46;s:2:\"37\";i:47;s:2:\"38\";i:48;s:2:\"39\";i:49;s:2:\"40\";i:50;s:2:\"41\";i:51;s:2:\"42\";i:52;s:2:\"43\";i:53;s:2:\"44\";i:54;s:2:\"45\";i:55;s:2:\"46\";i:56;s:2:\"47\";i:57;s:2:\"48\";i:58;s:2:\"49\";i:59;s:2:\"50\";i:60;s:2:\"51\";i:61;s:2:\"52\";i:62;s:2:\"53\";}}', '1', '100', '1', '0', '1541207846', '0');

-- -----------------------------
-- Table structure for `ey_channelfield`
-- -----------------------------
DROP TABLE IF EXISTS `ey_channelfield`;
CREATE TABLE `ey_channelfield` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '字段名称',
  `channel_id` int(10) NOT NULL DEFAULT '0' COMMENT '所属文档模型id',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '字段标题',
  `dtype` varchar(32) NOT NULL DEFAULT '' COMMENT '字段类型',
  `define` text NOT NULL COMMENT '字段定义',
  `maxlength` int(10) NOT NULL DEFAULT '0' COMMENT '最大长度，文本数据必须填写，大于255为text类型',
  `dfvalue` varchar(1000) NOT NULL DEFAULT '' COMMENT '默认值',
  `dfvalue_unit` varchar(50) NOT NULL DEFAULT '' COMMENT '数值单位',
  `remark` varchar(256) NOT NULL DEFAULT '' COMMENT '提示说明',
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
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=utf8 COMMENT='自定义字段表';

-- -----------------------------
-- Records of `ey_channelfield`
-- -----------------------------
INSERT INTO `ey_channelfield` VALUES ('1', 'add_time', '0', '新增时间', 'datetime', 'int(11)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533091575', '1533091575');
INSERT INTO `ey_channelfield` VALUES ('2', 'update_time', '0', '更新时间', 'datetime', 'int(11)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533091601', '1533091601');
INSERT INTO `ey_channelfield` VALUES ('3', 'aid', '0', '文档ID', 'int', 'int(11)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533091624', '1533091624');
INSERT INTO `ey_channelfield` VALUES ('4', 'typeid', '0', '当前栏目ID', 'int', 'int(11)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533091930', '1533091930');
INSERT INTO `ey_channelfield` VALUES ('5', 'channel', '0', '模型ID', 'int', 'int(11)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533092214', '1533092214');
INSERT INTO `ey_channelfield` VALUES ('6', 'is_b', '0', '是否加粗', 'switch', 'tinyint(1)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533092246', '1533092246');
INSERT INTO `ey_channelfield` VALUES ('7', 'title', '0', '文档标题', 'text', 'varchar(250)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533092381', '1533092381');
INSERT INTO `ey_channelfield` VALUES ('8', 'litpic', '0', '封面图', 'img', 'varchar(250)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533092398', '1533092398');
INSERT INTO `ey_channelfield` VALUES ('9', 'is_head', '0', '是否头条', 'switch', 'tinyint(1)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533092420', '1533092420');
INSERT INTO `ey_channelfield` VALUES ('10', 'is_special', '0', '是否特荐', 'switch', 'tinyint(1)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533092439', '1533092439');
INSERT INTO `ey_channelfield` VALUES ('11', 'is_top', '0', '是否置顶', 'switch', 'tinyint(1)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533092454', '1533092454');
INSERT INTO `ey_channelfield` VALUES ('12', 'is_recom', '0', '是否推荐', 'switch', 'tinyint(1)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533092468', '1533092468');
INSERT INTO `ey_channelfield` VALUES ('13', 'is_jump', '0', '是否跳转', 'switch', 'tinyint(1)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533092484', '1533092484');
INSERT INTO `ey_channelfield` VALUES ('14', 'author', '0', '编辑者', 'text', 'varchar(250)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533092498', '1533092498');
INSERT INTO `ey_channelfield` VALUES ('15', 'click', '0', '浏览量', 'int', 'int(11)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533092512', '1533092512');
INSERT INTO `ey_channelfield` VALUES ('16', 'arcrank', '0', '阅读权限', 'select', 'tinyint(1)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533092534', '1533092534');
INSERT INTO `ey_channelfield` VALUES ('17', 'jumplinks', '0', '跳转链接', 'text', 'varchar(250)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533092553', '1533092553');
INSERT INTO `ey_channelfield` VALUES ('18', 'ismake', '0', '是否静态页面', 'switch', 'tinyint(1)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533092698', '1533092698');
INSERT INTO `ey_channelfield` VALUES ('19', 'seo_title', '0', 'SEO标题', 'text', 'varchar(250)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533092713', '1533092713');
INSERT INTO `ey_channelfield` VALUES ('20', 'seo_keywords', '0', 'SEO关键词', 'text', 'varchar(250)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533092725', '1533092725');
INSERT INTO `ey_channelfield` VALUES ('21', 'seo_description', '0', 'SEO描述', 'text', 'varchar(250)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533092739', '1533092739');
INSERT INTO `ey_channelfield` VALUES ('22', 'status', '0', '状态', 'switch', 'tinyint(1)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533092753', '1533092753');
INSERT INTO `ey_channelfield` VALUES ('23', 'sort_order', '0', '排序号', 'int', 'int(11)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533092766', '1533092766');
INSERT INTO `ey_channelfield` VALUES ('24', 'content', '2', '内容', 'htmltext', 'longtext', '250', '', '', '', '1', '0', '1', '0', '0', '100', '1', '1533359739', '1533359739');
INSERT INTO `ey_channelfield` VALUES ('25', 'content', '3', '内容详情', 'htmltext', 'longtext', '250', '', '', '', '1', '0', '1', '0', '0', '100', '1', '1533359588', '1533359588');
INSERT INTO `ey_channelfield` VALUES ('26', 'content', '4', '内容详情', 'htmltext', 'longtext', '250', '', '', '', '1', '0', '1', '0', '0', '100', '1', '1533359752', '1533359752');
INSERT INTO `ey_channelfield` VALUES ('27', 'content', '6', '内容详情', 'htmltext', 'longtext', '250', '', '', '', '1', '0', '1', '0', '0', '100', '1', '1533464715', '1533464715');
INSERT INTO `ey_channelfield` VALUES ('29', 'content', '1', '内容详情', 'htmltext', 'longtext', '250', '', '', '', '1', '0', '1', '0', '0', '100', '1', '1533464713', '1533464713');
INSERT INTO `ey_channelfield` VALUES ('30', 'update_time', '-99', '更新时间', 'datetime', 'int(11)', '11', '0', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('31', 'add_time', '-99', '新增时间', 'datetime', 'int(11)', '11', '0', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('32', 'status', '-99', '启用 (1=正常，0=屏蔽)', 'switch', 'tinyint(1)', '1', '1', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('33', 'is_part', '-99', '栏目属性：0=内容栏目，1=外部链接', 'switch', 'tinyint(1)', '1', '0', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('34', 'is_hidden', '-99', '是否隐藏栏目：0=显示，1=隐藏', 'switch', 'tinyint(1)', '1', '0', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('35', 'sort_order', '-99', '排序号', 'int', 'int(10)', '10', '0', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('36', 'seo_description', '-99', 'seo描述', 'multitext', 'text', '0', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('37', 'seo_keywords', '-99', 'seo关键字', 'text', 'varchar(200)', '200', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('38', 'seo_title', '-99', 'SEO标题', 'text', 'varchar(200)', '200', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('39', 'tempview', '-99', '文档模板文件名', 'text', 'varchar(200)', '200', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('40', 'templist', '-99', '列表模板文件名', 'text', 'varchar(200)', '200', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('41', 'litpic', '-99', '栏目图片', 'img', 'varchar(250)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('42', 'typelink', '-99', '栏目链接', 'text', 'varchar(200)', '200', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('43', 'grade', '-99', '栏目等级', 'switch', 'tinyint(1)', '1', '0', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('44', 'englist_name', '-99', '栏目英文名', 'text', 'varchar(200)', '200', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('45', 'dirpath', '-99', '目录存放HTML路径', 'text', 'varchar(200)', '200', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('46', 'dirname', '-99', '目录英文名', 'text', 'varchar(200)', '200', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('47', 'typename', '-99', '栏目名称', 'text', 'varchar(200)', '200', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('48', 'parent_id', '-99', '栏目上级ID', 'int', 'int(10)', '10', '0', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('49', 'current_channel', '-99', '栏目当前模型ID', 'int', 'int(10)', '10', '0', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('50', 'channeltype', '-99', '栏目顶级模型ID', 'int', 'int(10)', '10', '0', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('51', 'id', '-99', '栏目ID', 'int', 'int(10)', '10', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1533524780', '1533524780');
INSERT INTO `ey_channelfield` VALUES ('52', 'del_method', '-99', '伪删除状态，1为主动删除，2为跟随上级栏目被动删除', 'switch', 'tinyint(1)', '1', '0', '', '', '1', '0', '1', '1', '1', '100', '1', '1547890773', '1547890773');
INSERT INTO `ey_channelfield` VALUES ('53', 'is_del', '0', '是否伪删除', 'switch', 'tinyint(1)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1547890773', '1547890773');
INSERT INTO `ey_channelfield` VALUES ('54', 'del_method', '0', '伪删除状态，1为主动删除，2为跟随上级栏目被动删除', 'switch', 'tinyint(1)', '250', '', '', '', '1', '0', '1', '1', '1', '100', '1', '1547890773', '1547890773');
INSERT INTO `ey_channelfield` VALUES ('55', 'prom_type', '0', '产品类型：0普通产品，1虚拟产品', 'switch', 'tinyint(1) unsigned', '1', '0', '', '', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` VALUES ('56', 'users_price', '0', '会员价', 'decimal', 'decimal(10,2)', '10', '0.00', '', '', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='栏目与自定义字段绑定表';


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
  `is_del` tinyint(1) DEFAULT '0' COMMENT '伪删除，1=是，0=否',
  `sort_order` smallint(6) DEFAULT '50' COMMENT '排序',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idention` (`nid`) USING BTREE,
  UNIQUE KEY `ctl_name` (`ctl_name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='系统模型表';

-- -----------------------------
-- Records of `ey_channeltype`
-- -----------------------------
INSERT INTO `ey_channeltype` VALUES ('1', 'article', '文章模型', '文章', 'article', 'Article', '1', '1', '1', '0', '1', '0', '1560004481');
INSERT INTO `ey_channeltype` VALUES ('4', 'download', '下载模型', '下载', 'download', 'Download', '1', '1', '1', '0', '4', '0', '1560004481');
INSERT INTO `ey_channeltype` VALUES ('2', 'product', '产品模型', '产品', 'product', 'Product', '1', '1', '1', '0', '2', '0', '1560004481');
INSERT INTO `ey_channeltype` VALUES ('8', 'guestbook', '留言模型', '留言', 'guestbook', 'Guestbook', '1', '1', '1', '0', '8', '1509197711', '1560004481');
INSERT INTO `ey_channeltype` VALUES ('6', 'single', '单页模型', '单页', 'single', 'Single', '1', '1', '1', '0', '6', '1523091961', '1560004481');
INSERT INTO `ey_channeltype` VALUES ('3', 'images', '图集模型', '图集', 'images', 'Images', '1', '1', '1', '0', '3', '1523929121', '1560004481');

-- -----------------------------
-- Table structure for `ey_common_pic`
-- -----------------------------
DROP TABLE IF EXISTS `ey_common_pic`;
CREATE TABLE `ey_common_pic` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '常用图片ID',
  `pic_path` varchar(255) NOT NULL DEFAULT '' COMMENT '图片地址',
  `lang` varchar(50) NOT NULL DEFAULT 'cn' COMMENT '多语言',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='常用图片';


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
) ENGINE=MyISAM AUTO_INCREMENT=195 DEFAULT CHARSET=utf8 COMMENT='系统配置表';

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
INSERT INTO `ey_config` VALUES ('11', 'file_size', '500', 'basic', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('12', 'image_type', 'jpg|gif|png|bmp|jpeg|ico', 'basic', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('13', 'file_type', 'zip|gz|rar|iso|doc|xsl|ppt|wps', 'basic', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('14', 'media_type', 'swf|mpg|mp3|rm|rmvb|wmv|wma|wav|mid|mov|mp4', 'basic', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('15', 'web_keywords', '', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('17', 'sms_platform', '1', 'sms', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('18', 'seo_viewtitle_format', '2', 'seo', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('24', 'mark_type', 'img', 'water', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('25', 'mark_txt_size', '30', 'water', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('26', 'mark_txt_color', '#000000', 'water', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('27', 'oss_switch', '0', 'oss', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('28', 'web_name', '易优Cms-演示站', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('29', 'web_logo', '/uploads/allimg/20190319/63dd9e50068fb7641aebbe905ff3ca33.png', 'web', '', 'cn', '0', '1552978946');
INSERT INTO `ey_config` VALUES ('30', 'web_ico', '/favicon.ico', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('31', 'web_basehost', 'http://127.0.0.15', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('32', 'web_description', '', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('79', 'web_recordnum', '琼ICP备xxxxxxxx号', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('33', 'web_copyright', 'Copyright © 2012-2018 EYOUCMS. 易优CMS 版权所有', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('34', 'web_thirdcode_pc', '', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('35', 'web_thirdcode_wap', '', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('39', 'seo_arcdir', '/html', 'seo', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('40', 'seo_pseudo', '1', 'seo', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('41', 'list_symbol', '&gt;', 'basic', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('42', 'sitemap_auto', '1', 'sitemap', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('43', 'sitemap_not1', '0', 'sitemap', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('44', 'sitemap_not2', '1', 'sitemap', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('45', 'sitemap_xml', '1', 'sitemap', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('46', 'sitemap_txt', '0', 'sitemap', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('47', 'sitemap_zzbaidutoken', '', 'sitemap', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('48', 'seo_expires_in', '7200', 'seo', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('55', 'web_title', '易优CMS -  Powered by Eyoucms.com', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('57', 'web_authortoken', '', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('58', 'web_attr_3', '123456789', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('59', 'web_attr_2', '8888-88888888', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('60', 'web_attr_1', 'http://www.weibo.com', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('61', 'web_attr_4', '/uploads/allimg/20190114/6a9bcb2f4cf5a66473fa8491f63481de.jpg', 'web', '', 'cn', '0', '1547463501');
INSERT INTO `ey_config` VALUES ('62', 'seo_inlet', '1', 'seo', '', 'cn', '0', '1553566003');
INSERT INTO `ey_config` VALUES ('63', 'web_cmspath', '', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('64', 'web_sqldatapath', '/data/sqldata', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('65', 'web_cmsurl', '', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('66', 'web_templets_dir', '/template', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('67', 'web_templeturl', '/template', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('68', 'web_templets_pc', '/template/pc', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('69', 'web_templets_m', '/template/mobile', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('70', 'web_eyoucms', 'http://www.eyoucms.com', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('78', '_cmscopyright', 'EvgtKmpb4fphxd6Ep7E9tD5K', 'php', '', 'cn', '0', '1554343222');
INSERT INTO `ey_config` VALUES ('76', 'seo_liststitle_format', '2', 'seo', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('77', 'web_status', '0', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('80', 'web_is_authortoken', '-1', 'web', '', 'cn', '0', '1557733846');
INSERT INTO `ey_config` VALUES ('81', 'web_adminbasefile', '/login.php', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('82', 'seo_rewrite_format', '1', 'seo', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('83', 'web_cmsmode', '2', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('84', 'web_htmlcache_expires_in', '0', 'web', '', 'cn', '0', '1546477337');
INSERT INTO `ey_config` VALUES ('85', 'web_show_popup_upgrade', '1', 'web', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('86', 'web_weapp_switch', '-1', 'web', '', 'cn', '0', '1553568761');
INSERT INTO `ey_config` VALUES ('88', 'seo_dynamic_format', '1', 'seo', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('89', 'system_sql_mode', 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION', 'system', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('90', 'system_home_default_lang', 'cn', 'system', '', 'cn', '0', '0');
INSERT INTO `ey_config` VALUES ('91', 'system_langnum', '2', 'system', '', 'cn', '0', '1545267532');
INSERT INTO `ey_config` VALUES ('93', 'is_mark', '0', 'water', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('94', 'mark_txt', '易优Cms', 'water', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('95', 'mark_img', '/uploads/allimg/20190114/f0d5e5830502125f5077212a90ef3a33.png', 'water', '', 'en', '0', '1547463466');
INSERT INTO `ey_config` VALUES ('96', 'mark_width', '200', 'water', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('97', 'mark_height', '50', 'water', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('98', 'mark_degree', '54', 'water', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('99', 'mark_quality', '56', 'water', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('100', 'mark_sel', '9', 'water', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('101', 'sms_time_out', '120', 'sms', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('102', 'theme_style', '1', 'basic', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('103', 'file_size', '500', 'basic', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('104', 'image_type', 'jpg|gif|png|bmp|jpeg|ico', 'basic', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('105', 'file_type', 'zip|gz|rar|iso|doc|xsl|ppt|wps', 'basic', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('106', 'media_type', 'swf|mpg|mp3|rm|rmvb|wmv|wma|wav|mid|mov|mp4', 'basic', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('107', 'web_keywords', '', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('108', 'sms_platform', '1', 'sms', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('109', 'seo_viewtitle_format', '2', 'seo', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('115', 'mark_type', 'img', 'water', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('116', 'mark_txt_size', '30', 'water', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('117', 'mark_txt_color', '#000000', 'water', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('118', 'oss_switch', '0', 'oss', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('119', 'web_name', 'en易优Cms-演示站', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('120', 'web_logo', '/uploads/allimg/20190114/ba52bdcbb075530e33454279df374aba.png', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('121', 'web_ico', '/favicon.ico', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('122', 'web_basehost', 'http://127.0.0.15', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('123', 'web_description', '', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('124', 'web_copyright', 'Copyright © 2012-2018 EYOUCMS. EYOU CMS All rights reserved', 'web', '', 'en', '0', '1545272727');
INSERT INTO `ey_config` VALUES ('125', 'web_thirdcode_pc', '', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('126', 'web_thirdcode_wap', '', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('127', 'seo_arcdir', '/html', 'seo', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('128', 'seo_pseudo', '1', 'seo', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('129', 'list_symbol', '&gt;', 'basic', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('130', 'sitemap_auto', '1', 'sitemap', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('131', 'sitemap_not1', '0', 'sitemap', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('132', 'sitemap_not2', '1', 'sitemap', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('133', 'sitemap_xml', '1', 'sitemap', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('134', 'sitemap_txt', '0', 'sitemap', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('135', 'sitemap_zzbaidutoken', '', 'sitemap', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('136', 'seo_expires_in', '7200', 'seo', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('137', 'web_title', '易优CMS -  Powered by Eyoucms.com', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('139', 'web_authortoken', '', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('140', 'web_attr_3', '123456789', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('141', 'web_attr_2', '8888-88888888', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('142', 'web_attr_1', 'http://www.weibo.com', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('143', 'web_attr_4', '/uploads/allimg/20190114/6a9bcb2f4cf5a66473fa8491f63481de.jpg', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('144', 'seo_inlet', '1', 'seo', '', 'en', '0', '1553566003');
INSERT INTO `ey_config` VALUES ('145', 'web_cmspath', '', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('146', 'web_sqldatapath', '/data/sqldata', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('147', 'web_cmsurl', '', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('148', 'web_templets_dir', '/template', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('149', 'web_templeturl', '/template', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('150', 'web_templets_pc', '/template/pc', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('151', 'web_templets_m', '/template/mobile', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('152', 'web_eyoucms', 'http://www.eyoucms.com', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('153', 'seo_liststitle_format', '2', 'seo', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('154', 'web_status', '0', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('155', '_cmscopyright', 's2n7uqbKpt3dTCoCq3zv1uhx', 'php', '', 'en', '0', '1554343222');
INSERT INTO `ey_config` VALUES ('156', 'web_recordnum', 'Qiong ICP Prepares xxxxxxx Number', 'web', '', 'en', '0', '1545272757');
INSERT INTO `ey_config` VALUES ('157', 'web_is_authortoken', '-1', 'web', '', 'en', '0', '1557733846');
INSERT INTO `ey_config` VALUES ('158', 'web_adminbasefile', '/login.php', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('159', 'seo_rewrite_format', '1', 'seo', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('160', 'web_cmsmode', '2', 'web', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('161', 'web_htmlcache_expires_in', '0', 'web', '', 'en', '0', '1546477338');
INSERT INTO `ey_config` VALUES ('162', 'web_show_popup_upgrade', '1', 'web', '', 'en', '0', '1552968816');
INSERT INTO `ey_config` VALUES ('163', 'web_weapp_switch', '-1', 'web', '', 'en', '0', '1553568761');
INSERT INTO `ey_config` VALUES ('164', 'seo_dynamic_format', '1', 'seo', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('165', 'system_sql_mode', 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION', 'system', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('166', 'system_home_default_lang', 'cn', 'system', '', 'en', '0', '0');
INSERT INTO `ey_config` VALUES ('167', 'system_langnum', '2', 'system', '', 'en', '0', '1545267532');
INSERT INTO `ey_config` VALUES ('169', 'basic_indexname', 'Home', 'basic', '', 'en', '0', '1545272633');
INSERT INTO `ey_config` VALUES ('170', 'web_exception', '0', 'web', '', 'cn', '0', '1546477337');
INSERT INTO `ey_config` VALUES ('171', 'web_language_switch', '1', 'web', '', 'cn', '0', '1546477337');
INSERT INTO `ey_config` VALUES ('172', 'web_exception', '0', 'web', '', 'en', '0', '1546477338');
INSERT INTO `ey_config` VALUES ('173', 'web_language_switch', '1', 'web', '', 'en', '0', '1546477338');
INSERT INTO `ey_config` VALUES ('174', 'web_is_https', '0', 'web', '', 'cn', '0', '1552968816');
INSERT INTO `ey_config` VALUES ('175', 'web_is_https', '0', 'web', '', 'en', '0', '1552968816');
INSERT INTO `ey_config` VALUES ('176', 'smtp_syn_weapp', '1', 'smtp', '', 'cn', '0', '1553566547');
INSERT INTO `ey_config` VALUES ('177', 'smtp_syn_weapp', '1', 'smtp', '', 'en', '0', '1553566547');
INSERT INTO `ey_config` VALUES ('178', 'php_eyou_blacklist', '', 'php', '', 'cn', '0', '1553654429');
INSERT INTO `ey_config` VALUES ('179', 'php_eyou_blacklist', '', 'php', '', 'en', '0', '1553654429');
INSERT INTO `ey_config` VALUES ('190', 'system_auth_code', '!*&^eyoucms<>|?', 'system', '', 'cn', '0', '1557462848');
INSERT INTO `ey_config` VALUES ('191', 'system_auth_code', '!*&^eyoucms<>|?', 'system', '', 'en', '0', '1557462848');
INSERT INTO `ey_config` VALUES ('192', 'system_upgrade_filelist', 'YXBwbGljYXRpb24vY29tbW9uL2NvbnRyb2xsZXIvQ29tbW9uLnBocDxicj5hcHBsaWNhdGlvbi9jb21tb24vbW9kZWwvQXJjdHlwZS5waHA8YnI+YXBwbGljYXRpb24vcm91dGUucGhwPGJyPmFwcGxpY2F0aW9uL3VzZXIvY29udHJvbGxlci9QYXkucGhwPGJyPmFwcGxpY2F0aW9uL3VzZXIvY29udHJvbGxlci9CYXNlLnBocDxicj5hcHBsaWNhdGlvbi91c2VyL2NvbnRyb2xsZXIvU2hvcC5waHA8YnI+YXBwbGljYXRpb24vdXNlci9jb250cm9sbGVyL1VzZXJzLnBocDxicj5hcHBsaWNhdGlvbi91c2VyL21vZGVsL1BheS5waHA8YnI+YXBwbGljYXRpb24vdXNlci9tb2RlbC9TaG9wLnBocDxicj5hcHBsaWNhdGlvbi91c2VyL3RlbXBsYXRlL3VwbG9hZGlmeS91cGxvYWQuaHRtPGJyPmFwcGxpY2F0aW9uL2NvbW1vbi5waHA8YnI+YXBwbGljYXRpb24vZnVuY3Rpb24ucGhwPGJyPmFwcGxpY2F0aW9uL2FwaS90ZW1wbGF0ZS91cGxvYWRpZnkvdXBsb2FkX3Byb2R1Y3QuaHRtPGJyPmFwcGxpY2F0aW9uL2FwaS90ZW1wbGF0ZS91cGxvYWRpZnkvdXBsb2FkLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi9jb21tb24ucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvRmlsZW1hbmFnZXIucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvQWRtaW4ucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvQXJjdHlwZS5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29udHJvbGxlci9Qcm9kdWN0LnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9jb250cm9sbGVyL1VwZ3JhZGUucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvU2VvLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9jb250cm9sbGVyL0FkUG9zaXRpb24ucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvSW5kZXgucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvQ2hhbm5lbHR5cGUucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbnRyb2xsZXIvTWVtYmVyLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9jb250cm9sbGVyL1N5c3RlbS5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29udHJvbGxlci9Ub29scy5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vY29udHJvbGxlci9TaG9wLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9sb2dpYy9GaWxlbWFuYWdlckxvZ2ljLnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9sb2dpYy9VcGdyYWRlTG9naWMucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2xvZ2ljL1Nob3BMb2dpYy5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vbG9naWMvTWVtYmVyTG9naWMucGhwPGJyPmFwcGxpY2F0aW9uL2FkbWluL2NvbmYvbWVudS5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vbW9kZWwvUGF5LnBocDxicj5hcHBsaWNhdGlvbi9hZG1pbi9tb2RlbC9BZC5waHA8YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvZmlsZW1hbmFnZXIvZWRpdC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvZmlsZW1hbmFnZXIvbmV3ZmlsZS5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvZmlsZW1hbmFnZXIvaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2FyY3R5cGUvZWRpdC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvYXJjdHlwZS9hZGQuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2FyY3R5cGUvaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2FyY3R5cGUvYWpheF9uZXd0cGwuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2ZpZWxkL2FyY3R5cGVfaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2ZpZWxkL2NoYW5uZWxfaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2luZGV4L2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9pbmRleC9zd2l0Y2hfbWFwLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9pbmRleC93ZWxjb21lLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9ndWVzdGJvb2svYXR0cmlidXRlX2VkaXQuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2d1ZXN0Ym9vay9hdHRyaWJ1dGVfYWRkLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9vdGhlci9iYXIuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3Nob3Avc2hpcHBpbmdfdGVtcGxhdGUuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3Nob3AvaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3Nob3Avb3JkZXJfZXhwcmVzcy5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvc2hvcC9zaG9wX2Jhci5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvc2hvcC9jb25mLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zaG9wL2Jhci5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvc2hvcC9vcmRlcl9zZW5kLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zaG9wL29yZGVyX2RldGFpbHMuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3Rvb2xzL2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS90b29scy9yZXN0b3JlLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9sYW5ndWFnZS9vZmZpY2lhbF9wYWNrX2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9sYW5ndWFnZS9pbmRleC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvbGFuZ3VhZ2UvY3VzdG9tdmFyX2JpbmQuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2xhbmd1YWdlL3BhY2tfaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3JlY3ljbGVfYmluL2FyY2hpdmVzX2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9yZWN5Y2xlX2Jpbi9jdXN0b212YXJfaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3JlY3ljbGVfYmluL2dib29rYXR0cl9pbmRleC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvcmVjeWNsZV9iaW4vYXJjdHlwZV9pbmRleC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvcmVjeWNsZV9iaW4vcHJvYXR0cl9pbmRleC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvc2VvL3Nlby5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvc2VvL2h0bWwuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3RhZ3MvaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2FkX3Bvc2l0aW9uL2VkaXQuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2FkX3Bvc2l0aW9uL2FkZC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvYWRfcG9zaXRpb24vaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2FkbWluL2FkbWluX3B3ZF9hamF4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9hZG1pbi9sb2dpbi5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvYWRtaW4vYWRtaW5fcHdkLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zeXN0ZW0vYmFzaWMuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3N5c3RlbS9zbXRwX3RwbF9lZGl0Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zeXN0ZW0vd2F0ZXIuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3N5c3RlbS9vc3MuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3N5c3RlbS9zbXRwLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zeXN0ZW0vY2xlYXJfY2FjaGUuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3N5c3RlbS93ZWIyLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zeXN0ZW0vc21zLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zeXN0ZW0vcmVnaW9uLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9zeXN0ZW0vdGh1bWIuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3N5c3RlbS93ZWIuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2FydGljbGUvZWRpdC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvYXJ0aWNsZS9hZGQuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2FydGljbGUvaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2FyY2hpdmVzL3JlbGVhc2UuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2FyY2hpdmVzL3JlbGVhc2VfaWZyYW1lLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9hcmNoaXZlcy9pbmRleF9hcmNoaXZlcy5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvYXJjaGl2ZXMvbW92ZS5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvcHJvZHVjdC9lZGl0Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9wcm9kdWN0L2FkZC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvcHJvZHVjdC9hdHRyaWJ1dGVfZWRpdC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvcHJvZHVjdC9pbmRleC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvcHJvZHVjdC9hamF4X2VkaXRfcHJvZHVjdGltZy5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvcHJvZHVjdC9hdHRyaWJ1dGVfYWRkLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9jaGFubmVsdHlwZS9pbmRleC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvbWVtYmVyL3VzZXJzX2NvbmZpZy5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvbWVtYmVyL2xldmVsX2VkaXQuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL21lbWJlci9tb25leV9lZGl0Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9tZW1iZXIvbGV2ZWxfYWRkLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9tZW1iZXIvYWpheF9zZXRfb2F1dGhfY29uZmlnLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9tZW1iZXIvcGF5X3NldC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvbWVtYmVyL2F0dHJfZWRpdC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvbWVtYmVyL2xldmVsX2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9tZW1iZXIvdXNlcnNfYmF0Y2hfYWRkLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9tZW1iZXIvdXNlcnNfaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL21lbWJlci9iYXIuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL21lbWJlci9tb25leV9pbmRleC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvbWVtYmVyL3VzZXJzX2VkaXQuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL21lbWJlci9hdHRyX2FkZC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvbWVtYmVyL2F0dHJfaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL21lbWJlci91c2Vyc19hZGQuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3dlYXBwL2luZGV4Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS93ZWFwcC9wYWNrLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS91cGxvYWRpZnkvdXBsb2FkX3Byb2R1Y3QuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3VwbG9hZGlmeS91cGxvYWQuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL3VwbG9hZGlmeS91cGxvYWRfZnVsbC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvdXBsb2FkaWZ5L3VwbG9hZF9mcmFtZS5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvZG93bmxvYWQvZWRpdC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvZG93bmxvYWQvYWRkLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9kb3dubG9hZC9pbmRleC5odG08YnI+YXBwbGljYXRpb24vYWRtaW4vdGVtcGxhdGUvcHVibGljL2xlZnQuaHRtPGJyPmFwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2ltYWdlcy9lZGl0Lmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9pbWFnZXMvYWRkLmh0bTxicj5hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9pbWFnZXMvaW5kZXguaHRtPGJyPmFwcGxpY2F0aW9uL2V4dHJhL2Vycm9yX2NvZGUucGhwPGJyPmFwcGxpY2F0aW9uL2V4dHJhL2dsb2JhbC5waHA8YnI+YXBwbGljYXRpb24vaG9tZS9jb21tb24ucGhwPGJyPmFwcGxpY2F0aW9uL2hvbWUvY29udHJvbGxlci9MaXN0cy5waHA8YnI+YXBwbGljYXRpb24vaG9tZS9jb250cm9sbGVyL1ZpZXcucGhwPGJyPmFwcGxpY2F0aW9uL2hvbWUvbG9naWMvRmllbGRMb2dpYy5waHA8YnI+YXBwbGljYXRpb24vY29uZmlnLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvcGFnaW5hdG9yL2RyaXZlci9Vc2Vyc2V5b3UucGhwPGJyPmNvcmUvbGlicmFyeS90aGluay9wYWdpbmF0b3IvZHJpdmVyL1VzZXJzbW9iaWxlLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvYmVoYXZpb3IvdXNlci90YWdzLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvYmVoYXZpb3IvdXNlci9BY3Rpb25CZWdpbkJlaGF2aW9yLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvYmVoYXZpb3IvYWRtaW4vdGFncy5waHA8YnI+Y29yZS9saWJyYXJ5L3RoaW5rL2JlaGF2aW9yL2FkbWluL0FjdGlvbkJlZ2luQmVoYXZpb3IucGhwPGJyPmNvcmUvbGlicmFyeS90aGluay9UZW1wbGF0ZS5waHA8YnI+Y29yZS9saWJyYXJ5L3RoaW5rL3ZpZXcvZHJpdmVyL1RoaW5rLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2V5b3UvVGFnQXJjdmlldy5waHA8YnI+Y29yZS9saWJyYXJ5L3RoaW5rL3RlbXBsYXRlL3RhZ2xpYi9leW91L1RhZ1NwcHVyY2hhc2UucGhwPGJyPmNvcmUvbGlicmFyeS90aGluay90ZW1wbGF0ZS90YWdsaWIvZXlvdS9UYWdTcGNhcnQucGhwPGJyPmNvcmUvbGlicmFyeS90aGluay90ZW1wbGF0ZS90YWdsaWIvZXlvdS9UYWdTcHN1Ym1pdG9yZGVyLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2V5b3UvVGFnU3BvcmRlci5waHA8YnI+Y29yZS9saWJyYXJ5L3RoaW5rL3RlbXBsYXRlL3RhZ2xpYi9leW91L1RhZ1Nwc2VhcmNoLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2V5b3UvVGFnR2xvYmFsLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2V5b3UvVGFnU3BvcmRlcmxpc3QucGhwPGJyPmNvcmUvbGlicmFyeS90aGluay90ZW1wbGF0ZS90YWdsaWIvZXlvdS9UYWdDaGFubmVsLnBocDxicj5jb3JlL2xpYnJhcnkvdGhpbmsvdGVtcGxhdGUvdGFnbGliL2V5b3UvVGFnVXNlcm1lbnUucGhwPGJyPmNvcmUvbGlicmFyeS90aGluay90ZW1wbGF0ZS90YWdsaWIvZXlvdS9UYWdTcGFkZHJlc3MucGhwPGJyPmNvcmUvbGlicmFyeS90aGluay90ZW1wbGF0ZS90YWdsaWIvZXlvdS9UYWdTdGF0aWMucGhwPGJyPmNvcmUvbGlicmFyeS90aGluay90ZW1wbGF0ZS90YWdsaWIvRXlvdS5waHA8YnI+ZGF0YS9iYWNrdXAvdHBsL3RlbXBsYXRlL3BjL3VzZXJzL3BheV9yZWNoYXJnZV9kZXRhaWwuaHRtPGJyPmRhdGEvYmFja3VwL3RwbC90ZW1wbGF0ZS9wYy91c2Vycy9wYXlfc3VjY2Vzcy5odG08YnI+ZGF0YS9iYWNrdXAvdHBsL3RlbXBsYXRlL3BjL3VzZXJzL3BheV9jb25zdW1lcl9kZXRhaWxzLmh0bTxicj5kYXRhL2JhY2t1cC90cGwvdGVtcGxhdGUvcGMvdXNlcnMvdXNlcnNfY2VudHJlLmh0bTxicj5kYXRhL2JhY2t1cC90cGwvdGVtcGxhdGUvcGMvdXNlcnMvc2tpbi9jc3MvZGl5X2Nzcy5odG08YnI+ZGF0YS9iYWNrdXAvdHBsL3RlbXBsYXRlL21vYmlsZS91c2Vycy9wYXlfcmVjaGFyZ2VfZGV0YWlsLmh0bTxicj5kYXRhL2JhY2t1cC90cGwvdGVtcGxhdGUvbW9iaWxlL3VzZXJzL3BheV9zdWNjZXNzLmh0bTxicj5kYXRhL2JhY2t1cC90cGwvdGVtcGxhdGUvbW9iaWxlL3VzZXJzL3BheV9jb25zdW1lcl9kZXRhaWxzLmh0bTxicj5kYXRhL2JhY2t1cC90cGwvdGVtcGxhdGUvbW9iaWxlL3VzZXJzL3VzZXJzX2NlbnRyZS5odG08YnI+ZGF0YS9iYWNrdXAvdHBsL3RlbXBsYXRlL21vYmlsZS91c2Vycy9za2luL2Nzcy9kaXlfY3NzLmh0bTxicj5kYXRhL3NjaGVtYS9leV9hcmNoaXZlcy5waHA8YnI+ZGF0YS9zY2hlbWEvZXlfc2hvcF9vcmRlci5waHA8YnI+ZGF0YS9zY2hlbWEvZXlfc2hvcF9jYXJ0LnBocDxicj5kYXRhL3NjaGVtYS9leV9zaG9wX29yZGVyX2xvZy5waHA8YnI+ZGF0YS9zY2hlbWEvZXlfcmVnaW9uLnBocDxicj5kYXRhL3NjaGVtYS9leV9zaG9wX3NoaXBwaW5nX3RlbXBsYXRlLnBocDxicj5kYXRhL3NjaGVtYS9leV9zaG9wX29yZGVyX2RldGFpbHMucGhwPGJyPmRhdGEvc2NoZW1hL2V5X3Nob3BfYWRkcmVzcy5waHA8YnI+ZGF0YS9zY2hlbWEvZXlfc2hvcF9leHByZXNzLnBocDxicj5kYXRhL2NvbmYvdmVyc2lvbi50eHQ8YnI+ZGF0YS9tb2RlbC9hcHBsaWNhdGlvbi9hZG1pbi90ZW1wbGF0ZS9jdXN0b21tb2RlbC9lZGl0Lmh0bTxicj5kYXRhL21vZGVsL2FwcGxpY2F0aW9uL2FkbWluL3RlbXBsYXRlL2N1c3RvbW1vZGVsL2FkZC5odG08YnI+ZGF0YS9tb2RlbC90ZW1wbGF0ZS9wYy9saXN0c19jdXN0b21tb2RlbC5odG08YnI+ZGF0YS9tb2RlbC90ZW1wbGF0ZS9wYy92aWV3X2N1c3RvbW1vZGVsLmh0bTxicj5wdWJsaWMvcGx1Z2lucy93ZWJ1cGxvYWRlci91cGxvYWQuanM8YnI+cHVibGljL3BsdWdpbnMvd2VidXBsb2FkZXIvaW1hZ2VzL25vcGljLnBuZzxicj5wdWJsaWMvc3RhdGljL2NvbW1vbi9jc3Mvc2hvcGNhcnQuY3NzPGJyPnB1YmxpYy9zdGF0aWMvY29tbW9uL2pzL3RhZ19zcGNhcnQuanM8YnI+cHVibGljL3N0YXRpYy9jb21tb24vanMvc2hvcF9hZGRfYWRkci5qczxicj5wdWJsaWMvc3RhdGljL2NvbW1vbi9qcy9zaG9wX2VkaXRfYWRkci5qczxicj5wdWJsaWMvc3RhdGljL2NvbW1vbi9qcy90YWdfc3BvcmRlcmxpc3QuanM8YnI+cHVibGljL3N0YXRpYy9jb21tb24vanMvdGFnX3NwcHVyY2hhc2UuanM8YnI+cHVibGljL3N0YXRpYy9jb21tb24vanMvdGFnX2dsb2JhbC5qczxicj5wdWJsaWMvc3RhdGljL2NvbW1vbi9qcy90YWdfc3BhZGRyZXNzLmpzPGJyPnB1YmxpYy9zdGF0aWMvY29tbW9uL2pzL3RhZ19zcHN1Ym1pdG9yZGVyLmpzPGJyPnB1YmxpYy9zdGF0aWMvY29tbW9uL2pzL3RhZ19zcG9yZGVyLmpzPGJyPnB1YmxpYy9zdGF0aWMvYWRtaW4vY3NzL21haW4uY3NzPGJyPnB1YmxpYy9zdGF0aWMvYWRtaW4vanMvZ2xvYmFsLmpzPGJyPnB1YmxpYy9zdGF0aWMvYWRtaW4vanMvdXNlcnNfdXBncmFkZS5qczxicj5wdWJsaWMvc3RhdGljL2FkbWluL2pzL215Rm9ybVZhbGlkYXRlLmpzPGJyPnB1YmxpYy9zdGF0aWMvYWRtaW4vanMvdXBncmFkZS5qczxicj5wdWJsaWMvc3RhdGljL3RlbXBsYXRlL3VzZXJzL3BheV9yZWNoYXJnZV90eXBlX20uaHRtPGJyPnB1YmxpYy9zdGF0aWMvdGVtcGxhdGUvdXNlcnMvdXNlcnNfbGVmdG1lbnUuaHRtPGJyPnB1YmxpYy9zdGF0aWMvdGVtcGxhdGUvdXNlcnMvdXNlcnNfbGVmdG1lbnVfbS5odG08YnI+cHVibGljL3N0YXRpYy90ZW1wbGF0ZS91c2Vycy9wYXlfcmVjaGFyZ2VfdHlwZS5odG08YnI+dGVtcGxhdGUvLmh0YWNjZXNz', 'system', '', 'cn', '0', '1557462850');
INSERT INTO `ey_config` VALUES ('193', 'system_version', 'v1.3.6', 'system', '', 'cn', '0', '1557462881');
INSERT INTO `ey_config` VALUES ('194', 'system_version', 'v1.3.6', 'system', '', 'en', '0', '1557462881');

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
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='自定义变量表';

-- -----------------------------
-- Records of `ey_config_attribute`
-- -----------------------------
INSERT INTO `ey_config_attribute` VALUES ('1', 'web', '微博地址', 'web_attr_1', '0', 'cn', '1525962574', '1526008818');
INSERT INTO `ey_config_attribute` VALUES ('2', 'web', '手机/固话', 'web_attr_2', '0', 'cn', '1525962600', '1525962600');
INSERT INTO `ey_config_attribute` VALUES ('3', 'web', 'QQ号码', 'web_attr_3', '0', 'cn', '1525962624', '1525962624');
INSERT INTO `ey_config_attribute` VALUES ('4', 'web', '微信二维码', 'web_attr_4', '3', 'cn', '1525999090', '1526008783');
INSERT INTO `ey_config_attribute` VALUES ('5', 'web', '微博地址', 'web_attr_1', '0', 'en', '1525962574', '1526008818');
INSERT INTO `ey_config_attribute` VALUES ('6', 'web', '手机/固话', 'web_attr_2', '0', 'en', '1525962600', '1525962600');
INSERT INTO `ey_config_attribute` VALUES ('7', 'web', 'QQ号码', 'web_attr_3', '0', 'en', '1525962624', '1525962624');
INSERT INTO `ey_config_attribute` VALUES ('8', 'web', '微信二维码', 'web_attr_4', '3', 'en', '1525999090', '1526008783');

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
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='下载附加表';

-- -----------------------------
-- Records of `ey_download_content`
-- -----------------------------
INSERT INTO `ey_download_content` VALUES ('1', '30', '&lt;p&gt;工程机械推土挖掘机类网站模板，下载地址：&lt;a href=&quot;http://www.eyoucms.com/moban/16/668.html&quot; target=&quot;_self&quot;&gt;http://www.eyoucms.com/moban/16/668.html&lt;/a&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;/uploads/allimg/20190114/4873105f54a14f3785047bd8ecc8b5ac.jpg&quot;/&gt;&lt;/p&gt;', '1547463959', '1547463959');
INSERT INTO `ey_download_content` VALUES ('2', '31', '&lt;p&gt;职业教育培训机构网站模板，下载地址：&lt;a href=&quot;http://www.eyoucms.com/moban/10/673.html&quot; target=&quot;_self&quot;&gt;http://www.eyoucms.com/moban/10/673.html&lt;/a&gt;&lt;/p&gt;', '1547463404', '1547463404');
INSERT INTO `ey_download_content` VALUES ('4', '48', '&lt;p&gt;Construction machinery bulldozer website template, download address：&lt;a href=&quot;http://www.eyoucms.com/moban/16/668.html&quot; target=&quot;_self&quot;&gt;http://www.eyoucms.com/moban/16/668.html&lt;/a&gt;&lt;/p&gt;&lt;p&gt;&lt;br style=&quot;box-sizing: inherit;&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;http://www.eyoucms.com/uploads/allimg/180426/150RQ155-0.jpg&quot; style=&quot;box-sizing: inherit; border-style: none; vertical-align: top; max-width: 100%; height: auto !important;&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1547463855', '1547463855');
INSERT INTO `ey_download_content` VALUES ('5', '49', '&lt;p&gt;Web template for vocational education and training institutions, download address: &lt;a href=&quot;http://www.eyoucms.com/moban/10/673.html&quot; target=&quot;_self&quot;&gt;http://www.eyoucms.com/moban/10/673.html&lt;/a&gt;&lt;/p&gt;', '1545268305', '1545268305');

-- -----------------------------
-- Table structure for `ey_download_file`
-- -----------------------------
DROP TABLE IF EXISTS `ey_download_file`;
CREATE TABLE `ey_download_file` (
  `file_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `aid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '产品ID',
  `title` varchar(200) DEFAULT '' COMMENT '产品标题',
  `file_url` varchar(255) DEFAULT '' COMMENT '文件存储路径',
  `file_size` varchar(255) DEFAULT '' COMMENT '文件大小',
  `file_ext` varchar(50) DEFAULT '' COMMENT '文件后缀名',
  `file_name` varchar(200) DEFAULT '' COMMENT '文件名',
  `file_mime` varchar(200) DEFAULT '' COMMENT '文件类型',
  `uhash` varchar(200) DEFAULT '' COMMENT '自定义的一种加密方式，用于文件下载权限验证',
  `md5file` varchar(200) DEFAULT '' COMMENT 'md5_file加密，可以检测上传/下载的文件包是否损坏',
  `sort_order` smallint(5) DEFAULT '0' COMMENT '排序',
  `add_time` int(10) unsigned DEFAULT '0' COMMENT '上传时间',
  PRIMARY KEY (`file_id`),
  KEY `arcid` (`aid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='下载附件表';

-- -----------------------------
-- Records of `ey_download_file`
-- -----------------------------
INSERT INTO `ey_download_file` VALUES ('15', '30', '工程机械推土挖掘机类网站模板', '/uploads/soft/20190114/4b0f01441b5a246badf158fa99c140ac.zip', '9268', 'zip', '4b0f01441b5a246badf158fa99c140ac.zip', 'application/x-zip-compressed', '1837c4067aa99f7005e62b20bdb1a67f', '1837c4067aa99f7005e62b20bdb1a67f', '1', '1547463434');
INSERT INTO `ey_download_file` VALUES ('13', '31', '职业教育培训机构网站模板', '/uploads/soft/20190114/3b3f753af0f13e6e0237b9577e0bcd17.zip', '9268', 'zip', '3b3f753af0f13e6e0237b9577e0bcd17.zip', 'application/x-zip-compressed', '1837c4067aa99f7005e62b20bdb1a67f', '1837c4067aa99f7005e62b20bdb1a67f', '1', '1547463404');
INSERT INTO `ey_download_file` VALUES ('14', '31', '职业教育培训机构网站模板', '/uploads/soft/20190114/44bbd259222c81bd3c41112a73c904a0.zip', '9268', 'zip', '44bbd259222c81bd3c41112a73c904a0.zip', 'application/x-zip-compressed', '1837c4067aa99f7005e62b20bdb1a67f', '1837c4067aa99f7005e62b20bdb1a67f', '2', '1547463404');
INSERT INTO `ey_download_file` VALUES ('10', '48', '工程机械推土挖掘机类网站模板', '/uploads/soft/20181220/4b0f01441b5a246badf158fa99c140ac.zip', '9268', 'zip', '4b0f01441b5a246badf158fa99c140ac.zip', 'application/x-zip-compressed', '1837c4067aa99f7005e62b20bdb1a67f', '1837c4067aa99f7005e62b20bdb1a67f', '1', '1545268147');
INSERT INTO `ey_download_file` VALUES ('11', '49', 'Website Template of Vocational Education and Training Institutions', '/uploads/soft/20181220/44bbd259222c81bd3c41112a73c904a0.zip', '9268', 'zip', '44bbd259222c81bd3c41112a73c904a0.zip', 'application/x-zip-compressed', '1837c4067aa99f7005e62b20bdb1a67f', '1837c4067aa99f7005e62b20bdb1a67f', '1', '1545268233');
INSERT INTO `ey_download_file` VALUES ('12', '49', 'Website Template of Vocational Education and Training Institutions', '/uploads/soft/20181220/3b3f753af0f13e6e0237b9577e0bcd17.zip', '9268', 'zip', '3b3f753af0f13e6e0237b9577e0bcd17.zip', 'application/x-zip-compressed', '1837c4067aa99f7005e62b20bdb1a67f', '1837c4067aa99f7005e62b20bdb1a67f', '2', '1545268233');

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
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='留言主表';


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='留言表单属性值';


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
  `sort_order` int(11) unsigned DEFAULT '0' COMMENT '表单排序',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否已删除，0=否，1=是',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`attr_id`),
  KEY `guest_id` (`typeid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='留言表单属性';

-- -----------------------------
-- Records of `ey_guestbook_attribute`
-- -----------------------------
INSERT INTO `ey_guestbook_attribute` VALUES ('1', '姓名', '30', '0', '', '100', 'cn', '0', '1526616441', '1526616441');
INSERT INTO `ey_guestbook_attribute` VALUES ('2', '手机号码', '30', '0', '', '100', 'cn', '0', '1526616453', '1526616453');
INSERT INTO `ey_guestbook_attribute` VALUES ('3', '约谈对象', '30', '1', '隔壁老王\r\n前台美女\r\n扫地阿姨', '100', 'cn', '0', '1526616497', '1526616812');
INSERT INTO `ey_guestbook_attribute` VALUES ('4', '姓名', '6', '0', '', '100', 'cn', '0', '1526634369', '1526874914');
INSERT INTO `ey_guestbook_attribute` VALUES ('5', '联系方式', '6', '0', '', '100', 'cn', '0', '1526634383', '1526634383');
INSERT INTO `ey_guestbook_attribute` VALUES ('6', '备注信息', '6', '2', '', '100', 'cn', '0', '1526634393', '1526875056');
INSERT INTO `ey_guestbook_attribute` VALUES ('7', '模式', '6', '1', '个人\r\n团队', '100', 'cn', '0', '1526875483', '1526876172');
INSERT INTO `ey_guestbook_attribute` VALUES ('9', 'Name', '34', '0', '', '100', 'en', '0', '1526616441', '1545272376');
INSERT INTO `ey_guestbook_attribute` VALUES ('10', 'Telephone', '34', '0', '', '100', 'en', '0', '1526616453', '1545272410');
INSERT INTO `ey_guestbook_attribute` VALUES ('11', 'Object of negotiation', '34', '1', 'Old king next door\r\nFront desk beauty\r\nAunt sweep', '100', 'en', '0', '1526616497', '1545272446');
INSERT INTO `ey_guestbook_attribute` VALUES ('12', 'Name', '53', '0', '', '100', 'en', '0', '1526634369', '1545267932');
INSERT INTO `ey_guestbook_attribute` VALUES ('13', 'Tel', '53', '0', '', '100', 'en', '0', '1526634383', '1545267998');
INSERT INTO `ey_guestbook_attribute` VALUES ('14', 'Remarks', '53', '2', '', '100', 'en', '0', '1526634393', '1545268014');
INSERT INTO `ey_guestbook_attribute` VALUES ('15', 'Pattern', '53', '1', 'personal\r\nteam', '100', 'en', '0', '1526875483', '1545272477');

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
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='图集附加表';

-- -----------------------------
-- Records of `ey_images_content`
-- -----------------------------
INSERT INTO `ey_images_content` VALUES ('5', '22', '&lt;p&gt;新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集新闻模型下的图集&lt;/p&gt;', '1547462752', '1547462752');
INSERT INTO `ey_images_content` VALUES ('6', '23', '&lt;p&gt;新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二新闻模型下的图集二&lt;/p&gt;', '1547462702', '1547462702');
INSERT INTO `ey_images_content` VALUES ('7', '42', '', '1547462847', '1547462847');
INSERT INTO `ey_images_content` VALUES ('8', '43', '', '1547462829', '1547462829');
INSERT INTO `ey_images_content` VALUES ('9', '44', '', '1547462806', '1547462806');
INSERT INTO `ey_images_content` VALUES ('10', '50', '', '1545268466', '1545268466');
INSERT INTO `ey_images_content` VALUES ('11', '51', '', '1545268514', '1545268514');
INSERT INTO `ey_images_content` VALUES ('12', '52', '', '1545268563', '1545268563');
INSERT INTO `ey_images_content` VALUES ('13', '57', '', '1545270783', '1545270783');
INSERT INTO `ey_images_content` VALUES ('14', '58', '', '1545270816', '1545270816');

-- -----------------------------
-- Table structure for `ey_images_upload`
-- -----------------------------
DROP TABLE IF EXISTS `ey_images_upload`;
CREATE TABLE `ey_images_upload` (
  `img_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `aid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '图集ID',
  `title` varchar(200) DEFAULT '' COMMENT '产品标题',
  `image_url` varchar(255) DEFAULT '' COMMENT '文件存储路径',
  `width` int(11) DEFAULT '0' COMMENT '图片宽度',
  `height` int(11) DEFAULT '0' COMMENT '图片高度',
  `filesize` mediumint(8) unsigned DEFAULT '0' COMMENT '文件大小',
  `mime` varchar(50) DEFAULT '' COMMENT '图片类型',
  `sort_order` smallint(5) DEFAULT '0' COMMENT '排序',
  `add_time` int(10) unsigned DEFAULT '0' COMMENT '上传时间',
  PRIMARY KEY (`img_id`),
  KEY `arcid` (`aid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 COMMENT='图集图片表';

-- -----------------------------
-- Records of `ey_images_upload`
-- -----------------------------
INSERT INTO `ey_images_upload` VALUES ('32', '22', '新闻模型下的图集', '/uploads/allimg/20190114/84cb8dc1626776b7c8a54a78f0177f48.jpg', '400', '400', '0', 'image/jpeg', '2', '1547462752');
INSERT INTO `ey_images_upload` VALUES ('31', '22', '新闻模型下的图集', '/uploads/allimg/20190114/546f502c79cf1b653816006be5d9e3fd.jpg', '560', '560', '0', 'image/jpeg', '1', '1547462752');
INSERT INTO `ey_images_upload` VALUES ('30', '23', '新闻模型下的图集二', '/uploads/allimg/20190114/f7a54389683dc21843aed505561de39a.jpg', '560', '560', '0', 'image/jpeg', '1', '1547462702');
INSERT INTO `ey_images_upload` VALUES ('38', '42', '客户案例一', '/uploads/allimg/20190114/9764f13943d3aca2f7d3b1065d143a7d.jpg', '1000', '782', '0', 'image/jpeg', '1', '1547462847');
INSERT INTO `ey_images_upload` VALUES ('37', '43', '客户案例二', '/uploads/allimg/20190114/f8373e1b08853b8bec3f0198bd83d671.jpg', '1000', '782', '0', 'image/jpeg', '2', '1547462829');
INSERT INTO `ey_images_upload` VALUES ('36', '43', '客户案例二', '/uploads/allimg/20190114/d26baa8e6d8069091afbe90c8cdf88b3.jpg', '1000', '782', '0', 'image/jpeg', '1', '1547462829');
INSERT INTO `ey_images_upload` VALUES ('35', '44', '客户案例三', '/uploads/allimg/20190114/f588a2247388733dfbf4ff54d9234db5.jpg', '1000', '782', '0', 'image/jpeg', '3', '1547462806');
INSERT INTO `ey_images_upload` VALUES ('34', '44', '客户案例三', '/uploads/allimg/20190114/34914446f0730fd1d15130fc92b9d4d1.jpg', '1000', '782', '0', 'image/jpeg', '2', '1547462806');
INSERT INTO `ey_images_upload` VALUES ('33', '44', '客户案例三', '/uploads/allimg/20190114/21b287f5e527170a57e51647b109b02b.jpg', '1000', '782', '0', 'image/jpeg', '1', '1547462806');
INSERT INTO `ey_images_upload` VALUES ('24', '50', 'Customer Case I', '/uploads/allimg/20181220/9d1ad4b6be1ecc79929e3e55cb671a14.jpg', '1000', '782', '0', 'image/jpeg', '1', '1545268466');
INSERT INTO `ey_images_upload` VALUES ('25', '51', 'Customer Case II', '/uploads/allimg/20181220/e33caf1e51fcefac340c351769a9928c.jpg', '1000', '782', '0', 'image/jpeg', '1', '1545268514');
INSERT INTO `ey_images_upload` VALUES ('26', '52', 'Customer Case III', '/uploads/allimg/20181220/5ae19f75c0465f48f00719a0f15d2f02.jpg', '1000', '782', '0', 'image/jpeg', '1', '1545268563');
INSERT INTO `ey_images_upload` VALUES ('27', '57', 'Atlas under News Model', '/uploads/allimg/20181220/b1a53d661f80d231d794ba8e7f9154ba.jpg', '560', '560', '0', 'image/jpeg', '1', '1545270783');
INSERT INTO `ey_images_upload` VALUES ('28', '57', 'Atlas under News Model', '/uploads/allimg/20181220/a85e92ce6e2561d5c93a4cf62b7a8201.jpg', '400', '400', '0', 'image/jpeg', '2', '1545270783');
INSERT INTO `ey_images_upload` VALUES ('29', '58', 'Atlas II under News Model', '/uploads/allimg/20181220/c76f2be2ebee26b8d8b2ebcc015280ef.jpg', '560', '560', '0', 'image/jpeg', '1', '1545270816');

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
INSERT INTO `ey_language` VALUES ('2', 'English', 'en', '', '0', '0', '0', '24', '1', '100', '1545267531', '1545267534');

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
) ENGINE=MyISAM AUTO_INCREMENT=85 DEFAULT CHARSET=utf8 COMMENT='多语言模板变量关联绑定表';

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
INSERT INTO `ey_language_attr` VALUES ('42', 'tid1', '31', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('43', 'tid8', '32', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('44', 'tid9', '33', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('45', 'tid30', '34', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('46', 'tid2', '35', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('47', 'tid10', '36', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('48', 'tid11', '37', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('49', 'tid12', '38', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('50', 'tid13', '39', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('51', 'tid23', '40', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('52', 'tid3', '41', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('53', 'tid20', '42', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('54', 'tid24', '43', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('55', 'tid25', '44', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('56', 'tid21', '45', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('57', 'tid26', '46', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('58', 'tid22', '47', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('59', 'tid27', '48', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('60', 'tid28', '49', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('61', 'tid29', '50', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('62', 'tid4', '51', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('63', 'tid5', '52', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('64', 'tid6', '53', 'arctype', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('65', 'attr_1', '11', 'product_attribute', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('66', 'attr_2', '12', 'product_attribute', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('67', 'attr_3', '13', 'product_attribute', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('68', 'attr_4', '14', 'product_attribute', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('69', 'attr_5', '15', 'product_attribute', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('70', 'attr_6', '16', 'product_attribute', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('71', 'attr_7', '17', 'product_attribute', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('72', 'attr_8', '18', 'product_attribute', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('73', 'attr_1', '9', 'guestbook_attribute', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('74', 'attr_2', '10', 'guestbook_attribute', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('75', 'attr_3', '11', 'guestbook_attribute', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('76', 'attr_4', '12', 'guestbook_attribute', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('77', 'attr_5', '13', 'guestbook_attribute', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('78', 'attr_6', '14', 'guestbook_attribute', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('79', 'attr_7', '15', 'guestbook_attribute', 'en', '1545267531', '1545267531');
INSERT INTO `ey_language_attr` VALUES ('80', 'adp1', '2', 'ad_position', 'en', '1545267532', '1545267532');
INSERT INTO `ey_language_attr` VALUES ('81', 'ad1', '3', 'ad', 'en', '1545267532', '1545267532');
INSERT INTO `ey_language_attr` VALUES ('82', 'ad2', '4', 'ad', 'en', '1545267532', '1545267532');
INSERT INTO `ey_language_attr` VALUES ('83', 'ad5', '5', 'ad', 'cn', '1553046945', '1553046945');
INSERT INTO `ey_language_attr` VALUES ('84', 'ad5', '6', 'ad', 'en', '1553046945', '1553046945');

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
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COMMENT='多语言模板变量表';

-- -----------------------------
-- Records of `ey_language_attribute`
-- -----------------------------
INSERT INTO `ey_language_attribute` VALUES ('1', '关于我们', 'tid1', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('2', '新闻动态', 'tid2', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('3', '产品展示', 'tid3', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('4', '客户案例', 'tid4', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('5', '资料下载', 'tid5', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('6', '报名入口', 'tid6', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('7', '公司简介', 'tid8', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('8', '公司荣誉', 'tid9', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('9', '媒体报道', 'tid10', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('10', 'SEO优化', 'tid11', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('11', '企业运营', 'tid12', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('12', '单页面', 'tid13', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('13', '手机', 'tid20', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('14', '电脑', 'tid21', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('15', '通用配件', 'tid22', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('16', '风景图集', 'tid23', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('17', '智能手机', 'tid24', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('18', '畅玩手机', 'tid25', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('19', '笔记本电脑', 'tid26', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('20', '耳机', 'tid27', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('21', '音箱', 'tid28', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('22', '充电宝', 'tid29', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('23', '预约面试', 'tid30', 'arctype', '0', '1545267517', '1545267517');
INSERT INTO `ey_language_attribute` VALUES ('24', '姓名', 'attr_1', 'guestbook_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('25', '手机号码', 'attr_2', 'guestbook_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('26', '约谈对象', 'attr_3', 'guestbook_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('27', '姓名', 'attr_4', 'guestbook_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('28', '联系方式', 'attr_5', 'guestbook_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('29', '备注信息', 'attr_6', 'guestbook_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('30', '模式', 'attr_7', 'guestbook_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('31', '用户界面', 'attr_1', 'product_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('32', '操作系统', 'attr_2', 'product_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('33', '键盘类型', 'attr_3', 'product_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('34', ' 型号', 'attr_4', 'product_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('35', '屏幕大小', 'attr_5', 'product_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('36', '重量', 'attr_6', 'product_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('37', '型号', 'attr_7', 'product_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('38', '支持蓝牙', 'attr_8', 'product_attribute', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('39', '共展蓝图', 'ad1', 'ad', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('40', '易优模板库', 'ad2', 'ad', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('41', '首页-大幻灯片', 'adp1', 'ad_position', '0', '1545267518', '1545267518');
INSERT INTO `ey_language_attribute` VALUES ('42', '第三组广告', 'ad5', 'ad', '0', '1553046945', '1553046945');

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
INSERT INTO `ey_language_pack` VALUES ('13', 'sys1', 'Home', '1', 'en', '100', '1545267534', '1545267534');
INSERT INTO `ey_language_pack` VALUES ('14', 'sys2', 'Previous', '1', 'en', '100', '1545267534', '1545267534');
INSERT INTO `ey_language_pack` VALUES ('15', 'sys3', 'Next', '1', 'en', '100', '1545267534', '1545267534');
INSERT INTO `ey_language_pack` VALUES ('16', 'sys4', 'Last', '1', 'en', '100', '1545267534', '1545267534');
INSERT INTO `ey_language_pack` VALUES ('17', 'sys5', 'Road <strong>%s</strong> page <strong>%s</strong> strip', '1', 'en', '100', '1545267534', '1545267534');
INSERT INTO `ey_language_pack` VALUES ('18', 'sys6', 'All', '1', 'en', '100', '1545267534', '1545267534');
INSERT INTO `ey_language_pack` VALUES ('19', 'sys7', 'Search', '1', 'en', '100', '1545267534', '1545267534');
INSERT INTO `ey_language_pack` VALUES ('20', 'sys8', 'View details', '1', 'en', '100', '1545267534', '1545267534');
INSERT INTO `ey_language_pack` VALUES ('21', 'sys9', 'Home', '1', 'en', '100', '1545267534', '1545267534');
INSERT INTO `ey_language_pack` VALUES ('22', 'sys10', 'No time', '1', 'en', '100', '1545267534', '1545267534');
INSERT INTO `ey_language_pack` VALUES ('23', 'sys11', 'Previous', '1', 'en', '100', '1545267534', '1545267534');
INSERT INTO `ey_language_pack` VALUES ('24', 'sys12', 'Next', '1', 'en', '100', '1545267534', '1545267534');
INSERT INTO `ey_language_pack` VALUES ('25', 'yybl1', '网站首页', '0', 'cn', '100', '1545272835', '1545272835');
INSERT INTO `ey_language_pack` VALUES ('26', 'yybl1', 'Home', '0', 'en', '100', '1545272835', '1545272835');
INSERT INTO `ey_language_pack` VALUES ('27', 'yybl2', '全部', '0', 'cn', '100', '1545272897', '1545272897');
INSERT INTO `ey_language_pack` VALUES ('28', 'yybl2', 'All', '0', 'en', '100', '1545272897', '1545272897');
INSERT INTO `ey_language_pack` VALUES ('29', 'yybl3', '查看更多', '0', 'cn', '100', '1545272961', '1545272961');
INSERT INTO `ey_language_pack` VALUES ('30', 'yybl3', 'View more', '0', 'en', '100', '1545272961', '1545272961');
INSERT INTO `ey_language_pack` VALUES ('31', 'yybl4', '联系热线', '0', 'cn', '100', '1545273023', '1545273023');
INSERT INTO `ey_language_pack` VALUES ('32', 'yybl4', 'Contact Hotline', '0', 'en', '100', '1545273023', '1545273023');
INSERT INTO `ey_language_pack` VALUES ('33', 'yybl5', '你的位置', '0', 'cn', '100', '1545273158', '1545273158');
INSERT INTO `ey_language_pack` VALUES ('34', 'yybl5', 'Rosition', '0', 'en', '100', '1545273158', '1545273158');
INSERT INTO `ey_language_pack` VALUES ('35', 'yybl6', '请输入关键词', '0', 'cn', '100', '1545273239', '1545273239');
INSERT INTO `ey_language_pack` VALUES ('36', 'yybl6', 'Please enter a keyword', '0', 'en', '100', '1545273239', '1545273239');
INSERT INTO `ey_language_pack` VALUES ('37', 'yybl7', '为您推荐', '0', 'cn', '100', '1545273292', '1545273292');
INSERT INTO `ey_language_pack` VALUES ('38', 'yybl7', 'Recommend for you', '0', 'en', '100', '1545273292', '1545273292');
INSERT INTO `ey_language_pack` VALUES ('39', 'yybl8', '热门推荐', '0', 'cn', '100', '1545273376', '1545273376');
INSERT INTO `ey_language_pack` VALUES ('40', 'yybl8', 'Popular recommendation', '0', 'en', '100', '1545273376', '1545273376');
INSERT INTO `ey_language_pack` VALUES ('41', 'yybl9', '详细信息', '0', 'cn', '100', '1545273418', '1545273418');
INSERT INTO `ey_language_pack` VALUES ('42', 'yybl9', 'Details', '0', 'en', '100', '1545273418', '1545273418');
INSERT INTO `ey_language_pack` VALUES ('43', 'yybl10', '下载包', '0', 'cn', '100', '1545273596', '1545273596');
INSERT INTO `ey_language_pack` VALUES ('44', 'yybl10', 'Download package', '0', 'en', '100', '1545273596', '1545273596');
INSERT INTO `ey_language_pack` VALUES ('45', 'yybl11', '文件附件列表', '0', 'cn', '100', '1545273655', '1545273655');
INSERT INTO `ey_language_pack` VALUES ('46', 'yybl11', 'List of file attachments', '0', 'en', '100', '1545273655', '1545273655');
INSERT INTO `ey_language_pack` VALUES ('47', 'yybl12', '结果', '0', 'cn', '100', '1545274437', '1545274437');
INSERT INTO `ey_language_pack` VALUES ('48', 'yybl12', 'Result', '0', 'en', '100', '1545274437', '1545274437');
INSERT INTO `ey_language_pack` VALUES ('49', 'yybl13', '没有数据了', '0', 'cn', '100', '1545274472', '1547516837');
INSERT INTO `ey_language_pack` VALUES ('50', 'yybl13', 'No data.', '0', 'en', '100', '1545274472', '1547516837');

-- -----------------------------
-- Table structure for `ey_links`
-- -----------------------------
DROP TABLE IF EXISTS `ey_links`;
CREATE TABLE `ey_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `typeid` tinyint(1) DEFAULT '1' COMMENT '类型：1=文字链接，2=图片链接',
  `title` varchar(50) DEFAULT '' COMMENT '网站标题',
  `url` varchar(100) DEFAULT '' COMMENT '网站地址',
  `logo` varchar(255) DEFAULT '' COMMENT '网站LOGO',
  `sort_order` int(11) DEFAULT '0' COMMENT '排序号',
  `target` tinyint(1) DEFAULT '0' COMMENT '是否开启浏览器新窗口',
  `email` varchar(50) DEFAULT NULL,
  `intro` text COMMENT '网站简况',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态(1=显示，0=屏蔽)',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `delete_time` int(11) DEFAULT '0' COMMENT '软删除时间',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='友情链接表';

-- -----------------------------
-- Records of `ey_links`
-- -----------------------------
INSERT INTO `ey_links` VALUES ('1', '1', '百度', 'http://www.baidu.com', '', '100', '1', '', '', '1', 'cn', '0', '1524975826', '1537585074');
INSERT INTO `ey_links` VALUES ('2', '1', '腾讯', 'http://www.qq.com', '', '100', '1', '', '', '1', 'cn', '0', '1524976095', '1537585061');
INSERT INTO `ey_links` VALUES ('3', '1', '新浪', 'http://www.sina.com.cn', '', '100', '1', '', '', '1', 'cn', '0', '1532414285', '1537585047');
INSERT INTO `ey_links` VALUES ('4', '1', '淘宝', 'http://www.taobao.com', '', '100', '1', '', '', '1', 'cn', '0', '1532414529', '1537585013');
INSERT INTO `ey_links` VALUES ('5', '1', '微博', 'http://www.weibo.com', '', '100', '1', '', '', '1', 'cn', '0', '1532414726', '1537585146');
INSERT INTO `ey_links` VALUES ('6', '1', 'baidu', 'http://www.baidu.com', '', '100', '1', '', '', '1', 'en', '0', '1524975826', '1547473399');
INSERT INTO `ey_links` VALUES ('7', '1', 'qq', 'http://www.qq.com', '', '100', '1', '', '', '1', 'en', '0', '1524976095', '1547473391');
INSERT INTO `ey_links` VALUES ('8', '1', 'sina', 'http://www.sina.com.cn', '', '100', '1', '', '', '1', 'en', '0', '1532414285', '1547473376');
INSERT INTO `ey_links` VALUES ('9', '1', 'taobao', 'http://www.taobao.com', '', '100', '1', '', '', '1', 'en', '0', '1532414529', '1547473365');
INSERT INTO `ey_links` VALUES ('10', '1', 'weibo', 'http://www.weibo.com', '', '100', '1', '', '', '1', 'en', '0', '1532414726', '1547473355');

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
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COMMENT='产品表单属性值';

-- -----------------------------
-- Records of `ey_product_attr`
-- -----------------------------
INSERT INTO `ey_product_attr` VALUES ('5', '28', '5', '13.3', '0', '1526613498', '1526613498');
INSERT INTO `ey_product_attr` VALUES ('6', '28', '6', '3KG', '0', '1526613498', '1526613498');
INSERT INTO `ey_product_attr` VALUES ('7', '29', '7', 'AKG&amp;HUAWEI', '0', '1526613820', '1526613820');
INSERT INTO `ey_product_attr` VALUES ('8', '29', '8', '支持', '0', '1526613820', '1526613820');
INSERT INTO `ey_product_attr` VALUES ('17', '37', '2', '苹果', '', '1527507984', '1527507984');
INSERT INTO `ey_product_attr` VALUES ('18', '37', '1', '牛逼', '', '1527507984', '1527507984');
INSERT INTO `ey_product_attr` VALUES ('19', '37', '3', '触摸', '', '1527507984', '1527507984');
INSERT INTO `ey_product_attr` VALUES ('20', '37', '4', '234234', '', '1527507984', '1527507984');
INSERT INTO `ey_product_attr` VALUES ('21', '27', '2', 'EMUI 4.1 + Android 6.0', '', '1531726843', '1531726843');
INSERT INTO `ey_product_attr` VALUES ('22', '27', '1', 'EMUI 4.1', '', '1531726843', '1531726843');
INSERT INTO `ey_product_attr` VALUES ('23', '27', '3', '虚拟键盘', '', '1531726843', '1531726843');
INSERT INTO `ey_product_attr` VALUES ('24', '27', '4', 'EDI-AL10', '', '1531726843', '1531726843');
INSERT INTO `ey_product_attr` VALUES ('25', '53', '17', 'AKG&amp;HUAWEI', '', '1545268991', '1545268991');
INSERT INTO `ey_product_attr` VALUES ('26', '53', '18', 'Support', '', '1545268991', '1545268991');
INSERT INTO `ey_product_attr` VALUES ('27', '54', '15', '13.3', '', '1545270139', '1545270139');
INSERT INTO `ey_product_attr` VALUES ('28', '54', '16', '3KG', '', '1545270139', '1545270139');
INSERT INTO `ey_product_attr` VALUES ('29', '55', '12', 'EMUI 4.1 + Android 6.0', '', '1545270361', '1545270361');
INSERT INTO `ey_product_attr` VALUES ('30', '55', '11', 'EMUI 4.1', '', '1545270361', '1545270361');
INSERT INTO `ey_product_attr` VALUES ('31', '55', '13', 'Virtual keyboard', '', '1545270361', '1545270361');
INSERT INTO `ey_product_attr` VALUES ('32', '55', '14', 'EDI-AL10', '', '1545270361', '1545270361');
INSERT INTO `ey_product_attr` VALUES ('33', '56', '12', 'iOS 9.0', '', '1545270634', '1545270634');
INSERT INTO `ey_product_attr` VALUES ('34', '56', '11', '4.7 inch display screen', '', '1545270634', '1545270634');
INSERT INTO `ey_product_attr` VALUES ('35', '56', '13', 'Virtual keyboard', '', '1545270634', '1545270634');
INSERT INTO `ey_product_attr` VALUES ('36', '56', '14', '6S', '', '1545270634', '1545270634');

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
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='产品表单属性表';

-- -----------------------------
-- Records of `ey_product_attribute`
-- -----------------------------
INSERT INTO `ey_product_attribute` VALUES ('1', '用户界面', '24', '0', '0', '', '100', 'cn', '0', '1526612774', '1526612774');
INSERT INTO `ey_product_attribute` VALUES ('2', '操作系统', '24', '0', '0', '', '10', 'cn', '0', '1526612785', '1526612785');
INSERT INTO `ey_product_attribute` VALUES ('3', '键盘类型', '24', '0', '0', '', '100', 'cn', '0', '1526613004', '1526613004');
INSERT INTO `ey_product_attribute` VALUES ('4', ' 型号', '24', '0', '0', '', '100', 'cn', '0', '1526613011', '1526613011');
INSERT INTO `ey_product_attribute` VALUES ('5', '屏幕大小', '26', '0', '0', '', '100', 'cn', '0', '1526613252', '1526613252');
INSERT INTO `ey_product_attribute` VALUES ('6', '重量', '26', '0', '0', '', '100', 'cn', '0', '1526613259', '1526613259');
INSERT INTO `ey_product_attribute` VALUES ('7', '型号', '27', '0', '0', '', '100', 'cn', '0', '1526613668', '1526613668');
INSERT INTO `ey_product_attribute` VALUES ('8', '支持蓝牙', '27', '0', '0', '', '100', 'cn', '0', '1526613732', '1526613732');
INSERT INTO `ey_product_attribute` VALUES ('11', 'User Interface', '43', '0', '0', '', '100', 'en', '0', '1526612774', '1545274001');
INSERT INTO `ey_product_attribute` VALUES ('12', 'operating system', '43', '0', '0', '', '10', 'en', '0', '1526612785', '1545273990');
INSERT INTO `ey_product_attribute` VALUES ('13', 'Keyboard type', '43', '0', '0', '', '100', 'en', '0', '1526613004', '1545274014');
INSERT INTO `ey_product_attribute` VALUES ('14', 'model', '43', '0', '0', '', '100', 'en', '0', '1526613011', '1545274025');
INSERT INTO `ey_product_attribute` VALUES ('15', 'Screen size', '46', '0', '0', '', '100', 'en', '0', '1526613252', '1545270158');
INSERT INTO `ey_product_attribute` VALUES ('16', 'Weight', '46', '0', '0', '', '100', 'en', '0', '1526613259', '1545270171');
INSERT INTO `ey_product_attribute` VALUES ('17', 'Model', '48', '0', '0', '', '100', 'en', '0', '1526613668', '1545268934');
INSERT INTO `ey_product_attribute` VALUES ('18', 'Support Bluetooth', '48', '0', '0', '', '100', 'en', '0', '1526613732', '1545268951');

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
  PRIMARY KEY (`id`),
  KEY `news_id` (`aid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='产品附加表';

-- -----------------------------
-- Records of `ey_product_content`
-- -----------------------------
INSERT INTO `ey_product_content` VALUES ('2', '27', '&lt;p&gt;&lt;span style=&quot;color: rgb(94, 115, 135); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);&quot;&gt;全向录音/指向回放、定向免提、指关节手势、分屏多窗口、语音控制、情景智能、单手操作、杂志锁屏、手机找回、无线WIFI打印、学生模式、多屏互动、运动健康&lt;/span&gt;&lt;span style=&quot;color: rgb(94, 115, 135); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);&quot;&gt;全向录音/指向回放、定向免提、指关节手势、分屏多窗口、语音控制、情景智能、单手操作、杂志锁屏、手机找回、无线WIFI打印、学生模式、多屏互动、运动健康&lt;/span&gt;&lt;span style=&quot;color: rgb(94, 115, 135); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);&quot;&gt;全向录音/指向回放、定向免提、指关节手势、分屏多窗口、语音控制、情景智能、单手操作、杂志锁屏、手机找回、无线WIFI打印、学生模式、多屏互动、运动健康的&lt;/span&gt;&lt;/p&gt;', '1552987268', '1552987268');
INSERT INTO `ey_product_content` VALUES ('3', '28', '&lt;p&gt;轻薄全金属机身 / 256GB SSD / 第八代 Intel 酷睿i5 处理器 / FHD 全贴合屏幕 / 指纹解锁 / office激活不支持7天无理由退货&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;/uploads/ueditor/20190114/aa0555d4f00163878c1d39ab046bb742.jpg&quot; title=&quot;aa0555d4f00163878c1d39ab046bb742.jpg&quot; alt=&quot;aa0555d4f00163878c1d39ab046bb742.jpg&quot;/&gt;&lt;/p&gt;', '1547462162', '1547462162');
INSERT INTO `ey_product_content` VALUES ('4', '29', '&lt;p&gt;&lt;span style=&quot;color: rgb(94, 115, 135); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 14px; background-color: rgb(255, 255, 255);&quot;&gt;特性	M3平板定制AKG品牌高保真耳机，配合M3平板享受HiFi音质&lt;/span&gt;&lt;/p&gt;', '1552992589', '1552992589');
INSERT INTO `ey_product_content` VALUES ('5', '37', '&lt;p&gt;全身都是科技亮点！7nm麒麟芯片，问鼎性能巅峰，4000万超广角徕卡三摄，随手捕捉大场面，支持25mm微距拍摄，解锁大波新题材，充电也有无线、反向玩法，快充之快刷新世界观。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;&lt;img src=&quot;/uploads/ueditor/20190319/e44a5110abdd18ef314e34769272bb44.jpg&quot; title=&quot;e44a5110abdd18ef314e34769272bb44.jpg&quot; alt=&quot;e44a5110abdd18ef314e34769272bb44.jpg&quot;/&gt;&lt;br/&gt;&lt;/p&gt;', '1552986270', '1552986270');
INSERT INTO `ey_product_content` VALUES ('6', '53', '&lt;p&gt;Features: M3 Tablet Customized AKG Brand High Fidelity Headphones, Enjoy HiFi Sound Quality with M3 Tablet&lt;/p&gt;', '1545270011', '1545270011');
INSERT INTO `ey_product_content` VALUES ('7', '54', '&lt;p style=&quot;box-sizing: inherit; margin-top: 0px; margin-bottom: 0.8em; color: rgb(42, 51, 60); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;Light and thin all-metal fuselage / 256GB SSD / 8th generation Intel Core i5 processor / FHD full-fit screen / fingerprint unlock / Office activation does not support 7 days without reason to return the goods&lt;/p&gt;&lt;p style=&quot;box-sizing: inherit; margin-top: 0px; margin-bottom: 0.8em; color: rgb(42, 51, 60); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; white-space: normal; background-color: rgb(255, 255, 255);&quot;&gt;&lt;img src=&quot;/uploads/ueditor/20181220/5c1af35580c9a.png&quot; title=&quot;4e5a31ff6bb3f88e03ae2d80353cdc67.jpg&quot; alt=&quot;4e5a31ff6bb3f88e03ae2d80353cdc67.jpg&quot; style=&quot;box-sizing: inherit; border-style: none; vertical-align: top; max-width: 100%; height: auto !important;&quot;/&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1545270139', '1545270139');
INSERT INTO `ey_product_content` VALUES ('8', '55', '&lt;p&gt;Omnidirectional recording/pointing playback, directional hands-free, finger joint gesture, split screen multi-window, voice control, situational intelligence, single-handed operation, magazine lock screen, mobile phone retrieval, wireless WIFI printing, student mode, multi-screen interaction, sports health omnidirectional recording/pointing playback, directional hands-free, finger joint gesture, split screen multi-window, voice control, situational intelligence, single-handed operation, magazine lock Screen, mobile phone retrieval, wireless WIFI printing, student mode, multi-screen interaction, omnidirectional recording/pointing playback of sports health, directional hands-free, finger joint gesture, split screen multi-window, voice control, situational intelligence, single-handed operation, magazine lock screen, mobile phone retrieval, wireless WIFI printing, student mode, multi-screen interaction, sports health&lt;/p&gt;', '1545270361', '1545270361');
INSERT INTO `ey_product_content` VALUES ('9', '56', '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;img src=&quot;/uploads/ueditor/20181220/ac06829b2cc1070d75c6d3ea8e6aa55e.jpg&quot; title=&quot;ac06829b2cc1070d75c6d3ea8e6aa55e.jpg&quot; alt=&quot;ac06829b2cc1070d75c6d3ea8e6aa55e.jpg&quot;/&gt;&lt;/p&gt;', '1545270643', '1545270643');

-- -----------------------------
-- Table structure for `ey_product_img`
-- -----------------------------
DROP TABLE IF EXISTS `ey_product_img`;
CREATE TABLE `ey_product_img` (
  `img_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `aid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '产品ID',
  `title` varchar(200) DEFAULT '' COMMENT '产品标题',
  `image_url` varchar(255) DEFAULT '' COMMENT '文件存储路径',
  `width` int(11) DEFAULT '0' COMMENT '图片宽度',
  `height` int(11) DEFAULT '0' COMMENT '图片高度',
  `filesize` varchar(255) DEFAULT '' COMMENT '文件大小',
  `mime` varchar(50) DEFAULT '' COMMENT '图片类型',
  `sort_order` smallint(5) DEFAULT '0' COMMENT '排序',
  `add_time` int(10) unsigned DEFAULT '0' COMMENT '上传时间',
  PRIMARY KEY (`img_id`),
  KEY `arcid` (`aid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=92 DEFAULT CHARSET=utf8 COMMENT='产品图片表';

-- -----------------------------
-- Records of `ey_product_img`
-- -----------------------------
INSERT INTO `ey_product_img` VALUES ('88', '27', '华为HUAWEI NOTE 8', '/uploads/allimg/20190319/26ae26bc48454c5504f5e73ab40c2ef7.jpg', '300', '300', '0', 'image/jpeg', '1', '1552987268');
INSERT INTO `ey_product_img` VALUES ('73', '28', '小米笔记本Air 13.3', '/uploads/allimg/20190114/ab5d06499e319c69ea0f07e7443846f0.jpg', '400', '400', '0', 'image/jpeg', '2', '1547462162');
INSERT INTO `ey_product_img` VALUES ('72', '28', '小米笔记本Air 13.3', '/uploads/allimg/20190114/44bfc4ca62d0c69d750716616410ff96.jpg', '560', '560', '0', 'image/jpeg', '1', '1547462162');
INSERT INTO `ey_product_img` VALUES ('90', '29', ' 小米蓝牙项圈耳机', '/uploads/allimg/20190114/252a53e6fbc8f441b2570f755d2bbeb8.jpg', '400', '400', '0', 'image/jpeg', '1', '1552992589');
INSERT INTO `ey_product_img` VALUES ('91', '29', ' 小米蓝牙项圈耳机', '/uploads/allimg/20190114/e4c13f6756671c2352699564e72090cd.jpg', '400', '400', '0', 'image/jpeg', '2', '1552992589');
INSERT INTO `ey_product_img` VALUES ('89', '27', '华为HUAWEI NOTE 8', '/uploads/allimg/20190319/644f02053cba0e7925a12afc4d97f473.jpg', '300', '300', '0', 'image/jpeg', '2', '1552987268');
INSERT INTO `ey_product_img` VALUES ('87', '37', '华为无线快充手机', '/uploads/allimg/20190319/7d9262ce87a9596a20c82fd04828c322.jpg', '300', '300', '0', 'image/jpeg', '3', '1552986270');
INSERT INTO `ey_product_img` VALUES ('85', '37', '华为无线快充手机', '/uploads/allimg/20190319/db90933b59102a0f3fa0d2588bd8e2fb.jpg', '300', '300', '0', 'image/jpeg', '1', '1552986270');
INSERT INTO `ey_product_img` VALUES ('86', '37', '华为无线快充手机', '/uploads/allimg/20190319/d86971fb735a22e0a29ba039c5a4179b.jpg', '300', '300', '0', 'image/jpeg', '2', '1552986270');
INSERT INTO `ey_product_img` VALUES ('53', '53', 'Millet Bluetooth Necklace Headset', '/uploads/allimg/20181220/076dc6f94445ca25c80f9bdd76fd51ca.jpg', '400', '400', '0', 'image/jpeg', '3', '1545270011');
INSERT INTO `ey_product_img` VALUES ('52', '53', 'Millet Bluetooth Necklace Headset', '/uploads/allimg/20181220/e574d239dda300f806d5cb0c3d3f993e.jpg', '400', '400', '0', 'image/jpeg', '2', '1545270011');
INSERT INTO `ey_product_img` VALUES ('51', '53', 'Millet Bluetooth Necklace Headset', '/uploads/allimg/20181220/d2464706d9e621164caa8d613f6e3ed7.jpg', '400', '400', '0', 'image/jpeg', '1', '1545270011');
INSERT INTO `ey_product_img` VALUES ('54', '54', 'Millet notebook Air 13.3', '/uploads/allimg/20181220/da4e2737e982fd5655a8945e85a57773.jpg', '560', '560', '0', 'image/jpeg', '1', '1545270139');
INSERT INTO `ey_product_img` VALUES ('55', '54', 'Millet notebook Air 13.3', '/uploads/allimg/20181220/28b9c72484b187acc2562553bf4e2751.jpg', '400', '400', '0', 'image/jpeg', '2', '1545270139');
INSERT INTO `ey_product_img` VALUES ('56', '55', 'HUAWEI NOTE 8', '/uploads/allimg/20181220/a0f7262557ef417ad237ff268843ff7e.jpg', '400', '400', '0', 'image/jpeg', '1', '1545270361');
INSERT INTO `ey_product_img` VALUES ('64', '56', 'Apple iPhone 6s 16GB ', '/uploads/allimg/20181220/91d1360d886bfaedae98ab0b2a0e2adb.jpg', '800', '800', '0', 'image/jpeg', '4', '1545270643');
INSERT INTO `ey_product_img` VALUES ('63', '56', 'Apple iPhone 6s 16GB ', '/uploads/allimg/20181220/23004c3ebfc092aa34f94e28d849bc4f.jpg', '800', '800', '0', 'image/jpeg', '3', '1545270643');
INSERT INTO `ey_product_img` VALUES ('62', '56', 'Apple iPhone 6s 16GB ', '/uploads/allimg/20181220/f4bba94ad9473b0ef23517613eed5bc8.jpg', '800', '800', '0', 'image/jpeg', '2', '1545270643');
INSERT INTO `ey_product_img` VALUES ('61', '56', 'Apple iPhone 6s 16GB ', '/uploads/allimg/20181220/658d0bdb72a0798baea2b255384e7c7a.jpg', '800', '800', '0', 'image/jpeg', '1', '1545270643');

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
) ENGINE=MyISAM AUTO_INCREMENT=47498 DEFAULT CHARSET=utf8 COMMENT='区域表';

-- -----------------------------
-- Records of `ey_region`
-- -----------------------------
INSERT INTO `ey_region` VALUES ('1', '北京市', '1', '0', 'B');
INSERT INTO `ey_region` VALUES ('2', '市辖区', '2', '1', 'S');
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
INSERT INTO `ey_region` VALUES ('300', '县', '2', '1', 'X');
INSERT INTO `ey_region` VALUES ('301', '密云县', '3', '300', 'M');
INSERT INTO `ey_region` VALUES ('322', '延庆县', '3', '300', 'Y');
INSERT INTO `ey_region` VALUES ('338', '天津市', '1', '0', 'T');
INSERT INTO `ey_region` VALUES ('339', '市辖区', '2', '338', 'S');
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
INSERT INTO `ey_region` VALUES ('569', '市辖县', '2', '338', 'S');
INSERT INTO `ey_region` VALUES ('570', '宁河县', '3', '569', 'N');
INSERT INTO `ey_region` VALUES ('586', '静海县', '3', '569', 'J');
INSERT INTO `ey_region` VALUES ('608', '蓟县', '3', '569', 'J');
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
INSERT INTO `ey_region` VALUES ('1223', '青龙满族自治县', '3', '1188', 'Q');
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
INSERT INTO `ey_region` VALUES ('1806', '满城县', '3', '1772', 'M');
INSERT INTO `ey_region` VALUES ('1820', '清苑县', '3', '1772', 'Q');
INSERT INTO `ey_region` VALUES ('1839', '涞水县', '3', '1772', 'L');
INSERT INTO `ey_region` VALUES ('1856', '阜平县', '3', '1772', 'F');
INSERT INTO `ey_region` VALUES ('1870', '徐水县', '3', '1772', 'X');
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
INSERT INTO `ey_region` VALUES ('2543', '丰宁满族自治县', '3', '2400', 'F');
INSERT INTO `ey_region` VALUES ('2570', '宽城满族自治县', '3', '2400', 'K');
INSERT INTO `ey_region` VALUES ('2589', '围场满族蒙古族自治县', '3', '2400', 'W');
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
INSERT INTO `ey_region` VALUES ('2765', '孟村回族自治县', '3', '2629', 'M');
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
INSERT INTO `ey_region` VALUES ('2932', '大厂回族自治县', '3', '2849', 'D');
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
INSERT INTO `ey_region` VALUES ('4670', '内蒙古自治区', '1', '0', 'N');
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
INSERT INTO `ey_region` VALUES ('5262', '莫力达瓦达斡尔族自治旗', '3', '5236', 'M');
INSERT INTO `ey_region` VALUES ('5277', '鄂伦春自治旗', '3', '5236', 'E');
INSERT INTO `ey_region` VALUES ('5303', '鄂温克族自治旗', '3', '5236', 'E');
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
INSERT INTO `ey_region` VALUES ('6445', '新宾满族自治县', '3', '6384', 'X');
INSERT INTO `ey_region` VALUES ('6461', '清原满族自治县', '3', '6384', 'Q');
INSERT INTO `ey_region` VALUES ('6476', '本溪市', '2', '5827', 'B');
INSERT INTO `ey_region` VALUES ('6477', '市辖区', '3', '6476', 'S');
INSERT INTO `ey_region` VALUES ('6478', '平山区', '3', '6476', 'P');
INSERT INTO `ey_region` VALUES ('6488', '溪湖区', '3', '6476', 'X');
INSERT INTO `ey_region` VALUES ('6499', '明山区', '3', '6476', 'M');
INSERT INTO `ey_region` VALUES ('6509', '南芬区', '3', '6476', 'N');
INSERT INTO `ey_region` VALUES ('6515', '本溪满族自治县', '3', '6476', 'B');
INSERT INTO `ey_region` VALUES ('6528', '桓仁满族自治县', '3', '6476', 'H');
INSERT INTO `ey_region` VALUES ('6542', '丹东市', '2', '5827', 'D');
INSERT INTO `ey_region` VALUES ('6543', '市辖区', '3', '6542', 'S');
INSERT INTO `ey_region` VALUES ('6544', '元宝区', '3', '6542', 'Y');
INSERT INTO `ey_region` VALUES ('6552', '振兴区', '3', '6542', 'Z');
INSERT INTO `ey_region` VALUES ('6563', '振安区', '3', '6542', 'Z');
INSERT INTO `ey_region` VALUES ('6573', '宽甸满族自治县', '3', '6542', 'K');
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
INSERT INTO `ey_region` VALUES ('6893', '阜新蒙古族自治县', '3', '6851', 'F');
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
INSERT INTO `ey_region` VALUES ('7299', '喀喇沁左翼蒙古族自治县', '3', '7208', 'K');
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
INSERT INTO `ey_region` VALUES ('7916', '伊通满族自治县', '3', '7868', 'Y');
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
INSERT INTO `ey_region` VALUES ('8192', '长白朝鲜族自治县', '3', '8144', 'C');
INSERT INTO `ey_region` VALUES ('8202', '临江市', '3', '8144', 'L');
INSERT INTO `ey_region` VALUES ('8216', '松原市', '2', '7531', 'S');
INSERT INTO `ey_region` VALUES ('8217', '松原市辖区', '3', '8216', 'S');
INSERT INTO `ey_region` VALUES ('8218', '宁江区', '3', '8216', 'N');
INSERT INTO `ey_region` VALUES ('8239', '前郭尔罗斯蒙古族自治县', '3', '8216', 'Q');
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
INSERT INTO `ey_region` VALUES ('8445', '延边朝鲜族自治州', '2', '7531', 'Y');
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
INSERT INTO `ey_region` VALUES ('10544', '市辖区', '2', '10543', 'S');
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
INSERT INTO `ey_region` VALUES ('10779', '县', '2', '10543', 'X');
INSERT INTO `ey_region` VALUES ('10780', '崇明县', '3', '10779', 'C');
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
INSERT INTO `ey_region` VALUES ('14189', '景宁畲族自治县', '3', '14033', 'J');
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
INSERT INTO `ey_region` VALUES ('24525', '长阳土家族自治县', '3', '24453', 'C');
INSERT INTO `ey_region` VALUES ('24537', '五峰土家族自治县', '3', '24453', 'W');
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
INSERT INTO `ey_region` VALUES ('26444', '城步苗族自治县', '3', '26242', 'C');
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
INSERT INTO `ey_region` VALUES ('27815', '麻阳苗族自治县', '3', '27650', 'M');
INSERT INTO `ey_region` VALUES ('27839', '新晃侗族自治县', '3', '27650', 'X');
INSERT INTO `ey_region` VALUES ('27863', '芷江侗族自治县', '3', '27650', 'Z');
INSERT INTO `ey_region` VALUES ('27892', '靖州苗族侗族县', '3', '27650', 'J');
INSERT INTO `ey_region` VALUES ('27906', '通道侗族自治县', '3', '27650', 'T');
INSERT INTO `ey_region` VALUES ('27930', '洪江市', '3', '27650', 'H');
INSERT INTO `ey_region` VALUES ('27963', '娄底市', '2', '25579', 'L');
INSERT INTO `ey_region` VALUES ('27964', '市辖区', '3', '27963', 'S');
INSERT INTO `ey_region` VALUES ('27965', '娄星区', '3', '27963', 'L');
INSERT INTO `ey_region` VALUES ('27980', '双峰县', '3', '27963', 'S');
INSERT INTO `ey_region` VALUES ('27997', '新化县', '3', '27963', 'X');
INSERT INTO `ey_region` VALUES ('28027', '冷水江市', '3', '27963', 'L');
INSERT INTO `ey_region` VALUES ('28044', '涟源市', '3', '27963', 'L');
INSERT INTO `ey_region` VALUES ('28065', '湘西土家族苗族自治州', '2', '25579', 'X');
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
INSERT INTO `ey_region` VALUES ('28497', '乳源瑶族自治县', '3', '28421', 'R');
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
INSERT INTO `ey_region` VALUES ('29797', '连南瑶族自治县', '3', '29755', 'L');
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
INSERT INTO `ey_region` VALUES ('30164', '广西壮族自治区', '1', '0', 'G');
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
INSERT INTO `ey_region` VALUES ('30411', '融水苗族自治县', '3', '30319', 'R');
INSERT INTO `ey_region` VALUES ('30432', '三江侗族自治县', '3', '30319', 'S');
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
INSERT INTO `ey_region` VALUES ('30559', '龙胜各族自治县', '3', '30448', 'L');
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
INSERT INTO `ey_region` VALUES ('31167', '隆林各族自治县', '3', '31033', 'L');
INSERT INTO `ey_region` VALUES ('31184', '贺州市', '2', '30164', 'H');
INSERT INTO `ey_region` VALUES ('31185', '市辖区', '3', '31184', 'S');
INSERT INTO `ey_region` VALUES ('31186', '八步区', '3', '31184', 'B');
INSERT INTO `ey_region` VALUES ('31208', '昭平县', '3', '31184', 'Z');
INSERT INTO `ey_region` VALUES ('31221', '钟山县', '3', '31184', 'Z');
INSERT INTO `ey_region` VALUES ('31236', '富川瑶族自治县', '3', '31184', 'F');
INSERT INTO `ey_region` VALUES ('31249', '河池市', '2', '30164', 'H');
INSERT INTO `ey_region` VALUES ('31250', '市辖区', '3', '31249', 'S');
INSERT INTO `ey_region` VALUES ('31251', '金城江区', '3', '31249', 'J');
INSERT INTO `ey_region` VALUES ('31264', '南丹县', '3', '31249', 'N');
INSERT INTO `ey_region` VALUES ('31276', '天峨县', '3', '31249', 'T');
INSERT INTO `ey_region` VALUES ('31286', '凤山县', '3', '31249', 'F');
INSERT INTO `ey_region` VALUES ('31296', '东兰县', '3', '31249', 'D');
INSERT INTO `ey_region` VALUES ('31311', '罗城仫佬族自治县', '3', '31249', 'L');
INSERT INTO `ey_region` VALUES ('31323', '环江毛南族自治县', '3', '31249', 'H');
INSERT INTO `ey_region` VALUES ('31336', '巴马瑶族自治县', '3', '31249', 'B');
INSERT INTO `ey_region` VALUES ('31347', '都安瑶族自治县', '3', '31249', 'D');
INSERT INTO `ey_region` VALUES ('31367', '大化瑶族自治县', '3', '31249', 'D');
INSERT INTO `ey_region` VALUES ('31384', '宜州市', '3', '31249', 'Y');
INSERT INTO `ey_region` VALUES ('31401', '来宾市', '2', '30164', 'L');
INSERT INTO `ey_region` VALUES ('31402', '市辖区', '3', '31401', 'S');
INSERT INTO `ey_region` VALUES ('31403', '兴宾区', '3', '31401', 'X');
INSERT INTO `ey_region` VALUES ('31427', '忻城县', '3', '31401', 'X');
INSERT INTO `ey_region` VALUES ('31440', '象州县', '3', '31401', 'X');
INSERT INTO `ey_region` VALUES ('31452', '武宣县', '3', '31401', 'W');
INSERT INTO `ey_region` VALUES ('31463', '金秀瑶族自治县', '3', '31401', 'J');
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
INSERT INTO `ey_region` VALUES ('31814', '白沙黎族自治县', '2', '31563', 'B');
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
INSERT INTO `ey_region` VALUES ('31835', '昌江黎族自治县', '2', '31563', 'C');
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
INSERT INTO `ey_region` VALUES ('31848', '乐东黎族自治县', '2', '31563', 'L');
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
INSERT INTO `ey_region` VALUES ('31869', '陵水黎族自治县', '2', '31563', 'L');
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
INSERT INTO `ey_region` VALUES ('31884', '保亭黎族苗族自治县', '2', '31563', 'B');
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
INSERT INTO `ey_region` VALUES ('31900', '琼中黎族苗族自治县', '2', '31563', 'Q');
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
INSERT INTO `ey_region` VALUES ('31923', '西沙群岛', '2', '31563', 'X');
INSERT INTO `ey_region` VALUES ('31924', '永兴岛（虚拟镇）', '3', '31923', 'Y');
INSERT INTO `ey_region` VALUES ('31925', '南沙群岛', '2', '31563', 'N');
INSERT INTO `ey_region` VALUES ('31926', '永署礁（虚拟镇）', '3', '31925', 'Y');
INSERT INTO `ey_region` VALUES ('31927', '中沙群岛的岛礁及其海域', '2', '31563', 'Z');
INSERT INTO `ey_region` VALUES ('31928', '中沙岛礁（虚拟镇）', '3', '31927', 'Z');
INSERT INTO `ey_region` VALUES ('31929', '重庆市', '1', '0', 'Z');
INSERT INTO `ey_region` VALUES ('31930', '市辖区', '2', '31929', 'S');
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
INSERT INTO `ey_region` VALUES ('32894', '秀山土家族苗族自治县', '3', '32380', 'X');
INSERT INTO `ey_region` VALUES ('32927', '酉阳土家族苗族自治县', '3', '32380', 'Y');
INSERT INTO `ey_region` VALUES ('32967', '彭水苗族土家族自治县', '3', '32380', 'P');
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
INSERT INTO `ey_region` VALUES ('34028', '北川羌族自治县', '3', '33817', 'B');
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
INSERT INTO `ey_region` VALUES ('34799', '峨边彝族自治县', '3', '34628', 'E');
INSERT INTO `ey_region` VALUES ('34819', '马边彝族自治县', '3', '34628', 'M');
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
INSERT INTO `ey_region` VALUES ('36926', '甘孜藏族自治州', '2', '33007', 'G');
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
INSERT INTO `ey_region` VALUES ('37315', '木里藏族自治县', '3', '37270', 'M');
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
INSERT INTO `ey_region` VALUES ('38267', '道真仡佬族苗族自治县', '3', '38145', 'D');
INSERT INTO `ey_region` VALUES ('38282', '务川仡佬族苗族自治县', '3', '38145', 'W');
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
INSERT INTO `ey_region` VALUES ('38452', '镇宁布依族苗族自治县', '3', '38402', 'Z');
INSERT INTO `ey_region` VALUES ('38469', '关岭自治县', '3', '38402', 'G');
INSERT INTO `ey_region` VALUES ('38484', '紫云苗族布依族自治县', '3', '38402', 'Z');
INSERT INTO `ey_region` VALUES ('38497', '铜仁地区', '2', '37906', 'T');
INSERT INTO `ey_region` VALUES ('38498', '铜仁市', '3', '38497', 'T');
INSERT INTO `ey_region` VALUES ('38516', '江口县', '3', '38497', 'J');
INSERT INTO `ey_region` VALUES ('38526', '玉屏侗族自治县', '3', '38497', 'Y');
INSERT INTO `ey_region` VALUES ('38533', '石阡县', '3', '38497', 'S');
INSERT INTO `ey_region` VALUES ('38552', '思南县　', '3', '38497', 'S');
INSERT INTO `ey_region` VALUES ('38580', '印江土家族苗族自治县', '3', '38497', 'Y');
INSERT INTO `ey_region` VALUES ('38598', '德江县', '3', '38497', 'D');
INSERT INTO `ey_region` VALUES ('38619', '沿河土家族自治县', '3', '38497', 'Y');
INSERT INTO `ey_region` VALUES ('38642', '松桃苗族自治县', '3', '38497', 'S');
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
INSERT INTO `ey_region` VALUES ('39011', '威宁彝族回族苗族自治县', '3', '38816', 'W');
INSERT INTO `ey_region` VALUES ('39047', '赫章县', '3', '38816', 'H');
INSERT INTO `ey_region` VALUES ('39075', '黔东南苗族侗族自治州', '2', '37906', 'Q');
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
INSERT INTO `ey_region` VALUES ('39302', '黔南布依族苗族自治州', '2', '37906', 'Q');
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
INSERT INTO `ey_region` VALUES ('39534', '三都水族自治县', '3', '39302', 'S');
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
INSERT INTO `ey_region` VALUES ('41103', '西藏自治区', '1', '0', 'X');
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
INSERT INTO `ey_region` VALUES ('44294', '肃南裕固族自治县', '3', '44265', 'S');
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
INSERT INTO `ey_region` VALUES ('44531', '肃北蒙古族自治县', '3', '44477', 'S');
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
INSERT INTO `ey_region` VALUES ('45131', '东乡族自治县', '3', '45035', 'D');
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
INSERT INTO `ey_region` VALUES ('45320', '大通回族土族自治县', '3', '45287', 'D');
INSERT INTO `ey_region` VALUES ('45341', '湟中县', '3', '45287', 'H');
INSERT INTO `ey_region` VALUES ('45358', '湟源县', '3', '45287', 'H');
INSERT INTO `ey_region` VALUES ('45368', '海东地区', '2', '45286', 'H');
INSERT INTO `ey_region` VALUES ('45369', '平安县', '3', '45368', 'P');
INSERT INTO `ey_region` VALUES ('45378', '民和县', '3', '45368', 'M');
INSERT INTO `ey_region` VALUES ('45401', '乐都县', '3', '45368', 'L');
INSERT INTO `ey_region` VALUES ('45421', '互助县', '3', '45368', 'H');
INSERT INTO `ey_region` VALUES ('45441', '化隆回族自治县', '3', '45368', 'H');
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
INSERT INTO `ey_region` VALUES ('45753', '宁夏回族自治区', '1', '0', 'N');
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
INSERT INTO `ey_region` VALUES ('46047', '新疆维吾尔自治区', '1', '0', 'X');
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
INSERT INTO `ey_region` VALUES ('46380', '博尔塔拉蒙古自治州', '2', '46047', 'B');
INSERT INTO `ey_region` VALUES ('46381', '博乐市', '3', '46380', 'B');
INSERT INTO `ey_region` VALUES ('46399', '精河县', '3', '46380', 'J');
INSERT INTO `ey_region` VALUES ('46410', '温泉县', '3', '46380', 'W');
INSERT INTO `ey_region` VALUES ('46422', '巴音郭楞蒙古自治州', '2', '46047', 'B');
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
INSERT INTO `ey_region` VALUES ('47360', '和布克赛尔蒙古自治县', '3', '47241', 'H');
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
INSERT INTO `ey_region` VALUES ('47493', '台湾省', '1', '0', 'T');
INSERT INTO `ey_region` VALUES ('47494', '香港特别行政区', '1', '0', 'X');
INSERT INTO `ey_region` VALUES ('47495', '澳门特别行政区', '1', '0', 'A');
INSERT INTO `ey_region` VALUES ('47496', '龙华新区', '3', '28558', 'L');
INSERT INTO `ey_region` VALUES ('47497', '光明新区', '3', '28558', 'G');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='搜索词统计表';


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
  `selected` tinyint(1) DEFAULT '1' COMMENT '购物车选中状态：0未选中，1选中',
  `lang` varchar(30) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '加入购物车的时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`cart_id`),
  KEY `users_id` (`users_id`,`product_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='购物车表';


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
  `order_code` varchar(20) NOT NULL DEFAULT '' COMMENT '订单编号',
  `users_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `order_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单状态：0未付款(已下单)，1已付款(待发货)，2已发货(待收货)，3已完成(确认收货)，-1订单取消(已关闭)，4订单过期',
  `payment_method` tinyint(1) DEFAULT '0' COMMENT '订单支付方式，0为在线支付，1为货到付款，默认0',
  `pay_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间',
  `pay_name` varchar(20) NOT NULL DEFAULT '' COMMENT '支付方式名称',
  `wechat_pay_type` varchar(20) NOT NULL DEFAULT '' COMMENT '微信支付时，标记使用的支付类型（扫码支付，微信内部，微信H5页面）',
  `pay_details` text COMMENT '支付时返回的数据，以serialize序列化后存入，用于后续查询。',
  `express_order` varchar(32) DEFAULT '' COMMENT '发货物流单号',
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
  `user_note` text COMMENT '会员备注',
  `lang` varchar(30) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned DEFAULT '0' COMMENT '下单时间',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_code` (`order_code`),
  KEY `users_id` (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='订单主表';


-- -----------------------------
-- Table structure for `ey_shop_order_details`
-- -----------------------------
DROP TABLE IF EXISTS `ey_shop_order_details`;
CREATE TABLE `ey_shop_order_details` (
  `details_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `order_id` int(10) DEFAULT '0' COMMENT '订单ID',
  `users_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `product_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '产品id',
  `product_name` varchar(100) DEFAULT '' COMMENT '产品名称',
  `num` int(10) DEFAULT '0' COMMENT '单个产品数量',
  `data` text COMMENT '序列化额外数据',
  `product_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '产品单价',
  `prom_type` tinyint(1) unsigned DEFAULT '0' COMMENT '产品类型：0普通产品，1虚拟产品',
  `litpic` varchar(500) DEFAULT '' COMMENT '封面图片',
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
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='单页附加表';

-- -----------------------------
-- Records of `ey_single_content`
-- -----------------------------
INSERT INTO `ey_single_content` VALUES ('1', '1', '1', '', '0', '1527836335');
INSERT INTO `ey_single_content` VALUES ('2', '2', '8', '&lt;p style=&quot;white-space: normal; text-indent: 2em;&quot;&gt;易优内容管理系统(&lt;a href=&quot;http://www.eyoucms.com/&quot; target=&quot;_blank&quot;&gt;EyouCms&lt;/a&gt;) 以模板多、易优化、开源而闻名，是国内新锐的PHP开源网站管理系统，也是最受用户好评的PHP类CMS系统，在经历多年的发展，目前的版本无论在功能，还是在后台易用性方面，都有了长足的发展和进步，eyoucms免费版的主要目标用户锁定在有企业建站需求的群体，当然也不乏有个人用户和学校等在使用该系统。&lt;/p&gt;&lt;p style=&quot;white-space: normal; text-indent: 2em;&quot;&gt;企业网站，无论大型还是中小型企业，利用网络传递信息在一定程度上提高了办事的效率，提高企业的竞争力。&lt;a href=&quot;http://www.eyoucms.com/&quot; target=&quot;_blank&quot;&gt;EyouCms&lt;/a&gt;网站建设系统做各种网站，&lt;a href=&quot;http://www.eyoucms.com/&quot; target=&quot;_blank&quot;&gt;EyouCms&lt;/a&gt;是什么，&lt;a href=&quot;http://www.eyoucms.com/&quot; target=&quot;_blank&quot;&gt;EyouCms&lt;/a&gt;是一个自由和开放源码的内容管理系统，它是一个可以独立使用的内容发布系统（CMS）。在中国，&lt;a href=&quot;http://www.eyoucms.com/&quot; target=&quot;_blank&quot;&gt;EyouCms&lt;/a&gt;属于最受人们喜爱的CMS系统。&lt;/p&gt;&lt;p style=&quot;white-space: normal; text-indent: 2em;&quot;&gt;政府机关，通过建立政府门户，有利于各种信息和资源的整合，为政府和社会公众之间加强联系和沟通，从而使政府可以更快、更便捷、更有效开展工作。&lt;/p&gt;&lt;p style=&quot;white-space: normal; text-indent: 2em;&quot;&gt;教育机构，通过网络信息的引入，使得教育机构之间及教育机构内部和教育者之间进行信息传递，全面提升教育类网站的层面。&lt;/p&gt;&lt;h3 style=&quot;white-space: normal;&quot;&gt;优点&lt;/h3&gt;&lt;ol class=&quot; list-paddingleft-2&quot;&gt;&lt;li&gt;&lt;p&gt;易用：使用易优你可以用它十分钟搭建一个企业网站，后台简单易用。&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p&gt;完善：易优基本包含了一个常规企业网站需要的一切功能。&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p&gt;扩展性：易优亦可通过插件库支持更多功能，如阿里短信或小程序等第三方扩展。&lt;br/&gt;&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p&gt;丰富的资料：作为一个国内cms，易优拥有完善的帮助文档及标签手册。&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p&gt;丰富的模版：易优拥有大量免费的漂亮模版，涵盖各行各业，任用户自由选择。&lt;/p&gt;&lt;/li&gt;&lt;/ol&gt;&lt;h3 style=&quot;white-space: normal;&quot;&gt;缺点&lt;/h3&gt;&lt;ol class=&quot; list-paddingleft-2&quot;&gt;&lt;li&gt;&lt;p&gt;创新性：易优是一个基于用户需求开发的系统，用户需求不断改变，我们竭力跟着并改变。&lt;/p&gt;&lt;/li&gt;&lt;li&gt;&lt;p&gt;社区：目前专注模板建设，这对于一个开源项目来说社区互动并没有真正建立起来。&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;/li&gt;&lt;/ol&gt;&lt;p style=&quot;white-space: normal; text-indent: 2em;&quot;&gt;未来，期待与用户携手缔造一个更好的易而优CMS...2018.6.1&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '0', '1527836706');
INSERT INTO `ey_single_content` VALUES ('3', '3', '13', '&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;针对不同服务器、虚拟空间，运行PHP的环境也有所不同，目前主要分为：Nginx、apache、IIS以及其他服务器。下面分享如何去掉URL上的index.php字符，&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;记得最后要重启服务器，在管理后台清除缓存哦！&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;nbsp;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;【IIS服务器】&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;在网站根目录下有个 web.config 文件，这个文件的作用是重写URL，让URL变得简短，易于SEO优化，以及用户的记忆。打开 web.config 文件，在原有的基础上加以下代码片段即可。&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;rewrite&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;rules&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;rule name=&amp;quot;Imported Rule 1&amp;quot; enabled=&amp;quot;true&amp;quot; stopProcessing=&amp;quot;true&amp;quot;&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;match url=&amp;quot;^(.*)$&amp;quot; /&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;conditions logicalGrouping=&amp;quot;MatchAll&amp;quot;&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;add input=&amp;quot;{HTTP_HOST}&amp;quot; pattern=&amp;quot;^(.*)$&amp;quot; /&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;add input=&amp;quot;{REQUEST_FILENAME}&amp;quot; matchType=&amp;quot;IsFile&amp;quot; negate=&amp;quot;true&amp;quot; /&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;add input=&amp;quot;{REQUEST_FILENAME}&amp;quot; matchType=&amp;quot;IsDirectory&amp;quot; negate=&amp;quot;true&amp;quot; /&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;/conditions&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;action type=&amp;quot;Rewrite&amp;quot; url=&amp;quot;index.php/{R:1}&amp;quot; /&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;/rule&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;/rules&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;/rewrite&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;nbsp;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;以下是某个香港虚拟空间的效果：&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;?xml version=&amp;quot;1.0&amp;quot; encoding=&amp;quot;UTF-8&amp;quot;?&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;configuration&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;system.webServer&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;handlers&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;remove name=&amp;quot;PHP-7.0-7i24.com&amp;quot; /&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;remove name=&amp;quot;PHP-5.6-7i24.com&amp;quot; /&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;remove name=&amp;quot;PHP-5.5-7i24.com&amp;quot; /&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;remove name=&amp;quot;PHP-5.4-7i24.com&amp;quot; /&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;remove name=&amp;quot;PHP-5.3-7i24.com&amp;quot; /&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;remove name=&amp;quot;PHP-5.2-7i24.com&amp;quot; /&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;add name=&amp;quot;PHP-5.4-7i24.com&amp;quot; path=&amp;quot;*.php&amp;quot; verb=&amp;quot;*&amp;quot; modules=&amp;quot;FastCgiModule&amp;quot; scriptProcessor=&amp;quot;c:php.4php-cgi.exe&amp;quot; resourceType=&amp;quot;Either&amp;quot; /&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;/handlers&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;rewrite&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;rules&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;rule name=&amp;quot;Imported Rule 1&amp;quot; enabled=&amp;quot;true&amp;quot; stopProcessing=&amp;quot;true&amp;quot;&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;match url=&amp;quot;^(.*)$&amp;quot; /&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;conditions logicalGrouping=&amp;quot;MatchAll&amp;quot;&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;add input=&amp;quot;{HTTP_HOST}&amp;quot; pattern=&amp;quot;^(.*)$&amp;quot; /&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;add input=&amp;quot;{REQUEST_FILENAME}&amp;quot; matchType=&amp;quot;IsFile&amp;quot; negate=&amp;quot;true&amp;quot; /&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;add input=&amp;quot;{REQUEST_FILENAME}&amp;quot; matchType=&amp;quot;IsDirectory&amp;quot; negate=&amp;quot;true&amp;quot; /&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;/conditions&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;action type=&amp;quot;Rewrite&amp;quot; url=&amp;quot;index.php/{R:1}&amp;quot; /&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;/rule&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;/rules&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;&amp;lt;/rewrite&amp;gt;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;/system.webServer&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;/configuration&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;nbsp;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;【Nginx服务器】&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;在原有的nginx重写文件里新增以下代码片段：&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;location / {&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;if (!-e $request_filename) {&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;rewrite ^(.*)$ /index.php?s=/$1 last;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;break;&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;}&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;}&lt;/span&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;nbsp;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;【apache服务器】&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;易优CMS在apache服务器环境默认自动隐藏index.php入口。&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;如果发现没隐藏，可以检查根目录.htaccess是否含有以下代码段：&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;IfModule mod_rewrite.c&amp;gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&lt;div style=&quot;box-sizing: border-box;&quot;&gt;Options +FollowSymlinks -Multiviews&lt;/div&gt;&lt;div style=&quot;box-sizing: border-box;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;RewriteEngine on&lt;/span&gt;&lt;/div&gt;&lt;div style=&quot;box-sizing: border-box;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;RewriteCond %{REQUEST_FILENAME} !-d&lt;/span&gt;&lt;/div&gt;&lt;div style=&quot;box-sizing: border-box;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;RewriteCond %{REQUEST_FILENAME} !-f&lt;/span&gt;&lt;/div&gt;&lt;div style=&quot;box-sizing: border-box;&quot;&gt;&lt;span style=&quot;box-sizing: border-box; color: rgb(255, 0, 0);&quot;&gt;RewriteRule ^(.*)$ index.php/$1 [QSA,PT,L]&lt;/span&gt;&lt;/div&gt;&lt;/div&gt;&lt;div yne-bulb-block=&quot;paragraph&quot; style=&quot;box-sizing: border-box; color: rgb(34, 34, 34); font-family: &amp;quot;Segoe UI&amp;quot;, &amp;quot;Lucida Grande&amp;quot;, Helvetica, Arial, &amp;quot;Microsoft YaHei&amp;quot;, FreeSans, Arimo, &amp;quot;Droid Sans&amp;quot;, &amp;quot;wenquanyi micro hei&amp;quot;, &amp;quot;Hiragino Sans GB&amp;quot;, &amp;quot;Hiragino Sans GB W3&amp;quot;, Roboto, Arial, sans-serif; font-size: 18px; white-space: normal;&quot;&gt;&amp;lt;/IfModule&amp;gt;&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;&lt;br style=&quot;box-sizing: border-box;&quot;/&gt;如果存在，继续查看apache是否开启了URL重写模块 rewrite_module ， 然后重启服务就行了。&lt;/div&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '0', '1531710225');
INSERT INTO `ey_single_content` VALUES ('4', '45', '31', '', '1545272580', '1545272580');
INSERT INTO `ey_single_content` VALUES ('5', '46', '32', '&lt;p&gt;EyouCms is known for its many templates, easy to optimize, and open source. It is a new PHP open source website management system and the most popular PHP CMS system. After years of development, the current version is no matter. In terms of functions and ease of use in the background, there has been considerable development and progress. The main target users of eyoucms free version are locked in groups that have the needs of enterprises to establish their stations. Of course, there are also some users and schools that use the system.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Enterprise websites, whether large ｏｒ small ｏｒ medium-sized enterprises, use the network to transmit information to a certain extent to improve the efficiency of business and improve the competitiveness of enterprises. EyouCms website construction system to do various websites, what is EyouCms, EyouCms is a free and open source content management system, it is a content publishing system (CMS) that can be used independently. In China, EyouCms belongs to the most popular CMS system.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Government agencies, through the establishment of government portals, facilitate the integration of various information and resources, and strengthen communication and communication between the government and the public, so that the government can work faster, more conveniently, and more effectively.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Educational institutions, through the introduction of network information, enable information transmission between educational institutions and within educational institutions and educators, and comprehensively enhance the level of educational websites.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;advantage&lt;/p&gt;&lt;p&gt;Easy to use: You can use it for ten minutes to build a corporate website with easy-to-use background.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Perfect: Yiyou Basic contains all the features needed for a regular corporate website.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Scalability: Yiyou can also support more functions through the plugin library, such as third-party extensions such as Ali SMS ｏｒ applet.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Rich information: As a domestic cms, Yiyou has a complete help document and label manual.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Rich template: Yiyou has a large number of free and beautiful templates, covering all walks of life, and users are free to choose.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Disadvantage&lt;/p&gt;&lt;p&gt;Innovative: Yiyou is a system developed based on user needs. User needs are constantly changing, and we strive to follow and change.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;Community: Currently focused on template building, this is not really established for an open source project.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;p&gt;In the future, we look forward to working with users to create a better and more excellent CMS...2018.6.1&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1545272565', '1545272565');
INSERT INTO `ey_single_content` VALUES ('6', '47', '39', '&lt;p&gt;For different servers and virtual spaces, the environment for running PHP is also different, currently mainly divided into: Nginx, apache, IIS and other servers. Here&amp;#39;s how to remove the index. PHP character from the URL. Remember to restart the server and clear the cache in the management background.&lt;/p&gt;&lt;p&gt;[IIS Server]&lt;/p&gt;&lt;p&gt;There is a web. config file in the root directory of the website. The function of this file is to rewrite the URL, make the URL short, easy to optimize by SEO, and the user&amp;#39;s memory. Open the web. config file and add the following code fragments on the original basis.&lt;/p&gt;&lt;p&gt;&amp;lt;rewrite&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;rules&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;rule name= &amp;quot;Imported Rule 1&amp;quot; enabled= &amp;quot;true&amp;quot; stopProcessing= &amp;quot;true&amp;quot;&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;match url=&amp;quot;^(. *)$&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;conditions logicalGrouping=&amp;quot;MatchAll&amp;quot;&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;add input=&amp;quot;{HTTP_HOST}&amp;quot; pattern=&amp;quot;^(. *)$&amp;quot;/&amp;gt;&amp;quot;&lt;/p&gt;&lt;p&gt;&amp;lt;add input=&amp;quot;{REQUEST_FILENAME}&amp;quot; matchType= &amp;quot;IsFile&amp;quot; negate= &amp;quot;true&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;add input=&amp;quot;{REQUEST_FILENAME}&amp;quot; matchType= &amp;quot;IsDirectory&amp;quot; negate= &amp;quot;true&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/conditions&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;action type=&amp;quot;Rewrite&amp;quot; url=&amp;quot;index.php/{R:1}&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/rule&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/rules&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/rewrite&amp;gt;&lt;/p&gt;&lt;p&gt;The following is the effect of a Hong Kong virtual space:&lt;/p&gt;&lt;p&gt;&amp;lt;? XML version = &amp;quot;1.0&amp;quot; encoding = &amp;quot;UTF-8&amp;quot;?&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;configuration&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;system.webServer&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;handlers&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;remove name=&amp;quot;PHP-7.0-7i24.com&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;remove name=&amp;quot;PHP-5.6-7i24.com&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;remove name=&amp;quot;PHP-5.5-7i24.com&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;remove name=&amp;quot;PHP-5.4-7i24.com&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;remove name=&amp;quot;PHP-5.3-7i24.com&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;remove name=&amp;quot;PHP-5.2-7i24.com&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;add name=&amp;quot;PHP-5.4-7i24.com&amp;quot; path=&amp;quot;*.php&amp;quot; verb=&amp;quot;*&amp;quot; modules=&amp;quot;FastCgiModule&amp;quot; scriptProcessor=&amp;quot;c:php.4php-cgi.exe&amp;quot; resourceType=&amp;quot;Either&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/handlers&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;rewrite&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;rules&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;rule name= &amp;quot;Imported Rule 1&amp;quot; enabled= &amp;quot;true&amp;quot; stopProcessing= &amp;quot;true&amp;quot;&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;match url=&amp;quot;^(. *)$&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;conditions logicalGrouping=&amp;quot;MatchAll&amp;quot;&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;add input=&amp;quot;{HTTP_HOST}&amp;quot; pattern=&amp;quot;^(. *)$&amp;quot;/&amp;gt;&amp;quot;&lt;/p&gt;&lt;p&gt;&amp;lt;add input=&amp;quot;{REQUEST_FILENAME}&amp;quot; matchType= &amp;quot;IsFile&amp;quot; negate= &amp;quot;true&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;add input=&amp;quot;{REQUEST_FILENAME}&amp;quot; matchType= &amp;quot;IsDirectory&amp;quot; negate= &amp;quot;true&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/conditions&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;action type=&amp;quot;Rewrite&amp;quot; url=&amp;quot;index.php/{R:1}&amp;quot;/&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/rule&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/rules&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/rewrite&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/system.webServer&amp;gt;&lt;/p&gt;&lt;p&gt;&amp;lt;/configuration&amp;gt;&lt;/p&gt;&lt;p&gt;[Nginx Server]&lt;/p&gt;&lt;p&gt;Add the following code fragments to the original nginx rewrite file:&lt;/p&gt;&lt;p&gt;Location / {&lt;/p&gt;&lt;p&gt;If (!-e $request_filename) {&lt;/p&gt;&lt;p&gt;Rewrite ^(. *)$/ index. php? S=/$1 last;&lt;/p&gt;&lt;p&gt;Break;&lt;/p&gt;&lt;p&gt;}&lt;/p&gt;&lt;p&gt;}&lt;/p&gt;&lt;p&gt;[apache server]&lt;/p&gt;&lt;p&gt;Yiyou CMS automatically hides index. PHP entries by default in Apache server environment.&lt;/p&gt;&lt;p&gt;If no hiding is found, you can check whether the root directory. htaccess contains the following code snippets:&lt;/p&gt;&lt;p&gt;&amp;lt;IfModule mod_rewrite.c&amp;gt;&lt;/p&gt;&lt;p&gt;Options + FollowSymlinks - Multiviews&lt;/p&gt;&lt;p&gt;RewriteEngine on&lt;/p&gt;&lt;p&gt;RewriteCond%{REQUEST_FILENAME}!-d&lt;/p&gt;&lt;p&gt;RewriteCond%{REQUEST_FILENAME}!-f&lt;/p&gt;&lt;p&gt;RewriteRule ^(*)$index.php/$1 [QSA, PT, L]&lt;/p&gt;&lt;p&gt;&amp;lt;/IfModule&amp;gt;&lt;/p&gt;&lt;p&gt;If it exists, go ahead and see if Apache has opened the URL rewrite_module and restart the service.&lt;/p&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;', '1545270877', '1545270877');

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
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='邮件模板表';

-- -----------------------------
-- Records of `ey_smtp_tpl`
-- -----------------------------
INSERT INTO `ey_smtp_tpl` VALUES ('1', '留言表单', '您有新的留言消息，请查收！', '${content}', '1', '1', 'cn', '1544763495', '1552638302');
INSERT INTO `ey_smtp_tpl` VALUES ('2', '会员注册', '验证码已发送至您的邮箱，请登录邮箱查看验证码！', '${content}', '2', '1', 'cn', '1544763495', '1552667056');
INSERT INTO `ey_smtp_tpl` VALUES ('3', '绑定邮箱', '验证码已发送至您的邮箱，请登录邮箱查看验证码！', '${content}', '3', '1', 'cn', '1544763495', '1552667400');
INSERT INTO `ey_smtp_tpl` VALUES ('4', '找回密码', '验证码已发送至您的邮箱，请登录邮箱查看验证码！', '${content}', '4', '1', 'cn', '1544763495', '1552663577');
INSERT INTO `ey_smtp_tpl` VALUES ('5', '留言表单', '您有新的留言消息，请查收！', '${content}', '1', '1', 'en', '1544763495', '1552638302');
INSERT INTO `ey_smtp_tpl` VALUES ('6', '会员注册', '验证码已发送至您的邮箱，请登录邮箱查看验证码！', '${content}', '2', '1', 'en', '1544763495', '1552667056');
INSERT INTO `ey_smtp_tpl` VALUES ('7', '绑定邮箱', '验证码已发送至您的邮箱，请登录邮箱查看验证码！', '${content}', '3', '1', 'en', '1544763495', '1552667400');
INSERT INTO `ey_smtp_tpl` VALUES ('8', '找回密码', '验证码已发送至您的邮箱，请登录邮箱查看验证码！', '${content}', '4', '1', 'en', '1544763495', '1552663577');

-- -----------------------------
-- Table structure for `ey_tagindex`
-- -----------------------------
DROP TABLE IF EXISTS `ey_tagindex`;
CREATE TABLE `ey_tagindex` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'tagid',
  `tag` varchar(50) NOT NULL DEFAULT '' COMMENT 'tag内容',
  `typeid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '栏目ID',
  `count` int(10) unsigned DEFAULT '0' COMMENT '点击',
  `total` int(10) unsigned DEFAULT '0' COMMENT '文档数',
  `weekcc` int(10) unsigned DEFAULT '0' COMMENT '周统计',
  `monthcc` int(10) unsigned DEFAULT '0' COMMENT '月统计',
  `weekup` int(10) unsigned DEFAULT '0' COMMENT '每周更新',
  `monthup` int(10) unsigned DEFAULT '0' COMMENT '每月更新',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(10) unsigned DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `typeid` (`typeid`) USING BTREE,
  KEY `count` (`count`,`total`,`weekcc`,`monthcc`,`weekup`,`monthup`,`add_time`) USING BTREE,
  KEY `tag` (`tag`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COMMENT='标签索引表';

-- -----------------------------
-- Records of `ey_tagindex`
-- -----------------------------
INSERT INTO `ey_tagindex` VALUES ('24', 'TAG标签', '10', '0', '0', '0', '0', '0', '0', 'cn', '1526544706');
INSERT INTO `ey_tagindex` VALUES ('25', '对的', '10', '0', '0', '0', '0', '0', '0', 'cn', '1526544706');
INSERT INTO `ey_tagindex` VALUES ('26', '替换稿件', '10', '0', '0', '0', '0', '0', '0', 'cn', '1526544706');
INSERT INTO `ey_tagindex` VALUES ('27', '杨靖宇', '10', '0', '0', '0', '0', '0', '0', 'cn', '1526544706');
INSERT INTO `ey_tagindex` VALUES ('28', '网站', '12', '0', '0', '0', '0', '0', '0', 'cn', '1526608291');
INSERT INTO `ey_tagindex` VALUES ('29', '建设', '12', '0', '0', '0', '0', '0', '0', 'cn', '1526608291');
INSERT INTO `ey_tagindex` VALUES ('30', '五大核心', '12', '0', '0', '0', '0', '0', '0', 'cn', '1526608291');
INSERT INTO `ey_tagindex` VALUES ('31', '要素', '12', '0', '0', '0', '0', '0', '0', 'cn', '1526608291');
INSERT INTO `ey_tagindex` VALUES ('32', '华为', '24', '0', '0', '0', '0', '0', '0', 'cn', '1526613161');
INSERT INTO `ey_tagindex` VALUES ('33', 'HUAWEI', '24', '0', '0', '0', '0', '0', '0', 'cn', '1526613161');
INSERT INTO `ey_tagindex` VALUES ('34', 'NOTE 8', '24', '0', '0', '0', '0', '0', '0', 'cn', '1526613161');
INSERT INTO `ey_tagindex` VALUES ('35', '宅男', '5', '0', '0', '0', '0', '0', '0', 'cn', '1526614158');
INSERT INTO `ey_tagindex` VALUES ('36', '女神', '5', '0', '0', '0', '0', '0', '0', 'cn', '1526614158');
INSERT INTO `ey_tagindex` VALUES ('37', '一号', '5', '0', '0', '0', '0', '0', '0', 'cn', '1526614158');

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
INSERT INTO `ey_taglist` VALUES ('30', '13', '12', '五大核心', '0', 'cn', '1547462639', '0');
INSERT INTO `ey_taglist` VALUES ('29', '13', '12', '建设', '0', 'cn', '1547462639', '0');
INSERT INTO `ey_taglist` VALUES ('32', '27', '24', '华为', '0', 'cn', '1552987268', '0');
INSERT INTO `ey_taglist` VALUES ('33', '27', '24', 'HUAWEI', '0', 'cn', '1552987268', '0');
INSERT INTO `ey_taglist` VALUES ('34', '27', '24', 'NOTE 8', '0', 'cn', '1552987268', '0');
INSERT INTO `ey_taglist` VALUES ('37', '30', '5', '一号', '0', 'cn', '1547463959', '0');
INSERT INTO `ey_taglist` VALUES ('36', '30', '5', '女神', '0', 'cn', '1547463959', '0');
INSERT INTO `ey_taglist` VALUES ('35', '30', '5', '宅男', '0', 'cn', '1547463959', '0');
INSERT INTO `ey_taglist` VALUES ('28', '13', '12', '网站', '0', 'cn', '1547462639', '0');
INSERT INTO `ey_taglist` VALUES ('31', '13', '12', '要素', '0', 'cn', '1547462639', '0');

-- -----------------------------
-- Table structure for `ey_ui_config`
-- -----------------------------
DROP TABLE IF EXISTS `ey_ui_config`;
CREATE TABLE `ey_ui_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `md5key` varchar(100) NOT NULL DEFAULT '' COMMENT '唯一键值（由 theme_style、page、name）组成',
  `theme_style` varchar(20) DEFAULT 'pc' COMMENT '模板风格',
  `page` varchar(64) DEFAULT '' COMMENT '页面分组',
  `type` varchar(50) DEFAULT '' COMMENT '编辑类型',
  `name` varchar(50) DEFAULT '' COMMENT '与页面的e-id对应',
  `value` text COMMENT '页面美化的val值',
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
  `reg_time` int(11) unsigned DEFAULT '0' COMMENT '注册时间',
  `last_login` int(11) unsigned DEFAULT '0' COMMENT '最后登录时间',
  `last_ip` varchar(15) DEFAULT '' COMMENT '最后登录ip',
  `login_count` int(11) DEFAULT '0' COMMENT '登陆次数',
  `head_pic` varchar(255) DEFAULT NULL COMMENT '头像',
  `province` int(6) DEFAULT '0' COMMENT '省份',
  `city` int(6) DEFAULT '0' COMMENT '市区',
  `district` int(6) DEFAULT '0' COMMENT '县',
  `level` smallint(5) DEFAULT '0' COMMENT '会员等级',
  `discount` decimal(10,2) DEFAULT '1.00' COMMENT '会员折扣，默认1不享受',
  `total_amount` decimal(10,2) DEFAULT '0.00' COMMENT '消费累计额度',
  `is_activation` tinyint(1) DEFAULT '1' COMMENT '是否激活，0否，1是。\r\n后台注册默认为1激活。\r\n前台注册时，当会员功能设置选择后台审核，需后台激活才可以登陆。',
  `register_place` tinyint(1) DEFAULT '2' COMMENT '注册位置。后台注册不受注册验证影响，1为后台注册，2为前台注册。默认为2。',
  `open_id` varchar(30) NOT NULL DEFAULT '' COMMENT '微信唯一标识openid',
  `is_lock` tinyint(1) DEFAULT '0' COMMENT '是否被锁定冻结',
  `lang` varchar(20) DEFAULT 'cn' COMMENT '语言标识',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '伪删除，1=是，0=否',
  `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员信息表';


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员功能配置表';


-- -----------------------------
-- Table structure for `ey_users_level`
-- -----------------------------
DROP TABLE IF EXISTS `ey_users_level`;
CREATE TABLE `ey_users_level` (
  `level_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `level_name` varchar(30) DEFAULT '' COMMENT '级别名称',
  `level_value` varchar(20) DEFAULT '' COMMENT '会员等级值',
  `is_system` tinyint(1) DEFAULT '0' COMMENT '类型，1=系统，0=用户',
  `amount` decimal(10,2) DEFAULT '0.00' COMMENT '消费额度',
  `discount` int(10) DEFAULT '0' COMMENT '折扣率',
  `lang` varchar(20) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`level_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='会员级别表';

-- -----------------------------
-- Records of `ey_users_level`
-- -----------------------------
INSERT INTO `ey_users_level` VALUES ('1', '注册会员', '10', '1', '0.00', '100', 'cn', '0', '1551151513');

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员属性表(信息）';


-- -----------------------------
-- Table structure for `ey_users_menu`
-- -----------------------------
DROP TABLE IF EXISTS `ey_users_menu`;
CREATE TABLE `ey_users_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `title` varchar(30) DEFAULT '' COMMENT '导航名称',
  `mca` varchar(50) DEFAULT '' COMMENT '分组/控制器/操作名',
  `is_userpage` tinyint(1) DEFAULT '0' COMMENT '默认会员首页',
  `sort_order` int(10) DEFAULT '0' COMMENT '排序号',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态，1=显示，0=隐藏',
  `lang` varchar(20) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='会员菜单表';

-- -----------------------------
-- Records of `ey_users_menu`
-- -----------------------------
INSERT INTO `ey_users_menu` VALUES ('1', '个人信息', 'user/Users/index', '1', '100', '1', 'cn', '1555904190', '1555917737');
INSERT INTO `ey_users_menu` VALUES ('2', '账户充值', 'user/Pay/pay_consumer_details', '0', '100', '0', 'cn', '1555904190', '1557733960');
INSERT INTO `ey_users_menu` VALUES ('3', '商城中心', 'user/Shop/shop_centre', '0', '100', '0', 'cn', '1555904190', '1557733960');

-- -----------------------------
-- Table structure for `ey_users_money`
-- -----------------------------
DROP TABLE IF EXISTS `ey_users_money`;
CREATE TABLE `ey_users_money` (
  `moneyid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '金额明细表ID',
  `users_id` int(10) DEFAULT '0' COMMENT '会员表ID',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '充值金额',
  `users_money` decimal(10,2) DEFAULT '0.00' COMMENT '此条记录的账户金额',
  `cause` varchar(60) DEFAULT '' COMMENT '事由',
  `cause_type` tinyint(1) DEFAULT '0' COMMENT '数据类型，0为消费，1为充值。其余后续添加。',
  `status` tinyint(1) DEFAULT '1' COMMENT '是否成功，默认1，0失败，1未付款，2已付款，3已完成，4订单取消。',
  `pay_method` varchar(10) DEFAULT '' COMMENT '支付方式，wechat为微信支付，alipay为支付宝支付',
  `wechat_pay_type` varchar(20) NOT NULL DEFAULT '' COMMENT '微信支付时，标记使用的支付类型（扫码支付，微信内部，微信H5页面）',
  `pay_details` text COMMENT '支付时返回的数据，以serialize序列化后存入，用于后续查询。',
  `order_number` varchar(30) DEFAULT '' COMMENT '订单号',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`moneyid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='金额明细表';


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
  `sort_order` smallint(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `lang` varchar(50) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`para_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='会员属性表(字段)';

-- -----------------------------
-- Records of `ey_users_parameter`
-- -----------------------------
INSERT INTO `ey_users_parameter` VALUES ('1', '手机号码', 'mobile_1', 'mobile', '', '1', '0', '0', '1', 'cn', '0', '1551238026');
INSERT INTO `ey_users_parameter` VALUES ('2', '邮箱地址', 'email_2', 'email', '', '1', '0', '1', '1', 'cn', '0', '1551238549');

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
  `sort_order` int(10) DEFAULT '100' COMMENT '排序号',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='插件应用表';

