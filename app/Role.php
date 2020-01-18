<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;
use App\User;
class Role extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $dates= ['deleted_at'];
    //
    public function users()
    {
    	return $this->hasMany('App\User');
    }
}
