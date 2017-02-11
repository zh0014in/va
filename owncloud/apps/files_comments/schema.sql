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