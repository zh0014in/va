<?php
require_once(OC::$APPSROOT . '/apps/files_comments/lib_comment.php');

OCP\JSON::checkAppEnabled('files_comments');
OCP\JSON::checkLoggedIn();

$source = '/'.OCP\USER::getUser().'/files'.$_POST['source'];
$uid_shared_with = $_POST['uid_shared_with'];
OC_Comment::unshare($source, $uid_shared_with);

OCP\JSON::success();

?>
