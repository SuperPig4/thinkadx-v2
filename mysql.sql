/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 50553
 Source Host           : localhost:3306
 Source Schema         : thinkadx-v2

 Target Server Type    : MySQL
 Target Server Version : 50553
 File Encoding         : 65001

 Date: 04/06/2019 13:11:22
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tx_admin
-- ----------------------------
DROP TABLE IF EXISTS `tx_admin`;
CREATE TABLE `tx_admin`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `avatar` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户头像 相对路径',
  `nickname` varchar(34) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户名',
  `access` varchar(34) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '账号',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE,
  INDEX `group_id`(`group_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员列表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for tx_admin_group
-- ----------------------------
DROP TABLE IF EXISTS `tx_admin_group`;
CREATE TABLE `tx_admin_group`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(24) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '分组名',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '分组状态 0:关闭 1:开启',
  `rules` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '规则ID 用,分隔',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员权限分组表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for tx_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `tx_admin_log`;
CREATE TABLE `tx_admin_log`  (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `des` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '操作描述',
  `ip` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `module` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `controller` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `action` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `other_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '其他信息',
  `act_time` int(11) NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员操作表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for tx_admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `tx_admin_menu`;
CREATE TABLE `tx_admin_menu`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '菜单栏图标文件路径',
  `name` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `module` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `controller` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `action` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '是否显示 0:不显示 1:显示',
  `create_time` int(11) NOT NULL,
  `father_id` int(11) NOT NULL DEFAULT 0 COMMENT '上级id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '管理员菜单表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for tx_admin_oauth
-- ----------------------------
DROP TABLE IF EXISTS `tx_admin_oauth`;
CREATE TABLE `tx_admin_oauth`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `identifier` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '第三方标识符 \r\n如:微信公众号授权登陆 则保存它的openid，unionid则保存到unique_identifier字段',
  `unique_identifier` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '唯一标识符 主要是用来存储相对于用户全局唯一的标识符',
  `oauth_type` varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '类型 auth:授权',
  `port_type` varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '终端类型 wxxcx:微信小程序',
  `access_token` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `refresh_token` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `last_use_access_token` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '最后一次使用的access_token',
  `access_token_create_time` int(11) NOT NULL COMMENT '访问令牌最后一次刷新时间',
  `refresh_token_create_time` int(11) NOT NULL COMMENT '刷新令牌最后一次刷新时间',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `admin_id`(`admin_id`) USING BTREE,
  INDEX `access_token`(`access_token`) USING BTREE,
  INDEX `refresh_token`(`refresh_token`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '第三方授权表 - 每条数据都有自己的 access_token和refresh_token 都是相对的' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for tx_admin_rule
-- ----------------------------
DROP TABLE IF EXISTS `tx_admin_rule`;
CREATE TABLE `tx_admin_rule`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '匹配规则',
  `des` varchar(124) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '规则描述',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员权限规则表' ROW_FORMAT = Compact;

SET FOREIGN_KEY_CHECKS = 1;
