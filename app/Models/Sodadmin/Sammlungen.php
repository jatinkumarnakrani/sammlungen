<?php

namespace App\Models\Sodadmin;

class Sammlungen extends AdminModel {
    protected $table = 'sammlungen';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public static function getInstance(){
        return new self();
    }

    function getSammlungenCategories() {
        return $this->getInstance()
            ->select('id', 'kurztitel')
            ->orderBy('titel', 'ASC')
            ->get();
    }

    function getSammlungen() {
        return $this->getInstance()
            ->orderBy('id', 'DESC')
            ->get();
    }
    
    function loadSammlungen($id) {
        return $this->getInstance()
            ->where('id', $id)
            ->first() ?? new static;
    }
}
?>