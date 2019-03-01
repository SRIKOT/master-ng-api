<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Auth;
use Validator;
use DB;
use Mail;
use Hash;
use Adldap;
use Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Adldap\Exceptions\Auth\BindException;
//use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;


class AuthenticateController extends Controller
{

	public function __construct() {
		$this->middleware('jwt.auth', ['only' => ['index']]);
	}

	public function index() {
		
	}

    public function authenticate(Request $request) {
        try {
            // verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt(array('user_code' => $request->email, 'password' => $request->password))) {
                return response()->json(['error' => 'invalid_credentials', 'status' => 401]);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        } catch (BindException $e) {
			return response()->json(['error' => 'Cannot connect to Server.'], 500);
		}
		
		if (empty(Auth::id())) {
			return response()->json(['error' => 'User not foundss.'], 500);
		}
		
        return response()->json(['token' => $token]);
    }
	
	public function destroy() {
		$token = JWTAuth::getToken();
		if (empty($token)) {
			return response()->json(['status' => 401,'message' => 'no token provided']);
		} else {
			try {
				$response = JWTAuth::invalidate(JWTAuth::getToken());
				return response()->json(['status' => 200,'t_stat' => $response]);
			} catch (JWTException $e) {
				return response()->json(['status' => 401, 'message' => $e->getMessage()], $e->getStatusCode());
			}
		}
	}
}