<?php
// Init owncloud


// Check if we are a user
OCP\JSON::checkLoggedIn();

$user = OCP\USER::getUser();
$filepath = isset($_POST['filepath']) ? $_POST['filepath'] : '';
$root = OC_Filesystem::getRoot();
if ($root == '/') {
    $root = '';
}
$filepath = $root . $filepath;
try {
    $source = OC_Comment::getSource($filepath);
    if($source != ''){
        OC_Comment::unCommenting($user, $source);
    }else{
        OC_Comment::unCommenting($user, $filepath);
    }
    OCP\JSON::success(array('data' => ''));
} catch (Exception $exception) {
    OCP\JSON::error(array('data' => array('message' => $exception->getMessage())));
}


