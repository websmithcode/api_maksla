<?php

namespace Tests\Feature\Console\Commands;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Services\WorksUploader;
use Tests\TestCase;

class UploadDemoDataTest extends TestCase
{
	use  DatabaseMigrations;
	public function testUploadingWorks()
	{
		$this->artisan('upload:demo-data works')
			->expectsOutput('Demo data "works" uploaded')
			->assertExitCode(0);

		$uploader = new WorksUploader();
		$count_of_cars = $uploader->parser->parseCars()->count();
		$count_of_works = $uploader->parser->parse()->count();
				
		$this->assertDatabaseCount('car_models', $count_of_cars);
		$this->assertDatabaseCount('maintenance_models', $count_of_works);
	}
}
