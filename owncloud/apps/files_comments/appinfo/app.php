<?php


 OC::$CLASSPATH['OC_Share'] = "apps/files_comments/lib_comment.php";
// OCP\App::registerAdmin('files_sharing', 'settings');
// OCP\Util::connectHook("OC_Filesystem", "post_delete", "OC_Share", "deleteItem");
// OCP\Util::connectHook("OC_Filesystem", "post_rename", "OC_Share", "renameItem");
// OCP\Util::connectHook("OC_Filesystem", "post_write", "OC_Share", "updateItem");
// OCP\Util::connectHook('OC_User', 'post_deleteUser', 'OC_Share', 'removeUser');
// OCP\Util::connectHook('OC_Group', 'post_addToGroup', 'OC_Share', 'addToGroupShare');
// OCP\Util::connectHook('OC_Group', 'post_removeFromGroup', 'OC_Share', 'removeFromGroupShare');
// $dir = isset($_GET['dir']) ? $_GET['dir'] : '/';
OCP\Util::addscript("files_comments", "comments");
OCP\Util::addscript("files_comments", "commentsList");
OCP\Util::addscript("3rdparty", "chosen/chosen.jquery.min");
OCP\Util::addStyle( 'files_comments', 'comments' );
// OCP\Util::addStyle("3rdparty", "chosen/chosen");
