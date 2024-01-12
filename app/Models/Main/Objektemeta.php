<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Objektemeta extends Model {
    protected $table = 'objektemeta';
    protected $primaryKey = 'id';

    public static function getInstance(){
        return new self();
    }

    function getCatagoryByObjekte($objecte) {
        $model= $this->getInstance()
                ->where(['metakey' => 'category', 'postid' => $objecte->id])
                ->orderBy('kategorien.titel', 'ASC');
        $model->leftJoin('kategorien', 'objektemeta.metavalue', '=', 'kategorien.id')
            ->select(
                'objektemeta.metavalue',
                'kategorien.id',
                'kategorien.nicename',
                'kategorien.titel'
            );
        return $model->get();
    }

    function getObjectsByKategory($kategory) {
        return $this->getInstance()
            ->select('objektemeta.postid','objekte.id','objekte.nicename','objekte.titel','objekte.beschreibung','objekte.thumbnail')
            ->leftJoin('objekte', 'objektemeta.postid', '=', 'objekte.id')
            ->where('objektemeta.metavalue', $kategory->id)
            ->where('objektemeta.metakey', 'category')
            ->where('objekte.status', 1)
            ->orderBy('objekte.id', 'DESC')
            ->get();
    }
}
?>