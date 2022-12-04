<?php

namespace App\Console\Commands;

use App\Services\WorksParser;
use Illuminate\Console\Command;

class UploadDemoData extends Command
{
	/** The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'upload:demo-data {data_name}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Uploads demo data to the database';

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle()
	{
		$data_name = $this->argument('data_name');
		$works_file =  static::getFixturePath($data_name);
		$works_json = file_get_contents($works_file);

		$parser = new WorksParser($works_json);

		$parser->upload();
		$this->info("Demo data \"$data_name\" uploaded");
		return Command::SUCCESS;
	}

	static function getFixturePath(string $name)
	{
    $path = storage_path("docs/demo/$name.json");
		if (!file_exists($path)) {
						throw new \Exception("Fixture \"$name\" not found");
		}
		return $path;
	}
}
