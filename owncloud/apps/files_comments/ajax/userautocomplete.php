<?php

OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('files_comments');

$users = array();
$self = OCP\USER::getUser();
$userGroups = OC_Group::getUserGroups($self);
$users[] = "<optgroup label='Users'>";
foreach ($userGroups as $group) {
	$groupUsers = OC_Group::usersInGroup($group);
	$userCount = 0;
	foreach ($groupUsers as $user) {
		if ($user != $self) {
			$users[] = "<option value='".$user."'>".$user."</option>";
			$userCount++;
		}
	}
}
$users = array_unique($users);
$users[] = "</optgroup>";
OCP\JSON::encodedPrint($users);

?>
