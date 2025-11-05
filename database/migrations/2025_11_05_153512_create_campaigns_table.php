<?php

use App\Modules\Campaign\Models\Category;
use App\Modules\User\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up(): void {
		Schema::create('campaigns', function (Blueprint $table) {
			$table->id();
			$table->foreignIdFor(User::class, 'creator_id');
			$table->foreignIdFor(Category::class);
			$table->string('title');
			$table->string('slug');
			$table->text('description');
			$table->decimal('goal_amount')->default(0);
			$table->decimal('current_amount');
            $table->enum('status', ['draft', 'active', 'paused', 'completed'])->default('draft');
			$table->timestamp('start_date');
			$table->timestamp('end_date');
			$table->string('beneficiary_name');
			$table->string('beneficiary_website');
			$table->boolean('featured')->default(false);
			$table->timestamps();
		});
	}

	public function down(): void {
		Schema::dropIfExists('campaigns');
	}
};
