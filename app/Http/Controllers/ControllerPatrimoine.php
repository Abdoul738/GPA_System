<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Patrimoine;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Models\commentaire;
class ControllerPatrimoine extends Controller
{
    public function save(Request $req)
    {
        $data = $req->input();
        $patrimoine = new Patrimoine;

        $patrimoine->titre = $data['nompat'];
        $patrimoine->description = $data['descpat'];
        $patrimoine->typepat = $data['typepat'];
        $patrimoine->entreprise = $data['entpat'];
        $patrimoine->chefEquipe = $data['chfequippat'];
        $patrimoine->pays = $data['payspat'];
        $patrimoine->ville = $data['villepat'];
        $patrimoine->latitude = $data['lat'];
        $patrimoine->longitude = $data['lng'];
        $patrimoine->echeance = $data['echeancepat'];
        $patrimoine->idUser = $data['idUser'];

      //  $imagename = $req->file('imgpat');
      // $input['imagename'] = time().'.'.$imagename->extension();
      //  $destinationPath = public_path().'/assets/img/PatImage' ;
      //  $img = Image::make($imagename->path());
      //  $img->resize(100, 100, function ($constraint) {
      //      $constraint->aspectRatio();
      //  })->save($destinationPath.'/'.$input['imagename']);

        $imagename= $req->file('imgpat')->hashname();
        Storage::disk('local')->put($imagename,file_get_contents($req->file('imgpat')));

        $planname= $req->file('planfilepat')->hashname();
        Storage::disk('local')->put($planname,file_get_contents($req->file('planfilepat')));

        $patrimoine->image=$imagename;
        $patrimoine->plan=$planname;

        $patrimoine->save();
        return response()->json($patrimoine, 200 );
    }

    public function getpatrimoines(){

        $data=Patrimoine::all();
        return response()->json($data, 200 );
    }

    public function getpatrimoinesvalidated()
    {
        $data=DB::table('patrimoines')->where('validation',1)->get();
        return response()->json($data, 200 );
    }

    public function getlat($id)
    {
        $data=DB::table('patrimoines')->where([  ['id',$id] ])->get();
        return response()->json($data[0]->latitude, 200 );
    }

    public function getlong($id)
    {
        $data=DB::table('patrimoines')->where([  ['id',$id] ])->get();
        return response()->json($data[0]->longitude, 200 );
    }

    public function getpatrimoinesnumber()
    {
        $patnum =DB::table('patrimoines')->where('validation',1)->count();
        return response()->json($patnum,200);
    }

    public function getOnepatrimoine($id){
        $patrimoine = DB::table('patrimoines')->where('id',$id)->get();
        return response()->json($patrimoine,200);
    }

    public function getpatrimoinebyuserid($id){
        $patrimoine = DB::table('patrimoines')->where('idUser',$id)->get();
        return response()->json($patrimoine,200);
    }


    public function getpatrimoinebnumber($id){
        $patrimoine = DB::table('patrimoines')->where('idUser',$id)->count();
        return response()->json($patrimoine,200);
    }


    public function comment(Request $req)
    {
        $data = $req->input();
        $comment = new commentaire;

        $comment->user_id = $data['userid'];
        $comment->pat_id = $data['patid'];
        $comment->parent_id = $data['parentid'];
        $comment->comment = $data['comment'];
        $comment->username = $data['username'];

        $comment->save();
        return response()->json($comment, 200 );
    }

    public function getprimarycomment($id){
        $comments1 = DB::table('commentaires')->where('pat_id',$id)->get();
        return response()->json($comments1);
    }

    public function getcommentsnumber($id){
        $commentsnumber = DB::table('commentaires')->where('pat_id',$id)->count();
        return response()->json($commentsnumber);
    }

    public function getsecondarycomment($id){
        $comments2 = DB::table('commentaires')->where('parent_id',$id)->first();
        return response()->json($comments2);
    }


    public function validerpatrimoine($id){
        $parimoines=Patrimoine::where('id',$id)
        ->update(['validation' => 1]);

        return response()->json($parimoines);
    }

    public function retirerpatrimoine($id){
        $parimoines=Patrimoine::where('id',$id)
        ->update(['validation' => 0]);

        return response()->json($parimoines);
    }

}
