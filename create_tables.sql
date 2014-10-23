
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
  `filePath` varchar(500) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  `filePathHash` binary(16) DEFAULT NULL,
  `title` varchar(500) CHARACTER SET utf8mb4 DEFAULT NULL,
  `artist` varchar(500) CHARACTER SET utf8mb4 DEFAULT NULL,
  `album` varchar(500) CHARACTER SET utf8mb4 DEFAULT NULL,
  `genre` varchar(500) CHARACTER SET utf8mb4 DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `trackNumber` int(11) DEFAULT NULL,
  `trackCount` int(11) DEFAULT NULL,
  `diskNumber` int(11) DEFAULT NULL,
  `diskCount` int(11) DEFAULT NULL,
  `length` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `filePathHash` (`filePathHash`),
  KEY `title` (`title`(191)),
  KEY `artist` (`artist`(191)),
  KEY `album` (`album`(191)),
  KEY `filePath` (`filePath`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

