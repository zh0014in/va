<?php
// Init owncloud


// Check if we are a user
OCP\JSON::checkLoggedIn();

$user = OCP\USER::getUser();
$id = isset($_POST['id']) ? $_POST['id'] : '';
$result = OC_Comment::deleteComment($id);
if($result){
    OCP\JSON::success(array('data' => $result));
}else{
    OCP\JSON::error(array('data' => array('message' => 'An error occurred.')));
}