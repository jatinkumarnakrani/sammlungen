<?php

namespace App\Models\Sodadmin;

class Veranstaltungen extends AdminModel {
    protected $table = 'veranstaltungen';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public static function getInstance(){
        return new self();
    }

    function loadVeranstaltungen($id = null) {
        return $this->getInstance()
            ->where('id', $id)
            ->first() ?? new static;

    }

    function getAllVeranstaltungen() {
        return $this->getInstance()
            ->orderBy('id', 'DESC')
            ->get();
    }
}
?>