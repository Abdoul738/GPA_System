<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;

class LoginRegistController extends Controller
{
    //Creation d'un utilisateur
    public function registerUser(Request $req){

        $result = DB::table('users')->where([['email', $req->email],])->get();

        $res = json_decode($result,true);
        if(sizeof($res) === 0){
            $user= User::create($req->all());
            return response()->json($user,200);
        }
        else{
            return response()->json(sizeof($res));
        }
    }

    //Connexion
    function login(Request $req){

        $result = DB::table('users')->where([

              ['email', $req->email],
              ['password',$req->password],

                        ])->get();

        $res = json_decode($result,true);
        return response()->json(sizeof($res));
        if(sizeof($res) === 0){
        return response()->json(sizeof($res));
        }
        else{
            if($result[0]->password === $req->password){
            $user=DB::table('users')->where('id',$result[0]->id)->get();
            return response()->json($result[0]->id);
            }
        }
    }

    //Retrouver un utilisateur par email
    public function getUser($email){
        $data = DB::table('users')->where('email',$email)->get();
        //$data= User::all();
        return response()->json($data);
      }

    public function getuserbyid($id)
    {
        $user= DB::table('users')->where('id',$id)->get();
        return response()->json($user);
    }

    //Liste des utilisateurs
    public function getAllUser(){
        $data = DB::table('users')->get();
        // return response()->json($data,200);

        $users = User::join('roles', 'users.role_id', '=', 'roles.id')
            ->get(['users.*', 'roles.libellerole']);
        return response()->json($users,200);

        // $users = User::join('roles', 'users.role_id', '=', 'roles.id')
        //     ->where('users.status', 'active')
        //     ->where('posts.status','active')
        //     ->get(['users.*', 'roles.libellerole']);
      }

    //Modifier Profile
    public function UpdateProfile(){

    }

    //Recuperer Mot de passe
    public function RecupPassword(){
        
    }

    public function updateUser(Request $req){
        $user=User::where('id',$req->id)
        ->update(
            [ 
                'nom' => $req->nom,
                'prenom' => $req->prenom,
                'email' => $req->email,
                'role_id' => $req->role_id,
                'numero' => $req->numero
            ]
        );

        return response()->json($user);
    }

    public function delUser($id){
        return User::destroy($id);
    }


}
