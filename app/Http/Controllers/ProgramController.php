<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Programme;
use App\Models\Activite;
use App\Models\Titreprogramme;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProgramController extends Controller
{
    /*********************** START GESTION DES ACTIVITES *******************/
    public function createactivite(Request $req){
        $error=0;
        $verif_activite = DB::table('activites')->where('libelleactivite',$req->libelleactivite)->get();
        $res = json_decode($verif_activite,true);
        if(sizeof($res) === 0)
        {
        $activite= Activite::create($req->all());
        return response()->json($activite->id);
        }
        else
        {
            return response()->json($verif_activite[0]->id);
        }
    }

    public function getactivite(){
        $activites = DB::table('activites')->get();
        return response()->json($activites,200);
    }
    /*********************** END GESTION DES ACTIVITES *******************/

    /*********************** START GESTION DES PROGRAMMES *******************/
    public function createprogramme(Request $req){

        $programme= Programme::create($req->all());
        return response()->json($programme,200);
    }

    public function getprogramme(){
        $programmes = DB::table('programmes')->get();
        return response()->json($programmes,200);
    }
    /*********************** END GESTION DES PROGRAMMES *******************/

    /*********************** START GESTION DES TITRES DE PROGRAMMES *******************/
    function getweek(){
        setlocale(LC_TIME, 'fr_FR.UTF8');
        $annee = date("Y");
        $no_semaine = date("W");
        if(date("w")>=6){
            $no_semaine = date("W")+1;
        }
        // Récup jour début et fin de la semaine
        $timeStart = strtotime("First Monday January {$annee} + ".($no_semaine - 1)." Week");
        $timeEnd   = strtotime("First Monday January {$annee} + {$no_semaine} Week -1 day");
        
        // Récup année et mois début
        $anneeStart = date("Y", $timeStart);
        $anneeEnd   = date("Y", $timeEnd);
        $moisStart  = date("m", $timeStart);
        $moisEnd    = date("m", $timeEnd);
        
        // Gestion des différents cas de figure
        if( $anneeStart != $anneeEnd ){
            // à cheval entre 2 années
            $retour = "du ".strftime("%d %B %Y", $timeStart)." au ".strftime("%d %B %Y", $timeEnd);
        } elseif( $moisStart != $moisEnd ){
            // à cheval entre 2 mois
            $retour = "du ".strftime("%d %B", $timeStart)." au ".strftime("%d %B %Y", $timeEnd);
        } else {
            // même mois
            $retour = "PROGRAMME DE LA SEMAINE : ".strftime("%d", $timeStart)." au ".strftime("%d %B %Y", $timeEnd);
        }
        // return $retour;
        return response()->json($retour,200);
    }

    public function createtitreprogramme(Request $req){
        $error=0;
        $verif_programme = DB::table('titreprogrammes')->where('titreprogramme',$req->titreprogramme)->get();
        $res = json_decode($verif_programme,true);
        if(sizeof($res) === 0)
        {
        $titreprogramme= Titreprogramme::create($req->all());
        return response()->json($titreprogramme->id);
        }
        else
        {
            return response()->json($verif_programme[0]->id);
        }
    }
    /*********************** END GESTION DES TITRES DE PROGRAMMES *******************/
}
