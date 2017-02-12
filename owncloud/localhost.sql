-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 24, 2017 at 10:24 AM
-- Server version: 5.1.72
-- PHP Version: 5.3.2-1ubuntu4.28

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `owncloud`
--
CREATE DATABASE `owncloud` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `owncloud`;

-- --------------------------------------------------------

--
-- Table structure for table `oc_appconfig`
--

CREATE TABLE IF NOT EXISTS `oc_appconfig` (
  `appid` varchar(255) NOT NULL DEFAULT '',
  `configkey` varchar(255) NOT NULL DEFAULT '',
  `configvalue` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oc_appconfig`
--

INSERT INTO `oc_appconfig` (`appid`, `configkey`, `configvalue`) VALUES
('core', 'installedat', '1389299678.6349'),
('core', 'lastupdatedat', '1389299678.6355'),
('files_pdfviewer', 'installed_version', '0.1'),
('files_pdfviewer', 'types', ''),
('files_pdfviewer', 'enabled', 'yes'),
('files_imageviewer', 'installed_version', '1.0'),
('files_imageviewer', 'types', ''),
('files_imageviewer', 'enabled', 'yes'),
('files_odfviewer', 'installed_version', '0.1'),
('files_odfviewer', 'types', ''),
('files_odfviewer', 'enabled', 'yes'),
('media', 'installed_version', '0.4'),
('core', 'remote_ampache', '/apps/media/remote.php'),
('media', 'types', ''),
('media', 'enabled', 'yes'),
('user_migrate', 'installed_version', '0.1'),
('user_migrate', 'types', ''),
('user_migrate', 'enabled', 'yes'),
('files_texteditor', 'installed_version', '0.3'),
('files_texteditor', 'types', ''),
('files_texteditor', 'enabled', 'yes'),
('calendar', 'installed_version', '0.4'),
('core', 'remote_calendar', '/apps/calendar/appinfo/remote.php'),
('core', 'remote_caldav', '/apps/calendar/appinfo/remote.php'),
('core', 'public_calendar', '/apps/calendar/share.php'),
('core', 'public_caldav', '/apps/calendar/share.php'),
('calendar', 'types', ''),
('calendar', 'enabled', 'yes'),
('admin_migrate', 'installed_version', '0.1'),
('admin_migrate', 'types', ''),
('admin_migrate', 'enabled', 'yes'),
('files_versions', 'installed_version', '1.0.1'),
('files_versions', 'types', 'filesystem'),
('files_versions', 'enabled', 'yes'),
('files', 'installed_version', '1.1.1'),
('core', 'remote_files', '/apps/files/appinfo/remote.php'),
('core', 'remote_webdav', '/apps/files/appinfo/remote.php'),
('files', 'types', 'filesystem'),
('files', 'enabled', 'yes'),
('files_archive', 'installed_version', '0.2'),
('files_archive', 'types', 'filesystem'),
('files_archive', 'enabled', 'yes'),
('contacts', 'installed_version', '0.2'),
('core', 'remote_contacts', '/apps/contacts/appinfo/remote.php'),
('core', 'remote_carddav', '/apps/contacts/appinfo/remote.php'),
('contacts', 'types', ''),
('contacts', 'enabled', 'yes'),
('gallery', 'installed_version', '0.5.0\n'),
('core', 'public_gallery', '/apps/gallery/sharing.php'),
('gallery', 'types', ''),
('gallery', 'enabled', 'yes'),
('files_sharing', 'installed_version', '0.2.1'),
('core', 'public_files', '/apps/files_sharing/get.php'),
('core', 'public_webdav', '/apps/files_sharing/get.php'),
('files_sharing', 'types', 'filesystem'),
('files_sharing', 'enabled', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `oc_calendar_calendars`
--

CREATE TABLE IF NOT EXISTS `oc_calendar_calendars` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` varchar(255) DEFAULT NULL,
  `displayname` varchar(100) DEFAULT NULL,
  `uri` varchar(100) DEFAULT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  `ctag` int(10) unsigned NOT NULL DEFAULT '0',
  `calendarorder` int(10) unsigned NOT NULL DEFAULT '0',
  `calendarcolor` varchar(10) DEFAULT NULL,
  `timezone` longtext,
  `components` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oc_calendar_calendars`
--


-- --------------------------------------------------------

--
-- Table structure for table `oc_calendar_objects`
--

CREATE TABLE IF NOT EXISTS `oc_calendar_objects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `calendarid` int(10) unsigned NOT NULL DEFAULT '0',
  `objecttype` varchar(40) NOT NULL DEFAULT '',
  `startdate` datetime DEFAULT '0000-00-00 00:00:00',
  `enddate` datetime DEFAULT '0000-00-00 00:00:00',
  `repeating` int(11) DEFAULT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `calendardata` longtext,
  `uri` varchar(100) DEFAULT NULL,
  `lastmodified` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oc_calendar_objects`
--


-- --------------------------------------------------------

--
-- Table structure for table `oc_calendar_share_calendar`
--

CREATE TABLE IF NOT EXISTS `oc_calendar_share_calendar` (
  `owner` varchar(255) NOT NULL DEFAULT '',
  `share` varchar(255) NOT NULL DEFAULT '',
  `sharetype` varchar(6) NOT NULL DEFAULT '',
  `calendarid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `permissions` tinyint(4) NOT NULL DEFAULT '0',
  `active` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oc_calendar_share_calendar`
--


-- --------------------------------------------------------

--
-- Table structure for table `oc_calendar_share_event`
--

CREATE TABLE IF NOT EXISTS `oc_calendar_share_event` (
  `owner` varchar(255) NOT NULL DEFAULT '',
  `share` varchar(255) NOT NULL DEFAULT '',
  `sharetype` varchar(6) NOT NULL DEFAULT '',
  `eventid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `permissions` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oc_calendar_share_event`
--


-- --------------------------------------------------------

--
-- Table structure for table `oc_contacts_addressbooks`
--

CREATE TABLE IF NOT EXISTS `oc_contacts_addressbooks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` varchar(255) NOT NULL DEFAULT '',
  `displayname` varchar(255) DEFAULT NULL,
  `uri` varchar(100) DEFAULT NULL,
  `description` longtext,
  `ctag` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oc_contacts_addressbooks`
--


-- --------------------------------------------------------

--
-- Table structure for table `oc_contacts_cards`
--

CREATE TABLE IF NOT EXISTS `oc_contacts_cards` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `addressbookid` int(10) unsigned NOT NULL DEFAULT '0',
  `fullname` varchar(255) DEFAULT NULL,
  `carddata` longtext,
  `uri` varchar(100) DEFAULT NULL,
  `lastmodified` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oc_contacts_cards`
--


-- --------------------------------------------------------

--
-- Table structure for table `oc_fscache`
--

CREATE TABLE IF NOT EXISTS `oc_fscache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(512) NOT NULL DEFAULT '',
  `path_hash` varchar(32) NOT NULL DEFAULT '',
  `parent` bigint(20) NOT NULL DEFAULT '0',
  `name` varchar(300) NOT NULL DEFAULT '\n	   ',
  `user` varchar(64) NOT NULL DEFAULT '\n	   ',
  `size` bigint(20) NOT NULL DEFAULT '0',
  `ctime` bigint(20) NOT NULL DEFAULT '0',
  `mtime` bigint(20) NOT NULL DEFAULT '0',
  `mimetype` varchar(96) NOT NULL DEFAULT '\n	   ',
  `mimepart` varchar(32) NOT NULL DEFAULT '\n	   ',
  `encrypted` tinyint(4) NOT NULL DEFAULT '0',
  `versioned` tinyint(4) NOT NULL DEFAULT '0',
  `writable` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fscache_path_hash_index` (`path_hash`),
  KEY `parent_index` (`parent`),
  KEY `name_index` (`name`),
  KEY `parent_name_index` (`parent`,`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `oc_fscache`
--

INSERT INTO `oc_fscache` (`id`, `path`, `path_hash`, `parent`, `name`, `user`, `size`, `ctime`, `mtime`, `mimetype`, `mimepart`, `encrypted`, `versioned`, `writable`) VALUES
(1, '/student/files', '1392189ecbe4053c038db8ff90fbd93e', -1, 'files', 'student', 19, 1389299762, 1389299762, 'httpd/unix-directory', 'httpd', 0, 0, 1),
(2, '/student/files/sample.txt', 'd932229aad9708b9dd162e6b585f2bf5', 1, 'sample.txt', 'student', 19, 1389299762, 1389299762, 'text/plain', 'text', 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `oc_gallery_sharing`
--

CREATE TABLE IF NOT EXISTS `oc_gallery_sharing` (
  `token` varchar(64) NOT NULL DEFAULT '',
  `gallery_id` int(11) NOT NULL DEFAULT '0',
  `recursive` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oc_gallery_sharing`
--


-- --------------------------------------------------------

--
-- Table structure for table `oc_group_user`
--

CREATE TABLE IF NOT EXISTS `oc_group_user` (
  `gid` varchar(64) NOT NULL DEFAULT '',
  `uid` varchar(64) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oc_group_user`
--

INSERT INTO `oc_group_user` (`gid`, `uid`) VALUES
('admin', 'student');

-- --------------------------------------------------------

--
-- Table structure for table `oc_groups`
--

CREATE TABLE IF NOT EXISTS `oc_groups` (
  `gid` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oc_groups`
--

INSERT INTO `oc_groups` (`gid`) VALUES
('admin');

-- --------------------------------------------------------

--
-- Table structure for table `oc_locks`
--

CREATE TABLE IF NOT EXISTS `oc_locks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` varchar(200) DEFAULT NULL,
  `owner` varchar(100) DEFAULT NULL,
  `timeout` int(10) unsigned DEFAULT NULL,
  `created` bigint(20) DEFAULT NULL,
  `token` varchar(100) DEFAULT NULL,
  `scope` tinyint(4) DEFAULT NULL,
  `depth` tinyint(4) DEFAULT NULL,
  `uri` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oc_locks`
--


-- --------------------------------------------------------

--
-- Table structure for table `oc_log`
--

CREATE TABLE IF NOT EXISTS `oc_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `moment` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `appid` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `action` varchar(255) NOT NULL DEFAULT '',
  `info` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oc_log`
--


-- --------------------------------------------------------

--
-- Table structure for table `oc_media_albums`
--

CREATE TABLE IF NOT EXISTS `oc_media_albums` (
  `album_id` int(11) NOT NULL AUTO_INCREMENT,
  `album_name` varchar(200) NOT NULL DEFAULT '',
  `album_artist` int(11) NOT NULL DEFAULT '0',
  `album_art` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`album_id`),
  KEY `album_index` (`album_id`),
  KEY `album_name_index` (`album_name`),
  KEY `album_artist_index` (`album_artist`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oc_media_albums`
--


-- --------------------------------------------------------

--
-- Table structure for table `oc_media_artists`
--

CREATE TABLE IF NOT EXISTS `oc_media_artists` (
  `artist_id` int(11) NOT NULL AUTO_INCREMENT,
  `artist_name` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`artist_id`),
  UNIQUE KEY `artist_name` (`artist_name`),
  KEY `artist_index` (`artist_id`),
  KEY `artist_name_index` (`artist_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oc_media_artists`
--


-- --------------------------------------------------------

--
-- Table structure for table `oc_media_sessions`
--

CREATE TABLE IF NOT EXISTS `oc_media_sessions` (
  `session_id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(64) NOT NULL DEFAULT '',
  `user_id` varchar(64) NOT NULL DEFAULT '',
  `start` datetime NOT NULL DEFAULT '1970-01-01 00:00:00',
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oc_media_sessions`
--


-- --------------------------------------------------------

--
-- Table structure for table `oc_media_songs`
--

CREATE TABLE IF NOT EXISTS `oc_media_songs` (
  `song_id` int(11) NOT NULL AUTO_INCREMENT,
  `song_name` varchar(200) NOT NULL DEFAULT '',
  `song_artist` int(11) NOT NULL DEFAULT '0',
  `song_album` int(11) NOT NULL DEFAULT '0',
  `song_path` varchar(200) NOT NULL DEFAULT '',
  `song_user` varchar(64) NOT NULL DEFAULT '0',
  `song_length` int(11) NOT NULL DEFAULT '0',
  `song_track` int(11) NOT NULL DEFAULT '0',
  `song_size` int(11) NOT NULL DEFAULT '0',
  `song_playcount` int(11) NOT NULL DEFAULT '0',
  `song_lastplayed` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`song_id`),
  KEY `song_index` (`song_id`),
  KEY `song_album_index` (`song_album`),
  KEY `song_artist_index` (`song_artist`),
  KEY `song_name_index` (`song_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `oc_media_songs`
--


-- --------------------------------------------------------

--
-- Table structure for table `oc_media_users`
--

CREATE TABLE IF NOT EXISTS `oc_media_users` (
  `user_id` varchar(64) NOT NULL DEFAULT '0',
  `user_password_sha256` varchar(64) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oc_media_users`
--

INSERT INTO `oc_media_users` (`user_id`, `user_password_sha256`) VALUES
('student', '264c8c381bf16c982a4e59b0dd4c6f7808c51a05f64c35db42cc78a2a72875bb');

-- --------------------------------------------------------

--
-- Table structure for table `oc_pictures_images_cache`
--

CREATE TABLE IF NOT EXISTS `oc_pictures_images_cache` (
  `uid_owner` varchar(64) NOT NULL DEFAULT '',
  `path` varchar(256) NOT NULL DEFAULT '',
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oc_pictures_images_cache`
--


-- --------------------------------------------------------

--
-- Table structure for table `oc_preferences`
--

CREATE TABLE IF NOT EXISTS `oc_preferences` (
  `userid` varchar(255) NOT NULL DEFAULT '',
  `appid` varchar(255) NOT NULL DEFAULT '',
  `configkey` varchar(255) NOT NULL DEFAULT '',
  `configvalue` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oc_preferences`
--


-- --------------------------------------------------------

--
-- Table structure for table `oc_properties`
--

CREATE TABLE IF NOT EXISTS `oc_properties` (
  `userid` varchar(200) NOT NULL DEFAULT '',
  `propertypath` varchar(255) NOT NULL DEFAULT '',
  `propertyname` varchar(255) NOT NULL DEFAULT '',
  `propertyvalue` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oc_properties`
--


-- --------------------------------------------------------

--
-- Table structure for table `oc_sharing`
--

CREATE TABLE IF NOT EXISTS `oc_sharing` (
  `uid_owner` varchar(64) NOT NULL DEFAULT '',
  `uid_shared_with` varchar(64) NOT NULL DEFAULT '',
  `source` varchar(128) NOT NULL DEFAULT '',
  `target` varchar(128) NOT NULL DEFAULT '',
  `permissions` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `oc_sharing`
--


-- --------------------------------------------------------

--
-- Table structure for table `oc_users`
--

CREATE TABLE IF NOT EXISTS `oc_users` (
  `uid` varchar(64) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `oc_users`
--

INSERT INTO `oc_users` (`uid`, `password`) VALUES
('student', '$2a$08$b7tfS9Zwao4rbC1YrSZ.uOrOi4NzKLsnwyWNdZpZJ9aMKlLS46tfy');

INSERT INTO `oc_appconfig`(`appid`, `configkey`, `configvalue`) VALUES ('files_comments','types','filesystem')
INSERT INTO `oc_appconfig`(`appid`, `configkey`, `configvalue`) VALUES ('files_comments','enabled','false')
INSERT INTO `oc_appconfig`(`appid`, `configkey`, `configvalue`) VALUES ('files_comments','installed_version','0.0.1')
CREATE TABLE IF NOT EXISTS `oc_commenting` (
  `uid_owner` varchar(64) NOT NULL DEFAULT '',
  `uid_commenting_with` varchar(64) NOT NULL DEFAULT '',
  `filepath` varchar(128) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
CREATE TABLE IF NOT EXISTS `oc_comments` (
  `uid_owner` varchar(64) NOT NULL DEFAULT '',
  `uid_createdby` varchar(64) NOT NULL DEFAULT '',
  `filepath` varchar(128) NOT NULL DEFAULT '',
  `body` varchar(128) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;