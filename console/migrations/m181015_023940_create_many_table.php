<?php

use yii\db\Migration;

/**
 * Handles the creation of table `many`.
 */
class m181015_023940_create_many_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $sql = "SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `access_system`
-- ----------------------------
DROP TABLE IF EXISTS `access_system`;
CREATE TABLE `access_system` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`subscriber_id`  int(11) NULL DEFAULT NULL ,
`ip_address`  varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`user_agent`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`site_id`  int(11) NULL DEFAULT NULL ,
`access_date`  int(11) NOT NULL ,
`created_at`  int(11) NULL DEFAULT NULL ,
`updated_at`  int(11) NULL DEFAULT NULL ,
`action`  varchar(126) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`request_detail`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`request_params`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
COMMENT='Thống kê lượt truy cập vào hệ thống'
AUTO_INCREMENT=1

;

-- ----------------------------
-- Table structure for `category`
-- ----------------------------
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`display_name`  varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`ascii_name`  varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`type`  smallint(6) NOT NULL DEFAULT 1 COMMENT 'type tuong ung voi cac loai content:\n1 - video\n2 - live\n3 - music\n4 - news\n' ,
`description`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  int(11) NOT NULL DEFAULT 1 COMMENT '10 - active\n0 - inactive\n3 - for test only' ,
`order_number`  int(11) NOT NULL DEFAULT 0 COMMENT 'dung de sap xep category theo thu tu xac dinh, order chi dc so sanh khi cac category co cung level' ,
`parent_id`  int(11) NULL DEFAULT NULL ,
`path`  varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'chua duong dan tu root den node nay trong category tree, vi du: 1/3/18/4, voi 4 la id cua category hien tai' ,
`level`  int(11) NULL DEFAULT NULL COMMENT '0 - root\n1 - category cap 2\n2 - category cap 3\n...' ,
`child_count`  int(11) NULL DEFAULT NULL ,
`images`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`created_at`  int(11) NULL DEFAULT NULL ,
`updated_at`  int(11) NULL DEFAULT NULL ,
`admin_note`  varchar(4000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`show_on_portal`  smallint(1) NULL DEFAULT 1 ,
`show_on_client`  smallint(1) NULL DEFAULT 1 ,
`is_content_service`  int(1) NULL DEFAULT 0 ,
`tvod1_id`  int(11) NULL DEFAULT NULL ,
`is_series`  int(11) NULL DEFAULT 0 ,
`is_live`  int(11) NULL DEFAULT 0 ,
`group_category_id`  int(11) NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`parent_id`) REFERENCES `category` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=1

;

-- ----------------------------
-- Table structure for `city`
-- ----------------------------
DROP TABLE IF EXISTS `city`;
CREATE TABLE `city` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`site_id`  int(11) NOT NULL DEFAULT 5 COMMENT 'Nhà cung cấp dịch vụ' ,
`name`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Tỉnh/thành phố' ,
`code`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Mã Tỉnh/thành phố' ,
`ascii_name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
COMMENT='Bảng tỉnh/thành phố theo nhà cung cấp dịch vụ'
AUTO_INCREMENT=1

;

-- ----------------------------
-- Table structure for `subscriber`
-- ----------------------------
DROP TABLE IF EXISTS `subscriber`;
CREATE TABLE `subscriber` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`site_id`  int(10) NOT NULL ,
`dealer_id`  int(11) NULL DEFAULT NULL ,
`msisdn`  varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`authen_type`  smallint(6) NOT NULL DEFAULT 1 COMMENT '1 - username(sdt)/pass\n2 - auto MAC login' ,
`channel`  int(11) NULL DEFAULT 7 ,
`username`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'ban dau de mac dinh la so dien thoai' ,
`machine_name`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`balance`  int(11) NOT NULL DEFAULT 0 COMMENT 'so du tien ao' ,
`status`  int(11) NOT NULL DEFAULT 1 COMMENT '10 - active' ,
`email`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`full_name`  varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`auth_key`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`password_hash`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`last_login_at`  int(11) NULL DEFAULT NULL ,
`last_login_session`  int(11) NULL DEFAULT NULL ,
`birthday`  int(11) NULL DEFAULT NULL ,
`sex`  tinyint(1) NULL DEFAULT NULL COMMENT '1 - male, 0 - female' ,
`avatar_url`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`skype_id`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`google_id`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`facebook_id`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`created_at`  int(11) NULL DEFAULT NULL ,
`updated_at`  int(11) NULL DEFAULT NULL ,
`client_type`  int(11) NULL DEFAULT NULL COMMENT '1 - wap, \n2 - android, \n3 - iOS\n4 - wp' ,
`using_promotion`  int(11) NULL DEFAULT 0 ,
`auto_renew`  tinyint(1) NULL DEFAULT 1 ,
`verification_code`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`user_agent`  varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`expired_at`  int(11) NULL DEFAULT NULL ,
`address`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`city`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`otp_code`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`expired_code_time`  int(11) NULL DEFAULT NULL ,
`number_otp`  int(11) NULL DEFAULT 3 ,
`whitelist`  int(11) NULL DEFAULT NULL ,
`register_at`  int(11) NULL DEFAULT NULL ,
`is_active`  int(11) NULL DEFAULT NULL ,
`ip_address`  varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`itvod_type`  int(11) NULL DEFAULT NULL ,
`pass_code`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`number_pass_code`  int(11) NULL DEFAULT NULL ,
`expired_pass_code`  int(11) NULL DEFAULT NULL ,
`ip_to_location`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`province_code`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' ,
`type`  int(11) NOT NULL DEFAULT 1 ,
`initialized_at`  int(11) NOT NULL DEFAULT 0 ,
`service_initialized`  int(11) NOT NULL DEFAULT 0 ,
`phone_number`  varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`ip_location_first`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`dealer_id`) REFERENCES `dealer` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=1

;

-- ----------------------------
-- Table structure for `subscriber_activity`
-- ----------------------------
DROP TABLE IF EXISTS `subscriber_activity`;
CREATE TABLE `subscriber_activity` (
`id`  bigint(20) NOT NULL AUTO_INCREMENT ,
`subscriber_id`  int(11) NOT NULL ,
`msisdn`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`action`  int(10) NULL DEFAULT NULL COMMENT '1 - login\n2 - logout\n3 - xem\n4 - download\n5 - gift\n6 - mua service\n7 - chu dong huy service\n8 - bi provider huy service\n9 - gia han service\n...' ,
`params`  mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`created_at`  int(11) NULL DEFAULT NULL ,
`ip_address`  varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  smallint(6) NOT NULL DEFAULT 10 COMMENT '10 - success\n0 - fail' ,
`target_id`  int(11) NULL DEFAULT NULL ,
`target_type`  smallint(6) NULL DEFAULT NULL ,
`type`  int(11) NULL DEFAULT NULL ,
`description`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`user_agent`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`channel`  smallint(6) NULL DEFAULT NULL COMMENT 'sms, wap, web, android app, ios app...' ,
`site_id`  int(10) NOT NULL ,
`device_id`  int(11) NULL DEFAULT NULL ,
`type_subscriber`  int(3) NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
COMMENT='bang log nay se lon rat nhanh'
AUTO_INCREMENT=1

;

-- ----------------------------
-- Table structure for `subscriber_favorite`
-- ----------------------------
DROP TABLE IF EXISTS `subscriber_favorite`;
CREATE TABLE `subscriber_favorite` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`subscriber_id`  int(11) NOT NULL ,
`content_id`  int(11) NOT NULL ,
`created_at`  int(11) NULL DEFAULT NULL ,
`updated_at`  int(11) NULL DEFAULT NULL ,
`site_id`  int(10) NOT NULL ,
`type`  smallint(6) NOT NULL DEFAULT 1 COMMENT '1: video, 2: live, 3: music, 4:news, 5: clips, 6:karaoke, 7:radio, 8: live_content' ,
PRIMARY KEY (`id`),
FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=1

;

-- ----------------------------
-- Table structure for `subscriber_feedback`
-- ----------------------------
DROP TABLE IF EXISTS `subscriber_feedback`;
CREATE TABLE `subscriber_feedback` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`subscriber_id`  int(11) NOT NULL ,
`content`  varchar(5000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`title`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`create_date`  int(11) NULL DEFAULT NULL ,
`status`  int(11) NOT NULL ,
`status_log`  mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`is_responsed`  tinyint(1) NOT NULL DEFAULT 0 ,
`response_date`  datetime NULL DEFAULT NULL ,
`response_user_id`  bigint(11) NULL DEFAULT NULL ,
`response_detail`  varchar(5000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`site_id`  int(10) NOT NULL ,
`content_id`  int(11) NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
AUTO_INCREMENT=1

;

-- ----------------------------
-- Table structure for `subscriber_token`
-- ----------------------------
DROP TABLE IF EXISTS `subscriber_token`;
CREATE TABLE `subscriber_token` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`subscriber_id`  int(11) NOT NULL ,
`package_name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`msisdn`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`token`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`type`  smallint(6) NOT NULL DEFAULT 1 COMMENT '1 - wifi password\n2 - access token\n' ,
`ip_address`  varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`created_at`  int(11) NULL DEFAULT NULL ,
`expired_at`  int(11) NULL DEFAULT NULL ,
`cookies`  varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`status`  int(11) NOT NULL DEFAULT 1 ,
`channel`  smallint(6) NULL DEFAULT NULL ,
`device_name`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`device_model`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`device_id`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
COMMENT='wifi password hoac access token khi dang nhap vao client'
AUTO_INCREMENT=1

;

-- ----------------------------
-- Table structure for `subscriber_transaction`
-- ----------------------------
DROP TABLE IF EXISTS `subscriber_transaction`;
CREATE TABLE `subscriber_transaction` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`subscriber_id`  int(11) NOT NULL ,
`msisdn`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`payment_type`  smallint(6) NOT NULL DEFAULT 1 COMMENT '1 - thanh toan tra truoc = tien ao\n2 - thanh toan tra truoc = sms\n3 - nap tiep/thanh toan tra sau' ,
`type`  smallint(6) NULL DEFAULT NULL COMMENT '1 : mua moi\n2 : gia han\n3 : subscriber chu dong huy\n4 : bi provider huy, \n5: pending, \n6: restore\n7 : mua dich vu\n8 : mua de xem\n9 : mua de download\n10 : mua de tang\n11: tang goi cuoc\n100: nap tien qua sms' ,
`service_id`  int(11) NULL DEFAULT NULL ,
`content_id`  int(11) NULL DEFAULT NULL ,
`transaction_time`  int(11) NULL DEFAULT NULL ,
`created_at`  int(11) NULL DEFAULT NULL ,
`updated_at`  int(11) NULL DEFAULT NULL ,
`status`  int(2) NOT NULL COMMENT '10 : success\n0 : fail\n' ,
`shortcode`  varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'đầu số nhắn tin' ,
`description`  varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`cost`  double NULL DEFAULT NULL ,
`channel`  smallint(6) NULL DEFAULT NULL COMMENT 'Kenh thuc hien giao dich: WAP, SMS' ,
`event_id`  varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Ma phan biet cac nhom noi dung...' ,
`error_code`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`subscriber_activity_id`  bigint(20) NULL DEFAULT NULL ,
`subscriber_service_asm_id`  int(11) NULL DEFAULT NULL ,
`site_id`  int(10) NOT NULL ,
`dealer_id`  int(11) NULL DEFAULT NULL ,
`application`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`balance`  double NULL DEFAULT NULL ,
`currency`  varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`card_serial`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`card_code`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`transaction_voucher_id`  int(11) NULL DEFAULT NULL ,
`white_list`  int(11) NULL DEFAULT NULL ,
`cp_id`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`order_id`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`expired_time`  int(11) NULL DEFAULT NULL ,
`smartgate_transaction_id`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`smartgate_transaction_timeout`  int(11) NULL DEFAULT NULL ,
`balance_before_charge`  double NULL DEFAULT NULL ,
`gateway`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
`number_month`  int(11) NULL DEFAULT NULL ,
`is_first_package`  int(11) NULL DEFAULT NULL ,
PRIMARY KEY (`id`),
FOREIGN KEY (`dealer_id`) REFERENCES `dealer` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`site_id`) REFERENCES `site` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`subscriber_id`) REFERENCES `subscriber` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`subscriber_activity_id`) REFERENCES `subscriber_activity` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
FOREIGN KEY (`content_id`) REFERENCES `content` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
COMMENT='luu lai toan bo transaction cua subscriber'
AUTO_INCREMENT=1

;

-- ----------------------------
-- Indexes structure for table `access_system`
-- ----------------------------
CREATE INDEX `fk_access_system_access_date_idx` ON `access_system`(`access_date`) USING BTREE ;
CREATE INDEX `fk_access_system_subscriber_id_idx` ON `access_system`(`subscriber_id`) USING BTREE ;
CREATE INDEX `fk_access_system_site_id_idx` ON `access_system`(`site_id`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `access_system`
-- ----------------------------
ALTER TABLE `access_system` AUTO_INCREMENT=1;

-- ----------------------------
-- Indexes structure for table `category`
-- ----------------------------
CREATE INDEX `fk_vod_category_vod_category_idx` ON `category`(`parent_id`) USING BTREE ;
CREATE INDEX `idx_name` ON `category`(`display_name`) USING BTREE ;
CREATE INDEX `idx_name_ascii` ON `category`(`ascii_name`) USING BTREE ;
CREATE INDEX `idx_desc` ON `category`(`description`(255)) USING BTREE ;
CREATE INDEX `idx_order_no` ON `category`(`order_number`) USING BTREE ;
CREATE INDEX `idx_parent_id` ON `category`(`parent_id`) USING BTREE ;
CREATE INDEX `idx_path` ON `category`(`path`) USING BTREE ;
CREATE INDEX `idx_level` ON `category`(`level`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `category`
-- ----------------------------
ALTER TABLE `category` AUTO_INCREMENT=1;

-- ----------------------------
-- Auto increment value for `city`
-- ----------------------------
ALTER TABLE `city` AUTO_INCREMENT=1;

-- ----------------------------
-- Indexes structure for table `subscriber`
-- ----------------------------
CREATE UNIQUE INDEX `username_UNIQUE` ON `subscriber`(`username`) USING BTREE ;
CREATE INDEX `fk_subscriber_subscriber_session1_idx` ON `subscriber`(`last_login_session`) USING BTREE ;
CREATE INDEX `email` ON `subscriber`(`email`) USING BTREE ;
CREATE INDEX `fk_subscriber_service_provider1_idx` ON `subscriber`(`site_id`) USING BTREE ;
CREATE INDEX `idx_msisdn` ON `subscriber`(`msisdn`) USING BTREE ;
CREATE INDEX `fk_subscriber_dealer_idx` ON `subscriber`(`dealer_id`) USING BTREE ;
CREATE INDEX `idx_subscriber_machine_name` ON `subscriber`(`machine_name`) USING BTREE ;
CREATE INDEX `idx_subscriber_authen_type` ON `subscriber`(`authen_type`) USING BTREE ;
CREATE INDEX `idx_subscriber_status` ON `subscriber`(`status`) USING BTREE ;
CREATE INDEX `idx_subscriber_register_at` ON `subscriber`(`register_at`) USING BTREE ;
CREATE INDEX `idx_subscriber_updated_at` ON `subscriber`(`updated_at`) USING BTREE ;
CREATE INDEX `idx_subscriber_ip_to_location` ON `subscriber`(`ip_to_location`) USING BTREE ;
CREATE INDEX `idx_subscriber_ip_address` ON `subscriber`(`ip_address`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `subscriber`
-- ----------------------------
ALTER TABLE `subscriber` AUTO_INCREMENT=46823;

-- ----------------------------
-- Indexes structure for table `subscriber_activity`
-- ----------------------------
CREATE INDEX `fk_subscriber_activity_log_subscriber1` ON `subscriber_activity`(`subscriber_id`) USING BTREE ;
CREATE INDEX `client_ip` ON `subscriber_activity`(`ip_address`) USING BTREE ;
CREATE INDEX `fk_subscriber_activity_service_provider1_idx` ON `subscriber_activity`(`site_id`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `subscriber_activity`
-- ----------------------------
ALTER TABLE `subscriber_activity` AUTO_INCREMENT=1;

-- ----------------------------
-- Indexes structure for table `subscriber_favorite`
-- ----------------------------
CREATE INDEX `fk_vod_subscriber_favorite_subscriber1` ON `subscriber_favorite`(`subscriber_id`) USING BTREE ;
CREATE INDEX `fk_vod_subscriber_favorite_vod_asset1` ON `subscriber_favorite`(`content_id`) USING BTREE ;
CREATE INDEX `idx_create_date` ON `subscriber_favorite`(`created_at`) USING BTREE ;
CREATE INDEX `fk_subscriber_favorite_service_provider1_idx` ON `subscriber_favorite`(`site_id`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `subscriber_favorite`
-- ----------------------------
ALTER TABLE `subscriber_favorite` AUTO_INCREMENT=1;

-- ----------------------------
-- Indexes structure for table `subscriber_feedback`
-- ----------------------------
CREATE INDEX `fk_subscriber_feedback_subscriber1` ON `subscriber_feedback`(`subscriber_id`) USING BTREE ;
CREATE INDEX `idx_create_date` ON `subscriber_feedback`(`create_date`) USING BTREE ;
CREATE INDEX `idx_is_responsed` ON `subscriber_feedback`(`is_responsed`) USING BTREE ;
CREATE INDEX `idx_response_date` ON `subscriber_feedback`(`response_date`) USING BTREE ;
CREATE INDEX `fk_subscriber_feedback_service_provider1_idx` ON `subscriber_feedback`(`site_id`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `subscriber_feedback`
-- ----------------------------
ALTER TABLE `subscriber_feedback` AUTO_INCREMENT=1;

-- ----------------------------
-- Indexes structure for table `subscriber_token`
-- ----------------------------
CREATE INDEX `fk_subscriber_session_subscriber1` ON `subscriber_token`(`subscriber_id`) USING BTREE ;
CREATE INDEX `idx_session_id` ON `subscriber_token`(`token`) USING BTREE ;
CREATE INDEX `idx_is_active` ON `subscriber_token`(`status`) USING BTREE ;
CREATE INDEX `idx_create_time` ON `subscriber_token`(`created_at`) USING BTREE ;
CREATE INDEX `idx_expire_time` ON `subscriber_token`(`expired_at`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `subscriber_token`
-- ----------------------------
ALTER TABLE `subscriber_token` AUTO_INCREMENT=1;

-- ----------------------------
-- Indexes structure for table `subscriber_transaction`
-- ----------------------------
CREATE INDEX `fk_subscriber_transaction_service1` ON `subscriber_transaction`(`service_id`) USING BTREE ;
CREATE INDEX `fk_subscriber_transaction_vod_asset1` ON `subscriber_transaction`(`content_id`) USING BTREE ;
CREATE INDEX `fk_subscriber_transaction_subscriber1` ON `subscriber_transaction`(`subscriber_id`) USING BTREE ;
CREATE INDEX `idx_create_date` ON `subscriber_transaction`(`created_at`) USING BTREE ;
CREATE INDEX `idx_status` ON `subscriber_transaction`(`status`) USING BTREE ;
CREATE INDEX `idx_purchase_type` ON `subscriber_transaction`(`type`) USING BTREE ;
CREATE INDEX `channel_type` ON `subscriber_transaction`(`channel`) USING BTREE ;
CREATE INDEX `fk_subscriber_transaction_subscriber_activity1_idx` ON `subscriber_transaction`(`subscriber_activity_id`) USING BTREE ;
CREATE INDEX `fk_subscriber_transaction_subscriber_service_asm1_idx` ON `subscriber_transaction`(`subscriber_service_asm_id`) USING BTREE ;
CREATE INDEX `fk_subscriber_transaction_service_provider1_idx` ON `subscriber_transaction`(`site_id`) USING BTREE ;
CREATE INDEX `fk_subscriber_transaction_content_provider1_idx` ON `subscriber_transaction`(`dealer_id`) USING BTREE ;
CREATE INDEX `fk_subscriber_transaction_order_id_idx` ON `subscriber_transaction`(`order_id`) USING BTREE ;

-- ----------------------------
-- Auto increment value for `subscriber_transaction`
-- ----------------------------
ALTER TABLE `subscriber_transaction` AUTO_INCREMENT=1;";

        Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
//        $this->dropTable('many');
    }
}
