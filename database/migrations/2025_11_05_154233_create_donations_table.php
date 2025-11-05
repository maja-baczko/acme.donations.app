<?php

use App\Modules\Campaign\Models\Campaign;
use App\Modules\User\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void {
		Schema::create('donations', function (Blueprint $table) {
			$table->id();
			$table->foreignIdFor(Campaign::class)->constrained()->cascadeOnDelete();
			$table->foreignIdFor(User::class, 'donor_id')->constrained('users')->cascadeOnDelete();
			$table->decimal('amount', 10, 2);
			$table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
			$table->string('payment_method')->nullable();
			$table->text('comment')->nullable();
			$table->boolean('is_anonymous')->default(false);
			$table->timestamps();
		});
	}

	public function down(): void {
		Schema::dropIfExists('donations');
	}
};
