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

 Date: 24/07/2019 02:02:49
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
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态 0:暂停 1:正常',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `id`(`id`) USING BTREE,
  INDEX `group_id`(`group_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员列表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tx_admin
-- ----------------------------
INSERT INTO `tx_admin` VALUES (1, 1, '', '超级管理员', 'admin', 0, 1562056804);

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
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员权限分组表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tx_admin_group
-- ----------------------------
INSERT INTO `tx_admin_group` VALUES (1, '所有权限', 1, '1', 1562056803);

-- ----------------------------
-- Table structure for tx_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `tx_admin_log`;
CREATE TABLE `tx_admin_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `des` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '操作描述',
  `ip` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `module` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `controller` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `action` varchar(24) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `other_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '其他信息',
  `act_time` int(11) NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员操作表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for tx_admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `tx_admin_menu`;
CREATE TABLE `tx_admin_menu`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `icon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '菜单栏图标文件路径',
  `title` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `module` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `controller` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `action` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(1) NOT NULL COMMENT '是否显示 0:不显示 1:显示',
  `father_id` int(11) NOT NULL DEFAULT 0 COMMENT '上级id',
  `create_time` int(11) NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '管理员菜单表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tx_admin_menu
-- ----------------------------
INSERT INTO `tx_admin_menu` VALUES (1, '/uploads/system_default_icon/system_setup.png', '系统设置', ' ', ' ', ' ', 1, 0, 1561317212);
INSERT INTO `tx_admin_menu` VALUES (2, '', '菜单设置', 'admin', 'menu', 'index', 1, 1, 1561317862);
INSERT INTO `tx_admin_menu` VALUES (3, '/uploads/system_default_icon/admin_manage.png', '管理员管理', '', '', '', 1, 0, 1561356111);
INSERT INTO `tx_admin_menu` VALUES (4, '', '管理员列表', 'admin', 'admin_user', 'index', 1, 3, 1561398603);
INSERT INTO `tx_admin_menu` VALUES (5, '', '管理员分组', 'admin', 'admin_group', 'index', 1, 3, 1561776003);
INSERT INTO `tx_admin_menu` VALUES (6, '', '分组规则', 'admin', 'admin_rule', 'index', 1, 3, 1561847710);
INSERT INTO `tx_admin_menu` VALUES (7, '', '系统配置', 'admin', 'config', 'index', 1, 1, 1562012009);
INSERT INTO `tx_admin_menu` VALUES (8, '', '管理员操作日志', 'admin', 'admin_log', 'index', 1, 1, 1562025198);
INSERT INTO `tx_admin_menu` VALUES (9, '', '过期缓存清除', 'admin', 'tool', 'empty_expired_cache', 1, 1, 1562025198);

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
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '第三方授权表 - 每条数据都有自己的 access_token和refresh_token 都是相对的' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tx_admin_oauth
-- ----------------------------
INSERT INTO `tx_admin_oauth` VALUES (1, 1, '3b1f1f4eafccab421abac7b9bfe056b6', '738607423', 'pwd', 'api', '85019411ed8b0e553e583379a1dcd89c', '79ad3af1e563927eda56b64a25cf8a71', '8ff9eee93f4d2154899215604d99b6b2', 1563902453, 1563902454, 1562056804);

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
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员权限规则表' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of tx_admin_rule
-- ----------------------------
INSERT INTO `tx_admin_rule` VALUES (1, '*/*:*', '所有权限', 1562056803);

-- ----------------------------
-- Table structure for tx_config
-- ----------------------------
DROP TABLE IF EXISTS `tx_config`;
CREATE TABLE `tx_config`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(34) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '配置名',
  `alias` varchar(34) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '别名',
  `type` varchar(34) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '配置分类',
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '内容',
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '描述',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `name`(`name`) USING BTREE,
  INDEX `alias`(`alias`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '配置' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tx_config
-- ----------------------------
INSERT INTO `tx_config` VALUES (1, '管理员访问令牌周期', 'admin_access_token_time_out', 'system', '7200', '秒位单位');
INSERT INTO `tx_config` VALUES (2, '管理员刷新令牌周期', 'admin_refresh_token_time_out', 'system', '604800', '秒位单位');
INSERT INTO `tx_config` VALUES (3, '后台系统名称', 'admin_system_name', 'system', '某某管理系统', '后台系统名称');

SET FOREIGN_KEY_CHECKS = 1;
