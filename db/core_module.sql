/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50714
Source Host           : localhost:3306
Source Database       : mcp_www

Target Server Type    : MYSQL
Target Server Version : 50714
File Encoding         : 65001

Date: 2017-11-02 16:37:36
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for core_module
-- ----------------------------
DROP TABLE IF EXISTS `core_module`;
CREATE TABLE `core_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_type` enum('android','ios','all','core') COLLATE utf8_bin NOT NULL DEFAULT 'core',
  `module_alias` varchar(50) COLLATE utf8_bin NOT NULL,
  `module_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `module_desc` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `installed` tinyint(1) NOT NULL DEFAULT '0',
  `sort` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- ----------------------------
-- Records of core_module
-- ----------------------------
INSERT INTO `core_module` VALUES ('1', 'core', '通用接口', 'common', '包括图片上传接口等', '0', '0');
INSERT INTO `core_module` VALUES ('2', 'core', '高级管理', 'admin', '包括模块管理、账号管理、权限管理等', '0', '0');
INSERT INTO `core_module` VALUES ('3', 'ios', '狮吼分发平台任务', 'v4_task', '包括任务管理等', '0', '8');
INSERT INTO `core_module` VALUES ('4', 'all', '手机号码管理', 'phone', '包括手机号码管理、导入、导出管理等', '0', '9');
