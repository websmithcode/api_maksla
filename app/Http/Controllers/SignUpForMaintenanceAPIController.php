<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignUpForMaintenanceAPISignUpRequest;
use App\Mail\SuccessSignUpForMaintenance;
use App\Mail\SuccessSignUpForMaintenanceAdmins;
use App\Models\CarModel;
use Illuminate\Support\Facades\Mail;

class SignUpForMaintenanceAPIController extends Controller
{
	public function signUp(SignUpForMaintenanceAPISignUpRequest $request)
	{
		$validated = collect($request->validated());
		$car_query = $validated->only(
			'brand',
			'model',
			'generation',
			'engine_type',
			'engine_model'
		);

		$car = CarModel::getByFieldValues($car_query->toArray());
		$maintenance_name = $validated->get('maintenance_name');
		$maintenance = $car
			->maintenances()
			->where('name', $maintenance_name)
			->firstOrFail();

		$response_data = [
			...$car_query,
			'maintenance_name' => $maintenance_name,
			'works' => $maintenance->works_json,
			'price' => $maintenance->price,
		];

		$letter_values = [
			'client_name' => $validated->get('client_name'),
			'car' => $car->verbose,
			'date' => $validated->get('date'),
			'time' => $validated->get('time'),
			'works' => $maintenance->works_json,
		];

		$letter = new SuccessSignUpForMaintenance(...$letter_values);
		Mail::to($validated->get('client_email'))->send($letter);

		$letter_values_admins = [
						...$letter_values,
						'client_email' => $validated->get('client_email'),
						'client_phone' => $validated->get('client_phone'),
						'maintenance_name' => $maintenance_name,
						'price' => $maintenance->price,
						'extra' => $validated->get('extra'),
		];
    $letter_admins = new SuccessSignUpForMaintenanceAdmins(...$letter_values_admins);
		foreach (config('mail.admins') as $admin_email) {
			Mail::to($admin_email)->send($letter_admins);
		}

		return 	$response_data;
	}
}
