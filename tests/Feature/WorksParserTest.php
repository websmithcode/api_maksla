<?php

namespace Tests\Feature;

use App\Models\CarModel;
use App\Models\MaintenanceModel;
use App\Services\WorksParser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Arr;
use Tests\TestCase;

class WorksParserTest extends TestCase
{
    use  DatabaseMigrations;

    const TEST_INPUT = <<<'JSON'
[
  {
		"Марка": "LAND ROVER",
		"Модель": "Range Rover",
		"Поколение": "IV 2017-2022",
		"ТипДВС": "Дизель",
		"МодельДВС": "3.0 249 л.с.",
		"ТО": "ТО-1 не оригинал",
		"МассивРабот": [
			"Фильтр салонный, с/у",
			"Фильтр топливный, с/у",
			"Масло моторное, замена"
		],
		"СуммаСвязанных": 8400.4123910
	}
]
JSON;

    const CAR_MODEL_VALUES = [
        'verbose' => 'LAND ROVER Range Rover IV 2017-2022 Дизель 3.0 249 л.с.',
        'brand' => 'LAND ROVER',
        'model' => 'Range Rover',
        'generation' => 'IV 2017-2022',
        'engine_type' => 'Дизель',
        'engine_model' => '3.0 249 л.с.',
    ];

    const WORKS_MODEL_VALUES = [
        'name' => 'ТО-1 не оригинал',
        'price' => 8400,
        'works_json_decoded' => [
            'Фильтр салонный, с/у',
            'Фильтр топливный, с/у',
            'Масло моторное, замена',
        ],
    ];

    public function getParser()
    {
        return new WorksParser(static::TEST_INPUT);
    }

    public function uploadCars()
    {
        $parser = $this->getParser();
        $parser->uploadCars();
    }

    public function setup(): void
    {
        parent::setup();
        $this->fixture_path = storage_path('docs/works.json');
        $this->works_json = file_get_contents($this->fixture_path);
        $this->parser = new WorksParser($this->works_json);
    }

    public function testFixtureFileExistsAndReadable()
    {
        $this->assertFileExists($this->fixture_path);
        $this->assertIsReadable($this->fixture_path);
    }

    public function testParserInitilizedAsExpected()
    {
        $this->assertEquals($this->works_json, $this->parser->json,
            'WorksParser json does not match');
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
                $this->assertArrayHasKey($key, $work,
                    "Key \"$key\" not found in work [$work_num]");
            }
        }
    }

    public function testParseCarsWorksAsExpected()
    {
        $expected_output = static::CAR_MODEL_VALUES;

        $parser = new WorksParser(static::TEST_INPUT);

        $value = $parser->parseCars()->first();
        $diff_with_expected = array_diff_assoc($value, $expected_output);
        $this->assertEmpty($diff_with_expected,
            'Parsed car do not match expected output');
    }

    public function testUploadCarsWorksAsExpected()
    {
        $this->uploadCars();

        $expected_output = static::CAR_MODEL_VALUES;
        $this->assertDatabaseHas('car_models', $expected_output);
        }


    public function testParseWorksWorksAsExpected()
    {
        $this->uploadCars();
        $works = json_decode(static::TEST_INPUT, true)[0]['МассивРабот'];
        $works_json = json_encode($works, JSON_UNESCAPED_UNICODE);
        $expected_output = [
            'name' => 'ТО-1 не оригинал',
            'price' => 8400,
            'works_json' => $works_json,
            'car_model_id' => 1,
        ];

        $parser = new WorksParser(static::TEST_INPUT);
        $value = $parser->parseWorks()->first();

        $diff_with_expected = array_diff_assoc($value, $expected_output);
        $this->assertEmpty($diff_with_expected,
            'Parsed work do not match expected output');
    }

    public function testUploadWorksWorksAsExpected()
    {
        $this->uploadCars();
        $parser = new WorksParser(static::TEST_INPUT);
        $parser->uploadWorks();
        $expected_output = [
            'id' => 1,
            'car_model_id' => 1,
            ...Arr::only(self::WORKS_MODEL_VALUES, ['name', 'price']),
        ];
        $this->assertDatabaseHas('maintenance_models', $expected_output);

        $work = MaintenanceModel::find(1);
        $works_json_decoded = json_decode($work->works_json, true);
        $works_json_decoded_expected = self::WORKS_MODEL_VALUES['works_json_decoded'];
        $works_json_decoded_diff = array_diff($works_json_decoded, $works_json_decoded_expected);
        $this->assertEmpty($works_json_decoded_diff,
            'Parsed work do not match expected output');
    }
}
