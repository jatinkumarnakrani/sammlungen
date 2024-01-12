<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Objekte extends Model {
    protected $table = 'objekte';
    protected $primaryKey = 'id';

    public static function getInstance(){
        return new self();
    }

    function getObjectRendomImages() {
        return $this->getInstance()->select('id', 'nicename', 'thumbnail', 'titel')
            ->where('thumbnail', '1')
            ->where('status', 1)
            ->inRandomOrder()
            ->limit(10)
            ->get();
    }

    function getObjekteBySammlungId($sammlungId) {
        return $this->getInstance()
            ->where(['status' => 1, 'sammlungid' => $sammlungId])
            ->orderBy('id','DESC')
            ->get();
    }

    function getRendomObjekte() {
        return $this->getInstance()
            ->where(['thumbnail' => 1, 'status' => 1])
            ->inRandomOrder()
            ->first();
    }

    function getObjektByNicename($nicename) {
        return $this->getInstance()
            ->where(['nicename' => $nicename, 'status' => 1])
            ->first();
    }

    function getRandomObjeks() {
        return $this->getInstance()
            ->where(['sammlungid' => $this->getAttribute('sammlungid'), 'status' => 1])
            ->whereNot('id',[$this->getAttribute('id')])
            ->inRandomOrder()
            ->limit(2)
            ->get();
        }
        
    function getSammlungsKategories() {
        return $this->getInstance()
            ->where(['sammlungskategorie' => $this->getAttribute('sammlungskategorie'), 'status' => 1])
            ->whereNot('id',[$this->getAttribute('id')])
            ->inRandomOrder()
            ->limit(8)
            ->get();
    }

    function getObjektByIds($ids) {
        return $this->getInstance()
            ->where('status', 1)
            ->whereIn('id', $ids)
            ->get();
    }
}
?>