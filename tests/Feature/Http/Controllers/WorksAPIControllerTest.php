<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\Services\WorksUploader;
use Tests\TestCase;

class WorksAPIControllerTest extends TestCase
{
	use  DatabaseMigrations;
	const API_URL = '/api/get-works';
	const BAD_PARAMS = [
		'brand' => 'BMW',
		'model' => 'X6',
		'generation' => 'X6 (F16) 2014-2019',
		'engine_type' => 'Бензин',
		'engine_model' => 'F 16 35i 306 л.с.',
		'maintenance_name' => 'ТО-1 оригинал',
	];
	const GOOD_PARAMS = [
		'brand' => 'LAND ROVER',
		'model' => 'Range Rover',
		'generation' => 'IV 2017-2022',
		'engine_type' => 'Дизель',
		'engine_model' => '3.0 249 л.с.',
		'maintenance_name' => 'ТО-1 не оригинал',
	];
	const EXPECTED_RESPONSE =  [
		"brand" => "LAND ROVER",
		"model" => "Range Rover",
		"generation" => "IV 2017-2022",
		"engine_type" => "Дизель",
		"engine_model" => "3.0 249 л.с.",
		"maintenance_name" => "ТО-1 не оригинал",
		"works" =>  ["Фильтр салонный, с/у", "Фильтр топливный, с/у", "Масло моторное, замена"],
		"price" => "8400.00",
	];

	public function test_nonvalid_requests()
	{
		$url = self::API_URL;
		$damaged_url = static::getDamagedUrl();
		$good_url = static::getGoodUrl();

		$this->json('GET', $url)->assertStatus(400);
		$this->get($url)->assertStatus(400);
		$this->post($url)->assertStatus(405);
		$this->put($url)->assertStatus(405);
		$this->delete($url)->assertStatus(405);
		$this->patch($url)->assertStatus(405);

		$this->json('GET', $damaged_url)->assertStatus(404);
		$this->get($damaged_url)->assertStatus(404);
	}

	public function test_valid_request()
	{
		$damaged_url = static::getDamagedUrl();
		$good_url = static::getGoodUrl();

		$this->json('GET', $damaged_url)->assertStatus(404);
		$this->json('GET', $good_url)->assertStatus(404);

		$uploader = new WorksUploader();
		$uploader->upload();
		$response = $this->json('GET', $good_url);
		$response
			->assertStatus(200)
			->assertJson(self::EXPECTED_RESPONSE);
	}
	static function getDamagedUrl()
	{
		return static::API_URL . '?' . Arr::query(static::BAD_PARAMS);
	}
	static function getGoodUrl()
	{
		return static::API_URL . '?' . Arr::query(static::GOOD_PARAMS);
	}
}
