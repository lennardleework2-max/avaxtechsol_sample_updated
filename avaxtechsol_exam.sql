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

INSERT INTO mf_employees
(employee_id, employee_name, salary, birth_date)
VALUES
('EMP-0006', 'Michael Garcia', 55000.00, '1997-05-15'),
('EMP-0007', 'Sarah Martinez', 62000.00, '1995-08-22'),
('EMP-0008', 'David Wilson', 48000.00, 'x`1998-11-03'),
('EMP-0009', 'Emily Anderson', 58000.00, '1996-02-18'),
('EMP-0010', 'James Taylor', 65000.00, '1994-07-09'),
('EMP-0011', 'Olivia Thomas', 53000.00, '1999-09-27'),
('EMP-0012', 'Daniel Moore', 47000.00, '2000-01-14'),
('EMP-0013', 'Sophia Jackson', 61000.00, '1997-12-05'),
('EMP-0014', 'Matthew White', 57000.00, '1995-04-30'),
('EMP-0015', 'Isabella Harris', 68000.00, '1993-10-12');


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
