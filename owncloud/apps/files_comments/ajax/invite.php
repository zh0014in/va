<?php
require_once(OC::$APPSROOT . '/apps/files_comments/lib_comment.php');

OCP\JSON::checkAppEnabled('files_comments');
OCP\JSON::checkLoggedIn();

$userDirectory = '/' . OCP\USER::getUser() . '/files';
$sources = explode(';', $_POST['sources']);
$uid_commenting_with = $_POST['uid_commenting_with'];
$permissions = $_POST['permissions'];
foreach ($sources as $filepath) {
    $file = OC_FileCache::get($filepath);
    $path = ltrim($filepath, '/');
    $filepath = $userDirectory . $filepath;

    if ($filepath && $file['encrypted'] == false && (OC_FILESYSTEM::file_exists($path) && OC_FILESYSTEM::is_readable($path) || OC_Comment::getFilePath($filepath))) {
        try {
            new OC_Share($filepath, $uid_commenting_with, $permissions);
        } catch (Exception $exception) {
            //
        }
        try {
            $invited = new OC_Comment($filepath, $uid_commenting_with, $permissions);
            OCP\JSON::success();
        } catch (Exception $exception) {
            OCP\Util::writeLog('files_comments', 'Unexpected Error : ' . $exception->getMessage(), OCP\Util::ERROR);
            OCP\JSON::error(array('data' => array('message' => $exception->getMessage())));
        }
    } else {
        if ($file['encrypted'] == true) {
            OCP\JSON::error(array('data' => array('message' => 'Encrypted files cannot be shared')));
        } else {
            OCP\Util::writeLog('files_comments', 'File does not exist or is not readable :' . $filepath, OCP\Util::ERROR);
            OCP\JSON::error(array('data' => array('message' => 'File does not exist or is not readable')));
        }
    }
}

?>
