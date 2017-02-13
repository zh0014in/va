<?php
// Init owncloud


// Check if we are a user
OCP\JSON::checkLoggedIn();

$user = OCP\USER::getUser();
$filepath = isset($_POST['filepath']) ? $_POST['filepath'] : '';
$body = isset($_POST['body']) ? $_POST['body'] : '';
if (empty($filepath)) {
    OCP\JSON::error(array('data' => array('message' => 'Invalid file path supplied.')));
}
if (empty($body)) {
    OCP\JSON::error(array('data' => array('message' => 'Comment body must be provided.')));
}

$owner = OC_FileCache::getOwner($filepath);
if ($owner) {
    $root = OC_Filesystem::getRoot();
    if ($root == '/') {
        $root = '';
    }
    $filepath = $root . $filepath;
    try {
        if($owner != $user) {
            OC_Comment::checkCanComment($user, $filepath);
        }
        OC_Comment::addComment($owner, $user, $filepath, $body);
        OCP\JSON::success(array('data' => $body));
    }catch (Exception $exception){
        OCP\JSON::error(array('data' => array('message' => $exception->getMessage())));
    }
} else {
    OCP\JSON::error(array('data' => array('message' => 'Onwer of file is not found.')));
}

