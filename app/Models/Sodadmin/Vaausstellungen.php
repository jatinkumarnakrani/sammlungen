<?php

namespace App\Models\Sodadmin;

class Vaausstellungen extends AdminModel {
    protected $table = 'vaausstellungen';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public static function getInstance(){
        return new self();
    }

    function loadVaAusstellungen($id = null) {
        return $this->getInstance()
            ->where('id', $id)
            ->first() ?? new static;

    }

    function getAllVaAusstellungen() {
        return $this->getInstance()
            ->orderBy('id', 'DESC')
            ->get();
    }
}
?>