<?php

namespace Tests\Feature;

use App\Models\CarModel;
use App\Models\MaintenanceModel;
use App\Services\WorksParser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use Tests\Services\WorksUploader;
use Tests\TestCase;

class WorksParserTest extends TestCase
{
	use  DatabaseMigrations;

	public function setup(): void
	{
		parent::setup();
		$this->uploader = new WorksUploader();

		$this->fixture_path = $this->uploader->fixture_path;
		$this->works_json = $this->uploader->works_json;
		$this->parser = $this->uploader->parser;
	}

	public function testFixtureFileExistsAndReadable()
	{
		$this->assertFileExists($this->fixture_path);
		$this->assertIsReadable($this->fixture_path);
	}

	public function testParserInitilizedAsExpected()
	{
		$this->assertEquals(
			$this->works_json,
			$this->parser->json,
			'WorksParser json does not match'
		);
	}

	public function testParseDataIsCollection()
	{
		$data = $this->parser->parse();
		$this->assertInstanceOf(\Illuminate\Support\Collection::class, $data);
	}

	public function testParseDataIsNotEmpty()
	{
		$data = $this->parser->parse();
		$this->assertNotEmpty($data);
	}

	public function testFixtureIsNotCorrupted()
	{
		$data = $this->parser->parse();
		$expected_keys = [
			'Марка',
			'Модель',
			'Поколение',
			'ТипДВС',
			'МодельДВС',
			'ТО',
			'МассивРабот',
			'СуммаСвязанных',
		];
		foreach ($data as $work_num => $work) {
			foreach ($expected_keys as $key) {
				$this->assertArrayHasKey(
					$key,
					$work,
					"Key \"$key\" not found in work [$work_num]"
				);
			}
		}
	}

	public function testParseCarsWorksAsExpected()
	{
		$expected_output = $this->uploader::CAR_MODEL_VALUES;

		$parser = new WorksParser($this->uploader::TEST_INPUT);

		$value = $parser->parseCars()->first();
		$diff_with_expected = array_diff_assoc($value, $expected_output);
		$this->assertEmpty(
			$diff_with_expected,
			'Parsed car do not match expected output'
		);
	}

	public function testUploadCarsWorksAsExpected()
	{
		$this->uploader->uploadCars();

		$expected_output = $this->uploader::CAR_MODEL_VALUES;
		$this->assertDatabaseHas('car_models', $expected_output);
	}


	public function testParseWorksWorksAsExpected()
	{
		$this->uploader->uploadCars();
		$works = json_decode($this->uploader::TEST_INPUT, true)[0]['МассивРабот'];
		$works_json = json_encode($works, JSON_UNESCAPED_UNICODE);
		$expected_output = [
			'name' => 'ТО-1 не оригинал',
			'price' => 8400.0,
			'works_json' => $works_json,
			'car_model_id' => 1,
		];

		$parser = new WorksParser($this->uploader::TEST_INPUT);
		$value = $parser->parseWorks()->first();
		$value['works_json'] = json_encode($value['works_json'], JSON_UNESCAPED_UNICODE);

		$diff_with_expected = array_diff_assoc($value, $expected_output);
		$this->assertEmpty(
			$diff_with_expected,
			'Parsed work do not match expected output'
		);
	}

	public function testUploadWorksWorksAsExpected()
	{
		$this->uploader->uploadCars();
		$parser = new WorksParser($this->uploader::TEST_INPUT);
		$parser->uploadWorks();
		$expected_output = [
			'id' => 1,
			'car_model_id' => 1,
			...Arr::only($this->uploader::WORKS_MODEL_VALUES, ['name', 'price']),
		];
		$this->assertDatabaseHas('maintenance_models', $expected_output);

		$maintenance = MaintenanceModel::find(1)->toArray();
		$maintenance['works_json'] = json_encode($maintenance['works_json'], JSON_UNESCAPED_UNICODE);
		unset($maintenance['works_json_decoded']);

		$works_json_decoded_expected = $this->uploader::WORKS_MODEL_VALUES;
		$works_json_decoded_expected['works_json'] = json_encode($this->uploader::WORKS_MODEL_VALUES['works_json_decoded'], JSON_UNESCAPED_UNICODE);
		unset($works_json_decoded_expected['works_json_decoded']);

		$works_json_decoded_diff = array_diff($maintenance, $works_json_decoded_expected);
		$this->assertEmpty(
			$works_json_decoded_diff,
			'Parsed work do not match expected output'
		);
	}
}
