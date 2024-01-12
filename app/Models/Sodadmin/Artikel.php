<?php

namespace App\Models\Sodadmin;

class Artikel extends AdminModel {
    protected $table = 'artikel';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public static function getInstance(){
        return new self();
    }

    function getAllArtikel() {
        return $this->getInstance()
            ->orderBy('id','DESC')
            ->get();
    }

    function loadArtikel($id) {
        return $this->getInstance()
            ->where('id', $id)
            ->first() ?? new static;
    }
}
?>