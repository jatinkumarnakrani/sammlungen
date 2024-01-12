<?php

namespace App\Models\Sodadmin;

class Kategorien extends AdminModel {
    protected $table = 'kategorien';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public static function getInstance(){
        return new self();
    }
    function objektartCategories() {
        return $this->getInstance()
            ->where(['typ' => 'Objektart'])
            ->orderBy('titel', 'ASC')
            ->get();
    }
    
    function materialCategories() {
        return $this->getInstance()
            ->where(['typ' => 'Material'])
            ->orderBy('titel', 'ASC')
            ->get();
    }
    
    function datierungCategories() {
        return $this->getInstance()
            ->where(['typ' => 'Datierung'])
            ->orderBy('sortiernummer', 'ASC')
            ->get();
    }

    function updateCountbyIds(array $ids) {
        if (!empty($ids)) {
            $this->getInstance()
                ->whereIn('id', $ids)
                ->update(['count' => \DB::raw('count + 1')]);
        }
    }

    function getAllCategory() {
        return $this->getInstance()
            ->orderBy('titel', 'ASC')
            ->get();
    }

    function decreesCountbyIds(array $ids) {
        if (!empty($ids)) {
            $this->getInstance()
                ->whereIn('id', $ids)
                ->update(['count' => \DB::raw('count - 1')]);
        }
    }

    function loadKategory($id = null) {
        return $this->getInstance()
            ->where('id', $id)
            ->first() ?? new static;

    }
}
?>