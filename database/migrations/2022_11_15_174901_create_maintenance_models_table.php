<?php

use App\Models\CarModel;
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
		Schema::create('maintenance_models', function (Blueprint $table) {
			$table->id();
			$table->timestamps();
			$table->string('name');
			$table->json('works_json');
			$table->decimal('price');
			$table->foreignIdFor(CarModel::class)
				->constrained()
				->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('maintenance_models');
	}
};
