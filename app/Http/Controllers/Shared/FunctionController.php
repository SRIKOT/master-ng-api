<?php

namespace App\Http\Controllers\Shared;

use Auth;
use DB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FunctionController extends Controller
{

	public function __construct()
	{
		$this->middleware('jwt.auth');
	}

	function trim_text($text) {
		return trim($text);
	}

	function strtolower_text($text) {
		return strtolower($text);
    }
    
    function strtolower_ReplaceSpace($text) {
		return strtolower(str_replace('', '_', $text));
	}
}
