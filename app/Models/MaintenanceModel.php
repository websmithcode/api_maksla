<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceModel extends Model
{
	use HasFactory;

	protected $casts = [
		'works_json' => 'array',
		'price' => 'integer',
	];
	protected $visible = ['name', 'works_json', 'price'];

	public function car_model()
	{
		return $this->belongsTo(CarModel::class);
	}
}
