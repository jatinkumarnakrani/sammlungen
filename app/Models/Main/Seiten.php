<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Seiten extends Model {
    protected $table = 'seiten';
    protected $primaryKey = 'id';

    public static function getInstance(){
        return new self();
    }

    function getDataById($id) {
        return $this->getInstance()->find($id);
    }
    
    function getDataByNicename($nicename) {
        return $this->getInstance()
            ->where('nicename', $nicename)
            ->where('status', 1)
            ->first();
    }
}
?>