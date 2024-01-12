<?php

namespace App\Models\Sodadmin;

class Seiten extends AdminModel {
    protected $table = 'seiten';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public static function getInstance(){
        return new self();
    }

    function loadSeiten($id = null) {
        return $this->getInstance()
            ->where('id', $id)
            ->first() ?? new static;

    }

    function getAllSeiten() {
        return $this->getInstance()
            ->where('parent_id', 0)
            ->orderBy('sortiernummer', 'ASC')
            ->get();
    }

    function getChildSeite($parentID) {
        return $this->getInstance()
            ->where('parent_id', $parentID)
            ->orderBy('sortiernummer', 'ASC')
            ->get();
    }

    function getUnterseiteVon() {
        return $this->getInstance()
            ->select('id', 'titel', 'parent_id')
            ->where('parent_id', 0)
            ->where('special', 2)
            ->orderBy('titel', 'ASC')
            ->get();
    }
}
?>