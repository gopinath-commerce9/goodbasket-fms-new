<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExtendUsersTableForAddresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable(false)->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('address_street_1', 512)->nullable()->default('');
            $table->string('address_street_2', 512)->nullable()->default('');
            $table->string('city', 512)->nullable()->default('');
            $table->string('region', 512)->nullable()->default('');
            $table->string('state', 512)->nullable()->default('');
            $table->string('zip_code', 12)->nullable()->default('');
            $table->string('country', 512)->nullable()->default('');
            $table->string('profile_picture', 512)->nullable()->default('');
            $table->string('phone_number', 20)->nullable()->default('');
            $table->string('mobile_number', 20)->nullable()->default('');
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        Schema::create('proof_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->nullable(false);
            $table->string('display_name', 100)->nullable(false);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        Schema::create('user_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable(false)->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('type_id')->nullable(false)->constrained('proof_types')->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('path')->nullable();
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

        Schema::dropIfExists('user_proofs');
        Schema::dropIfExists('proof_types');
        Schema::dropIfExists('user_addresses');

    }
}
