-- Adminer 4.7.6 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

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

INSERT INTO `files` (`id`, `username`, `url`, `title`, `created_at`, `updated_at`) VALUES
(2,	9,	'disk:/Приложения/hotgear_fileupload/9/- Алтайские горы. Горный Алтай.jpg',	'- Алтайские горы. Горный Алтай',	1607319764,	1607319764),
(3,	9,	'disk:/Приложения/hotgear_fileupload/9/- Багряный рассвет. Архангельская область.jpg',	'- Багряный рассвет. Архангельская область',	1607320067,	1607320067),
(4,	9,	'disk:/Приложения/hotgear_fileupload/9/29931439.rtf',	'Чаша мудрости',	1607321986,	1607327101),
(5,	9,	'disk:/Приложения/hotgear_fileupload/9/roma-56-bidet-2.jpg',	'Унитаз белый',	1607326442,	1607327026),
(6,	8,	'disk:/Приложения/hotgear_fileupload/8/3.jpg',	'Eurolengo',	1607327159,	1607327176),
(7,	8,	'disk:/Приложения/hotgear_fileupload/8/arma.jpg',	'arma',	1607327220,	1607327220),
(8,	8,	'disk:/Приложения/hotgear_fileupload/8/modo.jpg',	'modo',	1607327237,	1607327237),
(9,	9,	'disk:/Приложения/hotgear_fileupload/9/nuvola.jpg',	'nuvola',	1607331363,	1607331363),
(10,	16,	'disk:/Приложения/hotgear_fileupload/16/NB860101.jpg',	'NB860101',	1607331755,	1607331755);

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
(16,	'test_vasya',	'jiiK0r9N1S0YFlH58F8tZtrPaEdwGdp6',	'$2y$13$zZE7EDAgox1qZkC/I4aScOn6WYxb633quN5AD6iPTKlDLHZmc0UUi',	NULL,	'asd@mail.ru',	10,	1607331689,	1607331689,	'BiOLH_wgaMv-n0RsWDG2cr0eb1zbBde5_1607331689',	0);

-- 2020-12-07 09:04:52
