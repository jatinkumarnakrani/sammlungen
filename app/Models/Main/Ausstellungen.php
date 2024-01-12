<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Ausstellungen extends Model {
    protected $table = 'ausstellungen';
    protected $primaryKey = 'id';

    public static function getInstance(){
        return new self();
    }
    
    function getActiveAusstellungen() {
        return $this->getInstance()->where('status', 1)
        ->orderBy('sortierdatum', 'DESC')
        ->get();
    }
}
?>