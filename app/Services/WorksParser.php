<?php

namespace App\Services;

use App\Models\CarModel;
use App\Models\MaintenanceModel;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WorksParser
{
	public function __construct($works_json)
	{
		$this->json = $works_json;
		$this->data = null;
		$this->cars = null;
		$this->works = null;
	}

	public static function prepareArray(array $array)
	{
		return collect($array)->map(function ($item) {
			if (is_array($item)) {
				return self::prepareArray($item);
			} elseif (is_string($item)) {
				return Str::of($item)->trim()->__toString();
			} else {
				return $item;
			}
		})->toArray();
	}

	public function parse()
	{
		if (is_null($this->data)) {
			$this->data = collect(json_decode($this->json, true))
				->map(fn ($item) => self::prepareArray($item));
		}

		return $this->data;
	}

	/*
   * Returns an collection of unique cars
   * @return Illuminate\Support\Collection
   */
	public function parseCars()
	{
		if (is_null($this->cars)) {
			$cars = collect();
			foreach ($this->parse() as $work) {
				$cars->push([
					'verbose' => static::getCarVerboseFromWork($work),
					'brand' => $work['Марка'],
					'model' => $work['Модель'],
					'generation' => $work['Поколение'],
					'engine_type' => $work['ТипДВС'],
					'engine_model' => $work['МодельДВС'],
				]);
			}
			$this->cars = $cars
				->unique('verbose')
				->values();
		}

		return $this->cars;
	}

	/*
   * Returns an collection of unique cars
   * @return Illuminate\Support\Collection
   */
	public function parseWorks()
	{
		if (is_null($this->works)) {
			$works = collect($this->parse());
			$car_models = CarModel::all()->keyBy('verbose');
			if ($car_models->isEmpty()) {
				throw new \Exception('Car models are empty');
			}
			$this->works = $works->map(fn ($item) => [
				'name' => $item['ТО'],
				'works_json' => json_encode(
					$item['МассивРабот'],
					JSON_UNESCAPED_UNICODE
				),
				'price' => round($item['СуммаСвязанных']),
				'car_model_id' => $car_models[static::getCarVerboseFromWork($item)]->id,
			]);
		}

		return $this->works;
	}

	/*
   * Delete all records from table for the given model
   * @param string $model
   * @param bool $reset_autoincrement
   * @return void
   */
	public static function deleteAll(string $model, bool $reset_autoincrement = false): void
	{
		$deleted = $model::query()->delete();
		if ($reset_autoincrement && $deleted > 0) {
			$table = app($model)->getTable();
			DB::statement("ALTER TABLE $table AUTO_INCREMENT = 1");
		}
	}

	public function uploadCars($refresh = true)
	{
		if ($refresh) {
			self::deleteAll(CarModel::class, true);
		}
		$columns = ['brand', 'model', 'generation', 'engine_type', 'engine_model'];
		$cars = $this->parseCars()
			->map(fn ($car) => Arr::only($car, $columns));
		CarModel::factory()->createMany($cars->toArray());
	}

	public function uploadWorks()
	{
		$works = $this->parseWorks();
		MaintenanceModel::factory()->createMany($works);
	}
	public function upload()
	{
		$this->uploadCars();
		$this->uploadWorks();
	}

	/*
   * Returns a string with car verbose name
   * @param array $work
   * @param string $delimiter
   * @return string
   */
	public static function getCarVerboseFromWork($work, $delimiter = ' '): string
	{
		$data = [
			$work['Марка'],
			$work['Модель'],
			$work['Поколение'],
			$work['ТипДВС'],
			$work['МодельДВС'],
		];

		return implode($delimiter, $data);
	}
}
