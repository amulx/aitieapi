-- MySQL dump 10.14  Distrib 5.5.60-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: aitie
-- ------------------------------------------------------
-- Server version	5.5.60-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tbl_charge`
--

DROP TABLE IF EXISTS `tbl_charge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_charge` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` tinyint(3) unsigned zerofill DEFAULT NULL COMMENT '充值人员id及后台管理员id',
  `gold` tinyint(3) unsigned DEFAULT NULL COMMENT '充值金额',
  `memo` varchar(35) DEFAULT NULL COMMENT '备注',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_collect`
--

DROP TABLE IF EXISTS `tbl_collect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_collect` (
  `collectid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '用户id',
  `topicid` int(11) unsigned DEFAULT NULL COMMENT '爱贴Id',
  `title` varchar(35) DEFAULT NULL COMMENT '文章标题',
  `collect_time` datetime DEFAULT NULL COMMENT '收藏时间',
  PRIMARY KEY (`collectid`),
  KEY `collect_uid_topic_index` (`uid`,`topicid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_consume`
--

DROP TABLE IF EXISTS `tbl_consume`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_consume` (
  `consumeid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '用户id',
  `topicid` int(11) unsigned DEFAULT NULL COMMENT '爱贴id',
  `topic_title` varchar(35) DEFAULT NULL COMMENT '爱贴标题',
  `gold` tinyint(3) unsigned DEFAULT NULL COMMENT '消费的金币数',
  `create_time` datetime DEFAULT NULL COMMENT '消费时间',
  `type` char(5) DEFAULT NULL COMMENT '消费类型',
  `phone` tinyint(1) unsigned DEFAULT '0' COMMENT '1代表已购买',
  `wechat` tinyint(1) unsigned DEFAULT '0' COMMENT '1代表已购买',
  PRIMARY KEY (`consumeid`),
  KEY `consume_index` (`uid`,`topicid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_curd`
--

DROP TABLE IF EXISTS `tbl_curd`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_curd` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) DEFAULT NULL,
  `content` text,
  `state` tinyint(4) DEFAULT NULL,
  `post_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_loginlog`
--

DROP TABLE IF EXISTS `tbl_loginlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_loginlog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '用户id',
  `username` varchar(25) DEFAULT NULL COMMENT '登录用户名',
  `phonenumber` varchar(15) DEFAULT NULL COMMENT '登录手机号',
  `login_time` datetime DEFAULT NULL COMMENT '登录时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_praise`
--

DROP TABLE IF EXISTS `tbl_praise`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_praise` (
  `praiseid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned DEFAULT NULL COMMENT '用户id',
  `topicid` int(11) unsigned DEFAULT NULL COMMENT '爱贴Id',
  `title` varchar(35) DEFAULT NULL COMMENT '文章标题',
  `praise_time` datetime DEFAULT NULL COMMENT '收藏时间',
  PRIMARY KEY (`praiseid`),
  KEY `praise_index` (`uid`,`topicid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_test`
--

DROP TABLE IF EXISTS `tbl_test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) DEFAULT NULL,
  `title` varchar(32) DEFAULT NULL,
  `content` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_topic`
--

DROP TABLE IF EXISTS `tbl_topic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_topic` (
  `topicid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '用户ID',
  `title` varchar(35) DEFAULT NULL COMMENT '标题',
  `location` varchar(35) DEFAULT NULL COMMENT '地址',
  `wechat` varchar(35) DEFAULT NULL COMMENT '微信',
  `wechatgold` tinyint(4) unsigned DEFAULT '0' COMMENT '微信金币',
  `wechatError` tinyint(3) unsigned DEFAULT '0' COMMENT '微信报错',
  `phone` varchar(35) DEFAULT NULL COMMENT '手机号码',
  `phonegold` tinyint(4) unsigned DEFAULT '0' COMMENT '手机金币',
  `phoneError` tinyint(3) unsigned DEFAULT '0' COMMENT '微信报错',
  `content` varchar(310) DEFAULT NULL COMMENT '发帖内容',
  `collectnum` smallint(5) unsigned DEFAULT '0' COMMENT '收藏数',
  `praise` smallint(5) unsigned DEFAULT '0' COMMENT '点赞数',
  `imagelist` varchar(255) DEFAULT NULL COMMENT '图片地址',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`topicid`),
  KEY `topic_uid_index` (`uid`) USING HASH,
  KEY `topic_title_index` (`title`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tbl_user`
--

DROP TABLE IF EXISTS `tbl_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) DEFAULT NULL COMMENT '昵称，唯一性',
  `phonenumber` varchar(15) DEFAULT NULL COMMENT '手机号码',
  `password` varchar(32) DEFAULT NULL COMMENT '密码',
  `userimage` varchar(35) DEFAULT NULL COMMENT '用户头像',
  `wechat` varchar(35) DEFAULT NULL COMMENT '微信号',
  `sex` enum('保密','女','男') DEFAULT '保密' COMMENT '性别',
  `gold` int(11) DEFAULT NULL COMMENT '金币',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_username_index` (`username`),
  KEY `user_login_index` (`phonenumber`,`password`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-10-25 11:12:09
