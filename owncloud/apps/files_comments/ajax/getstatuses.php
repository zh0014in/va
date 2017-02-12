<?php
require_once(OC::$APPSROOT . '/apps/files_comments/lib_comment.php');

OCP\JSON::checkAppEnabled('files_comments');
OCP\JSON::checkLoggedIn();

$items = array();
$userDirectory = '/'.OCP\USER::getUser().'/files';
$dirLength = strlen($userDirectory);
if ($rows = OC_Comment::getMySharedItems()) {
	for ($i = 0; $i < count($rows); $i++) {
		$source = $rows[$i]['source'];
		// Strip out user directory
		$item = substr($source, $dirLength);
		if ($rows[$i]['uid_commenting_with'] == OC_Comment::PUBLICLINK) {
			$items[$item] = true;
		} else if (!isset($items[$item])) {
			$items[$item] = false;
		}
	}
}

OCP\JSON::success(array('data' => $items));

?>