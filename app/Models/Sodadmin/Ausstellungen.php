<?php

namespace App\Models\Sodadmin;

class Ausstellungen extends AdminModel {
    protected $table = 'ausstellungen';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public static function getInstance(){
        return new self();
    }

    function loadAusstellungen($id = null) {
        return $this->getInstance()
            ->where('id', $id)
            ->first() ?? new static;

    }

    function getAllAusstellungen() {
        return $this->getInstance()
            ->orderBy('id', 'DESC')
            ->get();
    }
}
?>