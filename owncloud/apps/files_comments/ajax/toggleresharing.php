<?php

OCP\JSON::checkAppEnabled('files_comments');
OCP\JSON::checkAdminUser();
if ($_POST['resharing'] == true) {
	OCP\Config::setAppValue('files_comments', 'resharing', 'yes');
} else {
	OCP\Config::setAppValue('files_comments', 'resharing', 'no');
}

?>
