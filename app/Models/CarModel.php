<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

/**
 * @implements \App\Models\Model
 * @method maintenances() : \Illuminate\Database\Eloquent\Relations\HasMany
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

	static function getByFieldValues(array $query){
					$validator = Validator::make($query, [
									'brand' => 'required|string',
									'model' => 'required|string',
									'generation' => 'required|string',
									'engine_type' => 'required|string',
									'engine_model' => 'required|string',
					]);
					$values = $validator->validate();

					return CarModel::where('brand', $values['brand'])
									->where('model', $values['model'])
									->where('generation', $values['generation'])
									->where('engine_type', $values['engine_type'])
									->where('engine_model', $values['engine_model'])
									->firstOrFail();
	}
}
