<?php

namespace App\Models\Sodadmin;

class Bilder extends AdminModel {
    protected $table = 'bilder';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public static function getInstance(){
        return new self();
    }

    function getAllBilder() {
        return $this->getInstance()
            ->orderBy('id','DESC')
            ->get();
    }

    function loadBilder($id) {
        return $this->getInstance()
            ->where('id', $id)
            ->first() ?? new static;
    }
}
?>