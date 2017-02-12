<?php

class OC_Comment {
    public static function getComments($filepath){
        $query = OC_DB::prepare("SELECT uid_owner,uid_createdby,body,filepath FROM *PREFIX*comments WHERE filepath = ?");
        $result = $query->execute(array($filepath));
        $comments=array();
        while( $row = $result->fetchRow()){
            $comments[] = $row;
        }
        return $comments;
    }

    public static function checkCanComment($user, $filepath){
        $query = OC_DB::prepare("SELECT uid_owner FROM *PREFIX*commenting WHERE uid_commenting_with = ? AND filepath = ?");
        $result = $query->execute(array($user,$filepath));
        if($result->numRows() == 0){
            throw new Exception("Comment not allowed.");
        }
        $row = $result->fetchRow();
        return $row['uid_owner'];
    }

    public  static function addComment($owner,$user,$filepath,$body){
        $query = OC_DB::prepare("INSERT INTO `*PREFIX*comments` (`uid_owner`,`uid_createdby`,`body`,`filepath`) VALUES(?,?,?,?)");
        $result = $query->execute( array( $owner, $user,$body,$filepath));
        return $result ? true : false;
    }

    public static function deleteComment($filepath,$body){
        $query = OC_DB::prepare("DELETE FROM `*PREFIX*comments` WHERE filepath = ? and body = ?");
        $result = $query->execute(array($filepath, $body));
        return $result ? true : false;
    }
}