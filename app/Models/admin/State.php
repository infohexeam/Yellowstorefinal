<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
	protected $table = "sys_states";
	protected $primaryKey = "state_id";

	protected $fillable = [
		'state_id', 'state_name', 'country_id',
	];

	public function country()
	{
		return $this->belongsTo('App\Models\admin\Country', 'country_id', 'country_id');
	}
}
