<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Kategorien extends Model {
    protected $table = 'kategorien';
    protected $primaryKey = 'id';

    public static function getInstance(){
        return new self();
    }

    function objektartCategories() {
        return $this->getInstance()
            ->select('id', 'titel')
            ->where(['typ' => 'Objektart','status' => 1])
            ->orderBy('titel', 'ASC')
            ->get();
    }
    
    function materialCategories() {
        return $this->getInstance()
            ->select('id', 'titel')
            ->where(['typ' => 'Material','status' => 1])
            ->orderBy('titel', 'ASC')
            ->get();
    }
    
    function datierungCategories() {
        return $this->getInstance()
            ->select('id', 'titel')
            ->where(['typ' => 'Datierung','status' => 1])
            ->orderBy('sortiernummer', 'ASC')
            ->get();
    }

    function getAllCatagories() {
        return $this->getInstance()
            ->where('status',1)
            ->orderBy('titel', 'ASC')
            ->get();
    }

    function getKategoriByNicename($nicename = null) {
        if (empty($nicename)) {
            return $this->getInstance();
        }
        return $this->getInstance()
            ->where('nicename', $nicename)
            ->where('status', 1)
            ->first();
    }
}
?>