<?php

namespace App\Models\Main;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Suchen extends Model {

    public static function getInstance(){
        return new self();
    }

    function getSearchedData($searchdata = array()) {
        $searchedText = NULL;
        $sammlungenId = NULL;
        $metaValue = array();

        if (isset($searchdata['input_search']) && !empty($searchdata['input_search'])) {
            $searchedText = $searchdata['input_search'];
        }
        if (isset($searchdata['sammlungen']) && !empty($searchdata['sammlungen'])) {
            $sammlungenId = $searchdata['sammlungen'];
        }

        if (isset($searchdata['objektart']) && !empty($searchdata['objektart'])) {
            $metaValue[] = $searchdata['objektart'];
        }
        if (isset($searchdata['material']) && !empty($searchdata['material'])) {
            $metaValue[] = $searchdata['material'];
        }
        if (isset($searchdata['datierung']) && !empty($searchdata['datierung'])) {
            $metaValue[] = $searchdata['datierung'];
        }

        if ($searchedText || !empty($metaValue) || $sammlungenId) {
            $model = DB::table('objekte as obj')
                ->leftjoin('sammlungen as sml',  'obj.sammlungid', '=', 'sml.id')
                ->leftjoin('objektemeta as objmeta', 'obj.id', '=', 'objmeta.postid')
                ->whereRaw('obj.status =  1')
                ->groupBy('obj.id', 'obj.titel', 'obj.nicename', 'obj.beschreibung', 'obj.thumbnail', 'sammlungtitel')
                ->orderBy('obj.titel', 'ASC')
                ->select(['obj.id', 'obj.titel', 'obj.nicename', 'obj.beschreibung', 'obj.thumbnail', 'sml.titel as sammlungtitel']);

            if ($searchedText) {
                $model->where(function ($query) use ($searchedText) {
                    $query->orWhereRaw("obj.titel LIKE '%{$searchedText}%'")
                    ->orWhereRaw("obj.beschreibung LIKE '%{$searchedText}%'")
                    ->orWhereRaw("obj.verfasser LIKE '%{$searchedText}%'")
                    ->orWhereRaw("obj.autorinformationen LIKE '%{$searchedText}%'")
                    ->orWhereRaw("obj.schlagwoerter LIKE '%{$searchedText}%'");       
                });
            }

            if($sammlungenId){
                $model->whereRaw("obj.sammlungid = ".$sammlungenId);
            }

            if (count($metaValue) > 0) {
                $model->whereRaw('objmeta.metakey = "category"')
                    ->whereRaw("objmeta.metavalue IN (".implode(',',  $metaValue).")");
            }

            // echo $model->toSql();die;
            $data = $model->get();
            if ($data->count()) {
                return $data;
            }
        }
        return NULL;
    }
}
?>