/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : eyoucms_develop

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-09-13 14:30:27
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for #@__weapp_sample
-- ----------------------------
DROP TABLE IF EXISTS `#@__weapp_sample`;
CREATE TABLE `#@__weapp_sample` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT '' COMMENT '网站标题',
  `url` varchar(100) DEFAULT '' COMMENT '网站地址',
  `logo` varchar(255) DEFAULT '' COMMENT '网站LOGO',
  `sort_order` int(11) DEFAULT '0' COMMENT '排序号',
  `target` tinyint(1) DEFAULT '0' COMMENT '是否开启浏览器新窗口',
  `intro` text COMMENT '网站简况',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态(1=显示，0=屏蔽)',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of #@__weapp_sample
-- ----------------------------
INSERT INTO `#@__weapp_sample` VALUES ('1', '百度', 'http://www.baidu.com', '', '100', '1', '', '1', '1524975826', '1537585074');
INSERT INTO `#@__weapp_sample` VALUES ('2', '腾讯', 'http://www.qq.com', '', '100', '1', '', '1', '1524976095', '1537585061');
INSERT INTO `#@__weapp_sample` VALUES ('3', '新浪', 'http://www.sina.com.cn', '', '100', '1', '', '1', '1532414285', '1537585047');
INSERT INTO `#@__weapp_sample` VALUES ('4', '小程序开发教程', 'http://www.yiyongtong.com', '', '100', '1', '', '1', '1532414529', '1537585013');
INSERT INTO `#@__weapp_sample` VALUES ('5', '素材58', 'http://www.sucai58.com', '', '100', '1', '', '1', '1532414726', '1537585146');