<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAktivsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aktivs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sub_street_id')->nullable();
            $table->foreign('sub_street_id')->references('id')->on('sub_streets')->onDelete('cascade');

            $table->unsignedBigInteger('street_id')->nullable();
            $table->foreign('street_id')->references('id')->on('streets')->onDelete('cascade');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('action', ['created', 'updated', 'deleted'])->nullable();
            $table->timestamp('action_timestamp')->nullable();
            $table->softDeletes();

            $table->string('object_name');
            $table->string('balance_keeper');
            $table->string('location');
            $table->decimal('land_area', 10, 2);
            $table->decimal('building_area', 10, 2)->nullable();
            $table->string('gas');
            $table->string('water');
            $table->string('home_number')->nullable();
            $table->string('apartment_number')->nullable();
            $table->string('electricity');
            $table->text('additional_info')->nullable();
            $table->string('geolokatsiya')->nullable(); // Allow NULL values
            $table->decimal('latitude', 10, 7)->nullable(); // Allow NULL values
            $table->decimal('longitude', 10, 7)->nullable(); // Allow NULL values
            $table->string('kadastr_raqami')->nullable();

            $table->string('kadastr_pdf')->nullable();
            $table->string('hokim_qarori_pdf')->nullable();
            $table->string('transfer_basis_pdf')->nullable();

            $table->enum('building_type', ['yer', 'kopQavatliUy', 'AlohidaSavdoDokoni'])->nullable();

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
        Schema::dropIfExists('aktivs');
    }
}
