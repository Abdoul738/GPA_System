<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Programme;
use App\Models\Activite;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProgramController extends Controller
{
    // GESTION DES ACTIVITES

    public function createactivite(Request $req){
        // $error=0;
        // $verif_activite = DB::table('activite')->where('libelleactivite',$req->libelleactivite)->get();
        // $res = json_decode($verif_activite,true);
        // return response()->json(sizeof($res));
        // if(sizeof($res) === 0)
        // {
        // $activite= Activite::create($req->all());
        // return response()->json($activite,200);
        // }
        // else
        // {
        //     return response()->json($error, 200);
        // }
        $activite= Activite::create($req->all());
        return response()->json($activite,200);
    }

    public function verifactivite(Request $req){
        $error=0;
        $verif_activite = DB::table('activite')->where('libelleactivite',$req->libelleactivite)->get();
        $res = json_decode($verif_activite,true);
        return response()->json(sizeof($res));
        if(sizeof($res) === 0)
        {
        $activite= Activite::create($req->all());
        return response()->json($activite->id,200);
        }
        else
        {
            $activiteid= $verif_activite->id;
            return response()->json($activiteid,200);
        }
    }

    public function getactivite(){
        $activites = DB::table('activite')->get();
        return response()->json($activites,200);
    }


    // GESTION DES PROGRAMMES

    public function createprogramme(Request $req){

        $programme= Programme::create($req->all());
        return response()->json($programme,200);
    }

    public function getprogramme(){
        $programmes = DB::table('programme')->get();
        return response()->json($programmes,200);
    }

    /**
     * Retourne une semaine sous forme de chaine "du {lundi} au {dimanche}..." en gérant des cas particuliers :
     *  - début et fin pas dans le même mois
     *  - début et fin pas dans la même année
     * !!! Penser à utiliser setlocale pour avoir la date (jour et mois) en Français !!!
     */
    function getweek(){
        setlocale(LC_TIME, 'fr_FR.UTF8');
        $annee = date("Y");
        $no_semaine = date("W");
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
            $retour = "du ".strftime("%d", $timeStart)." au ".strftime("%d %B %Y", $timeEnd);
        }
        return $retour;
    }
}
