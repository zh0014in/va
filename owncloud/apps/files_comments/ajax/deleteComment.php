<?php
// Init owncloud


// Check if we are a user
OCP\JSON::checkLoggedIn();

$user = OCP\USER::getUser();
$filepath = isset($_POST['filepath']) ? $_POST['filepath'] : '';
$body = isset($_POST['body']) ? $_POST['body'] : '';
$result = OC_Comment::deleteComment($filepath,$body);
if($result){
    OCP\JSON::success(array('data' => $result));
}else{
    OCP\JSON::error(array('data' => array('message' => 'An error occurred.')));
}