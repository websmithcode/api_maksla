<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SignUpForMaintenanceAPISignUpRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, mixed>
	 */
	public function rules()
	{
		return [
			'brand' => 'required|string',
			'model' => 'required|string',
			'generation' => 'required|string',
			'engine_type' => 'required|string',
			'engine_model' => 'required|string',
			'maintenance_name' => 'required|string',

			'client_name' => 'required|string',
			'client_phone' => 'required|string',
			'client_email' => 'required|email',
			'date' => 'required|date|after:today',
			'time' => ['required', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/'],

			'extra' => 'array',
		];
	}
	/**
	 * Return validation errors as json response
	 *
	 * @param Validator $validator
	 */
	protected function failedValidation(Validator $validator)
	{
		$response = [
			'status' => 'failure',
			'status_code' => 400,
			'message' => 'Bad Request',
			'errors' => $validator->errors(),
		];

		throw new HttpResponseException(response()->json($response, 400));
	}
}
