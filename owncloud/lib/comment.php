<?php


class OC_Comment {

    public static function getCommentingUsers($filepath){
        $query = OC_DB::prepare("SELECT uid FROM *PREFIX*commentingUsers WHERE filepath = ?");
        $result = $query->execute(array($filepath));
        $users=array();
        while( $row = $result->fetchRow()){
            $users[] = $row['uid'];
        }
        return $users;
    }

    public static function commenting($uid,$filepath){
        $users = self::getCommentingUsers($filepath);
        if(in_array($uid,$users)){
            return;
        }
        $query = OC_DB::prepare("INSERT INTO *PREFIX*commentingUsers (`uid`,`filepath`) VALUES (?,?)");
        $result = $query->execute(array($uid,$filepath));
        return $result;
    }

    public static function unCommenting($uid,$filepath){
        $query = OC_DB::prepare("DELETE FROM *PREFIX*commentingUsers WHERE UID = ? AND filepath = ?");
        $result = $query->execute(array($uid,$filepath));
        return $result;
    }

    public static function getComments($filepath){
        $query = OC_DB::prepare("SELECT source FROM `*PREFIX*sharing` WHERE target = ?");
        $result = $query->execute(array($filepath));
        if($result->numRows() == 1){
            $row = $result->fetchRow();
            $filepath = $row['source'];
        }

        $query = OC_DB::prepare("SELECT id, uid_owner,uid_createdby,body,filepath FROM *PREFIX*comments WHERE filepath = ?");
        $result = $query->execute(array($filepath));
        $comments=array();
        while( $row = $result->fetchRow()){
            $comments[] = $row;
        }
        return $comments;
    }

    public static function checkCanComment($user, $filepath){
        $query = OC_DB::prepare("SELECT source,uid_owner FROM `*PREFIX*sharing` WHERE target = ?");
        $result = $query->execute(array($filepath));
        $owner = '';
        if($result->numRows() == 1){
            $row = $result->fetchRow();
            $filepath = $row['source'];
            $owner = $row['uid_owner'];
        }
        if($owner != $user) {
            $query = OC_DB::prepare("SELECT uid_owner FROM *PREFIX*commenting WHERE uid_commenting_with = ? AND filepath = ?");
            $result = $query->execute(array($user, $filepath));
            if ($result->numRows() == 0) {
                throw new Exception("Comment not allowed.");
            }
            $row = $result->fetchRow();
            return $row['uid_owner'];
        }
    }

    public  static function addComment($owner,$user,$filepath,$body){
        $query = OC_DB::prepare("SELECT source,uid_owner FROM `*PREFIX*sharing` WHERE target = ?");
        $result = $query->execute(array($filepath));
        $owner = '';
        if($result->numRows() == 1){
            $row = $result->fetchRow();
            $filepath = $row['source'];
            $owner = $row['uid_owner'];
        }

        $query = OC_DB::prepare("INSERT INTO `*PREFIX*comments` (`uid_owner`,`uid_createdby`,`body`,`filepath`) VALUES(?,?,?,?)");
        $result = $query->execute( array( $owner, $user,$body,$filepath));
        return $result ? true : false;
    }

    public static function deleteComment($id){
        $query = OC_DB::prepare("DELETE FROM `*PREFIX*comments` WHERE id = ?");
        $result = $query->execute(array($id));
        return $result ? true : false;
    }

    public static function getSource($filepath){
        $query = OC_DB::prepare("SELECT source FROM `*PREFIX*sharing` WHERE target = ?");
        $result = $query->execute(array($filepath));
        if($result->numRows() == 1){
            $row = $result->fetchRow();
            $filepath = $row['source'];
            return $filepath;
        }
        return '';
    }
}