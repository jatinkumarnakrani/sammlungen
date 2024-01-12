<?php

namespace App\Models\Sodadmin;

class Objekte extends AdminModel {
    protected $table = 'objekte';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public static function getInstance(){
        return new self();
    }

    function getRecentObjekte() {
        return $this->getInstance()->select('objekte.*', 'sammlungen.titel as sammlungtitel')
            ->leftJoin('sammlungen', 'objekte.sammlungid', '=', 'sammlungen.id')
            ->orderBy('objekte.id', 'DESC')
            ->limit(5)
            ->get();
    }

    function getObjekte() {
        return $this->getInstance()->select('objekte.*', 'sammlungen.titel as sammlungtitel')
            ->leftJoin('sammlungen', 'objekte.sammlungid', '=', 'sammlungen.id')
            ->orderBy('objekte.id', 'DESC')
            ->get();
    }

    function loadObject($id) {
        return $this->getInstance()
            ->where('id', $id)
            ->first() ?? new static;
    }
}
?>