<?php

OCP\User::checkAdminUser();
OCP\Util::addscript('files_comments', 'settings');
$tmpl = new OCP\Template('files_comments', 'settings');
$tmpl->assign('allowResharing', OCP\Config::getAppValue('files_comments', 'resharing', 'yes'));
return $tmpl->fetchPage();

?>