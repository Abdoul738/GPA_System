<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Programme;
use App\Models\Activite;
use App\Models\Titreprogramme;
use App\Models\Executionjours;
use App\Models\Rapportsjours;
use App\Models\Rapports;
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

    public function getprogramactbyid($id){
        $programmes = Programme::where('programmes.id',$id)
                                ->join('activites', 'activites.id', '=', 'programmes.activite_id')
                                ->get(['programmes.*','activites.libelleactivite']);
        return response()->json($programmes,200);
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

    public function getallWeekDays(){
        // START GET WEEK DAYS
        $annee = date("Y");
        $no_semaine = date("W");
        for($i = 1; $i <= 365; $i++) {
            $week = date("W", mktime(0, 0, 0, 1, $i, $annee));
            if($week == $no_semaine) {
                for($d = 0; $d < 7; $d++) {
                    $nbr2[] = date("d/m/Y", mktime(0, 0, 0, 1, $i+$d, $annee));
                }
                break;
            }
        }
        return response()->json($nbr2,200);
        // END GET WEEK DAYS
    }

    public function getdaylyprogrammeprogresss(){
        $datejrs = date('Y-m-d');
        $titreprogrammes = DB::table('titreprogrammes')->orderBy('id','DESC')->first();
        if($titreprogrammes != null){
            // START CALCUL TAUX
            $nbr = DB::table('programmes')->where('titre_id',$titreprogrammes->id)->count();
            $nbr1 = DB::table('programmes')
            ->where([['titre_id', $titreprogrammes->id],['statut',1],['updated_at',$datejrs],])
            // ->where([['titre_id', $titreprogrammes->id],['statut',1]])
            ->count();
            $taux = round(($nbr1*100)/$nbr,2);
            // END CALCUL TAUX
            if($taux != null){
                // START UPDATE TAUX IN DATA BASE
                $verif_taux = DB::table('executionjours')->where('created_at',$datejrs)->get();
                $res = json_decode($verif_taux,true);

                if(sizeof($res) === 0)
                {
                    $create_new_taux= Executionjours::create(
                        [ 
                            'titreprogramme_id' => $titreprogrammes->id,
                            'taux' => $taux,
                        ]
                    );
                    $update_taux=Executionjours::where('id',$create_new_taux->id)
                    ->update(['taux' => $taux,]);
                    return response()->json($taux);
                }
                else
                {
                    $update_taux=Executionjours::where('id',$verif_taux[0]->id)
                    ->update(['taux' => $taux,]);
                    return response()->json($taux);
                }
                // END UPDATE TAUX IN DATA BASE
            }
        }
        else{
            return response()->json(0,200);
        }

    }

    public function getactualweekdatas(){
        $titreprogrammes = DB::table('titreprogrammes')->orderBy('id','DESC')->first();
        if($titreprogrammes != null){
            $datas0 = DB::table('executionjours')->where('titreprogramme_id',$titreprogrammes->id)->get();
            $nbr = DB::table('executionjours')->where('titreprogramme_id',$titreprogrammes->id)->count();
            $res = json_decode($datas0,true);
            if(sizeof($res) === 0){
                return response()->json(0,200);
            }else{
                for($d = 0; $d < $nbr; $d++) {
                    $datas[] = $datas0[$d]->taux;
                }
                return response()->json($datas,200);
            }        
        }
        else{
            return response()->json(0,200);
        }

    }

    public function getlastweekdatas(){
        $titreprogrammes = DB::table('titreprogrammes')->orderBy('id','DESC')->first();
        if($titreprogrammes != null){
            $p_id = $titreprogrammes->id-1;
            $datas0 = DB::table('executionjours')->where('titreprogramme_id',$p_id)->get();
            $nbr = DB::table('executionjours')->where('titreprogramme_id',$p_id)->count();
            $res = json_decode($datas0,true);
            if(sizeof($res) === 0){
                return response()->json(0,200);
            }else{
                for($d = 0; $d < $nbr; $d++) {
                    $datas[] = $datas0[$d]->taux;
                }
                return response()->json($datas,200);
            }        }
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
/*********************** END GESTION DES PROGRAMMES *******************/

/*********************** START GESTION DES TITRES DE PROGRAMMES *******************/
    function getweek(){
        setlocale(LC_TIME, 'fr_FR.UTF8');
        $annee = date("Y");
        $no_semaine = date("W");
        if(date("w") == 6 || date("w") == 0){
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

/*********************** START GESTION DES RAPPORTS D'ACTIVITES *******************/
    public function addrapport(Request $req){
        $rap= Rapportsjours::create($req->all());
        return response()->json($rap,200);
    }

    public function getrapport(){
        $rap = DB::table('rapportsjours')->get();
        return response()->json($rap,200);
    }

    public function updaterapport(Request $req){
        $rap=Rapportsjours::where('id',$req->id)->update(['body' => $req->body,]);
        return response()->json($rap);
    }

    public function getrapportbyid($id){
        $rap = Rapportsjours::where('id',$id)->get();
        return response()->json($rap,200);
    }

    public function delrapport($id){
        return Rapportsjours::destroy($id);
    }
/*********************** END GESTION DES RAPPORTS D'ACTIVITES *******************/
}