<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Models\Role;

class RoleController extends Controller
{
    public function createRole(Request $req){
        // $error=0;
        // $verif_role = DB::table('role')->where('libellerole',$req->libellerole)->get();
        // $res = json_decode($verif_role,true);
        // return response()->json(sizeof($res));
        // if(sizeof($res) === 0)
        // {
        // $role= Role::create($req->all());
        // return response()->json($role,200);
        // }
        // else
        // {
        //     return response()->json($error, 200);
        // }
        $role= Role::create($req->all());
        return response()->json($role,200);
    }

    public function storeRole(Request $request)
    {
        // La validation de données
        $this->validate($request, [
           'libellerole' => 'required|max:100',
           'niveau' => 'required|min:1'
        ]);

        // On crée un nouvel utilisateur
        $role = Role::create([
            'libellerole' => $request->libellerole,
            'niveau' => $request->niveau,
            // 'password' => bcrypt($request->password)
        ]);

        // On retourne les informations du nouvel utilisateur en JSON
        return response()->json($user, 201);
    }

    public function getrole(){
        // $roles= Role::all();
        $roles = DB::table('role')->get();
        return response()->json($roles,200);
    }
}
