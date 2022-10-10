<?php

namespace App\Http\Controllers\Api;

use Auth;
use JWTAuth;
use Validator;
use App\Models\User;
use App\Helpers\Helper;
use App\Models\ApiTokenLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        try {
            $rules = Validator::make($request->all(),[
                'name' => 'required|unique:users|min:5',
                'email' => 'required|email|unique:users|min:5',
                'password' => 'required',
            ]);
    
            if($rules->fails()){
                return Helper::error(null, $rules->errors(), 400);
            }else{
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password)
                ]);
                return Helper::success( $user,'Register successfully', 201);
            }
        } catch (\Throwable $th) {
           return Helper::error(null,$th->getMessage() ?? 'Internal server error',500);
        }
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            $rules = Validator::make($request->all(),[
                'email' => 'required|email',
                'password' => 'required',
            ]);
            if($rules->fails()){
                return Helper::error(null, $rules->errors(), 400);
            }else{
                if(!$token = JWTAuth::attempt(['email' => request()->email, 'password' => request()->password])){
                    return Helper::error(null,['message' => 'Invalid Credentials'], 400);
                }else{
                    $responseJson = Helper::success([
                        'user' => JWTAuth::user(),
                        'token' => $token,
                        'token_type' => 'bearer',
                        'expires_in' => JWTAuth::factory()->getTTL() * 60
                    ],'Login Successully');
                    $log['response'] = json_encode($responseJson);
                    $log['token'] = $token;
                    ApiTokenLog::create($log);
                    return $responseJson;
                }
            }
        } catch (\JWTException $th) {
           return Helper::error(null,$th->getMessage() ?? 'Internal server error',500);
        }
    }

    public function refresh()
    {
        return $this->respondWithToken(JWTAuth::refresh());
    }

    protected function respondWithToken($token)
    {
        return Helper::success([
            'user' => JWTAuth::user(),
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ],'token refresh created successfully');
    }

    public function logout()
    {
        try {
            if(auth()){
                auth()->logout();
            }
            return Helper::success(null, 'You have successfully logged out and the token was successfully deleted');
        } catch (\Throwable $th) {
            return Helper::error(null,$th->getMessage() ?? 'Internal server error',500);
        }
    } 

    public function update_profile()
    {

    }

    public function change_password()
    {
        
    }
}
