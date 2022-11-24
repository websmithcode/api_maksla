<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceModel extends Model
{
    use HasFactory;

		public function car_model()
		{
			return $this->belongsTo(CarModel::class);
		}
}
