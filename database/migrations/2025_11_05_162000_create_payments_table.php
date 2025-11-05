<?php

use App\Modules\Donation\Models\Donation;
use App\Modules\User\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void {
		Schema::create('payments', function (Blueprint $table) {
			$table->id();
			$table->foreignIdFor(Donation::class)->constrained()->cascadeOnDelete();
			$table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
			$table->decimal('amount', 10, 2);
            $table->enum('status', ['processing', 'completed', 'failed'])->default('processing');
			$table->string('gateway');
			$table->string('transaction_reference')->unique();
			$table->text('metadata')->nullable();
			$table->timestamps();
		});
	}

	public function down(): void {
		Schema::dropIfExists('payments');
	}
};
