SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `pst_posts` (
  `post_id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `thread_id` int(10) unsigned zerofill NOT NULL,
  `user_id` int(10) unsigned zerofill NOT NULL,
  `post_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `image` varchar(255) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`post_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `pst_threads` (
  `thread_id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `board_id` mediumint(8) unsigned zerofill NOT NULL,
  `key` varchar(10) DEFAULT NULL,
  `title` varchar(128) NOT NULL,
  `user` int(10) unsigned zerofill NOT NULL,
  `posted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`thread_id`),
  KEY `key` (`key`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `ste_boards` (
  `board_id` mediumint(8) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `dir` varchar(10) NOT NULL,
  `name` varchar(32) NOT NULL,
  `mes` varchar(128) NOT NULL,
  `hidden` enum('0','1') NOT NULL DEFAULT '0',
  `disabled` enum('0','1') NOT NULL DEFAULT '0',
  `post_min_lvl` mediumint(2) NOT NULL,
  `view_min_lvl` mediumint(2) NOT NULL,
  `reply_min_lvl` mediumint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`board_id`),
  UNIQUE KEY `dir` (`dir`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ste_navbar` (
  `id` smallint(5) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `position` tinyint(3) unsigned NOT NULL,
  `href` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` varchar(255) NOT NULL,
  `class` varchar(48) NOT NULL,
  `usr_thresh` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `usr_max` tinyint(4) NOT NULL DEFAULT '0',
  `board_id` mediumint(8) unsigned zerofill NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `position` (`position`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

INSERT INTO `ste_navbar` (`id`, `position`, `href`, `title`, `text`, `class`, `usr_thresh`, `usr_max`, `board_id`) VALUES
(00001, 0, 'index.php', 'Home Page', 'Home', '', 0, 0, 00000000),
(00006, 253, 'usr.php?mode=login', 'Login', 'Login', '', 0, 1, 00000000),
(00005, 252, 'usr.php?mode=uac', 'Account Control Panel', 'Account', '', 2, 0, 00000000),
(00007, 254, 'usr.php?mode=logout', 'Logout', 'Logout', '', 2, 0, 00000000),
(00008, 251, 'faq.php', 'Frequently Asked Questions', 'FAQ', '', 0, 0, 00000000),
(00009, 255, 'admin.php', 'Administration Panel', 'APanel', '', 70, 0, 00000000);

CREATE TABLE IF NOT EXISTS `ste_vars` (
  `key` varchar(64) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `ste_vars` (`key`, `value`) VALUES
('site_title', 'Halcyon Image Board'),
('base_header', 'Halcyon Image Board'),
('base_mes', 'Rockin'' the alpha'),
('board_active', '1'),
('base_url', 'http://www.myURL.com'),
('updir', 'i/up/images/'),
('avdir', 'i/av/'),
('thdir', 'i/up/thumbs/'),
('maxFileSize', '2621440');

CREATE TABLE IF NOT EXISTS `user_accounts` (
  `user_id` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `level` smallint(2) NOT NULL DEFAULT '2',
  `email` varchar(48) NOT NULL,
  `password` varchar(32) NOT NULL,
  `posts` int(6) unsigned NOT NULL,
  `avatar` varchar(128) NOT NULL DEFAULT 'anon.png',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `name` (`name`,`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

INSERT INTO `user_accounts` (`user_id`, `name`, `level`, `email`, `password`, `posts`, `avatar`) VALUES
(0000000001, 'Anonymous', 1, '', '', 0, 'anon.png');

CREATE TABLE IF NOT EXISTS `user_levels` (
  `level` smallint(6) NOT NULL,
  `rank` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `user_levels` (`level`, `rank`) VALUES
(1, 'Anonymous'),
(2, 'Registered'),
(30, 'Moderator'),
(50, 'Global Moderator'),
(70, 'Administrator'),
(90, 'Site Owner'),
(0, 'Banned User');

CREATE TABLE IF NOT EXISTS `user_online` (
  `user_id` int(10) unsigned zerofill NOT NULL,
  `last_ping` int(11) NOT NULL,
  `current_ip` varchar(16) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;