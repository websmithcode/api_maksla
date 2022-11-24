<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements \App\Models\Model
 */
class CarModel extends Model
{
	use HasFactory;
	protected $fillable = [
		'brand',
		'model',
		'generation',
		'engine_type',
		'engine_model',
	];

	public function maintenances()
	{
		return $this->hasMany(MaintenanceModel::class);
	}
}
