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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('admins', 'admin_id')->nullOnDelete();
            $table->string('name')->unique();
            $table->text('description');
            $table->float('cost');
            $table->smallInteger('validity');
            $table->enum('type', ['private', 'public']);
            $table->boolean('discount');
            $table->smallInteger('sale_ads_validity');
            $table->smallInteger('sale_ads_limit');
            $table->boolean('sale_ads_updateable');
            $table->boolean('sale_ads_resultable');
            $table->smallInteger('buy_ads_validity');
            $table->smallInteger('buy_ads_limit');
            $table->boolean('buy_ads_updateable');
            $table->boolean('buy_ads_resultable');
            $table->smallInteger('offers_limit');
            $table->smallInteger('service_discounts');
            $table->boolean('hide_offer');
            $table->boolean('offer_highlighting');
            $table->boolean('pinable');
            $table->smallInteger('pinable_validity');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
