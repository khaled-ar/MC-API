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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('fname');
            $table->string('lname');
            $table->string('phone_number');
            $table->string('content');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->default(now());
            $table->softDeletes();
            $table->unique(['fname', 'lname', 'phone_number', 'content']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
