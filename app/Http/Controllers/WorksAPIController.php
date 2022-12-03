<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorksAPIGetWorksRequest;
use App\Models\CarModel;
use App\Models\MaintenanceModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorksAPIController extends Controller
{
	public function getWorks(
		WorksAPIGetWorksRequest $request
	) {
		// Example of request:
		// http://lh/api/get-works?brand=BMW&model=%D0%A56&generation=%D0%A56%20(F16)%202014-2019&engine_type=%D0%91%D0%B5%D0%BD%D0%B7%D0%B8%D0%BD&engine_model=F%2016%2035i%20306%20%D0%BB.%D1%81.&maintenance_name=%D0%A2%D0%9E-1%20%D0%BE%D1%80%D0%B8%D0%B3%D0%B8%D0%BD%D0%B0%D0%BB
		$validated = collect($request->validated());
		$car_query = $validated->except('maintenance_name');
		$car = CarModel::getByFieldValues($car_query->toArray());
		$maintenance_name = $validated->get('maintenance_name');
		$maintenance = $car
			->maintenances()
			->where('name', $maintenance_name)
			->firstOrFail();

		$response_data = [
			...$validated,
			'works' => $maintenance->works_json,
			'price' => $maintenance->price,
		];

		return 	$response_data;
	}
}
