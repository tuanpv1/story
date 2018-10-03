/*
Navicat MySQL Data Transfer

Source Server         : viettalk_
Source Server Version : 50505
Source Host           : 10.84.86.34:3306
Source Database       : promotion

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2018-07-19 15:50:29
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `activity_user`
-- ----------------------------
DROP TABLE IF EXISTS `activity_user`;
CREATE TABLE `activity_user` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`user_id`  int(11) NOT NULL ,
`username`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`ip_address`  varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`user_agent`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`action`  varchar(126) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`target_id`  int(11) NULL DEFAULT '' ,
`target_type`  smallint(6) NULL DEFAULT '' ,
`created_at`  int(11) NULL DEFAULT '' ,
`description`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`status`  int(3) NULL DEFAULT '' ,
`request_detail`  varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`request_params`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
PRIMARY KEY (`id`),
FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=1

;

-- ----------------------------
-- Table structure for `attribute`
-- ----------------------------
DROP TABLE IF EXISTS `attribute`;
CREATE TABLE `attribute` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Ten cua truong du lieu tang ' ,
`status`  int(3) NOT NULL ,
`created_at`  int(11) NOT NULL ,
`updated_at`  int(11) NOT NULL ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=16

;

-- ----------------------------
-- Table structure for `attribute_value`
-- ----------------------------
DROP TABLE IF EXISTS `attribute_value`;
CREATE TABLE `attribute_value` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`attribute_id`  int(11) NOT NULL ,
`cp_order_id`  int(11) NULL DEFAULT '' ,
`value`  double NULL DEFAULT '' ,
`status`  int(3) NOT NULL ,
`created_at`  int(11) NOT NULL ,
`updated_at`  int(11) NOT NULL ,
`type`  int(3) NOT NULL COMMENT '1 data of table order\n2 data of table promotion\n3 data of table promotion_code' ,
`promotion_id`  int(11) NULL DEFAULT '' ,
`dealer_id`  int(11) NULL DEFAULT '' ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cp_order_id`) REFERENCES `cp_order` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`promotion_id`) REFERENCES `promotion` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`attribute_id`) REFERENCES `attribute` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=172

;

-- ----------------------------
-- Table structure for `auth_assignment`
-- ----------------------------
DROP TABLE IF EXISTS `auth_assignment`;
CREATE TABLE `auth_assignment` (
`item_name`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`user_id`  int(11) NOT NULL ,
`created_at`  int(11) NULL DEFAULT '' ,
`updated_at`  int(11) NULL DEFAULT '' ,
PRIMARY KEY (`item_name`, `user_id`),
FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci

;

-- ----------------------------
-- Table structure for `auth_item`
-- ----------------------------
DROP TABLE IF EXISTS `auth_item`;
CREATE TABLE `auth_item` (
`name`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`type`  int(11) NOT NULL ,
`description`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`rule_name`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`data`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`created_at`  int(11) NULL DEFAULT '' ,
`updated_at`  int(11) NULL DEFAULT '' ,
`acc_type`  int(11) NULL DEFAULT '' ,
PRIMARY KEY (`name`),
FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci

;

-- ----------------------------
-- Table structure for `auth_item_child`
-- ----------------------------
DROP TABLE IF EXISTS `auth_item_child`;
CREATE TABLE `auth_item_child` (
`parent`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`child`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
PRIMARY KEY (`parent`, `child`),
FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci

;

-- ----------------------------
-- Table structure for `auth_rule`
-- ----------------------------
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
`name`  varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`data`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`created_at`  int(11) NULL DEFAULT '' ,
`updated_at`  int(11) NULL DEFAULT '' ,
PRIMARY KEY (`name`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci

;

-- ----------------------------
-- Table structure for `cp_order`
-- ----------------------------
DROP TABLE IF EXISTS `cp_order`;
CREATE TABLE `cp_order` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`status`  int(3) NOT NULL ,
`created_at`  int(11) NOT NULL ,
`updated_at`  int(11) NOT NULL ,
`name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`expired_at`  int(11) NULL DEFAULT '' ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=34

;

-- ----------------------------
-- Table structure for `cp_order_asm`
-- ----------------------------
DROP TABLE IF EXISTS `cp_order_asm`;
CREATE TABLE `cp_order_asm` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`cp_order_id`  int(11) NOT NULL ,
`dealer_id`  int(11) NOT NULL ,
`status`  int(11) NOT NULL ,
`created_at`  int(11) NULL DEFAULT '' ,
`updated_at`  int(11) NULL DEFAULT '' ,
`transaction_time`  int(11) NULL DEFAULT '' ,
PRIMARY KEY (`id`),
FOREIGN KEY (`dealer_id`) REFERENCES `dealer` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
FOREIGN KEY (`cp_order_id`) REFERENCES `cp_order` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=16

;

-- ----------------------------
-- Table structure for `dealer`
-- ----------------------------
DROP TABLE IF EXISTS `dealer`;
CREATE TABLE `dealer` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`name_code`  varchar(12) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`full_name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`phone_number`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`email`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`address`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`status`  int(3) NOT NULL ,
`user_admin_id`  int(11) NULL DEFAULT '' ,
`created_at`  int(11) NULL DEFAULT '' ,
`updated_at`  int(11) NULL DEFAULT '' ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=13

;

-- ----------------------------
-- Table structure for `log_promotion_code`
-- ----------------------------
DROP TABLE IF EXISTS `log_promotion_code`;
CREATE TABLE `log_promotion_code` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`promotion_code_id`  int(11) NULL DEFAULT '' ,
`status`  int(3) NOT NULL ,
`type`  int(3) NOT NULL ,
`des`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`receiver`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`created_at`  int(11) NOT NULL ,
`updated_at`  int(11) NOT NULL ,
`error_code`  int(3) NOT NULL ,
`receiver_info`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`partner_id`  int(11) NULL DEFAULT '' ,
`partner_transaction_id`  int(11) NOT NULL ,
`promotion_code`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`promotion_code_id`) REFERENCES `promotion_code` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=1

;

-- ----------------------------
-- Table structure for `migration`
-- ----------------------------
DROP TABLE IF EXISTS `migration`;
CREATE TABLE `migration` (
`version`  varchar(180) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`apply_time`  int(11) NULL DEFAULT '' ,
PRIMARY KEY (`version`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci

;

-- ----------------------------
-- Table structure for `partner`
-- ----------------------------
DROP TABLE IF EXISTS `partner`;
CREATE TABLE `partner` (
`id`  int(11) NOT NULL AUTO_INCREMENT COMMENT 'Ma doi tac partner khac ma CP' ,
`name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'Ten doi tac khac ma CP' ,
`secret_key`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'Ma bi mat dung de ma hoa code' ,
`status`  int(11) NULL DEFAULT '' COMMENT '0- Dung hoat dong 1- Hoat dong' ,
`created_at`  int(11) NULL DEFAULT '' ,
`updated_at`  int(11) NULL DEFAULT '' ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=10

;

-- ----------------------------
-- Table structure for `partner_attribute_asm`
-- ----------------------------
DROP TABLE IF EXISTS `partner_attribute_asm`;
CREATE TABLE `partner_attribute_asm` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`partner_id`  int(11) NULL DEFAULT '' ,
`attribute_id`  int(11) NULL DEFAULT '' ,
`order`  int(11) NULL DEFAULT '' ,
`status`  int(11) NULL DEFAULT '' ,
`created_at`  int(11) NULL DEFAULT '' ,
`updated_at`  int(11) NULL DEFAULT '' ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
AUTO_INCREMENT=10

;

-- ----------------------------
-- Table structure for `promotion`
-- ----------------------------
DROP TABLE IF EXISTS `promotion`;
CREATE TABLE `promotion` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`dealer_id`  int(11) NOT NULL ,
`name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`status`  int(3) NOT NULL ,
`cp_order_id`  int(11) NULL DEFAULT '' ,
`total_promotion_code`  bigint(20) NOT NULL ,
`created_at`  int(11) NOT NULL ,
`updated_at`  int(11) NOT NULL ,
`gen_code`  int(11) NULL DEFAULT 0 ,
`active_time`  int(11) NULL DEFAULT '' ,
`expired_time`  int(11) NULL DEFAULT '' ,
`file`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
PRIMARY KEY (`id`),
FOREIGN KEY (`cp_order_id`) REFERENCES `cp_order` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`dealer_id`) REFERENCES `dealer` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=19

;

-- ----------------------------
-- Table structure for `promotion_code`
-- ----------------------------
DROP TABLE IF EXISTS `promotion_code`;
CREATE TABLE `promotion_code` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`code`  varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`expired_at`  int(11) NOT NULL ,
`status`  int(3) NOT NULL COMMENT '0 inactive\n10 active' ,
`created_at`  int(11) NOT NULL ,
`updated_at`  int(11) NOT NULL ,
`receiver`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`promotion_id`  int(11) NOT NULL ,
`receiver_info`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
PRIMARY KEY (`id`),
FOREIGN KEY (`promotion_id`) REFERENCES `promotion` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=535

;

-- ----------------------------
-- Table structure for `user`
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`username`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`full_name`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`phone`  varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`type`  int(3) NOT NULL ,
`status`  int(3) NOT NULL ,
`email`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`name_code`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'Ma dai ly cua doi tac' ,
`auth_key`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`password_hash`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`password_reset_token`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'Dung de reset mat khau qua mail' ,
`access_login_token`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`created_at`  int(11) NULL DEFAULT '' ,
`updated_at`  int(11) NULL DEFAULT '' ,
`dealer_id`  int(11) NULL DEFAULT '' ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=25

;

-- ----------------------------
-- Indexes structure for table `activity_user`
-- ----------------------------
CREATE INDEX `fk_activity_user_user_idx` ON `activity_user`(`user_id`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `activity_user`
-- ----------------------------
ALTER TABLE `activity_user` AUTO_INCREMENT=1;

-- ----------------------------
-- Indexes structure for table `attribute`
-- ----------------------------
CREATE INDEX `name` ON `attribute`(`name`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `attribute`
-- ----------------------------
ALTER TABLE `attribute` AUTO_INCREMENT=16;

-- ----------------------------
-- Indexes structure for table `attribute_value`
-- ----------------------------
CREATE INDEX `fk_name_value_idx` ON `attribute_value`(`attribute_id`) USING BTREE ;
CREATE INDEX `fk_attribute_value_cp_order1_idx` ON `attribute_value`(`cp_order_id`) USING BTREE ;
CREATE INDEX `fk_attribute_value_promotion1_idx` ON `attribute_value`(`promotion_id`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `attribute_value`
-- ----------------------------
ALTER TABLE `attribute_value` AUTO_INCREMENT=172;

-- ----------------------------
-- Indexes structure for table `auth_assignment`
-- ----------------------------
CREATE INDEX `fk_auth_assignment_user1_idx` ON `auth_assignment`(`user_id`) USING BTREE ;

-- ----------------------------
-- Indexes structure for table `auth_item`
-- ----------------------------
CREATE INDEX `rule_name` ON `auth_item`(`rule_name`) USING BTREE ;
CREATE INDEX `idx-auth_item-type` ON `auth_item`(`type`) USING BTREE ;

-- ----------------------------
-- Indexes structure for table `auth_item_child`
-- ----------------------------
CREATE INDEX `child` ON `auth_item_child`(`child`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `cp_order`
-- ----------------------------
ALTER TABLE `cp_order` AUTO_INCREMENT=34;

-- ----------------------------
-- Indexes structure for table `cp_order_asm`
-- ----------------------------
CREATE INDEX `fk-cp-order-asm-order_id` ON `cp_order_asm`(`cp_order_id`) USING BTREE ;
CREATE INDEX `fk-cp-order-asm-dealer-id` ON `cp_order_asm`(`dealer_id`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `cp_order_asm`
-- ----------------------------
ALTER TABLE `cp_order_asm` AUTO_INCREMENT=16;

-- ----------------------------
-- Indexes structure for table `dealer`
-- ----------------------------
CREATE UNIQUE INDEX `name_code` ON `dealer`(`name_code`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `dealer`
-- ----------------------------
ALTER TABLE `dealer` AUTO_INCREMENT=13;

-- ----------------------------
-- Indexes structure for table `log_promotion_code`
-- ----------------------------
CREATE INDEX `fk_log_promotion_code_promotion_code1_idx` ON `log_promotion_code`(`promotion_code_id`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `log_promotion_code`
-- ----------------------------
ALTER TABLE `log_promotion_code` AUTO_INCREMENT=1;

-- ----------------------------
-- Auto increment value for `partner`
-- ----------------------------
ALTER TABLE `partner` AUTO_INCREMENT=10;

-- ----------------------------
-- Auto increment value for `partner_attribute_asm`
-- ----------------------------
ALTER TABLE `partner_attribute_asm` AUTO_INCREMENT=10;

-- ----------------------------
-- Indexes structure for table `promotion`
-- ----------------------------
CREATE INDEX `fk_order_idx` ON `promotion`(`cp_order_id`) USING BTREE ;
CREATE INDEX `fk_cp_idx` ON `promotion`(`dealer_id`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `promotion`
-- ----------------------------
ALTER TABLE `promotion` AUTO_INCREMENT=19;

-- ----------------------------
-- Indexes structure for table `promotion_code`
-- ----------------------------
CREATE UNIQUE INDEX `code_UNIQUE` ON `promotion_code`(`code`) USING BTREE ;
CREATE INDEX `fk_promotion_code_promotion1_idx` ON `promotion_code`(`promotion_id`) USING BTREE ;
CREATE INDEX `code` ON `promotion_code`(`code`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `promotion_code`
-- ----------------------------
ALTER TABLE `promotion_code` AUTO_INCREMENT=535;

-- ----------------------------
-- Indexes structure for table `user`
-- ----------------------------
CREATE UNIQUE INDEX `username_UNIQUE` ON `user`(`username`) USING BTREE ;
CREATE UNIQUE INDEX `email_UNIQUE` ON `user`(`email`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `user`
-- ----------------------------
ALTER TABLE `user` AUTO_INCREMENT=25;
