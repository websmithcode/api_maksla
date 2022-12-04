<?php

namespace Tests\Feature;

use App\Console\Commands\UploadDemoData;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UploadViaAPITest extends TestCase
{
	public function setup(): void
	{
		parent::setup();
		$this->fixture_path = UploadDemoData::getFixturePath('works');
	}

	public function testEndpointAccess()
	{
		$get_response = $this->get('/api/upload');
		$get_response->assertStatus(405);

		$post_response = $this->post('/api/upload');
		$post_response->assertStatus(403);

		// Send file
		$file = $this->getUploadableFile($this->fixture_path);
		$upload_post_response = $this->call('POST', '/api/upload', [], [], ['file' => $file]);
		$upload_post_response->assertStatus(403);
	}

	protected function getUploadableFile($file, $mime_type = 'text/json')
	{
		$path = $file;
		$original_name = basename($path);
		$error = null;
		$test = true;

		$file = new UploadedFile($path, $original_name, $mime_type, $error, $test);

		return $file;
	}
}
