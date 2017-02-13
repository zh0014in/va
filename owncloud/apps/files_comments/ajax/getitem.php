<?php
require_once(OC::$APPSROOT . '/apps/files_comments/lib_comment.php');

OCP\JSON::checkAppEnabled('files_comments');
OCP\JSON::checkLoggedIn();

$item = array();
$userDirectory = '/' . OCP\USER::getUser() . '/files';
$source = $userDirectory . $_GET['item'];
$path = $source;
// Search for item and shared parent folders
while ($path != $userDirectory) {
    if ($rows = OC_Commenting::getMyInvitedItem($path)) {
        for ($i = 0; $i < count($rows); $i++) {
            $uid_shared_with = $rows[$i]['uid_commenting_with'];
            if ($path == $source) {
                $user = array(array('uid' => $uid_shared_with, 'parentFolder' => false));
            } else {
                $user = array(array('uid' => $uid_shared_with, 'parentFolder' => basename($path)));
            }
            if (!isset($item['users'])) {
                $item['users'] = $user;
            } else if (is_array($item['users'])) {
                $item['users'] = array_merge($item['users'], $user);
            }
        }
    }
    $path = dirname($path);
}

OCP\JSON::success(array('data' => $item));

?>
