<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	use SoftDeletes;
	protected $guarded = [];
    protected $dates= ['deleted_at'];
    //
}
