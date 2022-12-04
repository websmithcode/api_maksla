<?php

namespace Tests\Feature\Http\Controllers;

use App\Mail\SuccessSignUpForMaintenance;
use App\Mail\SuccessSignUpForMaintenanceAdmins;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SignUpForMaintenanceAPIControllerTest extends TestCase
{
	use  DatabaseMigrations;

	const API_URL = '/api/sign-up-for-maintenance';
	const GOOD_PARAMS = [
		'brand' => 'BMW',
		'model' => 'Х6',
		'generation' => 'Х6 (F16) 2014-2019',
		'engine_type' => 'Бензин',
		'engine_model' => 'F 16 35i 306 л.с.',
		'maintenance_name' => 'ТО-1 оригинал',
		'client_name' => 'Александр Кузнецов',
		'client_phone' => '89009001000',
		'client_email' => 'user@example.com',
		'date' => '25.03.2023',
		'time' => '12:00'
	];
	const BAD_PARAMS = [
		...self::GOOD_PARAMS,
		'brand' => 'BAD_BRAND'
	];

	const EXTRA_PARAMS = [
		'extra' => [
			'ad' => 'Yandex Direct',
			'message' => 'Комментарий клиента',
			'notification_type' => 'SMS'
		]
	];
	const BAD_EXTRA_PARAMS = [];

	public function setup(): void
	{
		parent::setUp();
		$this->artisan('upload:demo-data works')
			->expectsOutput('Demo data "works" uploaded')
			->assertExitCode(0);
	}

	public function test_bad_params()
	{
		Mail::fake();
		$response = $this->request(self::BAD_PARAMS);
		$response->assertStatus(404);

		// Same but with extra
		$response = $this->json('GET', self::API_URL .'?'. Arr::query([...self::BAD_PARAMS, ...self::EXTRA_PARAMS]));
		$response->assertStatus(404);
		$response = $this->json('GET', self::API_URL .'?'. Arr::query([...self::BAD_PARAMS, ...self::BAD_EXTRA_PARAMS]));
		$response->assertStatus(404);

		Mail::assertNothingSent();
	}

	public function test_good_params()
	{
		Mail::fake();
		// $response = $this->json('GET', self::API_URL .'?'. Arr::query(self::GOOD_PARAMS));
		$response = $this->request(self::GOOD_PARAMS);
		$response->assertStatus(200);

		Mail::assertSent(SuccessSignUpForMaintenance::class);
		Mail::assertSent(SuccessSignUpForMaintenanceAdmins::class);

		Mail::fake();
		$response_extra = $this->json('GET', self::API_URL .'?'. Arr::query([...self::GOOD_PARAMS, ...self::EXTRA_PARAMS]));
		$response_extra->assertStatus(200);

		Mail::assertSent(SuccessSignUpForMaintenance::class);
		Mail::assertSent(SuccessSignUpForMaintenanceAdmins::class);
	}

	public function request($params, $extra=null): \Illuminate\Testing\TestResponse{
		if($extra){
						$params= [...$params, ...$extra];
		}
		return $this->json('GET', self::API_URL . '?' . Arr::query($params));
	}

}
