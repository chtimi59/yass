SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `startDate` date DEFAULT NULL,
  `stopDate` date DEFAULT NULL,
  `duration` int(11) NOT NULL DEFAULT '0',
  `path` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `displays` (
  `ip` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `assetId` int(11) DEFAULT NULL,
  PRIMARY KEY (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

