-- Adminer 4.7.7 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `mf_employees`;
CREATE TABLE `mf_employees` (
  `recid` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` varchar(200) NOT NULL,
  `employee_name` varchar(200) NOT NULL,
  `salary` double(12,2) NOT NULL,
  `birth_date` date NOT NULL,
  PRIMARY KEY (`recid`),
  UNIQUE KEY `employee_id` (`employee_id`),
  KEY `idx_employee_id` (`employee_id`),
  KEY `idx_employee_name` (`employee_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `mf_employees` (`recid`, `employee_id`, `employee_name`, `salary`, `birth_date`) VALUES
(1,	'EMP-0001',	'John Doe',	50000.00,	'2020-01-01'),
(2,	'EMP-0002',	'Jane Smith2',	60000.00,	'2005-04-01'),
(3,	'EMP-0003',	'Bob Johnson',	45000.00,	'2000-01-10'),
(4,	'EMP-0004',	'Alice Williams',	60000.00,	'1999-02-10'),
(5,	'EMP-0005',	'Charlie Brown',	52000.00,	'1998-03-10');

DROP TABLE IF EXISTS `mf_users`;
CREATE TABLE `mf_users` (
  `recid` int(11) NOT NULL AUTO_INCREMENT,
  `userID` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`recid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `mf_users` (`recid`, `userID`, `username`, `password`) VALUES
(1,	'USR-0001',	'admin',	'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- 2026-06-07 07:42:16
