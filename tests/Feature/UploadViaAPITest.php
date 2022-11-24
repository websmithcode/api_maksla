<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UploadViaAPITest extends TestCase
{
				public function testEndpointIsReacheble()
				{
								$response = $this->get('/api/upload');
								$response->assertStatus(200);
				}
}
