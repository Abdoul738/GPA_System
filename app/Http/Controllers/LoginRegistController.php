<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginRegistController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
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

    public function register(Request $request){
        $validate = Validator::make($request->all(), [
            'nom'      => 'required',
            'email'     => 'required|email|unique:users',
            'password'  => 'required|min:4|confirmed',
        ]);        
        // return $request->all();
        if ($validate->fails()){
            return response()->json([
                'status' => 'error',
                'errors' => $validate->errors()
            ], 422);
        }        
        $user = new User;
        $user->nom = $request->nom;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();       
        return response()->json(['status' => 'success'], 200);
    } 

    //Connexion
    // function login(Request $req){
    //     // $result = User::where([['email', $req->email],['password',$req->password],])
    //     //             ->join('roles', 'users.role_id', '=', 'roles.id')
    //     //             ->firstOrFail(['users.*', 'roles.libellerole', 'roles.niveau']);

    //     // $res = json_decode($result,true);
    //     // if(sizeof($res) === 0){
    //     // return response()->json(sizeof($res));
    //     // }
    //     // else{
    //     //     if($result[0]->password === $req->password){
    //     //         return response()->json($result[0]);
    //     //     }
    //     // }
    //     // return response()->json($result);


    // }

    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = Auth::user();
        $token = $user->createToken('');

        return $this->respondWithToken($token, $request->email);
    }

    protected function respondWithToken($token, $email)
    {
        // $user = User::select('menuroles as roles')->where('email', '=', $email)->first();

        $user = User::where([['email', $email]])
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->first(['users.*', 'roles.libellerole', 'roles.niveau']);
            
            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'users' => $user,
            ]);
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

        $users = User::join('roles', 'users.role_id', '=', 'roles.id')
            ->get(['users.*', 'roles.libellerole', 'roles.niveau']);
        return response()->json($users,200);
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
