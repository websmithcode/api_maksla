<?php

namespace Tests\Services;

use App\Console\Commands\UploadDemoData;
use App\Services\WorksParser;

class WorksUploader
{
	const TEST_INPUT = <<<JSON
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

	public function __construct(string $data_name='works')
	{
		$this->fixture_path = UploadDemoData::getFixturePath($data_name);
		$this->works_json = file_get_contents($this->fixture_path);
		$this->parser = new WorksParser($this->works_json);
	}

	public function getParser()
	{
		return new WorksParser(static::TEST_INPUT);
	}

	public function uploadCars()
	{
		$parser = $this->getParser();
		$parser->uploadCars();
	}
	public function upload()
	{
		$parser = $this->getParser();
		$parser->upload();
	}
}
