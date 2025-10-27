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
        Schema::create('esim_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained('operators')->onDelete('cascade');
            $table->string('airalo_package_id')->unique();
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('amount',10,2);
            $table->bigInteger('day')->default(0);
            $table->boolean('is_unlimited')->nullable();
            $table->text('short_info')->nullable();
            $table->text('qr_installation')->nullable();
            $table->text('manual_installation')->nullable();
            $table->boolean('is_fair_usage_policy')->nullable();
            $table->text('fair_usage_policy')->nullable();
            $table->string('data', 50)->nullable();
            $table->decimal('net_price', 10, 2);
            $table->json('prices')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_recommend')->default(false);
            $table->boolean('is_best_value')->default(false);
             $table->tinyInteger('airalo_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('esim_packages');
    }
};
