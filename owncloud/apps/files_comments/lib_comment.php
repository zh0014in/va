<?php
/**
 * ownCloud
 *
 * @author Michael Gapczynski
 * @copyright 2011 Michael Gapczynski GapczynskiM@gmail.com
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this library.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * This class manages shared items within the database. 
 */
class OC_Comment {

	const WRITE = 1;
	const DELETE = 2;
	const UNSHARED = -1;
	const PUBLICLINK = "public";

	private $token;
      
	/**
	 * Share an item, adds an entry into the database
	 * @param $filepath The source location of the item
	 * @param $uid_commenting_with The user or group to share the item with
	 * @param $permissions The permissions, use the constants WRITE and DELETE
	 */
	public function __construct($filepath, $uid_commenting_with, $permissions) {
		$uid_owner = OCP\USER::getUser();
		$query = OCP\DB::prepare("INSERT INTO *PREFIX*commenting VALUES(?,?,?)");

		if ($uid_commenting_with == self::PUBLICLINK) {
			$token = sha1("$uid_commenting_with-$filepath");
			$query->execute(array($uid_owner, self::PUBLICLINK, $filepath, $token, $permissions));
			$this->token = $token;
		} else {
			if (OCP\User::userExists($uid_commenting_with)) {

			} else {
				throw new Exception($uid_commenting_with." is not a user");
			}

            // Check if this item is already shared with the user
            $checkInvited = OCP\DB::prepare("SELECT filepath FROM *PREFIX*commenting WHERE filepath = ? AND uid_commenting_with ".self::getUsersAndGroups($uid_commenting_with, false));
            $resultCheckInvited = $checkInvited->execute(array($filepath))->fetchAll();
            if (count($resultCheckInvited) > 0) {
                throw new Exception("This item is already invited with ".$uid_commenting_with);
            }
            $query->execute(array($uid_owner, $uid_commenting_with, $filepath));

            // send email to invite user with $uid_commenting_with
            $email = OC_Preferences::getValue($uid_commenting_with, 'settings', 'email', '');
            if($email == ''){
                throw new Exception("user email does not exist.");
            }

            $sender = OC_Preferences::getValue($uid_owner, 'settings','email','');
            if($sender == ''){
            	throw new Exception("sender email does not exist");
			}

            try {
                OC_Mail::send($email, $uid_commenting_with, 'Invite to comment file', 'You are invited to comment on file ' . $filepath.', Please login and goto shared folder to access the file and comment on it', $sender,$uid_owner);
            }
            catch (Exception $exception){
            	//
			}

            // Update mtime of shared folder to invoke a file cache rescan
            $rootView=new OC_FilesystemView('/');

		}
	}

	/**
	* Remove any duplicate or trailing '/' from the path
	* @return A clean path
	*/
	private static function cleanPath($path) {
		$path = rtrim($path, "/");
		return preg_replace('{(/)\1+}', "/", $path);
	}

	/**
	* Generate a string to be used for searching for uid_commenting_with that handles both users and groups
	* @param $uid (Optional) The uid to get the user groups for, a gid to get the users in a group, or if not set the current user
	* @return An IN operator as a string
	*/
	private static function getUsersAndGroups($uid = null, $includePrivateLinks = true) {
		$in = " IN(";
		if (isset($uid) && OC_Group::groupExists($uid)) {
			$users = OC_Group::usersInGroup($uid);
			foreach ($users as $user) {
				// Add a comma only if the the current element isn't the last
				if ($user !== end($users)) {
					$in .= "'".$user."@".$uid."', ";
				} else {
					$in .= "'".$user."@".$uid."'";
				}
			}
		} else if (isset($uid)) {
			// TODO Check if this is necessary, only constructor needs it as IN. It would be better for other queries to just return =$uid
			$in .= "'".$uid."'";
			$groups = OC_Group::getUserGroups($uid);
			foreach ($groups as $group) {
				$in .= ", '".$uid."@".$group."'";
			}
		} else {
			$uid = OCP\USER::getUser();
			$in .= "'".$uid."'";
			$groups = OC_Group::getUserGroups($uid);
			foreach ($groups as $group) {
				$in .= ", '".$uid."@".$group."'";
			}
		}
		if ($includePrivateLinks) {
			$in .= ", '".self::PUBLICLINK."'";
		}
		$in .= ")";
		return $in;
	}


	 /**
	 * Get the item with the specified source location
	 * @param $filepath The source location of the item
	 * @return An array with the users and permissions the item is shared with
	 */
	public static function getMyInvitedItem($filepath) {
		$filepath = self::cleanPath($filepath);
		$query = OCP\DB::prepare("SELECT uid_commenting_with FROM *PREFIX*commenting WHERE filepath = ? AND uid_owner = ?");
		$result = $query->execute(array($filepath, OCP\USER::getUser()))->fetchAll();
		if (count($result) > 0) {
			return $result;
		} else {
			return false;
		}
	}

}

?>
