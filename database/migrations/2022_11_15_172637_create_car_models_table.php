<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('car_models', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string('verbose', 1024)->storedAs('CONCAT(brand, " ", model, " ", generation, " ", engine_type, " ", engine_model)');
			$table->string('brand', 128);
			$table->string('model', 128);
			$table->string('generation', 128);
			$table->string('engine_type', 128);
			$table->string('engine_model', 128);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('car_models');
	}
};
