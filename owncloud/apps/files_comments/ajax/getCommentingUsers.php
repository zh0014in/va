<?php
// Init owncloud



// Check if we are a user
OCP\JSON::checkLoggedIn();
$user = OCP\USER::getUser();

OCP\JSON::success(array('data' => OC_Comment::$CommentingUsers));