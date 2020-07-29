<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function login(Request $req)
    {
        $credentials = $req->only('email','password');
        try{
            if(! $token = JWTAuth::attempt($credentials))
            {
                return response()->json(['error' =>'invalid_credentials'],400);
            }            
        } catch(JWTException $ex)
        {
            return response()->json(['error' =>'could_not_create_token'],500);
        }
        return response()->json(compact('token'));
    }

    public function register(Request $req)
    {
        $validator = Validator::make($req->all(),[
            'name' => 'required|string|max:255',
            'email'=> 'required|string|email|max:255|unique:users',
            'password'=> 'required|string|min:6|confirmed'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(),400);
        }

        $user = User::create([
            'name'=>$req->get('name'),
            'email'=>$req->get('email'),
            'password'=>Hash::make($req->get('password'))
        ]);

        $token = JWTAuth::fromUser($user);
        return response()->json(compact('user','token'),201);
    }

    public function getAuthenticatedUser()
    {
        try{
            if(! $user = JWTAuth::parseToken()->authenticate())
            {
                return response()->json(['User_not_found'],404);
            }
            
        }catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $ex){
           
            return response()->json(['token_expired'],$ex->getStatusCode()); 

        }catch (Tymon\JWTAuth\Exception\JWTException $ex){

            return response()->json(['token_absent'],$ex->getStatusCode());
        }
        
        return response()->json(compact('user'));
    }

}
