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

    public function getActNbrByUser($id){
        $nbr = DB::table('programmes')->where('user_id',$id)->count();
        return response()->json($nbr,200);
    }
    /*********************** END GESTION DES ACTIVITES *******************/

    /*********************** START GESTION DES PROGRAMMES *******************/
    public function createprogramme(Request $req){

        $programme= Programme::create($req->all());
        return response()->json($programme,200);
    }

    public function getprogramme(){
        $titreprogrammes = DB::table('titreprogrammes')->orderBy('id','DESC')->first();
        if($titreprogrammes != null){
            // $programmes = DB::table('programmes')->where('titre_id',$titreprogrammes->id)->orderBy('user_id')->get();
            // return response()->json($programmes,200);

            $programmes = Programme::join('users', 'users.id', '=', 'programmes.user_id')
                                    ->join('activites', 'activites.id', '=', 'programmes.activite_id')
                                    ->where('titre_id',$titreprogrammes->id)
                                    ->orderBy('user_id')
                                    ->get(['programmes.*','users.nom','users.prenom','activites.libelleactivite']);
            return response()->json($programmes,200);
        }
        else{
            return response()->json(0,200);
        }

    }

    public function getProgrammeProgress($id){
        $nbr = DB::table('programmes')->where('titre_id',$id)->count();
        $nbr1 = DB::table('programmes')->where(
            [
                ['titre_id', $id],
                ['statut',1],
            ]
        )->count();
        $nbr2 = round(($nbr1*100)/$nbr,2);
        $progressupdate = Titreprogramme::where('id',$id)->update(['progress' => $nbr2]);
        return response()->json($nbr2,200);
    }

    public function getdaylyprogrammeprogresss(){
        $titreprogrammes = DB::table('titreprogrammes')->orderBy('id','DESC')->first();
        if($titreprogrammes != null){
            $nbr = DB::table('programmes')->where('titre_id',$titreprogrammes->id)->count();
            $nbr1 = DB::table('programmes')
            ->groupBy('user_id')
            ->where([['titre_id', $titreprogrammes->id],['statut',1],])
            ->count();
            // $nbr2 = round(($nbr1*100)/$nbr,2);
            return response()->json($nbr1,200);
        }
        else{
            return response()->json(0,200);
        }

    }

    public function validActivite($id){
        $program = Programme::where('id',$id)->update(['statut' => 1]);
        return response()->json($program,200);
    }

    public function getprogrammeByUser($id){
        $programmes = DB::table('programmes')->where('titre_id',$id)->get();
        return response()->json($programmes,200);
    }

    // $users = DB::table('users')
    //          ->select(DB::raw('count(*) as user_count, status'))
    //          ->where('status', '<>', 1)
    //          ->groupBy('status')
    //          ->get();
    /*********************** END GESTION DES PROGRAMMES *******************/

    /*********************** START GESTION DES TITRES DE PROGRAMMES *******************/
    function getweek(){
        setlocale(LC_TIME, 'fr_FR.UTF8');
        $annee = date("Y");
        $no_semaine = date("W");
        if(date("w") == 5 || date("w") == 0){
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
            $retour = "PROGRAMME DE LA SEMAINE : ".strftime("%d %B %Y", $timeStart)." au ".strftime("%d %B %Y", $timeEnd);
        } elseif( $moisStart != $moisEnd ){
            // à cheval entre 2 mois
            $retour = "PROGRAMME DE LA SEMAINE : ".strftime("%d %B", $timeStart)." au ".strftime("%d %B %Y", $timeEnd);
        } else {
            // même mois
            $retour = "PROGRAMME DE LA SEMAINE : ".strftime("%d", $timeStart)." au ".strftime("%d %B %Y", $timeEnd);
        }
        // return $retour;
        return response()->json($retour,200);
    }

    public function createtitreprogramme(Request $req){
        $error=0;
        $titre_id = 0;
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

    public function getAllTitreprogramme(){
        $ttitreprogrammes = DB::table('titreprogrammes')->get();
        return response()->json($ttitreprogrammes,200);
    }
    /*********************** END GESTION DES TITRES DE PROGRAMMES *******************/
}
