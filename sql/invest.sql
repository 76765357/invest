-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2014-08-22 18:53:29
-- 服务器版本： 5.5.37-0ubuntu0.12.04.1
-- PHP Version: 5.3.10-1ubuntu3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `invest`
--

-- --------------------------------------------------------

--
-- 表的结构 `consult_type`
--

CREATE TABLE IF NOT EXISTS `consult_type` (
  `id` int(11) NOT NULL COMMENT '咨询类型id',
  `name` int(11) NOT NULL COMMENT '类似：新三板',
  `content` int(11) NOT NULL COMMENT '类似：什么是上市？上市流程是什么？上市适合哪些职业？',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='咨询';

-- --------------------------------------------------------

--
-- 表的结构 `judge`
--

CREATE TABLE IF NOT EXISTS `judge` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `agencyid` int(11) NOT NULL,
  `starrank` int(11) NOT NULL,
  `ishelpful` tinyint(4) NOT NULL,
  `iswell` tinyint(4) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='中介评价';

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL,
  `name` char(30) CHARACTER SET latin1 NOT NULL COMMENT '姓名',
  `address` varchar(200) CHARACTER SET latin1 NOT NULL,
  `hadservernums` int(11) NOT NULL,
  `company` varchar(200) CHARACTER SET latin1 NOT NULL,
  `phone` int(11) NOT NULL,
  `position` char(50) CHARACTER SET latin1 NOT NULL,
  `fansnum` int(11) NOT NULL,
  `introduction` varchar(11) CHARACTER SET latin1 NOT NULL,
  `serverrank` int(11) NOT NULL,
  `serverprecent` int(11) NOT NULL,
  `professionrank` int(11) NOT NULL,
  `professionprecent` int(11) NOT NULL,
  `tag` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
