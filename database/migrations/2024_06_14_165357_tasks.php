<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->char('title', length: 255);
            $table->text("description");
            $table->enum('priority', ['low', 'medium', 'high'])->nullable(false);
            $table->date('due_date');
            $table->boolean('completed')->default(false);
            $table->timestamps();
            $table->timestamp('completed_at')->nullable()->default(null);
            $table->foreignId('user_id')->constrained('users');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
