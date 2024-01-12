<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;

class Sammlungen extends Model {
    protected $table = 'sammlungen';
    protected $primaryKey = 'id';

    public static function getInstance(){
        return new self();
    }

    function getMiddleStartSammlungen() {
        return $this->getInstance()->where(['status' => 1, 'thumbnail' => '1'])
            ->inRandomOrder()
            ->limit(1)
            ->first(['id', 'nicename', 'titel', 'kurzbeschreibung']);
    }

    function getActiveSammlungen($sort = null) {
        $sort = preg_replace("/[^0-9]/", '', $sort);
        $model = $this->getInstance()->select('id', 'nicename', 'titel', 'kurztitel', 'thumbnail')
            ->where('status', 1);
        
        if ($sort == 1) {
            $model->orderBy('sortierwort', 'DESC');
        }
        if ($sort == 2) {
            $model= $this->getInstance()->select(
                    'sammlungen.id',
                    'sammlungen.nicename',
                    'sammlungen.fachbereichid',
                    'sammlungen.titel',
                    'sammlungen.kurztitel',
                    'sammlungen.thumbnail',
                    'fachbereiche.name AS fachbereich',
                )
                ->leftJoin('fachbereiche', 'sammlungen.fachbereichid', '=', 'fachbereiche.id')
                ->whereRaw('sammlungen.status =  1')
                ->orderBy('fachbereiche.ordnungszahl', 'ASC');
        }
        if ($sort == 3) {
            $model->orderBy('standort', 'ASC');
        }

        if(!in_array($sort,array(1,2,3))){
            $model->orderBy('sortierwort', 'ASC');
        }
        return $model->get();
    }
    
    function sammlungenCategories() {
        return $this->getInstance()
            ->select('id', 'kurztitel')
            ->where('status', 1)
            ->orderBy('titel', 'ASC')
            ->get();
    }

    function getSammlungenSort() {
        return array(
            "0" => "von A - Z",
            "sort/1" => "von Z - A",
            "sort/2" => "nach Fachbereichen",
            "sort/3" => "nach Uni-Standorten",
        );
    }

    function getSammlungById($id) {
        return $this->getInstance()->find($id);
    }
    
    function getSammlungByNicename($nicename) {
        return $this->getInstance()
            ->where(['status' => 1, 'nicename' => $nicename])
            ->first();
    }

    // function getLeftStartSammlungen() {
    //     return $this->getInstance()->where('status', 1)
    //         ->orderBy('titel', 'ASC')
    //         ->get(['id', 'titel']);
    // }

}
?>