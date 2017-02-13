<?php
// Init owncloud



// Check if we are a user
OCP\JSON::checkLoggedIn();
$user = OCP\USER::getUser();
$path = isset($_GET['filepath']) ? $_GET['filepath'] : '';

$root=OC_Filesystem::getRoot();
if($root=='/'){
    $root='';
}
$path=$root.$path;
$source = OC_Comment::getSource($path);
try {
    if ($source != '') {
        OCP\JSON::success(array('data' => OC_Comment::getCommentingUsers($source)));
    } else {
        OCP\JSON::success(array('data' => OC_Comment::getCommentingUsers($path)));
    }
}catch (Exception $exception){
    OCP\JSON::error(array('data' => array('message' => $exception->getMessage())));
}