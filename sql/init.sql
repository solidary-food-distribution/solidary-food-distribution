/*M!999999\- enable the sandbox mode */ 

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `msl_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_access` (
  `user_id` int(10) unsigned NOT NULL,
  `access` varchar(20) NOT NULL,
  `member_id` int(11) NOT NULL,
  `start` date NOT NULL DEFAULT '0000-00-00',
  `end` date NOT NULL DEFAULT '9999-12-31' COMMENT 'incl',
  PRIMARY KEY (`user_id`,`access`,`member_id`) USING BTREE,
  KEY `access` (`access`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_brands` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned NOT NULL DEFAULT 35,
  `bnn` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bnn` (`supplier_id`,`bnn`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=284 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_crons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_crons` (
  `cron_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `minutes_interval` int(10) unsigned NOT NULL DEFAULT 0,
  `last_run` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `next_run` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `task` varchar(255) NOT NULL,
  PRIMARY KEY (`cron_id`),
  KEY `next_run` (`next_run`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_debits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_debits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `member_id` int(10) unsigned NOT NULL,
  `pickup_id` int(10) unsigned NOT NULL DEFAULT 0,
  `tax` decimal(4,2) NOT NULL DEFAULT 0.00,
  `amount` decimal(7,2) NOT NULL DEFAULT 0.00 COMMENT 'incl tax',
  `due_date` date NOT NULL DEFAULT '0000-00-00',
  `status` char(1) NOT NULL DEFAULT 'o' COMMENT 'o(pen), e(xported), p(aid)',
  `exported` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `pickup_id` (`pickup_id`),
  KEY `status` (`status`),
  KEY `due_date` (`due_date`)
) ENGINE=InnoDB AUTO_INCREMENT=386 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_deliveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_deliveries` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned NOT NULL COMMENT 'member_id',
  `purchase_id` int(10) unsigned NOT NULL DEFAULT 0,
  `purchase_total` decimal(7,2) NOT NULL DEFAULT 0.00,
  `created` datetime NOT NULL,
  `creator_id` int(10) unsigned NOT NULL COMMENT 'user_id',
  `status` char(1) NOT NULL DEFAULT 'o' COMMENT 'o(pen), a(uto close), c(losed)',
  `preferences_mail_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `purchase_id` (`purchase_id`),
  KEY `supplier_id` (`supplier_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_delivery_dates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_delivery_dates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`),
  KEY `datetime` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_delivery_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_delivery_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `delivery_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `amount_pieces` int(10) unsigned NOT NULL DEFAULT 0,
  `amount_bundles` int(10) unsigned NOT NULL DEFAULT 0,
  `amount_weight` decimal(10,3) NOT NULL DEFAULT 0.000,
  `price_type` char(1) NOT NULL DEFAULT '' COMMENT 'p(iece), k(g)',
  `purchase` decimal(7,3) NOT NULL DEFAULT 0.000,
  `purchase_sum` decimal(7,3) NOT NULL DEFAULT 0.000,
  `dividable` decimal(4,2) NOT NULL DEFAULT 1.00 COMMENT '1 (yes), 0.5 (halves), 0.25 (quarters), 0 (no / unknown)',
  `best_before` date NOT NULL DEFAULT '0000-00-00',
  `weight_min` decimal(5,2) NOT NULL DEFAULT 0.00,
  `weight_max` decimal(5,2) NOT NULL DEFAULT 0.00,
  `weight_avg` decimal(5,2) NOT NULL DEFAULT 0.00,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifier_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'user_id',
  PRIMARY KEY (`id`),
  KEY `delivery_id` (`delivery_id`),
  KEY `product_id` (`product_id`),
  KEY `modified` (`modified`)
) ENGINE=InnoDB AUTO_INCREMENT=1480 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_favorites` (
  `member_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`member_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_info_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_info_users` (
  `info_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `read` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`info_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_infos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_infos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `subject` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL DEFAULT '',
  `published` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_inventory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp(),
  `product_id` int(10) unsigned NOT NULL,
  `delivery_item_id` int(10) unsigned NOT NULL DEFAULT 0,
  `pickup_item_id` int(10) unsigned NOT NULL DEFAULT 0,
  `order_item_id` int(10) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `amount_pieces` decimal(6,2) NOT NULL DEFAULT 0.00,
  `amount_weight` decimal(10,3) NOT NULL DEFAULT 0.000,
  `dividable` decimal(4,2) NOT NULL DEFAULT 1.00 COMMENT '1 (yes), 0.5 (halves), 0.25 (quarters), 0 (no / unknown)',
  `weight_min` decimal(5,2) NOT NULL DEFAULT 0.00,
  `weight_max` decimal(5,2) NOT NULL DEFAULT 0.00,
  `weight_avg` decimal(5,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`product_id`,`delivery_item_id`,`pickup_item_id`,`order_item_id`,`user_id`) USING BTREE,
  KEY `product_id` (`product_id`),
  KEY `delivery_item_id` (`delivery_item_id`),
  KEY `pickup_item_id` (`pickup_item_id`),
  KEY `user_id` (`user_id`),
  KEY `order_item_id` (`order_item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7567 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_inventory_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_inventory_log` (
  `id` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp(),
  `product_id` int(10) unsigned NOT NULL,
  `delivery_item_id` int(10) unsigned NOT NULL DEFAULT 0,
  `pickup_item_id` int(10) unsigned NOT NULL DEFAULT 0,
  `order_item_id` int(10) unsigned NOT NULL DEFAULT 0,
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `amount_pieces` decimal(6,2) NOT NULL DEFAULT 0.00,
  `amount_weight` decimal(10,3) NOT NULL DEFAULT 0.000,
  `dividable` decimal(4,2) NOT NULL DEFAULT 1.00 COMMENT '1 (yes), 0.5 (halves), 0.25 (quarters), 0 (no / unknown)',
  `weight_min` decimal(5,2) NOT NULL DEFAULT 0.00,
  `weight_max` decimal(5,2) NOT NULL DEFAULT 0.00,
  `weight_avg` decimal(5,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `delivery_item_id` (`delivery_item_id`),
  KEY `pickup_item_id` (`pickup_item_id`),
  KEY `user_id` (`user_id`),
  KEY `order_item_id` (`order_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_mail_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_mail_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `mail_id` int(10) unsigned NOT NULL,
  `answer_id` tinyint(3) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `mail_id` (`mail_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_mails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_mails` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `to` varchar(255) NOT NULL DEFAULT '''''',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL DEFAULT '',
  `sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_members` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp(),
  `status` char(1) NOT NULL DEFAULT 'a' COMMENT 'a(ctive), c(ancelled), i(nactive), d(eleted)',
  `deactivate_on` date NOT NULL DEFAULT '0000-00-00' COMMENT 'set status to inactive',
  `name` varchar(50) NOT NULL,
  `identification` varchar(255) NOT NULL DEFAULT '',
  `producer` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1: farm 2: trader',
  `consumer` tinyint(1) NOT NULL DEFAULT 1,
  `pate_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'test membership owner',
  `order_limit` decimal(6,2) NOT NULL DEFAULT 0.00 COMMENT 'test membership limit per order',
  `purchase_time` varchar(50) NOT NULL DEFAULT '' COMMENT 'relative to pickup date',
  `purchase_name` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_order_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp(),
  `replaces_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'for changes in delivery and alike',
  `order_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `amount_pieces` decimal(6,2) unsigned NOT NULL DEFAULT 0.00,
  `amount_weight` decimal(6,2) NOT NULL DEFAULT 0.00,
  `price_type` char(1) NOT NULL DEFAULT '' COMMENT 'p(iece), k(g)',
  `price` decimal(6,2) NOT NULL DEFAULT 0.00,
  `amount_per_bundle` decimal(5,2) NOT NULL DEFAULT 0.00,
  `price_bundle` decimal(7,2) NOT NULL DEFAULT 0.00,
  `price_sum` decimal(6,2) NOT NULL DEFAULT 0.00,
  `comment` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `pickup_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4856 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp(),
  `member_id` int(10) unsigned NOT NULL,
  `pickup_date` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=599 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_pickup_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_pickup_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pickup_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `order_item_id` int(10) unsigned NOT NULL DEFAULT 0,
  `delivery_item_id` int(10) unsigned NOT NULL DEFAULT 0,
  `amount_pieces_min` decimal(6,2) unsigned NOT NULL DEFAULT 0.00,
  `amount_pieces_max` decimal(6,2) unsigned NOT NULL DEFAULT 999.99,
  `amount_pieces` decimal(6,2) unsigned NOT NULL DEFAULT 0.00,
  `amount_weight_min` decimal(8,3) unsigned NOT NULL DEFAULT 0.000,
  `amount_weight_max` decimal(8,3) unsigned NOT NULL DEFAULT 9999.999,
  `amount_weight` decimal(8,3) unsigned NOT NULL DEFAULT 0.000,
  `price_type` char(1) NOT NULL DEFAULT '' COMMENT 'p(iece), k(g)',
  `price` decimal(6,2) NOT NULL DEFAULT 0.00,
  `amount_per_bundle` decimal(7,2) NOT NULL DEFAULT 0.00,
  `price_bundle` decimal(6,2) NOT NULL DEFAULT 0.00,
  `price_sum` decimal(6,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(5,2) NOT NULL DEFAULT 0.00,
  `best_before` date NOT NULL DEFAULT '0000-00-00',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifier_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'user_id',
  `preference_value` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `delivery_id` (`delivery_item_id`),
  KEY `product_id` (`product_id`),
  KEY `pickup_id` (`pickup_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3987 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_pickups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_pickups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `price_total` decimal(7,2) NOT NULL DEFAULT 0.00,
  `status` char(1) NOT NULL DEFAULT 'o' COMMENT 'o(pen), a(uto close), c(losed), p(aid)',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=389 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_poll_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_poll_answers` (
  `poll_answer_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poll_id` int(10) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp(),
  `answer` varchar(255) NOT NULL DEFAULT '',
  `ordering` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`poll_answer_id`),
  KEY `poll_id` (`poll_id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_poll_votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_poll_votes` (
  `poll_answer_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `value` tinyint(2) NOT NULL DEFAULT 1,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'user_id',
  `updated` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`poll_answer_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_polls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_polls` (
  `poll_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `title` varchar(255) NOT NULL,
  `text` text NOT NULL DEFAULT '',
  `type` char(1) NOT NULL COMMENT 'm(ulti text answers)',
  `data` varchar(255) NOT NULL,
  `has_votes` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `mandatory` tinyint(1) NOT NULL DEFAULT 0,
  `reminded` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `close_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`poll_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_preferences` (
  `member_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `value` tinyint(3) unsigned NOT NULL,
  `modifier_id` int(10) unsigned NOT NULL COMMENT 'user_id',
  `modified` datetime NOT NULL,
  PRIMARY KEY (`member_id`,`product_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_prices` (
  `product_id` int(10) unsigned NOT NULL COMMENT 'msl_products.id',
  `start` date NOT NULL DEFAULT '0000-00-00',
  `end` date NOT NULL COMMENT 'incl',
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp(),
  `price` decimal(7,2) unsigned NOT NULL DEFAULT 0.00 COMMENT 'selling price incl tax',
  `amount_per_bundle` decimal(5,2) unsigned NOT NULL DEFAULT 0.00,
  `price_bundle` decimal(7,2) unsigned NOT NULL DEFAULT 0.00,
  `tax` decimal(5,2) unsigned NOT NULL,
  `purchase` decimal(7,3) unsigned NOT NULL COMMENT 'buying price excl tax',
  `purchase_promo` tinyint(1) NOT NULL DEFAULT 0,
  `purchase_bulk1_amount` smallint(5) unsigned NOT NULL DEFAULT 0,
  `purchase_bulk1` decimal(7,3) unsigned NOT NULL DEFAULT 0.000,
  `purchase_bulk2_amount` smallint(5) unsigned NOT NULL DEFAULT 0,
  `purchase_bulk2` decimal(7,3) unsigned NOT NULL DEFAULT 0.000,
  `suggested_retail` decimal(7,2) NOT NULL DEFAULT 0.00,
  `price_new` decimal(7,2) NOT NULL DEFAULT 0.00,
  `price_bundle_new` decimal(7,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`product_id`,`start`,`purchase_promo`) USING BTREE,
  KEY `end` (`end`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_product_imports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_product_imports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `upload_id` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `row_nr` int(10) unsigned NOT NULL DEFAULT 0,
  `info` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT '',
  `purchase` decimal(5,2) NOT NULL DEFAULT 0.00,
  `brand` varchar(255) NOT NULL DEFAULT '',
  `product_id` int(10) NOT NULL DEFAULT 0,
  `status` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `upload_id` (`upload_id`)
) ENGINE=MyISAM AUTO_INCREMENT=414 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp(),
  `name` varchar(50) NOT NULL DEFAULT '',
  `supplier_name` varchar(50) NOT NULL DEFAULT '',
  `supplier_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'msl_member.id',
  `type` char(1) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'p(iece), k(g), w(eight per piece), b(udget)',
  `kg_per_piece` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'for type w',
  `amount_steps` decimal(5,2) NOT NULL DEFAULT 1.00,
  `amount_min` decimal(6,2) NOT NULL DEFAULT 0.00,
  `amount_max` decimal(6,2) NOT NULL DEFAULT 999.00,
  `status` char(1) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'o' COMMENT 'o(nline), e(nding), s(earchable), n(ot active), d(eleted)',
  `stock` char(1) NOT NULL DEFAULT 'n' COMMENT 'n: no stock\r\no: can be reordered\r\ni: only inventory can be used',
  `amount_per_bundle` decimal(5,2) unsigned NOT NULL DEFAULT 1.00,
  `category` varchar(50) NOT NULL DEFAULT '',
  `supplier_product_id` varchar(50) NOT NULL DEFAULT '',
  `brand_id` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT 'msl_brands.id',
  `gtin_piece` varchar(14) NOT NULL DEFAULT '',
  `gtin_bundle` varchar(14) NOT NULL DEFAULT '',
  `import_status` char(1) NOT NULL DEFAULT '' COMMENT 'n(ot imported)',
  `infos` varchar(1000) NOT NULL DEFAULT '' COMMENT 'JSON with date,image,link...',
  PRIMARY KEY (`id`),
  UNIQUE KEY `supplier_id` (`supplier_id`,`supplier_product_id`),
  KEY `brand_id` (`brand_id`),
  KEY `supplier_name` (`supplier_name`)
) ENGINE=MyISAM AUTO_INCREMENT=56739 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_purchase_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_purchase_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT '2000-01-01 00:00:00',
  `purchase_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `amount_pieces` int(10) unsigned NOT NULL DEFAULT 0,
  `amount_bundles` int(10) unsigned NOT NULL DEFAULT 0,
  `amount_weight` decimal(10,3) NOT NULL DEFAULT 0.000,
  `price_type` char(1) NOT NULL DEFAULT '' COMMENT 'p(iece), k(g)',
  `purchase` decimal(7,3) NOT NULL DEFAULT 0.000,
  `purchase_sum` decimal(7,3) NOT NULL DEFAULT 0.000,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modifier_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'user_id',
  PRIMARY KEY (`id`),
  KEY `purchase_id` (`purchase_id`),
  KEY `product_id` (`product_id`),
  KEY `modified` (`modified`)
) ENGINE=InnoDB AUTO_INCREMENT=1342 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_purchases` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `delivery_date_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'msl_delivery_dates.id',
  `supplier_id` int(10) unsigned NOT NULL DEFAULT 0,
  `status` char(1) NOT NULL DEFAULT 'n' COMMENT 'a(ctive) n(ot active)',
  `datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `content` text NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`delivery_date_id`,`supplier_id`) USING BTREE,
  KEY `supplier_id` (`supplier_id`),
  KEY `sent` (`sent`),
  KEY `datetime` (`datetime`),
  KEY `delivery_date_id` (`delivery_date_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19230 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `updated` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique` (`setting`,`user_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_task_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_task_users` (
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assign` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT 'percentage',
  `start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`task_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_tasks` (
  `task_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `type` char(1) NOT NULL COMMENT 'a(ssign)\r\ne(vent)\r\n',
  `title` varchar(255) NOT NULL,
  `description` varchar(2000) NOT NULL COMMENT 'html',
  `interval` varchar(3) NOT NULL COMMENT 'u(nique)\r\ne(vent)\r\nd(aily)\r\ndXX every XX days\r\nNX weekday 1=Mon, 7=Sun\r\nmXX monthly on XX\r\n',
  `effort` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT 'minutes approx.',
  `starts` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sort` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`task_id`),
  KEY `starts` (`starts`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_timesheet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_timesheet` (
  `user_id` int(10) unsigned NOT NULL,
  `id` bigint(20) unsigned NOT NULL,
  `date` date NOT NULL,
  `mins` smallint(11) unsigned NOT NULL,
  `km` smallint(5) unsigned NOT NULL DEFAULT 0,
  `topic` char(2) NOT NULL DEFAULT '',
  `what` varchar(1000) NOT NULL,
  `modified` bigint(20) NOT NULL,
  PRIMARY KEY (`user_id`,`id`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_uploads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_uploads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `email` varchar(255) NOT NULL,
  `passwd` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `passwd_tmp` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'password recovery 	',
  `passwd_sent` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `pickup_pin` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `member_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'primary member_id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`) USING BTREE,
  KEY `pickup_pin` (`pickup_pin`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_var`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_var` (
  `var` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`var`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `msl_wg_oeko`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `msl_wg_oeko` (
  `wg_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `wg_nr` int(11) NOT NULL,
  `wg_name` varchar(50) NOT NULL,
  `wg_suche` varchar(255) NOT NULL,
  `wg_rang` int(11) NOT NULL,
  `wg_filter` varchar(50) NOT NULL,
  `wg_ersatz` int(11) NOT NULL,
  `wg_faktor` double(3,2) NOT NULL,
  PRIMARY KEY (`wg_id`),
  UNIQUE KEY `wg_nr_unique` (`wg_nr`),
  KEY `wg_nr` (`wg_nr`,`wg_name`,`wg_suche`,`wg_rang`),
  KEY `wg_filter` (`wg_filter`),
  KEY `wg_ersatz` (`wg_ersatz`),
  KEY `wg_faktor` (`wg_faktor`)
) ENGINE=MyISAM AUTO_INCREMENT=97 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

