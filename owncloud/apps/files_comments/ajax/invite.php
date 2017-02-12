<?php
require_once(OC::$APPSROOT . '/apps/files_comments/lib_comment.php');

OCP\JSON::checkAppEnabled('files_comments');
OCP\JSON::checkLoggedIn();

$userDirectory = '/'.OCP\USER::getUser().'/files';
$sources = explode(';', $_POST['sources']);
$uid_commenting_with = $_POST['uid_commenting_with'];
$permissions = $_POST['permissions'];
foreach ($sources as $source) {
	$file = OC_FileCache::get($source);
	$path = ltrim($source, '/'); 
	$source = $userDirectory.$source;
	// Check if the file exists or if the file is being reshared
	if ($source && $file['encrypted'] == false && (OC_FILESYSTEM::file_exists($path) && OC_FILESYSTEM::is_readable($path) || OC_Comment::getFilePath($source))) {
		try {
			$shared = new OC_Comment($source, $uid_commenting_with, $permissions);
			// If this is a private link, return the token
			if ($uid_commenting_with == OC_Comment::PUBLICLINK) {
				OCP\JSON::success(array('data' => $shared->getToken()));
			} else {
				OCP\JSON::success();
			}
		} catch (Exception $exception) {
			OCP\Util::writeLog('files_comments', 'Unexpected Error : '.$exception->getMessage(), OCP\Util::ERROR);
			OCP\JSON::error(array('data' => array('message' => $exception->getMessage())));
		}
	} else {
		if ($file['encrypted'] == true) {
			OCP\JSON::error(array('data' => array('message' => 'Encrypted files cannot be shared')));
		} else {
			OCP\Util::writeLog('files_comments', 'File does not exist or is not readable :'.$source, OCP\Util::ERROR);
			OCP\JSON::error(array('data' => array('message' => 'File does not exist or is not readable')));
		}
	}
}

?>
