<?php

namespace App;

use DB;
use Auth;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;

class User extends Model implements AuthenticatableContract,
AuthorizableContract,
CanResetPasswordContract
{
    function __construct() {
        $this->table = env('DB_DATABASE_MASTER').'.user';
    }
    
    use Authenticatable, Authorizable, CanResetPassword;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    const CREATED_AT = 'created_dttm';
    const UPDATED_AT = 'updated_dttm';
    protected $table = null;
    protected $primaryKey = 'user_code';
    public $incrementing = false;
//    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function isAll() {
        $masterDB = env("DB_DATABASE_MASTER");
        $items = DB::select("
            SELECT r.is_all_user is_all
            FROM {$masterDB}.user u
            INNER JOIN {$masterDB}.role r ON r.role_id = u.role_id
			WHERE u.user_code = '".Auth::id()."'
		");
        return $items;
    }
}
