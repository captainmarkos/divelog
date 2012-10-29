
CREATE TABLE `divelogprefs` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `user_id` int(8) NOT NULL,
  `distance` enum('I','M') NOT NULL DEFAULT 'I',
  `weight` enum('I','M') NOT NULL DEFAULT 'I',
  `temperature` enum('I','M') NOT NULL DEFAULT 'I',
  `pressure` enum('I','M') NOT NULL DEFAULT 'I',
  `deleted` enum('Y','N') NOT NULL DEFAULT 'N',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;