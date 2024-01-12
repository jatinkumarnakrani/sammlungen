<?php

namespace App\Models\Sodadmin;

class Objektemeta extends AdminModel {
    protected $table = 'objektemeta';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public static function getInstance(){
        return new self();
    }

    function loadObjectMetaByPostId($postId) {
        return $this->getInstance()
            ->where('postid', $postId)
            ->pluck('metavalue')
            ->toArray();
    }

    function deleteObjektMetaPostId($postId) {
        $this->getInstance()->where('postid', $postId)->delete();
    }
}
?>