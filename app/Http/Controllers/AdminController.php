<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Sodadmin\Objekte;
use App\Models\Sodadmin\Objektemeta;
use App\Models\Sodadmin\Sammlungen;
use App\Models\Sodadmin\Kategorien;
use App\Models\Sodadmin\Seiten;
use App\Models\Sodadmin\Ausstellungen;
use App\Models\Sodadmin\Vaausstellungen;
use App\Models\Sodadmin\Veranstaltungen;
use App\Models\Sodadmin\Artikel;
use App\Models\Sodadmin\Bilder;

class AdminController extends Controller {

    public function index() {
        return view('admin.dashboard');
    }
    
    public function dashboard() {
        $objekte = new Objekte();
        return view('sodadmin.index')->with([
            'getRecentObjekte' => $objekte->getRecentObjekte(),
        ]);
    }
    
    function objekt($id = null) {
        $sammlungen = new Sammlungen();
        $kategorien = new Kategorien();
        $objekte = new Objekte();
        $objekteMeta = new Objektemeta();

        return view('sodadmin.objekt')->with([
            'getSammlungenCategories' => $sammlungen->getSammlungenCategories(),
            'kategorien' => $kategorien,
            'objekte' => $objekte->loadObject($id),
            'objekteMeta' => $objekteMeta,
            'id' => $id
        ]);
    }

    function objektSave(Request $request) {
        $objekte = new Objekte();
        $id = $request->input('id');

        $data = $request->input('objekt');
        $data['nicename'] = Str::slug($data['titel']);

        if($id) {
            $objekte = $objekte->loadObject($id);
            if($objekte->id){
                $objekte->setData($data)->update();

                $thumbnail = $request->file('thumbnail');
                if ($thumbnail) {
                    $filePath = public_path('img/objekte/middle/'.$objekte->id.'.jpg');
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    $filename = $objekte->id . '.' . $thumbnail->getClientOriginalExtension();
                    $thumbnail->move(public_path('img/objekte/middle/'), $filename);
                }

                $objektfotografie = $request->file('objektfotografie');
                if ($objektfotografie) {
                    $filePath = public_path('img/objekte/'.$objekte->id.'.jpg');
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    $filename = $objekte->id . '.' . $objektfotografie->getClientOriginalExtension();
                    $objektfotografie->move(public_path('img/objekte/'), $filename);
                }

                $objekteMeta = new Objektemeta();
                $objekteMeta->deleteObjektMetaPostId($objekte->id);
            }
        }else{
            $data['bilder'] = '';
            $data['standort'] = '';
            $data['epochenid'] = 0;
            $data['kategorienids'] = '';
            $data['sammlungskategorie'] = '';
            $objekte->setData($data)->save();
            
        }
        
        $category = $request->input('category');
        if(!empty($category) && $objekte->id) {
            $kategorien = new Kategorien();
            $kategorien->updateCountbyIds($request->input('category'));

            foreach ($category as $categoryId) {
                $objekteMeta = new Objektemeta();
                $objekteMeta->postid = $objekte->id;
                $objekteMeta->metakey = 'category';
                $objekteMeta->metavalue = $categoryId;
                $objekteMeta->save();
            }
        }

        return redirect()->intended(route('admin.objekte'));
    }

    function objektDelete($id = null) {
        $objekte = new Objekte();
        $objekte = $objekte->loadObject($id);

        if (!$objekte) {
            return redirect()->route('admin.objektId', ['id' => $id])->with('error', 'Object not found.');
        }

        $objekteMeta = new Objektemeta();
        $categoryIds = $objekteMeta->loadObjectMetaByPostId($objekte->id);
        $objekteMeta->deleteObjektMetaPostId($objekte->id);
        
        $kategorien = new Kategorien();
        $kategorien->decreesCountbyIds($categoryIds);

        $objekte->delete();

        return redirect()->route('admin.objekte')->with('success', 'Object deleted successfully.');
    }

    function sammlung($id = null) {
        $sammlungen = new Sammlungen();

        return view('sodadmin.sammlung')->with([
            'sammlungen' => $sammlungen->loadSammlungen($id),
            'id' => $id
        ]);
    }
    
    function sammlungsave(Request $request) {
        $sammlungen = new Sammlungen();

        $id = $request->input('id');
        $sammlungen = $sammlungen->loadSammlungen($id);

        $data = $request->input('sammlung');
        $data['nicename'] = Str::slug($data['titel']);

        if($sammlungen->id) {
            $sammlungen->setData($data)->update();
            
            $thumbnail = $request->file('thumbnail');
            if ($thumbnail) {
                $filePath = public_path('img/sammlungen/'.$sammlungen->id.'.jpg');
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $filename = $sammlungen->id . '.' . $thumbnail->getClientOriginalExtension();
                $thumbnail->move(public_path('img/sammlungen/'), $filename);
            }

        }else{
            $data['fachbereichid'] = 0;
            $sammlungen->setData($data)->save();
        }

        return redirect()->intended(route('admin.sammlungen'));
    }

    function objekte() {
        $objekte = new Objekte();
        return view('sodadmin.objekte')->with([
            'getObjekte' => $objekte->getObjekte()
        ]);
    }

    function sammlungenDelete($id = null) {
        $sammlungen = new Sammlungen();
        $sammlungen = $sammlungen->loadSammlungen($id);
        if($sammlungen->id) {
            $sammlungen->delete();
        }

        return redirect()->intended(route('admin.sammlungen'));
    }
    
    function sammlungen() {
        $sammlungen = new Sammlungen();
        return view('sodadmin.sammlungen')->with([
            'getSammlungen' => $sammlungen->getSammlungen()
        ]);
    }

    function kategory($id = null) {
        $kategorien = new Kategorien();
        return view('sodadmin.kategory')->with([
            'kategory' => $kategorien->loadKategory($id),
            'id' => $id
        ]);
    }
    
    function kategorySave(Request $request) {
        $kategorien = new Kategorien();

        $id = $request->input('id');
        $kategorien = $kategorien->loadKategory($id);

        $data = $request->input('kategory');
        $data['nicename'] = Str::slug($data['titel']);
        
        if($kategorien->id) {
            $kategorien->setData($data);
            $kategorien->update();
        }else{
            $data['sortiernummer'] = '';
            $kategorien->setData($data);
            $kategorien->save();
        }

        return redirect()->intended(route('admin.kategorien'));
    }

    function kategoryDelete($id = null) {
        $kategorien = new Kategorien();
        $kategorien = $kategorien->loadKategory($id);
        if($kategorien->id) {
            $kategorien->delete();
        }

        return redirect()->intended(route('admin.kategorien'));
    }

    function kategorien() {
        $kategorien = new Kategorien();
        return view('sodadmin.kategorien')->with([
            'getAllCategory' => $kategorien->getAllCategory()
        ]);
    }

    function seite($id = null) {
        $seiten = new Seiten();
        return view('sodadmin.seite')->with([
            'id' => $id,
            'seiten' => $seiten->loadSeiten($id)
        ]);
    }

    function seiteSave(Request $request) {
        $seiten = new Seiten();

        $id = $request->input('id');
        $seiten = $seiten->loadSeiten($id);

        $data = $request->input('seiten');
        $data['nicename'] = Str::slug($data['titel']);
        $seiten->setData($data);

        if($seiten->id) {
            $seiten->update();
        }else{
            $seiten->save();
        }

        return redirect()->intended(route('admin.seiten'));
    }
    
    function seitenDelete($id = null) {
        $seiten = new Seiten();
        $seiten = $seiten->loadSeiten($id);
        if($seiten->id && $seiten->parent_id != 0) {
            $seiten->delete();
        }
        return redirect()->intended(route('admin.seiten'));
    }

    function seiten() {
        $seiten = new Seiten();
        return view('sodadmin.seiten')->with([
            'seiten' => $seiten,
            'getAllSeiten' => $seiten->getAllSeiten()
        ]);
    }

    function ausstellungen() {
        $ausstellungen = new Ausstellungen();
        return view('sodadmin.ausstellungen')->with([
            'getAllAusstellungen' => $ausstellungen->getAllAusstellungen()
        ]);
    }

    function ausstellungSave(Request $request) {
        $ausstellungen = new Ausstellungen();

        $id = $request->input('id');
        $ausstellungen = $ausstellungen->loadAusstellungen($id);

        $data = $request->input('ausstellungen');
        $data['nicename'] = Str::slug($data['titel']);
        
        
        if($ausstellungen->id) {
            $ausstellungen->setData($data)->update();
            $bild = $request->file('bild');
            if ($bild) {
                $filePath = public_path('img/ausstellungen/'.$ausstellungen->id.'.jpg');
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $filename = $ausstellungen->id . '.' . $bild->getClientOriginalExtension();
                $bild->move(public_path('img/ausstellungen/'), $filename);
            }

        }else{
            $data['thumbnail'] = '';
            $ausstellungen->setData($data)->save();
        }

        return redirect()->intended(route('admin.ausstellungen'));
    }

    function ausstellungDelete($id = null) {
        $ausstellungen = new Ausstellungen();
        $ausstellungen = $ausstellungen->loadAusstellungen($id);
        if($ausstellungen->id) {
            $ausstellungen->delete();
        }
        return redirect()->intended(route('admin.ausstellungen'));
    }

    function ausstellung($id = null) {
        $ausstellungen = new Ausstellungen();
        return view('sodadmin.ausstellung')->with([
            'id' => $id,
            'ausstellungen' => $ausstellungen->loadAusstellungen($id),
        ]);
    }

    function virtuelleAusstellungen() {
        $vaausstellungen = new Vaausstellungen();
        return view('sodadmin.vaausstellungen')->with([
            'getAllVaAusstellungen' => $vaausstellungen->getAllVaAusstellungen()
        ]);
    }

    function virtuelleAusstellung($id = null) {
        $vaausstellungen = new Vaausstellungen();
        return view('sodadmin.vaausstellung')->with([
            'id' => $id,
            'vaausstellungen' => $vaausstellungen->loadVaAusstellungen($id),
        ]);
    }
    
    function virtuelleAusstellungSave(Request $request) {
        $vaausstellungen = new Vaausstellungen();

        $id = $request->input('id');
        $vaausstellungen = $vaausstellungen->loadVaAusstellungen($id);

        $data = $request->input('vaausstellungen');
        $data['nicename'] = Str::slug($data['titel']);
        
        
        if($vaausstellungen->id) {
            $vaausstellungen->setData($data)->update();
            $bild = $request->file('bild');
            if ($bild) {
                $filePath = public_path('img/va-ausstellungen/'.$vaausstellungen->id.'.jpg');
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $filename = $vaausstellungen->id . '.' . $bild->getClientOriginalExtension();
                $bild->move(public_path('img/va-ausstellungen/'), $filename);
            }
        }else{
            $data['thumbnail'] = 0;
            $vaausstellungen->setData($data)->save();
        }

        return redirect()->intended(route('admin.virtuelleAusstellungen'));
    }
    
    function virtuelleAusstellungDelete($id = null) {
        $vaausstellungen = new Vaausstellungen();
        $vaausstellungen = $vaausstellungen->loadVaAusstellungen($id);
        if($vaausstellungen->id) {
            $vaausstellungen->delete();
        }
        return redirect()->intended(route('admin.virtuelleAusstellungen'));
    }

    function veranstaltungen() {
        $veranstaltungen = new Veranstaltungen();
        return view('sodadmin.veranstaltungen')->with([
            'getAllVeranstaltungen' => $veranstaltungen->getAllVeranstaltungen()
        ]);
    }
    
    function veranstaltung($id = null) {
        $veranstaltungen = new Veranstaltungen();
        return view('sodadmin.veranstaltung')->with([
            'id' => $id,
            'veranstaltungen' => $veranstaltungen->loadVeranstaltungen($id),
        ]);
    }
    
    function veranstaltungSave(Request $request) {
        $veranstaltungen = new Veranstaltungen();

        $id = $request->input('id');
        $veranstaltungen = $veranstaltungen->loadVeranstaltungen($id);

        $data = $request->input('veranstaltung');
        $data['nicename'] = Str::slug($data['titel']);

        foreach ($data as $key => $value) {
            if(empty($value) && $value != 0){
                $data[$key] = '';
            }
        }
        
        if($veranstaltungen->id) {
            $veranstaltungen->setData($data)->update();
            $bild = $request->file('bild');
            if ($bild) {
                $filePath = public_path('img/veranstaltungen/'.$veranstaltungen->id.'.jpg');
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $filename = $veranstaltungen->id . '.' . $bild->getClientOriginalExtension();
                $bild->move(public_path('img/veranstaltungen/'), $filename);
            }
        }else{
            $data['thumbnail'] = 0;
            $veranstaltungen->setData($data)->save();
        }

        return redirect()->intended(route('admin.veranstaltungen'));
    }

    function veranstaltungDelete($id = null) {
        $veranstaltungen = new Veranstaltungen();
        $veranstaltungen = $veranstaltungen->loadVeranstaltungen($id);
        if($veranstaltungen->id) {
            $veranstaltungen->delete();
        }
        return redirect()->intended(route('admin.veranstaltungen'));
    }

    function artikel() {
        $artikel = new Artikel();
        return view('sodadmin.artikel')->with([
            'getAllArtikel' => $artikel->getAllArtikel()
        ]);
    }

    function article($id = null) {
        $sammlungen = new Sammlungen();
        $artikel = new Artikel();

        return view('sodadmin.article')->with([
            'getSammlungenCategories' => $sammlungen->getSammlungenCategories(),
            'artikel' => $artikel->loadArtikel($id),
            'id' => $id
        ]);
    }

    function articleSave(Request $request) {
        $artikel = new Artikel();

        $id = $request->input('id');
        $artikel = $artikel->loadArtikel($id);

        $data = $request->input('article');
        
        if($artikel->id) {
            $artikel->setData($data)->update();
            $artikelbild = $request->file('artikelbild');
            if ($artikelbild) {
                $filePath = public_path('img/artikel/'.$artikel->id.'.jpg');
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
                $filename = $artikel->id . '.' . $artikelbild->getClientOriginalExtension();
                $artikelbild->move(public_path('img/artikel/'), $filename);
            }
        }else{
            $data['bild'] = 0;
            $artikel->setData($data)->save();
        }

        return redirect()->intended(route('admin.artikel'));
    }

    function artikelDelete($id = null) {
        $artikel = new Artikel();
        $artikel = $artikel->loadArtikel($id);
        if($artikel->id) {
            $artikel->delete();
        }
        return redirect()->intended(route('admin.artikel'));
    }

    function bilder() {
        $bilder = new Bilder();
        return view('sodadmin.bilder')->with([
            'getAllBilder' => $bilder->getAllBilder()
        ]);
    }

    function bild() {
        return view('sodadmin.bild')->with([]);
    }

    function bildSave(Request $request) {
        
        
        $file = $request->file('bildimg');
        if ($file) {
            
            // $request->validate([
                //     'file' => 'required|file|mimes:jpg,png|max:1024',
            // ]);

            if ($file->getSize() <= (1024*1024)) {
                $insertedId = Bilder::insertGetId(['dateiname' => '']);
                $filename = $insertedId . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('img/uploads'), $filename);
            }
            
        }
        return redirect()->intended(route('admin.bilder'));
    }

    function bilderDelete($id = null) {
        $bilder = new Bilder();
        $bilder = $bilder->loadBilder($id);
        if($bilder->id) {
            $bilder->delete();
        }
        return redirect()->intended(route('admin.bilder'));
    }
}