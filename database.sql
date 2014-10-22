CREATE TABLE `playhistory` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`trackId` int(11) NOT NULL,
	`date` datetime NOT NULL,
	PRIMARY KEY (`id`),
	KEY `trackId` (`trackId`),
	KEY `date` (`date`),
	CONSTRAINT `playhistory_ibfk_1` FOREIGN KEY (`trackId`) REFERENCES `tracks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tracks` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`filePath` varchar(300) CHARACTER SET latin1 NOT NULL,
	`title` varchar(500) DEFAULT NULL,
	`artist` varchar(500) DEFAULT NULL,
	`album` varchar(500) DEFAULT NULL,
	`trackNumber` int(11) DEFAULT NULL,
	`trackCount` int(11) DEFAULT NULL,
	`diskNumber` int(11) DEFAULT NULL,
	`diskCount` int(11) DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `filePath` (`filePath`),
	KEY `title` (`title`(255)),
	KEY `artist` (`artist`(255)),
	KEY `album` (`album`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;