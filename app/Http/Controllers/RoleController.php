<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Shared\FunctionController;
use App\Role;
use App\User;
use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;
use Response;
use Validator;

class RoleController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.auth');
        $this->thisEmployee = new User;
        $this->thisFunction = new FunctionController;
        $this->masterDB = env("DB_DATABASE_MASTER");
    }

    public function listCustomer()
    {
        $items = DB::select("
            SELECT *
            FROM {$this->masterDB}.customer
        ");
        return response()->json($items);
    }

    public function getRole()
    {
        $items = DB::select("
            SELECT role_id id, role_name, is_all_user, is_active
            FROM {$this->masterDB}.role
            ORDER BY role_id ASC
        ");

        foreach ($items as $key => $value) {
            $items[$key]->is_all_user = $value->is_all_user == 1 ? true : false;
            $items[$key]->is_active = $value->is_active == 1 ? true : false;
        }

        return response()->json($items);
    }

    public function show($id)
    {
        $item = DB::select("
            SELECT role_id id, role_name, is_all_user, is_active
            FROM {$this->masterDB}.role
            WHERE role_id = {$id}
        ");

        foreach ($item as $key => $value) {
            $item[$key]->is_all_user = $value->is_all_user == 1 ? true : false;
            $item[$key]->is_active = $value->is_active == 1 ? true : false;
        }

        return response()->json($item[0]);
    }

    public function addEditRole(Request $request)
    {
        $errors = [];
        $errors_validator = [];

        $request->is_all_user = $request->is_all_user == true ? 1 : 0;
        $request->is_active = $request->is_active == true ? 1 : 0;

        $validator = Validator::make([
            'role_name' => $request->role_name,
            'is_all_user' => $request->is_all_user,
            'is_active' => $request->is_active,
        ], [
            'role_name' => 'required|max:255',
            'is_all_user' => 'required|integer',
            'is_active' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $errors_validator[] = $validator->errors();
        }

        if (!empty($errors_validator)) {
            return response()->json(['status' => 400, 'data' => $errors_validator]);
        }

        if (empty($request->id)) {
            $menu = new Role();
            $menu->role_name = $request->role_name;
            $menu->is_all_user = $request->is_all_user;
            $menu->is_active = $request->is_active;
            $menu->created_by = Auth::id();
            $menu->updated_by = Auth::id();
        } else {
            $menu = Role::find($request->id);
            $menu->role_name = $request->role_name;
            $menu->is_all_user = $request->is_all_user;
            $menu->is_active = $request->is_active;
            $menu->updated_by = Auth::id();
        }

        try {
            $menu->save();
        } catch (exception $e) {
            $errors[] = substr($e, 0, 254);
        }

        if (empty($errors)) {
            return response()->json(['status' => 200, 'data' => $errors]);
        } else {
            return response()->json(['status' => 500, 'data' => $errors]);
        }
    }
}
