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
) ENGINE=InnoDB AUTO_INCREMENT=173 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-09-03 17:32:00
