USE seongjinDB;

-- user 테이블 먼저 생성
CREATE TABLE IF NOT EXISTS `user` (
    `user_id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    `username` varchar(255) NOT NULL,
    `phone` varchar(255) NOT NULL,
    `role` enum('admin','user') NOT NULL DEFAULT 'user',
    `authority` tinyint(1) NOT NULL DEFAULT '1',
    `available` tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- board 테이블 생성
CREATE TABLE IF NOT EXISTS `board` (
    `board_id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `content` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
    `date` datetime NOT NULL,
    `status` enum('normal','notification') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
    `user_id` int(11) NOT NULL,
    `openclose` enum('open','close','wait') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'wait',
    `openclose_time` datetime DEFAULT NULL,
    `delete_type` tinyint(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`board_id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `board_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=407 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- attachment 테이블 생성
CREATE TABLE IF NOT EXISTS `attachment` (
    `attachment_id` int(11) NOT NULL AUTO_INCREMENT,
    `filename` varchar(255) NOT NULL,
    `filepath` varchar(255) NOT NULL,
    `filesize` int(11) NOT NULL,
    `file_type` varchar(255) NOT NULL,
    `upload_date` datetime NOT NULL,
    `board_id` int(11) NOT NULL,
    PRIMARY KEY (`attachment_id`),
    KEY `board_id` (`board_id`),
    CONSTRAINT `attachment_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `board` (`board_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- comment 테이블 생성
CREATE TABLE IF NOT EXISTS `comment` (
    `comment_id` int(11) NOT NULL AUTO_INCREMENT,
    `content` varchar(255) NOT NULL,
    `board_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `date` datetime NOT NULL,
    PRIMARY KEY (`comment_id`),
    KEY `board_id` (`board_id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `board` (`board_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- info 테이블 생성
CREATE TABLE IF NOT EXISTS `info` (
    `info_id` int(11) NOT NULL AUTO_INCREMENT,
    `date` datetime NOT NULL,
    `reason_content` varchar(255) NOT NULL,
    `board_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    PRIMARY KEY (`info_id`),
    KEY `board_id` (`board_id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `info_ibfk_1` FOREIGN KEY (`board_id`) REFERENCES `board` (`board_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `info_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=utf8;

-- 초기 관리자 데이터 생성
INSERT INTO `user` (`email`, `password`, `username`, `phone`, `role`, `authority`, `available`)
VALUES ('whtjdwls1539@nate.com', '0000', '조성진', '010-6640-8860', 'admin', 1, 1);