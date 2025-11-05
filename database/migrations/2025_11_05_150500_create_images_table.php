<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void {
		Schema::create('images', function (Blueprint $table) {
			$table->id();
			$table->string('type');
			$table->string('entity_type');
			$table->string('entity_id');
			$table->text('file_path');
			$table->string('file_name');
			$table->boolean('is_primary');
			$table->text('alt_text');
			$table->timestamps();
		});
	}

	public function down(): void {
		Schema::dropIfExists('images');
	}
};
