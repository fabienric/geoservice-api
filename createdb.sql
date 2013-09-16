-- Geoservice database creation script

CREATE TABLE IF NOT EXISTS `geopoint` (
  `metadata` text NOT NULL,
  `altitude` double NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `identifier` varchar(128) NOT NULL,
  `type` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`latitude`,`longitude`,`identifier`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;