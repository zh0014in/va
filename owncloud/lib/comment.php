<?php

class OC_Comment {
    public static function getComments($filepath){
        $query = OC_DB::prepare("SELECT uid_owner,uid_createdby,body,filepath FROM *PREFIX*comments WHERE filepath = ?");
        $result = $query->execute(array($filepath));
        return $result;
    }
}