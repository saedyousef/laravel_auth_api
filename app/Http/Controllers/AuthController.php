<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Constants\HttpStatus;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'signup']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login($params = null)
    {
    	if(empty($params))
        	$credentials = request([strtolower('email'), 'password']);
        else
        	$credentials = $params;

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => HttpStatus::$codesMessages[HttpStatus::UNAUTHORIZED]], HttpStatus::UNAUTHORIZED);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user()->get());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL()
        ], HttpStatus::CREATED);
    }

    public function signup(Request $request)
    {

    	$rules = [
    		'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required',
    	];
    	$res = Validator::make($request->all(), $rules);

    	if($res->fails())
    		return response()->json(['errors' => $res->errors()], HttpStatus::BAD_REQUEST);


    	$user = new User();
    	$user->password = bcrypt($request->password);
    	$user->role 	= $request->role;
    	$user->email 	= $request->email;
    	$user->name 	= strtolower($request->name);

    	$params = [
    		'email'    => strtolower($request->email),
    		'password' => $request->password,
    	];

    	if($user->save())
    		return $this->login($params);
    	else
    		return response()->json(['error' => 'Something went wrong!'], HttpStatus::BAD_REQUEST);
    }

}
