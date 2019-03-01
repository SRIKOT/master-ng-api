<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
	function __construct() {
        $this->table = env('DB_DATABASE_MASTER').'.role';
    }
    /**
     * The table associated with the model.
     *
     * @var string
     */
	 
	const CREATED_AT = 'created_dttm';
	const UPDATED_AT = 'updated_dttm';
    protected $table = null;
	protected $primaryKey = 'role_id';
	public $incrementing = true;
	//public $timestamps = false;
	protected $guarded = array();
	
	// protected $fillable = array('questionnaire_id','customer_code','customer_name','customer_type','industry_class');
	// protected $hidden = ['created_by', 'updated_by', 'created_dttm', 'updated_dttm'];
}