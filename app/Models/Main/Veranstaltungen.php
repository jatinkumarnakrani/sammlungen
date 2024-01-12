<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Veranstaltungen extends Model {
    protected $table = 'veranstaltungen';
    protected $primaryKey = 'id';

    public static function getInstance(){
        return new self();
    }

    function getLeftStartSammlungen() {
        return $this->getInstance()->where('status', 1)
        ->orderBy('sortierdatum', 'DESC')
        ->limit(1)
        ->first();
    }
    
    function getActiveVeranstaltungen($page = 1) {
        return $this->getInstance()->where('status', 1)
        ->orderBy('sortierdatum', 'DESC')
        ->paginate(10, ['*'], 'page', $page);
        // ->get();
    }
}
?>