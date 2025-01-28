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
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('admins', 'admin_id')->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('price');
            $table->string('location')->nullable();
            $table->enum('status', ['active', 'inactive', 'pending', 'unaccept'])->default('pending');
            $table->enum('type', ['sale', 'buy']);
            $table->boolean('updateable');
            $table->boolean('resultable');
            $table->boolean('pinable');
            $table->text('images')->nullable();
            $table->unique(['user_id', 'title']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
