-- phpMyAdmin SQL Dump

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone="+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `trader`
--

CREATE TABLE `area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) NOT NULL,
  `area` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);

INSERT INTO `area` (`id`, `id_parent`, `area`)
VALUES
	(1, 0, 'portfolio'),
	(2, 0, 'quotes'),
	(3, 0, 'buy'),
	(4, 0, 'sell');

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `exchange` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exchange` varchar(255) NOT NULL DEFAULT '',
  `full_name` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `exchange` (`exchange`, `full_name`) VALUES
('NASDAQ', 'NASDAQ QMX - National Association of Securities Dealers Quotation System', 'http://www.nasdaq.com/'),
('NYSE', 'New York Stock Exchange', 'http://www.nyse.com/'),
('AMEX', 'NYSE AMEX', ''),
('TSE', 'Tokyo Stock Exchange', 'http://www.tse.or.jp/english'),
('LSE', 'London Stock Exchange', 'http://www.londonstockexchange.com/home/homepage.htm'),
('FWB', 'Frankfurt Stock Exchange', 'http://deutsche-boerse.com/dbag/dispatch/en/kir/gdb_navigation/home'),
('HKEX', 'Hong Kong Stock Exchange', 'http://www.hkex.com.hk/index.htm'),
('SIX', 'Swiss Exchange', 'https://www.six-swiss-exchange.com/'),
('OTC', 'Over the Counter', 'http://www.otcbb.com/');

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `symbol` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `symbol` varchar(255) NOT NULL,
  `security_name` varchar(255) NOT NULL DEFAULT '',
  `id_exchange` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `archive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_symbol` int(11) NOT NULL,
  `price` decimal(65,4) NOT NULL,
  `number_of_share` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `transaction` varchar(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_symbol` int(10) NOT NULL,
  `price` decimal(65,4) NOT NULL COMMENT 'price_close',
  `price_open` decimal(65,4) NOT NULL,
  `price_high` decimal(65,4) NOT NULL,
  `price_low` decimal(65,4) NOT NULL,
  `price_change` decimal(65,4) NOT NULL,
  `time` int(11) NOT NULL,
  `price_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `price` WRITE;
/*!40000 ALTER TABLE `price` DISABLE KEYS */;

INSERT INTO `price` (`id_symbol`, `price`, `price_open`, `price_high`, `price_low`, `price_change`, `time`)
VALUES
	(14,133.5550,0.0000,0.0000,0.0000,0,1487017842),
	(248,33.7800,33.8800,34.1500,33.6500,0.13,1487022502),
	(650,168.0300,166.4500,169.0700,166.3500,1.8,1487022508),
	(2643,134.0500,134.5800,134.7000,133.7000,-0.14,1487021884),
	(3278,820.7400,819.0000,823.0000,816.0000,1.5,1487103926),
	(3323,3.5600,3.5800,3.6300,3.5400,-0.02,1487024969),
	(3772,179.9001,178.5700,179.9300,178.3500,0.5401,1487104223),
	(5261,143.2000,145.1900,145.9500,143.0500,-1.62,1487021833),
	(5572,6.0900,6.1100,6.1200,6.0600,-0.1,1487024995),
	(8182,39.7100,38.6700,40.3900,38.5800,2.11,1487021824),
	(8238,83.0000,82.7700,83.1800,82.3500,0.48,1487021830);

/*!40000 ALTER TABLE `price` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `price_latest`
AS SELECT
   `price`.`id` AS `id_price`,
   `price`.`id_symbol` AS `id_symbol`,
   `price`.`price` AS `price`,
   `price`.`price_open` AS `price_open`,
   `price`.`price_low` AS `price_low`,
   `price`.`price_high` AS `price_high`,
   `price`.`price_change` AS `price_change`,max(`price`.`time`) AS `time`
FROM `price` group by `price`.`id_symbol`;

-- --------------------------------------------------------

CREATE TABLE `portfolio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_symbol` int(11) NOT NULL,
  `number_of_share` int(11) NOT NULL,
  `price` decimal(65,4) NOT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
);

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `displayname` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `cash` decimal(65,4) NOT NULL,
  `apparent_cash` decimal(65,4) NOT NULL,
  `last_search` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `username` (`username`,`password`),
  KEY `username_2` (`username`,`password`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- --------------------------------------------------------

CREATE TABLE `preference` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_area` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `preference` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
