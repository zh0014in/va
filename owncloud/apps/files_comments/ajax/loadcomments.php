<?php
// Init owncloud



// Check if we are a user
OCP\JSON::checkLoggedIn();

// Set the session key for the file we are about to edit.
$dir = isset($_GET['dir']) ? $_GET['dir'] : '';
$filename = isset($_GET['file']) ? $_GET['file'] : '';
if(!empty($filename))
{
    $path = $dir.'/'.$filename;
    $comments = OC_Comment::getComments($path);
    OCP\JSON::success(array('data' => $comments));
} else {
    OCP\JSON::error(array('data' => array( 'message' => 'Invalid file path supplied.')));
}