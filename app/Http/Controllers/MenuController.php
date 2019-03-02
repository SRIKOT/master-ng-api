<?php

namespace App\Http\Controllers;

use App\User;
use App\Menu;
use App\MenuGroup;

use App\Http\Controllers\Shared\FunctionController;

use Illuminate\Http\Request;
use Auth;
use DB;
use Validator;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MenuController extends Controller {
    
    public function __construct() {
        $this->middleware('jwt.auth');
        $this->thisEmployee = new User;
        $this->thisFunction = new FunctionController;
        $this->masterDB = env("DB_DATABASE_MASTER");
    }

    public function getMenu() {
        $menu_group = DB::select("
            SELECT page_group_id, page_group_code, page_group_name, seq_no
            FROM {$this->masterDB}.page_group
            WHERE is_active = 1
            ORDER BY seq_no
        ");

        $user = $this->thisEmployee->isAll();
        foreach($menu_group as $mgk => $mgv) {
            if($user[0]->is_all> 0) {
                $menu = DB::select("
                    SELECT *
                    FROM {$this->masterDB}.page
                    WHERE page_group_id = '{$mgv->page_group_id}'
                    ORDER BY seq_no
                ");
                $menu_group[$mgk]->menu = $menu;
            } else {
                $menu = DB::select("
                    SELECT p.*
                    FROM {$this->masterDB}.user u
                    INNER JOIN {$this->masterDB}.role r ON r.role_id = u.role_id
                    INNER JOIN {$this->masterDB}.role_page rp ON rp.role_id = r.role_id
                    INNER JOIN {$this->masterDB}.page p ON p.page_id = rp.page_id
                    WHERE u.user_code = '".Auth::id()."'
                    AND p.page_group_id = '{$mgv->page_group_id}'
                    ORDER BY p.seq_no
                ");
                if(!empty($menu)) {
                    $menu_group[$mgk]->menu = $menu;
                } else {
                    unset($menu_group[$mgk]);
                }
            }
        }

        $data_menu = collect($menu_group)->values()->all();
        return response()->json(['menu_group' => $data_menu, 'is_all' => $user[0]->is_all]);
    }

    public function CU(Request $request) {
        $errors = [];
        $errors_validator = [];

		$validator = Validator::make([
			'MenuName' => $request->menuName
		], [
			'MenuName' => 'required|max:255'
		]);

		if($validator->fails()) {
			$errors_validator[] = $validator->errors();
        }
        
        if(!empty($errors_validator)) {
            return response()->json(['status' => 400, 'data' => $errors_validator]);
        }

        if(empty($request->menuId)) {
            $menu = new Menu();
            $menu->page_group_id = $request->groupId;
            $menu->page_name = $request->menuName;
            $menu->page_url = $this->thisFunction->strtolower_ReplaceSpace($request->menuName);
            $menu->is_active = 1;
            $menu->created_by = Auth::id();
            $menu->updated_by = Auth::id();
        } else {
            $menu = Menu::find($request->menuId);
            $menu->page_group_id = $request->groupId;
            $menu->page_name = $request->menuName;
            $menu->page_url = $this->thisFunction->strtolower_ReplaceSpace($request->menuName);
            $menu->is_active = 1;
            $menu->updated_by = Auth::id();
        }

        try {
            $menu->save();
        } catch(exception $e) {
            $errors[] = substr($e,0,254);
        }

        if(empty($errors)) {
            return response()->json(['status' => 200, 'data' => []]);
        } else {
            return response()->json(['status' => 400, 'data' => $errors]);
        }
    }

    public function sortMenu(Request $request) {
        foreach($request->all() as $keyG => $valueG) {
            $g = MenuGroup::find($valueG['page_group_id']);
            $g->seq_no = $keyG;
            $g->created_by = Auth::id();
            $g->updated_by = Auth::id();
            $g->save();
            foreach($valueG['menu'] as $keyG2 => $valueG2) {
                $m = Menu::find($valueG2['page_id']);
                $m->page_group_id = $valueG['page_group_id'];
                $m->seq_no = $keyG2;
                $m->created_by = Auth::id();
                $m->updated_by = Auth::id();
                $m->save();
            }
        }
        return response()->json(['status' => 200]);
    }

    public function destroy($page_id) {
		try {
			$item = Menu::findOrFail($page_id);
		} catch (ModelNotFoundException $e) {
			return response()->json(['status' => 404, 'data' => 'Menu not found.']);
		}

		try {
			$item->delete();
		} catch (Exception $e) {
			if ($e->errorInfo[1] == 1451) {
				return response()->json(['status' => 400, 'data' => 'Cannot delete because this QuestionaireDataHeader is in use.']);
			} else {
				return response()->json($e->errorInfo);
			}
		}

		return response()->json(['status' => 200]);

	}
}