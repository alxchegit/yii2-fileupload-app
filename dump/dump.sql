-- Adminer 4.7.6 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE `hotgear_test_task` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `hotgear_test_task`;

DROP TABLE IF EXISTS `files`;
CREATE TABLE `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` int(11) NOT NULL,
  `url` varchar(1000) NOT NULL,
  `title` varchar(250) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`),
  CONSTRAINT `files_ibfk_3` FOREIGN KEY (`username`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `verification_token` varchar(255) DEFAULT NULL,
  `role` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`, `verification_token`, `role`) VALUES
(8,	'mmisha',	'BOooaKYnq1bwfodTpRoXbXxB0JtgbGsh',	'$2y$13$p95lVt9vUTbeTVEfUhkdtOkA4KnNI7vUvX/yN/m2JibnqdMRjXfR2',	NULL,	'mmishavlad@gmail.com',	10,	1606595590,	1606595685,	'L6cJ4MVObZuTA1S9x1SXphRpj9PBhecz_1606595590',	0),
(9,	'admin',	'38NESL8SSlJUG7r6IDdkS2v4wgljCpQ-',	'$2y$13$eLRYICq9sOYdjfoyrFhtF.83NYJe7D7jAtDyVUKhsrXJoHvPamPY.',	NULL,	'alxche@mail.ru',	10,	1606660528,	1606830701,	'O3gE8Hq612GhzHlvOlnye8QcEiBjVVOC_1606660528',	1),
(10,	'peter_floyd',	'1RblazBSu7SI5PvL5_oelZPgZXvRZZez',	'$2y$13$U5d/pTcrq3HEAGpXfMMcneltkudQlYiAAa/r1zJJPwVrKXQtKJn8K',	NULL,	'a.alxche@yandex.ua',	9,	1606660621,	1606827587,	'BFRVQcMUAc7urihmHOkrAYEew1Hngd_m_1606660621',	1);

-- 2020-12-04 12:36:11
