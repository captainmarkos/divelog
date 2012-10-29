-- MySQL dump 10.13  Distrib 5.1.36, for Win32 (ia32)
--
-- Host: localhost    Database: bluewild
-- ------------------------------------------------------
-- Server version	5.1.36-community

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
-- Table structure for table `albums`
--

DROP TABLE IF EXISTS `albums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `albums` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `collection_id` int(8) NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL DEFAULT '',
  `image` varchar(64) NOT NULL DEFAULT '',
  `description` text,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `collection_id` (`collection_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `collections`
--

DROP TABLE IF EXISTS `collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `collections` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `gallery_id` int(8) NOT NULL DEFAULT '0',
  `name` varchar(64) NOT NULL DEFAULT '',
  `image` varchar(64) NOT NULL DEFAULT '',
  `description` text,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `gallery_id` (`gallery_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `divelog`
--

DROP TABLE IF EXISTS `divelog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `divelog` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL,
  `dive_no` int(8) NOT NULL DEFAULT '1',
  `dive_date` date DEFAULT NULL,
  `time_in` varchar(32) NOT NULL DEFAULT '',
  `time_out` varchar(32) NOT NULL DEFAULT '',
  `rnt` int(8) NOT NULL DEFAULT '0',
  `abt` int(8) NOT NULL DEFAULT '0',
  `tbt` int(8) NOT NULL DEFAULT '0',
  `air_temp` varchar(16) NOT NULL DEFAULT '',
  `bottom_temp` varchar(16) NOT NULL DEFAULT '',
  `begin_psi` int(8) NOT NULL DEFAULT '0',
  `end_psi` int(8) NOT NULL DEFAULT '0',
  `viz` varchar(16) NOT NULL DEFAULT '',
  `weight` varchar(16) NOT NULL DEFAULT '',
  `salt` enum('Y','N') NOT NULL DEFAULT 'N',
  `fresh` enum('Y','N') NOT NULL DEFAULT 'N',
  `shore` enum('Y','N') NOT NULL DEFAULT 'N',
  `boat` enum('Y','N') NOT NULL DEFAULT 'N',
  `waves` enum('Y','N') NOT NULL DEFAULT 'N',
  `wetsuit` enum('Y','N') NOT NULL DEFAULT 'N',
  `drysuit` enum('Y','N') NOT NULL DEFAULT 'N',
  `hood` enum('Y','N') NOT NULL DEFAULT 'N',
  `gloves` enum('Y','N') NOT NULL DEFAULT 'N',
  `boots` enum('Y','N') NOT NULL DEFAULT 'N',
  `surge` enum('Y','N') DEFAULT 'N',
  `vest` enum('Y','N') DEFAULT 'N',
  `location` varchar(256) NOT NULL DEFAULT '',
  `site_name` varchar(256) NOT NULL DEFAULT '',
  `si` varchar(16) NOT NULL DEFAULT '',
  `begin_pg` varchar(8) NOT NULL DEFAULT '',
  `end_pg` varchar(8) NOT NULL DEFAULT '',
  `depth` varchar(16) NOT NULL DEFAULT '',
  `safety_stop` varchar(16) NOT NULL DEFAULT '',
  `bottom_time` int(8) NOT NULL DEFAULT '0',
  `computer` enum('Y','N') NOT NULL DEFAULT 'N',
  `computer_desc` varchar(128) NOT NULL DEFAULT '',
  `eanx` enum('Y','N') NOT NULL DEFAULT 'N',
  `eanx_percent` varchar(128) NOT NULL DEFAULT '',
  `comments` text,
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`,`email`)
) ENGINE=InnoDB AUTO_INCREMENT=169 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `divelogprefs`
--

DROP TABLE IF EXISTS `divelogprefs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `divelogprefs` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `user_id` int(8) DEFAULT NULL,
  `distance` enum('I','M') NOT NULL DEFAULT 'I',
  `weight` enum('I','M') NOT NULL DEFAULT 'I',
  `temperature` enum('I','M') NOT NULL DEFAULT 'I',
  `pressure` enum('I','M') NOT NULL DEFAULT 'I',
  `cert_level` varchar(64) NOT NULL DEFAULT '',
  `cert_agency` varchar(64) NOT NULL DEFAULT '',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `users_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `galleries`
--

DROP TABLE IF EXISTS `galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `galleries` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `image` varchar(64) NOT NULL DEFAULT '',
  `description` text,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1005 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `album_id` int(8) NOT NULL DEFAULT '0',
  `title` varchar(64) NOT NULL DEFAULT '',
  `type` varchar(64) NOT NULL DEFAULT '',
  `image` varchar(128) DEFAULT NULL,
  `image_width` int(8) NOT NULL DEFAULT '0',
  `image_height` int(8) NOT NULL DEFAULT '0',
  `thumbnail` varchar(128) DEFAULT NULL,
  `display_image` varchar(128) DEFAULT NULL,
  `description` text,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `album_id` (`album_id`)
) ENGINE=MyISAM AUTO_INCREMENT=247 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `imageviews`
--

DROP TABLE IF EXISTS `imageviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imageviews` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `filename` text NOT NULL,
  `counter` int(8) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=314 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `gallery_id` int(8) NOT NULL,
  `email` varchar(128) NOT NULL DEFAULT '',
  `passwd` varchar(128) NOT NULL,
  `fname` varchar(64) NOT NULL DEFAULT '',
  `lname` varchar(64) NOT NULL DEFAULT '',
  `collection_id` int(8) NOT NULL,
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-08-18 17:34:12
