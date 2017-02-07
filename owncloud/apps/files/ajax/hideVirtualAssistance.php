<?php

// Init owncloud
// require_once('../../../lib/base.php');

// Check if we are a user
OC_JSON::checkLoggedIn();

OC_Preferences::setValue( OC_User::getUser(), 'files', 'showAssistant', 0 );

?>