<?php

namespace App\Http\Controllers;

use Hash;
use App\User;
use Illuminate\Http\Request;
use App\Constants\HttpStatus;
use Illuminate\Support\Facades\Validator;


class UsersController extends Controller
{
    /**
     * Create a new UsersController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => []]);
    }


    /**
    * @author Saed Yousef
    * @return user object from the auth token
    */
    protected function getUserObject()
    {
    	$user = auth()->user()->get()->toArray()[0];
    	return $user;
    }

    /**
    * @author Saed Yousef
    * @return user object from the auth token
    */
    public function get_user_details()
    {
    	$user = $this->getUserObject();
    	return response()->json(['User' => $user], HttpStatus::OK);
    }

    /**
    * @author Saed Yousef
    * @param password <Optional>
    * @param current_password <Required> if password passed
    * @param password_confirmation <Required> if password passed
    * @param name <Optional>
    * @param email <Optional>
    * @param current_email <Required> if email passed
    * @return message|status
    */
    public function edit_profile(Request $request)
    {
    	$user = User::find(auth()->user()->id);
    	if(!empty($request->name))
    	{
    		$rules = [
	            'name' => 'required|string|max:255'
	    	];

	    	$validation = Validator::make($request->all(), $rules);
	    	if($validation->fails())
    			return response()->json(['errors' => $validation->errors()], HttpStatus::BAD_REQUEST);
    		$user->name = $request->name;
    	}

    	if(!empty($request->email))
    	{
    		$request->request->add(['current_email' => $user->email]);
    		$rules = [
    			'current_email' => 'required',
	            'email' 		=> 'required|string|email|max:255|different:current_email|unique:users',
	    	];

	    	$validation = Validator::make($request->all(), $rules);
	    	if($validation->fails())
    			return response()->json(['errors' => $validation->errors()], HttpStatus::BAD_REQUEST);
    		$user->email = $request->email;
    	}

    	if(!empty($request->password))
    	{
    		if(empty($request->current_password))
    			return response()->json(['error' => 'current_password is required to update your password'], HttpStatus::BAD_REQUEST);

    		if(!Hash::check($request->current_password, $user->password))
    			return response()->json(['error' => 'Wrong password'], HttpStatus::UNAUTHORIZED);

    		$rules = [
	            'current_password' 		=> 'required',
				'password'     			=> 'min:8|confirmed|different:current_password',
				'password_confirmation' => 'required_with:password'
	    	];

	    	$validation = Validator::make($request->all(), $rules);
	    	if($validation->fails())
    			return response()->json(['errors' => $validation->errors()], HttpStatus::BAD_REQUEST);

    		$newPassword = bcrypt($request->password);
    		if($newPassword == $user->password)
    			return response()->json(['errors' => 'you cannot use the same pssword'], HttpStatus::BAD_REQUEST);

    		$user->password = $newPassword;
    	}
    	
    	if(empty($request->all()))
    		return response()->json(['errors' => 'Nothing to update'], HttpStatus::BAD_REQUEST);

    	if($user->save())
    		return response()->json(['success' => 'Profile successfully updated'], HttpStatus::OK);
    	else
    		return response()->json(['errors' => 'something went wrong'], HttpStatus::BAD_REQUEST);
    }
}
