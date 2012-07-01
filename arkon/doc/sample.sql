-- 
-- Structure for table `School`
-- 

DROP TABLE IF EXISTS `School`;
CREATE TABLE IF NOT EXISTS `School` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` smallint(6) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `createdAt` datetime NOT NULL,
  `modifiedAt` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- 
-- Data for table `School`
-- 

INSERT INTO `School` (`id`, `name`, `type`, `logo`, `deleted`, `createdAt`, `modifiedAt`) VALUES
  ('4', 'Puskás Tivadar Távközlési Technikum', '1', 'e77529401801c8bb94307ad3a11208bd.jpg', '0', '2012-06-22 12:50:03', '2012-06-22 12:50:03'),
  ('5', 'Török Flóris Altalános Iskola', '0', 'd8edb35259cf83e81bef3ba847a229c8.jpg', '0', '2012-06-25 16:28:01', '2012-06-25 16:28:01'),
  ('6', 'Budapesti Műszaki és Gazdaságtudományi Egyetem', '0', 'a42724c814d11666106ee18a9acc14f4.jpg', '0', '2012-06-25 16:33:12', '2012-06-25 16:52:38'),
  ('7', 'Delete me', '0', '', '1', '2012-06-25 16:39:42', '2012-06-25 16:45:01');

-- 
-- Structure for table `Student`
-- 

DROP TABLE IF EXISTS `Student`;
CREATE TABLE IF NOT EXISTS `Student` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `gender` tinyint(1) NOT NULL,
  `birthDate` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- 
-- Data for table `Student`
-- 

INSERT INTO `Student` (`id`, `name`, `gender`, `birthDate`) VALUES
  ('1', 'Sevcsik András', '0', '1990-05-02'),
  ('3', 'Gipsz Jakab', '0', '1892-01-01'),
  ('4', 'Cserepes Virág', '1', '1987-09-12');

-- 
-- Structure for table `Study`
-- 

DROP TABLE IF EXISTS `Study`;
CREATE TABLE IF NOT EXISTS `Study` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start` date NOT NULL,
  `finish` date NOT NULL,
  `type` smallint(6) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_27BEB84DCB944F1A` (`student_id`),
  KEY `IDX_27BEB84DC32A47EE` (`school_id`),
  CONSTRAINT `FK_27BEB84DC32A47EE` FOREIGN KEY (`school_id`) REFERENCES `School` (`id`),
  CONSTRAINT `FK_27BEB84DCB944F1A` FOREIGN KEY (`student_id`) REFERENCES `Student` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- 
-- Data for table `Study`
-- 

INSERT INTO `Study` (`id`, `start`, `finish`, `type`, `student_id`, `school_id`) VALUES
  ('1', '2007-01-01', '2010-01-01', '1', '1', '4'),
  ('2', '2007-09-01', '2010-05-06', '0', '4', '5'),
  ('3', '2008-09-01', '2010-05-01', '1', '4', '4'),
  ('4', '2007-09-01', '2017-12-01', '0', '3', '6'),
  ('8', '2007-01-01', '2007-01-01', '2', '1', '6');

-- 
-- Structure for table `User`
-- 

DROP TABLE IF EXISTS `User`;
CREATE TABLE IF NOT EXISTS `User` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` smallint(6) NOT NULL,
  `createdAt` datetime NOT NULL,
  `modifiedAt` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- 
-- Data for table `User`
-- 

INSERT INTO `User` (`id`, `username`, `password`, `role`, `createdAt`, `modifiedAt`) VALUES
  ('2', 'admin1', '6c7ca345f63f835cb353ff15bd6c5e052ec08e7a', '1', '2012-06-28 23:15:15', '2012-06-28 23:15:27'),
  ('3', 'user1', 'b3daa77b4c04a9551b8781d03191fe098f325e67', '0', '2012-06-28 23:25:33', '2012-06-28 23:25:33');

