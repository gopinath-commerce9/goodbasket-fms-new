<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehicleRelatedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('type', 100)->nullable(false);
            $table->string('reg_number', 100)->nullable(false);
            $table->string('chassis_number', 200)->nullable(false);
            $table->string('maker', 100)->nullable(false);
            $table->string('model', 100)->nullable(false);
            $table->string('class', 100)->nullable();
            $table->string('fuel_type', 100)->nullable(false);
            $table->string('color', 100)->nullable(false);
            $table->dateTime('insurance_number')->nullable(false);
            $table->dateTime('insurance_due')->nullable(false);
            $table->dateTime('pollution_certificate')->nullable();
            $table->dateTime('pollution_due')->nullable();
            $table->dateTime('fitness_certificate')->nullable();
            $table->dateTime('fitness_due')->nullable();
            $table->text('vehicle_picture')->nullable();
            $table->string('owner_name', 512)->nullable();
            $table->foreignId('owner_id')->nullable()->default(null)->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        Schema::create('user_vehicle_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable(false)->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->nullable(false)->constrained('vehicles')->cascadeOnUpdate()->cascadeOnDelete();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        Schema::create('vehicle_zones', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->nullable(false);
            $table->string('display_name', 100)->nullable(false);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        Schema::create('vehicle_zone_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->nullable(false)->constrained('vehicles')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('zone_id')->nullable(false)->constrained('vehicle_zones')->cascadeOnUpdate()->cascadeOnDelete();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('vehicle_zone_maps');
        Schema::dropIfExists('vehicle_zones');
        Schema::dropIfExists('user_vehicle_maps');
        Schema::dropIfExists('vehicles');

    }
}
