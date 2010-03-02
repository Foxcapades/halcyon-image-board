-- phpMyAdmin SQL Dump
-- version 3.2.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 25, 2010 at 07:22 PM
-- Server version: 5.1.43
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `revrocom_forum`
--

-- --------------------------------------------------------

--
-- Table structure for table `pst_posts`
--

CREATE TABLE IF NOT EXISTS `pst_posts` (
  `pid` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `thread` int(10) unsigned zerofill NOT NULL,
  `poster` int(10) unsigned zerofill NOT NULL,
  `post_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `image` varchar(255) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=80 ;

--
-- Dumping data for table `pst_posts`
--

-- --------------------------------------------------------

--
-- Table structure for table `pst_threads`
--

CREATE TABLE IF NOT EXISTS `pst_threads` (
  `tid` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `board` mediumint(8) unsigned zerofill NOT NULL,
  `key` varchar(10) DEFAULT NULL,
  `title` varchar(128) NOT NULL,
  `user` int(10) unsigned zerofill NOT NULL,
  `posted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`tid`),
  KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=41 ;

--
-- Dumping data for table `pst_threads`
--

-- --------------------------------------------------------

--
-- Table structure for table `ste_boards`
--

CREATE TABLE IF NOT EXISTS `ste_boards` (
  `id` mediumint(8) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `dir` varchar(10) NOT NULL,
  `name` varchar(32) NOT NULL,
  `mes` varchar(128) NOT NULL,
  `hidden` enum('0','1') NOT NULL DEFAULT '0',
  `disabled` enum('0','1') NOT NULL DEFAULT '0',
  `allowed` mediumint(2) NOT NULL,
  `thresh` mediumint(2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dir` (`dir`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `ste_boards`
--

-- --------------------------------------------------------

--
-- Table structure for table `ste_navbar`
--

CREATE TABLE IF NOT EXISTS `ste_navbar` (
  `id` smallint(5) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `position` tinyint(3) unsigned NOT NULL,
  `href` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` varchar(255) NOT NULL,
  `class` varchar(48) NOT NULL,
  `usr_thresh` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `usr_max` tinyint(4) NOT NULL DEFAULT '0',
  `delmo` mediumint(8) unsigned zerofill NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `position` (`position`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `ste_navbar`
--

INSERT INTO `ste_navbar` (`id`, `position`, `href`, `title`, `text`, `class`, `usr_thresh`, `usr_max`, `delmo`) VALUES
(00001, 0, 'index.php', 'Home Page', 'Home', '', 0, 0, 00000000),
(00006, 253, 'usr.php?mode=login', 'Login', 'Login', '', 0, 1, 00000000),
(00005, 252, 'usr.php?mode=uac', 'Account Control Panel', 'Account', '', 2, 0, 00000000),
(00007, 254, 'usr.php?mode=logout', 'Logout', 'Logout', '', 2, 0, 00000000),
(00008, 251, 'faq.php', 'Frequently Asked Questions', 'FAQ', '', 0, 0, 00000000),
(00009, 255, 'admin.php', 'Administration Panel', 'APanel', '', 70, 0, 00000000);

-- --------------------------------------------------------

--
-- Table structure for table `ste_vars`
--

CREATE TABLE IF NOT EXISTS `ste_vars` (
  `key` varchar(64) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ste_vars`
--

INSERT INTO `ste_vars` (`key`, `value`) VALUES
('site_title', 'Taco Board Beta'),
('base_header', 'Beta Board'),
('base_mes', 'Generic Test Message'),
('board_active', '1'),
('base_url', 'http://www.ovar9k.com'),
('updir', 'i/up/images/'),
('avdir', 'i/av/'),
('thdir', 'i/up/thumbs/'),
('maxFileSize', '2621440');

-- --------------------------------------------------------

--
-- Table structure for table `usr_accounts`
--

CREATE TABLE IF NOT EXISTS `usr_accounts` (
  `id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `level` smallint(2) NOT NULL DEFAULT '2',
  `email` varchar(48) NOT NULL,
  `password` varchar(32) NOT NULL,
  `posts` int(6) unsigned NOT NULL,
  `avatar` varchar(128) NOT NULL DEFAULT 'anon.png',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `usr_accounts`
--

INSERT INTO `usr_accounts` (`id`, `name`, `level`, `email`, `password`, `posts`, `avatar`) VALUES
(0000000001, 'Anonymous', 1, '', '', 0, 'anon.png');
