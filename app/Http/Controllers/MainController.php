<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Main\Sammlungen;
use App\Models\Main\Veranstaltungen;
use App\Models\Main\Objekte;
use App\Models\Main\Objektemeta;
use App\Models\Main\Seiten;
use App\Models\Main\Kategorien;
use App\Models\Main\Suchen;
use App\Models\Main\Ausstellungen;

use App\helper;

class MainController extends Controller {
    
    public function index() {
        $veranstaltungen = new Veranstaltungen();
        $sammlungen = new Sammlungen();
        $objekte = new Objekte();
        $kategorien = new Kategorien();

        return view('main.index')->with([
            'leftStartveranstaltungen' => $veranstaltungen->getLeftStartSammlungen(),
            'middleStartSammlungen' => $sammlungen->getMiddleStartSammlungen(),
            'objectRendomImages' => $objekte->getObjectRendomImages(),
            'sammlungenCategories' => $sammlungen->sammlungenCategories(),
            'objektartCategories' => $kategorien->objektartCategories(),
            'materialCategories' => $kategorien->materialCategories(),
            'datierungCategories' => $kategorien->datierungCategories(),
        ]);
    }

    function neuigkeiten($page = 1) {
        $veranstaltungen = new Veranstaltungen();
        $seiten = new Seiten();
        return view('main.neuigkeiten')->with([
            'activeVeranstaltungen' => $veranstaltungen->getActiveVeranstaltungen($page),
            'newsStart' => $seiten->getDataById(19),
        ]);
    }
    
    function hintergrund() {
        $seiten = new Seiten();
        return view('main.seite')->with([
            'seiten' => $seiten->getDataById(1),
        ]);
    }
    
    function seite($nicename = null) {
        $seiten = new Seiten();
        return view('main.seite')->with([
            'seiten' => $seiten->getDataByNicename($nicename),
        ]);
    }

    function sammlungen($sort = null) {
        $sammlungen = new Sammlungen();
        if ($sort != null && !array_key_exists($sort, $sammlungen->getSammlungenSort())) {
            abort(404);
        }
        return view('main.sammlungen')->with([
            'sortId' => $sort,
            'getSammlungenSort' => $sammlungen->getSammlungenSort(),
            'getActiveSammlungen' => $sammlungen->getActiveSammlungen($sort),
        ]);
    }
    
    function projekte($sort = null) {
        $seiten = new Seiten();
        return view('main.projekte')->with([
            'seiten' => $seiten->getDataById(3),
        ]);
    }
    
    function sammlung($nicename) {
        $sammlungen = new Sammlungen();
        $objekte = new Objekte();
        $objektemeta = new Objektemeta();
        $sammlungen = $sammlungen->getSammlungByNicename($nicename);
        if (empty($sammlungen->id)) {
            abort(404);
        }
        return view('main.sammlung')->with([
            'sammlungen' => $sammlungen,
            'objekte' => $objekte->getObjekteBySammlungId($sammlungen->id),
            'objektemeta' => $objektemeta
        ]);
    }

    function objekte() {
        $objekte = new Objekte();
        $seiten = new Seiten();
        $kategorien = new Kategorien();
        $sammlungen = new Sammlungen();
        return view('main.objekte')->with([
            'objekte' => $objekte->getRendomObjekte(),
            'seiten' => $seiten->getDataById(7),
            'kategorien' => $kategorien,
            'objectRendomImages' => $objekte->getObjectRendomImages(),
            'sammlungenCategories' => $sammlungen->sammlungenCategories(),
            'objektartCategories' => $kategorien->objektartCategories(),
            'materialCategories' => $kategorien->materialCategories(),
            'datierungCategories' => $kategorien->datierungCategories(),
        ]);
    }
    
    function objekt($nicename) {
        $objekte = new Objekte();
        $objektemeta = new Objektemeta();
        $sammlungen = new Sammlungen();
        $objekte = $objekte->getObjektByNicename($nicename);
        $objektemeta = $objektemeta->getCatagoryByObjekte($objekte);

        if(empty($objekte->id)){
            abort(404);
        }
        return view('main.objekt')->with([
            'objekte' => $objekte,
            'objektemeta' => $objektemeta,
            'weiterObjekte' => $objekte->getRandomObjeks(),
            'sammlungsKategories' => $objekte->getSammlungsKategories(),
            'sammlungen' => $sammlungen->getSammlungById($objekte->sammlungid)
        ]);
    }
    
    function suchenCheck(Request $request) {
        $suchen = $request->input('suchen');
        $suchenObj = new Suchen();
        $searcheddata = $suchenObj->getSearchedData($suchen);
        $data = [
            'url' => route('main.search'),
            'searchedCount' => ($searcheddata && $searcheddata->count()) ? $searcheddata->count() : 0
        ];
        return response()->json($data);
    }
    
    function suchen(Request $request) {
        $suchen = $request->input('suchen',array('input_search'=> '', 'sammlungen' => 0, 'objektart' => 0, 'material' => 0, 'datierung' => 0 ));
        $suchenObj = new Suchen();
        $suchenData = $suchenObj->getSearchedData($suchen);

        $kategorien = new Kategorien();
        $sammlungen = new Sammlungen();
        $objektemeta = new Objektemeta();

        return view('main.suchen')->with([
            'sammlungenCategories' => $sammlungen->sammlungenCategories(),
            'objektartCategories' => $kategorien->objektartCategories(),
            'materialCategories' => $kategorien->materialCategories(),
            'datierungCategories' => $kategorien->datierungCategories(),
            'suchenData' => $suchenData,
            'objektemeta' => $objektemeta,
            'suchen' => (object) $suchen
        ]);
    }

    function ausstellungen() {
        $seiten = new Seiten();
        $ausstellungen = new Ausstellungen();
        return view('main.ausstellungen')->with([
            'seiten' => $seiten->getDataById(6),
            'ausstellungens' => $ausstellungen->getActiveAusstellungen()
        ]);
    }

    function favorite() {

        $objekte = new Objekte();
        $objektemeta = new Objektemeta();
  
        return view('main.favorite')->with([
            'objekte' => $objekte->getObjektByIds(getFavorites()),
            'objektemeta' => $objektemeta,
        ]);
    }

    function addFavorite(Request $request) {
        $id = $request->input('id');

        $favorites = session()->get('favorites', []);
        if(!in_array($id,$favorites)) {
            $favorites[] = $id;
        }else {
            if (($key = array_search($id, $favorites)) !== false) {
                unset($favorites[$key]);
            }
        }
        session()->put('favorites', $favorites);

        return response()->json($favorites);
    }

    function kategorie($nicename = null) {
        $kategorien = new Kategorien();
        $objektemeta = new Objektemeta();
        $kategorien = $kategorien->getKategoriByNicename($nicename);
  
        return view('main.kategorie')->with([
            'kategorien' => $kategorien,
            'objekte' => $objektemeta->getObjectsByKategory($kategorien),
            'objektemeta' => $objektemeta,
        ]);
    }
}
