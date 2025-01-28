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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete()->cascadeOnUpdate();
            $table->smallInteger('sale_ads_validity');
            $table->smallInteger('sale_ads_limit');
            $table->smallInteger('buy_ads_validity');
            $table->smallInteger('buy_ads_limit');
            $table->smallInteger('offers_limit');
            $table->smallInteger('service_discounts');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
