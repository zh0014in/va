<?php
// Init owncloud



// Check if we are a user
OCP\JSON::checkLoggedIn();
$user = OCP\USER::getUser();
// Set the session key for the file we are about to edit.
$dir = isset($_GET['dir']) ? $_GET['dir'] : '';
$filename = isset($_GET['file']) ? $_GET['file'] : '';
if(!empty($filename))
{
    $path = $dir.'/'.$filename;
    $root=OC_Filesystem::getRoot();
    if($root=='/'){
        $root='';
    }
    $path=$root.$path;
    $source = OC_Comment::getSource($path);
    if($source != ''){
        $commentingUser = new CommentingUser();
        $commentingUser->filepath = $source;
        $commentingUser->uid = $user;
        OC_Comment::$CommentingUsers[] = $commentingUser;
    }else{
        $commentingUser = new CommentingUser();
        $commentingUser->filepath = $path;
        $commentingUser->uid = $user;
        OC_Comment::$CommentingUsers[] = $commentingUser;
    }
    $comments = OC_Comment::getComments($path);
    OCP\JSON::success(array('data' => $comments));
} else {
    OCP\JSON::error(array('data' => array( 'message' => 'Invalid file path supplied.')));
}